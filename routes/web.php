<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\ProfileController;
// routes admin
use App\Http\Controllers\Admin\FormController;
use App\Http\Controllers\Admin\FormSettingsController;
use App\Http\Controllers\Admin\SectionController as AdminSectionController;
use App\Http\Controllers\Admin\QuestionController;
use App\Http\Controllers\Admin\LogicController;
use App\Http\Controllers\Admin\ResponseAdminController;
use App\Http\Controllers\Admin\ExportController;
use App\Http\Controllers\Admin\FormPreviewController;
use App\Http\Controllers\Admin\QuestionValidationAdminController;



use App\Http\Controllers\Respondent\StartController;
use App\Http\Controllers\Respondent\SectionController as RespondentSectionController;
use App\Http\Controllers\Respondent\SubmitController;
use App\Http\Controllers\Respondent\MyFormsController;


// ====== Landing / umum ======
Route::get('/', function () {
    return view('welcome');
});

// ====== Dashboard (responden) ======
// routes/web.php
Route::get('/dashboard', function () {
    $user = Auth::user();
    if ($user && $user->role === 'admin') {
        return redirect()->route('admin.home');
    }
    return redirect()->route('respondent.forms.index');
})->middleware(['auth', 'verified'])->name('dashboard');


// ====== Akun/Profile (Breeze default) ======
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// ====== Admin Area ======
// Pastikan middleware alias 'role' sudah diregister di Kernel: 'role' => \App\Http\Middleware\RoleMiddleware::class
Route::middleware(['auth', 'role:admin'])
    ->prefix('admin')
    ->as('admin.')
    ->group(function () {
        Route::view('/home', 'admin.home')->name('home');
        // Forms CRUD
        Route::resource('forms', FormController::class);
        // Settings (edit/update)
        Route::get('forms/{form:uid}/settings', [FormSettingsController::class, 'edit'])
            ->name('forms.settings.edit');
        Route::put('forms/{form:uid}/settings', [FormSettingsController::class, 'update'])
            ->name('forms.settings.update');
        // Sections (nested, shallow)
        // Admin
        Route::resource('forms.sections', AdminSectionController::class)->shallow();
        // Questions (nested, shallow)
        Route::resource('forms.questions', QuestionController::class)->shallow();
        // Branching / Logic rules
        Route::resource('forms.logic', LogicController::class)->shallow()->parameters([
            'logic' => 'logicRule'
        ]);
        // Responses admin (ringkasan/daftar/detail)
        Route::get('forms/{form:uid}/responses', [ResponseAdminController::class, 'index'])
            ->name('forms.responses.index');
        Route::get('responses/{response:uid}', [ResponseAdminController::class, 'show'])
            ->name('responses.show');
        // Export CSV
        Route::get('forms/{form:uid}/responses/export', [ExportController::class, 'csv'])
            ->name('forms.responses.export')
            ->middleware('throttle:5,1');
        // route publish
        Route::put('forms/{form:uid}/publish', [FormController::class, 'publish'])->name('forms.publish');
        Route::put('forms/{form:uid}/unpublish', [FormController::class, 'unpublish'])->name('forms.unpublish');
        // preview
        Route::get('forms/{form:uid}/preview', [FormPreviewController::class, 'show'])
            ->name('forms.preview');
        // Route Validation pertanyaan
        Route::get('questions/{question}/validations',        [QuestionValidationAdminController::class, 'index'])->name('questions.validations.index');
        Route::get('questions/{question}/validations/create', [QuestionValidationAdminController::class, 'create'])->name('questions.validations.create');
        Route::post('questions/{question}/validations',       [QuestionValidationAdminController::class, 'store'])->name('questions.validations.store');
        Route::get('validations/{validation}/edit',           [QuestionValidationAdminController::class, 'edit'])->name('questions.validations.edit');
        Route::put('validations/{validation}',                [QuestionValidationAdminController::class, 'update'])->name('questions.validations.update');
        Route::delete('validations/{validation}',             [QuestionValidationAdminController::class, 'destroy'])->name('questions.validations.destroy');
    });

// ====== Respondent: Pengisian Form (WAJIB login) ======
Route::middleware(['auth', 'verified'/*, 'respondent.domain' */])->group(function () {

    // Daftar form yang tersedia untuk responden
    Route::get('/my/forms', [MyFormsController::class, 'index'])
        ->name('respondent.forms.index');

    // Halaman awal/cover form + mulai/lanjut
    Route::get('/forms/{form:uid}', [StartController::class, 'show'])
        ->middleware('throttle:60,1') // 60 views per minute
        ->name('forms.start');

    Route::post('/forms/{form:uid}/begin', [StartController::class, 'begin'])
        ->middleware('throttle:draft-creation') // Use custom limiter
        ->name('forms.begin');

    // Isi per section
    Route::get('/forms/{form:uid}/s/{pos?}', [RespondentSectionController::class, 'show'])
        ->whereNumber('pos')
        ->middleware('throttle:100,1') // Navigation intensive
        ->name('forms.section');

    Route::post('/forms/{form:uid}/s/{pos}/save', [RespondentSectionController::class, 'save'])
        ->whereNumber('pos')
        ->middleware('throttle:answer-save') // Use custom limiter
        ->name('forms.section.save');

    // Submit & selesai
    Route::post('/forms/{form:uid}/submit', [SubmitController::class, 'submit'])
        ->middleware('throttle:form-submission') // STRICT limit - most important
        ->name('forms.submit');

    Route::get('/forms/{form:uid}/done', [SubmitController::class, 'done'])
        ->middleware('throttle:30,1')
        ->name('forms.done');

    Route::get('/forms/{form:uid}/review', [RespondentSectionController::class, 'review'])
        ->middleware('throttle:30,1')
        ->name('forms.review');
});
// ====== Auth routes dari Breeze ======
require __DIR__ . '/auth.php';
