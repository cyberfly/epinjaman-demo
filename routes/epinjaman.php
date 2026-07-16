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
});
