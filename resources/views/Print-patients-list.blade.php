<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Patient List - SMH Laboratory</title>
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

    .meta-line {
      display: flex;
      justify-content: space-between;
      font-size: 0.8rem;
      color: #555;
      margin-bottom: 18px;
    }

    table {
      width: 100%;
      border-collapse: collapse;
      font-size: 0.85rem;
    }
    th, td {
      border: 1px solid #ccc;
      padding: 8px 10px;
      text-align: left;
      vertical-align: top;
    }
    th {
      background: #f0f6ff;
      color: #0d6efd;
      font-size: 0.75rem;
      text-transform: uppercase;
      letter-spacing: 0.5px;
    }
    tr:nth-child(even) td { background: #fafafa; }

    .print-footer {
      margin-top: 24px;
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
    <button onclick="window.print()">Print this list</button>
  </div>

  <div class="print-header">
    <img src="{{ asset('images/SMHLogo.png') }}" alt="SMH Logo">
    <div>
      <h1>SMH Laboratory — Patient Master Registry</h1>
      <p>14-A National Highway Mangan-Vaca, Subic, Philippines, 2209</p>
    </div>
  </div>

  <div class="meta-line">
    <span>Generated: {{ now()->timezone('Asia/Manila')->format('F j, Y g:i A') }}</span>
    <span>Total Patients: {{ $patients->count() }}</span>
  </div>

  <table>
    <thead>
      <tr>
        <th>#</th>
        <th>Full Name</th>
        <th>Email</th>
        <th>Phone</th>
        <th>Address</th>
        <th>Registered</th>
      </tr>
    </thead>
    <tbody>
      @foreach($patients as $index => $patient)
        <tr>
          <td>{{ $index + 1 }}</td>
          <td>{{ $patient->first_name }} {{ $patient->middle_name }} {{ $patient->last_name }}</td>
          <td>{{ $patient->email }}</td>
          <td>{{ $patient->phone_number ?? 'N/A' }}</td>
          <td>{{ $patient->Ustreet_house }}, {{ $patient->Ubarangay }}, {{ $patient->Umunicipality }}</td>
          <td>{{ optional($patient->created_at)->timezone('Asia/Manila')->format('M j, Y') }}</td>
        </tr>
      @endforeach
    </tbody>
  </table>

  <div class="print-footer">
    SMH Laboratory System &mdash; Confidential Patient Registry &mdash; For internal administrative use only
  </div>

  {{-- <script>
    window.onload = function () {
      window.print();
    };
  </script> --}}
</body>
</html>