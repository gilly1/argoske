<?php

namespace App\Http\Controllers\Logic;

use Cache;
use App\Model\Approvers;
use App\Helpers\AppHelper;
use App\Exports\MainExport;
use App\Model\ModelMapping;
use App\Http\Controllers\dbsave;
use App\Imports\OnEachRowImport;
use Spatie\Permission\Models\Role;
use App\Repositories\MainRepository;
use Maatwebsite\Excel\Facades\Excel;
use App\Http\Controllers\Logic\MainLogic;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
//import class

class ApproversLogic
{
    const route = 'approvers';
    const redirect = 'approvers/approvers';

    public $model;

    function __construct(
        Approvers $model
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
        $vue = '<approvers/>';
        $data = $this->model->where('id',$data)->first();
        return MainLogic::view($subdomain,$data,self::route,[],$vue);
    }
    public  function show($subdomain,$data)
    {
        $data = $this->model->where('id',$data)->first();
        return MainLogic::show($subdomain,$data,self::route,self::fields($data));
    }

    public  function save($subdomain,$request,$data,$state)
    {      
        
        foreach($request->fields as $field){  
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

            $data->modelMapping_id = $request->model_mapping;
            $data->approver_model_id = $field['name'];
            $data->weight = $field['counter'];
            $data->super_approver = 0;
            $log = $data->save();
            MainLogic::bulkSave($subdomain,$data,$state,$log,self::redirect,self::route);
        }
        // dbsave::main($data,$request,self::dbfields());
        
        return redirect(self::redirect);

        // return MainLogic::save($subdomain,$data,$state,$log,self::redirect,self::route);
    }

    public  function delete($subdomain,$data)
    {
        $data = $this->model->where('id',$data)->first();
        $name = $data;
        $log = $this->model->where('modelMapping_id',$data->modelMapping_id)->delete();
        
        // AppHelper::deleteImage(null,$data,'sign');  

        // $log = $data->delete();

        Cache::forget(the_sub_domain().'approvers');

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
            'Model mapping'=>['relationship','modelMapping','name'],
			'Approver'=>['dynamicrelationship','modelMapping','approver_model','App\Model\ModelsToApprove','approver_model_id','name'],
			'Weight'=>'weight',
			'Super approver'=>'super_approver',
			
			//table columns
        ];
    }

    public  function dbfields() //variable
    {
        return [
            'modelMapping_id','approver_model_id','weight','super_approver'
			
        ];

    }

    public  function fields($forEdit) //variable
    {    
        

        $values = [
            'modelMapping_id'=>
			[
				'select','select','Choose Model mapping',6,true,true,'modelMapping_id','modelMapping_id','Select modelMapping_id','',ModelMapping::all(),isset($forEdit) ? Approvers::with('modelMapping')->where('id',$forEdit->id)->first()->modelMapping : ''
			],
			'approver_model_id'=>
			[
				'text','integer','Approver',6,true,true,'approver_model_id','approver_model_id','Enter Approver'
			],
			'weight'=>
			[
				'text','integer','Weight',6,true,true,'weight','weight','Enter Weight'
			],
			'super_approver'=>
			[
				'text','integer','Super approver',6,true,true,'super_approver','super_approver','Enter Super approver'
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
        $modelName = 'Approvers';
        $model = '';
        $columns = [
            'modelMapping_id','approver_model_id','weight','super_approver'
			
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
            'Model mapping','Approver','Weight','Super approver'
			//headings
        ];
        $data = $this->model->select('modelMapping_id','approver_model_id','weight','super_approver'
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
