<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Subsidiary_Ledger extends CI_Controller {
	function __construct(){
		parent::__construct();
		$this->load->model("Subsidiaryledger_model");
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
		$this->load->view('subsidiary_ledger',$data);
	}
	function AddRecord(){// Function Standard PascalCase
		$this->Site_model->has_menupermission($this->uri->segment(1));

		$this->Subsidiaryledger_model->InsertRecord();
		$this->Subsidiaryledger_model->GetRecordGrid();
	}
	function GetRecords(){
		$this->Site_model->has_menupermission($this->uri->segment(1));
		$this->Subsidiaryledger_model->GetRecordGrid();
		
	}
	function DeleteRecord(){
		$this->Site_model->has_menupermission($this->uri->segment(1));
		$this->Subsidiaryledger_model->DeleteRecord();
	}
	function FillRecord(){
		$this->Site_model->has_menupermission($this->uri->segment(1));

		$row     = $this->Subsidiaryledger_model->FillRecord();		
		echo $row->company_id."##&##".$row->sub_id."##&##".$row->sub_head_name."##&##".$row->parents_id."@@##@@"." ";
	}
	function editRecord(){
		$this->Site_model->has_menupermission($this->uri->segment(1));

		$this->Subsidiaryledger_model->EditRecord();
	}	

//End Class
}
