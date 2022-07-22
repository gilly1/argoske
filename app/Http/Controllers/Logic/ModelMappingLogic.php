<?php

namespace App\Http\Controllers\Logic;

use Cache;
use App\Helpers\AppHelper;
use App\Exports\MainExport;
use App\Model\ModelMapping;
use App\Model\ModelsToApprove;
use App\Http\Controllers\dbsave;
use App\Imports\OnEachRowImport;
use App\Model\ModelTobeApproved;
use Spatie\Permission\Models\Role;
use App\Repositories\MainRepository;
use Maatwebsite\Excel\Facades\Excel;
use App\Http\Controllers\Logic\MainLogic;
use App\Model\Approvers;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
//import class

class ModelMappingLogic
{
    const route = 'model_mappings';
    const redirect = 'model_mappings/model_mappings';
    const routeSubTable = 'approvers';

    public $model;

    function __construct(
        ModelMapping $model
    ){
        $this->model = $model;
    }

    public  function table($subdomain)
    {
        $list = (new MainRepository($subdomain.self::route,$this->model->get()))->all();
        return MainLogic::table($subdomain,$list,self::route,['create','delete','view','edit','export','import'],self::tableColumns(),
        self::tableSubColumns(),self::routeSubTable,'approvers',['create','view','edit','delete']);
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
            $confirmIfExist = $this->model->where('approver_model',$request->approver_model)->where('approved_model',$request->approved_model)->first();
            if($confirmIfExist){
                session()->flash('error','Approval already exists');
                return back()->withInput();
            }
            // $data->sign=AppHelper::image($request,'sign','logo');
        }else{
            $data = $this->model->where('id',$data)->first();
            Approvers::where('modelMapping_id',$data->id)->delete();
            Cache::forget(the_sub_domain().'approvers');
            $data->delete();
            $data = new $this->model;

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
            'Name'=>'name',
            'Model to approve'=>['relationship','modelsToApprove','name'],
            'Model to approved'=>['relationship','modelTobeApproved','name'],
            'Approval Rate'=>'approval_rate',
            'Rejection Rate'=>'rejection_rate'
			
			//table columns
        ];
    }

    public  function tableSubColumns() //variable
    {
        return [
			'Approver'=>['dynamicrelationship','modelMapping','approver_model','App\Model\ModelsToApprove','approver_model_id','name'],
			'Weight'=>'weight',
			'Super approver'=>'super_approver'
			
			//table columns
        ];
    }

    public  function dbfields() //variable
    {
        return [
            'name','approver_model','approved_model','approval_rate','rejection_rate'
			
        ];

    }

    public  function fields($forEdit) //variable
    {    
        

        $values = [
            'name'=>
			[
				'text','string','Name',12,true,true,'name','name','Enter Name'
			],
			'approver_model'=>
			[
                'select','select','Choose Model to approve',6,true,true,'approver_model','approver_model','Select approver model','',ModelsToApprove::all(),isset($forEdit) ? ModelsToApprove::where('id',$forEdit->approver_model)->first() : '',
			],
			'approved_model'=>
			[
                'select','select','Choose Model to be approved',6,true,true,'approved_model','approved_model','Select Model to be approved','',ModelTobeApproved::all(),isset($forEdit) ? ModelTobeApproved::where('id',$forEdit->approved_model)->first() : '',
			],
            'approval_rate'=>
			[
				'text','string','Approval Rate',6,true,true,'approval_rate','approval_rate','Put % if percentage'
			],
            'rejection_rate'=>
			[
				'text','string','Rjection Rate',6,true,true,'rejection_rate','rejection_rate','Put % if percentage'
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
        $modelName = 'ModelMapping';
        $model = 'App\Model';
        $columns = [
            'name','approver_model','approved_model'
			
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
            'Name','Model to approve','Model to be approved'
			//headings
        ];
        $data = $this->model->select('name','approver_model','approved_model'
			)->get(); //variable

        if($formatType == 'xlsx')
        {
            return Excel::download(new MainExport($data,$format,$styles,$headings,'Excel '), self::route.'.xlsx');
        }elseif($formatType == 'pdf')
        {
            return (new MainExport($data,$format,$styles,$headings,'Pdf '))->download(self::route.'.pdf', \Maatwebsite\Excel\Excel::DOMPDF);
        }
    }

    public function mappings()
    {
        return ModelMapping::all();
    }

    public function mappingApprovers($id)
    {
        $modelToApprove = ModelMapping::with('modelsToApprove')->where('id',$id)->first()->modelsToApprove;

        if($modelToApprove->model == 'App\Model\Hierarchy' || $modelToApprove->model == 'App\Model\Designation')
        {
            return [];
        }

        return $modelToApprove->model::select('id','name')->get();
    }
}
