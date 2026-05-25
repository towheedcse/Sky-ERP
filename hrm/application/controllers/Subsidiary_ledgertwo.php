<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Subsidiary_Ledgertwo extends CI_Controller {
	function __construct(){
		parent::__construct();
		$this->load->model("Subsidiaryledgertwo_model");
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
		$this->load->view('subsidiary_ledger2',$data);
	}
	function AddRecord(){// Function Standard PascalCase
		$this->Site_model->has_menupermission($this->uri->segment(1));

		$this->Subsidiaryledgertwo_model->InsertRecord();
		$this->Subsidiaryledgertwo_model->GetRecordGrid();
	}
	function GetRecords(){
		$this->Site_model->has_menupermission($this->uri->segment(1));

		$this->Subsidiaryledgertwo_model->GetRecordGrid();
	}
	function DeleteRecord(){
		$this->Site_model->has_menupermission($this->uri->segment(1));
		$this->Subsidiaryledgertwo_model->DeleteRecord();
	}
	function FillRecord(){
		$this->Site_model->has_menupermission($this->uri->segment(1));
		
		$row = $this->Subsidiaryledgertwo_model->FillRecord();	
		if(empty($row->sub_id)){$sub_head1=0;}else{$sub_head1=$row->sub_id;}
	  	$SubHead1 = $this->alllist->GetAjaxSubAccountList($row->company_id,$row->group_id,$sub_head1);	
		echo $row->sub2_id."##&##".$row->company_id."##&##".$row->group_id."##&##".$row->sub_id."##&##".$row->subsidiary_name2."@@##@@".$SubHead1;
	}
	function editRecord(){
		$this->Site_model->has_menupermission($this->uri->segment(1));

		$this->Subsidiaryledgertwo_model->EditRecord();
	}	

//End Class
}
