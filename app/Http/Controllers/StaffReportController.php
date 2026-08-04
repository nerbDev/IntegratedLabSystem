<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\AppointmentResult;
use App\Models\Useraccount;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class StaffReportController extends Controller
{
    /**
     * Which period types are supported and how many cards to build for each.
     */
    protected $periodConfig = [
        'daily'     => ['count' => 7,  'label' => 'Daily'],
        'weekly'    => ['count' => 6,  'label' => 'Weekly'],
        'monthly'   => ['count' => 6,  'label' => 'Monthly'],
        'halfyear'  => ['count' => 4,  'label' => 'Half-Yearly'],
    ];

    public function index(Request $request)
    {
        $filter = $request->query('filter', 'weekly');

        if (!array_key_exists($filter, $this->periodConfig)) {
            $filter = 'weekly';
        }

        $periods = $this->buildPeriods($filter);

        return view('Staff_SReports', [
            'periods'    => $periods,
            'filter'     => $filter,
            'filterLabel'=> $this->periodConfig[$filter]['label'],
        ]);
    }

    /**
     * AJAX endpoint: return the stats for ONE period only (lazy-load, like
     * the admin "Generate Report" button). Avoids computing every period's
     * stats on every page load.
     */
    public function generate(Request $request)
    {
        $request->validate([
            'start' => 'required|date',
            'end'   => 'required|date',
        ]);

        $start = Carbon::parse($request->query('start'))->startOfDay();
        $end   = Carbon::parse($request->query('end'))->endOfDay();

        return response()->json($this->buildStats($start, $end));
    }

    /**
     * Build the list of period "cards" (date ranges + labels) for the given
     * filter type. Stats are NOT computed here — only date boundaries and
     * display labels, so the page loads fast. Stats are fetched on demand
     * via the generate() endpoint when staff click "Generate Report".
     */
    protected function buildPeriods(string $filter): array
    {
        $config = $this->periodConfig[$filter];
        $count  = $config['count'];
        $today  = Carbon::today();
        $periods = [];

        for ($i = 0; $i < $count; $i++) {
            switch ($filter) {

                case 'daily':
                    $day = $today->copy()->subDays($i);
                    $start = $day->copy()->startOfDay();
                    $end   = $day->copy()->endOfDay();
                    $label = $i === 0 ? 'Today' : ($i === 1 ? 'Yesterday' : $day->format('D, M j'));
                    $isCurrent = $i === 0;
                    break;

                case 'weekly':
                    $start = $today->copy()->subWeeks($i)->startOfWeek(Carbon::MONDAY);
                    $end   = $start->copy()->endOfWeek(Carbon::SUNDAY);
                    $label = 'Week ' . $start->format('W');
                    $isCurrent = $i === 0;
                    break;

                case 'monthly':
                    $month = $today->copy()->subMonths($i);
                    $start = $month->copy()->startOfMonth();
                    $end   = $month->copy()->endOfMonth();
                    $label = $month->format('F Y');
                    $isCurrent = $i === 0;
                    break;

                case 'halfyear':
                default:
                    // Each "half" is 6 months back from today, in 6-month blocks.
                    $end   = $today->copy()->subMonths($i * 6)->endOfMonth();
                    $start = $end->copy()->subMonths(5)->startOfMonth();
                    $half  = $end->month <= 6 ? 'H1' : 'H2';
                    $label = $half . ' ' . $end->year;
                    $isCurrent = $i === 0;
                    break;
            }

            $periods[] = [
                'label'      => $label,
                'start'      => $start->toDateString(),
                'end'        => $end->toDateString(),
                'start_disp' => $start->format('M j, Y'),
                'end_disp'   => $end->format('M j, Y'),
                'is_current' => $isCurrent,
            ];
        }

        return $periods;
    }

    /**
     * Compute the actual report stats for a single date range.
     * Mirrors the shape used by the admin System Reports view
     * (appointments / lab_results / patients).
     */
    protected function buildStats(Carbon $start, Carbon $end): array
    {
        $appointmentsQuery = Appointment::whereBetween('appointment_date', [
            $start->toDateString(), $end->toDateString(),
        ]);

        $total      = (clone $appointmentsQuery)->count();
        $approved   = (clone $appointmentsQuery)->where('status', 'approved')->count();
        $pending    = (clone $appointmentsQuery)->where('status', 'pending')->count();
        $cancelled  = (clone $appointmentsQuery)->where('status', 'cancelled')->count();
        $completed  = (clone $appointmentsQuery)->where('status', 'completed')->count();

        // Adjust these two values to match however appointment_type is
        // actually stored (e.g. 'home' / 'clinic', 'Home Service' / 'Clinic Visit').
        $home   = (clone $appointmentsQuery)->where('appointment_type', 'home')->count();
        $clinic = (clone $appointmentsQuery)->where('appointment_type', 'clinic')->count();

        // Lab results: an appointment counts as "processed" once it has at
        // least one row in appointment_results.
        $appointmentIdsInRange = (clone $appointmentsQuery)->pluck('id');

        $processedCount = AppointmentResult::whereIn('appointment_id', $appointmentIdsInRange)
            ->distinct('appointment_id')
            ->count('appointment_id');

        $abnormalCount = AppointmentResult::whereIn('appointment_id', $appointmentIdsInRange)
            ->where('is_abnormal', true)
            ->distinct('appointment_id')
            ->count('appointment_id');

        $unprocessed = $approved - $processedCount;
        $unprocessed = $unprocessed > 0 ? $unprocessed : 0;

        // Patients: "new" = patient accounts created within the period.
        // "returning" = distinct patient_id in this period who also had an
        // appointment before the period started.
        $newPatients = Useraccount::where('role', 'patient')
            ->whereBetween('created_at', [$start, $end])
            ->count();

        $patientIdsInRange = (clone $appointmentsQuery)->pluck('patient_id')->unique();

        $returningPatients = Appointment::whereIn('patient_id', $patientIdsInRange)
            ->where('appointment_date', '<', $start->toDateString())
            ->distinct('patient_id')
            ->count('patient_id');

        $totalPatients = Useraccount::where('role', 'patient')->count();

        return [
            'appointments' => [
                'total'     => $total,
                'approved'  => $approved,
                'pending'   => $pending,
                'cancelled' => $cancelled,
                'completed' => $completed,
                'home'      => $home,
                'clinic'    => $clinic,
            ],
            'lab_results' => [
                'processed'   => $processedCount,
                'unprocessed' => $unprocessed,
                'abnormal'    => $abnormalCount,
            ],
            'patients' => [
                'new'       => $newPatients,
                'returning' => $returningPatients,
                'total'     => $totalPatients,
            ],
        ];
    }
}