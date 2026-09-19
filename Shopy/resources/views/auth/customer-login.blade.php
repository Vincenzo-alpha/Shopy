@extends('layouts.app')

@section('title', 'Customer Login - Shopy')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-5">
            <div class="card shadow-sm border-0">
                <div class="card-body p-4 p-md-5">
                    <div class="text-center mb-4">
                        <div class="rounded-circle bg-primary-subtle text-primary d-inline-flex align-items-center justify-content-center mb-2" style="width: 56px; height: 56px;">
                            <i class="bi bi-person-fill fs-3"></i>
                        </div>
                        <h3 class="fw-bold">Customer Login</h3>
                        <p class="text-muted small">Sign in to browse local products, negotiate, and complete deals</p>
                    </div>

                    <form action="{{ route('customer.login.submit') }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Email Address</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light"><i class="bi bi-envelope"></i></span>
                                <input type="email" name="email" class="form-control" value="{{ old('email', 'customer1@shopy.com') }}" required autofocus>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Password</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light"><i class="bi bi-lock"></i></span>
                                <input type="password" name="password" class="form-control" value="password123" required>
                            </div>
                        </div>

                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="remember" id="remember" checked>
                                <label class="form-check-label small" for="remember">Remember me</label>
                            </div>
                            <span class="small text-muted">Demo: customer1@shopy.com / password123</span>
                        </div>

                        <button type="submit" class="btn btn-primary w-100 py-2 mb-3">
                            <i class="bi bi-box-arrow-in-right me-1"></i> Sign In as Customer
                        </button>

                        <div class="text-center small text-muted">
                            Don't have an account? <a href="{{ route('customer.register') }}" class="text-primary fw-semibold">Register here</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
