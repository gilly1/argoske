<?php

namespace App;

use App\Model\Designation;
use App\Repositories\MainRepository;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use Notifiable, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email','designation_id', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_,verified_at' => 'datetime',
    ];

    protected $table = 'users2';

    use SoftDeletes;

    
    protected static function boot()
    {
        parent::boot();


        //affects perfomance as its called everytime this model is called
        // $mainRepository =  new MainRepository('user',User::where('id','!=',1)->select('id','name','email','designation_id')->get()); 

        self::created(function () {
            (new MainRepository(the_sub_domain().'users',User::where('id','!=',1)->select('id','name','email','designation_id')->get()))->reCache();
        });
        self::updated(function () {
            (new MainRepository(the_sub_domain().'users',User::where('id','!=',1)->select('id','name','email','designation_id')->get()))->reCache(); 
        });
        self::deleted(function () {
            (new MainRepository(the_sub_domain().'users',User::where('id','!=',1)->select('id','name','email','designation_id')->get()))->reCache(); 
        });
    }
    public function designation()
	{
		return $this->belongsTo(Designation::class,'designation_id','id');
	}

}
