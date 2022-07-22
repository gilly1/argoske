<?php

namespace App\Model;

use App\Helpers\AppHelper;
use App\Notifications\tellAdmin;
use App\Repositories\MainRepository;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
//import class

class ModelTobeApproved  extends Authenticatable
{
    use Notifiable, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name','model'
			
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

    
    protected static function boot()
    {
        parent::boot();

        self::created(function () {
            (new MainRepository(the_sub_domain().'model_tobe_approveds',ModelTobeApproved::all()))->reCache();
        });
        self::updated(function ($model) {
            (new MainRepository(the_sub_domain().'model_tobe_approveds',ModelTobeApproved::all()))->reCache(); 
        });
        self::deleted(function () {
            (new MainRepository(the_sub_domain().'model_tobe_approveds',ModelTobeApproved::all()))->reCache(); 
        });
    }
    public function modelMapping()
	{
		return $this->hasMany(ModelMapping::class,'approved_model','id');
	}
    //relationships

}
