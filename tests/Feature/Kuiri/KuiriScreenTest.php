<?php

use App\Enums\PermohonanStatus;
use App\Enums\UserRole;
use App\Models\Kuiri;
use App\Models\Pemohon;
use App\Models\Permohonan;
use App\Models\User;
use Illuminate\Support\Facades\Notification;
use Livewire\Livewire;

test('PSID mencetus kuiri melalui skrin', function () {
    Notification::fake();
    $pemohon = Pemohon::factory()->create();
    User::factory()->pemohon($pemohon)->create();
    $psid = User::factory()->role(UserRole::PSID)->create();
    $permohonan = Permohonan::factory()->for($pemohon)->status(PermohonanStatus::DalamSemakanSID)->create();

    Livewire::actingAs($psid)
        ->test('pages::sid.kuiri', ['permohonan' => $permohonan])
        ->set('tajuk', 'Dokumen tidak lengkap')
        ->set('sebab', 'Sila lengkapkan')
        ->call('cetus')
        ->assertHasNoErrors();

    expect($permohonan->kuiris()->count())->toBe(1);
});

test('PSID tandakan berpuas hati & teruskan ke Rundingan melalui skrin', function () {
    $psid = User::factory()->role(UserRole::PSID)->create();
    $permohonan = Permohonan::factory()->status(PermohonanStatus::DalamKuiri)->create();
    $kuiri = $permohonan->kuiris()->save(Kuiri::factory()->make());

    Livewire::actingAs($psid)
        ->test('pages::sid.kuiri', ['permohonan' => $permohonan])
        ->call('berpuasHati', $kuiri->id)
        ->call('teruskan')
        ->assertRedirect(route('sid.tray'));

    expect($permohonan->fresh()->status)->toBe(PermohonanStatus::DalamRundingan);
});

test('Pemohon membalas kuiri melalui skrin', function () {
    $pemohon = Pemohon::factory()->create();
    $user = User::factory()->pemohon($pemohon)->create();
    $permohonan = Permohonan::factory()->for($pemohon)->status(PermohonanStatus::DalamKuiri)->create();
    $kuiri = $permohonan->kuiris()->save(Kuiri::factory()->make());

    Livewire::actingAs($user)
        ->test('pages::kuiri.balas', ['kuiri' => $kuiri])
        ->set('mesej', 'Sudah dikemas kini')
        ->call('balas')
        ->assertHasNoErrors();

    expect($kuiri->replies()->count())->toBe(1);
});

test('peranan bukan PSID ditolak daripada skrin kuiri', function () {
    $permohonan = Permohonan::factory()->status(PermohonanStatus::DalamKuiri)->create();
    $pemohon = User::factory()->pemohon()->create();

    Livewire::actingAs($pemohon)
        ->test('pages::sid.kuiri', ['permohonan' => $permohonan])
        ->assertForbidden();
});
