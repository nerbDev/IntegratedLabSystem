<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Manage Accounts - SMH</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
  <style>
    body { margin:0; font-family:Arial,sans-serif; color:#fff; min-height:100vh; overflow-x:hidden; overflow-y:auto; }
    body::before { content:""; position:fixed; width:100%; height:100%; background:url('/images/SMHPhoto.jpg') no-repeat center center/cover; filter:blur(12px) brightness(0.6); z-index:-2; }
    body::after { content:""; position:fixed; width:100%; height:100%; background:rgba(0,0,0,0.4); z-index:-1; }
    .header { position:sticky; top:0; z-index:1100; display:flex; align-items:center; justify-content:space-between; padding:15px 30px; background:rgba(255,255,255,0.1); backdrop-filter:blur(15px); border-bottom:1px solid rgba(255,255,255,0.2); box-shadow:0 4px 20px rgba(0,0,0,0.3); }
    .logo-section { display:flex; align-items:center; gap:10px; }
    .logo-section img { width:50px; }
    .logo-text { font-weight:bold; font-size:18px; }
    .mobile-toggle { display:none; background:none; border:none; color:white; font-size:1.8rem; margin-right:15px; cursor:pointer; }
    .profile-dropdown { position:relative; cursor:pointer; }
    .profile-dropdown-content { display:none; position:absolute; right:0; background:rgba(30,30,30,0.9); backdrop-filter:blur(12px); border:1px solid rgba(255,255,255,0.2); min-width:160px; border-radius:10px; overflow:hidden; z-index:9999; }
    .profile-dropdown-content a { color:#fff; padding:10px; display:block; text-decoration:none; transition:0.3s; }
    .profile-dropdown-content a:hover { background:rgba(255,255,255,0.3); color:#000; }
    .main-content { display:flex; min-height:calc(100vh - 81px); }
    .sidebar { width:260px; padding:20px; background:rgba(255,255,255,0.1); backdrop-filter:blur(15px); border-right:1px solid rgba(255,255,255,0.2); display:flex; flex-direction:column; gap:10px; position:sticky; top:81px; height:calc(100vh - 81px); transition:transform 0.3s ease-in-out; }
    .sidebar h3 { text-align:center; margin-bottom:20px; }
    .sidebar a { color:#fff; text-decoration:none; padding:12px; border-radius:8px; background:rgba(255,255,255,0.15); transition:0.3s; }
    .sidebar a.active, .sidebar a:hover { background:rgba(0,212,255,0.3); color:#fff; transform:translateX(5px); border:1px solid rgba(0,212,255,0.5); }
    .content-area { flex:1; padding:40px; }
    .page-header { text-align:center; margin-bottom:30px; }
    .page-header h2 { font-size:2rem; font-weight:bold; color:#fff; }
    .page-header p { color:rgba(255,255,255,0.6); font-size:0.95rem; }
    .stat-box { background:rgba(255,255,255,0.1); border:1px solid rgba(255,255,255,0.2); border-radius:12px; padding:20px; text-align:center; }
    .stat-box h3 { font-size:2.2rem; font-weight:bold; color:#00d4ff; margin:0; }
    .stat-box p { margin:0; font-size:0.9rem; color:rgba(255,255,255,0.7); }
    .glass-card { background:rgba(255,255,255,0.08); backdrop-filter:blur(12px); border:1px solid rgba(255,255,255,0.2); border-radius:12px; padding:25px; }
    .filter-bar { display:flex; gap:12px; flex-wrap:wrap; margin-bottom:20px; align-items:center; }
    .filter-input { background:rgba(255,255,255,0.12); border:1px solid rgba(255,255,255,0.25); color:#fff; border-radius:50px; padding:8px 18px; font-size:0.9rem; }
    .filter-input::placeholder { color:rgba(255,255,255,0.45); }
    .filter-input:focus { outline:none; border-color:#00d4ff; background:rgba(255,255,255,0.2); color:#fff; }
    .filter-select { background:rgba(255,255,255,0.12); border:1px solid rgba(255,255,255,0.25); color:#fff; border-radius:50px; padding:8px 18px; font-size:0.9rem; cursor:pointer; }
    .filter-select option { background:#1a1a2e; color:#fff; }
    .table-glass { color:#fff; }
    .table-glass thead th { background:rgba(255,255,255,0.12); border-color:rgba(255,255,255,0.15); color:rgba(255,255,255,0.8); font-weight:600; font-size:0.85rem; text-transform:uppercase; letter-spacing:0.5px; padding:14px 12px; }
    .table-glass tbody td { background:rgba(255,255,255,0.04); border-color:rgba(255,255,255,0.08); vertical-align:middle; padding:12px; }
    .table-glass tbody tr:hover td { background:rgba(255,255,255,0.1); }
    .avatar { width:38px; height:38px; border-radius:50%; background:rgba(0,212,255,0.25); border:2px solid rgba(0,212,255,0.5); display:flex; align-items:center; justify-content:center; font-size:1rem; color:#00d4ff; }
    .badge-active { background:rgba(40,167,69,0.25); border:1px solid #28a745; color:#6dff9a; padding:4px 10px; border-radius:50px; font-size:0.8rem; }
    .badge-patient { background:rgba(0,212,255,0.2); border:1px solid #00d4ff; color:#00d4ff; padding:4px 10px; border-radius:50px; font-size:0.8rem; }
    .btn-view { background:transparent; border:1px solid rgba(255,255,255,0.3); color:#fff; border-radius:6px; padding:5px 10px; font-size:0.82rem; transition:0.2s; cursor:pointer; }
    .btn-view:hover { background:rgba(255,255,255,0.15); color:#fff; }
    .pagination-bar { display:flex; justify-content:space-between; align-items:center; margin-top:15px; font-size:0.85rem; color:rgba(255,255,255,0.6); }
    /* CUSTOM MODAL */
    .custom-modal { display:none; position:fixed; top:0; left:0; width:100%; height:100%; z-index:99999; align-items:center; justify-content:center; }
    .custom-modal.show { display:flex; }
    .modal-backdrop-custom { position:absolute; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.75); }
    .modal-box { position:relative; z-index:100000; background:#0f0f1e; border:1px solid rgba(255,255,255,0.15); border-radius:16px; width:90%; max-width:700px; max-height:85vh; overflow-y:auto; padding:25px; color:#fff; }
    .modal-box-header { display:flex; justify-content:space-between; align-items:center; border-bottom:1px solid rgba(255,255,255,0.1); padding-bottom:15px; margin-bottom:20px; }
    .modal-close-btn { background:none; border:none; color:#fff; font-size:1.8rem; cursor:pointer; line-height:1; }
    .info-section { background:rgba(255,255,255,0.06); border-radius:10px; padding:15px; margin-bottom:10px; }
    .info-label { color:rgba(255,255,255,0.5); font-size:0.75rem; text-transform:uppercase; margin-bottom:10px; }
    .info-item { margin-bottom:8px; }
    .info-item small { color:rgba(255,255,255,0.5); display:block; }
    @media (max-width:768px) {
      .mobile-toggle { display:block; }
      .welcome-text { display:none; }
      .sidebar { position:fixed; left:0; top:81px; z-index:1050; transform:translateX(-100%); width:280px; background:rgba(255,255,255,0.1); backdrop-filter:blur(25px); box-shadow:10px 0 30px rgba(0,0,0,0.5); }
      .sidebar.active { transform:translateX(0); }
      .content-area { padding:20px; }
      .filter-bar { flex-direction:column; }
      .filter-input, .filter-select { width:100%; }
    }
  </style>
</head>
<body>

<div class="header">
  <div class="logo-section">
    <button class="mobile-toggle" id="menuToggle"><i class="bi bi-list"></i></button>
    <img src="{{ asset('images/SMHLogo.png') }}">
    <span class="logo-text">Subic Med Health</span>
  </div>
  <div class="welcome-text">Welcome, Staff 👨‍⚕️</div>
  <div class="profile-dropdown">
    <i class="bi bi-person-circle" style="font-size:22px;"></i>
    <div class="profile-dropdown-content">
      <a href="#">Profile</a>
      <a href="#">Settings</a>
      <form action="{{ route('logout') }}" method="POST">
        @csrf
        <button type="submit" style="width:100%; background:none; border:none; color:#fff; padding:10px; text-align:left;">Logout</button>
      </form>
    </div>
  </div>
</div>

<div class="main-content">
  <div class="sidebar" id="sidebar">
    <h3>Menu</h3>
    <a href="{{ route('staffdashboard') }}"><i class="bi bi-speedometer2"></i> Dashboard</a>
    <a href="#"><i class="bi bi-file-earmark-text"></i> System Reports</a>
    <a href="#"><i class="bi bi-file-text"></i> Generate Reports</a>
    <a href="#"><i class="bi bi-calendar-check"></i> Appointment Schedule</a>
    <a href="{{ route('appointments.requests') }}"><i class="bi bi-envelope"></i> Appointment Requests</a>
    <a href="{{ route('staff.manageaccounts') }}" class="active"><i class="bi bi-person-badge"></i> Manage Accounts</a>
  </div>

  <div class="content-area">

    @if(session('success'))
      <div class="alert alert-success alert-dismissible fade show">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
      </div>
    @endif

    <div class="page-header">
      <h2><i class="bi bi-people-fill me-2"></i>Patient Management</h2>
      <p>Manage all patient accounts. View detailed information and control access.</p>
    </div>

    <div class="row g-3 mb-4">
      <div class="col-md-4">
        <div class="stat-box">
          <h3>{{ $totalPatients }}</h3>
          <p><i class="bi bi-people me-1"></i>Total Patients</p>
        </div>
      </div>
      <div class="col-md-4">
        <div class="stat-box">
          <h3>{{ $totalPatients }}</h3>
          <p><i class="bi bi-check-circle me-1"></i>Active Patients</p>
        </div>
      </div>
      <div class="col-md-4">
        <div class="stat-box">
          <h3>{{ now()->format('M. Y') }}</h3>
          <p><i class="bi bi-calendar me-1"></i>Current Period</p>
        </div>
      </div>
    </div>

    <div class="glass-card">
      <div class="filter-bar">
        <input type="text" id="searchInput" class="filter-input" placeholder="🔍  Search by name or email..." style="flex:1; min-width:200px;">
        <select id="sexFilter" class="filter-select">
          <option value="">👤 All Sex</option>
          <option value="male">Male</option>
          <option value="female">Female</option>
        </select>
      </div>

      <div class="table-responsive">
        <table class="table table-glass table-borderless" id="accountsTable">
          <thead>
            <tr>
              <th><input type="checkbox" id="selectAll"></th>
              <th>Full Name</th>
              <th>Email</th>
              <th>Phone</th>
              <th>Sex</th>
              <th>Joined Date</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody id="tableBody">
            @forelse($accounts as $account)
            <tr data-sex="{{ $account->sex }}">
              <td><input type="checkbox" class="row-check"></td>
              <td>
                <div class="d-flex align-items-center gap-2">
                  <div class="avatar"><i class="bi bi-person-fill"></i></div>
                  <div>
                    <div style="font-weight:600;">{{ $account->first_name }} {{ $account->middle_name }} {{ $account->last_name }}</div>
                    <div style="font-size:0.78rem; color:rgba(255,255,255,0.5);">ID #{{ $account->id }}</div>
                  </div>
                </div>
              </td>
              <td style="color:rgba(255,255,255,0.75);">{{ $account->email }}</td>
              <td>{{ $account->phone_number ?? 'N/A' }}</td>
              <td>{{ ucfirst($account->sex ?? 'N/A') }}</td>
              <td style="color:rgba(255,255,255,0.6);">{{ $account->created_at->format('M d, Y') }}</td>
              <td>
                <button class="btn-view" onclick="openModal('modal-{{ $account->id }}')">
                  <i class="bi bi-eye"></i> View
                </button>
              </td>
            </tr>
            @empty
            <tr>
              <td colspan="8" class="text-center py-4" style="color:rgba(255,255,255,0.4);">
                <i class="bi bi-inbox" style="font-size:2rem;"></i>
                <div class="mt-2">No patient accounts found.</div>
              </td>
            </tr>
            @endforelse
          </tbody>
        </table>
      </div>

      <div class="pagination-bar">
        <span>Showing {{ $accounts->count() }} of {{ $totalPatients }} patients</span>
      </div>
    </div>

  </div>
</div>

{{-- MODALS (outside main layout) --}}
@foreach($accounts as $account)
<div class="custom-modal" id="modal-{{ $account->id }}">
  <div class="modal-backdrop-custom" onclick="closeModal('modal-{{ $account->id }}')"></div>
  <div class="modal-box">
    <div class="modal-box-header">
      <h5><i class="bi bi-person-badge me-2" style="color:#00d4ff;"></i>Patient Details</h5>
      <button class="modal-close-btn" onclick="closeModal('modal-{{ $account->id }}')">&times;</button>
    </div>

    <div class="text-center mb-4">
      <div style="width:80px; height:80px; border-radius:50%; background:rgba(0,212,255,0.15); display:flex; align-items:center; justify-content:center; margin:0 auto; font-size:2.2rem; border:2px solid rgba(0,212,255,0.4);">
        <i class="bi bi-person-fill" style="color:#00d4ff;"></i>
      </div>
      <h5 class="mt-3 mb-1">{{ $account->first_name }} {{ $account->middle_name }} {{ $account->last_name }}</h5>
      <span class="badge-patient">Patient</span>
    </div>

    <div class="row g-3">
      <div class="col-md-6">
        <div class="info-section">
          <div class="info-label">Personal Info</div>
          <div class="info-item"><small>Full Name</small>{{ $account->first_name }} {{ $account->middle_name }} {{ $account->last_name }}</div>
          <div class="info-item"><small>Date of Birth</small>{{ $account->date_of_birth ?? 'N/A' }}</div>
          <div class="info-item"><small>Sex</small>{{ ucfirst($account->sex ?? 'N/A') }}</div>
          <div class="info-item"><small>Registered</small>{{ $account->created_at->format('M d, Y') }}</div>
        </div>
      </div>
      <div class="col-md-6">
        <div class="info-section">
          <div class="info-label">Contact Info</div>
          <div class="info-item"><small>Email</small>{{ $account->email }}</div>
          <div class="info-item"><small>Phone</small>{{ $account->phone_number ?? 'N/A' }}</div>
          <div class="info-item"><small>Contact Person</small>{{ $account->contact_person ?? 'N/A' }}</div>
          <div class="info-item"><small>Contact Number</small>{{ $account->contact_number ?? 'N/A' }}</div>
        </div>
      </div>
      <div class="col-12">
        <div class="info-section">
          <div class="info-label">Address</div>
          <div class="row">
            <div class="col-md-4 info-item"><small>Municipality</small>{{ $account->Umunicipality ?? 'N/A' }}</div>
            <div class="col-md-4 info-item"><small>Barangay</small>{{ $account->Ubarangay ?? 'N/A' }}</div>
            <div class="col-md-4 info-item"><small>Street/House</small>{{ $account->Ustreet_house ?? 'N/A' }}</div>
          </div>
        </div>
      </div>
    </div>

    <div style="text-align:right; margin-top:20px; border-top:1px solid rgba(255,255,255,0.1); padding-top:15px;">
      <button onclick="closeModal('modal-{{ $account->id }}')" class="btn btn-secondary btn-sm">Close</button>
    </div>
  </div>
</div>
@endforeach

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
  const profileDropdown = document.querySelector('.profile-dropdown');
  const dropdownContent = profileDropdown.querySelector('.profile-dropdown-content');
  profileDropdown.addEventListener('click', (e) => {
    e.stopPropagation();
    dropdownContent.style.display = dropdownContent.style.display === 'block' ? 'none' : 'block';
  });

  const menuToggle = document.getElementById('menuToggle');
  const sidebar = document.getElementById('sidebar');
  menuToggle.addEventListener('click', (e) => {
    e.stopPropagation();
    sidebar.classList.toggle('active');
  });

  document.addEventListener('click', (e) => {
    dropdownContent.style.display = 'none';
    if (window.innerWidth <= 768 && !sidebar.contains(e.target) && !menuToggle.contains(e.target)) {
      sidebar.classList.remove('active');
    }
  });

  document.getElementById('selectAll').addEventListener('change', function() {
    document.querySelectorAll('.row-check').forEach(cb => cb.checked = this.checked);
  });

  function applyFilters() {
    const search = document.getElementById('searchInput').value.toLowerCase();
    const sex = document.getElementById('sexFilter').value.toLowerCase();
    document.querySelectorAll('#tableBody tr[data-sex]').forEach(row => {
      const matchSearch = row.textContent.toLowerCase().includes(search);
      const matchSex = sex === '' || (row.getAttribute('data-sex') || '') === sex;
      row.style.display = matchSearch && matchSex ? '' : 'none';
    });
  }

  document.getElementById('searchInput').addEventListener('keyup', applyFilters);
  document.getElementById('sexFilter').addEventListener('change', applyFilters);

  function openModal(id) {
    document.getElementById(id).classList.add('show');
    document.body.style.overflow = 'hidden';
  }

  function closeModal(id) {
    document.getElementById(id).classList.remove('show');
    document.body.style.overflow = 'auto';
  }
</script>
</body>
</html>