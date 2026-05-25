<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Project extends CI_Controller {	
	function __construct(){
		parent::__construct();
		$this->load->model("Project_model");		
		$this->load->library('alllist');
	}

	function index(){
	    $menu_slug= $this->uri->segment(1);	
	    $this->Site_model->has_menupermission($menu_slug);			    
	    $data['hasCreateOption']= $this->Site_model->hasOptionPermission($menu_slug,"Create");	    
	    $data['hasViewOption']  = $this->Site_model->hasOptionPermission($menu_slug,"View");
            $data['cquery']=$this->alllist->GetCompanyList();
            $data['bquery']=$this->alllist->GetBranchList();
            $this->load->view('project',$data);
	}
	function AddRecord(){// Function Standard PascalCase
		$this->Site_model->has_menupermission($this->uri->segment(1));
		$this->Project_model->InsertRecord();
		$this->Project_model->GetRecordGrid();
	}
	function GetRecord(){
		$this->Site_model->has_menupermission($this->uri->segment(1));
		$this->Project_model->GetRecordGrid();
	}
	function DelRecord(){
		$this->Site_model->has_menupermission($this->uri->segment(1));
		$this->Project_model->DelRecord();
	}
	function FillRecord(){
		$this->Site_model->has_menupermission($this->uri->segment(1));
		$row = $this->Project_model->FillRecord();
		echo $row->project_id."##&##".$row->company_id."##&##".$row->branch_id."##&##".$row->project_name."##&##".$row->project_description."##&##".$row->project_price."##&##".$row->project_budget;
	}
	function EditRecord(){
		$this->Site_model->has_menupermission($this->uri->segment(1));
		$this->Project_model->EditRecord();
	}
}
