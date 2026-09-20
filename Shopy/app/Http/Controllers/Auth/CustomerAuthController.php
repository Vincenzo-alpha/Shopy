<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\CustomerLoginRequest;
use App\Http\Requests\Auth\CustomerRegisterRequest;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class CustomerAuthController extends Controller
{
    public function showLogin()
    {
        if (Auth::guard('customer')->check()) {
            return redirect()->route('customer.dashboard');
        }
        return view('auth.customer-login');
    }

    public function showRegister()
    {
        if (Auth::guard('customer')->check()) {
            return redirect()->route('customer.dashboard');
        }
        return view('auth.customer-register');
    }

    public function register(CustomerRegisterRequest $request)
    {
        $validated = $request->validated();

        $uniqueNo = 'CUS-' . date('Ymd') . '-' . strtoupper(Str::random(4));

        $customer = Customer::create([
            'customer_unique_no' => $uniqueNo,
            'customer_name' => $validated['customer_name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'contact_no' => $validated['contact_no'],
            'address' => $validated['address'],
            'city' => $validated['city'],
            'state' => $validated['state'],
            'account_status' => 'active',
        ]);

        Auth::guard('customer')->login($customer);
        $request->session()->regenerate();

        return redirect()->route('customer.dashboard')->with('success', 'Account registered successfully! Welcome to Shopy.');
    }

    public function login(CustomerLoginRequest $request)
    {
        $credentials = $request->only(['email', 'password']);

        if (Auth::guard('customer')->attempt($credentials, $request->boolean('remember'))) {
            $customer = Auth::guard('customer')->user();
            if ($customer->account_status !== 'active') {
                Auth::guard('customer')->logout();
                return back()->with('error', 'Your account is currently ' . $customer->account_status . '. Please reach out to customer support.');
            }

            $request->session()->regenerate();
            return redirect()->intended(route('marketplace.index'))->with('success', 'Logged in successfully.');
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our customer records.',
        ])->onlyInput('email');
    }

    public function logout(Request $request)
    {
        Auth::guard('customer')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('marketplace.index')->with('info', 'Logged out successfully.');
    }
}
