@extends('layouts.masterlayout')

@section('title', 'Manage Appointment')

@section('content')
<style>
    /* Glassmorphism Styles */
    .management-card {
        background: rgba(255, 255, 255, 0.08);
        backdrop-filter: blur(12px);
        border: 1px solid rgba(255, 255, 255, 0.15);
        border-radius: 15px;
        padding: 30px;
        margin-bottom: 15px;
    }

    .section-title {
        color: #00d4ff;
        font-size: 0.85rem;
        font-weight: bold;
        text-transform: uppercase;
        letter-spacing: 1.2px;
        margin-bottom: 15px;
        border-left: 3px solid #00d4ff;
        padding-left: 10px;
    }

    /* Form Controls */
    .staff-input {
        background: rgba(255, 255, 255, 0.05) !important;
        border: 1px solid rgba(255, 255, 255, 0.2) !important;
        color: #fff !important;
        border-radius: 8px;
    }
    
    .staff-input:focus {
        border-color: #00d4ff !important;
        box-shadow: 0 0 0 0.25rem rgba(0, 212, 255, 0.1) !important;
    }

    .action-btn {
        border-radius: 10px;
        padding: 12px;
        font-weight: 600;
        transition: 0.2s;
    }

    .detail-label { 
        color: rgba(255, 255, 255, 0.5); 
        font-size: 0.7rem; 
        text-transform: uppercase; 
        margin-bottom: 5px;
        display: block;
    }

    .detail-value {
        color: #fff;
        font-size: 1rem;
        margin-bottom: 15px;
    }
</style>

<div class="container py-4">
    <div class="mb-4">
        <a href="{{ route('appointments.requests') }}" class="text-info text-decoration-none">
            <i class="bi bi-arrow-left me-1"></i> Back to Pending Requests
        </a>
    </div>

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-0"><i class="bi bi-calendar-check me-2"></i> Appointment Manager</h4>
            <p class="text-muted small mb-0">Reviewing request for: <strong>{{ $appointment->user?->first_name }} {{ $appointment->user?->last_name }}</strong></p>
        </div>
        <div class="text-end">
            <span class="badge rounded-pill bg-dark border border-secondary px-3 py-2">
                Reference ID: #{{ $appointment->id }}
            </span>
        </div>
    </div>

    <div class="management-card">
        <form action="{{ route('appointments.update', $appointment->id) }}" method="POST">
            @csrf
            @method('PUT')

            <input type="hidden" name="appointment_type" value="{{ $appointment->appointment_type }}">
            
            <div class="row">
                <div class="col-md-5 border-end border-secondary pe-md-4">
                    <div class="section-title">Patient Details</div>
                    
                    <label class="detail-label">Full Name</label>
                    <div class="detail-value">{{ $appointment->user?->first_name }} {{ $appointment->user?->last_name }}</div>

                    <label class="detail-label">Contact Number</label>
                    <div class="detail-value text-info">{{ $appointment->phone }}</div>

                    <div class="section-title mt-4">Service & Location</div>
                    
                    <label class="detail-label">Requested Service</label>
                    <div class="detail-value">
                        <span class="text-info fw-bold">{{ $appointment->service }}</span> 
                        <br><small class="text-muted">Type: {{ $appointment->appointment_type }}</small>
                    </div>

                    <label class="detail-label">Address</label>
                    <div class="detail-value small">
                        {{ $appointment->street_details }}, {{ $appointment->barangay }}, {{ $appointment->municipality }}
                    </div>
                    
                    <div class="p-3 rounded bg-dark border border-secondary">
                        <label class="detail-label text-warning">Landmark</label>
                        <p class="mb-0 small text-white">{{ $appointment->landmark ?? 'No landmark specified' }}</p>
                    </div>
                </div>

                <div class="col-md-7 ps-md-4">
                    <div class="section-title">Update Schedule & Status</div>
                    
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="detail-label">Appointment Date</label>
                            {{-- Formatted for YYYY-MM-DD to ensure it displays in date picker --}}
                            <input type="date" name="appointment_date" class="form-control staff-input" 
                                value="{{ \Carbon\Carbon::parse($appointment->appointment_date)->format('Y-m-d') }}">
                        </div>
                        <div class="col-md-6">
                            <label class="detail-label">Appointment Time</label>
                            {{-- Formatted to H:i (24hr) for the time input --}}
                            <input type="time" name="appointment_time" class="form-control staff-input" 
                                value="{{ \Carbon\Carbon::parse($appointment->appointment_time)->format('H:i') }}">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="detail-label">Staff Notes (Visible to Patient)</label>
                        <textarea name="notes" class="form-control staff-input" rows="4" placeholder="Add instructions or reason for rescheduling...">{{ $appointment->notes }}</textarea>
                    </div>

                    <div class="mb-4">
                        <label class="detail-label">Appointment Status</label>
                        <select name="status" class="form-select staff-input">
                            <option value="pending" {{ $appointment->status == 'pending' ? 'selected' : '' }}>Pending Request</option>
                            <option value="approved" {{ $appointment->status == 'approved' ? 'selected' : '' }}>Approve Appointment</option>
                            <option value="rescheduled" {{ $appointment->status == 'rescheduled' ? 'selected' : '' }}>Mark as Rescheduled</option>
                            <option value="cancelled" {{ $appointment->status == 'cancelled' ? 'selected' : '' }}>Cancel Appointment</option>
                        </select>
                    </div>

                    <div class="d-grid">
                        <button type="submit" class="btn btn-info action-btn text-dark">
                            <i class="bi bi-save2-fill me-2"></i> Save Changes & Notify Patient
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection