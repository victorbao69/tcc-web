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

    /**
     * Endereço final da imagem do produto.
     * - sem imagem: mostra uma imagem padrão bege com o nome do produto
     * - link da internet (começa com http): usa o link direto
     * - arquivo enviado: está em public/uploads/produtos, então usa asset()
     */
    public function getImagemUrlAttribute(): string
    {
        if (empty($this->urlimagem)) {
            return 'https://placehold.co/400x300/F7E9D4/5F3A1A?text=' . urlencode($this->nome);
        }

        if (str_starts_with($this->urlimagem, 'http')) {
            return $this->urlimagem;
        }

        return asset($this->urlimagem);
    }

    public function empresa()
    {
        return $this->belongsTo(Empresa::class, 'empresa_id');
    }

    public function pedidos()
    {
        return $this->hasMany(Pedido::class, 'produto_id');
    }
}
