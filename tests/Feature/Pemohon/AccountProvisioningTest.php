<?php

use App\Actions\Account\ActivateAccount;
use App\Actions\Pemohon\CreatePemohonOrganisasi;
use App\Actions\Pemohon\ProvisionPemohonUser;
use App\Enums\PemohonStatus;
use App\Enums\SumberDana;
use App\Enums\UserRole;
use App\Models\KementerianPengawal;
use App\Models\Pemohon;
use App\Models\User;
use App\Notifications\AccountActivationNotification;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;

test('SID mencipta organisasi Pemohon dengan status Pemohon', function () {
    $pemohon = app(CreatePemohonOrganisasi::class)->handle('Agensi Pembangunan ABC');

    expect($pemohon->nama)->toBe('Agensi Pembangunan ABC')
        ->and($pemohon->status)->toBe(PemohonStatus::Pemohon);

    $this->assertDatabaseHas('pemohons', [
        'nama' => 'Agensi Pembangunan ABC',
        'status' => PemohonStatus::Pemohon->value,
    ]);
});

test('SID menetapkan Sumber Dana & Kementerian Pengawal organisasi semasa provision', function () {
    $kementerian = KementerianPengawal::factory()->create();

    $pemohon = app(CreatePemohonOrganisasi::class)->handle(
        'Agensi DE',
        SumberDana::DE,
        false,
        $kementerian->id,
    );

    expect($pemohon->sumber_dana)->toBe(SumberDana::DE)
        ->and($pemohon->ada_kementerian_pengawal)->toBeFalse()
        ->and($pemohon->kementerian_pengawal_id)->toBe($kementerian->id);
});

test('memprovision pengguna Pemohon dalam keadaan belum aktif dan menghantar e-mel pengaktifan', function () {
    Notification::fake();

    $pemohon = Pemohon::factory()->create();

    $user = app(ProvisionPemohonUser::class)->handle($pemohon, 'Ali bin Abu', 'ali@agensi.test');

    expect($user->role)->toBe(UserRole::Pemohon)
        ->and($user->pemohon_id)->toBe($pemohon->id)
        ->and($user->email_verified_at)->toBeNull()
        ->and($user->activated_at)->toBeNull();

    Notification::assertSentTo($user, AccountActivationNotification::class);
});

test('satu organisasi Pemohon boleh mempunyai lebih daripada satu pengguna', function () {
    Notification::fake();

    $pemohon = Pemohon::factory()->create();

    app(ProvisionPemohonUser::class)->handle($pemohon, 'Ali', 'ali@agensi.test');
    app(ProvisionPemohonUser::class)->handle($pemohon, 'Abu', 'abu@agensi.test');

    expect($pemohon->users()->count())->toBe(2);
});

test('mengaktifkan akaun menetapkan kata laluan, mengesahkan e-mel & menandakan aktivasi', function () {
    $user = User::factory()->pemohon()->unactivated()->create();

    app(ActivateAccount::class)->handle($user, 'kata-laluan-baharu-123');

    $user->refresh();

    expect($user->activated_at)->not->toBeNull()
        ->and($user->email_verified_at)->not->toBeNull()
        ->and(Hash::check('kata-laluan-baharu-123', $user->password))->toBeTrue();
});

test('setiap peranan boleh diberikan kepada pengguna', function () {
    foreach (UserRole::cases() as $role) {
        $user = User::factory()->role($role)->create();
        expect($user->role)->toBe($role)
            ->and($user->hasRole($role))->toBeTrue();
    }
});
