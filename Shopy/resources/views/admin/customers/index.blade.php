@extends('layouts.admin')

@section('title', 'Customers Management - Admin')
@section('page_title', 'Customers Management')

@section('content')
<div class="card border-0 shadow-sm overflow-hidden">
    <div class="card-header bg-white py-3">
        <h6 class="fw-bold mb-0">Registered Customers (sk_customer_master)</h6>
    </div>
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th class="ps-4">Customer ID</th>
                    <th>Customer Name</th>
                    <th>Email / Phone</th>
                    <th>City & State</th>
                    <th>Active Deals</th>
                    <th>Completed Deals</th>
                    <th>Status</th>
                    <th class="text-end pe-4">Change Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse($customers as $customer)
                    <tr>
                        <td class="ps-4 font-monospace small text-primary fw-semibold">
                            {{ $customer->customer_unique_no }}
                        </td>
                        <td>
                            <div class="fw-bold">{{ $customer->customer_name }}</div>
                            <small class="text-muted">Registered {{ $customer->created_at->format('M d, Y') }}</small>
                        </td>
                        <td>
                            <div>{{ $customer->email }}</div>
                            <small class="text-muted">{{ $customer->contact_no }}</small>
                        </td>
                        <td>{{ $customer->city }}, {{ $customer->state }}</td>
                        <td><span class="badge bg-warning-subtle text-dark border">{{ $customer->active_deals_count }}</span></td>
                        <td><span class="badge bg-success-subtle text-success border">{{ $customer->completed_deals_count }}</span></td>
                        <td>
                            @if($customer->account_status === 'active')
                                <span class="badge bg-success">Active</span>
                            @elseif($customer->account_status === 'suspended')
                                <span class="badge bg-warning text-dark">Suspended</span>
                            @else
                                <span class="badge bg-danger">Deactivated</span>
                            @endif
                        </td>
                        <td class="text-end pe-4">
                            <form action="{{ route('admin.customers.status', $customer->customer_id_pk) }}" method="POST" class="d-inline-flex gap-1">
                                @csrf
                                <select name="status" class="form-select form-select-sm" style="width: 120px;" onchange="this.form.submit()">
                                    <option value="active" {{ $customer->account_status === 'active' ? 'selected' : '' }}>Active</option>
                                    <option value="suspended" {{ $customer->account_status === 'suspended' ? 'selected' : '' }}>Suspend</option>
                                    <option value="deactivated" {{ $customer->account_status === 'deactivated' ? 'selected' : '' }}>Deactivate</option>
                                </select>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="text-center py-4 text-muted">No customers registered.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($customers->hasPages())
        <div class="card-footer bg-white border-0 py-3 d-flex justify-content-center">
            {{ $customers->links('pagination::bootstrap-5') }}
        </div>
    @endif
</div>
@endsection
