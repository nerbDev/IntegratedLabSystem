@extends('layouts.masterlayout')

@section('title', $filterLabel . ' System Reports')

@section('content')
<style>
    .report-page { padding: 28px 10px; max-width: 960px; margin: 0 auto; }

    /* ── Page Header ── */
    .rp-header { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 20px; flex-wrap: wrap; gap: 16px; }
    .rp-header h2 { font-size: 1.45rem; font-weight: 700; color: #fff; display: flex; align-items: center; gap: 10px; margin: 0; }
    .rp-header h2 i { color: #00d4ff; }
    .rp-subtitle { color: rgba(255,255,255,0.4); font-size: 0.8rem; margin-top: 4px; }
    .rp-system-badge {
        background: rgba(0,212,255,0.1); border: 1px solid rgba(0,212,255,0.3);
        color: #00d4ff; font-size: 0.72rem; font-weight: 700;
        letter-spacing: 1.2px; text-transform: uppercase;
        padding: 6px 16px; border-radius: 30px;
    }

    /* ── Filter Tabs ── */
    .rp-tabs {
        display: flex; gap: 8px; margin-bottom: 24px; flex-wrap: wrap;
        border-bottom: 1px solid rgba(255,255,255,0.09); padding-bottom: 14px;
    }
    .rp-tab {
        background: rgba(255,255,255,0.04); border: 1px solid rgba(255,255,255,0.1);
        color: rgba(255,255,255,0.55); font-size: 0.82rem; font-weight: 600;
        padding: 8px 20px; border-radius: 30px; cursor: pointer;
        text-decoration: none; transition: 0.2s; display: inline-flex; align-items: center; gap: 6px;
    }
    .rp-tab:hover { border-color: rgba(0,212,255,0.3); color: #00d4ff; }
    .rp-tab.active {
        background: rgba(0,212,255,0.12); border-color: rgba(0,212,255,0.4);
        color: #00d4ff;
    }

    /* ── Card ── */
    .rp-card {
        background: rgba(255,255,255,0.05);
        border: 1px solid rgba(255,255,255,0.09);
        border-radius: 16px; margin-bottom: 12px;
        overflow: hidden; transition: border-color 0.25s;
    }
    .rp-card.is-current { border-color: rgba(0,212,255,0.3); background: rgba(0,212,255,0.03); }
    .rp-card:hover { border-color: rgba(0,212,255,0.2); }

    .rp-card-header {
        display: flex; justify-content: space-between; align-items: center;
        padding: 15px 22px; cursor: pointer; flex-wrap: wrap; gap: 10px;
        user-select: none;
    }
    .rp-card-meta { display: flex; align-items: center; gap: 12px; flex-wrap: wrap; }
    .rp-week-pill {
        background: rgba(0,212,255,0.12); border: 1px solid rgba(0,212,255,0.3);
        color: #00d4ff; font-size: 0.7rem; font-weight: 700;
        letter-spacing: 1.5px; text-transform: uppercase;
        padding: 4px 12px; border-radius: 20px; white-space: nowrap;
    }
    .rp-date-range { color: rgba(255,255,255,0.65); font-size: 0.86rem; }
    .rp-current-tag {
        background: rgba(0,255,150,0.1); border: 1px solid rgba(0,255,150,0.3);
        color: #00ff96; font-size: 0.65rem; font-weight: 700;
        letter-spacing: 1.2px; text-transform: uppercase;
        padding: 3px 10px; border-radius: 20px;
    }

    .btn-generate {
        background: rgba(0,212,255,0.12); color: #00d4ff;
        border: 1px solid rgba(0,212,255,0.35); border-radius: 9px;
        padding: 7px 18px; font-size: 0.82rem; font-weight: 600;
        cursor: pointer; transition: 0.22s;
        display: flex; align-items: center; gap: 7px; white-space: nowrap;
    }
    .btn-generate:hover { background: #00d4ff; color: #000; }
    .btn-generate.done {
        background: rgba(0,255,150,0.1); border-color: rgba(0,255,150,0.3);
        color: #00ff96; pointer-events: none;
    }
    .btn-generate.loading { opacity: 0.6; pointer-events: none; }

    .btn-print {
        background: none; border: 1px solid rgba(255,255,255,0.15); color: rgba(255,255,255,0.5);
        font-size: 0.9rem; cursor: pointer; padding: 6px 10px;
        border-radius: 6px; transition: 0.2s; display: flex; align-items: center; gap: 5px;
    }
    .btn-print:hover { border-color: #00d4ff; color: #00d4ff; background: rgba(0,212,255,0.08); }
    .btn-print[disabled] { opacity: 0.35; cursor: not-allowed; }

    .rp-card-body { display: none; padding: 0 22px 22px; border-top: 1px solid rgba(255,255,255,0.06); }
    .rp-card-body.open { display: block; }

    .rp-placeholder { text-align: center; padding: 32px; color: rgba(255,255,255,0.25); font-size: 0.83rem; }
    .rp-placeholder i { font-size: 2rem; display: block; margin-bottom: 10px; }

    .rp-section {
        font-size: 0.75rem; font-weight: 700; text-transform: uppercase;
        letter-spacing: 1px; color: #00d4ff;
        border-left: 3px solid #00d4ff; padding-left: 10px;
        margin: 22px 0 12px; display: flex; align-items: center; gap: 8px;
    }

    .rp-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(175px, 1fr)); gap: 12px; }
    .rp-tile {
        background: rgba(255,255,255,0.04); border: 1px solid rgba(255,255,255,0.08);
        border-radius: 12px; padding: 14px 16px; transition: 0.2s;
    }
    .rp-tile:hover { background: rgba(255,255,255,0.07); border-color: rgba(0,212,255,0.2); }
    .rp-tile-label {
        font-size: 0.65rem; text-transform: uppercase; letter-spacing: 1px;
        color: rgba(255,255,255,0.4); margin-bottom: 7px;
        display: flex; align-items: center; gap: 6px;
    }
    .rp-tile-label i { color: #00d4ff; }
    .rp-tile-val { font-size: 1.55rem; font-weight: 700; color: #fff; line-height: 1; }
    .rp-tile-sub { font-size: 0.68rem; color: rgba(255,255,255,0.3); margin-top: 4px; }
    .accent-green .rp-tile-val { color: #00ff96; }
    .accent-yellow .rp-tile-val { color: #ffc107; }
    .accent-red .rp-tile-val { color: #ff5c7a; }
    .accent-purple .rp-tile-val { color: #bf94ff; }
    .accent-cyan .rp-tile-val { color: #00d4ff; }

    .rp-spinner {
        width: 13px; height: 13px; border-radius: 50%;
        border: 2px solid rgba(0,212,255,0.25);
        border-top-color: #00d4ff;
        animation: spin 0.7s linear infinite; display: inline-block;
    }
    @keyframes spin { to { transform: rotate(360deg); } }
    @keyframes fadeUp { from { opacity: 0; transform: translateY(8px); } to { opacity: 1; transform: translateY(0); } }
    .fade-up { animation: fadeUp 0.3s ease forwards; }
</style>

<div class="report-page">

    {{-- Page Header --}}
    <div class="rp-header">
        <div>
            <h2><i class="bi bi-clipboard2-pulse"></i> {{ $filterLabel }} System Reports</h2>
            <p class="rp-subtitle">Integrated Lab System &nbsp;·&nbsp; Staff Console</p>
        </div>
        <span class="rp-system-badge"><i class="bi bi-person-badge me-1"></i> Staff View</span>
    </div>

    {{-- Filter Tabs --}}
    <div class="rp-tabs">
        <a href="{{ route('staff.reports.index', ['filter' => 'daily']) }}"
           class="rp-tab {{ $filter === 'daily' ? 'active' : '' }}">
            <i class="bi bi-calendar-day"></i> Daily
        </a>
        <a href="{{ route('staff.reports.index', ['filter' => 'weekly']) }}"
           class="rp-tab {{ $filter === 'weekly' ? 'active' : '' }}">
            <i class="bi bi-calendar-week"></i> Weekly
        </a>
        <a href="{{ route('staff.reports.index', ['filter' => 'monthly']) }}"
           class="rp-tab {{ $filter === 'monthly' ? 'active' : '' }}">
            <i class="bi bi-calendar3"></i> Monthly
        </a>
        <a href="{{ route('staff.reports.index', ['filter' => 'halfyear']) }}"
           class="rp-tab {{ $filter === 'halfyear' ? 'active' : '' }}">
            <i class="bi bi-calendar-range"></i> Half a Year
        </a>
    </div>

    {{-- Period Cards --}}
    @foreach($periods as $index => $period)
    <div class="rp-card {{ $period['is_current'] ? 'is-current' : '' }}" id="card-{{ $index }}">

        <div class="rp-card-header" onclick="toggleBody({{ $index }})">
            <div class="rp-card-meta">
                <span class="rp-week-pill"><i class="bi bi-calendar3"></i> {{ $period['label'] }}</span>
                <span class="rp-date-range">{{ $period['start_disp'] }} – {{ $period['end_disp'] }}</span>
                @if($period['is_current'])
                    <span class="rp-current-tag"><i class="bi bi-circle-fill" style="font-size:0.45rem;"></i> Current</span>
                @endif
            </div>
            <div class="rp-card-actions" style="display:flex; align-items:center; gap:8px;">
                <button class="btn-generate" id="genbtn-{{ $index }}"
                    onclick="generateReport(event, {{ $index }}, '{{ $period['start'] }}', '{{ $period['end'] }}')">
                    <i class="bi bi-file-earmark-bar-graph"></i> Generate Report
                </button>
                <button class="btn-print" id="printbtn-{{ $index }}" title="Print this report" disabled
                    onclick="printReport(event, {{ $index }}, '{{ $period['start'] }}', '{{ $period['end'] }}')">
                    <i class="bi bi-printer"></i>
                </button>
            </div>
        </div>

        <div class="rp-card-body" id="body-{{ $index }}">
            <div class="rp-placeholder" id="placeholder-{{ $index }}">
                <i class="bi bi-bar-chart-line"></i>
                Click <strong>Generate Report</strong> to compile data for {{ $period['label'] }}.
            </div>
        </div>
    </div>
    @endforeach

</div>

<script>
    const generated = {};
    const periodMeta = @json($periods); // label / start_disp / end_disp per card, for the print header
    const labLogoUrl = "{{ asset('images/SMHLogo.png') }}";

    function generateReport(e, idx, start, end) {
        e.stopPropagation();

        const btn      = document.getElementById('genbtn-' + idx);
        const printBtn = document.getElementById('printbtn-' + idx);
        const body     = document.getElementById('body-' + idx);
        const ph       = document.getElementById('placeholder-' + idx);

        if (generated[idx]) {
            body.classList.toggle('open');
            return Promise.resolve();
        }

        body.classList.add('open');
        btn.classList.add('loading');
        btn.innerHTML = '<span class="rp-spinner"></span> Compiling…';
        ph.innerHTML  = '<i class="bi bi-hourglass-split" style="color:#00d4ff;font-size:1.6rem;display:block;margin-bottom:8px;"></i><span style="color:rgba(255,255,255,0.35);font-size:0.8rem;">Fetching data…</span>';

        return fetch(`{{ route('staff.reports.generate') }}?start=${start}&end=${end}`)
            .then(res => res.json())
            .then(data => {
                renderReport(idx, data);
                btn.classList.remove('loading');
                btn.classList.add('done');
                btn.innerHTML = '<i class="bi bi-check-circle-fill"></i> Report Ready';
                printBtn.removeAttribute('disabled');
                generated[idx] = true;
            })
            .catch(() => {
                btn.classList.remove('loading');
                btn.innerHTML = '<i class="bi bi-exclamation-triangle"></i> Retry';
                ph.innerHTML = '<span style="color:#ff5c7a;">Failed to load report. Please try again.</span>';
            });
    }

    function renderReport(idx, data) {
        const ph = document.getElementById('placeholder-' + idx);
        const a  = data.appointments;
        const l  = data.lab_results;
        const p  = data.patients;
        const approvalRate = a.total > 0 ? Math.round((a.approved / a.total) * 100) : 0;

        ph.className = 'fade-up';
        ph.innerHTML = `
            <div class="rp-section"><i class="bi bi-calendar2-check"></i> Appointment Summary</div>
            <div class="rp-grid">
                <div class="rp-tile accent-cyan">
                    <div class="rp-tile-label"><i class="bi bi-send"></i> Total Requests</div>
                    <div class="rp-tile-val">${a.total}</div>
                    <div class="rp-tile-sub">submitted this period</div>
                </div>
                <div class="rp-tile accent-green">
                    <div class="rp-tile-label"><i class="bi bi-check2-circle"></i> Approved</div>
                    <div class="rp-tile-val">${a.approved}</div>
                    <div class="rp-tile-sub">${approvalRate}% approval rate</div>
                </div>
                <div class="rp-tile accent-yellow">
                    <div class="rp-tile-label"><i class="bi bi-hourglass-split"></i> Pending</div>
                    <div class="rp-tile-val">${a.pending}</div>
                    <div class="rp-tile-sub">awaiting action</div>
                </div>
                <div class="rp-tile accent-red">
                    <div class="rp-tile-label"><i class="bi bi-x-circle"></i> Cancelled</div>
                    <div class="rp-tile-val">${a.cancelled}</div>
                    <div class="rp-tile-sub">by patient or staff</div>
                </div>
                <div class="rp-tile">
                    <div class="rp-tile-label"><i class="bi bi-house-heart"></i> Home Service</div>
                    <div class="rp-tile-val">${a.home}</div>
                    <div class="rp-tile-sub">field appointments</div>
                </div>
                <div class="rp-tile">
                    <div class="rp-tile-label"><i class="bi bi-laptop"></i> Online Booking</div>
                    <div class="rp-tile-val">${a.clinic}</div>
                    <div class="rp-tile-sub">booked online</div>
                </div>
            </div>

            <div class="rp-section"><i class="bi bi-file-medical"></i> Lab Results Activity</div>
            <div class="rp-grid">
                <div class="rp-tile accent-green">
                    <div class="rp-tile-label"><i class="bi bi-filetype-pdf"></i> Results Processed</div>
                    <div class="rp-tile-val">${l.processed}</div>
                    <div class="rp-tile-sub">appointments with results</div>
                </div>
                <div class="rp-tile accent-yellow">
                    <div class="rp-tile-label"><i class="bi bi-clock-history"></i> Awaiting Result</div>
                    <div class="rp-tile-val">${l.unprocessed}</div>
                    <div class="rp-tile-sub">approved, not yet processed</div>
                </div>
            </div>

            <div class="rp-section"><i class="bi bi-people"></i> Patient Data</div>
            <div class="rp-grid">
                <div class="rp-tile accent-green">
                    <div class="rp-tile-label"><i class="bi bi-person-plus"></i> New Patients</div>
                    <div class="rp-tile-val">${p.new}</div>
                    <div class="rp-tile-sub">registered this period</div>
                </div>
                <div class="rp-tile">
                    <div class="rp-tile-label"><i class="bi bi-arrow-repeat"></i> Returning Patients</div>
                    <div class="rp-tile-val">${p.returning}</div>
                    <div class="rp-tile-sub">had prior appointments</div>
                </div>
                <div class="rp-tile accent-purple">
                    <div class="rp-tile-label"><i class="bi bi-database"></i> Total in System</div>
                    <div class="rp-tile-val">${p.total}</div>
                    <div class="rp-tile-sub">cumulative patient records</div>
                </div>
            </div>
        `;
    }

    function toggleBody(idx) {
        document.getElementById('body-' + idx).classList.toggle('open');
    }

    // ── Print: works for any period type (daily/weekly/monthly/half-year) ──
    // Builds a fully self-contained print document in a hidden iframe, so
    // the printout can never pick up shadows/backgrounds/margins from the
    // staff layout (topbar, sidebar, card shells, etc).
    function printReport(e, idx, start, end) {
        e.stopPropagation();

        const doPrint = () => openPrintFrame(idx, periodMeta[idx]);

        if (!generated[idx]) {
            generateReport(e, idx, start, end).then(doPrint);
            return;
        }

        doPrint();
    }

    function openPrintFrame(idx, meta) {
        const reportHtml = document.getElementById('placeholder-' + idx).innerHTML;

        const doc = `
            <!DOCTYPE html>
            <html>
            <head>
            <meta charset="utf-8">
            <title>${meta.label} System Report</title>
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
                .print-head { padding-bottom: 10px; margin-bottom: 20px; }
                .print-head h1 { margin: 0 0 4px; font-size: 1.15rem; }
                .print-head p { margin: 0; font-size: 0.85rem; color: #333; }
                .rp-section {
                    font-size: 0.8rem; font-weight: 700; text-transform: uppercase;
                    letter-spacing: 1px; color: #000;
                    border-left: 3px solid #000; padding-left: 10px;
                    margin: 22px 0 12px;
                }
                .rp-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(160px, 1fr)); gap: 10px; }
                .rp-tile {
                    border: 1px solid #ccc; border-radius: 6px; padding: 12px 14px;
                    background: #fff; box-shadow: none;
                }
                .rp-tile-label {
                    font-size: 0.65rem; text-transform: uppercase; letter-spacing: 1px;
                    color: #555; margin-bottom: 6px;
                }
                .rp-tile-val { font-size: 1.4rem; font-weight: 700; color: #000; }
                .rp-tile-sub { font-size: 0.68rem; color: #777; margin-top: 3px; }
                .bi { display: none; } /* icon font not loaded in the print frame */
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
                    <h1>${meta.label} System Report</h1>
                    <p>${meta.start_disp} &ndash; ${meta.end_disp}</p>
                </div>
                ${reportHtml}
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