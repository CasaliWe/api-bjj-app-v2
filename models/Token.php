<?php

namespace Models;

use Illuminate\Database\Eloquent\Model;

class Token extends Model {
    protected $table = 'token';
    protected $fillable = [
        'user_id', 'valor'
    ];
    public $timestamps = true;
}