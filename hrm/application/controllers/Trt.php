<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Trt extends CI_Controller {
	function __construct(){
		parent::__construct();
		$this->load->model("Trt_model");
		$this->load->library('Alllist');
	}
		
	function index(){
		$menu_slug= $this->uri->segment(1);	
		$this->Site_model->has_menupermission($menu_slug);			    
		$data['hasCreateOption']= $this->Site_model->hasOptionPermission($menu_slug,"Create");	    
		$data['hasViewOption']  = $this->Site_model->hasOptionPermission($menu_slug,"View");		
		$data['cquery']=$this->alllist->GetCompanyList();
		$data['dquery']=$this->alllist->GetDivisionList();
		$data['aquery']=$this->alllist->GetAreaList();
		$this->load->view('trt',$data);
	}
	function AddRecord(){ // Function Standard PascalCase
		$this->Site_model->has_menupermission($this->uri->segment(1));
		$this->Trt_model->InsertRecord();
		$this->Trt_model->GetRecordGrid();
		
	}
	function FillRecord(){
		$this->Site_model->has_menupermission($this->uri->segment(1));
		$row = $this->Trt_model->FillRecord();
		echo $row->trt_id."##&##".$row->division_id."##&##".$row->area_id."##&##".$row->company_id."##&##".$row->trt_name."##&##".$row->trt_status;
	}
	function EditRecord(){
		$this->Site_model->has_menupermission($this->uri->segment(1));
		$this->Trt_model->EditRecord();
	}
	function DelRecord(){
		$this->Site_model->has_menupermission($this->uri->segment(1));
		$this->Trt_model->DelRecord();
	}
	function GetRecord(){
		$this->Site_model->has_menupermission($this->uri->segment(1));
		$this->Trt_model->GetRecordGrid();
	}
}
