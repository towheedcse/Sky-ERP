<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Sales extends CI_Controller {	
	function __construct(){
		parent::__construct();
		$this->load->model("Sales_model");
		$this->load->library('Alllist');
	}		
	function index(){
		$menu_slug= $this->uri->segment(1);	
		$this->Site_model->has_menupermission($menu_slug);		
		$this->load->model("Voucher_model");
		$data['hasCreateOption']	= $this->Site_model->hasOptionPermission($menu_slug,"Create");				    
		$data['hasConcessionOption']= $this->Site_model->hasOptionPermission($menu_slug,"Concession");    
		$data['hasViewOption']  	= $this->Site_model->hasOptionPermission($menu_slug,"View");    
		$data['hasPrintOption']   	= $this->Site_model->hasOptionPermission($menu_slug,"Print");
		$headlist = array(11);
		$data['spquery']=$this->alllist->GetAccountList($headlist);
		$data['smquery']=$this->alllist->GetAccountList(1);
		$data['cquery']=$this->alllist->GetCompanyList();
		$data['bquery']=$this->alllist->GetBranchList();
		$data['squery']=$this->alllist->GetSessionList();
		$data['stquery']=$this->alllist->GetStoreList();
		$data['fequery']=$this->alllist->GetProductList();
		$data['fpquery']=$this->alllist->GetFeePeriodList();
		$data['supquery']           =$this->alllist->GetAccountList(3);
		$data['cash_account'] 		= $this->Voucher_model->GetDrAccountList(4);
		$data['bank_account'] 		= $this->Voucher_model->GetDrAccountList(5);
		$this->load->view('sales',$data);	
	}
	
	function AddBill(){ // Function Standard PascalCase
		$this->Site_model->has_menupermission($this->uri->segment(1));
		$this->Sales_model->InsertBillRecord();
		$this->Sales_model->GetAjaxBillList();
		
	}
	function SaveBill(){ // Function Standard PascalCase
		$this->Site_model->has_menupermission($this->uri->segment(1));
		$bill_id		= $this->input->post('bill-id');
		if(empty($bill_id)){$bill_id=0;}
		if($bill_id >=0){
		$this->Sales_model->saveBillMaster($bill_id);
		}
		$this->Sales_model->GetRecordGrid();		
	}
	
	function FillDetails(){
		$this->Site_model->has_menupermission($this->uri->segment(1));
		$row = $this->Sales_model->FillDetails();
		$stock_qty = $this->Sales_model->GetStockBalanceQty($row->institute_id,$row->branch_id,$row->session_id,$row->store_id,$row->account_id);
				
		echo $row->details_id."##&##".$row->fee_account."##&##".$row->unit_price."##&##".$row->quantity."##&##".$stock_qty."##&##".$row->total_price."##&##".$row->remarks."##&##".$row->admission_id."##&##".$row->cost_price."##&##".$row->version_id; // supplier: admission_id & cost price 4, applicant: version_id travel
	}	
	function FillRecord(){
		$this->Site_model->has_menupermission($this->uri->segment(1));
		$row = $this->Sales_model->FillRecord();		
		echo "0##&##".$row->bill_id."##&##".$row->institute_id."##&##".$row->branch_id."##&##".$row->account_id."##&##".$row->session_id."##&##".$row->store_id."##&##".$row->billing_month."##&##".$this->Sales_model->formatDateDMY($row->billing_date)."##&##".$row->discount_persent."##&##".$row->discount_amount."##&##".$row->vat_percentage."##&##".$row->vat_amount."##&##".$row->bill_amount."##&##".$row->net_bill_amount."##&##".$row->description."##&##".$row->payment_mode."##&##".$row->received_account."##&##".$row->paid_amount."##&##".$row->due_amount."##&##".$row->version_id."##&##".$row->group_id."##&##".$row->class_id."##&##".$row->shift_id."##&##".$this->Sales_model->formatDateDMY($row->travel_date)."##&##".$row->depart_datetime."##&##".$row->arraival_datetime."##&##".$row->pnr_no."##&##".$row->deposit_amount."##&##".$row->airline."##&##".$row->depart_place."##&##".$row->arrival_place."##&##".$row->invoice_note1."##&##".$row->invoice_note2."##&##".$row->invoice_note3."@@##@@";
	}
	function EditRecord(){
		$this->Site_model->has_menupermission($this->uri->segment(1));
		$this->Sales_model->EditRecord();
	}
	function DelRecord(){
		$this->Site_model->has_menupermission($this->uri->segment(1));
		$this->Sales_model->DelRecord();
	}
	function DeleteRow(){
		$this->Site_model->has_menupermission($this->uri->segment(1));
		$this->Sales_model->DelRowRecord();
		$this->Sales_model->GetAjaxBillList();
	}
	function GetBillList(){
		$this->Site_model->has_menupermission($this->uri->segment(1));
		$this->Sales_model->GetAjaxBillList();
	}	
	function GetRecords(){
		$this->Site_model->has_menupermission($this->uri->segment(1));
		$this->Sales_model->GetRecordGrid();
	}
	function ViewBillForm(){
		$menu_slug= $this->uri->segment(1);	
		$this->Site_model->has_menupermission($menu_slug);
		$data['hasPrintOption'] = $this->Site_model->hasOptionPermission($menu_slug,"Print");
		$bill_id	= $this->uri->segment(3); $account_id	= $this->uri->segment(4);	
		$data['bill_id']= $bill_id;	
		$data['account_id']= $account_id;
		$this->load->view('print_sales_bill',$data);
	}	
	function GetSalesBill(){
		$menu_slug= $this->uri->segment(1);	
		$this->Site_model->has_menupermission($menu_slug);
		$data['hasPrintOption'] = $this->Site_model->hasOptionPermission($menu_slug,"Print");	    
		
		$this->load->model("Report_model");		
		$this->Report_model->PrintSalesBill();
	}
	function GetAjaxPeriodList(){
		$institute_id = $this->input->post('institute-id');
		$branch_id  = $this->input->post('branch-id');
		$period_num = $this->input->post('period-num');
		echo $this->alllist->GetAjaxFeePeriodList($institute_id,$branch_id,$period_num=0);
	}
	function loadProductSalesPrice(){
		$this->Site_model->has_menupermission($this->uri->segment(1));
		$this->Sales_model->GetAjaxProductSalesPrice();
	}
	function loadSupplierBalance(){
		$this->Site_model->has_menupermission($this->uri->segment(1));
		$this->Sales_model->GetAjaxSupplierBalance();
	}
	function downloadPDFBill(){
		
	}	
}
