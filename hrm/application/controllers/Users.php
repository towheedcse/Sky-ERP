<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Users extends CI_Controller {	
	function __construct(){
		parent::__construct();
		$this->load->model("Users_model");
        	$this->load->library('Alllist');
	}
		
	function index(){		
		$menu_slug= $this->uri->segment(1);	
		$this->Site_model->has_menupermission($menu_slug);			    
		$data['hasCreateOption']= $this->Site_model->hasOptionPermission($menu_slug,"Create");	    
		$data['hasViewOption']  = $this->Site_model->hasOptionPermission($menu_slug,"View");
		$head_type =array(1,2,10,11);
		$data['empquery']=$this->alllist->GetAccountList($head_type);
		$data['cquery']=$this->alllist->GetCompanyList();
		$data['bquery']=$this->alllist->GetBranchList();
		$data['rquery']=$this->alllist->GetUserGroupList();
		//$data['mquery']=$this->alllist->GetModuleList();
		$this->load->view('users',$data);
	}
	function AddRecord(){// Function Standard PascalCase
		$this->Site_model->has_menupermission($this->uri->segment(1));
		$this->Users_model->InsertRecord();
		$this->Users_model->GetRecordGrid();
	}
	function change_password(){	
		$this->Site_model->has_menupermission($this->uri->segment(1));
		$row 			= $this->Users_model->GetUserInfo();
		$data['u_id']		= $row->u_id;
		$data['user_name']	= $row->user_name;
		$data['access_key']	= $row->access_key;
		$this->load->view('change_password',$data);
	}
	function ChangePassword(){
		$this->Site_model->has_menupermission($this->uri->segment(1));	
		$row 			= $this->Users_model->GetUserInfo();
		$oldPassword	= $this->input->post('old_password');
		$userPassword	= $this->input->post('user_password');
		if($row->access_key != $oldPassword){
			echo "The old password is not valid";
		}else{
			$this->Users_model->ChangePassword();
		}
		
	}

	function GetRecord(){
		$this->Site_model->has_menupermission($this->uri->segment(1));
		$this->Users_model->GetRecordGrid();
		//$this->load->view('employee');	
	}
	function DelRecord(){
		$this->Site_model->has_menupermission($this->uri->segment(1));
		$this->Users_model->DelRecord();
		//$this->load->view('employee');	
	}
	function chkUserName(){
		$this->Site_model->has_menupermission($this->uri->segment(1));
		$this->Users_model->chkUserName();
	}
	function FillRecord(){
		$this->Site_model->has_menupermission($this->uri->segment(1));
		$row = $this->Users_model->FillRecord();
		echo $row->user_id.'##&##'.$row->ref_id.'##&##'.$row->company_id.'##&##'.$row->branch_id.'##&##'.$row->user_name.'##&##'.$row->access_key.'##&##'.$row->user_role.'##&##'.$row->user_status;
	}
	function EditRecord(){
		$this->Site_model->has_menupermission($this->uri->segment(1));
		$this->Users_model->EditRecord();
	}
//End Class	
}
