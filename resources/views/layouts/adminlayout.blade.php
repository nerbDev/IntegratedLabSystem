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

    /* LOGO = RETURN BUTTON */
    .logo-section { display: flex; align-items: center; }
    .logo-link {
        display: flex; align-items: center; gap: 10px;
        padding: 4px 10px 4px 4px; border-radius: 12px;
        color: #fff; text-decoration: none; cursor: pointer;
        transition: background 0.25s ease, transform 0.15s ease;
        -webkit-tap-highlight-color: transparent;
    }
    .logo-link:hover { background: rgba(255,255,255,0.15); color: #fff; }
    .logo-link:active { transform: scale(0.96); }
    .logo-link:focus-visible { outline: 2px solid #00d4ff; outline-offset: 2px; }
    .logo-link img { width: 50px; }

    .profile-dropdown { position: relative; cursor: pointer; }
    .profile-dropdown-content { display: none; position: absolute; right: 0; background: rgba(20,20,20,0.9); backdrop-filter: blur(12px); border: 1px solid rgba(255,255,255,0.2); min-width: 160px; border-radius: 10px; z-index: 9999; }
    .profile-dropdown-content a, .profile-dropdown-content button { color: #fff; padding: 10px; display: block; text-decoration: none; width: 100%; text-align: left; background: none; border: none; }
    .profile-dropdown-content a:hover { background: rgba(255,255,255,0.2); }

    /* FULL-WIDTH CONTENT (no sidebar) */
    .main-content { min-height: calc(100vh - 80px); }
    .content-area { padding: 30px; }

    .dashboard-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 20px; }
    .card-dashboard { background: rgba(255,255,255,0.1); backdrop-filter: blur(12px); border: 1px solid rgba(255,255,255,0.2); border-radius: 12px; padding: 20px; }

    .status-card { background: rgba(255,255,255,0.05); backdrop-filter: blur(10px); border: 1px solid rgba(255,255,255,0.1); border-radius: 20px; padding: 20px; margin-bottom: 15px; }
    .badge-pending { background: #ffc107; color: #000; }
    .badge-approved { background: #198754; color: #fff; }

    @media (max-width: 768px) {
        .content-area { padding: 15px; }
        .logo-text { font-size: 0.95rem; }
    }
  </style>
</head>
<body>
  <div class="header">
    <div class="logo-section">
      <a href="{{ url('/admindashboard') }}" class="logo-link" title="Back to dashboard" aria-label="Back to dashboard">
        <img src="{{ asset('images/SMHLogo.png') }}" alt="SMH Logo">
        <span class="logo-text">Subic Med Health | Admin</span>
      </a>
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

    // Close dropdown when clicking outside
    document.addEventListener('click', function() {
      document.getElementById('profileMenu').style.display = 'none';
    });
  </script>
</body>
</html>