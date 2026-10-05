<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Um item do carrinho: "o cliente X quer N unidades do produto Y".
 */
class CarrinhoItem extends Model
{
    protected $table = 'carrinho_item';

    protected $fillable = [
        'user_id',
        'produto_id',
        'quantidade',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function produto()
    {
        return $this->belongsTo(Produto::class);
    }
}
