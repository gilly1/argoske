<?php

namespace App\Http\Controllers\Logic;

use App\Model\Gender;
use App\Model\Contract;
use App\Model\Guarantor;
use App\Helpers\AppHelper;
use App\Model\Nationality;
use App\Exports\MainExport;
use App\Http\Controllers\dbsave;
use App\Imports\OnEachRowImport;
use App\Model\IdentificationType;
use Spatie\Permission\Models\Role;
use App\Repositories\MainRepository;
use Maatwebsite\Excel\Facades\Excel;
use App\Http\Controllers\Logic\MainLogic;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
//import class

class GuarantorLogic
{
    const route = 'guarantors';
    const redirect = 'guarantors/guarantors';

    public $model;

    function __construct(
        Guarantor $model
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
			'email' => ['required' , ],
			//validate
        ]);
    }

    public  function tableColumns() //variable
    {
        return [
            'Designation'=>'designation',
			'Department'=>'department',
			'Station'=>'station',
			'Section'=>'section',
			'Last Name'=>'last_name',
			
			//table columns
        ];
    }

    public  function dbfields() //variable
    {
        return [
            'designation','department','station','section','last_name','other_names','d0b','gender_id','district_of_birth','identification_type_id','nationality_id','id_number','serial_number','date_of_issue','place_of_issue','district','division','location','sub_location','phone_number','email'
			
        ];

    }

    public  function fields($forEdit) //variable
    {    
        

        $values = [
            'contract_id'=>
			[
				'select','select','Choose Contract No',6,true,true,'contract_id','contract_id','Select contract_id','',Contract::all(),isset($forEdit) ? Guarantor::with('contract')->where('id',$forEdit->id)->first()->contract : ''
			],
            'designation'=>
			[
				'text','string','Designation',6,true,true,'designation','designation','Enter Designation'
			],
			'department'=>
			[
				'text','string','Department',6,true,true,'department','department','Enter Department'
			],
			'station'=>
			[
				'text','string','Station',6,true,true,'station','station','Enter Station'
			],
			'section'=>
			[
				'text','string','Section',6,true,true,'section','section','Enter Section'
			],
			'last_name'=>
			[
				'text','string','Last Name',6,true,true,'last_name','last_name','Enter Last Name'
			],
			'other_names'=>
			[
				'text','string','Other Names',6,true,true,'other_names','other_names','Enter Other Names'
			],
			'd0b'=>
			[
				'dateTime','dateTime','Date Of Birth',6,true,true,'date0','d0b','Enter Date Of Birth'
			],
			'gender_id'=>
			[
				'select','select','Choose Gender',6,true,true,'gender_id','gender_id','Select gender_id','',Gender::all(),isset($forEdit) ? Guarantor::with('gender')->where('id',$forEdit->id)->first()->gender : ''
			],
			'district_of_birth'=>
			[
				'text','string','District Of Birth',6,true,true,'district_of_birth','district_of_birth','Enter District Of Birth'
			],
			'identification_type_id'=>
			[
				'select','select','Choose ID Type',6,true,true,'identification_type_id','identification_type_id','Select identification_type_id','',IdentificationType::all(),isset($forEdit) ? Guarantor::with('identification')->where('id',$forEdit->id)->first()->identification : ''
			],
			'nationality_id'=>
			[
				'select','select','Choose Nationality',6,true,true,'nationality_id','nationality_id','Select nationality_id','',Nationality::all(),isset($forEdit) ? Guarantor::with('nationality')->where('id',$forEdit->id)->first()->nationality : ''
			],
			'id_number'=>
			[
				'text','integer','ID No',6,true,true,'id_number','id_number','Enter ID No'
			],
			'serial_number'=>
			[
				'text','integer','ID Serial No',6,true,true,'serial_number','serial_number','Enter ID Serial No'
			],
			'date_of_issue'=>
			[
				'dateTime','dateTime','Date Of Issue',6,true,true,'date1','date_of_issue','Enter Date Of Issue'
			],
			'place_of_issue'=>
			[
				'text','string','Place Of Issue',6,true,true,'place_of_issue','place_of_issue','Enter Place Of Issue'
			],
			'district'=>
			[
				'text','string','District',6,true,true,'district','district','Enter District'
			],
			'division'=>
			[
				'text','string','Division',6,true,true,'division','division','Enter Division'
			],
			'location'=>
			[
				'text','string','Location',6,true,true,'location','location','Enter Location'
			],
			'sub_location'=>
			[
				'text','string','Sub Location',6,true,true,'sub_location','sub_location','Enter Sub Location'
			],
			'phone_number'=>
			[
				'text','string','Phone Number',6,true,true,'phone_number','phone_number','Enter Phone Number'
			],
			'email'=>
			[
				'text','string','Email',6,true,true,'email','email','Enter Email'
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
        $modelName = 'Guarantor';
        $model = 'App\Model';
        $columns = [
            'designation','department','station','section','last_name','other_names','d0b','gender_id','district_of_birth','identification_type_id','nationality_id','id_number','serial_number','date_of_issue','place_of_issue','district','division','location','sub_location','phone_number','email'
			
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
            'Designation','Department','Station','Section','Last Name','Other Names','Date Of Birth','Gender','District Of Birth','ID Type','Nationality','ID No','ID Serial No','Date Of Issue','Place Of Issue','District','Division','Location','Sub Location','Phone Number','Email'
			//headings
        ];
        $data = $this->model->select('designation','department','station','section','last_name','other_names','d0b','gender_id','district_of_birth','identification_type_id','nationality_id','id_number','serial_number','date_of_issue','place_of_issue','district','division','location','sub_location','phone_number','email'
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
