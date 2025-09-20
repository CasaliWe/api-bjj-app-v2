<?php

namespace Models;

use Illuminate\Database\Eloquent\Model;

class Plans extends Model {
    protected $table = 'plans';
    protected $fillable = [
        'months',
        'basePrice',
        'totalPrice',
        'discount',
        'label'
    ];
    
    public $timestamps = true;
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';
}

