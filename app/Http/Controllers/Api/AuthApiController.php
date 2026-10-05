<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

/**
 * Cadastro, login e conta do CLIENTE para o app mobile, com tokens do Sanctum.
 * (Empresas continuam usando só o site.)
 */
class AuthApiController extends Controller
{
    public function register(Request $request)
    {
        $dados = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:6'],
            'telefone' => ['nullable', 'string', 'max:30'],
            'endereco' => ['nullable', 'string', 'max:255'],
        ], [
            'email.unique' => 'Já existe uma conta com esse e-mail.',
            'password.min' => 'A senha precisa ter pelo menos 6 caracteres.',
        ]);

        $user = User::create([
            'name' => $dados['name'],
            'email' => $dados['email'],
            'password' => Hash::make($dados['password']),
            'tipousuario' => 'cliente',
            'telefone' => $dados['telefone'] ?? null,
            'endereco' => $dados['endereco'] ?? null,
        ]);

        $token = $user->createToken('app-flutter')->plainTextToken;

        return response()->json([
            'token' => $token,
            'cliente' => $this->dadosDoCliente($user),
        ], 201);
    }

    public function login(Request $request)
    {
        $dados = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        $user = User::where('email', $dados['email'])->first();

        if (! $user || ! Hash::check($dados['password'], $user->password)) {
            return response()->json(['message' => 'E-mail ou senha inválidos.'], 401);
        }

        if ($user->ehEmpresa()) {
            return response()->json(['message' => 'Contas de empresa só podem entrar pelo site.'], 403);
        }

        $token = $user->createToken('app-flutter')->plainTextToken;

        return response()->json([
            'token' => $token,
            'cliente' => $this->dadosDoCliente($user),
        ]);
    }

    public function logout(Request $request)
    {
        // Apaga só o token que está sendo usado nesta requisição
        $request->user()->currentAccessToken()->delete();

        return response()->json(['message' => 'Logout realizado com sucesso.'], 200);
    }

    public function me(Request $request)
    {
        return response()->json($this->dadosDoCliente($request->user()));
    }

    public function update(Request $request)
    {
        $user = $request->user();

        $dados = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . $user->id],
            'password' => ['nullable', 'string', 'min:6'],
            'telefone' => ['nullable', 'string', 'max:30'],
            'endereco' => ['nullable', 'string', 'max:255'],
        ], [
            'email.unique' => 'Já existe uma conta com esse e-mail.',
            'password.min' => 'A senha precisa ter pelo menos 6 caracteres.',
        ]);

        $user->name = $dados['name'];
        $user->email = $dados['email'];
        $user->telefone = $dados['telefone'] ?? null;
        $user->endereco = $dados['endereco'] ?? null;

        // A senha só muda se vier preenchida
        if (! empty($dados['password'])) {
            $user->password = Hash::make($dados['password']);
        }

        $user->save();

        return response()->json($this->dadosDoCliente($user));
    }

    public function destroy(Request $request)
    {
        $user = $request->user();

        // Os tokens e os pedidos precisam sair antes da conta
        // (os pedidos têm chave estrangeira "restrict")
        $user->tokens()->delete();
        $user->carrinhoItens()->delete();
        $user->pedidos()->delete();
        $user->delete();

        return response()->json(null, 204);
    }

    private function dadosDoCliente(User $user): array
    {
        return [
            'id' => $user->id,
            'nome' => $user->name,
            'email' => $user->email,
            'telefone' => $user->telefone ?? '',
            'endereco' => $user->endereco ?? '',
        ];
    }
}
