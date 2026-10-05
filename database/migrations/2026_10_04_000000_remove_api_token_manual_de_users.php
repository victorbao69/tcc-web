<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Limpeza: uma versão anterior da API guardava o token na própria tabela users
 * (coluna api_token). Agora quem cuida dos tokens é o Sanctum (tabela
 * personal_access_tokens), então a coluna antiga sai. Se ela não existe
 * (banco novo), não faz nada.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('users', 'api_token')) {
            return;
        }

        Schema::table('users', function (Blueprint $table) {
            $table->dropUnique(['api_token']);
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('api_token');
        });
    }

    public function down(): void
    {
        // Não há o que desfazer: a coluna antiga não é mais usada.
    }
};
