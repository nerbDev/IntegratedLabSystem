@extends('layouts.masterlayout')

@section('title', 'My Appointment Requests')

@section('content')
<style>
    /* Patient Dashboard Theme */
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

    .staff-note-box {
        background: rgba(0, 212, 255, 0.05);
        border-left: 4px solid #00d4ff;
        padding: 15px;
        border-radius: 0 12px 12px 0;
        margin-top: 15px;
    }

    .date-badge {
        background: rgba(255, 255, 255, 0.1);
        padding: 10px 15px;
        border-radius: 12px;
        text-align: center;
        min-width: 80px;
    }

    .badge-pending { background: #ffc107; color: #000; }
    .badge-approved { background: #198754; color: #fff; }
    .badge-rescheduled { background: #0dcaf0; color: #000; }
    .badge-cancelled { background: #dc3545; color: #fff; }

    .info-label {
        font-size: 0.75rem;
        text-transform: uppercase;
        color: rgba(255, 255, 255, 0.5);
        letter-spacing: 1px;
    }

    /* Details Modal Styling */
    .modal-content.details-glass {
        background: rgba(25, 25, 25, 0.95);
        backdrop-filter: blur(20px);
        border: 1px solid rgba(255, 255, 255, 0.2);
        color: white;
        border-radius: 20px;
    }
    
    .modal-header, .modal-footer { border: none; }
    .detail-item { margin-bottom: 15px; }
    .detail-item label { display: block; font-size: 0.7rem; color: #00d4ff; text-transform: uppercase; font-weight: bold; }
</style>

<div class="container py-5">
    <div class="row mb-4">
        <div class="col-md-8">
            <h3 class="text-white mb-1">My Appointments</h3>
            <p class="text-muted">Track the status of your healthcare requests at SMH</p>
        </div>
        <div class="col-md-4 text-md-end">
            <a href="{{ url('/appointment') }}" class="btn btn-info px-4 rounded-pill fw-bold">
                <i class="bi bi-plus-lg me-1"></i> New Request
            </a>
        </div>
    </div>

    {{-- Filter collection to exclude 'completed' and 'released' items inside the view layer --}}
    @php
        $activeAppointments = $appointments->reject(function ($appointment) {
            return in_array(strtolower($appointment->status), ['completed', 'released']);
        });
    @endphp

    @if($activeAppointments->isEmpty())
        <div class="text-center py-5">
            <i class="bi bi-calendar-x text-muted" style="font-size: 4rem;"></i>
            <h5 class="text-white mt-3">No active appointments found</h5>
            <p class="text-muted">Your scheduled and pending requests will appear here. Released and completed results can be viewed under Laboratory Results.</p>
        </div>
    @else
        <div class="row">
            @foreach($activeAppointments as $app)
                <div class="col-12">
                    <div class="status-card">
                        <div class="row align-items-center">
                            <div class="col-md-2 mb-3 mb-md-0">
                                <div class="date-badge">
                                    <div class="text-info fw-bold small">{{ \Carbon\Carbon::parse($app->appointment_date)->format('M') }}</div>
                                    <div class="h3 text-white mb-0">{{ \Carbon\Carbon::parse($app->appointment_date)->format('d') }}</div>
                                    <div class="text-muted extra-small">{{ \Carbon\Carbon::parse($app->appointment_time)->format('h:i A') }}</div>
                                </div>
                            </div>

                            <div class="col-md-4 mb-3 mb-md-0">
                                <div class="info-label">Service Requested</div>
                                <h5 class="text-white mb-1">{{ $app->service }}</h5>
                                <span class="badge bg-secondary opacity-75">{{ $app->appointment_type }}</span>
                            </div>

                            <div class="col-md-3 mb-3 mb-md-0 text-md-center">
                                <div class="info-label mb-2">Current Status</div>
                                <span class="badge rounded-pill px-4 py-2 
                                    @if($app->status == 'pending') badge-pending 
                                    @elseif($app->status == 'approved') badge-approved
                                    @elseif($app->status == 'rescheduled') badge-rescheduled
                                    @else badge-cancelled @endif">
                                    {{ strtoupper($app->status) }}
                                </span>
                            </div>

                            <div class="col-md-3 text-md-end">
                                <div class="info-label">Reference ID</div>
                                <p class="text-white mb-2">#SMH-{{ str_pad($app->id, 5, '0', STR_PAD_LEFT) }}</p>
                                
                                {{-- Added data-file-url attribute to supply file paths to the interactive details modal --}}
                                <button class="btn btn-outline-info btn-sm rounded-pill px-3" 
                                        data-date="{{ \Carbon\Carbon::parse($app->appointment_date)->format('M d, Y') }}"
                                        data-time="{{ \Carbon\Carbon::parse($app->appointment_time)->format('h:i A') }}"
                                        data-service="{{ $app->service }}"
                                        data-type="{{ $app->appointment_type}}"
                                        data-address="{{ $app->street_details }}, Brgy. {{ $app->barangay }}, {{ $app->municipality }}"
                                        data-landmark="{{ $app->landmark ?? 'No landmark provided' }}"
                                        data-status="{{ $app->status }}"
                                        data-notes="{{ $app->notes }}" 
                                        data-file-url="{{ ($app->result && $app->result->file_path) ? asset('storage/' . $app->result->file_path) : '' }}"
                                        onclick="showAppDetails(this)">
                                    <i class="bi bi-info-circle me-1"></i> Details
                                </button>
                            </div>
                        </div>

                        @if($app->notes)
                            <div class="staff-note-box">
                                <div class="info-label text-info"><i class="bi bi-chat-left-dots-fill me-2"></i>Note from Medical Staff:</div>
                                <p class="text-white-50 mb-0 mt-1 small italic">
                                    "{{ $app->notes }}"
                                </p>
                            </div>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>

<div class="modal fade" id="detailsModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content details-glass">
            <div class="modal-header">
                <h5 class="modal-title"><i class="bi bi-file-earmark-medical me-2 text-info"></i>Appointment Summary</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <div class="row">
                    <div class="col-6 detail-item">
                        <label>Schedule</label>
                        <span id="det-schedule"></span>
                    </div>
                    <div class="col-6 detail-item">
                        <label>Status</label>
                        <span id="det-status" class="badge rounded-pill"></span>
                    </div>
                    <div class="col-12 detail-item">
                        <label>Service</label>
                        <span id="det-service" class="fw-bold"></span>
                    </div>
                    <div class="col-12 detail-item border-top border-secondary pt-3 mt-2">
                        <label>Full Address</label>
                        <span id="det-address"></span>
                    </div>
                    <div class="col-12 detail-item">
                        <label>Landmark</label>
                        <span id="det-landmark" class="text-info"></span>
                    </div>
                    
                    {{-- NEWLY ADDED: File Download Container for Released Lab Files --}}
                    <div id="det-file-section" class="col-12 mt-2 d-none">
                        <label class="mb-1 text-info fw-bold">Available Documents</label>
                        <div class="d-flex align-items-center justify-content-between p-3 rounded bg-success bg-opacity-10 border border-success border-opacity-25">
                            <div class="d-flex align-items-center gap-2">
                                <i class="bi bi-file-earmark-pdf text-danger fs-4"></i>
                                <div>
                                    <div class="small fw-bold text-white">Laboratory_Result.pdf</div>
                                    <div class="extra-small text-muted">Official Signed Copy</div>
                                </div>
                            </div>
                            <a id="det-file-download" href="#" target="_blank" class="btn btn-sm btn-success rounded-pill px-3 fw-bold">
                                <i class="bi bi-download me-1"></i> Download
                            </a>
                        </div>
                    </div>

                    {{-- THIS IS THE NOTE SECTION --}}
                    <div id="det-note-section" class="col-12 mt-3 d-none">
                        <div class="p-3 rounded bg-info bg-opacity-10 border border-info border-opacity-25">
                            <label class="text-info">Medical Staff Notes</label>
                            <p id="det-notes" class="mb-0 small italic" style="white-space: pre-wrap;"></p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary w-100 rounded-pill" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<script>
    function showAppDetails(btn) {
        // Pull values from data attributes
        const date = btn.getAttribute('data-date');
        const time = btn.getAttribute('data-time');
        const service = btn.getAttribute('data-service');
        const type = btn.getAttribute('data-type');
        const address = btn.getAttribute('data-address');
        const landmark = btn.getAttribute('data-landmark');
        const status = btn.getAttribute('data-status');
        const notes = btn.getAttribute('data-notes');
        const fileUrl = btn.getAttribute('data-file-url');

        // Set Text
        document.getElementById('det-schedule').innerText = `${date} @ ${time}`;
        document.getElementById('det-service').innerText = `${service} (${type})`;
        document.getElementById('det-address').innerText = address;
        document.getElementById('det-landmark').innerText = landmark;
        
        // Status Badge
        const statusEl = document.getElementById('det-status');
        statusEl.innerText = status.toUpperCase();
        statusEl.className = 'badge rounded-pill ' + 
            (status === 'pending' ? 'badge-pending' : 
             status === 'approved' ? 'badge-approved' : 
             status === 'rescheduled' ? 'badge-rescheduled' : 'badge-cancelled');

        // Document Delivery Logic
        const fileSection = document.getElementById('det-file-section');
        const fileDownloadLink = document.getElementById('det-file-download');
        
        if (fileUrl && fileUrl !== "" && fileUrl.trim() !== window.location.origin + "/") {
            fileSection.classList.remove('d-none');
            fileDownloadLink.setAttribute('href', fileUrl);
        } else {
            fileSection.classList.add('d-none');
            fileDownloadLink.setAttribute('href', '#');
        }

        // Staff Notes Logic
        const noteSection = document.getElementById('det-note-section');
        const noteText = document.getElementById('det-notes');
        
        // Check if notes is NOT null, NOT empty string, and NOT the literal string "null"
        if(notes && notes !== "" && notes !== "null") {
            noteSection.classList.remove('d-none');
            noteText.innerText = `"${notes}"`;
        } else {
            noteSection.classList.add('d-none');
        }

        new bootstrap.Modal(document.getElementById('detailsModal')).show();
    }
</script>
@endsection
