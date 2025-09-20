<?php

namespace Models;

use Illuminate\Database\Eloquent\Model;

class Contatos extends Model {
    protected $table = 'contatos';
    protected $fillable = [
        'email',
        'instagram_url',
        'instagram_handle',
        'youtube_url',
        'youtube_handle',
        'tiktok_url',
        'tiktok_handle',
    ];
    
    public $timestamps = true;
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';
}