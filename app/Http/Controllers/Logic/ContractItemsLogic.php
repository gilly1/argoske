<?php

namespace App\Http\Controllers\Logic;

use App\StockMaster;
use App\Model\Contract;
use App\Helpers\AppHelper;
use App\Exports\MainExport;
use App\Model\ContractItems;
use App\Http\Controllers\dbsave;
use App\Imports\OnEachRowImport;
use Spatie\Permission\Models\Role;
use App\Repositories\MainRepository;
use Maatwebsite\Excel\Facades\Excel;
use App\Http\Controllers\Logic\MainLogic;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
//import class

class ContractItemsLogic
{
    const route = 'contract_items';
    const redirect = 'contract_items/contract_items';

    public $model;

    function __construct(
        ContractItems $model
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
            'Contract No'=>['relationship','contract','name'],
			'Item'=>'stock_id',
			'Quantity'=>'quantity',
			'Installment'=>'installments',
			'Period In Months'=>'duration',
			
			//table columns
        ];
    }

    public  function dbfields() //variable
    {
        return [
            'contract_id','stock_id','quantity','installments','duration'
			
        ];

    }

    public  function fields($forEdit) //variable
    {    
        

        $values = [
            'contract_id'=>
			[
				'select','select','Choose Contract No',6,true,true,'contract_id','contract_id','Select contract_id','',Contract::all(),isset($forEdit) ? ContractItems::with('contract')->where('id',$forEdit->id)->first()->contract : ''
			],
			'stock_id'=>
			[
                'select','select','Choose Item.',6,true,true,'stock_id','stock_id','Select Item','',StockMaster::all(),isset($forEdit) ? ContractItems::with('stockMaster')->where('id',$forEdit->id)->first()->stockMaster : ''
			],
			'quantity'=>
			[
				'text','float','Quantity',6,true,true,'quantity','quantity','Enter Quantity'
			],
			'installments'=>
			[
				'text','double','Installment',6,true,true,'installments','installments','Enter Installment'
			],
			'duration'=>
			[
				'text','float','Period In Months',6,true,true,'duration','duration','Enter Period In Months'
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
        $modelName = 'ContractItems';
        $model = 'App\Model';
        $columns = [
            'contract_id','stock_id','quantity','installments','duration'
			
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
            'Contract No','Item','Quantity','Installment','Period In Months'
			//headings
        ];
        $data = $this->model->select('contract_id','stock_id','quantity','installments','duration'
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
