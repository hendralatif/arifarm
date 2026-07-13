<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Expense;
use App\Models\Goat;
use App\Models\GoatFeeding;
use App\Models\FeedStock;
use App\Models\FeedingSchedule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class AdminFeedingController extends Controller
{
    public function index(Request $request)
    {
        $query = GoatFeeding::with(['recorder', 'feedStock1', 'feedStock2'])->latest('feeding_date');

        if ($request->filled('session')) {
            $query->where('session', $request->session);
        }
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('feed_type_1', 'like', '%' . $search . '%')
                  ->orWhere('feed_type_2', 'like', '%' . $search . '%');
            });
        }

        $feedings = $query->paginate(15)->withQueryString();

        $totalKg   = GoatFeeding::sum('quantity_1_kg') + GoatFeeding::sum('quantity_2_kg');
        $todayKg   = GoatFeeding::whereDate('feeding_date', today())->sum('quantity_1_kg') + GoatFeeding::whereDate('feeding_date', today())->sum('quantity_2_kg');
        $monthCount = GoatFeeding::whereMonth('feeding_date', now()->month)->count();

        // Load feed stocks
        $feedStocks = FeedStock::all();

        // Weekly schedule list
        $schedules = FeedingSchedule::with(['feedStock1', 'feedStock2'])->get();

        // Mineral Blok bi-weekly reminder calculations
        $lastMineralBlok = GoatFeeding::where(function($q) {
            $q->where('feed_type_1', 'Mineral Blok')
              ->orWhere('feed_type_2', 'Mineral Blok');
        })->latest('feeding_date')->first();

        $mineralBlokDaysAgo = null;
        $isMineralBlokDue = false;
        $nextMineralBlokDate = null;

        if ($lastMineralBlok) {
            $lastDate = Carbon::parse($lastMineralBlok->feeding_date);
            $mineralBlokDaysAgo = $lastDate->diffInDays(now());
            $isMineralBlokDue = $mineralBlokDaysAgo >= 14;
            $nextMineralBlokDate = $lastDate->copy()->addDays(14);
        } else {
            $isMineralBlokDue = true;
        }

        // Total goats for per-goat schedule estimation
        $totalGoats = Goat::where('status', 'available')->count();

        return view('admin.feedings.index', compact(
            'feedings', 'totalKg', 'todayKg', 'monthCount', 'feedStocks', 'schedules',
            'lastMineralBlok', 'mineralBlokDaysAgo', 'isMineralBlokDue', 'nextMineralBlokDate',
            'totalGoats'
        ));
    }

    public function create()
    {
        $feedStocks = FeedStock::all();

        // Get today's day name in Indonesian for schedule matching
        $dayMap = [
            'Monday' => 'senin', 'Tuesday' => 'selasa', 'Wednesday' => 'rabu',
            'Thursday' => 'kamis', 'Friday' => 'jumat', 'Saturday' => 'sabtu', 'Sunday' => 'minggu',
        ];
        $todayKey = $dayMap[now()->format('l')] ?? 'senin';

        $todaySchedules = FeedingSchedule::with(['feedStock1', 'feedStock2'])
            ->where('day_of_week', $todayKey)
            ->get()
            ->keyBy('session');

        $totalGoats = Goat::where('status', 'available')->count();

        return view('admin.feedings.create', compact('feedStocks', 'todaySchedules', 'totalGoats'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'feeding_date'  => 'required|date',
            'feeding_time'  => 'nullable|date_format:H:i',
            'session'       => 'required|in:pagi,sore',
            'goat_count'    => 'required|integer|min:1',
            'feed_stock_1_id' => 'required_without:feed_stock_2_id|nullable|exists:feed_stocks,id',
            'quantity_1_kg'   => 'required_with:feed_stock_1_id|nullable|numeric|min:0.1|max:9999',
            'feed_stock_2_id' => 'nullable|exists:feed_stocks,id|different:feed_stock_1_id',
            'quantity_2_kg'   => 'required_with:feed_stock_2_id|nullable|numeric|min:0.1|max:9999',
            'notes'         => 'nullable|string|max:500',
        ], [
            'feeding_date.required'  => 'Tanggal pemberian pakan wajib diisi.',
            'session.in'             => 'Sesi pemberian pakan hanya boleh pagi dan sore.',
            'goat_count.required'    => 'Jumlah kambing wajib diisi.',
            'feed_stock_1_id.required_without' => 'Paling tidak harus memilih satu jenis pakan.',
            'quantity_1_kg.required_with'      => 'Jumlah pakan 1 wajib diisi.',
            'quantity_2_kg.required_with'      => 'Jumlah pakan 2 wajib diisi.',
            'feed_stock_2_id.different'        => 'Jenis pakan 2 tidak boleh sama dengan jenis pakan 1.',
        ]);

        $feedStock1 = $request->feed_stock_1_id ? FeedStock::findOrFail($request->feed_stock_1_id) : null;
        $feedStock2 = $request->feed_stock_2_id ? FeedStock::findOrFail($request->feed_stock_2_id) : null;

        // Check stock availability
        if ($feedStock1 && $feedStock1->stock_kg < $request->quantity_1_kg) {
            return redirect()->back()
                ->withInput()
                ->with('error', "Stok pakan {$feedStock1->name} tidak mencukupi. Stok saat ini: {$feedStock1->stock_kg} kg.");
        }

        if ($feedStock2 && $feedStock2->stock_kg < $request->quantity_2_kg) {
            return redirect()->back()
                ->withInput()
                ->with('error', "Stok pakan {$feedStock2->name} tidak mencukupi. Stok saat ini: {$feedStock2->stock_kg} kg.");
        }

        // Deduct stocks
        if ($feedStock1) {
            $feedStock1->stock_kg -= $request->quantity_1_kg;
            $feedStock1->save();
        }

        if ($feedStock2) {
            $feedStock2->stock_kg -= $request->quantity_2_kg;
            $feedStock2->save();
        }

        GoatFeeding::create([
            'feeding_date'    => $request->feeding_date,
            'feeding_time'    => $request->feeding_time,
            'feed_stock_1_id' => $feedStock1?->id,
            'feed_stock_2_id' => $feedStock2?->id,
            'feed_type_1'     => $feedStock1?->name,
            'feed_type_2'     => $feedStock2?->name,
            'quantity_1_kg'   => $request->quantity_1_kg ?? 0,
            'quantity_2_kg'   => $request->quantity_2_kg ?? 0,
            'goat_count'      => $request->goat_count,
            'session'         => $request->session,
            'notes'           => $request->notes,
            'recorded_by'     => Auth::id(),
        ]);

        return redirect()->route('admin.feedings.index')
            ->with('success', 'Catatan pakan berhasil ditambahkan dan stok pakan telah diperbarui.');
    }

    public function addStock(Request $request)
    {
        $request->validate([
            'feed_stock_id' => 'required|exists:feed_stocks,id',
            'added_kg'      => 'required|numeric|min:0.1|max:99999',
            'cost'          => 'required|numeric|min:0',
            'supplier'      => 'nullable|string|max:255',
        ], [
            'added_kg.required' => 'Jumlah tambahan stok wajib diisi.',
            'cost.required'     => 'Biaya pembelian pakan wajib diisi (isi 0 jika gratis/hibah).',
        ]);

        $feedStock = FeedStock::findOrFail($request->feed_stock_id);
        $feedStock->stock_kg += $request->added_kg;
        $feedStock->save();

        // Automatically record the cost as a Pakan expense
        if ($request->cost > 0) {
            $supplierNote = $request->supplier ? " (Supplier: {$request->supplier})" : '';
            Expense::create([
                'expense_date' => today()->toDateString(),
                'category'     => 'pakan',
                'title'        => "Pembelian Stok Pakan: {$feedStock->name}",
                'amount'       => $request->cost,
                'description'  => "Penambahan stok {$feedStock->name} sebanyak {$request->added_kg} kg{$supplierNote}. (Dicatat otomatis dari refill stok pakan)",
                'recorded_by'  => Auth::id(),
            ]);
        }

        return redirect()->route('admin.feedings.index')
            ->with('success', "Stok pakan {$feedStock->name} berhasil ditambahkan sebanyak {$request->added_kg} kg" . ($request->cost > 0 ? ' dan biaya dicatat sebagai pengeluaran.' : '.'));
    }

    public function destroy($id)
    {
        $feeding = GoatFeeding::findOrFail($id);

        // Restore stock 1
        if ($feeding->feed_stock_1_id && $feeding->quantity_1_kg > 0) {
            $fs1 = FeedStock::find($feeding->feed_stock_1_id);
            if ($fs1) {
                $fs1->stock_kg += $feeding->quantity_1_kg;
                $fs1->save();
            }
        }

        // Restore stock 2
        if ($feeding->feed_stock_2_id && $feeding->quantity_2_kg > 0) {
            $fs2 = FeedStock::find($feeding->feed_stock_2_id);
            if ($fs2) {
                $fs2->stock_kg += $feeding->quantity_2_kg;
                $fs2->save();
            }
        }

        $feeding->delete();

        return redirect()->route('admin.feedings.index')
            ->with('success', 'Catatan pakan berhasil dihapus dan stok pakan telah dikembalikan.');
    }

    // Weekly Feeding Schedules logic
    public function schedulesStore(Request $request)
    {
        $request->validate([
            'day_of_week'     => 'required|in:senin,selasa,rabu,kamis,jumat,sabtu,minggu',
            'session'         => 'required|in:pagi,sore',
            'feed_stock_1_id' => 'required_without:feed_stock_2_id|nullable|exists:feed_stocks,id',
            'quantity_1_kg'   => 'required_with:feed_stock_1_id|nullable|numeric|min:0',
            'qty_type_1'      => 'required_with:feed_stock_1_id|in:fixed,per_goat',
            'feed_stock_2_id' => 'nullable|exists:feed_stocks,id|different:feed_stock_1_id',
            'quantity_2_kg'   => 'required_with:feed_stock_2_id|nullable|numeric|min:0',
            'qty_type_2'      => 'nullable|in:fixed,per_goat',
            'notes'           => 'nullable|string|max:500',
        ], [
            'day_of_week.required'             => 'Hari wajib dipilih.',
            'session.required'                 => 'Sesi wajib dipilih.',
            'feed_stock_1_id.required_without' => 'Paling tidak harus memilih satu jenis pakan untuk jadwal ini.',
            'feed_stock_2_id.different'        => 'Jenis pakan 2 tidak boleh sama dengan jenis pakan 1.',
        ]);

        FeedingSchedule::updateOrCreate(
            [
                'day_of_week' => $request->day_of_week,
                'session'     => $request->session,
            ],
            [
                'feed_stock_1_id' => $request->feed_stock_1_id,
                'feed_stock_2_id' => $request->feed_stock_2_id,
                'quantity_1_kg'   => $request->quantity_1_kg ?? 0,
                'qty_type_1'      => $request->qty_type_1 ?? 'fixed',
                'quantity_2_kg'   => $request->quantity_2_kg ?? 0,
                'qty_type_2'      => $request->qty_type_2 ?? 'fixed',
                'notes'           => $request->notes,
                'created_by'      => Auth::id(),
            ]
        );

        return redirect()->route('admin.feedings.index', ['tab' => 'schedule'])
            ->with('success', 'Jadwal mingguan pakan berhasil disimpan.');
    }

    public function schedulesDestroy($id)
    {
        $schedule = FeedingSchedule::findOrFail($id);
        $schedule->delete();

        return redirect()->route('admin.feedings.index', ['tab' => 'schedule'])
            ->with('success', 'Jadwal mingguan pakan berhasil dihapus.');
    }

    /**
     * Salin semua sesi (pagi & sore) dari hari sumber ke semua hari lainnya dalam seminggu.
     */
    public function schedulesCopyToAllDays(Request $request)
    {
        $request->validate([
            'source_day' => 'required|in:senin,selasa,rabu,kamis,jumat,sabtu,minggu',
        ]);

        $sourceDay = $request->source_day;
        $allDays   = ['senin', 'selasa', 'rabu', 'kamis', 'jumat', 'sabtu', 'minggu'];

        // Ambil semua jadwal dari hari sumber
        $sourceSchedules = FeedingSchedule::where('day_of_week', $sourceDay)->get();

        if ($sourceSchedules->isEmpty()) {
            return redirect()->route('admin.feedings.index', ['tab' => 'schedule'])
                ->with('error', "Tidak ada jadwal yang ditemukan untuk hari " . ucfirst($sourceDay) . ". Tidak ada jadwal yang disalin.");
        }

        $targetDays = array_filter($allDays, fn($d) => $d !== $sourceDay);
        $copiedCount = 0;

        foreach ($targetDays as $targetDay) {
            foreach ($sourceSchedules as $src) {
                FeedingSchedule::updateOrCreate(
                    [
                        'day_of_week' => $targetDay,
                        'session'     => $src->session,
                    ],
                    [
                        'feed_stock_1_id' => $src->feed_stock_1_id,
                        'feed_stock_2_id' => $src->feed_stock_2_id,
                        'quantity_1_kg'   => $src->quantity_1_kg,
                        'qty_type_1'      => $src->qty_type_1,
                        'quantity_2_kg'   => $src->quantity_2_kg,
                        'qty_type_2'      => $src->qty_type_2,
                        'notes'           => $src->notes,
                        'created_by'      => Auth::id(),
                    ]
                );
                $copiedCount++;
            }
        }

        $dayLabels = [
            'senin' => 'Senin', 'selasa' => 'Selasa', 'rabu' => 'Rabu',
            'kamis' => 'Kamis', 'jumat' => 'Jumat', 'sabtu' => 'Sabtu', 'minggu' => 'Minggu',
        ];

        return redirect()->route('admin.feedings.index', ['tab' => 'schedule'])
            ->with('success', "✅ Jadwal hari {$dayLabels[$sourceDay]} berhasil disalin ke semua hari ({$copiedCount} slot jadwal diperbarui).");
    }
}
