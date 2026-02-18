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
        //Creación de la tabla assets
        Schema::create('assets', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('device_type_id');
            $table->string('tag')->nullable();
            $table->string('serie')->nullable();
            $table->string('marca')->nullable();
            $table->string('modelo')->nullable();
            
            $table->string('propiedad', 50)->default('ARRENDADO');
            
            $table->unsignedBigInteger('supplier_id');
            $table->unsignedBigInteger('department_id')->nullable(); 
            $table->string('activo', 35)->nullable(); // Número de inventario definido por la empresa

            $table->string('estado', 50)->default('OPERACION');

            $table->timestamps();

            // Relaciones
            $table->foreign('device_type_id')->references('id')->on('device_types')->onDelete('cascade');
            $table->foreign('supplier_id')->references('id')->on('suppliers')->onDelete('cascade');
            $table->foreign('department_id')->references('id')->on('departments')->onDelete('set null');
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('assets');
    }
};
