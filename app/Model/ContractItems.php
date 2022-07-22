<?php

namespace App\Model;

use App\StockMaster;
use App\Model\Contract;
use App\ContractPayment;
use App\Traits\Approval;
use App\Helpers\AppHelper;
use App\Notifications\tellAdmin;
use App\Repositories\MainRepository;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
//import class

class ContractItems  extends Authenticatable
{
    use Notifiable, HasRoles, Approval;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'contract_id','stock_id','quantity','installments','duration'
			
    ];

    protected $dates = [
        
			
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        //
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        //
    ];

    use SoftDeletes;

    
    
    public function contract()
	{
		return $this->belongsTo(Contract::class,'contract_id');
	}
    public function stockMaster()
	{
		return $this->belongsTo(StockMaster::class,'stock_id','stock_id');
	}
    public function contractPayment()
	{
		return $this->hasMany(ContractPayment::class,'contract_items_id');
	}

	//relationships

}
