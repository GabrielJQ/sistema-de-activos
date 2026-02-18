<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        //Creación de la tabla departments
       Schema::create('departments', function (Blueprint $table) {
            $table->id();

            // Relaciones normalizadas
            $table->foreignId('unit_id')->constrained('units')->cascadeOnDelete();
            $table->foreignId('address_id')->nullable()->constrained('addresses')->nullOnDelete();

            // Área y tipo
            $table->unsignedInteger('areacve')->index();
            $table->string('areanom')->index();
            $table->enum('tipo', ['Oficina', 'Almacen', 'Otro'])->default('Oficina')->index();

            $table->timestamps();
        });

    }
    public function down(): void
    {
        Schema::dropIfExists('departments');
    }
};
