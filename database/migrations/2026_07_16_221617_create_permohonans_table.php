<?php

use App\Enums\PermohonanStatus;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('permohonans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pemohon_id')->constrained('pemohons')->cascadeOnDelete();
            $table->string('no_rujukan')->nullable()->unique();
            $table->string('status')->default(PermohonanStatus::Draf->value);

            // Structured form fields (base set — see spec: exact schema per
            // Garis Panduan ms 42-49 is out of scope for v1).
            $table->string('tajuk')->nullable();
            $table->decimal('jumlah_dipohon', 15, 2)->nullable();
            $table->text('tujuan')->nullable();
            $table->unsignedInteger('tempoh_bulan')->nullable();

            // Funding-source branch (ADR-0002).
            $table->string('sumber_dana')->nullable();
            $table->boolean('ada_kementerian_pengawal')->nullable();
            $table->foreignId('kementerian_pengawal_id')->nullable()->constrained('kementerian_pengawals')->nullOnDelete();

            $table->timestamp('dihantar_pada')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('permohonans');
    }
};
