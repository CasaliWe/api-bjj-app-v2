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
        'exp', 'bjj_id', 'competicao_objetivo', 'treino_objetivo', 'tecnica_objetivo'
    ];
    protected $hidden = ['senha'];
    public $timestamps = false;
    
    public function competicoes() {
        return $this->hasMany(Competicoes::class, 'user_id');
    }
    
    public function treinos() {
        return $this->hasMany(Treino::class, 'usuario_id');
    }
    
    public function observacoes() {
        return $this->hasMany(Observacoes::class, 'usuario_id');
    }
    
    public function tokens() {
        return $this->hasMany(Token::class, 'user_id');
    }
    
    public function tecnicas() {
        return $this->hasMany(Tecnicas::class, 'usuario_id');
    }
    
    public function posicoes() {
        return $this->hasMany(Posicoes::class, 'usuario_id');
    }
}