<?php

namespace App;

use App\SalesType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Price extends Model
{
    use HasFactory;

    
    public function salesType()
	{
		return $this->belongsTo(SalesType::class,'sales_type_id');
	}
}
