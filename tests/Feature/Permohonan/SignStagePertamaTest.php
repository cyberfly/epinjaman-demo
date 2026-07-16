<?php

use App\Actions\Permohonan\SignStagePertama;
use App\Enums\PermohonanStatus;
use App\Enums\SignatureStage;
use App\Enums\UserRole;
use App\Models\ChecklistItem;
use App\Models\KementerianPengawal;
use App\Models\Pemohon;
use App\Models\Permohonan;
use App\Models\User;
use App\Notifications\TemplatedNotification;
use Illuminate\Support\Facades\Notification;
use Illuminate\Validation\ValidationException;

test('tandatangan Peringkat 1 direkod, dihantar ke Kementerian Pengawal & notifikasi dicetus', function () {
    Notification::fake();

    $kementerian = KementerianPengawal::factory()->create();
    $kpUser = User::factory()->role(UserRole::KementerianPengawal)->create(['kementerian_pengawal_id' => $kementerian->id]);
    $pemohon = Pemohon::factory()->create();
    $user = User::factory()->pemohon($pemohon)->create();
    $permohonan = Permohonan::factory()
        ->for($pemohon)
        ->kwapbbDenganKementerian($kementerian)
        ->status(PermohonanStatus::MenungguSemakanKelengkapan)
        ->create();

    $signature = app(SignStagePertama::class)->handle($permohonan, $user);

    expect($signature->stage)->toBe(SignatureStage::Peringkat1)
        ->and($signature->role)->toBe(UserRole::Pemohon)
        ->and($signature->statements)->toBe(['Disediakan', 'Disahkan'])
        ->and($permohonan->fresh()->status)->toBe(PermohonanStatus::MenungguTandatanganKementerianPengawal)
        ->and($permohonan->fresh()->dihantar_ke_kementerian_pada)->not->toBeNull();

    Notification::assertSentTo($kpUser, TemplatedNotification::class);
});

test('tidak boleh tandatangan Peringkat 1 apabila dokumen tidak lengkap', function () {
    ChecklistItem::factory()->create(); // active checklist item, no document uploaded
    $pemohon = Pemohon::factory()->create();
    $user = User::factory()->pemohon($pemohon)->create();
    $permohonan = Permohonan::factory()
        ->for($pemohon)
        ->kwapbbDenganKementerian()
        ->status(PermohonanStatus::MenungguSemakanKelengkapan)
        ->create();

    expect(fn () => app(SignStagePertama::class)->handle($permohonan, $user))
        ->toThrow(ValidationException::class);

    expect($permohonan->fresh()->status)->toBe(PermohonanStatus::MenungguSemakanKelengkapan);
});

test('tidak boleh tandatangan Peringkat 1 pada status yang salah', function () {
    $pemohon = Pemohon::factory()->create();
    $user = User::factory()->pemohon($pemohon)->create();
    $permohonan = Permohonan::factory()->for($pemohon)->status(PermohonanStatus::Draf)->create();

    expect(fn () => app(SignStagePertama::class)->handle($permohonan, $user))
        ->toThrow(DomainException::class);
});
