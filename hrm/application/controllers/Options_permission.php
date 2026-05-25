<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Options_permission extends CI_Controller {
	function __construct(){
		parent::__construct();
		$this->load->model("Options_model");
		$this->load->library('Alllist');
	}
		
	function index(){
		$menu_slug= $this->uri->segment(1);	
		$this->Site_model->has_menupermission($menu_slug);			    
	    $data['hasViewOption']  = $this->Site_model->hasOptionPermission($menu_slug,"View");
		$data['cquery']=$this->alllist->GetCompanyList();
        $data['mquery']=$this->alllist->GetModuleList();
        $data['muquery']=$this->alllist->GetMenuList();
		$this->load->view('options_permission',$data);
	}
	function GetPermissionRecord(){
		$this->Site_model->has_menupermission($this->uri->segment(1));
		echo $this->Options_model->GetPermissionRecordGrid();
		
	}
	function SaveRecord(){ // Function Standard PascalCase
		$this->Site_model->has_menupermission($this->uri->segment(1));
		$this->Options_model->InsertPermissionRecord();
		echo $this->Options_model->GetPermissionRecordGrid();
		
	}
}
