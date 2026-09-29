<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Empresa extends Model
{
    use HasFactory;

    protected $table = 'empresa';

    protected $fillable = [
        'name',
        'cnpj',
        'users_id',
    ];

    public function produtos()
    {
        return $this->hasMany(Produto::class, 'empresa_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'users_id');
    }
}
