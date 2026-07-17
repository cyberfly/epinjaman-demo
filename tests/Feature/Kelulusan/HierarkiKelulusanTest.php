<?php

use App\Actions\Kelulusan\EndorseMemo;
use App\Actions\Kelulusan\KeputusanKelulusan;
use App\Actions\Kelulusan\PulangkanMemo;
use App\Enums\ApprovalDecision;
use App\Enums\PermohonanStatus;
use App\Enums\UserRole;
use App\Models\Memo;
use App\Models\Permohonan;
use App\Models\User;
use App\Notifications\TemplatedNotification;
use Illuminate\Support\Facades\Notification;

/**
 * @return array{0: Permohonan, 1: Memo, 2: array<string, User>}
 */
function setupKelulusan(): array
{
    $permohonan = Permohonan::factory()->status(PermohonanStatus::DalamKelulusan)->create();
    $memo = Memo::factory()->for($permohonan)->create(['peringkat' => UserRole::PSID, 'keputusan' => null]);

    $users = [];
    foreach (UserRole::approvalHierarchy() as $role) {
        $users[$role->value] = User::factory()->role($role)->create();
    }

    return [$permohonan, $memo, $users];
}

test('endorse berurutan penuh PSID -> YB MK, keputusan Lulus, sejarah lengkap', function () {
    [$permohonan, $memo, $users] = setupKelulusan();

    foreach ([UserRole::PSID, UserRole::KSID, UserRole::KKID, UserRole::TSID, UserRole::SSID, UserRole::TKSPI, UserRole::KSP] as $role) {
        app(EndorseMemo::class)->handle($memo->fresh(), $users[$role->value]);
    }

    expect($memo->fresh()->peringkat)->toBe(UserRole::YBMK);

    app(KeputusanKelulusan::class)->handle($memo->fresh(), $users[UserRole::YBMK->value], ApprovalDecision::Lulus);

    expect($memo->fresh()->keputusan)->toBe(ApprovalDecision::Lulus)
        ->and($permohonan->fresh()->status)->toBe(PermohonanStatus::Ditawarkan)
        ->and($permohonan->fresh()->suratTawaran)->not->toBeNull()
        ->and($memo->approvalSteps()->count())->toBe(8);
});

test('peringkat pertengahan boleh pulangkan ke peringkat sebelumnya dengan notifikasi', function () {
    Notification::fake();
    [$permohonan, $memo, $users] = setupKelulusan();

    app(EndorseMemo::class)->handle($memo->fresh(), $users[UserRole::PSID->value]); // -> KSID
    app(EndorseMemo::class)->handle($memo->fresh(), $users[UserRole::KSID->value]); // -> KKID

    app(PulangkanMemo::class)->handle($memo->fresh(), $users[UserRole::KKID->value], 'Perlu pembetulan terma');

    expect($memo->fresh()->peringkat)->toBe(UserRole::KSID);
    Notification::assertSentTo($users[UserRole::KSID->value], TemplatedNotification::class);
});

test('PSID (peringkat pertama) tidak boleh pulangkan memo', function () {
    [$permohonan, $memo, $users] = setupKelulusan();

    expect(fn () => app(PulangkanMemo::class)->handle($memo, $users[UserRole::PSID->value], 'x'))
        ->toThrow(DomainException::class);
});

test('pegawai bukan peringkat semasa tidak boleh endorse', function () {
    [$permohonan, $memo, $users] = setupKelulusan();

    expect(fn () => app(EndorseMemo::class)->handle($memo, $users[UserRole::KSID->value]))
        ->toThrow(DomainException::class);
});

test('keputusan Tidak Lulus mengembalikan permohonan ke Rundingan', function () {
    [$permohonan, $memo, $users] = setupKelulusan();
    $memo->update(['peringkat' => UserRole::YBMK]);

    app(KeputusanKelulusan::class)->handle($memo->fresh(), $users[UserRole::YBMK->value], ApprovalDecision::TidakLulus, 'Tidak memenuhi syarat');

    expect($permohonan->fresh()->status)->toBe(PermohonanStatus::DalamRundingan)
        ->and($memo->fresh()->keputusan)->toBe(ApprovalDecision::TidakLulus);
});

test('YB MK tidak boleh membuat keputusan sebelum memo sampai ke peringkat YB MK', function () {
    [$permohonan, $memo, $users] = setupKelulusan();

    expect(fn () => app(KeputusanKelulusan::class)->handle($memo, $users[UserRole::YBMK->value], ApprovalDecision::Lulus))
        ->toThrow(DomainException::class);
});
