<?php

use App\Enums\PermohonanStatus;
use App\Enums\UserRole;
use App\Models\Permohonan;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Layout('layouts.app')] #[Title('Tray Kementerian Pengawal')] class extends Component {
    public function mount(): void
    {
        abort_unless(auth()->user()?->hasRole(UserRole::KementerianPengawal) ?? false, 403);
    }

    /**
     * @return \Illuminate\Support\Collection<int, Permohonan>
     */
    #[Computed]
    public function permohonans()
    {
        $kementerianId = auth()->user()->kementerian_pengawal_id;

        if ($kementerianId === null) {
            return collect();
        }

        return Permohonan::query()
            ->where('kementerian_pengawal_id', $kementerianId)
            ->where('status', PermohonanStatus::MenungguTandatanganKementerianPengawal)
            ->with('pemohon')
            ->latest('dihantar_ke_kementerian_pada')
            ->get();
    }
}; ?>

<div class="flex flex-col gap-6">
    <flux:heading size="xl">{{ __('Tray Tugasan — Kementerian Pengawal') }}</flux:heading>

    <flux:card>
        <flux:table>
            <flux:table.columns>
                <flux:table.column>{{ __('Pemohon') }}</flux:table.column>
                <flux:table.column>{{ __('Tajuk') }}</flux:table.column>
                <flux:table.column>{{ __('Traffic Light') }}</flux:table.column>
                <flux:table.column></flux:table.column>
            </flux:table.columns>
            <flux:table.rows>
                @forelse ($this->permohonans as $permohonan)
                    <flux:table.row wire:key="kp-permohonan-{{ $permohonan->id }}">
                        <flux:table.cell>{{ $permohonan->pemohon->nama }}</flux:table.cell>
                        <flux:table.cell>{{ $permohonan->tajuk }}</flux:table.cell>
                        <flux:table.cell>
                            @if ($permohonan->traffic_light)
                                <flux:badge size="sm" :color="$permohonan->traffic_light->color()">{{ $permohonan->traffic_light->label() }}</flux:badge>
                            @else
                                —
                            @endif
                        </flux:table.cell>
                        <flux:table.cell>
                            <flux:button size="sm" variant="primary" :href="route('kementerian.tandatangan-p2', $permohonan)" wire:navigate>
                                {{ __('Tandatangan P2') }}
                            </flux:button>
                        </flux:table.cell>
                    </flux:table.row>
                @empty
                    <flux:table.row>
                        <flux:table.cell colspan="4">{{ __('Tiada permohonan menunggu tindakan.') }}</flux:table.cell>
                    </flux:table.row>
                @endforelse
            </flux:table.rows>
        </flux:table>
    </flux:card>
</div>
