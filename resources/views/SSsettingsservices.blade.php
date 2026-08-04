@extends('layouts.masterlayout')

@section('title', 'Manage Individual Services')

@section('content')
<style>
    .settings-page { padding: 28px 10px; max-width: 900px; margin: 0 auto; }
    .settings-header h2 { font-size: 1.4rem; font-weight: 700; color: #fff; display: flex; align-items: center; gap: 10px; margin-bottom: 4px; }
    .settings-header p { color: rgba(255,255,255,0.4); font-size: 0.8rem; margin-bottom: 22px; }

    .panel { background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.09); border-radius: 16px; padding: 22px; margin-bottom: 20px; }
    .panel h5 { color: #00d4ff; font-size: 0.95rem; margin-bottom: 16px; }

    .form-control { background: rgba(255,255,255,0.06); border: 1px solid rgba(255,255,255,0.15); color: #fff; }
    .form-control:focus { background: rgba(255,255,255,0.1); color: #fff; border-color: #00d4ff; box-shadow: none; }

    .svc-table { width: 100%; border-collapse: collapse; }
    .svc-table th { text-transform: uppercase; font-size: 0.68rem; color: #00d4ff; padding: 10px 12px; text-align: left; letter-spacing: 0.5px; }
    .svc-table td { padding: 10px 12px; border-top: 1px solid rgba(255,255,255,0.07); color: #fff; font-size: 0.85rem; vertical-align: middle; }
    .svc-table tr.inactive td { opacity: 0.4; }
    .svc-price-input { width: 110px; background: rgba(255,255,255,0.06); border: 1px solid rgba(255,255,255,0.15); color: #fff; border-radius: 6px; padding: 4px 8px; }
</style>

<div class="settings-page">
    <div class="settings-header">
        <h2><i class="bi bi-box-seam"></i> Manage Individual Services</h2>
        <p>Standalone tests patients can pick under "Others" in the booking form</p>
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
        <h5><i class="bi bi-plus-circle me-1"></i> Add Service</h5>
        <form method="POST" action="{{ route('staff.settings.package.store') }}" class="row g-2">
            @csrf
            <div class="col-md-7"><input type="text" name="name" class="form-control" placeholder="Service name (e.g. Fecalysis)" required></div>
            <div class="col-md-3"><input type="number" step="0.01" min="0" name="price" class="form-control" placeholder="Price (₱)" required></div>
            <div class="col-md-2"><button type="submit" class="btn btn-info fw-bold text-dark w-100">Add</button></div>
        </form>
    </div>

    <div class="panel">
        <h5><i class="bi bi-list-ul me-1"></i> Existing Services</h5>
        <table class="svc-table">
            <thead><tr><th>Name</th><th>Price</th><th>Status</th><th></th></tr></thead>
            <tbody>
                @forelse($services as $service)
                    {{-- Two standalone forms per row, linked to inputs/buttons via the form="" attribute
                         so the <tr>/<td> structure stays valid HTML (forms can't wrap table cells). --}}
                    <form id="svc-update-{{ $service->id }}" method="POST" action="{{ route('staff.settings.package.update', $service) }}">
                        @csrf @method('PUT')
                    </form>
                    <form id="svc-delete-{{ $service->id }}" method="POST" action="{{ route('staff.settings.package.destroy', $service) }}" onsubmit="return confirm('Remove this service?')">
                        @csrf @method('DELETE')
                    </form>

                    <tr class="{{ $service->is_active ? '' : 'inactive' }}">
                        <td><input form="svc-update-{{ $service->id }}" type="text" name="name" class="form-control svc-price-input" style="width:220px" value="{{ $service->name }}" required></td>
                        <td>₱<input form="svc-update-{{ $service->id }}" type="number" step="0.01" name="price" class="svc-price-input" value="{{ $service->price }}" required></td>
                        <td>
                            <div class="form-check form-switch">
                                <input form="svc-update-{{ $service->id }}" class="form-check-input" type="checkbox" name="is_active" value="1" {{ $service->is_active ? 'checked' : '' }}>
                            </div>
                        </td>
                        <td class="d-flex gap-2">
                            <button form="svc-update-{{ $service->id }}" type="submit" class="btn btn-sm btn-outline-info">Save</button>
                            <button form="svc-delete-{{ $service->id }}" type="submit" class="btn btn-sm btn-outline-danger">Remove</button>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="4" class="text-muted text-center py-3">No services yet.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection