<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>@yield('title', 'Admin Dashboard - SMH')</title>

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
  <style>
    body { margin: 0; font-family: Arial, sans-serif; color: #fff; min-height: 100vh; overflow-x: hidden; }
    body::before { content: ""; position: fixed; width: 100%; height: 100%; background: url('/images/SMHPhoto.jpg') no-repeat center center/cover; filter: blur(12px) brightness(0.6); z-index: -2; }
    body::after { content: ""; position: fixed; width: 100%; height: 100%; background: rgba(0,0,0,0.4); z-index: -1; }

    /* FIXED HEADER */
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
    }
    .logo-section { display: flex; align-items: center; gap: 10px; }
    .logo-section img { width: 50px; }

    /* MOBILE TOGGLE */
    .mobile-toggle { display: none; background: none; border: none; color: white; font-size: 1.8rem; margin-right: 10px; }

    .profile-dropdown { position: relative; cursor: pointer; }
    .profile-dropdown-content { display: none; position: absolute; right: 0; background: rgba(20,20,20,0.9); backdrop-filter: blur(12px); border: 1px solid rgba(255,255,255,0.2); min-width: 160px; border-radius: 10px; z-index: 9999; }
    .profile-dropdown-content a, .profile-dropdown-content button { color: #fff; padding: 10px; display: block; text-decoration: none; width: 100%; text-align: left; background: none; border: none; }
    .profile-dropdown-content a:hover { background: rgba(255,255,255,0.2); }

    .main-content { display: flex; min-height: calc(100vh - 80px); }

    /* FIXED SIDEBAR */
    .sidebar { 
        width: 240px; 
        padding: 20px; 
        background: rgba(255,255,255,0.1); 
        backdrop-filter: blur(15px); 
        border-right: 1px solid rgba(255,255,255,0.2); 
        flex-shrink: 0; 
        position: sticky; 
        top: 80px; 
        height: calc(100vh - 80px); 
        transition: transform 0.3s ease-in-out;
    }
    .sidebar a { color: #fff; text-decoration: none; padding: 12px; border-radius: 8px; background: rgba(255,255,255,0.1); display: block; margin-bottom: 8px; transition: 0.3s; }
    .sidebar a:hover { background: rgba(255,255,255,0.3); transform: translateX(5px); }

    .content-area { flex: 1; padding: 30px; }

    .dashboard-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 20px; }
    .card-dashboard { background: rgba(255,255,255,0.1); backdrop-filter: blur(12px); border: 1px solid rgba(255,255,255,0.2); border-radius: 12px; padding: 20px; }
    
    .status-card { background: rgba(255,255,255,0.05); backdrop-filter: blur(10px); border: 1px solid rgba(255,255,255,0.1); border-radius: 20px; padding: 20px; margin-bottom: 15px; }
    .badge-pending { background: #ffc107; color: #000; }
    .badge-approved { background: #198754; color: #fff; }

    /* MOBILE STYLES */
    @media (max-width: 768px) {
        .mobile-toggle { display: block; }
        .sidebar { 
            position: fixed; 
            left: 0; 
            top: 80px; 
            transform: translateX(-100%); 
            z-index: 1050; 
            width: 260px;
            /* MOBILE GLASSMORPHISM background */
            background: rgba(255, 255, 255, 0.15);
            backdrop-filter: blur(25px);
            box-shadow: 10px 0 20px rgba(0,0,0,0.3);
        }
        .sidebar.active { transform: translateX(0); }
        .content-area { padding: 15px; }
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
      <span class="logo-text">Subic Med Health | Admin</span>
    </div>
    <div class="profile-dropdown" id="profileBtn">
      <span>Welcome, Admin 🛠️ <i class="bi bi-caret-down-fill"></i></span>
      <div class="profile-dropdown-content" id="profileMenu">
        <a href="#">Profile</a>
        <form action="{{ route('logout') }}" method="POST">@csrf<button type="submit">Logout</button></form>
      </div>
    </div>
  </div>

  <div class="main-content">
    <div class="sidebar" id="sidebar">
      <h4 class="text-center mb-4">Menu</h4>
      <a href="{{ url('/admindashboard') }}"><i class="bi bi-speedometer2 me-2"></i> Overview</a>
      <a href="{{ route('admin.appointments.index') }}"><i class="bi bi-calendar-event me-2"></i> Appointments</a>
      <a href="#"><i class="bi bi-people me-2"></i> Patients</a>
    </div>
    <div class="content-area">
      @yield('admincontent')
    </div>
  </div>

  <script>
    // Profile Dropdown
    document.getElementById('profileBtn').addEventListener('click', function(e) {
      const menu = document.getElementById('profileMenu');
      menu.style.display = menu.style.display === 'block' ? 'none' : 'block';
      e.stopPropagation();
    });

    // Mobile Sidebar Toggle
    const menuToggle = document.getElementById('menuToggle');
    const sidebar = document.getElementById('sidebar');

    menuToggle.addEventListener('click', function(e) {
      sidebar.classList.toggle('active');
      e.stopPropagation();
    });

    // Close menus when clicking outside
    document.addEventListener('click', function() {
      document.getElementById('profileMenu').style.display = 'none';
      if (window.innerWidth <= 768) {
        sidebar.classList.remove('active');
      }
    });
  </script>
</body>
</html>