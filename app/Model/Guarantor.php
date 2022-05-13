<?php

namespace App\Model;

use App\Traits\Approval;
use App\Helpers\AppHelper;
use App\Notifications\tellAdmin;
use App\Repositories\MainRepository;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use App\Model\Gender;use App\Model\Identification;use App\Model\Nationality;
//import class

class Guarantor  extends Authenticatable
{
    use Notifiable, HasRoles, Approval;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'designation','department','station','section','last_name','other_names','d0b','gender_id','district_of_birth','identification_type_id','nationality_id','id_number','serial_number','date_of_issue','place_of_issue','district','division','location','sub_location','phone_number','email'
			
    ];

    protected $dates = [
        'd0b','date_of_issue'
			
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

    
    
    public function gender()
	{
		return $this->belongsTo(Gender::class,'gender_id');
	}
    public function identification()
	{
		return $this->belongsTo(Identification::class,'identification_type_id');
	}
    public function nationality()
	{
		return $this->belongsTo(Nationality::class,'nationality_id');
	}
    public function contract()
	{
		return $this->belongsTo(Contract::class,'contract_id');
	}

	//relationships

}
