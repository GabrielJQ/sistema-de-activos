@php
    $damagedTab = isset($damagedTab) ? (bool)$damagedTab : false;
    $inactiveTab = isset($inactiveTab) ? (bool)$inactiveTab : false;
@endphp

<div class="table-responsive shadow-sm rounded-4 bg-white p-3 table-card">

    <table class="table table-hover table-striped table-sm align-middle datatable modern-table">
        <thead class="table-dark text-white">
            <tr>
                <th>TAG</th>
                <th>Equipo</th>
                <th>Marca</th>
                <th>Modelo</th>
                <th>Serie</th>
                <th>Estado</th>
                <th>Resguardo</th>
                <th>Departamento</th>
                <th class="{{ hasRole(['super_admin','admin']) ? '' : 'd-none' }}">Acciones</th>
            </tr>
        </thead>

        <tbody>
            @forelse($activos as $activo)
                <tr>
                    <td class="fw-semibold">{{ $activo->tag }}</td>

                    <td>{{ $activo->deviceType->equipo ?? '—' }}</td>
                    <td>{{ $activo->marca }}</td>
                    <td>{{ $activo->modelo }}</td>
                    <td>{{ $activo->serie }}</td>

                    {{-- Estado --}}
                    <td>
                        @if($activo->estado === 'DANADO')
                            <span data-bs-toggle="tooltip" title="En uso pero dañado">
                                <i class="fas fa-exclamation-triangle text-danger"></i>
                                {{ $activo->estado }}
                            </span>
                        @else
                            {{ $activo->estado }}
                        @endif
                    </td>

                    {{-- Resguardo --}}
                    <td>
                        {{ $activo->currentHolder?->employee->full_name ?? 'Informática' }}
                    </td>

                    {{-- Departamento --}}
                    <td>
                        @if($activo->estado === 'BAJA')
                            <span class="badge bg-danger">INACTIVO</span>

                        @elseif(empty($activo->currentHolder))
                            <span class="badge bg-warning text-dark">DISPONIBLE</span>

                        @else
                            {{ $activo->department->areanom ?? '—' }}
                        @endif
                    </td>

                    {{-- ACCIONES --}}
                    <td class="{{ hasRole(['super_admin','admin']) ? '' : 'd-none' }} text-center">

                        @if(hasRole(['super_admin','admin']))

                            {{-- Normal: ver grupo --}}
                            @if(!$damagedTab && !$inactiveTab)
                                <a href="{{ route('assets.group', $activo->tag) }}"
                                   class="btn btn-info btn-sm"
                                   data-bs-toggle="tooltip"
                                   title="Ver grupo por TAG">
                                    <i class="fas fa-eye"></i> Detalles
                                </a>
                            @endif

                            {{-- Solo DAÑADOS: editar + reporte --}}
                            @if($damagedTab)
                                <a href="{{ route('assets.edit', $activo->id) }}"
                                   class="btn btn-warning btn-sm"
                                   data-bs-toggle="tooltip"
                                   title="Editar activo dañado">
                                    <i class="fas fa-edit"></i> Editar
                                </a>

                                <a href="{{ route('assets.report', $activo->id) }}"
                                   class="btn btn-primary btn-sm"
                                   data-bs-toggle="tooltip"
                                   title="Reporte de daño">
                                    <i class="fas fa-file-alt me-1"></i> Reportar
                                </a>
                            @endif

                            {{-- Solo INACTIVOS: eliminar --}}
                            @if($inactiveTab)
                                <button type="button"
                                    class="btn btn-danger btn-sm text-white-hover"
                                    data-bs-toggle="tooltip"
                                    title="Eliminar activo inactivo"

                                    data-confirm-delete
                                    data-name="{{ $activo->tag }} — {{ $activo->deviceType->equipo ?? 'Activo' }}"
                                    data-text="¿Eliminar este activo permanentemente?"
                                    data-action="{{ route('assets.destroy', $activo->id) }}"
                                >
                                    <i class="fas fa-trash"></i> Eliminar
                                </button>

                            @endif

                        @endif

                    </td>

                </tr>
            @empty
                <tr>
                    <td colspan="9" class="text-center text-muted py-4">
                        No hay registros
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

</div>
@section('js')
<script>
    // Inicializar tooltips de Bootstrap
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
    const tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl)
    })
</script>
@stop
