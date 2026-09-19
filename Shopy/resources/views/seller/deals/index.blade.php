@extends('layouts.seller')

@section('title', 'Active Deals - Seller Portal')
@section('page_title', 'Deals & Negotiations')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h5 class="fw-bold mb-1">Deals in Progress (Archive)</h5>
        <small class="text-muted">Negotiate prices, track customer sandbox payments, and verify collection keys</small>
    </div>
    <a href="{{ route('seller.deals.completed') }}" class="btn btn-outline-secondary btn-sm">
        <i class="bi bi-clock-history me-1"></i> Completed Deals (Master)
    </a>
</div>

<div class="card border-0 shadow-sm overflow-hidden">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th class="ps-4">Deal Ref</th>
                    <th>Customer</th>
                    <th>Item / Service</th>
                    <th>Status</th>
                    <th>Latest Offer / Price</th>
                    <th>Payment</th>
                    <th class="text-end pe-4">Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse($deals as $deal)
                    <tr>
                        <td class="ps-4 font-monospace small text-primary fw-semibold">
                            {{ $deal->deal_unique_no }}
                        </td>
                        <td>
                            <div class="fw-bold">{{ $deal->customer->customer_name }}</div>
                            <small class="text-muted"><i class="bi bi-geo-alt"></i> {{ $deal->customer->city }}</small>
                        </td>
                        <td>
                            <div class="fw-bold">{{ $deal->product->prod_service_name }}</div>
                            <span class="badge {{ $deal->product->item_type === 'service' ? 'badge-type-service' : 'badge-type-product' }} small">
                                {{ ucfirst($deal->product->item_type) }}
                            </span>
                        </td>
                        <td>
                            @if($deal->deal_status === 'negotiating')
                                <span class="badge bg-warning-subtle text-warning-emphasis border border-warning px-2 py-1">Negotiating</span>
                            @elseif($deal->deal_status === 'payment_pending')
                                <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-2 py-1">Awaiting Payment</span>
                            @elseif($deal->deal_status === 'in_fulfillment')
                                <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1">Paid • Ready for Key</span>
                            @else
                                <span class="badge bg-light text-dark">{{ ucfirst($deal->deal_status) }}</span>
                            @endif
                        </td>
                        <td>
                            @if($deal->agreed_amount)
                                <div class="fw-bold text-success fs-6">₹{{ number_format($deal->agreed_amount, 2) }}</div>
                                <small class="text-muted">Net: ₹{{ number_format($deal->seller_net_amount, 2) }}</small>
                            @elseif($deal->negotiation)
                                <div>You: <strong>₹{{ number_format($deal->negotiation->seller_negotiation_amt, 2) }}</strong></div>
                                <small class="text-muted">Customer: ₹{{ number_format($deal->negotiation->customer_negotiation_amt, 2) }}</small>
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
                            <a href="{{ route('seller.deals.show', $deal->deal_id_pk) }}" class="btn btn-primary btn-sm">
                                <i class="bi bi-arrow-right-circle me-1"></i> Open Deal
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center py-5 text-muted">
                            <i class="bi bi-inbox display-4 opacity-25 d-block mb-3"></i>
                            <h6 class="fw-bold">No active deals in archive</h6>
                            <p class="small mb-0">Initiate deals from customer interests to begin negotiations.</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($deals->hasPages())
        <div class="card-footer bg-white border-0 py-3 d-flex justify-content-center">
            {{ $deals->links('pagination::bootstrap-5') }}
        </div>
    @endif
</div>
@endsection
