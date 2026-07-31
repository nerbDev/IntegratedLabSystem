<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\UserAccount;
use App\Services\ActivityLogger;
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

        // Log that admin viewed this patient's record
        ActivityLogger::log(
            module: 'Patients',
            action: 'View',
            description: "Admin viewed patient record for {$patient->first_name} {$patient->last_name}",
            reference_id: $patient->id
        );

        return response()->json([
            'patient'      => $patient,
            'appointments' => $patient->appointments
        ]);
    }

    // Update complete patient information registry profile metrics
    public function update(Request $request, $id)
    {
        $patient = UserAccount::findOrFail($id);
        $oldData = $patient->only([
            'first_name', 'middle_name', 'last_name', 'date_of_birth', 'sex',
            'email', 'phone_number', 'Umunicipality', 'Ubarangay', 'Ustreet_house',
            'contact_person', 'contact_number'
        ]);

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

        // Log the update
        ActivityLogger::log(
            module: 'Patients',
            action: 'Update',
            description: "Admin updated patient record for {$patient->first_name} {$patient->last_name}",
            old: $oldData,
            new: $validated,
            reference_id: $patient->id
        );

        return redirect()->back()->with('success', 'Patient record metrics successfully customized.');
    }

    // Safely remove user account profile from clinical registry records
    public function destroy($id)
    {
        $patient = UserAccount::findOrFail($id);
        $patientData = $patient->only(['id', 'first_name', 'last_name', 'email']);

        $patient->delete();

        // Log the deletion — important since this is destructive and irreversible
        ActivityLogger::log(
            module: 'Patients',
            action: 'Delete',
            description: "Admin deleted patient account for {$patientData['first_name']} {$patientData['last_name']} (ID: {$patientData['id']})",
            old: $patientData,
            reference_id: $patientData['id']
        );

        return redirect()->back()->with('success', 'Patient account file permanently dropped from databases.');
    }
}