<div class="list-group mb-3">
    @forelse($assignments as $assignment)
        <div class="list-group-item list-group-item-action mb-2 rounded-4 shadow-sm d-flex align-items-start">
            <input class="form-check-input row-checkbox me-4" type="checkbox" name="assignments[]" value="{{ $assignment->id }}">
            <div class="ms-3">
                <h5 class="mb-1 fw-bold text-guinda">TAG/DICO-{{ $assignment->asset->tag ?? '-' }}</h5>
                <p class="mb-0 text-secondary fs-5">
                    Tipo: {{ $assignment->asset->deviceType->equipo ?? '-' }} |
                    Serie: {{ $assignment->asset->serie ?? '-' }} |
                    Asignado: {{ $assignment->assigned_at->format('d/m/Y') }}
                </p>
                @if($assignment->observations)
                    <p class="mb-0 text-muted fs-6">
                        <strong>Observaciones:</strong> {{ $assignment->observations }}
                    </p>
                @endif
            </div>
        </div>
    @empty
        <div class="text-center py-5">
            <i class="fas fa-box-open fa-3x text-muted mb-3"></i>
            <p class="fs-5 text-muted">No hay activos asignados</p>
        </div>
    @endforelse
</div>
