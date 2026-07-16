<?php

use App\Enums\PermohonanStatus;
use App\Models\ChecklistItem;
use App\Models\KementerianPengawal;
use App\Models\Pemohon;
use App\Models\Permohonan;
use App\Models\User;
use Illuminate\Support\Facades\Notification;
use Livewire\Livewire;

test('pemohon boleh turunkan tandatangan pada borang lengkap melalui skrin', function () {
    Notification::fake();
    $kementerian = KementerianPengawal::factory()->create();
    $pemohon = Pemohon::factory()->create();
    $user = User::factory()->pemohon($pemohon)->create();
    $permohonan = Permohonan::factory()
        ->for($pemohon)
        ->kwapbbDenganKementerian($kementerian)
        ->status(PermohonanStatus::MenungguSemakanKelengkapan)
        ->create();

    Livewire::actingAs($user)
        ->test('pages::pemohon.permohonan.tandatangan-p1', ['permohonan' => $permohonan])
        ->assertSet('isComplete', true)
        ->call('sign')
        ->assertHasNoErrors()
        ->assertRedirect(route('permohonan.index'));

    expect($permohonan->fresh()->status)->toBe(PermohonanStatus::MenungguTandatanganKementerianPengawal);
});

test('borang tidak lengkap dipaparkan dengan penunjuk & boleh dipulangkan ke draf', function () {
    ChecklistItem::factory()->create(['label' => 'Dokumen wajib']);
    $pemohon = Pemohon::factory()->create();
    $user = User::factory()->pemohon($pemohon)->create();
    $permohonan = Permohonan::factory()
        ->for($pemohon)
        ->kwapbbDenganKementerian()
        ->status(PermohonanStatus::MenungguSemakanKelengkapan)
        ->create();

    Livewire::actingAs($user)
        ->test('pages::pemohon.permohonan.tandatangan-p1', ['permohonan' => $permohonan])
        ->assertSet('isComplete', false)
        ->assertSee('Dokumen wajib')
        ->call('returnToDraft')
        ->assertRedirect(route('permohonan.borang', $permohonan));

    expect($permohonan->fresh()->status)->toBe(PermohonanStatus::Draf);
});

test('pengguna organisasi lain ditolak daripada skrin tandatangan', function () {
    $permohonan = Permohonan::factory()->status(PermohonanStatus::MenungguSemakanKelengkapan)->create();
    $lain = User::factory()->pemohon()->create();

    Livewire::actingAs($lain)
        ->test('pages::pemohon.permohonan.tandatangan-p1', ['permohonan' => $permohonan])
        ->assertForbidden();
});
