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
use App\Model\RoundMethod;
//import class

class Employer  extends Authenticatable
{
    use Notifiable, HasRoles, Approval;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'number', 'name', 'commission_rate', 'round_method_id', 'precision', 'retirement_age', 'accounts', 'debtor_no'

    ];

    protected $dates = [];

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



    public function round()
    {
        return $this->belongsTo(RoundMethod::class, 'round_method_id');
    }

    //relationships

}
