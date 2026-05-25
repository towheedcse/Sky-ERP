<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Salary_bill extends CI_Controller {
	function __construct(){
		parent::__construct();
		$this->load->model("Salarysheet_model");
		$this->load->model("Billing_model");
		$this->load->library('Alllist');
	}		
	function index(){
		$menu_slug= $this->uri->segment(1);	
		$this->Site_model->has_menupermission($menu_slug);	
		$data['hasCreateOption']  	= $this->Site_model->hasOptionPermission($menu_slug,"Create");  		    
		$data['hasViewOption']  	= $this->Site_model->hasOptionPermission($menu_slug,"View");    
		$data['hasPrintOption']   	= $this->Site_model->hasOptionPermission($menu_slug,"Print");
		$data['etquery']=$this->alllist->GetEmployeeList();
		$data['cquery']=$this->alllist->GetCompanyList();
		$data['bquery']=$this->alllist->GetBranchList();
		$data['squery']=$this->alllist->GetSessionList();
		$data['vquery']=$this->alllist->GetVersionList();
		$data['fpquery']=$this->alllist->GetFeePeriodList();
		$data['shquery']=$this->alllist->GetShiftList();
		$this->load->view('manage_salary_bill',$data);	
	}
	function GetSalaryBillRecords(){		
		$this->Site_model->has_menupermission($this->uri->segment(1));
		$this->Billing_model->GetSalaryBillRecordGrid();
	}
	
	function ViewBillForm(){
		$menu_slug= $this->uri->segment(1);	
		$this->Site_model->has_menupermission($menu_slug);
		$data['hasPrintOption'] = $this->Site_model->hasOptionPermission($menu_slug,"Print");
		$bill_id	= $this->uri->segment(3); $employee_id	= $this->uri->segment(4);	
		$data['bill_id']= $bill_id;	
		$data['employee_id']= $employee_id;
		$this->load->view('print_salary_bill',$data);
	}
	
	function GetMonthlyPayBill(){
		$menu_slug= $this->uri->segment(1);	
		$this->Site_model->has_menupermission($menu_slug);
		$data['hasPrintOption'] = $this->Site_model->hasOptionPermission($menu_slug,"Print");	    
		
		$this->load->model("Report_model");		
		$this->Report_model->PrintMonthlySalaryBill();
	}
	
	function DelBillRecord(){
		$this->Site_model->has_menupermission($this->uri->segment(1));
		$this->Salarysheet_model->DelMonthlySalaryBillRecord();
	}
}