<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin Console - Shopy')</title>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    
    <style>
        :root {
            --primary: #4f46e5;
            --dark-navy: #0f172a;
            --bg-body: #f8fafc;
        }
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: var(--bg-body);
            color: #1e293b;
            min-height: 100vh;
        }
        .sidebar {
            background-color: var(--dark-navy);
            min-height: 100vh;
        }
        .sidebar .nav-link {
            color: #94a3b8;
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
            color: #ffffff;
            background-color: rgba(255, 255, 255, 0.1);
            font-weight: 600;
        }
        .card {
            border: 1px solid #e2e8f0;
            border-radius: 0.75rem;
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
    </style>
    @yield('styles')
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Admin Sidebar -->
            <nav class="col-md-3 col-lg-2 d-md-block sidebar py-3 collapse" id="adminSidebar">
                <div class="px-3 mb-4">
                    <a href="{{ route('admin.dashboard') }}" class="text-decoration-none d-flex align-items-center gap-2">
                        <i class="bi bi-shield-check text-primary fs-3"></i>
                        <span class="fs-5 fw-bold text-white">Shopy Admin</span>
                    </a>
                </div>

                <ul class="nav flex-column">
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}" href="{{ route('admin.dashboard') }}">
                            <i class="bi bi-speedometer2"></i> Dashboard Overview
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('admin.sellers.*') ? 'active' : '' }}" href="{{ route('admin.sellers.index') }}">
                            <i class="bi bi-shop"></i> Sellers Management
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('admin.customers.*') ? 'active' : '' }}" href="{{ route('admin.customers.index') }}">
                            <i class="bi bi-people"></i> Customers Management
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('admin.listings.*') ? 'active' : '' }}" href="{{ route('admin.listings.index') }}">
                            <i class="bi bi-box-seam"></i> Listings Moderation
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('admin.deals.*') ? 'active' : '' }}" href="{{ route('admin.deals.index') }}">
                            <i class="bi bi-journal-text"></i> Archive & Master Deals
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('admin.settings.*') ? 'active' : '' }}" href="{{ route('admin.settings.index') }}">
                            <i class="bi bi-sliders"></i> Platform Fee Settings
                        </a>
                    </li>
                    <li class="nav-item mt-5 pt-3 border-top border-secondary">
                        <form action="{{ route('admin.logout') }}" method="POST" class="px-3">
                            @csrf
                            <button type="submit" class="btn btn-outline-danger w-100 btn-sm">
                                <i class="bi bi-box-arrow-right me-1"></i> Admin Logout
                            </button>
                        </form>
                    </li>
                </ul>
            </nav>

            <!-- Admin Content -->
            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 py-3">
                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-2 pb-2 mb-3 border-bottom bg-white p-3 rounded shadow-sm">
                    <button class="btn btn-sm btn-outline-secondary d-md-none me-2" type="button" data-bs-toggle="collapse" data-bs-target="#adminSidebar">
                        <i class="bi bi-list"></i>
                    </button>
                    <div>
                        <h5 class="mb-0 fw-bold">@yield('page_title', 'Admin Dashboard')</h5>
                        <small class="text-muted">Administrator: {{ Auth::guard('admin')->user()->name ?? 'Admin' }}</small>
                    </div>
                    <div>
                        <a href="{{ route('marketplace.index') }}" target="_blank" class="btn btn-outline-primary btn-sm">
                            <i class="bi bi-eye me-1"></i> Public Marketplace
                        </a>
                    </div>
                </div>

                @if(session('success') || session('error') || $errors->any())
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
        let confirmCallback = null;
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
        function confirmToggleStatus(event, formId, label) {
            event.preventDefault();
            showConfirm(`Update status of this ${label}?`, function () {
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
