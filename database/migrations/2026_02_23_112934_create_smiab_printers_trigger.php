<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration 
{
    public function up(): void
    {
        DB::unprepared("
            -- ==========================================================
            -- FUNCIÓN 1: Cuando cambia la tabla de Red (IP Nueva o Editada)
            -- ==========================================================
            CREATE OR REPLACE FUNCTION sync_printer_from_network()
            RETURNS TRIGGER AS $$
            DECLARE
                v_name VARCHAR; v_dept_id BIGINT; v_unit_id BIGINT; v_reg_id BIGINT; v_is_printer BOOLEAN;
            BEGIN
                -- Verificar si el asset es impresora o requiere IP
                SELECT EXISTS (
                    SELECT 1 FROM assets a
                    JOIN device_types dt ON a.device_type_id = dt.id
                    WHERE a.id = NEW.asset_id AND (dt.equipo ILIKE '%Impresora%' OR dt.requires_ip = true)
                ) INTO v_is_printer;

                IF v_is_printer THEN
                    SELECT CONCAT(a.marca, ' ', d.areanom), a.department_id, d.unit_id, u.region_id
                    INTO v_name, v_dept_id, v_unit_id, v_reg_id
                    FROM assets a
                    LEFT JOIN departments d ON a.department_id = d.id
                    LEFT JOIN units u ON d.unit_id = u.id
                    WHERE a.id = NEW.asset_id;

                    INSERT INTO printers (asset_id, ip_printer, name_printer, department_id, unit_id, region_id, created_at, updated_at)
                    VALUES (NEW.asset_id, TRIM(NEW.ip_address::text)::inet, v_name, v_dept_id, v_unit_id, v_reg_id, NOW(), NOW())
                    ON CONFLICT (asset_id) 
                    DO UPDATE SET 
                        ip_printer = EXCLUDED.ip_printer, 
                        name_printer = EXCLUDED.name_printer, 
                        department_id = EXCLUDED.department_id, 
                        unit_id = EXCLUDED.unit_id, 
                        region_id = EXCLUDED.region_id, 
                        updated_at = NOW();
                END IF;
                RETURN NEW;
            END;
            $$ LANGUAGE plpgsql;

            CREATE TRIGGER after_network_interface_change
            AFTER INSERT OR UPDATE ON asset_network_interfaces
            FOR EACH ROW EXECUTE FUNCTION sync_printer_from_network();

            -- ==========================================================
            -- FUNCIÓN 2: Cuando cambia el Activo (Ej. Cambio de Departamento)
            -- ==========================================================
            CREATE OR REPLACE FUNCTION sync_printer_from_asset()
            RETURNS TRIGGER AS $$
            DECLARE
                v_ip_address inet; v_name VARCHAR; v_dept_name VARCHAR; v_unit_id BIGINT; v_reg_id BIGINT; v_is_printer BOOLEAN;
            BEGIN
                -- Buscar si este activo tiene una IP registrada
                SELECT ip_address INTO v_ip_address
                FROM asset_network_interfaces
                WHERE asset_id = NEW.id LIMIT 1;

                -- Si tiene IP, significa que es monitoreable, actualizamos en printers
                IF v_ip_address IS NOT NULL THEN
                    SELECT EXISTS (
                        SELECT 1 FROM device_types dt WHERE dt.id = NEW.device_type_id AND (dt.equipo ILIKE '%Impresora%' OR dt.requires_ip = true)
                    ) INTO v_is_printer;

                    IF v_is_printer THEN
                        -- Recuperar los ids de unidad y región basándonos en el nuevo department_id
                        SELECT u.id, u.region_id, d.areanom
                        INTO v_unit_id, v_reg_id, v_dept_name
                        FROM departments d
                        LEFT JOIN units u ON d.unit_id = u.id
                        WHERE d.id = NEW.department_id;

                        v_name := CONCAT(NEW.marca, ' ', v_dept_name);

                        INSERT INTO printers (asset_id, ip_printer, name_printer, department_id, unit_id, region_id, created_at, updated_at)
                        VALUES (NEW.id, TRIM(v_ip_address::text)::inet, v_name, NEW.department_id, v_unit_id, v_reg_id, NOW(), NOW())
                        ON CONFLICT (asset_id)
                        DO UPDATE SET 
                            name_printer = EXCLUDED.name_printer, 
                            department_id = EXCLUDED.department_id, 
                            unit_id = EXCLUDED.unit_id, 
                            region_id = EXCLUDED.region_id, 
                            updated_at = NOW();
                    END IF;
                END IF;
                RETURN NEW;
            END;
            $$ LANGUAGE plpgsql;

            CREATE TRIGGER after_asset_change
            AFTER INSERT OR UPDATE ON assets
            FOR EACH ROW EXECUTE FUNCTION sync_printer_from_asset();
        ");
    }

    public function down(): void
    {
        DB::unprepared("
            DROP TRIGGER IF EXISTS after_network_interface_change ON asset_network_interfaces;
            DROP FUNCTION IF EXISTS sync_printer_from_network();
            
            DROP TRIGGER IF EXISTS after_asset_change ON assets;
            DROP FUNCTION IF EXISTS sync_printer_from_asset();
        ");
    }
};