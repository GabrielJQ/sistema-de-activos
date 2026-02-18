@extends('layouts.admin')

@section('title', 'Preparar Resguardo')

@section('content')
<div class="container py-4 prepare-receipt">

    {{-- Header --}}
    <div class="d-flex flex-wrap justify-content-between align-items-start gap-3 mb-4">
        <div class="d-flex align-items-start gap-3">
            <span class="icon-circle bg-guinda text-white shadow-soft">
                <i class="fas fa-file-signature"></i>
            </span>
            <div>
                <h1 class="fw-bold text-guinda mb-1">Preparar Resguardo</h1>
                <small class="text-muted">Completa los datos antes de generar el PDF</small>
            </div>
        </div>

        <a href="{{ url()->previous() }}" class="btn btn-secondary btn-sm px-3 shadow-sm">
            <i class="fas fa-arrow-left me-1"></i> Volver Asignaciones del Empleado
        </a>
    </div>

    <div class="row g-4 align-items-start">

        {{-- Resumen de selección --}}
        <div class="col-12 col-lg-5">
            <div class="card card-modern shadow-soft rounded-4 overflow-hidden">
                <div class="card-header card-header-soft d-flex align-items-center justify-content-between">
                    <div class="d-flex align-items-center gap-2">
                        <i class="fas fa-list text-guinda"></i>
                        <span class="fw-bold">Resumen</span>
                    </div>
                </div>

                <div class="card-body">
                    <div class="info-pill mb-3">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <div class="text-muted small mb-1">Resguardante</div>
                                <div class="fw-bold">{{ $employee->full_name }}</div>
                            </div>
                            <i class="fas fa-user-check text-guinda opacity-75"></i>
                        </div>
                    </div>

                    <div class="section-mini-title mb-2">
                        <i class="fas fa-tags me-1"></i> Grupos seleccionados
                    </div>

                    <div class="summary-list">
                        @foreach($groupedByTag as $tag => $items)
                            <div class="summary-item">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div class="text-truncate fw-semibold">{{ $tag }}</div>
                                    <span class="badge bg-secondary">{{ $items->count() }}</span>
                                </div>
                                <div class="small text-muted mt-1">
                                    {{ $items->pluck('asset.deviceType.equipo')->filter()->unique()->implode(', ') }}
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <div class="hint-box mt-3">
                        <i class="fas fa-circle-info me-1"></i>
                        Revisa que los datos de firma y adscripción estén completos para evitar campos vacíos en el PDF.
                    </div>
                </div>
            </div>
        </div>

        {{-- Formulario de datos editables --}}
        <div class="col-12 col-lg-7">
            <form method="POST" action="{{ route('asset_assignments.previewReceipt') }}" target="_blank">
                @csrf

                @foreach($ids as $id)
                    <input type="hidden" name="ids[]" value="{{ $id }}">
                @endforeach

                <div class="card card-modern shadow-soft rounded-4 overflow-hidden">
                    <div class="card-header card-header-soft d-flex align-items-center justify-content-between">
                        <div class="d-flex align-items-center gap-2">
                            <i class="fas fa-pen text-guinda"></i>
                            <span class="fw-bold">Datos a completar</span>
                        </div>
                    </div>

                    <div class="card-body">
                        <div class="row g-3">

                            <div class="col-12 col-md-6">
                                <label class="form-label fw-semibold">Folio</label>
                                <div class="input-group input-group-modern">
                                    <span class="input-group-text bg-light text-muted">
                                        <i class="fas fa-hashtag"></i>
                                    </span>
                                    <input type="text" name="folio" class="form-control"
                                           value="{{ old('folio', $defaults['folio']) }}"
                                           placeholder="Ej. OAX-2026-0001">
                                </div>
                            </div>

                            <div class="col-12 col-md-6">
                                <div class="d-flex align-items-center justify-content-between">
                                    <label class="form-label fw-semibold mb-1">Piso</label>

                                    <div class="form-check form-switch m-0">
                                        <input class="form-check-input" type="checkbox" id="togglePiso">
                                        <label class="form-check-label small text-muted" for="togglePiso">
                                            Editar
                                        </label>
                                    </div>
                                </div>

                                <div class="input-group input-group-modern">
                                    <span class="input-group-text bg-light text-muted">
                                        <i class="fas fa-building"></i>
                                    </span>

                                    {{-- Input visible (inicia deshabilitado) --}}
                                    <input type="text"
                                        id="pisoInput"
                                        class="form-control"
                                        value="{{ old('piso', $defaults['piso']) }}"
                                        placeholder="Ej. PB / 1 / 2"
                                        disabled>

                                    {{-- ✅ Hidden que SI se envía siempre --}}
                                    <input type="hidden"
                                        name="piso"
                                        id="pisoHidden"
                                        value="{{ old('piso', $defaults['piso']) }}">
                                </div>

                                <small class="text-muted d-block mt-1">
                                    Activa “Editar” si necesitas modificar el piso.
                                </small>
                            </div>


                            <div class="col-12">
                                <label class="form-label fw-semibold">Centro de Trabajo</label>
                                <div class="input-group input-group-modern">
                                    <span class="input-group-text bg-light text-muted">
                                        <i class="fas fa-location-dot"></i>
                                    </span>
                                    <input type="text" name="centro_trabajo" class="form-control"
                                           value="{{ old('centro_trabajo', $defaults['centro_trabajo']) }}">
                                </div>
                            </div>

                            <div class="col-12">
                                <label class="form-label fw-semibold">Unidad / Adscripción</label>
                                <div class="input-group input-group-modern">
                                    <span class="input-group-text bg-light text-muted">
                                        <i class="fas fa-sitemap"></i>
                                    </span>
                                    <input type="text" name="unidad_adscripcion" class="form-control"
                                           placeholder="Unidad de adscripción del resguardante"
                                           required>
                                </div>
                            </div>

                            <div class="col-12">
                                <label class="form-label fw-semibold">Jefe que autoriza (Nombre)</label>
                                <div class="input-group input-group-modern">
                                    <span class="input-group-text bg-light text-muted">
                                        <i class="fas fa-user-tie"></i>
                                    </span>
                                    <input type="text" name="jefe_autoriza_nombre" class="form-control"
                                           value="{{ old('jefe_autoriza_nombre', $defaults['jefe_autoriza_nombre']) }}"
                                           placeholder="Nombre completo"
                                           required>
                                </div>
                            </div>


                        </div>

                        <div class="divider-soft my-4"></div>

                        <div class="col-12">
                            <div class="section-mini-title mb-2">
                                <i class="fas fa-desktop me-1"></i> Hostname por grupo (TAG)
                            </div>

                            <div class="row g-3">
                                @foreach($groupedByTag as $tag => $items)
                                    @php
                                        $tagKey = \Illuminate\Support\Str::slug($tag, '_'); // id seguro
                                        $defaultHostname = old('hostname.'.$tag, $hostnamesByTag[$tag] ?? '');
                                    @endphp

                                    <div class="col-12">
                                        <div class="d-flex align-items-center justify-content-between">
                                            <label class="form-label fw-semibold mb-1">
                                                Hostname ({{ $tag }})
                                            </label>

                                            <div class="form-check form-switch m-0">
                                                <input class="form-check-input hostname-toggle"
                                                    type="checkbox"
                                                    id="toggleHostname_{{ $tagKey }}"
                                                    data-target="#hostnameInput_{{ $tagKey }}">
                                                <label class="form-check-label small text-muted"
                                                    for="toggleHostname_{{ $tagKey }}">
                                                    Editar
                                                </label>
                                            </div>
                                        </div>

                                        <div class="input-group input-group-modern">
                                            <span class="input-group-text bg-light text-muted">
                                                <i class="fas fa-network-wired"></i>
                                            </span>

                                            {{-- Input visible (inicia deshabilitado) --}}
                                            <input type="text"
                                                id="hostnameInput_{{ $tagKey }}"
                                                class="form-control hostname-input"
                                                value="{{ $defaultHostname }}"
                                                placeholder="Ej. OAX-IRVIN-PC01"
                                                disabled>

                                            {{-- ✅ Hidden que SI se envía siempre (clave real por TAG) --}}
                                            <input type="hidden"
                                                name="hostname[{{ $tag }}]"
                                                id="hostnameHidden_{{ $tagKey }}"
                                                value="{{ $defaultHostname }}">
                                        </div>

                                        <small class="text-muted">
                                            Se enviará al PDF del grupo <b>{{ $tag }}</b>.
                                        </small>
                                    </div>
                                @endforeach
                            </div>
                        </div>



                        <div class="d-flex flex-wrap justify-content-end gap-2">
                            <button type="submit" class="btn btn-guinda px-4">
                                <i class="fas fa-file-pdf me-1"></i> Generar PDF
                            </button>
                        </div>

                        <div class="small text-muted mt-2">
                            <i class="fas fa-up-right-from-square me-1"></i> Se abrirá el PDF en una nueva pestaña.
                        </div>
                    </div>
                </div>
            </form>
        </div>

    </div>
</div>
@endsection

@section('css')
<style>
/* =========================
   Paleta institucional
   ========================= */
.prepare-receipt .text-guinda{ color:#611232 !important; }
.prepare-receipt .bg-guinda{ background:#611232 !important; }
.prepare-receipt .border-guinda-soft{ border-color: rgba(97,18,50,.18) !important; }

/* =========================
   Cards modernas
   ========================= */
.prepare-receipt .card-modern{
    border: 1px solid rgba(0,0,0,.06);
    background: #fff;
}
.prepare-receipt .shadow-soft{
    box-shadow: 0 10px 26px rgba(0,0,0,.08);
}
.prepare-receipt .card-header-soft{
    background: linear-gradient(180deg, rgba(97,18,50,.06), rgba(0,0,0,0));
    border-bottom: 1px solid rgba(0,0,0,.06);
    padding: .9rem 1.1rem;
}

/* Icon circle */
.prepare-receipt .icon-circle{
    width: 44px; height: 44px;
    border-radius: 999px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    flex: 0 0 auto;
}

/* =========================
   Resumen
   ========================= */
.prepare-receipt .info-pill{
    background: rgba(97,18,50,.04);
    border: 1px solid rgba(97,18,50,.10);
    border-radius: 1rem;
    padding: .85rem 1rem;
}
.prepare-receipt .section-mini-title{
    font-weight: 800;
    color: #611232;
    font-size: .85rem;
    letter-spacing: .2px;
    display: flex;
    align-items: center;
}
.prepare-receipt .summary-list{
    display: flex;
    flex-direction: column;
    gap: .6rem;
}
.prepare-receipt .summary-item{
    border: 1px solid rgba(97,18,50,.10);
    background: #fff;
    border-radius: .85rem;
    padding: .75rem .85rem;
    transition: .18s ease;
}
.prepare-receipt .summary-item:hover{
    box-shadow: 0 10px 22px rgba(97,18,50,.10);
    transform: translateY(-1px);
}
.prepare-receipt .hint-box{
    background: rgba(13,110,253,.06);
    border: 1px solid rgba(13,110,253,.12);
    color: #0d47a1;
    border-radius: .85rem;
    padding: .65rem .8rem;
    font-size: .85rem;
}

/* Badge guinda */
.prepare-receipt .badge-guinda{
    background: rgba(97,18,50,.10);
    color: #611232;
    border: 1px solid rgba(97,18,50,.15);
    font-weight: 800;
    border-radius: 999px;
    padding: .35rem .65rem;
}

/* =========================
   Inputs + focus premium
   ========================= */
.prepare-receipt .input-group-modern .input-group-text{
    border-radius: .75rem 0 0 .75rem;
    border: 1px solid rgba(0,0,0,.08);
}
.prepare-receipt .input-group-modern .form-control{
    border-radius: 0 .75rem .75rem 0;
    border: 1px solid rgba(0,0,0,.08);
}
.prepare-receipt .form-control:focus{
    border-color: rgba(97,18,50,.35);
    box-shadow: 0 0 0 .2rem rgba(97,18,50,.12);
}

/* Divider */
.prepare-receipt .divider-soft{
    height: 1px;
    background: linear-gradient(90deg, rgba(0,0,0,0), rgba(97,18,50,.22), rgba(0,0,0,0));
}

/* Botón guinda */
.prepare-receipt .btn-guinda{
    background:#611232;
    color:#fff;
    border:1px solid #611232;
    border-radius:.85rem;
    padding:.62rem 1.1rem;
    font-weight:800;
    letter-spacing:.2px;
    transition:.18s ease;
}
.prepare-receipt .btn-guinda:hover{
    background:#7b2046;
    color:#fff;
    box-shadow: 0 12px 22px rgba(97,18,50,.18);
    transform: translateY(-1px);
}

.prepare-receipt .form-switch .form-check-input{
    cursor: pointer;
}
.prepare-receipt .form-switch .form-check-input:checked{
    background-color:#611232;
    border-color:#611232;
}

</style>
@endsection

@section('js')
<script>
document.addEventListener('DOMContentLoaded', function () {

    // ===== Piso toggle =====
    const togglePiso  = document.getElementById('togglePiso');
    const pisoInput   = document.getElementById('pisoInput');
    const pisoHidden  = document.getElementById('pisoHidden');

    if (togglePiso && pisoInput && pisoHidden) {

        // Sync inicial (por si old() trae algo)
        pisoHidden.value = pisoInput.value || pisoHidden.value;

        togglePiso.addEventListener('change', function () {
            pisoInput.disabled = !this.checked;

            if (this.checked) {
                pisoInput.focus();
                pisoInput.select();
            } else {
                // Al desactivar, asegura que hidden quede con lo último que se capturó
                pisoHidden.value = pisoInput.value;
            }
        });

        pisoInput.addEventListener('input', function () {
            pisoHidden.value = this.value;
        });
    }

    // ===== Hostname toggles =====
    document.querySelectorAll('.hostname-toggle').forEach(function (toggle) {
        const selector = toggle.getAttribute('data-target');
        const input = document.querySelector(selector);
        if (!input) return;

        // Derivar hidden correspondiente: hostnameInput_x => hostnameHidden_x
        const hiddenId = input.id.replace('hostnameInput_', 'hostnameHidden_');
        const hidden = document.getElementById(hiddenId);
        if (!hidden) return;

        // Sync inicial
        hidden.value = input.value || hidden.value;

        toggle.addEventListener('change', function () {
            input.disabled = !this.checked;

            if (this.checked) {
                input.focus();
                input.select();
            } else {
                hidden.value = input.value;
            }
        });

        input.addEventListener('input', function () {
            hidden.value = this.value;
        });
    });

});
</script>
@endsection
