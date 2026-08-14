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
    <div class="row mb-4 align-items-center">
        <div class="col-md-8">
            <h3 class="text-white mb-1">User Account Registry</h3>
            <p class="text-muted small">Manage authentication profiles, system operational security roles, and user data fields.</p>
        </div>
        <div class="col-md-4 text-md-end">
            <button type="button" class="btn btn-info rounded-pill px-4 fw-bold text-dark shadow"
                    data-bs-toggle="modal" data-bs-target="#createUserAccountModal">
                <i class="bi bi-person-plus-fill me-1"></i> Create Account
            </button>
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

    @if($errors->any())
        <div class="alert alert-danger bg-danger bg-opacity-25 text-white border-0 rounded-4 mb-4">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @if($users->isEmpty())
        <div class="text-center py-5">
            <i class="bi bi-people text-muted" style="font-size: 4rem;"></i>
            <h5 class="text-white mt-3">No registered profiles inside table records</h5>
        </div>
    @else
        <div class="row mb-4">
            <div class="col-md-6">
                <div class="input-group">
                    <span class="input-group-text bg-transparent border-end-0" style="border: 1px solid rgba(255,255,255,0.1); color: rgba(0,212,255,0.8);">
                        <i class="bi bi-search"></i>
                    </span>
                    <input type="text" id="userSearchInput" class="form-control border-start-0"
                           placeholder="Search by name, email, or role..."
                           style="background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.1); border-left: none; color: white;"
                           onkeyup="filterUserAccounts()">
                </div>
            </div>
        </div>

        <h5 class="text-info mb-4"><i class="bi bi-person-lines-fill me-2"></i>Active Registered Profiles</h5>
        <div class="row mb-5">
            @foreach($users as $user)
                <div class="col-12 user-account-row"
                     data-search="{{ strtolower($user->first_name . ' ' . $user->middle_name . ' ' . $user->last_name . ' ' . $user->email . ' ' . $user->role) }}">
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

        <div id="noSearchResults" class="text-center py-5" style="display:none;">
            <i class="bi bi-search text-muted" style="font-size: 3rem;"></i>
            <h6 class="text-white mt-3">No matching accounts found</h6>
        </div>
    @endif
</div>

<!-- ══════════════ EDIT EXISTING ACCOUNT MODAL ══════════════ -->
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

<!-- ══════════════ CREATE NEW ACCOUNT MODAL ══════════════ -->
<div class="modal fade" id="createUserAccountModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content admin-glass">
            <form method="POST" action="{{ route('admin.users.store') }}">
                @csrf
                <div class="modal-header border-0 px-4 pt-4">
                    <h5 class="modal-title text-info"><i class="bi bi-person-plus-fill me-2"></i>Create New Account</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body p-4">
                    <div class="row">
                        <div class="col-md-8 modal-divider pe-4">
                            <h6 class="text-info mb-3">Personal Profile Information</h6>
                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <label class="info-label mb-2">First Name</label>
                                    <input type="text" name="first_name" class="form-control rounded-pill" value="{{ old('first_name') }}" required>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="info-label mb-2">Middle Name</label>
                                    <input type="text" name="middle_name" class="form-control rounded-pill" value="{{ old('middle_name') }}">
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="info-label mb-2">Last Name</label>
                                    <input type="text" name="last_name" class="form-control rounded-pill" value="{{ old('last_name') }}" required>
                                </div>
                            </div>

                            <div class="row mt-2">
                                <div class="col-md-6 mb-3">
                                    <label class="info-label mb-2">Date of Birth</label>
                                    <input type="date" name="date_of_birth" class="form-control rounded-pill" value="{{ old('date_of_birth') }}" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="info-label mb-2">Sex</label>
                                    <select name="sex" class="form-select rounded-pill" required>
                                        <option value="male" {{ old('sex') == 'male' ? 'selected' : '' }}>Male</option>
                                        <option value="female" {{ old('sex') == 'female' ? 'selected' : '' }}>Female</option>
                                    </select>
                                </div>
                            </div>

                            <div class="row mt-2">
                                <div class="col-md-6 mb-3">
                                    <label class="info-label mb-2">Email Address</label>
                                    <input type="email" name="email" class="form-control rounded-pill" value="{{ old('email') }}" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="info-label mb-2">Phone Number</label>
                                    <input type="text" name="phone_number" class="form-control rounded-pill" value="{{ old('phone_number') }}" required>
                                </div>
                            </div>

                            <h6 class="text-info mt-4 mb-3">Geographic Address Info</h6>
                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <label class="info-label mb-2">Municipality</label>
                                    <input type="text" name="Umunicipality" class="form-control rounded-pill" value="{{ old('Umunicipality') }}" required>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="info-label mb-2">Barangay</label>
                                    <input type="text" name="Ubarangay" class="form-control rounded-pill" value="{{ old('Ubarangay') }}" required>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="info-label mb-2">Street / House Details</label>
                                    <input type="text" name="Ustreet_house" class="form-control rounded-pill" value="{{ old('Ustreet_house') }}" required>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-4 ps-4">
                            <h6 class="text-info mb-4">Security Access Roles</h6>
                            <div class="mb-4">
                                <label class="info-label mb-2">Assigned Authorization Tier</label>
                                <select name="role" class="form-select rounded-pill" required>
                                    <option value="patient" {{ old('role') == 'patient' ? 'selected' : '' }}>Patient Access Profile</option>
                                    <option value="staff" {{ old('role') == 'staff' ? 'selected' : '' }}>Staff/Operator Profile</option>
                                    <option value="admin" {{ old('role') == 'admin' ? 'selected' : '' }}>System Administrator</option>
                                </select>
                            </div>

                            <h6 class="text-info mt-4 mb-3">Emergency / Next-of-Kin Contacts</h6>
                            <div class="mb-3">
                                <label class="info-label mb-2">Contact Person Name</label>
                                <input type="text" name="contact_person" class="form-control rounded-pill" value="{{ old('contact_person') }}" required>
                            </div>
                            <div class="mb-3">
                                <label class="info-label mb-2">Contact Person Phone</label>
                                <input type="text" name="contact_number" class="form-control rounded-pill" value="{{ old('contact_number') }}" required>
                            </div>

                            <h6 class="text-info mt-4 mb-3">Login Credentials</h6>
                            <div class="mb-3">
                                <label class="info-label mb-2">Password</label>
                                <input type="password" name="password" class="form-control rounded-pill" required minlength="6">
                            </div>
                            <div class="mb-3">
                                <label class="info-label mb-2">Confirm Password</label>
                                <input type="password" name="password_confirmation" class="form-control rounded-pill" required minlength="6">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer border-0 pb-4 px-4">
                    <button type="button" class="btn btn-outline-secondary rounded-pill px-4" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-info rounded-pill px-5 fw-bold text-dark shadow">Create Account</button>
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

    function filterUserAccounts() {
        const query = document.getElementById('userSearchInput').value.toLowerCase().trim();
        const rows = document.querySelectorAll('.user-account-row');
        let visibleCount = 0;

        rows.forEach(function (row) {
            const matches = row.dataset.search.includes(query);
            row.style.display = matches ? '' : 'none';
            if (matches) visibleCount++;
        });

        const noResults = document.getElementById('noSearchResults');
        if (noResults) {
            noResults.style.display = (visibleCount === 0 && query !== '') ? 'block' : 'none';
        }
    }

    // If validation failed on the "Create Account" submission, re-open that
    // modal on page load so the admin doesn't lose context (old() values are
    // already re-populated into the fields above via Blade).
    @if($errors->any() && old('role'))
        document.addEventListener('DOMContentLoaded', function () {
            new bootstrap.Modal(document.getElementById('createUserAccountModal')).show();
        });
    @endif
</script>
@endsection