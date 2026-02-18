<div class="table-responsive shadow-sm rounded-4 bg-white p-3 table-card">
    <table class="table table-hover table-striped table-sm align-middle datatable modern-table">
        <thead class="table-dark text-white">
            <tr>
                <th>Expediente</th>
                <th>Nombre completo</th>
                <th>Departamento</th>
                <th>Puesto</th>
                <th>Tipo</th>
                <th>Email</th>
                <th>Teléfono</th>
                <th>Extensión</th>
                <th>Estado</th>
                <th class="{{ hasRole(['super_admin','admin','editor']) ? '' : 'd-none' }}">Acciones</th>
            </tr>
        </thead>

        <tbody>
            @foreach($employees as $emp)
                <tr>
                    <td class="fw-semibold">{{ $emp->expediente }}</td>
                    <td>{{ $emp->full_name }}</td>

                    <td class="text-muted">
                        <i class="fas fa-building me-1"></i>
                        {{ $emp->department->areanom ?? '—' }}
                    </td>

                    <td>{{ $emp->puesto }}</td>
                    <td>{{ $emp->tipo }}</td>
                    <td>{{ $emp->email }}</td>
                    <td>{{ $emp->telefono }}</td>
                    <td>{{ $emp->extension }}</td>

                    {{-- ESTADO --}}
                    <td class="text-center">
                        <span class="badge bg-guinda px-2 py-1 badge-soft">
                            {{ $emp->status }}
                        </span>
                    </td>

                    {{-- ACCIONES (estilo Bootstrap por defecto) --}}
                    <td class="{{ hasRole(['super_admin','admin','editor']) ? '' : 'd-none' }} text-center">

                        @if(hasRole(['super_admin','admin','editor']))
                            
                            {{-- EDITAR --}}
                            <a href="{{ route('employees.edit', $emp) }}"
                               class="btn btn-warning btn-sm mb-1">
                                <i class="fas fa-edit"></i>
                            </a>

                            {{-- ELIMINAR --}}
                            <button type="button"
                                class="btn btn-danger btn-sm mb-1"

                                data-confirm-delete
                                data-name="{{ $emp->full_name }}"
                                data-text="¿Deseas eliminar este empleado?"
                                data-action="{{ route('employees.destroy', $emp) }}"
                            >
                                <i class="fas fa-trash-alt"></i>
                            </button>

                        @endif

                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
