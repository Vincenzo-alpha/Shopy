@extends('layouts.app')

@section('title', 'Active Deals - Shopy')

@section('content')
<div class="container py-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="fw-bold mb-1">Active Deals & Negotiations</h3>
            <p class="text-muted small mb-0">Deals in progress, negotiations, payments, and collections</p>
        </div>
        <div>
            <a href="{{ route('customer.deals.completed') }}" class="btn btn-outline-secondary btn-sm">
                <i class="bi bi-clock-history me-1"></i> Completed Deals History
            </a>
        </div>
    </div>

    <div class="card border-0 shadow-sm overflow-hidden">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-4">Deal ID</th>
                        <th>Item / Service</th>
                        <th>Seller</th>
                        <th>Current Status</th>
                        <th>Latest Offer</th>
                        <th>Payment</th>
                        <th class="text-end pe-4">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($activeDeals as $deal)
                        <tr>
                            <td class="ps-4 fw-semibold text-primary font-monospace small">
                                {{ $deal->deal_unique_no }}
                            </td>
                            <td>
                                <div class="fw-bold">{{ $deal->product->prod_service_name }}</div>
                                <span class="badge {{ $deal->product->item_type === 'service' ? 'badge-type-service' : 'badge-type-product' }} small">
                                    {{ ucfirst($deal->product->item_type) }}
                                </span>
                            </td>
                            <td>
                                <div>{{ $deal->seller->seller_name }}</div>
                                <small class="text-muted"><i class="bi bi-geo-alt"></i> {{ $deal->seller->city }}</small>
                            </td>
                            <td>
                                @if($deal->deal_status === 'negotiating')
                                    <span class="badge bg-warning-subtle text-warning-emphasis border border-warning px-2 py-1">Negotiating</span>
                                @elseif($deal->deal_status === 'payment_pending')
                                    <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-2 py-1">Payment Pending</span>
                                @elseif($deal->deal_status === 'in_fulfillment')
                                    <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1">Paid • Ready for Collection</span>
                                @else
                                    <span class="badge bg-light text-dark">{{ ucfirst($deal->deal_status) }}</span>
                                @endif
                            </td>
                            <td>
                                @if($deal->agreed_amount)
                                    <div class="fw-bold text-success fs-6">₹{{ number_format($deal->agreed_amount, 2) }}</div>
                                    <small class="text-muted">Agreed Price</small>
                                @elseif($deal->negotiation)
                                    <div>Seller: <strong>₹{{ number_format($deal->negotiation->seller_negotiation_amt, 2) }}</strong></div>
                                    <small class="text-muted">You: ₹{{ number_format($deal->negotiation->customer_negotiation_amt, 2) }}</small>
                                @endif
                            </td>
                            <td>
                                @if($deal->payment_status === 'paid')
                                    <span class="badge bg-success"><i class="bi bi-check2"></i> Paid</span>
                                @elseif($deal->payment_status === 'failed')
                                    <span class="badge bg-danger">Failed</span>
                                @else
                                    <span class="badge bg-secondary">Unpaid</span>
                                @endif
                            </td>
                            <td class="text-end pe-4">
                                <a href="{{ route('customer.deals.show', $deal->deal_id_pk) }}" class="btn btn-primary btn-sm">
                                    <i class="bi bi-arrow-right-circle me-1"></i> Open Deal
                                </a>
                                @if($deal->deal_status === 'payment_pending' && $deal->payment_status !== 'paid')
                                    <a href="{{ route('customer.deals.payment', $deal->deal_id_pk) }}" class="btn btn-success btn-sm ms-1">
                                        <i class="bi bi-credit-card me-1"></i> Pay Now
                                    </a>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-5 text-muted">
                                <i class="bi bi-inboxes display-4 opacity-25 d-block mb-3"></i>
                                <h5 class="fw-bold">No active deals right now</h5>
                                <p class="mb-3">Express interest in marketplace items. Once the seller initiates a deal, it will appear here.</p>
                                <a href="{{ route('marketplace.index') }}" class="btn btn-primary btn-sm">Browse Marketplace</a>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($activeDeals->hasPages())
            <div class="card-footer bg-white border-0 py-3 d-flex justify-content-center">
                {{ $activeDeals->links('pagination::bootstrap-5') }}
            </div>
        @endif
    </div>
</div>
@endsection
