<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('pemohons', function (Blueprint $table) {
            // Funding-source routing is an organisation attribute set by SID at
            // provisioning; each Permohonan inherits it at draft creation
            // (ticket 16, ADR-0004). Nullable so existing rows survive the add.
            $table->string('sumber_dana')->nullable()->after('status');
            $table->boolean('ada_kementerian_pengawal')->nullable()->after('sumber_dana');
            $table->foreignId('kementerian_pengawal_id')
                ->nullable()
                ->after('ada_kementerian_pengawal')
                ->constrained('kementerian_pengawals')
                ->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pemohons', function (Blueprint $table) {
            $table->dropConstrainedForeignId('kementerian_pengawal_id');
            $table->dropColumn(['sumber_dana', 'ada_kementerian_pengawal']);
        });
    }
};
