@extends('layouts.adminlayout')

@section('title', 'Weekly System Reports')

@section('admincontent')
<style>
    .report-page { padding: 28px 10px; max-width: 960px; margin: 0 auto; }

    /* ── Page Header ── */
    .rp-header { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 32px; flex-wrap: wrap; gap: 16px; }
    .rp-header h2 { font-size: 1.45rem; font-weight: 700; color: #fff; display: flex; align-items: center; gap: 10px; margin: 0; }
    .rp-header h2 i { color: #00d4ff; }
    .rp-subtitle { color: rgba(255,255,255,0.4); font-size: 0.8rem; margin-top: 4px; }
    .rp-system-badge {
        background: rgba(0,212,255,0.1); border: 1px solid rgba(0,212,255,0.3);
        color: #00d4ff; font-size: 0.72rem; font-weight: 700;
        letter-spacing: 1.2px; text-transform: uppercase;
        padding: 6px 16px; border-radius: 30px;
    }

    /* ── Collapse Notice ── */
    .rp-notice {
        display: none; background: rgba(0,212,255,0.06);
        border: 1px dashed rgba(0,212,255,0.3); border-radius: 10px;
        padding: 10px 18px; margin-bottom: 16px;
        align-items: center; gap: 10px; font-size: 0.8rem;
        color: rgba(255,255,255,0.45);
    }
    .rp-notice.visible { display: flex; }
    .rp-notice-btn {
        margin-left: auto; background: none;
        border: 1px solid rgba(0,212,255,0.3); color: #00d4ff;
        padding: 4px 14px; border-radius: 6px; font-size: 0.76rem;
        cursor: pointer; transition: 0.2s; display: flex; align-items: center; gap: 6px;
    }
    .rp-notice-btn:hover { background: rgba(0,212,255,0.12); }

    /* ── Week Card ── */
    .rp-card {
        background: rgba(255,255,255,0.05);
        border: 1px solid rgba(255,255,255,0.09);
        border-radius: 16px; margin-bottom: 12px;
        overflow: hidden; transition: border-color 0.25s;
    }
    .rp-card.is-current { border-color: rgba(0,212,255,0.3); background: rgba(0,212,255,0.03); }
    .rp-card.is-hidden  { display: none; }
    .rp-card:hover { border-color: rgba(0,212,255,0.2); }

    /* ── Card Header ── */
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
    .rp-card-actions { display: flex; align-items: center; gap: 8px; }

    /* ── Buttons ── */
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

    .btn-eye {
        background: none; border: none; color: rgba(255,255,255,0.3);
        font-size: 1rem; cursor: pointer; padding: 5px 7px;
        border-radius: 6px; transition: 0.2s;
    }
    .btn-eye:hover, .btn-eye.active { color: #00d4ff; background: rgba(0,212,255,0.1); }

    /* ── Card Body ── */
    .rp-card-body {
        display: none; padding: 0 22px 22px;
        border-top: 1px solid rgba(255,255,255,0.06);
    }
    .rp-card-body.open { display: block; }

    /* ── Placeholder ── */
    .rp-placeholder {
        text-align: center; padding: 32px;
        color: rgba(255,255,255,0.25); font-size: 0.83rem;
    }
    .rp-placeholder i { font-size: 2rem; display: block; margin-bottom: 10px; }

    /* ── Section Label ── */
    .rp-section {
        font-size: 0.75rem; font-weight: 700; text-transform: uppercase;
        letter-spacing: 1px; color: #00d4ff;
        border-left: 3px solid #00d4ff; padding-left: 10px;
        margin: 22px 0 12px; display: flex; align-items: center; gap: 8px;
    }

    /* ── Stat Grid ── */
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

    /* ── Info Notice (no lab DB) ── */
    .rp-info-note {
        background: rgba(255,193,7,0.07); border: 1px solid rgba(255,193,7,0.25);
        border-left: 3px solid #ffc107; border-radius: 8px;
        padding: 10px 14px; font-size: 0.8rem; color: rgba(255,255,255,0.55);
        display: flex; align-items: flex-start; gap: 10px; margin-top: 12px;
    }
    .rp-info-note i { color: #ffc107; margin-top: 2px; flex-shrink: 0; }

    /* ── Report Footer ── */
    .rp-foot { display: flex; gap: 10px; margin-top: 20px; flex-wrap: wrap; }
    .btn-rp-action {
        display: flex; align-items: center; gap: 7px;
        padding: 8px 18px; border-radius: 9px;
        font-size: 0.8rem; font-weight: 600; cursor: pointer; transition: 0.2s;
    }
    .btn-rp-outline {
        background: none; border: 1px solid rgba(255,255,255,0.15);
        color: rgba(255,255,255,0.6);
    }
    .btn-rp-outline:hover { border-color: #00d4ff; color: #00d4ff; background: rgba(0,212,255,0.07); }
    .btn-rp-primary {
        background: rgba(0,212,255,0.12); border: 1px solid rgba(0,212,255,0.35);
        color: #00d4ff;
    }
    .btn-rp-primary:hover { background: #00d4ff; color: #000; }

    /* ── Spinner ── */
    .rp-spinner {
        width: 13px; height: 13px; border-radius: 50%;
        border: 2px solid rgba(0,212,255,0.25);
        border-top-color: #00d4ff;
        animation: spin 0.7s linear infinite; display: inline-block;
    }
    @keyframes spin { to { transform: rotate(360deg); } }
    @keyframes fadeUp {
        from { opacity: 0; transform: translateY(8px); }
        to   { opacity: 1; transform: translateY(0); }
    }
    .fade-up { animation: fadeUp 0.3s ease forwards; }
</style>

<div class="report-page">

    {{-- Page Header --}}
    <div class="rp-header">
        <div>
            <h2><i class="bi bi-clipboard2-pulse"></i> Weekly System Reports</h2>
            <p class="rp-subtitle">Integrated Lab System &nbsp;·&nbsp; Admin Console</p>
        </div>
        <span class="rp-system-badge"><i class="bi bi-shield-check me-1"></i> Admin View</span>
    </div>

    {{-- Collapse Notice --}}
    <div class="rp-notice" id="rpNotice">
        <i class="bi bi-eye-slash" style="color:#00d4ff;"></i>
        <span>Other weekly reports are hidden. Use the eye icon or show all.</span>
        <button class="rp-notice-btn" onclick="showAllWeeks()">
            <i class="bi bi-eye"></i> Show All
        </button>
    </div>

    {{-- Week Cards --}}
    @foreach($weeks as $index => $week)
    <div class="rp-card {{ $week['is_current'] ? 'is-current' : '' }}" id="card-{{ $index }}">

        {{-- Card Header --}}
        <div class="rp-card-header" onclick="toggleBody({{ $index }})">
            <div class="rp-card-meta">
                <span class="rp-week-pill">
                    <i class="bi bi-calendar3"></i> {{ $week['label'] }}
                </span>
                <span class="rp-date-range">{{ $week['start'] }} – {{ $week['end'] }}</span>
                @if($week['is_current'])
                    <span class="rp-current-tag">
                        <i class="bi bi-circle-fill" style="font-size:0.45rem;"></i> Current
                    </span>
                @endif
            </div>
            <div class="rp-card-actions">
                <button class="btn-generate" id="genbtn-{{ $index }}"
                    onclick="generateReport(event, {{ $index }})"
                    data-week='@json($week)'>
                    <i class="bi bi-file-earmark-bar-graph"></i> Generate Report
                </button>
                @if($week['is_current'])
                    {{-- Eye: hides/shows other weeks --}}
                    <button class="btn-eye" id="eye-{{ $index }}" title="Hide other weeks"
                        onclick="toggleOthers(event, {{ $index }})">
                        <i class="bi bi-eye"></i>
                    </button>
                @else
                    {{-- Chevron: just expands this card --}}
                    <button class="btn-eye" id="eye-{{ $index }}" title="Expand"
                        onclick="event.stopPropagation(); toggleBody({{ $index }})">
                        <i class="bi bi-chevron-down"></i>
                    </button>
                @endif
            </div>
        </div>

        {{-- Card Body --}}
        <div class="rp-card-body" id="body-{{ $index }}">
            <div class="rp-placeholder" id="placeholder-{{ $index }}">
                <i class="bi bi-bar-chart-line"></i>
                Click <strong>Generate Report</strong> to compile data for {{ $week['label'] }}.
            </div>
        </div>

    </div>
    @endforeach

</div>

<script>
    // Data from Laravel
    const weeks = @json($weeks);
    const generated = {};
    let othersHidden = false;

    // ── Generate Report ──
    function generateReport(e, idx) {
        e.stopPropagation();

        const btn  = document.getElementById('genbtn-' + idx);
        const body = document.getElementById('body-' + idx);
        const ph   = document.getElementById('placeholder-' + idx);

        // If already generated, just toggle body
        if (generated[idx]) {
            body.classList.toggle('open');
            return;
        }

        // Open body + show loading
        body.classList.add('open');
        btn.classList.add('loading');
        btn.innerHTML = '<span class="rp-spinner"></span> Compiling…';
        ph.innerHTML  = '<i class="bi bi-hourglass-split" style="color:#00d4ff;font-size:1.6rem;display:block;margin-bottom:8px;"></i><span style="color:rgba(255,255,255,0.35);font-size:0.8rem;">Fetching data…</span>';

        setTimeout(() => {
            renderReport(idx, weeks[idx]);
            btn.classList.remove('loading');
            btn.classList.add('done');
            btn.innerHTML = '<i class="bi bi-check-circle-fill"></i> Report Ready';
            generated[idx] = true;
        }, 1200);
    }

    // ── Render Report Content ──
    function renderReport(idx, w) {
        const ph = document.getElementById('placeholder-' + idx);
        const a  = w.appointments;
        const l  = w.lab_results;
        const p  = w.patients;
        const approvalRate = a.total > 0 ? Math.round((a.approved / a.total) * 100) : 0;

        ph.className = 'fade-up';
        ph.innerHTML = `

            {{-- ── Appointments ── --}}
            <div class="rp-section"><i class="bi bi-calendar2-check"></i> Online Appointment Summary</div>
            <div class="rp-grid">
                <div class="rp-tile accent-cyan">
                    <div class="rp-tile-label"><i class="bi bi-send"></i> Total Requests</div>
                    <div class="rp-tile-val">${a.total}</div>
                    <div class="rp-tile-sub">submitted this week</div>
                </div>
                <div class="rp-tile accent-green">
                    <div class="rp-tile-label"><i class="bi bi-check2-circle"></i> Approved</div>
                    <div class="rp-tile-val">${a.approved}</div>
                    <div class="rp-tile-sub">${approvalRate}% approval rate</div>
                </div>
                <div class="rp-tile accent-yellow">
                    <div class="rp-tile-label"><i class="bi bi-hourglass-split"></i> Pending</div>
                    <div class="rp-tile-val">${a.pending}</div>
                    <div class="rp-tile-sub">awaiting admin action</div>
                </div>
                <div class="rp-tile accent-red">
                    <div class="rp-tile-label"><i class="bi bi-x-circle"></i> Cancelled</div>
                    <div class="rp-tile-val">${a.cancelled}</div>
                    <div class="rp-tile-sub">by patient or admin</div>
                </div>
                <div class="rp-tile">
                    <div class="rp-tile-label"><i class="bi bi-house-heart"></i> Home Service</div>
                    <div class="rp-tile-val">${a.home}</div>
                    <div class="rp-tile-sub">field appointments</div>
                </div>
                <div class="rp-tile">
                    <div class="rp-tile-label"><i class="bi bi-building"></i> Clinic Visit</div>
                    <div class="rp-tile-val">${a.clinic}</div>
                    <div class="rp-tile-sub">walk-in / scheduled</div>
                </div>
            </div>

            {{-- ── Lab Results ── --}}
            <div class="rp-section"><i class="bi bi-file-medical"></i> Lab Results Activity</div>
            <div class="rp-grid">
                <div class="rp-tile accent-green">
                    <div class="rp-tile-label"><i class="bi bi-filetype-pdf"></i> PDFs Generated</div>
                    <div class="rp-tile-val">${l.processed}</div>
                    <div class="rp-tile-sub">from approved appointments</div>
                </div>
                <div class="rp-tile accent-yellow">
                    <div class="rp-tile-label"><i class="bi bi-clock-history"></i> Awaiting Result</div>
                    <div class="rp-tile-val">${l.unprocessed}</div>
                    <div class="rp-tile-sub">pending appointments</div>
                </div>
            </div>
            <div class="rp-info-note">
                <i class="bi bi-info-circle"></i>
                <span>Lab results are currently PDF-generated and not stored in the database.
                Counts are derived from appointment status. To track released, revised, and
                flagged results individually, consider adding a
                <code style="color:#ffc107;">result_status</code> column to the
                <code style="color:#ffc107;">appointments</code> table.</span>
            </div>

            {{-- ── Patient Data ── --}}
            <div class="rp-section"><i class="bi bi-people"></i> Patient Data Management</div>
            <div class="rp-grid">
                <div class="rp-tile accent-green">
                    <div class="rp-tile-label"><i class="bi bi-person-plus"></i> New Patients</div>
                    <div class="rp-tile-val">${p.new}</div>
                    <div class="rp-tile-sub">registered this week</div>
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

            {{-- ── Footer Actions ── --}}
            <div class="rp-foot">
                <button class="btn-rp-action btn-rp-outline" onclick="window.print()">
                    <i class="bi bi-printer"></i> Print
                </button>
                <button class="btn-rp-action btn-rp-primary">
                    <i class="bi bi-download"></i> Export PDF
                </button>
            </div>
        `;
    }

    // ── Toggle card body ──
    function toggleBody(idx) {
        document.getElementById('body-' + idx).classList.toggle('open');
    }

    // ── Eye: hide all other weeks ──
    function toggleOthers(e, activeIdx) {
        e.stopPropagation();
        const total = weeks.length;

        if (!othersHidden) {
            for (let i = 0; i < total; i++) {
                if (i !== activeIdx) document.getElementById('card-' + i).classList.add('is-hidden');
            }
            document.getElementById('rpNotice').classList.add('visible');
            document.getElementById('eye-' + activeIdx).classList.add('active');
            othersHidden = true;
        } else {
            showAllWeeks();
        }
    }

    function showAllWeeks() {
        weeks.forEach((_, i) => document.getElementById('card-' + i).classList.remove('is-hidden'));
        document.getElementById('rpNotice').classList.remove('visible');
        document.querySelectorAll('.btn-eye').forEach(b => b.classList.remove('active'));
        othersHidden = false;
    }
</script>

@endsection