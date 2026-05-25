<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Sessions extends CI_Controller {	
	function __construct(){
		parent::__construct();
		$this->load->model("Sessions_model");
		$this->load->library('Alllist');
	}
		
	function index(){
		$menu_slug= $this->uri->segment(1);	
		$this->Site_model->has_menupermission($menu_slug);			    
	    $data['hasCreateOption']= $this->Site_model->hasOptionPermission($menu_slug,"Create");	    
	    $data['hasViewOption']  = $this->Site_model->hasOptionPermission($menu_slug,"View");
		$data['cquery']=$this->alllist->GetCompanyList();
		$this->load->view('sessions',$data);	
	}
	function AddRecord(){ // Function Standard PascalCase
		$this->Site_model->has_menupermission($this->uri->segment(1));
		$this->Sessions_model->InsertRecord();
		$this->Sessions_model->GetRecordGrid();
	}
	function FillRecord(){
		$this->Site_model->has_menupermission($this->uri->segment(1));
		$row = $this->Sessions_model->FillRecord();
		echo $row->sessions_id."##&##".$row->institute_id."##&##".$row->session_name."##&##".$this->Sessions_model->formatDateDMY($row->session_start)."##&##".$this->Sessions_model->formatDateDMY($row->session_end)."##&##".$row->session_status;
	}
	function EditRecord(){
		$this->Site_model->has_menupermission($this->uri->segment(1));
		$this->Sessions_model->EditRecord();
	}
	function DelRecord(){
		$this->Site_model->has_menupermission($this->uri->segment(1));
		$this->Sessions_model->DelRecord();
	}
	function GetRecord(){
		$this->Site_model->has_menupermission($this->uri->segment(1));
		$this->Sessions_model->GetRecordGrid();
	}
}
