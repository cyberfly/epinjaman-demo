<?php

use App\Actions\Permohonan\MuatNaikDrafPerjanjian;
use App\Models\Permohonan;
use Flux\Flux;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Locked;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithFileUploads;

new #[Layout('layouts.app')] #[Title('Draf Perjanjian')] class extends Component {
    use WithFileUploads;

    #[Locked]
    public int $permohonanId;

    public $draf = null;

    public function mount(Permohonan $permohonan): void
    {
        $this->authorize('view', $permohonan);
        $this->permohonanId = $permohonan->id;
    }

    #[Computed]
    public function permohonan(): ?Permohonan
    {
        return Permohonan::with(['perjanjian.ulasans'])->find($this->permohonanId);
    }

    public function muatNaik(MuatNaikDrafPerjanjian $muatNaikDrafPerjanjian): void
    {
        $permohonan = Permohonan::findOrFail($this->permohonanId);
        $this->authorize('uploadDrafPerjanjian', $permohonan);

        $this->validate(['draf' => ['required', 'file', 'max:10240']]);

        $path = $this->draf->store('perjanjian-draf');

        $muatNaikDrafPerjanjian->handle($permohonan, auth()->user(), $path, $this->draf->getClientOriginalName());

        $this->reset('draf');
        unset($this->permohonan);

        Flux::toast(variant: 'success', text: __('Draf Dokumen Perjanjian dimuat naik.'));
    }
}; ?>

<div class="flex flex-col gap-6">
    <flux:heading size="xl">{{ __('Draf Dokumen Perjanjian') }}</flux:heading>

    @if ($this->permohonan?->traffic_light)
        <flux:badge :color="$this->permohonan->traffic_light->color()">{{ $this->permohonan->traffic_light->label() }}</flux:badge>
    @endif

    @can('uploadDrafPerjanjian', $this->permohonan)
        <flux:card class="space-y-4">
            <form wire:submit="muatNaik" class="space-y-4">
                <flux:input type="file" wire:model="draf" :label="__('Fail draf perjanjian')" />
                <flux:button variant="primary" type="submit" data-test="upload-perjanjian-button">{{ __('Muat Naik') }}</flux:button>
            </form>
        </flux:card>
    @endcan

    @if ($this->permohonan?->perjanjian)
        <flux:card class="space-y-3">
            <flux:text>{{ __('Draf semasa') }}: {{ $this->permohonan->perjanjian->draf_nama ?? '—' }}</flux:text>
            <flux:text>
                {{ __('Status BUU') }}:
                <flux:badge size="sm" :color="$this->permohonan->perjanjian->disahkan_buu ? 'green' : 'zinc'">
                    {{ $this->permohonan->perjanjian->disahkan_buu ? __('Disahkan') : __('Menunggu') }}
                </flux:badge>
            </flux:text>

            @foreach ($this->permohonan->perjanjian->ulasans as $ulasan)
                <div class="rounded bg-zinc-100 p-2 text-sm dark:bg-zinc-800" wire:key="ulasan-{{ $ulasan->id }}">
                    <span class="font-medium">{{ $ulasan->role?->label() ?? __('Pegawai') }}:</span> {{ $ulasan->ulasan }}
                </div>
            @endforeach
        </flux:card>
    @endif
</div>
