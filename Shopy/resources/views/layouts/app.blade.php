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

    <!-- Global Alerts -->
    <div class="container mt-3">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm" role="alert">
                <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm" role="alert">
                <i class="bi bi-exclamation-triangle-fill me-2"></i> {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if(session('warning'))
            <div class="alert alert-warning alert-dismissible fade show border-0 shadow-sm" role="alert">
                <i class="bi bi-exclamation-circle-fill me-2"></i> {{ session('warning') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if(session('info'))
            <div class="alert alert-info alert-dismissible fade show border-0 shadow-sm" role="alert">
                <i class="bi bi-info-circle-fill me-2"></i> {{ session('info') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if($errors->any())
            <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm" role="alert">
                <div class="fw-bold mb-1"><i class="bi bi-x-circle me-1"></i> Please correct the following errors:</div>
                <ul class="mb-0 ps-3">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
    </div>

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
    
    <script>
        // Setup CSRF header for all jQuery AJAX requests
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        // Utility debounce function for smooth performance
        function debounce(func, wait) {
            let timeout;
            return function (...args) {
                const context = this;
                clearTimeout(timeout);
                timeout = setTimeout(() => func.apply(context, args), wait);
            };
        }
    </script>
    @yield('scripts')
</body>
</html>
