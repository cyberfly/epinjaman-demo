<?php

use App\Enums\PermohonanStatus;
use App\Enums\UserRole;
use App\Models\Memo;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Layout('layouts.app')] #[Title('Tray Kelulusan')] class extends Component {
    public function mount(): void
    {
        $role = auth()->user()?->role;
        abort_unless($role !== null && in_array($role, UserRole::approvalHierarchy(), true), 403);
    }

    /**
     * @return \Illuminate\Support\Collection<int, Memo>
     */
    #[Computed]
    public function memos()
    {
        $role = auth()->user()->role;

        return Memo::query()
            ->where('peringkat', $role->value)
            ->whereNull('keputusan')
            ->whereHas('permohonan', fn ($q) => $q->where('status', PermohonanStatus::DalamKelulusan->value))
            ->with('permohonan')
            ->get();
    }
}; ?>

<div class="flex flex-col gap-6">
    <flux:heading size="xl">{{ __('Tray Kelulusan') }} — {{ auth()->user()->role?->label() }}</flux:heading>

    <flux:card>
        <flux:table>
            <flux:table.columns>
                <flux:table.column>{{ __('No. Rujukan') }}</flux:table.column>
                <flux:table.column>{{ __('Peringkat Semasa') }}</flux:table.column>
                <flux:table.column></flux:table.column>
            </flux:table.columns>
            <flux:table.rows>
                @forelse ($this->memos as $memo)
                    <flux:table.row wire:key="memo-{{ $memo->id }}">
                        <flux:table.cell>{{ $memo->permohonan->no_rujukan ?? '—' }}</flux:table.cell>
                        <flux:table.cell>{{ $memo->peringkat->label() }}</flux:table.cell>
                        <flux:table.cell>
                            <flux:button size="sm" variant="primary" :href="route('kelulusan.memo', $memo)" wire:navigate>
                                {{ __('Buka Memo') }}
                            </flux:button>
                        </flux:table.cell>
                    </flux:table.row>
                @empty
                    <flux:table.row>
                        <flux:table.cell colspan="3">{{ __('Tiada memo menunggu tindakan.') }}</flux:table.cell>
                    </flux:table.row>
                @endforelse
            </flux:table.rows>
        </flux:table>
    </flux:card>
</div>
