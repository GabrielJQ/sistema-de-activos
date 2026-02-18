<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        //Creación de la tabla regions
        Schema::create('regions', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('regcve')->unique(); 
            $table->string('regnom')->unique();         
            $table->timestamps();
        });

    }

    public function down(): void
    {
        Schema::dropIfExists('regions');
    }
};
