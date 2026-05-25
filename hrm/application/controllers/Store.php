<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Store extends CI_Controller {	
	function __construct(){
		parent::__construct();
		$this->load->model("Store_model");
		$this->load->library('Alllist');
	}
		
	function index(){
		$menu_slug= $this->uri->segment(1);	
		$this->Site_model->has_menupermission($menu_slug);			    
		$data['hasCreateOption']= $this->Site_model->hasOptionPermission($menu_slug,"Create");	    
		$data['hasViewOption']  = $this->Site_model->hasOptionPermission($menu_slug,"View");	    
		$data['hasPrintOption']  = $this->Site_model->hasOptionPermission($menu_slug,"Print");
		$data['cquery']=$this->alllist->GetCompanyList();
		$data['bquery']=$this->alllist->GetBranchList();
		$this->load->view('store',$data);	
	}
	function AddRecord(){ // Function Standard PascalCase
		$this->Site_model->has_menupermission($this->uri->segment(1));
		$this->Store_model->InsertRecord();
		$this->Store_model->GetRecordGrid();
		
	}
	function FillRecord(){
		$this->Site_model->has_menupermission($this->uri->segment(1));
		$row = $this->Store_model->FillRecord();
		echo $row->store_id."##&##".$row->institute_id."##&##".$row->branch_id."##&##".$row->store_name."##&##".$row->status;
	}
	function EditRecord(){
		$this->Site_model->has_menupermission($this->uri->segment(1));
		$this->Store_model->EditRecord();
	}
	function DelRecord(){
		$this->Site_model->has_menupermission($this->uri->segment(1));
		$this->Store_model->DelRecord();
	}
	function GetRecord(){
		$this->Site_model->has_menupermission($this->uri->segment(1));
		$this->Store_model->GetRecordGrid();
		
	}
}
