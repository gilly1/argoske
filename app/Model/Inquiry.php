<?php

namespace App\Model;

use App\Model\Status;
use App\Traits\Approval;
use App\Helpers\AppHelper;
use App\Model\ProspectCustomer;
use App\Notifications\tellAdmin;
use App\Repositories\MainRepository;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
//import class

class Inquiry  extends Authenticatable
{
    use Notifiable, HasRoles, Approval;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'inquiry_number', 'prospect_customer_id', 'date', 'notes'

    ];

    protected $dates = [
        'date'

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



    public function prospect()
    {
        return $this->belongsTo(ProspectCustomer::class, 'prospect_customer_id');
    }
    public function shops()
    {
        return $this->belongsTo(Shops::class, 'shops_id');
    }
    public function allStatus()
    {
        return $this->belongsTo(Status::class, 'status');
    }
    public function inquiry_items()
    {
        return $this->hasMany(InquiryItems::class);
    }

    //relationships

}
