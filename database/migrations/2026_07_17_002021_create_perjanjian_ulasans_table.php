<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * SID / BUU review comments on the draft agreement (ticket 11).
     */
    public function up(): void
    {
        Schema::create('perjanjian_ulasans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('perjanjian_id')->constrained('perjanjians')->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('role')->nullable();
            $table->text('ulasan');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('perjanjian_ulasans');
    }
};
