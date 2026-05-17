<?php
class SalesDeliveryMissing{
   function run() { 
	 	     
      $cmd = getRequest('cmd');
	  $u_t_id = getFromSession('u_type_id');
      if( ($u_t_id == 101) || ($u_t_id == 102) || ($u_t_id == 103))
      {
      	switch ($cmd){
      	   case 'missing_qty'           : $screen = $this->showEditor("Edit Page");  break;    
      	   case 'list'               	: $screen = $this->showDeliveryChallanList($msg);   break;
      	   default                      : $cmd = 'list'; $screen = $this->showDeliveryChallanList($msg);   break;
      	}
      }else {
      	header("location:index.php?app=user_home&msg=You are not authorised !!!");
      }     	          
      
      return true;
   }        
   function showDeliveryChallanList(){
   	  require_once(CLASS_DIR.'/common.list.class.php');	
	  $comListApp = new CommonList();  
	  require_once(CLASS_DIR.'/sales.report.class.php');	
	  $srApp = new SalesReport();     
	  $data                		= array();
	  $data['cmd']         		= getRequest('cmd');
	  $data['sdcrecord_list'] 	= $srApp->getSalesDeliveryChallanList(getRequest('from'),getRequest('to'));
	  $data['totalrecord']		= $srApp->getTotalSalesDeliveryChallanList(getRequest('from'),getRequest('to'));	  
	  $data['customer_list'] 	= $comListApp->getCustomerList();	  
	  require_once(SALES_DELIVERY_CHALLAN_LIST4MISSING_QTY); 
	  return $data[0];
   }    
   function showEditor($msg = null) { 
      require_once(CLASS_DIR.'/sales.class.php');	
	  $salesApp 			= new Sales();
	  require_once(CLASS_DIR.'/common.list.class.php');	
	  $clistApp = new CommonList();
	  require_once(CLASS_DIR.'/common.class.php');	
	  $comApp = new Common();
	  $data                	= array();	   
	  $project_id = getFromSession('project_id');
	  if(getRequest('submit')) {
		mysql_query("START TRANSACTION;");
		$return_date=formatDate(getRequest('return_date'));
		$voucher_no = getRequest('voucher_no');
	    $NetPayble	= getRequest('net_payble');
		$deliveryAmount=getRequest('item_delivery_amount');
		$returnAmount 	= getRequest('total_return_price');
		$net_return_amount=getRequest('net_return_amount');
		//======= Customer Cr ======
		$customer = getRequest('customer');
		$CrReturn = $returnAmount;		
		$totalPartyCR  = $salesApp->getTotalCreditAmount($customer,getFromSession('project_id'));
		$totalPartyDR  = $salesApp->getTotalDebitAmount($customer,getFromSession('project_id'));					 
		$PartyBalance  = ($totalPartyDR-($totalPartyCR+$CrReturn));						 
		$salesApp->saveAccountJournal($voucher_no,$customer,"Customer",$project_id,"Product Return ",0,$CrReturn,$PartyBalance,1,$return_date);
				
	  	if($net_return_amount==0){			
		    if($returnAmount==$deliveryAmount){ $due=0;}			
			$umsql="UPDATE ".SALES_MASTER_TBL." SET return_amount='".$returnAmount."',due='$due' WHERE voucher_no='".$voucher_no."'";
			mysql_query($umsql);
			$this->updateSalesDetails(getRequest('ttlfields'));
	  	}elseif($net_return_amount>0){
		    $due=0;	
			if($returnAmount>$net_return_amount){
			$return_amount=	($returnAmount-$net_return_amount);	
			}else{$return_amount =$returnAmount;}
			$umsql="UPDATE ".SALES_MASTER_TBL." SET return_amount='".$return_amount."',due='$due' WHERE voucher_no='".$voucher_no."'";
			mysql_query($umsql);			
			$this->updateSalesDetails(getRequest('ttlfields'));
			$last_return_amount = $this-> adjustReturnAmount($customer,$net_return_amount); 
			if($last_return_amount>0){
			$RMSQL="INSERT INTO ".SALES_RETURN_PAYBLE_TBL."(voucher_no,project_id,customer_id,currency,return_amount,paid_amount,due,return_date,created_by)  
			VALUES('$voucher_no','$project_id','$customer','$currency','$last_return_amount','0','$last_return_amount','$return_date','$return_by')";
			mysql_query($RMSQL); 
			}
	  	}elseif($net_return_amount<0){
		    $due=abs($net_return_amount);			
			$umsql="UPDATE ".SALES_MASTER_TBL." SET return_amount='".$returnAmount."',due='$due' WHERE voucher_no='".$voucher_no."'";
			mysql_query($umsql);
			$this->updateSalesDetails(getRequest('ttlfields'));
	  	}
		mysql_query("COMMIT;");
		header("location:index.php?app=sales.report&cmd=sales.return&msg=Successfully done sales return");
	  }else{
	  	 $voucher_no 	= getRequest('voucher_no');  
		 $data['cmd']         = getRequest('cmd');		  
		 $advArr 			  = $salesApp->getSalesMasterInfo($voucher_no);
         $advArr 			  = parseThisValue($advArr); 
		 $data   			  = array_merge(array(),$advArr); 
		 $data['item_list']	  = $salesApp->getProductList($voucher_no);
	  }
	  require_once(CURRENT_APP_SKIN_FILE); 
	  return $data[0];	    
   }
  function adjustReturnAmount($customer,$DrAmount){
  $PMsql = "SELECT voucher_no,net_payble,paid_amount,return_amount,due,item_delivery_amount FROM ".SALES_MASTER_TBL." WHERE customer ='".$customer."' AND project_id = '$project_id' AND paid_amount<net_payble AND due>0";
	$PMRes = mysql_query($PMsql);
	if(mysql_num_rows($PMRes)>0){
	while($PMrow = mysql_fetch_object($PMRes)){
		$voucher_no 	= $PMrow->voucher_no;  $net_payble = $PMrow->net_payble;
		$paid_amount 	= $PMrow->paid_amount; $return_amount = $PMrow->return_amount; $existing_due = $PMrow->due;
		$item_delivery_amount = $PMrow->item_delivery_amount;
		
		if(($DrAmount>=$existing_due) && ($item_delivery_amount>=$net_payble)){			
			if($existing_due>0){
			$DrAmount 		= $DrAmount - $existing_due;
			$return_amount 	= ($return_amount + $existing_due); 
			$PMUpdate = "UPDATE ".SALES_MASTER_TBL." SET return_amount = $return_amount, due=0  WHERE voucher_no ='$voucher_no' AND project_id = '$project_id'";
			mysql_query($PMUpdate);
			} 
		}elseif(($DrAmount<$existing_due) && ($item_delivery_amount>=$net_payble)){
		$present_due 	  = ($existing_due - $DrAmount);
		if($existing_due>0){
			$return_amount 	= ($return_amount + $DrAmount); 			
			$DrAmount 		=  0;
			$PMUpdate = "UPDATE ".SALES_MASTER_TBL." SET return_amount=$return_amount,due=$present_due WHERE voucher_no='$voucher_no' AND project_id= '$project_id'";
			mysql_query($PMUpdate);
		}
		break;
		}
		
	} // end while
	}// end num rows
	return $DrAmount;
  } 
  function updateSalesDetails($totalfields)
  {  	
	require_once(CLASS_DIR.'/purchase.class.php');	
	$poApp 			= new Purchase();
	require_once(CLASS_DIR.'/common.list.class.php');	
	$comlistApp 	= new CommonList();
	require_once(CLASS_DIR.'/sales.class.php');	
	$salesApp 			= new Sales();
	  
	$return_date	= formatDate(getRequest('return_date'));  $return_by = getFromSession('userid');
	$return_amount	= getRequest('net_return_amount'); $store_id = getRequest('store_id');
	$j=1; 
	for($j; $j<=$totalfields; $j++){
		$sales_details_id= getRequest("sales_details_id$j");
		$sold_qty		 = getRequest("sold_qty$j");
		$return_qty		 = getRequest("return_qty$j");
		$balanceQty 	 = $sold_qty-$return_qty;  
	    $SdSql="SELECT voucher_no,pvoucher_no,project_id,customer,catagory,brand_id,product,serial,warranty,m_unit,purchase_price,unit_profit,unit_price,qty,delivery_qty,currency 
		FROM ".SALES_DETAILS_TBL." WHERE sal_detail_id='".$sales_details_id."'";
		$SdRes= mysql_query($SdSql);	
		$row = mysql_fetch_array($SdRes);
		$voucher_no=$row["voucher_no"];
		$pvoucher_no=$row["pvoucher_no"];
		$project_id=$row["project_id"];
		$catagory=$row["catagory"];
		$brand_id=$row["brand_id"];
		$product_id=$row["product"];
		$serial=$row["serial"];
		$warranty=$row["warranty"];
		$m_unit=$row["m_unit"];
		$purchase_price=$row["purchase_price"]; $sales_unit_price = $row["unit_price"]; $unit_profit = $row["unit_profit"];
		$customer_id 	= $row["customer"];  $currency = $row["currency"];  $currency_rate = 0;		
			
		$upsql = "UPDATE ".SALES_DETAILS_TBL." SET return_qty='$return_qty' WHERE sal_detail_id='".$sales_details_id."'";
		$ures = mysql_query($upsql);	
		if($ures){
		if($unit_profit>0){
		$ReturnProfite = ($unit_profit*$return_qty);
		//========= Direct Income Cr ==========
		$SalesIncomeId 	 = $comlistApp->getProductSalesIncomeId(getFromSession('project_id'));
		$totalSalesIncomeCR = $salesApp->getTotalCreditAmount($SalesIncomeId,getFromSession('project_id'));
		$totalSalesIncomeDR = $salesApp->getTotalDebitAmount($SalesIncomeId,getFromSession('project_id'));					 
		$SalesIncomeBalance = ($totalSalesIncomeDR-($totalSalesIncomeCR+$ReturnProfite));	
		$salesDtl = "Sales return from $voucher_no";				 
		$comlistApp->saveAccJournal($voucher_no,$SalesIncomeId,"Acc","Direct Incomes",getFromSession('project_id'),$salesDtl,0,$ReturnProfite,$SalesIncomeBalance,0,$return_date);
		}elseif($unit_profit<0){
		$ReturnProfite = ($unit_profit*$return_qty);
		$ReturnProfite = abs($ReturnProfite);
		//========= Direct Income Dr ==========
		$SalesIncomeId 	 = $comlistApp->getProductSalesIncomeId(getFromSession('project_id'));
		$totalSalesIncomeCR = $salesApp->getTotalCreditAmount($SalesIncomeId,getFromSession('project_id'));
		$totalSalesIncomeDR = $salesApp->getTotalDebitAmount($SalesIncomeId,getFromSession('project_id'));					 
		$SalesIncomeBalance = (($totalSalesIncomeDR+$ReturnProfite)-$totalSalesIncomeCR);	
		$salesDtl = "Return loss from sales return";			 
		$comlistApp->saveAccJournal($voucher_no,$SalesIncomeId,"Acc","Direct Incomes",getFromSession('project_id'),$salesDtl,$ReturnProfite,0,$SalesIncomeBalance,0,$return_date);
		}// save unit_profit		
				
		$PUSql="SELECT pur_detail_id,sales_qty FROM ".PURCHASE_DETAILS_TBL." WHERE product='$product_id' AND brand_id='$brand_id' 
	    AND project_id='$project_id' AND voucher_no='$pvoucher_no' AND serial='$serial'";
	    $Prorow = mysql_fetch_object(mysql_query($PUSql));
	    $pur_detail_id 	= $Prorow->pur_detail_id;
	    $sales_qty 	   	= ($Prorow->sales_qty-$return_qty);
	    
		$pdusql = "UPDATE ".PURCHASE_DETAILS_TBL." SET sales_qty='".$sales_qty."' WHERE pur_detail_id='$pur_detail_id'";
		$pdures = mysql_query($pdusql);
		if($pdures){
		$Psql = "SELECT product_type FROM ".PRODUCT_TBL." WHERE product_id = '$product_id' AND project_id = '$project_id'";
		$Prow = mysql_fetch_object(mysql_query($Psql));
		$product_type 		= $Prow->product_type;
		$totalCR  			= $poApp->getTotalCreditStock($product_id,getFromSession('project_id'));
		$totalDR  			= $poApp->getTotalDebitStock($product_id,getFromSession('project_id'));					 
		$StockBalance  		= (($totalDR + $return_qty) - $totalCR);	
		$this->saveStockJournal($voucher_no,$pvoucher_no,$project_id,$store_id,$product_id,$product_type,$serial,$warranty,$purchase_price,$m_unit,$return_qty,0,$StockBalance,$return_date);				
		//=== Stock Dr =====
		$StockAmount 	= ($purchase_price*$return_qty);
		$StockId 	 	= $comlistApp->getStockId(getFromSession('project_id'));
		$totalStockCr  	= $salesApp->getTotalCreditAmount($StockId,getFromSession('project_id'));
		$totalStockDr  	= $salesApp->getTotalDebitAmount($StockId,getFromSession('project_id'));					 
		$StockBalance  	= (($totalStockDr+$StockAmount)-$totalStockCr);	$description = "Product Sales Return";					 
		$comlistApp->saveAccJournal($voucher_no,$StockId,"Stock","Sales Return",getFromSession('project_id'),$description,$StockAmount,0,$StockBalance,0,$delivery_date);
		}// update purchase details
		$RSQL="INSERT INTO ".SALES_RETURN_TBL."(voucher_no,sales_details_id,project_id,customer_id,catagory,brand_id,product_id,currency,currency_rate,return_qty,
		sales_unit_price,return_date,return_by) VALUES('$voucher_no','$sales_details_id','$product_id','$customer_id','$catagory','$brand_id','$product_id','$currency',
		'$currency_rate','$return_qty','$sales_unit_price','$return_date','$return_by')";
		mysql_query($RSQL);
		}// update sales dtl
	}//  end for	
  } //End of updateSalesDetails
  
  function saveStockJournal($po_no,$voucher_no,$project_id,$store_id,$product_id,$product_type,$serial=NULL,$warranty=NULL,$unit_price=NULL,$m_unit,$DR=NULL,$CR=NULL,$balance,$create_date=NULL){
	$sql = "INSERT INTO ".STOCK_LEDGER_TBL." (po_no,voucher_no,project_id,store_id,product_id,product_type,serial,warranty,unit_price,m_unit,dr,cr,balance,create_date) 
	VALUES('".$po_no."','".$voucher_no."','".$project_id."','".$store_id."','".$product_id."','".$product_type."','".$serial."','".$warranty."','".$unit_price."','".$m_unit."','".$DR."','".$CR."','".$balance."','".$create_date."')";
		mysql_query($sql);
  }
  
  function getDueSalesReturnPaybleList($from,$to) { 
		if($from == "" && $to == ""){$from=0; $to=700;}
		$date_from 		= formatDate(getRequest('date_from'));
		$date_to 		= formatDate(getRequest('date_to'));				
		$project_id     = getFromSession('project_id');  
		$info           = array();    
		$info['table']  = SALES_RETURN_PAYBLE_TBL.' sr,'.SUB_ACC_HEAD_TBL.' s,'.PROJECT_TBL.' p,'.CURRENCY_TBL.' c';	
		$info['fields'] = array('pm.return_id','sr.voucher_no','sr.project_id','p.project_name','p.location','sr.customer_id','s.sub_head_name','s.head_details',"DATE_FORMAT(sr.return_date,'%d %b %y' ) as return_date",'sr.return_amount','c.curr_symble','sr.paid_amount','sr.due as return_due');
		if(getFromSession('project_type')!='Group Company'){
		$sql="sr.customer_id = s.sub_id AND sr.project_id = p.project_id AND sr.currency = c.currency_id AND sr.project_id = '".$project_id."' AND sr.due>0 ";
		}else{
		$sql="sr.customer_id = s.sub_id AND sr.project_id = p.project_id AND sr.currency = c.currency_id AND sr.due>0 ";
		}
		
		if($date_from!="" && $date_to ==""){
			$sql.=" AND sr.return_date >= '$date_from'";
		}elseif($date_from=="" && $date_to !=""){
			$sql.=" AND sr.return_date <= '$date_to'";
		}elseif($date_from!="" && $date_to !=""){
			$sql.=" AND sr.return_date BETWEEN '$date_from' AND '$date_to'";
		}

		$info['where']  =$sql;	
		$info['orderby'] = array("sr.return_id asc LIMIT $from,$to");
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
   function updategetSalesReturnPayble($return_id,$srref_no,$voucher_no,$CrAmount){
		$srsql= "SELECT return_amount as net_payble,paid_amount,due FROM ".SALES_RETURN_PAYBLE_TBL." WHERE 
		voucher_no  = '".$srref_no."' AND due >0 AND return_id='$return_id'";
		$srres = mysql_query($srsql);
		if(mysql_num_rows($srres)>0){
			$srow = mysql_fetch_object($srres);						
			$due = $srow->due;
			$net_payble = $srow->net_payble;
			$paidAmount = $srow->paid_amount;
			$total_due = $net_payble - ($paidAmount + $CrAmount);
			$total_paid = ($paidAmount + $CrAmount);
			$srusql= "UPDATE ".SALES_RETURN_PAYBLE_TBL." SET paid_amount ='".$total_paid."',due ='".$total_due."' WHERE voucher_no = '".$srref_no."' AND 
			 return_id='$return_id'";
			$srures = mysql_query($srusql);
			if($srures){ return true;}else{ return false;}
	   } 
	}// end updateCommissionMaster
      
} // End class
?>