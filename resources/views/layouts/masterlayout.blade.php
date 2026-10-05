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

    .header {
      position: relative; z-index: 1050; display: flex; align-items: center; justify-content: space-between;
      padding: 15px 30px; background: rgba(255,255,255,0.1); backdrop-filter: blur(15px);
      border-bottom: 1px solid rgba(255,255,255,0.2); box-shadow: 0 4px 20px rgba(0,0,0,0.3);
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
    .logo-link img { width: 45px; }

    .welcome-text { font-weight: 500; letter-spacing: 0.5px; }

    .profile-dropdown { position: relative; cursor: pointer; }
    .profile-dropdown-content {
      display: none; position: absolute; right: 0; background: rgba(30, 30, 30, 0.95);
      backdrop-filter: blur(12px); border: 1px solid rgba(255,255,255,0.2);
      min-width: 180px; border-radius: 10px; z-index: 9999; margin-top: 10px;
    }

    /* FULL-WIDTH CONTENT (no sidebar) */
    .main-content { height: calc(100vh - 81px); }
    .content-area { height: 100%; padding: 30px; overflow-y: auto; }

    .content-area::-webkit-scrollbar { width: 8px; }
    .content-area::-webkit-scrollbar-track { background: transparent; }
    .content-area::-webkit-scrollbar-thumb { background: rgba(255,255,255,0.1); border-radius: 10px; }

    @media (max-width: 991.98px) {
        .welcome-text { font-size: 0.9rem; }
        .content-area { padding: 20px 15px; }
    }
    @media (max-width: 575.98px) {
        .logo-text { font-size: 0.95rem; }
    }
  </style>
</head>
<body>

  @php
      $isStaffSide = in_array(auth()->user()->role, ['staff', 'admin']);
      $homeUrl     = $isStaffSide ? route('staffdashboard') : url('/patientdashboard');
  @endphp

  <div class="header">
    <div class="logo-section">
      <a href="{{ $homeUrl }}" class="logo-link" title="Back to dashboard" aria-label="Back to dashboard">
        <img src="{{ asset('images/SMHLogo.png') }}" alt="SMH Logo">
        <span class="logo-text fw-bold">Subic Med Health</span>
      </a>
    </div>

    <div class="welcome-text d-none d-sm-block">
        @if($isStaffSide)
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
    <div class="content-area" id="contentArea">
      @yield('content')
    </div>
  </div>

  <script>
    function toggleDropdown() {
      const menu = document.getElementById('profileMenu');
      menu.style.display = menu.style.display === 'block' ? 'none' : 'block';
    }

    window.onclick = function(event) {
      if (!event.target.closest('.profile-dropdown')) {
        const menu = document.getElementById('profileMenu');
        if (menu) menu.style.display = 'none';
      }
    }
  </script>
</body>
</html>