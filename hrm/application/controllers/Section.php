<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Section extends CI_Controller {
	function __construct(){
		parent::__construct();
		$this->load->model("Section_model");
		$this->load->library('Alllist');
	}
		
	function index(){
		$menu_slug= $this->uri->segment(1);	
		$this->Site_model->has_menupermission($menu_slug);			    
		$data['hasCreateOption']= $this->Site_model->hasOptionPermission($menu_slug,"Create");	    
		$data['hasViewOption']  = $this->Site_model->hasOptionPermission($menu_slug,"View");	
		$data['cquery']=$this->alllist->GetCompanyList();
        $data['dquery']=$this->alllist->GetDepartmentList();
		$this->load->view('section',$data);
	}
	function AddRecord(){ // Function Standard PascalCase
		$this->Site_model->has_menupermission($this->uri->segment(1));
		$this->Section_model->InsertRecord();
		$this->Section_model->GetRecordGrid();		
	}
	function FillRecord(){
		$this->Site_model->has_menupermission($this->uri->segment(1));
		$row = $this->Section_model->FillRecord();
		echo $row->section_id."##&##".$row->department_id."##&##".$row->company_id."##&##".$row->section_name."##&##".$row->section_head."##&##".$row->section_status;
	}
	function EditRecord(){
		$this->Site_model->has_menupermission($this->uri->segment(1));
		$this->Section_model->EditRecord();
	}
	function DelRecord(){
		$this->Site_model->has_menupermission($this->uri->segment(1));
		$this->Section_model->DelRecord();
	}
	function GetRecord(){
		$this->Site_model->has_menupermission($this->uri->segment(1));
		$this->Section_model->GetRecordGrid();
	}
}
