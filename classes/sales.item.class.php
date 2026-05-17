<?php
class SalesItem
{
   function run()
   {         
      $cmd = getRequest('cmd');
	  $u_t_id = getFromSession('u_type_id');
      if( ($u_t_id ==101) || ($u_t_id == 102) || ($u_t_id == 103)) // 101 = sysadmin, 102 = admin, 103= salesman
      {
      	switch ($cmd)
      	{
      	   case 'add'					: $this->showEditor(); break;
      	   case 'loadProduct'  			: $this->loadProduct4Catagory(trim(getRequest('catagory_id'))); break; 
      	   case 'get_dtl'  				: $this->loadProductDtl(trim(getRequest('product_id'))); break; 
      	   case 'loadPatient'  			: $this->loadPatientbySrc(trim(getRequest('sub_id'))); break;  
      	   case 'save_tmp'  			: $this->saveTempSales(); break;   
		   case 'deltemp'				: $this->delTempSales(); break;  
		   case 'allClear'	  			: $this->deleteAllTempSales(); break;  
		   case 'saveSales'				: $this->saveSalesItem(); break; 
		   case 'print_vouchar'			: $screen = $this->showPrintEditor($msg); break;  
		   case 'delete'             	: $screen = $this->deleteRecord(getRequest('id')); break;
      	   default                   	: $cmd = 'list'; $screen = $this->showEditor();   break;
      	}
      }elseif($u_t_id == 104) // 104 = acc
      {
      	switch ($cmd)
      	{
		   case 'sal_dtl'				: $this->showEditor4SalesDetails(); break;
		   case 'print_vouchar'			: $screen = $this->showPrintEditor($msg); break;
      	   default                   	: $cmd = 'list'; $screen = $this->showEditor();   break;
      	}

      }else {
      	header("location:index.php?app=user_home&msg=You are not authorised !!!");
      }     	          
      if($cmd == 'list') {
       require_once(CURRENT_APP_SKIN_FILE);
      } 
      return true;
   } 
  
   function showPrintEditor($msg = null) {    	  
	  $voucher_no 	= getRequest('voucher_no');  
	  if ($voucher_no) {
         $advArr 			= $this->getSalesMasterInfo($voucher_no);
         $advArr 			= parseThisValue($advArr); 
		 $data   			= array_merge(array(), $advArr); 
		 $data['item_list']	= $this->getProductList($voucher_no);
		 $data['message'] 	= $msg;
		 $data['cmd']     	= getRequest('cmd');
		if(getFromSession('u_type_id')==102){
		 require_once(POS_SALES_VOUCHAR_SKIN);
		}else{
		 require_once(SALES_VOUCHAR_SKIN);
		}
		 return true;
	 }else{
		require_once(PRINT_VOUCHAR_SKIN);
	  }
   }
   function showEditor($msg = null) {
   	   $data                	= array();
       require_once(CLASS_DIR.'/common.list.class.php');	
	   $comListApp 	= new CommonList();	
	   $data['customer_list']   = $comListApp->getAccountHeadList("Customer");
	   $data['patient_list']    = $comListApp->getAccountHeadList("Patient");
	   $data['shareholder_list']= $comListApp->getAccountHeadList("Shareholder");
	   $data['reference_list'] 	= $comListApp->getReferenceList();	      	
	   $data['product_list'] 	= $comListApp->getProductList();	
	   $data['cat_list'] 		= $comListApp->getCatagoryList();	      
	   $data['brand_list'] 		= $comListApp->getBrandList();	  
	   $data['currency_list']   = $comListApp->getCurrencyList();	   	
	   $data['area_list'] 		= $comListApp->getAreaList();      	
	   $data['depo_list'] 		= $comListApp->getDeliveryPointList(true); 
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
		$requestdata = getUserDataSet(TEMP_SALES_ORDER_TBL);
		$project_id  				= getFromSession('project_id');
		$requestdata['project_id'] 		= $project_id;
		$requestdata['customer'] 		= getRequest('customer');
		$requestdata['delivery_point'] 	= getRequest('delivery_point'); $store_id = getRequest('delivery_point');
		$requestdata['sales_date'] 		= formatDate(getRequest('sales_date'));
		$requestdata['currency'] 		= getRequest('currency');
		$requestdata['currencyName'] 	= getRequest('currencyName');
		$requestdata['productid'] 		= getRequest('productid');
		$sql 		= "SELECT product_name,catagory,brand_code,m_unit FROM ".PRODUCT_TBL." WHERE product_id = '".$requestdata['productid']."'";
		$row 		= mysql_fetch_object(mysql_query($sql));
		$requestdata['product_name'] 	= $row->product_name;	
		$requestdata['catagory'] 		= $row->catagory;		
		$requestdata['catagoryname'] 	= getRequest('catagoryname');
		$requestdata['brand_id'] 		= $row->brand_code;	
		$requestdata['brandname'] 		= getRequest('brandname');
		$requestdata['details'] 		= getRequest('details');
		$requestdata['munit'] 			= $row->m_unit;
		$requestdata['stock_qty'] 		= getRequest('stock_qty');
		$requestdata['qty'] 			= getRequest('qty');
		$requestdata['free_qty'] 		= getRequest('free_qty');
		$requestdata['unit_price'] 		= getRequest('unit_price');
		$requestdata['unit_discount'] 	= getRequest('unit_discount');
		$requestdata['discount_amount'] = getRequest('discount_amount');
		$requestdata['total'] 			= getRequest('total');
		$product_id = getRequest('productid');
		$requestdata['created_by'] 		= getFromSession('userid');		
		$info        		=  array();
		$info['table']	= TEMP_SALES_ORDER_TBL;
		$info['data'] 	= $requestdata;     
		//$info['debug']  	=  true;
		$stockQty = $this->loadStockQty($product_id); 
		$totalOrderQty = $this->getTotalOrderQty($product_id,$store_id);
		if($stockQty>=$totalOrderQty && getRequest('qty')>0 ){
		$res = insert($info);
		}
		$str1="<table width='100%' align='center' bgcolor='#99CC66'>
		<tr style='color:#fff;' bgcolor='#007DFB' height=28>
		  <td width='1%' nowrap><div align='left'>SL</div></td>
		  <td width='30%' nowrap><div align='left'>Product Name</div></td>
		  <td width='10%' nowrap><div align='left'>Catagory</div></td>
		  <td width='10%' nowrap><div align='left'>Brand</div></td>
		  <td width='8%' nowrap><div align='right'>Sales Qty</div></td>
		  <td width='8%' nowrap><div align='right'>Free Qty</div></td>
		  <td width='8%' nowrap><div align='right'>Rate</div></td>
		  <td width='8%' nowrap><div align='center'>Discount %</div></td>		  
		  <td width='10%' nowrap><div align='right'>Amount</div></td>				  
		  <td width='7%' nowrap align='center'>Option</td>
		</tr>";
		$total_value = 0; $product_discount = 0; $sl=1; $TotalQty=0; $TotalFreeQty=0;
		$getSql		= "SELECT * FROM ".TEMP_SALES_ORDER_TBL." WHERE created_by = '".getFromSession('userid')."' AND project_id='".$project_id."' ORDER BY `tmp_id` ASC";
		$gres 		= mysql_query($getSql);
		while($row = mysql_fetch_array($gres)){
		extract($row);
		$total_value+=$total; $product_discount+=$discount_amount; $TotalQty+=$qty; $TotalFreeQty+=$free_qty;
		$str2.="
		<tr style='color:#000000' bgcolor='#fff'>
		  <td width='1%' nowrap align='left'>$sl</td>
		  <td width='30%' nowrap align='left'>$product_name</td>
		  <td width='10%' nowrap align='left'>$catagoryname</td>
		  <td width='10%' nowrap align='left'>$brandname</td>
		  <td width='8%' nowrap><div align='right'>$qty $munit</div></td>
		  <td width='8%' nowrap><div align='right'>$free_qty $munit</div></td>
		  <td width='8%' nowrap align='right'>$unit_price $currencyName</td>				  
		  <td width='8%' nowrap align='center'>$unit_discount %</td>			  
		  <td width='10%' nowrap align='right'>$total</td>				  				  
		  <td width='7%' nowrap align='center'><a href=\"?app=sales.item&cmd=deltemp&id=$tmp_id\"><img src=\"images/common/icons/delete.gif\"></a></td>
		</tr>";  $sl++;
		}
		$str3="
		<tr style='color:#000;' bgcolor='#CCCCCC' height=25>
		  <td colspan='4' nowrap><div align='right'>Total </div></td>
		  <td nowrap align='right'>$TotalQty $munit</td>
		  <td nowrap align='right'>$TotalFreeQty $munit</td>
		  <td nowrap>&nbsp;</td>
		  <td nowrap>&nbsp;</td>
		  <td nowrap align='right'>$total_value $currencyName</td>
		  <td nowrap align='center'>&nbsp;</td>
		</tr>
		</table>";
		echo $str1.$str2.$str3."####-@@@@".$total_value."####-@@@@".$product_discount;
	}
	function delTempSales(){
		$tmp_id = $_REQUEST['id'];
		if($tmp_id!=""){
		 $dsql = "DELETE FROM ".TEMP_SALES_ORDER_TBL." WHERE tmp_id ='".$tmp_id."'";
		 mysql_query($dsql);
		}		
		header("location:?app=sales.item");
	}
	function deleteAllTempSales(){
	 $project_id  	= getFromSession('project_id');
	 $dsql = "DELETE FROM ".TEMP_SALES_ORDER_TBL." WHERE created_by = '".getFromSession('userid')."' AND project_id='".$project_id."'";
	 mysql_query($dsql);
	 header("location:?app=sales.item"); 		
	}
	function getTempSales(){
		$project_id  	= getFromSession('project_id');
		$str1="<table width='100%' align='center' bgcolor='#99CC66'>
		<tr style='color:#fff;' bgcolor='#007DFB' height=28>
		  <td width='1%' nowrap><div align='left'>SL</div></td>
		  <td width='30%' nowrap><div align='left'>Product Name</div></td>
		  <td width='10%' nowrap><div align='left'>Catagory</div></td>
		  <td width='10%' nowrap><div align='left'>Brand</div></td>
		  <td width='8%' nowrap><div align='right'>Sales Qty</div></td>
		  <td width='8%' nowrap><div align='right'>Free Qty</div></td>
		  <td width='8%' nowrap><div align='right'>Rate</div></td>
		  <td width='8%' nowrap><div align='center'>Discount %</div></td>		  
		  <td width='10%' nowrap><div align='right'>Amount</div></td>				  
		  <td width='7%' nowrap align='center'>Option</td>
		</tr>";
		$total_value = 0; $product_discount=0; $TotalQty=0; $TotalFreeQty=0; $sl=1;
		$getSql		= "SELECT * FROM ".TEMP_SALES_ORDER_TBL." WHERE created_by = '".getFromSession('userid')."' AND project_id='".$project_id."' ORDER BY `tmp_id` ASC";
		$gres 		= mysql_query($getSql);
		while($row = mysql_fetch_array($gres)){
		extract($row);
		$total_value+=$total; $product_discount+=$discount_amount; $TotalQty+=$qty; $TotalFreeQty+=$free_qty;
		$str2.="
		<tr style='color:#000000' bgcolor='#fff'>
		  <td width='1%' nowrap>$sl</td>
		  <td width='30%' nowrap align='left'>$product_name</td>
		  <td width='10%' nowrap align='left'>$catagoryname</td>
		  <td width='10%' nowrap align='left'>$brandname</td>
		  <td width='8%' nowrap><div align='right'>$qty $munit</div></td>
		  <td width='8%' nowrap><div align='right'>$free_qty $munit</div></td>
		  <td width='8%' nowrap><div align='right'>$unit_price $currencyName</div></td>				  
		  <td width='8%' nowrap><div align='center'>$unit_discount %</div></td>			  
		  <td width='10%' nowrap><div align='right'>$total</div></td>				  				  
		  <td width='7%' nowrap align='center'><a href=\"?app=sales.item&cmd=deltemp&id=$tmp_id\"><img src=\"images/common/icons/delete.gif\"></a></td>
		</tr>"; $sl++;
		}
		$str3="
		<tr style='color:#000;' bgcolor='#CCCCCC' height=25>
		  <td colspan='4' nowrap><div align='right'>Total </div></td>
		  <td nowrap align='right'>$TotalQty $munit</td>
		  <td nowrap align='right'>$TotalFreeQty $munit</td>
		  <td nowrap>&nbsp;</td>
		  <td nowrap>&nbsp;</td>
		  <td nowrap align='right'>$total_value $currencyName </td>
		  <td nowrap align='center'>&nbsp;</td>
		</tr>
		</table>";
		$total_salesStr = $str1.$str2.$str3."####-@@@@".$total_value."####-@@@@".$product_discount;
		return $total_salesStr;
	}
  //====== End Save Sales =====
  
  function saveSalesItem(){
	mysql_query("START TRANSACTION;");	
	$store_id   = getRequest('delivery_point');
	$voucher_no = $this->saveDebitVouchar();
	if($voucher_no!="" && $store_id!=""){
	$PreviousPartyBalance = $this->saveCreditVouchar($voucher_no);
	$this->insertSalesMaster($voucher_no,$PreviousPartyBalance);
	$this->insertSalesDetails($voucher_no); 	
	}else{
	mysql_query("ROLLBACK;");	
	header("location:index.php?app=sales.item&cmd=add");
	}
  }
  
  function insertSalesDetails($voucher_no,$dm_id)
  {
	require_once(CLASS_DIR.'/common.list.class.php');	
	$comlistApp 				= new CommonList();
	require_once(CLASS_DIR.'/sales_order.class.php');	
	$soApp 			= new SalesOrder();
		
	$customer 		= getRequest('customer');
	$reference 		= getRequest('reference');
	$delivery_point 	= getRequest('delivery_point');
	$store_id 		= getRequest('delivery_point');
	$discount 		= getRequest('discount');
	$orderValue 		= getRequest('total_value');
	$overall_discount = (($discount/$orderValue)*100);
	$project_id 		= getFromSession('project_id');
	$created_by 		= getFromSession('userid');
	$totalDeliveryAmount = 0; $totalDeliveryQty = 0; $j=1;	
	
	$getSql	= "SELECT * FROM ".TEMP_SALES_ORDER_TBL." WHERE created_by = '".getFromSession('userid')."' AND project_id='".$project_id."' ORDER BY `tmp_id` ASC";
	$gres 	= mysql_query($getSql);
	if(mysql_num_rows($gres)>0){
	while($row = mysql_fetch_object($gres)){
	$catagory 			= $row->catagory;       	  
	$brand_id 			= $row->brand_id;
	$product_id			= $row->productid;		
	$serial = $row->serial; $warranty=$row->warranty;      	  
	$discount_per_qty 	= $row->unit_discount;
	$details 			= $row->details;   	  
	$sales_price 		= $row->unit_price;       	  
	$qty 				= $row->qty;  	 
	$free_qty 			= $row->free_qty; 	      	  
	$m_unit				= $row->munit;       	  
	$total 				= $row->total;
	$discount_amount 	= (($row->unit_price/100)*$discount_per_qty);		
	$overall_discount	= (($discount/$orderValue)*100);	
	$sales_date			= $row->sales_date;
	$UDQty  		 	= ($qty+$free_qty);
	$PDAmount=0; $PDQty=0; $ProductFreeQty=0; $pvoucher_no=""; $avgpo_price=0; $avgUnitprofit=0;
	if($UDQty>0){
	$Pcsql="SELECT product_type,product_catagory FROM ".PRODUCT_TBL." WHERE product_id='$product_id' AND project_id='$project_id'";
	$Pcrow = mysql_fetch_object(mysql_query($Pcsql));
	$product_catagory 	= $Pcrow->product_catagory;
	$product_type 		= $Pcrow->product_type;				
	$PuSql="SELECT pur_detail_id,voucher_no,serial,warranty,unit_price,sales_qty,stock_qty FROM ".PURCHASE_ITEM_VIEW." 
	WHERE product='$product_id' AND brand_id='$brand_id' AND project_id='$project_id' AND stock_qty >0 ORDER BY pur_detail_id ASC"; 
	$pres = mysql_query($PuSql);
	if(mysql_num_rows($pres)>0){	 
	while($Purow = mysql_fetch_object($pres)){
		$pur_detail_id  = $Purow->pur_detail_id;
		if($pvoucher_no!=""){$pvoucher_no.= ",".$Purow->voucher_no;}else{ $pvoucher_no = $Purow->voucher_no; }
		$sales_qty=$Purow->sales_qty;
		$purchase_price = $Purow->unit_price; $stockQty=$Purow->stock_qty;
		if($avgpo_price==0){$avgpo_price = $purchase_price;}else{$avgpo_price = (($avgpo_price+$purchase_price)/2);}
		$serial=$Purow->serial; $warranty=$Purow->warranty;
				
		if($stockQty >= $UDQty){
			$TTLSalesQty    = ($sales_qty+$UDQty);			
			$unit_profit    = ($sales_price-$purchase_price);
			$overall_discount_amount = (($sales_price/100)*$overall_discount);
			$unit_profit    = ($unit_profit-$overall_discount_amount);
			$deliveryAmount	= (($sales_price-$overall_discount_amount)*$qty);
			if($avgUnitprofit==0){$avgUnitprofit = $unit_profit;}else{$avgUnitprofit = (($avgUnitprofit+$unit_profit)/2);}					
			$pdusql="UPDATE ".PURCHASE_DETAILS_TBL." SET sales_qty='".$TTLSalesQty."' WHERE pur_detail_id='$pur_detail_id'";
			$pures = mysql_query($pdusql);
			if($pures){
			$totalDeliveryAmount+=$deliveryAmount; $PDAmount+=$deliveryAmount; $PDQty+=$qty; $ProductFreeQty+=$free_qty;							
			$this->saveProfit($voucher_no,$product_id,$unit_profit,$qty,$free_qty,$sales_price,$purchase_price,$sales_date);			
			//=== update stock ===
			$totalCR  = $this->getTotalCreditStock($product_id,getFromSession('project_id'));
			$totalDR  = $this->getTotalDebitStock($product_id,getFromSession('project_id'));					 
			$balance  = ($totalDR - ($totalCR+$UDQty));	
			if($product_type=="Sales Item" || $product_type=="Raw Materials"){	
			if($overall_discount_amount>0){ $netSalesPrice = $sales_price-$overall_discount_amount; }else{$netSalesPrice = $sales_price;}
			$note = "Sales Product";
			$this->saveStockJournal($pvoucher_no,$voucher_no,$project_id,$store_id,$product_id,$serial,$warranty,$note,$netSalesPrice,$m_unit,0,$UDQty,$balance,$sales_date);			
			}
			//=== Stock Cr =====
			$StockAmount = ($purchase_price*$UDQty);
			$StockId 	 = $comlistApp->getStockId(getFromSession('project_id'));
			$totalStockCr  = $this->getTotalCreditAmount($StockId,getFromSession('project_id'));
			$totalStockDr  = $this->getTotalDebitAmount($StockId,getFromSession('project_id'));					 
			$StockBalance  = ($totalStockDr-($totalStockCr+$StockAmount));	$description = "Sales Product, Qty- $UDQty $m_unit";				 
			$comlistApp->saveAccJournal($voucher_no,$StockId,"Stock","Sales Product",$project_id,$description,0,$StockAmount,$StockBalance,0,$sales_date,$sl_id);
			
			if($free_qty>0){
			$DrAmount = ($purchase_price*$free_qty);
			$description = "Gives free product with sales item";
			//========= Capital Cr ==========
			$capital_head 	 = $comlistApp->getMainCapitalId(getFromSession('project_id'));
			$totalCapitalCR  = $this->getTotalCreditAmount($capital_head,getFromSession('project_id'));
			$totalCapitalDR  = $this->getTotalDebitAmount($capital_head,getFromSession('project_id'));					 
			$Capitalbalance  = ($totalCapitalDR-($totalCapitalCR+$DrAmount));					 
			//$comlistApp->saveAccJournal($voucher_no,$capital_head,"Capital","Free Product",$project_id,$description,0,$DrAmount,$Capitalbalance,0,$sales_date,$sl_id);
			//========= Administrative Cost Dr ==========
			$freeItemhead 	 = $comlistApp->getAdvCostFreeItemId(getFromSession('project_id'));
			$totalfreeItemCR = $this->getTotalCreditAmount($freeItemhead,getFromSession('project_id'));
			$totalfreeItemDR = $this->getTotalDebitAmount($freeItemhead,getFromSession('project_id'));					 
			$freeItemBalance = (($totalfreeItemDR+$DrAmount)-$totalfreeItemCR);					 
			$comlistApp->saveAccJournal($voucher_no,$freeItemhead,"Acc","Free Product",$project_id,$description,$DrAmount,0,$freeItemBalance,0,$sales_date,$sl_id);
			}
			$free_qty=0; $UDQty=0;
			} // update PO
			break;
		}elseif(($stockQty < $UDQty) && ($UDQty >0)){	
						
			if(($stockQty >= $free_qty) && ($free_qty >=0)){
				$UDQty = $UDQty - $stockQty;									
				$NDQty = $stockQty-$free_qty; // NDQty is Now Delivery Qty
				$qty	= $qty-$NDQty; 								
				$TTLSalesQty    = ($sales_qty+$stockQty);			
				$unit_profit    = ($sales_price-$purchase_price);
				$overall_discount_amount = (($sales_price/100)*$overall_discount);
				$unit_profit    = ($unit_profit-$overall_discount_amount);					
				if($avgUnitprofit==0){$avgUnitprofit = $unit_profit;}else{$avgUnitprofit = (($avgUnitprofit+$unit_profit)/2);}
				$pdusql="UPDATE ".PURCHASE_DETAILS_TBL." SET sales_qty='".$TTLSalesQty."' WHERE pur_detail_id='$pur_detail_id'";
				$pdres = mysql_query($pdusql);
				if($pdres){
					if($NDQty>0){ $deliveryAmount	= (($sales_price-$overall_discount_amount)*$NDQty);
					}else{$discount_amount=0;$total=0;$deliveryAmount=0;$unit_profit=0; $NDQty=0;}						
					$totalDeliveryAmount+=$deliveryAmount; $PDAmount+=$deliveryAmount; $PDQty+=$NDQty; $ProductFreeQty+=$free_qty;					
					$this->saveProfit($voucher_no,$product_id,$unit_profit,$NDQty,$free_qty,$sales_price,$purchase_price,$sales_date);					
					//=== update stock ===
					$totalCR  = $this->getTotalCreditStock($product_id,getFromSession('project_id'));
					$totalDR  = $this->getTotalDebitStock($product_id,getFromSession('project_id'));					 
					$balance  = ($totalDR - ($totalCR+$stockQty));	
					if($product_type=="Sales Item" || $product_type=="Raw Materials"){	
					if($overall_discount_amount>0){ $netSalesPrice = $sales_price-$overall_discount_amount; }else{$netSalesPrice = $sales_price;}
					$note = "Sales Product";
					$this->saveStockJournal($pvoucher_no,$voucher_no,$project_id,$store_id,$product_id,$serial,$warranty,$note,$netSalesPrice,$m_unit,0,$stockQty,$balance,$sales_date);			
					}
					//=== Stock Cr =====
					$StockAmount = ($purchase_price*$stockQty);
					$StockId 	 = $comlistApp->getStockId(getFromSession('project_id'));
					$totalStockCr  = $this->getTotalCreditAmount($StockId,getFromSession('project_id'));
					$totalStockDr  = $this->getTotalDebitAmount($StockId,getFromSession('project_id'));					 
					$StockBalance  = ($totalStockDr-($totalStockCr+$StockAmount));	$description = "Sales Product, Qty- $stockQty $m_unit";		 
					$comlistApp->saveAccJournal($voucher_no,$StockId,"Stock","Sales Product",$project_id,$description,0,$StockAmount,$StockBalance,0,$sales_date);				
										
					if($free_qty>0){
					$DrAmount = ($purchase_price*$free_qty);
					$description = "Gives free product with sales item";
					//========= Capital Cr ==========
					$capital_head 	 = $comlistApp->getMainCapitalId(getFromSession('project_id'));
					$totalCapitalCR  = $this->getTotalCreditAmount($capital_head,getFromSession('project_id'));
					$totalCapitalDR  = $this->getTotalDebitAmount($capital_head,getFromSession('project_id'));					 
					$Capitalbalance  = ($totalCapitalDR-($totalCapitalCR+$DrAmount));					 
					//$comlistApp->saveAccJournal($voucher_no,$capital_head,"Acc","Free Product",$project_id,$description,0,$DrAmount,$Capitalbalance,0,$sales_date);
					//========= Administrative Cost Dr ==========
					$freeItemhead 	 = $comlistApp->getAdvCostFreeItemId(getFromSession('project_id'));
					$totalfreeItemCR = $this->getTotalCreditAmount($freeItemhead,getFromSession('project_id'));
					$totalfreeItemDR = $this->getTotalDebitAmount($freeItemhead,getFromSession('project_id'));					 
					$freeItemBalance = (($totalfreeItemDR+$DrAmount)-$totalfreeItemCR);					 
					$comlistApp->saveAccJournal($voucher_no,$freeItemhead,"Acc","Free Product",$project_id,$description,$DrAmount,0,$freeItemBalance,0,$sales_date);
					}// save free item
					$free_qty=0; $NDQty=0;
				} // update pursaves qty
			}
			elseif($stockQty < $free_qty && $free_qty >0 && $stockQty >0){
				$UDQty = $UDQty - $stockQty;									
				$NDQty = 0; // NDQty is Now Delivery Qty
				$free_qty	= $free_qty-$stockQty; 								
				$TTLSalesQty    = ($sales_qty+$stockQty);			
				$unit_profit    = ($sales_price-$purchase_price);
				$overall_discount_amount = (($sales_price/100)*$overall_discount);
				$unit_profit    = ($unit_profit-$overall_discount_amount);					
				
				$pdusql="UPDATE ".PURCHASE_DETAILS_TBL." SET sales_qty='".$TTLSalesQty."' WHERE pur_detail_id='$pur_detail_id'";
				$pdres = mysql_query($pdusql);
				if($pdres){						
					$ProductFreeQty+=$stockQty;								
					//=== update stock ===
					$totalCR  = $this->getTotalCreditStock($product_id,getFromSession('project_id'));
					$totalDR  = $this->getTotalDebitStock($product_id,getFromSession('project_id'));					 
					$balance  = ($totalDR - ($totalCR+$stockQty));	
					if($product_type=="Sales Item" || $product_type=="Raw Materials"){	
					if($overall_discount_amount>0){ $netSalesPrice = $sales_price-$overall_discount_amount; }else{$netSalesPrice = $sales_price;}
					$note = "Sales Product";
					$this->saveStockJournal($pvoucher_no,$voucher_no,$project_id,$store_id,$product_id,$serial,$warranty,$note,$netSalesPrice,$m_unit,0,$stockQty,$balance,$sales_date);			
					}
					//=== Stock Cr =====
					$StockAmount = ($purchase_price*$stockQty);
					$StockId 	 = $comlistApp->getStockId(getFromSession('project_id'));
					$totalStockCr  = $this->getTotalCreditAmount($StockId,getFromSession('project_id'));
					$totalStockDr  = $this->getTotalDebitAmount($StockId,getFromSession('project_id'));					 
					$StockBalance  = ($totalStockDr-($totalStockCr+$StockAmount));	$description = "Sales Product, Qty- $stockQty $m_unit";		 
					$comlistApp->saveAccJournal($voucher_no,$StockId,"Stock","Sales Product",$project_id,$description,0,$StockAmount,$StockBalance,0,$sales_date);				
										
					$DrAmount = ($purchase_price*$stockQty);
					$description = "Gives free product with sales item";
					//========= Capital Cr ==========
					$capital_head 	 = $comlistApp->getMainCapitalId(getFromSession('project_id'));
					$totalCapitalCR  = $this->getTotalCreditAmount($capital_head,getFromSession('project_id'));
					$totalCapitalDR  = $this->getTotalDebitAmount($capital_head,getFromSession('project_id'));					 
					$Capitalbalance  = ($totalCapitalDR-($totalCapitalCR+$DrAmount));					 
					//$comlistApp->saveAccJournal($voucher_no,$capital_head,"Acc","Free Product",$project_id,$description,0,$DrAmount,$Capitalbalance,0,$sales_date);
					//========= Administrative Cost Dr ==========
					$freeItemhead 	 = $comlistApp->getAdvCostFreeItemId(getFromSession('project_id'));
					$totalfreeItemCR = $this->getTotalCreditAmount($freeItemhead,getFromSession('project_id'));
					$totalfreeItemDR = $this->getTotalDebitAmount($freeItemhead,getFromSession('project_id'));					 
					$freeItemBalance = (($totalfreeItemDR+$DrAmount)-$totalfreeItemCR);					 
					$comlistApp->saveAccJournal($voucher_no,$freeItemhead,"Acc","Free Product",$project_id,$description,$DrAmount,0,$freeItemBalance,0,$sales_date);					
					$stockQty=0; 
				} // update pursaves qty
			} //end free qty delivery	
					
		}//end stock qty small		
	}// end while
	
	// === Save Sales Delivery Qty ===	
	$this->saveSalesItems($voucher_no,$pvoucher_no,$project_id,$customer,$reference,$catagory,$brand_id,$product_id,$details,$serial,$warranty,$m_unit,$sales_price,$avgpo_price,$discount_per_qty,$discount_amount,$avgUnitprofit,$qty,$PDQty,$PDAmount,$ProductFreeQty,$total,$created_by);
	$qty=0;	$PDQty=0; $avgpo_price=0; $pvoucher_no=""; $ProductFreeQty=0;
			
	}// end stock num	
	
	}// UDQty qty > 0 
			
	}// end temp while
	}//end temp num if
	$project_id = getFromSession('project_id'); $created_date = $sales_date; 	
	$NetReceivable = getRequest('net_payble');
	$general_discount_amount   	= getRequest('general_discount_amount');
	$exclusive_discount_amount 	= getRequest('exclusive_discount_amount');
	$additional_discount 		= getRequest('additional_discount');
	$TotalNetReceivable=($NetReceivable+$general_discount_amount+$exclusive_discount_amount+$additional_discount);
	
	if(intval($totalDeliveryAmount) != intval($TotalNetReceivable)){ 
		if(intval($totalDeliveryAmount) >= intval($TotalNetReceivable)){ 
			$diffAmount = ($totalDeliveryAmount - $TotalNetReceivable);
		}else{
			$diffAmount = ($TotalNetReceivable - $totalDeliveryAmount);	
		}
		$product_discount	= (getRequest('discount') + $diffAmount);	
		$updateSM="UPDATE ".SALES_MASTER_TBL." SET product_discount='$product_discount' WHERE voucher_no='".$voucher_no."' AND project_id = '$project_id'";
		mysql_query($updateSM);
		$totalDeliveryAmount = $TotalNetReceivable;
	}
	
	if(intval($totalDeliveryAmount) == intval($TotalNetReceivable)){
	 $dsql = "DELETE FROM ".TEMP_SALES_ORDER_TBL." WHERE created_by = '".getFromSession('userid')."' AND project_id='".$project_id."'";
	 mysql_query($dsql); 			 	
	 mysql_query("COMMIT;");
	 header("location:index.php?app=sales&cmd=print_vouchar&voucher_no=".$voucher_no);
	}elseif(intval($totalDeliveryAmount) != intval($TotalNetReceivable)){ 
	 //=======Update Sales Master =====	
	 //$this->updateSalesVoucher($voucher_no,$total_delivery_amount);
	 mysql_query("ROLLBACK;");
	 $Stsql="DELETE FROM ".STOCK_LEDGER_TBL." WHERE voucher_no='".$voucher_no."' AND project_id='".$project_id."'";
	 mysql_query($Stsql); 
	 header("location:index.php?app=sales.item&cmd=add");
	}
	
  } //End of the function saveSalesDetails()
  function insertSalesMaster($voucher_no,$PreviousPartyBalance){
	  $requestdata = array();
	  $project_id  = getFromSession('project_id');	$customer = getRequest('customer');
	  $requestdata = getUserDataSet(SALES_MASTER_TBL);	
	  if($mode_of_payment =="Check"){
		$requestdata['check_no'] = formatDate(getRequest('check_no'));
		$requestdata['check_issue_date'] = formatDate(getRequest('check_issue_date'));
	  }
	  $requestdata['transaction_type']  = "Received";  
	  $requestdata['item_delivery_amount'] = $requestdata['net_payble']; 
	  if(getRequest('reference')!=""){
	  $reference_id = getRequest('reference');
	  //======== Sales Commission ============
	  $cSql="SELECT * FROM ".COMMISSION_SLOT_TBL." WHERE cid=1 AND project_id='$project_id'";
	  $crow = mysql_fetch_object(mysql_query($cSql));
	  if($requestdata['net_payble']<=$crow->slot_range1){
	  $commission_slot = $crow->slot1;
	  }elseif($requestdata['net_payble']<=$crow->slot_range2){
	  $commission_slot = $crow->slot2;
	  }elseif($requestdata['net_payble']<=$crow->slot_range3){
	  $commission_slot = $crow->slot3;
	  }elseif($requestdata['net_payble']<=$crow->slot_range4){
	  $commission_slot = $crow->slot4;
	  }
	  $total_commission = (($requestdata['net_payble']/100)*$commission_slot); 
	  $commission_due 	= $total_commission;
	  saveSalesCommission($voucher_no,$requestdata['net_payble'],$reference_id,$commission_slot,$total_commission,$commission_due,"Sales");
	  }
	  if($PreviousPartyBalance<0){
		  $dueAmount 	= $requestdata['item_delivery_amount']-$requestdata['paid_amount'];
		  if($dueAmount>0){
		  $restofAmount	= $this->saveAdjustCustomerPayble($voucher_no,getRequest('customer'),$dueAmount); 
		  $adjust_Amount = ($dueAmount-$restofAmount);
		  $adjustAmount = "-$adjust_Amount";
		  $requestdata['due'] = ($requestdata['net_payble']-($requestdata['paid_amount']+$adjust_Amount));
		  }else{
		  $adjustAmount = "0";
		  }
	  }else{
	   $adjustAmount = $PreviousPartyBalance;	 
	  }
	  $requestdata['previour_balance']= $PreviousPartyBalance;
	  $customer    = getRequest('customer');
	  $CSql="SELECT district,area FROM ".SUB_ACC_HEAD_TBL." WHERE head_type='Customer' AND sub_id='$customer' AND project_id='$project_id' ";
	  $Crow = mysql_fetch_object(mysql_query($CSql));
	  $requestdata['district'] = $Crow->district;
	  $requestdata['area']	    = $Crow->area;
	  $requestdata['sales_date'] 		= formatDate(getRequest('sales_date')); 
	  $general_discount_amount			= getRequest('general_discount_amount'); 
	  $exclusive_discount_amount		= getRequest('exclusive_discount_amount'); 
	  $additional_discount				= getRequest('additional_discount');
	  $product_discount					= getRequest('discount');
	  $requestdata['product_discount']  = $product_discount;
	  $requestdata['discount']  		= ($product_discount+$general_discount_amount+$exclusive_discount_amount+$additional_discount);
	  $requestdata['voucher_no']        = $voucher_no;   
	  $requestdata['project_id']        = getFromSession('project_id');    
	  $requestdata['created_by']        = getFromSession('userid');	
	  $requestdata['created_date']      = date('Y-m-d h:i:s');
	  mt_srand($this->make_seed());
	  $gatepass = mt_rand();   
	  $requestdata['gate_pass']        = $gatepass;	 $sales_amount = $requestdata['net_payble'];
	  $info        		=  array();
	  $info['table']	= SALES_MASTER_TBL;
	  $info['data'] 	= $requestdata;  
	  //$info['debug']  	= true;   
	  $res = insert($info);
	  if($res){
	  $sales_date  = formatDate(getRequest('sales_date')); $created_by = getFromSession('userid');
	  if($voucher_no!="" && $sales_amount>0){
		$received_amount = getRequest('paid_amount');
		if($received_amount >0){
		//insertSalesCollection($voucher_no,$voucher_no,$customer,$received_amount,$sales_date,$details);
		}
		$sqlSM="INSERT INTO ".SALES_DELIVERY_MASTER_TBL."(voucher_no,project_id,customer,challan_no,delivery_point,delivery_date,
		total_value,created_by,created_date) VALUES('$voucher_no','$project_id','$customer','0','D0010','$sales_date',
		'$sales_amount','$created_by','$sales_date')";
		mysql_query($sqlSM);
		}
	  }
  }
  
  function updateSalesVoucher($voucher_no,$totalDeliveryAmount){
    $project_id = getFromSession('project_id');
  	$DrVUpdate="UPDATE ".DEVIT_VOUCHAR_TBL." SET debit='$totalDeliveryAmount' WHERE voucher_no='".$voucher_no."' AND project_id = '$project_id'";
	 mysql_query($DrVUpdate);	 
	 $CrVUpdate="UPDATE ".CREDIT_VOUCHAR_TBL." SET credit='$totalDeliveryAmount' WHERE voucher_no='".$voucher_no."' AND project_id = '$project_id'";
	 mysql_query($CrVUpdate);
	  $JlUpdate="UPDATE ".ACCOUNT_JOURNAL_TBL." SET dr='$totalDeliveryAmount' WHERE voucher_no='".$voucher_no."' AND project_id = '$project_id'";
	 mysql_query($JlUpdate);
  }
   
  function saveSalesItems($voucher_no,$pvoucher_no,$project_id,$customer,$reference,$catagory,$brand_id,$product,$details,$serial,$warranty,$m_unit,$unit_price,$purchase_price,$discount_per_qty,$discount_amount,$unit_profit,$qty,$delivery_qty,$delivery_amount,$free_qty,$total,$created_by){
		
	$sql = "INSERT INTO ".SALES_DETAILS_TBL." (voucher_no,pvoucher_no,project_id,customer,reference,catagory,brand_id,product,details,serial,warranty,m_unit,unit_price,purchase_price,discount_per_qty,discount_amount,unit_profit,qty,delivery_qty,delivery_amount,free_qty,total,created_by) 
	VALUES('".$voucher_no."','".$pvoucher_no."','".$project_id."','".$customer."','".$reference."','".$catagory."','".$brand_id."','".$product."','".$details."','".$serial."','".$warranty."','".$m_unit."','".$unit_price."','".$purchase_price."','".$discount_per_qty."','".$discount_amount."','".$unit_profit."','".$qty."','".$delivery_qty."','".$delivery_amount."','".$free_qty."','".$total."','".$created_by."')";
	mysql_query($sql);
  }
	
  function saveProfit($voucher_no,$product,$unit_profit,$deliveryQty,$freeQty,$sales_price,$purchase_price,$created_date){
  	require_once(CLASS_DIR.'/common.list.class.php');	
	$comlistApp = new CommonList();
	$salesQty 	= ($deliveryQty-$freeQty);
	if($unit_profit>0 && $salesQty>0){
	$totalProfite = ($salesQty*$unit_profit);	
	//========= Direct Income Dr ==========
	$SalesIncomeId 	 = $comlistApp->getProductSalesIncomeId(getFromSession('project_id'));
	$totalSalesIncomeCR = $this->getTotalCreditAmount($SalesIncomeId,getFromSession('project_id'));
	$totalSalesIncomeDR = $this->getTotalDebitAmount($SalesIncomeId,getFromSession('project_id'));					 
	$SalesIncomeBalance = (($totalSalesIncomeDR+$totalProfite)-$totalSalesIncomeCR);	
	$salesDtl = "Income from product($product) sales, voucher no- $voucher_no";			 
	$comlistApp->saveAccJournal($voucher_no,$SalesIncomeId,"Acc","Direct Incomes",getFromSession('project_id'),$salesDtl,$totalProfite,0,$SalesIncomeBalance,0,$created_date);
	}elseif($unit_profit<0 && $salesQty>0){
	$totalProfite = ($salesQty*$unit_profit);	
	$totalProfite = abs($totalProfite);
	//========= Direct Income Cr ==========
	$SalesIncomeId 	 = $comlistApp->getProductSalesIncomeId(getFromSession('project_id'));
	$totalSalesIncomeCR = $this->getTotalCreditAmount($SalesIncomeId,getFromSession('project_id'));
	$totalSalesIncomeDR = $this->getTotalDebitAmount($SalesIncomeId,getFromSession('project_id'));					 
	$SalesIncomeBalance = ($totalSalesIncomeDR-($totalSalesIncomeCR+$totalProfite));	
	$salesDtl = "loss from product($product) sales, voucher no- $voucher_no";				 
	$comlistApp->saveAccJournal($voucher_no,$SalesIncomeId,"Acc","Direct Loss",getFromSession('project_id'),$salesDtl,0,$totalProfite,$SalesIncomeBalance,0,$created_date);
	}
  }
   
 //======== saveDebitVouchar =========
  function saveDebitVouchar()
  {     
	  $mode_of_payment = getRequest('mode_of_payment');
	  $requestdata = array();
	  $requestdata = getUserDataSet(DEVIT_VOUCHAR_TBL);	
	  if($mode_of_payment =="Check"){
		$requestdata['bank_name'] 			= getRequest('bank_name');
		$requestdata['acc_no'] 				= getRequest('acc_no');
		$requestdata['check_no'] 			= getRequest('check_no');
		$requestdata['check_issue_date'] 	= formatDate(getRequest('check_issue_date'));
		$requestdata['account_head']     	= getRequest('acc_no'); 
		$requestdata['debit']        		= getRequest('paid_amount');  
		$requestdata['credit']        		=  0;  
		$requestdata['head_type']     		= "Check";   
	  }elseif($mode_of_payment=="Cash"){
		$requestdata['bank_name'] = "";
		$requestdata['acc_no'] = "";
		$requestdata['check_no'] = "";
		$requestdata['check_issue_date']    = "";
		if(getFromSession('u_type_id')==102){ // 102 = Pharmecy
		$requestdata['account_head']     	= getFromSession('cash_id'); 	
		}else{
		$requestdata['account_head']     	= $this->getCashId(getFromSession('project_id')); 
		}
		$requestdata['debit']        		= getRequest('paid_amount'); 
		$requestdata['credit']        		= 0;     
		$requestdata['head_type']     		= "Cash";   
	  }elseif($mode_of_payment=="Recievable"){
		$requestdata['bank_name'] = "";
		$requestdata['acc_no'] = "";
		$requestdata['check_no'] = "";
		$requestdata['check_issue_date'] = "";
		$requestdata['account_head']     	= getRequest('customer');  
		$requestdata['debit']        		= getRequest('due'); 
		$requestdata['credit']        		= 0;     
		$requestdata['head_type']     		= "Customer"; 
	 }
	  $requestdata['transaction_type']  = "Received";
	  $requestdata['project_id']        = getFromSession('project_id');    
	  $requestdata['created_by']        = getFromSession('userid'); 
	  $requestdata['created_date']      = formatDate(getRequest('sales_date')); 
	  $voucher_no = $this->createVoucharID();
	 if($voucher_no!="" && $voucher_no!="B9999999"){
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
		return $voucher_no;
	  }else {	
		return false;
	  }  

}//EOFn   

 function saveCreditVouchar($voucher_no){     
	$mode_of_payment = getRequest('mode_of_payment');	  
	$requestdata = array();
	$requestdata = getUserDataSet(CREDIT_VOUCHAR_TBL);
	
	if($mode_of_payment =="Check"){
		$requestdata['bank_name'] 			= getRequest('bank_name');
		$requestdata['acc_no'] 				= getRequest('acc_no');
		$requestdata['check_no'] 			= getRequest('check_no');
		$requestdata['check_issue_date'] 	= formatDate(getRequest('check_issue_date'));     
		$requestdata['account_head']      	= getRequest('customer'); 
		$requestdata['credit']        		= getRequest('paid_amount');
	  }elseif($mode_of_payment=="Cash"){
		$requestdata['bank_name'] = "";
		$requestdata['acc_no'] = "";
		$requestdata['check_no'] = "";
		$requestdata['check_issue_date'] = "";     
		$requestdata['account_head']     = getRequest('customer'); 
		$requestdata['credit']        	 = getRequest('paid_amount');
	  }elseif($mode_of_payment=="Recievable"){
		$requestdata['bank_name'] = "";
		$requestdata['acc_no'] = "";
		$requestdata['check_no'] = "";
		$requestdata['check_issue_date'] = "";
		$requestdata['account_head']     	= $this->getRecievableId(getFromSession('project_id')); 
		$requestdata['credit']        		= getRequest('due'); 
		$requestdata['debit']        		= 0;     
		$requestdata['head_type']     		= "Acc"; 
	}  
	$requestdata['transaction_type']  = "Received";     
	$requestdata['head_type']     	= "Acc"; 
	$requestdata['project_id']        = getFromSession('project_id');    
	$requestdata['created_by']        = getFromSession('userid'); 
	$requestdata['created_date']      = formatDate(getRequest('sales_date')); 
	$requestdata['voucher_no']   	= $voucher_no;
	$info        		=  array();
	$info['table']	= CREDIT_VOUCHAR_TBL;
	$info['data'] 	= $requestdata;     
	//$info['debug']  	=  true;
	$res = insert($info);
	$created_date = $requestdata['created_date'];
	if($res['affected_rows']) {
		if(getRequest('salse_type')=="Sales") {		 
		 $DrAmount = getRequest('paid_amount');
		 $due = getRequest('due');
		 $project_id = getFromSession('project_id');
		 if($mode_of_payment=="Cash"){ 
			if(getRequest('due')>0){					
				//======= Party Dr ======	
				$description = getRequest('description');
				if($description==""){ $description = "Amount chargeable against buy medicine";}	
				$fullReceivable = getRequest('net_payble');
				$PartyAcc_head = getRequest('customer');  
				$totalPartyCR  = $this->getTotalCreditAmount($PartyAcc_head,getFromSession('project_id'));
				$totalPartyDR  = $this->getTotalDebitAmount($PartyAcc_head,getFromSession('project_id'));	
				$PreviousPartyBalance = ($totalPartyDR-$totalPartyCR);					 
				$PartyBalance  = (($totalPartyDR+$fullReceivable)-$totalPartyCR);					 
			$this->saveAccountJournal($voucher_no,$PartyAcc_head,"Acc",$project_id,$description,$fullReceivable,0,$PartyBalance,1,$created_date);				
				//======== Party Cr ============== 
				$description = getRequest('description');
				if($description==""){ $description = "Paid amount against buy medicine";}	
				$paidAmount = getRequest('paid_amount');
				$PartyAcc_head1 = getRequest('customer'); 
				$totalPartyCR1  = $this->getTotalCreditAmount($PartyAcc_head1,getFromSession('project_id'));
				$totalPartyDR1  = $this->getTotalDebitAmount($PartyAcc_head1,getFromSession('project_id'));					 
				$PartyBalance1  = ($totalPartyDR1-($totalPartyCR1+$paidAmount));					 
				$this->saveAccountJournal($voucher_no,$PartyAcc_head1,"Acc",$project_id,$description,0,$paidAmount,$PartyBalance1,1,$created_date);	
				//============== Cash Dr ===============
				$description = getRequest('description');
				if($description==""){ $description = "Receipt amount against sales medicine";}
				if(getFromSession('u_type_id')==102){
				$acc_head = getFromSession('cash_id'); 	
				}else{
				$acc_head = $this->getCashId(getFromSession('project_id'));
				}	
				 
				$totalCR  = $this->getTotalCreditAmount($acc_head,getFromSession('project_id'));
				$totalDR  = $this->getTotalDebitAmount($acc_head,getFromSession('project_id'));					 
				$balance  = (($totalDR+$DrAmount)-$totalCR);					 
				$this->saveAccountJournal($voucher_no,$acc_head,"Acc",$project_id,$description,$DrAmount,0,$balance,1,$created_date);	
			}elseif(getRequest('due')==0){		
				//======= Party Dr ======
				$description = getRequest('description');
				if($description==""){ $description = "Amount chargeable against buy medicine";}				
				$Receivable    = getRequest('net_payble');
				$PartyAcc_head = getRequest('customer');  
				$totalPartyCR  = $this->getTotalCreditAmount($PartyAcc_head,getFromSession('project_id'));
				$totalPartyDR  = $this->getTotalDebitAmount($PartyAcc_head,getFromSession('project_id'));	
				$PreviousPartyBalance = ($totalPartyDR-$totalPartyCR);					 
				$PartyBalance  = (($totalPartyDR+$Receivable)-$totalPartyCR);					 
				$this->saveAccountJournal($voucher_no,$PartyAcc_head,"Acc",$project_id,$description,$Receivable,0,$PartyBalance,1,$created_date);	
				//======= Party Cr ======	
				$description = getRequest('description');
				if($description==""){ $description = "Paid amount against buy medicine";}			
				$CrAmount1 = getRequest('paid_amount');
				$PartyAcc_head = getRequest('customer');  
				$totalPartyCR  = $this->getTotalCreditAmount($PartyAcc_head,getFromSession('project_id'));
				$totalPartyDR  = $this->getTotalDebitAmount($PartyAcc_head,getFromSession('project_id'));					 
				$PartyBalance  = ($totalPartyDR-($totalPartyCR+$CrAmount1));					 
				$this->saveAccountJournal($voucher_no,$PartyAcc_head,"Acc",$project_id,$description,0,$CrAmount1,$PartyBalance,1,$created_date);	
				//============== Cash Dr ===============
				$description = getRequest('description');
				if($description==""){ $description = "Receipt amount against sales medicine";}
				$DrAmount1 = $CrAmount1;
				if(getFromSession('u_type_id')==102){
				$acc_head = getFromSession('cash_id'); 	
				}else{
				$acc_head = $this->getCashId(getFromSession('project_id'));
				}
				$totalCR  = $this->getTotalCreditAmount($acc_head,getFromSession('project_id'));
				$totalDR  = $this->getTotalDebitAmount($acc_head,getFromSession('project_id'));					 
				$balance  = (($totalDR+$DrAmount1)-$totalCR);					 
				$this->saveAccountJournal($voucher_no,$acc_head,"Acc",$project_id,$description,$DrAmount1,0,$balance,1,$created_date);	
			}
		 }elseif($mode_of_payment=="Check"){
			//====== save payable_check ======
			$this->savePayableCheck($voucher_no,$voucher_no,"Received",getRequest('paid_amount'));
			//======= Party Dr ======
			$description = getRequest('description');
			if($description==""){ $description = "Amount chargeable against buy medicine";}					
			$fullReceivable = getRequest('net_payble');
			$PartyAcc_head = getRequest('customer');  
			$totalPartyCR  = $this->getTotalCreditAmount($PartyAcc_head,getFromSession('project_id'));
			$totalPartyDR  = $this->getTotalDebitAmount($PartyAcc_head,getFromSession('project_id'));	
			$PreviousPartyBalance = ($totalPartyDR-$totalPartyCR);					 
			$PartyBalance  = (($totalPartyDR+$fullReceivable)-$totalPartyCR);					 
			$this->saveAccountJournal($voucher_no,$PartyAcc_head,"Acc",$project_id,$description,$fullReceivable,0,$PartyBalance,1,$created_date);	
			//======= Party Cr ======	
			$description = getRequest('description');
			if($description==""){ $description = "Paid amount against buy medicine";}			
			$CrAmount1 = getRequest('paid_amount');
			$PartyAcc_head = getRequest('customer');  
			$totalPartyCR  = $this->getTotalCreditAmount($PartyAcc_head,getFromSession('project_id'));
			$totalPartyDR  = $this->getTotalDebitAmount($PartyAcc_head,getFromSession('project_id'));					 
			$PartyBalance  = ($totalPartyDR-($totalPartyCR+$CrAmount1));					 
			$this->saveAccountJournal($voucher_no,$PartyAcc_head,"Acc",$project_id,$description,0,$CrAmount1,$PartyBalance,1,$created_date);	
		}elseif($mode_of_payment=="Recievable"){
			//======= Party Dr ======
			$description = getRequest('description');
			if($description==""){ $description = "Amount chargeable against buy medicine";}					
			$DrAmount1 = getRequest('due');
			$PartyAcc_head = getRequest('customer');  
			$totalPartyCR  = $this->getTotalCreditAmount($PartyAcc_head,getFromSession('project_id'));
			$totalPartyDR  = $this->getTotalDebitAmount($PartyAcc_head,getFromSession('project_id'));	
			$PreviousPartyBalance = ($totalPartyDR-$totalPartyCR);					 
			$PartyBalance  = (($totalPartyDR+$DrAmount1)-$totalPartyCR);					 
			$this->saveAccountJournal($voucher_no,$PartyAcc_head,"Acc",$project_id,$description,$DrAmount1,0,$PartyBalance,1,$created_date);				
		}	
		}
		return $PreviousPartyBalance;		
	  }else {	
		return 0;
	  } 	 
}//EOFn 
  
function make_seed(){
   list($usec, $sec) = explode(' ', microtime());
   return (float) $sec + ((float) $usec * 100000);
}
function saveAdjustCustomerPayble($NewVoucherNo,$account_head,$CrAmount){
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
	 if($CrAmount >= $srow->due){
		$CrAmount = ($CrAmount - $srow->due); $adjustAmount = $srow->due;
		$totalPaidAmount = ($srow->paid_amount+$srow->due);
		if($totalPaidAmount==$srow->debit){
		 $pusql="UPDATE ".DEVIT_VOUCHAR_TBL." SET paid_amount='".$totalPaidAmount."',due='0',`status`=1 WHERE voucher_no='$voucher_no'";
		 mysql_query($pusql);
		 $clistApp->saveInvoiceAdjustHistory($NewVoucherNo,0,$project_id,DEVIT_VOUCHAR_TBL,$voucher_no,$adjustAmount,"-");
		}
	 }elseif(($CrAmount < $srow->due) && ($CrAmount >0)){
		$presentDue = ($srow->due - $CrAmount);
		$PaidAmount = ($srow->paid_amount + $CrAmount);
		if($PaidAmount < $srow->debit){
		 $adjustAmount = $CrAmount; $CrAmount=0;
		 $pusql2="UPDATE ".DEVIT_VOUCHAR_TBL." SET paid_amount='".$PaidAmount."',due='$presentDue',`status`=0 WHERE voucher_no='$voucher_no'";
		 mysql_query($pusql2);
		 $clistApp->saveInvoiceAdjustHistory($NewVoucherNo,0,$project_id,DEVIT_VOUCHAR_TBL,$voucher_no,$adjustAmount,"-");
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
		if(($CrAmount >= $existing_due)){
			$CrAmount 	= $CrAmount - $existing_due;
			if($existing_due>0){						
			$total_paid = ($paid_amount + $existing_due); 
			$SRUpSql = "UPDATE ".SALES_RETURN_PAYBLE_TBL." SET paid_amount=$total_paid, due=0  WHERE return_id ='$return_id' AND project_id = '$project_id'";
			mysql_query($SRUpSql);
			$clistApp->saveInvoiceAdjustHistory($NewVoucherNo,0,$project_id,SALES_RETURN_PAYBLE_TBL,$return_id,$existing_due,"-");
			} 
		}elseif(($CrAmount < $existing_due)){					
			if($existing_due>0 && $CrAmount>0){
			$totalpaid 	 = ($paid_amount + $CrAmount); 
			$present_due = ($existing_due - $CrAmount);
			$adjustAmount = $CrAmount; $CrAmount = 0;
			$SRPUpdate="UPDATE ".SALES_RETURN_PAYBLE_TBL." SET paid_amount=$totalpaid,due=$present_due WHERE return_id='$return_id' AND project_id='$project_id'";
			mysql_query($SRPUpdate);
			$clistApp->saveInvoiceAdjustHistory($NewVoucherNo,0,$project_id,SALES_RETURN_PAYBLE_TBL,$return_id,$adjustAmount,"-");
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
function saveStockJournal($pvoucher_no,$voucher_no,$project_id,$store_id,$product_id,$serial,$warranty,$note,$unit_price=NULL,$m_unit,$DR=NULL,$CR=NULL,$balance,$create_date=NULL,$sdmid=NULL){
  	$created_by = getFromSession('userid');
	$sql="INSERT INTO ".STOCK_LEDGER_TBL." (po_no,voucher_no,project_id,store_id,delivery_id,product_id,serial,warranty,note,unit_price,m_unit,dr,cr,balance,created_by,create_date) 
	VALUES('".$pvoucher_no."','".$voucher_no."','".$project_id."','".$store_id."','".$sdmid."','".$product_id."','".$serial."','".$warranty."','".$note."','".$unit_price."','".$m_unit."','".$DR."','".$CR."','".$balance."','".$created_by."','".$create_date."')";
	mysql_query($sql);
}
function saveAccountJournal($voucher_no,$sub_id,$head_type,$project_id,$description,$DR=NULL,$CR=NULL,$balance,$status=NULL,$purchare_date=NULL){	
	$rsql= "SELECT head_type FROM ".SUB_ACC_HEAD_TBL." WHERE sub_id='".$sub_id."'";  
	$rres = mysql_query($rsql);
	$hnum = mysql_num_rows($rres);
	if($hnum>0){ 
	$hrow = mysql_fetch_object($rres);
	$head_type= $hrow->head_type;
	}else{ 	$head_type= "Supplier"; }
	$transaction_type = "Sales";	$created_by = getFromSession('userid');		
	$sql = "INSERT INTO ".ACCOUNT_JOURNAL_TBL." (voucher_no,created_date,sub_id,head_type,transaction_type,project_id,description,dr,cr,balance,status,created_by) VALUES('".$voucher_no."','".$purchare_date."','"
	.$sub_id."','".$head_type."','".$transaction_type."','".$project_id."','".$description."','".$DR."','".$CR."','".$balance."','".$status."','".$created_by."')";
	mysql_query($sql);
}
function getSalesMasterInfo($id){		   
	$project_id     = getFromSession('project_id');  
	$info           = array();    
	$info['table']  = SALES_MASTER_TBL.' pm,'.SUB_ACC_HEAD_TBL.' s,'.PROJECT_TBL.' p,'.CURRENCY_TBL.' c';	
	$info['fields'] = array('pm.voucher_no','pm.po_no','pm.wo_no','pm.delivery_point','p.project_name','p.location','p.project_logo','pm.customer','s.sub_head_name','s.head_details','s.phone','s.mobile','s.email','s.att_name1','s.att_designation1','s.att_mobile1','pm.reference','pm.gate_pass','pm.track_no','pm.salse_type','pm.total_value',"DATE_FORMAT(pm.sales_date,'%d %b %y' ) as sales_date","pm.sales_date as salesdate","DATE_FORMAT(pm.delivery_date,'%d %b %y' ) as delivery_date",'pm.mode_of_payment','c.curr_symble','pm.bank_name','pm.acc_no','pm.check_no','pm.check_no','pm.general_discount_percent','pm.discount','pm.service_charge','pm.net_payble','pm.previour_balance','pm.adjust','pm.item_delivery_amount','pm.paid_amount','pm.due','pm.ref_no','pm.created_date');	
	$sql="pm.customer = s.sub_id AND pm.project_id = p.project_id AND pm.currency = c.currency_id AND pm.project_id = '".$project_id."' AND pm.voucher_no = '$id'";						
	$info['where']  =$sql;	  	
	$info['groupby'] = array("pm.voucher_no");
	$res            =	select($info);
	if(count($res)){
		foreach($res as $i=>$v){
			$data[$i] = $v;             
		}
	}
	return $data[0];
} 
   function getProductList($id) { 
		$info           = array();    
		$info['table']  =  SALES_DETAILS_TBL.' sd,'.PRODUCT_TBL.' p,'.CURRENCY_TBL.' c,'.BRAND_TBL.' b';	
		$info['fields'] = array('sd.sal_detail_id','sd.voucher_no','sd.project_id','sd.serial','sd.warranty','sd.catagory','b.brand_name','sd.product','sd.details','p.product_name','p.product_desc','sd.m_unit','sd.unit_price','c.curr_symble','sd.discount_per_qty','sd.qty','sd.delivery_qty','sd.total_bag','sd.total','sd.created_time');
		
		$sql="sd.product = p.product_id AND p.brand_code = b.brand_id AND sd.currency = c.currency_id AND sd.voucher_no = '$id'";
		
		$info['where']  = $sql;
	        $info['groupby'] = array("sd.sal_detail_id");
		$info['orderby'] = array("sd.product asc");
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
	function loadProduct4Catagory($catagory)
	{	  
		  $brand_id		   = trim(getRequest('brand_id'));
	  	  $project_id = getFromSession('project_id');  
		  $info            = array();
		  $info['table']   = PRODUCT_TBL;
		  $info['fields']  =  array('product_id','product_name','product_desc');
		  $info['where']   = "`brand_code`='$brand_id' AND project_id = '$project_id' AND approval_status = 1";
		  $info['groupby'] = array("product_id");
		  //$info['debug']   = true;
		  $result          = select($info);
		  $data            = array();	
		  if(count($result)){
			 foreach($result as $key=>$value){
				$data[$key][]        = $value;
			 }
		  }
		  foreach($data as $i=>$v){
			 $subject_idname .= $v[0]->product_id.'#####'.$v[0]->product_code.'-'.$v[0]->product_name.'#####'.$v[0]->product_desc.'@@@';
		  }
		  echo $subject_idname;	
	}

  function loadProductDtl($product_id){
	$project_id = getFromSession('project_id');  	$stock_qty=0;	 
	$info            = array();	  	  
	$info['table']   = PRODUCT_TBL." p,".CATAGORY_TBL.' c,'.BRAND_TBL.' b';
	$info['fields']  =  array('p.m_unit','p.product_desc','p.unit_price','p.product_catagory','p.catagory','c.catagory_name','p.brand_code','b.brand_name');
	$info['where']   = "p.catagory =c.catagory_code AND p.brand_code =b.brand_id AND p.product_id = '$product_id'  AND p.project_id = '$project_id'";
	$info['groupby'] = array("p.product_id");		  
	//$info['debug']   = true;
	$result          = select($info);
	$data            = array();
	if(count($result)){
	 foreach($result as $key=>$value){
		$data[$key][]        = $value;
	 }
	}
	
	foreach($data as $i=>$v){
	$str = $v[0]->m_unit."#####".$v[0]->product_desc."#####".$v[0]->unit_price."#####".$v[0]->catagory."###".$v[0]->catagory_name."#####".$v[0]->brand_code."###".$v[0]->brand_name;
	}
	$stock_qty = $this->loadStockQty($product_id); 
	echo $str."#####".$stock_qty;	
  }
	function loadStockQty($product_id){
	  $project_id = getFromSession('project_id');	 
	  $totalCr = $this->getTotalCreditStock($product_id,$project_id);
	  $totalDr = $this->getTotalDebitStock($product_id,$project_id);
	  $balanceQty = $totalDr - $totalCr; if($balanceQty==""){ $balanceQty=0;}
	  return $balanceQty;	
	}
    function getTotalOrderQty($product_id,$store_id){
		$created_by = getFromSession('userid'); 
	    $project_id = getFromSession('project_id');
		$getSql	= "SELECT SUM( qty+free_qty ) AS total_qty FROM ".TEMP_SALES_ORDER_TBL." WHERE AND project_id='".$project_id."' 
		AND productid='$product_id' AND delivery_point='$store_id' GROUP BY productid";
		$gres 	= mysql_query($getSql);
		$row = mysql_fetch_object($gres);
		$total_qty = $row->total_qty;
		if($total_qty==""){ $total_qty=0;}
		return $total_qty;
	}
  function getCashId($project_id){
		$sql = "SELECT sub_id FROM ".SUB_ACC_HEAD_TBL." WHERE head_type = 'Cash' AND project_id = '$project_id'";
		$row = mysql_fetch_object(mysql_query($sql));
		return $sub_id = $row->sub_id;
	}
	function getCapitalId($project_id){
		$sql = "SELECT sub_id FROM ".SUB_ACC_HEAD_TBL." WHERE head_type = 'Capital' AND project_id = '$project_id'";
		$row = mysql_fetch_object(mysql_query($sql));
		return $sub_id = $row->sub_id;
	}
	function getRecievableId($project_id){
		$sql = "SELECT sub_id FROM ".SUB_ACC_HEAD_TBL." WHERE head_type = 'Accounts Recievable' AND project_id = '$project_id'";
		$row = mysql_fetch_object(mysql_query($sql));
		return $sub_id = $row->sub_id;
	}
	function getPayableId($project_id){
		$sql = "SELECT sub_id FROM ".SUB_ACC_HEAD_TBL." WHERE head_type = 'Accounts Payable' AND project_id = '$project_id'";
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
   function getTotalCreditStock($acc_head,$project_id){
   		$sql = "SELECT sum(`cr`) as credit_amount FROM ".STOCK_LEDGER_TBL." WHERE product_id = '$acc_head' AND project_id = '$project_id'";
		$row = mysql_fetch_object(mysql_query($sql));
		$credit_amount = $row->credit_amount;
		if(empty($credit_amount)){
			$credit_amount = 0;
		}
		return $credit_amount;
   }
  
   function getTotalDebitStock($acc_head,$project_id){
   		$sql = "SELECT sum(`dr`) as debit_amount FROM ".STOCK_LEDGER_TBL." WHERE product_id = '$acc_head' AND project_id = '$project_id'";
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
      $info['table'] = SALES_MASTER_TBL;
      $info['fields'] = array('max(voucher_no) as maxvoucher');
	  //$info['debug']   = true;      
      $res = select($info);      
      $maxvoucherId = 'B0000000';      
      if(count($res)){
         foreach($res as $v){
         	 if($v->maxvoucher){
             $maxvoucherId = $v->maxvoucher;
             }
             break;   	
         }
      }
      $maxvoucherId = generateID("B",$maxvoucherId,8);
      return $maxvoucherId;
  }  
  
 function savePayableCheck($voucher_no,$rvoucher_no,$transaction_type,$paid_amount){
  $requestdata = array();
  $requestdata = getUserDataSet(PAYABLE_CHECK_TBL);	
  $requestdata['check_no'] 			= getRequest('check_no');
  $requestdata['check_issue_date']  = formatDate(getRequest('check_issue_date')); 
  $requestdata['created_date']      = formatDate(getRequest('sales_date'));
  $requestdata['acc_head'] 			= getRequest('customer'); 
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
  $res = insert($info);
}
//==================== End Sales Details =====================
 function loadPatientbySrc($vipsrc)
 {	  
	  	  $project_id = getFromSession('project_id');  
		  $info            = array();
		  $info['table']   = SUB_ACC_HEAD_TBL;
		  $info['fields']  =  array('sub_id','employee_id','sub_head_name');
		  $sql = "project_id = '$project_id' ";
		  if($vipsrc!=""){			  
			$sql.=" AND (sub_id LIKE '%$vipsrc%' ) ";
		  }
		  $sql.=" AND (`head_type`='Shareholder' OR `head_type`='Patient' OR `head_type`='Customer') ";
		  $info['where']   =$sql; 
		  $info['groupby'] = array("sub_id");
		  //$info['debug']   = true;	
		  $result          = select($info);
		  $data            = array();	
		  if(count($result)){
			 foreach($result as $key=>$value){
				$data[$key][]        = $value;
			 }
		  }
		  foreach($data as $i=>$v){
			 $subject_idname .= $v[0]->sub_id.'#####'.$v[0]->sub_head_name.'@@@';
		  }
		  echo $subject_idname;	
 }  
} // End class
?>
