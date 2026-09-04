<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Staff Dashboard - SMH</title>

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
      /* Changed to auto to allow the content area to scroll correctly */
      overflow-x: hidden;
      overflow-y: auto;
    }

    /* 🔥 BLURRED BACKGROUND */
    body::before {
      content: "";
      position: fixed;
      width: 100%;
      height: 100%;
      background: url('/images/SMHPhoto.jpg') no-repeat center center/cover;
      filter: blur(12px) brightness(0.6);
      z-index: -2;
    }

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
      /* FIXED POSITIONING */
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
    .welcome-text { font-size: 16px; font-weight: 500; }

    /* MOBILE TOGGLE BUTTON */
    .mobile-toggle {
      display: none;
      background: none;
      border: none;
      color: white;
      font-size: 1.8rem;
      margin-right: 15px;
      cursor: pointer;
    }

    /* PROFILE DROPDOWN */
    .profile-dropdown { position: relative; cursor: pointer; }
    .profile-dropdown-content {
      display: none;
      position: absolute;
      right: 0;
      background: rgba(30, 30, 30, 0.9);
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
      /* Ensure it takes up remaining space */
      min-height: calc(100vh - 81px); 
    }

    /* SIDEBAR */
    .sidebar {
      width: 260px;
      padding: 20px;
      background: rgba(255,255,255,0.1);
      backdrop-filter: blur(15px);
      border-right: 1px solid rgba(255,255,255,0.2);
      display: flex;
      flex-direction: column;
      gap: 10px;
      /* FIXED POSITIONING */
      position: sticky;
      top: 81px;
      height: calc(100vh - 81px);
      transition: transform 0.3s ease-in-out;
      overflow-y: auto;
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

    /* Active State for Sidebar */
    .sidebar a.active, .sidebar a:hover {
      background: rgba(0, 212, 255, 0.3);
      color: #fff;
      transform: translateX(5px);
      border: 1px solid rgba(0, 212, 255, 0.5);
    }

    /* SETTINGS / MANAGE DROPDOWN */
    .sidebar-dropdown-toggle {
      display: flex;
      align-items: center;
      justify-content: space-between;
      cursor: pointer;
    }
    .sidebar-dropdown-toggle i.bi-gear {
      margin-right: 8px;
    }
    .sidebar-dropdown-caret {
      margin-left: auto;
      font-size: 0.75rem;
      transition: transform 0.2s ease;
    }
    .sidebar-dropdown-caret.open {
      transform: rotate(180deg);
    }

    .sidebar-submenu {
      max-height: 0;
      overflow: hidden;
      transition: max-height 0.25s ease;
      display: flex;
      flex-direction: column;
      gap: 8px;
    }
    .sidebar-submenu.open {
      max-height: 320px; /* enough to fit 4 items comfortably */
      margin-top: -2px;
    }
    .sidebar-submenu a {
      padding-left: 40px; /* indent under parent icon+label */
      font-size: 0.88rem;
      opacity: 0.85;
    }
    .sidebar-submenu a.active,
    .sidebar-submenu a:hover {
      opacity: 1;
    }

    /* CONTENT */
    .content-area {
      flex: 1;
      padding: 40px;
    }

    /* GLASS CARDS (For Overview) */
    .card-dashboard {
      background: rgba(255,255,255,0.1);
      backdrop-filter: blur(12px);
      border: 1px solid rgba(255,255,255,0.2);
      border-radius: 12px;
      padding: 20px;
      transition: 0.3s;
      margin-bottom: 20px;
      display: flex;
      flex-direction: column;
    }
    .card-dashboard:hover {
      transform: translateY(-5px);
      box-shadow: 0 8px 20px rgba(0,0,0,0.4);
    }

    /* ---------- TYPOGRAPHY ---------- */
    .card-dashboard h5,
    .kpi-value {
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
    .icon-blue   { background: rgba(0,212,255,0.2); color: #00d4ff; }
    .icon-amber  { background: rgba(255,212,59,0.22); color: #ffe066; }
    .icon-green  { background: rgba(105,219,124,0.22); color: #8ce99a; }
    .icon-purple { background: rgba(177,151,252,0.22); color: #b197fc; }

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
      background: rgba(0,212,255,0.22);
      border-color: rgba(0,212,255,0.4);
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
    .schedule-activity-row {
      display: grid;
      grid-template-columns: 1fr 1.4fr;
      gap: 20px;
    }

    /* MOBILE VIEW STYLES */
    @media (max-width: 992px) {
      .schedule-activity-row {
        grid-template-columns: 1fr;
      }
    }

    @media (max-width: 768px) {
      .mobile-toggle { display: block; }
      
      .welcome-text { display: none; } /* Hide text on small screens for space */

      .sidebar {
        position: fixed;
        left: 0;
        top: 81px;
        z-index: 1050;
        transform: translateX(-100%);
        width: 280px;
        /* MOBILE GLASSMORPHISM */
        background: rgba(255, 255, 255, 0.1);
        backdrop-filter: blur(25px);
        box-shadow: 10px 0 30px rgba(0,0,0,0.5);
      }

      .sidebar.active {
        transform: translateX(0);
      }

      .content-area {
        padding: 20px;
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
      <button class="mobile-toggle" id="menuToggle">
        <i class="bi bi-list"></i>
      </button>
      <img src="{{ asset('images/SMHLogo.png') }}">
      <span class="logo-text">Subic Med Health</span>
    </div>

 

    <div class="profile-dropdown">
      <i class="bi bi-person-circle" style="font-size: 22px;"></i>
      <div class="profile-dropdown-content">
       <a href="{{ route('staff.profile.show') }}">Profile</a>
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
      <a href="{{ route('staff.reports.index') }}" class="{{ request()->routeIs('staff.reports.index') ? 'active' : '' }}">
          <i class="bi bi-clipboard2-pulse"></i> System Reports
      </a>
      <a href="{{ route('staff.transactions') }}" class="{{ request()->routeIs('staff.transactions') ? 'active' : '' }}">
        <i class="bi bi-clock-history me-2"></i> My Transactions
      </a>
    <a href="{{ route('staff.appointments.approved') }}" class="{{ request()->routeIs('staff.appointments.approved') ? 'active' : '' }}">
        <i class="bi bi-calendar-check"></i> Appointment Schedule
    </a>
    <a href="{{ route('appointments.requests') }}" class="{{ request()->routeIs('appointments.requests') ? 'active' : '' }}">
      <i class="bi bi-envelope"></i> Appointment Requests
    </a>

      {{-- Settings / Manage dropdown --}}
      @php
          $settingsRoutes = [
              'staff.settings.promo',
              'staff.settings.package',
              'staff.settings.price',
              'staff.settings.unavailable',
          ];
          $settingsActive = request()->routeIs($settingsRoutes);
      @endphp

      <a href="#" class="sidebar-dropdown-toggle {{ $settingsActive ? 'active' : '' }}"
         onclick="event.preventDefault(); toggleSidebarDropdown(this)">
          <i class="bi bi-gear"></i> Settings/ Manage
          <i class="bi bi-chevron-down sidebar-dropdown-caret {{ $settingsActive ? 'open' : '' }}"></i>
      </a>

      <div class="sidebar-submenu {{ $settingsActive ? 'open' : '' }}">
          <a href="{{ route('staff.settings.promo') }}" class="{{ request()->routeIs('staff.settings.promo') ? 'active' : '' }}">
              <i class="bi bi-megaphone"></i> Add Promo
          </a>
          <a href="{{ route('staff.settings.package') }}" class="{{ request()->routeIs('staff.settings.package') ? 'active' : '' }}">
              <i class="bi bi-box-seam"></i> Add Package Type
          </a>
          <a href="{{ route('staff.settings.price') }}" class="{{ request()->routeIs('staff.settings.price') ? 'active' : '' }}">
              <i class="bi bi-tag"></i> Modify Price
          </a>
          <a href="{{ route('staff.settings.unavailable') }}" class="{{ request()->routeIs('staff.settings.unavailable') ? 'active' : '' }}">
              <i class="bi bi-calendar-x"></i> Block Unavailable Days
          </a>
      </div>
    </div>

    <div class="content-area">
      @yield('content')

      @if(!View::hasSection('content'))
      <div class="dashboard-stack">

        <!-- QUICK ACTION SHORTCUTS -->
        <div class="quick-actions-row">
          <a href="{{ route('appointments.requests') }}" class="btn-glass">
            <i class="bi bi-envelope"></i> Appointment Requests
          </a>
          <a href="{{ route('staff.appointments.approved') }}" class="btn-glass">
            <i class="bi bi-calendar-check"></i> Appointment Schedule
          </a>
          <a href="{{ route('staff.reports.index') }}" class="btn-glass">
            <i class="bi bi-clipboard2-pulse"></i> System Reports
          </a>
          <a href="{{ route('staff.transactions') }}" class="btn-glass">
            <i class="bi bi-clock-history"></i> My Transactions
          </a>
          <a href="{{ route('staff.settings.unavailable') }}" class="btn-glass">
            <i class="bi bi-calendar-x"></i> Block Unavailable Days
          </a>
        </div>

        <!-- KPI CARDS -->
        <div class="kpi-row">
          <div class="card-dashboard" style="flex-direction:row; align-items:center; gap:14px;">
            <div class="kpi-icon-circle icon-blue"><i class="bi bi-calendar-day"></i></div>
            <div>
              <p class="kpi-value">{{ number_format($todaysSchedule) }}</p>
              <p class="kpi-label">Today's Schedule</p>
            </div>
          </div>
          <div class="card-dashboard" style="flex-direction:row; align-items:center; gap:14px;">
            <div class="kpi-icon-circle icon-amber"><i class="bi bi-envelope"></i></div>
            <div>
              <p class="kpi-value">{{ number_format($pendingRequests) }}</p>
              <p class="kpi-label">Pending Requests</p>
            </div>
          </div>
          <div class="card-dashboard" style="flex-direction:row; align-items:center; gap:14px;">
            <div class="kpi-icon-circle icon-green"><i class="bi bi-check-circle-fill"></i></div>
            <div>
              <p class="kpi-value">{{ number_format($completedThisWeek) }}</p>
              <p class="kpi-label">Completed This Week</p>
            </div>
          </div>
          <div class="card-dashboard" style="flex-direction:row; align-items:center; gap:14px;">
            <div class="kpi-icon-circle icon-purple"><i class="bi bi-calendar-check"></i></div>
            <div>
              <p class="kpi-value">{{ number_format($upcomingApproved) }}</p>
              <p class="kpi-label">Upcoming Approved</p>
            </div>
          </div>
        </div>

        <!-- CHARTS ROW -->
        <div class="charts-row">
          <div class="card-dashboard">
            <div class="card-dashboard-header">
              <i class="bi bi-pie-chart-fill" style="color:#00d4ff;"></i>
              <h5>Appointment Status Breakdown</h5>
            </div>
            <hr class="card-divider">
            <div class="card-dashboard-body">
              <canvas id="appointmentStatusChart" height="220"></canvas>
            </div>
          </div>

          <div class="card-dashboard">
            <div class="card-dashboard-header">
              <i class="bi bi-bar-chart-line-fill" style="color:#ffe066;"></i>
              <h5>This Week's Schedule</h5>
            </div>
            <hr class="card-divider">
            <div class="card-dashboard-body">
              <canvas id="weekScheduleChart" height="220"></canvas>
            </div>
          </div>

          <div class="card-dashboard">
            <div class="card-dashboard-header">
              <i class="bi bi-house-heart-fill" style="color:#8ce99a;"></i>
              <h5>Home vs Clinic Visits</h5>
            </div>
            <hr class="card-divider">
            <div class="card-dashboard-body">
              <canvas id="homeClinicChart" height="220"></canvas>
            </div>
          </div>
        </div>

        <!-- RECENT ACTIVITY -->
        <div class="schedule-activity-row" style="grid-template-columns: 1fr;">
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
      @endif
    </div>

  </div>

  <script>
    // Profile Dropdown
    const profileDropdown = document.querySelector('.profile-dropdown');
    const dropdownContent = profileDropdown.querySelector('.profile-dropdown-content');

    profileDropdown.addEventListener('click', (e) => {
      e.stopPropagation();
      dropdownContent.style.display = dropdownContent.style.display === 'block' ? 'none' : 'block';
    });

    // Mobile Sidebar Toggle
    const menuToggle = document.getElementById('menuToggle');
    const sidebar = document.getElementById('sidebar');

    menuToggle.addEventListener('click', (e) => {
      e.stopPropagation();
      sidebar.classList.toggle('active');
    });

    // Global Click Listener to close menus
    document.addEventListener('click', (e) => {
      // Close profile dropdown
      dropdownContent.style.display = 'none';
      
      // Close sidebar on mobile if clicking outside
      if (window.innerWidth <= 768 && !sidebar.contains(e.target) && !menuToggle.contains(e.target)) {
        sidebar.classList.remove('active');
      }
    });

    // Settings / Manage sidebar dropdown
    function toggleSidebarDropdown(el) {
      const submenu = el.nextElementSibling;
      const caret = el.querySelector('.sidebar-dropdown-caret');
      submenu.classList.toggle('open');
      caret.classList.toggle('open');
    }
  </script>

  @if(!View::hasSection('content'))
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
          backgroundColor: ['#4dabf7', '#00d4ff', '#b197fc', '#69db7c', '#ff8787', '#63e6be']
        }]
      },
      options: { plugins: { legend: { position: 'bottom' } } }
    });

    // This Week's Schedule
    new Chart(document.getElementById('homeClinicChart'), {
      type: 'doughnut',
      data: {
        labels: ['Home Service', 'Online Booking'],
        datasets: [{
          data: [{{ $homeCount }}, {{ $clinicCount }}],
          backgroundColor: ['#8ce99a', '#4dabf7']
        }]
      },
      options: { plugins: { legend: { position: 'bottom' } } }
    });

    // Home vs Clinic
    new Chart(document.getElementById('homeClinicChart'), {
      type: 'doughnut',
      data: {
        labels: ['Home Service', 'Clinic Visit'],
        datasets: [{
          data: [{{ $homeCount }}, {{ $clinicCount }}],
          backgroundColor: ['#8ce99a', '#4dabf7']
        }]
      },
      options: { plugins: { legend: { position: 'bottom' } } }
    });
  </script>
  @endif

</body>
</html>