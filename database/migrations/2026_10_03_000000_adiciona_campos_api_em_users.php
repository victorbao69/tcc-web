<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('users', 'telefone')) {
            Schema::table('users', function (Blueprint $table) {
                $table->string('telefone', 30)->nullable();
            });
        }

        if (! Schema::hasColumn('users', 'endereco')) {
            Schema::table('users', function (Blueprint $table) {
                $table->string('endereco')->nullable();
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('users', 'telefone')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('telefone');
            });
        }

        if (Schema::hasColumn('users', 'endereco')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('endereco');
            });
        }
    }
};