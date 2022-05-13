<?php

namespace App\Model;

use App\Model\Shops;
use App\Model\Employer;
use App\Traits\Approval;
use App\Helpers\AppHelper;
use App\Notifications\tellAdmin;
use App\Repositories\MainRepository;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
//import class

class ProspectCustomer  extends Authenticatable
{
    use Notifiable, HasRoles, Approval;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'work_number','full_name','phone_number','secondary_phone_number','email','employer_id','ability','town','notes'
			
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

    
    
    public function shops()
	{
		return $this->belongsTo(Shops::class,'shop_id');
	}
    public function employer()
	{
		return $this->belongsTo(Employer::class,'employer_id');
	}

	//relationships

}
