<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Workorder extends CI_Controller {	
	function __construct(){
		parent::__construct();
		$this->load->model("Workorder_model");
		$this->load->library('Alllist');
	}		
	function index(){
		$menu_slug= $this->uri->segment(1);	
		$this->Site_model->has_menupermission($menu_slug);
		$data['hasCreateOption']	= $this->Site_model->hasOptionPermission($menu_slug,"Create");				    
		$data['hasConcessionOption']= $this->Site_model->hasOptionPermission($menu_slug,"Concession");    
		$data['hasViewOption']  	= $this->Site_model->hasOptionPermission($menu_slug,"View");    
		$data['hasPrintOption']   	= $this->Site_model->hasOptionPermission($menu_slug,"Print");
		$headlist = array(11);
		$data['spquery']=$this->alllist->GetAccountList($headlist);
		$data['smquery']=$this->alllist->GetAccountList(1);
		$this->load->view('workorder',$data);	
	}
	
	function AddProduct(){ // Function Standard PascalCase
		$this->Site_model->has_menupermission($this->uri->segment(1));
		$this->Workorder_model->InsertDetailRecord();
		$this->Workorder_model->GetAjaxDetailList();
		
	}
	function SaveWorkorder(){ // Function Standard PascalCase
		$this->Site_model->has_menupermission($this->uri->segment(1));
		$workorder_id		= $this->input->post('workorder-id');
		if(empty($workorder_id)){$workorder_id=0;}
		if($workorder_id >=0){
		$this->Workorder_model->saveWorkorderMaster($workorder_id);
		}
		//$this->Workorder_model->GetRecordGrid();
		
		$msg="Successfully saved workorder!!!";
		$data['msg']=$msg;
		redirect(SERVER.'/workorder',$data);
	}
	
	function FillDetails(){
		$this->Site_model->has_menupermission($this->uri->segment(1));
		$row = $this->Workorder_model->FillDetails();
		
		echo $row->details_id."##&##".$row->category."##&##".$row->product_description."##&##".$row->product_sku."##&##".$row->unit_price."##&##".$row->quantity."##&##".$row->validity."##&##".$row->total_price."##&##".$row->vat_percentage."##&##".$row->vat_amount."##&##".$row->ait_percentage."##&##".$row->ait_amount."##&##".$row->remarks; 
	}	
	function FillRecord(){
		$this->Site_model->has_menupermission($this->uri->segment(1));
		$row = $this->Workorder_model->FillRecord();		
		echo "0##&##".$row->workorder_id."##&##".$row->workorder_no."##&##".$row->customer_id."##&##".$row->salesman_id."##&##".$row->workorder_type."##&##".$this->Workorder_model->formatDateDMY($row->workorder_date)."##&##".$row->oem."##&##".$row->total_bill."##&##".$row->discount_persent."##&##".$row->discount_amount."##&##".$row->sub_total."##&##".$row->vat_percentage."##&##".$row->vat_amount."##&##".$row->grand_total."##&##".$row->ait_percentage."##&##".$row->ait_amount."##&##".$row->net_bill_amount."##&##".$row->payment_mode."##&##".$row->payment_terms."##&##".$row->workorder_note."##&##".$row->description."##&##".$row->institute_id."##&##".$row->branch_id."##&##".$this->Workorder_model->formatDateDMY($row->delivery_date)."##&##".$row->midman_commission."##&##".$row->including_vat."@@##@@";
	}
	function EditRecord(){
		$this->Site_model->has_menupermission($this->uri->segment(1));
		$this->Workorder_model->EditRecord();
	}
	function DelRecord(){
		$this->Site_model->has_menupermission($this->uri->segment(1));
		$this->Workorder_model->DelRecord();
	}
	function DeleteRow(){
		$this->Site_model->has_menupermission($this->uri->segment(1));
		$this->Workorder_model->DelRowRecord();
		$this->Workorder_model->GetAjaxDetailList();
	}
	function GetProductList(){
		$this->Site_model->has_menupermission($this->uri->segment(1));
		$this->Workorder_model->GetAjaxDetailList();
	}	
	function GetRecords(){
		$this->Site_model->has_menupermission($this->uri->segment(1));
		$this->Workorder_model->GetRecordGrid();
	}	
	function GetRecordList(){
		$this->Site_model->has_menupermission($this->uri->segment(1));
		$this->Workorder_model->GetRecordListGrid();
	}
	function ViewWOForm(){
		$menu_slug= $this->uri->segment(1);	
		$this->Site_model->has_menupermission($menu_slug);
		$data['hasPrintOption'] = $this->Site_model->hasOptionPermission($menu_slug,"Print");
		$workorder_id	= $this->uri->segment(3); $customer_id	= $this->uri->segment(4);	
		$data['workorder_id']  = $workorder_id;	
		$data['customer_id']   = $customer_id;
		$this->load->view('print_workorder',$data);
	}	
	function GetWorkorder(){
		$menu_slug= $this->uri->segment(1);	
		$this->Site_model->has_menupermission($menu_slug);
		$data['hasPrintOption'] = $this->Site_model->hasOptionPermission($menu_slug,"Print");	    
		
		$this->load->model("Report_model");		
		$this->Report_model->PrintWorkorder();
	}
	function downloadWOPDFForm(){
		
	}	
			
	function WorkorderList(){
		$menu_slug= $this->uri->segment(1);	
		$this->Site_model->has_menupermission($menu_slug);    
		$data['hasViewOption']  	= $this->Site_model->hasOptionPermission($menu_slug,"View");    
		$data['hasPrintOption']   	= $this->Site_model->hasOptionPermission($menu_slug,"Print");
		$headlist = array(11);
		$data['spquery']=$this->alllist->GetAccountList($headlist);
		$this->load->view('workorder_list',$data);	
	}
}
