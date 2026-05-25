<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Category extends CI_Controller {	
	function __construct(){
		parent::__construct();
		$this->load->model("Category_model");
	}
		
	function index(){
		$this->Site->is_loggedin_superAdmin();
		$this->load->view('category');	
	}
	function AddRecord(){// Function Standard PascalCase
		$this->Site->is_loggedin_superAdmin();
		$this->Category_model->InsertRecord();
		$this->Category_model->GetRecordGrid();
	}
	function FillRecord(){
		$this->Site->is_loggedin_superAdmin();
		$row = $this->Category_model->FillRecord();
		echo $row->cat_id."##&##".$row->category_name;
	}
	function EditRecord(){
		$this->Site->is_loggedin_superAdmin();
		$this->Category_model->EditRecord();
	}
	function DelRecord(){
		$this->Site->is_loggedin_superAdmin();
		$this->Category_model->DelRecord();	
	}
	function GetRecord(){
		$this->Site->is_loggedin_superAdmin();
		$this->Category_model->GetRecordGrid();
	}
}
