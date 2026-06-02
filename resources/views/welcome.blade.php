<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>SMH Laboratory System</title>

  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">

  <style>
    body {
      font-family: Arial, sans-serif;
    }

    /* NAVBAR (original style restored) */
    .navbar {
      background: rgba(13, 110, 253, 0.25);
      backdrop-filter: blur(12px);
      -webkit-backdrop-filter: blur(12px);
      border-bottom: 1px solid rgba(255, 255, 255, 0.2);
      box-shadow: 0 4px 20px rgba(0,0,0,0.1);
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
    }

    .nav-link:hover { text-decoration: underline; }

    /* HERO */
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

    /* GLASS BUTTON */
    .btn-glass {
      background: rgba(255, 255, 255, 0.15);
      color: black;
      font-weight: bold;
      border-radius: 30px;
      padding: 12px 30px;
      border: 2px solid transparent;
      backdrop-filter: blur(10px);
      transition: 0.3s all;
    }

    .btn-glass:hover {
      border: 2px solid white;
      background: rgba(255, 255, 255, 0.25);
      color: white;
      transform: translateY(-2px);
    }

    /* SCROLL DOWN ARROW */
    .scroll-down {
      position: absolute;
      bottom: 30px;
      left: 50%;
      transform: translateX(-50%);
      z-index: 3;
      font-size: 30px;
      color: white;
      animation: bounce 2s infinite;
      cursor: pointer;
    }

    @keyframes bounce {
      0%, 20%, 50%, 80%, 100% { transform: translate(-50%, 0); }
      40% { transform: translate(-50%, 10px); }
      60% { transform: translate(-50%, 5px); }
    }

    /* FEATURES */
    .features { background: #f8f9fa; padding: 60px 0; }

    .feature-box { text-align: center; padding: 20px; }
    .feature-box i {
      font-size: 40px;
      color: #0d6efd;
      margin-bottom: 15px;
    }

    /* BODY TEXT POLISH */
    .hero-content h1 { font-size: 3rem; margin-bottom: 20px; line-height: 1.2; }
    .hero-content p {
      font-size: 1.25rem;
      color: #f0f0f0;
      margin-bottom: 30px;
    }

    footer {
      background: #0d6efd;
      color: white;
      text-align: center;
      padding: 15px;
    }

    /* MOBILE */
    @media (max-width: 768px) {
      .hero { height: 80vh; padding: 20px; }
      .hero-content h1 { font-size: 24px; }
      .hero-content p { font-size: 14px; }
    }
  </style>
</head>

<body>

<!-- NAVBAR (original header) -->
<nav class="navbar navbar-expand-lg navbar-dark fixed-top">
  <div class="container">

    <a class="navbar-brand" href="#">
      <img src="/images/SMHLogo.png" alt="SMH Logo">
      SMH Laboratory
    </a>

    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
      <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse" id="navbarNav">
      <div class="ms-auto d-flex align-items-center gap-3">
        <a class="nav-link" href="#">Home</a>
        <a class="nav-link" href="#">About</a>
        <a class="nav-link" href="#">Services</a>
        <a class="nav-link" href="#">Contact</a>
        <a href="/login" class="btn btn-glass">Log In</a>
      </div>
    </div>
  </div>
</nav>

<!-- HERO -->
<div class="hero">
  <div id="heroCarousel"
       class="carousel slide carousel-fade position-absolute w-100 h-100"
       data-bs-ride="carousel"
       style="z-index: 1;">
    <div class="carousel-inner h-100">
      <div class="carousel-item active h-100">
        <img src="/images/SMHPhoto.jpg" class="d-block w-100 h-100 object-fit-cover">
      </div>
    </div>
  </div>

  <div class="position-absolute w-100 h-100" style="background: rgba(0,0,0,0.5); z-index: 2;"></div>

  <div class="hero-content text-center text-white">
    <h1 class="fw-bold">Your Health, Our Priority</h1>
    <p>Seamlessly book appointments, track lab results, and access your secure patient portal for a smoother healthcare experience.</p>
    <a href="/login" class="btn btn-glass btn-lg">Book Appointment</a>
  </div>

  <div class="scroll-down" onclick="document.getElementById('features').scrollIntoView({behavior: 'smooth'})">
    <i class="bi bi-chevron-down"></i>
  </div>
</div>

<!-- FEATURES -->
<section id="features" class="features">
  <div class="container">
    <div class="row justify-content-center">
      <div class="col-md-4 feature-box">
        <i class="bi bi-calendar-check"></i>
        <h5>Online Appointment</h5>
        <p>Book lab visits quickly and easily online with instant confirmation.</p>
      </div>
      <div class="col-md-4 feature-box">
        <i class="bi bi-file-earmark-text"></i>
        <h5>Lab Results</h5>
        <p>Access lab results securely anytime, anywhere, with full privacy.</p>
      </div>
    </div>
  </div>
</section>

<footer>
  <p class="mb-0">&copy; 2026 SMH Laboratory System</p>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>