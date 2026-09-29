<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
  public function up(): void{
        Schema::create('produto', function (Blueprint $table) {
            $table->id();
            $table->string('nome', 100);
            $table->string('descricao', 100);
            $table->decimal('preco', 10, 2);
            $table->enum('categoria', [
                'sofa',
                'armario',
                'cama',
                'mesa',
                'cadeira'
            ]);

            $table->enum('status', [
                'ativo',
                'vendido'
            ]);

            $table->text('urlimagem');

            $table->unsignedBigInteger('empresa_id');
            $table->foreign('empresa_id')->references('id')->on('empresa')->onDelete('cascade');

            $table->timestamps();
        });
    }


    public function down(): void
    {
        Schema::dropIfExists('users');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('sessions');
    }
};
