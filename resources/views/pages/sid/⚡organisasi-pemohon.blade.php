<?php

use App\Actions\Pemohon\CreatePemohonOrganisasi;
use App\Actions\Pemohon\ProvisionPemohonUser;
use App\Models\Pemohon;
use Flux\Flux;
use Illuminate\Support\Facades\Gate;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Layout('layouts.app')] #[Title('Organisasi Pemohon')] class extends Component {
    public string $namaOrganisasi = '';

    public ?int $selectedPemohonId = null;

    public string $namaPengguna = '';

    public string $emailPengguna = '';

    /**
     * Only SID/Admin may reach this screen.
     */
    public function mount(): void
    {
        Gate::authorize('provision-accounts');
    }

    /**
     * Pemohon organisations, newest first.
     */
    #[Computed]
    public function pemohons()
    {
        return Pemohon::withCount('users')->latest()->get();
    }

    /**
     * Create a new Pemohon organisation.
     */
    public function ciptaOrganisasi(CreatePemohonOrganisasi $createPemohonOrganisasi): void
    {
        Gate::authorize('provision-accounts');

        $validated = $this->validate([
            'namaOrganisasi' => ['required', 'string', 'max:255'],
        ]);

        $createPemohonOrganisasi->handle($validated['namaOrganisasi']);

        $this->reset('namaOrganisasi');
        unset($this->pemohons);

        Flux::toast(variant: 'success', text: __('Organisasi Pemohon dicipta.'));
    }

    /**
     * Provision a new user under the selected organisation.
     */
    public function tambahPengguna(ProvisionPemohonUser $provisionPemohonUser): void
    {
        Gate::authorize('provision-accounts');

        $validated = $this->validate([
            'selectedPemohonId' => ['required', 'exists:pemohons,id'],
            'namaPengguna' => ['required', 'string', 'max:255'],
            'emailPengguna' => ['required', 'email', 'max:255', 'unique:users,email'],
        ]);

        $pemohon = Pemohon::findOrFail($validated['selectedPemohonId']);

        $provisionPemohonUser->handle($pemohon, $validated['namaPengguna'], $validated['emailPengguna']);

        $this->reset('namaPengguna', 'emailPengguna');
        unset($this->pemohons);

        Flux::toast(variant: 'success', text: __('E-mel pengaktifan telah dihantar kepada pengguna.'));
    }
}; ?>

<div class="flex flex-col gap-6">
    <flux:heading size="xl">{{ __('Urus Organisasi Pemohon') }}</flux:heading>

        <div class="grid gap-6 md:grid-cols-2">
            <flux:card class="space-y-4">
                <flux:heading>{{ __('Cipta Organisasi Baharu') }}</flux:heading>
                <form wire:submit="ciptaOrganisasi" class="space-y-4">
                    <flux:input wire:model="namaOrganisasi" :label="__('Nama organisasi')" required />
                    <flux:button variant="primary" type="submit" data-test="cipta-organisasi-button">
                        {{ __('Cipta') }}
                    </flux:button>
                </form>
            </flux:card>

            <flux:card class="space-y-4">
                <flux:heading>{{ __('Tambah Pengguna') }}</flux:heading>
                <form wire:submit="tambahPengguna" class="space-y-4">
                    <flux:select wire:model="selectedPemohonId" :label="__('Organisasi')" placeholder="{{ __('Pilih organisasi') }}">
                        @foreach ($this->pemohons as $pemohon)
                            <flux:select.option :value="$pemohon->id">{{ $pemohon->nama }}</flux:select.option>
                        @endforeach
                    </flux:select>
                    <flux:input wire:model="namaPengguna" :label="__('Nama pengguna')" required />
                    <flux:input wire:model="emailPengguna" :label="__('E-mel')" type="email" required />
                    <flux:button variant="primary" type="submit" data-test="tambah-pengguna-button">
                        {{ __('Tambah & Hantar Pengaktifan') }}
                    </flux:button>
                </form>
            </flux:card>
        </div>

        <flux:card>
            <flux:heading>{{ __('Senarai Organisasi') }}</flux:heading>
            <flux:table class="mt-4">
                <flux:table.columns>
                    <flux:table.column>{{ __('Organisasi') }}</flux:table.column>
                    <flux:table.column>{{ __('Status') }}</flux:table.column>
                    <flux:table.column>{{ __('Bil. Pengguna') }}</flux:table.column>
                </flux:table.columns>
                <flux:table.rows>
                    @foreach ($this->pemohons as $pemohon)
                        <flux:table.row wire:key="pemohon-{{ $pemohon->id }}">
                            <flux:table.cell>{{ $pemohon->nama }}</flux:table.cell>
                            <flux:table.cell>{{ $pemohon->status->label() }}</flux:table.cell>
                            <flux:table.cell>{{ $pemohon->users_count }}</flux:table.cell>
                        </flux:table.row>
                    @endforeach
                </flux:table.rows>
            </flux:table>
    </flux:card>
</div>
