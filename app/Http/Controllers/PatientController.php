<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\UserAccount;
use Illuminate\Support\Facades\Auth;

class PatientController extends Controller
{
    // Render list of accounts filtered exclusively to 'patient' role profiles
    public function index()
    {
        // Fetch only accounts with the role of patient
        $patients = UserAccount::where('role', 'patient')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('ASpatientdetails', compact('patients'));
    }

    // Dynamic relational view to pull comprehensive logs of specific patients
    public function show($id)
    {
        // Fetch specific user along with their dynamic appointments history data array
        $patient = UserAccount::with(['appointments' => function ($query) {
            $query->orderBy('appointment_date', 'desc')
                  ->orderBy('appointment_time', 'desc');
        }])->findOrFail($id);

        return response()->json([
            'patient'      => $patient,
            'appointments' => $patient->appointments
        ]);
    }

    // Update complete patient information registry profile metrics
    public function update(Request $request, $id)
    {
        $patient = UserAccount::findOrFail($id);

        $validated = $request->validate([
            'first_name'     => 'required|string|max:255',
            'middle_name'    => 'nullable|string|max:255',
            'last_name'      => 'required|string|max:255',
            'date_of_birth'  => 'required|date',
            'sex'            => 'required|in:male,female',
            'email'          => 'required|email|unique:useraccount,email,' . $id,
            'phone_number'   => 'required|string|max:20',
            'Umunicipality'  => 'required|string|max:255',
            'Ubarangay'      => 'required|string|max:255',
            'Ustreet_house'  => 'required|string|max:255',
            'contact_person' => 'required|string|max:255',
            'contact_number' => 'required|string|max:20',
        ]);

        $patient->update($validated);

        return redirect()->back()->with('success', 'Patient record metrics successfully customized.');
    }

    // Safely remove user account profile from clinical registry records
    public function destroy($id)
    {
        $patient = UserAccount::findOrFail($id);
        $patient->delete();

        return redirect()->back()->with('success', 'Patient account file permanently dropped from databases.');
    }
}