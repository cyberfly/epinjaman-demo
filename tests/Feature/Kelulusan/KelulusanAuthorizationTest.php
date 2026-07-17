<?php

use App\Enums\PermohonanStatus;
use App\Enums\UserRole;
use App\Models\Memo;
use App\Models\Permohonan;
use App\Models\User;

test('policy kelulusan menghadkan tindakan mengikut peringkat semasa', function () {
    $permohonan = Permohonan::factory()->status(PermohonanStatus::DalamKelulusan)->create();
    $memo = Memo::factory()->for($permohonan)->create(['peringkat' => UserRole::PSID, 'keputusan' => null]);

    $psid = User::factory()->role(UserRole::PSID)->create();
    $ksid = User::factory()->role(UserRole::KSID)->create();
    $ybmk = User::factory()->role(UserRole::YBMK)->create();
    $pemohon = User::factory()->pemohon()->create();

    expect($psid->can('endorse', $memo))->toBeTrue()
        ->and($psid->can('returnToPrevious', $memo))->toBeFalse() // first level
        ->and($ksid->can('endorse', $memo))->toBeFalse() // not current level
        ->and($pemohon->can('viewHistory', $memo))->toBeFalse()
        ->and($psid->can('viewHistory', $memo))->toBeTrue();

    $memo->update(['peringkat' => UserRole::KSID]);
    expect($ksid->fresh()->can('returnToPrevious', $memo->fresh()))->toBeTrue();

    $memo->update(['peringkat' => UserRole::YBMK]);
    expect($ybmk->can('decide', $memo->fresh()))->toBeTrue()
        ->and($ybmk->can('endorse', $memo->fresh()))->toBeFalse();
});
