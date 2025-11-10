<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        $user = Auth::user();

        // Se não estiver logado envia para página de login
        if(!$user){
            return redirect()->route('auth.login');
        }

        $roleName = strtolower($user->role?->name ?? '');
        $roles = array_map('strtolower', $roles);

        if (!in_array($roleName, $roles)) {
            abort(403, 'Acesso negado');
        }

        return $next($request);
    }
}
