<?php

namespace App\Http\Controllers\Logic;

use App\StockMaster;
use App\Helpers\AppHelper;
use App\Exports\MainExport;
use App\Model\InquiryItems;
use App\Http\Controllers\dbsave;
use App\Imports\OnEachRowImport;
use App\Model\InquiryCalculator;
use Spatie\Permission\Models\Role;
use App\Repositories\MainRepository;
use Maatwebsite\Excel\Facades\Excel;
use App\Http\Controllers\Logic\MainLogic;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
//import class

class InquiryCalculatorLogic
{
    const route = 'inquiry_calculators';
    const redirect = 'inquiry_calculators/inquiry_calculators';

    public $model;

    function __construct(
        InquiryCalculator $model
    ) {
        $this->model = $model;
    }

    public  function table($subdomain)
    {
        $data = null;
        return MainLogic::view($subdomain, $data, self::route, self::fields($data));
    }

    public  function view($subdomain, $data)
    {
        $data = $this->model->where('id', $data)->first();
        return MainLogic::view($subdomain, $data, self::route, self::fields($data));
    }
    public  function show($subdomain, $data)
    {
        $data = null;
        return MainLogic::view($subdomain, $data, self::route, self::fields($data));
    }

    public  function save($subdomain, $request, $data, $state)
    {

        $routeName = self::route;
        [$installments, $total_cash_pay, $duration, $intrest_for_one_month, $loading_amount_for_one_month, $principle_for_one_month, $currency] = self::price_calculator($request->stock_id, $request->installment, $request->duration, $request->quantity);

        if (!$installments) {
            return back();
        }

        return view('argos/amotization_table')->with('installments', $installments)->with('total_cash_pay', $total_cash_pay)->with('duration', $duration)->with('currency', $currency)
            ->with('intrest_for_one_month', $intrest_for_one_month)->with('loading', $loading_amount_for_one_month)
            ->with('principle_for_one_month', $principle_for_one_month)
            ->with('title', ucwords(implode(' ', preg_split('/(?=[A-Z])/', ucfirst(str_replace('_', ' ', $routeName))))))
            ->with('route', $routeName)
            ->with('subdomain', $subdomain);

        // if($state == 'save')
        // {
        //     self::validated($request);

        //     $data = new $this->model;
        //     // $data->sign=AppHelper::image($request,'sign','logo');
        // }else{
        //     $data = $this->model->where('id',$data)->first();

        //     // AppHelper::deleteImage($request,$data,'sign');
        //     // $data->sign=AppHelper::image($request,'sign','logo');
        // }

        // dbsave::main($data,$request,self::dbfields());
        // $log = $data->save();

        return MainLogic::save($subdomain, $data, $state, $log, self::redirect, self::route);
    }

    public static function price_calculator($stock_id, $installment, $duration, $quantity)
    {

        $stock = StockMaster::with('prices.salesType')->where('stock_id', $stock_id)
            ->select(
                'stock_id',
                'description',
                'purchase_cost',
                'mark_up',
                'mark_up_round_method',
                'mark_up_precision',
                'intrest_rate',
                'intrest_rate_round_method',
                'intrest_rate_precision',
                'loading',
                'loading_discount',
                'loading_discount_round_method',
                'loading_discount_precision'
            )
            ->first();

        if (!($stock->prices->first())) {
            back()->with('error', 'Prices Not Set!');
            return false;
        }


        $amount = $stock->prices->first()->price * $quantity; // to be updated with the selling price
        $currency = $stock->prices->first()->curr_abrev;
        $mark_up = $stock->mark_up;
        $mark_up_round_method = $stock->mark_up_round_method;
        $mark_up_precision = $stock->mark_up_precision;
        $intrest_rate = $stock->intrest_rate;
        $intrest_rate_round_method = $stock->intrest_rate_round_method;
        $intrest_rate_precision = $stock->intrest_rate_precision;
        $loading = $stock->loading;
        if ($loading) {
            $loading_discount = $stock->loading_discount;
            $loading_discount_round_method = $stock->loading_discount_round_method;
            $loading_discount_precision = $stock->loading_discount_precision;
        }

        if (!$intrest_rate) {
            back()->with('error', 'Intrest Rate Not Set!');
            return;
        }

        $raw_processing_fee = $amount * str_replace('%', '', $mark_up ?? 1) / 100;


        $processing_fee = self::roundMethod($mark_up_round_method, $raw_processing_fee, $mark_up_precision);

        $cash_amount = $amount + $processing_fee;


        $raw_intrest_for_one_month = $cash_amount * str_replace('%', '', $intrest_rate) / 100 ?? 1;

        $intrest_for_one_month = self::roundMethod($intrest_rate_round_method, $raw_intrest_for_one_month, $intrest_rate_precision);
        // $intrest_for_one_month = round($raw_intrest_for_one_month,0,PHP_ROUND_HALF_UP);

        $loading_amount_for_one_month = 0;
        $loading_amount_for_n_months = 0;
        if ($loading) {
            $raw_loading_amount_for_one_month = $intrest_for_one_month * str_replace('%', '', $loading_discount) / 100;
            $loading_amount_for_one_month = self::roundMethod($loading_discount_round_method, $raw_loading_amount_for_one_month, $loading_discount_precision);

            // $raw_loading_amount_for_one_month = $intrest_for_one_month * 1.34 * 0.495962555 * 0.55;
            // $loading_amount_for_one_month = self::roundMethod($loading_discount_round_method, $raw_loading_amount_for_one_month, $loading_discount_precision);
        }

        if (isset($installment) && $installment > 0) {
            $duration = ($cash_amount / ($installment - $intrest_for_one_month - ($loading_amount_for_one_month)));
        }
        if ($duration < 0) {
            $installment -= ($installment - $intrest_for_one_month - ($loading_amount_for_one_month));
            back()->with('error', 'Installments Should be atleast ' . ($installment + ($installment * 0.1)));
            return;
        }


        $intrest_for_n_months = $intrest_for_one_month * $duration;

        if ($loading) {

            $loading_amount_for_n_months = $loading_amount_for_one_month * $duration;
        }

        $total_cash_pay = ($cash_amount + $intrest_for_n_months + $loading_amount_for_n_months);

        $installments = ($total_cash_pay) / $duration;
        if (isset($installment) && $installment > 0) {
            $installments = $installment;
        }

        $principle_for_one_month = $cash_amount / $duration;

        // dd([(int)($installments), ($cash_amount / $duration), ($total_cash_pay), $duration, $loading_amount_for_one_month, ($intrest_for_one_month)]);
        return [($installments), ($total_cash_pay), ($duration), ($intrest_for_one_month), ($loading_amount_for_one_month), $principle_for_one_month, $currency, $amount];
        return [ceil($installments), ceil($total_cash_pay), ceil($duration), ceil($intrest_for_one_month), ceil($loading_amount_for_one_month), $principle_for_one_month, $currency, $amount];
    }
    protected static function roundMethod($method, $amount, $precision)
    {
        if ($precision < 0) {
            $precision = 5;
        }
        switch ($method) {
            case "round_up":
                return round($amount, $precision, PHP_ROUND_HALF_UP);
                break;
            case "round_down":
                return round($amount, $precision, PHP_ROUND_HALF_DOWN);
                break;
            case "round_off":
                return round($amount, $precision);
                break;
            default:
                return $amount;
        }
    }

    public  function delete($subdomain, $data)
    {
        $data = null;
        return MainLogic::view($subdomain, $data, self::route, self::fields($data));
    }

    public static  function validated($request)
    { //variable

        $request->validate([

            'installment' => ['required',],
            'duration' => ['required',],
            //validate
        ]);
    }

    public  function tableColumns() //variable
    {
        return [
            'Item' => 'stock_id',
            'Installment' => 'installment',
            'Payment Period' => 'duration',

            //table columns
        ];
    }

    public  function dbfields() //variable
    {
        return [
            'stock_id', 'installment', 'duration'

        ];
    }

    public  function fields($forEdit) //variable
    {


        $values = [
            'stock_id' =>
            [
                'select', 'select', 'Choose Item.', 6, true, true, 'stock_id', 'stock_id', 'Select Item', '', StockMaster::all(), isset($forEdit) ? InquiryItems::with('stockMaster')->where('id', $forEdit->id)->first()->stockMaster : ''
            ],
            'quantity' =>
            [
                'text', 'float', 'Quantity', 6, true, true, 'quantity', 'quantity', 'Enter quantity'
            ],
            'installment' =>
            [
                'text', 'float', 'Installment', 6, true, true, 'installment', 'installment', 'Enter Installment'
            ],
            'duration' =>
            [
                'text', 'float', 'Payment Period', 6, true, true, 'duration', 'duration', 'Enter Payment Period'
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
        $modelName = 'InquiryCalculator';
        $model = 'App\Model';
        $columns = [
            'stock_id', 'installment', 'duration'

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
            'Item', 'Installment', 'Payment Period'
            //headings
        ];
        $data = $this->model->select(
            'stock_id',
            'installment',
            'duration'
        )->get(); //variable

        if ($formatType == 'xlsx') {
            return Excel::download(new MainExport($data, $format, $styles, $headings, 'Excel '), self::route . '.xlsx');
        } elseif ($formatType == 'pdf') {
            return (new MainExport($data, $format, $styles, $headings, 'Pdf '))->download(self::route . '.pdf', \Maatwebsite\Excel\Excel::DOMPDF);
        }
    }
}
