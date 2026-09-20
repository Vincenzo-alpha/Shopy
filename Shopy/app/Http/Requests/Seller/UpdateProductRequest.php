<?php

namespace App\Http\Requests\Seller;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class UpdateProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Auth::guard('seller')->check();
    }

    public function rules(): array
    {
        return [
            'prod_service_name'   => 'required|string|min:5|max:200',
            'category'            => 'required|string|min:2|max:100',
            'item_type'           => 'required|in:product,service',
            'listed_price'        => 'required|numeric|min:1|max:9999999',
            'minimum_rate'        => 'required|numeric|min:1|lte:maximum_rate',
            'maximum_rate'        => 'required|numeric|min:1|gte:minimum_rate',
            'description'         => 'nullable|string|max:2000',
            'image'               => 'nullable|image|mimes:jpeg,png,jpg,webp|max:3072',
            'availability_status' => 'required|in:available,unavailable,deactivated',
        ];
    }

    public function messages(): array
    {
        return [
            'prod_service_name.required'   => 'Item/service title is required.',
            'prod_service_name.min'        => 'Title must be at least 5 characters.',
            'category.required'            => 'Category is required.',
            'item_type.required'           => 'Please select a listing type.',
            'item_type.in'                 => 'Listing type must be Product or Service.',
            'listed_price.required'        => 'Listed catalog price is required.',
            'listed_price.min'             => 'Listed price must be at least ₹1.',
            'minimum_rate.required'        => 'Minimum acceptable rate is required.',
            'minimum_rate.lte'             => 'Minimum rate must be less than or equal to the maximum rate.',
            'maximum_rate.required'        => 'Maximum starting rate is required.',
            'maximum_rate.gte'             => 'Maximum rate must be greater than or equal to the minimum rate.',
            'availability_status.required' => 'Availability status is required.',
            'availability_status.in'       => 'Invalid availability status selected.',
            'image.image'                  => 'Uploaded file must be an image.',
            'image.mimes'                  => 'Image must be JPEG, PNG, or WEBP format.',
            'image.max'                    => 'Image must not exceed 3MB.',
        ];
    }
}
