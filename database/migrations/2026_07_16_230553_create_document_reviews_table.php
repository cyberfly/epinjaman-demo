<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * PSID's per-item review of a Permohonan's supporting documents against the
     * Lampiran 6 checklist (ticket 06).
     */
    public function up(): void
    {
        Schema::create('document_reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('permohonan_id')->constrained('permohonans')->cascadeOnDelete();
            $table->foreignId('checklist_item_id')->constrained('checklist_items')->cascadeOnDelete();
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->boolean('is_complete')->default(false);
            $table->boolean('is_orderly')->default(false);
            $table->text('catatan')->nullable();
            $table->timestamps();

            $table->unique(['permohonan_id', 'checklist_item_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('document_reviews');
    }
};
