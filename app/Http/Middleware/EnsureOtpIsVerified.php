<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureOtpIsVerified
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();

        // Allow guest access to public routes
        if (!$user) {
            return $next($request);
        }

        // IMPORTANT: Only check OTP verification for specific routes
        // Allow profile and orders without OTP verification
        
        // Get current route name
        $routeName = $request->route()->getName();
        
        // Define routes that DO require OTP verification
        $requireOtpRoutes = [
            // Add specific routes that need OTP verification here
            // Example: 'checkout.index' if you want checkout to require OTP
        ];
        
        // If user hasn't verified email AND trying to access route that requires OTP
        if (!$user->email_verified_at && in_array($routeName, $requireOtpRoutes)) {
            // Redirect to OTP verification only for specific routes
            return redirect()->route('otp.verify.show')
                ->with('info', 'Please verify your email to access this feature.');
        }

        return $next($request);
    }
}