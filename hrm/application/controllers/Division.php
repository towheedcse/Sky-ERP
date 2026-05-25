<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Division extends CI_Controller {	
	function __construct(){
		parent::__construct();
		$this->load->model("Division_model");
		$this->load->library('Alllist');
	}
		
	function index(){
		$menu_slug= $this->uri->segment(1);	
		$this->Site_model->has_menupermission($menu_slug);			    
		$data['hasCreateOption']= $this->Site_model->hasOptionPermission($menu_slug,"Create");	    
		$data['hasViewOption']  = $this->Site_model->hasOptionPermission($menu_slug,"View");	
		$data['cquery']=$this->alllist->GetCompanyList();		
		$this->load->view('division',$data);	
	}
	function AddRecord(){ // Function Standard PascalCase
		$this->Site_model->has_menupermission($this->uri->segment(1));
		$this->Division_model->InsertRecord();
		$this->Division_model->GetRecordGrid();
		
	}
	function FillRecord(){
		$this->Site_model->has_menupermission($this->uri->segment(1));
		$row = $this->Division_model->FillRecord();
		echo $row->division_id."##&##".$row->company_id."##&##".$row->division_name."##&##".$row->division_status;
	}
	function EditRecord(){
		$this->Site_model->has_menupermission($this->uri->segment(1));
		$this->Division_model->EditRecord();
	}
	function DelRecord(){
		$this->Site_model->has_menupermission($this->uri->segment(1));
		$this->Division_model->DelRecord();
	}
	function GetRecord(){
		$this->Site_model->has_menupermission($this->uri->segment(1));
		$this->Division_model->GetRecordGrid();
	}
}
