<?php
class BankReceivedVoucher
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
      	   case 'vouchar_print'         : $screen = $this->showPrintEditor(getRequest('voucher_no'));   break;
		   case 'list'               	: $screen = $this->showList($msg);   break;
      	   default                   	: $screen = $this->showEditor($msg); break;
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
	  $data                				= array();
	  $data['cmd']         				= getRequest('cmd');
	  $data['TotalDebitAmount']			= $this->getTotalDebitAmount();
	  $data['TotalCreditAmount']		= $this->getTotalCreditAmount(); 
	   if(getRequest('deleted')=='yes') {
		  $data['message'] = "Item Deleted Successfully";
	   }elseif(getRequest('deleted')=='no') {
		  $data['message'] = "Item Not Deleted";
	   }
	   require_once(RECEIVED_VOUCHER_SKIN); 
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
	require_once(CLASS_DIR.'/common.list.class.php');	
	$clistApp = new CommonList(); 
	$ID = getRequest('id');
	if ($ID) {
	$advArr = $this->getAccJournalInfo($ID);
	$advArr = parseThisValue($advArr);  
	$data   = array_merge(array(), $advArr); 
	}else{
	if(getRequest('submit')){  
	 	mysql_query("START TRANSACTION;");        
	$this->saveDebitVouchar();           
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
 	function saveDebitVouchar()
 	{     
 	   	  $mode_of_payment = getRequest('mode_of_payment');	 
		  $requestdata = array();	
		  $requestdata = getUserDataSet(DEVIT_VOUCHAR_TBL);			  	  		    
		  if($mode_of_payment =="Check"){
		  	$requestdata['mode_of_payment'] 	= "Bank";
			$requestdata['bank_name'] 			= getRequest('bank_name');
			$requestdata['acc_no'] 				= getRequest('acc_no');
			$requestdata['check_no'] 			= getRequest('check_no');
			$requestdata['check_issue_date'] 	= formatDate(getRequest('check_issue_date'));
    
		  	$requestdata['account_head']     	= getRequest('dr_account'); 
		  	$requestdata['debit']        		= getRequest('amount'); 
		  	$requestdata['credit']        		= 0;    
		  	$requestdata['head_type']     		= "Check";   
		  }else{
			$requestdata['bank_name'] 			= "";
			$requestdata['acc_no'] 				= "";
			$requestdata['check_no'] 			= "";
			$requestdata['check_issue_date'] 	= "";
			$requestdata['account_head']     	= getRequest('dr_account'); 
		  	$requestdata['debit']        		= getRequest('amount');
		  	$requestdata['credit']        		= 0;    
		  	$requestdata['head_type']     		= getHeadType(getRequest('dr_account'));  
		  }
     	  $requestdata['transaction_type']  = "Received"; 
		  $requestdata['project_id']        = getFromSession('project_id');    
		  $requestdata['created_by']        = getFromSession('userid'); 
		  $requestdata['created_date']      = formatDate(getRequest('created_date'));
		  //$requestdata['created_date']      = date('Y-m-d h:i:s');
		  $vouchar_type = getRequest('vouchar_type');
		  if($vouchar_type==""){
		  $requestdata['vouchar_type']="Received Vouchar";
		  }			  	
		  $requestdata['paid_amount']   = getRequest('amount');  
		  $requestdata['due']   		= 0; 
		  $requestdata['status']   		= 1; 
		  $voucher_no = $this->createVoucharID();
	
		 if($voucher_no != -1){
			$requestdata['voucher_no']  = $voucher_no;
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
		  	mysql_query("ROLLBACK;");
			header("location:index.php?app=bank.received.voucher&cmd=add");	
		  }	 

    }//EOFn  

    function saveCreditVouchar($voucher_no)
 	{     
		  $requestdata = array();
 	   	  $mode_of_payment = getRequest('mode_of_payment');
		  $deposited	   = getRequest('deposited');
		  $requestdata = getUserDataSet(CREDIT_VOUCHAR_TBL);
		  if($mode_of_payment =="Check"){
		  $requestdata['mode_of_payment'] 	= "Bank";
		  }				  	  		    
		  $requestdata['head_type']     	= getHeadType(getRequest('cr_account'));   
		  $requestdata['account_head']      = getRequest('cr_account'); 
		  $requestdata['debit']        		= 0;     
		  $requestdata['credit']        	= getRequest('amount');
		  $requestdata['bank_name'] 		= "";
		  $requestdata['acc_no'] 			= "";
	      $requestdata['check_no'] 			= "";
		  $requestdata['check_issue_date'] 	= ""; 
		  $requestdata['transaction_type']  = "Received";
		  $requestdata['project_id']        = getFromSession('project_id');    
		  $requestdata['created_by']        = getFromSession('userid'); 			 
		  $requestdata['created_date']      = formatDate(getRequest('created_date'));
		  $created_date = formatDate(getRequest('created_date'));
		  //$requestdata['created_date']      = date('Y-m-d h:i:s');	
		  $requestdata['voucher_no']   		= $voucher_no;
		 
		  $info        		=  array();
		  $info['table']	= CREDIT_VOUCHAR_TBL;
		  $info['data'] 	= $requestdata;     
		  //$info['debug']  	= true;
		  $res = insert($info);			
	
		  if($res['affected_rows']) {
			$CrAmount 	= getRequest('amount');			
			$DrAmount 	= getRequest('amount');
			$head_type 	= getHeadType(getRequest('cr_account')); $description = getRequest('description');
			if($mode_of_payment=="Cash"){ 
			 //============== Cash Dr ===============
			 $acc_head = getRequest('dr_account'); 
			 $totalCR  = $this->getTotalCreditAmount($acc_head,getFromSession('project_id'));
			 $totalDR  = $this->getTotalDebitAmount($acc_head,getFromSession('project_id'));					 
			 $balance  = (($totalDR+$DrAmount)-$totalCR);					 
			 $this->saveAccountJournal($voucher_no,$acc_head,$head_type,getFromSession('project_id'),$description,$DrAmount,0,$balance,1,$created_date);	
			 //======= Party Cr ======	
			 $PartyAcc_head = getRequest('cr_account'); 
			 $totalPartyCR  = $this->getTotalCreditAmount($PartyAcc_head,getFromSession('project_id'));
			 $totalPartyDR  = $this->getTotalDebitAmount($PartyAcc_head,getFromSession('project_id'));					 
			 $PartyBalance  = ($totalPartyDR-($totalPartyCR+$DrAmount));					 
			 $this->saveAccountJournal($voucher_no,$PartyAcc_head,$head_type,getFromSession('project_id'),$description,0,$CrAmount,$PartyBalance,1,$created_date);
			}elseif($mode_of_payment=="Check" && $deposited=="No"){
			//====== save payable_check ======
			$this->saveReceivableCheck($voucher_no,$voucher_no,"Received",getRequest('amount'));				
			}elseif($mode_of_payment=="Check" && $deposited=="Yes"){
			 //========= Bank Account Dr =======
			 $acc_head = getRequest('dr_account'); 
			 $totalCR  = $this->getTotalCreditAmount($acc_head,getFromSession('project_id'));
			 $totalDR  = $this->getTotalDebitAmount($acc_head,getFromSession('project_id'));					 
			 $balance  = (($totalDR+$paidAmount)-$totalCR);	
			 $this->saveAccountJournal($voucher_no,$acc_head,$head_type,getFromSession('project_id'),$description,$DrAmount,0,$balance,1,$created_date);	
			 //========== Party Cr ============	
			 $PartyAcc_head = getRequest('cr_account'); 
			 $totalPartyCR  = $this->getTotalCreditAmount($PartyAcc_head,getFromSession('project_id'));
			 $totalPartyDR  = $this->getTotalDebitAmount($PartyAcc_head,getFromSession('project_id'));					 
			 $PartyBalance  = ($totalPartyDR-($totalPartyCR+$DrAmount));					 
			 $this->saveAccountJournal($voucher_no,$PartyAcc_head,$head_type,getFromSession('project_id'),$description,0,$CrAmount,$PartyBalance,1,$created_date);		 
			}
			//================ Adjust Due ====================
			if($head_type=="Customer"){
			$this->adjustCustomerReceibavle($voucher_no,$DrAmount);
			}elseif($head_type=="Supplier"){
			$this->adjustSupplierReceibavle($voucher_no,$DrAmount);
			}
			//exit;
			mysql_query("COMMIT;");			
			header("location:index.php?app=journal&cmd=vouchar_print&voucher_no=".$requestdata['voucher_no']);
		  }else {	
		  	mysql_query("ROLLBACK;");
			header("location:index.php?app=bank.received.voucher&cmd=add");	
		  }
    }//EOFn 
	function adjustCustomerReceibavle($NewVoucherNo,$DrAmount){
		$project_id = getFromSession('project_id');	
		$created_date = formatDate(getRequest('created_date'));	
		//======= Receibavle for Sales to him ===========
		$PMsql = "SELECT voucher_no,net_payble,paid_amount,due,item_delivery_amount FROM ".SALES_MASTER_TBL." WHERE customer ='".getRequest('cr_account')."' 
		AND project_id = '$project_id' AND paid_amount < net_payble AND due >0 ORDER BY voucher_no ASC"; // AND fyear='$fyear'
		$PMRes = mysql_query($PMsql);
		$SMnum = mysql_num_rows($PMRes);
		if($SMnum>0 && $DrAmount>0){
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
					$PMUpdate = "UPDATE ".SALES_MASTER_TBL." SET paid_amount = '$total_paid', due=0  WHERE voucher_no ='$voucher_no' AND project_id = '$project_id'";
					mysql_query($PMUpdate);
					} 
				}elseif(($DrAmount < $existing_due) && ($item_delivery_amount >0)){					
					if($existing_due >0 && $DrAmount >0){
						$totalpaid 	 = ($paid_amount + $DrAmount); 
						$present_due = ($existing_due - $DrAmount);
						$DrAmount 	 =  0;
						$PMUpdate = "UPDATE ".SALES_MASTER_TBL." SET paid_amount='$totalpaid',due='$present_due' WHERE voucher_no='$voucher_no' 
						AND project_id= '$project_id'";
						mysql_query($PMUpdate);
					}
					break;
				}
			} // end while
		}// end $SMnum>0 && $DrAmount>0
			
	   //======= Receibavle for Opening Balance ===========		
	   if($DrAmount>0){
		$rsql= "SELECT voucher_no,debit,paid_amount,due FROM ".DEVIT_VOUCHAR_TBL." WHERE account_head='".getRequest('cr_account')."' AND vouchar_type='Recievable Vouchar' AND due >0 
		AND status=0  ORDER BY voucher_no ASC";  
		$rres = mysql_query($rsql);
		while($srow = mysql_fetch_object($rres)){
		 $voucher_no = $srow->voucher_no;
		 if($DrAmount>=$srow->due && $srow->due>0){
			$DrAmount = $DrAmount - $srow->due;
			$totalPaidAmount = $srow->paid_amount+$srow->due;
			if($totalPaidAmount==$srow->debit){
			 $pusql="UPDATE ".DEVIT_VOUCHAR_TBL." SET paid_amount='".$totalPaidAmount."',due='0',`status`=1 WHERE voucher_no='$voucher_no'";
			 mysql_query($pusql);
			}
		 }elseif(($DrAmount < $srow->due) && ($srow->due >0 && $DrAmount >0)){
			$presentDue = $srow->due-$DrAmount;
			$PaidAmount = $srow->paid_amount+$DrAmount;
			if($PaidAmount < $srow->debit){
			 $DrAmount=0;
			 $pusql2="UPDATE ".DEVIT_VOUCHAR_TBL." SET paid_amount='".$PaidAmount."',due='$presentDue',`status`=0 WHERE voucher_no='$voucher_no'";
			 mysql_query($pusql2);
			}
			break;
		 }
		}// end while
		} //============End DrAmount >0 ===========	
		//========Customer can be Receibavle for my Adv Payment ========== 
		if($DrAmount>0){
		$SRPSql="SELECT return_id,supplier,return_amount,paid_amount,due FROM ".PURCHASE_RETURN_RECEIBAVLE_TBL." WHERE supplier ='".getRequest('cr_account')."' 
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
				} 
			}elseif(($DrAmount<$existing_due)){					
				if($existing_due >0 && $DrAmount >0){
				$totalpaid 	 = ($paid_amount + $DrAmount); 
				$present_due = ($existing_due - $DrAmount);
				$DrAmount 	 =  0;
				$SRPUpdate="UPDATE ".PURCHASE_RETURN_RECEIBAVLE_TBL." SET paid_amount=$totalpaid,due=$present_due WHERE return_id='$return_id' 
				AND project_id= '$project_id'";
				mysql_query($SRPUpdate);
				}
				break;
			}
		} // end while
		}// end $DrAmount>0
		//====== Make Customer Payble if Received Amount greater then his Receibavle ======
		if($DrAmount>0){
		$customer = getRequest('cr_account'); $return_date = formatDate(getRequest('created_date')); $return_by = getFromSession('userid'); 
		$currency = getRequest('currency');	if($voucher_no==""){$voucher_no = $NewVoucherNo;}
		$RMSQL="INSERT INTO ".SALES_RETURN_PAYBLE_TBL."(voucher_no,project_id,customer_id,currency,return_amount,paid_amount,due,payble_source,return_date,created_by)  
		VALUES('$voucher_no','$project_id','$customer','$currency','$DrAmount','0','$DrAmount','Advanced Received','$return_date','$return_by')";
		mysql_query($RMSQL); 
		}
	}
	function adjustSupplierReceibavle($NewVoucherNo,$DrAmount){
		$project_id = getFromSession('project_id');	
		$created_date = formatDate(getRequest('created_date'));	
		//===== for Opening Balance ========
		if($DrAmount>0){
		$rsql= "SELECT voucher_no,debit,paid_amount,due FROM ".DEVIT_VOUCHAR_TBL." WHERE account_head='".getRequest('cr_account')."' 
		AND vouchar_type='Recievable Vouchar' AND due >0 AND status=0 ORDER BY voucher_no ASC";  
		$rres = mysql_query($rsql);
		while($srow = mysql_fetch_object($rres)){
		 $voucher_no = $srow->voucher_no;
		 if($DrAmount>=$srow->due && $srow->due>0){
			$DrAmount = $DrAmount - $srow->due;
			$totalPaidAmount = $srow->paid_amount+$srow->due;
			if($totalPaidAmount==$srow->debit){
			 $pusql="UPDATE ".DEVIT_VOUCHAR_TBL." SET paid_amount='".$totalPaidAmount."',due='0',`status`=1 WHERE voucher_no='$voucher_no'";
			 mysql_query($pusql);
			}
		 }elseif(($DrAmount < $srow->due) && ($srow->due >0 && $DrAmount >0)){
			$presentDue = $srow->due-$DrAmount;
			$PaidAmount = $srow->paid_amount+$DrAmount;
			if($PaidAmount < $srow->debit && $PaidAmount < $srow->due){
			 $DrAmount=0;
			 $pusql2="UPDATE ".DEVIT_VOUCHAR_TBL." SET paid_amount='".$PaidAmount."',due='$presentDue',`status`=0 WHERE voucher_no='$voucher_no'";
			 mysql_query($pusql2);
			}
			break;
		 }
		}// end while
		} //============End DrAmount >0 ===========
		//========Supplier can be Receibavle for my Purchase Return, Beddebs, Adv Paid ========== 
		if($DrAmount>0){
		$SRPSql="SELECT return_id,supplier,return_amount,paid_amount,due FROM ".PURCHASE_RETURN_RECEIBAVLE_TBL." WHERE supplier ='".getRequest('cr_account')."' 
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
				} 
			}elseif(($DrAmount<$existing_due)){					
				if($existing_due>0 && $DrAmount >0){
				$totalpaid 	 = ($paid_amount + $DrAmount); 
				$present_due = ($existing_due - $DrAmount);
				$DrAmount 	 =  0;
				$SRPUpdate="UPDATE ".PURCHASE_RETURN_RECEIBAVLE_TBL." SET paid_amount=$totalpaid,due=$present_due WHERE return_id='$return_id' 
				AND project_id= '$project_id'";
				mysql_query($SRPUpdate);
				}
				break;
			}
		} // end while
		}// end $DrAmount>0
		//====== Make Supplier Payble if Received Amount greater then his Receibavle ======
		if($DrAmount>0){
		$supplier = getRequest('cr_account'); $return_date = formatDate(getRequest('created_date')); $return_by = getFromSession('userid'); 
		$currency = getRequest('currency');	if($voucher_no==""){$voucher_no = $NewVoucherNo;}
		$RMSQL="INSERT INTO ".SALES_RETURN_PAYBLE_TBL."(voucher_no,project_id,customer_id,currency,return_amount,paid_amount,due,payble_source,return_date,created_by)  
		VALUES('$voucher_no','$project_id','$supplier','$currency','$DrAmount','0','$DrAmount','Advanced Received','$return_date','$return_by')";
		mysql_query($RMSQL);  
		}
	}     
	function saveReceivableCheck($voucher_no,$rvoucher_no,$transaction_type,$paid_amount){
	  $requestdata = array();
	  $requestdata = getUserDataSet(PAYABLE_CHECK_TBL);	
	  $requestdata['check_no'] 			= getRequest('check_no');
	  $requestdata['check_issue_date']  = formatDate(getRequest('check_issue_date')); 	 			 
	  $requestdata['created_date']      = formatDate(getRequest('created_date'));
	  $requestdata['acc_head'] 			= getRequest('acc_no'); 
	  $requestdata['customer'] 			= getRequest('cr_account');	  
	  $requestdata['head_type'] 		= "Check"; 
	  $requestdata['voucher_no']        = $voucher_no; 
	  $requestdata['pvoucher_no']       = $rvoucher_no;   
	  $requestdata['paid_amount']  		= $paid_amount;   
	  $requestdata['transaction_type']  = $transaction_type;   
	  $requestdata['project_id']        = getFromSession('project_id');    
	  $requestdata['created_by']        = getFromSession('userid');

	  $info        		=  array();
	  $info['table']	= PAYABLE_CHECK_TBL;
	  $info['data'] 	= $requestdata;     
	  //$info['debug']  	=  true;
	  $res = insert($info);		
	}
	
	function saveAccountJournal($voucher_no,$sub_id,$head_type,$project_id,$description,$DR=NULL,$CR=NULL,$balance,$status,$created_date){
		$head_type		= getHeadType($sub_id);   $created_by = getFromSession('userid');
		$sql = "INSERT INTO ".ACCOUNT_JOURNAL_TBL." (voucher_no,created_date,sub_id,head_type,transaction_type,project_id,description,dr,cr,balance,status,created_by) VALUES('".$voucher_no."','".$created_date."','".$sub_id."','".$head_type."','Received','".$project_id."','".$description."','".$DR."','".$CR."','".$balance."','".$status."','".$created_by."')";
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
