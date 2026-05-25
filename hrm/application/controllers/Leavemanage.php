<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Leavemanage extends CI_Controller {
	function __construct(){
		parent::__construct();
		$this->load->model("Leavemanage_model");
		$this->load->library('Alllist');
	}
		
	function index(){
		$menu_slug= $this->uri->segment(1);	
		$this->Site_model->has_menupermission($menu_slug);			    
		$data['hasCreateOption']= $this->Site_model->hasOptionPermission($menu_slug,"Create");	    
		$data['hasViewOption']  = $this->Site_model->hasOptionPermission($menu_slug,"View");
		$data['equery']         = $this->alllist->GetEmployeeList();
		$data['lquery']         = $this->alllist->GetLeaveTypeList();
		$data['employee_id']    = 0;
		$data['section_chief']  = "";
		$data['dept_head']      = "";
		$this->load->view('leave_management',$data);
	}
		
	function AddNew(){
		$menu_slug= $this->uri->segment(1);	
		$this->Site_model->has_menupermission($menu_slug);			    
		$data['hasCreateOption']= $this->Site_model->hasOptionPermission($menu_slug,"Create");	    
		$data['hasViewOption']  = $this->Site_model->hasOptionPermission($menu_slug,"View");
		$data['equery']         = $this->alllist->GetEmployeeList();
		$data['lquery']         = $this->alllist->GetLeaveTypeList();
		$employee_id	        = $this->uri->segment(3);
		$row = $this->Leavemanage_model->getEmployInfo($employee_id);
		$data['dept_head']      = $this->Leavemanage_model->getDepartmentHead($row->department_id,$row->company_id);
		$data['section_chief']  = $this->Leavemanage_model->getSectionHead($row->section_id,$row->department_id,$row->company_id);	
		$data['employee_id']    = $employee_id;
		$this->load->view('leave_management',$data);
	}
	function AddRecord(){
		$this->Site_model->has_menupermission($this->uri->segment(1));
		$this->Leavemanage_model->InsertRecord();
		$this->Leavemanage_model->GetRecordGrid();
	}
	function FillRecord(){
		$this->Site_model->has_menupermission($this->uri->segment(1));
		$row = $this->Leavemanage_model->FillRecord();
		echo $row->leave_id."##&##".$row->employee_id."##&##".$row->leave_nature."##&##".$row->leave_type."##&##".$this->Leavemanage_model->formatDateDMY($row->application_date)."##&##".$this->Leavemanage_model->formatDateDMY($row->leave_from)."##&##".$this->Leavemanage_model->formatDateDMY($row->leave_to)."##&##".$row->leave_purpose."##&##".$row->recommended_by."##&##".$row->section_chiaf."##&##".$row->dept_head."##&##".$row->leave_address."##&##".$row->leave_mobile;
	}
	function EditRecord(){
		$this->Site_model->has_menupermission($this->uri->segment(1));
		$this->Leavemanage_model->EditRecord();
	}
	function DelRecord(){
		$this->Site_model->has_menupermission($this->uri->segment(1));
		$this->Leavemanage_model->DelRecord();
	}
	function GetRecord(){
		$this->Site_model->has_menupermission($this->uri->segment(1));
		$this->Leavemanage_model->GetRecordGrid();
	}
    function getAjaxEmployeeInfo(){
        $employee_id=$this->input->post('id');
		$row = $this->Leavemanage_model->getEmployInfo($employee_id);
		$department_head= $this->Leavemanage_model->getDepartmentHead($row->department_id,$row->company_id);
		$section_head   = $this->Leavemanage_model->getSectionHead($row->section_id,$row->department_id,$row->company_id); 
		echo $section_head."##&##".$department_head."##&##".$row->address;
    }
	function ViewLeaveForm(){
		$menu_slug= $this->uri->segment(1);	
		$this->Site_model->has_menupermission($menu_slug);
		$data['hasPrintOption'] = $this->Site_model->hasOptionPermission($menu_slug,"Print");
		$leave_id	= $this->uri->segment(3);	
		$data['leave_id']= $leave_id;
		$this->load->view('print_leave_form',$data);
	}
	function GetLeaveDetails(){
		$menu_slug= $this->uri->segment(1);	
		$this->Site_model->has_menupermission($menu_slug);
		$data['hasPrintOption'] = $this->Site_model->hasOptionPermission($menu_slug,"Print");	    
		
		$this->load->model("Report_model");		
		$this->Report_model->GetLeaveDetails();
	}
}
