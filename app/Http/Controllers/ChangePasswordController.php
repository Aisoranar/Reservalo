<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rules\Password;

class ChangePasswordController extends Controller
{
    /**
     * Mostrar el formulario de cambio de contraseña
     */
    public function show()
    {
        return view('auth.change-password');
    }

    /**
     * Actualizar la contraseña temporal
     */
    public function update(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'password' => ['required', 'confirmed', Password::defaults()],
        ]);

        $user = Auth::user();

        // Verificar que la contraseña actual sea correcta
        if (!Hash::check($request->current_password, $user->password)) {
            return back()->withErrors([
                'current_password' => 'La contraseña actual no es correcta.',
            ]);
        }

        // Actualizar la contraseña y quitar el flag de cambio obligatorio
        $user->update([
            'password' => Hash::make($request->password),
            'must_change_password' => false,
            'temp_password' => null, // Limpiar la contraseña temporal
        ]);

        return redirect()->route('dashboard')
            ->with('success', 'Contraseña actualizada exitosamente. Ya puedes usar el sistema normalmente.');
    }
}
