<?php

namespace App\Model;

use App\User;
use App\Traits\Approval;
use App\Repositories\MainRepository;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
//import class

class Designation  extends Authenticatable
{
    use Notifiable, HasRoles,Approval;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name','designation_id'
			
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

    
    // protected static function boot()
    // {
    //     parent::boot();

    //     self::created(function () {
    //         (new MainRepository(the_sub_domain().'designations',Designation::all()))->reCache();
    //     });
    //     self::updated(function () {
    //         (new MainRepository(the_sub_domain().'designations',Designation::all()))->reCache(); 
    //     });
    //     self::deleted(function () {
    //         (new MainRepository(the_sub_domain().'designations',Designation::all()))->reCache(); 
    //     });
    // }
    public function designation()
	{
		return $this->belongsTo(Designation::class,'designation_id');
	}
    public function users()
	{
		return $this->hasMany(User::class,'designation_id');
	}

	//relationships

}
