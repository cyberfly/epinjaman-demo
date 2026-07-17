<?php

use App\Actions\Permohonan\MuatNaikSyaratDuluan;
use App\Enums\SyaratDuluanJenis;
use App\Models\Permohonan;
use Flux\Flux;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Locked;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithFileUploads;

new #[Layout('layouts.app')] #[Title('Syarat Duluan')] class extends Component {
    use WithFileUploads;

    #[Locked]
    public int $permohonanId;

    public ?string $jenis = null;

    public $dokumen = null;

    public function mount(Permohonan $permohonan): void
    {
        $this->authorize('view', $permohonan);
        $this->permohonanId = $permohonan->id;
    }

    #[Computed]
    public function permohonan(): ?Permohonan
    {
        return Permohonan::with('syaratDuluans')->find($this->permohonanId);
    }

    public function muatNaik(MuatNaikSyaratDuluan $muatNaikSyaratDuluan): void
    {
        $permohonan = Permohonan::findOrFail($this->permohonanId);
        $this->authorize('uploadSyaratDuluan', $permohonan);

        $this->validate([
            'jenis' => ['required', 'string', 'in:'.implode(',', array_column(SyaratDuluanJenis::cases(), 'value'))],
            'dokumen' => ['required', 'file', 'max:10240'],
        ]);

        $path = $this->dokumen->store('syarat-duluan');

        $muatNaikSyaratDuluan->handle(
            $permohonan,
            auth()->user(),
            SyaratDuluanJenis::from($this->jenis),
            $path,
            $this->dokumen->getClientOriginalName(),
        );

        $this->reset('jenis', 'dokumen');
        unset($this->permohonan);

        Flux::toast(variant: 'success', text: __('Dokumen Syarat Duluan dimuat naik.'));
    }
}; ?>

<div class="flex flex-col gap-6">
    <flux:heading size="xl">{{ __('Syarat Duluan (CP)') }}</flux:heading>

    @if ($this->permohonan?->traffic_light)
        <flux:badge :color="$this->permohonan->traffic_light->color()">{{ $this->permohonan->traffic_light->label() }}</flux:badge>
    @endif

    @can('uploadSyaratDuluan', $this->permohonan)
        <flux:card class="space-y-4">
            <form wire:submit="muatNaik" class="space-y-3">
                <flux:select wire:model="jenis" :label="__('Jenis Syarat Duluan')" placeholder="{{ __('Pilih jenis') }}">
                    @foreach (SyaratDuluanJenis::cases() as $case)
                        <flux:select.option :value="$case->value">{{ $case->label() }}</flux:select.option>
                    @endforeach
                </flux:select>
                <flux:input type="file" wire:model="dokumen" :label="__('Fail dokumen')" />
                <flux:button variant="primary" type="submit" data-test="upload-cp-button">{{ __('Muat Naik') }}</flux:button>
            </form>
        </flux:card>
    @endcan

    <flux:card>
        <flux:table>
            <flux:table.columns>
                <flux:table.column>{{ __('Jenis') }}</flux:table.column>
                <flux:table.column>{{ __('Dokumen') }}</flux:table.column>
                <flux:table.column>{{ __('Status') }}</flux:table.column>
            </flux:table.columns>
            <flux:table.rows>
                @foreach (SyaratDuluanJenis::cases() as $case)
                    @php($item = $this->permohonan?->syaratDuluans->firstWhere('jenis', $case))
                    <flux:table.row wire:key="cp-{{ $case->value }}">
                        <flux:table.cell>{{ $case->label() }}</flux:table.cell>
                        <flux:table.cell>{{ $item?->dokumen_nama ?? '—' }}</flux:table.cell>
                        <flux:table.cell>
                            @if ($item?->disahkan)
                                <flux:badge size="sm" color="green">{{ __('Disahkan') }}</flux:badge>
                            @elseif ($item)
                                <flux:badge size="sm" color="yellow">{{ __('Menunggu semakan') }}</flux:badge>
                            @else
                                <flux:badge size="sm" color="zinc">{{ __('Belum dimuat naik') }}</flux:badge>
                            @endif
                        </flux:table.cell>
                    </flux:table.row>
                @endforeach
            </flux:table.rows>
        </flux:table>
    </flux:card>
</div>
