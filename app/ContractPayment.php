<?php

namespace App;

use App\Model\ContractItems;
use App\Traits\Approval;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;

class ContractPayment extends Model
{
    use HasFactory, HasRoles, Approval, Notifiable;

    protected $fillable = [
        'contract_items_id','month','intrest','principle','loading','installment','balance','paid','actual_balance'
			
    ];

    protected $dates = [
        'month'
			
    ];
    public function contractItems()
	{
		return $this->belongsTo(ContractItems::class,'contract_items_id');
	}
}
