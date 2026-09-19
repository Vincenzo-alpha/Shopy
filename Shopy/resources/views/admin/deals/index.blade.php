@extends('layouts.admin')

@section('title', 'Marketplace Deals - Admin')
@section('page_title', 'Deals Oversight')

@section('content')
<!-- Archive Deals (Pending / In Progress) -->
<div class="card border-0 shadow-sm overflow-hidden mb-4">
    <div class="card-header bg-white py-3">
        <h6 class="fw-bold mb-0 text-warning-emphasis"><i class="bi bi-hourglass-split me-1"></i> Deals in Progress (sk_deal_archive)</h6>
        <small class="text-muted">Deals in active negotiation, awaiting payment, or awaiting collection verification</small>
    </div>
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0 small">
            <thead class="table-light">
                <tr>
                    <th class="ps-4">Deal ID</th>
                    <th>Seller</th>
                    <th>Customer</th>
                    <th>Listing</th>
                    <th>Agreed Amount</th>
                    <th>Status</th>
                    <th>Payment</th>
                    <th>Created At</th>
                </tr>
            </thead>
            <tbody>
                @forelse($archiveDeals as $deal)
                    <tr>
                        <td class="ps-4 font-monospace text-primary fw-semibold">{{ $deal->deal_unique_no }}</td>
                        <td>{{ $deal->seller->seller_name }}</td>
                        <td>{{ $deal->customer->customer_name }}</td>
                        <td>{{ $deal->product->prod_service_name }}</td>
                        <td class="fw-bold">{{ $deal->agreed_amount ? '₹' . number_format($deal->agreed_amount, 2) : 'Negotiating' }}</td>
                        <td><span class="badge bg-warning text-dark">{{ ucfirst($deal->deal_status) }}</span></td>
                        <td>
                            @if($deal->payment_status === 'paid')
                                <span class="badge bg-success">Paid</span>
                            @else
                                <span class="badge bg-secondary">Unpaid</span>
                            @endif
                        </td>
                        <td class="text-muted">{{ $deal->created_at->format('M d, Y H:i') }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="text-center py-4 text-muted">No pending deals in archive.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- Master Deals (Completed) -->
<div class="card border-0 shadow-sm overflow-hidden">
    <div class="card-header bg-white py-3">
        <h6 class="fw-bold mb-0 text-success"><i class="bi bi-check-circle-fill me-1"></i> Completed Deals (sk_deal_master)</h6>
        <small class="text-muted">Deals successfully completed and transferred after completion key verification</small>
    </div>
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0 small">
            <thead class="table-light">
                <tr>
                    <th class="ps-4">Deal ID</th>
                    <th>Seller</th>
                    <th>Customer</th>
                    <th>Listing</th>
                    <th>Agreed Amount</th>
                    <th>Platform Fee</th>
                    <th>Seller Net</th>
                    <th>Completed At</th>
                </tr>
            </thead>
            <tbody>
                @forelse($completedDeals as $deal)
                    <tr>
                        <td class="ps-4 font-monospace text-primary fw-semibold">{{ $deal->deal_unique_no }}</td>
                        <td>{{ $deal->seller->seller_name }}</td>
                        <td>{{ $deal->customer->customer_name }}</td>
                        <td>{{ $deal->product->prod_service_name }}</td>
                        <td class="fw-bold">₹{{ number_format($deal->agreed_amount, 2) }}</td>
                        <td class="text-success fw-bold">₹{{ number_format($deal->platform_fee, 2) }} ({{ $deal->platform_fee_percent }}%)</td>
                        <td class="text-dark">₹{{ number_format($deal->seller_net_amount, 2) }}</td>
                        <td class="text-muted">{{ $deal->completed_at ? $deal->completed_at->format('M d, Y H:i') : 'N/A' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="text-center py-4 text-muted">No completed deals in master records yet.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
