<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class User_role extends CI_Controller {
	function __construct(){
		parent::__construct();
		$this->load->model("User_role_model");
	}
	function index(){
		$this->Site_model->is_loggedin_superAdmin();
		$this->load->view('user_role');
	}
	function AddRecord(){ // Function Standard PascalCase
		$this->Site_model->is_loggedin_superAdmin();
		$this->User_role_model->InsertRecord();
		$this->User_role_model->GetRecordGrid();
	}
	function FillRecord(){
		$this->Site_model->is_loggedin_superAdmin();
		$row = $this->User_role_model->FillRecord();
		echo $row->role_id."##&##".$row->role_name."##&##".$row->role_description."##&##".$row->role_status;
	}
	function EditRecord(){
		$this->Site_model->is_loggedin_superAdmin();
		$this->User_role_model->EditRecord();
	}
	function DelRecord(){
		$this->Site_model->is_loggedin_superAdmin();
		$this->User_role_model->DelRecord();
	}
	function GetRecord(){
		$this->Site_model->is_loggedin_superAdmin();
		$this->User_role_model->GetRecordGrid();
	}
}
