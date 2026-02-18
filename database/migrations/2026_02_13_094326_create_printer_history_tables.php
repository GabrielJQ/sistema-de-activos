<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB; // Added this line for DB::statement

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Tabla de Cambios de Tóner (Registro Manual)
        Schema::create('printer_toner_changes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('asset_id');
            $table->string('toner_model', 100)->nullable();
            $table->text('observations')->nullable();
            $table->timestamp('changed_at')->useCurrent();
            $table->timestamp('created_at')->useCurrent();

            $table->foreign('asset_id')
                ->references('asset_id')
                ->on('printers')
                ->onDelete('cascade');
        });

        // 2. Tabla de Estadísticas Mensuales (Contadores Específicos)
        Schema::create('printer_monthly_stats', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('asset_id');
            $table->integer('year');
            $table->integer('month');
            $table->bigInteger('print_total_delta')->default(0);
            $table->bigInteger('copy_delta')->default(0);
            $table->bigInteger('print_only_delta')->default(0);
            $table->timestamp('updated_at')->useCurrent();
            $table->timestamp('created_at')->useCurrent();

            $table->unique(['asset_id', 'year', 'month'], 'uq_printer_monthly_stats');
            $table->foreign('asset_id')
                ->references('asset_id')
                ->on('printers')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('printer_toner_changes');
        Schema::dropIfExists('printer_monthly_stats');
    }
};
