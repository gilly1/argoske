<?php

namespace App\Imports;

use App\User;
use Spatie\Permission\Models\Role;
use App\Model\Dotenv;
use App\Model\SideBar;
use App\Model\ModelMapping;
use App\Model\Approvers;
use App\Model\ApproverStatuses;
use App\Model\Designation;
use App\Model\Hierarchy;
use App\Model\DesignationHierarchy;
use App\Model\ModelsToApprove;
use App\Model\ModelTobeApproved;
use App\Model\Bursary;
use App\Model\RoundMethod;
use App\Model\Regions;
use App\Model\Shops;
use App\Model\Gender;
use App\Model\Nationality;
use App\Model\IdentificationType;
use App\Model\Employer;
use App\Model\ProspectCustomer;
use App\Model\Inquiry;
use App\Model\InquiryItems;
use App\Model\InquiryCalculator;
use App\Model\AccountCustomer;
use App\Model\Contract;
use App\Model\ContractItems;
use App\Model\Guarantor;
use App\Model\Status;
//add import class

class Models
{

    function __construct() 
    {

    }
    public function models($row,$route)
    {
        if($route == 'users')
        {
            $user = User::firstOrCreate([
                'email' => $row['email'],
                'name' => $row['name'],
                'password' => $row['password'],
            ]);
            $role = Role::where('name',$row['role'])->first();
            if(!$role)
            {
                $role = Role::firstOrCreate([
                    'name' => $row['role']
                ]);
            }
            $user->roles()->sync($role->id);
        }
        elseif($route == 'names')
        {

        }
        elseif($route == 'dotenvs')
		{
			return Dotenv::firstOrCreate([
				'company_name'=>$row['company_name'],
			'subdomain'=>$row['subdomain'],
			'host'=>$row['host'],
			'db_user'=>$row['db_user'],
			'db_pass'=>$row['db_pass'],
			
			]);
		}
			elseif($route == 'side_bars')
		{
			return SideBar::firstOrCreate([
				'parent_id'=>$row['parent_id'],
			'title'=>$row['title'],
			'url'=>$row['url'],
			'menu_order'=>$row['menu_order'],
			'status'=>$row['status'],
			'level'=>$row['level'],
			'icon'=>$row['icon'],
			'description'=>$row['description'],
			'custom_title'=>$row['custom_title'],
			'permission'=>$row['permission'],
			
			]);
		}
			elseif($route == 'model_mappings')
		{
			return ModelMapping::firstOrCreate([
				'name'=>$row['name'],
			'approver_model'=>$row['model_to_approve'],
			'approved_model'=>$row['model_to_be_approved'],
			
			]);
		}
			elseif($route == 'approvers')
		{
			return Approvers::firstOrCreate([
				'modelMapping_id'=>$row['model_mapping'],
			'approver_model_id'=>$row['approver'],
			'weight'=>$row['weight'],
			'super_approver'=>$row['super_approver'],
			
			]);
		}
			elseif($route == 'approver_statuses')
		{
			return ApproverStatuses::firstOrCreate([
				'modelMapping_id'=>$row['model_mapping'],
			'approver_model_id'=>$row['approver'],
			'approved_model_id'=>$row['approved_model'],
			'weight'=>$row['weight'],
			'status'=>$row['status'],
			'approved'=>$row['approved'],
			'super_admin'=>$row['super_admin'],
			'reason'=>$row['reason'],
			
			]);
		}
			elseif($route == 'designations')
		{
			return Designation::firstOrCreate([
				'name'=>$row['name'],
			'designation_id'=>$row['parent_designation'],
			
			]);
		}
			elseif($route == 'hierarchies')
		{
			return Hierarchy::firstOrCreate([
				'name'=>$row['name'],
			'description'=>$row['description'],
			
			]);
		}
			elseif($route == 'designation_hierarchies')
		{
			return DesignationHierarchy::firstOrCreate([
				'hierarchy_id'=>$row['hierarchy'],
			'designation_id'=>$row['designation'],
			'parnt_designation'=>$row['parnt_designation'],
			
			]);
		}
			elseif($route == 'models_to_approves')
		{
			return ModelsToApprove::firstOrCreate([
				'name'=>$row['name'],
			'model'=>$row['model'],
			
			]);
		}
			elseif($route == 'model_tobe_approveds')
		{
			return ModelTobeApproved::firstOrCreate([
				'name'=>$row['name'],
			'model'=>$row['model'],
			
			]);
		}
			elseif($route == 'bursaries')
		{
			return Bursary::firstOrCreate([
				'name'=>$row['name'],
			'amount'=>$row['amount'],
			
			]);
		}
			elseif($route == 'round_methods')
		{
			return RoundMethod::firstOrCreate([
				'name'=>$row['name'],
			'function'=>$row['function'],
			
			]);
		}
			elseif($route == 'regions')
		{
			return Regions::firstOrCreate([
				'name'=>$row['name'],
			
			]);
		}
			elseif($route == 'shops')
		{
			return Shops::firstOrCreate([
				'regions_id'=>$row['Region Name'],
			'shop_name'=>$row['Shop Name'],
			'shop_code'=>$row['Shop Code'],
			
			]);
		}
			elseif($route == 'genders')
		{
			return Gender::firstOrCreate([
				'name'=>$row['name'],
			
			]);
		}
			elseif($route == 'nationalities')
		{
			return Nationality::firstOrCreate([
				'name'=>$row['name'],
			
			]);
		}
			elseif($route == 'identification_types')
		{
			return IdentificationType::firstOrCreate([
				'name'=>$row['name'],
			
			]);
		}
			elseif($route == 'employers')
		{
			return Employer::firstOrCreate([
				'number'=>$row['Employer Number'],
			'name'=>$row['Employer Name'],
			'commission_rate'=>$row['Commission Rate'],
			'round_method_id'=>$row['Rounding Method'],
			'precision'=>$row['Rounding Precision'],
			'retirement_age'=>$row['Retirement Age'],
			'accounts'=>$row['Max. Recommended Accounts'],
			
			]);
		}
			elseif($route == 'prospect_customers')
		{
			return ProspectCustomer::firstOrCreate([
				'work_number'=>$row['Work No.'],
			'full_name'=>$row['Full_name'],
			'phone_number'=>$row['Phone No.'],
			'secondary_phone_number'=>$row['Secondary Phone No'],
			'email'=>$row['Email'],
			'employer_id'=>$row['Employer Name'],
			'ability'=>$row['Ability'],
			'town'=>$row['Town'],
			'notes'=>$row['Notes'],
			
			]);
		}
			elseif($route == 'inquiries')
		{
			return Inquiry::firstOrCreate([
				'inquiry_number'=>$row['Inquiry No.'],
			'prospect_customer_id'=>$row['Prospect Customer'],
			'date'=>$row['Date'],
			'notes'=>$row['Notes'],
			
			]);
		}
			elseif($route == 'inquiry_items')
		{
			return InquiryItems::firstOrCreate([
				'inquiry_id'=>$row['Inquiry No.'],
			'stock_id'=>$row['Item'],
			'quantity'=>$row['Quantity'],
			'installments'=>$row['Installment'],
			'duration'=>$row['Period In Months'],
			
			]);
		}
			elseif($route == 'inquiry_calculators')
		{
			return InquiryCalculator::firstOrCreate([
				'stock_id'=>$row['Item'],
			'installment'=>$row['Installment'],
			'duration'=>$row['Payment Period'],
			
			]);
		}
			elseif($route == 'account_customers')
		{
			return AccountCustomer::firstOrCreate([
				'work_number'=>$row['Work No.'],
			'designation'=>$row['Designation'],
			'department'=>$row['Department'],
			'deo'=>$row['DEO'],
			'section'=>$row['Section'],
			'station'=>$row['Station'],
			'gross_salary'=>$row['Gross Salary'],
			'net_salary'=>$row['Net Salary'],
			'pin_no'=>$row['Pin No.'],
			'town'=>$row['Town'],
			'phone_number'=>$row['Phone No.'],
			'secondry_phone_number'=>$row['Sec. Phone No.'],
			'email'=>$row['Email'],
			'address'=>$row['Address'],
			'back_account_number'=>$row['Bank Account No'],
			'prospect_customer_id'=>$row['Prospect Customer'],
			'last_name'=>$row['Last Name'],
			'other_names'=>$row['Other Names'],
			'dob'=>$row['Date Of Birth'],
			'place_of_birth'=>$row['Place Of Birth'],
			'gender_id'=>$row['Gender'],
			'identification_type_id'=>$row['Id Type'],
			'nationality_id'=>$row['Nationality'],
			'serial_number'=>$row['Serial No.'],
			'id_number'=>$row['ID No.'],
			'date_of_issue'=>$row['Date Of Issue'],
			'place_of_issue'=>$row['Place Of Issue'],
			'district'=>$row['District'],
			'division'=>$row['Division'],
			'location'=>$row['Location'],
			'sub_location'=>$row['Sub Location'],
			
			]);
		}
			elseif($route == 'contracts')
		{
			return Contract::firstOrCreate([
				'contract_number'=>$row['Contract No'],
			'name'=>$row['Name As Per Payslip'],
			'document_number'=>$row['Document No'],
			'sale_date'=>$row['Sale Date'],
			'account_customer_id'=>$row['Account Customer'],
			'inquiry_id'=>$row['Inquiry'],
			
			]);
		}
			elseif($route == 'contract_items')
		{
			return ContractItems::firstOrCreate([
				'contract_id'=>$row['Contract No'],
			'stock_id'=>$row['Item'],
			'quantity'=>$row['Quantity'],
			'installments'=>$row['Installment'],
			'duration'=>$row['Period In Months'],
			
			]);
		}
			elseif($route == 'guarantors')
		{
			return Guarantor::firstOrCreate([
				'designation'=>$row['Designation'],
			'department'=>$row['Department'],
			'station'=>$row['Station'],
			'section'=>$row['Section'],
			'last_name'=>$row['Last Name'],
			'other_names'=>$row['Other Names'],
			'd0b'=>$row['Date Of Birth'],
			'gender_id'=>$row['Gender'],
			'district_of_birth'=>$row['District Of Birth'],
			'identification_type_id'=>$row['ID Type'],
			'nationality_id'=>$row['Nationality'],
			'id_number'=>$row['ID No'],
			'serial_number'=>$row['ID Serial No'],
			'date_of_issue'=>$row['Date Of Issue'],
			'place_of_issue'=>$row['Place Of Issue'],
			'district'=>$row['District'],
			'division'=>$row['Division'],
			'location'=>$row['Location'],
			'sub_location'=>$row['Sub Location'],
			'phone_number'=>$row['Phone Number'],
			'email'=>$row['Email'],
			
			]);
		}
			elseif($route == 'statuses')
		{
			return Status::firstOrCreate([
				'name'=>$row['name'],
			
			]);
		}
			//save from excel

    }

    public function uniqueBy($route)
    {
        if($route == 'users')
            return 'email';
        elseif($route == 'names')
            return 'password';
        else
            return;
    }

    public function validation($route)
    {
        if($route == 'users')
        {
            return [
                'email' => 'unique:users,email' , 
                'role' => 'required' ,               
            ];
        }else{
            return [];
        }
    }
    public function validationError($route)
    {
        if($route == 'users')
        {
            return [
                'email.unique' => ':attribute needs to be unique',
                // 'role.required' => ':attribute is being required'              
            ];
        }else{
            return [];
        }
    }
}
