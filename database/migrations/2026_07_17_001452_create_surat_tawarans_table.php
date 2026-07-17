<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * The official offer letter (Surat Tawaran) with the Key Terms & Conditions
     * attachment, auto-generated on approval (ticket 10).
     */
    public function up(): void
    {
        Schema::create('surat_tawarans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('permohonan_id')->constrained('permohonans')->cascadeOnDelete();
            $table->text('kandungan');
            $table->text('terma_utama');
            $table->timestamp('dijana_pada');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('surat_tawarans');
    }
};
