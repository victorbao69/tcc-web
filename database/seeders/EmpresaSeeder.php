<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Empresa;
use App\Models\User;

class EmpresaSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::where('email', 'admin@email.com')->first();

        if ($admin) {
            Empresa::factory()->create(['users_id' => $admin->id]);
        }
    }
}