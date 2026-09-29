<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Produto;

class ProdutoSeeder extends Seeder
{
    public function run(): void
    {
        Produto::create([
            'nome' => 'Sofá Conforto',
            'descricao' => 'Sofá muito confortável',
            'preco' => 1500.00, 
            'categoria' => 'sofa',
            'status' => 'ativo',
            'urlimagem' => '',
            'empresa_id' => 1
        ]);

        Produto::create([
            'nome' => 'Mesa de Madeira',
            'descricao' => 'Mesa resistente',
            'preco' => 800.00, 
            'categoria' => 'mesa',
            'status' => 'ativo',
            'urlimagem' => '',
            'empresa_id' => 1
        ]);
    }
}