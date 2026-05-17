<?php
class VoucherEdit
{
   
   function run()
   {         
      $cmd = getRequest('cmd');
	  $u_t_id = getFromSession('u_type_id');

      if( ($u_t_id == 101)) // 101 = sysadmin, 102 = admin, 104= accounce
      {
      	switch ($cmd)
      	{
      	   case 'edit'               	: $screen = $this->showEditor("Edit Page");    break;	
      	   case 'print_vouchar'         : $screen = $this->showPrintEditor($msg); break;
	   case 'save_tmp'  		: $this->saveTempVoucher(); break;   
	   case 'deltemp'		: $this->delTempVoucher(); break;     
	   case 'save_vouchar'		: $this->saveVoucher(); break;
	   case 'delete'               	: $screen = $this->deleteVoucher("Edit Page");    break;
	   case 'list'               	: $screen = $this->showVoucherList($msg);   break;
      	   default                   	: $screen = $this->showVoucherList($msg); break;
      	}
      }elseif( (($u_t_id == 102) || ($u_t_id == 104))) // 101 = sysadmin, 102 = admin, 104= accounce
      {
      	if(getFromSession('userid')!="jahid_acc"){ // only 4 samrat erp
		switch ($cmd)
		{
		   //case 'edit'               	: $screen = $this->showEditor("Edit Page");    break;	
		   case 'print_vouchar'         : $screen = $this->showPrintEditor($msg); break;
		   case 'save_tmp'  		: $this->saveTempVoucher(); break;   
		   case 'deltemp'		: $this->delTempVoucher(); break;     
		   //case 'save_vouchar'		: $this->saveVoucher(); break;		   
		   case 'list'               	: $screen = $this->showVoucherList($msg);   break;
		   default                   	: $screen = $this->showVoucherList($msg); break;
		}
		}else{
			header("location:index.php?app=user_home&msg=You are not authorised !!!");
		}
      }elseif(($u_t_id == 102) || ($u_t_id == 104)) // 101 = sysadmin, 102 = admin, 104= accounce
      {
      	switch ($cmd)
      	{
      	   //case 'edit'               	: $screen = $this->showEditor("Edit Page");    break;	
      	   case 'print_vouchar'         : $screen = $this->showPrintEditor($msg); break;
	   case 'save_tmp'  		: $this->saveTempVoucher(); break;   
	   case 'deltemp'		: $this->delTempVoucher(); break;     
	   //case 'save_vouchar'		: $this->saveVoucher(); break;
	   case 'list'          	: $screen = $this->showVoucherList($msg);   break;
      	   default                   	: $screen = $this->showVoucherList($msg); break;
      	}
      }else {
      	header("location:index.php?app=user_home&msg=You are not authorised !!!");
      }     	          
      return true;
   }   

   function showList($msg = null) {  
	  $data                			= array();
	  $data['cmd']         			= getRequest('cmd');
	  $data['voucher_list']			= $this->getContraVoucherList(getRequest('from'),getRequest('to'));
	  $data['totalrecord']			= $this->getTotalContraVoucherList(); 
	  require_once(CONTRA_VOUCHER_SKIN_LIST); 
	  return $data[0];

   }
   
  function showPrintEditor($msg = null) {   	  
	  $contra_id 	= getRequest('contra_id');  
	  if ($contra_id) {
         	 $advArr 		= $this->getContraMasterInfo($contra_id);
         	 $advArr 		= parseThisValue($advArr); 
		 $data   		= array_merge(array(), $advArr); 
		 $data['item_list']	= $this->getContraDetails($contra_id);
		 $data['message'] 	= $msg;
		 $data['cmd']     	= getRequest('cmd');
		 require_once(PRNIT_CONTRA_VOUCHER_SKIN);      
		 return true;
	 }
   }
   
  function rollbackCustomerReceibavle($voucher_no){
	$project_id = getFromSession('project_id');
	$getSql	= "SELECT * FROM ".VOUCHER_ADJUST_HISTORY_TBL." WHERE voucher_no = '".$voucher_no."' AND project_id='".getFromSession('project_id')."'";
	$gres 	= mysql_query($getSql);
	if(mysql_num_rows($gres)>0){
	  while($row = mysql_fetch_object($gres)){
		 $adjust_tbl 	= $row->adjust_tbl; 
		 $adjust_ref 	= $row->adjust_ref;  
		 $adjust_amount = $row->adjust_amount; 
		 $adjust_type	= $row->adjust_type;
		 $adjust_tblArr = explode(".",$adjust_tbl);
		 $adjust_tbl_name = $adjust_tblArr[1];
		 //======= rollback previous sales amount =========
		 if($adjust_tbl_name=="sales_master" && $adjust_type=="+"){			 
			$getsSql= "SELECT * FROM ".SALES_MASTER_TBL." WHERE voucher_no = '".$adjust_ref."' AND project_id='".getFromSession('project_id')."'";
			$gsres  = mysql_query($getsSql);
			$srow = mysql_fetch_object($gsres);
			$paid_amount = ($srow->paid_amount-$adjust_amount);
			$due 		 = ($srow->due+$adjust_amount); 
			$Usql="UPDATE ".SALES_MASTER_TBL." SET paid_amount='$paid_amount',due='$due' WHERE voucher_no='".$adjust_ref."' AND project_id='".$project_id."'";
			mysql_query($Usql);
		 }elseif(($adjust_tbl_name=="cs_delivery_product" || $adjust_tbl_name=="devit_vouchar") && ($adjust_type=="+")){	 
		 	//======= rollback previous recievable amount =========		 
			$getdSql= "SELECT * FROM ".DEVIT_VOUCHAR_TBL." WHERE voucher_no = '".$adjust_ref."' AND project_id='".getFromSession('project_id')."'";
			$gdres  = mysql_query($getdSql);
			$drow = mysql_fetch_object($gdres);
			$paid_amount = ($drow->paid_amount-$adjust_amount);
			$due 		  = ($drow->due+$adjust_amount); 
			$Usql="UPDATE ".DEVIT_VOUCHAR_TBL." SET paid_amount='$paid_amount',due='$due',status=0 WHERE voucher_no='".$adjust_ref."' 
			AND project_id='".$project_id."'";
			mysql_query($Usql);
		 }elseif($adjust_tbl_name=="purchase_return_receivable" && $adjust_type=="+"){
			//======= rollback previous purchase return recievable amount =========			 
			$getdSql= "SELECT * FROM ".PURCHASE_RETURN_RECEIBAVLE_TBL." WHERE return_id='".$adjust_ref."' AND project_id='".getFromSession('project_id')."' 
			AND paid_amount >0";
			$gdres  = mysql_query($getdSql);
			$drow = mysql_fetch_object($gdres);
			$paid_amount = ($drow->paid_amount-$adjust_amount);
			$due 		 = ($drow->due+$adjust_amount); 
			$Usql="UPDATE ".PURCHASE_RETURN_RECEIBAVLE_TBL." SET paid_amount='$paid_amount',due='$due' WHERE return_id='".$adjust_ref."' 
			AND project_id='".$project_id."'";
			mysql_query($Usql);
		 }elseif($adjust_tbl_name=="sales_return_payble" && $adjust_type=="Payble ROA"){
			//======= delete previous advanced received payble amount =========			 
			$Usql="DELETE FROM ".SALES_RETURN_PAYBLE_TBL." WHERE voucher_no='".$adjust_ref."' AND project_id='".$project_id."'";
			mysql_query($Usql);
		 }
	  }
	}
  }
  function rollbackCustomerPayble($voucher_no){
	$project_id = getFromSession('project_id');
	$getSql	= "SELECT * FROM ".VOUCHER_ADJUST_HISTORY_TBL." WHERE voucher_no = '".$voucher_no."' AND project_id='".getFromSession('project_id')."'";
	$gres 	= mysql_query($getSql);
	if(mysql_num_rows($gres)>0){
	  while($row = mysql_fetch_object($gres)){
		 $adjust_tbl 	= $row->adjust_tbl; 
		 $adjust_ref 	= $row->adjust_ref;  
		 $adjust_amount = $row->adjust_amount; 
		 $adjust_type	= $row->adjust_type;
		 $adjust_tblArr = explode(".",$adjust_tbl);
		 $adjust_tbl_name = $adjust_tblArr[1];
		 //======= rollback previous opening payble amount =========
		 if(($adjust_tbl_name=="cs_delivery_product" || $adjust_tbl_name=="devit_vouchar") && ($adjust_type=="-")){			 
			$getsSql= "SELECT * FROM ".DEVIT_VOUCHAR_TBL." WHERE voucher_no = '".$adjust_ref."' AND project_id='".getFromSession('project_id')."'";
			$gsres  = mysql_query($getsSql);
			$srow = mysql_fetch_object($gsres);
			$paid_amount = ($srow->paid_amount-$adjust_amount);
			$due 		  = ($srow->due+$adjust_amount); 
			$Usql="UPDATE ".DEVIT_VOUCHAR_TBL." SET paid_amount='$paid_amount',due='$due',`status`=0 WHERE voucher_no='".$adjust_ref."' 
			AND project_id='".$project_id."'";
			mysql_query($Usql);
		 }elseif(($adjust_tbl_name=="sales_return_payble") && ($adjust_type=="-")){	 
		 	//======= rollback previous sales return/advanced paid payble amount =========		 
			$getdSql= "SELECT * FROM ".SALES_RETURN_PAYBLE_TBL." WHERE return_id = '".$adjust_ref."' AND project_id='".getFromSession('project_id')."'";
			$gdres  = mysql_query($getdSql);
			$drow = mysql_fetch_object($gdres);
			$paid_amount = ($drow->paid_amount-$adjust_amount);
			$due 		  = ($drow->due+$adjust_amount); 
			$Usql="UPDATE ".SALES_RETURN_PAYBLE_TBL." SET paid_amount='$paid_amount',due='$due' WHERE return_id='".$adjust_ref."' 
			AND project_id='".$project_id."'";
			mysql_query($Usql);		
			
		 }elseif($adjust_tbl_name=="purchase_return_receivable" && $adjust_type=="Receibavle ROA"){
			//======= delete previous purchase return/advanced payment payble amount =========			 
			$Usql="DELETE FROM ".PURCHASE_RETURN_RECEIBAVLE_TBL." WHERE voucher_no='".$adjust_ref."' AND project_id='".$project_id."'";
			mysql_query($Usql);
		 }
	  }// end while
	}
  }
  
  function rollbackSupplierReceibavle($voucher_no){
	$project_id = getFromSession('project_id');
	$getSql	= "SELECT * FROM ".VOUCHER_ADJUST_HISTORY_TBL." WHERE voucher_no = '".$voucher_no."' AND project_id='".getFromSession('project_id')."'";
	$gres 	= mysql_query($getSql);
	if(mysql_num_rows($gres)>0){
	  while($row = mysql_fetch_object($gres)){
		 $adjust_tbl 	= $row->adjust_tbl; 
		 $adjust_ref 	= $row->adjust_ref;  
		 $adjust_amount = $row->adjust_amount; 
		 $adjust_type	= $row->adjust_type;
		 $adjust_tblArr = explode(".",$adjust_tbl);
		 $adjust_tbl_name = $adjust_tblArr[1];
		 //======= rollback previous opening recievable amount =========
		 if(($adjust_tbl_name=="cs_delivery_product" || $adjust_tbl_name=="devit_vouchar") && ($adjust_type=="-")){			 
			$getsSql= "SELECT * FROM ".DEVIT_VOUCHAR_TBL." WHERE voucher_no = '".$adjust_ref."' AND project_id='".getFromSession('project_id')."'";
			$gsres  = mysql_query($getsSql);
			$srow = mysql_fetch_object($gsres);
			$paid_amount = ($srow->paid_amount-$adjust_amount);
			$due 		 = ($srow->due+$adjust_amount); 
			$Usql="UPDATE ".DEVIT_VOUCHAR_TBL." SET paid_amount='$paid_amount',due='$due',status=0 WHERE voucher_no='".$adjust_ref."' 
			AND project_id='".$project_id."'";
			mysql_query($Usql);
		 }elseif($adjust_tbl_name=="purchase_return_receivable" && $adjust_type=="-"){
			//======= rollback previous my purchase return recievable amount =========			 
			$getdSql= "SELECT * FROM ".PURCHASE_RETURN_RECEIBAVLE_TBL." WHERE return_id='".$adjust_ref."' AND project_id='".getFromSession('project_id')."' 
			AND paid_amount >0";
			$gdres  = mysql_query($getdSql);
			$drow = mysql_fetch_object($gdres);
			$paid_amount = ($drow->paid_amount-$adjust_amount);
			$due 		 = ($drow->due+$adjust_amount); 
			$Usql="UPDATE ".PURCHASE_RETURN_RECEIBAVLE_TBL." SET paid_amount='$paid_amount',due='$due' WHERE return_id='".$adjust_ref."' 
			AND project_id='".$project_id."'";
			mysql_query($Usql);
		 }elseif($adjust_tbl_name=="sales_return_payble" && $adjust_type=="Payble ROA"){
			//======= delete previous advanced received payble amount =========			 
			$Usql="DELETE FROM ".SALES_RETURN_PAYBLE_TBL." WHERE voucher_no='".$adjust_ref."' AND project_id='".$project_id."'";
			mysql_query($Usql);
		 }
	  }
	}  
  }
  function rollbackSupplierPayble($voucher_no){
	$project_id = getFromSession('project_id');
	$getSql	= "SELECT * FROM ".VOUCHER_ADJUST_HISTORY_TBL." WHERE voucher_no = '".$voucher_no."' AND project_id='".getFromSession('project_id')."'";
	$gres 	= mysql_query($getSql);
	if(mysql_num_rows($gres)>0){
	  while($row = mysql_fetch_object($gres)){
		 $adjust_tbl 	= $row->adjust_tbl; 
		 $adjust_ref 	= $row->adjust_ref;  
		 $adjust_amount = $row->adjust_amount; 
		 $adjust_type	= $row->adjust_type;
		 $adjust_tblArr = explode(".",$adjust_tbl);
		 $adjust_tbl_name = $adjust_tblArr[1];
		 //======= rollback previous purchase amount =========
		 if(($adjust_tbl_name==" purchase_master") && ($adjust_type=="+")){			 
			$getsSql= "SELECT * FROM ".PURCHASE_MASTER_TBL." WHERE voucher_no = '".$adjust_ref."' AND project_id='".getFromSession('project_id')."'";
			$gsres  = mysql_query($getsSql);
			$srow = mysql_fetch_object($gsres);
			$paid_amount = ($srow->paid_amount-$adjust_amount);
			$due 		 = ($srow->due+$adjust_amount); 
			$Usql="UPDATE ".PURCHASE_MASTER_TBL." SET paid_amount='$paid_amount',due='$due' WHERE voucher_no='".$adjust_ref."' 
			AND project_id='".$project_id."'";
			mysql_query($Usql);
		 }elseif(($adjust_tbl_name=="cs_delivery_product" || $adjust_tbl_name=="devit_vouchar") && ($adjust_type=="+")){
			//======= rollback previous opening payable amount =========			 
			$getsSql= "SELECT * FROM ".DEVIT_VOUCHAR_TBL." WHERE voucher_no = '".$adjust_ref."' AND project_id='".getFromSession('project_id')."'";
			$gsres  = mysql_query($getsSql);
			$srow = mysql_fetch_object($gsres);
			$paid_amount = ($srow->paid_amount-$adjust_amount);
			$due 		 = ($srow->due+$adjust_amount); 
			$Usql="UPDATE ".DEVIT_VOUCHAR_TBL." SET paid_amount='$paid_amount',due='$due',status=0 WHERE voucher_no='".$adjust_ref."' 
			AND project_id='".$project_id."'";
			mysql_query($Usql);
		 }elseif($adjust_tbl_name=="sales_return_payble" && $adjust_type=="+"){
			//======= rollback previous my advanced paid payable amount =========			 
			$getdSql= "SELECT * FROM ".SALES_RETURN_PAYBLE_TBL." WHERE return_id='".$adjust_ref."' AND project_id='".getFromSession('project_id')."' 
			AND paid_amount >0";
			$gdres  = mysql_query($getdSql);
			$drow = mysql_fetch_object($gdres);
			$paid_amount = ($drow->paid_amount-$adjust_amount);
			$due 		 = ($drow->due+$adjust_amount); 
			$Usql="UPDATE ".SALES_RETURN_PAYBLE_TBL." SET paid_amount='$paid_amount',due='$due' WHERE return_id='".$adjust_ref."' 
			AND project_id='".$project_id."'";
			mysql_query($Usql);
		 }elseif($adjust_tbl_name=="purchase_return_receivable" && $adjust_type=="Receibavle ROA"){
			//======= delete previous advanced received payble amount =========			 
			$Usql="DELETE FROM ".PURCHASE_RETURN_RECEIBAVLE_TBL." WHERE voucher_no='".$adjust_ref."' AND project_id='".$project_id."'";
			mysql_query($Usql);
		 }
	  }
	}  
  }
  function deletePreviousVoucher($voucher_no){
	$project_id = getFromSession('project_id');
	$Dsql="DELETE FROM ".DEVIT_VOUCHAR_TBL." WHERE voucher_no='".$voucher_no."' AND project_id='".$project_id."'";
	mysql_query($Dsql); 
	$Csql="DELETE FROM ".CREDIT_VOUCHAR_TBL." WHERE voucher_no='".$voucher_no."' AND project_id='".$project_id."'";
	mysql_query($Csql);
	$Jsql="DELETE FROM ".ACCOUNT_JOURNAL_TBL." WHERE voucher_no='".$voucher_no."' AND project_id='".$project_id."'";
	mysql_query($Jsql); 
	$Hsql="DELETE FROM ".VOUCHER_ADJUST_HISTORY_TBL." WHERE voucher_no='".$voucher_no."' AND project_id='".$project_id."'";
	mysql_query($Hsql);
  }
  function deleteVoucher($msg = null) { 
        require_once(CLASS_DIR.'/common.list.class.php');	
	$clistApp 	= new CommonList(); 
	$data	  	= array();
	$voucher_no = getRequest('voucher_no');
	$project_id = getFromSession('project_id');
	$created_by = getFromSession('userid');
	if(!userCondition(true)){
		$msg = "You are not authorized!!!";
	      	header("location:index.php?app=voucher.edit&cmd=list&msg=$msg");
	      	exit;
	}

	if($voucher_no!=""){
	$getdSql= "SELECT * FROM ".DEVIT_VOUCHAR_TBL." WHERE voucher_no='".$voucher_no."' AND project_id='".$project_id."'";
	$gdres  = mysql_query($getdSql);
	$drow = mysql_fetch_object($gdres);
	$vouchar_type  = $drow->vouchar_type;
	$dr_account    = $drow->account_head;
	$befoure_amount= $drow->debit;
	$getCSql= "SELECT * FROM ".CREDIT_VOUCHAR_TBL." WHERE voucher_no='".$voucher_no."' AND project_id='".$project_id."'";
	$gcres  = mysql_query($getCSql);
	$crow = mysql_fetch_object($gcres);	
	$cr_account = $crow->account_head;
	//========= Rollback for Delete===========
	mysql_query("START TRANSACTION;");
	if($vouchar_type!="Payable Vouchar" && $vouchar_type!="Recievable Vouchar"){ 
		$HeadType 		  = getHeadType($dr_account);
		if($HeadType=="Cash" || $HeadType=="Bank"){
			$head_type  = getHeadType($cr_account);
			if($head_type=="Customer"){
			$this->rollbackCustomerReceibavle($voucher_no);
			}elseif($head_type=="Supplier"){
			$this->rollbackSupplierReceibavle($voucher_no);
			}	
		}else{
			$head_type 		= getHeadType($dr_account);
			if($head_type=="Supplier"){
			$this->rollbackSupplierPayble($voucher_no);
			}elseif($head_type=="Customer"){		
			$this->rollbackCustomerPayble($voucher_no);
			}	
		}	
	}//end vouchar_type
	
	//========= Delete for Edit===========
	 $this->deletePreviousVoucher($voucher_no);		
	
	SaveActivityLog("General Voucher",$voucher_no,"Delete",$created_by,$befoure_amount,0);

	mysql_query("COMMIT;");
	header("location:index.php?app=voucher.edit&cmd=list");		
	}else{
	header("location:index.php?app=voucher.edit&cmd=list");		
	}
   }
   //================ End Delete Voucher ===============
   function showEditor($msg = null) {    
    require_once(CLASS_DIR.'/common.list.class.php');	
	$clistApp 	= new CommonList(); 
	$data	  	= array();
	$voucher_no = getRequest('voucher_no');
	$project_id = getFromSession('project_id');
	if($_POST['submit']){	
	
	$msql = "SELECT * FROM ".DEVIT_VOUCHAR_TBL." WHERE voucher_no='".$voucher_no."' AND project_id='".$project_id."'";
	$mrow = mysql_fetch_object(mysql_query($msql));
	$befoure_amount = $mrow->debit;
	$created_by     = getFromSession('userid');
	if(!userCondition() && getFromSession('u_type_id') != 101){
		$msg = "You are not authorized!!!";
	      	header("location:index.php?app=voucher.edit&cmd=list&msg=$msg");
	      	exit;
	}
	
	$payment_mode 	= getRequest('mode_of_payment');
	$vouchar_type 	= getRequest('vouchar_type');		
	$dr_account 	= getRequest('dr_account');
	$dr_amount 	= getRequest('amount');
	$cr_account	= getRequest('cr_account');
	$cr_amount 	= getRequest('amount');
	
	$created_date	= getRequest('created_date');
	$bank_name	= getRequest('bank_name'); 
	$acc_no		= getRequest('acc_no'); 
	$check_no	= getRequest('check_no'); 
	$check_date	= formatDate(getRequest('check_issue_date')); 
	$description	= getRequest('description');
	$description 	= str_replace("'","&rsquo;",$description);
	//========= Rollback for Edit===========
	mysql_query("START TRANSACTION;");
	if($vouchar_type!="Payable Vouchar" && $vouchar_type!="Recievable Vouchar"){ 
		$HeadType 		  = getHeadType($dr_account);
		if($HeadType=="Cash" || $HeadType=="Bank"){
			//$head_type  = getHeadType($cr_account);
			$head_type  = getHeadType(getRequest('pvcr_account'));
			if($head_type=="Customer"){
			$this->rollbackCustomerReceibavle($voucher_no);
			}elseif($head_type=="Supplier"){
			$this->rollbackSupplierReceibavle($voucher_no);
			}	
		}else{
			//$head_type 		= getHeadType($dr_account);
			$head_type 		= getHeadType(getRequest('pvdr_account'));			
			$account_head 	= $dr_account;
			if($head_type=="Supplier"){
			$this->rollbackSupplierPayble($voucher_no);
			}elseif($head_type=="Customer"){		
			$this->rollbackCustomerPayble($voucher_no);
			}	
		}	
	}//end vouchar_type
	
	//========= Delete for Edit===========
	 $this->deletePreviousVoucher($voucher_no);
	//========= Save for Edit===========		
	$dvres = $this->saveDebitVouchar($voucher_no,$payment_mode,$vouchar_type,$dr_account,$dr_amount,$cr_account,$created_date,$bank_name,$acc_no,$check_no
	,$check_date);
	if($dvres){
	$this->saveCreditVouchar($voucher_no,$payment_mode,$vouchar_type,$dr_account,$cr_account,$cr_amount,$created_date,$bank_name,$acc_no,$check_no,$check_date
	,$description);	
	
	SaveActivityLog("General Voucher",$voucher_no,"Edit",$created_by,$befoure_amount,$dr_amount);
	mysql_query("COMMIT;");
	header("location:index.php?app=journal&cmd=vouchar_print&voucher_no=".$voucher_no);	
	}	
	}else{
		if ($voucher_no) {
		 $advArr = $this->getVoucherInfo($voucher_no);
		 $advArr = parseThisValue($advArr);  
		 $data   = array_merge(array(), $advArr); 
		}
	}
	$data['headlist1']   	= $clistApp->getAccountHeadList("Current Assets","S130"); // Cash & Bank
	$data['headlist2']   	= $clistApp->getAccountHeadList("Current Assets","S128","C000105"); // Party
	$data['headlist3']   	= $clistApp->getAccountHeadList("Non Current Assets","","","S130","C000105"); 
	$data['headlist4']   	= $clistApp->getAccountHeadList("Current Assets","","","S130","C000105");
	$data['headlist5']   	= $clistApp->getAccountHeadList("Liabilities");		
	$data['headlist6']   	= $clistApp->getAccountHeadList("Capital");	
	$data['headlist7']   	= $clistApp->getAccountHeadList("Retained earnings");	
	$data['headlist8']   	= $clistApp->getAccountHeadList("Sales");	
	$data['headlist9']   	= $clistApp->getAccountHeadList("Incomes");	
	$data['headlist10']   	= $clistApp->getAccountHeadList("Direct Expenses");		
	$data['headlist11']   	= $clistApp->getAccountHeadList("Indirect Expenses");	
	$data['headlist12']   	= $clistApp->getAccountHeadList("Opening Balance");	
	$data['headlist13']   	= $clistApp->getAccountHeadList("Adjustments Balance");	
	$data['headlist14']   	= $clistApp->getAccountHeadList("Closing Balance");	
	$data['supplier_list']  = $clistApp->getSupplierList();			   			
	$data['currency_list']  = $this->getCurrencyList();	   
	$data['message'] = $msg;
	$data['cmd']     = getRequest('cmd');
	require_once(CURRENT_APP_SKIN_FILE);      
	
	return true;

   }
	//==================== saveDebitVouchar ====================
 	function saveDebitVouchar($voucher_no,$payment_mode,$vouchar_type,$dr_account,$dr_amount,$cr_account,$created_date,$bank_name=NULL,$acc_no=NULL,$check_no=NULL,$check_date=NULL)
 	{     
		  $requestdata = array();
		  $mode_of_payment = $payment_mode;
		  $requestdata = getUserDataSet(DEVIT_VOUCHAR_TBL);			  	  		    
		  $requestdata['head_type']     	= getHeadType($dr_account);   
		  $requestdata['account_head']      = $dr_account; 
		  $requestdata['debit']        		= $dr_amount;    
		  $requestdata['credit']        	= 0; 
		  if($mode_of_payment =="Check"){
			$requestdata['mode_of_payment'] = "Bank";
			$requestdata['bank_name'] 		= $bank_name;
			$requestdata['acc_no'] 			= $acc_no;
			$requestdata['check_no'] 		= $check_no;
			$requestdata['check_issue_date']= $check_date;	
		  }else{
			$requestdata['bank_name']= "";
			$requestdata['acc_no']	 = "";
			$requestdata['check_no'] = "";
			$requestdata['check_issue_date']= "";  
		  }
		  $requestdata['project_id']      = getFromSession('project_id');    
		  $requestdata['created_by']      = getFromSession('userid'); 
		  $requestdata['created_date']    = formatDate($created_date);
		  if($vouchar_type=="Payable Vouchar"||$vouchar_type=="Recievable Vouchar"){
		    $requestdata['vouchar_type']= $vouchar_type;				
		  	$requestdata['due']   		= $dr_amount; 
		  	$requestdata['status']		= 0;   
		  }else{			
		  	$requestdata['paid_amount']   = $dr_amount;		  	
			if(getHeadType($dr_account)=="Cash" || getHeadType($dr_account)=="Bank"){
			$requestdata['vouchar_type'] = "Received Vouchar"; $requestdata['transaction_name'] = "Received";
			}elseif(getHeadType($cr_account)=="Cash" || getHeadType($cr_account)=="Bank"){
			$requestdata['vouchar_type'] = "Payment Vouchar"; $requestdata['transaction_name'] = "Payment";
			}
		  }
		 if($voucher_no != -1){
			$requestdata['voucher_no']   	= $voucher_no;
		  }else{
			$msg = "ID overflow !!!"; header("location:index.php?app=user_home&msg=$msg"); exit;
		  }	 
		  $info        		=  array();
		  $info['table']	= DEVIT_VOUCHAR_TBL;
		  $info['data'] 	= $requestdata;     
		  //$info['debug']  	=  true;
		  $res = insert($info);			
	
		  if($res['affected_rows']) {
			return true;
		  }else {	
			return false;	
		  }  

    }//EOFn  

    function saveCreditVouchar($voucher_no,$payment_mode,$vouchar_type,$dr_account,$cr_account,$cr_amount,$created_date,$bank_name=NULL,$acc_no=NULL,$check_no=NULL,$check_date=NULL,$description=NULL)
 	{     
	  $mode_of_payment = $payment_mode;
	  $requestdata = array();
	  $requestdata = getUserDataSet(CREDIT_VOUCHAR_TBL);
	  $requestdata['head_type']     	= getHeadType($cr_account);   
	  $requestdata['account_head']      = $cr_account; 
	  $requestdata['debit']        		= 0; 
	  $requestdata['credit']        	= $cr_amount;
	  if($mode_of_payment =="Check"){
		$requestdata['mode_of_payment'] = "Bank";
		$requestdata['bank_name'] 		= $bank_name;
		$requestdata['acc_no'] 			= $acc_no;
		$requestdata['check_no'] 		= $check_no;
		$requestdata['check_issue_date']= $check_date;	
	  }else{
		$requestdata['bank_name'] = "";
		$requestdata['acc_no'] = "";
		$requestdata['check_no'] = "";
		$requestdata['check_issue_date'] = "";  
	  }
	  $requestdata['project_id']        = getFromSession('project_id');    
	  $requestdata['created_by']        = getFromSession('userid'); 			 
	  $requestdata['created_date']      = formatDate($created_date);
	  //$requestdata['created_date']      = date('Y-m-d h:i:s');	
	  $requestdata['voucher_no']   		= $voucher_no;
	  if($vouchar_type!="Payable Vouchar" && $vouchar_type!="Recievable Vouchar"){			
		if(getHeadType($dr_account)=="Cash" || getHeadType($dr_account)=="Bank"){
		$requestdata['vouchar_type'] = "Received Vouchar"; $requestdata['transaction_name'] = "Received";
		}elseif(getHeadType($cr_account)=="Cash" || getHeadType($cr_account)=="Bank"){
		$requestdata['vouchar_type'] = "Payment Vouchar"; $requestdata['transaction_name'] = "Payment";
		}
	  }else{ $requestdata['vouchar_type'] = $vouchar_type; }
	  
	  $info        		=  array();
	  $info['table']	= CREDIT_VOUCHAR_TBL;
	  $info['data'] 	= $requestdata;     
	  //$info['debug']  	=  true;
	  $res = insert($info);	  

	  if($res['affected_rows']) {
		$project_id	 = getFromSession('project_id');
		$DrAmount 	 = $cr_amount;
		$CrAmount 	 = $cr_amount;
		$description = $description;
		$created_date = formatDate($created_date);
		if(getHeadType($dr_account)=="Cash" || getHeadType($dr_account)=="Bank"){
		 $transaction_type = "Received";
		}else{
		 $transaction_type = "Payment";
		}	
		//======= Dr Account ======	
		$PartyAcc_head = $dr_account; 
		$totalPartyCR  = $this->getTotalCreditAmount($PartyAcc_head,getFromSession('project_id'));
		$totalPartyDR  = $this->getTotalDebitAmount($PartyAcc_head,getFromSession('project_id'));					 
		$PartyBalance  = (($totalPartyDR+$DrAmount)-$totalPartyCR);					 
		$this->saveAccountJournal($voucher_no,$PartyAcc_head,$transaction_type,$project_id,$description,$DrAmount,0,$PartyBalance,0,$created_date);	
		//============== Cr Account ===============
		$acc_head = $cr_account; 
		$totalCR  = $this->getTotalCreditAmount($cr_account,getFromSession('project_id'));
		$totalDR  = $this->getTotalDebitAmount($cr_account,getFromSession('project_id'));					 
		$balance  = ($totalDR-($totalCR+$DrAmount));					 
		$this->saveAccountJournal($voucher_no,$cr_account,$transaction_type,$project_id,$description,0,$DrAmount,$balance,0,$created_date);
		
		if($vouchar_type!="Payable Vouchar" && $vouchar_type!="Recievable Vouchar"){
			require_once(CLASS_DIR.'/general_vouchar.class.php');	
			$gvApp 		= new GeneralVouchar(); 
			$HeadType 	= getHeadType($dr_account);
			if($HeadType=="Cash" || $HeadType=="Bank"){
				
				$head_type  = getHeadType($cr_account);
				if($head_type=="Customer"){
				$gvApp->adjustCustomerReceibavle($cr_account,$voucher_no,$DrAmount,$created_date);
				}elseif($head_type=="Supplier"){
				$gvApp->adjustSupplierReceibavle($cr_account,$voucher_no,$DrAmount,$created_date);
				}	
			}else{
				require_once(CLASS_DIR.'/advanced_payment.class.php');	
				$advpApp 	= new AdvancedPayment();
				$head_type 	= getHeadType($dr_account);
				$account_head 	= $dr_account;
				if($head_type=="Supplier"){
				$advpApp->adjustSupplierPayble($voucher_no,$dr_account,$CrAmount,$created_date);
				}elseif($head_type=="Customer"){		
				$advpApp->adjustCustomerPayble($voucher_no,$dr_account,$CrAmount,$created_date);
				}	
			}
		}//end vouchar_type
		//==== Start Adjust Sales/Purchase =====
		if($HeadType=="Cash" || $HeadType=="Bank"){
			//==== Sales Collection ======				
			$head_type = getHeadType($cr_account);
			if($head_type=="Customer" || $head_type=="Supplier"){
			$gvApp->adjustACReceibavle($voucher_no,$CrAmount,$created_date);
			}	
		}elseif($HeadType !="Cash" || $HeadType !="Bank"){
			//==== Purchase Payment ======	
			$head_type = getHeadType($dr_account);
			if($head_type=="Customer" || $head_type=="Supplier"){
			$gvApp->adjustACPayable($voucher_no,$DrAmount,$created_date);
			}
		}
		
		$transactionType  = getRequest('transaction_type');
		$HeadType 	  = getHeadType($dr_account); 
				
		$collection_source = getRequest('collection_source');
		if($collection_source!="Others"){	
		 
		 if($collection_source=="Servicing"){
			$rsql= "SELECT warranty_id,service_bill,paid_amount,due FROM ".WARRANTY_TBL." WHERE customer_id='".$cr_account."' AND due >0";  				
			$rres = mysql_query($rsql);
			while($srow = mysql_fetch_object($rres)){
			 $warranty_id = $srow->warranty_id;
			 if($DrAmount>=$srow->due){
				$DrAmount = $DrAmount - $srow->due;
				$totalPaidAmount = $srow->paid_amount+$srow->due;
				if($totalPaidAmount==$srow->service_bill){
				 $pusql="UPDATE ".WARRANTY_TBL." SET paid_amount='".$totalPaidAmount."',due='0' WHERE warranty_id='$warranty_id'";
				 mysql_query($pusql);
				}
			}elseif($DrAmount<$srow->due){
				$presentDue = $srow->due-$DrAmount;
				$PaidAmount = $srow->paid_amount+$DrAmount;
				if($PaidAmount<$srow->service_bill){
				 $pusql2="UPDATE ".WARRANTY_TBL." SET paid_amount='".$PaidAmount."',due='$presentDue' WHERE warranty_id='$warranty_id'";
				 mysql_query($pusql2);
				}
			 }
			}// end while
		 }
		} 			
		return true;
	  }else {	
		return false;
	  }  

    }//EOFn
	     
   function getVoucherInfo($id)
   {
   	   $data           =  array();                  
       $info           =  array();     
       $info['table']  =  DEVIT_VOUCHAR_TBL." d,".CREDIT_VOUCHAR_TBL." c";
	   $info['fields'] = array('d.voucher_no','d.custom_voucher_no','d.account_head as dr_account','c.account_head as cr_account','d.project_id','d.bank_name','d.acc_no','d.check_no','d.check_issue_date','d.ref_no','d.vouchar_type','d.mode_of_payment','d.transaction_type','d.collection_source','d.debit as amount','d.description','c.currency',"d.created_date"); 
       $info['where']  =  "d.voucher_no=c.voucher_no AND d.voucher_no='".$id."' ";
       //$info['debug']  =  true;                     
       $res            =	select($info);
       if(count($res))
       {
          foreach($res as $i=>$v)
          {
             $data[$i] = $v;             
          }
       }
       return $data[0];
   }
	
  //======== Voucher List for edit =====
  function showVoucherList($msg = null) { 
	  $data                				= array();
	  $data['cmd']         				= getRequest('cmd');
	  $data['record_list'] 				= $this->getVoucherList(getRequest('from'),getRequest('to'));
	  $data['totalrecord']				= $this->getTotalVoucherList();	
	   require_once(SHOW_VOUCHER_LIST_SKIN); 
	   return $data[0];
   } 
   function getVoucherList($from,$to) { 
		if($from == "" && $to == ""){$from=0; $to=500;}
		$date_from 		= formatDate(getRequest('date_from'));
		$date_to 		= formatDate(getRequest('date_to'));
		$voucher_no 	= getRequest('voucher_no');				
		$project_id 	= getFromSession('project_id');
		$info           = array(); 
		$info['table']  =  DEVIT_VOUCHAR_TBL.' t,'.CURRENCY_TBL.' c';
	    $info['fields'] = array('t.voucher_no','t.custom_voucher_no','t.account_head','t.project_id','t.head_type','t.received_id', 'c.curr_symble','t.mode_of_payment','t.bank_name','t.acc_no','t.check_no',"DATE_FORMAT(t.check_issue_date,'%d %b %y' ) as check_issue_date",'t.ref_no','t.vouchar_type','t.transaction_type','t.transaction_name','t.delivery_bag_qty','t.credit','t.debit','t.service_charge','t.description',"DATE_FORMAT(t.created_date,'%d %b %y' ) as created_date"); 
		$sql="t.project_id = '$project_id' AND t.currency = c.currency_id AND DATE(transaction_date)>= '2014-05-25' 
		AND t.vouchar_type != 'Sales Order' AND t.transaction_name != 'Purchase'";
		if($date_from!="" && $date_to ==""){
			$sql.=" AND t.created_date >= '$date_from'";
		}elseif($date_from=="" && $date_to !=""){
			$sql.=" AND t.created_date <= '$date_to'";
		}elseif($date_from!="" && $date_to !=""){
			$sql.=" AND t.created_date BETWEEN '$date_from' AND '$date_to'";
		}
		if($voucher_no!=""){ $sql.=" AND t.voucher_no = '$voucher_no' "; }
		//$info['debug']  =  true; 
		$info['where']  =$sql;	
		$info['orderby'] = array("t.voucher_no DESC LIMIT $from,$to");
		$result         = select($info);
		$data           = array();
		$cnt = count($result);  	     
		if($cnt) {
			foreach($result as $value)  {				
			$data[]	= $value;	
			}
		} 
		return $data; 
   } 

   function getTotalVoucherList() {  
   		$date_from 		= formatDate(getRequest('date_from'));
		$date_to 		= formatDate(getRequest('date_to'));
		$voucher_no 	= getRequest('voucher_no');						
		$project_id 	= getFromSession('project_id');
		$info           = array(); 
		$info['table']  =  DEVIT_VOUCHAR_TBL.' t,'.CURRENCY_TBL.' c';
	    $info['fields'] = array('t.voucher_no'); 
		$sql="t.project_id = '$project_id' AND t.currency = c.currency_id AND DATE(transaction_date)>= '2014-05-25' 
		AND t.vouchar_type != 'Sales Order' AND t.transaction_name != 'Purchase'";
		if($date_from!="" && $date_to ==""){
			$sql.=" AND t.created_date >= '$date_from'";
		}elseif($date_from=="" && $date_to !=""){
			$sql.=" AND t.created_date <= '$date_to'";
		}elseif($date_from!="" && $date_to !=""){
			$sql.=" AND t.created_date BETWEEN '$date_from' AND '$date_to'";
		}
		if($voucher_no!=""){ $sql.=" AND t.voucher_no = '$voucher_no' "; }
		$info['where']  =$sql;	
		$info['orderby'] = array("t.voucher_no asc");
		$result         	= select($info);
		$data           	= array();     
	    $cnt = count($result);  	
      if($cnt) {
        return $cnt;
      } 
	  else {
	  return 0;
	 }        
   } 	
  function saveAccountJournal($voucher_no,$sub_id,$transaction_type,$project_id,$description,$DR=NULL,$CR=NULL,$balance,$status,$created_date){
		$head_type			= getHeadType($sub_id);   $created_by = getFromSession('userid'); 
		$sql = "INSERT INTO ".ACCOUNT_JOURNAL_TBL." (voucher_no,created_date,sub_id,head_type,transaction_type,project_id,description,dr,cr,balance,status,created_by) 
		 VALUES('".$voucher_no."','".$created_date."','".$sub_id."','".$head_type."','".$transaction_type."','".$project_id."','".$description."','".$DR."','".$CR."','".$balance."','".$status."','".$created_by."')";
		mysql_query($sql);
	}
	// ======== Create Voucher ID =======
   function createVoucharID()
   {
      $info = array();
      $info['table'] = DEVIT_VOUCHAR_TBL;
      $info['fields'] = array('max(voucher_no) as maxvoucher');
      $res = select($info);
      $maxvoucherId = 'D0000000';
      if(count($res))
      {
         foreach($res as $v)
         {
         	 if($v->maxvoucher)
         	 {
             $maxvoucherId = $v->maxvoucher;
             }
             break;   	
         }
      }
      $maxvoucherId = generateID("D",$maxvoucherId,8);
      return $maxvoucherId;
   }  
   
   function getSubAccHeadList(){
      $info            = array();
 	  $project_id 	   = getFromSession('project_id');
      $info['table']   = SUB_ACC_HEAD_TBL;

      $info['fields']  = array('sub_id', 'sub_head_name','head_details','head_type'); 	
      $info['where']   =  "project_id = '$project_id'"; 
	  $info['orderby'] = array("sub_head_name ASC");
      $info['debug']   = false;
      $result          = select($info);
      //dBug($result);
      $data            = array();
      if(count($result))
      {
         foreach($result as $i=>$v)
         {
            $data[$i] = $v;             
         }
      }
      return $data;
   }

   function getCurrencyList()
   {
      $info            = array();
      $info['table']   = CURRENCY_TBL;
      //$info['fields'] = array('currency_id', 'name'); 
	  $info['orderby'] = array("currency_name ASC");
      $info['debug']   = false;
      $result          = select($info);
      //dBug($result);
      $data            = array();
      if(count($result))
      {
         foreach($result as $i=>$v)
         {
            $data[$i] = $v;             
         }
      }
      return $data;
   }
   function getHeadName($head_id){
		$project_id = getFromSession('project_id');
		$headName   = "";
		$acsql= "SELECT sub_head_name ,head_details FROM ".SUB_ACC_HEAD_TBL." WHERE BINARY sub_id  = '".$head_id."' AND project_id = '$project_id'";
		$acres = mysql_query($acsql);
		$acnum = mysql_num_rows($acres);
		if($acnum>0){
			$row = mysql_fetch_object($acres);
			 $headName= $row->sub_head_name;
			 if($row->head_details!=""){
				 $headName.="<br>".$row->head_details;
			}
		}else{
			$sql= "SELECT name ,address FROM ".SUPPLIER_TBL." WHERE BINARY supplier_code = '".$head_id."' AND project_id = '$project_id'";
			$res = mysql_query($sql);
			$num = mysql_num_rows($res);
			if($num>0){
				$row = mysql_fetch_object($res);
				$headName= $row->name;
				 if($row->address!=""){
					$headName.="<br>".$row->address;
				}
			}else{
				$sql= "SELECT product_name,product_desc FROM ".PRODUCT_TBL." WHERE BINARY product_id = '".$head_id."' AND project_id = '$project_id'";
				$res = mysql_query($sql);
				$num = mysql_num_rows($res);
				if($num>0){
					$row = mysql_fetch_object($res);
					$headName= $row->product_name;
					 if($row->product_desc!=""){
						 $headName.="<br>".$row->product_desc;
					}
				}else{
					$asql= "SELECT b.bank_name, c.bank_account_no FROM ".BANK_TBL." b, ".BANK_ACCOUNT_TBL." c WHERE b.bank_id=c.bank_code AND BINARY c.bank_account_no ='".$head_id."' 
					AND b.project_id = '$project_id'";
					$ares = mysql_query($asql);
					$anum = mysql_num_rows($ares);
					if($anum>0){
					$arow=mysql_fetch_object($ares);
					$headName=$arow->bank_name.", Acc No. ".$arow->bank_account_no;
					}
				}
			}
		}
		return $headName;
   }
   function getCashId($project_id){
		$sql = "SELECT sub_id FROM ".SUB_ACC_HEAD_TBL." WHERE head_type ='Cash' AND project_id = '$project_id'";
		$row = mysql_fetch_object(mysql_query($sql));
		return $sub_id = $row->sub_id;
	}
   function getCapitalId($project_id){
		$sql = "SELECT sub_id FROM ".SUB_ACC_HEAD_TBL." WHERE head_type ='Capital' AND project_id = '$project_id'";
		$row = mysql_fetch_object(mysql_query($sql));
		return $sub_id = $row->sub_id;
	}
	function getRecievableId($project_id){
		$sql = "SELECT sub_id FROM ".SUB_ACC_HEAD_TBL." WHERE sub_head_name = 'Recievable' AND project_id = '$project_id'";
		$row = mysql_fetch_object(mysql_query($sql));
		return $sub_id = $row->sub_id;
	}
	function getPayableId($project_id){
		$sql = "SELECT sub_id FROM ".SUB_ACC_HEAD_TBL." WHERE sub_head_name = 'Payable' AND project_id = '$project_id'";
		$row = mysql_fetch_object(mysql_query($sql));
		return $sub_id = $row->sub_id;
	}
    function getTotalCreditAmount($acc_head,$project_id){
   		$sql = "SELECT sum(`cr`) as credit_amount FROM ".ACCOUNT_JOURNAL_TBL." WHERE sub_id = '$acc_head' AND project_id = '$project_id'";
		$row = mysql_fetch_object(mysql_query($sql));
		$credit_amount = $row->credit_amount;
		if(empty($credit_amount)){
			$credit_amount = 0;
		}
		return $credit_amount;
   }
  
   function getTotalDebitAmount($acc_head,$project_id){
   		$sql = "SELECT sum(`dr`) as debit_amount FROM ".ACCOUNT_JOURNAL_TBL." WHERE sub_id = '$acc_head' AND project_id = '$project_id'";
		$row = mysql_fetch_object(mysql_query($sql));
		$debit_amount = $row->debit_amount;
		if(empty($debit_amount)){
			$debit_amount = 0;
		}
		return $debit_amount;
   }
  //=============End =============
} // End class
?>
