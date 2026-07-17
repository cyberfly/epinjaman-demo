<?php

use App\Actions\Permohonan\SahkanPerjanjianBUU;
use App\Actions\Permohonan\UlasPerjanjian;
use App\Models\Permohonan;
use Flux\Flux;
use Illuminate\Support\Facades\Gate;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Locked;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Layout('layouts.app')] #[Title('Semakan Perjanjian')] class extends Component {
    #[Locked]
    public int $permohonanId;

    public string $ulasan = '';

    public function mount(Permohonan $permohonan): void
    {
        Gate::authorize('reviewPerjanjian', $permohonan);
        $this->permohonanId = $permohonan->id;
    }

    #[Computed]
    public function permohonan(): ?Permohonan
    {
        return Permohonan::with(['perjanjian.ulasans'])->find($this->permohonanId);
    }

    public function ulas(UlasPerjanjian $ulasPerjanjian): void
    {
        $permohonan = Permohonan::with('perjanjian')->findOrFail($this->permohonanId);
        Gate::authorize('reviewPerjanjian', $permohonan);
        abort_if($permohonan->perjanjian === null, 404);

        $this->validate(['ulasan' => ['required', 'string']]);

        $ulasPerjanjian->handle($permohonan->perjanjian, auth()->user(), $this->ulasan);

        $this->reset('ulasan');
        unset($this->permohonan);

        Flux::toast(variant: 'success', text: __('Ulasan direkod.'));
    }

    public function sahkan(SahkanPerjanjianBUU $sahkanPerjanjianBUU): void
    {
        $permohonan = Permohonan::with('perjanjian')->findOrFail($this->permohonanId);
        Gate::authorize('approvePerjanjian', $permohonan);
        abort_if($permohonan->perjanjian === null, 404);

        $sahkanPerjanjianBUU->handle($permohonan->perjanjian, auth()->user());

        Flux::toast(variant: 'success', text: __('Perjanjian disahkan teratur oleh BUU.'));
        unset($this->permohonan);
    }
}; ?>

<div class="flex flex-col gap-6">
    <flux:heading size="xl">{{ __('Semakan & Pengesahan Perjanjian') }}</flux:heading>

    <flux:card class="space-y-3">
        <flux:text>{{ __('No. Rujukan') }}: {{ $this->permohonan?->no_rujukan ?? '—' }}</flux:text>
        <flux:text>{{ __('Draf') }}: {{ $this->permohonan?->perjanjian?->draf_nama ?? __('Belum dimuat naik') }}</flux:text>
        <flux:text>
            {{ __('Status BUU') }}:
            <flux:badge size="sm" :color="$this->permohonan?->perjanjian?->disahkan_buu ? 'green' : 'zinc'">
                {{ $this->permohonan?->perjanjian?->disahkan_buu ? __('Disahkan') : __('Menunggu') }}
            </flux:badge>
        </flux:text>
    </flux:card>

    @if ($this->permohonan?->perjanjian)
        <flux:card class="space-y-4">
            <flux:heading size="sm">{{ __('Ulasan') }}</flux:heading>
            @foreach ($this->permohonan->perjanjian->ulasans as $item)
                <div class="rounded bg-zinc-100 p-2 text-sm dark:bg-zinc-800" wire:key="ulasan-{{ $item->id }}">
                    <span class="font-medium">{{ $item->role?->label() ?? __('Pegawai') }}:</span> {{ $item->ulasan }}
                </div>
            @endforeach

            <form wire:submit="ulas" class="space-y-3">
                <flux:textarea wire:model="ulasan" :label="__('Ulasan baharu')" />
                <flux:button variant="filled" type="submit" data-test="ulas-perjanjian-button">{{ __('Hantar Ulasan') }}</flux:button>
            </form>

            @can('approvePerjanjian', $this->permohonan)
                <flux:button variant="primary" wire:click="sahkan" data-test="sahkan-perjanjian-button">
                    {{ __('Sahkan Perjanjian Teratur (BUU)') }}
                </flux:button>
            @endcan
        </flux:card>
    @endif
</div>
