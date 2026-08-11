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

    .mobile-toggle {
      display: none;
      background: none;
      border: none;
      color: white;
      font-size: 1.8rem;
      margin-right: 15px;
      cursor: pointer;
    }

    .main-content { display: flex; min-height: calc(100vh - 81px); }

    .sidebar {
      width: 250px;
      padding: 20px;
      background: rgba(255,255,255,0.1);
      backdrop-filter: blur(15px);
      border-right: 1px solid rgba(255,255,255,0.2);
      display: flex;
      flex-direction: column;
      gap: 10px;
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

    .content-area { flex: 1; padding: 30px; }

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

    /* ================= CARD STACK (staircase, all 3 visible) ================= */
    .stack-wrapper {
        width: 100%;
        max-width: 1100px;
        margin: 10px auto 40px auto;
    }

    /* 3/4 of the viewport height reserved for the stack */
    .stack-container {
        position: relative;
        width: 100%;
        height: 75vh;
        min-height: 620px;
    }

    .stack-card {
        position: absolute;
        width: 60%;
        height: 54%;
        border-radius: 22px;
        background: rgba(255,255,255,0.1);
        backdrop-filter: blur(18px);
        border: 1px solid rgba(255,255,255,0.2);
        box-shadow: 0 20px 45px rgba(0,0,0,0.4);
        color: #fff;
        overflow-y: auto;
        padding: 26px;
        cursor: pointer;
        will-change: top, left, filter;
        transition:
            top 0.5s cubic-bezier(.2,.8,.2,1),
            left 0.5s cubic-bezier(.2,.8,.2,1),
            filter 0.5s ease,
            box-shadow 0.5s ease;
        transform: rotateX(var(--tiltX, 0deg)) rotateY(var(--tiltY, 0deg));
        transform-style: preserve-3d;
    }

    /* fixed staircase slots — box 1 top-left (largest visual priority),
       box 2 offset down-right, box 3 further down-right, corner touching
       the container's bottom-right edge, all three fully on-screen */
    .stack-card[data-order="0"] { top: 0%;   left: 0%;   z-index: 12; filter: brightness(1); }
    .stack-card[data-order="1"] { top: 23%;  left: 20%;  z-index: 11; filter: brightness(0.88); }
    .stack-card[data-order="2"] { top: 46%;  left: 40%;  z-index: 10; filter: brightness(0.78); }

    .stack-card:not([data-order="0"]) a,
    .stack-card:not([data-order="0"]) button {
        pointer-events: none;
    }

    .stack-card::-webkit-scrollbar { width: 6px; }
    .stack-card::-webkit-scrollbar-thumb { background: rgba(255,255,255,0.25); border-radius: 6px; }

    .stack-card h4 {
        display: flex;
        align-items: center;
        gap: 10px;
        margin-bottom: 16px;
    }

    .stack-hint {
        text-align: center;
        font-size: 0.8rem;
        color: rgba(255,255,255,0.55);
        margin-top: 10px;
    }

    .promo-item {
        background: rgba(255,255,255,0.08);
        border: 1px solid rgba(255,255,255,0.15);
        border-radius: 14px;
        padding: 14px 16px;
        margin-bottom: 12px;
    }
    .promo-item h6 {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 6px;
        font-weight: 700;
        font-size: 0.95rem;
    }
    .promo-price { color: #00d4ff; font-weight: 700; }
    .promo-badge {
        background: rgba(255, 193, 7, 0.2);
        border: 1px solid #ffc107;
        color: #ffc107;
        font-size: 0.65rem;
        padding: 2px 8px;
        border-radius: 20px;
        margin-left: 8px;
    }
    .promo-inclusions {
        font-size: 0.8rem;
        color: rgba(255,255,255,0.7);
        margin: 0;
    }

    .unavailable-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 10px 4px;
        border-bottom: 1px solid rgba(255,255,255,0.1);
        font-size: 0.9rem;
    }
    .unavailable-row:last-child { border-bottom: none; }
    .unavailable-date { font-weight: 700; color: #ff8a94; }
    .unavailable-reason {
        color: rgba(255,255,255,0.65);
        font-size: 0.8rem;
        text-align: right;
    }

    .book-cta {
        height: 100%;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        text-align: center;
    }
    .book-cta i.bi-calendar-plus {
        font-size: 2.6rem;
        color: #00d4ff;
        margin-bottom: 14px;
    }

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
        background: rgba(255, 255, 255, 0.15);
        backdrop-filter: blur(25px);
        box-shadow: 10px 0 30px rgba(0,0,0,0.5);
      }
      .sidebar.active { transform: translateX(0); }
      .content-area { padding: 20px; }

      .stack-container { height: 82vh; min-height: 560px; }

      .stack-card { width: 82%; height: 44%; padding: 18px; }
      .stack-card[data-order="0"] { top: 0%;  left: 0%; }
      .stack-card[data-order="1"] { top: 28%; left: 9%; }
      .stack-card[data-order="2"] { top: 56%; left: 18%; }
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
  <div class="welcome-text">Welcome, {{ auth()->user()->first_name ?? 'Patient' }} 👋</div>
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
    <a href="{{ route('patient.appointments') }}"><i class="bi bi-clock-history"></i> Appointment List </a>
    <a href="{{ route('patient.results.index') }}" class="{{ request()->routeIs('patient.results.index') ? 'active' : '' }}">
      <i class="bi bi-file-earmark-check"></i> Laboratory Results
    </a>
      <a href="{{ route('patient.transactions') }}" class="{{ request()->routeIs('patient.transactions') ? 'active' : '' }}">
        <i class="bi bi-clock-history me-2"></i> My Transactions
      </a>
    <a href="{{ route('patient.accountsetting') }}" class="nav-link {{ Request::routeIs('patient.accountsetting') ? 'active' : '' }}"> <i class="bi bi-person-badge"></i> Account Setting</a>
  </div> 

  <div class="content-area">
    @yield('content')

    {{-- ================= CARD STACK (all 3 visible, staircase) ================= --}}
    <div class="stack-wrapper">
        <div class="stack-container" id="dashboardStack">

            {{-- Box 1 (front by default): Promos / Packages --}}
            <div class="stack-card" data-order="0" data-card="promos">
                <h4><i class="bi bi-stars text-warning"></i> New Packages & Promos</h4>

                @forelse(($packages ?? []) as $package)
                    <div class="promo-item">
                        <h6>
                            <span>{{ $package->name }}
                                @if($package->requires_fasting)
                                    <span class="promo-badge">FASTING REQUIRED</span>
                                @endif
                            </span>
                            <span class="promo-price">₱{{ number_format($package->price, 2) }}</span>
                        </h6>
                        @if($package->inclusions->isNotEmpty())
                            <p class="promo-inclusions">
                                {{ $package->inclusions->pluck('item_name')->join(', ') }}
                            </p>
                        @endif
                        <a href="{{ route('appointment.form', ['package' => $package->id]) }}" class="btn-action" style="padding:6px 14px; font-size:0.8rem; margin-top:4px;">
                            Book This Package
                        </a>
                    </div>
                @empty
                    <p class="text-white-50">No active packages at the moment. Check back soon!</p>
                @endforelse

                <a href="{{ route('appointment.form') }}" class="btn-action mt-2">
                    <i class="bi bi-calendar-check"></i> Browse & Book
                </a>
            </div>

            {{-- Box 2 (middle): Unavailable Dates --}}
            <div class="stack-card" data-order="1" data-card="unavailable">
                <h4><i class="bi bi-calendar-x text-danger"></i> Unavailable Dates</h4>

                @forelse(($unavailableDates ?? []) as $blocked)
                    <div class="unavailable-row">
                        <span class="unavailable-date">{{ $blocked->date->format('M d, Y') }}</span>
                        <span class="unavailable-reason">{{ $blocked->reason ?? 'Not accepting appointments' }}</span>
                    </div>
                @empty
                    <p class="text-white-50">No blocked dates coming up — book anytime!</p>
                @endforelse
            </div>

            {{-- Box 3 (back): Book Appointment shortcut --}}
            <div class="stack-card" data-order="2" data-card="book">
                <div class="book-cta">
                    <i class="bi bi-calendar-plus"></i>
                    <h4 class="justify-content-center">Ready for your next visit?</h4>
                    <p class="text-white-50 mb-3">Book a laboratory appointment in just a few clicks.</p>
                    <a href="{{ route('appointment.form') }}" class="btn-action">
                        <i class="bi bi-calendar-plus"></i> Book Appointment
                    </a>
                </div>
            </div>

        </div>
        <div class="stack-hint">Click a box to bring it to the front</div>
    </div>

    {{-- ================= RECENT ACTIVITY (unchanged) ================= --}}
    <div class="row">
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

  document.addEventListener('click', (e) => {
    if (window.innerWidth <= 768 && !sidebar.contains(e.target) && !menuToggle.contains(e.target)) {
      sidebar.classList.remove('active');
    }
  });
</script>

<script>
(function () {
    const stack = document.getElementById('dashboardStack');
    if (!stack) return;

    const cards = Array.from(stack.querySelectorAll('.stack-card'));

    // subtle tilt only on the front card, purely visual — position/size
    // for all 3 boxes now comes straight from the CSS [data-order] rules
    cards.forEach(card => {
        card.addEventListener('mousemove', (e) => {
            if (card.dataset.order !== '0') return;
            const rect = card.getBoundingClientRect();
            const px = (e.clientX - rect.left) / rect.width - 0.5;
            const py = (e.clientY - rect.top) / rect.height - 0.5;
            card.style.setProperty('--tiltY', (px * 5) + 'deg');
            card.style.setProperty('--tiltX', (py * -5) + 'deg');
        });

        card.addEventListener('mouseleave', () => {
            card.style.setProperty('--tiltX', '0deg');
            card.style.setProperty('--tiltY', '0deg');
        });

        // click a peeking box -> promote to front, shift the rest back
        card.addEventListener('click', () => {
            const clickedOrder = parseInt(card.dataset.order, 10);
            if (clickedOrder === 0) return;

            cards.forEach(c => {
                let order = parseInt(c.dataset.order, 10);
                if (c === card) {
                    order = 0;
                } else if (order < clickedOrder) {
                    order += 1;
                }
                c.dataset.order = order;
            });
        });
    });
})();
</script>

</body>
</html>