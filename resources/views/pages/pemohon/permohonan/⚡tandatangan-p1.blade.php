<?php

use App\Actions\Permohonan\CheckPermohonanCompleteness;
use App\Actions\Permohonan\ReturnPermohonanToDraft;
use App\Actions\Permohonan\SignStagePertama;
use App\Models\Permohonan;
use Flux\Flux;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Locked;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Layout('layouts.app')] #[Title('Tandatangan Peringkat 1')] class extends Component {
    #[Locked]
    public int $permohonanId;

    public string $tajuk = '';

    public bool $isComplete = false;

    /** @var array<int, string> */
    public array $missingFields = [];

    /** @var array<int, string> */
    public array $missingDocuments = [];

    public function mount(Permohonan $permohonan, CheckPermohonanCompleteness $checkPermohonanCompleteness): void
    {
        $this->authorize('signStagePertama', $permohonan);

        $this->permohonanId = $permohonan->id;
        $this->tajuk = (string) $permohonan->tajuk;

        $result = $checkPermohonanCompleteness->handle($permohonan);
        $this->isComplete = $result->isComplete();
        $this->missingFields = $result->missingFields;
        $this->missingDocuments = $result->missingDocuments;
    }

    public function sign(SignStagePertama $signStagePertama): void
    {
        $permohonan = Permohonan::findOrFail($this->permohonanId);
        $this->authorize('signStagePertama', $permohonan);

        $signStagePertama->handle($permohonan, auth()->user());

        Flux::toast(variant: 'success', text: __('Tandatangan Peringkat 1 direkod. Permohonan dihantar ke Kementerian Pengawal.'));

        $this->redirectRoute('permohonan.index', navigate: true);
    }

    public function returnToDraft(ReturnPermohonanToDraft $returnPermohonanToDraft): void
    {
        $permohonan = Permohonan::findOrFail($this->permohonanId);
        $this->authorize('signStagePertama', $permohonan);

        $returnPermohonanToDraft->handle($permohonan);

        Flux::toast(variant: 'warning', text: __('Permohonan dipulangkan ke draf untuk pembetulan.'));

        $this->redirectRoute('permohonan.borang', $permohonan, navigate: true);
    }
}; ?>

<div class="flex flex-col gap-6">
    <flux:heading size="xl">{{ __('Tandatangan Digital Peringkat 1') }}</flux:heading>
    <flux:text>{{ $tajuk }}</flux:text>

    @if ($isComplete)
        <flux:card class="space-y-4">
            <flux:text>{{ __('Borang & dokumen lengkap. Sila turunkan Tandatangan Digital Peringkat 1 ("Disediakan" & "Disahkan").') }}</flux:text>
            <flux:button variant="primary" wire:click="sign" data-test="sign-p1-button">
                {{ __('Turunkan Tandatangan (Disediakan & Disahkan)') }}
            </flux:button>
        </flux:card>
    @else
        <flux:card class="space-y-4">
            <flux:callout variant="warning" :heading="__('Borang belum lengkap')" />

            @if ($missingFields)
                <div>
                    <flux:heading size="sm">{{ __('Medan yang tiada') }}</flux:heading>
                    <ul class="list-disc ps-5">
                        @foreach ($missingFields as $field)
                            <li>{{ $field }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @if ($missingDocuments)
                <div>
                    <flux:heading size="sm">{{ __('Dokumen yang tiada') }}</flux:heading>
                    <ul class="list-disc ps-5">
                        @foreach ($missingDocuments as $document)
                            <li>{{ $document }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <flux:button variant="filled" wire:click="returnToDraft" data-test="return-to-draft-button">
                {{ __('Pulangkan untuk Pembetulan') }}
            </flux:button>
        </flux:card>
    @endif
</div>
