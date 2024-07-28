<?php

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
        Schema::table('vehicles', function (Blueprint $table) {
            $table->text('istimarah')->nullable();
            $table->text('istimarah_expiry_date')->nullable();
            $table->text('tamm_report')->nullable();
            $table->text('other_report')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('vehicles', function (Blueprint $table) {
            $table->dropColumn('istimarah');
            $table->dropColumn('istimarah_expiry_date');
            $table->dropColumn('tamm_report');
            $table->dropColumn('other_report');
        });
    }
};
