{{-- resources/views/admin/transactions.blade.php --}}
@extends('layouts.adminlayout')

@section('title', 'My Transactions')

@section('admincontent')
<div class="container-fluid py-4">
    <h3 class="mb-3">My Transactions</h3>
    <p class="text-muted">Your own actions, separate from the full system Activity Log.</p>

    <form method="GET" class="txn-filter-bar row g-2 align-items-end">
        <div class="col-auto">
            <label class="form-label small mb-1">From</label>
            <input type="date" name="date_from" value="{{ request('date_from') }}" class="form-control">
        </div>
        <div class="col-auto">
            <label class="form-label small mb-1">To</label>
            <input type="date" name="date_to" value="{{ request('date_to') }}" class="form-control">
        </div>
        <div class="col-auto">
            <label class="form-label small mb-1">Search</label>
            <input type="text" name="search" value="{{ request('search') }}" class="form-control" placeholder="Search description">
        </div>
        <div class="col-auto">
            <button class="btn btn-primary" type="submit">Filter</button>
            <a href="{{ route('admin.transactions') }}" class="btn btn-outline-secondary">Reset</a>
            <a href="{{ route('admin.transactions.print', request()->query()) }}" target="_blank" class="btn btn-outline-secondary">
                <i class="bi bi-printer"></i> Print
            </a>
        </div>
    </form>

    @include('partials.transactions_table')
</div>
@endsection