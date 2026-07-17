<?php

use App\Enums\PermohonanStatus;
use App\Enums\UserRole;
use App\Models\Permohonan;
use App\Models\User;
use Livewire\Livewire;

test('sidebar navigasi memaparkan tanpa ralat untuk setiap peranan', function () {
    foreach (UserRole::cases() as $role) {
        $user = User::factory()->role($role)->create([
            'email' => 'nav-'.$role->value.'@test.local',
        ]);

        $this->actingAs($user)->get(route('dashboard'))->assertOk();
    }
});

test('BUU melihat perjanjian Dalam Perjanjian dalam tray', function () {
    $buu = User::factory()->role(UserRole::BUU)->create();
    Permohonan::factory()->status(PermohonanStatus::DalamPerjanjian)->create(['no_rujukan' => 'SID/2026/0100']);

    Livewire::actingAs($buu)
        ->test('pages::buu.tray')
        ->assertSee('SID/2026/0100');
});

test('bukan BUU ditolak daripada tray BUU', function () {
    $psid = User::factory()->role(UserRole::PSID)->create();

    $this->actingAs($psid)->get(route('buu.tray'))->assertForbidden();
});
