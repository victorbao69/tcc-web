<?php

namespace Database\Factories;
use App\Models\Empresa;
use App\Models\Produto;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProdutoFactory extends Factory
{

    protected $model = Produto::class;

    public function definition(): array
    {
        return [
            'nome' => $this->faker->words(2, true),
            'descricao' => $this->faker->sentence(5),
            'preco' => $this->faker->randomFloat(4, 50, 100, 1000, 5000),
            'categoria' => $this->faker->randomElement([
                'sofa',
                'armario',
                'cama',
                'mesa',
                'cadeira'
            ]),

            'status' => $this->faker->randomElement([
                'ativo',
                'vendido'
            ]),

           'urlimagem' => $this->faker->imageUrl(640, 480, 'furniture', true),

'empresa_id' => Empresa::inRandomOrder()->first()->id, 
        ];
    }
}