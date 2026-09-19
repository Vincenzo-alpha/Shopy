@extends('layouts.seller')

@section('title', 'Completed Deals - Seller Portal')
@section('page_title', 'Completed Deals (Master Records)')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h5 class="fw-bold mb-1">Completed Deals History</h5>
        <small class="text-muted">Deals successfully completed and transferred from archive to master</small>
    </div>
    <a href="{{ route('seller.deals.index') }}" class="btn btn-outline-primary btn-sm">
        <i class="bi bi-briefcase me-1"></i> Active Deals
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
                    <th>Agreed Price</th>
                    <th>Platform Fee</th>
                    <th>Net Earned</th>
                    <th>Completed At</th>
                    <th class="text-end pe-4">Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse($completedDeals as $deal)
                    <tr>
                        <td class="ps-4 font-monospace small text-primary fw-semibold">
                            {{ $deal->deal_unique_no }}
                        </td>
                        <td>
                            <div class="fw-bold">{{ $deal->customer->customer_name }}</div>
                            <small class="text-muted">{{ $deal->customer->city }}</small>
                        </td>
                        <td>
                            <div class="fw-bold">{{ $deal->product->prod_service_name }}</div>
                            <span class="badge {{ $deal->product->item_type === 'service' ? 'badge-type-service' : 'badge-type-product' }} small">
                                {{ ucfirst($deal->product->item_type) }}
                            </span>
                        </td>
                        <td class="fw-bold text-dark">₹{{ number_format($deal->agreed_amount, 2) }}</td>
                        <td class="text-danger small">-₹{{ number_format($deal->platform_fee, 2) }} ({{ $deal->platform_fee_percent }}%)</td>
                        <td class="fw-bold text-success fs-6">₹{{ number_format($deal->seller_net_amount, 2) }}</td>
                        <td class="small text-muted">{{ $deal->completed_at ? $deal->completed_at->format('M d, Y h:i A') : 'N/A' }}</td>
                        <td class="text-end pe-4">
                            <span class="badge bg-success-subtle text-success border border-success px-3 py-2">
                                <i class="bi bi-check-all me-1"></i> Transferred to Master
                            </span>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="text-center py-5 text-muted">
                            <i class="bi bi-journal-check display-4 opacity-25 d-block mb-3"></i>
                            <h6 class="fw-bold">No completed deals yet</h6>
                            <p class="small mb-0">Once you verify a buyer's completion key, the deal will appear here.</p>
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
@endsection
