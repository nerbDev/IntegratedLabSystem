@extends('layouts.masterlayout')

@section('title', 'Appointment Requests')

@section('content')
<style>
    /* Glassmorphism Card Style */
    .request-card {
        background: rgba(255, 255, 255, 0.08);
        backdrop-filter: blur(12px);
        border: 1px solid rgba(255, 255, 255, 0.15);
        border-radius: 15px;
        padding: 15px 25px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 15px;
        transition: 0.3s ease;
    }

    .request-card:hover {
        background: rgba(255, 255, 255, 0.12);
        transform: translateX(5px);
        border-color: rgba(0, 212, 255, 0.4);
    }

    .patient-name {
        font-size: 1.1rem;
        font-weight: 500;
        color: #fff;
        margin: 0;
    }

    .btn-view-request {
        background: rgba(0, 212, 255, 0.15);
        color: #00d4ff;
        border: 1px solid rgba(0, 212, 255, 0.4);
        border-radius: 8px;
        padding: 8px 20px;
        font-size: 0.9rem;
        transition: 0.3s;
    }

    .btn-view-request:hover {
        background: #00d4ff;
        color: #000;
    }

    /* Modal Styling */
    .modal-glass {
        background: rgba(20, 20, 20, 0.95);
        backdrop-filter: blur(25px);
        border: 1px solid rgba(255, 255, 255, 0.1);
        color: #fff;
        border-radius: 20px;
    }

    .section-title {
        color: #00d4ff;
        font-size: 0.9rem;
        font-weight: bold;
        text-transform: uppercase;
        margin-bottom: 15px;
        border-left: 3px solid #00d4ff;
        padding-left: 10px;
    }

    .detail-label { 
        color: rgba(255, 255, 255, 0.5); 
        font-size: 0.7rem; 
        text-transform: uppercase; 
        letter-spacing: 1px; 
    }
    
    .detail-value { 
        color: #fff; 
        font-size: 0.95rem; 
        margin-bottom: 15px; 
        border-bottom: 1px solid rgba(255, 255, 255, 0.05); 
        padding-bottom: 5px; 
    }

    .landmark-badge {
        background: rgba(0, 212, 255, 0.1);
        border: 1px dashed #00d4ff;
        padding: 10px;
        border-radius: 10px;
        color: #00d4ff;
        font-size: 0.9rem;
    }
</style>

<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4><i class="bi bi-envelope-paper me-2"></i> Pending Requests</h4>
        <span class="badge rounded-pill" style="background: rgba(0, 212, 255, 0.2); color: #00d4ff; border: 1px solid #00d4ff;">
            {{ count($appointments) }} New
        </span>
    </div>

    @forelse($appointments as $app)
        @php
            $useraccount = $app->user; 
        @endphp

        <div class="request-card">
            <div class="d-flex align-items-center">
                <div class="rounded-circle d-flex justify-content-center align-items-center me-3" 
                     style="width: 45px; height: 45px; background: rgba(0, 212, 255, 0.15); border: 1px solid rgba(0, 212, 255, 0.3);">
                    <i class="bi bi-person-fill text-info" style="font-size: 1.2rem;"></i>
                </div>
                <div>
                    <p class="patient-name">
                        {{ $useraccount?->first_name ?? 'Unknown' }} {{ $useraccount?->last_name ?? 'User' }}
                    </p>
                    <small class="text-muted">{{ $app->municipality }} - {{ $app->barangay }}</small>
                </div>
            </div>
            
            <button class="btn btn-view-request" data-bs-toggle="modal" data-bs-target="#requestModal{{ $app->id }}">
                View Details
            </button>
        </div>

        <div class="modal fade" id="requestModal{{ $app->id }}" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content modal-glass">
                    <div class="modal-header px-4 pt-4 border-0">
                        <h5 class="modal-title"><i class="bi bi-file-earmark-person me-2"></i> Request Review</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>

                    <div class="modal-body px-4 pb-4">
                        @if($useraccount)
                            <div class="section-title">Patient Profile</div>
                            <div class="row mb-3">
                                <div class="col-md-4">
                                    <div class="detail-label">Full Name</div>
                                    <div class="detail-value">{{ $useraccount->first_name }} {{ $useraccount->middle_name }} {{ $useraccount->last_name }}</div>
                                </div>
                                <div class="col-md-4">
                                    <div class="detail-label">Phone Number</div>
                                    <div class="detail-value">
                                        <a href="tel:{{ $app->phone ?? $useraccount->phone_number }}" class="text-info text-decoration-none">
                                            <i class="bi bi-telephone me-1"></i> {{ $app->phone ?? $useraccount->phone_number }}
                                        </a>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="detail-label">Sex / Birthdate</div>
                                    <div class="detail-value">
                                        {{ ucfirst($useraccount->sex ?? 'N/A') }} | {{ $useraccount->date_of_birth ? \Carbon\Carbon::parse($useraccount->date_of_birth)->format('M d, Y') : 'N/A' }}
                                    </div>
                                </div>
                            </div>

                            <div class="section-title">Appointment Details</div>
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <div class="detail-label">Service Type</div>
                                    <div class="detail-value">
                                        <span class="badge {{ $app->appointment_type == 'Home Service' ? 'bg-warning text-dark' : 'bg-info text-dark' }}">
                                            {{ $app->appointment_type ?? 'N/A' }}
                                        </span>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="detail-label">Schedule</div>
                                    <div class="detail-value">
                                        {{ \Carbon\Carbon::parse($app->appointment_date)->format('M d, Y') }} at {{ \Carbon\Carbon::parse($app->appointment_time)->format('h:i A') }}
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="detail-label">Package/Services Requested</div>
                                    <div class="detail-value">{{ $app->service }}</div>
                                </div>
                            </div>

                            <div class="section-title">Location & Landmark</div>
                            <div class="row mb-3">
                                <div class="col-md-12">
                                    <div class="detail-label">Full Address</div>
                                    <div class="detail-value">
                                        {{ $app->street_details }}, Brgy. {{ $app->barangay }}, {{ $app->municipality }}
                                        <a href="https://www.google.com/maps/search/?api=1&query={{ urlencode($app->street_details . ' ' . $app->barangay . ' ' . $app->municipality) }}" 
                                           target="_blank" class="ms-2 btn btn-sm btn-outline-info" style="font-size: 0.7rem;">
                                            <i class="bi bi-geo-alt"></i> Open Maps
                                        </a>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="detail-label">Nearby Landmark</div>
                                    <div class="landmark-badge">
                                        <i class="bi bi-pin-map-fill me-2"></i> {{ $app->landmark ?? 'No landmark provided' }}
                                    </div>
                                </div>
                            </div>

                        @else
                            <div class="text-center py-5">
                                <i class="bi bi-exclamation-triangle text-warning fs-1"></i>
                                <p class="mt-3">Account details missing for User ID: {{ $app->patient_id }}</p>
                            </div>
                        @endif
                        
                        <div class="d-flex gap-2 mt-4">
                            <button type="button" class="btn btn-outline-light w-100 py-2" data-bs-dismiss="modal" style="border-radius:10px;">
                                Close
                            </button>
                            
                        <a href="{{ route('appointments.manage', $app->id) }}" 
                        class="btn btn-info w-100 py-2 text-dark fw-bold" 
                        style="border-radius:10px; background: #00d4ff; border: none;">
                            Go to Management Panel
                        </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @empty
        <div class="text-center py-5" style="background: rgba(255,255,255,0.05); border-radius: 15px; border: 1px dashed rgba(255,255,255,0.1);">
            <i class="bi bi-check2-circle text-success" style="font-size: 3rem;"></i>
            <h5 class="text-white mt-3">All caught up!</h5>
            <p class="text-muted">No pending appointment requests at the moment.</p>
        </div>
    @endforelse
</div>
@endsection