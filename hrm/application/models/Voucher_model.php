<?php 
class Voucher_model extends CI_Model {
		
    function __construct()
    {
		parent::__construct();
    }	
    
	function GetAjaxConcessionOn(){
		$institute_id		=$this->input->post('institute_id');
		$branch_id			=$this->input->post('branch_id');		
		$discount_type		=$this->input->post('discount_type');		
		$admission_id		=$this->input->post('cr_account');
		if(empty($discount_type)){$discount_type=0;}
		
		if($institute_id >0 && $branch_id >0 && $admission_id>0){
			$asql = "SELECT *,session_id as sessions_id FROM ".ADMISSION_TBL." WHERE institute_id = $institute_id AND branch_id = $branch_id AND admission_id=$admission_id AND status = 1 ORDER BY admission_id DESC";
			$aquery = $this->db->query($asql);				
			if($aquery->num_rows() >0){	
				$session_id = $aquery->row()->sessions_id;
				$version_id = $aquery->row()->version_id;
				$class_id   = $aquery->row()->class_id;				
				$group_id   = $aquery->row()->group_id;
				$fee_period = $aquery->row()->fee_period;
				$total_bill = $aquery->row()->total_bill;
			    $discount_percentage = $aquery->row()->discount_percentage;
			    $discount_amount     = $aquery->row()->discount_amount;
			    $less_tuitionfee     = $aquery->row()->less_tuitionfee;
				$FeePeriod  = (($fee_period * 2) - 1);
				$concession_on = 0;	
				if($discount_type ==2){
				  //===== Get Consession On ======
				  $concession_hreads = $this->session->userdata('concession_hreads');
				  $cfsql = "SELECT SUM(fee_amount) as concession_on FROM ".COURSE_FEE_TBL." WHERE institute_id = $institute_id AND branch_id = $branch_id AND sessions_id = $session_id AND version_id=$version_id 
				  AND class_id=$class_id AND group_id=$group_id AND SUBSTRING(`fee_month`,$FeePeriod,1) > 0 AND `account_id` IN($concession_hreads)";
				  $caquery = $this->db->query($cfsql);				
				  if($caquery->num_rows() >0){
				      $concession_on = $caquery->row()->concession_on;
				      return $total_bill."##&##".$concession_on."##&##".$discount_percentage."##&##".$discount_amount."##&##".$less_tuitionfee."##&##".$fee_period;				  
				  }else{
					  return $total_bill."##&##".$total_bill."##&##".$discount_percentage."##&##".$discount_amount."##&##".$less_tuitionfee."##&##".$fee_period;
				  }
				}elseif($discount_type ==1){
					return $total_bill."##&##".$total_bill."##&##0##&##".$discount_amount."##&##0##&##".$fee_period;  
				}
			}else{
				return "0##&##0##&##0##&##0##&##0##&##0";
			}
		}
	}
	
    function SaveRV(){	
		$contra_id	= $this->input->post('contra-id');
		if(empty($contra_id)){ $contra_id=0; }	
			$bill_id	= $this->input->post('invoice-no');
		if(empty($bill_id)){ $bill_id=0; }	
			$mode_of_payment= $this->input->post('payment-mode');
			$voucher_date   = $this->formatDate($this->input->post('voucher-date'));
			$voucher_no	= $this->input->post('voucher-no');
		if(empty($voucher_no)){			
			$voucher_no 	= $this->getVoucherID($voucher_date);
		}
		
    	$bank_name	= $this->input->post('bank-name');
    	$branch_name= $this->input->post('branch-name');
    	$acc_no		= $this->input->post('acc-no');
    	$cheque_no	= $this->input->post('cheque-no');
		$cheque_type= $this->input->post('cheque-type');
		$issue_date	= $this->formatDate($this->input->post('issue-date'));		
		$dr_account	= $this->input->post('dr-account');
		$cr_account	= $this->input->post('cr-account');
		
		$dr_amount 	= $this->input->post('received-amount');
		$cr_amount 	= $this->input->post('received-amount');
		
		$voucher_type	= $this->input->post('voucher-type'); // 2=Received		
		$description	= $this->input->post('naration');
		$including_vat	= $this->input->post('including-vat');	
		$created_by		= $this->session->userdata('created_by');
		$CQType	=""; $cheque_details="";
		if($mode_of_payment==2){
			if($cheque_type==1){
			$CQType ="Cash Cheque";
			}elseif($cheque_type==2){
			$CQType ="A/C Payee Cheque";
			}elseif($cheque_type==3){
			$CQType ="Bearer Cheque";
			}elseif($cheque_type==4){
			$CQType ="Pay Order";
			}elseif($cheque_type==5){
			$CQType ="Bank Transfer";
			}
			$cheque_details = $bank_name.", ".$branch_name."<br>A/C No. ".$acc_no.", C/Q No. ".$cheque_no."<br>Issue Date: ".$issue_date.", C/Q Type: ".$CQType;
		}// End if mode_of_payment
		if($contra_id >0){			
			//=== Update Master =====
			$SQL= "UPDATE ".VOUCHER_MASTER_TBL." SET dr_amount='".$dr_amount."',cr_amount='".$cr_amount."', hrm_mode_of_payment='".$mode_of_payment."', hrm_voucher_type='".$voucher_type."', description='".$description."' WHERE contra_id = ".$contra_id;
			$this->db->query($SQL);
			$this->rollbackBillPayment($contra_id);
			$this->AdjustToInvoice($dr_account,$cr_account,$contra_id,$bill_id,$dr_amount,$including_vat);
			//==== Dr Account Update =====	
			$DSQL= "UPDATE ".VOUCHER_DETAILS_TBL." SET amount='".$dr_amount."', hrm_voucher_type='".$voucher_type."', description='".$description."' WHERE account_id='".$dr_account."' AND contra_id = ".$contra_id;
			$this->db->query($DSQL);			
			$this->SaveAccountLedger($voucher_no,$bill_id,$voucher_date,$dr_account,$voucher_type,$description,$dr_amount,"Dr","U");
			if($mode_of_payment==2){
			//==== Cr Account Update =====				
			$CSQL= "UPDATE ".VOUCHER_DETAILS_TBL." SET amount='".$cr_amount."', bank_name='".$bank_name."', branch_name='".$branch_name."', acc_no='".$acc_no."', cheque_no='".$cheque_no."', cheque_issue_date='".$issue_date."', hrm_cheque_type='".$cheque_type."', hrm_voucher_type='".$voucher_type."', description='".$description."' WHERE account_id='".$cr_account."' AND contra_id = ".$contra_id;
			$this->db->query($CSQL);			
			$this->SaveAccountLedger($voucher_no,$bill_id,$voucher_date,$cr_account,$voucher_type,$description,$cr_amount,"Cr","U",$cheque_details);
			}else{
			//==== Cr Account Update =====				
			$CSQL= "UPDATE ".VOUCHER_DETAILS_TBL." SET amount='".$cr_amount."', hrm_voucher_type='".$voucher_type."', description='".$description."' WHERE account_id='".$cr_account."' AND contra_id = ".$contra_id;
			$this->db->query($CSQL);
			$this->SaveAccountLedger($voucher_no,$bill_id,$voucher_date,$cr_account,$voucher_type,$description,$cr_amount,"Cr","U");
			}
			
		}else{
			$SQL= "INSERT INTO ".VOUCHER_MASTER_TBL."(invoice_no,voucher_no,voucher_date,dr_amount,cr_amount,hrm_mode_of_payment,hrm_voucher_type,description,created_by) ";
			$SQL.="VALUES('".$bill_id."','".$voucher_no."','".$voucher_date."','".$dr_amount."','".$cr_amount."','".$mode_of_payment."','".$voucher_type."','".$description."','".$created_by."')";
			if($voucher_no !=""){
				$this->db->query($SQL);
				$contra_id = $this->db->insert_id();
				$this->AdjustToInvoice($dr_account,$cr_account,$contra_id,$bill_id,$dr_amount,$including_vat);
				if($contra_id >0){			
				//==== Dr Account =====	
				$DSQL="INSERT INTO ".VOUCHER_DETAILS_TBL."(contra_id,invoice_no,voucher_no,headtypes,account_id,amount,hrm_voucher_type,description,hrm_created_by) ";
				$DSQL.="VALUES('".$contra_id."','".$bill_id."','".$voucher_no."','Dr','".$dr_account."','".$dr_amount."','".$voucher_type."','".$description."','".$created_by."')";
				$this->db->query($DSQL);
				$this->SaveAccountLedger($voucher_no,$bill_id,$voucher_date,$dr_account,$voucher_type,$description,$dr_amount,"Dr","I");
				 if($mode_of_payment==2){
				 //==== Cr Account =====
				 $CSQL="INSERT INTO ".VOUCHER_DETAILS_TBL."(contra_id,invoice_no,voucher_no,headtypes,account_id,amount,bank_name,branch_name,acc_no,cheque_no,cheque_issue_date,hrm_cheque_type,hrm_voucher_type,description,hrm_created_by) ";
				 $CSQL.="VALUES('".$contra_id."','".$bill_id."','".$voucher_no."','Cr','".$cr_account."','".$cr_amount."','".$bank_name."','".$branch_name."','".$acc_no."','".$cheque_no."','".$issue_date."','".$cheque_type."','".$voucher_type."','".$description."','".$created_by."')";
				 $this->db->query($CSQL);
				 $this->SaveAccountLedger($voucher_no,$bill_id,$voucher_date,$cr_account,$voucher_type,$description,$cr_amount,"Cr","I",$cheque_details);
				 }else{
				 //==== Cr Account =====
				 $CSQL="INSERT INTO ".VOUCHER_DETAILS_TBL."(contra_id,invoice_no,voucher_no,headtypes,account_id,amount,hrm_voucher_type,description,hrm_created_by) ";
				 $CSQL.="VALUES('".$contra_id."','".$bill_id."','".$voucher_no."','Cr','".$cr_account."','".$cr_amount."','".$voucher_type."','".$description."','".$created_by."')";
				 $this->db->query($CSQL);
				 $this->SaveAccountLedger($voucher_no,$bill_id,$voucher_date,$cr_account,$voucher_type,$description,$cr_amount,"Cr","I");
				 }//end if mode_of_payment ==2
				}//end if contra_id >0
				return $voucher_no;
				//print  $this->db->last_query();
			}
		}
	
		//print $this->db->last_query();
    }
    function SaveAccountLedger($voucher_no,$invoice_no,$received_date,$account_id,$transaction_type,$description,$amount,$headtype,$mode,$cheque_details=NULL){
		$created_by	= $this->session->userdata('created_by');
		if($headtype=="Dr"){$dr=$amount; $cr=0;}else{$dr=0; $cr=$amount;}
		
		if($mode=="I"){		
		$SQL="INSERT INTO ".ACC_LEDGER_TBL."(voucher_no,invoice_no,received_date,account_id,hrm_transaction_type,description,dr,cr,cheque_details,hrm_created_by) ";
		$SQL.="VALUES('".$voucher_no."','".$invoice_no."','".$received_date."','".$account_id."','".$transaction_type."','".$description."','".$dr."','".$cr."','".$cheque_details."','".$created_by."')";
		$this->db->query($SQL);
		}else{

		$CSQL= "UPDATE ".ACC_LEDGER_TBL." SET invoice_no='".$invoice_no."', hrm_transaction_type='".$transaction_type."', description='".$description."', dr='".$dr."', cr='".$cr."'";
		if($cheque_details !=""){
		$CSQL.=", cheque_details='".$cheque_details."'";
		}
		$CSQL.=" WHERE account_id='".$account_id."' AND voucher_no = '".$voucher_no."'";
		$this->db->query($CSQL);
		}//End else
    }
    function SaveOthersAccountLedger($voucher_no,$invoice_no,$received_date,$account_id,$transaction_type,$description,$amount,$headtype,$mode,$others_income=0,$others_source=0,$cheque_details=NULL){
		$created_by	= $this->session->userdata('created_by');
		if($headtype=="Dr"){$dr=$amount; $cr=0;}else{$dr=0; $cr=$amount;}
		
		if($mode=="I"){		
		$SQL="INSERT INTO ".ACC_LEDGER_TBL."(voucher_no,invoice_no,received_date,account_id,hrm_transaction_type,description,dr,cr,cheque_details,others_income,others_source,hrm_created_by) ";
		$SQL.="VALUES('".$voucher_no."','".$invoice_no."','".$received_date."','".$account_id."','".$transaction_type."','".$description."','".$dr."','".$cr."','".$cheque_details."','".$others_income."','".$others_source."','".$created_by."')";
		$this->db->query($SQL);
		}else{

		$CSQL= "UPDATE ".ACC_LEDGER_TBL." SET invoice_no='".$invoice_no."', hrm_transaction_type='".$transaction_type."', description='".$description."', dr='".$dr."', cr='".$cr."'";
		if($cheque_details !=""){
		$CSQL.=", cheque_details='".$cheque_details."'";
		}
		if($others_income >0){
		$CSQL.=", others_income='".$others_income."'";
		}
		if($others_source !=""){
		$CSQL.=", others_source='".$others_source."'";
		}
		$CSQL.=" WHERE account_id='".$account_id."' AND voucher_no = '".$voucher_no."'";
		$this->db->query($CSQL);
		}//End else
    }
    function AdjustToInvoice($dr_account,$account_id,$contra_id,$bill_id,$DrAmount,$including_vat,$admission_id=0){
	$headtypes = $this->getHeadType($dr_account);
	if($DrAmount >0){
		//======= Receivable for Sales to him ===========
		$BSQL="SELECT bill_id,admission_id,session_id,bill_no as invoice_id,billing_month as period_id,net_bill_amount,paid_amount,due_amount FROM ".BILL_MASTER_TBL." WHERE status < 5 ";		
		if($account_id >0){
		$BSQL.= " AND account_id ='".$account_id."'";
		}
		if($admission_id >0){
		$BSQL.= " AND admission_id ='".$admission_id."'";
		}
		if($bill_id >0){
		$BSQL.= " AND bill_id='".$bill_id."'";
		}
		$BSQL.= " AND paid_amount < net_bill_amount AND due_amount >0 ORDER BY billing_date ASC"; 
		$query = $this->db->query($BSQL); //echo $BSQL;
		$BNum  = $query->num_rows();
		
		if($BNum >0){
			foreach($query->result() as $irow){
				$bill_id 		= $irow->bill_id;
				$session_id 	= $irow->session_id;
				$period_id		= $irow->period_id;
				$invoice_id 	= $irow->invoice_id;
				$net_payble 	= $irow->net_bill_amount;
				$paid_amount 	= $irow->paid_amount;
				$existing_due 	= $irow->due_amount;
				$total_paid	= 0; $adjustAmount=0; $present_due=0;
				if(($DrAmount >= $existing_due) && ($existing_due >0)){
				  $DrAmount  = $DrAmount - $existing_due;
				  $total_paid= ($paid_amount + $existing_due); 
				  $BUP  = "UPDATE ".BILL_MASTER_TBL." ";
				  if($including_vat >0){
				  	$BUP.= " SET vat_paid=1,";
				  }elseif($headtypes==6){
					if($dr_account == $this->session->userdata('vat_head')){
					 $BUP.= "SET vat_paid=1,";
					}else{
					 $BUP.= "SET ";
					}
				  }else{
					$BUP.= "SET ";
				  }
				  $BUP.="paid_amount='$total_paid',due_amount=0,status=4 WHERE bill_id='$bill_id'";
				  if($BUP !=""){
				     $this->db->query($BUP); 
				  } $BUP="";
				  $this->saveInvoiceAdjustHistory($contra_id,$bill_id,$dr_account,BILL_MASTER_TBL,$invoice_id,$existing_due,"+",$including_vat);	
					 
				}elseif(($DrAmount < $existing_due) && ($DrAmount >0)){
				  if($existing_due >0){
				     $total_paid   = ($paid_amount + $DrAmount); 
				     $present_due  = ($existing_due - $DrAmount);
				     $adjustAmount = $DrAmount; $DrAmount = 0;
				     $BUP = "UPDATE ".BILL_MASTER_TBL." ";
				     if($including_vat >0){
					   $BUP.= " SET vat_paid=1,";
				     }elseif($headtypes==6){
						if($dr_account == $this->session->userdata('vat_head')){
						 $BUP.= "SET vat_paid=1,";
						}else{
						 $BUP.= "SET ";
						}
				     }else{
					$BUP.= "SET ";
				     }
				     $BUP.="paid_amount='$total_paid', due_amount='$present_due' WHERE bill_id =$bill_id";			     
					 if($BUP !=""){
				     $this->db->query($BUP);
				     } $BUP="";
				     $this->saveInvoiceAdjustHistory($contra_id,$bill_id,$dr_account,BILL_MASTER_TBL,$invoice_id,$adjustAmount,"+",$including_vat);
				  }//end existing due >0
				  break;
				}//end else if DrAmount < existing_due
				$MSQL= "UPDATE ".VOUCHER_MASTER_TBL." SET session_id='".$session_id."',period_id='".$period_id."' WHERE contra_id = ".$contra_id;
			    $this->db->query($MSQL);
			} // END foreach
		}// END BNum >0
	} // END DrAmount>0
    }
    
    function rollbackBillPayment($contra_id,$bill_id){
		$BSQL	= "SELECT * FROM ".BILL_ADJUST_HISTORY_TBL." WHERE contra_id = '".$contra_id."' AND bill_id='".$bill_id."'";
		$query = $this->db->query($BSQL);
		$BNum  = $query->num_rows();
		$due_amount = 0;
		if($BNum >0){
			foreach($query->result() as $row){
			$bill_id 	= $row->bill_id; 
			$dr_account 	= $row->dr_account; 
			$adjust_tbl 	= $row->adjust_tbl; 
			$adjust_ref 	= $row->adjust_ref;  
			$adjust_amount  = $row->adjust_amount; 
			$adjust_type	= $row->adjust_type; 
			$including_vat	= $row->including_vat;
			$headtypes      = $this->getHeadType($dr_account);
			//======= rollback previous adjust bill amount =====
			if($adjust_tbl=="bill_master" && $adjust_type=="+"){			 
				$HSql= "SELECT * FROM ".BILL_MASTER_TBL." WHERE bill_id = '".$bill_id."' AND bill_no ='".$adjust_ref."'";
				$hquery 	= $this->db->query($HSql);
				$srow   	= $hquery->row();
				$paid_amount 	= ($srow->paid_amount-$adjust_amount);
				$due_amount 	= ($srow->due_amount+$adjust_amount); 
				$Usql="UPDATE ".BILL_MASTER_TBL." ";
				if($including_vat >0){
					$Usql.= " SET vat_paid=0,";
				}elseif($headtypes==6){
					if($dr_account == $this->session->userdata('vat_head')){
					 $Usql.= "SET vat_paid=0,";
					}else{
					 $Usql.= "SET ";
					}
				}else{
					$Usql.= "SET ";
				}
				$Usql.="paid_amount='$paid_amount',due_amount='$due_amount' WHERE bill_id='".$bill_id."' AND bill_no ='".$adjust_ref."'";
				$this->db->query($Usql);
			} // End if adjust_tbl
			$HDsql="DELETE FROM ".BILL_ADJUST_HISTORY_TBL." WHERE bill_id='".$bill_id."' AND adjust_ref='".$adjust_ref."'";
			$this->db->query($HDsql);

			}// End foreach
		} // End if
    }
    function saveInvoiceAdjustHistory($contra_id,$bill_id,$dr_account,$adjust_tbl,$adjust_ref,$adjust_amount,$adjust_type,$including_vat){
	 $sql = "INSERT INTO ".BILL_ADJUST_HISTORY_TBL." (contra_id,bill_id,dr_account,adjust_tbl,adjust_ref,adjust_amount,adjust_type,including_vat)
	 VALUES('".$contra_id."','".$bill_id."','".$dr_account."','".$adjust_tbl."','".$adjust_ref."','".$adjust_amount."','".$adjust_type."','".$including_vat."')";
	 $this->db->query($sql);
   }
   function getVoucherID($invoice_date){
		$invoice_id = "";
		$INVArr     = explode("-",$invoice_date);
		$INVSL      = $this->getNextVoucherNo($INVArr[0],$INVArr[1]);
		$invoice_id = $INVArr[0]."/".$INVArr[1]."/".$INVSL;
		return $invoice_id;	
    }
    function getNextVoucherNo($year,$month){
		$INVSL=""; $INVNo="";		
		if ($year !="" && $month !="") {
			$SQL = "SELECT COUNT(voucher_no) AS voucher_no FROM ".VOUCHER_MASTER_TBL." WHERE YEAR(voucher_date)='".$year."' AND MONTH(voucher_date)='".$month."'";
			$query = $this->db->query($SQL);				
			$INVNo = $query->row()->voucher_no+1;
			if($INVNo <10){
			$INVSL = "000".$INVNo;	
			}elseif($INVNo <100){
			$INVSL = "00".$INVNo;	
			}elseif($INVNo <1000){
			$INVSL = "0".$INVNo;	
			}else{
			$INVSL = $INVNo;	
			}
		}else{
			$INVSL="0001";
		}
		return $INVSL;
    }
    function GetAjaxInvoiceList($customer_id){	
		$invoice_id = $this->input->post('invoice-id');
		
		$sql = "SELECT b.bill_id, b.bill_no, p.period_name_en as period_name, p.period_year FROM ".BILL_MASTER_TBL." as b, ".PERIOD_TBL." as p  WHERE b.billing_month=p.period_id AND b.status <= 5";
		if($customer_id !=""){
			$sql.= " AND b.account_id =$customer_id";
		}
		if($invoice_id >0){
			$sql.= " AND b.due_amount >=0";
		}else{
			$sql.= " AND b.due_amount >0";
		}
		$query 	= $this->db->query($sql); //echo $sql;
  		
		$options = "<option value=''>Select Invoice No</option>";
		foreach($query->result() as $irow){	
			if($invoice_id!="" && $invoice_id==$irow->bill_id){ $selected = "selected='selected'"; }else{ $selected = ""; }
			$options.="<option value='".$irow->bill_id."' $selected>".$irow->bill_no." ".$irow->period_name." ".$irow->period_year."</option>";
		}
		$options.="</select>";
		return $options;
    }
	
    function GetAjaxPaymentInvoiceList($customer_id,$invoice_id=0,$bill_type=0){	
		if($invoice_id==0){ $invoice_id = $this->input->post('invoice-id');}
		
		$sql = "SELECT b.bill_id, b.bill_no, p.period_name_en as period_name, p.period_year FROM ".BILL_MASTER_TBL." as b, ".PERIOD_TBL." as p  WHERE b.billing_month=p.period_id AND b.status < 5";
		if($customer_id !=""){
			$sql.= " AND b.account_id =$customer_id";
		}
		if($bill_type >0){
			$sql.= " AND b.bill_type IN($bill_type)";
		}
		if($invoice_id >0){
			$sql.= " AND b.due_amount >=0";
		}else{
			$sql.= " AND b.due_amount >0";
		}
		$query 	= $this->db->query($sql); //echo $sql;
  		
		$options = "<option value=''>Select Invoice No</option>";
		foreach($query->result() as $irow){	
			if($invoice_id!="" && $invoice_id==$irow->bill_id){ $selected = "selected='selected'"; }else{ $selected = ""; }
			$options.="<option value='".$irow->bill_id."' $selected>".$irow->bill_no." ".$irow->period_name." ".$irow->period_year."</option>";
		}
		$options.="</select>";
		return $options;
    }
    function getAjaxInvoiceInfo(){
        $bill_id = $this->input->post('invoice-no');
        $this->db->select('*');
        $this->db->from(BILL_MASTER_TBL);
        $this->db->where('bill_id', $bill_id);
        $query = $this->db->get();
        return $query->row(); 
    }
    function getHeadType($account_id){
        $this->db->select('head_type');
        $this->db->from(ACC_HEAD_TBL);
        $this->db->where('account_id', $account_id);
        $query = $this->db->get();
        return $query->row()->head_type; 
    }
    //======== Start Draft =======
    
    function saveCVDetails(){
		$details_id		= $this->input->post('details-id');
		if(empty($details_id)){ $details_id = 0;}		    
		$contra_id		= $this->input->post('contra-id');
		if(empty($contra_id)){ 
		$contra_id=0; $voucher_no = 0; $status = 0;
		}else{
		$status	= $this->input->post('status'); if(empty($status)){ $status = 1;}
		}	
		$bill_id	= $this->input->post('invoice-no');
		if(empty($bill_id)){ $bill_id=0;}	
			$mode_of_payment= $this->input->post('payment-mode');
			$voucher_date   = $this->formatDate($this->input->post('voucher-date'));
			$voucher_no	= $this->input->post('voucher-no');
		if(empty($voucher_no)){			
			$voucher_no 	= 0; $status = 0;
		}else{$status = 1;}
		$bank_name	= $this->input->post('bank-name');
		$branch_name= $this->input->post('branch-name');
		$acc_no		= $this->input->post('acc-no');
		$cheque_no	= $this->input->post('cheque-no');
		$cheque_type= $this->input->post('cheque-type');
		$issue_date	= $this->formatDate($this->input->post('issue-date'));		
		$dr_account	= $this->input->post('dr-account');
		$cr_account	= $this->input->post('cr-account');
		$others_income  = $this->input->post('others-income');
		if(empty($others_income)){ $others_income= 0;}
		$others_payment  = $this->input->post('others-payment');
		if(empty($others_payment)){ $others_payment= 0;}
		$admission_id = $this->input->post('cr-account');
		$dr_amount 	= $this->input->post('received-amount');
		$cr_amount 	= $this->input->post('received-amount');
		$amount 	= round($dr_amount,0,PHP_ROUND_HALF_UP);
		$voucher_type	= $this->input->post('voucher-type'); // 1=Payment, 2=Received, 3=Expense, 4=Journal		
		$description	= $this->input->post('naration');		
		$receive_note	= $this->input->post('receive-note');
		$including_vat	= $this->input->post('including-vat');	
		if(empty($including_vat)){ $including_vat=0;}
		$advance_collect= $this->input->post('advance-collect');
		if(empty($advance_collect)){ $advance_collect=0;}
		if($bill_id >0){ $advance_collect=0;}	
		$created_by	= $this->session->userdata('created_by');
		$CQType	=""; $cheque_details="";
		if($mode_of_payment==2){
			if($cheque_type==1){
			$CQType ="Cash Cheque";
			}elseif($cheque_type==2){
			$CQType ="A/C Payee Cheque";
			}elseif($cheque_type==3){
			$CQType ="Bearer Cheque";
			}elseif($cheque_type==4){
			$CQType ="Pay Order";
			}elseif($cheque_type==5){
			$CQType ="Bank Transfer";
			}
			$cheque_details = $bank_name.", ".$branch_name."<br>A/C No. ".$acc_no.", C/Q No. ".$cheque_no."<br>Issue Date: ".$issue_date.", C/Q Type: ".$CQType;
		}// End if mode_of_payment

		$DNum = 0;
		$DrSQL= "SELECT * FROM ".VOUCHER_DETAILS_TBL." WHERE contra_id = '".$contra_id."' AND voucher_no = '".$voucher_no."' AND headtypes='Dr' AND status=".$status." AND created_by='".$created_by."'";
		$dquery = $this->db->query($DrSQL);
		$DNum   = $dquery->num_rows();
		//==== Dr Account =====
		if($DNum==0){	
		 $DSQL="INSERT INTO ".VOUCHER_DETAILS_TBL."(contra_id,invoice_no,voucher_no,headtypes,account_id,amount,hrm_voucher_type,description,receive_note,status,hrm_created_by) ";
		 $DSQL.="VALUES('".$contra_id."','".$bill_id."','".$voucher_no."','Dr','".$dr_account."','".$dr_amount."','".$voucher_type."','".$description."','".$receive_note."','".$status."','".$created_by."')";
		 $this->db->query($DSQL);
		}
		
		if($details_id ==0){	
			 if($mode_of_payment==2){
			 //==== Cr Account =====
			 $CSQL="INSERT INTO ".VOUCHER_DETAILS_TBL."(contra_id,invoice_no,admission_id,voucher_no,headtypes,account_id,amount,bank_name,branch_name,acc_no,cheque_no,cheque_issue_date,hrm_cheque_type,hrm_voucher_type,description,including_vat,advance_collect,receive_note,status,hrm_created_by) ";
			 $CSQL.="VALUES('".$contra_id."','".$bill_id."','".$admission_id."','".$voucher_no."','Cr','".$cr_account."','".$cr_amount."','".$bank_name."','".$branch_name."','".$acc_no."','".$cheque_no."','".$issue_date."','".$cheque_type."','".$voucher_type."','".$description."','".$including_vat."','".$advance_collect."','".$receive_note."','".$status."','".$created_by."')";
			 $this->db->query($CSQL);		 
			 }else{
			 //==== Cr Account =====
			 $CSQL="INSERT INTO ".VOUCHER_DETAILS_TBL."(contra_id,invoice_no,admission_id,voucher_no,headtypes,account_id,amount,hrm_voucher_type,description,including_vat,advance_collect,receive_note,status,hrm_created_by) ";
			 $CSQL.="VALUES('".$contra_id."','".$bill_id."','".$admission_id."','".$voucher_no."','Cr','".$cr_account."','".$cr_amount."','".$voucher_type."','".$description."','".$including_vat."','".$advance_collect."','".$receive_note."','".$status."','".$created_by."')";
			 $this->db->query($CSQL);
			 }//end if mode_of_payment ==2
		}else{	
			if($mode_of_payment==2){
			//==== Cr Account Update =====				
			$CSQL= "UPDATE ".VOUCHER_DETAILS_TBL." SET invoice_no ='".$bill_id."', admission_id ='".$admission_id."', amount='".$cr_amount."', bank_name='".$bank_name."', branch_name='".$branch_name."', acc_no='".$acc_no."', cheque_no='".$cheque_no."', cheque_issue_date='".$issue_date."', hrm_cheque_type='".$cheque_type."', hrm_voucher_type='".$voucher_type."', description='".$description."', including_vat='".$including_vat."', advance_collect='".$advance_collect."', receive_note='".$receive_note."', status='".$status."' WHERE details_id='".$details_id."' AND account_id='".$cr_account."' AND contra_id = ".$contra_id;
			$this->db->query($CSQL);
			}else{
			//==== Cr Account Update =====				
			$CSQL= "UPDATE ".VOUCHER_DETAILS_TBL." SET invoice_no ='".$bill_id."', admission_id ='".$admission_id."', amount='".$cr_amount."', hrm_voucher_type='".$voucher_type."', description='".$description."', including_vat='".$including_vat."', advance_collect='".$advance_collect."', receive_note='".$receive_note."', status='".$status."' WHERE details_id='".$details_id."' AND account_id='".$cr_account."' AND contra_id = ".$contra_id;
			$this->db->query($CSQL);
			}
			//===== Update Dr A/C ====
			if($voucher_type==1){
				//==== Cr Account Update =====				
			    $DSQL= "UPDATE ".VOUCHER_DETAILS_TBL." SET invoice_no ='".$bill_id."', admission_id ='".$admission_id."', amount='".$cr_amount."', description='".$description."', including_vat='".$including_vat."', advance_collect='".$advance_collect."', receive_note='".$receive_note."', status='".$status."' WHERE  headtypes='Dr' AND account_id='".$dr_account."' AND contra_id = ".$contra_id;
			    $this->db->query($DSQL);
			}
		}
		
    }
	
	function SaveBkashReceivedVoucher($bKashId,$bill_id,$bill_amount,$bill_month,$bill_year,$TrxID){
		$institute_id	= $this->session->userdata('company_id');
		$branch_id	    = $this->session->userdata('branch_id');
		$session_id		= $this->session->userdata('sessions_id');
		$version_id		= $this->session->userdata('version_id');
		$class_id	    = $this->session->userdata('class_id');
		$group_id	    = $this->session->userdata('group_id');
		$shift_id	    = $this->session->userdata('shift_id');
		$section_id	    = $this->session->userdata('section_id');
		$admission_id	= $this->session->userdata('admission_id');	
		$cr_account		= $this->session->userdata('user_ref_id');	
		$dr_account		= $bKashId;
		$mode_of_payment= 4; // bKash
		$invoice_nos   = $bill_id;
		$voucher_date   = date("Y-m-d");
		$voucher_type	= 2; // 1=Payment, 2=Received, 3=Expense, 4=Journal
		$others_income	= 0;
		$dr_amount 		= $bill_amount;
		$cr_amount 		= $bill_amount;
		$including_vat  = 0;
		$PSQL ="SELECT period_name_en as period_name, period_name_bn as period_name_bn FROM ".PERIOD_TBL." WHERE institute_id = $institute_id AND branch_id = $branch_id AND period_no=$bill_month";
		$PRES = $this->db->query($PSQL);
		if($PRES->num_rows() >0){
			$period_name = $PRES->row()->period_name;
		}else{
			$period_name = "$bill_month/$bill_year";
		}		
		$receive_note   = "bKash Transaction ID - ".$TrxID; $cheque_details="bKash Transaction ID - ".$TrxID;
		$description    = "Received Tuition & Fees by bKash month of ".$period_name." - ".$bill_year;
		$voucher_no 	= $this->getVoucherID($voucher_date);
		$SQL= "INSERT INTO ".VOUCHER_MASTER_TBL."(invoice_no,institute_id,branch_id,session_id,version_id,class_id,group_id,admission_id,account_id,voucher_no,voucher_date,dr_amount,cr_amount,mode_of_payment,voucher_type,description,others_income,created_by) ";
		$SQL.="VALUES('".$invoice_nos."','".$institute_id."','".$branch_id."','".$session_id."','".$version_id."','".$class_id."','".$group_id."','".$admission_id."','".$cr_account."','".$voucher_no."','".$voucher_date."','".$dr_amount."','".$cr_amount."','".$mode_of_payment."','".$voucher_type."','".$description."','".$others_income."','".$created_by."')";
		if($voucher_no !=""){
			$this->db->query($SQL);
			$contra_id = $this->db->insert_id(); $status = 1;			
			if($contra_id >0){
				 if($invoice_nos >0){
					$this->AdjustToInvoice($dr_account,$cr_account,$contra_id,$bill_id,$cr_amount,$including_vat,$admission_id);			
				 }//End if
				
				 //==== Dr Account =====
				 $DSQL="INSERT INTO ".VOUCHER_DETAILS_TBL."(contra_id,invoice_no, voucher_no,headtypes,account_id,amount,voucher_type,description,receive_note,status,created_by) ";
				 $DSQL.="VALUES('".$contra_id."','".$bill_id."','".$voucher_no."','Dr','".$dr_account."','".$dr_amount."','".$voucher_type."','".$description."','".$receive_note."','".$status."','".$created_by."')";
				 $this->db->query($DSQL);
				
				 //==== Cr Account =====
				 $CSQL="INSERT INTO ".VOUCHER_DETAILS_TBL."(contra_id,invoice_no, admission_id, voucher_no,headtypes,account_id,amount,voucher_type,description,including_vat,advance_collect,receive_note,status,created_by) ";
				 $CSQL.="VALUES('".$contra_id."','".$bill_id."','".$admission_id."','".$voucher_no."','Cr','".$cr_account."','".$cr_amount."','".$voucher_type."','".$description."','".$including_vat."','".$advance_collect."','".$receive_note."','".$status."','".$created_by."')";
				 $this->db->query($CSQL);
			 
				 //==== Dr Ledger Insert =======			
				 $this->SaveAccountLedger($voucher_no,$invoice_nos,$voucher_date,$dr_account,$voucher_type,$description,$dr_amount,"Dr","I",$cheque_details);
			
				 //==== Cr Ledger Insert =======	
				 $this->SaveAccountLedger($voucher_no,$invoice_nos,$voucher_date,$cr_account,$voucher_type,$description,$cr_amount,"Cr","I",$cheque_details);
			}//end if contra_id >0
			return $contra_id;
			//print  $this->db->last_query();
		}//End if voucher_no
					
		//$this->db->trans_complete();
		//if($this->db->trans_status() === FALSE)
		if ($this->db->affected_rows() > 0)
		{
			return true;
		}else{
			return false;	
		}
	}
	function SaveBkashApplicationReceivedVoucher($bKashId,$bill_id,$bill_amount,$bill_month,$bill_year,$TrxID,$institute_id,$branch_id,$session_id,$version_id){
		$cr_account		= 40; // Accounts Receivable	
		$dr_account		= $bKashId;
		$mode_of_payment= 4; // bKash
		$invoice_nos    = $bill_id;
		$application_id = $bill_id;
		$voucher_date   = date("Y-m-d");
		$voucher_type	= 2; // 1=Payment, 2=Received, 3=Expense, 4=Journal
		$others_income	= 1; 
		$others_source  = 1; // 1=Online Application,
		$dr_amount 		= $bill_amount;
		$cr_amount 		= $bill_amount;
		$including_vat  = 0;
		$PSQL ="SELECT * FROM ".APPLICATION_TBL." WHERE institute_id = $institute_id AND branch_id = $branch_id AND session_id = $session_id AND version_id = $version_id AND application_id=$application_id";
		$PRES = $this->db->query($PSQL);
		if($PRES->num_rows() >0){
			$class_id   = $PRES->row()->class_id;
			$group_id   = $PRES->row()->group_id;
			$shift_id   = $PRES->row()->shift_id;
			$section_id = $PRES->row()->section_id;
		}
		$period_name = "$bill_month/$bill_year";
		
		$receive_note   = "bKash Transaction ID - ".$TrxID; $cheque_details="bKash Transaction ID - ".$TrxID;
		$description    = "Received online admission application charge by bKash month of ".$period_name." - ".$bill_year;
		$voucher_no 	= $this->getVoucherID($voucher_date);
		$SQL= "INSERT INTO ".VOUCHER_MASTER_TBL."(invoice_no,institute_id,branch_id,session_id,version_id,class_id,group_id,admission_id,account_id,voucher_no,voucher_date,dr_amount,cr_amount,mode_of_payment,voucher_type,description,others_income,others_source,created_by) ";
		$SQL.="VALUES('".$invoice_nos."','".$institute_id."','".$branch_id."','".$session_id."','".$version_id."','".$class_id."','".$group_id."','".$application_id."','".$cr_account."','".$voucher_no."','".$voucher_date."','".$dr_amount."','".$cr_amount."','".$mode_of_payment."','".$voucher_type."','".$description."','".$others_income."','".$others_source."','".$created_by."')";
		
		if($voucher_no !=""){
			$this->db->query($SQL);
			$contra_id = $this->db->insert_id(); $status = 1;			
			if($contra_id >0){
				 //==== Dr Account =====
				 $DSQL="INSERT INTO ".VOUCHER_DETAILS_TBL."(contra_id,invoice_no, voucher_no,headtypes,account_id,amount,voucher_type,description,receive_note,status,created_by) ";
				 $DSQL.="VALUES('".$contra_id."','".$bill_id."','".$voucher_no."','Dr','".$dr_account."','".$dr_amount."','".$voucher_type."','".$description."','".$receive_note."','".$status."','".$created_by."')";
				 $this->db->query($DSQL);
				
				 //==== Cr Account =====
				 $CSQL="INSERT INTO ".VOUCHER_DETAILS_TBL."(contra_id,invoice_no, admission_id, voucher_no,headtypes,account_id,amount,voucher_type,description,including_vat,advance_collect,receive_note,status,created_by) ";
				 $CSQL.="VALUES('".$contra_id."','".$bill_id."','".$application_id."','".$voucher_no."','Cr','".$cr_account."','".$cr_amount."','".$voucher_type."','".$description."','".$including_vat."','".$advance_collect."','".$receive_note."','".$status."','".$created_by."')";
				 $this->db->query($CSQL);
			 
				 //==== Dr Ledger Insert =======			
				 $this->SaveOthersAccountLedger($voucher_no,$invoice_nos,$voucher_date,$dr_account,$voucher_type,$description,$dr_amount,"Dr","I",$cheque_details,$others_income,$others_source);
			
				 //==== Cr Ledger Insert =======	
				 $this->SaveOthersAccountLedger($voucher_no,$invoice_nos,$voucher_date,$cr_account,$voucher_type,$description,$cr_amount,"Cr","I",$cheque_details,$others_income,$others_source);
			}//end if contra_id >0
			//print  $this->db->last_query();
			return $contra_id;
			
		}//End if voucher_no
					
		//$this->db->trans_complete();
		//if($this->db->trans_status() === FALSE)
		if ($this->db->affected_rows() > 0)
		{
			return true;
		}else{
			return false;	
		}
	}
    function saveReceivedCVMaster(){			    
		$contra_id		= $this->input->post('contra-id');
		if(empty($contra_id)){ 
		$contra_id=0; $voucher_no = 0; $status = 0;
		}else{
		$status	= $this->input->post('status'); if(empty($status)){ $status = 1;}
		}	
		$bill_id	= $this->input->post('invoice-no');
		if(empty($bill_id)){ $bill_id=0;}	
			$mode_of_payment= $this->input->post('payment-mode');
			$voucher_date   = $this->formatDate($this->input->post('voucher-date'));
			$voucher_no	= $this->input->post('voucher-no');
		if(empty($voucher_no)){			
			$voucher_no 	= $this->getVoucherID($voucher_date);
		if(empty($status)){ $status = 1;}
		}
		
		$others_income  = $this->input->post('others-income');
		if(empty($others_income)){ $others_income= 0;}
		
		$institute_id	= $this->input->post('institute-id');
		$branch_id		= $this->input->post('branch-id');
		$session_id		= $this->input->post('session-id');
		$version_id		= $this->input->post('version-id');
		$class_id		= $this->input->post('class-id');
		$group_id		= $this->input->post('group-id');
		$admission_id	= $this->input->post('cr-account');
		$bank_name		= $this->input->post('bank-name');
		$branch_name	= $this->input->post('branch-name');
		$acc_no			= $this->input->post('acc-no');
		$cheque_no		= $this->input->post('cheque-no');
		$cheque_type	= $this->input->post('cheque-type');
		$issue_date		= $this->formatDate($this->input->post('issue-date'));		
		$dr_account		= $this->input->post('dr-account');
		$cr_account		= $this->input->post('cr-account');
		if($others_income==0){
			$session_id=0;$version_id=0;$class_id=0;$group_id=0;
			$admission_id =$this->input->post('midea-id');
		}else{
			$session_id=0;$version_id=0;$class_id=0;$group_id=0;
			$admission_id =$this->input->post('midea-id');
		}
		$dr_amount 		= $this->input->post('received-amount');
		$dr_amount 		= round($dr_amount,0,PHP_ROUND_HALF_UP);
		$cr_amount 		= $this->input->post('received-amount');
		$cr_amount 		= round($cr_amount,0,PHP_ROUND_HALF_UP);
		$voucher_type	= $this->input->post('voucher-type'); // 1=Payment, 2=Received, 3=Expense, 4=Journal
		$description 	= str_replace("U 0026", '&', $this->input->post('naration'));
		$description	= $this->db->escape_str($description);
		$including_vat	= $this->input->post('including-vat');	
		$created_by	= $this->session->userdata('created_by');
		$CQType	=""; $cheque_details="";
		if($mode_of_payment==2){
			if($cheque_type==1){
			$CQType ="Cash Cheque";
			}elseif($cheque_type==2){
			$CQType ="A/C Payee Cheque";
			}elseif($cheque_type==3){
			$CQType ="Bearer Cheque";
			}elseif($cheque_type==4){
			$CQType ="Pay Order";
			}elseif($cheque_type==5){
			$CQType ="Bank Transfer";
			}
			$cheque_details = $bank_name.", ".$branch_name."<br>A/C No. ".$acc_no.", C/Q No. ".$cheque_no."<br>Issue Date: ".$issue_date.", C/Q Type: ".$CQType;
		}// End if mode_of_payment
		
		if($contra_id >0){
			//$this->db->trans_start();
			$invoice_nos=""; 
			$CrSQL	= "SELECT * FROM ".VOUCHER_DETAILS_TBL." WHERE contra_id = '".$contra_id."' AND voucher_no = '".$voucher_no."' AND headtypes='Cr' AND invoice_no >0 AND status = $status";
			$cquery    = $this->db->query($CrSQL);
			if($cquery->num_rows() >0){
				foreach($cquery->result() as $row){
				  if($row->invoice_no >0){
				   $invoice_nos.=$row->invoice_no.",";
				  }//End if
				}//End foreach
				if($invoice_nos !=""){
				  $invoice_nos = substr($invoice_nos, 0, -1);
				}else{ $invoice_nos =0;}//End if
			}else{$invoice_nos=0;}
				
			//=== Update Master =====
			$MSQL= "UPDATE ".VOUCHER_MASTER_TBL." SET invoice_no='".$invoice_nos."',institute_id=$institute_id,branch_id=$branch_id,session_id=$session_id,version_id=$version_id,class_id=$class_id,group_id=$group_id,admission_id='".$admission_id."', dr_amount='".$dr_amount."',cr_amount='".$cr_amount."', mode_of_payment='".$mode_of_payment."', voucher_type='".$voucher_type."', description='".$description."', others_income='".$others_income."' WHERE contra_id = ".$contra_id;
			$this->db->query($MSQL);

			$DrSQL= "UPDATE ".VOUCHER_DETAILS_TBL." SET invoice_no='".$invoice_nos."', amount=".$dr_amount." WHERE contra_id=".$contra_id." AND headtypes='Dr' AND account_id=".$dr_account;
			$this->db->query($DrSQL);
			//==== Adjust Invoice Using cr query (CrSQL)=====
			if($cquery->num_rows() >0){ // advance: 1=Yes, 0=No
				foreach($cquery->result() as $row){
				  if($row->invoice_no >0 && $row->advance_collect==0){
				  $bill_id =$row->invoice_no;
				  $including_vat =$row->including_vat;
				  $amount  =$row->amount;
				  $this->rollbackBillPayment($contra_id,$bill_id);
				  $this->AdjustToInvoice($dr_account,$cr_account,$contra_id,$bill_id,$amount,$including_vat,$admission_id);
				  }//End if
				}//End foreach			
			}//End if				
			//==== Dr Ledger Update =======			
			$this->SaveAccountLedger($voucher_no,$invoice_nos,$voucher_date,$dr_account,$voucher_type,$description,$dr_amount,"Dr","U",$cheque_details);
			
			//==== Cr Ledger Update =======	
			$this->SaveAccountLedger($voucher_no,$invoice_nos,$voucher_date,$cr_account,$voucher_type,$description,$cr_amount,"Cr","U",$cheque_details);		
			
			//$this->db->trans_complete();
			//if($this->db->trans_status() === FALSE)
			if ($this->db->affected_rows() > 0)
			{
				return false;
			}else{
				return true;	
			}
			
		}else{
			//$this->db->trans_start();
			$invoice_nos="";
			$CrSQL	= "SELECT * FROM ".VOUCHER_DETAILS_TBL." WHERE contra_id = '0' AND voucher_no = '0' AND headtypes='Cr' AND invoice_no >0 AND status=0 AND created_by='".$created_by."'";
			$cquery = $this->db->query($CrSQL);
			if($cquery->num_rows() >0){
				foreach($cquery->result() as $row){
				  if($row->invoice_no >0){
				   $invoice_nos.=$row->invoice_no.",";
				  }//End if
				}//End foreach
				if($invoice_nos !=""){
				$invoice_nos = substr($invoice_nos, 0, -1);
				}else{ $invoice_nos =0;}//End if invoice_nos
			}else{$invoice_nos=0;} 
			
			$SQL= "INSERT INTO ".VOUCHER_MASTER_TBL."(invoice_no,institute_id,branch_id,session_id,version_id,class_id,group_id,admission_id,account_id,voucher_no,voucher_date,dr_amount,cr_amount,mode_of_payment,voucher_type,description,others_income,created_by) ";
			$SQL.="VALUES('".$invoice_nos."','".$institute_id."','".$branch_id."','".$session_id."','".$version_id."','".$class_id."','".$group_id."','".$admission_id."','".$cr_account."','".$voucher_no."','".$voucher_date."','".$dr_amount."','".$cr_amount."','".$mode_of_payment."','".$voucher_type."','".$description."','".$others_income."','".$created_by."')";
			if($voucher_no !=""){
				$this->db->query($SQL);
				$contra_id = $this->db->insert_id(); $status = 1;			
				if($contra_id >0){
				if($cquery->num_rows() >0){
					foreach($cquery->result() as $row){
					if($row->invoice_no >0 && $row->advance_collect==0){ // advance: 1=Yes, 0=No
					$bill_id =$row->invoice_no;
					$including_vat =$row->including_vat;
					$amount  =$row->amount;
					$this->AdjustToInvoice($dr_account,$cr_account,$contra_id,$bill_id,$amount,$including_vat,$admission_id);
					}//End if
					$amount=0;
					}//End foreach			
				}//End if
				$DrSQL= "UPDATE ".VOUCHER_DETAILS_TBL." SET amount='".$dr_amount."',invoice_no='".$invoice_nos."' WHERE contra_id='0' AND headtypes='Dr' AND voucher_no = '0' AND created_by='".$created_by."'";
				$this->db->query($DrSQL);
				
				$CrSQL= "UPDATE ".VOUCHER_DETAILS_TBL." SET contra_id='".$contra_id."', admission_id='".$admission_id."', voucher_no='".$voucher_no."', status='".$status."' WHERE contra_id='0' AND voucher_no = '0' AND created_by='".$created_by."'";
				$this->db->query($CrSQL); 
				//==== Dr Ledger Insert =======			
				$this->SaveAccountLedger($voucher_no,$invoice_nos,$voucher_date,$dr_account,$voucher_type,$description,$dr_amount,"Dr","I",$cheque_details);
			
				//==== Dr Ledger Insert =======	
				$this->SaveAccountLedger($voucher_no,$invoice_nos,$voucher_date,$cr_account,$voucher_type,$description,$cr_amount,"Cr","I",$cheque_details);
				}//end if contra_id >0
				return $voucher_no;
				//print  $this->db->last_query();
			}//End if voucher_no
					
			//$this->db->trans_complete();
			//if($this->db->trans_status() === FALSE)
			if ($this->db->affected_rows() > 0)
			{
				return false;
			}else{
				return true;	
			}			
			
		}//End else	
    }
    //====== Save Payment Voucher ========
	function savePaymentCVMaster(){			    
		$contra_id		= $this->input->post('contra-id');
		if(empty($contra_id)){ 
		$contra_id=0; $voucher_no = 0; $status = 0;
		}else{
		$status	= $this->input->post('status'); if(empty($status)){ $status = 1;}
		}	
		$bill_id	= $this->input->post('invoice-no');
		if(empty($bill_id)){ $bill_id=0;}	
			$mode_of_payment= $this->input->post('payment-mode');
			$voucher_date   = $this->formatDate($this->input->post('voucher-date'));
			$voucher_no	= $this->input->post('voucher-no');
		if(empty($voucher_no)){			
			$voucher_no 	= $this->getVoucherID($voucher_date);
		if(empty($status)){ $status = 1;}
		}
		
		$others_payment  = $this->input->post('others-payment');
		if(empty($others_payment)){ $others_payment= 0;}
		$others_income  = 0;
		$institute_id	= $this->input->post('institute-id');
		$branch_id		= $this->input->post('branch-id');
		$session_id		= $this->input->post('session-id');
		$version_id		= $this->input->post('version-id');
		$class_id		= $this->input->post('class-id');
		$group_id		= $this->input->post('group-id');
		$admission_id	= $this->input->post('cr-account');
		$bank_name		= $this->input->post('bank-name');
		$branch_name	= $this->input->post('branch-name');
		$acc_no			= $this->input->post('acc-no');
		$cheque_no		= $this->input->post('cheque-no');
		$cheque_type	= $this->input->post('cheque-type');
		$issue_date		= $this->formatDate($this->input->post('issue-date'));		
		$dr_account		= $this->input->post('dr-account');
		$cr_account		= $this->input->post('cr-account');
		if(empty($class_id)){$class_id = 1;} if(empty($group_id)){$group_id = 1;} if(empty($admission_id)){$admission_id = 1;}
		
		if($others_payment >0){
			$asql = "SELECT session_id,version_id,class_id,group_id,admission_id, student_name_en as student_id FROM ".ADMISSION_TBL." WHERE admission_id = $cr_account AND status = 1";
			$aquery = $this->db->query($asql);			
			if($aquery->num_rows() >0){
				$session_id		= $aquery->row()->session_id;
				$version_id		= $aquery->row()->version_id;				
				$cr_account 	= $aquery->row()->student_id;
				$admission_id	= $aquery->row()->admission_id;
				$class_id		= $aquery->row()->class_id;
				$group_id		= $aquery->row()->group_id;
			}else{ $session_id=0;$version_id=0;$class_id=0;$group_id=0;$admission_id =0;}
		}else{
			$session_id=0;$version_id=0;$class_id=0;$group_id=0;$admission_id =0;
		}
		$dr_amount 		= $this->input->post('payment-amount');
		$dr_amount 		= round($dr_amount,0,PHP_ROUND_HALF_UP);
		$cr_amount 		= $this->input->post('payment-amount');
		$cr_amount 		= round($cr_amount,0,PHP_ROUND_HALF_UP);
		$voucher_type	= $this->input->post('voucher-type'); // 1=Payment, 2=Received, 3=Expense, 4=Journal
		$description 	= str_replace("U 0026", '&', $this->input->post('naration'));
		$description	= $this->db->escape_str($description);
		$including_vat	= $this->input->post('including-vat');
		$created_by	= $this->session->userdata('created_by');
		$CQType	=""; $cheque_details=""; if(empty($voucher_type)){ $voucher_type=1;}
		if($mode_of_payment==2){
			if($cheque_type==1){
			$CQType ="Cash Cheque";
			}elseif($cheque_type==2){
			$CQType ="A/C Payee Cheque";
			}elseif($cheque_type==3){
			$CQType ="Bearer Cheque";
			}elseif($cheque_type==4){
			$CQType ="Pay Order";
			}elseif($cheque_type==5){
			$CQType ="Bank Transfer";
			}
			$cheque_details = $bank_name.", ".$branch_name."<br>A/C No. ".$acc_no.", C/Q No. ".$cheque_no."<br>Issue Date: ".$issue_date.", C/Q Type: ".$CQType;
		}// End if mode_of_payment
		
		if($contra_id >0){
			//$this->db->trans_start();
			$invoice_nos=""; 
			$DrSQL	= "SELECT * FROM ".VOUCHER_DETAILS_TBL." WHERE contra_id = '".$contra_id."' AND voucher_no = '".$voucher_no."' AND headtypes='Dr' AND invoice_no >0 AND status = $status";
			$dquery    = $this->db->query($DrSQL);
			if($dquery->num_rows() >0){
				foreach($dquery->result() as $row){
				  if($row->invoice_no >0){
				   $invoice_nos.=$row->invoice_no.",";
				  }//End if
				}//End foreach
				if($invoice_nos !=""){
				  $invoice_nos = substr($invoice_nos, 0, -1);
				}else{ $invoice_nos =0;}//End if
			}else{$invoice_nos=0;}
			$invoice_nos = implode(',', array_unique(explode(',', $invoice_nos)));	
			//=== Update Master =====
			$MSQL= "UPDATE ".VOUCHER_MASTER_TBL." SET invoice_no='".$invoice_nos."',institute_id=$institute_id,branch_id=$branch_id,session_id=$session_id,version_id=$version_id,class_id=$class_id,group_id=$group_id, admission_id='".$admission_id."', dr_amount='".$dr_amount."',cr_amount='".$cr_amount."', mode_of_payment='".$mode_of_payment."', voucher_type='".$voucher_type."', description='".$description."', others_payment='".$others_payment."' WHERE contra_id = ".$contra_id;
			$this->db->query($MSQL);

			$CrSQL= "UPDATE ".VOUCHER_DETAILS_TBL." SET invoice_no='".$invoice_nos."', amount=".$dr_amount." WHERE contra_id=".$contra_id." AND headtypes='Cr' AND account_id=".$dr_account;
			$this->db->query($CrSQL);
			//==== Adjust Invoice Using cr query (DrSQL)=====
			if($dquery->num_rows() >0){ // advance: 1=Yes, 0=No
				foreach($dquery->result() as $row){
				  if($row->invoice_no >0 && $row->advance_collect==0){
				  $bill_id =$row->invoice_no;
				  $including_vat =$row->including_vat;
				  $amount  =$row->amount;
				  $this->rollbackBillPayment($contra_id,$bill_id);
				  $this->AdjustToInvoice($cr_account,$dr_account,$contra_id,$bill_id,$amount,$including_vat,$admission_id);
				  }//End if
				}//End foreach			
			}//End if
			$CVSQL	= "SELECT * FROM ".VOUCHER_DETAILS_TBL." WHERE contra_id = '".$contra_id."' AND voucher_no = '".$voucher_no."' AND headtypes='Cr' AND invoice_no >0 AND including_vat>0 AND status=$status";
			$cvquery = $this->db->query($CVSQL);
			if($cvquery->num_rows() >0){
				$including_vat = $cvquery->row()->including_vat;				
				$bill_no       = $cvquery->row()->invoice_no;
			}
			if($including_vat==1){
				$vat_head 	= $this->GetAccountId(6,11,41,17,6);
				$VATSQL		= "SELECT * FROM ".VOUCHER_DETAILS_TBL." WHERE invoice_no = '".$bill_no."' AND headtypes='Cr' AND account_id =$vat_head AND status = $status";
				$vquery    	= $this->db->query($VATSQL);
				if($vquery->num_rows() >0){
					$vat_amount = $vquery->row()->amount;
					$dr_amount  = $dr_amount - $vat_amount;
					//===== Insert Dr VAT Payable Account =====
					$vat_head = $this->GetAccountId(6,11,41,17,6);	
					if($vat_amount >0 && $vat_head >0){
					   $vat_description = "Cr VAT payable against purchase item";
					   $dcsql = "SELECT * FROM ".VOUCHER_DETAILS_TBL." WHERE account_id ='".$vat_head."' AND contra_id = $contra_id AND status ='1'";
					   $dquery = $this->db->query($dcsql);
					   if($dquery->num_rows() >0){			
					   $DSQL= "UPDATE ".VOUCHER_DETAILS_TBL." SET amount='".$vat_amount."', voucher_type='".$voucher_type."', description='".$vat_description."' WHERE account_id='".$vat_head."' AND contra_id = ".$contra_id; $this->db->query($DSQL);			
					   $this->SaveAccountLedger($voucher_no,$invoice_nos,$voucher_date,$vat_head,$voucher_type,$vat_description,$vat_amount,"Dr","U");
					   }else{
						//==== Start Dr VAT Payable Account =====					
						$DSQL="INSERT INTO ".VOUCHER_DETAILS_TBL."(contra_id,invoice_no,voucher_no,headtypes,account_id,amount,voucher_type,description,created_by) ";
						$DSQL.="VALUES('".$contra_id."','".$invoice_nos."','".$voucher_no."','Dr','".$vat_head."','".$vat_amount."','".$voucher_type."','".$vat_description."','".$created_by."')";
						$this->db->query($DSQL);
						$this->SaveAccountLedger($voucher_no,$invoice_nos,$voucher_date,$vat_head,$voucher_type,$vat_description,$vat_amount,"Dr","I");
					   }//end else
					}
					/*
					//===== Dr VAT Expense Account Update: for Import/Export =====
					$vatexp_head = $this->GetAccountId(6,10,29,24,9);	
					if($vat_amount >0 && $vatexp_head >0){
					   $vat_description = "Payment VAT expense against purchase item";
					   $vecsql = "SELECT * FROM ".VOUCHER_DETAILS_TBL." WHERE account_id ='".$vatexp_head."' AND contra_id = $contra_id AND status ='1'";
					   $vequery = $this->db->query($vecsql);
					   if($vequery->num_rows() >0){			
					   $VESQL= "UPDATE ".VOUCHER_DETAILS_TBL." SET amount='".$vat_amount."', voucher_type='".$voucher_type."', description='".$vat_description."' WHERE account_id='".$vatexp_head."' AND contra_id = ".$contra_id; $this->db->query($VESQL);			
					   $this->SaveAccountLedger($voucher_no,$invoice_nos,$voucher_date,$vatexp_head,$voucher_type,$vat_description,$vat_amount,"Dr","U");
					   }else{
						//==== Start Dr VAT Expense Account =====					
						$VESQL="INSERT INTO ".VOUCHER_DETAILS_TBL."(contra_id,invoice_no,voucher_no,headtypes,account_id,amount,voucher_type,description,created_by) ";
						$VESQL.="VALUES('".$contra_id."','".$invoice_nos."','".$voucher_no."','Dr','".$vatexp_head."','".$vat_amount."','".$voucher_type."','".$vat_description."','".$created_by."')";
						$this->db->query($VESQL);
						$this->SaveAccountLedger($voucher_no,$invoice_nos,$voucher_date,$vatexp_head,$voucher_type,$vat_description,$vat_amount,"Dr","I");
					   }//end else
					}
					*/
				}else{
					$vat_amount = 0;
				}
				if($dr_amount >0){
				//==== Dr Ledger Update =======
				$CrSQL= "UPDATE ".VOUCHER_DETAILS_TBL." SET amount=".$dr_amount." WHERE contra_id=".$contra_id." AND headtypes='Dr' AND account_id=".$dr_account;
				$this->db->query($CrSQL);
				$this->SaveAccountLedger($voucher_no,$invoice_nos,$voucher_date,$dr_account,$voucher_type,$description,$dr_amount,"Dr","U",$cheque_details);
				}
			}else{
				//==== Dr Ledger Update =======			
				$this->SaveAccountLedger($voucher_no,$invoice_nos,$voucher_date,$dr_account,$voucher_type,$description,$dr_amount,"Dr","U",$cheque_details);
			}
			//==== Cr Ledger Update =======	
			$this->SaveAccountLedger($voucher_no,$invoice_nos,$voucher_date,$cr_account,$voucher_type,$description,$cr_amount,"Cr","U",$cheque_details);		
			
			//$this->db->trans_complete();
			//if($this->db->trans_status() === FALSE)
			if ($this->db->affected_rows() > 0)
			{
				return false;
			}else{
				return true;	
			}
			
		}else{
			//$this->db->trans_start();
			$invoice_nos="";
			$DrSQL	= "SELECT * FROM ".VOUCHER_DETAILS_TBL." WHERE contra_id = '0' AND voucher_no = '0' AND headtypes='Dr' AND invoice_no >0 AND status=0 AND created_by='".$created_by."'";
			$dquery = $this->db->query($DrSQL);
			if($dquery->num_rows() >0){
				foreach($dquery->result() as $row){
				  if($row->invoice_no >0){
				   $invoice_nos.=$row->invoice_no.",";
				  }//End if
				}//End foreach
				if($invoice_nos !=""){
				$invoice_nos = substr($invoice_nos, 0, -1);
				}else{ $invoice_nos =0;}//End if invoice_nos
			}else{$invoice_nos=0;} 
			
			$invoice_nos = implode(',', array_unique(explode(',', $invoice_nos)));
			
			$SQL= "INSERT INTO ".VOUCHER_MASTER_TBL."(invoice_no,institute_id,branch_id,session_id,version_id,class_id,group_id,admission_id,account_id,voucher_no,voucher_date,dr_amount,cr_amount,mode_of_payment,voucher_type,description,others_income,created_by) ";
			$SQL.="VALUES('".$invoice_nos."','".$institute_id."','".$branch_id."','".$session_id."','".$version_id."','".$class_id."','".$group_id."','".$admission_id."','".$cr_account."','".$voucher_no."','".$voucher_date."','".$dr_amount."','".$cr_amount."','".$mode_of_payment."','".$voucher_type."','".$description."','".$others_income."','".$created_by."')";
			if($voucher_no !=""){
				$this->db->query($SQL);
				$contra_id = $this->db->insert_id(); $status = 1;			
				if($contra_id >0){
				if($dquery->num_rows() >0){
					foreach($dquery->result() as $row){
					if($row->invoice_no >0 && $row->advance_collect==0){ // advance: 1=Yes, 0=No
					$bill_id =$row->invoice_no;
					$including_vat =$row->including_vat;
					$amount  =$row->amount;
					$this->AdjustToInvoice($cr_account,$dr_account,$contra_id,$bill_id,$amount,$including_vat,$admission_id);
					}//End if
					$amount=0;
					}//End foreach			
				}//End if				
								
				$CVSQL	= "SELECT * FROM ".VOUCHER_DETAILS_TBL." WHERE contra_id = '0' AND voucher_no = '0' AND headtypes='Cr' AND invoice_no >0 AND including_vat >0 AND status=0 AND created_by='".$created_by."'";
				$cvquery = $this->db->query($CVSQL); 
				if($cvquery->num_rows() >0){					
					$including_vat  = $cvquery->row()->including_vat;
					$bill_no        = $cvquery->row()->invoice_no;
				}
				//===== Update 1 Cr A/C because if payment Multy Staff (Dr) =====
				$CrSQL= "UPDATE ".VOUCHER_DETAILS_TBL." SET amount='".$dr_amount."',invoice_no='".$invoice_nos."' WHERE contra_id='0' AND headtypes='Cr' AND voucher_no = '0' AND created_by='".$created_by."'";
				$this->db->query($CrSQL);
				
				$DrSQL= "UPDATE ".VOUCHER_DETAILS_TBL." SET contra_id='".$contra_id."', admission_id='".$admission_id."', voucher_no='".$voucher_no."', status='".$status."' WHERE contra_id='0' AND voucher_no = '0' AND created_by='".$created_by."'";
				$this->db->query($DrSQL);
				
				if($including_vat==1){
					$vat_head 	= $this->GetAccountId(6,11,41,17,6);
					$VATSQL		= "SELECT * FROM ".VOUCHER_DETAILS_TBL." WHERE invoice_no = '".$bill_no."' AND headtypes='Cr' AND account_id =$vat_head AND status = $status";
					$vquery    	= $this->db->query($VATSQL);
					if($vquery->num_rows() >0){ 					
						$vat_amount = $vquery->row()->amount;
						$dr_amount  = $dr_amount - $vat_amount;
						//===== Insert Dr VAT Payable Account =====	
						if($vat_amount >0 && $vat_head >0){
							$vat_description = "Paid VAT payable against purchase item";							   
							//==== Start Dr VAT Payable Account =====					
							$DSQL="INSERT INTO ".VOUCHER_DETAILS_TBL."(contra_id,invoice_no,voucher_no,headtypes,account_id,amount,voucher_type,description,created_by) ";
							$DSQL.="VALUES('".$contra_id."','".$invoice_nos."','".$voucher_no."','Dr','".$vat_head."','".$vat_amount."','".$voucher_type."','".$vat_description."','".$created_by."')";
							$this->db->query($DSQL); 
							$this->SaveAccountLedger($voucher_no,$invoice_nos,$voucher_date,$vat_head,$voucher_type,$vat_description,$vat_amount,"Dr","I");
						}
						
					}else{
						$vat_amount = 0;
					}
					/*		
					//===== Dr VAT Expense Account Update: for Import/Export =====
					$vatexp_head = $this->GetAccountId(6,10,29,24,9);	
					if($vat_amount >0 && $vatexp_head >0){
						$vat_description = "Payment VAT expense against purchase item";
					    //==== Start Dr VAT Expense Account =====					
						$VESQL="INSERT INTO ".VOUCHER_DETAILS_TBL."(contra_id,invoice_no,voucher_no,headtypes,account_id,amount,voucher_type,description,created_by) ";
						$VESQL.="VALUES('".$contra_id."','".$invoice_nos."','".$voucher_no."','Dr','".$vatexp_head."','".$vat_amount."','".$voucher_type."','".$vat_description."','".$created_by."')";
						$this->db->query($VESQL);
						$this->SaveAccountLedger($voucher_no,$invoice_nos,$voucher_date,$vatexp_head,$voucher_type,$vat_description,$vat_amount,"Dr","I");
					}
					*/
					if($dr_amount >0){ 	
					//==== Dr Ledger Update =======
					$CrSQL= "UPDATE ".VOUCHER_DETAILS_TBL." SET amount=".$dr_amount." WHERE contra_id=".$contra_id." AND headtypes='Dr' AND account_id=".$dr_account;
					$this->db->query($CrSQL);			
					$this->SaveAccountLedger($voucher_no,$invoice_nos,$voucher_date,$dr_account,$voucher_type,$description,$dr_amount,"Dr","I",$cheque_details);
					}
				}else{
					//==== Dr Ledger Update =======			
					$this->SaveAccountLedger($voucher_no,$invoice_nos,$voucher_date,$dr_account,$voucher_type,$description,$dr_amount,"Dr","I",$cheque_details);
				}
				//==== Cr Ledger Insert =======	
				$this->SaveAccountLedger($voucher_no,$invoice_nos,$voucher_date,$cr_account,$voucher_type,$description,$cr_amount,"Cr","I",$cheque_details);
				}//end if contra_id >0
				
				return $voucher_no;
				//print  $this->db->last_query();
			}//End if voucher_no
					
			//$this->db->trans_complete();
			//if($this->db->trans_status() === FALSE)
			if ($this->db->affected_rows() > 0)
			{
				return false;
			}else{
				return true;	
			}			
			
		}//End else	
    }
    //====== Save Expense Voucher ========
	function saveExpenseCVMaster(){			    
		$contra_id		= $this->input->post('contra-id');
		if(empty($contra_id)){ 
		$contra_id=0; $voucher_no = 0; $status = 0;
		}else{
		$status	= $this->input->post('status'); if(empty($status)){ $status = 1;}
		}	
		$bill_id	= 0;
		if(empty($bill_id)){ $bill_id=0;}	
		$mode_of_payment= $this->input->post('payment-mode');
		$voucher_date   = $this->formatDate($this->input->post('voucher-date'));
		$voucher_no		= $this->input->post('voucher-no');
		if(empty($voucher_no)){			
			$voucher_no 	= $this->getVoucherID($voucher_date);
			if(empty($status)){ $status = 1;}
		}		
		$others_payment  = $this->input->post('others-payment');
		if(empty($others_payment)){ $others_payment= 0;}
		$others_income  = 0;
		$institute_id	= $this->input->post('institute-id');
		$branch_id		= $this->input->post('branch-id');
		$session_id		= $this->input->post('session-id');
		$period_id		= $this->input->post('transaction-period');
		$version_id		= 0;
		$class_id		= 0;
		$group_id		= 0;
		$admission_id	= 0;
		$bank_name		= $this->input->post('bank-name');
		$branch_name	= $this->input->post('branch-name');
		$acc_no			= $this->input->post('acc-no');
		$cheque_no		= $this->input->post('cheque-no');
		$cheque_type	= $this->input->post('cheque-type');
		$issue_date		= $this->formatDate($this->input->post('issue-date'));		
		$dr_account		= $this->input->post('dr-account');
		$cr_account		= $this->input->post('cr-account');
		if(empty($class_id)){$class_id = 0;} if(empty($group_id)){$group_id = 0;} if(empty($admission_id)){$admission_id = 0;}
		
		$dr_amount 		= $this->input->post('payment-amount');
		$dr_amount 		= round($dr_amount,0,PHP_ROUND_HALF_UP);
		$vat_amount 	= $this->input->post('vat-amount');
		$vat_amount 	= round($vat_amount,0,PHP_ROUND_HALF_UP);
		$cr_amount 		= $this->input->post('payment-amount');
		$cr_amount 		= round($cr_amount,0,PHP_ROUND_HALF_UP);
		$voucher_type	= $this->input->post('voucher-type'); // 1=Payment, 2=Received, 3=Expense
		$description 	= str_replace("U 0026", '&', $this->input->post('naration'));
		$description	= $this->db->escape_str($description);
		$including_vat	= $this->input->post('including-vat');
		$created_by	= $this->session->userdata('created_by');
		$CQType	=""; $cheque_details=""; if(empty($voucher_type)){ $voucher_type=3;}
		if($mode_of_payment==2){
			if($cheque_type==1){
			$CQType ="Cash Cheque";
			}elseif($cheque_type==2){
			$CQType ="A/C Payee Cheque";
			}elseif($cheque_type==3){
			$CQType ="Bearer Cheque";
			}elseif($cheque_type==4){
			$CQType ="Pay Order";
			}elseif($cheque_type==5){
			$CQType ="Bank Transfer";
			}
			$cheque_details = $bank_name.", ".$branch_name."<br>A/C No. ".$acc_no.", C/Q No. ".$cheque_no."<br>Issue Date: ".$issue_date.", C/Q Type: ".$CQType;
		}// End if mode_of_payment
		$invoice_nos = 0;
		if($contra_id >0){
			//$this->db->trans_start();				
			//=== Update Master =====
			$MSQL= "UPDATE ".VOUCHER_MASTER_TBL." SET institute_id=$institute_id,branch_id=$branch_id,session_id=$session_id,period_id=$period_id,dr_amount='".$dr_amount."',cr_amount='".$cr_amount."', mode_of_payment='".$mode_of_payment."', voucher_type='".$voucher_type."', description='".$description."', others_payment='".$others_payment."' WHERE contra_id = ".$contra_id;
			$this->db->query($MSQL);
			
			if($mode_of_payment==2){
			//==== Cr Account Update =====				
			$CSQL= "UPDATE ".VOUCHER_DETAILS_TBL." SET invoice_no ='".$bill_id."', amount='".$cr_amount."', bank_name='".$bank_name."', branch_name='".$branch_name."', acc_no='".$acc_no."', cheque_no='".$cheque_no."', cheque_issue_date='".$issue_date."', cheque_type='".$cheque_type."', voucher_type='".$voucher_type."', description='".$description."', receive_note='".$receive_note."', status='".$status."' WHERE account_id='".$cr_account."' AND headtypes='Cr' AND contra_id = ".$contra_id;
			$this->db->query($CSQL);
			//==== Cr Ledger Update =======	
			$this->SaveAccountLedger($voucher_no,$invoice_nos,$voucher_date,$cr_account,$voucher_type,$description,$cr_amount,"Cr","U",$cheque_details);
			}else{
			//==== Cr Account Update =====				
			$CSQL= "UPDATE ".VOUCHER_DETAILS_TBL." SET invoice_no ='".$bill_id."', amount='".$cr_amount."', voucher_type='".$voucher_type."', description='".$description."', including_vat='".$including_vat."', advance_collect='".$advance_collect."', receive_note='".$receive_note."', status='".$status."' WHERE account_id='".$cr_account."' AND headtypes='Cr' AND contra_id = ".$contra_id;
			$this->db->query($CSQL);
			//==== Cr Ledger Update =======	
			$this->SaveAccountLedger($voucher_no,$invoice_nos,$voucher_date,$cr_account,$voucher_type,$description,$cr_amount,"Cr","U");
			}			
			//==== Dr Account Update ===== 
			if($vat_amount >0){$dr_amount = $dr_amount - $vat_amount;}				
			$DSQL= "UPDATE ".VOUCHER_DETAILS_TBL." SET invoice_no ='".$bill_id."', amount='".$dr_amount."', voucher_type='".$voucher_type."', description='".$description."', including_vat='".$including_vat."', status='".$status."' WHERE account_id='".$dr_account."' AND headtypes='Dr' AND contra_id = ".$contra_id;
			$this->db->query($DSQL);
			//==== Cr Ledger Update =======	
			$this->SaveAccountLedger($voucher_no,$invoice_nos,$voucher_date,$dr_account,$voucher_type,$description,$dr_amount,"Dr","U");		
					
			if($vat_amount >0){
				//==== Dr Ledger Update =======	
				$vat_head = $this->GetAccountId(6,11,41,17,6);	
				if($vat_amount >0 && $vat_head >0){
				   $vat_description = "Dr VAT amount against expense";
				   $dcsql  = "SELECT * FROM ".VOUCHER_DETAILS_TBL." WHERE account_id ='".$vat_head."' AND contra_id = $contra_id AND status ='1'";
				   $dquery = $this->db->query($dcsql);
				   if($dquery->num_rows() >0){			
				    $DSQL= "UPDATE ".VOUCHER_DETAILS_TBL." SET amount='".$vat_amount."', voucher_type='".$voucher_type."', description='".$vat_description."' WHERE account_id='".$vat_head."' AND contra_id = ".$contra_id; 
					$this->db->query($DSQL);			
				    $this->SaveAccountLedger($voucher_no,$invoice_nos,$voucher_date,$vat_head,$voucher_type,$vat_description,$vat_amount,"Dr","U");
				   }else{
					//==== Start Dr VAT Expense Account =====					
					$DSQL="INSERT INTO ".VOUCHER_DETAILS_TBL."(contra_id,invoice_no,voucher_no,headtypes,account_id,amount,voucher_type,description,created_by) ";
					$DSQL.="VALUES('".$contra_id."','".$invoice_nos."','".$voucher_no."','Dr','".$vat_head."','".$vat_amount."','".$voucher_type."','".$vat_description."','".$created_by."')";
					$this->db->query($DSQL);
					$this->SaveAccountLedger($voucher_no,$invoice_nos,$voucher_date,$vat_head,$voucher_type,$vat_description,$vat_amount,"Dr","I");
				   }//end else
				}			
			}			
			//$this->db->trans_complete();
			//if($this->db->trans_status() === FALSE)
			if ($this->db->affected_rows() > 0)
			{
				return false;
			}else{
				return true;	
			}
			
		}else{
			//$this->db->trans_start();
			$invoice_nos=""; $bill_id=0;							
			$SQL= "INSERT INTO ".VOUCHER_MASTER_TBL."(invoice_no,institute_id,branch_id,session_id,period_id,account_id,voucher_no,voucher_date,dr_amount,cr_amount,mode_of_payment,voucher_type,description,others_income,created_by) ";
			$SQL.="VALUES('".$invoice_nos."','".$institute_id."','".$branch_id."','".$session_id."','".$period_id."','".$cr_account."','".$voucher_no."','".$voucher_date."','".$dr_amount."','".$cr_amount."','".$mode_of_payment."','".$voucher_type."','".$description."','".$others_income."','".$created_by."')";
			if($voucher_no !=""){
				$this->db->query($SQL);
				$contra_id = $this->db->insert_id(); $status = 1;			
				if($contra_id >0){					
				 if($mode_of_payment==2){
				 //==== Cr Account =====
				 $CSQL="INSERT INTO ".VOUCHER_DETAILS_TBL."(contra_id,invoice_no, voucher_no,headtypes,account_id,amount,bank_name,branch_name,acc_no,cheque_no,cheque_issue_date,cheque_type,voucher_type,description,including_vat,advance_collect,receive_note,status,created_by) ";
				 $CSQL.="VALUES('".$contra_id."','".$bill_id."','".$voucher_no."','Cr','".$cr_account."','".$cr_amount."','".$bank_name."','".$branch_name."','".$acc_no."','".$cheque_no."','".$issue_date."','".$cheque_type."','".$voucher_type."','".$description."','".$including_vat."','".$advance_collect."','".$receive_note."','".$status."','".$created_by."')";
				 $this->db->query($CSQL);
				 //==== Cr Ledger Insert =======		
				 $this->SaveAccountLedger($voucher_no,$invoice_nos,$voucher_date,$cr_account,$voucher_type,$description,$cr_amount,"Cr","I");
				 }else{
				 //==== Cr Account =====
				 $CSQL="INSERT INTO ".VOUCHER_DETAILS_TBL."(contra_id,invoice_no, voucher_no,headtypes,account_id,amount,voucher_type,description,including_vat,advance_collect,receive_note,status,created_by) ";
				 $CSQL.="VALUES('".$contra_id."','".$bill_id."','".$voucher_no."','Cr','".$cr_account."','".$cr_amount."','".$voucher_type."','".$description."','".$including_vat."','".$advance_collect."','".$receive_note."','".$status."','".$created_by."')";
				 $this->db->query($CSQL);
				 //==== Cr Ledger Insert =======		
				 $this->SaveAccountLedger($voucher_no,$invoice_nos,$voucher_date,$cr_account,$voucher_type,$description,$cr_amount,"Cr","I");
				 }//end if mode_of_payment
				 
				 //==== Dr Account =====				 
				 $receive_note = "Expense"; if($vat_amount >0){$dr_amount = $dr_amount - $vat_amount;}	
				 $DSQL="INSERT INTO ".VOUCHER_DETAILS_TBL."(contra_id,invoice_no, voucher_no,headtypes,account_id,amount,voucher_type,description,receive_note,status,created_by) ";
				 $DSQL.="VALUES('".$contra_id."','".$bill_id."','".$voucher_no."','Dr','".$dr_account."','".$dr_amount."','".$voucher_type."','".$description."','".$receive_note."','".$status."','".$created_by."')";
				 $this->db->query($DSQL);
				 //==== Dr Ledger Insert =======				 
				 $this->SaveAccountLedger($voucher_no,$invoice_nos,$voucher_date,$dr_account,$voucher_type,$description,$dr_amount,"Dr","I");
				 
				 if($vat_amount >0){
					$vat_head 		 = $this->GetAccountId(6,11,41,17,6);
					$vat_description = "Paid VAT amount against expense";
					$DSQL="INSERT INTO ".VOUCHER_DETAILS_TBL."(contra_id,invoice_no, voucher_no,headtypes,account_id,amount,voucher_type,description,receive_note,status,created_by) ";
					$DSQL.="VALUES('".$contra_id."','".$bill_id."','".$voucher_no."','Dr','".$vat_head."','".$vat_amount."','".$voucher_type."','".$description."','".$vat_description."','".$status."','".$created_by."')";
					$this->db->query($DSQL);
					//==== Dr Ledger Insert =======					
					$this->SaveAccountLedger($voucher_no,$invoice_nos,$voucher_date,$vat_head,$voucher_type,$description,$vat_amount,"Dr","I");
				 }
				
				}//end if contra_id >0				
				return $voucher_no;
				//print  $this->db->last_query();
			}//End if voucher_no
					
			//$this->db->trans_complete();
			//if($this->db->trans_status() === FALSE)
			if ($this->db->affected_rows() > 0)
			{
				return false;
			}else{
				return true;	
			}			
			
		}//End else	
    }
	function GetAccountId($group_id,$subsidiary_level1,$subsidiary_level2,$subsidiary_level3,$head_type=NULL){		
        $this->db->select('*');
        $this->db->from(ACC_HEAD_TBL);
		if($group_id >0){
			$this->db->where("group_id", $group_id);
		}
		if($subsidiary_level1 >0){
			$this->db->where("subsidiary_level1", $subsidiary_level1);
		}
		if($subsidiary_level2 >0){
			$this->db->where("subsidiary_level2", $subsidiary_level2);
		}
		if($subsidiary_level3 >0){
			$this->db->where("subsidiary_level3", $subsidiary_level3);
		}		
		if($head_type >0){
			$this->db->where("head_type", $head_type);
		}
		$this->db->where('status',1);
        $aquery = $this->db->get();
		return $aquery->row()->account_id;         
    }
    function GetDraftRecordGrid($contra_id,$account_id,$voucher_type,$onlyGrid=0){ 
	$menu_slug= $this->uri->segment(1);
	$this->load->model('Site_model');
	$hasEditPM 	= $this->Site_model->hasOptionPermission($menu_slug,"Edit");
	$hasDelPM  	= $this->Site_model->hasOptionPermission($menu_slug,"Delete");	
	$created_by	= $this->session->userdata('created_by');
	$roles 		= $this->session->userdata('user_role');	
	$from		= $this->input->post('from');
	$to			= $this->input->post('to');
	
	if($from==""){ $from=0;} if($to==""){ $to=150;}
	$this->db->select('d.*,c.account_name as customer_name, c.account_details, b.bill_id, b.bill_no, p.period_name_en as period_name, p.period_year', FALSE);
	$this->db->from(VOUCHER_DETAILS_TBL.' AS d');
	$this->db->join(BILL_MASTER_TBL.' AS b', 'b.bill_id=d.invoice_no','LEFT');
	$this->db->join(CUSTOMER_TBL.' AS c', 'c.account_id=d.account_id','LEFT');
	$this->db->join(PERIOD_TBL.' AS p', 'p.period_id=b.billing_month','LEFT');
	$this->db->where("d.contra_id", $contra_id);
	if($voucher_type==1){
	$this->db->where("d.headtypes", "Dr");	
	}else{
	$this->db->where("d.headtypes", "Cr");
	}
	if($contra_id ==0){
	$this->db->where("d.created_by", $created_by);	
	}	 
	if($account_id >0){
	   $this->db->where("d.account_id", $account_id);
	}	 
	if($contra_id >0){
	   $this->db->where("d.status", 1);
	}else{
	   $this->db->where("d.status", 0);
	}	
	$this->db->group_by('d.details_id');
	$this->db->order_by('d.details_id','ASC');
    	$this->db->limit($to,$from);
	$query = $this->db->get(); //echo $this->db->last_query();

	$totalrecord = $this->GetTotalDraftRecordGrid($voucher_type);

	$perPage=20; $Pagination="";
	if($totalrecord >0){
	   $Pagination = $this->getPagination($totalrecord,$perPage,"nextDraftPage");
	} //print  $this->db->last_query();
	$Grid="";
	$Grid.=
	"<table width='100%' id='data-table' class='table table-responsive table-hover table-bordered'>
		<thead class='bg-primary'>
		  <tr>
		  	<th width='2%' class='text-center'>SL</th>
			<th width='20%'>Invoice No.</th>
			<th width='15%'>Bill Period</th>
			<th width='36%'>Customer Name</th>
			<th width='15%' align='right'>Collection Amount</th>
			<th width='12%' class='text-center'>Action</th>
		  </tr>
		</thead>";
		$sl=0; $TotalAmount=0; 
	    foreach($query->result() as $row){
		$sl++; $TotalAmount+=$row->amount;
		$bill_id = $row->invoice_no;
		
		if($bill_id >0){		   
			$inv_details=$row->bill_no;
		}elseif($bill_id ==0){
			$inv_details=" (Advance Collection)";
		}else{ $inv_details="";}
		
		if($row->status==0 || $row->status==1){$disabled="";}else{$disabled="disabled";}
		$Grid.="<tr>
			<td align='center'>".$sl."</td>
			<td>".$inv_details."</td>
			<td>".$row->period_name." - ".$row->period_year."</td>
			<td>".$row->customer_name."</td>
			<td align='right'>".number_format($row->amount, 2, '.', ',')."</td>
			<td align='center'>";
			if($hasEditPM){
			$Grid.="<span data-toggle='tooltip' data-original-title='Edit'><a class='btn btn-info btn-xs' data-toggle='modal' onclick=editRecord('".$row->contra_id."','".$row->details_id."') id='".$row->details_id."' href='#'><i class='fa fa-edit'></i></a></span> &nbsp;";
			}else{
			$Grid.="<span data-toggle='tooltip' data-original-title='Edit'><a class='btn btn-info btn-xs disabled' data-toggle='modal' id='".$row->details_id."' href='#'><i class='fa fa-edit'></i></a></span> &nbsp;";	
			}
			if($hasDelPM){				
			$Grid.="<span data-toggle='tooltip' data-original-title='Delete'><a class='btn btn-danger btn-xs' data-toggle='modal' onclick=deleteItem('".$row->details_id."','".$row->contra_id."','".$row->account_id."','".$row->invoice_no."') id='".$row->details_id."' href='#deleteItemModal'><i class='fa fa-trash'></i></a></span>";
			}else{
			$Grid.="<span data-toggle='tooltip' data-original-title='Delete'><a class='btn btn-danger btn-xs disabled' data-toggle='modal' href='#'><i class='fa fa-trash'></i></a></span>";	
			}
		$Grid.="</td>
		</tr>";
		}
	   $Grid.="<tr>
		<td colspan='4' align='right'><strong>Total:</strong></td>
		<td align='right'><strong>".number_format($TotalAmount, 2, '.', ',')."</strong></td>
		<td>&nbsp;</td>
		</tr>";
	   $Grid.="</table>";
	   $Grid.="<div class='text-right'>$Pagination</div>";
	   if($onlyGrid==0){
		 if($voucher_type==1){
		  $DrSQL	= "SELECT SUM(amount) as amount FROM ".VOUCHER_DETAILS_TBL." WHERE contra_id = '".$contra_id."' AND headtypes='Cr'";
		  if($contra_id ==0){
			$this->db->where("d.created_by", $created_by);	
		  }	 
		  if($account_id >0){
			   $DrSQL.= " AND account_id=".$account_id;
		  }	 
		  if($contra_id >0){
			   $DrSQL.= " AND status=1";
		  }else{
			   $DrSQL.= " AND status=0";
		  }
		  $TotalAmount = $this->db->query($DrSQL)->row()->amount;
		  $Grid.="##&##".$TotalAmount;
		 }else{
		  $Grid.="##&##".$TotalAmount;
		 }
	     
	   }
	   return $Grid;
    }

    function GetTotalDraftRecordGrid($voucher_type){
		$account_id		= $this->input->post('account-id');
		$contra_id		= $this->input->post('contra-id');
		if(empty($account_id)){$account_id= 0;} if(empty($contra_id)){$contra_id= 0;}	
		$created_by		= $this->session->userdata('created_by');
		$roles 			= $this->session->userdata('user_role');
		$this->db->select('d.*,c.account_name as customer_name, c.account_details', FALSE);
		$this->db->from(VOUCHER_DETAILS_TBL.' AS d');
		$this->db->join(CUSTOMER_TBL.' AS c', 'c.account_id=d.account_id','LEFT');
		$this->db->where("d.contra_id", $contra_id);	
		if($contra_id ==0){
		$this->db->where("d.created_by", $created_by);	
		}
		if($voucher_type==1){
		$this->db->where("d.headtypes", "Dr");	
		}else{
		$this->db->where("d.headtypes", "Cr");
		}		
		if($account_id >0){
		   $this->db->where("d.account_id", $account_id);
		}	 
		if($contra_id >0){
		   $this->db->where("d.status", 1);
		}else{
		   $this->db->where("d.status", 0);
		}	
		$this->db->group_by('d.details_id');
		$this->db->order_by('d.details_id','ASC');
		$query = $this->db->get();
		if($query->num_rows() >0){
			return $query->num_rows();
		}else{
			return 0;
		}//echo $this->db->last_query();
    }
    //======== Retrive by Ajax =========
    function GetRecordGrid($voucher_type){
		$menu_slug= $this->uri->segment(1);
		$this->load->model('Site_model');
		$hasEditPM = $this->Site_model->hasOptionPermission($menu_slug,"Edit");
		$hasDelPM  = $this->Site_model->hasOptionPermission($menu_slug,"Delete");
		$hasPrintPM= $this->Site_model->hasOptionPermission($menu_slug,"Print");
		$from	= $this->input->post('from');
		$to	= $this->input->post('to');
		if($from==""){ $from=0;} if($to==""){ $to=50;}
		$institute_id	= $this->input->post('src-institute-id');
		$branch_id		= $this->input->post('src-branch-id');
		$session_id		= $this->input->post('src-session-id');
		$period_id		= $this->input->post('src-fee-period');
		$account_id	    = $this->input->post('src-account-id');
		$payment_mode	= $this->input->post('src-payment-mode');
		if($this->input->post('receive-from')!="undefined"){
			$srcFrom	= $this->formatDate($this->input->post('receive-from'));
		}else{
			$srcFrom	= "";
		}
		if($this->input->post('receive-to')!="undefined"){
			$srcTo	= $this->formatDate($this->input->post('receive-to'));
		}else{
			$srcTo	= "";
		}
		$roles 		= $this->session->userdata('user_role');
		
		$this->db->select('s.*,DATE_FORMAT(s.voucher_date,"%d %b %y") as voucher_date',FALSE);
		$this->db->from(VOUCHER_MASTER_TBL.' AS s');
		$this->db->where('s.status', 1);
		if($institute_id >0){
		$this->db->where('s.institute_id', $institute_id);
		}
		if($branch_id >0){
		$this->db->where('s.branch_id', $branch_id);
		}
		if($session_id >0){
		$this->db->where('s.session_id', $session_id);
		}		
		if($period_id >0){
		$this->db->where('s.period_id', $period_id);
		}
		if($voucher_type >0){
		$this->db->where('s.voucher_type', $voucher_type);
		}
		if($srcFrom!="" && $srcTo==""){
		   $this->db->where("s.voucher_date >=", $srcFrom);
		}elseif($srcFrom=="" && $srcTo!=""){
		   $this->db->where("s.voucher_date <=", $srcTo);
		}elseif($srcFrom!="" && $srcTo!=""){
		   $this->db->where("s.voucher_date BETWEEN '$srcFrom' AND '$srcTo'",NULL,FALSE);
		}	
		if($payment_mode >0){
		$this->db->where('s.mode_of_payment', $payment_mode);
		}
		$this->db->join(VOUCHER_DETAILS_TBL.' AS d', 'd.contra_id=s.contra_id','LEFT');
		
		if($account_id >0){
		$this->db->where('d.account_id', $account_id);
		}		
		if($this->session->userdata('user_role') >4){
			$this->db->where("d.account_id", $this->session->userdata('user_ref_id')); 
		}	
		$this->db->group_by('s.contra_id');
		//$this->db->order_by('s.voucher_no,s.contra_id','ASC');
		$this->db->order_by('s.contra_id,s.voucher_no','DESC');
		$this->db->limit($to,$from);
		$query = $this->db->get(); //echo $this->db->last_query();

		$totalrecord = $this->GetTotalRecords($voucher_type);

		$perPage=50; $Pagination="";
		if($totalrecord >0){
		   $Pagination = $this->getPagination($totalrecord,$perPage);
		} 
		echo 
		'<table width="100%" id="data-table" class="table table-responsive table-hover table-bordered">
		<thead class="bg-primary">
		  <tr>
		  	<th width="2%" class="text-center">SL</th>
			<th width="11%">Voucher No</th>
			<th width="29%">Account Head</th>
			<th width="9%">Pay Mode</th>
			<th width="24%">Naration</th>
			<th width="13%" class="text-right">Amount</th>
			<th width="12%">Action</th>
		  </tr>
		</thead>';
	    $sl=0; $paymentmode=""; $dr_account=""; $cr_account=""; $TotalAmount=0;
		if(empty($this->input->post('from'))){ $sl=0; }else{ $sl= $this->input->post('from');}
	    foreach($query->result() as $row){
		$sl++;	$TotalAmount+=$row->dr_amount;	
		if($row->mode_of_payment==1){$paymentmode="Cash";}
		elseif($row->mode_of_payment==2){$paymentmode="Cheque";}
		elseif($row->mode_of_payment==3){$paymentmode="Challan";}
		elseif($row->mode_of_payment==4){$paymentmode="bKash";}
		elseif($row->mode_of_payment==5){$paymentmode="Card";}
		elseif($row->mode_of_payment==10){$paymentmode="Others";}

		$dr_account = $this->GetAccountHead($row->contra_id,"Dr");
		$cr_account = $this->GetAccountHead($row->contra_id,"Cr");
		 
		if($row->voucher_type==4){$disabled="disabled";}else{$disabled="";}
		
		echo "<tr>
			<td align='center'>".$sl."</td>
			<td>".$row->voucher_no."<br>".$row->voucher_date."</td>
			<td>Dr: ".$dr_account."<br>Cr: ".$cr_account."</td>
			<td>".$paymentmode."</td>
			<td>".$row->description."</td>
			<td class='text-right'>".number_format($row->dr_amount, 2, '.', ',')."</td>
			<td align='center'>";
			if($hasEditPM){
			  if($row->mode_of_payment==2){
				if($row->voucher_type==3){  
				echo "<span data-toggle='tooltip' data-original-title='Edit'><a class='btn btn-info btn-sm $disabled' data-toggle='modal' onclick=editExpense('".$row->contra_id."') id='".$row->contra_id."' href='#'><i class='fa fa-edit'></i></a></span> &nbsp;<span data-toggle='tooltip' data-original-title='Dishonored Cheque'><a class='btn btn-warning btn-sm $disabled' data-toggle='modal' onclick=DishonoredCheque('".$row->invoice_no."','".$row->contra_id."') id='".$row->invoice_no."' href='#DishonoredModal'><i class='fa fa-ban'></i></a></span>";
				}else{
				echo "<span data-toggle='tooltip' data-original-title='Edit'><a class='btn btn-info btn-sm $disabled' data-toggle='modal' onclick=editMaster('".$row->contra_id."') id='".$row->contra_id."' href='#'><i class='fa fa-edit'></i></a></span> &nbsp;<span data-toggle='tooltip' data-original-title='Dishonored Cheque'><a class='btn btn-warning btn-sm $disabled' data-toggle='modal' onclick=DishonoredCheque('".$row->invoice_no."','".$row->contra_id."') id='".$row->invoice_no."' href='#DishonoredModal'><i class='fa fa-ban'></i></a></span>";
				}
			  }else{
				if($row->voucher_type==3){  
				echo "<span data-toggle='tooltip' data-original-title='Edit'><a class='btn btn-info btn-sm $disabled' data-toggle='modal' onclick=editExpense('".$row->contra_id."') id='".$row->contra_id."' href='#'><i class='fa fa-edit'></i></a></span> &nbsp;";
				}else{
				echo "<span data-toggle='tooltip' data-original-title='Edit'><a class='btn btn-info btn-sm $disabled' data-toggle='modal' onclick=editMaster('".$row->contra_id."') id='".$row->contra_id."' href='#'><i class='fa fa-edit'></i></a></span> &nbsp;";
				}
			  }		
			}
			if($hasDelPM){				
				echo "<span data-toggle='tooltip' data-original-title='Delete'><a class='btn btn-danger btn-sm $disabled' data-toggle='modal' onclick=deleteRecord('".$row->invoice_no."','".$row->contra_id."') id='".$row->invoice_no."' href='#deleteModal'><i class='fa fa-trash'></i></a></span>";
			}
			if($hasPrintPM){
				if($row->voucher_type==1){
					echo "<div class='clearfix'></div><br><div style='height:5px'></div> <span data-toggle='tooltip' data-original-title='Print Voucher'><a class='btn btn-success btn-sm' id='".$row->contra_id."' target='_blank' href='".base_url()."voucher/ViewPaymentVoucher/".$row->contra_id."'><i class='fa fa-print'></i> &nbsp; Print &nbsp;</a></span>";
				}elseif($row->voucher_type==2){
					echo "<div class='clearfix'></div><br><div style='height:5px'></div> <span data-toggle='tooltip' data-original-title='Print Voucher'><a class='btn btn-success btn-sm' id='".$row->contra_id."' target='_blank' href='".base_url()."voucher/ViewReceivedVoucher/".$row->contra_id."'><i class='fa fa-print'></i> &nbsp; Print &nbsp;</a></span>";
				}elseif($row->voucher_type==3){
					echo "<div class='clearfix'></div><br><div style='height:5px'></div> <span data-toggle='tooltip' data-original-title='Print Voucher'><a class='btn btn-success btn-sm' id='".$row->contra_id."' target='_blank' href='".base_url()."voucher/ViewExpenseVoucher/".$row->contra_id."'><i class='fa fa-print'></i> &nbsp; Print &nbsp;</a></span>";
				}elseif($row->voucher_type==4){
					echo "<div class='clearfix'></div><br><div style='height:5px'></div> <span data-toggle='tooltip' data-original-title='Print Voucher'><a class='btn btn-success btn-sm' id='".$row->contra_id."' target='_blank' href='".base_url()."voucher/ViewJournalVoucher/".$row->contra_id."'><i class='fa fa-print'></i> &nbsp; Print &nbsp;</a></span>";
				}				
			}			
			echo "</td>
		</tr>";
		}
		echo "<tr>
		<td colspan='5' align='right'><strong>Total:</strong></td>
		<td align='right'><strong>".number_format($TotalAmount, 2, '.', ',')."</strong></td>
		<td>&nbsp;</td>
		</tr>";
		echo '</table>';
	   echo "<div class='text-right'>$Pagination</div>";
	}
    //======== Retrive by Ajax =========
    function GetAllRecordGrid($voucher_type){
		$menu_slug= $this->uri->segment(1);
		$this->load->model('Site_model');
		$hasPrintPM= $this->Site_model->hasOptionPermission($menu_slug,"Print");
		$from	= $this->input->post('from');
		$to	= $this->input->post('to');
		if($from==""){ $from=0;} if($to==""){ $to=25;}
		$institute_id	= $this->input->post('src-institute-id');
		$branch_id		= $this->input->post('src-branch-id');
		$session_id		= $this->input->post('src-session-id');
		$version_id		= $this->input->post('src-version-id');
		$period_id		= $this->input->post('src-fee-period');
		$account_id		= $this->input->post('src-account-id');
		$payment_mode	= $this->input->post('src-payment-mode');
		if($this->input->post('receive-from')!="undefined"){
			$srcFrom	= $this->formatDate($this->input->post('receive-from'));
		}else{
			$srcFrom	= "";
		}
		if($this->input->post('receive-to')!="undefined"){
			$srcTo	= $this->formatDate($this->input->post('receive-to'));
		}else{
			$srcTo	= "";
		}
		$roles 		= $this->session->userdata('user_role');
		
		$this->db->select('s.*,DATE_FORMAT(s.voucher_date,"%d %b %y") as voucher_date',FALSE);
		$this->db->from(VOUCHER_MASTER_TBL.' AS s');
		$this->db->where('s.status', 1);
		if($institute_id >0){
		$this->db->where('s.institute_id', $institute_id);
		}
		if($branch_id >0){
		$this->db->where('s.branch_id', $branch_id);
		}
		if($session_id >0){
		$this->db->where('s.session_id', $session_id);
		}
		if($version_id >0){
		$this->db->where('s.version_id', $version_id);
		}		
		if($period_id >0){
		$this->db->where('s.period_id', $period_id);
		}
		if($voucher_type >0){
		$this->db->where('s.voucher_type', $voucher_type);
		}
		if($srcFrom!="" && $srcTo==""){
		   $this->db->where("s.voucher_date >=", $srcFrom);
		}elseif($srcFrom=="" && $srcTo!=""){
		   $this->db->where("s.voucher_date <=", $srcTo);
		}elseif($srcFrom!="" && $srcTo!=""){
		   $this->db->where("s.voucher_date BETWEEN '$srcFrom' AND '$srcTo'",NULL,FALSE);
		}	
		if($payment_mode >0){
		$this->db->where('s.mode_of_payment', $payment_mode);
		}
		$this->db->join(VOUCHER_DETAILS_TBL.' AS d', 'd.contra_id=s.contra_id','LEFT');
		
		if($account_id >0){
		$this->db->where('d.account_id', $account_id);
		}		
		if($this->session->userdata('user_role') >4){
			$this->db->where("d.account_id", $this->session->userdata('user_ref_id')); 
		}	
		$this->db->group_by('s.contra_id');
		$this->db->order_by('s.voucher_no,s.contra_id','ASC');
		$this->db->limit($to,$from);
		$query = $this->db->get(); //echo $this->db->last_query();

		$totalrecord = $this->GetTotalRecords($voucher_type);

		$perPage=25; $Pagination="";
		if($totalrecord >0){
		   $Pagination = $this->getPagination($totalrecord,$perPage);
		} 
		echo 
		'<table width="100%" id="data-table" class="table table-responsive table-hover table-bordered">
		<thead class="bg-primary">
		  <tr>
		  	<th width="2%" class="text-center">SL</th>
			<th width="11%">Voucher No</th>
			<th width="29%">Account Head</th>
			<th width="9%">Pay Mode</th>
			<th width="24%">Naration</th>
			<th width="13%" class="text-right">Amount</th>
			<th width="12%">Action</th>
		  </tr>
		</thead>';
	    $sl=0; $paymentmode=""; $dr_account=""; $cr_account=""; $TotalAmount=0;
		if(empty($this->input->post('from'))){ $sl=0; }else{ $sl= $this->input->post('from');}
	    foreach($query->result() as $row){
		$sl++;	$TotalAmount+=$row->dr_amount;	
		if($row->mode_of_payment==1){$paymentmode="Cash";}
		elseif($row->mode_of_payment==2){$paymentmode="Cheque";}
		elseif($row->mode_of_payment==3){$paymentmode="Challan";}
		elseif($row->mode_of_payment==4){$paymentmode="bKash";}
		elseif($row->mode_of_payment==5){$paymentmode="Card";}
		elseif($row->mode_of_payment==10){$paymentmode="Others";}

		$dr_account = $this->GetAccountHead($row->contra_id,"Dr");
		$cr_account = $this->GetAccountHead($row->contra_id,"Cr");
		 
		if($row->voucher_type==4){$disabled="disabled";}else{$disabled="";}
		
		echo "<tr>
			<td align='center'>".$sl."</td>
			<td>".$row->voucher_no."<br>".$row->voucher_date."</td>
			<td>Dr: ".$dr_account."<br>Cr: ".$cr_account."</td>
			<td>".$paymentmode."</td>
			<td>".$row->description."</td>
			<td class='text-right'>".number_format($row->dr_amount, 2, '.', ',')."</td>
			<td align='center'>";			
			if($hasPrintPM){				
				if($row->voucher_type==1){
					echo "<div class='clearfix'></div><br><div style='height:5px'></div> <span data-toggle='tooltip' data-original-title='Print Voucher'><a class='btn btn-success btn-sm' id='".$row->contra_id."' target='_blank' href='".base_url()."voucher/ViewPaymentVoucher/".$row->contra_id."'><i class='fa fa-print'></i> &nbsp; Print &nbsp;</a></span>";
				}elseif($row->voucher_type==2){
					echo "<div class='clearfix'></div><br><div style='height:5px'></div> <span data-toggle='tooltip' data-original-title='Print Voucher'><a class='btn btn-success btn-sm' id='".$row->contra_id."' target='_blank' href='".base_url()."voucher/ViewReceivedVoucher/".$row->contra_id."'><i class='fa fa-print'></i> &nbsp; Print &nbsp;</a></span>";
				}elseif($row->voucher_type==3){
					echo "<div class='clearfix'></div><br><div style='height:5px'></div> <span data-toggle='tooltip' data-original-title='Print Voucher'><a class='btn btn-success btn-sm' id='".$row->contra_id."' target='_blank' href='".base_url()."voucher/ViewExpenseVoucher/".$row->contra_id."'><i class='fa fa-print'></i> &nbsp; Print &nbsp;</a></span>";
				}elseif($row->voucher_type==4){
					echo "<div class='clearfix'></div><br><div style='height:5px'></div> <span data-toggle='tooltip' data-original-title='Print Voucher'><a class='btn btn-success btn-sm' id='".$row->contra_id."' target='_blank' href='".base_url()."voucher/ViewJournalVoucher/".$row->contra_id."'><i class='fa fa-print'></i> &nbsp; Print &nbsp;</a></span>";
				}
			}			
			echo "</td>
		</tr>";
		}
		echo "<tr>
		<td colspan='5' align='right'><strong>Total:</strong></td>
		<td align='right'><strong>".number_format($TotalAmount, 2, '.', ',')."</strong></td>
		<td>&nbsp;</td>
		</tr>";
		echo '</table>';
	   echo "<div class='text-right'>$Pagination</div>";
	}
	function GetTotalRecords($voucher_type){
		$institute_id	= $this->input->post('src-institute-id');
		$branch_id		= $this->input->post('src-branch-id');
		$session_id		= $this->input->post('src-session-id');
		$version_id		= $this->input->post('src-version-id');
		$period_id		= $this->input->post('src-fee-period');
		$class_id		= $this->input->post('src-class-id');
		$account_id		= $this->input->post('src-account-id');
		$payment_mode	= $this->input->post('src-payment-mode');
		if($this->input->post('receive-from')!="undefined"){
			$srcFrom	= $this->formatDate($this->input->post('receive-from'));
		}else{
			$srcFrom	= "";
		}
		if($this->input->post('receive-to')!="undefined"){
			$srcTo	= $this->formatDate($this->input->post('receive-to'));
		}else{
			$srcTo	= "";
		}
	
		$this->db->select('s.*,DATE_FORMAT(s.voucher_date,"%d %b %y") as voucher_date',FALSE);
		$this->db->from(VOUCHER_MASTER_TBL.' AS s');
		$this->db->where('s.status', 1);
		if($institute_id >0){
		$this->db->where('s.institute_id', $institute_id);
		}
		if($branch_id >0){
		$this->db->where('s.branch_id', $branch_id);
		}
		if($session_id >0){
		$this->db->where('s.session_id', $session_id);
		}
		if($version_id >0){
		$this->db->where('s.version_id', $version_id);
		}
		if($class_id >0){
		$this->db->where('s.class_id', $class_id);
		}		
		if($period_id >0){
		$this->db->where('s.period_id', $period_id);
		}
		if($voucher_type >0){
		$this->db->where('s.voucher_type', $voucher_type);
		}
		if($srcFrom!="" && $srcTo==""){
		   $this->db->where("s.voucher_date >=", $srcFrom);
		}elseif($srcFrom=="" && $srcTo!=""){
		   $this->db->where("s.voucher_date <=", $srcTo);
		}elseif($srcFrom!="" && $srcTo!=""){
		   $this->db->where("s.voucher_date BETWEEN '$srcFrom' AND '$srcTo'",NULL,FALSE);
		}
	
		if($payment_mode >0){
		$this->db->where('s.mode_of_payment', $payment_mode);
		}
		$this->db->join(VOUCHER_DETAILS_TBL.' AS d', 'd.contra_id=s.contra_id','LEFT');
	
		if($account_id >0){
		$this->db->where('d.account_id', $account_id);
		}	
		$this->db->group_by('s.contra_id');
		$this->db->order_by('s.voucher_no,s.contra_id','ASC');
		$query = $this->db->get();
		if($query->num_rows() >0){
			return $query->num_rows();
		}else{
			return 0;
		}//echo $this->db->last_query();
    }
    //==== Start Advanced =======
    function GetAdvancedRecordGrid($voucher_type=0){
		$menu_slug= $this->uri->segment(1);
		$this->load->model('Site_model');
		$hasEditPM = $this->Site_model->hasOptionPermission($menu_slug,"Edit");
		$hasDelPM  = $this->Site_model->hasOptionPermission($menu_slug,"Delete");
		$hasPrintPM= $this->Site_model->hasOptionPermission($menu_slug,"Print");
		$from	= $this->input->post('from');
		$to	= $this->input->post('to');
		if($from==""){ $from=0;} if($to==""){ $to=25;}
		$account_id	= $this->input->post('src-account-id');
		$payment_mode	= $this->input->post('src-payment-mode');
		$srcFrom	= $this->formatDate($this->input->post('receive-from'));
		$srcTo		= $this->formatDate($this->input->post('receive-to'));
		$roles 		= $this->session->userdata('user_role');

		$this->db->select('m.*,d.amount,DATE_FORMAT(m.voucher_date,"%d %b %y") as voucher_date',FALSE);
		$this->db->from(VOUCHER_MASTER_TBL.' AS m');
		$this->db->join(VOUCHER_DETAILS_TBL.' AS d', 'd.contra_id=m.contra_id','LEFT');
		$this->db->where('d.invoice_no', 0);
		$this->db->where('d.status', 1);
		if($voucher_type >0){
		$this->db->where('s.voucher_type', $voucher_type);
		}	
		if($account_id >0){
			$this->db->where('d.account_id', $account_id);
		}
		if($srcFrom!="" && $srcTo==""){
			$this->db->where("m.voucher_date >=", $srcFrom);
		}elseif($srcFrom=="" && $srcTo!=""){
			$this->db->where("m.voucher_date <=", $srcTo);
		}elseif($srcFrom!="" && $srcTo!=""){
			$this->db->where("m.voucher_date BETWEEN '$srcFrom' AND '$srcTo'",NULL,FALSE);
		}	
		if($payment_mode >0){
			$this->db->where('m.mode_of_payment', $payment_mode);
		}	
		$this->db->group_by('d.contra_id');
		$this->db->order_by('m.voucher_no,m.contra_id','ASC');
		$this->db->limit($to,$from);
		$query = $this->db->get(); //echo $this->db->last_query();

		$totalrecord = $this->GetTotalAdvancedRecords($voucher_type);

		$perPage=25; $Pagination="";
		if($totalrecord >0){
			$Pagination = $this->getPagination($totalrecord,$perPage);
		} 
		echo 
		'<table width="100%" id="data-table" class="table table-responsive table-hover table-bordered">
		<thead class="bg-primary">
		  <tr>
			<th width="2%" class="text-center">SL</th>
			<th width="11%">Voucher No</th>
			<th width="29%">Account Head</th>
			<th width="9%">Pay Mode</th>
			<th width="24%">Naration</th>
			<th width="13%" class="text-right">Amount</th>
			<th width="12%">Action</th>
		  </tr>
		</thead>';
		$sl=0; $paymentmode=""; $dr_account=""; $cr_account=""; $TotalAmount=0;
		foreach($query->result() as $row){
			$sl++; $TotalAmount+=$row->amount;	
			if($row->mode_of_payment==1){$paymentmode="Cash";}
			elseif($row->mode_of_payment==2){$paymentmode="Cheque";}
			elseif($row->mode_of_payment==3){$paymentmode="Challan";}
			elseif($row->mode_of_payment==4){$paymentmode="bKash";}
			elseif($row->mode_of_payment==5){$paymentmode="Card";}
			elseif($row->mode_of_payment==10){$paymentmode="Others";}

			$dr_account = $this->GetAccountHead($row->contra_id,"Dr");
			$cr_account = $this->GetAccountHead($row->contra_id,"Cr");
			 
			if($row->voucher_type==4){$disabled="disabled";}else{$disabled="";}

			echo "<tr>
				<td align='center'>".$sl."</td>
				<td>".$row->voucher_no."<br>".$row->voucher_date."</td>
				<td>Dr: ".$dr_account."<br>Cr: ".$cr_account."</td>
				<td>".$paymentmode."</td>
				<td>".$row->description."</td>
				<td class='text-right'>".number_format($row->amount, 2, '.', ',')."</td>
				<td align='center'>";
				if($hasEditPM){
				  if($row->mode_of_payment==2){
					if($row->voucher_type==3){  
					echo "<span data-toggle='tooltip' data-original-title='Edit'><a class='btn btn-info btn-sm $disabled' data-toggle='modal' onclick=editExpense('".$row->contra_id."') id='".$row->contra_id."' href='#'><i class='fa fa-edit'></i></a></span> &nbsp;<span data-toggle='tooltip' data-original-title='Dishonored Cheque'><a class='btn btn-warning btn-sm $disabled' data-toggle='modal' onclick=DishonoredCheque('".$row->invoice_no."','".$row->contra_id."') id='".$row->invoice_no."' href='#DishonoredModal'><i class='fa fa-ban'></i></a></span>";
					}else{
					echo "<span data-toggle='tooltip' data-original-title='Edit'><a class='btn btn-info btn-sm $disabled' data-toggle='modal' onclick=editMaster('".$row->contra_id."') id='".$row->contra_id."' href='#'><i class='fa fa-edit'></i></a></span> &nbsp;<span data-toggle='tooltip' data-original-title='Dishonored Cheque'><a class='btn btn-warning btn-sm $disabled' data-toggle='modal' onclick=DishonoredCheque('".$row->invoice_no."','".$row->contra_id."') id='".$row->invoice_no."' href='#DishonoredModal'><i class='fa fa-ban'></i></a></span>";
					}
				  }else{
					if($row->voucher_type==3){  
					echo "<span data-toggle='tooltip' data-original-title='Edit'><a class='btn btn-info btn-sm $disabled' data-toggle='modal' onclick=editExpense('".$row->contra_id."') id='".$row->contra_id."' href='#'><i class='fa fa-edit'></i></a></span> &nbsp;";
					}else{
					echo "<span data-toggle='tooltip' data-original-title='Edit'><a class='btn btn-info btn-sm $disabled' data-toggle='modal' onclick=editMaster('".$row->contra_id."') id='".$row->contra_id."' href='#'><i class='fa fa-edit'></i></a></span> &nbsp;";
					}
				  }			
				}
				if($hasDelPM){				
					echo "<span data-toggle='tooltip' data-original-title='Delete'><a class='btn btn-danger btn-sm $disabled' data-toggle='modal' onclick=deleteRecord('".$row->invoice_no."','".$row->contra_id."') id='".$row->invoice_no."' href='#deleteModal'><i class='fa fa-trash'></i></a></span>";
				}
				if($hasPrintPM){					
					if($row->voucher_type==1){
						echo "<div class='clearfix'></div><br><div style='height:5px'></div> <span data-toggle='tooltip' data-original-title='Print Voucher'><a class='btn btn-success btn-sm' id='".$row->contra_id."' target='_blank' href='".base_url()."voucher/ViewPaymentVoucher/".$row->contra_id."'><i class='fa fa-print'></i> &nbsp; Print &nbsp;</a></span>";
					}elseif($row->voucher_type==2){
						echo "<div class='clearfix'></div><br><div style='height:5px'></div> <span data-toggle='tooltip' data-original-title='Print Voucher'><a class='btn btn-success btn-sm' id='".$row->contra_id."' target='_blank' href='".base_url()."voucher/ViewReceivedVoucher/".$row->contra_id."'><i class='fa fa-print'></i> &nbsp; Print &nbsp;</a></span>";
					}elseif($row->voucher_type==3){
						echo "<div class='clearfix'></div><br><div style='height:5px'></div> <span data-toggle='tooltip' data-original-title='Print Voucher'><a class='btn btn-success btn-sm' id='".$row->contra_id."' target='_blank' href='".base_url()."voucher/ViewExpenseVoucher/".$row->contra_id."'><i class='fa fa-print'></i> &nbsp; Print &nbsp;</a></span>";
					}elseif($row->voucher_type==4){
						echo "<div class='clearfix'></div><br><div style='height:5px'></div> <span data-toggle='tooltip' data-original-title='Print Voucher'><a class='btn btn-success btn-sm' id='".$row->contra_id."' target='_blank' href='".base_url()."voucher/ViewJournalVoucher/".$row->contra_id."'><i class='fa fa-print'></i> &nbsp; Print &nbsp;</a></span>";
					}
				}							
				echo "</td>
			</tr>";
		}//end foreach
		echo "<tr>
		<td colspan='5' align='right'><strong>Total:</strong></td>
		<td align='right'><strong>".number_format($TotalAmount, 2, '.', ',')."</strong></td>
		<td>&nbsp;</td>
		</tr>";
		echo '</table>';
		echo "<div class='text-right'>$Pagination</div>";
	}

	function GetTotalAdvancedRecords($voucher_type=0){
		$account_id		= $this->input->post('src-account-id');
		$payment_mode	= $this->input->post('src-payment-mode');
		$srcFrom		= $this->formatDate($this->input->post('receive-from'));
		$srcTo			= $this->formatDate($this->input->post('receive-to'));
	
		$this->db->select('m.*,d.amount,DATE_FORMAT(m.voucher_date,"%d %b %y") as voucher_date',FALSE);
		$this->db->from(VOUCHER_MASTER_TBL.' AS m');
		$this->db->join(VOUCHER_DETAILS_TBL.' AS d', 'd.contra_id=m.contra_id','LEFT');
		$this->db->where('d.invoice_no', 0);
		$this->db->where('d.status', 1);		
		if($voucher_type >0){
		$this->db->where('s.voucher_type', $voucher_type);
		}	
		if($account_id >0){
		$this->db->where('d.account_id', $account_id);
		}
		if($srcFrom!="" && $srcTo==""){
		   $this->db->where("m.voucher_date >=", $srcFrom);
		}elseif($srcFrom=="" && $srcTo!=""){
		   $this->db->where("m.voucher_date <=", $srcTo);
		}elseif($srcFrom!="" && $srcTo!=""){
		   $this->db->where("m.voucher_date BETWEEN '$srcFrom' AND '$srcTo'",NULL,FALSE);
		}	
		if($payment_mode >0){
		$this->db->where('m.mode_of_payment', $payment_mode);
		}	
		$this->db->group_by('d.contra_id');
		$this->db->order_by('m.voucher_no,m.contra_id','ASC');
		$query = $this->db->get();
		if($query->num_rows() >0){
			return $query->num_rows();
		}else{
			return 0;
		}//echo $this->db->last_query();
    }
    function GetAccountHead($contra_id,$type){
        $this->db->select('a.account_name,a.account_details');
		$this->db->from(VOUCHER_DETAILS_TBL.' AS v');
		$this->db->join(ACC_HEAD_TBL.' AS a', 'a.account_id=v.account_id','LEFT');
		
		$this->db->where('v.headtypes', $type);
		$this->db->where('v.contra_id', $contra_id);
		$this->db->group_by('a.account_id');
		$query = $this->db->get(); //echo $this->db->last_query();
		if($query->num_rows() >0){
			$Details="";
			$Details = $query->row()->account_name;
			if($query->row()->account_details !=""){
			$Details.= "<br><i>".$query->row()->account_details."</i>";
			}
		}else{$Details="";}
		
		return $Details;
    }
    
    function DeleteRecord(){
        $contra_id 	=$this->input->post('contra-id');
        $bill_id 	=$this->input->post('invoice-no');
		if($contra_id >0 && $bill_id !=""){
			//$this->db->trans_start();		
			//==== Start Delete Ledger ======
			$DSQL= "SELECT * FROM ".VOUCHER_DETAILS_TBL." WHERE contra_id ='".$contra_id."' AND status=1";
			$cquery    = $this->db->query($DSQL);
			if($cquery->num_rows() >0){
				$invoice_no=0;
				foreach($cquery->result() as $row){
				$voucher_no = $row->voucher_no;
				$invoice_no = $row->invoice_no;
				if($invoice_no >0 && $row->headtypes=="Cr"){
				//==== Start Rulback Transaction ========		
				$this->rollbackBillPayment($contra_id,$invoice_no);
				}
				$VSQL1="UPDATE ".ACC_LEDGER_TBL." SET status ='2' WHERE invoice_no LIKE('%".$invoice_no."%') AND voucher_no='".$voucher_no."'";
				$this->db->query($VSQL1);
				}
			}
			//==== Start Master Voucher Delete =====
			$VSQL1= "UPDATE ".VOUCHER_MASTER_TBL." SET status ='2' WHERE contra_id = ".$contra_id;
			$res1 = $this->db->query($VSQL1);
			//==== Start Details Voucher Delete =====
			$VSQL2= "UPDATE ".VOUCHER_DETAILS_TBL." SET status ='2' WHERE contra_id = ".$contra_id;
			$res2 = $this->db->query($VSQL2);
			
			//$this->db->trans_complete();
			//if($this->db->trans_status() === FALSE)
			if ($this->db->affected_rows() > 0)
			{
				echo "0";
			}else{
				echo "1";	
			}
		}else{ echo "0"; }
    }
    
    function DeleteVoucherItem(){
        $contra_id 	=$this->input->post('contra-id');
        $bill_id 	=$this->input->post('invoice-no');
        $details_id =$this->input->post('details-id');
		if($details_id >0){
			//$this->db->trans_start();
			//==== Start Rulback ========
			if($contra_id >0){
			  if($bill_id >0){
			   $this->rollbackBillPayment($contra_id,$bill_id);
			  }
			  $MVSQL= "SELECT * FROM ".VOUCHER_MASTER_TBL." WHERE contra_id = '".$contra_id."' AND status=1";
			  $mquery = $this->db->query($MVSQL);
			  $mrow   = $mquery->row();
			  $dr_account  = $mrow->dr_amount;
			  $cr_account  = $mrow->cr_account;
			  $voucher_no  = $mrow->voucher_no;
			  $voucher_date= $mrow->voucher_date;
			  $voucher_type= $mrow->voucher_type;
			  $description = $mrow->description;
			
			  $DSQL= "UPDATE ".VOUCHER_DETAILS_TBL." SET status=2 WHERE contra_id=".$contra_id." AND details_id=".$details_id;
			  $this->db->query($DSQL);
			
			  $dr_amount=0; $invoice_nos="";
			  $CrSQL= "SELECT * FROM ".VOUCHER_DETAILS_TBL." WHERE contra_id = ".$contra_id." AND headtypes='Cr' AND status IN(0,1) AND details_id NOT IN($details_id)";
			  $cquery    = $this->db->query($CrSQL);
			  if($cquery->num_rows() >0){
				foreach($cquery->result() as $row){
				  $dr_amount+=$row->amount;
				  if($row->invoice_no >0){
				   $invoice_nos.=$row->invoice_no.",";
				  }//End if				
				}//End foreach
				$cr_amount = $dr_amount;
				if($invoice_nos !=""){
				$invoice_nos = substr($invoice_nos, 0, -1);
				}else{$invoice_nos =0;}
				//End if invoice_nos
				
				//=== Update Master =====
				$MSQL= "UPDATE ".VOUCHER_MASTER_TBL." SET invoice_no='".$invoice_nos."', dr_amount=".$dr_amount.",cr_amount=".$cr_amount." WHERE contra_id = ".$contra_id." AND voucher_no='".$voucher_no."'";
				$this->db->query($MSQL);

				$DrSQL= "UPDATE ".VOUCHER_DETAILS_TBL." SET invoice_no='".$invoice_nos."' WHERE contra_id=".$contra_id." AND headtypes='Dr' AND account_id=".$dr_account;
				$this->db->query($DrSQL);
							
				//==== Dr Ledger Update =======			
				$this->SaveAccountLedger($voucher_no,$invoice_nos,$voucher_date,$dr_account,$voucher_type,$description,$dr_amount,"Dr","U");
			
				//==== Cr Ledger Update =======	
				$this->SaveAccountLedger($voucher_no,$invoice_nos,$voucher_date,$cr_account,$voucher_type,$description,$cr_amount,"Cr","U");
							
			  }else{//End if numrows	
				$MSQL= "UPDATE ".VOUCHER_MASTER_TBL." SET status=2 WHERE contra_id = ".$contra_id;
				$this->db->query($MSQL);
				$DSQL= "UPDATE ".VOUCHER_DETAILS_TBL." SET status=2 WHERE contra_id=".$contra_id;
				$this->db->query($DSQL);
				//==== Start Delete Ledger ======
				$DSQL= "SELECT * FROM ".VOUCHER_DETAILS_TBL." WHERE contra_id =".$contra_id." AND status=2";
				$dquery    = $this->db->query($DSQL);
				if($dquery->num_rows() >0){
					foreach($dquery->result() as $row){
					$voucher_no = $row->voucher_no;
					$invoice_no = $row->invoice_no;
					$VSQL2="UPDATE ".ACC_LEDGER_TBL." SET status ='2' WHERE invoice_no LIKE('%".$invoice_no."%') AND voucher_no='".$voucher_no."'";
					$this->db->query($VSQL2);
					}
				}
				
			  }//End else of numrows		
			
			}else{ //End if contra_id >0 && bill_id >0
			   //==== Start Delete Details ======		
			   $VSQL3= "DELETE FROM ".VOUCHER_DETAILS_TBL." WHERE contra_id = ".$contra_id." AND details_id=".$details_id;
			   $this->db->query($VSQL3);
			}		
			//$this->db->trans_complete();
			//if($this->db->trans_status() === FALSE)
			if ($this->db->affected_rows() > 0)
			{
				echo "0";
			}else{
				echo "1";	
			}
		}else{ echo "0"; }
    }
    function DishonoredCheque(){
        $contra_id 	=$this->input->post('contra-id');
        $bill_id 	=$this->input->post('invoice-no');
        $dishonor_reason=$this->input->post('dishonor-remarks');
		if($contra_id >0 && $bill_id !=""){
			//$this->db->trans_start();
				
			//==== Start Dishonore Ledger ======
			$DSQL= "SELECT * FROM ".VOUCHER_DETAILS_TBL." WHERE contra_id ='".$contra_id."' AND status=1";
			$cquery    = $this->db->query($DSQL);
			if($cquery->num_rows() >0){
				$invoice_no=0;
				foreach($cquery->result() as $row){
				$voucher_no = $row->voucher_no;
				$invoice_no = $row->invoice_no;
				if($invoice_no >0 && $row->headtypes=="Cr"){
				//==== Start Rulback Transaction ========		
				$this->rollbackBillPayment($contra_id,$invoice_no);
				}
				$VSQL2="UPDATE ".ACC_LEDGER_TBL." SET status ='3' WHERE invoice_no LIKE('%".$invoice_no."%') AND voucher_no='".$voucher_no."'";
				$this->db->query($VSQL2);
				}
			}
			//==== Start Dishonore M Voucher =====
			$VSQL1= "UPDATE ".VOUCHER_MASTER_TBL." SET dishonor_reason ='$dishonor_reason', status ='3' WHERE contra_id =".$contra_id;
			$this->db->query($VSQL1);
			//==== Start Dishonore D Voucher =====
			$VSQL3= "UPDATE ".VOUCHER_DETAILS_TBL." SET status ='3' WHERE contra_id =".$contra_id;
			$this->db->query($VSQL3);
			
			//$this->db->trans_complete();
			//if($this->db->trans_status() === FALSE)
			if ($this->db->affected_rows() > 0)
			{
				echo "0";
			}else{
				echo "1";	
			}
		}else{ echo "0";}
    }
    
    function FillVMRecord(){
        $contra_id = $this->input->post('contra-id');
        $this->db->select('*');
		$this->db->from(VOUCHER_MASTER_TBL);
		$this->db->where('contra_id', $contra_id);
		$this->db->where('status', 1);
		$query = $this->db->get(); //echo $this->db->last_query();
        return $query->row();
    }

    function FillVDRecord($type,$voucher_type){
        $contra_id = $this->input->post('contra-id');
		$details_id= $this->input->post('details-id');
        $this->db->select('*');
		$this->db->from(VOUCHER_DETAILS_TBL);
		$this->db->where('headtypes', $type);
		$this->db->where('contra_id', $contra_id);
		if($voucher_type==2){
			if($details_id >0 && $type=="Cr"){
			$this->db->where('details_id', $details_id);
			}
		}else{
			if($details_id >0 && $type=="Dr" && $voucher_type>0){
			$this->db->where('details_id', $details_id);
			}
		}
		$this->db->group_by('details_id');
		$query = $this->db->get(); //echo $this->db->last_query();
        return $query->row();
    }
    function UpdateBillingDiscount($admission_id,$discount_type,$discount_amount){		
	    $institute_id		= $this->input->post('institute_id');
		$branch_id			= $this->input->post('branch_id');		
		$fee_period			= $this->input->post('fee_period');	
		$total_bill			= $this->input->post('total_bill');	
		$discount_percentage= $this->input->post('discount_percentage');	
		$less_tuitionfee	= $this->input->post('less_tuitionfee');
		$concession_on		= $this->input->post('concession_on');	
		$net_bill_amount	= $this->input->post('net_bill_amount');
		if(empty($admission_id)){$admission_id=0;}
		if(empty($fee_period)){$fee_period=1;} 
		if(empty($total_bill)){$total_bill=0;} 
		if(empty($discount_percentage)){$discount_percentage=0;} 
		if(empty($concession_on)){$concession_on=0;} 
		if(empty($net_bill_amount)){$net_bill_amount=0;} 
		$net_bill_amount = round($net_bill_amount,0,PHP_ROUND_HALF_UP);
		
		$dr_amount 			= ($net_bill_amount);
		$cr_amount 			= ($net_bill_amount);
		
		$mode_of_payment	= 10; // 10=Others
		$voucher_type		= 4; // 4=Journal	
		 
		if($discount_type==1){
			$d_description		= "The one-time discount $discount_amount TK on $concession_on TK.";
			$discount_head 		= $this->session->userdata('discount_head');
		}elseif($discount_type==2){
			$d_description		= "The monthly scholarship $discount_percentage% on $concession_on TK";
			if($discount_percentage==100){
		    $discount_head 		= $this->session->userdata('full_scholarship');
			}else{
			$discount_head 		= $this->session->userdata('partial_scholarship');  
			}
		}else{
			$discount_head=0; $d_description="";
		}
		$created_by		= $this->session->userdata('created_by');
		$bill_id		= 0; $contra_id=0;
		$bsql = "SELECT bill_id,account_id,paid_amount FROM ".BILL_MASTER_TBL." WHERE institute_id=".$institute_id." AND branch_id =".$branch_id." AND admission_id ='".$admission_id."' AND billing_month = $fee_period AND status ='1'";
		$bquery = $this->db->query($bsql);
		if($bquery->num_rows() >0){
		   $bill_id 	= $bquery->row()->bill_id;
		   $account_id  = $bquery->row()->account_id;
		   $paid_amount = $bquery->row()->paid_amount;
		   $due_amount  = ($net_bill_amount - $paid_amount);
		}
		$vsql = "SELECT contra_id,voucher_no,voucher_date,voucher_type,description FROM ".VOUCHER_MASTER_TBL." WHERE invoice_no ='".$bill_id."' AND voucher_type = 4 AND status ='1'";
		$vquery = $this->db->query($vsql);
		if($vquery->num_rows() >0 && $admission_id >0){
		   $contra_id    = $vquery->row()->contra_id;
		   $voucher_no   = $vquery->row()->voucher_no;	
		   $voucher_date = $vquery->row()->voucher_date;
		   $voucher_type = $vquery->row()->voucher_type;
		   $description  = $vquery->row()->description;
		   //=== Update Master =====
		   $AMSQL= "UPDATE ".ADMISSION_TBL." SET discount_type=".$discount_type.",discount_percentage=".$discount_percentage.",discount_amount=".$discount_amount.",less_tuitionfee=".$less_tuitionfee.",concession_on=".$concession_on.",net_amount=".$net_bill_amount." WHERE institute_id=".$institute_id." AND branch_id =".$branch_id." AND admission_id = ".$admission_id;
		   $this->db->query($AMSQL);
		   $BMSQL= "UPDATE ".BILL_MASTER_TBL." SET discount_persent=".$discount_percentage.",discount_amount=".$discount_amount.",net_bill_amount=".$net_bill_amount.",due_amount=".$due_amount." WHERE institute_id=".$institute_id." AND branch_id =".$branch_id." AND bill_id = ".$bill_id;
		   $this->db->query($BMSQL);
		   
		   $VMSQL= "UPDATE ".VOUCHER_MASTER_TBL." SET dr_amount='".$dr_amount."',cr_amount='".$cr_amount."' WHERE institute_id=".$institute_id." AND branch_id =".$branch_id." AND contra_id = ".$contra_id;
		   $this->db->query($VMSQL);		   
		   //==== Dr Account Update =====	
		   if($account_id >0 && $dr_amount >0){
			   $acsql = "SELECT * FROM ".VOUCHER_DETAILS_TBL." WHERE account_id ='".$account_id."' AND contra_id = $contra_id AND headtypes='Dr' AND status ='1'";
			   $aquery = $this->db->query($acsql);
			   if($aquery->num_rows() >0){				 
			     $DSQL= "UPDATE ".VOUCHER_DETAILS_TBL." SET amount='".$dr_amount."'  WHERE account_id='".$account_id."' AND contra_id = ".$contra_id." AND headtypes='Dr'"; $this->db->query($DSQL);			
			     $this->SaveAccountLedger($voucher_no,$bill_id,$voucher_date,$account_id,$voucher_type,$description,$dr_amount,"Dr","U");
			   }else{
			    //==== Start Dr Customer Account =====					
			    $DSQL="INSERT INTO ".VOUCHER_DETAILS_TBL."(contra_id,invoice_no,voucher_no,headtypes,account_id,amount,voucher_type,description,created_by) ";
			    $DSQL.="VALUES('".$contra_id."','".$bill_id."','".$voucher_no."','Dr','".$account_id."','".$dr_amount."','".$voucher_type."','".$description."','".$created_by."')";
			    $this->db->query($DSQL);
			    $this->SaveAccountLedger($voucher_no,$bill_id,$voucher_date,$account_id,$voucher_type,$description,$dr_amount,"Dr","I");
			   }//end else
		   }// End account_head 
		   
	       //===== Update Dr Discount Account =====
		   if($discount_amount >0 && $discount_head >0){
			   $dcsql = "SELECT * FROM ".VOUCHER_DETAILS_TBL." WHERE account_id ='".$discount_head."' AND contra_id = $contra_id AND headtypes='Dr' AND status ='1'";
			   $dquery = $this->db->query($dcsql);
			   if($dquery->num_rows() >0){			
			   $DSQL= "UPDATE ".VOUCHER_DETAILS_TBL." SET amount='".$discount_amount."', voucher_type='".$voucher_type."', description='".$d_description."' WHERE account_id='".$discount_head."' AND contra_id = ".$contra_id; $this->db->query($DSQL);			
			   $this->SaveAccountLedger($voucher_no,$bill_id,$voucher_date,$discount_head,$voucher_type,$d_description,$discount_amount,"Dr","U");
			   }else{
			    //==== Start Dr Discount Account =====					
			    $DSQL="INSERT INTO ".VOUCHER_DETAILS_TBL."(contra_id,invoice_no,voucher_no,headtypes,account_id,amount,voucher_type,description,created_by) ";
			    $DSQL.="VALUES('".$contra_id."','".$bill_id."','".$voucher_no."','Dr','".$discount_head."','".$discount_amount."','".$voucher_type."','".$d_description."','".$created_by."')";
			    $this->db->query($DSQL);
			    $this->SaveAccountLedger($voucher_no,$bill_id,$voucher_date,$discount_head,$voucher_type,$d_description,$discount_amount,"Dr","I");
			   }//end else
		   }
		   return 1;
		   
		}else{ return 0;}
	}	
    function GetDrAccountList($head_type){		
		$this->db->select('*');
		$this->db->from(ACC_HEAD_TBL);
		if(is_array($head_type)){
			$this->db->where_in("head_type", $head_type);
		}elseif($head_type >0){
			$this->db->where("head_type", $head_type);
		}
		$this->db->where('status', 1);
		$this->db->order_by('account_id','ASC');
		$query = $this->db->get(); //echo $this->db->last_query();
		return $query;
    }
    //===== Not Used ==========	
    function GetBankAccountList(){
		$sql = "SELECT sub_id FROM ".SUB_HEAD_TBL." WHERE sub_head_name ='Bank' 
		AND parents_id = 1";
		$pquery = $this->db->query($sql);
		if($pquery->num_rows() >0){
		$sub_head = $pquery->row()->sub_id;
		}else{
		$sub_head = 2;
		}
		$this->db->select('*');
		$this->db->from(ACC_HEAD_TBL);
		$this->db->where("sub_head", $sub_head); // 5=Accounts Receivable
		$this->db->where("head_type", 10); // 1=Customer
		$this->db->where('status', 1);
		$this->db->order_by('account_id','ASC');
		$query = $this->db->get();
		return $query;
    }	
    
    /*======Start Common Function for pagination=======*/

    function getPagination($totalrecord, $block)
    {
        $from_rs = $this->input->post('from');
        if ($from_rs == "") {
            $from_rs = 0;
        }
        if ($block == "") {
            $block = 12;
        }
        $to_rs = $from_rs + $block;
        if ($from_rs >= $block) {
            $from_rs = $from_rs + 1;
        }
        if ($from_rs == "" || $from_rs == 0) {
            $from_rs = 1;
        }
        if ($to_rs == "" || $totalrecord < $block) {
            $to_rs = $totalrecord;
        } else if ($to_rs == "" && $totalrecord > $block) {
            $to_rs = $block;
        }
        if ($to_rs > $totalrecord) {
            $to_rs = $totalrecord;
        }
        if ($totalrecord == 0) {
            $from_rs = 0;
        }

        $plink = $this->input->post('page_no');
        if ($plink == "") {
            $plink = 1;
        }
        if ($totalrecord > $block) {
            $res = $totalrecord / $block;
            $res = (int)$res;
            if (($totalrecord % $block) != 0) {
                $totalpage = $res + 1;
            } else {
                $totalpage = $res;
            }
        } else {
            $totalpage = 1;
        }
        $paginationStr = "";
        $paginationStr .= "<ul class='pagination pagination-sm m-0'>";

        if ($totalrecord > $block) {
            $two = $this->input->post('from');
            if ($two == "") {
                $two = 0;
            }
            $pno = $this->input->post('page_no');
            if ($pno == "") {
                $pno = 0;
            }
            $pno = $pno - 1;
            $frm = $two - $block;
            $to = $block;
            if ($pno <= $totalpage && $pno > 0) {
                $paginationStr .= "<li class='page-item'><a class='page-link' onclick=nextPage($frm,$to,$pno) href='#'>&laquo;</a></li>";
            }
        } else {
            $paginationStr .= "<li class='page-item disabled'><a class='page-link' href='#'>&laquo;</a></li>";
        }
        if ($totalpage >= 1) {
            $i = 1;
            $from = 0;
            $to = $block;
            while ($i <= $totalpage) {
                if ($from == 0) {
                    $paginationStr .= "<li class='page-item'>";
                    $paginationStr .= "<a ";
					if ($i == $plink) {
                        $paginationStr .= "class='active-link'";
                    }else{
						$paginationStr .= "class='page-link'";
					}
					$paginationStr .= " onclick=nextPage($from,$to,$i) href='#'>$i</a></li>";
                } else {
                    $paginationStr .= "<li class='page-item'>";
                    $paginationStr .= "<a ";
					if ($i == $plink) {
                        $paginationStr .= "class='active-link'";
                    }else{
						$paginationStr .= "class='page-link'";
					}
					$paginationStr .= " onclick=nextPage($from,$to,$i) href='#'>$i</a></li>";
                }
                $i++;
                $from = $from + $block;
                if ($to > $totalrecord) {
                    $to = $totalrecord;
                }
            }
        }
        if ($totalrecord > $block) {
            $f = $this->input->post('from');
            $page = $this->input->post('page_no');
            $page = $page + 1;
            if ($f == "" || $f == 0) {
                $f = $block;
                $page = 2;
            } else {
                $f = $f + $block;
            }
            $t = $block;
            if ($t > $totalrecord) {
                $t = $totalrecord;
            }
            if ($page <= $totalpage) {
                $paginationStr .= "<li class='page-item'><a class='page-link' onclick=nextPage($f,$t,$page) href='#'>&raquo;</a></li>";
            }
        } else {
            $paginationStr .= "<li class='page-item disabled'><a class='page-link' href='#'>&raquo;</a></li>";
        }

        $paginationStr .= "</ul>";
        return $paginationStr;
    }
    /*======End Common Function for pagination=======*/
    
	function formatDate($dt)
	{
		if (trim($dt)) {
			$day = substr($dt, 0, 2);
			$month = substr($dt, 3, 2);
			$year = substr($dt, 6, 4);
			$hour = substr($dt, 11, 2);
			$minute = substr($dt, 14, 2);
			$second = substr($dt, 17, 2);
			$ampm = substr($dt, 20, 2);
			//echo $ampm;
			if ($hour == '' AND $minute == '' AND $second == '') {
				return $year . "-" . $month . "-" . $day;
			} else {
				if (strtoupper($ampm) == 'PM') {
					$hour = intval($hour) + 12;
					return $year . "-" . $month . "-" . $day . ' ' . $hour . ':' . $minute . ':' . $second;
				} else {
					return $year . "-" . $month . "-" . $day . ' ' . $hour . ':' . $minute . ':' . $second;
				}
			}
		}
	}

	function formatDateTimeDMY($dt)
	{
		if (trim($dt)) {
			$year = substr($dt, 0, 4);
			$month = substr($dt, 5, 2);
			$day = substr($dt, 8, 2);
			$hour = substr($dt, 11, 2);
			$minute = substr($dt, 14, 2);
			$second = substr($dt, 17, 2);
			$ampm = substr($dt, 20, 2);
			if ($hour == '' AND $minute == '' AND $second == '') {
				return $year . "-" . $month . "-" . $day;
			} else {
				if (strtoupper($ampm) == 'PM') {
					$hour = intval($hour) + 12;
					return $day . "-" . $month . "-" . $year . ' ' . $hour . ':' . $minute . ':' . $second;
				} else {
					return $day . "-" . $month . "-" . $year . ' ' . $hour . ':' . $minute . ':' . $second;
				}
			}
		}
	}

	function formatDateDMY($val)
	{
		if ($val) {
			$yy = substr($val, 0, 4);
			$mm = substr($val, 5, 2);
			$dd = substr($val, 8, 2);
			return $dd . '-' . $mm . '-' . $yy;
		}
	}

	function dateInputFormatDMY($val)
	{
		if ($val) {
			$yy = substr($val, 0, 4);
			$mm = substr($val, 5, 2);
			$dd = substr($val, 8, 2);
			return $dd . '-' . $mm . '-' . $yy;
		}
	}
	function dateDisplayFormat($input_date)
	{
		if ($input_date) {
			$query = $this->db->query("SELECT DATE_FORMAT( '$input_date', '%d %b %Y' ) AS ctc_date");				
			return $query->row()->ctc_date;
		}
	}

   //End Class
}
