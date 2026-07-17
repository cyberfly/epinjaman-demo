<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * A reply within a Kuiri thread — a message and/or an uploaded document,
     * posted by the Pemohon or a Kementerian Pengawal officer (ticket 07).
     */
    public function up(): void
    {
        Schema::create('kuiri_replies', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kuiri_id')->constrained('kuiris')->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('role')->nullable();
            $table->text('mesej')->nullable();
            $table->string('lampiran_path')->nullable();
            $table->string('lampiran_nama')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kuiri_replies');
    }
};
