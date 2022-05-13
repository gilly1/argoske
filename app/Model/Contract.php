<?php

namespace App\Model;

use Carbon\Carbon;
use App\Model\Status;
use App\Model\Inquiry;
use App\Traits\Approval;
use App\Helpers\AppHelper;
use App\Model\ContractItems;
use App\Model\AccountCustomer;
use App\Notifications\tellAdmin;
use App\Repositories\MainRepository;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
//import class

class Contract  extends Authenticatable
{
    use Notifiable, HasRoles, Approval;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'contract_number', 'name', 'document_number', 'sale_date', 'account_customer_id', 'inquiry_id', 'repayment_date', 'status'

    ];

    protected $dates = [
        'sale_date', 'repayment_date'

    ];

    // protected function asDateTime($value)
    // {
    //     return parent::asDateTime($value)->format('d-m-Y');
    // }

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



    public function account()
    {
        return $this->belongsTo(AccountCustomer::class, 'account_customer_id');
    }
    public function inquiry()
    {
        return $this->belongsTo(Inquiry::class, 'inquiry_id');
    }
    public function allStatus()
    {
        return $this->belongsTo(Status::class, 'status');
    }
    public function contract_items()
    {
        return $this->hasMany(ContractItems::class);
    }

    //relationships

}
