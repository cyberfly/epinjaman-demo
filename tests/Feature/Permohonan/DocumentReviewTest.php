<?php

use App\Actions\Permohonan\SahkanDokumenLengkap;
use App\Actions\Permohonan\SaveDocumentReview;
use App\Actions\Permohonan\TandakanDokumenPerluKuiri;
use App\Enums\PermohonanStatus;
use App\Enums\UserRole;
use App\Models\ChecklistItem;
use App\Models\Permohonan;
use App\Models\User;

test('PSID menyimpan semakan per-item (lengkap/teratur)', function () {
    $psid = User::factory()->role(UserRole::PSID)->create();
    $permohonan = Permohonan::factory()->status(PermohonanStatus::DalamSemakanSID)->create();
    $item = ChecklistItem::factory()->create();

    $review = app(SaveDocumentReview::class)->handle($permohonan, $item, $psid, true, false, 'Perlu semak semula');

    expect($review->is_complete)->toBeTrue()
        ->and($review->is_orderly)->toBeFalse()
        ->and($review->reviewed_by)->toBe($psid->id);

    // upsert — second call updates the same row
    app(SaveDocumentReview::class)->handle($permohonan, $item, $psid, true, true, null);
    expect($permohonan->documentReviews()->count())->toBe(1)
        ->and($review->fresh()->is_orderly)->toBeTrue();
});

test('mengesahkan dokumen lengkap meneruskan ke Rundingan', function () {
    $permohonan = Permohonan::factory()->status(PermohonanStatus::DalamSemakanSID)->create();

    app(SahkanDokumenLengkap::class)->handle($permohonan);

    expect($permohonan->fresh()->status)->toBe(PermohonanStatus::DalamRundingan);
});

test('menandakan dokumen tidak lengkap memindahkan ke Kuiri', function () {
    $permohonan = Permohonan::factory()->status(PermohonanStatus::DalamSemakanSID)->create();

    app(TandakanDokumenPerluKuiri::class)->handle($permohonan);

    expect($permohonan->fresh()->status)->toBe(PermohonanStatus::DalamKuiri);
});

test('tidak boleh sahkan dokumen pada status yang salah', function () {
    $permohonan = Permohonan::factory()->status(PermohonanStatus::DalamRundingan)->create();

    expect(fn () => app(SahkanDokumenLengkap::class)->handle($permohonan))
        ->toThrow(DomainException::class);
});
