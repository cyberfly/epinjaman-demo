<?php

use App\Actions\Kelulusan\EndorseMemo;
use App\Actions\Kelulusan\KeputusanKelulusan;
use App\Actions\Kelulusan\PulangkanMemo;
use App\Enums\ApprovalDecision;
use App\Models\Memo;
use Flux\Flux;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Locked;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Layout('layouts.app')] #[Title('Memo Pertimbangan')] class extends Component {
    #[Locked]
    public int $memoId;

    public string $sebab = '';

    public function mount(Memo $memo): void
    {
        $this->authorize('viewHistory', $memo);
        $this->memoId = $memo->id;
    }

    #[Computed]
    public function memo(): ?Memo
    {
        return Memo::with(['permohonan', 'approvalSteps.user'])->find($this->memoId);
    }

    public function endorse(EndorseMemo $endorseMemo): void
    {
        $memo = Memo::with('permohonan')->findOrFail($this->memoId);
        $this->authorize('endorse', $memo);

        $endorseMemo->handle($memo, auth()->user());

        Flux::toast(variant: 'success', text: __('Memo diendors & dihantar ke peringkat seterusnya.'));
        $this->redirectRoute('kelulusan.tray', navigate: true);
    }

    public function pulangkan(PulangkanMemo $pulangkanMemo): void
    {
        $memo = Memo::with('permohonan')->findOrFail($this->memoId);
        $this->authorize('returnToPrevious', $memo);

        $this->validate(['sebab' => ['required', 'string']]);

        $pulangkanMemo->handle($memo, auth()->user(), $this->sebab);

        Flux::toast(variant: 'warning', text: __('Memo dipulangkan untuk pembetulan.'));
        $this->redirectRoute('kelulusan.tray', navigate: true);
    }

    public function lulus(KeputusanKelulusan $keputusanKelulusan): void
    {
        $memo = Memo::with('permohonan')->findOrFail($this->memoId);
        $this->authorize('decide', $memo);

        $keputusanKelulusan->handle($memo, auth()->user(), ApprovalDecision::Lulus);

        Flux::toast(variant: 'success', text: __('Permohonan diluluskan.'));
        $this->redirectRoute('kelulusan.tray', navigate: true);
    }

    public function tidakLulus(KeputusanKelulusan $keputusanKelulusan): void
    {
        $memo = Memo::with('permohonan')->findOrFail($this->memoId);
        $this->authorize('decide', $memo);

        $keputusanKelulusan->handle($memo, auth()->user(), ApprovalDecision::TidakLulus, $this->sebab !== '' ? $this->sebab : null);

        Flux::toast(variant: 'warning', text: __('Permohonan tidak diluluskan. Dikembalikan ke Rundingan.'));
        $this->redirectRoute('kelulusan.tray', navigate: true);
    }
}; ?>

<div class="flex flex-col gap-6">
    <flux:heading size="xl">{{ __('Memo Pertimbangan') }}</flux:heading>

    <flux:card class="space-y-3">
        <flux:text>{{ __('No. Rujukan') }}: {{ $this->memo?->permohonan->no_rujukan ?? '—' }}</flux:text>
        <flux:text>{{ __('Peringkat semasa') }}: <flux:badge size="sm">{{ $this->memo?->peringkat->label() }}</flux:badge></flux:text>
        <flux:heading size="sm">{{ __('Terma & Syarat Utama') }}</flux:heading>
        <flux:text class="whitespace-pre-line">{{ $this->memo?->terma_utama }}</flux:text>
    </flux:card>

    @if ($this->memo)
        <flux:card class="space-y-4">
            @can('returnToPrevious', $this->memo)
                <flux:input wire:model="sebab" :label="__('Sebab pemulangan / catatan')" />
            @elsecan('decide', $this->memo)
                <flux:input wire:model="sebab" :label="__('Catatan keputusan (pilihan)')" />
            @endcan

            <div class="flex flex-wrap gap-2">
                @can('endorse', $this->memo)
                    <flux:button variant="primary" wire:click="endorse" data-test="endorse-button">{{ __('Endorse & Hantar') }}</flux:button>
                @endcan
                @can('returnToPrevious', $this->memo)
                    <flux:button variant="danger" wire:click="pulangkan" data-test="pulangkan-button">{{ __('Pulangkan untuk Pembetulan') }}</flux:button>
                @endcan
                @can('decide', $this->memo)
                    <flux:button variant="primary" wire:click="lulus" data-test="lulus-button">{{ __('Lulus') }}</flux:button>
                    <flux:button variant="danger" wire:click="tidakLulus" data-test="tidak-lulus-button">{{ __('Tidak Lulus') }}</flux:button>
                @endcan
            </div>
        </flux:card>
    @endif

    <flux:card>
        <flux:heading size="sm">{{ __('Sejarah Pergerakan Memo') }}</flux:heading>
        <ul class="mt-3 space-y-2 text-sm">
            @foreach ($this->memo?->approvalSteps ?? [] as $step)
                <li wire:key="step-{{ $step->id }}">
                    <span class="font-medium">{{ $step->peringkat->label() }}</span> —
                    {{ $step->tindakan->label() }}
                    @if ($step->sebab)
                        <span class="opacity-70">({{ $step->sebab }})</span>
                    @endif
                    <span class="opacity-50">· {{ $step->created_at?->diffForHumans() }}</span>
                </li>
            @endforeach
        </ul>
    </flux:card>
</div>
