@extends('layouts.app')

@section('title', 'Editar Plantilla - Super Admin')

@section('content')
<div class="container-fluid py-4">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h3 mb-0 text-gray-800">
                        <i class="fas fa-edit text-primary me-2"></i>
                        Editar Plantilla: {{ $emailTemplate->display_name }}
                    </h1>
                    <p class="text-muted mb-0">Modifica la plantilla de correo</p>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('superadmin.email-templates') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left me-1"></i>Volver
                    </a>
                    <button type="button" class="btn btn-info" onclick="previewTemplate()">
                        <i class="fas fa-eye me-1"></i>Previsualizar
                    </button>
                </div>
            </div>
        </div>
    </div>

    <form method="POST" action="{{ route('superadmin.email-templates.update', $emailTemplate) }}">
        @csrf
        @method('PUT')
        
        <div class="row">
            <!-- Formulario Principal -->
            <div class="col-lg-8">
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">
                            <i class="fas fa-cog me-2"></i>Configuración de la Plantilla
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="name" class="form-label">Nombre interno <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                           id="name" name="name" value="{{ old('name', $emailTemplate->name) }}" required>
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="form-text text-muted">Identificador único para la plantilla</small>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="display_name" class="form-label">Nombre para mostrar <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('display_name') is-invalid @enderror" 
                                           id="display_name" name="display_name" value="{{ old('display_name', $emailTemplate->display_name) }}" required>
                                    @error('display_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="type" class="form-label">Tipo <span class="text-danger">*</span></label>
                                    <select class="form-select @error('type') is-invalid @enderror" id="type" name="type" required>
                                        @foreach($types as $value => $label)
                                            <option value="{{ $value }}" {{ old('type', $emailTemplate->type) === $value ? 'selected' : '' }}>
                                                {{ $label }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('type')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="subject" class="form-label">Asunto <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('subject') is-invalid @enderror" 
                                           id="subject" name="subject" value="{{ old('subject', $emailTemplate->subject) }}" required>
                                    @error('subject')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">Descripción</label>
                            <textarea class="form-control @error('description') is-invalid @enderror" 
                                      id="description" name="description" rows="2">{{ old('description', $emailTemplate->description) }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Cuerpo del Correo -->
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">
                            <i class="fas fa-envelope me-2"></i>Cuerpo del Correo
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label for="body" class="form-label">Cuerpo HTML <span class="text-danger">*</span></label>
                            <textarea class="form-control @error('body') is-invalid @enderror" 
                                      id="body" name="body" rows="15" required>{{ old('body', $emailTemplate->body) }}</textarea>
                            @error('body')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted">Usa las variables disponibles en el panel lateral</small>
                        </div>

                        <div class="mb-3">
                            <label for="body_text" class="form-label">Cuerpo de Texto Plano</label>
                            <textarea class="form-control @error('body_text') is-invalid @enderror" 
                                      id="body_text" name="body_text" rows="8">{{ old('body_text', $emailTemplate->body_text) }}</textarea>
                            @error('body_text')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted">Versión de texto plano del correo (opcional)</small>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Panel Lateral -->
            <div class="col-lg-4">
                <!-- Variables Disponibles -->
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">
                            <i class="fas fa-code me-2"></i>Variables Disponibles
                        </h6>
                    </div>
                    <div class="card-body">
                        <div id="variablesList">
                            @if($emailTemplate->variables)
                                @foreach($emailTemplate->variables as $key => $description)
                                    <div class="mb-2">
                                        <code class="text-primary">{{ '{{' . $key . '}}' }}</code>
                                        <small class="text-muted d-block">{{ $description }}</small>
                                    </div>
                                @endforeach
                            @else
                                <p class="text-muted">No hay variables definidas</p>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Acciones -->
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">
                            <i class="fas fa-bolt me-2"></i>Acciones
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-success">
                                <i class="fas fa-save me-2"></i>Guardar Cambios
                            </button>
                            <button type="button" class="btn btn-info" onclick="previewTemplate()">
                                <i class="fas fa-eye me-2"></i>Previsualizar
                            </button>
                            <button type="button" class="btn btn-warning" onclick="sendTestEmail()">
                                <i class="fas fa-paper-plane me-2"></i>Enviar Prueba
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Información -->
                <div class="card shadow">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">
                            <i class="fas fa-info-circle me-2"></i>Información
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="mb-2">
                            <strong>Creada:</strong>
                            <p class="text-muted mb-0">{{ $emailTemplate->created_at->format('d/m/Y H:i') }}</p>
                        </div>
                        <div class="mb-2">
                            <strong>Última actualización:</strong>
                            <p class="text-muted mb-0">{{ $emailTemplate->updated_at->format('d/m/Y H:i') }}</p>
                        </div>
                        <div class="mb-2">
                            <strong>Estado:</strong>
                            <p class="text-muted mb-0">
                                @if($emailTemplate->is_active)
                                    <span class="badge bg-success">Activa</span>
                                @else
                                    <span class="badge bg-secondary">Inactiva</span>
                                @endif
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
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
function previewTemplate() {
    $('#previewModal').modal('show');
    
    // Obtener datos del formulario
    const formData = new FormData(document.querySelector('form'));
    
    fetch(`/superadmin/email-templates/{{ $emailTemplate->id }}/preview`, {
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
    $('#testEmailModal').modal('show');
}

$('#testEmailForm').on('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    
    fetch(`/superadmin/email-templates/{{ $emailTemplate->id }}/send-test`, {
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
