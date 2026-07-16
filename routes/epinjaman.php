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
});
