<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Expense;
use App\Models\Goat;
use App\Models\GoatFeeding;
use App\Models\GoatBirth;
use App\Models\GoatHealthRecord;
use App\Models\FeedStock;
use App\Models\FeedingSchedule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;

class AdminReportController extends Controller
{
    public function index(Request $request)
    {
        $data = $this->getReportData($request);
        return view('admin.reports.index', $data);
    }

    public function downloadPdf(Request $request)
    {
        $data = $this->getReportData($request);
        
        // Render custom styling optimized for PDF generation using DomPDF
        $pdf = Pdf::loadView('admin.reports.pdf', $data);
        
        return $pdf->download('laporan-peternakan-' . $data['periodType'] . '-' . now()->format('Y-m-d') . '.pdf');
    }

    private function getReportData(Request $request)
    {
        $periodType = $request->input('period', 'this_month');
        $dateFrom = null;
        $dateTo = null;

        switch ($periodType) {
            case 'daily':
                $selectedDate = $request->input('date', today()->toDateString());
                $dateFrom = Carbon::parse($selectedDate)->startOfDay();
                $dateTo = Carbon::parse($selectedDate)->endOfDay();
                break;
            case 'weekly':
                $selectedWeek = $request->input('week');
                if ($selectedWeek) {
                    $year = substr($selectedWeek, 0, 4);
                    $week = substr($selectedWeek, 6);
                    $dateFrom = Carbon::now()->setISODate($year, $week)->startOfWeek()->startOfDay();
                    $dateTo = Carbon::now()->setISODate($year, $week)->endOfWeek()->endOfDay();
                } else {
                    $dateFrom = now()->startOfWeek()->startOfDay();
                    $dateTo = now()->endOfWeek()->endOfDay();
                }
                break;
            case 'monthly':
                $selectedMonth = $request->input('month');
                if ($selectedMonth) {
                    $dateFrom = Carbon::parse($selectedMonth . '-01')->startOfMonth()->startOfDay();
                    $dateTo = Carbon::parse($selectedMonth . '-01')->endOfMonth()->endOfDay();
                } else {
                    $dateFrom = now()->startOfMonth()->startOfDay();
                    $dateTo = now()->endOfMonth()->endOfDay();
                }
                break;
            case 'yearly':
                $selectedYear = $request->input('year', now()->year);
                $dateFrom = Carbon::create($selectedYear, 1, 1)->startOfYear()->startOfDay();
                $dateTo = Carbon::create($selectedYear, 12, 31)->endOfYear()->endOfDay();
                break;
            case 'transaction':
                $dateFrom = $request->filled('date_from') ? Carbon::parse($request->date_from)->startOfDay() : now()->startOfMonth()->startOfDay();
                $dateTo = $request->filled('date_to') ? Carbon::parse($request->date_to)->endOfDay() : now()->endOfMonth()->endOfDay();
                break;
            case 'this_month':
                $dateFrom = now()->startOfMonth()->startOfDay();
                $dateTo = now()->endOfMonth()->endOfDay();
                break;
            case 'last_month':
                $dateFrom = now()->subMonth()->startOfMonth()->startOfDay();
                $dateTo = now()->subMonth()->endOfMonth()->endOfDay();
                break;
            case '3_months':
                $dateFrom = now()->subMonths(2)->startOfMonth()->startOfDay();
                $dateTo = now()->endOfMonth()->endOfDay();
                break;
            case '6_months':
                $dateFrom = now()->subMonths(5)->startOfMonth()->startOfDay();
                $dateTo = now()->endOfMonth()->endOfDay();
                break;
            case 'this_year':
                $dateFrom = now()->startOfYear()->startOfDay();
                $dateTo = now()->endOfYear()->endOfDay();
                break;
            case 'custom':
                $dateFrom = $request->filled('date_from') ? Carbon::parse($request->date_from)->startOfDay() : now()->startOfMonth()->startOfDay();
                $dateTo = $request->filled('date_to') ? Carbon::parse($request->date_to)->endOfDay() : now()->endOfMonth()->endOfDay();
                break;
        }

        // ========== FINANCIAL SUMMARY ==========
        $totalIncome = Order::where('status', 'completed')
            ->whereBetween('updated_at', [$dateFrom, $dateTo])
            ->sum('total_amount');

        $totalExpense = Expense::whereBetween('expense_date', [$dateFrom, $dateTo])
            ->sum('amount');

        $netProfit = $totalIncome - $totalExpense;

        // Expense breakdown by category
        $expenseByCategory = Expense::select('category', DB::raw('SUM(amount) as total'))
            ->whereBetween('expense_date', [$dateFrom, $dateTo])
            ->groupBy('category')
            ->orderByDesc('total')
            ->get();

        // Income by month (for chart — last 6 months always)
        $incomeByMonth = [];
        $expenseByMonth = [];
        for ($i = 5; $i >= 0; $i--) {
            $monthStart = now()->subMonths($i)->startOfMonth();
            $monthEnd = now()->subMonths($i)->endOfMonth();
            $monthLabel = $monthStart->locale('id')->translatedFormat('M Y');

            $incomeByMonth[] = [
                'label' => $monthLabel,
                'total' => Order::where('status', 'completed')
                    ->whereBetween('updated_at', [$monthStart, $monthEnd])
                    ->sum('total_amount'),
            ];
            $expenseByMonth[] = [
                'label' => $monthLabel,
                'total' => Expense::whereBetween('expense_date', [$monthStart, $monthEnd])
                    ->sum('amount'),
            ];
        }

        // ========== GOAT STOCK SUMMARY ==========
        $goatStats = [
            'total'      => Goat::count(),
            'available'  => Goat::where('status', 'available')->count(),
            'sold'       => Goat::where('status', 'sold')->count(),
            'by_origin'  => Goat::select('acquisition_type', DB::raw('COUNT(*) as count'))
                ->groupBy('acquisition_type')
                ->pluck('count', 'acquisition_type')
                ->toArray(),
        ];

        // ========== FEEDING SUMMARY ==========
        $feedingStats = [
            'total_kg_period'  => GoatFeeding::whereBetween('feeding_date', [$dateFrom, $dateTo])
                ->selectRaw('COALESCE(SUM(quantity_1_kg),0) + COALESCE(SUM(quantity_2_kg),0) as total')
                ->value('total') ?? 0,
            'feeding_count'    => GoatFeeding::whereBetween('feeding_date', [$dateFrom, $dateTo])->count(),
            'feed_stocks'      => FeedStock::all(),
        ];

        // ========== BIRTH SUMMARY ==========
        $birthStats = [
            'total_births'  => GoatBirth::whereBetween('birth_date', [$dateFrom, $dateTo])->count(),
            'total_male'    => GoatBirth::whereBetween('birth_date', [$dateFrom, $dateTo])->sum('male_count'),
            'total_female'  => GoatBirth::whereBetween('birth_date', [$dateFrom, $dateTo])->sum('female_count'),
            'total_dead'    => GoatBirth::whereBetween('birth_date', [$dateFrom, $dateTo])->sum('stillborn_count'),
        ];

        // ========== HEALTH SUMMARY ==========
        $healthStats = [
            'total_records' => GoatHealthRecord::whereBetween('check_date', [$dateFrom, $dateTo])->count(),
            'by_type'       => GoatHealthRecord::select('record_type', DB::raw('COUNT(*) as count'))
                ->whereBetween('check_date', [$dateFrom, $dateTo])
                ->groupBy('record_type')
                ->pluck('count', 'record_type')
                ->toArray(),
        ];

        // ========== ORDER SUMMARY ==========
        $orderStats = [
            'total_orders'  => Order::whereBetween('created_at', [$dateFrom, $dateTo])->count(),
            'completed'     => Order::where('status', 'completed')->whereBetween('created_at', [$dateFrom, $dateTo])->count(),
            'cancelled'     => Order::where('status', 'cancelled')->whereBetween('created_at', [$dateFrom, $dateTo])->count(),
            'processing'    => Order::whereIn('status', ['processing', 'shipped'])->whereBetween('created_at', [$dateFrom, $dateTo])->count(),
        ];

        $ordersList = Order::with('user')
            ->whereBetween('created_at', [$dateFrom, $dateTo])
            ->latest()
            ->get();
        $expensesList = Expense::with('recorder')
            ->whereBetween('expense_date', [$dateFrom, $dateTo])
            ->latest()
            ->get();

        return compact(
            'periodType', 'dateFrom', 'dateTo',
            'totalIncome', 'totalExpense', 'netProfit',
            'expenseByCategory', 'incomeByMonth', 'expenseByMonth',
            'goatStats', 'feedingStats', 'birthStats', 'healthStats', 'orderStats',
            'ordersList', 'expensesList'
        );
    }
}
