<?php

namespace App\Providers;

use App\Enums\UserRole;
use App\Models\User;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Rules\Password;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->configureDefaults();
        $this->configureGates();
    }

    /**
     * Register application-wide authorization gates.
     */
    protected function configureGates(): void
    {
        // Provisioning Pemohon organisations and their users is restricted to
        // SID officers and Admin (ticket 01).
        Gate::define('provision-accounts', fn (User $user): bool => $user->role?->canProvisionAccounts() ?? false);

        // Managing the admin reference repositories (Lampiran 6 & 8) is Admin-only (ticket 02).
        Gate::define('manage-repositories', fn (User $user): bool => $user->hasRole(UserRole::Admin));
    }

    /**
     * Configure default behaviors for production-ready applications.
     */
    protected function configureDefaults(): void
    {
        Date::use(CarbonImmutable::class);

        DB::prohibitDestructiveCommands(
            app()->isProduction(),
        );

        Password::defaults(fn (): ?Password => app()->isProduction()
            ? Password::min(12)
                ->mixedCase()
                ->letters()
                ->numbers()
                ->symbols()
                ->uncompromised()
            : null,
        );
    }
}
