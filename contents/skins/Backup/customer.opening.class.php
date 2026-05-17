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
	/*
	$project_id     = getFromSession('project_id');   
	$sql= "SELECT * FROM ".SUB_ACC_HEAD_TBL." WHERE head_type='Customer' AND project_id = '$project_id'";
	$res = mysql_query($sql);
	while($arow=mysql_fetch_object($res)){
	$Acc_id = $arow->sub_id; $op_amount = $arow->opening_balance;  $op_type = $arow->op_type; $sales_amount = $arow->total_value;
	$paid_amount = $arow->paid_amount; $return_amount = $arow->return_amount; $baddebt_amount = $arow->baddebt_amount;
	$this->saveInSalesTbl($Acc_id,$op_amount,$op_type,$sales_amount,$paid_amount,$return_amount,$baddebt_amount,"2014-02-28");
	}
	*/
	/* 
	//========Save Opening Sales Return ==========
	$project_id     = getFromSession('project_id');   
	$sql= "SELECT * FROM ".SUB_ACC_HEAD_TBL." WHERE head_type='Customer' AND project_id = '$project_id' ORDER BY `sub_head_name` ASC";
	$res = mysql_query($sql);
	$sl=1;
	while($arow=mysql_fetch_object($res)){
	$Acc_id = $arow->sub_id; $op_amount = $arow->opening_balance;  $op_type = $arow->op_type; $sales_amount = $arow->total_value;
	$paid_amount = $arow->paid_amount; $return_amount = $arow->return_amount; $baddebt_amount = $arow->baddebt_amount;
	$this->saveOBSalesReturn($project_id,$Acc_id,$return_amount,$baddebt_amount,"2014-02-28");
	$sl++;	
	} echo $sl;
	//======= End =======
	*/
	/* 
	//======= Start Sales Opening ==========
	$project_id     = getFromSession('project_id');   
	$sql= "SELECT * FROM ".SUB_ACC_HEAD_TBL." WHERE head_type='Customer' AND project_id = '$project_id' AND total_value>0 
	ORDER BY `sub_id` ASC";
	$res = mysql_query($sql);
	$sl=1;
	while($arow=mysql_fetch_object($res)){
	$customer = $arow->sub_id; $sales_amount = $arow->total_value;
	$paid_amount = $arow->paid_amount; $return_amount = $arow->return_amount; $baddebt_amount = $arow->baddebt_amount;
	$this->saveOBSales($project_id,$customer,$sales_amount,$paid_amount,"2014-02-28");
	$sl++;	
	//if($sl==5){ break;}
	} echo $sl;
	*/ 
	require_once(CURRENT_APP_SKIN_FILE);
	return $data[0];
   }
   function saveInSalesTbl($customer,$op_amount,$op_type,$sales_amount,$paid_amount,$return_amount,$baddebt_amount,$sales_date){
	require_once(CLASS_DIR.'/purchase.class.php');	
	$PurApp 		= new Purchase();	
	require_once(CLASS_DIR.'/sales.class.php');	
	$salesApp 			= new Sales();
	if($op_amount==""){ $op_amount=0; $op_type="Dr";} if($sales_amount==""){ $sales_amount=0;} if($paid_amount==""){ $paid_amount=0;} 
	if($return_amount==""){ $return_amount=0;} if($baddebt_amount==""){ $baddebt_amount=0;}
	$ob_date			= "2014-01-01";
	$voucher_no 	= $PurApp->createVoucharID();
	$created_date   = date('Y-m-d h:i:s');
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
	$salesApp->saveAccountJournal($voucher_no,$customer,"Customer",$project_id,"OS",$sales_amount,0,$PartyBalance,1,$sales_date);
	}
	if($paid_amount>0){	// === CR ===	Opening Payment is OP
	$totalPartyCR  = $salesApp->getTotalCreditAmount($customer,getFromSession('project_id'));
	$totalPartyDR  = $salesApp->getTotalDebitAmount($customer,getFromSession('project_id'));					 
	$PartyBalance  = ($totalPartyDR-($totalPartyCR+$paid_amount));						 
	$salesApp->saveAccountJournal($voucher_no,$customer,"Customer",$project_id,"OP",0,$paid_amount,$PartyBalance,1,$sales_date);
	}
	if($return_amount>0){	// === CR ===	Opening Sales Return is OSR
	$totalPartyCR  = $salesApp->getTotalCreditAmount($customer,getFromSession('project_id'));
	$totalPartyDR  = $salesApp->getTotalDebitAmount($customer,getFromSession('project_id'));					 
	$PartyBalance  = ($totalPartyDR-($totalPartyCR+$return_amount));						 
	$salesApp->saveAccountJournal($voucher_no,$customer,"Customer",$project_id,"OSR",0,$return_amount,$PartyBalance,1,$sales_date);
	}		
	if($baddebt_amount>0){	// === CR ===	Opening Sales Baddebt is OSBD
	$totalPartyCR  = $salesApp->getTotalCreditAmount($customer,getFromSession('project_id'));
	$totalPartyDR  = $salesApp->getTotalDebitAmount($customer,getFromSession('project_id'));					 
	$BDBalance     = ($totalPartyDR-($totalPartyCR+$baddebt_amount));						 
	$salesApp->saveAccountJournal($voucher_no,$customer,"Customer",$project_id,"OSBD",0,$baddebt_amount,$BDBalance,1,$sales_date);
	}
	//====== get Party Balance for due ====
	if($op_type=="Dr"){	
	$due = (($op_amount+$sales_amount)-($paid_amount+$return_amount+$baddebt_amount));
	$adjust = $op_amount; 
	}else{
	$due 	= ($sales_amount-($op_amount+$paid_amount+$return_amount+$baddebt_amount));
	$adjust = "-$op_amount";
	}
	
	if(($due>0) && ($sales_amount==0)){ 
	$vouchar_type ='Recievable Vouchar'; $status = 0; $debit = $op_amount; $paidamount =  ($paid_amount+$return_amount+$baddebt_amount);
	$sqlDV="INSERT INTO ".CREDIT_VOUCHAR_TBL."(voucher_no,account_head,project_id,head_type,transaction_type,vouchar_type,transaction_name,credit,list_view,created_by,created_date) 
	VALUES('$voucher_no','A000014','$project_id','OP','Opening Balance','$vouchar_type','Sales Opening','$op_amount','Active','$created_by','$created_date')";
	$res1= mysql_query($sqlDV);
	$sqlCV="INSERT INTO ".DEVIT_VOUCHAR_TBL."(voucher_no,account_head,project_id,head_type,transaction_type,vouchar_type,transaction_name,debit,paid_amount,due,list_view,created_by,
	created_date,status) VALUES('$voucher_no','$customer','$project_id','Customer','Opening Balance','$vouchar_type','Sales Opening','$op_amount','$paidamount','$due','Active','$created_by','$created_date','0')";
	$res2=mysql_query($sqlCV);
	}else{	
	if($due>=0){ $credit = $due; $dr_head = $customer; $cr_head = "A000014"; }else{ $credit = abs($due); $dr_head = "A000014"; $cr_head = $customer;}
	$sqlDV="INSERT INTO ".CREDIT_VOUCHAR_TBL."(voucher_no,account_head,project_id,head_type,transaction_type,vouchar_type,transaction_name,credit,list_view,created_by,created_date) 
	VALUES('$voucher_no','$cr_head','$project_id','OP','Opening Balance','Others Vouchar','Sales Opening','$credit','Hidden','$created_by','$created_date')";
	$res1= mysql_query($sqlDV);
	$sqlCV="INSERT INTO ".DEVIT_VOUCHAR_TBL."(voucher_no,account_head,project_id,head_type,transaction_type,vouchar_type,transaction_name,debit,paid_amount,due,list_view,created_by,
	created_date,status) VALUES('$voucher_no','$dr_head','$project_id','Customer','Opening Balance','Others Vouchar','Sales Opening','$credit','$credit','0','Hidden','$created_by','$created_date','1')";
	$res2=mysql_query($sqlCV);
	}
	
	if($sales_amount>0 && $due>=0){
	$sqlM="INSERT INTO ".SALES_MASTER_TBL."(voucher_no,wo_no,project_id,customer,order_type,sales_date,delivery_date,total_value,mode_of_payment,net_payble,paid_amount,return_amount,
	baddebt_amount,adjust,item_delivery_amount,due,created_by,created_date) VALUES('$voucher_no','$voucher_no','$project_id','$customer','Sales Opening','$sales_date','$sales_date',
	'$sales_amount','Recievable','$sales_amount','$paid_amount','$return_amount','$baddebt_amount','$adjust','$sales_amount','$due','$created_by','$created_date')";
	$res3=mysql_query($sqlM);
	}
	
	if($due<0){
	//===== Customer Payble =====
	$paybleAmount = abs($due);
	$RMSQL="INSERT INTO ".SALES_RETURN_PAYBLE_TBL."(voucher_no,project_id,customer_id,currency,return_amount,paid_amount,due,return_date,created_by) 			
	VALUES('$voucher_no','$project_id','$customer','1','$paybleAmount','0','$paybleAmount','$sales_date','$created_by')";
	mysql_query($RMSQL); 
	}
	$opSQL="INSERT INTO ".OPENING_BALANCE_TBL."(voucher_no,project_id,head_id,head_type,opening_balance,op_type,opening_month,created_by) 			
	VALUES('$voucher_no','$project_id','$customer','Customer','$op_amount','$op_type','01','$created_by')";
	mysql_query($opSQL); 
	return $voucher_no;
   }
   function saveOBSalesReturn($project_id,$customer,$return_amount,$baddebt_amount,$return_date){
		$total_amount = ($return_amount+$baddebt_amount);	
		$voucher_no = $this->createOBReturnID(); 
		if($baddebt_amount>0 || $return_amount>0){   
		$RMSQL="INSERT INTO ".SALES_RETURN_MASTER_TBL."(voucher_no,project_id,customer,total_amount,total_sales_return,total_baddebts,discount_percent,net_payble,
			return_date,created_by,status) VALUES('$voucher_no','$project_id','$customer','$total_amount','$return_amount','$baddebt_amount','0',
			'$total_amount','$return_date','imran','0')";
		mysql_query($RMSQL);
		}
		
		if($baddebt_amount>0){ 
		$RSQL="INSERT INTO ".SALES_RETURN_TBL."(voucher_no,project_id,customer_id,product_status,
		unit_price,return_amount,net_amount,return_date,return_by) VALUES('$voucher_no','$project_id','$customer','Yes',
		'$baddebt_amount','$baddebt_amount','$baddebt_amount','$return_date','imran')";
		mysql_query($RSQL);
		}
		if($return_amount>0){ 
		$RSQL="INSERT INTO ".SALES_RETURN_TBL."(voucher_no,project_id,customer_id,product_status,
		unit_price,return_amount,net_amount,return_date,return_by) VALUES('$voucher_no','$project_id','$customer','No',
		'$return_amount','$return_amount','$return_amount','$return_date','imran')";
		mysql_query($RSQL);
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