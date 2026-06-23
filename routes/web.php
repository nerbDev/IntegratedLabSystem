<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AccountController;
use App\Http\Controllers\AppointmentController;
use App\Http\Controllers\AppointmentResultController;
use App\Http\Controllers\LabResultController;
use App\Http\Controllers\PatientController;
use App\Http\Controllers\ReportController;

// ------------------------------
// Public Landing Page
// ------------------------------
Route::get('/', function () {
    return view('welcome');
})->name('welcome');


// ------------------------------
// Auth Page (Login & Register)
// ------------------------------
Route::get('/login', [AccountController::class, 'showAuth'])->name('login.register');
Route::post('/login', [AccountController::class, 'login'])->name('login.submit');
Route::post('/register', [AccountController::class, 'register'])->name('register.submit');


// ------------------------------
// Protected Routes (Dashboards)
// ------------------------------
Route::middleware(['auth'])->group(function () {

    Route::middleware(['role:admin'])->group(function () {
        Route::get('/admindashboard', [AccountController::class, 'dashboard'])->name('admindashboard');
    });

    Route::middleware(['role:staff'])->group(function () {
        Route::get('/staffdashboard', function () {
            return view('staffdashboard');
        })->name('staffdashboard');
    });

    Route::middleware(['role:patient'])->group(function () {
        Route::get('/patientdashboard', function () {
            return view('patientdashboard');
        })->name('patientdashboard');
    });

});


// ------------------------------
// Logout
// ------------------------------
Route::post('/logout', [AccountController::class, 'logout'])->name('logout');


// ------------------------------
// Appointments
// ------------------------------

// Show appointment form
Route::get('/appointment', function () {
    return view('appointmentform');
})->name('appointment.form');

// Get available slots (for JS/AJAX)
Route::get('/get-available-slots', [AppointmentController::class, 'getAvailableSlots']);

// Submit appointment form
Route::post('/appointments', [AppointmentController::class, 'store'])->name('appointments.store');

// Staff & Admin: view all appointment requests
Route::get('/appointment-requests', [AppointmentController::class, 'showRequests'])->name('appointments.requests');

// Staff & Admin: manage a single appointment
Route::get('/manage-appointment/{id}', [AppointmentController::class, 'manageSingle'])->name('appointments.manage');
Route::put('/appointments/{id}', [AppointmentController::class, 'update'])->name('appointments.update');

// Patient: view their own appointments
Route::get('/my-appointments', [AppointmentController::class, 'patientIndex'])->name('patient.appointments');

// Admin: view all appointments
Route::get('/admin/appointments', [AppointmentController::class, 'adminIndex'])->name('admin.appointments.index');


// ------------------------------
// Lab Results (Admin)
// ------------------------------

// Admin: view the upload results page
Route::get('/admin/upload-results', [AppointmentResultController::class, 'showUploadForm'])
    ->name('admin.uploadResults');

// Admin: save/upload the PDF result
Route::post('/appointments/{id}/results', [AppointmentResultController::class, 'store'])
    ->name('admin.results.store');


// ------------------------------
// Lab Results (Patient)
// ------------------------------

// Patient: view released results
Route::get('/patient/results', [AppointmentResultController::class, 'patientResults'])
    ->name('patient.results.index');

// Patient: download/view PDF
Route::get('/patient/result/download/{id}', [AppointmentResultController::class, 'download'])
    ->name('patient.result.download');

    // Standalone blank builder (no appointment pre-filled)
Route::get('/admin/lab-result/create', [LabResultController::class, 'create'])
    ->name('admin.lab-result.create');
 
// Builder pre-filled from a specific appointment
// Link to this from your uploadResult blade "Upload File" button
Route::get('/admin/lab-result/{appointment}', [LabResultController::class, 'builder'])
    ->name('admin.lab-result.builder');

// Protects the group using your parameterized RoleMiddleware
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    
    // User Account Directory Management Panel Routes
    Route::get('/user-accounts', [AccountController::class, 'adminUserAccountsIndex'])->name('users.index');
   Route::put('/user-accounts/{id}', [AccountController::class, 'adminUserAccountsUpdate'])->name('users.update');
    Route::delete('/user-accounts/{id}', [AccountController::class, 'adminUserAccountsDestroy'])->name('users.destroy');
});

Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    // Patient Management Directorial Core Systems Maps
    Route::get('/patient-details', [PatientController::class, 'index'])->name('patients.index');
    Route::get('/patient-details/{id}', [PatientController::class, 'show'])->name('patients.show');
    Route::put('/patient-details/{id}', [PatientController::class, 'update'])->name('patients.update');
    Route::delete('/patient-details/{id}', [PatientController::class, 'destroy'])->name('patients.destroy');
});

Route::get('/admin/reports/weekly', [ReportController::class, 'index'])
    ->name('reports.weekly');