<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;

/**
 * Banco limpo: só o administrador.
 * (Os seeders UserSeeder, EmpresaSeeder, ProdutoSeeder e PedidoSeeder continuam na
 * pasta, mas não são mais chamados aqui. Para dados de exemplo, rode-os à mão com
 * "php artisan db:seed --class=NomeDoSeeder".)
 */
class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Apaga as imagens enviadas antes (o banco novo não tem mais esses produtos)
        $pasta = public_path('uploads/produtos');
        if (File::isDirectory($pasta)) {
            foreach (File::files($pasta) as $arquivo) {
                File::delete($arquivo->getPathname());
            }
        }

        $this->call(AdminSeeder::class);
    }
}
