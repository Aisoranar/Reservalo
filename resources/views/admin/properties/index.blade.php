@extends('layouts.app')

@section('title', 'Gestión de Propiedades - Admin')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h2">
                <i class="fas fa-home me-2"></i>Gestión de Propiedades
            </h1>
            @if(isset($activeGlobalPricing) && $activeGlobalPricing)
                <div class="alert alert-info alert-sm d-inline-block mb-0">
                    <i class="fas fa-tag me-1"></i>
                    <strong>Precio Global Activo:</strong> {{ $activeGlobalPricing->name }} - 
                    ${{ number_format($activeGlobalPricing->final_price, 0, ',', '.') }}
                </div>
            @endif
        </div>
        <a href="{{ route('admin.properties.create') }}" class="btn btn-primary">
            <i class="fas fa-plus me-2"></i>Nueva Propiedad
        </a>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Imagen</th>
                            <th>Propiedad</th>
                            <th>Propietario</th>
                            <th>Ubicación</th>
                            <th>Estado</th>
                            <th>Precio</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($properties as $property)
                            <tr>
                                <td>
                                    @if($property->images->first())
                                        <img src="{{ $property->images->first()->full_url }}" 
                                             alt="{{ $property->name }}"
                                             class="rounded"
                                             style="width: 50px; height: 50px; object-fit: cover;">
                                    @else
                                        <div class="bg-light rounded d-flex align-items-center justify-content-center"
                                             style="width: 50px; height: 50px;">
                                            <i class="fas fa-home text-muted"></i>
                                        </div>
                                    @endif
                                </td>
                                <td>
                                    <div class="d-flex flex-column">
                                        <strong class="text-primary">{{ $property->name }}</strong>
                                        <small class="text-muted">{{ ucfirst($property->type) }}</small>
                                        <div class="mt-1">
                                            <span class="badge bg-light text-dark">
                                                <i class="fas fa-users me-1"></i>{{ $property->capacity }} personas
                                            </span>
                                            @if($property->bedrooms)
                                                <span class="badge bg-light text-dark ms-1">
                                                    <i class="fas fa-bed me-1"></i>{{ $property->bedrooms }} hab
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="avatar-sm bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-2">
                                            {{ substr($property->owner->name ?? 'N', 0, 1) }}
                                        </div>
                                        <div>
                                            <div class="fw-bold">{{ $property->owner->name ?? 'N/A' }}</div>
                                            <small class="text-muted">{{ $property->owner->email ?? '' }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <i class="fas fa-map-marker-alt text-primary me-2"></i>
                                        <div>
                                            <div class="fw-bold">{{ $property->city->name ?? 'N/A' }}</div>
                                            <small class="text-muted">{{ $property->city->department->name ?? '' }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    @if($property->is_active)
                                        <span class="badge bg-success">Activa</span>
                                    @else
                                        <span class="badge bg-secondary">Inactiva</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="d-flex flex-column">
                                        <span class="fw-bold text-primary">${{ number_format($property->effective_price, 0, ',', '.') }}</span>
                                        @if($property->effective_price != $property->price_per_night)
                                            <small class="text-muted text-decoration-line-through">${{ number_format($property->price_per_night, 0, ',', '.') }}</small>
                                        @endif
                                    </div>
                                </td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('properties.show', $property) }}" 
                                           class="btn btn-sm btn-outline-primary" 
                                           title="Ver">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('admin.properties.edit', $property) }}" 
                                           class="btn btn-sm btn-outline-warning" 
                                           title="Editar">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <button type="button" 
                                                class="btn btn-sm btn-outline-danger" 
                                                title="Eliminar"
                                                onclick="confirmDelete({{ $property->id }})">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-4">
                                    <div class="text-muted">
                                        <i class="fas fa-home fa-3x mb-3"></i>
                                        <p>No hay propiedades registradas</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($properties->hasPages())
                <div class="d-flex justify-content-center mt-4">
                    {{ $properties->links() }}
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Modal de confirmación de eliminación -->
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirmar Eliminación</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>¿Estás seguro de que quieres eliminar esta propiedad?</p>
                <p class="text-danger"><small>Esta acción no se puede deshacer.</small></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <form id="deleteForm" method="POST" style="display: inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Eliminar</button>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection

@push('styles')
<style>
.avatar-sm {
    width: 32px;
    height: 32px;
    font-size: 0.875rem;
    font-weight: 600;
}

.alert-sm {
    padding: 0.5rem 0.75rem;
    font-size: 0.875rem;
}

.table th {
    background-color: #f8f9fa;
    border-bottom: 2px solid #dee2e6;
    font-weight: 600;
    color: #495057;
}

.table td {
    vertical-align: middle;
    border-bottom: 1px solid #dee2e6;
}

.table tbody tr:hover {
    background-color: #f8f9fa;
}

.badge {
    font-size: 0.75rem;
    padding: 0.25rem 0.5rem;
}

.text-decoration-line-through {
    text-decoration: line-through;
}
</style>
@endpush

@push('scripts')
<script>
function confirmDelete(propertyId) {
    const modal = new bootstrap.Modal(document.getElementById('deleteModal'));
    const form = document.getElementById('deleteForm');
    form.action = `/admin/properties/${propertyId}`;
    modal.show();
}
</script>
@endpush
