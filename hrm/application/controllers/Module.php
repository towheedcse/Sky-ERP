<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Module extends CI_Controller {	
	function __construct(){
		parent::__construct();
		$this->load->model("Module_model");
		$this->load->library('alllist');
	}
	function index(){
		$menu_slug= $this->uri->segment(1);	
		$this->Site_model->has_menupermission($menu_slug);			    
		$data['hasCreateOption']= $this->Site_model->hasOptionPermission($menu_slug,"Create");	    
		$data['hasViewOption']  = $this->Site_model->hasOptionPermission($menu_slug,"View");		
		$data['cquery']=$this->alllist->GetCompanyList();
		$this->load->view('module',$data);	
	}
	function AddRecord(){ // Function Standard PascalCase
		$this->Site_model->has_menupermission($this->uri->segment(1));
		$this->Module_model->InsertRecord();
		$this->Module_model->GetRecordGrid();
	}
	function FillRecord(){
		$this->Site_model->has_menupermission($this->uri->segment(1));
		$row = $this->Module_model->FillRecord();
		echo $row->module_id."##&##".$row->company_id."##&##".$row->module_name."##&##".$row->module_icon."##&##".$row->order_no."##&##".$row->module_status;
	}
	function EditRecord(){
		$this->Site_model->has_menupermission($this->uri->segment(1));
		$this->Module_model->EditRecord();
	}
	function DelRecord(){
		$this->Site_model->has_menupermission($this->uri->segment(1));
		$this->Module_model->DelRecord();
	}
	function GetRecord(){
		$this->Site_model->has_menupermission($this->uri->segment(1));
		$this->Module_model->GetRecordGrid();
	}
}
