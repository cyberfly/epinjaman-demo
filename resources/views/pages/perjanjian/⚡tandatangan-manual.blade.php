<?php

use App\Actions\Permohonan\RekodPenyeteman;
use App\Actions\Permohonan\RekodTandatanganManual;
use App\Models\Permohonan;
use Flux\Flux;
use Illuminate\Support\Facades\Gate;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Locked;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithFileUploads;

new #[Layout('layouts.app')] #[Title('Tandatangan Manual & Penyeteman')] class extends Component {
    use WithFileUploads;

    #[Locked]
    public int $permohonanId;

    public string $tarikhTandatangan = '';

    public $imbasan = null;

    public string $tarikhPenyeteman = '';

    public function mount(Permohonan $permohonan): void
    {
        abort_unless(
            Gate::allows('recordTandatanganManual', $permohonan) || Gate::allows('recordPenyeteman', $permohonan),
            403,
        );
        $this->permohonanId = $permohonan->id;
    }

    #[Computed]
    public function permohonan(): ?Permohonan
    {
        return Permohonan::with('perjanjian')->find($this->permohonanId);
    }

    public function rekodTandatangan(RekodTandatanganManual $rekodTandatanganManual): void
    {
        $permohonan = Permohonan::with('perjanjian')->findOrFail($this->permohonanId);
        Gate::authorize('recordTandatanganManual', $permohonan);

        $this->validate(['tarikhTandatangan' => ['required', 'date'], 'imbasan' => ['nullable', 'file', 'max:10240']]);

        $path = $this->imbasan?->store('perjanjian-imbasan');

        $rekodTandatanganManual->handle($permohonan, auth()->user(), $this->tarikhTandatangan, $path);

        $this->reset('imbasan');
        unset($this->permohonan);

        Flux::toast(variant: 'success', text: __('Sesi Tandatangan Manual direkod. Organisasi kini berstatus Peminjam.'));
    }

    public function rekodPenyeteman(RekodPenyeteman $rekodPenyeteman): void
    {
        $permohonan = Permohonan::with('perjanjian')->findOrFail($this->permohonanId);
        Gate::authorize('recordPenyeteman', $permohonan);

        $this->validate(['tarikhPenyeteman' => ['required', 'date']]);

        $rekodPenyeteman->handle($permohonan, auth()->user(), $this->tarikhPenyeteman);

        Flux::toast(variant: 'success', text: __('Penyeteman LHDNM direkod. Permohonan bersedia untuk Syarat Duluan.'));
        $this->redirectRoute('sid.tray', navigate: true);
    }
}; ?>

<div class="flex flex-col gap-6">
    <flux:heading size="xl">{{ __('Tandatangan Manual & Penyeteman LHDNM') }}</flux:heading>

    @can('recordTandatanganManual', $this->permohonan)
        <flux:card class="space-y-4">
            <flux:heading size="sm">{{ __('Sesi Tandatangan Manual') }}</flux:heading>
            <flux:text variant="subtle">{{ __('1 Set Asal Dimatikan Setem + 8 Set Salinan Asal') }}</flux:text>
            @if ($this->permohonan?->perjanjian?->tandatangan_manual_selesai)
                <flux:badge color="green">{{ __('Direkod pada') }} {{ $this->permohonan->perjanjian->tandatangan_manual_tarikh?->format('d/m/Y') }}</flux:badge>
            @else
                <form wire:submit="rekodTandatangan" class="space-y-3">
                    <flux:input type="date" wire:model="tarikhTandatangan" :label="__('Tarikh sesi tandatangan')" />
                    <flux:input type="file" wire:model="imbasan" :label="__('Imbasan dokumen (pilihan)')" />
                    <flux:button variant="primary" type="submit" data-test="rekod-tandatangan-button">{{ __('Rekod Selesai') }}</flux:button>
                </form>
            @endif
        </flux:card>
    @endcan

    <flux:card class="space-y-4">
        <flux:heading size="sm">{{ __('Penyeteman & Pengesahan Cap Duti Setem LHDNM') }}</flux:heading>
        @if ($this->permohonan?->perjanjian?->penyeteman_selesai)
            <flux:badge color="green">{{ __('Direkod pada') }} {{ $this->permohonan->perjanjian->penyeteman_tarikh?->format('d/m/Y') }}</flux:badge>
        @elseif ($this->permohonan?->perjanjian?->tandatangan_manual_selesai)
            @can('recordPenyeteman', $this->permohonan)
                <form wire:submit="rekodPenyeteman" class="space-y-3">
                    <flux:input type="date" wire:model="tarikhPenyeteman" :label="__('Tarikh penyeteman')" />
                    <flux:button variant="primary" type="submit" data-test="rekod-penyeteman-button">{{ __('Rekod Penyeteman') }}</flux:button>
                </form>
            @endcan
        @else
            <flux:text variant="subtle">{{ __('Sila lengkapkan Sesi Tandatangan Manual dahulu.') }}</flux:text>
        @endif
    </flux:card>
</div>
