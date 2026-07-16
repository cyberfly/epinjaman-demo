<?php

use App\Models\ChecklistItem;
use Flux\Flux;
use Illuminate\Support\Facades\Gate;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Layout('layouts.app')] #[Title('Senarai Semak Dokumen (Lampiran 6)')] class extends Component {
    public ?int $editingId = null;

    public string $label = '';

    public string $description = '';

    public int $position = 0;

    public bool $is_active = true;

    public function mount(): void
    {
        Gate::authorize('manage-repositories');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Collection<int, ChecklistItem>
     */
    #[Computed]
    public function items()
    {
        return ChecklistItem::orderBy('position')->orderBy('id')->get();
    }

    public function save(): void
    {
        Gate::authorize('manage-repositories');

        $validated = $this->validate([
            'label' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'position' => ['integer', 'min:0'],
            'is_active' => ['boolean'],
        ]);

        if ($this->editingId !== null) {
            ChecklistItem::findOrFail($this->editingId)->update($validated);
        } else {
            ChecklistItem::create($validated);
        }

        $this->resetForm();
        unset($this->items);

        Flux::toast(variant: 'success', text: __('Item senarai semak disimpan.'));
    }

    public function edit(int $id): void
    {
        $item = ChecklistItem::findOrFail($id);
        $this->editingId = $item->id;
        $this->label = $item->label;
        $this->description = (string) $item->description;
        $this->position = $item->position;
        $this->is_active = $item->is_active;
    }

    public function delete(int $id): void
    {
        Gate::authorize('manage-repositories');

        ChecklistItem::findOrFail($id)->delete();
        unset($this->items);

        Flux::toast(variant: 'success', text: __('Item senarai semak dipadam.'));
    }

    public function resetForm(): void
    {
        $this->reset('editingId', 'label', 'description', 'position');
        $this->is_active = true;
    }
}; ?>

<div class="flex flex-col gap-6">
    <flux:heading size="xl">{{ __('Senarai Semak Dokumen (Lampiran 6)') }}</flux:heading>

    <flux:card class="space-y-4">
        <flux:heading>{{ $editingId ? __('Kemas Kini Item') : __('Item Baharu') }}</flux:heading>
        <form wire:submit="save" class="space-y-4">
            <flux:input wire:model="label" :label="__('Label')" required />
            <flux:textarea wire:model="description" :label="__('Huraian')" />
            <flux:input wire:model="position" type="number" :label="__('Kedudukan')" />
            <flux:switch wire:model="is_active" :label="__('Aktif')" />
            <div class="flex gap-2">
                <flux:button variant="primary" type="submit" data-test="save-checklist-button">{{ __('Simpan') }}</flux:button>
                @if ($editingId)
                    <flux:button variant="ghost" wire:click="resetForm">{{ __('Batal') }}</flux:button>
                @endif
            </div>
        </form>
    </flux:card>

    <flux:card>
        <flux:table>
            <flux:table.columns>
                <flux:table.column>{{ __('Label') }}</flux:table.column>
                <flux:table.column>{{ __('Huraian') }}</flux:table.column>
                <flux:table.column>{{ __('Kedudukan') }}</flux:table.column>
                <flux:table.column>{{ __('Aktif') }}</flux:table.column>
                <flux:table.column></flux:table.column>
            </flux:table.columns>
            <flux:table.rows>
                @foreach ($this->items as $item)
                    <flux:table.row wire:key="checklist-{{ $item->id }}">
                        <flux:table.cell>{{ $item->label }}</flux:table.cell>
                        <flux:table.cell>{{ $item->description }}</flux:table.cell>
                        <flux:table.cell>{{ $item->position }}</flux:table.cell>
                        <flux:table.cell>{{ $item->is_active ? __('Ya') : __('Tidak') }}</flux:table.cell>
                        <flux:table.cell>
                            <flux:button size="sm" variant="ghost" wire:click="edit({{ $item->id }})">{{ __('Edit') }}</flux:button>
                            <flux:button size="sm" variant="ghost" wire:click="delete({{ $item->id }})">{{ __('Padam') }}</flux:button>
                        </flux:table.cell>
                    </flux:table.row>
                @endforeach
            </flux:table.rows>
        </flux:table>
    </flux:card>
</div>
