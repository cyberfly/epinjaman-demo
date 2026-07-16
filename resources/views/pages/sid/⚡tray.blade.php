<?php

use App\Enums\PermohonanStatus;
use App\Enums\UserRole;
use App\Models\Permohonan;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Layout('layouts.app')] #[Title('Tray Tugasan SID')] class extends Component {
    public function mount(): void
    {
        abort_unless(auth()->user()?->hasRole(UserRole::PSID) ?? false, 403);
    }

    /**
     * Statuses a PSID acts on. Extended by later tickets (kuiri, rundingan, memo).
     *
     * @return array<int, PermohonanStatus>
     */
    private function actionableStatuses(): array
    {
        return [
            PermohonanStatus::DalamSemakanSID,
            PermohonanStatus::DalamKuiri,
            PermohonanStatus::DalamRundingan,
        ];
    }

    /**
     * @return \Illuminate\Support\Collection<int, Permohonan>
     */
    #[Computed]
    public function permohonans()
    {
        return Permohonan::query()
            ->whereIn('status', collect($this->actionableStatuses())->map->value)
            ->with('pemohon')
            ->latest('diterima_sid_pada')
            ->get();
    }
}; ?>

<div class="flex flex-col gap-6">
    <flux:heading size="xl">{{ __('Tray Tugasan — SID') }}</flux:heading>

    <flux:card>
        <flux:table>
            <flux:table.columns>
                <flux:table.column>{{ __('No. Rujukan') }}</flux:table.column>
                <flux:table.column>{{ __('Pemohon') }}</flux:table.column>
                <flux:table.column>{{ __('Status') }}</flux:table.column>
                <flux:table.column></flux:table.column>
            </flux:table.columns>
            <flux:table.rows>
                @forelse ($this->permohonans as $permohonan)
                    <flux:table.row wire:key="sid-permohonan-{{ $permohonan->id }}">
                        <flux:table.cell>{{ $permohonan->no_rujukan ?? '—' }}</flux:table.cell>
                        <flux:table.cell>{{ $permohonan->pemohon->nama }}</flux:table.cell>
                        <flux:table.cell><flux:badge size="sm">{{ $permohonan->status->label() }}</flux:badge></flux:table.cell>
                        <flux:table.cell>
                            @if ($permohonan->status === PermohonanStatus::DalamSemakanSID)
                                <flux:button size="sm" variant="primary" :href="route('sid.semakan-dokumen', $permohonan)" wire:navigate>
                                    {{ __('Semak Dokumen') }}
                                </flux:button>
                            @endif
                        </flux:table.cell>
                    </flux:table.row>
                @empty
                    <flux:table.row>
                        <flux:table.cell colspan="4">{{ __('Tiada permohonan dalam tray.') }}</flux:table.cell>
                    </flux:table.row>
                @endforelse
            </flux:table.rows>
        </flux:table>
    </flux:card>
</div>
