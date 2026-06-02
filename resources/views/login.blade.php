<!DOCTYPE html> 
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login & Registration - SMH</title>
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
      max-width: 550px;
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

    .form-toggle {
      display: flex;
      justify-content: center;
      margin-bottom: 20px;
      gap: 10px;
    }

    .form-control, .form-select {
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

      <div class="form-toggle mb-3">
        <button id="showLogin" class="btn btn-outline-light flex-fill active">Log In</button>
        <button id="showRegister" class="btn btn-outline-light flex-fill">Register</button>
      </div>

      <div id="loginForm">
        <h4 class="text-center mb-4">Welcome Back</h4>
        @if(session('login_error'))
          <div class="alert alert-danger py-2">{{ session('login_error') }}</div>
        @endif

        <form action="{{ route('login.submit') }}" method="POST">
          @csrf
          <div class="mb-3">
            <label for="login_email">Email</label>
            <input type="email" id="login_email" name="email" class="form-control" required>
          </div>
          <div class="mb-3">
            <label for="login_password">Password</label>
            <input type="password" id="login_password" name="password" class="form-control" required>
          </div>
          <button type="submit" class="btn btn-primary w-100 mt-2">Log In</button>
        </form>
      </div>

      <div id="registerForm" style="display: none;">
        <h4 class="text-center mb-4">Create Account</h4>
        
        @if($errors->any())
          <div class="alert alert-danger py-2">
            <ul class="mb-0 small">
              @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
              @endforeach
            </ul>
          </div>
        @endif

        <form action="{{ route('register.submit') }}" method="POST">
          @csrf
          
          <div class="row">
              <div class="col-md-12 mb-3">
                <label>Account Role</label>
                <select name="role" class="form-select" required>
                  <option value="patient">Patient</option>
                  <option value="staff">Staff</option>
                  <option value="admin">Admin</option>
                </select>
              </div>

              <div class="col-md-4 mb-3">
                <label>First Name</label>
                <input type="text" name="first_name" class="form-control" required>
              </div>
              <div class="col-md-4 mb-3">
                <label>Middle Name</label>
                <input type="text" name="middle_name" class="form-control">
              </div>
              <div class="col-md-4 mb-3">
                <label>Last Name</label>
                <input type="text" name="last_name" class="form-control" required>
              </div>

              <div class="col-md-6 mb-3">
                <label>Birth Date</label>
                <input type="date" name="date_of_birth" class="form-control" required>
              </div>
              <div class="col-md-6 mb-3">
                <label>Sex</label>
                <select name="sex" class="form-select" required>
                  <option value="male">Male</option>
                  <option value="female">Female</option>
                </select>
              </div>

              <div class="col-md-6 mb-3">
                <label>Phone Number</label>
                <input type="text" name="phone_number" class="form-control" required>
              </div>
              <div class="col-md-6 mb-3">
                <label>Email Address</label>
                <input type="email" name="email" class="form-control" required>
              </div>

              <hr class="my-3">
              <h6 class="text-info mb-3"><i class="bi bi-geo-alt"></i> Home Address</h6>

              <div class="col-md-12 mb-3">
                <label for="Umunicipality">Municipality</label>
                <select id="Umunicipality" name="Umunicipality" class="form-select" onchange="updateBarangays()" required>
                  <option value="">Select Municipality</option>
                  <option value="Subic">Subic</option>
                  <option value="Olongapo">Olongapo</option>
                  <option value="Castillejos">Castillejos</option>
                </select>
              </div>

              <div class="col-md-6 mb-3">
                <label for="Ubarangay">Barangay</label>
                <select id="Ubarangay" name="Ubarangay" class="form-select" onchange="updateStreets()" required disabled>
                  <option value="">Select Municipality first</option>
                </select>
              </div>

              <div class="col-md-6 mb-3">
                <label for="Ustreet_house">Street / Area</label>
                <select id="Ustreet_house" name="Ustreet_house" class="form-select" required disabled>
                  <option value="">Select Barangay first</option>
                </select>
              </div>

              <hr class="my-3">
              <div class="col-md-6 mb-3">
                <label>Emergency Contact Person</label>
                <input type="text" name="contact_person" class="form-control" required>
              </div>
              <div class="col-md-6 mb-3">
                <label>Emergency Contact #</label>
                <input type="text" name="contact_number" class="form-control" required>
              </div>

              <div class="col-md-6 mb-3">
                <label>Password</label>
                <input type="password" name="password" class="form-control" required>
              </div>
              <div class="col-md-6 mb-3">
                <label>Confirm Password</label>
                <input type="password" name="password_confirmation" class="form-control" required>
              </div>
          </div>

          <button type="submit" class="btn btn-success w-100 mt-3 py-2 fw-bold">Register Now</button>
        </form>
      </div>
    </div>
  </div>

  <script>
    const loginForm = document.getElementById('loginForm');
    const registerForm = document.getElementById('registerForm');
    const showLogin = document.getElementById('showLogin');
    const showRegister = document.getElementById('showRegister');

    showLogin.addEventListener('click', () => {
      loginForm.style.display = 'block';
      registerForm.style.display = 'none';
      showLogin.classList.add('active');
      showRegister.classList.remove('active');
    });

    showRegister.addEventListener('click', () => {
      loginForm.style.display = 'none';
      registerForm.style.display = 'block';
      showRegister.classList.add('active');
      showLogin.classList.remove('active');
    });

    // Dependent Dropdown Data
    const locationData = {
        "Subic": {
            "Baraca-Camachile": ["Purok 1", "Purok 2", "Purok 3", "Riverside St."],
            "Calapacuan": ["Purok 4", "Purok 5", "National Highway", "Fisherman's Village"],
            "Mangan-Vaca": ["St. Joseph St.", "San Roque St.", "Mangan-Vaca Road"],
            "Sto. Tomas": ["Purok 6", "Green Valley", "Sto. Tomas Proper"],
            "Cawag": ["Sitio Nagtulong", "Agusuhin Relocation", "upper matang-ib", "lower matang-ib", "nagyantok", "rough-road", "cawag proper", "cawag resettlement", "malingaw"],
            "San Isidro": ["Purok 1", "Purok 2", "Purok 3", "Purok 4"],
            "Ilwas" : ["Nara", "mahogany"]
        },
        "Olongapo": {
            "Barretto": ["Baloy Long Beach", "Sierra Beach", "Rizal St."],
            "Gordon Heights": ["Block 1", "Block 2", "Upper Gordon"],
            "East Tapinac": ["Rizal Avenue", "Magsaysay Drive"]
        },
        "Castillejos": {
            "San Pablo": ["Street A", "Street B"],
            "San Roque": ["Street C", "Street D"],
            "Balaybay": ["Purok 1", "Purok 2", "Purok 3", "Purok 4", "Purok 5", "Purok 6", "Purok 7", "Purok 8", "Purok 9", "Purok 10", "Purok 11", "Purok 12", "Purok 13", "Kalye Putol "]
        }
    };

    function updateBarangays() {
        const municipality = document.getElementById("Umunicipality").value;
        const barangaySelect = document.getElementById("Ubarangay");
        const streetSelect = document.getElementById("Ustreet_house");

        barangaySelect.innerHTML = '<option value="">Select Barangay</option>';
        streetSelect.innerHTML = '<option value="">Select Barangay first</option>';
        streetSelect.disabled = true;

        if (municipality && locationData[municipality]) {
            barangaySelect.disabled = false;
            Object.keys(locationData[municipality]).forEach(brgy => {
                let opt = document.createElement("option");
                opt.value = brgy;
                opt.text = brgy;
                barangaySelect.add(opt);
            });
        } else {
            barangaySelect.disabled = true;
        }
    }

    function updateStreets() {
        const municipality = document.getElementById("Umunicipality").value;
        const barangay = document.getElementById("Ubarangay").value;
        const streetSelect = document.getElementById("Ustreet_house");

        streetSelect.innerHTML = '<option value="">Select Street/Area</option>';

        if (barangay && locationData[municipality][barangay]) {
            streetSelect.disabled = false;
            locationData[municipality][barangay].forEach(street => {
                let opt = document.createElement("option");
                opt.value = street;
                opt.text = street;
                streetSelect.add(opt);
            });
        } else {
            streetSelect.disabled = true;
        }
    }
  </script>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>