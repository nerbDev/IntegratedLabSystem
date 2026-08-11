<?php

namespace App\Console\Commands;

use App\Models\Appointment;
use App\Services\AppointmentArchiver;
use Illuminate\Console\Command;
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
            $description = "System auto-archived appointment #{$appointment->id} ({$appointment->first_name} {$appointment->last_name}) after 7 days in 'released' status";

            AppointmentArchiver::archive($appointment, $description);
        }

        $this->info(count($appointments) . ' appointment(s) archived.');
    }
}