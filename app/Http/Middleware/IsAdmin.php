<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class IsAdmin
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next)
    {
        $user = Auth::user();

        if (!$user || (int) $user->admin !== 1) {
            // Opcional: puedes redirigir al perfil con mensaje de error
            return redirect()
                ->route('perfil')
                ->with('error', 'No tienes permisos para acceder a la zona de administración.');
        }

        return $next($request);
    }
}
