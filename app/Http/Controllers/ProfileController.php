<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use App\Services\UserDeactivationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;
use Illuminate\Support\Facades\Hash;

class ProfileController extends Controller
{
    protected $deactivationService;

    public function __construct(UserDeactivationService $deactivationService)
    {
        $this->deactivationService = $deactivationService;
    }

    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $request->user()->fill($request->validated());

        if ($request->user()->isDirty('email')) {
            $request->user()->email_verified_at = null;
        }

        $request->user()->save();

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    /**
     * Deactivate the user's account.
     */
    public function deactivate(Request $request): RedirectResponse
    {
        $request->validate([
            'deactivation_reason' => ['required', 'string', 'max:255'],
            'deactivation_notes' => ['nullable', 'string', 'max:1000'],
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        // Verificar que la cuenta no esté ya desactivada
        if ($user->isDeactivated()) {
            return Redirect::route('profile.edit')
                ->withErrors(['deactivation' => 'Tu cuenta ya está desactivada.'])
                ->withInput();
        }

        // Desactivar la cuenta usando el servicio
        $success = $this->deactivationService->deactivateUser(
            $user,
            $request->deactivation_reason,
            $request->deactivation_notes
        );

        if ($success) {
            // Cerrar sesión del usuario
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return Redirect::route('home')
                ->with('status', 'Tu cuenta ha sido desactivada exitosamente. Puedes reactivarla iniciando sesión nuevamente.');
        }

        return Redirect::route('profile.edit')
            ->withErrors(['deactivation' => 'Hubo un error al desactivar tu cuenta. Por favor, inténtalo de nuevo.'])
            ->withInput();
    }

    /**
     * Reactivate the user's account.
     */
    public function reactivate(Request $request): RedirectResponse
    {
        $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        // Buscar usuario desactivado
        $deactivatedUser = \App\Models\DeactivatedUser::where('email', $request->email)->first();

        if (!$deactivatedUser) {
            return Redirect::route('login')
                ->withErrors(['email' => 'No se encontró una cuenta desactivada con este email.']);
        }

        // Verificar contraseña
        if (!Hash::check($request->password, $deactivatedUser->password)) {
            return Redirect::route('login')
                ->withErrors(['password' => 'La contraseña proporcionada es incorrecta.']);
        }

        // Verificar si puede ser reactivado
        if (!$deactivatedUser->canBeReactivated()) {
            return Redirect::route('login')
                ->withErrors(['email' => 'Esta cuenta no puede ser reactivada automáticamente. Contacta a soporte.']);
        }

        // Reactivar la cuenta
        $success = $this->deactivationService->reactivateUser($deactivatedUser);

        if ($success) {
            // Iniciar sesión automáticamente
            $user = \App\Models\User::where('email', $request->email)->first();
            if ($user) {
                Auth::login($user);
                return Redirect::route('dashboard')
                    ->with('status', '¡Bienvenido de vuelta! Tu cuenta ha sido reactivada exitosamente.');
            }
        }

        return Redirect::route('login')
            ->withErrors(['email' => 'Hubo un error al reactivar tu cuenta. Por favor, contacta a soporte.']);
    }

    /**
     * Delete the user's account (mantener para casos especiales).
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        // Verificar que la cuenta no esté desactivada
        if ($user->isDeactivated()) {
            return Redirect::route('profile.edit')
                ->withErrors(['deactivation' => 'No puedes eliminar una cuenta desactivada. Primero reactívala.']);
        }

        // Desactivar primero (para mantener el respaldo)
        $this->deactivationService->deactivateUser($user, 'permanent_deletion', 'Eliminación permanente solicitada por el usuario');

        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}
