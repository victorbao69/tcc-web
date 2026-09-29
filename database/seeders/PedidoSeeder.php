<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Pedido;
use App\Models\User;
use App\Models\Produto;

class PedidoSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::first(); // ou inRandomOrder()->first()
        $produto = Produto::first();

        Pedido::create([
            'valor' => 500,
            'status' => 'pendente',

            // 🔥 CORRETO
            'user_id' => $user->id,

            'produto_id' => $produto->id,
        ]);
    }
}