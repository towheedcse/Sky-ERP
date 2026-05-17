<?php
class CustomerYearEnding
{
   function run() {     
		$cmd    = getRequest('cmd');
		$u_t_id = getFromSession('u_type_id');
		if($u_t_id == 101) 
		{      
		  switch($cmd) { 
		  	 case 'add'           : $screen = $this->showEditor($msg); break;
			 default              : $cmd = 'list'; $screen = $this->showEditor($msg);   break;
		  }
		}else {
      		header("location:index.php?app=user_home&msg=You are not authorised !!!");
      	} 
		
		return true;
  }
  
  function showEditor($msg=NULL)
  {
	// All Showing rows (4583 total)  but customer : 2977 	
	/* ====== Customer Year Ending 31 Dec 2017 =======*/
	mysql_query("START TRANSACTION;");
	$project_id     = getFromSession('project_id'); 
	   
	echo $cusql= "SELECT s.sub_id, SUM(a.dr)-SUM(a.cr) as balance  FROM ".ACCOUNT_JOURNAL_TBL." as a,".SUB_ACC_HEAD_TBL." as s WHERE BINARY s.sub_id = BINARY a.sub_id AND s.`head_type` = 'Current Assets' AND s.`sub_headtype` = 'S128' AND s.`child_head` = 'C000105' AND a.project_id = '$project_id' AND a.`created_date` <= '2020-12-31' GROUP BY a.`sub_id` ORDER BY s.`sub_id` ASC";
		
	$cures = mysql_query($cusql); $sl=0; 
	$numrow = mysql_num_rows($cures);
	while($arow=mysql_fetch_object($cures)){
		$customer   = $arow->sub_id;
		$EndBalance = $arow->balance;
		if($EndBalance ==""){ $EndBalance=0;}  
			if($customer !=""){
			//==== Start New Year Ending =====
			
			$voucher_no = $this->createVoucharID();
			if($voucher_no !="" && $customer !=""){
				$this->customerYearEnding($voucher_no,$customer,$EndBalance,"2020-12-31");
				$customer=""; $EndBalance=0;
				$sl++;
			}
			
			//==== End New Year Ending =====
			/*
			//==== Start Update Year Ending =====
			$voucher_no = $this->getVoucherNo($customer);	
			if($voucher_no !="" && $customer !=""){				
				$this->deleteCustomerOpening($voucher_no);		
				$this->customerYearEnding($voucher_no,$customer,$EndBalance,"2020-12-31");			
				$customer=""; $EndBalance=0;
				$sl++;
			}
			//==== End Update Year Ending =====
			*/
		}
	} // End While
	echo "Total =".$numrow." Ending =".$sl;
	if($numrow==$sl){
		mysql_query("COMMIT;");
		echo "<br>====== Done =======";	
	}else{
		mysql_query("ROLLBACK;");
		echo "<br>====== Failed =======";
	}
	//======= End Closing =======		
   }
   
   //===== Start customer Year Ending =====
   function customerYearEnding($voucher_no,$customer,$PartyBalance,$ob_date){	
	
	$created_date  = $ob_date;
	$project_id    = getFromSession('project_id');    
	$created_by    = getFromSession('userid');			
	if($PartyBalance >=0 && $customer !=""){	
	$op_type = "Dr";			 
	$this->saveAccountJournal($voucher_no,$customer,"Customer",$project_id,"OB",$PartyBalance,0,$PartyBalance,1,$ob_date);
	}elseif($PartyBalance < 0 && $customer!=""){
	$op_type = "Cr";						 
	$this->saveAccountJournal($voucher_no,$customer,"Customer",$project_id,"OB",0,abs($PartyBalance),$PartyBalance,1,$ob_date);
	}
				
	if($PartyBalance >0 && $customer !=""){ 
	$vouchar_type ='Recievable Vouchar'; $status = 0; $debit = $PartyBalance; $details = "Closing Recievable amount of 31 Dec 2020";
	$sqlDV="INSERT INTO ".NDB_NAME.".credit_vouchar (voucher_no,account_head,project_id,head_type,transaction_type,vouchar_type,transaction_name,
	credit,description,list_view,created_by,created_date) VALUES('$voucher_no','A000014','$project_id','Accounts Recievable','Opening Balance',
	'$vouchar_type','Opening Recievable','$debit','$details','Active','$created_by','$created_date')";	
	$res1= mysql_query($sqlDV);
	
	$sqlCV="INSERT INTO ".NDB_NAME.".cs_delivery_product 
	(voucher_no,account_head,project_id,head_type,transaction_type,vouchar_type,transaction_name,
	debit,paid_amount,due,description,list_view,created_by,created_date,status) VALUES('$voucher_no','$customer','$project_id','Customer',
	'Opening Balance','$vouchar_type','Opening Recievable','$debit','0','$debit','$details','Active','$created_by','$created_date','0')";
	$res2=mysql_query($sqlCV);
	
	}elseif($PartyBalance <0 && $customer !=""){ 
	
	$vouchar_type ='Payable Vouchar'; $status = 0; $debit = abs($PartyBalance); $credit = abs($PartyBalance); 
	$details = "Closing Payable amount of 31 Dec 2020";
	$sqlCV="INSERT INTO ".NDB_NAME.".credit_vouchar (voucher_no,account_head,head_type,project_id,mode_of_payment,vouchar_type,transaction_name,
	credit,description,list_view,created_by,created_date) VALUES('$voucher_no','$customer','Customer','$project_id','Others',
	'$vouchar_type','Opening Payble','$credit','$details','Active','$created_by','$created_date')";
	$res1= mysql_query($sqlCV);
	
	$sqlDV="INSERT INTO ".NDB_NAME.".cs_delivery_product 
	(voucher_no,account_head,project_id,head_type,mode_of_payment,vouchar_type,transaction_name,
	debit,paid_amount,due,description,list_view,created_by,created_date,status) VALUES('$voucher_no','A000028','$project_id','Accounts Payable',
	'Others','$vouchar_type','Opening Payble','$debit','0','$debit','$details','Active','$created_by','$created_date','0')";
	$res2=mysql_query($sqlDV);
	
	}elseif($PartyBalance == 0 && $customer !=""){ 
	
	$sqlCV="INSERT INTO ".NDB_NAME.".credit_vouchar (voucher_no,account_head,project_id,head_type,mode_of_payment,vouchar_type,transaction_name,
	credit,description,list_view,created_by,created_date) VALUES('$voucher_no','A000014','$project_id','Opening Balance','Others',
	'Others Vouchar','OB','0','OB','Hidden','$created_by','$created_date')";
	$res1= mysql_query($sqlCV);
	$sqlDV="INSERT INTO ".NDB_NAME.".cs_delivery_product(voucher_no,account_head,project_id,head_type,mode_of_payment,vouchar_type,
	transaction_name,debit,paid_amount,due,description,list_view,created_by,created_date,status) 
	VALUES('$voucher_no','$customer','$project_id','Customer','Others','Others Vouchar','OB','0','0','0',
	'OB','Hidden','$created_by','$created_date','1')";
	$res2=mysql_query($sqlDV);
	
	}
	if($customer !=""){		
	$opSQL="INSERT INTO ".NDB_NAME.".opening_balance (voucher_no,project_id,head_id,head_type,fyear,opening_balance,op_type,opening_month,
	created_by) VALUES('$voucher_no','$project_id','$customer','Customer','FY004','$PartyBalance','$op_type','01','$created_by')";
	mysql_query($opSQL); 
	}
	
	$customer = ""; $balance=0;
	return $voucher_no;
   }
   
   function deleteCustomerOpening($voucher_no){
	 
	 if($voucher_no!=""){
	 $this->deleteRecord(NDB_NAME.".credit_vouchar","voucher_no",$voucher_no,"",""); 
	 $this->deleteRecord(NDB_NAME.".cs_delivery_product","voucher_no",$voucher_no,"",""); 
	 $this->deleteRecord(NDB_NAME.".opening_balance","voucher_no",$voucher_no,"","");	  
	 $this->deleteRecord(NDB_NAME.".account_journal","voucher_no",$voucher_no,"","");
	 }	 
   }
   
   function deleteRecord($TBL,$idName,$idValue,$redirect,$cmd){
   	  if($idValue!=""){ 
      	$info = array();
      	$info['table'] = $TBL;
      	$info['where'] = "$idName='$idValue'";
      	//$info['debug'] = true;
      	$res = delete($info);      	
      	if($res){
      	  return true;    	   
      	}else{
      	  return false;
      	}      	
      }
   }
   function getVoucherNo($sub_id){
	 $SQL="SELECT voucher_no FROM ".NDB_NAME.".account_journal WHERE created_date='2017-12-31' AND head_type ='Customer' AND transaction_type='OB' AND BINARY sub_id='$sub_id'"; 
	 $res = mysql_query($SQL); 
	 if(mysql_num_rows($res)>0){
		 $row = mysql_fetch_object($res);
		 $voucher_no = $row->voucher_no;
	 }else{
		 $voucher_no = $this->createVoucharID();
	 }
	 return $voucher_no;
   }
   // $this->saveAccountJournal($voucher_no,$customer,"Customer",$project_id,"OB",0,$PartyBalance,$PartyBalance,1,$ob_date);
   function saveAccountJournal($voucher_no,$sub_id,$head_type,$project_id,$description,$DR=NULL,$CR=NULL,$balance,$status,$created_date){
		$created_by = getFromSession('userid'); $delivery_id=0;
		$sql = "INSERT INTO ".NDB_NAME.".account_journal (voucher_no,delivery_id,created_date,sub_id,head_type,transaction_type,
		project_id,description,dr,cr,balance,status,created_by) VALUES('".$voucher_no."','".$delivery_id."','".$created_date."','"
		.$sub_id."','".$head_type."','".$description."','".$project_id."','".$description."','".$DR."','".$CR."','".$balance."','"
		.$status."','".$created_by."')";
		mysql_query($sql); 
   }
   function getTotalCreditAmount($acc_head,$project_id){
   		$sql = "SELECT sum(`cr`) as credit_amount FROM ".ACCOUNT_JOURNAL_TBL." WHERE BINARY sub_id = '$acc_head' AND project_id = '$project_id'";
		$row = mysql_fetch_object(mysql_query($sql));
		$credit_amount = $row->credit_amount;
		if(empty($credit_amount)){
			$credit_amount = 0;
		}
		return $credit_amount;
   }
  
   function getTotalDebitAmount($acc_head,$project_id){
   		$sql = "SELECT sum(`dr`) as debit_amount FROM ".ACCOUNT_JOURNAL_TBL." WHERE BINARY sub_id = '$acc_head' AND project_id = '$project_id'";
		$row = mysql_fetch_object(mysql_query($sql));
		$debit_amount = $row->debit_amount;
		if(empty($debit_amount)){
			$debit_amount = 0;
		}
		return $debit_amount;
   }    
   function createVoucharID()
   {
      $info = array();
      $info['table'] = NDB_NAME.".cs_delivery_product";
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
      
    
} // End class
?>
