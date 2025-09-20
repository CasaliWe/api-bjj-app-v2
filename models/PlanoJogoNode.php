<?php

namespace Models;

use Illuminate\Database\Eloquent\Model;

class PlanoJogoNode extends Model {
    protected $table = 'plano_jogo_nodes';
    protected $primaryKey = 'id';
    public $incrementing = false; // id é string (UUID/ULID)
    protected $keyType = 'string';

    protected $fillable = [
        'id', 'plano_id', 'parent_id', 'nome', 'tipo', 'descricao', 'tecnica_id',
        'categoria', 'posicao', 'passos', 'observacoes', 'video_url', 'video_poster', 'video', 'ordem'
    ];

    protected $casts = [
        'passos' => 'array',
        'observacoes' => 'array',
        'ordem' => 'integer',
    ];

    public $timestamps = true;
    const CREATED_AT = 'criado_em';
    const UPDATED_AT = 'atualizado_em';

    public function plano() {
        return $this->belongsTo(PlanoJogo::class, 'plano_id');
    }

    public function parent() {
        return $this->belongsTo(PlanoJogoNode::class, 'parent_id');
    }

    public function children() {
        return $this->hasMany(PlanoJogoNode::class, 'parent_id');
    }
}
