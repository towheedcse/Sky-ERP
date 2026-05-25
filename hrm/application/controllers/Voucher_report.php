<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Voucher_report extends CI_Controller {	
	function __construct(){
		parent::__construct();
		$this->load->model("Billing_model");
		$this->load->model("Voucher_model");
		$this->load->library('Alllist');
	}    
	//======== Voucher List ========
	function VoucherList(){
		$menu_slug= $this->uri->segment(1);	
		$this->Site_model->has_menupermission($menu_slug);  
		$data['hasViewOption']  	= $this->Site_model->hasOptionPermission($menu_slug,"View");    
		$data['hasPrintOption']   	= $this->Site_model->hasOptionPermission($menu_slug,"Print");
		$data['squery']				= $this->alllist->GetStudentList();
		$data['cquery']				= $this->alllist->GetCompanyList();
		$data['bquery']				= $this->alllist->GetBranchList();
		$data['sequery']			= $this->alllist->GetSessionList();
		$data['vquery']				= $this->alllist->GetVersionList();
		$data['clquery']			= $this->alllist->GetClassList();
		$data['gquery']				= $this->alllist->GetGroupsList();
		$data['scquery']			= $this->alllist->GetSectionList();
		$data['shquery']			= $this->alllist->GetShiftList();
		$data['fpquery']			= $this->alllist->GetFeePeriodList();
		$data['cash_account'] 		= $this->Voucher_model->GetDrAccountList(4);
		$data['bank_account'] 		= $this->Voucher_model->GetDrAccountList(5);
		$data['dnt_account'] 		= $this->Voucher_model->GetDrAccountList(6);
		$data['pdd_account'] 		= $this->Voucher_model->GetDrAccountList(19);
		
		$data['tah_account'] 		= $this->Voucher_model->GetDrAccountList(1);
		$data['aah_account'] 		= $this->Voucher_model->GetDrAccountList(2);
		$data['sah_account'] 		= $this->Voucher_model->GetDrAccountList(3);
		$expense_head				= array(9,17,18,19,20,21,22,23,24);
		$data['exp_account'] 		= $this->Voucher_model->GetDrAccountList($expense_head);
		$data['eah_account'] 		= $this->Voucher_model->GetDrAccountList(10);
		$data['voucher_date']   	= date("d-m-Y");
		$this->load->view('voucher_list',$data);
	}
	
	function GetRecords(){
		$this->Site_model->has_menupermission($this->uri->segment(1));
		$voucher_type	= $this->input->post('voucher-type');if(empty($voucher_type)){$voucher_type=2;}
		$this->Voucher_model->GetRecordGrid($voucher_type);
	}
	//====== Start Report ======
	function ViewVoucher(){
		$this->Site_model->has_menupermission($this->uri->segment(1));
		$menu_slug= $this->uri->segment(1);			
		$data['hasViewOption']  	= $this->Site_model->hasOptionPermission($menu_slug,"View");    
		$data['hasPrintOption']   	= $this->Site_model->hasOptionPermission($menu_slug,"Print");
		$contra_id	= $this->uri->segment(3);	
		$data['voucher_id']= $contra_id;
		$this->load->view('print_voucher',$data);
	}	
	function GetVoucherInfo(){
		$this->Site_model->has_menupermission($this->uri->segment(1));
		$this->load->model("Report_model");
		
		$this->Report_model->GetPrintVoucher();
	}
	function GetAjaxInvoiceList(){
		$customer_id =$this->input->post('cr-account');
		echo $this->Voucher_model->GetAjaxInvoiceList($customer_id);
	}
//End Class
}
