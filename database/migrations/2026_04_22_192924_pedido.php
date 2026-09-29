<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pedido', function (Blueprint $table) {
            $table->id();

            $table->float('valor');

            $table->enum('status', [
                'pendente',
                'pago',
                'enviado',
                'concluido'
            ]);

            $table->foreignId('user_id')
                ->constrained('users')
                ->restrictOnDelete()
                ->cascadeOnUpdate();

            $table->foreignId('produto_id')
                ->constrained('produto')
                ->restrictOnDelete()
                ->cascadeOnUpdate();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pedido');
    }
};