<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration 
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Crear la función del trigger (sync_printer_to_smiab)
        DB::unprepared('
            CREATE OR REPLACE FUNCTION sync_printer_to_smiab()
            RETURNS trigger AS $$
            DECLARE
                v_tag VARCHAR;
                v_marca VARCHAR;
                v_modelo VARCHAR;
                v_serie VARCHAR;
                v_areanom VARCHAR;
            BEGIN
                -- Buscar en las tablas assets y departments
                SELECT a.tag, a.marca, a.modelo, a.serie, d.areanom
                INTO v_tag, v_marca, v_modelo, v_serie, v_areanom
                FROM assets a
                LEFT JOIN departments d ON a.department_id = d.id
                WHERE a.id = NEW.asset_id;

                -- Insertar/Actualizar (Upsert) en la tabla printers
                INSERT INTO printers (ip_address, tag, marca, modelo, serie, areanom, created_at, updated_at)
                VALUES (NEW.ip_address, v_tag, v_marca, v_modelo, v_serie, v_areanom, NOW(), NOW())
                ON CONFLICT (ip_address) 
                DO UPDATE SET 
                    tag = EXCLUDED.tag,
                    marca = EXCLUDED.marca,
                    modelo = EXCLUDED.modelo,
                    serie = EXCLUDED.serie,
                    areanom = EXCLUDED.areanom,
                    updated_at = NOW();

                RETURN NEW;
            END;
            $$ LANGUAGE plpgsql;
        ');

        // 2. Crear el trigger en asset_network_interfaces
        DB::unprepared('
            CREATE TRIGGER after_network_interface_insert
            AFTER INSERT OR UPDATE ON asset_network_interfaces
            FOR EACH ROW
            EXECUTE FUNCTION sync_printer_to_smiab();
        ');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::unprepared('DROP TRIGGER IF EXISTS after_network_interface_insert ON asset_network_interfaces;');
        DB::unprepared('DROP FUNCTION IF EXISTS sync_printer_to_smiab();');
    }
};
