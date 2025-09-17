<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mantenimiento - Reservalo</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100 min-h-screen flex items-center justify-center">
    <div class="max-w-md w-full bg-white rounded-lg shadow-lg p-8 text-center">
        <div class="mb-6">
            <i class="fas fa-tools text-6xl text-blue-600 mb-4"></i>
            <h1 class="text-3xl font-bold text-gray-800 mb-2">Sistema en Mantenimiento</h1>
            <p class="text-gray-600">
                {{ \App\Models\SystemSetting::get('maintenance_message', 'El sistema está en mantenimiento. Volveremos pronto.') }}
            </p>
        </div>
        
        <div class="space-y-4">
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                <i class="fas fa-info-circle text-blue-600 mb-2"></i>
                <p class="text-sm text-blue-800">
                    Estamos trabajando para mejorar tu experiencia. 
                    El sistema estará disponible nuevamente en breve.
                </p>
            </div>
            
            <div class="text-sm text-gray-500">
                <p>Si necesitas asistencia urgente, contáctanos:</p>
                <p class="font-semibold">soporte@reservalo.com</p>
            </div>
            
            <div class="pt-4">
                <button onclick="location.reload()" 
                        class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded-lg transition duration-200">
                    <i class="fas fa-refresh mr-2"></i>
                    Intentar de nuevo
                </button>
            </div>
        </div>
        
        <div class="mt-8 text-xs text-gray-400">
            <p>Reservalo © {{ date('Y') }} - Sistema de Reservas</p>
        </div>
    </div>
    
    <script>
        // Auto-refresh cada 30 segundos
        setTimeout(function() {
            location.reload();
        }, 30000);
    </script>
</body>
</html>
