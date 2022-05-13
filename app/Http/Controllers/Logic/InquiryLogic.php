<?php

namespace App\Http\Controllers\Logic;

use Carbon\Carbon;
use App\Model\Shops;
use App\Model\Status;
use App\Model\Inquiry;
use App\Model\Contract;
use App\Helpers\AppHelper;
use App\Exports\MainExport;
use App\Model\InquiryItems;
use App\Model\ContractItems;
use App\Model\AccountCustomer;
use App\Model\ProspectCustomer;
use App\Http\Controllers\dbsave;
use App\Imports\OnEachRowImport;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;
use App\Repositories\MainRepository;
use Maatwebsite\Excel\Facades\Excel;
use App\Http\Controllers\Logic\MainLogic;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

//import class

class InquiryLogic
{
    const route = 'inquiries';
    const redirect = 'inquiries/inquiries';
    const routeSubTable = 'inquiry_items';

    public $model;

    function __construct(
        Inquiry $model
    ) {
        $this->model = $model;
    }

    public  function table($subdomain)
    {
        $list = (new MainRepository($subdomain . self::route, $this->model->get()))->all();
        return MainLogic::table(
            $subdomain,
            $list,
            self::route,
            ['create', 'edit', 'delete', 'view', 'export', 'import'],
            self::tableColumns(),
            // self::tableSubColumns(),
            // self::routeSubTable,
            // 'inquiry_items',
            // ['view', 'edit', 'delete']
        );
    }

    public  function view($subdomain, $data)
    {
        if ($data) {
            $vue = "<items-edit id=$data />";
        } else {
            $vue = '<items/>';
        }
        $data = $this->model->where('id', $data)->first();
        return MainLogic::view($subdomain, $data, self::route, self::fields($data), $vue);
    }
    public  function show($subdomain, $data)
    {
        $data = $this->model->where('id', $data)->first();
        return MainLogic::show($subdomain, $data, self::route, self::fields($data));
    }

    public  function save($subdomain, $request, $data, $state)
    {
        $approved = false;
        if ($state == 'save') {
            $inqury_number = $this->model->count();
            self::validated($request);

            $data = new $this->model;
            $data->inquiry_number = str_pad($inqury_number + 1, 6, "0", STR_PAD_LEFT) . '/' . Carbon::now()->format('Y');
            // $data->sign=AppHelper::image($request,'sign','logo');
        } else {
            $data = $this->model->where('id', $data)->first();

            if ($data->status == 2) {
                $approved = true;
            }

            // AppHelper::deleteImage($request,$data,'sign');
            // $data->sign=AppHelper::image($request,'sign','logo');
        }

        DB::transaction(function () use ($subdomain, $request, $data, $state, $approved) {
            dbsave::main($data, $request, self::dbfields());
            $log = $data->save();

            if ($state == 'save') {
                foreach ($request->fields as $item) {
                    $inquiryItems = new InquiryItems;
                    $inquiryItems->inquiry_id = $data->id;
                    $inquiryItems->stock_id = $item['name'];
                    $inquiryItems->quantity = $item['quantity'];
                    $inquiryItems->installments = $item['installment'];
                    $inquiryItems->duration = $item['duration'];
                    $inquiryItems->save();
                }
            } else {
                $allInquiryItems = InquiryItems::where('inquiry_id', $data->id)->get();
                foreach ($request->fields as $item) {
                    if ($item['id']) {
                        $inquiryItems = $allInquiryItems->where('id', $item['id'])->first();
                        $inquiryItems->quantity = $item['quantity'];
                        $inquiryItems->installments = $item['installment'];
                        $inquiryItems->duration = $item['duration'];
                        $inquiryItems->save();
                    } else {
                        $inquiryItems = new InquiryItems;
                        $inquiryItems->inquiry_id = $data->id;
                        $inquiryItems->stock_id = $item['name'];
                        $inquiryItems->quantity = $item['quantity'];
                        $inquiryItems->installments = $item['installment'];
                        $inquiryItems->duration = $item['duration'];
                        $inquiryItems->save();
                    }
                }
            }

            if ($request->status == 2 && !$approved) {
                $checkIfCustExists = $this->model->with('inquiry_items', 'prospect')->where('id', $data->id)->first();

                $accountCustomer = AccountCustomer::where('email', $checkIfCustExists->prospect->email)->first();

                if (!$accountCustomer) {
                    $accountCustomer = new AccountCustomer;
                    $accountCustomer->email = $checkIfCustExists->prospect->email;
                    $accountCustomer->work_number = $checkIfCustExists->prospect->work_number;
                    $accountCustomer->phone_number = $checkIfCustExists->prospect->phone_number;
                    $accountCustomer->secondry_phone_number = $checkIfCustExists->prospect->secondary_phone_number;
                    $accountCustomer->prospect_customer_id = $checkIfCustExists->prospect->id;
                    $accountCustomer->town = $checkIfCustExists->prospect->town;
                    $accountCustomer->save();
                }

                $contract = Contract::where('inquiry_id', $checkIfCustExists->id)->first();

                $nextContractNo = Contract::count();
                if (!$contract) {
                    $contract = new Contract;
                    $contract->contract_number = str_pad($nextContractNo + 1, 3, "0", STR_PAD_LEFT) . '/' . Carbon::now()->format('Y');
                    $contract->name = $checkIfCustExists->prospect->full_name;
                    $contract->document_number = 0;
                    $contract->sale_date = Carbon::now();
                    $contract->inquiry_id = $checkIfCustExists->id;
                    $contract->account_customer_id = $accountCustomer->id;
                    $contract->save();
                }

                foreach ($checkIfCustExists->inquiry_items as $items) {
                    $contractItems = new ContractItems;
                    $contractItems->contract_id = $contract->id;
                    $contractItems->stock_id = $items->stock_id;
                    $contractItems->quantity = $items->quantity;
                    $contractItems->installments = $items->installments;
                    $contractItems->duration = $items->duration;
                    $contractItems->save();
                }
            }
        });
        session()->flash('success', 'Inquiry Added Successfully');
        return MainLogic::save($subdomain, $data, $state, [], self::redirect, self::route);
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

        $request->validate([
            'shops_id' => ['required',],
            'prospect_customer_id' => ['required',],
            //validate
        ]);
    }

    public  function tableColumns() //variable
    {
        return [
            'Inquiry No.' => 'inquiry_number',
            'Shop' => ['relationship', 'shops', 'shop_name'],
            'Prospect Customer' => ['relationship', 'prospect', 'full_name'],
            'Date' => 'date',
            'Status' => ['label', 'default', 'status', ['1' => 'Approved', '0' => 'Rejected', null => 'Pending']],

            //table columns
        ];
    }
    private static  function tableSubColumns()
    {
        return [
            'Item' => 'stock_id',
            'Quantity' => 'quantity',
            'Installment' => 'installments',
            'Period In Months' => 'duration',
        ];
    }

    public  function dbfields() //variable
    {
        return [
            'shops_id', 'prospect_customer_id', 'date', 'notes', 'status'

        ];
    }

    public  function fields($forEdit) //variable
    {


        $values = [
            // 'inquiry_number'=>
            // [
            // 	'text','string','Inquiry No.',4,true,true,'inquiry_number','inquiry_number','Enter Inquiry No.'
            // ],

            'prospect_customer_id' =>
            [
                'select', 'select', 'Prospect Customer', 6, true, true, 'prospect_customer_id', 'prospect_customer_id', 'Select prospect_customer_id', '', ProspectCustomer::all(), isset($forEdit) ? Inquiry::with('prospect')->where('id', $forEdit->id)->first()->prospect : ''
            ],
            'shops_id' =>
            [
                'select', 'select', 'Select Shop', 6, true, true, 'shops_id', 'shops_id', 'Select shops_id', '', Shops::all(), isset($forEdit) ? Inquiry::with('shops')->where('id', $forEdit->id)->first()->shops : ''
            ],
            'date' =>
            [
                'dateTime', 'dateTime', 'Date', 6, true, true, 'date0', 'date', 'Enter Date'
            ],
            'status' =>
            [
                'select', 'select', 'Status', 6, true, true, 'status', 'status', 'Select Status', '', Status::all(), isset($forEdit) ? Inquiry::with('allStatus')->where('id', $forEdit->id)->first()->allStatus : ''
            ],
            'notes' =>
            [
                'textarea', 'text', 'Notes', 12, true, true, 'notes', 'notes', 'Enter Notes'
            ],

            //input fields
        ];

        if (!$forEdit || $forEdit->status) {
            unset($values['status']);
        }

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
        $modelName = 'Inquiry';
        $model = 'App\Model';
        $columns = [
            'inquiry_number', 'shops_id', 'prospect_customer_id', 'date', 'notes'

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
            'Inquiry No.', 'Shop', 'Prospect Customer', 'Date', 'Notes'
            //headings
        ];
        $data = $this->model->select(
            'inquiry_number',
            'shops_id',
            'prospect_customer_id',
            'date',
            'notes'
        )->get(); //variable

        if ($formatType == 'xlsx') {
            return Excel::download(new MainExport($data, $format, $styles, $headings, 'Excel '), self::route . '.xlsx');
        } elseif ($formatType == 'pdf') {
            return (new MainExport($data, $format, $styles, $headings, 'Pdf '))->download(self::route . '.pdf', \Maatwebsite\Excel\Excel::DOMPDF);
        }
    }
}
