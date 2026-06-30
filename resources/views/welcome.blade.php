<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>SMH Laboratory System</title>

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">

  <style>
    * { scroll-behavior: smooth; }

    body {
      font-family: Arial, sans-serif;
      overflow-x: hidden;
    }

    /* ── NAVBAR ── */
    .navbar {
      background: rgba(13, 110, 253, 0.25);
      backdrop-filter: blur(12px);
      -webkit-backdrop-filter: blur(12px);
      border-bottom: 1px solid rgba(255,255,255,0.2);
      box-shadow: 0 4px 20px rgba(0,0,0,0.1);
      transition: background 0.4s ease;
    }
    .navbar.scrolled {
      background: rgba(13, 110, 253, 0.85);
    }
    .navbar-brand {
      display: flex;
      align-items: center;
      gap: 10px;
      font-weight: bold;
      color: white !important;
    }
    .navbar img { height: 40px; }
    .nav-link {
      color: white !important;
      font-weight: 500;
      position: relative;
    }
    .nav-link::after {
      content: '';
      position: absolute;
      bottom: -2px; left: 0;
      width: 0; height: 2px;
      background: white;
      transition: width 0.3s ease;
    }
    .nav-link:hover::after,
    .nav-link.active-section::after { width: 100%; }

    /* ── GLASS BUTTON ── */
    .btn-glass {
      background: rgba(255,255,255,0.15);
      color: white;
      font-weight: bold;
      border-radius: 30px;
      padding: 12px 30px;
      border: 2px solid rgba(255,255,255,0.4);
      backdrop-filter: blur(10px);
      transition: 0.3s all;
    }
    .btn-glass:hover {
      border: 2px solid white;
      background: rgba(255,255,255,0.25);
      color: white;
      transform: translateY(-2px);
      box-shadow: 0 8px 25px rgba(0,0,0,0.2);
    }

    /* ── HERO ── */
    .hero {
      height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
      position: relative;
      overflow: hidden;
    }
    .object-fit-cover { object-fit: cover; }
    .hero-content {
      position: relative;
      z-index: 3;
      max-width: 700px;
    }
    .hero-content h1 {
      font-size: 3rem;
      margin-bottom: 20px;
      line-height: 1.2;
      animation: fadeUp 0.9s ease both;
    }
    .hero-content p {
      font-size: 1.25rem;
      color: #f0f0f0;
      margin-bottom: 30px;
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
      font-size: 30px;
      color: white;
      animation: bounce 2s infinite;
      cursor: pointer;
    }
    @keyframes bounce {
      0%,20%,50%,80%,100% { transform: translate(-50%,0); }
      40%  { transform: translate(-50%,10px); }
      60%  { transform: translate(-50%,5px); }
    }

    /* ── SECTION BASE ── */
    .page-section { padding: 90px 0 80px; }
    .section-title {
      font-size: 2.2rem;
      font-weight: 700;
      color: #0d6efd;
      margin-bottom: 10px;
    }
    .section-divider {
      width: 60px; height: 4px;
      background: #0d6efd;
      border-radius: 2px;
      margin: 0 auto 40px;
    }

    /* ── ABOUT ── */
    #about { background: #ffffff; }
    .about-card {
      background: #f0f6ff;
      border-left: 5px solid #0d6efd;
      border-radius: 12px;
      padding: 25px 30px;
      margin-bottom: 20px;
      transition: transform 0.3s, box-shadow 0.3s;
    }
    .about-card:hover {
      transform: translateY(-5px);
      box-shadow: 0 12px 30px rgba(13,110,253,0.15);
    }
    .about-card i {
      font-size: 28px;
      color: #0d6efd;
      margin-bottom: 10px;
    }
    .about-card h5 { font-weight: 700; color: #1a1a2e; }
    .about-map-wrap {
      border-radius: 16px;
      overflow: hidden;
      box-shadow: 0 8px 30px rgba(0,0,0,0.12);
    }

    /* ── SERVICES ── */
    #services { background: #f8f9fa; }
    .service-card {
      background: white;
      border-radius: 16px;
      padding: 35px 25px;
      text-align: center;
      border: 1px solid #e0eaff;
      transition: all 0.35s ease;
      height: 100%;
    }
    .service-card:hover {
      background: #0d6efd;
      color: white;
      transform: translateY(-8px);
      box-shadow: 0 16px 40px rgba(13,110,253,0.3);
    }
    .service-card:hover p,
    .service-card:hover h5 { color: white; }
    .service-card:hover i { color: white; }
    .service-card i {
      font-size: 42px;
      color: #0d6efd;
      margin-bottom: 18px;
      transition: color 0.3s;
    }
    .service-card h5 { font-weight: 700; margin-bottom: 10px; }
    .service-card p { font-size: 0.92rem; color: #666; }

    /* ── CONTACT ── */
    #contact { background: #ffffff; }
    .contact-info-item {
      display: flex;
      align-items: flex-start;
      gap: 16px;
      margin-bottom: 24px;
    }
    .contact-info-item .icon-wrap {
      width: 48px; height: 48px;
      background: #0d6efd;
      border-radius: 50%;
      display: flex; align-items: center; justify-content: center;
      flex-shrink: 0;
    }
    .contact-info-item .icon-wrap i { color: white; font-size: 20px; }
    .contact-form .form-control {
      border-radius: 10px;
      border: 1px solid #cde;
      padding: 12px 16px;
      font-size: 0.95rem;
      transition: border-color 0.3s, box-shadow 0.3s;
    }
    .contact-form .form-control:focus {
      border-color: #0d6efd;
      box-shadow: 0 0 0 3px rgba(13,110,253,0.15);
    }
    .btn-primary-custom {
      background: #0d6efd;
      color: white;
      border: none;
      border-radius: 30px;
      padding: 12px 36px;
      font-weight: 600;
      transition: 0.3s all;
    }
    .btn-primary-custom:hover {
      background: #0b5ed7;
      transform: translateY(-2px);
      box-shadow: 0 8px 20px rgba(13,110,253,0.35);
    }

    /* ── FOOTER ── */
    footer {
      background: #0d6efd;
      color: white;
      text-align: center;
      padding: 20px;
    }

    /* ── SCROLL REVEAL ── */
    .reveal {
      opacity: 0;
      transform: translateY(40px);
      transition: opacity 0.7s ease, transform 0.7s ease;
    }
    .reveal.visible {
      opacity: 1;
      transform: translateY(0);
    }
    .reveal-left {
      opacity: 0;
      transform: translateX(-40px);
      transition: opacity 0.7s ease, transform 0.7s ease;
    }
    .reveal-left.visible {
      opacity: 1;
      transform: translateX(0);
    }
    .reveal-right {
      opacity: 0;
      transform: translateX(40px);
      transition: opacity 0.7s ease, transform 0.7s ease;
    }
    .reveal-right.visible {
      opacity: 1;
      transform: translateX(0);
    }
    .delay-1 { transition-delay: 0.1s; }
    .delay-2 { transition-delay: 0.2s; }
    .delay-3 { transition-delay: 0.3s; }
    .delay-4 { transition-delay: 0.4s; }
    .delay-5 { transition-delay: 0.5s; }

    /* ── COUNTER BADGE ── */
    .stat-badge {
      text-align: center;
      padding: 20px;
    }
    .stat-badge .num {
      font-size: 2.5rem;
      font-weight: 800;
      color: #0d6efd;
    }
    .stat-badge p { color: #555; font-size: 0.9rem; margin: 0; }

    /* ── MOBILE ── */
    @media (max-width: 768px) {
      .hero { height: 80vh; padding: 20px; }
      .hero-content h1 { font-size: 24px; }
      .hero-content p { font-size: 14px; }
      .section-title { font-size: 1.7rem; }
    }

    @media (prefers-reduced-motion: reduce) {
      .reveal, .reveal-left, .reveal-right { transition: none; opacity: 1; transform: none; }
    }
  </style>
</head>

<body>

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

  <div class="position-absolute w-100 h-100" style="background:rgba(0,0,0,0.5);z-index:2;"></div>

  <div class="hero-content text-center text-white">
    <h1 class="fw-bold">Your Health, Our Priority</h1>
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
        <div class="about-card reveal delay-1">
          <i class="bi bi-bullseye d-block"></i>
          <h5>Our Mission</h5>
          <p class="mb-0 text-muted">To provide high-quality, reliable laboratory diagnostic services that empower healthcare providers and patients to make informed decisions for better health outcomes.</p>
        </div>
        <div class="about-card reveal delay-2">
          <i class="bi bi-eye d-block"></i>
          <h5>Our Vision</h5>
          <p class="mb-0 text-muted">To be the leading community laboratory recognized for accuracy, innovation, and patient-centered care across the region.</p>
        </div>
        <div class="about-card reveal delay-3">
          <i class="bi bi-award d-block"></i>
          <h5>Accreditation & Standards</h5>
          <p class="mb-0 text-muted">Licensed by the Department of Health (DOH) Philippines and follows strict ISO 15189 quality standards for medical laboratories.</p>
        </div>
        <div class="about-card reveal delay-4">
          <i class="bi bi-geo-alt d-block"></i>
          <h5>Location</h5>
          <p class="mb-0 text-muted">
            <strong>SMH Laboratory</strong><br>
            123 Health Avenue, Quezon City<br>
            Metro Manila, Philippines 1100<br>
            <i class="bi bi-clock me-1"></i> Mon – Sat: 6:00 AM – 6:00 PM<br>
            <i class="bi bi-clock me-1"></i> Sun: 7:00 AM – 12:00 NN
          </p>
        </div>
      </div>

      <!-- Stats + Map column -->
      <div class="col-lg-6 reveal-right">
        <!-- Stats row -->
        <div class="row g-3 mb-4">
          <div class="col-4">
            <div class="stat-badge" style="background:#f0f6ff;border-radius:12px;padding:20px;">
              <div class="num" data-target="25">0</div>
              <p>Years of Service</p>
            </div>
          </div>
          <div class="col-4">
            <div class="stat-badge" style="background:#f0f6ff;border-radius:12px;padding:20px;">
              <div class="num" data-target="50000">0</div>
              <p>Patients Served</p>
            </div>
          </div>
          <div class="col-4">
            <div class="stat-badge" style="background:#f0f6ff;border-radius:12px;padding:20px;">
              <div class="num" data-target="120">0</div>
              <p>Tests Available</p>
            </div>
          </div>
        </div>
        <!-- Map placeholder (replace src with actual embed) -->
        <div class="about-map-wrap">
          <iframe
            src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d123819.70808631987!2d120.97878!3d14.6760413!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3397b070ea1e30ef%3A0xa902018cd4e4f92!2sQuezon%20City%2C%20Metro%20Manila!5e0!3m2!1sen!2sph!4v1680000000000"
            width="100%" height="280" style="border:0;display:block;"
            allowfullscreen="" loading="lazy"
            referrerpolicy="no-referrer-when-downgrade">
          </iframe>
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
      <p class="text-muted mb-5" style="max-width:600px;margin:0 auto 40px;">
        A comprehensive range of laboratory tests and diagnostics — fast, accurate, and secure.
      </p>
    </div>

    <div class="row g-4">
      <div class="col-md-4 col-sm-6 reveal delay-1">
        <div class="service-card">
          <i class="bi bi-calendar-check"></i>
          <h5>Online Appointment</h5>
          <p>Book lab visits quickly and easily online with instant confirmation and reminders.</p>
        </div>
      </div>
      <div class="col-md-4 col-sm-6 reveal delay-2">
        <div class="service-card">
          <i class="bi bi-file-earmark-medical"></i>
          <h5>Lab Results Portal</h5>
          <p>Access your lab results securely anytime, anywhere — with full privacy protection.</p>
        </div>
      </div>
      <div class="col-md-4 col-sm-6 reveal delay-3">
        <div class="service-card">
          <i class="bi bi-droplet-half"></i>
          <h5>Hematology</h5>
          <p>Complete blood count, blood typing, clotting studies, and more with same-day results.</p>
        </div>
      </div>
      <div class="col-md-4 col-sm-6 reveal delay-1">
        <div class="service-card">
          <i class="bi bi-heart-pulse"></i>
          <h5>Clinical Chemistry</h5>
          <p>Glucose, lipid profile, kidney & liver function tests, electrolytes, and cardiac markers.</p>
        </div>
      </div>
      <div class="col-md-4 col-sm-6 reveal delay-2">
        <div class="service-card">
          <i class="bi bi-virus"></i>
          <h5>Microbiology</h5>
          <p>Culture & sensitivity, gram staining, and infection screening to identify bacterial agents.</p>
        </div>
      </div>
      <div class="col-md-4 col-sm-6 reveal delay-3">
        <div class="service-card">
          <i class="bi bi-house-heart"></i>
          <h5>Home Service</h5>
          <p>Can't come in? We bring the lab to you. Schedule a home collection at your convenience.</p>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- ══════════════ CONTACT ══════════════ -->
<section class="page-section" id="contact" style="background:#f8f9fa;">
  <div class="container">
    <div class="text-center reveal">
      <h2 class="section-title">Contact Us</h2>
      <div class="section-divider"></div>
      <p class="text-muted mb-5" style="max-width:600px;margin:0 auto 40px;">
        Have questions or concerns? Reach out and our team will get back to you promptly.
      </p>
    </div>

    <div class="row g-5 align-items-start">
      <!-- Contact Info -->
      <div class="col-lg-5 reveal-left">
        <div class="contact-info-item">
          <div class="icon-wrap"><i class="bi bi-geo-alt-fill"></i></div>
          <div>
            <h6 class="fw-bold mb-1">Address</h6>
            <p class="mb-0 text-muted">123 Health Avenue, Quezon City, Metro Manila, Philippines 1100</p>
          </div>
        </div>
        <div class="contact-info-item">
          <div class="icon-wrap"><i class="bi bi-telephone-fill"></i></div>
          <div>
            <h6 class="fw-bold mb-1">Phone</h6>
            <p class="mb-0 text-muted">(02) 8123-4567 &nbsp;|&nbsp; 0917-123-4567</p>
          </div>
        </div>
        <div class="contact-info-item">
          <div class="icon-wrap"><i class="bi bi-envelope-fill"></i></div>
          <div>
            <h6 class="fw-bold mb-1">Email</h6>
            <p class="mb-0 text-muted">info@smhlaboratory.com</p>
          </div>
        </div>
        <div class="contact-info-item">
          <div class="icon-wrap"><i class="bi bi-clock-fill"></i></div>
          <div>
            <h6 class="fw-bold mb-1">Operating Hours</h6>
            <p class="mb-0 text-muted">
              Mon – Sat: 6:00 AM – 6:00 PM<br>
              Sunday: 7:00 AM – 12:00 NN
            </p>
          </div>
        </div>
        <div class="contact-info-item">
          <div class="icon-wrap"><i class="bi bi-facebook"></i></div>
          <div>
            <h6 class="fw-bold mb-1">Social Media</h6>
            <p class="mb-0 text-muted">facebook.com/SMHLaboratory</p>
          </div>
        </div>
      </div>

      <!-- Contact Form -->
      <div class="col-lg-7 reveal-right">
        <div style="background:white;border-radius:20px;padding:40px;box-shadow:0 8px 30px rgba(0,0,0,0.08);">
          <h5 class="fw-bold mb-4" style="color:#1a1a2e;">Send us a message</h5>
          <div class="contact-form">
            <div class="row g-3">
              <div class="col-sm-6">
                <label class="form-label text-muted small">Full Name</label>
                <input type="text" class="form-control" placeholder="Juan dela Cruz">
              </div>
              <div class="col-sm-6">
                <label class="form-label text-muted small">Email Address</label>
                <input type="email" class="form-control" placeholder="juan@email.com">
              </div>
              <div class="col-12">
                <label class="form-label text-muted small">Subject</label>
                <input type="text" class="form-control" placeholder="Inquiry / Appointment / Feedback">
              </div>
              <div class="col-12">
                <label class="form-label text-muted small">Message</label>
                <textarea class="form-control" rows="5" placeholder="Write your message here…"></textarea>
              </div>
              <div class="col-12 text-end">
                <button class="btn btn-primary-custom">
                  <i class="bi bi-send me-2"></i>Send Message
                </button>
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
  <p class="mb-1">&copy; 2026 SMH Laboratory System. All rights reserved.</p>
  <p class="mb-0" style="font-size:0.85rem;opacity:0.8;">Licensed by the Department of Health Philippines</p>
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
            : Math.floor(current) + (target === 25 ? '+' : '+');
          if (current < target) requestAnimationFrame(tick);
        };
        tick();
        counterObserver.unobserve(el);
      }
    });
  }, { threshold: 0.5 });
  counters.forEach(c => counterObserver.observe(c));
</script>
</body>
</html>