<?php

use App\Enums\UserRole;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * The Memo Pertimbangan (Kertas Pertimbangan) with draft key terms, which
     * travels the approval hierarchy (ticket 08 -> 09). `peringkat` tracks the
     * current hierarchy level; `keputusan` records YB MK's final decision.
     */
    public function up(): void
    {
        Schema::create('memos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('permohonan_id')->constrained('permohonans')->cascadeOnDelete();
            $table->foreignId('disediakan_oleh')->nullable()->constrained('users')->nullOnDelete();
            $table->text('terma_utama');
            $table->text('catatan')->nullable();
            $table->string('peringkat')->default(UserRole::PSID->value);
            $table->string('keputusan')->nullable();
            $table->timestamp('disediakan_pada')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('memos');
    }
};
