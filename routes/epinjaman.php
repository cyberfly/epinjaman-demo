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
    Route::livewire('permohonan/{permohonan}/surat-tawaran', 'pages::pemohon.permohonan.surat-tawaran')
        ->name('permohonan.surat-tawaran');
    Route::livewire('permohonan/{permohonan}/perjanjian', 'pages::pemohon.permohonan.perjanjian')
        ->name('permohonan.perjanjian');

    // SID / BUU agreement review.
    Route::livewire('perjanjian/{permohonan}/semakan', 'pages::perjanjian.semakan')
        ->name('perjanjian.semakan');
    Route::livewire('perjanjian/{permohonan}/tandatangan-manual', 'pages::perjanjian.tandatangan-manual')
        ->name('perjanjian.tandatangan-manual');
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
    Route::livewire('sid/permohonan/{permohonan}/kuiri', 'pages::sid.kuiri')
        ->name('sid.kuiri');
    Route::livewire('sid/permohonan/{permohonan}/rundingan', 'pages::sid.rundingan')
        ->name('sid.rundingan');
    Route::livewire('sid/permohonan/{permohonan}/syarat-duluan', 'pages::sid.syarat-duluan')
        ->name('sid.syarat-duluan');
    Route::livewire('sid/permohonan/{permohonan}/sejarah', 'pages::sid.sejarah')
        ->name('sid.sejarah');

    // Peminjam — Conditions Precedent (CP) upload.
    Route::livewire('permohonan/{permohonan}/syarat-duluan', 'pages::pemohon.permohonan.syarat-duluan')
        ->name('permohonan.syarat-duluan');

    // Kuiri reply (Pemohon / Kementerian Pengawal).
    Route::livewire('kuiri/{kuiri}/balas', 'pages::kuiri.balas')->name('kuiri.balas');

    // Approval hierarchy.
    Route::livewire('kelulusan/tray', 'pages::kelulusan.tray')->name('kelulusan.tray');
    Route::livewire('kelulusan/memo/{memo}', 'pages::kelulusan.memo')->name('kelulusan.memo');

    // BUU — agreement review tray.
    Route::livewire('buu/tray', 'pages::buu.tray')->name('buu.tray');
});
