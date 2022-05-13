<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Spatie\Permission\Models\Permission;

class SystemPermission extends Permission
{
    public static function defaultPermissions()
    {
        return [
            // -------- Users ---------
            '1' => [

                'system_users' => [
                    'view_users',
                    'create_users',
                    'edit_users',
                    'delete_users',
                    'export_users',
                    'import_users'
                ],
                'roles' => [
                    'view_roles',
                    'create_roles',
                    'edit_roles',
                    'delete_roles',
                    'export_roles',
                    'import_roles'
                ],

                
			'dotenvs' => [
				'view_dotenvs',
				'create_dotenvs',
				'edit_dotenvs',
				'delete_dotenvs',
				'export',
				'import'
			],
	
			'side_bars' => [
				'view_side_bars',
				'create_side_bars',
				'edit_side_bars',
				'delete_side_bars',
				'export_side_bars',
				'import_side_bars'
			],
	
			'model_mappings' => [
				'view_model_mappings',
				'create_model_mappings',
				'edit_model_mappings',
				'delete_model_mappings',
				'export_model_mappings',
				'import_model_mappings'
			],
	
			'approvers' => [
				'view_approvers',
				'create_approvers',
				'edit_approvers',
				'delete_approvers',
				'export_approvers',
				'import_approvers'
			],
	
			'approver_statuses' => [
				'view_approver_statuses',
				'create_approver_statuses',
				'edit_approver_statuses',
				'delete_approver_statuses',
				'export_approver_statuses',
				'import_approver_statuses'
			],
	
			'members' => [
				'view_members',
				'create_members',
				'edit_members',
				'delete_members',
				'export_members',
				'import_members'
			],
	
			'memes' => [
				'view_memes',
				'create_memes',
				'edit_memes',
				'delete_memes',
				'export_memes',
				'import_memes'
			],
	
			'departments' => [
				'view_departments',
				'create_departments',
				'edit_departments',
				'delete_departments',
				'export_departments',
				'import_departments'
			],
	
			'designations' => [
				'view_designations',
				'create_designations',
				'edit_designations',
				'delete_designations',
				'export_designations',
				'import_designations'
			],
	
			'hierarchies' => [
				'view_hierarchies',
				'create_hierarchies',
				'edit_hierarchies',
				'delete_hierarchies',
				'export_hierarchies',
				'import_hierarchies'
			],
	
			'designation_hierarchies' => [
				'view_designation_hierarchies',
				'create_designation_hierarchies',
				'edit_designation_hierarchies',
				'delete_designation_hierarchies',
				'export_designation_hierarchies',
				'import_designation_hierarchies'
			],
	
			'models_to_approves' => [
				'view_models_to_approves',
				'create_models_to_approves',
				'edit_models_to_approves',
				'delete_models_to_approves',
				'export_models_to_approves',
				'import_models_to_approves'
			],
	
			'model_tobe_approveds' => [
				'view_model_tobe_approveds',
				'create_model_tobe_approveds',
				'edit_model_tobe_approveds',
				'delete_model_tobe_approveds',
				'export_model_tobe_approveds',
				'import_model_tobe_approveds'
			],
	
			'bursaries' => [
				'view_bursaries',
				'create_bursaries',
				'edit_bursaries',
				'delete_bursaries',
				'export_bursaries',
				'import_bursaries'
			],
	
			'helb_loans' => [
				'view_helb_loans',
				'create_helb_loans',
				'edit_helb_loans',
				'delete_helb_loans',
				'export_helb_loans',
				'import_helb_loans'
			],
	
			'round_methods' => [
				'view_round_methods',
				'create_round_methods',
				'edit_round_methods',
				'delete_round_methods',
				'export_round_methods',
				'import_round_methods'
			],
	
			'regions' => [
				'view_regions',
				'create_regions',
				'edit_regions',
				'delete_regions',
				'export_regions',
				'import_regions'
			],
	
			'shops' => [
				'view_shops',
				'create_shops',
				'edit_shops',
				'delete_shops',
				'export_shops',
				'import_shops'
			],
	
			'genders' => [
				'view_genders',
				'create_genders',
				'edit_genders',
				'delete_genders',
				'export_genders',
				'import_genders'
			],
	
			'nationalities' => [
				'view_nationalities',
				'create_nationalities',
				'edit_nationalities',
				'delete_nationalities',
				'export_nationalities',
				'import_nationalities'
			],
	
			'identification_types' => [
				'view_identification_types',
				'create_identification_types',
				'edit_identification_types',
				'delete_identification_types',
				'export_identification_types',
				'import_identification_types'
			],
	
			'employers' => [
				'view_employers',
				'create_employers',
				'edit_employers',
				'delete_employers',
				'export_employers',
				'import_employers'
			],
	
			'prospect_customers' => [
				'view_prospect_customers',
				'create_prospect_customers',
				'edit_prospect_customers',
				'delete_prospect_customers',
				'export_prospect_customers',
				'import_prospect_customers'
			],
	
			'inquiries' => [
				'view_inquiries',
				'create_inquiries',
				'edit_inquiries',
				'delete_inquiries',
				'export_inquiries',
				'import_inquiries'
			],
	
			'inquiry_items' => [
				'view_inquiry_items',
				'create_inquiry_items',
				'edit_inquiry_items',
				'delete_inquiry_items',
				'export_inquiry_items',
				'import_inquiry_items'
			],
	
			'inquiry_calculators' => [
				'view_inquiry_calculators',
				'create_inquiry_calculators',
				'edit_inquiry_calculators',
				'delete_inquiry_calculators',
				'export_inquiry_calculators',
				'import_inquiry_calculators'
			],
	
			'account_customers' => [
				'view_account_customers',
				'create_account_customers',
				'edit_account_customers',
				'delete_account_customers',
				'export_account_customers',
				'import_account_customers'
			],
	
			'contracts' => [
				'view_contracts',
				'create_contracts',
				'edit_contracts',
				'delete_contracts',
				'export_contracts',
				'import_contracts'
			],
	
			'contract_items' => [
				'view_contract_items',
				'create_contract_items',
				'edit_contract_items',
				'delete_contract_items',
				'export_contract_items',
				'import_contract_items'
			],
	
			'guarantors' => [
				'view_guarantors',
				'create_guarantors',
				'edit_guarantors',
				'delete_guarantors',
				'export_guarantors',
				'import_guarantors'
			],
	
			'statuses' => [
				'view_statuses',
				'create_statuses',
				'edit_statuses',
				'delete_statuses',
				'export_statuses',
				'import_statuses'
			],
	//permissions Array

            ]


        ];
    }
}
