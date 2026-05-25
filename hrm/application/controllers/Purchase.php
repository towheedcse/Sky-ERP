<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Purchase extends CI_Controller {	
	function __construct(){
		parent::__construct();
		//$this->load->model("Billing_model");
		$this->load->model("Purchase_model");
		$this->load->library('Alllist');
	}		
	function index(){
		$menu_slug= $this->uri->segment(1);	
		$this->Site_model->has_menupermission($menu_slug);			    
		$data['hasCreateOption']	= $this->Site_model->hasOptionPermission($menu_slug,"Create");				    
		$data['hasConcessionOption']= $this->Site_model->hasOptionPermission($menu_slug,"Concession");    
		$data['hasViewOption']  	= $this->Site_model->hasOptionPermission($menu_slug,"View");    
		$data['hasPrintOption']   	= $this->Site_model->hasOptionPermission($menu_slug,"Print");
		$data['spquery']=$this->alllist->GetAccountList(3);
		$data['cquery']=$this->alllist->GetCompanyList();
		$data['bquery']=$this->alllist->GetBranchList();
		$data['squery']=$this->alllist->GetSessionList();
		$data['stquery']=$this->alllist->GetStoreList();
		$data['fequery']=$this->alllist->GetProductList();
		$data['fpquery']=$this->alllist->GetFeePeriodList();
		$this->load->view('purchase',$data);	
	}
	
	function AddBill(){ // Function Standard PascalCase
		$this->Site_model->has_menupermission($this->uri->segment(1));
		$this->Purchase_model->InsertBillRecord();
		$this->Purchase_model->GetAjaxBillList();
		
	}
	function SaveBill(){ // Function Standard PascalCase
		$this->Site_model->has_menupermission($this->uri->segment(1));
		$bill_id		= $this->input->post('bill-id');
		if(empty($bill_id)){$bill_id=0;}
		if($bill_id >=0){
		$this->Purchase_model->saveBillMaster($bill_id);
		}
		$this->Purchase_model->GetRecordGrid();		
	}
	
	function FillDetails(){
		$this->Site_model->has_menupermission($this->uri->segment(1));
		$row = $this->Purchase_model->FillDetails();
		echo $row->details_id."##&##".$row->fee_account."##&##".$row->unit_price."##&##".$row->quantity."##&##".$row->free_qty."##&##".$row->total_price."##&##".$row->remarks;
	}	
	function FillRecord(){
		$this->Site_model->has_menupermission($this->uri->segment(1));
		$row = $this->Purchase_model->FillRecord();		
		echo "0##&##".$row->bill_id."##&##".$row->institute_id."##&##".$row->branch_id."##&##".$row->account_id."##&##".$row->session_id."##&##".$row->store_id."##&##".$row->billing_month."##&##".$this->Purchase_model->formatDateDMY($row->billing_date)."##&##".$row->discount_persent."##&##".$row->discount_amount."##&##".$row->vat_percentage."##&##".$row->vat_amount."##&##".$row->bill_amount."##&##".$row->net_bill_amount."##&##".$row->description."@@##@@";
	}
	function EditRecord(){
		$this->Site_model->has_menupermission($this->uri->segment(1));
		$this->Purchase_model->EditRecord();
	}
	function DelRecord(){
		$this->Site_model->has_menupermission($this->uri->segment(1));
		$this->Purchase_model->DelRecord();
	}
	function GetBillList(){
		$this->Site_model->has_menupermission($this->uri->segment(1));
		$this->Purchase_model->GetAjaxBillList();
	}	
	function GetRecords(){
		$this->Site_model->has_menupermission($this->uri->segment(1));
		$this->Purchase_model->GetRecordGrid();
	}
	function ViewBillForm(){
		$menu_slug= $this->uri->segment(1);	
		$this->Site_model->has_menupermission($menu_slug);
		$data['hasPrintOption'] = $this->Site_model->hasOptionPermission($menu_slug,"Print");
		$bill_id	= $this->uri->segment(3); $account_id	= $this->uri->segment(4);	
		$data['bill_id']= $bill_id;	
		$data['account_id']= $account_id;
		$this->load->view('print_purchase_bill',$data);
	}	
	function GetPurchaseBill(){
		$menu_slug= $this->uri->segment(1);	
		$this->Site_model->has_menupermission($menu_slug);
		$data['hasPrintOption'] = $this->Site_model->hasOptionPermission($menu_slug,"Print");	    
		
		$this->load->model("Report_model");		
		$this->Report_model->PrintPurchaseBill();
	}
	function GetAjaxPeriodList(){
		$institute_id = $this->input->post('institute-id');
		$branch_id  = $this->input->post('branch-id');
		$period_num = $this->input->post('period-num');
		echo $this->alllist->GetAjaxFeePeriodList($institute_id,$branch_id,$period_num=0);
	}
	
	function downloadPDFBill(){
		
	}	
}
