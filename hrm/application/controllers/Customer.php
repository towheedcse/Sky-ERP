<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Customer extends CI_Controller {	
	function __construct(){
		parent::__construct();
		$this->load->model("Customer_model");
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
		$data['ctquery']=$this->alllist->GetCountryList();
		$data['spquery']=$this->alllist->GetAccountList(1);
		$data['mpquery']= $this->alllist->GetMideaList();
		$data['dvquery'] =$this->alllist->GetDivisionList();
		$data['qquery']=$this->alllist->GetQualificationList();
		$this->load->view('short_customer',$data);	
	}
	function AddRecord(){ // Function Standard PascalCase
		$this->Site_model->has_menupermission($this->uri->segment(1));
		$this->Customer_model->InsertRecord();
		$msg="Successfully save record!!!";
		$data['msg']=$msg;
		redirect(SERVER.'/customer',$data);
	}
	function FillRecord(){
		$this->Site_model->has_menupermission($this->uri->segment(1));
		$erow = $this->Customer_model->FillRecord();
		$arow = $this->Customer_model->FillAccountHead();
		$password = substr($erow->password, 0, -2);
		echo $erow->customer_id."##&##".$erow->company_id."##&##".$erow->branch_id."##&##".$erow->customer_type."##&##".$erow->customer_full_name."##&##".$erow->billing_address."##&##".$erow->shipping_address."##&##".$erow->contact_person."##&##".$erow->designation."##&##".$erow->gender."##&##".$erow->nationality."##&##".$erow->salesman_id."##&##".$erow->division."##&##".$erow->district."##&##".$erow->thana."##&##".$erow->address."##&##".$erow->permanent_address."##&##".$erow->mobile."##&##".$erow->phone."##&##".$erow->fax."##&##".$erow->email."##&##".$erow->login_id."##&##".$password."##&##".$arow->status;
	}
	function EditRecord(){
		$this->Site_model->has_menupermission($this->uri->segment(1));
		$this->Customer_model->UpdateRecord();
	}
	function DelRecord(){
		$this->Site_model->has_menupermission($this->uri->segment(1));
		$this->Customer_model->DelRecord();
	}	
	function GetRecord(){
		$this->Site_model->has_menupermission($this->uri->segment(1));
		$this->Customer_model->GetRecordGrid();
	}

	function ViewProfile(){
		$menu_slug= $this->uri->segment(1);	
		$this->Site_model->has_menupermission($menu_slug);
		$data['hasPrintOption'] = $this->Site_model->hasOptionPermission($menu_slug,"Print");
		$customer_id	= $this->uri->segment(3);	
		$data['customer_id']= $customer_id;
		$this->load->view('print_customer_profile',$data);
	}
	function GetProfile(){
		$menu_slug= $this->uri->segment(1);	
		$this->Site_model->has_menupermission($menu_slug);
		$data['hasPrintOption'] = $this->Site_model->hasOptionPermission($menu_slug,"Print");	    
		
		$this->load->model("Report_model");		
		$this->Report_model->GetCustomerProfile();
	}	
	function GetAjaxDistrictList(){
		$division_id  	=$this->input->post('division-id');
		$district_id  	=$this->input->post('district-id');
		if(empty($company_id)){$company_id=0;}
		if(empty($district_id)){$district_id=0;}
		if(empty($division_id)){$division_id=0;}
		echo $this->alllist->GetAjaxDistrictList($division_id,$district_id);
	}	
	function GetAjaxAreaList(){
		$district_id  	=$this->input->post('district-id');
		$area_id  	    =$this->input->post('area-id');
		if(empty($company_id)){$company_id=0;}
		if(empty($district_id)){$district_id=0;}
		if(empty($area_id)){$area_id=0;}
		echo $this->alllist->GetAjaxAreaList($district_id,$area_id);
	}
	function downloadEmpPDFForm(){
		
	}
}
