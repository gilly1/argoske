<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SalesOrderDetail extends Model
{
    use HasFactory;
    public $timestamps = false;
    public $incrementing = false;
    protected $primary_key = 'order_no';
}
