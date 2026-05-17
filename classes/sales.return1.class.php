<?php
class SalesReturn{
   function run() { 
	 	     
      $cmd = getRequest('cmd');
	  $u_t_id = getFromSession('u_type_id');
      if( ($u_t_id == 101) || ($u_t_id == 102) || ($u_t_id == 103))
      {
      	switch ($cmd){
      	   case 'return'               	: $screen = $this->showEditor("Edit Page");    break;    
      	   case 'list'               	: $screen = $this->showEditor($msg);   break;
      	   default                      : $cmd = 'list'; $screen = $this->showEditor($msg);   break;
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
      
   function showEditor($msg = null) { 
      require_once(CLASS_DIR.'/sales.class.php');	
	  $salesApp 			= new Sales();
	  require_once(CLASS_DIR.'/common.list.class.php');	
	  $clistApp = new CommonList();
	  require_once(CLASS_DIR.'/common.class.php');	
	  $comApp = new Common();
	  $data                	= array();	   
	  
	  if(getRequest('submit')) {
		mysql_query("START TRANSACTION;");
		$return_date=formatDate(getRequest('return_date'));
		$voucher_no = getRequest('voucher_no');
	    $NetPayble	= getRequest('net_payble');
		$discount 	= getRequest('discount');
		$return_discount = getRequest('return_discount');
		$ReturnAmountAfterDiscount=getRequest('ReturnAfterDiscount');
		$net_return_amount=getRequest('net_return_amount');
	  	if($NetPayble>$ReturnAmountAfterDiscount){
			$remarks = $this->updateSalesDetails(getRequest('ttlfields'));
			if($net_return_amount>0){
			$net_payble  = ($NetPayble-$ReturnAmountAfterDiscount);
			$paid_amount = (getRequest('paid_amount')-$net_return_amount);
			$due		 = 0;
			$discount	 = ($discount-$return_discount);
			$total_value = ($net_payble+$discount);
			$umsql="UPDATE ".SALES_MASTER_TBL." SET total_value='$total_value',discount='$discount',net_payble='".$net_payble."',paid_amount='".$paid_amount."',due='0' 
			WHERE voucher_no='".$voucher_no."'";
			mysql_query($umsql);
			$customer = getRequest('customer');
			$fullReceivable = $net_return_amount;
			$PartyAcc_head = getRequest('customer');  
			$totalPartyCR  = $salesApp->getTotalCreditAmount($PartyAcc_head,getFromSession('project_id'));
			$totalPartyDR  = $salesApp->getTotalDebitAmount($PartyAcc_head,getFromSession('project_id'));					 
			$PartyBalance  = (($totalPartyDR+$fullReceivable)-$totalPartyCR);					 
			$salesApp->saveAccountJournal($voucher_no,$PartyAcc_head,"Acc",getFromSession('project_id'),$remarks,$fullReceivable,0,$PartyBalance,1,$return_date);			
			//echo "Update SM and SD... then net_return_amount will be Dr at Customer jarnal"; exit;
			}else{
			$discount	 = ($discount-$return_discount);
			$net_payble  = ($NetPayble-$ReturnAmountAfterDiscount);			
			$total_value = ($net_payble+$discount);
			$due		 = abs($net_return_amount);
			$umsql="UPDATE ".SALES_MASTER_TBL." SET total_value='$total_value',discount='$discount',net_payble='".$net_payble."',due='".$due."' WHERE voucher_no='".$voucher_no."'";
			mysql_query($umsql);
			}
	  	}else{
		//echo "Delete from SD and SM then ReturnAfterDiscount will be Dr at jarnal";
		$usmsql="UPDATE ".SALES_MASTER_TBL." SET total_value='0',discount='0',net_payble='0',due='0' WHERE voucher_no='".$voucher_no."'";
		mysql_query($usmsql);
		$remarks = $this->updateSalesDetails(getRequest('ttlfields'));
		//======= Party Cr ======
		$remarks="Return full sales order where sales order id is $voucher_no";
		$customer = getRequest('customer');
		$CrAmount = $NetPayble;
		$PartyAcc_head = getRequest('customer');  
		$totalPartyCR  = $salesApp->getTotalCreditAmount($PartyAcc_head,getFromSession('project_id'));
		$totalPartyDR  = $salesApp->getTotalDebitAmount($PartyAcc_head,getFromSession('project_id'));
		$PartyBalance  = ($totalPartyDR-($totalPartyCR+$CrAmount));					 
		$salesApp->saveAccountJournal($voucher_no,$PartyAcc_head,"Acc",getFromSession('project_id'),$remarks,0,$fullReceivable,$PartyBalance,1,$return_date);
		// Not Done
		//$this->addJournalDr4Amj($voucher_no,$net_return_amount,$return_date,$remarks);
		}
		mysql_query("COMMIT;");
		header("location:index.php?app=sales&cmd=sal_dtl&msg=Successfully done sales return");
	  }else{
	  	 $voucher_no 	= getRequest('voucher_no');  
		 $data['cmd']         = getRequest('cmd');		  
		 $advArr 					= $salesApp->getSalesMasterInfo($voucher_no);
         $advArr 					= parseThisValue($advArr); 
		 $data   					= array_merge(array(), $advArr); 
		 $data['item_list']			= $salesApp->getProductList($voucher_no);
		     
	  }      
	  require_once(CURRENT_APP_SKIN_FILE); 
	  return $data[0];	    
   }
   
  function updateSalesDetails($totalfields)
  {  	
	require_once(CLASS_DIR.'/purchase.class.php');	
	$poApp 			= new Purchase();
	$return_date	= formatDate(getRequest('return_date'));  $return_by = getFromSession('userid');
	$return_amount	= getRequest('net_return_amount');
	$j=1; $productStr=""; $QtyStr=""; $UpriceStr="";
	for($j; $j<=$totalfields; $j++){
		$sales_details_id= getRequest("sales_details_id$j");
		$sold_qty		 = getRequest("sold_qty$j");
		$return_qty		 = getRequest("return_qty$j");
		$balanceQty 	 = $sold_qty-$return_qty;  
	    $SdSql="SELECT voucher_no,pvoucher_no,project_id,customer,catagory,brand_id,product,serial,warranty,m_unit,purchase_price,unit_price,delivery_qty,currency 
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
		$purchase_price=$row["purchase_price"];
		$sales_unit_price = $row["unit_price"]; 
		$customer_id 	= $row["customer"];  $currency = $row["currency"];  $currency_rate = 0;
		$productStr.=$product_id.", ";  $QtyStr.= $return_qty.","; $UpriceStr.=$sales_unit_price.", ";
		
		$delivery_qty=($row["delivery_qty"]-$return_qty);
		$total = ($row["unit_price"]*$balanceQty);		
		$upsql = "UPDATE ".SALES_DETAILS_TBL." SET qty='".$balance_qty."',delivery_qty='$delivery_qty',total='".$total."' WHERE sal_detail_id='".$sales_details_id."'";
		$ures = mysql_query($upsql);		
		if($ures){		
		$PUSql="SELECT pur_detail_id,sales_qty FROM ".PURCHASE_DETAILS_TBL." WHERE product='$product_id' AND brand_id='$brand_id' 
	    AND project_id='$project_id' AND voucher_no='$pvoucher_no' AND serial='$serial'";
	    $Prorow = mysql_fetch_object(mysql_query($PUSql));
	    $pur_detail_id 	= $Prorow->pur_detail_id;
	    $sales_qty 	   	= ($Prorow->sales_qty-$return_qty);
	    $pdusql 		= "UPDATE ".PURCHASE_DETAILS_TBL." SET sales_qty='".$sales_qty."' WHERE pur_detail_id='$pur_detail_id'";
		mysql_query($pdusql);
		$Psql = "SELECT product_type FROM ".PRODUCT_TBL." WHERE product_id = '$product_id' AND project_id = '$project_id'";
		$Prow = mysql_fetch_object(mysql_query($Psql));
		$product_type 		= $Prow->product_type;
		$totalCR  			= $poApp->getTotalCreditStock($product_id,getFromSession('project_id'));
		$totalDR  			= $poApp->getTotalDebitStock($product_id,getFromSession('project_id'));					 
		$StockBalance  		= (($totalDR + $return_qty) - $totalCR);	
		$this->saveStockJournal($voucher_no,$pvoucher_no,$project_id,$product_id,$product_type,$serial,$warranty,$purchase_price,$m_unit,$return_qty,0,$StockBalance,$return_date);				
		
		$RSQL="INSERT INTO ".SALES_RETURN_TBL."(voucher_no,sales_details_id,project_id,customer_id,catagory,brand_id,product_id,currency,currency_rate,return_qty,
		sales_unit_price,return_date,return_by) VALUES('$voucher_no','$sales_details_id','$customer_id','$catagory','$brand_id','$product_id','$currency',
		'$currency_rate','$return_qty','$sales_unit_price','$return_date','$return_by')";
		mysql_query($RSQL);
		}
	}
	
	$RMSQL="INSERT INTO ".SALES_RETURN_MASTER_TBL."(voucher_no,project_id,customer_id,currency,return_amount,paid_amount,due,return_date,created_by) VALUES('$voucher_no','$project_id','$customer_id','$currency','$return_amount','0','$return_amount','$return_date','$return_by')";
	mysql_query($RMSQL);
	//====== save will be sales return master
	$Remarks = "Product No. ".$productStr." Sales return qty: $QtyStr  ";
	$Remarks.= " Uprice: $UpriceStr ";
	$customer_name = getCustomerName($customer_id);
	$Remarks.=" Sales Return to $customer_name";
	return $Remarks;	 
  } //End of updateSalesDetails
  
  function saveStockJournal($po_no,$voucher_no,$project_id,$product_id,$product_type,$serial=NULL,$warranty=NULL,$unit_price=NULL,$m_unit,$DR=NULL,$CR=NULL,$balance,$create_date=NULL){
	$sql = "INSERT INTO ".STOCK_LEDGER_TBL." (voucher_no,project_id,product_id,product_type,serial,warranty,unit_price,m_unit,dr,cr,balance,create_date) 
	VALUES('".$po_no."','".$voucher_no."','".$project_id."','".$product_id."','".$product_type."','".$serial."','".$warranty."','".$unit_price."','".$m_unit."','".$DR."','".$CR."','".$balance."','".$create_date."')";
		mysql_query($sql);
  }
  function createCrPayble($voucher_no,$customer_id,$head_type,$mode_of_payment,$transaction_type,$vouchar_type,$returnAmount,$remarks,$return_date)
   {   
	  date_default_timezone_set('Asia/Dhaka');  	
	  $transaction_type	= "Sales Return";			  
   	  $requestdata = array();
      $requestdata = getUserDataSet(CREDIT_VOUCHAR_TBL);
	  
	  $requestdata['voucher_no']   = $voucher_no;
	  $requestdata['account_head'] = $customer_id;
	  $requestdata['head_type']    = $head_type;
	  $requestdata['project_id']   = getFromSession('project_id');
	  $requestdata['mode_of_payment']= $mode_of_payment;
	  $requestdata['transaction_type'] = $transaction_type;
	  $requestdata['vouchar_type'] = $vouchar_type;
	  $requestdata['transaction_name'] 	= $transaction_type;
	  $requestdata['credit']     = $returnAmount;  
	  $requestdata['description']   = $remarks;	
      $requestdata['created_date'] 	= $return_date;  
      $requestdata['created_by']        = getFromSession('userid');
	  $requestdata['custom_voucher_no'] =$voucher_no;
	  
	  	  
      $info        		= array();
      $info['table']	= CREDIT_VOUCHAR_TBL;
      $info['data'] 	= $requestdata;     
      //$info['debug']  	= true;                     
      $res = insert($info);    
   }
    
} // End class
?>