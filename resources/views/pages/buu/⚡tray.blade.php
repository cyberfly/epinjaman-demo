<?php

use App\Enums\PermohonanStatus;
use App\Enums\UserRole;
use App\Models\Permohonan;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Layout('layouts.app')] #[Title('Tray BUU')] class extends Component {
    public function mount(): void
    {
        abort_unless(auth()->user()?->hasRole(UserRole::BUU) ?? false, 403);
    }

    /**
     * @return \Illuminate\Support\Collection<int, Permohonan>
     */
    #[Computed]
    public function permohonans()
    {
        return Permohonan::query()
            ->where('status', PermohonanStatus::DalamPerjanjian)
            ->with('pemohon')
            ->latest('diterima_setuju_pada')
            ->get();
    }
}; ?>

<div class="flex flex-col gap-6">
    <flux:heading size="xl">{{ __('Tray Tugasan — BUU (Pengesahan Perjanjian)') }}</flux:heading>

    <flux:card>
        <flux:table>
            <flux:table.columns>
                <flux:table.column>{{ __('No. Rujukan') }}</flux:table.column>
                <flux:table.column>{{ __('Pemohon') }}</flux:table.column>
                <flux:table.column></flux:table.column>
            </flux:table.columns>
            <flux:table.rows>
                @forelse ($this->permohonans as $permohonan)
                    <flux:table.row wire:key="buu-permohonan-{{ $permohonan->id }}">
                        <flux:table.cell>{{ $permohonan->no_rujukan ?? '—' }}</flux:table.cell>
                        <flux:table.cell>{{ $permohonan->pemohon->nama }}</flux:table.cell>
                        <flux:table.cell>
                            <flux:button size="sm" variant="primary" :href="route('perjanjian.semakan', $permohonan)" wire:navigate>
                                {{ __('Semak Perjanjian') }}
                            </flux:button>
                        </flux:table.cell>
                    </flux:table.row>
                @empty
                    <flux:table.row>
                        <flux:table.cell colspan="3">{{ __('Tiada perjanjian menunggu semakan.') }}</flux:table.cell>
                    </flux:table.row>
                @endforelse
            </flux:table.rows>
        </flux:table>
    </flux:card>
</div>
