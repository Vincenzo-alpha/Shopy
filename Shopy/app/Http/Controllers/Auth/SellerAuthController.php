<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Seller;
use App\Models\SellerCity;
use App\Models\SellerWallet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class SellerAuthController extends Controller
{
    public function showLogin()
    {
        if (Auth::guard('seller')->check()) {
            return redirect()->route('seller.dashboard');
        }
        return view('auth.seller-login');
    }

    public function showRegister()
    {
        if (Auth::guard('seller')->check()) {
            return redirect()->route('seller.dashboard');
        }
        return view('auth.seller-register');
    }

    public function register(Request $request)
    {
        $validated = $request->validate([
            'seller_name' => 'required|string|max:150',
            'email' => 'required|email|max:150|unique:sk_seller_master,email',
            'contact_no' => 'required|string|max:20',
            'password' => 'required|string|min:6|confirmed',
            'address' => 'required|string|max:500',
            'city' => 'required|string|max:100',
            'state' => 'required|string|max:100',
            'service_cities' => 'nullable|string|max:500', // comma-separated cities
        ]);

        DB::transaction(function () use ($validated, &$seller) {
            $uniqueNo = 'SEL-' . date('Ymd') . '-' . strtoupper(Str::random(4));

            $seller = Seller::create([
                'seller_unique_no' => $uniqueNo,
                'seller_name' => $validated['seller_name'],
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
                'contact_no' => $validated['contact_no'],
                'address' => $validated['address'],
                'city' => $validated['city'],
                'state' => $validated['state'],
                'account_status' => 'active',
            ]);

            // Initialize simulated wallet
            SellerWallet::create([
                'seller_id_fk' => $seller->seller_id_pk,
                'available_balance' => 0.00,
                'pending_balance' => 0.00,
            ]);

            // Add base city
            SellerCity::create([
                'seller_id_fk' => $seller->seller_id_pk,
                'city_name' => trim($validated['city']),
                'state' => $validated['state'],
            ]);

            // Add extra service cities if provided
            if (!empty($validated['service_cities'])) {
                $cities = array_filter(array_map('trim', explode(',', $validated['service_cities'])));
                foreach ($cities as $cityName) {
                    if (strcasecmp($cityName, $validated['city']) !== 0) {
                        SellerCity::firstOrCreate([
                            'seller_id_fk' => $seller->seller_id_pk,
                            'city_name' => $cityName,
                        ], [
                            'state' => $validated['state'],
                        ]);
                    }
                }
            }
        });

        Auth::guard('seller')->login($seller);
        $request->session()->regenerate();

        return redirect()->route('seller.dashboard')->with('success', 'Seller account created successfully! Welcome to Shopy.');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        if (Auth::guard('seller')->attempt($credentials, $request->boolean('remember'))) {
            $seller = Auth::guard('seller')->user();
            if ($seller->account_status !== 'active') {
                Auth::guard('seller')->logout();
                return back()->with('error', 'Your account is currently ' . $seller->account_status . '. Please reach out to administration.');
            }

            $request->session()->regenerate();
            return redirect()->intended(route('seller.dashboard'))->with('success', 'Logged in successfully.');
        }

        return back()->withErrors([
            'email' => 'The provided seller credentials do not match our records.',
        ])->onlyInput('email');
    }

    public function logout(Request $request)
    {
        Auth::guard('seller')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('seller.login')->with('info', 'Logged out of seller portal.');
    }
}
