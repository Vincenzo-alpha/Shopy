<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Seller Portal - Shopy')</title>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    
    <style>
        :root {
            --primary: #4f46e5;
            --primary-hover: #4338ca;
            --secondary: #0f172a;
            --accent: #10b981;
            --bg-body: #f1f5f9;
        }
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: var(--bg-body);
            color: #334155;
            min-height: 100vh;
        }
        .sidebar {
            background-color: #ffffff;
            min-height: 100vh;
            border-right: 1px solid #e2e8f0;
        }
        .sidebar .nav-link {
            color: #64748b;
            font-weight: 500;
            padding: 0.75rem 1.25rem;
            border-radius: 0.5rem;
            margin: 0.15rem 0.75rem;
            display: flex;
            align-items: center;
            gap: 0.75rem;
            transition: all 0.2s;
        }
        .sidebar .nav-link:hover, .sidebar .nav-link.active {
            color: var(--primary);
            background-color: #eef2ff;
            font-weight: 600;
        }
        .card {
            border: 1px solid #e2e8f0;
            border-radius: 0.75rem;
            box-shadow: 0 1px 3px rgba(0,0,0,0.03);
        }
        .wallet-pill {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            color: white;
            padding: 0.4rem 0.9rem;
            border-radius: 2rem;
            font-weight: 600;
            font-size: 0.875rem;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }
        /* --- Global Alert Modal Styles --- */
        .modal .gradient-top {
            height: 6px;
            border-radius: 0.5rem 0.5rem 0 0;
        }
        .modal .gradient-success { background: linear-gradient(90deg, #10b981, #059669); }
        .modal .gradient-danger  { background: linear-gradient(90deg, #ef4444, #dc2626); }
        .modal .gradient-warning { background: linear-gradient(90deg, #f59e0b, #d97706); }
        .modal .gradient-info    { background: linear-gradient(90deg, #3b82f6, #2563eb); }
        .modal .icon-wrapper {
            width: 64px; height: 64px;
            border-radius: 50%;
            display: inline-flex; align-items: center; justify-content: center;
            font-size: 1.75rem;
        }
        .modal .icon-success { background: #d1fae5; color: #059669; }
        .modal .icon-danger  { background: #fee2e2; color: #dc2626; }
        .modal .icon-warning { background: #fef3c7; color: #d97706; }
        .modal .icon-info    { background: #dbeafe; color: #2563eb; }
        /* --- Validation Feedback --- */
        .invalid-feedback { display: block; }
        .form-control.is-invalid, .form-select.is-invalid {
            border-color: #ef4444;
            box-shadow: 0 0 0 0.2rem rgba(239,68,68,.15);
        }
        .form-control.is-valid, .form-select.is-valid {
            border-color: #10b981;
            box-shadow: 0 0 0 0.2rem rgba(16,185,129,.10);
        }
    </style>
    @yield('styles')
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <nav class="col-md-3 col-lg-2 d-md-block sidebar py-3 collapse" id="sidebarMenu">
                <div class="px-3 mb-4">
                    <a href="{{ route('marketplace.index') }}" class="text-decoration-none d-flex align-items-center gap-2">
                        <i class="bi bi-shop text-primary fs-3"></i>
                        <span class="fs-5 fw-bold text-dark">Shopy</span>
                        <span class="badge bg-primary-subtle text-primary">Seller</span>
                    </a>
                </div>

                <div class="px-3 mb-3">
                    <div class="p-2 bg-light rounded text-center border">
                        <small class="text-muted d-block">Available Earnings</small>
                        <span class="fw-bold text-success fs-5">
                            ₹{{ number_format(Auth::guard('seller')->user()->wallet->available_balance ?? 0, 2) }}
                        </span>
                        @if((Auth::guard('seller')->user()->wallet->pending_balance ?? 0) > 0)
                            <div class="small text-muted" style="font-size: 0.75rem;">
                                +₹{{ number_format(Auth::guard('seller')->user()->wallet->pending_balance, 2) }} pending
                            </div>
                        @endif
                    </div>
                </div>

                <ul class="nav flex-column">
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('seller.dashboard') || request()->routeIs('seller.deals.*') ? 'active' : '' }}" href="{{ route('seller.dashboard') }}">
                            <i class="bi bi-briefcase"></i> Deals & Negotiations
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('seller.interests.*') ? 'active' : '' }}" href="{{ route('seller.interests.index') }}">
                            <i class="bi bi-bell"></i> Customer Interests
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('seller.products.*') ? 'active' : '' }}" href="{{ route('seller.products.index') }}">
                            <i class="bi bi-box-seam"></i> Products & Services
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('seller.cities.*') ? 'active' : '' }}" href="{{ route('seller.cities.index') }}">
                            <i class="bi bi-geo-alt"></i> Service Cities
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('seller.wallet.*') ? 'active' : '' }}" href="{{ route('seller.wallet.index') }}">
                            <i class="bi bi-wallet2"></i> Wallet & Ledger
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('marketplace.index') }}" target="_blank">
                            <i class="bi bi-box-arrow-up-right"></i> View Marketplace
                        </a>
                    </li>
                    <li class="nav-item mt-4 pt-3 border-top">
                        <form action="{{ route('seller.logout') }}" method="POST" class="px-3">
                            @csrf
                            <button type="submit" class="btn btn-outline-danger w-100 btn-sm">
                                <i class="bi bi-box-arrow-right me-1"></i> Logout
                            </button>
                        </form>
                    </li>
                </ul>
            </nav>

            <!-- Main Content Area -->
            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 py-3">
                <!-- Topbar -->
                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-2 pb-2 mb-3 border-bottom bg-white p-3 rounded shadow-sm">
                    <button class="btn btn-sm btn-outline-secondary d-md-none me-2" type="button" data-bs-toggle="collapse" data-bs-target="#sidebarMenu">
                        <i class="bi bi-list"></i>
                    </button>
                    <div>
                        <h5 class="mb-0 fw-bold">@yield('page_title', 'Seller Portal')</h5>
                        <small class="text-muted">Store: {{ Auth::guard('seller')->user()->seller_name }} ({{ Auth::guard('seller')->user()->city }})</small>
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        <span class="wallet-pill">
                            <i class="bi bi-cash-coin"></i>
                            Wallet: ₹{{ number_format(Auth::guard('seller')->user()->wallet->available_balance ?? 0, 2) }}
                        </span>
                    </div>
                </div>

                @if(session('success') || session('error') || session('warning') || session('info') || $errors->any())
                    {{-- Flash handled by modal JS below --}}
                @endif

                @yield('content')
            </main>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="{{ asset('js/app.js') }}"></script>

    <!-- Global Alert Modal -->
    <div class="modal fade" id="globalAlertModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-sm">
            <div class="modal-content border-0 shadow-lg" style="border-radius:1rem;overflow:hidden;">
                <div id="globalAlertGradient" class="gradient-top"></div>
                <div class="modal-body text-center p-4">
                    <div id="globalAlertIconWrap" class="icon-wrapper mb-3 mx-auto">
                        <i id="globalAlertIcon" class="bi"></i>
                    </div>
                    <h6 id="globalAlertType" class="fw-bold mb-2"></h6>
                    <p id="globalAlertMessage" class="text-muted small mb-0"></p>
                    <ul id="globalAlertList" class="small ps-3 d-none text-start mt-2 mb-0"></ul>
                    <div class="mt-3">
                        <button type="button" class="btn btn-primary btn-sm px-4" data-bs-dismiss="modal">OK</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Confirm Modal -->
    <div class="modal fade" id="confirmModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg" style="border-radius:1rem;overflow:hidden;">
                <div class="gradient-top gradient-warning"></div>
                <div class="modal-body text-center p-4">
                    <div class="icon-wrapper icon-warning mb-3 mx-auto">
                        <i class="bi bi-question-circle-fill"></i>
                    </div>
                    <h6 class="fw-bold mb-2">Confirmation</h6>
                    <p id="confirmMessage" class="text-muted small">Do you want to proceed?</p>
                    <div class="d-flex gap-2 justify-content-center mt-3">
                        <button type="button" class="btn btn-outline-secondary btn-sm px-3" data-bs-dismiss="modal">Cancel</button>
                        <button type="button" class="btn btn-warning btn-sm px-3 fw-bold" id="confirmContinue">Confirm</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        $.ajaxSetup({
            headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }
        });

        function showAlert(type, message, listItems) {
            const configs = {
                success: { gradient: 'gradient-success', icon: 'bi-check-circle-fill', iconClass: 'icon-success', label: 'Success' },
                error:   { gradient: 'gradient-danger',  icon: 'bi-x-circle-fill',     iconClass: 'icon-danger',  label: 'Error' },
                danger:  { gradient: 'gradient-danger',  icon: 'bi-x-circle-fill',     iconClass: 'icon-danger',  label: 'Error' },
                warning: { gradient: 'gradient-warning', icon: 'bi-exclamation-triangle-fill', iconClass: 'icon-warning', label: 'Warning' },
                info:    { gradient: 'gradient-info',    icon: 'bi-info-circle-fill',  iconClass: 'icon-info',    label: 'Info' },
            };
            const cfg = configs[type] || configs.info;
            $('#globalAlertGradient').attr('class', 'gradient-top ' + cfg.gradient);
            $('#globalAlertIcon').attr('class', 'bi ' + cfg.icon);
            $('#globalAlertIconWrap').attr('class', 'icon-wrapper mb-3 mx-auto ' + cfg.iconClass);
            $('#globalAlertType').text(cfg.label);
            $('#globalAlertMessage').text(message || '');
            const $list = $('#globalAlertList').empty();
            if (listItems && listItems.length) {
                listItems.forEach(item => $list.append(`<li>${item}</li>`));
                $list.removeClass('d-none');
            } else { $list.addClass('d-none'); }
            new bootstrap.Modal(document.getElementById('globalAlertModal')).show();
        }

        let confirmCallback = null;
        function showConfirm(message, callback) {
            document.getElementById('confirmMessage').innerText = message;
            confirmCallback = callback;
            new bootstrap.Modal(document.getElementById('confirmModal')).show();
        }
        document.getElementById('confirmContinue').addEventListener('click', function () {
            if (confirmCallback) confirmCallback();
            bootstrap.Modal.getInstance(document.getElementById('confirmModal')).hide();
        });

        function confirmFormSubmit(event, formId, message) {
            event.preventDefault();
            showConfirm(message, function () { document.getElementById(formId).submit(); });
            return false;
        }
        function confirmAcceptOffer(event, formId, amount) {
            event.preventDefault();
            showConfirm(`Accept buyer offer of ₹${amount}? This will lock the price and await customer payment.`, function () {
                document.getElementById(formId).submit();
            });
            return false;
        }
        function confirmDenyOffer(event, formId) {
            event.preventDefault();
            showConfirm('Are you sure you want to deny this offer? This will decline the offer and terminate the deal negotiation.', function () {
                document.getElementById(formId).submit();
            });
            return false;
        }
        function confirmVerifyKey(event, formId) {
            event.preventDefault();
            showConfirm('Verify the completion key and mark this deal as completed? This action cannot be undone.', function () {
                document.getElementById(formId).submit();
            });
            return false;
        }
        function confirmToggleAvailability(event, formId, currentStatus) {
            event.preventDefault();
            const newStatus = currentStatus === 'available' ? 'unavailable' : 'available';
            showConfirm(`Change listing status to "${newStatus}"?`, function () {
                document.getElementById(formId).submit();
            });
            return false;
        }
        function confirmLogout(event, formId) {
            event.preventDefault();
            showConfirm('Are you sure you want to logout?', function () {
                document.getElementById(formId).submit();
            });
            return false;
        }
        function confirmInitiateDeal(event, formId) {
            event.preventDefault();
            showConfirm('Initiate a deal with this customer? A live negotiation will be opened.', function () {
                document.getElementById(formId).submit();
            });
            return false;
        }
        function confirmDeleteCity(event, formId) {
            event.preventDefault();
            showConfirm('Remove this service city from your profile?', function () {
                document.getElementById(formId).submit();
            });
            return false;
        }

        document.addEventListener('DOMContentLoaded', function () {
            @php
                $flashSuccess = session('success');
                $flashError   = session('error');
                $flashWarning = session('warning');
                $flashInfo    = session('info');
                $flashErrors  = $errors->any() ? $errors->all() : [];
            @endphp
            @if($flashSuccess)
                showAlert('success', @json($flashSuccess));
            @elseif(!empty($flashErrors))
                showAlert('error', 'Please correct the following errors:', @json($flashErrors));
            @elseif($flashError)
                showAlert('error', @json($flashError));
            @elseif($flashWarning)
                showAlert('warning', @json($flashWarning));
            @elseif($flashInfo)
                showAlert('info', @json($flashInfo));
            @endif
        });
    </script>
    @yield('scripts')
</body>
</html>
