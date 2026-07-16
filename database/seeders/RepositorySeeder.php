<?php

namespace Database\Seeders;

use App\Models\ChecklistItem;
use App\Models\NotificationTemplate;
use Illuminate\Database\Seeder;

/**
 * Default Lampiran 6 (document checklist) and Lampiran 8 (notification
 * templates) content. Admin may edit these through the admin screens.
 */
class RepositorySeeder extends Seeder
{
    public function run(): void
    {
        $checklist = [
            'Surat permohonan rasmi daripada organisasi',
            'Penyata kewangan teraudit tiga tahun terkini',
            'Unjuran aliran tunai tempoh pinjaman',
            'Kertas cadangan projek / justifikasi pinjaman',
            'Resolusi Lembaga Pengarah meluluskan permohonan',
            'Dokumen sokongan cagaran / gadaian',
        ];

        foreach ($checklist as $position => $label) {
            ChecklistItem::firstOrCreate(['label' => $label], [
                'position' => $position,
                'is_active' => true,
            ]);
        }

        $templates = [
            [
                'event' => 'permohonan.diterima',
                'title' => 'Permohonan Diterima — :no_rujukan',
                'content' => 'Permohonan anda telah diterima dan kini dalam proses semakan. No. Rujukan: :no_rujukan.',
            ],
            [
                'event' => 'permohonan.dihantar_kementerian',
                'title' => 'Permohonan Baharu untuk Semakan Kementerian Pengawal',
                'content' => 'Permohonan ":tajuk" telah dihantar ke kementerian anda untuk Tandatangan Peringkat 2.',
            ],
            [
                'event' => 'kuiri.dicetus',
                'title' => 'Kuiri Baharu bagi Permohonan :no_rujukan',
                'content' => 'Terdapat kuiri yang memerlukan tindakan anda: :sebab. Sila balas sebelum :tarikh_akhir.',
            ],
            [
                'event' => 'memo.dipulangkan',
                'title' => 'Memo Pertimbangan Dipulangkan',
                'content' => 'Memo Pertimbangan bagi :no_rujukan dipulangkan untuk pembetulan. Sebab: :sebab.',
            ],
            [
                'event' => 'tawaran.dijana',
                'title' => 'Tawaran Pinjaman — :no_rujukan',
                'content' => 'Surat Tawaran Pinjaman anda telah dijana. Sila semak dan turunkan tandatangan penerimaan.',
            ],
            [
                'event' => 'traffic_light.amaran',
                'title' => 'Amaran Tarikh Akhir — :no_rujukan',
                'content' => 'Status traffic light peringkat :peringkat kini :status. Sila ambil tindakan sewajarnya.',
            ],
        ];

        foreach ($templates as $template) {
            NotificationTemplate::firstOrCreate(['event' => $template['event']], $template);
        }
    }
}
