<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\DeviceType;

class DeviceTypeSeeder extends Seeder
{
    public function run(): void
    {
        // Define tipos de dispositivos con descripciones
        $deviceTypes = [
            'Access Point' => 'Dispositivo para ampliar cobertura de red inalámbrica',
            'Biometrico' => 'Dispositivo para control de acceso por huella',
            'Cargador' => 'Cargador de laptop intermedia o avanzado',
            'Cargador Docking' => 'Cargador para base de expansión',
            'CCTV' => 'Sistema de videovigilancia de circuito cerrado',
            'Docking' => 'Base de expansión para laptops',
            'Equipo All In One' => 'Computadora con monitor integrado',
            'Equipo Escritorio' => 'Computadora de escritorio',
            'Escritorio Avanzada' => 'Equipo de escritorio para servidor',
            'Firewall' => 'Dispositivo de seguridad para red',
            'Impresora Multifuncional' => 'Impresora con escáner, copiadora e impresión',
            'Laptop de Avanzada' => 'Laptop con periféricos avanzados',
            'Laptop de Intermedia' => 'Laptop',
            'Lector DVD' => 'Unidad lectora de discos DVD',
            'Modem Satelital' => 'Dispositivo de conexión a internet vía satélite',
            'Monitor' => 'Pantalla para visualización de contenido',
            'Mouse' => 'Dispositivo apuntador',
            'No Break' => 'Sistema de respaldo eléctrico (UPS básico)',
            'Portatil' => 'Dispositivo móvil, generalmente laptop',
            'Proyector' => 'Dispositivo para proyectar imagen o video',
            'Router' => 'Router para conexión a internet',
            'Router LTE' => 'Router con conexión LTE para red celular',
            'Switch' => 'Dispositivo de red para interconexión de equipos',
            'Tableta' => 'Dispositivo táctil portátil',
            'Teclado' => 'Periférico de entrada de texto',
            'Telefonia IP' => 'Teléfono con IP',
            'Telefono Analogico' => 'Teléfono analógico tradicional',
            'UPS' => 'Sistema de alimentación ininterrumpida',
        ];

        foreach ($deviceTypes as $equipo => $descripcion) {
            DeviceType::create([
                'equipo' => $equipo,
                'descripcion' => $descripcion,
                'image_path' => 'images/dispositivos/' . strtolower(str_replace(' ', '_', $equipo)) . '.png',
            ]);
        }
    }
}
