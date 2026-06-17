<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Account Settings - SMH</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">

  <style>
    body {
      margin: 0;
      font-family: Arial, sans-serif;
      color: #fff;
      min-height: 100vh;
      overflow-x: hidden;
      overflow-y: auto;
    }
    body::before {
      content: "";
      position: fixed;
      top: 0; left: 0;
      width: 100%; height: 100%;
      background: url('/images/SMHPhoto.jpg') no-repeat center center/cover;
      filter: blur(12px) brightness(0.6);
      z-index: -2;
    }
    body::after {
      content: "";
      position: fixed;
      top: 0; left: 0;
      width: 100%; height: 100%;
      background: rgba(0,0,0,0.4);
      z-index: -1;
    }
    .header {
      position: sticky;
      top: 0;
      z-index: 1100;
      display: flex;
      align-items: center;
      justify-content: space-between;
      padding: 15px 30px;
      background: rgba(255,255,255,0.1);
      backdrop-filter: blur(15px);
      border-bottom: 1px solid rgba(255,255,255,0.2);
      box-shadow: 0 4px 20px rgba(0,0,0,0.3);
    }
    .logo-section { display: flex; align-items: center; gap: 10px; }
    .logo-section img { width: 50px; }
    .logo-text { font-weight: bold; font-size: 18px; }
    .mobile-toggle {
      display: none;
      background: none;
      border: none;
      color: white;
      font-size: 1.8rem;
      margin-right: 15px;
      cursor: pointer;
    }
    .main-content { display: flex; min-height: calc(100vh - 81px); }
    .sidebar {
      width: 250px;
      padding: 20px;
      background: rgba(255,255,255,0.1);
      backdrop-filter: blur(15px);
      border-right: 1px solid rgba(255,255,255,0.2);
      display: flex;
      flex-direction: column;
      gap: 10px;
      position: sticky;
      top: 81px;
      height: calc(100vh - 81px);
      transition: transform 0.3s ease-in-out;
    }
    .sidebar h3 { text-align: center; margin-bottom: 20px; color: #fff; }
    .sidebar a {
      color: #fff;
      text-decoration: none;
      padding: 12px;
      border-radius: 8px;
      background: rgba(255,255,255,0.1);
      transition: 0.3s;
      display: flex;
      align-items: center;
      gap: 10px;
    }
    .sidebar a:hover { background: rgba(255,255,255,0.3); transform: translateX(5px); }
    .sidebar a.active { background: rgba(255,255,255,0.4); font-weight: bold; }
    .content-area { flex: 1; padding: 30px; }
    .glass-card {
      background: rgba(255,255,255,0.1);
      backdrop-filter: blur(12px);
      border: 1px solid rgba(255,255,255,0.2);
      border-radius: 12px;
      padding: 30px;
      margin-bottom: 20px;
    }
    .form-label { color: #fff; font-weight: 500; }
    .form-control, .form-select {
      background: rgba(255,255,255,0.15);
      border: 1px solid rgba(255,255,255,0.3);
      color: #fff;
      border-radius: 8px;
    }
    .form-control:focus, .form-select:focus {
      background: rgba(255,255,255,0.25);
      border-color: #00d4ff;
      color: #fff;
      box-shadow: 0 0 0 0.2rem rgba(0,212,255,0.25);
    }
    .form-control::placeholder { color: rgba(255,255,255,0.5); }
    .btn-save {
      background: #00d4ff;
      border: none;
      color: #000;
      font-weight: bold;
      padding: 10px 30px;
      border-radius: 8px;
    }
    .btn-save:hover { background: #00b0d4; color: #fff; }
    .btn-change-pass {
      background: rgba(255,255,255,0.2);
      border: 1px solid rgba(255,255,255,0.3);
      color: #fff;
      font-weight: bold;
      padding: 10px 30px;
      border-radius: 8px;
    }
    .btn-change-pass:hover { background: rgba(255,255,255,0.35); color: #fff; }
    .section-title {
      font-size: 1.2rem;
      font-weight: bold;
      margin-bottom: 20px;
      border-bottom: 1px solid rgba(255,255,255,0.2);
      padding-bottom: 10px;
    }
    .avatar-circle {
      width: 80px;
      height: 80px;
      border-radius: 50%;
      background: rgba(0,212,255,0.3);
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 2.5rem;
      margin: 0 auto 15px;
      border: 2px solid rgba(0,212,255,0.5);
    }
    @media (max-width: 768px) {
      .mobile-toggle { display: block; }
      .welcome-text { display: none; }
      .sidebar {
        position: fixed;
        left: 0; top: 81px;
        z-index: 1050;
        transform: translateX(-100%);
        width: 280px;
        background: rgba(255,255,255,0.15);
        backdrop-filter: blur(25px);
        box-shadow: 10px 0 30px rgba(0,0,0,0.5);
      }
      .sidebar.active { transform: translateX(0); }
      .content-area { padding: 20px; }
    }
  </style>
</head>
<body>

<div class="header">
  <div class="logo-section">
    <button class="mobile-toggle" id="menuToggle">
      <i class="bi bi-list"></i>
    </button>
    <img src="{{ asset('images/SMHLogo.png') }}" alt="Logo">
    <span class="logo-text">Subic Med Health</span>
  </div>
  <div class="welcome-text">Welcome, {{ auth()->user()->name ?? 'Patient' }} 👋</div>
  <div class="profile-section">
    <form action="{{ route('logout') }}" method="POST" style="display:inline;">
      @csrf
      <button type="submit" class="btn btn-sm btn-outline-light">Logout</button>
    </form>
  </div>
</div>

<div class="main-content">
  <div class="sidebar" id="sidebar">
    <h3>Menu</h3>
    <a href="{{ route('patientdashboard') }}"><i class="bi bi-speedometer2"></i> Dashboard</a>
    <a href="{{ route('appointment.form') }}"><i class="bi bi-calendar-plus"></i> Book Appointment</a>
    <a href="{{ route('patient.appointments') }}"><i class="bi bi-clock-history"></i> Pending Appointment</a>
    <a href="{{ route('patient.results.index') }}"><i class="bi bi-file-earmark-check"></i> Laboratory Results</a>
    <a href="{{ route('patient.settings') }}" class="active"><i class="bi bi-person-gear"></i> Account Settings</a>
  </div>

  <div class="content-area">

    {{-- Success/Error Messages --}}
    @if(session('success'))
      <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
      </div>
    @endif
    @if(session('error'))
      <div class="alert alert-danger alert-dismissible fade show" role="alert">
        {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
      </div>
    @endif

    {{-- Profile Info --}}
    <div class="glass-card">
      <div class="text-center mb-3">
        <div class="avatar-circle">
          <i class="bi bi-person-fill"></i>
        </div>
        <h5>{{ auth()->user()->name ?? 'Patient' }}</h5>
        <small class="text-white-50">{{ auth()->user()->email ?? '' }}</small>
      </div>

      <div class="section-title"><i class="bi bi-person-lines-fill me-2"></i>Personal Information</div>

      <form action="{{ route('patient.settings.update') }}" method="POST">
        @csrf
        @method('PUT')
        <div class="row g-3">
          <div class="col-md-4">
            <label class="form-label">First Name</label>
            <input type="text" name="first_name" class="form-control"
              value="{{ auth()->user()->first_name ?? '' }}" placeholder="First Name">
          </div>
          <div class="col-md-4">
            <label class="form-label">Middle Name</label>
            <input type="text" name="middle_name" class="form-control"
              value="{{ auth()->user()->middle_name ?? '' }}" placeholder="Middle Name">
          </div>
          <div class="col-md-4">
            <label class="form-label">Last Name</label>
            <input type="text" name="last_name" class="form-control"
              value="{{ auth()->user()->last_name ?? '' }}" placeholder="Last Name">
          </div>
          <div class="col-md-6">
            <label class="form-label">Email Address</label>
            <input type="email" name="email" class="form-control"
              value="{{ auth()->user()->email ?? '' }}" placeholder="Email">
          </div>
          <div class="col-md-6">
            <label class="form-label">Phone Number</label>
            <input type="text" name="phone" class="form-control"
              value="{{ auth()->user()->phone ?? '' }}" placeholder="Phone Number">
          </div>
        </div>
        <div class="mt-3">
          <button type="submit" class="btn btn-save">
            <i class="bi bi-save me-1"></i> Save Changes
          </button>
        </div>
      </form>
    </div>

   {{-- Change Password --}}
    <div class="glass-card">
      <div class="section-title"><i class="bi bi-lock-fill me-2"></i>Change Password</div>
      <form action="{{ route('patient.settings.password') }}" method="POST">
        @csrf
        @method('PUT')
        <div class="row g-3">
          <div class="col-md-4">
            <label class="form-label">Current Password</label>
            <div class="input-group">
              <input type="password" name="current_password" id="current_password" class="form-control" placeholder="Current Password">
              <button class="btn btn-outline-light" type="button" onclick="togglePassword('current_password', this)">
                <i class="bi bi-eye"></i>
              </button>
            </div>
          </div>
          <div class="col-md-4">
            <label class="form-label">New Password</label>
            <div class="input-group">
              <input type="password" name="new_password" id="new_password" class="form-control" placeholder="New Password">
              <button class="btn btn-outline-light" type="button" onclick="togglePassword('new_password', this)">
                <i class="bi bi-eye"></i>
              </button>
            </div>
          </div>
          <div class="col-md-4">
            <label class="form-label">Confirm New Password</label>
            <div class="input-group">
              <input type="password" name="new_password_confirmation" id="new_password_confirmation" class="form-control" placeholder="Confirm Password">
              <button class="btn btn-outline-light" type="button" onclick="togglePassword('new_password_confirmation', this)">
                <i class="bi bi-eye"></i>
              </button>
            </div>
          </div>
        </div>
        <div class="mt-3">
          <button type="submit" class="btn btn-change-pass">
            <i class="bi bi-shield-lock me-1"></i> Change Password
          </button>
        </div>
      </form>
    </div>

  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

<script>
  const menuToggle = document.getElementById('menuToggle');
  const sidebar = document.getElementById('sidebar');
  menuToggle.addEventListener('click', (e) => {
    e.stopPropagation();
    sidebar.classList.toggle('active');
  });
  document.addEventListener('click', (e) => {
    if (window.innerWidth <= 768 && !sidebar.contains(e.target) && !menuToggle.contains(e.target)) {
      sidebar.classList.remove('active');
    }
  });

  function togglePassword(fieldId, btn) {
    const input = document.getElementById(fieldId);
    const icon = btn.querySelector('i');
    if (input.type === 'password') {
      input.type = 'text';
      icon.classList.remove('bi-eye');
      icon.classList.add('bi-eye-slash');
    } else {
      input.type = 'password';
      icon.classList.remove('bi-eye-slash');
      icon.classList.add('bi-eye');
    }
  }
</script>
</body>
</html>