@extends('layouts.app')

@section('title', 'My Interests - Shopy')

@section('content')
<div class="container py-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="fw-bold mb-1">My Expressed Interests</h3>
            <p class="text-muted small mb-0">Track items you are interested in and watch for seller deal initiation</p>
        </div>
        <div>
            <a href="{{ route('marketplace.index') }}" class="btn btn-outline-primary btn-sm">
                <i class="bi bi-grid me-1"></i> Browse More
            </a>
            <a href="{{ route('customer.deals.index') }}" class="btn btn-primary btn-sm ms-2">
                <i class="bi bi-briefcase me-1"></i> Active Deals
            </a>
        </div>
    </div>

    <div class="card border-0 shadow-sm overflow-hidden">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-4">Interest Ref</th>
                        <th>Product / Service</th>
                        <th>Seller</th>
                        <th>Listed Price</th>
                        <th>Status</th>
                        <th>Date Expressed</th>
                        <th class="text-end pe-4">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($interests as $interest)
                        <tr>
                            <td class="ps-4 fw-semibold text-primary font-monospace small">
                                {{ $interest->interest_unique_no }}
                            </td>
                            <td>
                                <div class="fw-bold">{{ $interest->product->prod_service_name }}</div>
                                <span class="badge {{ $interest->product->item_type === 'service' ? 'badge-type-service' : 'badge-type-product' }} small">
                                    {{ ucfirst($interest->product->item_type) }}
                                </span>
                            </td>
                            <td>
                                <div>{{ $interest->seller->seller_name }}</div>
                                <small class="text-muted"><i class="bi bi-geo-alt"></i> {{ $interest->seller->city }}</small>
                            </td>
                            <td class="fw-bold text-dark">
                                ₹{{ number_format($interest->product->listed_price, 2) }}
                            </td>
                            <td>
                                @if($interest->interest_status === 'Active')
                                    <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1">Active</span>
                                @elseif($interest->interest_status === 'Deal Denied' || ($interest->deal && $interest->deal->deal_status === 'cancelled'))
                                    <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-2 py-1">Deal Denied</span>
                                @elseif($interest->interest_status === 'Converted to deal')
                                    <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-2 py-1">Deal Created</span>
                                @else
                                    <span class="badge bg-secondary-subtle text-secondary px-2 py-1">Withdrawn</span>
                                @endif
                            </td>
                            <td class="text-muted small">
                                {{ $interest->created_at->format('M d, Y h:i A') }}
                            </td>
                            <td class="text-end pe-4">
                                @if($interest->deal)
                                    <a href="{{ route('customer.deals.show', $interest->deal->deal_id_pk) }}" class="btn {{ ($interest->interest_status === 'Deal Denied' || $interest->deal->deal_status === 'cancelled') ? 'btn-outline-secondary' : 'btn-primary' }} btn-sm">
                                        <i class="bi bi-chat-dots me-1"></i> {{ ($interest->interest_status === 'Deal Denied' || $interest->deal->deal_status === 'cancelled') ? 'View Deal' : 'Negotiate Deal' }}
                                    </a>
                                @elseif($interest->interest_status === 'Active')
                                    <form action="{{ route('customer.interests.withdraw', $interest->interest_id_pk) }}" method="POST" class="d-inline" onsubmit="return confirm('Withdraw interest for this item?');">
                                        @csrf
                                        <button type="submit" class="btn btn-outline-danger btn-sm">
                                            <i class="bi bi-x-circle me-1"></i> Withdraw
                                        </button>
                                    </form>
                                @else
                                    <span class="text-muted small">—</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-5 text-muted">
                                <i class="bi bi-heart display-4 opacity-25 d-block mb-3"></i>
                                <h5 class="fw-bold">No interests expressed yet</h5>
                                <p class="mb-3">Browse our local catalog to find products and services in your area.</p>
                                <a href="{{ route('marketplace.index') }}" class="btn btn-primary btn-sm">
                                    Explore Marketplace
                                </a>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($interests->hasPages())
            <div class="card-footer bg-white border-0 py-3 d-flex justify-content-center">
                {{ $interests->links('pagination::bootstrap-5') }}
            </div>
        @endif
    </div>
</div>
@endsection
