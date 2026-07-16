<?php

use App\Models\NotificationTemplate;
use App\Models\User;
use App\Notifications\TemplatedNotification;
use Illuminate\Support\Facades\Notification;

test('notifikasi bertemplat dihantar melalui saluran mel dan pangkalan data', function () {
    Notification::fake();

    $user = User::factory()->create();

    $user->notify(new TemplatedNotification('permohonan.diterima', ['no_rujukan' => 'SID/2026/001']));

    Notification::assertSentTo(
        $user,
        TemplatedNotification::class,
        function (TemplatedNotification $notification, array $channels) {
            return in_array('mail', $channels, true) && in_array('database', $channels, true);
        },
    );
});

test('notifikasi bertemplat menyimpan tajuk & kandungan yang dirender daripada templat tersimpan', function () {
    NotificationTemplate::factory()->create([
        'event' => 'permohonan.diterima',
        'title' => 'Permohonan :no_rujukan',
        'content' => 'Rujukan anda ialah :no_rujukan.',
    ]);

    $user = User::factory()->create();

    $user->notify(new TemplatedNotification('permohonan.diterima', ['no_rujukan' => 'SID/2026/001']));

    expect($user->notifications()->count())->toBe(1);

    $data = $user->notifications()->first()->data;

    expect($data['title'])->toBe('Permohonan SID/2026/001')
        ->and($data['content'])->toBe('Rujukan anda ialah SID/2026/001.')
        ->and($data['event'])->toBe('permohonan.diterima');
});

test('mel notifikasi menggunakan tajuk templat sebagai subjek', function () {
    NotificationTemplate::factory()->create([
        'event' => 'tawaran.dijana',
        'title' => 'Tawaran Pinjaman :no_rujukan',
        'content' => 'Sila semak surat tawaran anda.',
    ]);

    $user = User::factory()->create();
    $notification = new TemplatedNotification('tawaran.dijana', ['no_rujukan' => 'SID/2026/009']);

    $mail = $notification->toMail($user);

    expect($mail->subject)->toBe('Tawaran Pinjaman SID/2026/009');
});
