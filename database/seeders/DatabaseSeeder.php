<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Models\KementerianPengawal;
use App\Models\Pemohon;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // One activated staff user per internal role, for local development.
        foreach (UserRole::cases() as $role) {
            if ($role === UserRole::Pemohon || $role === UserRole::KementerianPengawal) {
                continue;
            }

            User::factory()->role($role)->create([
                'name' => $role->label(),
                'email' => str($role->value)->slug().'@epinjaman.test',
            ]);
        }

        // A sample controlling ministry with an officer.
        $kementerian = KementerianPengawal::factory()->create(['nama' => 'Kementerian Pengawal Contoh']);
        User::factory()->role(UserRole::KementerianPengawal)->create([
            'name' => 'Pegawai Kementerian Pengawal',
            'email' => 'kementerian@epinjaman.test',
            'kementerian_pengawal_id' => $kementerian->id,
        ]);

        // A sample Pemohon organisation with an activated user.
        $pemohon = Pemohon::factory()->create(['nama' => 'Agensi Contoh Sdn Bhd']);
        User::factory()->pemohon($pemohon)->create([
            'name' => 'Pegawai Pemohon',
            'email' => 'pemohon@epinjaman.test',
        ]);
    }
}
