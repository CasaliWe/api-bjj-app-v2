<?php

namespace Models;

use Illuminate\Database\Eloquent\Model;

class PlanoJogo extends Model {
    protected $table = 'planos_jogo';
    protected $fillable = [
        'user_id', 'nome', 'descricao', 'categoria'
    ];

    public $timestamps = true;
    const CREATED_AT = 'criado_em';
    const UPDATED_AT = 'atualizado_em';

    public function usuario() {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function nodes() {
        return $this->hasMany(PlanoJogoNode::class, 'plano_id');
    }
}
