<?php

use App\Enums\UserRole;
use App\Models\Pemohon;
use App\Models\User;
use Illuminate\Support\Facades\Notification;
use Livewire\Livewire;

test('pegawai SID boleh melihat skrin provisioning', function () {
    $sid = User::factory()->role(UserRole::PSID)->create();

    $this->actingAs($sid)->get(route('sid.organisasi-pemohon'))->assertOk();
});

test('admin boleh melihat skrin provisioning', function () {
    $admin = User::factory()->role(UserRole::Admin)->create();

    $this->actingAs($admin)->get(route('sid.organisasi-pemohon'))->assertOk();
});

test('pengguna bukan SID/Admin ditolak daripada skrin provisioning', function () {
    $pemohon = User::factory()->pemohon()->create();

    $this->actingAs($pemohon)->get(route('sid.organisasi-pemohon'))->assertForbidden();
});

test('SID boleh cipta organisasi dan tambah pengguna melalui skrin', function () {
    Notification::fake();

    $sid = User::factory()->role(UserRole::PSID)->create();

    Livewire::actingAs($sid)
        ->test('pages::sid.organisasi-pemohon')
        ->set('namaOrganisasi', 'Agensi XYZ')
        ->call('ciptaOrganisasi')
        ->assertHasNoErrors();

    $pemohon = Pemohon::firstWhere('nama', 'Agensi XYZ');
    expect($pemohon)->not->toBeNull();

    Livewire::actingAs($sid)
        ->test('pages::sid.organisasi-pemohon')
        ->set('selectedPemohonId', $pemohon->id)
        ->set('namaPengguna', 'Siti')
        ->set('emailPengguna', 'siti@xyz.test')
        ->call('tambahPengguna')
        ->assertHasNoErrors();

    expect($pemohon->users()->count())->toBe(1);
});
