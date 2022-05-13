<?php

namespace App\Http\Controllers\Logic\Users;

use App\User;
use App\Helpers\AppHelper;
use App\Model\Designation;
use App\Exports\MainExport;
use App\Http\Controllers\dbsave;
use App\Imports\OnEachRowImport;
use Spatie\Permission\Models\Role;
use App\Repositories\MainRepository;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Facades\Excel;
use App\Http\Controllers\Logic\MainLogic;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

class UserLogic
{
    const route = 'users';
    const redirect = 'users/users';

    public $model;

    function __construct(
        User $model
    ){
        $this->model = $model;
    }

    public  function table($subdomain)
    {
        $list = (new MainRepository($subdomain.self::route,$this->model->where('id','!=',1)->select('id','name','email','designation_id')->get()))->all();
        return MainLogic::table($subdomain,$list,self::route,['create','edit','delete','view','export','import'],self::tableColumns());
    }

    public  function view($subdomain,$data)
    {
        $data = $this->model->where('id',$data)->first();
        return MainLogic::view($subdomain,$data,self::route,self::fields($data));
    }
    public  function show($subdomain,$data)
    {
        $data = $this->model->where('id',$data)->first();
        return MainLogic::show($subdomain,$data,self::route,self::fields($data));
    }

    public  function save($subdomain,$request,$data,$state)
    {
        
        if($state == 'save')
        {
            self::validated($request);
            
            $data = new $this->model;
        }else{
            $data = $this->model->where('id',$data)->first();
            AppHelper::deleteImage($request,$data,'sign');
        }

        if($data->id == 1)
        {
            session()->flash("error","Not allowed For Super Admin.");
            return back();
        }

        if($request->password){
            $data->password = Hash::make($request->password);
        }        
        dbsave::main($data,$request,self::dbfields());
        $data->sign=AppHelper::image($request,'sign','logo');
        $log = $data->save();
        $data->roles()->sync($request->roles);

        return MainLogic::save($subdomain,$data,$state,$log,self::redirect,self::route);
    }

    public  function delete($subdomain,$data)
    {
        $data = $this->model->where('id',$data)->first();
        $name = $data;
        
        AppHelper::deleteImage(null,$data,'sign');        
        $log = $data->delete();

        return MainLogic::delete($subdomain,$name,$log,self::redirect,self::route);
    }

    public static  function validated($request){ //variable

        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'roles' => ['required'],
            'designation_id' => ['required'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
        ]);
    }

    public  function tableColumns() //variable
    {
        return [
            'Email'=>['view_link','email'],
            'Name'=> ['label','default','name'],
            "Roles"=>['hasManyRelationship','roles','name'],
			'Designation'=>['relationship','designation','name']
        ];
    }

    public  function dbfields() //variable
    {
        return [
            'name','email','designation_id'
        ];

    }

    public  function fields($forEdit) //variable
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
                'password','password','Password',6,true,true,'password','password','Enter Password'
            ],
            'password_confirmation'=>
            [
                'password','password','Password Confirmation',6,true,true,'password_confirmation','password_confirmation','Enter Password Confirmation'
            ],
            'role'=>
            [
                'select','select','Choose Role',6,false,true,'roles','roles','Select roles','',Role::where('name','!=','Super Admin')->get(),isset($forEdit) ? User::with('roles')->where('id',$forEdit->id)->first()->roles->first() : ''
            ],
            'designation_id'=>
            [
                'select','select','Choose Designation',6,false,true,'designation_id','designation_id','Select designation_id','',Designation::all(),isset($forEdit) ? User::with('designation')->where('id',$forEdit->id)->first()->designation : ''
            ],
            'file'=>
            [
                'file','file','Signature',12,false,false,'sign','sign','Select sign'
            ],
        ];

        return AppHelper::inputValues($values); 
    }
    public function import($subdomain,$request) 
    {  
        if ( $permission = AppHelper::permissions('import_'.self::route) )  return $permission;
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
                return view('main/error')->with('failures',$failures)->with('route',$route)->with('subdomain',$subdomain);
            }
        
        return redirect(self::redirect)->with('success', 'Data Import was successful!');
    }
    public function export($subdomain,$formatType) 
    {     
        if ( $permission = AppHelper::permissions('export_'.self::route) )  return $permission;
        $format = [ //variable
            'C' => NumberFormat::FORMAT_DATE_DDMMYYYY,
        ];
        $styles = [ //variable
            // Style the first row as bold text.
            '1'   => ['font' => ['bold' => true]],
            '2'   => ['font' => ['bold' => true]],
            '4'   => ['font' => ['bold' => true]],
            // Styling a specific cell by coordinate.
            'B' => ['font' => ['italic' => true]],
        ];
        $headings = [ //variable
            'Name','Email'
        ];
        $data = $this->model->select('name','email')->where('id','!=',1)->get(); //variable

        if($formatType == 'xlsx')
        {
            return Excel::download(new MainExport($data,$format,$styles,$headings,'Excel '), self::route.'.xlsx');
        }elseif($formatType == 'pdf')
        {
            return (new MainExport($data,$format,$styles,$headings,'Pdf '))->download(self::route.'.pdf', \Maatwebsite\Excel\Excel::DOMPDF);
        }
    }
}
