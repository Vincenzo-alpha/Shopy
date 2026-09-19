@extends('layouts.app')

@section('title', 'Completed Deals History - Shopy')

@section('content')
<div class="container py-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="fw-bold mb-1">Completed Deals History</h3>
            <p class="text-muted small mb-0">Deals successfully verified and transferred to master records</p>
        </div>
        <a href="{{ route('customer.deals.index') }}" class="btn btn-outline-primary btn-sm">
            <i class="bi bi-briefcase me-1"></i> Active Deals
        </a>
    </div>

    <div class="card border-0 shadow-sm overflow-hidden">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-4">Deal Ref</th>
                        <th>Product / Service</th>
                        <th>Seller</th>
                        <th>Agreed Amount</th>
                        <th>Fulfillment</th>
                        <th>Completed Date</th>
                        <th class="text-end pe-4">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($completedDeals as $deal)
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
                                <small class="text-muted">{{ $deal->seller->city }}</small>
                            </td>
                            <td class="fw-bold text-success fs-6">
                                ₹{{ number_format($deal->agreed_amount, 2) }}
                            </td>
                            <td class="small text-muted">
                                {{ ucfirst(str_replace('_', ' ', $deal->fulfillment_method)) }}
                            </td>
                            <td class="small text-muted">
                                {{ $deal->completed_at ? $deal->completed_at->format('M d, Y h:i A') : 'N/A' }}
                            </td>
                            <td class="text-end pe-4">
                                <span class="badge bg-success-subtle text-success border border-success px-3 py-2">
                                    <i class="bi bi-check-all me-1"></i> Verified & Closed
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-5 text-muted">
                                <i class="bi bi-journal-check display-4 opacity-25 d-block mb-3"></i>
                                <h5 class="fw-bold">No completed deals yet</h5>
                                <p class="mb-3">When deals are collected and keys verified by sellers, they appear here.</p>
                                <a href="{{ route('customer.deals.index') }}" class="btn btn-primary btn-sm">View Active Deals</a>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($completedDeals->hasPages())
            <div class="card-footer bg-white border-0 py-3 d-flex justify-content-center">
                {{ $completedDeals->links('pagination::bootstrap-5') }}
            </div>
        @endif
    </div>
</div>
@endsection
