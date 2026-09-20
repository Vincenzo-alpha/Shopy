@extends('layouts.app')

@section('title', 'Deal #' . $deal->deal_unique_no . ' - Shopy')

@section('content')
<div class="container py-4">
    <!-- Header Breadcrumbs -->
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-2">
        <div>
            <a href="{{ route('customer.deals.index') }}" class="text-decoration-none small text-muted">
                <i class="bi bi-arrow-left me-1"></i> Back to Active Deals
            </a>
            <h3 class="fw-bold mb-0 mt-1">Deal #{{ $deal->deal_unique_no }}</h3>
            <span class="text-muted small">Created on {{ $deal->created_at->format('M d, Y h:i A') }}</span>
        </div>
        <div class="d-flex gap-2">
            @if($deal->deal_status === 'payment_pending')
                <a href="{{ route('customer.deals.payment', $deal->deal_id_pk) }}" class="btn btn-success">
                    <i class="bi bi-credit-card me-1"></i> Complete Sandbox Payment
                </a>
            @endif
        </div>
    </div>

    <!-- Status Progression Banner -->
    @if($deal->deal_status === 'cancelled')
        <div class="card border-0 shadow-sm mb-4 p-3 bg-danger-subtle text-danger border-start border-danger border-4">
            <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
                <div class="d-flex align-items-center gap-2">
                    <i class="bi bi-x-circle-fill fs-4"></i>
                    <div>
                        <strong class="fs-6 text-danger">Offer Denied & Deal Terminated</strong>
                        <div class="small text-muted">The negotiation for this deal has ended because the offer was denied.</div>
                    </div>
                </div>
                <span class="badge bg-danger fs-6 px-3 py-2">Cancelled</span>
            </div>
        </div>
    @else
        <div class="card border-0 shadow-sm mb-4 p-3 bg-white">
            <div class="row text-center g-2">
                <div class="col-3">
                    <div class="p-2 rounded {{ $deal->deal_status === 'negotiating' ? 'bg-primary text-white fw-bold' : 'bg-light text-muted' }}">
                        <i class="bi bi-chat-left-text me-1"></i> 1. Negotiation
                    </div>
                </div>
                <div class="col-3">
                    <div class="p-2 rounded {{ $deal->deal_status === 'payment_pending' ? 'bg-primary text-white fw-bold' : ($deal->payment_status === 'paid' ? 'bg-success-subtle text-success fw-semibold' : 'bg-light text-muted') }}">
                        <i class="bi bi-credit-card me-1"></i> 2. Payment
                    </div>
                </div>
                <div class="col-3">
                    <div class="p-2 rounded {{ $deal->deal_status === 'in_fulfillment' ? 'bg-primary text-white fw-bold' : 'bg-light text-muted' }}">
                        <i class="bi bi-key me-1"></i> 3. Key & Collection
                    </div>
                </div>
                <div class="col-3">
                    <div class="p-2 rounded bg-light text-muted">
                        <i class="bi bi-check2-circle me-1"></i> 4. Completed
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- SECURE COMPLETION KEY BANNER (Always visible to customer during fulfillment) -->
    @if($deal->deal_status === 'in_fulfillment' && $deal->completion)
        <div class="card border-success border-2 shadow-sm mb-4 bg-success-subtle text-dark">
            <div class="card-body p-4 text-center">
                <span class="badge bg-success px-3 py-2 fs-6 mb-2">
                    <i class="bi bi-shield-lock-fill me-1"></i> Your Secure Deal Completion Key
                </span>
                <p class="mb-2 text-dark">
                    Your payment has been simulated successfully! When meeting the seller in <strong>{{ $deal->seller->city }}</strong>, provide them this completion key <strong>ONLY after collecting the item or receiving the service</strong>:
                </p>
                <div class="d-flex align-items-center justify-content-center gap-2 my-3">
                    <div class="display-5 fw-extrabold text-success font-monospace px-4 py-2 bg-white border border-success rounded-3 letter-spacing-2 shadow-sm" id="dealCompletionKeyCode">
                        {{ $deal->completion->completion_key ?? session('completion_key_revealed') }}
                    </div>
                    <button type="button" class="btn btn-outline-success btn-lg px-3 py-2 shadow-sm" onclick="copyCompletionKey()" title="Copy Key">
                        <i class="bi bi-clipboard" id="copyKeyIcon"></i>
                    </button>
                </div>
                <div class="small text-muted d-flex align-items-center justify-content-center gap-2 flex-wrap">
                    <span>Key Status: <strong class="badge bg-warning text-dark">{{ ucfirst($deal->completion->completion_status) }}</strong></span>
                    <span>&bull;</span>
                    <span class="text-secondary">Keep this code confidential until handover is complete.</span>
                </div>
            </div>
        </div>
    @endif

    <div class="row g-4">
        <!-- Left: Product & Seller Details -->
        <div class="col-lg-4">
            <!-- Product Summary Card -->
            <div class="card border-0 shadow-sm p-3 mb-4">
                <h6 class="fw-bold mb-3"><i class="bi bi-box-seam text-primary me-2"></i> Item Details</h6>
                <div class="d-flex align-items-center gap-3 mb-3">
                    @if($deal->product->image_path)
                        <img src="{{ asset('storage/' . $deal->product->image_path) }}" alt="" class="rounded" style="width: 70px; height: 70px; object-fit: cover;">
                    @else
                        <div class="rounded bg-light p-3 text-center text-muted" style="width: 70px; height: 70px;">
                            <i class="bi bi-box fs-3"></i>
                        </div>
                    @endif
                    <div>
                        <h6 class="fw-bold mb-1">{{ $deal->product->prod_service_name }}</h6>
                        <span class="badge {{ $deal->product->item_type === 'service' ? 'badge-type-service' : 'badge-type-product' }} small">
                            {{ ucfirst($deal->product->item_type) }}
                        </span>
                    </div>
                </div>

                <div class="p-2 bg-light rounded small border mb-2">
                    <div class="d-flex justify-content-between">
                        <span class="text-muted">Listed Price:</span>
                        <span class="fw-bold">₹{{ number_format($deal->product->listed_price, 2) }}</span>
                    </div>
                    <div class="d-flex justify-content-between mt-1">
                        <span class="text-muted">Min Acceptable:</span>
                        <span>₹{{ number_format($deal->product->minimum_rate, 2) }}</span>
                    </div>
                    <div class="d-flex justify-content-between mt-1">
                        <span class="text-muted">Max Starting:</span>
                        <span>₹{{ number_format($deal->product->maximum_rate, 2) }}</span>
                    </div>
                </div>
            </div>

            <!-- Seller Collection Address Card -->
            <div class="card border-0 shadow-sm p-3">
                <h6 class="fw-bold mb-3"><i class="bi bi-geo-alt text-danger me-2"></i> Collection & Seller Details</h6>
                <div class="mb-3">
                    <div class="fw-bold">{{ $deal->seller->seller_name }}</div>
                    <div class="text-muted small"><i class="bi bi-geo"></i> {{ $deal->seller->city }}, {{ $deal->seller->state }}</div>
                </div>

                <!-- Contact & Address disclosed based on deal active/negotiating/paid -->
                <div class="p-3 bg-light rounded border small">
                    <div class="fw-semibold text-secondary mb-1">Fulfillment Address:</div>
                    <p class="mb-2 text-dark fw-medium">{{ $deal->seller->address }}, {{ $deal->seller->city }}</p>
                    <div class="fw-semibold text-secondary mb-1">Seller Contact:</div>
                    <p class="mb-0 text-dark fw-medium">{{ $deal->seller->contact_no }}</p>
                </div>

                <div class="mt-3 small text-muted">
                    <i class="bi bi-info-circle me-1"></i>
                    <strong>Fulfillment Method:</strong> {{ ucfirst(str_replace('_', ' ', $deal->fulfillment_method)) }}
                </div>
            </div>
        </div>

        <!-- Right: Negotiation Console / Payment Details -->
        <div class="col-lg-8">
            <!-- Active Negotiation Console -->
            @if($deal->deal_status === 'negotiating')
                <div class="card border-0 shadow-sm p-4 mb-4">
                    <div class="d-flex justify-content-between align-items-center border-bottom pb-3 mb-3">
                        <h5 class="fw-bold mb-0">Live Price Negotiation</h5>
                        <span class="badge bg-warning text-dark px-3 py-2">
                            <i class="bi bi-arrow-repeat me-1"></i> In Progress
                        </span>
                    </div>

                    <!-- Current Price Comparison Grid -->
                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <div class="p-3 rounded border negotiation-bubble-seller">
                                <small class="text-muted fw-bold d-block">Seller's Current Offer</small>
                                <div class="display-6 fw-bold text-primary">
                                    ₹{{ number_format($deal->negotiation->seller_negotiation_amt, 2) }}
                                </div>
                                <div class="small text-muted mt-1">
                                    @if($deal->negotiation->current_offer_by === 'seller')
                                        <span class="badge bg-primary">Awaiting your response</span>
                                    @else
                                        <span>Seller offered this amount</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="p-3 rounded border negotiation-bubble-customer">
                                <small class="text-muted fw-bold d-block">Your Counteroffer</small>
                                <div class="display-6 fw-bold text-success">
                                    ₹{{ number_format($deal->negotiation->customer_negotiation_amt, 2) }}
                                </div>
                                <div class="small text-muted mt-1">
                                    @if($deal->negotiation->current_offer_by === 'customer')
                                        <span class="badge bg-success">Awaiting seller response</span>
                                    @else
                                        <span>Your latest proposal</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Action: Counteroffer Form or Accept -->
                    <div class="row g-3">
                        <!-- Submit Counteroffer Form -->
                        <div class="col-md-7">
                            <div class="card p-3 bg-light border-0">
                                <h6 class="fw-bold mb-2">Submit a Counteroffer</h6>
                                <form action="{{ route('customer.deals.counter', $deal->deal_id_pk) }}" method="POST">
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
                                                   value="{{ old('offer_amount', $deal->negotiation->customer_negotiation_amt) }}" 
                                                   required>
                                        </div>
                                        <div class="form-text small">
                                            Permitted range: ₹{{ number_format($deal->product->minimum_rate, 2) }} – ₹{{ number_format($deal->product->maximum_rate, 2) }}
                                        </div>
                                    </div>
                                    <div class="mb-3">
                                        <input type="text" name="notes" class="form-control form-control-sm" placeholder="Optional note for seller (e.g. Can collect today afternoon)">
                                    </div>
                                    <button type="submit" class="btn btn-outline-primary w-100 fw-bold">
                                        <i class="bi bi-send me-1"></i> Send Counteroffer
                                    </button>
                                </form>
                            </div>
                        </div>

                        <!-- Accept or Deny Seller Offer Action -->
                        <div class="col-md-5">
                            <div class="card p-3 bg-light border-0 h-100 d-flex flex-column justify-content-between">
                                <div>
                                    <h6 class="fw-bold mb-1">Deal Decision</h6>
                                    <p class="small text-muted mb-3">Accept the seller's current price of <strong>₹{{ number_format($deal->negotiation->seller_negotiation_amt, 2) }}</strong> to proceed to payment, or deny this offer.</p>
                                    
                                    @if($feePreview && $deal->deal_status === 'negotiating')
                                        <div class="p-2 bg-white rounded border small mb-3">
                                            <div class="d-flex justify-content-between">
                                                <span>You Pay:</span>
                                                <strong class="text-primary">₹{{ number_format($feePreview['agreed_amount'], 2) }}</strong>
                                            </div>
                                            {{-- Platform fee is an internal seller detail; not shown to customer --}}
                                        </div>
                                    @endif
                                </div>

                                <div class="d-flex flex-column gap-2">
                                    <form id="acceptOfferForm" action="{{ route('customer.deals.accept', $deal->deal_id_pk) }}" method="POST">
                                        @csrf
                                        <button type="button"
                                                class="btn btn-success w-100 py-2 fw-bold shadow-sm"
                                                onclick="confirmAcceptOffer(event, 'acceptOfferForm', '{{ number_format($deal->negotiation->seller_negotiation_amt, 2) }}')">
                                            <i class="bi bi-check2-circle me-1"></i> Accept Offer (₹{{ number_format($deal->negotiation->seller_negotiation_amt, 2) }})
                                        </button>
                                    </form>

                                    <button type="button" class="btn btn-outline-danger w-100 py-2 fw-bold" data-bs-toggle="collapse" data-bs-target="#denyOfferCollapse" aria-expanded="false">
                                        <i class="bi bi-x-circle me-1"></i> Deny Offer
                                    </button>

                                    <div class="collapse mt-2" id="denyOfferCollapse">
                                        <div class="p-3 bg-white rounded border border-danger-subtle">
                                            <form id="denyOfferForm" action="{{ route('customer.deals.deny', $deal->deal_id_pk) }}" method="POST">
                                                @csrf
                                                <label class="form-label small fw-semibold text-danger">Deny Offer Confirmation</label>
                                                <input type="text" name="reason" class="form-control form-control-sm mb-2" placeholder="Optional reason (e.g. Price too high)">
                                                <button type="button" class="btn btn-danger btn-sm w-100 fw-bold" onclick="confirmDenyOffer(event, 'denyOfferForm')">
                                                    <i class="bi bi-x-circle me-1"></i> Confirm Deny Offer
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @elseif($deal->deal_status === 'payment_pending')
                <!-- Payment Pending State -->
                <div class="card border-0 shadow-sm p-4 mb-4 text-center">
                    <div class="rounded-circle bg-warning-subtle text-warning-emphasis d-inline-flex align-items-center justify-content-center p-3 mb-3 mx-auto" style="width: 64px; height: 64px;">
                        <i class="bi bi-credit-card fs-2"></i>
                    </div>
                    <h4 class="fw-bold">Agreed Amount Frozen: ₹{{ number_format($deal->agreed_amount, 2) }}</h4>
                    <p class="text-muted">Both parties have accepted this price. Please complete the simulated payment to receive your completion key.</p>
                    <div class="d-flex justify-content-center gap-2 mt-2">
                        <a href="{{ route('customer.deals.payment', $deal->deal_id_pk) }}" class="btn btn-success btn-lg px-4 fw-bold shadow-sm">
                            <i class="bi bi-arrow-right-circle me-1"></i> Proceed to Sandbox Checkout
                        </a>
                    </div>
                </div>
            @elseif($deal->deal_status === 'cancelled')
                <!-- Denied / Cancelled State -->
                <div class="card border-0 shadow-sm p-4 mb-4 text-center">
                    <div class="rounded-circle bg-danger-subtle text-danger d-inline-flex align-items-center justify-content-center p-3 mb-3 mx-auto" style="width: 64px; height: 64px;">
                        <i class="bi bi-x-circle fs-2"></i>
                    </div>
                    <h4 class="fw-bold text-danger">Offer Denied & Negotiation Closed</h4>
                    <p class="text-muted">This deal negotiation has been terminated. No further offers or payments can be made.</p>
                    <div class="d-flex justify-content-center gap-2 mt-2">
                        <a href="{{ route('marketplace.index') }}" class="btn btn-primary px-4 fw-bold shadow-sm">
                            <i class="bi bi-shop me-1"></i> Explore Marketplace
                        </a>
                        <a href="{{ route('customer.deals.index') }}" class="btn btn-outline-secondary px-4 fw-bold">
                            <i class="bi bi-arrow-left me-1"></i> My Active Deals
                        </a>
                    </div>
                </div>
            @else
                <!-- Paid & In Fulfillment State -->
                <div class="card border-0 shadow-sm p-4 mb-4">
                    <h5 class="fw-bold mb-3"><i class="bi bi-check2-circle text-success me-2"></i> Deal Paid & Active</h5>
                    <div class="p-3 bg-light rounded border mb-3">
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted">Agreed Price Paid:</span>
                            <strong class="fs-5 text-success">₹{{ number_format($deal->agreed_amount, 2) }}</strong>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted">Payment Status:</span>
                            <span class="badge bg-success">SIMULATED SUCCESS</span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted">Payment Reference:</span>
                            <span class="font-monospace small">{{ $deal->payments->last()->payment_reference ?? 'N/A' }}</span>
                        </div>
                        @if($deal->completion && ($deal->completion->completion_key || session('completion_key_revealed')))
                        <div class="d-flex justify-content-between align-items-center pt-2 border-top">
                            <span class="text-muted fw-semibold">Completion Key:</span>
                            <span class="badge bg-success-subtle text-success fs-6 font-monospace px-3 py-2 border border-success">
                                {{ $deal->completion->completion_key ?? session('completion_key_revealed') }}
                            </span>
                        </div>
                        @endif
                    </div>
                    <p class="small text-muted mb-0">
                        Please proceed to collect the item from the seller or receive your service. Give the seller your completion key to complete the transaction.
                    </p>
                </div>
            @endif

            <!-- Negotiation History Log -->
            <div class="card border-0 shadow-sm p-4">
                <h6 class="fw-bold mb-3"><i class="bi bi-clock-history me-2 text-primary"></i> Negotiation & Offer History</h6>
                <div class="timeline">
                    @forelse($deal->negotiation->history ?? [] as $log)
                        <div class="p-3 mb-2 rounded border {{ $log->offered_by === 'customer' ? 'bg-light' : 'bg-white' }}">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <span class="badge {{ $log->offered_by === 'customer' ? 'bg-success' : 'bg-primary' }}">
                                    {{ $log->offered_by === 'customer' ? 'Your Proposal' : 'Seller Proposal' }}
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
                        <p class="text-muted small mb-0">No offers recorded yet.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
var validationRules = {
    offer_amount: {
        required: true,
        pattern: /^[0-9]+(\.?[0-9]{0,2})?$/,
        min: {{ $deal->product->minimum_rate }},
        max: {{ $deal->product->maximum_rate }},
        message: 'Please enter a valid offer amount between ₹{{ number_format($deal->product->minimum_rate, 2) }} and ₹{{ number_format($deal->product->maximum_rate, 2) }}.',
    },
    notes: {
        required: false,
        maxLength: 255,
    },
};
setupFormValidation('form[action*="counter"]');

function copyCompletionKey() {
    const el = document.getElementById('dealCompletionKeyCode');
    if (!el) return;
    const text = el.innerText.trim();
    navigator.clipboard.writeText(text).then(function() {
        const icon = document.getElementById('copyKeyIcon');
        if (icon) {
            icon.className = 'bi bi-check-lg text-success';
            setTimeout(() => { icon.className = 'bi bi-clipboard'; }, 2000);
        }
    });
}
</script>
@endsection
