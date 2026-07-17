<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * A Conditions Precedent (Syarat Duluan) item: one document per jenis,
     * uploaded by the Peminjam and verified by PSID (ticket 13).
     */
    public function up(): void
    {
        Schema::create('syarat_duluans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('permohonan_id')->constrained('permohonans')->cascadeOnDelete();
            $table->string('jenis');
            $table->string('dokumen_path')->nullable();
            $table->string('dokumen_nama')->nullable();
            $table->foreignId('dimuat_naik_oleh')->nullable()->constrained('users')->nullOnDelete();
            $table->boolean('disahkan')->default(false);
            $table->foreignId('disahkan_oleh')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('disahkan_pada')->nullable();
            $table->timestamps();

            $table->unique(['permohonan_id', 'jenis']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('syarat_duluans');
    }
};
