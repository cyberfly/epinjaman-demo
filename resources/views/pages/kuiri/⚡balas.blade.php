<?php

use App\Actions\Kuiri\BalasKuiri;
use App\Models\Kuiri;
use Flux\Flux;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Locked;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithFileUploads;

new #[Layout('layouts.app')] #[Title('Balas Kuiri')] class extends Component {
    use WithFileUploads;

    #[Locked]
    public int $kuiriId;

    public string $mesej = '';

    public $lampiran = null;

    public function mount(Kuiri $kuiri): void
    {
        $this->authorize('replyKuiri', $kuiri->permohonan);
        $this->kuiriId = $kuiri->id;
    }

    #[Computed]
    public function kuiri(): ?Kuiri
    {
        return Kuiri::with('replies')->find($this->kuiriId);
    }

    public function balas(BalasKuiri $balasKuiri): void
    {
        $kuiri = Kuiri::with('permohonan')->findOrFail($this->kuiriId);
        $this->authorize('replyKuiri', $kuiri->permohonan);

        $this->validate([
            'mesej' => ['nullable', 'string'],
            'lampiran' => ['nullable', 'file', 'max:10240'],
        ]);

        $path = null;
        $nama = null;

        if ($this->lampiran !== null) {
            $path = $this->lampiran->store('kuiri-lampiran');
            $nama = $this->lampiran->getClientOriginalName();
        }

        $balasKuiri->handle($kuiri, auth()->user(), $this->mesej !== '' ? $this->mesej : null, $path, $nama);

        $this->reset('mesej', 'lampiran');
        unset($this->kuiri);

        Flux::toast(variant: 'success', text: __('Balasan dihantar.'));
    }
}; ?>

<div class="flex flex-col gap-6">
    <flux:heading size="xl">{{ __('Balas Kuiri') }}</flux:heading>

    <flux:card class="space-y-3">
        <flux:heading size="sm">{{ $this->kuiri?->tajuk }}</flux:heading>
        <flux:text>{{ $this->kuiri?->sebab }}</flux:text>

        @foreach ($this->kuiri?->replies ?? [] as $reply)
            <div class="rounded bg-zinc-100 p-2 text-sm dark:bg-zinc-800" wire:key="reply-{{ $reply->id }}">
                <span class="font-medium">{{ $reply->role?->label() ?? __('Pengguna') }}:</span> {{ $reply->mesej }}
                @if ($reply->lampiran_nama)
                    <span class="opacity-70">({{ $reply->lampiran_nama }})</span>
                @endif
            </div>
        @endforeach
    </flux:card>

    <flux:card class="space-y-4">
        <form wire:submit="balas" class="space-y-4">
            <flux:textarea wire:model="mesej" :label="__('Mesej balasan')" />
            <flux:input type="file" wire:model="lampiran" :label="__('Lampiran (pilihan)')" />
            <flux:button variant="primary" type="submit" data-test="balas-kuiri-button">{{ __('Hantar Balasan') }}</flux:button>
        </form>
    </flux:card>
</div>
