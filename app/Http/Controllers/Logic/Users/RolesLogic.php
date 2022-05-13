<?php

namespace App\Http\Controllers\Logic\Users;

use Carbon\Carbon;
use App\Helpers\AppHelper;
use App\Exports\MainExport;
use App\Imports\ToModelImport;
use App\Http\Controllers\dbsave;
use App\Notifications\tellAdmin;
use App\Http\Controllers\objClass;
use Spatie\Permission\Models\Role;
use App\Repositories\MainRepository;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Facades\Excel;
use Spatie\Permission\Models\Permission;
use App\Http\Controllers\Logic\MainLogic;
use Illuminate\Support\Facades\Notification;

class RolesLogic
{
    const route = 'roles';
    const redirect = 'roles/roles';
    const routeSubTable = 'users';

    public $model;

    function __construct(
        Role $model
    ){
        $this->model = $model;
    }

    public  function table($subdomain)
    {
        $list = (new MainRepository($subdomain.self::route,$this->model->where('name','!=','Super Admin')->get()))->all();
        
        return MainLogic::table($subdomain,
            $list,self::route,['create','edit','delete','view','export','import'],self::tableColumns(),
            self::tableSubColumns(),self::routeSubTable,'users',['view','edit','delete']
        );
    }

    public  function view($subdomain,$data)
    {
        
        if($data){
            if ( $permission = AppHelper::permissions('edit_roles') )  return $permission;
        }else{
            if ( $permission = AppHelper::permissions('create_roles') )  return $permission;
        }

        $permissions = Permission::all();
        $data = $this->model->with('permissions')->where('id',$data)->first();
        return view('Custom/Roles/view')->with('data',$data)
                                ->with('permissions',$permissions)
                                ->with('title','Roles')
                                ->with('route',self::route)
                                ->with('inputName',self::fields($data))
                                ->with('subdomain',$subdomain);
    }

    public  function save($subdomain,$request,$data,$state)
    {
        self::validated($request);
        
        if($state == 'save')
        {
            $data = new $this->model;
        }else{
            $data = $this->model->where('id',$data)->first();
        }

        dbsave::main($data,$request,self::dbfields());
        if($request->password){
            $data->password = Hash::make($request->password);
        }
        $log = $data->save();
        $data->syncPermissions($request->roles);

        if($state == 'save')
        {
            self::log($data,$log,'save');
        }else{
            self::log($data,$log,'update');
        }

        return redirect(self::redirect);
    }

    public function delete($subdomain,$data)
    {
        $data = $this->model->where('id',$data)->first();
        $name = $data;
        
        $log = $data->delete();
        
        self::log($name,$log,'delete');

        
        return redirect(self::redirect);
    }

    private static function validated($request){
        // $request->validate([
        //     'name' => ['required', 'string', 'max:255'],
        //     'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
        // ]);
    }
    
    private static function log($data, $log, $state)
    {
        if(!$log){
            session()->flash('error','Something went wrong');
        }

        $route = self::route.'/'.$data->id.'/edit';
        
        if($state == 'save'){
        
            $message = AppHelper::logfunction('Role ',$data->name,'Added');
            Notification::send(AppHelper::admin(),new tellAdmin('info',$message,$route));
            AppHelper::logs('critical',$message);
                
            session()->flash('success','Added Successfully');
        }
        elseif($state == 'update'){
            
            $message = AppHelper::logfunction('Role',$data->name,'Updated');
            Notification::send(AppHelper::admin(),new tellAdmin('info',$message,$route));
            AppHelper::logs('critical',$message);
                
            session()->flash('success','Updated Successfully');
        }
        elseif($state == 'delete'){
        
            $message = AppHelper::logfunction('Role ',$data->name,'Deleted');
            Notification::send(AppHelper::admin(),new tellAdmin('info',$message,$route));
            AppHelper::logs('critical',$message);
    
            session()->flash('success','Deleted Successfully');

        }
    }

    private static function tableColumns()
    {
        return [
            'Name'=> 'name',
            // "Permissions"=>['hasManyRelationship','permissions','name'],
        ];
    }
    private static  function tableSubColumns()
    {
        return [
            'Name'=>'name',
            'Email'=> 'email'
        ];
    }

    private static  function dbfields()
    {
        return [
            'name'
        ];

    }

    private static  function fields($forEdit)
    { 
        $values = [
            'name'=>
            [
                'text','text','Role Name',12,true,true,'name','name','Enter Role Name'
            ]
        ];

        return AppHelper::inputValues($values); 
    }
    public function import($request) 
    {        
        $modelName = 'Role';
        $model = 'Spatie\Permission\Models\Role';
        $columns = [
            'name'
        ];
        try {
            Excel::import(new ToModelImport($model,$modelName,null,$columns), $request->file);
        } catch (\Maatwebsite\Excel\Validators\ValidationException $e) {
             $failures = $e->failures();
             
        }

        //fix import errors
        //validate if content available
        //validate if it contains rows
        // return view('main/error')->with('failures',$failures);
        
        return redirect(self::redirect)->with('success', 'Data Import was successful!');
    }
    public function export($formatType) 
    {     
        $format = [
            
        ];
        $styles = [
            // Style the first row as bold text.
            '4'   => ['font' => ['bold' => true]],
            // Styling a specific cell by coordinate.
            'B' => ['font' => ['italic' => true]],
        ];
        $headings = [
            'Name'
        ];
        $data = Role::select('name')->get();
        
        if($formatType == 'xlsx')
        {
            return Excel::download(new MainExport($data,$format,$styles,$headings), self::route.'.xlsx');
        }elseif($formatType == 'pdf')
        {
            return (new MainExport($data,$format,$styles,$headings))->download(self::route.'.pdf', \Maatwebsite\Excel\Excel::DOMPDF);
        }

    }
}
