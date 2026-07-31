@extends('layouts.adminlayout')

@section('admincontent')

<style>
    .glass-card {
        background: rgba(255, 255, 255, 0.08);
        backdrop-filter: blur(15px);
        border: 1px solid rgba(255, 255, 255, 0.15);
        border-radius: 20px;
        box-shadow: 0 15px 35px rgba(0,0,0,0.3);
        color: #fff;
    }

    .glass-card .card-header {
        background: rgba(255, 255, 255, 0.05);
        border-bottom: 1px solid rgba(255, 255, 255, 0.15);
        border-radius: 20px 20px 0 0 !important;
        font-weight: 600;
        letter-spacing: 0.3px;
    }

    .glass-card .card-body {
        padding: 25px;
    }

    .profile-page label.form-label {
        color: rgba(255,255,255,0.75);
        font-size: 0.85rem;
        margin-bottom: 5px;
    }

    .profile-page input.form-control,
    .profile-page select.form-control {
        background: rgba(255,255,255,0.1);
        border: 1px solid rgba(255,255,255,0.2);
        color: #fff;
        border-radius: 10px;
    }

    .profile-page input.form-control:focus,
    .profile-page select.form-control:focus {
        background: rgba(255,255,255,0.18);
        color: #fff;
        border-color: #00d4ff;
        box-shadow: 0 0 0 0.2rem rgba(0, 212, 255, 0.15);
    }

    .profile-page select.form-control option {
        background: #222;
        color: #fff;
    }

    .profile-page hr {
        border-color: rgba(255,255,255,0.15);
    }

    .profile-page h6.section-label {
        color: #00d4ff;
        font-weight: 600;
        letter-spacing: 0.5px;
        font-size: 0.9rem;
        text-transform: uppercase;
    }

    .avatar-circle {
        width: 90px;
        height: 90px;
        border-radius: 50%;
        background: rgba(0, 212, 255, 0.15);
        border: 2px solid #00d4ff;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 2.2rem;
        font-weight: bold;
        color: #00d4ff;
        margin: 0 auto 15px auto;
    }

    .info-row {
        display: flex;
        justify-content: space-between;
        padding: 10px 0;
        border-bottom: 1px solid rgba(255,255,255,0.08);
        font-size: 0.9rem;
    }
    .info-row:last-child { border-bottom: none; }
    .info-row .label { color: rgba(255,255,255,0.6); }
    .info-row .value { font-weight: 500; text-align: right; }

    .badge-role {
        background: rgba(0, 212, 255, 0.2);
        color: #00d4ff;
        border: 1px solid #00d4ff;
        padding: 4px 12px;
        border-radius: 20px;
        font-size: 0.75rem;
        font-weight: 600;
        letter-spacing: 0.5px;
    }

    .profile-page .btn-glass-primary {
        background: #00d4ff;
        border: none;
        color: #002733;
        font-weight: 600;
        border-radius: 10px;
        padding: 10px 0;
    }
    .profile-page .btn-glass-primary:hover {
        background: #00b8e0;
        color: #002733;
    }

    .profile-page .btn-glass-warning {
        background: rgba(255, 193, 7, 0.15);
        border: 1px solid #ffc107;
        color: #ffc107;
        font-weight: 600;
        border-radius: 10px;
        padding: 10px 0;
    }
    .profile-page .btn-glass-warning:hover {
        background: rgba(255, 193, 7, 0.3);
        color: #ffc107;
    }

    .oauth-badge {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        background: rgba(255,255,255,0.08);
        border-radius: 10px;
        padding: 8px 12px;
        font-size: 0.85rem;
    }
</style>

<div class="container-fluid profile-page">
    <h2 class="mb-4 text-white">
        <i class="bi bi-person-circle text-info"></i> My Profile
    </h2>

    @if(session('success'))
        <div class="alert alert-success" style="background: rgba(25, 135, 84, 0.2); color: #7ee8a1; border: 1px solid #198754;">
            {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger" style="background: rgba(220, 53, 69, 0.2); color: #ff8a94; border: 1px solid #dc3545;">
            {{ session('error') }}
        </div>
    @endif

    <div class="row">
        {{-- Profile Details --}}
        <div class="col-md-8 mb-4">
            <div class="card glass-card">
                <div class="card-header">
                    <i class="bi bi-person-lines-fill me-2"></i>Profile Details
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('admin.profile.update') }}">
                        @csrf
                        @method('PUT')

                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label class="form-label">First Name</label>
                                <input type="text" name="first_name" class="form-control"
                                       value="{{ old('first_name', $admin->first_name) }}">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Middle Name</label>
                                <input type="text" name="middle_name" class="form-control"
                                       value="{{ old('middle_name', $admin->middle_name) }}">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Last Name</label>
                                <input type="text" name="last_name" class="form-control"
                                       value="{{ old('last_name', $admin->last_name) }}">
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Date of Birth</label>
                                <input type="date" name="date_of_birth" class="form-control"
                                       value="{{ old('date_of_birth', $admin->date_of_birth) }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Sex</label>
                                <select name="sex" class="form-control">
                                    <option value="male" {{ $admin->sex === 'male' ? 'selected' : '' }}>Male</option>
                                    <option value="female" {{ $admin->sex === 'female' ? 'selected' : '' }}>Female</option>
                                </select>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Email</label>
                                <input type="email" name="email" class="form-control"
                                       value="{{ old('email', $admin->email) }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Phone Number</label>
                                <input type="text" name="phone_number" class="form-control"
                                       value="{{ old('phone_number', $admin->phone_number) }}">
                            </div>
                        </div>

                        <hr>
                        <h6 class="mb-3 section-label"><i class="bi bi-geo-alt-fill"></i> Address</h6>

                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label class="form-label">Municipality</label>
                                <input type="text" name="Umunicipality" class="form-control"
                                       value="{{ old('Umunicipality', $admin->Umunicipality) }}">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Barangay</label>
                                <input type="text" name="Ubarangay" class="form-control"
                                       value="{{ old('Ubarangay', $admin->Ubarangay) }}">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Street/House No.</label>
                                <input type="text" name="Ustreet_house" class="form-control"
                                       value="{{ old('Ustreet_house', $admin->Ustreet_house) }}">
                            </div>
                        </div>

                        <hr>
                        <h6 class="mb-3 section-label"><i class="bi bi-telephone-fill"></i> Emergency Contact</h6>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Contact Person</label>
                                <input type="text" name="contact_person" class="form-control"
                                       value="{{ old('contact_person', $admin->contact_person) }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Contact Number</label>
                                <input type="text" name="contact_number" class="form-control"
                                       value="{{ old('contact_number', $admin->contact_number) }}">
                            </div>
                        </div>

                        <button type="submit" class="btn btn-glass-primary w-100 mt-2">
                            <i class="bi bi-check-circle me-1"></i> Save Changes
                        </button>
                    </form>
                </div>
            </div>
        </div>

        {{-- Sidebar: Account Info + Password --}}
        <div class="col-md-4">
            <div class="card glass-card mb-4">
                <div class="card-body text-center">
                    <div class="avatar-circle">
                        {{ strtoupper(substr($admin->first_name, 0, 1)) }}{{ strtoupper(substr($admin->last_name, 0, 1)) }}
                    </div>
                    <h5 class="mb-1">{{ $admin->first_name }} {{ $admin->last_name }}</h5>
                    <span class="badge-role">{{ strtoupper($admin->role) }}</span>

                    <hr>

                    <div class="info-row">
                        <span class="label">Account Created</span>
                        <span class="value">{{ $admin->created_at->format('M d, Y') }}</span>
                    </div>
                    <div class="info-row">
                        <span class="label">Last Updated</span>
                        <span class="value">{{ $admin->updated_at->format('M d, Y h:i A') }}</span>
                    </div>

                    @if($admin->oauth_provider)
                        <div class="mt-3">
                            <span class="oauth-badge">
                                <i class="bi bi-shield-check text-info"></i>
                                Signed in via {{ ucfirst($admin->oauth_provider) }}
                            </span>
                        </div>
                    @endif
                </div>
            </div>

            @if(!$admin->oauth_provider)
                <div class="card glass-card">
                    <div class="card-header">
                        <i class="bi bi-key-fill me-2"></i>Change Password
                    </div>
                    <div class="card-body">
                        <form method="POST" action="{{ route('admin.profile.password') }}">
                            @csrf
                            @method('PUT')

                            <div class="mb-3">
                                <label class="form-label">Current Password</label>
                                <input type="password" name="current_password" class="form-control">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">New Password</label>
                                <input type="password" name="new_password" class="form-control">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Confirm New Password</label>
                                <input type="password" name="new_password_confirmation" class="form-control">
                            </div>

                            <button type="submit" class="btn btn-glass-warning w-100">
                                <i class="bi bi-shield-lock me-1"></i> Update Password
                            </button>
                        </form>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

@endsection