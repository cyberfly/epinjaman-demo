<?php

use App\Enums\ApprovalDecision;
use App\Enums\PermohonanStatus;
use App\Enums\UserRole;
use App\Models\Memo;
use App\Models\Permohonan;
use App\Models\User;
use Livewire\Livewire;

test('pegawai endorse memo melalui skrin', function () {
    $permohonan = Permohonan::factory()->status(PermohonanStatus::DalamKelulusan)->create();
    $memo = Memo::factory()->for($permohonan)->create(['peringkat' => UserRole::PSID, 'keputusan' => null]);
    $psid = User::factory()->role(UserRole::PSID)->create();

    Livewire::actingAs($psid)
        ->test('pages::kelulusan.memo', ['memo' => $memo])
        ->call('endorse')
        ->assertRedirect(route('kelulusan.tray'));

    expect($memo->fresh()->peringkat)->toBe(UserRole::KSID);
});

test('YB MK meluluskan melalui skrin', function () {
    $permohonan = Permohonan::factory()->status(PermohonanStatus::DalamKelulusan)->create();
    $memo = Memo::factory()->for($permohonan)->create(['peringkat' => UserRole::YBMK, 'keputusan' => null]);
    $ybmk = User::factory()->role(UserRole::YBMK)->create();

    Livewire::actingAs($ybmk)
        ->test('pages::kelulusan.memo', ['memo' => $memo])
        ->call('lulus')
        ->assertRedirect(route('kelulusan.tray'));

    expect($memo->fresh()->keputusan)->toBe(ApprovalDecision::Lulus);
});

test('KSID memulangkan memo melalui skrin dengan sebab', function () {
    $permohonan = Permohonan::factory()->status(PermohonanStatus::DalamKelulusan)->create();
    $memo = Memo::factory()->for($permohonan)->create(['peringkat' => UserRole::KSID, 'keputusan' => null]);
    $ksid = User::factory()->role(UserRole::KSID)->create();

    Livewire::actingAs($ksid)
        ->test('pages::kelulusan.memo', ['memo' => $memo])
        ->set('sebab', 'Sila betulkan angka')
        ->call('pulangkan')
        ->assertRedirect(route('kelulusan.tray'));

    expect($memo->fresh()->peringkat)->toBe(UserRole::PSID);
});

test('pemohon ditolak daripada skrin memo', function () {
    $permohonan = Permohonan::factory()->status(PermohonanStatus::DalamKelulusan)->create();
    $memo = Memo::factory()->for($permohonan)->create(['peringkat' => UserRole::PSID, 'keputusan' => null]);
    $pemohon = User::factory()->pemohon()->create();

    Livewire::actingAs($pemohon)
        ->test('pages::kelulusan.memo', ['memo' => $memo])
        ->assertForbidden();
});
