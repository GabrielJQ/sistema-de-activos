<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        //Creación de la tabla temporary_assignments
        Schema::create('temporary_assignments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('asset_assignment_id');
            $table->string('temporary_holder');
            $table->timestamps();

            $table->foreign('asset_assignment_id')
                  ->references('id')
                  ->on('asset_assignments')
                  ->onDelete('cascade');
        });
    }

    public function down(): void {
        Schema::dropIfExists('temporary_assignments');
    }
};
