<?php
class SupplierOpening
{
   function run() {     
		$cmd    = getRequest('cmd');
		$u_t_id = getFromSession('u_type_id');
		if($u_t_id == 101 || $u_t_id == 102 || $u_t_id == 106) 
		{      
		  switch($cmd) { 
		  	 case 'add'                	: $screen = $this->showEditor($msg); break;
      	     case 'edit'               	: $screen = $this->showEditor("Edit Page");    break;			 
      	   	 case 'doUpdate'           	: $screen = $this->showEditor($msg); break;
		     case 'delete'             	: $screen = $this->deleteItem(); break;
			 default                   	: $cmd = 'list'; $screen = $this->showEditor($msg);   break;
		  }
		}else {
      		header("location:index.php?app=user_home&msg=You are not authorised !!!");
      	} 
		
		return true;
   }
  function showEditor()
  {
	 require_once(CLASS_DIR.'/common.class.php');	
	 $comApp = new Common(); 
	 require_once(CLASS_DIR.'/common.list.class.php');	
	 $comListApp 	= new CommonList(); 
	 $supplier_code = getRequest('id');	 
	 $data               = array();		
	 if($supplier_code){
	 $TBDArr			= $comApp->getRecordInfo(SUPPLIER_TBL,"supplier_code",$supplier_code);      
	 $TBDArr 			= parseThisValue($TBDArr);
	 $data        		= array_merge(array(),$TBDArr);		 			
	 if(getRequest('save')){
		$comApp->updateRecord(SUPPLIER_TBL,"supplier_code",$supplier_code,"","","","","supplier","list");
		$msg="Successfully Update Record !!!";
		header("location:?app=supplier&cmd=list&msg=$msg");	      	
	 } 
	} else {		
	if(getRequest('save')) {
		$supplier_code = $comApp->NewID(SUPPLIER_TBL,"supplier_code","S520000","S",7);
		$comApp->saveRecord(SUPPLIER_TBL,"supplier_code","$supplier_code","","","created_by","created_date","supplier","list");
		$msg="Successfully Save Record !!!";
		header("location:?app=supplier&cmd=list&msg=$msg");     		       		      	
	 }			 
	}
	
	$f1Value = getRequest('srckey');
	$from =getRequest('from'); if($from==""){ $from=0;} $to =getRequest('to'); if($to==""){ $to=20;}
	$data['supplier_list']  = $comApp->getRecords(SUPPLIER_TBL,"supplier_code","","sub_head_name",$f1Value,"","",$from,$to);
	$data['totalrecord']  	= $comApp->getTotalRecords(SUPPLIER_TBL,"supplier_code","","sub_head_name",$f1Value,"","");
	$data['message'] 		= $msg;
	$data['cmd']     		= getRequest('cmd'); 
	
	/*
	$project_id     = getFromSession('project_id');   
	$sql= "SELECT * FROM ".SUPPLIER_TBL." WHERE project_id = '$project_id' AND op_type!=''";
	$res = mysql_query($sql);
	while($arow=mysql_fetch_object($res)){
	$Acc_id = $arow->supplier_code; $op_amount = $arow->opening_balance;  $op_type = $arow->op_type; $purchase_amount = $arow->total_value;
	$paid_amount = $arow->paid_amount; $return_amount = $arow->return_amount; $baddebt_amount = $arow->baddebt_amount;
	$this->saveInPurchaseTbl($Acc_id,$op_amount,$op_type,$purchase_amount,$paid_amount,$return_amount,$baddebt_amount,"2014-02-28");
	}
	*/
	
	require_once(CURRENT_APP_SKIN_FILE);
	return $data[0];
   }   
   function deleteItem(){
	require_once(CLASS_DIR.'/common.class.php');	
	$comApp = new Common(); 
	$supplier_code = getRequest('id');
	$comApp->deleteRecord(SUPPLIER_TBL,"supplier_code",$supplier_code,"supplier","list"); 
   } 
   
   
   function saveInPurchaseTbl($supplier,$op_amount,$op_type,$purchase_amount,$paid_amount,$return_amount,$baddebt_amount,$purchase_date){
	require_once(CLASS_DIR.'/purchase.class.php');	
	$PurApp 		= new Purchase();	
	require_once(CLASS_DIR.'/sales.class.php');	
	$salesApp 			= new Sales();
	if($op_amount==""){ $op_amount=0; $op_type="Dr";} if($purchase_amount==""){ $purchase_amount=0;} if($paid_amount==""){ $paid_amount=0;} 
	if($return_amount==""){ $return_amount=0;} if($baddebt_amount==""){ $baddebt_amount=0;}
	$ob_date			= "2014-01-01";
	$voucher_no 	= $PurApp->createVoucharID();
	$created_date   = date('2014-02-28');
	$project_id     = getFromSession('project_id');    
	$created_by     = getFromSession('userid');
	
	if($op_type=="Dr"){			
	$totalPartyCR  = $PurApp->getTotalCreditAmount($supplier,getFromSession('project_id'));
	$totalPartyDR  = $PurApp->getTotalDebitAmount($supplier,getFromSession('project_id'));					 
	$PartyBalance  = (($totalPartyDR+$op_amount)-$totalPartyCR);						 
	$PurApp->saveAccountJournal($voucher_no,$supplier,"Supplier",$project_id,"OB",$op_amount,0,$PartyBalance,1,$ob_date);
	}elseif($op_type=="Cr"){		
	$CrReturn = $op_amount;		
	$totalPartyCR  = $PurApp->getTotalCreditAmount($supplier,getFromSession('project_id'));
	$totalPartyDR  = $PurApp->getTotalDebitAmount($supplier,getFromSession('project_id'));					 
	$PartyBalance  = ($totalPartyDR-($totalPartyCR+$op_amount));						 
	$PurApp->saveAccountJournal($voucher_no,$supplier,"Supplier",$project_id,"OB",0,$op_amount,$PartyBalance,1,$ob_date);
	}
	
	if($purchase_amount>0){ // === Cr ====	Opening Purchase is Opening Purchase Amount		
	$totalPartyCR  = $PurApp->getTotalCreditAmount($supplier,getFromSession('project_id'));
	$totalPartyDR  = $PurApp->getTotalDebitAmount($supplier,getFromSession('project_id'));					 
	$PartyBalance  = ($totalPartyDR-($totalPartyCR+$purchase_amount));						 
	$PurApp->saveAccountJournal($voucher_no,$supplier,"Supplier",$project_id,"Opening Purchase Amount",0,$purchase_amount,$PartyBalance,1,$purchase_date);
	}	
	if($paid_amount>0){	// === Dr ===	Opening Payment is Opening Payment
	$totalPartyCR  = $PurApp->getTotalCreditAmount($supplier,getFromSession('project_id'));
	$totalPartyDR  = $PurApp->getTotalDebitAmount($supplier,getFromSession('project_id'));					 
	$PartyBalance  = (($totalPartyDR+$paid_amount)-$totalPartyCR);						 
	$PurApp->saveAccountJournal($voucher_no,$supplier,"Supplier",$project_id,"Opening Payment",$paid_amount,0,$PartyBalance,1,$purchase_date);
	}	
	if($return_amount>0){	// === Dr ===	Opening Purchase Return is OPR
	$totalPartyCR  = $PurApp->getTotalCreditAmount($supplier,getFromSession('project_id'));
	$totalPartyDR  = $PurApp->getTotalDebitAmount($supplier,getFromSession('project_id'));					 
	$PartyBalance  = (($totalPartyDR+$return_amount)-$totalPartyCR);						 
	$PurApp->saveAccountJournal($voucher_no,$supplier,"Supplier",$project_id,"OPR",$return_amount,0,$PartyBalance,1,$purchase_date);
	}		
	if($baddebt_amount>0){	// === Dr ===	Opening Purchase Baddebt is OPBD
	$totalPartyCR  = $PurApp->getTotalCreditAmount($supplier,getFromSession('project_id'));
	$totalPartyDR  = $PurApp->getTotalDebitAmount($supplier,getFromSession('project_id'));					 
	$BDBalance     = (($totalPartyDR+$baddebt_amount)-$totalPartyCR);						 
	$PurApp->saveAccountJournal($voucher_no,$supplier,"Supplier",$project_id,"OPBD",$baddebt_amount,0,$BDBalance,1,$purchase_date);
	}
	
	//====== get Party Balance for due ====
	if($op_type=="Dr"){	
	$credit 	= $purchase_amount;
	$paidamount = ($op_amount+$paid_amount+$return_amount+$baddebt_amount);
	$due 		= (($purchase_amount)-($op_amount+$paid_amount+$return_amount+$baddebt_amount));
	$adjust 	= $op_amount; 
	}else{
	$credit 	= ($op_amount+$purchase_amount);
	$paidamount = ($paid_amount+$return_amount+$baddebt_amount);
	$due 		= (($op_amount+$purchase_amount)-($paid_amount+$return_amount+$baddebt_amount));
	$adjust 	= "-$op_amount";
	}
	
	if($due>0){ 
	$vouchar_type ='Payable Vouchar'; $status = 0; $debit = $op_amount; $paidamount =  ($paid_amount+$return_amount+$baddebt_amount);
	$sqlDV="INSERT INTO ".CREDIT_VOUCHAR_TBL."(voucher_no,account_head,project_id,head_type,transaction_type,vouchar_type,transaction_name,credit,list_view,created_by,created_date) 
	VALUES('$voucher_no','$supplier','$project_id','Supplier','Opening Balance','$vouchar_type','Purchase Opening','$due','Active','$created_by','$created_date')";
	$res1= mysql_query($sqlDV);
	
	$sqlCV="INSERT INTO ".DEVIT_VOUCHAR_TBL."(voucher_no,account_head,project_id,head_type,transaction_type,vouchar_type,transaction_name,debit,paid_amount,due,list_view,created_by,
	created_date,status) VALUES('$voucher_no','A000014','$project_id','Supplier','Opening Balance','$vouchar_type','Purchase Opening','$due','0','$due','Active','$created_by','$created_date','0')";
	$res2=mysql_query($sqlCV);	
	}else{		
	$vouchar_type ='Recievable Vouchar'; $debit = abs($due);
	$sqlDV="INSERT INTO ".CREDIT_VOUCHAR_TBL."(voucher_no,account_head,project_id,head_type,transaction_type,vouchar_type,transaction_name,credit,list_view,created_by,created_date) 
	VALUES('$voucher_no','A000014','$project_id','OP','Opening Balance','$vouchar_type','Purchase Opening','$debit','Active','$created_by','$created_date')";
	$res1= mysql_query($sqlDV);
	$sqlCV="INSERT INTO ".DEVIT_VOUCHAR_TBL."(voucher_no,account_head,project_id,head_type,transaction_type,vouchar_type,transaction_name,debit,paid_amount,due,list_view,created_by,
	created_date,status) VALUES('$voucher_no','$supplier','$project_id','Supplier','Opening Balance','$vouchar_type','Purchase Opening','$debit','0','$debit','Active','$created_by','$created_date','0')";
	$res2=mysql_query($sqlCV);
	}
		
	$opSQL="INSERT INTO ".OPENING_BALANCE_TBL."(voucher_no,project_id,head_id,head_type,opening_balance,op_type,opening_month,created_by) 			
	VALUES('$voucher_no','$project_id','$supplier','Supplier','$op_amount','$op_type','01','$created_by')";
	mysql_query($opSQL); 
	return $voucher_no;
   }
    
} // End class
?>