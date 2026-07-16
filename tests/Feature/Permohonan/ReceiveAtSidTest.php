<?php

use App\Actions\Permohonan\ReceiveAtSid;
use App\Actions\Permohonan\SubmitPermohonan;
use App\Enums\PermohonanStatus;
use App\Models\Pemohon;
use App\Models\Permohonan;
use App\Models\User;
use App\Notifications\TemplatedNotification;
use Illuminate\Support\Facades\Notification;

test('penerimaan di SID menjana No. Rujukan, e-mel & status semakan', function () {
    Notification::fake();
    $pemohon = Pemohon::factory()->create();
    User::factory()->pemohon($pemohon)->create();
    $permohonan = Permohonan::factory()->for($pemohon)->status(PermohonanStatus::MenungguTandatanganKementerianPengawal)->create();

    app(ReceiveAtSid::class)->handle($permohonan);

    $permohonan->refresh();
    expect($permohonan->no_rujukan)->toMatch('/^SID\/\d{4}\/\d{4}$/')
        ->and($permohonan->status)->toBe(PermohonanStatus::DalamSemakanSID)
        ->and($permohonan->diterima_sid_pada)->not->toBeNull();

    Notification::assertSentTo($pemohon->users->first(), TemplatedNotification::class);
});

test('laluan langkau KWAPBB-tanpa-Kementerian menjana No. Rujukan semasa dihantar', function () {
    $permohonan = Permohonan::factory()->kwapbbTanpaKementerian()->status(PermohonanStatus::Draf)->create();

    app(SubmitPermohonan::class)->handle($permohonan);

    $permohonan->refresh();
    expect($permohonan->status)->toBe(PermohonanStatus::DalamSemakanSID)
        ->and($permohonan->no_rujukan)->not->toBeNull();
});
