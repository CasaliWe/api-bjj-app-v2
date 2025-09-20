<?php

namespace Models;

use Illuminate\Database\Eloquent\Model;

class CompeticoesImagens extends Model {
    protected $table = 'competicoes_imagens';
    protected $fillable = [
        'competicao_id',
        'url',
        'ordem'
    ];
    public $timestamps = true;

    public function competicao() {
        return $this->belongsTo(Competicoes::class, 'competicao_id');
    }
}