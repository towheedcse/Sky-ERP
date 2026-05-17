<?php
class AdvancedPayment
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
	  $data                				= array();
	  $data['cmd']         				= getRequest('cmd');
	  $data['TotalDebitAmount']			= $this->getTotalDebitAmount();
	  $data['TotalCreditAmount']		= $this->getTotalCreditAmount();
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
		 	mysql_query("START TRANSACTION;");             
            $this->saveDebitVouchar();           
         }
      }	 
	  $data['head_list']   				= $this->getSubAccHeadList();	
	  $data['currency_list']   	 		= $this->getCurrencyList();
      $data['message'] = $msg;
      $data['cmd']     = getRequest('cmd');
	  require_once(CURRENT_APP_SKIN_FILE);      
      return true;
   }
//==================== saveDebitVouchar ====================
   function saveDebitVouchar()
   {     
	  $requestdata = array();
	  $mode_of_payment = getRequest('mode_of_payment');
	  $requestdata = getUserDataSet(DEVIT_VOUCHAR_TBL);	 
	  if($mode_of_payment =="Check"){
	  $requestdata['mode_of_payment'] 	= "Bank";
	  }  
	  $requestdata['account_head']      = getRequest('dr_account');	  		  	  		    
	  $requestdata['head_type']     	= getHeadType(getRequest('dr_account'));   
	  $requestdata['debit']        		= getRequest('amount');    
	  $requestdata['credit']        	= 0; 
	  $requestdata['project_id']        = getFromSession('project_id');    
	  $requestdata['created_by']        = getFromSession('userid'); 
	  $requestdata['created_date']      = formatDate(getRequest('created_date'));
	  //$requestdata['created_date']      = date('Y-m-d h:i:s');
	  $requestdata['vouchar_type']="Payment Vouchar";			
	  $requestdata['paid_amount']   = getRequest('amount');  
	  
	  $voucher_no = $this->createVoucharID();
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
		$this->saveCreditVouchar($voucher_no);
	  }else {	
		header("location:index.php?app=journal&cmd=add");	
	  }  
    }//EOFn  

    function saveCreditVouchar($voucher_no)
 	{     
	  $requestdata = array();
	  $mode_of_payment = getRequest('mode_of_payment');
	  $requestdata = getUserDataSet(CREDIT_VOUCHAR_TBL);				  	  		    
	  if($mode_of_payment =="Check"){
	    $requestdata['mode_of_payment'] 	= "Bank";
		$requestdata['bank_name'] 			= getRequest('bank_name');
		$requestdata['acc_no'] 				= getRequest('acc_no');
		$requestdata['check_no'] 			= getRequest('check_no');
		$requestdata['check_issue_date'] 	= formatDate(getRequest('check_issue_date'));

		$requestdata['account_head']     	= getRequest('acc_no'); 
		$requestdata['debit']        		= 0; 
		$requestdata['credit']        		= getRequest('amount');     
		$requestdata['head_type']     		= "Check";   
	  }else{
		$requestdata['bank_name'] = "";
		$requestdata['acc_no'] = "";
		$requestdata['check_no'] = "";
		$requestdata['check_issue_date'] = "";
		$requestdata['account_head']     = $this->getCashId(getFromSession('project_id')); 
		$requestdata['debit']        	 = 0; 
		$requestdata['credit']        	 = getRequest('amount');     
		$requestdata['head_type']     	 = "Cash";   
	  }
	  $requestdata['project_id']        = getFromSession('project_id');    
	  $requestdata['created_by']        = getFromSession('userid'); 			 
	  $requestdata['created_date']      = formatDate(getRequest('created_date'));
	  //$requestdata['created_date']      = date('Y-m-d h:i:s');	
	  $requestdata['voucher_no']   		= $voucher_no;
	 
	  $info        		=  array();
	  $info['table']	= CREDIT_VOUCHAR_TBL;
	  $info['data'] 	= $requestdata;     
	  //$info['debug']  	=  true;
	  $res = insert($info);
 
 	  if($res['affected_rows']) {
		$CrAmount = getRequest('amount');			
		$DrAmount = getRequest('amount');
		$created_date = formatDate(getRequest('created_date'));
		//======= Dr Account ======	
		$PartyAcc_head = getRequest('dr_account'); 
		$totalPartyCR  = $this->getTotalCreditAmount($PartyAcc_head,getFromSession('project_id'));
		$totalPartyDR  = $this->getTotalDebitAmount($PartyAcc_head,getFromSession('project_id'));					 
		$PartyBalance  = (($totalPartyDR+$DrAmount)-$totalPartyCR);					 
		$this->saveAccountJournal($voucher_no,$PartyAcc_head,"Acc",getFromSession('project_id'),getRequest('description'),$DrAmount,0,$PartyBalance,0,$created_date);	
		if($mode_of_payment =="Cash"){
		//======== Cr Account ========
		 $acc_head = $this->getCashId(getFromSession('project_id')); 
		 $totalCR  = $this->getTotalCreditAmount($acc_head,getFromSession('project_id'));
		 $totalDR  = $this->getTotalDebitAmount($acc_head,getFromSession('project_id'));					 
		 $balance  = ($totalDR-($totalCR+$CrAmount));					 
		 $this->saveAccountJournal($voucher_no,$acc_head,"Acc",getFromSession('project_id'),getRequest('description'),0,$CrAmount,$balance,0,$created_date);	
		 }elseif($mode_of_payment =="Check"){
		 	$this->savePayableCheck($voucher_no,getRequest('purchase_order_no'),"Payment",$CrAmount);
		}
		$project_id = getFromSession('project_id');
		//================ Adjust Due ====================
		$head_type 		= getHeadType(getRequest('dr_account'));
		$account_head 	= getRequest('dr_account');
		if($head_type=="Supplier"){
		$this->adjustSupplierPayble($voucher_no,$account_head,$CrAmount,$created_date);
		}elseif($head_type=="Customer"){		
		$this->adjustCustomerPayble($voucher_no,$account_head,$CrAmount,$created_date);
		}
		mysql_query("COMMIT;");	
		header("location:index.php?app=journal&cmd=vouchar_print&voucher_no=".$requestdata['voucher_no']);
	  }else {	
		header("location:index.php?app=journal&cmd=add");
	  }  
  }//EOFn      
  function adjustSupplierPayble($NewVoucherNo,$account_head,$CrAmount,$created_date){
  	$project_id 	= getFromSession('project_id');
	require_once(CLASS_DIR.'/common.list.class.php');	
	$clistApp = new CommonList();			
	//========== Payble for Purchase ===========
	$PMsql = "SELECT voucher_no,net_payble,paid_amount,due,item_received_amount FROM ".PURCHASE_MASTER_TBL." WHERE supplier ='".$account_head."' 
	AND project_id = '$project_id' AND paid_amount < net_payble AND due >0";
	$PMRes = mysql_query($PMsql);
	$PMnum = mysql_num_rows($PMRes);
	if($PMnum>0 && $CrAmount>0){
		while($PMrow = mysql_fetch_object($PMRes)){
			$voucher_no 	= $PMrow->voucher_no;
			$net_payble 	= $PMrow->net_payble;
			$paid_amount 	= $PMrow->paid_amount;
			$existing_due 	= $PMrow->due;
			$item_received_amount = $PMrow->item_received_amount;
			$totalPaidAmount 	  = ($CrAmount+$paid_amount);
			if(($CrAmount>=$existing_due) && ($item_received_amount>=$net_payble)){
				if($existing_due>0){
				$CrAmount 	= $CrAmount - $existing_due;
				$total_paid = $paid_amount + $existing_due; 
				$PMUpdate = "UPDATE ".PURCHASE_MASTER_TBL." SET paid_amount = $total_paid, due = 0  WHERE voucher_no ='$voucher_no' AND project_id = '$project_id'";
				mysql_query($PMUpdate);
				$clistApp->saveVoucherAdjustHistory($NewVoucherNo,$project_id,PURCHASE_MASTER_TBL,$voucher_no,$existing_due,"+");
				} 
			}elseif(($CrAmount < $existing_due) && ($item_received_amount>0)){
				if($existing_due>0 && $CrAmount >0){
				$total_paid   = $paid_amount + $CrAmount;
				$present_due  = ($existing_due - $CrAmount);		
				$adjustAmount = $CrAmount; $CrAmount = 0;
				$PMUpdate = "UPDATE ".PURCHASE_MASTER_TBL." SET paid_amount=$total_paid,due=$present_due WHERE voucher_no='$voucher_no' AND project_id= '$project_id'";
				mysql_query($PMUpdate);
				$clistApp->saveVoucherAdjustHistory($NewVoucherNo,$project_id,PURCHASE_MASTER_TBL,$voucher_no,$adjustAmount,"+");
				}
				break;					
			}//end else
		} // end while
	}// end $PMnum>0 && $CrAmount>0 
	//========== Payble for Opening Balance ===========			
	if($CrAmount>0){
		$rsql= "SELECT dr.voucher_no,cr.credit as debit,dr.paid_amount,dr.due FROM ".CREDIT_VOUCHAR_TBL." as cr,".DEVIT_VOUCHAR_TBL." as dr 
		WHERE dr.voucher_no=cr.voucher_no AND cr.account_head='".$account_head."' AND cr.vouchar_type='Payable Vouchar' AND dr.due >0 AND dr.status=0";  
		$rres = mysql_query($rsql);
		while($srow = mysql_fetch_object($rres)){
		 $voucher_no = $srow->voucher_no;
		 if($CrAmount >= $srow->due && $srow->due >0){
			$CrAmount = $CrAmount - $srow->due; $adjustAmount = $srow->due;
			$totalPaidAmount = $srow->paid_amount+$srow->due;
			if($totalPaidAmount==$srow->debit){
			 $pusql="UPDATE ".DEVIT_VOUCHAR_TBL." SET paid_amount='".$totalPaidAmount."', due='0',`status`=1 WHERE voucher_no='$voucher_no'";
			 mysql_query($pusql);
			 $clistApp->saveVoucherAdjustHistory($NewVoucherNo,$project_id,DEVIT_VOUCHAR_TBL,$voucher_no,$adjustAmount,"+");
			}
		 }elseif(($CrAmount < $srow->due) && ($srow->due >0 && $CrAmount >0)){
			$presentDue = ($srow->due - $CrAmount);
			$PaidAmount = ($srow->paid_amount + $CrAmount);
			if($PaidAmount < $srow->debit){
			 $adjustAmount = $CrAmount; $CrAmount = 0;
			 $pusql2="UPDATE ".DEVIT_VOUCHAR_TBL." SET paid_amount='".$PaidAmount."', due='$presentDue',`status`=0 WHERE voucher_no='$voucher_no'";
			 mysql_query($pusql2);
			 $clistApp->saveVoucherAdjustHistory($NewVoucherNo,$project_id,DEVIT_VOUCHAR_TBL,$voucher_no,$adjustAmount,"+");
			}
			break;
		 }
		}// end while
	} //============End CrAmount >0 ===========	
	//========Supplier can be Payble for my Adv Received to his/him ========== 
	if($CrAmount>0){
	$SRPSql="SELECT return_id,customer_id,return_amount,paid_amount,due FROM ".SALES_RETURN_PAYBLE_TBL." WHERE customer_id ='".$account_head."' 
	 AND project_id = '$project_id' AND paid_amount < return_amount AND due >0  ORDER BY return_id ASC"; // AND fyear='$fyear'
	$SRPRes = mysql_query($SRPSql);
	while($srprow = mysql_fetch_object($SRPRes)){
		$return_id 	= $srprow->return_id;
		$net_payble 	= $srprow->return_amount;
		$paid_amount 	= $srprow->paid_amount;
		$existing_due 	= $srprow->due;
		if(($CrAmount>=$existing_due)){
			$CrAmount 		= $CrAmount - $existing_due;
			if($existing_due>0){						
			$total_paid = ($paid_amount + $existing_due); 
			$SRUpSql = "UPDATE ".SALES_RETURN_PAYBLE_TBL." SET paid_amount=$total_paid, due=0  WHERE return_id ='$return_id' AND project_id = '$project_id'";
			mysql_query($SRUpSql);
			$clistApp->saveVoucherAdjustHistory($NewVoucherNo,$project_id,SALES_RETURN_PAYBLE_TBL,$return_id,$existing_due,"+");
			} 
		}elseif(($CrAmount < $existing_due)){					
			if($existing_due>0 && $CrAmount >0){
			$totalpaid 	 = ($paid_amount + $CrAmount); 
			$present_due = ($existing_due - $CrAmount); $adjustAmount = $CrAmount;
			$CrAmount 	 = 0;
			$SRPUpdate="UPDATE ".SALES_RETURN_PAYBLE_TBL." SET paid_amount=$totalpaid, due=$present_due WHERE return_id='$return_id' AND project_id= '$project_id'";
			mysql_query($SRPUpdate);
			$clistApp->saveVoucherAdjustHistory($NewVoucherNo,$project_id,SALES_RETURN_PAYBLE_TBL,$return_id,$adjustAmount,"+");
			}
			break;
		}
	} // end while
	}// end $CrAmount>0
			
	//====== Make Supplier Receibavle if Payment Amount is greater then his Payble ======
	if($CrAmount>0){
	$supplier = $account_head; $return_date = $created_date; $return_by = getFromSession('userid'); 
	$currency = getRequest('currency');	if($currency==""){$currency = 1;} if($voucher_no==""){$voucher_no = $NewVoucherNo;}
	$RMSQL="INSERT INTO ".PURCHASE_RETURN_RECEIBAVLE_TBL."(voucher_no,project_id,supplier,currency,return_amount,paid_amount,due,payment_source,return_date,created_by)  	VALUES('$voucher_no','$project_id','$supplier','$currency','$CrAmount','0','$CrAmount','Advanced Payment','$return_date','$return_by')";
	mysql_query($RMSQL); 
	$clistApp->saveVoucherAdjustHistory($NewVoucherNo,$project_id,PURCHASE_RETURN_RECEIBAVLE_TBL,$voucher_no,$CrAmount,"Receibavle ROA");
	}
  }
  function adjustCustomerPayble($NewVoucherNo,$account_head,$CrAmount,$created_date){
  	$project_id = getFromSession('project_id');	
	require_once(CLASS_DIR.'/common.list.class.php');	
	$clistApp = new CommonList();	
  	//===== for Opening Balance ========
  	if($CrAmount>0){
	$rsql= "SELECT dr.voucher_no,cr.credit as debit,dr.paid_amount,dr.due FROM ".CREDIT_VOUCHAR_TBL." as cr,".DEVIT_VOUCHAR_TBL." as dr 
	WHERE dr.voucher_no=cr.voucher_no AND cr.account_head='".$account_head."' AND cr.vouchar_type='Payable Vouchar' AND dr.due >0 AND dr.status=0";   
	$rres = mysql_query($rsql);
	while($srow = mysql_fetch_object($rres)){
	 $voucher_no = $srow->voucher_no;
	 if($CrAmount >= $srow->due && $srow->due >0){
		$CrAmount = ($CrAmount - $srow->due); $adjustAmount = $srow->due;
		$totalPaidAmount = ($srow->paid_amount+$srow->due);
		if($totalPaidAmount==$srow->debit){
		 $pusql="UPDATE ".DEVIT_VOUCHAR_TBL." SET paid_amount='".$totalPaidAmount."',due='0',`status`=1 WHERE voucher_no='$voucher_no'";
		 mysql_query($pusql);
		 $clistApp->saveVoucherAdjustHistory($NewVoucherNo,$project_id,DEVIT_VOUCHAR_TBL,$voucher_no,$adjustAmount,"-");
		}
	 }elseif(($CrAmount < $srow->due) && ($srow->due >0 && $CrAmount >0)){
		$presentDue = ($srow->due - $CrAmount);
		$PaidAmount = ($srow->paid_amount + $CrAmount);
		if($PaidAmount < $srow->debit){
		 $adjustAmount = $CrAmount; $CrAmount=0;
		 $pusql2="UPDATE ".DEVIT_VOUCHAR_TBL." SET paid_amount='".$PaidAmount."',due='$presentDue',`status`=0 WHERE voucher_no='$voucher_no'";
		 mysql_query($pusql2);
		 $clistApp->saveVoucherAdjustHistory($NewVoucherNo,$project_id,DEVIT_VOUCHAR_TBL,$voucher_no,$adjustAmount,"-");
		}
		break;
	 }
	}// end while
	} //============End CrAmount >0 ===========
	
	//=======Customer can be Payble for his Sales Return, Beddebs, Adv Paid ======= 
	if($CrAmount>0){
	$SRPSql="SELECT return_id,customer_id,return_amount,paid_amount,due FROM ".SALES_RETURN_PAYBLE_TBL." WHERE customer_id ='".$account_head."' 
	 AND project_id = '$project_id' AND paid_amount < return_amount AND due >0  ORDER BY return_id ASC"; // AND fyear='$fyear'
	$SRPRes = mysql_query($SRPSql);
	while($srprow = mysql_fetch_object($SRPRes)){
		$return_id 		= $srprow->return_id;
		$net_payble 	= $srprow->return_amount;
		$paid_amount 	= $srprow->paid_amount;
		$existing_due 	= $srprow->due;
		if(($CrAmount>=$existing_due)){
			$CrAmount 	= $CrAmount - $existing_due;
			if($existing_due>0){						
			$total_paid = ($paid_amount + $existing_due); 
			$SRUpSql = "UPDATE ".SALES_RETURN_PAYBLE_TBL." SET paid_amount=$total_paid, due=0  WHERE return_id ='$return_id' AND project_id = '$project_id'";
			mysql_query($SRUpSql);
			$clistApp->saveVoucherAdjustHistory($NewVoucherNo,$project_id,SALES_RETURN_PAYBLE_TBL,$return_id,$existing_due,"-");
			} 
		}elseif(($CrAmount<$existing_due)){					
			if($existing_due>0 && $CrAmount>0){
			$totalpaid 	 = ($paid_amount + $CrAmount); 
			$present_due = ($existing_due - $CrAmount);
			$adjustAmount = $CrAmount; $CrAmount = 0;
			$SRPUpdate="UPDATE ".SALES_RETURN_PAYBLE_TBL." SET paid_amount=$totalpaid,due=$present_due WHERE return_id='$return_id' AND project_id='$project_id'";
			mysql_query($SRPUpdate);
			$clistApp->saveVoucherAdjustHistory($NewVoucherNo,$project_id,SALES_RETURN_PAYBLE_TBL,$return_id,$adjustAmount,"-");
			}
			break;
		}
	} // end while
	} // end $CrAmount>0
	//====== Make Customer Receibavle if Payment Amount is greater then his Payble ======
	if($CrAmount>0){
	$customer = $account_head; $return_date = $created_date; $return_by = getFromSession('userid'); 
	$currency = getRequest('currency');	if($currency==""){$currency = 1;}  if($voucher_no==""){$voucher_no = $NewVoucherNo;}
	$RMSQL="INSERT INTO ".PURCHASE_RETURN_RECEIBAVLE_TBL."(voucher_no,project_id,supplier,currency,return_amount,paid_amount,due,payment_source,return_date,created_by)  	VALUES('$voucher_no','$project_id','$customer','$currency','$CrAmount','0','$CrAmount','Advanced Payment','$return_date','$return_by')";
	mysql_query($RMSQL); 
	$clistApp->saveVoucherAdjustHistory($NewVoucherNo,$project_id,PURCHASE_RETURN_RECEIBAVLE_TBL,$voucher_no,$CrAmount,"Receibavle ROA"); 
	}
  }
  
  function saveAccountJournal($voucher_no,$sub_id,$head_type,$project_id,$description,$DR=NULL,$CR=NULL,$balance,$status,$created_date){
    $head_type		= getHeadType($sub_id);   $created_by = getFromSession('userid'); 
	$sql = "INSERT INTO ".ACCOUNT_JOURNAL_TBL." (voucher_no,created_date,sub_id,head_type,transaction_type,project_id,description,dr,cr,balance,status,created_by) 
	VALUES('".$voucher_no."','".$created_date."','".$sub_id."','".$head_type."','Payment','".$project_id."','".$description."','".$DR."','".$CR."','".$balance."','".$status."','".$created_by."')";
	mysql_query($sql);
  }
  function savePayableCheck($voucher_no,$pvoucher_no,$transaction_type,$paid_amount){
	  $requestdata = array();
	  $requestdata = getUserDataSet(PAYABLE_CHECK_TBL);	
	  $requestdata['check_no'] 			= getRequest('check_no');
	  $requestdata['check_issue_date']  = formatDate(getRequest('check_issue_date')); 
	  $requestdata['created_date']      = formatDate(getRequest('created_date'));
	  $requestdata['acc_head'] 			= getRequest("dr_account"); 
	  $requestdata['head_type'] 		= "Check"; 
	  $requestdata['voucher_no']        = $voucher_no;  
	  $requestdata['pvoucher_no']       = $pvoucher_no; 
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

    function createVoucharID(){
      $info = array();
      $info['table'] = DEVIT_VOUCHAR_TBL;
      $info['fields'] = array('max(voucher_no) as maxvoucher');      
      $res = select($info);      
      $maxvoucherId = 'D0000000';      
      if(count($res)){
         foreach($res as $v){
         	 if($v->maxvoucher){
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
      if(count($result)){
         foreach($result as $i=>$v)
         {
            $data[$i] = $v;             
         }
      }
      return $data;
   }
   function getCurrencyList() {
      $info            = array();
      $info['table']   = CURRENCY_TBL;
      //$info['fields'] = array('currency_id', 'name');
	  $info['orderby'] = array("currency_name ASC");
      $info['debug']   = false;
      $result          = select($info);
      //dBug($result);
      $data            = array();
      if(count($result)){
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
