<?php

use App\Actions\Permohonan\SaveDraftPermohonan;
use App\Actions\Permohonan\SubmitPermohonan;
use App\Actions\Permohonan\UploadPermohonanDocument;
use App\Enums\SumberDana;
use App\Models\ChecklistItem;
use App\Models\KementerianPengawal;
use App\Models\Pemohon;
use App\Models\Permohonan;
use Flux\Flux;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Locked;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithFileUploads;

new #[Layout('layouts.app')] #[Title('Borang Permohonan')] class extends Component {
    use WithFileUploads;

    public ?int $permohonanId = null;

    public string $tajuk = '';

    public ?string $jumlah_dipohon = null;

    public string $tujuan = '';

    public ?int $tempoh_bulan = null;

    /**
     * Read-only on the Pemohon form: the funding source is set upstream (SID),
     * never chosen here (ticket 14). Locked so a crafted request cannot change
     * it, and dropped from the write path (see formData()).
     */
    #[Locked]
    public ?string $sumber_dana = null;

    /**
     * The controlling-ministry routing fields are also set upstream by SID and
     * shown read-only here (ticket 16). Locked & dropped from the write path.
     */
    #[Locked]
    public ?bool $ada_kementerian_pengawal = null;

    #[Locked]
    public ?int $kementerian_pengawal_id = null;

    public ?int $selectedChecklistItemId = null;

    public $document = null;

    public function mount(?Permohonan $permohonan = null): void
    {
        if ($permohonan !== null && $permohonan->exists) {
            $this->authorize('view', $permohonan);
            $this->permohonanId = $permohonan->id;
            $this->fillFromModel($permohonan);
        } else {
            $user = auth()->user();
            abort_unless($user?->isPemohon() ?? false, 403);
            $this->fillRoutingFromPemohon($user->pemohon);
        }
    }

    /**
     * A new draft inherits its funding-source routing from the organisation
     * (ticket 16); pre-fill the read-only display so the Pemohon sees it before
     * the first save.
     */
    private function fillRoutingFromPemohon(?Pemohon $pemohon): void
    {
        if ($pemohon === null) {
            return;
        }

        $this->sumber_dana = $pemohon->sumber_dana?->value;
        $this->ada_kementerian_pengawal = $pemohon->ada_kementerian_pengawal;
        $this->kementerian_pengawal_id = $pemohon->kementerian_pengawal_id;
    }

    private function fillFromModel(Permohonan $permohonan): void
    {
        $this->tajuk = (string) $permohonan->tajuk;
        $this->jumlah_dipohon = $permohonan->jumlah_dipohon;
        $this->tujuan = (string) $permohonan->tujuan;
        $this->tempoh_bulan = $permohonan->tempoh_bulan;
        $this->sumber_dana = $permohonan->sumber_dana?->value;
        $this->ada_kementerian_pengawal = $permohonan->ada_kementerian_pengawal;
        $this->kementerian_pengawal_id = $permohonan->kementerian_pengawal_id;
    }

    /**
     * @return \Illuminate\Database\Eloquent\Collection<int, ChecklistItem>
     */
    #[\Livewire\Attributes\Computed]
    public function checklistItems()
    {
        return ChecklistItem::active();
    }

    /**
     * Name of the stored controlling ministry for read-only display, or a dash
     * when none applies (ticket 16).
     */
    #[\Livewire\Attributes\Computed]
    public function kementerianPengawalNama(): string
    {
        return $this->kementerian_pengawal_id !== null
            ? (KementerianPengawal::find($this->kementerian_pengawal_id)?->nama ?? '—')
            : '—';
    }

    #[\Livewire\Attributes\Computed]
    public function permohonan(): ?Permohonan
    {
        return $this->permohonanId !== null ? Permohonan::with('documents')->find($this->permohonanId) : null;
    }

    /**
     * Human label of the stored funding source for read-only display, or a
     * dash when none has been set upstream yet (ticket 14).
     */
    #[\Livewire\Attributes\Computed]
    public function sumberDanaLabel(): string
    {
        $sumber = $this->sumber_dana !== null ? SumberDana::tryFrom($this->sumber_dana) : null;

        return $sumber?->label() ?? '—';
    }

    private function formData(): array
    {
        return [
            'tajuk' => $this->tajuk ?: null,
            'jumlah_dipohon' => $this->jumlah_dipohon,
            'tujuan' => $this->tujuan ?: null,
            'tempoh_bulan' => $this->tempoh_bulan,
        ];
    }

    public function saveDraft(SaveDraftPermohonan $saveDraftPermohonan): void
    {
        $pemohon = auth()->user()->pemohon;
        abort_if($pemohon === null, 403);

        $permohonan = $this->permohonanId !== null ? Permohonan::findOrFail($this->permohonanId) : null;

        if ($permohonan !== null) {
            $this->authorize('update', $permohonan);
        }

        $saved = $saveDraftPermohonan->handle($pemohon, $this->formData(), $permohonan);
        $this->permohonanId = $saved->id;

        Flux::toast(variant: 'success', text: __('Draf disimpan.'));
    }

    public function uploadDocument(UploadPermohonanDocument $uploadPermohonanDocument): void
    {
        $this->validate(['document' => ['required', 'file', 'max:10240']]);

        $permohonan = Permohonan::findOrFail($this->permohonanId);
        $this->authorize('uploadDocument', $permohonan);

        $path = $this->document->store('permohonan-documents');
        $checklistItem = $this->selectedChecklistItemId !== null
            ? ChecklistItem::find($this->selectedChecklistItemId)
            : null;

        $uploadPermohonanDocument->handle(
            $permohonan,
            auth()->user(),
            $path,
            $this->document->getClientOriginalName(),
            $checklistItem,
        );

        $this->reset('document', 'selectedChecklistItemId');
        unset($this->permohonan);

        Flux::toast(variant: 'success', text: __('Dokumen dimuat naik.'));
    }

    public function submit(SaveDraftPermohonan $saveDraftPermohonan, SubmitPermohonan $submitPermohonan): void
    {
        $pemohon = auth()->user()->pemohon;
        abort_if($pemohon === null, 403);

        $permohonan = $this->permohonanId !== null ? Permohonan::findOrFail($this->permohonanId) : null;
        $permohonan = $saveDraftPermohonan->handle($pemohon, $this->formData(), $permohonan);

        $this->authorize('submit', $permohonan);

        try {
            $submitPermohonan->handle($permohonan);
        } catch (ValidationException $e) {
            $this->permohonanId = $permohonan->id;
            throw $e;
        }

        Flux::toast(variant: 'success', text: __('Permohonan dihantar.'));

        $this->redirectRoute('permohonan.index', navigate: true);
    }
}; ?>

<div class="flex flex-col gap-6">
    <flux:heading size="xl">{{ __('Borang Permohonan Pinjaman') }}</flux:heading>

    <flux:card class="space-y-4">
        <flux:input wire:model="tajuk" :label="__('Tajuk / Nama Projek')" />
        <flux:input wire:model="jumlah_dipohon" type="number" step="0.01" :label="__('Jumlah Dipohon (RM)')" />
        <flux:textarea wire:model="tujuan" :label="__('Tujuan')" />
        <flux:input wire:model="tempoh_bulan" type="number" :label="__('Tempoh (bulan)')" />

        <flux:input
            :value="$this->sumberDanaLabel"
            :label="__('Sumber Dana')"
            :description="__('Ditetapkan di peringkat SID — tidak boleh diubah oleh Pemohon.')"
            readonly
            data-test="sumber-dana-readonly"
        />

        <flux:input
            :value="$this->kementerianPengawalNama"
            :label="__('Kementerian Pengawal')"
            :description="__('Ditetapkan di peringkat SID — tidak boleh diubah oleh Pemohon.')"
            readonly
            data-test="kementerian-pengawal-readonly"
        />

        @if ($this->permohonan === null || $this->permohonan->status->isDraf())
            <div class="flex gap-2">
                <flux:button variant="filled" wire:click="saveDraft" data-test="save-draft-button">{{ __('Simpan Draf') }}</flux:button>
                <flux:button variant="primary" wire:click="submit" data-test="submit-permohonan-button">{{ __('Hantar Permohonan') }}</flux:button>
            </div>
        @else
            <flux:callout variant="secondary" icon="lock-closed" data-test="permohonan-dihantar-notis">
                <flux:callout.heading>{{ __('Permohonan telah dihantar') }}</flux:callout.heading>
                <flux:callout.text>{{ __('Status semasa: :status. Borang ini tidak boleh diubah atau dihantar semula.', ['status' => $this->permohonan->status->label()]) }}</flux:callout.text>
            </flux:callout>
        @endif
    </flux:card>

    @if ($this->permohonan)
        <flux:card class="space-y-4">
            <flux:heading>{{ __('Dokumen Sokongan (Lampiran 6)') }}</flux:heading>

            <flux:select wire:model="selectedChecklistItemId" :label="__('Item senarai semak')" placeholder="{{ __('Pilih item') }}">
                @foreach ($this->checklistItems as $item)
                    <flux:select.option :value="$item->id">{{ $item->label }}</flux:select.option>
                @endforeach
            </flux:select>
            <flux:input type="file" wire:model="document" :label="__('Fail dokumen')" />
            <flux:button variant="filled" wire:click="uploadDocument" data-test="upload-document-button">{{ __('Muat Naik') }}</flux:button>

            <flux:table>
                <flux:table.columns>
                    <flux:table.column>{{ __('Dokumen') }}</flux:table.column>
                    <flux:table.column>{{ __('Item') }}</flux:table.column>
                </flux:table.columns>
                <flux:table.rows>
                    @foreach ($this->permohonan->documents as $doc)
                        <flux:table.row wire:key="doc-{{ $doc->id }}">
                            <flux:table.cell>{{ $doc->original_name }}</flux:table.cell>
                            <flux:table.cell>{{ $doc->checklistItem?->label ?? '—' }}</flux:table.cell>
                        </flux:table.row>
                    @endforeach
                </flux:table.rows>
            </flux:table>
        </flux:card>
    @endif
</div>
