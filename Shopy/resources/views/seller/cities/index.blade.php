@extends('layouts.seller')

@section('title', 'Service Cities - Seller Portal')
@section('page_title', 'Service Coverage Cities')

@section('content')
<div class="row g-4">
    <div class="col-md-5">
        <div class="card border-0 shadow-sm p-4">
            <h6 class="fw-bold mb-2"><i class="bi bi-plus-circle text-primary me-2"></i> Add Service Coverage City</h6>
            <p class="small text-muted mb-3">Add cities where you or your crew can travel to provide on-site services.</p>
            
            <form action="{{ route('seller.cities.add') }}" method="POST">
                @csrf
                <div class="mb-3">
                    <label class="form-label fw-semibold">City Name <span class="text-danger">*</span></label>
                    <input type="text" name="city_name" class="form-control" placeholder="e.g. Pune, Navi Mumbai, Thane" required>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold">State (Optional)</label>
                    <input type="text" name="state" class="form-control" placeholder="e.g. Maharashtra">
                </div>
                <button type="submit" class="btn btn-primary w-100 fw-bold">
                    <i class="bi bi-geo-alt me-1"></i> Add Service City
                </button>
            </form>
        </div>
    </div>

    <div class="col-md-7">
        <div class="card border-0 shadow-sm overflow-hidden">
            <div class="card-header bg-white py-3">
                <h6 class="fw-bold mb-0">Current Covered Cities ({{ $cities->count() }})</h6>
            </div>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-4">City</th>
                            <th>State</th>
                            <th class="text-end pe-4">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($cities as $city)
                            <tr>
                                <td class="ps-4 fw-semibold text-dark">
                                    <i class="bi bi-geo-alt-fill text-danger me-1"></i> {{ $city->city_name }}
                                </td>
                                <td class="text-muted small">{{ $city->state ?: '—' }}</td>
                                <td class="text-end pe-4">
                                    <form action="{{ route('seller.cities.delete', $city->city_id_pk) }}" method="POST" class="d-inline" onsubmit="return confirm('Remove this service city?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-outline-danger btn-sm">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="text-center py-4 text-muted">
                                    No additional service cities added yet.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
