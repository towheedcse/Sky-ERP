<?php
class SalesDelivery{
   
   function run() { 
	 	     
      $cmd = getRequest('cmd');
	  $u_t_id = getFromSession('u_type_id');
      if( ($u_t_id == 101) || ($u_t_id == 102) || ($u_t_id == 103) || ($u_t_id == 105))
      {
      	switch ($cmd){
      	   case 'delivery'              : $screen = $this->showEditor("Edit Page");    break;    
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
	  $comlistApp 			= new CommonList();

	  if(getRequest('submit')) {
		$this->saveDeliveryChallan();
	  }else{
	  	 $voucher_no 			= getRequest('voucher_no');  
		 $data['cmd']       	= getRequest('cmd');		  
		 $advArr 				= $salesApp->getSalesMasterInfo($voucher_no);
         $advArr 				= parseThisValue($advArr); 
		 $data   				= array_merge($advArr); 
		 $data['item_list']		= $salesApp->getProductList($voucher_no);		     	
	   	 $data['retailer_list'] = $comlistApp->getRetailerList();
	  }
	  require_once(CURRENT_APP_SKIN_FILE); 
	  return $data[0];	    
   }
	function saveDeliveryChallan(){
		//ini_set('max_execution_time', 800);
		//ini_set('max_input_time', 800);
		mysql_query("START TRANSACTION;");
		$delivery_master_id = $this->insertDeliveryChallanMaster(getRequest("voucher_no"));
		$this->insertDeliveryChallanDetails(getRequest("voucher_no"),$delivery_master_id);		
	}
	function insertDeliveryChallanMaster($voucher_no){
		$requestdata = array();
		$requestdata = getUserDataSet(SALES_DELIVERY_MASTER_TBL);
		$requestdata['delivery_date'] 	  = formatDate(getRequest('delivery_date'));   
		$requestdata['total_value']       = getRequest('total_delivery_amount');
		$requestdata['voucher_no']        = $voucher_no;
		$requestdata['project_id']        = getFromSession('project_id');    
		$requestdata['created_by']        = getFromSession('userid');	
		$requestdata['created_date']      = date('Y-m-d h:i:s');
		mt_srand($this->make_seed());
		$gatepass = mt_rand();   
		//$requestdata['gate_pass']       = $gatepass;	
		$info        		=  array();
		$info['table']	= SALES_DELIVERY_MASTER_TBL;
		$info['data'] 	= $requestdata;     
		//$info['debug']  =  true;
		$res = insert($info);
		if($res['affected_rows']){
		$delivery_master_id = mysql_insert_id();
		return $delivery_master_id;
		}
  }	
  function insertDeliveryChallanDetails($voucher_no,$dm_id)
  {
	require_once(CLASS_DIR.'/common.list.class.php');	
	$comlistApp 				= new CommonList();
	require_once(CLASS_DIR.'/sales_order.class.php');	
	$soApp 			= new SalesOrder();
		
	$customer 		= getRequest('customer');
	$voucher_no 	= getRequest('voucher_no');
	$delivery_point = getRequest('delivery_point');
	$store_id 		= getRequest('delivery_point');
	$discount 		= getRequest('discount');
	$orderValue 	= getRequest('total_sales_price');
	$overall_discount = (($discount/$orderValue)*100);
	$consignee 		= getRequest('consignee');	
	$totalfields 	= getRequest('ttlfields');
	$delivery_date 	= formatDate(getRequest('delivery_date')); 
	$project_id 	= getFromSession('project_id');
	$created_by 	= getFromSession('userid');
	$totalDeliveryAmount = 0; $totalDeliveryQty = 0; $totalOrderQty = 0;$j=1; $totalStockDeliveryAmount = 0; $totalProfit = 0; $totalLoss=0; 
	for($j; $j<=$totalfields; $j++){
	$sales_details_id= getRequest("details_id$j");
	$catagory		 = getRequest("catagory$j");
	$brand_id		 = getRequest("brand_id$j");
	$product		 = getRequest("product$j");
	$m_unit			 = getRequest("m_unit$j");
	$stock_qty		 = getRequest("stock_qty$j");
	$order_qty		 = getRequest("order_qty$j");
	$delivery_qty	 = getRequest("delivery_qty$j");
	$freeQty  	     = getRequest("free_qty$j");
	$Prvfree_qty  	 = getRequest("Prvfree_qty$j");
	$sales_price  	 = getRequest("unit_price$j");
	$totalOrderQty	+=$order_qty;
	$discount_per_qty=($overall_discount/$order_qty);
	$discount_amount = getRequest("discount_amount$j");
	$UDQty  		 = ($delivery_qty+$freeQty);
	if($UDQty >0){
	$Pcsql="SELECT product_type,product_catagory FROM ".PRODUCT_TBL." WHERE product_id='$product' AND project_id='$project_id'";
	$Pcrow = mysql_fetch_object(mysql_query($Pcsql));
	$product_catagory 	= $Pcrow->product_catagory;
	$product_type 		= $Pcrow->product_type;
				
	$PuSql="SELECT pur_detail_id,voucher_no,serial,warranty,unit_price,sales_qty,stock_qty FROM ".PURCHASE_ITEM_VIEW." 
	WHERE product='$product' AND project_id='$project_id' AND stock_qty >0 ORDER BY pur_detail_id ASC";
	$pres = mysql_query($PuSql);
	if(mysql_num_rows($pres)>0){
	while($Purow = mysql_fetch_object($pres)){
		$pur_detail_id  = $Purow->pur_detail_id;
		$pvoucher_no    = $Purow->voucher_no; $sales_qty=$Purow->sales_qty;
		$purchase_price = $Purow->unit_price; $stockQty=$Purow->stock_qty;
		$serial=$Purow->serial; $warranty=$Purow->warranty;
		
		$sldsql="SELECT delivery_qty,purchase_price,unit_profit FROM ".SALES_DETAILS_TBL." WHERE product='$product' AND project_id='$project_id' 
		AND sal_detail_id='$sales_details_id'";
		$sldrow = mysql_fetch_object(mysql_query($sldsql));
		$PvDeliveryQty 	  = $sldrow->delivery_qty;
		$PrvPurchasePrice = $sldrow->purchase_price;
		$PrvUnitProfit	  = $sldrow->unit_profit;
		
		if($stockQty>=$UDQty){
			$TTLSalesQty    = ($sales_qty+$UDQty);			
			$unit_profit    = ($sales_price-$purchase_price);
			$overall_discount_amount = (($sales_price/100)*$overall_discount);
			$unit_profit    = ($unit_profit-$overall_discount_amount);
			$deliveryAmount	= (($sales_price-$overall_discount_amount)*$delivery_qty);
			$totalDeliveryAmount+=$deliveryAmount;	 $totalDeliveryQty+=$delivery_qty;		
			$pdusql="UPDATE ".PURCHASE_DETAILS_TBL." SET sales_qty='".$TTLSalesQty."' WHERE pur_detail_id='$pur_detail_id'";
			$pures = mysql_query($pdusql);
			if($pures){
			if($unit_profit>=0){
				$totalProfit += (($UDQty-$freeQty)*$unit_profit);	
			}else{
				$totalLoss += abs((($UDQty-$freeQty)*$unit_profit));	
			}
			//======= Will be save product wise profit loss =======
			//$this->saveProfit($voucher_no,$product,$dm_id,$unit_profit,$UDQty,$freeQty,$sales_price,$purchase_price,$overall_discount_amount,$delivery_date);
			
			$this->saveDeliveryChallanDtl($sales_details_id,$dm_id,$voucher_no,$pvoucher_no,$delivery_point,$consignee,$project_id,$catagory,$brand_id,$product,$serial,$warranty,$m_unit,$sales_price,$discount_per_qty,$discount_amount,$overall_discount,$overall_discount_amount,$unit_profit,$delivery_qty,$freeQty,$deliveryAmount,$created_by);
			if($product_catagory=="Serial"){
				$sduSql = "UPDATE ".SALES_DETAILS_TBL." SET pvoucher_no='$pvoucher_no',serial='$serial',warranty='$warranty',
				purchase_price='$purchase_price',unit_profit='$unit_profit',delivery_qty='1',free_qty='$freeQty' 
				WHERE voucher_no='".$voucher_no."' AND  product='$product' AND sal_detail_id='$sales_details_id'";
				mysql_query($sduSql);
			}else{
				$TTLDvQty = ($PvDeliveryQty+$delivery_qty);	
				$ttlFreeQty = $Prvfree_qty+$freeQty;	
				if(($PrvPurchasePrice>0 && $PrvUnitProfit>0) && ($purchase_price!=$PrvPurchasePrice)){
					$purchase_price 	= (($purchase_price+$PrvPurchasePrice)/2); 
					$PresentUnitProfit 	= ($unit_profit*$delivery_qty);
					$PvUnitProfit 		= ($PrvUnitProfit*$PvDeliveryQty);
					$unit_profit 		= (($PresentUnitProfit+$PvUnitProfit)/$TTLDvQty); 				
				}
				$sduSql = "UPDATE ".SALES_DETAILS_TBL." SET pvoucher_no='$pvoucher_no',warranty='$warranty',purchase_price='$purchase_price',
				unit_profit='$unit_profit',delivery_qty='".$TTLDvQty."',free_qty='$ttlFreeQty' WHERE voucher_no='".$voucher_no."'";
				$sduSql.=" AND  product='$product' AND sal_detail_id='$sales_details_id'";
				mysql_query($sduSql);			
			}// end Serial else
			//=== update stock ===
			$totalCR  = $this->getTotalCreditStock($product,getFromSession('project_id'));
			$totalDR  = $this->getTotalDebitStock($product,getFromSession('project_id'));					 
			$balance  = ($totalDR - ($totalCR+$UDQty));	
			if($product_type=="Sales Item" || $product_type=="Raw Materials"){	
			if($overall_discount_amount>0){ $netSalesPrice = $sales_price-$overall_discount_amount; }else{$netSalesPrice = $sales_price;}
			$note = "Sales Delivery";
			$this->saveStockJournal($pvoucher_no,$voucher_no,$project_id,$store_id,$product,$serial,$warranty,$note,$netSalesPrice,$m_unit,0,$UDQty,$balance,$delivery_date,$dm_id);			
			}
			//=== Stock Cr =====
			$StockAmount = ($purchase_price*$UDQty);
			$totalStockDeliveryAmount+=$StockAmount;
			
			if($freeQty>0){
			$DrAmount = ($purchase_price*$freeQty);
			$description = "Delivery free product";
			//========= Capital Cr ==========
			$capital_head 	 = $comlistApp->getMainCapitalId(getFromSession('project_id'));
			$totalCapitalCR  = $this->getTotalCreditAmount($capital_head,getFromSession('project_id'));
			$totalCapitalDR  = $this->getTotalDebitAmount($capital_head,getFromSession('project_id'));					 
			$Capitalbalance  = ($totalCapitalDR-($totalCapitalCR+$DrAmount));					 
			$comlistApp->saveAccJournal($voucher_no,$capital_head,"Capital","Free Product",$project_id,$description,0,$DrAmount,$Capitalbalance,0,$delivery_date,$dm_id);
			//========= Administrative Cost Dr ==========
			$freeItemhead 	 = $comlistApp->getAdvCostFreeItemId(getFromSession('project_id'));
			$totalfreeItemCR = $this->getTotalCreditAmount($freeItemhead,getFromSession('project_id'));
			$totalfreeItemDR = $this->getTotalDebitAmount($freeItemhead,getFromSession('project_id'));					 
			$freeItemBalance = (($totalfreeItemDR+$DrAmount)-$totalfreeItemCR);					 
			$comlistApp->saveAccJournal($voucher_no,$freeItemhead,"Acc","Free Product",$project_id,$description,$DrAmount,0,$freeItemBalance,0,$delivery_date,$dm_id);
			}
			$delivery_qty=0; $freeQty=0; $UDQty=0; $PvDeliveryQty=0; $Prvfree_qty=0;
			} // update PO
			break;
		}elseif($stockQty < $UDQty){
			$UDQty = $UDQty - $stockQty; $PurchaseIsUpdate = 0;			
			if(($stockQty >= $freeQty) && ($freeQty >=0)){
				$NowDeliveryQty = $stockQty-$freeQty;
				$delivery_qty	= $delivery_qty-$NowDeliveryQty;					
				$TTLSalesQty    = ($sales_qty+$stockQty);			
				$unit_profit    = ($sales_price-$purchase_price);
				$overall_discount_amount = (($sales_price/100)*$overall_discount);
				$unit_profit    = ($unit_profit-$overall_discount_amount);					
				
				$pdusql="UPDATE ".PURCHASE_DETAILS_TBL." SET sales_qty='".$TTLSalesQty."' WHERE pur_detail_id='$pur_detail_id'";
				$pdres = mysql_query($pdusql);
				if($pdres){
					if($NowDeliveryQty >0){
					$deliveryAmount	= (($sales_price-$overall_discount_amount)*$NowDeliveryQty);
					$totalDeliveryAmount+=$deliveryAmount; $totalDeliveryQty+=$NowDeliveryQty;
					
					if($unit_profit>=0){
						$totalProfit += (($NowDeliveryQty-$freeQty)*$unit_profit);	
					}else{
						$totalLoss += abs((($NowDeliveryQty-$freeQty)*$unit_profit));	
					}
					//======= Will be save product wise profit loss ======= 	
					
					}
					$TTLDvQty = ($PvDeliveryQty+$NowDeliveryQty);	
					$ttlFreeQty = $Prvfree_qty+$freeQty;	
					$this->saveDeliveryChallanDtl($sales_details_id,$dm_id,$voucher_no,$pvoucher_no,$delivery_point,$consignee,$project_id,$catagory,$brand_id,$product,$serial,$warranty,$m_unit,$sales_price,$discount_per_qty,$discount_amount,$overall_discount,$overall_discount_amount,$unit_profit,$NowDeliveryQty,$freeQty,$deliveryAmount,$created_by);
					if($product_catagory=="Serial"){
						$sduSql = "UPDATE ".SALES_DETAILS_TBL." SET pvoucher_no='$pvoucher_no',serial='$serial',warranty='$warranty',
						purchase_price='$purchase_price',unit_profit='$unit_profit',delivery_qty='$TTLDvQty',free_qty='$ttlFreeQty' 
						WHERE voucher_no='".$voucher_no."' AND  product='$product' AND sal_detail_id='$sales_details_id'";
						mysql_query($sduSql);						
					}else{
						$TTLDvQty = ($PvDeliveryQty+$NowDeliveryQty);	
						$ttlFreeQty = $Prvfree_qty+$freeQty;	
						if(($PrvPurchasePrice>0 && $PrvUnitProfit>0) && ($purchase_price!=$PrvPurchasePrice)){
							$purchase_price 	= (($purchase_price+$PrvPurchasePrice)/2); 
							$PresentUnitProfit 	= ($unit_profit*$NowDeliveryQty);
							$PvUnitProfit 		= ($PrvUnitProfit*$PvDeliveryQty);
							$unit_profit 		= (($PresentUnitProfit+$PvUnitProfit)/$TTLDvQty); 				
						}
						$sduSql = "UPDATE ".SALES_DETAILS_TBL." SET pvoucher_no='$pvoucher_no',warranty='$warranty',purchase_price='$purchase_price',
						unit_profit='$unit_profit',delivery_qty='".$TTLDvQty."',free_qty='$ttlFreeQty' WHERE voucher_no='".$voucher_no."'";
						$sduSql.=" AND  product='$product' AND sal_detail_id='$sales_details_id'";
						mysql_query($sduSql);			
					}// end Serial else
					if($freeQty>0){
					$DrAmount = ($purchase_price*$freeQty);
					$description = "Delivery free product";
					//========= Capital Cr ==========
					$capital_head 	 = $comlistApp->getMainCapitalId(getFromSession('project_id'));
					$totalCapitalCR  = $this->getTotalCreditAmount($capital_head,getFromSession('project_id'));
					$totalCapitalDR  = $this->getTotalDebitAmount($capital_head,getFromSession('project_id'));					 
					$Capitalbalance  = ($totalCapitalDR-($totalCapitalCR+$DrAmount));					 
					$comlistApp->saveAccJournal($voucher_no,$capital_head,"Acc","Free Product",$project_id,$description,0,$DrAmount,$Capitalbalance,0,$delivery_date,$dm_id);
					//========= Administrative Cost Dr ==========
					$freeItemhead 	 = $comlistApp->getAdvCostFreeItemId(getFromSession('project_id'));
					$totalfreeItemCR = $this->getTotalCreditAmount($freeItemhead,getFromSession('project_id'));
					$totalfreeItemDR = $this->getTotalDebitAmount($freeItemhead,getFromSession('project_id'));					 
					$freeItemBalance = (($totalfreeItemDR+$DrAmount)-$totalfreeItemCR);					 
					$comlistApp->saveAccJournal($voucher_no,$freeItemhead,"Acc","Free Product",$project_id,$description,$DrAmount,0,$freeItemBalance,0,$delivery_date,$dm_id); $freeQty=0;
					}// save free item
					$PurchaseIsUpdate=1; $NowDeliveryQty=0;
				} // update pursaves qty
			}elseif(($stockQty < $freeQty) && ($freeQty >0)){
				$freeQty = $freeQty - $stockQty;
				$TTLSalesQty=($sales_qty+$stockQty);
				$TTLDvQty = ($PvDeliveryQty+0);	
				$ttlFreeQty = $Prvfree_qty+$stockQty;
					
				$pdusql="UPDATE ".PURCHASE_DETAILS_TBL." SET sales_qty='".$TTLSalesQty."' WHERE pur_detail_id='$pur_detail_id'";
				$pdres = mysql_query($pdusql);
				if($pdres){
				$sduSql = "UPDATE ".SALES_DETAILS_TBL." SET pvoucher_no='$pvoucher_no',serial='$serial',warranty='$warranty',purchase_price='$purchase_price',unit_profit='$unit_profit',
				free_qty='$ttlFreeQty' WHERE voucher_no='".$voucher_no."' AND  product='$product' AND sal_detail_id='$sales_details_id'";
				mysql_query($sduSql);
				//====== less free qty ======
				$DrAmount = ($purchase_price*$stockQty);
				$description = "Delivery free product";
				//========= Capital Cr ==========
				$capital_head 	 = $comlistApp->getMainCapitalId(getFromSession('project_id'));
				$totalCapitalCR  = $this->getTotalCreditAmount($capital_head,getFromSession('project_id'));
				$totalCapitalDR  = $this->getTotalDebitAmount($capital_head,getFromSession('project_id'));					 
				$Capitalbalance  = ($totalCapitalDR-($totalCapitalCR+$DrAmount));					 
				$comlistApp->saveAccJournal($voucher_no,$capital_head,"Acc","Free Product",$project_id,$description,0,$DrAmount,$Capitalbalance,0,$delivery_date,$dm_id);
				//========= Administrative Cost Dr ==========
				$freeItemhead 	 = $comlistApp->getAdvCostFreeItemId(getFromSession('project_id'));
				$totalfreeItemCR = $this->getTotalCreditAmount($freeItemhead,getFromSession('project_id'));
				$totalfreeItemDR = $this->getTotalDebitAmount($freeItemhead,getFromSession('project_id'));					 
				$freeItemBalance = (($totalfreeItemDR+$DrAmount)-$totalfreeItemCR);					 
				$comlistApp->saveAccJournal($voucher_no,$freeItemhead,"Acc","Free Product",$project_id,$description,$DrAmount,0,$freeItemBalance,0,$delivery_date,$dm_id);
				$PurchaseIsUpdate=1;
				} // update purchase qty
			}// end $stockQty<$freeQty
			
			if($PurchaseIsUpdate==1){
			//=== Update stock for All ===
			$totalCR  = $this->getTotalCreditStock($product,getFromSession('project_id'));
			$totalDR  = $this->getTotalDebitStock($product,getFromSession('project_id'));					 
			$balance  = ($totalDR - ($totalCR+$stockQty));	
			if($product_type=="Sales Item" || $product_type=="Raw Materials"){	
			if($overall_discount_amount>0){ $netSalesPrice = $sales_price-$overall_discount_amount; }else{$netSalesPrice = $sales_price;}
			$note = "Sales Delivery";
			$this->saveStockJournal($pvoucher_no,$voucher_no,$project_id,$store_id,$product,$serial,$warranty,$note,$netSalesPrice,$m_unit,0,$stockQty,$balance,$delivery_date,$dm_id);			
			}
			//=== Stock Cr =====
			$StockAmount = ($purchase_price*$stockQty);
			$totalStockDeliveryAmount+=$StockAmount;			
			$PurchaseIsUpdate=0;
			}
		}//end stock qty small		
		
	}// end while
	}// end PO num rows
	}// UDQty qty > 0
	}// end for
	
	$project_id = getFromSession('project_id'); $created_date = formatDate(getRequest('delivery_date')); 
	if($totalStockDeliveryAmount > 0){
	//=== Stock Cr =====	
	$StockId 	 = $comlistApp->getStockId(getFromSession('project_id'));
	$totalStockCr  = $this->getTotalCreditAmount($StockId,getFromSession('project_id'));
	$totalStockDr  = $this->getTotalDebitAmount($StockId,getFromSession('project_id'));					 
	$StockBalance  = ($totalStockDr-($totalStockCr+$totalStockDeliveryAmount));	$description = "Sales delivery challan";					 
	$comlistApp->saveAccJournal($voucher_no,$StockId,"Stock","Sales Product",$project_id,$description,0,$totalStockDeliveryAmount,$StockBalance,0,$delivery_date,$dm_id);
	}
	
	if($totalProfit > 0 ){
	//========= Direct Income Dr ==========
	$IncomeId 	 		= $comlistApp->getProductSalesIncomeId(getFromSession('project_id'));
	$totalSalesIncomeCR = $this->getTotalCreditAmount($IncomeId,getFromSession('project_id'));
	$totalSalesIncomeDR = $this->getTotalDebitAmount($IncomeId,getFromSession('project_id'));					 
	$IncomeBalance 		= (($totalSalesIncomeDR+$totalProfit)-$totalSalesIncomeCR);	
	$incDtl = "Sales Income, DC- $dm_id";			 
	$comlistApp->saveAccJournal($voucher_no,$IncomeId,"Acc","Direct Incomes",$project_id,$incDtl,$totalProfit,0,$IncomeBalance,0,$delivery_date,$dm_id);
	}
	
	if($totalLoss > 0 ){	
	//========= Direct Income Cr ==========
	$IncomeId 	 		= $comlistApp->getProductSalesIncomeId(getFromSession('project_id'));
	$totalSalesIncomeCR = $this->getTotalCreditAmount($IncomeId,getFromSession('project_id'));
	$totalSalesIncomeDR = $this->getTotalDebitAmount($IncomeId,getFromSession('project_id'));					 
	$IncomeBalance 		= ($totalSalesIncomeDR-($totalSalesIncomeCR+$totalLoss));	
	$losDtl = "Sales Loss, DC- $dm_id";				 
	$comlistApp->saveAccJournal($voucher_no,$IncomeId,"Acc","Direct Loss",$project_id,$losDtl,0,$totalLoss,$IncomeBalance,0,$delivery_date,$dm_id);
	
	}
			
	$totalDeliveryAmount 	= $this->getTotalDeliveryAmount($dm_id,$voucher_no,$project_id);
	$netOrderAmount 		= getRequest('netOrderAmount');
	$totalDeliveryAmount 	= number_format($totalDeliveryAmount, 2, '.', '');
	$netOrderAmount 		= number_format($netOrderAmount, 2, '.', '');
	$epsilon = 0.00000; 
	if((abs($totalDeliveryAmount-$netOrderAmount) != $epsilon ) && (intval($totalDeliveryQty)==intval($totalOrderQty))){
		//$totalDeliveryAmount = $netOrderAmount; 
	}
	
	if( (abs($totalDeliveryAmount-$netOrderAmount) == $epsilon ) && (intval($totalDeliveryQty)==intval($totalOrderQty))){
	//======= Party Dr ======		
	 $DrAmount1  = $totalDeliveryAmount;
	 $PartyAcc_head = getRequest('customer');  $description = "Sales Delivery";
	 $totalPartyCR  = $soApp->getTotalCreditAmount($PartyAcc_head,getFromSession('project_id'));
	 $totalPartyDR  = $soApp->getTotalDebitAmount($PartyAcc_head,getFromSession('project_id'));
	 $PreviousPartyBalance = ($totalPartyDR-$totalPartyCR);					 
	 $PartyBalance  = (($totalPartyDR+$DrAmount1)-$totalPartyCR);					 
	 $comlistApp->saveAccJournal($voucher_no,$PartyAcc_head,"Customer","Buy Product",getFromSession('project_id'),$description,$DrAmount1,0,$PartyBalance,1,$created_date,$dm_id);	
	 
	 $consignee 		= getRequest('consignee');	
	 if($consignee !=""){
		 $totalPartyCR  = $soApp->getTotalCreditAmount($consignee,getFromSession('project_id'));
		 $totalPartyDR  = $soApp->getTotalDebitAmount($consignee,getFromSession('project_id'));
		 $PreviousPartyBalance = ($totalPartyDR-$totalPartyCR);					 
		 $PartyBalance  = (($totalPartyDR+$DrAmount1)-$totalPartyCR);					 
		 $this->saveRetailerJournal($voucher_no,$consignee,"Retailer","Buy Product",getFromSession('project_id'),$description,$DrAmount1,0,$PartyBalance,1,$created_date,$dm_id);		  
	 }
	 //=======Update Sales Master =====
	  
	 $PMsql = "SELECT voucher_no,discount,net_payble,paid_amount,due,item_delivery_amount,service_charge FROM ".SALES_MASTER_TBL." 
	 WHERE voucher_no ='".$voucher_no."' AND project_id = '$project_id'";
	 $PMrow 			= mysql_fetch_object(mysql_query($PMsql));		 
	 $total_received_amount	= $PMrow->paid_amount;
	 $existing_due 			= $PMrow->due;
	 $item_delivery_amount 	= $PMrow->item_delivery_amount;
	 $total_delivery_amount = ($totalDeliveryAmount+$item_delivery_amount);		 
	 
	 if($PreviousPartyBalance <0){
	  $actual_delivery_amount 	= $this->adjustCustomerPayble($voucher_no,getRequest('customer'),$totalDeliveryAmount,$dm_id); 
	  $adjustAmount = ($totalDeliveryAmount-$actual_delivery_amount);
	  $adjustAmount = "-$adjustAmount";
	  $present_due 			= ($existing_due+$actual_delivery_amount);
	 }else{
	  $actual_delivery_amount = $totalDeliveryAmount;
	  $adjustAmount = $PreviousPartyBalance;
	  $present_due 			= ($existing_due+$actual_delivery_amount);
	 }
	 $SDMUpSQL="UPDATE ".SALES_DELIVERY_MASTER_TBL." SET total_value='$totalDeliveryAmount',previour_balance='$PreviousPartyBalance',roa='$actual_delivery_amount' WHERE voucher_no='".$voucher_no."' AND
	  project_id = '$project_id' AND sales_delivery_master_id='$dm_id'";
	 mysql_query($SDMUpSQL);
	
	 $SMUpdate="UPDATE ".SALES_MASTER_TBL." SET net_payble='$total_delivery_amount',due='$present_due',item_delivery_amount='$total_delivery_amount',
	 adjust='$adjustAmount' WHERE voucher_no='".$voucher_no."' AND project_id = '$project_id'";
	 mysql_query($SMUpdate);
	 $this->updateSalesVoucher($voucher_no,$total_delivery_amount);
	 $customer = getRequest('customer');
	 
	 $Csql = "SELECT mobile FROM ".SUB_ACC_HEAD_TBL." WHERE sub_id ='".$customer."' AND project_id = '$project_id'";
	 $Crow = mysql_fetch_object(mysql_query($Csql));		 
	 $recipients	= $Crow->mobile;
	 if($recipients !=""){
		 if($PartyBalance >0){
			$LastPartyBalance = $PartyBalance." Dr";
		 }else{
			 $LastPartyBalance = abs($PartyBalance)." Cr";
		 }
		$message = "Dear Sir, Your Sales Invoice No ".$voucher_no." and delivery amount is ".$totalDeliveryAmount." TK. Now your outstanding balance is ".$LastPartyBalance.".";
	 	//$this->sendSMS(COMPANY_NAME,$recipients,$message);
		require_once(CLASS_DIR . '/common.list.class.php');
                $response = (new CommonList())->sendSMS($recipients, $message);
	 }
	 mysql_query("COMMIT;");
	 header("location:index.php?app=delivery_challan&cmd=print_vouchar&voucher_no=".$voucher_no."&sdm_id=".$dm_id);
	}else{
	mysql_query("ROLLBACK;");	
	$Stsql="DELETE FROM ".STOCK_LEDGER_TBL." WHERE voucher_no='".$voucher_no."' AND project_id='".$project_id."' AND delivery_id='$dm_id'";
	mysql_query($Stsql); 
	$Sditsql="DELETE FROM ".SALES_DELIVERY_CHALLAN_TBL." WHERE voucher_no='".$voucher_no."' AND project_id='".$project_id."' 
	AND delivery_master_id='$dm_id'";
	mysql_query($Sditsql);
	$Sdmtsql="DELETE FROM ".SALES_DELIVERY_MASTER_TBL." WHERE voucher_no='".$voucher_no."' AND project_id='".$project_id."' 
	AND sales_delivery_master_id='$dm_id'";
	mysql_query($Sdmtsql);
	header("location:index.php?app=sales.delivery&cmd=delivery&voucher_no=".$voucher_no."&msg=Delivery Amount($totalDeliveryAmount) is not equal to Order Amount($netOrderAmount) !!! Please Try again");	
	}
  } //End of the function savePaymentDetails()
  function sendSMS($sender,$recipients,$message){
	$postUrl = "http://193.105.74.59/api/sendsms/xml";
	
	// XML-formatted data
	$xmlString =
	"<SMS>
	<authentification>
		<username>parksoft</username>
		<password>S1CCqAPf</password>
	</authentification>
	<message>
		<sender>$sender</sender>
		<text>$message</text>
	</message>
	<recipients>
	<gsm>$recipients</gsm>
	<gsm>8801841325588</gsm>
	</recipients>
	</SMS>";
	
	// previously formatted XML data becomes value of "XML" POST variable
	$fields = "XML=" . urlencode($xmlString);
	
	// in this example, POST request was made using PHP's CURL
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $postUrl);
	curl_setopt($ch, CURLOPT_POST, 1);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
	
	// response of the POST request
	$response = curl_exec($ch);
	curl_close($ch);  
  }
  function saveRetailerJournal($voucher_no,$sub_id,$head_type,$transaction_type,$project_id,$description,$DR=NULL,$CR=NULL,$balance,$status,$created_date,$delivery_id=NULL){
		$head_type	= "Retailer";   $created_by = getFromSession('userid'); if($delivery_id==""){ $delivery_id=0;}
		$sql = "INSERT INTO ".ACCOUNT_JOURNAL_TBL." (voucher_no,delivery_id,created_date,sub_id,head_type,transaction_type,project_id,description,dr,cr,balance,status,created_by)
		 VALUES('".$voucher_no."','".$delivery_id."','".$created_date."','".$sub_id."','".$head_type."','".$transaction_type."','".$project_id."','".$description."','".$DR."','".$CR."','".$balance."','".$status."','".$created_by."')";
		mysql_query($sql); 
  }
  function updateSalesVoucher($voucher_no,$totalDeliveryAmount){
     $project_id = getFromSession('project_id');
  	 $DrVUpdate="UPDATE ".DEVIT_VOUCHAR_TBL." SET debit='$totalDeliveryAmount' WHERE voucher_no='".$voucher_no."' AND project_id = '$project_id'";
	 mysql_query($DrVUpdate);	 
	 $CrVUpdate="UPDATE ".CREDIT_VOUCHAR_TBL." SET credit='$totalDeliveryAmount' WHERE voucher_no='".$voucher_no."' AND project_id = '$project_id'";
	 mysql_query($CrVUpdate);
  }
  function adjustCustomerPayble($NewVoucherNo,$account_head,$CrAmount,$delivery_id){
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
		 $clistApp->saveInvoiceAdjustHistory($NewVoucherNo,$delivery_id,$project_id,DEVIT_VOUCHAR_TBL,$voucher_no,$adjustAmount,"-");
		}
	 }elseif(($CrAmount < $srow->due) && ($srow->due >0 && $CrAmount >0)){
		$presentDue = ($srow->due - $CrAmount);
		$PaidAmount = ($srow->paid_amount + $CrAmount);
		if($PaidAmount < $srow->debit){
		 $adjustAmount = $CrAmount; $CrAmount=0;
		 $pusql2="UPDATE ".DEVIT_VOUCHAR_TBL." SET paid_amount='".$PaidAmount."',due='$presentDue',`status`=0 WHERE voucher_no='$voucher_no'";
		 mysql_query($pusql2);
		 $clistApp->saveInvoiceAdjustHistory($NewVoucherNo,$delivery_id,$project_id,DEVIT_VOUCHAR_TBL,$voucher_no,$adjustAmount,"-");
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
			$clistApp->saveInvoiceAdjustHistory($NewVoucherNo,$delivery_id,$project_id,SALES_RETURN_PAYBLE_TBL,$return_id,$existing_due,"-");
			} 
		}elseif(($CrAmount<$existing_due)){					
			if($existing_due>0 && $CrAmount>0){
			$totalpaid 	 = ($paid_amount + $CrAmount); 
			$present_due = ($existing_due - $CrAmount);
			$adjustAmount = $CrAmount; $CrAmount = 0;
			$SRPUpdate="UPDATE ".SALES_RETURN_PAYBLE_TBL." SET paid_amount=$totalpaid,due=$present_due WHERE return_id='$return_id' AND project_id='$project_id'";
			mysql_query($SRPUpdate);
			$clistApp->saveInvoiceAdjustHistory($NewVoucherNo,$delivery_id,$project_id,SALES_RETURN_PAYBLE_TBL,$return_id,$adjustAmount,"-");
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
  
  function getTotalDeliveryAmount($delivery_mid,$voucher_no,$project_id){
   $SDCSql="SELECT SUM(total_amount) as delivery_Amount FROM ".SALES_DELIVERY_CHALLAN_TBL." WHERE delivery_master_id ='".$delivery_mid."' AND project_id = '$project_id' AND 
   voucher_no='$voucher_no'";
   $SDCRes = mysql_query($SDCSql);
   $srprow = mysql_fetch_object($SDCRes);
   if($srprow->delivery_Amount>0){
   return $srprow->delivery_Amount;
   }else{
   return 0;
   }
  }
  function saveDeliveryChallanDtl($sd_id,$dm_id,$voucher_no,$pvoucher_no,$delivery_point,$consignee,$project_id,$catagory,$brand_id,$product,$serial,$warranty,$m_unit,$unit_price,$discount_per_qty,$discount_amount,$overall_discount,$overall_discount_amount,$unit_profit,$delivery_qty,$free_qty,$total_amount,$created_by){
		
	$sql = "INSERT INTO ".SALES_DELIVERY_CHALLAN_TBL." (delivery_master_id,voucher_no,pvoucher_no,sal_detail_id,delivery_point,consignee,project_id,catagory,brand_id,product,serial,warranty,m_unit,unit_price,discount_per_qty,discount_amount,overall_discount,overall_discount_amount,unit_profit,delivery_qty,total_bag,total_amount,created_by) 
	VALUES('".$dm_id."','".$voucher_no."','".$pvoucher_no."','".$sd_id."','".$delivery_point."','".$consignee."','".$project_id."','".$catagory."','".$brand_id."','".$product."','".$serial."','".$warranty."','".$m_unit."','".$unit_price."','".$discount_per_qty."','".$discount_amount."','".$overall_discount."','".$overall_discount_amount."','".$unit_profit."','".$delivery_qty."','".$free_qty."','".$total_amount."','".$created_by."')";
	mysql_query($sql);
  }
	
  function saveProfit($voucher_no,$product,$dm_id,$unit_profit,$deliveryQty,$freeQty,$sales_price,$purchase_price,$discount_amount,$created_date){
  	require_once(CLASS_DIR.'/common.list.class.php');	
	$comlistApp 				= new CommonList();
	if($unit_profit>=0){
	$totalProfite = (($deliveryQty-$freeQty)*$unit_profit);	
	//========= Direct Income Dr ==========
	$SalesIncomeId 	 = $comlistApp->getProductSalesIncomeId(getFromSession('project_id'));
	$totalSalesIncomeCR = $this->getTotalCreditAmount($SalesIncomeId,getFromSession('project_id'));
	$totalSalesIncomeDR = $this->getTotalDebitAmount($SalesIncomeId,getFromSession('project_id'));					 
	$SalesIncomeBalance = (($totalSalesIncomeDR+$totalProfite)-$totalSalesIncomeCR);	
	$salesDtl = "Income from product($product) sales, delivery no- $dm_id";			 
	$comlistApp->saveAccJournal($voucher_no,$SalesIncomeId,"Acc","Direct Incomes",getFromSession('project_id'),$salesDtl,$totalProfite,0,$SalesIncomeBalance,0,$created_date,$dm_id);
	}else{
	$totalProfite = (($deliveryQty-$freeQty)*$unit_profit);	
	$totalProfite = abs($totalProfite);
	//========= Direct Income Cr ==========
	$SalesIncomeId 	 = $comlistApp->getProductSalesIncomeId(getFromSession('project_id'));
	$totalSalesIncomeCR = $this->getTotalCreditAmount($SalesIncomeId,getFromSession('project_id'));
	$totalSalesIncomeDR = $this->getTotalDebitAmount($SalesIncomeId,getFromSession('project_id'));					 
	$SalesIncomeBalance = ($totalSalesIncomeDR-($totalSalesIncomeCR+$totalProfite));	
	$salesDtl = "loss from product($product) sales, delivery no- $dm_id";				 
	$comlistApp->saveAccJournal($voucher_no,$SalesIncomeId,"Acc","Direct Loss",getFromSession('project_id'),$salesDtl,0,$totalProfite,$SalesIncomeBalance,0,$created_date,$dm_id);
	}
  }
  //========= make_seed function 4 gatepass ========
  function make_seed(){
	   list($usec, $sec) = explode(' ', microtime());
	   return (float) $sec + ((float) $usec * 100000);
  }	
  function saveStockJournal($pvoucher_no,$voucher_no,$project_id,$store_id,$product_id,$serial,$warranty,$note,$unit_price=NULL,$m_unit,$DR=NULL,$CR=NULL,$balance,$create_date=NULL,$sdmid=NULL){
  	$created_by = getFromSession('userid');
	$sql="INSERT INTO ".STOCK_LEDGER_TBL." (po_no,voucher_no,project_id,store_id,delivery_id,product_id,serial,warranty,note,unit_price,m_unit,dr,cr,balance,created_by,create_date) 
	VALUES('".$pvoucher_no."','".$voucher_no."','".$project_id."','".$store_id."','".$sdmid."','".$product_id."','".$serial."','".$warranty."','".$note."','".$unit_price."','".$m_unit."','".$DR."','".$CR."','".$balance."','".$created_by."','".$create_date."')";
	mysql_query($sql);
  }
  
  function getSalesMasterInfo($id,$delivery_master_id=NULL){	
		$project_id     = getFromSession('project_id');  
		$SQLMain = "";  
		$SQL = "
		SELECT pm.voucher_no,pm.delivery_point,pm.po_no,pm.wo_no,p.project_name,p.location,pm.customer,COALESCE((s.sub_id), (sp.supplier_code)) as sub_id,COALESCE((s.sub_head_name), (sp.name)) as sub_head_name,COALESCE((s.head_details), (sp.address)) as head_details,COALESCE((s.phone), (sp.phone)) as phone,COALESCE((s.mobile), (sp.mobile)) as mobile,COALESCE((s.email), (sp.email)) as email,COALESCE((s.att_name1), (sp.contact_person)) as att_name1,COALESCE((s.att_designation1), (sp.designation)) as att_designation1,COALESCE((s.att_mobile1), (sp.contact_person_mobile)) as att_mobile1,pm.gate_pass,pm.track_no,pm.salse_type,pm.total_value,DATE_FORMAT(pm.sales_date,'%d %b %y' ) as sales_date,pm.service_charge,pm.mode_of_payment,c.curr_symble,pm.bank_name,pm.acc_no,pm.check_no, pm.general_discount_percent,pm.general_discount_amount,pm.exclusive_discount_percent,pm.exclusive_discount_amount,pm.additional_discount,pm.product_discount, pm.discount,pm.net_payble,pm.paid_amount,pm.due,pm.item_delivery_amount,pm.return_amount,pm.ref_no,pm.created_date,DATE_FORMAT(pm.created_date,'%d %b %y' ) as date,pm.reference,pm.commission_slot,pm.total_commission,pm.commission_adv_paid,pm.commission_total_paid,pm.commission_total_due,pm.commission_status";
		if($delivery_master_id !=""){
		  $SQL.=",DATE_FORMAT(sdm.delivery_date,'%d %b %y' ) as delivery_date, sdm.challan_no, sdm.consignee ";
		}
		
		$SQLTBL="
		FROM ".SALES_MASTER_TBL." pm
		LEFT JOIN ".SUB_ACC_HEAD_TBL." s ON BINARY s.sub_id =pm.customer
		LEFT JOIN ".SUPPLIER_TBL." sp ON BINARY sp.supplier_code = pm.customer
		LEFT JOIN ".PROJECT_TBL." p ON p.project_id  =pm.project_id
		LEFT JOIN ".CURRENCY_TBL." c ON c.currency_id  =pm.currency
		";
		if($delivery_master_id !=""){
		  $SQLTBL.=" LEFT JOIN ".SALES_DELIVERY_MASTER_TBL." sdm ON BINARY sdm.voucher_no = pm.voucher_no ";
		}
		
		$SQLWhere =" WHERE pm.project_id = '".$project_id."' AND pm.voucher_no = '".$id."'";		
		
		if($delivery_master_id !=""){
		  $SQLWhere.=" AND sdm.sales_delivery_master_id='$delivery_master_id'";
		}
		$SQLMain = 	$SQL.$SQLTBL.$SQLWhere." GROUP BY pm.voucher_no";				
		echo $SQLMain;
		$res     = query($SQLMain);		
		$data    = array();
		
		if(count($res) >0){
			foreach($res as $i=>$v){
				$data[$i] = $v;             
			}
		}
		  //dumpVar($data);
		  return $data[0];
   } 
        
   function getProductList($id,$delivery_master_id) {  
		$info           = array();    
		$info['table']  = SALES_DETAILS_TBL.' sd,'.SALES_DELIVERY_CHALLAN_TBL.' sdi,'.CURRENCY_TBL.' c,'.PRODUCT_TBL.' p,'.BRAND_TBL.' b';	
		$info['fields'] = array('sd.sal_detail_id','sd.voucher_no','sd.project_id','sd.catagory','sd.serial','sd.warranty','b.brand_name','sd.product','sd.details','p.product_name','p.product_desc','sd.m_unit','sd.unit_price','c.curr_symble','sd.discount_per_qty','sd.discount_amount','sd.qty','SUM(sdi.delivery_qty) as delivery_qty','sdi.total_amount as delivery_item_amount','sd.delivery_qty as totaldelivery_qty','sd.total_bag','sd.total','sd.created_time');
		
		$sql="sd.product = sdi.product AND sd.product = p.product_id AND p.brand_code = b.brand_id AND sd.currency = c.currency_id AND sd.voucher_no = '$id' 
		AND sdi.delivery_master_id='$delivery_master_id'";
		
		$info['where']  = $sql;	  	
	    $info['groupby']= array("sd.sal_detail_id");
		$info['orderby']= array("sd.product asc");
		//$info['debug']= true;
		$result         = select($info);
		$data           = array();
		$cnt 			= count($result);  	     
		if($cnt){
			foreach($result as $value){				
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
   function getCatagoryList()
   {	
	  $project_id  		= getFromSession('project_id');
      $data 			= array(); 
      $info        		=  array();
      $info['table']	= CATAGORY_TBL;
	  $info['where']   = "project_id = '$project_id'";
      $res            	=	 select($info);      

      if(count($res))
      {
         foreach($res as $i=>$v)
         {
            $data[$i] = $v;             
         }
      }
	  //dumpVar($data);
      return $data;	
   }
 
  function getCustomerList()
  {		  
	  $project_id  		= getFromSession('project_id');
      $data 			= array(); 
      $info        		= array();
      $info['table']	= SUB_ACC_HEAD_TBL;
	  $info['where']  	= "head_type = 'Customer' AND project_id='".$project_id."'";	  	
      $res            	= select($info);      
      if(count($res))
      {
         foreach($res as $i=>$v)
         {
            $data[$i] = $v;             
         }
      }
	  //dumpVar($data);
      return $data;	
   }     

	function loadStockQty($product_id){
	  $project_id = getFromSession('project_id');  
	  $voucher_no = $_REQUEST['voucher_no'];		 
	  $totalCr = $this->getTotalCreditStock($product_id,$project_id);
	  $totalDr = $this->getTotalDebitStock($product_id,$project_id);
	  $balanceQty = $totalDr - $totalCr;
	  $sql = "SELECT SUM(delivery_qty) AS delivery_qty FROM ".SALES_DELIVERY_CHALLAN_TBL." WHERE product ='$product_id' AND project_id = '$project_id' AND voucher_no = '$voucher_no'";
	  $res = mysql_query($sql);
	  if(mysql_num_rows($res)>0){
	  $row = mysql_fetch_object($res);
	  if($row->delivery_qty==""){$delivery_qty=0;}else{$delivery_qty=$row->delivery_qty;}
	  }else{
	  $delivery_qty=0;
	  }
	  $balanceQty = $balanceQty - $delivery_qty;
	  echo $balanceQty;	
   }
   function getCashId($project_id){
		$sql = "SELECT sub_id FROM ".SUB_ACC_HEAD_TBL." WHERE head_type = 'Cash' AND project_id = '$project_id'";
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
      
} // End class
?>
