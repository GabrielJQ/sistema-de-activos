#  Manual de Despliegue: Optimización de Rendimiento y Arquitectura Asíncrona (Livewire)

**Rama:** `perf/optimizacion-general-y-livewire`

Este documento detalla todas las modificaciones arquitectónicas implementadas en esta rama para resolver cuellos de botella críticos de memoria (Memory Bounds / N+1) y latencia en el sistema SIAT. Además, incluye las instrucciones obligatorias para el equipo de Infraestructura / DevOps al momento de desplegar estos cambios a Producción.

---

## 🛑 PASO OBLIGATORIO DE DESPLIEGUE A PRODUCCIÓN

Debido a que hemos migrado las tablas pesadas a una arquitectura reactiva (DOM Virtual), el sistema ahora tiene una dependencia obligatoria en **Laravel Livewire v3**.

Inmediatamente después de hacer `git pull` (o aceptar el Merge Request en la rama `main`), el equipo encargado de Producción **debe ejecutar los siguientes 2 comandos en el servidor**:

```bash
# 1. Instalar la nueva dependencia del core (Livewire v3) sin paquetes de desarrollo
composer install --optimize-autoloader --no-dev

# 2. Limpiar profundamente los cachés del framework
php artisan optimize:clear
```

### ¿Por qué son necesarios estos comandos?
- **El paso 1** descargará el motor de Livewire que introdujimos en el `composer.json`. Sin él, el sistema dará error `Class 'Livewire' not found`.
- **El paso 2** es vital para borrar la compilación antigua de las vistas (`.blade.php`). Dado que cambiamos la estructura de los `<table...>` nativos por componentes asíncronos `<livewire:...>`, si no se borra la caché de vistas, Laravel intentará mostrar la versión vieja del historial o del dashboard resultando en pantallas rotas.

---

## 🛠 Cambios Técnicos Arquitectónicos Implementados (Resumen de Mejoras)

### 1. Qué resolvimos (El problema de Memoria RAM)
El principal riesgo previo en el SAI era el problema N+1 y el sobreconsumo estático. En vistas críticas (como el *Historial* de Empleados/Asignaciones), el sistema extraía a la memoria RAM el 100% de los registros de la base de datos *antes* de renderizar el HTML.
Con un volumen de datos en crecimiento (miles de empleados o etiquetas físicas), esta arquitectura estaba destinada a causar un colapso del servidor (`Out of Memory`).

### 2. La Solución (Migración a Livewire v3)
Migramos todo el esquema de presentación (Client-Side Rendering masivo con `DataTables`) a un esquema Reactivo Bajo Demanda (Lazy Loading Asíncrono):

*   **Paginación Directa a BD:** Ahora, si el usuario navega a la página 3, Livewire viaja "por debajo de la mesa" al servidor y solo extrae **10 registros exactos** usando PostgreSQL nativo, consumiendo kilobytes en lugar de megabytes.
*   **Búsqueda en Tiempo Real:** Filtros ultra rápidos `wire:model.live.debounce` que reaccionan sin recargar la página.

### 3. Módulos Optimizados Oficialmente
Se reprogramaron completamente los siguientes submódulos y controladores para interactuar bajo Livewire:
- Historial General de Movimientos (`HistoryIndex`) **[Criticidad Alta]**
- Dashboard Principal Estadísticas **[Optimización SQL]**
- Administrador de Bienes (`AssetIndex`)
- Administrador de Asignaciones (`AssetAssignmentIndex`)
- Catálogos de Usuarios, Proveedores, Puestos y Empleados (`UserIndex`, `SupplierIndex`, `DepartmentIndex`, `DeviceTypeIndex`, `EmployeeIndex`).

### 4. Corrección en Lógica SQL (Dashboard Controller)
- **Top Departamentos/Empleados:** Resolvimos el problema N+1 delegando los conteos a un `GROUP BY` y un `COUNT()` real en SQL, evitando iterar colecciones inmensas en PHP.
- **Top Proveedores:** Se ajustó el filtro de equipos de cómputo para abarcar universalmente los proveedores de otros bienes (ej. `SYNNEX` provee 'No Breaks'), solucionando la estadística engañosa o "faltante".
- **Castreo Postgres:** Se fijó un problema silencioso sobre la comparación de strings en condicionales booleanas resolviéndolo elegantemente con `DB::raw('true')`.

---
*Este avance blinda funcionalmente al proyecto SAI frente al escalamiento inminente de información para este año, manteniendo un tiempo de latencia sub-segundo en todas sus operaciones maestras.*
