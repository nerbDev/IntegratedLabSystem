<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>SMH Laboratory System</title>

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@500;600;700;800&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">

  <style>
    * { scroll-behavior: smooth; }

    :root {
      --navy: #0a2540;
      --navy-deep: #061729;
      --blue: #1868c9;
      --blue-light: #4f9df0;
      --teal: #17b3ac;
      --gold: #f2a900;
      --glass-bg: rgba(255,255,255,0.10);
      --glass-bg-soft: rgba(255,255,255,0.55);
      --glass-border: rgba(255,255,255,0.35);
      --ink: #0f1e33;
      --muted: #5b6b7f;
    }

    body {
      font-family: 'Inter', Arial, sans-serif;
      color: var(--ink);
      overflow-x: hidden;
      background: #f4f8fc;
      position: relative;
    }

    h1, h2, h3, h4, h5, h6 { font-family: 'Poppins', 'Inter', Arial, sans-serif; }

    /* ── AMBIENT BACKDROP (the page's one recurring visual signature) ── */
    .bg-blob {
      position: fixed;
      border-radius: 50%;
      filter: blur(90px);
      z-index: 0;
      pointer-events: none;
    }
    .bg-blob-1 { width: 520px; height: 520px; top: -160px; right: -140px; background: radial-gradient(circle, rgba(24,104,201,0.35), rgba(24,104,201,0) 70%); }
    .bg-blob-2 { width: 460px; height: 460px; bottom: -120px; left: -160px; background: radial-gradient(circle, rgba(23,179,172,0.30), rgba(23,179,172,0) 70%); }
    .bg-blob-3 { width: 380px; height: 380px; bottom: 40%; right: -120px; background: radial-gradient(circle, rgba(242,169,0,0.16), rgba(242,169,0,0) 70%); }

    /* ── NAVBAR (floating, no separating rule) ── */
    .navbar {
      background: rgba(10, 37, 64, 0.35);
      backdrop-filter: blur(16px);
      -webkit-backdrop-filter: blur(16px);
      border: none;
      box-shadow: 0 8px 30px rgba(6,23,41,0.12);
      transition: background 0.4s ease, box-shadow 0.4s ease;
      padding-top: 14px;
      padding-bottom: 14px;
    }
    .navbar.scrolled {
      background: rgba(10, 37, 64, 0.78);
      box-shadow: 0 10px 30px rgba(6,23,41,0.25);
    }
    .navbar-brand {
      display: flex;
      align-items: center;
      gap: 10px;
      font-weight: 700;
      color: white !important;
      letter-spacing: 0.2px;
    }
    .navbar img { height: 38px; }
    .nav-link {
      color: rgba(255,255,255,0.88) !important;
      font-weight: 500;
      position: relative;
      margin: 0 4px;
    }
    .nav-link::after {
      content: '';
      position: absolute;
      bottom: -4px; left: 0;
      width: 0; height: 2px;
      background: var(--gold);
      border-radius: 2px;
      transition: width 0.3s ease;
    }
    .nav-link:hover::after,
    .nav-link.active-section::after { width: 100%; }

    /* ── GLASS BUTTON ── */
    .btn-glass {
      background: rgba(255,255,255,0.14);
      color: white;
      font-weight: 600;
      border-radius: 30px;
      padding: 12px 30px;
      border: 1.5px solid rgba(255,255,255,0.45);
      backdrop-filter: blur(10px);
      transition: 0.3s all;
    }
    .btn-glass:hover {
      border-color: white;
      background: rgba(255,255,255,0.26);
      color: white;
      transform: translateY(-2px);
      box-shadow: 0 8px 25px rgba(0,0,0,0.2);
    }

    /* ── HERO (content shifted left) ── */
    .hero {
      height: 100vh;
      display: flex;
      align-items: center;
      justify-content: flex-start;
      position: relative;
      overflow: hidden;
    }
    .object-fit-cover { object-fit: cover; }
    .hero-content {
      position: relative;
      z-index: 3;
      max-width: 620px;
      text-align: left;
      padding-left: 6vw;
    }
    .hero-eyebrow {
      display: inline-flex;
      align-items: center;
      gap: 8px;
      color: var(--gold);
      font-weight: 600;
      font-size: 0.95rem;
      margin-bottom: 14px;
      letter-spacing: 0.3px;
    }
    .hero-eyebrow .dash { width: 26px; height: 2px; background: var(--gold); display: inline-block; border-radius: 2px; }
    .hero-content h1 {
      font-size: 3.1rem;
      font-weight: 700;
      margin-bottom: 20px;
      line-height: 1.18;
      animation: fadeUp 0.9s ease both;
    }
    .hero-content p {
      font-size: 1.15rem;
      color: #eef2f7;
      margin-bottom: 32px;
      max-width: 480px;
      animation: fadeUp 0.9s 0.2s ease both;
    }
    .hero-content .btn-glass {
      animation: fadeUp 0.9s 0.4s ease both;
    }
    @keyframes fadeUp {
      from { opacity: 0; transform: translateY(30px); }
      to   { opacity: 1; transform: translateY(0); }
    }

    /* ── SCROLL ARROW ── */
    .scroll-down {
      position: absolute;
      bottom: 30px; left: 50%;
      transform: translateX(-50%);
      z-index: 3;
      font-size: 28px;
      color: white;
      animation: bounce 2s infinite;
      cursor: pointer;
      opacity: 0.85;
    }
    @keyframes bounce {
      0%,20%,50%,80%,100% { transform: translate(-50%,0); }
      40%  { transform: translate(-50%,10px); }
      60%  { transform: translate(-50%,5px); }
    }

    /* ── SECTION BASE ── */
    .page-section { padding: 100px 0 90px; position: relative; z-index: 1; }
    .section-title {
      font-size: 2.1rem;
      font-weight: 700;
      color: var(--navy);
      margin-bottom: 10px;
    }
    .section-divider {
      width: 54px; height: 4px;
      background: linear-gradient(90deg, var(--blue), var(--teal));
      border-radius: 2px;
      margin: 0 auto 36px;
    }

    /* ── GLASS CARD (shared) ── */
    .glass-card {
      background: var(--glass-bg-soft);
      backdrop-filter: blur(16px);
      -webkit-backdrop-filter: blur(16px);
      border: 1px solid var(--glass-border);
      border-radius: 18px;
      box-shadow: 0 12px 34px rgba(10,37,64,0.10);
    }

    /* ── ABOUT ── */
    #about { background: transparent; }
    .about-card {
      display: flex;
      gap: 16px;
      align-items: flex-start;
      padding: 22px 24px;
      margin-bottom: 16px;
      transition: transform 0.3s, box-shadow 0.3s, background 0.3s;
    }
    .about-card:hover {
      transform: translateY(-4px);
      box-shadow: 0 18px 40px rgba(10,37,64,0.14);
      background: rgba(255,255,255,0.72);
    }
    .about-card .icon-circle {
      flex-shrink: 0;
      width: 46px; height: 46px;
      border-radius: 50%;
      background: linear-gradient(135deg, var(--blue), var(--teal));
      display: flex; align-items: center; justify-content: center;
      color: white; font-size: 20px;
    }
    .about-card h5 { font-weight: 700; color: var(--navy); margin-bottom: 6px; }
    .about-card p { color: var(--muted); font-size: 0.95rem; }

    .about-map-wrap {
      border-radius: 18px;
      overflow: hidden;
      border: 1px solid var(--glass-border);
      box-shadow: 0 12px 34px rgba(10,37,64,0.12);
    }
    .about-map-wrap iframe { width: 100%; height: 300px; display: block; border: 0; }

    .stat-badge {
      text-align: center;
      padding: 22px 10px;
    }
    .stat-badge .num {
      font-size: 2.2rem;
      font-weight: 800;
      background: linear-gradient(135deg, var(--blue), var(--teal));
      -webkit-background-clip: text;
      background-clip: text;
      color: transparent;
    }
    .stat-badge p { color: var(--muted); font-size: 0.85rem; margin: 4px 0 0; }

    /* ── SERVICES CAROUSEL ── */
    #services { background: transparent; }
    .services-carousel-wrap { position: relative; }
    .services-track {
      display: flex;
      gap: 22px;
      overflow-x: auto;
      scroll-snap-type: x mandatory;
      padding: 8px 6px 26px;
      scrollbar-width: none;
    }
    .services-track::-webkit-scrollbar { display: none; }
    .service-card {
      scroll-snap-align: start;
      flex: 0 0 300px;
      background: var(--glass-bg-soft);
      backdrop-filter: blur(16px);
      -webkit-backdrop-filter: blur(16px);
      border: 1px solid var(--glass-border);
      border-radius: 18px;
      overflow: hidden;
      transition: transform 0.35s ease, box-shadow 0.35s ease;
    }
    .service-card:hover {
      transform: translateY(-8px);
      box-shadow: 0 20px 44px rgba(10,37,64,0.18);
    }
    .service-img {
      height: 150px;
      background: linear-gradient(135deg, rgba(24,104,201,0.85), rgba(23,179,172,0.85));
      display: flex; align-items: center; justify-content: center;
      position: relative;
      overflow: hidden;
    }
    .service-img img { width: 100%; height: 100%; object-fit: cover; }
    .service-img i {
      font-size: 40px;
      color: white;
      position: relative;
      z-index: 1;
    }
    .service-body { padding: 22px 22px 26px; }
    .service-body h5 { font-weight: 700; color: var(--navy); margin-bottom: 8px; }
    .service-body p { font-size: 0.9rem; color: var(--muted); margin: 0; }

    .carousel-arrow {
      width: 46px; height: 46px;
      border-radius: 50%;
      background: rgba(255,255,255,0.6);
      backdrop-filter: blur(10px);
      border: 1px solid var(--glass-border);
      color: var(--navy);
      display: flex; align-items: center; justify-content: center;
      transition: 0.25s;
      flex-shrink: 0;
    }
    .carousel-arrow:hover { background: var(--navy); color: white; }
    .services-controls { display: flex; justify-content: center; gap: 14px; margin-top: 4px; }

    /* ── CONTACT (glassmorphism) ── */
    #contact {
      background: linear-gradient(135deg, rgba(10,37,64,0.92), rgba(24,104,201,0.85));
      position: relative;
      overflow: hidden;
    }
    #contact .section-title { color: white; }
    #contact > .container { position: relative; z-index: 1; }
    #contact .lead-sub { color: rgba(255,255,255,0.78); }

    .contact-panel {
      background: rgba(255,255,255,0.08);
      backdrop-filter: blur(18px);
      -webkit-backdrop-filter: blur(18px);
      border: 1px solid rgba(255,255,255,0.22);
      border-radius: 20px;
      padding: 40px;
    }
    .contact-info-item {
      display: flex;
      align-items: flex-start;
      gap: 16px;
      margin-bottom: 22px;
    }
    .contact-info-item .icon-wrap {
      width: 44px; height: 44px;
      background: rgba(255,255,255,0.14);
      border: 1px solid rgba(255,255,255,0.3);
      border-radius: 50%;
      display: flex; align-items: center; justify-content: center;
      flex-shrink: 0;
    }
    .contact-info-item .icon-wrap i { color: var(--gold); font-size: 18px; }
    .contact-info-item h6 { color: white; }
    .contact-info-item p { color: rgba(255,255,255,0.78); }

    /* ── FOOTER (minimized + darker) ── */
    footer {
      background: var(--navy-deep);
      color: rgba(255,255,255,0.75);
      text-align: center;
      padding: 14px 10px;
      position: relative;
      z-index: 1;
    }
    footer p { margin: 0; font-size: 0.82rem; }
    footer .sep { opacity: 0.5; margin: 0 6px; }

    /* ── SCROLL REVEAL ── */
    .reveal {
      opacity: 0;
      transform: translateY(40px);
      transition: opacity 0.7s ease, transform 0.7s ease;
    }
    .reveal.visible { opacity: 1; transform: translateY(0); }
    .reveal-left {
      opacity: 0;
      transform: translateX(-40px);
      transition: opacity 0.7s ease, transform 0.7s ease;
    }
    .reveal-left.visible { opacity: 1; transform: translateX(0); }
    .reveal-right {
      opacity: 0;
      transform: translateX(40px);
      transition: opacity 0.7s ease, transform 0.7s ease;
    }
    .reveal-right.visible { opacity: 1; transform: translateX(0); }
    .delay-1 { transition-delay: 0.1s; }
    .delay-2 { transition-delay: 0.2s; }
    .delay-3 { transition-delay: 0.3s; }
    .delay-4 { transition-delay: 0.4s; }
    .delay-5 { transition-delay: 0.5s; }

    /* ── MOBILE ── */
    @media (max-width: 768px) {
      .hero { height: 88vh; }
      .hero-content { padding-left: 24px; padding-right: 24px; max-width: 100%; }
      .hero-content h1 { font-size: 2rem; }
      .hero-content p { font-size: 0.95rem; }
      .section-title { font-size: 1.6rem; }
      .contact-panel { padding: 26px; }
      .service-card { flex: 0 0 250px; }
    }

    @media (prefers-reduced-motion: reduce) {
      .reveal, .reveal-left, .reveal-right { transition: none; opacity: 1; transform: none; }
    }
  </style>
</head>

<body>

<div class="bg-blob bg-blob-1"></div>
<div class="bg-blob bg-blob-2"></div>
<div class="bg-blob bg-blob-3"></div>

<!-- ══════════════ NAVBAR ══════════════ -->
<nav class="navbar navbar-expand-lg navbar-dark fixed-top" id="mainNav">
  <div class="container">
    <a class="navbar-brand" href="#home">
      <img src="/images/SMHLogo.png" alt="SMH Logo">
      SMH Laboratory
    </a>

    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
      <span class="navbar-toggler-icon"></span>
    </button>

     <div class="collapse navbar-collapse" id="navbarNav">
      <div class="ms-auto d-flex align-items-center gap-3">
        <a class="nav-link" href="#home"     data-section="home">Home</a>
        <a class="nav-link" href="#about"    data-section="about">About</a>
        <a class="nav-link" href="#services" data-section="services">Services</a>
        <a class="nav-link" href="#contact"  data-section="contact">Contact</a>
        <a href="/login" class="btn btn-glass">Log In</a>
      </div>
    </div>

    
  </div>
</nav>

<!-- ══════════════ HERO ══════════════ -->
<section class="hero" id="home">
  <div id="heroCarousel"
       class="carousel slide carousel-fade position-absolute w-100 h-100"
       data-bs-ride="carousel"
       style="z-index:1;">
    <div class="carousel-inner h-100">
      <div class="carousel-item active h-100">
        <img src="/images/SMHPhoto.jpg" class="d-block w-100 h-100 object-fit-cover" alt="SMH Lab">
      </div>
    </div>
  </div>

  <div class="position-absolute w-100 h-100" style="background:linear-gradient(90deg, rgba(6,23,41,0.78) 0%, rgba(6,23,41,0.55) 45%, rgba(6,23,41,0.25) 100%);z-index:2;"></div>

  <div class="hero-content text-white">
    <div class="hero-eyebrow"><span class="dash"></span> Your Health, Our Priority</div>
    <h1 class="fw-bold">Trusted diagnostics, delivered with care</h1>
    <p>Seamlessly book appointments, track lab results, and access your secure patient portal for a smoother healthcare experience.</p>
    <a href="/login" class="btn btn-glass btn-lg">Book Appointment</a>
  </div>

  <div class="scroll-down" onclick="scrollToSection('about')">
    <i class="bi bi-chevron-down"></i>
  </div>
</section>

<!-- ══════════════ ABOUT ══════════════ -->
<section class="page-section" id="about">
  <div class="container">
    <div class="text-center reveal">
      <h2 class="section-title">About SMH Laboratory</h2>
      <div class="section-divider"></div>
      <p class="text-muted mb-5" style="max-width:600px;margin:0 auto 40px;">
        Committed to delivering accurate, timely, and compassionate diagnostic services since 1998.
      </p>
    </div>

    <div class="row g-4 align-items-start">
      <!-- Cards column -->
      <div class="col-lg-6">
        <div class="about-card glass-card reveal delay-1">
          <div class="icon-circle"><i class="bi bi-bullseye"></i></div>
          <div>
            <h5>Our Mission</h5>
            <p class="mb-0">To provide high-quality, reliable laboratory diagnostic services that empower healthcare providers and patients to make informed decisions for better health outcomes.</p>
          </div>
        </div>
        <div class="about-card glass-card reveal delay-2">
          <div class="icon-circle"><i class="bi bi-eye"></i></div>
          <div>
            <h5>Our Vision</h5>
            <p class="mb-0">To be the leading community laboratory recognized for accuracy, innovation, and patient-centered care across the region.</p>
          </div>
        </div>
        <div class="about-card glass-card reveal delay-3">
          <div class="icon-circle"><i class="bi bi-award"></i></div>
          <div>
            <h5>Accreditation & Standards</h5>
            <p class="mb-0">Licensed by the Department of Health (DOH) Philippines and follows strict ISO 15189 quality standards for medical laboratories.</p>
          </div>
        </div>
        <div class="about-card glass-card reveal delay-4">
          <div class="icon-circle"><i class="bi bi-geo-alt"></i></div>
          <div>
            <h5>Location</h5>
            <p class="mb-0">
              <strong>SMH Laboratory</strong><br>
              14-A National Highway Mangan-Vaca<br>
              Subic, Philippines, 2209<br>
              <i class="bi bi-clock me-1"></i> Mon – Sat: 8:00 AM – 3:00 PM
            </p>
          </div>
        </div>
      </div>

      <!-- Stats + Map column -->
      <div class="col-lg-6 reveal-right">
        <!-- Stats row -->
        <div class="row g-3 mb-4">
          <div class="col-4">
            <div class="stat-badge glass-card">
              <div class="num" data-target="2">0</div>
              <p>Years of Service</p>
            </div>
          </div>
          <div class="col-4">
            <div class="stat-badge glass-card">
              <div class="num" data-target="1000">0</div>
              <p>Patients Served</p>
            </div>
          </div>
          <div class="col-4">
            <div class="stat-badge glass-card">
              <div class="num" data-target="100">0</div>
              <p>Tests Available</p>
            </div>
          </div>
        </div>
        <!-- Map -->
        <div class="about-map-wrap">
          <iframe
            src="https://www.google.com/maps/embed?pb=!1m14!1m12!1m3!1d563.9096603508078!2d120.2363038295367!3d14.88513311429441!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!5e1!3m2!1sen!2sph!4v1788266581201!5m2!1sen!2sph"
            allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- ══════════════ SERVICES ══════════════ -->
<section class="page-section" id="services">
  <div class="container">
    <div class="text-center reveal">
      <h2 class="section-title">Our Services</h2>
      <div class="section-divider"></div>
      <p class="text-muted mb-4" style="max-width:600px;margin:0 auto 30px;">
        A comprehensive range of laboratory tests and diagnostics — fast, accurate, and secure.
      </p>
    </div>

    <div class="services-carousel-wrap reveal">
      <div class="services-track" id="servicesTrack">
        <div class="service-card">
          <div class="service-img"><img src="{{ asset('images/services/online_appointment.jfif') }}" alt="Online Appointment"><i class="bi bi-calendar-check position-absolute"></i></div>
          <div class="service-body">
            <h5>Online Appointment</h5>
            <p>Book lab visits quickly and easily online with instant confirmation and reminders.</p>
          </div>
        </div>
        <div class="service-card">
          <div class="service-img"><img src="{{ asset('images/services/lab_result.png') }}" alt="Lab Results Portal"><i class="bi bi-file-earmark-medical position-absolute"></i></div>
          <div class="service-body">
            <h5>Lab Results Portal</h5>
            <p>Access your lab results securely anytime, anywhere — with full privacy protection.</p>
          </div>
        </div>
        <div class="service-card">
          <div class="service-img"><img src="{{ asset ('images/services/hematology.jfif') }}" alt="Hematology"><i class="bi bi-droplet-half position-absolute"></i></div>
          <div class="service-body">
            <h5>Hematology</h5>
            <p>Complete blood count, blood typing, clotting studies, and more with same-day results.</p>
          </div>
        </div>
        <div class="service-card">
          <div class="service-img"><img src="{{ asset ('images/services/clinical_chemistry.jfif') }}" alt="Clinical Chemistry"><i class="bi bi-heart-pulse position-absolute"></i></div>
          <div class="service-body">
            <h5>Clinical Chemistry</h5>
            <p>Glucose, lipid profile, kidney & liver function tests, electrolytes, and cardiac markers.</p>
          </div>
        </div>
        <div class="service-card">
          <div class="service-img"><img src="{{ asset ('images/services/microbiology.jfif') }}" alt="Microbiology"><i class="bi bi-virus position-absolute"></i></div>
          <div class="service-body">
            <h5>Microbiology</h5>
            <p>Culture & sensitivity, gram staining, and infection screening to identify bacterial agents.</p>
          </div>
        </div>
        <div class="service-card">
          <div class="service-img"><img src="{{ asset ('images/services/home_service.jfif') }}" alt="Home Service"><i class="bi bi-house-heart position-absolute"></i></div>
          <div class="service-body">
            <h5>Home Service</h5>
            <p>Can't come in? We bring the lab to you. Schedule a home collection at your convenience.</p>
          </div>
        </div>
      </div>

      <div class="services-controls">
        <button type="button" class="carousel-arrow" id="servicesPrev" aria-label="Previous services">
          <i class="bi bi-chevron-left"></i>
        </button>
        <button type="button" class="carousel-arrow" id="servicesNext" aria-label="Next services">
          <i class="bi bi-chevron-right"></i>
        </button>
      </div>
    </div>
  </div>
</section>

<!-- ══════════════ CONTACT ══════════════ -->
<section class="page-section" id="contact">
  <div class="container">
    <div class="text-center reveal">
      <h2 class="section-title">Contact Us</h2>
      <div class="section-divider"></div>
      <p class="lead-sub mb-5" style="max-width:600px;margin:0 auto 40px;">
        Have questions or concerns? Reach out and our team will get back to you promptly.
      </p>
    </div>

    <div class="row g-5 align-items-start justify-content-center">
      <!-- Contact Info -->
      <div class="col-lg-8 reveal-left">
        <div class="contact-panel">
          <div class="row g-4">
            <div class="col-md-6">
              <div class="contact-info-item">
                <div class="icon-wrap"><i class="bi bi-geo-alt-fill"></i></div>
                <div>
                  <h6 class="fw-bold mb-1">Address</h6>
                  <p class="mb-0">14-A National Highway Mangan-Vaca, Subic, Philippines, 2209</p>
                </div>
              </div>
              <div class="contact-info-item">
                <div class="icon-wrap"><i class="bi bi-telephone-fill"></i></div>
                <div>
                  <h6 class="fw-bold mb-1">Phone</h6>
                  <p class="mb-0">09354815423</p>
                </div>
              </div>
              <div class="contact-info-item">
                <div class="icon-wrap"><i class="bi bi-envelope-fill"></i></div>
                <div>
                  <h6 class="fw-bold mb-1">Email</h6>
                  <p class="mb-0">subicmedhealthlab@gmail.com</p>
                </div>
              </div>
            </div>
            <div class="col-md-6">
              <div class="contact-info-item">
                <div class="icon-wrap"><i class="bi bi-clock-fill"></i></div>
                <div>
                  <h6 class="fw-bold mb-1">Operating Hours</h6>
                  <p class="mb-0">Mon – Sat: 7:00 AM – 3:00 PM</p>
                </div>
              </div>
              <div class="contact-info-item">
                <div class="icon-wrap"><i class="bi bi-facebook"></i></div>
                <div>
                  <h6 class="fw-bold mb-1">Social Media</h6>
                  <p class="mb-0">facebook.com/Subic Med-Health Laboratory</p>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- ══════════════ FOOTER ══════════════ -->
<footer>
  <p>&copy; 2026 SMH Laboratory System. All rights reserved.<span class="sep">|</span>Licensed by the Department of Health Philippines</p>
</footer>

<!-- ══════════════ SCRIPTS ══════════════ -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
  // ── Smooth scroll helper ──
  function scrollToSection(id) {
    const el = document.getElementById(id);
    if (el) el.scrollIntoView({ behavior: 'smooth' });
  }

  // ── Navbar: darken on scroll + active link highlight ──
  const nav = document.getElementById('mainNav');
  const sections = document.querySelectorAll('section[id], .hero[id]');
  const navLinks = document.querySelectorAll('.nav-link[data-section]');

  window.addEventListener('scroll', () => {
    nav.classList.toggle('scrolled', window.scrollY > 60);

    // Active nav link
    let current = '';
    sections.forEach(sec => {
      if (window.scrollY >= sec.offsetTop - 100) current = sec.id;
    });
    navLinks.forEach(link => {
      link.classList.toggle('active-section', link.dataset.section === current);
    });
  });

  // ── Scroll reveal ──
  const revealEls = document.querySelectorAll('.reveal, .reveal-left, .reveal-right');
  const observer = new IntersectionObserver((entries) => {
    entries.forEach(e => {
      if (e.isIntersecting) {
        e.target.classList.add('visible');
        observer.unobserve(e.target);
      }
    });
  }, { threshold: 0.15 });
  revealEls.forEach(el => observer.observe(el));

  // ── Counter animation ──
  const counters = document.querySelectorAll('.num[data-target]');
  const counterObserver = new IntersectionObserver((entries) => {
    entries.forEach(e => {
      if (e.isIntersecting) {
        const el = e.target;
        const target = +el.dataset.target;
        const duration = 1800;
        const step = target / (duration / 16);
        let current = 0;
        const tick = () => {
          current = Math.min(current + step, target);
          el.textContent = target >= 1000
            ? Math.floor(current).toLocaleString() + '+'
            : Math.floor(current) + '+';
          if (current < target) requestAnimationFrame(tick);
        };
        tick();
        counterObserver.unobserve(el);
      }
    });
  }, { threshold: 0.5 });
  counters.forEach(c => counterObserver.observe(c));

  // ── Services carousel controls ──
  const track = document.getElementById('servicesTrack');
  const prevBtn = document.getElementById('servicesPrev');
  const nextBtn = document.getElementById('servicesNext');
  if (track && prevBtn && nextBtn) {
    const scrollAmount = () => (track.querySelector('.service-card')?.offsetWidth || 300) + 22;
    prevBtn.addEventListener('click', () => track.scrollBy({ left: -scrollAmount(), behavior: 'smooth' }));
    nextBtn.addEventListener('click', () => track.scrollBy({ left: scrollAmount(), behavior: 'smooth' }));
  }
</script>
</body>
</html>