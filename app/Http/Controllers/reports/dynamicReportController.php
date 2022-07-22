<?php

namespace App\Http\Controllers\reports;

use App\ReportTemplate;
use App\Helpers\AppHelper;
use App\Exports\MainExport;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Maatwebsite\Excel\Facades\Excel;
use App\Http\Controllers\Logic\MainLogic;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

class dynamicReportController extends Controller
{
    const route = 'reports';

    public function table($subdomain)
    {  
        $vue = '<task-draggable/>';
        return MainLogic::view($subdomain,null,self::route,self::fields(null),$vue);
    }
    public function tables($subdomain,$table)
    {
        // return \DB::connection()->getDoctrineSchemaManager()->listTableNames();
        $tableColumns = \Schema::getColumnListing($table);

        $data = DB::table($table)
                ->select($tableColumns)
                ->get();

        return $data;
        // return \Schema::getColumnListing('designations');
        // return \DB::getSchemaBuilder()->getColumnListing('users');
        // return \DB::connection()->getDoctrineSchemaManager()->listTableNames();
    }
    

    public  function save($subdomain,$request,$data,$state)
    {
        $tableColumns = \Schema::getColumnListing($request->table);

        $columns = explode(',',$request->columns);

        $data = DB::table($request->table)
                ->select($columns)
                ->get();

        return $this->export($subdomain,$request->file_type,$data,$columns,$request->file_name);

        return $data;
        
    }

    public  function fields($forEdit) //variable
    {    
        $values = [
            
        ];

        return AppHelper::inputValues($values); 
    }

    
    public function getTableColumns($subdomain,$table)
    {  

        $tableColumnsList = \Schema::getColumnListing($table);
        $tableColumns = new Collection();
        $i= 1;
        foreach($tableColumnsList as $column){
            if($column == 'id' || $column == 'created_at' || $column == 'deleted_at' || $column == 'updated_at' || $column == 'is_approved'){
                continue;
            }
            $tableColumns->push([
                'name'=>$column
            ]);

        }

        return $tableColumns;
        
    }
    public function saveTemplate(Request $request)
    {  
        $report = new ReportTemplate;
        $report->table = $request->table;
        $report->name = $request->name;
        $report->columns = implode(",",$request->columns);
        $report->save();

        return response('Updated Successfully.', 200);
        
    }
    public function templates($subdomain,$id)
    {  
        return ReportTemplate::where('table',$id)->get();
        
    }
    public function export($subdomain,$formatType,$data,$headings,$fileName) 
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

        if($formatType == 'xlsx')
        {
            return Excel::download(new MainExport($data,$format,$styles,$headings,'Excel '), $fileName.'.xlsx');
        }elseif($formatType == 'pdf')
        {
            return (new MainExport($data,$format,$styles,$headings,'Pdf '))->download($fileName.'.pdf', \Maatwebsite\Excel\Excel::DOMPDF);
        }elseif($formatType == 'csv')
        {
            return (new MainExport($data,$format,$styles,$headings,'csv '))->download($fileName.'.csv', \Maatwebsite\Excel\Excel::CSV);
        }
    }
}
