<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin Dashboard - SMH</title>

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@500;600;700&display=swap" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.4/dist/chart.umd.min.js"></script>

  <style>
    body {
      margin: 0;
      font-family: Arial, sans-serif;
      color: #fff;
      min-height: 100vh;
      overflow: auto; 
    }

    /* 🔥 BACKGROUND IMAGE */
    body::before {
      content: "";
      position: fixed;
      width: 100%;
      height: 100%;
      background: url('/images/SMHPhoto.jpg') no-repeat center center/cover;
      filter: blur(12px) brightness(0.6);
      z-index: -2;
    }

    /* DARK OVERLAY */
    body::after {
      content: "";
      position: fixed;
      width: 100%;
      height: 100%;
      background: rgba(0,0,0,0.4);
      z-index: -1;
    }

    /* HEADER */
    .header {
      position: sticky;
      top: 0;
      z-index: 1000;
      display: flex;
      align-items: center;
      justify-content: space-between;
      padding: 15px 30px;
      background: rgba(255,255,255,0.1);
      backdrop-filter: blur(15px);
      border-bottom: 1px solid rgba(255,255,255,0.2);
      box-shadow: 0 4px 20px rgba(0,0,0,0.3);
    }

    /* HAMBURGER BUTTON */
    .menu-toggle {
      display: none;
      background: none;
      border: none;
      color: white;
      font-size: 1.8rem;
      cursor: pointer;
      margin-right: 15px;
    }

    .logo-section {
      display: flex;
      align-items: center;
      gap: 10px;
    }

    .logo-section img { width: 50px; }
    .logo-text { font-weight: bold; font-size: 18px; }

    /* PROFILE DROPDOWN */
    .profile-dropdown { position: relative; cursor: pointer; }
    .profile-dropdown-content {
      display: none;
      position: absolute;
      right: 0;
      background: rgba(255,255,255,0.1);
      backdrop-filter: blur(12px);
      border: 1px solid rgba(255,255,255,0.2);
      min-width: 160px;
      border-radius: 10px;
      overflow: hidden;
      z-index: 9999;
    }
    .profile-dropdown-content a {
      color: #fff;
      padding: 10px;
      display: block;
      text-decoration: none;
      transition: 0.3s;
    }
    .profile-dropdown-content a:hover {
      background: rgba(255,255,255,0.3);
      color: #000;
    }

    /* MAIN */
    .main-content {
      display: flex;
      min-height: calc(100vh - 80px); 
      align-items: stretch;
    }

    /* SIDEBAR */
    .sidebar {
      width: 220px;
      padding: 20px;
      background: rgba(255,255,255,0.1);
      backdrop-filter: blur(15px);
      border-right: 1px solid rgba(255,255,255,0.2);
      display: flex;
      flex-direction: column;
      gap: 10px;
      position: sticky;
      top: 80px; 
      height: calc(100vh - 80px);
      overflow-y: auto;
      transition: transform 0.3s ease-in-out;
    }

    .sidebar h3 { text-align: center; margin-bottom: 20px; }
    .sidebar a {
      color: #fff;
      text-decoration: none;
      padding: 12px;
      border-radius: 8px;
      background: rgba(255,255,255,0.15);
      transition: 0.3s;
    }
    .sidebar a:hover {
      background: rgba(255,255,255,0.35);
      color: #000;
      transform: translateX(5px);
    }

    /* CONTENT */
    .content-area {
      flex: 1;
      padding: 40px;
      align-content: start;
    }

    /* GLASS CARDS */
    .card-dashboard {
      position: relative;
      z-index: 1;
      background: rgba(255,255,255,0.1);
      backdrop-filter: blur(12px);
      border: 1px solid rgba(255,255,255,0.2);
      border-radius: 12px;
      padding: 20px;
      transition: 0.3s;
      display: flex;
      flex-direction: column;
    }
    .card-dashboard:hover {
      transform: translateY(-5px);
      box-shadow: 0 8px 20px rgba(0,0,0,0.4);
    }

    /* ---------- TYPOGRAPHY ---------- */
    .card-dashboard h5,
    .kpi-value,
    .section-label {
      font-family: 'Poppins', Arial, sans-serif;
    }

    /* ---------- CARD HEADER + DIVIDER ---------- */
    .card-dashboard-header {
      display: flex;
      align-items: center;
      gap: 10px;
    }
    .card-dashboard-header i {
      font-size: 1.25rem;
      line-height: 1;
    }
    .card-dashboard-header h5 {
      margin: 0;
      font-weight: 600;
      font-size: 0.92rem;
      letter-spacing: 0.2px;
      color: rgba(255,255,255,0.92);
    }
    .card-divider {
      border: none;
      border-top: 1px solid rgba(255,255,255,0.22);
      margin: 12px 0 16px;
    }
    .card-dashboard-body { flex: 1; }

    /* ---------- KPI ICON CIRCLES ---------- */
    .kpi-icon-circle {
      width: 42px;
      height: 42px;
      min-width: 42px;
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
    }
    .icon-blue   { background: rgba(77,171,247,0.22); color: #74c0fc; }
    .icon-amber  { background: rgba(255,212,59,0.22); color: #ffe066; }
    .icon-red    { background: rgba(255,135,135,0.22); color: #ff9a9a; }
    .icon-green  { background: rgba(105,219,124,0.22); color: #8ce99a; }

    .kpi-value {
      font-weight: 700;
      font-size: 1.9rem;
      margin: 0;
      color: #fff;
    }
    .kpi-label {
      font-size: 0.82rem;
      color: rgba(255,255,255,0.75);
      margin: 2px 0 0;
    }

    /* ---------- GLASS BUTTONS (quick actions) ---------- */
    .btn-glass {
      background: rgba(255,255,255,0.14);
      backdrop-filter: blur(10px);
      border: 1px solid rgba(255,255,255,0.25);
      color: #fff;
      padding: 10px 16px;
      border-radius: 10px;
      font-size: 0.85rem;
      font-weight: 500;
      text-decoration: none;
      display: inline-flex;
      align-items: center;
      gap: 6px;
      transition: 0.25s ease;
    }
    .btn-glass:hover {
      background: rgba(255,255,255,0.28);
      color: #fff;
      transform: translateY(-2px);
    }

    /* ---------- GLASS TABLE (recent activity) ---------- */
    .table-glass {
      width: 100%;
      border-collapse: collapse;
      font-size: 0.85rem;
    }
    .table-glass thead th {
      font-family: 'Poppins', Arial, sans-serif;
      font-weight: 600;
      font-size: 0.78rem;
      color: rgba(255,255,255,0.7);
      text-align: left;
      padding-bottom: 8px;
      border-bottom: 1px solid rgba(255,255,255,0.25);
    }
    .table-glass tbody td {
      padding: 9px 6px;
      border-bottom: 1px solid rgba(255,255,255,0.1);
      color: rgba(255,255,255,0.92);
    }
    .table-glass tbody tr {
      transition: background 0.2s ease;
    }
    .table-glass tbody tr:hover {
      background: rgba(255,255,255,0.08);
    }
    .table-scroll {
      overflow-x: auto;
    }

    /* ---------- GRID LAYOUT WRAPPERS ---------- */
    .dashboard-stack { display: flex; flex-direction: column; gap: 24px; }
    .quick-actions-row { display: flex; flex-wrap: wrap; gap: 12px; }
    .kpi-row {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(210px, 1fr));
      gap: 20px;
    }
    .charts-row {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
      gap: 20px;
    }
    .area-activity-row {
      display: grid;
      grid-template-columns: 1fr 1.4fr;
      gap: 20px;
    }

    /* MOBILE RESPONSIVENESS */
    @media (max-width: 992px) {
      .area-activity-row {
        grid-template-columns: 1fr;
      }
    }

    @media (max-width: 768px) {
      .menu-toggle {
        display: block;
      }

      .sidebar {
        position: fixed;
        left: 0;
        top: 80px; /* Stay below header */
        height: calc(100vh - 80px);
        transform: translateX(-100%);
        z-index: 1500;
        width: 250px;
        
        /* 🔥 GLAMORPHISIZED MOBILE SIDEBAR */
        background: rgba(255, 255, 255, 0.1); 
        backdrop-filter: blur(25px); /* Increased blur for mobile legibility */
        border-right: 1px solid rgba(255, 255, 255, 0.2);
        box-shadow: 10px 0 30px rgba(0, 0, 0, 0.5);
      }

      .sidebar.active {
        transform: translateX(0);
      }

      .content-area {
        padding: 16px;
      }

      .card-dashboard {
        padding: 16px;
      }

      .kpi-row {
        grid-template-columns: repeat(auto-fit, minmax(140px, 1fr));
        gap: 14px;
      }

      .kpi-value {
        font-size: 1.5rem;
      }

      .kpi-icon-circle {
        width: 36px;
        height: 36px;
        min-width: 36px;
      }

      .quick-actions-row .btn-glass {
        flex: 1 1 45%;
        justify-content: center;
      }

      canvas {
        max-height: 200px !important;
      }
    }

    @media (max-width: 480px) {
      .quick-actions-row .btn-glass {
        flex: 1 1 100%;
      }
      .card-dashboard-header h5 {
        font-size: 0.85rem;
      }
    }

  </style>
</head>

<body>

  <div class="header">
    <div class="logo-section">
      <button class="menu-toggle" id="mobile-nav-toggle">
        <i class="bi bi-list"></i>
      </button>
      <img src="{{ asset('images/SMHLogo.png') }}">
      <span class="logo-text">Subic Med Health</span>
    </div>

    <div class="profile-dropdown">
      <span>Admin <i class="bi bi-caret-down-fill"></i></span>
      <div class="profile-dropdown-content">
        <a href="{{ route('admin.profile.show') }}">  Profile </a>
        <form action="{{ route('logout') }}" method="POST">
          @csrf
          <button type="submit" style="width:100%; background:none; border:none; color:#fff; padding:10px;">Logout</button>
        </form>
      </div>
    </div>
  </div>

  <div class="main-content">
    <div class="sidebar" id="sidebar">
      <h3>Menu</h3>
      <a href="{{ route('admin.reports.index') }}" class="nav-link">
    <i class="bi bi-clipboard2-pulse me-2"></i> Weekly Reports
</a>
      <a href="{{ route('admin.appointments.index') }}">
          <i class="bi bi-calendar-event"></i> Appointment Schedule
</a>
<a href="{{ route('admin.activityLogs.index') }}" 
   class="{{ request()->routeIs('activityLogs.index') ? 'active' : '' }}">
   <i class="bi bi-clock-history"></i> Activity Logs
</a>
    <a href="{{ route('admin.transactions') }}" class="{{ request()->routeIs('admin.transactions') ? 'active' : '' }}">
     <i class="bi bi-clock-history me-2"></i> My Transactions
    </a>
    <a href="{{ route('admin.archive.index') }}"><i class="bi bi-archive-fill"></i> Archive Records</a>
    <a href="{{ route('admin.patients.index') }}" class="nav-link {{ Request::routeIs('admin.patients.index') ? 'active' : '' }}"> <i class="bi bi-person-badge"></i> Patients</a>
    <a href="{{ route('admin.uploadResults') }}"><i class="bi bi-cloud-upload me-2"></i> Upload Results</a>
    <a href="{{ route('admin.lab-result.create') }}"><i class="bi bi-journal-plus me-2"></i> Create Result</a>
      <a href="{{ route('admin.users.index') }}"><i class="bi bi-person-badge"></i> User Accounts</a>

    </div>

    <div class="content-area">
    <div class="dashboard-stack">

      <!-- QUICK ACTION SHORTCUTS -->
      <div class="quick-actions-row">
        <a href="{{ route('admin.uploadResults') }}" class="btn-glass">
          <i class="bi bi-cloud-upload"></i> Upload Results
        </a>
        <a href="{{ route('admin.lab-result.create') }}" class="btn-glass">
          <i class="bi bi-journal-plus"></i> Create Result
        </a>
        <a href="{{ route('admin.appointments.index') }}" class="btn-glass">
          <i class="bi bi-calendar-event"></i> Appointments
        </a>
        <a href="{{ route('admin.patients.index') }}" class="btn-glass">
          <i class="bi bi-person-badge"></i> Patients
        </a>
        <a href="{{ route('admin.reports.index') }}" class="btn-glass">
          <i class="bi bi-clipboard2-pulse"></i> Generate Report
        </a>
      </div>

      <!-- KPI CARDS -->
      <div class="kpi-row">
        <div class="card-dashboard" style="flex-direction:row; align-items:center; gap:14px;">
          <div class="kpi-icon-circle icon-blue"><i class="bi bi-people-fill"></i></div>
          <div>
            <p class="kpi-value">{{ number_format($totalPatients) }}</p>
            <p class="kpi-label">Total Patients</p>
          </div>
        </div>
        <div class="card-dashboard" style="flex-direction:row; align-items:center; gap:14px;">
          <div class="kpi-icon-circle icon-amber"><i class="bi bi-hourglass-split"></i></div>
          <div>
            <p class="kpi-value">{{ number_format($pendingAppointments) }}</p>
            <p class="kpi-label">Pending Appointments</p>
          </div>
        </div>
        <div class="card-dashboard" style="flex-direction:row; align-items:center; gap:14px;">
          <div class="kpi-icon-circle icon-red"><i class="bi bi-file-earmark-medical"></i></div>
          <div>
            <p class="kpi-value">{{ number_format($pendingLabResults) }}</p>
            <p class="kpi-label">Pending Lab Results</p>
          </div>
        </div>
        <div class="card-dashboard" style="flex-direction:row; align-items:center; gap:14px;">
          <div class="kpi-icon-circle icon-green"><i class="bi bi-check-circle-fill"></i></div>
          <div>
            <p class="kpi-value">{{ number_format($releasedLabResults) }}</p>
            <p class="kpi-label">Released Results</p>
          </div>
        </div>
      </div>

      <!-- CHARTS ROW -->
      <div class="charts-row">
        <div class="card-dashboard">
          <div class="card-dashboard-header">
            <i class="bi bi-pie-chart-fill" style="color:#74c0fc;"></i>
            <h5>Appointment Status Breakdown</h5>
          </div>
          <hr class="card-divider">
          <div class="card-dashboard-body">
            <canvas id="appointmentStatusChart" height="220"></canvas>
          </div>
        </div>

        <div class="card-dashboard">
          <div class="card-dashboard-header">
            <i class="bi bi-clipboard2-check-fill" style="color:#8ce99a;"></i>
            <h5>Lab Results: Pending vs Released</h5>
          </div>
          <hr class="card-divider">
          <div class="card-dashboard-body">
            <canvas id="labResultChart" height="220"></canvas>
          </div>
        </div>

        <div class="card-dashboard">
          <div class="card-dashboard-header">
            <i class="bi bi-graph-up-arrow" style="color:#ffe066;"></i>
            <h5>Patient Growth (Last 6 Months)</h5>
          </div>
          <hr class="card-divider">
          <div class="card-dashboard-body">
            <canvas id="patientGrowthChart" height="220"></canvas>
          </div>
        </div>
      </div>

      <!-- PATIENTS BY AREA + RECENT ACTIVITY -->
      <div class="area-activity-row">
        <div class="card-dashboard">
          <div class="card-dashboard-header">
            <i class="bi bi-geo-alt-fill" style="color:#ff9a9a;"></i>
            <h5>Patients by Area</h5>
          </div>
          <hr class="card-divider">
          <div class="card-dashboard-body">
            <canvas id="patientsByAreaChart" height="240"></canvas>
          </div>
        </div>

        <div class="card-dashboard">
          <div class="card-dashboard-header">
            <i class="bi bi-clock-history" style="color:#b197fc;"></i>
            <h5>Recent Activity</h5>
          </div>
          <hr class="card-divider">
          <div class="card-dashboard-body table-scroll">
            <table class="table-glass">
              <thead>
                <tr>
                  <th>User</th>
                  <th>Action</th>
                  <th>Module</th>
                  <th>When</th>
                </tr>
              </thead>
              <tbody>
                @forelse($recentActivity as $log)
                  <tr>
                    <td>{{ $log->user_name }}</td>
                    <td>{{ $log->action }}</td>
                    <td>{{ $log->module }}</td>
                    <td>{{ $log->created_at }}</td>
                  </tr>
                @empty
                  <tr><td colspan="4">No recent activity.</td></tr>
                @endforelse
              </tbody>
            </table>
          </div>
        </div>
      </div>

    </div>
    </div>
  </div>

  <script>
    const profileDropdown = document.querySelector('.profile-dropdown');
    const dropdownContent = profileDropdown.querySelector('.profile-dropdown-content');

    profileDropdown.addEventListener('click', (e) => {
      e.stopPropagation();
      dropdownContent.style.display =
        dropdownContent.style.display === 'block' ? 'none' : 'block';
    });

    const menuToggle = document.getElementById('mobile-nav-toggle');
    const sidebar = document.getElementById('sidebar');

    menuToggle.addEventListener('click', (e) => {
      e.stopPropagation();
      sidebar.classList.toggle('active');
    });

    // Close menu when clicking outside
    document.addEventListener('click', (e) => {
      if (!sidebar.contains(e.target) && !menuToggle.contains(e.target)) {
        sidebar.classList.remove('active');
      }
      dropdownContent.style.display = 'none';
    });
  </script>

  <script>
    const chartTextColor = '#ffffff';
    Chart.defaults.color = chartTextColor;
    Chart.defaults.borderColor = 'rgba(255,255,255,0.15)';

    // Appointment Status Breakdown
    new Chart(document.getElementById('appointmentStatusChart'), {
      type: 'doughnut',
      data: {
        labels: @json($appointmentStatusLabels),
        datasets: [{
          data: @json($appointmentStatusData),
          backgroundColor: ['#4dabf7', '#ffd43b', '#69db7c', '#ff8787', '#b197fc', '#63e6be']
        }]
      },
      options: { plugins: { legend: { position: 'bottom' } } }
    });

    // Lab Results: Pending vs Released
    new Chart(document.getElementById('labResultChart'), {
      type: 'doughnut',
      data: {
        labels: @json($labResultLabels),
        datasets: [{
          data: @json($labResultData),
          backgroundColor: ['#ffd43b', '#69db7c']
        }]
      },
      options: { plugins: { legend: { position: 'bottom' } } }
    });

    // Patient Growth
    new Chart(document.getElementById('patientGrowthChart'), {
      type: 'line',
      data: {
        labels: @json($patientGrowthLabels),
        datasets: [{
          label: 'New Patients',
          data: @json($patientGrowthData),
          borderColor: '#4dabf7',
          backgroundColor: 'rgba(77,171,247,0.2)',
          fill: true,
          tension: 0.3
        }]
      },
      options: { scales: { y: { beginAtZero: true, ticks: { precision: 0 } } } }
    });

    // Patients by Area
    new Chart(document.getElementById('patientsByAreaChart'), {
      type: 'bar',
      data: {
        labels: @json($patientsByArea->pluck('municipality')),
        datasets: [{
          label: 'Patients',
          data: @json($patientsByArea->pluck('total')),
          backgroundColor: '#63e6be'
        }]
      },
      options: {
        indexAxis: 'y',
        scales: { x: { beginAtZero: true, ticks: { precision: 0 } } },
        plugins: { legend: { display: false } }
      }
    });
  </script>

</body>
</html>