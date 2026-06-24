<?php
class Site_model extends CI_Model {

	function __construct()
	{
		parent::__construct();
	
    	define("COMPANY_SETTINGS_TBL", 	"company_settings");
		define("DEPARTMENT_TBL", 		"department");
		define("LEAVE_CATEGORY_TBL", 	"leave_category");
		define("LEAVE_TBL", 	        "employee_leave");
		define("USER_TBL", 				"users");
		define("MODULE_TBL", 			"module");
    	define("MODULE_PERMISSION_TBL", "module_permission");
		define("MENU_TBL", 				"menu");
    	define("MENU_PERMISSION_TBL", 	"menu_permission");
		define("OPTIONS_TBL", 			"options");
    	define("OPTIONS_PERMISSION_TBL","options_permission");
		define("USER_ROLE_TBL", 		"hrm_user_role");
    	define("USERS_TBL", 			"users");
    	
    	define("GUEST_TBL",             "guest_registration");
    	define("CURRENCY_TBL",          "currency");
    	
    	define("WORKORDER_MASTER_TBL",  "workorder_master");
    	define("WORKORDER_DETAILS_TBL", "workorder_detail");
    	
    	define("DISTRIPO_MASTER_TBL",  "distripo_master");
    	define("DISTRIPO_DETAILS_TBL", "distripo_detail");
    	
    	define("CHALLAN_MASTER_TBL",  "challan_master");
    	define("CHALLAN_DETAILS_TBL", "challan_detail");

		define("CATEGORY_TBL", 			"category");
		define("SUB_CATEGORY_TBL", 		"subcategory");
		define("DIVISION_TBL", 			"division");
		define("DISTRICT_TBL", 			"district");
		define("AREA_TBL", 				"area");
		define("TRT_AREA_TBL", 			"trt_area");
		define("BRANCH_TBL", 			"branch");
		define("PROJECT_TBL", 			"project");
		define("BUILDING_TBL", 			"hostel_building");
		define("HOSTEL_ROOM_TBL", 		"hostel_room");
		define("HOSTEL_BED_TBL", 		"hostel_bed");
		define("GROUP_HEAD_TBL", 		"group_ledger");
		define("SUB_HEAD_L1_TBL", 		"subsidiary_ledger1");
		define("SUB_HEAD_L2_TBL", 		"subsidiary_ledger2");	
		define("SUB_HEAD_L3_TBL", 		"subsidiary_ledger3");	
		define("ACC_HEAD_TBL", 			"coa_head");
		
		define("CLIENT_TBL", 		    "customer");
		define("DISTRIBUTOR_TBL", 		"distributor");
		define("IMPORTER_TBL", 		    "importer"); 
		
		define("APPLICATION_TBL", 		"admission_application");				
		define("HOLIDAY_TBL", 			"holiday");	
		
		define("ATTENDANCE_TBL", 		"attendance");
		define("OUTSATTION_TBL", 		"outstationduty");
		define("AGENCY_TBL", 			"coa_head");
		define("CUSTOMER_TBL", 			"coa_head");
		define("EMPLOYEE_TBL", 			"employee");
		define("STORE_TBL", 			"store");
		define("PRODUCT_TBL", 			"product");
		define("AVG_PURCHASE_PRICE_TBL","avg_purchase_price");

		define("BILL_MASTER_TBL", 		"bill_master");
		define("BILL_DETAILS_TBL", 		"bill_details");
		define("VOUCHER_MASTER_TBL", 	"contra_master");
		define("VOUCHER_DETAILS_TBL", 	"contra_details");
		define("ACC_LEDGER_TBL", 		"account_journal");
		define("BILL_ADJUST_HISTORY_TBL", "bill_adjust_history");
		define("COUNTRY_TBL", "country");
		define("SALARY_SHEET_TBL",      "salary_sheet");
		
		//====== School Table ========
		define("SESSION_TBL", 				"session");
		define("VERSION_TBL", 				"version");
		define("CLASS_TBL", 				"class");
    	define("CLASS_PERMISSION_TBL",  	"class_permission");
		define("GROUPS_TBL", 				"groups");
		define("GROUPS_MAPPING_TBL", 		"groups_mapping");
		define("SHIFT_TBL", 				"shift");
		define("SECTION_TBL", 				"section");
		define("SECTION_MAPPING_TBL", 		"class_section_mapping");
		define("COURSE_FEE_TBL", 			"course_fees");
		define("COURSE_FINE_TBL", 			"course_fine");
		define("PERIOD_TBL", 				"collection_period");
		define("SUBJECT_TBL", 				"subject");
		define("CLASS_SUBJECT_MAPPING_TBL", "class_subject_mapping");
		define("CLASS_TEACHER_ASSIGN_TBL", "class_teachers_assign");
		
		define("OPTIONAL_SUBJECT_DEFINE_TBL", "optional_subject_define");
		define("QUALIFICATION_TBL", 		"qualification");
		define("ROUTINE_TBL", 				"routine");
		define("POINT_TBL", 				"point_table");
		define("MONTH_DAYS_TBL", 			"month_days");
		
		define("PICTURE_TBL", 				"picture");	
	}
    function CheckLicense(){
	    $LSERVER_NAME = isset($_SERVER['SERVER_NAME']) ? $_SERVER['SERVER_NAME'] : '';
        $allowed = array(
            'www.lirahrm.deasbd.com',
            'lirahrm.deasbd.com',
            'skyerp.cwaos.com',
            'www.skyerp.cwaos.com',
        );
        if (in_array($LSERVER_NAME, $allowed, true)) {
            return true;
        } else {
            echo "Invalid License key"; exit;
        }
	}
	function is_loggedin(){
	    $this->CheckLicense();
		if(!$this->session->userdata('validate')){
			redirect(SERVER.'/dashboard/Login');
			exit;
		}
	}

	function has_menupermission($menu_slug){
	    $this->CheckLicense();
		if(!$this->session->userdata('validate')){redirect(SERVER.'/dashboard/Login'); exit;}
		$company_id = $this->session->userdata('company_id');
		$role_id    = $this->session->userdata('user_role');
		$MESQL="SELECT me.menu_id FROM ".MENU_TBL." as me,".MENU_PERMISSION_TBL." as mep WHERE me.module_id=mep.module_id AND mep.company_id =$company_id AND me.menu_slug='$menu_slug' AND mep.role_id=$role_id AND me.menu_status =1 GROUP BY me.menu_id ORDER BY me.order_no ASC";
		$mequery  = $this->db->query($MESQL); //echo $MESQL; exit;
		if($mequery->num_rows() >0){return true;}else{redirect(SERVER.'/dashboard/Userhome'); exit;}
	}
	function hasOptionPermission($menu_slug,$option_type){
	    $this->CheckLicense();
		if(!$this->session->userdata('validate')){redirect(SERVER.'/dashboard/Login'); exit;}
		$company_id = $this->session->userdata('company_id');
		$role_id    = $this->session->userdata('user_role');
		$MESQL="SELECT o.options_id FROM ".MENU_TBL." as me,".OPTIONS_TBL." as o,".OPTIONS_PERMISSION_TBL." as p WHERE me.menu_id=o.menu_id AND o.options_id=p.options_id AND me.menu_slug='$menu_slug' AND o.action_type='$option_type' AND p.company_id =$company_id AND p.role_id=$role_id AND me.menu_status =1 AND o.action_status=1 GROUP BY o.options_id";
		$mequery  = $this->db->query($MESQL);
		if($mequery->num_rows() >0){return true;}else{return false;}
	}

	function is_loggedin_superAdmin(){
	    $this->CheckLicense();
		if(!$this->session->userdata('validate')){
			redirect(SERVER.'/dashboard/Login');
			exit;
		}
		if($this->session->userdata('user_role')!='101' && $this->session->userdata('user_role')!='102'){
			redirect(SERVER.'/dashboard/Login');
			exit;
		}
	}

	function is_loggedin_user(){
	    $this->CheckLicense();
		if(!$this->session->userdata('userlogged')){
			redirect(SERVER.ALISE.'Login');
			exit;
		}
		if(($this->session->userdata('usertype')!='User')){
			redirect(SERVER.ALISE.'Login');
			exit;
		}
	}

	function pagination_config($num){
		$config['per_page'] = $num;
		/*
		$config['full_tag_open'] = '<div class="pagination">';
		$config['full_tag_close'] = '</div>';
		$config['first_link'] = 'First';
		$config['first_tag_open'] = '<div class="next">';
		$config['first_tag_close'] = '</div>';

		$config['last_link'] = 'Last';
		$config['last_tag_open'] = '<div class="next">';
		$config['last_tag_close'] = '</div>';

		$config['next_link'] = 'Next';
		$config['next_tag_open'] = '<div class="next">';
		$config['next_tag_close'] = '</div>';

		$config['prev_link'] = 'Prev';
		$config['prev_tag_open'] = '<div class="next">';
		$config['prev_tag_close'] = '</div>';

		$config['cur_tag_open'] = '<div class="current">';
		$config['cur_tag_close'] = '</div>';

		$config['num_tag_open'] = '<div class="no_current">';
		$config['num_tag_close'] = '</div>';*/
		return $config;
	}
}
?>
