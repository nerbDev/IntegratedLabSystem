{{-- resources/views/patient-profile/transactions.blade.php --}}
@extends('layouts.masterlayout')

@section('title', 'My Transaction History')

@section('content')
<div class="container-fluid py-4">
    <h3 class="mb-3">My Transaction History</h3>
    <p class="text-muted">Includes actions you took (bookings, downloads) and updates made to your appointments.</p>

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
            <button class="btn btn-primary" type="submit">Filter</button>
            <a href="{{ route('patient.transactions') }}" class="btn btn-outline-secondary">Reset</a>
        </div>
    </form>

    @include('partials.transactions_table')
</div>
@endsection