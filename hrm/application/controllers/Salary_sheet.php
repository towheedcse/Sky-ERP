<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Salary_sheet extends CI_Controller {
	function __construct(){
		parent::__construct();
		$this->load->model("Salarysheet_model");
		$this->load->library('Alllist');
	}		
	function index(){
		$menu_slug= $this->uri->segment(1);	
		$this->Site_model->has_menupermission($menu_slug);			    
	    $data['hasViewOption']      = $this->Site_model->hasOptionPermission($menu_slug,"View");
		$data['hasGenerateOption']  = $this->Site_model->hasOptionPermission($menu_slug,"Generate");
		$data['hasApprovedOption']  = $this->Site_model->hasOptionPermission($menu_slug,"Approved");
		$data['hasPrintOption']     = $this->Site_model->hasOptionPermission($menu_slug,"Print");
		$data['iquery']=$this->alllist->GetCompanyList();
		$data['bquery']=$this->alllist->GetBranchList();
		$data['squery']=$this->alllist->GetSessionList();
		$data['vquery']=$this->alllist->GetVersionList();
		$this->load->view('generate_salary_sheet',$data);
	}
	function GetSalarySheetRecord(){
		$this->Site_model->has_menupermission($this->uri->segment(1));	
		echo $this->Salarysheet_model->GetSalarySheetRecordGrid();		
	}
	function SaveRecord(){ // Function Standard PascalCase
		$this->Site_model->has_menupermission($this->uri->segment(1));
		$this->Salarysheet_model->saveSalarySheet();
		echo $this->Salarysheet_model->GetSalarySheetRecordGrid();
	}
	function GenerateSalarySheet(){
		$this->Site_model->has_menupermission($this->uri->segment(1));
		$this->Salarysheet_model->FinalizeSalarySheet();
		echo $this->Salarysheet_model->GetSalarySheetRecordGrid();
	}
	function ApprovedSalarySheet(){
		$this->Site_model->has_menupermission($this->uri->segment(1));
		$this->Salarysheet_model->ApprovedSalarySheet();
		echo $this->Salarysheet_model->GetSalarySheetRecordGrid();
	}	
	function GetMonthlySalarySheet(){
		$menu_slug= $this->uri->segment(1);	
		$this->Site_model->has_menupermission($menu_slug);		    
	    $data['hasViewOption']  = $this->Site_model->hasOptionPermission($menu_slug,"View");
		$data['iquery']=$this->alllist->GetCompanyList();
		$data['bquery']=$this->alllist->GetBranchList();
		$data['squery']=$this->alllist->GetSessionList();
		$data['vquery']=$this->alllist->GetVersionList();
		$this->load->view('monthly_salary_sheet',$data);		
	}	
	function GetMonthlySalarySheetRecord(){
		$this->Site_model->has_menupermission($this->uri->segment(1));	
		echo $this->Salarysheet_model->GetMonthlySalarySheetRecordGrid();
		
	}	
	function GetCashSalarySheet(){
		$menu_slug= $this->uri->segment(1);	
		$this->Site_model->has_menupermission($menu_slug);		    
	    $data['hasViewOption']  = $this->Site_model->hasOptionPermission($menu_slug,"View");		    
	    $data['hasPrintOption']  = $this->Site_model->hasOptionPermission($menu_slug,"Print");
		$data['iquery']=$this->alllist->GetCompanyList();
		$data['bquery']=$this->alllist->GetBranchList();
		$data['squery']=$this->alllist->GetSessionList();
		$this->load->view('cash_salary_sheet',$data);		
	}
	function GetCashSalarySheetRecord(){
		$this->Site_model->has_menupermission($this->uri->segment(1));
		echo $this->Salarysheet_model->GetCashSalarySheetRecordGrid();
	}
		
	function GetBankSalarySheet(){
		$menu_slug= $this->uri->segment(1);	
		$this->Site_model->has_menupermission($menu_slug);		    
	    $data['hasViewOption']  = $this->Site_model->hasOptionPermission($menu_slug,"View");		    
	    $data['hasPrintOption']  = $this->Site_model->hasOptionPermission($menu_slug,"Print");
		$data['iquery']=$this->alllist->GetCompanyList();
		$data['bquery']=$this->alllist->GetBranchList();
		$data['squery']=$this->alllist->GetSessionList();
		$this->load->view('bank_salary_sheet',$data);		
	}
	function GetBankSalarySheetRecord(){
		$this->Site_model->has_menupermission($this->uri->segment(1));
		echo $this->Salarysheet_model->GetBankSalarySheetRecordGrid();
	}
}
