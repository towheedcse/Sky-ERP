<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Area extends CI_Controller {
	function __construct(){
		parent::__construct();
		$this->load->model("Area_model");
		$this->load->library('Alllist');
	}
		
	function index(){
		$menu_slug= $this->uri->segment(1);	
		$this->Site_model->has_menupermission($menu_slug);			    
		$data['hasCreateOption']= $this->Site_model->hasOptionPermission($menu_slug,"Create");	    
		$data['hasViewOption']  = $this->Site_model->hasOptionPermission($menu_slug,"View");	
		$data['cquery']=$this->alllist->GetCompanyList();
        	$data['dquery']=$this->alllist->GetDivisionList();
		$this->load->view('area',$data);
	}
	function AddRecord(){ // Function Standard PascalCase
		$this->Site_model->has_menupermission($this->uri->segment(1));
		$this->Area_model->InsertRecord();
		$this->Area_model->GetRecordGrid();		
	}
	function FillRecord(){
		$this->Site_model->has_menupermission($this->uri->segment(1));
		$row = $this->Area_model->FillRecord();
		echo $row->area_id."##&##".$row->division_id."##&##".$row->company_id."##&##".$row->area_name."##&##".$row->area_status;
	}
	function EditRecord(){
		$this->Site_model->has_menupermission($this->uri->segment(1));
		$this->Area_model->EditRecord();
	}
	function DelRecord(){
		$this->Site_model->has_menupermission($this->uri->segment(1));
		$this->Area_model->DelRecord();
	}
	function GetRecord(){
		$this->Site_model->has_menupermission($this->uri->segment(1));
		$this->Area_model->GetRecordGrid();
	}
}
