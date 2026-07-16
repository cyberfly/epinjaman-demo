<?php

use App\Enums\UserRole;
use App\Models\User;

dataset('repository routes', [
    'checklist' => ['admin.checklist-items'],
    'templates' => ['admin.notification-templates'],
]);

test('admin boleh akses skrin repositori', function (string $route) {
    $admin = User::factory()->role(UserRole::Admin)->create();

    $this->actingAs($admin)->get(route($route))->assertOk();
})->with('repository routes');

test('bukan admin ditolak daripada skrin repositori', function (string $route) {
    $psid = User::factory()->role(UserRole::PSID)->create();

    $this->actingAs($psid)->get(route($route))->assertForbidden();
})->with('repository routes');
