<?php

namespace Models;

use Illuminate\Database\Eloquent\Model;

class Treino extends Model {
    protected $table = 'treinos';
    protected $fillable = [
        'usuario_id',
        'numero_aula',
        'tipo',
        'dia_semana',
        'horario',
        'data',
        'observacoes',
        'is_publico'
    ];
    
    public $timestamps = true;
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';
    
    // Relacionamento com imagens (um treino tem muitas imagens)
    public function imagens() {
        return $this->hasMany(TreinoImagem::class, 'treino_id');
    }
    
    // Relacionamento com usuário (um treino pertence a um usuário)
    public function usuario() {
        return $this->belongsTo(User::class, 'usuario_id');
    }
}
