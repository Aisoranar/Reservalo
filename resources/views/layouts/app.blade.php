<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <meta name="user-authenticated" content="{{ auth()->check() ? 'true' : 'false' }}">

        <title>@yield('title', \App\Models\SystemSetting::get('site_name', config('app.name', 'Laravel')))</title>

        <!-- Bootstrap CSS -->
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
        
        <!-- Font Awesome -->
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
        
        <!-- AOS Animation Library -->
        <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
        
        <!-- Estilos personalizados modernos -->
        <style>
            /* Variables CSS Globales */
            :root {
                --primary-color: #667eea;
                --secondary-color: #764ba2;
                --accent-color: #f093fb;
                --success-color: #4facfe;
                --warning-color: #ffd93d;
                --danger-color: #ff6b6b;
                --dark-color: #2c3e50;
                --light-color: #f8f9fa;
                --text-color: #495057;
                --border-color: #e9ecef;
                --shadow-light: 0 4px 20px rgba(0,0,0,0.1);
                --shadow-medium: 0 8px 30px rgba(0,0,0,0.15);
                --shadow-heavy: 0 15px 50px rgba(0,0,0,0.2);
                --gradient-primary: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
                --gradient-accent: linear-gradient(135deg, var(--accent-color) 0%, var(--success-color) 100%);
                --border-radius: 20px;
                --transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            }

            /* Reset y Base */
            * {
                box-sizing: border-box;
            }

            body {
                font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
                background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
                min-height: 100vh;
                margin: 0;
                padding: 0;
                line-height: 1.6;
                color: var(--text-color);
            }
            
            .navbar {
                box-shadow: 0 2px 10px rgba(0,0,0,0.1);
                background: linear-gradient(135deg, #667eea 0%, #764ba2 100%) !important;
                padding: 0.5rem 0;
            }
            
            .navbar-brand {
                font-weight: bold;
                font-size: 1.2rem;
                color: white !important;
                transition: all 0.3s ease;
            }
            
            .navbar-brand:hover {
                transform: scale(1.02);
                text-shadow: 0 0 15px rgba(255,255,255,0.4);
            }
            
            .navbar-nav .nav-link {
                color: rgba(255,255,255,0.9) !important;
                font-weight: 500;
                padding: 0.5rem 0.75rem !important;
                border-radius: 6px;
                margin: 0 0.15rem;
                transition: all 0.3s ease;
                position: relative;
                overflow: hidden;
            }

            /* ===== ESTILOS PARA PÁGINA DE PROPIEDADES ===== */
            
            /* Página Principal */
            .properties-page {
                background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
                min-height: 100vh;
            }

            /* Hero Section Ultra Moderno */
            .hero-section-modern {
                position: relative;
                min-height: 100vh;
                display: flex;
                align-items: center;
                overflow: hidden;
                background: var(--gradient-primary);
            }

            .hero-background {
                position: absolute;
                top: 0;
                left: 0;
                right: 0;
                bottom: 0;
                z-index: 1;
            }

            .hero-overlay {
                position: absolute;
                top: 0;
                left: 0;
                right: 0;
                bottom: 0;
                background: linear-gradient(135deg, rgba(102, 126, 234, 0.9) 0%, rgba(118, 75, 162, 0.9) 100%);
            }

            .hero-particles {
                position: absolute;
                top: 0;
                left: 0;
                right: 0;
                bottom: 0;
                background-image: 
                    radial-gradient(circle at 20% 80%, rgba(255, 255, 255, 0.1) 0%, transparent 50%),
                    radial-gradient(circle at 80% 20%, rgba(255, 255, 255, 0.1) 0%, transparent 50%),
                    radial-gradient(circle at 40% 40%, rgba(255, 255, 255, 0.05) 0%, transparent 50%);
                animation: float 20s ease-in-out infinite;
            }

            @keyframes float {
                0%, 100% { transform: translateY(0px) rotate(0deg); }
                50% { transform: translateY(-20px) rotate(5deg); }
            }

            .hero-content {
                position: relative;
                z-index: 2;
                text-align: center;
                color: white;
                padding: 2rem 0;
            }

            /* Badge Moderno */
            .hero-badge-modern {
                margin-bottom: 2rem;
            }

            .badge-content {
                position: relative;
                display: inline-flex;
                align-items: center;
                gap: 0.5rem;
                background: rgba(255, 255, 255, 0.2);
                backdrop-filter: blur(20px);
                border: 1px solid rgba(255, 255, 255, 0.3);
                padding: 0.75rem 1.5rem;
                border-radius: 50px;
                font-weight: 600;
                font-size: 0.9rem;
                transition: var(--transition);
            }

            .badge-content:hover {
                background: rgba(255, 255, 255, 0.3);
                transform: translateY(-2px);
            }

            .badge-glow {
                position: absolute;
                top: -2px;
                left: -2px;
                right: -2px;
                bottom: -2px;
                background: var(--gradient-accent);
                border-radius: 50px;
                z-index: -1;
                opacity: 0;
                transition: var(--transition);
            }

            .badge-content:hover .badge-glow {
                opacity: 0.3;
            }

            /* Título Principal */
            .hero-title {
                font-size: clamp(2rem, 8vw, 4rem);
                font-weight: 900;
                line-height: 1.1;
                margin-bottom: 1.5rem;
                text-shadow: 0 4px 20px rgba(0,0,0,0.3);
            }

            .title-line-1 {
                display: block;
                background: linear-gradient(135deg, #ffffff 0%, #f0f0f0 100%);
                -webkit-background-clip: text;
                -webkit-text-fill-color: transparent;
                background-clip: text;
            }

            .title-line-2 {
                display: block;
                background: var(--gradient-accent);
                -webkit-background-clip: text;
                -webkit-text-fill-color: transparent;
                background-clip: text;
                position: relative;
            }

            .title-underline {
                position: absolute;
                bottom: -10px;
                left: 50%;
                transform: translateX(-50%);
                width: 100px;
                height: 4px;
                background: var(--gradient-accent);
                border-radius: 2px;
                animation: underline 2s ease-in-out infinite;
            }

            @keyframes underline {
                0%, 100% { width: 100px; }
                50% { width: 150px; }
            }

            /* Subtítulo */
            .hero-subtitle {
                font-size: clamp(1rem, 4vw, 1.25rem);
                font-weight: 400;
                margin-bottom: 3rem;
                opacity: 0.9;
                text-shadow: 0 2px 10px rgba(0,0,0,0.2);
            }

            /* Búsqueda Moderna */
            .search-container-modern {
                max-width: 600px;
                margin: 0 auto 4rem;
            }

            .search-form-modern {
                position: relative;
            }

            .search-input-group {
                position: relative;
                display: flex;
                background: rgba(255, 255, 255, 0.95);
                backdrop-filter: blur(20px);
                border-radius: 50px;
                padding: 0.5rem;
                box-shadow: var(--shadow-heavy);
                border: 1px solid rgba(255, 255, 255, 0.3);
                transition: var(--transition);
            }

            .search-input-group:focus-within {
                transform: translateY(-2px);
                box-shadow: 0 20px 60px rgba(0,0,0,0.3);
            }

            .search-icon {
                display: flex;
                align-items: center;
                justify-content: center;
                width: 50px;
                color: var(--primary-color);
                font-size: 1.2rem;
            }

            .search-input {
                flex: 1;
                border: none;
                background: transparent;
                padding: 1rem 0;
                font-size: 1.1rem;
                color: var(--dark-color);
                outline: none;
            }

            .search-input::placeholder {
                color: var(--text-color);
                opacity: 0.7;
            }

            .search-button {
                background: var(--gradient-primary);
                border: none;
                color: white;
                padding: 1rem 2rem;
                border-radius: 50px;
                font-weight: 600;
                font-size: 1rem;
                display: flex;
                align-items: center;
                gap: 0.5rem;
                transition: var(--transition);
                cursor: pointer;
            }

            .search-button:hover {
                transform: scale(1.05);
                box-shadow: var(--shadow-medium);
            }

            /* Estadísticas Modernas */
            .hero-stats-modern {
                margin-top: 4rem;
            }

            .stats-grid {
                display: grid;
                grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
                gap: 1.5rem;
                margin-bottom: 3rem;
            }

            .stat-card-modern {
                background: rgba(255, 255, 255, 0.15);
                backdrop-filter: blur(20px);
                border: 1px solid rgba(255, 255, 255, 0.2);
                border-radius: var(--border-radius);
                padding: 1.5rem;
                text-align: center;
                transition: var(--transition);
                position: relative;
                overflow: hidden;
            }

            .stat-card-modern::before {
                content: '';
                position: absolute;
                top: 0;
                left: -100%;
                width: 100%;
                height: 100%;
                background: linear-gradient(90deg, transparent, rgba(255,255,255,0.1), transparent);
                transition: left 0.5s ease;
            }

            .stat-card-modern:hover::before {
                left: 100%;
            }

            .stat-card-modern:hover {
                transform: translateY(-10px);
                background: rgba(255, 255, 255, 0.25);
            }

            .stat-icon {
                width: 50px;
                height: 50px;
                background: var(--gradient-accent);
                border-radius: 50%;
                display: flex;
                align-items: center;
                justify-content: center;
                margin: 0 auto 1rem;
                font-size: 1.2rem;
                color: white;
                box-shadow: var(--shadow-medium);
            }

            .stat-content {
                color: white;
            }

            .stat-number {
                font-size: 2rem;
                font-weight: 900;
                margin-bottom: 0.5rem;
                text-shadow: 0 2px 10px rgba(0,0,0,0.3);
            }

            .stat-label {
                font-size: 0.8rem;
                font-weight: 600;
                opacity: 0.9;
                text-transform: uppercase;
                letter-spacing: 1px;
            }

            /* Precio Global Moderno */
            .pricing-card-modern {
                background: rgba(255, 255, 255, 0.2);
                backdrop-filter: blur(20px);
                border: 1px solid rgba(255, 255, 255, 0.3);
                border-radius: var(--border-radius);
                padding: 2rem;
                text-align: center;
                position: relative;
                overflow: hidden;
                max-width: 400px;
                margin: 0 auto;
            }

            .pricing-glow {
                position: absolute;
                top: -50%;
                left: -50%;
                width: 200%;
                height: 200%;
                background: var(--gradient-accent);
                opacity: 0.1;
                animation: rotate 10s linear infinite;
            }

            @keyframes rotate {
                0% { transform: rotate(0deg); }
                100% { transform: rotate(360deg); }
            }

            .pricing-content {
                position: relative;
                z-index: 2;
                color: white;
            }

            .pricing-icon {
                width: 50px;
                height: 50px;
                background: var(--gradient-accent);
                border-radius: 50%;
                display: flex;
                align-items: center;
                justify-content: center;
                margin: 0 auto 1rem;
                font-size: 1.2rem;
                color: white;
            }

            .pricing-label {
                font-size: 0.9rem;
                font-weight: 600;
                opacity: 0.9;
                margin-bottom: 0.5rem;
                text-transform: uppercase;
                letter-spacing: 1px;
            }

            .pricing-amount {
                font-size: 2.5rem;
                font-weight: 900;
                margin-bottom: 0.5rem;
                text-shadow: 0 2px 10px rgba(0,0,0,0.3);
            }

            .pricing-name {
                font-size: 1rem;
                opacity: 0.8;
            }

            /* Scroll Indicator */
            .scroll-indicator {
                position: absolute;
                bottom: 2rem;
                left: 50%;
                transform: translateX(-50%);
                z-index: 2;
            }

            .scroll-arrow {
                width: 50px;
                height: 50px;
                background: rgba(255, 255, 255, 0.2);
                backdrop-filter: blur(20px);
                border: 1px solid rgba(255, 255, 255, 0.3);
                border-radius: 50%;
                display: flex;
                align-items: center;
                justify-content: center;
                color: white;
                font-size: 1.2rem;
                animation: bounce 2s infinite;
                cursor: pointer;
                transition: var(--transition);
            }

            .scroll-arrow:hover {
                background: rgba(255, 255, 255, 0.3);
                transform: translateX(-50%) scale(1.1);
            }

            @keyframes bounce {
                0%, 20%, 50%, 80%, 100% { transform: translateX(-50%) translateY(0); }
                40% { transform: translateX(-50%) translateY(-10px); }
                60% { transform: translateX(-50%) translateY(-5px); }
            }

            /* Contenido Principal */
            .main-content {
                padding: 4rem 0;
                background: white;
            }

            /* Filtros Modernos */
            .filters-sidebar-modern {
                position: sticky;
                top: 2rem;
            }

            .filters-card {
                background: white;
                border-radius: var(--border-radius);
                box-shadow: var(--shadow-light);
                border: 1px solid var(--border-color);
                overflow: hidden;
                transition: var(--transition);
            }

            .filters-card:hover {
                box-shadow: var(--shadow-medium);
                transform: translateY(-2px);
            }

            .filters-header {
                background: var(--gradient-primary);
                color: white;
                padding: 1.5rem;
                display: flex;
                align-items: center;
                gap: 1rem;
            }

            .filters-icon {
                width: 40px;
                height: 40px;
                background: rgba(255, 255, 255, 0.2);
                border-radius: 50%;
                display: flex;
                align-items: center;
                justify-content: center;
                font-size: 1.1rem;
            }

            .filters-title {
                font-size: 1.25rem;
                font-weight: 700;
                margin: 0;
                flex: 1;
            }

            .filters-toggle {
                background: rgba(255, 255, 255, 0.2);
                border: none;
                color: white;
                width: 35px;
                height: 35px;
                border-radius: 50%;
                display: flex;
                align-items: center;
                justify-content: center;
                transition: var(--transition);
            }

            .filters-toggle:hover {
                background: rgba(255, 255, 255, 0.3);
            }

            .filters-body {
                padding: 2rem;
            }

            .filters-form {
                display: flex;
                flex-direction: column;
                gap: 2rem;
            }

            /* Header de Contenido Moderno */
            .content-header-modern {
                background: white;
                border-radius: var(--border-radius);
                padding: 2rem;
                margin-bottom: 2rem;
                box-shadow: var(--shadow-light);
                border: 1px solid var(--border-color);
            }

            .header-content {
                display: flex;
                align-items: center;
                justify-content: space-between;
                gap: 2rem;
            }

            .header-left {
                display: flex;
                align-items: center;
                gap: 1.5rem;
            }

            .header-icon-modern {
                width: 60px;
                height: 60px;
                background: var(--gradient-primary);
                border-radius: 50%;
                display: flex;
                align-items: center;
                justify-content: center;
                color: white;
                font-size: 1.5rem;
                box-shadow: var(--shadow-medium);
            }

            .header-text {
                color: var(--dark-color);
            }

            .header-title {
                font-size: 1.75rem;
                font-weight: 800;
                margin: 0 0 0.5rem 0;
                color: var(--dark-color);
            }

            .header-subtitle {
                font-size: 1rem;
                color: var(--text-color);
                margin: 0;
                display: flex;
                align-items: center;
                gap: 0.5rem;
            }

            .header-subtitle i {
                color: var(--primary-color);
            }

            /* Formulario de Ordenamiento Moderno */
            .sort-form-modern {
                display: flex;
                align-items: center;
            }

            .sort-container {
                display: flex;
                flex-direction: column;
                gap: 0.5rem;
            }

            .sort-label {
                font-size: 0.9rem;
                font-weight: 600;
                color: var(--text-color);
                display: flex;
                align-items: center;
                gap: 0.5rem;
            }

            .sort-label i {
                color: var(--primary-color);
            }

            .sort-select-modern {
                background: var(--light-color);
                border: 2px solid var(--border-color);
                border-radius: 12px;
                padding: 0.75rem 1rem;
                font-weight: 600;
                color: var(--dark-color);
                transition: var(--transition);
                min-width: 200px;
            }

            .sort-select-modern:focus {
                border-color: var(--primary-color);
                box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
                outline: none;
            }

            /* Grid de Propiedades Moderno */
            .properties-grid-modern {
                margin-bottom: 3rem;
            }

            .properties-container {
                display: grid;
                grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
                gap: 2rem;
            }

            /* Tarjeta de Propiedad Moderna */
            .property-card-modern {
                background: white;
                border-radius: var(--border-radius);
                overflow: hidden;
                box-shadow: var(--shadow-light);
                border: 1px solid var(--border-color);
                transition: var(--transition);
                position: relative;
                height: 100%;
                display: flex;
                flex-direction: column;
            }

            .property-card-modern:hover {
                transform: translateY(-8px);
                box-shadow: var(--shadow-heavy);
            }

            .property-image-modern {
                position: relative;
                height: 250px;
                overflow: hidden;
            }

            .property-img {
                width: 100%;
                height: 100%;
                object-fit: cover;
                transition: var(--transition);
            }

            .property-card-modern:hover .property-img {
                transform: scale(1.1);
            }

            .property-placeholder-modern {
                width: 100%;
                height: 100%;
                background: var(--light-color);
                display: flex;
                flex-direction: column;
                align-items: center;
                justify-content: center;
                color: var(--text-color);
                font-size: 3rem;
                opacity: 0.5;
            }

            .property-placeholder-modern span {
                font-size: 1rem;
                margin-top: 1rem;
            }

            .property-overlay {
                position: absolute;
                top: 0;
                left: 0;
                right: 0;
                bottom: 0;
                background: linear-gradient(135deg, rgba(0,0,0,0.1) 0%, rgba(0,0,0,0.3) 100%);
                opacity: 0;
                transition: var(--transition);
                display: flex;
                align-items: flex-start;
                justify-content: space-between;
                padding: 1rem;
            }

            .property-card-modern:hover .property-overlay {
                opacity: 1;
            }

            .favorite-btn-modern {
                background: rgba(255, 255, 255, 0.9);
                border: none;
                width: 40px;
                height: 40px;
                border-radius: 50%;
                display: flex;
                align-items: center;
                justify-content: center;
                color: var(--danger-color);
                font-size: 1.1rem;
                transition: var(--transition);
                cursor: pointer;
                position: relative;
            }

            .favorite-btn-modern:hover {
                background: var(--danger-color);
                color: white;
                transform: scale(1.1);
            }

            .favorite-btn-modern.active {
                background: var(--danger-color);
                color: white;
            }

            .login-indicator {
                position: absolute;
                top: -5px;
                right: -5px;
                font-size: 0.8rem;
            }

            .price-tag-modern {
                background: var(--gradient-primary);
                color: white;
                padding: 0.75rem 1.25rem;
                border-radius: 25px;
                text-align: center;
                box-shadow: var(--shadow-medium);
                margin-top: auto;
            }

            .price-amount {
                font-size: 1.25rem;
                font-weight: 800;
                margin-bottom: 0.25rem;
            }

            .price-period {
                font-size: 0.8rem;
                opacity: 0.9;
            }

            .quick-view-btn-modern {
                background: rgba(255, 255, 255, 0.9);
                border: none;
                width: 40px;
                height: 40px;
                border-radius: 50%;
                display: flex;
                align-items: center;
                justify-content: center;
                color: var(--primary-color);
                font-size: 1.1rem;
                transition: var(--transition);
                cursor: pointer;
            }

            .quick-view-btn-modern:hover {
                background: var(--primary-color);
                color: white;
                transform: scale(1.1);
            }

            /* Contenido de Propiedad Moderno */
            .property-content-modern {
                padding: 1.5rem;
                flex: 1;
                display: flex;
                flex-direction: column;
            }

            .property-title-modern {
                font-size: 1.25rem;
                font-weight: 700;
                color: var(--dark-color);
                margin: 0 0 0.75rem 0;
                line-height: 1.3;
            }

            .property-location-modern {
                color: var(--text-color);
                font-size: 0.9rem;
                margin-bottom: 1rem;
                display: flex;
                align-items: center;
                gap: 0.5rem;
            }

            .property-location-modern i {
                color: var(--primary-color);
                font-size: 0.8rem;
            }

            .property-description-modern {
                color: var(--text-color);
                font-size: 0.9rem;
                line-height: 1.6;
                margin-bottom: 1.5rem;
                flex: 1;
            }

            .property-features-modern {
                display: flex;
                gap: 1rem;
                margin-bottom: 1.5rem;
                flex-wrap: wrap;
            }

            .feature-item-modern {
                display: flex;
                align-items: center;
                gap: 0.5rem;
                background: var(--light-color);
                padding: 0.5rem 0.75rem;
                border-radius: 20px;
                font-size: 0.8rem;
                font-weight: 600;
                color: var(--text-color);
                transition: var(--transition);
            }

            .feature-item-modern:hover {
                background: var(--primary-color);
                color: white;
                transform: translateY(-2px);
            }

            .feature-item-modern i {
                color: var(--primary-color);
                font-size: 0.9rem;
            }

            .feature-item-modern:hover i {
                color: white;
            }

            .property-action-modern {
                margin-top: auto;
            }

            .btn-view-details-modern {
                display: flex;
                align-items: center;
                justify-content: center;
                gap: 0.5rem;
                background: var(--gradient-primary);
                color: white;
                text-decoration: none;
                padding: 1rem 1.5rem;
                border-radius: 12px;
                font-weight: 600;
                font-size: 0.9rem;
                transition: var(--transition);
                width: 100%;
            }

            .btn-view-details-modern:hover {
                transform: translateY(-2px);
                box-shadow: var(--shadow-medium);
                color: white;
                text-decoration: none;
            }

            .btn-view-details-modern i {
                transition: var(--transition);
            }

            .btn-view-details-modern:hover i {
                transform: translateX(3px);
            }

            /* Estado Vacío Moderno */
            .empty-state-modern {
                text-align: center;
                padding: 4rem 2rem;
                background: white;
                border-radius: var(--border-radius);
                box-shadow: var(--shadow-light);
                border: 1px solid var(--border-color);
            }

            .empty-state-icon-modern {
                font-size: 4rem;
                color: var(--text-color);
                opacity: 0.5;
                margin-bottom: 2rem;
            }

            .empty-state-title-modern {
                font-size: 1.5rem;
                font-weight: 700;
                color: var(--dark-color);
                margin-bottom: 1rem;
            }

            .empty-state-text-modern {
                color: var(--text-color);
                margin-bottom: 2rem;
                font-size: 1rem;
            }

            /* Paginación Moderna */
            .pagination-modern {
                display: flex;
                justify-content: center;
                gap: 0.5rem;
                margin-top: 3rem;
            }

            .page-link-modern {
                display: flex;
                align-items: center;
                justify-content: center;
                width: 45px;
                height: 45px;
                background: white;
                border: 2px solid var(--border-color);
                border-radius: 12px;
                color: var(--text-color);
                text-decoration: none;
                font-weight: 600;
                transition: var(--transition);
            }

            .page-link-modern:hover {
                background: var(--primary-color);
                border-color: var(--primary-color);
                color: white;
                transform: translateY(-2px);
                text-decoration: none;
            }

            .page-link-modern.active {
                background: var(--gradient-primary);
                border-color: var(--primary-color);
                color: white;
            }

            /* ===== RESPONSIVE DESIGN - MOBILE FIRST ===== */
            
            /* Mobile First - Base styles for mobile */
            @media (max-width: 576px) {
                .hero-title {
                    font-size: 2rem;
                }
                
                .hero-subtitle {
                    font-size: 1rem;
                    margin-bottom: 2rem;
                }
                
                .search-input-group {
                    flex-direction: column;
                    gap: 0.5rem;
                    padding: 0.75rem;
                }
                
                .search-button {
                    border-radius: 12px;
                    padding: 0.75rem 1.5rem;
                }
                
                .stats-grid {
                    grid-template-columns: repeat(2, 1fr);
                    gap: 1rem;
                }
                
                .stat-card-modern {
                    padding: 1rem;
                }
                
                .stat-number {
                    font-size: 1.5rem;
                }
                
                .stat-label {
                    font-size: 0.7rem;
                }
                
                .properties-container {
                    grid-template-columns: 1fr;
                    gap: 1.5rem;
                }
                
                .header-content {
                    flex-direction: column;
                    align-items: flex-start;
                    gap: 1rem;
                }
                
                .header-icon-modern {
                    width: 50px;
                    height: 50px;
                    font-size: 1.2rem;
                }
                
                .header-title {
                    font-size: 1.5rem;
                }
                
                .filters-sidebar-modern {
                    position: static;
                    margin-bottom: 2rem;
                }
                
                .filters-body {
                    padding: 1.5rem;
                }
                
                .property-features-modern {
                    flex-wrap: wrap;
                    gap: 0.5rem;
                }
                
                .feature-item-modern {
                    font-size: 0.7rem;
                    padding: 0.4rem 0.6rem;
                }
            }

            /* Tablet */
            @media (min-width: 577px) and (max-width: 768px) {
                .hero-title {
                    font-size: 2.5rem;
                }
                
                .stats-grid {
                    grid-template-columns: repeat(3, 1fr);
                }
                
                .properties-container {
                    grid-template-columns: repeat(2, 1fr);
                }
                
                .header-content {
                    flex-direction: row;
                    align-items: center;
                }
            }

            /* Desktop */
            @media (min-width: 769px) {
                .hero-title {
                    font-size: 3.5rem;
                }
                
                .stats-grid {
                    grid-template-columns: repeat(4, 1fr);
                }
                
                .properties-container {
                    grid-template-columns: repeat(3, 1fr);
                }
                
                .filters-sidebar-modern {
                    position: sticky;
                    top: 2rem;
                }
            }

            /* Large Desktop */
            @media (min-width: 1200px) {
                .properties-container {
                    grid-template-columns: repeat(4, 1fr);
                }
            }

            /* Scrollbar Personalizado */
            ::-webkit-scrollbar {
                width: 8px;
            }

            ::-webkit-scrollbar-track {
                background: var(--light-color);
            }

            ::-webkit-scrollbar-thumb {
                background: var(--gradient-primary);
                border-radius: 4px;
            }

            ::-webkit-scrollbar-thumb:hover {
                background: var(--secondary-color);
            }

            /* Efectos de Hover Mejorados */
            .property-card-modern::before {
                content: '';
                position: absolute;
                top: 0;
                left: 0;
                right: 0;
                bottom: 0;
                background: var(--gradient-primary);
                opacity: 0;
                transition: var(--transition);
                z-index: -1;
            }

            .property-card-modern:hover::before {
                opacity: 0.05;
            }

            /* Animaciones AOS */
            [data-aos] {
                pointer-events: none;
            }

            [data-aos].aos-animate {
                pointer-events: auto;
            }
                font-size: 0.9rem;
            }
            
            .navbar-nav .nav-link::before {
                content: '';
                position: absolute;
                top: 0;
                left: -100%;
                width: 100%;
                height: 100%;
                background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
                transition: left 0.5s;
            }
            
            .navbar-nav .nav-link:hover::before {
                left: 100%;
            }
            
            .navbar-nav .nav-link:hover {
                color: white !important;
                background-color: rgba(255,255,255,0.2);
                transform: translateY(-1px);
                box-shadow: 0 3px 10px rgba(0,0,0,0.15);
            }
            
            .navbar-nav .nav-link.active {
                color: white !important;
                background-color: rgba(255,255,255,0.3);
                font-weight: 600;
                box-shadow: 0 2px 8px rgba(0,0,0,0.15);
            }
            
            .dropdown-menu {
                border: none;
                box-shadow: 0 8px 25px rgba(0,0,0,0.12);
                border-radius: 10px;
                padding: 0.4rem 0;
                margin-top: 0.3rem;
                animation: fadeInDown 0.3s ease;
                min-width: 200px;
            }
            
            /* Estilos para submenús desplegables */
            .dropdown-submenu {
                position: relative;
            }
            
            .dropdown-submenu .dropdown-menu {
                top: 0;
                left: 100%;
                margin-top: -1px;
                margin-left: 0.125rem;
                min-width: 200px;
                border-radius: 8px;
                box-shadow: 0 8px 25px rgba(0,0,0,0.15);
                background: rgba(255,255,255,0.98);
                backdrop-filter: blur(15px);
                border: 1px solid rgba(0,0,0,0.08);
            }
            
            .dropdown-submenu:hover .dropdown-menu {
                display: block;
            }
            
            .dropdown-submenu .dropdown-toggle::after {
                transform: rotate(-90deg);
                margin-left: 0.5rem;
            }
            
            /* Estilos especiales para el menú de Super Admin */
            .navbar-nav .nav-link[href="#"]:hover {
                background-color: rgba(255,255,255,0.15);
            }
            
            .dropdown-menu .dropdown-header {
                background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                color: white;
                font-weight: 600;
                padding: 0.75rem 1rem;
                margin: -0.4rem 0 0.5rem 0;
                border-radius: 8px 8px 0 0;
            }
            
            .dropdown-menu .dropdown-item {
                padding: 0.5rem 1rem;
                font-size: 0.9rem;
                transition: all 0.2s ease;
                border-radius: 4px;
                margin: 0.1rem 0.5rem;
            }
            
            .dropdown-menu .dropdown-item:hover {
                background-color: rgba(102, 126, 234, 0.1);
                color: #667eea;
                transform: translateX(5px);
            }
            
            .dropdown-menu .dropdown-item i {
                width: 20px;
                text-align: center;
            }
            
            @keyframes fadeInDown {
                from {
                    opacity: 0;
                    transform: translateY(-8px);
                }
                to {
                    opacity: 1;
                    transform: translateY(0);
                }
            }
            
            .dropdown-item {
                padding: 0.5rem 1.2rem;
                transition: all 0.2s ease;
                border-radius: 6px;
                margin: 0 0.3rem;
                position: relative;
                font-size: 0.9rem;
            }
            
            .dropdown-item:hover {
                background-color: #f8f9fa;
                transform: translateX(3px);
                box-shadow: 0 2px 6px rgba(0,0,0,0.08);
            }
            
            .dropdown-header {
                font-weight: 600;
                color: #667eea !important;
                padding: 0.5rem 1.2rem;
                border-bottom: 1px solid #e9ecef;
                font-size: 0.8rem;
                text-transform: uppercase;
                letter-spacing: 0.5px;
            }
            
            .btn-primary {
                background: rgba(255,255,255,0.2);
                border: 1px solid rgba(255,255,255,0.3);
                color: white;
                font-weight: 500;
                padding: 0.5rem 1.2rem;
                border-radius: 6px;
                transition: all 0.3s ease;
                position: relative;
                overflow: hidden;
                font-size: 0.9rem;
            }
            
            .btn-primary::before {
                content: '';
                position: absolute;
                top: 0;
                left: -100%;
                width: 100%;
                height: 100%;
                background: linear-gradient(90deg, transparent, rgba(255,255,255,0.3), transparent);
                transition: left 0.5s;
            }
            
            .btn-primary:hover::before {
                left: 100%;
            }
            
            .btn-primary:hover {
                background: rgba(255,255,255,0.3);
                border-color: rgba(255,255,255,0.5);
                transform: translateY(-1px);
                box-shadow: 0 3px 10px rgba(0,0,0,0.15);
            }
            
            .navbar-toggler {
                border: 1px solid rgba(255,255,255,0.3);
                color: white;
                transition: all 0.3s ease;
                padding: 0.25rem 0.5rem;
            }
            
            .navbar-toggler:hover {
                border-color: rgba(255,255,255,0.6);
                transform: scale(1.02);
            }
            
            .navbar-toggler:focus {
                box-shadow: 0 0 0 0.2rem rgba(255,255,255,0.25);
            }
            
            /* Responsive mejorado */
            @media (max-width: 991.98px) {
                .navbar-collapse {
                    background: rgba(255,255,255,0.1);
                    backdrop-filter: blur(10px);
                    border-radius: 10px;
                    padding: 0.8rem;
                    margin-top: 0.8rem;
                    box-shadow: 0 4px 15px rgba(0,0,0,0.15);
                }
                
                .navbar-nav .nav-link {
                    margin: 0.2rem 0;
                    text-align: center;
                    border-radius: 6px;
                }
                
                .dropdown-menu {
                    background: rgba(255,255,255,0.95);
                    margin: 0.3rem 0;
                    box-shadow: none;
                }
                
                .navbar-brand {
                    font-size: 1.1rem;
                }
                
                .navbar-brand small {
                    display: none;
                }
            }
            
            /* Animaciones para elementos activos */
            .nav-link.active::after {
                content: '';
                position: absolute;
                bottom: 0;
                left: 50%;
                transform: translateX(-50%);
                width: 20px;
                height: 2px;
                background: white;
                border-radius: 1px;
                animation: slideIn 0.3s ease;
            }
            
            @keyframes slideIn {
                from {
                    width: 0;
                }
                to {
                    width: 20px;
                }
            }
            
            /* Mejoras para el logo */
            .navbar-brand i {
                transition: all 0.3s ease;
                font-size: 1.5rem;
            }
            
            .navbar-brand:hover i {
                transform: rotate(3deg) scale(1.05);
            }
            
            /* Ajustes adicionales para compactar */
            .container {
                padding-left: 1rem;
                padding-right: 1rem;
            }
            
            .navbar-nav {
                align-items: center;
            }
            
            .dropdown-toggle::after {
                margin-left: 0.3rem;
            }
        </style>

        <style>
        /* Estilos para el Dashboard del Admin */
        .admin-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 2rem;
            border-radius: 15px;
            margin-bottom: 2rem;
        }

        .admin-header .header-icon {
            width: 60px;
            height: 60px;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
        }

        .admin-header h1 {
            color: white;
            margin: 0;
        }

        .admin-header p {
            color: rgba(255, 255, 255, 0.8);
            margin: 0;
        }

        .header-actions .btn {
            border: 2px solid rgba(255, 255, 255, 0.3);
            color: white;
            transition: all 0.3s ease;
        }

        .header-actions .btn:hover {
            background: rgba(255, 255, 255, 0.1);
            border-color: rgba(255, 255, 255, 0.5);
            color: white;
        }

        .stat-card {
            background: white;
            border-radius: 15px;
            padding: 1.5rem;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            transition: all 0.3s ease;
            border: 1px solid #f8f9fa;
            height: 100%;
        }

        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.12);
        }

        .stat-card .stat-icon {
            width: 60px;
            height: 60px;
            border-radius: 15px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            color: white;
            margin-bottom: 1rem;
        }

        .stat-card .stat-icon.users {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }

        .stat-card .stat-icon.properties {
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
        }

        .stat-card .stat-icon.reservations {
            background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
        }

        .stat-card .stat-icon.pending {
            background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);
        }

        .stat-card .stat-icon.warning {
            background: linear-gradient(135deg, #fa709a 0%, #fee140 100%);
        }

        .stat-card .stat-icon.info {
            background: linear-gradient(135deg, #a8edea 0%, #fed6e3 100%);
            color: #333;
        }

        .stat-card .stat-number {
            font-size: 2rem;
            font-weight: 700;
            color: #2d3748;
            margin-bottom: 0.5rem;
        }

        .stat-card .stat-label {
            color: #718096;
            font-size: 0.9rem;
            font-weight: 500;
        }

        .deactivated-users-section .stat-card {
            border-left: 4px solid #f6ad55;
        }

        .quick-action-card {
            display: block;
            text-decoration: none;
            color: inherit;
            background: white;
            border-radius: 12px;
            padding: 1.5rem;
            border: 1px solid #e2e8f0;
            transition: all 0.3s ease;
            height: 100%;
        }

        .quick-action-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
            border-color: #cbd5e0;
            text-decoration: none;
            color: inherit;
        }

        .quick-action-card .action-icon {
            width: 50px;
            height: 50px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 1.2rem;
            margin-bottom: 1rem;
        }

        .quick-action-card h6 {
            color: #2d3748;
            margin-bottom: 0.5rem;
            font-weight: 600;
        }

        .quick-action-card p {
            color: #718096;
            font-size: 0.85rem;
            margin: 0;
        }

        .nav-links {
            display: flex;
            flex-direction: column;
            gap: 0.75rem;
        }

        .nav-links .nav-link {
            display: flex;
            align-items: center;
            padding: 0.75rem 1rem;
            background: #f7fafc;
            border-radius: 8px;
            color: #4a5568;
            text-decoration: none;
            transition: all 0.3s ease;
            border: 1px solid #e2e8f0;
        }

        .nav-links .nav-link:hover {
            background: #edf2f7;
            color: #2d3748;
            text-decoration: none;
            transform: translateX(5px);
        }

        .nav-links .nav-link i {
            width: 20px;
            margin-right: 0.75rem;
            color: #667eea;
        }

        /* Responsive adjustments */
        @media (max-width: 768px) {
            .admin-header {
                padding: 1.5rem;
                text-align: center;
            }

            .admin-header .header-actions {
                margin-top: 1rem;
                text-align: center;
            }

            .stat-card {
                padding: 1rem;
            }

            .stat-card .stat-number {
                font-size: 1.5rem;
            }

            .quick-action-card {
                padding: 1rem;
                text-align: center;
            }
        }
    </style>
</head>
    <body>
        @include('layouts.navigation')

        <!-- Mensajes de alerta -->
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show m-3" role="alert">
                <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show m-3" role="alert">
                <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if(session('warning'))
            <div class="alert alert-warning alert-dismissible fade show m-3" role="alert">
                <i class="fas fa-exclamation-triangle me-2"></i>{{ session('warning') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if($errors->any())
            <div class="alert alert-danger alert-dismissible fade show m-3" role="alert">
                <i class="fas fa-exclamation-triangle me-2"></i>
                <strong>Error:</strong> Por favor corrige los siguientes errores:
                <ul class="mb-0 mt-2">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <!-- Page Heading -->
        @if (isset($header))
            <header class="bg-white shadow">
                <div class="container py-3">
                    {{ $header }}
                </div>
            </header>
        @endif

        <!-- Page Content -->
        <main>
            @yield('content')
        </main>

        <!-- jQuery -->
        <script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
        
        <!-- Bootstrap JS -->
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
        
        <!-- Location Selector JS -->
        <script src="{{ asset('js/location-selector.js') }}"></script>
        
        <!-- AOS Animation Library -->
        <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
        
        <!-- Scripts personalizados -->
        @stack('scripts')
        
        <!-- Estilos personalizados -->
        @stack('styles')
        
        <!-- Script para submenús desplegables -->
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                // Manejar submenús desplegables
                const submenuToggles = document.querySelectorAll('.dropdown-submenu .dropdown-toggle');
                
                submenuToggles.forEach(function(toggle) {
                    toggle.addEventListener('click', function(e) {
                        e.preventDefault();
                        e.stopPropagation();
                        
                        const submenu = this.nextElementSibling;
                        const isOpen = submenu.style.display === 'block';
                        
                        // Cerrar todos los submenús
                        document.querySelectorAll('.dropdown-submenu .dropdown-menu').forEach(function(menu) {
                            menu.style.display = 'none';
                        });
                        
                        // Abrir/cerrar el submenú actual
                        if (!isOpen) {
                            submenu.style.display = 'block';
                        }
                    });
                });
                
                // Cerrar submenús al hacer clic fuera
                document.addEventListener('click', function(e) {
                    if (!e.target.closest('.dropdown-submenu')) {
                        document.querySelectorAll('.dropdown-submenu .dropdown-menu').forEach(function(menu) {
                            menu.style.display = 'none';
                        });
                    }
                });
            });

            // Función para mostrar toasts
            function showToast(message, type = 'info') {
                // Crear el contenedor de toast si no existe
                let toastContainer = document.getElementById('toast-container');
                if (!toastContainer) {
                    toastContainer = document.createElement('div');
                    toastContainer.id = 'toast-container';
                    toastContainer.className = 'toast-container position-fixed top-0 end-0 p-3';
                    toastContainer.style.zIndex = '9999';
                    document.body.appendChild(toastContainer);
                }

                // Crear el toast
                const toastId = 'toast-' + Date.now();
                const toastHtml = `
                    <div id="${toastId}" class="toast" role="alert" aria-live="assertive" aria-atomic="true">
                        <div class="toast-header">
                            <i class="fas fa-${type === 'success' ? 'check-circle text-success' : type === 'error' || type === 'danger' ? 'exclamation-circle text-danger' : 'info-circle text-info'} me-2"></i>
                            <strong class="me-auto">${type === 'success' ? 'Éxito' : type === 'error' || type === 'danger' ? 'Error' : 'Información'}</strong>
                            <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
                        </div>
                        <div class="toast-body">
                            ${message}
                        </div>
                    </div>
                `;
                
                toastContainer.insertAdjacentHTML('beforeend', toastHtml);
                
                // Mostrar el toast
                const toastElement = document.getElementById(toastId);
                const toast = new bootstrap.Toast(toastElement, {
                    autohide: true,
                    delay: 5000
                });
                toast.show();
                
                // Remover el toast del DOM después de que se oculte
                toastElement.addEventListener('hidden.bs.toast', function() {
                    toastElement.remove();
                });
            }
        </script>
        
        <!-- Script para animaciones y efectos modernos -->
        <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Inicializar AOS
            AOS.init({
                duration: 800,
                easing: 'ease-in-out',
                once: true,
                offset: 100
            });
            
            // Animación de contadores
            function animateCounters() {
                const counters = document.querySelectorAll('[data-count]');
                counters.forEach(counter => {
                    const target = parseInt(counter.getAttribute('data-count'));
                    const duration = 2000;
                    const increment = target / (duration / 16);
                    let current = 0;
                    
                    const timer = setInterval(() => {
                        current += increment;
                        if (current >= target) {
                            current = target;
                            clearInterval(timer);
                        }
                        counter.textContent = Math.floor(current).toLocaleString();
                    }, 16);
                });
            }
            
            // Ejecutar animación de contadores cuando sea visible
            const statsSection = document.querySelector('.hero-stats-modern');
            if (statsSection) {
                const observer = new IntersectionObserver((entries) => {
                    entries.forEach(entry => {
                        if (entry.isIntersecting) {
                            animateCounters();
                            observer.unobserve(entry.target);
                        }
                    });
                });
                observer.observe(statsSection);
            }
            
            // Efecto de partículas en el hero
            function createParticles() {
                const particlesContainer = document.querySelector('.hero-particles');
                if (!particlesContainer) return;
                
                for (let i = 0; i < 50; i++) {
                    const particle = document.createElement('div');
                    particle.style.position = 'absolute';
                    particle.style.width = Math.random() * 4 + 2 + 'px';
                    particle.style.height = particle.style.width;
                    particle.style.background = 'rgba(255, 255, 255, 0.1)';
                    particle.style.borderRadius = '50%';
                    particle.style.left = Math.random() * 100 + '%';
                    particle.style.top = Math.random() * 100 + '%';
                    particle.style.animation = `float ${Math.random() * 20 + 10}s linear infinite`;
                    particlesContainer.appendChild(particle);
                }
            }
            
            createParticles();
            
            // Smooth scroll para el indicador de scroll
            const scrollIndicator = document.querySelector('.scroll-arrow');
            if (scrollIndicator) {
                scrollIndicator.addEventListener('click', function() {
                    const mainContent = document.querySelector('.main-content');
                    if (mainContent) {
                        mainContent.scrollIntoView({ 
                            behavior: 'smooth',
                            block: 'start'
                        });
                    }
                });
            }
            
            // Toggle de filtros en móvil
            const filtersToggle = document.querySelector('.filters-toggle');
            if (filtersToggle) {
                filtersToggle.addEventListener('click', function() {
                    const icon = this.querySelector('i');
                    if (icon) {
                        icon.classList.toggle('fa-chevron-down');
                        icon.classList.toggle('fa-chevron-up');
                    }
                });
            }
        });
        </script>
    </body>
</html>
