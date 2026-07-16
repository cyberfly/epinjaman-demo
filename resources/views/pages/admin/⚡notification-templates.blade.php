<?php

use App\Models\NotificationTemplate;
use Flux\Flux;
use Illuminate\Support\Facades\Gate;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Layout('layouts.app')] #[Title('Templat Notifikasi (Lampiran 8)')] class extends Component {
    public ?int $editingId = null;

    public string $event = '';

    public string $title = '';

    public string $content = '';

    public bool $is_active = true;

    public function mount(): void
    {
        Gate::authorize('manage-repositories');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Collection<int, NotificationTemplate>
     */
    #[Computed]
    public function templates()
    {
        return NotificationTemplate::orderBy('event')->get();
    }

    public function save(): void
    {
        Gate::authorize('manage-repositories');

        $validated = $this->validate([
            'event' => ['required', 'string', 'max:255', 'regex:/^[\w.\-]+$/', 'unique:notification_templates,event,'.($this->editingId ?? 'NULL')],
            'title' => ['required', 'string', 'max:255'],
            'content' => ['required', 'string'],
            'is_active' => ['boolean'],
        ]);

        if ($this->editingId !== null) {
            NotificationTemplate::findOrFail($this->editingId)->update($validated);
        } else {
            NotificationTemplate::create($validated);
        }

        $this->resetForm();
        unset($this->templates);

        Flux::toast(variant: 'success', text: __('Templat notifikasi disimpan.'));
    }

    public function edit(int $id): void
    {
        $template = NotificationTemplate::findOrFail($id);
        $this->editingId = $template->id;
        $this->event = $template->event;
        $this->title = $template->title;
        $this->content = $template->content;
        $this->is_active = $template->is_active;
    }

    public function delete(int $id): void
    {
        Gate::authorize('manage-repositories');

        NotificationTemplate::findOrFail($id)->delete();
        unset($this->templates);

        Flux::toast(variant: 'success', text: __('Templat notifikasi dipadam.'));
    }

    public function resetForm(): void
    {
        $this->reset('editingId', 'event', 'title', 'content');
        $this->is_active = true;
    }
}; ?>

<div class="flex flex-col gap-6">
    <flux:heading size="xl">{{ __('Templat Notifikasi (Lampiran 8)') }}</flux:heading>

    <flux:card class="space-y-4">
        <flux:heading>{{ $editingId ? __('Kemas Kini Templat') : __('Templat Baharu') }}</flux:heading>
        <flux:text variant="subtle">{{ __('Gunakan :token dalam tajuk/kandungan untuk pemboleh ubah, cth. :no_rujukan.') }}</flux:text>
        <form wire:submit="save" class="space-y-4">
            <flux:input wire:model="event" :label="__('Pencetus (event)')" placeholder="cth: permohonan.diterima" required />
            <flux:input wire:model="title" :label="__('Tajuk')" required />
            <flux:textarea wire:model="content" :label="__('Kandungan')" required />
            <flux:switch wire:model="is_active" :label="__('Aktif')" />
            <div class="flex gap-2">
                <flux:button variant="primary" type="submit" data-test="save-template-button">{{ __('Simpan') }}</flux:button>
                @if ($editingId)
                    <flux:button variant="ghost" wire:click="resetForm">{{ __('Batal') }}</flux:button>
                @endif
            </div>
        </form>
    </flux:card>

    <flux:card>
        <flux:table>
            <flux:table.columns>
                <flux:table.column>{{ __('Pencetus') }}</flux:table.column>
                <flux:table.column>{{ __('Tajuk') }}</flux:table.column>
                <flux:table.column>{{ __('Aktif') }}</flux:table.column>
                <flux:table.column></flux:table.column>
            </flux:table.columns>
            <flux:table.rows>
                @foreach ($this->templates as $template)
                    <flux:table.row wire:key="template-{{ $template->id }}">
                        <flux:table.cell>{{ $template->event }}</flux:table.cell>
                        <flux:table.cell>{{ $template->title }}</flux:table.cell>
                        <flux:table.cell>{{ $template->is_active ? __('Ya') : __('Tidak') }}</flux:table.cell>
                        <flux:table.cell>
                            <flux:button size="sm" variant="ghost" wire:click="edit({{ $template->id }})">{{ __('Edit') }}</flux:button>
                            <flux:button size="sm" variant="ghost" wire:click="delete({{ $template->id }})">{{ __('Padam') }}</flux:button>
                        </flux:table.cell>
                    </flux:table.row>
                @endforeach
            </flux:table.rows>
        </flux:table>
    </flux:card>
</div>
