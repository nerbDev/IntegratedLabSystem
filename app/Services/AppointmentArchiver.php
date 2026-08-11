<?php

namespace App\Services;

use App\Models\Appointment;
use App\Models\ArchivedAppointment;
use App\Models\ArchivedAppointmentResult;
use App\Services\ActivityLogger;
use Illuminate\Support\Facades\DB;

class AppointmentArchiver
{
    /**
     * Move a single appointment (and its result, if any) into the archive
     * tables and delete the originals. Shared by the scheduled 7-day
     * auto-archive command (ArchiveReleasedAppointments) and the admin's
     * manual "Archive Now" button (ArchiveController::archiveNow), so both
     * paths always produce identical archive records.
     *
     * @param  Appointment  $appointment  Must have 'result' eager-loaded if it exists.
     * @param  string       $description  Activity log description — differs
     *                                     between the auto job and manual action.
     */
    public static function archive(Appointment $appointment, string $description): ArchivedAppointment
    {
        return DB::transaction(function () use ($appointment, $description) {
            $archived = ArchivedAppointment::create([
                'original_appointment_id' => $appointment->id,
                'patient_id'              => $appointment->patient_id,
                'service'                 => $appointment->service,
                'appointment_type'        => $appointment->appointment_type,
                'appointment_date'        => $appointment->appointment_date,
                'appointment_time'        => $appointment->appointment_time,
                'first_name'              => $appointment->first_name,
                'middle_name'             => $appointment->middle_name,
                'last_name'               => $appointment->last_name,
                'suffix'                  => $appointment->suffix,
                'email'                   => $appointment->email,
                'phone'                   => $appointment->phone,
                'municipality'            => $appointment->municipality,
                'barangay'                => $appointment->barangay,
                'street_details'          => $appointment->street_details,
                'landmark'                => $appointment->landmark,
                'status'                  => $appointment->status,
                'notes'                   => $appointment->notes,
                'original_created_at'     => $appointment->created_at,
                'original_updated_at'     => $appointment->updated_at,
                'archived_at'             => now(),
            ]);

            if ($appointment->result) {
                ArchivedAppointmentResult::create([
                    'archived_appointment_id' => $archived->id,
                    'original_appointment_id' => $appointment->id,
                    'file_path'               => $appointment->result->file_path,
                    'notes'                   => $appointment->result->notes,
                    'status'                  => $appointment->result->status,
                    'original_created_at'     => $appointment->result->created_at,
                    'original_updated_at'     => $appointment->result->updated_at,
                    'archived_at'             => now(),
                ]);

                // Delete the result row (but NOT the actual file in storage)
                $appointment->result->delete();
            }

            ActivityLogger::log(
                module: 'Archive',
                action: 'Archive',
                description: $description,
                old: ['appointment_id' => $appointment->id, 'status' => $appointment->status],
                reference_id: $archived->id
            );

            $appointment->delete();

            return $archived;
        });
    }
}