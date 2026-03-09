<div>
    <div class="tabs-wrap px-3 pt-3 pb-2 border-bottom">
        <ul class="nav nav-pills modern-pills gap-2" role="tablist">
            <li class="nav-item">
                <button wire:click="setTab('employees')" 
                        class="nav-link pill-tab {{ $activeTab === 'employees' ? 'active' : '' }}" 
                        type="button">
                    <i class="fas fa-user me-1"></i> Empleados
                </button>
            </li>
            @if(hasRole(['super_admin','admin','collaborator']))
            <li class="nav-item">
                <button wire:click="setTab('assets')" 
                        class="nav-link pill-tab {{ $activeTab === 'assets' ? 'active' : '' }}" 
                        type="button">
                    <i class="fas fa-laptop me-1"></i> Bienes
                </button>
            </li>
            @endif
        </ul>
    </div>

    <div class="tab-content p-3 p-md-4">
        @if($activeTab === 'employees')
            {{-- ================= EMPLEADOS ================= --}}
            <div class="tab-pane fade show active">
                <div class="search-bar-container mb-4">
                    <div class="input-group search-group shadow-sm">
                        <span class="input-group-text border-0 bg-white ps-3">
                            <i class="fas fa-search text-muted"></i>
                        </span>
                        <input type="text" 
                               wire:model.live.debounce.300ms="searchEmployees" 
                               class="form-control border-0 py-2 shadow-none" 
                               placeholder="Buscar por nombre, apellido o departamento...">
                    </div>
                </div>

                <div class="table-card">
                    <div class="table-card-head d-flex justify-content-between align-items-center">
                        <div class="d-flex align-items-center gap-2">
                            <span class="mini-dot bg-guinda"></span>
                            <div class="fw-semibold text-dark">Empleados con historial</div>
                        </div>
                    </div>

                    <div class="table-responsive p-0">
                        <table class="table table-hover align-middle modern-table w-100 mb-0">
                            <thead class="table-head">
                                <tr>
                                    <th class="text-start ps-4">Empleado</th>
                                    <th class="text-start">Departamento</th>
                                    <th class="text-center">Status</th>
                                    <th class="text-center pe-4">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($employees as $employee)
                                    <tr>
                                        <td class="text-start ps-4">
                                            <div class="d-flex align-items-center gap-2">
                                                <span class="avatar-soft">
                                                    <i class="fas fa-user"></i>
                                                </span>
                                                <div class="lh-sm">
                                                    <div class="fw-semibold text-dark">{{ $employee->full_name }}</div>
                                                    @if(!empty($employee->puesto))
                                                        <small class="text-muted">{{ $employee->puesto }}</small>
                                                    @endif
                                                </div>
                                            </div>
                                        </td>
                                        <td class="text-start">
                                            <span class="chip chip-muted">
                                                <i class="fas fa-building me-1"></i>
                                                {{ $employee->department?->areanom ?? '-' }}
                                            </span>
                                        </td>
                                        <td class="text-center">
                                            @php $status = strtolower($employee->status ?? ''); @endphp
                                            @if($status === 'activo')
                                                <span class="status-pill status-ok">
                                                    <i class="fas fa-check me-1"></i> Activo
                                                </span>
                                            @elseif($status === 'inactivo')
                                                <span class="status-pill status-bad">
                                                    <i class="fas fa-times me-1"></i> Inactivo
                                                </span>
                                            @else
                                                <span class="status-pill status-mid">
                                                    <i class="fas fa-minus me-1"></i> Sin definir
                                                </span>
                                            @endif
                                        </td>
                                        <td class="text-center pe-4">
                                            <a href="{{ route('history.showEmployee', $employee->id) }}"
                                               class="btn btn-guinda btn-sm px-3 rounded-3">
                                                <i class="fas fa-eye me-1"></i> Ver historial
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="empty-cell">
                                            <div class="empty-wrap">
                                                <div class="empty-icon"><i class="fas fa-users-slash"></i></div>
                                                <div class="fw-semibold text-dark mb-1">Sin resultados</div>
                                                <div class="text-muted">No se encontraron empleados.</div>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="mt-4 px-2">
                    {{ $employees->links() }}
                </div>
            </div>
        @else
            {{-- ================= BIENES ================= --}}
            <div class="tab-pane fade show active">
                <div class="search-bar-container mb-4">
                    <div class="input-group search-group shadow-sm">
                        <span class="input-group-text border-0 bg-white ps-3">
                            <i class="fas fa-search text-muted"></i>
                        </span>
                        <input type="text" 
                               wire:model.live.debounce.300ms="searchAssets" 
                               class="form-control border-0 py-2 shadow-none" 
                               placeholder="Buscar por TAG, Serie o Tipo...">
                    </div>
                </div>

                <div class="table-card">
                    <div class="table-card-head">
                        <div class="d-flex align-items-center gap-2">
                            <span class="mini-dot bg-guinda"></span>
                            <div class="fw-semibold text-dark">Activos con historial</div>
                        </div>
                    </div>

                    <div class="table-responsive p-0">
                        <table class="table table-hover align-middle modern-table w-100 mb-0">
                            <thead class="table-head">
                                <tr>
                                    <th class="text-start ps-4">TAG / DICO</th>
                                    <th class="text-start">Tipo</th>
                                    <th class="text-start">Serie</th>
                                    <th class="text-center">Status</th>
                                    <th class="text-center pe-4">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($assets as $asset)
                                    <tr>
                                        <td class="text-start ps-4">
                                            <span class="tag-badge">
                                                <i class="fas fa-tag me-1"></i>
                                                {{ $asset->tag }}
                                            </span>
                                        </td>
                                        <td class="text-start">
                                            <span class="chip chip-muted">
                                                <i class="fas fa-laptop me-1"></i>
                                                {{ $asset->deviceType?->equipo ?? '-' }}
                                            </span>
                                        </td>
                                        <td class="text-start">
                                            <span class="fw-semibold text-dark">{{ $asset->serie ?? '-' }}</span>
                                        </td>
                                        <td class="text-center">
                                            <span class="status-pill {{ $asset->isDecommissioned() ? 'status-bad' : 'status-ok' }}">
                                                @if($asset->isDecommissioned())
                                                    <i class="fas fa-ban me-1"></i> Baja
                                                @else
                                                    <i class="fas fa-play-circle me-1"></i> En operación
                                                @endif
                                            </span>
                                        </td>
                                        <td class="text-center pe-4">
                                            <a href="{{ route('history.showAsset', $asset->id) }}"
                                               class="btn btn-guinda btn-sm px-3 rounded-3">
                                                <i class="fas fa-eye me-1"></i> Ver historial
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="empty-cell">
                                            <div class="empty-wrap">
                                                <div class="empty-icon"><i class="fas fa-box-open"></i></div>
                                                <div class="fw-semibold text-dark mb-1">Sin resultados</div>
                                                <div class="text-muted">No se encontraron activos.</div>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="mt-4 px-2">
                    {{ $assets->links() }}
                </div>
            </div>
        @endif
    </div>
</div>
