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
        // 1. TRIGGER: Sincronización de Impresoras (SMIAB)
        DB::unprepared("
            CREATE OR REPLACE FUNCTION public.fn_sync_printer_snapshot() RETURNS TRIGGER AS $$
            DECLARE
                v_is_printer boolean;
                v_dept_id bigint; v_unit_id bigint; v_reg_id bigint; v_name text;
            BEGIN
                SELECT EXISTS (
                    SELECT 1 FROM public.assets a
                    JOIN public.device_types dt ON a.device_type_id = dt.id
                    WHERE a.id = NEW.asset_id AND (dt.equipo ILIKE '%Impresora%' OR dt.requires_ip = true)
                ) INTO v_is_printer;

                IF v_is_printer THEN
                    SELECT a.department_id, d.unit_id, u.region_id, CONCAT(a.marca, ' ', a.modelo)
                    INTO v_dept_id, v_unit_id, v_reg_id, v_name
                    FROM public.assets a
                    JOIN public.departments d ON a.department_id = d.id
                    JOIN public.units u ON d.unit_id = u.id
                    WHERE a.id = NEW.asset_id;

                    INSERT INTO public.printers (asset_id, ip_printer, name_printer, department_id, unit_id, region_id, updated_at)
                    VALUES (NEW.asset_id, NEW.ip_address, v_name, v_dept_id, v_unit_id, v_reg_id, NOW())
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

            DROP TRIGGER IF EXISTS trg_sync_printer_data ON public.asset_network_interfaces;

            CREATE TRIGGER trg_sync_printer_data
            AFTER INSERT OR UPDATE ON public.asset_network_interfaces
            FOR EACH ROW EXECUTE PROCEDURE public.fn_sync_printer_snapshot();
        ");

        // 2. TRIGGER: Sincronización de Usuarios (Auth -> Public)
        DB::unprepared("
            CREATE OR REPLACE FUNCTION public.handle_new_user() 
            RETURNS TRIGGER AS $$
            BEGIN
              INSERT INTO public.users (supabase_user_id, email, name, role, password)
              VALUES (
                new.id,
                new.email,
                COALESCE(new.raw_user_meta_data->>'name', 'Usuario Nuevo'),
                'visitor',
                '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi' -- default password 'password' for users created via Supabase trigger
              );
              RETURN new;
            END;
            $$ LANGUAGE plpgsql SECURITY DEFINER;

            DROP TRIGGER IF EXISTS on_auth_user_created ON auth.users;

            CREATE TRIGGER on_auth_user_created
              AFTER INSERT ON auth.users
              FOR EACH ROW EXECUTE PROCEDURE public.handle_new_user();
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revertir SMIAB
        DB::unprepared("
            DROP TRIGGER IF EXISTS trg_sync_printer_data ON public.asset_network_interfaces;
            DROP FUNCTION IF EXISTS public.fn_sync_printer_snapshot();
        ");

        // Revertir Auth
        DB::unprepared("
            DROP TRIGGER IF EXISTS on_auth_user_created ON auth.users;
            DROP FUNCTION IF EXISTS public.handle_new_user();
        ");
    }
};
