<?php

namespace App\Console\Commands;

use App\Models\Appointment;
use App\Models\ArchivedAppointment;
use App\Models\ArchivedAppointmentResult;
use App\Services\ActivityLogger;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class ArchiveReleasedAppointments extends Command
{
    protected $signature = 'appointments:archive';
    protected $description = 'Move released appointments older than 7 days into the archive tables';

    public function handle()
    {
        $cutoff = Carbon::now()->subDays(7);

        $appointments = Appointment::with('result')
            ->where('status', 'released')
            ->where('updated_at', '<=', $cutoff)
            ->get();

        if ($appointments->isEmpty()) {
            $this->info('No appointments to archive.');
            return;
        }

        foreach ($appointments as $appointment) {
            DB::transaction(function () use ($appointment) {
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
                    description: "System auto-archived appointment #{$appointment->id} ({$appointment->first_name} {$appointment->last_name}) after 7 days in 'released' status",
                    old: ['appointment_id' => $appointment->id, 'status' => $appointment->status],
                    referenceId: $archived->id
                );

                $appointment->delete();
            });
        }

        $this->info(count($appointments) . ' appointment(s) archived.');
    }
}