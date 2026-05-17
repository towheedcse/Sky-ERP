<?php
class CustomerOpening
{
   function run() {     
		$cmd    = getRequest('cmd');
		$u_t_id = getFromSession('u_type_id');
		if($u_t_id == 101) 
		{      
		  switch($cmd) { 
		  	 case 'add'                	: $screen = $this->showEditor($msg); break;
      	     case 'edit'               	: $screen = $this->showEditor("Edit Page");    break;
      	     case 'loadArea'  			: $this->loadArea(trim(getRequest('district'))); break; 					 
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
	 $sub_id = getRequest('id');	 
	 $data          = array();	
	 $op_amount 	= getRequest('opening_balance');
	 $op_type 		= getRequest('op_type');
	 $sales_amount 	= getRequest('total_value');
	 $paid_amount 	= getRequest('paid_amount');
	 $return_amount = getRequest('return_amount');
	 $baddebt_amount= getRequest('baddebt_amount');
	 $sales_date	= formatDate(getRequest('sales_date'));
	 	
	 if($sub_id){
	 $TBDArr			= $comApp->getRecordInfo(SUB_ACC_HEAD_TBL,"sub_id",$sub_id);      
	 $TBDArr 			= parseThisValue($TBDArr);
	 $data        		= array_merge(array(),$TBDArr);		 			
	 if(getRequest('save')){
		$comApp->updateRecord(SUB_ACC_HEAD_TBL,"sub_id",$sub_id,"","","","","customer.opening","list");
		$msg="Successfully Update Record !!!";
		header("location:?app=customer.opening&cmd=list&msg=$msg");	      	
	 } 
	} else {		
	if(getRequest('save')) {
		mysql_query("START TRANSACTION;");
		$Acc_id = $comApp->NewID(SUB_ACC_HEAD_TBL,"sub_id","A000001","A",7);
		$comApp->saveRecord(SUB_ACC_HEAD_TBL,"sub_id",$Acc_id,"","","created_by","created_date","customer.opening","");
		//$this->saveInSalesTbl($Acc_id,$op_amount,$op_type,$sales_amount,$paid_amount,$return_amount,$baddebt_amount,$sales_date);
		mysql_query("COMMIT;");
		$msg="Successfully Save Record !!!";
		header("location:?app=customer.opening&cmd=list&msg=$msg");     		       		      	
	 }			 
	}
	$f1Value = getRequest('srckey');
	$comdition = "head_type='Customer' ";
	$from =getRequest('from'); if($from==""){ $from=0;} $to =getRequest('to'); if($to==""){ $to=20;}
	$data['customer_list']  = $comApp->getRecords(SUB_ACC_HEAD_TBL,"sub_id DESC",$comdition,"sub_head_name",$f1Value,"","",$from,$to);
	$data['totalrecord']  	= $comApp->getTotalRecords(SUB_ACC_HEAD_TBL,"sub_id",$comdition,"sub_head_name",$f1Value,"",""); 
	$data['district_list']	= $comListApp->getDistrictList();
	$data['area_list'] 		= $comListApp->getAreaList();
	$data['message'] 		= $msg;
	$data['cmd']     		= getRequest('cmd');
	/* ====== Customer Opening 1 Jan to 28 Feb =======
	mysql_query("START TRANSACTION;");
	$project_id     = getFromSession('project_id');   
	//$cusql= "SELECT * FROM vw_customer_details WHERE project_id = '$project_id' ORDER BY `sub_id` ASC";
	$cusql= "SELECT * FROM vw_customer_details WHERE project_id = '$project_id' AND `sub_id` REGEXP ('A001532|A001467') ORDER BY `sub_id` ASC";
	$cures = mysql_query($cusql); $sl=1;
	while($arow=mysql_fetch_object($cures)){
	$customer = $arow->sub_id; $op_amount = $arow->opening_balance;  $op_type = $arow->op_type; $sales_amount = $arow->total_value;
	$paid_amount = $arow->paid_amount; $return_amount = $arow->return_amount; $baddebt_amount = $arow->baddebt_amount;
	$this->saveInSalesTbl($customer,$op_amount,$op_type,$sales_amount,$paid_amount,$return_amount,$baddebt_amount,"2014-02-28");
	$sl++;
	} echo $sl;
	mysql_query("COMMIT;");
	//======= End Opening =======
	*/
	
	
	/* 	
	//========March Opening ==========	
	mysql_query("START TRANSACTION;");
	$project_id     = getFromSession('project_id');   
	//$sql= "SELECT * FROM ".SUB_ACC_HEAD_TBL." WHERE head_type='Customer' AND project_id = '$project_id' ORDER BY `sub_id` ASC";
	$cusql= "SELECT * FROM vw_customer_details WHERE project_id = '$project_id' AND `sub_id` REGEXP ('A001532|A001467') ";
	$cres = mysql_query($cusql);
	$sl=1;
	while($arow=mysql_fetch_object($cres)){
	//======= Start March to May Sales Opening ==========
	$customer = $arow->sub_id; $sales_amount = $arow->march_sales;
	$paid_amount = $arow->march_receipt; $return_amount = $arow->march_return; $baddebt_amount = $arow->march_baddebt;
	$this->saveMarchOpening($project_id,$customer,$sales_amount,$paid_amount,$return_amount,$baddebt_amount,"2014-05-31");
	$sl++;	
	} 
	mysql_query("COMMIT;");
	echo $sl;
	//======= End =======	
	*/ 
	
	/* 	
	//========June Opening ==========	
	mysql_query("START TRANSACTION;");
	$project_id     = getFromSession('project_id');   
	//$sql= "SELECT * FROM ".SUB_ACC_HEAD_TBL." WHERE head_type='Customer' AND project_id = '$project_id' ORDER BY `sub_id` ASC";
	$cusql= "SELECT * FROM vw_customer_details WHERE project_id = '$project_id' AND `sub_id` REGEXP ('A001532')";
	$cres = mysql_query($cusql);
	$sl=1;
	while($arow=mysql_fetch_object($cres)){
	//======= Start June Sales Opening ==========
	$customer = $arow->sub_id; $sales_amount = $arow->june_sales;
	$paid_amount = $arow->june_receipt; $return_amount = $arow->june_return; $baddebt_amount = $arow->june_baddebt;
	$this->saveJuneOpening($project_id,$customer,$sales_amount,$paid_amount,$return_amount,$baddebt_amount,"2014-06-01");
	$sl++;	
	} 
	mysql_query("COMMIT;");
	echo $sl;
	//======= End =======	
	*/ 
	
	require_once(CURRENT_APP_SKIN_FILE);
	return $data[0];
   }
   //===== First Opening =====
   function saveInSalesTbl($customer,$op_amount,$op_type,$sales_amount,$paid_amount,$return_amount,$baddebt_amount,$sales_date){
	require_once(CLASS_DIR.'/purchase.class.php');	
	$PurApp 		= new Purchase();	
	require_once(CLASS_DIR.'/sales.class.php');	
	$salesApp 			= new Sales();
	if($op_amount==""){ $op_amount=0; $op_type="Dr";} if($sales_amount==""){ $sales_amount=0;} if($paid_amount==""){ $paid_amount=0;} 
	if($return_amount==""){ $return_amount=0;} if($baddebt_amount==""){ $baddebt_amount=0;}
	$ob_date		= "2014-01-01";
	$voucher_no 	= $PurApp->createVoucharID();
	$created_date   = $sales_date;
	$project_id     = getFromSession('project_id');    
	$created_by     = getFromSession('userid');
	
	if($op_type=="Dr"){			
	$totalPartyCR  = $salesApp->getTotalCreditAmount($customer,getFromSession('project_id'));
	$totalPartyDR  = $salesApp->getTotalDebitAmount($customer,getFromSession('project_id'));					 
	$PartyBalance  = (($totalPartyDR+$op_amount)-$totalPartyCR);						 
	$salesApp->saveAccountJournal($voucher_no,$customer,"Customer",$project_id,"OB",$op_amount,0,$PartyBalance,1,$ob_date);
	}elseif($op_type=="Cr"){		
	$CrReturn = $op_amount;		
	$totalPartyCR  = $salesApp->getTotalCreditAmount($customer,getFromSession('project_id'));
	$totalPartyDR  = $salesApp->getTotalDebitAmount($customer,getFromSession('project_id'));					 
	$PartyBalance  = ($totalPartyDR-($totalPartyCR+$op_amount));						 
	$salesApp->saveAccountJournal($voucher_no,$customer,"Customer",$project_id,"OB",0,$op_amount,$PartyBalance,1,$ob_date);
	}
	if($sales_amount>0){ // === Dr ====	Opening Sales is OS		
	$totalPartyCR  = $salesApp->getTotalCreditAmount($customer,getFromSession('project_id'));
	$totalPartyDR  = $salesApp->getTotalDebitAmount($customer,getFromSession('project_id'));					 
	$PartyBalance  = (($totalPartyDR+$sales_amount)-$totalPartyCR);						 
	$salesApp->saveAccountJournal($voucher_no,$customer,"Customer",$project_id,"Opening Sales",$sales_amount,0,$PartyBalance,1,$sales_date);
	}
	if($paid_amount>0){	// === CR ===	Opening Receipt is OR
	$totalPartyCR  = $salesApp->getTotalCreditAmount($customer,getFromSession('project_id'));
	$totalPartyDR  = $salesApp->getTotalDebitAmount($customer,getFromSession('project_id'));					 
	$PartyBalance  = ($totalPartyDR-($totalPartyCR+$paid_amount));						 
	$salesApp->saveAccountJournal($voucher_no,$customer,"Customer",$project_id,"Opening Receipt",0,$paid_amount,$PartyBalance,1,$sales_date);
	}
	if($return_amount>0){	// === CR ===	Opening Sales Return is OSR
	$totalPartyCR  = $salesApp->getTotalCreditAmount($customer,getFromSession('project_id'));
	$totalPartyDR  = $salesApp->getTotalDebitAmount($customer,getFromSession('project_id'));					 
	$PartyBalance  = ($totalPartyDR-($totalPartyCR+$return_amount));						 
	$salesApp->saveAccountJournal($voucher_no,$customer,"Customer",$project_id,"Opening Sales Return",0,$return_amount,$PartyBalance,1,$sales_date);
	}		
	if($baddebt_amount>0){	// === CR ===	Opening Sales Baddebt is OSBD
	$totalPartyCR  = $salesApp->getTotalCreditAmount($customer,getFromSession('project_id'));
	$totalPartyDR  = $salesApp->getTotalDebitAmount($customer,getFromSession('project_id'));					 
	$BDBalance     = ($totalPartyDR-($totalPartyCR+$baddebt_amount));						 
	$salesApp->saveAccountJournal($voucher_no,$customer,"Customer",$project_id,"Opening Sales Baddebt",0,$baddebt_amount,$BDBalance,1,$sales_date);
	}
	//====== get Party Balance for due ====
	if($op_type=="Dr"){	
	$due = (($op_amount+$sales_amount)-($paid_amount+$return_amount+$baddebt_amount));
	}else{
	$due 	= ($sales_amount-($op_amount+$paid_amount+$return_amount+$baddebt_amount));
	}
	
	if($due>0){ 
	$vouchar_type ='Recievable Vouchar'; $status = 0; $debit = $due;
	$sqlDV="INSERT INTO ".CREDIT_VOUCHAR_TBL."(voucher_no,account_head,project_id,head_type,transaction_type,vouchar_type,transaction_name,credit,description,list_view,created_by,created_date) 
	VALUES('$voucher_no','A000014','$project_id','OP','Opening Balance','$vouchar_type','Opening Recievable','$due','Recievable amount of Jan-1 to Feb 28','Active','$created_by','$created_date')";
	$res1= mysql_query($sqlDV);
	$sqlCV="INSERT INTO ".DEVIT_VOUCHAR_TBL."(voucher_no,account_head,project_id,head_type,transaction_type,vouchar_type,transaction_name,debit,paid_amount,due,description,list_view,created_by,
	created_date,status) VALUES('$voucher_no','$customer','$project_id','Customer','Opening Balance','$vouchar_type','Opening Recievable','$debit','0','$due','Recievable amount of Jan-1 to Feb 28','Active','$created_by','$created_date','0')";
	$res2=mysql_query($sqlCV);
	}elseif($due<0){ 
	$vouchar_type ='Payable Vouchar'; $status = 0; $debit = abs($due); $credit = abs($due);
	$sqlCV="INSERT INTO ".CREDIT_VOUCHAR_TBL."(voucher_no,account_head,head_type,project_id,mode_of_payment,vouchar_type,transaction_name,credit,description,list_view,created_by,created_date) 
	VALUES('$voucher_no','$customer','Customer','$project_id','Others','$vouchar_type','Opening Payble','$credit','Payble amount of Jan-1 to Feb 28','Active','$created_by','$created_date')";
	$res1= mysql_query($sqlCV);
	$sqlDV="INSERT INTO ".DEVIT_VOUCHAR_TBL."(voucher_no,account_head,project_id,head_type,mode_of_payment,vouchar_type,transaction_name,debit,paid_amount,due,description,list_view,created_by,
	created_date,status) VALUES('$voucher_no','A000028','$project_id','Accounts Payable','Others','$vouchar_type','Opening Payble','$debit','0','$debit','Payble amount of Jan-1 to Feb 28','Active','$created_by','$created_date','0')";
	$res2=mysql_query($sqlDV);
	}else{	
	if($op_amount>=0){ $credit = $op_amount; $dr_head = $customer; $cr_head = "A000014"; }else{ $credit = abs($op_amount); $dr_head = "A000014"; $cr_head = $customer;}
	$sqlCV="INSERT INTO ".CREDIT_VOUCHAR_TBL."(voucher_no,account_head,project_id,head_type,transaction_type,vouchar_type,transaction_name,credit,description,list_view,created_by,created_date) 
	VALUES('$voucher_no','$cr_head','$project_id','OB','Opening Balance','Others Vouchar','Opening Balance','$credit','Opening Balance','Hidden','$created_by','$created_date')";
	$res1= mysql_query($sqlCV);
	$sqlDV="INSERT INTO ".DEVIT_VOUCHAR_TBL."(voucher_no,account_head,project_id,head_type,transaction_type,vouchar_type,transaction_name,debit,paid_amount,due,description,list_view,created_by,
	created_date,status) VALUES('$voucher_no','$dr_head','$project_id','Customer','Opening Balance','Others Vouchar','Opening Balance','$credit','$credit','0','Opening Balance','Hidden','$created_by','$created_date','1')";
	$res2=mysql_query($sqlDV);
	}		
	$opSQL="INSERT INTO ".OPENING_BALANCE_TBL."(voucher_no,project_id,head_id,head_type,opening_balance,op_type,opening_month,created_by) 			
	VALUES('$voucher_no','$project_id','$customer','Customer','$op_amount','$op_type','01','$created_by')";
	mysql_query($opSQL); 
	//====== Opening Sales =====
	$this->saveOBMarchSales("",$project_id,$customer,$sales_amount,$paid_amount,$sales_date);
	//========Save March Opening Sales Return ==========	
	$this->saveOBSalesReturn($project_id,$customer,$return_amount,$baddebt_amount,$sales_date);
	
	return $voucher_no;
   }
   //===== 2nd Opening =====
   function saveMarchOpening($project_id,$customer,$sales_amount,$paid_amount,$return_amount,$baddebt_amount,$sales_date){
   		require_once(CLASS_DIR.'/sales.class.php');	
		$salesApp 			= new Sales();
		require_once(CLASS_DIR.'/purchase.class.php');	
		$PurApp 		= new Purchase();
		
		if($sales_amount==""){ $sales_amount=0;} if($paid_amount==""){ $paid_amount=0;} 
		if($return_amount==""){ $return_amount=0;} if($baddebt_amount==""){ $baddebt_amount=0;}
		$created_date   = $sales_date;
	    $project_id     = getFromSession('project_id');    
	    $created_by     = getFromSession('userid');
		$voucher_no 	= $PurApp->createVoucharID();	
	   //======== Start Sales Amount ===========
	   if($sales_amount>0){ // === Dr ====	Opening Sales is OS, A002286 = Sates Item	    
		$totalPartyCR  = $salesApp->getTotalCreditAmount($customer,getFromSession('project_id'));
		$totalPartyDR  = $salesApp->getTotalDebitAmount($customer,getFromSession('project_id'));
		$PartyPrvBalance  = ($totalPartyDR-$totalPartyCR);						 
		$PartyBalance  = (($totalPartyDR+$sales_amount)-$totalPartyCR);	
		if($PartyPrvBalance<0){
		  $rest_of_amount = $this->adjustCustomerPayble($voucher_no,$customer,$sales_amount);
		}		 					 
		$salesApp->saveAccountJournal($voucher_no,$customer,"Customer",$project_id,"Opening Sales Delivery 01 March to 31 May 2014",$sales_amount,0,$PartyBalance,1,$sales_date);
		if($rest_of_amount>0){
		$vouchar_type ='Recievable Vouchar'; $status = 0; $debit = $rest_of_amount; $description = "Opening Sales Delivery of 01 March to 31 May 2014";
		$sqlDV="INSERT INTO ".CREDIT_VOUCHAR_TBL."(voucher_no,account_head,project_id,head_type,transaction_type,vouchar_type,transaction_name,credit,description,list_view,created_by,created_date) 
		VALUES('$voucher_no','A002286','$project_id','Sales','Opening Balance','$vouchar_type','Opening Recievable','$rest_of_amount','$description','Active','$created_by','$created_date')";
		$res1= mysql_query($sqlDV);
		$sqlCV="INSERT INTO ".DEVIT_VOUCHAR_TBL."(voucher_no,account_head,project_id,head_type,transaction_type,vouchar_type,transaction_name,debit,paid_amount,due,description,list_view,created_by,
		created_date,status) VALUES('$voucher_no','$customer','$project_id','Customer','Opening Balance','$vouchar_type','Opening Recievable','$debit','0','$debit','$description','Active','$created_by','$created_date','0')";
		$res2=mysql_query($sqlCV);
		}
		}
		//====== end sales amount========
		
		if($paid_amount>0){	// === CR ===	Opening Receipt is OR, A002287 = Opening Sales Receipt
		$totalPartyCR  = $salesApp->getTotalCreditAmount($customer,getFromSession('project_id'));
		$totalPartyDR  = $salesApp->getTotalDebitAmount($customer,getFromSession('project_id'));					 
		$PartyBalance  = ($totalPartyDR-($totalPartyCR+$paid_amount));	
		$voucher_no 	= $PurApp->createVoucharID();					 
		$salesApp->saveAccountJournal($voucher_no,$customer,"Customer",$project_id,"Opening Receipt Amount of 01 March to 31 May 2014",0,$paid_amount,$PartyBalance,1,$sales_date);	
		$vouchar_type ='Received Vouchar'; $debit = $paid_amount; $description = "Receipt Amount ($paid_amount Tk) of 01 March to 31 May 2014";
		$sqlDV="INSERT INTO ".CREDIT_VOUCHAR_TBL."(voucher_no,account_head,project_id,head_type,transaction_type,vouchar_type,transaction_name,credit,description,list_view,created_by,created_date) 
		VALUES('$voucher_no','$customer','$project_id','Customer','Opening Balance','$vouchar_type','Opening Receipt','$paid_amount','$description','Active','$created_by','$created_date')";
		$res1= mysql_query($sqlDV);
		$sqlCV="INSERT INTO ".DEVIT_VOUCHAR_TBL."(voucher_no,account_head,project_id,head_type,transaction_type,vouchar_type,transaction_name,debit,paid_amount,due,description,list_view,created_by,
		created_date,status) VALUES('$voucher_no','A002287','$project_id','Opening Balance','Opening Balance','$vouchar_type','Opening Receipt','$debit','$debit','0','$description','Active','$created_by','$created_date','1')";
		$res2=mysql_query($sqlCV);	
		}
		
		if($return_amount>0){	// === CR ===	Opening Sales Return is OSR, A002288 = Opening Sales Return
		$voucher_no 	= $PurApp->createVoucharID();
		$totalPartyCR  = $salesApp->getTotalCreditAmount($customer,getFromSession('project_id'));
		$totalPartyDR  = $salesApp->getTotalDebitAmount($customer,getFromSession('project_id'));					 
		$PartyBalance  = ($totalPartyDR-($totalPartyCR+$return_amount));
		$voucher_no 	= $PurApp->createVoucharID();						 
		$salesApp->saveAccountJournal($voucher_no,$customer,"Customer",$project_id,"Opening Sales Return of 01 March to 31 May 2014",0,$return_amount,$PartyBalance,1,$sales_date);
		$vouchar_type ='Received Vouchar'; $debit = $return_amount; $description = "Opening Sales Return of 01 March to 31 May 2014";
		$sqlDV="INSERT INTO ".CREDIT_VOUCHAR_TBL."(voucher_no,account_head,project_id,head_type,transaction_type,vouchar_type,transaction_name,credit,description,list_view,created_by,created_date) 
		VALUES('$voucher_no','$customer','$project_id','Customer','Opening Balance','$vouchar_type','Opening Receipt','$return_amount','$description','Active','$created_by','$created_date')";
		$res1= mysql_query($sqlDV);
		$sqlCV="INSERT INTO ".DEVIT_VOUCHAR_TBL."(voucher_no,account_head,project_id,head_type,transaction_type,vouchar_type,transaction_name,debit,paid_amount,due,description,list_view,created_by,
		created_date,status) VALUES('$voucher_no','A002288','$project_id','Opening Balance','Opening Balance','$vouchar_type','Opening Receipt','$debit','$debit','0','$description','Active','$created_by','$created_date','1')";
		$res2=mysql_query($sqlCV);
		
		}		
		if($baddebt_amount>0){	// === CR ===	Opening Sales Baddebt is OSBD
		$voucher_no 	= $PurApp->createVoucharID();
		$totalPartyCR  = $salesApp->getTotalCreditAmount($customer,getFromSession('project_id'));
		$totalPartyDR  = $salesApp->getTotalDebitAmount($customer,getFromSession('project_id'));					 
		$BDBalance     = ($totalPartyDR-($totalPartyCR+$baddebt_amount));
		$voucher_no 	= $PurApp->createVoucharID();						 
		$salesApp->saveAccountJournal($voucher_no,$customer,"Customer",$project_id,"Opening Sales Baddebt of 01 March to 31 May 2014",0,$baddebt_amount,$BDBalance,1,$sales_date);
		$vouchar_type ='Received Vouchar'; $debit = $baddebt_amount; $description = "Opening Sales Baddebt of 01 March to 31 May 2014";
		$sqlDV="INSERT INTO ".CREDIT_VOUCHAR_TBL."(voucher_no,account_head,project_id,head_type,transaction_type,vouchar_type,transaction_name,credit,description,list_view,created_by,created_date) 
		VALUES('$voucher_no','$customer','$project_id','Customer','Opening Balance','$vouchar_type','Opening Receipt','$baddebt_amount','$description','Active','$created_by','$created_date')";
		$res1= mysql_query($sqlDV);
		$sqlCV="INSERT INTO ".DEVIT_VOUCHAR_TBL."(voucher_no,account_head,project_id,head_type,transaction_type,vouchar_type,transaction_name,debit,paid_amount,due,description,list_view,created_by,
		created_date,status) VALUES('$voucher_no','A002288','$project_id','Opening Balance','Opening Balance','$vouchar_type','Opening Receipt','$debit','$debit','0','$description','Active','$created_by','$created_date','1')";
		$res2=mysql_query($sqlCV);
		}
		$totalReceipt = ($paid_amount+$return_amount+$baddebt_amount);
		if($totalReceipt>0){ 
		require_once(CLASS_DIR.'/general_vouchar.class.php');	
		$gvApp 		= new GeneralVouchar();
		$gvApp->adjustCustomerReceibavle($customer,$voucher_no,$totalReceipt,$sales_date);
		}
		
		//====== Opening Sales =====
		$this->saveOBMarchSales("",$project_id,$customer,$sales_amount,$paid_amount,$sales_date);
		//========Save March Opening Sales Return ==========	
		$this->saveOBSalesReturn($project_id,$customer,$return_amount,$baddebt_amount,$sales_date);
	
   }
   //===== 3rd Opening =====
   function saveJuneOpening($project_id,$customer,$sales_amount,$paid_amount,$return_amount,$baddebt_amount,$sales_date){
   		require_once(CLASS_DIR.'/sales.class.php');	
		$salesApp 			= new Sales();
		require_once(CLASS_DIR.'/purchase.class.php');	
		$PurApp 		= new Purchase();
		
		if($sales_amount==""){ $sales_amount=0;} if($paid_amount==""){ $paid_amount=0;} 
		if($return_amount==""){ $return_amount=0;} if($baddebt_amount==""){ $baddebt_amount=0;}
		$created_date   = $sales_date;
	    $project_id     = getFromSession('project_id');    
	    $created_by     = getFromSession('userid');
	   //======== Start Sales Amount ===========
	   if($sales_amount>0){ // === Dr ====	Opening Sales is OS, A002286 = Sates Item	
	    $voucher_no 	= $PurApp->createVoucharID();	
		$totalPartyCR  = $salesApp->getTotalCreditAmount($customer,getFromSession('project_id'));
		$totalPartyDR  = $salesApp->getTotalDebitAmount($customer,getFromSession('project_id'));
		$PartyPrvBalance  = ($totalPartyDR-$totalPartyCR);						 
		$PartyBalance  = (($totalPartyDR+$sales_amount)-$totalPartyCR);	
		if($PartyPrvBalance<0){
		  $rest_of_amount = $this->adjustCustomerPayble($voucher_no,$customer,$sales_amount);
		}		 					 
		$salesApp->saveAccountJournal($voucher_no,$customer,"Customer",$project_id,"Opening Sales Delivery 01 June 2014",$sales_amount,0,$PartyBalance,1,$sales_date);
		if($rest_of_amount>0){
		$vouchar_type ='Recievable Vouchar'; $status = 0; $debit = $rest_of_amount; $description = "Opening Sales Delivery of 01 June 2014";
		$sqlDV="INSERT INTO ".CREDIT_VOUCHAR_TBL."(voucher_no,account_head,project_id,head_type,transaction_type,vouchar_type,transaction_name,credit,description,list_view,created_by,created_date) 
		VALUES('$voucher_no','A002286','$project_id','Sales','Opening Balance','$vouchar_type','Opening Recievable','$rest_of_amount','$description','Active','$created_by','$created_date')";
		$res1= mysql_query($sqlDV);
		$sqlCV="INSERT INTO ".DEVIT_VOUCHAR_TBL."(voucher_no,account_head,project_id,head_type,transaction_type,vouchar_type,transaction_name,debit,paid_amount,due,description,list_view,created_by,
		created_date,status) VALUES('$voucher_no','$customer','$project_id','Customer','Opening Balance','$vouchar_type','Opening Recievable','$debit','0','$debit','$description','Active','$created_by','$created_date','0')";
		$res2=mysql_query($sqlCV);
		}
		}
		//====== end sales amount========
		
		if($paid_amount>0){	// === CR ===	Opening Receipt is OR, A002287 = Opening Sales Receipt
		$totalPartyCR  = $salesApp->getTotalCreditAmount($customer,getFromSession('project_id'));
		$totalPartyDR  = $salesApp->getTotalDebitAmount($customer,getFromSession('project_id'));					 
		$PartyBalance  = ($totalPartyDR-($totalPartyCR+$paid_amount));	
		$voucher_no 	= $PurApp->createVoucharID();					 
		$salesApp->saveAccountJournal($voucher_no,$customer,"Customer",$project_id,"Opening Receipt Amount of 01 June 2014",0,$paid_amount,$PartyBalance,1,$sales_date);	
		$vouchar_type ='Received Vouchar'; $debit = $paid_amount; $description = "Receipt Amount ($paid_amount Tk) of 01 June 2014";
		$sqlDV="INSERT INTO ".CREDIT_VOUCHAR_TBL."(voucher_no,account_head,project_id,head_type,transaction_type,vouchar_type,transaction_name,credit,description,list_view,created_by,created_date) 
		VALUES('$voucher_no','$customer','$project_id','Customer','Opening Balance','$vouchar_type','Opening Receipt','$paid_amount','$description','Active','$created_by','$created_date')";
		$res1= mysql_query($sqlDV);
		$sqlCV="INSERT INTO ".DEVIT_VOUCHAR_TBL."(voucher_no,account_head,project_id,head_type,transaction_type,vouchar_type,transaction_name,debit,paid_amount,due,description,list_view,created_by,
		created_date,status) VALUES('$voucher_no','A002287','$project_id','Opening Balance','Opening Balance','$vouchar_type','Opening Receipt','$debit','$debit','0','$description','Active','$created_by','$created_date','1')";
		$res2=mysql_query($sqlCV);	
		}
		
		if($return_amount>0){	// === CR ===	Opening Sales Return is OSR, A002288 = Opening Sales Return
		$voucher_no 	= $PurApp->createVoucharID();
		$totalPartyCR  = $salesApp->getTotalCreditAmount($customer,getFromSession('project_id'));
		$totalPartyDR  = $salesApp->getTotalDebitAmount($customer,getFromSession('project_id'));					 
		$PartyBalance  = ($totalPartyDR-($totalPartyCR+$return_amount));
		$voucher_no 	= $PurApp->createVoucharID();						 
		$salesApp->saveAccountJournal($voucher_no,$customer,"Customer",$project_id,"Opening Sales Return of 01 June 2014",0,$return_amount,$PartyBalance,1,$sales_date);
		$vouchar_type ='Received Vouchar'; $debit = $return_amount; $description = "Opening Sales Return of 01 June 2014";
		$sqlDV="INSERT INTO ".CREDIT_VOUCHAR_TBL."(voucher_no,account_head,project_id,head_type,transaction_type,vouchar_type,transaction_name,credit,description,list_view,created_by,created_date) 
		VALUES('$voucher_no','$customer','$project_id','Customer','Opening Balance','$vouchar_type','Opening Receipt','$return_amount','$description','Active','$created_by','$created_date')";
		$res1= mysql_query($sqlDV);
		$sqlCV="INSERT INTO ".DEVIT_VOUCHAR_TBL."(voucher_no,account_head,project_id,head_type,transaction_type,vouchar_type,transaction_name,debit,paid_amount,due,description,list_view,created_by,
		created_date,status) VALUES('$voucher_no','A002288','$project_id','Opening Balance','Opening Balance','$vouchar_type','Opening Receipt','$debit','$debit','0','$description','Active','$created_by','$created_date','1')";
		$res2=mysql_query($sqlCV);
		
		}		
		if($baddebt_amount>0){	// === CR ===	Opening Sales Baddebt is OSBD
		$voucher_no 	= $PurApp->createVoucharID();
		$totalPartyCR  = $salesApp->getTotalCreditAmount($customer,getFromSession('project_id'));
		$totalPartyDR  = $salesApp->getTotalDebitAmount($customer,getFromSession('project_id'));					 
		$BDBalance     = ($totalPartyDR-($totalPartyCR+$baddebt_amount));
		$voucher_no 	= $PurApp->createVoucharID();						 
		$salesApp->saveAccountJournal($voucher_no,$customer,"Customer",$project_id,"Opening Sales Baddebt of 01 June 2014",0,$baddebt_amount,$BDBalance,1,$sales_date);
		$vouchar_type ='Received Vouchar'; $debit = $baddebt_amount; $description = "Opening Sales Baddebt of 01 June 2014";
		$sqlDV="INSERT INTO ".CREDIT_VOUCHAR_TBL."(voucher_no,account_head,project_id,head_type,transaction_type,vouchar_type,transaction_name,credit,description,list_view,created_by,created_date) 
		VALUES('$voucher_no','$customer','$project_id','Customer','Opening Balance','$vouchar_type','Opening Receipt','$baddebt_amount','$description','Active','$created_by','$created_date')";
		$res1= mysql_query($sqlDV);
		$sqlCV="INSERT INTO ".DEVIT_VOUCHAR_TBL."(voucher_no,account_head,project_id,head_type,transaction_type,vouchar_type,transaction_name,debit,paid_amount,due,description,list_view,created_by,
		created_date,status) VALUES('$voucher_no','A002288','$project_id','Opening Balance','Opening Balance','$vouchar_type','Opening Receipt','$debit','$debit','0','$description','Active','$created_by','$created_date','1')";
		$res2=mysql_query($sqlCV);
		}
		$totalReceipt = ($paid_amount+$return_amount+$baddebt_amount);
		if($totalReceipt>0){ 
		require_once(CLASS_DIR.'/general_vouchar.class.php');	
		$gvApp 		= new GeneralVouchar();
		$gvApp->adjustCustomerReceibavle($customer,$voucher_no,$totalReceipt,$sales_date);
		}
		
		//====== Opening Sales =====
		$this->saveOBMarchSales("",$project_id,$customer,$sales_amount,$paid_amount,$sales_date);
		//========Save March Opening Sales Return ==========	
		$this->saveOBSalesReturn($project_id,$customer,$return_amount,$baddebt_amount,$sales_date);
   }
   
   function saveOBSalesReturn($project_id,$customer,$return_amount,$baddebt_amount,$return_date){
		$total_amount = ($return_amount+$baddebt_amount);	
		$voucher_no = $this->createOBReturnID(); 
		if($baddebt_amount>0 || $return_amount>0){   
		$RMSQL="INSERT INTO ".SALES_RETURN_MASTER_TBL."(voucher_no,project_id,customer,total_amount,total_sales_return,total_baddebts,discount_percent,net_payble,
			return_date,created_by,status) VALUES('$voucher_no','$project_id','$customer','$total_amount','$return_amount','$baddebt_amount','0',
			'$total_amount','$return_date','system','0')";
		mysql_query($RMSQL);
		}
		
		if($baddebt_amount>0){ 
		$RSQL="INSERT INTO ".SALES_RETURN_TBL."(voucher_no,project_id,customer_id,product_status,
		unit_price,return_amount,net_amount,return_date,return_by) VALUES('$voucher_no','$project_id','$customer','Yes',
		'$baddebt_amount','$baddebt_amount','$baddebt_amount','$return_date','system')";
		mysql_query($RSQL);
		}
		if($return_amount>0){ 
		$RSQL="INSERT INTO ".SALES_RETURN_TBL."(voucher_no,project_id,customer_id,product_status,
		unit_price,return_amount,net_amount,return_date,return_by) VALUES('$voucher_no','$project_id','$customer','No',
		'$return_amount','$return_amount','$return_amount','$return_date','system')";
		mysql_query($RSQL);
		}		
   }
   function saveOBMarchSales($voucher_no=NULL,$project_id,$customer,$sales_amount,$paid_amount,$sales_date){
	if($voucher_no==""){
	$voucher_no = $this->createOBSalesID();	
	}
	if($sales_amount>0){   
	$sqlM="INSERT INTO ".SALES_MASTER_TBL."(voucher_no,wo_no,project_id,customer,order_type,sales_date,delivery_date,total_value,mode_of_payment,net_payble,paid_amount,item_delivery_amount,due,created_by,created_date,status) VALUES('$voucher_no','$voucher_no','$project_id','$customer','Sales Opening','$sales_date','$sales_date',
'$sales_amount','Opening','$sales_amount','$sales_amount','$sales_amount','0','system','$sales_date',0)";
	mysql_query($sqlM);
	}
	
	if($voucher_no!="" && $sales_amount>0){
	$sqlSM="INSERT INTO ".SALES_DELIVERY_MASTER_TBL."(voucher_no,project_id,customer,challan_no,delivery_point,delivery_date,
	total_value,created_by,created_date) VALUES('$voucher_no','$project_id','$customer','0','D0010','$sales_date',
	'$sales_amount','system','$sales_date')";
	mysql_query($sqlSM);
	}
   }
   function adjustCustomerPayble($NewVoucherNo,$account_head,$CrAmount){
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
		 $clistApp->saveInvoiceAdjustHistory($NewVoucherNo,"0",$project_id,DEVIT_VOUCHAR_TBL,$voucher_no,$adjustAmount,"-");
		}
	 }elseif(($CrAmount < $srow->due) && ($srow->due >0 && $CrAmount >0)){
		$presentDue = ($srow->due - $CrAmount);
		$PaidAmount = ($srow->paid_amount + $CrAmount);
		if($PaidAmount < $srow->debit){
		 $adjustAmount = $CrAmount; $CrAmount=0;
		 $pusql2="UPDATE ".DEVIT_VOUCHAR_TBL." SET paid_amount='".$PaidAmount."',due='$presentDue',`status`=0 WHERE voucher_no='$voucher_no'";
		 mysql_query($pusql2);
		 $clistApp->saveInvoiceAdjustHistory($NewVoucherNo,"0",$project_id,DEVIT_VOUCHAR_TBL,$voucher_no,$adjustAmount,"-");
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
			$clistApp->saveInvoiceAdjustHistory($NewVoucherNo,"0",$project_id,SALES_RETURN_PAYBLE_TBL,$return_id,$existing_due,"-");
			} 
		}elseif(($CrAmount<$existing_due)){					
			if($existing_due>0 && $CrAmount>0){
			$totalpaid 	 = ($paid_amount + $CrAmount); 
			$present_due = ($existing_due - $CrAmount);
			$adjustAmount = $CrAmount; $CrAmount = 0;
			$SRPUpdate="UPDATE ".SALES_RETURN_PAYBLE_TBL." SET paid_amount=$totalpaid,due=$present_due WHERE return_id='$return_id' AND project_id='$project_id'";
			mysql_query($SRPUpdate);
			$clistApp->saveInvoiceAdjustHistory($NewVoucherNo,"0",$project_id,SALES_RETURN_PAYBLE_TBL,$return_id,$adjustAmount,"-");
			}
			break;
		}
	} // end while
	} // end $CrAmount>0
	//====== Make Customer Receibavle if Delivery Amount is greater then his Payble ======
	if($CrAmount>0){	
	return $CrAmount; 
	}else{
	return 0; 
	}
   }
   
   function createOBReturnID()
   {
      $info = array();
      $info['table']  = SALES_RETURN_MASTER_TBL; 
      $info['fields'] = array('max(voucher_no) as maxvoucher');
	  $info['where']   = "status=0";
      $res = select($info);
      $maxvoucherId = 'OR00000000';
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
      
      $maxvoucherId = generateID("OR",$maxvoucherId,10);
      return $maxvoucherId;
   }
   
   function saveOBSales($project_id,$customer,$sales_amount,$paid_amount,$sales_date){
	
	$gsql = "SELECT * FROM ".SALES_MASTER_TBL." WHERE customer='".$customer."' AND order_type='Sales Opening'";
	$gres = mysql_query($gsql);
	if(mysql_num_rows($gres)==0){
		$voucher_no = $this->createOBSalesID();	
		if($sales_amount>0){   
		$sqlM="INSERT INTO ".SALES_MASTER_TBL."(voucher_no,wo_no,project_id,customer,order_type,sales_date,delivery_date,total_value,mode_of_payment,net_payble,paid_amount,item_delivery_amount,due,created_by,created_date,status) VALUES('$voucher_no','$voucher_no','$project_id','$customer','Sales Opening','$sales_date','$sales_date',
	'$sales_amount','Opening','$sales_amount','$sales_amount','$sales_amount','0','system','$sales_date',0)";
		mysql_query($sqlM);
		}
	}else{
		$grow = mysql_fetch_object($gres);
		$voucher_no = $grow->voucher_no;
	}
	
	if($voucher_no!="" && $sales_amount>0){
	$sqlSM="INSERT INTO ".SALES_DELIVERY_MASTER_TBL."(voucher_no,project_id,customer,challan_no,delivery_point,delivery_date,
	total_value,created_by,created_date) VALUES('$voucher_no','$project_id','$customer','0','D0010','$sales_date',
	'$sales_amount','system','$sales_date')";
	mysql_query($sqlSM);
	}
   }
   
   function createOBSalesID()
   {
      $info = array();
      $info['table']  = SALES_MASTER_TBL; 
      $info['fields'] = array('max(voucher_no) as maxvoucher');
	  $info['where']   = "status=0";
      $res = select($info);
      $maxvoucherId = 'OS000000';
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
      
      $maxvoucherId = generateID("OS",$maxvoucherId,8);
      return $maxvoucherId;
   } 
   function loadArea($district)
   {	  
	  $project_id 	   = getFromSession('project_id');  
	  $info            = array();
	  $info['table']   = AREA_TBL;
	  $info['fields']  =  array('area_id','area_name');
	  $SQL = "district='$district' AND project_id='$project_id' ";
	  $info['where']   = $SQL; 
	  $info['groupby'] = array("area_id");
	  $info['orderby'] = array("district,area_name ASC");
	  //$info['debug']   = true;	
	  $result          = select($info);
	  $data            = array();	
	  if(count($result)){
		 foreach($result as $key=>$value){
			$data[$key][]        = $value;
		 }
	  }				
	  foreach($data as $i=>$v){
		 $subject_idname .= $v[0]->area_id.'#####'.$v[0]->area_name.'@@@';
	  }
	  echo $subject_idname;	
	}   
   function deleteItem(){
	require_once(CLASS_DIR.'/common.class.php');	
	$comApp = new Common(); 
	$sub_id = getRequest('id');
	$comApp->deleteRecord(SUB_ACC_HEAD_TBL,"sub_id",$sub_id,"customer.opening","list"); 
   }  
} // End class
?>