<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\AppointmentResult;
use App\Models\UserAccount;
use App\Models\ActivityLog;
use Illuminate\Support\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        // ---------- KPI CARDS ----------
        $totalPatients = UserAccount::where('role', 'patient')->count();

        $pendingAppointments = Appointment::where('status', 'pending')->count();

        // Lab results "pending" = appointment marked completed/proceed-to-lab
        // but no result uploaded yet. Adjust the status strings below if yours differ.
        $pendingLabResults = Appointment::whereIn('status', ['completed', 'proceed_to_lab'])
            ->whereDoesntHave('result')
            ->count();

        $releasedLabResults = Appointment::where('status', 'released')->count();

        // ---------- APPOINTMENT STATUS BREAKDOWN (doughnut) ----------
        $statusCounts = Appointment::selectRaw('status, COUNT(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        $appointmentStatusLabels = $statusCounts->keys();
        $appointmentStatusData = $statusCounts->values();

        // ---------- LAB RESULTS: PENDING VS RELEASED (doughnut) ----------
        $labResultLabels = ['Pending', 'Released'];
        $labResultData = [$pendingLabResults, $releasedLabResults];

        // ---------- PATIENT GROWTH (line, last 6 months) ----------
        $months = collect(range(5, 0))->map(function ($i) {
            return Carbon::now()->subMonths($i)->format('Y-m');
        });

        $patientsByMonth = UserAccount::where('role', 'patient')
            ->where('created_at', '>=', Carbon::now()->subMonths(5)->startOfMonth())
            ->selectRaw("DATE_FORMAT(created_at, '%Y-%m') as ym, COUNT(*) as total")
            ->groupBy('ym')
            ->pluck('total', 'ym');

        $patientGrowthLabels = $months->map(fn ($m) => Carbon::createFromFormat('Y-m', $m)->format('M Y'));
        $patientGrowthData = $months->map(fn ($m) => $patientsByMonth[$m] ?? 0);

        // ---------- PATIENTS BY AREA (bar) ----------
        $patientsByArea = UserAccount::where('role', 'patient')
            ->whereNotNull('Umunicipality')
            ->selectRaw('Umunicipality as municipality, COUNT(*) as total')
            ->groupBy('Umunicipality')
            ->orderByDesc('total')
            ->limit(8)
            ->get();

        // ---------- RECENT ACTIVITY FEED ----------
        $recentActivity = ActivityLog::orderByDesc('created_at')->limit(8)->get();

        return view('admindashboard', compact(
            'totalPatients',
            'pendingAppointments',
            'pendingLabResults',
            'releasedLabResults',
            'appointmentStatusLabels',
            'appointmentStatusData',
            'labResultLabels',
            'labResultData',
            'patientGrowthLabels',
            'patientGrowthData',
            'patientsByArea',
            'recentActivity'
        ));
    }
}