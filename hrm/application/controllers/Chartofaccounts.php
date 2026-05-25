<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class ChartOfAccounts extends CI_Controller {
	function __construct(){
		parent::__construct();
		$this->load->model("Subsidiaryledgertwo_model");
		$this->load->model("Accounthead_model");		
		$this->load->library('alllist');	
	}

	function index(){						
		$menu_slug= $this->uri->segment(1);	
		$this->Site_model->has_menupermission($menu_slug);			    
		$data['hasCreateOption']= $this->Site_model->hasOptionPermission($menu_slug,"Create");	    
		$data['hasViewOption']  = $this->Site_model->hasOptionPermission($menu_slug,"View");
		
		$data['gquery'] =$this->alllist->GetGroupLedgerList();
        	$data['cquery'] =$this->alllist->GetCompanyList();
		$this->load->view('chartofaccounts',$data);
	}
	function AddRecord(){// Function Standard PascalCase
		$this->Site_model->has_menupermission($this->uri->segment(1));

		$this->Accounthead_model->InsertRecord();
		$this->Accounthead_model->GetRecordGrid();
	}
	function GetRecords(){
		$this->Site_model->has_menupermission($this->uri->segment(1));

		$this->Accounthead_model->GetRecordGrid();
	}
	function DeleteRecord(){
		$this->Site_model->has_menupermission($this->uri->segment(1));
		$this->Accounthead_model->DeleteRecord();
	}
	function FillRecord(){
	  $this->Site_model->has_menupermission($this->uri->segment(1));	
	  $row     = $this->Accounthead_model->FillRecord();
	  if(empty($row->subsidiary_level1)){$sub_head1=0;}else{$sub_head1=$row->subsidiary_level1;}
	  $SubHead1 = $this->alllist->GetAjaxSubAccountList($row->company_id,$row->group_id,$sub_head1);
	  if(empty($row->subsidiary_level2)){$sub_head2=0;}else{$sub_head2=$row->subsidiary_level2;}
	  $SubHead2 = $this->alllist->GetAjaxChildAccountList($row->company_id,$row->group_id,$sub_head1,$sub_head2);
	  if(empty($row->subsidiary_level3)){$sub_head3=0;}else{$sub_head3=$row->subsidiary_level3;}
	  $SubHead3 = $this->alllist->GetAjaxSubChildAccountList($row->company_id,$row->group_id,$sub_head1,$sub_head2,$sub_head3);
	  echo $row->account_id."##&##".$row->company_id."##&##".$row->head_id."##&##".$row->group_id."##&##".$row->subsidiary_level1."##&##".$row->subsidiary_level2."##&##".$row->subsidiary_level3."##&##".$row->head_type."##&##".$row->account_name."##&##".$row->account_details."##&##".$row->count_unit."##&##".$row->status."@@##@@".$SubHead1."@@##@@".$SubHead2."@@##@@".$SubHead3;
	}
	function editRecord(){
		$this->Site_model->has_menupermission($this->uri->segment(1));

		$this->Accounthead_model->EditRecord();
	}
	function GetAjaxSubAccountList(){
		$company_id=$this->input->post('company-id');
		$group_id  =$this->input->post('group-id');
		$sub_head  =$this->input->post('sub-head');
		if(empty($sub_head)){$sub_head=0;}
		echo $this->alllist->GetAjaxSubAccountList($company_id,$group_id,$sub_head);
	}
	function GetAjaxChildAccountList(){
		
		$company_id 	=$this->input->post('company-id');
		$group_id  	=$this->input->post('group-id');
		$sub_head  	=$this->input->post('sub-head');
		$child_head  	=$this->input->post('child-head');
		if(empty($sub_head)){$sub_head=0;} if(empty($child_head)){$child_head=0;}
		echo $this->alllist->GetAjaxChildAccountList($company_id,$group_id,$sub_head,$child_head);
	}
	function GetAjaxSubChildAccountList(){
		
		$company_id 	=$this->input->post('company-id');
		$group_id  	=$this->input->post('group-id');
		$sub1_id  	=$this->input->post('sub1-id');
		$sub2_id  	=$this->input->post('sub2-id');
		$sub3_id  	=$this->input->post('sub3-id');
		if(empty($sub1_id)){$sub1_id=0;} if(empty($sub2_id)){$sub2_id=0;}if(empty($sub3_id)){$sub3_id=0;}
		echo $this->alllist->GetAjaxSubChildAccountList($company_id,$group_id,$sub1_id,$sub2_id,$sub3_id);
	}	

//End Class
}
