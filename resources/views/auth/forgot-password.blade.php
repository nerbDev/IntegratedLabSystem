<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Forgot Password - SMH</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">

  <style>
    body {
      min-height: 100vh;
      margin: 0;
      overflow-x: hidden;
    }

    .bg-img {
      height: 100vh;
      object-fit: cover;
    }

    #bgCarousel {
      position: fixed;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      z-index: -2;
    }

    #bgCarousel::after {
      content: "";
      position: absolute;
      width: 100%;
      height: 100%;
      top: 0;
      left: 0;
      background: rgba(0, 0, 0, 0.4);
      z-index: 1;
    }

    .auth-container {
      max-width: 450px;
      margin: 30px auto;
      padding: 20px;
      position: relative;
      z-index: 2;
    }

    .form-box {
      background: rgba(255, 255, 255, 0.15);
      backdrop-filter: blur(12px);
      -webkit-backdrop-filter: blur(12px);
      padding: 30px;
      border-radius: 12px;
      box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
      border: 1px solid rgba(255, 255, 255, 0.3);
      color: #fff;
    }

    .auth-logo {
      display: block;
      margin: 0 auto 20px auto;
      width: 100px;
    }

    .form-control {
      background: rgba(255, 255, 255, 0.9);
      border: none;
    }

    label {
      font-weight: 500;
      margin-bottom: 5px;
      font-size: 0.9rem;
    }
  </style>
</head>

<body>

  <div id="bgCarousel" class="carousel slide carousel-fade" data-bs-ride="carousel" data-bs-interval="4000">
    <div class="carousel-inner">
      <div class="carousel-item active">
        <img src="{{ asset('images/SMHPhoto.jpg') }}" class="d-block w-100 bg-img">
      </div>
    </div>
  </div>

  <div class="auth-container">
    <div class="form-box">
      <img src="{{ asset('images/SMHLogo.png') }}" alt="SMH Logo" class="auth-logo">

      <h4 class="text-center mb-2">Forgot Password</h4>
      <p class="text-center small mb-4" style="color: rgba(255,255,255,0.85);">
        Enter the email linked to your account and we'll send you a 6-digit code.
      </p>

      @if(session('status'))
        <div class="alert alert-success py-2">{{ session('status') }}</div>
      @endif

      @if($errors->any())
        <div class="alert alert-danger py-2">
          <ul class="mb-0 small">
            @foreach($errors->all() as $error)
              <li>{{ $error }}</li>
            @endforeach
          </ul>
        </div>
      @endif

      <form action="{{ route('password.send-otp') }}" method="POST">
        @csrf
        <div class="mb-3">
          <label for="email">Email Address</label>
          <input type="email" id="email" name="email" class="form-control" value="{{ old('email') }}" required autofocus>
        </div>
        <button type="submit" class="btn btn-primary w-100 mt-2">Send Code</button>
      </form>

      <div class="text-center mt-3">
        <a href="{{ route('login.register') }}" class="small" style="color: rgba(255,255,255,0.85);">
          <i class="bi bi-arrow-left"></i> Back to Log In
        </a>
      </div>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>