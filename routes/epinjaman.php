<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| ePinjaman domain routes
|--------------------------------------------------------------------------
*/

// Account activation from the signed 24h link (guest, no auth required).
Route::livewire('account/activate/{user}', 'pages::auth.activate-account')
    ->middleware('signed')
    ->name('account.activate');

// SID / Admin provisioning.
Route::middleware(['auth', 'verified'])->group(function () {
    Route::livewire('sid/organisasi-pemohon', 'pages::sid.organisasi-pemohon')
        ->name('sid.organisasi-pemohon');

    // Admin reference repositories (Lampiran 6 & 8).
    Route::livewire('admin/checklist-items', 'pages::admin.checklist-items')
        ->name('admin.checklist-items');
    Route::livewire('admin/notification-templates', 'pages::admin.notification-templates')
        ->name('admin.notification-templates');

    // Pemohon — loan application form.
    Route::livewire('permohonan', 'pages::pemohon.permohonan.index')
        ->name('permohonan.index');
    Route::livewire('permohonan/baharu', 'pages::pemohon.permohonan.borang')
        ->name('permohonan.baharu');
    Route::livewire('permohonan/{permohonan}/tandatangan-p1', 'pages::pemohon.permohonan.tandatangan-p1')
        ->name('permohonan.tandatangan-p1');
    Route::livewire('permohonan/{permohonan}', 'pages::pemohon.permohonan.borang')
        ->name('permohonan.borang');

    // Kementerian Pengawal — stage-2 tray & signature.
    Route::livewire('kementerian/tray', 'pages::kementerian.tray')
        ->name('kementerian.tray');
    Route::livewire('kementerian/permohonan/{permohonan}/tandatangan-p2', 'pages::kementerian.tandatangan-p2')
        ->name('kementerian.tandatangan-p2');

    // SID — task tray & document checklist review.
    Route::livewire('sid/tray', 'pages::sid.tray')->name('sid.tray');
    Route::livewire('sid/permohonan/{permohonan}/semakan-dokumen', 'pages::sid.semakan-dokumen')
        ->name('sid.semakan-dokumen');
});
