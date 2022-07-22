<?php

namespace App\Model;

use App\Model\Approvers;
use App\Helpers\AppHelper;
use App\Model\ModelsToApprove;
use App\Model\ModelTobeApproved;
use App\Notifications\tellAdmin;
use App\Repositories\MainRepository;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
//import class

class ModelMapping  extends Authenticatable
{
    use Notifiable, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name','approver_model','approved_model'
			
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
            (new MainRepository(the_sub_domain().'model_mappings',ModelMapping::all()))->reCache();
        });
        self::updated(function ($model) {
            $changes = $model->isDirty() ? $model->getDirty() : false;

            if($changes)
            {
                $message = "updated ModelMapping </br>";
                foreach($changes as $attr => $value)
                {      
                    if($attr == 'updated_at')   
                    {
                        continue;
                    }
                    $message .= "$attr from <b>{$model->getOriginal($attr)} </b> to <b> {$model->$attr} </b>  </br>";

                }
                $message .= " by ".auth()->user()->name;
                $route = 'model_mappings/'.$model->id.'/edit';   
                \Notification::send(AppHelper::admin(),new tellAdmin('info',$message,$route));
                AppHelper::logs('critical',$message);
            }
            (new MainRepository(the_sub_domain().'model_mappings',ModelMapping::all()))->reCache(); 
        });
        self::deleted(function () {
            (new MainRepository(the_sub_domain().'model_mappings',ModelMapping::all()))->reCache(); 
        });
    }
    public function approvers()
	{
		return $this->hasMany(Approvers::class,'modelMapping_id')->orderBy('weight','asc');
	}
    public function approverStatuses()
	{
		return $this->hasMany(ApproverStatuses::class,'modelMapping_id','id');
	}
    public function modelsToApprove()
	{
		return $this->belongsTo(ModelsToApprove::class,'approver_model');
	}
    public function modelTobeApproved()
	{
		return $this->belongsTo(ModelTobeApproved::class,'approved_model');
	}
    //relationships

}
