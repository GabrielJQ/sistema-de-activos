@extends('layouts.admin') 
@section('title','Panel de Inicio')

@section('content_header')
<div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
    <div>
        <h1 class="mb-0 fw-bold text-dark d-flex align-items-center gap-2">
            <span class="icon-circle icon-circle-primary">
                <i class="fas fa-chart-line"></i>
            </span>
            Panel Principal
        </h1>
        <p class="text-muted mt-1 mb-0">
            Resumen general de inventario y asignaciones de activos informáticos.
        </p>
    </div>

    <div>
        <span class="badge bg-white border shadow-sm text-dark fs-6 px-3 py-2 rounded-3">
            <i class="far fa-calendar-alt me-1 text-primary"></i>
            <span id="fecha-actual" class="fw-semibold"></span>
        </span>
    </div>
</div>
@endsection

@section('content')

{{-- KPIs --}}
<div class="row mb-4 g-4">

    <div class="col-xl-3 col-md-6 col-12">
        <div class="kpi-card bg-dark text-white">
            <div class="kpi-body">
                <h3 id="totalAssetsGlobal">{{ $totalAssetsGlobal ?? 0 }}</h3>
                <p>Total de Activos<br><span class="opacity-75">Inventario General</span></p>
            </div>
            <div class="kpi-icon"><i class="fas fa-database"></i></div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 col-12">
        <div class="kpi-card bg-primary text-white">
            <div class="kpi-body">
                <h3 id="totalAssets">{{ $totalAssets ?? 0 }}</h3>
                <p>Equipos de<br>Cómputo</p>
            </div>
            <div class="kpi-icon"><i class="fas fa-desktop"></i></div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 col-12">
        <div class="kpi-card bg-success text-white">
            <div class="kpi-body">
                <h3 id="assignedAssets">{{ $assignedAssets ?? 0 }}</h3>
                <p>Equipos en<br>Operación</p>
            </div>
            <div class="kpi-icon"><i class="fas fa-user-check"></i></div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 col-12">
        <div class="kpi-card bg-orange text-white">
            <div class="kpi-body">
                <h3 id="availableAssets">{{ $availableAssets ?? 0 }}</h3>
                <p>Equipos Arrendados<br>Disponibles</p>
            </div>
            <div class="kpi-icon"><i class="fas fa-box-open"></i></div>
        </div>
    </div>

</div>

{{-- FILA 1 --}}
<div class="row g-4">

    <div class="col-lg-6">
        <div class="card dashboard-card h-100">
            <div class="card-header">
                <i class="fas fa-desktop me-2 text-primary"></i>Tipos de Equipo
            </div>
            <div class="card-body">
                <div class="chart-container">
                    <canvas id="chartAssetsByType"></canvas>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-6">
        <div class="card dashboard-card h-100">
            <div class="card-header">
                <i class="fas fa-truck me-2 text-primary"></i>Equipos por Proveedor
            </div>
            <div class="card-body">
                <div class="chart-container">
                    <canvas id="chartTopSuppliers"></canvas>
                </div>
            </div>
        </div>
    </div>

</div>

{{-- FILA 2 --}}
<div class="row g-4 mt-1">

    <div class="col-lg-4">
        <div class="card dashboard-card h-100">
            <div class="card-header">
                <i class="fas fa-info-circle me-2 text-primary"></i>Estado del Inventario
            </div>
            <div class="card-body">
                <div class="chart-container chart-md">
                    <canvas id="chartAssetsByStatus"></canvas>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card dashboard-card h-100">
            <div class="card-header">
                @if(hasRole(['super_admin']))
                    <i class="fas fa-map-marked-alt me-2 text-primary"></i>Activos por Región
                @else
                    <i class="fas fa-building me-2 text-primary"></i>Top Departamentos
                @endif
            </div>
            <div class="card-body">
                @if(hasRole(['super_admin']))
                    <div class="chart-container">
                        <canvas id="chartAssetsByRegion"></canvas>
                    </div>
                @else
                    <ul class="list-group list-group-flush" id="topDepartmentsList"></ul>
                @endif
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card dashboard-card h-100">
            <div class="card-header">
                <i class="fas fa-users me-2 text-primary"></i>Top Empleados
            </div>
            <div class="card-body">
                <div class="chart-container">
                    <canvas id="chartTopEmployees"></canvas>
                </div>
            </div>
        </div>
    </div>

</div>

@endsection

@section('css')

@if(session()->has('smiab_access_token'))
    <meta name="smiab-token" content="{{ session('smiab_access_token') }}">
@endif

<style>
/* =========================================================
   PALETA + BASE (institucional guinda)
========================================================= */
:root{
    --guinda:#611232;
    --guinda-dark:#4b0f27;

    --ink:#111827;
    --muted:#6b7280;

    --card:#ffffff;
    --soft:#faf7f9;
    --line:#e5e7eb;

    --shadow: 0 14px 30px rgba(17,24,39,.08);
    --shadow-sm: 0 8px 18px rgba(17,24,39,.10);
}

.text-guinda{ color: var(--guinda) !important; }
.bg-guinda{ background: var(--guinda) !important; }

body{
    background: #f4f6f9; 
}

/* =========================================================
   HEADER
========================================================= */
.icon-circle{
    width:44px;height:44px;border-radius:999px;
    display:flex;align-items:center;justify-content:center;
}
.icon-circle-primary{
    background: rgba(97,18,50,.10);
    color: var(--guinda);
    border: 1px solid rgba(97,18,50,.14);
    box-shadow: var(--shadow-sm);
}

.badge.bg-white.border.shadow-sm{
    border: 1px solid rgba(229,231,235,.9) !important;
    border-radius: 999px !important;
    box-shadow: var(--shadow-sm) !important;
}

/* =========================================================
   KPI
========================================================= */
.kpi-card{
    position:relative;
    border-radius: 1.25rem;
    padding: 1.25rem 1.25rem;
    overflow:hidden;
    box-shadow: var(--shadow);
    transition: transform .18s ease, box-shadow .18s ease;
    border: 1px solid rgba(255,255,255,.10);
}

.kpi-card:hover{
    transform: translateY(-3px);
    box-shadow: 0 18px 40px rgba(17,24,39,.12);
}

.kpi-body h3{
    font-weight: 900;
    letter-spacing: .2px;
    margin-bottom: .25rem;
    font-size: 1.85rem;
}

.kpi-body p{
    margin:0;
    font-size: .92rem;
    line-height: 1.2;
    opacity: .95;
}

.kpi-icon{
    position:absolute;
    right: .9rem;
    top: .9rem;
    font-size: 3.1rem;
    opacity: .18;
    filter: drop-shadow(0 10px 14px rgba(0,0,0,.15));
}

/* KPI naranja institucional */
.bg-orange{
    background: linear-gradient(135deg, #fd7e14, #ff9a3d) !important;
}

/* Ajuste sutil para “dark” */
.kpi-card.bg-dark{
    background: linear-gradient(135deg, #111827, #0b1220) !important;
}

/* =========================================================
   DASHBOARD CARDS
========================================================= */
.dashboard-card{
    border: 0;
    border-radius: 1.25rem;
    background: var(--card);
    box-shadow: var(--shadow);
    overflow: hidden; 
}

.dashboard-card .card-header{
    background: #fff;
    font-weight: 800;
    border-bottom: 1px solid rgba(229,231,235,.9);
    padding: .95rem 1.1rem;
    display:flex;
    align-items:center;
    gap:.35rem;
}

/* Header “título” más pro */
.dashboard-card .card-header i{
    opacity: .95;
}

/* =========================================================
   CHARTS (clave: que NO se corten)
========================================================= */
.card-body{
    padding: 1rem 1.1rem; 
}

.chart-container{
    position: relative;
    height: 320px;         
    padding: .25rem .25rem .35rem;
}

.chart-container.chart-md{
    height: 340px;       
}

/* En pantallas grandes, un poquito más alto */
@media (min-width: 1400px){
    .chart-container{ height: 350px; }
    .chart-container.chart-md{ height: 370px; }
}

/* =========================================================
   LISTA TOP DEPARTAMENTOS
========================================================= */
.list-group-item{
    border: 0;
    border-bottom: 1px dashed rgba(17,24,39,.12);
    padding: .85rem 0;
}

.list-group-item:last-child{
    border-bottom: 0;
}

.list-group-item .badge.bg-primary{
    border-radius: 999px;
    padding: .45rem .65rem;
    font-weight: 700;
}

/* badge light del tipo más común */
.badge.bg-light.text-dark.border{
    border: 1px solid rgba(229,231,235,.9) !important;
    border-radius: 999px !important;
    padding: .4rem .6rem;
}

/* =========================================================
   DETALLES PEQUEÑOS
========================================================= */
.text-primary{ color: var(--guinda) !important; } 
</style>
@endsection


@section('js')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener("DOMContentLoaded", function () {
    const fecha = new Date();
    document.getElementById("fecha-actual").textContent =
        fecha.toLocaleDateString('es-MX',{
            weekday:'long', year:'numeric', month:'long', day:'numeric'
        });

    const assetTypes = @json($assetTypes);

    let chartAssetsByType = null;
    let chartTopSuppliers = null;
    let chartTopEmployees = null;
    let chartAssetsByStatus = null;
    let chartAssetsByRegion = null;


    function fetchDashboard() {
        fetch("{{ route('dashboard') }}", {
            headers: { 'X-Requested-With':'XMLHttpRequest' }
        })
        .then(res => res.json())
        .then(data => {
            
            document.getElementById('totalAssetsGlobal').textContent =data.totalAssetsGlobal;
            document.getElementById('totalAssets').textContent = data.totalAssets;
            document.getElementById('assignedAssets').textContent = data.assignedAssets;
            document.getElementById('availableAssets').textContent = data.availableAssets;

            // Departamentos
            const list = document.getElementById('topDepartmentsList');

            if (list && Array.isArray(data.topDepartments)) {
                list.innerHTML = '';

                data.topDepartments.forEach(d => {
                    const li = document.createElement('li');
                    li.className = 'list-group-item';
                    li.innerHTML = `
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="fw-semibold">${d.name}</span>
                        <span class="badge bg-primary">${d.count}</span>
                    </div>
                    <div class="d-flex align-items-center gap-2 mt-1">
                        <span class="badge bg-light text-dark border">
                            <i class="fas fa-desktop me-1"></i>${d.mostCommonType}
                        </span>
                        <small class="text-muted">Equipo más común</small>
                    </div>
                    `;

                    list.appendChild(li);
                });
            }


            // Tipos
            if(chartAssetsByType) chartAssetsByType.destroy();
            chartAssetsByType = new Chart(chartAssetsByType = document.getElementById('chartAssetsByType'), {
                type: 'bar',
                data: {
                    labels: assetTypes,
                    datasets: [{
                        data: assetTypes.map(t => data.assetsByType?.[t] || 0),
                        backgroundColor: ['#1f77b4','#ff7f0e','#2ca02c','#d62728','#9467bd']
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { display:false } },
                    scales: { y: { beginAtZero:true, ticks:{stepSize:1} } }
                }
            });

            // Proveedores
            if (chartTopSuppliers) chartTopSuppliers.destroy();

            // Convertimos correctamente la estructura
            const supplierLabels = (data.topSuppliers || []).map(s => s.name);
            const supplierValues = (data.topSuppliers || []).map(s => s.count);

            // Paleta institucional
            const supplierColors = [
                '#17a2b8', // cyan
                '#20c997', // verde aqua
                '#0d6efd', // azul
                '#6610f2', // morado
                '#198754'  // verde
            ];

            chartTopSuppliers = new Chart(document.getElementById('chartTopSuppliers'), {
                type: 'bar',
                data: {
                    labels: supplierLabels,
                    datasets: [{
                        data: supplierValues,
                        backgroundColor: supplierLabels.map(
                            (_, i) => supplierColors[i % supplierColors.length]
                        )
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            callbacks: {
                                label: ctx => ` Equipos: ${ctx.raw}`
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: { stepSize: 1 }
                        }
                    }
                }
            });

            // Empleados
            if(chartTopEmployees) chartTopEmployees.destroy();

            const employeeLabels = Object.keys(data.topEmployees || {});
            const employeeValues = Object.values(data.topEmployees || {});

            // Paleta coherente con tu dashboard
            const employeeColors = [
                '#007bff', // azul
                '#28a745', // verde
                '#6f42c1', // morado
                '#fd7e14'  // naranja 
            ];

            chartTopEmployees = new Chart(document.getElementById('chartTopEmployees'), {
                type: 'bar',
                data: {
                    labels: employeeLabels,
                    datasets: [{
                        data: employeeValues,
                        backgroundColor: employeeLabels.map(
                            (_, i) => employeeColors[i % employeeColors.length]
                        )
                    }]
                },
                options: {
                    indexAxis: 'y',
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            callbacks: {
                                label: ctx => ` Equipos asignados: ${ctx.raw}`
                            }
                        }
                    },
                    scales: {
                        x: {
                            beginAtZero: true,
                            ticks: { stepSize: 1 }
                        }
                    }
                }
            });

            @if(hasRole(['super_admin']))
            // Activos por región
            if (chartAssetsByRegion) chartAssetsByRegion.destroy();

            const regionLabels = Object.keys(data.assetsByRegion || {});
            const regionValues = Object.values(data.assetsByRegion || {});

            const regionColors = [
                '#0d6efd',
                '#198754',
                '#6610f2',
                '#fd7e14',
                '#20c997'
            ];

            chartAssetsByRegion = new Chart(
                document.getElementById('chartAssetsByRegion'),
                {
                    type: 'bar',
                    data: {
                        labels: regionLabels,
                        datasets: [{
                            data: regionValues,
                            backgroundColor: regionLabels.map(
                                (_, i) => regionColors[i % regionColors.length]
                            )
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: { display: false },
                            tooltip: {
                                callbacks: {
                                    label: ctx => ` Equipos: ${ctx.raw}`
                                }
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                ticks: { stepSize: 1 }
                            }
                        }
                    }
                }
            );
            @endif


            // Estado del inventario
            if (chartAssetsByStatus) chartAssetsByStatus.destroy();

            const statusLabels = Object.keys(data.assetsByStatus || {});
            const statusValues = Object.values(data.assetsByStatus || {});

            const statusColors = {
                'OPERACION': '#28a745',    // verde
                'RESGUARDADO': '#fd7e14',  // naranja
                'DANADO': '#dc3545',       // rojo
                'BAJA': '#6c757d'          // gris
            };

            chartAssetsByStatus = new Chart(
                document.getElementById('chartAssetsByStatus'),
                {
                    type: 'doughnut',
                    data: {
                        labels: statusLabels,
                        datasets: [{
                            data: statusValues,
                            backgroundColor: statusLabels.map(
                                s => statusColors[s] || '#0d6efd'
                            )
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                position: 'bottom',
                                labels: {
                                    boxWidth: 14
                                }
                            },
                            tooltip: {
                                callbacks: {
                                    label: ctx => ` ${ctx.label}: ${ctx.raw} equipos`
                                }
                            }
                        }
                    }
                }
            );


        });
    }

    fetchDashboard();
});
</script>
@stop
