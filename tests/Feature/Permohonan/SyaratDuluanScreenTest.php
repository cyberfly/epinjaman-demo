<?php

use App\Enums\PermohonanStatus;
use App\Enums\SyaratDuluanJenis;
use App\Enums\UserRole;
use App\Models\Pemohon;
use App\Models\Permohonan;
use App\Models\SyaratDuluan;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;

test('Peminjam memuat naik dokumen CP melalui skrin', function () {
    Storage::fake('local');
    $pemohon = Pemohon::factory()->peminjam()->create();
    $user = User::factory()->pemohon($pemohon)->create();
    $permohonan = Permohonan::factory()->for($pemohon)->status(PermohonanStatus::DalamPenyediaanCP)->create();

    Livewire::actingAs($user)
        ->test('pages::pemohon.permohonan.syarat-duluan', ['permohonan' => $permohonan])
        ->set('jenis', SyaratDuluanJenis::Cagaran->value)
        ->set('dokumen', UploadedFile::fake()->create('cagaran.pdf', 100))
        ->call('muatNaik')
        ->assertHasNoErrors();

    expect($permohonan->syaratDuluans()->count())->toBe(1);
});

test('PSID mengesahkan CP, CS & mengunci LENGKAP melalui skrin', function () {
    $psid = User::factory()->role(UserRole::PSID)->create();
    $permohonan = Permohonan::factory()->status(PermohonanStatus::DalamPenyediaanCP)->create();

    foreach (SyaratDuluanJenis::cases() as $jenis) {
        SyaratDuluan::factory()->for($permohonan)->jenis($jenis)->disahkan()->create();
    }

    Livewire::actingAs($psid)
        ->test('pages::sid.syarat-duluan', ['permohonan' => $permohonan])
        ->call('sahkanCS')
        ->call('kunci')
        ->assertRedirect(route('sid.tray'));

    expect($permohonan->fresh()->status)->toBe(PermohonanStatus::Lengkap)
        ->and($permohonan->fresh()->cs_disahkan)->toBeTrue();
});

test('bukan PSID ditolak daripada skrin semakan CP', function () {
    $permohonan = Permohonan::factory()->status(PermohonanStatus::DalamPenyediaanCP)->create();
    $pemohon = User::factory()->pemohon()->create();

    Livewire::actingAs($pemohon)
        ->test('pages::sid.syarat-duluan', ['permohonan' => $permohonan])
        ->assertForbidden();
});

test('SID boleh melihat skrin sejarah; pemohon ditolak', function () {
    $permohonan = Permohonan::factory()->status(PermohonanStatus::Lengkap)->create();
    $psid = User::factory()->role(UserRole::PSID)->create();
    $pemohon = User::factory()->pemohon()->create();

    Livewire::actingAs($psid)
        ->test('pages::sid.sejarah', ['permohonan' => $permohonan])
        ->assertOk();

    Livewire::actingAs($pemohon)
        ->test('pages::sid.sejarah', ['permohonan' => $permohonan])
        ->assertForbidden();
});
