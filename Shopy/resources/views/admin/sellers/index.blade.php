@extends('layouts.admin')

@section('title', 'Sellers Management - Admin')
@section('page_title', 'Sellers Management')

@section('content')
<div class="card border-0 shadow-sm overflow-hidden">
    <div class="card-header bg-white py-3">
        <h6 class="fw-bold mb-0">Registered Sellers (sk_seller_master)</h6>
    </div>
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th class="ps-4">Seller ID</th>
                    <th>Business Name</th>
                    <th>Contact / Email</th>
                    <th>City & State</th>
                    <th>Listings</th>
                    <th>Deals (Arch/Mast)</th>
                    <th>Status</th>
                    <th class="text-end pe-4">Change Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse($sellers as $seller)
                    <tr>
                        <td class="ps-4 font-monospace small text-primary fw-semibold">
                            {{ $seller->seller_unique_no }}
                        </td>
                        <td>
                            <div class="fw-bold">{{ $seller->seller_name }}</div>
                            <small class="text-muted">Joined {{ $seller->created_at->format('M d, Y') }}</small>
                        </td>
                        <td>
                            <div>{{ $seller->email }}</div>
                            <small class="text-muted">{{ $seller->contact_no }}</small>
                        </td>
                        <td>{{ $seller->city }}, {{ $seller->state }}</td>
                        <td><span class="badge bg-light text-dark border">{{ $seller->products_count }}</span></td>
                        <td>
                            <span class="badge bg-warning-subtle text-dark border">{{ $seller->active_deals_count }} active</span>
                            <span class="badge bg-success-subtle text-success border">{{ $seller->completed_deals_count }} done</span>
                        </td>
                        <td>
                            @if($seller->account_status === 'active')
                                <span class="badge bg-success">Active</span>
                            @elseif($seller->account_status === 'suspended')
                                <span class="badge bg-warning text-dark">Suspended</span>
                            @else
                                <span class="badge bg-danger">Deactivated</span>
                            @endif
                        </td>
                        <td class="text-end pe-4">
                            <form action="{{ route('admin.sellers.status', $seller->seller_id_pk) }}" method="POST" class="d-inline-flex gap-1">
                                @csrf
                                <select name="status" class="form-select form-select-sm" style="width: 120px;" onchange="this.form.submit()">
                                    <option value="active" {{ $seller->account_status === 'active' ? 'selected' : '' }}>Active</option>
                                    <option value="suspended" {{ $seller->account_status === 'suspended' ? 'selected' : '' }}>Suspend</option>
                                    <option value="deactivated" {{ $seller->account_status === 'deactivated' ? 'selected' : '' }}>Deactivate</option>
                                </select>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="text-center py-4 text-muted">No sellers found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($sellers->hasPages())
        <div class="card-footer bg-white border-0 py-3 d-flex justify-content-center">
            {{ $sellers->links('pagination::bootstrap-5') }}
        </div>
    @endif
</div>
@endsection
