@extends('layouts.masterlayout')

@section('title', 'Manage Promo Packages')

@section('content')
<style>
    .settings-page { padding: 28px 10px; max-width: 1000px; margin: 0 auto; }
    .settings-header h2 { font-size: 1.4rem; font-weight: 700; color: #fff; display: flex; align-items: center; gap: 10px; margin-bottom: 4px; }
    .settings-header p { color: rgba(255,255,255,0.4); font-size: 0.8rem; margin-bottom: 22px; }

    .panel {
        background: rgba(255,255,255,0.05);
        border: 1px solid rgba(255,255,255,0.09);
        border-radius: 16px;
        padding: 22px;
        margin-bottom: 20px;
    }
    .panel h5 { color: #00d4ff; font-size: 0.95rem; margin-bottom: 16px; }

    .form-control, .form-select {
        background: rgba(255,255,255,0.06);
        border: 1px solid rgba(255,255,255,0.15);
        color: #fff;
    }
    .form-control:focus, .form-select:focus { background: rgba(255,255,255,0.1); color: #fff; border-color: #00d4ff; box-shadow: none; }
    .form-control::placeholder { color: rgba(255,255,255,0.35); }

    .inclusion-row { display: flex; gap: 8px; margin-bottom: 8px; }
    .btn-remove-inclusion { background: rgba(255,92,122,0.15); border: 1px solid rgba(255,92,122,0.4); color: #ff5c7a; border-radius: 8px; padding: 0 12px; }
    .btn-add-inclusion { background: rgba(0,212,255,0.1); border: 1px solid rgba(0,212,255,0.35); color: #00d4ff; border-radius: 8px; padding: 6px 14px; font-size: 0.8rem; }

    .promo-card { background: rgba(255,255,255,0.04); border: 1px solid rgba(255,255,255,0.08); border-radius: 12px; padding: 16px 18px; margin-bottom: 12px; }
    .promo-card.inactive { opacity: 0.45; }
    .promo-title { display: flex; justify-content: space-between; align-items: center; margin-bottom: 6px; }
    .promo-title h6 { color: #fff; margin: 0; font-size: 1rem; }
    .promo-price { color: #00d4ff; font-weight: 700; }
    .promo-items { color: rgba(255,255,255,0.5); font-size: 0.78rem; margin-bottom: 10px; }
    .fasting-tag { background: rgba(255,193,7,0.12); color: #ffc107; border: 1px solid rgba(255,193,7,0.4); font-size: 0.65rem; padding: 2px 8px; border-radius: 20px; margin-left: 8px; }
</style>

<div class="settings-page">
    <div class="settings-header">
        <h2><i class="bi bi-megaphone"></i> Manage Promo Packages</h2>
        <p>Bundled deals patients can select during booking (e.g. Buntis Package A)</p>
    </div>

    @if(session('success'))
        <div class="alert alert-success" style="background: rgba(0,255,150,0.1); border: 1px solid rgba(0,255,150,0.3); color: #00ff96;">{{ session('success') }}</div>
    @endif
    @if($errors->any())
        <div class="alert alert-danger" style="background: rgba(255,92,122,0.1); border: 1px solid rgba(255,92,122,0.4); color: #ff5c7a;">
            <ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
        </div>
    @endif

    {{-- Add new promo --}}
    <div class="panel">
        <h5><i class="bi bi-plus-circle me-1"></i> Add New Promo</h5>
        <form method="POST" action="{{ route('staff.settings.promo.store') }}">
            @csrf
            <div class="row g-2 mb-3">
                <div class="col-md-7">
                    <input type="text" name="name" class="form-control" placeholder="Promo name (e.g. Buntis Package A)" required>
                </div>
                <div class="col-md-3">
                    <input type="number" step="0.01" min="0" name="price" class="form-control" placeholder="Price (₱)" required>
                </div>
                <div class="col-md-2 d-flex align-items-center">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="requires_fasting" value="1" id="fasting-new">
                        <label class="form-check-label small" for="fasting-new">Requires fasting</label>
                    </div>
                </div>
            </div>

            <label class="small text-muted mb-2 d-block">Inclusions (individual tests in this package)</label>
            <div id="inclusions-container">
                <div class="inclusion-row">
                    <input type="text" name="inclusions[]" class="form-control" placeholder="e.g. Fasting Blood Sugar (FBS)" required>
                    <button type="button" class="btn-remove-inclusion" onclick="this.parentElement.remove()">&times;</button>
                </div>
            </div>
            <button type="button" class="btn-add-inclusion mb-3" onclick="addInclusionRow()">+ Add Item</button>
            <br>
            <button type="submit" class="btn btn-info fw-bold text-dark px-4">Save Promo</button>
        </form>
    </div>

    {{-- Existing promos --}}
    <div class="panel">
        <h5><i class="bi bi-list-ul me-1"></i> Existing Promos</h5>
        @forelse($packages as $package)
            <div class="promo-card {{ $package->is_active ? '' : 'inactive' }}">
                <div class="promo-title">
                    <h6>{{ $package->name }} @if($package->requires_fasting)<span class="fasting-tag">FASTING</span>@endif</h6>
                    <span class="promo-price">₱{{ number_format($package->price, 2) }}</span>
                </div>
                <div class="promo-items">{{ $package->inclusions->pluck('item_name')->join(', ') }}</div>

                <div class="d-flex gap-2">
                    <button class="btn btn-sm btn-outline-info" type="button" data-bs-toggle="collapse" data-bs-target="#edit-{{ $package->id }}">
                        <i class="bi bi-pencil"></i> Edit
                    </button>
                    <form method="POST" action="{{ route('staff.settings.promo.destroy', $package) }}" onsubmit="return confirm('Remove this promo?')">
                        @csrf @method('DELETE')
                        <button class="btn btn-sm btn-outline-danger" type="submit"><i class="bi bi-trash"></i> Remove</button>
                    </form>
                </div>

                <div class="collapse mt-3" id="edit-{{ $package->id }}">
                    <form method="POST" action="{{ route('staff.settings.promo.update', $package) }}">
                        @csrf @method('PUT')
                        <div class="row g-2 mb-2">
                            <div class="col-md-6"><input type="text" name="name" class="form-control" value="{{ $package->name }}" required></div>
                            <div class="col-md-3"><input type="number" step="0.01" name="price" class="form-control" value="{{ $package->price }}" required></div>
                            <div class="col-md-3 d-flex align-items-center gap-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="requires_fasting" value="1" {{ $package->requires_fasting ? 'checked' : '' }}>
                                    <label class="form-check-label small">Fasting</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="is_active" value="1" {{ $package->is_active ? 'checked' : '' }}>
                                    <label class="form-check-label small">Active</label>
                                </div>
                            </div>
                        </div>
                        <div class="edit-inclusions-{{ $package->id }}">
                            @foreach($package->inclusions as $inc)
                                <div class="inclusion-row">
                                    <input type="text" name="inclusions[]" class="form-control" value="{{ $inc->item_name }}" required>
                                    <button type="button" class="btn-remove-inclusion" onclick="this.parentElement.remove()">&times;</button>
                                </div>
                            @endforeach
                        </div>
                        <button type="button" class="btn-add-inclusion mb-2" onclick="addInclusionRow('.edit-inclusions-{{ $package->id }}')">+ Add Item</button>
                        <br>
                        <button type="submit" class="btn btn-sm btn-info fw-bold text-dark px-3">Update</button>
                    </form>
                </div>
            </div>
        @empty
            <p class="text-muted">No promo packages yet.</p>
        @endforelse
    </div>
</div>

<script>
    function addInclusionRow(containerSelector = '#inclusions-container') {
        const container = document.querySelector(containerSelector);
        const row = document.createElement('div');
        row.className = 'inclusion-row';
        row.innerHTML = `<input type="text" name="inclusions[]" class="form-control" placeholder="Item name" required>
                          <button type="button" class="btn-remove-inclusion" onclick="this.parentElement.remove()">&times;</button>`;
        container.appendChild(row);
    }
</script>
@endsection