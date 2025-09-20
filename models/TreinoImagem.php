<?php

namespace Models;

use Illuminate\Database\Eloquent\Model;

class TreinoImagem extends Model {
    protected $table = 'treinos_imagens';
    protected $fillable = [
        'treino_id',
        'url'
    ];
    
    public $timestamps = true;
    const CREATED_AT = 'created_at';
    const UPDATED_AT = null;
    
    // Relacionamento com treino (uma imagem pertence a um treino)
    public function treino() {
        return $this->belongsTo(Treino::class, 'treino_id');
    }
}
