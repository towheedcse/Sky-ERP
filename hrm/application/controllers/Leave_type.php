<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Leave_type extends CI_Controller {	
	function __construct(){
		parent::__construct();
		$this->load->model("Leavetype_model");
		$this->load->library('Alllist');
	}
		
	function index(){
		$menu_slug= $this->uri->segment(1);	
		$this->Site_model->has_menupermission($menu_slug);			    
		$data['hasCreateOption']= $this->Site_model->hasOptionPermission($menu_slug,"Create");	    
		$data['hasViewOption']  = $this->Site_model->hasOptionPermission($menu_slug,"View");	
		$data['cquery']=$this->alllist->GetCompanyList();		
		$this->load->view('leave_type',$data);	
	}
	function AddRecord(){ // Function Standard PascalCase
		$this->Site_model->has_menupermission($this->uri->segment(1));
		$this->Leavetype_model->InsertRecord();
		$this->Leavetype_model->GetRecordGrid();
		
	}
	function FillRecord(){
		$this->Site_model->has_menupermission($this->uri->segment(1));
		$row = $this->Leavetype_model->FillRecord();
		echo $row->category_id."##&##".$row->company_id."##&##".$row->leave_type."##&##".$row->total_leave."##&##".$row->status;
	}
	function EditRecord(){
		$this->Site_model->has_menupermission($this->uri->segment(1));
		$this->Leavetype_model->EditRecord();
	}
	function DelRecord(){
		$this->Site_model->has_menupermission($this->uri->segment(1));
		$this->Leavetype_model->DelRecord();
	}
	function GetRecord(){
		$this->Site_model->has_menupermission($this->uri->segment(1));
		$this->Leavetype_model->GetRecordGrid();
	}
}
