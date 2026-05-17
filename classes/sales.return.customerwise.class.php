<?php
class SalesReturnCustomerWise
{
   
   function run()
   {         
      $cmd = getRequest('cmd');
	  $u_t_id = getFromSession('u_type_id');
      if( ($u_t_id ==101) || ($u_t_id == 102) || ($u_t_id == 103)) // 101 = sysadmin, 102 = admin, 103= salesman
      {

      	switch ($cmd)
      	{
      	   	case 'add'		: $this->showEditor(); break;
		case 'edit'		: $this->showEditor(); break;
		case 'sal_dtl'		: $this->showEditor4SalesDetails(); break;
		case 'admin_sal_dtl'	: $this->showAllCompaniesSalesDetails(); break;
		case 'loadProduct'  	: $this->loadProduct4Catagory(trim(getRequest('catagory_id'))); break; 
		case 'get_dtl'  	: $this->loadProductDtl(trim(getRequest('product_id'))); break;  
		case 'save_tmp'  	: $this->saveTempSales(); break;   
		case 'deltemp'		: $this->delTempSales(); break;    
		case 'save_transfer'	: $this->saveSalesItem(); break; 
		case 'print_return'	: $screen = $this->showPrintEditor($msg); break;  
		case 'delete'           : $screen = $this->deleteRecord(getRequest('voucher_no')); break;
      	   	default                 : $cmd = 'list'; $screen = $this->showEditor();   break; 
		case 'load_rate'     	: $this->loadProductRate(trim(getRequest('product_id')));break; 
      	}
      }elseif($u_t_id == 104) // 104 = acc
      {
      	switch ($cmd)
      	{
		   case 'sal_dtl'				: $this->showEditor4SalesDetails(); break;
		   case 'print_return'			: $screen = $this->showPrintEditor($msg); break;
      	   default                   	: $cmd = 'list'; $screen = $this->showEditor();   break;
      	}

      }elseif($u_t_id == 107) // 107 = hr
      {
      	switch ($cmd)
      	{
	   case 'delete'             	: $screen = $this->deleteRecord(getRequest('voucher_no')); break;
      	   default                   	: $cmd = 'list'; $screen = $this->showEditor();   break;
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
  
   function showPrintEditor($msg = null) {   	  
	  $voucher_no 	= getRequest('voucher_no');  
	  if ($voucher_no) {
         $advArr 			= $this->getReturnMasterInfo($voucher_no);
         $advArr 			= parseThisValue($advArr); 
		 $data   			= array_merge(array(), $advArr); 
		 $data['item_list']	= $this->getProductList($voucher_no);
		 $data['message'] 	= $msg;
		 $data['cmd']     	= getRequest('cmd');
		 require_once(PRNIT_SALES_RETURN_INVOICE_SKIN);      
		 return true;
	 }
   }
     
   function showEditor($msg = null) {
   	   $data                	= array();
           require_once(CLASS_DIR.'/common.list.class.php');	
	   $comListApp 	= new CommonList();	   	
	   $data['customer_list'] 	= $comListApp->getCustomerList(); 
	   $data['supplier_list'] 	= $comListApp->getSupplierList(); 
	   $data['product_list'] 	= $comListApp->getProductList(); 
	   $data['brand_list'] 		= $comListApp->getBrandList();	   
	   $data['currency_list']   = $this->getCurrencyList();  	
	   $data['depo_list'] 		= $comListApp->getDeliveryPointList(); 
	   $data['tmp_sales']		= $this->getTempSales();	   
	   
	   $data['cmd']         	= getRequest('cmd');   
	   require_once(CURRENT_APP_SKIN_FILE); 
	   return $data[0];
   }
	
   //===== Saart Save Sales ====
  
   function saveTempSales(){
	$str 			= getRequest('str');
	$strArr 		= explode("####",$str);
	//======= Insert into tamp ========	
	$requestdata = array();
	$requestdata = getUserDataSet(TEMP_SALES_RETURN_TBL);
	$project_id  				= getFromSession('project_id');
	$requestdata['project_id'] 		= $project_id;
	$requestdata['customer'] 		= getRequest('customer');
	$requestdata['baddebts_godown'] 	= getRequest('baddebts_godown');
	$requestdata['intact_godown'] 		= getRequest('intact_godown');
	$requestdata['return_date'] 		= formatDate(getRequest('return_date'));
	$requestdata['currency'] 		= getRequest('currency');
	$requestdata['currencyName'] 		= getRequest('currencyName');
	$requestdata['productid'] 		= getRequest('productid');
	$requestdata['product_status'] 		= getRequest('product_status');
	$requestdata['qty'] 			= getRequest('qty');
	$requestdata['unit_price'] 		= getRequest('unit_price');
	$requestdata['discount_percent'] 	= getRequest('discount_percent');
	$requestdata['total'] 			= getRequest('total');
	$requestdata['net_total'] 		= getRequest('nettotal');
	$sql = "SELECT p.product_name,p.catagory,c.catagory_name,p.brand_code,b.brand_name,p.m_unit FROM ".PRODUCT_TBL." as p,".CATAGORY_TBL." as c,".BRAND_TBL." as b 
	WHERE p.catagory=c.catagory_code AND p.brand_code=b.brand_id AND p.product_id = '".$requestdata['productid']."'";
	$row 		= mysql_fetch_object(mysql_query($sql));
	$requestdata['product_name'] 	= $row->product_name;	
	$requestdata['catagory'] 		= $row->catagory;		
	$requestdata['catagoryname'] 	= $row->catagory_name;
	$requestdata['brand_id'] 		= $row->brand_code;	
	$requestdata['brandname'] 		= $row->brand_name;	
	//$requestdata['details'] 		= getRequest('details');
	$requestdata['munit'] 			= $row->m_unit;
	$requestdata['qty'] 			= getRequest('qty');
	$requestdata['unit_price'] 		= getRequest('unit_price');
	$requestdata['total'] 			= getRequest('total');
	
	$requestdata['created_by'] 		= getFromSession('userid');		
	$info        		=  array();
	$info['table']	= TEMP_SALES_RETURN_TBL;
	$info['data'] 	= $requestdata;     
	//$info['debug']  	=  true;
	$res = insert($info);
	  
	$str1="<table width='100%' align='center' bgcolor='#99CC66'>
	<tr style='color:#fff;' bgcolor='#00B000' height=28>
	  <td width='2%' nowrap><div align='left'>SL</div></td>
	  <td width='28%' nowrap><div align='left'>Product Name</div></td>
	  <td width='15%' nowrap><div align='left'>Catagory</div></td>
	  <td width='12%' nowrap><div align='left'>Brand</div></td>
	  <td width='5%' nowrap><div align='left'>Baddebts</div></td>
	  <td width='8%' nowrap><div align='right'>Return Qty</div></td>
	  <td width='8%' nowrap><div align='right'>Rate</div></td>
	  <td width='8%' nowrap><div align='right'>Deduction</div></td>	  
	  <td width='8%' nowrap><div align='right'>Net Amount</div></td>				  
	  <td width='6%' nowrap align='center'>Option</td>
	</tr>";
	$total_value = 0; $product_discount=0; $TotalQty=0; $total_sales_return =0; $total_baddebts =0; $sl=1;
	$total_discount = 0; $net_total_value=0;
	$getSql		= "SELECT * FROM ".TEMP_SALES_RETURN_TBL." WHERE created_by = '".getFromSession('userid')."' AND project_id='".$project_id."'";
	$gres 		= mysql_query($getSql);
	while($row = mysql_fetch_array($gres)){
	extract($row);
	$total_value+=$total; $net_total_value+=$net_total; $TotalQty+=$qty;
	if($product_status=="No"){ $total_sales_return+=$net_total; }else{ $total_baddebts+=$net_total; }
	$str2.="
	<tr style='color:#000000' bgcolor='#fff'>
	  <td width='2%' nowrap align='left'>$sl</td>
	  <td width='28%' nowrap align='left'>$product_name</td>
	  <td width='15%' nowrap align='left'>$catagoryname</td>
	  <td width='12%' nowrap align='left'>$brandname</td>
	  <td width='5%' nowrap align='left'>$product_status</td>
	  <td width='8%' nowrap><div align='right'>$qty $munit</div></td>
	  <td width='8%' nowrap align='right'>$unit_price $currencyName</td>	  
	  <td width='8%' nowrap align='right'>$discount_percent %</td>				  
	  <td width='8%' nowrap align='right'>$net_total</td>					  
	  <td width='6%' nowrap align='center'><a href=\"?app=sales.return.customerwise&cmd=deltemp&id=$tmp_id\"><img src=\"images/common/icons/delete.gif\"></a></td>
	</tr>"; $sl++;
	}
	$total_discount = ($total_value-$net_total_value);
	$str3="
	<tr style='color:#000;' bgcolor='#CCCCCC' height=25>
	  <td colspan='6' nowrap><div align='right'>Total </div></td>
	  <td nowrap align='right'>$TotalQty $munit</td>
	  <td nowrap>&nbsp;</td>
	  <td nowrap align='right'>$net_total_value $currencyName</td>
	  <td nowrap align='center'>&nbsp;</td>
	</tr>
	</table>";
	$total_salesStr = $str1.$str2.$str3."####-@@@@".$net_total_value."####-@@@@".$total_sales_return."####-@@@@".$total_baddebts."####-@@@@".$total_discount;
	echo $total_salesStr;
   }
   function delTempSales(){
	$tmp_id = $_REQUEST['id'];
	if($tmp_id!=""){
	 $dsql = "DELETE FROM ".TEMP_SALES_RETURN_TBL." WHERE tmp_id ='".$tmp_id."'";
	 mysql_query($dsql);
	}		
	header("location:?app=sales.return.customerwise&cmd=add");
   }
   function getTempSales(){
	$project_id  	= getFromSession('project_id');
	$str1="<table width='100%' align='center' bgcolor='#99CC66'>
	<tr style='color:#fff;' bgcolor='#00B000' height=28>
	  <td width='2%' nowrap><div align='left'>SL</div></td>
	  <td width='28%' nowrap><div align='left'>Product Name</div></td>
	  <td width='15%' nowrap><div align='left'>Catagory</div></td>
	  <td width='12%' nowrap><div align='left'>Brand</div></td>
	  <td width='5%' nowrap><div align='left'>Baddebts</div></td>
	  <td width='8%' nowrap><div align='right'>Return Qty</div></td>
	  <td width='8%' nowrap><div align='right'>Rate</div></td>
	  <td width='8%' nowrap><div align='right'>Deduction</div></td>	  
	  <td width='8%' nowrap><div align='right'>Net Amount</div></td>				  
	  <td width='6%' nowrap align='center'>Option</td>
	</tr>";
	$total_value = 0; $product_discount=0; $TotalQty=0; $total_sales_return =0; $total_baddebts =0; $sl=1;
	$total_discount = 0; $net_total_value=0;
	$getSql		= "SELECT * FROM ".TEMP_SALES_RETURN_TBL." WHERE created_by = '".getFromSession('userid')."' AND project_id='".$project_id."'";
	$gres 		= mysql_query($getSql);
	while($row = mysql_fetch_array($gres)){
	extract($row);
	$total_value+=$total; $net_total_value+=$net_total; $TotalQty+=$qty;
	if($product_status=="No"){ $total_sales_return+=$net_total; }else{ $total_baddebts+=$net_total; }
	$str2.="
	<tr style='color:#000000' bgcolor='#fff'>
	  <td width='2%' nowrap align='left'>$sl</td>
	  <td width='28%' nowrap align='left'>$product_name</td>
	  <td width='15%' nowrap align='left'>$catagoryname</td>
	  <td width='12%' nowrap align='left'>$brandname</td>
	  <td width='5%' nowrap align='left'>$product_status</td>
	  <td width='8%' nowrap><div align='right'>$qty $munit</div></td>
	  <td width='8%' nowrap align='right'>$unit_price $currencyName</td>	  
	  <td width='8%' nowrap align='right'>$discount_percent %</td>				  
	  <td width='8%' nowrap align='right'>$net_total</td>					  
	  <td width='6%' nowrap align='center'><a href=\"?app=sales.return.customerwise&cmd=deltemp&id=$tmp_id\"><img src=\"images/common/icons/delete.gif\"></a></td>
	</tr>"; $sl++;
	}
	$total_discount = ($total_value-$net_total_value);
	$str3="
	<tr style='color:#000;' bgcolor='#CCCCCC' height=25>
	  <td colspan='6' nowrap><div align='right'>Total </div></td>
	  <td nowrap align='right'>$TotalQty $munit</td>
	  <td nowrap>&nbsp;</td>
	  <td nowrap align='right'>$net_total_value $currencyName</td>
	  <td nowrap align='center'>&nbsp;</td>
	</tr>
	</table>";
	$total_salesStr = $str1.$str2.$str3."####-@@@@".$net_total_value."####-@@@@".$total_sales_return."####-@@@@".$total_baddebts."####-@@@@".$total_discount;
	return $total_salesStr;
  }
	
  //====== End Save Sales =====
  
  function saveInPurchaseDetails($voucher_no,$catagory,$brand_id,$product,$m_unit,$qty,$unit_price){
		$project_id     = getFromSession('project_id');    
	    $created_by     = getFromSession('userid');	
		$total 			= ($qty*$unit_price);	
		$sqlD="INSERT INTO ".PURCHASE_DETAILS_TBL."(voucher_no,project_id,catagory,brand_id,product,m_unit,unit_price,qty,rec_qty,total,created_by) 
		VALUES('$voucher_no','$project_id','$catagory','$brand_id','$product','$m_unit','$unit_price','$qty','$qty','$total','$created_by')";
		$res2=mysql_query($sqlD);
		if($res2){ return true;	}else{ return false;} 
  } 
  function insertReturnDetails($voucher_no){
	$requestdata 				= array();
	$arr_catagory_product_id	= array();	
	$project_id  			= getFromSession('project_id');
	$currency        		= getRequest('currency');
	
	$getSql	= "SELECT * FROM ".TEMP_SALES_RETURN_TBL." WHERE created_by = '".getFromSession('userid')."' AND project_id='".getFromSession('project_id')."'";
	$gres 	= mysql_query($getSql);
	if(mysql_num_rows($gres)>0){
		while($row = mysql_fetch_object($gres)){
		$requestdata['voucher_no']	= $voucher_no;
		$requestdata['project_id']	= $project_id;    
		$requestdata['customer_id']	= getRequest('customer'); 
		if($row->product_status=="No"){
		$requestdata['in_stock_id'] = $row->intact_godown;      
		}else{
		$requestdata['in_stock_id'] = $row->baddebts_godown; 
		}
		$discount_percent = $row->discount_percent;
		$in_stock_id = $requestdata['in_stock_id'];
		$requestdata['product_status'] = $row->product_status;
		$requestdata['catagory'] 	= $row->catagory; $catagory = $row->catagory;
		$requestdata['brand_id'] 	= $row->brand_id; $brand_id = $row->brand_id;
		$requestdata['product_id'] 	= $row->productid; $product_id	= $row->productid; 
		$requestdata['m_unit'] 		= $row->munit;       	  
		//$requestdata['details'] 	= $row->details;    	  
		$requestdata['unit_price'] 	= $row->unit_price; $unit_price = $row->unit_price; 
	 	  
		$requestdata['return_qty'] 	= $row->qty;   
		$requestdata['return_amount'] = $row->total; 		
		$discount_amount = (($row->unit_price/100)*$discount_percent);  
		$requestdata['discount_amount'] = ($discount_amount*$row->qty);	
		$requestdata['net_amount']  	= $row->net_total; 
		$requestdata['return_date']	= formatDate(getRequest('return_date'));	  	
		$requestdata['return_by'] 	= getFromSession('userid');	
		
		$info        	=  array();
		$info['table']	= SALES_RETURN_TBL;
		$info['data'] 	= $requestdata;
		//$info['debug']  	=  true;   
		$res = insert($info);
		if($res){
			$return_date = formatDate(getRequest('return_date'));
			$Prosql = "SELECT product_type FROM ".PRODUCT_TBL." WHERE product_id = '$product_id' AND project_id = '$project_id'";
			$Prorow = mysql_fetch_object(mysql_query($Prosql));
			$product_type = $Prorow->product_type;		
			//===== Dr Stock (In Stock)======
			if($row->product_status=="No"){
			$this->saveInPurchaseDetails($voucher_no,$catagory,$brand_id,$product_id,$row->munit,$row->qty,$row->unit_price);
			$totalFCR  = $this->getTotalCreditStock($product_id,getFromSession('project_id'));
			$totalFDR  = $this->getTotalDebitStock($product_id,getFromSession('project_id'));
			}else{
			$totalFCR  = $this->getTotalCreditStock($product_id,getFromSession('project_id'));
			$totalFDR  = $this->getTotalDebitStock($product_id,getFromSession('project_id'));
			}					 
			$TTbalance = (($totalFDR+$row->qty) - $totalFCR);	$note = "Sales Return";
			$this->saveStockJournal($voucher_no,"SR",$project_id,$in_stock_id,$product_id,$product_type,$note,$unit_price,$row->munit,$row->qty,0,$TTbalance,$return_date);
		
		}// if save end
		}// end while 
        }// end if
	
	if($res){ 
	 $dsql = "DELETE FROM ".TEMP_SALES_RETURN_TBL." WHERE created_by = '".getFromSession('userid')."' AND project_id='".getFromSession('project_id')."'";
	 mysql_query($dsql);
	}
  } //End of the function insertSalesDetails()

  
  function insertReturnMaster(){
	  //==== Get Total Return Amount then  Customer Cr, Stock Dr, Direct Income Dr =====	  
	  require_once(CLASS_DIR.'/common.list.class.php');	
	  $comlistApp 	 = new CommonList();
	  $project_id    = getFromSession('project_id'); 
	     
	  $Party_head    = getRequest('customer');  
	  $totalPartyCR  = $comlistApp->getTotalCreditAmount($Party_head,$project_id);
	  $totalPartyDR  = $comlistApp->getTotalDebitAmount($Party_head,$project_id);
	  $PreviousPartyBalance = ($totalPartyDR-$totalPartyCR);
	  $BadDebtExp    = getRequest('total_baddebts'); 
	  
	  $requestdata = array();	
	  $requestdata = getUserDataSet(SALES_RETURN_MASTER_TBL); 
	  $requestdata['previour_balance']= $PreviousPartyBalance;
	  $requestdata['return_date']	= formatDate(getRequest('return_date'));	  
	  $requestdata['project_id']    = getFromSession('project_id');    
	  $requestdata['created_by']    = getFromSession('userid');	
	  $requestdata['created_time']  = date('Y-m-d h:i:s');
	  $voucher_no = $this->createVoucharID();
	  $requestdata['voucher_no']   = $voucher_no;	  
	  $info        		=  array();
	  $info['table']	= SALES_RETURN_MASTER_TBL;
	  $info['data'] 	= $requestdata;     
	  //$info['debug']  =  true;
	  $res = insert($info);
	  if($res){
	  	$created_by  = getFromSession('userid'); $net_payble = getRequest('net_payble');
	        $return_date = formatDate(getRequest('return_date')); $created_date = date('Y-m-d h:i:s');
	  	$sqlM="INSERT INTO ".PURCHASE_MASTER_TBL."(voucher_no,project_id,purchase_date,purchase_type,total_value,net_payble,paid_amount,due,item_received_amount,
		created_by,created_date) VALUES('$voucher_no','$project_id','$return_date','Production','$net_payble','$net_payble','$net_payble','0',
		'$net_payble','$created_by','$created_date')";
		mysql_query($sqlM);
				
		//======= Start Accounts Ledger ========
		$DrAmount = getRequest('net_payble'); 
	 	//===== Customer Recievable Cr ======   
		$account_head  = getRequest('customer');  	
	 	$totalPartyCR  = $comlistApp->getTotalCreditAmount($account_head,$project_id);
	 	$totalPartyDR  = $comlistApp->getTotalDebitAmount($account_head,$project_id);
	 	$PreviousPartyBalance = ($totalPartyDR-$totalPartyCR);					 
	 	$PartyBalance  = ($totalPartyDR-($totalPartyCR+$DrAmount));	
		$note = getRequest('note');
		if($note==""){ 
		 $note ="Amount payable against return"; 
		}				 
	 	$comlistApp->saveAccJournal($voucher_no,$account_head,"Customer","Sales Return",$project_id,$note,0,$DrAmount,$PartyBalance,1,$return_date);		

		//===== Adjust with Party Previous due ====== 
		$this->adjustCustomerReceibavle($voucher_no,$DrAmount,$account_head,$return_date);
	 	
		//======= AC Sales Return Dr ======
		if($DrAmount >0){
		 $description 	  = "Return sales goods";
		 $ACSalesId 	  = $comlistApp->getSalesReturnId($project_id);
		 $store_id = getRequest('intact_godown');
                $returnHeadID = $comlistApp->getStoreMapLedgerID($store_id, "return");
                if ($returnHeadID) {
                    $ACSalesId = $returnHeadID;
                }
		 if($ACSalesId){
		  $ACSalesAmount  = $comlistApp->getAccounceBalance($ACSalesId,$project_id);
		  $SalesBalance   = ($ACSalesAmount+$DrAmount);
		  $comlistApp->saveAccJournal($voucher_no,$ACSalesId,"Sales","Sales Return",$project_id,$description,$DrAmount,0,$SalesBalance,1,$return_date); 
		 }
		}// DrAmount >0
		
		if($BadDebtExp >0){
			 //========= Expire or Bad Debt Dr =========
			 $BadDebtId 	  = $comlistApp->getBadDebtExpId($project_id);
			 if($BadDebtId){	 	 
			 $description	  = "Expire or Bad Debt item";
			 $BadDebtBL 	  = $comlistApp->getAccounceBalance($BadDebtId,$project_id);
			 $BadDebtBalance = ($BadDebtBL+$BadDebtExp);
			 $comlistApp->saveAccJournal($transfer_no,$BadDebtId,"Expense","Expire or Bad Debt",$project_id,$description,$BadDebtExp,0,$BadDebtBalance,0,$return_date);
			 }
		}
		//======= Stock will be Dr ======
		$StockAmount   = getRequest('total_sales_return');
		$StockId       = $comlistApp->getFGStockId($project_id);
		$totalStockCr  = $this->getTotalCreditAmount($StockId,$project_id);
		$totalStockDr  = $this->getTotalDebitAmount($StockId,$project_id);
		$StockBalance  = (($totalStockDr+$StockAmount)-$totalStockCr);
		$description   = "Return sales item by customer";					 
		$comlistApp->saveAccJournal($voucher_no,$StockId,"Stock","SR",$project_id,$description,$StockAmount,0,$StockBalance,0,$return_date);
		//========= Direct Income Dr (15% deduction of Sales Return)======
		$DisAmount 	= getRequest('discount_amount');
		if($DisAmount >0){
		$description    = "Get profit from sales return";		
		$IncomeId 	= $comlistApp->getOthersIncomeId($project_id);
		$totalIncomeCR  = $comlistApp->getTotalCreditAmount($IncomeId,$project_id);
		$totalIncomeDR  = $comlistApp->getTotalDebitAmount($IncomeId,$project_id);
		$IncomeBalance  = (($totalIncomeDR+$DisAmount)-$totalIncomeCR);					 
		$comlistApp->saveAccJournal($voucher_no,$IncomeId,"Income","Return income",$project_id,$description,$DisAmount,0,$IncomeBalance,0,$return_date);
		}
		// ====== End Accounts Ledger ========
		return $voucher_no;
	  }else{
	  	return 0;
	  }
	}
   function adjustCustomerReceibavle($NewVoucherNo,$DrAmount,$account_head,$created_date){
		require_once(CLASS_DIR.'/common.list.class.php');	
	    $clistApp = new CommonList();			
		$project_id = getFromSession('project_id');	
		//======= Receibavle for Sales to him ===========
		$PMsql = "SELECT voucher_no,net_payble,paid_amount,due,item_delivery_amount FROM ".SALES_MASTER_TBL." WHERE customer ='".$account_head."' 
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
			
	   //======= Receibavle for Opening Balance ===========		
	   if($DrAmount>0){
		$rsql= "SELECT voucher_no,debit,paid_amount,due FROM ".DEVIT_VOUCHAR_TBL." WHERE account_head='".$account_head."' AND vouchar_type='Recievable Vouchar' 
		AND due >0 AND status=0  ORDER BY voucher_no ASC";  
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
		$SRPSql="SELECT return_id,supplier,return_amount,paid_amount,due FROM ".PURCHASE_RETURN_RECEIBAVLE_TBL." WHERE supplier ='".$account_head."' 
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
				$clistApp->saveVoucherAdjustHistory($NewVoucherNo,$project_id,PURCHASE_RETURN_RECEIBAVLE_TBL,$return_id,$existing_due,"+");
				} 
			}elseif(($DrAmount<$existing_due)){					
				if($existing_due >0 && $DrAmount >0){
				$totalpaid 	 = ($paid_amount + $DrAmount); 
				$present_due = ($existing_due - $DrAmount);
				$adjustAmount = $DrAmount; $DrAmount 	 = 0;
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
		if($DrAmount>0){
		$customer = $account_head; $return_date = $created_date; $return_by = getFromSession('userid'); 
		$currency = getRequest('currency');	if($currency==""){$currency = 1;} if($voucher_no==""){$voucher_no = $NewVoucherNo;}
		$RMSQL="INSERT INTO ".SALES_RETURN_PAYBLE_TBL."(voucher_no,project_id,customer_id,currency,return_amount,paid_amount,due,payble_source,return_date,created_by)  
		VALUES('$voucher_no','$project_id','$customer','$currency','$DrAmount','0','$DrAmount','Sales Return','$return_date','$return_by')";
		mysql_query($RMSQL); 
		$clistApp->saveVoucherAdjustHistory($NewVoucherNo,$project_id,SALES_RETURN_PAYBLE_TBL,$voucher_no,$DrAmount,"Payble ROA");
		}
		
		return $DrAmount;
	}
   function saveSalesItem(){
		mysql_query("START TRANSACTION;");
		mysql_query("SET autocommit=0;");
		$voucher_no = $this->insertReturnMaster();
		$this->insertReturnDetails($voucher_no); 
		mysql_query("COMMIT;");
		if($voucher_no!=""){
		header("location:index.php?app=sales.return.customerwise&cmd=print_return&voucher_no=".$voucher_no);	
		}else{
		header("location:index.php?app=sales.return.customerwise&cmd=add");
		}
   }
   function deleteRecord($voucher_no){
		require_once(CLASS_DIR.'/common.list.class.php');	
		$clistApp 	= new CommonList(); 
		require_once(CLASS_DIR.'/voucher.edit.class.php');	
		$veApp 		= new VoucherEdit(); 
		$data	  	= array();
		$project_id = getFromSession('project_id');
		if($voucher_no!=""){
		$getdSql= "SELECT * FROM ".SALES_RETURN_MASTER_TBL." WHERE voucher_no='".$voucher_no."' AND project_id='".$project_id."'";
		$gdres  = mysql_query($getdSql);
		$drow = mysql_fetch_object($gdres);
		$customer = $drow->customer;
		//========= Rollback for Delete===========
		mysql_query("START TRANSACTION;");
		
		$HeadType 		  = getHeadType($customer);
		$head_type  = getHeadType($customer);
		if($head_type=="Customer"){
		$veApp->rollbackCustomerReceibavle($voucher_no);
		}elseif($head_type=="Supplier"){
		$veApp->rollbackSupplierReceibavle($voucher_no);
		}
		
		//========= Delete All ===========
		$Dsql="DELETE FROM ".SALES_RETURN_MASTER_TBL." WHERE voucher_no='".$voucher_no."' AND project_id='".$project_id."'";
		mysql_query($Dsql); 
		$Csql="DELETE FROM ".SALES_RETURN_TBL." WHERE voucher_no='".$voucher_no."' AND project_id='".$project_id."'";
		mysql_query($Csql);
		$Pmsql="DELETE FROM ".PURCHASE_MASTER_TBL." WHERE voucher_no='".$voucher_no."' AND project_id='".$project_id."'";
		mysql_query($Pmsql); 
		$Pdsql="DELETE FROM ".PURCHASE_DETAILS_TBL." WHERE voucher_no='".$voucher_no."' AND project_id='".$project_id."'";
		mysql_query($Pdsql);		 
		$Stsql="DELETE FROM ".STOCK_LEDGER_TBL." WHERE voucher_no='".$voucher_no."' AND project_id='".$project_id."'";
		mysql_query($Stsql); 		
		$Jsql="DELETE FROM ".ACCOUNT_JOURNAL_TBL." WHERE voucher_no='".$voucher_no."' AND project_id='".$project_id."'";
		mysql_query($Jsql); 
		$Hsql="DELETE FROM ".VOUCHER_ADJUST_HISTORY_TBL." WHERE voucher_no='".$voucher_no."' AND project_id='".$project_id."'";
		mysql_query($Hsql);
		
		mysql_query("COMMIT;");
		
		header("location:index.php?app=sales.report&cmd=salesreturn_list&msg=Sales return successfully deleted");		
		}else{
		header("location:index.php?app=sales.report&cmd=salesreturn_list");		
		} 
   }
   
   function loadProductRate($product_id){
	  $project_id = getFromSession('project_id');  	  	  
	  $Prosql = "SELECT unit_price,m_unit FROM ".PRODUCT_TBL." WHERE product_id = '$product_id' AND project_id = '$project_id'";
	  $Prorow = mysql_fetch_object(mysql_query($Prosql));
	  echo $Prorow->unit_price."#####".$Prorow->m_unit;	
    }
  function saveAccountJournal($voucher_no,$sub_id,$head_type,$project_id,$description,$DR=NULL,$CR=NULL,$balance,$status=NULL,$purchare_date=NULL){
		$rsql= "SELECT head_type FROM ".SUB_ACC_HEAD_TBL." WHERE sub_id='".$sub_id."'";  
		$rres = mysql_query($rsql);
		$hnum = mysql_num_rows($rres);
		if($hnum>0){ 
		$hrow = mysql_fetch_object($rres);
		$head_type= $hrow->head_type;
		}else{ 	$head_type= "Supplier"; }		
		$sql = "INSERT INTO ".ACCOUNT_JOURNAL_TBL." (voucher_no,created_date,sub_id,head_type,project_id,description,dr,cr,balance,status) VALUES('".$voucher_no."','".$purchare_date."','".$sub_id."','".$head_type."','".$project_id."','".$description."','".$DR."','".$CR."','".$balance."','".$status."')";
		mysql_query($sql);
	}
	function getReturnMasterInfo($id){		
		$project_id     = getFromSession('project_id');  
		$info           = array();    
		$info['table']  = SALES_RETURN_MASTER_TBL.' rm,'.DELIVERY_POINT_TBL.' d,'.PROJECT_TBL.' p,'.CURRENCY_TBL.' c';	
		$info['fields'] = array('rm.voucher_no','rm.customer','rm.baddebts_godown','rm.intact_godown','d.delivery_point_name','d.details','p.project_name','p.location','p.project_logo','rm.previour_balance','rm.total_amount','rm.total_sales_return','rm.total_baddebts','rm.discount_percent','rm.discount_amount','rm.net_payble',"DATE_FORMAT(rm.return_date,'%d %b %y' ) as return_date","rm.note",'c.curr_symble','rm.created_by','rm.created_time');
	
		$sql="rm.baddebts_godown = d.delivery_pid AND rm.project_id = p.project_id AND rm.currency = c.currency_id AND rm.project_id = '".$project_id."' 
		AND rm.voucher_no = '$id'";
							
		$info['where']  =$sql;	  	
	    $info['groupby'] = array("rm.voucher_no");
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
   
        
   function getProductList($id) {  

		$info           = array();    
		$info['table']  =  SALES_RETURN_TBL.' sr,'.PRODUCT_TBL.' p,'.CURRENCY_TBL.' c,'.BRAND_TBL.' b';	
		$info['fields'] = array('sr.return_did','sr.voucher_no','sr.project_id','sr.customer_id','sr.in_stock_id','sr.catagory','b.brand_name','sr.product_id',
		'p.product_name','p.product_desc','p.m_unit','sr.product_status','sr.unit_price','sr.discount_amount','sr.return_qty','sr.return_amount','c.curr_symble','sr.net_amount','sr.return_date');
		
		$sql="sr.product_id = p.product_id AND p.brand_code = b.brand_id AND sr.currency = c.currency_id AND sr.voucher_no = '$id'";
		
		$info['where']  = $sql;
	        //$info['groupby'] = array("sr.voucher_no");
		$info['orderby'] = array("sr.product_id asc");
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
  
   function getCurrencyList()
   {
      $info            = array();
      $info['table']   = CURRENCY_TBL;
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
   
   function getAccounceBalance($account_id,$project_id){
	$sql = "SELECT (sum(`dr`) - sum(`cr`)) as balance_amount FROM ".ACCOUNT_JOURNAL_TBL." WHERE product_id = '$product_id' AND project_id = '$project_id'";
	
	$row = mysql_fetch_object(mysql_query($sql));
	$balance_amount = $row->balance_amount;
	if(empty($balance_amount)){
		$balance_amount = 0;
	}
	return $balance_amount;
   }
   function getTotalCreditStock($acc_head,$project_id,$store_id=NULL){
   		$sql = "SELECT sum(`cr`) as credit_amount FROM ".STOCK_LEDGER_TBL." WHERE product_id = '$acc_head' AND project_id = '$project_id' ";
		if($store_id!=""){$sql.=" AND store_id='$store_id'";}
		$row = mysql_fetch_object(mysql_query($sql));
		$credit_amount = $row->credit_amount;
		if(empty($credit_amount)){
			$credit_amount = 0;
		}
		return $credit_amount;
   }
  
   function getTotalDebitStock($acc_head,$project_id,$store_id=NULL){
   		$sql = "SELECT sum(`dr`) as debit_amount FROM ".STOCK_LEDGER_TBL." WHERE product_id = '$acc_head' AND project_id = '$project_id'";
		if($store_id!=""){$sql.=" AND store_id='$store_id'";}
		$row = mysql_fetch_object(mysql_query($sql));
		$debit_amount = $row->debit_amount;
		if(empty($debit_amount)){
			$debit_amount = 0;
		}
		return $debit_amount;

   }
  function saveStockJournal($voucher_no,$pvoucher_no,$project_id,$store_id,$product_id,$product_type,$note,$unit_price=NULL,$m_unit,$DR=NULL,$CR=NULL,$balance,$create_date){
  	$created_by = getFromSession('userid');
	$sql = "INSERT INTO ".STOCK_LEDGER_TBL." (voucher_no,po_no,project_id,store_id,product_id,product_type,note,unit_price,m_unit,dr,cr,balance,created_by,create_date)
	 VALUES('".$voucher_no."','".$pvoucher_no."','".$project_id."','".$store_id."','".$product_id."','".$product_type."','".$note."','".$unit_price."','".$m_unit."','".$DR."','".$CR."','".$balance."','".$created_by."','".$create_date."')";
	mysql_query($sql); 
   } 
  
  function createVoucharID()
   {
      $info = array();
      $info['table']  = SALES_RETURN_MASTER_TBL; 
      $info['fields'] = array('max(voucher_no) as maxvoucher');
      $res = select($info);
      $maxvoucherId = 'R000000000';
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
      
      $maxvoucherId = generateID("R",$maxvoucherId,10);
      return $maxvoucherId;
   }  
      
} // End class
?>
