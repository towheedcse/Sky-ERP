<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Loaddata extends CI_Controller {
	function __construct(){
		parent::__construct();
		$this->load->model("Attendance_model");
		$this->load->library('Alllist');
	}
		
	function index(){
		$menu_slug= $this->uri->segment(1);	
		$this->Site_model->has_menupermission($menu_slug);			    
	    $data['hasLoadOption']  = $this->Site_model->hasOptionPermission($menu_slug,"Upload");
		$data['iquery']=$this->alllist->GetCompanyList();
		$data['bquery']=$this->alllist->GetBranchList();
		$data['squery']=$this->alllist->GetSessionList();
        $data['dquery']=$this->alllist->GetDepartmentList();
		$data['shquery']=$this->alllist->GetShiftList(); 
		$this->load->view('loaddata',$data);
	}
	function AddRecord(){ // Function Standard PascalCase
		$this->Site_model->has_menupermission($this->uri->segment(1));
		$file_name = date("Y-m-d"); // $modified_time = date("Y-m-d H:i:s");
		$this->Attendance_model->UploadDataFile($file_name);
		$this->Attendance_model->ProcessData($file_name);
		$msg="Data Upload & Process Successfully Completed !!!";
		$data['msg']=$msg;		
		redirect(SERVER.'/Loaddata',$data);
	}
}
