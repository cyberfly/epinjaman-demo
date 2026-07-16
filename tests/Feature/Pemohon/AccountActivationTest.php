<?php

use App\Models\User;
use Illuminate\Support\Facades\URL;
use Livewire\Livewire;

test('pautan pengaktifan sah dalam tempoh 24 jam', function () {
    $user = User::factory()->pemohon()->unactivated()->create();

    $url = URL::temporarySignedRoute('account.activate', now()->addHours(24), ['user' => $user->id]);

    $this->get($url)->assertOk();
});

test('pautan pengaktifan tamat tempoh selepas 24 jam', function () {
    $user = User::factory()->pemohon()->unactivated()->create();

    $url = URL::temporarySignedRoute('account.activate', now()->addHours(24), ['user' => $user->id]);

    $this->travel(25)->hours();

    $this->get($url)->assertForbidden();
});

test('pengguna boleh mengaktifkan akaun melalui skrin dan terus log masuk', function () {
    $user = User::factory()->pemohon()->unactivated()->create();

    Livewire::test('pages::auth.activate-account', ['user' => $user])
        ->set('password', 'rahsia-baharu-123')
        ->set('password_confirmation', 'rahsia-baharu-123')
        ->call('activate')
        ->assertHasNoErrors()
        ->assertRedirect(route('dashboard'));

    $this->assertAuthenticatedAs($user->fresh());
    expect($user->fresh()->activated_at)->not->toBeNull();
});

test('akaun yang telah diaktifkan dialihkan ke log masuk', function () {
    $user = User::factory()->pemohon()->create(); // activated by default factory (verified)
    $user->forceFill(['activated_at' => now()])->save();

    Livewire::test('pages::auth.activate-account', ['user' => $user])
        ->assertRedirect(route('login'));
});
