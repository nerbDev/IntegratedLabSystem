<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>{{ $title }}</title>
    <style>
        body { font-family: Arial, sans-serif; color: #000; padding: 20px; }

        .print-header {
            display: flex;
            align-items: center;
            gap: 15px;
            border-bottom: 2px solid #333;
            padding-bottom: 12px;
            margin-bottom: 16px;
        }
        .print-header img {
            height: 60px;
            width: auto;
        }
        .print-header .org-info h1 {
            margin: 0;
            font-size: 1.2rem;
        }
        .print-header .org-info p {
            margin: 2px 0 0;
            font-size: 0.8rem;
            color: #444;
        }
        .print-header .gen-info {
            margin-left: auto;
            text-align: right;
            font-size: 0.8rem;
            color: #444;
        }

        h2 { margin: 0 0 4px; }
        .meta { color: #555; font-size: 0.85rem; margin-bottom: 20px; }
        table { width: 100%; border-collapse: collapse; font-size: 0.8rem; }
        th, td { border: 1px solid #999; padding: 6px 8px; text-align: left; vertical-align: top; }
        th { background: #eee; }
        .no-print { margin-bottom: 20px; }
        @media print {
            .no-print { display: none; }
        }
    </style>
</head>
<body>
    <div class="no-print">
        <button onclick="window.print()">Print</button>
    </div>

    <div class="print-header">
        {{-- Replace with your actual logo path --}}
        <img src="{{ asset('images/SMHLogo.png') }}" alt="Logo">

        <div class="org-info">
            <h1>Integrated Lab System</h1>
            <p>{{ $ownerName }} &mdash; {{ $ownerRole }}</p>
        </div>

        <div class="gen-info">
            <div>Generated: {{ now()->format('M d, Y h:i A') }}</div>
        </div>
    </div>

    <h2>{{ $title }}</h2>
    <div class="meta">
        @if(request('module')) Module: {{ request('module') }} @endif
        @if(request('date_from')) &nbsp;|&nbsp; From: {{ request('date_from') }} @endif
        @if(request('date_to')) &nbsp;|&nbsp; To: {{ request('date_to') }} @endif
        @if(request('search')) &nbsp;|&nbsp; Search: "{{ request('search') }}" @endif
        &nbsp;|&nbsp; {{ $transactions->count() }} record(s)
    </div>

    <table>
        <thead>
            <tr>
                <th>Date/Time</th>
                <th>Module</th>
                <th>Action</th>
                <th>Description</th>
            </tr>
        </thead>
        <tbody>
            @forelse($transactions as $log)
                <tr>
                    <td>{{ $log->created_at->format('M d, Y h:i A') }}</td>
                    <td>{{ $log->module }}</td>
                    <td>{{ $log->action }}</td>
                    <td>{{ $log->description }}</td>
                </tr>
            @empty
                <tr><td colspan="4">No records found.</td></tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>