@extends('layouts.app')

@section('title', 'Seller Portal Login - Shopy')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-5">
            <div class="card shadow-sm border-0">
                <div class="card-body p-4 p-md-5">
                    <div class="text-center mb-4">
                        <div class="rounded-circle bg-primary text-white d-inline-flex align-items-center justify-content-center mb-2" style="width: 56px; height: 56px;">
                            <i class="bi bi-shop-window fs-3"></i>
                        </div>
                        <h3 class="fw-bold">Seller Portal</h3>
                        <p class="text-muted small">Manage listings, negotiate with buyers, and confirm collections</p>
                    </div>

                    <form action="{{ route('seller.login.submit') }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Seller Email</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light"><i class="bi bi-envelope"></i></span>
                                <input type="email" name="email" class="form-control" value="{{ old('email', 'seller1@shopy.com') }}" required autofocus>
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
                                <input class="form-check-input" type="checkbox" name="remember" id="sellerRemember" checked>
                                <label class="form-check-label small" for="sellerRemember">Remember me</label>
                            </div>
                            <span class="small text-muted">Demo: seller1@shopy.com / password123</span>
                        </div>

                        <button type="submit" class="btn btn-primary w-100 py-2 mb-3">
                            <i class="bi bi-box-arrow-in-right me-1"></i> Access Seller Dashboard
                        </button>

                        <div class="text-center small text-muted">
                            Want to sell products or services? <a href="{{ route('seller.register') }}" class="text-primary fw-semibold">Register as a Seller</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
