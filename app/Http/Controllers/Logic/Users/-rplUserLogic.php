<?php

namespace App\Http\Controllers\Logic\Users;

use App\User;
use App\Helpers\AppHelper;
use App\Exports\MainExport;
use App\Http\Controllers\dbsave;
use App\Imports\OnEachRowImport;
use App\Notifications\tellAdmin;
use Spatie\Permission\Models\Role;
use App\Repositories\MainRepository;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Notification;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

class UserLogic
{
    const route = 'user';
    const redirect = 'users/user';

    public $model;

    function __construct(
        User $model
    ){
        $this->model = $model;
    }

    public  function table()
    {
        $list = (new MainRepository($subdomain.self::route,$this->model->where('id','!=',1)->select('id','name','email')->get()))->all();
        return view('main/index')->with('title','All Users')
                                ->with('table_name','Users List')
                                ->with('route',self::route)
                                ->with('collection',$list)
                                ->with('canCreate','create_users')
                                ->with('canEdit','edit_users')
                                ->with('canDelete','delete_users')
                                ->with('canExport','import_users')
                                ->with('canImport','import_users')
                                ->with('canView','view_users')
                                ->with('columns',self::tableColumns());
    }

    public  function view($data)
    {
        if($data){
            if ( $permission = AppHelper::permissions('edit_users') )  return $permission;
        }else{
            if ( $permission = AppHelper::permissions('create_users') )  return $permission;
        }
        $data = $this->model->where('id',$data)->first();
        return view('main/view')->with('data',$data)
                                ->with('title','User')
                                ->with('route',self::route)
                                ->with('inputName',self::fields($data));
    }
    public  function show($data)
    {
        if ( $permission = AppHelper::permissions('view_users') )  return $permission;

        $data = $this->model->where('id',$data)->first();

        if(!$data) return redirect()->back()->with('error','user not found') ;
        
        return view('main/show')->with('data',$data)
                                ->with('title','User Details')
                                ->with('route',self::route)
                                ->with('canEdit','edit_users')
                                ->with('inputName',self::fields($data));
    }

    public  function save($request,$data,$state)
    {
        
        if($state == 'save')
        {
            self::validated($request);
            
            $data = new $this->model;
            $data->sign=AppHelper::image($request,'sign','logo');
        }else{
            $data = $this->model->where('id',$data)->first();
            AppHelper::deleteImage($request,$data,'sign');
            $data->sign=AppHelper::image($request,'sign','logo');
        }

        if($data->id == 1)
        {
            session()->flash("error","Not allowed For Super Admin.");
            return back();
        }

        dbsave::main($data,$request,self::dbfields());
        if($request->password){
            $data->password = Hash::make($request->password);
        }
        $log = $data->save();
        $data->roles()->sync($request->roles);

        if($state == 'save')
        {
            self::log($data,$log,'save');
        }else{
            self::log($data,$log,'update');
        }

        return redirect(self::redirect);
    }

    public  function delete($data)
    {
        $data = $this->model->where('id',$data)->first();
        $name = $data;
        
        AppHelper::deleteImage(null,$data,'sign');
        
        $log = $data->delete();
        
        self::log($name,$log,'delete');

        
        return redirect(self::redirect);
    }

    public static  function validated($request){

        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'roles' => ['required'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
        ]);
    }
    
    public static function log($data, $log, $state)
    {
        // try to write it once in one class
        //message come from top - to get it customised
        //types of logs - log(default),email,notification option - supply emails
        if(!$log){
            session()->flash('error','Something went wrong');
        }

        $route = self::route.'/'.$data->id.'/edit';
        
        if($state == 'save'){
        
            $message = AppHelper::logfunction('User ',$data->name,'Added');
            Notification::send(AppHelper::admin(),new tellAdmin('info',$message,$route));
            AppHelper::logs('critical',$message);
                
            session()->flash('success','Updated Successfully');
        }
        elseif($state == 'update'){
            
            $message = AppHelper::logfunction('User',$data->name,'Updated');
            Notification::send(AppHelper::admin(),new tellAdmin('info',$message,$route));
            AppHelper::logs('critical',$message);
                
            session()->flash('success','Added Successfully');
        }
        elseif($state == 'delete'){
        
            $message = AppHelper::logfunction('User ',$data->name,'Deleted');
            Notification::send(AppHelper::admin(),new tellAdmin('info',$message,$route));
            AppHelper::logs('critical',$message);
    
            session()->flash('success','Deleted Successfully');

        }
    }

    public  function tableColumns()
    {
        return [
            'Email'=>['view_link','email'],
            'Name'=> ['label','default','name'],
            "Roles"=>['hasManyRelationship','roles','name'],
        ];
    }

    public  function dbfields()
    {
        return [
            'name','email'
        ];

    }

    public  function fields($forEdit)
    {    
        

        $values = [
            'name'=>
            [
                'text','text','User Name',6,true,true,'name','name','Enter User Name'
            ],
            'email'=>
            [
                'text','email','Email Address',6,true,true,'email','email','Enter Email Address'
            ],
            'password'=>
            [
                'text','password','Password',6,true,true,'password','password','Enter Password'
            ],
            'password_confirmation'=>
            [
                'text','password','Password Confirmation',6,true,true,'password_confirmation','password_confirmation','Enter Password Confirmation'
            ],
            'file'=>
            [
                'file','file','Signature',6,true,true,'sign','sign','Select sign'
            ],
            'role'=>
            [
                'select','select','Choose Role',6,false,true,'roles','roles','Select roles','',Role::where('name','!=','Super Admin')->get(),isset($forEdit) ? User::with('roles')->where('id',$forEdit->id)->first()->roles->first() : ''
            ]
        ];

        return AppHelper::inputValues($values); 
    }
    public function import($request) 
    {  
        if ( $permission = AppHelper::permissions('import_users') )  return $permission;
        if(!$request->hasFile('file')){
            
            return back()->with('error', 'Whoops!! No Attachment Found!');
         }
        
        // ToModelImport

        $uniqueBy = 'email';
        $modelName = 'User';
        $model = 'App\User';
        $columns = [
            'name','email','password'
        ];
        // try {
        //     Excel::import(new ToModelImport($model,$modelName,$uniqueBy,$columns), $request->file);
        // } catch (\Maatwebsite\Excel\Validators\ValidationException $e) {
        //      $failures = $e->failures();
             
        // }

        //OnEachRowImport
        
            try {
                Excel::import(new OnEachRowImport(), $request->file);
            } catch (\Maatwebsite\Excel\Validators\ValidationException $e) {
                    $failures = $e->failures();                                        
            }
            if(isset($failures) && $failures > 0)
            {                
                $route = self::route;
                return view('main/error')->with('failures',$failures)->with('route',$route);
            }

        //fix import errors
        //validate if content available
        //validate if it contains rows
        // return view('main/error')->with('failures',$failures);
        
        return redirect(self::redirect)->with('success', 'Data Import was successful!');
    }
    public function export($formatType) 
    {     
        if ( $permission = AppHelper::permissions('export_users') )  return $permission;
        $format = [
            'C' => NumberFormat::FORMAT_DATE_DDMMYYYY,
        ];
        $styles = [
            // Style the first row as bold text.
            '1'   => ['font' => ['bold' => true]],
            '2'   => ['font' => ['bold' => true]],
            '4'   => ['font' => ['bold' => true]],
            // Styling a specific cell by coordinate.
            'B' => ['font' => ['italic' => true]],
        ];
        $headings = [
            'Name','Email' 
        ];
        $data = User::select('name','email')->where('id','!=',1)->get();

        if($formatType == 'xlsx')
        {
            return Excel::download(new MainExport($data,$format,$styles,$headings), self::route.'.xlsx');
        }elseif($formatType == 'pdf')
        {
            return (new MainExport($data,$format,$styles,$headings))->download(self::route.'.pdf', \Maatwebsite\Excel\Excel::DOMPDF);
        }
    }
}
