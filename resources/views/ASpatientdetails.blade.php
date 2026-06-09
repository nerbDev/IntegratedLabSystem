@extends('layouts.adminlayout')

@section('title', 'Patient Directory Systems')

@section('admincontent')
<style>
    .patient-card {
        background: rgba(255, 255, 255, 0.03);
        backdrop-filter: blur(15px);
        border: 1px solid rgba(255, 255, 255, 0.08);
        border-radius: 24px;
        padding: 25px;
        margin-bottom: 20px;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }
    .patient-card:hover {
        transform: translateY(-4px);
        border-color: rgba(0, 212, 255, 0.4);
        background: rgba(255, 255, 255, 0.06);
    }
    .medical-avatar {
        background: rgba(13, 202, 240, 0.1);
        color: #0dcaf0;
        border-radius: 20px;
        width: 55px;
        height: 55px;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .info-tag {
        font-size: 0.68rem;
        text-transform: uppercase;
        color: rgba(0, 212, 255, 0.8);
        letter-spacing: 1.2px;
        font-weight: 700;
        margin-bottom: 3px;
    }
    .modal-glass {
        background: rgba(12, 12, 12, 0.99);
        backdrop-filter: blur(35px);
        border: 1px solid rgba(255, 255, 255, 0.12);
        color: #fff;
        border-radius: 32px;
    }
    .divider-line { border-right: 1px solid rgba(255, 255, 255, 0.08); }
    .history-scroll {
        max-height: 420px;
        overflow-y: auto;
        padding-right: 10px;
    }
    .history-scroll::-webkit-scrollbar { width: 5px; }
    .history-scroll::-webkit-scrollbar-thumb { background: rgba(255,255,255,0.1); border-radius: 10px; }
    
    .appointment-log-node {
        background: rgba(255,255,255,0.02);
        border: 1px solid rgba(255,255,255,0.05);
        border-radius: 16px;
        padding: 15px;
        margin-bottom: 12px;
    }
    .form-control, .form-select {
        background: rgba(255, 255, 255, 0.04);
        border: 1px solid rgba(255, 255, 255, 0.08);
        color: white;
    }
    .form-control:focus, .form-select:focus {
        background: rgba(255, 255, 255, 0.08);
        color: white;
        border-color: #0dcaf0;
        box-shadow: none;
    }
</style>

<div class="container py-5">
    <div class="row mb-4">
        <div class="col-md-8">
            <h3 class="text-white mb-1">Patient Master Registry</h3>
            <p class="text-muted small">Access complete demographic diagnostics profiles and unified clinical laboratory transaction records.</p>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success bg-success bg-opacity-25 text-white border-0 rounded-4 mb-4">
            {{ session('success') }}
        </div>
    @endif

    @if($patients->isEmpty())
        <div class="text-center py-5">
            <i class="bi bi-heart-pulse text-muted" style="font-size: 4rem;"></i>
            <h5 class="text-white mt-3">No profiles matched patient user classification matrices</h5>
        </div>
    @else
        <div class="row">
            @foreach($patients as $user)
                <div class="col-12">
                    <div class="patient-card">
                        <div class="row align-items-center">
                            <div class="col-md-1 d-flex justify-content-md-center mb-3 mb-md-0">
                                <div class="medical-avatar">
                                    <i class="bi bi-heart-pulse-fill h3 mb-0"></i>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="info-tag">Patient Full Name</div>
                                <h5 class="text-white mb-0">{{ $user->first_name }} {{ $user->last_name }}</h5>
                                <small class="text-muted">UID Profile Map: #{{ $user->id }}</small>
                            </div>
                            <div class="col-md-3">
                                <div class="info-tag">Primary Com-Link Email</div>
                                <div class="text-white text-truncate">{{ $user->email }}</div>
                                <small class="text-muted">Mobile: {{ $user->phone_number }}</small>
                            </div>
                            <div class="col-md-3">
                                <div class="info-tag">Geographical Registry Address</div>
                                <div class="text-muted text-truncate target-value" style="font-size:0.9rem;">
                                    {{ $user->Ustreet_house }}, {{ $user->Ubarangay }}, {{ $user->Umunicipality }}
                                </div>
                            </div>
                            <div class="col-md-2 text-md-end">
                                <div class="d-flex justify-content-md-end gap-2 mt-3 mt-md-0">
                                    <button class="btn btn-info rounded-pill px-4 fw-bold text-dark btn-sm" onclick="loadPatientRegistryFile({{ $user->id }})">
                                        Open File
                                    </button>
                                    <form action="{{ route('admin.patients.destroy', $user->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to permanently erase this complete patient registry file profile? This action cannot be reversed.');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-outline-danger rounded-pill btn-sm px-3">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>

<div class="modal fade" id="patientFileModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content modal-glass">
            <form id="patientRecordsUpdateForm" method="POST" action="">
                @csrf
                @method('PUT')
                <div class="modal-header border-0 px-4 pt-4">
                    <h5 class="modal-title text-info"><i class="bi bi-folder2-open me-2"></i>Unified Clinical Record Sheet: Patient #<span id="label-patient-id"></span></h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                
                <div class="modal-body p-4">
                    <div class="row">
                        <div class="col-md-6 divider-line pe-4">
                            <h6 class="text-info mb-3"><i class="bi bi-person-badge me-2"></i>Demographics Information Mapping</h6>
                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <label class="info-tag">First Name</label>
                                    <input type="text" name="first_name" id="p-first-name" class="form-control rounded-pill" required>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="info-tag">Middle Name</label>
                                    <input type="text" name="middle_name" id="p-middle-name" class="form-control rounded-pill">
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="info-tag">Last Name</label>
                                    <input type="text" name="last_name" id="p-last-name" class="form-control rounded-pill" required>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="info-tag">Date Of Birth</label>
                                    <input type="date" name="date_of_birth" id="p-dob" class="form-control rounded-pill" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="info-tag">Sex Phenotype Mapping</label>
                                    <select name="sex" id="p-sex" class="form-select rounded-pill">
                                        <option value="male">Male</option>
                                        <option value="female">Female</option>
                                    </select>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="info-tag">Secure Email Addr</label>
                                    <input type="email" name="email" id="p-email" class="form-control rounded-pill" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="info-tag">Mobile Com-Lines</label>
                                    <input type="text" name="phone_number" id="p-phone" class="form-control rounded-pill" required>
                                </div>
                            </div>

                            <h6 class="text-info mt-4 mb-3"><i class="bi bi-geo-alt me-2"></i>Geographic Location Profile Data</h6>
                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <label class="info-tag">Municipality</label>
                                    <input type="text" name="Umunicipality" id="p-municipality" class="form-control rounded-pill" required>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="info-tag">Barangay</label>
                                    <input type="text" name="Ubarangay" id="p-barangay" class="form-control rounded-pill" required>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="info-tag">Street / House</label>
                                    <input type="text" name="Ustreet_house" id="p-street" class="form-control rounded-pill" required>
                                </div>
                            </div>

                            <h6 class="text-info mt-4 mb-3"><i class="bi bi-telephone-plus me-2"></i>Emergency / Next-Of-Kin Matrix</h6>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="info-tag">Contact Person Name</label>
                                    <input type="text" name="contact_person" id="p-contact-name" class="form-control rounded-pill" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="info-tag">Contact Number</label>
                                    <input type="text" name="contact_number" id="p-contact-phone" class="form-control rounded-pill" required>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6 ps-4">
                            <h6 class="text-info mb-3"><i class="bi bi-activity me-2"></i>Dynamic Clinical Laboratory Log History</h6>
                            <div class="history-scroll" id="appointments-timeline-box">
                                </div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer border-0 pb-4 px-4">
                    <button type="button" class="btn btn-outline-secondary rounded-pill px-4" data-bs-dismiss="modal">Close Profile</button>
                    <button type="submit" class="btn btn-info rounded-pill px-5 fw-bold text-dark shadow">Commit Modifications</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    function loadPatientRegistryFile(patientId) {
        // Form endpoint dynamic assignment targeting PatientController updates
        document.getElementById('patientRecordsUpdateForm').action = "{{ url('/admin/patient-details') }}/" + patientId;
        
        // Fetch operations targeting specific ID
        fetch("{{ url('/admin/patient-details') }}/" + patientId)
            .then(response => response.json())
            .then(data => {
                const patient = data.patient;
                const appointments = data.appointments;

                // Populate form metrics securely
                document.getElementById('label-patient-id').innerText = patient.id;
                document.getElementById('p-first-name').value = patient.first_name || '';
                document.getElementById('p-middle-name').value = patient.middle_name || '';
                document.getElementById('p-last-name').value = patient.last_name || '';
                document.getElementById('p-dob').value = patient.date_of_birth || '';
                document.getElementById('p-sex').value = patient.sex || 'male';
                document.getElementById('p-email').value = patient.email || '';
                document.getElementById('p-phone').value = patient.phone_number || '';
                document.getElementById('p-municipality').value = patient.Umunicipality || '';
                document.getElementById('p-barangay').value = patient.Ubarangay || '';
                document.getElementById('p-street').value = patient.Ustreet_house || '';
                document.getElementById('p-contact-name').value = patient.contact_person || '';
                document.getElementById('p-contact-phone').value = patient.contact_number || '';

                // Processing Dynamic Appointments Panel
                const logsBox = document.getElementById('appointments-timeline-box');
                logsBox.innerHTML = ''; // Empty previous renderings

                if (appointments.length === 0) {
                    logsBox.innerHTML = `
                        <div class="text-center py-5 text-muted">
                            <i class="bi bi-calendar-x h3"></i>
                            <p class="small mt-2">Zero clinical transaction files matched this user identifier.</p>
                        </div>`;
                } else {
                    appointments.forEach(log => {
                        let statusColor = '#ffc107'; // Pending fallback
                        if(log.status === 'completed' || log.status === 'approved') statusColor = '#198754';
                        if(log.status === 'cancelled' || log.status === 'rejected') statusColor = '#dc3545';

                        logsBox.innerHTML += `
                            <div class="appointment-log-node">
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <div>
                                        <span class="badge bg-opacity-10 text-info px-2 py-1 small" style="background:rgba(13,202,240,0.15)">
                                            ID: #${log.id}
                                        </span>
                                        <h6 class="text-white mb-0 mt-1">${log.service || 'Diagnostic Run'}</h6>
                                    </div>
                                    <span class="badge rounded-pill px-3 py-1 text-uppercase text-white small" style="background:${statusColor}; font-size:0.7rem;">
                                        ${log.status}
                                    </span>
                                </div>
                                <div class="row small text-muted g-1 mt-2">
                                    <div class="col-6"><i class="bi bi-calendar3 me-1"></i> ${log.appointment_date}</div>
                                    <div class="col-6"><i class="bi bi-clock me-1"></i> ${log.appointment_time}</div>
                                    <div class="col-6"><i class="bi bi-tag me-1"></i> Type: ${log.appointment_type || 'Standard'}</div>
                                    <div class="col-6"><i class="bi bi-building me-1"></i> Branch Code: ${log.branch_id || 'Main'}</div>
                                </div>
                                <div class="mt-2 text-muted border-top border-secondary border-opacity-10 pt-2" style="font-size:0.8rem;">
                                    <strong>Patient Input:</strong> ${log.first_name} ${log.last_name} (${log.email})<br>
                                    <strong>Loc:</strong> ${log.street_details || ''}, ${log.barangay || ''}, ${log.municipality || ''}<br>
                                    <strong>Notes:</strong> <span class="text-light">${log.notes || 'None logged'}</span>
                                </div>
                            </div>`;
                    });
                }

                // Call bootstrap overlay trigger
                new bootstrap.Modal(document.getElementById('patientFileModal')).show();
            })
            .catch(error => alert('Clinical engine encountered processing anomalies parsing profile arrays.'));
    }
</script>
@endsection