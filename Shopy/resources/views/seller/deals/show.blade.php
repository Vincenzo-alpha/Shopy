@extends('layouts.seller')

@section('title', 'Deal #' . $deal->deal_unique_no . ' - Seller Portal')
@section('page_title', 'Deal Management')

@section('content')
<div class="mb-4">
    <a href="{{ route('seller.deals.index') }}" class="text-decoration-none small text-muted">
        <i class="bi bi-arrow-left me-1"></i> Back to Active Deals
    </a>
    <div class="d-flex justify-content-between align-items-center mt-1">
        <div>
            <h4 class="fw-bold mb-0">Deal #{{ $deal->deal_unique_no }}</h4>
            <span class="text-muted small">Initiated on {{ $deal->created_at->format('M d, Y h:i A') }}</span>
        </div>
        <div>
            @if($deal->payment_status === 'paid')
                <span class="badge bg-success px-3 py-2 fs-6"><i class="bi bi-check2-circle me-1"></i> Payment Verified</span>
            @elseif($deal->deal_status === 'payment_pending')
                <span class="badge bg-warning text-dark px-3 py-2 fs-6"><i class="bi bi-hourglass-split me-1"></i> Payment Pending</span>
            @else
                <span class="badge bg-primary px-3 py-2 fs-6"><i class="bi bi-chat-dots me-1"></i> Price Negotiating</span>
            @endif
        </div>
    </div>
</div>

<!-- Deal Lifecycle Progression -->
<div class="card border-0 shadow-sm mb-4 p-3 bg-white">
    <div class="row text-center g-2">
        <div class="col-3">
            <div class="p-2 rounded {{ $deal->deal_status === 'negotiating' ? 'bg-primary text-white fw-bold' : 'bg-light text-muted' }}">
                1. Negotiation
            </div>
        </div>
        <div class="col-3">
            <div class="p-2 rounded {{ $deal->deal_status === 'payment_pending' ? 'bg-primary text-white fw-bold' : ($deal->payment_status === 'paid' ? 'bg-success-subtle text-success fw-semibold' : 'bg-light text-muted') }}">
                2. Customer Payment
            </div>
        </div>
        <div class="col-3">
            <div class="p-2 rounded {{ $deal->deal_status === 'in_fulfillment' ? 'bg-primary text-white fw-bold' : 'bg-light text-muted' }}">
                3. Key Verification
            </div>
        </div>
        <div class="col-3">
            <div class="p-2 rounded bg-light text-muted">
                4. Completed
            </div>
        </div>
    </div>
</div>

<!-- KEY VERIFICATION SECTION (When deal is paid and in fulfillment) -->
@if($deal->deal_status === 'in_fulfillment' && $deal->payment_status === 'paid')
    <div class="card border-primary border-2 shadow-sm mb-4 bg-primary-subtle text-dark">
        <div class="card-body p-4">
            <div class="row align-items-center">
                <div class="col-md-7">
                    <span class="badge bg-primary px-3 py-1 mb-2 fs-6"><i class="bi bi-shield-check me-1"></i> Customer Collection & Key Verification</span>
                    <h5 class="fw-bold mb-1">Verify Completion Key from Customer</h5>
                    <p class="small text-muted mb-0">
                        Ask the customer for their secure 8-character completion key upon collecting the item or completing the service. Entering the correct key marks this deal completed, unlocks <strong>₹{{ number_format($deal->seller_net_amount, 2) }}</strong> into your available balance, and transfers this record to the master database.
                    </p>
                </div>
                <div class="col-md-5 mt-3 mt-md-0">
                    <form action="{{ route('seller.deals.verify', $deal->deal_id_pk) }}" method="POST" class="p-3 bg-white rounded border shadow-sm" onsubmit="return confirm('Confirm deal completion with this key?');">
                        @csrf
                        <label class="form-label fw-bold small">Enter Customer's Completion Key</label>
                        <div class="input-group mb-2">
                            <span class="input-group-text bg-light"><i class="bi bi-key-fill"></i></span>
                            <input type="text" 
                                   name="completion_key" 
                                   class="form-control form-control-lg font-monospace text-uppercase fw-bold" 
                                   placeholder="e.g. SHP-AB12CD" 
                                   required autofocus>
                        </div>
                        <button type="submit" class="btn btn-success w-100 fw-bold">
                            <i class="bi bi-check-circle-fill me-1"></i> Verify Key & Complete Deal
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endif

<div class="row g-4">
    <!-- Left Column: Customer & Listing Details -->
    <div class="col-lg-4">
        <!-- Customer Info Card -->
        <div class="card border-0 shadow-sm p-3 mb-4">
            <h6 class="fw-bold mb-3"><i class="bi bi-person-circle text-primary me-2"></i> Buyer Details</h6>
            <div class="mb-3">
                <div class="fw-bold fs-6">{{ $deal->customer->customer_name }}</div>
                <div class="small text-muted">{{ $deal->customer->email }}</div>
                <div class="small text-muted"><i class="bi bi-telephone text-success me-1"></i> {{ $deal->customer->contact_no }}</div>
            </div>
            
            <div class="p-3 bg-light rounded border small">
                <div class="fw-semibold text-secondary mb-1">Customer City & State:</div>
                <p class="mb-2 text-dark">{{ $deal->customer->city }}, {{ $deal->customer->state }}</p>
                <div class="fw-semibold text-secondary mb-1">Customer Address:</div>
                <p class="mb-0 text-dark">{{ $deal->customer->address }}</p>
            </div>
        </div>

        <!-- Listing Card -->
        <div class="card border-0 shadow-sm p-3 mb-4">
            <h6 class="fw-bold mb-3"><i class="bi bi-box-seam text-primary me-2"></i> Deal Listing</h6>
            <div class="fw-bold">{{ $deal->product->prod_service_name }}</div>
            <span class="badge {{ $deal->product->item_type === 'service' ? 'badge-type-service' : 'badge-type-product' }} mt-1 mb-3 align-self-start">
                {{ ucfirst($deal->product->item_type) }}
            </span>

            <div class="p-2 bg-light rounded small border">
                <div class="d-flex justify-content-between mb-1">
                    <span class="text-muted">Listed Price:</span>
                    <span class="fw-bold">₹{{ number_format($deal->product->listed_price, 2) }}</span>
                </div>
                <div class="d-flex justify-content-between mb-1">
                    <span class="text-muted">Min Acceptable:</span>
                    <span>₹{{ number_format($deal->product->minimum_rate, 2) }}</span>
                </div>
                <div class="d-flex justify-content-between">
                    <span class="text-muted">Max Starting:</span>
                    <span>₹{{ number_format($deal->product->maximum_rate, 2) }}</span>
                </div>
            </div>
        </div>

        <!-- Platform Fee & Earnings Breakdown -->
        <div class="card border-0 shadow-sm p-3">
            <h6 class="fw-bold mb-2"><i class="bi bi-cash-stack text-success me-2"></i> Financial Breakdown</h6>
            <small class="text-muted mb-3 d-block">Platform fee is paid by seller from the deal amount</small>

            @if($deal->agreed_amount)
                <div class="p-3 bg-light rounded border small">
                    <div class="d-flex justify-content-between mb-2">
                        <span>Agreed Deal Amount:</span>
                        <strong class="text-dark">₹{{ number_format($deal->agreed_amount, 2) }}</strong>
                    </div>
                    <div class="d-flex justify-content-between mb-2 text-danger">
                        <span>Platform Fee ({{ $deal->platform_fee_percent }}%):</span>
                        <strong>-₹{{ number_format($deal->platform_fee, 2) }}</strong>
                    </div>
                    <div class="d-flex justify-content-between pt-2 border-top fs-6 text-success fw-bold">
                        <span>Your Net Earnings:</span>
                        <span>₹{{ number_format($deal->seller_net_amount, 2) }}</span>
                    </div>
                </div>
            @else
                <div class="p-3 bg-light rounded border small text-muted">
                    <i class="bi bi-info-circle me-1"></i> Fee and net earnings will be frozen once an offer is accepted.
                </div>
            @endif
        </div>
    </div>

    <!-- Right Column: Negotiation Actions / Status -->
    <div class="col-lg-8">
        @if($deal->deal_status === 'negotiating')
            <div class="card border-0 shadow-sm p-4 mb-4">
                <div class="d-flex justify-content-between align-items-center border-bottom pb-3 mb-3">
                    <h5 class="fw-bold mb-0">Negotiate Price</h5>
                    <span class="badge bg-warning text-dark px-3 py-2">
                        <i class="bi bi-arrow-repeat me-1"></i> In Progress
                    </span>
                </div>

                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <div class="p-3 rounded border negotiation-bubble-seller">
                            <small class="text-muted fw-bold d-block">Your Current Offer</small>
                            <div class="display-6 fw-bold text-primary">
                                ₹{{ number_format($deal->negotiation->seller_negotiation_amt, 2) }}
                            </div>
                            <div class="small text-muted mt-1">
                                @if($deal->negotiation->current_offer_by === 'seller')
                                    <span class="badge bg-primary">Waiting for buyer response</span>
                                @else
                                    <span>Your last proposed price</span>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="p-3 rounded border negotiation-bubble-customer">
                            <small class="text-muted fw-bold d-block">Buyer's Counteroffer</small>
                            <div class="display-6 fw-bold text-success">
                                ₹{{ number_format($deal->negotiation->customer_negotiation_amt, 2) }}
                            </div>
                            <div class="small text-muted mt-1">
                                @if($deal->negotiation->current_offer_by === 'customer')
                                    <span class="badge bg-success">Action needed: Counter or Accept</span>
                                @else
                                    <span>Buyer proposal</span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Seller Actions -->
                <div class="row g-3">
                    <!-- Counteroffer form -->
                    <div class="col-md-7">
                        <div class="card p-3 bg-light border-0">
                            <h6 class="fw-bold mb-2">Send a Counteroffer</h6>
                            <form action="{{ route('seller.deals.counter', $deal->deal_id_pk) }}" method="POST">
                                @csrf
                                <div class="mb-2">
                                    <div class="input-group">
                                        <span class="input-group-text">₹</span>
                                        <input type="number" 
                                               name="offer_amount" 
                                               class="form-control form-control-lg fw-bold" 
                                               step="1" 
                                               min="{{ $deal->product->minimum_rate }}" 
                                               max="{{ $deal->product->maximum_rate }}" 
                                               value="{{ old('offer_amount', $deal->negotiation->seller_negotiation_amt) }}" 
                                               required>
                                    </div>
                                    <div class="form-text small">
                                        Allowed bounds: ₹{{ number_format($deal->product->minimum_rate, 2) }} – ₹{{ number_format($deal->product->maximum_rate, 2) }}
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <input type="text" name="notes" class="form-control form-control-sm" placeholder="Optional note for buyer (e.g. Ready for collection this evening)">
                                </div>
                                <button type="submit" class="btn btn-outline-primary w-100 fw-bold">
                                    <i class="bi bi-send me-1"></i> Send Counteroffer to Buyer
                                </button>
                            </form>
                        </div>
                    </div>

                    <!-- Accept Buyer Counteroffer -->
                    <div class="col-md-5">
                        <div class="card p-3 bg-light border-0 h-100 d-flex flex-column justify-content-between">
                            <div>
                                <h6 class="fw-bold mb-1">Accept Buyer's Price</h6>
                                <p class="small text-muted mb-3">Accept the buyer's proposal of <strong>₹{{ number_format($deal->negotiation->customer_negotiation_amt, 2) }}</strong>.</p>
                                
                                @if($feePreview)
                                    <div class="p-2 bg-white rounded border small mb-3">
                                        <div class="d-flex justify-content-between">
                                            <span>Agreed Price:</span>
                                            <strong>₹{{ number_format($feePreview['agreed_amount'], 2) }}</strong>
                                        </div>
                                        <div class="d-flex justify-content-between text-danger" style="font-size: 0.75rem;">
                                            <span>Platform fee ({{ $feePreview['fee_percent'] }}%):</span>
                                            <span>-₹{{ number_format($feePreview['platform_fee'], 2) }}</span>
                                        </div>
                                        <div class="d-flex justify-content-between text-success fw-bold pt-1 border-top mt-1">
                                            <span>Net Earnings:</span>
                                            <span>₹{{ number_format($feePreview['seller_net_amount'], 2) }}</span>
                                        </div>
                                    </div>
                                @endif
                            </div>

                            <form action="{{ route('seller.deals.accept', $deal->deal_id_pk) }}" method="POST" onsubmit="return confirm('Accept this offer of ₹{{ number_format($deal->negotiation->customer_negotiation_amt, 2) }}?');">
                                @csrf
                                <button type="submit" class="btn btn-success w-100 py-2 fw-bold shadow-sm">
                                    <i class="bi bi-check2-circle me-1"></i> Accept Offer (₹{{ number_format($deal->negotiation->customer_negotiation_amt, 2) }})
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        @elseif($deal->deal_status === 'payment_pending')
            <div class="card border-0 shadow-sm p-4 mb-4 text-center">
                <div class="rounded-circle bg-warning-subtle text-warning-emphasis d-inline-flex align-items-center justify-content-center p-3 mb-3 mx-auto" style="width: 64px; height: 64px;">
                    <i class="bi bi-hourglass-split fs-2"></i>
                </div>
                <h5 class="fw-bold">Agreed Amount: ₹{{ number_format($deal->agreed_amount, 2) }}</h5>
                <p class="text-muted small">Offer accepted! Waiting for customer to complete sandbox payment. Once paid, the customer will receive a completion key.</p>
            </div>
        @else
            <div class="card border-0 shadow-sm p-4 mb-4">
                <h5 class="fw-bold mb-3"><i class="bi bi-check2-circle text-success me-2"></i> Payment Confirmed</h5>
                <div class="p-3 bg-light rounded border mb-3">
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Paid Deal Amount:</span>
                        <span class="fw-bold text-dark">₹{{ number_format($deal->agreed_amount, 2) }}</span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Your Net Payout (in Pending Escrow):</span>
                        <strong class="text-success fs-5">₹{{ number_format($deal->seller_net_amount, 2) }}</strong>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span class="text-muted">Fulfillment:</span>
                        <span class="badge bg-primary">{{ ucfirst(str_replace('_', ' ', $deal->fulfillment_method)) }}</span>
                    </div>
                </div>
                <div class="small text-muted">
                    <i class="bi bi-info-circle me-1"></i> Customer has been given a secret completion key. Verify the key above once customer collects the product or receives service.
                </div>
            </div>
        @endif

        <!-- Timeline Log -->
        <div class="card border-0 shadow-sm p-4">
            <h6 class="fw-bold mb-3"><i class="bi bi-clock-history me-2 text-primary"></i> Offer & Negotiation Audit Trail</h6>
            <div class="timeline">
                @forelse($deal->negotiation->history ?? [] as $log)
                    <div class="p-3 mb-2 rounded border {{ $log->offered_by === 'seller' ? 'bg-light' : 'bg-white' }}">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <span class="badge {{ $log->offered_by === 'seller' ? 'bg-primary' : 'bg-success' }}">
                                {{ $log->offered_by === 'seller' ? 'Your Proposal' : 'Buyer Proposal' }}
                            </span>
                            <small class="text-muted">{{ $log->created_at->format('M d, h:i A') }}</small>
                        </div>
                        <div class="fw-bold fs-5 text-dark">
                            ₹{{ number_format($log->amount, 2) }}
                        </div>
                        @if($log->notes)
                            <div class="small text-muted mt-1">{{ $log->notes }}</div>
                        @endif
                    </div>
                @empty
                    <p class="text-muted small mb-0">No negotiation entries.</p>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection
