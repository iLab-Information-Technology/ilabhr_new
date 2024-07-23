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
        Schema::create('vehicles', function (Blueprint $table) {
            $table->id();
            $table->integer('ilab_id');
            $table->integer('vehicle_type_id');
            $table->string('vehicle_plate_number');
            $table->integer('make_model_id');
            $table->integer('rental_company_id');
            $table->string('color');
            $table->integer('status')->comment('0=Active, 1=Inactive, 3=Replacement');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vehicles');
    }
};
