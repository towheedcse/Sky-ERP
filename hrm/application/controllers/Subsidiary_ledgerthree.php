<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Subsidiary_Ledgerthree extends CI_Controller {
	function __construct(){
		parent::__construct();
		$this->load->model("Subsidiaryledgerthree_model");
		$this->load->model("Accounthead_model");		
		$this->load->library('alllist');	
	}

	function index(){						
		$menu_slug= $this->uri->segment(1);	
		$this->Site_model->has_menupermission($menu_slug);			    
		$data['hasCreateOption']= $this->Site_model->hasOptionPermission($menu_slug,"Create");	    
		$data['hasViewOption']  = $this->Site_model->hasOptionPermission($menu_slug,"View");
		
		$data['gquery'] =$this->alllist->GetGroupLedgerList();
        	$data['cquery'] =$this->alllist->GetCompanyList();
		$this->load->view('subsidiary_ledger3',$data);
	}
	function AddRecord(){// Function Standard PascalCase
		$this->Site_model->has_menupermission($this->uri->segment(1));

		$this->Subsidiaryledgerthree_model->InsertRecord();
		$this->Subsidiaryledgerthree_model->GetRecordGrid();
	}
	function GetRecords(){
		$this->Site_model->has_menupermission($this->uri->segment(1));

		$this->Subsidiaryledgerthree_model->GetRecordGrid();
	}
	function DeleteRecord(){
		$this->Site_model->has_menupermission($this->uri->segment(1));
		$this->Subsidiaryledgerthree_model->DeleteRecord();
	}
	function FillRecord(){
		$this->Site_model->has_menupermission($this->uri->segment(1));
		
		$row = $this->Subsidiaryledgerthree_model->FillRecord();	
		if(empty($row->sub1_id)){$sub_head1=0;}else{$sub_head1=$row->sub1_id;}
	  	$SubHead1 = $this->alllist->GetAjaxSubAccountList($row->company_id,$row->group_id,$sub_head1);	
		if(empty($row->sub2_id)){$sub_head2=0;}else{$sub_head2=$row->sub2_id;}
	  	$SubHead2 = $this->alllist->GetAjaxSubAccountList($row->company_id,$row->group_id,$sub_head1);	
		echo $row->sub3_id."##&##".$row->sub1_id."##&##".$row->sub2_id."##&##".$row->company_id."##&##".$row->group_id."##&##".$row->subsidiary_name3."@@##@@".$SubHead1."@@##@@".$SubHead2;
	}
	function editRecord(){
		$this->Site_model->has_menupermission($this->uri->segment(1));

		$this->Subsidiaryledgerthree_model->EditRecord();
	}	

//End Class
}
