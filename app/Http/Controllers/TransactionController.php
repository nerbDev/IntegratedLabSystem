<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Appointment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TransactionController extends Controller
{
    /**
     * Staff: their own logged actions only.
     */
    public function staffTransactions(Request $request)
    {
        $staffId = Auth::id();

        $query = ActivityLog::where('user_id', $staffId)
            ->orderByDesc('created_at');

        $this->applyCommonFilters($query, $request);

        $transactions = $query->paginate(20)->withQueryString();

        return view('SStransactions_table', compact('transactions'));
    }

    public function printStaffTransactions(Request $request)
    {
        $staffId = Auth::id();

        $query = ActivityLog::where('user_id', $staffId)
            ->orderByDesc('created_at');

        $this->applyCommonFilters($query, $request);

        return view('partials.transactions-print', [
            'transactions' => $query->get(),
            'title'        => 'My Transactions',
            'ownerName'    => Auth::user()->first_name . ' ' . Auth::user()->last_name,
            'ownerRole'    => 'Staff',
        ]);
    }

    /**
     * Patient: own actions + actions taken on their own appointments.
     */
    public function patientTransactions(Request $request)
    {
        $patientId = Auth::id();

        $appointmentIds = Appointment::where('patient_id', $patientId)->pluck('id');

        $query = ActivityLog::where(function ($q) use ($patientId, $appointmentIds) {
                $q->where('user_id', $patientId)
                  ->orWhere(function ($q2) use ($appointmentIds) {
                      $q2->whereIn('module', ['Appointment', 'AppointmentResult'])
                         ->whereIn('reference_id', $appointmentIds);
                  });
            })
            ->orderByDesc('created_at');

        $this->applyCommonFilters($query, $request);

        $transactions = $query->paginate(20)->withQueryString();

        return view('PStransactions_table', compact('transactions'));
    }

    public function printPatientTransactions(Request $request)
    {
        $patientId = Auth::id();

        $appointmentIds = Appointment::where('patient_id', $patientId)->pluck('id');

        $query = ActivityLog::where(function ($q) use ($patientId, $appointmentIds) {
                $q->where('user_id', $patientId)
                  ->orWhere(function ($q2) use ($appointmentIds) {
                      $q2->whereIn('module', ['Appointment', 'AppointmentResult'])
                         ->whereIn('reference_id', $appointmentIds);
                  });
            })
            ->orderByDesc('created_at');

        $this->applyCommonFilters($query, $request);

        return view('partials.transactions-print', [
            'transactions' => $query->get(),
            'title'        => 'My Transaction History',
            'ownerName'    => Auth::user()->first_name . ' ' . Auth::user()->last_name,
            'ownerRole'    => 'Patient',
        ]);
    }

    /**
     * Admin: "My Activity" - own actions only.
     */
    public function adminTransactions(Request $request)
    {
        $adminId = Auth::id();

        $query = ActivityLog::where('user_id', $adminId)
            ->orderByDesc('created_at');

        $this->applyCommonFilters($query, $request);

        $transactions = $query->paginate(20)->withQueryString();

        return view('AStransactions_table', compact('transactions'));
    }

    public function printAdminTransactions(Request $request)
    {
        $adminId = Auth::id();

        $query = ActivityLog::where('user_id', $adminId)
            ->orderByDesc('created_at');

        $this->applyCommonFilters($query, $request);

        return view('partials.transactions-print', [
            'transactions' => $query->get(),
            'title'        => 'My Transactions',
            'ownerName'    => Auth::user()->first_name . ' ' . Auth::user()->last_name,
            'ownerRole'    => 'Admin',
        ]);
    }

    private function applyCommonFilters($query, Request $request)
    {
        if ($request->filled('module')) {
            $query->where('module', $request->module);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        if ($request->filled('search')) {
            $query->where('description', 'like', '%' . $request->search . '%');
        }

        return $query;
    }
}