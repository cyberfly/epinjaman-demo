<?php

use App\Actions\Permohonan\SahkanDokumenLengkap;
use App\Actions\Permohonan\SaveDocumentReview;
use App\Actions\Permohonan\TandakanDokumenPerluKuiri;
use App\Models\ChecklistItem;
use App\Models\Permohonan;
use Flux\Flux;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Locked;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Layout('layouts.app')] #[Title('Semakan Dokumen')] class extends Component {
    #[Locked]
    public int $permohonanId;

    public string $noRujukan = '';

    /**
     * Keyed by checklist_item_id.
     *
     * @var array<int, array{is_complete: bool, is_orderly: bool, catatan: string}>
     */
    public array $reviews = [];

    public function mount(Permohonan $permohonan): void
    {
        $this->authorize('reviewDocuments', $permohonan);

        $this->permohonanId = $permohonan->id;
        $this->noRujukan = (string) $permohonan->no_rujukan;

        $existing = $permohonan->documentReviews()->get()->keyBy('checklist_item_id');

        foreach (ChecklistItem::active() as $item) {
            $review = $existing->get($item->id);
            $this->reviews[$item->id] = [
                'is_complete' => (bool) ($review?->is_complete ?? false),
                'is_orderly' => (bool) ($review?->is_orderly ?? false),
                'catatan' => (string) ($review?->catatan ?? ''),
            ];
        }
    }

    /**
     * @return \Illuminate\Database\Eloquent\Collection<int, ChecklistItem>
     */
    #[Computed]
    public function checklistItems()
    {
        return ChecklistItem::active();
    }

    #[Computed]
    public function permohonan(): ?Permohonan
    {
        return Permohonan::with('documents.checklistItem')->find($this->permohonanId);
    }

    private function persistReviews(SaveDocumentReview $saveDocumentReview): Permohonan
    {
        $permohonan = Permohonan::findOrFail($this->permohonanId);
        $this->authorize('reviewDocuments', $permohonan);

        foreach ($this->reviews as $itemId => $review) {
            $item = ChecklistItem::find($itemId);

            if ($item === null) {
                continue;
            }

            $saveDocumentReview->handle(
                $permohonan,
                $item,
                auth()->user(),
                (bool) $review['is_complete'],
                (bool) $review['is_orderly'],
                $review['catatan'] !== '' ? $review['catatan'] : null,
            );
        }

        return $permohonan;
    }

    public function simpanSemakan(SaveDocumentReview $saveDocumentReview): void
    {
        $this->persistReviews($saveDocumentReview);

        Flux::toast(variant: 'success', text: __('Semakan disimpan.'));
    }

    public function sahkanLengkap(SaveDocumentReview $saveDocumentReview, SahkanDokumenLengkap $sahkanDokumenLengkap): void
    {
        $permohonan = $this->persistReviews($saveDocumentReview);

        $sahkanDokumenLengkap->handle($permohonan);

        Flux::toast(variant: 'success', text: __('Dokumen disahkan lengkap. Permohonan diteruskan ke Rundingan.'));

        $this->redirectRoute('sid.tray', navigate: true);
    }

    public function tandakanPerluKuiri(SaveDocumentReview $saveDocumentReview, TandakanDokumenPerluKuiri $tandakanDokumenPerluKuiri): void
    {
        $permohonan = $this->persistReviews($saveDocumentReview);

        $tandakanDokumenPerluKuiri->handle($permohonan);

        Flux::toast(variant: 'warning', text: __('Permohonan ditandakan memerlukan Kuiri.'));

        $this->redirectRoute('sid.tray', navigate: true);
    }
}; ?>

<div class="flex flex-col gap-6">
    <flux:heading size="xl">{{ __('Semakan Senarai Semak Dokumen') }}</flux:heading>
    <flux:text>{{ $noRujukan }}</flux:text>

    <flux:card class="space-y-6">
        @foreach ($this->checklistItems as $item)
            <div class="space-y-2 border-b border-zinc-200 pb-4 dark:border-zinc-700" wire:key="review-{{ $item->id }}">
                <flux:heading size="sm">{{ $item->label }}</flux:heading>

                @php($docs = $this->permohonan?->documents->where('checklist_item_id', $item->id) ?? collect())
                @if ($docs->isNotEmpty())
                    <ul class="list-disc ps-5 text-sm">
                        @foreach ($docs as $doc)
                            <li>{{ $doc->original_name }}</li>
                        @endforeach
                    </ul>
                @else
                    <flux:text variant="subtle">{{ __('Tiada dokumen dimuat naik.') }}</flux:text>
                @endif

                <div class="flex gap-6">
                    <flux:switch wire:model="reviews.{{ $item->id }}.is_complete" :label="__('Lengkap')" />
                    <flux:switch wire:model="reviews.{{ $item->id }}.is_orderly" :label="__('Teratur')" />
                </div>
                <flux:input wire:model="reviews.{{ $item->id }}.catatan" :label="__('Catatan')" />
            </div>
        @endforeach

        <div class="flex flex-wrap gap-2">
            <flux:button wire:click="simpanSemakan" data-test="save-review-button">{{ __('Simpan Semakan') }}</flux:button>
            <flux:button variant="primary" wire:click="sahkanLengkap" data-test="confirm-complete-button">{{ __('Sahkan Lengkap & Teruskan ke Rundingan') }}</flux:button>
            <flux:button variant="danger" wire:click="tandakanPerluKuiri" data-test="mark-kuiri-button">{{ __('Tandakan Perlu Kuiri') }}</flux:button>
        </div>
    </flux:card>
</div>
