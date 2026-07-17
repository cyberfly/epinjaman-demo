<?php

use App\Actions\Kuiri\BalasKuiri;
use App\Actions\Kuiri\CetusKuiri;
use App\Actions\Kuiri\TandakanKuiri;
use App\Actions\Kuiri\TeruskanKeRundinganDariKuiri;
use App\Enums\KuiriStatus;
use App\Enums\PermohonanStatus;
use App\Enums\UserRole;
use App\Models\Kuiri;
use App\Models\Pemohon;
use App\Models\Permohonan;
use App\Models\User;
use App\Notifications\TemplatedNotification;
use Illuminate\Support\Facades\Notification;
use Illuminate\Validation\ValidationException;

test('PSID mencetus kuiri, notifikasi dihantar & status jadi Dalam Kuiri', function () {
    Notification::fake();
    $pemohon = Pemohon::factory()->create();
    User::factory()->pemohon($pemohon)->create();
    $psid = User::factory()->role(UserRole::PSID)->create();
    $permohonan = Permohonan::factory()->for($pemohon)->status(PermohonanStatus::DalamSemakanSID)->create();

    $kuiri = app(CetusKuiri::class)->handle($permohonan, $psid, 'Dokumen tidak lengkap', 'Sila lengkapkan penyata kewangan');

    expect($kuiri->status)->toBe(KuiriStatus::Terbuka)
        ->and($kuiri->tarikh_akhir)->not->toBeNull()
        ->and($permohonan->fresh()->status)->toBe(PermohonanStatus::DalamKuiri);

    Notification::assertSentTo($pemohon->users->first(), TemplatedNotification::class);
});

test('boleh ada berbilang kuiri aktif serentak', function () {
    $psid = User::factory()->role(UserRole::PSID)->create();
    $permohonan = Permohonan::factory()->status(PermohonanStatus::DalamSemakanSID)->create();

    app(CetusKuiri::class)->handle($permohonan, $psid, 'Isu 1', 'Sebab 1');
    app(CetusKuiri::class)->handle($permohonan->fresh(), $psid, 'Isu 2', 'Sebab 2');

    expect($permohonan->kuiris()->count())->toBe(2);
});

test('Pemohon boleh balas kuiri dengan mesej', function () {
    $pemohon = Pemohon::factory()->create();
    $user = User::factory()->pemohon($pemohon)->create();
    $permohonan = Permohonan::factory()->for($pemohon)->status(PermohonanStatus::DalamKuiri)->create();
    $kuiri = $permohonan->kuiris()->save(Kuiri::factory()->make());

    $reply = app(BalasKuiri::class)->handle($kuiri, $user, 'Dokumen dikemas kini');

    expect($reply->mesej)->toBe('Dokumen dikemas kini')
        ->and($reply->role)->toBe(UserRole::Pemohon)
        ->and($kuiri->replies()->count())->toBe(1);
});

test('balasan mesti ada mesej atau lampiran', function () {
    $user = User::factory()->pemohon()->create();
    $kuiri = Kuiri::factory()->create();

    expect(fn () => app(BalasKuiri::class)->handle($kuiri, $user, null, null, null))
        ->toThrow(ValidationException::class);
});

test('kitaran penuh: cetus -> balas -> tidak berpuas hati -> berpuas hati -> teruskan', function () {
    $psid = User::factory()->role(UserRole::PSID)->create();
    $pemohon = Pemohon::factory()->create();
    $user = User::factory()->pemohon($pemohon)->create();
    $permohonan = Permohonan::factory()->for($pemohon)->status(PermohonanStatus::DalamSemakanSID)->create();

    $kuiri = app(CetusKuiri::class)->handle($permohonan, $psid, 'Isu', 'Sebab');
    app(BalasKuiri::class)->handle($kuiri, $user, 'Percubaan pertama');

    // Not satisfied — stays open for the next round
    app(TandakanKuiri::class)->tidakBerpuasHati($kuiri);
    expect($kuiri->fresh()->status)->toBe(KuiriStatus::TidakBerpuasHati);

    // Cannot proceed while a Kuiri is unsatisfied
    expect(fn () => app(TeruskanKeRundinganDariKuiri::class)->handle($permohonan->fresh()))
        ->toThrow(DomainException::class);

    // Reply again, then satisfied
    app(BalasKuiri::class)->handle($kuiri->fresh(), $user, 'Percubaan kedua');
    app(TandakanKuiri::class)->berpuasHati($kuiri->fresh());

    app(TeruskanKeRundinganDariKuiri::class)->handle($permohonan->fresh());

    expect($permohonan->fresh()->status)->toBe(PermohonanStatus::DalamRundingan);
});

test('tidak boleh teruskan jika ada kuiri belum berpuas hati', function () {
    $psid = User::factory()->role(UserRole::PSID)->create();
    $permohonan = Permohonan::factory()->status(PermohonanStatus::DalamSemakanSID)->create();

    app(CetusKuiri::class)->handle($permohonan, $psid, 'A', 'a');
    $k2 = app(CetusKuiri::class)->handle($permohonan->fresh(), $psid, 'B', 'b');
    app(TandakanKuiri::class)->berpuasHati($k2);

    expect(fn () => app(TeruskanKeRundinganDariKuiri::class)->handle($permohonan->fresh()))
        ->toThrow(DomainException::class);
});

test('tidak boleh balas kuiri yang telah berpuas hati', function () {
    $user = User::factory()->pemohon()->create();
    $kuiri = Kuiri::factory()->berpuasHati()->create();

    expect(fn () => app(BalasKuiri::class)->handle($kuiri, $user, 'lewat'))
        ->toThrow(DomainException::class);
});
