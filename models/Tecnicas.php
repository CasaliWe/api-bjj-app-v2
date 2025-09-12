<?php

namespace Models;

use Illuminate\Database\Eloquent\Model;

class Tecnicas extends Model {
    protected $table = 'tecnicas';
    protected $fillable = [
        'usuario_id', 'nome', 'categoria', 'posicao', 'passos', 'observacoes',
        'nota', 'video', 'video_url', 'video_poster', 'destacado', 'publica'
    ];
    
    public $timestamps = true;
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    // relacionamento
    public function usuario() {
        return $this->belongsTo(User::class, 'usuario_id');
    }

    public function posicaoDetalhes() {
        return $this->belongsTo(Posicoes::class, 'posicao', 'nome');
    }
}

