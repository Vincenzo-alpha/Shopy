@extends('layouts.app')

@section('title', 'Shopy – Product & Service Marketplace')

@section('content')
<!-- Hero Section -->
<section class="hero-banner">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-8">
                <span class="badge bg-primary-subtle text-primary fw-bold px-3 py-2 rounded-pill mb-2">
                    <i class="bi bi-geo-alt-fill me-1"></i> Local Product & Service Deals
                </span>
                <h1 class="display-5 fw-extrabold text-dark mb-2">
                    Connect Directly with Verified Sellers in Your City.
                </h1>
                <p class="lead text-muted mb-4">
                    Express interest, negotiate transparent prices directly with sellers, simulate payments, and securely collect or receive services using verification keys.
                </p>
            </div>
            <div class="col-lg-4 d-none d-lg-block text-end">
                <div class="p-4 bg-white rounded-3 shadow-sm border text-start">
                    <h6 class="fw-bold text-dark mb-2"><i class="bi bi-shield-check text-success me-1"></i> How Shopy Works</h6>
                    <ul class="list-unstyled small text-muted mb-0">
                        <li class="mb-2"><i class="bi bi-1-circle text-primary me-2"></i><strong>Express Interest</strong> in any item or service</li>
                        <li class="mb-2"><i class="bi bi-2-circle text-primary me-2"></i><strong>Negotiate</strong> within seller boundaries</li>
                        <li class="mb-2"><i class="bi bi-3-circle text-primary me-2"></i><strong>Sandbox Pay</strong> & receive completion key</li>
                        <li><i class="bi bi-4-circle text-primary me-2"></i><strong>Collect or Receive</strong> item & confirm key</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Filter & Search Bar -->
<section class="py-4 bg-white border-bottom sticky-top" style="top: 56px; z-index: 100;">
    <div class="container">
        <form id="filterForm" class="row g-2 align-items-center">
            <!-- Keyword Search -->
            <div class="col-md-4">
                <div class="input-group">
                    <span class="input-group-text bg-light border-end-0"><i class="bi bi-search text-muted"></i></span>
                    <input type="text" name="q" id="searchInput" class="form-control border-start-0" placeholder="Search products, services, categories..." value="{{ request('q') }}">
                </div>
            </div>

            <!-- City Filter -->
            <div class="col-6 col-md-2">
                <select name="city" id="citySelect" class="form-select">
                    <option value="">All Cities</option>
                    @foreach($cities as $c)
                        <option value="{{ $c }}" {{ request('city') == $c ? 'selected' : '' }}>{{ $c }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Category Filter -->
            <div class="col-6 col-md-2">
                <select name="category" id="categorySelect" class="form-select">
                    <option value="">All Categories</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat }}" {{ request('category') == $cat ? 'selected' : '' }}>{{ $cat }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Item Type Filter -->
            <div class="col-6 col-md-2">
                <select name="item_type" id="itemTypeSelect" class="form-select">
                    <option value="">All Types</option>
                    <option value="product" {{ request('item_type') == 'product' ? 'selected' : '' }}>Products Only</option>
                    <option value="service" {{ request('item_type') == 'service' ? 'selected' : '' }}>Services Only</option>
                </select>
            </div>

            <!-- Reset Button & Spinner -->
            <div class="col-6 col-md-2 d-flex align-items-center gap-2">
                <button type="button" id="resetBtn" class="btn btn-outline-secondary w-100">
                    <i class="bi bi-x-circle me-1"></i> Reset
                </button>
                <div id="searchSpinner" class="spinner-border spinner-border-sm text-primary d-none" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
            </div>
        </form>
    </div>
</section>

<!-- Listings Catalog Section -->
<section class="py-5">
    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h4 class="fw-bold mb-0">Explore Marketplace</h4>
                <small class="text-muted" id="resultsCount">Showing verified local listings</small>
            </div>
        </div>

        <div id="listingsContainer">
            @include('marketplace.partials.listing-grid', ['listings' => $listings])
        </div>
    </div>
</section>
@endsection

@section('scripts')
<script>
    $(document).ready(function () {
        const form = $('#filterForm');
        const container = $('#listingsContainer');
        const spinner = $('#searchSpinner');

        function fetchListings() {
            spinner.removeClass('d-none');
            const data = form.serialize();

            $.ajax({
                url: "{{ route('marketplace.index') }}",
                data: data,
                type: "GET",
                success: function (response) {
                    container.html(response);
                    spinner.addClass('d-none');
                },
                error: function () {
                    spinner.addClass('d-none');
                }
            });
        }

        // Debounced text search (350ms)
        $('#searchInput').on('input', debounce(function () {
            fetchListings();
        }, 350));

        // Immediate change for dropdown filters
        $('#citySelect, #categorySelect, #itemTypeSelect').on('change', function () {
            fetchListings();
        });

        // Reset filters
        $('#resetBtn').on('click', function () {
            form[0].reset();
            fetchListings();
        });

        // AJAX pagination clicks
        $(document).on('click', '.pagination a', function (e) {
            e.preventDefault();
            const url = $(this).attr('href');
            spinner.removeClass('d-none');

            $.get(url, function (response) {
                container.html(response);
                spinner.addClass('d-none');
                $('html, body').animate({
                    scrollTop: $("#listingsContainer").offset().top - 120
                }, 200);
            });
        });
    });
</script>
@endsection
