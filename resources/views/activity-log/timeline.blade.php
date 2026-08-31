@extends('layouts.adminlayout')

@section('admincontent')

<style>
    .glass-card {
        background: rgba(255, 255, 255, 0.08);
        backdrop-filter: blur(15px);
        border: 1px solid rgba(255, 255, 255, 0.15);
        border-radius: 16px;
        box-shadow: 0 15px 35px rgba(0,0,0,0.3);
        color: #fff;
    }

    .role-badge {
        padding: 4px 12px;
        border-radius: 20px;
        font-size: 0.75rem;
        font-weight: 600;
        letter-spacing: 0.3px;
        border: 1px solid;
    }
    .role-admin { background: rgba(220,53,69,0.15); color: #ff8a94; border-color: #dc3545; }
    .role-staff { background: rgba(255,193,7,0.15); color: #ffdb70; border-color: #ffc107; }
    .role-patient { background: rgba(0,212,255,0.15); color: #00d4ff; border-color: #00d4ff; }

    .btn-glass-secondary {
        background: rgba(255,255,255,0.1);
        border: 1px solid rgba(255,255,255,0.25);
        color: #fff;
        border-radius: 10px;
    }
    .btn-glass-secondary:hover { background: rgba(255,255,255,0.2); color: #fff; }

    .timeline-page pre {
        background: rgba(255,255,255,0.05);
        border: 1px solid rgba(255,255,255,0.1);
        border-radius: 10px;
        padding: 12px;
        color: #d6faff;
        font-size: 0.8rem;
    }

    .timeline-page summary {
        color: #00d4ff;
        cursor: pointer;
        font-size: 0.85rem;
    }

    /* Vertical connecting line */
    .timeline-wrapper {
        position: relative;
        padding-left: 30px;
    }
    .timeline-wrapper::before {
        content: "";
        position: absolute;
        left: 9px;
        top: 8px;
        bottom: 8px;
        width: 2px;
        background: rgba(0, 212, 255, 0.25);
    }
    .timeline-dot {
        position: absolute;
        left: 0;
        top: 22px;
        width: 20px;
        height: 20px;
        border-radius: 50%;
        background: #00d4ff;
        border: 3px solid rgba(0,0,0,0.3);
        box-shadow: 0 0 8px rgba(0,212,255,0.6);
    }
    .timeline-entry {
        position: relative;
        margin-bottom: 20px;
    }
</style>

<div class="container-fluid timeline-page">
    <h2 class="mb-2 text-white">
        <i class="bi bi-list-check text-info"></i> Appointment #{{ $appointment->id }} — Timeline
    </h2>
    <p class="text-white-50">
        {{ $appointment->first_name }} {{ $appointment->last_name }} —
        {{ $appointment->service }} ({{ $appointment->appointment_type }})
    </p>

    <a href="{{ route('admin.activityLogs.index') }}" class="btn btn-glass-secondary btn-sm mb-4">
        <i class="bi bi-arrow-left"></i> Back to Activity Logs
    </a>
    <a href="{{ route('admin.appointments.timeline.print', $appointment->id) }}" target="_blank" class="btn btn-glass-secondary btn-sm mb-4">
        <i class="bi bi-printer"></i> Print
    </a>

    <div class="timeline-wrapper">
        @forelse($logs as $log)
            <div class="timeline-entry">
                <div class="timeline-dot"></div>
                <div class="card glass-card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between flex-wrap">
                            <div>
                                <span class="role-badge role-{{ $log->user_role }}">
                                    {{ ucfirst($log->user_role) }}
                                </span>
                                <strong class="ms-2">{{ $log->user_name }}</strong>
                            </div>
                            <small class="text-white-50">
                                {{ $log->created_at->format('M d, Y h:i A') }}
                            </small>
                        </div>

                        <p class="mt-2 mb-1">{{ $log->description }}</p>

                        @if($log->old_values || $log->new_values)
                            <details>
                                <summary>View change details</summary>
                                <div class="row mt-2">
                                    @if($log->old_values)
                                        <div class="col-md-6">
                                            <strong class="text-info">Before</strong>
                                            <pre>{{ json_encode($log->old_values, JSON_PRETTY_PRINT) }}</pre>
                                        </div>
                                    @endif
                                    @if($log->new_values)
                                        <div class="col-md-6">
                                            <strong class="text-info">After</strong>
                                            <pre>{{ json_encode($log->new_values, JSON_PRETTY_PRINT) }}</pre>
                                        </div>
                                    @endif
                                </div>
                            </details>
                        @endif
                    </div>
                </div>
            </div>
        @empty
            <p class="text-white-50">No activity recorded for this appointment yet.</p>
        @endforelse
    </div>
</div>
@endsection