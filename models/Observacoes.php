<?php

namespace Models;

use Illuminate\Database\Eloquent\Model;

class Observacoes extends Model {
    protected $table = 'observacoes';
    protected $fillable = [
        'titulo',
        'conteudo',
        'tag',
        'data',
        'usuario_id',
        'data_atualizacao'
    ];
    public $timestamps = false;
    
    public function usuario() {
        return $this->belongsTo(User::class, 'usuario_id');
    }
}