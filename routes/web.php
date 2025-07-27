<?php

use App\Http\Controllers\Auth\UnifiedAuthController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\EquipmentController as AdminEquipmentController;
use App\Http\Controllers\Admin\AcademicYearController;
use App\Http\Controllers\Admin\AcademicTermController;
use App\Http\Controllers\Admin\LaboratoryController;
use App\Http\Controllers\Admin\ComputerLabCalendarController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin\EquipmentCategoryController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Ruser\RDashboardController;
use App\Http\Controllers\Ruser\EquipmentController as RuserEquipmentController;
use App\Http\Controllers\Ruser\LaboratoryController as RuserLaboratoryController;
use App\Http\Controllers\Ruser\LaboratoryReservationController as RuserLaboratoryReservationController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// Ruser Authentication Routes
Route::middleware('guest')->group(function () {
    Route::get('login', [UnifiedAuthController::class, 'showLoginForm'])->name('login');
    Route::post('login', [UnifiedAuthController::class, 'login']);
    Route::get('register', [UnifiedAuthController::class, 'showRegistrationForm'])->name('register');
    Route::post('register', [UnifiedAuthController::class, 'register']);
});

Route::middleware('auth')->group(function () {
    Route::post('logout', [UnifiedAuthController::class, 'logout'])->name('logout');
    Route::get('/dashboard', [RDashboardController::class, 'index'])->name('dashboard');

    // Profile Routes
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Equipment Borrowing Routes
    Route::prefix('equipment')->name('ruser.equipment.')->group(function () {
        Route::get('/', [RuserEquipmentController::class, 'index'])->name('borrow');
        Route::post('/request', [RuserEquipmentController::class, 'request'])->name('request');
        Route::get('/borrowed', [RuserEquipmentController::class, 'borrowed'])->name('borrowed');
        Route::get('/history', [RuserEquipmentController::class, 'history'])->name('history');
        Route::delete('/request/{equipmentRequest}', [RuserEquipmentController::class, 'cancelRequest'])->name('cancel-request');
        Route::post('/return/{equipmentRequest}', [RuserEquipmentController::class, 'return'])->name('return');
    });

    // Laboratory Reservation Routes
    Route::prefix('laboratory')->name('ruser.laboratory.')->group(function () {
        Route::get('/', [RuserLaboratoryController::class, 'index'])->name('index');
        Route::get('/{laboratory}', [RuserLaboratoryController::class, 'show'])->name('show');
        Route::post('/{laboratory}/reserve', [RuserLaboratoryController::class, 'reserve'])->name('reserve');
        
        // Laboratory Reservations
        Route::prefix('reservations')->name('reservations.')->group(function () {
            Route::get('/', [RuserLaboratoryReservationController::class, 'index'])->name('index');
            Route::get('/calendar', [RuserLaboratoryReservationController::class, 'calendar'])->name('calendar');
            Route::get('/quick', [RuserLaboratoryReservationController::class, 'quickReserve'])->name('quick');
            Route::post('/quick-store', [RuserLaboratoryReservationController::class, 'quickStore'])->name('quick-store');
            Route::get('/{laboratory}/create', [RuserLaboratoryReservationController::class, 'create'])->name('create');
            Route::post('/{laboratory}', [RuserLaboratoryReservationController::class, 'store'])->name('store');
            Route::get('/{reservation}/show', [RuserLaboratoryReservationController::class, 'show'])->name('show');
            Route::post('/{reservation}/cancel', [RuserLaboratoryReservationController::class, 'cancel'])->name('cancel');
        });
    });
});

// Admin Routes
Route::prefix('admin')->name('admin.')->group(function () {
    // Guest routes (login)
    Route::middleware('guest:admin')->group(function () {
        Route::get('login', [UnifiedAuthController::class, 'showLoginForm'])->name('login');
        Route::post('login', [UnifiedAuthController::class, 'login'])->name('login');
    });

    // Authenticated admin routes
    Route::middleware('auth:admin')->group(function () {
        Route::post('logout', [UnifiedAuthController::class, 'logout'])->name('logout');
        
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
        });

        // Laboratory Calendar Management
        Route::prefix('comlab')->name('comlab.')->group(function () {
            Route::get('/calendar', [ComputerLabCalendarController::class, 'index'])->name('calendar');
            Route::get('/{laboratory}/schedule/create', [ComputerLabCalendarController::class, 'create'])->name('schedule.create');
            Route::post('/{laboratory}/schedule', [ComputerLabCalendarController::class, 'store'])->name('schedule.store');
            Route::get('/{laboratory}/schedule/{schedule}/edit', [ComputerLabCalendarController::class, 'edit'])->name('schedule.edit');
            Route::put('/{laboratory}/schedule/{schedule}', [ComputerLabCalendarController::class, 'update'])->name('schedule.update');
            Route::delete('/{laboratory}/schedule/{schedule}', [ComputerLabCalendarController::class, 'destroy'])->name('schedule.destroy');
            Route::get('/{laboratory}/schedules', [ComputerLabCalendarController::class, 'laboratorySchedules'])->name('schedule.list');
        });

        // Equipment Management
        Route::prefix('equipment')->name('equipment.')->group(function () {
            Route::get('/', [AdminEquipmentController::class, 'index'])->name('index');
            Route::get('/manage', [AdminEquipmentController::class, 'manage'])->name('manage');
            Route::get('/create', [AdminEquipmentController::class, 'create'])->name('create');
            
            // Equipment Categories
            Route::prefix('categories')->name('categories.')->group(function () {
                Route::get('/', [EquipmentCategoryController::class, 'index'])->name('index');
                Route::get('/create', [EquipmentCategoryController::class, 'create'])->name('create');
                Route::post('/', [EquipmentCategoryController::class, 'store'])->name('store');
                Route::get('/{category}/edit', [EquipmentCategoryController::class, 'edit'])->name('edit');
                Route::put('/{category}', [EquipmentCategoryController::class, 'update'])->name('update');
                Route::delete('/{category}', [EquipmentCategoryController::class, 'destroy'])->name('destroy');
            });

            Route::get('/borrow-requests', [AdminEquipmentController::class, 'borrowRequests'])->name('borrow-requests');
            Route::post('/borrow-requests/onsite', [AdminEquipmentController::class, 'createOnsiteBorrow'])->name('borrow-requests.onsite');
            Route::get('/history', [AdminEquipmentController::class, 'history'])->name('history');
            Route::post('/', [AdminEquipmentController::class, 'store'])->name('store');
            Route::put('/{equipment}', [AdminEquipmentController::class, 'update'])->name('update');
            Route::delete('/{equipment}', [AdminEquipmentController::class, 'destroy'])->name('destroy');
            Route::patch('/{equipment}/rfid', [AdminEquipmentController::class, 'updateRfid'])->name('update-rfid');

            // Equipment Request Management Routes
            Route::get('/requests/create', [AdminEquipmentController::class, 'createRequest'])->name('create-request');
            Route::post('/requests', [AdminEquipmentController::class, 'storeRequest'])->name('store-request');
            Route::delete('/requests/{request}', [AdminEquipmentController::class, 'destroyRequest'])->name('destroy-request');
            Route::post('/requests/{request}/approve', [AdminEquipmentController::class, 'approveRequest'])->name('approve-request');
            Route::post('/requests/{request}/reject', [AdminEquipmentController::class, 'rejectRequest'])->name('reject-request');
            Route::post('/requests/{request}/return', [AdminEquipmentController::class, 'markAsReturned'])->name('return-request');
            
            // AJAX route for equipment RFID lookup
            Route::post('/find-by-rfid', [AdminEquipmentController::class, 'findByRfid'])->name('find-by-rfid');
        });

        // User Management
        Route::prefix('users')->name('users.')->group(function () {
            Route::get('/', [UserController::class, 'index'])->name('index');
            Route::get('/create', [UserController::class, 'create'])->name('create');
            Route::post('/', [UserController::class, 'store'])->name('store');
            Route::get('/{user}', [UserController::class, 'show'])->name('show');
            Route::get('/{user}/edit', [UserController::class, 'edit'])->name('edit');
            Route::put('/{user}', [UserController::class, 'update'])->name('update');
            Route::delete('/{user}', [UserController::class, 'destroy'])->name('destroy');
            Route::get('/{user}/reset-password', [UserController::class, 'showResetPasswordForm'])->name('reset-password');
            Route::post('/{user}/reset-password', [UserController::class, 'resetPassword'])->name('reset-password.store');
            
            // AJAX route for RFID lookup
            Route::post('/find-by-rfid', [UserController::class, 'findByRfid'])->name('find-by-rfid');
        });
    });
});
