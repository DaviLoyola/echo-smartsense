<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckAdmin
{
    
     // Trata a requisição que entra no sistema.
    public function handle(Request $request, Closure $next): Response
    {
        // 1. Verifica se o usuário está autenticado
        // 2. Verifica se a coluna 'role' dele no banco é 'admin'
        if (Auth::check() && Auth::user()->role === 'admin') {
            return $next($request); // Permissão concedida, segue em frente!
        }

        // Se falhar em qualquer um, barra e joga para os dispositivos com mensagem
        return redirect('/dispositivos')->with('error', 'Acesso restrito para administradores.');
    }
}