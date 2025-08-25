/**
 * Selector de ubicaciones para Colombia
 * Maneja la selección de departamentos y ciudades
 */
class LocationSelector {
    constructor(departmentSelectId, citySelectId, options = {}) {
        this.departmentSelect = document.getElementById(departmentSelectId);
        this.citySelect = document.getElementById(citySelectId);
        this.options = {
            placeholder: 'Selecciona una opción',
            loadingText: 'Cargando...',
            noDataText: 'No hay datos disponibles',
            ...options
        };

        this.init();
    }

    init() {
        if (!this.departmentSelect || !this.citySelect) {
            console.error('LocationSelector: No se encontraron los elementos select');
            return;
        }

        this.setupEventListeners();
        this.loadDepartments();
    }

    setupEventListeners() {
        // Cambio en departamento
        this.departmentSelect.addEventListener('change', (e) => {
            const departmentId = e.target.value;
            if (departmentId) {
                this.loadCitiesByDepartment(departmentId);
            } else {
                this.clearCities();
            }
        });

        // Cambio en ciudad
        this.citySelect.addEventListener('change', (e) => {
            // Disparar evento personalizado para notificar cambios
            this.citySelect.dispatchEvent(new CustomEvent('cityChanged', {
                detail: { cityId: e.target.value }
            }));
        });
    }

    async loadDepartments() {
        try {
            console.log('🔄 Cargando departamentos...');
            this.setDepartmentLoading(true);
            
            const response = await fetch('/api/departments');
            console.log('📡 Respuesta de la API:', response);
            
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            
            const departments = await response.json();
            console.log('📋 Departamentos recibidos:', departments);

            this.populateDepartments(departments);
        } catch (error) {
            console.error('❌ Error cargando departamentos:', error);
            this.showDepartmentError();
        } finally {
            this.setDepartmentLoading(false);
        }
    }

    async loadCitiesByDepartment(departmentId) {
        try {
            this.setCityLoading(true);
            
            const response = await fetch(`/api/cities/by-department?department_id=${departmentId}`);
            const cities = await response.json();

            this.populateCities(cities);
        } catch (error) {
            console.error('Error cargando ciudades:', error);
            this.showCityError();
        } finally {
            this.setCityLoading(false);
        }
    }

    populateDepartments(departments) {
        console.log('🏗️ Poblando departamentos en el select...');
        this.departmentSelect.innerHTML = '';
        
        // Opción por defecto
        const defaultOption = document.createElement('option');
        defaultOption.value = '';
        defaultOption.textContent = this.options.placeholder;
        this.departmentSelect.appendChild(defaultOption);

        // Opciones de departamentos
        departments.forEach(department => {
            console.log(`➕ Agregando departamento: ${department.name} (${department.code})`);
            const option = document.createElement('option');
            option.value = department.id;
            option.textContent = `${department.name} (${department.code})`;
            this.departmentSelect.appendChild(option);
        });
        
        console.log(`✅ Total de departamentos agregados: ${departments.length}`);
    }

    populateCities(cities) {
        this.citySelect.innerHTML = '';
        
        // Opción por defecto
        const defaultOption = document.createElement('option');
        defaultOption.value = '';
        defaultOption.textContent = this.options.placeholder;
        this.citySelect.appendChild(defaultOption);

        // Opciones de ciudades
        cities.forEach(city => {
            const option = document.createElement('option');
            option.value = city.id;
            option.textContent = city.name;
            this.citySelect.appendChild(option);
        });

        // Habilitar select de ciudades
        this.citySelect.disabled = false;
    }

    clearCities() {
        this.citySelect.innerHTML = '';
        const defaultOption = document.createElement('option');
        defaultOption.value = '';
        defaultOption.textContent = this.options.placeholder;
        this.citySelect.appendChild(defaultOption);
        this.citySelect.disabled = true;
    }

    setDepartmentLoading(loading) {
        if (loading) {
            this.departmentSelect.innerHTML = `<option>${this.options.loadingText}</option>`;
            this.departmentSelect.disabled = true;
        } else {
            this.departmentSelect.disabled = false;
        }
    }

    setCityLoading(loading) {
        if (loading) {
            this.citySelect.innerHTML = `<option>${this.options.loadingText}</option>`;
            this.citySelect.disabled = true;
        } else {
            this.citySelect.disabled = false;
        }
    }

    showDepartmentError() {
        this.departmentSelect.innerHTML = `<option>Error al cargar departamentos</option>`;
    }

    showCityError() {
        this.citySelect.innerHTML = `<option>Error al cargar ciudades</option>`;
    }

    // Métodos públicos para obtener valores
    getSelectedDepartment() {
        return this.departmentSelect.value;
    }

    getSelectedCity() {
        return this.citySelect.value;
    }

    getSelectedDepartmentText() {
        const option = this.departmentSelect.options[this.departmentSelect.selectedIndex];
        return option ? option.textContent : '';
    }

    getSelectedCityText() {
        const option = this.citySelect.options[this.citySelect.selectedIndex];
        return option ? option.textContent : '';
    }

    // Método para establecer valores
    setValues(departmentId, cityId) {
        if (departmentId) {
            this.departmentSelect.value = departmentId;
            this.loadCitiesByDepartment(departmentId).then(() => {
                if (cityId) {
                    this.citySelect.value = cityId;
                }
            });
        }
    }

    // Método para limpiar selección
    clear() {
        this.departmentSelect.value = '';
        this.clearCities();
    }
}

// Función helper para crear un selector de ubicación
function createLocationSelector(departmentSelectId, citySelectId, options = {}) {
    return new LocationSelector(departmentSelectId, citySelectId, options);
}

// Exportar para uso en módulos
if (typeof module !== 'undefined' && module.exports) {
    module.exports = { LocationSelector, createLocationSelector };
}
