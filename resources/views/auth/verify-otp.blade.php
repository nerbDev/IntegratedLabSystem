<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Verify Code - SMH</title>
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

    .otp-input {
      letter-spacing: 10px;
      font-size: 1.5rem;
      text-align: center;
      font-weight: 700;
    }

    .btn-link-light {
      color: rgba(255,255,255,0.85);
      text-decoration: none;
      font-size: 0.85rem;
      background: none;
      border: none;
    }
    .btn-link-light:hover { color: #fff; text-decoration: underline; }
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

      <h4 class="text-center mb-2">Enter Verification Code</h4>
      <p class="text-center small mb-4" style="color: rgba(255,255,255,0.85);">
        We sent a 6-digit code to your email. It expires in 10 minutes.
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

      <form action="{{ route('password.verify-otp') }}" method="POST">
        @csrf
        <div class="mb-3">
          <label for="otp">6-digit code</label>
          <input type="text" id="otp" name="otp" maxlength="6" inputmode="numeric" pattern="[0-9]*"
                 class="form-control otp-input" required autofocus>
        </div>
        <button type="submit" class="btn btn-primary w-100 mt-2">Verify Code</button>
      </form>

      <form action="{{ route('password.send-otp') }}" method="POST" class="text-center mt-3">
        @csrf
        <input type="hidden" name="email" value="{{ session('password_reset_email') }}">
        <button type="submit" class="btn-link-light">Didn't get a code? Resend</button>
      </form>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>