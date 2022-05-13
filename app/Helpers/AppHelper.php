<?php

namespace App\Helpers;

use auth;
use App\User;
use App\model\logs\log;
use App\Http\Controllers\objClass;
use Illuminate\Support\Facades\File;


class AppHelper
{ 
    //check for admins
    public static function admin()
    {
        $admins = array();
        if(AppHelper::loggedUser()->hasAnyRole('admin'))
        {
            return $admins;
        }else
        {
            $users = User::all();
            foreach($users as $user){
                if($user->hasAnyRole('admin')){
                    if($user->id != AppHelper::loggedUser()->id){
                        $admins[] = $user;
                    }
                }
            }
            return $admins;
        }
        return $admins;
    }
    //check for approvers
    public static function approver($users)
    {
        $approvers = array();
        foreach($users as $user){
            if($user->id != AppHelper::loggedUser()->id){
                $approvers[] = $user;
            }
        }
        return $approvers;
    }

    //check logged user
    public static function loggedUser()
    {
        return auth()->user();
    }
    
    //check for user notifications
    public static function notification()
    {
        $user = auth()->user();
        $notifications = $user->unreadNotifications;

        $messages = [];
         foreach ($notifications as $notification){
            if($notification->type == 'App\Notifications\tellAdmin'){
                $messages[] = [
                     "route" => $notification->data['route'],
                     "type" => $notification->data['type'],
                     "message" => $notification->data['message'],
                     "created_at" => $notification->created_at->format('M j,y h:i:s a')
                    ];
            }
         }

        return $messages;
    }

    // logs
    public static function logs($type, $message)
    {
        $user = auth()->user()->id;

        $logs=new log;
        $logs->type = $type;
        $logs->text = $message;
        $logs->user_id = $user;
        $logs->ip = \Request::ip();
        // $logs->mac = exec('getmac');
        $logs->save();
    }

    //save image
    public static function image($request,$image,$path=null)
    {
        if($request->hasFile($image))
        {
            //getting file name with extension
            $fileNamewithext=$request->file($image)->getClientOriginalName();
            //getting the file
            $fileName=pathInfo($fileNamewithext,PATHINFO_FILENAME);
            //getting the extension
            $extension=$request->file($image)->getClientOriginalExtension();
            //file name to store
            $fileNameToStore=$fileName.'_'.time().'.'.$extension;
            //upload image
            $path=$request->file($image)->move(storage_path("app/public"),$fileNameToStore);
        }else
        {
            $fileNameToStore = 'nophoto.jpeg';
        }

        return $fileNameToStore;
    }
    //delete image
    public static function deleteImage($request,$person,$image)
    {
        if($request)
        {
            if($request->hasFile($image))
            {
                if($person->$image != 'nophoto.jpeg')
                {
                    File::delete(public_path().'/storage/'.$person->image);
                }
            }
        }else
        {
            if($person->$image != 'nophoto.jpeg')
            {
                File::delete(public_path().'/storage/'.$person->image);
            }

        }
    }
    //log message    
    public static function logfunction($text, $content,$state)
    {
        $user = AppHelper::loggedUser()->name;
        return $message = $text.' - '.$content .' - '.$state .' by '. $user;
    }

    //check permission
    public static function permissions($permission)
    {
        if(!auth()->user()->can($permission))
        {
            session()->flash('error','Action Not Allowed');
            return redirect()->back();
        }
    }

    public static function inputValues($values)
    {
        $returnArray = [];
        $data = ['mainType','type','title','col','span','required','id','name','placeHolder','switch','loop','model'];
        
        foreach($values as $key => $value)
        {
            $i = 0;
            $key = new objClass();
            foreach($value as $val)
            {
                $key->{$data[$i]} = $val;
                $i++;
            }
            array_push($returnArray,$key);
        }
    
        return $returnArray;
        
    }

    public static function ModelToApprove()
    {
        $collection = collect(['name', 'age']);

        return $collection->combine(['George', 29]);
    }
}