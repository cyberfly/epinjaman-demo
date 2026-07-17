<?php

use App\Enums\PemohonStatus;
use App\Enums\PermohonanStatus;
use App\Enums\UserRole;
use App\Models\Pemohon;
use App\Models\Perjanjian;
use App\Models\Permohonan;
use App\Models\User;
use Livewire\Livewire;

test('SID merekod tandatangan manual melalui skrin', function () {
    $sid = User::factory()->role(UserRole::PSID)->create();
    $pemohon = Pemohon::factory()->create();
    $permohonan = Permohonan::factory()->for($pemohon)->status(PermohonanStatus::DalamPerjanjian)->create();
    Perjanjian::factory()->for($permohonan)->disahkan()->create();

    Livewire::actingAs($sid)
        ->test('pages::perjanjian.tandatangan-manual', ['permohonan' => $permohonan])
        ->set('tarikhTandatangan', '2026-07-17')
        ->call('rekodTandatangan')
        ->assertHasNoErrors();

    expect($pemohon->fresh()->status)->toBe(PemohonStatus::Peminjam);
});

test('SID merekod penyeteman melalui skrin', function () {
    $sid = User::factory()->role(UserRole::PSID)->create();
    $permohonan = Permohonan::factory()->status(PermohonanStatus::DalamPerjanjian)->create();
    Perjanjian::factory()->for($permohonan)->disahkan()->create(['tandatangan_manual_selesai' => true]);

    Livewire::actingAs($sid)
        ->test('pages::perjanjian.tandatangan-manual', ['permohonan' => $permohonan])
        ->set('tarikhPenyeteman', '2026-07-20')
        ->call('rekodPenyeteman')
        ->assertHasNoErrors()
        ->assertRedirect(route('sid.tray'));

    expect($permohonan->fresh()->status)->toBe(PermohonanStatus::DalamPenyediaanCP);
});

test('peranan tanpa kaitan ditolak daripada skrin tandatangan manual', function () {
    $permohonan = Permohonan::factory()->status(PermohonanStatus::DalamPerjanjian)->create();
    $lain = User::factory()->pemohon()->create();

    Livewire::actingAs($lain)
        ->test('pages::perjanjian.tandatangan-manual', ['permohonan' => $permohonan])
        ->assertForbidden();
});
