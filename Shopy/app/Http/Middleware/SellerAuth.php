<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class SellerAuth
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!Auth::guard('seller')->check()) {
            if ($request->expectsJson()) {
                return response()->json(['error' => 'Unauthenticated.'], 401);
            }
            return redirect()->route('seller.login')->with('warning', 'Please login to access the seller portal.');
        }

        $seller = Auth::guard('seller')->user();
        if ($seller->account_status === 'suspended' || $seller->account_status === 'deactivated') {
            Auth::guard('seller')->logout();
            return redirect()->route('seller.login')->with('error', 'Your seller account has been ' . $seller->account_status . '. Please contact support.');
        }

        return $next($request);
    }
}
