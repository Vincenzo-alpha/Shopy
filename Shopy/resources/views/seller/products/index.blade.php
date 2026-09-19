@extends('layouts.seller')

@section('title', 'My Products & Services - Seller Portal')
@section('page_title', 'Products & Services')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h5 class="fw-bold mb-1">Catalog Listings</h5>
        <small class="text-muted">Manage your listed products and on-site services</small>
    </div>
    <a href="{{ route('seller.products.create') }}" class="btn btn-primary btn-sm">
        <i class="bi bi-plus-circle me-1"></i> Add New Listing
    </a>
</div>

<div class="card border-0 shadow-sm overflow-hidden">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th class="ps-4">Item Ref</th>
                    <th>Listing Name</th>
                    <th>Category</th>
                    <th>Type</th>
                    <th>Listed Price</th>
                    <th>Negotiable Range</th>
                    <th>Status</th>
                    <th class="text-end pe-4">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($products as $product)
                    <tr>
                        <td class="ps-4 font-monospace small text-primary fw-semibold">
                            {{ $product->prod_servics_unique_no }}
                        </td>
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                @if($product->image_path)
                                    <img src="{{ asset('storage/' . $product->image_path) }}" alt="" class="rounded" style="width: 40px; height: 40px; object-fit: cover;">
                                @else
                                    <div class="rounded bg-light p-2 text-center text-muted" style="width: 40px; height: 40px;">
                                        <i class="bi {{ $product->item_type === 'service' ? 'bi-tools' : 'bi-box' }}"></i>
                                    </div>
                                @endif
                                <span class="fw-bold text-dark">{{ $product->prod_service_name }}</span>
                            </div>
                        </td>
                        <td><span class="badge bg-light text-dark border">{{ $product->category }}</span></td>
                        <td>
                            <span class="badge {{ $product->item_type === 'service' ? 'badge-type-service' : 'badge-type-product' }}">
                                {{ ucfirst($product->item_type) }}
                            </span>
                        </td>
                        <td class="fw-bold text-primary">₹{{ number_format($product->listed_price, 2) }}</td>
                        <td class="small text-muted">
                            ₹{{ number_format($product->minimum_rate, 2) }} – ₹{{ number_format($product->maximum_rate, 2) }}
                        </td>
                        <td>
                            @if($product->availability_status === 'available')
                                <span class="badge bg-success-subtle text-success border border-success-subtle">Available</span>
                            @elseif($product->availability_status === 'unavailable')
                                <span class="badge bg-warning-subtle text-warning border border-warning-subtle">Unavailable</span>
                            @else
                                <span class="badge bg-secondary-subtle text-secondary">Deactivated</span>
                            @endif
                        </td>
                        <td class="text-end pe-4">
                            <div class="d-flex justify-content-end gap-1">
                                <a href="{{ route('seller.products.edit', $product->prod_service_id_pk) }}" class="btn btn-outline-secondary btn-sm" title="Edit">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <form action="{{ route('seller.products.toggle', $product->prod_service_id_pk) }}" method="POST" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-outline-primary btn-sm" title="Toggle Availability">
                                        <i class="bi bi-arrow-repeat"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="text-center py-5 text-muted">
                            <i class="bi bi-box-seam display-4 opacity-25 d-block mb-3"></i>
                            <h6 class="fw-bold">No listings yet</h6>
                            <p class="small mb-3">Add your first product or service to begin receiving buyer interest.</p>
                            <a href="{{ route('seller.products.create') }}" class="btn btn-primary btn-sm">Add Listing</a>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($products->hasPages())
        <div class="card-footer bg-white border-0 py-3 d-flex justify-content-center">
            {{ $products->links('pagination::bootstrap-5') }}
        </div>
    @endif
</div>
@endsection
