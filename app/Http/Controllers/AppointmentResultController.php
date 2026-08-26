<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Appointment;
use App\Models\AppointmentResult;
use App\Services\ActivityLogger;
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

        $oldStatus = $appointment->status;
        $hadPreviousFile = $appointment->result && $appointment->result->file_path;

        // Delete old file if one already exists for this appointment
        if ($hadPreviousFile) {
            Storage::disk('supabase')->delete($appointment->result->file_path);
        }

        // Store the uploaded PDF
        $path = $request->file('lab_file')->store('medical_records', 'supabase');

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

        // Notify patient only when the result is actually released (not just marked completed)
        if ($request->status === 'released') {
            $appointment->user->notify(new \App\Notifications\LabResultReleased($appointment));
        }

        // Log the upload
        ActivityLogger::log(
            module: 'Lab Results',
            action: $hadPreviousFile ? 'Re-upload' : 'Upload',
            description: "Admin uploaded lab result for {$appointment->first_name} {$appointment->last_name}'s appointment #{$appointment->id}",
            old: ['status' => $oldStatus, 'file_path' => $hadPreviousFile ? $appointment->result->file_path : null],
            new: ['status' => $request->status, 'file_path' => $path],
            reference_id: $appointment->id
        );

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

        // Log that the patient viewed their results list
        ActivityLogger::log(
            module: 'Lab Results',
            action: 'View',
            description: 'Patient viewed their released lab results list'
        );

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

        if (!Storage::disk('supabase')->exists($filePath)) {
            return redirect()->back()->with('error', 'The requested file does not exist on our servers.');
        }

        $downloadName = 'Lab_Result_' . $appointment->first_name . '_' . $appointment->last_name . '.' . pathinfo($filePath, PATHINFO_EXTENSION);

        // Log the download
        ActivityLogger::log(
            module: 'Lab Results',
            action: 'Download',
            description: "Patient downloaded lab result file for Appointment #{$appointment->id}",
            reference_id: $appointment->id
        );

        $url = Storage::disk('supabase')->temporaryUrl(
            $filePath,
            now()->addMinutes(5),
            ['ResponseContentDisposition' => 'attachment; filename="' . $downloadName . '"']
        );

        return redirect($url);
    }
}