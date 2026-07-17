<?php

use App\Enums\PermohonanStatus;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Layout('layouts.app')] #[Title('Permohonan Saya')] class extends Component {
    public function mount(): void
    {
        abort_unless(auth()->user()?->isPemohon() ?? false, 403);
    }

    /**
     * @return \Illuminate\Support\Collection<int, \App\Models\Permohonan>
     */
    #[Computed]
    public function permohonans()
    {
        $pemohon = auth()->user()->pemohon;

        return $pemohon?->permohonans()->latest()->get() ?? collect();
    }
}; ?>

<div class="flex flex-col gap-6">
    <div class="flex items-center justify-between">
        <flux:heading size="xl">{{ __('Permohonan Saya') }}</flux:heading>
        <flux:button variant="primary" :href="route('permohonan.baharu')" wire:navigate>{{ __('Permohonan Baharu') }}</flux:button>
    </div>

    <flux:card>
        <flux:table>
            <flux:table.columns>
                <flux:table.column>{{ __('Tajuk') }}</flux:table.column>
                <flux:table.column>{{ __('No. Rujukan') }}</flux:table.column>
                <flux:table.column>{{ __('Status') }}</flux:table.column>
                <flux:table.column>{{ __('Traffic Light') }}</flux:table.column>
                <flux:table.column></flux:table.column>
            </flux:table.columns>
            <flux:table.rows>
                @forelse ($this->permohonans as $permohonan)
                    <flux:table.row wire:key="permohonan-{{ $permohonan->id }}">
                        <flux:table.cell>{{ $permohonan->tajuk ?? __('(tiada tajuk)') }}</flux:table.cell>
                        <flux:table.cell>{{ $permohonan->no_rujukan ?? '—' }}</flux:table.cell>
                        <flux:table.cell>
                            <flux:badge size="sm">{{ $permohonan->status->label() }}</flux:badge>
                        </flux:table.cell>
                        <flux:table.cell>
                            @if ($permohonan->traffic_light)
                                <flux:badge size="sm" :color="$permohonan->traffic_light->color()">
                                    {{ $permohonan->traffic_light->label() }}
                                </flux:badge>
                            @else
                                —
                            @endif
                        </flux:table.cell>
                        <flux:table.cell>
                            @if ($permohonan->status === PermohonanStatus::MenungguSemakanKelengkapan)
                                <flux:button size="sm" variant="primary" :href="route('permohonan.tandatangan-p1', $permohonan)" wire:navigate>
                                    {{ __('Tandatangan P1') }}
                                </flux:button>
                            @elseif ($permohonan->status === PermohonanStatus::Ditawarkan)
                                <flux:button size="sm" variant="primary" :href="route('permohonan.surat-tawaran', $permohonan)" wire:navigate>
                                    {{ __('Surat Tawaran') }}
                                </flux:button>
                            @elseif ($permohonan->status === PermohonanStatus::DalamPerjanjian)
                                <flux:button size="sm" variant="primary" :href="route('permohonan.perjanjian', $permohonan)" wire:navigate>
                                    {{ __('Draf Perjanjian') }}
                                </flux:button>
                            @elseif ($permohonan->status === PermohonanStatus::DalamPenyediaanCP)
                                <flux:button size="sm" variant="primary" :href="route('permohonan.syarat-duluan', $permohonan)" wire:navigate>
                                    {{ __('Syarat Duluan') }}
                                </flux:button>
                            @else
                                <flux:button size="sm" variant="ghost" :href="route('permohonan.borang', $permohonan)" wire:navigate>
                                    {{ $permohonan->status === PermohonanStatus::Draf ? __('Edit') : __('Lihat') }}
                                </flux:button>
                            @endif
                        </flux:table.cell>
                    </flux:table.row>
                @empty
                    <flux:table.row>
                        <flux:table.cell colspan="5">{{ __('Tiada permohonan lagi.') }}</flux:table.cell>
                    </flux:table.row>
                @endforelse
            </flux:table.rows>
        </flux:table>
    </flux:card>
</div>
