@extends('layouts.app')

@section('title', 'Plantillas de Correo - Super Admin')

@section('content')
<div class="container-fluid py-4">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h3 mb-0 text-gray-800">
                        <i class="fas fa-envelope text-primary me-2"></i>
                        Plantillas de Correo
                    </h1>
                    <p class="text-muted mb-0">Gestiona las plantillas de correo del sistema</p>
                </div>
                <div class="d-flex gap-2">
                    <form method="POST" action="{{ route('superadmin.email-templates.create-defaults') }}" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-info">
                            <i class="fas fa-magic me-1"></i>Crear Plantillas por Defecto
                        </button>
                    </form>
                    <a href="{{ route('superadmin.email-templates.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus me-1"></i>Nueva Plantilla
                    </a>
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
                        <div class="col-md-4">
                            <label class="form-label">Buscar</label>
                            <input type="text" class="form-control" name="search" 
                                   value="{{ request('search') }}" placeholder="Nombre o asunto...">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Tipo</label>
                            <select class="form-select" name="type">
                                <option value="">Todos</option>
                                <option value="reservation" {{ request('type') === 'reservation' ? 'selected' : '' }}>Reservas</option>
                                <option value="notification" {{ request('type') === 'notification' ? 'selected' : '' }}>Notificaciones</option>
                                <option value="system" {{ request('type') === 'system' ? 'selected' : '' }}>Sistema</option>
                                <option value="marketing" {{ request('type') === 'marketing' ? 'selected' : '' }}>Marketing</option>
                            </select>
                        </div>
                        <div class="col-md-3 d-flex align-items-end">
                            <button type="submit" class="btn btn-primary me-2">
                                <i class="fas fa-search me-1"></i>Filtrar
                            </button>
                            <a href="{{ route('superadmin.email-templates') }}" class="btn btn-outline-secondary">
                                <i class="fas fa-times me-1"></i>Limpiar
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Lista de Plantillas -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-list me-2"></i>Plantillas ({{ $templates->total() }})
                    </h6>
                </div>
                <div class="card-body">
                    @if($templates->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Nombre</th>
                                        <th>Tipo</th>
                                        <th>Asunto</th>
                                        <th>Estado</th>
                                        <th>Variables</th>
                                        <th>Actualizada</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($templates as $template)
                                        <tr>
                                            <td>
                                                <div class="fw-bold">{{ $template->display_name }}</div>
                                                <small class="text-muted">{{ $template->name }}</small>
                                            </td>
                                            <td>
                                                <span class="badge bg-{{ $template->type === 'reservation' ? 'primary' : ($template->type === 'notification' ? 'info' : ($template->type === 'system' ? 'warning' : 'success')) }}">
                                                    {{ ucfirst($template->type) }}
                                                </span>
                                            </td>
                                            <td>
                                                <div class="text-truncate" style="max-width: 200px;" title="{{ $template->subject }}">
                                                    {{ $template->subject }}
                                                </div>
                                            </td>
                                            <td>
                                                @if($template->is_active)
                                                    <span class="badge bg-success">
                                                        <i class="fas fa-check me-1"></i>Activa
                                                    </span>
                                                @else
                                                    <span class="badge bg-secondary">
                                                        <i class="fas fa-ban me-1"></i>Inactiva
                                                    </span>
                                                @endif
                                            </td>
                                            <td>
                                                <span class="badge bg-info">
                                                    {{ count($template->variables ?? []) }} variables
                                                </span>
                                            </td>
                                            <td>
                                                <div>{{ $template->updated_at->format('d/m/Y') }}</div>
                                                <small class="text-muted">{{ $template->updated_at->format('H:i') }}</small>
                                            </td>
                                            <td>
                                                <div class="btn-group" role="group">
                                                    <a href="{{ route('superadmin.email-templates.show', $template) }}" 
                                                       class="btn btn-sm btn-outline-primary" title="Ver">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    <a href="{{ route('superadmin.email-templates.edit', $template) }}" 
                                                       class="btn btn-sm btn-outline-warning" title="Editar">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    <button type="button" 
                                                            class="btn btn-sm btn-outline-info" 
                                                            onclick="previewTemplate({{ $template->id }})" 
                                                            title="Previsualizar">
                                                        <i class="fas fa-eye"></i>
                                                    </button>
                                                    <button type="button" 
                                                            class="btn btn-sm btn-outline-{{ $template->is_active ? 'secondary' : 'success' }}" 
                                                            onclick="toggleTemplateStatus({{ $template->id }})" 
                                                            title="{{ $template->is_active ? 'Desactivar' : 'Activar' }}">
                                                        <i class="fas fa-{{ $template->is_active ? 'ban' : 'check' }}"></i>
                                                    </button>
                                                    <button type="button" 
                                                            class="btn btn-sm btn-outline-danger" 
                                                            onclick="deleteTemplate({{ $template->id }})" 
                                                            title="Eliminar">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Paginación -->
                        <div class="d-flex justify-content-center">
                            {{ $templates->links() }}
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-envelope fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">No hay plantillas</h5>
                            <p class="text-muted">No se encontraron plantillas con los filtros aplicados.</p>
                            <a href="{{ route('superadmin.email-templates.create') }}" class="btn btn-primary">
                                <i class="fas fa-plus me-1"></i>Crear Primera Plantilla
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal de Previsualización -->
<div class="modal fade" id="previewModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Previsualización de Plantilla</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div id="previewContent">
                    <div class="text-center">
                        <div class="spinner-border" role="status">
                            <span class="visually-hidden">Cargando...</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                <button type="button" class="btn btn-primary" onclick="sendTestEmail()">Enviar Prueba</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal de Envío de Prueba -->
<div class="modal fade" id="testEmailModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Enviar Correo de Prueba</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="testEmailForm">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="test_email" class="form-label">Correo de destino</label>
                        <input type="email" class="form-control" id="test_email" name="email" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Enviar Prueba</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
let currentTemplateId = null;

function previewTemplate(templateId) {
    currentTemplateId = templateId;
    $('#previewModal').modal('show');
    
    // Cargar previsualización
    fetch(`/superadmin/email-templates/${templateId}/preview`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            document.getElementById('previewContent').innerHTML = data.body;
        } else {
            document.getElementById('previewContent').innerHTML = '<div class="alert alert-danger">Error al cargar la previsualización</div>';
        }
    })
    .catch(error => {
        document.getElementById('previewContent').innerHTML = '<div class="alert alert-danger">Error al cargar la previsualización</div>';
    });
}

function sendTestEmail() {
    $('#previewModal').modal('hide');
    $('#testEmailModal').modal('show');
}

function toggleTemplateStatus(templateId) {
    fetch(`/superadmin/email-templates/${templateId}/toggle-status`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert('Error: ' + (data.message || 'No se pudo cambiar el estado'));
        }
    })
    .catch(error => {
        alert('Error al cambiar el estado de la plantilla');
    });
}

function deleteTemplate(templateId) {
    if (confirm('¿Estás seguro de que quieres eliminar esta plantilla?')) {
        fetch(`/superadmin/email-templates/${templateId}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('Error: ' + (data.message || 'No se pudo eliminar la plantilla'));
            }
        })
        .catch(error => {
            alert('Error al eliminar la plantilla');
        });
    }
}

$('#testEmailForm').on('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    
    fetch(`/superadmin/email-templates/${currentTemplateId}/send-test`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Accept': 'application/json'
        },
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Correo de prueba enviado correctamente');
            $('#testEmailModal').modal('hide');
        } else {
            alert('Error: ' + (data.message || 'No se pudo enviar el correo'));
        }
    })
    .catch(error => {
        alert('Error al enviar el correo de prueba');
    });
});
</script>
@endpush
@endsection
