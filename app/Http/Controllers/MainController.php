<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Logic\GitLogic;
use App\Http\Controllers\Logic\DotenvLogic;
use App\Http\Controllers\Logic\BursaryLogic;
use App\Http\Controllers\Logic\SideBarLogic;
use App\Http\Controllers\Logic\ApproversLogic;
use App\Http\Controllers\Logic\HierarchyLogic;
use App\Http\Controllers\Logic\Users\UserLogic;
use App\Http\Controllers\Logic\DesignationLogic;
use App\Http\Controllers\Logic\Users\RolesLogic;
use App\Http\Controllers\Logic\ModelMappingLogic;
use App\Http\Controllers\Logic\Users\PasswordLogic;
use App\Http\Controllers\Logic\ModelsToApproveLogic;
use App\Http\Controllers\Logic\ApproverStatusesLogic;
use App\Http\Controllers\Logic\ModelTobeApprovedLogic;
use App\Http\Controllers\Logic\DesignationHierarchyLogic;
use App\Http\Controllers\reports\dynamicReportController;
use App\Http\Controllers\Logic\RoundMethodLogic;
use App\Http\Controllers\Logic\RegionsLogic;
use App\Http\Controllers\Logic\ShopsLogic;
use App\Http\Controllers\Logic\GenderLogic;
use App\Http\Controllers\Logic\NationalityLogic;
use App\Http\Controllers\Logic\IdentificationTypeLogic;
use App\Http\Controllers\Logic\EmployerLogic;
use App\Http\Controllers\Logic\ProspectCustomerLogic;
use App\Http\Controllers\Logic\InquiryLogic;
use App\Http\Controllers\Logic\InquiryItemsLogic;
use App\Http\Controllers\Logic\InquiryCalculatorLogic;
use App\Http\Controllers\Logic\AccountCustomerLogic;
use App\Http\Controllers\Logic\ContractLogic;
use App\Http\Controllers\Logic\ContractItemsLogic;
use App\Http\Controllers\Logic\GuarantorLogic;
use App\Http\Controllers\Logic\StatusLogic;
//dont remove this comment
 
class MainController extends Controller
{

    //try passing arrey in th constructor 
    // private $modelLogics = [UserLogic $user,RolesLogic $roles,PasswordLogic $changePassword,TestLogic $test];
    
    function __construct(
        //passing variables in a constructor                
        UserLogic $users,
        RolesLogic $roles,
        PasswordLogic $changePassword
		,DotenvLogic $dotenvs
		,GitLogic $git
		,SideBarLogic $side_bars
		,ModelMappingLogic $model_mappings
		,ApproversLogic $approvers
		,ApproverStatusesLogic $approver_statuses
		,DesignationLogic $designations
		,HierarchyLogic $hierarchies
		,DesignationHierarchyLogic $designation_hierarchies
		,ModelsToApproveLogic $models_to_approves
		,ModelTobeApprovedLogic $model_tobe_approveds
		,BursaryLogic $bursaries
        ,dynamicReportController $reports
		,RoundMethodLogic $round_methods
		,RegionsLogic $regions
		,ShopsLogic $shops
		,GenderLogic $genders
		,NationalityLogic $nationalities
		,IdentificationTypeLogic $identification_types
		,EmployerLogic $employers
		,ProspectCustomerLogic $prospect_customers
		,InquiryLogic $inquiries
		,InquiryItemsLogic $inquiry_items
		,InquiryCalculatorLogic $inquiry_calculators
		,AccountCustomerLogic $account_customers
		,ContractLogic $contracts
		,ContractItemsLogic $contract_items
		,GuarantorLogic $guarantors
		,StatusLogic $statuses
		//also dont remove this comment
    ) 
    {
        $path = self::route();
        $this->$path = $$path;
        
        // $this->user = $user;
        // $this->roles = $roles;
    }

    public function index($subdomain)
    {
        $net = self::route();
        return $this->$net->table($subdomain);
    }

    public function create($subdomain)
    {
        $net = self::route();

        return $this->$net->view($subdomain,null);
    }

    public function store($subdomain,Request $request)
    {
        $net = self::route();
        return $this->$net->save($subdomain,$request, null,'save');
    }

    public function show($subdomain,$id)
    {
        $net = self::route();
        return $this->$net->show($subdomain,$id);
    }

    public function edit($subdomain,$id)
    {
        $net = self::route();
        return $this->$net->view($subdomain,$id);
    }

    public function update($subdomain, Request $request, $id)
    {
        $net = self::route();
        return $this->$net->save($subdomain,$request, $id,'update');
    }

    public function destroy($subdomain,$id)
    {
        $net = self::route();
        return $this->$net->delete($subdomain,$id);
    }
    public function import($subdomain,Request $request)
    {
        $net = self::route();
        return $this->$net->import($subdomain,$request);
    }
    public function export($subdomain,$format)
    {
        $net = self::route();
        return $this->$net->export($subdomain,$format);
    }
    public function sample($subdomain)
    {
        $net = self::route();
        // $filePath = public_path($subdomain.'/export_samples/'.$net.'.xlsx');
        $filePath = public_path('export_samples/'.$net.'.xlsx');
    	$fileName = time().$net.'.xlsx';

    	return response()->download($filePath, $fileName);
    }

    public static function route()
    {
        $path = Route::currentRouteName();

        preg_match('/^(.*)\./', $path, $output_array); // adviced by zaq mugo

        return $output_array[1];

    }
}
