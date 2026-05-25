<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Outstationduty extends CI_Controller {
	function __construct(){
		parent::__construct();
		$this->load->model("Outstationduty_model");
		$this->load->library('Alllist');
	}
		
	function index(){
		$menu_slug= $this->uri->segment(1);	
		$this->Site_model->has_menupermission($menu_slug);			    
		$data['hasCreateOption']= $this->Site_model->hasOptionPermission($menu_slug,"Create");	    
		$data['hasViewOption']  = $this->Site_model->hasOptionPermission($menu_slug,"View");
		$data['iquery']=$this->alllist->GetCompanyList();
		$data['bquery']=$this->alllist->GetBranchList();
		$data['squery']=$this->alllist->GetSessionList();
        $data['dquery']=$this->alllist->GetDepartmentList();
		$data['shquery']=$this->alllist->GetShiftList();
		$data['emquery']=$this->alllist->GetEmployeeList();
		$this->load->view('outstation_duty',$data);
	}
	function AddRecord(){
		$this->Site_model->has_menupermission($this->uri->segment(1));
		$this->Outstationduty_model->InsertRecord();
		$this->Outstationduty_model->GetRecordGrid();
		$msg="Successfully save record!!!";
		$data['msg']=$msg;
	}
	function FillRecord(){
		$this->Site_model->has_menupermission($this->uri->segment(1));
		$row = $this->Outstationduty_model->FillRecord();
		$IntimeArr = explode(":",$row->in_time); $OuttimeArr = explode(":",$row->out_time);
		echo $row->od_id."##&##".$row->company_id."##&##".$row->branch_id."##&##".$row->department_id."##&##".$row->section_id."##&##".$row->session_id."##&##".$row->shift_id."##&##".$row->employee_id."##&##".$this->Outstationduty_model->formatDateDMY($row->from_date)."##&##".$this->Outstationduty_model->formatDateDMY($row->to_date)."##&##".$IntimeArr[0].':'.$IntimeArr[1]."##&##".$OuttimeArr[0].':'.$OuttimeArr[1]."##&##".$row->remarks;
	}
	function EditRecord(){
		$this->Site_model->has_menupermission($this->uri->segment(1));
		$this->Outstationduty_model->EditRecord();
	}
	function DelRecord(){
		$this->Site_model->has_menupermission($this->uri->segment(1));
		$this->Outstationduty_model->DelRecord();
	}
	function GetRecord(){
		$this->Site_model->has_menupermission($this->uri->segment(1));
		$this->Outstationduty_model->GetRecordGrid();
	}

	function ViewOutdoor(){
		$menu_slug= $this->uri->segment(1);	
		$this->Site_model->has_menupermission($menu_slug);
		$data['hasPrintOption'] = $this->Site_model->hasOptionPermission($menu_slug,"Print");
		$od_id	= $this->uri->segment(3);	
		$data['od_id']= $od_id;
		$this->load->view('print_outdoor',$data);
	}
	function GetOutdoorDetails(){
		$menu_slug= $this->uri->segment(1);	
		$this->Site_model->has_menupermission($menu_slug);
		$data['hasPrintOption'] = $this->Site_model->hasOptionPermission($menu_slug,"Print");	    
		
		$this->load->model("Report_model");		
		$this->Report_model->GetOutdoorDetails();
	}
}
