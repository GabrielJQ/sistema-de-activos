<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

    public function up(): void
    {
        //Creación de la tabla device_types
        Schema::create('device_types', function (Blueprint $table) {
            $table->id();
            $table->string('equipo');
            $table->string('descripcion');
            $table->string('image_path')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('device_types');
    }
};
