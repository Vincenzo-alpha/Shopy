@extends('layouts.admin')

@section('title', 'Listings Moderation - Admin')
@section('page_title', 'Marketplace Listings')

@section('content')
<div class="card border-0 shadow-sm overflow-hidden">
    <div class="card-header bg-white py-3">
        <h6 class="fw-bold mb-0">Products & Services (sk_product_sevice_master)</h6>
    </div>
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th class="ps-4">Item Ref</th>
                    <th>Title</th>
                    <th>Seller</th>
                    <th>Type</th>
                    <th>Category</th>
                    <th>Listed Price</th>
                    <th>Rate Range</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse($listings as $item)
                    <tr>
                        <td class="ps-4 font-monospace small text-primary fw-semibold">{{ $item->prod_servics_unique_no }}</td>
                        <td>
                            <div class="fw-bold">{{ $item->prod_service_name }}</div>
                            <small class="text-muted text-truncate d-inline-block" style="max-width: 250px;">{{ $item->description }}</small>
                        </td>
                        <td>
                            <div>{{ $item->seller->seller_name }}</div>
                            <small class="text-muted">{{ $item->seller->city }}</small>
                        </td>
                        <td>
                            <span class="badge {{ $item->item_type === 'service' ? 'badge-type-service' : 'badge-type-product' }}">
                                {{ ucfirst($item->item_type) }}
                            </span>
                        </td>
                        <td><span class="badge bg-light text-dark border">{{ $item->category }}</span></td>
                        <td class="fw-bold text-dark">₹{{ number_format($item->listed_price, 2) }}</td>
                        <td class="small text-muted">₹{{ number_format($item->minimum_rate, 2) }} – ₹{{ number_format($item->maximum_rate, 2) }}</td>
                        <td>
                            @if($item->availability_status === 'available')
                                <span class="badge bg-success-subtle text-success border border-success">Available</span>
                            @else
                                <span class="badge bg-secondary">{{ ucfirst($item->availability_status) }}</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="text-center py-4 text-muted">No listings found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($listings->hasPages())
        <div class="card-footer bg-white border-0 py-3 d-flex justify-content-center">
            {{ $listings->links('pagination::bootstrap-5') }}
        </div>
    @endif
</div>
@endsection
