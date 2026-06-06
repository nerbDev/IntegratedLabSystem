<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use Illuminate\Http\Request;

class LabResultController extends Controller
{
    /**
     * The list of available service/test types.
     * Add or remove entries here to automatically update
     * both the dropdown and the JS template map.
     */
    protected array $services = [
        'Complete Blood Count',
        'Urinalysis',
        'Blood Chemistry',
        'Thyroid Function Test',
        'Stool Examination',
        'Pregnancy Test',
        'X-Ray',
        'ECG / EKG',
    ];

    /**
     * Show the lab result builder for a specific appointment.
     *
     * Route: GET /admin/lab-result/{appointment}
     */
    public function builder(Appointment $appointment)
    {
        return view('lab_result_builder', [
            'appointment' => $appointment,
            'services'    => $this->services,
        ]);
    }

    /**
     * Show the builder without a pre-linked appointment
     * (standalone / blank form).
     *
     * Route: GET /admin/lab-result/create
     */
    public function create()
    {
        return view('lab_result_builder', [
            'appointment' => null,
            'services'    => $this->services,
        ]);
    }
}