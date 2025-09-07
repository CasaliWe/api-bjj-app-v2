<?php

namespace Models;

use Illuminate\Database\Eloquent\Model;

class User extends Model {
    protected $table = 'user';
    protected $fillable = [
        'nome', 'email', 'senha', 'whatsapp', 'whatsapp_verificado', 'idade', 'peso', 'faixa',
        'imagem', 'instagram', 'tiktok', 'youtube', 'perfilPublico',
        'academia', 'cidade', 'estado', 'pais', 'estilo', 'competidor',
        'finalizacao', 'bio', 'primeiroAcesso', 'plano', 'vencimento',
        'exp', 'bjj_id'
    ];
    protected $hidden = ['senha'];
    public $timestamps = false;
}