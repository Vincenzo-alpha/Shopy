@extends('layouts.seller')

@section('title', 'Wallet & Earnings - Seller Portal')
@section('page_title', 'Seller Wallet & Ledger')

@section('content')
<!-- Simulated Accounting Notice -->
<div class="alert alert-info border-info border-2 shadow-sm d-flex align-items-center mb-4">
    <i class="bi bi-info-circle-fill fs-3 me-3 text-info"></i>
    <div>
        <strong class="d-block">SIMULATED LEDGER ACCOUNTING</strong>
        <span class="small text-muted">This ledger represents sandbox marketplace earnings. Funds move from pending to available balance upon verified deal completion.</span>
    </div>
</div>

<!-- Balance Overview Cards -->
<div class="row g-3 mb-4">
    <div class="col-md-6 col-lg-4">
        <div class="card border-0 shadow-sm p-4 bg-white">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <span class="text-muted small fw-semibold">Available Balance</span>
                <span class="badge bg-success-subtle text-success p-2 rounded-circle">
                    <i class="bi bi-wallet2 fs-5"></i>
                </span>
            </div>
            <div class="display-6 fw-bold text-success mb-1">
                ₹{{ number_format($wallet->available_balance, 2) }}
            </div>
            <small class="text-muted">Cleared earnings from completed deals</small>
        </div>
    </div>

    <div class="col-md-6 col-lg-4">
        <div class="card border-0 shadow-sm p-4 bg-white">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <span class="text-muted small fw-semibold">Pending Balance (Escrow)</span>
                <span class="badge bg-warning-subtle text-warning p-2 rounded-circle">
                    <i class="bi bi-hourglass-split fs-5"></i>
                </span>
            </div>
            <div class="display-6 fw-bold text-warning-emphasis mb-1">
                ₹{{ number_format($wallet->pending_balance, 2) }}
            </div>
            <small class="text-muted">Awaiting completion key verification</small>
        </div>
    </div>

    <div class="col-md-12 col-lg-4">
        <div class="card border-0 shadow-sm p-4 bg-white">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <span class="text-muted small fw-semibold">Total Cumulative Recorded</span>
                <span class="badge bg-primary-subtle text-primary p-2 rounded-circle">
                    <i class="bi bi-graph-up-arrow fs-5"></i>
                </span>
            </div>
            <div class="display-6 fw-bold text-primary mb-1">
                ₹{{ number_format($wallet->available_balance + $wallet->pending_balance, 2) }}
            </div>
            <small class="text-muted">Net seller earnings generated</small>
        </div>
    </div>
</div>

<!-- Transaction History Ledger -->
<div class="card border-0 shadow-sm overflow-hidden">
    <div class="card-header bg-white py-3">
        <h6 class="fw-bold mb-0">Financial Transactions Ledger (sk_wallet_transactions)</h6>
    </div>
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th class="ps-4">Reference No</th>
                    <th>Transaction Type</th>
                    <th>Amount</th>
                    <th>Status</th>
                    <th>Timestamp</th>
                </tr>
            </thead>
            <tbody>
                @forelse($transactions as $txn)
                    <tr>
                        <td class="ps-4 font-monospace small text-primary fw-semibold">
                            {{ $txn->reference_no }}
                        </td>
                        <td>
                            @if($txn->transaction_type === 'credit_earnings')
                                <span class="badge bg-success-subtle text-success border border-success">Deal Completion Credit</span>
                            @else
                                <span class="badge bg-secondary-subtle text-secondary">{{ ucfirst($txn->transaction_type) }}</span>
                            @endif
                        </td>
                        <td class="fw-bold text-success fs-6">
                            +₹{{ number_format($txn->amount, 2) }}
                        </td>
                        <td>
                            <span class="badge bg-success"><i class="bi bi-check2"></i> {{ ucfirst($txn->transaction_status) }}</span>
                        </td>
                        <td class="small text-muted">
                            {{ $txn->created_at->format('M d, Y h:i A') }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center py-5 text-muted">
                            <i class="bi bi-receipt display-4 opacity-25 d-block mb-3"></i>
                            <h6 class="fw-bold">No transactions recorded yet</h6>
                            <p class="small mb-0">When deals are verified with customer completion keys, ledger entries appear here.</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($transactions->hasPages())
        <div class="card-footer bg-white border-0 py-3 d-flex justify-content-center">
            {{ $transactions->links('pagination::bootstrap-5') }}
        </div>
    @endif
</div>
@endsection
