<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'name' => 'Admin',
            'email' => 'admin@email.com',
            'password' => Hash::make('123456'),
            'tipousuario' => 'empresa',
        ]);

        User::create([
            'name' => 'Cliente Teste',
            'email' => 'cliente@email.com',
            'password' => Hash::make('123456'),
            'tipousuario' => 'cliente',
        ]);
    }
}