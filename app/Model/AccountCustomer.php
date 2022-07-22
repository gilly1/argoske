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
use App\Model\ProspectCustomer;
use App\Model\Gender;
use App\Model\IdentificationType;
//import class

class AccountCustomer  extends Authenticatable
{
    use Notifiable, HasRoles, Approval;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'work_number', 'designation', 'department', 'deo', 'section', 'station', 'gross_salary', 'net_salary', 'pin_no', 'town', 'phone_number', 'secondry_phone_number', 'email', 'address', 'back_account_number', 'prospect_customer_id', 'last_name', 'other_names', 'dob', 'place_of_birth', 'gender_id', 'identification_type_id', 'nationality_id', 'serial_number', 'id_number', 'date_of_issue', 'place_of_issue', 'district', 'division', 'location', 'sub_location'

    ];

    protected $dates = [
        'dob', 'date_of_issue'

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
    public function gender()
    {
        return $this->belongsTo(Gender::class, 'gender_id');
    }
    public function identification()
    {
        return $this->belongsTo(IdentificationType::class, 'identification_type_id');
    }
    public function nationality()
    {
        return $this->belongsTo(Nationality::class, 'nationality_id');
    }

    //relationships

}
