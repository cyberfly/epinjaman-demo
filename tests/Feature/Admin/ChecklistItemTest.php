<?php

use App\Enums\UserRole;
use App\Models\ChecklistItem;
use App\Models\User;
use Livewire\Livewire;

test('admin boleh cipta, kemas kini dan padam item senarai semak', function () {
    $admin = User::factory()->role(UserRole::Admin)->create();

    // Create
    Livewire::actingAs($admin)
        ->test('pages::admin.checklist-items')
        ->set('label', 'Surat permohonan rasmi')
        ->set('description', 'Ditandatangani ketua jabatan')
        ->set('position', 1)
        ->call('save')
        ->assertHasNoErrors();

    $item = ChecklistItem::firstWhere('label', 'Surat permohonan rasmi');
    expect($item)->not->toBeNull()
        ->and($item->description)->toBe('Ditandatangani ketua jabatan');

    // Update
    Livewire::actingAs($admin)
        ->test('pages::admin.checklist-items')
        ->call('edit', $item->id)
        ->set('label', 'Surat permohonan (dikemaskini)')
        ->call('save')
        ->assertHasNoErrors();

    expect($item->fresh()->label)->toBe('Surat permohonan (dikemaskini)');

    // Delete
    Livewire::actingAs($admin)
        ->test('pages::admin.checklist-items')
        ->call('delete', $item->id)
        ->assertHasNoErrors();

    expect(ChecklistItem::find($item->id))->toBeNull();
});

test('label wajib diisi', function () {
    $admin = User::factory()->role(UserRole::Admin)->create();

    Livewire::actingAs($admin)
        ->test('pages::admin.checklist-items')
        ->set('label', '')
        ->call('save')
        ->assertHasErrors(['label' => 'required']);
});

test('senarai semak aktif boleh diambil semula oleh sistem mengikut kedudukan', function () {
    ChecklistItem::factory()->create(['label' => 'B', 'position' => 2]);
    ChecklistItem::factory()->create(['label' => 'A', 'position' => 1]);
    ChecklistItem::factory()->inactive()->create(['label' => 'Tidak aktif', 'position' => 0]);

    $active = ChecklistItem::active();

    expect($active)->toHaveCount(2)
        ->and($active->pluck('label')->all())->toBe(['A', 'B']);
});
