<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pedido extends Model
{
    protected $table = 'pedido';

    protected $fillable = [
        'valor',
        'status',
        'user_id',
        'produto_id',
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
