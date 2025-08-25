# 🌍 Sistema de Ubicaciones para Colombia - Reservalo

Este documento explica cómo implementar y usar el sistema de ubicaciones de Colombia en la plataforma Reservalo.

## 📋 Características

- **33 Departamentos** de Colombia con códigos oficiales
- **201 Ciudades principales** organizadas por departamento
- **Selector dinámico** de departamentos y ciudades
- **API REST** para obtener ubicaciones
- **JavaScript reutilizable** para formularios

## 🗄️ Base de Datos

### Tabla `departments`
- `id` - Identificador único
- `name` - Nombre del departamento
- `code` - Código oficial (ej: ANT, CUN, VAC)
- `is_active` - Estado activo/inactivo
- `created_at`, `updated_at` - Timestamps

### Tabla `cities`
- `id` - Identificador único
- `name` - Nombre de la ciudad
- `department_id` - Referencia al departamento
- `is_active` - Estado activo/inactivo
- `created_at`, `updated_at` - Timestamps

## 🚀 Instalación

### 1. Migraciones
```bash
php artisan migrate
```

### 2. Seeders
```bash
php artisan db:seed --class=ColombianDepartmentsSeeder
php artisan db:seed --class=ColombianCitiesSeeder
```

### 3. Archivo JavaScript
El archivo `public/js/location-selector.js` debe estar disponible en tu proyecto.

## 📡 API Endpoints

### Obtener Departamentos
```http
GET /api/departments
```

**Respuesta:**
```json
[
    {
        "id": 1,
        "name": "Antioquia",
        "code": "ANT"
    },
    {
        "id": 2,
        "name": "Atlántico",
        "code": "ATL"
    }
]
```

### Obtener Ciudades por Departamento
```http
GET /api/cities/by-department?department_id=1
```

**Respuesta:**
```json
[
    {
        "id": 1,
        "name": "Medellín"
    },
    {
        "id": 2,
        "name": "Bello"
    }
]
```

### Buscar Ciudades
```http
GET /api/cities/search?query=med
```

**Respuesta:**
```json
[
    {
        "id": 1,
        "name": "Medellín",
        "department_id": 1,
        "department": {
            "id": 1,
            "name": "Antioquia"
        }
    }
]
```

## 🎯 Implementación en Formularios

### HTML Básico
```html
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
```

### JavaScript
```javascript
// Crear el selector
const locationSelector = createLocationSelector('department', 'city', {
    placeholder: 'Selecciona una opción'
});

// Escuchar cambios en la ciudad
document.getElementById('city').addEventListener('cityChanged', function(e) {
    console.log('Ciudad seleccionada:', e.detail.cityId);
});

// Obtener valores seleccionados
const departmentId = locationSelector.getSelectedDepartment();
const cityId = locationSelector.getSelectedCity();
const departmentText = locationSelector.getSelectedDepartmentText();
const cityText = locationSelector.getSelectedCityText();
```

## 🔧 Opciones de Configuración

```javascript
const options = {
    placeholder: 'Selecciona una opción',      // Texto por defecto
    loadingText: 'Cargando...',               // Texto durante carga
    noDataText: 'No hay datos disponibles'    // Texto sin datos
};

const locationSelector = createLocationSelector('department', 'city', options);
```

## 📱 Eventos Disponibles

### `cityChanged`
Se dispara cuando se selecciona una ciudad:

```javascript
document.getElementById('city').addEventListener('cityChanged', function(e) {
    const cityId = e.detail.cityId;
    // Tu código aquí
});
```

## 🎨 Personalización

### Estilos CSS
```css
/* Estilo para el select de departamentos */
#department {
    border: 2px solid #007bff;
}

/* Estilo para el select de ciudades */
#city {
    border: 2px solid #28a745;
}

/* Estilo para opciones deshabilitadas */
select:disabled {
    background-color: #f8f9fa;
    opacity: 0.6;
}
```

### Mensajes Personalizados
```javascript
const locationSelector = createLocationSelector('department', 'city', {
    placeholder: 'Elige una opción',
    loadingText: 'Espera un momento...',
    noDataText: 'No se encontraron resultados'
});
```

## 🧪 Ejemplo Completo

Visita `/ejemplo-ubicaciones` en tu aplicación para ver un ejemplo completo de implementación.

## 📊 Datos Incluidos

### Departamentos Principales
- **Antioquia** - Medellín, Bello, Itagüí, Envigado
- **Atlántico** - Barranquilla, Soledad, Malambo
- **Bolívar** - Cartagena, Magangué, Turbaco
- **Boyacá** - Tunja, Duitama, Sogamoso
- **Caldas** - Manizales, La Dorada, Chinchiná
- **Cauca** - Popayán, Santander de Quilichao
- **Córdoba** - Montería, Cereté, Sahagún
- **Cundinamarca** - Soacha, Facatativá, Zipaquirá
- **Distrito Capital** - Bogotá D.C.
- **Valle del Cauca** - Cali, Buenaventura, Palmira

### Y muchos más...

## 🔍 Solución de Problemas

### Error: "No se encontraron los elementos select"
- Verifica que los IDs `department` y `city` existan en tu HTML
- Asegúrate de que el JavaScript se ejecute después de que el DOM esté listo

### Error: "Error al cargar departamentos"
- Verifica que la ruta `/api/departments` esté funcionando
- Revisa la consola del navegador para errores de red

### Las ciudades no se cargan
- Verifica que la ruta `/api/cities/by-department` esté funcionando
- Asegúrate de que el `department_id` se esté enviando correctamente

## 📚 Recursos Adicionales

- [Documentación de Laravel](https://laravel.com/docs)
- [API de Ubicaciones de Colombia](https://www.dane.gov.co/)
- [Códigos de Departamentos](https://es.wikipedia.org/wiki/Organización_territorial_de_Colombia)

## 🤝 Contribuciones

Para agregar más ciudades o departamentos:

1. Edita `database/seeders/ColombianCitiesSeeder.php`
2. Agrega las nuevas ciudades en el array correspondiente
3. Ejecuta `php artisan db:seed --class=ColombianCitiesSeeder`

## 📄 Licencia

Este sistema de ubicaciones es parte de la plataforma Reservalo y está bajo la misma licencia.

---

**Desarrollado con ❤️ para Colombia**
