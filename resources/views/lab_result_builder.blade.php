@extends('layouts.adminlayout')

@section('title', 'Lab Result Builder')

@section('admincontent')

{{-- Google Fonts --}}
<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700&family=Source+Serif+4:ital,opsz,wght@0,8..60,400;0,8..60,600;1,8..60,400&display=swap" rel="stylesheet">

<style>
/* ── Page shell ──────────────────────────────────────────── */
.lrb-shell {
    padding: 28px 10px;
    font-family: 'Inter', system-ui, sans-serif;
    background: transparent;
}

/* ── Top bar ─────────────────────────────────────────────── */
.lrb-topbar {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 24px;
    flex-wrap: wrap;
    gap: 12px;
}
.lrb-topbar-title { font-size: 22px; font-weight: 700; color: #fff; letter-spacing: -.5px; }
.lrb-topbar-sub   { font-size: 13px; color: rgba(0,212,255,.7); margin-top: 2px; }
.lrb-btn {
    border-radius: 20px; padding: 8px 20px; font-size: 13px;
    cursor: pointer; font-weight: 600; border: none; transition: all .2s;
}
.lrb-btn-outline {
    background: rgba(255,255,255,.07);
    border: 1px solid rgba(255,255,255,.15);
    color: #cdd6e0;
}
.lrb-btn-outline.active {
    background: rgba(0,212,255,.15);
    border-color: rgba(0,212,255,.5);
    color: #00d4ff;
}
.lrb-btn-primary {
    background: linear-gradient(135deg,#0d6efd,#00d4ff);
    color: #fff; font-weight: 700;
    box-shadow: 0 4px 18px rgba(13,110,253,.35);
}
.lrb-btn-primary:hover { opacity: .9; }

/* ── Layout grid ─────────────────────────────────────────── */
.lrb-grid { display: grid; grid-template-columns: 300px 1fr; gap: 20px; align-items: start; }
.lrb-grid.preview-mode { grid-template-columns: 1fr; }

/* ── Left panel ──────────────────────────────────────────── */
.lrb-panel {
    background: rgba(255,255,255,.06);
    border: 1px solid rgba(255,255,255,.12);
    border-radius: 20px; padding: 20px;
    position: sticky;
    top: 100px;
    max-height: calc(100vh - 120px);
    overflow-y: auto;
}
.lrb-panel::-webkit-scrollbar { width: 4px; }
.lrb-panel::-webkit-scrollbar-thumb { background: rgba(255,255,255,.15); border-radius: 4px; }

.lrb-panel-label {
    font-size: 11px; text-transform: uppercase; letter-spacing: 1.5px;
    color: rgba(0,212,255,.7); font-weight: 700; margin-bottom: 14px;
}
.lrb-field { margin-bottom: 12px; }
.lrb-field-label {
    font-size: 10px; color: rgba(0,212,255,.6);
    text-transform: uppercase; letter-spacing: 1px; margin-bottom: 4px;
}
.lrb-input, .lrb-select, .lrb-textarea {
    width: 100%; background: rgba(255,255,255,.06);
    border: 1px solid rgba(255,255,255,.12);
    color: #e8f0fe; border-radius: 10px; padding: 7px 11px;
    font-size: 12px; outline: none; font-family: inherit; box-sizing: border-box;
}
.lrb-input:focus, .lrb-select:focus, .lrb-textarea:focus {
    border-color: rgba(0,212,255,.4);
    background: rgba(255,255,255,.09);
}
.lrb-select option { background: #1a2a3a; color: #e8f0fe; }
.lrb-textarea { resize: vertical; min-height: 70px; }
.lrb-divider {
    border: none; border-top: 1px solid rgba(255,255,255,.08);
    margin: 16px 0;
}

/* ── Paper wrapper — dark glass card holding the white paper  */
.lrb-paper-wrapper {
    background: rgba(0,0,0,.25);
    border: 1px solid rgba(255,255,255,.1);
    border-radius: 20px;
    padding: 24px;
}

/* ── Paper (printable area) ──────────────────────────────── */
.lrb-paper {
    background: #fff;
    font-family: 'Source Serif 4', Georgia, serif;
    color: #0d1b2a;
    max-width: 794px;
    margin: 0 auto;
    padding: 36px 40px 40px;
    border-radius: 10px;
    box-shadow: 0 12px 48px rgba(0,0,0,.55);
    position: relative;
    z-index: 1;
}

/* ── Lab header ──────────────────────────────────────────── */
.lrb-header {
    display: flex; justify-content: space-between; align-items: flex-start;
    padding-bottom: 16px; border-bottom: 2px solid #0d6efd;
}
.lrb-logo-wrap { display: flex; align-items: center; gap: 14px; }
.lrb-logo-icon {
    width: 56px; height: 56px; border-radius: 50%;
    background: linear-gradient(135deg,#0d6efd,#00d4ff);
    display: flex; align-items: center; justify-content: center; flex-shrink: 0;
}
.lrb-lab-name {
    font-family: 'Playfair Display', serif;
    font-size: 20px; font-weight: 700; color: #0d1b2a; letter-spacing: -.5px;
}
.lrb-lab-tagline { font-size: 10.5px; color: #5a6a7e; margin-top: 1px; }
.lrb-lab-contact { font-size: 9.5px; color: #8a9ab0; margin-top: 1px; }
.lrb-control-no { text-align: right; }
.lrb-control-no .lrb-meta-label {
    font-size: 9px; text-transform: uppercase; letter-spacing: 1.5px;
    color: #7a8da0; font-weight: 700;
}
.lrb-control-no .lrb-control-val {
    font-family: 'Playfair Display', serif;
    font-size: 15px; font-weight: 700; color: #0d6efd;
}
.lrb-control-no .lrb-control-date { font-size: 10px; color: #8a9ab0; margin-top: 4px; }

/* ── Report title banner ─────────────────────────────────── */
.lrb-title-banner {
    background: linear-gradient(90deg,#0d6efd,#00b4d8);
    color: #fff; text-align: center; padding: 7px 0; margin-top: 14px;
    border-radius: 6px; font-size: 12px; font-weight: 700;
    letter-spacing: 1.5px; text-transform: uppercase;
}

/* ── Patient info grid ───────────────────────────────────── */
.lrb-info-grid {
    display: grid; grid-template-columns: repeat(3,1fr); gap: 8px 20px;
    margin-top: 14px; padding: 12px 14px;
    background: #f7fafd; border-radius: 8px; border: 1px solid #e0eaf5;
}
.lrb-info-label {
    font-size: 9px; text-transform: uppercase; letter-spacing: 1px;
    color: #7a8da0; font-weight: 600;
}
.lrb-info-value {
    font-size: 13px; color: #0d1b2a; font-weight: 600;
    border-bottom: 1px solid #dde6ef; padding-bottom: 3px; margin-top: 2px;
    min-height: 20px;
}

/* ── Result table ────────────────────────────────────────── */
.lrb-section-wrap { margin-bottom: 18px; margin-top: 18px; }
.lrb-section-title {
    background: linear-gradient(90deg,rgba(13,110,253,.1),rgba(0,212,255,.04));
    border-left: 3px solid #0d6efd;
    padding: 5px 10px; font-size: 10.5px; font-weight: 700;
    text-transform: uppercase; letter-spacing: 1px; color: #0d4fa0;
    margin-bottom: 6px; border-radius: 0 4px 4px 0;
}
.lrb-table { width: 100%; border-collapse: collapse; font-size: 12px; }
.lrb-table th {
    padding: 5px 8px; text-align: left; font-size: 9.5px;
    text-transform: uppercase; letter-spacing: .8px;
    color: #5a6a7e; font-weight: 700; border-bottom: 2px solid #d0dcea;
    background: #f0f5fb;
}
.lrb-table td {
    padding: 5px 8px; border-bottom: 1px solid #e8eff7; color: #1a2d45;
}
.lrb-table tr:nth-child(even) td { background: #f7fafd; }
.lrb-result-input {
    border: none; background: transparent;
    border-bottom: 1.5px solid rgba(13,110,253,.35);
    outline: none; width: 100%; font-size: 12px;
    color: #0d1b2a; padding: 1px 2px; font-family: inherit;
}
.lrb-result-input:focus { border-bottom-color: #0d6efd; }

/* ── Remarks box ─────────────────────────────────────────── */
.lrb-remarks {
    margin-top: 12px; padding: 10px 14px;
    background: #fffbf0; border: 1px solid #f0e0a0;
    border-left: 3px solid #f0a500; border-radius: 6px;
}
.lrb-remarks-label {
    font-size: 9.5px; text-transform: uppercase; letter-spacing: 1px;
    color: #b07000; font-weight: 700; margin-bottom: 4px;
}
.lrb-remarks-text { font-size: 12px; color: #3a2a00; line-height: 1.6; }

/* ── Signatories ─────────────────────────────────────────── */
.lrb-sigs { display: grid; grid-template-columns: 1fr 1fr; gap: 30px; margin-top: 32px; }
.lrb-sig { text-align: center; }
.lrb-sig-line { height: 48px; border-bottom: 1.5px solid #1a2d45; margin-bottom: 6px; }
.lrb-sig-name { font-weight: 700; font-size: 13px; color: #0d1b2a; }
.lrb-sig-role { font-size: 10px; color: #5a6a7e; margin-top: 2px; }
.lrb-sig-lic  { font-size: 9.5px; color: #8a9ab0; margin-top: 1px; }

/* ── Footer ──────────────────────────────────────────────── */
.lrb-footer {
    margin-top: 24px; padding-top: 10px;
    border-top: 1px dashed #ccd8e8;
    display: flex; justify-content: space-between; align-items: center;
}
.lrb-footer-note { font-size: 9px; color: #aab8c8; }
.lrb-footer-badge {
    font-size: 9px; color: #aab8c8;
    background: #f0f5fb; padding: 3px 10px;
    border-radius: 20px; border: 1px solid #dde6ef;
}

/* ── Print styles ────────────────────────────────────────── */
@media print {

    /* 1. Reset html/body so the admin layout's dark background doesn't show */
    html, body {
        background: #fff !important;
        margin: 0 !important;
        padding: 0 !important;
        -webkit-print-color-adjust: exact !important;
        print-color-adjust: exact !important;
    }

    /* 2. Hide EVERYTHING on the page by default */
    body * {
        visibility: hidden !important;
    }

    /* 3. Show ONLY the paper and everything inside it */
    #lrbPaper,
    #lrbPaper * {
        visibility: visible !important;
    }

    /* 4. Position the paper to fill the page from the top-left */
    #lrbPaper {
        position: fixed !important;
        top: 0 !important;
        left: 0 !important;
        width: 100% !important;
        max-width: 100% !important;
        margin: 0 !important;
        padding: 20px 28px !important;
        box-shadow: none !important;
        border-radius: 0 !important;
        background: #fff !important;
        z-index: 9999 !important;
    }

    /* 5. Make result inputs look like plain text (not editable fields) */
    .lrb-result-input {
        border-bottom: 1px solid #bbb !important;
        -webkit-print-color-adjust: exact !important;
        print-color-adjust: exact !important;
    }

    /* 6. Preserve background colors for colored elements */
    .lrb-title-banner,
    .lrb-section-title,
    .lrb-table th,
    .lrb-table tr:nth-child(even) td,
    .lrb-info-grid,
    .lrb-remarks,
    .lrb-logo-icon {
        -webkit-print-color-adjust: exact !important;
        print-color-adjust: exact !important;
    }

    /* 7. Prevent awkward page breaks inside table rows and sections */
    .lrb-section-wrap { page-break-inside: avoid; }
    .lrb-table tr      { page-break-inside: avoid; }
    .lrb-sigs          { page-break-inside: avoid; }
}
</style>

<div class="lrb-shell">
    <div style="max-width:1160px; margin:0 auto;">

        {{-- Top bar --}}
        <div class="lrb-topbar">
            <div>
                <div class="lrb-topbar-title">🧪 Lab Result Builder</div>
                <div class="lrb-topbar-sub">Fill in patient info → enter results → print as PDF</div>
            </div>
            <div style="display:flex; gap:10px; flex-wrap:wrap;">
                <button class="lrb-btn lrb-btn-outline" id="btnPreview" onclick="togglePreview()">
                    👁 Preview
                </button>
                <button class="lrb-btn lrb-btn-primary" onclick="printForm()">
                    🖨 Print / Save PDF
                </button>
            </div>
        </div>

        {{-- Main grid --}}
        <div class="lrb-grid" id="lrbGrid">

            {{-- ── LEFT PANEL ── --}}
            <div class="lrb-panel" id="lrbPanel">

                <div class="lrb-panel-label">Patient & Appointment</div>

                <div class="lrb-field">
                    <div class="lrb-field-label">Patient Full Name</div>
                    <input class="lrb-input" id="f-name" type="text"
                        value="{{ trim(($appointment->first_name ?? '') . ' ' . ($appointment->last_name ?? '')) }}"
                        oninput="syncField('name', this.value)">
                </div>
                <div class="lrb-field">
                    <div class="lrb-field-label">Age</div>
                    <input class="lrb-input" id="f-age" type="text"
                        value="{{ $appointment->age ?? '' }}"
                        oninput="syncField('age', this.value)">
                </div>
                <div class="lrb-field">
                    <div class="lrb-field-label">Sex</div>
                    <select class="lrb-select" onchange="syncField('sex', this.value)">
                        <option value="">—</option>
                        <option {{ ($appointment->sex ?? '') == 'Male'   ? 'selected' : '' }}>Male</option>
                        <option {{ ($appointment->sex ?? '') == 'Female' ? 'selected' : '' }}>Female</option>
                        <option {{ ($appointment->sex ?? '') == 'Other'  ? 'selected' : '' }}>Other</option>
                    </select>
                </div>
                <div class="lrb-field">
                    <div class="lrb-field-label">Address</div>
                    <input class="lrb-input" id="f-address" type="text"
                        value="{{ $appointment->address ?? '' }}"
                        oninput="syncField('address', this.value)">
                </div>
                <div class="lrb-field">
                    <div class="lrb-field-label">Contact No.</div>
                    <input class="lrb-input" id="f-contact" type="text"
                        value="{{ $appointment->contact ?? '' }}"
                        oninput="syncField('contact', this.value)">
                </div>
                <div class="lrb-field">
                    <div class="lrb-field-label">Referring Physician</div>
                    <input class="lrb-input" id="f-doctor" type="text"
                        value="{{ $appointment->doctor ?? '' }}"
                        oninput="syncField('doctor', this.value)">
                </div>
                <div class="lrb-field">
                    <div class="lrb-field-label">Specimen Collection Date</div>
                    <input class="lrb-input" id="f-collected" type="date"
                        oninput="syncField('collected', this.value)">
                </div>
                <div class="lrb-field">
                    <div class="lrb-field-label">Service / Test Type</div>
                    <select class="lrb-select" onchange="changeService(this.value)">
                        @foreach($services as $svc)
                            <option value="{{ $svc }}"
                                {{ ($appointment->service ?? '') == $svc ? 'selected' : '' }}>
                                {{ $svc }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <hr class="lrb-divider">
                <div class="lrb-panel-label">Signatory 1</div>

                <div class="lrb-field">
                    <div class="lrb-field-label">Full Name</div>
                    <input class="lrb-input" type="text" placeholder="e.g. Juan dela Cruz, RMT"
                        oninput="syncSig('sig1name', this.value)">
                </div>
                <div class="lrb-field">
                    <div class="lrb-field-label">Position / Title</div>
                    <input class="lrb-input" type="text" placeholder="e.g. Medical Technologist"
                        oninput="syncSig('sig1role', this.value)">
                </div>
                <div class="lrb-field">
                    <div class="lrb-field-label">License No.</div>
                    <input class="lrb-input" type="text" placeholder="PRC License No."
                        oninput="syncSig('sig1lic', this.value)">
                </div>

                <hr class="lrb-divider">
                <div class="lrb-panel-label">Signatory 2</div>

                <div class="lrb-field">
                    <div class="lrb-field-label">Full Name</div>
                    <input class="lrb-input" type="text" placeholder="e.g. Dr. Maria Santos, MD"
                        oninput="syncSig('sig2name', this.value)">
                </div>
                <div class="lrb-field">
                    <div class="lrb-field-label">Position / Title</div>
                    <input class="lrb-input" type="text" placeholder="e.g. Pathologist"
                        oninput="syncSig('sig2role', this.value)">
                </div>
                <div class="lrb-field">
                    <div class="lrb-field-label">License No.</div>
                    <input class="lrb-input" type="text" placeholder="PRC License No."
                        oninput="syncSig('sig2lic', this.value)">
                </div>

                <hr class="lrb-divider">
                <div class="lrb-panel-label">Remarks</div>

                <div class="lrb-field">
                    <textarea class="lrb-textarea"
                        placeholder="Optional clinical notes for this result..."
                        oninput="syncNotes(this.value)">{{ $appointment->notes ?? '' }}</textarea>
                </div>

            </div>{{-- end panel --}}

            {{-- ── PAPER WRAPPER (dark glass container) ── --}}
            <div class="lrb-paper-wrapper">

                {{-- ── PRINTABLE PAPER ── --}}
                <div id="lrbPaper" class="lrb-paper">

                    {{-- Header --}}
                    <div class="lrb-header">
                        <div class="lrb-logo-wrap">
                            <div class="lrb-logo-icon">
                                <svg width="28" height="28" viewBox="0 0 24 24" fill="none"
                                    stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M9 3H5a2 2 0 0 0-2 2v4m6-6h10a2 2 0 0 1 2 2v4M9 3v11a1 1 0 0 0 1 1h4a1 1 0 0 0 1-1V3"/>
                                    <path d="M3 9v10a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V9"/>
                                    <line x1="3" y1="9" x2="21" y2="9"/>
                                </svg>
                            </div>
                            <div>
                                <div class="lrb-lab-name">Subic Med Health Laboratory</div>
                                <div class="lrb-lab-contact">
                                    14-A National Highway Mangan-Vaca, Subic, Philippines
                                </div>
                                <div class="lrb-lab-contact">
                                    0935 481 5423 &nbsp;|&nbsp; subicmedhealthlab@gmail.com
                                </div>
                            </div>
                        </div>
                        <div class="lrb-control-no">
                            <div class="lrb-meta-label">Control No.</div>
                            <div class="lrb-control-val" id="p-control">
                                LAB-{{ Str::upper(Str::random(6)) }}
                            </div>
                            <div class="lrb-control-date">
                                Report Date: <span id="p-report-date">{{ now()->format('F d, Y') }}</span>
                            </div>
                            <div class="lrb-control-date">
                                Collection: <span id="p-collected">—</span>
                            </div>
                        </div>
                    </div>

                    {{-- Title banner --}}
                    <div class="lrb-title-banner" id="p-banner">
                        {{ $appointment->service ?? 'Laboratory' }} — Laboratory Report
                    </div>

                    {{-- Patient info --}}
                    <div class="lrb-info-grid">
                        <div>
                            <div class="lrb-info-label">Patient Name</div>
                            <div class="lrb-info-value" id="p-name">
                                {{ trim(($appointment->first_name ?? '—') . ' ' . ($appointment->last_name ?? '')) }}
                            </div>
                        </div>
                        <div>
                            <div class="lrb-info-label">Age</div>
                            <div class="lrb-info-value" id="p-age">{{ $appointment->age ?? '—' }}</div>
                        </div>
                        <div>
                            <div class="lrb-info-label">Sex</div>
                            <div class="lrb-info-value" id="p-sex">{{ $appointment->sex ?? '—' }}</div>
                        </div>
                        <div>
                            <div class="lrb-info-label">Address</div>
                            <div class="lrb-info-value" id="p-address">{{ $appointment->address ?? '—' }}</div>
                        </div>
                        <div>
                            <div class="lrb-info-label">Contact No.</div>
                            <div class="lrb-info-value" id="p-contact">{{ $appointment->contact ?? '—' }}</div>
                        </div>
                        <div>
                            <div class="lrb-info-label">Referring Physician</div>
                            <div class="lrb-info-value" id="p-doctor">{{ $appointment->doctor ?? '—' }}</div>
                        </div>
                    </div>

                    {{-- Result tables (injected by JS) --}}
                    <div id="p-tables"></div>

                    {{-- Remarks --}}
                    <div class="lrb-remarks" id="p-remarks-box" style="display:none;">
                        <div class="lrb-remarks-label">Clinical Remarks / Notes</div>
                        <div class="lrb-remarks-text" id="p-remarks-text"></div>
                    </div>

                    {{-- Signatories --}}
                    <div class="lrb-sigs">
                        <div class="lrb-sig">
                            <div class="lrb-sig-line"></div>
                            <div class="lrb-sig-name" id="p-sig1name" style="min-height:18px;">—</div>
                            <div class="lrb-sig-role" id="p-sig1role" style="min-height:16px;"></div>
                            <div class="lrb-sig-lic"  id="p-sig1lic"  style="min-height:14px;"></div>
                        </div>
                        <div class="lrb-sig">
                            <div class="lrb-sig-line"></div>
                            <div class="lrb-sig-name" id="p-sig2name" style="min-height:18px;">—</div>
                            <div class="lrb-sig-role" id="p-sig2role" style="min-height:16px;"></div>
                            <div class="lrb-sig-lic"  id="p-sig2lic"  style="min-height:14px;"></div>
                        </div>
                    </div>

                    {{-- Footer --}}
                    <div class="lrb-footer">
                        <div class="lrb-footer-note">
                            This report is confidential and intended solely for the named patient and the referring physician.
                        </div>
                        <div class="lrb-footer-badge">Subic Med Health Laboratory</div>
                    </div>

                </div>{{-- end paper --}}
            </div>{{-- end paper-wrapper --}}

        </div>{{-- end grid --}}
    </div>
</div>

<script>
const TEMPLATES = {
    "Complete Blood Count": [
        { title: "Hematology", rows: [
            { test:"Hemoglobin",    unit:"g/dL",       ref:"M: 13.5–17.5 | F: 12.0–16.0" },
            { test:"Hematocrit",    unit:"%",           ref:"M: 41–53 | F: 36–46" },
            { test:"RBC Count",     unit:"×10⁶/μL",    ref:"M: 4.5–5.9 | F: 4.0–5.2" },
            { test:"WBC Count",     unit:"×10³/μL",    ref:"4.5–11.0" },
            { test:"Platelet Count",unit:"×10³/μL",    ref:"150–400" },
            { test:"MCV",           unit:"fL",          ref:"80–100" },
            { test:"MCH",           unit:"pg",          ref:"27–33" },
            { test:"MCHC",          unit:"g/dL",        ref:"32–36" },
        ]},
        { title: "Differential Count", rows: [
            { test:"Neutrophils",   unit:"%", ref:"50–70" },
            { test:"Lymphocytes",   unit:"%", ref:"20–40" },
            { test:"Monocytes",     unit:"%", ref:"2–8" },
            { test:"Eosinophils",   unit:"%", ref:"1–4" },
            { test:"Basophils",     unit:"%", ref:"0.5–1" },
        ]},
    ],
    "Urinalysis": [
        { title: "Physical Examination", rows: [
            { test:"Color",           unit:"—", ref:"Yellow" },
            { test:"Transparency",    unit:"—", ref:"Clear" },
            { test:"Specific Gravity",unit:"—", ref:"1.005–1.030" },
            { test:"pH",              unit:"—", ref:"4.5–8.0" },
        ]},
        { title: "Chemical Examination", rows: [
            { test:"Protein",              unit:"—", ref:"Negative" },
            { test:"Glucose",              unit:"—", ref:"Negative" },
            { test:"Ketones",              unit:"—", ref:"Negative" },
            { test:"Blood",                unit:"—", ref:"Negative" },
            { test:"Bilirubin",            unit:"—", ref:"Negative" },
            { test:"Nitrite",              unit:"—", ref:"Negative" },
            { test:"Leukocyte Esterase",   unit:"—", ref:"Negative" },
        ]},
        { title: "Microscopic Examination", rows: [
            { test:"RBC",             unit:"/hpf", ref:"0–2" },
            { test:"WBC",             unit:"/hpf", ref:"0–5" },
            { test:"Epithelial Cells",unit:"/hpf", ref:"Few" },
            { test:"Bacteria",        unit:"—",    ref:"None" },
            { test:"Casts",           unit:"—",    ref:"None seen" },
            { test:"Crystals",        unit:"—",    ref:"None seen" },
        ]},
    ],
    "Blood Chemistry": [
        { title: "Glucose", rows: [
            { test:"Fasting Blood Sugar",    unit:"mg/dL", ref:"70–100" },
            { test:"2-Hour Post Prandial",   unit:"mg/dL", ref:"< 140" },
            { test:"HbA1c",                  unit:"%",     ref:"< 5.7" },
        ]},
        { title: "Lipid Profile", rows: [
            { test:"Total Cholesterol", unit:"mg/dL", ref:"< 200" },
            { test:"HDL Cholesterol",   unit:"mg/dL", ref:"M: > 40 | F: > 50" },
            { test:"LDL Cholesterol",   unit:"mg/dL", ref:"< 100" },
            { test:"Triglycerides",     unit:"mg/dL", ref:"< 150" },
            { test:"VLDL",              unit:"mg/dL", ref:"2–30" },
        ]},
        { title: "Renal Function", rows: [
            { test:"Creatinine", unit:"mg/dL", ref:"M: 0.7–1.2 | F: 0.5–1.0" },
            { test:"BUN",        unit:"mg/dL", ref:"7–20" },
            { test:"Uric Acid",  unit:"mg/dL", ref:"M: 3.5–7.2 | F: 2.6–6.0" },
        ]},
        { title: "Liver Function", rows: [
            { test:"ALT (SGPT)",          unit:"U/L",   ref:"M: 7–56 | F: 7–35" },
            { test:"AST (SGOT)",          unit:"U/L",   ref:"10–40" },
            { test:"Total Bilirubin",     unit:"mg/dL", ref:"0.2–1.2" },
            { test:"Direct Bilirubin",    unit:"mg/dL", ref:"0.0–0.3" },
            { test:"Alkaline Phosphatase",unit:"U/L",   ref:"44–147" },
            { test:"Total Protein",       unit:"g/dL",  ref:"6.3–8.2" },
            { test:"Albumin",             unit:"g/dL",  ref:"3.5–5.0" },
        ]},
    ],
    "Thyroid Function Test": [
        { title: "Thyroid Panel", rows: [
            { test:"TSH",               unit:"mIU/L",  ref:"0.4–4.0" },
            { test:"Free T3 (FT3)",     unit:"pmol/L", ref:"3.5–6.5" },
            { test:"Free T4 (FT4)",     unit:"pmol/L", ref:"10.0–20.0" },
            { test:"Total T3",          unit:"nmol/L", ref:"1.1–2.9" },
            { test:"Total T4",          unit:"nmol/L", ref:"60–150" },
            { test:"Anti-TPO Antibody", unit:"IU/mL",  ref:"< 34" },
        ]},
    ],
    "Stool Examination": [
        { title: "Macroscopic Examination", rows: [
            { test:"Color",        unit:"—", ref:"Brown" },
            { test:"Consistency",  unit:"—", ref:"Formed" },
            { test:"Mucus",        unit:"—", ref:"None" },
            { test:"Blood",        unit:"—", ref:"None" },
            { test:"Occult Blood", unit:"—", ref:"Negative" },
        ]},
        { title: "Microscopic Examination", rows: [
            { test:"WBC",          unit:"/hpf", ref:"None" },
            { test:"RBC",          unit:"/hpf", ref:"None" },
            { test:"Fat Globules", unit:"—",    ref:"None" },
            { test:"Yeast Cells",  unit:"—",    ref:"None" },
            { test:"Ova/Parasites",unit:"—",    ref:"None seen" },
        ]},
    ],
    "Pregnancy Test": [
        { title: "Result", rows: [
            { test:"Serum β-hCG",             unit:"mIU/mL", ref:"Non-pregnant: < 5" },
            { test:"Urine hCG (Qualitative)", unit:"—",      ref:"Negative" },
        ]},
    ],
    "X-Ray": [
        { title: "Radiological Findings", rows: [
            { test:"View",                unit:"—", ref:"PA / Lateral / AP" },
            { test:"Lung Fields",         unit:"—", ref:"Clear" },
            { test:"Heart Size",          unit:"—", ref:"Normal" },
            { test:"Mediastinum",         unit:"—", ref:"Not widened" },
            { test:"Diaphragm",           unit:"—", ref:"Normal" },
            { test:"Costophrenic Angles", unit:"—", ref:"Sharp" },
            { test:"Bony Structures",     unit:"—", ref:"Intact" },
        ]},
        { title: "Impression", rows: [
            { test:"Radiologist Impression", unit:"—", ref:"—" },
        ]},
    ],
    "ECG / EKG": [
        { title: "ECG Measurements", rows: [
            { test:"Heart Rate",      unit:"bpm", ref:"60–100" },
            { test:"PR Interval",     unit:"ms",  ref:"120–200" },
            { test:"QRS Duration",    unit:"ms",  ref:"< 120" },
            { test:"QT/QTc Interval", unit:"ms",  ref:"< 440" },
            { test:"P Wave",          unit:"—",   ref:"Normal" },
            { test:"T Wave",          unit:"—",   ref:"Normal" },
            { test:"ST Segment",      unit:"—",   ref:"Isoelectric" },
            { test:"Rhythm",          unit:"—",   ref:"Normal Sinus Rhythm" },
            { test:"Axis",            unit:"°",   ref:"−30° to +90°" },
        ]},
    ],
};

let currentService = document.querySelector('.lrb-select').value || Object.keys(TEMPLATES)[0];
let isPreview = false;

function buildTables(service) {
    const sections = TEMPLATES[service] || [];
    const container = document.getElementById('p-tables');
    container.innerHTML = '';

    sections.forEach(sec => {
        const wrap = document.createElement('div');
        wrap.className = 'lrb-section-wrap';

        const title = document.createElement('div');
        title.className = 'lrb-section-title';
        title.textContent = sec.title;
        wrap.appendChild(title);

        const table = document.createElement('table');
        table.className = 'lrb-table';

        const thead = table.createTHead();
        const hr = thead.insertRow();
        ['Test / Examination','Result','Unit','Reference Range'].forEach(h => {
            const th = document.createElement('th');
            th.textContent = h;
            hr.appendChild(th);
        });

        const tbody = table.createTBody();
        sec.rows.forEach((row, i) => {
            const tr = tbody.insertRow();
            tr.insertCell().textContent = row.test;

            const resultCell = tr.insertCell();
            const inp = document.createElement('input');
            inp.type = 'text';
            inp.className = 'lrb-result-input';
            inp.placeholder = '—';
            inp.setAttribute('data-test', row.test);
            resultCell.appendChild(inp);

            const unitCell = tr.insertCell();
            unitCell.textContent = row.unit;
            unitCell.style.color = '#7a8da0';
            unitCell.style.fontSize = '11px';

            const refCell = tr.insertCell();
            refCell.textContent = row.ref;
            refCell.style.color = '#5a6a7e';
            refCell.style.fontSize = '11px';
        });

        wrap.appendChild(table);
        container.appendChild(wrap);
    });
}

function syncField(id, val) {
    const map = {
        name: 'p-name', age: 'p-age', sex: 'p-sex',
        address: 'p-address', contact: 'p-contact', doctor: 'p-doctor',
    };
    if (map[id]) document.getElementById(map[id]).textContent = val || '—';
    if (id === 'collected') document.getElementById('p-collected').textContent = val || '—';
}

function syncSig(id, val) {
    const el = document.getElementById('p-' + id);
    if (el) el.textContent = val || (id.endsWith('name') ? '—' : '');
}

function syncNotes(val) {
    const box  = document.getElementById('p-remarks-box');
    const text = document.getElementById('p-remarks-text');
    text.textContent = val;
    box.style.display = val.trim() ? 'block' : 'none';
}

function changeService(val) {
    currentService = val;
    buildTables(val);
    document.getElementById('p-banner').textContent = val + ' — Laboratory Report';
}

function togglePreview() {
    isPreview = !isPreview;
    const panel   = document.getElementById('lrbPanel');
    const grid    = document.getElementById('lrbGrid');
    const btn     = document.getElementById('btnPreview');
    panel.style.display = isPreview ? 'none' : 'block';
    grid.className = isPreview ? 'lrb-grid preview-mode' : 'lrb-grid';
    btn.textContent = isPreview ? '✏️ Edit Mode' : '👁 Preview';
    btn.classList.toggle('active', isPreview);
}

function printForm() {
    window.print();
}

document.addEventListener('DOMContentLoaded', () => {
    buildTables(currentService);
    @if(!empty($appointment->notes))
        syncNotes(@json($appointment->notes));
        document.querySelector('.lrb-textarea').value = @json($appointment->notes);
    @endif
});
</script>

@endsection