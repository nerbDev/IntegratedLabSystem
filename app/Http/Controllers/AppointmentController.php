<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Appointment; 
use App\Models\Package;
use App\Models\Service;
use App\Models\UnavailableDate;
use App\Services\ActivityLogger;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Models\ActivityLog;
use App\Models\UserAccount;

class AppointmentController extends Controller
{
        /**
     * Patient: Cancel their own appointment while it's still pending
     */
    public function patientCancel(Request $request, $id)
    {
        $appointment = Appointment::where('id', $id)
                        ->where('patient_id', Auth::id())
                        ->firstOrFail();

        if ($appointment->status !== 'pending') {
            abort(403, 'This appointment can no longer be cancelled directly.');
        }

        $oldData = $appointment->only(['status']);
        $appointment->update(['status' => 'cancelled']);

        ActivityLogger::log(
            module: 'Appointments',
            action: 'Update',
            description: "Patient cancelled their own appointment #{$appointment->id} ({$appointment->service})",
            old: $oldData,
            new: $appointment->only(['status']),
            reference_id: $appointment->id
        );

        return redirect()->back()->with('success', 'Your appointment request has been cancelled.');
    }

    /**
     * Patient: Accept or reject a rescheduled appointment
     */
    public function patientRespondReschedule(Request $request, $id)
    {
        $request->validate([
            'decision' => 'required|in:accept,reject',
        ]);

        $appointment = Appointment::where('id', $id)
                        ->where('patient_id', Auth::id())
                        ->firstOrFail();

        if ($appointment->status !== 'rescheduled') {
            abort(403, 'This appointment is not awaiting a reschedule response.');
        }

        $oldData = $appointment->only(['status']);
        $newStatus = $request->decision === 'accept' ? 'approved' : 'cancelled';
        $appointment->update(['status' => $newStatus]);

        ActivityLogger::log(
            module: 'Appointments',
            action: 'Update',
            description: 'Patient ' . ($request->decision === 'accept' ? 'accepted' : 'rejected')
                        . " the rescheduled date for appointment #{$appointment->id}",
            old: $oldData,
            new: $appointment->only(['status']),
            reference_id: $appointment->id
        );

        $message = $request->decision === 'accept'
            ? 'You have accepted the new schedule.'
            : 'You have rejected the rescheduled appointment and it has been cancelled.';

        return redirect()->back()->with('success', $message);
    }
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
     * Public: Feeds the patient booking form with live packages, individual
     * services, and staff-blocked dates. Replaces what used to be hardcoded
     * JS objects (packagePrices / allAvailableServices) in the blade.
     */
    public function bookingData()
    {
        return response()->json([
            'packages' => Package::where('is_active', true)
                ->with('inclusions')
                ->orderBy('name')
                ->get()
                ->map(fn ($p) => [
                    'id' => $p->id,
                    'name' => $p->name,
                    'price' => (float) $p->price,
                    'requires_fasting' => $p->requires_fasting,
                    'inclusions' => $p->inclusions->pluck('item_name'),
                ]),
            'services' => Service::where('is_active', true)
                ->orderBy('name')
                ->get(['id', 'name', 'price']),
            'unavailable_dates' => UnavailableDate::pluck('date')
                ->map(fn ($d) => $d->format('Y-m-d')),
        ]);
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

        // Guard against booking a staff-blocked date (belt-and-suspenders —
        // the JS already prevents this in fetchSlots(), but that's client-side only)
        $isBlocked = UnavailableDate::where('date', $request->appointment_date)->exists();
        if ($isBlocked) {
            return redirect()->back()
                ->withInput()
                ->withErrors(['appointment_date' => 'This date is unavailable for booking. Please choose another day.']);
        }

        $formattedTime = Carbon::parse($request->appointment_time)->format('H:i:s');

        $appointment = Appointment::create([
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

        // Log the booking
        ActivityLogger::log(
            module: 'Appointments',
            action: 'Create',
            description: "Patient booked appointment #{$appointment->id} ({$appointment->service}) for {$appointment->first_name} {$appointment->last_name}",
            new: $appointment->toArray(),
            reference_id: $appointment->id   // ✅ matches $referenceId in the method signature
        );

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
        $oldStatus = $appointment->status;
        $oldData = $appointment->only(['appointment_date', 'appointment_time', 'status', 'notes']);

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

        // Log the status/detail change
        ActivityLogger::log(
            module: 'Appointments',
            action: 'Update',
            description: "{$this->actorRole()} changed appointment #{$appointment->id} ({$appointment->first_name} {$appointment->last_name}) status from {$oldStatus} to {$request->status}",
            old: $oldData,
            new: $appointment->only(['appointment_date', 'appointment_time', 'status', 'notes']),
            reference_id: $appointment->id
        );

        // Notify patient only when status actually changed to one of these three
        if ($oldStatus !== $request->status && in_array($request->status, ['approved', 'cancelled', 'rescheduled'])) {
            $appointment->user->notify(new \App\Notifications\AppointmentStatusUpdated($appointment));
        }

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

        public function timeline($id)
    {
        $appointment = Appointment::with('user')->findOrFail($id);

        $logs = ActivityLog::where('reference_id', $id)
                    ->orderBy('created_at', 'asc')
                    ->get();

        return view('activity-log.timeline', compact('appointment', 'logs'));
    }
    
// for the staff to see the list of "approved" schedules
    public function approvedSchedule()
    {
        $approvedAppointments = Appointment::where('status', 'approved')
            ->orderBy('appointment_date')
            ->orderBy('appointment_time')
            ->get();

        return view('SSappointmentschedule', compact('approvedAppointments'));
    }

    /**
     * Small helper to describe who performed the action in log messages
     * (Staff approving vs Admin completing use the same update() method)
     */
    private function actorRole()
    {
        $role = Auth::user()->role ?? 'User';
        return ucfirst($role);
    }
}