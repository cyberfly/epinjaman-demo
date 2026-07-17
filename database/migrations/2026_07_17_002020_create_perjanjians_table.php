<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * The draft agreement uploaded by the Pemohon, reviewed by SID & BUU, and
     * confirmed orderly by BUU before manual signing (ticket 11). Also carries
     * the ticket-12 physical-process records (manual signing, stamping).
     */
    public function up(): void
    {
        Schema::create('perjanjians', function (Blueprint $table) {
            $table->id();
            $table->foreignId('permohonan_id')->constrained('permohonans')->cascadeOnDelete();
            $table->string('draf_path')->nullable();
            $table->string('draf_nama')->nullable();
            $table->foreignId('dimuat_naik_oleh')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('dimuat_naik_pada')->nullable();
            $table->boolean('disahkan_buu')->default(false);
            $table->foreignId('disahkan_oleh')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('disahkan_pada')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('perjanjians');
    }
};
