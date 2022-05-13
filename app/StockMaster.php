<?php

namespace App;

use App\Price;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class StockMaster extends Model
{
    use HasFactory;

    protected $table = 'stock_master';
    protected $primary_key = 'stock_id';
    public $incrementing = false;

    
    public function prices()
	{
		return $this->hasMany(Price::class,'stock_id','stock_id');
	}
}
