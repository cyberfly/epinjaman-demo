<?php

use App\Actions\Permohonan\KunciPermohonanLengkap;
use App\Actions\Permohonan\SahkanCS;
use App\Actions\Permohonan\SahkanItemSyaratDuluan;
use App\Enums\SyaratDuluanJenis;
use App\Models\Permohonan;
use App\Models\SyaratDuluan;
use Flux\Flux;
use Illuminate\Support\Facades\Gate;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Locked;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Layout('layouts.app')] #[Title('Semakan Syarat Duluan')] class extends Component {
    #[Locked]
    public int $permohonanId;

    public function mount(Permohonan $permohonan): void
    {
        Gate::authorize('manageSyaratDuluan', $permohonan);
        $this->permohonanId = $permohonan->id;
    }

    #[Computed]
    public function permohonan(): ?Permohonan
    {
        return Permohonan::with('syaratDuluans')->find($this->permohonanId);
    }

    public function sahkanItem(int $itemId, SahkanItemSyaratDuluan $sahkanItemSyaratDuluan): void
    {
        $item = SyaratDuluan::with('permohonan')->findOrFail($itemId);
        Gate::authorize('manageSyaratDuluan', $item->permohonan);

        $sahkanItemSyaratDuluan->handle($item, auth()->user());
        unset($this->permohonan);
    }

    public function sahkanCS(SahkanCS $sahkanCS): void
    {
        $permohonan = Permohonan::findOrFail($this->permohonanId);
        Gate::authorize('manageSyaratDuluan', $permohonan);

        $sahkanCS->handle($permohonan);
        unset($this->permohonan);

        Flux::toast(variant: 'success', text: __('Syarat Kemudian (CS) disahkan.'));
    }

    public function kunci(KunciPermohonanLengkap $kunciPermohonanLengkap): void
    {
        $permohonan = Permohonan::findOrFail($this->permohonanId);
        Gate::authorize('manageSyaratDuluan', $permohonan);

        try {
            $kunciPermohonanLengkap->handle($permohonan);
        } catch (\DomainException $e) {
            Flux::toast(variant: 'danger', text: $e->getMessage());

            return;
        }

        Flux::toast(variant: 'success', text: __('Permohonan dikunci LENGKAP.'));
        $this->redirectRoute('sid.tray', navigate: true);
    }
}; ?>

<div class="flex flex-col gap-6">
    <flux:heading size="xl">{{ __('Semakan Syarat Duluan (CP)') }}</flux:heading>

    <flux:card>
        <flux:table>
            <flux:table.columns>
                <flux:table.column>{{ __('Jenis') }}</flux:table.column>
                <flux:table.column>{{ __('Dokumen') }}</flux:table.column>
                <flux:table.column>{{ __('Status') }}</flux:table.column>
                <flux:table.column></flux:table.column>
            </flux:table.columns>
            <flux:table.rows>
                @foreach (SyaratDuluanJenis::cases() as $case)
                    @php($item = $this->permohonan?->syaratDuluans->firstWhere('jenis', $case))
                    <flux:table.row wire:key="cp-{{ $case->value }}">
                        <flux:table.cell>{{ $case->label() }}</flux:table.cell>
                        <flux:table.cell>{{ $item?->dokumen_nama ?? '—' }}</flux:table.cell>
                        <flux:table.cell>
                            <flux:badge size="sm" :color="$item?->disahkan ? 'green' : 'zinc'">
                                {{ $item?->disahkan ? __('Disahkan') : __('Belum') }}
                            </flux:badge>
                        </flux:table.cell>
                        <flux:table.cell>
                            @if ($item && ! $item->disahkan)
                                <flux:button size="sm" variant="primary" wire:click="sahkanItem({{ $item->id }})" data-test="verify-cp-{{ $case->value }}">
                                    {{ __('Sahkan') }}
                                </flux:button>
                            @endif
                        </flux:table.cell>
                    </flux:table.row>
                @endforeach
            </flux:table.rows>
        </flux:table>
    </flux:card>

    <flux:card class="space-y-4">
        <div class="flex items-center gap-3">
            <flux:heading size="sm">{{ __('Syarat Kemudian (CS)') }}</flux:heading>
            <flux:badge size="sm" :color="$this->permohonan?->cs_disahkan ? 'green' : 'zinc'">
                {{ $this->permohonan?->cs_disahkan ? __('Disahkan') : __('Belum') }}
            </flux:badge>
        </div>
        @unless ($this->permohonan?->cs_disahkan)
            <flux:button variant="filled" wire:click="sahkanCS" data-test="confirm-cs-button">{{ __('Tandakan CS Disahkan') }}</flux:button>
        @endunless

        <flux:button variant="primary" wire:click="kunci" data-test="lock-lengkap-button">
            {{ __('Kunci Permohonan LENGKAP') }}
        </flux:button>
    </flux:card>
</div>
