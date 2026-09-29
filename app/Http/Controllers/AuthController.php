<?php

namespace App\Http\Controllers;

use App\Models\Empresa;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:6', 'confirmed'],
            'tipousuario' => ['required', 'in:cliente,empresa'],
            'empresa_nome' => ['required_if:tipousuario,empresa', 'nullable', 'string', 'max:255'],
            'empresa_cnpj' => ['required_if:tipousuario,empresa', 'nullable', 'string', 'max:20'],
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'tipousuario' => $request->tipousuario,
        ]);

        if ($request->tipousuario === 'empresa') {
            Empresa::create([
                'name' => $request->empresa_nome,
                'cnpj' => $request->empresa_cnpj,
                'users_id' => $user->id,
            ]);
        }

        Auth::login($user);

        return redirect($user->ehEmpresa() ? '/empresas' : '/produtos');
    }

    public function login(Request $request)
    {
        $credenciais = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (! Auth::attempt($credenciais, $request->boolean('lembrar'))) {
            return back()
                ->withErrors(['email' => 'Email ou senha incorretos.'])
                ->onlyInput('email');
        }

        $request->session()->regenerate();

        return redirect(auth()->user()->ehEmpresa() ? '/empresas' : '/produtos');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('welcome');
    }
}
