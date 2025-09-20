<?php

namespace Models;

use Illuminate\Database\Eloquent\Model;

class Avaliacoes extends Model {
    protected $table = 'avaliacoes';
    protected $fillable = [
        'nome',
        'faixa',
        'texto',
    ];
    
    public $timestamps = true;
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';
}

