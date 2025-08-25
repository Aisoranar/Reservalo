@extends('layouts.app')

@section('title', 'Selector de Ubicaciones - Colombia')

@section('content')
<div class="container py-4">
    <div class="row">
        <div class="col-12">
            <h1 class="h2 mb-4">Selector de Ubicaciones para Colombia</h1>
            <p class="text-muted">Ejemplo de implementación del selector de departamentos y ciudades</p>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-map-marker-alt me-2"></i>Selección de Ubicación
                    </h5>
                </div>
                <div class="card-body">
                    <form id="locationForm">
                        <div class="mb-3">
                            <label for="department" class="form-label">Departamento *</label>
                            <select class="form-select" id="department" name="department" required>
                                <option value="">Selecciona un departamento</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="city" class="form-label">Ciudad *</label>
                            <select class="form-select" id="city" name="city" required disabled>
                                <option value="">Primero selecciona un departamento</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="address" class="form-label">Dirección</label>
                            <input type="text" class="form-control" id="address" name="address" 
                                   placeholder="Dirección específica, barrio, etc.">
                        </div>

                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i>Guardar Ubicación
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-info-circle me-2"></i>Información de la Selección
                    </h5>
                </div>
                <div class="card-body">
                    <div id="selectionInfo">
                        <p class="text-muted">Selecciona un departamento y una ciudad para ver la información</p>
                    </div>
                </div>
            </div>

            <div class="card mt-3">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-code me-2"></i>Código de Implementación
                    </h5>
                </div>
                <div class="card-body">
                    <pre><code>// HTML
&lt;select id="department"&gt;
    &lt;option value=""&gt;Selecciona un departamento&lt;/option&gt;
&lt;/select&gt;

&lt;select id="city"&gt;
    &lt;option value=""&gt;Selecciona una ciudad&lt;/option&gt;
&lt;/select&gt;

// JavaScript
const locationSelector = createLocationSelector('department', 'city', {
    placeholder: 'Selecciona una opción'
});

// Escuchar cambios en la ciudad
document.getElementById('city').addEventListener('cityChanged', (e) => {
    console.log('Ciudad seleccionada:', e.detail.cityId);
});</code></pre>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Crear el selector de ubicaciones
    const locationSelector = createLocationSelector('department', 'city', {
        placeholder: 'Selecciona una opción'
    });

    // Escuchar cambios en la ciudad
    document.getElementById('city').addEventListener('cityChanged', function(e) {
        updateSelectionInfo();
    });

    // Escuchar cambios en el departamento
    document.getElementById('department').addEventListener('change', function() {
        updateSelectionInfo();
    });

    // Función para actualizar la información mostrada
    function updateSelectionInfo() {
        const departmentId = locationSelector.getSelectedDepartment();
        const cityId = locationSelector.getSelectedCity();
        const departmentText = locationSelector.getSelectedDepartmentText();
        const cityText = locationSelector.getSelectedCityText();

        const infoDiv = document.getElementById('selectionInfo');
        
        if (departmentId && cityId) {
            infoDiv.innerHTML = `
                <div class="alert alert-success">
                    <h6><i class="fas fa-check-circle me-2"></i>Ubicación Seleccionada</h6>
                    <p class="mb-1"><strong>Departamento:</strong> ${departmentText}</p>
                    <p class="mb-1"><strong>Ciudad:</strong> ${cityText}</p>
                    <p class="mb-0"><strong>IDs:</strong> Dept: ${departmentId}, Ciudad: ${cityId}</p>
                </div>
            `;
        } else if (departmentId) {
            infoDiv.innerHTML = `
                <div class="alert alert-info">
                    <h6><i class="fas fa-info-circle me-2"></i>Departamento Seleccionado</h6>
                    <p class="mb-1"><strong>Departamento:</strong> ${departmentText}</p>
                    <p class="mb-0">Ahora selecciona una ciudad</p>
                </div>
            `;
        } else {
            infoDiv.innerHTML = `
                <p class="text-muted">Selecciona un departamento y una ciudad para ver la información</p>
            `;
        }
    }

    // Manejar el envío del formulario
    document.getElementById('locationForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        const departmentId = locationSelector.getSelectedDepartment();
        const cityId = locationSelector.getSelectedCity();
        const address = document.getElementById('address').value;

        if (!departmentId || !cityId) {
            alert('Por favor selecciona un departamento y una ciudad');
            return;
        }

        // Aquí puedes enviar los datos al servidor
        const formData = {
            department_id: departmentId,
            city_id: cityId,
            address: address
        };

        console.log('Datos del formulario:', formData);
        alert('Ubicación guardada correctamente!\n\n' + JSON.stringify(formData, null, 2));
    });
});
</script>
@endpush
