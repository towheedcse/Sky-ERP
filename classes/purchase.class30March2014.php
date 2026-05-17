<?php
require_once('journal.class.php');
class Purchase extends Journal
{
   
   function run()
   {         

      $cmd = getRequest('cmd');
	  $u_t_id = getFromSession('u_type_id');
      if( ($u_t_id == 101) || ($u_t_id ==102) || ($u_t_id == 103)) // 1 = sysadmin, 2 = admin, 3 = project admin
      {
      	switch ($cmd)
      	{
      	   case 'add'					: $this->showEditor(); break;
 		   case 'addlc'					: $this->showEditor(); break;
		   case 'edit'					: $this->showEditor(); break;
		   case 'pur_dtl'				: $this->showEditor4PurchaseDetails(); break;
      	   case 'loadProduct'  			: $this->loadProduct4Catagory(trim(getRequest('brand_id'))); break;  
      	   case 'getproductdtl'  		: $this->loadProductDtl(trim(getRequest('product_id'))); break;   
		   case 'savePurchase'			: $this->savePurchaseItem(); break;
		   case 'print_vouchar'			: $screen = $this->showPrintEditor($msg); break;  
		   case 'delete'             	: $screen = $this->deleteRecord(getRequest('id')); break;
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
			$advArr 					= $this->getPurchaseMasterInfo($voucher_no);
			$advArr 					= parseThisValue($advArr); 
			$data   					= array_merge(array(), $advArr); 
		  
			$data['item_list']	= $this->getProductList($voucher_no);
			$data['message'] = $msg;
			$data['cmd']     = getRequest('cmd');
			require_once(PURCHASE_VOUCHAR_SKIN);      
			return true;
	   }else{
		require_once(PRINT_VOUCHAR_SKIN);
	   }
   }
     
   function showEditor($msg = null) {
	   require_once(CLASS_DIR.'/common.list.class.php');	
	   $comListApp 	= new CommonList();
   	   $data                	= array();       
	   $data['supplier_list'] 	= $this->getSupplierList();	     	
	   $data['product_list'] 	= $comListApp->getProductList();
	   $data['cat_list'] 		= $this->getCatagoryList();	
	   $data['currency_list']   = $this->getCurrencyList();   
	   $data['brand_list'] 		= $comListApp->getBrandList();      	
	   $data['depo_list'] 		= $comListApp->getDeliveryPointList();
	 	
	   $data['cmd']         	= getRequest('cmd');
		if(getRequest('cmd')=="addlc"){       
        	$data['country_list']     = $comListApp->getCountryList();
	   		require_once(LC_OPENING_SKIN); 
		}else{			
	   		require_once(CURRENT_APP_SKIN_FILE); 
		}
	   return $data[0];
   }

  function insertPurchaseDetails($voucher_no)
  {
	require_once(CLASS_DIR.'/common.list.class.php');	
	$comlistApp 				= new CommonList();
	$requestdata 				= array();
	$arr_catagory_product_id	= array();
	$project_id  				= getFromSession('project_id');
	$currency        			= getRequest('currency');
	$arr_catagory_product_id	= getRequest('input_catagory_product_id');
	$arr_brand        			= getRequest('input_brand');
	$arr_pdetails        		= getRequest('input_pdetails');
	$arr_serial        			= getRequest('input_serial');
	$arr_warranty        		= getRequest('input_warranty');
	$arr_m_unit        			= getRequest('input_m_unit');
	$arr_total_unit        		= getRequest('input_total_unit');
	$arr_unit_price				= getRequest('input_unit_price');
	$arr_qty      				= getRequest('input_qty');
	$arr_total_bag      		= getRequest('input_total_bag');
	$arr_total_value       		= getRequest('input_total_value'); 
	$total_value = getRequest('total_value');  $discount = getRequest('discount'); $discount_persent = (($discount/$total_value)*100);
		
	for($i=0;$i<count($arr_catagory_product_id);$i++)
	{
	  $catagory_product_sep = $arr_catagory_product_id[$i];		
	  $requestdata['project_id'] = $project_id;       	  

	  for($j=0;$j<count($catagory_product_sep);$j++)
	  {
			$catagory_product = explode("###",$catagory_product_sep);
			$catagoryid  	= array();
			$productid 		= array();				
			$brandid 		= array();					
			$serialid 		= array();			  

			$catagoryid['c'] 	= $catagory_product[0];				
			$brandid['b']  	 	= $catagory_product[1];				
			$productid['p']  	= $catagory_product[2];				
			$serialid['s']  	= $catagory_product[3];
		}
	   foreach($catagoryid as $val){
			$requestdata['catagory'] = $val;	
	   }
	   foreach($brandid as $val){
			$requestdata['brand_id']=$val; $brand_id = $val;
	   }	
	   foreach($productid as $val){
			$requestdata['product'] =$val;	
			$product_id				=$val;
	   }		   	
	   foreach($serialid as $val){
			$requestdata['serial'] =$val;	
			$serial				=$val;
	   }	   
	   foreach($arr_m_unit as $key => $val){
		  if($catagory_product_sep==$key){
			$requestdata['m_unit'] = $val;	
		  }
	   }
	   foreach($arr_brand as $key => $val){
		  if($catagory_product_sep==$key){
			$requestdata['brandname'] = $val;	
		  }
	   } 
	   foreach($arr_pdetails as $key => $val){
		  if($catagory_product_sep==$key){
			$requestdata['details'] = $val;
		  }
	   } 
	   foreach($arr_warranty as $key => $val){
		  if($catagory_product_sep==$key){
			$requestdata['warranty'] = $val; $warranty = $val;	
		  }
	   }   
	   foreach($arr_total_unit as $key => $val){
		  if($catagory_product_sep==$key){
			$requestdata['total_unit'] = $val;	
		  }
	   }
	   foreach($arr_unit_price as $key => $val){
		  if($catagory_product_sep==$key)
		  {
			$requestdata['unit_price'] = $val;	
		  }
	   }
	   foreach($arr_qty as $key => $val){
			if($catagory_product_sep==$key){
				 $requestdata['qty'] = $val; $requestdata['rec_qty'] = $val;
				 $productQty		 = $val;
			}
	   }
	   foreach($arr_total_bag as $key => $val){
			if($catagory_product_sep==$key){
				 $requestdata['total_bag'] = $val;	
			}
	   }
	   foreach($arr_currency as $key => $val){
			if($catagory_product_sep==$key){
			 $requestdata['currency'] = $val;	
			}
	   }
	   foreach($arr_total_value as $key => $val){
		  if($catagory_product_sep==$key){
			 $requestdata['total'] = $val;	
		  }
	   }
	    $perQtyAmount = ($requestdata['total']/$productQty);
		$requestdata['discount_per_qty']  = $discount_persent;
		$requestdata['discount_amount']   = (($perQtyAmount/100)*$discount_persent);
		$requestdata['created_by'] 		  = getFromSession('userid');
		$requestdata['created_date']      = date('Y-m-d h:i:s');  
		$project_id						  = getFromSession('project_id'); 
		$requestdata['project_id']        = $project_id;
		$requestdata['voucher_no']        = $voucher_no;
		$created_date 	 = formatDate(getRequest('purchase_date'));
		$store_id = getRequest('store_id');

		$info        		=  array();
		$info['table']	= PURCHASE_DETAILS_TBL;
		$info['data'] 	= $requestdata;      
		$res = insert($info);
		if($res){
		if($requestdata['discount_amount']>0){
		$DisAmount = ($requestdata['discount_amount']*$productQty);
		$description = "Get Discount from Purchase";
		//========= Capital Dr ==========
		$capital_head 	 = $comlistApp->getMainCapitalId(getFromSession('project_id'));
		$totalCapitalCR  = $this->getTotalCreditAmount($capital_head,getFromSession('project_id'));
		$totalCapitalDR  = $this->getTotalDebitAmount($capital_head,getFromSession('project_id'));					 
		$Capitalbalance  = (($totalCapitalDR+$DisAmount)-$totalCapitalCR);					 
		$comlistApp->saveAccJournal($voucher_no,$capital_head,"Acc","Purchase Discount",getFromSession('project_id'),$description,$DisAmount,0,$Capitalbalance,0,$created_date);
		}
		$m_unit = $requestdata['m_unit']; $unit_price = $requestdata['unit_price']; $StockAmount = ($unit_price*$productQty);
		$totalCR  = $this->getTotalCreditStock($product_id,getFromSession('project_id'));
		$totalDR  = $this->getTotalDebitStock($product_id,getFromSession('project_id'));					 
		$balance  = (($totalDR + $productQty) - $totalCR);	
		$Prosql = "SELECT product_type FROM ".PRODUCT_TBL." WHERE product_id = '$product_id' AND project_id = '$project_id'";
		$Prorow = mysql_fetch_object(mysql_query($Prosql));
		$product_type 		= $Prorow->product_type;
		$inventory_auto_out = getFromSession('inventory_auto_out'); 
		if($inventory_auto_out==1 && $product_type=="Invetory Item"){				
			$this->saveStockJournal($voucher_no,$project_id,$product_id,$product_type,$serial,$warranty,$unit_price,$m_unit,$productQty,0,$balance,$created_date,$store_id);
			$Autobalance  = ($totalDR - ($totalCR+$productQty));
			$this->saveStockJournal($voucher_no,$project_id,$product_id,$product_type,$serial,$warranty,$unit_price,$m_unit,0,$productQty,$Autobalance,$created_date,$store_id);
		}else{	
		//=== Stock Dr =====
		$StockId 	 = $comlistApp->getStockId(getFromSession('project_id'));
		$totalStockCr  = $this->getTotalCreditAmount($StockId,getFromSession('project_id'));
		$totalStockDr  = $this->getTotalDebitAmount($StockId,getFromSession('project_id'));					 
		$StockBalance  = (($totalStockDr+$StockAmount)-$totalStockCr);	$description = "Purchase Product";				 
		$comlistApp->saveAccJournal($voucher_no,$StockId,"Stock","Purchase Product",getFromSession('project_id'),$description,$StockAmount,0,$StockBalance,0,$created_date);
		$this->saveStockJournal($voucher_no,$project_id,$product_id,$product_type,$serial,$warranty,$unit_price,$m_unit,$productQty,0,$balance,$created_date,$store_id);
		}
	  }// end purchase save
   } // end foreach

  } //End of the func

   //==================== saveDebitVouchar ====================
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
		    $requestdata['account_head']     	= getRequest('supplier');         
		    $requestdata['head_type']     	 	= "Supplier";  
		    $requestdata['debit']        	 	= getRequest('paid_amount'); 
		  	$requestdata['credit']        	 	= 0; 
		  }elseif($mode_of_payment=="Cash"){
			$requestdata['bank_name'] 			= "";
			$requestdata['acc_no'] 				= "";
			$requestdata['check_no'] 			= "";
			$requestdata['check_issue_date'] 	= "";     
		    $requestdata['account_head']     	= getRequest('supplier'); 
		    $requestdata['debit']        	 	= getRequest('paid_amount'); 
		  	$requestdata['credit']        	 	= 0;         
		    $requestdata['head_type']     	 	= "Supplier";  
		  }elseif($mode_of_payment=="Payable"){
			//======= Party Cr ======
			$requestdata['bank_name'] = "";
			$requestdata['acc_no'] = "";
			$requestdata['check_no'] = "";
			$requestdata['check_issue_date'] = "";
			$requestdata['account_head']     	= $this->getPayableId(getFromSession('project_id')); 
		  	$requestdata['debit']        		= getRequest('due'); 
		  	$requestdata['credit']        		= 0;     
		  	$requestdata['head_type']     		= "Acc"; 
		 }
		  $requestdata['transaction_type']  = "Payment"; 
		  $requestdata['project_id']        = getFromSession('project_id');    
		  $requestdata['created_by']        = getFromSession('userid');	
		  $requestdata['created_date']      = formatDate(getRequest('purchase_date'));
	
		  $voucher_no = $this->createVoucharID();
	
		 if($voucher_no != -1)
		  {
			$requestdata['voucher_no']   	= $voucher_no;
		  }
		  else
		  {
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
			header("location:index.php?app=purchase&cmd=add");	
		  } 

    }//EOFn  

    function saveCreditVouchar($voucher_no)
 	{     
 	   	  $mode_of_payment = getRequest('mode_of_payment');
		  
		  $requestdata = array();
	
		  $requestdata = getUserDataSet(CREDIT_VOUCHAR_TBL);	
		  if($mode_of_payment =="Check"){
			$requestdata['bank_name'] 			= getRequest('bank_name');
			$requestdata['acc_no'] 				= getRequest('acc_no');
			$requestdata['check_no'] 			= getRequest('check_no');
			$requestdata['check_issue_date'] 	= formatDate(getRequest('check_issue_date'));
    
		  	$requestdata['account_head']     	= getRequest('acc_no'); 
		  	$requestdata['debit']        		= 0; 
		  	$requestdata['credit']        		= getRequest('paid_amount');     
		  	$requestdata['head_type']     		= "Check";   
		  }elseif($mode_of_payment=="Cash"){
			$requestdata['bank_name'] = "";
			$requestdata['acc_no'] = "";
			$requestdata['check_no'] = "";
			$requestdata['check_issue_date'] = "";

			$requestdata['account_head']     	= $this->getCashId(getFromSession('project_id')); 
		  	$requestdata['debit']        		= 0; 
		  	$requestdata['credit']        		= getRequest('paid_amount');     
		  	$requestdata['head_type']     		= "Acc";   
		  }elseif($mode_of_payment=="Payable"){
			//======= Party Dr ======
			$requestdata['bank_name'] = "";
			$requestdata['acc_no'] = "";
			$requestdata['check_no'] = "";
			$requestdata['check_issue_date'] = "";

			$requestdata['account_head']     	= getRequest('supplier'); 
		  	$requestdata['credit']        		= getRequest('due'); 
		  	$requestdata['debit']        		= 0;     
		  	$requestdata['head_type']     		= "Supplier"; 
		 }
		  $requestdata['transaction_type']  = "Payment"; 
		  $requestdata['project_id']        = getFromSession('project_id');    
		  $requestdata['created_by']        = getFromSession('userid'); 
	
		  $requestdata['created_date']      = formatDate(getRequest('purchase_date')); //date('Y-m-d h:i:s');	
		  $requestdata['voucher_no']   	= $voucher_no;
		 
		  $info        		=  array();
		  $info['table']	= CREDIT_VOUCHAR_TBL;
		  $info['data'] 	= $requestdata;     
		  //$info['debug']  	=  true;
		  $res = insert($info);
		  $created_date = $requestdata['created_date']; 
	
		  if($res['affected_rows']) {
			 $CrAmount = getRequest('paid_amount');
    		 $due = getRequest('due');
			if(getRequest('advanced_paid_amount')==0){
			  if($mode_of_payment=="Cash"){ 
				if(getRequest('due')>0){
					 //=========== Payable Dr ========
					 $fullpayble		= getRequest('net_payble');
					 $payable_head 		= $this->getPayableId(getFromSession('project_id'));
					 $totalPayableCR  	= $this->getTotalCreditAmount($payable_head,getFromSession('project_id'));
					 $totalPayableDR  	= $this->getTotalDebitAmount($payable_head,getFromSession('project_id'));					 
					 $payableBalance  	= (($totalPayableDR+$fullpayble)-$totalPayableCR);					 
					 $this->saveAccountJournal($voucher_no,$payable_head,"Acc",getFromSession('project_id'),getRequest('description'),$fullpayble,0,$payableBalance,1,$created_date);	
					//========= Payable Cr ==========
	
					 $payble	= getRequest('paid_amount');
					 $pblAcc_head = $this->getPayableId(getFromSession('project_id'));
					 $totalPblCR  = $this->getTotalCreditAmount($pblAcc_head,getFromSession('project_id'));
					 $totalPblDR  = $this->getTotalDebitAmount($pblAcc_head,getFromSession('project_id'));					 
					 $pblBalance  = ($totalPblDR-($totalPblCR+$payble));					 
					 $this->saveAccountJournal($voucher_no,$pblAcc_head,"Acc",getFromSession('project_id'),getRequest('description'),0,$payble,$pblBalance,1,$created_date);	//before 0
					//======= Supplier Cr ======	
					 $fullCr = getRequest('net_payble');
					 $PartyAcc_head = getRequest('supplier'); 
					 $totalPartyCR  = $this->getTotalCreditAmount($PartyAcc_head,getFromSession('project_id'));
					 $totalPartyDR  = $this->getTotalDebitAmount($PartyAcc_head,getFromSession('project_id'));	
					 $PreviousPartyBalance = ($totalPartyDR-$totalPartyCR);					 
					 $PartyBalance  = ($totalPartyDR-($totalPartyCR+$fullCr));					 
					 $this->saveAccountJournal($voucher_no,$PartyAcc_head,"Supplier",getFromSession('project_id'),getRequest('description'),0,$fullCr,$PartyBalance,1,$created_date);
					
					 //======= Supplier Dr ======			
					 $DrAmount = getRequest('paid_amount');
					 $PartyAcc_head1 = getRequest('supplier'); 
					 $totalPartyCR1  = $this->getTotalCreditAmount($PartyAcc_head1,getFromSession('project_id'));
					 $totalPartyDR1  = $this->getTotalDebitAmount($PartyAcc_head1,getFromSession('project_id'));					 
					 $PartyBalance1  = (($totalPartyDR1+$DrAmount)-$totalPartyCR1);					 
					 $this->saveAccountJournal($voucher_no,$PartyAcc_head1,"Supplier",getFromSession('project_id'),getRequest('description'),$DrAmount,0,$PartyBalance1,1,$created_date);	
	
					//============== Cash Cr ===============
					 $acc_head = $this->getCashId(getFromSession('project_id'));
					 $totalCR  = $this->getTotalCreditAmount($acc_head,getFromSession('project_id'));
					 $totalDR  = $this->getTotalDebitAmount($acc_head,getFromSession('project_id'));					 
					 $balance  = ($totalDR-($totalCR+$CrAmount));					 
					 $this->saveAccountJournal($voucher_no,$acc_head,"Acc",getFromSession('project_id'),getRequest('description'),0,$CrAmount,$balance,1,$created_date);	
										
				}elseif(getRequest('due')==0){	
					//======= Supplier Cr ======	
					 $fullCr = getRequest('net_payble');
					 $PartyAcc_head1 = getRequest('supplier'); 
					 $totalPartyCR1  = $this->getTotalCreditAmount($PartyAcc_head1,getFromSession('project_id'));
					 $totalPartyDR1  = $this->getTotalDebitAmount($PartyAcc_head1,getFromSession('project_id'));					 
					 $PartyBalance1  = ($totalPartyDR1-($totalPartyCR1+$fullCr));					 
					 $this->saveAccountJournal($voucher_no,$PartyAcc_head1,"Supplier",getFromSession('project_id'),getRequest('description'),0,$fullCr,$PartyBalance1,1,$created_date);
						
					//======= Supplier Dr ======			
					$DrAmount = getRequest('paid_amount');
					$PartyAcc_head = getRequest('supplier'); 
					$totalPartyCR  = $this->getTotalCreditAmount($PartyAcc_head,getFromSession('project_id'));
					$totalPartyDR  = $this->getTotalDebitAmount($PartyAcc_head,getFromSession('project_id'));					 
					$PartyBalance  = (($totalPartyDR+$DrAmount)-$totalPartyCR);					 
					$this->saveAccountJournal($voucher_no,$PartyAcc_head,"Supplier",getFromSession('project_id'),getRequest('description'),$DrAmount,0,$PartyBalance,1,$created_date);	
	
					//============== Cash Cr ===============
					 $acc_head = $this->getCashId(getFromSession('project_id'));
					 $totalCR  = $this->getTotalCreditAmount($acc_head,getFromSession('project_id'));
					 $totalDR  = $this->getTotalDebitAmount($acc_head,getFromSession('project_id'));					 
					 $balance  = ($totalDR-($totalCR+$CrAmount));					 
					 $this->saveAccountJournal($voucher_no,$acc_head,"Acc",getFromSession('project_id'),getRequest('description'),0,$CrAmount,$balance,1,$created_date);	
					 
				}
				// header("location:index.php?app=purchase&cmd=print_vouchar&voucher_no=".$voucher_no);
			 }elseif($mode_of_payment=="Check"){
				//====== save payable_check ======
				$this->savePayableCheck($voucher_no,$voucher_no,"Payment",getRequest('paid_amount'));
				//======= Supplier Cr ======	
				 $fullCr = getRequest('net_payble');
				 $PartyAcc_head1 = getRequest('supplier'); 
				 $totalPartyCR1  = $this->getTotalCreditAmount($PartyAcc_head1,getFromSession('project_id'));
				 $totalPartyDR1  = $this->getTotalDebitAmount($PartyAcc_head1,getFromSession('project_id'));					 
				 $PartyBalance1  = ($totalPartyDR1-($totalPartyCR1+$fullCr));					 
				 $this->saveAccountJournal($voucher_no,$PartyAcc_head1,"Supplier",getFromSession('project_id'),getRequest('description'),0,$fullCr,$PartyBalance1,1,$created_date);
					
				//======= Supplier Dr ======			
				$DrAmount = getRequest('paid_amount');
				$PartyAcc_head = getRequest('supplier'); 
				$totalPartyCR  = $this->getTotalCreditAmount($PartyAcc_head,getFromSession('project_id'));
				$totalPartyDR  = $this->getTotalDebitAmount($PartyAcc_head,getFromSession('project_id'));					 
				$PartyBalance  = (($totalPartyDR+$DrAmount)-$totalPartyCR);					 
				$this->saveAccountJournal($voucher_no,$PartyAcc_head,"Supplier",getFromSession('project_id'),getRequest('description'),$DrAmount,0,$PartyBalance,1,$created_date);	
	
			}elseif($mode_of_payment=="Payable"){
				//======= Supplier Cr ======	
				 $fullCr = getRequest('net_payble');
				 $PartyAcc_head1 = getRequest('supplier'); 
				 $totalPartyCR1  = $this->getTotalCreditAmount($PartyAcc_head1,getFromSession('project_id'));
				 $totalPartyDR1  = $this->getTotalDebitAmount($PartyAcc_head1,getFromSession('project_id'));					 
				 $PartyBalance1  = ($totalPartyDR1-($totalPartyCR1+$fullCr));					 
				 $this->saveAccountJournal($voucher_no,$PartyAcc_head1,"Supplier",getFromSession('project_id'),getRequest('description'),0,$fullCr,$PartyBalance1,1,$created_date);
				 
				//=========== Payable Dr ========
				 $fullpayble		= getRequest('net_payble');
				 $payable_head 		= $this->getPayableId(getFromSession('project_id'));
				 $totalPayableCR  	= $this->getTotalCreditAmount($payable_head,getFromSession('project_id'));
				 $totalPayableDR  	= $this->getTotalDebitAmount($payable_head,getFromSession('project_id'));					 
				 $payableBalance  	= (($totalPayableDR+$fullpayble)-$totalPayableCR);					 
				 $this->saveAccountJournal($voucher_no,$payable_head,"Acc",getFromSession('project_id'),getRequest('description'),$fullpayble,0,$payableBalance,1,$created_date);	
					
			}
		 }else{ // end Advanced paid is 0
			//======= Supplier Dr advanced_paid_amount ======	
			 $advancedDrAmount = getRequest('advanced_paid_amount');
			 $PartyAcc_head1 = getRequest('supplier'); 
			 $totalPartyCR1  = $this->getTotalCreditAmount($PartyAcc_head1,getFromSession('project_id'));
			 $totalPartyDR1  = $this->getTotalDebitAmount($PartyAcc_head1,getFromSession('project_id'));					 
			 $PartyBalance1  = (($totalPartyDR1+$advancedDrAmount)-$totalPartyCR1);					 
			 $this->saveAccountJournal($voucher_no,$PartyAcc_head1,"Supplier",getFromSession('project_id'),getRequest('description'),$advancedDrAmount,0,$PartyBalance1,1,$created_date);
									
			  if($mode_of_payment=="Cash"){ 
				if(getRequest('due')>0){
					//======= Supplier Cr ======	
					 $fullCr = (getRequest('net_payble')-getRequest('advanced_paid_amount'));
					 $PartyAcc_head = getRequest('supplier'); 
					 $totalPartyCR  = $this->getTotalCreditAmount($PartyAcc_head,getFromSession('project_id'));
					 $totalPartyDR  = $this->getTotalDebitAmount($PartyAcc_head,getFromSession('project_id'));					 
					 $PartyBalance  = ($totalPartyDR-($totalPartyCR+$fullCr));					 
					 $this->saveAccountJournal($voucher_no,$PartyAcc_head,"Supplier",getFromSession('project_id'),getRequest('description'),0,$fullCr,$PartyBalance,1,$created_date);
					if(getRequest('paid_amount')>0){
						 //======= Supplier Dr ======			
						 $DrAmount = getRequest('paid_amount');
						 $PartyAcc_head1 = getRequest('supplier'); 
						 $totalPartyCR1  = $this->getTotalCreditAmount($PartyAcc_head1,getFromSession('project_id'));
						 $totalPartyDR1  = $this->getTotalDebitAmount($PartyAcc_head1,getFromSession('project_id'));					 
						 $PartyBalance1  = (($totalPartyDR1+$DrAmount)-$totalPartyCR1);					 
						 $this->saveAccountJournal($voucher_no,$PartyAcc_head1,"Supplier",getFromSession('project_id'),getRequest('description'),$DrAmount,0,$PartyBalance1,1,$created_date);	
		
						//============== Cash Cr ===============			
						 $CrAmount = getRequest('paid_amount');
						 $acc_head = $this->getCashId(getFromSession('project_id'));
						 $totalCR  = $this->getTotalCreditAmount($acc_head,getFromSession('project_id'));
						 $totalDR  = $this->getTotalDebitAmount($acc_head,getFromSession('project_id'));					 
						 $balance  = ($totalDR-($totalCR+$CrAmount));					 
						 $this->saveAccountJournal($voucher_no,$acc_head,"Acc",getFromSession('project_id'),getRequest('description'),0,$CrAmount,$balance,1,$created_date);	
					}
				}elseif(getRequest('due')==0){	
					if(getRequest('paid_amount')>0){
						//======= Supplier Cr ======	
						 $fullCr = getRequest('paid_amount');
						 $PartyAcc_head1 = getRequest('supplier'); 
						 $totalPartyCR1  = $this->getTotalCreditAmount($PartyAcc_head1,getFromSession('project_id'));
						 $totalPartyDR1  = $this->getTotalDebitAmount($PartyAcc_head1,getFromSession('project_id'));					 
						 $PartyBalance1  = ($totalPartyDR1-($totalPartyCR1+$fullCr));					 
						 $this->saveAccountJournal($voucher_no,$PartyAcc_head1,"Supplier",getFromSession('project_id'),getRequest('description'),0,$fullCr,$PartyBalance1,1,$created_date);
							
						//======= Supplier Dr ======			
						$DrAmount = getRequest('paid_amount');
						$PartyAcc_head = getRequest('supplier'); 
						$totalPartyCR  = $this->getTotalCreditAmount($PartyAcc_head,getFromSession('project_id'));
						$totalPartyDR  = $this->getTotalDebitAmount($PartyAcc_head,getFromSession('project_id'));					 
						$PartyBalance  = (($totalPartyDR+$DrAmount)-$totalPartyCR);					 
						$this->saveAccountJournal($voucher_no,$PartyAcc_head,"Supplier",getFromSession('project_id'),getRequest('description'),$DrAmount,0,$PartyBalance,1,$created_date);	
		
						//============== Cash Cr ===============			
						 $CrAmount = getRequest('paid_amount');
						 $acc_head = $this->getCashId(getFromSession('project_id'));
						 $totalCR  = $this->getTotalCreditAmount($acc_head,getFromSession('project_id'));
						 $totalDR  = $this->getTotalDebitAmount($acc_head,getFromSession('project_id'));					 
						 $balance  = ($totalDR-($totalCR+$CrAmount));					 
						 $this->saveAccountJournal($voucher_no,$acc_head,"Acc",getFromSession('project_id'),getRequest('description'),0,$CrAmount,$balance,1,$created_date);	
					 }
				}
				// header("location:index.php?app=purchase&cmd=print_vouchar&voucher_no=".$voucher_no);
			 }elseif($mode_of_payment=="Check"){	
				if(getRequest('due')>0){
					if(getRequest('paid_amount')>0){
						//====== save payable_check ======
						$this->savePayableCheck($voucher_no,$voucher_no,"Payment",getRequest('paid_amount'));
						//======= Supplier Cr ======	
						 $fullCr = (getRequest('net_payble')-getRequest('advanced_paid_amount'));
						 $PartyAcc_head1 = getRequest('supplier'); 
						 $totalPartyCR1  = $this->getTotalCreditAmount($PartyAcc_head1,getFromSession('project_id'));
						 $totalPartyDR1  = $this->getTotalDebitAmount($PartyAcc_head1,getFromSession('project_id'));					 
						 $PartyBalance1  = ($totalPartyDR1-($totalPartyCR1+$fullCr));					 
						 $this->saveAccountJournal($voucher_no,$PartyAcc_head1,"Supplier",getFromSession('project_id'),getRequest('description'),0,$fullCr,$PartyBalance1,1,$created_date);
							
						//======= Supplier Dr ======			
						$DrAmount = getRequest('paid_amount');
						$PartyAcc_head = getRequest('supplier'); 
						$totalPartyCR  = $this->getTotalCreditAmount($PartyAcc_head,getFromSession('project_id'));
						$totalPartyDR  = $this->getTotalDebitAmount($PartyAcc_head,getFromSession('project_id'));					 
						$PartyBalance  = (($totalPartyDR+$DrAmount)-$totalPartyCR);					 
						$this->saveAccountJournal($voucher_no,$PartyAcc_head,"Supplier",getFromSession('project_id'),getRequest('description'),$DrAmount,0,$PartyBalance,1,$created_date);	
					}else{
						//======= Supplier Cr ======	
						 $fullCr = getRequest('due');
						 $PartyAcc_head1 = getRequest('supplier'); 
						 $totalPartyCR1  = $this->getTotalCreditAmount($PartyAcc_head1,getFromSession('project_id'));
						 $totalPartyDR1  = $this->getTotalDebitAmount($PartyAcc_head1,getFromSession('project_id'));					 
						 $PartyBalance1  = ($totalPartyDR1-($totalPartyCR1+$fullCr));					 
						 $this->saveAccountJournal($voucher_no,$PartyAcc_head1,"Supplier",getFromSession('project_id'),getRequest('description'),0,$fullCr,$PartyBalance1,1,$created_date);
					}
				}elseif(getRequest('due')==0){
					if(getRequest('paid_amount')>0){
						//====== save payable_check ======
						$this->savePayableCheck($voucher_no,$voucher_no,"Payment",getRequest('paid_amount'));
						//======= Supplier Cr ======	
						 $fullCr = getRequest('paid_amount');
						 $PartyAcc_head1 = getRequest('supplier'); 
						 $totalPartyCR1  = $this->getTotalCreditAmount($PartyAcc_head1,getFromSession('project_id'));
						 $totalPartyDR1  = $this->getTotalDebitAmount($PartyAcc_head1,getFromSession('project_id'));					 
						 $PartyBalance1  = ($totalPartyDR1-($totalPartyCR1+$fullCr));					 
						 $this->saveAccountJournal($voucher_no,$PartyAcc_head1,"Supplier",getFromSession('project_id'),getRequest('description'),0,$fullCr,$PartyBalance1,1,$created_date);
							
						//======= Supplier Dr ======			
						$DrAmount = getRequest('paid_amount');
						$PartyAcc_head = getRequest('supplier'); 
						$totalPartyCR  = $this->getTotalCreditAmount($PartyAcc_head,getFromSession('project_id'));
						$totalPartyDR  = $this->getTotalDebitAmount($PartyAcc_head,getFromSession('project_id'));					 
						$PartyBalance  = (($totalPartyDR+$DrAmount)-$totalPartyCR);					 
						$this->saveAccountJournal($voucher_no,$PartyAcc_head,"Supplier",getFromSession('project_id'),getRequest('description'),$DrAmount,0,$PartyBalance,1,$created_date);	
					}
				}// end due if
			}elseif($mode_of_payment=="Payable"){
				//======= Supplier Cr ======	
				 $fullCr = (getRequest('net_payble')-getRequest('advanced_paid_amount'));
				 $PartyAcc_head1 = getRequest('supplier'); 
				 $totalPartyCR1  = $this->getTotalCreditAmount($PartyAcc_head1,getFromSession('project_id'));
				 $totalPartyDR1  = $this->getTotalDebitAmount($PartyAcc_head1,getFromSession('project_id'));					 
				 $PartyBalance1  = ($totalPartyDR1-($totalPartyCR1+$fullCr));					 
				 $this->saveAccountJournal($voucher_no,$PartyAcc_head1,"Supplier",getFromSession('project_id'),getRequest('description'),0,$fullCr,$PartyBalance1,1,$created_date);
			}
		 }
						
		  }else {	
			header("location:index.php?app=purchase&cmd=add");	
		  }

    }//EOFn  
	function savePurchaseItem(){
		mysql_query("START TRANSACTION;");
		$PartyAcc_head = getRequest('supplier'); 
		$totalPartyCR  = $this->getTotalCreditAmount($PartyAcc_head,getFromSession('project_id'));
		$totalPartyDR  = $this->getTotalDebitAmount($PartyAcc_head,getFromSession('project_id'));	
		$PreviousPartyBalance = ($totalPartyDR-$totalPartyCR);	
					 
		$voucher_no = $this->saveDebitVouchar();
		$this->saveCreditVouchar($voucher_no);
		$this->insertPurchaseMaster($voucher_no,$PreviousPartyBalance);
		$this->insertPurchaseDetails($voucher_no); 	
		mysql_query("COMMIT;");
		header("location:index.php?app=purchase&cmd=print_vouchar&voucher_no=".$voucher_no);
	}
	function insertPurchaseMaster($voucher_no,$advanced_paid_amount){
		if($advanced_paid_amount>0){
		 
		}
			 
    	 $requestdata = array();	
		  $requestdata = getUserDataSet(PURCHASE_MASTER_TBL);	
		  if($mode_of_payment =="Check"){
			$requestdata['check_no'] = formatDate(getRequest('check_no'));
			$requestdata['check_issue_date'] = formatDate(getRequest('check_issue_date'));
		  }
		  if(getRequest('lcopening_date')!=""){
			$requestdata['lcopening_date'] = formatDate(getRequest('lcopening_date'));
		  }
		  if($advanced_paid_amount>0){
			$requestdata['purchase_type']  	= "Advanced Paid";
		  	$requestdata['paid_amount']  	= (getRequest('paid_amount')+$advanced_paid_amount);
			// will be adjust adv payment in devit tbl 			
		  }else{
			$requestdata['purchase_type']  	= getRequest('mode_of_payment');
		  }
		  $requestdata['item_received_amount']= getRequest('total_value');
		  $requestdata['due']= (getRequest('net_payble')-$requestdata['paid_amount']);		  
		  $requestdata['transaction_type']  = "Payment";    
		  $requestdata['purchase_date'] 	= formatDate(getRequest('purchase_date')); 
		  $requestdata['voucher_no']        = $voucher_no;   
		  $requestdata['project_id']        = getFromSession('project_id');    
		  $requestdata['created_by']        = getFromSession('userid');
	
		  $requestdata['created_date']      = date('Y-m-d h:i:s');
	
		  $info        		=  array();
		  $info['table']	= PURCHASE_MASTER_TBL;
		  $info['data'] 	= $requestdata;     
		  //$info['debug']  	=  true;
		  $res = insert($info);
			
	}

	function getPurchaseMasterInfo($id){		
		   
		$project_id     = getFromSession('project_id');  
		$info           = array();    
		$info['table']  = PURCHASE_MASTER_TBL.' pm,'.SUPPLIER_TBL.' s,'.PROJECT_TBL.' p,'.CURRENCY_TBL.' c';	
		$info['fields'] = array('pm.voucher_no','pm.po_no','p.project_name','p.location','s.name','s.address','pm.quotation_no','pm.lc_no','pm.lcopener','pm.lcopening_bank',"DATE_FORMAT(pm.lcopening_date,'%d %b %y' ) as lcopening_date",'pm.country','pm.lc_details','pm.track_no','pm.van_no','pm.total_value',"DATE_FORMAT(pm.purchase_date,'%d %b %y' ) as purchase_date",'pm.mode_of_payment','c.curr_symble','pm.bank_name','pm.acc_no','pm.check_no','pm.check_no','pm.discount','pm.net_payble','pm.paid_amount','pm.due','pm.ref_no','pm.created_date');
		
		$sql="pm.supplier = s.supplier_code AND pm.project_id = p.project_id AND pm.currency = c.currency_id AND pm.project_id = '".$project_id."' AND pm.voucher_no = '$id'";
							
		$info['where']  =$sql;	  	
	    $info['groupby'] = array("pm.voucher_no");
		//$info['debug']  = true;
		$res            =	select($info);
		if(count($res))
		{
			foreach($res as $i=>$v)
			{
				$data[$i] = $v;             
			}
		}
		  //dumpVar($data);
		  return $data[0];
   } 
           
   function getProductList($id) {  

		$info           = array();    
		$info['table']  =  PURCHASE_DETAILS_TBL.' pd,'.PRODUCT_TBL.' p,'.CURRENCY_TBL.' c,'.BRAND_TBL.' b';	
		$info['fields'] = array('pd.pur_detail_id','pd.voucher_no','pd.project_id','pd.details','pd.serial','pd.warranty','pd.catagory','b.brand_name','pd.product','p.product_name','p.product_desc','pd.m_unit','pd.unit_price','c.curr_symble','pd.qty','pd.rec_qty','pd.total_bag','pd.total','pd.created_time');
		
		$sql="pd.product = p.product_id AND p.brand_code = b.brand_id AND pd.currency = c.currency_id AND pd.voucher_no = '$id'";
		
		$info['where']  = $sql;	  	
	    $info['groupby'] = array("pd.pur_detail_id");
		$info['orderby'] = array("pd.product asc");
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

	function loadProduct4Catagory($brand_id)
	{	 
		  $catagory=trim(getRequest('catagory_id'));
	  	  $project_id = getFromSession('project_id');  
		  $info            = array();
		  $info['table']   = PRODUCT_TBL;
		  $info['fields']  =  array('product_id','product_name','product_desc');
		  //$info['where']   = "catagory = '$catagory' AND brand_code = '$brand_id' AND project_id = '$project_id'";
		  $info['where']   = "brand_code = '$brand_id' AND project_id = '$project_id'";
		  $info['groupby'] = array("product_id");
		  //$info['debug']   = true;
	
		  $result          = select($info);
		  $data            = array();
	
		  if(count($result))
		  {
			 foreach($result as $key=>$value)
			 {
				$data[$key][]        = $value;
			 }
		  }
				
		  foreach($data as $i=>$v)
		  {
			 $subject_idname .= $v[0]->product_id.'#####'.$v[0]->product_name.'#####'.$v[0]->product_desc.'@@@';
		  }
		  echo $subject_idname;	
	}
	function loadProductDtl($product_id){
		  $project_id = getFromSession('project_id');  		 
		  $info            = array();
		  $info['table']   = PRODUCT_TBL." p,".CATAGORY_TBL.' c,'.BRAND_TBL.' b';
		  $info['fields']  =  array('p.m_unit','p.product_catagory','p.product_desc','p.catagory','c.catagory_name','p.brand_code','b.brand_name');
		  $info['where']   = "p.catagory =c.catagory_code AND p.brand_code =b.brand_id AND p.product_id = '$product_id'  AND p.project_id = '$project_id'";
		  $info['groupby'] = array("p.product_id");
		  //$info['debug']   = true;
	
		  $result          = select($info);
		  $data            = array();
	
		  if(count($result))
		  {
			 foreach($result as $key=>$value)
			 {
				$data[$key][]        = $value;
			 }
		  }
				
		  foreach($data as $i=>$v)
		  {
			$str.= $v[0]->m_unit."####".$v[0]->product_catagory."####".$v[0]->product_desc."####".$v[0]->catagory."###".$v[0]->catagory_name."####".$v[0]->brand_code."###".$v[0]->brand_name;
		  }
		  echo $str;	
	}
	function getBankAccountList($purchase_no=null)
	{
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
	
	   if(count($res))
	   {
		  foreach($res as $i=>$v)
		  {
			 $data[$i] = $v;
		  }
	   }
	   if($purchase_no==""){
		return $data; // for list
	  }else{
		return $data[0];	// for view
	  }
	
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
 
 
  function getSupplierList()
  {	
	  $project_id  		= getFromSession('project_id');
      $data 			= array(); 
      $info        		= array();
      $info['table']	= SUPPLIER_TBL;
	  $info['where']   = "project_id = '$project_id'";
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
   function saveAccountJournal($voucher_no,$sub_id,$head_type,$project_id,$description,$DR=NULL,$CR=NULL,$balance,$status=NULL,$purchare_date=NULL){	
   		$rsql= "SELECT head_type FROM ".SUB_ACC_HEAD_TBL." WHERE sub_id='".$sub_id."'";  
		$rres = mysql_query($rsql);
		$hnum = mysql_num_rows($rres);
		if($hnum>0){ 
		$hrow = mysql_fetch_object($rres);
		$head_type= $hrow->head_type;
		}else{
		$head_type= "Supplier";
		}
		$transaction_type = "Purchase";	
			
		$sql = "INSERT INTO ".ACCOUNT_JOURNAL_TBL." (voucher_no,created_date,sub_id,head_type,transaction_type,project_id,description,dr,cr,balance,status) VALUES('".$voucher_no."','".$purchare_date."','".$sub_id."','".$head_type."','".$transaction_type."','".$project_id."','".$description."','".$DR."','".$CR."','".$balance."','".$status."')";
		mysql_query($sql);
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
	function saveStockJournal($voucher_no,$project_id,$product_id,$product_type,$serial=NULL,$warranty=NULL,$unit_price=NULL,$m_unit,$DR=NULL,$CR=NULL,$balance,$create_date=NULL,$store_id=NULL){
		$sql = "INSERT INTO ".STOCK_LEDGER_TBL." (voucher_no,project_id,store_id,product_id,product_type,serial,warranty,unit_price,m_unit,dr,cr,balance,create_date) VALUES('".$voucher_no."','".$project_id."','".$store_id."','".$product_id."','".$product_type."','".$serial."','".$warranty."','".$unit_price."','".$m_unit."','".$DR."','".$CR."','".$balance."','".$create_date."')";
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
  function deleteRecord($id)
  {
   	  if(getRequest('id'))
      { 
      	$info = array();
      	$info['table'] = BANK_ACCOUNT_TBL;
      	$info['where'] = "purchase_no='$id'";
      	$info['debug'] = false;
      	$res = delete($info);      	

      	if($res)
      	{
      	  $msg="Successfully delete Record !!!";
          header("location:?app=bank_account&cmd=view&msg=$msg");     	   

      	} else{
      		 header("location:?app=bank_account&cmd=view&cmd=list&deleted=no");
      	}      	

      }

   } 
 //====================== Start Purchase Details ===============

  function showEditor4PurchaseDetails($msg = null) {        

	  $data                				= array();
	  $data['cmd']         				= getRequest('cmd');
	  $data['record_list'] 				= $this->getPurchaseDetailsList(getRequest('from'),getRequest('to'));
	  $data['totalrecord']				= $this->getTotalPurchaseDetailsList(getRequest('from'),getRequest('to'));	
	  require_once(PURCHASE_DETAILS_SKIN); 
	  return $data[0];

   }
 function getPurchaseDetailsList($from,$to) { 

		if($from == "" && $to == ""){$from=0; $to=500;}
		$date_from 			= formatDate(getRequest('date_from'));
		$date_to 			= formatDate(getRequest('date_to'));				
		$project_id     = getFromSession('project_id');  
		$info           = array();    
		$info['table']  = PURCHASE_MASTER_TBL.' pm,'.SUPPLIER_TBL.' s,'.PROJECT_TBL.' p,'.CURRENCY_TBL.' c';	
		$info['fields'] = array('pm.voucher_no','p.project_name','p.location','s.supplier_code','s.name','s.address','pm.quotation_no','pm.lc_no','pm.lcopener','pm.lcopening_bank',"DATE_FORMAT(pm.lcopening_date,'%d %b %y' ) as lcopening_date",'pm.country','pm.lc_details','pm.track_no','pm.van_no','pm.total_value',"DATE_FORMAT(pm.purchase_date,'%d %b %y' ) as purchase_date",'pm.mode_of_payment','c.curr_symble','pm.bank_name','pm.acc_no','pm.check_no','pm.check_no','pm.discount','pm.net_payble','pm.paid_amount','pm.due','pm.ref_no',"DATE_FORMAT(pm.created_date,'%d %b %y' ) as date",'pm.created_date');
		
		$sql="pm.supplier = s.supplier_code AND pm.project_id = p.project_id AND pm.currency = c.currency_id AND pm.project_id = '".$project_id."' AND pm.purchase_type!='Production'";
									
		if($date_from!="" && $date_to ==""){
			$sql.=" AND pm.created_date >= '$date_from'";
		}elseif($date_from=="" && $date_to !=""){
			$sql.=" AND pm.created_date <= '$date_to'";
		}elseif($date_from!="" && $date_to !=""){
			$sql.=" AND pm.created_date BETWEEN '$date_from' AND '$date_to'";
		}
		$info['where']  =$sql;	
		$info['orderby'] = array("pm.voucher_no asc LIMIT $from,$to");
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

   function getTotalPurchaseDetailsList($from,$to) {  

		$date_from 			= formatDate(getRequest('date_from'));
		$date_to 			= formatDate(getRequest('date_to'));				
		$project_id     = getFromSession('project_id');  
		$info           = array();    
		$info['table']  = PURCHASE_MASTER_TBL.' pm,'.SUPPLIER_TBL.' s,'.PROJECT_TBL.' p,'.CURRENCY_TBL.' c';	
		$info['fields'] = array('pm.voucher_no');
		
		$sql="pm.supplier = s.supplier_code AND pm.project_id = p.project_id AND pm.currency = c.currency_id AND pm.project_id = '".$project_id."'";
									
		if($date_from!="" && $date_to ==""){
			$sql.=" AND pm.created_date >= '$date_from'";
		}elseif($date_from=="" && $date_to !=""){
			$sql.=" AND pm.created_date <= '$date_to'";
		}elseif($date_from!="" && $date_to !=""){
			$sql.=" AND pm.created_date BETWEEN '$date_from' AND '$date_to'";
		}
		$info['where']  =$sql;	

		$info['orderby'] 	= array("pm.created_date asc");
		//$info['debug']  	= true;
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
	function savePayableCheck($voucher_no,$pvoucher_no,$transaction_type,$paid_amount){
	  $requestdata = array();
	  $requestdata = getUserDataSet(PAYABLE_CHECK_TBL);	
	  $requestdata['check_no'] 			= getRequest('check_no');
	  $requestdata['check_issue_date']  = formatDate(getRequest('check_issue_date')); 
	  $requestdata['created_date']      = formatDate(getRequest('purchase_date'));
	  $requestdata['acc_head'] 			= getRequest('supplier'); 
	  $requestdata['head_type'] 		= "Check"; 
	  $requestdata['voucher_no']        = $voucher_no;  
	  $requestdata['pvoucher_no']       = $pvoucher_no; 
	  $requestdata['paid_amount']  		= $paid_amount;   
	  $requestdata['transaction_type']  = $transaction_type;   
	  $requestdata['project_id']        = getFromSession('project_id');    
	  $requestdata['created_by']        = getFromSession('userid');

	  $info        		=  array();
	  $info['table']	= PAYABLE_CHECK_TBL;
	  $info['data'] 	= $requestdata;     
	  //$info['debug']  	=  true;
	  $res = insert($info);
		
	}
   
} // End class
?>
