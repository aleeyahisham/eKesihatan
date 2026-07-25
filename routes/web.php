<?php

use App\Http\Controllers\Admin\AppointmentController as AdminAppointmentController;
use App\Http\Controllers\Admin\AppointmentSlotController;
use App\Http\Controllers\Admin\BulletinController;
use App\Http\Controllers\Admin\DoctorController;
use App\Http\Controllers\Admin\DownloadableFormController;
use App\Http\Controllers\Admin\HealthServiceController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Doctor\AppointmentController as DoctorAppointmentController;
use App\Http\Controllers\Doctor\MedicalDocumentController;
use App\Http\Controllers\Doctor\PatientHistoryController;
use App\Http\Controllers\LandingController;
use App\Http\Controllers\Patient\AppointmentController as PatientAppointmentController;
use App\Http\Controllers\Patient\BmiController;
use App\Http\Controllers\Patient\ServiceController as PatientServiceController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\QueueController;
use App\Http\Controllers\Staff\PatientController as StaffPatientController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/

Route::get('/', [LandingController::class, 'index'])
    ->name('landing');

Route::get('/login', [AuthController::class, 'showLogin'])
    ->name('login');

Route::post('/login', [AuthController::class, 'login'])
    ->name('login.attempt');

Route::get('/staff/login', [AuthController::class, 'showStaffLogin'])
    ->name('staff.login');

Route::post('/staff/login', [AuthController::class, 'loginStaff'])
    ->name('staff.login.attempt');

Route::get('/register', [AuthController::class, 'showRegister'])
    ->name('register');

Route::post('/register', [AuthController::class, 'register'])
    ->name('register.store');

/*
|--------------------------------------------------------------------------
| Public QR Check-In
|--------------------------------------------------------------------------
|
| This route remains public because patients access it through the QR code.
|
*/

Route::get('/check-in/{token}', [QueueController::class, 'checkIn'])
    ->name('queue.check-in');

/*
|--------------------------------------------------------------------------
| Authenticated User Routes
|--------------------------------------------------------------------------
*/

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])
        ->name('dashboard');

    Route::get('/profile', [ProfileController::class, 'edit'])
        ->name('profile.edit');

    Route::put('/profile', [ProfileController::class, 'update'])
        ->name('profile.update');

    Route::put('/profile/password', [ProfileController::class, 'updatePassword'])
        ->name('profile.password.update');

    Route::post('/logout', [AuthController::class, 'logout'])
        ->name('logout');
});

/*
|--------------------------------------------------------------------------
| Patient Routes
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'role:patient'])
    ->prefix('patient')
    ->name('patient.')
    ->group(function () {
        Route::get('services', [PatientServiceController::class, 'index'])
            ->name('services.index');

        Route::get(
            'appointments/recommendations',
            [PatientAppointmentController::class, 'recommendations']
        )->name('appointments.recommendations');

        Route::get(
            'appointments/{appointment}/qr',
            [QueueController::class, 'qr']
        )->name('appointments.qr');

        Route::get(
            'appointments/{appointment}/qr-image',
            [QueueController::class, 'qrImage']
        )->name('appointments.qr-image');

        Route::get(
            'documents/{document}',
            [MedicalDocumentController::class, 'show']
        )->name('documents.show');

        Route::get('bmi', [BmiController::class, 'show'])
            ->name('bmi.show');

        Route::post('bmi', [BmiController::class, 'calculate'])
            ->name('bmi.calculate');

        Route::resource('appointments', PatientAppointmentController::class);
    });

/*
|--------------------------------------------------------------------------
| Doctor Routes
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'role:doctor'])
    ->prefix('doctor')
    ->name('doctor.')
    ->group(function () {
        Route::get(
            'appointments',
            [DoctorAppointmentController::class, 'index']
        )->name('appointments.index');

        Route::post(
            'appointments/emergency-reschedule',
            [DoctorAppointmentController::class, 'emergencyReschedule']
        )->name('appointments.emergency-reschedule');

        Route::post(
            'notifications/{notification}/read',
            [DoctorAppointmentController::class, 'markNotificationRead']
        )->name('notifications.read');

        Route::get(
            'appointments/{appointment}',
            [DoctorAppointmentController::class, 'show']
        )->name('appointments.show');

        Route::patch(
            'appointments/{appointment}',
            [DoctorAppointmentController::class, 'update']
        )->name('appointments.update');

        Route::get(
            'appointments/{appointment}/documents/create',
            [MedicalDocumentController::class, 'create']
        )->name('documents.create');

        Route::post(
            'appointments/{appointment}/documents',
            [MedicalDocumentController::class, 'store']
        )->name('documents.store');

        Route::get(
            'documents/{document}',
            [MedicalDocumentController::class, 'show']
        )->name('documents.show');

        Route::get(
            'patients/{patient}/history',
            [PatientHistoryController::class, 'show']
        )->name('patients.history');
    });

/*
|--------------------------------------------------------------------------
| Shared Staff and Doctor Patient Records
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'role:staff,doctor'])
    ->prefix('staff')
    ->name('staff.')
    ->group(function () {
        Route::get(
            'patients',
            [StaffPatientController::class, 'index']
        )->name('patients.index');

        Route::get(
            'patients/{patient}',
            [StaffPatientController::class, 'show']
        )->name('patients.show');
    });

/*
|--------------------------------------------------------------------------
| Admin / Staff Routes
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'role:staff'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::resource('services', HealthServiceController::class)
            ->except(['show']);

        Route::resource('doctors', DoctorController::class)
            ->except(['show']);

        Route::resource('slots', AppointmentSlotController::class)
            ->except(['show']);

        Route::resource('bulletins', BulletinController::class)
            ->except(['show']);

        Route::resource('forms', DownloadableFormController::class)
            ->except(['show']);

        Route::get(
            'appointments',
            [AdminAppointmentController::class, 'index']
        )->name('appointments.index');

        Route::get(
            'appointments/report/download',
            [AdminAppointmentController::class, 'downloadReport']
        )->name('appointments.report');

        Route::get(
            'appointments/{appointment}',
            [AdminAppointmentController::class, 'show']
        )->name('appointments.show');

        Route::put(
            'appointments/{appointment}',
            [AdminAppointmentController::class, 'update']
        )->name('appointments.update');
    });