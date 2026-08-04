@extends('layouts.masterlayout')

@section('title', 'Modify Prices')

@section('content')
<style>
    .settings-page { padding: 28px 10px; max-width: 900px; margin: 0 auto; }
    .settings-header h2 { font-size: 1.4rem; font-weight: 700; color: #fff; display: flex; align-items: center; gap: 10px; margin-bottom: 4px; }
    .settings-header p { color: rgba(255,255,255,0.4); font-size: 0.8rem; margin-bottom: 22px; }

    .panel { background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.09); border-radius: 16px; padding: 22px; margin-bottom: 20px; }
    .panel h5 { color: #00d4ff; font-size: 0.95rem; margin-bottom: 16px; }

    .price-row { display: flex; align-items: center; justify-content: space-between; padding: 10px 4px; border-bottom: 1px solid rgba(255,255,255,0.06); gap: 10px; }
    .price-row:last-child { border-bottom: none; }
    .price-row .name { color: #fff; font-size: 0.88rem; }
    .price-row .name small { display: block; color: rgba(255,255,255,0.35); font-size: 0.7rem; }
    .price-input-group { display: flex; align-items: center; gap: 8px; }
    .price-input-group input { width: 120px; background: rgba(255,255,255,0.06); border: 1px solid rgba(255,255,255,0.15); color: #fff; border-radius: 6px; padding: 6px 10px; }
</style>

<div class="settings-page">
    <div class="settings-header">
        <h2><i class="bi bi-tag"></i> Modify Prices</h2>
        <p>Quick-edit pricing for promo packages and individual services</p>
    </div>

    @if(session('success'))
        <div class="alert alert-success" style="background: rgba(0,255,150,0.1); border: 1px solid rgba(0,255,150,0.3); color: #00ff96;">{{ session('success') }}</div>
    @endif

    <div class="panel">
        <h5><i class="bi bi-megaphone me-1"></i> Promo Packages</h5>
        @forelse($packages as $package)
            <div class="price-row">
                <div class="name">{{ $package->name }} @if(!$package->is_active)<small>(inactive)</small>@endif</div>
                <form method="POST" action="{{ route('staff.settings.price.package', $package) }}" class="price-input-group">
                    @csrf @method('PUT')
                    <span>₱</span>
                    <input type="number" step="0.01" min="0" name="price" value="{{ $package->price }}" required>
                    <button type="submit" class="btn btn-sm btn-outline-info">Save</button>
                </form>
            </div>
        @empty
            <p class="text-muted">No promo packages yet.</p>
        @endforelse
    </div>

    <div class="panel">
        <h5><i class="bi bi-box-seam me-1"></i> Individual Services</h5>
        @forelse($services as $service)
            <div class="price-row">
                <div class="name">{{ $service->name }} @if(!$service->is_active)<small>(inactive)</small>@endif</div>
                <form method="POST" action="{{ route('staff.settings.price.service', $service) }}" class="price-input-group">
                    @csrf @method('PUT')
                    <span>₱</span>
                    <input type="number" step="0.01" min="0" name="price" value="{{ $service->price }}" required>
                    <button type="submit" class="btn btn-sm btn-outline-info">Save</button>
                </form>
            </div>
        @empty
            <p class="text-muted">No services yet.</p>
        @endforelse
    </div>
</div>
@endsection