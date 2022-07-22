<?php

namespace App\Imports\Payment;

use App\Ref;
use App\User;
use App\GlTran;
use App\BankTran;
use App\ContractPayment;
use App\crmPerson;
use Carbon\Carbon;
use App\crmContact;
use App\custBranch;
use App\DebtorTran;
use App\debtorsMaster;
use App\CustAllocation;
use App\Model\Contract;
use App\Helpers\AppHelper;
use App\Notifications\tellAdmin;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Events\AfterImport;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithUpserts;
use Illuminate\Support\Facades\Notification;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithChunkReading;

class ImportPaymentToModel implements ToCollection, WithHeadingRow, WithBatchInserts, WithChunkReading, WithValidation, WithUpserts, WithEvents
{

    private
        $model, $uniqueBy;

    function __construct($model, $modelName, $uniqueBy, $columns, $rules = [], $errorMessage = [])
    {
        $this->model = $model;
        $this->rules = $rules;
        $this->columns = $columns;
        $this->uniqueBy = $uniqueBy;
        $this->modelName = $modelName;
        $this->errorMessage = $errorMessage;
        $this->data = [];
    }
    private static function refNo($no)
    {
        $ref_no = DebtorTran::where('type', $no)->orderBy('trans_no', 'desc')->get();
        $newRefNo = str_pad(count($ref_no) + 1, 3, "0", STR_PAD_LEFT) . '/' . Carbon::now()->format('Y');
        $refId = Ref::where('type', $no)->orderBy('id', 'desc')->first();
        return [$newRefNo, $ref_no, ($refId ? $refId->id + 1 : 0)];
    }

    public static function group_data($contract, $month)
    {

        $allData = [];
        foreach ($contract->contract_items as $contract_items) {
            foreach ($contract_items->contractPayment as $contractPayment) {
                if ($month) {
                    if (Carbon::parse($contractPayment->month)->format('m-Y') !== $month) {
                        continue;
                    }
                }
                $data = [
                    // 'id' => $contract->id,
                    'id' => $contractPayment->id,
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

        return $allData;

        return  self::group_by($allData, 'trans_no');
    }
    public static function group_by($array, $key)
    {
        $return = array();
        $return2 = array();
        foreach ($array as $val) {
            $return[$val[$key]][] = $val;
        }
        return $return;
    }
    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */

    public function collection(Collection  $rows)
    {
        DB::transaction(function () use ($rows) {
            $crmPeople = crmPerson::all();
            $crm_contacts = crmContact::all();
            $branches = custBranch::all();
            foreach ($rows as $row) {

                foreach ($this->columns as  $column) {
                    $this->data[$column] = $row[$column];
                }

                $day = Carbon::parse($this->data['month'])->startOfDay()->format('m-Y');
                $contracts = Contract::whereHas('contract_items.contractPayment')->whereHas('account')->where('id', $this->data['id'])->first();

                $payment_schedule = self::group_data($contracts, $day);


                $tax_id = $contracts->account->pin_no;
                $id_number = $contracts->account->id_number;
                $crmPerson = $crmPeople->where('id_number', $id_number)->first();


                $crm_contact = $crm_contacts->where('type', 'customer')->where('person_id', $crmPerson->id)->first();
                $branch = $branches->where('branch_code', $crm_contact->entity_id)->first();
                $debtor = $branch->debtorsMaster;


                // $debtor = debtorsMaster::whereHas('custBranch')->where('tax_id', $tax_id)->first();
                // $branch = $debtor->custBranch->first();
                $total_amount_paid = $this->data['paid'];


                if (!$contracts->account || !$debtor || !$branch || $total_amount_paid <= 0) {
                    //email informing no contract
                    continue;
                }

                $debtor_no = $debtor->debtor_no;
                $branch_code = $branch->branch_code;

                //add customer Allocation

                $trans_date = Carbon::now()->format('Y-m-d');



                foreach ($payment_schedule as $trans_no) {
                    $tr_no = $trans_no['trans_no'];
                    $debtor_transaction = DB::select("
                            SELECT trans.*,ov_amount+ov_gst+ov_freight+ov_freight_tax+ov_discount AS Total,cust.name AS DebtorName, cust.address, cust.debtor_ref, cust.curr_code, cust.tax_id,
                        trans.prep_amount>0 as prepaid,com.memo_, shippers.shipper_name, sales_types.sales_type, sales_types.tax_included, branch.*, cust.discount, tax_groups.name AS tax_group_name, tax_groups.id AS tax_group_id  FROM debtor_trans trans
                                    LEFT JOIN comments com ON trans.type=com.type AND trans.trans_no=com.id
                                    LEFT JOIN shippers ON shippers.shipper_id=trans.ship_via, 
                                    debtors_master cust, sales_types, cust_branch branch, tax_groups  
                                    WHERE trans.trans_no=$tr_no
                        AND trans.type='10'
                        AND trans.debtor_no=cust.debtor_no AND 
                        trans.debtor_no=$debtor_no AND sales_types.id = trans.tpe
                            AND branch.branch_code = trans.branch_code
                            AND branch.tax_group_id = tax_groups.id
                    ");



                    foreach ($debtor_transaction as $dt_trans) {

                        $to_pay = 0;
                        if ($total_amount_paid > $trans_no['installment']) {
                            if ($total_amount_paid > ($dt_trans->ov_amount - $dt_trans->alloc)) {
                                $to_pay = ($dt_trans->ov_amount - $dt_trans->alloc);
                                $total_amount_paid -= ($dt_trans->ov_amount - $dt_trans->alloc);
                            } else {
                                $to_pay = $total_amount_paid;
                                $total_amount_paid = 0;
                            }
                        } else {
                            $to_pay = $total_amount_paid;
                            $total_amount_paid = 0;
                        }
                        if ($to_pay <= 0) {
                            continue;
                        }
                        $debtor_trans_to_be_paid = DebtorTran::where('trans_no', $dt_trans->trans_no)->where('type', 10)
                            ->where('debtor_no', $debtor_no)->where('branch_code', $branch_code)->first();



                        $newRefNo = self::refNo(12);
                        $debtor_trans = new DebtorTran;
                        $debtor_trans->trans_no = $newRefNo[2];
                        $debtor_trans->type = 12;
                        $debtor_trans->version = 0;
                        $debtor_trans->debtor_no = $debtor_no;
                        $debtor_trans->branch_code = $branch_code;
                        $debtor_trans->tran_date = $trans_date;
                        $debtor_trans->due_date = $trans_date;
                        $debtor_trans->reference = $newRefNo[0];
                        $debtor_trans->ov_amount = $to_pay;
                        $debtor_trans->alloc = $to_pay;
                        $debtor_trans->save();


                        // $debtor_trans_to_be_paid->alloc =  $debtor_trans_to_be_paid->alloc + $to_pay;
                        // $debtor_trans_to_be_paid->save();

                        $cust_allocation = new CustAllocation;
                        $cust_allocation->person_id = $debtor_no;
                        $cust_allocation->amt = $to_pay;
                        $cust_allocation->date_alloc = $trans_date;
                        $cust_allocation->trans_type_from = 12;
                        $cust_allocation->trans_type_to = 10;
                        $cust_allocation->trans_no_from = $debtor_trans->trans_no;
                        $cust_allocation->trans_no_to = $tr_no;
                        $cust_allocation->save();


                        $ref = new Ref;
                        $ref->id = $newRefNo[2];
                        $ref->type = 12;
                        $ref->reference = $newRefNo[0];
                        $ref->save();

                        $bank_trans = new BankTran;
                        $bank_trans->type = 12;
                        $bank_trans->trans_no = $cust_allocation->id;
                        $bank_trans->bank_act = 1;
                        $bank_trans->ref = 'auto';
                        $bank_trans->trans_date = $trans_date;
                        $bank_trans->amount = $to_pay;
                        $bank_trans->person_type_id = 2;
                        $bank_trans->person_id = $debtor_no;
                        $bank_trans->save();

                        $gl_trans = new GlTran;
                        $gl_trans->type = 12;
                        $gl_trans->type_no = $cust_allocation->id; //cust_allocations id
                        $gl_trans->tran_date = $trans_date;
                        $gl_trans->account = 1200; //for -ve and 1060 for +ve
                        $gl_trans->memo_ = ' ';
                        $gl_trans->amount = -$to_pay; //also for +ve account
                        $gl_trans->person_type_id = 2; //null when account is +ve
                        $gl_trans->person_id = $debtor_no;
                        $gl_trans->save();

                        $gl_trans = new GlTran;
                        $gl_trans->type = 12;
                        $gl_trans->type_no = $cust_allocation->id; //cust_allocations id
                        $gl_trans->tran_date = $trans_date;
                        $gl_trans->account = 1060; //for -ve and 1060 for +ve
                        $gl_trans->memo_ = ' ';
                        $gl_trans->amount = $to_pay; //also for +ve account
                        $gl_trans->save();

                        $payment = ContractPayment::where('id', $trans_no['id'])->first();
                        $payment->paid = $to_pay;
                        $payment->save();
                    }
                }


                if ($total_amount_paid > 0) {
                    //save this data to refund customer
                    //that will be a new table
                }
            }
        });
    }
    public function uniqueBy()
    {
        $this->uniqueBy;
    }
    public function batchSize(): int
    {
        return 500;
    }
    public function chunkSize(): int
    {
        return 500;
    }
    public function rules(): array
    {
        return $this->rules;
    }
    public function customValidationMessages()
    {
        return $this->errorMessage;
    }
    public function registerEvents(): array
    {
        return [
            // Handle by a closure.
            AfterImport::class => function (AfterImport $event) {
                $message = AppHelper::logfunction('Excel ', $this->modelName, 'Imported');
                Notification::send(AppHelper::admin(), new tellAdmin('info', $message, $this->modelName));
                AppHelper::logs('critical', $message);
            },

        ];
    }
}
