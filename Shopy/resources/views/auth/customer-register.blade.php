@extends('layouts.app')

@section('title', 'Customer Registration - Shopy')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-7">
            <div class="card shadow-sm border-0">
                <div class="card-body p-4 p-md-5">
                    <div class="text-center mb-4">
                        <div class="rounded-circle bg-primary-subtle text-primary d-inline-flex align-items-center justify-content-center mb-2" style="width: 56px; height: 56px;">
                            <i class="bi bi-person-plus-fill fs-3"></i>
                        </div>
                        <h3 class="fw-bold">Customer Registration</h3>
                        <p class="text-muted small">Create your customer account to connect with sellers and access local deals</p>
                    </div>

                    <form action="{{ route('customer.register.submit') }}" method="POST">
                        @csrf
                        <div class="row g-3">
                            <div class="col-md-12">
                                <label class="form-label fw-semibold">Full Name <span class="text-danger">*</span></label>
                                <input type="text" name="customer_name" class="form-control" value="{{ old('customer_name') }}" required>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Email Address <span class="text-danger">*</span></label>
                                <input type="email" name="email" class="form-control" value="{{ old('email') }}" required>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Contact Number <span class="text-danger">*</span></label>
                                <input type="text" name="contact_no" class="form-control" placeholder="+91 98765 43210" value="{{ old('contact_no') }}" required>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Password <span class="text-danger">*</span></label>
                                <input type="password" name="password" class="form-control" required>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Confirm Password <span class="text-danger">*</span></label>
                                <input type="password" name="password_confirmation" class="form-control" required>
                            </div>

                            <div class="col-12">
                                <label class="form-label fw-semibold">Delivery / Collection Address <span class="text-danger">*</span></label>
                                <textarea name="address" class="form-control" rows="2" placeholder="Street, Building, Flat / Villa no." required>{{ old('address') }}</textarea>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold">City <span class="text-danger">*</span></label>
                                <input type="text" name="city" class="form-control" placeholder="e.g. Mumbai, Bengaluru" value="{{ old('city') }}" required>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold">State <span class="text-danger">*</span></label>
                                <input type="text" name="state" class="form-control" placeholder="e.g. Maharashtra, Karnataka" value="{{ old('state') }}" required>
                            </div>
                        </div>

                        <div class="mt-4">
                            <button type="submit" class="btn btn-primary w-100 py-2">
                                <i class="bi bi-check-circle me-1"></i> Register Customer Account
                            </button>
                        </div>

                        <div class="text-center mt-3 small text-muted">
                            Already have an account? <a href="{{ route('customer.login') }}" class="text-primary fw-semibold">Log in here</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
