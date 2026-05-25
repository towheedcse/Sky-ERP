<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Deliverychallan extends CI_Controller {	
	function __construct(){
		parent::__construct();
		$this->load->model("Deliverychallan_model");
		$this->load->library('Alllist');
	}		
	function index(){
		$menu_slug= $this->uri->segment(1);	
		$this->Site_model->has_menupermission($menu_slug);
		$data['hasCreateOption']	= $this->Site_model->hasOptionPermission($menu_slug,"Create");				    
		$data['hasConcessionOption']= $this->Site_model->hasOptionPermission($menu_slug,"Concession");    
		$data['hasViewOption']  	= $this->Site_model->hasOptionPermission($menu_slug,"View");    
		$data['hasPrintOption']   	= $this->Site_model->hasOptionPermission($menu_slug,"Print");
		
		$data['cuquery']=$this->alllist->GetAccountList(11);
		$this->load->view('delivery_challan',$data);	
	}
	
	function AddProduct(){ // Function Standard PascalCase
		$this->Site_model->has_menupermission($this->uri->segment(1));
		$this->Deliverychallan_model->InsertDetailRecord();
		$this->Deliverychallan_model->GetAjaxDetailList();
		
	}
	function SaveDC(){ // Function Standard PascalCase
		$this->Site_model->has_menupermission($this->uri->segment(1));
		$challan_id		= $this->input->post('challan-id');
		if(empty($challan_id)){$challan_id=0;}
		if($challan_id >=0){
		$this->Deliverychallan_model->saveDCMaster($challan_id);
		}
		$this->Deliverychallan_model->GetRecordGrid();
		
		$msg="Successfully saved delivery challan!!!";
		$data['msg']=$msg;
		//redirect(SERVER.'/deliverychallan',$data);
	}
	
	function FillDetails(){
		$this->Site_model->has_menupermission($this->uri->segment(1));
		$row = $this->Deliverychallan_model->FillDetails();
		
		echo $row->details_id."##&##".$row->product_description."##&##".$row->product_sku."##&##".$row->unit_price."##&##".$row->quantity."##&##".$row->validity."##&##".$this->Deliverychallan_model->formatDateDMY($row->start_date)."##&##".$this->Deliverychallan_model->formatDateDMY($row->end_date)."##&##".$row->total_price."##&##".$row->remarks;
	}	
	function FillRecord(){
		$this->Site_model->has_menupermission($this->uri->segment(1));
		$row = $this->Deliverychallan_model->FillRecord();
		$WorkorderList = $this->Deliverychallan_model->getWorkorderList($row->customer_id,$row->workorder_id);
		$ProductGrid   = $this->Deliverychallan_model->GetProductDetailList($row->challan_id,$row->customer_id,$row->workorder_id);
		echo "0##&##".$row->challan_id."##&##".$row->challan_no."##&##".$row->customer_id."##&##".$row->workorder_id."##&##".$this->Deliverychallan_model->formatDateDMY($row->challan_date)."##&##".$row->delivery_address."##&##".$row->delivery_note."##&##".$row->status."##&##".$row->institute_id."##&##".$row->branch_id."@@##@@".$WorkorderList."@@##@@".$ProductGrid;
	}
	function EditRecord(){
		$this->Site_model->has_menupermission($this->uri->segment(1));
		$this->Deliverychallan_model->EditRecord();
	}
	function DelRecord(){
		$this->Site_model->has_menupermission($this->uri->segment(1));
		$this->Deliverychallan_model->DelRecord();
	}
	function DeleteRow(){
		$this->Site_model->has_menupermission($this->uri->segment(1));
		$this->Deliverychallan_model->DelRowRecord();
		$this->Deliverychallan_model->GetAjaxDetailList();
	}
	function ApproveDC(){
		$this->Site_model->has_menupermission($this->uri->segment(1));
		$this->Deliverychallan_model->ApproveDC();
	}
	function UnapproveDC(){
		$this->Site_model->has_menupermission($this->uri->segment(1));
		$this->Deliverychallan_model->UnapproveDC();
	}
	function GetProductList(){
		$this->Site_model->has_menupermission($this->uri->segment(1));
		$this->Deliverychallan_model->GetAjaxDetailList();
	}
	function getWorkorderList(){
		$this->Site_model->has_menupermission($this->uri->segment(1));
		if($this->input->post('id') >0){
		 echo $this->Deliverychallan_model->getAjaxWorkorderList();
		}
	}
	
	function GetRecords(){
		$this->Site_model->has_menupermission($this->uri->segment(1));
		$this->Deliverychallan_model->GetRecordGrid();
	}
	function ViewDCForm(){
		$menu_slug= $this->uri->segment(1);	
		$this->Site_model->has_menupermission($menu_slug);
		$data['hasPrintOption'] = $this->Site_model->hasOptionPermission($menu_slug,"Print");
		$challan_id	= $this->uri->segment(3); $workorder_id = $this->uri->segment(4);	
		$data['challan_id']   = $challan_id;	
		$data['workorder_id'] = $workorder_id;
		$this->load->view('print_delivery_challan',$data);
	}	
	function GetChallanDetails(){
		$menu_slug= $this->uri->segment(1);	
		$this->Site_model->has_menupermission($menu_slug);
		$data['hasPrintOption'] = $this->Site_model->hasOptionPermission($menu_slug,"Print");	    
		
		$this->load->model("Report_model");		
		$this->Report_model->PrintDeliveryChallan();
	}
	function downloadPDFDC(){
		
	}
			
	function ChallanList(){
		$menu_slug= $this->uri->segment(1);	
		$this->Site_model->has_menupermission($menu_slug);    
		$data['hasViewOption']  	= $this->Site_model->hasOptionPermission($menu_slug,"View");    
		$data['hasPrintOption']   	= $this->Site_model->hasOptionPermission($menu_slug,"Print");
		$headlist = array(2); $iheadlist = array(3);
		$data['spquery']=$this->alllist->GetAccountList($headlist);
		$data['imquery']=$this->alllist->GetAccountList($iheadlist);
		$this->load->view('challan_list',$data);	
	}
	function GetChallanList(){
		$this->Site_model->has_menupermission($this->uri->segment(1));
		$this->Deliverychallan_model->GetChallanGridList();
	}
}
