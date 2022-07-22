<?php

namespace App\Http\Controllers\Logic;

use App\Model\ProspectCustomer;
use App\Helpers\AppHelper;
use App\Exports\MainExport;
use App\Http\Controllers\dbsave;
use App\Imports\OnEachRowImport;
use Spatie\Permission\Models\Role;
use App\Repositories\MainRepository;
use Maatwebsite\Excel\Facades\Excel;
use App\Http\Controllers\Logic\MainLogic;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use App\Model\Employer;
use App\Model\Shops;

//import class

class ProspectCustomerLogic
{
    const route = 'prospect_customers';
    const redirect = 'prospect_customers/prospect_customers';

    public $model;

    function __construct(
        ProspectCustomer $model
    ) {
        $this->model = $model;
    }

    public  function table($subdomain)
    {
        $list = (new MainRepository($subdomain . self::route, $this->model->get()))->all();
        return MainLogic::table($subdomain, $list, self::route, ['create', 'edit', 'delete', 'view', 'export', 'import'], self::tableColumns());
    }

    public  function view($subdomain, $data)
    {
        $data = $this->model->where('id', $data)->first();
        return MainLogic::view($subdomain, $data, self::route, self::fields($data));
    }
    public  function show($subdomain, $data)
    {
        $data = $this->model->where('id', $data)->first();
        return MainLogic::show($subdomain, $data, self::route, self::fields($data));
    }

    public  function save($subdomain, $request, $data, $state)
    {

        if ($state == 'save') {
            $test = self::validated($request);

            $data = new $this->model;
            // $data->sign=AppHelper::image($request,'sign','logo');
        } else {
            $data = $this->model->where('id', $data)->first();

            // AppHelper::deleteImage($request,$data,'sign');
            // $data->sign=AppHelper::image($request,'sign','logo');
        }

        dbsave::main($data, $request, self::dbfields());
        $log = $data->save();

        return MainLogic::save($subdomain, $data, $state, $log, self::redirect, self::route);
    }

    public  function delete($subdomain, $data)
    {
        $data = $this->model->where('id', $data)->first();
        $name = $data;

        // AppHelper::deleteImage(null,$data,'sign');  

        $log = $data->delete();

        return MainLogic::delete($subdomain, $name, $log, self::redirect, self::route);
    }

    public static  function validated($request)
    { //variable

        return $request->validate([



            'secondary_phone_number' => ['required',],
            'email' => ['required',],

            'ability' => ['required',],
            'town' => ['required',],
            'employer_id' => ['required',],
            'shop_id' => ['required',],
            //validate
        ]);
    }

    public  function tableColumns() //variable
    {
        return [
            'Work No.' => 'work_number',
            'Full name' => 'full_name',
            'Phone No.' => 'phone_number',
            'Secondary Phone No' => 'secondary_phone_number',
            'Email' => 'email',

            //table columns
        ];
    }

    public  function dbfields() //variable
    {
        return [
            'work_number', 'full_name', 'phone_number', 'secondary_phone_number', 'email', 'employer_id', 'ability', 'town', 'notes', 'shop_id'

        ];
    }

    public  function fields($forEdit) //variable
    {


        $values = [
            'work_number' =>
            [
                'text', 'string', 'Work No.', 6, true, true, 'work_number', 'work_number', 'Enter Work No.'
            ],
            'full_name' =>
            [
                'text', 'string', 'Full name', 6, true, true, 'full_name', 'full_name', 'Enter Full name'
            ],
            'phone_number' =>
            [
                'text', 'string', 'Phone No.', 6, true, true, 'phone_number', 'phone_number', 'Enter Phone No.'
            ],
            'secondary_phone_number' =>
            [
                'text', 'string', 'Secondary Phone No', 6, true, true, 'secondary_phone_number', 'secondary_phone_number', 'Enter Secondary Phone No'
            ],
            'email' =>
            [
                'text', 'string', 'Email', 6, true, true, 'email', 'email', 'Enter Email'
            ],
            'shop_id' =>
            [
                'select', 'select', 'Choose Shop Name', 6, true, true, 'shop_id', 'shop_id', 'Select shop_id', '', Shops::all(), isset($forEdit) ? ProspectCustomer::with('shops')->where('id', $forEdit->id)->first()->shops : ''
            ],
            'employer_id' =>
            [
                'select', 'select', 'Choose Employer Name', 6, true, true, 'employer_id', 'employer_id', 'Select employer_id', '', Employer::all(), isset($forEdit) ? ProspectCustomer::with('employer')->where('id', $forEdit->id)->first()->employer : ''
            ],
            'ability' =>
            [
                'text', 'float', 'Ability', 6, true, true, 'ability', 'ability', 'Enter Ability'
            ],
            'town' =>
            [
                'text', 'string', 'Town', 6, true, true, 'town', 'town', 'Enter Town'
            ],
            'notes' =>
            [
                'textarea', 'text', 'Notes', 12, true, true, 'notes', 'notes', 'Enter Notes'
            ],

            //input fields
        ];

        return AppHelper::inputValues($values);
    }
    public function import($subdomain, $request)
    {
        if ($permission = AppHelper::permissions('import_' . self::route))  return $permission;
        if (!$request->hasFile('file')) {

            return back()->with('error', 'Whoops!! No Attachment Found!');
        }

        // ToModelImport

        $uniqueBy = '';
        $modelName = 'ProspectCustomer';
        $model = 'App\Model';
        $columns = [
            'work_number', 'full_name', 'phone_number', 'secondary_phone_number', 'email', 'employer_id', 'ability', 'town', 'notes'

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
        if (isset($failures) && $failures > 0) {
            $route = self::route;
            return view('main/error')->with('failures', $failures)->with('route', $route);
        }

        return redirect(self::redirect)->with('success', 'Data Import was successful!');
    }
    public function export($subdomain, $formatType)
    {
        if ($permission = AppHelper::permissions('export_' . self::route))  return $permission;
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
            'Work No.', 'Full name', 'Phone No.', 'Secondary Phone No', 'Email', 'Employer Name', 'Ability', 'Town', 'Notes'
            //headings
        ];
        $data = $this->model->select(
            'work_number',
            'full_name',
            'phone_number',
            'secondary_phone_number',
            'email',
            'employer_id',
            'ability',
            'town',
            'notes'
        )->get(); //variable

        if ($formatType == 'xlsx') {
            return Excel::download(new MainExport($data, $format, $styles, $headings, 'Excel '), self::route . '.xlsx');
        } elseif ($formatType == 'pdf') {
            return (new MainExport($data, $format, $styles, $headings, 'Pdf '))->download(self::route . '.pdf', \Maatwebsite\Excel\Excel::DOMPDF);
        }
    }
}
