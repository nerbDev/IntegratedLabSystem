{{-- resources/views/partials/transactions_table.blade.php --}}

<style>
    .txn-card {
        background: rgba(255,255,255,0.08);
        backdrop-filter: blur(15px);
        -webkit-backdrop-filter: blur(15px);
        border: 1px solid rgba(255,255,255,0.2);
        border-radius: 16px;
        padding: 24px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.25);
    }

    .txn-filter-bar {
        background: rgba(255,255,255,0.06);
        backdrop-filter: blur(15px);
        -webkit-backdrop-filter: blur(15px);
        border: 1px solid rgba(255,255,255,0.15);
        border-radius: 14px;
        padding: 18px 20px;
        margin-bottom: 20px;
    }

    .txn-filter-bar .form-control,
    .txn-filter-bar .form-select {
        background: rgba(255,255,255,0.08);
        border: 1px solid rgba(255,255,255,0.2);
        color: #fff;
        border-radius: 8px;
    }
    .txn-filter-bar .form-control::placeholder { color: rgba(255,255,255,0.55); }
    .txn-filter-bar .form-control:focus,
    .txn-filter-bar .form-select:focus {
        background: rgba(255,255,255,0.12);
        border-color: #00d4ff;
        box-shadow: 0 0 0 0.2rem rgba(0,212,255,0.15);
        color: #fff;
    }
    .txn-filter-bar .form-select option { color: #000; }
    .txn-filter-bar input[type="date"]::-webkit-calendar-picker-indicator { filter: invert(1); cursor: pointer; }

    .txn-filter-bar .btn-primary {
        background: rgba(0,212,255,0.25);
        border: 1px solid rgba(0,212,255,0.5);
        color: #00d4ff;
        font-weight: 600;
    }
    .txn-filter-bar .btn-primary:hover { background: rgba(0,212,255,0.4); color: #fff; }

    .txn-filter-bar .btn-outline-secondary {
        border: 1px solid rgba(255,255,255,0.3);
        color: rgba(255,255,255,0.8);
    }
    .txn-filter-bar .btn-outline-secondary:hover {
        background: rgba(255,255,255,0.1);
        color: #fff;
        border-color: rgba(255,255,255,0.4);
    }

    .txn-table { color: #fff; margin-bottom: 0; }
    .txn-table thead th {
        background: transparent;
        border-bottom: 1px solid rgba(255,255,255,0.2);
        color: rgba(255,255,255,0.6);
        font-size: 0.75rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        font-weight: 600;
        padding-bottom: 12px;
    }
    .txn-table tbody td {
        background: transparent;
        border-bottom: 1px solid rgba(255,255,255,0.08);
        color: rgba(255,255,255,0.9);
        vertical-align: middle;
        padding: 14px 12px;
    }
    .txn-table tbody tr:hover td { background: rgba(255,255,255,0.05); }
    .txn-table tbody tr:last-child td { border-bottom: none; }

    .txn-badge-module {
        background: rgba(255,255,255,0.12);
        border: 1px solid rgba(255,255,255,0.2);
        color: rgba(255,255,255,0.85);
        font-weight: 500;
    }

    .txn-empty { color: rgba(255,255,255,0.5); }

    .txn-pagination .pagination { justify-content: center; margin-top: 16px; }
    .txn-pagination .page-link {
        background: rgba(255,255,255,0.08);
        border: 1px solid rgba(255,255,255,0.15);
        color: rgba(255,255,255,0.8);
    }
    .txn-pagination .page-link:hover { background: rgba(255,255,255,0.15); color: #fff; }
    .txn-pagination .page-item.active .page-link {
        background: rgba(0,212,255,0.3);
        border-color: rgba(0,212,255,0.5);
        color: #fff;
    }
    .txn-pagination .page-item.disabled .page-link {
        background: rgba(255,255,255,0.03);
        color: rgba(255,255,255,0.3);
    }
</style>

<div class="txn-card">
    <div class="table-responsive">
        <table class="table txn-table align-middle">
            <thead>
                <tr>
                    <th>Date &amp; Time</th>
                    <th>Module</th>
                    <th>Action</th>
                    <th>Description</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($transactions as $log)
                    <tr>
                        <td>{{ $log->created_at->format('M d, Y h:i A') }}</td>
                        <td><span class="badge txn-badge-module">{{ $log->module }}</span></td>
                        <td>{{ ucfirst($log->action) }}</td>
                        <td>{{ $log->description }}</td>
                        <td>
                            @php
                                $statusColors = [
                                    'success' => 'success',
                                    'failed'  => 'danger',
                                    'pending' => 'warning',
                                ];
                                $color = $statusColors[$log->status] ?? 'secondary';
                            @endphp
                            <span class="badge bg-{{ $color }}">{{ ucfirst($log->status) }}</span>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center txn-empty py-4">No transactions found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="txn-pagination">
        {{ $transactions->links() }}
    </div>
</div>