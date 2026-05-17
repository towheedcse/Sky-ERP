<?php
require_once('journal.class.php');
class Sales extends Journal
{
   function run(){         
      $cmd = getRequest('cmd');
	  $u_t_id = getFromSession('u_type_id');
      if( ($u_t_id ==101) || ($u_t_id == 102) || ($u_t_id == 103)) // 101 = sysadmin, 102 = admin
      {
      	switch ($cmd){
      	   case 'add'					: $this->showEditor(); break;
		   case 'edit'					: $this->showEditor(); break;
		   case 'sal_dtl'				: $this->showEditor4SalesDetails(); break; // deleted
		   case 'pending_sales'			: $this->showEditor4SalesDetails(); break;
		   case 'com_list'				: $this->showEditor4SalesCommission(); break;
      	   case 'loadProduct'  			: $this->loadProduct4Catagory(trim(getRequest('catagory_id'))); break; 		    
      	   case 'loadSOInfo'  			: $this->loadSOInfo(trim(getRequest('po_no'))); break;   
      	   case 'get_uprice'  			: $this->loadUnitePrice(trim(getRequest('product_id'))); break; 
		   case 'get_serial'  			: $this->loadProductSerial(trim(getRequest('product_id'))); break; 
      	   case 'get_stock_qty'  		: $this->loadStockQty(trim(getRequest('product_id'))); break; 	 
		   case 'save_sales'			: $this->saveSalesItem(); break;
		   case 'print_vouchar'			: $screen = $this->showPrintEditor($msg); break;  
		   case 'srcwarranty'			: $this->showWarrantyEditor($msg); break; 
		   case 'delete'             	: $screen = $this->deleteRecord(getRequest('id')); break;
      	   default                   	:$cmd = 'list'; $screen = $this->showEditor();   break;
      	}
      }elseif($u_t_id == 101) // 101 = sysadmin, 102 = admin
      {
      	switch ($cmd){
      	   case 'add'					: $this->showEditor(); break;
		   case 'edit'					: $this->showEditor(); break;
		   case 'admin_sal_dtl'			: $this->showAllCompaniesSalesDetails(); break;		   
		   case 'sal_dtl'				: $this->showEditor4SalesDetails(); break;
		   case 'pending_sales'			: $this->showEditor4SalesDetails(); break;
		   case 'com_list'				: $this->showEditor4SalesCommission(); break;
      	   case 'loadProduct'  			: $this->loadProduct4Catagory(trim(getRequest('catagory_id'))); break; 		    
      	   case 'loadSOInfo'  			: $this->loadSOInfo(trim(getRequest('po_no'))); break;   
      	   case 'get_uprice'  			: $this->loadUnitePrice(trim(getRequest('product_id'))); break; 
		   case 'get_serial'  			: $this->loadProductSerial(trim(getRequest('product_id'))); break; 
      	   case 'get_stock_qty'  		: $this->loadStockQty(trim(getRequest('product_id'))); break; 	 
		   case 'save_sales'			: $this->saveSalesItem(); break;
		   case 'print_vouchar'			: $screen = $this->showPrintEditor($msg); break;  
		   case 'srcwarranty'			: $this->showWarrantyEditor($msg); break;  
		   case 'delete'             	: $screen = $this->deleteRecord(getRequest('id')); break;
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
   	  require_once(CLASS_DIR.'/common.list.class.php');	
	  $clistApp = new CommonList(); 	  
	  $voucher_no 	= getRequest('voucher_no');  
	  if ($voucher_no!="") {
         	 $advArr 		= $this->getSalesMasterInfo($voucher_no);
         	 $advArr 		= parseThisValue($advArr); 
		 $data   		= array_merge(array(), $advArr); 
		 $data['item_list']	= $this->getProductList($voucher_no);
		 
		 $data['message'] 	= $msg;
		 $data['cmd']     	= getRequest('cmd');
		 require_once(SALES_VOUCHAR_SKIN);      
		 return true;
	 }else{
		require_once(PRINT_VOUCHAR_SKIN);
	  }
   }
   function showWarrantyEditor($msg=NULL){
   	 $voucher_no 	= getRequest('voucher_no');  
	 $serial 		= getRequest('serial');  
	 $sales_date1 	= getRequest('sales_date1');  
	 $sales_date2 	= getRequest('sales_date2');
	 $data                	= array();
	 $data['cmd']     	= getRequest('cmd');
	 if($voucher_no!="" || $serial!="" || $sales_date1!="" || $sales_date2!="") {
		 $data['record_list']   = $this->getSalesWarrantyList($voucher_no,$serial,$sales_date1,$sales_date2,getRequest('from'),getRequest('to'));
	  	 $data['totalrecord']	= $this->getTotalSalesWarrantyList($voucher_no,$serial,$sales_date1,$sales_date2);			 
		 require_once(SALES_WARRANTY_LIST_SKIN);      
		 return $data[0];
	 }else{
		require_once(SEARCH_WARRANTY_SKIN);
	 }
   }  
   function showEditor($msg = null) {
   	   $data                	= array();
       	   require_once(CLASS_DIR.'/common.list.class.php');	
	   $comListApp 	= new CommonList();
	   $data['customer_list'] 	= $this->getCustomerList();	
	   $data['reference_list'] 	= $comListApp->getReferenceList();	
	   $data['cat_list'] 		= $this->getCatagoryList();		   
	   $data['currency_list']       = $this->getCurrencyList();	      
	   $data['brand_list'] 		= $comListApp->getBrandList();	
	   $data['claim_list'] 		= $comListApp->getClaimList();   	
	   $data['area_list'] 		= $comListApp->getAreaList();      	
	   $data['depo_list'] 		= $comListApp->getDeliveryPointList();      	
	   $data['retailer_list'] 	= $comListApp->getRetailerList();  
	 	
	   $data['cmd']         	= getRequest('cmd');   
	   require_once(CURRENT_APP_SKIN_FILE); 
	   return $data[0];
   }

  function insertSalesDetails($voucher_no){
        require_once(CLASS_DIR.'/common.list.class.php');	
	$comlistApp 				= new CommonList();
	$sales_date = formatDate(getRequest('sales_date'));
	$requestdata 				= array();
	$arr_catagory_product_id	= array();
	$project_id  				= getFromSession('project_id');  $currency 	= getRequest('currency');
	$arr_catagory_product_id	= getRequest('input_catagory_product_id');
	$arr_brand        			= getRequest('input_brand');     $arr_pdetails  = getRequest('input_pdetails');
	$arr_warranty        		= getRequest('input_warranty');
	$arr_pvno        			= getRequest('input_pvoucher_no');
	$arr_m_unit        			= getRequest('input_m_unit');
	$arr_unit_price				= getRequest('input_unit_price');
	$arr_qty      				= getRequest('input_qty');
	$arr_total_bag      		= getRequest('input_total_bag');
	$arr_total_value       		= getRequest('input_total_value');
	$total_value = getRequest('total_value');  $discount = getRequest('discount'); $discount_persent = (($discount/$total_value)*100);
	for($i=0;$i<count($arr_catagory_product_id);$i++){
	  $catagory_product_sep 	= $arr_catagory_product_id[$i];		
	  $requestdata['project_id']= $project_id;       	  
	  for($j=0;$j<count($catagory_product_sep);$j++){
			$catagory_product = explode("###",$catagory_product_sep);
			$catagoryid  	  = array();   $productid   = array();  $brandid  = array();  $serialid  = array();  $purchaseNo = array();	  
			$catagoryid['c']  = $catagory_product[0];	$brandid['b']  = $catagory_product[1];				
			$productid['p']   = $catagory_product[2];	$serialid['s'] = $catagory_product[3];			
			$purchaseNo['po'] = $catagory_product[4];
		}
	   foreach($catagoryid as $val){
			$requestdata['catagory'] = $val;	
	   }
	   foreach($brandid as $val){
			$requestdata['brand_id']= $val; $brand_id = $val;
	   }	
	   foreach($productid as $val){
			$requestdata['product'] =$val;	
			$product_id				=$val;
	   }		   	
	   foreach($serialid as $val){
			$requestdata['serial'] =$val;	
			$serial	=$requestdata['serial'];
	   }	   
	   foreach($purchaseNo as $val){
			$requestdata['pvoucher_no']= $val; $pvoucher_no = $val;
	   }    
	   foreach($arr_pdetails as $key => $val){
		  if($catagory_product_sep==$key){
			$requestdata['details'] = $val;
		  }
	   }
	   foreach($arr_m_unit as $key => $val){
		  if($catagory_product_sep==$key){
			$requestdata['m_unit'] = $val;	
		  }
	   }
	   foreach($arr_warranty as $key => $val){
		  if($catagory_product_sep==$key){
			$requestdata['warranty'] = $val;  
			$warranty = $requestdata['warranty']; 
		  }
	   }
	   foreach($arr_unit_price as $key => $val){
		  if($catagory_product_sep==$key){
			$requestdata['unit_price'] = $val;	
		  }
	   }
	   foreach($arr_qty as $key => $val){
			if($catagory_product_sep==$key){
				 $requestdata['qty'] = $val;  $requestdata['delivery_qty'] = $val;	 $productQty  = $val;
			}
	   }
	   foreach($arr_total_bag as $key => $val){
			if($catagory_product_sep==$key){
				 $requestdata['total_bag'] = $val;	
			}
	   }	
	   foreach($arr_total_value as $key => $val){
		  if($catagory_product_sep==$key){
			 $requestdata['total'] = $val; 	
		  }
	   }
	   $perQtyAmount = ($requestdata['total']/$productQty);
	   $PUSql="SELECT pur_detail_id,unit_price,sales_qty FROM ".PURCHASE_DETAILS_TBL." WHERE product='$product_id' AND brand_id='$brand_id' 
	   AND project_id='$project_id' AND voucher_no='$pvoucher_no' AND serial='$serial'";
	   $Prorow = mysql_fetch_object(mysql_query($PUSql));
	   $requestdata['purchase_price'] = $Prorow->unit_price;
	   $pur_detail_id 				  = $Prorow->pur_detail_id;	   
	   
	   $TTLSalesQty = ($Prorow->sales_qty+$productQty);
	   $customer    = getRequest('customer');
	   $CSql="SELECT district,area FROM ".SUB_ACC_HEAD_TBL." WHERE head_type='Customer' AND sub_id='$customer' AND project_id='$project_id' ";
	   $Crow = mysql_fetch_object(mysql_query($CSql));
	   $requestdata['district'] = $Crow->district;  $requestdata['area']  = $Crow->area;
	   $requestdata['discount_per_qty']= $discount_persent;
	   $requestdata['discount_amount'] = (($perQtyAmount/100)*$discount_persent);
	   $unit_profit = ($requestdata['unit_price']-$requestdata['purchase_price']);
	   $unit_profit = ($unit_profit-$requestdata['discount_amount']);
	   $requestdata['unit_profit']  = $unit_profit;
	   $requestdata['created_by'] 	= getFromSession('userid');
	   $requestdata['created_date'] = date('Y-m-d h:i:s');  
	   $project_id					= getFromSession('project_id'); 
	   $requestdata['project_id']   = $project_id;
	   $requestdata['voucher_no']   = $voucher_no;
	   $requestdata['customer']     = getRequest('customer');
	   $requestdata['reference']    = getRequest('reference');
	   $info        	=  array();
	   $info['table']	= SALES_DETAILS_TBL;
	   $info['data'] 	= $requestdata;      
	   $res = insert($info);	
	   if($res){
		if($unit_profit>=0){
		$totalProfite = ($unit_profit*$productQty);	
		//========= Direct Income Dr ==========
		$SalesIncomeId 	 = $comlistApp->getProductSalesIncomeId(getFromSession('project_id'));
		$totalSalesIncomeCR = $this->getTotalCreditAmount($SalesIncomeId,getFromSession('project_id'));
		$totalSalesIncomeDR = $this->getTotalDebitAmount($SalesIncomeId,getFromSession('project_id'));					 
		$SalesIncomeBalance = (($totalSalesIncomeDR+$totalProfite)-$totalSalesIncomeCR);	
		$salesDtl = "Income from product sales";			 
		$comlistApp->saveAccJournal($voucher_no,$SalesIncomeId,"Acc","Direct Incomes",getFromSession('project_id'),$salesDtl,$totalProfite,0,$SalesIncomeBalance,0,$sales_date);
		}else{
		$totalProfite = ($unit_profit*$productQty);
		$totalProfite = abs($totalProfite);
		//========= Direct Income Cr ==========
		$SalesIncomeId 	 = $comlistApp->getProductSalesIncomeId(getFromSession('project_id'));
		$totalSalesIncomeCR = $this->getTotalCreditAmount($SalesIncomeId,getFromSession('project_id'));
		$totalSalesIncomeDR = $this->getTotalDebitAmount($SalesIncomeId,getFromSession('project_id'));					 
		$SalesIncomeBalance = ($totalSalesIncomeDR-($totalSalesIncomeCR+$totalProfite));	
		$salesDtl = "loss from product sales";				 
		$comlistApp->saveAccJournal($voucher_no,$SalesIncomeId,"Acc","Direct Incomes",getFromSession('project_id'),$salesDtl,0,$totalProfite,$SalesIncomeBalance,0,$sales_date);
		}
		$pdusql = "UPDATE ".PURCHASE_DETAILS_TBL." SET sales_qty='".$TTLSalesQty."' WHERE pur_detail_id='$pur_detail_id'";
		mysql_query($pdusql);
		if(getRequest('salse_type')=="Sales"){
		//=== Stock Cr =====
	        $StockAmount = ($requestdata['purchase_price']*$productQty);
		$StockId 	 = $comlistApp->getStockId(getFromSession('project_id'));
		$totalStockCr  = $this->getTotalCreditAmount($StockId,getFromSession('project_id'));
		$totalStockDr  = $this->getTotalDebitAmount($StockId,getFromSession('project_id'));					 
		$StockBalance  = ($totalStockDr-($totalStockCr+$StockAmount));	$description = "Sales Product";					 
		$comlistApp->saveAccJournal($voucher_no,$StockId,"Stock","Sales Product",getFromSession('project_id'),$description,0,$StockAmount,$StockBalance,0,$sales_date);
		
		$totalCR  = $this->getTotalCreditStock($product_id,getFromSession('project_id'));
		$totalDR  = $this->getTotalDebitStock($product_id,getFromSession('project_id'));					 
		$balance  = ($totalDR - ($totalCR+$productQty));	
		$sales_date = formatDate(getRequest('sales_date'));					
		$this->saveStockJournal($pvoucher_no,$voucher_no,$project_id,$product_id,$serial,$warranty,$requestdata['unit_price'],$requestdata['m_unit'],0,$productQty,$balance,$sales_date);
		} // end salse_type
	  } // $res insert
   	} 
 } //End of the function
 
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
		$requestdata['check_issue_date'] = "";

		$requestdata['account_head']     	= $this->getCashId(getFromSession('project_id')); 
		$requestdata['debit']        		= getRequest('paid_amount'); 
		$requestdata['credit']        		= 0;     
		$requestdata['head_type']     		= "Acc";   
	  }elseif($mode_of_payment=="Recievable"){
		$requestdata['bank_name'] = "";
		$requestdata['acc_no'] = "";
		$requestdata['check_no'] = "";
		$requestdata['check_issue_date'] = "";
		$requestdata['account_head']     	= getRequest('customer');  
		$requestdata['debit']        		= getRequest('due'); 
		$requestdata['credit']        		= 0;     
		$requestdata['head_type']     		= "Acc"; 
	 }
	  $requestdata['transaction_type']  = "Received";
	  $requestdata['project_id']        = getFromSession('project_id');    
	  $requestdata['created_by']        = getFromSession('userid'); 
	  $requestdata['created_date']      = formatDate(getRequest('sales_date')); 
	  $voucher_no = $this->createVoucharID();
	 if($voucher_no != -1){
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
		 if($mode_of_payment=="Cash"){ 
			if(getRequest('due')>0){
				 //=========== Receivable Dr ========
				 $fullReceivable		= getRequest('net_payble');
				 $receivable_head 		= $this->getRecievableId(getFromSession('project_id'));
				 $totalReceivableCR  	= $this->getTotalCreditAmount($receivable_head,getFromSession('project_id'));
				 $totalReceivableDR  	= $this->getTotalDebitAmount($receivable_head,getFromSession('project_id'));					 
				 $receivableBalance  	= (($totalReceivableDR+$fullReceivable)-$totalReceivableCR);					 
				 $this->saveAccountJournal($voucher_no,$receivable_head,"Acc",getFromSession('project_id'),getRequest('description'),$fullReceivable,0,$receivableBalance,1,$created_date);	
				//======= Party Dr ======			
				$fullReceivable = getRequest('net_payble');
				$PartyAcc_head = getRequest('customer');  
				$totalPartyCR  = $this->getTotalCreditAmount($PartyAcc_head,getFromSession('project_id'));
				$totalPartyDR  = $this->getTotalDebitAmount($PartyAcc_head,getFromSession('project_id'));					 
				$PartyBalance  = (($totalPartyDR+$fullReceivable)-$totalPartyCR);					 
				$this->saveAccountJournal($voucher_no,$PartyAcc_head,"Acc",getFromSession('project_id'),getRequest('description'),$fullReceivable,0,$PartyBalance,1,$created_date);	
				//========= Receivable Cr ==========	
				 $Receivable	= getRequest('paid_amount');
				 $rblAcc_head = $this->getRecievableId(getFromSession('project_id'));
				 $totalRblCR  = $this->getTotalCreditAmount($rblAcc_head,getFromSession('project_id'));
				 $totalRblDR  = $this->getTotalDebitAmount($rblAcc_head,getFromSession('project_id'));					 
				 $rblBalance  = ($totalRblDR-($totalRblCR+$Receivable));					 
				 $this->saveAccountJournal($voucher_no,$rblAcc_head,"Acc",getFromSession('project_id'),getRequest('description'),0,$Receivable,$rblBalance,1,$created_date);	//before 0
				//======== Party Cr ============== 
				$paidAmount = getRequest('paid_amount');
				$PartyAcc_head1 = getRequest('customer'); 
				$totalPartyCR1  = $this->getTotalCreditAmount($PartyAcc_head1,getFromSession('project_id'));
				$totalPartyDR1  = $this->getTotalDebitAmount($PartyAcc_head1,getFromSession('project_id'));					 
				$PartyBalance1  = ($totalPartyDR1-($totalPartyCR1+$paidAmount));					 
				$this->saveAccountJournal($voucher_no,$PartyAcc_head1,"Acc",getFromSession('project_id'),getRequest('description'),0,$paidAmount,$PartyBalance1,1,$created_date);	
				//============== Cash Dr ===============
				 $acc_head = $this->getCashId(getFromSession('project_id'));
				 $totalCR  = $this->getTotalCreditAmount($acc_head,getFromSession('project_id'));
				 $totalDR  = $this->getTotalDebitAmount($acc_head,getFromSession('project_id'));					 
				 $balance  = (($totalDR+$DrAmount)-$totalCR);					 
				 $this->saveAccountJournal($voucher_no,$acc_head,"Acc",getFromSession('project_id'),getRequest('description'),$DrAmount,0,$balance,1,$created_date);	
			}elseif(getRequest('due')==0){		
				//======= Party Dr ======			
				$fullReceivable = getRequest('net_payble');
				$PartyAcc_head = getRequest('customer');  
				$totalPartyCR  = $this->getTotalCreditAmount($PartyAcc_head,getFromSession('project_id'));
				$totalPartyDR  = $this->getTotalDebitAmount($PartyAcc_head,getFromSession('project_id'));					 
				$PartyBalance  = (($totalPartyDR+$fullReceivable)-$totalPartyCR);					 
				$this->saveAccountJournal($voucher_no,$PartyAcc_head,"Acc",getFromSession('project_id'),getRequest('description'),$fullReceivable,0,$PartyBalance,1,$created_date);	
				//======= Party Cr ======			
				$CrAmount1 = getRequest('paid_amount');
				$PartyAcc_head = getRequest('customer');  
				$totalPartyCR  = $this->getTotalCreditAmount($PartyAcc_head,getFromSession('project_id'));
				$totalPartyDR  = $this->getTotalDebitAmount($PartyAcc_head,getFromSession('project_id'));					 
				$PartyBalance  = ($totalPartyDR-($totalPartyCR+$CrAmount1));					 
				$this->saveAccountJournal($voucher_no,$PartyAcc_head,"Acc",getFromSession('project_id'),getRequest('description'),0,$CrAmount1,$PartyBalance,1,$created_date);	
				//============== Cash Dr ===============
				$DrAmount1 = $CrAmount1;
				$acc_head = $this->getCashId(getFromSession('project_id'));
				$totalCR  = $this->getTotalCreditAmount($acc_head,getFromSession('project_id'));
				$totalDR  = $this->getTotalDebitAmount($acc_head,getFromSession('project_id'));					 
				$balance  = (($totalDR+$DrAmount1)-$totalCR);					 
				$this->saveAccountJournal($voucher_no,$acc_head,"Acc",getFromSession('project_id'),getRequest('description'),$DrAmount1,0,$balance,1,$created_date);	
			}
		 }elseif($mode_of_payment=="Check"){
		//====== save payable_check ======
		$this->savePayableCheck($voucher_no,$voucher_no,"Received",getRequest('paid_amount'));
		//======= Party Dr ======			
		$fullReceivable = getRequest('net_payble');
		$PartyAcc_head = getRequest('customer');  
		$totalPartyCR  = $this->getTotalCreditAmount($PartyAcc_head,getFromSession('project_id'));
		$totalPartyDR  = $this->getTotalDebitAmount($PartyAcc_head,getFromSession('project_id'));					 
		$PartyBalance  = (($totalPartyDR+$fullReceivable)-$totalPartyCR);					 
		$this->saveAccountJournal($voucher_no,$PartyAcc_head,"Acc",getFromSession('project_id'),getRequest('description'),$fullReceivable,0,$PartyBalance,1,$created_date);	
		//======= Party Cr ======			
		$CrAmount1 = getRequest('paid_amount');
		$PartyAcc_head = getRequest('customer');  
		$totalPartyCR  = $this->getTotalCreditAmount($PartyAcc_head,getFromSession('project_id'));
		$totalPartyDR  = $this->getTotalDebitAmount($PartyAcc_head,getFromSession('project_id'));					 
		$PartyBalance  = ($totalPartyDR-($totalPartyCR+$CrAmount1));					 
		$this->saveAccountJournal($voucher_no,$PartyAcc_head,"Acc",getFromSession('project_id'),getRequest('description'),0,$CrAmount1,$PartyBalance,1,$created_date);	
		}elseif($mode_of_payment=="Recievable"){
		//======= Party Dr ======			
		$DrAmount1 = getRequest('due');
		$PartyAcc_head = getRequest('customer');  
		$totalPartyCR  = $this->getTotalCreditAmount($PartyAcc_head,getFromSession('project_id'));
		$totalPartyDR  = $this->getTotalDebitAmount($PartyAcc_head,getFromSession('project_id'));					 
		$PartyBalance  = (($totalPartyDR+$DrAmount1)-$totalPartyCR);					 
		$this->saveAccountJournal($voucher_no,$PartyAcc_head,"Acc",getFromSession('project_id'),getRequest('description'),$DrAmount1,0,$PartyBalance,1,$created_date);	
		//========= Receivable Cr ==========	
		 $Receivable	= $DrAmount1;
		 $rblAcc_head = $this->getRecievableId(getFromSession('project_id'));
		 $totalRblCR  = $this->getTotalCreditAmount($rblAcc_head,getFromSession('project_id'));
		 $totalRblDR  = $this->getTotalDebitAmount($rblAcc_head,getFromSession('project_id'));					 
		 $rblBalance  = ($totalRblDR-($totalRblCR+$Receivable));					 
		 $this->saveAccountJournal($voucher_no,$rblAcc_head,"Acc",getFromSession('project_id'),getRequest('description'),0,$Receivable,$rblBalance,1,$created_date);		
		}	
		}
		return true;		
	  }else {	
		return false;
	  } 	 
}//EOFn  
function saveSalesItem(){
	mysql_query("START TRANSACTION;");
	$voucher_no = $this->saveDebitVouchar();	
 	$this->saveCreditVouchar($voucher_no);
	$this->insertSalesMaster($voucher_no);
	$this->insertSalesDetails($voucher_no); 
	mysql_query("COMMIT;");
	if($voucher_no!=""){
	header("location:index.php?app=sales&cmd=print_vouchar&voucher_no=".$voucher_no);	
	}else{
	header("location:index.php?app=sales&cmd=add");
	}
}
function make_seed(){
   list($usec, $sec) = explode(' ', microtime());
   return (float) $sec + ((float) $usec * 100000);
}
function insertSalesMaster($voucher_no){
	  $requestdata = array();
	  $project_id  = getFromSession('project_id');	
	  $requestdata = getUserDataSet(SALES_MASTER_TBL);	
	  if($mode_of_payment =="Check"){
		$requestdata['check_no'] = formatDate(getRequest('check_no'));
		$requestdata['check_issue_date'] = formatDate(getRequest('check_issue_date'));
	  }
	  $requestdata['transaction_type']  = "Received";  
	  $requestdata['item_delivery_amount'] = $requestdata['net_payble'];
	  if(getRequest('reference')!=""){
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
	  $commission_total_due = $total_commission;  
	  $requestdata['commission_slot'] 	= $commission_slot;
	  $requestdata['total_commission'] 	= $total_commission;
	  $requestdata['commission_total_due'] = $commission_total_due;
	  }
	  $customer    = getRequest('customer');
	  $CSql="SELECT district,area FROM ".SUB_ACC_HEAD_TBL." WHERE head_type='Customer' AND sub_id='$customer' AND project_id='$project_id' ";
	  $Crow = mysql_fetch_object(mysql_query($CSql));
	  $requestdata['district'] = $Crow->district;
	  $requestdata['area']	    = $Crow->area;
	  $requestdata['sales_date'] 		= formatDate(getRequest('sales_date')); 
	  $requestdata['voucher_no']        = $voucher_no;   
	  $requestdata['project_id']        = getFromSession('project_id');    
	  $requestdata['created_by']        = getFromSession('userid');	
	  $requestdata['created_date']      = date('Y-m-d h:i:s');
	  mt_srand($this->make_seed());
	  $gatepass = mt_rand();   
	  $requestdata['gate_pass']        = $gatepass;	
	  $info        		=  array();
	  $info['table']	= SALES_MASTER_TBL;
	  $info['data'] 	= $requestdata;     
	  $res = insert($info);
}

function saveAccountJournal($voucher_no,$sub_id,$head_type,$project_id,$description,$DR=NULL,$CR=NULL,$balance,$status=NULL,$purchare_date=NULL){	
	$rsql= "SELECT head_type FROM ".SUB_ACC_HEAD_TBL." WHERE sub_id='".$sub_id."'";  
	$rres = mysql_query($rsql);
	$hnum = mysql_num_rows($rres);
	if($hnum>0){ 
	$hrow = mysql_fetch_object($rres);
	$head_type= $hrow->head_type;
	}else{ 	$head_type= "Supplier"; }
	$transaction_type = "Sales";			
	$sql = "INSERT INTO ".ACCOUNT_JOURNAL_TBL." (voucher_no,created_date,sub_id,head_type,transaction_type,project_id,description,dr,cr,balance,status) VALUES('".$voucher_no."','".$purchare_date."','"
	.$sub_id."','".$head_type."','".$transaction_type."','".$project_id."','".$description."','".$DR."','".$CR."','".$balance."','".$status."')";
	mysql_query($sql);
}
/*
function getSalesMasterInfo($id){		   
	$project_id     = getFromSession('project_id');  
	$info           = array();    
	$info['table']  = SALES_MASTER_TBL.' pm,'.SUB_ACC_HEAD_TBL.' s,'.PROJECT_TBL.' p,'.CURRENCY_TBL.' c';	
	$info['fields'] = array('pm.voucher_no','pm.po_no','pm.wo_no','pm.delivery_point','p.project_name','p.project_logo','p.location','pm.customer','s.sub_head_name','s.head_details','s.phone','s.mobile','s.email','s.att_name1','s.att_designation1','s.att_mobile1','pm.reference','pm.gate_pass','pm.track_no','pm.salse_type','pm.total_value',"DATE_FORMAT(pm.sales_date,'%d %b %y' ) as sales_date","DATE_FORMAT(pm.delivery_date,'%d %b %y' ) as delivery_date",'pm.mode_of_payment','c.curr_symble','pm.bank_name','pm.acc_no','pm.check_no','pm.check_no','pm.general_discount_percent','pm.general_discount_amount','pm.exclusive_discount_percent','pm.exclusive_discount_amount','pm.additional_discount','pm.product_discount','pm.discount','pm.service_charge','pm.net_payble','pm.item_delivery_amount','pm.adjust','pm.paid_amount','pm.due','pm.ref_no','pm.description','pm.created_date');
	
	$sql="pm.customer = s.sub_id AND pm.project_id = p.project_id AND pm.currency = c.currency_id AND pm.project_id = '".$project_id."' AND pm.voucher_no = '$id'";						
	$info['where']  =$sql;	  	
	$info['groupby'] = array("pm.voucher_no");
	//$info['debug']  = true;
	$res            =	select($info);
	if(count($res)){
		foreach($res as $i=>$v){
			$data[$i] = $v;             
		}
	}
	return $data[0];
} 
*/
function getSalesMasterInfo($id,$delivery_master_id=NULL){	
	$project_id     = getFromSession('project_id');  
	$SQLMain = "";  
	$SQL = "
	SELECT pm.voucher_no,pm.delivery_point,pm.po_no,pm.wo_no,pm.und_wo_no,p.project_name,p.project_logo,p.location,pm.customer,COALESCE((s.sub_id), (sp.supplier_code)) as sub_id,COALESCE((s.sub_head_name), (sp.name)) as sub_head_name,COALESCE((s.head_details), (sp.address)) as head_details,COALESCE((s.phone), (sp.phone)) as phone,COALESCE((s.mobile), (sp.mobile)) as mobile,COALESCE((s.email), (sp.email)) as email,COALESCE((s.att_name1), (sp.contact_person)) as att_name1,COALESCE((s.att_designation1), (sp.designation)) as att_designation1,COALESCE((s.att_mobile1), (sp.contact_person_mobile)) as att_mobile1,pm.gate_pass,pm.track_no,pm.salse_type,pm.total_value,pm.sales_date as order_date,DATE_FORMAT(pm.sales_date,'%d %b %y' ) as sales_date,DATE_FORMAT(pm.delivery_date,'%d %b %y' ) as value_date, pm.delivery_date as edit_value_date, pm.sales_date as edit_sales_date,pm.service_charge,pm.mode_of_payment,c.curr_symble,pm.bank_name,pm.acc_no,pm.check_no,pm.adjust,pm.general_discount_percent,pm.general_discount_amount,pm.exclusive_discount_percent,pm.exclusive_discount_amount,pm.additional_discount_percent,pm.additional_discount,pm.product_discount, pm.discount,pm.net_payble,pm.paid_amount,pm.due,pm.item_delivery_amount,pm.return_amount,pm.ref_no,pm.created_date,DATE_FORMAT(pm.created_date,'%d %b %y' ) as date,aging_date,pm.reference,pm.description,pm.commission_slot,pm.total_commission,pm.commission_adv_paid,pm.commission_total_paid,pm.commission_total_due,pm.commission_status,pm.status,pm.retailer_id,pm.additional_cost,pm.vat_type,pm.additional_vat_percent,pm.additional_vat_amount,pm.vehicle_no,pm.driver_name,pm.contact_person,pm.ref_voucher,pm.delivery_address,pm.vat_no,pm.is_deleted";
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
function getSalesWarrantyList($id,$serial,$sales_date1,$sales_date2,$from,$to){	
	if($from == "" && $to == ""){$from=0; $to=500;}
	$sales_date1 	= formatDate($sales_date1);
	$sales_date2 	= formatDate($sales_date2);	   
	$project_id     = getFromSession('project_id');	
	$info           = array();    
	$info['table']  = SALES_MASTER_TBL.' pm,'.SALES_DETAILS_TBL.' sd,'.SUB_ACC_HEAD_TBL.' s,'.PROJECT_TBL.' p,'.CURRENCY_TBL.' c';	
	$info['fields'] = array('pm.voucher_no','pm.po_no','pm.wo_no','p.project_name','p.location','s.sub_id','s.sub_head_name','s.head_details','pm.gate_pass','pm.track_no','pm.salse_type','pm.total_value',"DATE_FORMAT(pm.sales_date,'%d %b %y' ) as sales_date","DATE_FORMAT(pm.delivery_date,'%d %b %y' ) as delivery_date",'pm.mode_of_payment','c.curr_symble','pm.bank_name','pm.acc_no','pm.check_no','pm.check_no','pm.discount','pm.service_charge','pm.net_payble','pm.item_delivery_amount','pm.paid_amount','pm.due','pm.ref_no','pm.created_date');
	
	$sql="pm.customer = s.sub_id AND pm.voucher_no = sd.voucher_no AND pm.project_id=p.project_id AND pm.currency=c.currency_id AND pm.project_id='".$project_id."' ";
	if($id!=""){ $sql.=" AND pm.voucher_no = '$id' "; }
	if($serial!=""){ $sql.=" AND sd.serial='".$serial."'"; }
	if($sales_date1!="" && $sales_date2!=""){ 
		$sql.= " AND (pm.sales_date BETWEEN '$sales_date1' AND '$sales_date2') ";
	}elseif($sales_date1!="" && $sales_date2==""){ 
		$sql.= " AND pm.sales_date >= '$sales_date1' ";
	}elseif($sales_date1=="" && $sales_date2!=""){ 
		$sql.= " AND pm.sales_date <= '$sales_date2' ";
	}
	$info['where']   = $sql;	  	
	$info['groupby'] = array("pm.voucher_no");
	$info['orderby'] = array("pm.voucher_no asc LIMIT $from,$to");
	$result         = select($info);
	$data           = array();
	$cnt = count($result);  	     
	if($cnt) {
		foreach($result as $value){	
		$data[]	= $value;	
		}
		return $data; 
	}else{
	return false;
	}
} 
function getTotalSalesWarrantyList($id,$serial,$sales_date1,$sales_date2){	
	$sales_date1 	= formatDate($sales_date1);
	$sales_date2 	= formatDate($sales_date2);	   
	$project_id     = getFromSession('project_id');	
	$info           = array();    
	$info['table']  = SALES_MASTER_TBL.' pm,'.SALES_DETAILS_TBL.' sd,'.SUB_ACC_HEAD_TBL.' s,'.PROJECT_TBL.' p,'.CURRENCY_TBL.' c';	
	$info['fields'] = array('pm.voucher_no','pm.po_no','pm.wo_no','p.project_name','p.location','s.sub_head_name','s.head_details','pm.gate_pass','pm.track_no','pm.salse_type','pm.total_value',"DATE_FORMAT(pm.sales_date,'%d %b %y' ) as sales_date","DATE_FORMAT(pm.delivery_date,'%d %b %y' ) as delivery_date",'pm.mode_of_payment','c.curr_symble','pm.bank_name','pm.acc_no','pm.check_no','pm.check_no','pm.discount','pm.service_charge','pm.net_payble','pm.item_delivery_amount','pm.paid_amount','pm.due','pm.ref_no','pm.created_date');
	
	$sql="pm.customer = s.sub_id AND pm.voucher_no = sd.voucher_no AND pm.project_id=p.project_id AND pm.currency=c.currency_id AND pm.project_id='".$project_id."' ";
	if($id!=""){ $sql.=" AND pm.voucher_no = '$id' "; }
	if($serial!=""){ $sql.=" sd.serial='".$serial."'"; }
	if($sales_date1!="" && $sales_date2!=""){ 
		$sql.= " AND (pm.sales_date BETWEEN '$sales_date1' AND '$sales_date2') ";
	}elseif($sales_date1!="" && $sales_date2==""){ 
		$sql.= " AND pm.sales_date >= '$sales_date1' ";
	}elseif($sales_date1=="" && $sales_date2!=""){ 
		$sql.= " AND pm.sales_date <= '$sales_date2' ";
	}
	$info['where']   = $sql;	  	
	$info['groupby'] = array("pm.voucher_no");
	$info['orderby'] = array("pm.voucher_no");
	$res            =	select($info);
	return count($res);
}   
        
   function getProductList($id) {  
		$info           = array();    
		$info['table'] = SALES_DETAILS_TBL . " sd
		    LEFT JOIN " . PRODUCT_TBL . " p ON sd.product = p.product_id
		    LEFT JOIN " . CURRENCY_TBL . " c ON sd.currency = c.currency_id
		    LEFT JOIN " . BRAND_TBL . " b ON p.brand_code = b.brand_id";

		$info['fields'] = array('sd.sal_detail_id','sd.brand_id','sd.voucher_no','sd.pvoucher_no','sd.project_id','sd.serial','sd.warranty','sd.catagory','b.brand_name','sd.product','sd.details','p.product_name','p.product_desc','p.product_code','sd.m_unit','sd.purchase_price','sd.unit_price','sd.unit_profit','sd.discount_per_qty','sd.discount_amount','c.curr_symble','sd.currency','sd.qty','sd.delivery_qty','sd.free_qty','sd.return_qty','sd.missing_qty','sd.undelivery_qty','sd.prev_undelivery_qty','sd.total_bag','sd.total','sd.vat','sd.vat_amount','sd.is_order_complete','sd.gross_weight','sd.net_weight','sd.created_time');
		$sql="sd.voucher_no = '$id'";
		$info['where']  = $sql;	  	
	    $info['groupby'] = array("sd.sal_detail_id");
		$info['orderby'] = array("sd.sal_detail_id asc");
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
	function loadProduct4Catagory($catagory){	  
		  $brand_id		   = trim(getRequest('brand_id'));
	  	  $project_id 	   = getFromSession('project_id');  
		  $info            = array();
		  $info['table']   = PRODUCT_TBL.' p,'.PURCHASE_DETAILS_TBL.' pd';
		  $info['fields']  =  array('pd.voucher_no','pd.product','p.product_code','p.product_name','p.product_desc','pd.details','pd.serial',"(pd.`rec_qty`-pd.`sales_qty`) as stock");
//$SQL = "p.product_id=pd.product AND pd.`catagory`='$catagory' AND pd.`brand_id`='$brand_id' AND pd.project_id='$project_id' AND (pd.`rec_qty`-pd.`sales_qty`)>0 ";
		  $SQL = "p.product_id=pd.product AND pd.`brand_id`='$brand_id' AND pd.project_id='$project_id' AND (pd.`rec_qty`-pd.`sales_qty`)>0 ";
		  $info['where']   = $SQL; 
		  $info['groupby'] = array("pd.voucher_no,pd.product");
		  $info['orderby'] = array("pd.voucher_no ASC");
		  //$info['debug']   = true;	
		  $result          = select($info);
		  $data            = array();
		  if(count($result)){
			 foreach($result as $key=>$value){
				$data[$key][]        = $value;
			 }
		  }

		  require_once(CLASS_DIR . '/common.list.class.php');
		  foreach($data as $i=>$v){
			$productName = (new CommonList())->normalizeProductName($v[0]->product_code, $v[0]->product_name);
			 $subject_idname .= $v[0]->voucher_no.'#####'.$v[0]->product.'#####'.$productName.'#####'.$v[0]->details.'#####'.$v[0]->product_desc.'#####'.$v[0]->stock.'@@@';
		  }
		  echo $subject_idname;	
	}
	function loadProductSerial($product){	  
		  $voucher_no	   = trim(getRequest('voucher_no'));
	  	  $project_id 	   = getFromSession('project_id');  
		  $info            = array();
		  $info['table']   = PURCHASE_DETAILS_TBL;
		  $info['fields']  =  array('serial');
		  $SQL = "`product`='$product' AND `voucher_no`='$voucher_no' AND project_id='$project_id' AND (`rec_qty`-`sales_qty`)>0 ";
		  $info['where']   = $SQL; 
		  $info['groupby'] = array("serial");
		  $info['orderby'] = array("serial ASC");
		  //$info['debug']   = true;	
		  $result          = select($info);
		  $data            = array();	
		  if(count($result)){
			 foreach($result as $key=>$value){
				$data[$key][]        = $value;
			 }
		  }
		  foreach($data as $i=>$v){
			 $subject_idname.=$v[0]->serial.'#####'.$v[0]->serial.'@@@';
		  }
		  echo $subject_idname;	
	}
	function getBankAccountList($purchase_no=null){
	   if($from == "" && $to == ""){$from=0; $to=40;}  
	   $data            = array();	  
	   $info            = array();
	   $info['table']   = BANK_ACCOUNT_TBL.' ba,'.BANK_TBL.' b';	
	   $info['fields'] = array('ba.bank_code','b.bank_name','ba.purchase_no','ba.account_name','ba.account_type','ba.phone','ba.fax');
	   if($purchase_no!=""){				
			$info['where']   = "ba.bank_code = b.bank_id AND ba.purchase_no = '".$purchase_no."'";
	   }else{
			$info['where']   = "ba.bank_code = b.bank_id";
	   }    
	   $info['orderby'] = array("ba.purchase_no asc LIMIT $from,$to");
	   $info['debug']   = false;	 
	   $res            =	select($info);   
	   if(count($res)){
		  foreach($res as $i=>$v){
			 $data[$i] = $v;
		  }
	   }
	   if($purchase_no==""){
		return $data; // for list
	  }else{
		return $data[0];	// for view
	  }
	}
   function getCurrencyList(){
      $info            = array();
      $info['table']   = CURRENCY_TBL;
      $info['debug']   = false;
      $result          = select($info);
      $data            = array();
      if(count($result)){
         foreach($result as $i=>$v){
            $data[$i] = $v;   
         }
      }
      return $data;
   } 
   function getCatagoryList(){	
	  $project_id  		= getFromSession('project_id');
      $data 			= array(); 
      $info        		=  array();
      $info['table']	= CATAGORY_TBL;
	  $info['where']    = "project_id = '$project_id'";
      $res            	=	 select($info);      
      if(count($res)){
         foreach($res as $i=>$v){
            $data[$i] = $v;             
         }
      }
      return $data;	
   }
  function getCustomerList(){		  
	  $project_id  		= getFromSession('project_id');
      $data 			= array(); 
      $info        		= array();
      $info['table']	= SUB_ACC_HEAD_TBL;
	  $info['where']  	= "head_type = 'Customer' AND project_id='".$project_id."'";	  	
      $res            	= select($info);      
      if(count($res)){
         foreach($res as $i=>$v){
            $data[$i] = $v;             
         }
      }
      return $data;	
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
  function saveStockJournal($pvoucher_no,$voucher_no,$project_id,$product_id,$serial,$warranty,$unit_price=NULL,$m_unit,$DR=NULL,$CR=NULL,$balance,$create_date=NULL){
		$sql = "INSERT INTO ".STOCK_LEDGER_TBL." (po_no,voucher_no,project_id,product_id,serial,warranty,unit_price,m_unit,dr,cr,balance,create_date) VALUES('".$pvoucher_no."','".$voucher_no."','".$project_id."','".$product_id."','".$serial."','".$warranty."','".$unit_price."','".$m_unit."','".$DR."','".$CR."','".$balance."','".$create_date."')";
		mysql_query($sql);
  }
   function createVoucharID()
   {
      $info = array();
      $info['table'] = DEVIT_VOUCHAR_TBL;
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
  function showEditor4SalesCommission($msg = null) { 
      require_once(CLASS_DIR.'/sales.report.class.php');	
	  $slsrpApp = new SalesReport();       
	  $data                				= array();
	  $data['cmd']         				= getRequest('cmd');
	  $data['record_list'] 				= $slsrpApp->getSalesDetailsList(getRequest('from'),getRequest('to'));
	  $data['totalrecord']				= $slsrpApp->getTotalSalesDetailsList(getRequest('from'),getRequest('to'));			
	  require_once(SALES_COMMISSION_LIST_SKIN); 
	  return $data[0];
  }
  function showAllCompaniesSalesDetails($msg = null) {        
	  require_once(CLASS_DIR.'/common.list.class.php');	
	  $comListApp = new CommonList();
	  $data                				= array();
	  $data['cmd']         				= getRequest('cmd');
	  $data['record_list'] 				= $this->getAllSalesDetailsList(getRequest('from'),getRequest('to'));
	  $data['totalrecord']				= $this->getAllTotalSalesDetailsList(getRequest('from'),getRequest('to'));	
	  $data['district_list']	= $comListApp->getDistrictList();
	  $data['area_list'] 		= $comListApp->getAreaList();  	
	  require_once(ADMIN_SALES_DETAILS_SKIN);		
	  return $data[0];
   }
	
	function getAllSalesDetailsList($from,$to) { 
		if($from == "" && $to == ""){$from=0; $to=500;}
		$date_from 			= formatDate(getRequest('date_from'));
		$date_to 			= formatDate(getRequest('date_to'));				
		$project_id     = getFromSession('project_id');  
		$info           = array();    
		$info['table']  = SALES_MASTER_TBL.' pm,'.SUB_ACC_HEAD_TBL.' s,'.CURRENCY_TBL.' c';	
		$info['fields'] = array('pm.voucher_no','pm.project_id','s.sub_id','s.sub_head_name','s.head_details','pm.gate_pass','pm.track_no','pm.salse_type','pm.total_value',"DATE_FORMAT(pm.sales_date,'%d %b %y' ) as sales_date",'pm.mode_of_payment','c.curr_symble','pm.bank_name','pm.acc_no','pm.check_no','pm.check_no','pm.discount','pm.net_payble','pm.paid_amount','pm.due','pm.ref_no','pm.created_date',"DATE_FORMAT(pm.created_date,'%d %b %y' ) as date");
		
		$sql="pm.customer = s.sub_id AND pm.currency = c.currency_id";		
		if($date_from!="" && $date_to ==""){
			$sql.=" AND pm.created_date >= '$date_from'";
		}elseif($date_from=="" && $date_to !=""){
			$sql.=" AND pm.created_date <= '$date_to'";
		}elseif($date_from!="" && $date_to !=""){
			$sql.=" AND pm.created_date BETWEEN '$date_from' AND '$date_to'";
		}
		$info['where']  =$sql;
		$info['orderby'] = array("pm.voucher_no asc LIMIT $from,$to");
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

   function getAllTotalSalesDetailsList($from,$to) {  
		$date_from 			= formatDate(getRequest('date_from'));
		$date_to 			= formatDate(getRequest('date_to'));				
		$project_id     = getFromSession('project_id');  
		$info           = array();    
		$info['table']  = SALES_MASTER_TBL.' pm,'.SUB_ACC_HEAD_TBL.' s,'.CURRENCY_TBL.' c';	
		$info['fields'] = array('pm.voucher_no');
		$sql="pm.customer = s.sub_id AND pm.currency = c.currency_id";
		if($date_from!="" && $date_to ==""){
			$sql.=" AND pm.created_date >= '$date_from'";
		}elseif($date_from=="" && $date_to !=""){
			$sql.=" AND pm.created_date <= '$date_to'";
		}elseif($date_from!="" && $date_to !=""){
			$sql.=" AND pm.created_date BETWEEN '$date_from' AND '$date_to'";
		}
		$info['where']  =$sql;
		$info['orderby'] 	= array("pm.created_date asc");
		$result         	= select($info);
		$data           	= array(); 
	    $cnt = count($result); 	
      if($cnt) {
        return $cnt;
      } 
	  else {
	  return 0;
	 }    
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

function loadSOInfo($po_no)
{	  
	  $project_id = getFromSession('project_id');  
	  $info            = array();
	  $info['table']   = PURCHASE_MASTER_TBL." sm, ".PURCHASE_DETAILS_TBL." sd, ".SUB_ACC_HEAD_TBL." s,".CATAGORY_TBL." c";
	  $info['fields']  =  array('sm.voucher_no','sm.po_no','sd.catagory','c.catagory_name');
	  $info['where']   = " sm.po_no = '$po_no' AND sm.project_id = '$project_id' AND sm.voucher_no=sd.voucher_no AND sd.catagory = c.catagory_code";
	  $info['groupby'] = array("po_no");
	  $result          = select($info);
	  $data            = array();
	  if(count($result)){
		 foreach($result as $key=>$value){
			$data[$key][]        = $value;
		 }
	  }
			
	  foreach($data as $i=>$v){
		 $subject_idname.= $v[0]->voucher_no.'#####'.$v[0]->po_no.'#####'.$v[0]->catagory.'###'.$v[0]->catagory_name;
	  }
	  echo $subject_idname;	
}
function loadUnitePrice($product_id){
	  $project_id = getFromSession('project_id');  		 
	  $info            = array();
	  $info['table']   = PURCHASE_DETAILS_TBL.' pd,'.PRODUCT_TBL." p";
	  $info['fields']  =  array('pd.m_unit','pd.serial','pd.warranty','pd.unit_price','(pd.rec_qty-pd.sales_qty) as stockqty','p.product_desc','pd.details');
	  $where= "pd.product=p.product_id AND pd.voucher_no = '".$_REQUEST['voucher_no']."' AND pd.product = '$product_id' AND pd.project_id = '$project_id' AND (pd.rec_qty-pd.sales_qty)>0";
	  $info['where']   = $where;
	  $info['groupby'] = array("pd.voucher_no");
	  $result          = select($info);
	  $data            = array();

	  if(count($result)){
		 foreach($result as $key=>$value){
			$data[$key][]        = $value;
		 }
	  }
			
	  foreach($data as $i=>$v)
	  {
		 $str = $v[0]->unit_price."#####".$v[0]->stockqty."#####".$v[0]->m_unit."#####".$v[0]->serial."#####".$v[0]->warranty."#####".$v[0]->details."#####".$v[0]->product_desc."#####";
	  }
	  echo $str;	
}
function loadStockQty($product_id){
	  $project_id = getFromSession('project_id');  
	  $voucher_no = $_REQUEST['voucher_no'];		 
	  $totalCr = $this->getTotalCreditStock($product_id,$project_id);
	  $totalDr = $this->getTotalDebitStock($product_id,$project_id);
	  $balanceQty = $totalDr - $totalCr;
	  echo $balanceQty;	
}

} // End class
?>
