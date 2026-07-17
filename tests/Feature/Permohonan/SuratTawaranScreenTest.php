<?php

use App\Enums\PermohonanStatus;
use App\Models\Pemohon;
use App\Models\Permohonan;
use App\Models\SuratTawaran;
use App\Models\User;
use Livewire\Livewire;

test('Pemohon menyemak & menandatangani Surat Akuan melalui skrin', function () {
    $pemohon = Pemohon::factory()->create();
    $user = User::factory()->pemohon($pemohon)->create();
    $permohonan = Permohonan::factory()->for($pemohon)->status(PermohonanStatus::Ditawarkan)->create();
    SuratTawaran::factory()->for($permohonan)->create();

    Livewire::actingAs($user)
        ->test('pages::pemohon.permohonan.surat-tawaran', ['permohonan' => $permohonan])
        ->call('tandatangan')
        ->assertHasNoErrors()
        ->assertRedirect(route('permohonan.index'));

    expect($permohonan->fresh()->status)->toBe(PermohonanStatus::DalamPerjanjian);
});

test('organisasi lain ditolak daripada skrin surat tawaran', function () {
    $permohonan = Permohonan::factory()->status(PermohonanStatus::Ditawarkan)->create();
    SuratTawaran::factory()->for($permohonan)->create();
    $lain = User::factory()->pemohon()->create();

    Livewire::actingAs($lain)
        ->test('pages::pemohon.permohonan.surat-tawaran', ['permohonan' => $permohonan])
        ->assertForbidden();
});
