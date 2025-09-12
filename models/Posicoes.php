<?php

namespace Models;

use Illuminate\Database\Eloquent\Model;

class Posicoes extends Model {
    protected $table = 'posicoes';
    protected $fillable = [
        'nome', 'usuario_id', 'padrao'
    ];
    
    public $timestamps = true;
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    // relacionamento
    public function usuario() {
        return $this->belongsTo(User::class, 'usuario_id');
    }

    public function tecnicas() {
        return $this->hasMany(Tecnicas::class, 'posicao', 'nome');
    }
}

