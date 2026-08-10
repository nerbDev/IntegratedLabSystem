<?php

namespace App\Http\Controllers;

use App\Models\UserAccount;
use App\Services\ActivityLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class StaffProfileController extends Controller
{
    /**
     * Show the logged-in staff's own profile
     */
    public function show()
    {
        $staff = Auth::user();

        return view('staff-profile.show', compact('staff'));
    }

    /**
     * Update profile details (not password)
     */
    public function update(Request $request)
    {
        $staff = Auth::user();
        $oldData = $staff->only([
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
            'email'          => 'required|email|unique:useraccount,email,' . $staff->id,
            'phone_number'   => 'required|string|max:20',
            'Umunicipality'  => 'required|string|max:255',
            'Ubarangay'      => 'required|string|max:255',
            'Ustreet_house'  => 'required|string|max:255',
            'contact_person' => 'required|string|max:255',
            'contact_number' => 'required|string|max:20',
        ]);

        $staff->update($validated);

        // Log the profile update
        ActivityLogger::log(
            module: 'Staff Profile',
            action: 'Update',
            description: "Staff {$staff->first_name} {$staff->last_name} updated their own profile",
            old: $oldData,
            new: $validated,
            reference_id: $staff->id
        );

        return redirect()->back()->with('success', 'Profile updated successfully.');
    }

    /**
     * Update password separately
     */
    public function updatePassword(Request $request)
    {
        $staff = Auth::user();

        $request->validate([
            'current_password' => 'required',
            'new_password'      => 'required|min:8|confirmed',
        ]);

        if (!Hash::check($request->current_password, $staff->password)) {
            return redirect()->back()->with('error', 'Current password is incorrect.');
        }

        $staff->update([
            'password' => Hash::make($request->new_password),
        ]);

        // Log the password change (no old/new values, since we never store plaintext passwords)
        ActivityLogger::log(
            module: 'Staff Profile',
            action: 'Password Change',
            description: "Staff {$staff->first_name} {$staff->last_name} changed their password",
            reference_id: $staff->id
        );

        return redirect()->back()->with('success', 'Password updated successfully.');
    }
}