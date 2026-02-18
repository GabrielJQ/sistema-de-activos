@extends('layouts.admin')

@section('title', 'Activos con TAG: ' . $mainAsset->tag)

@section('content_header')
<div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-4">
    <div>
        <h1 class="fw-bold text-guinda mb-1">
            <i class="fas fa-barcode me-2"></i> TAG: {{ $mainAsset->tag }}
        </h1>
        <small class="text-muted">
            Vista consolidada de activos asociados al mismo TAG
        </small>
    </div>

    <a href="{{ route('assets.index') }}"
       class="btn btn-outline-secondary shadow-sm px-3">
        <i class="fas fa-arrow-left me-1"></i> Regresar
    </a>
</div>
@endsection

@section('content')
<div class="card shadow-sm border-0 rounded-4 overflow-hidden">

    {{-- CABECERA --}}
    <div class="card-header bg-guinda text-white fw-semibold d-flex align-items-center">
        <i class="fas fa-info-circle me-2"></i>
        Información general del TAG
    </div>

    <div class="card-body p-4">

        {{-- INFO GENERAL (TABLA CLÁSICA PARA RESGUARDO) --}}
        <div class="table-responsive mb-5">
            <table class="table align-middle mb-0">
                <tbody>
                    <tr>
                        <th style="width: 240px;">
                            <i class="fas fa-barcode me-1 text-guinda"></i> TAG
                        </th>
                        <td class="fw-bold fs-6">
                            {{ $mainAsset->tag }}
                        </td>
                    </tr>

                    <tr>
                        <th>
                            <i class="fas fa-user-shield me-1 text-guinda"></i> Resguardante
                        </th>
                        <td>
                            {{ $assets->first()?->currentHolder?->employee?->full_name ?? 'Informática' }}
                        </td>
                    </tr>

                    <tr class="table-light">
                        <th>
                            <i class="fas fa-building me-1 text-guinda"></i> Departamento
                        </th>
                        <td>
                            {{ $assets->first()?->department?->areanom ?? 'N/A' }}
                        </td>
                    </tr>

                    <tr class="table-light">
                        <th>
                            <i class="fas fa-calendar-alt me-1 text-guinda"></i> Fecha de asignación
                        </th>
                        <td>
                            {{ $assets->first()?->created_at?->format('d/m/Y') ?? 'N/A' }}
                        </td>
                    </tr>

                    <tr class="table-light">
                        <th>
                            <i class="fas fa-layer-group me-1 text-guinda"></i> Total de activos asociados
                        </th>
                        <td class="fw-bold">
                            {{ $assets->count() }}
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        {{-- SECCIÓN LISTA --}}
        <div class="d-flex align-items-center justify-content-between mb-3">
            <h5 class="fw-bold text-guinda mb-0">
                <i class="fas fa-desktop me-2"></i> Activos relacionados
            </h5>
            @if(hasRole(['super_admin','admin']))
                <button class="btn btn-guinda btn-sm px-3 shadow-sm"
                        data-bs-toggle="modal" data-bs-target="#bulkActionsModal">
                    <i class="fas fa-bolt me-1"></i> Acciones masivas
                </button>
            @endif
        </div>

        <div class="table-responsive">
            <table class="table table-hover align-middle assets-table">
                <thead class="table-light">
                    <tr>
                        <th style="width: 60px;">#</th>
                        <th>Equipo</th>
                        <th>Serie</th>
                        <th>Marca</th>
                        <th>Modelo</th>
                        <th style="width: 140px;">Estado</th>
                        @if(hasRole(['super_admin','admin']))
                            <th style="width: 100px;" class="text-center">Acciones</th>
                        @endif
                    </tr>
                </thead>
                <tbody>
                    @foreach($assets as $i => $asset)
                        <tr>
                            <td class="fw-semibold">{{ $i + 1 }}</td>
                            <td>{{ $asset->deviceType->equipo ?? 'N/A' }}</td>
                            <td>{{ $asset->serie }}</td>
                            <td>{{ $asset->marca }}</td>
                            <td>{{ $asset->modelo }}</td>
                            <td>
                                @switch($asset->estado)
                                    @case('DANADO')
                                        <span class="badge bg-danger px-3 py-2"
                                              data-bs-toggle="tooltip"
                                              title="Activo dañado">
                                            <i class="fas fa-exclamation-triangle me-1"></i>
                                            DANADO
                                        </span>
                                        @break

                                    @case('RESGUARDADO')
                                        <span class="badge bg-secondary px-3 py-2">
                                            RESGUARDADO
                                        </span>
                                        @break

                                    @case('OPERACION')
                                        <span class="badge bg-success px-3 py-2">
                                            OPERACIÓN
                                        </span>
                                        @break

                                    @default
                                        <span class="badge bg-info px-3 py-2">
                                            {{ $asset->estado }}
                                        </span>
                                @endswitch
                            </td>

                            @if(hasRole(['super_admin','admin']))
                                <td class="text-center">
                                    <a href="{{ route('assets.edit', $asset) }}"
                                       class="btn btn-warning btn-sm shadow-sm px-3"
                                       data-bs-toggle="tooltip"
                                       title="Editar activo">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                </td>
                            @endif
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

    </div>
</div>
@if(hasRole(['super_admin','admin']))
<div class="modal fade" id="bulkActionsModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <form method="POST" action="{{ route('assets.bulkUpdateByTag', $mainAsset->tag) }}" class="modal-content border-0 rounded-4 overflow-hidden">
      @csrf

      <div class="modal-header bg-guinda text-white">
        <h5 class="modal-title fw-bold">
          <i class="fas fa-bolt me-2"></i> Acciones masivas (TAG: {{ $mainAsset->tag }})
        </h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>

      <div class="modal-body p-4">

        <div class="alert alert-warning rounded-3">
          <i class="fas fa-exclamation-triangle me-2"></i>
          Esto aplicará cambios a <b>todos</b> los activos del TAG (excepto BAJA).
        </div>

        <div class="row g-3">

          {{-- Estado --}}
          <div class="col-md-6">
            <label class="form-label fw-semibold">Cambiar Estado (opcional)</label>
            <select name="bulk_estado" class="form-select">
              <option value="">-- No cambiar --</option>
              <option value="OPERACION">OPERACION</option>
              <option value="RESGUARDADO">RESGUARDADO</option>
              <option value="DANADO">DANADO</option>
              <option value="GARANTIA">GARANTIA</option>
              <option value="SINIESTRO">SINIESTRO</option>
              <option value="BAJA">BAJA</option>
              <option value="OTRO">OTRO</option>
            </select>
            <small class="text-muted d-block mt-1">
              Si eliges <b>BAJA</b> o <b>SINIESTRO</b>, se cerrarán asignaciones actuales.
            </small>
          </div>

          {{-- Proveedor --}}
          <div class="col-md-6">
            <label class="form-label fw-semibold">Cambiar Proveedor (opcional)</label>
            <select name="bulk_supplier_id" class="form-select">
              <option value="">-- No cambiar --</option>
              @foreach(\App\Models\Supplier::orderBy('prvnombre')->get() as $s)
                <option value="{{ $s->id }}">{{ $s->prvnombre }}</option>
              @endforeach
            </select>
          </div>

          {{-- Departamento --}}
          <div class="col-md-6">
            <label class="form-label fw-semibold">Cambiar Departamento (opcional)</label>
            <select name="bulk_department_id" class="form-select">
              <option value="">-- No cambiar --</option>
              @foreach(\App\Models\Department::orderBy('areanom')->get() as $d)
                <option value="{{ $d->id }}">{{ $d->areanom }}</option>
              @endforeach
            </select>
          </div>

          {{-- Marca --}}
          <div class="col-md-6">
            <label class="form-label fw-semibold">Cambiar Marca (opcional)</label>
            <select name="bulk_marca" class="form-select">
              <option value="">-- No cambiar --</option>
              @php
                $marcas = $assets->pluck('marca')->filter()->unique()->sort()->values();
              @endphp
              @foreach($marcas as $m)
                <option value="{{ $m }}">{{ $m }}</option>
              @endforeach
            </select>
          </div>

          {{-- Modelo --}}
          <div class="col-md-6">
            <label class="form-label fw-semibold">Cambiar Modelo (opcional)</label>
            <select name="bulk_modelo" class="form-select">
              <option value="">-- No cambiar --</option>
              @php
                $modelos = $assets->pluck('modelo')->filter()->unique()->sort()->values();
              @endphp
              @foreach($modelos as $m)
                <option value="{{ $m }}">{{ $m }}</option>
              @endforeach
            </select>
          </div>

        </div>
      </div>

      <div class="modal-footer bg-light">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
          Cancelar
        </button>
        <button type="submit" class="btn btn-guinda-outline px-4">
          <i class="fas fa-save me-1"></i> Aplicar cambios
        </button>
      </div>

    </form>
  </div>
</div>
@endif

@endsection

@section('css')
<style>
.text-guinda {
    color: #611232 !important;
}
.bg-guinda {
    background-color: #611232 !important;
}
.btn-guinda {
    background-color: #7a1f3d;
    color: #fff;
    border-radius: .55rem;
}
.btn-guinda:hover {
    background-color: #661a33;
    color: #fff;
}
.btn-guinda-outline {
    border-radius: .55rem;
    font-weight: 500;
    background: #fff !important;
    color: #611232 !important;
    border: 1px solid #611232 !important;
    transition: .25s ease-in-out;
}
.btn-guinda-outline:hover {
    background-color: #4b0f27 !important;
    border-color: #4b0f27 !important;
    color: #fff !important;
}

.info-table th {
    background-color: #f8f9fa;
    font-weight: 600;
}
.info-table td {
    background-color: #ffffff;
}

.assets-table thead th {
    font-size: 0.85rem;
    text-transform: uppercase;
    letter-spacing: 0.03em;
}
.assets-table tbody tr:hover {
    background-color: #fafafa;
}

/* BADGES */
.badge {
    font-size: 0.75rem;
    border-radius: 20px;
}

/* RESPONSIVE */
@media (max-width: 768px) {
    h1 {
        font-size: 1.3rem;
    }
}
</style>
@endsection
