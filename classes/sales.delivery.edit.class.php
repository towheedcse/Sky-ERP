<?php
class SalesDeliveryEdit{
   
   function run() { 
	 	     
      $cmd = getRequest('cmd');
	  $u_t_id = getFromSession('u_type_id');
      if( ($u_t_id == 101) || ($u_t_id == 102) || ($u_t_id == 103))
      {
      	switch ($cmd){
      	   case 'delivery'              : $screen = $this->showEditor("Edit Page");    break;    
      	   case 'list'               	: $screen = $this->showEditor($msg);   break;
      	   default                      : $cmd = 'list'; $screen = $this->showEditor($msg);   break;
      	}
      }else if( ($u_t_id == 107))
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
	  $voucher_no  = getRequest('voucher_no');
	  $delivery_id = getRequest('delivery_id');
	  $target      = getRequest('target');
	  if($voucher_no!="" && $delivery_id!="") {	
		$this->rollbackSalesDelivery($voucher_no,$delivery_id,$target);	  
	  } 
  }
  
  function rollbackSalesDelivery($voucher_no,$delivery_id,$target){  
        mysql_query("START TRANSACTION;");
        $project_id = getFromSession('project_id');
	$getSql	= "SELECT * FROM ".SALES_DELIVERY_CHALLAN_TBL." WHERE voucher_no = '".$voucher_no."' AND project_id='".$project_id."' AND delivery_master_id='$delivery_id'";
	$gres 	= mysql_query($getSql);
	if(mysql_num_rows($gres)>0){
	  while($row = mysql_fetch_object($gres)){
	  $sal_detail_id 	= $row->sal_detail_id;
	  $pvoucher_no		= $row->pvoucher_no;
	  $product  		= $row->product;
	  $unit_profit  	= $row->unit_profit;
	  $delivery_qty	 	= $row->delivery_qty;
	  $free_qty 		= $row->total_bag;
	  //====== Get and Update Sales Details =======
	  $getSSql	= "SELECT * FROM ".SALES_DETAILS_TBL." WHERE voucher_no = '".$voucher_no."' AND project_id='".$project_id."' AND product='$product' AND sal_detail_id='$sal_detail_id'";
	  $gsres 	= mysql_query($getSSql);
	  $srow 	= mysql_fetch_object($gsres);
	  $prvUnitProfit= ($srow->unit_profit-$unit_profit);
	  $prvDvQty	= ($srow->delivery_qty - $delivery_qty); 	  
	  $prvFreeQty	= ($srow->free_qty - $free_qty);
	  
	  if($item_delivery_amount <0){ $item_delivery_amount=0;}
	  $sduSql = "UPDATE ".SALES_DETAILS_TBL." SET pvoucher_no='', serial='', warranty='', unit_profit='$prvUnitProfit', delivery_qty='$prvDvQty' WHERE voucher_no='".$voucher_no."' AND product='$product' AND sal_detail_id='$sal_detail_id'";
	  $res1 = mysql_query($sduSql);
	  if($res1){ 
	  //echo  $sduSql;
	   }
	  //====== Get and Update Purchase Details =======
	  $getPSql	= "SELECT pur_detail_id,sales_qty FROM ".PURCHASE_DETAILS_TBL." WHERE voucher_no = '".$pvoucher_no."' AND project_id='".$project_id."' AND product='$product'";
	  $gpres 	= mysql_query($getPSql);
	  $prow 	= mysql_fetch_object($gpres);
	  $pur_detail_id 	= $prow->pur_detail_id; 
	  $prvStockQty 		= ($prow->sales_qty-$delivery_qty);
	  $pduSql = "UPDATE ".PURCHASE_DETAILS_TBL." SET sales_qty='$prvStockQty'  WHERE voucher_no='".$pvoucher_no."' AND  product='$product' AND pur_detail_id='$pur_detail_id'";
	  $res2 = mysql_query($pduSql);	  
	  	
	  }//end while
	} // end if
	
	//======= Get from Sales Delivery Master =====
	$getSDMSql	= "SELECT * FROM ".SALES_DELIVERY_MASTER_TBL." WHERE voucher_no = '".$voucher_no."' AND project_id='".$project_id."' AND sales_delivery_master_id='$delivery_id'";
	$gsdmres 	= mysql_query($getSDMSql);
	$sdmrow 	= mysql_fetch_object($gsdmres);
	
	$getSMSql	= "SELECT * FROM ".SALES_MASTER_TBL." WHERE voucher_no = '".$voucher_no."' AND project_id='".$project_id."'";
	$gsmres 	= mysql_query($getSMSql);
	$smrow 		= mysql_fetch_object($gsmres);
	$prvNetPayble = ($smrow->net_payble - $sdmrow->total_value);
	$prvDue 	= ($smrow->due - $sdmrow->roa);
	$prvDeliveryAmount = ($smrow->item_delivery_amount-$sdmrow->total_value);
	//===== Get Sales Master ========
	 $SMUpdate="UPDATE ".SALES_MASTER_TBL." SET net_payble='$prvNetPayble',due='$prvDue',item_delivery_amount='$prvDeliveryAmount',is_deleted=1 WHERE voucher_no='".$voucher_no."' 
	 AND project_id = '$project_id'";
	 $res3 = mysql_query($SMUpdate);

	 //======Start Rollback Adjust Amount ======
	 $project_id = getFromSession('project_id');
	 $getISql	= "SELECT * FROM ".INVOICE_ADJUST_HISTORY_TBL." WHERE voucher_no = '".$voucher_no."' AND project_id='".$project_id."' AND delivery_id ='$delivery_id'";
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
			$getsSql= "SELECT * FROM ".DEVIT_VOUCHAR_TBL." WHERE voucher_no = '".$adjust_ref."' AND project_id='".getFromSession('project_id')."'";
			$gsres  = mysql_query($getsSql);
			$srow = mysql_fetch_object($gsres);
			$paid_amount = ($srow->paid_amount-$adjust_amount);
			$due 		 = ($srow->due+$adjust_amount); 
			$Usql="UPDATE ".DEVIT_VOUCHAR_TBL." SET paid_amount='$paid_amount',due='$due',status=0 WHERE voucher_no='".$adjust_ref."' 
			AND project_id='".$project_id."'";
			$res4 = mysql_query($Usql);
		 }elseif($adjust_tbl_name=="sales_return_payble" && $adjust_type=="-"){
			//======= rollback previous my advanced paid payable amount =========			 
			$getdSql= "SELECT * FROM ".SALES_RETURN_PAYBLE_TBL." WHERE return_id='".$adjust_ref."' AND project_id='".getFromSession('project_id')."' 
			AND paid_amount >0";
			$gdres  = mysql_query($getdSql);
			$drow = mysql_fetch_object($gdres);
			$paid_amount = ($drow->paid_amount-$adjust_amount);
			$due 		 = ($drow->due+$adjust_amount); 
			$Usql="UPDATE ".SALES_RETURN_PAYBLE_TBL." SET paid_amount='$paid_amount',due='$due' WHERE return_id='".$adjust_ref."' 
			AND project_id='".$project_id."'";
			$res4 = mysql_query($Usql);
		 }
		}
	 }
	 //======End Rollback Adjust Amount ======
	//========== Delete All ===========
	$Dsql="DELETE FROM ".SALES_DELIVERY_CHALLAN_TBL." WHERE voucher_no='".$voucher_no."' AND project_id='".$project_id."' AND delivery_master_id ='$delivery_id' ";
	mysql_query($Dsql); 
	$Csql="DELETE FROM ".SALES_DELIVERY_MASTER_TBL." WHERE voucher_no='".$voucher_no."' AND project_id='".$project_id."' AND sales_delivery_master_id ='$delivery_id'";
	mysql_query($Csql);
	$Stsql="DELETE FROM ".STOCK_LEDGER_TBL." WHERE voucher_no='".$voucher_no."' AND project_id='".$project_id."' AND delivery_id ='$delivery_id'";
	mysql_query($Stsql); 
	$Jsql="DELETE FROM ".ACCOUNT_JOURNAL_TBL." WHERE voucher_no='".$voucher_no."' AND project_id='".$project_id."' AND delivery_id ='$delivery_id'";
	mysql_query($Jsql); 
	$Hsql="DELETE FROM ".INVOICE_ADJUST_HISTORY_TBL." WHERE voucher_no='".$voucher_no."' AND project_id='".$project_id."' AND delivery_id ='$delivery_id'";
	mysql_query($Hsql);

	$SQL2 ="UPDATE ".SALES_DETAILS_TBL." SET delivery_qty='0' WHERE voucher_no='".$voucher_no."' AND project_id='".$project_id."'";
	mysql_query($SQL2);
	
	mysql_query("COMMIT;");  
	  
	if($target=="edit"){
	header("location:index.php?app=sales.delivery&cmd=delivery&voucher_no=$voucher_no");
	}else{
	header("location:index.php?app=sales.report&cmd=sales_delivery_list&msg=Successfully Invoice Deleted!!!");
	}
  }
        
} // End class
?>
