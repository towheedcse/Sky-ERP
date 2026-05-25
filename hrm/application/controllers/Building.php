<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Building extends CI_Controller {	
	function __construct(){
		parent::__construct();
		$this->load->model("Building_model");		
		$this->load->library('alllist');
	}

	function index(){	    
		$menu_slug= $this->uri->segment(1);	
		$this->Site_model->has_menupermission($menu_slug);			    
		$data['hasCreateOption']= $this->Site_model->hasOptionPermission($menu_slug,"Create");	    
		$data['hasViewOption']  = $this->Site_model->hasOptionPermission($menu_slug,"View");
		$data['cquery']=$this->alllist->GetCompanyList();
		$data['bquery']=$this->alllist->GetBranchList();
		$this->load->view('building',$data);
	}
	function AddRecord(){// Function Standard PascalCase
		$this->Site_model->has_menupermission($this->uri->segment(1));
		$this->Building_model->InsertRecord();
		$this->Building_model->GetRecordGrid();
	}
	function GetRecord(){
		$this->Site_model->has_menupermission($this->uri->segment(1));
		$this->Building_model->GetRecordGrid();
	}
	function DelRecord(){
		$this->Site_model->has_menupermission($this->uri->segment(1));
		$this->Building_model->DelRecord();
	}
	function FillRecord(){
		$this->Site_model->has_menupermission($this->uri->segment(1));
		$row = $this->Building_model->FillRecord();

		$BranchList = $this->alllist->GetAjaxBranchList($row->company_id,$row->branch_id);

		echo $row->building_id."##&##".$row->company_id."##&##".$row->branch_id."##&##".$row->building_name."##&##".$row->building_description."##&##".$row->total_floor."@@##@@".$BranchList;
	}
	function EditRecord(){
		$this->Site_model->has_menupermission($this->uri->segment(1));
		$this->Building_model->EditRecord();
	}	
	function GetAjaxBranchList(){		
		$company_id 	=$this->input->post('company-id');
		$branch_id  	=$this->input->post('branch-id');
		if(empty($company_id)){$company_id=0;} if(empty($branch_id)){$branch_id=0;}
		echo $this->alllist->GetAjaxBranchList($company_id,$branch_id);
	}
}
