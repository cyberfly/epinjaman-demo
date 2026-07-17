<?php

use App\Enums\PermohonanStatus;
use App\Enums\UserRole;
use App\Models\Permohonan;
use App\Models\User;
use Livewire\Livewire;

test('PSID merekod rundingan & menyediakan memo melalui skrin', function () {
    $psid = User::factory()->role(UserRole::PSID)->create();
    $permohonan = Permohonan::factory()->status(PermohonanStatus::DalamRundingan)->create();

    Livewire::actingAs($psid)
        ->test('pages::sid.rundingan', ['permohonan' => $permohonan])
        ->set('terma', 'Kadar 4%')
        ->call('rekod')
        ->assertHasNoErrors()
        ->set('termaUtama', 'Terma utama pinjaman')
        ->call('sediakanMemo')
        ->assertHasNoErrors()
        ->assertRedirect(route('sid.tray'));

    expect($permohonan->fresh()->status)->toBe(PermohonanStatus::DalamKelulusan)
        ->and($permohonan->rundingans()->count())->toBe(1)
        ->and($permohonan->memo)->not->toBeNull();
});

test('peranan bukan SID ditolak daripada skrin rundingan', function () {
    $permohonan = Permohonan::factory()->status(PermohonanStatus::DalamRundingan)->create();
    $kp = User::factory()->role(UserRole::KementerianPengawal)->create();

    Livewire::actingAs($kp)
        ->test('pages::sid.rundingan', ['permohonan' => $permohonan])
        ->assertForbidden();
});
