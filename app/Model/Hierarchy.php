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
//import class

class Hierarchy  extends Authenticatable
{
    use Notifiable, HasRoles, Approval;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name','description'
			
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
    //         (new MainRepository(the_sub_domain().'hierarchies',Hierarchy::all()))->reCache();
    //     });
    //     self::updated(function ($model) {
    //         $changes = $model->isDirty() ? $model->getDirty() : false;

    //         if($changes)
    //         {
    //             $message = "updated Hierarchy </br>";
    //             foreach($changes as $attr => $value)
    //             {      
    //                 if($attr == 'updated_at')   
    //                 {
    //                     continue;
    //                 }
    //                 $message .= "$attr from <b>{$model->getOriginal($attr)} </b> to <b> {$model->$attr} </b>  </br>";

    //             }
    //             $message .= " by ".auth()->user()->name;
    //             $route = 'hierarchies/'.$model->id.'/edit';   
    //             \Notification::send(AppHelper::admin(),new tellAdmin('info',$message,$route));
    //             AppHelper::logs('critical',$message);
    //         }
    //         (new MainRepository(the_sub_domain().'hierarchies',Hierarchy::all()))->reCache(); 
    //     });
    //     self::deleted(function () {
    //         (new MainRepository(the_sub_domain().'hierarchies',Hierarchy::all()))->reCache(); 
    //     });
    // }
    //relationships

}
