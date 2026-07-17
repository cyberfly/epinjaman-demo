<?php

use App\Actions\Kuiri\CetusKuiri;
use App\Actions\Kuiri\TandakanKuiri;
use App\Actions\Kuiri\TeruskanKeRundinganDariKuiri;
use App\Models\Kuiri;
use App\Models\Permohonan;
use Flux\Flux;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Locked;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Layout('layouts.app')] #[Title('Modul Kuiri')] class extends Component {
    #[Locked]
    public int $permohonanId;

    public string $tajuk = '';

    public string $sebab = '';

    public function mount(Permohonan $permohonan): void
    {
        $this->authorize('manageKuiri', $permohonan);
        $this->permohonanId = $permohonan->id;
    }

    #[Computed]
    public function permohonan(): ?Permohonan
    {
        return Permohonan::with(['kuiris.replies'])->find($this->permohonanId);
    }

    public function cetus(CetusKuiri $cetusKuiri): void
    {
        $permohonan = Permohonan::findOrFail($this->permohonanId);
        $this->authorize('manageKuiri', $permohonan);

        $this->validate([
            'tajuk' => ['required', 'string', 'max:255'],
            'sebab' => ['required', 'string'],
        ]);

        $cetusKuiri->handle($permohonan, auth()->user(), $this->tajuk, $this->sebab);

        $this->reset('tajuk', 'sebab');
        unset($this->permohonan);

        Flux::toast(variant: 'success', text: __('Kuiri dicetuskan.'));
    }

    public function berpuasHati(int $kuiriId, TandakanKuiri $tandakanKuiri): void
    {
        $kuiri = Kuiri::with('permohonan')->findOrFail($kuiriId);
        $this->authorize('manageKuiri', $kuiri->permohonan);

        $tandakanKuiri->berpuasHati($kuiri);
        unset($this->permohonan);
    }

    public function tidakBerpuasHati(int $kuiriId, TandakanKuiri $tandakanKuiri): void
    {
        $kuiri = Kuiri::with('permohonan')->findOrFail($kuiriId);
        $this->authorize('manageKuiri', $kuiri->permohonan);

        $tandakanKuiri->tidakBerpuasHati($kuiri);
        unset($this->permohonan);
    }

    public function teruskan(TeruskanKeRundinganDariKuiri $teruskanKeRundinganDariKuiri): void
    {
        $permohonan = Permohonan::findOrFail($this->permohonanId);
        $this->authorize('manageKuiri', $permohonan);

        try {
            $teruskanKeRundinganDariKuiri->handle($permohonan);
        } catch (\DomainException $e) {
            Flux::toast(variant: 'danger', text: $e->getMessage());

            return;
        }

        Flux::toast(variant: 'success', text: __('Semua Kuiri selesai. Permohonan diteruskan ke Rundingan.'));

        $this->redirectRoute('sid.tray', navigate: true);
    }
}; ?>

<div class="flex flex-col gap-6">
    <flux:heading size="xl">{{ __('Modul Kuiri') }}</flux:heading>

    <flux:card class="space-y-4">
        <flux:heading>{{ __('Cetus Kuiri Baharu') }}</flux:heading>
        <form wire:submit="cetus" class="space-y-4">
            <flux:input wire:model="tajuk" :label="__('Tajuk isu')" required />
            <flux:textarea wire:model="sebab" :label="__('Sebab / butiran')" required />
            <flux:button variant="primary" type="submit" data-test="cetus-kuiri-button">{{ __('Cetus Kuiri') }}</flux:button>
        </form>
    </flux:card>

    <flux:card class="space-y-4">
        <flux:heading>{{ __('Senarai Kuiri') }}</flux:heading>
        @forelse ($this->permohonan?->kuiris ?? [] as $kuiri)
            <div class="space-y-2 border-b border-zinc-200 pb-4 dark:border-zinc-700" wire:key="kuiri-{{ $kuiri->id }}">
                <div class="flex items-center justify-between">
                    <flux:heading size="sm">{{ $kuiri->tajuk }}</flux:heading>
                    <flux:badge size="sm">{{ $kuiri->status->label() }}</flux:badge>
                </div>
                <flux:text>{{ $kuiri->sebab }}</flux:text>

                @foreach ($kuiri->replies as $reply)
                    <div class="rounded bg-zinc-100 p-2 text-sm dark:bg-zinc-800" wire:key="reply-{{ $reply->id }}">
                        <span class="font-medium">{{ $reply->role?->label() ?? __('Pengguna') }}:</span>
                        {{ $reply->mesej }}
                        @if ($reply->lampiran_nama)
                            <span class="opacity-70">({{ $reply->lampiran_nama }})</span>
                        @endif
                    </div>
                @endforeach

                @unless ($kuiri->isSatisfied())
                    <div class="flex gap-2">
                        <flux:button size="sm" variant="primary" wire:click="berpuasHati({{ $kuiri->id }})">{{ __('Berpuas Hati') }}</flux:button>
                        <flux:button size="sm" variant="danger" wire:click="tidakBerpuasHati({{ $kuiri->id }})">{{ __('Tidak Berpuas Hati') }}</flux:button>
                    </div>
                @endunless
            </div>
        @empty
            <flux:text variant="subtle">{{ __('Tiada kuiri lagi.') }}</flux:text>
        @endforelse

        <flux:button variant="primary" wire:click="teruskan" data-test="teruskan-rundingan-button">
            {{ __('Teruskan ke Rundingan') }}
        </flux:button>
    </flux:card>
</div>
