<?php

namespace App\Model;

use App\User;
use App\Helpers\AppHelper;
use App\Model\ModelMapping;
use App\Notifications\tellAdmin;
use App\Repositories\MainRepository;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
//import class

class ApproverStatuses  extends Authenticatable
{
    use Notifiable, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'modelMapping_id','approver_model_id','approved_model_id','weight','status','approved','super_admin','reason'
			
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
            (new MainRepository(the_sub_domain().'approver_statuses',ApproverStatuses::all()))->reCache();
        });
        self::updated(function ($model) {
            $changes = $model->isDirty() ? $model->getDirty() : false;

            if($changes)
            {
                $message = "updated Approver Statuses </br>";
                foreach($changes as $attr => $value)
                {      
                    if($attr == 'updated_at')   
                    {
                        continue;
                    }
                    $message .= "$attr from <b>{$model->getOriginal($attr)} </b> to <b> {$model->$attr} </b>  </br>";

                }
                $message .= " by ".auth()->user()->name;
                $route = 'approver_statuses/'.$model->id.'/edit';   
                \Notification::send(AppHelper::admin(),new tellAdmin('info',$message,$route));
                AppHelper::logs('critical',$message);
            }
            (new MainRepository(the_sub_domain().'approver_statuses',ApproverStatuses::all()))->reCache(); 
        });
        self::deleted(function () {
            (new MainRepository(the_sub_domain().'approver_statuses',ApproverStatuses::all()))->reCache(); 
        });
    }
    public function modelMapping()
	{
		return $this->belongsTo(ModelMapping::class,'modelMapping_id');
	}
    public function users()
	{
		return $this->belongsTo(User::class,'user_id');
	}
    //relationships

}
