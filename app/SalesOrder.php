<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SalesOrder extends Model
{
    use HasFactory;
    // protected $table = 'stock_master';
    protected $primary_key = null;
    public $incrementing = false;
    public $timestamps = false;
}
