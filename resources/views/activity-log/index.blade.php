@extends('layouts.adminlayout')

@section('admincontent')

<style>
    .glass-card {
        background: rgba(255, 255, 255, 0.08);
        backdrop-filter: blur(15px);
        border: 1px solid rgba(255, 255, 255, 0.15);
        border-radius: 20px;
        box-shadow: 0 15px 35px rgba(0,0,0,0.3);
        color: #fff;
    }

    .glass-card .card-header {
        background: rgba(255, 255, 255, 0.05);
        border-bottom: 1px solid rgba(255, 255, 255, 0.15);
        border-radius: 20px 20px 0 0 !important;
        font-weight: 600;
    }

    .activity-page label.form-label {
        color: rgba(255,255,255,0.75);
        font-size: 0.8rem;
        margin-bottom: 4px;
    }

    .activity-page .form-select,
    .activity-page .form-control {
        background: rgba(255,255,255,0.1);
        border: 1px solid rgba(255,255,255,0.2);
        color: #fff;
        border-radius: 10px;
    }

    .activity-page .form-select:focus,
    .activity-page .form-control:focus {
        background: rgba(255,255,255,0.18);
        color: #fff;
        border-color: #00d4ff;
        box-shadow: 0 0 0 0.2rem rgba(0, 212, 255, 0.15);
    }

    .activity-page .form-select option {
        background: #222;
        color: #fff;
    }

    .activity-page .form-control::placeholder {
        color: rgba(255,255,255,0.5);
    }

    .btn-glass-primary {
        background: #00d4ff;
        border: none;
        color: #002733;
        font-weight: 600;
        border-radius: 10px;
    }
    .btn-glass-primary:hover { background: #00b8e0; color: #002733; }

    .btn-glass-secondary {
        background: rgba(255,255,255,0.1);
        border: 1px solid rgba(255,255,255,0.25);
        color: #fff;
        border-radius: 10px;
    }
    .btn-glass-secondary:hover { background: rgba(255,255,255,0.2); color: #fff; }

    .btn-glass-outline {
        background: transparent;
        border: 1px solid #00d4ff;
        color: #00d4ff;
        border-radius: 8px;
        font-size: 0.8rem;
    }
    .btn-glass-outline:hover { background: rgba(0, 212, 255, 0.15); color: #00d4ff; }

    .activity-table {
        width: 100%;
        border-collapse: separate;
        border-spacing: 0;
        color: #fff;
    }

    .activity-table thead th {
        background: rgba(255,255,255,0.05);
        color: rgba(255,255,255,0.7);
        font-size: 0.8rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        border-bottom: 1px solid rgba(255,255,255,0.15);
        padding: 12px 15px;
    }

    .activity-table tbody td {
        padding: 14px 15px;
        border-bottom: 1px solid rgba(255,255,255,0.08);
        font-size: 0.9rem;
        vertical-align: middle;
    }

    .activity-table tbody tr:hover {
        background: rgba(255,255,255,0.05);
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

    /* Modal styles are NOT scoped to .activity-page since modals render outside that container */
    .modal-content {
        background: rgba(30, 30, 30, 0.95);
        backdrop-filter: blur(20px);
        color: white;
        border: 1px solid rgba(255,255,255,0.2);
        border-radius: 16px;
    }

    .modal-body pre {
        background: rgba(255,255,255,0.05);
        border: 1px solid rgba(255,255,255,0.1);
        border-radius: 10px;
        padding: 12px;
        color: #d6faff;
        font-size: 0.8rem;
        white-space: pre-wrap;
        word-break: break-word;
    }

    .activity-page .pagination .page-link {
        background: rgba(255,255,255,0.08);
        border: 1px solid rgba(255,255,255,0.15);
        color: #fff;
    }
    .activity-page .pagination .page-item.active .page-link {
        background: #00d4ff;
        border-color: #00d4ff;
        color: #002733;
    }
</style>

<div class="container-fluid activity-page">
    <h2 class="mb-4 text-white">
        <i class="bi bi-clock-history text-info"></i> Activity Logs
    </h2>

    {{-- Filters --}}
    <div class="card glass-card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('admin.activityLogs.index') }}" class="row g-2">
                <div class="col-md-2">
                    <label class="form-label">Role</label>
                    <select name="user_role" class="form-select">
                        <option value="">All Roles</option>
                        @foreach($roles as $role)
                            <option value="{{ $role }}" {{ request('user_role') == $role ? 'selected' : '' }}>
                                {{ ucfirst($role) }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-2">
                    <label class="form-label">Module</label>
                    <select name="module" class="form-select">
                        <option value="">All Modules</option>
                        @foreach($modules as $module)
                            <option value="{{ $module }}" {{ request('module') == $module ? 'selected' : '' }}>
                                {{ $module }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-2">
                    <label class="form-label">Action</label>
                    <select name="action" class="form-select">
                        <option value="">All Actions</option>
                        @foreach($actions as $action)
                            <option value="{{ $action }}" {{ request('action') == $action ? 'selected' : '' }}>
                                {{ $action }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-2">
                    <label class="form-label">From</label>
                    <input type="date" name="date_from" class="form-control" value="{{ request('date_from') }}">
                </div>

                <div class="col-md-2">
                    <label class="form-label">To</label>
                    <input type="date" name="date_to" class="form-control" value="{{ request('date_to') }}">
                </div>

                <div class="col-md-2">
                    <label class="form-label">Search</label>
                    <input type="text" name="search" class="form-control" placeholder="Description/user..." value="{{ request('search') }}">
                </div>

                <div class="col-12 mt-2">
                    <button type="submit" class="btn btn-glass-primary btn-sm px-3">
                        <i class="bi bi-funnel-fill me-1"></i> Filter
                    </button>
                    <a href="{{ route('admin.activityLogs.index') }}" class="btn btn-glass-secondary btn-sm px-3">
                        Reset
                    </a>
                </div>
            </form>
        </div>
    </div>

    {{-- Table --}}
    <div class="card glass-card">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="activity-table">
                    <thead>
                        <tr>
                            <th>Date/Time</th>
                            <th>User</th>
                            <th>Role</th>
                            <th>Module</th>
                            <th>Action</th>
                            <th>Description</th>
                            <th>Details</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($logs as $log)
                            <tr>
                                <td class="text-nowrap">{{ $log->created_at->format('M d, Y h:i A') }}</td>
                                <td>{{ $log->user_name }}</td>
                                <td>
                                    <span class="role-badge role-{{ $log->user_role }}">
                                        {{ ucfirst($log->user_role) }}
                                    </span>
                                </td>
                                <td>{{ $log->module }}</td>
                                <td>{{ $log->action }}</td>
                                <td>{{ $log->description }}</td>
                                <td class="text-nowrap">
                                    @if($log->old_values || $log->new_values)
                                        <button class="btn btn-glass-outline btn-sm"
                                                data-bs-toggle="modal"
                                                data-bs-target="#logModal{{ $log->id }}">
                                            View
                                        </button>
                                    @else
                                        <span class="text-white-50">—</span>
                                    @endif

                                    @if($log->reference_id)
                                        <a href="{{ route('admin.appointments.timeline', $log->reference_id) }}"
                                           class="btn btn-glass-outline btn-sm">
                                            Timeline
                                        </a>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center text-white-50 py-4">No activity logs found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="mt-3">
        {{ $logs->links() }}
    </div>
</div>

{{-- Modals rendered OUTSIDE the table/table-responsive to avoid Bootstrap positioning/click issues --}}
@foreach($logs as $log)
    @if($log->old_values || $log->new_values)
        <div class="modal fade" id="logModal{{ $log->id }}" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header border-0">
                        <h5 class="modal-title">Change Details</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        @if($log->old_values)
                            <h6 class="text-info">Before</h6>
                            <pre>{{ json_encode($log->old_values, JSON_PRETTY_PRINT) }}</pre>
                        @endif
                        @if($log->new_values)
                            <h6 class="text-info">After</h6>
                            <pre>{{ json_encode($log->new_values, JSON_PRETTY_PRINT) }}</pre>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    @endif
@endforeach

@endsection