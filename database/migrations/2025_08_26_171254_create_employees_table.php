<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        //Creación de la tabla employees
        Schema::create('employees', function (Blueprint $table) {
            $table->id();
            $table->string('expediente', 20)->nullable()->index();
            $table->string('nombre', 255)->nullable();
            $table->string('apellido_pat', 65)->nullable();
            $table->string('apellido_mat', 65)->nullable();
            $table->string('curp', 18)->nullable();
            $table->unsignedBigInteger('department_id')->nullable();
            $table->string('puesto', 65)->nullable();
            $table->string('tipo', 50)->default('Sindicalizado');
            $table->string('email', 65)->nullable();
            $table->string('telefono', 65)->nullable();
            $table->string('extension', 65)->nullable();
            $table->string('status', 25)->nullable();
            $table->timestamps();

            $table->foreign('department_id')
                ->references('id')->on('departments')
                ->onDelete('set null');
        });


    }
    public function down(): void
    {
        Schema::dropIfExists('employees');
    }
};
