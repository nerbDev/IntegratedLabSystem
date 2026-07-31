<?php

namespace App\Http\Controllers;

use App\Models\UserAccount;
use App\Services\ActivityLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AdminProfileController extends Controller
{
    /**
     * Show the logged-in admin's own profile
     */
    public function show()
    {
        $admin = Auth::user();

        return view('admin-profile.show', compact('admin'));
    }

    /**
     * Update profile details (not password)
     */
    public function update(Request $request)
    {
        $admin = Auth::user();
        $oldData = $admin->only([
            'first_name', 'middle_name', 'last_name', 'date_of_birth', 'sex',
            'Umunicipality', 'Ubarangay', 'Ustreet_house',
            'phone_number', 'email', 'contact_person', 'contact_number'
        ]);

        $validated = $request->validate([
            'first_name'     => 'required|string|max:255',
            'middle_name'    => 'nullable|string|max:255',
            'last_name'      => 'required|string|max:255',
            'date_of_birth'  => 'required|date',
            'sex'            => 'required|in:male,female',
            'email'          => 'required|email|unique:useraccount,email,' . $admin->id,
            'phone_number'   => 'required|string|max:20',
            'Umunicipality'  => 'required|string|max:255',
            'Ubarangay'      => 'required|string|max:255',
            'Ustreet_house'  => 'required|string|max:255',
            'contact_person' => 'required|string|max:255',
            'contact_number' => 'required|string|max:20',
        ]);

        $admin->update($validated);

        // Log the profile update
        ActivityLogger::log(
            module: 'Admin Profile',
            action: 'Update',
            description: "Admin {$admin->first_name} {$admin->last_name} updated their own profile",
            old: $oldData,
            new: $validated,
            referenceId: $admin->id
        );

        return redirect()->back()->with('success', 'Profile updated successfully.');
    }

    /**
     * Update password separately
     */
    public function updatePassword(Request $request)
    {
        $admin = Auth::user();

        $request->validate([
            'current_password' => 'required',
            'new_password'      => 'required|min:8|confirmed',
        ]);

        if (!Hash::check($request->current_password, $admin->password)) {
            return redirect()->back()->with('error', 'Current password is incorrect.');
        }

        $admin->update([
            'password' => Hash::make($request->new_password),
        ]);

        // Log the password change (no old/new values, since we never store plaintext passwords)
        ActivityLogger::log(
            module: 'Admin Profile',
            action: 'Password Change',
            description: "Admin {$admin->first_name} {$admin->last_name} changed their password",
            referenceId: $admin->id
        );

        return redirect()->back()->with('success', 'Password updated successfully.');
    }
}