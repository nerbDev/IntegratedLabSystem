<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>@yield('title') - Subic Med Health</title>

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
  <style>
    body { margin: 0; font-family: 'Segoe UI', Arial, sans-serif; color: #fff; height: 100vh; overflow: hidden; }
    body::before {
      content: ""; position: fixed; width: 100%; height: 100%;
      background: url('/images/SMHPhoto.jpg') no-repeat center center/cover;
      filter: blur(12px) brightness(0.6); z-index: -2;
    }
    body::after { content: ""; position: fixed; width: 100%; height: 100%; background: rgba(0,0,0,0.4); z-index: -1; }

    /* HEADER */
    .header {
      position: relative; z-index: 1050; display: flex; align-items: center; justify-content: space-between;
      padding: 15px 30px; background: rgba(255,255,255,0.1); backdrop-filter: blur(15px);
      border-bottom: 1px solid rgba(255,255,255,0.2); box-shadow: 0 4px 20px rgba(0,0,0,0.3);
    }
    .logo-section { display: flex; align-items: center; gap: 10px; }
    .logo-section img { width: 45px; }
    .welcome-text { font-weight: 500; letter-spacing: 0.5px; }
    
    /* MOBILE TOGGLE BUTTON */
    .menu-toggle {
        display: none;
        background: rgba(255, 255, 255, 0.1);
        border: 1px solid rgba(255, 255, 255, 0.2);
        color: white;
        padding: 5px 12px;
        border-radius: 8px;
        font-size: 1.2rem;
        cursor: pointer;
        transition: 0.3s;
    }
    .menu-toggle:hover { background: rgba(255, 255, 255, 0.2); }

    /* DROPDOWN */
    .profile-dropdown { position: relative; cursor: pointer; }
    .profile-dropdown-content {
      display: none; position: absolute; right: 0; background: rgba(30, 30, 30, 0.95);
      backdrop-filter: blur(12px); border: 1px solid rgba(255,255,255,0.2);
      min-width: 180px; border-radius: 10px; z-index: 9999; margin-top: 10px;
    }

    .main-content { display: flex; height: calc(100vh - 81px); position: relative; }

    /* SIDEBAR RESPONSIVE LOGIC */
    .sidebar {
      width: 260px; padding: 25px 20px; background: rgba(255,255,255,0.05);
      backdrop-filter: blur(15px); border-right: 1px solid rgba(255,255,255,0.1);
      display: flex; flex-direction: column; gap: 8px;
      transition: all 0.3s ease;
      z-index: 1040;
    }
    .sidebar a {
      color: rgba(255,255,255,0.8); text-decoration: none; padding: 12px 15px; border-radius: 10px;
      transition: all 0.3s ease; display: flex; align-items: center;
    }
    .sidebar a:hover { background: rgba(255,255,255,0.1); color: #fff; }
    .sidebar a.active { 
        background: rgba(0, 212, 255, 0.2); color: #00d4ff; 
        border: 1px solid rgba(0, 212, 255, 0.3); font-weight: 600;
    }

    .content-area { flex: 1; padding: 30px; overflow-y: auto; transition: 0.3s; }

    /* SCROLLBAR STYLING */
    .content-area::-webkit-scrollbar { width: 8px; }
    .content-area::-webkit-scrollbar-track { background: transparent; }
    .content-area::-webkit-scrollbar-thumb { background: rgba(255,255,255,0.1); border-radius: 10px; }

    /* MOBILE BREAKPOINT (Under 992px) */
    @media (max-width: 991.98px) {
        .menu-toggle { display: block; }
        .sidebar {
            position: absolute;
            left: -260px; /* Hidden state */
            height: 100%;
        }
        .sidebar.show {
            left: 0; /* Visible state */
            box-shadow: 10px 0 30px rgba(0,0,0,0.5);
        }
        .welcome-text { font-size: 0.9rem; }
        .logo-text { display: none; } /* Hide text on small phones to save space */
    }
  </style>
</head>
<body>

  <div class="header">
    <div class="logo-section">
      <button class="menu-toggle" onclick="toggleSidebar()">
          <i class="bi bi-list"></i>
      </button>

      <img src="{{ asset('images/SMHLogo.png') }}" alt="SMH Logo">
      <span class="logo-text fw-bold">Subic Med Health</span>
    </div>

    <div class="welcome-text d-none d-sm-block">
        @if(auth()->user()->role == 'staff' || auth()->user()->role == 'admin')
            Staff Dashboard 👨‍⚕️
        @else
            Patient Portal 🏥
        @endif
    </div>

    <div class="profile-dropdown" onclick="toggleDropdown()">
      <div class="d-flex align-items-center gap-2">
        <span class="small d-none d-md-inline">{{ auth()->user()->first_name }}</span>
        <i class="bi bi-person-circle" style="font-size: 24px;"></i>
      </div>
      <div class="profile-dropdown-content shadow-lg" id="profileMenu">
        <div class="p-3 border-bottom border-secondary">
            <p class="mb-0 small fw-bold">{{ auth()->user()->email }}</p>
            <p class="mb-0 extra-small text-muted text-uppercase">{{ auth()->user()->role }}</p>
        </div>
        <form action="{{ route('logout') }}" method="POST">
          @csrf
          <button type="submit" class="dropdown-item p-3 text-danger d-flex align-items-center">
            <i class="bi bi-box-arrow-right me-2"></i> Logout
          </button>
        </form>
      </div>
    </div>
  </div>

  <div class="main-content">
    <div class="sidebar" id="sidebarMenu">
      <h6 class="text-uppercase small fw-bold opacity-50 mb-3 px-2">Main Menu</h6>

      @if(auth()->user()->role == 'staff' || auth()->user()->role == 'admin')
        <a href="{{ route('staffdashboard') }}" class="{{ request()->routeIs('staffdashboard') ? 'active' : '' }}">
          <i class="bi bi-speedometer2 me-2"></i> Overview
        </a>
        <a href="{{ route('appointments.requests') }}" class="{{ request()->routeIs('requests') ? 'active' : '' }}">
          <i class="bi bi-envelope me-2"></i> Appointment Requests
        </a>
      @else
        <a href="{{ url('/patientdashboard') }}" class="{{ request()->is('patientdashboard') ? 'active' : '' }}">
          <i class="bi bi-house-door me-2"></i> Home
        </a>
        <a href="{{ route('patient.appointments') }}" class="{{ request()->routeIs('patient.appointments') ? 'active' : '' }}">
          <i class="bi bi-calendar3 me-2"></i> My Appointments
        </a>
        <a href="{{ url('/appointment') }}" class="{{ request()->is('appointment.form') ? 'active' : '' }}">
          <i class="bi bi-plus-circle me-2"></i> Book Appointment
        </a>
      @endif
    </div>

    <div class="content-area" id="contentArea">
      @yield('content')
    </div>
  </div>

  <script>
    function toggleSidebar() {
        const sidebar = document.getElementById('sidebarMenu');
        sidebar.classList.toggle('show');
    }

    function toggleDropdown() {
      const menu = document.getElementById('profileMenu');
      menu.style.display = menu.style.display === 'block' ? 'none' : 'block';
    }

    // Close menus when clicking outside
    window.onclick = function(event) {
      // Handle Profile Dropdown
      if (!event.target.closest('.profile-dropdown')) {
        const menu = document.getElementById('profileMenu');
        if (menu) menu.style.display = 'none';
      }

      // Handle Sidebar on Mobile
      const sidebar = document.getElementById('sidebarMenu');
      if (window.innerWidth < 992 && 
          !event.target.closest('#sidebarMenu') && 
          !event.target.closest('.menu-toggle') && 
          sidebar.classList.contains('show')) {
        sidebar.classList.remove('show');
      }
    }
  </script>
</body>
</html>