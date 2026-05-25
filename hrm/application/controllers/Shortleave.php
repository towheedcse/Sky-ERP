<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Shortleave extends CI_Controller {
	function __construct(){
		parent::__construct();
		$this->load->model("Shortleave_model");
		$this->load->library('Alllist');
	}
		
	function index(){
		$menu_slug= $this->uri->segment(1);	
		$this->Site_model->has_menupermission($menu_slug);			    
		$data['hasCreateOption'] = $this->Site_model->hasOptionPermission($menu_slug,"Create");	    
		$data['hasViewOption']   = $this->Site_model->hasOptionPermission($menu_slug,"View");	    
		$data['hasApproveOption']= $this->Site_model->hasOptionPermission($menu_slug,"Approved");
		$data['emquery']         = $this->alllist->GetEmployeeList();
		$this->load->view('short_leave',$data);
	}
	function AddRecord(){
		$this->Site_model->has_menupermission($this->uri->segment(1));
		$this->Shortleave_model->InsertRecord();
		$this->Shortleave_model->GetRecordGrid();
		$msg="Successfully save record!!!";
		$data['msg']=$msg;
	}
	function FillRecord(){
		$this->Site_model->has_menupermission($this->uri->segment(1));
		$row = $this->Shortleave_model->FillRecord();
		$TimeFrmArr = explode(":",$row->time_from); $TimeToArr = explode(":",$row->time_to);
		echo $row->sl_id."##&##".$row->employee_id."##&##".$row->leave_type."##&##".$this->Shortleave_model->formatDateDMY($row->application_date)."##&##".$this->Shortleave_model->formatDateDMY($row->leave_date)."##&##".$row->time_from."##&##".$row->time_to."##&##".$row->purpose_of_leave."##&##".$TimeFrmArr[0].':'.$TimeFrmArr[1]."##&##".$TimeToArr[0].':'.$TimeToArr[1]."##&##".$row->status;
	}
	function EditRecord(){
		$this->Site_model->has_menupermission($this->uri->segment(1));
		$this->Shortleave_model->EditRecord();
	}
	function DelRecord(){
		$this->Site_model->has_menupermission($this->uri->segment(1));
		$this->Shortleave_model->DelRecord();
	}
	function GetRecord(){
		$this->Site_model->has_menupermission($this->uri->segment(1));
		$this->Shortleave_model->GetRecordGrid();
	}

	function ViewShortLeave(){
		$menu_slug= $this->uri->segment(1);	
		$this->Site_model->has_menupermission($menu_slug);
		$data['hasPrintOption'] = $this->Site_model->hasOptionPermission($menu_slug,"Print");
		$od_id	= $this->uri->segment(3);	
		$data['od_id']= $od_id;
		$this->load->view('print_short_leave',$data);
	}
	function GetShortLeaveDetails(){
		$menu_slug= $this->uri->segment(1);	
		$this->Site_model->has_menupermission($menu_slug);
		$data['hasPrintOption'] = $this->Site_model->hasOptionPermission($menu_slug,"Print");	    
		
		$this->load->model("Report_model");		
		$this->Report_model->GetShortLeaveDetails();
	}
}
