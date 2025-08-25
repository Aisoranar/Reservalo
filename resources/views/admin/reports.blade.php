@extends('layouts.app')

@section('title', 'Reportes - Admin')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h2">
            <i class="fas fa-chart-bar me-2"></i>Reportes y Análisis
        </h1>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-chart-line me-2"></i>Reservas por Mes
                    </h5>
                </div>
                <div class="card-body">
                    <p class="text-muted">Funcionalidad en desarrollo</p>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-chart-pie me-2"></i>Ocupación por Propiedad
                    </h5>
                </div>
                <div class="card-body">
                    <p class="text-muted">Funcionalidad en desarrollo</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
