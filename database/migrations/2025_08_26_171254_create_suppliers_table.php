<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        //Creación de la tabla suppliers
        Schema::create('suppliers', function (Blueprint $table) {
            $table->id(); 
            $table->string('prvnombre');
            $table->string('contrato')->nullable();
            $table->boolean('prvstatus')->default(true);
            $table->string('telefono')->nullable();
            $table->string('enlace')->nullable();
            $table->string('logo_path')->nullable();
            $table->timestamps();
        });

    }
    public function down(): void
    {
        Schema::dropIfExists('suppliers');
    }
};
