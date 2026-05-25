<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Dashboard extends CI_Controller {
	
	function __construct()
	{
		parent::__construct();
	}	

	public function index()
	{	$this->Authenticate_model->LoadCompanyInfo();		
		$this->load->view('login');
	}
	public function Userhome()
	{
		$this->lang->load("back",$this->session->userdata('language'));
		$this->load->model("Activities_model");
		
		$data['PendingCard'] =$this->Activities_model->GetPendingCardNum();
		$data['ReceivedCard']=$this->Activities_model->GetReceivedCardNum();
		$this->load->view('user_home',$data);
	}

	public function Mycard()
	{	//$this->Authenticate_model->LoadCompanyInfo();		
		$this->load->view('mycard');
	}
	function loadSubMenu(){
		$id	=$this->input->post('id');	
		echo $this->Dashboard_model->loadSubMenu($id);		
	}
	function loadProduct(){
		$sid	=$this->input->post('id');	
		echo $this->Dashboard_model->getSubMenuChields($sid);		
	}
	function Login(){
		$this->Authenticate_model->LoadCompanyInfo();
		$data['title']="Sign in";
		$this->load->view('login',$data);
	}
	function LoginProcess(){
		$data['title']="Sign in";
		$msg="Invalid user name and/or Password!";
		$data['msg']=$msg;
		$result=$this->Authenticate_model->Login();
		if($result){ 			
			if($this->session->userdata('user_role') >0){
			redirect(SERVER.'/dashboard/Userhome',$data);
			}else{				
			$this->load->view('login',$data);
			}
		}else{
			$this->load->view('login',$data);
		}
	}
	function Logout(){
		//$this->Authenticate_model->saveOutAttendance($this->session->userdata('employee_id'));
		$this->session->sess_destroy();
        redirect(SERVER.'/dashboard/login');
	}
	
}
