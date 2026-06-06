<?php

use App\Http\Controllers\ProductController;
use App\Http\Controllers\WorkshopController;
use App\Http\Controllers\WorkshopBookingController;
use App\Http\Controllers\LandingController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\Admin\WorkshopController as AdminWorkshopController;
use App\Http\Middleware\AdminMiddleware;
use App\Http\Middleware\CustomerMiddleware;
use App\Livewire\Settings\Password;
use App\Livewire\Settings\Profile;
use App\Livewire\Settings\Address as AddressSettings;
use App\Livewire\Dashboard\MyAddresses;
use App\Livewire\Dashboard\FormAlamat;
use App\Livewire\Auth\ForgotPassword as ForgotPasswordComponent;
use App\Livewire\Auth\ResetPassword as ResetPasswordComponent;
use App\Models\Workshop;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Password as PasswordFacade;

// Landing Pages (Public)
Route::get('/', [LandingController::class, 'home'])->name('landing.home');
Route::get('/about', [LandingController::class, 'about'])->name('landing.about');
Route::get('/shop', [LandingController::class, 'shop'])->name('landing.shop');
Route::get('/product/{id}', [LandingController::class, 'productDetail'])->where('id', '[0-9]+')->name('landing.product.detail');
Route::get('/workshop-public', [LandingController::class, 'workshop'])->name('landing.workshop');
Route::get('/contact', [LandingController::class, 'contact'])->name('landing.contact');
Route::post('/contact', [LandingController::class, 'contactSubmit'])->name('landing.contact.submit');
Route::view('/diagnostics', 'diagnostics')->name('diagnostics');
Route::view('/livewire-test', 'livewire-test')->name('livewire-test');
Route::view('/livewire-debug', 'livewire-debug')->name('livewire-debug');

// Test WorkshopBookingForm Component
Route::get('/test-booking-form/{workshopId}', function($workshopId) {
    $workshop = Workshop::findOrFail($workshopId);
    return view('workshop.test-booking-form', compact('workshop'));
})->name('test-booking-form');

// Email Testing Routes (Development Only)
Route::get('/test-email', function() {
    return view('email-test');
})->name('email.test');

Route::post('/test-email-send', function() {
    try {
        $email = request('email');
        Mail::raw('Test email from Batik Giri Alam', function ($message) use ($email) {
            $message->to($email)->subject('Test Email');
        });
        return back()->with('success', '✅ Email sent to ' . $email);
    } catch (\Exception $e) {
        return back()->with('error', '❌ Error: ' . $e->getMessage());
    }
})->name('email.send');

Route::post('/test-forgot-password', function() {
    try {
        $email = request('email');
        $response = PasswordFacade::sendResetLink(['email' => $email]);
        
        if ($response == PasswordFacade::RESET_LINK_SENT) {
            return back()->with('success', '✅ Password reset email sent to ' . $email);
        } else {
            return back()->with('error', '❌ Error sending reset email: ' . $response);
        }
    } catch (\Exception $e) {
        return back()->with('error', '❌ Error: ' . $e->getMessage());
    }
})->name('forgot-password.send');

// Auth Routes (Guest-only)
Route::middleware('guest')->group(function () {
    Route::get('/forgot-password', function () {
        return view('auth.forgot-password-page');
    })->name('forgot-password');

    Route::get('/reset-password/{token}', function ($token) {
        return view('auth.reset-password-page', ['token' => $token]);
    })->name('password.reset');
});

// Workshop Public Routes
Route::get('/workshop', [WorkshopBookingController::class, 'index'])->name('workshop.index');
Route::get('/workshop-list', function() { 
    $workshops = Workshop::where('is_active', true)->get();
    return view('workshop.workshop-list', ['workshops' => $workshops]); 
})->name('workshop.list');
Route::get('/workshop/{workshop}/book', function(Workshop $workshop) { 
    return view('workshop.booking-landing', ['workshop' => $workshop]); 
})->name('workshop.book');
Route::get('/workshop/{workshop}/available-schedules', [WorkshopBookingController::class, 'getAvailableSchedules'])->name('workshop.available-schedules');

// Workshop Booking Routes (Protected - must be authenticated)
Route::middleware('auth')->group(function () {
    Route::get('/workshop/{workshop}/booking', function(Workshop $workshop) { 
        return view('workshop.booking-simple', ['workshop' => $workshop]); 
    })->name('workshop.booking.page');
    Route::post('/workshop/booking', [WorkshopBookingController::class, 'store'])->name('workshop.booking.store');
});

// TEST: Simple POST endpoint
Route::post('/test-json', function() {
    return response()->json(['success' => true, 'message' => 'Test POST works!']);
})->name('test.json');

// Workshop Booking Routes moved to protected group above

// Route::view('dashboard', 'dashboard')
//     ->middleware(['auth'])
//     ->name('dashboard');

Route::middleware(['auth'])->group(function () {
    // Admin Dashboard & Management Routes
    Route::middleware([AdminMiddleware::class])->prefix('admin')->name('admin.')->group(function () {
        Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');
        


        // Workshop Routes
        Route::resource('workshop', AdminWorkshopController::class);
        
        // OLD SYSTEM (Workshop Dates) - Keep for backward compatibility
        Route::post('workshop/{workshop}/dates', [AdminWorkshopController::class, 'addDate'])->name('workshop.oldAddDate');
        Route::put('workshop-dates/{workshopDate}', [AdminWorkshopController::class, 'updateDate'])->name('workshop.updateDate');
        Route::delete('workshop-dates/{workshopDate}', [AdminWorkshopController::class, 'deleteDate'])->name('workshop.deleteDate');
        
        // NEW SYSTEM (Workshop Available Dates)
        Route::post('workshop/{workshop}/available-dates', [AdminWorkshopController::class, 'addAvailableDate'])->name('workshop.addDate');
        Route::delete('workshop-available-dates/{availableDate}', [AdminWorkshopController::class, 'removeAvailableDate'])->name('workshop.removeDate');
        
        Route::get('workshop/{workshop}/bookings', [AdminWorkshopController::class, 'bookings'])->name('workshop.bookings');
        Route::get('workshop/{workshop}/bookings/{booking}', [AdminWorkshopController::class, 'viewBooking'])->name('workshop.bookings.show');

        // Product Routes
        Route::resource('product', ProductController::class)->names([
            'index' => 'product.index',
            'create' => 'product.create',
            'store' => 'product.store',
            'show' => 'product.show',
            'edit' => 'product.edit',
            'update' => 'product.update',
            'destroy' => 'product.destroy',
        ]);

        // Order Management Routes
        Route::resource('orders', OrderController::class)->only(['index', 'show']);
        Route::patch('orders/{order}/status', [OrderController::class, 'updateStatus'])->name('orders.updateStatus');
        Route::post('orders/{order}/invoice', [OrderController::class, 'generateInvoice'])->name('orders.generateInvoice');
        Route::post('orders/{order}/cancel', [OrderController::class, 'cancel'])->name('orders.cancel');

        // Shipping Origin (Admin address for shipping calculation)
        Route::view('settings/shipping-origin', 'admin.settings.shipping-origin')->name('settings.shipping-origin');
    });

    // Customer Dashboard & Routes
    Route::middleware([CustomerMiddleware::class])->group(function () {
        Route::get('customer-dashboard', function () {
            $user = auth('web')->user();

            $recentOrders = $user
                ->orders()
                ->latest()
                ->take(5)
                ->get();

            $recentWorkshopBookings = $user
                ->workshopBookings()
                ->with(['workshopAvailableDate.workshop', 'slotSchedule.timeSlot', 'payments'])
                ->latest()
                ->take(5)
                ->get();

            $recentActivities = collect()
                ->merge($recentOrders->map(function ($order) {
                    $badge = $order->status_badge;

                    return [
                        'type' => 'order',
                        'at' => $order->created_at,
                        'title' => $order->order_number,
                        'subtitle' => 'Rp ' . number_format($order->total ?? 0, 0, ',', '.'),
                        'badge_label' => $badge['label'] ?? ucfirst($order->status),
                        'badge_class' => trim(($badge['bg'] ?? 'bg-gray-100') . ' ' . ($badge['text'] ?? 'text-gray-800')),
                        'url' => route('orders.show', $order),
                    ];
                }))
                ->merge($recentWorkshopBookings->map(function ($booking) {
                    $pay = $booking->payment_status;
                    $payLabel = match ($pay) {
                        'fully_paid' => 'Lunas',
                        'deposit_paid' => 'DP Dibayar',
                        'pending' => 'Menunggu Pembayaran',
                        'failed' => 'Gagal',
                        'expired' => 'Kadaluarsa',
                        'refunded' => 'Dikembalikan',
                        default => $pay ?? 'Unknown',
                    };
                    $payClass = match ($pay) {
                        'fully_paid' => 'bg-green-100 text-green-800',
                        'deposit_paid' => 'bg-blue-100 text-blue-800',
                        'pending' => 'bg-yellow-100 text-yellow-800',
                        'failed' => 'bg-red-100 text-red-800',
                        'expired' => 'bg-orange-100 text-orange-800',
                        'refunded' => 'bg-purple-100 text-purple-800',
                        default => 'bg-gray-100 text-gray-800',
                    };

                    $workshopTitle = $booking->workshopAvailableDate?->workshop?->title ?? $booking->workshop_name ?? 'Workshop';

                    $dateVal = $booking->slotSchedule?->date ?? $booking->workshopAvailableDate?->date ?? $booking->workshop_date ?? null;
                    $dateStr = is_string($dateVal) ? $dateVal : ($dateVal?->format('Y-m-d') ?? '-');

                    $startVal = $booking->slotSchedule?->timeSlot?->start_time ?? $booking->start_time ?? null;
                    $endVal = $booking->slotSchedule?->timeSlot?->end_time ?? $booking->end_time ?? null;

                    $startStr = is_string($startVal) ? substr($startVal, 0, 5) : ($startVal?->format('H:i') ?? '-');
                    $endStr = is_string($endVal) ? substr($endVal, 0, 5) : ($endVal?->format('H:i') ?? '-');

                    return [
                        'type' => 'workshop',
                        'at' => $booking->created_at,
                        'title' => $workshopTitle,
                        'subtitle' => $booking->booking_number . ' • ' . $dateStr . ' ' . $startStr . '-' . $endStr,
                        'badge_label' => $payLabel,
                        'badge_class' => $payClass,
                        'url' => route('workshop.booking.detail', $booking),
                    ];
                }))
                ->sortByDesc('at')
                ->take(5)
                ->values();

            return view('customer-dashboard', compact('recentActivities'));
        })->name('customer.dashboard');
        
        // Order History
        Route::get('orders', function () {
            return view('orders.index');
        })->name('orders.index');
        Route::get('orders/{order}', [OrderController::class, 'showCustomer'])->name('orders.show');

        // Shopping Cart
        Route::get('cart', function () {
            return view('cart.index');
        })->name('cart.index');

        // Wishlist
        Route::get('wishlist', function () {
            return view('wishlist.index');
        })->name('wishlist');

        // Workshop Bookings
        Route::get('workshop/my-bookings', [WorkshopBookingController::class, 'myBookings'])->name('workshop.my-bookings');
        Route::get('workshop/booking/{booking}/detail', [WorkshopBookingController::class, 'detail'])->name('workshop.booking.detail');
    });

    // Shared Routes (for all authenticated users)
    Route::get('settings/profile', Profile::class)->name('profile.edit');
    Route::get('settings/password', Password::class)->name('user-password.edit');
    Route::get('settings/addresses', AddressSettings::class)->name('address.index');
    
    // Dashboard Routes
    Route::get('dashboard/alamat-saya', MyAddresses::class)->name('my-addresses');
    Route::get('dashboard/alamat-saya/tambah', FormAlamat::class)->name('add-address');
    Route::get('dashboard/alamat-saya/{id}/edit', FormAlamat::class)->name('edit-address');

    // Workshop Booking Routes (payment requires auth)
    Route::get('workshop/booking/{booking}/payment', [WorkshopBookingController::class, 'showPayment'])->name('workshop.booking.payment');
    Route::post('workshop/booking/{booking}/payment', [WorkshopBookingController::class, 'processPayment'])->name('workshop.booking.payment.post');
    Route::post('workshop/booking/{booking}/process-payment', [WorkshopBookingController::class, 'processPayment'])->name('workshop.booking.process-payment');
    Route::get('workshop/booking/{booking}/payment-status', [WorkshopBookingController::class, 'getPaymentStatus'])->name('workshop.booking.payment-status');
    Route::post('workshop/booking/{booking}/store-payment', [WorkshopBookingController::class, 'storePayment'])->name('workshop.booking.store-payment');
    Route::get('workshop/booking/{booking}/confirmation', [WorkshopBookingController::class, 'confirmation'])->name('workshop.booking.confirmation');
    Route::post('workshop/booking/{booking}/cancel', [WorkshopBookingController::class, 'cancel'])->name('workshop.booking.cancel');
    Route::post('workshop/booking/{booking}/pay-remaining', [WorkshopBookingController::class, 'payRemaining'])->name('workshop.booking.pay-remaining');
});

// Test Routes
Route::get('test-livewire', function () {
    return view('test-livewire');
});

// Checkout & Payment Routes (Standalone, not in dashboard)
Route::middleware(['auth'])->group(function () {
    Route::get('checkout', \App\Livewire\Checkout::class)->name('checkout');
    Route::get('payment/{order}', [\App\Http\Controllers\PaymentController::class, 'show'])->name('payment.checkout');
    Route::get('payment/{order}/success', [\App\Http\Controllers\PaymentController::class, 'success'])->name('payment.success');
    Route::get('payment/{order}/failed', [\App\Http\Controllers\PaymentController::class, 'failed'])->name('payment.failed');
    
    // API Payment Routes
    Route::get('api/payment/tripay/checkout', [\App\Http\Controllers\Api\PaymentApiController::class, 'generateTripayCheckout']);
    Route::get('api/payment/{order}/status', [\App\Http\Controllers\Api\PaymentApiController::class, 'getPaymentStatus']);
    Route::post('api/payment/{order}/process', [\App\Http\Controllers\Api\PaymentApiController::class, 'processOrderPayment']);
});

// Tripay Webhook (HARUS tanpa auth middleware untuk Tripay bisa kirim callback)
use App\Http\Controllers\TripayWebhookController;
use App\Http\Controllers\Api\AddressController;

Route::post('/webhooks/tripay', [TripayWebhookController::class, 'handle'])
    ->name('tripay.webhook')
    ->withoutMiddleware(['web']); // Skip session & CSRF untuk webhook

// API Routes with Session Auth (accessible from web pages)
Route::prefix('api')->middleware('auth')->group(function () {
    Route::get('/debug/user', function() {
        return response()->json([
            'authenticated' => true,
            'user_id' => auth()->id(),
            'user_name' => auth()->user()->name,
            'user_email' => auth()->user()->email,
        ]);
    });
    
    Route::prefix('user/addresses')->group(function () {
        Route::get('/', [AddressController::class, 'index']);
        Route::post('/', [AddressController::class, 'store']);
        Route::delete('/{id}', [AddressController::class, 'destroy']);
    });
});

// Test Endpoint (untuk development saja)
if (env('APP_DEBUG')) {
    Route::get('/test/payment-response', function () {
        return response()->json([
            'success' => true,
            'checkout_url' => 'https://example.com/checkout',
            'reference' => 'TEST_REF_12345',
            'payment_details' => [
                'method' => 'BCAVA',
                'method_name' => 'BCA Virtual Account',
                'type' => 'virtual_account',
                'account_number' => '1234567890',
                'customer_id' => '1234567890',
                'bank_name' => 'BCA Virtual Account',
            ],
            'method' => 'BCAVA',
            'amount' => 100000,
        ]);
    });
}