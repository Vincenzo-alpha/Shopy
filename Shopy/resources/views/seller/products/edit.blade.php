@extends('layouts.seller')

@section('title', 'Edit Listing - Seller Portal')
@section('page_title', 'Edit Listing')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-bottom py-3">
                <h5 class="fw-bold mb-0">Edit Listing: {{ $product->prod_service_name }}</h5>
                <small class="text-muted">Ref: {{ $product->prod_servics_unique_no }}</small>
            </div>
            <div class="card-body p-4">
                <form action="{{ route('seller.products.update', $product->prod_service_id_pk) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    
                    <div class="row g-3">
                        <div class="col-md-8">
                            <label class="form-label fw-semibold">Item / Service Title <span class="text-danger">*</span></label>
                            <input type="text" name="prod_service_name" class="form-control" value="{{ old('prod_service_name', $product->prod_service_name) }}" required>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Listing Type <span class="text-danger">*</span></label>
                            <select name="item_type" class="form-select" required>
                                <option value="product" {{ old('item_type', $product->item_type) == 'product' ? 'selected' : '' }}>Physical Product</option>
                                <option value="service" {{ old('item_type', $product->item_type) == 'service' ? 'selected' : '' }}>On-Site Service</option>
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Category <span class="text-danger">*</span></label>
                            <input type="text" name="category" class="form-control" value="{{ old('category', $product->category) }}" required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Listed Catalog Price (₹) <span class="text-danger">*</span></label>
                            <input type="number" step="0.01" name="listed_price" class="form-control" value="{{ old('listed_price', $product->listed_price) }}" required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Minimum Acceptable Rate (₹) <span class="text-danger">*</span></label>
                            <input type="number" step="0.01" name="minimum_rate" class="form-control" value="{{ old('minimum_rate', $product->minimum_rate) }}" required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Maximum Starting Rate (₹) <span class="text-danger">*</span></label>
                            <input type="number" step="0.01" name="maximum_rate" class="form-control" value="{{ old('maximum_rate', $product->maximum_rate) }}" required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Availability Status <span class="text-danger">*</span></label>
                            <select name="availability_status" class="form-select" required>
                                <option value="available" {{ old('availability_status', $product->availability_status) == 'available' ? 'selected' : '' }}>Available</option>
                                <option value="unavailable" {{ old('availability_status', $product->availability_status) == 'unavailable' ? 'selected' : '' }}>Unavailable</option>
                                <option value="deactivated" {{ old('availability_status', $product->availability_status) == 'deactivated' ? 'selected' : '' }}>Deactivated</option>
                            </select>
                        </div>

                        <div class="col-12">
                            <label class="form-label fw-semibold">Description</label>
                            <textarea name="description" class="form-control" rows="4">{{ old('description', $product->description) }}</textarea>
                        </div>

                        <div class="col-12">
                            <label class="form-label fw-semibold">Replace Image</label>
                            @if($product->image_path)
                                <div class="mb-2">
                                    <img src="{{ asset('storage/' . $product->image_path) }}" alt="" class="rounded" style="height: 70px; object-fit: cover;">
                                </div>
                            @endif
                            <input type="file" name="image" class="form-control" accept="image/png, image/jpeg, image/webp">
                        </div>
                    </div>

                    <div class="d-flex justify-content-end gap-2 mt-4 pt-3 border-top">
                        <a href="{{ route('seller.products.index') }}" class="btn btn-outline-secondary">Cancel</a>
                        <button type="submit" class="btn btn-primary px-4 fw-bold">Update Listing</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
var validationRules = {
    prod_service_name: {
        required: true,
        textType: 'textWithSpecial',
        minLength: 5,
        maxLength: 200,
        message: 'Item/service title is required (min 5 characters).',
    },
    category: {
        required: true,
        minLength: 2,
        maxLength: 100,
        message: 'Category is required.',
    },
    item_type: {
        required: true,
        message: 'Please select a listing type.',
    },
    listed_price: {
        required: true,
        pattern: /^[0-9]+(\.?[0-9]{0,2})?$/,
        message: 'Enter a valid catalog price.',
    },
    minimum_rate: {
        required: true,
        pattern: /^[0-9]+(\.?[0-9]{0,2})?$/,
        message: 'Enter a valid minimum rate.',
    },
    maximum_rate: {
        required: true,
        pattern: /^[0-9]+(\.?[0-9]{0,2})?$/,
        message: 'Enter a valid maximum rate.',
    },
    availability_status: {
        required: true,
        message: 'Please select an availability status.',
    },
    description: {
        required: false,
        maxLength: 2000,
    },
};
setupFormValidation('form', function () {
    const min = parseFloat($('[name="minimum_rate"]').val()) || 0;
    const max = parseFloat($('[name="maximum_rate"]').val()) || 0;
    if (min > max && max > 0) {
        const $min = $('[name="minimum_rate"]');
        $min.next('.invalid-feedback').remove();
        $min.addClass('is-invalid').after('<div class="invalid-feedback">Minimum rate must be ≤ maximum rate.</div>');
        return false;
    }
    return true;
});
</script>
@endsection
