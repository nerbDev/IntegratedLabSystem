<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Book Appointment - SMH</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">

  <style>
    body {
      margin: 0;
      font-family: Arial, sans-serif;
      color: #fff;
      min-height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
    }

    body::before {
      content: "";
      position: fixed;
      top: 0; left: 0; width: 100%; height: 100%;
      background: url('/images/SMHPhoto.jpg') no-repeat center center/cover;
      filter: blur(12px) brightness(0.6);
      z-index: -2;
    }

    body::after {
      content: "";
      position: fixed;
      top: 0; left: 0; width: 100%; height: 100%;
      background: rgba(0,0,0,0.4);
      z-index: -1;
    }

    .appointment-card {
      /* FIX: Added relative positioning and z-index so buttons are clickable over the background */
      position: relative;
      z-index: 10;
      background: rgba(255, 255, 255, 0.1);
      backdrop-filter: blur(15px);
      border: 1px solid rgba(255, 255, 255, 0.2);
      border-radius: 20px;
      padding: 40px;
      width: 100%;
      max-width: 850px;
      box-shadow: 0 15px 35px rgba(0,0,0,0.5);
    }

    .step { display: none; }
    .step.active { display: block; }

    .steps-header { display: flex; gap: 10px; margin-bottom: 10px; }
    .step-pill { flex: 1; padding: 10px; text-align: center; border-radius: 8px; background: rgba(255,255,255,0.1); font-size: 0.9rem; }
    .step-pill.active { background: rgba(255,255,255,0.3); font-weight: bold; border: 1px solid #fff; }

    .glass-option {
      background: rgba(255,255,255,0.05);
      border: 1px solid rgba(255,255,255,0.2);
      border-radius: 12px;
      padding: 15px;
      cursor: pointer;
      transition: 0.3s;
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 10px;
    }
    .glass-option:hover:not(.disabled) { background: rgba(255,255,255,0.2); border-color: #fff; }
    .glass-option.selected { border-color: #00d4ff; background: rgba(0, 212, 255, 0.2); }
    .glass-option.disabled { opacity: 0.4; cursor: not-allowed; border-color: rgba(255,0,0,3); background: rgba(255,255,255,0.05) !important; }

    .view-link { color: #00d4ff; text-decoration: underline; font-size: 0.8rem; cursor: pointer; }

    input.form-control, select.form-control, textarea.form-control {
      background: rgba(255,255,255,0.1);
      border: 1px solid rgba(255,255,255,0.2);
      color: #fff;
    }
    input.form-control:focus, select.form-control:focus, textarea.form-control:focus { 
      background: rgba(255,255,255,0.2); 
      color: #fff; 
    }
    select.form-control option { background: #333; color: #fff; }
    
    .modal-content {
      background: rgba(30, 30, 30, 0.95);
      backdrop-filter: blur(20px);
      color: white;
      border: 1px solid rgba(255,255,255,0.2);
    }
    .price-tag {
      font-size: 1.5rem;
      color: #00d4ff;
      font-weight: bold;
      border-top: 1px solid rgba(255,255,255,0.1);
      padding-top: 15px;
      margin-top: 15px;
    }
    .inclusion-list {
        list-style: none;
        padding-left: 0;
    }
    .inclusion-list li::before {
        content: "• ";
        color: #00d4ff;
        font-weight: bold;
    }

    .custom-service-item {
        padding: 10px;
        border-bottom: 1px solid rgba(255,255,255,0.1);
        display: flex;
        align-items: center;
        gap: 10px;
    }
    .custom-service-item:last-child { border-bottom: none; }
    .custom-service-item input { cursor: pointer; }
  </style>
</head>
<body>

<div class="appointment-card">
  <div class="text-center mb-4">
    <img src="{{ asset('images/SMHLogo.png') }}" width="60" alt="Logo">
    <h3 class="mt-2">Book Your Appointment</h3>
  </div>

  <div class="progress mb-3" style="height: 4px; background: rgba(255,255,255,0.1); border-radius: 10px;">
    <div id="progressBar" class="progress-bar bg-info" style="width: 25%; transition: 0.5s;"></div>
  </div>

  <div class="steps-header">
    <div class="step-pill active" id="pill1">Step 1</div>
    <div class="step-pill" id="pill2">Step 2</div>
    <div class="step-pill" id="pill3">Step 3</div>
    <div class="step-pill" id="pill4">Step 4</div>
  </div>

  @if ($errors->any())
    <div class="alert alert-danger shadow-sm mb-3" style="background: rgba(220, 53, 69, 0.2); color: #fff; border: 1px solid #dc3545;">
        <ul class="mb-0">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
  @endif

  @if(session('success'))
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var successModal = new bootstrap.Modal(document.getElementById('successModal'));
            successModal.show();
            localStorage.removeItem('smh_booking_draft');
        });
    </script>
  @endif

  <form action="{{ route('appointments.store') }}" method="POST" id="appointmentForm">
    @csrf
    <input type="hidden" name="appointment_type" id="hidden_appointment_type">
    <input type="hidden" name="service" id="hidden_service">
    <input type="hidden" name="appointment_time" id="hidden_appointment_time">

    <div id="step1" class="step active">
      <h5 class="mb-3">1. Select Service Type</h5>
      <div class="row g-2 mb-4" id="type-container">
        <div class="col-md-6">
          <div class="glass-option text-center d-block" onclick="selectType('Online Booking', event)">
            Online Booking
          </div>
        </div>
        <div class="col-md-6">
          <div class="glass-option text-center d-block" onclick="selectType('Home Service', event)">
            Home Service
          </div>
        </div>
      </div>

      <h5 class="mb-3">2. Select a Package</h5>
      <div class="row g-2" id="promo-container">
        @php 
          $promos = ['Buntis Package A', 'Buntis Package B', 'CHEM 5', 'CHEM 9', 'CHEM 10', 'General Package', 'Thyroid Test', 'Electrolytes Package', 'Pre-Employment A', 'Pre-Employment B'];
        @endphp
        @foreach($promos as $p)
        <div class="col-md-6">
          <div class="glass-option" onclick="selectPromo('{{ $p }}', event)">
            <span>{{ $p }}</span>
            <span class="view-link" onclick="viewDetails(event, '{{ $p }}')">view details</span>
          </div>
        </div>
        @endforeach
        
        <div class="col-md-6">
          <div class="glass-option" id="others-btn" onclick="openOthersModal(event)">
            <span>Others (Select Specific Services)</span>
            <i class="bi bi-plus-circle text-info"></i>
          </div>
        </div>
      </div>

      <div id="fasting-warning" class="alert alert-warning mt-3 d-none" style="background: rgba(255, 193, 7, 0.2); color: #ffc107; border: 1px solid #ffc107; font-size: 0.85rem;">
        <i class="bi bi-exclamation-triangle-fill"></i> Note: This package requires 8-12 hours of fasting.
      </div>

      <button type="button" class="btn btn-light mt-4 w-100" onclick="goToStep(2)">Next Step</button>
    </div>

    <div id="step2" class="step">
      <h5 class="mb-3">Select Schedule</h5>
      <label class="small mb-1">Pick a Date (Mon - Fri only)</label>
      <input type="date" name="appointment_date" id="appointment_date" class="form-control mb-3" onchange="fetchSlots()">
      <label class="small mb-1">Available Times</label>
      <div id="slot-container" class="row g-2">
        <p class="text-muted">Please select a date first.</p>
      </div>
      <div class="d-flex gap-2 mt-4">
        <button type="button" class="btn btn-outline-light w-50" onclick="goToStep(1)">Back</button>
        <button type="button" class="btn btn-light w-50" onclick="goToStep(3)">Next</button>
      </div>
    </div>

    <div id="step3" class="step">
      <h5 class="mb-3">Personal Information & Address</h5>
      <div class="row g-2">
          <div class="col-md-3 mb-3">
            <input type="text" name="first_name" id="first_name" class="form-control" placeholder="First Name" required oninput="saveFormData()">
          </div>
          <div class="col-md-3 mb-3">
            <input type="text" name="middle_name" id="middle_name" class="form-control" placeholder="Middle Name" oninput="saveFormData()">
          </div>
          <div class="col-md-3 mb-3">
            <input type="text" name="last_name" id="last_name" class="form-control" placeholder="Last Name" required oninput="saveFormData()">
          </div>
          <div class="col-md-3 mb-3">
            <select name="suffix" id="suffix" class="form-control" onchange="saveFormData()">
                <option value="">Suffix (None)</option>
                <option value="Jr.">Jr.</option>
                <option value="Sr.">Sr.</option>
                <option value="III">III</option>
                <option value="IV">IV</option>
                <option value="V">V</option>
            </select>
          </div>
          
          <div class="col-md-12 mb-3">
            <input type="email" name="email" id="email" class="form-control" placeholder="Email Address" value="{{ auth()->user()->email ?? '' }}" required oninput="saveFormData()">
          </div>
      </div>
      <div class="mb-3">
        <input type="text" name="phone" id="phone" class="form-control" placeholder="Phone Number" required oninput="saveFormData()">
      </div>

      <h6 class="mt-4 mb-2 text-info">Appointment Address</h6>
      <div class="row g-2">
          <div class="col-md-4 mb-3">
              <select name="municipality" id="municipality" class="form-control" required onchange="updateBarangays()">
                  <option value="">Select Municipality</option>
                  <option value="Subic">Subic</option>
                  <option value="Olongapo">Olongapo</option>
                  <option value="Castillejos">Castillejos</option>
              </select>
          </div>
          <div class="col-md-4 mb-3">
              <select name="barangay" id="barangay" class="form-control" required onchange="updateStreets()">
                  <option value="">Select Barangay</option>
              </select>
          </div>
          <div class="col-md-4 mb-3">
              <select name="street_details" id="street_details" class="form-control" required onchange="saveFormData()">
                  <option value="">Select Street/Purok</option>
              </select>
          </div>
      </div>
      <div class="mb-3">
          <input type="text" name="landmark" id="landmark" class="form-control" placeholder="📍 Landmark (e.g., Beside 7-Eleven, Blue Gate, Church)" required oninput="saveFormData()">
      </div>

      <div class="d-flex gap-2 mt-4">
        <button type="button" class="btn btn-outline-light w-50" onclick="goToStep(2)">Back</button>
        <button type="button" class="btn btn-light w-50" onclick="goToStep(4)">Review</button>
      </div>
    </div>

    <div id="step4" class="step">
      <h5 class="mb-3">Review Details</h5>
      <div class="p-3" style="background: rgba(255,255,255,0.05); border-radius:10px;">
        <p><strong>Type:</strong> <span id="rev-type"></span></p>
        <p><strong>Service:</strong> <span id="rev-service"></span></p>
        <p><strong>Schedule:</strong> <span id="rev-date"></span> at <span id="rev-time"></span></p>
        <p><strong>Patient:</strong> <span id="rev-name"></span></p>
        <p><strong>Address:</strong> <span id="rev-address"></span></p>
        <p><strong>Landmark:</strong> <span id="rev-landmark" class="text-info"></span></p>
        <p><strong>Estimated Total:</strong> <span id="rev-price" class="text-info fw-bold">₱0</span></p>
      </div>
      <div class="d-flex gap-2 mt-4">
        <button type="button" class="btn btn-outline-light w-50" onclick="goToStep(3)">Back</button>
        <button type="submit" class="btn btn-success w-50">Confirm Booking</button>
      </div>
    </div>
  </form>
</div>

<div class="modal fade" id="detailsModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header border-0">
        <h5 class="modal-title" id="modalTitle">Package Details</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <ul id="modalInclusions" class="inclusion-list" style="font-size: 0.95rem; line-height: 1.6; color: #ccc;"></ul>
        <div id="modalPrice" class="price-tag text-center"></div>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="othersModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
    <div class="modal-content">
      <div class="modal-header border-0">
        <h5 class="modal-title">Select Individual Services</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <p class="small text-info mb-3">You can select one or multiple services below.</p>
        <div id="custom-services-list"></div>
      </div>
      <div class="modal-footer border-0">
        <button type="button" class="btn btn-primary w-100" onclick="confirmCustomServices()">Confirm Selection</button>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="successModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content text-center p-4">
      <div class="modal-body">
        <i class="bi bi-check-circle-fill text-success" style="font-size: 4rem;"></i>
        <h4 class="mt-3">Appointment Booked Successfully</h4>
        <p class="text-white-50">Kindly check the status of your appointment below.</p>
        <a href="{{ route('patient.appointments') }}" class="btn btn-info w-100 mt-3 py-2 fw-bold text-dark">See details</a>
      </div>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
  let selectedPromo = '';
  let selectedType = ''; 
  let selectedTime = ''; 
  let customServices = []; 
  let totalPrice = 0;

  const packagePrices = {
    'Buntis Package A': 1800, 'Buntis Package B': 980, 'CHEM 5': 500,
    'CHEM 9': 1000, 'CHEM 10': 1200, 'General Package': 1700,
    'Thyroid Test': 2000, 'Electrolytes Package': 650,
    'Pre-Employment A': 500, 'Pre-Employment B': 650
  };

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
            "Balaybay": ["Purok 1", "Purok 2", "Purok 3", "Purok 4", "Purok 5", "Purok 6", "Purok 7", "Purok 8", "Purok 9", "Purok 10", "Purok 11", "Purok 12", "Purok 13", "Kalye Putol"]
        }
  };

  const today = new Date().toISOString().split('T')[0];
  document.getElementById('appointment_date').setAttribute('min', today);

  const allAvailableServices = [
    'Fasting Blood Sugar (FBS)', 'CBC with platelet', 'Blood Typing', 'HBsAg', 'RPR/VDRL', 
    'Urinalysis', 'HIV', 'Cholesterol', 'Triglyceride', 'HDL', 'LDL', 'BUN', 
    'Creatinine', 'BUA', 'SGPT', 'SGOT', 'CBC', 'Fecalysis', 'TSH', 'FT3', 'FT4',
    'Na (Sodium)', 'K (Potassium)', 'Cl (Chloride)', 'BSM'
  ];

  function updateBarangays() {
    const muni = document.getElementById('municipality').value;
    const brgySelect = document.getElementById('barangay');
    const streetSelect = document.getElementById('street_details');
    brgySelect.innerHTML = '<option value="">Select Barangay</option>';
    streetSelect.innerHTML = '<option value="">Select Street/Purok</option>';
    if (muni && locationData[muni]) {
        Object.keys(locationData[muni]).forEach(brgy => {
            const opt = document.createElement('option');
            opt.value = brgy; opt.innerText = brgy;
            brgySelect.appendChild(opt);
        });
    }
    saveFormData();
  }

  function updateStreets() {
    const muni = document.getElementById('municipality').value;
    const brgy = document.getElementById('barangay').value;
    const streetSelect = document.getElementById('street_details');
    streetSelect.innerHTML = '<option value="">Select Street/Purok</option>';
    
    if (muni && brgy && locationData[muni] && locationData[muni][brgy]) {
        locationData[muni][brgy].forEach(street => {
            const opt = document.createElement('option');
            opt.value = street; opt.innerText = street;
            streetSelect.appendChild(opt);
        });
    }
    saveFormData();
  }

  function selectType(type, event) {
    selectedType = type;
    document.getElementById('hidden_appointment_type').value = type;
    document.querySelectorAll('#type-container .glass-option').forEach(el => el.classList.remove('selected'));
    event.currentTarget.classList.add('selected');
  }

  function selectPromo(p, event) {
    selectedPromo = p;
    customServices = []; 
    totalPrice = packagePrices[p] || 0;
    document.getElementById('hidden_service').value = p;
    document.querySelectorAll('#promo-container .glass-option').forEach(el => el.classList.remove('selected'));
    event.currentTarget.classList.add('selected');

    const fastingPackages = ['Buntis Package A', 'Buntis Package B', 'CHEM 5', 'CHEM 9', 'CHEM 10', 'General Package'];
    const warningDiv = document.getElementById('fasting-warning');
    if(fastingPackages.includes(p)) warningDiv.classList.remove('d-none');
    else warningDiv.classList.add('d-none');
  }

  function openOthersModal(event) {
    const listContainer = document.getElementById('custom-services-list');
    listContainer.innerHTML = '';
    allAvailableServices.sort().forEach(service => {
      const div = document.createElement('div');
      div.className = 'custom-service-item';
      div.innerHTML = `<input type="checkbox" class="service-check" value="${service}" id="chk-${service}"><label for="chk-${service}" class="ms-2">${service}</label>`;
      listContainer.appendChild(div);
    });
    new bootstrap.Modal(document.getElementById('othersModal')).show();
  }

  function confirmCustomServices() {
    const checked = document.querySelectorAll('.service-check:checked');
    if (checked.length === 0) { alert("Please select at least one service."); return; }
    customServices = Array.from(checked).map(cb => cb.value);
    selectedPromo = "Custom: " + customServices.join(', ');
    totalPrice = 0; 
    document.getElementById('hidden_service').value = selectedPromo;
    document.querySelectorAll('#promo-container .glass-option').forEach(el => el.classList.remove('selected'));
    document.getElementById('others-btn').classList.add('selected');
    bootstrap.Modal.getInstance(document.getElementById('othersModal')).hide();
  }

  function viewDetails(event, promoName) {
    event.stopPropagation();
    const packageData = {
      'Buntis Package A': { price: '1,800', items: ['Fasting Blood Sugar (FBS)', 'CBC with platelet', 'Blood Typing', 'HBsAg', 'RPR/VDRL', 'Urinalysis', 'HIV', '*8 hours fasting'] },
      'Buntis Package B': { price: '980', items: ['Fasting Blood Sugar (FBS)', 'CBC with platelet', 'Blood Typing', 'HBsAg', 'RPR/VDRL', 'Urinalysis', '*8 hours fasting'] },
      'CHEM 5': { price: '500', items: ['FBS', 'Cholesterol', 'Triglyceride', 'HDL', 'LDL'] },
      'CHEM 9': { price: '1,000', items: ['FBS', 'Cholesterol', 'Triglyceride', 'HDL', 'LDL', 'BUN', 'Creatinine', 'BUA', 'SGPT'] },
      'CHEM 10': { price: '1,200', items: ['FBS', 'Cholesterol', 'Triglyceride', 'HDL', 'LDL', 'BUN', 'Creatinine', 'BUA', 'SGPT', 'SGOT'] },
      'General Package': { price: '1,700', items: ['CBC with Platelet', 'Urinalysis', 'Fecalysis', 'FBS', 'Cholesterol', 'Triglyceride', 'HDL', 'LDL', 'BUN', 'Creatinine', 'BUA', 'SGPT', 'SGOT'] },
      'Thyroid Test': { price: '2,000', items: ['TSH', 'FT3', 'FT4'] },
      'Electrolytes Package': { price: '650', items: ['Na (Sodium)', 'K (Potassium)', 'Cl (Chloride)'] },
      'Pre-Employment A': { price: '500', items: ['CBC', 'Urinalysis', 'Fecalysis', 'HBsAg'] },
      'Pre-Employment B': { price: '650', items: ['BSM', 'Urinalysis', 'Fecalysis', 'HBsAg'] }
    };
    const data = packageData[promoName];
    document.getElementById('modalTitle').innerText = promoName;
    const listContainer = document.getElementById('modalInclusions');
    listContainer.innerHTML = '';
    if(data) { data.items.forEach(item => { const li = document.createElement('li'); li.innerText = item; listContainer.appendChild(li); }); }
    document.getElementById('modalPrice').innerText = data ? `₱${data.price}` : "";
    new bootstrap.Modal(document.getElementById('detailsModal')).show();
  }

  async function fetchSlots() {
    const dateInput = document.getElementById('appointment_date');
    const dateValue = dateInput.value;
    const container = document.getElementById('slot-container');
    if(!dateValue) return;

    const date = new Date(dateValue);
    const day = date.getDay(); 
    if(day === 6 || day === 0) { 
      alert("SMH is closed on weekends. Please select a date between Monday and Friday.");
      dateInput.value = ""; 
      container.innerHTML = '<p class="text-danger">Closed on Weekends. Please select a weekday.</p>';
      return;
    }

    container.innerHTML = '<div class="text-center"><div class="spinner-border text-light"></div></div>';
    try {
      const resp = await fetch(`/get-available-slots?date=${dateValue}`);
      const data = await resp.json();
      const slots = ['08:00', '09:00', '10:00', '11:00', '13:00', '14:00', '15:00'];
      container.innerHTML = '';
      slots.forEach(s => {
        const taken = data.taken.some(t => t.startsWith(s)); 
        const div = document.createElement('div');
        div.className = `col-4`;
        div.innerHTML = `<div class="glass-option text-center d-block ${taken ? 'disabled' : ''}" onclick="${taken ? '' : "selectTime(event, '" + s + "')" }">${formatToAMPM(s)}${taken ? '<br><small>Full</small>' : ''}</div>`;
        container.appendChild(div);
      });
    } catch(err) { container.innerHTML = 'Error loading slots.'; }
  }

  function selectTime(event, s) {
    selectedTime = s;
    document.getElementById('hidden_appointment_time').value = s;
    document.querySelectorAll('#slot-container .glass-option').forEach(el => el.classList.remove('selected'));
    event.currentTarget.classList.add('selected');
  }

  function formatToAMPM(time24) {
    if(!time24) return "";
    let [hours, minutes] = time24.split(':');
    let modifier = 'AM';
    if (hours >= 12) { modifier = 'PM'; if (hours > 12) hours -= 12; }
    if (hours == 0) hours = 12;
    return `${hours}:${minutes} ${modifier}`;
  }

  function goToStep(n) {
    const currentStep = parseInt(document.querySelector('.step.active').id.replace('step', ''));
    if(n > currentStep) {
        if(n === 2 && (!selectedPromo || !selectedType)) return alert('Please select both a service type and a package.');
        if(n === 3 && (!selectedTime || !document.getElementById('appointment_date').value)) return alert('Select date and time.');
        if(n === 4) {
            if(!document.getElementById('municipality').value || !document.getElementById('barangay').value || !document.getElementById('street_details').value || !document.getElementById('landmark').value || !document.getElementById('first_name').value || !document.getElementById('last_name').value) {
                return alert('Please complete your name, address, and landmark details.');
            }
        }
    }
    document.querySelectorAll('.step').forEach(s => s.classList.remove('active'));
    document.querySelectorAll('.step-pill').forEach(p => p.classList.remove('active'));
    document.getElementById('step'+n).classList.add('active');
    document.getElementById('pill'+n).classList.add('active');
    document.getElementById('progressBar').style.width = (n / 4) * 100 + '%';

    if(n === 4) {
      document.getElementById('rev-type').innerText = selectedType;
      document.getElementById('rev-service').innerText = selectedPromo;
      document.getElementById('rev-date').innerText = document.getElementById('appointment_date').value;
      document.getElementById('rev-time').innerText = formatToAMPM(selectedTime);
      
      const fName = document.getElementById('first_name').value;
      const mName = document.getElementById('middle_name').value;
      const lName = document.getElementById('last_name').value;
      const suffix = document.getElementById('suffix').value;
      const fullName = `${fName} ${mName} ${lName} ${suffix}`.replace(/\s+/g, ' ').trim();
      document.getElementById('rev-name').innerText = fullName;
      
      const muni = document.getElementById('municipality').value;
      const brgy = document.getElementById('barangay').value;
      const street = document.getElementById('street_details').value;
      document.getElementById('rev-address').innerText = `${street}, Brgy. ${brgy}, ${muni}`;

      document.getElementById('rev-landmark').innerText = document.getElementById('landmark').value;
      document.getElementById('rev-price').innerText = `₱${totalPrice.toLocaleString()}`;
    }
    saveFormData();
  }

  function saveFormData() {
    const formData = {
      fName: document.getElementById('first_name').value,
      mName: document.getElementById('middle_name').value,
      lName: document.getElementById('last_name').value,
      suffix: document.getElementById('suffix').value,
      email: document.getElementById('email').value,
      phone: document.getElementById('phone').value,
      muni: document.getElementById('municipality').value,
      brgy: document.getElementById('barangay').value,
      street: document.getElementById('street_details').value,
      landmark: document.getElementById('landmark').value
    };
    localStorage.setItem('smh_booking_draft', JSON.stringify(formData));
  }

  window.onload = function() {
    const saved = localStorage.getItem('smh_booking_draft');
    if(saved) {
      const d = JSON.parse(saved);
      if(d.fName) document.getElementById('first_name').value = d.fName;
      if(d.mName) document.getElementById('middle_name').value = d.mName;
      if(d.lName) document.getElementById('last_name').value = d.lName;
      if(d.suffix) document.getElementById('suffix').value = d.suffix;
      if(d.email) document.getElementById('email').value = d.email;
      if(d.phone) document.getElementById('phone').value = d.phone;
      if(d.landmark) document.getElementById('landmark').value = d.landmark;
      if(d.muni) {
        document.getElementById('municipality').value = d.muni;
        updateBarangays();
        if(d.brgy) {
            document.getElementById('barangay').value = d.brgy;
            updateStreets();
            if(d.street) document.getElementById('street_details').value = d.street;
        }
      }
    }
  };
</script>
</body>
</html>