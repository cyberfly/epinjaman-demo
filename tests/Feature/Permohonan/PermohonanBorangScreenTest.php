<?php

use App\Enums\PermohonanStatus;
use App\Enums\SumberDana;
use App\Models\ChecklistItem;
use App\Models\KementerianPengawal;
use App\Models\Pemohon;
use App\Models\Permohonan;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;
use Livewire\Features\SupportLockedProperties\CannotUpdateLockedPropertyException;
use Livewire\Livewire;

test('pemohon boleh isi & simpan borang sebagai draf', function () {
    $pemohon = Pemohon::factory()->create();
    $user = User::factory()->pemohon($pemohon)->create();

    Livewire::actingAs($user)
        ->test('pages::pemohon.permohonan.borang')
        ->set('tajuk', 'Projek Baharu')
        ->set('jumlah_dipohon', '500000')
        ->set('tujuan', 'Pembangunan')
        ->set('tempoh_bulan', 36)
        ->call('saveDraft')
        ->assertHasNoErrors();

    $permohonan = $pemohon->permohonans()->first();
    expect($permohonan)->not->toBeNull()
        ->and($permohonan->status)->toBe(PermohonanStatus::Draf)
        ->and($permohonan->tajuk)->toBe('Projek Baharu');
});

test('pemohon boleh hantar borang lengkap dan status berubah', function () {
    $pemohon = Pemohon::factory()->create();
    $user = User::factory()->pemohon($pemohon)->create();
    $kementerian = KementerianPengawal::factory()->create();
    // Sumber Dana (DE) is set upstream, not on the form (ticket 14/16).
    $permohonan = Permohonan::factory()->for($pemohon)->de($kementerian)->status(PermohonanStatus::Draf)->create();

    Livewire::actingAs($user)
        ->test('pages::pemohon.permohonan.borang', ['permohonan' => $permohonan])
        ->set('tajuk', 'Projek Lengkap')
        ->set('jumlah_dipohon', '750000')
        ->set('tujuan', 'Naik taraf')
        ->set('tempoh_bulan', 48)
        ->call('submit')
        ->assertHasNoErrors()
        ->assertRedirect(route('permohonan.index'));

    expect($permohonan->refresh()->status)->toBe(PermohonanStatus::MenungguSemakanKelengkapan);
});

test('borang tidak boleh dihantar tanpa medan wajib', function () {
    $pemohon = Pemohon::factory()->create();
    $user = User::factory()->pemohon($pemohon)->create();

    Livewire::actingAs($user)
        ->test('pages::pemohon.permohonan.borang')
        ->set('tajuk', '')
        ->call('submit')
        ->assertHasErrors();
});

test('pemohon boleh muat naik dokumen sokongan mengikut senarai semak', function () {
    Storage::fake('local');
    $pemohon = Pemohon::factory()->create();
    $user = User::factory()->pemohon($pemohon)->create();
    $permohonan = Permohonan::factory()->for($pemohon)->status(PermohonanStatus::Draf)->create();
    $item = ChecklistItem::factory()->create();

    Livewire::actingAs($user)
        ->test('pages::pemohon.permohonan.borang', ['permohonan' => $permohonan])
        ->set('selectedChecklistItemId', $item->id)
        ->set('document', UploadedFile::fake()->create('sokongan.pdf', 100))
        ->call('uploadDocument')
        ->assertHasNoErrors();

    expect($permohonan->documents()->count())->toBe(1)
        ->and($permohonan->documents()->first()->checklist_item_id)->toBe($item->id);
});

test('pengguna organisasi lain ditolak daripada melihat borang', function () {
    $permohonan = Permohonan::factory()->status(PermohonanStatus::Draf)->create();
    $lain = User::factory()->pemohon()->create();

    Livewire::actingAs($lain)
        ->test('pages::pemohon.permohonan.borang', ['permohonan' => $permohonan])
        ->assertForbidden();
});

test('butang hantar tersembunyi untuk permohonan yang sudah dihantar', function () {
    $pemohon = Pemohon::factory()->create();
    $user = User::factory()->pemohon($pemohon)->create();
    $permohonan = Permohonan::factory()->for($pemohon)->status(PermohonanStatus::DalamSemakanSID)->create();

    Livewire::actingAs($user)
        ->test('pages::pemohon.permohonan.borang', ['permohonan' => $permohonan])
        ->assertOk()
        ->assertDontSeeHtml('data-test="submit-permohonan-button"')
        ->assertDontSeeHtml('data-test="save-draft-button"')
        ->assertSeeHtml('data-test="permohonan-dihantar-notis"');
});

test('butang hantar dipaparkan untuk draf', function () {
    $pemohon = Pemohon::factory()->create();
    $user = User::factory()->pemohon($pemohon)->create();
    $permohonan = Permohonan::factory()->for($pemohon)->status(PermohonanStatus::Draf)->create();

    Livewire::actingAs($user)
        ->test('pages::pemohon.permohonan.borang', ['permohonan' => $permohonan])
        ->assertSeeHtml('data-test="submit-permohonan-button"');
});

test('hantar semula permohonan bukan draf ditolak (403)', function () {
    $pemohon = Pemohon::factory()->create();
    $user = User::factory()->pemohon($pemohon)->create();
    $permohonan = Permohonan::factory()->for($pemohon)->status(PermohonanStatus::DalamSemakanSID)->create();

    Livewire::actingAs($user)
        ->test('pages::pemohon.permohonan.borang', ['permohonan' => $permohonan])
        ->call('submit')
        ->assertForbidden();
});

test('sumber dana dipaparkan sebagai medan baca-sahaja, bukan select boleh-pilih', function () {
    $pemohon = Pemohon::factory()->create();
    $user = User::factory()->pemohon($pemohon)->create();
    $permohonan = Permohonan::factory()->for($pemohon)->kwapbbDenganKementerian()->status(PermohonanStatus::Draf)->create();

    Livewire::actingAs($user)
        ->test('pages::pemohon.permohonan.borang', ['permohonan' => $permohonan])
        ->assertOk()
        ->assertSeeHtml('data-test="sumber-dana-readonly"')
        ->assertSeeHtml(SumberDana::KWAPBB->label())
        ->assertDontSeeHtml('wire:model.live="sumber_dana"');
});

test('sumber dana null dipaparkan sebagai sengkang', function () {
    $pemohon = Pemohon::factory()->create();
    $user = User::factory()->pemohon($pemohon)->create();
    $permohonan = Permohonan::factory()->for($pemohon)->status(PermohonanStatus::Draf)->create(['sumber_dana' => null]);

    Livewire::actingAs($user)
        ->test('pages::pemohon.permohonan.borang', ['permohonan' => $permohonan])
        ->assertOk()
        ->assertSeeHtml('data-test="sumber-dana-readonly"')
        ->assertSee('—');
});

test('pemohon tidak boleh menetapkan sumber dana daripada borang', function () {
    $pemohon = Pemohon::factory()->create();
    $user = User::factory()->pemohon($pemohon)->create();
    $permohonan = Permohonan::factory()->for($pemohon)->de()->status(PermohonanStatus::Draf)->create();

    $component = Livewire::actingAs($user)
        ->test('pages::pemohon.permohonan.borang', ['permohonan' => $permohonan]);

    expect(fn () => $component->set('sumber_dana', SumberDana::KWAPBB->value))
        ->toThrow(CannotUpdateLockedPropertyException::class);
});

test('simpan draf tidak mengubah sumber dana yang ditetapkan di hulu', function () {
    $pemohon = Pemohon::factory()->create();
    $user = User::factory()->pemohon($pemohon)->create();
    $permohonan = Permohonan::factory()->for($pemohon)->de()->status(PermohonanStatus::Draf)->create();

    Livewire::actingAs($user)
        ->test('pages::pemohon.permohonan.borang', ['permohonan' => $permohonan])
        ->set('tajuk', 'Tajuk Dikemas Kini')
        ->call('saveDraft')
        ->assertHasNoErrors();

    expect($permohonan->refresh())
        ->tajuk->toBe('Tajuk Dikemas Kini')
        ->sumber_dana->toBe(SumberDana::DE);
});

test('kementerian pengawal dipaparkan baca-sahaja daripada nilai tersimpan', function () {
    $kementerian = KementerianPengawal::factory()->create(['nama' => 'Kementerian Contoh']);
    $pemohon = Pemohon::factory()->create();
    $user = User::factory()->pemohon($pemohon)->create();
    $permohonan = Permohonan::factory()->for($pemohon)->de($kementerian)->status(PermohonanStatus::Draf)->create();

    Livewire::actingAs($user)
        ->test('pages::pemohon.permohonan.borang', ['permohonan' => $permohonan])
        ->assertOk()
        ->assertSeeHtml('data-test="kementerian-pengawal-readonly"')
        ->assertSee('Kementerian Contoh')
        ->assertDontSeeHtml('wire:model.live="ada_kementerian_pengawal"')
        ->assertDontSeeHtml('wire:model="kementerian_pengawal_id"');
});

test('pemohon tidak boleh menetapkan kementerian pengawal daripada borang', function () {
    $pemohon = Pemohon::factory()->create();
    $user = User::factory()->pemohon($pemohon)->create();
    $permohonan = Permohonan::factory()->for($pemohon)->de()->status(PermohonanStatus::Draf)->create();

    $component = Livewire::actingAs($user)
        ->test('pages::pemohon.permohonan.borang', ['permohonan' => $permohonan]);

    expect(fn () => $component->set('kementerian_pengawal_id', 999))
        ->toThrow(CannotUpdateLockedPropertyException::class);
});

test('borang baharu mewarisi paparan sumber dana daripada organisasi', function () {
    $kementerian = KementerianPengawal::factory()->create();
    $pemohon = Pemohon::factory()->kwapbbDenganKementerian($kementerian)->create();
    $user = User::factory()->pemohon($pemohon)->create();

    Livewire::actingAs($user)
        ->test('pages::pemohon.permohonan.borang')
        ->assertOk()
        ->assertSeeHtml(SumberDana::KWAPBB->label());
});

test('draf baharu mewarisi routing DE dan boleh dihantar hujung-ke-hujung', function () {
    $kementerian = KementerianPengawal::factory()->create();
    $pemohon = Pemohon::factory()->de($kementerian)->create();
    $user = User::factory()->pemohon($pemohon)->create();

    Livewire::actingAs($user)
        ->test('pages::pemohon.permohonan.borang')
        ->set('tajuk', 'Projek E2E')
        ->set('jumlah_dipohon', '600000')
        ->set('tujuan', 'Pembinaan')
        ->set('tempoh_bulan', 24)
        ->call('submit')
        ->assertHasNoErrors()
        ->assertRedirect(route('permohonan.index'));

    $permohonan = $pemohon->permohonans()->first();
    expect($permohonan->sumber_dana)->toBe(SumberDana::DE)
        ->and($permohonan->kementerian_pengawal_id)->toBe($kementerian->id)
        ->and($permohonan->status)->toBe(PermohonanStatus::MenungguSemakanKelengkapan);
});

test('draf baharu KWAPBB-tanpa-kementerian mewarisi routing dan terus ke SID', function () {
    Notification::fake();
    $pemohon = Pemohon::factory()->kwapbbTanpaKementerian()->create();
    $user = User::factory()->pemohon($pemohon)->create();

    Livewire::actingAs($user)
        ->test('pages::pemohon.permohonan.borang')
        ->set('tajuk', 'Projek Skip')
        ->set('jumlah_dipohon', '400000')
        ->set('tujuan', 'Operasi')
        ->set('tempoh_bulan', 12)
        ->call('submit')
        ->assertHasNoErrors()
        ->assertRedirect(route('permohonan.index'));

    expect($pemohon->permohonans()->first()->status)->toBe(PermohonanStatus::DalamSemakanSID);
});
