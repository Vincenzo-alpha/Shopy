<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Shopy - Product & Service Marketplace')</title>
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Bootstrap 5 CSS & Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    
    <!-- Custom Modern Styling -->
    <style>
        :root {
            --primary: #4f46e5;
            --primary-hover: #4338ca;
            --primary-light: #eef2ff;
            --secondary: #0f172a;
            --accent: #10b981;
            --warning-amber: #f59e0b;
            --bg-body: #f8fafc;
            --card-border: #e2e8f0;
        }

        body {
            font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, sans-serif;
            background-color: var(--bg-body);
            color: #334155;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        .navbar-brand {
            font-weight: 800;
            font-size: 1.45rem;
            color: var(--primary) !important;
            letter-spacing: -0.5px;
        }

        .navbar-brand span {
            color: var(--secondary);
        }

        .btn-primary {
            background-color: var(--primary);
            border-color: var(--primary);
            font-weight: 600;
            padding: 0.55rem 1.25rem;
            border-radius: 0.5rem;
            transition: all 0.2s ease;
        }

        .btn-primary:hover, .btn-primary:focus {
            background-color: var(--primary-hover);
            border-color: var(--primary-hover);
            transform: translateY(-1px);
        }

        .card {
            border: 1px solid var(--card-border);
            border-radius: 0.85rem;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
            transition: all 0.2s ease;
        }

        .card-hover:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.08);
            border-color: #cbd5e1;
        }

        .badge-type-product {
            background-color: #e0e7ff;
            color: #3730a3;
            font-weight: 600;
        }

        .badge-type-service {
            background-color: #fef3c7;
            color: #92400e;
            font-weight: 600;
        }

        .badge-status-available {
            background-color: #d1fae5;
            color: #065f46;
        }

        .badge-status-pending {
            background-color: #fed7aa;
            color: #9a3412;
        }

        .badge-status-completed {
            background-color: #dbeafe;
            color: #1e40af;
        }

        .text-gradient {
            background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .hero-banner {
            background: radial-gradient(circle at 10% 20%, rgb(241, 245, 249) 0%, rgb(238, 242, 255) 90%);
            border-bottom: 1px solid #e2e8f0;
            padding: 3.5rem 0 2.5rem;
        }

        .negotiation-bubble-seller {
            background-color: #f1f5f9;
            border-left: 4px solid var(--primary);
            border-radius: 0.5rem;
            padding: 0.85rem 1rem;
        }

        .negotiation-bubble-customer {
            background-color: #ecfdf5;
            border-left: 4px solid var(--accent);
            border-radius: 0.5rem;
            padding: 0.85rem 1rem;
        }

        .footer {
            margin-top: auto;
            background-color: #ffffff;
            border-top: 1px solid #e2e8f0;
            padding: 2rem 0;
        }

        .sidebar-menu a {
            color: #475569;
            font-weight: 500;
            padding: 0.65rem 1rem;
            border-radius: 0.5rem;
            display: flex;
            align-items: center;
            gap: 0.75rem;
            text-decoration: none;
            transition: all 0.15s ease;
        }

        .sidebar-menu a:hover, .sidebar-menu a.active {
            background-color: var(--primary-light);
            color: var(--primary);
            font-weight: 600;
        }
    </style>
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
    <!-- Top Navigation -->
    <nav class="navbar navbar-expand-lg navbar-light bg-white border-bottom sticky-top shadow-sm">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center gap-2" href="{{ route('marketplace.index') }}">
                <i class="bi bi-shop text-primary fs-3"></i>
                <span>Shopy</span><span class="text-primary fs-5">.in</span>
            </a>
            
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarContent">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarContent">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0 ms-lg-3">
                    <li class="nav-item">
                        <a class="nav-link fw-semibold {{ request()->routeIs('marketplace.index') ? 'active text-primary' : '' }}" href="{{ route('marketplace.index') }}">
                            <i class="bi bi-grid me-1"></i> Browse Marketplace
                        </a>
                    </li>
                </ul>

                <ul class="navbar-nav ms-auto align-items-center gap-2">
                    @if(Auth::guard('customer')->check())
                        <li class="nav-item">
                            <a class="nav-link position-relative" href="{{ route('customer.interests') }}">
                                <i class="bi bi-heart fs-5"></i>
                                <span class="d-lg-none ms-1">My Interests</span>
                            </a>
                        </li>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle fw-semibold d-flex align-items-center gap-2" href="#" role="button" data-bs-toggle="dropdown">
                                <span class="rounded-circle bg-primary-subtle text-primary px-2 py-1 fs-6">
                                    <i class="bi bi-person"></i>
                                </span>
                                {{ Auth::guard('customer')->user()->customer_name }}
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0">
                                <li><a class="dropdown-item" href="{{ route('customer.dashboard') }}"><i class="bi bi-speedometer2 me-2"></i> Active Deals</a></li>
                                <li><a class="dropdown-item" href="{{ route('customer.interests') }}"><i class="bi bi-heart me-2"></i> My Interests</a></li>
                                <li><a class="dropdown-item" href="{{ route('customer.deals.completed') }}"><i class="bi bi-check-circle me-2"></i> Completed Deals</a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <form action="{{ route('customer.logout') }}" method="POST">
                                        @csrf
                                        <button type="submit" class="dropdown-item text-danger"><i class="bi bi-box-arrow-right me-2"></i> Logout</button>
                                    </form>
                                </li>
                            </ul>
                        </li>
                    @elseif(Auth::guard('seller')->check())
                        <li class="nav-item">
                            <a class="btn btn-outline-primary btn-sm me-2" href="{{ route('seller.dashboard') }}">
                                <i class="bi bi-briefcase me-1"></i> Seller Portal
                            </a>
                        </li>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle fw-semibold" href="#" role="button" data-bs-toggle="dropdown">
                                {{ Auth::guard('seller')->user()->seller_name }}
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0">
                                <li><a class="dropdown-item" href="{{ route('seller.dashboard') }}"><i class="bi bi-speedometer2 me-2"></i> Dashboard</a></li>
                                <li><a class="dropdown-item" href="{{ route('seller.products.index') }}"><i class="bi bi-box me-2"></i> My Listings</a></li>
                                <li><a class="dropdown-item" href="{{ route('seller.wallet.index') }}"><i class="bi bi-wallet2 me-2"></i> Wallet & Earnings</a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <form action="{{ route('seller.logout') }}" method="POST">
                                        @csrf
                                        <button type="submit" class="dropdown-item text-danger"><i class="bi bi-box-arrow-right me-2"></i> Logout</button>
                                    </form>
                                </li>
                            </ul>
                        </li>
                    @elseif(Auth::guard('admin')->check())
                        <li class="nav-item">
                            <a class="btn btn-dark btn-sm" href="{{ route('admin.dashboard') }}">
                                <i class="bi bi-shield-lock me-1"></i> Admin Panel
                            </a>
                        </li>
                    @else
                        <!-- Guest Auth Links -->
                        <li class="nav-item">
                            <a class="nav-link fw-semibold" href="{{ route('customer.login') }}">Customer Sign In</a>
                        </li>
                        <li class="nav-item">
                            <a class="btn btn-sm btn-outline-primary fw-semibold" href="{{ route('seller.login') }}">
                                <i class="bi bi-shop-window me-1"></i> Seller Portal
                            </a>
                        </li>
                    @endif
                </ul>
            </div>
        </div>
    </nav>

    <!-- session data rendered as JS vars for modal trigger -->
    @php
        $flashSuccess = session('success');
        $flashError   = session('error');
        $flashWarning = session('warning');
        $flashInfo    = session('info');
        $flashErrors  = $errors->any() ? $errors->all() : [];
    @endphp

    <!-- Main Content Area -->
    <main class="flex-grow-1">
        @yield('content')
    </main>

    <!-- Modern Footer -->
    <footer class="footer">
        <div class="container">
            <div class="row align-items-center gy-3">
                <div class="col-md-6 text-center text-md-start">
                    <div class="fw-bold fs-5 text-primary mb-1">
                        <i class="bi bi-shop me-1"></i> Shopy
                    </div>
                    <p class="text-muted small mb-0">Direct Marketplace Connecting Local Sellers & Customers. Negotiate, Agree & Collect with Secure Completion Keys.</p>
                </div>
                <div class="col-md-6 text-center text-md-end">
                    <div class="d-flex justify-content-center justify-content-md-end gap-3 small text-muted">
                        <a href="{{ route('marketplace.index') }}" class="text-decoration-none text-muted">Marketplace</a>
                        <a href="{{ route('seller.login') }}" class="text-decoration-none text-muted">Sell on Shopy</a>
                        <a href="{{ route('admin.login') }}" class="text-decoration-none text-muted">Admin Access</a>
                    </div>
                    <div class="small text-muted mt-2">
                        &copy; {{ date('Y') }} Shopy Marketplace. All Rights Reserved.
                    </div>
                </div>
            </div>
        </div>
    </footer>

    <!-- Bootstrap 5 JS & jQuery -->
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
        // ── Setup CSRF for AJAX ──
        $.ajaxSetup({
            headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }
        });

        function debounce(func, wait) {
            let timeout;
            return function (...args) {
                clearTimeout(timeout);
                timeout = setTimeout(() => func.apply(this, args), wait);
            };
        }

        // ── Global Alert Modal ──
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
            } else {
                $list.addClass('d-none');
            }

            new bootstrap.Modal(document.getElementById('globalAlertModal')).show();
        }

        // ── Confirm Modal ──
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

        // ── Named confirm helpers ──
        function confirmFormSubmit(event, formId, message) {
            event.preventDefault();
            showConfirm(message, function () {
                document.getElementById(formId).submit();
            });
            return false;
        }
        function confirmLink(event, link, message) {
            event.preventDefault();
            showConfirm(message || 'Do you want to proceed?', function () {
                window.location.href = link.href;
            });
            return false;
        }
        function confirmAcceptOffer(event, formId, amount) {
            event.preventDefault();
            showConfirm(`Accept this offer of ₹${amount} and proceed to payment?`, function () {
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

        // ── Auto-fire modal from session flash ──
        @php
            $flashSuccess = session('success');
            $flashError   = session('error');
            $flashWarning = session('warning');
            $flashInfo    = session('info');
            $flashErrors  = $errors->any() ? $errors->all() : [];
        @endphp
        document.addEventListener('DOMContentLoaded', function () {
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
