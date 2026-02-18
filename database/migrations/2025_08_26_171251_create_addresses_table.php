<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        //Creación de la tabla addresses
        Schema::create('addresses', function (Blueprint $table) {
            $table->id();
            $table->string('calle')->nullable();
            $table->string('colonia')->nullable();
            $table->string('cp', 10)->nullable();
            $table->string('municipio')->nullable()->index();
            $table->string('ciudad')->nullable();
            $table->string('estado')->nullable()->index();
            $table->timestamps();
        });

    }
    public function down(): void
    {
        Schema::dropIfExists('addresses');
    }
};
