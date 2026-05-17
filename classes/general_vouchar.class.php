<?php
class GeneralVouchar
{
   
   function run()
   {         
      $cmd = getRequest('cmd');
	  $u_t_id = getFromSession('u_type_id');

      if( ($u_t_id == 101) || ($u_t_id == 102) || ($u_t_id == 104)) //1 = admin 2 = Sales man
      {

      	switch ($cmd){
      	   case 'add'                	: $screen = $this->showEditor($msg); break;
      	   case 'edit'               	: $screen = $this->showEditor("Edit Page");    break;	
      	   case 'vouchar_print'         : $screen = $this->showPrintEditor(getRequest('voucher_no'));   break;
	   case 'list'               	: $screen = $this->showList($msg);   break;
      	   default                   	: $screen = $this->showEditor($msg);   break;
      	}
      }else {
      	header("location:index.php?app=user_home&msg=You are not authorised !!!");
      }     	          

      if($cmd == 'list') {

         if($deleted = getRequest('deleted')) {
            if($deleted == 'yes') {
               $screen['message'] = "Item Deleted Successfully";
            } else {
            	  $screen['message'] = "Item Deletion Failure";	
            }
        }
       require_once(CURRENT_APP_SKIN_FILE);
      } 

      return true;

   }   



   function showList($msg = null) {        
	  $data                		= array();
	  $data['cmd']         		= getRequest('cmd');
	  $data['TotalDebitAmount']	= $this->getTotalDebitAmount();
	  $data['TotalCreditAmount']	= $this->getTotalCreditAmount(); 
	   if(getRequest('deleted')=='yes') {
		  $data['message'] = "Item Deleted Successfully";
	   }elseif(getRequest('deleted')=='no') {
		  $data['message'] = "Item Not Deleted";
	   }
	   require_once(CURRENT_APP_SKIN_FILE); 
	   return $data[0];
   }
   
   function showPrintEditor($ID) { 	    
	  if ($ID) {
         $advArr = $this->getDebitVoucharDetails($ID);
         $advArr = parseThisValue($advArr);
         $data   = array_merge(array(), $advArr); 
		 $data['message'] = $msg;
      	 $data['cmd']     = getRequest('cmd');
	  	 require_once(VOUCHAR_PRINT_SKIN);
      }else{
		require_once(PRINT_VOUCHAR_SKIN);
	  }
      return true;
   }  

   //================ End Due Received List ===============
   function showEditor($msg = null) {     
	$ID = getRequest('id');
	  if ($ID) {
	 $advArr = $this->getAccJournalInfo($ID);
	 $advArr = parseThisValue($advArr);  
	 $data   = array_merge(array(), $advArr); 
	}else{
	 if(getRequest('submit')){          
	    $this->saveDebitVouchar();           
	 }
	}	
	require_once(CLASS_DIR.'/common.list.class.php');	
	$clistApp = new CommonList(); 
	$data['headlist1']   	= $clistApp->getAccountHeadList("Current Assets","S130"); // Cash & Bank
	$data['headlist2']   	= $clistApp->getAccountHeadList("Current Assets","S128","C000105"); // Party
	$data['headlist3']   	= $clistApp->getAccountHeadList("Non Current Assets","","","S130","C000105"); 
	$data['headlist4']   	= $clistApp->getAccountHeadList("Current Assets","","","S130","C000105");
	$data['NLiabilities']   = $clistApp->getAccountHeadList("Non Current Liabilities"); 
	$data['CLiabilities']   = $clistApp->getAccountHeadList("Current Liabilities");
	$data['headlist6']   	= $clistApp->getAccountHeadList("Capital");	
	$data['headlist7']   	= $clistApp->getAccountHeadList("Retained earnings");	
	$data['headlist8']   	= $clistApp->getAccountHeadList("Sales");	
	$data['headlist9']   	= $clistApp->getAccountHeadList("Operating Revenue");	
	$data['headlist15']   	= $clistApp->getAccountHeadList("Non-Operating Revenue");	
	$data['headlist10']   	= $clistApp->getAccountHeadList("Direct Expenses");		
	$data['headlist11']   	= $clistApp->getAccountHeadList("Indirect Expenses");	
	$data['headlist12']   	= $clistApp->getAccountHeadList("Opening Balance");	
	$data['headlist13']   	= $clistApp->getAccountHeadList("Adjustments Balance");	
	$data['headlist14']   	= $clistApp->getAccountHeadList("Closing Balance");
	$data['cogsheadlist']   = $clistApp->getAccountHeadList("Cost Center");		
	$data['supplier_list']  = $clistApp->getSupplierList();		
	$data['currency_list']  = $this->getCurrencyList();
	$data['message'] 	= $msg;
	$data['cmd']     	= getRequest('cmd');
	require_once(CURRENT_APP_SKIN_FILE);      

	return true;

   }
   //==================== saveDebitVouchar ====================
    function saveDebitVouchar()
    {     
	  $requestdata = array();
	  $mode_of_payment = getRequest('mode_of_payment');
	  $requestdata = getUserDataSet(DEVIT_VOUCHAR_TBL);			  	  		    
	  $requestdata['head_type']         = getHeadType(getRequest('dr_account'));   
	  $requestdata['account_head']      = getRequest('dr_account'); 
	  $requestdata['debit']        	    = getRequest('amount');    
	  $requestdata['credit']            = 0; 
	  if($mode_of_payment =="Check"){
		$requestdata['mode_of_payment'] 	= "Bank";
		$requestdata['bank_name'] 		= getRequest('bank_name');
		$requestdata['acc_no'] 			= getRequest('acc_no');
		$requestdata['check_no'] 		= getRequest('check_no');
		$requestdata['check_issue_date'] 	= formatDate(getRequest('check_issue_date'));	
	  }else{
		$requestdata['bank_name'] = "";
		$requestdata['acc_no'] = "";
		$requestdata['check_no'] = "";
		$requestdata['check_issue_date'] = "";  
	  }
	  $requestdata['project_id']        = getFromSession('project_id');    
	  $requestdata['created_by']        = getFromSession('userid'); 
	  $requestdata['created_date']      = formatDate(getRequest('created_date'));
	  $vouchar_type = getRequest('vouchar_type');
	  if($vouchar_type=="Payable Vouchar"||$vouchar_type=="Recievable Vouchar"){				
	  	$requestdata['due']   = getRequest('amount'); 
	  	$requestdata['status']= 0;   
	  }else{			
	  	$requestdata['paid_amount']   = getRequest('amount');	
		    if ($vouchar_type == "Journal Vouchar") {
		        $requestdata['vouchar_type'] = "Journal Vouchar";
		        $requestdata['transaction_name'] = "Journal";
		    } else {	  	
			if(getHeadType(getRequest('dr_account'))=="Cash" || getHeadType(getRequest('dr_account'))=="Bank"){
			$requestdata['vouchar_type'] = "Received Vouchar"; $requestdata['transaction_name'] = "Received";
			}elseif(getHeadType(getRequest('cr_account'))=="Cash" || getHeadType(getRequest('cr_account'))=="Bank"){
			$requestdata['vouchar_type'] = "Payment Vouchar"; $requestdata['transaction_name'] = "Payment";
			}
		    }
	  }
	 $voucher_no = $this->createVoucharID();
	 if($voucher_no != -1){
		$requestdata['voucher_no']   	= $voucher_no;
	  }else{
		$msg = "ID overflow !!!";
		header("location:index.php?app=user_home&msg=$msg");
		exit;
	  }	 
	  $info        		=  array();
	  $info['table']	= DEVIT_VOUCHAR_TBL;
	  $info['data'] 	= $requestdata;     
	  //$info['debug']  	=  true;
	  $res = insert($info);			

	  if($res['affected_rows']) {
		$this->saveCreditVouchar($voucher_no);
	  }else {	
		header("location:index.php?app=journal&cmd=add");	
	  }  

    }//EOFn  

    function saveCreditVouchar($voucher_no)
    {    
	  require_once(CLASS_DIR.'/common.list.class.php');	
	  $comlistApp 	= new CommonList(); 
	  $mode_of_payment = getRequest('mode_of_payment');
	  $requestdata = array();
	  $requestdata = getUserDataSet(CREDIT_VOUCHAR_TBL);
	  $requestdata['head_type']     = getHeadType(getRequest('cr_account'));   
	  $requestdata['account_head']  = getRequest('cr_account'); 
	  $requestdata['debit']        	= 0; 
	  $requestdata['credit']        = getRequest('amount');
	  if($mode_of_payment =="Check"){
		$requestdata['mode_of_payment'] 	= "Bank";
		$requestdata['bank_name'] 		= getRequest('bank_name');
		$requestdata['acc_no'] 			= getRequest('acc_no');
		$requestdata['check_no'] 		= getRequest('check_no');
		$requestdata['check_issue_date'] 	= formatDate(getRequest('check_issue_date'));	
	  }else{
		$requestdata['bank_name'] = "";
		$requestdata['acc_no'] = "";
		$requestdata['check_no'] = "";
		$requestdata['check_issue_date'] = "";  
	  }
	  $requestdata['project_id']        = getFromSession('project_id');    
	  $requestdata['created_by']        = getFromSession('userid'); 			 
	  $requestdata['created_date']      = formatDate(getRequest('created_date'));
	  //$requestdata['created_date']    = date('Y-m-d h:i:s');	
	  $requestdata['voucher_no']   		= $voucher_no;
	  $vouchar_type = getRequest('vouchar_type');
	  if($vouchar_type!="Payable Vouchar" && $vouchar_type!="Recievable Vouchar"){	
		if ($vouchar_type == "Journal Vouchar") {
		        $requestdata['vouchar_type'] = "Journal Vouchar";
		        $requestdata['transaction_name'] = "Journal";
		 } else {		
			if(getHeadType(getRequest('dr_account'))=="Cash" || getHeadType(getRequest('dr_account'))=="Bank"){
			$requestdata['vouchar_type'] = "Received Vouchar"; $requestdata['transaction_name'] = "Received";
			}elseif(getHeadType(getRequest('cr_account'))=="Cash" || getHeadType(getRequest('cr_account'))=="Bank"){
			$requestdata['vouchar_type'] = "Payment Vouchar"; $requestdata['transaction_name'] = "Payment";
			}
		}
	  }
	  
	  $info        		=  array();
	  $info['table']	= CREDIT_VOUCHAR_TBL;
	  $info['data'] 	= $requestdata;     
	  //$info['debug']  	=  true;
	  $res = insert($info);	  

	  if($res['affected_rows']) {
		$project_id	 = getFromSession('project_id');
		$DrAmount 	 = getRequest('amount');
		$CrAmount 	 = getRequest('amount');
		$description     = getRequest('description');		
		$description     = str_replace("'","&rsquo;",$description);
		$created_date    = formatDate(getRequest('created_date'));
		if(getHeadType(getRequest('dr_account'))=="Cash" || getHeadType(getRequest('dr_account'))=="Bank"){
		 $transaction_type = "Received";
		}else{
		 $transaction_type = "Payment";
		}	
		//======= Dr Account ======	
		$PartyAcc_head = getRequest('dr_account'); 
		$totalPartyCR  = $this->getTotalCreditAmount($PartyAcc_head,getFromSession('project_id'));
		$totalPartyDR  = $this->getTotalDebitAmount($PartyAcc_head,getFromSession('project_id'));					 
		$PartyBalance  = (($totalPartyDR+$DrAmount)-$totalPartyCR);					 
		$this->saveAccountJournal($voucher_no,$PartyAcc_head,$transaction_type,$project_id,$description,$DrAmount,0,$PartyBalance,0,$created_date);	
		//============== Cr Account ===============
		$acc_head = getRequest('cr_account'); 
		$totalCR  = $this->getTotalCreditAmount($acc_head,getFromSession('project_id'));
		$totalDR  = $this->getTotalDebitAmount($acc_head,getFromSession('project_id'));
		$balance  = ($totalDR-($totalCR+$DrAmount));					 
		$this->saveAccountJournal($voucher_no,$acc_head,$transaction_type,$project_id,$description,0,$DrAmount,$balance,0,$created_date);
		$vouchar_type = getRequest('vouchar_type');
		if($vouchar_type!="Payable Vouchar" && $vouchar_type!="Recievable Vouchar"){ 
			$HeadType 		  = getHeadType(getRequest('dr_account'));
			if($HeadType=="Cash" || $HeadType=="Bank"){
				$head_type = getHeadType(getRequest('cr_account'));
				if($head_type=="Customer" || $head_type=="Supplier"){
				$this->adjustCustomerReceibavle($acc_head,$voucher_no,$DrAmount,$created_date);
				}
				/*
				elseif($head_type=="Supplier"){
				$this->adjustSupplierReceibavle($acc_head,$voucher_no,$DrAmount,$created_date);
				}
				*/	
			}else{
				require_once(CLASS_DIR.'/advanced_payment.class.php');	
				$advpApp 	= new AdvancedPayment();
				$head_type 	= getHeadType(getRequest('dr_account'));
				$account_head 	= getRequest('dr_account');
				/*
				if($head_type=="Supplier"){
				$advpApp->adjustSupplierPayble($voucher_no,$account_head,$CrAmount,$created_date);
				}else */
				if($head_type=="Customer" || $head_type=="Supplier"){		
				$advpApp->adjustCustomerPayble($voucher_no,$account_head,$CrAmount,$created_date);
				}	
			}
		}//end vouchar_type
		
		
		if($HeadType=="Cash" || $HeadType=="Bank"){				
			$cr_account = getRequest('cr_account');
			$CrAmount   = getRequest('amount');
			//==== Sales Collection ======				
			$head_type = getHeadType($cr_account);
			if($head_type=="Customer" || $head_type=="Supplier"){
			$this->adjustACReceibavle($voucher_no,$CrAmount,$created_date);
			}	
		}elseif($HeadType !="Cash" || $HeadType !="Bank"){			
			$dr_account = getRequest('dr_account');
			$DrAmount   = getRequest('amount');
			//==== Purchase Payment ======	
			$head_type = getHeadType($dr_account);
			if($head_type=="Customer" || $head_type=="Supplier"){
			$this->adjustACPayable($voucher_no,$DrAmount,$created_date);
			}
		}
		
		
		$collection_source = getRequest('collection_source');
		if($collection_source !="Others"){	
		 
		 if($collection_source=="Servicing"){
			$rsql= "SELECT warranty_id,service_bill,paid_amount,due FROM ".WARRANTY_TBL." WHERE customer_id='".$acc_head."' AND due >0";  				
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
		header("location:index.php?app=journal&cmd=vouchar_print&voucher_no=".$voucher_no);
	  }else {	
		header("location:index.php?app=journal&cmd=add");
	  }  

    }//EOFn      
	function adjustCustomerReceibavle($cr_account,$NewVoucherNo,$DrAmount,$created_date){
		$project_id = getFromSession('project_id');	
		require_once(CLASS_DIR.'/common.list.class.php');	
	        $clistApp = new CommonList();			
		//======= Receibavle for Sales to him ===========
		$PMsql = "SELECT voucher_no,net_payble,paid_amount,due,item_delivery_amount FROM ".SALES_MASTER_TBL." WHERE customer ='".$cr_account."' AND project_id = '$project_id' AND paid_amount < net_payble AND due >0 ORDER BY voucher_no ASC"; // AND fyear='$fyear'
		$PMRes = mysql_query($PMsql);
		$SMnum = mysql_num_rows($PMRes);
		if($SMnum >0 && $DrAmount >0){
			while($PMrow = mysql_fetch_object($PMRes)){
				$voucher_no 	= $PMrow->voucher_no;
				$net_payble 	= $PMrow->net_payble;
				$paid_amount 	= $PMrow->paid_amount;
				$existing_due 	= $PMrow->due;
				$item_delivery_amount = $PMrow->item_delivery_amount;
				$totalPaidAmount 	  = ($DrAmount+$paid_amount);
				if(($DrAmount>=$existing_due) && ($item_delivery_amount>=$net_payble)){
					$DrAmount 		= $DrAmount - $existing_due;
					if($existing_due>0){						
					$total_paid = ($paid_amount + $existing_due); 
					$PMUpdate = "UPDATE ".SALES_MASTER_TBL." SET paid_amount='$total_paid', due=0  WHERE voucher_no='$voucher_no' AND project_id= '$project_id'";
					mysql_query($PMUpdate);
					$clistApp->saveVoucherAdjustHistory($NewVoucherNo,$project_id,SALES_MASTER_TBL,$voucher_no,$existing_due,"+");	
					} 
				}elseif(($DrAmount < $existing_due) && ($item_delivery_amount >0)){					
					if($existing_due >0 && $DrAmount >0){
						$totalpaid 	 = ($paid_amount + $DrAmount); 
						$present_due = ($existing_due - $DrAmount);
						$adjustAmount = $DrAmount; $DrAmount 	 =  0;
						$PMUpdate = "UPDATE ".SALES_MASTER_TBL." SET paid_amount='$totalpaid',due='$present_due' WHERE voucher_no='$voucher_no' AND project_id= '$project_id'";
						mysql_query($PMUpdate);
						$clistApp->saveVoucherAdjustHistory($NewVoucherNo,$project_id,SALES_MASTER_TBL,$voucher_no,$adjustAmount,"+");
					}
					break;
				}
			} // end while
		}// end $SMnum>0 && $DrAmount>0
			
	   //======= Receibavle for Opening Balance ===========		
	   if($DrAmount>0){
		$rsql= "SELECT voucher_no,debit,paid_amount,due FROM ".DEVIT_VOUCHAR_TBL." WHERE account_head='".$cr_account."' AND vouchar_type='Recievable Vouchar' AND due >0 
		AND status=0  ORDER BY voucher_no ASC";  
		$rres = mysql_query($rsql);
		while($srow = mysql_fetch_object($rres)){
		 $voucher_no = $srow->voucher_no;
		 if($DrAmount>=$srow->due && $srow->due>0){
			$DrAmount = $DrAmount - $srow->due; 
			$totalPaidAmount = $srow->paid_amount+$srow->due;
			if($totalPaidAmount==$srow->debit){
			 $adjustAmount=$srow->due;
			 $pusql="UPDATE ".DEVIT_VOUCHAR_TBL." SET paid_amount='".$totalPaidAmount."',due='0',`status`=1 WHERE voucher_no='$voucher_no'";
			 mysql_query($pusql);
			 $clistApp->saveVoucherAdjustHistory($NewVoucherNo,$project_id,DEVIT_VOUCHAR_TBL,$voucher_no,$adjustAmount,"+");
			}
		 }elseif(($DrAmount < $srow->due) && ($srow->due >0 && $DrAmount >0)){
			$presentDue = $srow->due-$DrAmount;
			$PaidAmount = $srow->paid_amount+$DrAmount;
			if($PaidAmount < $srow->debit){
			 $adjustAmount = $DrAmount; $DrAmount=0;
			 $pusql2="UPDATE ".DEVIT_VOUCHAR_TBL." SET paid_amount='".$PaidAmount."',due='$presentDue',`status`=0 WHERE voucher_no='$voucher_no'";
			 mysql_query($pusql2);
			 $clistApp->saveVoucherAdjustHistory($NewVoucherNo,$project_id,DEVIT_VOUCHAR_TBL,$voucher_no,$adjustAmount,"+");
			}
			break;
		 }
		}// end while
		} //============End DrAmount >0 ===========	
		//========Customer can be Receibavle for my Adv Payment ========== 
		if($DrAmount>0){
		$SRPSql="SELECT return_id,supplier,return_amount,paid_amount,due FROM ".PURCHASE_RETURN_RECEIBAVLE_TBL." WHERE supplier ='".$cr_account."' AND project_id = '$project_id' AND paid_amount < return_amount AND due >0  ORDER BY return_id ASC"; // AND fyear='$fyear'
		$SRPRes = mysql_query($SRPSql);
		while($srprow = mysql_fetch_object($SRPRes)){
			$return_id 	= $srprow->return_id;
			$net_payble 	= $srprow->return_amount;
			$paid_amount 	= $srprow->paid_amount;
			$existing_due 	= $srprow->due;
			if(($DrAmount>=$existing_due)){
				$DrAmount = $DrAmount - $existing_due;
				if($existing_due>0){						
				$total_paid = ($paid_amount + $existing_due); 
				$SRUpSql = "UPDATE ".PURCHASE_RETURN_RECEIBAVLE_TBL." SET paid_amount=$total_paid, due=0  WHERE return_id ='$return_id' AND project_id = '$project_id'";
				mysql_query($SRUpSql);
				$clistApp->saveVoucherAdjustHistory($NewVoucherNo,$project_id,PURCHASE_RETURN_RECEIBAVLE_TBL,$return_id,$existing_due,"+");
				} 
			}elseif(($DrAmount<$existing_due)){					
				if($existing_due >0 && $DrAmount >0){
				$totalpaid 	 = ($paid_amount + $DrAmount); 
				$present_due = ($existing_due - $DrAmount);
				$adjustAmount = $DrAmount; $DrAmount =  0;
				$SRPUpdate="UPDATE ".PURCHASE_RETURN_RECEIBAVLE_TBL." SET paid_amount=$totalpaid,due=$present_due WHERE return_id='$return_id' 
				AND project_id= '$project_id'";
				mysql_query($SRPUpdate);
				$clistApp->saveVoucherAdjustHistory($NewVoucherNo,$project_id,PURCHASE_RETURN_RECEIBAVLE_TBL,$return_id,$adjustAmount,"+");
				}
				break;
			}
		} // end while
		}// end $DrAmount>0
		//====== Make Customer Payble if Received Amount greater then his Receibavle ======
		if($DrAmount >0){
		$customer = $cr_account; $return_date = $created_date; $return_by = getFromSession('userid'); 
		$currency = getRequest('currency');	if($currency==""){$currency = 1;} if($voucher_no==""){$voucher_no = $NewVoucherNo;}
		$RMSQL="INSERT INTO ".SALES_RETURN_PAYBLE_TBL."(voucher_no,project_id,customer_id,currency,return_amount,paid_amount,due,payble_source,return_date,created_by)  
		VALUES('$voucher_no','$project_id','$customer','$currency','$DrAmount','0','$DrAmount','Advanced Received','$return_date','$return_by')";
		mysql_query($RMSQL); 
		$clistApp->saveVoucherAdjustHistory($NewVoucherNo,$project_id,SALES_RETURN_PAYBLE_TBL,$voucher_no,$DrAmount,"Payble ROA");
		}
	}
	function adjustSupplierReceibavle($cr_account,$NewVoucherNo,$DrAmount,$created_date){
		$project_id = getFromSession('project_id');	
		require_once(CLASS_DIR.'/common.list.class.php');	
	        $clistApp = new CommonList();
		//===== for Opening Balance ========
		if($DrAmount>0){
		$rsql= "SELECT voucher_no,debit,paid_amount,due FROM ".DEVIT_VOUCHAR_TBL." WHERE account_head='".$cr_account."' 
		AND vouchar_type='Recievable Vouchar' AND due >0 AND status=0 ORDER BY voucher_no ASC";  
		$rres = mysql_query($rsql);
		while($srow = mysql_fetch_object($rres)){
		 $voucher_no = $srow->voucher_no;
		 if($DrAmount>=$srow->due && $srow->due>0){
			$DrAmount = $DrAmount - $srow->due;
			$totalPaidAmount = $srow->paid_amount+$srow->due;
			if($totalPaidAmount==$srow->debit){
			 $adjustAmount = $srow->due;
			 $pusql="UPDATE ".DEVIT_VOUCHAR_TBL." SET paid_amount='".$totalPaidAmount."',due='0',`status`=1 WHERE voucher_no='$voucher_no'";
			 mysql_query($pusql);
			 $clistApp->saveVoucherAdjustHistory($NewVoucherNo,$project_id,DEVIT_VOUCHAR_TBL,$voucher_no,$adjustAmount,"-");
			}
		 }elseif(($DrAmount < $srow->due) && ($srow->due >0 && $DrAmount >0)){
			$presentDue = $srow->due-$DrAmount;
			$PaidAmount = $srow->paid_amount+$DrAmount;
			if($PaidAmount < $srow->debit && $PaidAmount < $srow->due){
			 $adjustAmount = $DrAmount; $DrAmount=0;
			 $pusql2="UPDATE ".DEVIT_VOUCHAR_TBL." SET paid_amount='".$PaidAmount."',due='$presentDue',`status`=0 WHERE voucher_no='$voucher_no'";
			 mysql_query($pusql2);
			 $clistApp->saveVoucherAdjustHistory($NewVoucherNo,$project_id,DEVIT_VOUCHAR_TBL,$voucher_no,$adjustAmount,"-");
			}
			break;
		 }
		}// end while
		} //============End DrAmount >0 ===========
		//========Supplier can be Receibavle for my Purchase Return, Beddebs, Adv Paid ========== 
		if($DrAmount>0){
		$SRPSql="SELECT return_id,supplier,return_amount,paid_amount,due FROM ".PURCHASE_RETURN_RECEIBAVLE_TBL." WHERE supplier ='".$cr_account."' 
		 AND project_id = '$project_id' AND paid_amount < return_amount AND due >0  ORDER BY return_id ASC"; // AND fyear='$fyear'
		$SRPRes = mysql_query($SRPSql);
		while($srprow = mysql_fetch_object($SRPRes)){
			$return_id 	= $srprow->return_id;
			$net_payble 	= $srprow->return_amount;
			$paid_amount 	= $srprow->paid_amount;
			$existing_due 	= $srprow->due;
			if(($DrAmount>=$existing_due)){
				$DrAmount 		= $DrAmount - $existing_due;
				if($existing_due>0){						
				$total_paid = ($paid_amount + $existing_due); 
				$SRUpSql = "UPDATE ".PURCHASE_RETURN_RECEIBAVLE_TBL." SET paid_amount=$total_paid, due=0  WHERE return_id ='$return_id' AND project_id = '$project_id'";
				mysql_query($SRUpSql);
				$clistApp->saveVoucherAdjustHistory($NewVoucherNo,$project_id,PURCHASE_RETURN_RECEIBAVLE_TBL,$return_id,$existing_due,"-");
				} 
			}elseif(($DrAmount<$existing_due)){					
				if($existing_due>0 && $DrAmount >0){
				$totalpaid 	 = ($paid_amount + $DrAmount); 
				$present_due = ($existing_due - $DrAmount);
				$adjustAmount = $DrAmount; $DrAmount 	 =  0;
				$SRPUpdate="UPDATE ".PURCHASE_RETURN_RECEIBAVLE_TBL." SET paid_amount=$totalpaid,due=$present_due WHERE return_id='$return_id' 
				AND project_id= '$project_id'";
				mysql_query($SRPUpdate);
				$clistApp->saveVoucherAdjustHistory($NewVoucherNo,$project_id,PURCHASE_RETURN_RECEIBAVLE_TBL,$return_id,$adjustAmount,"-");
				}
				break;
			}
		} // end while
		}// end $DrAmount>0
		
		//======= Receibavle for Sales to him ===========
		$PMsql = "SELECT voucher_no,net_payble,paid_amount,due,item_delivery_amount FROM ".SALES_MASTER_TBL." WHERE customer ='".$cr_account."' 
		AND project_id = '$project_id' AND paid_amount < net_payble AND due >0 ORDER BY voucher_no ASC"; // AND fyear='$fyear'
		$PMRes = mysql_query($PMsql);
		$SMnum = mysql_num_rows($PMRes);
		if($SMnum >0 && $DrAmount >0){
			while($PMrow = mysql_fetch_object($PMRes)){
				$voucher_no 	= $PMrow->voucher_no;
				$net_payble 	= $PMrow->net_payble;
				$paid_amount 	= $PMrow->paid_amount;
				$existing_due 	= $PMrow->due;
				$item_delivery_amount = $PMrow->item_delivery_amount;
				$totalPaidAmount 	  = ($DrAmount+$paid_amount);
				if(($DrAmount >= $existing_due) && ($item_delivery_amount >= $net_payble)){
					$DrAmount 		= $DrAmount - $existing_due;
					if($existing_due >0){						
					$total_paid = ($paid_amount + $existing_due); 
					$PMUpdate = "UPDATE ".SALES_MASTER_TBL." SET paid_amount='$total_paid', due=0  WHERE voucher_no='$voucher_no' AND project_id= '$project_id'";
					mysql_query($PMUpdate);
					$clistApp->saveVoucherAdjustHistory($NewVoucherNo,$project_id,SALES_MASTER_TBL,$voucher_no,$existing_due,"+");	
					} 
				}elseif(($DrAmount < $existing_due) && ($item_delivery_amount >0)){					
					if($existing_due >0 && $DrAmount >0){
						$totalpaid 	 = ($paid_amount + $DrAmount); 
						$present_due = ($existing_due - $DrAmount);
						$adjustAmount = $DrAmount; $DrAmount 	 =  0;
						$PMUpdate = "UPDATE ".SALES_MASTER_TBL." SET paid_amount='$totalpaid',due='$present_due' WHERE voucher_no='$voucher_no' 
						AND project_id= '$project_id'";
						mysql_query($PMUpdate);
						$clistApp->saveVoucherAdjustHistory($NewVoucherNo,$project_id,SALES_MASTER_TBL,$voucher_no,$adjustAmount,"+");
					}
					break;
				}
			} // end while
		}// end $SMnum>0 && $DrAmount>0
		
		//====== Make Supplier Payble if Received Amount greater then his Receibavle ======
		if($DrAmount >0){
		$supplier = $cr_account; $return_date = $created_date; $return_by = getFromSession('userid'); 
		$currency = getRequest('currency');	if($currency==""){$currency = 1;} if($voucher_no==""){$voucher_no = $NewVoucherNo;}
		$RMSQL="INSERT INTO ".SALES_RETURN_PAYBLE_TBL."(voucher_no,project_id,customer_id,currency,return_amount,paid_amount,due,payble_source,return_date,created_by)  
		VALUES('$voucher_no','$project_id','$supplier','$currency','$DrAmount','0','$DrAmount','Advanced Received','$return_date','$return_by')";
		mysql_query($RMSQL);  
		$clistApp->saveVoucherAdjustHistory($NewVoucherNo,$project_id,SALES_RETURN_PAYBLE_TBL,$voucher_no,$DrAmount,"Payble ROA");
		}
  }

  function adjustACPayable($voucher_no,$DrAmount,$created_date){
	//======= AC Payable Dr ======
	/*
	$project_id 	 = getFromSession('project_id'); 
	$PayableId 	 = $this->getACPayableId(getFromSession('project_id'));
	$description 	 = "Paid payable against cost of goods purchased";
	$ACPayableAmount = $this->getAccounceBalance($PayableId,$project_id);
	$PayableBalance  = ($ACPayableAmount+$DrAmount);					 
	$this->saveAccountJournal($voucher_no,$PayableId,"Accounts Payable",$project_id,$description,$DrAmount,0,$PayableBalance,0,$created_date);
	*/
  }
  function adjustACReceibavle($voucher_no,$CrAmount,$created_date){
	 //======= AC Recievable Cr ======
	 /*
	 $ACRecievableId 	= $this->getACRecievableId(getFromSession('project_id'));
	 $description 	 	= "Collection receivable against cost of goods sold"; 
	 $ACRecievableAmount	= $this->getAccounceBalance($ACRecievableId,$project_id);
	 $RecievableBalance  	= ($ACRecievableAmount-$CrAmount);					 
	 $this->saveAccountJournal($voucher_no,$ACRecievableId,"Account Recievable",$project_id,$description,0,$CrAmount,$RecievableBalance,0,$created_date);
	*/
  }

  function saveAccountJournal($voucher_no,$sub_id,$transaction_type,$project_id,$description,$DR=NULL,$CR=NULL,$balance,$status,$created_date){
		$head_type			= getHeadType($sub_id);   $created_by = getFromSession('userid'); 
		$sql = "INSERT INTO ".ACCOUNT_JOURNAL_TBL." (voucher_no,created_date,sub_id,head_type,transaction_type,project_id,description,dr,cr,balance,status,created_by) 
		 VALUES('".$voucher_no."','".$created_date."','".$sub_id."','".$head_type."','".$transaction_type."','".$project_id."','".$description."','".$DR."','".$CR."','".$balance."','".$status."','".$created_by."')";
		mysql_query($sql);
   }

   function getDebitVoucharDetails($voucher_no)
   {
       $data           =  array();     
       $info           =  array();     
       $info['table']  =  DEVIT_VOUCHAR_TBL;
       $info['where']  =  "voucher_no='".$voucher_no."' ";
       $info['debug']  =  false;                     
       $res            =	select($info);
       if(count($res))
       {
          foreach($res as $i=>$v)
          {
             $data[$i] = $v;             
          }
       }
       //dumpVar($data);
       return $data[0];
   }

   // ==== function createVoucharID ===============

   function createVoucharID()
   {
      $user_sl = getFromSession('user_sl');
      if($user_sl !=""){ $prefix = "G".$user_sl;}else{$prefix = "G";}
      $info = array();
      $info['table'] = DEVIT_VOUCHAR_TBL;
      $info['fields'] = array('max(voucher_no) as maxvoucher');
      $info['where']  = "voucher_no LIKE '%".$prefix."%'";      
      $res = select($info);      
      if($user_sl !=""){ 
	$maxvoucherId = $prefix.'0000000';
      }else{
	$maxvoucherId = $prefix.'00000000';
      }
      
      if(count($res)){
         foreach($res as $v){
         	 if($v->maxvoucher){
             $maxvoucherId = $v->maxvoucher;
             }
             break;   	
         }
      
      }      
      $maxvoucherId = generateID("$prefix",$maxvoucherId,9);
      return $maxvoucherId;
   }  
   
   function getSubAccHeadList()
   {

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
   function getACRecievableId($project_id){
	$sql = "SELECT sub_id FROM ".SUB_ACC_HEAD_TBL." WHERE sub_id='A000024' AND head_type = 'Accounts Recievable' AND project_id = '$project_id'";
	$row = mysql_fetch_object(mysql_query($sql));
	return $sub_id = $row->sub_id;
   }
   function getACPayableId($project_id){
	$sql = "SELECT sub_id FROM ".SUB_ACC_HEAD_TBL." WHERE sub_id='A000028' AND head_type = 'Accounts Payable' AND project_id = '$project_id'";
	$row = mysql_fetch_object(mysql_query($sql));
	return $sub_id = $row->sub_id;
   }
   function getAccounceBalance($account_id,$project_id){
	$sql = "SELECT (sum(`dr`) - sum(`cr`)) as balance_amount FROM ".ACCOUNT_JOURNAL_TBL." WHERE sub_id = '$account_id' AND project_id = '$project_id'";
	
	$row = mysql_fetch_object(mysql_query($sql));
	$balance_amount = $row->balance_amount;
	if(empty($balance_amount)){
		$balance_amount = 0;
	}
	return $balance_amount;
   }
//=============End =============

} // End class
?>
