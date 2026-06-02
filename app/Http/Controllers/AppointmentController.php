<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Appointment; 
use App\Models\UserAccount; 
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class AppointmentController extends Controller
{
    /**
     * Fetch taken slots for a specific date (AJAX)
     */
    public function getAvailableSlots(Request $request)
    {
        $date = $request->query('date');
        
        $takenSlots = Appointment::where('appointment_date', $date)
                        ->where('status', '!=', 'cancelled')
                        ->pluck('appointment_time')
                        ->map(function($time) {
                            return Carbon::parse($time)->format('H:i');
                        })
                        ->toArray();

        return response()->json(['taken' => $takenSlots]);
    }

    /**
     * Patient: Store a new appointment request
     */
    public function store(Request $request)
    {
        $request->validate([
            'service'          => 'required|string',
            'appointment_type'     => 'required|string',
            'appointment_date' => 'required|date|after_or_equal:today',
            'appointment_time' => 'required',
            'first_name'       => 'required|string|max:255',
            'middle_name'      => 'nullable|string|max:255',
            'last_name'        => 'required|string|max:255',
            'suffix'           => 'nullable|string|max:50',
            'email'            => 'required|email',
            'phone'            => 'required|string',
            'municipality'     => 'required|string',
            'barangay'         => 'required|string',
            'street_details'   => 'required|string',
            'landmark'         => 'required|string',
        ]);

        $formattedTime = Carbon::parse($request->appointment_time)->format('H:i:s');

        Appointment::create([
            'patient_id'       => Auth::id(), 
            'service'          => $request->service, 
            'appointment_type' => $request->appointment_type,
            'appointment_date' => $request->appointment_date,
            'appointment_time' => $formattedTime,
            'first_name'       => $request->first_name,
            'middle_name'      => $request->middle_name,
            'last_name'        => $request->last_name,
            'suffix'           => $request->suffix,
            'email'            => $request->email,
            'phone'            => $request->phone,
            'municipality'     => $request->municipality,
            'barangay'         => $request->barangay,
            'street_details'   => $request->street_details,
            'landmark'         => $request->landmark,
            'status'           => 'pending',
        ]);

        return redirect()->back()->with('success', 'Appointment booked successfully!');
    }

    /**
     * Page 1: The Inbox (List of all pending)
     */
    public function showRequests() 
    {
        $appointments = Appointment::where('status', 'pending')
                        ->with('user')
                        ->orderBy('created_at', 'desc')
                        ->get();

        return view('appointmentrequests', compact('appointments'));
    }

    /**
     * Page 2: The Management Panel (Editor for a single request)
     */
    public function manageSingle($id)
    {
        $appointment = Appointment::with('user')->findOrFail($id);
        return view('manageappointment', compact('appointment'));
    }

    public function patientIndex()
    {
        // Modified to exclude completed/released statuses and eager-load results for file placement links
        $appointments = Appointment::with('result')
                                   ->where('patient_id', Auth::id())
                                   ->whereNotIn('status', ['completed', 'released'])
                                   ->orderBy('appointment_date', 'desc')
                                   ->get();

        return view('PSpendingrequests', compact('appointments'));
    }

    /**
     * Staff/Admin: Update logic
     */
    public function update(Request $request, $id)
    {
        $appointment = Appointment::findOrFail($id);

        // Date and Time are marked 'sometimes' so the Modal (which doesn't have them) doesn't fail validation
        $request->validate([
            'appointment_date' => 'sometimes|required|date',
            'appointment_time' => 'sometimes|required',
            'status'           => 'required|in:pending,approved,rescheduled,cancelled,completed,released',
            'notes'            => 'nullable|string|max:1000',
        ]);

        // Retain existing values if the request fields are missing (typical for the modal update)
        $date = $request->has('appointment_date') ? $request->appointment_date : $appointment->appointment_date;
        
        $time = $request->has('appointment_time') 
                ? Carbon::parse($request->appointment_time)->format('H:i:s') 
                : $appointment->appointment_time;

        $appointment->update([
            'appointment_date' => $date,
            'appointment_time' => $time,
            'status'           => $request->status,
            'notes'            => $request->notes,
        ]);

        // OBJECTIVE: If status is 'completed', transfer the admin to the results/upload blade
        if ($request->status === 'completed') {
            return redirect()->route('admin.uploadResults')
                             ->with('success', 'Appointment for ' . $appointment->first_name . ' moved to Lab Processing.');
        }

        return redirect()->back()->with('success', 'Appointment for ' . $appointment->first_name . ' has been updated.');
    }

    /**
     * Admin Side: Separated view
     */
    public function adminIndex()
    {
        // 1. Management List: Show ONLY Approved and Rescheduled (No Pending)
        $activeAppointments = Appointment::whereIn('status', ['approved', 'rescheduled'])
                                ->with('user')
                                ->orderBy('appointment_date', 'desc')
                                ->get();

        // 2. Results List: Show ONLY Completed and Released
        $completedAppointments = Appointment::whereIn('status', ['completed', 'released'])
                                    ->with('user')
                                    ->orderBy('updated_at', 'desc')
                                    ->get();

        // Return the Admin version with the two separate lists
        return view('ASpendingrequests', compact('activeAppointments', 'completedAppointments'));
    }
}