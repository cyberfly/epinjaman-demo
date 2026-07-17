<?php

use App\Actions\Permohonan\RekodRundingan;
use App\Actions\Permohonan\SediakanMemo;
use App\Models\Permohonan;
use Flux\Flux;
use Illuminate\Support\Facades\Gate;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Locked;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Layout('layouts.app')] #[Title('Rundingan & Memo Pertimbangan')] class extends Component {
    #[Locked]
    public int $permohonanId;

    public string $terma = '';

    public string $catatan = '';

    public string $termaUtama = '';

    public string $memoCatatan = '';

    public function mount(Permohonan $permohonan): void
    {
        Gate::authorize('recordRundingan', $permohonan);
        $this->permohonanId = $permohonan->id;
    }

    #[Computed]
    public function permohonan(): ?Permohonan
    {
        return Permohonan::with('rundingans')->find($this->permohonanId);
    }

    public function rekod(RekodRundingan $rekodRundingan): void
    {
        $permohonan = Permohonan::findOrFail($this->permohonanId);
        Gate::authorize('recordRundingan', $permohonan);

        $this->validate(['terma' => ['required', 'string']]);

        $rekodRundingan->handle($permohonan, auth()->user(), $this->terma, $this->catatan !== '' ? $this->catatan : null);

        $this->reset('terma', 'catatan');
        unset($this->permohonan);

        Flux::toast(variant: 'success', text: __('Rundingan direkod.'));
    }

    public function sediakanMemo(SediakanMemo $sediakanMemo): void
    {
        $permohonan = Permohonan::findOrFail($this->permohonanId);
        Gate::authorize('prepareMemo', $permohonan);

        $this->validate(['termaUtama' => ['required', 'string']]);

        $sediakanMemo->handle($permohonan, auth()->user(), $this->termaUtama, $this->memoCatatan !== '' ? $this->memoCatatan : null);

        Flux::toast(variant: 'success', text: __('Memo Pertimbangan disediakan. Permohonan dihantar ke hierarki kelulusan.'));

        $this->redirectRoute('sid.tray', navigate: true);
    }
}; ?>

<div class="flex flex-col gap-6">
    <flux:heading size="xl">{{ __('Rundingan & Memo Pertimbangan') }}</flux:heading>

    <flux:card class="space-y-4">
        <flux:heading>{{ __('Rekod Rundingan') }}</flux:heading>
        <form wire:submit="rekod" class="space-y-4">
            <flux:textarea wire:model="terma" :label="__('Terma & syarat dipersetujui')" required />
            <flux:input wire:model="catatan" :label="__('Catatan')" />
            <flux:button variant="primary" type="submit" data-test="rekod-rundingan-button">{{ __('Rekod Rundingan') }}</flux:button>
        </form>

        @if ($this->permohonan?->rundingans->isNotEmpty())
            <ul class="list-disc ps-5 text-sm">
                @foreach ($this->permohonan->rundingans as $rundingan)
                    <li wire:key="rundingan-{{ $rundingan->id }}">{{ $rundingan->terma }}</li>
                @endforeach
            </ul>
        @endif
    </flux:card>

    <flux:card class="space-y-4">
        <flux:heading>{{ __('Sediakan Memo Pertimbangan') }}</flux:heading>
        <form wire:submit="sediakanMemo" class="space-y-4">
            <flux:textarea wire:model="termaUtama" :label="__('Draf Terma & Syarat Utama')" required />
            <flux:input wire:model="memoCatatan" :label="__('Catatan memo')" />
            <flux:button variant="primary" type="submit" data-test="sediakan-memo-button">{{ __('Sediakan Memo & Hantar ke Kelulusan') }}</flux:button>
        </form>
    </flux:card>
</div>
