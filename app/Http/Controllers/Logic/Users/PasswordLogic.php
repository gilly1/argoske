<?php

namespace App\Http\Controllers\Logic\Users;

use App\User;
use App\Helpers\AppHelper;
use App\Notifications\tellAdmin;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;

class PasswordLogic
{
    const route = 'changePassword';
    const redirect = 'users/changePassword';

    public $model;

    function __construct(
        User $model
    ){
        $this->model = $model;
    }

    public  function table($subdomain)
    {
        $data = Auth::user();
        return view('main/view')->with('data',$data)
                                ->with('title','Change Password')
                                ->with('route',self::route)
                                ->with('inputName',self::fields($data))
                                ->with('subdomain',$subdomain);
    }

    public  function view($subdomain,$data)
    {
        $data = Auth::user();
        return view('main/view')->with('data',$data)
                                ->with('title','Change Password')
                                ->with('route',self::route)
                                ->with('inputName',self::fields($data))
                                ->with('subdomain',$subdomain);
    }

    public  function save($subdomain,$request,$data,$state)
    {
        // return $request->all();
        self::validated($request);

        
        $data = Auth::user();

        if (! Hash::check($request->get('old_password'), $data->getAuthPassword())) {
            session()->flash('error','Sorry, old password is incorrect');

            return back();
        }
        
        if(strcmp($request->password_confirmation, $request->password) != 0){
            //password and password confirmation need to be same
            session()->flash("error","Password Confirmation does not match.");
            return back();
        }
        if(strcmp($request->old_password, $request->password) == 0){
            session()->flash('error','Sorry, old password can\'t be same with new password');

            return back();
        }        
        $data->password = Hash::make($request->password);
        $log = $data->save();
        $data->syncPermissions($request->roles);

        self::log($data,$log,'update');

        return redirect(self::redirect);
    }

    public  function validated($request){
        // $this->validate($request,[
        //     'name' => ['required', 'string', 'max:255'],
        //     'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
        // ]);
    }
    
    public  function log($data, $log, $state)
    {
        if(!$log){
            session()->flash('error','Something went wrong');
        }

        $route = self::route.'/'.$data->id.'/edit';
        
        if($state == 'save'){
        
            $message = AppHelper::logfunction('Password for ',$data->name,'Added');
            Notification::send(AppHelper::admin(),new tellAdmin('info',$message,$route));
            AppHelper::logs('critical',$message);
                
            session()->flash('success','Added Successfully');
        }
        elseif($state == 'update'){
            
            $message = AppHelper::logfunction('Password for',$data->name,'Updated');
            Notification::send(AppHelper::admin(),new tellAdmin('info',$message,$route));
            AppHelper::logs('critical',$message);
                
            session()->flash('success','Updated Successfully');
        }
        elseif($state == 'delete'){
        
            $message = AppHelper::logfunction('Password for ',$data->name,'Deleted');
            Notification::send(AppHelper::admin(),new tellAdmin('info',$message,$route));
            AppHelper::logs('critical',$message);
    
            session()->flash('success','Deleted Successfully');

        }
    }


    public  function fields($forEdit)
    {   

        $values = [
            'oldPassword'=>
            [
                'password','password','Old Password',12,true,true,'old_password','old_password','Old Password'
            ],
            'Password'=>
            [
                'password','password','Password',12,true,true,'password','password','New Password'
            ],
            'password_confirmation'=>
            [
                'password','password','Password Confirmation',12,true,true,'password_confirmation','password_confirmation','Confirm New Password'
            ]
        ];

        return AppHelper::inputValues($values);
    }
}
