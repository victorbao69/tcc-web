<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Recado para um usuário, mostrado na próxima página que ele abrir
 * (ex: "o produto X foi excluído pelo administrador") e depois apagado.
 */
class Aviso extends Model
{
    protected $table = 'avisos';

    protected $fillable = [
        'user_id',
        'mensagem',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
