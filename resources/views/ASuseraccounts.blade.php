@extends('layouts.adminlayout')

@section('title', 'User Accounts Management')

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
    .user-avatar-badge {
        background: rgba(255, 255, 255, 0.1);
        padding: 15px;
        border-radius: 50%;
        text-align: center;
        width: 60px;
        height: 60px;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .badge-admin { background: #dc3545; color: #fff; }
    .badge-staff { background: #0dcaf0; color: #000; }
    .badge-patient { background: #198754; color: #fff; }
    
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
            <h3 class="text-white mb-1">User Account Registry</h3>
            <p class="text-muted small">Manage authentication profiles, system operational security roles, and user data fields.</p>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success bg-success bg-opacity-25 text-white border-0 rounded-4 mb-4">
            {{ session('success') }}
        </div>
    @endif
    
    @if(session('error'))
        <div class="alert alert-danger bg-danger bg-opacity-25 text-white border-0 rounded-4 mb-4">
            {{ session('error') }}
        </div>
    @endif

    @if($users->isEmpty())
        <div class="text-center py-5">
            <i class="bi bi-people text-muted" style="font-size: 4rem;"></i>
            <h5 class="text-white mt-3">No registered profiles inside table records</h5>
        </div>
    @else
        <h5 class="text-info mb-4"><i class="bi bi-person-lines-fill me-2"></i>Active Registered Profiles</h5>
        <div class="row mb-5">
            @foreach($users as $user)
                <div class="col-12">
                    <div class="status-card">
                        <div class="row align-items-center">
                            <div class="col-md-1 d-flex justify-content-md-center mb-3 mb-md-0">
                                <div class="user-avatar-badge text-info">
                                    <i class="bi bi-person-bounding-box h4 mb-0"></i>
                                </div>
                            </div>
                            
                            <div class="col-md-3">
                                <div class="info-label">Account Owner</div>
                                <h5 class="text-white mb-0">
                                    {{ $user->first_name ?? 'No First Name' }} {{ $user->last_name ?? '' }}
                                </h5>
                                <small class="text-muted">Contact: {{ $user->phone_number ?? 'N/A' }}</small>
                            </div>
                            
                            <div class="col-md-3">
                                <div class="info-label">Email Access Destination</div>
                                <div class="text-white text-truncate">{{ $user->email }}</div>
                                <small class="text-muted">DOB: {{ $user->date_of_birth ?? 'N/A' }}</small>
                            </div>
                            
                            <div class="col-md-3 text-md-center">
                                <div class="info-label mb-2">System Access Layer</div>
                                <span class="badge rounded-pill px-3 py-2 
                                    @if($user->role == 'admin') badge-admin
                                    @elseif($user->role == 'staff') badge-staff
                                    @elseif($user->role == 'patient') badge-patient
                                    @endif">
                                    {{ strtoupper($user->role) }}
                                </span>
                            </div>
                            
                            <div class="col-md-2 text-md-end">
                                <div class="d-flex justify-content-md-end gap-2 mt-3 mt-md-0">
                                    <button class="btn btn-outline-info rounded-pill px-4" 
                                            onclick="openUserModal({{ json_encode($user) }})">
                                        Details
                                    </button>
                                    <form action="{{ route('admin.users.destroy', $user->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to completely delete this user profile? This action is irreversible.');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-outline-danger rounded-pill px-3">
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

<div class="modal fade" id="userAccountModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content admin-glass">
            <form id="userAccountUpdateForm" method="POST" action="">
                @csrf
                @method('PUT')
                <div class="modal-header border-0 px-4 pt-4">
                    <h5 class="modal-title text-info"><i class="bi bi-shield-check me-2"></i>System Account Registry Profile: #<span id="display-user-id"></span></h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                
                <div class="modal-body p-4">
                    <div class="row">
                        <div class="col-md-8 modal-divider pe-4">
                            <h6 class="text-info mb-3">Personal Profile Information</h6>
                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <label class="info-label mb-2">First Name</label>
                                    <input type="text" name="first_name" id="modal-first-name" class="form-control rounded-pill" required>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="info-label mb-2">Middle Name</label>
                                    <input type="text" name="middle_name" id="modal-middle-name" class="form-control rounded-pill">
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="info-label mb-2">Last Name</label>
                                    <input type="text" name="last_name" id="modal-last-name" class="form-control rounded-pill" required>
                                </div>
                            </div>

                            <div class="row mt-2">
                                <div class="col-md-6 mb-3">
                                    <label class="info-label mb-2">Email Address</label>
                                    <input type="email" name="email" id="modal-email" class="form-control rounded-pill" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="info-label mb-2">Phone Number</label>
                                    <input type="text" name="phone_number" id="modal-phone" class="form-control rounded-pill" required>
                                </div>
                            </div>

                            <h6 class="text-info mt-4 mb-3">Geographic Address Info</h6>
                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <label class="info-label mb-2">Municipality</label>
                                    <input type="text" name="Umunicipality" id="modal-municipality" class="form-control rounded-pill" required>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="info-label mb-2">Barangay</label>
                                    <input type="text" name="Ubarangay" id="modal-barangay" class="form-control rounded-pill" required>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="info-label mb-2">Street / House Details</label>
                                    <input type="text" name="Ustreet_house" id="modal-street" class="form-control rounded-pill" required>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-4 ps-4">
                            <h6 class="text-info mb-4">Security Access Roles</h6>
                            <div class="mb-4">
                                <label class="info-label mb-2">Assigned Authorization Tier</label>
                                <select name="role" id="modal-role" class="form-select rounded-pill">
                                    <option value="patient">Patient Access Profile</option>
                                    <option value="staff">Staff/Operator Profile</option>
                                    <option value="admin">System Administrator</option>
                                </select>
                            </div>

                            <h6 class="text-info mt-4 mb-3">Emergency / Next-of-Kin Contacts</h6>
                            <div class="mb-3">
                                <label class="info-label mb-2">Contact Person Name</label>
                                <input type="text" name="contact_person" id="modal-contact-person" class="form-control rounded-pill" required>
                            </div>
                            <div class="mb-3">
                                <label class="info-label mb-2">Contact Person Phone</label>
                                <input type="text" name="contact_number" id="modal-contact-number" class="form-control rounded-pill" required>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer border-0 pb-4 px-4">
                    <button type="button" class="btn btn-outline-secondary rounded-pill px-4" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-info rounded-pill px-5 fw-bold text-dark shadow">Save Profile Changes</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    function openUserModal(user) {
        // FIXED 404 URL BUG HERE 🔥 Dynamically assigns clean URL routing endpoints
        document.getElementById('userAccountUpdateForm').action = "{{ url('/admin/user-accounts') }}/" + user.id;
        
        document.getElementById('display-user-id').innerText = user.id;
        
        document.getElementById('modal-first-name').value = user.first_name || '';
        document.getElementById('modal-middle-name').value = user.middle_name || '';
        document.getElementById('modal-last-name').value = user.last_name || '';
        document.getElementById('modal-email').value = user.email || '';
        document.getElementById('modal-phone').value = user.phone_number || '';
        document.getElementById('modal-municipality').value = user.Umunicipality || '';
        document.getElementById('modal-barangay').value = user.Ubarangay || '';
        document.getElementById('modal-street').value = user.Ustreet_house || '';
        document.getElementById('modal-role').value = user.role || 'patient';
        document.getElementById('modal-contact-person').value = user.contact_person || '';
        document.getElementById('modal-contact-number').value = user.contact_number || '';

        new bootstrap.Modal(document.getElementById('userAccountModal')).show();
    }
</script>
@endsection