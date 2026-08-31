<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\Usuario;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required'
        ]);

        $user = Usuario::where('email', $request->email)->first();

        if ($user && Hash::check($request->password, $user->senha_hash)) {
            if ($user->status === 'inactive' || $user->status === 'blocked') {
                return back()->withErrors(['login' => 'Sua conta está inativa ou bloqueada.'])->withInput();
            }

            Auth::login($user);
            $user->ultimo_login = now();
            $user->save();

            return redirect('/dispositivos');
        }

        return back()->withErrors(['login' => 'Email ou senha incorretos.'])->withInput();
    }

    public function register(Request $request)
    {
        $request->validate([
            'nome'     => 'required|string|max:255',
            'email'    => 'required|email|unique:usuarios,email',
            'senha'    => 'required|min:6',
            'telefone' => 'nullable|string'
        ], [
            'email.unique' => 'Este email já está cadastrado no sistema.'
        ]);

        $user = Usuario::create([
            'nome'         => $request->nome,
            'email'        => $request->email,
            'senha_hash'   => Hash::make($request->senha),
            'telefone'     => $request->telefone,
            'role'         => 'user',
            'status'       => 'active',
            'ultimo_login' => now(),
            'notificacoes' => 'on',  // Explicitado o padrão no Laravel
            'permanent'    => false  // Explicitado o padrão no Laravel
        ]);

        Auth::login($user);

        return redirect('/dispositivos');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/login');
    }

    public function updateConfig(Request $request)
{
    $user = auth()->user();

    $request->validate([
        'nome'         => 'required|string|max:100',
        'email'        => 'required|email|max:100|unique:usuarios,email,' . $user->id,
        'notificacoes' => 'required|in:on,off'
    ], [
        'email.unique' => 'Este e-mail já está sendo utilizado por outro usuário.'
    ]);

    $user->nome = $request->nome;
    $user->email = $request->email;
    $user->notificacoes = $request->notificacoes;
    $user->save();

    return redirect()->back()->with('success', 'Configurações salvas com sucesso!');
}

}
