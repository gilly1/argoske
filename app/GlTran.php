<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GlTran extends Model
{
    use HasFactory;
    
    protected $primary_key = 'counter';
    public $timestamps = false;
}
