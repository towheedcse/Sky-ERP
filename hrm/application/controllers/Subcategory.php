<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Subcategory extends CI_Controller {	
	function __construct(){
		parent::__construct();
		$this->load->model("Subcategory_model");
	}
		
	function index(){
		$this->Site->is_loggedin_superAdmin();		
		$data['cquery']=$this->Subcategory_model->GetCategoryList();
		$this->load->view('subcategory',$data);	
	}
	function AddSubCategory(){// Function Standard PascalCase
		$this->Site->is_loggedin_superAdmin();
		$this->Subcategory_model->InsertSubCategory();
		$this->Subcategory_model->GetSubCategoryGrid();
		//$this->load->view('category');	
	}
	function GetSubCategory(){
		$this->Site->is_loggedin_superAdmin();
		$this->Subcategory_model->GetSubCategoryGrid();
		//$this->load->view('category');	
	}
	function DelSubCategory(){
		$this->Site->is_loggedin_superAdmin();
		$this->Subcategory_model->DelSubCategory();
		//$this->load->view('category');	
	}
	function FillSubCategory(){
		$this->Site->is_loggedin_superAdmin();
		$row = $this->Subcategory_model->FillSubCategory();
		echo $row->subid."##&##".$row->cid."##&##".$row->name;
	}
	function EditSubCategory(){
		$this->Site->is_loggedin_superAdmin();
		$this->Subcategory_model->EditSubCategory();
	}
		
	
//End Class	
}
