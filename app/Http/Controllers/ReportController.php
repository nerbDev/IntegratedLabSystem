<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\AppointmentResult;
use App\Models\Useraccount;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class ReportController extends Controller
{
    /**
     * Which period types are supported and how many cards to build for each.
     */
    protected $periodConfig = [
        'daily'    => ['count' => 7, 'label' => 'Daily'],
        'weekly'   => ['count' => 6, 'label' => 'Weekly'],
        'monthly'  => ['count' => 6, 'label' => 'Monthly'],
        'annually' => ['count' => 5, 'label' => 'Annual'],
    ];

    /**
     * App's local timezone for calendar-day/week/month/year boundaries.
     * Used anywhere we need to know what "today" means for Manila-based staff,
     * regardless of what config('app.timezone') is set to (likely UTC).
     */
    protected $localTz = 'Asia/Manila';

    public function index(Request $request)
    {
        $filter = $request->query('filter', 'weekly');

        if (!array_key_exists($filter, $this->periodConfig)) {
            $filter = 'weekly';
        }

        $periods = $this->buildPeriods($filter);

        return view('systemreports', [
            'periods'     => $periods,
            'filter'      => $filter,
            'filterLabel' => $this->periodConfig[$filter]['label'],
        ]);
    }

    /**
     * AJAX endpoint: return the stats for ONE period only, fetched when
     * admin clicks "Generate Report" on a card.
     */
    public function generate(Request $request)
    {
        $request->validate([
            'start' => 'required|date',
            'end'   => 'required|date',
        ]);

        $startDate = $request->query('start');
        $endDate   = $request->query('end');

        // For DATE-only columns (e.g. appointment_date) — plain calendar-day
        // comparison, no timezone conversion needed since there's no time component.
        $start = Carbon::parse($startDate)->startOfDay();
        $end   = Carbon::parse($endDate)->endOfDay();

        // For DATETIME columns stored in UTC (e.g. created_at) — convert the
        // intended Manila calendar-day boundaries into their UTC equivalents,
        // so records near the start/end of the day aren't clipped off.
        $startUtc = Carbon::parse($startDate, $this->localTz)->startOfDay()->setTimezone('UTC');
        $endUtc   = Carbon::parse($endDate, $this->localTz)->endOfDay()->setTimezone('UTC');

        return response()->json($this->buildStats($start, $end, $startUtc, $endUtc));
    }

    /**
     * Build the list of period "cards" (date ranges + labels) for the given
     * filter type. Stats are NOT computed here — only date boundaries and
     * display labels, so the page loads fast.
     */
    protected function buildPeriods(string $filter): array
    {
        $config = $this->periodConfig[$filter];
        $count  = $config['count'];
        // Use Manila's calendar day as "today", not the server/app timezone's.
        $today  = Carbon::now($this->localTz)->startOfDay();
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

                case 'annually':
                default:
                    $year = $today->copy()->subYears($i);
                    $start = $year->copy()->startOfYear();
                    $end   = $year->copy()->endOfYear();
                    $label = $year->format('Y');
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
     *
     * $start/$end        -> Manila calendar-day boundaries, used for DATE-only
     *                        columns like appointment_date (no conversion needed).
     * $startUtc/$endUtc  -> the same Manila calendar-day boundaries converted to
     *                        UTC, used for real TIMESTAMP columns like created_at
     *                        (which Laravel stores in UTC by default).
     */
    protected function buildStats(Carbon $start, Carbon $end, Carbon $startUtc, Carbon $endUtc): array
    {
        $appointmentsQuery = Appointment::whereBetween('appointment_date', [
            $start->toDateString(), $end->toDateString(),
        ]);

        $total    = (clone $appointmentsQuery)->count();
        // Cumulative: once an appointment is approved, it stays counted here
        // even after it later moves to 'completed' or 'released' — this tile
        // tracks "how many were approved this period", not "how many are
        // currently sitting in Approved status". Add 'rescheduled' to this
        // list too if a rescheduled appointment should also count as approved.
        $approved  = (clone $appointmentsQuery)->whereIn('status', ['approved', 'completed', 'released'])->count();
        $pending   = (clone $appointmentsQuery)->where('status', 'pending')->count();
        $cancelled = (clone $appointmentsQuery)->where('status', 'cancelled')->count();
        $completed = (clone $appointmentsQuery)->where('status', 'completed')->count();

        // Adjust these two values to match however appointment_type is
        // actually stored (e.g. 'home' / 'clinic', 'Home Service' / 'Clinic Visit').
        $home   = (clone $appointmentsQuery)->where('appointment_type', 'home')->count();
        $clinic = (clone $appointmentsQuery)->where('appointment_type', 'clinic')->count();

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

        // created_at is a real timestamp stored in UTC -> use the UTC-converted
        // boundaries here, not the plain Manila-day ones.
        $newPatients = Useraccount::where('role', 'patient')
            ->whereBetween('created_at', [$startUtc, $endUtc])
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