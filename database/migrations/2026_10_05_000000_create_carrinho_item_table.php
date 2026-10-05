<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Se a tabela já existir (banco que já recebeu esta migration), não faz nada
        if (Schema::hasTable('carrinho_item')) {
            return;
        }

        // Carrinho de compras guardado no servidor: uma linha por produto de cada cliente
        Schema::create('carrinho_item', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')
                ->constrained('users')
                ->cascadeOnDelete();

            $table->foreignId('produto_id')
                ->constrained('produto')
                ->cascadeOnDelete();

            $table->unsignedInteger('quantidade');

            $table->timestamps();

            // O mesmo produto não aparece duas vezes no carrinho do mesmo cliente
            $table->unique(['user_id', 'produto_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('carrinho_item');
    }
};
