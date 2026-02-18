<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        //Creación de la tabla asset_assignments
        Schema::create('asset_assignments', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('asset_id');
            $table->unsignedBigInteger('employee_id')->nullable();
            $table->unsignedBigInteger('department_id')->nullable();

            $table->timestamp('assigned_at')->useCurrent();
            $table->timestamp('returned_at')->nullable();
            $table->boolean('is_current')->default(true);

            $table->enum('assignment_type', ['normal', 'temporal'])->default('normal');
            $table->text('observations')->nullable();
            $table->timestamps();

            $table->foreign('asset_id')->references('id')->on('assets')->onDelete('cascade');
            $table->foreign('employee_id')->references('id')->on('employees')->onDelete('set null');
            $table->foreign('department_id')->references('id')->on('departments')->onDelete('set null');

            $table->index(['asset_id', 'is_current']);
            $table->index(['employee_id', 'is_current']);
            $table->index(['department_id', 'is_current']);
        });
    
    }

    public function down(): void {
        Schema::dropIfExists('asset_assignments');
    }
};
