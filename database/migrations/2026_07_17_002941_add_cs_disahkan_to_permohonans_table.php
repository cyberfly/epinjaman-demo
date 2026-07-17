<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * The single manual confirmation field for Syarat Kemudian (CS) — out of
     * scope for a detailed workflow in v1 (see CONTEXT.md).
     */
    public function up(): void
    {
        Schema::table('permohonans', function (Blueprint $table) {
            $table->boolean('cs_disahkan')->default(false)->after('penyeteman_pada');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('permohonans', function (Blueprint $table) {
            $table->dropColumn('cs_disahkan');
        });
    }
};
