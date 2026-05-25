<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Menu_permission extends CI_Controller {
	function __construct(){
		parent::__construct();
		$this->load->model("Menu_model");
		$this->load->library('Alllist');
	}
		
	function index(){
		$menu_slug= $this->uri->segment(1);	
		$this->Site_model->has_menupermission($menu_slug);			    
	    	$data['hasViewOption']  = $this->Site_model->hasOptionPermission($menu_slug,"View");
		$data['cquery']=$this->alllist->GetCompanyList();
        	$data['mquery']=$this->alllist->GetModuleList();
		$this->load->view('menu_permission',$data);
	}
	function GetPermissionRecord(){
		$this->Site_model->has_menupermission($this->uri->segment(1));	
		echo $this->Menu_model->GetPermissionRecordGrid();
		
	}
	function SaveRecord(){ // Function Standard PascalCase
		$this->Site_model->has_menupermission($this->uri->segment(1));
		$this->Menu_model->InsertPermissionRecord();
		echo $this->Menu_model->GetPermissionRecordGrid();
		
	}
}
