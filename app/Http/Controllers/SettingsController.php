<?php

namespace App\Http\Controllers;

use App\Models\Package;
use App\Models\PackageInclusion;
use App\Models\Service;
use App\Models\UnavailableDate;
use Illuminate\Http\Request;

class SettingsController extends Controller
{
    // ─────────────────────────────
    // ADD PROMO (bundled packages)
    // ─────────────────────────────

    public function promoIndex()
    {
        $packages = Package::with('inclusions')->orderBy('name')->get();
        return view('SSsettingspromo', compact('packages'));
    }

    public function promoStore(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'requires_fasting' => 'nullable|boolean',
            'inclusions' => 'required|array|min:1',
            'inclusions.*' => 'required|string|max:255',
        ]);

        $package = Package::create([
            'name' => $validated['name'],
            'price' => $validated['price'],
            'requires_fasting' => $request->boolean('requires_fasting'),
        ]);

        foreach ($validated['inclusions'] as $i => $item) {
            PackageInclusion::create([
                'package_id' => $package->id,
                'item_name' => $item,
                'sort_order' => $i,
            ]);
        }

        return back()->with('success', 'Promo package added.');
    }

    public function promoUpdate(Request $request, Package $package)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'requires_fasting' => 'nullable|boolean',
            'is_active' => 'nullable|boolean',
            'inclusions' => 'required|array|min:1',
            'inclusions.*' => 'required|string|max:255',
        ]);

        $package->update([
            'name' => $validated['name'],
            'price' => $validated['price'],
            'requires_fasting' => $request->boolean('requires_fasting'),
            'is_active' => $request->boolean('is_active'),
        ]);

        $package->inclusions()->delete();
        foreach ($validated['inclusions'] as $i => $item) {
            PackageInclusion::create([
                'package_id' => $package->id,
                'item_name' => $item,
                'sort_order' => $i,
            ]);
        }

        return back()->with('success', 'Promo package updated.');
    }

    public function promoDestroy(Package $package)
    {
        $package->delete(); // inclusions cascade via FK
        return back()->with('success', 'Promo package removed.');
    }

    // ─────────────────────────────
    // ADD PACKAGE TYPE (individual services)
    // ─────────────────────────────

    public function serviceIndex()
    {
        $services = Service::orderBy('name')->get();
        return view('SSsettingsservices', compact('services'));
    }

    public function serviceStore(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:services,name',
            'price' => 'required|numeric|min:0',
        ]);

        Service::create($validated);

        return back()->with('success', 'Service added.');
    }

    public function serviceUpdate(Request $request, Service $service)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:services,name,' . $service->id,
            'price' => 'required|numeric|min:0',
            'is_active' => 'nullable|boolean',
        ]);

        $service->update([
            'name' => $validated['name'],
            'price' => $validated['price'],
            'is_active' => $request->boolean('is_active'),
        ]);

        return back()->with('success', 'Service updated.');
    }

    public function serviceDestroy(Service $service)
    {
        $service->delete();
        return back()->with('success', 'Service removed.');
    }

    // ─────────────────────────────
    // MODIFY PRICE (quick-edit list across both)
    // ─────────────────────────────

    public function priceIndex()
    {
        $packages = Package::orderBy('name')->get();
        $services = Service::orderBy('name')->get();
        return view('SSsettingsprice', compact('packages', 'services'));
    }

    public function priceUpdatePackage(Request $request, Package $package)
    {
        $request->validate(['price' => 'required|numeric|min:0']);
        $package->update(['price' => $request->price]);
        return back()->with('success', "Price updated for {$package->name}.");
    }

    public function priceUpdateService(Request $request, Service $service)
    {
        $request->validate(['price' => 'required|numeric|min:0']);
        $service->update(['price' => $request->price]);
        return back()->with('success', "Price updated for {$service->name}.");
    }

    // ─────────────────────────────
    // BLOCK UNAVAILABLE DAYS
    // ─────────────────────────────

    public function unavailableIndex()
    {
        $blockedDates = UnavailableDate::orderBy('date')->get();
        return view('SSsettingsunavailable', compact('blockedDates'));
    }

    public function unavailableStore(Request $request)
    {
        $validated = $request->validate([
            'date' => 'required|date|after_or_equal:today|unique:unavailable_dates,date',
            'reason' => 'nullable|string|max:255',
        ]);

        UnavailableDate::create([
            'date' => $validated['date'],
            'reason' => $validated['reason'] ?? null,
            'created_by' => auth()->id(),
        ]);

        return back()->with('success', 'Date blocked.');
    }

    public function unavailableDestroy(UnavailableDate $unavailableDate)
    {
        $unavailableDate->delete();
        return back()->with('success', 'Date unblocked.');
    }
}