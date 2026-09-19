@extends('layouts.admin')

@section('title', 'Platform Settings - Admin')
@section('page_title', 'Platform Settings & Fee Policy')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-7">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white py-3 border-bottom">
                <h5 class="fw-bold mb-0">Platform Fee Configuration</h5>
                <small class="text-muted">Centralized fee calculation applied to seller earnings on deal completion</small>
            </div>
            <div class="card-body p-4">
                <form action="{{ route('admin.settings.update') }}" method="POST">
                    @csrf

                    <div class="mb-4">
                        <label class="form-label fw-bold">Platform Fee Percentage (%) <span class="text-danger">*</span></label>
                        <div class="input-group input-group-lg">
                            <input type="number" step="0.01" min="0" max="50" name="platform_fee_percent" class="form-control fw-bold" value="{{ old('platform_fee_percent', $feePercent) }}" required>
                            <span class="input-group-text bg-light fw-bold">%</span>
                        </div>
                        <div class="form-text small mt-2">
                            <strong>Policy Note:</strong> The seller pays this platform fee. The customer pays the agreed deal amount. The fee is deducted from the seller's earnings when the deal completes. Historical deals retain their snapshot fee percentage and are never modified.
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-semibold">Currency Symbol</label>
                        <input type="text" name="currency_symbol" class="form-control" value="{{ old('currency_symbol', $currencySymbol) }}" required>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-semibold">Platform Support Email</label>
                        <input type="email" name="contact_email" class="form-control" value="{{ old('contact_email', $contactEmail) }}" required>
                    </div>

                    <!-- Interactive Fee Calculator Preview -->
                    <div class="p-3 bg-light rounded border mb-4">
                        <h6 class="fw-bold mb-2 small text-uppercase tracking-wide text-secondary">Fee Calculation Example:</h6>
                        <div class="d-flex justify-content-between small text-muted mb-1">
                            <span>Agreed Deal Amount:</span>
                            <span class="fw-bold text-dark">₹1,000.00</span>
                        </div>
                        <div class="d-flex justify-content-between small text-danger mb-1">
                            <span>Deducted Platform Fee (<span id="previewPercent">{{ $feePercent }}</span>%):</span>
                            <strong id="previewFee">-₹{{ number_format(1000 * ((float)$feePercent) / 100, 2) }}</strong>
                        </div>
                        <div class="d-flex justify-content-between small text-success fw-bold pt-2 border-top">
                            <span>Seller Net Payout:</span>
                            <span id="previewNet">₹{{ number_format(1000 - (1000 * ((float)$feePercent) / 100), 2) }}</span>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary w-100 py-2 fw-bold">
                        <i class="bi bi-save me-1"></i> Save Platform Settings
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    $(document).ready(function () {
        $('input[name="platform_fee_percent"]').on('input', function () {
            const p = parseFloat($(this).val()) || 0;
            const fee = (1000 * p / 100).toFixed(2);
            const net = (1000 - fee).toFixed(2);
            $('#previewPercent').text(p.toFixed(2));
            $('#previewFee').text('-₹' + fee);
            $('#previewNet').text('₹' + net);
        });
    });
</script>
@endsection
