@extends('layouts.adminlayout')

@section('title', 'Upload Laboratory Results')

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
        border-color: rgba(0, 255, 150, 0.3);
    }
    .info-label {
        font-size: 0.7rem;
        text-transform: uppercase;
        color: rgba(0, 212, 255, 0.8);
        letter-spacing: 1px;
        font-weight: bold;
    }
    .badge-completed { background: #6610f2; color: #fff; }
    .badge-released  { background: #198754; color: #fff; }
    .admin-glass {
        background: rgba(15, 15, 15, 0.98);
        backdrop-filter: blur(30px);
        border: 1px solid rgba(255, 255, 255, 0.15);
        color: white;
        border-radius: 28px;
    }
</style>

<div class="container py-5">
    <div class="row mb-4">
        <div class="col-md-8">
            <h3 class="text-white mb-1">Upload Lab Results</h3>
            <p class="text-muted small">Manage laboratory reports and patient releases</p>
        </div>
    </div>

    {{-- TEMPORARY DEBUG: shows exactly why validation fails --}}
    @if($errors->any())
        <div class="alert alert-danger rounded-4 mb-4">
            <strong>Upload failed. Reasons:</strong>
            <ul class="mb-0 mt-2">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @if(session('success'))
        <div class="alert alert-success rounded-4 mb-4">
            <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger rounded-4 mb-4">
            <i class="bi bi-x-circle me-2"></i>{{ session('error') }}
        </div>
    @endif

    @if(!isset($completedAppointments) || $completedAppointments->isEmpty())
        <div class="text-center py-5">
            <i class="bi bi-cloud-check text-muted" style="font-size: 4rem;"></i>
            <h5 class="text-white mt-3">No results to process</h5>
            <p class="text-muted small">Appointments marked as "Completed" will appear here.</p>
        </div>
    @else
        <div class="row">
            @foreach($completedAppointments as $app)
                <div class="col-12">
                    <div class="status-card" style="border-left: 4px solid {{ $app->status == 'released' ? '#198754' : '#6610f2' }};">
                        <div class="row align-items-center">

                            <div class="col-md-3">
                                <div class="info-label">Patient</div>
                                <h5 class="text-white mb-0">{{ $app->first_name }} {{ $app->last_name }}</h5>
                                <small class="text-muted">Service: {{ $app->service }}</small>
                            </div>

                            <div class="col-md-3">
                                <div class="info-label">Completion Date</div>
                                <div class="text-white fw-bold">{{ \Carbon\Carbon::parse($app->updated_at)->format('M d, Y') }}</div>
                                <small class="text-muted">{{ \Carbon\Carbon::parse($app->updated_at)->format('h:i A') }}</small>
                            </div>

                            <div class="col-md-2 text-md-center">
                                <div class="info-label mb-2">Status</div>
                                <span class="badge rounded-pill px-3 py-2 {{ $app->status == 'released' ? 'badge-released' : 'badge-completed' }}">
                                    {{ strtoupper($app->status) }}
                                </span>
                            </div>

                            <div class="col-md-4 text-md-end">
                                @if($app->status == 'completed')
                                    <button class="btn btn-info rounded-pill px-4 me-2 fw-bold text-dark"
                                        onclick="openUploadModal({{ json_encode($app, JSON_HEX_APOS | JSON_HEX_QUOT) }})">
                                        <i class="bi bi-upload me-1"></i> Upload File
                                    </button>
                                @else
                                    <button class="btn btn-outline-success rounded-pill px-4 me-2" disabled>
                                        <i class="bi bi-file-earmark-check me-1"></i> Finalized
                                    </button>
                                    @if($app->result && $app->result->file_path)
                                        <span class="badge bg-info text-dark ms-1">
                                            <i class="bi bi-paperclip"></i> PDF Attached
                                        </span>
                                    @endif
                                @endif
                            </div>

                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>

<!-- Upload Modal -->
<div class="modal fade" id="uploadModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content admin-glass">
            <form id="uploadForm" method="POST" enctype="multipart/form-data">
                @csrf

                <div class="modal-header border-0 px-4 pt-4">
                    <h5 class="modal-title text-info">
                        <i class="bi bi-cloud-arrow-up me-2"></i>Result Attachment
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body p-4">

                    <div class="mb-4 p3 rounded-4" style="background: rgba(255,255,255,0.04); border: 1px solid rgba(255,255,255,0.08);">
                        <div class="info-label mb-1">Uploading result for</div>
                        <div class="text-white fw-bold" id="modal-patient-name">—</div>
                        <div class="text-muted small" id="modal-patient-service">—</div>
                    </div>

                    <div class="mb-4 text-center p-4 rounded-4"
                        style="background: rgba(255,255,255,0.03); border: 1px dashed rgba(255,255,255,0.2);">
                        <i class="bi bi-file-pdf text-info" style="font-size: 2.5rem;"></i>
                        <div class="mt-2 text-muted small">Select PDF file of lab results</div>
                        <input type="file" name="lab_file"
                            class="form-control mt-3 bg-dark text-white border-secondary"
                            accept=".pdf">
                    </div>

                    <div class="mb-4">
                        <label class="info-label mb-2">Final Action</label>
                        <select name="status" class="form-select bg-dark text-white border-secondary rounded-pill">
                            <option value="released">Release to Patient</option>
                            <option value="completed">Keep in Pending Upload</option>
                        </select>
                    </div>

                    <div class="mb-0">
                        <label class="info-label mb-2">Clinical Findings / Notes</label>
                        <textarea name="notes" id="modal-notes"
                            class="form-control bg-dark text-white border-secondary rounded-4"
                            rows="4" placeholder="Optional notes for this result..."></textarea>
                    </div>

                </div>

                <div class="modal-footer border-0 pb-4 px-4">
                    <button type="button" class="btn btn-outline-secondary rounded-pill px-4"
                        data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-info rounded-pill px-5 fw-bold text-dark">
                        <i class="bi bi-cloud-check me-1"></i> Save & Release
                    </button>
                </div>

            </form>
        </div>
    </div>
</div>

<script>
    function openUploadModal(app) {
        const form = document.getElementById('uploadForm');
        form.action = '/appointments/' + app.id + '/results';

        document.getElementById('modal-patient-name').textContent =
            app.first_name + ' ' + app.last_name;
        document.getElementById('modal-patient-service').textContent =
            'Service: ' + app.service;

        document.getElementById('modal-notes').value = app.notes || '';

        new bootstrap.Modal(document.getElementById('uploadModal')).show();
    }
</script>
@endsection