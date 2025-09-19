@extends('layouts.app')

@section('title', 'Logs de Auditoría - Super Admin')

@section('content')
<div class="container-fluid py-4">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h3 mb-0 text-gray-800">
                        <i class="fas fa-history text-primary me-2"></i>
                        Logs de Auditoría
                    </h1>
                    <p class="text-muted mb-0">Registro de todas las actividades del sistema</p>
                </div>
                <div class="d-flex gap-2">
                    <button type="button" class="btn btn-success" onclick="exportLogs()">
                        <i class="fas fa-download me-1"></i>Exportar
                    </button>
                    <button type="button" class="btn btn-warning" onclick="cleanupLogs()">
                        <i class="fas fa-trash me-1"></i>Limpiar Logs Antiguos
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Filtros -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-body">
                    <form method="GET" class="row g-3">
                        <div class="col-md-3">
                            <label class="form-label">Buscar</label>
                            <input type="text" class="form-control" name="search" 
                                   value="{{ request('search') }}" placeholder="Usuario, acción o descripción...">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Acción</label>
                            <select class="form-select" name="action">
                                <option value="">Todas</option>
                                <option value="created" {{ request('action') === 'created' ? 'selected' : '' }}>Creado</option>
                                <option value="updated" {{ request('action') === 'updated' ? 'selected' : '' }}>Actualizado</option>
                                <option value="deleted" {{ request('action') === 'deleted' ? 'selected' : '' }}>Eliminado</option>
                                <option value="login" {{ request('action') === 'login' ? 'selected' : '' }}>Login</option>
                                <option value="logout" {{ request('action') === 'logout' ? 'selected' : '' }}>Logout</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Modelo</label>
                            <select class="form-select" name="model">
                                <option value="">Todos</option>
                                <option value="User" {{ request('model') === 'User' ? 'selected' : '' }}>Usuario</option>
                                <option value="Property" {{ request('model') === 'Property' ? 'selected' : '' }}>Propiedad</option>
                                <option value="Reservation" {{ request('model') === 'Reservation' ? 'selected' : '' }}>Reserva</option>
                                <option value="Membership" {{ request('model') === 'Membership' ? 'selected' : '' }}>Membresía</option>
                                <option value="Role" {{ request('model') === 'Role' ? 'selected' : '' }}>Rol</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Usuario</label>
                            <select class="form-select" name="user">
                                <option value="">Todos</option>
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}" {{ request('user') == $user->id ? 'selected' : '' }}>
                                        {{ $user->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Fecha</label>
                            <input type="date" class="form-control" name="date" value="{{ request('date') }}">
                        </div>
                        <div class="col-md-1 d-flex align-items-end">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-search"></i>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Estadísticas Rápidas -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Total Logs
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ number_format($stats['total_logs']) }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-list fa-2x text-gray-300"></i>
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
                                Hoy
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ number_format($stats['today_logs']) }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-calendar-day fa-2x text-gray-300"></i>
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
                                Esta Semana
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ number_format($stats['week_logs']) }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-calendar-week fa-2x text-gray-300"></i>
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
                                Usuarios Activos
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ number_format($stats['active_users']) }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-users fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabla de Logs -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        Registro de Actividades ({{ $logs->total() }} total)
                    </h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover" width="100%" cellspacing="0">
                            <thead class="table-light">
                                <tr>
                                    <th>Fecha/Hora</th>
                                    <th>Usuario</th>
                                    <th>Acción</th>
                                    <th>Modelo</th>
                                    <th>Descripción</th>
                                    <th>IP</th>
                                    <th>Detalles</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($logs as $log)
                                    <tr>
                                        <td>
                                            <div>
                                                <div class="fw-bold">{{ $log->created_at->format('d/m/Y') }}</div>
                                                <small class="text-muted">{{ $log->created_at->format('H:i:s') }}</small>
                                            </div>
                                        </td>
                                        <td>
                                            @if($log->user)
                                                <div class="d-flex align-items-center">
                                                    <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center me-2" 
                                                         style="width: 30px; height: 30px; font-size: 12px;">
                                                        {{ substr($log->user->name, 0, 1) }}
                                                    </div>
                                                    <div>
                                                        <div class="fw-bold">{{ $log->user->name }}</div>
                                                        <small class="text-muted">{{ $log->user->email }}</small>
                                                    </div>
                                                </div>
                                            @else
                                                <span class="text-muted">Sistema</span>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="badge bg-{{ $log->action === 'created' ? 'success' : ($log->action === 'updated' ? 'warning' : ($log->action === 'deleted' ? 'danger' : 'info')) }}">
                                                <i class="fas fa-{{ $log->action === 'created' ? 'plus' : ($log->action === 'updated' ? 'edit' : ($log->action === 'deleted' ? 'trash' : 'info-circle')) }} me-1"></i>
                                                {{ ucfirst($log->action) }}
                                            </span>
                                        </td>
                                        <td>
                                            @if($log->model_type)
                                                <span class="badge bg-light text-dark">{{ $log->model_type }}</span>
                                                @if($log->model_id)
                                                    <br><small class="text-muted">ID: {{ $log->model_id }}</small>
                                                @endif
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="text-muted">{{ $log->description ?? 'Sin descripción' }}</span>
                                        </td>
                                        <td>
                                            <span class="text-muted">{{ $log->ip_address ?? 'N/A' }}</span>
                                        </td>
                                        <td>
                                            <button type="button" class="btn btn-sm btn-outline-info" 
                                                    onclick="showLogDetails({{ $log->id }})" title="Ver Detalles">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center py-4">
                                            <i class="fas fa-history fa-3x text-muted mb-3"></i>
                                            <p class="text-muted">No se encontraron logs</p>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Paginación -->
                    @if($logs->hasPages())
                        <div class="d-flex justify-content-center mt-4">
                            {{ $logs->appends(request()->query())->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal para detalles del log -->
<div class="modal fade" id="logDetailsModal" tabindex="-1" aria-labelledby="logDetailsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="logDetailsModalLabel">Detalles del Log</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="logDetailsContent">
                <!-- Contenido cargado dinámicamente -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
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
<script>
function showLogDetails(logId) {
    fetch(`/superadmin/audit-logs/${logId}`, {
        method: 'GET',
        headers: {
            'Accept': 'application/json',
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                const log = data.log;
                const content = `
                    <div class="row">
                        <div class="col-md-6">
                            <h6>Información General</h6>
                            <table class="table table-sm">
                                <tr><td><strong>Usuario:</strong></td><td>${log.user ? log.user.name : 'Sistema'}</td></tr>
                                <tr><td><strong>Acción:</strong></td><td>${log.action}</td></tr>
                                <tr><td><strong>Modelo:</strong></td><td>${log.model_type || 'N/A'}</td></tr>
                                <tr><td><strong>ID del Modelo:</strong></td><td>${log.model_id || 'N/A'}</td></tr>
                                <tr><td><strong>Fecha:</strong></td><td>${log.created_at}</td></tr>
                                <tr><td><strong>IP:</strong></td><td>${log.ip_address || 'N/A'}</td></tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <h6>Descripción</h6>
                            <p>${log.description || 'Sin descripción'}</p>
                            
                            <h6>User Agent</h6>
                            <p class="small text-muted">${log.user_agent || 'N/A'}</p>
                        </div>
                    </div>
                    ${log.old_values || log.new_values ? `
                    <div class="row mt-3">
                        <div class="col-12">
                            <h6>Cambios</h6>
                            <div class="row">
                                ${log.old_values ? `
                                <div class="col-md-6">
                                    <h6 class="text-danger">Valores Anteriores</h6>
                                    <pre class="bg-light p-2 small">${JSON.stringify(log.old_values, null, 2)}</pre>
                                </div>
                                ` : ''}
                                ${log.new_values ? `
                                <div class="col-md-6">
                                    <h6 class="text-success">Valores Nuevos</h6>
                                    <pre class="bg-light p-2 small">${JSON.stringify(log.new_values, null, 2)}</pre>
                                </div>
                                ` : ''}
                            </div>
                        </div>
                    </div>
                    ` : ''}
                `;
                
                document.getElementById('logDetailsContent').innerHTML = content;
                new bootstrap.Modal(document.getElementById('logDetailsModal')).show();
            } else {
                alert('Error al cargar los detalles del log');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error al cargar los detalles del log: ' + error.message);
        });
}

function exportLogs() {
    const search = document.querySelector('input[name="search"]').value;
    const action = document.querySelector('select[name="action"]').value;
    const model = document.querySelector('select[name="model"]').value;
    const user = document.querySelector('select[name="user"]').value;
    const date = document.querySelector('input[name="date"]').value;
    
    let url = '{{ route("superadmin.audit-logs.export") }}?';
    const params = new URLSearchParams();
    
    if (search) params.append('search', search);
    if (action) params.append('action', action);
    if (model) params.append('model', model);
    if (user) params.append('user', user);
    if (date) params.append('date', date);
    
    window.open(url + params.toString(), '_blank');
}

function cleanupLogs() {
    const days = prompt('¿Cuántos días de logs quieres conservar? (mínimo 30):', '90');
    
    if (days && parseInt(days) >= 30) {
        if (confirm(`¿Estás seguro de que quieres eliminar los logs más antiguos de ${days} días? Esta acción no se puede deshacer.`)) {
            fetch('{{ route("superadmin.audit-logs.cleanup") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({ days: parseInt(days) })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert(`Se eliminaron ${data.deleted_count} logs antiguos`);
                    location.reload();
                } else {
                    alert('Error: ' + (data.message || 'No se pudieron eliminar los logs'));
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error al eliminar los logs');
            });
        }
    } else if (days) {
        alert('Debes especificar al menos 30 días');
    }
}
</script>
@endpush
@endsection
