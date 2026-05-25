<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Shift extends CI_Controller {	
	function __construct(){
		parent::__construct();
		$this->load->model("Shift_model");
		$this->load->library('Alllist');
	}
		
	function index(){
		$menu_slug= $this->uri->segment(1);	
		$this->Site_model->has_menupermission($menu_slug);			    
	    	$data['hasCreateOption']= $this->Site_model->hasOptionPermission($menu_slug,"Create");	    
	    	$data['hasViewOption']  = $this->Site_model->hasOptionPermission($menu_slug,"View");
		$data['cquery']=$this->alllist->GetCompanyList();
		$this->load->view('shift',$data);	
	}
	function AddRecord(){ // Function Standard PascalCase
		$this->Site_model->has_menupermission($this->uri->segment(1));
		$this->Shift_model->InsertRecord();
		$this->Shift_model->GetRecordGrid();
		
	}
	function FillRecord(){
		$this->Site_model->has_menupermission($this->uri->segment(1));
		$row = $this->Shift_model->FillRecord();
		echo $row->shift_id."##&##".$row->institute_id."##&##".$row->shift_name."##&##".$row->shift_start."##&##".$row->shift_end."##&##".$row->shift_capacity."##&##".$row->status;
	}
	function EditRecord(){
		$this->Site_model->has_menupermission($this->uri->segment(1));
		$this->Shift_model->EditRecord();
	}
	function DelRecord(){
		$this->Site_model->has_menupermission($this->uri->segment(1));
		$this->Shift_model->DelRecord();
	}
	function GetRecord(){
		$this->Site_model->has_menupermission($this->uri->segment(1));
		$this->Shift_model->GetRecordGrid();
		
	}
}
