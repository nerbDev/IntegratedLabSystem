<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Appointment;
use App\Models\AppointmentResult;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class AppointmentResultController extends Controller
{
    /**
     * Display the Upload Page for ALL completed/released appointments
     */
    public function showUploadForm()
    {
        $completedAppointments = Appointment::whereIn('status', ['completed', 'released'])
                                    ->with(['user', 'result'])
                                    ->orderBy('updated_at', 'desc')
                                    ->get();

        return view('uploadResults', compact('completedAppointments'));
    }

    /**
     * Upload the PDF result file and update appointment status
     */
    public function store(Request $request, $id)
    {
        $appointment = Appointment::findOrFail($id);

        // Validate only the file and status — no more 'results' array requirement
        $request->validate([
            'lab_file' => 'required|mimes:pdf|max:10240',
            'status'   => 'required|in:completed,released',
            'notes'    => 'nullable|string',
        ]);

        // Delete old file if one already exists for this appointment
        if ($appointment->result && $appointment->result->file_path) {
            Storage::disk('public')->delete($appointment->result->file_path);
        }

        // Store the uploaded PDF
        $path = $request->file('lab_file')->store('medical_records', 'public');

        // Save or update the result record
        AppointmentResult::updateOrCreate(
            ['appointment_id' => $appointment->id],
            [
                'file_path' => $path,
                'notes'     => $request->notes,
                'status'    => $request->status,
            ]
        );

        // Update appointment status
        $appointment->update([
            'status' => $request->status,
            'notes'  => $request->notes,
        ]);

        return redirect()->back()
            ->with('success', 'Lab result for ' . $appointment->first_name . ' ' . $appointment->last_name . ' uploaded successfully!');
    }

    /**
     * Patient: View their released results
     */
    public function patientResults()
    {
        $releasedAppointments = Appointment::with('result')
            ->where('patient_id', Auth::id())
            ->where('status', 'released')
            ->orderBy('appointment_date', 'desc')
            ->get();

        return view('PSresultview', compact('releasedAppointments'));
    }

    /**
     * Patient: Download/stream the PDF result
     */
    public function download($id)
    {
        $appointment = Appointment::with('result')->findOrFail($id);

        if (!$appointment->result || !$appointment->result->file_path) {
            return redirect()->back()->with('error', 'The lab result file was not found.');
        }

        $filePath = $appointment->result->file_path;

        if (!Storage::disk('public')->exists($filePath)) {
            return redirect()->back()->with('error', 'The requested file does not exist on our servers.');
        }

        $downloadName = 'Lab_Result_' . $appointment->first_name . '_' . $appointment->last_name . '.' . pathinfo($filePath, PATHINFO_EXTENSION);

        return Storage::disk('public')->download($filePath, $downloadName);
    }
}