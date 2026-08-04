@extends('layouts.masterlayout')

@section('title', 'Approved Appointment Schedule')

@section('content')
<style>
    .schedule-page { padding: 28px 10px; max-width: 1100px; margin: 0 auto; }

    .sp-header { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 20px; flex-wrap: wrap; gap: 16px; }
    .sp-header h2 { font-size: 1.45rem; font-weight: 700; color: #fff; display: flex; align-items: center; gap: 10px; margin: 0; }
    .sp-header h2 i { color: #00d4ff; }
    .sp-subtitle { color: rgba(255,255,255,0.4); font-size: 0.8rem; margin-top: 4px; }

    .btn-print-schedule {
        background: rgba(0,212,255,0.12); color: #00d4ff;
        border: 1px solid rgba(0,212,255,0.35); border-radius: 9px;
        padding: 9px 22px; font-size: 0.85rem; font-weight: 600;
        cursor: pointer; transition: 0.22s;
        display: flex; align-items: center; gap: 7px; white-space: nowrap;
    }
    .btn-print-schedule:hover { background: #00d4ff; color: #000; }

    .sp-table-wrap {
        background: rgba(255,255,255,0.05);
        border: 1px solid rgba(255,255,255,0.09);
        border-radius: 16px;
        padding: 6px;
        overflow: hidden;
    }

    .sp-table { width: 100%; border-collapse: collapse; }
    .sp-table th {
        background: rgba(0, 212, 255, 0.1);
        color: #0dcaf0;
        text-transform: uppercase;
        font-size: 0.72rem;
        letter-spacing: 1px;
        padding: 12px 15px;
        text-align: left;
    }
    .sp-table td {
        padding: 13px 15px;
        border-bottom: 1px solid rgba(255, 255, 255, 0.06);
        color: white;
        vertical-align: middle;
        font-size: 0.88rem;
    }
    .sp-table tr:last-child td { border-bottom: none; }
    .sp-table tr:hover td { background: rgba(255, 255, 255, 0.03); }

    .badge-approved {
        background: #198754;
        color: #fff;
        font-size: 0.7rem;
        padding: 5px 12px;
        border-radius: 20px;
    }

    .sp-empty { text-align: center; padding: 50px; color: rgba(255,255,255,0.3); }
    .sp-empty i { font-size: 3rem; display: block; margin-bottom: 12px; }
</style>

<div class="schedule-page">

    <div class="sp-header">
        <div>
            <h2><i class="bi bi-calendar-check"></i> Approved Appointment Schedule</h2>
            <p class="sp-subtitle">Integrated Lab System &nbsp;·&nbsp; Staff Console</p>
        </div>
        <button class="btn-print-schedule" onclick="printSchedule()">
            <i class="bi bi-printer"></i> Print Schedule
        </button>
    </div>

    <div class="sp-table-wrap">
        @if($approvedAppointments->isEmpty())
            <div class="sp-empty">
                <i class="bi bi-calendar-x"></i>
                No approved appointments found.
            </div>
        @else
            <table class="sp-table" id="scheduleTable">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Time</th>
                        <th>Patient Name</th>
                        <th>Contact</th>
                        <th>Service</th>
                        <th>Type</th>
                        <th>Address</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($approvedAppointments as $app)
                        <tr>
                            <td>{{ \Carbon\Carbon::parse($app->appointment_date)->format('M d, Y') }}</td>
                            <td>{{ \Carbon\Carbon::parse($app->appointment_time)->format('h:i A') }}</td>
                            <td>{{ $app->first_name }} {{ $app->middle_name ? substr($app->middle_name, 0, 1).'.' : '' }} {{ $app->last_name }} {{ $app->suffix }}</td>
                            <td>
                                {{ $app->phone }}<br>
                                <small class="text-muted">{{ $app->email }}</small>
                            </td>
                            <td>{{ $app->service }}</td>
                            <td>{{ $app->appointment_type }}</td>
                            <td>{{ $app->street_details }}, Brgy. {{ $app->barangay }}, {{ $app->municipality }}</td>
                            <td><span class="badge-approved">{{ strtoupper($app->status) }}</span></td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>
</div>

<script>
    // Same isolated-iframe printing approach used on the System Reports page:
    // builds a standalone print document so nothing from the staff layout
    // (sidebar, topbar, card backgrounds/shadows) can bleed into the printout.
    const labLogoUrl = "{{ asset('images/SMHLogo.png') }}";

    function printSchedule() {
        const tableHtml = document.getElementById('scheduleTable')
            ? document.getElementById('scheduleTable').outerHTML
            : '<p style="text-align:center;color:#777;">No approved appointments found.</p>';

        const generatedAt = new Date().toLocaleString('en-US', {
            year: 'numeric', month: 'long', day: 'numeric',
            hour: '2-digit', minute: '2-digit'
        });

        const doc = `
            <!DOCTYPE html>
            <html>
            <head>
            <meta charset="utf-8">
            <title>Approved Appointment Schedule</title>
            <style>
                * { box-sizing: border-box; -webkit-print-color-adjust: exact; print-color-adjust: exact; }
                body {
                    margin: 0; padding: 32px; background: #fff; color: #000;
                    font-family: -apple-system, Segoe UI, Roboto, Arial, sans-serif;
                }
                .letterhead {
                    display: flex; align-items: center; gap: 16px;
                    border-bottom: 2px solid #000; padding-bottom: 14px; margin-bottom: 16px;
                }
                .letterhead img { height: 60px; width: auto; }
                .letterhead .lab-name { font-size: 1.15rem; font-weight: 700; margin: 0 0 2px; }
                .letterhead .lab-details { font-size: 0.78rem; color: #333; line-height: 1.5; margin: 0; }
                .print-head { padding-bottom: 10px; margin-bottom: 18px; display: flex; justify-content: space-between; align-items: flex-end; flex-wrap: wrap; gap: 8px; }
                .print-head h1 { margin: 0 0 4px; font-size: 1.1rem; }
                .print-head p { margin: 0; font-size: 0.8rem; color: #333; }
                table { width: 100%; border-collapse: collapse; margin-top: 6px; }
                th {
                    background: #eee; color: #000; text-transform: uppercase;
                    font-size: 0.68rem; letter-spacing: 0.5px;
                    padding: 8px 10px; text-align: left; border-bottom: 1px solid #999;
                }
                td {
                    padding: 8px 10px; font-size: 0.8rem; color: #000;
                    border-bottom: 1px solid #ddd; vertical-align: top;
                }
                .badge-approved {
                    background: #fff; color: #000; border: 1px solid #000;
                    font-size: 0.65rem; padding: 3px 9px; border-radius: 20px;
                }
                small { color: #555; }
            </style>
            </head>
            <body>
                <div class="letterhead">
                    <img src="${labLogoUrl}" alt="SMH Logo">
                    <div>
                        <p class="lab-name">SMH Laboratory</p>
                        <p class="lab-details">
                            14-A National Highway Mangan-Vaca<br>
                            Subic, Philippines, 2209<br>
                            Mon &ndash; Fri: 8:00 AM &ndash; 3:00 PM
                        </p>
                    </div>
                </div>
                <div class="print-head">
                    <div>
                        <h1>Approved Appointment Schedule</h1>
                        <p>Staff Console</p>
                    </div>
                    <p>Generated: ${generatedAt}</p>
                </div>
                ${tableHtml}
            </body>
            </html>
        `;

        const iframe = document.createElement('iframe');
        iframe.style.position = 'fixed';
        iframe.style.right = '0';
        iframe.style.bottom = '0';
        iframe.style.width = '0';
        iframe.style.height = '0';
        iframe.style.border = '0';
        document.body.appendChild(iframe);

        iframe.contentWindow.document.open();
        iframe.contentWindow.document.write(doc);
        iframe.contentWindow.document.close();

        iframe.onload = () => {
            iframe.contentWindow.focus();
            iframe.contentWindow.print();
        };

        iframe.contentWindow.addEventListener('afterprint', () => {
            document.body.removeChild(iframe);
        });
    }
</script>

@endsection