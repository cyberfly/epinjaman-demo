<?php

use App\Enums\PermohonanStatus;
use App\Enums\UserRole;
use App\Models\ChecklistItem;
use App\Models\Permohonan;
use App\Models\User;
use Livewire\Livewire;

test('PSID menyemak item lalu mengesahkan lengkap & teruskan ke Rundingan', function () {
    $psid = User::factory()->role(UserRole::PSID)->create();
    $item = ChecklistItem::factory()->create();
    $permohonan = Permohonan::factory()->status(PermohonanStatus::DalamSemakanSID)->create();

    Livewire::actingAs($psid)
        ->test('pages::sid.semakan-dokumen', ['permohonan' => $permohonan])
        ->set("reviews.{$item->id}.is_complete", true)
        ->set("reviews.{$item->id}.is_orderly", true)
        ->call('sahkanLengkap')
        ->assertHasNoErrors()
        ->assertRedirect(route('sid.tray'));

    expect($permohonan->fresh()->status)->toBe(PermohonanStatus::DalamRundingan)
        ->and($permohonan->documentReviews()->where('checklist_item_id', $item->id)->first()->is_complete)->toBeTrue();
});

test('PSID menandakan perlu Kuiri melalui skrin', function () {
    $psid = User::factory()->role(UserRole::PSID)->create();
    ChecklistItem::factory()->create();
    $permohonan = Permohonan::factory()->status(PermohonanStatus::DalamSemakanSID)->create();

    Livewire::actingAs($psid)
        ->test('pages::sid.semakan-dokumen', ['permohonan' => $permohonan])
        ->call('tandakanPerluKuiri')
        ->assertRedirect(route('sid.tray'));

    expect($permohonan->fresh()->status)->toBe(PermohonanStatus::DalamKuiri);
});

test('peranan bukan PSID ditolak daripada skrin semakan', function () {
    $permohonan = Permohonan::factory()->status(PermohonanStatus::DalamSemakanSID)->create();
    $pemohon = User::factory()->pemohon()->create();

    Livewire::actingAs($pemohon)
        ->test('pages::sid.semakan-dokumen', ['permohonan' => $permohonan])
        ->assertForbidden();
});
