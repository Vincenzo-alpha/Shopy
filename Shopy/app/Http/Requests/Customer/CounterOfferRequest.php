<?php

namespace App\Http\Requests\Customer;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class CounterOfferRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Auth::guard('customer')->check();
    }

    public function rules(): array
    {
        // Resolve the deal to get dynamic min/max for this specific deal
        $deal = \App\Models\DealArchive::with('product')
            ->where('deal_id_pk', $this->route('id'))
            ->where('customer_id_fk', Auth::guard('customer')->id())
            ->first();

        $min = $deal ? (float) $deal->product->minimum_rate : 1;
        $max = $deal ? (float) $deal->product->maximum_rate : 9999999;

        return [
            'offer_amount' => "required|numeric|min:{$min}|max:{$max}",
            'notes'        => 'nullable|string|max:255',
        ];
    }

    public function messages(): array
    {
        return [
            'offer_amount.required' => 'Please enter an offer amount.',
            'offer_amount.numeric'  => 'Offer amount must be a valid number.',
            'offer_amount.min'      => 'Offer must be at least the seller\'s minimum acceptable rate.',
            'offer_amount.max'      => 'Offer cannot exceed the seller\'s maximum starting rate.',
            'notes.max'             => 'Note cannot exceed 255 characters.',
        ];
    }
}
