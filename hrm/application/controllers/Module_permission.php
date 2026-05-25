<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Module_permission extends CI_Controller {	
	function __construct(){
		parent::__construct();
		$this->load->model("Module_model");
		$this->load->library('Alllist');
	}
		
	function index(){
		$menu_slug= $this->uri->segment(1);	
		$this->Site_model->has_menupermission($menu_slug);			    
	    	$data['hasViewOption']  = $this->Site_model->hasOptionPermission($menu_slug,"View");
		$data['cquery']=$this->alllist->GetCompanyList();
		$this->load->view('module_permission',$data);	
	}
	function GetModulePermissionRecord(){
		$this->Site_model->has_menupermission($this->uri->segment(1));
		echo $this->Module_model->GetModulePermissionRecordGrid();
		
	}
	function SaveRecord(){ // Function Standard PascalCase
		$this->Site_model->has_menupermission($this->uri->segment(1));
		$this->Module_model->InsertPermissionRecord();
		echo $this->Module_model->GetModulePermissionRecordGrid();
		
	}
}
