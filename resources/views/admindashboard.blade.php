<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin Dashboard - SMH</title>

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">

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
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
      gap: 20px;
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
    }
    .card-dashboard:hover {
      transform: translateY(-5px);
      box-shadow: 0 8px 20px rgba(0,0,0,0.4);
    }

    /* MOBILE RESPONSIVENESS */
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
        padding: 20px;
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
      <span>Welcome, Admin 🛠️ <i class="bi bi-caret-down-fill"></i></span>
      <div class="profile-dropdown-content">
        <a href="#">Profile</a>
        <a href="#">Settings</a>
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
      <a href="{{ route('reports.weekly') }}" class="nav-link">
    <i class="bi bi-clipboard2-pulse me-2"></i> Weekly Reports
</a>
      <a href="{{ route('admin.appointments.index') }}">
          <i class="bi bi-calendar-event"></i> Appointments 
      </a>
      <a href="#"><i class="bi bi-clock-history"></i> Activity Logs</a>
      <a href="#"><i class="bi bi-archive"></i> Archived Records</a>
    <a href="{{ route('admin.patients.index') }}" class="nav-link {{ Request::routeIs('admin.patients.index') ? 'active' : '' }}"> <i class="bi bi-person-badge"></i> Patients</a>
    <a href="{{ route('admin.uploadResults') }}"><i class="bi bi-cloud-upload me-2"></i> Upload Results</a>
    <a href="{{ route('admin.lab-result.create') }}"><i class="bi bi-journal-plus me-2"></i> Create Result</a>
      <a href="{{ route('admin.users.index') }}"><i class="bi bi-person-badge"></i> User Accounts</a>

    </div>

    <div class="content-area">
      <div class="card-dashboard">
        <h5>Total Patients</h5>
        <p>1,245 registered patients</p>
      </div>
      <div class="card-dashboard">
        <h5>Pending Lab Results</h5>
        <p>12 lab results awaiting review</p>
      </div>
      <div class="card-dashboard">
        <h5>Recent Activity</h5>
        <p>5 recent updates in patient records</p>
      </div>
      <div class="card-dashboard">
        <h5>System Reports</h5>
        <p>View latest reports generated</p>
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

</body>
</html>