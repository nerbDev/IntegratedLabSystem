<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Patient Record - {{ $patient->first_name }} {{ $patient->last_name }} - SMH Laboratory</title>
  <style>
    * { box-sizing: border-box; }
    body {
      font-family: Arial, sans-serif;
      color: #1a1a1a;
      background: #ffffff;
      margin: 0;
      padding: 30px 40px;
    }

    .print-header {
      display: flex;
      align-items: center;
      gap: 16px;
      border-bottom: 3px solid #0d6efd;
      padding-bottom: 16px;
      margin-bottom: 20px;
    }
    .print-header img { height: 60px; }
    .print-header h1 {
      font-size: 1.4rem;
      margin: 0;
      color: #0d6efd;
    }
    .print-header p {
      margin: 2px 0 0;
      font-size: 0.85rem;
      color: #555;
    }

    .section-title {
      font-size: 0.95rem;
      color: #0d6efd;
      font-weight: bold;
      text-transform: uppercase;
      letter-spacing: 0.5px;
      margin: 24px 0 10px;
      border-bottom: 1px solid #ddd;
      padding-bottom: 4px;
    }

    .demo-grid {
      display: grid;
      grid-template-columns: repeat(3, 1fr);
      gap: 10px 20px;
      font-size: 0.85rem;
    }
    .demo-grid .label {
      font-size: 0.7rem;
      text-transform: uppercase;
      color: #888;
      letter-spacing: 0.5px;
    }
    .demo-grid .value { font-weight: 600; }

    table {
      width: 100%;
      border-collapse: collapse;
      font-size: 0.8rem;
      margin-bottom: 10px;
    }
    th, td {
      border: 1px solid #ccc;
      padding: 6px 8px;
      text-align: left;
      vertical-align: top;
    }
    th {
      background: #f0f6ff;
      color: #0d6efd;
      font-size: 0.7rem;
      text-transform: uppercase;
      letter-spacing: 0.5px;
    }

    .appointment-block {
      margin-bottom: 22px;
      page-break-inside: avoid;
    }
    .appointment-block h4 {
      font-size: 0.9rem;
      margin: 0 0 6px;
      color: #1a1a1a;
    }
    .appointment-block .meta {
      font-size: 0.78rem;
      color: #666;
      margin-bottom: 6px;
    }
    .abnormal { color: #dc3545; font-weight: bold; }

    .no-results {
      font-size: 0.85rem;
      color: #888;
      font-style: italic;
      padding: 10px 0;
    }

    .pdf-embed-wrap {
      margin-bottom: 10px;
    }
    .pdf-embed {
      width: 100%;
      height: 600px;
      border: 1px solid #ccc;
      border-radius: 4px;
    }
    .pdf-fallback-note {
      font-size: 0.75rem;
      color: #888;
      margin: 6px 0 0;
    }
    @media print {
      .pdf-fallback-note { display: none; }
      .pdf-embed { height: 900px; border: none; }
    }

    .print-footer {
      margin-top: 30px;
      font-size: 0.75rem;
      color: #888;
      text-align: center;
      border-top: 1px solid #ddd;
      padding-top: 10px;
    }

    .no-print { text-align: center; margin-bottom: 20px; }
    .no-print button {
      background: #0d6efd;
      color: #fff;
      border: none;
      padding: 10px 24px;
      border-radius: 30px;
      font-weight: bold;
      cursor: pointer;
    }

    @media print {
      .no-print { display: none; }
      body { padding: 0 20px; }
      @page { margin: 20mm 15mm; }
    }
  </style>
</head>
<body>

  <div class="no-print">
    <button onclick="window.print()">Print this record</button>
  </div>

  <div class="print-header">
    <img src="{{ asset('images/SMHLogo.png') }}" alt="SMH Logo">
    <div>
      <h1>SMH Laboratory — Patient Clinical Record</h1>
      <p>14-A National Highway Mangan-Vaca, Subic, Philippines, 2209</p>
    </div>
  </div>

  <div class="section-title">Patient Demographics</div>
  <div class="demo-grid">
    <div>
      <div class="label">Full Name</div>
      <div class="value">{{ $patient->first_name }} {{ $patient->middle_name }} {{ $patient->last_name }}</div>
    </div>
    <div>
      <div class="label">Date of Birth</div>
      <div class="value">{{ $patient->date_of_birth ?? 'N/A' }}</div>
    </div>
    <div>
      <div class="label">Sex</div>
      <div class="value">{{ ucfirst($patient->sex ?? 'N/A') }}</div>
    </div>
    <div>
      <div class="label">Email</div>
      <div class="value">{{ $patient->email }}</div>
    </div>
    <div>
      <div class="label">Phone</div>
      <div class="value">{{ $patient->phone_number ?? 'N/A' }}</div>
    </div>
    <div>
      <div class="label">Address</div>
      <div class="value">{{ $patient->Ustreet_house }}, {{ $patient->Ubarangay }}, {{ $patient->Umunicipality }}</div>
    </div>
    <div>
      <div class="label">Emergency Contact</div>
      <div class="value">{{ $patient->contact_person ?? 'N/A' }}</div>
    </div>
    <div>
      <div class="label">Emergency Phone</div>
      <div class="value">{{ $patient->contact_number ?? 'N/A' }}</div>
    </div>
    <div>
      <div class="label">Patient Since</div>
      <div class="value">{{ optional($patient->created_at)->timezone('Asia/Manila')->format('M j, Y') }}</div>
    </div>
  </div>

  <div class="section-title">Laboratory & Appointment History</div>

  @if($patient->appointments->isEmpty())
    <div class="no-results">No appointment or lab record history found for this patient.</div>
  @else
    @foreach($patient->appointments as $appointment)
      <div class="appointment-block">
        <h4>{{ $appointment->service ?? 'Diagnostic Appointment' }} &mdash; #{{ $appointment->id }}</h4>
        <div class="meta">
          Date: {{ $appointment->appointment_date }} &nbsp;|&nbsp;
          Time: {{ $appointment->appointment_time }} &nbsp;|&nbsp;
          Type: {{ $appointment->appointment_type ?? 'Standard' }} &nbsp;|&nbsp;
          Status: {{ ucfirst($appointment->status) }}
          @if($appointment->notes)
            <br>Notes: {{ $appointment->notes }}
          @endif
        </div>

        @if($appointment->result && $appointment->result->file_path)
          <div class="pdf-embed-wrap">
            <iframe
              src="{{ asset('storage/' . $appointment->result->file_path) }}"
              class="pdf-embed"
              title="Lab result document for appointment #{{ $appointment->id }}">
            </iframe>
            <p class="pdf-fallback-note">
              If the document above doesn't display, <a href="{{ asset('storage/' . $appointment->result->file_path) }}" target="_blank">open it in a new tab</a>.
            </p>
          </div>
        @else
          <div class="no-results">No lab result document has been uploaded for this appointment.</div>
        @endif
      </div>
    @endforeach
  @endif

  <div class="print-footer">
    SMH Laboratory System &mdash; Confidential Medical Record &mdash; For internal administrative use only
  </div>

  <script>
    window.onload = function () {
      window.print();
    };
  </script>
</body>
</html>