<?php
class ContraVoucher
{
   
   function run()
   {         
      $cmd = getRequest('cmd');
	  $u_t_id = getFromSession('u_type_id');

      if( ($u_t_id == 101) || ($u_t_id == 102) || ($u_t_id == 104)) //1 = admin 2 = Sales man
      {

      	switch ($cmd)
      	{
      	   case 'add'                	: $screen = $this->showEditor($msg); break;
      	   case 'edit'               	: $screen = $this->showEditor("Edit Page");    break;	
      	   case 'print_vouchar'         : $screen = $this->showPrintEditor($msg); break;
	   case 'save_tmp'  		: $this->saveTempVoucher(); break;   
	   case 'deltemp'		: $this->delTempVoucher(); break;     
	   case 'save_vouchar'		: $this->saveVoucher(); break;
	   case 'list'               	: $screen = $this->showList($msg);   break;
      	   default                   	: $screen = $this->showEditor($msg); break;

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
         $advArr 			= $this->getContraMasterInfo($contra_id);
         $advArr 			= parseThisValue($advArr); 
		 $data   			= array_merge(array(), $advArr); 
		 $data['item_list']	= $this->getContraDetails($contra_id);
		 $data['message'] 	= $msg;
		 $data['cmd']     	= getRequest('cmd');
		 require_once(PRNIT_CONTRA_VOUCHER_SKIN);      
		 return true;
	 }
   }
   function saveVoucher(){
		mysql_query("START TRANSACTION;");
		$contra_id 	= $this->saveContraMasterVouchar();	
		$this->saveContraDetailsVouchar($contra_id); 
		mysql_query("COMMIT;");
		if($contra_id!=""){
		header("location:index.php?app=contra.voucher&cmd=print_vouchar&contra_id=".$contra_id);	
		}else{
		header("location:index.php?app=contra.voucher&cmd=add");
		}
   }
   function saveContraMasterVouchar(){
    	$requestdata = array();
		$requestdata = getUserDataSet(CONTRA_MASTER_TBL);
		$requestdata['headtypes'] 		= $_SESSION['headtypes'];
		$requestdata['dr_account'] 		= getRequest('dr_account');
		$requestdata['dr_amount'] 		= getRequest('dr_amount');
		$requestdata['mode_of_payment'] = getRequest('mode_of_payment');
		$requestdata['transaction_type']= getRequest('transaction_type');		
		$currency = getRequest('currency');
		$currencyArr = explode("###",$currency);
		$requestdata['currency'] = $currencyArr[0];
		
		$requestdata['created_date']	= formatDate(getRequest('created_date'));		
		$requestdata['project_id'] 		= getFromSession('project_id');		
		$requestdata['created_by'] 		= getFromSession('userid');
		
		if(getHeadType(getRequest('dr_account'))=="Cash" || getHeadType(getRequest('dr_account'))=="Bank"){
		$requestdata['vouchar_type'] = "Received Vouchar"; $requestdata['transaction_name'] = "Received";
		}elseif((getHeadType(getRequest('dr_account'))!="Cash" && getHeadType(getRequest('dr_account'))!="Bank") && (getRequest('vouchar_type')=="Others Vouchar")){
		$requestdata['vouchar_type'] = "Payment Vouchar"; $requestdata['transaction_name'] = "Payment";
		}else{
		$requestdata['vouchar_type'] = getRequest('vouchar_type');
		}
		$info        		=  array();
		$info['table']	= CONTRA_MASTER_TBL;
		$info['data'] 	= $requestdata;     
		//$info['debug']  	=  true;
		$res = insert($info);
		if($res){
		return mysql_insert_id();
		}else{ return false;}
			
	}
	function saveContraDetailsVouchar($contra_id){
	$requestdata 				= array();			
	$getSql	= "SELECT * FROM ".TMP_GRVDETAILS_TBL." WHERE created_by = '".getFromSession('userid')."' 
	AND project_id='".getFromSession('project_id')."' AND tmp_grvid='".$_SESSION['tmp_grvid']."'";
	$gres 	= mysql_query($getSql);
	if(mysql_num_rows($gres)>0){
	  while($row = mysql_fetch_object($gres)){
		$requestdata = getUserDataSet(CONTRA_DETAILS_TBL);		
		$requestdata['contra_id'] 		= $contra_id;		
		$requestdata['project_id'] 		= getFromSession('project_id');		
		$requestdata['headtypes'] 		= $_SESSION['headtypes'];
		$requestdata['dr_account'] 		= getRequest('dr_account');
		$requestdata['currency'] 		= $row->currency;		
		$requestdata['cr_account']		= $row->cr_account;
		$requestdata['cr_amount'] 		= $row->cr_amount;
		$requestdata['bank_name'] 		= $row->bank_name;
		$requestdata['acc_no'] 			= $row->acc_no;
		$requestdata['check_no'] 		= $row->check_no;
		$requestdata['check_issue_date']= $row->check_issue_date;
		$requestdata['cheque_type'] 	= $row->cheque_type;
		
		if(getHeadType($row->dr_account)=="Cash" || getHeadType($row->dr_account)=="Bank"){
		$requestdata['vouchar_type'] = "Received Vouchar"; $requestdata['transaction_name'] = "Received";
		}elseif(getHeadType($row->cr_account)=="Cash" || getHeadType($row->cr_account)=="Bank"){
		$requestdata['vouchar_type'] = "Payment Vouchar"; $requestdata['transaction_name'] = "Payment";
		}else{
		$requestdata['vouchar_type'] 	= $row->vouchar_type;
		}	
		$requestdata['description'] 	= $row->description;
		$requestdata['created_by'] 		= getFromSession('userid');
		
		$info        		=  array();
		$info['table']		= CONTRA_DETAILS_TBL;
		$info['data'] 		= $requestdata;
		//$info['debug']  	=  true;   
		$res = insert($info);		
		if($res){
		// ===Start Create Voucher===
		$voucher_no 	= $this->createVoucharID();
		$payment_mode 	= getRequest('mode_of_payment');
		$vouchar_type 	= $row->vouchar_type;
		if($_SESSION['headtypes']=="Dr"){
		$dr_account 	= getRequest('dr_account');
		$dr_amount 		= $row->cr_amount;
		$cr_account		= $row->cr_account;
		$cr_amount 		= $row->cr_amount;
		}else{
		$dr_account 	= $row->cr_account;
		$dr_amount 		= $row->cr_amount;
		$cr_account		= getRequest('dr_account');
		$cr_amount 		= $row->cr_amount;
		}
		$created_date	= getRequest('created_date');
		$bank_name		= $row->bank_name;
		$acc_no			= $row->acc_no;
		$check_no		= $row->check_no;
		$check_date		= $row->check_issue_date;
		$description	= $row->description;
		
		$dvres = $this->saveDebitVouchar($voucher_no,$payment_mode,$vouchar_type,$dr_account,$dr_amount,$cr_account,$created_date,$bank_name,$acc_no,$check_no,$check_date);
		if($dvres){
		$this->saveCreditVouchar($voucher_no,$payment_mode,$vouchar_type,$dr_account,$cr_account,$cr_amount,$created_date,$bank_name,$acc_no,$check_no,$check_date,$description);	
		}
		// ===End Create Voucher===	
		}else{
		return false;
		}
	  }// end while 
		
    }// end if
	
	if($dvres){ 
	 $dsql = "DELETE FROM ".TMP_GRVDETAILS_TBL." WHERE created_by = '".getFromSession('userid')."' 
	 AND project_id='".getFromSession('project_id')."' AND tmp_grvid='".$_SESSION['tmp_grvid']."'";
	 mysql_query($dsql);
	 $dmsql="DELETE FROM ".TMP_GRVMASTER_TBL." WHERE created_by='".getFromSession('userid')."' AND project_id='".getFromSession('project_id')."' AND tmp_grvid='".$_SESSION['tmp_grvid']."'";
	 mysql_query($dmsql);
	 $_SESSION['tmp_grvid']=""; $_SESSION['headtypes']="";
	}
	
  } //End of the function insertSalesDetails()
//================ End Due Received List ===============
   function showEditor($msg = null) { 
    require_once(CLASS_DIR.'/common.list.class.php');	
	$clistApp = new CommonList(); 
	
	$getGM="SELECT * FROM ".TMP_GRVMASTER_TBL." WHERE created_by = '".getFromSession('userid')."'";
	$gres = mysql_query($getGM);
	if(mysql_num_rows($gres)>0){
	$grow = mysql_fetch_object($gres);
	$_SESSION['tmp_grvid']=$grow->tmp_grvid; $_SESSION['headtypes']=$grow->headtypes;
	}else{$_SESSION['tmp_grvid']=""; $_SESSION['headtypes']="";}
		 
	$data['headlist']   		= $clistApp->getAccountHeadList();
	$data['headlist1']   		= $clistApp->getAccountHeadList("Cash");
	$data['headlist2']   		= $clistApp->getAccountHeadList("Overhead Cost");	
	$data['headlist3']   		= $clistApp->getAccountHeadList("Administrative Cost");	
	$data['headlist4']   		= $clistApp->getAccountHeadList("Current Assets");	
	$data['headlist5']   		= $clistApp->getAccountHeadList("Fixed Assets");	
	$data['headlist6']   		= $clistApp->getAccountHeadList("Equipments");	
	$data['headlist7']   		= $clistApp->getAccountHeadList("Incomes");	
	$data['headlist8']   		= $clistApp->getAccountHeadList("LC");	
	$data['headlist9']   		= $clistApp->getAccountHeadList("Capital");		
	$data['headlist10']   		= $clistApp->getAccountHeadList("Loan");	
	$data['headlist11']   		= $clistApp->getAccountHeadList("Customer");	
	$data['headlist12']   		= $clistApp->getAccountHeadList("Staff");			
	$data['currency_list']   	= $this->getCurrencyList();	  
	$data['tmp_voucher']		= $this->getTempVoucher();	   
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
		$this->saveAccountJournal($voucher_no,$PartyAcc_head,$transaction_type,$project_id,$description,$DrAmount,0,$PartyBalance,1,$created_date);	
		//============== Cr Account ===============
		$acc_head = $cr_account; 
		$totalCR  = $this->getTotalCreditAmount($cr_account,getFromSession('project_id'));
		$totalDR  = $this->getTotalDebitAmount($cr_account,getFromSession('project_id'));					 
		$balance  = ($totalDR-($totalCR+$DrAmount));					 
		$this->saveAccountJournal($voucher_no,$cr_account,$transaction_type,$project_id,$description,0,$DrAmount,$balance,1,$created_date);
		
		if($vouchar_type!="Payable Vouchar" && $vouchar_type!="Recievable Vouchar"){ 
			$HeadType 		  = getHeadType($dr_account);
			if($HeadType=="Cash" || $HeadType=="Bank"){
				require_once(CLASS_DIR.'/general_vouchar.class.php');	
				$gvApp 		= new GeneralVouchar();
				$head_type  = getHeadType($cr_account);
				if($head_type=="Customer"){
				$gvApp->adjustCustomerReceibavle($cr_account,$voucher_no,$DrAmount,$created_date);
				}elseif($head_type=="Supplier"){
				$gvApp->adjustSupplierReceibavle($cr_account,$voucher_no,$DrAmount,$created_date);
				}	
			}else{
				require_once(CLASS_DIR.'/advanced_payment.class.php');	
				$advpApp 		= new AdvancedPayment();
				$head_type 		= getHeadType($dr_account);
				$account_head 	= $dr_account;
				if($head_type=="Supplier"){
				$advpApp->adjustSupplierPayble($voucher_no,$dr_account,$CrAmount,$created_date);
				}elseif($head_type=="Customer"){		
				$advpApp->adjustCustomerPayble($voucher_no,$dr_account,$CrAmount,$created_date);
				}	
			}
		}//end vouchar_type
		//=========== Cr Capital =======
		$transactionType  = getRequest('transaction_type');
		$HeadType 		  = getHeadType($dr_account);  
		if($transactionType=="Administrative Cost" || $HeadType=="Administrative Cost"){	
		 $capital_head = $this->getCapitalId(getFromSession('project_id'));
		 $totalCapitalCR  = $this->getTotalCreditAmount($capital_head,getFromSession('project_id'));
		 $totalCapitalDR  = $this->getTotalDebitAmount($capital_head,getFromSession('project_id'));					 
		 $Capitalbalance  = ($totalCapitalDR-($totalCapitalCR+$DrAmount));					 
		 $this->saveAccountJournal($voucher_no,$capital_head,$transaction_type,$project_id,$description,0,$DrAmount,$Capitalbalance,0,$created_date);	
		}
		//=========== Dr Capital =======
		$collection_source = getRequest('collection_source');
		if($collection_source!="Others"){	
		 $capital_head 	  = $this->getCapitalId(getFromSession('project_id'));
		 $totalCapitalCR  = $this->getTotalCreditAmount($capital_head,getFromSession('project_id'));
		 $totalCapitalDR  = $this->getTotalDebitAmount($capital_head,getFromSession('project_id'));					 
		 $Capitalbalance  = (($totalCapitalDR+$DrAmount)-$totalCapitalCR);					 
		 $this->saveAccountJournal($voucher_no,$capital_head,$transaction_type,$project_id,$description,$DrAmount,0,$Capitalbalance,0,$created_date);
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
	//========== SaveIn Temp Tbl ======
	
	function saveTempGRVMaster($strArr){
		$requestdata = array();
		$requestdata = getUserDataSet(TMP_GRVMASTER_TBL);		
		$requestdata['headtypes'] 		= getRequest('headtypes');
		$requestdata['dr_account'] 		= getRequest('dr_account');
		$requestdata['dr_amount'] 		= getRequest('dr_amount');
		$requestdata['mode_of_payment'] = getRequest('mode_of_payment');
		$requestdata['vouchar_type'] 	= getRequest('vouchar_type');
		$requestdata['transaction_type']= getRequest('transaction_type');
		$requestdata['currency'] 		= getRequest('currency');
		$requestdata['currencyName']	= getRequest('currencyName');
		$requestdata['created_date']	= formatDate(getRequest('created_date'));		
		$requestdata['project_id'] 		= getFromSession('project_id');		
		$requestdata['created_by'] 		= getFromSession('userid');
		$info        		=  array();
		$info['table']	= TMP_GRVMASTER_TBL;
		$info['data'] 	= $requestdata;     
		//$info['debug']  	=  true;
		$res = insert($info);
		if($res){
		$_SESSION['tmp_grvid']=mysql_insert_id();
		$_SESSION['headtypes']=getRequest('headtypes');
		}
		
	}
	function saveTempVoucher(){
		$str 			= getRequest('str');
		$strArr 		= explode("####",$str);
		
		if($_SESSION['tmp_grvid']==""){
		 $getGM="SELECT * FROM ".TMP_GRVMASTER_TBL." WHERE created_by = '".getFromSession('userid')."' AND project_id='".getFromSession('project_id')."'";
		 $gres = mysql_query($getGM);
		 if(mysql_num_rows($gres)==0){
		 $this->saveTempGRVMaster($strArr);
		 }else{ 
		 $grow = mysql_fetch_object($gres);
		 $_SESSION['tmp_grvid']=$grow->tmp_grvid;		 
		 $_SESSION['headtypes']=$grow->headtypes;
		 }
		} 
		//======= Insert into tamp ========	
		$requestdata = array();
		$requestdata = getUserDataSet(TMP_GRVDETAILS_TBL);
		$requestdata['tmp_grvid'] 		= $_SESSION['tmp_grvid'];	
		$requestdata['headtypes'] 		= $_SESSION['headtypes'];	
		$requestdata['project_id'] 		= getFromSession('project_id');
		$requestdata['dr_account'] 		= getRequest('dr_account');
		$requestdata['currency'] 		= getRequest('currency');
		$requestdata['currencyName'] 	= getRequest('currencyName');
		$requestdata['cr_account']		= getRequest('cr_account');
		$requestdata['cr_amount'] 		= getRequest('cr_amount');
		$requestdata['bank_name'] 		= getRequest('bank_name');
		$requestdata['acc_no'] 			= getRequest('acc_no');
		$requestdata['check_no'] 		= getRequest('check_no');
		$requestdata['check_issue_date']= formatDate(getRequest('check_issue_date'));
		$requestdata['cheque_type'] 	= getRequest('cheque_type');
		$requestdata['vouchar_type'] 	= getRequest('vouchar_type');
		$requestdata['description'] 	= getRequest('description');
		$requestdata['created_by'] 		= getFromSession('userid');		
		$requestdata['cr_acname'] = $this->getHeadName($requestdata['cr_account']);
		
		$info        		=  array();
		$info['table']	= TMP_GRVDETAILS_TBL;
		$info['data'] 	= $requestdata;     
		//$info['debug']  	=  true;
		$res = insert($info);
		  
		$str1="<table width='100%' align='center' bgcolor='#99CC66'>
		<tr style='color:#fff;' bgcolor='#00B000' height=28>
		  <td width='30%' align='left' nowrap>Cr Account </td>
		  <td width='11%' align='right' nowrap>Cr Amount </td>
		  <td width='11%' align='center' nowrap>Bank Name</td>
		  <td width='11%' align='left' nowrap>Branch Name</td>
		  <td width='10%' align='left' nowrap>Cheque No.</td>
		  <td width='10%' align='left' nowrap>Cheque Issue Date</td>
		  <td width='12%' align='left' nowrap>Note</td>					  
		  <td width='5%' align='center' nowrap>Option</td>
		</tr>";
		$totalCr_amount = 0;
		$getSql	= "SELECT * FROM ".TMP_GRVDETAILS_TBL." WHERE created_by='".getFromSession('userid')."' AND project_id='".getFromSession('project_id')."' 
		AND tmp_grvid='".$_SESSION['tmp_grvid']."'";
		$gres 	= mysql_query($getSql);
		while($row = mysql_fetch_array($gres)){
		extract($row);
		$totalCr_amount+=$cr_amount;
		$str2.="
		<tr style='color:#000000' bgcolor='#CCCCCC'>
		  <td width='30%' align='left' nowrap>$cr_acname</td>
		  <td width='11%' align='right' nowrap>$cr_amount $currencyName</td>
		  <td width='11%' align='center' nowrap>$acc_no</td>
		  <td width='11%' align='center' nowrap>$branch_name</td>
		  <td width='10%' align='left' nowrap>$check_no</td>
		  <td width='10%' align='left' nowrap>$check_issue_date</td>
		  <td width='12%' align='left' nowrap>$description</td>				  
		  <td width='5%' align='center' nowrap>
		  <a href=\"?app=contra.voucher&cmd=deltemp&id=$tmp_id\"><img src=\"images/common/icons/delete.gif\"></a>
		  </td>
		</tr>";
		}
		$str3="</table>";
		echo $str1.$str2.$str3."####-@@@@".$totalCr_amount;
	}
	function delTempVoucher(){
		$tmp_id = $_REQUEST['id'];
		if($tmp_id!=""){
		 $dsql = "DELETE FROM ".TMP_GRVDETAILS_TBL." WHERE tmp_id ='".$tmp_id."'";
		 mysql_query($dsql);
		 
		 $gsql = "SELECT * FROM ".TMP_GRVDETAILS_TBL." WHERE tmp_grvid='".$_SESSION['tmp_grvid']."'";
		 $NUM = mysql_num_rows(mysql_query($gsql));
		 if($NUM==0){
			$dsql = "DELETE FROM ".TMP_GRVMASTER_TBL." WHERE tmp_grvid ='".$_SESSION['tmp_grvid']."'";
		 	mysql_query($dsql); 
			$_SESSION['tmp_grvid']="";
			$_SESSION['headtypes']="";
		 }
		 
		}		
		header("location:?app=contra.voucher&cmd=add");
	}
	function getTempVoucher(){
		$str1="<table width='100%' align='center' bgcolor='#99CC66'>
		<tr style='color:#fff;' bgcolor='#00B000' height=28>
		  <td width='30%' align='left' nowrap>Cr Account </td>
		  <td width='11%' align='right' nowrap>Cr Amount </td>
		  <td width='11%' align='center' nowrap>Bank Name</td>
		  <td width='11%' align='left' nowrap>Branch Name</td>
		  <td width='10%' align='left' nowrap>Cheque No.</td>
		  <td width='10%' align='left' nowrap>Cheque Issue Date</td>
		  <td width='12%' align='left' nowrap>Note</td>					  
		  <td width='5%' align='center' nowrap>Option</td>
		</tr>";
		$totalCr_amount = 0;
		$created_by = getFromSession('userid');
		$getSql		= "SELECT * FROM ".TMP_GRVDETAILS_TBL." WHERE created_by='".getFromSession('userid')."' AND project_id='".getFromSession('project_id')."' 
		AND tmp_grvid='".$_SESSION['tmp_grvid']."'";
		$gres 		= mysql_query($getSql);
		while($row = mysql_fetch_array($gres)){
		extract($row);
		$totalCr_amount+=$cr_amount;
		$str2.="
		<tr style='color:#000000' bgcolor='#CCCCCC'>
		  <td width='30%' align='left' nowrap>$cr_acname</td>
		  <td width='11%' align='right' nowrap>$cr_amount $currencyName</td>
		  <td width='11%' align='center' nowrap>$acc_no</td>
		  <td width='11%' align='center' nowrap>$branch_name</td>
		  <td width='10%' align='left' nowrap>$check_no</td>
		  <td width='10%' align='left' nowrap>$check_issue_date</td>
		  <td width='12%' align='left' nowrap>$description</td>				  
		  <td width='5%' align='center' nowrap>
		  <a href=\"?app=contra.voucher&cmd=deltemp&id=$tmp_id\"><img src=\"images/common/icons/delete.gif\"></a>
		  </td>
		</tr>";
		}
		$str3="</table>";
		$total_salesStr = $str1.$str2.$str3."####-@@@@".$totalCr_amount;
		return $total_salesStr;
	}
	//======= End SaveIn Temp Tbl ========     
	
	function getContraMasterInfo($id){		
		$project_id     = getFromSession('project_id');  
		$info           = array();    
		$info['table']  = CONTRA_MASTER_TBL.' cm,'.PROJECT_TBL.' p,'.CURRENCY_TBL.' c';	
		$info['fields'] = array('cm.contra_id','cm.headtypes','cm.dr_account','p.project_name','p.location','p.project_logo','cm.dr_amount','cm.mode_of_payment','cm.vouchar_type','cm.transaction_type','cm.description',"DATE_FORMAT(cm.created_date,'%d %b %y' ) as created_date",'c.curr_symble','cm.created_by','cm.created_time');	
		$sql="cm.project_id = p.project_id AND cm.currency = c.currency_id AND cm.project_id = '".$project_id."' AND cm.contra_id = '$id'";							
		$info['where']   = $sql;	  	
	    $info['groupby'] = array("cm.contra_id");
		//$info['debug']  = true;
		$res            =	select($info);
		if(count($res)){
			foreach($res as $i=>$v){
				$data[$i] = $v;             
			}
		}
		  //dumpVar($data);
		return $data[0];
   }   
        
   function getContraDetails($id) {
		$info           = array();    
		$info['table']  =  CONTRA_DETAILS_TBL.' cd,'.CURRENCY_TBL.' c';	
		$info['fields'] = array('cd.details_id','cd.contra_id','cd.dr_account','cd.cr_account','cd.cr_amount','cd.bank_name','cd.acc_no',
		'cd.check_no',"DATE_FORMAT(cd.check_issue_date,'%d %b %y' ) as check_issue_date",'cd.cheque_type','cd.vouchar_type','c.curr_symble','cd.description','cd.created_by');		
		$sql="cd.currency = c.currency_id AND cd.contra_id = '$id'";		
		$info['where']   = $sql;
	    $info['groupby'] = array("cd.details_id");
		$info['orderby'] = array("cd.details_id asc");
		//$info['debug']  = true;
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
   //========= Contra List View  ========
   function getContraVoucherList($from,$to) { 
       if($from == "" && $to == ""){$from=0; $to=100;} 
   		$date_from 		= formatDate(getRequest('date_from'));
		$date_to 		= formatDate(getRequest('date_to'));				
		$project_id     = getFromSession('project_id');
		$info           = array();    
		$info['table']  =  CONTRA_MASTER_TBL.' cm,'.CURRENCY_TBL.' c';	
		$info['fields'] = array('cm.`contra_id`','cm.headtypes','cm.dr_account','cm.dr_amount','cm.mode_of_payment','cm.vouchar_type','cm.transaction_type','cm.description',"DATE_FORMAT(cm.created_date,'%d %b %y' ) as created_date",'c.curr_symble','cm.created_by','cm.created_time');
		$sql="cm.currency = c.currency_id AND cm.project_id = '".$project_id."'";
		if($date_from!="" && $date_to ==""){
			$sql.=" AND cm.created_date >= '$date_from'";
		}elseif($date_from=="" && $date_to !=""){
			$sql.=" AND cm.created_date <= '$date_to'";
		}elseif($date_from!="" && $date_to !=""){
			$sql.=" AND cm.created_date BETWEEN '$date_from' AND '$date_to'";
		}	
		$info['where']  = $sql;
	    $info['groupby'] = array("cm.`contra_id`");
		$info['orderby'] = array("cm.`contra_id` DESC LIMIT $from,$to");
		//$info['debug']  = true;
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
  function  getTotalContraVoucherList(){
   		$date_from 		= formatDate(getRequest('date_from'));
		$date_to 		= formatDate(getRequest('date_to'));				
		$project_id     = getFromSession('project_id');
		$info           = array();    
		$info['table']  =  CONTRA_MASTER_TBL.' cm,'.CURRENCY_TBL.' c';	
		$info['fields'] = array('cm.contra_id','cm.dr_account','cm.dr_amount','cm.mode_of_payment','cm.vouchar_type','cm.transaction_type','cm.description',"DATE_FORMAT(cm.created_date,'%d %b %y' ) as created_date",'c.curr_symble','cm.created_by','cm.created_time');
		$sql="cm.currency = c.currency_id AND cm.project_id = '".$project_id."'";
		if($date_from!="" && $date_to ==""){
			$sql.=" AND cm.created_date >= '$date_from'";
		}elseif($date_from=="" && $date_to !=""){
			$sql.=" AND cm.created_date <= '$date_to'";
		}elseif($date_from!="" && $date_to !=""){
			$sql.=" AND cm.created_date BETWEEN '$date_from' AND '$date_to'";
		}	
		$info['where']  = $sql;
	    $info['groupby'] = array("cm.contra_id");
		$info['orderby'] = array("cm.created_time,cm.contra_id ASC");
		//$info['debug']  = true;
		$result         = select($info);
		$data           = array();
		$cnt = count($result);  
		if($cnt) {
			return $cnt;
		}else {
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
