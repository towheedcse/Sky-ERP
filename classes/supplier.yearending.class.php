<?php
class SupplierYearEnding
{
   function run() {     
		$cmd    = getRequest('cmd');
		$u_t_id = getFromSession('u_type_id');
		if($u_t_id == 101) 
		{      
		  switch($cmd) { 
		  	 case 'add'          : $screen = $this->showEditor($msg); break;
			 default             : $cmd = 'list'; $screen = $this->showEditor($msg);   break;
		  }
		}else {
      		header("location:index.php?app=user_home&msg=You are not authorised !!!");
      	} 
		
		return true;
  }
  function showEditor($msg=NULL)
  {
	 	
	/* ====== Supplier Year Ending 31 Dec 2017 =======*/
	/* // Total Supplier: 106
	mysql_query("START TRANSACTION;");
	$project_id     = getFromSession('project_id');   
	
	echo $cusql= "SELECT s.supplier_code, SUM(a.dr)-SUM(a.cr) as balance  FROM ".ACCOUNT_JOURNAL_TBL." as a,".SUPPLIER_TBL." as s WHERE BINARY s.supplier_code = BINARY a.sub_id AND a.project_id = '$project_id' AND a.`created_date` <= '2020-12-31' GROUP BY s.`supplier_code` ORDER BY s.`supplier_code` ASC ";  
	
	$cures = mysql_query($cusql); $sl=0; 
	$numrow = mysql_num_rows($cures);
	while($arow=mysql_fetch_object($cures)){
		$supplier = $arow->supplier_code;
		$EndBalance = $arow->balance;
		if($EndBalance==""){ $EndBalance=0;}  
		if($supplier !=""){
		//==== Year Ending =====	
		$voucher_no = $this->createVoucharID();
		$this->supplierYearEnding($voucher_no,$supplier,$EndBalance,"2020-12-31");
		
		//==== Update Year Ending =====
		
		//$voucher_no = $this->getVoucherNo($supplier,"Supplier");		
		//$this->deleteOpening($voucher_no);	
		//$this->supplierYearEnding($voucher_no,$supplier,$EndBalance,"2020-12-31");
		
		$supplier=""; $EndBalance=0;
		$sl++;
		}
	} 
	echo "Total =".$numrow." Ending =".$sl;
	
	if($numrow==$sl){
		mysql_query("COMMIT;");
		echo "<br>====== Done Supplier =======<br>";	
	}else{
		mysql_query("ROLLBACK;");
		echo "<br>====== Failed Supplier <br>=======";
	}
	*/
	//======= End Supplier Closing =======	
	
		
	/* ====== Start Bank Year Ending 31 Dec 2017 =======*/
	/*
	mysql_query("START TRANSACTION;");
	$project_id  = getFromSession('project_id');   
	
	echo $bsql= "SELECT s.bank_account_no, SUM(a.dr)-SUM(a.cr) as balance  FROM ".ACCOUNT_JOURNAL_TBL." as a,".BANK_ACCOUNT_TBL." as s WHERE BINARY s.bank_account_no = BINARY a.sub_id AND a.project_id = '$project_id' AND a.`created_date` <= '2020-12-31' GROUP BY s.`bank_account_no` 
	ORDER BY s.`bank_account_no` ASC "; 
	$bres = mysql_query($bsql); $sl=0; 
	
	$bnumrow = mysql_num_rows($bres);
	while($brow=mysql_fetch_object($bres)){		
		$bank_account = $brow->bank_account_no; 
		$BankBalance = $brow->balance;
		if($BankBalance==""){ $BankBalance=0;} 
		
		if($bank_account !=""){
			//==== Bank Year Ending =====
			
			$voucher_no = $this->createVoucharID();
			$this->bankYearEnding($voucher_no,$bank_account,$BankBalance,"2020-12-31");
			
			//==== Update Bank Year Ending =====
			
			//$voucher_no = $this->getVoucherNo($bank_account,"Bank");		
			//$this->deleteOpening($voucher_no);	
			//$this->bankYearEnding($voucher_no,$bank_account,$BankBalance,"2020-12-31");
		
			$bank_account = ""; $BankBalance = 0;
			$sl++;
		}
	} 
	
	echo "<br>Total =".$bnumrow." Ending =".$sl;
	if($bnumrow==$sl){
		mysql_query("COMMIT;");
		echo "<br>====== Done Bank =======<br>";	
	}else{
		mysql_query("ROLLBACK;");
		echo "<br>====== Failed Bank <br>=======";
	}
	*/
	//======= End Bank Closing =======
	
	/* ====== Start Account Year Ending 31 Dec 2017 =======*/
	/**/
	mysql_query("START TRANSACTION;");
	$project_id  = getFromSession('project_id');   
	
	echo $cusql= "SELECT s.sub_id, SUM(a.dr)-SUM(a.cr) as balance  FROM ".ACCOUNT_JOURNAL_TBL." as a,".SUB_ACC_HEAD_TBL." as s WHERE BINARY s.sub_id = BINARY a.sub_id AND a.project_id = '$project_id' AND s.`child_head` != 'C000105' AND a.`created_date` <= '2020-12-31' GROUP BY s.`sub_id` ORDER BY s.`sub_id` ASC "; 
		
	$cures = mysql_query($cusql); $sl=0; 
	$numrow = mysql_num_rows($cures);
	while($arow=mysql_fetch_object($cures)){
		$account_id = $arow->sub_id;
		$EndBalance = $arow->balance;
		if($EndBalance==""){ $EndBalance=0;}  
		if($account_id!=""){
		//==== Account Year Ending =====
			
		$voucher_no = $this->createVoucharID();
		$this->accountYearEnding($voucher_no,$account_id,$EndBalance,"2020-12-31");
		
		//==== Update Bank Year Ending =====
		
		//$head_type		= getHeadType($account_id);		
		//$voucher_no = $this->getVoucherNo($account_id,$head_type);		
		//$this->deleteOpening($voucher_no);
		//$this->accountYearEnding($voucher_no,$account_id,$EndBalance,"2017-12-31");
		
		$account_id=""; $EndBalance=0;
		$sl++;
		}
	} 
	echo "Total =".$numrow." Ending =".$sl;

	if($numrow==$sl){
		mysql_query("COMMIT;");
		echo "<br>====== Done Account Year Ending =======";	
	}else{
		mysql_query("ROLLBACK;");
		echo "<br>====== Failed =======";
	}
	/**/
	//======= End Account Closing =======
		
   }
   
   //===== Start Supplier Year Ending =====
   function supplierYearEnding($voucher_no,$supplier,$PartyBalance,$ob_date){	
		
	$created_date   = $ob_date;
	$project_id     = getFromSession('project_id');    
	$created_by     = getFromSession('userid');	
			
	if($PartyBalance >=0 && $supplier !=""){	
	$op_type = "Dr";			 
	$this->saveAccountJournal($voucher_no,$supplier,"Supplier",$project_id,"OB",$PartyBalance,0,$PartyBalance,1,$ob_date);
	}elseif($PartyBalance < 0 && $supplier!=""){
	$op_type = "Cr";						 
	$this->saveAccountJournal($voucher_no,$supplier,"Supplier",$project_id,"OB",0,abs($PartyBalance),$PartyBalance,1,$ob_date);
	}
				
	if($PartyBalance >=0 && $supplier!=""){ 
	$vouchar_type ='Recievable Vouchar'; $status = 0; $debit = $PartyBalance; $details = "Closing Recievable amount of 31 Dec 2020";
	$sqlDV="INSERT INTO ".NDB_NAME.".credit_vouchar (voucher_no,account_head,project_id,head_type,transaction_type,vouchar_type,transaction_name,
	credit,description,list_view,created_by,created_date) VALUES('$voucher_no','A000014','$project_id','Accounts Recievable','Opening Balance',
	'$vouchar_type','Opening Recievable','$debit','$details','Active','$created_by','$created_date')";	
	$res1= mysql_query($sqlDV);
	
	$sqlCV="INSERT INTO ".NDB_NAME.".cs_delivery_product 
	(voucher_no,account_head,project_id,head_type,transaction_type,vouchar_type,transaction_name,
	debit,paid_amount,due,description,list_view,created_by,created_date,status) VALUES('$voucher_no','$supplier','$project_id','Supplier',
	'Opening Balance','$vouchar_type','Opening Recievable','$debit','0','$debit','$details','Active','$created_by','$created_date','0')";
	$res2=mysql_query($sqlCV);
	
	}elseif($PartyBalance < 0 && $supplier !=""){ 
	
	$vouchar_type ='Payable Vouchar'; $status = 0; $debit = abs($PartyBalance); $credit = abs($PartyBalance); 
	$details = "Closing Payable amount of 31 Dec 2020";
	$sqlCV="INSERT INTO ".NDB_NAME.".credit_vouchar (voucher_no,account_head,head_type,project_id,mode_of_payment,vouchar_type,transaction_name,
	credit,description,list_view,created_by,created_date) VALUES('$voucher_no','$supplier','Supplier','$project_id','Others',
	'$vouchar_type','Opening Payable','$credit','$details','Active','$created_by','$created_date')";
	$res1= mysql_query($sqlCV);
	
	$sqlDV="INSERT INTO ".NDB_NAME.".cs_delivery_product 
	(voucher_no,account_head,project_id,head_type,mode_of_payment,vouchar_type,transaction_name,
	debit,paid_amount,due,description,list_view,created_by,created_date,status) VALUES('$voucher_no','A000028','$project_id','Accounts Payable',
	'Others','$vouchar_type','Opening Payble','$debit','0','$debit','$details','Active','$created_by','$created_date','0')";
	$res2=mysql_query($sqlDV);	
	}
	if($supplier !=""){		
	$opSQL="INSERT INTO ".NDB_NAME.".opening_balance (voucher_no,project_id,head_id,head_type,fyear,opening_balance,op_type,opening_month,
	created_by) VALUES('$voucher_no','$project_id','$supplier','Supplier','FY004','$PartyBalance','$op_type','01','$created_by')";
	mysql_query($opSQL); 
	}
	$PartyBalance=0;
	return $voucher_no;
   }
   
   //===== Start Bank Year Ending =====
   function bankYearEnding($voucher_no,$bank_id,$BankBalance,$ob_date){	
		
	$created_date   = $ob_date;
	$project_id     = getFromSession('project_id');    
	$created_by     = getFromSession('userid');	
			
	if($BankBalance >=0 && $bank_id !="" ){	
	$op_type = "Dr";			 
	$this->saveAccountJournal($voucher_no,$bank_id,"Bank",$project_id,"OB",$BankBalance,0,$BankBalance,1,$ob_date);
	}elseif($BankBalance < 0 && $bank_id !="" ){
	$op_type = "Cr";						 
	$this->saveAccountJournal($voucher_no,$bank_id,"Bank",$project_id,"OB",0,abs($BankBalance),$BankBalance,1,$ob_date);
	}
				
	if($bank_id !=""){ 
	$debit = abs($BankBalance); $credit = abs($BankBalance);
	 
	$sqlCV="INSERT INTO ".NDB_NAME.".credit_vouchar (voucher_no,account_head,project_id,head_type,mode_of_payment,vouchar_type,transaction_name,
	credit,description,list_view,created_by,created_date) VALUES('$voucher_no','A000014','$project_id','Bank','Others',
	'Others Vouchar','OB','$credit','OB','Hidden','$created_by','$created_date')";
	$res1= mysql_query($sqlCV);
	
	$sqlDV="INSERT INTO ".NDB_NAME.".cs_delivery_product(voucher_no,account_head,project_id,head_type,mode_of_payment,vouchar_type,
	transaction_name,debit,paid_amount,due,description,list_view,created_by,created_date,status) 
	VALUES('$voucher_no','$bank_id','$project_id','Bank','Others','Others Vouchar','OB','$debit','0','0',
	'OB','Hidden','$created_by','$created_date','1')";
	$res2=mysql_query($sqlDV);	
	}
	
	if($bank_id !=""){		
	$opSQL="INSERT INTO ".NDB_NAME.".opening_balance (voucher_no,project_id,head_id,head_type,fyear,opening_balance,op_type,opening_month,
	created_by) VALUES('$voucher_no','$project_id','$bank_id','Bank','FY004','$BankBalance','$op_type','01','$created_by')";
	mysql_query($opSQL); 
	}
	return $voucher_no;
   }
   //===== Start Account Year Ending =====
   function accountYearEnding($voucher_no,$account_id,$AccountBalance,$ob_date){	
	$head_type	= getHeadType($account_id);	
	$created_date   = $ob_date;
	$project_id     = getFromSession('project_id');    
	$created_by     = getFromSession('userid');	
			
	if($AccountBalance >=0 && $account_id !="" ){	
	$op_type = "Dr";			 
	$this->saveAccountJournal($voucher_no,$account_id,$head_type,$project_id,"OB",$AccountBalance,0,$AccountBalance,1,$ob_date);
	}elseif($AccountBalance < 0 && $account_id !="" ){
	$op_type = "Cr";						 
	$this->saveAccountJournal($voucher_no,$account_id,$head_type,$project_id,"OB",0,abs($AccountBalance),$AccountBalance,1,$ob_date);
	}
				
	if($account_id !="" && $AccountBalance >=0){ 
	$debit = abs($AccountBalance); $credit = abs($AccountBalance);
	 
	$sqlCV="INSERT INTO ".NDB_NAME.".credit_vouchar (voucher_no,account_head,project_id,head_type,mode_of_payment,vouchar_type,transaction_name,
	credit,description,list_view,created_by,created_date) VALUES('$voucher_no','A000013','$project_id','Closing Balance','Others',
	'Others Vouchar','OB','$credit','OB','Hidden','$created_by','$created_date')";
	$res1= mysql_query($sqlCV);
	
	$sqlDV="INSERT INTO ".NDB_NAME.".cs_delivery_product(voucher_no,account_head,project_id,head_type,mode_of_payment,vouchar_type,
	transaction_name,debit,paid_amount,due,description,list_view,created_by,created_date,status) 
	VALUES('$voucher_no','$account_id','$project_id','$head_type','Others','Others Vouchar','OB','$debit','0','0',
	'OB','Hidden','$created_by','$created_date','1')";
	$res2=mysql_query($sqlDV);	
	}elseif($account_id !="" && $AccountBalance <0){ 
	$debit = abs($AccountBalance); $credit = abs($AccountBalance);
	 
	$sqlCV="INSERT INTO ".NDB_NAME.".credit_vouchar (voucher_no,account_head,project_id,head_type,mode_of_payment,vouchar_type,transaction_name,
	credit,description,list_view,created_by,created_date) VALUES('$voucher_no','$account_id','$project_id','$head_type','Others',
	'Others Vouchar','OB','$credit','OB','Hidden','$created_by','$created_date')";
	$res1= mysql_query($sqlCV);
	
	$sqlDV="INSERT INTO ".NDB_NAME.".cs_delivery_product(voucher_no,account_head,project_id,head_type,mode_of_payment,vouchar_type,
	transaction_name,debit,paid_amount,due,description,list_view,created_by,created_date,status) 
	VALUES('$voucher_no','A000013','$project_id','Closing Balance','Others','Others Vouchar','OB','$debit','0','0',
	'OB','Hidden','$created_by','$created_date','1')";
	$res2=mysql_query($sqlDV);	
	}
	
	if($account_id !=""){		
	$opSQL="INSERT INTO ".NDB_NAME.".opening_balance (voucher_no,project_id,head_id,head_type,fyear,opening_balance,op_type,opening_month,
	created_by) VALUES('$voucher_no','$project_id','$account_id','$head_type','FY004','$AccountBalance','$op_type','01','$created_by')";
	mysql_query($opSQL); 
	}
	return $voucher_no;
   }
   
   
   function deleteOpening($voucher_no){
	 
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
      	$info['where'] = " BINARY $idName='$idValue'";
      	//$info['debug'] = true;
      	$res = delete($info);      	
      	if($res){
      	  return true;    	   
      	}else{
      	  return false;
      	}      	
      }
   }
   
   function getVoucherNo($sub_id,$head_type){
	 $SQL="SELECT voucher_no FROM ".NDB_NAME.".account_journal WHERE created_date='2017-12-31' AND head_type ='$head_type' AND transaction_type='OB' AND BINARY sub_id='$sub_id'"; 
	 $res = mysql_query($SQL); 
	 if(mysql_num_rows($res)>0){
		 $row = mysql_fetch_object($res);
		 $voucher_no = $row->voucher_no;
	 }else{
		 $voucher_no = $this->createVoucharID();
	 }
	 return $voucher_no;
   }
   
   function saveAccountJournal($voucher_no,$sub_id,$head_type,$project_id,$description,$DR=NULL,$CR=NULL,$balance,$status,$created_date){
		$head_type	= getHeadType($sub_id);   $created_by = getFromSession('userid'); $delivery_id=0;
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
