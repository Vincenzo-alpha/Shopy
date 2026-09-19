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

                <!-- Flash Alerts -->
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

                @if(session('info'))
                    <div class="alert alert-info alert-dismissible fade show border-0 shadow-sm" role="alert">
                        <i class="bi bi-info-circle-fill me-2"></i> {{ session('info') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                @if($errors->any())
                    <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm" role="alert">
                        <div class="fw-bold mb-1"><i class="bi bi-x-circle me-1"></i> Errors found:</div>
                        <ul class="mb-0 ps-3">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                @yield('content')
            </main>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
    </script>
    @yield('scripts')
</body>
</html>
