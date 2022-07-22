<?php

namespace App\Http\Controllers\Logic;

use App\Model\AccountCustomer;
use App\Helpers\AppHelper;
use App\Exports\MainExport;
use App\Http\Controllers\dbsave;
use App\Imports\OnEachRowImport;
use Spatie\Permission\Models\Role;
use App\Repositories\MainRepository;
use Maatwebsite\Excel\Facades\Excel;
use App\Http\Controllers\Logic\MainLogic;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use App\Model\Gender;
use App\Model\IdentificationType;
use App\Model\Nationality;
use App\Model\ProspectCustomer;

//import class

class AccountCustomerLogic
{
	const route = 'account_customers';
	const redirect = 'account_customers/account_customers';

	public $model;

	function __construct(
		AccountCustomer $model
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
			self::validated($request);

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

		$request->validate([

			'id_number' => ['required',],
			'other_names' => ['required',]

			//validate
		]);
	}

	public  function tableColumns() //variable
	{
		return [
			'Name.' => ['relationship', 'prospect', 'full_name'],
			'Id No.' => 'id_number',
			'Work Number' => 'work_number',
			'Town' => 'town',
			'Phone No.' => 'phone_number',

			//table columns
		];
	}

	public  function dbfields() //variable
	{
		return [
			'work_number', 'designation', 'department', 'deo', 'section', 'station', 'gross_salary', 'net_salary', 'pin_no', 'town', 'phone_number', 'secondry_phone_number', 'email', 'address', 'back_account_number', 'prospect_customer_id', 'last_name', 'other_names', 'dob', 'place_of_birth', 'gender_id', 'identification_type_id', 'nationality_id', 'serial_number', 'id_number', 'date_of_issue', 'place_of_issue', 'district', 'division', 'location', 'sub_location'

		];
	}

	public  function fields($forEdit) //variable
	{


		$values = [
			'work_number' =>
			[
				'text', 'string', 'Work No.', 6, true, true, 'work_number', 'work_number', 'Enter Work No.'
			],
			'designation' =>
			[
				'text', 'string', 'Designation', 6, true, true, 'designation', 'designation', 'Enter Designation'
			],
			'department' =>
			[
				'text', 'string', 'Department', 6, true, true, 'department', 'department', 'Enter Department'
			],
			'deo' =>
			[
				'text', 'string', 'DEO', 6, true, true, 'deo', 'deo', 'Enter DEO'
			],
			'section' =>
			[
				'text', 'string', 'Section', 6, true, true, 'section', 'section', 'Enter Section'
			],
			'station' =>
			[
				'text', 'string', 'Station', 6, true, true, 'station', 'station', 'Enter Station'
			],
			'gross_salary' =>
			[
				'text', 'float', 'Gross Salary', 6, true, true, 'gross_salary', 'gross_salary', 'Enter Gross Salary'
			],
			'net_salary' =>
			[
				'text', 'float', 'Net Salary', 6, true, true, 'net_salary', 'net_salary', 'Enter Net Salary'
			],
			'pin_no' =>
			[
				'text', 'string', 'Pin No.', 6, true, true, 'pin_no', 'pin_no', 'Enter Pin No.'
			],
			'town' =>
			[
				'text', 'string', 'Town', 6, true, true, 'town', 'town', 'Enter Town'
			],
			'phone_number' =>
			[
				'text', 'string', 'Phone No.', 6, true, true, 'phone_number', 'phone_number', 'Enter Phone No.'
			],
			'secondry_phone_number' =>
			[
				'text', 'string', 'Sec. Phone No.', 6, true, true, 'secondry_phone_number', 'secondry_phone_number', 'Enter Sec. Phone No.'
			],
			'email' =>
			[
				'text', 'string', 'Email', 6, true, true, 'email', 'email', 'Enter Email'
			],
			'address' =>
			[
				'textarea', 'text', 'Address', 12, true, true, 'address', 'address', 'Enter Address'
			],
			'back_account_number' =>
			[
				'text', 'string', 'Bank Account No', 6, true, true, 'back_account_number', 'back_account_number', 'Enter Bank Account No'
			],
			'prospect_customer_id' =>
			[
				'select', 'select', 'Choose Prospect Customer', 6, true, true, 'prospect_customer_id', 'prospect_customer_id', 'Select prospect_customer_id', '', ProspectCustomer::all(), isset($forEdit) ? AccountCustomer::with('prospect')->where('id', $forEdit->id)->first()->prospect : ''
			],
			'last_name' =>
			[
				'text', 'string', 'Last Name', 6, true, true, 'last_name', 'last_name', 'Enter Last Name'
			],
			'other_names' =>
			[
				'text', 'string', 'Other Names', 6, true, true, 'other_names', 'other_names', 'Enter Other Names'
			],
			'dob' =>
			[
				'dateTime', 'dateTime', 'Date Of Birth', 6, true, true, 'date0', 'dob', 'Enter Date Of Birth'
			],
			'place_of_birth' =>
			[
				'text', 'string', 'Place Of Birth', 6, true, true, 'place_of_birth', 'place_of_birth', 'Enter Place Of Birth'
			],
			'gender_id' =>
			[
				'select', 'select', 'Choose Gender', 6, true, true, 'gender_id', 'gender_id', 'Select gender_id', '', Gender::all(), isset($forEdit) ? AccountCustomer::with('gender')->where('id', $forEdit->id)->first()->gender : ''
			],
			'identification_type_id' =>
			[
				'select', 'select', 'Choose Id Type', 6, true, true, 'identification_type_id', 'identification_type_id', 'Select identification_type_id', '', IdentificationType::all(), isset($forEdit) ? AccountCustomer::with('identification')->where('id', $forEdit->id)->first()->identification : ''
			],
			'nationality_id' =>
			[
				'select', 'select', 'Choose Nationality', 6, true, true, 'nationality_id', 'nationality_id', 'Select nationality_id', '', Nationality::all(), isset($forEdit) ? AccountCustomer::with('nationality')->where('id', $forEdit->id)->first()->nationality : ''
			],
			'serial_number' =>
			[
				'text', 'integer', 'Serial No.', 6, true, true, 'serial_number', 'serial_number', 'Enter Serial No.'
			],
			'id_number' =>
			[
				'text', 'integer', 'ID No.', 6, true, true, 'id_number', 'id_number', 'Enter ID No.'
			],
			'date_of_issue' =>
			[
				'dateTime', 'dateTime', 'Date Of Issue', 6, true, true, 'date1', 'date_of_issue', 'Enter Date Of Issue'
			],
			'place_of_issue' =>
			[
				'text', 'string', 'Place Of Issue', 6, true, true, 'place_of_issue', 'place_of_issue', 'Enter Place Of Issue'
			],
			'district' =>
			[
				'text', 'string', 'District', 6, true, true, 'district', 'district', 'Enter District'
			],
			'division' =>
			[
				'text', 'string', 'Division', 6, true, true, 'division', 'division', 'Enter Division'
			],
			'location' =>
			[
				'text', 'string', 'Location', 6, true, true, 'location', 'location', 'Enter Location'
			],
			'sub_location' =>
			[
				'text', 'string', 'Sub Location', 6, true, true, 'sub_location', 'sub_location', 'Enter Sub Location'
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
		$modelName = 'AccountCustomer';
		$model = 'App\Model';
		$columns = [
			'work_number', 'designation', 'department', 'deo', 'section', 'station', 'gross_salary', 'net_salary', 'pin_no', 'town', 'phone_number', 'secondry_phone_number', 'email', 'address', 'back_account_number', 'prospect_customer_id', 'last_name', 'other_names', 'dob', 'place_of_birth', 'gender_id', 'identification_type_id', 'nationality_id', 'serial_number', 'id_number', 'date_of_issue', 'place_of_issue', 'district', 'division', 'location', 'sub_location'

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
			'Work No.', 'Designation', 'Department', 'DEO', 'Section', 'Station', 'Gross Salary', 'Net Salary', 'Pin No.', 'Town', 'Phone No.', 'Sec. Phone No.', 'Email', 'Address', 'Bank Account No', 'Prospect Customer', 'Last Name', 'Other Names', 'Date Of Birth', 'Place Of Birth', 'Gender', 'Id Type', 'Nationality', 'Serial No.', 'ID No.', 'Date Of Issue', 'Place Of Issue', 'District', 'Division', 'Location', 'Sub Location'
			//headings
		];
		$data = $this->model->select(
			'work_number',
			'designation',
			'department',
			'deo',
			'section',
			'station',
			'gross_salary',
			'net_salary',
			'pin_no',
			'town',
			'phone_number',
			'secondry_phone_number',
			'email',
			'address',
			'back_account_number',
			'prospect_customer_id',
			'last_name',
			'other_names',
			'dob',
			'place_of_birth',
			'gender_id',
			'identification_type_id',
			'nationality_id',
			'serial_number',
			'id_number',
			'date_of_issue',
			'place_of_issue',
			'district',
			'division',
			'location',
			'sub_location'
		)->get(); //variable

		if ($formatType == 'xlsx') {
			return Excel::download(new MainExport($data, $format, $styles, $headings, 'Excel '), self::route . '.xlsx');
		} elseif ($formatType == 'pdf') {
			return (new MainExport($data, $format, $styles, $headings, 'Pdf '))->download(self::route . '.pdf', \Maatwebsite\Excel\Excel::DOMPDF);
		}
	}
}
