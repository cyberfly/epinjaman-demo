<?php

use App\Enums\PermohonanStatus;
use App\Enums\UserRole;
use App\Models\Pemohon;
use App\Models\Perjanjian;
use App\Models\Permohonan;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;

test('Pemohon memuat naik draf perjanjian melalui skrin', function () {
    Storage::fake('local');
    $pemohon = Pemohon::factory()->create();
    $user = User::factory()->pemohon($pemohon)->create();
    $permohonan = Permohonan::factory()->for($pemohon)->status(PermohonanStatus::DalamPerjanjian)->create();

    Livewire::actingAs($user)
        ->test('pages::pemohon.permohonan.perjanjian', ['permohonan' => $permohonan])
        ->set('draf', UploadedFile::fake()->create('perjanjian.pdf', 200))
        ->call('muatNaik')
        ->assertHasNoErrors();

    expect($permohonan->fresh()->perjanjian)->not->toBeNull();
});

test('BUU mengesahkan perjanjian melalui skrin', function () {
    $permohonan = Permohonan::factory()->status(PermohonanStatus::DalamPerjanjian)->create();
    Perjanjian::factory()->for($permohonan)->create();
    $buu = User::factory()->role(UserRole::BUU)->create();

    Livewire::actingAs($buu)
        ->test('pages::perjanjian.semakan', ['permohonan' => $permohonan])
        ->call('sahkan')
        ->assertHasNoErrors();

    expect($permohonan->fresh()->perjanjian->disahkan_buu)->toBeTrue();
});

test('peranan bukan SID/BUU ditolak daripada skrin semakan perjanjian', function () {
    $permohonan = Permohonan::factory()->status(PermohonanStatus::DalamPerjanjian)->create();
    $pemohon = User::factory()->pemohon()->create();

    Livewire::actingAs($pemohon)
        ->test('pages::perjanjian.semakan', ['permohonan' => $permohonan])
        ->assertForbidden();
});
