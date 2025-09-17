@extends('layouts.app')

@section('title', 'Reportes - Super Admin')

@section('content')
<div class="container-fluid py-4">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h3 mb-0 text-gray-800">
                        <i class="fas fa-chart-bar text-primary me-2"></i>
                        Reportes del Sistema
                    </h1>
                    <p class="text-muted mb-0">Análisis y estadísticas del sistema</p>
                </div>
                <div class="d-flex gap-2">
                    <button type="button" class="btn btn-success" onclick="exportReport()">
                        <i class="fas fa-download me-1"></i>Exportar
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Filtros de Reportes -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-body">
                    <form method="GET" class="row g-3">
                        <div class="col-md-3">
                            <label class="form-label">Tipo de Reporte</label>
                            <select class="form-select" name="type" onchange="updateReportFilters()">
                                <option value="general" {{ request('type') === 'general' ? 'selected' : '' }}>General</option>
                                <option value="users" {{ request('type') === 'users' ? 'selected' : '' }}>Usuarios</option>
                                <option value="memberships" {{ request('type') === 'memberships' ? 'selected' : '' }}>Membresías</option>
                                <option value="properties" {{ request('type') === 'properties' ? 'selected' : '' }}>Propiedades</option>
                                <option value="reservations" {{ request('type') === 'reservations' ? 'selected' : '' }}>Reservas</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Período</label>
                            <select class="form-select" name="period">
                                <option value="7" {{ request('period') === '7' ? 'selected' : '' }}>Últimos 7 días</option>
                                <option value="30" {{ request('period') === '30' ? 'selected' : '' }}>Últimos 30 días</option>
                                <option value="90" {{ request('period') === '90' ? 'selected' : '' }}>Últimos 90 días</option>
                                <option value="365" {{ request('period') === '365' ? 'selected' : '' }}>Último año</option>
                                <option value="custom" {{ request('period') === 'custom' ? 'selected' : '' }}>Personalizado</option>
                            </select>
                        </div>
                        <div class="col-md-2" id="custom-dates" style="display: none;">
                            <label class="form-label">Fecha Inicio</label>
                            <input type="date" class="form-control" name="start_date" value="{{ request('start_date') }}">
                        </div>
                        <div class="col-md-2" id="custom-dates-end" style="display: none;">
                            <label class="form-label">Fecha Fin</label>
                            <input type="date" class="form-control" name="end_date" value="{{ request('end_date') }}">
                        </div>
                        <div class="col-md-3 d-flex align-items-end">
                            <button type="submit" class="btn btn-primary me-2">
                                <i class="fas fa-search me-1"></i>Generar Reporte
                            </button>
                            <a href="{{ route('superadmin.reports') }}" class="btn btn-outline-secondary">
                                <i class="fas fa-times me-1"></i>Limpiar
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Resumen de Estadísticas -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Total Usuarios
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ number_format($stats['total_users']) }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-users fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Membresías Activas
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ number_format($stats['active_memberships']) }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-gem fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Propiedades
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ number_format($stats['total_properties']) }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-home fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Reservas
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ number_format($stats['total_reservations']) }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-calendar-check fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Gráficos y Tablas -->
    <div class="row">
        <!-- Gráfico de Usuarios por Mes -->
        <div class="col-xl-6 col-lg-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-chart-line me-2"></i>Usuarios Registrados por Mes
                    </h6>
                </div>
                <div class="card-body">
                    <canvas id="usersChart" width="400" height="200"></canvas>
                </div>
            </div>
        </div>

        <!-- Gráfico de Membresías por Plan -->
        <div class="col-xl-6 col-lg-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-chart-pie me-2"></i>Distribución de Membresías
                    </h6>
                </div>
                <div class="card-body">
                    <canvas id="membershipsChart" width="400" height="200"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabla de Actividad Reciente -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-history me-2"></i>Actividad Reciente
                    </h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered" width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                    <th>Fecha</th>
                                    <th>Usuario</th>
                                    <th>Acción</th>
                                    <th>Descripción</th>
                                    <th>IP</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($recentActivity as $activity)
                                    <tr>
                                        <td>{{ $activity->created_at->format('d/m/Y H:i') }}</td>
                                        <td>{{ $activity->user->name ?? 'Sistema' }}</td>
                                        <td>
                                            <span class="badge bg-{{ $activity->action === 'created' ? 'success' : ($activity->action === 'updated' ? 'warning' : 'danger') }}">
                                                {{ ucfirst($activity->action) }}
                                            </span>
                                        </td>
                                        <td>{{ $activity->description ?? 'Sin descripción' }}</td>
                                        <td>{{ $activity->ip_address ?? 'N/A' }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center py-4">
                                            <i class="fas fa-history fa-3x text-muted mb-3"></i>
                                            <p class="text-muted">No hay actividad reciente</p>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
    .border-left-primary {
        border-left: 0.25rem solid #4e73df !important;
    }
    .border-left-success {
        border-left: 0.25rem solid #1cc88a !important;
    }
    .border-left-info {
        border-left: 0.25rem solid #36b9cc !important;
    }
    .border-left-warning {
        border-left: 0.25rem solid #f6c23e !important;
    }
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Gráfico de usuarios por mes
const usersCtx = document.getElementById('usersChart').getContext('2d');
const usersChart = new Chart(usersCtx, {
    type: 'line',
    data: {
        labels: {!! json_encode($chartData['users']['labels']) !!},
        datasets: [{
            label: 'Usuarios Registrados',
            data: {!! json_encode($chartData['users']['data']) !!},
            borderColor: 'rgb(75, 192, 192)',
            backgroundColor: 'rgba(75, 192, 192, 0.2)',
            tension: 0.1
        }]
    },
    options: {
        responsive: true,
        scales: {
            y: {
                beginAtZero: true
            }
        }
    }
});

// Gráfico de membresías por plan
const membershipsCtx = document.getElementById('membershipsChart').getContext('2d');
const membershipsChart = new Chart(membershipsCtx, {
    type: 'doughnut',
    data: {
        labels: {!! json_encode($chartData['memberships']['labels']) !!},
        datasets: [{
            data: {!! json_encode($chartData['memberships']['data']) !!},
            backgroundColor: [
                '#FF6384',
                '#36A2EB',
                '#FFCE56',
                '#4BC0C0',
                '#9966FF',
                '#FF9F40'
            ]
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: {
                position: 'bottom'
            }
        }
    }
});

function updateReportFilters() {
    const periodSelect = document.querySelector('select[name="period"]');
    const customDates = document.getElementById('custom-dates');
    const customDatesEnd = document.getElementById('custom-dates-end');
    
    if (periodSelect.value === 'custom') {
        customDates.style.display = 'block';
        customDatesEnd.style.display = 'block';
    } else {
        customDates.style.display = 'none';
        customDatesEnd.style.display = 'none';
    }
}

function exportReport() {
    const type = document.querySelector('select[name="type"]').value;
    const period = document.querySelector('select[name="period"]').value;
    const startDate = document.querySelector('input[name="start_date"]').value;
    const endDate = document.querySelector('input[name="end_date"]').value;
    
    let url = '{{ route("superadmin.reports.export") }}?type=' + type + '&period=' + period;
    
    if (period === 'custom' && startDate && endDate) {
        url += '&start_date=' + startDate + '&end_date=' + endDate;
    }
    
    window.open(url, '_blank');
}

// Inicializar filtros
document.addEventListener('DOMContentLoaded', function() {
    updateReportFilters();
});
</script>
@endpush
@endsection
