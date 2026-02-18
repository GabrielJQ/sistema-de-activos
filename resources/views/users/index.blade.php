@extends('layouts.admin')

@section('title', 'Usuarios')

@section('content_header')
<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-2 mb-3">
    <h1 class="fw-bold mb-0 text-guinda d-flex align-items-center gap-2">
        <span class="icon-circle icon-circle-guinda">
            <i class="fas fa-users"></i>
        </span>
        Usuarios del Sistema
    </h1>
</div>
@stop

@section('content')
<div class="container-fluid">

    {{-- Mensajes --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show shadow-sm alert-soft-success d-flex align-items-start gap-2" role="alert">
            <i class="fas fa-check-circle mt-1"></i>
            <div class="flex-grow-1">
                <div class="fw-semibold">Acción completada</div>
                <div class="small opacity-90">{{ session('success') }}</div>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show shadow-sm alert-soft-danger d-flex align-items-start gap-2" role="alert">
            <i class="fas fa-exclamation-triangle mt-1"></i>
            <div class="flex-grow-1">
                <div class="fw-semibold">Ocurrió un problema</div>
                <div class="small opacity-90">{{ session('error') }}</div>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
        </div>
    @endif

    {{-- Botón Nuevo Usuario (sección) --}}
    <div class="d-flex justify-content-start mb-3 flex-wrap btn_actions">
        {{-- Botón Nuevo Usuario (header) --}}
        @if(auth()->user()->isSuperAdmin() || auth()->user()->isAdmin())
            <a href="{{ route('users.create') }}"
            class="btn btn-guinda shadow-sm px-4 py-2 d-inline-flex align-items-center gap-2">
                <i class="fas fa-user-plus"></i> Nuevo Usuario
            </a>
        @endif
    </div>

    {{-- Cards de usuarios agrupados por rol --}}
    @php
        $roles = [
            'super_admin' => ['label' => 'Super Administrador', 'icon' => '👑'],
            'admin' => ['label' => 'Administradores', 'icon' => '🛡️'],
            'collaborator' => ['label' => 'Colaboradores', 'icon' => '👷'],
            'visitor' => ['label' => 'Visitantes', 'icon' => '👤'],
        ];
    @endphp

    @foreach($roles as $roleKey => $roleData)

        {{-- Ocultar sección "Super Administrador" según permisos --}}
        @if($roleKey === 'super_admin' && !auth()->user()->isSuperAdmin())
            @continue
        @endif

        {{-- Header de sección --}}
        <div class="role-header mt-4">
            <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
                <div class="d-flex align-items-center gap-2">
                    <span class="role-pill">
                        <span class="me-1">{!! $roleData['icon'] !!}</span>
                        {{ $roleData['label'] }}
                    </span>
                    <span class="text-muted small">
                        {{ $users->where('role', $roleKey)->count() }} usuario(s)
                    </span>
                </div>
                <div class="role-divider flex-grow-1 d-none d-md-block"></div>
            </div>
        </div>

        <div class="row g-4 mt-2">
            @forelse($users->where('role', $roleKey) as $user)
                <div class="col-12 col-sm-6 col-lg-4 col-xl-3">
                    <div class="card shadow-sm border-0 rounded-4 user-card h-100" id="user-{{ $user->id }}">
                        <div class="card-body p-4 d-flex flex-column">

                            {{-- Top --}}
                            <div class="user-top">
                                <div class="user-main">
                                    <div class="user-name-line">
                                        <span class="user-role-icon">{!! $roleData['icon'] !!}</span>
                                        <h5 class="card-title fw-bold mb-0 user-name">{{ $user->name }}</h5>
                                    </div>
                                    {{-- Badge estado --}}
                                    <span class="badge px-1 py-2 status badge-status
                                        @if($user->isOnline()) bg-success @else bg-secondary @endif">
                                        <i class="fas @if($user->isOnline()) fa-circle @else fa-circle-notch @endif me-1"></i>
                                        <span class="status-text">
                                            @if($user->isOnline()) En línea&nbsp;&nbsp;@else Desconectado&nbsp; @endif 
                                        </span>
                                    </span>
                                    <hr class="my-2 soft-hr">
                                    <div class="user-email-line text-muted small">
                                        <i class="fas fa-envelope me-1 text-guinda"></i>
                                        <span class="user-email-text fw-semibold">{{ $user->email }}</span>
                                    </div>
                                </div>
                            </div>

                            {{-- Separador --}}
                            <hr class="my-2 soft-hr">

                            {{-- Info --}}
                            <div class="user-meta d-grid gap-2">
                                <div class="d-flex align-items-start gap-2">
                                    <span class="meta-icon">
                                        <i class="fas fa-map-marker-alt"></i>
                                    </span>
                                    <div class="small">
                                        <div class="text-muted">Región</div>
                                        <div class="fw-semibold">{{ $user->region?->regnom ?? '—' }}</div>
                                    </div>
                                </div>

                                <div class="d-flex align-items-start gap-2">
                                    <span class="meta-icon">
                                        <i class="fas fa-building"></i>
                                    </span>
                                    <div class="small">
                                        <div class="text-muted">Unidad</div>
                                        <div class="fw-semibold">{{ $user->unit?->uninom ?? '—' }}</div>
                                    </div>
                                </div>
                            </div>

                            {{-- Footer acciones --}}
                            <div class="mt-auto pt-3">
                                <div class="d-flex justify-content-end gap-2">

                                    {{-- Botón Editar --}}
                                    @if(
                                        (auth()->user()->isSuperAdmin() && $user->id !== auth()->user()->id) ||
                                        (auth()->user()->isAdmin() && !in_array($user->role, ['admin','super_admin']))
                                    )
                                        <a href="{{ route('users.edit', $user) }}"
                                           class="btn btn-sm btn-warning px-3 shadow-sm btn-action"
                                           data-bs-toggle="tooltip"
                                           title="Editar">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                    @endif

                                    {{-- Botón Eliminar --}}
                                    @if(
                                        (auth()->user()->isSuperAdmin() && $user->id !== auth()->user()->id)
                                        ||
                                        (auth()->user()->isAdmin() && !in_array($user->role, ['admin','super_admin']))
                                    )
                                        <button type="button"
                                            class="btn btn-sm btn-danger px-3 shadow-sm btn-action"
                                            data-bs-toggle="tooltip"
                                            title="Eliminar"

                                            data-confirm-delete
                                            data-name="{{ $user->name }}"
                                            data-text="¿Deseas eliminar al usuario?"
                                            data-action="{{ route('users.destroy', $user) }}"
                                        >
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                    @endif

                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            @empty
                {{-- Estado vacío por rol --}}
                <div class="col-12">
                    <div class="card border-0 shadow-sm rounded-4">
                        <div class="card-body p-4 d-flex align-items-center gap-3">
                            <div class="empty-icon">
                                <i class="fas fa-user-slash"></i>
                            </div>
                            <div>
                                <div class="fw-semibold">Sin usuarios en esta sección</div>
                                <div class="text-muted small">Aún no hay usuarios registrados con el rol: <strong>{{ $roleData['label'] }}</strong>.</div>
                            </div>
                        </div>
                    </div>
                </div>
            @endforelse
        </div>

    @endforeach

</div>
@stop

@section('css')
<style>
/* =========
   PALETA / BASE
========= */
:root{
    --guinda:#611232;
    --guinda-2:#4d0f27;
    --soft:#f6f7f9;
    --muted:#6c757d;
    --card-radius: 1rem;
}

.text-guinda{ color: var(--guinda) !important; }

/* Icono circular en header */
.icon-circle{
    width: 38px;
    height: 38px;
    border-radius: 999px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
}
.icon-circle-guinda{
    background: rgba(97,18,50,.10);
    color: var(--guinda);
}

/* =========
   ALERTAS "SOFT"
========= */
.alert{
    border-radius: 0.9rem;
    border: 0;
}
.alert-soft-success{
    background: rgba(25,135,84,.10);
    color: #155724;
}
.alert-soft-danger{
    background: rgba(220,53,69,.10);
    color: #842029;
}

/* =========
   BOTONES GUINDA
========= */
.btn-guinda{
    background-color: var(--guinda);
    color:#fff;
    border-radius: .75rem;
    border:1px solid var(--guinda);
    transition:.2s ease;
}
.btn-guinda:hover{
    background-color: var(--guinda-2);
    border-color: var(--guinda-2);
    color:#fff;
    transform: translateY(-1px);
}
.btn-guinda-outline{
    background: #fff;
    color: var(--guinda);
    border-radius: .75rem;
    border:1px solid rgba(97,18,50,.35);
    transition:.2s ease;
}
.btn-guinda-outline:hover{
    background: rgba(97,18,50,.06);
    border-color: rgba(97,18,50,.55);
    color: var(--guinda);
    transform: translateY(-1px);
}

/* =========
   HEADER POR ROL
========= */
.role-header{
    position: relative;
}
.role-pill{
    display: inline-flex;
    align-items: center;
    gap: .45rem;
    padding: .45rem .75rem;
    border-radius: 999px;
    background: rgba(97,18,50,.08);
    color: var(--guinda);
    font-weight: 700;
}
.role-divider{
    height: 1px;
    background: linear-gradient(to right, rgba(97,18,50,.25), rgba(97,18,50,0));
    margin-left: .75rem;
}

/* =========
   TARJETAS USUARIO
========= */
.user-card{
    border-radius: var(--card-radius) !important;
    background: #fff;
    transition: transform .18s ease, box-shadow .18s ease;
    overflow: hidden;
    
}
.user-card:hover{
    transform: translateY(-4px);
    box-shadow: 0 10px 24px rgba(0,0,0,.10);
}
.soft-hr{
    border-top: 1px solid rgba(0,0,0,.06);
}
.meta-icon{
    width: 34px;
    height: 34px;
    border-radius: 10px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    background: rgba(0,0,0,.04);
    color: #495057;
    flex: 0 0 auto;
}
.user-meta .fw-semibold{
    line-height: 1.2;
}

/* Badge estado más pro */
.badge-status{
    border-radius: 999px;
    font-size: .85rem;
    font-weight: 700;
    letter-spacing: .2px;
    white-space: nowrap;
}

/* Botones de acción compactos y consistentes */
.btn-action{
    border-radius: .75rem;
}

/* Empty state */
.empty-icon{
    width: 44px;
    height: 44px;
    border-radius: 14px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    background: rgba(97,18,50,.10);
    color: var(--guinda);
    font-size: 1.15rem;
}

/*------------------------------- */
/* =========================
   TOP: cada dato en su línea (desktop)
   ========================= */
.user-top{
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    gap: .75rem;
}

.user-main{
    flex: 1 1 auto;
    min-width: 0; /* importante para que flex no reviente, pero SIN truncar */
}

/* Línea 1: icon + nombre */
.user-name-line{
    display: flex;
    align-items: center;
    gap: .5rem;
}

.user-role-icon{
    font-size: 1.35rem;
    flex: 0 0 auto;
}

.user-name{
    line-height: 1.2;
    margin: 0;
    /* NO truncar en escritorio */
    white-space: normal;
    word-break: break-word;
}

/* Línea 2: correo (wrap y nunca se “come”) */
.user-email-line{
    margin-top: .25rem;
    line-height: 1.2;
    /* permite saltos en emails largos */
    overflow-wrap: anywhere;
    word-break: break-word;
}

/* Badge estado: fijo y no aplasta el contenido */
.badge-status{
    flex: 0 0 auto;
    display: inline-flex;
    align-items: center;
    gap: .35rem;
    white-space: nowrap;
    border-radius: 999px;
    font-size: .85rem;
    font-weight: 700;
}

/* =========================
   Responsive: cuando se aprieta, el badge baja abajo
   ========================= */
@media (max-width: 992px){
    .user-top{
        flex-wrap: wrap;
    }
    .badge-status{
        width: 55%;
        justify-content: center;
        margin-top: .5rem;
    }
}

/* Móvil: más compacto */
@media (max-width: 576px){
    .user-card .card-body{
        padding: 1.1rem !important;
    }
    .badge-status{
        font-size: .78rem;
        padding: .45rem .75rem !important;
    }
}


</style>
@stop

@push('js')
<script>
$(document).ready(function() {

    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(el => new bootstrap.Tooltip(el));

    // Refresco de estado online
    function refreshUserStatus() {
        $.ajax({
            url: "{{ route('users.status') }}",
            type: 'GET',
            success: function(data) {
                data.forEach(function(user) {
                    var cell = $('#user-' + user.id + ' .status');
                    if(cell.length) {
                        if(user.is_online) {
                            cell.removeClass('bg-secondary').addClass('bg-success');
                            cell.find('.status-text').text('En línea');
                            cell.find('i.fas').removeClass('fa-circle-notch').addClass('fa-circle');
                        } else {
                            cell.removeClass('bg-success').addClass('bg-secondary');
                            cell.find('.status-text').text('Desconectado');
                            cell.find('i.fas').removeClass('fa-circle').addClass('fa-circle-notch');
                        }
                    }
                });
            }
        });
    }

    setInterval(refreshUserStatus, 30000);
});
</script>
@endpush
