<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Physical-process records (ticket 12): manual signing session and LHDNM
     * stamping. Recorded as status + date + officer (+ optional scan), not
     * automated — the actual process happens outside the system.
     */
    public function up(): void
    {
        Schema::table('perjanjians', function (Blueprint $table) {
            $table->boolean('tandatangan_manual_selesai')->default(false)->after('disahkan_pada');
            $table->date('tandatangan_manual_tarikh')->nullable()->after('tandatangan_manual_selesai');
            $table->foreignId('tandatangan_manual_oleh')->nullable()->after('tandatangan_manual_tarikh')->constrained('users')->nullOnDelete();
            $table->string('tandatangan_manual_imbasan_path')->nullable()->after('tandatangan_manual_oleh');

            $table->boolean('penyeteman_selesai')->default(false)->after('tandatangan_manual_imbasan_path');
            $table->date('penyeteman_tarikh')->nullable()->after('penyeteman_selesai');
            $table->foreignId('penyeteman_oleh')->nullable()->after('penyeteman_tarikh')->constrained('users')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('perjanjians', function (Blueprint $table) {
            $table->dropConstrainedForeignId('tandatangan_manual_oleh');
            $table->dropConstrainedForeignId('penyeteman_oleh');
            $table->dropColumn([
                'tandatangan_manual_selesai',
                'tandatangan_manual_tarikh',
                'tandatangan_manual_imbasan_path',
                'penyeteman_selesai',
                'penyeteman_tarikh',
            ]);
        });
    }
};
