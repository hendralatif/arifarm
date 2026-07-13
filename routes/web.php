<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\GoatController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\AdminGoatController;
use App\Http\Controllers\Admin\AdminCategoryController;
use App\Http\Controllers\Admin\AdminOrderController;
use App\Http\Controllers\Admin\AdminUserController;
use App\Http\Controllers\Admin\AdminFeedingController;
use App\Http\Controllers\Admin\AdminHealthController;
use App\Http\Controllers\Admin\AdminBirthController;
use App\Http\Controllers\Admin\AdminExpenseController;
use App\Http\Controllers\Admin\AdminPaymentController;
use App\Http\Controllers\Admin\AdminReportController;
use App\Http\Controllers\Admin\AdminWeighingController;
use Illuminate\Support\Facades\Route;

// Public Customer Routes
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/catalog', [GoatController::class, 'index'])->name('catalog');
Route::get('/catalog/{slug}', [GoatController::class, 'show'])->name('catalog.show');

// Dynamic remote migration runner
Route::get('/run-migrations', function () {
    try {
        \Illuminate\Support\Facades\Artisan::call('migrate:fresh', [
            '--seed' => true,
            '--force' => true,
        ]);
        return '<pre style="background:#111;color:#5f5;padding:20px;">' 
            . "Migrations & Seeding successfully executed!\n\n" 
            . \Illuminate\Support\Facades\Artisan::output() 
            . '</pre>';
    } catch (\Throwable $e) {
        return '<pre style="background:#111;color:#f55;padding:20px;">' 
            . "Migration/Seeding failed: " . $e->getMessage() . "\n\n" 
            . $e->getTraceAsString() 
            . '</pre>';
    }
});

// Shopping Cart Routes (Session based, public)
Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
Route::post('/cart/add/{id}', [CartController::class, 'add'])->name('cart.add');
Route::post('/cart/remove/{id}', [CartController::class, 'remove'])->name('cart.remove');
Route::post('/cart/update', [CartController::class, 'update'])->name('cart.update');

// Midtrans Public Callback
Route::post('/payment/midtrans/notification', [PaymentController::class, 'handleNotification'])->name('payment.midtrans.notification');

// Customer Protected Routes (Requires Auth)
Route::middleware(['auth'])->group(function () {
    // Checkout
    Route::get('/checkout', [CartController::class, 'checkout'])->name('checkout');
    Route::post('/orders', [OrderController::class, 'store'])->name('orders.store');

    // Midtrans Payment Integration
    Route::get('/orders/{id}/snap-token', [PaymentController::class, 'getSnapToken'])->name('orders.snap-token');
    Route::get('/payment/midtrans/finish', [PaymentController::class, 'paymentFinish'])->name('payment.midtrans.finish');

    // Customer Dashboard & Orders
    Route::get('/dashboard', [OrderController::class, 'dashboard'])->name('dashboard');
    Route::get('/orders/{id}', [OrderController::class, 'show'])->name('orders.show');
    Route::post('/orders/{id}/upload-receipt', [OrderController::class, 'uploadReceipt'])->name('orders.upload-receipt');
    Route::post('/orders/{id}/cancel', [OrderController::class, 'cancel'])->name('orders.cancel');
    Route::post('/orders/{id}/confirm', [OrderController::class, 'confirmArrival'])->name('orders.confirm');
    Route::get('/orders/{id}/invoice', [OrderController::class, 'invoice'])->name('orders.invoice');
    Route::get('/pembayaran', [OrderController::class, 'payments'])->name('pembayaran');
    Route::get('/histori', [OrderController::class, 'history'])->name('histori');

    // Profile (Breeze default)
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Customer Live Chat
    Route::get('/chat/messages', [\App\Http\Controllers\ChatController::class, 'fetchMessages'])->name('chat.messages');
    Route::post('/chat/messages', [\App\Http\Controllers\ChatController::class, 'sendMessage'])->name('chat.messages.send');

    // Customer / General User Live Chat (WhatsApp Web Style)
    Route::get('/chats/unread-count', [\App\Http\Controllers\ConversationController::class, 'unreadCount'])->name('chats.unread-count');
    Route::get('/chats', [\App\Http\Controllers\ConversationController::class, 'index'])->name('chats.index');
    Route::get('/chats/conversations', [\App\Http\Controllers\ConversationController::class, 'fetchConversations'])->name('chats.conversations');
    Route::get('/chats/messages/{conversationId}', [\App\Http\Controllers\ConversationController::class, 'fetchMessages'])->name('chats.messages');
    Route::post('/chats/messages/{conversationId}', [\App\Http\Controllers\ConversationController::class, 'sendMessage'])->name('chats.messages.send');
    Route::post('/chats/start/{sellerId}', [\App\Http\Controllers\ConversationController::class, 'startConversation'])->name('chats.start');
    Route::get('/chats/search-users', [\App\Http\Controllers\ConversationController::class, 'searchUsers'])->name('chats.search-users');
    Route::post('/chats/create-by-user/{userId}', [\App\Http\Controllers\ConversationController::class, 'createConversationWithUser'])->name('chats.create-by-user');
});

// Admin Protected Routes (Requires Auth & Admin Role)
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    // Dashboard Summary
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');

    // Category CRUD
    Route::resource('categories', AdminCategoryController::class);

    // Goat (Product) CRUD
    Route::post('/goats/{id}/weighing', [AdminGoatController::class, 'addWeighing'])->name('goats.weighing');
    Route::resource('goats', AdminGoatController::class);
    Route::get('/weighings', [AdminWeighingController::class, 'index'])->name('weighings.index');
    Route::post('/weighings', [AdminWeighingController::class, 'store'])->name('weighings.store');
    Route::delete('/weighings/{id}', [AdminWeighingController::class, 'destroy'])->name('weighings.destroy');

    // Orders Management
    Route::get('/orders', [AdminOrderController::class, 'index'])->name('orders.index');
    Route::get('/orders/{id}', [AdminOrderController::class, 'show'])->name('orders.show');
    Route::post('/orders/{id}/approve', [AdminOrderController::class, 'approveOrder'])->name('orders.approve');
    Route::post('/orders/{id}/reject', [AdminOrderController::class, 'rejectOrder'])->name('orders.reject');
    Route::post('/orders/{id}/verify', [AdminOrderController::class, 'verifyPayment'])->name('orders.verify');
    Route::post('/orders/{id}/status', [AdminOrderController::class, 'updateStatus'])->name('orders.status');

    // Expenses (Pengeluaran) Management
    Route::get('/expenses', [AdminExpenseController::class, 'index'])->name('expenses.index');
    Route::post('/expenses', [AdminExpenseController::class, 'store'])->name('expenses.store');
    Route::delete('/expenses/{id}', [AdminExpenseController::class, 'destroy'])->name('expenses.destroy');

    // Customer (User) Management
    Route::get('/users', [AdminUserController::class, 'index'])->name('users.index');
    Route::get('/users/create', [AdminUserController::class, 'create'])->name('users.create');
    Route::post('/users', [AdminUserController::class, 'store'])->name('users.store');
    Route::get('/users/{id}', [AdminUserController::class, 'show'])->name('users.show');
    Route::delete('/users/{id}', [AdminUserController::class, 'destroy'])->name('users.destroy');

    // Pakan (Feeding Records)
    Route::get('/feedings', [AdminFeedingController::class, 'index'])->name('feedings.index');
    Route::get('/feedings/create', [AdminFeedingController::class, 'create'])->name('feedings.create');
    Route::post('/feedings', [AdminFeedingController::class, 'store'])->name('feedings.store');
    Route::post('/feedings/add-stock', [AdminFeedingController::class, 'addStock'])->name('feedings.add-stock');
    Route::delete('/feedings/{id}', [AdminFeedingController::class, 'destroy'])->name('feedings.destroy');
    Route::post('/feedings/schedules', [AdminFeedingController::class, 'schedulesStore'])->name('feedings.schedules.store');
    Route::delete('/feedings/schedules/{id}', [AdminFeedingController::class, 'schedulesDestroy'])->name('feedings.schedules.destroy');
    Route::post('/feedings/schedules/copy-all', [AdminFeedingController::class, 'schedulesCopyToAllDays'])->name('feedings.schedules.copy-all');

    // Kesehatan (Health Records)
    Route::get('/health', [AdminHealthController::class, 'index'])->name('health.index');
    Route::get('/health/create', [AdminHealthController::class, 'create'])->name('health.create');
    Route::post('/health', [AdminHealthController::class, 'store'])->name('health.store');
    Route::get('/health/{id}', [AdminHealthController::class, 'show'])->name('health.show');
    Route::delete('/health/{id}', [AdminHealthController::class, 'destroy'])->name('health.destroy');

    // Kelahiran (Birth Records)
    Route::get('/births', [AdminBirthController::class, 'index'])->name('births.index');
    Route::get('/births/create', [AdminBirthController::class, 'create'])->name('births.create');
    Route::post('/births', [AdminBirthController::class, 'store'])->name('births.store');
    Route::delete('/births/{id}', [AdminBirthController::class, 'destroy'])->name('births.destroy');

    // Pembayaran (Payment Verification)
    Route::get('/payments', [AdminPaymentController::class, 'index'])->name('payments.index');
    Route::post('/payments/{id}/verify', [AdminPaymentController::class, 'verify'])->name('payments.verify');

    // Laporan (Reports Dashboard)
    Route::get('/reports', [AdminReportController::class, 'index'])->name('reports.index');
    Route::get('/reports/download', [AdminReportController::class, 'downloadPdf'])->name('reports.download');

    // Live Chat Room
    Route::get('/chats', [\App\Http\Controllers\Admin\AdminChatController::class, 'index'])->name('chats.index');
    Route::get('/chats/conversations', [\App\Http\Controllers\Admin\AdminChatController::class, 'fetchConversations'])->name('chats.conversations');
    Route::get('/chats/messages/{customerId}', [\App\Http\Controllers\Admin\AdminChatController::class, 'fetchMessages'])->name('chats.messages');
    Route::post('/chats/messages/{customerId}', [\App\Http\Controllers\Admin\AdminChatController::class, 'sendMessage'])->name('chats.messages.send');
});

require __DIR__.'/auth.php';
