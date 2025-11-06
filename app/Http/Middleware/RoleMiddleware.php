<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  string  ...$roles  // Recibe la lista de roles permitidos (ej: 'Gestor', 'Administrador')
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        // 1. Verificar si el usuario está autenticado
        if (! $request->user()) {
            // Si no está autenticado, devuelve un error 401 (No autorizado)
            return response()->json([
                'message' => 'No autorizado. Se requiere autenticación.'
            ], 401);
        }

        // 2. Obtener los roles del usuario autenticado
        // NOTA: Usamos el Accessor 'role_names' que definimos en el modelo User.php
        $userRoles = $request->user()->role_names;

        // 3. Comprobar si el usuario tiene alguno de los roles requeridos
        // $roles es el array de roles permitidos (ej: ['Gestor', 'Administrador'])
        // Se intersecta los roles del usuario con los roles permitidos.
        // Si el resultado (intersect) no está vacío, significa que el usuario tiene acceso.
        $hasRole = collect($userRoles)->intersect($roles)->isNotEmpty();

        if ($hasRole) {
            // El usuario tiene al menos un rol permitido. Continúa con la solicitud.
            return $next($request);
        }

        // 4. Si no tiene el rol, denegar el acceso con un error 403 (Prohibido)
        return response()->json([
            'message' => 'Acceso Prohibido. El usuario no tiene el rol necesario.'
        ], 403);
    }
}