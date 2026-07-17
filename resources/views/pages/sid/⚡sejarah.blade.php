<?php

use App\Models\Permohonan;
use Illuminate\Support\Facades\Gate;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Locked;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Layout('layouts.app')] #[Title('Sejarah Permohonan')] class extends Component {
    #[Locked]
    public int $permohonanId;

    public function mount(Permohonan $permohonan): void
    {
        Gate::authorize('viewHistory', $permohonan);
        $this->permohonanId = $permohonan->id;
    }

    #[Computed]
    public function permohonan(): ?Permohonan
    {
        return Permohonan::with([
            'signatures.user',
            'kuiris.replies',
            'memo.approvalSteps.user',
        ])->find($this->permohonanId);
    }
}; ?>

<div class="flex flex-col gap-6">
    <flux:heading size="xl">{{ __('Sejarah Penuh Permohonan') }} — {{ $this->permohonan?->no_rujukan ?? '—' }}</flux:heading>
    <flux:badge>{{ $this->permohonan?->status->label() }}</flux:badge>

    <flux:card class="space-y-2">
        <flux:heading size="sm">{{ __('Tandatangan Digital') }}</flux:heading>
        @forelse ($this->permohonan?->signatures ?? [] as $signature)
            <div class="text-sm" wire:key="sig-{{ $signature->id }}">
                {{ $signature->stage->label() }} — {{ $signature->role?->label() }}
                <span class="opacity-50">· {{ $signature->signed_at?->format('d/m/Y H:i') }}</span>
            </div>
        @empty
            <flux:text variant="subtle">{{ __('Tiada tandatangan.') }}</flux:text>
        @endforelse
    </flux:card>

    <flux:card class="space-y-2">
        <flux:heading size="sm">{{ __('Kuiri') }}</flux:heading>
        @forelse ($this->permohonan?->kuiris ?? [] as $kuiri)
            <div class="text-sm" wire:key="hist-kuiri-{{ $kuiri->id }}">
                {{ $kuiri->tajuk }} — <flux:badge size="sm">{{ $kuiri->status->label() }}</flux:badge>
                <span class="opacity-50">· {{ $kuiri->replies->count() }} {{ __('balasan') }}</span>
            </div>
        @empty
            <flux:text variant="subtle">{{ __('Tiada kuiri.') }}</flux:text>
        @endforelse
    </flux:card>

    <flux:card class="space-y-2">
        <flux:heading size="sm">{{ __('Pergerakan Memo (Hierarki Kelulusan)') }}</flux:heading>
        @forelse ($this->permohonan?->memo?->approvalSteps ?? [] as $step)
            <div class="text-sm" wire:key="hist-step-{{ $step->id }}">
                {{ $step->peringkat->label() }} — {{ $step->tindakan->label() }}
                @if ($step->sebab)<span class="opacity-70">({{ $step->sebab }})</span>@endif
                <span class="opacity-50">· {{ $step->created_at?->format('d/m/Y H:i') }}</span>
            </div>
        @empty
            <flux:text variant="subtle">{{ __('Tiada pergerakan memo.') }}</flux:text>
        @endforelse
    </flux:card>
</div>
