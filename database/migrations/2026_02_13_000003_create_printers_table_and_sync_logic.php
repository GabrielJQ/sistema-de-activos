<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 2. Tabla Operativa Printers (Snapshot)
        Schema::create('printers', function (Blueprint $table) {
            $table->unsignedBigInteger('asset_id')->primary();
            $table->ipAddress('ip_printer');
            $table->string('name_printer')->nullable();

            // Foreign keys matching the user requirements
            $table->unsignedBigInteger('department_id')->nullable();
            $table->unsignedBigInteger('unit_id')->nullable();
            $table->unsignedBigInteger('region_id')->nullable();

            $table->string('printer_status', 50)->default('UNKNOWN');
            $table->integer('toner_lvl')->default(0);
            $table->bigInteger('total_pages_printed')->default(0);
            $table->timestamp('last_read_at')->nullable();
            $table->timestamps();

            // Constraints
            $table->foreign('asset_id')->references('id')->on('assets')->onDelete('cascade');
            $table->foreign('department_id')->references('id')->on('departments');
            $table->foreign('unit_id')->references('id')->on('units');
            $table->foreign('region_id')->references('id')->on('regions');
        });

        // 3. Función y Trigger de Sincronización using raw SQL (Solo PostgreSQL)
        if (Schema::getConnection()->getDriverName() === 'pgsql') {
            DB::unprepared("
                CREATE OR REPLACE FUNCTION fn_sync_printer_snapshot() RETURNS TRIGGER AS $$
                DECLARE
                    v_is_printer boolean;
                    v_dept_id bigint; v_unit_id bigint; v_reg_id bigint; v_name text;
                BEGIN
                    -- Verificar si es impresora usando device_types
                    SELECT EXISTS (
                        SELECT 1 FROM public.assets a
                        JOIN public.device_types dt ON a.device_type_id = dt.id
                        WHERE a.id = NEW.asset_id AND (dt.equipo ILIKE '%Impresora%' OR dt.requires_ip = true)
                    ) INTO v_is_printer;

                    IF v_is_printer THEN
                        -- Obtener jerarquía
                        SELECT a.department_id, d.unit_id, u.region_id, CONCAT(a.marca, ' ', a.modelo)
                        INTO v_dept_id, v_unit_id, v_reg_id, v_name
                        FROM public.assets a
                        LEFT JOIN public.departments d ON a.department_id = d.id
                        LEFT JOIN public.units u ON d.unit_id = u.id
                        WHERE a.id = NEW.asset_id;

                        -- Upsert en printers
                        INSERT INTO public.printers (asset_id, ip_printer, name_printer, department_id, unit_id, region_id, updated_at, created_at)
                        VALUES (NEW.asset_id, NEW.ip_address, v_name, v_dept_id, v_unit_id, v_reg_id, NOW(), NOW())
                        ON CONFLICT (asset_id) DO UPDATE SET
                            ip_printer = EXCLUDED.ip_printer, 
                            department_id = EXCLUDED.department_id,
                            unit_id = EXCLUDED.unit_id, 
                            region_id = EXCLUDED.region_id, 
                            updated_at = NOW();
                    END IF;
                    RETURN NEW;
                END;
                $$ LANGUAGE plpgsql;

                CREATE TRIGGER trg_sync_printer_data
                AFTER INSERT OR UPDATE ON public.asset_network_interfaces
                FOR EACH ROW EXECUTE FUNCTION fn_sync_printer_snapshot();
            ");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::getConnection()->getDriverName() === 'pgsql') {
            DB::unprepared("
                DROP TRIGGER IF EXISTS trg_sync_printer_data ON public.asset_network_interfaces;
                DROP FUNCTION IF EXISTS fn_sync_printer_snapshot();
            ");
        }
        Schema::dropIfExists('printers');
    }
};
