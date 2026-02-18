<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        //Creación de la tabla units
        Schema::create('units', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('unicve'); // unicve
            $table->string('uninom');                     // uninom
            $table->foreignId('region_id')->constrained('regions')->cascadeOnDelete();
            $table->timestamps();
        });

    }
    public function down(): void
    {
        Schema::dropIfExists('units');
    }
};
