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
        Schema::create('asset_replacements', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('original_asset_id');
            $table->unsignedBigInteger('replacement_asset_id');
            $table->text('reason')->nullable();
            $table->timestamp('date_replaced')->useCurrent();
            $table->timestamps();

            $table->foreign('original_asset_id')->references('id')->on('assets')->onDelete('cascade');
            $table->foreign('replacement_asset_id')->references('id')->on('assets')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('asset_replacements');
    }
};
