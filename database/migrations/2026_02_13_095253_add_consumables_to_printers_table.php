<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('printers', function (Blueprint $table) {
            $table->integer('kit_mttnce_lvl')->default(0)->after('toner_lvl');
            $table->integer('uni_img_lvl')->default(0)->after('kit_mttnce_lvl');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('printers', function (Blueprint $table) {
            $table->dropColumn(['kit_mttnce_lvl', 'uni_img_lvl']);
        });
    }
};
