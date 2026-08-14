<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Reset Password - SMH</title>
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

      <h4 class="text-center mb-2">Set New Password</h4>
      <p class="text-center small mb-4" style="color: rgba(255,255,255,0.85);">
        Choose a new password for your account.
      </p>

      @if($errors->any())
        <div class="alert alert-danger py-2">
          <ul class="mb-0 small">
            @foreach($errors->all() as $error)
              <li>{{ $error }}</li>
            @endforeach
          </ul>
        </div>
      @endif

      <form action="{{ route('password.reset') }}" method="POST">
        @csrf
        <div class="mb-3">
          <label for="password">New Password</label>
          <input type="password" id="password" name="password" class="form-control" required minlength="8">
        </div>
        <div class="mb-3">
          <label for="password_confirmation">Confirm New Password</label>
          <input type="password" id="password_confirmation" name="password_confirmation" class="form-control" required minlength="8">
        </div>
        <button type="submit" class="btn btn-primary w-100 mt-2">Reset Password</button>
      </form>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>