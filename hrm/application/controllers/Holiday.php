<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Holiday extends CI_Controller {
	function __construct(){
		parent::__construct();
		$this->load->model("Holiday_model");
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
		$data['vquery']=$this->alllist->GetVersionList();
		$this->load->view('holiday_setup',$data);
	}
	function AddRecord(){
		$this->Site_model->has_menupermission($this->uri->segment(1));
		$this->Holiday_model->InsertRecord();
		//$this->Holiday_model->GetRecordGrid();
		$msg="Successfully save record!!!";
		$data['msg']=$msg;		
		redirect(SERVER.'/holiday',$data);
	}
	function FillRecord(){
		$this->Site_model->has_menupermission($this->uri->segment(1));
		$row = $this->Holiday_model->FillRecord();
		echo $row->holiday_id."##&##".$row->institute_id."##&##".$row->branch_id."##&##".$row->session_id."##&##".$row->version_id."##&##".$row->holiday_name."##&##".$this->Holiday_model->formatDateDMY($row->from_date)."##&##".$this->Holiday_model->formatDateDMY($row->to_date)."##&##".$row->is_holiday."##&##".$row->status;
	}
	function EditRecord(){
		$this->Site_model->has_menupermission($this->uri->segment(1));
		$this->Holiday_model->EditRecord();
	}
	function DelRecord(){
		$this->Site_model->has_menupermission($this->uri->segment(1));
		$this->Holiday_model->DelRecord();
	}
	function GetRecord(){
		$this->Site_model->has_menupermission($this->uri->segment(1));
		$this->Holiday_model->GetRecordGrid();
	}

	function ViewNotice(){
		$menu_slug= $this->uri->segment(1);	
		$this->Site_model->has_menupermission($menu_slug);
		$data['hasPrintOption'] = $this->Site_model->hasOptionPermission($menu_slug,"Print");
		$holiday_id	= $this->uri->segment(3);	
		$data['holiday_id']= $holiday_id;
		$this->load->view('print_notice',$data);
	}
	function GetHolidayInfo(){
		$menu_slug= $this->uri->segment(1);	
		$this->Site_model->has_menupermission($menu_slug);
		$data['hasPrintOption'] = $this->Site_model->hasOptionPermission($menu_slug,"Print");	    
		
		$this->load->model("Report_model");		
		$this->Report_model->GetHolidayInfo();
	}
}
