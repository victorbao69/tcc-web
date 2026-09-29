<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Produto; 

class DatabaseSeeder extends Seeder
{
    public function run(): void
    { 
        $this->call([
            UserSeeder::class,
            EmpresaSeeder::class,
            ProdutoSeeder::class,
            PedidoSeeder::class,
        ]); 
Produto::factory()->count(10)->create();
        
    }
}