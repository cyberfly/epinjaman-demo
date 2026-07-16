<?php

use App\Enums\PermohonanStatus;
use App\Enums\UserRole;
use App\Models\Permohonan;
use App\Models\User;
use Livewire\Livewire;

test('PSID melihat permohonan Dalam Semakan SID dalam tray', function () {
    $psid = User::factory()->role(UserRole::PSID)->create();
    $permohonan = Permohonan::factory()->status(PermohonanStatus::DalamSemakanSID)->create(['no_rujukan' => 'SID/2026/0007']);

    Livewire::actingAs($psid)
        ->test('pages::sid.tray')
        ->assertSee('SID/2026/0007');
});

test('laluan langkau KWAPBB-tanpa-Kementerian muncul dalam tray PSID', function () {
    $psid = User::factory()->role(UserRole::PSID)->create();
    $permohonan = Permohonan::factory()
        ->kwapbbTanpaKementerian()
        ->status(PermohonanStatus::DalamSemakanSID)
        ->create(['no_rujukan' => 'SID/2026/0009']);

    Livewire::actingAs($psid)
        ->test('pages::sid.tray')
        ->assertSee('SID/2026/0009');
});

test('peranan bukan PSID ditolak daripada tray SID', function () {
    $pemohon = User::factory()->pemohon()->create();

    $this->actingAs($pemohon)->get(route('sid.tray'))->assertForbidden();
});
