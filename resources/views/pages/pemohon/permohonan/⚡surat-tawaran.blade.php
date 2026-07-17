<?php

use App\Actions\Permohonan\TandatanganSuratAkuan;
use App\Models\Permohonan;
use Flux\Flux;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Locked;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Layout('layouts.app')] #[Title('Surat Tawaran')] class extends Component {
    #[Locked]
    public int $permohonanId;

    public function mount(Permohonan $permohonan): void
    {
        $this->authorize('view', $permohonan);
        $this->permohonanId = $permohonan->id;
    }

    #[Computed]
    public function permohonan(): ?Permohonan
    {
        return Permohonan::with('suratTawaran')->find($this->permohonanId);
    }

    public function tandatangan(TandatanganSuratAkuan $tandatanganSuratAkuan): void
    {
        $permohonan = Permohonan::findOrFail($this->permohonanId);
        $this->authorize('signSuratAkuan', $permohonan);

        $tandatanganSuratAkuan->handle($permohonan, auth()->user());

        Flux::toast(variant: 'success', text: __('Surat Akuan Penerimaan ditandatangani.'));

        $this->redirectRoute('permohonan.index', navigate: true);
    }
}; ?>

<div class="flex flex-col gap-6">
    <flux:heading size="xl">{{ __('Surat Tawaran Pinjaman') }}</flux:heading>

    @if ($this->permohonan?->traffic_light)
        <flux:badge :color="$this->permohonan->traffic_light->color()">{{ $this->permohonan->traffic_light->label() }}</flux:badge>
    @endif

    <flux:card class="space-y-4">
        <flux:text class="whitespace-pre-line">{{ $this->permohonan?->suratTawaran?->kandungan }}</flux:text>

        <flux:heading size="sm">{{ __('Lampiran: Terma & Syarat Utama') }}</flux:heading>
        <flux:text class="whitespace-pre-line">{{ $this->permohonan?->suratTawaran?->terma_utama }}</flux:text>
    </flux:card>

    @can('signSuratAkuan', $this->permohonan)
        <flux:card class="space-y-4">
            <flux:text>{{ __('Sila turunkan Tandatangan Digital pada Surat Akuan Penerimaan. Dibenarkan tanpa mengira status Traffic Light.') }}</flux:text>
            <flux:button variant="primary" wire:click="tandatangan" data-test="sign-akuan-button">
                {{ __('Setuju Terima & Tandatangan') }}
            </flux:button>
        </flux:card>
    @endcan
</div>
