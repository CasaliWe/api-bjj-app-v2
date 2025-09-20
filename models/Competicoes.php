<?php

namespace Models;

use Illuminate\Database\Eloquent\Model;

class Competicoes extends Model {
    protected $table = 'competicoes';
    protected $fillable = [
        'user_id', 'nome_evento', 'cidade', 'data', 'modalidade', 'colocacao', 'categoria',
        'numero_lutas', 'numero_vitorias', 'numero_derrotas', 'numero_finalizacoes',
        'observacoes', 'is_publico'
    ];
    public $timestamps = true;

    public function imagens() {
        return $this->hasMany(CompeticoesImagens::class, 'competicao_id');
    }
    
    public function user() {
        return $this->belongsTo(User::class, 'user_id');
    }
}