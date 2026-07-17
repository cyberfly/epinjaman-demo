<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
    <head>
        @include('partials.head')
    </head>
    <body class="min-h-screen bg-white dark:bg-zinc-800">
        <flux:sidebar sticky collapsible="mobile" class="border-e border-zinc-200 bg-zinc-50 dark:border-zinc-700 dark:bg-zinc-900">
            <flux:sidebar.header>
                <x-app-logo :sidebar="true" href="{{ route('dashboard') }}" wire:navigate />
                <flux:sidebar.collapse class="lg:hidden" />
            </flux:sidebar.header>

            <flux:sidebar.nav>
                <flux:sidebar.group :heading="__('Platform')" class="grid">
                    <flux:sidebar.item icon="home" :href="route('dashboard')" :current="request()->routeIs('dashboard')" wire:navigate>
                        {{ __('Dashboard') }}
                    </flux:sidebar.item>
                </flux:sidebar.group>

                @php($role = auth()->user()?->role)
                @if ($role)
                    <flux:sidebar.group :heading="__('ePinjaman')" class="grid">
                        @if ($role === \App\Enums\UserRole::Pemohon)
                            <flux:sidebar.item icon="document-text" :href="route('permohonan.index')" :current="request()->routeIs('permohonan.*')" wire:navigate>
                                {{ __('Permohonan Saya') }}
                            </flux:sidebar.item>
                        @endif

                        @if ($role === \App\Enums\UserRole::KementerianPengawal)
                            <flux:sidebar.item icon="inbox" :href="route('kementerian.tray')" :current="request()->routeIs('kementerian.*')" wire:navigate>
                                {{ __('Tray Kementerian') }}
                            </flux:sidebar.item>
                        @endif

                        @if ($role === \App\Enums\UserRole::PSID)
                            <flux:sidebar.item icon="inbox" :href="route('sid.tray')" :current="request()->routeIs('sid.*')" wire:navigate>
                                {{ __('Tray SID') }}
                            </flux:sidebar.item>
                        @endif

                        @if (in_array($role, \App\Enums\UserRole::approvalHierarchy(), true))
                            <flux:sidebar.item icon="check-circle" :href="route('kelulusan.tray')" :current="request()->routeIs('kelulusan.*')" wire:navigate>
                                {{ __('Tray Kelulusan') }}
                            </flux:sidebar.item>
                        @endif

                        @if ($role === \App\Enums\UserRole::BUU)
                            <flux:sidebar.item icon="scale" :href="route('buu.tray')" :current="request()->routeIs('buu.*')" wire:navigate>
                                {{ __('Tray BUU (Perjanjian)') }}
                            </flux:sidebar.item>
                        @endif

                        @if ($role === \App\Enums\UserRole::Admin)
                            <flux:sidebar.item icon="clipboard-document-list" :href="route('admin.checklist-items')" :current="request()->routeIs('admin.checklist-items')" wire:navigate>
                                {{ __('Senarai Semak (L6)') }}
                            </flux:sidebar.item>
                            <flux:sidebar.item icon="envelope" :href="route('admin.notification-templates')" :current="request()->routeIs('admin.notification-templates')" wire:navigate>
                                {{ __('Templat Notifikasi (L8)') }}
                            </flux:sidebar.item>
                        @endif
                    </flux:sidebar.group>
                @endif
            </flux:sidebar.nav>

            <flux:spacer />

            <flux:sidebar.nav>
                <flux:sidebar.item icon="folder-git-2" href="https://github.com/laravel/livewire-starter-kit" target="_blank">
                    {{ __('Repository') }}
                </flux:sidebar.item>

                <flux:sidebar.item icon="book-open-text" href="https://laravel.com/docs/starter-kits#livewire" target="_blank">
                    {{ __('Documentation') }}
                </flux:sidebar.item>
            </flux:sidebar.nav>

            <x-desktop-user-menu class="hidden lg:block" :name="auth()->user()->name" />
        </flux:sidebar>

        <!-- Mobile User Menu -->
        <flux:header class="lg:hidden">
            <flux:sidebar.toggle class="lg:hidden" icon="bars-2" inset="left" />

            <flux:spacer />

            <flux:dropdown position="top" align="end">
                <flux:profile
                    :initials="auth()->user()->initials()"
                    icon-trailing="chevron-down"
                />

                <flux:menu>
                    <flux:menu.radio.group>
                        <div class="p-0 text-sm font-normal">
                            <div class="flex items-center gap-2 px-1 py-1.5 text-start text-sm">
                                <flux:avatar
                                    :name="auth()->user()->name"
                                    :initials="auth()->user()->initials()"
                                />

                                <div class="grid flex-1 text-start text-sm leading-tight">
                                    <flux:heading class="truncate">{{ auth()->user()->name }}</flux:heading>
                                    <flux:text class="truncate">{{ auth()->user()->email }}</flux:text>
                                </div>
                            </div>
                        </div>
                    </flux:menu.radio.group>

                    <flux:menu.separator />

                    <flux:menu.radio.group>
                        <flux:menu.item :href="route('profile.edit')" icon="cog" wire:navigate>
                            {{ __('Settings') }}
                        </flux:menu.item>
                    </flux:menu.radio.group>

                    <flux:menu.separator />

                    <form method="POST" action="{{ route('logout') }}" class="w-full">
                        @csrf
                        <flux:menu.item
                            as="button"
                            type="submit"
                            icon="arrow-right-start-on-rectangle"
                            class="w-full cursor-pointer"
                            data-test="logout-button"
                        >
                            {{ __('Log out') }}
                        </flux:menu.item>
                    </form>
                </flux:menu>
            </flux:dropdown>
        </flux:header>

        {{ $slot }}

        @persist('toast')
            <flux:toast.group>
                <flux:toast />
            </flux:toast.group>
        @endpersist

        @fluxScripts
    </body>
</html>
