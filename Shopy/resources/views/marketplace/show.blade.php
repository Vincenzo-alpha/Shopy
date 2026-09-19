@extends('layouts.app')

@section('title', $item->prod_service_name . ' - Shopy')

@section('content')
<div class="container py-5">
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('marketplace.index') }}" class="text-decoration-none">Marketplace</a></li>
            <li class="breadcrumb-item"><a href="{{ route('marketplace.index', ['category' => $item->category]) }}" class="text-decoration-none">{{ $item->category }}</a></li>
            <li class="breadcrumb-item active" aria-current="page">{{ $item->prod_service_name }}</li>
        </ol>
    </nav>

    <div class="row g-4">
        <!-- Listing Visual / Image Column -->
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm overflow-hidden p-4 bg-white text-center">
                @if($item->image_path)
                    <img src="{{ asset('storage/' . $item->image_path) }}" 
                         alt="{{ $item->prod_service_name }}" 
                         class="img-fluid rounded" 
                         style="max-height: 400px; object-fit: contain;">
                @else
                    <div class="py-5 bg-light rounded text-muted">
                        <i class="bi {{ $item->item_type === 'service' ? 'bi-tools' : 'bi-box-seam' }} display-1 opacity-25"></i>
                        <p class="mt-3 mb-0">No image uploaded for this listing</p>
                    </div>
                @endif

                <div class="d-flex justify-content-between align-items-center mt-3 pt-3 border-top">
                    <span class="badge {{ $item->item_type === 'service' ? 'badge-type-service' : 'badge-type-product' }} px-3 py-2 rounded-pill fs-6">
                        <i class="bi {{ $item->item_type === 'service' ? 'bi-wrench' : 'bi-box' }} me-1"></i>
                        {{ ucfirst($item->item_type) }} Listing
                    </span>
                    <span class="badge bg-light text-dark border px-3 py-2 rounded-pill">
                        Ref: {{ $item->prod_servics_unique_no }}
                    </span>
                </div>
            </div>

            <!-- Seller Information Card -->
            <div class="card border-0 shadow-sm mt-4 p-4">
                <h5 class="fw-bold mb-3 d-flex align-items-center gap-2">
                    <i class="bi bi-shop text-primary"></i> Verified Seller Profile
                </h5>
                <div class="d-flex align-items-center gap-3 mb-3">
                    <div class="rounded-circle bg-primary-subtle text-primary p-3 fs-4">
                        <i class="bi bi-person-badge"></i>
                    </div>
                    <div>
                        <h6 class="fw-bold mb-0">{{ $item->seller->seller_name }}</h6>
                        <small class="text-muted"><i class="bi bi-geo-alt-fill text-danger me-1"></i> {{ $item->seller->city }}, {{ $item->seller->state }}</small>
                    </div>
                </div>

                <div class="p-3 bg-light rounded border small">
                    <div class="fw-semibold text-secondary mb-1"><i class="bi bi-info-circle me-1"></i> Privacy & Contact Policy:</div>
                    <p class="mb-0 text-muted">To prevent spam, seller's exact address and phone number are disclosed only upon active deal finalization and payment.</p>
                </div>

                @if($item->item_type === 'service')
                    <div class="mt-3">
                        <small class="text-muted fw-bold d-block mb-1">Service Coverage Cities:</small>
                        <div class="d-flex flex-wrap gap-1">
                            @forelse($item->seller->cities as $city)
                                <span class="badge bg-secondary-subtle text-secondary">{{ $city->city_name }}</span>
                            @empty
                                <span class="badge bg-secondary-subtle text-secondary">{{ $item->seller->city }}</span>
                            @endforelse
                        </div>
                    </div>
                @endif
            </div>
        </div>

        <!-- Listing Details & Interest Box Column -->
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm p-4 h-100 d-flex flex-column">
                <span class="text-uppercase tracking-wide text-primary fw-bold small mb-1">{{ $item->category }}</span>
                <h2 class="fw-extrabold text-dark mb-3">{{ $item->prod_service_name }}</h2>

                <!-- Price Box -->
                <div class="p-4 bg-light rounded-3 border mb-4">
                    <div class="row align-items-center">
                        <div class="col-sm-6">
                            <small class="text-muted d-block fw-semibold">Listed Price</small>
                            <span class="display-6 fw-bold text-primary">₹{{ number_format($item->listed_price, 2) }}</span>
                        </div>
                        <div class="col-sm-6 text-sm-end mt-2 mt-sm-0">
                            <small class="text-muted d-block fw-semibold">Negotiation Range</small>
                            <span class="fw-bold text-dark">
                                ₹{{ number_format($item->minimum_rate, 2) }} – ₹{{ number_format($item->maximum_rate, 2) }}
                            </span>
                            <div class="text-success small"><i class="bi bi-check2-circle"></i> Price open to negotiation</div>
                        </div>
                    </div>

                    <!-- Platform Fee Notice -->
                    <div class="mt-3 pt-3 border-top small text-muted">
                        <i class="bi bi-shield-check text-success me-1"></i>
                        <strong>Fair Pricing Policy:</strong> The customer pays only the agreed negotiated amount. The platform fee is paid exclusively by the seller and deducted from their earnings upon completion.
                    </div>
                </div>

                <!-- Description -->
                <h5 class="fw-bold mb-2">Description</h5>
                <p class="text-muted mb-4" style="line-height: 1.7;">
                    {{ $item->description ?: 'No detailed description provided by the seller.' }}
                </p>

                <!-- Fulfillment Method -->
                <div class="p-3 bg-white rounded border mb-4">
                    <h6 class="fw-bold mb-1"><i class="bi bi-truck-flatbed text-primary me-2"></i> Fulfillment Method</h6>
                    <p class="small text-muted mb-0">
                        @if($item->item_type === 'product')
                            <strong>Customer Collection:</strong> Once your deal is finalized and sandbox payment is made, you will collect this item directly from the seller's verified address in <strong>{{ $item->seller->city }}</strong>.
                        @else
                            <strong>Service Receipt:</strong> The seller or service crew will attend to you in <strong>{{ $item->seller->city }}</strong> or your selected service city.
                        @endif
                    </p>
                </div>

                <!-- Action Button Area -->
                <div class="mt-auto pt-3 border-top">
                    @if(Auth::guard('customer')->check())
                        @if($existingInterest)
                            <div class="alert alert-info border-0 shadow-sm d-flex align-items-center justify-content-between mb-0">
                                <div>
                                    <i class="bi bi-heart-fill text-danger me-2"></i>
                                    <strong>Active Interest Expressed!</strong>
                                    <div class="small text-muted">Status: {{ $existingInterest->interest_status }}</div>
                                </div>
                                <a href="{{ route('customer.interests') }}" class="btn btn-sm btn-primary">
                                    View in Interests <i class="bi bi-arrow-right ms-1"></i>
                                </a>
                            </div>
                        @else
                            <button id="expressInterestBtn" class="btn btn-primary btn-lg w-100 py-3 shadow-sm fw-bold">
                                <i class="bi bi-heart me-2"></i> Express Interest & Start Negotiating
                            </button>
                            <div id="interestFeedback" class="mt-2 text-center small text-success d-none"></div>
                        @endif
                    @elseif(Auth::guard('seller')->check())
                        <div class="alert alert-light border text-center mb-0">
                            <i class="bi bi-info-circle me-1"></i> You are logged in as a <strong>Seller</strong>. Log in with a customer account to express buyer interest.
                        </div>
                    @else
                        <div class="d-grid gap-2">
                            <a href="{{ route('customer.login') }}" class="btn btn-primary btn-lg py-3 fw-bold">
                                <i class="bi bi-box-arrow-in-right me-2"></i> Sign In to Express Interest
                            </a>
                            <a href="{{ route('customer.register') }}" class="btn btn-outline-secondary btn-sm text-center">
                                New to Shopy? Create a free customer account
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    $(document).ready(function () {
        $('#expressInterestBtn').on('click', function () {
            const btn = $(this);
            btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-2"></span> Submitting Interest...');

            $.ajax({
                url: "{{ route('customer.interests.express') }}",
                type: "POST",
                data: {
                    prod_service_id: "{{ $item->prod_service_id_pk }}"
                },
                success: function (response) {
                    btn.removeClass('btn-primary').addClass('btn-success')
                       .html('<i class="bi bi-check2-circle me-2"></i> Interest Expressed!');
                    
                    $('#interestFeedback').removeClass('d-none')
                        .html('Success! Redirecting to your interests...');

                    setTimeout(function () {
                        window.location.href = "{{ route('customer.interests') }}";
                    }, 1200);
                },
                error: function (xhr) {
                    btn.prop('disabled', false).html('<i class="bi bi-heart me-2"></i> Express Interest & Start Negotiating');
                    alert('An error occurred. Please try again.');
                }
            });
        });
    });
</script>
@endsection
