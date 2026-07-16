<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Supporting documents uploaded against a Permohonan, optionally tied to a
     * Lampiran 6 checklist item.
     */
    public function up(): void
    {
        Schema::create('permohonan_documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('permohonan_id')->constrained('permohonans')->cascadeOnDelete();
            $table->foreignId('checklist_item_id')->nullable()->constrained('checklist_items')->nullOnDelete();
            $table->foreignId('uploaded_by')->nullable()->constrained('users')->nullOnDelete();
            $table->string('path');
            $table->string('original_name');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('permohonan_documents');
    }
};
