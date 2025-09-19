@extends('layouts.app')

@section('title', 'Configuración de Precios Globales')

@section('styles')
<style>
/* Estilos para botones de acciones */
.btn-group .btn {
    min-width: 35px;
    height: 35px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 6px !important;
    margin: 0 2px;
    transition: all 0.2s ease;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.btn-group .btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.2);
}

.btn-group .btn-success {
    background-color: #28a745 !important;
    border-color: #28a745 !important;
    color: #ffffff !important;
}

.btn-group .btn-success:hover {
    background-color: #218838 !important;
    border-color: #1e7e34 !important;
}

.btn-group .btn-warning {
    background-color: #ffc107 !important;
    border-color: #ffc107 !important;
    color: #000000 !important;
}

.btn-group .btn-warning:hover {
    background-color: #e0a800 !important;
    border-color: #d39e00 !important;
}

.btn-group .btn-info {
    background-color: #17a2b8 !important;
    border-color: #17a2b8 !important;
    color: #ffffff !important;
}

.btn-group .btn-info:hover {
    background-color: #138496 !important;
    border-color: #117a8b !important;
}

.btn-group .btn-danger {
    background-color: #dc3545 !important;
    border-color: #dc3545 !important;
    color: #ffffff !important;
}

.btn-group .btn-danger:hover {
    background-color: #c82333 !important;
    border-color: #bd2130 !important;
}

/* Iconos más grandes y visibles */
.btn-group .btn i {
    font-size: 14px;
    font-weight: 600;
}

/* Estilos para el botón "Nuevo Precio Global" */
.btn-success.text-white {
    background-color: #28a745 !important;
    border-color: #28a745 !important;
    color: #ffffff !important;
    font-weight: 600 !important;
    text-shadow: 0 1px 2px rgba(0,0,0,0.2);
    padding: 12px 24px;
    font-size: 16px;
    border-radius: 8px;
    box-shadow: 0 3px 6px rgba(40, 167, 69, 0.3);
    transition: all 0.3s ease;
}

.btn-success.text-white:hover {
    background-color: #218838 !important;
    border-color: #1e7e34 !important;
    color: #ffffff !important;
    transform: translateY(-2px);
    box-shadow: 0 6px 12px rgba(40, 167, 69, 0.4);
}

.btn-success.text-white:focus {
    box-shadow: 0 0 0 0.2rem rgba(40, 167, 69, 0.5) !important;
    outline: none;
}

.btn-success.text-white:active {
    transform: translateY(0);
    box-shadow: 0 2px 4px rgba(40, 167, 69, 0.3);
}
</style>
@endsection

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h3 mb-0 text-gray-800">
                        <i class="fas fa-dollar-sign me-2"></i>
                        Configuración de Precios Globales
                    </h1>
                    <p class="text-muted mb-0">Gestiona los precios globales para todas las propiedades</p>
                </div>
                <div>
                    <a href="{{ route('superadmin.pricing.create') }}" class="btn btn-success text-white fw-bold">
                        <i class="fas fa-plus me-1"></i>
                        Nuevo Precio Global
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Precios Activos -->
    @php
        $activePricings = \App\Models\GlobalPricing::getAllActivePricing();
    @endphp
    @if($activePricings->count() > 0)
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-success">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-check-circle me-2"></i>
                        Precios Globales Activos ({{ $activePricings->count() }})
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        @foreach($activePricings as $pricing)
                        <div class="col-md-6 col-lg-4 mb-3">
                            <div class="card h-100 border-success">
                                <div class="card-body">
                                    <h6 class="text-success mb-2">{{ $pricing->name }}</h6>
                                    <p class="text-muted mb-2 small">{{ Str::limit($pricing->description, 60) }}</p>
                                    <div class="d-flex align-items-center mb-2">
                                        <span class="badge bg-info me-2">
                                            {{ $pricing->price_type === 'daily' ? 'Diario' : 'Nocturno' }}
                                        </span>
                                        @if($pricing->has_discount)
                                            <span class="badge bg-warning text-dark me-2">
                                                Con Descuento
                                            </span>
                                        @endif
                                    </div>
                                    <div class="text-center">
                                        <h5 class="text-success mb-1">
                                            ${{ number_format($pricing->final_price, 0, ',', '.') }}
                                        </h5>
                                        <small class="text-muted">
                                            Base: ${{ number_format($pricing->base_price, 0, ',', '.') }}
                                        </small>
                                        @if($pricing->has_discount)
                                            <br>
                                            <small class="text-success">
                                                Descuento: 
                                                @if($pricing->discount_type === 'percentage')
                                                    {{ $pricing->discount_percentage }}%
                                                @else
                                                    ${{ number_format($pricing->discount_amount, 0, ',', '.') }}
                                                @endif
                                            </small>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
    @else
    <div class="row mb-4">
        <div class="col-12">
            <div class="alert alert-warning">
                <i class="fas fa-exclamation-triangle me-2"></i>
                <strong>No hay precios globales activos.</strong> 
                Crea un nuevo precio global para comenzar a usarlo en las reservas.
            </div>
        </div>
    </div>
    @endif

    <!-- Lista de Precios -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-list me-2"></i>
                        Todos los Precios Globales
                    </h5>
                </div>
                <div class="card-body">
                    @if($pricings->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Nombre</th>
                                        <th>Precio Base</th>
                                        <th>Tipo</th>
                                        <th>Descuento</th>
                                        <th>Precio Final</th>
                                        <th>Estado</th>
                                        <th>Creado</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($pricings as $pricing)
                                    <tr class="{{ $pricing->is_active ? 'table-success' : '' }}">
                                        <td>
                                            <div>
                                                <strong>{{ $pricing->name }}</strong>
                                                @if($pricing->is_active)
                                                    <span class="badge bg-success ms-2">Activo</span>
                                                @endif
                                            </div>
                                            @if($pricing->description)
                                                <small class="text-muted">{{ Str::limit($pricing->description, 50) }}</small>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="fw-bold">${{ number_format($pricing->base_price, 0, ',', '.') }}</span>
                                        </td>
                                        <td>
                                            <span class="badge bg-info">
                                                {{ $pricing->price_type === 'daily' ? 'Diario' : 'Nocturno' }}
                                            </span>
                                        </td>
                                        <td>
                                            @if($pricing->has_discount)
                                                @if($pricing->discount_type === 'percentage')
                                                    <span class="text-success">{{ $pricing->discount_percentage }}%</span>
                                                @else
                                                    <span class="text-success">-${{ number_format($pricing->discount_amount, 0, ',', '.') }}</span>
                                                @endif
                                            @else
                                                <span class="text-muted">Sin descuento</span>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="fw-bold text-success">
                                                ${{ number_format($pricing->final_price, 0, ',', '.') }}
                                            </span>
                                        </td>
                                        <td>
                                            @if($pricing->is_active)
                                                <span class="badge bg-success">
                                                    <i class="fas fa-check-circle me-1"></i>Activo
                                                </span>
                                                <br>
                                                <small class="text-muted">
                                                    <i class="fas fa-info-circle me-1"></i>Eliminable
                                                </small>
                                            @else
                                                <span class="badge bg-secondary">
                                                    <i class="fas fa-pause-circle me-1"></i>Inactivo
                                                </span>
                                            @endif
                                        </td>
                                        <td>
                                            <small class="text-muted">
                                                {{ $pricing->created_at->format('d/m/Y') }}<br>
                                                por {{ $pricing->creator->name ?? 'Sistema' }}
                                            </small>
                                        </td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                @if(!$pricing->is_active)
                                                    <form action="{{ route('superadmin.pricing.activate', $pricing) }}" method="POST" class="d-inline">
                                                        @csrf
                                                        <button type="submit" class="btn btn-success btn-sm text-white fw-bold" 
                                                                onclick="return confirm('¿Activar este precio global?')"
                                                                title="Activar precio">
                                                            <i class="fas fa-check"></i>
                                                        </button>
                                                    </form>
                                                @else
                                                    <form action="{{ route('superadmin.pricing.deactivate', $pricing) }}" method="POST" class="d-inline">
                                                        @csrf
                                                        <button type="submit" class="btn btn-warning btn-sm text-white fw-bold" 
                                                                onclick="return confirm('¿Desactivar este precio global?')"
                                                                title="Desactivar precio">
                                                            <i class="fas fa-pause"></i>
                                                        </button>
                                                    </form>
                                                @endif
                                                
                                                <a href="{{ route('superadmin.pricing.edit', $pricing) }}" 
                                                   class="btn btn-info btn-sm text-white fw-bold"
                                                   title="Editar precio">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                
                                                <form action="{{ route('superadmin.pricing.destroy', $pricing) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-danger btn-sm text-white fw-bold" 
                                                            onclick="return confirmDelete('{{ $pricing->name }}', {{ $pricing->is_active ? 'true' : 'false' }})"
                                                            title="Eliminar precio">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-dollar-sign fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">No hay precios globales configurados</h5>
                            <p class="text-muted">Crea tu primer precio global para comenzar a usarlo en las reservas.</p>
                            <a href="{{ route('superadmin.pricing.create') }}" class="btn btn-primary">
                                <i class="fas fa-plus me-1"></i>
                                Crear Primer Precio Global
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

@if(session('success'))
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            showToast('{{ session('success') }}', 'success');
        });
    </script>
@endif

@if(session('error'))
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            showToast('{{ session('error') }}', 'danger');
        });
    </script>
@endif

<script>
// Función para confirmar eliminación con mensajes diferentes según el estado
function confirmDelete(priceName, isActive) {
    let message;
    
    if (isActive) {
        message = `⚠️ ADVERTENCIA: Este precio global está ACTIVO.\n\n¿Estás seguro de que quieres eliminar "${priceName}"?\n\nEsta acción:\n• Eliminará el precio permanentemente\n• Afectará las reservas que usen este precio\n• No se puede deshacer\n\n¿Continuar con la eliminación?`;
    } else {
        message = `¿Estás seguro de que quieres eliminar "${priceName}"?\n\nEsta acción no se puede deshacer.`;
    }
    
    return confirm(message);
}
</script>
@endsection
