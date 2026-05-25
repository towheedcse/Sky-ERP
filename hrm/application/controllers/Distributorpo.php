<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Distributorpo extends CI_Controller {	
	function __construct(){
		parent::__construct();
		$this->load->model("Distributorpo_model");
		$this->load->library('Alllist');
	}		
	function index(){
		$menu_slug= $this->uri->segment(1);	
		$this->Site_model->has_menupermission($menu_slug);
		$data['hasCreateOption']	= $this->Site_model->hasOptionPermission($menu_slug,"Create");				    
		$data['hasConcessionOption']= $this->Site_model->hasOptionPermission($menu_slug,"Concession");    
		$data['hasViewOption']  	= $this->Site_model->hasOptionPermission($menu_slug,"View");    
		$data['hasPrintOption']   	= $this->Site_model->hasOptionPermission($menu_slug,"Print");
		$headlist = array(2); $iheadlist = array(3);
		$data['spquery']=$this->alllist->GetAccountList($headlist);
		$data['imquery']=$this->alllist->GetAccountList($iheadlist);
		$data['cuquery']=$this->alllist->GetAccountList(11);
		$this->load->view('distributor_po',$data);	
	}
	
	function InsertFromWorkorder(){ // Function Standard PascalCase
		$this->Site_model->has_menupermission($this->uri->segment(1));
		$workorder_id = $this->input->post('workorder-id');
		if(empty($workorder_id)){$workorder_id=0;}
		if($workorder_id >0){
		$this->Distributorpo_model->GetSaveWODetailRecord($workorder_id);
		$this->Distributorpo_model->GetAjaxDetailList();
		} 
	}
	function AddProduct(){ // Function Standard PascalCase
		$this->Site_model->has_menupermission($this->uri->segment(1));
		$this->Distributorpo_model->InsertDetailRecord();
		$this->Distributorpo_model->GetAjaxDetailList(); 
		
	}
	function SavePO(){ // Function Standard PascalCase
		$this->Site_model->has_menupermission($this->uri->segment(1));
		$po_id		= $this->input->post('po-id');
		if(empty($po_id)){$po_id=0;}
		if($po_id >=0){
		$this->Distributorpo_model->savePOMaster($po_id);
		}
		//$this->Distributorpo_model->GetRecordGrid();
		
		$msg="Successfully saved distributor PO!!!";
		$data['msg']=$msg;
		redirect(SERVER.'/distributorpo',$data);
	}
	
	function FillDetails(){
		$this->Site_model->has_menupermission($this->uri->segment(1));
		$row = $this->Distributorpo_model->FillDetails();
		
		echo $row->details_id."##&##".$row->product_description."##&##".$row->product_sku."##&##".$row->unit_price."##&##".$row->quantity."##&##".$row->validity."##&##".$row->total_price."##&##".$row->vat_percentage."##&##".$row->vat_amount."##&##".$row->ait_percentage."##&##".$row->ait_amount."##&##".$row->remarks;
	}	
	function FillRecord(){
		$this->Site_model->has_menupermission($this->uri->segment(1));
		$row = $this->Distributorpo_model->FillRecord();
		$WorkorderList = $this->Distributorpo_model->getWorkorderList($row->customer_id,$row->workorder_id);
		$ProductGrid   = $this->Distributorpo_model->GetProductDetailList($row->po_id,$row->distributor_id,$row->customer_id,$row->workorder_id);
		echo "0##&##".$row->po_id."##&##".$row->po_no."##&##".$row->distributor_id."##&##".$row->importer_id."##&##".$row->customer_id."##&##".$row->workorder_id."##&##".$this->Distributorpo_model->formatDateDMY($row->po_date)."##&##".$row->currency_id."##&##".$row->currency."##&##".$row->attention."##&##".$row->subject."##&##".$row->total_bill."##&##".$row->discount_persent."##&##".$row->discount_amount."##&##".$row->sub_total."##&##".$row->vat_percentage."##&##".$row->vat_amount."##&##".$row->grand_total."##&##".$row->ait_percentage."##&##".$row->ait_amount."##&##".$row->net_bill_amount."##&##".$row->payment_mode."##&##".$row->payment_terms."##&##".$row->delivery_to."##&##".$row->ship_to."##&##".$row->bill_to."##&##".$row->status."##&##".$row->institute_id."##&##".$row->branch_id."##&##".$row->including_vat."@@##@@".$WorkorderList."@@##@@".$ProductGrid;
	}
	function EditRecord(){
		$this->Site_model->has_menupermission($this->uri->segment(1));
		$this->Distributorpo_model->EditRecord();
	}
	function DelRecord(){
		$this->Site_model->has_menupermission($this->uri->segment(1));
		$this->Distributorpo_model->DelRecord();
	}
	function DeleteRow(){
		$this->Site_model->has_menupermission($this->uri->segment(1));
		$this->Distributorpo_model->DelRowRecord();
		$this->Distributorpo_model->GetAjaxDetailList();
	}
	function ApprovePO(){
		$this->Site_model->has_menupermission($this->uri->segment(1));
		$this->Distributorpo_model->ApprovePO();
	}
	function UnapprovePO(){
		$this->Site_model->has_menupermission($this->uri->segment(1));
		$this->Distributorpo_model->UnapprovePO();
	}
	function GetProductList(){
		$this->Site_model->has_menupermission($this->uri->segment(1));
		$this->Distributorpo_model->GetAjaxDetailList();
	}
	function getDistriInfo(){
		$this->Site_model->has_menupermission($this->uri->segment(1));
		if($this->input->post('id') >0){
		$drow = $this->Distributorpo_model->getAjaxDistriInfo();
		echo $drow->contact_person."##&##".$drow->currency_name."##&##".$drow->currency_id."##&##".$drow->shipping_address."##&##".$drow->billing_address;
		}
	}
	function getWorkorderList(){
		$this->Site_model->has_menupermission($this->uri->segment(1));
		if($this->input->post('id') >0){
		 echo $this->Distributorpo_model->getAjaxWorkorderList();
		}
	}
	
	function GetRecords(){
		$this->Site_model->has_menupermission($this->uri->segment(1));
		$this->Distributorpo_model->GetRecordGrid();
	}
	function ViewPOForm(){
		$menu_slug= $this->uri->segment(1);	
		$this->Site_model->has_menupermission($menu_slug);
		$data['hasPrintOption'] = $this->Site_model->hasOptionPermission($menu_slug,"Print");
		$po_id	= $this->uri->segment(3); $distributor_id = $this->uri->segment(4);	
		$data['po_id']          = $po_id;	
		$data['distributor_id'] = $distributor_id;
		$this->load->view('print_distri_po',$data);
	}	
	function GetPODetails(){
		$menu_slug= $this->uri->segment(1);	
		$this->Site_model->has_menupermission($menu_slug);
		$data['hasPrintOption'] = $this->Site_model->hasOptionPermission($menu_slug,"Print");	    
		
		$this->load->model("Report_model");		
		$this->Report_model->PrintProformaInvoice();
	}
	function downloadPDPO(){
		
	}
			
	function DistriPOList(){
		$menu_slug= $this->uri->segment(1);	
		$this->Site_model->has_menupermission($menu_slug);    
		$data['hasViewOption']  	= $this->Site_model->hasOptionPermission($menu_slug,"View");    
		$data['hasPrintOption']   	= $this->Site_model->hasOptionPermission($menu_slug,"Print");
		$headlist = array(2); $iheadlist = array(3);
		$data['spquery']=$this->alllist->GetAccountList($headlist);
		$data['imquery']=$this->alllist->GetAccountList($iheadlist);
		$this->load->view('distri_po_list',$data);	
	}
	function GetDistriPOList(){
		$this->Site_model->has_menupermission($this->uri->segment(1));
		$this->Distributorpo_model->GetDistriPOGridList();
	}
			
	function ImporterPOList(){
		$menu_slug= $this->uri->segment(1);	
		$this->Site_model->has_menupermission($menu_slug);    
		$data['hasViewOption']  	= $this->Site_model->hasOptionPermission($menu_slug,"View");    
		$data['hasPrintOption']   	= $this->Site_model->hasOptionPermission($menu_slug,"Print");
		$headlist = array(2); $iheadlist = array(3);
		$data['spquery']=$this->alllist->GetAccountList($headlist);
		$data['imquery']=$this->alllist->GetAccountList($iheadlist);
		$this->load->view('importer_po_list',$data);	
	}
	
	function GetImporterPOList(){
		$this->Site_model->has_menupermission($this->uri->segment(1));
		$this->Distributorpo_model->GetImporterPOGridList();
	}
	function ViewIPOForm(){
		$menu_slug= $this->uri->segment(1);	
		$this->Site_model->has_menupermission($menu_slug);
		$data['hasPrintOption'] = $this->Site_model->hasOptionPermission($menu_slug,"Print");
		$po_id	= $this->uri->segment(3); $importer_id = $this->uri->segment(4);	
		$data['po_id']       = $po_id;	
		$data['importer_id'] = $importer_id;
		$this->load->view('print_importer_po',$data);
	}	
	function GetIPODetails(){
		$menu_slug= $this->uri->segment(1);	
		$this->Site_model->has_menupermission($menu_slug);
		$data['hasPrintOption'] = $this->Site_model->hasOptionPermission($menu_slug,"Print");	    
		
		$this->load->model("Report_model");		
		$this->Report_model->PrintImporterPO();
	}
	//===== Start Approval Top Sheet ======
	
	function ViewReqForm(){
		$menu_slug= $this->uri->segment(1);	
		$this->Site_model->has_menupermission($menu_slug);
		$data['hasPrintOption'] = $this->Site_model->hasOptionPermission($menu_slug,"Print");
		$po_id	= $this->uri->segment(3); $workorder_id = $this->uri->segment(4);
		$distributor_id = $this->uri->segment(5); $importer_id = $this->uri->segment(6);
		$data['po_id']          = $po_id;	
		$data['workorder_id']   = $workorder_id;	
		$data['distributor_id'] = $distributor_id;	
		$data['importer_id']    = $importer_id;
		$this->load->view('print_payment_requisation',$data);
	}	
	function GetReqDetails(){
		$menu_slug= $this->uri->segment(1);	
		$this->Site_model->has_menupermission($menu_slug);
		$data['hasPrintOption'] = $this->Site_model->hasOptionPermission($menu_slug,"Print");	    
		
		$this->load->model("Report_model");		
		$this->Report_model->PrintReqTopSheet();
	}
}
