{{-- resources/views/staff/transactions.blade.php --}}
@extends('layouts.masterlayout')

@section('title', 'My Transactions')

@section('content')
<div class="container-fluid py-4">
    <h3 class="mb-3">My Transactions</h3>
    <p class="text-muted">A history of actions you've performed in the system.</p>

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
            <label class="form-label small mb-1">Module</label>
            <select name="module" class="form-select">
                <option value="">All Modules</option>
                <option value="Appointment" @selected(request('module') === 'Appointment')>Appointment</option>
                <option value="AppointmentResult" @selected(request('module') === 'AppointmentResult')>Appointment Result</option>
            </select>
        </div>
        <div class="col-auto">
            <label class="form-label small mb-1">Search</label>
            <input type="text" name="search" value="{{ request('search') }}" class="form-control" placeholder="Search description">
        </div>
        <div class="col-auto">
            <button class="btn btn-primary" type="submit">Filter</button>
            <a href="{{ route('staff.transactions') }}" class="btn btn-outline-secondary">Reset</a>
            <a href="{{ route('staff.transactions.print', request()->query()) }}" target="_blank" class="btn btn-outline-secondary">
                <i class="bi bi-printer"></i> Print
            </a>
        </div>
    </form>

    @include('partials.transactions_table')
</div>
@endsection