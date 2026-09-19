@extends('layouts.seller')

@section('title', 'Add New Listing - Seller Portal')
@section('page_title', 'Create Listing')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-bottom py-3">
                <h5 class="fw-bold mb-0">New Product or Service Listing</h5>
                <small class="text-muted">Set clear negotiation boundaries to automate initial offers</small>
            </div>
            <div class="card-body p-4">
                <form action="{{ route('seller.products.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    
                    <div class="row g-3">
                        <div class="col-md-8">
                            <label class="form-label fw-semibold">Item / Service Title <span class="text-danger">*</span></label>
                            <input type="text" name="prod_service_name" class="form-control" placeholder="e.g. Wireless Noise Canceling Headphones" value="{{ old('prod_service_name') }}" required>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Listing Type <span class="text-danger">*</span></label>
                            <select name="item_type" class="form-select" required>
                                <option value="product" {{ old('item_type') == 'product' ? 'selected' : '' }}>Physical Product</option>
                                <option value="service" {{ old('item_type') == 'service' ? 'selected' : '' }}>On-Site Service</option>
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Category <span class="text-danger">*</span></label>
                            <input type="text" name="category" class="form-control" placeholder="e.g. Electronics, Home Cleaning, Repair" value="{{ old('category') }}" required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Listed Catalog Price (₹) <span class="text-danger">*</span></label>
                            <input type="number" step="0.01" name="listed_price" class="form-control" placeholder="e.g. 5000.00" value="{{ old('listed_price') }}" required>
                        </div>

                        <!-- Negotiation Rate Boundaries -->
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Minimum Acceptable Rate (₹) <span class="text-danger">*</span></label>
                            <input type="number" step="0.01" name="minimum_rate" class="form-control" placeholder="Lowest offer you will accept" value="{{ old('minimum_rate') }}" required>
                            <div class="form-text small">Offers below this amount will be rejected automatically.</div>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Maximum Starting Rate (₹) <span class="text-danger">*</span></label>
                            <input type="number" step="0.01" name="maximum_rate" class="form-control" placeholder="Initial negotiation starting amount" value="{{ old('maximum_rate') }}" required>
                            <div class="form-text small">When a deal is created, the starting offer will be set to this amount.</div>
                        </div>

                        <div class="col-12">
                            <label class="form-label fw-semibold">Description</label>
                            <textarea name="description" class="form-control" rows="4" placeholder="Describe features, condition, warranty, or scope of service...">{{ old('description') }}</textarea>
                        </div>

                        <div class="col-12">
                            <label class="form-label fw-semibold">Listing Image</label>
                            <input type="file" name="image" class="form-control" accept="image/png, image/jpeg, image/webp">
                            <div class="form-text small">Upload clear image (JPEG, PNG, WEBP, max 3MB).</div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end gap-2 mt-4 pt-3 border-top">
                        <a href="{{ route('seller.products.index') }}" class="btn btn-outline-secondary">Cancel</a>
                        <button type="submit" class="btn btn-primary px-4 fw-bold">
                            <i class="bi bi-check-circle me-1"></i> Save Listing
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
