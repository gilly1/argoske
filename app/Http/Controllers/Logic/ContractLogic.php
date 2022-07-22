<?php

namespace App\Http\Controllers\Logic;

use App\Ref;
use App\GlTran;
use App\crmPerson;
use Carbon\Carbon;
use App\crmContact;
use App\custBranch;
use App\DebtorTran;
use App\SalesOrder;
use App\StockMaster;
use App\Model\Status;
use App\debtorsMaster;
use App\Model\Inquiry;
use App\Model\Contract;
use App\ContractPayment;
use App\StockMove;
use App\TransTaxDetails;
use App\SalesOrderDetail;
use App\DebtorTransDetail;
use App\Helpers\AppHelper;
use App\Exports\MainExport;
use App\Model\ContractItems;
use Illuminate\Http\Request;
use App\Imports\ToModelImport;
use App\Model\AccountCustomer;
use App\Http\Controllers\dbsave;
use App\Imports\OnEachRowImport;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;
use App\Repositories\MainRepository;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\Payments\PaymentExport;
use App\Http\Controllers\Logic\MainLogic;
use App\Imports\Payment\ImportPaymentToModel;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use App\Http\Controllers\Logic\AccountCustomerLogic;
use App\Http\Controllers\Logic\InquiryCalculatorLogic;
use App\Model\Employer;

//import class

class ContractLogic
{
    const route = 'contracts';
    const redirect = 'contracts/contracts';
    const routeSubTable = 'contract_items';

    public $model;

    function __construct(
        Contract $model
    ) {
        $this->model = $model;
    }

    public  function table($subdomain)
    {
        $list = (new MainRepository($subdomain . self::route, $this->model->get()))->all();
        return MainLogic::table($subdomain, $list, self::route, ['edit', 'delete', 'view', 'export', 'import'], self::tableColumns());
        // self::tableSubColumns(),self::routeSubTable,'contract_items',['view','edit','delete']);

    }

    public  function view($subdomain, $data)
    {
        if ($data) {
            $vue = "<items-edit id=$data type='Contract' />";
        } else {
            $vue = '<items/>';
        }
        $data = $this->model->where('id', $data)->first();
        if (!($data->account->other_names && $data->account->id_number)) {
            session()->flash('error', 'Fill Customer Details first');
            return redirect('/account_customers/account_customers/' . $data->account->id . '/edit');
        }
        return MainLogic::view($subdomain, $data, self::route, self::fields($data), $vue);
    }
    public static function group_by($array, $key)
    {
        $return = array();
        $return2 = array();
        foreach ($array as $val) {
            $return[$val[$key]][] = $val;
            // $return[] = $val;
        }
        return $return;
    }
    public static function group_data($model, $year = null, $month = null, $employer = null)
    {

        $data = $model->whereHas('contract_items.contractPayment')->whereHas('account.prospect')->get();
        $allData = [];
        foreach ($data as $contract) {
            if ($employer) {
                if ($contract->account->prospect->employer_id != $employer) {
                    continue;
                }
            }
            foreach ($contract->contract_items as $contract_items) {
                foreach ($contract_items->contractPayment as $contractPayment) {
                    if ($year) {
                        if (Carbon::parse($contractPayment->month)->format('Y') !== $year) {
                            continue;
                        }
                    }
                    if ($month) {
                        if (Carbon::parse($contractPayment->month)->format('m-Y') !== $month) {
                            continue;
                        }
                    }
                    $data = [
                        'id' => $contract->id,
                        'name' => $contract->account->other_names . ' ' . $contract->account->last_name,
                        'contract' => $contract->contract_number,
                        'month' => Carbon::parse($contractPayment->month)->format('Y-m-d'),
                        'principle' => $contractPayment->principle,
                        'trans_no' => $contractPayment->trans_no,
                        'intrest' => $contractPayment->intrest,
                        'loading' => $contractPayment->loading,
                        'installment' => $contractPayment->installment,
                        'balance' => $contractPayment->balance,
                        'paid' => $contractPayment->paid,
                        'actual_balance' => $contractPayment->actual_balance,
                    ];
                    array_push($allData, $data);
                }
            }
        }


        return  self::group_by($allData, 'month');
    }
    public  function year($subdomain, $data)
    {
        $year = Carbon::parse($data)->format('Y');
        $data3 = self::group_data($this->model, $year, null);



        return view('argos/all_contract_payment')->with('data', $data3)
            ->with('title', ucwords(implode(' ', preg_split('/(?=[A-Z])/', ucfirst(str_replace('_', ' ', self::route))))))
            ->with('route', self::route)
            ->with('subdomain', $subdomain);
    }
    public  function month(Request $request, $subdomain, $data)
    {
        $month = Carbon::parse($data)->format('m-Y');
        $employers = Employer::all();
        $employer_id = null;
        if ($request->isMethod('post')) {
            $employer_id = $request->employer;
            $data3 = self::group_data($this->model, null, $month, $employer_id);
        } else {
            $data3 = self::group_data($this->model, null, $month);
        }



        return view('argos/all_contract_payment')->with('data', $data3)
            ->with('title', ucwords(implode(' ', preg_split('/(?=[A-Z])/', ucfirst(str_replace('_', ' ', self::route))))))
            ->with('route', self::route)
            ->with('month', $data)
            ->with('employers', $employers)
            ->with('employer_id', $employer_id)
            ->with('subdomain', $subdomain);
    }
    public function group($subdomain)
    {
        $data3 = self::group_data($this->model);

        $month = array_keys($data3);

        return view('argos/all_contract_payment_group')->with('month', $month)
            ->with('title', ucwords(implode(' ', preg_split('/(?=[A-Z])/', ucfirst(str_replace('_', ' ', self::route))))))
            ->with('route', self::route)->with('subdomain', $subdomain);
    }
    public  function show($subdomain, $data)
    {
        $data = $this->model->whereHas('contract_items.contractPayment')->where('id', $data)->first();
        $allData = [];
        // $data2 = $data->contract_items->map(function($contract_items) use ($allData){
        //     return $contract_items->contractPayment->map(function($contractPayment) use ($allData){
        //         $data = [
        //             'month'=>Carbon::parse($contractPayment->month)->format('Y-m-d'),
        //             'principle'=>$contractPayment->principle,
        //             'intrest'=>$contractPayment->intrest,
        //             'loading'=>$contractPayment->loading,
        //             'installment'=>$contractPayment->installment,
        //             'balance'=>$contractPayment->balance,
        //             'paid'=>$contractPayment->paid,
        //             'actual_balance'=>$contractPayment->actual_balance,
        //         ];
        //         array_push($allData,$data);
        //         return $data;
        //     });
        // });

        foreach ($data->contract_items as $contract_items) {
            foreach ($contract_items->contractPayment as $contractPayment) {
                $data = [
                    'month' => Carbon::parse($contractPayment->month)->format('Y-m-d'),
                    'principle' => $contractPayment->principle,
                    'intrest' => $contractPayment->intrest,
                    'loading' => $contractPayment->loading,
                    'installment' => $contractPayment->installment,
                    'balance' => $contractPayment->balance,
                    'paid' => $contractPayment->paid,
                    'actual_balance' => $contractPayment->actual_balance,
                ];
                array_push($allData, $data);
            }
        }

        $data3 = self::group_by($allData, 'month');

        // $month = array_keys($data3);


        // $month = array_column($data3, 'month');
        // array_multisort($month, SORT_ASC, $data3);

        return view('argos/contract_payment')->with('data', $data3)
            ->with('title', ucwords(implode(' ', preg_split('/(?=[A-Z])/', ucfirst(str_replace('_', ' ', self::route))))))
            ->with('route', self::route)
            ->with('subdomain', $subdomain);
        return $data3;

        $data = $this->model->with('account:id,pin_no')->whereHas('contract_items.contractPayment')->where('id', $data)->first();

        $debtorsMaster = debtorsMaster::with('custBranch')->where('tax_id', $data->account->pin_no)->first();
        $branch = $debtorsMaster->custBranch->first();
        // $amount = 
        if ($data->contract_items) {
            $amount = 0;
            foreach ($data->contract_items as $contract_items) {
                //cust_allocations table
                $paymentTerm = $contract_items->contractPayment->whereNull('paid')->first();

                $amount += $paymentTerm->installment;
            }

            $debtor_no = $debtorsMaster->debtor_no;
            $branch_code = $branch->branch_code;
            $trans_date = Carbon::now()->format('Y-m-d');

            $newRefNo = self::refNo(10);
            $debtor_trans = new DebtorTran;
            $debtor_trans->trans_no = $newRefNo[1]->first() ? $newRefNo[1]->first()->trans_no + 1 : 1;
            $debtor_trans->type = 10;
            $debtor_trans->version = 0;
            $debtor_trans->debtor_no = $debtor_no;
            $debtor_trans->branch_code = $branch_code;
            $debtor_trans->tran_date = $trans_date;
            $debtor_trans->due_date = $trans_date;
            $debtor_trans->reference = $newRefNo[0];
            $debtor_trans->ov_amount = $amount;
            $debtor_trans->alloc = 0;
            $debtor_trans->save();
        }
        return MainLogic::show($subdomain, $data, self::route, self::fields($data));
    }

    private static function refNo($no)
    {
        $ref_no = DebtorTran::where('type', $no)->orderBy('trans_no', 'desc')->get();
        $newRefNo = str_pad(count($ref_no) + 1, 3, "0", STR_PAD_LEFT) . '/' . Carbon::now()->format('Y');
        $refId = Ref::where('type', $no)->orderBy('id', 'desc')->first();
        return [$newRefNo, $ref_no, ($refId ? $refId->id + 1 : 0)];
    }

    public  function save($subdomain, $request, $data, $state)
    {

        if ($state == 'save') {
            self::validated($request);

            $data = new $this->model;
            // $data->sign=AppHelper::image($request,'sign','logo');
        } else {
            $data = $this->model->where('id', $data)->first();

            // AppHelper::deleteImage($request,$data,'sign');
            // $data->sign=AppHelper::image($request,'sign','logo');
        }

        DB::transaction(function () use ($subdomain, $request, $data, $state) {
            dbsave::main($data, $request, self::dbfields());
            $log = $data->save();
            if ($state == 'save') {
                foreach ($request->fields as $item) {
                    $contractItems = new ContractItems;
                    $contractItems->contract_id = $data->id;
                    $contractItems->stock_id = $item['name'];
                    $contractItems->quantity = $item['quantity'];
                    $contractItems->installments = $item['installment'];
                    $contractItems->duration = $item['duration'];
                    $contractItems->save();
                }
            } else {
                $allcontractItems = ContractItems::where('contract_id', $data->id)->get();

                foreach ($request->fields as $item) {
                    if ($item['id']) {
                        $contractItems = $allcontractItems->where('id', $item['id'])->first();
                        $contractItems->quantity = $item['quantity'];
                        $contractItems->installments = $item['installment'];
                        $contractItems->duration = $item['duration'];
                        $contractItems->save();
                    } else {
                        $contractItems = new ContractItems;
                        $contractItems->contract_id = $data->id;
                        $contractItems->stock_id = $item['name'];
                        $contractItems->quantity = $item['quantity'];
                        $contractItems->installments = $item['installment'];
                        $contractItems->duration = $item['duration'];
                        $contractItems->save();
                    }
                }
                foreach ($allcontractItems as $allcontractItem) {
                    [$installments, $total_cash_pay, $duration, $intrest_for_one_month, $loading, $principle_for_one_month, $currency] = InquiryCalculatorLogic::price_calculator($allcontractItem->stock_id, $allcontractItem->installments, $allcontractItem->duration, $allcontractItem->quantity);
                    if (!$installments || !$total_cash_pay || !$duration) {
                        back()->with('error', 'Prices Not Set!');
                        return false;
                    }
                }

                if ($request->status == 2) {
                    $customer = AccountCustomer::with('prospect.employer')->where('id', $data->account_customer_id)->first();
                    if (!$customer->id_number && !$customer->email) {
                        $data->status = 1;
                        $data->save();

                        session()->flash('error', 'Update Customer Id No and Email First');
                    }



                    $crmPerson = crmPerson::where('id_number', $customer->id_number)->first();
                    $newCrmPerson = false;
                    if (!$crmPerson) {
                        $newCrmPerson = true;
                        $crmPerson = new crmPerson;
                    }
                    $crmPerson->ref  = $customer->last_name . '-' . $customer->id_number;
                    $crmPerson->name  = $customer->other_names . ' ' . $customer->last_name;
                    $crmPerson->address  = strip_tags($customer->address);
                    $crmPerson->phone  = $customer->phone_number;
                    $crmPerson->phone2  = $customer->secondry_phone_number;
                    $crmPerson->email  = $customer->email;
                    $crmPerson->notes  = '';
                    $crmPerson->id_number  = $customer->id_number;
                    $crmPerson->save();

                    if ($newCrmPerson) {

                        // $debtorsMaster = new debtorsMaster;
                        // $debtorsMaster->name  = $customer->other_names;
                        // $debtorsMaster->debtor_ref  = $customer->last_name . '-' . $customer->id_number;
                        // $debtorsMaster->address  = strip_tags($customer->address);
                        // $debtorsMaster->tax_id  = $customer->pin_no ?? ' ';
                        // $debtorsMaster->curr_code  = 'KES';
                        // $debtorsMaster->credit_status  = 1;
                        // $debtorsMaster->payment_terms  = 4;
                        // $debtorsMaster->notes  = ' ';
                        // $debtorsMaster->save();

                        $custBranch = new custBranch;
                        $custBranch->br_name  = $customer->other_names;
                        $custBranch->branch_ref  = $customer->last_name . '-' . $customer->id_number;
                        $custBranch->br_address  = strip_tags($customer->address);
                        $custBranch->debtor_no  = $customer->prospect->employer->debtor_no;
                        $custBranch->default_location  = 'DEF';
                        $custBranch->br_post_address  = strip_tags($customer->address);
                        $custBranch->tax_group_id  = 1;
                        $custBranch->area  = 1;
                        $custBranch->salesman  = 1;
                        $custBranch->sales_discount_account  = 4510;
                        $custBranch->receivables_account  = 1200;
                        $custBranch->payment_discount_account = 4500;
                        $custBranch->bank_account  = $customer->back_account_number;
                        $custBranch->notes  = '';
                        $custBranch->save();

                        $crmContacts = new crmContact;
                        $crmContacts->person_id  = $crmPerson->id;
                        $crmContacts->type  = 'customer';
                        $crmContacts->action  = 'general';
                        $crmContacts->entity_id  = $custBranch->id;
                        $crmContacts->save();
                    }


                    $crm_contact = crmContact::where('type', 'customer')->where('person_id', $crmPerson->id)->first();
                    $branch = custBranch::where('branch_code', $crm_contact->entity_id)->first();
                    $debtorsMaster = $branch->debtorsMaster;


                    $allcontractItems = ContractItems::where('contract_id', $data->id)->get();

                    $AllSalesOrder = SalesOrder::orderBy('order_no', 'desc')->first();
                    $salesOder = new SalesOrder;
                    $salesOder->order_no = $AllSalesOrder ? $AllSalesOrder->order_no + 1 : 1;
                    $salesOder->trans_type = 30;
                    $salesOder->version = 1;
                    $salesOder->type = 0;
                    $salesOder->debtor_no = $debtorsMaster->debtor_no;
                    $salesOder->branch_code = isset($branch->branch_code) ? $branch->branch_code : $debtorsMaster->custBranch->first()->branch_code;
                    // $salesOder->reference = $debtorsMaster;//generate unique ref
                    $salesOder->customer_ref = ' ';
                    $salesOder->comments = ' ';
                    $salesOder->ord_date = Carbon::parse($request->sale_date)->format('Y-m-d');
                    $salesOder->order_type = 1;
                    $salesOder->ship_via = 1;
                    $salesOder->delivery_address = strip_tags($customer->address);
                    $salesOder->contact_phone = $customer->phone_number;
                    $salesOder->contact_email = $customer->email;
                    $salesOder->deliver_to = strip_tags($customer->address);
                    $salesOder->freight_cost = 0;
                    $salesOder->from_stk_loc = 'DEF';
                    $salesOder->delivery_date = Carbon::parse($request->sale_date)->addDays(5)->format('Y-m-d');
                    $salesOder->payment_terms = 1; //important for loan
                    $salesOder->total = 1000; //total sales amount
                    $salesOder->prep_amount = 0;
                    $salesOder->alloc = 0;
                    $salesOder->reference = 'auto';
                    $salesOder->save();

                    $debtor_no = $debtorsMaster->debtor_no;
                    $branch_code = $branch->branch_code;
                    $trans_date = Carbon::now()->format('Y-m-d');

                    //for sales order
                    $newRefNo = self::refNo(13);
                    $debtor_trans1 = new DebtorTran;
                    $debtor_trans1->trans_no = $newRefNo[1]->first() ? $newRefNo[1]->first()->trans_no + 1 : 1;
                    $debtor_trans1->type = 13;
                    $debtor_trans1->version = 1;
                    $debtor_trans1->tpe = 1;
                    $debtor_trans1->ship_via = 1;
                    $debtor_trans1->tax_included = 1;
                    $debtor_trans1->debtor_no = $debtor_no;
                    $debtor_trans1->branch_code = $branch_code;
                    $debtor_trans1->tran_date = $trans_date;
                    $debtor_trans1->due_date = $trans_date;
                    $debtor_trans1->reference = 'auto';
                    $debtor_trans1->order_ = $salesOder->order_no;
                    $debtor_trans1->ov_amount = 0;
                    $debtor_trans1->alloc = 0;
                    $debtor_trans1->tax_included = 0; //taxed or not
                    $debtor_trans1->save();

                    //for invoice
                    $newRefNo = self::refNo(10);
                    $debtor_trans = new DebtorTran;
                    $debtor_trans->trans_no = $newRefNo[1]->first() ? $newRefNo[1]->first()->trans_no + 1 : 1;
                    $debtor_trans->type = 10;
                    $debtor_trans->version = 0;
                    $debtor_trans->tpe = 1;
                    $debtor_trans->ship_via = 1;
                    $debtor_trans->tax_included = 1;
                    $debtor_trans->debtor_no = $debtor_no;
                    $debtor_trans->branch_code = $branch_code;
                    $debtor_trans->tran_date = $trans_date;
                    $debtor_trans->due_date = $trans_date;
                    $debtor_trans->reference = $newRefNo[0];
                    $debtor_trans->order_ = $salesOder->order_no;
                    $debtor_trans->ov_amount = 0;
                    $debtor_trans->alloc = 0;
                    $debtor_trans->tax_included = 0; //taxed or not
                    $debtor_trans->save();

                    $ref = new Ref;
                    $ref->id = $newRefNo[2];
                    $ref->type = 10;
                    $ref->reference = $newRefNo[0];
                    $ref->save();

                    $stockMaster = StockMaster::all();
                    $tested = [];
                    $total_sales_oder_amount = 0;
                    $finance_charges_amount = 0;
                    foreach ($allcontractItems as $allcontractItem) {
                        [$installments, $total_cash_pay, $duration, $intrest_for_one_month, $loading, $principle_for_one_month, $currency, $unit_price] = InquiryCalculatorLogic::price_calculator($allcontractItem->stock_id, $allcontractItem->installments, $allcontractItem->duration, $allcontractItem->quantity);
                        // dd([$installments, $total_cash_pay, $duration, $intrest_for_one_month, $loading_amount_for_one_month, $currency, $amount]);
                        $salesOderDetails = new SalesOrderDetail;
                        $salesOderDetails->order_no  = $salesOder->order_no;
                        $salesOderDetails->trans_type  = $salesOder->trans_type;
                        $salesOderDetails->stk_code  = $allcontractItem->stock_id;
                        $salesOderDetails->description  = $stockMaster->where('stock_id', $allcontractItem->stock_id)->first()->description;
                        $salesOderDetails->qty_sent  = $allcontractItem->quantity;
                        $salesOderDetails->invoiced  = 0;
                        // $salesOderDetails->unit_price  = $total_cash_pay * $allcontractItem->quantity;
                        $salesOderDetails->unit_price  = $unit_price / $allcontractItem->quantity;
                        $salesOderDetails->quantity  = $allcontractItem->quantity;
                        $salesOderDetails->save();

                        $finance_charges_amount += ($total_cash_pay - $unit_price);
                        $total_sales_oder_amount += $total_cash_pay;


                        $debtor_trans_details1 = new DebtorTransDetail;
                        $debtor_trans_details1->debtor_trans_no = $debtor_trans1->trans_no;
                        $debtor_trans_details1->debtor_trans_type = 13;
                        $debtor_trans_details1->stock_id  = $salesOderDetails->stk_code;
                        $debtor_trans_details1->description  = $salesOderDetails->description;
                        $debtor_trans_details1->unit_price  = $unit_price / $allcontractItem->quantity;
                        $debtor_trans_details1->quantity  = $allcontractItem->quantity;
                        $debtor_trans_details1->unit_tax  = 1; //tax
                        $debtor_trans_details1->src_id = 1;
                        $debtor_trans_details1->save();


                        $debtor_trans_details = new DebtorTransDetail;
                        $debtor_trans_details->debtor_trans_no = $debtor_trans->trans_no;
                        $debtor_trans_details->debtor_trans_type = 10;
                        $debtor_trans_details->stock_id  = $salesOderDetails->stk_code;
                        $debtor_trans_details->description  = $salesOderDetails->description;
                        $debtor_trans_details->unit_price  = $unit_price / $allcontractItem->quantity;
                        $debtor_trans_details->unit_tax  = 1; //tax
                        $debtor_trans_details->src_id = 1;
                        $debtor_trans_details->quantity  = $allcontractItem->quantity;
                        $debtor_trans_details->save();


                        $stockMove = new StockMove;
                        $stockMove->trans_no = $debtor_trans1->trans_no;
                        $stockMove->stock_id = $salesOderDetails->stk_code;
                        $stockMove->type = 13;
                        $stockMove->loc_code = 'DEF';
                        $stockMove->tran_date = $trans_date;
                        $stockMove->price = $unit_price;
                        $stockMove->reference = 'auto';
                        $stockMove->qty = -$allcontractItem->quantity;
                        $stockMove->standard_cost = $unit_price;
                        $stockMove->save();

                        $gl_trans = new GlTran;
                        $gl_trans->type = 10;
                        $gl_trans->type_no = $debtor_trans->trans_no; //cust_allocations id
                        $gl_trans->tran_date = $trans_date;
                        $gl_trans->account = 1200; //for -ve and 1060 for +ve
                        $gl_trans->memo_ = ' ';
                        $gl_trans->amount = $unit_price; //also for +ve account
                        $gl_trans->person_type_id = 2; //null when account is +ve
                        $gl_trans->person_id = $debtor_no;
                        $gl_trans->save();

                        $gl_trans = new GlTran;
                        $gl_trans->type = 10;
                        $gl_trans->type_no = $debtor_trans->trans_no; //cust_allocations id
                        $gl_trans->tran_date = $trans_date;
                        $gl_trans->account = 4010; //for -ve and 1060 for +ve
                        $gl_trans->memo_ = ' ';
                        $gl_trans->amount = - ($unit_price); //also for +ve account
                        $gl_trans->save();

                        $gl_trans = new GlTran;
                        $gl_trans->type = 10;
                        $gl_trans->type_no = $debtor_trans->trans_no; //cust_allocations id
                        $gl_trans->tran_date = $trans_date;
                        $gl_trans->account = 2150; //for -ve and 1060 for +ve sales tax
                        $gl_trans->memo_ = ' ';
                        $gl_trans->amount = 0; //also for +ve account
                        $gl_trans->save();

                        self::createSchedule($unit_price, $installments, $intrest_for_one_month, $loading / $duration, 0, $data->id, $request->repayment_date, $allData = [], $total_cash_pay, $debtor_trans->trans_no);



                        // DB::table('debtor_trans_details')
                        // ->where('debtor_trans_no', $debtor_trans_details1->debtor_trans_no)
                        // ->where('debtor_trans_type', $debtor_trans_details1->debtor_trans_type)
                        // ->where('stock_id', $debtor_trans_details1->stock_id)
                        // ->update(['src_id' => $salesOderDetails->id]);

                        DB::table('debtor_trans_details')
                            ->where('debtor_trans_no', $debtor_trans_details->debtor_trans_no)
                            ->where('debtor_trans_type', $debtor_trans_details->debtor_trans_type)
                            ->where('stock_id', $debtor_trans_details->stock_id)
                            ->update(['src_id' => $debtor_trans_details1->id]);
                    }
                    self::add_finance_charges($salesOder, $debtor_trans, $debtor_trans1, $finance_charges_amount, $trans_date, $debtor_no, $stockMaster);

                    //add finance charges as a service

                    // //update sales order actual total
                    // $salesOder->total = $total_sales_oder_amount; 
                    // $salesOder->save();
                    // //update debtor trans actual total
                    // $debtor_trans->ov_amount = $total_sales_oder_amount;
                    // $debtor_trans->save();

                    DB::table('sales_orders')
                        ->where('order_no', $salesOder->order_no)
                        ->update(['total' => $total_sales_oder_amount]);
                    DB::table('debtor_trans')
                        ->where('trans_no', $debtor_trans->trans_no)
                        ->where('reference', $newRefNo[0])
                        ->where('type', '10')
                        ->update(['ov_amount' => $total_sales_oder_amount]);
                    DB::table('debtor_trans')
                        ->where('trans_no', $debtor_trans1->trans_no)
                        ->where('reference', 'auto')
                        ->where('type', '13')
                        ->update(['ov_amount' => $total_sales_oder_amount]);


                    $trans_tax_details1 = new TransTaxDetails;
                    $trans_tax_details1->trans_type = 13;
                    $trans_tax_details1->trans_no = $debtor_trans1->trans_no;
                    $trans_tax_details1->tran_date = $trans_date;
                    $trans_tax_details1->tax_type_id = 1;
                    $trans_tax_details1->rate = 0;
                    $trans_tax_details1->ex_rate = 1;
                    $trans_tax_details1->included_in_price = 1;
                    $trans_tax_details1->net_amount = $total_sales_oder_amount;
                    $trans_tax_details1->memo = 'auto';
                    $trans_tax_details1->reg_type = null;
                    $trans_tax_details1->save();


                    $trans_tax_details = new TransTaxDetails;
                    $trans_tax_details->trans_type = 10;
                    $trans_tax_details->trans_no = $debtor_trans->trans_no;
                    $trans_tax_details->tran_date = $trans_date;
                    $trans_tax_details->tax_type_id = 1;
                    $trans_tax_details->rate = 0;
                    $trans_tax_details->ex_rate = 1;
                    $trans_tax_details->included_in_price = 1;
                    $trans_tax_details->net_amount = $total_sales_oder_amount;
                    $trans_tax_details->memo = $debtor_trans->reference;
                    $trans_tax_details->reg_type = 0;
                    $trans_tax_details->save();
                }
            }
        });
        return MainLogic::save($subdomain, $data, $state, [], self::redirect, self::route);
    }

    protected static function add_finance_charges($salesOder, $debtor_trans, $debtor_trans1, $finance_charges_amount, $trans_date, $debtor_no, $stockMaster)
    {
        $finance_charges_code = 'SERV1';
        $salesOderDetails = new SalesOrderDetail;
        $salesOderDetails->order_no  = $salesOder->order_no;
        $salesOderDetails->trans_type  = $salesOder->trans_type;
        $salesOderDetails->stk_code  = $finance_charges_code;
        $salesOderDetails->description  = $stockMaster->where('stock_id', $finance_charges_code)->first()->description;
        $salesOderDetails->qty_sent  = 1;
        $salesOderDetails->invoiced  = 0;
        $salesOderDetails->unit_price  = $finance_charges_amount;
        $salesOderDetails->quantity  = 1;
        $salesOderDetails->save();


        $debtor_trans_details1 = new DebtorTransDetail;
        $debtor_trans_details1->debtor_trans_no = $debtor_trans1->trans_no;
        $debtor_trans_details1->debtor_trans_type = 13;
        $debtor_trans_details1->stock_id  = $salesOderDetails->stk_code;
        $debtor_trans_details1->description  = $salesOderDetails->description;
        $debtor_trans_details1->unit_price  = $finance_charges_amount;
        $debtor_trans_details1->quantity  = 1;
        $debtor_trans_details1->unit_tax  = 1; //tax
        $debtor_trans_details1->src_id = 1;
        $debtor_trans_details1->save();


        $debtor_trans_details = new DebtorTransDetail;
        $debtor_trans_details->debtor_trans_no = $debtor_trans->trans_no;
        $debtor_trans_details->debtor_trans_type = 10;
        $debtor_trans_details->stock_id  = $salesOderDetails->stk_code;
        $debtor_trans_details->description  = $salesOderDetails->description;
        $debtor_trans_details->unit_price  = $finance_charges_amount;
        $debtor_trans_details->unit_tax  = 1; //tax
        $debtor_trans_details->src_id = 1;
        $debtor_trans_details->quantity  = 1;
        $debtor_trans_details->save();


        $stockMove = new StockMove;
        $stockMove->trans_no = $debtor_trans1->trans_no;
        $stockMove->stock_id = $salesOderDetails->stk_code;
        $stockMove->type = 13;
        $stockMove->loc_code = 'DEF';
        $stockMove->tran_date = $trans_date;
        $stockMove->price = $finance_charges_amount;
        $stockMove->reference = 'auto';
        $stockMove->qty = -1;
        $stockMove->standard_cost = $finance_charges_amount;
        $stockMove->save();

        $gl_trans = new GlTran;
        $gl_trans->type = 10;
        $gl_trans->type_no = $debtor_trans->trans_no; //cust_allocations id
        $gl_trans->tran_date = $trans_date;
        $gl_trans->account = 1200; //for -ve and 1060 for +ve
        $gl_trans->memo_ = ' ';
        $gl_trans->amount = $finance_charges_amount; //also for +ve account
        $gl_trans->person_type_id = 2; //null when account is +ve
        $gl_trans->person_id = $debtor_no;
        $gl_trans->save();

        $gl_trans = new GlTran;
        $gl_trans->type = 10;
        $gl_trans->type_no = $debtor_trans->trans_no; //cust_allocations id
        $gl_trans->tran_date = $trans_date;
        $gl_trans->account = 4010; //for -ve and 1060 for +ve
        $gl_trans->memo_ = ' ';
        $gl_trans->amount = -$finance_charges_amount; //also for +ve account
        $gl_trans->save();
    }
    protected static function createSchedule($amount, $installment, $intrest, $loading, $i, $contract_id, $repayment_date, $allData, $total_cash_pay, $trans_no)
    {
        $intrest_amount = $amount * ($intrest / 12);
        if ($amount > $installment) {
            $balance = $amount -  $installment + $intrest_amount;

            if ($amount > 0) {
                $allData[] = [
                    'principle' => $installment - $intrest_amount,
                    'trans_no' => $trans_no,
                    'intrest' => $intrest_amount,
                    'loading' => $loading,
                    'installment' => $installment + $loading,
                    'balance' => $balance,
                    'actual_balance' => $total_cash_pay,
                    'contract_items_id' => $contract_id,
                    'month' => Carbon::parse($repayment_date)->copy()->startOfMonth()->addMonths($i),
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now()
                ];
                $i++;
            }
            self::createSchedule($balance, $installment, $intrest, $loading, $i, $contract_id, $repayment_date, $allData, $total_cash_pay, $trans_no);
        } else {
            $balance = $amount - $intrest - $loading;

            if ($amount > 0) {
                $allData[] = [
                    'principle' => $amount,
                    'trans_no' => $trans_no,
                    'intrest' => $intrest_amount,
                    'loading' => $loading,
                    'installment' => $amount + $loading + $intrest_amount,
                    'balance' => 0,
                    'actual_balance' => $total_cash_pay,
                    'contract_items_id' => $contract_id,
                    'month' => Carbon::parse($repayment_date)->copy()->startOfMonth()->addMonths($i),
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now()
                ];
            }

            // return $allData;

            ContractPayment::insert($allData);
        }
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






            //validate
        ]);
    }

    public  function tableColumns() //variable
    {
        return [
            'Contract No' => 'contract_number',
            'Name' => 'name',
            'Document No' => 'document_number',
            'Sale Date' => 'sale_date',
            // 'Customer'=>['relationship','account','other_names'],
            'Status' => ['label', 'default', 'status', ['2' => 'Approved', '1' => 'Rejected', null => 'Pending', '0' => 'Pending']],

            //table columns
        ];
    }
    private static  function tableSubColumns()
    {
        return [
            // 'Item'=>['relationship','stockMaster','description'],
            'Item' => 'stock_id',
            'Quantity' => 'quantity',
            'Installment' => 'installments',
            'Period In Months' => 'duration',
        ];
    }

    public  function dbfields() //variable
    {
        return [
            'name', 'document_number', 'sale_date', 'repayment_date', 'account_customer_id', 'inquiry_id', 'status'

        ];
    }

    public  function fields($forEdit) //variable
    {


        $values = [
            // 'contract_number'=>
            // [
            // 	'text','string','Contract No',6,true,true,'contract_number','contract_number','Enter Contract No'
            // ],
            'name' =>
            [
                'text', 'string', 'Name As Per Payslip', 6, true, true, 'name', 'name', 'Enter Name As Per Payslip'
            ],
            'document_number' =>
            [
                'text', 'string', 'Document No', 6, true, true, 'document_number', 'document_number', 'Enter Document No'
            ],
            'sale_date' =>
            [
                'dateTime', 'dateTime', 'Sale Date', 6, true, true, 'date0', 'sale_date', 'Enter Sale Date'
            ],
            'account_customer_id' =>
            [
                'select', 'select', 'Choose Account Customer', 6, true, true, 'account_customer_id', 'account_customer_id', 'Select account_customer_id', '', AccountCustomer::all(), isset($forEdit) ? Contract::with('account')->where('id', $forEdit->id)->first()->account : ''
            ],
            'inquiry_id' =>
            [
                'select', 'select', 'Choose Inquiry', 6, true, true, 'inquiry_id', 'inquiry_id', 'Select inquiry', '', Inquiry::all(), isset($forEdit) ? Contract::with('inquiry')->where('id', $forEdit->id)->first()->inquiry : ''
            ],
            'status' =>
            [
                'select', 'select', 'Status', 4, true, true, 'status', 'status', 'Select Status', '', Status::all(), isset($forEdit) ? Contract::with('allStatus')->where('id', $forEdit->id)->first()->allStatus : ''
            ],
            'repayment_date' =>
            [
                'dateTime', 'dateTime', 'Repayment Date', 6, true, true, 'date1', 'repayment_date', 'Enter Repayment Date'
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
        $modelName = 'Contract';
        $model = 'App\Model';
        $columns = [
            'contract_number', 'name', 'document_number', 'sale_date', 'repayment_date', 'account_customer_id', 'contract_id'

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
            'Contract No', 'Name As Per Payslip', 'Document No', 'Sale Date', 'Account Customer', 'Inquiry'
            //headings
        ];
        $data = $this->model->select(
            'contract_number',
            'name',
            'document_number',
            'sale_date',
            'repayment_date',
            'account_customer_id',
            'contract_id'
        )->get(); //variable

        if ($formatType == 'xlsx') {
            return Excel::download(new MainExport($data, $format, $styles, $headings, 'Excel '), self::route . '.xlsx');
        } elseif ($formatType == 'pdf') {
            return (new MainExport($data, $format, $styles, $headings, 'Pdf '))->download(self::route . '.pdf', \Maatwebsite\Excel\Excel::DOMPDF);
        }
    }
    public function export_month($subdomain, $formatType, $data, $employer_id)
    {
        $employer = Employer::find($employer_id);
        // if ( $permission = AppHelper::permissions('export_'.self::route) )  return $permission;
        $format = [ //variable
            'C' => NumberFormat::FORMAT_DATE_DDMMYYYY,
        ];
        $styles = [ //variable
            // Style the first row as bold text.
            // '1'   => ['font' => ['bold' => true]],
            // '2'   => ['font' => ['bold' => true]],
            // '4'   => ['font' => ['bold' => true]],
            // // Styling a specific cell by coordinate.
            // 'B' => ['font' => ['italic' => true]],
        ];
        $headings = [ //variable
            'Contract No', 'Name', 'Month', 'Principle', 'Interest ', 'Loading', 'Balance', 'Paid ', 'Actual Balance'
            //headings
        ];

        $month = Carbon::parse($data)->format('m-Y');
        $data = self::group_data($this->model, null, $month, $employer_id);

        if ($formatType == 'xlsx') {
            return Excel::download(new PaymentExport($data, $format, $styles, $headings, 'Excel '), $employer->name . '_' . $month . '.xlsx');
        } elseif ($formatType == 'pdf') {
            return (new PaymentExport($data, $format, $styles, $headings, 'Pdf '))->download($employer->name . '_' . $month . '.pdf', \Maatwebsite\Excel\Excel::DOMPDF);
        }
    }
    public function import_month($subdomain, Request $request)
    {
        if ($permission = AppHelper::permissions('import_' . self::route))  return $permission;
        if (!$request->hasFile('file')) {

            return back()->with('error', 'Whoops!! No Attachment Found!');
        }

        // ToModelImport

        $uniqueBy = '';
        $modelName = 'Contract';
        $model = 'App\Model';
        $columns = [
            'id', 'contract', 'customer', 'month', 'paid'

        ];

        try {
            Excel::import(new ImportPaymentToModel($model, $modelName, $uniqueBy, $columns), $request->file);
        } catch (\Maatwebsite\Excel\Validators\ValidationException $e) {
            $failures = $e->failures();
        }


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

        return redirect()->back()->with('success', 'Data Import was successful!');
    }
}
