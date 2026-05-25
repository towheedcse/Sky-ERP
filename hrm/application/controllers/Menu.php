<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Menu extends CI_Controller {
	function __construct(){
		parent::__construct();
		$this->load->model("Menu_model");
		$this->load->library('Alllist');
	}
		
	function index(){		
		$menu_slug= $this->uri->segment(1);	
		$this->Site_model->has_menupermission($menu_slug);			    
		$data['hasCreateOption']= $this->Site_model->hasOptionPermission($menu_slug,"Create");	    
		$data['hasViewOption']  = $this->Site_model->hasOptionPermission($menu_slug,"View");	
		$data['cquery'] =$this->alllist->GetCompanyList();
		$data['bquery'] =$this->alllist->GetBranchList();		
		$data['mquery'] =$this->alllist->GetModuleList();
		$data['muquery']=$this->alllist->GetMenuList();
		$this->load->view('menu',$data);
	}
	function AddRecord(){ // Function Standard PascalCase
		$this->Site_model->has_menupermission($this->uri->segment(1));
		$this->Menu_model->InsertRecord();
		$this->Menu_model->GetRecordGrid();
		
	}
	function FillRecord(){
		$this->Site_model->has_menupermission($this->uri->segment(1));
		$row = $this->Menu_model->FillRecord();
		echo $row->menu_id."##&##".$row->company_id."##&##".$row->module_id."##&##".$row->menu_slug."##&##".$row->menu_name."##&##".$row->order_no."##&##".$row->menu_status;
	}
	function EditRecord(){
		$this->Site_model->has_menupermission($this->uri->segment(1));
		$this->Menu_model->EditRecord();
	}
	function DelRecord(){
		$this->Site_model->has_menupermission($this->uri->segment(1));
		$this->Menu_model->DelRecord();
	}
	function GetRecord(){
		$this->Site_model->has_menupermission($this->uri->segment(1));
		$this->Menu_model->GetRecordGrid();
	}
}
