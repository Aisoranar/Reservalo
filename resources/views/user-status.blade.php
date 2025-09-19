@extends('layouts.app')

@section('title', 'Estado de Cuenta')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h4 class="mb-0">
                        <i class="fas fa-user-check me-2"></i>Estado de tu Cuenta
                    </h4>
                </div>
                <div class="card-body">
                    @if(auth()->user()->is_active)
                        <div class="alert alert-success">
                            <i class="fas fa-check-circle me-2"></i>
                            <strong>¡Cuenta Activa!</strong> Tu cuenta está activa y puedes usar todos los servicios.
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="card bg-light">
                                    <div class="card-body text-center">
                                        <i class="fas fa-home fa-2x text-primary mb-3"></i>
                                        <h5>Propiedades</h5>
                                        <p class="text-muted">Puedes crear y gestionar propiedades</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="card bg-light">
                                    <div class="card-body text-center">
                                        <i class="fas fa-calendar-check fa-2x text-success mb-3"></i>
                                        <h5>Reservas</h5>
                                        <p class="text-muted">Puedes hacer y gestionar reservas</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="alert alert-danger">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            <strong>Cuenta Desactivada</strong> Tu cuenta ha sido desactivada por un administrador.
                        </div>
                        
                        <div class="text-center">
                            <p class="text-muted">Para reactivar tu cuenta, contacta al administrador del sistema.</p>
                            <a href="mailto:admin@reservalo.com" class="btn btn-primary">
                                <i class="fas fa-envelope me-2"></i>Contactar Administrador
                            </a>
                        </div>
                    @endif
                    
                    <div class="mt-4">
                        <a href="{{ route('dashboard') }}" class="btn btn-outline-primary">
                            <i class="fas fa-arrow-left me-2"></i>Volver al Dashboard
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
