<?php

namespace App\Http\Controllers\Logic;

use App\Helpers\AppHelper;
use App\Notifications\tellAdmin;
use Illuminate\Support\Facades\Notification;

class MainLogic
{

    public static  function table($subdomain,$list, $routeName,$permissions = [],$tableColumns=null,$tableSubColumns = null,$routeSubTable = null,$nestedRelationship = null,$subPermissions = [])
    {
        // if(in_array("create", $permission)) dd(2);
        if ( $permission = AppHelper::permissions('view_'.$routeName) )  return $permission;
        $view =  view('main/index')->with('title','All ' .ucwords(implode(' ',preg_split('/(?=[A-Z])/', ucfirst(str_replace('_', ' ', $routeName))))) )
                                ->with('table_name',ucwords(implode(' ',preg_split('/(?=[A-Z])/', ucfirst(str_replace('_', ' ', $routeName)))) ).' List')
                                ->with('route',$routeName)
                                ->with('collection',$list)
                                ->with('columns',$tableColumns)
                                ->with('subdomain',$subdomain);
        if(in_array("create", $permissions)) $view->with('canCreate','create_'.$routeName);
        if(in_array("edit", $permissions)) $view->with('canEdit','edit_'.$routeName);
        if(in_array("delete", $permissions)) $view->with('canDelete','delete_'.$routeName);
        if(in_array("view", $permissions)) $view->with('canView','view_'.$routeName);
        if(in_array("export", $permissions)) $view->with('canExport','export_'.$routeName);
        if(in_array("import", $permissions)) $view->with('canImport','import_'.$routeName);
        
        if($routeSubTable) $view->with('routeSubTable',$routeSubTable);
        if($tableSubColumns) $view->with('subColumns',$tableSubColumns);
        if($nestedRelationship) $view->with('nestedRelationship',$nestedRelationship);
        if(in_array("create", $subPermissions)) $view->with('canCreateSubTable','create_'.$routeSubTable);
        if(in_array("edit", $subPermissions)) $view->with('canEditSubTable','edit_'.$routeSubTable);
        if(in_array("delete", $subPermissions)) $view->with('canDeleteSubTable','delete_'.$routeSubTable);
        if(in_array("view", $subPermissions)) $view->with('canViewSubTable','view_'.$routeSubTable);

        return $view;
    }

    public static  function view($subdomain,$data,$routeName,$fields,$vue = null)
    {
        if($data){
            if ( $permission = AppHelper::permissions('edit_'.$routeName) )  return $permission;
        }else{
            if ( $permission = AppHelper::permissions('create_'.$routeName) )  return $permission;
        }
        $view = view('main/view')->with('data',$data)
                                ->with('title',ucwords(implode(' ',preg_split('/(?=[A-Z])/', ucfirst(str_replace('_', ' ', $routeName))))))
                                ->with('route',$routeName)
                                ->with('inputName',$fields)
                                ->with('subdomain',$subdomain);
        if($vue) $view->with('vue',$vue);

        return $view;
    }
    public static function show($subdomain,$data,$routeName,$fields)
    {
        if ( $permission = AppHelper::permissions('view_'.$routeName) )  return $permission;


        if(!$data) return redirect()->back()->with('error',$routeName.' not found') ;
        
        $view =  view('main/show')->with('data',$data)
                                ->with('title',ucwords(implode(' ',preg_split('/(?=[A-Z])/', ucfirst(str_replace('_', ' ', $routeName))))).' Details')
                                ->with('route',$routeName)
                                ->with('canEdit','edit_'.$routeName)
                                ->with('inputName',$fields)
                                ->with('subdomain',$subdomain);
        
        $modelName = get_class($data);
        if(method_exists($modelName,'approve')){
            [$superApproverStatus,$superApprover,$nextApproverStatus,$nextApprover,$approvers,$users,$isUserModelApproval,$allSuperApprover,$allApproved,$user_id,$modelToApprove] = $modelName::approve($data->id);
            
            if($isUserModelApproval){
                
            }
    
            $view->with('superApproverStatus',$superApproverStatus)
                ->with('superApprover',$superApprover)
                ->with('nextApproverStatus',$nextApproverStatus)
                ->with('nextApprover',$nextApprover)
                ->with('approvers',$approvers)
                ->with('user_id',$user_id)
                ->with('modelToApprove',$modelToApprove)
                ->with('users',$users)
                ->with('modelName',$modelName)
                ->with('modelNameId',$data->id)
                ->with('isUserModelApproval',$isUserModelApproval)
                ->with('allSuperApprover',$allSuperApprover)
                ->with('allApproved',$allApproved)
                ->with('include','inc/approval/approval');
        }
    
        return $view;
    }

    public static function save($subdomain,$data,$state,$log,$redirect,$routeName)
    {

        if($state == 'save')
        {
            self::log($data,$log,'save',$routeName);
        }else{
            self::log($data,$log,'update',$routeName);
        }

        return redirect($redirect);
    }
    public static function bulkSave($subdomain,$data,$state,$log,$redirect,$routeName)
    {

        if($state == 'save')
        {
            self::log($data,$log,'save',$routeName);
        }else{
            self::log($data,$log,'update',$routeName);
        }
    }

    public static function delete($subdomain,$data,$log,$redirect,$routeName)
    {        
        self::log($data,$log,'delete',$routeName);
        
        return redirect($redirect);
    }
    
    public static function log($data, $log, $state,$routeName)
    {
        // try to write it once in one class
        //message come from top - to get it customised
        //types of logs - log(default),email,notification option - supply emails
        // if(!$log){
        //     session()->flash('error','Something went wrong');
        // }

        // $route = $routeName.'/'.$data->id.'/edit';
        
        // foreach(array_keys($data->toArray()) as $keys){
        //     $checkIfId = strpos($keys, 'id');
        //     if($checkIfId !== false)
        //     {
        //         continue;
        //     }
        //     $name = $keys;
        //     break;
        // }
        
        // if($state == 'save'){
        
        //     $message = AppHelper::logfunction(ucwords($routeName).' ',$data->$name,'Added');
        //     Notification::send(AppHelper::admin(),new tellAdmin('info',$message,$route));
        //     AppHelper::logs('critical',$message);
                
        //     session()->flash('success','Updated Successfully');
        // }
        // elseif($state == 'update'){
            
        //     $message = AppHelper::logfunction(ucwords($routeName).' ',$data->$name,'Updated');
        //     Notification::send(AppHelper::admin(),new tellAdmin('info',$message,$route));
        //     AppHelper::logs('critical',$message);
                
        //     session()->flash('success','Added Successfully');
        // }
        // elseif($state == 'delete'){
        
        //     $message = AppHelper::logfunction(ucwords($routeName).' ',$data->$name,'Deleted');
        //     Notification::send(AppHelper::admin(),new tellAdmin('info',$message,$route));
        //     AppHelper::logs('critical',$message);
    
        //     session()->flash('success','Deleted Successfully');

        // }
    }
}
