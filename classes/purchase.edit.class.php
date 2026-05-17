<?php
require_once('journal.class.php');
class PurchaseEdit extends Journal
{
   
   function run()
   {         

      $cmd = getRequest('cmd');
	  $u_t_id = getFromSession('u_type_id');
      if( ($u_t_id ==101) || ($u_t_id == 102) || ($u_t_id == 103)) // 101 = sysadmin, 102 = admin, 103= salesman
      {

      	switch ($cmd)
      	{
      	   case 'add'			: $this->showEditor(); break;
	   case 'edit'			: $this->showEidtEditor(); break;
	   case 'delete'		: $this->deletePurchaseInvoice(); break;
	   case 'editlc'		: $this->showLCEidtEditor(); break;
	   case 'sal_dtl'		: $this->showEditor4SalesDetails(); break;
	   case 'admin_sal_dtl'		: $this->showAllCompaniesSalesDetails(); break;
   	   case 'loadProduct'  		: $this->loadProduct4Catagory(trim(getRequest('catagory_id'))); break; 
   	   case 'get_dtl'  		: $this->loadProductDtl(trim(getRequest('product_id'))); break;   
	   case 'save_sales'		: $this->saveSalesItem(); break; 
	   case 'print_vouchar'		: $screen = $this->showPrintEditor($msg); break; 
      	   default                   	: $cmd = 'list'; $screen = $this->showEditor();   break;
      	}
      }elseif($u_t_id == 104) // 104 = acc
      {
      	switch ($cmd)
      	{
	   case 'sal_dtl'		: $this->showEditor4SalesDetails(); break;
	   case 'print_vouchar'		: $screen = $this->showPrintEditor($msg); break;
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
         $advArr 					= $this->getSalesMasterInfo($voucher_no);
         $advArr 					= parseThisValue($advArr); 
		 $data   					= array_merge(array(), $advArr); 
      
		 $data['item_list']	= $this->getProductList($voucher_no);
		 $data['message'] 	= $msg;
		 $data['cmd']     	= getRequest('cmd');
		 require_once(SALES_VOUCHAR_SKIN);      
		 return true;
	 }else{
		require_once(PRINT_VOUCHAR_SKIN);
	  }
   }
   function deletePurchaseInvoice(){
	  $voucher_no 	= getRequest('voucher_no');  
	  if($voucher_no !="") {
	    mysql_query("START TRANSACTION;");
		$rres = $this->rollbackPurchase($voucher_no);
		$sres = $this->deletePurchase($voucher_no);
		mysql_query("COMMIT;");
		if($voucher_no!=""){
		 header("location:index.php?app=purchase&cmd=pur_dtl&msg=Successfully delete purchase invoice.");	
		}else{
		 header("location:index.php?app=purchase&cmd=pur_dtl");	
		}
	  }else{
		 header("location:index.php?app=purchase&cmd=pur_dtl");	 
	  } 
   }
   
   function showEidtEditor($msg = null) { 
      require_once(CLASS_DIR.'/purchase.class.php');	
	  $parchApp 			= new Purchase();
	  require_once(CLASS_DIR.'/common.list.class.php');	
	  $comlistApp 		= new CommonList();
	  $voucher_no 		= getRequest('voucher_no');  
	  $data['cmd']      = getRequest('cmd'); 
	  if(getRequest('submit')) {
	    mysql_query("START TRANSACTION;");
		$rres = $this->rollbackPurchase($voucher_no);
		$sres = $this->updatePurchase();
		mysql_query("COMMIT;");
		if($voucher_no!=""){
		 header("location:index.php?app=purchase&cmd=print_vouchar&voucher_no=".$voucher_no);	
		}else{
		 header("location:index.php?app=purchase.edit&cmd=edit&voucher_no=".$voucher_no);	
		}
	  }
	  if(getRequest('pid')) {
		$this->deleteItem();
	  }
	  $advArr 			= $parchApp->getPurchaseMasterInfo($voucher_no);
	  $advArr 			= parseThisValue($advArr); 
	  $data   			= array_merge($advArr); 
	  $data['supplier_list'] 	= $comlistApp->getSupplierList();		
	  $data['item_list']		= $parchApp->getProductList($voucher_no);
	  $data['product_list'] 	= $comlistApp->getProductList();
	  $data['depo_list'] 		= $comlistApp->getDeliveryPointList(true);
	  require_once(CURRENT_APP_SKIN_FILE); 
	  return $data[0];	    
   }
   function showLCEidtEditor($msg = null) { 
      require_once(CLASS_DIR.'/purchase.class.php');	
	  $parchApp 			= new Purchase();
	  require_once(CLASS_DIR.'/common.list.class.php');	
	  $comlistApp 		= new CommonList();
	  $voucher_no 		= getRequest('voucher_no');  
	  $data['cmd']      = getRequest('cmd'); 
	  $project_id = getFromSession('project_id');

	  if(getRequest('submit') == "save") {
	    mysql_query("START TRANSACTION;");
		$rres = $this->rollbackPurchase($voucher_no);
		$sres = $this->updatePurchase();
		mysql_query("COMMIT;");
		if($voucher_no!=""){
		 header("location:index.php?app=purchase&cmd=print_vouchar&voucher_no=".$voucher_no);	
		}else{
		 header("location:index.php?app=purchase.edit&cmd=edit&voucher_no=".$voucher_no);	
		}
	  }
	  if(getRequest('pid')) {
		$this->deleteItem();
	  }
	  $advArr 			= $parchApp->getPurchaseMasterInfo($voucher_no);
	  $advArr 			= parseThisValue($advArr); 
	  $data   			= array_merge($advArr); 

	  require_once(CLASS_DIR.'/deliverypoint.class.php');	
	  $deliveryPoint 		= new DeliveryPoint();
	  $data['section'] 		= $deliveryPoint->getHeadType($data['inventory_id']);
	  
	  $supplier_list 		= $comlistApp->getSupplierListCombined();
          $impoter_list 		= $comlistApp->getImpoterList();
          $cost_center_list       	= $comlistApp->getAccountHeadList("Cost Center");
	  $supplier_list_receivable 	= $comlistApp->getCustomerListReceivable(); 
	  $data['supplier_list'] 	= array_merge($supplier_list, $impoter_list, $cost_center_list, $supplier_list_receivable);

	  $vatSql = "SELECT sub_id FROM " . ACCOUNT_JOURNAL_TBL . " WHERE voucher_no='$voucher_no' AND transaction_type='VAT on purchase' AND project_id='$project_id'";
          $vatRow = mysql_fetch_object(mysql_query($vatSql));
          $data['vat_type'] = $vatRow->sub_id;

          $atSql = "SELECT sub_id FROM " . ACCOUNT_JOURNAL_TBL . " WHERE voucher_no='$voucher_no' AND transaction_type='Advance Tax' AND project_id='$project_id'";
          $atRow = mysql_fetch_object(mysql_query($atSql));
          $data['at_type'] = $atRow->sub_id;		

	  $data['item_list']		= $parchApp->getProductList($voucher_no);
	  $data['product_list'] 	= $comlistApp->getProductList();
	  $data['depo_list'] 		= $comlistApp->getDeliveryPointList(true);

	  $data['equipment_list'] = $comlistApp->getAccountHeadList("Non Current Assets", "S126");
	  $data['raw_material_list'] = $comlistApp->getAccountHeadList("Current Assets", NULL, "C000055");
	  $data['fg_list'] = $comlistApp->getAccountHeadList("Current Assets", NULL, "C000056");
	  $data['maintanance_list'] = $comlistApp->getAccountHeadList("Current Assets", NULL, "C000154");

	  $data['cogsheadlist'] 	= $comlistApp->getAccountHeadList("Cost Center");
          $data['vat_type_list'] 	= $this->getVatHeadList();


	   $supplierData       		= $comlistApp->getSupplierData();
	   $data['supplierData'] 	=json_encode($supplierData, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);

	  require_once(PURCHASE_LC_EDIT_SKIN); 
	  return $data[0];	    
   }

    function getVatHeadList()
    {
        $head_type = "Current Assets";
        $sl_three_head = "S300247";
        $project_id = getFromSession('project_id');
        $data = array();
        $info = array();
        $info['table'] = SUB_ACC_HEAD_TBL;
        $sql = "head_type = '$head_type' AND project_id='" . $project_id . "' AND sl_three_head = '$sl_three_head'";
        $info['where'] = $sql;
        $info['orderby'] = array("sub_head_name ASC");
        $res = select($info);
        if (count($res)) {
            foreach ($res as $i => $v) {
                $data[$i] = $v;
            }
        }
        //dumpVar($data);
        return $data;
    }

   function rollbackPurchase($voucher_no){  
    $project_id = getFromSession('project_id');		
	//===== Get Sales Master ========
	 $SMUpdate="UPDATE ".PURCHASE_MASTER_TBL." SET net_payble='0',due='0',item_received_amount='0' WHERE voucher_no='".$voucher_no."' 
	 AND project_id = '$project_id'";
	 $res3 = mysql_query($SMUpdate);

	 //======Start Rollback Adjust Amount ======
	 $project_id = getFromSession('project_id');
	 $getISql	= "SELECT * FROM ".INVOICE_ADJUST_HISTORY_TBL." WHERE voucher_no = '".$voucher_no."' AND project_id='".$project_id."'";
	 $gires 	= mysql_query($getISql);
	 if(mysql_num_rows($gires)>0){
	  while($irow = mysql_fetch_object($gires)){
		 $adjust_tbl 	= $irow->adjust_tbl; 
		 $adjust_ref 	= $irow->adjust_ref;  
		 $adjust_amount = $irow->adjust_amount; 
		 $adjust_type	= $irow->adjust_type;
		 $adjust_tblArr = explode(".",$adjust_tbl);
		 $adjust_tbl_name = $adjust_tblArr[1];
		 
		 if(($adjust_tbl_name=="cs_delivery_product" || $adjust_tbl_name=="devit_vouchar") && ($adjust_type=="-")){
			//======= rollback previous opening payable amount =========			 
			$getsSql= "SELECT * FROM ".DEVIT_VOUCHAR_TBL." WHERE voucher_no = '".$adjust_ref."' AND project_id='".$project_id."'";
			$gsres  = mysql_query($getsSql);
			$srow = mysql_fetch_object($gsres);
			$paid_amount = ($srow->paid_amount-$adjust_amount);
			$due 		 = ($srow->due+$adjust_amount); 
			$Usql="UPDATE ".DEVIT_VOUCHAR_TBL." SET paid_amount='$paid_amount',due='$due',status=0 WHERE voucher_no='".$adjust_ref."' 
			AND project_id='".$project_id."'";
			$res4 = mysql_query($Usql);
		 }elseif($adjust_tbl_name=="purchase_return_receivable" && $adjust_type=="-"){
			//======= rollback previous my advanced paid payable amount =========			 
			$getdSql= "SELECT * FROM ".PURCHASE_RETURN_RECEIBAVLE_TBL." WHERE return_id='".$adjust_ref."' AND project_id='".$project_id."' 
			AND paid_amount >0";
			$gdres  = mysql_query($getdSql);
			$drow = mysql_fetch_object($gdres);
			$paid_amount = ($drow->paid_amount-$adjust_amount);
			$due 		 = ($drow->due+$adjust_amount); 
			$Usql="UPDATE ".PURCHASE_RETURN_RECEIBAVLE_TBL." SET paid_amount='$paid_amount',due='$due' WHERE return_id='".$adjust_ref."' 
			AND project_id='".$project_id."'";
			$res4 = mysql_query($Usql);
		 }
		}
	 }
	 //======End Rollback Adjust Amount ======
	//========== Delete All ===========	
	$Stsql="DELETE FROM ".STOCK_LEDGER_TBL." WHERE voucher_no='".$voucher_no."' AND project_id='".$project_id."'";
	mysql_query($Stsql); 
	$Jsql="DELETE FROM ".ACCOUNT_JOURNAL_TBL." WHERE voucher_no='".$voucher_no."' AND project_id='".$project_id."'";
	mysql_query($Jsql); 
	$Hsql="DELETE FROM ".INVOICE_ADJUST_HISTORY_TBL." WHERE voucher_no='".$voucher_no."' AND project_id='".$project_id."'";
	mysql_query($Hsql);	
  }
  function deletePurchase($voucher_no){  
    $project_id = getFromSession('project_id');	
	
	$PMsql="DELETE FROM ".PURCHASE_MASTER_TBL." WHERE voucher_no='".$voucher_no."' AND project_id='".$project_id."'";
	mysql_query($PMsql);

	$PDsql="DELETE FROM ".PURCHASE_DETAILS_TBL." WHERE voucher_no='".$voucher_no."' AND project_id='".$project_id."'";
	mysql_query($PDsql);

	$DVsql="DELETE FROM ".DEVIT_VOUCHAR_TBL." WHERE voucher_no='".$voucher_no."' AND project_id='".$project_id."'";
	mysql_query($DVsql);

	$CVsql="DELETE FROM ".CREDIT_VOUCHAR_TBL." WHERE voucher_no='".$voucher_no."' AND project_id='".$project_id."'";
	mysql_query($CVsql);	 
	 
  }
  function updatePurchase(){
  	require_once(CLASS_DIR.'/common.list.class.php');	
	$comlistApp 	= new CommonList();
	require_once(CLASS_DIR.'/purchase.class.php');	
	$parchApp 		= new Purchase();
   	$voucher_no 	= getRequest('voucher_no'); 
	$delivery_point  = getRequest('delivery_point'); 
	$van_no  = getRequest('van_no');
	$ttlfield 		= getRequest('ttlfield');   $supplier	= getRequest('supplier'); 
	$payment_mode   = getRequest('mode_of_payment'); $project_id = getFromSession('project_id'); 
	$purchase_date  = formatDate(getRequest('purchase_date')); $created_by = getFromSession('userid');
	if($payment_mode=="Check"){
	$bank_name = getRequest('bank_name');  $acc_no = getRequest('acc_no');  $check_no = getRequest('check_no'); 
	$check_issue_date = formatDate(getRequest('check_issue_date'));    
	}else{
	$ref_no = getRequest('ref_no');
	}	
	$total_amount  =0; $totalOrderPrice =0; $totalProductDis=0; $unitDiscountAmount=0; $total_order_qty=0;
	$total_free_qty=0; $TotalStockAmount=0; $TotalFreeAmount=0;
	$j=1;
	for($j; $j<$ttlfield; $j++){
		$details_id 	= getRequest("details_id$j"); 
		$product 	= getRequest("product$j"); 
		$order_qty 	= getRequest("order_qty$j"); 
		$free_qty 	= getRequest("free_qty$j");
		$rec_qty 	= ($order_qty+$free_qty);
		$unit_price 	= getRequest("unit_price$j"); 
		$discount_persent = getRequest("discount_per_qty$j"); 		 
		$unitDiscountAmount = (($unit_price/100)*$discount_persent);
		$totalDiscount 	= ($unitDiscountAmount*$order_qty);
		
		$totalAmount 	= ($unit_price*$order_qty); 
		$totalOrderPrice+=$totalAmount;
		$totalPrice 	= ($totalAmount-$totalDiscount);		
		$serial 	= getRequest("serial$j"); 		
		$warranty 	= getRequest("warranty$j"); 
		if($serial==""){ $serial=0;} if($warranty==""){ $warranty=0;}
		if($details_id!="" && $product!=""){
		$perQtyAmount = ($totalAmount/$order_qty);
	    	$PSql="SELECT * FROM ".PRODUCT_TBL." WHERE product_id='$product' AND project_id='$project_id'";
	    	$Prorow = mysql_fetch_object(mysql_query($PSql));
	    	$catagory 	= $Prorow->catagory;
	    	$brand 		= $Prorow->brand_code;
	   	$product_catagory = $Prorow->product_catagory;
	    	$m_unit         = $Prorow->m_unit;	
	    	$product_type   = $Prorow->product_type;    	
		$this->updatePdoductionDtl($voucher_no,$details_id,$product,$delivery_point,$unit_price,$product_catagory,$m_unit);	
		$usql= "UPDATE ".PURCHASE_DETAILS_TBL." SET product='$product',details='',catagory='$catagory',brand_id='$brand',m_unit='$m_unit',serial='$serial',			
		warranty='$warranty',unit_price='$unit_price',discount_per_qty='$discount_persent',discount_amount='$unitDiscountAmount',qty='$order_qty',
		rec_qty='$rec_qty',free_qty='$free_qty',total='$totalPrice' WHERE pur_detail_id=$details_id AND voucher_no='$voucher_no'";
		$ures = mysql_query($usql);
		}else{ 
		if($rec_qty >0 && $product !=""){
		$isql = "INSERT INTO ".PURCHASE_DETAILS_TBL." (voucher_no,project_id,product,catagory,brand_id,m_unit,serial,warranty,
		unit_price,discount_per_qty,discount_amount,qty,rec_qty,free_qty,total,created_by) VALUES(		
		'$voucher_no','$project_id','$product','$catagory','$brand','$m_unit','$serial','$warranty',
		'$unit_price','$discount_persent','$unitDiscountAmount','$order_qty','$rec_qty','$free_qty','$totalPrice','$created_by' 
		)";
		$ures = mysql_query($isql);	
		}
		} // update purchase
		if($ures){		
		//=== Stock Dr =====
		if($rec_qty >0 && $product !=""){
	    	$StockAmount = ($unit_price * $rec_qty);
		$TotalStockAmount+=$StockAmount;
				
		$this->saveAVGPurchasePrice($voucher_no,$project_id,$product,$unit_price);
		$totalCR  = $this->getTotalCreditStock($product,$project_id);
		$totalDR  = $this->getTotalDebitStock($product,$project_id);					 
		$balance  = ($totalDR - ($totalCR+$rec_qty));					
		$this->saveStockJournal($voucher_no,$project_id,$delivery_point,$product,$product_type,$serial,$warranty,$unit_price,$m_unit,$rec_qty,0,$balance,$purchase_date);
		}
		}
		if($free_qty >0){
		$DrAmount = ($unit_price*$free_qty);
		$TotalFreeAmount+=$DrAmount;
		$description = "Receipt free product with purchase item";		
		}
		$total_order_qty+=$order_qty;
		$total_free_qty+=$free_qty;
		$total_order_amount+=$totalPrice;
		$totalProductDis+=$totalDiscount; 

		if ($product != "" && $unit_price > 0) {
		   $PMSql = "SELECT * FROM " . PURCHASE_MASTER_TBL . " WHERE voucher_no='$voucher_no' AND project_id='$project_id'";
	           $Pmrow = mysql_fetch_object(mysql_query($PMSql));
	           $inventory_type = $Pmrow->inventory_type;

	           if (isset($inventory_type) && $inventory_type != "" && $inventory_type != "A000036") {
	               $productRequestData['unit_price'] = $unit_price;
	           }
		        
		        $productRequestData['purchase_unit_price'] = $unit_price;

		        $infoData = array();
		        $infoData['table'] = PRODUCT_TBL;
		        $infoData['data'] = $productRequestData;
		        $infoData['where'] = "product_id ='" . $product . "'";
		        //$infoData['debug']  	=  true;
		        $productRes = update($infoData);
		}
	}
	$general_discount_percent 	= getRequest('general_discount_percent'); 
	$GDiscountAmount=(($total_order_amount/100)*$general_discount_percent);
	
	$exclusive_discount_percent  = getRequest('exclusive_discount_percent'); 
	$deliveryAmountAfterDiscount = ($total_order_amount - $GDiscountAmount);
	$EDiscountAmount = (($deliveryAmountAfterDiscount/100)*$exclusive_discount_percent);
	
	$additional_discount 	 = getRequest('additional_discount');
	$paid_amount		 = getRequest('paid_amount');

        $vat_percentage = getRequest('vat_percent');
        $vat_amount = getRequest('vat_amount');
        $at_percentage = getRequest('AT_percent');
        $at_amount = getRequest('AT_amount');
	
	$net_payble = (($totalOrderPrice + $vat_amount + $at_amount)-($GDiscountAmount+$EDiscountAmount+$totalProductDis+$additional_discount));

	$due	       = ($net_payble-$paid_amount);
  	$discount      = ($GDiscountAmount+$EDiscountAmount+$totalProductDis+$additional_discount);
	$PartyAcc_head = getRequest('supplier');  
	$totalPartyCR  = $comlistApp->getTotalCreditAmount($PartyAcc_head,getFromSession('project_id'));
	$totalPartyDR  = $comlistApp->getTotalDebitAmount($PartyAcc_head,getFromSession('project_id'));	
	$PreviousPartyBalance = ($totalPartyDR-$totalPartyCR);	
	$due 	   	   = getRequest('due');
	if($PreviousPartyBalance>0){
		$dueAmount 	= $net_payble-$paid_amount;
		if($dueAmount>0){
		$supplier 	= getRequest('supplier'); $purchase_date = formatDate(getRequest('purchase_date')); 			
		$restofAmount	= $parchApp->saveAdjustSupplierReceibavle($supplier,$voucher_no,$dueAmount,$purchase_date); 
		$adjustAmount 	= (getRequest('net_payble')-$restofAmount);
		$due 		= (getRequest('net_payble')-$adjustAmount);
		}else{
		$adjustAmount = "0";
		}
	}else{
	   $adjustAmount = $PreviousPartyBalance;	 
	}
	$previour_balance= $PreviousPartyBalance;
	if(getRequest('sub_head_name1')!=""){
	$headtypes    = getRequest('head_type1'); 
	$sub_headtype = getRequest('sub_headtype1'); 
	$child_head1  = getRequest('child_head1'); 
	$slthree_head1= getRequest('sl_three_head1');
	$sub_head_name= getRequest('sub_head_name1');
	$head_details = getRequest('head_details1');
	$bankLoanLTR  = $this->createAccHead($headtypes,$sub_headtype,$child_head1,$slthree_head1,$sub_head_name,$head_details);
	}
	if(getRequest('sub_head_name2')!=""){
	
	$headtypes2   = getRequest('head_type2'); 
	$sub_headtype2= getRequest('sub_headtype2'); 
	$child_head2  = getRequest('child_head2'); 
	$slthree_head2= getRequest('sl_three_head2'); 
	$sub_head_name= getRequest('sub_head_name2');
	$head_details = getRequest('head_details2');
	$marginLC = $this->createAccHead($headtypes2,$sub_headtype2,$child_head2,$slthree_head2,$sub_head_name,$head_details);
	}
	$purchase_date = formatDate(getRequest('purchase_date'));

	$totalVatAmount = getRequest('vat_amount');
        $vatHeadID = getRequest('vat_type');
        if ($totalVatAmount > 0 && $vatHeadID != "") {
            $totalPartyCR = $comlistApp->getTotalCreditAmount($vatHeadID, $project_id);
            $totalPartyDR = $comlistApp->getTotalDebitAmount($vatHeadID, $project_id);
            $balance = (($totalPartyDR + $totalVatAmount) - $totalPartyCR);
            $transaction_type = "VAT on purchase";
            $description = "";
            $comlistApp->saveAccJournal($voucher_no, $vatHeadID, "Purchase", $transaction_type, $project_id, $description, $totalVatAmount, 0, $balance, 0, $purchase_date);
        }
        $totalATAmount = getRequest('AT_amount');
        $ATHeadID = getRequest('AT_type');
        if ($totalATAmount > 0 && $ATHeadID != "") {
            $totalPartyCR = $comlistApp->getTotalCreditAmount($ATHeadID, $project_id);
            $totalPartyDR = $comlistApp->getTotalDebitAmount($ATHeadID, $project_id);
            $balance = (($totalPartyDR + $totalATAmount) - $totalPartyCR);
            $transaction_type = "Advance Tax";
            $description = "";
            $comlistApp->saveAccJournal($voucher_no, $ATHeadID, "Purchase", $transaction_type, $project_id, $description, $totalATAmount, 0, $balance, 0, $purchase_date);
        }

        $inventory_type = getRequest('inventory_type');
        $inventory_id = getRequest('inventory_id');
		
  	$pmusql = "UPDATE ".PURCHASE_MASTER_TBL." SET supplier='$supplier',";
	if($bankLoanLTR!="" || $$marginLC!=""){
	$pmusql .= "lc_no='$bankLoanLTR',lcopener='$marginLC',";
	}
	$pmusql .= " purchase_date='$purchase_date',van_no='$van_no',inventory_type='$inventory_type',inventory_id='$inventory_id',	mode_of_payment='$payment_mode',bank_name='$bank_name',acc_no='$acc_no',check_no='$check_no',check_issue_date='$check_issue_date',
	ref_no='$ref_no',total_value='$totalOrderPrice',general_discount_percent='$general_discount_percent',
	general_discount_amount='$GDiscountAmount',exclusive_discount_percent='$exclusive_discount_percent',	exclusive_discount_amount='$EDiscountAmount',product_discount='$totalProductDis',discount='$discount',additional_discount='$additional_discount',
	store_id='$delivery_point',previour_balance='$previour_balance',net_payble='$net_payble',paid_amount='$paid_amount',due='$due',
	item_received_amount='$totalOrderPrice',vat_percentage='$vat_percentage',vat_amount='$vat_amount',at_percentage='$at_percentage',at_amount='$at_amount' WHERE voucher_no='$voucher_no'";
  	$smres 	= mysql_query($pmusql);
	if($discount >0){	 
	 $DisAmount   = $discount;
	 //========= Purchase Discount Cr =========
	 $DiscountId 	  = $comlistApp->getPurchaseDiscountId($project_id);
	 if($DiscountId){	 	 
	 $description	  = "Give discount with purchase item";
	 $DiscountBL 	  = $comlistApp->getAccounceBalance($DiscountId,$project_id);
	 $DiscountBalance = ($DiscountBL-$discount);
	 $comlistApp->saveAccJournal($voucher_no,$DiscountId,"Purchase","Purchase discount",$project_id,$description,0,$discount,$DiscountBalance,0,$purchase_date);
	 }
	}//End TotalDiscount

	//=== Stock Dr Amount =====
	if($TotalStockAmount >0){
	  $PMSql="SELECT * FROM ".PURCHASE_MASTER_TBL." WHERE voucher_no='$voucher_no' AND project_id='$project_id'";
    	  $Pmrow = mysql_fetch_object(mysql_query($PMSql));
    	  $StockId = $Pmrow->inventory_type;
	  if(!empty($Pmrow->inventory_id)){
	  	$StockId 	 = $Pmrow->inventory_id;
	  } 
	  $TotalStock    = $comlistApp->getAccounceBalance($StockId,$project_id);
	  $StockBalance  = ($TotalStock+$TotalStockAmount);	
	  $description   = "Purchase Item";				 
	  $comlistApp->saveAccJournal($voucher_no,$StockId,"Stock","Purchase Item",$project_id,$description,$TotalStockAmount,0,$StockBalance,0,$purchase_date);
	}
	//========= Free Product Cr ==========
	if($TotalFreeAmount >0){		
	  $description     = "Receipt free with purchase item";		
	  $freeItemhead    = $comlistApp->getPurchaseDiscountId($project_id);
	  $TotalFreeBL 	   = $comlistApp->getAccounceBalance($freeItemhead,$project_id);
	  $freeItemBalance = ($TotalFreeBL -$TotalFreeAmount);
	  $comlistApp->saveAccJournal($voucher_no,$freeItemhead,"Acc","Free Item",$project_id,$description,0,$TotalFreeAmount,$freeItemBalance,0,$purchase_date,0);
	}

	$supplier = getRequest('supplier');
	$this->updatePurchaseVoucher($voucher_no,$supplier,$net_payble,$paid_amount,$due);
	if($smres){ return true;  }else{ return false; }
	   
   }
   function saveAVGPurchasePrice($voucher_no,$project_id,$product_id,$purchase_price){	
		$sql = "INSERT INTO ".AVG_PURCHASE_PRICE_TBL."(voucher_no,project_id,product_id,purchase_price) 
		VALUES('".$voucher_no."','".$project_id."','".$product_id."','".$purchase_price."')"; 
		$ires = mysql_query($sql);
		$avg_purchase_price	=0; 
		if($ires){
			$Prosql 		= "SELECT purchase_price  FROM ".AVG_PURCHASE_PRICE_TBL." WHERE product_id = '$product_id' AND project_id = '$project_id' ORDER BY `id` DESC LIMIT 0 , 2";
			$pres 			= mysql_query($Prosql);
			$ttl_product 	= mysql_num_rows($pres);
			if($ttl_product >0){
				while($prow = mysql_fetch_object($pres)){
					$avg_purchase_price += $prow->purchase_price;
				}		
				$avg_purchase_price = ($avg_purchase_price / $ttl_product);
			}
			if(intval($avg_purchase_price)==""){ $avg_purchase_price=0;}			
			
			if($avg_purchase_price ==0){
				$avg_purchase_price = $purchase_price;
			}
			$USQL 	= "UPDATE ".PRODUCT_TBL." SET purchase_unit_price = $avg_purchase_price WHERE product_id = '$product_id' AND project_id = '$project_id'";
			//mysql_query($USQL);
		}
   }
   function updatePdoductionDtl($voucher_no,$details_id,$product_id,$store_id,$uprice,$product_type,$m_unit){
	$project_id 	= getFromSession('project_id');
	   
	$PSql="SELECT* FROM ".PURCHASE_DETAILS_TBL." WHERE pur_detail_id='$details_id' AND project_id='$project_id'";
	$Prorow = mysql_fetch_object(mysql_query($PSql));
	$old_product = $Prorow->product; 
	if($product_id!=$old_product){	
	   $getdSql	= "SELECT * FROM ".PRODUCTION_DETAILS_TBL." WHERE pvoucher_no='".$voucher_no."' AND project_id='".$project_id."' AND product='$old_product'";
	   $gdres  	= mysql_query($getdSql);
	   if(mysql_num_rows($gdres)>0){	   
	    $usql = "UPDATE ".PRODUCTION_DETAILS_TBL." SET out_store_id='$store_id',product='$product_id',amount='$uprice' WHERE pvoucher_no='$voucher_no' 
		AND project_id='".$project_id."' AND product='$old_product'";
		mysql_query($usql);	
		// == will be update at stock dtl ======
		$susql = "UPDATE ".STOCK_LEDGER_TBL." SET store_id='$store_id',product_id='$product_id',unit_price='$uprice',product_type='$product_type',
		m_unit='$m_unit' WHERE po_no='$voucher_no' AND project_id='".$project_id."' AND product_id='$old_product'";
		mysql_query($susql);	
	   }
	}else{
	   $getdSql	= "SELECT * FROM ".PRODUCTION_DETAILS_TBL." WHERE pvoucher_no='".$voucher_no."' AND project_id='".$project_id."' AND product='$product_id'";
	   $gdres  	= mysql_query($getdSql);
	   if(mysql_num_rows($gdres)>0){	   
	   $usql ="UPDATE ".PRODUCTION_DETAILS_TBL." SET out_store_id='$store_id',amount='$uprice' WHERE pvoucher_no='$voucher_no' AND project_id='".$project_id."' 
	   AND product='$product_id'";
  		mysql_query($usql);	
		// == will be update at stock dtl ======
		$susql = "UPDATE ".STOCK_LEDGER_TBL." SET store_id='$store_id',unit_price='$uprice' WHERE po_no='$voucher_no' AND project_id='".$project_id."' 
		AND product_id='$product_id'";
		mysql_query($susql);	
	   }
	}
   }
   function createAccHead($head_type,$sub_headtype,$child_head,$sl_three_head,$sub_head_name,$head_details){
	 require_once(CLASS_DIR.'/common.class.php');	
	 $comApp	= new Common(); 
     	 $sub_id 	= $comApp->NewID(SUB_ACC_HEAD_TBL,"sub_id","A000001","A",7);
	 $project_id	= getFromSession('project_id');	  
	  if($head_type=="Current Assets" || $head_type=="Non Current Assets"){
	    $group_ledger = "ASSETS"; 
	  }elseif($head_type=="Current Liabilities" || $head_type=="Non Current Liabilities"){
	    $group_ledger = "LIABILITIES"; 
	  }elseif($head_type=="Capital" || $head_type=="Retained earnings" || $head_type=="Retained Earnings"){
	    $group_ledger = "EQUITY"; 
	  }elseif($head_type=="Operating Revenue" || $head_type=="Non-Operating Revenue"){
	    $group_ledger = "REVENUE"; 
	  }elseif($head_type=="Direct Expenses" || $head_type=="Indirect Expenses"){
	    $group_ledger = "EXPENSES"; 
	  }
	 $created_by 	= getFromSession('userid');
	 $created_time 	= date('Y-m-d h:i:s');
	 $acisql= "INSERT INTO ".SUB_ACC_HEAD_TBL." (sub_id,sub_headtype,child_head,sl_three_head,sub_head_name,head_details,group_ledger,head_type,project_id,created_by,created_time) 
	 VALUES('$sub_id','$sub_headtype','$child_head','$sl_three_head','$sub_head_name','$head_details','$group_ledger','$head_type','$project_id','$created_by','$created_time')";
	 $ures = mysql_query($acisql);	
	 if($ures){
		 return $sub_id;
	 }else{ return false;}
   
   }
   function updateProductionOutAmount($voucher_no){
	   $project_id 	= getFromSession('project_id');
	   $getdSql		= "SELECT * FROM ".PRODUCTION_DETAILS_TBL." WHERE pvoucher_no='".$voucher_no."' AND project_id='".$project_id."' GROUP BY production_id";
	   $gdres  		= mysql_query($getdSql);
	   while($drow = mysql_fetch_object($gdres)){
		   $production_id = $drow->production_id;
		   if($production_id!=""){
		   $pdSql		= "SELECT * FROM ".PRODUCTION_DETAILS_TBL." WHERE pvoucher_no='".$voucher_no."' AND production_id='".$production_id."'";
		   $pdres  		= mysql_query($pdSql);
		   $totalOutAmount=0;
		   while($pdrow = mysql_fetch_object($pdres)){
		   $outQty = $pdrow->qty; $price = $pdrow->amount;
		   $amount = ($outQty*$price);
		   $totalOutAmount+=$amount; $amount=0;
		   }
		   //===== Update Production Master ========
		   $pmusql ="UPDATE ".PRODUCTION_MASTER_TBL." SET total_value='$totalOutAmount' WHERE production_id='$production_id' AND project_id='".$project_id."'";
  		   mysql_query($pmusql);
		   
		   $ausql ="UPDATE ".ACCOUNT_JOURNAL_TBL." SET cr='$totalOutAmount' WHERE voucher_no='$production_id' AND project_id='".$project_id."'
		    AND head_type='Stocks'";
  		   mysql_query($ausql);
		   }
	   }
   }
   function updatePurchaseVoucher($voucher_no,$customer,$order_amount,$paid_amount,$due){
    	require_once(CLASS_DIR.'/common.list.class.php');	
	$comlistApp = new CommonList();
    	$project_id = getFromSession('project_id'); $sales_date = formatDate(getRequest('purchase_date')); 		
	$payment_mode   = getRequest('mode_of_payment'); 
	if($payment_mode=="Check"){
	$bank_name = getRequest('bank_name'); $acc_no = getRequest('acc_no'); $check_no = getRequest('check_no');
	$check_issue_date = formatDate(getRequest('check_issue_date')); 
	$DrAcc 			= $customer;  
	$CrAcc 			= $acc_no; 
	$paidAmount		= $paid_amount; 
	}elseif($payment_mode=="Cash"){
	$bank_name = "";  $acc_no = "";  $check_no = ""; 
	$check_issue_date 	= "0000-00-00"; 
	$DrAcc 				= $customer;  
	$CrAcc 				= $comlistApp->getCashId($project_id);
	$paidAmount			= $paid_amount;
	}else{
	$ref_no 			= getRequest('ref_no');
	$DrAcc  			= $acc_no;
	$CrAcc 				= $customer;
	$paidAmount			= $order_amount;
	}
	$created_date = formatDate(getRequest('purchase_date')); 
  	$DrVUpdate="UPDATE ".DEVIT_VOUCHAR_TBL." SET account_head='$DrAcc',mode_of_payment='$payment_mode',bank_name='$bank_name',acc_no='$acc_no',check_no='$check_no',
	check_issue_date='$check_issue_date',ref_no='$ref_no',debit='$paidAmount' WHERE voucher_no='".$voucher_no."' AND project_id = '$project_id'";
	 mysql_query($DrVUpdate);	 
	 $CrVUpdate="UPDATE ".CREDIT_VOUCHAR_TBL." SET account_head='$CrAcc',credit='$paidAmount' WHERE voucher_no='".$voucher_no."' AND project_id = '$project_id'";
	 mysql_query($CrVUpdate);
 		 
	 $DrAmount 	 = $paid_amount;
	 $project_id = getFromSession('project_id');
	 $cost_center = getRequest('cost_center');

	 if($payment_mode=="Cash"){ 
		if($due >0){					
			//======= Supplier Cr ======	
			$description = getRequest('description');
			if($description==""){ $description = "Amount payable against purchase item";}	
			 $fullCr 	= getRequest('net_payble');
			 $PartyAcc_head = getRequest('supplier'); 
			 $totalPartyCR  = $comlistApp->getTotalCreditAmount($PartyAcc_head,$project_id);
			 $totalPartyDR  = $comlistApp->getTotalDebitAmount($PartyAcc_head,$project_id);	
			 $PreviousPartyBalance = ($totalPartyDR-$totalPartyCR);					 
			 $PartyBalance  = ($totalPartyDR-($totalPartyCR+$fullCr));
			 $this->saveAccountJournal($voucher_no,$PartyAcc_head,"Supplier",$project_id,$description,0,$fullCr,$PartyBalance,0,$created_date,$cost_center);
			 if($DrAmount >0){
		     	 //======= Supplier Dr ======	
			 $description = getRequest('description');
			 if($description==""){ $description = "Paid amount by cash against purchase item";}
			 $DrAmount       = getRequest('paid_amount');
			 $PartyAcc_head1 = getRequest('supplier'); 
			 $totalPartyCR1  = $comlistApp->getTotalCreditAmount($PartyAcc_head1,$project_id);
			 $totalPartyDR1  = $comlistApp->getTotalDebitAmount($PartyAcc_head1,$project_id); 
			 $PartyBalance1  = (($totalPartyDR1+$DrAmount)-$totalPartyCR1);	
			 $this->saveAccountJournal($voucher_no,$PartyAcc_head1,"Supplier",$project_id,$description,$DrAmount,0,$PartyBalance1,0,$created_date,$cost_center);	
			//============== Cash Cr ===============			
			$description = getRequest('description');
			if($description==""){ $description = "Paid amount by cash against purchase item";}
			if(getFromSession('u_type_id')==102){ // 102 = Pharmecy
			$acc_head     	= getFromSession('cash_id'); 	
			}else{
			$acc_head     	= $comlistApp->getCashId($project_id);
			}
			$totalCR  = $comlistApp->getTotalCreditAmount($acc_head,getFromSession('project_id'));
			$totalDR  = $comlistApp->getTotalDebitAmount($acc_head,getFromSession('project_id')); 
			$balance  = ($totalDR-($totalCR+$DrAmount));					 
			$this->saveAccountJournal($voucher_no,$acc_head,"Cash",$project_id,$description,0,$DrAmount,$balance,0,$created_date,$cost_center);	
			}
		}elseif($due==0){	
			//======= Supplier Cr ======	
			$description = getRequest('description');
			if($description==""){ $description = "Amount payable against purchase item";}
			$fullCr 	= getRequest('net_payble');
			$PartyAcc_head = getRequest('supplier'); 
			$totalPartyCR  = $comlistApp->getTotalCreditAmount($PartyAcc_head,$project_id);
			$totalPartyDR  = $comlistApp->getTotalDebitAmount($PartyAcc_head,$project_id);	
			$PreviousPartyBalance = ($totalPartyDR-$totalPartyCR);					 
			$PartyBalance  = ($totalPartyDR-($totalPartyCR+$fullCr));
			$this->saveAccountJournal($voucher_no,$PartyAcc_head,"Supplier",$project_id,$description,0,$fullCr,$PartyBalance,0,$created_date,$cost_center);
			//======= Supplier Dr ======
			$description = getRequest('description');
			if($description==""){ $description = "Paid amount by cash against purchase item";}
			$DrAmount       = getRequest('paid_amount');
			$PartyAcc_head1 = getRequest('supplier'); 
			$totalPartyCR1  = $comlistApp->getTotalCreditAmount($PartyAcc_head1,$project_id);
			$totalPartyDR1  = $comlistApp->getTotalDebitAmount($PartyAcc_head1,$project_id); 
			$PartyBalance1  = (($totalPartyDR1+$DrAmount)-$totalPartyCR1);	
			$this->saveAccountJournal($voucher_no,$PartyAcc_head1,"Supplier",$project_id,$description,$DrAmount,0,$PartyBalance1,0,$created_date,$cost_center);	
			//============== Cash Cr ===============
			$description = getRequest('description');
			if($description==""){ $description = "Paid amount by cash against purchase item";}	
			if(getFromSession('u_type_id')==102){ // 102 = Pharmecy
			$acc_head     	= getFromSession('cash_id'); 	
			}else{
			$acc_head     	= $comlistApp->getCashId($project_id);
			}
			$totalCR  = $comlistApp->getTotalCreditAmount($acc_head,$project_id);
			$totalDR  = $comlistApp->getTotalDebitAmount($acc_head,$project_id);
			$balance  = ($totalDR-($totalCR+$CrAmount));					 
			$this->saveAccountJournal($voucher_no,$acc_head,"Cash",$project_id,$description,0,$CrAmount,$balance,0,$created_date,$cost_center);	
		}
	 }elseif($payment_mode=="Check"){
	 
		//====== save payable_check ======
		$this->savePayableCheck($voucher_no,$voucher_no,"Payment",getRequest('paid_amount'));
		//======= Supplier Cr ======
		$description = getRequest('description');
		if($description==""){ $description = "Amount payable against purchase item";}	
		$fullCr = getRequest('net_payble');
		$PartyAcc_head1 =getRequest('supplier'); 
		$totalPartyCR1  =$comlistApp->getTotalCreditAmount($PartyAcc_head1,$project_id);
		$totalPartyDR1  =$comlistApp->getTotalDebitAmount($PartyAcc_head1,$project_id);
		$PartyBalance1  =($totalPartyDR1-($totalPartyCR1+$fullCr));					 
		$this->saveAccountJournal($voucher_no,$PartyAcc_head1,"Supplier",$project_id,$description,0,$fullCr,$PartyBalance1,0,$created_date,$cost_center);
		//======= Supplier Dr ======
		$description = getRequest('description');
		if($description==""){ $description = "Paid amount by cheque against purchase item";}
		$DrAmount      = getRequest('paid_amount');
		$PartyAcc_head = getRequest('supplier'); 
		$totalPartyCR  = $comlistApp->getTotalCreditAmount($PartyAcc_head,$project_id);
		$totalPartyDR  = $comlistApp->getTotalDebitAmount($PartyAcc_head,$project_id);	 
		$PartyBalance  = (($totalPartyDR+$DrAmount)-$totalPartyCR);					 
		$this->saveAccountJournal($voucher_no,$PartyAcc_head,"Supplier",$project_id,$description,$DrAmount,0,$PartyBalance,0,$created_date,$cost_center);	
	
	 }elseif($payment_mode=="Payable"){		
		//======= Supplier Cr ======	
		$description = getRequest('description');
		if($description==""){ $description = "Amount payable against purchase item";}
		$fullCr = getRequest('net_payble');
		$PartyAcc_head1 = getRequest('supplier'); 
		$totalPartyCR1  = $comlistApp->getTotalCreditAmount($PartyAcc_head1,$project_id);
		$totalPartyDR1  = $comlistApp->getTotalDebitAmount($PartyAcc_head1,$project_id);
		$PartyBalance1  = ($totalPartyDR1-($totalPartyCR1+$fullCr));					 
		$this->saveAccountJournal($voucher_no,$PartyAcc_head1,"Supplier",$project_id,$description,0,$fullCr,$PartyBalance1,0,$created_date,$cost_center);				
	}
	
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
  function saveStockJournal($voucher_no,$project_id,$store_id,$product_id,$product_type,$serial=NULL,$warranty=NULL,$unit_price=NULL,$m_unit,$DR=NULL,$CR=NULL,$balance,$create_date=NULL){
	$sql = "INSERT INTO ".STOCK_LEDGER_TBL." (voucher_no,project_id,store_id,product_id,product_type,serial,warranty,note,unit_price,m_unit,dr,cr,balance,create_date) VALUES('".$voucher_no."','".$project_id."','".$store_id."','".$product_id."','".$product_type."','".$serial."','".$warranty."','Purchase Item','".$unit_price."','".$m_unit."','".$DR."','".$CR."','".$balance."','".$create_date."')";
	mysql_query($sql);
  }
 function saveAccountJournal($voucher_no,$sub_id,$head_type,$project_id,$description,$DR=NULL,$CR=NULL,$balance,$status=NULL,$purchare_date=NULL, $cost_center = ""){	
	$head_type = getHeadType($sub_id); $transaction_type = "Purchase";			
	$sql = "INSERT INTO ".ACCOUNT_JOURNAL_TBL." (voucher_no,created_date,sub_id,head_type,transaction_type,project_id,description,dr,cr,balance,status,cost_center) VALUES('".$voucher_no."','".$purchare_date."','".$sub_id."','".$head_type."','".$transaction_type."','".$project_id."','".$description."','".$DR."','".$CR."','".$balance."','".$status."','" . $cost_center . "')";
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
   function getCapitalId($project_id){
		$sql = "SELECT sub_id FROM ".SUB_ACC_HEAD_TBL." WHERE head_type = 'Capital' AND project_id = '$project_id'";
		$row = mysql_fetch_object(mysql_query($sql));
		return $sub_id = $row->sub_id;
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
 function deleteItem(){
	require_once(CLASS_DIR.'/common.class.php');	
	$comApp = new Common(); 
	$details_id 	= getRequest('pid');
	$voucher_no 	= getRequest('voucher_no');
	$comApp->deleteRecord(PURCHASE_DETAILS_TBL,"pur_detail_id",$details_id,"purchase.edit","edit&voucher_no=$voucher_no"); 
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
} // End class


?>
