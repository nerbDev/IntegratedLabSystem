<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\ArchivedAppointment;
use App\Services\AppointmentArchiver;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class ArchiveController extends Controller
{
    public function index(Request $request)
    {
        $archives = ArchivedAppointment::with('result')
            ->when($request->search, fn($q) => $q->where(function($sub) use ($request) {
                $sub->where('first_name', 'like', '%'.$request->search.'%')
                    ->orWhere('last_name', 'like', '%'.$request->search.'%');
            }))
            ->when($request->date_from, fn($q) => $q->whereDate('archived_at', '>=', $request->date_from))
            ->when($request->date_to, fn($q) => $q->whereDate('archived_at', '<=', $request->date_to))
            ->orderBy('archived_at', 'desc')
            ->paginate(20)
            ->withQueryString();

        return view('archive_records', compact('archives'));
    }

    public function download($id)
    {
        $archive = ArchivedAppointment::with('result')->findOrFail($id);

        if (!$archive->result || !$archive->result->file_path) {
            return redirect()->back()->with('error', 'Archived result file not found.');
        }

        if (!Storage::disk('public')->exists($archive->result->file_path)) {
            return redirect()->back()->with('error', 'File no longer exists on the server.');
        }

        return Storage::disk('public')->download(
            $archive->result->file_path,
            'Archived_Lab_Result_' . $archive->first_name . '_' . $archive->last_name . '.pdf'
        );
    }

    /**
     * Restore an archived appointment back into the active appointments table.
     *
     * NOTE: this assumes ArchivedAppointment shares the same column structure
     * as Appointment (minus the archive-specific columns like archived_at).
     * Adjust the field list below if your archived_appointments table differs.
     */
    public function restore($id)
    {
        $archive = ArchivedAppointment::findOrFail($id);

        DB::transaction(function () use ($archive) {
            $appointment = Appointment::create([
                'patient_id'       => $archive->patient_id,
                'service'          => $archive->service,
                'appointment_type' => $archive->appointment_type,
                'appointment_date' => $archive->appointment_date,
                'appointment_time' => $archive->appointment_time,
                'first_name'       => $archive->first_name,
                'middle_name'      => $archive->middle_name,
                'last_name'        => $archive->last_name,
                'suffix'           => $archive->suffix,
                'email'            => $archive->email,
                'phone'            => $archive->phone,
                'municipality'     => $archive->municipality,
                'barangay'         => $archive->barangay,
                'street_details'   => $archive->street_details,
                'landmark'         => $archive->landmark,
                'status'           => $archive->status,
                'notes'            => $archive->notes ?? null,
            ]);

            $archive->delete();
        });

        return redirect()->back()->with('success', 'Appointment restored successfully.');
    }

    /**
     * Manually archive a single appointment right now, instead of waiting
     * for the 7-day auto-archive command to pick it up. Uses the same
     * AppointmentArchiver service as the scheduled job, so the resulting
     * archive record is identical either way.
     *
     * Restricted to 'released' appointments to match what the auto-archive
     * job would eventually archive on its own — adjust the status check
     * below if instant archiving should also be allowed from other statuses.
     */
    public function archiveNow($id)
    {
        $appointment = Appointment::with('result')->findOrFail($id);

        if ($appointment->status !== 'released') {
            return redirect()->back()->with('error', 'Only released appointments can be archived.');
        }

        $description = "Admin manually archived appointment #{$appointment->id} ({$appointment->first_name} {$appointment->last_name}) before the 7-day auto-archive window";

        AppointmentArchiver::archive($appointment, $description);

        return redirect()->back()->with('success', 'Appointment archived successfully.');
    }
}