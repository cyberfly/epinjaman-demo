<?php

use App\Enums\PermohonanStatus;
use App\Enums\UserRole;
use App\Models\KementerianPengawal;
use App\Models\Permohonan;
use App\Models\User;
use Illuminate\Support\Facades\Notification;
use Livewire\Livewire;

test('Kementerian Pengawal hanya melihat permohonan kementeriannya dalam tray', function () {
    $k1 = KementerianPengawal::factory()->create();
    $k2 = KementerianPengawal::factory()->create();
    $kpUser = User::factory()->role(UserRole::KementerianPengawal)->create(['kementerian_pengawal_id' => $k1->id]);

    Permohonan::factory()->kwapbbDenganKementerian($k1)->status(PermohonanStatus::MenungguTandatanganKementerianPengawal)->create(['tajuk' => 'Projek Milik Saya']);
    Permohonan::factory()->kwapbbDenganKementerian($k2)->status(PermohonanStatus::MenungguTandatanganKementerianPengawal)->create(['tajuk' => 'Projek Orang Lain']);

    Livewire::actingAs($kpUser)
        ->test('pages::kementerian.tray')
        ->assertSee('Projek Milik Saya')
        ->assertDontSee('Projek Orang Lain');
});

test('Kementerian Pengawal boleh turunkan Tandatangan Peringkat 2 melalui skrin', function () {
    Notification::fake();
    $kementerian = KementerianPengawal::factory()->create();
    $kpUser = User::factory()->role(UserRole::KementerianPengawal)->create(['kementerian_pengawal_id' => $kementerian->id]);
    $permohonan = Permohonan::factory()->kwapbbDenganKementerian($kementerian)->status(PermohonanStatus::MenungguTandatanganKementerianPengawal)->create();

    Livewire::actingAs($kpUser)
        ->test('pages::kementerian.tandatangan-p2', ['permohonan' => $permohonan])
        ->call('sign')
        ->assertHasNoErrors()
        ->assertRedirect(route('kementerian.tray'));

    expect($permohonan->fresh()->status)->toBe(PermohonanStatus::DalamSemakanSID);
});

test('peranan bukan Kementerian Pengawal ditolak daripada tray', function () {
    $psid = User::factory()->role(UserRole::PSID)->create();

    $this->actingAs($psid)->get(route('kementerian.tray'))->assertForbidden();
});

test('Kementerian Pengawal lain ditolak daripada skrin tandatangan', function () {
    $k1 = KementerianPengawal::factory()->create();
    $k2 = KementerianPengawal::factory()->create();
    $permohonan = Permohonan::factory()->kwapbbDenganKementerian($k1)->status(PermohonanStatus::MenungguTandatanganKementerianPengawal)->create();
    $kpLain = User::factory()->role(UserRole::KementerianPengawal)->create(['kementerian_pengawal_id' => $k2->id]);

    Livewire::actingAs($kpLain)
        ->test('pages::kementerian.tandatangan-p2', ['permohonan' => $permohonan])
        ->assertForbidden();
});
