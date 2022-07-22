<?php

namespace App;

use App\Traits\Logging;
use App\StockMaster;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class StockMove extends Model
{
    use HasFactory;
    protected $primaryKey = 'trans_id';
    // protected $table = 'stock_moves';
    public $timestamps = false;

    
    public function stockMaster()
	{
		return $this->belongsTo(StockMaster::class,'stock_id','stock_id');
	}
}
