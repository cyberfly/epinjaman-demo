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
        Schema::table('users', function (Blueprint $table) {
            $table->string('role')->nullable()->after('email');
            $table->foreignId('pemohon_id')->nullable()->after('role')->constrained('pemohons')->nullOnDelete();
            $table->foreignId('kementerian_pengawal_id')->nullable()->after('pemohon_id')->constrained('kementerian_pengawals')->nullOnDelete();
            $table->timestamp('activated_at')->nullable()->after('kementerian_pengawal_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropConstrainedForeignId('pemohon_id');
            $table->dropConstrainedForeignId('kementerian_pengawal_id');
            $table->dropColumn(['role', 'activated_at']);
        });
    }
};
