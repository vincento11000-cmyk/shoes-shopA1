<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\VariantController;
use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\AdminProfileController;
use App\Http\Controllers\ShopController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\CustomerOrderController;
use App\Http\Controllers\FeedbackController;
use App\Http\Controllers\Auth\OtpVerificationController;
use App\Http\Controllers\Auth\PasswordResetOtpController;
use App\Http\Controllers\Auth\NewPasswordController;

// PUBLIC ROUTES (No middleware - accessible to everyone)
Route::get('/', [ShopController::class, 'home'])->name('home');
Route::get('/products', [ShopController::class, 'index'])->name('products.index');
Route::get('/products/{id}', [ShopController::class, 'show'])->name('products.show');

// OTP Verification Routes (only for registration verification)
Route::middleware(['auth'])->group(function () {
    Route::get('/verify-otp', [OtpVerificationController::class, 'show'])
        ->name('otp.verify.show');
    
    Route::post('/verify-otp', [OtpVerificationController::class, 'verify'])
        ->name('otp.verify');
    
    Route::post('/resend-otp', [OtpVerificationController::class, 'resend'])
        ->name('otp.resend');
});

Route::middleware('guest')->group(function () {
    // Request password reset (send OTP)
    Route::get('/forgot-password', [PasswordResetOtpController::class, 'create'])
        ->name('password.request');
    
    Route::post('/forgot-password', [PasswordResetOtpController::class, 'store'])
        ->name('password.email'); // This is IMPORTANT - keep this name
    
    // Verify password reset OTP
    Route::get('/verify-password-otp', [PasswordResetOtpController::class, 'showOtpForm'])
        ->name('password.verify.otp');
    
    Route::post('/verify-password-otp', [PasswordResetOtpController::class, 'verifyOtp'])
        ->name('password.verify.otp.submit'); // Different name for POST
    
    Route::post('/resend-password-otp', [PasswordResetOtpController::class, 'resendOtp'])
        ->name('password.resend.otp');
    
    // Reset password after OTP verification
    Route::get('/reset-password/{token}', [NewPasswordController::class, 'create'])
        ->name('password.reset');
    
    Route::post('/reset-password', [NewPasswordController::class, 'store'])
        ->name('password.update');
});

// AUTH ROUTES (Protected by auth middleware - NO OTP required for profile/orders)
Route::middleware('auth')->group(function () {
    // Profile routes - NO OTP required
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    
    // Profile picture routes
    Route::patch('/profile/picture', [ProfileController::class, 'updatePicture'])->name('profile.picture.update');
    Route::delete('/profile/picture', [ProfileController::class, 'destroyPicture'])->name('profile.picture.destroy');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    
    // Cart routes - NO OTP required
    Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
    Route::post('/cart/add', [CartController::class, 'add'])->name('cart.add');
    Route::post('/cart/update/{id}', [CartController::class, 'update'])->name('cart.update');
    Route::post('/cart/remove/{id}', [CartController::class, 'remove'])->name('cart.remove');
    
    // Customer order routes - NO OTP required
    Route::get('/orders', [CustomerOrderController::class, 'index'])->name('orders');
    Route::get('/orders/{order}', [CustomerOrderController::class, 'show'])->name('orders.show');
    Route::get('/orders/{order}/confirmation', [CustomerOrderController::class, 'confirmation'])->name('orders.confirmation');
    Route::post('/orders/{order}/cancel', [CustomerOrderController::class, 'cancel'])->name('orders.cancel');
    
    // CHECKOUT ROUTES - Add OTP verification ONLY for checkout if you want
    Route::middleware(['otp.verified'])->group(function () {
        Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout.index');
        Route::post('/checkout/cod', [CheckoutController::class, 'processCOD'])->name('checkout.cod');
        Route::post('/checkout/pay', [PaymentController::class, 'pay'])->name('checkout.pay');
        
        // Payment routes
        Route::get('/payment/success', [PaymentController::class, 'success'])->name('payment.success');
        Route::get('/payment/failed', [PaymentController::class, 'failed'])->name('payment.failed');
    });
});

// Feedback routes (public)
Route::get('/contact', [FeedbackController::class, 'create'])->name('feedback.create');
Route::post('/contact', [FeedbackController::class, 'store'])->name('feedback.store');
Route::get('/contact/thankyou', [FeedbackController::class, 'thankyou'])->name('feedback.thankyou');

// Admin message management (requires auth only)
Route::middleware(['auth'])->prefix('admin')->group(function() {
    Route::get('/messages', [FeedbackController::class, 'index'])->name('admin.messages.index');
    Route::get('/messages/{message}', [FeedbackController::class, 'show'])->name('admin.messages.show');
    Route::put('/messages/{message}', [FeedbackController::class, 'update'])->name('admin.messages.update');
    Route::delete('/messages/{message}', [FeedbackController::class, 'destroy'])->name('admin.messages.destroy');
});

// ADMIN ROUTES (requires auth only)
Route::middleware(['auth'])->prefix('admin')->name('admin.')->group(function() {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // Admin orders routes
    Route::get('/orders', [OrderController::class, 'index'])->name('orders');
    Route::get('/orders/{order}', [OrderController::class, 'show'])->name('orders.show');
    Route::post('/orders/{order}/status', [OrderController::class, 'updateStatus'])->name('orders.update');
    
    Route::resource('categories', CategoryController::class);
    Route::resource('products', ProductController::class);
    Route::resource('variants', VariantController::class);

    // Admin profile routes
    Route::get('/profile', [AdminProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [AdminProfileController::class, 'update'])->name('profile.update');

    Route::get('/orders/{order}/receipt', [OrderController::class, 'receipt'])->name('orders.receipt');
});

// Debug route (optional - public)
Route::get('/debug-paypal-config', function() {
    echo "<h2>PayPal Configuration Debug</h2>";
    // ... rest of debug code
    return "";
});




// ONE-CLICK OTP TEST
Route::get('/one-click-otp-test', function() {
    // Get or create test user
    $user = \App\Models\User::first();
    
    if (!$user) {
        return "No users in database. Please register first.";
    }
    
    echo "<h1>One-Click OTP Test</h1>";
    echo "<p>Testing with user: {$user->email}</p>";
    
    // Step 1: Set OTP in database
    \Illuminate\Support\Facades\DB::table('users')
        ->where('id', $user->id)
        ->update([
            'otp' => '999999',
            'otp_purpose' => 'verification',
            'otp_expires_at' => now()->addHours(1),
            'email_verified_at' => null,
        ]);
    
    echo "<p>✓ Step 1: Set OTP '999999' in database</p>";
    
    // Step 2: Check OTP
    $check = \Illuminate\Support\Facades\DB::table('users')
        ->where('id', $user->id)
        ->select('otp', 'otp_purpose', 'email_verified_at')
        ->first();
    
    echo "<p>✓ Step 2: Database check - OTP: {$check->otp}, Purpose: {$check->otp_purpose}</p>";
    
    // Step 3: Verify OTP
    if ($check->otp === '999999' && $check->otp_purpose === 'verification') {
        \Illuminate\Support\Facades\DB::table('users')
            ->where('id', $user->id)
            ->update([
                'email_verified_at' => now(),
                'otp' => null,
                'otp_purpose' => null,
                'otp_expires_at' => null,
            ]);
        
        echo "<p style='color: green;'>✓ Step 3: OTP verified! Email marked as verified.</p>";
    }
    
    // Step 4: Final check
    $final = \Illuminate\Support\Facades\DB::table('users')
        ->where('id', $user->id)
        ->select('email_verified_at', 'otp')
        ->first();
    
    echo "<p>✓ Step 4: Final check - Email verified: " . 
         ($final->email_verified_at ? 'YES' : 'NO') . 
         ", OTP: " . ($final->otp ?: 'NULL') . "</p>";
    
    echo "<hr>";
    echo "<h3>Complete OTP Flow Tested Successfully!</h3>";
    echo "<p>The database operations are working. If your OTP system isn't working, check:</p>";
    echo "<ol>";
    echo "<li>Is the OtpTrait being called? (check logs at /check-otp-logs)</li>";
    echo "<li>Is the OTP being generated and saved?</li>";
    echo "<li>Is the verification method being called?</li>";
    echo "</ol>";
    
    return '';
});


// Simple OTP Debug Route
Route::get('/simple-otp-debug', function() {
    $user = \Illuminate\Support\Facades\Auth::user();
    
    if (!$user) {
        return 'Please <a href="/login">login</a> first.';
    }
    
    $output = "<h1>OTP Debug</h1>";
    $output .= "<h3>User: {$user->email}</h3>";
    
    // 1. Check current state
    $dbData = \Illuminate\Support\Facades\DB::table('users')
        ->where('id', $user->id)
        ->select('otp', 'otp_purpose', 'otp_expires_at', 'email_verified_at')
        ->first();
    
    $output .= "<h4>Current Database State:</h4>";
    $output .= "<pre>" . json_encode($dbData, JSON_PRETTY_PRINT) . "</pre>";
    
    // 2. Test sending OTP
    $output .= "<h4>Test OTP Sending:</h4>";
    
    // Clear first
    \Illuminate\Support\Facades\DB::table('users')
        ->where('id', $user->id)
        ->update(['otp' => null, 'otp_purpose' => null]);
    
    // Send test OTP using direct DB
    $testOtp = '123456';
    $updateResult = \Illuminate\Support\Facades\DB::table('users')
        ->where('id', $user->id)
        ->update([
            'otp' => $testOtp,
            'otp_purpose' => 'verification',
            'otp_expires_at' => now()->addMinutes(5),
        ]);
    
    $output .= "DB Update result: {$updateResult} rows affected<br>";
    
    // Check
    $afterUpdate = \Illuminate\Support\Facades\DB::table('users')
        ->where('id', $user->id)
        ->select('otp', 'otp_purpose')
        ->first();
    
    $output .= "After update - OTP: " . ($afterUpdate->otp ?? 'NULL') . "<br>";
    $output .= "After update - Purpose: " . ($afterUpdate->otp_purpose ?? 'NULL') . "<br>";
    
    // 3. Test verification
    $output .= "<h4>Test OTP Verification:</h4>";
    $output .= "<form method='POST' action='/simple-verify-test'>";
    $output .= csrf_field();
    $output .= "<input type='hidden' name='test_otp' value='{$testOtp}'>";
    $output .= "<button type='submit'>Test Verify with OTP: {$testOtp}</button>";
    $output .= "</form>";
    
    return $output;
});

Route::post('/simple-verify-test', function(\Illuminate\Http\Request $request) {
    $user = \Illuminate\Support\Facades\Auth::user();
    $testOtp = $request->input('test_otp');
    
    $output = "<h1>OTP Verification Test</h1>";
    $output .= "<h3>User: {$user->email}</h3>";
    
    // Get current data
    $before = \Illuminate\Support\Facades\DB::table('users')
        ->where('id', $user->id)
        ->select('otp', 'otp_purpose', 'email_verified_at')
        ->first();
    
    $output .= "<h4>Before Verification:</h4>";
    $output .= "<pre>" . json_encode($before, JSON_PRETTY_PRINT) . "</pre>";
    
    // Check if OTP matches
    if ($before->otp === $testOtp && $before->otp_purpose === 'verification') {
        // Update
        \Illuminate\Support\Facades\DB::table('users')
            ->where('id', $user->id)
            ->update([
                'email_verified_at' => now(),
                'otp' => null,
                'otp_purpose' => null,
                'otp_expires_at' => null,
            ]);
        
        $output .= "<p style='color: green;'>✓ OTP Verified! Email marked as verified.</p>";
    } else {
        $output .= "<p style='color: red;'>✗ OTP Verification Failed</p>";
        $output .= "<p>Reason: ";
        if ($before->otp !== $testOtp) $output .= "OTP doesn't match. ";
        if ($before->otp_purpose !== 'verification') $output .= "Purpose is not 'verification'. ";
        $output .= "</p>";
    }
    
    // Check after
    $after = \Illuminate\Support\Facades\DB::table('users')
        ->where('id', $user->id)
        ->select('email_verified_at', 'otp', 'otp_purpose')
        ->first();
    
    $output .= "<h4>After Verification:</h4>";
    $output .= "<pre>" . json_encode($after, JSON_PRETTY_PRINT) . "</pre>";
    
    $output .= "<p><a href='/simple-otp-debug'>Test Again</a></p>";
    $output .= "<p><a href='/check-logs'>Check Logs</a></p>";
    
    return $output;
});

Route::get('/check-logs', function() {
    $logPath = storage_path('logs/laravel.log');
    
    if (!file_exists($logPath)) {
        return 'No log file found at: ' . $logPath;
    }
    
    $content = file_get_contents($logPath);
    $lines = explode("\n", $content);
    
    $output = "<h1>Laravel Logs (OTP Related)</h1>";
    $output .= "<pre style='background: #f0f0f0; padding: 10px; max-height: 500px; overflow: auto;'>";
    
    $otpLines = [];
    foreach ($lines as $line) {
        if (stripos($line, 'otp') !== false || stripos($line, 'OTP') !== false) {
            $otpLines[] = $line;
        }
    }
    
    if (empty($otpLines)) {
        $output .= "No OTP-related logs found.\n";
        $output .= "Showing last 50 lines instead:\n\n";
        foreach (array_slice($lines, -50) as $line) {
            $output .= htmlspecialchars($line) . "\n";
        }
    } else {
        foreach (array_slice($otpLines, -100) as $line) {
            $output .= htmlspecialchars($line) . "\n";
        }
    }
    
    $output .= "</pre>";
    
    return $output;
});


require __DIR__.'/auth.php';