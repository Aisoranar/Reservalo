@extends('layouts.app')

@section('title', 'Crear Usuario - Super Admin')

@section('content')
<div class="container-fluid py-4">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h3 mb-0 text-gray-800">
                        <i class="fas fa-user-plus text-primary me-2"></i>
                        Crear Nuevo Usuario
                    </h1>
                    <p class="text-muted mb-0">Registra un nuevo usuario en el sistema</p>
                </div>
                <div>
                    <a href="{{ route('superadmin.users') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left me-1"></i>Volver
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-user-circle me-2"></i>Información del Usuario
                    </h6>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('superadmin.users.store') }}">
                        @csrf

                        <!-- Información Básica -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h6 class="text-primary mb-3">Información Básica</h6>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="name" class="form-label">Nombre Completo <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                           id="name" name="name" value="{{ old('name') }}" required>
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                                    <input type="email" class="form-control @error('email') is-invalid @enderror" 
                                           id="email" name="email" value="{{ old('email') }}" required>
                                    @error('email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row mb-4">
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="phone" class="form-label">Teléfono</label>
                                    <input type="text" class="form-control @error('phone') is-invalid @enderror" 
                                           id="phone" name="phone" value="{{ old('phone') }}">
                                    @error('phone')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="whatsapp" class="form-label">WhatsApp</label>
                                    <input type="text" class="form-control @error('whatsapp') is-invalid @enderror" 
                                           id="whatsapp" name="whatsapp" value="{{ old('whatsapp') }}">
                                    @error('whatsapp')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row mb-4">
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="password" class="form-label">Contraseña <span class="text-danger">*</span></label>
                                    <input type="password" class="form-control @error('password') is-invalid @enderror" 
                                           id="password" name="password" required>
                                    @error('password')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="password_confirmation" class="form-label">Confirmar Contraseña <span class="text-danger">*</span></label>
                                    <input type="password" class="form-control" 
                                           id="password_confirmation" name="password_confirmation" required>
                                </div>
                            </div>
                        </div>

                        <!-- Configuración de Cuenta -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h6 class="text-primary mb-3">Configuración de Cuenta</h6>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="role" class="form-label">Rol <span class="text-danger">*</span></label>
                                    <select class="form-select @error('role') is-invalid @enderror" 
                                            id="role" name="role" required>
                                        <option value="">Seleccionar rol</option>
                                        <option value="user" {{ old('role') === 'user' ? 'selected' : '' }}>Usuario</option>
                                        <option value="admin" {{ old('role') === 'admin' ? 'selected' : '' }}>Administrador</option>
                                        <option value="superadmin" {{ old('role') === 'superadmin' ? 'selected' : '' }}>Super Administrador</option>
                                    </select>
                                    @error('role')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="account_type" class="form-label">Tipo de Cuenta <span class="text-danger">*</span></label>
                                    <select class="form-select @error('account_type') is-invalid @enderror" 
                                            id="account_type" name="account_type" required>
                                        <option value="">Seleccionar tipo</option>
                                        <option value="individual" {{ old('account_type') === 'individual' ? 'selected' : '' }}>Individual</option>
                                        <option value="business" {{ old('account_type') === 'business' ? 'selected' : '' }}>Empresarial</option>
                                    </select>
                                    @error('account_type')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Información de Ubicación -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h6 class="text-primary mb-3">Información de Ubicación</h6>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group mb-3">
                                    <label for="address" class="form-label">Dirección</label>
                                    <input type="text" class="form-control @error('address') is-invalid @enderror" 
                                           id="address" name="address" value="{{ old('address') }}">
                                    @error('address')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row mb-4">
                            <div class="col-md-4">
                                <div class="form-group mb-3">
                                    <label for="city" class="form-label">Ciudad</label>
                                    <input type="text" class="form-control @error('city') is-invalid @enderror" 
                                           id="city" name="city" value="{{ old('city') }}">
                                    @error('city')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group mb-3">
                                    <label for="state" class="form-label">Estado/Departamento</label>
                                    <input type="text" class="form-control @error('state') is-invalid @enderror" 
                                           id="state" name="state" value="{{ old('state') }}">
                                    @error('state')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group mb-3">
                                    <label for="country" class="form-label">País</label>
                                    <input type="text" class="form-control @error('country') is-invalid @enderror" 
                                           id="country" name="country" value="{{ old('country', 'Colombia') }}">
                                    @error('country')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Información Personal -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h6 class="text-primary mb-3">Información Personal</h6>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="birth_date" class="form-label">Fecha de Nacimiento</label>
                                    <input type="date" class="form-control @error('birth_date') is-invalid @enderror" 
                                           id="birth_date" name="birth_date" value="{{ old('birth_date') }}">
                                    @error('birth_date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="gender" class="form-label">Género</label>
                                    <select class="form-select @error('gender') is-invalid @enderror" 
                                            id="gender" name="gender">
                                        <option value="">Seleccionar género</option>
                                        <option value="male" {{ old('gender') === 'male' ? 'selected' : '' }}>Masculino</option>
                                        <option value="female" {{ old('gender') === 'female' ? 'selected' : '' }}>Femenino</option>
                                        <option value="other" {{ old('gender') === 'other' ? 'selected' : '' }}>Otro</option>
                                    </select>
                                    @error('gender')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row mb-4">
                            <div class="col-12">
                                <div class="form-group mb-3">
                                    <label for="bio" class="form-label">Biografía</label>
                                    <textarea class="form-control @error('bio') is-invalid @enderror" 
                                              id="bio" name="bio" rows="3">{{ old('bio') }}</textarea>
                                    @error('bio')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Botones -->
                        <div class="row">
                            <div class="col-12">
                                <div class="d-flex justify-content-end gap-2">
                                    <a href="{{ route('superadmin.users') }}" class="btn btn-secondary">
                                        <i class="fas fa-times me-1"></i>Cancelar
                                    </a>
                                    <button type="submit" class="btn btn-success text-white fw-bold">
                                        <i class="fas fa-save me-1"></i>Crear Usuario
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
