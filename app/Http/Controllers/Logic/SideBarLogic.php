<?php

namespace App\Http\Controllers\Logic;

use App\Model\SideBar;
use App\Helpers\AppHelper;
use App\Exports\MainExport;
use App\Http\Controllers\dbsave;
use App\Imports\OnEachRowImport;
use Spatie\Permission\Models\Role;
use App\Repositories\MainRepository;
use Maatwebsite\Excel\Facades\Excel;
use App\Http\Controllers\Logic\MainLogic;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
//import class

class SideBarLogic
{
    const route = 'side_bars';
    const redirect = 'side_bars/side_bars';

    public $model;

    function __construct(
        SideBar $model
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
            
			
			'url' => ['required' , ],
			
			
			
			
			'description' => ['required' , ],
			
			'permission' => ['required' , ],
			//validate
        ]);
    }

    public  function tableColumns() //variable
    {
        return [
            'Parent id'=>'parent_id',
			'Title'=>'title',
			'Url'=>'url',
			'Custom Title'=>'custom_title',
			'Icon'=>'icon',
			
			//table columns
        ];
    }

    public  function dbfields() //variable
    {
        return [
            'parent_id','title','url','menu_order','icon','description','custom_title','permission',
			
        ];

    }

    public  function fields($forEdit) //variable
    {    
        

        $values = [
            'parent_id'=>
			[
				'text','integer','Parent id',6,true,true,'parent_id','parent_id','Enter Parent id'
			],
			'title'=>
			[
				'text','string','Title',6,true,true,'title','title','Enter Title'
			],
			'url'=>
			[
				'text','string','Url',6,true,true,'url','url','Enter Url'
			],
			'menu_order'=>
			[
				'text','integer','Menu order',6,true,true,'menu_order','menu_order','Enter Menu order'
			],
			'icon'=>
			[
				'text','string','Icon',6,true,true,'icon','icon','Enter Icon'
			],
			'description'=>
			[
				'text','string','Description',6,true,true,'description','description','Enter Description'
			],
			'custom_title'=>
			[
				'text','string','Custom title',6,true,true,'custom_title','custom_title','Enter Custom title'
			],
			'permission'=>
			[
				'text','string','Permission',6,true,true,'permission','permission','Enter Permission'
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
        $modelName = 'SideBar';
        $model = 'App\Model';
        $columns = [
            'parent_id','title','url','menu_order','icon','description','custom_title','permission',
			
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
            'Parent id','Title','Url','Menu order','Icon','Description','Custom title','Permission'
			//headings
        ];
        $data = $this->model->select('parent_id','title','url','menu_order','icon','description','custom_title','permission'
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
