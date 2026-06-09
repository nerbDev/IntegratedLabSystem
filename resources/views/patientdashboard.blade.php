<!DOCTYPE html> 
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Patient Dashboard - SMH</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">

  <style>
    body {
      margin: 0;
      font-family: Arial, sans-serif;
      color: #fff;
      min-height: 100vh;
      /* Changed to auto for better scrolling behavior with fixed/sticky elements */
      overflow-x: hidden;
      overflow-y: auto;
    }

    body::before {
      content: "";
      position: fixed;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background: url('/images/SMHPhoto.jpg') no-repeat center center/cover;
      filter: blur(12px) brightness(0.6);
      z-index: -2;
    }

    body::after {
      content: "";
      position: fixed;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background: rgba(0,0,0,0.4);
      z-index: -1;
    }

    /* HEADER */
    .header {
      /* FIXED HEADER */
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

    /* MOBILE TOGGLE */
    .mobile-toggle {
      display: none;
      background: none;
      border: none;
      color: white;
      font-size: 1.8rem;
      margin-right: 15px;
      cursor: pointer;
    }

    /* MAIN CONTENT */
    .main-content { display: flex; min-height: calc(100vh - 81px); }

    /* SIDEBAR */
    .sidebar {
      width: 250px;
      padding: 20px;
      background: rgba(255,255,255,0.1);
      backdrop-filter: blur(15px);
      border-right: 1px solid rgba(255,255,255,0.2);
      display: flex;
      flex-direction: column;
      gap: 10px;
      /* FIXED SIDEBAR */
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

    /* CONTENT AREA */
    .content-area { flex: 1; padding: 30px; }

    /* GLASS CARDS */
    .card-dashboard {
      background: rgba(255,255,255,0.1);
      backdrop-filter: blur(12px);
      border: 1px solid rgba(255,255,255,0.2);
      border-radius: 12px;
      padding: 25px;
      margin-bottom: 20px;
      transition: 0.3s;
    }
    .card-dashboard:hover { transform: translateY(-5px); box-shadow: 0 8px 20px rgba(0,0,0,0.4); }

    .btn-action {
      background: #00d4ff;
      border: none;
      color: #000;
      font-weight: bold;
      padding: 10px 20px;
      border-radius: 8px;
      text-decoration: none;
      display: inline-block;
      margin-top: 10px;
    }
    .btn-action:hover { background: #00b0d4; color: #fff; }

    /* MOBILE VIEW STYLES */
    @media (max-width: 768px) {
      .mobile-toggle { display: block; }
      
      .welcome-text { display: none; }

      .sidebar {
        position: fixed;
        left: 0;
        top: 81px;
        z-index: 1050;
        transform: translateX(-100%);
        width: 280px;
        /* MOBILE GLASSMORPHISM */
        background: rgba(255, 255, 255, 0.15);
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
    <a href="#" class="{{ request()->is('patient/dashboard') ? 'active' : '' }}"><i class="bi bi-speedometer2"></i> Dashboard</a>
    <a href="{{ route('appointment.form') }}"><i class="bi bi-calendar-plus"></i> Book Appointment</a>
    <a href="{{ route('patient.appointments') }}"><i class="bi bi-clock-history"></i> Pending Appointment</a>
    
    <!-- MODIFIED: Linked to your compiled results blade -->
    <a href="{{ route('patient.results.index') }}" class="{{ request()->routeIs('patient.results.index') ? 'active' : '' }}">
      <i class="bi bi-file-earmark-check"></i> Laboratory Results
    </a>
    
    <a href="{{ route('patient.settings') }}"><i class="bi bi-person-gear"></i> Account Settings</a>
  </div>

  <div class="content-area">
    @yield('patientcontent') <!-- If you use this as a layout -->
    
    <div class="row">
        <div class="col-md-6">
            <div class="card-dashboard">
                <h5>Book a Laboratory Test</h5>
                <p>Schedule your blood tests, urinalysis, or X-rays easily.</p>
                <a href="{{ route('appointment.form') }}" class="btn-action">Start Booking</a>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card-dashboard">
                <h5>Laboratory Results</h5>
                <p>View and download your official medical findings once released.</p>
                <!-- MODIFIED: Linked to your compiled results blade -->
                <a href="{{ route('patient.results.index') }}" class="btn-action" style="background: #fff; color: #000;">View Results</a>
            </div>
        </div>

        <div class="col-md-12">
            <div class="card-dashboard">
                <h5>Recent Activity</h5>
                <table class="table table-transparent text-white mt-3">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Service</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>--</td>
                            <td>No recent appointments</td>
                            <td>--</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
  </div>
</div>

<script>
  const menuToggle = document.getElementById('menuToggle');
  const sidebar = document.getElementById('sidebar');

  menuToggle.addEventListener('click', (e) => {
    e.stopPropagation();
    sidebar.classList.toggle('active');
  });

  // Close sidebar when clicking outside on mobile
  document.addEventListener('click', (e) => {
    if (window.innerWidth <= 768 && !sidebar.contains(e.target) && !menuToggle.contains(e.target)) {
      sidebar.classList.remove('active');
    }
  });
</script>

</body>
</html>