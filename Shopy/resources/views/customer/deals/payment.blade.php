@extends('layouts.app')

@section('title', 'Sandbox Checkout - Deal #' . $deal->deal_unique_no)

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-7">
            <!-- Sandbox Test Environment Banner -->
            <div class="alert alert-warning border-warning border-2 shadow-sm d-flex align-items-center mb-4">
                <i class="bi bi-cone-striped fs-2 me-3 text-warning-emphasis"></i>
                <div>
                    <strong class="d-block text-warning-emphasis">SIMULATED SANDBOX PAYMENT GATEWAY</strong>
                    <span class="small text-muted">This is a development testing environment. No real currency is transferred or charged. Choose your simulated outcome below.</span>
                </div>
            </div>

            <div class="card border-0 shadow-sm overflow-hidden mb-4">
                <div class="card-header bg-dark text-white p-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <span class="badge bg-primary px-2 py-1 mb-1">Shopy Checkout</span>
                            <h4 class="fw-bold mb-0">Complete Payment for Deal</h4>
                        </div>
                        <div class="text-end">
                            <span class="text-white-50 small d-block">Deal Reference</span>
                            <span class="font-monospace fw-bold">{{ $deal->deal_unique_no }}</span>
                        </div>
                    </div>
                </div>

                <div class="card-body p-4">
                    <!-- Itemized Summary -->
                    <h6 class="fw-bold mb-3">Order Summary</h6>
                    <div class="p-3 bg-light rounded border mb-4">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <div>
                                <span class="fw-bold">{{ $deal->product->prod_service_name }}</span>
                                <div class="small text-muted">Seller: {{ $deal->seller->seller_name }} ({{ $deal->seller->city }})</div>
                            </div>
                            <span class="fs-5 fw-bold text-dark">₹{{ number_format($deal->agreed_amount, 2) }}</span>
                        </div>
                        <div class="d-flex justify-content-between small text-muted pt-2 border-top">
                            <span>Fulfillment Method:</span>
                            <span class="fw-semibold">{{ ucfirst(str_replace('_', ' ', $deal->fulfillment_method)) }}</span>
                        </div>
                        <div class="d-flex justify-content-between small text-muted pt-1">
                            <span>Delivery/Courier Charges:</span>
                            <span class="text-success fw-bold">₹0.00 (Self Collection / Local Service)</span>
                        </div>
                        <div class="d-flex justify-content-between fs-5 fw-bold text-primary pt-3 border-top mt-2">
                            <span>Total Payable Amount:</span>
                            <span>₹{{ number_format($deal->agreed_amount, 2) }}</span>
                        </div>
                    </div>

                    <!-- Payment Form -->
                    <form action="{{ route('customer.deals.payment.process', $deal->deal_id_pk) }}" method="POST">
                        @csrf
                        <h6 class="fw-bold mb-3">Select Simulated Payment Method</h6>
                        
                        <div class="row g-2 mb-4">
                            <div class="col-md-4">
                                <label class="card p-3 border text-center h-100 cursor-pointer">
                                    <input type="radio" name="payment_method" value="sandbox_card" checked class="form-check-input mb-2 mx-auto">
                                    <i class="bi bi-credit-card-2-front fs-3 text-primary mb-1"></i>
                                    <span class="fw-semibold small d-block">Test Card</span>
                                    <small class="text-muted" style="font-size: 0.7rem;">Visa / Master</small>
                                </label>
                            </div>
                            <div class="col-md-4">
                                <label class="card p-3 border text-center h-100 cursor-pointer">
                                    <input type="radio" name="payment_method" value="sandbox_upi" class="form-check-input mb-2 mx-auto">
                                    <i class="bi bi-qr-code-scan fs-3 text-success mb-1"></i>
                                    <span class="fw-semibold small d-block">Test UPI</span>
                                    <small class="text-muted" style="font-size: 0.7rem;">GPay / PhonePe</small>
                                </label>
                            </div>
                            <div class="col-md-4">
                                <label class="card p-3 border text-center h-100 cursor-pointer">
                                    <input type="radio" name="payment_method" value="sandbox_netbanking" class="form-check-input mb-2 mx-auto">
                                    <i class="bi bi-bank fs-3 text-secondary mb-1"></i>
                                    <span class="fw-semibold small d-block">Test NetBanking</span>
                                    <small class="text-muted" style="font-size: 0.7rem;">Simulated Bank</small>
                                </label>
                            </div>
                        </div>

                        <h6 class="fw-bold mb-3">Simulate Gateway Response</h6>
                        <div class="mb-4">
                            <div class="form-check p-3 bg-light rounded border mb-2">
                                <input class="form-check-input ms-0 me-2" type="radio" name="payment_outcome" id="outcomeSuccess" value="success" checked>
                                <label class="form-check-label fw-bold text-success" for="outcomeSuccess">
                                    <i class="bi bi-check-circle-fill me-1"></i> Simulate SUCCESSFUL Payment (Recommended)
                                </label>
                                <div class="small text-muted ps-4">
                                    Transitions deal to paid, moves seller earnings into pending escrow, and generates your completion key.
                                </div>
                            </div>

                            <div class="form-check p-3 bg-light rounded border">
                                <input class="form-check-input ms-0 me-2" type="radio" name="payment_outcome" id="outcomeFailed" value="failed">
                                <label class="form-check-label fw-bold text-danger" for="outcomeFailed">
                                    <i class="bi bi-x-circle-fill me-1"></i> Simulate FAILED Payment
                                </label>
                                <div class="small text-muted ps-4">
                                    Simulates insufficient funds or gateway timeout, keeping the deal unpaid for retry.
                                </div>
                            </div>
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary btn-lg py-3 fw-bold">
                                <i class="bi bi-lock-fill me-1"></i> Authorize Simulated Payment (₹{{ number_format($deal->agreed_amount, 2) }})
                            </button>
                            <a href="{{ route('customer.deals.show', $deal->deal_id_pk) }}" class="btn btn-outline-secondary btn-sm text-center">
                                Cancel and return to deal
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
