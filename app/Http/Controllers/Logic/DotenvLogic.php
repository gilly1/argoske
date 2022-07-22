<?php

namespace App\Http\Controllers\Logic;

use App\Model\Dotenv;
use App\Helpers\AppHelper;
use App\Exports\MainExport;
use App\Http\Controllers\dbsave;
use App\Imports\OnEachRowImport;
use Spatie\Permission\Models\Role;
use App\Repositories\MainRepository;
use Maatwebsite\Excel\Facades\Excel;
use App\Http\Controllers\Logic\MainLogic;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use App\Http\Controllers\dashboard\dashboardController;
//import class

class DotenvLogic
{
    const route = 'dotenvs';
    const redirect = 'dotenvs/dotenvs';

    public $model;

    function __construct(
        Dotenv $model
    ){
        $this->model = $model;
    }

    public  function table($subdomain)
    {
        $list = (new MainRepository($subdomain.self::route,$this->model->get()))->all();
        return MainLogic::table($subdomain,$list,self::route,['create','delete','view','export','import'],self::tableColumns());
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
            // $data->sign=AppHelper::image($request,'sign','logo');
        }else{
            $data = $this->model->where('id',$data)->first();

            // AppHelper::deleteImage($request,$data,'sign');
            // $data->sign=AppHelper::image($request,'sign','logo');
        }

        dbsave::main($data,$request,self::dbfields());
        $log = $data->save();

        $subdomain = $request->subdomain;
        $companyName = $request->company_name;
        $dbhost = $request->host;
        $dbUser = $request->db_user;
        $dbPass = $request->db_pass;

        dashboardController::copy($subdomain,$companyName,$dbhost,$dbUser,$dbPass);

        return MainLogic::save($subdomain,$data,$state,$log,self::redirect,self::route);
    }

    public  function delete($subdomain,$data)
    {
        $data = $this->model->where('id',$data)->first();
        $name = $data;
        
        // AppHelper::deleteImage(null,$data,'sign');  

        $log = $data->delete();

        return MainLogic::delete($subdomain,$name,$log,self::redirect,self::route);
    }

    public static  function validated($request){ //variable

        $request->validate([
            
			
			
			
			
			//validate
        ]);
    }

    public  function tableColumns() //variable
    {
        return [
            'Company name'=>'company_name',
			'Subdomain'=>'subdomain',
			'Host'=>'host',
			'Db user'=>'db_user',
			'Db pass'=>'db_pass',
			
			//table columns
        ];
    }

    public  function dbfields() //variable
    {
        return [
            'company_name','subdomain','host','db_user','db_pass'
			
        ];

    }

    public  function fields($forEdit) //variable
    {    
        

        $values = [
            'company_name'=>
			[
				'text','string','Company name',6,true,true,'company_name','company_name','Enter Company name'
			],
			'subdomain'=>
			[
				'text','string','Subdomain',6,true,true,'subdomain','subdomain','Enter Subdomain'
			],
			'host'=>
			[
				'text','string','Host',6,true,true,'host','host','Enter Host'
			],
			'db_user'=>
			[
				'text','string','Db user',6,true,true,'db_user','db_user','Enter Db user'
			],
			'db_pass'=>
			[
				'text','string','Db pass',6,false,false,'db_pass','db_pass','Enter Db pass'
			],
			
			//input fields
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

        $uniqueBy = '';
        $modelName = 'Dotenv';
        $model = 'App\Model';
        $columns = [
            'company_name','subdomain','host','db_user','db_pass'
			
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
            'Company name','Subdomain','Host','Db user','Db pass'
			//headings
        ];
        $data = $this->model->select('company_name','subdomain','host','db_user','db_pass'
			)->get(); //variable

        if($formatType == 'xlsx')
        {
            return Excel::download(new MainExport($data,$format,$styles,$headings), self::route.'.xlsx');
        }elseif($formatType == 'pdf')
        {
            return (new MainExport($data,$format,$styles,$headings))->download(self::route.'.pdf', \Maatwebsite\Excel\Excel::DOMPDF);
        }
    }
}
