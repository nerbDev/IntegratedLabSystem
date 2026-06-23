<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\UserAccount;
use Carbon\Carbon;

class ReportController extends Controller
{
    public function index()
    {
        $weeks = [];

        for ($i = 0; $i < 4; $i++) {
            $start = Carbon::now()->startOfWeek()->subWeeks($i);
            $end   = $start->copy()->endOfWeek();

            $appts = Appointment::whereBetween('created_at', [$start, $end]);

            $weeks[] = [
                'label'      => 'Week ' . ($i + 1),
                'start'      => $start->format('M d, Y'),
                'end'        => $end->format('M d, Y'),
                'is_current' => $i === 0,

                'appointments' => [
                    'total'     => (clone $appts)->count(),
                    'approved'  => (clone $appts)->where('status', 'approved')->count(),
                    'pending'   => (clone $appts)->where('status', 'pending')->count(),
                    'cancelled' => (clone $appts)->where('status', 'cancelled')->count(),
                    'home'      => (clone $appts)->where('appointment_type', 'Home Service')->count(),
                    'clinic'    => (clone $appts)->where('appointment_type', '!=', 'Home Service')->count(),
                ],

                // No LabResult table yet — derived from appointments
                'lab_results' => [
                    'processed'    => (clone $appts)->where('status', 'approved')->count(),
                    'unprocessed'  => (clone $appts)->where('status', 'pending')->count(),
                    'note'         => 'Results are PDF-generated. No DB storage yet.',
                ],

                'patients' => [
                    'new' => UserAccount::where('role', 'patient')
                        ->whereBetween('created_at', [$start, $end])
                        ->count(),
                    
                    'returning' => (clone $appts)
                        ->whereHas('useraccount', function ($q) use ($start) {
                            $q->where('role', 'patient')
                              ->where('created_at', '<', $start);
                        })
                        ->distinct('patient_id')
                        ->count(),
                    
                    'total' => UserAccount::where('role', 'patient')
                        ->where('created_at', '<=', $end)
                        ->count(),
                ],
            ];
        }

        return view('systemreports', compact('weeks'));
    }
}