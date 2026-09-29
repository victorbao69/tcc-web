<?php

namespace Database\Factories;

use App\Models\Empresa;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class EmpresaFactory extends Factory
{
    protected $model = Empresa::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->company(),
            'cnpj' => $this->faker->numerify('##.###.###/####-##'),
            'users_id' => User::inRandomOrder()->first()->id,
        ];
    }
}