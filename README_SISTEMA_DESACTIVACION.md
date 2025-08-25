# 🔒 Sistema de Desactivación de Cuentas - Reservalo

## 📋 Descripción General

Este sistema implementa un mecanismo seguro y flexible para **desactivar cuentas de usuario** en lugar de eliminarlas permanentemente. Esto garantiza:

- **Seguridad de datos**: Los datos del usuario se mantienen seguros
- **Recuperabilidad**: Las cuentas pueden ser reactivadas en cualquier momento
- **Cumplimiento legal**: Cumple con regulaciones de protección de datos
- **Auditoría**: Mantiene un historial completo de acciones

## 🏗️ Arquitectura del Sistema

### 📊 Tablas de Base de Datos

#### 1. `users` (Tabla Principal)
- **Campos nuevos agregados**:
  - `is_active` (boolean): Estado de la cuenta
  - `deactivated_at` (timestamp): Fecha de desactivación
  - `deactivation_reason` (enum): Motivo de desactivación
  - `deactivation_notes` (text): Notas adicionales
  - `reactivated_at` (timestamp): Fecha de reactivación
  - `last_login_at` (timestamp): Último acceso
  - `last_login_ip` (string): IP del último acceso

#### 2. `deactivated_users` (Tabla de Respaldo)
- **Campos principales**:
  - Todos los datos del usuario original
  - Metadatos de desactivación
  - Información de auditoría
  - Datos relacionados (reservas, reseñas, etc.)

### 🔧 Modelos

#### `User` Model
```php
// Verificar estado de la cuenta
$user->isActive();           // true/false
$user->isDeactivated();      // true/false
$user->isSuspended();        // true/false

// Scopes útiles
User::active();              // Solo usuarios activos
User::deactivated();         // Solo usuarios desactivados
User::suspended();           // Solo usuarios suspendidos
```

#### `DeactivatedUser` Model
```php
// Verificar si puede ser reactivado
$deactivatedUser->canBeReactivated();

// Obtener estadísticas
$deactivatedUser->time_since_deactivation;
$deactivatedUser->deactivation_reason_text;
```

### 🚀 Servicios

#### `UserDeactivationService`
- **Métodos principales**:
  - `deactivateUser()`: Desactivar cuenta
  - `reactivateUser()`: Reactivar cuenta
  - `requestReactivation()`: Solicitar reactivación
  - `getDeactivationStats()`: Estadísticas del sistema
  - `cleanupOldDeactivatedUsers()`: Limpieza automática

## 🔄 Flujo de Desactivación

### 1. Usuario Solicita Desactivación
```
Usuario → Perfil → Desactivar Cuenta → Formulario → Confirmación
```

### 2. Proceso de Desactivación
```
1. Validar contraseña del usuario
2. Crear respaldo en tabla deactivated_users
3. Marcar usuario como inactivo
4. Cerrar sesión automáticamente
5. Redirigir a página principal
```

### 3. Reactivación de Cuenta
```
Usuario → Login → Verificar cuenta desactivada → Reactivar → Dashboard
```

## 🎯 Tipos de Desactivación

### 1. **Pausa Temporal** (`temporary_break`)
- **Descripción**: Para vacaciones o pausas cortas
- **Reactivación**: Inmediata
- **Datos**: Completamente preservados

### 2. **Desactivación Completa** (`user_request`)
- **Descripción**: Solicitud del usuario
- **Reactivación**: Requiere verificación
- **Datos**: Completamente preservados

### 3. **Suspensión por Políticas** (`policy_violation`)
- **Descripción**: Violación de términos de servicio
- **Reactivación**: Solo por administrador
- **Datos**: Preservados para auditoría

### 4. **Actividad Sospechosa** (`suspicious_activity`)
- **Descripción**: Comportamiento sospechoso detectado
- **Reactivación**: Solo por administrador
- **Datos**: Preservados para investigación

## 🛡️ Seguridad y Validaciones

### Middleware de Protección
```php
// Aplicar a rutas que requieren usuarios activos
Route::middleware(['auth', 'active.user'])->group(function () {
    // Rutas protegidas
});
```

### Validaciones del Sistema
- **Contraseña requerida** para desactivación
- **Verificación de estado** antes de acciones
- **Logs de auditoría** para todas las operaciones
- **Transacciones de base de datos** para consistencia

## 📊 Monitoreo y Estadísticas

### Comando de Limpieza
```bash
# Ver estadísticas (modo simulación)
php artisan users:cleanup-deactivated --dry-run

# Ejecutar limpieza real
php artisan users:cleanup-deactivated --days=365

# Limpiar usuarios de más de 6 meses
php artisan users:cleanup-deactivated --days=180
```

### Métricas Disponibles
- Total de usuarios desactivados
- Solicitudes de reactivación pendientes
- Usuarios que pueden ser reactivados
- Violaciones de políticas
- Actividad sospechosa

## 🔧 Configuración y Personalización

### Variables de Entorno
```env
# Tiempo de limpieza automática (días)
DEACTIVATED_USERS_CLEANUP_DAYS=365

# Habilitar limpieza automática
ENABLE_AUTO_CLEANUP=true
```

### Personalización de Motivos
```php
// En el modelo DeactivatedUser
protected $deactivationReasons = [
    'user_request' => 'Solicitud del usuario',
    'inactivity' => 'Inactividad',
    'policy_violation' => 'Violación de políticas',
    'suspicious_activity' => 'Actividad sospechosa',
    'temporary_hold' => 'Suspensión temporal',
    'other' => 'Otro motivo'
];
```

## 🚨 Casos de Uso Comunes

### 1. **Usuario Quiere "Eliminar" su Cuenta**
- **Solución**: Desactivar en lugar de eliminar
- **Beneficio**: Datos preservados, recuperación posible

### 2. **Pausa Temporal por Vacaciones**
- **Solución**: Desactivación temporal
- **Beneficio**: Reactivación inmediata al regreso

### 3. **Violación de Políticas**
- **Solución**: Suspensión con revisión manual
- **Beneficio**: Auditoría completa, decisión informada

### 4. **Actividad Sospechosa**
- **Solución**: Suspensión automática
- **Beneficio**: Protección del sistema y otros usuarios

## 📱 Interfaz de Usuario

### Formulario de Desactivación
- **Motivo requerido**: Dropdown con opciones predefinidas
- **Comentarios opcionales**: Campo de texto libre
- **Confirmación de contraseña**: Seguridad adicional
- **Información clara**: Beneficios y proceso de reactivación

### Página de Reactivación
- **Proceso simple**: Login normal
- **Confirmación automática**: Reactivación inmediata
- **Mensajes claros**: Estado y próximos pasos

## 🔍 Mantenimiento y Limpieza

### Limpieza Automática
```php
// Programar en el Kernel
protected function schedule(Schedule $schedule)
{
    // Limpiar usuarios desactivados antiguos semanalmente
    $schedule->command('users:cleanup-deactivated')
             ->weekly()
             ->sundays()
             ->at('02:00');
}
```

### Backup y Recuperación
- **Datos preservados**: En tabla separada
- **Soft deletes**: Protección adicional
- **Logs completos**: Trazabilidad de acciones

## 🚀 Beneficios del Sistema

### Para el Usuario
- ✅ **Seguridad**: Datos nunca se pierden
- ✅ **Flexibilidad**: Reactivación en cualquier momento
- ✅ **Transparencia**: Proceso claro y comprensible
- ✅ **Control**: Decisión reversible

### Para la Empresa
- ✅ **Cumplimiento**: Regulaciones de protección de datos
- ✅ **Retención**: Usuarios pueden regresar
- ✅ **Auditoría**: Historial completo de acciones
- ✅ **Seguridad**: Protección contra abusos

### Para el Sistema
- ✅ **Integridad**: Datos consistentes
- ✅ **Escalabilidad**: Manejo eficiente de cuentas
- ✅ **Monitoreo**: Métricas y estadísticas
- ✅ **Mantenimiento**: Limpieza automática

## 🔮 Futuras Mejoras

### Funcionalidades Planificadas
- [ ] **Notificaciones por email** para reactivación
- [ ] **Dashboard de administrador** para gestión
- [ ] **API endpoints** para integración externa
- [ ] **Reportes automáticos** de estadísticas
- [ ] **Integración con CRM** para seguimiento

### Optimizaciones Técnicas
- [ ] **Cache de estadísticas** para mejor rendimiento
- [ ] **Queue jobs** para operaciones pesadas
- [ ] **Eventos y listeners** para extensibilidad
- [ ] **Tests automatizados** para validación

## 📞 Soporte y Contacto

### Documentación Adicional
- [Guía de Usuario](docs/user-guide.md)
- [API Reference](docs/api-reference.md)
- [Troubleshooting](docs/troubleshooting.md)

### Equipo de Desarrollo
- **Mantenedor**: Equipo de Reservalo
- **Versión**: 1.0.0
- **Última actualización**: Agosto 2025

---

**⚠️ Importante**: Este sistema reemplaza completamente la funcionalidad de eliminación de cuentas. Todas las operaciones de "eliminación" ahora son desactivaciones seguras y reversibles.
