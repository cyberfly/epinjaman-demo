<?php

use App\Actions\Permohonan\SignStageKedua;
use App\Models\Permohonan;
use Flux\Flux;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Locked;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Layout('layouts.app')] #[Title('Tandatangan Peringkat 2')] class extends Component {
    #[Locked]
    public int $permohonanId;

    public string $tajuk = '';

    public function mount(Permohonan $permohonan): void
    {
        $this->authorize('signStageKedua', $permohonan);

        $this->permohonanId = $permohonan->id;
        $this->tajuk = (string) $permohonan->tajuk;
    }

    public function sign(SignStageKedua $signStageKedua): void
    {
        $permohonan = Permohonan::findOrFail($this->permohonanId);
        $this->authorize('signStageKedua', $permohonan);

        $signStageKedua->handle($permohonan, auth()->user());

        Flux::toast(variant: 'success', text: __('Tandatangan Peringkat 2 direkod. Permohonan dihantar ke SID.'));

        $this->redirectRoute('kementerian.tray', navigate: true);
    }
}; ?>

<div class="flex flex-col gap-6">
    <flux:heading size="xl">{{ __('Tandatangan Digital Peringkat 2') }}</flux:heading>
    <flux:text>{{ $tajuk }}</flux:text>

    <flux:card class="space-y-4">
        <flux:text>{{ __('Sila semak permohonan dan turunkan Tandatangan Digital Peringkat 2 ("Disemak" & "Diperaku"). Tindakan ini dibenarkan tanpa mengira status Traffic Light.') }}</flux:text>
        <flux:button variant="primary" wire:click="sign" data-test="sign-p2-button">
            {{ __('Turunkan Tandatangan (Disemak & Diperaku)') }}
        </flux:button>
    </flux:card>
</div>
