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
        Route::post('/check-availability', [RuserEquipmentController::class, 'checkAvailability'])->name('check-availability');
        Route::get('/borrowed', [RuserEquipmentController::class, 'borrowed'])->name('borrowed');
        Route::get('/history', [RuserEquipmentController::class, 'history'])->name('history');
        Route::patch('/request/{equipmentRequest}/cancel', [RuserEquipmentController::class, 'cancelRequest'])->name('cancel-request');
        Route::post('/return/{equipmentRequest}', [RuserEquipmentController::class, 'return'])->name('return');
    });

    // Laboratory Reservation Routes
    Route::prefix('laboratory')->name('ruser.laboratory.')->group(function () {
        Route::get('/', [RuserLaboratoryController::class, 'index'])->name('index');
        Route::get('/{laboratory}', [RuserLaboratoryController::class, 'show'])->name('show');
        Route::post('/{laboratory}/reserve', [RuserLaboratoryController::class, 'reserve'])->name('reserve');
        
        // Conflict checking route
        Route::post('/{laboratory}/conflicts/check', [RuserLaboratoryReservationController::class, 'checkConflicts'])->name('conflicts.check');
        
        // Get schedules for specific date
        Route::post('/{laboratory}/schedules/date', [RuserLaboratoryReservationController::class, 'getSchedulesForDate'])->name('schedules.date');
        
        // Laboratory Reservations
        Route::prefix('reservations')->name('reservations.')->group(function () {
            Route::get('/', [RuserLaboratoryReservationController::class, 'index'])->name('index');
            Route::get('/calendar', [RuserLaboratoryReservationController::class, 'calendar'])->name('calendar');
            Route::get('/quick', [RuserLaboratoryReservationController::class, 'quickReserve'])->name('quick');
            Route::post('/quick-store', [RuserLaboratoryReservationController::class, 'quickStore'])->name('quick-store');
            Route::get('/{laboratory}/create', [RuserLaboratoryReservationController::class, 'create'])->name('create');
            Route::post('/{laboratory}', [RuserLaboratoryReservationController::class, 'store'])->name('store');
            Route::get('/{reservation}/show', [RuserLaboratoryReservationController::class, 'show'])->name('show');
            Route::get('/{reservation}/confirmation', [RuserLaboratoryReservationController::class, 'confirmation'])->name('confirmation');
            Route::post('/{reservation}/cancel', [RuserLaboratoryReservationController::class, 'cancel'])->name('cancel');
        });
    });
});

// Admin Routes
Route::prefix('admin')->name('admin.')->group(function () {
    // Guest routes (login)
    Route::middleware('guest:admin')->group(function () {
        Route::get('login', [UnifiedAuthController::class, 'showLoginForm'])->name('login');
        Route::post('login', [UnifiedAuthController::class, 'login'])->name('login.post');
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
            Route::post('/set-current-by-date', [AcademicYearController::class, 'setCurrentByDate'])->name('set-current-by-date');
            Route::get('/daily-overview', [AcademicYearController::class, 'getDailyOverview'])->name('daily-overview');

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
            Route::get('/reservations', [LaboratoryController::class, 'reservations'])->name('reservations');
            Route::patch('/reservations/{reservation}/approve', [LaboratoryController::class, 'approveRequest'])->name('approve-request');
            Route::patch('/reservations/{reservation}/reject', [LaboratoryController::class, 'rejectRequest'])->name('reject-request');
            
            // Schedule Override Management (integrated under laboratory)
            Route::get('/schedule-overrides', [LaboratoryController::class, 'scheduleOverrides'])->name('schedule-overrides');
            Route::get('/schedule-overrides/create', [LaboratoryController::class, 'createScheduleOverride'])->name('create-override');
            Route::post('/schedule-overrides', [LaboratoryController::class, 'storeScheduleOverride'])->name('store-override');
            Route::delete('/schedule-overrides/{override}', [LaboratoryController::class, 'deactivateScheduleOverride'])->name('deactivate-override');
            Route::post('/get-schedules-for-date', [LaboratoryController::class, 'getSchedulesForDate'])->name('get-schedules-for-date');
        });

        // Laboratory Calendar Management
        Route::prefix('comlab')->name('comlab.')->group(function () {
            Route::get('/calendar', [ComputerLabCalendarController::class, 'index'])->name('calendar');
            Route::get('/schedule/create', [ComputerLabCalendarController::class, 'create'])->name('schedule.create-generic');
            Route::post('/schedule', [ComputerLabCalendarController::class, 'store'])->name('schedule.store-generic');
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
            Route::post('/check-approved-request', [AdminEquipmentController::class, 'checkApprovedRequest'])->name('check-approved-request');
            Route::get('/history', [AdminEquipmentController::class, 'history'])->name('history');
            Route::post('/', [AdminEquipmentController::class, 'store'])->name('store');
            Route::put('/{equipment}', [AdminEquipmentController::class, 'update'])->name('update');
            Route::delete('/{equipment}', [AdminEquipmentController::class, 'destroy'])->name('destroy');
            Route::patch('/{equipment}/rfid', [AdminEquipmentController::class, 'updateRfid'])->name('update-rfid');
            Route::patch('/{equipment}/identification', [AdminEquipmentController::class, 'updateIdentificationCode'])->name('update-identification');

            // Equipment Request Management Routes
            Route::get('/requests/create', [AdminEquipmentController::class, 'createRequest'])->name('create-request');
            Route::post('/requests', [AdminEquipmentController::class, 'storeRequest'])->name('store-request');
            Route::delete('/requests/{request}', [AdminEquipmentController::class, 'destroyRequest'])->name('destroy-request');
            Route::post('/requests/{request}/approve', [AdminEquipmentController::class, 'approveRequest'])->name('approve-request');
            Route::get('/requests/{request}/preview-conflicts', [AdminEquipmentController::class, 'previewConflicts'])->name('preview-conflicts');
            Route::post('/requests/{request}/checkout', [AdminEquipmentController::class, 'checkOutEquipment'])->name('checkout-request');
            Route::post('/requests/{request}/reject', [AdminEquipmentController::class, 'rejectRequest'])->name('reject-request');
            Route::post('/requests/{request}/return', [AdminEquipmentController::class, 'markAsReturned'])->name('return-request');
            
            // AJAX routes for equipment identification lookup
            Route::post('/find-by-rfid', [AdminEquipmentController::class, 'findByRfid'])->name('find-by-rfid');
            Route::post('/find-by-code', [AdminEquipmentController::class, 'findByCode'])->name('find-by-code');
            
            // Barcode export routes
            Route::get('/barcode/export', [AdminEquipmentController::class, 'barcodeExport'])->name('barcode.export');
            Route::get('/barcode/all', [AdminEquipmentController::class, 'exportAllBarcodes'])->name('barcode.all');
            Route::post('/barcode/selected', [AdminEquipmentController::class, 'exportSelectedBarcodes'])->name('barcode.selected');
            Route::get('/{equipment}/barcode', [AdminEquipmentController::class, 'exportSingleBarcode'])->name('barcode.single');
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

        // Super Admin Routes (restricted to super admins only)
        Route::prefix('super-admin')->name('super-admin.')->group(function () {
            Route::get('/', [\App\Http\Controllers\Admin\SuperAdminController::class, 'index'])->name('index');
            
            // System Reports
            Route::get('/reports', [\App\Http\Controllers\Admin\SuperAdminController::class, 'systemReports'])->name('reports');
            Route::get('/export/users', [\App\Http\Controllers\Admin\SuperAdminController::class, 'exportUsers'])->name('export.users');
            Route::post('/bulk-delete-users', [\App\Http\Controllers\Admin\SuperAdminController::class, 'bulkDeleteUsers'])->name('bulk-delete-users');
            
            // Admin Management
            Route::get('/admins/create', [\App\Http\Controllers\Admin\SuperAdminController::class, 'createAdmin'])->name('admins.create');
            Route::post('/admins', [\App\Http\Controllers\Admin\SuperAdminController::class, 'storeAdmin'])->name('admins.store');
            Route::get('/admins/{admin}', [\App\Http\Controllers\Admin\SuperAdminController::class, 'showAdmin'])->name('admins.show');
            Route::get('/admins/{admin}/edit', [\App\Http\Controllers\Admin\SuperAdminController::class, 'editAdmin'])->name('admins.edit');
            Route::put('/admins/{admin}', [\App\Http\Controllers\Admin\SuperAdminController::class, 'updateAdmin'])->name('admins.update');
            Route::delete('/admins/{admin}', [\App\Http\Controllers\Admin\SuperAdminController::class, 'destroyAdmin'])->name('admins.destroy');
            Route::patch('/admins/{admin}/toggle-status', [\App\Http\Controllers\Admin\SuperAdminController::class, 'toggleAdminStatus'])->name('admins.toggle-status');
            
            // User Management
            Route::get('/users/create', [\App\Http\Controllers\Admin\SuperAdminController::class, 'createUser'])->name('users.create');
            Route::post('/users', [\App\Http\Controllers\Admin\SuperAdminController::class, 'storeUser'])->name('users.store');
            Route::get('/users/{user}', [\App\Http\Controllers\Admin\SuperAdminController::class, 'showUser'])->name('users.show');
            Route::get('/users/{user}/edit', [\App\Http\Controllers\Admin\SuperAdminController::class, 'editUser'])->name('users.edit');
            Route::put('/users/{user}', [\App\Http\Controllers\Admin\SuperAdminController::class, 'updateUser'])->name('users.update');
            Route::delete('/users/{user}', [\App\Http\Controllers\Admin\SuperAdminController::class, 'destroyUser'])->name('users.destroy');
        });
    });
});
