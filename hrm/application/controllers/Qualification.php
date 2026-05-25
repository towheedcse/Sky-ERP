<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Qualification extends CI_Controller {	
	function __construct(){
		parent::__construct();
		$this->load->model("Qualification_model");
		$this->load->library('Alllist');
	}
		
	function index(){
		$menu_slug= $this->uri->segment(1);	
		$this->Site_model->has_menupermission($menu_slug);			    
	    $data['hasCreateOption']= $this->Site_model->hasOptionPermission($menu_slug,"Create");	    
	    $data['hasViewOption']  = $this->Site_model->hasOptionPermission($menu_slug,"View");
		$data['cquery']=$this->alllist->GetCompanyList();
		$this->load->view('qualification',$data);	
	}
	function AddRecord(){ // Function Standard PascalCase
		$this->Site_model->has_menupermission($this->uri->segment(1));
		$this->Qualification_model->InsertRecord();
		$this->Qualification_model->GetRecordGrid();
		
	}
	function FillRecord(){
		$this->Site_model->has_menupermission($this->uri->segment(1));
		$row = $this->Qualification_model->FillRecord();
		echo $row->qualification_id."##&##".$row->institute_id."##&##".$row->qualification_name."##&##".$row->status;
	}
	function EditRecord(){
		$this->Site_model->has_menupermission($this->uri->segment(1));
		$this->Qualification_model->EditRecord();
	}
	function DelRecord(){
		$this->Site_model->has_menupermission($this->uri->segment(1));
		$this->Qualification_model->DelRecord();
	}
	function GetRecord(){
		$this->Site_model->has_menupermission($this->uri->segment(1));
		$this->Qualification_model->GetRecordGrid();
	}
}
