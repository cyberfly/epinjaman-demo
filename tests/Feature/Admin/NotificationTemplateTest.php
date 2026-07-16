<?php

use App\Enums\UserRole;
use App\Models\NotificationTemplate;
use App\Models\User;
use Livewire\Livewire;

test('admin boleh cipta, kemas kini dan padam templat notifikasi', function () {
    $admin = User::factory()->role(UserRole::Admin)->create();

    Livewire::actingAs($admin)
        ->test('pages::admin.notification-templates')
        ->set('event', 'permohonan.diterima')
        ->set('title', 'Permohonan Diterima')
        ->set('content', 'Rujukan anda :no_rujukan')
        ->call('save')
        ->assertHasNoErrors();

    $template = NotificationTemplate::firstWhere('event', 'permohonan.diterima');
    expect($template)->not->toBeNull();

    Livewire::actingAs($admin)
        ->test('pages::admin.notification-templates')
        ->call('edit', $template->id)
        ->set('title', 'Permohonan Telah Diterima')
        ->call('save')
        ->assertHasNoErrors();

    expect($template->fresh()->title)->toBe('Permohonan Telah Diterima');

    Livewire::actingAs($admin)
        ->test('pages::admin.notification-templates')
        ->call('delete', $template->id)
        ->assertHasNoErrors();

    expect(NotificationTemplate::find($template->id))->toBeNull();
});

test('event mesti unik', function () {
    $admin = User::factory()->role(UserRole::Admin)->create();
    NotificationTemplate::factory()->create(['event' => 'kuiri.dicetus']);

    Livewire::actingAs($admin)
        ->test('pages::admin.notification-templates')
        ->set('event', 'kuiri.dicetus')
        ->set('title', 'Kuiri')
        ->set('content', 'Sila balas')
        ->call('save')
        ->assertHasErrors(['event']);
});

test('sistem boleh resolve templat aktif mengikut event', function () {
    NotificationTemplate::factory()->create(['event' => 'tawaran.dijana', 'is_active' => true]);
    NotificationTemplate::factory()->create(['event' => 'tak.aktif', 'is_active' => false]);

    expect(NotificationTemplate::resolve('tawaran.dijana'))->not->toBeNull()
        ->and(NotificationTemplate::resolve('tak.aktif'))->toBeNull()
        ->and(NotificationTemplate::resolve('tiada'))->toBeNull();
});

test('templat menginterpolasi token :key daripada data', function () {
    $template = NotificationTemplate::factory()->create([
        'event' => 'permohonan.diterima',
        'title' => 'Permohonan :no_rujukan',
        'content' => 'Rujukan anda ialah :no_rujukan bagi :nama.',
    ]);

    $data = ['no_rujukan' => 'SID/2026/001', 'nama' => 'Agensi ABC'];

    expect($template->renderTitle($data))->toBe('Permohonan SID/2026/001')
        ->and($template->renderContent($data))->toBe('Rujukan anda ialah SID/2026/001 bagi Agensi ABC.');
});
