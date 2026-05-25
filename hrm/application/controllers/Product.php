<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Product extends CI_Controller {	
	function __construct(){
		parent::__construct();
		$this->load->model("Product_model");
		$this->load->library('Alllist');
	}
		
	function index(){
		$menu_slug= $this->uri->segment(1);	
		$this->Site_model->has_menupermission($menu_slug);			    
		$data['hasCreateOption']= $this->Site_model->hasOptionPermission($menu_slug,"Create");   
		$data['hasViewOption']  = $this->Site_model->hasOptionPermission($menu_slug,"View");
		$data['cquery']=$this->alllist->GetCompanyList();
		$data['bquery']=$this->alllist->GetBranchList();
		$this->load->view('product',$data);	 
	}
	function AddRecord(){ // Function Standard PascalCase
		$this->Site_model->has_menupermission($this->uri->segment(1));
		$this->Product_model->InsertRecord();
		$msg="Successfully save record!!!";
		$data['msg']=$msg;
		redirect(SERVER.'/product',$data);
	}
	function FillRecord(){
		$this->Site_model->has_menupermission($this->uri->segment(1));
		$prow = $this->Product_model->FillProduct();
		$arow = $this->Product_model->FillAccountHead();
		echo $prow->product_id."##&##".$prow->company_id."##&##".$prow->branch_id."##&##".$arow->head_type."##&##".$arow->account_name."##&##".$prow->product_code."##&##".$prow->product_details."##&##".$arow->count_unit."##&##".$prow->purchase_price."##&##".$prow->sales_price."##&##".$prow->reorder_level."##&##".$prow->status;
	}
	function EditRecord(){
		$this->Site_model->has_menupermission($this->uri->segment(1));
		$this->Product_model->UpdateRecord();
	}
	function DelRecord(){
		$this->Site_model->has_menupermission($this->uri->segment(1));
		$this->Product_model->DelRecord();
	}	
	function GetRecord(){
		$this->Site_model->has_menupermission($this->uri->segment(1));
		$this->Product_model->GetRecordGrid();
	}

}
