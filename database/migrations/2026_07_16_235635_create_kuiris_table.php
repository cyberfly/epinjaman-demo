<?php

use App\Enums\KuiriStatus;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * A single query item raised by PSID against a Permohonan (ticket 07). A
     * Permohonan may have several active Kuiri simultaneously.
     */
    public function up(): void
    {
        Schema::create('kuiris', function (Blueprint $table) {
            $table->id();
            $table->foreignId('permohonan_id')->constrained('permohonans')->cascadeOnDelete();
            $table->foreignId('dicetus_oleh')->nullable()->constrained('users')->nullOnDelete();
            $table->string('tajuk');
            $table->text('sebab');
            $table->string('status')->default(KuiriStatus::Terbuka->value);
            $table->timestamp('tarikh_akhir')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kuiris');
    }
};
