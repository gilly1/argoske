<?php

namespace App\Http\Controllers\Logic;

use Cache;
use App\Helpers\AppHelper;
use App\Exports\MainExport;
use App\Model\ModelMapping;
use Illuminate\Support\Str;
use App\Model\ModelsToApprove;
use App\Model\ApproverStatuses;
use App\Http\Controllers\dbsave;
use App\Imports\OnEachRowImport;
use App\Model\ModelTobeApproved;
use App\Notifications\tellAdmin;
use Spatie\Permission\Models\Role;
use App\Repositories\MainRepository;
use Maatwebsite\Excel\Facades\Excel;
use App\Http\Controllers\MainController;
use App\Http\Controllers\Logic\MainLogic;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use App\Mail\sendMails;
//import class

class ApproverStatusesLogic
{
    const route = 'approver_statuses';
    const redirect = 'approver_statuses/approver_statuses';

    public $model;

    function __construct(
        ApproverStatuses $model
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
            $data = $this->model->with('modelMapping')->where('id',$data)->first();

            // AppHelper::deleteImage($request,$data,'sign');
            // $data->sign=AppHelper::image($request,'sign','logo');
        }


        dbsave::main($data,$request,self::dbfields());
        $data->user_id = auth()->user()->id;
        $log = $data->save();

        $approvers = $this->model->where('modelMapping_id',$data->modelMapping_id)
                            ->where('approved_model_id',$data->approved_model_id)->get();

        $approversCount = $approvers->where('super_admin',0)->count();
        $approved = $approvers->where('super_admin',0)->where('status',1)->where('approved',1)->count();
        $approvedPercentage = ($approved/$approversCount)*100;
        
        $rejected = $approvers->where('super_admin',0)->where('status',1)->where('approved',0)->count();
        $rejectedPercentage = ($rejected/$approversCount)*100;

        $modelMapping = $data->modelMapping;
        $isSuperApproved = $approvers->where('status',1)->where('super_admin',1)->where('approved',1)->first();
        $isSuperRejected = $approvers->where('status',1)->where('super_admin',1)->where('approved',0)->first();

        $deleted = $this->model->where('modelMapping_id',$data->modelMapping_id)
                            ->where('approved_model_id',$data->approved_model_id)
                            ->where('super_admin',0)->where('status',0);

        //check if rate is % or number        
        $modelToApprove = self::modelToBeApproved($modelMapping->approved_model);
        if (strpos($modelMapping->approval_rate, '%') !== false) {
            $approval_rate = (int)str_replace("%","",$modelMapping->approval_rate);
            if($approvedPercentage >= $approval_rate)
            {
                self::isApproved($modelToApprove->model,$data->approved_model_id,'1',$deleted);
            }
        }else{
            if($approved >= $modelMapping->approval_rate)
            {
                self::isApproved($modelToApprove->model,$data->approved_model_id,'1',$deleted);
            }
        }
        if (strpos($modelMapping->rejection_rate, '%') !== false ) {
            $rejection_rate = (int)str_replace("%","",$modelMapping->rejection_rate);
            if($rejectedPercentage >= $rejection_rate)
            {
                self::isApproved($modelToApprove->model,$data->approved_model_id,'0',$deleted);
            }
        }else{
            if($rejected >= $modelMapping->rejection_rate)
            {
                self::isApproved($modelToApprove->model,$data->approved_model_id,'0',$deleted);
            }
        }
        if(!$modelMapping->rejection_rate){
            if($rejected > 0)
            {
                self::isApproved($modelToApprove->model,$data->approved_model_id,'0',$deleted);
            }
            if($approvedPercentage == 100)
            {
                self::isApproved($modelToApprove->model,$data->approved_model_id,'1',$deleted);
            }
        }
        if($isSuperApproved)
        {
            self::isApproved($modelToApprove->model,$data->approved_model_id,'1',$deleted);
        }
        if($isSuperRejected)
        {
            self::isApproved($modelToApprove->model,$data->approved_model_id,'0',$deleted);
        }

        $modelName = $request->modelName;        

        if(method_exists($modelName,'approve'))
        {
            [$superApproverStatus,$superApprover,$nextApproverStatus,$nextApprover,$approvers,$users,$isUserModelApproval,$allSuperApprover,$allApproved,$user_id,$modelToApprove] = $modelName::approve($request->modelNameId);
            if(!$nextApprover){
                return;
            }
            $message = 'Approval Request';
            $route = Str::snake(Str::pluralStudly( basename(str_replace('\\', '/', $modelName)) )); 
            $fullRoute = $route.'/'.$route.'/'.$request->modelNameId;
            $approvers = AppHelper::approver($nextApprover);
            \Mail::to($approvers)->send(new sendMails(env('MAIL_FROM_ADDRESS', 'gillycode@gmail.com'),env('MAIL_FROM_NAME', 'Gilly Code'), $message, $message,$fullRoute));
            \Notification::send($approvers,new tellAdmin('info',$message,$fullRoute));
            
        }



        return MainLogic::save($subdomain,$data,$state,$log,self::redirect,self::route);
    }
    private static function modelToBeApproved($id)
    {
        $modelToApprove = ModelTobeApproved::where('id',$id)->first(); 
        if(!$modelToApprove) {
            return false;
        }
        return $modelToApprove;
    }

    private static function isApproved($modelToApprove,$id,$status,$deleted)
    {
        $modelToApprove::where('id',$id)->update(['is_approved'=> $status]);
        $deleted->delete();
        $url = url()->previous();
        $route = app('router')->getRoutes($url)->match(app('request')->create($url))->getName();
        $routeName = substr($route, 0, strpos($route, "."));
        Cache::forget(the_sub_domain().$routeName);
    }

    public  function deleteLoop($data)
    {
        foreach($data as $data){
            $data->delete();            
        }
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
			'Approver'=>'approver_model_id',
			'Approved model'=>'approved_model_id',
			'Weight'=>'weight',
			'Status'=>'status',
			'User'=>'user_id',
			
			//table columns
        ];
    }

    public  function dbfields() //variable
    {
        return [
            'status','approved','reason'
			
        ];

    }

    public  function fields($forEdit) //variable
    {    
        

        $values = [
            'modelMapping_id'=>
			[
				'select','select','Choose Model mapping',6,true,true,'modelMapping_id','modelMapping_id','Select modelMapping_id','',ModelMapping::all(),isset($forEdit) ? ApproverStatuses::with('modelMapping')->where('id',$forEdit->id)->first()->modelMapping : ''
			],
			'approver_model_id'=>
			[
				'text','integer','Approver',6,true,true,'approver_model_id','approver_model_id','Enter Approver'
			],
			'approved_model_id'=>
			[
				'text','integer','Approved model',6,true,true,'approved_model_id','approved_model_id','Enter Approved model'
			],
			'weight'=>
			[
				'text','integer','Weight',6,true,true,'weight','weight','Enter Weight'
			],
			'status'=>
			[
				'text','integer','Status',6,true,true,'status','status','Enter Status'
			],
			'approved'=>
			[
				'text','integer','Approved',6,true,true,'approved','approved','Enter Approved'
			],
			'super_admin'=>
			[
				'text','integer','Super admin',6,true,true,'super_admin','super_admin','Enter Super admin'
			],
			'reason'=>
			[
				'textarea','text','Reason',12,true,true,'reason','reason','Enter Reason'
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
        $modelName = 'ApproverStatuses';
        $model = '';
        $columns = [
            'modelMapping_id','approver_model_id','approved_model_id','weight','status','approved','super_admin','reason'
			
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
            'Model mapping','Approver','Approved model','Weight','Status','Approved','Super admin','Reason'
			//headings
        ];
        $data = $this->model->select('modelMapping_id','approver_model_id','approved_model_id','weight','status','approved','super_admin','reason'
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
