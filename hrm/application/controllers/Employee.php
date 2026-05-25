<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Employee extends CI_Controller {	
	function __construct(){
		parent::__construct();
		$this->load->model("Employee_model");
		$this->load->library('Alllist');
	}
		
	function index(){
		$menu_slug= $this->uri->segment(1);	
		$this->Site_model->has_menupermission($menu_slug);			    
		$data['hasCreateOption']= $this->Site_model->hasOptionPermission($menu_slug,"Create");				    
		$data['hasConcessionOption'] = $this->Site_model->hasOptionPermission($menu_slug,"Concession");    
		$data['hasViewOption']  = $this->Site_model->hasOptionPermission($menu_slug,"View");
		$data['cquery']=$this->alllist->GetCompanyList();
		$data['bquery']=$this->alllist->GetBranchList();
        $data['dquery']=$this->alllist->GetDepartmentList();
		$data['squery']=$this->alllist->GetSubjectList();
		$data['shquery']=$this->alllist->GetShiftList();
		
		$data['pfh_account'] = $this->alllist->GetAccountList(26);
		$data['lon_account'] = $this->alllist->GetAccountList(27);
		$data['qquery']=$this->alllist->GetQualificationList();
		$this->load->view('employee',$data);	
	}
	function AddRecord(){ // Function Standard PascalCase
		$this->Site_model->has_menupermission($this->uri->segment(1));
		$this->Employee_model->InsertRecord();
		$msg="Successfully save record!!!";
		$data['msg']=$msg;
		redirect(SERVER.'/employee',$data);
	}
	function FillRecord(){
		$this->Site_model->has_menupermission($this->uri->segment(1));
		$erow = $this->Employee_model->FillEmployee();
		$arow = $this->Employee_model->FillAccountHead();
		$password = substr($erow->password, 0, -2);
		echo $erow->employee_id."##&##".$erow->company_id."##&##".$erow->branch_id."##&##".$erow->department_id."##&##".$erow->section_id."##&##".$erow->appointment_type."##&##".$this->Employee_model->formatDateDMY($erow->appointment_date)."##&##".$this->Employee_model->formatDateDMY($erow->joining_date)."##&##".$erow->employee_code."##&##".$erow->employee_type."##&##".$erow->designation."##&##".$erow->card_id."##&##".$erow->major_subject."##&##".$erow->weekend."##&##".$erow->login_id."##&##".$password."##&##".$arow->bangla_name."##&##".$arow->account_name."##&##".$erow->fathers_name."##&##".$erow->mothers_name."##&##".$erow->spouse_name."##&##".$this->Employee_model->formatDateDMY($arow->dob)."##&##".$erow->address."##&##".$erow->permanent_address."##&##".$erow->education_qualification."##&##".$erow->extra_qualification."##&##".$arow->nationality."##&##".$arow->gender."##&##".$arow->blood_group."##&##".$erow->marital_status."##&##".$arow->religion."##&##".$arow->phone."##&##".$arow->mobile."##&##".$arow->email."##&##".$erow->cash_salary."##&##".$erow->tnt_allowance."##&##".$erow->others_payble."##&##".$erow->total_fix_payble."##&##".$erow->basic_salary."##&##".$erow->houserent_allowance."##&##".$erow->medical_allowance."##&##".$erow->transport_allowance."##&##".$erow->communication_allowance."##&##".$erow->festival_bonus."##&##".$erow->others_allowance."##&##".$erow->gross_salary."##&##".$erow->provident_fund."##&##".$erow->income_tax."##&##".$erow->income_tax_amount."##&##".$erow->loan_and_adv."##&##".$erow->gross_deduction."##&##".$erow->net_salary."##&##".$erow->pf_achead_mapping."##&##".$erow->loan_achead_mapping."##&##".$erow->shift_id."##&##".$erow->total_loan_and_adv."##&##".$erow->loan_total_paid."##&##".$arow->status;
	}
	function EditRecord(){
		$this->Site_model->has_menupermission($this->uri->segment(1));
		$this->Employee_model->UpdateRecord();
	}
	function DelRecord(){
		$this->Site_model->has_menupermission($this->uri->segment(1));
		$this->Employee_model->DelRecord();
	}	
	function GetRecord(){
		$this->Site_model->has_menupermission($this->uri->segment(1));
		$this->Employee_model->GetRecordGrid();
	}

	function ViewProfile(){
		$menu_slug= $this->uri->segment(1);	
		$this->Site_model->has_menupermission($menu_slug);
		$data['hasPrintOption'] = $this->Site_model->hasOptionPermission($menu_slug,"Print");
		$employee_id	= $this->uri->segment(3);	
		$data['employee_id']= $employee_id;
		$this->load->view('print_employee_profile',$data);
	}
	function GetEmpProfile(){
		$menu_slug= $this->uri->segment(1);	
		$this->Site_model->has_menupermission($menu_slug);
		$data['hasPrintOption'] = $this->Site_model->hasOptionPermission($menu_slug,"Print");	    
		
		$this->load->model("Report_model");		
		$this->Report_model->GetEmpProfile();
	}
	function ViewLeave(){
		$menu_slug= $this->uri->segment(1);	
		$this->Site_model->has_menupermission($menu_slug);
		$data['hasPrintOption'] = $this->Site_model->hasOptionPermission($menu_slug,"Print");
		$employee_id	= $this->uri->segment(3);	
		$data['employee_id']= $employee_id;
		$this->load->view('employee_leave_status',$data);
	}
	function GetEmpLeave(){
		$menu_slug= $this->uri->segment(1);	
		$this->Site_model->has_menupermission($menu_slug);
		$data['hasPrintOption'] = $this->Site_model->hasOptionPermission($menu_slug,"Print");	    
		
		$this->load->model("Report_model");		
		$this->Report_model->GetEmpLeaveStatus();
	}
	
	function ViewLeaveForm(){
		$menu_slug= $this->uri->segment(1);	
		$this->Site_model->has_menupermission($menu_slug);
		$data['hasPrintOption'] = $this->Site_model->hasOptionPermission($menu_slug,"Print");
		$employee_id	= $this->uri->segment(3);	
		$data['employee_id']= $employee_id;
		$this->load->view('employee_leave_form',$data);
	}
	function GetEmpLeaveform(){
		$menu_slug= $this->uri->segment(1);	
		$this->Site_model->has_menupermission($menu_slug);
		$data['hasPrintOption'] = $this->Site_model->hasOptionPermission($menu_slug,"Print");	    
		
		$this->load->model("Report_model");		
		$this->Report_model->GetEmpLeaveForm();
	}
	function saveAjaxEmp(){
		$this->Site_model->has_menupermission($this->uri->segment(1));
		$this->Employee_model->AddAjaxRecord();
	}
	function downloadEmpPDFForm(){
		
	}
	function GetAjaxSectionList(){
		$company_id     =$this->input->post('company-id');
		$department_id  =$this->input->post('department-id');
		$section_id     =$this->input->post('section-id');
		if(empty($sub_head)){$sub_head=0;}
		echo $this->alllist->GetAjaxSectionList($company_id,$department_id,$section_id);
	}
	function GetAjaxEmployeeList(){
		//$this->Site_model->has_menupermission($this->uri->segment(1));
		$employee_id	=$this->input->post('employee_id');
		$company_id	    =$this->input->post('company_id');
		$branch_id		=$this->input->post('branch_id');
		$department_id	=$this->input->post('department_id');
		$section_id		=$this->input->post('section_id');
		$shift_id		=$this->input->post('shift_id');
		if($company_id >0 && $branch_id >0 && $department_id >0 && $section_id >0 && $shift_id >0){
		 echo $this->alllist->GetAjaxEmployeeList($employee_id,$company_id,$branch_id,$department_id,$section_id,$shift_id);
		}
	}
	function GetEmployeeDetails(){
		$this->Site_model->has_menupermission($this->uri->segment(1));
		$erow       = $this->Employee_model->FillEmployee();
		$arow       = $this->Employee_model->FillAccountHead();
		$session_id = $this->session->userdata('default_session');
		if(empty($erow->shift_id)){
		$shift_id =$this->session->userdata('default_shift');
		}else{
		$shift_id = $erow->shift_id;    
		}
		if($shift_id>0){
		  $TSQL = "SELECT * FROM ".SHIFT_TBL." WHERE shift_id=".$shift_id." AND institute_id=".$erow->company_id." AND status =1";
		  $TRES = $this->db->query($TSQL);
		  if($TRES->num_rows() >0){
		      $in_time = $TRES->row()->shift_start; $out_time = $TRES->row()->shift_end;
		      
		  }else{$in_time=date("H:i:s"); $out_time = "18:00:00";}
		  $od_from = date("d/m/Y");
		}
		echo $erow->company_id."##&##".$erow->branch_id."##&##".$erow->department_id."##&##".$erow->section_id."##&##".$shift_id."##&##".$this->session->userdata('default_session')."##&##".$od_from."##&##".$in_time."##&##".$out_time;
	}
}
