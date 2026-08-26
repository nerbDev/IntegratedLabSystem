<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Artisan;
use App\Http\Controllers\AppointmentController;
use App\Http\Controllers\AccountController;
use App\Http\Controllers\AppointmentResultController;
use App\Http\Controllers\LabResultController;
use App\Http\Controllers\PatientController;
use App\Http\Controllers\Auth\SocialAuthController;
use App\Http\Controllers\ActivityLogController;
use App\Http\Controllers\AdminProfileController;
use App\Http\Controllers\ArchiveController;
use App\Http\Controllers\StaffReportController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\StaffProfileController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\Auth\ForgotPasswordController;



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

// route for "create account" - admin side
Route::post('/admin/user-accounts', [AccountController::class, 'adminUserAccountsStore'])
    ->name('admin.users.store');

//google account redirect
Route::get('/auth/{provider}/redirect', [SocialAuthController::class, 'redirect'])
    ->name('social.redirect')
    ->whereIn('provider', ['google', 'facebook']);

Route::get('/auth/{provider}/callback', [SocialAuthController::class, 'callback'])
    ->whereIn('provider', ['google', 'facebook']);

Route::get('/complete-profile', [SocialAuthController::class, 'showCompleteProfile'])
    ->middleware('auth')->name('profile.complete');
Route::post('/complete-profile', [SocialAuthController::class, 'completeProfile'])
    ->middleware('auth')->name('profile.complete.submit');
// ------------------------------
// Protected Routes (Dashboards)
// ------------------------------
    Route::middleware(['auth'])->group(function () {

        Route::middleware(['role:admin'])->group(function () {
            Route::get('/admindashboard', [AccountController::class, 'dashboard'])->name('admindashboard');
        });

        Route::middleware(['role:staff'])->group(function () {
            Route::get('/staffdashboard', [AccountController::class, 'staffDashboard'])->name('staffdashboard');
        });

        Route::middleware(['role:patient'])->group(function () {
            Route::get('/patientdashboard', [AccountController::class, 'patientDashboard'])->name('patientdashboard');
            Route::patch('/appointments/{id}/cancel', [AppointmentController::class, 'patientCancel'])
                ->name('patient.appointments.cancel');
            Route::patch('/appointments/{id}/reschedule-response', [AppointmentController::class, 'patientRespondReschedule'])
                ->name('patient.appointments.reschedule-response');
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
    Route::get('/patient-details/print-list', [PatientController::class, 'printList'])->name('patients.print-list');
    Route::get('/patient-details/{id}/print', [PatientController::class, 'printPatient'])->name('patients.print');
    Route::get('/patient-details/{id}', [PatientController::class, 'show'])->name('patients.show');
    Route::put('/patient-details/{id}', [PatientController::class, 'update'])->name('patients.update');
    Route::delete('/patient-details/{id}', [PatientController::class, 'destroy'])->name('patients.destroy');
});


//for the admin the view activity logs
Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/admin/activity-logs', [ActivityLogController::class, 'index'])->name('admin.activityLogs.index');
});


//for the admin to view activitylogs\timeline
Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/admin/appointments/{id}/timeline', [AppointmentController::class, 'timeline'])
        ->name('admin.appointments.timeline');
});

Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/admin/profile', [AdminProfileController::class, 'show'])->name('admin.profile.show');
    Route::put('/admin/profile', [AdminProfileController::class, 'update'])->name('admin.profile.update');
    Route::put('/admin/profile/password', [AdminProfileController::class, 'updatePassword'])->name('admin.profile.password');
});


// Archive Records
Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/admin/archive', [ArchiveController::class, 'index'])->name('admin.archive.index');
});

Route::post('/admin/appointments/{id}/archive-now', [ArchiveController::class, 'archiveNow'])
    ->name('admin.archive.now');
    
Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/admin/archive', [ArchiveController::class, 'index'])->name('admin.archive.index');
    Route::get('/admin/archive/{id}/download', [ArchiveController::class, 'download'])->name('admin.archive.download');
});

Route::post('/admin/archive/{id}/restore', [ArchiveController::class, 'restore'])->name('admin.archive.restore');
 
// Staff: System Reports
Route::middleware(['auth', 'role:staff'])->group(function () {
    Route::get('/staff/reports', [StaffReportController::class, 'index'])
        ->name('staff.reports.index');
 
    Route::get('/staff/reports/generate', [StaffReportController::class, 'generate'])
        ->name('staff.reports.generate');
});
 
// Admin: System Reports
Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/admin/reports', [ReportController::class, 'index'])
        ->name('admin.reports.index');
 
    Route::get('/admin/reports/generate', [ReportController::class, 'generate'])
        ->name('admin.reports.generate');
});

// for the staff to view approved schedules 
Route::get('/staff/appointments/approved', [AppointmentController::class, 'approvedSchedule'])
    ->name('staff.appointments.approved');

 

// Add Promo
Route::get('/staff/settings/promo', [SettingsController::class, 'promoIndex'])->name('staff.settings.promo');
Route::post('/staff/settings/promo', [SettingsController::class, 'promoStore'])->name('staff.settings.promo.store');
Route::put('/staff/settings/promo/{package}', [SettingsController::class, 'promoUpdate'])->name('staff.settings.promo.update');
Route::delete('/staff/settings/promo/{package}', [SettingsController::class, 'promoDestroy'])->name('staff.settings.promo.destroy');
 
// Add Package Type (individual services)
Route::get('/staff/settings/services', [SettingsController::class, 'serviceIndex'])->name('staff.settings.package');
Route::post('/staff/settings/services', [SettingsController::class, 'serviceStore'])->name('staff.settings.package.store');
Route::put('/staff/settings/services/{service}', [SettingsController::class, 'serviceUpdate'])->name('staff.settings.package.update');
Route::delete('/staff/settings/services/{service}', [SettingsController::class, 'serviceDestroy'])->name('staff.settings.package.destroy');
 
// Modify Price
Route::get('/staff/settings/price', [SettingsController::class, 'priceIndex'])->name('staff.settings.price');
Route::put('/staff/settings/price/package/{package}', [SettingsController::class, 'priceUpdatePackage'])->name('staff.settings.price.package');
Route::put('/staff/settings/price/service/{service}', [SettingsController::class, 'priceUpdateService'])->name('staff.settings.price.service');
 
// Block Unavailable Days
Route::get('/staff/settings/unavailable', [SettingsController::class, 'unavailableIndex'])->name('staff.settings.unavailable');
Route::post('/staff/settings/unavailable', [SettingsController::class, 'unavailableStore'])->name('staff.settings.unavailable.store');
Route::delete('/staff/settings/unavailable/{unavailableDate}', [SettingsController::class, 'unavailableDestroy'])->name('staff.settings.unavailable.destroy');
 
// Public — feeds the patient booking form (no auth needed, it's read-only)
Route::get('/booking-data', [AppointmentController::class, 'bookingData'])->name('booking.data');


// for the staff to get, and edit profile
Route::middleware(['auth', 'role:staff'])->group(function () {
    Route::get('/staff/profile', [StaffProfileController::class, 'show'])->name('staff.profile.show');
    Route::put('/staff/profile', [StaffProfileController::class, 'update'])->name('staff.profile.update');
    Route::put('/staff/profile/password', [StaffProfileController::class, 'updatePassword'])->name('staff.profile.password');
});


// for the patient to view and  edit profile
Route::middleware('auth')->group(function () {
    Route::get('/patient/PSaccount-setting', [AccountController::class, 'patientAccountSettingShow'])->name('patient.accountsetting');
    Route::put('/patient/PSaccount-setting', [AccountController::class, 'patientAccountSettingUpdate'])->name('patient.accountsetting.update');
    Route::put('/patient/account-setting/password', [AccountController::class, 'patientPasswordUpdate'])->name('patient.accountsetting.password');
});

// --- Staff group (wherever 'staffdashboard' / 'appointments.requests' are defined) ---
Route::get('/staff/transactions', [TransactionController::class, 'staffTransactions'])
    ->name('staff.transactions');
 
// --- Patient group (wherever 'patient.appointments' is defined) ---
Route::get('/patient/transactions', [TransactionController::class, 'patientTransactions'])
    ->name('patient.transactions');
 
// --- Admin group (wherever your admin-only routes are defined) ---
Route::get('/admin/my-transactions', [TransactionController::class, 'adminTransactions'])
    ->name('admin.transactions');
 

    Route::get('/forgot-password', [ForgotPasswordController::class, 'showForgotForm'])
    ->name('password.forgot-form');
Route::post('/forgot-password', [ForgotPasswordController::class, 'sendOtp'])
    ->name('password.send-otp');
 
Route::get('/forgot-password/verify-otp', [ForgotPasswordController::class, 'showVerifyForm'])
    ->name('password.verify-otp.form');
Route::post('/forgot-password/verify-otp', [ForgotPasswordController::class, 'verifyOtp'])
    ->name('password.verify-otp');
 
Route::get('/forgot-password/reset', [ForgotPasswordController::class, 'showResetForm'])
    ->name('password.reset-form');
Route::post('/forgot-password/reset', [ForgotPasswordController::class, 'resetPassword'])
    ->name('password.reset');
 

//route for the admin to print patient list
Route::get('/admin/patient-details/print-list', [PatientController::class, 'printList'])
    ->name('admin.patients.print-list');
    
//route for the admin to print specific patient 
Route::get('/admin/patient-details/{id}/print', [PatientController::class, 'printPatient'])
    ->name('admin.patients.print');

Route::get('/cron/run-tasks/{secret}', function ($secret) {
    if ($secret !== config('app.cron_secret')) {
        abort(403);
    }

    Artisan::call('queue:work', ['--stop-when-empty' => true]);
    $queueOutput = Artisan::output();

    Artisan::call('archive:released-appointments'); // ⚠️ confirm this matches your actual command signature
    $archiveOutput = Artisan::output();

    return response()->json([
        'queue' => trim($queueOutput),
        'archive' => trim($archiveOutput),
        'ran_at' => now()->toDateTimeString(),
    ]);
});