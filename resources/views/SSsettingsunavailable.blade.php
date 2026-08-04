@extends('layouts.masterlayout')

@section('title', 'Block Unavailable Days')

@section('content')
<style>
    .settings-page { padding: 28px 10px; max-width: 800px; margin: 0 auto; }
    .settings-header h2 { font-size: 1.4rem; font-weight: 700; color: #fff; display: flex; align-items: center; gap: 10px; margin-bottom: 4px; }
    .settings-header p { color: rgba(255,255,255,0.4); font-size: 0.8rem; margin-bottom: 22px; }

    .panel { background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.09); border-radius: 16px; padding: 22px; margin-bottom: 20px; }
    .panel h5 { color: #00d4ff; font-size: 0.95rem; margin-bottom: 16px; }

    .form-control { background: rgba(255,255,255,0.06); border: 1px solid rgba(255,255,255,0.15); color: #fff; }
    .form-control:focus { background: rgba(255,255,255,0.1); color: #fff; border-color: #00d4ff; box-shadow: none; }

    .blocked-row { display: flex; justify-content: space-between; align-items: center; padding: 12px 14px; background: rgba(255,255,255,0.04); border: 1px solid rgba(255,255,255,0.08); border-radius: 10px; margin-bottom: 8px; }
    .blocked-row .date { color: #fff; font-weight: 600; font-size: 0.9rem; }
    .blocked-row .reason { color: rgba(255,255,255,0.45); font-size: 0.78rem; margin-top: 2px; }
</style>

<div class="settings-page">
    <div class="settings-header">
        <h2><i class="bi bi-calendar-x"></i> Block Unavailable Days</h2>
        <p>Dates blocked here won't be bookable by patients — great for holidays, inventory days, or planned closures</p>
    </div>

    @if(session('success'))
        <div class="alert alert-success" style="background: rgba(0,255,150,0.1); border: 1px solid rgba(0,255,150,0.3); color: #00ff96;">{{ session('success') }}</div>
    @endif
    @if($errors->any())
        <div class="alert alert-danger" style="background: rgba(255,92,122,0.1); border: 1px solid rgba(255,92,122,0.4); color: #ff5c7a;">
            <ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
        </div>
    @endif

    <div class="panel">
        <h5><i class="bi bi-plus-circle me-1"></i> Block a Date</h5>
        <form method="POST" action="{{ route('staff.settings.unavailable.store') }}" class="row g-2">
            @csrf
            <div class="col-md-5">
                <input type="date" name="date" class="form-control" min="{{ now()->addDay()->toDateString() }}" required>
            </div>
            <div class="col-md-5">
                <input type="text" name="reason" class="form-control" placeholder="Reason (optional) — e.g. Holiday, Inventory Day">
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-info fw-bold text-dark w-100">Block</button>
            </div>
        </form>
    </div>

    <div class="panel">
        <h5><i class="bi bi-list-ul me-1"></i> Currently Blocked Dates</h5>
        @forelse($blockedDates as $blocked)
            <div class="blocked-row">
                <div>
                    <div class="date">{{ \Carbon\Carbon::parse($blocked->date)->format('F d, Y (l)') }}</div>
                    @if($blocked->reason)<div class="reason">{{ $blocked->reason }}</div>@endif
                </div>
                <form method="POST" action="{{ route('staff.settings.unavailable.destroy', $blocked) }}" onsubmit="return confirm('Unblock this date?')">
                    @csrf @method('DELETE')
                    <button type="submit" class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i> Unblock</button>
                </form>
            </div>
        @empty
            <p class="text-muted">No blocked dates yet.</p>
        @endforelse
    </div>
</div>
@endsection