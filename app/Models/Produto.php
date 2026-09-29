<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Produto extends Model
{
    use HasFactory;

    protected $table = 'produto';

    protected $fillable = [
        'nome',
        'descricao',
        'preco',
        'categoria',
        'status',
        'urlimagem',
        'empresa_id',
    ];

    public function empresa()
    {
        return $this->belongsTo(Empresa::class, 'empresa_id');
    }

    public function pedidos()
    {
        return $this->hasMany(Pedido::class, 'produto_id');
    }
}
