@extends('layouts.app')

@section('title', 'Admin Portal Login - Shopy')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-5">
            <div class="card shadow-sm border-0">
                <div class="card-body p-4 p-md-5">
                    <div class="text-center mb-4">
                        <div class="rounded-circle bg-dark text-white d-inline-flex align-items-center justify-content-center mb-2" style="width: 56px; height: 56px;">
                            <i class="bi bi-shield-lock-fill fs-3"></i>
                        </div>
                        <h3 class="fw-bold">Platform Admin</h3>
                        <p class="text-muted small">System administration, platform fees, seller moderation & dispute oversight</p>
                    </div>

                    <form action="{{ route('admin.login.submit') }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Admin Email</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light"><i class="bi bi-envelope"></i></span>
                                <input type="email" name="email" class="form-control" value="{{ old('email', 'admin@shopy.com') }}" required autofocus>
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
                                <input class="form-check-input" type="checkbox" name="remember" id="adminRemember" checked>
                                <label class="form-check-label small" for="adminRemember">Remember me</label>
                            </div>
                            <span class="small text-muted">Demo: admin@shopy.com / password123</span>
                        </div>

                        <button type="submit" class="btn btn-dark w-100 py-2">
                            <i class="bi bi-shield-check me-1"></i> Enter Admin Console
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
