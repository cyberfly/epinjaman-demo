<?php

use Illuminate\Support\Facades\Route;

test('laluan pendaftaran layan diri dinyahaktifkan', function () {
    expect(Route::has('register'))->toBeFalse();
});

test('titik akhir pendaftaran memberi 404', function () {
    $this->get('/register')->assertNotFound();
    $this->post('/register', [
        'name' => 'John Doe',
        'email' => 'john@example.com',
        'password' => 'password',
        'password_confirmation' => 'password',
    ])->assertNotFound();
});
