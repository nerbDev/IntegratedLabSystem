<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Staff Dashboard - SMH</title>

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">

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
    }

    /* MOBILE VIEW STYLES */
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

    <div class="welcome-text">Welcome, Staff 👨‍⚕️</div>

    <div class="profile-dropdown">
      <i class="bi bi-person-circle" style="font-size: 22px;"></i>
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
      <a href="#"><i class="bi bi-file-earmark-text"></i> System Reports</a>
      <a href="#"><i class="bi bi-file-text"></i> Generate Reports</a>
      <a href="#">
        <i class="bi bi-calendar-check"></i> Appointment Schedule
      </a>
    <a href="{{ route('appointments.requests') }}" class="{{ request()->routeIs('appointments.requests') ? 'active' : '' }}">
      <i class="bi bi-envelope"></i> Appointment Requests
    </a>
      <a href="#"><i class="bi bi-person-badge"></i> Manage Accounts</a>
    </div>

    <div class="content-area">
      @yield('content')

      @if(!View::hasSection('content'))
        <div class="row row-cols-1 row-cols-md-3 g-4">
          <div class="col">
            <div class="card-dashboard">
              <h5>Today’s Schedule</h5>
              <p>5 appointments scheduled today</p>
            </div>
          </div>
          <div class="col">
            <div class="card-dashboard">
              <h5>Pending Requests</h5>
              <p>2 new appointment requests</p>
            </div>
          </div>
          <div class="col">
            <div class="card-dashboard">
              <h5>Completed</h5>
              <p>12 appointments completed this week</p>
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
  </script>

</body>
</html>