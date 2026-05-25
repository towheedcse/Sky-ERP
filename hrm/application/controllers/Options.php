<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Options extends CI_Controller {
	function __construct(){
		parent::__construct();
		$this->load->model("Options_model");
		$this->load->library('Alllist');
	}
		
	function index(){			
		$menu_slug= $this->uri->segment(1);	
		$this->Site_model->has_menupermission($menu_slug);			    
		$data['hasCreateOption']=$this->Site_model->hasOptionPermission($menu_slug,"Create");	    
		$data['hasViewOption']  =$this->Site_model->hasOptionPermission($menu_slug,"View");	
		$data['cquery']			=$this->alllist->GetCompanyList();
		$data['mquery']			=$this->alllist->GetModuleList();
		$data['muquery']		=$this->alllist->GetMenuList();
		$this->load->view('options',$data);
	}
	function AddRecord(){ // Function Standard PascalCase
		$this->Site_model->has_menupermission($this->uri->segment(1));		
		$options_id		=$this->input->post('options_id');
		if($options_id >0){
		$this->Options_model->EditRecord($options_id);
		}else{
		$this->Options_model->InsertRecord();
		}
		$this->Options_model->GetRecordGrid();
		
	}
	function FillRecord(){
		$this->Site_model->has_menupermission($this->uri->segment(1));
		$row = $this->Options_model->FillRecord();
		echo $row->options_id."##&##".$row->company_id."##&##".$row->module_id."##&##".$row->menu_id."##&##".$row->action_type."##&##".$row->action_name."##&##".$row->action_status;
	}
	function EditRecord(){
		$this->Site_model->has_menupermission($this->uri->segment(1));
		$this->Options_model->EditRecord();
	}
	function DelRecord(){
		$this->Site_model->has_menupermission($this->uri->segment(1));
		$this->Options_model->DelRecord();
	}
	function GetRecord(){
		$this->Site_model->has_menupermission($this->uri->segment(1));
		$this->Options_model->GetRecordGrid();
	}
}
