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

    .archive-page label.form-label {
        color: rgba(255,255,255,0.75);
        font-size: 0.8rem;
        margin-bottom: 4px;
    }

    .archive-page .form-control {
        background: rgba(255,255,255,0.1);
        border: 1px solid rgba(255,255,255,0.2);
        color: #fff;
        border-radius: 10px;
    }

    .archive-page .form-control:focus {
        background: rgba(255,255,255,0.18);
        color: #fff;
        border-color: #00d4ff;
        box-shadow: 0 0 0 0.2rem rgba(0, 212, 255, 0.15);
    }

    .archive-page .form-control::placeholder {
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

    .archive-table {
        width: 100%;
        border-collapse: separate;
        border-spacing: 0;
        color: #fff;
    }

    .archive-table thead th {
        background: rgba(255,255,255,0.05);
        color: rgba(255,255,255,0.7);
        font-size: 0.8rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        border-bottom: 1px solid rgba(255,255,255,0.15);
        padding: 12px 15px;
    }

    .archive-table tbody td {
        padding: 14px 15px;
        border-bottom: 1px solid rgba(255,255,255,0.08);
        font-size: 0.9rem;
        vertical-align: middle;
    }

    .archive-table tbody tr:hover {
        background: rgba(255,255,255,0.05);
    }

    .status-badge {
        padding: 4px 12px;
        border-radius: 20px;
        font-size: 0.75rem;
        font-weight: 600;
        letter-spacing: 0.3px;
        border: 1px solid;
        background: rgba(0,212,255,0.15);
        color: #00d4ff;
        border-color: #00d4ff;
    }

    .archive-page .pagination .page-link {
        background: rgba(255,255,255,0.08);
        border: 1px solid rgba(255,255,255,0.15);
        color: #fff;
    }
    .archive-page .pagination .page-item.active .page-link {
        background: #00d4ff;
        border-color: #00d4ff;
        color: #002733;
    }
</style>

<div class="container-fluid archive-page">
    <h2 class="mb-4 text-white">
        <i class="bi bi-archive-fill text-info"></i> Archived Records
    </h2>

    {{-- Filters --}}
    <div class="card glass-card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('admin.archive.index') }}" class="row g-2">
                <div class="col-md-4">
                    <label class="form-label">Search Patient Name</label>
                    <input type="text" name="search" class="form-control" placeholder="First or last name..." value="{{ request('search') }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Archived From</label>
                    <input type="date" name="date_from" class="form-control" value="{{ request('date_from') }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Archived To</label>
                    <input type="date" name="date_to" class="form-control" value="{{ request('date_to') }}">
                </div>
                <div class="col-md-2 d-flex align-items-end">
                    <button type="submit" class="btn btn-glass-primary btn-sm w-100 me-1">
                        <i class="bi bi-search"></i> Search
                    </button>
                </div>
                <div class="col-12">
                    <a href="{{ route('admin.archive.index') }}" class="btn btn-glass-secondary btn-sm">Reset</a>
                </div>
            </form>
        </div>
    </div>

    {{-- Table --}}
    <div class="card glass-card">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="archive-table">
                    <thead>
                        <tr>
                            <th>Patient</th>
                            <th>Service</th>
                            <th>Appointment Date</th>
                            <th>Status</th>
                            <th>Archived On</th>
                            <th>Lab Result</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($archives as $archive)
                            <tr>
                                <td>{{ $archive->first_name }} {{ $archive->last_name }}</td>
                                <td>{{ $archive->service }}</td>
                                <td>{{ \Carbon\Carbon::parse($archive->appointment_date)->format('M d, Y') }}</td>
                                <td>
                                    <span class="status-badge">{{ ucfirst($archive->status) }}</span>
                                </td>
                                <td class="text-nowrap">{{ $archive->archived_at->format('M d, Y h:i A') }}</td>
                                <td>
                                    @if($archive->result && $archive->result->file_path)
                                        <a href="{{ route('admin.archive.download', $archive->id) }}" class="btn btn-glass-outline btn-sm">
                                            <i class="bi bi-download"></i> Download
                                        </a>
                                    @else
                                        <span class="text-white-50">No file</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center text-white-50 py-4">No archived records found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="mt-3">
        {{ $archives->links() }}
    </div>
</div>
@endsection