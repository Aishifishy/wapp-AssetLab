<?php

use App\Http\Controllers\Auth\RuserAuthController;
use App\Http\Controllers\Auth\RadminAuthController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\EquipmentController;
use App\Http\Controllers\Admin\AcademicYearController;
use App\Http\Controllers\Admin\AcademicTermController;
use App\Http\Controllers\Admin\LaboratoryController;
use App\Http\Controllers\Admin\ComputerLabCalendarController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin\EquipmentCategoryController;
use App\Http\Controllers\Ruser\RDashboardController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// Ruser Authentication Routes
Route::middleware('guest')->group(function () {
    Route::get('login', [RuserAuthController::class, 'showLoginForm'])->name('login');
    Route::post('login', [RuserAuthController::class, 'login']);
    Route::get('register', [RuserAuthController::class, 'showRegistrationForm'])->name('register');
    Route::post('register', [RuserAuthController::class, 'register']);
});

Route::middleware('auth')->group(function () {
    Route::post('logout', [RuserAuthController::class, 'logout'])->name('logout');
    Route::get('/dashboard', [RDashboardController::class, 'index'])->name('dashboard');
    
    // Ruser specific routes
    Route::prefix('ruser')->name('ruser.')->group(function() {
        Route::get('/dashboard', [RDashboardController::class, 'index'])->name('dashboard');
        Route::get('/profile', [ProfileController::class, 'edit'])->name('profile');
        
        // Laboratory reservation routes
        Route::prefix('laboratory')->name('laboratory.')->group(function() {
            Route::get('/', [\App\Http\Controllers\Ruser\LaboratoryController::class, 'index'])->name('index');
            Route::get('/{laboratory}', [\App\Http\Controllers\Ruser\LaboratoryController::class, 'show'])->name('show');
            Route::post('/{laboratory}/reserve', [\App\Http\Controllers\Ruser\LaboratoryController::class, 'reserve'])->name('reserve');
            
            // New Laboratory Reservation Routes
            Route::prefix('reservations')->name('reservations.')->group(function() {
                Route::get('/', [\App\Http\Controllers\Ruser\LaboratoryReservationController::class, 'index'])->name('index');
                Route::get('/calendar', [\App\Http\Controllers\Ruser\LaboratoryReservationController::class, 'calendar'])->name('calendar');
                Route::get('/quick', [\App\Http\Controllers\Ruser\LaboratoryReservationController::class, 'quickReserveForm'])->name('quick');
                Route::post('/quick', [\App\Http\Controllers\Ruser\LaboratoryReservationController::class, 'quickReserveStore'])->name('quick-store');
                Route::get('/create/{laboratory}', [\App\Http\Controllers\Ruser\LaboratoryReservationController::class, 'create'])->name('create');
                Route::post('/store/{laboratory}', [\App\Http\Controllers\Ruser\LaboratoryReservationController::class, 'store'])->name('store');
                Route::get('/{reservation}', [\App\Http\Controllers\Ruser\LaboratoryReservationController::class, 'show'])->name('show');
                Route::post('/{reservation}/cancel', [\App\Http\Controllers\Ruser\LaboratoryReservationController::class, 'cancel'])->name('cancel');
            });
        });
        
        // Equipment borrowing routes for regular users
        Route::prefix('equipment')->name('equipment.')->group(function() {
            Route::get('/', [\App\Http\Controllers\Ruser\EquipmentController::class, 'index'])->name('borrow');
            Route::post('/request', [\App\Http\Controllers\Ruser\EquipmentController::class, 'request'])->name('request');
            Route::delete('/request/{equipmentRequest}', [\App\Http\Controllers\Ruser\EquipmentController::class, 'cancelRequest'])->name('cancel-request');
            Route::post('/return/{equipmentRequest}', [\App\Http\Controllers\Ruser\EquipmentController::class, 'return'])->name('return');
        });
    });

    // Profile Routes
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Admin Routes
Route::prefix('admin')->name('admin.')->group(function () {
    // Guest routes (login)
    Route::middleware('guest:admin')->group(function () {
        Route::get('login', [RadminAuthController::class, 'showLoginForm'])->name('login');
        Route::post('login', [RadminAuthController::class, 'login'])->name('login');
    });

    // Authenticated admin routes
    Route::middleware('auth:admin')->group(function () {
        Route::post('logout', [RadminAuthController::class, 'logout'])->name('logout');
        
        // Dashboard
        Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');

        // Academic Year Management
        Route::prefix('academic')->name('academic.')->group(function () {
            Route::get('/', [AcademicYearController::class, 'index'])->name('index');
            Route::get('/create', [AcademicYearController::class, 'create'])->name('create');
            Route::post('/', [AcademicYearController::class, 'store'])->name('store');
            Route::get('/{academicYear}/edit', [AcademicYearController::class, 'edit'])->name('edit');
            Route::put('/{academicYear}', [AcademicYearController::class, 'update'])->name('update');
            Route::delete('/{academicYear}', [AcademicYearController::class, 'destroy'])->name('destroy');
            Route::post('/{academicYear}/set-current', [AcademicYearController::class, 'setCurrent'])->name('set-current');

            // Academic Term Management
            Route::prefix('{academicYear}/terms')->name('terms.')->group(function () {
                Route::get('/', [AcademicTermController::class, 'index'])->name('index');
                Route::get('/create', [AcademicTermController::class, 'create'])->name('create');
                Route::post('/', [AcademicTermController::class, 'store'])->name('store');
                Route::get('/{term}/edit', [AcademicTermController::class, 'edit'])->name('edit');
                Route::put('/{term}', [AcademicTermController::class, 'update'])->name('update');
                Route::post('/{term}/set-current', [AcademicTermController::class, 'setCurrent'])->name('set-current');
            });
        });

        // Laboratory Management
        Route::prefix('laboratory')->name('laboratory.')->group(function () {
            Route::get('/', [LaboratoryController::class, 'index'])->name('index');
            Route::get('/create', [LaboratoryController::class, 'create'])->name('create');
            Route::post('/', [LaboratoryController::class, 'store'])->name('store');
            Route::get('/{laboratory}/edit', [LaboratoryController::class, 'edit'])->name('edit');
            Route::put('/{laboratory}', [LaboratoryController::class, 'update'])->name('update');
            Route::delete('/{laboratory}', [LaboratoryController::class, 'destroy'])->name('destroy');
            Route::patch('/{laboratory}/status', [LaboratoryController::class, 'updateStatus'])->name('update-status');
            
            // Laboratory Reservations Management
            Route::prefix('reservations')->name('reservations.')->group(function () {
                Route::get('/', [\App\Http\Controllers\Admin\LaboratoryReservationController::class, 'index'])->name('index');
                Route::get('/{reservation}', [\App\Http\Controllers\Admin\LaboratoryReservationController::class, 'show'])->name('show');
                Route::post('/{reservation}/approve', [\App\Http\Controllers\Admin\LaboratoryReservationController::class, 'approve'])->name('approve');
                Route::post('/{reservation}/reject', [\App\Http\Controllers\Admin\LaboratoryReservationController::class, 'reject'])->name('reject');
                Route::delete('/{reservation}', [\App\Http\Controllers\Admin\LaboratoryReservationController::class, 'destroy'])->name('destroy');
            });
        });

        // Laboratory Calendar Management
        Route::prefix('comlab')->name('comlab.')->group(function () {
            Route::get('/calendar', [ComputerLabCalendarController::class, 'index'])->name('calendar');
            Route::get('/schedule/create', [ComputerLabCalendarController::class, 'create'])->name('schedule.create');
            Route::post('/schedule', [ComputerLabCalendarController::class, 'store'])->name('schedule.store');
            Route::get('/{laboratory}/schedule/{schedule}/edit', [ComputerLabCalendarController::class, 'edit'])->name('schedule.edit');
            Route::put('/{laboratory}/schedule/{schedule}', [ComputerLabCalendarController::class, 'update'])->name('schedule.update');
            Route::delete('/{laboratory}/schedule/{schedule}', [ComputerLabCalendarController::class, 'destroy'])->name('schedule.destroy');
            Route::get('/{laboratory}/schedules', [ComputerLabCalendarController::class, 'laboratorySchedules'])->name('schedule.list');
        });

        // Equipment Management
        Route::prefix('equipment')->name('equipment.')->group(function () {
            Route::get('/', [EquipmentController::class, 'index'])->name('index');
            Route::get('/manage', [EquipmentController::class, 'manage'])->name('manage');
            Route::get('/create', [EquipmentController::class, 'create'])->name('create');
            
            // Equipment Categories
            Route::prefix('categories')->name('categories.')->group(function () {
                Route::get('/', [EquipmentCategoryController::class, 'index'])->name('index');
                Route::get('/create', [EquipmentCategoryController::class, 'create'])->name('create');
                Route::post('/', [EquipmentCategoryController::class, 'store'])->name('store');
                Route::get('/{category}/edit', [EquipmentCategoryController::class, 'edit'])->name('edit');
                Route::put('/{category}', [EquipmentCategoryController::class, 'update'])->name('update');
                Route::delete('/{category}', [EquipmentCategoryController::class, 'destroy'])->name('destroy');
            });

            Route::get('/borrow-requests', [EquipmentController::class, 'borrowRequests'])->name('borrow-requests');
            Route::post('/borrow-requests/onsite', [EquipmentController::class, 'createOnsiteBorrow'])->name('borrow-requests.onsite');
            Route::post('/', [EquipmentController::class, 'store'])->name('store');
            Route::put('/{equipment}', [EquipmentController::class, 'update'])->name('update');
            Route::delete('/{equipment}', [EquipmentController::class, 'destroy'])->name('destroy');
            Route::patch('/{equipment}/rfid', [EquipmentController::class, 'updateRfid'])->name('update-rfid');

            // Equipment Request Management Routes
            Route::get('/requests/create', [EquipmentController::class, 'createRequest'])->name('create-request');
            Route::post('/requests', [EquipmentController::class, 'storeRequest'])->name('store-request');
            Route::delete('/requests/{request}', [EquipmentController::class, 'destroyRequest'])->name('destroy-request');
            Route::post('/requests/{request}/approve', [EquipmentController::class, 'approveRequest'])->name('approve-request');
            Route::post('/requests/{request}/reject', [EquipmentController::class, 'rejectRequest'])->name('reject-request');
            Route::post('/requests/{request}/return', [EquipmentController::class, 'markAsReturned'])->name('return-request');
        });
    });
});
