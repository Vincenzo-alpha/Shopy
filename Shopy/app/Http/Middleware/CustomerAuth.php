<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CustomerAuth
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!Auth::guard('customer')->check()) {
            if ($request->expectsJson()) {
                return response()->json(['error' => 'Unauthenticated.'], 401);
            }
            return redirect()->route('customer.login')->with('warning', 'Please login to continue.');
        }

        $customer = Auth::guard('customer')->user();
        if ($customer->account_status === 'suspended' || $customer->account_status === 'deactivated') {
            Auth::guard('customer')->logout();
            return redirect()->route('customer.login')->with('error', 'Your customer account has been ' . $customer->account_status . '. Please contact support.');
        }

        return $next($request);
    }
}
