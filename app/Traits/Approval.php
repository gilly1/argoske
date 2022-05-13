<?php
  
namespace App\Traits;

use App\User;
use App\Mail\sendMails;
use App\Helpers\AppHelper;
use App\Model\Designation;
use App\Model\ModelMapping;
use Illuminate\Support\Str;
use App\Model\ModelsToApprove;
use App\Model\ApproverStatuses;
use App\Model\ModelTobeApproved;
use App\Notifications\tellAdmin;
use App\Model\DesignationHierarchy;
use App\Repositories\MainRepository;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Notification;

/**
 * 
 */
trait Approval
{  
     
    protected static function boot()
    {
        parent::boot();
        

        $route = Str::snake(Str::pluralStudly( basename(str_replace('\\', '/', static::class)) )); 

        $models = ModelTobeApproved::with('modelMapping')->where('model',static::class)->first();
        $isToBeApproved = $models ?? false;


        self::created(function ($model) use ($route,$isToBeApproved) {
            if($isToBeApproved)
                self::setApproval($model);

                $changes = $model->isDirty() ? $model->getDirty() : false;
            
                if($changes)
                {
                    $message = "added {$route} </br>";
                    foreach($changes as $attr => $value)
                    {      
                        if($attr == 'updated_at' || $attr == 'created_at'|| $attr == 'id')   
                        {
                            continue;
                        }
                        $message .= "$attr  <b> {$model->$attr} </b>  </br>";
    
                    }
                    $message .= " by ".auth()->user()->name;
                    $logRoute = $route.'/'.$model->id.'/edit';   
                    \Notification::send(AppHelper::admin(),new tellAdmin('info',$message,$logRoute));
                    AppHelper::logs('critical',$message);
                }
                // (new MainRepository(the_sub_domain().$route,self::get()))->reCache();
            (new MainRepository('test'.$route,self::get()))->reCache();
        });
        self::updating(function ($model) use ($isToBeApproved) {
            if(!self::isEditable($model) && $isToBeApproved)
            {
                return false;
            }
        });
        self::deleting(function ($model) use ($isToBeApproved,$route) {
            if(!self::isEditable($model) && $isToBeApproved)
            {
                return false;
            }
            $message = "deleted {$route} </br>";
                foreach($model->original as $attr => $value)
                {      
                    if($attr == 'updated_at' || $attr == 'deleted_at' || $attr == 'created_at'|| $attr == 'id') 
                    {
                        continue;
                    }
                    $message .= "$attr  <b> {$model->$attr} </b>  </br>";

                }
                $message .= " by ".auth()->user()->name;
                $logRoute = $route.'/'.$model->id.'/edit';   
                \Notification::send(AppHelper::admin(),new tellAdmin('info',$message,$logRoute));
                AppHelper::logs('critical',$message);
        });
        self::updated(function ($model) use ($route,$isToBeApproved) {
            if($isToBeApproved)
                self::setApproval($model);

            $changes = $model->isDirty() ? $model->getDirty() : false;

            if($changes)
            {
                $message = "updated {$route} </br>";
                foreach($changes as $attr => $value)
                {      
                    if($attr == 'updated_at')   
                    {
                        continue;
                    }
                    $message .= "$attr from <b>{$model->getOriginal($attr)} </b> to <b> {$model->$attr} </b>  </br>";

                }
                $message .= " by ".auth()->user()->name;
                $logRoute = $route.'/'.$model->id.'/edit';   
                \Notification::send(AppHelper::admin(),new tellAdmin('info',$message,$logRoute));
                AppHelper::logs('critical',$message);
            }
            // (new MainRepository(the_sub_domain().$route,self::get()))->reCache(); 
            (new MainRepository('test'.$route,self::get()))->reCache();
        });
        self::deleted(function ($model) use ($route) {
            
            // (new MainRepository(the_sub_domain().$route,self::get()))->reCache(); 
            (new MainRepository('test'.$route,self::get()))->reCache();
        });
    }
    //relationships    

    
    public static function get()
    {
        return self::where('is_approved','1')->orWhere('is_approved',null)->get();
    }
    public static function isApproved()
    {
        return self::where('is_approved','1')->get();
    }
    public static function isRejected()
    {
        return self::where('is_approved','0')->get();
    }
    public static function pendingApproval()
    {
        return self::where('is_approved',null)->get();
    }
    public static function isUserModelApproval() : Bool
    {
        $modelMapping = self::currentModelMapping();
        $modelToApprove = self::modelToApprove($modelMapping->approver_model);
        return ($modelToApprove->model == 'App\User');
    }
    public static function approve($model_id) : Array
    {
        $modelMappingIds = self::allCurrentModelMapping();
        $approvers =  ApproverStatuses::with('users')->whereIn('modelMapping_id',$modelMappingIds)->where('approved_model_id',$model_id)->get();
        $users = User::withTrashed()->get();
        $isUserModelApproval =  self::isUserModelApproval();
        $modelMapping = self::modelMapping($approvers->first()->modelMapping_id ?? null);
        $modelToApprove = self::modelToApprove($modelMapping->approver_model ?? null);

        $user_id = null;
        if($modelToApprove){
            if($modelToApprove->model == 'Spatie\Permission\Models\Role')
            {
                $user_id = User::withTrashed()->with('roles')->where('id',auth()->user()->id)->first();
            }else
            {
                $user_id = User::withTrashed()->with('designation')->where('id',auth()->user()->id)->first();
            }

        $isUserModelApproval ?
            $users = $modelToApprove->model::withTrashed()->get() //Users::all();
            :
            (
                ($modelToApprove->model == 'Spatie\Permission\Models\Role') ?
                    $users = $modelToApprove->model::with('users')->get()
                    : $users = $modelToApprove->model::withTrashed()->with('users')->get()
            ); //designation::with('users)->get()
        }

        $superApproverStatus =  $approvers->where('super_admin',1)->first();

        $allSuperApprover = $users->where('id',$superApproverStatus->approver_model_id ?? null)->first();
        $isUserModelApproval ?
            $superApprover = $allSuperApprover
            :
            $superApprover = $allSuperApprover ? $allSuperApprover->users : $allSuperApprover;


        $allApprovedId =  $approvers->where('super_admin',0)->where('status',1)->pluck('approver_model_id');
        $allApproved = $users->whereIn('id',$allApprovedId);
        $nextApproverStatus =  $approvers->where('super_admin',0)->where('status',0)->first();
        $allNextApprover = $users->where('id',$nextApproverStatus->approver_model_id ?? null)->first();
        $isUserModelApproval ?
            $nextApprover = $allNextApprover
            :
            $nextApprover = $allNextApprover ? $allNextApprover->users : $allNextApprover;

        return [$superApproverStatus,$superApprover,$nextApproverStatus,$nextApprover,$approvers,$users,$isUserModelApproval,$allSuperApprover,$allApproved,$user_id,$modelToApprove];
    }

    private static function modelToBeApproved()
    {
        $modelToBeApproved = ModelTobeApproved::withTrashed()->where('model',static::class)->first(); 
        if(!$modelToBeApproved) {
            return false;
        }
        return $modelToBeApproved;
    }

    private static function modelToApprove($id)
    {
        $modelToApprove = ModelsToApprove::withTrashed()->where('id',$id)->first(); 
        if(!$modelToApprove) {
            return false;
        }
        return $modelToApprove;
    }

    private static function currentModelMapping()
    {
        $modelToBeApproved = self::modelToBeApproved();
        if(!$modelToBeApproved) {
            return false;
        }
        //remove trashed on saving
        return ModelMapping::withTrashed()->where('approved_model',$modelToBeApproved->id)->orderBy('id','desc')->select('id','approver_model','approved_model')->first();
    }
    private static function modelMapping($id)
    {
        //remove trashed on saving
        return ModelMapping::withTrashed()->where('id',$id)->orderBy('id','desc')->select('id','approver_model','approved_model')->first();
    }

    private static function allCurrentModelMapping()
    {
        $modelToBeApproved = self::modelToBeApproved();
        if(!$modelToBeApproved) {
            return false;
        }
        //remove trashed on saving
        return ModelMapping::withTrashed()->where('approved_model',$modelToBeApproved->id)->orderBy('id','desc')->select('id')->get();
    }

    public static function isEditable($model) : Bool
    {
        $modelMapping = self::currentModelMapping();
        if(!$modelMapping)
        {
            return false;
        }
        $approverStatuses = ApproverStatuses::where('modelMapping_id',$modelMapping->id)->where('approved_model_id',$model->id)->where('status',1)->first();
        if($approverStatuses)
        {
            return false;
        }
        return true;
    }

    public static function setApproval($model)
    {    
        $modelToBeApproved = self::modelToBeApproved();
        if(!$modelToBeApproved) {
            return false;
        }
        $modelMapping = ModelMapping::with('approvers')->where('approved_model',$modelToBeApproved->id)->first();  
        ApproverStatuses::where('modelMapping_id',$modelMapping->id)->where('approved_model_id',$model->id)->delete();
        
        if($modelMapping){
            $approverModel = self::modelToApprove($modelMapping->approver_model);
            if($approverModel->model == 'App\User' || $approverModel->model == 'Spatie\Permission\Models\Role'){
                $modelMapping_id = $modelMapping->id;
                foreach($modelMapping->approvers as $approver){
                    $approverStatus = new ApproverStatuses;
                    $approverStatus->modelMapping_id  = $modelMapping_id;
                    $approverStatus->approver_model_id  = $approver->approver_model_id;
                    $approverStatus->approved_model_id  = $model->id;
                    $approverStatus->weight  = $approver->weight;
                    $approverStatus->status  = 0;
                    $approverStatus->approved  = 0;
                    $approverStatus->super_admin  = $approver->super_approver;
                    $approverStatus->save();    
                } 
                
            }else{
                if($approverModel->model == 'App\Model\Designation'){
                    $allDesignation = Designation::withTrashed()->with('users')->orderBy('designation_id','desc')->get();
                    // $currentDesignation = $allDesignation->where('id',$model->user()->designation_id)->first();
                    // $currentDesignation = $allDesignation->where('id',3)->first();
                    $weight=0;
                    $designation_id = null;
                    foreach($allDesignation as $designation){
                        $approverStatus = new ApproverStatuses;
                        $approverStatus->modelMapping_id  = $modelMapping->id;
                        $approverStatus->approver_model_id  = $designation->id;
                        $approverStatus->approved_model_id  = $model->id; 
                        if($designation_id != $designation->id){
                            $designation_id = $designation->id;
                            $weight++;
                        }
                        $approverStatus->weight  = $weight;
                        $approverStatus->status  = 0;
                        $approverStatus->approved  = 0;
                        $approverStatus->super_admin  = 0;
                        $approverStatus->save();  
                        
                    }
                }elseif($approverModel->model == 'App\Model\Hierarchy'){

                    $allDesignation = DesignationHierarchy::withTrashed()->with('designation.users')->where('hierarchy_id',$modelMapping->hierarchy_id)->get();
                    $weight=0;
                    $designation_id = null;
                    foreach($allDesignation as $designation){
                        $approverStatus = new ApproverStatuses;
                        $approverStatus->modelMapping_id  = $modelMapping->id;
                        $approverStatus->approver_model_id  = $designation->id;
                        $approverStatus->approved_model_id  = $model->id; 
                        if($designation_id != $designation->id){
                            $designation_id = $designation->id;
                            $weight++;
                        }
                        $approverStatus->weight  = $weight;
                        $approverStatus->status  = 0;
                        $approverStatus->approved  = 0;
                        $approverStatus->super_admin  = 0;
                        $approverStatus->save();  
                        
                    }
                }
            }
           
            //first Approver

            if($approverModel->model == 'App\User' || $approverModel->model == 'Spatie\Permission\Models\Role')
            {
                $approver = $modelMapping->approvers->where('super_approver',0)->sortByDesc('weight')->last();                 
                if(count($modelMapping->approvers) < 1){
                    return false;
                }
                if($approverModel->model != 'App\User')
                {
                    $firstApprover = $approverModel->model::with('users')->where('id',$approver->approver_model_id)->get();
                    $users = $firstApprover->first()->users; 
                }else{
                    $firstApprover = $approverModel->model::where('id',$approver->approver_model_id)->get();
                    $users = $firstApprover;
                }
                
            }else{
                if($approverModel->model == 'App\Model\Designation'){
                    $approverStatus = ApproverStatuses::where('modelMapping_id',$modelMapping->id)->where('approved_model_id',$model->id)->orderBy('weight','asc')->first();
                    $designationUser = $allDesignation->where('id',$approverStatus->approver_model_id)->first();
                    $users = $designationUser->users;
                    
                }elseif($approverModel->model == 'App\Model\Hierarchy'){
                    $approverStatus = ApproverStatuses::where('modelMapping_id',$modelMapping->id)->where('approved_model_id',$model->id)->orderBy('weight','asc')->first();
                    $designationUser = $allDesignation->where('id',$approverStatus->approver_model_id)->first();
                    $users = $designationUser->designation->users;

                }
            }
            
            if(!$users){
                return;
            }
            $message = 'Approval Request';
            $route = Str::snake(Str::pluralStudly( basename(str_replace('\\', '/', static::class)) )); 
            $fullRoute = $route.'/'.$route.'/'.$model->id;
            $approvers = AppHelper::approver($users);
            Mail::to($approvers)->send(new sendMails(env('MAIL_FROM_ADDRESS', 'gillycode@gmail.com'),env('MAIL_FROM_NAME', 'Gilly Code'), $message, $message,$fullRoute));
            Notification::send($approvers,new tellAdmin('info',$message,$fullRoute));
            // send email to first approver             
        }
    }
}
