<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Voucher extends CI_Controller {	
	function __construct(){
		parent::__construct();
		$this->load->model("Billing_model");
		$this->load->model("Voucher_model");
		$this->load->library('Alllist');
	}
		
	function index(){
		$menu_slug= $this->uri->segment(1);	
		$this->Site_model->has_menupermission($menu_slug);			    
		$data['hasCreateOption']	= $this->Site_model->hasOptionPermission($menu_slug,"Create");				    
		$data['hasConcessionOption']= $this->Site_model->hasOptionPermission($menu_slug,"Concession");    
		$data['hasViewOption']  	= $this->Site_model->hasOptionPermission($menu_slug,"View");    
		$data['hasPrintOption']   	= $this->Site_model->hasOptionPermission($menu_slug,"Print");
		$data['squery']				= $this->alllist->GetRegCustomerList();
		$data['cquery']				= $this->alllist->GetCompanyList();
		$data['bquery']				= $this->alllist->GetBranchList();
		$data['sequery']			= $this->alllist->GetSessionList();
		$data['mpquery']			= $this->alllist->GetMideaList();
		$data['apquery']			= $this->alllist->GetAgentList();
		$data['fpquery']			= $this->alllist->GetFeePeriodList();
		$data['cash_account'] 		= $this->Voucher_model->GetDrAccountList(4);
		$data['bank_account'] 		= $this->Voucher_model->GetDrAccountList(5);
		$data['dnt_account'] 		= $this->Voucher_model->GetDrAccountList(6);
		$data['pdd_account'] 		= $this->Voucher_model->GetDrAccountList(19);
		$data['bkash_account'] 		= $this->Voucher_model->GetDrAccountList(30);
		$data['card_account'] 		= $this->Voucher_model->GetDrAccountList(31);
		$data['voucher_date']   	= date("d-m-Y");
		$this->load->view('received_voucher',$data);
	}	
	
	function SaveRecord(){// Function Standard PascalCase		
		$this->Site_model->has_menupermission($this->uri->segment(1));
		$res = $this->Voucher_model->saveReceivedCVMaster();
		if($res){
		    $this->Voucher_model->GetRecordGrid(2);
		}else{
		    echo "0";
		}		
	}		
	function GetRecords(){
		$this->Site_model->has_menupermission($this->uri->segment(1));
		$this->Voucher_model->GetRecordGrid(2);
	}

    function AddCollection(){
		$this->Site_model->has_menupermission($this->uri->segment(1));		
		$account_id		= $this->input->post('account-id');
		$contra_id		= $this->input->post('contra-id');
		$voucher_type	= $this->input->post('voucher-type');
		$this->Voucher_model->saveCVDetails();
		if(empty($account_id)){$account_id=0;} if(empty($contra_id)){$contra_id=0;} if(empty($voucher_type)){$voucher_type=2;}
		echo $this->Voucher_model->GetDraftRecordGrid($contra_id,$account_id,$voucher_type);
	}
    function GetDraftRecords(){		
		$this->Site_model->has_menupermission($this->uri->segment(1));
		$account_id		= $this->input->post('account-id');
		$contra_id		= $this->input->post('contra-id');
		$voucher_type	= $this->input->post('voucher-type');
		if(empty($account_id)){$account_id= 0;} if(empty($contra_id)){$contra_id= 0;} if(empty($voucher_type)){$voucher_type=2;}
		echo $this->Voucher_model->GetDraftRecordGrid($contra_id,$account_id,$voucher_type);
	}
	function GetConcessionOn(){
		echo $this->Voucher_model->GetAjaxConcessionOn();
	}
    /*==== Start Advanced Received =====*/  
	
	function Advanced(){
		$this->Site_model->has_menupermission($this->uri->segment(1));
		$data['cash_account'] 	=$this->Voucher_model->GetDrAccountList(4);
		$data['bank_account'] 	=$this->Voucher_model->GetDrAccountList(5);
		$data['dnt_account'] 	=$this->Voucher_model->GetDrAccountList(6);
		$data['pdd_account'] 	=$this->Voucher_model->GetDrAccountList(19);
		$data['cquery'] 		=$this->Commercial_model->GetCustomerList();
		$data['aquery'] 		=$this->Releaseorder_model->GetAgencyList();
		$data['voucher_date']   =date("d-m-Y");
		$this->load->view('advanced_received_voucher',$data);
	}	
	
	function SaveAdvancedReceived(){// Function Standard PascalCase
		$this->Site_model->has_menupermission($this->uri->segment(1));
		$this->Voucher_model->SaveARV();
		$voucher_type	= $this->input->post('voucher-type'); if(empty($voucher_type)){$voucher_type=2;}
		$this->Voucher_model->GetAdvancedRecordGrid($voucher_type);
	}		
	function GetAdvancedRecords(){
		$this->Site_model->has_menupermission($this->uri->segment(1));
		$voucher_type	= $this->input->post('voucher-type'); if(empty($voucher_type)){$voucher_type=2;}
		$this->Voucher_model->GetAdvancedRecordGrid($voucher_type);
	}
    function UpdateAdvancedReceived(){
		$this->Site_model->has_menupermission($this->uri->segment(1));		
		$res = $this->Voucher_model->saveCVMaster();
		if($res){
			$voucher_type	= $this->input->post('voucher-type'); if(empty($voucher_type)){$voucher_type=2;}
		    $this->Voucher_model->GetAdvancedRecordGrid($voucher_type);
		}else{
		    echo "0";
		}
    }
	/*==== End Advanced Received =======*/
	function FillMasterRecord(){
		$this->Site_model->has_menupermission($this->uri->segment(1));
		$row1 = $this->Voucher_model->FillVMRecord();
		$row2 = $this->Voucher_model->FillVDRecord("Dr",$row1->voucher_type);
		$row3 = $this->Voucher_model->FillVDRecord("Cr",$row1->voucher_type);
		$onlyGrid = 1; $CrList=""; $INVList="";
		
		if($row1->voucher_type==1){ // Payment
			$INVList = $this->Voucher_model->GetAjaxPaymentInvoiceList($row2->account_id,$row2->invoice_no);
		    $Grid    = $this->Voucher_model->GetDraftRecordGrid($row1->contra_id,$row2->account_id,$row1->voucher_type,$onlyGrid);
		}elseif($row1->voucher_type==3){ // Expense
			$expense_head	= array(6,7,9,10,17,18,19,20,21,22,23,24,25,55,56);
			$CrList = $this->Voucher_model->GetDrAccountList($expense_head);
		    $Grid   = $this->Voucher_model->GetDraftRecordGrid($row1->contra_id,$row2->account_id,$row1->voucher_type,$onlyGrid);
		}else{
			$Grid    = $this->Voucher_model->GetDraftRecordGrid($row1->contra_id,$row3->account_id,$row1->voucher_type,$onlyGrid);
			$INVList = $this->Voucher_model->GetAjaxInvoiceList($row1->account_id);
		}		
		
		$AdmList = $this->alllist->GetAjaxRegCustomerList($row1->institute_id,$row1->branch_id,$row1->admission_id);
		if(empty($row1->voucher_date)){$voucher_date = date("Y-m-d");}
		else{$voucher_date =$row1->voucher_date;}

		echo $row1->contra_id."##&##".$row1->voucher_no."##&##".$row1->mode_of_payment."##&##".$this->Voucher_model->formatDateDMY($voucher_date)."##&##".$row1->voucher_type."##&##".$row3->bank_name."##&##".$row3->branch_name."##&##".$row3->acc_no."##&##".$row3->cheque_no."##&##".$this->Voucher_model->formatDateDMY($row3->cheque_issue_date)."##&##".$row3->cheque_type."##&##".$row2->account_id."##&##".$row2->headtypes."##&##".$row3->account_id."##&##".$row3->headtypes."##&##".$row1->dr_amount."##&##".$row1->description."##&##".$row1->others_income."##&##".$row1->status."##&##".$row1->including_vat."##&##".$row1->institute_id."##&##".$row1->branch_id."##&##".$row1->session_id."##&##".$row1->version_id."##&##".$row1->class_id."##&##".$row1->group_id."##&##".$row1->admission_id."@@##@@".$Grid."@@##@@".$INVList."@@##@@".$AdmList."@@##@@".$CrList;
	}	
	function FillRecord(){
		$this->Site_model->has_menupermission($this->uri->segment(1));
		$row1 = $this->Voucher_model->FillVMRecord();
		$row2 = $this->Voucher_model->FillVDRecord("Dr",$row1->voucher_type);
		$row3 = $this->Voucher_model->FillVDRecord("Cr",$row1->voucher_type);
		if(empty($row1->voucher_date)){$voucher_date = date("Y-m-d");}
		else{$voucher_date =$row1->voucher_date;}
		if(empty($row1->account_id)){$dr_account =$row2->account_id; $admission_id =$row1->admission_id;}else{$dr_account =$row1->account_id; $admission_id =$row1->admission_id;}
		echo $row1->contra_id."##&##".$row1->voucher_no."##&##".$row1->mode_of_payment."##&##".$this->Voucher_model->formatDateDMY($voucher_date)."##&##".$row1->voucher_type."##&##".$row3->bank_name."##&##".$row3->branch_name."##&##".$row3->acc_no."##&##".$row3->cheque_no."##&##".$this->Voucher_model->formatDateDMY($row3->cheque_issue_date)."##&##".$row3->cheque_type."##&##".$row2->account_id."##&##".$row2->headtypes."##&##".$dr_account."##&##".$row3->headtypes."##&##".$row3->invoice_no."##&##".$row3->amount."##&##".$row1->description."##&##".$row3->including_vat."##&##".$row3->advance_collect."##&##".$row1->status."##&##".$row3->details_id."##&##".$admission_id."##&##".$row3->account_id."##&##".$row2->receive_note;
	}
	function DeleteRecord(){		
		$this->Site_model->has_menupermission($this->uri->segment(1));
		$this->Voucher_model->DeleteRecord();
	}
	function DishonoredCheque(){		
		$this->Site_model->has_menupermission($this->uri->segment(1));
		$this->Voucher_model->DishonoredCheque();
	}
	function DeleteItem(){		
		$this->Site_model->has_menupermission($this->uri->segment(1));
		$account_id		= $this->input->post('account-id');
		$contra_id		= $this->input->post('contra-id');
		$voucher_type	= $this->input->post('voucher-type');
		if(empty($account_id)){$account_id=0;} if(empty($contra_id)){$contra_id=0;} if(empty($voucher_type)){$voucher_type=2;}
		$dres = $this->Voucher_model->DeleteVoucherItem();
		if($dres==1){
		echo $this->Voucher_model->GetDraftRecordGrid($contra_id,$account_id,$voucher_type);
		} 
		echo $dres;
	}
	function AjaxUpdateDiscount(){
		$discount_type		= $this->input->post('discount_type');
		$discount_amount	= $this->input->post('discount_amount');
		$admission_id		= $this->input->post('admission_id');
		if($admission_id >0 && $discount_type >0 && $discount_amount>0){
		echo $this->Voucher_model->UpdateBillingDiscount($admission_id,$discount_type,$discount_amount);
		}
	}
	//====== Start Report ======
	function ViewReceivedVoucher(){
		$this->Site_model->has_menupermission($this->uri->segment(1));
		$menu_slug= $this->uri->segment(1);			
		$data['hasViewOption']  	= $this->Site_model->hasOptionPermission($menu_slug,"View");    
		$data['hasPrintOption']   	= $this->Site_model->hasOptionPermission($menu_slug,"Print");
		$contra_id	= $this->uri->segment(3);	
		$data['voucher_id']= $contra_id;
		$this->load->view('print_received_voucher',$data);
	}
	function ViewPaymentVoucher(){
		$this->Site_model->has_menupermission($this->uri->segment(1));
		$menu_slug= $this->uri->segment(1);			
		$data['hasViewOption']  	= $this->Site_model->hasOptionPermission($menu_slug,"View");    
		$data['hasPrintOption']   	= $this->Site_model->hasOptionPermission($menu_slug,"Print");
		$contra_id	= $this->uri->segment(3);	
		$data['voucher_id']= $contra_id;
		$this->load->view('print_payment_voucher',$data);
	}
	function ViewExpenseVoucher(){
		$this->Site_model->has_menupermission($this->uri->segment(1));
		$menu_slug= $this->uri->segment(1);			
		$data['hasViewOption']  	= $this->Site_model->hasOptionPermission($menu_slug,"View");    
		$data['hasPrintOption']   	= $this->Site_model->hasOptionPermission($menu_slug,"Print");
		$contra_id	= $this->uri->segment(3);	
		$data['voucher_id']= $contra_id;
		$this->load->view('print_expense_voucher',$data);
	}
	function ViewJournalVoucher(){
		$this->Site_model->has_menupermission($this->uri->segment(1));
		$menu_slug= $this->uri->segment(1);			
		$data['hasViewOption']  	= $this->Site_model->hasOptionPermission($menu_slug,"View");    
		$data['hasPrintOption']   	= $this->Site_model->hasOptionPermission($menu_slug,"Print");
		$contra_id	= $this->uri->segment(3);	
		$data['voucher_id']= $contra_id;
		$this->load->view('print_journal_voucher',$data);
	}	
	function GetVoucherInfo(){
		$this->Site_model->has_menupermission($this->uri->segment(1));
		$this->load->model("Report_model");
		
		$this->Report_model->GetPrintVoucher();
	}
		
	function GetCustomVoucherInfo(){
		$this->Site_model->has_menupermission($this->uri->segment(1));
		$this->load->model("Report_model");		
		$this->Report_model->GetPrintCustomVoucher();
	}
	function GetAjaxInvoiceList(){
		$customer_id =$this->input->post('cr-account');
		echo $this->Voucher_model->GetAjaxInvoiceList($customer_id);
	}

	function getAjaxInvoiceInfo(){
		$row = $this->Voucher_model->getAjaxInvoiceInfo();
		echo $row->net_bill_amount."##&##".$row->paid_amount."##&##".$row->due_amount;
	}
	//======== Payment Voucher ========
	function PaymentVoucher(){
		$menu_slug= $this->uri->segment(1)."/PaymentVoucher";	
		$this->Site_model->has_menupermission($menu_slug);			    
		$data['hasCreateOption']	= $this->Site_model->hasOptionPermission($menu_slug,"Create");				    
		$data['hasConcessionOption']= $this->Site_model->hasOptionPermission($menu_slug,"Concession");    
		$data['hasViewOption']  	= $this->Site_model->hasOptionPermission($menu_slug,"View");    
		$data['hasPrintOption']   	= $this->Site_model->hasOptionPermission($menu_slug,"Print");
		$data['squery']				= $this->alllist->GetRegCustomerList();
		$data['cquery']				=$this->alllist->GetCompanyList();
		$data['bquery']				=$this->alllist->GetBranchList();
		$data['sequery']			=$this->alllist->GetSessionList();
		$data['fpquery']			=$this->alllist->GetFeePeriodList();
		$data['cash_account'] 		= $this->Voucher_model->GetDrAccountList(4);
		$data['bank_account'] 		= $this->Voucher_model->GetDrAccountList(5);
		$data['dnt_account'] 		= $this->Voucher_model->GetDrAccountList(6);
		$data['pdd_account'] 		= $this->Voucher_model->GetDrAccountList(19);
		$data['bkash_account'] 		= $this->Voucher_model->GetDrAccountList(30);
		$data['card_account'] 		= $this->Voucher_model->GetDrAccountList(31);
		
		$data['tah_account'] 		= $this->Voucher_model->GetDrAccountList(1);
		$data['aah_account'] 		= $this->Voucher_model->GetDrAccountList(2);
		$data['sah_account'] 		= $this->Voucher_model->GetDrAccountList(3);
		$expense_head				= array(9,17,18,19,20,21,22,23,24,25);
		$data['exp_account'] 		= $this->Voucher_model->GetDrAccountList($expense_head);
		$data['eah_account'] 		= $this->Voucher_model->GetDrAccountList(10);
		$data['voucher_date']   	= date("d-m-Y");
		$this->load->view('payment_voucher',$data);
	}		
	function GetPaymentRecords(){
		$this->Site_model->has_menupermission($this->uri->segment(1));
		$this->Voucher_model->GetRecordGrid(1);
	}
	function SavePaymentRecord(){// Function Standard PascalCase		
		$this->Site_model->has_menupermission($this->uri->segment(1));
		$res = $this->Voucher_model->savePaymentCVMaster();
		if($res){
		    $this->Voucher_model->GetRecordGrid(1);
		}else{
		    echo "0";
		}		
	}
    function GetPaymentDraftRecords(){		
		$this->Site_model->has_menupermission($this->uri->segment(1));
		$account_id		= $this->input->post('account-id');
		$contra_id		= $this->input->post('contra-id');
		$voucher_type	= $this->input->post('voucher-type');
		if(empty($account_id)){$account_id=0;} if(empty($contra_id)){$contra_id=0;} if(empty($voucher_type)){$voucher_type=1;}
		echo $this->Voucher_model->GetDraftRecordGrid($contra_id,$account_id,$voucher_type);
	}
	function GetAjaxPaymentInvoiceList(){
		$customer_id =$this->input->post('cr-account');
		$bill_type   = "3,4"; //3=Salary
		echo $this->Voucher_model->GetAjaxPaymentInvoiceList($customer_id,$bill_type);
	}
	
	//======== Expense Voucher ========
	function ExpenseVoucher(){
		$menu_slug= $this->uri->segment(1);	
		$this->Site_model->has_menupermission($menu_slug);			    
		$data['hasCreateOption']	= $this->Site_model->hasOptionPermission($menu_slug,"Create");   
		$data['hasViewOption']  	= $this->Site_model->hasOptionPermission($menu_slug,"View");    
		$data['hasPrintOption']   	= $this->Site_model->hasOptionPermission($menu_slug,"Print");
		$data['squery']				= $this->alllist->GetRegCustomerList();
		$data['cquery']				= $this->alllist->GetCompanyList();
		$data['bquery']				= $this->alllist->GetBranchList();
		$data['sequery']			= $this->alllist->GetSessionList();
		$data['fpquery']			= $this->alllist->GetFeePeriodList();
		$data['cash_account'] 		= $this->Voucher_model->GetDrAccountList(4);
		$data['bank_account'] 		= $this->Voucher_model->GetDrAccountList(5);
		$data['dnt_account'] 		= $this->Voucher_model->GetDrAccountList(6);
		$data['pdd_account'] 		= $this->Voucher_model->GetDrAccountList(19);
		$data['bkash_account'] 		= $this->Voucher_model->GetDrAccountList(30);
		$data['card_account'] 		= $this->Voucher_model->GetDrAccountList(31);
		
		$data['tah_account'] 		= $this->Voucher_model->GetDrAccountList(1);
		$data['aah_account'] 		= $this->Voucher_model->GetDrAccountList(2);
		$data['sah_account'] 		= $this->Voucher_model->GetDrAccountList(3);
		$expense_head				= array(6,7,9,10,17,18,19,20,21,22,23,24,25,55,56);
		$data['exp_account'] 		= $this->Voucher_model->GetDrAccountList($expense_head);
		$data['eah_account'] 		= $this->Voucher_model->GetDrAccountList(10);
		$data['voucher_date']   	= date("d-m-Y");
		$this->load->view('expense_voucher',$data);
	}		
	function GetExpenseRecords(){
		$this->Site_model->has_menupermission($this->uri->segment(1));
		$this->Voucher_model->GetRecordGrid(3);
	}
	function SaveExpenseRecord(){// Function Standard PascalCase		
		$this->Site_model->has_menupermission($this->uri->segment(1));
		$res = $this->Voucher_model->saveExpenseCVMaster();
		if($res){
		    $this->Voucher_model->GetRecordGrid(3);
		}else{
		    echo "0";
		}		
	}
	function FillExpenseRecord(){
		$this->Site_model->has_menupermission($this->uri->segment(1));
		$row1 = $this->Voucher_model->FillVMRecord();
		$row2 = $this->Voucher_model->FillVDRecord("Dr",$row1->voucher_type);
		$row3 = $this->Voucher_model->FillVDRecord("Cr",$row1->voucher_type);
		$onlyGrid = 1; $CrList=""; $INVList=""; $AdmList="";
		
		$expense_head	= array(6,9,12,14,16,27,55,56);
		$CrList = $this->Voucher_model->GetDrAccountList($expense_head);
		$Grid   = $this->Voucher_model->GetDraftRecordGrid($row1->contra_id,$row2->account_id,$row1->voucher_type,$onlyGrid);		
		
		if(empty($row1->voucher_date)){$voucher_date = date("Y-m-d");}
		else{$voucher_date =$row1->voucher_date;}
		
		echo $row1->contra_id."##&##".$row1->voucher_no."##&##".$row1->mode_of_payment."##&##".$this->Voucher_model->formatDateDMY($voucher_date)."##&##".$row1->voucher_type."##&##".$row3->bank_name."##&##".$row3->branch_name."##&##".$row3->acc_no."##&##".$row3->cheque_no."##&##".$this->Voucher_model->formatDateDMY($row3->cheque_issue_date)."##&##".$row3->cheque_type."##&##".$row2->account_id."##&##".$row2->headtypes."##&##".$row3->account_id."##&##".$row3->headtypes."##&##".$row1->dr_amount."##&##".$row1->vat_amount."##&##".$row1->description."##&##".$row1->status."##&##".$row1->institute_id."##&##".$row1->branch_id."##&##".$row1->session_id."##&##".$row1->period_id."##&##".$row1->class_id."##&##".$row1->group_id."##&##".$row1->admission_id."@@##@@".$Grid."@@##@@".$INVList."@@##@@".$AdmList."@@##@@".$CrList;
	}
	function AllVoucherList(){
		$menu_slug= $this->uri->segment(1);	
		$this->Site_model->has_menupermission($menu_slug);  
		$data['hasViewOption']  	= $this->Site_model->hasOptionPermission($menu_slug,"View");    
		$data['hasPrintOption']   	= $this->Site_model->hasOptionPermission($menu_slug,"Print");
		$data['squery']				= $this->alllist->GetCustomerList();
		$data['cquery']				= $this->alllist->GetCompanyList();
		$data['bquery']				= $this->alllist->GetBranchList();
		$data['sequery']			= $this->alllist->GetSessionList();
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
	function GetAllRecords(){
		$this->Site_model->has_menupermission($this->uri->segment(1));
		$voucher_type	= $this->input->post('voucher-type');if(empty($voucher_type)){$voucher_type=2;}
		$this->Voucher_model->GetAllRecordGrid($voucher_type);
	}
	function GetCustomerList(){
		$institute_id		=$this->input->post('institute_id');
		$branch_id			=$this->input->post('branch_id');
		$customer_id		=$this->input->post('customer');
		echo $this->alllist->GetAjaxRegCustomerList($institute_id,$branch_id,$customer_id);
	}
//End Class
}
