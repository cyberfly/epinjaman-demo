<?php

use App\Actions\Account\ActivateAccount;
use App\Concerns\PasswordValidationRules;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Locked;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Layout('layouts.auth')] #[Title('Aktifkan Akaun')] class extends Component {
    use PasswordValidationRules;

    #[Locked]
    public int $userId;

    public string $name = '';

    public string $password = '';

    public string $password_confirmation = '';

    /**
     * Mount the component from the signed activation link.
     */
    public function mount(User $user): void
    {
        if ($user->activated_at !== null) {
            $this->redirectRoute('login', navigate: true);

            return;
        }

        $this->userId = $user->getKey();
        $this->name = $user->name;
    }

    /**
     * Activate the account by setting a password.
     */
    public function activate(ActivateAccount $activateAccount): void
    {
        $validated = $this->validate([
            'password' => $this->passwordRules(),
        ]);

        $user = User::findOrFail($this->userId);

        $activateAccount->handle($user, $validated['password']);

        Auth::login($user);

        $this->redirectRoute('dashboard', navigate: true);
    }
}; ?>

<div class="flex flex-col gap-6">
    <x-auth-header
        :title="__('Aktifkan akaun anda')"
        :description="__('Tetapkan kata laluan untuk mengaktifkan akaun :name', ['name' => $name])"
    />

    <form method="POST" wire:submit="activate" class="flex flex-col gap-6">
        <flux:input
            wire:model="password"
            :label="__('Kata laluan')"
            type="password"
            required
            autocomplete="new-password"
            viewable
        />

        <flux:input
            wire:model="password_confirmation"
            :label="__('Sahkan kata laluan')"
            type="password"
            required
            autocomplete="new-password"
            viewable
        />

        <flux:button variant="primary" type="submit" class="w-full" data-test="activate-account-button">
            {{ __('Aktifkan & Log Masuk') }}
        </flux:button>
    </form>
</div>
