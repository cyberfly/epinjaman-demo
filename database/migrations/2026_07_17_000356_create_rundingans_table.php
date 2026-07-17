<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * A recorded negotiation session between SID and the Pemohon, capturing the
     * agreed loan terms (ticket 08).
     */
    public function up(): void
    {
        Schema::create('rundingans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('permohonan_id')->constrained('permohonans')->cascadeOnDelete();
            $table->foreignId('direkod_oleh')->nullable()->constrained('users')->nullOnDelete();
            $table->text('terma');
            $table->text('catatan')->nullable();
            $table->boolean('dipersetujui')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rundingans');
    }
};
