<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Interest;
use App\Models\ProductService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class InterestController
{
    public function expressInterest(Request $request)
    {
        $request->validate([
            'prod_service_id' => 'required|exists:sk_product_sevice_master,prod_service_id_pk',
        ]);

        $customer = Auth::guard('customer')->user();
        $product = ProductService::findOrFail($request->prod_service_id);

        // Prevent duplicate active interest
        $existing = Interest::where('customer_id_fk', $customer->customer_id_pk)
            ->where('prod_service_id_fk', $product->prod_service_id_pk)
            ->where('interest_status', 'Active')
            ->first();

        if ($existing) {
            if ($request->ajax()) {
                return response()->json([
                    'status' => 'exists',
                    'message' => 'You have already expressed active interest in this item.',
                ]);
            }
            return back()->with('info', 'You have already expressed active interest in this item.');
        }

        $uniqueNo = 'INT-' . date('Ymd') . '-' . strtoupper(Str::random(4));

        $interest = Interest::create([
            'interest_unique_no' => $uniqueNo,
            'seller_id_fk' => $product->seller_id_fk,
            'customer_id_fk' => $customer->customer_id_pk,
            'prod_service_id_fk' => $product->prod_service_id_pk,
            'interest_status' => 'Active',
        ]);

        if ($request->ajax()) {
            return response()->json([
                'status' => 'success',
                'message' => 'Interest expressed successfully! The seller has been notified.',
                'interest' => $interest,
            ]);
        }

        return redirect()->route('customer.interests')->with('success', 'Interest expressed successfully!');
    }

    public function myInterests()
    {
        $customer = Auth::guard('customer')->user();
        $interests = Interest::with(['product.seller', 'deal'])
            ->where('customer_id_fk', $customer->customer_id_pk)
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('customer.interests', compact('interests'));
    }

    public function withdrawInterest($id)
    {
        $customer = Auth::guard('customer')->user();
        $interest = Interest::where('interest_id_pk', $id)
            ->where('customer_id_fk', $customer->customer_id_pk)
            ->firstOrFail();

        if ($interest->interest_status === 'Converted to deal') {
            return back()->with('error', 'Cannot withdraw interest: a deal has already been initiated.');
        }

        $interest->update(['interest_status' => 'Withdrawn']);

        return back()->with('success', 'Interest has been withdrawn.');
    }
}
