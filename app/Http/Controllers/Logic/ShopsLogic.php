<?php

namespace App\Http\Controllers\Logic;

use App\Model\Shops;
use App\Helpers\AppHelper;
use App\Exports\MainExport;
use App\Http\Controllers\dbsave;
use App\Imports\OnEachRowImport;
use Spatie\Permission\Models\Role;
use App\Repositories\MainRepository;
use Maatwebsite\Excel\Facades\Excel;
use App\Http\Controllers\Logic\MainLogic;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use App\Model\Regions;
//import class

class ShopsLogic
{
    const route = 'shops';
    const redirect = 'shops/shops';

    public $model;

    function __construct(
        Shops $model
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
            'Region Name'=>['relationship','regions','name'],
			'Shop Name'=>'shop_name',
			'Shop Code'=>'shop_code',
			
			//table columns
        ];
    }

    public  function dbfields() //variable
    {
        return [
            'regions_id','shop_name','shop_code'
			
        ];

    }

    public  function fields($forEdit) //variable
    {    
        

        $values = [
            'regions_id'=>
			[
				'select','select','Choose Region Name',6,true,true,'regions_id','regions_id','Select regions_id','',Regions::all(),isset($forEdit) ? Shops::with('regions')->where('id',$forEdit->id)->first()->regions : ''
			],
			'shop_name'=>
			[
				'text','string','Shop Name',6,true,true,'shop_name','shop_name','Enter Shop Name'
			],
			'shop_code'=>
			[
				'text','string','Shop Code',6,true,true,'shop_code','shop_code','Enter Shop Code'
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
        $modelName = 'Shops';
        $model = 'App\Model';
        $columns = [
            'regions_id','shop_name','shop_code'
			
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
            'Region Name','Shop Name','Shop Code'
			//headings
        ];
        $data = $this->model->select('regions_id','shop_name','shop_code'
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
