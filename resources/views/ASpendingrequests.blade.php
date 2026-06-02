@extends('layouts.adminlayout')

@section('title', 'Appointment Details')

@section('admincontent')
<style>
    .status-card {
        background: rgba(255, 255, 255, 0.05);
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.1);
        border-radius: 20px;
        padding: 25px;
        margin-bottom: 20px;
        transition: transform 0.3s ease;
    }
    .status-card:hover {
        transform: translateY(-5px);
        border-color: rgba(0, 212, 255, 0.3);
    }
    .date-badge {
        background: rgba(255, 255, 255, 0.1);
        padding: 10px 15px;
        border-radius: 12px;
        text-align: center;
        min-width: 80px;
    }
    .badge-approved { background: #198754; color: #fff; }
    .badge-rescheduled { background: #0dcaf0; color: #000; }
    
    .info-label {
        font-size: 0.7rem;
        text-transform: uppercase;
        color: rgba(0, 212, 255, 0.8);
        letter-spacing: 1px;
        font-weight: bold;
    }
    .detail-value { color: white; font-size: 0.95rem; margin-bottom: 10px; }

    .modal-content.admin-glass {
        background: rgba(15, 15, 15, 0.98);
        backdrop-filter: blur(30px);
        border: 1px solid rgba(255, 255, 255, 0.15);
        color: white;
        border-radius: 28px;
    }
    .modal-divider { border-right: 1px solid rgba(255, 255, 255, 0.1); }
    
    .form-control, .form-select {
        background: rgba(255, 255, 255, 0.05);
        border: 1px solid rgba(255, 255, 255, 0.1);
        color: white;
    }
    .form-control:focus, .form-select:focus {
        background: rgba(255, 255, 255, 0.1);
        color: white;
        border-color: #0dcaf0;
        box-shadow: none;
    }
</style>

<div class="container py-5">
    <div class="row mb-4">
        <div class="col-md-8">
            <h3 class="text-white mb-1">Operational Worklist</h3>
            <p class="text-muted small">Manage active appointments and laboratory processing</p>
        </div>
    </div>

    @if($activeAppointments->isEmpty())
        <div class="text-center py-5">
            <i class="bi bi-calendar-check text-muted" style="font-size: 4rem;"></i>
            <h5 class="text-white mt-3">No active records found</h5>
        </div>
    @else
        <h5 class="text-info mb-4"><i class="bi bi-person-gear me-2"></i>Active Appointments</h5>
        <div class="row mb-5">
            @foreach($activeAppointments as $app)
                <div class="col-12">
                    <div class="status-card">
                        <div class="row align-items-center">
                            <div class="col-md-2">
                                <div class="date-badge">
                                    <div class="text-info fw-bold small">{{ \Carbon\Carbon::parse($app->appointment_date)->format('M') }}</div>
                                    <div class="h3 text-white mb-0">{{ \Carbon\Carbon::parse($app->appointment_date)->format('d') }}</div>
                                    <div class="text-muted extra-small">{{ \Carbon\Carbon::parse($app->appointment_time)->format('h:i A') }}</div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="info-label">Patient</div>
                                <h5 class="text-white mb-0">{{ $app->first_name }} {{ $app->last_name }}</h5>
                                <small class="text-muted">{{ $app->phone }}</small>
                            </div>
                            <div class="col-md-3">
                                <div class="info-label">Service</div>
                                <div class="text-white fw-bold">{{ $app->service }}</div>
                                <span class="badge bg-info bg-opacity-25 text-info small">{{ $app->appointment_type }}</span>
                            </div>
                            <div class="col-md-2 text-md-center">
                                <div class="info-label mb-2">Status</div>
                                <span class="badge rounded-pill px-3 py-2 
                                    @if($app->status == 'approved') badge-approved
                                    @elseif($app->status == 'rescheduled') badge-rescheduled
                                    @endif">
                                    {{ strtoupper($app->status) }}
                                </span>
                            </div>
                            <div class="col-md-2 text-md-end">
                                <button class="btn btn-outline-info rounded-pill px-4" 
                                        onclick="openDetailModal({{ json_encode($app) }})">
                                    Details
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>

<!-- RESTORED: FULL SIZE GLASS MODAL -->
<div class="modal fade" id="detailModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content admin-glass">
            <form id="updateForm" method="POST" action="">
                @csrf
                @method('PUT')
                <div class="modal-header border-0 px-4 pt-4">
                    <h5 class="modal-title text-info"><i class="bi bi-person-badge me-2"></i>Appointment File: #<span id="display-id"></span></h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                
                <div class="modal-body p-4">
                    <div class="row">
                        <!-- Left Side: Patient Info -->
                        <div class="col-md-8 modal-divider pe-4">
                            <div class="row">
                                <div class="col-md-6 mb-4">
                                    <label class="info-label">Full Name</label>
                                    <div class="detail-value" id="display-name"></div>
                                    
                                    <label class="info-label">Contact Details</label>
                                    <div class="detail-value">
                                        <i class="bi bi-envelope me-2 text-info"></i><span id="display-email"></span><br>
                                        <i class="bi bi-phone me-2 text-info"></i><span id="display-phone"></span>
                                    </div>
                                </div>
                                <div class="col-md-6 mb-4">
                                    <label class="info-label">Appointment Schedule</label>
                                    <div class="detail-value">
                                        <i class="bi bi-calendar-event me-2 text-info"></i><span id="display-date"></span><br>
                                        <i class="bi bi-clock me-2 text-info"></i><span id="display-time"></span>
                                    </div>
                                    <label class="info-label">Type & Service</label>
                                    <div class="detail-value">
                                        <span id="display-type" class="badge bg-secondary me-2"></span> 
                                        <span id="display-service" class="fw-bold"></span>
                                    </div>
                                </div>
                                <div class="col-12 mb-4">
                                    <label class="info-label">Home Address</label>
                                    <div class="detail-value">
                                        <span id="display-street"></span>, Brgy. <span id="display-barangay"></span>, <span id="display-municipality"></span>
                                    </div>
                                    <label class="info-label">Landmark</label>
                                    <div class="detail-value" id="display-landmark"></div>
                                </div>
                                <div class="col-12">
                                    <label class="info-label">Previous Notes / Reason</label>
                                    <div class="detail-value p-3 rounded-4" style="background: rgba(255,255,255,0.05); min-height: 60px;">
                                        <span id="display-notes"></span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Right Side: Action Panel -->
                        <div class="col-md-4 ps-4">
                            <h6 class="text-info mb-4">Status Update</h6>
                            <div class="mb-4">
                                <label class="info-label mb-2">Current Status</label>
                                <select name="status" id="modal-status" class="form-select rounded-pill">
                                    <option value="approved">Approved</option>
                                    <option value="rescheduled">Rescheduled</option>
                                    <option value="completed">Completed (Send to Lab)</option>
                                    <option value="cancelled">Cancelled</option>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label class="info-label mb-2">Internal Notes</label>
                                <textarea name="notes" id="modal-notes" class="form-control rounded-4" rows="8" placeholder="Enter status updates..."></textarea>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer border-0 pb-4 px-4">
                    <button type="button" class="btn btn-outline-secondary rounded-pill px-4" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-info rounded-pill px-5 fw-bold text-dark shadow">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    function openDetailModal(app) {
        document.getElementById('updateForm').action = "/appointments/" + app.id;
        
        document.getElementById('display-id').innerText = app.id;
        document.getElementById('display-name').innerText = `${app.first_name} ${app.last_name}`;
        document.getElementById('display-email').innerText = app.email;
        document.getElementById('display-phone').innerText = app.phone;
        document.getElementById('display-date').innerText = app.appointment_date;
        document.getElementById('display-time').innerText = app.appointment_time;
        document.getElementById('display-type').innerText = app.appointment_type;
        document.getElementById('display-service').innerText = app.service;
        document.getElementById('display-street').innerText = app.street_details;
        document.getElementById('display-barangay').innerText = app.barangay;
        document.getElementById('display-municipality').innerText = app.municipality;
        document.getElementById('display-landmark').innerText = app.landmark || 'None';
        document.getElementById('display-notes').innerText = app.notes || 'No notes.';
        
        document.getElementById('modal-status').value = app.status;
        document.getElementById('modal-notes').value = app.notes || '';

        new bootstrap.Modal(document.getElementById('detailModal')).show();
    }
</script>
@endsection