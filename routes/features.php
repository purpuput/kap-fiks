<?php

use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\LogbookController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\AuditReportController;
use App\Http\Controllers\DocumentManagementController;
use App\Http\Controllers\ChatbotController;
use Illuminate\Support\Facades\Route;

// ATTENDANCE - Semua role
Route::middleware(['auth'])->group(function () {
    Route::get('/attendances', [AttendanceController::class, 'index'])->name('attendances.index');
    Route::get('/attendances/create', [AttendanceController::class, 'create'])->name('attendances.create');
    Route::post('/attendances', [AttendanceController::class, 'store'])->name('attendances.store');
    Route::get('/attendances/{attendance}/edit', [AttendanceController::class, 'edit'])->name('attendances.edit');
    Route::put('/attendances/{attendance}', [AttendanceController::class, 'update'])->name('attendances.update');
    Route::delete('/attendances/{attendance}', [AttendanceController::class, 'destroy'])->name('attendances.destroy');
    Route::get('/attendances/export/pdf', [AttendanceController::class, 'exportPdf'])->name('attendances.export.pdf');
    Route::get('/attendances/export/excel', [AttendanceController::class, 'exportExcel'])->name('attendances.export.excel');
    Route::get('/attendances/{attendance}/download', [AttendanceController::class, 'downloadAttachment'])->name('attendances.download');
    Route::get('/attendances/{attendance}/preview', [AttendanceController::class, 'previewAttachment'])->name('attendances.preview');
});

// LOGBOOK - Admin, Auditor, Pimpinan
Route::middleware(['auth', 'role:admin,auditor,pimpinan'])->group(function () {
    Route::resource('logbooks', LogbookController::class);
    Route::patch('/logbooks/{logbook}/approve', [LogbookController::class, 'approve'])->name('logbooks.approve');
    Route::patch('/logbooks/{logbook}/revision', [LogbookController::class, 'revision'])->name('logbooks.revision');
});

// CLIENTS - Admin, Auditor, Pimpinan
Route::middleware(['auth', 'role:admin,auditor,pimpinan'])->group(function () {
    Route::resource('clients', ClientController::class);
    Route::post('clients/{client}/approve', [ClientController::class, 'approve'])->name('clients.approve');
    Route::post('clients/{client}/revision', [ClientController::class, 'revision'])->name('clients.revision');
});

// AUDIT REPORTS - Admin, Auditor, Pimpinan
Route::middleware(['auth', 'role:admin,auditor,pimpinan'])->group(function () {
    Route::resource('audit_reports', AuditReportController::class);
    Route::get('/audit_reports/files/{file}/download', [AuditReportController::class, 'download'])->name('audit_reports.download');
    Route::get('/audit_reports/files/{file}/preview', [AuditReportController::class, 'preview'])->name('audit_reports.preview');
    Route::patch('/audit_reports/files/{file}/review', [AuditReportController::class, 'reviewFile'])->name('audit_reports.files.review');
    Route::put('/audit_reports/{audit_report}/resubmit', [AuditReportController::class, 'resubmit'])->name('audit_reports.resubmit');
});

// DOCUMENT MANAGEMENT - Admin, Auditor, Pimpinan (otomatis dari approve laporan)
Route::middleware(['auth', 'role:admin,auditor,pimpinan'])->group(function () {
    Route::get('/document-managements', [DocumentManagementController::class, 'index'])->name('document_managements.index');
    Route::get('/document-managements/{documentManagement}/download', [DocumentManagementController::class, 'download'])->name('document_managements.download');
    Route::get('/document-managements/{client}', [DocumentManagementController::class, 'show'])->name('document_managements.show');
    Route::get('/document-managements/preview/{documentManagement}', [DocumentManagementController::class, 'preview'])->name('document_managements.preview');
});

// CHATBOT - Semua role
Route::middleware(['auth'])->group(function () {
    Route::get('/chatbot', [ChatbotController::class, 'index'])->name('chatbot.index');
    Route::post('/chatbot/send', [ChatbotController::class, 'chat'])->name('chatbot.send');
    Route::post('/chatbot/clear', [ChatbotController::class, 'clearHistory'])->name('chatbot.clear');
    Route::delete('/chatbot/session/{id}', [ChatbotController::class, 'destroySession'])->name('chatbot.destroy_session');
});
