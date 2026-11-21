<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\BorrowingController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\MemberController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\KtaController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
})->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    // Dashboard - semua role yang login bisa akses
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard/trend', [DashboardController::class, 'trend'])->name('dashboard.trend');

    // Inventaris - hanya ketua_umum dan pengurus yang bisa CRUD
    Route::middleware('role:ketua_umum,pengurus')->group(function () {
        Route::get('/inventories', [InventoryController::class, 'index'])->name('inventories.index');
        Route::post('/inventories', [InventoryController::class, 'store'])->name('inventories.store');
        Route::put('/inventories/{inventory}', [InventoryController::class, 'update'])->name('inventories.update');
        Route::delete('/inventories/{inventory}', [InventoryController::class, 'destroy'])->name('inventories.destroy');
    });

    // Peminjaman - semua bisa lihat dan ajukan, hanya pengurus yang approve/hapus
    Route::get('/borrowings', [BorrowingController::class, 'index'])->name('borrowings.index');
    Route::post('/borrowings', [BorrowingController::class, 'store'])->name('borrowings.store');
    Route::middleware('role:ketua_umum,pengurus')->group(function () {
        Route::patch('/borrowings/{borrowing}/status', [BorrowingController::class, 'updateStatus'])->name('borrowings.updateStatus');
        Route::delete('/borrowings/{borrowing}', [BorrowingController::class, 'destroy'])->name('borrowings.destroy');
    });

    // Event - semua bisa lihat, hanya ketua_umum dan pengurus yang buat/ubah/hapus
    Route::get('/events', [EventController::class, 'index'])->name('events.index');
    Route::middleware('role:ketua_umum,pengurus')->group(function () {
        Route::post('/events', [EventController::class, 'store'])->name('events.store');
        Route::put('/events/{event}', [EventController::class, 'update'])->name('events.update');
        Route::delete('/events/{event}', [EventController::class, 'destroy'])->name('events.destroy');
    });

    // Manajemen Anggota - ketua_umum dan pengurus
    Route::middleware('role:ketua_umum,pengurus')->group(function () {
        Route::resource('members', MemberController::class)->except(['create', 'show', 'edit']);
        Route::get('/users', [UserController::class, 'index'])->name('users.index');
    });

    // Modul Presensi QR
    Route::get('/attendances', [AttendanceController::class, 'index'])->name('attendances.index');
    Route::post('/attendances/scan', [AttendanceController::class, 'scan'])->name('attendances.scan');
    Route::middleware('role:ketua_umum,pengurus')->group(function () {
        Route::post('/attendances', [AttendanceController::class, 'store'])->name('attendances.store');
        Route::patch('/attendances/{attendance}', [AttendanceController::class, 'update'])->name('attendances.update');
        Route::post('/attendances/events/{event}/populate', [AttendanceController::class, 'populate'])->name('attendances.populate');
        Route::post('/attendances/events/{event}/token', [AttendanceController::class, 'generateToken'])->name('attendances.generateToken');
    });

    // Self-attendance dari halaman detail event - semua role aktif bisa presensi sendiri
    Route::post('/events/{event}/self-attend', [AttendanceController::class, 'selfAttend'])->name('events.selfAttend');

    // Detail presensi event (AJAX untuk modal detail)
    Route::get('/events/{event}/attendances', [AttendanceController::class, 'eventAttendances'])->name('events.attendances');
    Route::post('/events/{event}/auto-alpha', [AttendanceController::class, 'markAutoAlpha'])->middleware('role:ketua_umum,pengurus')->name('events.autoAlpha');

    // Modul KTA Digital - semua bisa lihat KTA sendiri
    Route::get('/kta', [KtaController::class, 'index'])->name('kta.index');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // ============================================
    // MODUL BARU DARI CLUBSPHERE (kecuali Tenant & Organisasi)
    // ============================================

    // Akses & Hak Akses (roles, staff_positions, work_units, approval_flows)
    Route::middleware('role:ketua_umum')->group(function () {
        Route::resource('roles', \App\Http\Controllers\Access\RoleController::class)->except(['create', 'show', 'edit']);
        Route::resource('staff-positions', \App\Http\Controllers\Access\StaffPositionController::class)
            ->parameters(['staff-positions' => 'position'])
            ->except(['create', 'show', 'edit']);
        Route::resource('work-units', \App\Http\Controllers\Access\WorkUnitController::class)
            ->parameters(['work-units' => 'unit'])
            ->except(['create', 'show', 'edit']);
        Route::resource('approval-flows', \App\Http\Controllers\Access\ApprovalFlowController::class)
            ->parameters(['approval-flows' => 'flow'])
            ->except(['create', 'show', 'edit']);
        Route::resource('approval-flow-steps', \App\Http\Controllers\Access\ApprovalFlowStepController::class)
            ->parameters(['approval-flow-steps' => 'step'])
            ->except(['create', 'show', 'edit']);
    });

    // Keuangan (financial_accounts, financial_categories, financial_transactions, funding_requests)
    Route::middleware('role:ketua_umum,pengurus')->group(function () {
        Route::resource('financial-accounts', \App\Http\Controllers\Finance\FinancialAccountController::class)
            ->parameters(['financial-accounts' => 'account'])
            ->except(['create', 'show', 'edit']);
        Route::resource('financial-categories', \App\Http\Controllers\Finance\FinancialCategoryController::class)
            ->parameters(['financial-categories' => 'category'])
            ->except(['create', 'show', 'edit']);
        Route::resource('financial-transactions', \App\Http\Controllers\Finance\FinancialTransactionController::class)
            ->parameters(['financial-transactions' => 'transaction'])
            ->except(['create', 'show', 'edit']);
        Route::resource('funding-requests', \App\Http\Controllers\Finance\FundingRequestController::class)
            ->parameters(['funding-requests' => 'requestFunding'])
            ->except(['create', 'show', 'edit']);
    });

    // Presensi (attendance_sessions)
    Route::middleware('role:ketua_umum,pengurus')->group(function () {
        Route::resource('attendance-sessions', \App\Http\Controllers\Attendance\SessionController::class)
            ->parameters(['attendance-sessions' => 'session'])
            ->except(['create', 'show', 'edit']);
    });

    // Surat (letters, letter_approvals, letter_dispositions)
    Route::middleware('role:ketua_umum,pengurus')->group(function () {
        Route::resource('letters', \App\Http\Controllers\Letter\LetterController::class)->except(['create', 'show', 'edit']);
        Route::resource('letter-approvals', \App\Http\Controllers\Letter\LetterApprovalController::class)
            ->parameters(['letter-approvals' => 'approval'])
            ->except(['create', 'show', 'edit']);
        Route::resource('letter-dispositions', \App\Http\Controllers\Letter\LetterDispositionController::class)
            ->parameters(['letter-dispositions' => 'disposition'])
            ->except(['create', 'show', 'edit']);
    });

    // Evaluasi (forms, form_questions, form_responses)
    Route::middleware('role:ketua_umum,pengurus')->group(function () {
        Route::resource('forms', \App\Http\Controllers\Evaluation\FormController::class)->except(['create', 'show', 'edit']);
        Route::resource('form-questions', \App\Http\Controllers\Evaluation\FormQuestionController::class)
            ->parameters(['form-questions' => 'question'])
            ->except(['create', 'show', 'edit']);
        Route::resource('form-responses', \App\Http\Controllers\Evaluation\FormResponseController::class)
            ->parameters(['form-responses' => 'response'])
            ->except(['create', 'show', 'edit']);
    });

    // Sponsor (sponsorship_packages, sponsorships, partnership_agreements)
    Route::middleware('role:ketua_umum,pengurus')->group(function () {
        Route::resource('sponsorship-packages', \App\Http\Controllers\Sponsor\SponsorshipPackageController::class)
            ->parameters(['sponsorship-packages' => 'package'])
            ->except(['create', 'show', 'edit']);
        Route::resource('sponsorships', \App\Http\Controllers\Sponsor\SponsorshipController::class)->except(['create', 'show', 'edit']);
        Route::resource('partnership-agreements', \App\Http\Controllers\Sponsor\PartnershipAgreementController::class)
            ->parameters(['partnership-agreements' => 'agreement'])
            ->except(['create', 'show', 'edit']);
    });

    // Program (programs)
    Route::middleware('role:ketua_umum,pengurus')->group(function () {
        Route::resource('programs', \App\Http\Controllers\Program\ProgramController::class)->except(['create', 'show', 'edit']);
    });

    // Anggota & Rekrutmen (recruitments, recruitment_applicants)
    Route::middleware('role:ketua_umum,pengurus')->group(function () {
        Route::resource('recruitments', \App\Http\Controllers\Member\RecruitmentController::class)->except(['create', 'show', 'edit']);
        Route::resource('recruitment-applicants', \App\Http\Controllers\Member\RecruitmentApplicantController::class)
            ->parameters(['recruitment-applicants' => 'applicant'])
            ->except(['create', 'show', 'edit']);
    });
});

require __DIR__.'/auth.php';
