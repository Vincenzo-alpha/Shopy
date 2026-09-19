@extends('layouts.admin')

@section('title', 'Admin Dashboard - Shopy')
@section('page_title', 'Marketplace Overview')

@section('content')
<!-- Metric Cards Grid -->
<div class="row g-3 mb-4">
    <div class="col-md-6 col-xl-3">
        <div class="card border-0 shadow-sm p-3 bg-white">
            <div class="d-flex justify-content-between align-items-center mb-1">
                <span class="text-muted small fw-semibold">Platform Fee Collected</span>
                <span class="badge bg-success-subtle text-success p-2 rounded-circle">
                    <i class="bi bi-cash-stack fs-5"></i>
                </span>
            </div>
            <div class="fs-3 fw-bold text-success mb-1">₹{{ number_format($totalRevenue, 2) }}</div>
            <small class="text-muted">Current Fee: <strong>{{ $activePlatformFee }}%</strong></small>
        </div>
    </div>

    <div class="col-md-6 col-xl-3">
        <div class="card border-0 shadow-sm p-3 bg-white">
            <div class="d-flex justify-content-between align-items-center mb-1">
                <span class="text-muted small fw-semibold">Gross Deal Volume</span>
                <span class="badge bg-primary-subtle text-primary p-2 rounded-circle">
                    <i class="bi bi-graph-up fs-5"></i>
                </span>
            </div>
            <div class="fs-3 fw-bold text-primary mb-1">₹{{ number_format($totalVolume, 2) }}</div>
            <small class="text-muted">{{ $completedDealsCount }} Completed Deals</small>
        </div>
    </div>

    <div class="col-md-6 col-xl-3">
        <div class="card border-0 shadow-sm p-3 bg-white">
            <div class="d-flex justify-content-between align-items-center mb-1">
                <span class="text-muted small fw-semibold">Active Deals (Archive)</span>
                <span class="badge bg-warning-subtle text-warning p-2 rounded-circle">
                    <i class="bi bi-hourglass-split fs-5"></i>
                </span>
            </div>
            <div class="fs-3 fw-bold text-dark mb-1">{{ $activeDealsCount }}</div>
            <small class="text-muted">Negotiating & In Fulfillment</small>
        </div>
    </div>

    <div class="col-md-6 col-xl-3">
        <div class="card border-0 shadow-sm p-3 bg-white">
            <div class="d-flex justify-content-between align-items-center mb-1">
                <span class="text-muted small fw-semibold">Registered Community</span>
                <span class="badge bg-info-subtle text-info p-2 rounded-circle">
                    <i class="bi bi-people fs-5"></i>
                </span>
            </div>
            <div class="fs-3 fw-bold text-dark mb-1">{{ $totalSellers + $totalCustomers }}</div>
            <small class="text-muted">{{ $totalSellers }} Sellers • {{ $totalCustomers }} Customers</small>
        </div>
    </div>
</div>

<!-- Two Column Tables: Recent Archive Deals & Recent Completed Deals -->
<div class="row g-4">
    <!-- Active Deals in Archive -->
    <div class="col-lg-6">
        <div class="card border-0 shadow-sm overflow-hidden h-100">
            <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                <h6 class="fw-bold mb-0"><i class="bi bi-journal-text text-warning me-2"></i> In-Progress Deals (sk_deal_archive)</h6>
                <a href="{{ route('admin.deals.index') }}" class="btn btn-sm btn-outline-primary">View All</a>
            </div>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0 small">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-3">Deal ID</th>
                            <th>Parties</th>
                            <th>Status</th>
                            <th>Payment</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recentArchiveDeals as $deal)
                            <tr>
                                <td class="ps-3 font-monospace text-primary fw-semibold">{{ $deal->deal_unique_no }}</td>
                                <td>
                                    <div>S: {{ $deal->seller->seller_name }}</div>
                                    <div class="text-muted">C: {{ $deal->customer->customer_name }}</div>
                                </td>
                                <td><span class="badge bg-light text-dark border">{{ ucfirst($deal->deal_status) }}</span></td>
                                <td>
                                    @if($deal->payment_status === 'paid')
                                        <span class="badge bg-success">Paid</span>
                                    @else
                                        <span class="badge bg-secondary">Unpaid</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center py-4 text-muted">No deals currently in archive.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Completed Deals in Master -->
    <div class="col-lg-6">
        <div class="card border-0 shadow-sm overflow-hidden h-100">
            <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                <h6 class="fw-bold mb-0"><i class="bi bi-check-all text-success me-2"></i> Completed Deals (sk_deal_master)</h6>
                <a href="{{ route('admin.deals.index') }}" class="btn btn-sm btn-outline-primary">View All</a>
            </div>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0 small">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-3">Deal ID</th>
                            <th>Amount</th>
                            <th>Fee Collected</th>
                            <th>Completed At</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recentCompletedDeals as $deal)
                            <tr>
                                <td class="ps-3 font-monospace text-primary fw-semibold">{{ $deal->deal_unique_no }}</td>
                                <td class="fw-bold">₹{{ number_format($deal->agreed_amount, 2) }}</td>
                                <td class="text-success fw-bold">₹{{ number_format($deal->platform_fee, 2) }}</td>
                                <td class="text-muted">{{ $deal->completed_at ? $deal->completed_at->format('M d, H:i') : 'N/A' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center py-4 text-muted">No completed deals in master table yet.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
