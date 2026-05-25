<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Branch extends CI_Controller {	
	function __construct(){
		parent::__construct();
		$this->load->model("Branch_model");
		$this->load->library('Alllist');
	}
		
	function index(){
		$menu_slug= $this->uri->segment(1);	
		$this->Site_model->has_menupermission($menu_slug);			    
	    	$data['hasCreateOption']= $this->Site_model->hasOptionPermission($menu_slug,"Create");	    
	    	$data['hasViewOption']  = $this->Site_model->hasOptionPermission($menu_slug,"View");
		$data['cquery']=$this->alllist->GetCompanyList();
		$this->load->view('branch',$data);	
	}
	function AddRecord(){ // Function Standard PascalCase
		$this->Site_model->has_menupermission($this->uri->segment(1));
		$this->Branch_model->InsertRecord();
		$this->Branch_model->GetRecordGrid();
		
	}
	function FillRecord(){
		$this->Site_model->has_menupermission($this->uri->segment(1));
		$row = $this->Branch_model->FillRecord();
		echo $row->branch_id."##&##".$row->company_id."##&##".$row->branch_name."##&##".$row->branch_address."##&##".$row->phone."##&##".$row->mobile."##&##".$row->email;
	}
	function EditRecord(){
		$this->Site_model->has_menupermission($this->uri->segment(1));
		$this->Branch_model->EditRecord();
	}
	function DelRecord(){
		$this->Site_model->has_menupermission($this->uri->segment(1));
		$this->Branch_model->DelRecord();
	}
	function GetRecord(){
		$this->Site_model->has_menupermission($this->uri->segment(1));
		$this->Branch_model->GetRecordGrid();
		
	}
}
