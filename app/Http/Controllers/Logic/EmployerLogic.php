<?php

namespace App\Http\Controllers\Logic;

use App\Model\Employer;
use App\Helpers\AppHelper;
use App\Model\RoundMethod;
use App\Exports\MainExport;
use App\Http\Controllers\dbsave;
use App\Imports\OnEachRowImport;
use Spatie\Permission\Models\Role;
use App\Repositories\MainRepository;
use Maatwebsite\Excel\Facades\Excel;
use App\Http\Controllers\Logic\MainLogic;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
//import class

class EmployerLogic
{
    const route = 'employers';
    const redirect = 'employers/employers';

    public $model;

    function __construct(
        Employer $model
    ){
        $this->model = $model;
    }

    public  function table($subdomain)
    {
        $list = (new MainRepository($subdomain.self::route,$this->model->get()))->all();
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
            // $data->sign=AppHelper::image($request,'sign','logo');
        }else{
            $data = $this->model->where('id',$data)->first();

            // AppHelper::deleteImage($request,$data,'sign');
            // $data->sign=AppHelper::image($request,'sign','logo');
        }

        dbsave::main($data,$request,self::dbfields());
        $log = $data->save();

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
            'Employer Number'=>'number',
			'Employer Name'=>'name',
			'Commission Rate'=>'commission_rate',
			'Rounding Method'=>['relationship','round','name'],
			'Rounding Precision'=>'precision',
			
			//table columns
        ];
    }

    public  function dbfields() //variable
    {
        return [
            'number','name','commission_rate','round_method_id','precision','retirement_age','accounts'
			
        ];

    }

    public  function fields($forEdit) //variable
    {    
        

        $values = [
            'number'=>
			[
				'text','string','Employer Number',6,true,true,'number','number','Enter Employer Number'
			],
			'name'=>
			[
				'text','string','Employer Name',6,true,true,'name','name','Enter Employer Name'
			],
			'commission_rate'=>
			[
				'text','string','Commission Rate',6,true,true,'commission_rate','commission_rate','Enter Commission Rate'
			],
			'round_method_id'=>
			[
				'select','select','Choose Rounding Method',6,true,true,'round_method_id','round_method_id','Select round_method_id','',RoundMethod::all(),isset($forEdit) ? Employer::with('round')->where('id',$forEdit->id)->first()->round : ''
			],
			'precision'=>
			[
				'text','float','Rounding Precision',6,true,true,'precision','precision','Enter Rounding Precision'
			],
			'retirement_age'=>
			[
				'text','integer','Retirement Age',6,true,true,'retirement_age','retirement_age','Enter Retirement Age'
			],
			'accounts'=>
			[
				'text','integer','Max. Recommended Accounts',6,true,true,'accounts','accounts','Enter Max. Recommended Accounts'
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
        $modelName = 'Employer';
        $model = 'App\Model';
        $columns = [
            'number','name','commission_rate','round_method_id','precision','retirement_age','accounts'
			
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
            'Employer Number','Employer Name','Commission Rate','Rounding Method','Rounding Precision','Retirement Age','Max. Recommended Accounts'
			//headings
        ];
        $data = $this->model->select('number','name','commission_rate','round_method_id','precision','retirement_age','accounts'
			)->get(); //variable

        if($formatType == 'xlsx')
        {
            return Excel::download(new MainExport($data,$format,$styles,$headings,'Excel '), self::route.'.xlsx');
        }elseif($formatType == 'pdf')
        {
            return (new MainExport($data,$format,$styles,$headings,'Pdf '))->download(self::route.'.pdf', \Maatwebsite\Excel\Excel::DOMPDF);
        }
    }
}
