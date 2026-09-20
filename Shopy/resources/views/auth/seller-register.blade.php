@extends('layouts.app')

@section('title', 'Seller Registration - Shopy')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-7">
            <div class="card shadow-sm border-0">
                <div class="card-body p-4 p-md-5">
                    <div class="text-center mb-4">
                        <div class="rounded-circle bg-primary text-white d-inline-flex align-items-center justify-content-center mb-2" style="width: 56px; height: 56px;">
                            <i class="bi bi-briefcase-fill fs-3"></i>
                        </div>
                        <h3 class="fw-bold">Register as a Seller</h3>
                        <p class="text-muted small">List products and local services, negotiate deals, and grow your local presence</p>
                    </div>

                    <form action="{{ route('seller.register.submit') }}" method="POST">
                        @csrf
                        <div class="row g-3">
                            <div class="col-md-12">
                                <label class="form-label fw-semibold">Business / Seller Name <span class="text-danger">*</span></label>
                                <input type="text" name="seller_name" class="form-control" placeholder="e.g. Acme Tech Solutions" value="{{ old('seller_name') }}" required>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Business Email <span class="text-danger">*</span></label>
                                <input type="email" name="email" class="form-control" value="{{ old('email') }}" required>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Contact Phone Number <span class="text-danger">*</span></label>
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
                                <label class="form-label fw-semibold">Shop / Warehouse / Office Address <span class="text-danger">*</span></label>
                                <textarea name="address" class="form-control" rows="2" placeholder="Street, Unit / Shop No., Landmark" required>{{ old('address') }}</textarea>
                                <div class="form-text small">This address will be shared with customers for item collection or service fulfillment once a deal is made.</div>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Primary Base City <span class="text-danger">*</span></label>
                                <input type="text" name="city" class="form-control" placeholder="e.g. Mumbai" value="{{ old('city') }}" required>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold">State <span class="text-danger">*</span></label>
                                <input type="text" name="state" class="form-control" placeholder="e.g. Maharashtra" value="{{ old('state') }}" required>
                            </div>

                            <div class="col-12">
                                <label class="form-label fw-semibold">Service-Providing Cities (Optional)</label>
                                <input type="text" name="service_cities" class="form-control" placeholder="e.g. Pune, Thane, Navi Mumbai (comma separated)" value="{{ old('service_cities') }}">
                                <div class="form-text small">If you offer on-site services, enter other cities where you can deliver services. You can update these anytime.</div>
                            </div>
                        </div>

                        <div class="mt-4">
                            <button type="submit" class="btn btn-primary w-100 py-2">
                                <i class="bi bi-shop me-1"></i> Register Seller Account
                            </button>
                        </div>

                        <div class="text-center mt-3 small text-muted">
                            Already registered as a seller? <a href="{{ route('seller.login') }}" class="text-primary fw-semibold">Sign in here</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
var validationRules = {
    seller_name: {
        required: true,
        textType: 'textWithSpecial',
        minLength: 3,
        maxLength: 150,
        message: 'Business/Seller name is required (min 3 characters).',
    },
    email: {
        required: true,
        pattern: /^[^\s@]+@[^\s@]+\.[^\s@]+$/,
        message: 'Please enter a valid business email address.',
    },
    contact_no: {
        required: true,
        minLength: 7,
        maxLength: 20,
        pattern: /^[\+0-9\s\-\(\)]{7,20}$/,
        message: 'Enter a valid phone number.',
    },
    password: {
        required: true,
        minLength: 6,
        maxLength: 100,
        notAllowSpace: true,
        message: 'Password must be at least 6 characters.',
    },
    password_confirmation: {
        required: true,
        minLength: 6,
        notAllowSpace: true,
        message: 'Please confirm your password.',
    },
    address: {
        required: true,
        minLength: 10,
        maxLength: 500,
        message: 'Shop/office address must be at least 10 characters.',
    },
    city: {
        required: true,
        minLength: 2,
        maxLength: 100,
        message: 'Primary base city is required.',
    },
    state: {
        required: true,
        minLength: 2,
        maxLength: 100,
        message: 'State is required.',
    },
    service_cities: {
        required: false,
        maxLength: 500,
    },
};
setupFormValidation('form', function () {
    return validatePasswordMatch('password', 'password_confirmation');
});
</script>
@endsection
