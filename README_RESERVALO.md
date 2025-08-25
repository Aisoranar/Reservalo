# 🏠 Reservalo - Plataforma de Reservas Turísticas

Reservalo es una plataforma web completa para la publicación, gestión y reserva de propiedades turísticas (fincas, hoteles, casas vacacionales, etc.). Desarrollada con Laravel 11, ofrece un sistema robusto de reservas con validación manual por administradores y notificaciones automáticas.

## ✨ Características Principales

### 🔐 Sistema de Usuarios
- **Visitantes**: Explorar propiedades sin registro
- **Usuarios registrados**: Crear reservas y gestionar perfil
- **Administradores**: Panel completo de gestión

### 🏡 Gestión de Propiedades
- CRUD completo de propiedades
- Múltiples imágenes por propiedad
- Categorización por tipo (casa, hotel, finca, apartamento)
- Servicios incluidos configurables
- Estados activo/inactivo

### 📅 Sistema de Reservas
- Calendario de disponibilidad dinámico
- Validación manual por administradores
- Estados: Pendiente, Aprobada, Rechazada
- Cálculo automático de precios
- Bloqueo de fechas para mantenimiento

### 📧 Notificaciones
- Correos automáticos al cambiar estado de reservas
- Plantillas HTML profesionales
- Preparado para integración con WhatsApp

### 📊 Panel Administrativo
- Dashboard con estadísticas en tiempo real
- Gráficos de ocupación y reservas
- Gestión de usuarios y propiedades
- Reportes exportables
- Filtros avanzados de búsqueda

## 🚀 Instalación

### Requisitos Previos
- PHP 8.1 o superior
- Composer
- MySQL/MariaDB
- Node.js y NPM (para Vite)

### 1. Clonar el Proyecto
```bash
git clone <url-del-repositorio>
cd Reservalo
```

### 2. Instalar Dependencias
```bash
composer install
npm install
```

### 3. Configuración del Entorno
```bash
cp .env.example .env
php artisan key:generate
```

### 4. Configurar Base de Datos
Editar el archivo `.env` con tus credenciales de base de datos:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=reservalo
DB_USERNAME=tu_usuario
DB_PASSWORD=tu_password
```

### 5. Ejecutar Migraciones
```bash
php artisan migrate
```

### 6. Crear Enlace Simbólico de Storage
```bash
php artisan storage:link
```

### 7. Ejecutar Seeders (Datos de Prueba)
```bash
php artisan db:seed
```

### 8. Compilar Assets
```bash
npm run build
```

### 9. Iniciar Servidor
```bash
php artisan serve
```

## 👥 Usuarios de Prueba

Después de ejecutar los seeders, tendrás acceso a:

### Administrador
- **Email**: admin@reservalo.com
- **Password**: password
- **Acceso**: Panel completo de administración

### Usuarios Regulares
- **Email**: juan@example.com, maria@example.com, carlos@example.com
- **Password**: password
- **Acceso**: Crear reservas y gestionar perfil

## 🗄️ Estructura de Base de Datos

### Tablas Principales
- **users**: Usuarios del sistema con roles
- **properties**: Propiedades turísticas
- **property_images**: Imágenes de las propiedades
- **reservations**: Reservas de usuarios
- **availability_blocks**: Bloqueos de fechas

### Relaciones
- Un usuario puede tener múltiples reservas
- Una propiedad puede tener múltiples imágenes
- Una propiedad puede tener múltiples reservas
- Las reservas tienen estados y fechas

## 🛣️ Rutas Principales

### Públicas
- `/` - Página principal con listado de propiedades
- `/propiedades` - Listado de propiedades con filtros
- `/propiedades/{id}` - Detalle de propiedad con calendario

### Usuarios Autenticados
- `/reservas` - Historial de reservas del usuario
- `/profile` - Edición de perfil

### Administradores
- `/admin/dashboard` - Panel principal
- `/admin/properties` - Gestión de propiedades
- `/admin/reservations` - Gestión de reservas
- `/admin/users` - Gestión de usuarios
- `/admin/reports` - Reportes y estadísticas

## 🔧 Configuración Adicional

### Configuración de Correo
Para las notificaciones por email, configurar en `.env`:
```env
MAIL_MAILER=smtp
MAIL_HOST=tu_servidor_smtp
MAIL_PORT=587
MAIL_USERNAME=tu_email
MAIL_PASSWORD=tu_password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@reservalo.com
MAIL_FROM_NAME="Reservalo"
```

### Configuración de Storage
Las imágenes se almacenan en `storage/app/public/properties/` y son accesibles públicamente a través de `/storage/properties/`.

## 📱 Características Técnicas

### Frontend
- **Bootstrap 5**: Framework CSS responsive
- **FullCalendar**: Calendario interactivo para disponibilidad
- **Font Awesome**: Iconos vectoriales
- **Chart.js**: Gráficos para el dashboard

### Backend
- **Laravel 11**: Framework PHP moderno
- **Eloquent ORM**: Gestión de base de datos
- **Middleware**: Autenticación y autorización
- **Validación**: Formularios robustos y seguros

### Seguridad
- **CSRF Protection**: Protección contra ataques CSRF
- **Middleware de Roles**: Control de acceso por rol
- **Validación de Datos**: Sanitización de entradas
- **Autenticación**: Sistema seguro de login

## 🚀 Despliegue en Producción

### Optimizaciones Recomendadas
```bash
# Cache de configuración
php artisan config:cache

# Cache de rutas
php artisan route:cache

# Cache de vistas
php artisan view:cache

# Optimizar autoloader
composer install --optimize-autoloader --no-dev
```

### Variables de Entorno de Producción
```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://tudominio.com
LOG_LEVEL=error
```

## 🔮 Próximas Funcionalidades

- [ ] Integración con WhatsApp Business API
- [ ] Sistema de pagos en línea
- [ ] Aplicación móvil nativa
- [ ] Sistema de reseñas y calificaciones
- [ ] Múltiples idiomas
- [ ] API REST para integraciones externas
- [ ] Sistema de notificaciones push
- [ ] Dashboard de anfitriones

## 🤝 Contribución

1. Fork el proyecto
2. Crea una rama para tu feature (`git checkout -b feature/AmazingFeature`)
3. Commit tus cambios (`git commit -m 'Add some AmazingFeature'`)
4. Push a la rama (`git push origin feature/AmazingFeature`)
5. Abre un Pull Request

## 📄 Licencia

Este proyecto está bajo la Licencia MIT. Ver el archivo `LICENSE` para más detalles.

## 📞 Soporte

- **Email**: soporte@reservalo.com
- **Documentación**: [Wiki del proyecto]
- **Issues**: [GitHub Issues]

## 🙏 Agradecimientos

- Laravel Team por el framework excepcional
- Bootstrap Team por el framework CSS
- FullCalendar por el componente de calendario
- Font Awesome por los iconos

---

**Desarrollado con ❤️ para la comunidad de viajeros y anfitriones**
