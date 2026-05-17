<? 
//==== Start Sales Order & Delivery ========
function getTotalSalesDeliveryAmount($store_id,$project_id){	 
	$from_date 	= formatDate(getRequest('date_from'));
	$to_date 	= formatDate(getRequest('date_to'));
	$customer 	= getRequest('customer');
	$sales_amount=0; 	
	$Psql= "SELECT SUM(total_value) AS sales_amount FROM ".SALES_DELIVERY_MASTER_TBL." WHERE project_id='$project_id'";	
	
	if($store_id!=""){
		$Psql.=" AND delivery_point='$store_id' ";
	}		
	if($customer!=""){
		$Psql.=" AND customer = '$customer'";
	}
	if($from_date!="" && $to_date ==""){
		$Psql.=" AND delivery_date >= '$from_date'";
	}elseif($from_date=="" && $to_date !=""){
		$Psql.=" AND delivery_date <= '$to_date'";
	}elseif($from_date!="" && $to_date !=""){
		$Psql.=" AND delivery_date BETWEEN '$from_date' AND '$to_date'";
	}
	//echo $Psql;
	$pres = mysql_query($Psql); 
	$cnum = mysql_num_rows($pres);
	if($cnum>0){
	$prow = mysql_fetch_object($pres);
	$sales_amount = $prow->sales_amount;
	}
	//SELECT SELECT SUM(total_value) AS sales_amount FROM `sales_delivery_item_master` WHERE `customer` =''	
	return $sales_amount; 
}

function getTotalSalesOrderAmount($store_id,$project_id,$type){	 
	$from_date 	= formatDate(getRequest('date_from'));
	$to_date 	= formatDate(getRequest('date_to'));
	$customer 	= getRequest('customer');  
	$division_id 	= getRequest('division_id');  
	$district 	= getRequest('district'); 
	$original_copy 	= getRequest('original_copy'); 
	$sales_amount=0; 
	if($original_copy==1){
	 $Psql= "SELECT SUM(net_payble) AS sales_amount FROM ".SALES_MASTER_APP_TBL." WHERE project_id='$project_id'";

	}else{	
	 $Psql= "SELECT SUM(net_payble) AS sales_amount FROM ".SALES_MASTER_TBL." WHERE project_id='$project_id'";
		
	 if($type=="Order"){$Psql.=" AND item_delivery_amount=0 AND status=1 AND is_deleted=0";}else{$Psql.=" AND item_delivery_amount >0 AND status=1 AND is_deleted=0";} 
	}
	if($division_id!=""){
		$Psql.=" AND division = '$division_id'";
	}
	if($district!=""){
		$Psql.=" AND district = '$district'";
	}
	if($store_id!=""){
		$Psql.=" AND delivery_point='$store_id' ";
	}		
	if($customer!=""){
		$Psql.=" AND customer = '$customer'";
	}
	if($from_date!="" && $to_date ==""){
		$Psql.=" AND sales_date >= '$from_date'";
	}elseif($from_date=="" && $to_date !=""){
		$Psql.=" AND sales_date <= '$to_date'";
	}elseif($from_date!="" && $to_date !=""){
		$Psql.=" AND sales_date BETWEEN '$from_date' AND '$to_date'";
	}

	$pres = mysql_query($Psql); 
	$cnum = mysql_num_rows($pres);
	if($cnum>0){
	$prow = mysql_fetch_object($pres);
	$sales_amount = $prow->sales_amount;
	}
	//SELECT SELECT SUM(total_value) AS sales_amount FROM `sales_delivery_item_master` WHERE `customer` =''	
	return $sales_amount; 
}
//==== End Sales Order & Delivery ========
  
function getTotalCatagoryOBQty($catagory,$project_id){	
	$product_type = getRequest('product_type');
	$stock_id 	  = getRequest('store_id');  
	$from_date 	  = formatDate(getRequest('date_from')); $to_date = formatDate(getRequest('date_to')); 
	$ob_balance=0;
    if($from_date==""){   
		$Psql= "SELECT SUM(op_qty) as op_qty FROM ".STOCK_STATUS_BY_DATE_VIEW." WHERE catagory='$catagory' AND project_id='$project_id' ";
		if($product_type!=""){$Psql.=" AND product_type='$product_type'";}
		if($stock_id!=""){$Psql.=" AND store_id='$stock_id'";}
		$Psql.=" group by catagory";
		$pres = mysql_query($Psql);		
		$prow = mysql_fetch_object($pres);
		$ob_balance   = $prow->op_qty;	
	}else{
		$Psql= "SELECT (SUM(s.dr)- SUM(s.cr)) AS ob_balance FROM ".STOCK_LEDGER_TBL." as s, ".PRODUCT_TBL." as p  WHERE 
		s.`product_id`=p.product_id AND p.catagory = '$catagory' AND s.project_id='$project_id'";
		if($product_type!=""){$Psql.=" AND p.product_type='$product_type'";}
		if($stock_id!=""){$Psql.=" AND s.store_id='$stock_id'";}		
		if($from_date!=""){
			$Psql.=" AND s.create_date < '$from_date'";
		}
		$Psql.=" group by p.catagory";	
		$cres = mysql_query($Psql);		
		$crow = mysql_fetch_object($cres);
		$ob_balance = $crow->ob_balance;		
	}
	if(intval($ob_balance)==""){$ob_balance=0;}
	return $ob_balance;
}
function getAvgCatagoryOBRate($catagory,$project_id){
	$product_type = getRequest('product_type');
	$stock_id 	  = getRequest('store_id');   	 
	$opening_rate=0;
	$from_date 	 = formatDate(getRequest('date_from')); $to_date = formatDate(getRequest('date_to'));
    if($from_date==""){   
		$Psql= "SELECT AVG(unit_price) as opening_rate FROM ".PRODUCT_TBL." WHERE catagory='$catagory' AND project_id='$project_id' ";
		if($product_type!=""){$Psql.=" AND product_type='$product_type'";}
		$Psql.=" GROUP BY catagory ";	
		$pres = mysql_query($Psql);
		$prow = mysql_fetch_object($pres);
		$opening_rate   = $prow->opening_rate;	
	}else{
		$csql= "SELECT AVG(s.unit_price) AS opening_rate FROM ".STOCK_LEDGER_TBL." as s, ".PRODUCT_TBL." as p  WHERE s.`product_id`=p.product_id AND p.catagory = '$catagory' AND s.dr >0 AND s.project_id='$project_id'";
		if($product_type!=""){$csql.=" AND p.product_type='$product_type'";}
		if($stock_id!=""){$csql.=" AND s.store_id='$stock_id'";}
		if($from_date!=""){
			$csql.=" AND s.create_date < '$from_date'";
		}
		$csql.=" group by p.catagory";
		$cres = mysql_query($csql);		
		$crow = mysql_fetch_object($cres);
		$opening_rate = $crow->opening_rate;
	}
	if(intval($opening_rate)==""){$opening_rate=0;}
	return $opening_rate;
}
function getAvgCatagoryInRate($catagory,$project_id){	
	$product_type = getRequest('product_type'); 
	$stock_id 	  = getRequest('store_id');  	 
	$closing_rate=0;
	$from_date 	 = formatDate(getRequest('date_from')); $to_date = formatDate(getRequest('date_to'));
    $csql= "SELECT AVG(s.unit_price) AS closing_rate  FROM ".STOCK_LEDGER_TBL." as s, ".PRODUCT_TBL." as p  WHERE s.`product_id`=p.product_id AND p.catagory = '$catagory' AND s.dr >0 AND s.project_id='$project_id'";
	if($product_type!=""){$csql.=" AND p.product_type='$product_type'";}
	if($stock_id!=""){$csql.=" AND s.store_id='$stock_id'";}		
	if($from_date!="" && $to_date ==""){
		$csql.=" AND s.create_date >= '$from_date'";
	}elseif($from_date=="" && $to_date !=""){
		$csql.=" AND s.create_date <= '$to_date'";
	}elseif($from_date!="" && $to_date !=""){
		$csql.=" AND s.create_date BETWEEN '$from_date' AND '$to_date'";
	}
	$csql.=" group by p.catagory";
	$cres = mysql_query($csql);
	$crow = mysql_fetch_object($cres);
	$closing_rate= $crow->closing_rate;
	if(intval($closing_rate)==""){$closing_rate=0;}
	return $closing_rate;
}
function getAvgCatagoryOutRate($catagory,$project_id){	
	$product_type = getRequest('product_type'); 
	$stock_id 	  = getRequest('store_id'); $OutRate=0;
	$from_date 	  = formatDate(getRequest('date_from')); $to_date = formatDate(getRequest('date_to'));
    $csql= "SELECT AVG(s.unit_price) AS OutRate  FROM ".STOCK_LEDGER_TBL." as s, ".PRODUCT_TBL." as p  WHERE s.`product_id`=p.product_id AND p.catagory = '$catagory' AND s.cr >0 AND s.project_id='$project_id'";
	if($product_type!=""){$csql.=" AND p.product_type='$product_type'";}
	if($stock_id!=""){$csql.=" AND s.store_id='$stock_id'";}		
	if($from_date!="" && $to_date ==""){
		$csql.=" AND s.create_date >= '$from_date'";
	}elseif($from_date=="" && $to_date !=""){
		$csql.=" AND s.create_date <= '$to_date'";
	}elseif($from_date!="" && $to_date !=""){
		$csql.=" AND s.create_date BETWEEN '$from_date' AND '$to_date'";
	}
	$csql.=" group by p.catagory";
	$cres = mysql_query($csql);
	$crow = mysql_fetch_object($cres);
	$OutRate= $crow->OutRate;
	if(intval($OutRate)==""){$OutRate=0;}
	return $OutRate;
}
function getAvgCatagoryClosingRate($catagory,$project_id){	
	$product_type = getRequest('product_type'); 
	$stock_id 	  = getRequest('store_id');   
	$closing_rate=0;
	$from_date 	 = formatDate(getRequest('date_from')); $to_date = formatDate(getRequest('date_to'));
    $csql= "SELECT AVG(s.unit_price) AS closing_rate  FROM ".STOCK_LEDGER_TBL." as s, ".PRODUCT_TBL." as p  WHERE s.`product_id`=p.product_id AND p.catagory = '$catagory' AND s.dr >0 AND s.project_id='$project_id'";
	if($product_type!=""){$csql.=" AND p.product_type='$product_type'";}
	if($stock_id!=""){$csql.=" AND s.store_id='$stock_id'";}		
	if($to_date !=""){
		$csql.=" AND s.create_date <= '$to_date'";
	}
	$csql.=" group by p.catagory"; 
	$cres = mysql_query($csql);
	$crow = mysql_fetch_object($cres);
	$closing_rate= $crow->closing_rate;
	if(intval($closing_rate)==""){$closing_rate=0;}
	return $closing_rate;
}
function getAvgProductOBRate($product_id,$project_id){
	$unit_price=0; 
	$stock_id 	 = getRequest('store_id');  
	$from_date 	 = formatDate(getRequest('date_from')); $to_date = formatDate(getRequest('date_to'));
    if($from_date==""){   
		$Psql= "SELECT unit_price as closing_rate FROM ".PRODUCT_TBL." WHERE product_id='$product_id' AND project_id='$project_id' ";
		$pres = mysql_query($Psql);
		$prow = mysql_fetch_object($pres);
		$unit_price   = $prow->closing_rate;
	}else{
		$Psql= "SELECT AVG(unit_price) AS unit_price FROM ".STOCK_LEDGER_TBL." WHERE dr >0 AND product_id='$product_id' AND project_id='$project_id' ";
		if($stock_id!=""){$Psql.=" AND store_id='$stock_id'";}
		if($from_date!=""){
			$Psql.=" AND create_date < '$from_date'";
		}
		$Psql.=" GROUP BY product_id ";	
		$pres = mysql_query($Psql);
		$prow = mysql_fetch_object($pres);
		$unit_price = $prow->unit_price;
	}
	if(intval($unit_price)==""){$unit_price=0;}
	return $unit_price;
}
function getCurrentStockInRate($stock_id,$project_id,$product_id=NULL){	 
   $unit_price   = 0;
   $catagory	 = getRequest('catagory');
   $subcatagory	 = getRequest('subcatagory');
   $product_type = getRequest('product_type'); 
   $from_date 	 = formatDate(getRequest('date_from')); 
   $to_date 	 = formatDate(getRequest('date_to'));
   
    if($product_id!=""){   
	$Psql= "SELECT AVG(p.unit_price) AS unit_price FROM ".STORE_STOCK_VIEW." as s,".PRODUCT_TBL." as p WHERE s.product_id=p.product_id AND s.balance >0 AND s.store_id='$stock_id' AND s.project_id = p.project_id AND s.project_id='$project_id' AND p.product_id='$product_id' ";
	if($catagory !=""){	$Psql.=" AND p.catagory='$catagory'";}
	if($subcatagory !=""){	$Psql.=" AND p.subcatagory='$subcatagory'";}
	if($product_type!=""){  $Psql.=" AND p.product_type ='$product_type'";}
	if($from_date!="" && $to_date ==""){
		$Psql.=" AND s.create_date >= '$from_date'";
	}elseif($from_date=="" && $to_date !=""){
		$Psql.=" AND s.create_date <= '$to_date'";
	}elseif($from_date!="" && $to_date !=""){
		$Psql.=" AND s.create_date BETWEEN '$from_date' AND '$to_date'"; 
	}
	
	$Psql.=" GROUP BY p.product_id ";	
	$pres = mysql_query($Psql);
	$prow = mysql_fetch_object($pres);
	$unit_price   = $prow->unit_price;
	if(intval($unit_price)==""){$unit_price=0;}
	return $unit_price;
    }else{
	$catagory	= getRequest('catagory');		
	$product_type	= getRequest('product_type'); 
	
	$Psql= "SELECT p.unit_price,(SUM(s.dr)- SUM(s.cr)) AS balance_qty,((SUM(s.dr)- SUM(s.cr))*p.unit_price) AS balance_amount FROM ".STOCK_LEDGER_TBL." as s, ".PRODUCT_TBL." as p WHERE s.product_id = p.product_id AND s.store_id='$stock_id' AND s.project_id='$project_id' ";			
	if($catagory!=""){$Psql.=" AND p.catagory ='$catagory'";}
	if($subcatagory!=""){$Psql.=" AND p.subcatagory ='$subcatagory'";}
	if($product_type!=""){$Psql.=" AND p.product_type ='$product_type'";}
	if($product!=""){$Psql.=" AND s.product_id='$product'";}		

	if($from_date!="" && $to_date ==""){
		$Psql.=" AND s.create_date >= '$from_date'";
	}elseif($from_date=="" && $to_date !=""){
		$Psql.=" AND s.create_date <= '$to_date'";
	}elseif($from_date!="" && $to_date !=""){
		$Psql.=" AND s.create_date <= '$to_date'";
	}
	$Psql.=" GROUP BY p.product_id "; //echo $Psql;
	$balanceAmount =0;
	$pres = mysql_query($Psql);
	while($prow = mysql_fetch_object($pres)){
	$balanceAmount += $prow->balance_amount;//($prow->unit_price * $prow->balance_qty);
	}		
	if(intval($balanceAmount)==""){$balanceAmount=0;}
	return $balanceAmount;
    }
	
}
function getCurrentStockOpening($stock_id,$project_id,$product_id=NULL){	 
	$unit_price   = 0;
	$catagory	 = getRequest('catagory');
	$subcatagory	 = getRequest('subcatagory');
	$product_type = getRequest('product_type'); 
	$from_date 	 = formatDate(getRequest('date_from')); 

	$catagory	= getRequest('catagory');		
	$product_type	= getRequest('product_type'); 

	$Psql= "SELECT p.unit_price,(SUM(s.dr)- SUM(s.cr)) AS balance_qty,((SUM(s.dr)- SUM(s.cr))*p.unit_price) AS balance_amount FROM ".STOCK_LEDGER_TBL." as s, ".PRODUCT_TBL." as p WHERE s.product_id = p.product_id AND s.store_id='$stock_id' AND s.project_id='$project_id' ";			
	if($catagory!=""){$Psql.=" AND p.catagory ='$catagory'";}
	if($subcatagory!=""){$Psql.=" AND p.subcatagory ='$subcatagory'";}
	if($product_type!=""){$Psql.=" AND p.product_type ='$product_type'";}
	if($product!=""){$Psql.=" AND s.product_id='$product'";}
	$Psql.=" AND s.create_date < '$from_date'";
	$Psql.=" GROUP BY p.product_id "; //echo $Psql;
	$balanceAmount =0;
	$pres = mysql_query($Psql);
	while($prow = mysql_fetch_object($pres)){
	$balanceAmount += $prow->balance_amount;//($prow->unit_price * $prow->balance_qty);
	}		
	if(intval($balanceAmount)==""){$balanceAmount=0;}
	return $balanceAmount;
	
}
function getAvgProductInRate($product_id,$project_id){	 
	$unit_price=0; $from_date = formatDate(getRequest('date_from')); $to_date = formatDate(getRequest('date_to'));
	$stock_id 	  = getRequest('store_id');  
    if($from_date==""){   
		$Psql= "SELECT AVG(unit_price) AS unit_price FROM ".STOCK_LEDGER_TBL." WHERE dr >0 AND product_id='$product_id' AND project_id='$project_id' ";
		if($stock_id!=""){$Psql.=" AND store_id='$stock_id'";}	
		$Psql.=" GROUP BY product_id ";	
		$pres = mysql_query($Psql);
		$prow = mysql_fetch_object($pres);
		$unit_price   = $prow->unit_price;
	}else{
		$Psql= "SELECT AVG(unit_price) AS unit_price FROM ".STOCK_LEDGER_TBL." WHERE dr >0 AND product_id='$product_id' AND project_id='$project_id' ";
		if($stock_id!=""){$Psql.=" AND store_id='$stock_id'";}
		if($from_date!="" && $to_date ==""){
			$Psql.=" AND create_date >= '$from_date'";
		}elseif($from_date=="" && $to_date !=""){
			$Psql.=" AND create_date <= '$to_date'";
		}elseif($from_date!="" && $to_date !=""){
			$Psql.=" AND create_date BETWEEN '$from_date' AND '$to_date'";
		}
		$Psql.=" GROUP BY product_id ";	
		$pres = mysql_query($Psql);
		$prow = mysql_fetch_object($pres);
		$unit_price = $prow->unit_price;
	}
	if(intval($unit_price)==""){$unit_price=0;}
	return $unit_price;
}
function getAvgProductOutRate($product_id,$project_id){	 
	$unit_price=0; $from_date = formatDate(getRequest('date_from')); $to_date = formatDate(getRequest('date_to'));
	$stock_id = getRequest('store_id');  
    if($from_date==""){   
		$Psql= "SELECT AVG(unit_price) AS unit_price FROM ".STOCK_LEDGER_TBL." WHERE cr >0 AND product_id='$product_id' AND project_id='$project_id' ";
		if($stock_id!=""){$Psql.=" AND store_id='$stock_id'";}	
		$Psql.=" GROUP BY product_id ";	
		$pres = mysql_query($Psql);
		$prow = mysql_fetch_object($pres);
		$unit_price   = $prow->unit_price;
	}else{
		$Psql= "SELECT AVG(unit_price) AS unit_price FROM ".STOCK_LEDGER_TBL." WHERE cr >0 AND product_id='$product_id' AND project_id='$project_id' ";	
		if($stock_id!=""){$Psql.=" AND store_id='$stock_id'";}	
		if($from_date!="" && $to_date ==""){
			$Psql.=" AND create_date >= '$from_date'";
		}elseif($from_date=="" && $to_date !=""){
			$Psql.=" AND create_date <= '$to_date'";
		}elseif($from_date!="" && $to_date !=""){
			$Psql.=" AND create_date BETWEEN '$from_date' AND '$to_date'";
		}
		$Psql.=" GROUP BY product_id ";	
		$pres = mysql_query($Psql);
		$prow = mysql_fetch_object($pres);
		$unit_price = $prow->unit_price;
	}
	if(intval($unit_price)==""){$unit_price=0;}
	return $unit_price;
}
function getTotalCatagoryInStockQty($catagory,$project_id){	
	$product_type= getRequest('product_type'); 
	$stock_id 	 = getRequest('store_id');  
	$from_date 	 = formatDate(getRequest('date_from'));
	$to_date 	 = formatDate(getRequest('date_to'));
	$instock=0;
    if($from_date==""){   
		$Psql="SELECT SUM(op_qty) as op_qty,SUM(instock) as instock FROM ".STOCK_STATUS_BY_DATE_VIEW." WHERE catagory='$catagory' AND project_id='$project_id' ";
		if($product_type!=""){$Psql.=" AND product_type='$product_type'";}
		if($stock_id!=""){$Psql.=" AND store_id='$stock_id'";}
		$Psql.=" GROUP BY catagory";	
		$pres 	= mysql_query($Psql);
		$prow 	= mysql_fetch_object($pres);
		$instock   = ($prow->instock-$prow->op_qty);	
	}else{		
		$Psql= "SELECT SUM(s.dr) AS in_qty FROM ".STOCK_LEDGER_TBL." as s, ".PRODUCT_TBL." as p  WHERE s.`product_id`=p.product_id AND p.catagory = '$catagory' AND s.dr >0 AND s.project_id='$project_id'";
		if($product_type!=""){$Psql.=" AND p.product_type='$product_type'";}
		if($stock_id!=""){$Psql.=" AND s.store_id='$stock_id'";}
		if($from_date!="" && $to_date ==""){
			$Psql.=" AND s.create_date >= '$from_date'";
		}elseif($from_date=="" && $to_date !=""){
			$Psql.=" AND s.create_date <= '$to_date'";
		}elseif($from_date!="" && $to_date !=""){
			$Psql.=" AND s.create_date BETWEEN '$from_date' AND '$to_date'";
		}
		$Psql.=" group by p.catagory";		
		$pres = mysql_query($Psql);
		$prow = mysql_fetch_object($pres);
		$instock = $prow->in_qty;		
	}
	if(intval($instock)==""){$instock=0;}
	return $instock;
}
function getTotalCatagoryOutStockQty($catagory,$project_id){	 
	$product_type= getRequest('product_type'); $stock_id = getRequest('store_id');  $outstock=0;
	$from_date 	 = formatDate(getRequest('date_from')); $to_date = formatDate(getRequest('date_to'));	
    if($from_date==""){   
		$Psql = "SELECT SUM(outstock) as outstock FROM ".STOCK_STATUS_BY_DATE_VIEW." WHERE catagory='$catagory' AND project_id='$project_id' ";
		if($product_type!=""){$Psql.=" AND product_type='$product_type'";}
		if($stock_id!=""){$Psql.=" AND store_id='$stock_id'";}
		$Psql.=" GROUP BY catagory ";	  
		$pres 		= mysql_query($Psql);
		$prow 		= mysql_fetch_object($pres);
		$outstock 	= $prow->outstock;
	}else{
		$Psql="SELECT SUM(s.cr) AS outstock FROM ".STOCK_LEDGER_TBL." as s, ".PRODUCT_TBL." as p  WHERE s.`product_id`=p.product_id 
		AND p.catagory = '$catagory' AND s.cr >0 AND s.project_id='$project_id'";
		if($product_type!=""){$Psql.=" AND p.product_type='$product_type'";}
		if($stock_id!=""){$Psql.=" AND s.store_id='$stock_id'";}
		if($from_date!="" && $to_date ==""){
			$Psql.=" AND s.create_date >= '$from_date'";
		}elseif($from_date=="" && $to_date !=""){
			$Psql.=" AND s.create_date <= '$to_date'";
		}elseif($from_date!="" && $to_date !=""){
			$Psql.=" AND s.create_date BETWEEN '$from_date' AND '$to_date'";
		}
		$Psql.=" group by p.catagory";		
		$pres = mysql_query($Psql);
		$prow = mysql_fetch_object($pres);
		$outstock = $prow->outstock;
	}
	if(intval($outstock)==""){$outstock=0;}
	return $outstock;
}
//======== Start Stock Qty ===========
function getTotalStockQty($type,$project_id){	
	$product_type = getRequest('product_type'); $stock_id 	  = getRequest('store_id');  $total_balance=0; 
	$from_date 	  = formatDate(getRequest('date_from')); $to_date = formatDate(getRequest('date_to')); 	
    if($type=="OB"){
		$Psql= "SELECT (SUM(s.dr)- SUM(s.cr)) AS total_balance FROM ".STOCK_LEDGER_TBL." as s, ".PRODUCT_TBL." as p  WHERE 
		s.`product_id`=p.product_id AND s.project_id='$project_id'";
		if($product_type!=""){$Psql.=" AND p.product_type='$product_type'";}
		if($stock_id!=""){$Psql.=" AND s.store_id='$stock_id'";}		
		if($from_date!=""){
			$Psql.=" AND s.create_date < '$from_date'";
		}
	}elseif($type=="InStock"){
		$Psql= "SELECT SUM(s.dr) AS total_balance FROM ".STOCK_LEDGER_TBL." as s, ".PRODUCT_TBL." as p  WHERE s.project_id='$project_id' 
		AND s.`product_id`=p.product_id AND s.dr >0 ";
		if($product_type!=""){$Psql.=" AND p.product_type='$product_type'";}
		if($stock_id!=""){$Psql.=" AND s.store_id='$stock_id'";}
		if($from_date!="" && $to_date ==""){
			$Psql.=" AND s.create_date >= '$from_date'";
		}elseif($from_date=="" && $to_date !=""){
			$Psql.=" AND s.create_date <= '$to_date'";
		}elseif($from_date!="" && $to_date !=""){
			$Psql.=" AND s.create_date BETWEEN '$from_date' AND '$to_date'";
		}
	}elseif($type=="OutStock"){
		$Psql="SELECT SUM(s.cr) AS total_balance FROM ".STOCK_LEDGER_TBL." as s, ".PRODUCT_TBL." as p  WHERE s.project_id='$project_id' 
		AND s.`product_id`=p.product_id AND s.cr >0 ";
		if($product_type!=""){$Psql.=" AND p.product_type='$product_type'";}
		if($stock_id!=""){$Psql.=" AND s.store_id='$stock_id'";}
		if($from_date!="" && $to_date ==""){
			$Psql.=" AND s.create_date >= '$from_date'";
		}elseif($from_date=="" && $to_date !=""){
			$Psql.=" AND s.create_date <= '$to_date'";
		}elseif($from_date!="" && $to_date !=""){
			$Psql.=" AND s.create_date BETWEEN '$from_date' AND '$to_date'";
		}
	}
	$Psql.=" group by s.store_id";	
	$cres = mysql_query($Psql);		
	$crow = mysql_fetch_object($cres);
	$total_balance = $crow->total_balance;
	if(intval($total_balance)==""){$total_balance=0;}
	return $total_balance;
}
function getAvgStockInRate($project_id){	 
	$unit_price=0; $from_date = formatDate(getRequest('date_from')); $to_date = formatDate(getRequest('date_to'));
	$stock_id 	  = getRequest('store_id'); 
	$Psql= "SELECT AVG(unit_price) AS unit_price FROM ".STOCK_LEDGER_TBL." WHERE dr >0 AND project_id='$project_id' ";
	if($stock_id!=""){$Psql.=" AND store_id='$stock_id'";}	
	$Psql.=" GROUP BY store_id "; 	
	$pres = mysql_query($Psql);
	$prow = mysql_fetch_object($pres);
	$unit_price   = $prow->unit_price;	
	if(intval($unit_price)==""){$unit_price=0;}
	return $unit_price;
}
function getAvgStockCurrentInRate($project_id){	 
	$unit_price=0; $from_date = formatDate(getRequest('date_from')); $to_date = formatDate(getRequest('date_to'));
	$stock_id 	  = getRequest('store_id'); 
	$product	  = getRequest('product'); 
	
	$Psql="SELECT AVG(p.unit_price) AS unit_price FROM ".STOCK_LEDGER_TBL." as s, ".PRODUCT_TBL." as p  WHERE s.project_id='$project_id' 
		AND s.`product_id`=p.product_id AND s.dr >0 ";
		
	if($stock_id!=""){$Psql.=" AND s.store_id='$stock_id'";}
	if($product!=""){$Psql.=" AND p.product_id='$product'";}		
	$Psql.=" GROUP BY s.store_id ";
	$pres = mysql_query($Psql);
	$prow = mysql_fetch_object($pres);
	$unit_price   = $prow->unit_price;	
	if(intval($unit_price)==""){$unit_price=0;}
	return $unit_price;
}

//======== End Stock Qty ===========

//======= Start Store Movement Topsheet ========

function getProductSalesValue($project_id,$store_id,$product_id){ // now not used topsheet
	$catagory	 = getRequest('catagory');
	$subcatagory	 = getRequest('subcatagory');
	$product_type	 = getRequest('product_type'); 			   
	$from_date 	 = formatDate(getRequest('date_from'));   
	$to_date 	 = formatDate(getRequest('date_to'));
	
	$sales_price=0;
	$SQL="SELECT SUM(total_amount) as sales_price FROM ".SALES_DELIVERY_MASTER_TBL." as sm, ".SALES_DELIVERY_CHALLAN_TBL." as sd, ".PRODUCT_TBL." as p WHERE sm.sales_delivery_master_id = sd.delivery_master_id AND sd.product=p.product_id  AND sd.project_id='$project_id' AND sd.delivery_point='$store_id' ";		
	if($product_id !=""){$SQL.=" AND p.product_id ='$product_id'";}
	if($catagory!=""){$SQL.=" AND p.catagory ='$catagory'";}
	if($subcatagory !=""){	$Psql.=" AND p.subcatagory='$subcatagory'";}
	if($product_type!=""){$SQL.=" AND p.product_type ='$product_type'";}
	
	if($from_date!="" && $to_date ==""){
		$SQL.=" AND sm.delivery_date >= '$from_date'";
	}elseif($from_date=="" && $to_date !=""){
		$SQL.=" AND sm.delivery_date <= '$to_date'";
	}elseif($from_date!="" && $to_date !=""){
		$SQL.=" AND sm.delivery_date BETWEEN '$from_date' AND '$to_date'";
	}
	if($product_id !=""){
		$SQL.="GROUP BY p.product_id";
	}else{
		$SQL.=" GROUP BY sd.delivery_point "; 
	}
	
	$pres = mysql_query($SQL);
	while($prow = mysql_fetch_object($pres)){
		$sales_price = ($prow->sales_price);
	}		
		
	if(intval($sales_price)==""){ $sales_price=0;}
	
	return $sales_price;			 
}
function getStoreProductSalesValue($project_id,$in_type,$store_id,$product_id){
	$catagory	 = getRequest('catagory');
	$subcatagory	 = getRequest('subcatagory');
	$product_type	 = getRequest('product_type'); 			   
	$from_date 	 = formatDate(getRequest('date_from'));   $to_date 	 = formatDate(getRequest('date_to'));
	
	$sales_price=0;
	$Psql= "SELECT s.cr as sales_qty,s.unit_price FROM ".STOCK_LEDGER_TBL." as s, ".PRODUCT_TBL." as p WHERE s.product_id = p.product_id AND s.store_id='$store_id' AND s.project_id='$project_id' AND s.cr >0 ";
	if($product_id!=""){$Psql.=" AND p.product_id ='$product_id'";}	
	if($in_type!=""){$Psql.=" AND s.note ='$in_type'";}	
	if($catagory!=""){$Psql.=" AND p.catagory ='$catagory'";}
	if($subcatagory !=""){	$Psql.=" AND p.subcatagory='$subcatagory'";}
	if($product_type!=""){$Psql.=" AND p.product_type ='$product_type'";}		
	
	if($from_date!="" && $to_date ==""){
		$Psql.=" AND s.create_date >= '$from_date'";
	}elseif($from_date=="" && $to_date !=""){
		$Psql.=" AND s.create_date <= '$to_date'";
	}elseif($from_date!="" && $to_date !=""){
		$Psql.=" AND s.create_date BETWEEN '$from_date' AND '$to_date'";
	} 
	$Psql.="GROUP BY p.product_id";
	$pres = mysql_query($Psql);
	while($prow = mysql_fetch_object($pres)){
		$sales_price += ($prow->sales_qty * $prow->unit_price);
	}		
		
	if(intval($sales_price)==""){ $sales_price=0;}
	
	return $sales_price;			 
}
function getStoreProductDeliverySalesValue($project_id,$store_id,$product_id){
	$catagory	 = getRequest('catagory');
	$subcatagory	 = getRequest('subcatagory');
	$product_type	 = getRequest('product_type'); 			   
	$from_date 	 = formatDate(getRequest('date_from'));   
	$to_date 	 = formatDate(getRequest('date_to'));
	
	$total_amount	= 0; 
	if($product_id !=""){
	$Psql= "SELECT SUM(pd.total_amount) AS total_amount FROM ".DELIVERY_SALES_STATUS_VIEW."  as pd WHERE pd.project_id = '$project_id' ";
	}else{
	$Psql= "SELECT SUM(pd.total_amount) AS total_amount FROM ".DELIVERY_SALES_STATUS_VIEW."  as pd WHERE pd.project_id = '$project_id' ";	
	}
	if($product_id !=""){	$Psql.=" AND pd.product='$product_id'";}
	if($store_id !=""){	$Psql.=" AND pd.store_id='$store_id'";}
	if($catagory !=""){	$Psql.=" AND pd.catagory='$catagory'";}
	if($subcatagory !=""){	$Psql.=" AND pd.subcatagory='$subcatagory'";}
	if($product_type!=""){  $Psql.=" AND pd.product_type ='$product_type'";}
		
	if($from_date!="" && $to_date !=""){
		$Psql.=" AND pd.delivery_date BETWEEN '$from_date' AND '$to_date'";
	}
	if($store_id !="" && $product_id !=""){
		$Psql.=" GROUP BY pd.product"; 
	}elseif($store_id !="" && $product_id ==""){
		$Psql.=" GROUP BY pd.store_id"; 
	} //echo $Psql;
	$pres = mysql_query($Psql);
	$prow = mysql_fetch_object($pres);
	$total_amount   = $prow->total_amount;	
	if(intval($total_amount)==""){$total_amount=0;}		
	return $total_amount;			 
}
function getTotalStoreProductOBQty($project_id,$stock_id){
	$catagory	= getRequest('catagory');
	$subcatagory	= getRequest('subcatagory');	
	$product_type	= getRequest('product_type'); $op_qty=0; 	
	$product	= getRequest('product'); 
	$from_date 	= formatDate(getRequest('date_from'));   
	$to_date 	 = formatDate(getRequest('date_to'));
    if($from_date==""){   
		$Psql="SELECT SUM(s.op_qty) as op_qty FROM ".STOCK_STATUS_BY_DATE_VIEW." as s ,".PRODUCT_TBL." as p WHERE s.product_id = p.product_id AND s.store_id='$stock_id' AND s.project_id='$project_id' ";
		if($catagory!=""){$Psql.=" AND p.catagory ='$catagory'";}
		if($subcatagory!=""){$Psql.=" AND p.subcatagory ='$subcatagory'";}
		if($product_type!=""){$Psql.=" AND p.product_type='$product_type'";}
		if($product!=""){$Psql.=" AND s.product_id='$product'";}
		$Psql.=" GROUP BY s.store_id ";
		$pres 	= mysql_query($Psql);
		$prow 	= mysql_fetch_object($pres);
		$op_qty = $prow->op_qty;	
	}else{		
		$Psql= "SELECT (SUM(s.dr)- SUM(s.cr)) AS op_qty FROM ".STOCK_LEDGER_TBL." as s, ".PRODUCT_TBL." as p WHERE s.product_id = p.product_id AND s.store_id='$stock_id' AND s.project_id='$project_id' ";			
		if($catagory!=""){$Psql.=" AND p.catagory ='$catagory'";}
		if($subcatagory!=""){$Psql.=" AND p.subcatagory ='$subcatagory'";}
		if($product_type!=""){$Psql.=" AND p.product_type ='$product_type'";}
		if($product!=""){$Psql.=" AND s.product_id='$product'";}
		if($from_date!=""){
			$Psql.=" AND s.create_date < '$from_date'";		
		}
		$Psql.=" GROUP BY s.store_id ";	
		$pres = mysql_query($Psql); 
		$prow = mysql_fetch_object($pres);
		$op_qty = $prow->op_qty;		
	}
	if(intval($op_qty)==""){$op_qty=0;}
	return $op_qty;
}

function getTotalStoreProductInQty($project_id,$in_type,$stock_id){	
	$catagory	= getRequest('catagory');
	$subcatagory	= getRequest('subcatagory');
	$product_type	= getRequest('product_type'); 		
	$product	= getRequest('product'); 		 
	$from_date 	= formatDate(getRequest('date_from')); $to_date 	 = formatDate(getRequest('date_to'));
	$instock=0; 
	if($stock_id =="" && $stockid !=""){ $stock_id = $stockid;}   	
	$Psql= "SELECT SUM(s.dr) AS instock FROM ".STOCK_LEDGER_TBL." as s, ".PRODUCT_TBL." as p WHERE s.product_id = p.product_id AND s.store_id='$stock_id' AND s.project_id='$project_id' AND s.dr >0 ";
	
	if($in_type!=""){$Psql.=" AND s.note ='$in_type'";}	
	if($catagory!=""){$Psql.=" AND p.catagory ='$catagory'";}
	if($subcatagory!=""){$Psql.=" AND p.subcatagory ='$subcatagory'";}
	if($product_type!=""){$Psql.=" AND p.product_type ='$product_type'";}
	if($product!=""){$Psql.=" AND p.product_id ='$product'";}		
	
	if($from_date!="" && $to_date ==""){
		$Psql.=" AND s.create_date >= '$from_date'";
	}elseif($from_date=="" && $to_date !=""){
		$Psql.=" AND s.create_date <= '$to_date'";
	}elseif($from_date!="" && $to_date !=""){
		$Psql.=" AND s.create_date BETWEEN '$from_date' AND '$to_date'";
	} 
	
	$Psql.=" GROUP BY s.store_id "; //echo $Psql;
	$pres = mysql_query($Psql);
	$prow = mysql_fetch_object($pres);
	$instock = $prow->instock;		
	if(intval($instock)==""){ $instock=0;}
	return $instock;
}
function getTotalStoreProductOutQty($project_id,$in_type,$stock_id){
	$catagory	= getRequest('catagory');
	$subcatagory	= getRequest('subcatagory');
	$product_type	= getRequest('product_type');
	$product 	= getRequest('product'); 				  
	$from_date 	= formatDate(getRequest('date_from')); $to_date 	 = formatDate(getRequest('date_to'));
	$instock=0;    	
	$Psql= "SELECT SUM(s.cr) AS instock FROM ".STOCK_LEDGER_TBL." as s, ".PRODUCT_TBL." as p WHERE s.product_id = p.product_id AND s.store_id='$stock_id' AND s.project_id='$project_id' AND s.cr >0 ";
	
	if($in_type!=""){$Psql.=" AND s.note ='$in_type'";}	
	if($catagory!=""){$Psql.=" AND p.catagory ='$catagory'";}
	if($subcatagory!=""){$Psql.=" AND p.subcatagory ='$subcatagory'";}
	if($product_type!=""){$Psql.=" AND p.product_type ='$product_type'";}	
	if($product!=""){$Psql.=" AND p.product_id ='$product'";}		
	
	if($from_date!="" && $to_date ==""){
		$Psql.=" AND s.create_date >= '$from_date'";
	}elseif($from_date=="" && $to_date !=""){
		$Psql.=" AND s.create_date <= '$to_date'";
	}elseif($from_date!="" && $to_date !=""){
		$Psql.=" AND s.create_date BETWEEN '$from_date' AND '$to_date'";
	} 
	
	$Psql.=" GROUP BY s.store_id "; //echo $Psql;
	$pres = mysql_query($Psql);
	$prow = mysql_fetch_object($pres);
	$instock = $prow->instock;		
	if(intval($instock)==""){ $instock=0;}
	return $instock;
}
function getStoreProductSalesQty($project_id,$store_id,$product_id){ // now not used in topsheet
	$catagory	= getRequest('catagory');
	$subcatagory	= getRequest('subcatagory');
	$product_type	= getRequest('product_type'); 			   
	$from_date 	= formatDate(getRequest('date_from'));   
	$to_date 	= formatDate(getRequest('date_to'));
	
	$delivery_sales_qty=0;
	$SQL="SELECT SUM(delivery_qty) as delivery_sales_qty FROM ".SALES_DELIVERY_MASTER_TBL." as sm, ".SALES_DELIVERY_CHALLAN_TBL." as sd, ".PRODUCT_TBL." as p WHERE sm.sales_delivery_master_id = sd.delivery_master_id AND sd.product=p.product_id  AND sd.project_id='$project_id' AND sd.delivery_point='$store_id' ";		
	if($product_id !=""){$SQL.=" AND p.product_id ='$product_id'";}
	if($catagory!=""){$SQL.=" AND p.catagory ='$catagory'";}
	if($subcatagory!=""){$Psql.=" AND p.subcatagory ='$subcatagory'";}
	if($product_type!=""){$SQL.=" AND p.product_type ='$product_type'";}
	
	if($from_date!="" && $to_date ==""){
		$SQL.=" AND sm.delivery_date >= '$from_date'";
	}elseif($from_date=="" && $to_date !=""){
		$SQL.=" AND sm.delivery_date <= '$to_date'";
	}elseif($from_date!="" && $to_date !=""){
		$SQL.=" AND sm.delivery_date BETWEEN '$from_date' AND '$to_date'";
	}
	if($product_id !=""){
		$SQL.="GROUP BY p.product_id";
	}else{
		$SQL.=" GROUP BY sd.delivery_point "; 
	}
	
	$pres = mysql_query($SQL);
	while($prow = mysql_fetch_object($pres)){
		$delivery_sales_qty = ($prow->delivery_sales_qty);
	}		
		
	if(intval($delivery_sales_qty)==""){ $delivery_sales_qty=0;}
	
	return $delivery_sales_qty;			 
}
//====== End Store Movement Topsheet ===========

function getAVGProductPrice($product_id,$project_id,$in_type,$stockid=0){
	$product_type= getRequest('product_type'); 			  $stock_id  = getRequest('store_id');  
	$from_date 	 = formatDate(getRequest('date_from'));   $to_date 	 = formatDate(getRequest('date_to'));
	if($stock_id =="" && $stockid !=""){ $stock_id = $stockid;} 
	$product_price=0;
	$SQL="SELECT AVG(`unit_price`) as product_price FROM ".STOCK_LEDGER_TBL." WHERE `product_id` = '$product_id' ";
	if($stock_id!=""){$Psql.=" AND store_id='$stock_id'";}		
	if($in_type!=""){$Psql.=" AND note ='$in_type'";}	
	
	if($from_date!="" && $to_date ==""){
		$SQL.=" AND create_date >= '$from_date'";
	}elseif($from_date=="" && $to_date !=""){
		$SQL.=" AND create_date <= '$to_date'";
	}elseif($from_date!="" && $to_date !=""){
		$SQL.=" AND create_date BETWEEN '$from_date' AND '$to_date'";
	}
	$pres = mysql_query($SQL); 
	$prow = mysql_fetch_object($pres);
	$product_price = $prow->product_price;
	if(intval($product_price)==""){$product_price=0;}
	return $product_price;			 
}
function getTotalProductOBQty($product_id,$project_id,$stockid=0){	
	$product_type= getRequest('product_type'); $op_qty=0; $stock_id  = getRequest('store_id');  
	$from_date 	 = formatDate(getRequest('date_from'));   $to_date 	 = formatDate(getRequest('date_to'));
	if($stock_id =="" && $stockid !=""){ $stock_id = $stockid;} 
    if($from_date==""){   
		$Psql="SELECT SUM(op_qty) as op_qty FROM ".STOCK_STATUS_BY_DATE_VIEW." WHERE product_id='$product_id' AND project_id='$project_id' ";
		if($product_type!=""){$Psql.=" AND product_type='$product_type'";}
		if($stock_id!=""){$Psql.=" AND store_id='$stock_id'";}
		$Psql.=" GROUP BY product_id ";
		$pres 	= mysql_query($Psql);
		$prow 	= mysql_fetch_object($pres);
		$op_qty = $prow->op_qty;	
	}else{		
		$Psql= "SELECT (SUM(dr)- SUM(cr)) AS op_qty FROM ".STOCK_LEDGER_TBL." WHERE product_id='$product_id' AND project_id='$project_id' ";
		if($stock_id!=""){$Psql.=" AND store_id='$stock_id'";}	
		if($from_date!=""){
			$Psql.=" AND create_date < '$from_date'";		
		}
		$Psql.=" GROUP BY product_id ";	
		$pres = mysql_query($Psql); 
		$prow = mysql_fetch_object($pres);
		$op_qty = $prow->op_qty;		
	}
	if(intval($op_qty)==""){$op_qty=0;}
	return $op_qty;
}

function getTotalProductInQty($product_id,$project_id,$in_type,$stockid=0){	
	$product_type= getRequest('product_type'); 			$stock_id 	 = getRequest('store_id');  
	$from_date 	 = formatDate(getRequest('date_from')); $to_date 	 = formatDate(getRequest('date_to'));
	$instock=0; 
	if($stock_id =="" && $stockid !=""){ $stock_id = $stockid;}   	
	$Psql= "SELECT SUM(dr) AS instock FROM ".STOCK_LEDGER_TBL." WHERE product_id='$product_id' AND project_id='$project_id' AND dr >0 ";
	if($stock_id!=""){$Psql.=" AND store_id='$stock_id'";}	
	if($in_type!=""){$Psql.=" AND note ='$in_type'";}	
	if($from_date!="" && $to_date ==""){
		$Psql.=" AND create_date >= '$from_date'";
	}elseif($from_date=="" && $to_date !=""){
		$Psql.=" AND create_date <= '$to_date'";
	}elseif($from_date!="" && $to_date !=""){
		$Psql.=" AND create_date BETWEEN '$from_date' AND '$to_date'";
	} 
	$Psql.=" GROUP BY product_id ";
	$pres = mysql_query($Psql);
	$prow = mysql_fetch_object($pres);
	$instock = $prow->instock;		
	if(intval($instock)==""){ $instock=0;}
	return $instock;
}

function getTotalProductOutQty($product_id,$project_id,$in_type,$stockid=0){	
	$product_type= getRequest('product_type'); 	$stock_id = getRequest('store_id');  
	$from_date 	 = formatDate(getRequest('date_from')); $to_date 	 = formatDate(getRequest('date_to'));
	$instock=0; if($stock_id =="" && $stockid !=""){ $stock_id = $stockid;}    	
	$Psql= "SELECT SUM(cr) AS instock FROM ".STOCK_LEDGER_TBL." WHERE product_id='$product_id' AND project_id='$project_id' AND cr >0 ";
	if($stock_id!=""){$Psql.=" AND store_id='$stock_id'";}	
	if($in_type!=""){$Psql.=" AND note ='$in_type'";}	
	if($from_date!="" && $to_date ==""){
		$Psql.=" AND create_date >= '$from_date'";
	}elseif($from_date=="" && $to_date !=""){
		$Psql.=" AND create_date <= '$to_date'";
	}elseif($from_date!="" && $to_date !=""){
		$Psql.=" AND create_date BETWEEN '$from_date' AND '$to_date'";
	} //echo $Psql;
	$Psql.=" GROUP BY product_id ";
	$pres = mysql_query($Psql);
	$prow = mysql_fetch_object($pres);
	$instock = $prow->instock;		
	if(intval($instock)==""){ $instock=0;}
	return $instock;
}

function getProductSalesQty($project_id,$store_id,$product_id){
	$catagory	 = getRequest('catagory');
	$product_type= getRequest('product_type'); 			   
	$from_date 	 = formatDate(getRequest('date_from'));   $to_date 	 = formatDate(getRequest('date_to'));
	
	$delivery_sales_qty=0;
	$SQL="SELECT delivery_qty as delivery_sales_qty FROM ".SALES_DELIVERY_MASTER_TBL." as sm, ".SALES_DELIVERY_CHALLAN_TBL." as sd, ".PRODUCT_TBL." as p WHERE sm.sales_delivery_master_id = sd.delivery_master_id AND sd.product=p.product_id  AND sd.project_id='$project_id' AND sd.delivery_point='$store_id' ";		
	if($product_id !=""){$SQL.=" AND p.product_id ='$product_id'";}
	if($catagory!=""){$SQL.=" AND p.catagory ='$catagory'";}
	if($product_type!=""){$SQL.=" AND p.product_type ='$product_type'";}
	
	if($from_date!="" && $to_date ==""){
		$SQL.=" AND sm.delivery_date >= '$from_date'";
	}elseif($from_date=="" && $to_date !=""){
		$SQL.=" AND sm.delivery_date <= '$to_date'";
	}elseif($from_date!="" && $to_date !=""){
		$SQL.=" AND sm.delivery_date BETWEEN '$from_date' AND '$to_date'";
	}
	
	$SQL.="GROUP BY p.product_id";
		
	$pres = mysql_query($SQL);
	while($prow = mysql_fetch_object($pres)){
		$delivery_sales_qty = ($prow->delivery_sales_qty);
	}		
		
	if(intval($delivery_sales_qty)==""){ $delivery_sales_qty=0;}
	
	return $delivery_sales_qty;			 
} 

function getTotalProductInStockQty($product_id,$project_id){	
	$product_type= getRequest('product_type'); 			$stock_id 	 = getRequest('store_id');  
	$from_date 	 = formatDate(getRequest('date_from')); $to_date 	 = formatDate(getRequest('date_to'));
	$instock=0;
    if($from_date==""){   
		$Psql="SELECT SUM(op_qty) as op_qty,SUM(instock) as instock FROM ".STOCK_STATUS_BY_DATE_VIEW." WHERE product_id='$product_id' AND project_id='$project_id' ";
		if($product_type!=""){$Psql.=" AND product_type='$product_type'";}
		if($stock_id!=""){$Psql.=" AND store_id='$stock_id'";}
		$Psql.=" GROUP BY product_id ";	
		$pres 	= mysql_query($Psql);
		$prow 	= mysql_fetch_object($pres);
		$instock= ($prow->instock-$prow->op_qty);	
	}else{		
		$Psql= "SELECT SUM(dr) AS instock FROM ".STOCK_LEDGER_TBL." WHERE product_id='$product_id' AND project_id='$project_id' ";
		if($stock_id!=""){$Psql.=" AND store_id='$stock_id'";}	
		if($from_date!="" && $to_date ==""){
			$Psql.=" AND create_date >= '$from_date'";
		}elseif($from_date=="" && $to_date !=""){
			$Psql.=" AND create_date <= '$to_date'";
		}elseif($from_date!="" && $to_date !=""){
			$Psql.=" AND create_date BETWEEN '$from_date' AND '$to_date'";
		} //echo $Psql;
		$Psql.=" GROUP BY product_id ";
		$pres = mysql_query($Psql);
		$prow = mysql_fetch_object($pres);
		$instock = $prow->instock;		
	}
	return $instock;
}
function getTotalProductOutStockQty($product_id,$project_id){	 
	$product_type= getRequest('product_type'); 
	$stock_id 	 = getRequest('store_id');  
	$from_date 	 = formatDate(getRequest('date_from'));
	$to_date 	 = formatDate(getRequest('date_to'));
	$outstock=0;
    if($from_date==""){   
		$Psql= "SELECT SUM(cr) AS out_qty FROM ".STOCK_LEDGER_TBL." WHERE product_id='$product_id' AND project_id='$project_id' ";
		if($stock_id!=""){$Psql.=" AND store_id='$stock_id'";}
		$Psql.=" GROUP BY product_id ";
		$pres 		= mysql_query($Psql);
		$prow 		= mysql_fetch_object($pres);
		$outstock 	= $prow->out_qty;
	}else{		
		$Psql= "SELECT SUM(cr) AS out_qty FROM ".STOCK_LEDGER_TBL." WHERE product_id='$product_id' AND project_id='$project_id' ";
		if($stock_id!=""){$Psql.=" AND store_id='$stock_id'";}
		if($from_date!="" && $to_date ==""){
			$Psql.=" AND create_date >= '$from_date'";
		}elseif($from_date=="" && $to_date !=""){
			$Psql.=" AND create_date <= '$to_date'";
		}elseif($from_date!="" && $to_date !=""){
			$Psql.=" AND create_date BETWEEN '$from_date' AND '$to_date'";
		}
		$Psql.=" GROUP BY product_id ";	
		$pres = mysql_query($Psql);
		$prow = mysql_fetch_object($pres);
		$outstock = $prow->out_qty;
		
	}
	return $outstock;
}
//===== Customer Top Sheet =========

//====== All Area Function =========
function getTotalDivisionOB($division_id,$project_id){	 
	$from_date 	= formatDate(getRequest('date_from'));
	$to_date 	= formatDate(getRequest('date_to'));	
	$area 	 	= getRequest('district');
	$trt 	 	= getRequest('area');
	$sales_type = getRequest('sales_type'); 
	$opening_balance=0; $ob_dr=0; $ob_cr=0;
	if(($from_date=="") || ($from_date=="2014/01/01")){
		//======== Total Dr OB ======
		$dsql= "SELECT SUM(opening_balance) AS ob_dr FROM ".CUSTOMER_OB_VIEW." WHERE  project_id='$project_id' AND op_type='Dr'";
		if($area!="" && $trt==""){
			$dsql.=" AND area_id='$area' ";
		}elseif($area!="" && $trt!=""){
			$dsql.=" AND trt_id='$trt' ";
		}elseif($area=="" && $trt==""){
			$dsql.=" AND division_id='$division_id' ";
		}		
		if($sales_type!=""){
			$dsql.=" AND sales_type = '$sales_type'";
		}
		$dres = mysql_query($dsql);
		$dnum = mysql_num_rows($dres);
		if($dnum>0){
		$drow = mysql_fetch_object($dres);
		$ob_dr = $drow->ob_dr;
		}
		//======== Total Cr OB ======
		$csql= "SELECT SUM(opening_balance) AS ob_cr FROM ".CUSTOMER_OB_VIEW." WHERE project_id='$project_id' AND op_type='Cr'";
		
		if($area!="" && $trt==""){
			$csql.=" AND area_id='$area' ";
		}elseif($area!="" && $trt!=""){
			$csql.=" AND trt_id='$trt' ";
		}elseif($area=="" && $trt==""){
			$csql.=" AND division_id='$division_id' ";
		}
		if($sales_type!=""){
			$csql.=" AND sales_type = '$sales_type'";
		}
		$cres = mysql_query($csql);
		$cnum = mysql_num_rows($cres);
		if($cnum>0){
		$crow = mysql_fetch_object($cres);
		$ob_cr = $crow->ob_cr;
		}
		$opening_balance = ($ob_dr-$ob_cr);			
	}else{
		$Psql= "SELECT (SUM(dr) - SUM(cr) )AS customer_ob FROM ".CUSTOMER_LEDGER_VIEW." WHERE project_id='$project_id'";
		if($from_date!=""){
			$Psql.=" AND created_date < '$from_date'";
		}	
		if($area!="" && $trt==""){
			$Psql.=" AND area_id='$area' ";
		}elseif($area!="" && $trt!=""){
			$Psql.=" AND trt_id='$trt' ";
		}elseif($area=="" && $trt==""){
			$Psql.=" AND division_id='$division_id' ";
		}
		if($sales_type!=""){
			$Psql.=" AND sales_type = '$sales_type'";
		}
		$pres = mysql_query($Psql);
		$cnum = mysql_num_rows($pres);
		if($cnum>0){
		$prow = mysql_fetch_object($pres);
		$opening_balance = $prow->customer_ob;
		}
	}
	return $opening_balance; 
}
function getTotalSupplierDivisionOB($division_id,$project_id){	 
	$from_date 	= formatDate(getRequest('date_from'));
	$to_date 	= formatDate(getRequest('date_to'));	
	$area 	 	= getRequest('district');
	$trt 	 	= getRequest('area');
	$sales_type = getRequest('sales_type'); 
	$opening_balance=0; $ob_dr=0; $ob_cr=0;
	if(($from_date=="") || ($from_date=="2014/01/01")){
		//======== Total Dr OB ======
		$dsql= "SELECT SUM(opening_balance) AS ob_dr FROM ".SUPPLIER_OB_VIEW." WHERE  project_id='$project_id' AND op_type='Dr'";
		if($area!="" && $trt==""){
			$dsql.=" AND area_id='$area' ";
		}elseif($area!="" && $trt!=""){
			$dsql.=" AND trt_id='$trt' ";
		}elseif($area=="" && $trt==""){
			$dsql.=" AND division_id='$division_id' ";
		}		
		if($sales_type!=""){
			$dsql.=" AND sales_type = '$sales_type'";
		}
		$dres = mysql_query($dsql);
		$dnum = mysql_num_rows($dres);
		if($dnum>0){
		$drow = mysql_fetch_object($dres);
		$ob_dr = $drow->ob_dr;
		}
		//======== Total Cr OB ======
		$csql= "SELECT SUM(opening_balance) AS ob_cr FROM ".SUPPLIER_OB_VIEW." WHERE project_id='$project_id' AND op_type='Cr'";
		
		if($area!="" && $trt==""){
			$csql.=" AND area_id='$area' ";
		}elseif($area!="" && $trt!=""){
			$csql.=" AND trt_id='$trt' ";
		}elseif($area=="" && $trt==""){
			$csql.=" AND division_id='$division_id' ";
		}
		if($sales_type!=""){
			$csql.=" AND sales_type = '$sales_type'";
		}
		$cres = mysql_query($csql);
		$cnum = mysql_num_rows($cres);
		if($cnum>0){
		$crow = mysql_fetch_object($cres);
		$ob_cr = $crow->ob_cr;
		}
		$opening_balance = ($ob_dr-$ob_cr);			
	}else{
		$Psql= "SELECT (SUM(dr) - SUM(cr) )AS customer_ob FROM ".SUPPLIER_LEDGER_VIEW." WHERE project_id='$project_id'";
		if($from_date!=""){
			$Psql.=" AND created_date < '$from_date'";
		}	
		if($area!="" && $trt==""){
			$Psql.=" AND area_id='$area' ";
		}elseif($area!="" && $trt!=""){
			$Psql.=" AND trt_id='$trt' ";
		}elseif($area=="" && $trt==""){
			$Psql.=" AND division_id='$division_id' ";
		}
		if($sales_type!=""){
			$Psql.=" AND sales_type = '$sales_type'";
		}
		//echo $Psql;
		$pres = mysql_query($Psql);
		$cnum = mysql_num_rows($pres);
		if($cnum>0){
		$prow = mysql_fetch_object($pres);
		$opening_balance = $prow->customer_ob;
		}
	}
	return $opening_balance; 
}
function getTotalDivisionSalesAmount($division_id,$project_id){	 
	$from_date 	= formatDate(getRequest('date_from'));
	$to_date 	= formatDate(getRequest('date_to'));
	$area 	 	= getRequest('district');
	$trt 	 	= getRequest('area');
	$sales_type 	= getRequest('sales_type'); 
	$odd_date 	= getRequest('odd_date'); 	    
	$minus_odd_date = getRequest('minus_odd_date'); 
	if($odd_date !="" || $minus_odd_date!=""){$sales_date="sales_date";}else{$sales_date="delivery_date";} 
	
	$sales_amount=0; 	
	$Psql= "SELECT SUM(sales_amount) AS sales_amount FROM ".CUSTOMER_SALES_LEDGER_VIEW." WHERE project_id='$project_id'";	
	
	if($area!="" && $trt==""){
		$Psql.=" AND area_id='$area' ";
	}elseif($area!="" && $trt!=""){
		$Psql.=" AND trt_id='$trt' ";
	}elseif($area=="" && $trt==""){
		$Psql.=" AND division_id='$division_id' ";
	}		
	if($sales_type!=""){
		$Psql.=" AND sales_type = '$sales_type'";
	}
	if($from_date!="" && $to_date ==""){
		$Psql.=" AND $sales_date >= '$from_date'";
	}elseif($from_date=="" && $to_date !=""){
		$Psql.=" AND $sales_date <= '$to_date'";
	}elseif($from_date!="" && $to_date !=""){
		$Psql.=" AND $sales_date BETWEEN '$from_date' AND '$to_date'";
	}
	if($minus_odd_date!=""){
		$Psql.=" AND sales_date = value_date";
	}

	$pres = mysql_query($Psql); 
	$cnum = mysql_num_rows($pres);
	if($cnum>0){
	$prow = mysql_fetch_object($pres);
	$sales_amount = $prow->sales_amount;
	}
	// SELECT SUM( dr ) AS sales FROM `vw_customer_ledger` WHERE `area_id` =1 AND `description` = "OS" 	
	return $sales_amount; 
}

function getTotalSupplierDivisionSalesAmount($division_id,$project_id){	 
	$from_date 	= formatDate(getRequest('date_from'));
	$to_date 	= formatDate(getRequest('date_to'));
	$area 	 	= getRequest('district');
	$trt 	 	= getRequest('area');
	$sales_type 	= getRequest('sales_type'); 
	$odd_date 	= getRequest('odd_date'); 	    
	$minus_odd_date = getRequest('minus_odd_date'); 
	if($odd_date !="" || $minus_odd_date!=""){$sales_date="sales_date";}else{$sales_date="delivery_date";}
	$sales_amount=0; 	
	$Psql= "SELECT SUM(sales_amount) AS sales_amount FROM ".SUPPLIER_SALES_LEDGER_VIEW." WHERE project_id='$project_id'";	
	
	if($area!="" && $trt==""){
		$Psql.=" AND area_id='$area' ";
	}elseif($area!="" && $trt!=""){
		$Psql.=" AND trt_id='$trt' ";
	}elseif($area=="" && $trt==""){
		$Psql.=" AND division_id='$division_id' ";
	}		
	if($sales_type!=""){
		$Psql.=" AND sales_type = '$sales_type'";
	}
	if($from_date!="" && $to_date ==""){
		$Psql.=" AND $sales_date >= '$from_date'";
	}elseif($from_date=="" && $to_date !=""){
		$Psql.=" AND $sales_date <= '$to_date'";
	}elseif($from_date!="" && $to_date !=""){
		$Psql.=" AND $sales_date BETWEEN '$from_date' AND '$to_date'";
	}
	if($minus_odd_date!=""){
		$Psql.=" AND sales_date = value_date";
	}
	$pres = mysql_query($Psql); 
	$cnum = mysql_num_rows($pres);
	if($cnum>0){
	$prow = mysql_fetch_object($pres);
	$sales_amount = $prow->sales_amount;
	}
	// SELECT SUM( dr ) AS sales FROM `vw_customer_ledger` WHERE `area_id` =1 AND `description` = "OS" 	
	return $sales_amount; 
}
function getTotalDivisionReceiptAmount($division_id,$project_id){	 
	$from_date 	= formatDate(getRequest('date_from'));
	$to_date 	= formatDate(getRequest('date_to'));
	$area 	 	= getRequest('district');
	$trt 	 	= getRequest('area');
	$sales_type 	= getRequest('sales_type');     
	$issue_date 	= getRequest('issue_date');	
	if($issue_date!=""){$created_date="issue_date";}else{$created_date="created_date";}
	$receipt_amount=0; 	$return_amount=0;
	//======= Get Total Receipt Amount =========
	$Psql= "SELECT SUM(cr) AS receipt_amount FROM ".CUSTOMER_LEDGER_VIEW." WHERE project_id='$project_id' AND cr >0 AND description!='OB' AND adjustment =0 AND beddebts =0";
	if($area!="" && $trt==""){
		$Psql.=" AND area_id='$area' ";
	}elseif($area!="" && $trt!=""){
		$Psql.=" AND trt_id='$trt' ";
	}elseif($area=="" && $trt==""){
		$Psql.=" AND division_id='$division_id' ";
	}	
	if($sales_type!=""){
		$Psql.=" AND sales_type = '$sales_type'";
	}
	if($from_date!="" && $to_date ==""){
		$Psql.=" AND $created_date >= '$from_date'";
	}elseif($from_date=="" && $to_date !=""){
		$Psql.=" AND $created_date <= '$to_date'";
	}elseif($from_date!="" && $to_date !=""){
		$Psql.=" AND $created_date BETWEEN '$from_date' AND '$to_date'";
	}
	$pres = mysql_query($Psql);
	$cnum = mysql_num_rows($pres);
	if($cnum>0){
	$prow = mysql_fetch_object($pres);
	$receipt_amount = $prow->receipt_amount;
	}else{ $receipt_amount =0; }
	//======= Get Total Return Amount =========	
	$Rsql= "SELECT SUM(return_amount) AS return_amount FROM ".CUSTOMER_SALES_RETURN_DETAILS_VIEW." WHERE project_id='$project_id'"; 
	//AND product_status='No' ";
	
	if($area!="" && $trt==""){
		$Rsql.=" AND area_id='$area' ";
	}elseif($area!="" && $trt!=""){
		$Rsql.=" AND trt_id='$trt' ";
	}elseif($area=="" && $trt==""){
		$Rsql.=" AND division_id='$division_id' ";
	}	
	if($sales_type!=""){
		$Rsql.=" AND sales_type = '$sales_type'";
	}
	if($from_date!="" && $to_date ==""){
		$Rsql.=" AND return_date >= '$from_date'";
	}elseif($from_date=="" && $to_date !=""){
		$Rsql.=" AND return_date <= '$to_date'";
	}elseif($from_date!="" && $to_date !=""){
		$Rsql.=" AND return_date BETWEEN '$from_date' AND '$to_date'";
	}
	$rres = mysql_query($Rsql); 
	$rnum = mysql_num_rows($rres);
	if($rnum>0){
	$rrow = mysql_fetch_object($rres);
	$return_amount = $rrow->return_amount;
	}else{ $return_amount =0; }
	$receipt_amount = $receipt_amount - $return_amount;
	if($receipt_amount<0){ $receipt_amount=0;}
	
	return $receipt_amount; 
}

function getTotalDivisionAdjustAmount($division_id,$project_id){	 
	$from_date 	= formatDate(getRequest('date_from'));
	$to_date 	= formatDate(getRequest('date_to'));
	$area 	 	= getRequest('district');
	$trt 	 	= getRequest('area');
	$sales_type 	= getRequest('sales_type'); 
	 
	$receipt_amount=0; 	$return_amount=0;
	//======= Get Total Adjust Amount =========
	$Psql= "SELECT SUM(cr) AS receipt_amount FROM ".CUSTOMER_LEDGER_VIEW." WHERE project_id='$project_id' AND cr >0 AND description!='OB' AND adjustment =1 AND beddebts =0";
	if($area!="" && $trt==""){
		$Psql.=" AND area_id='$area' ";
	}elseif($area!="" && $trt!=""){
		$Psql.=" AND trt_id='$trt' ";
	}elseif($area=="" && $trt==""){
		$Psql.=" AND division_id='$division_id' ";
	}	
	if($sales_type!=""){
		$Psql.=" AND sales_type = '$sales_type'";
	}
	if($from_date!="" && $to_date ==""){
		$Psql.=" AND created_date >= '$from_date'";
	}elseif($from_date=="" && $to_date !=""){
		$Psql.=" AND created_date <= '$to_date'";
	}elseif($from_date!="" && $to_date !=""){
		$Psql.=" AND created_date BETWEEN '$from_date' AND '$to_date'";
	}
	$pres = mysql_query($Psql);
	$cnum = mysql_num_rows($pres);
	if($cnum>0){
	$prow = mysql_fetch_object($pres);
	$receipt_amount = $prow->receipt_amount;
	}else{ $receipt_amount =0; }
	//======= Get Total Return Amount =========	
	$Rsql= "SELECT SUM(return_amount) AS return_amount FROM ".CUSTOMER_SALES_RETURN_DETAILS_VIEW." WHERE project_id='$project_id'"; 
	//AND product_status='No' ";
	
	if($area!="" && $trt==""){
		$Rsql.=" AND area_id='$area' ";
	}elseif($area!="" && $trt!=""){
		$Rsql.=" AND trt_id='$trt' ";
	}elseif($area=="" && $trt==""){
		$Rsql.=" AND division_id='$division_id' ";
	}	
	if($sales_type!=""){
		$Rsql.=" AND sales_type = '$sales_type'";
	}
	if($from_date!="" && $to_date ==""){
		$Rsql.=" AND return_date >= '$from_date'";
	}elseif($from_date=="" && $to_date !=""){
		$Rsql.=" AND return_date <= '$to_date'";
	}elseif($from_date!="" && $to_date !=""){
		$Rsql.=" AND return_date BETWEEN '$from_date' AND '$to_date'";
	}
	$rres = mysql_query($Rsql); 
	$rnum = mysql_num_rows($rres);
	if($rnum>0){
	$rrow = mysql_fetch_object($rres);
	$return_amount = $rrow->return_amount;
	}else{ $return_amount =0; }
	$receipt_amount = $receipt_amount - $return_amount;
	if($receipt_amount<0){ $receipt_amount=0;}
	
	return $receipt_amount; 
}
function getTotalSupplierDivisionReceiptAmount($division_id,$project_id){	 
	$from_date 	= formatDate(getRequest('date_from'));
	$to_date 	= formatDate(getRequest('date_to'));
	$area 	 	= getRequest('district');
	$trt 	 	= getRequest('area');
	$sales_type 	= getRequest('sales_type');    
	   
	$issue_date 	= getRequest('issue_date');	
	if($issue_date!=""){$created_date="issue_date";}else{$created_date="created_date";} 
	$receipt_amount=0; 	$return_amount=0;
	//======= Get Total Receipt Amount =========
	$Psql= "SELECT SUM(cr) AS receipt_amount FROM ".SUPPLIER_LEDGER_VIEW." WHERE project_id='$project_id' AND cr >0 AND description!='OB' AND adjustment =0 AND beddebts =0";
	if($area!="" && $trt==""){
		$Psql.=" AND area_id='$area' ";
	}elseif($area!="" && $trt!=""){
		$Psql.=" AND trt_id='$trt' ";
	}elseif($area=="" && $trt==""){
		$Psql.=" AND division_id='$division_id' ";
	}	
	if($sales_type!=""){
		$Psql.=" AND sales_type = '$sales_type'";
	}
	if($from_date!="" && $to_date ==""){
		$Psql.=" AND $created_date >= '$from_date'";
	}elseif($from_date=="" && $to_date !=""){
		$Psql.=" AND $created_date <= '$to_date'";
	}elseif($from_date!="" && $to_date !=""){
		$Psql.=" AND $created_date BETWEEN '$from_date' AND '$to_date'";
	}
	$pres = mysql_query($Psql);
	$cnum = mysql_num_rows($pres);
	if($cnum>0){
	$prow = mysql_fetch_object($pres);
	$receipt_amount = $prow->receipt_amount;
	}else{ $receipt_amount =0; }
	//======= Get Total Return Amount =========	
	$Rsql= "SELECT SUM(return_amount) AS return_amount FROM ".SUPPLIER_SALES_RETURN_DETAILS_VIEW." WHERE project_id='$project_id'"; 
	//AND product_status='No' ";
	
	if($area!="" && $trt==""){
		$Rsql.=" AND area_id='$area' ";
	}elseif($area!="" && $trt!=""){
		$Rsql.=" AND trt_id='$trt' ";
	}elseif($area=="" && $trt==""){
		$Rsql.=" AND division_id='$division_id' ";
	}	
	if($sales_type!=""){
		$Rsql.=" AND sales_type = '$sales_type'";
	}
	if($from_date!="" && $to_date ==""){
		$Rsql.=" AND return_date >= '$from_date'";
	}elseif($from_date=="" && $to_date !=""){
		$Rsql.=" AND return_date <= '$to_date'";
	}elseif($from_date!="" && $to_date !=""){
		$Rsql.=" AND return_date BETWEEN '$from_date' AND '$to_date'";
	}
	$rres = mysql_query($Rsql); 
	$rnum = mysql_num_rows($rres);
	if($rnum>0){
	$rrow = mysql_fetch_object($rres);
	$return_amount = $rrow->return_amount;
	}else{ $return_amount =0; }
	$receipt_amount = $receipt_amount - $return_amount;
	if($receipt_amount<0){ $receipt_amount=0;}
	
	return $receipt_amount; 
}

function getTotalSupplierDivisionAdjustAmount($division_id,$project_id){	 
	$from_date 	= formatDate(getRequest('date_from'));
	$to_date 	= formatDate(getRequest('date_to'));
	$area 	 	= getRequest('district');
	$trt 	 	= getRequest('area');
	$sales_type 	= getRequest('sales_type');   	   
	
	$receipt_amount=0; 	$return_amount=0;
	//======= Get Total Receipt Amount =========
	$Psql= "SELECT SUM(cr) AS receipt_amount FROM ".SUPPLIER_LEDGER_VIEW." WHERE project_id='$project_id' AND cr >0 AND description!='OB' AND adjustment =1 AND beddebts =0";
	if($area!="" && $trt==""){
		$Psql.=" AND area_id='$area' ";
	}elseif($area!="" && $trt!=""){
		$Psql.=" AND trt_id='$trt' ";
	}elseif($area=="" && $trt==""){
		$Psql.=" AND division_id='$division_id' ";
	}	
	if($sales_type!=""){
		$Psql.=" AND sales_type = '$sales_type'";
	}
	if($from_date!="" && $to_date ==""){
		$Psql.=" AND created_date >= '$from_date'";
	}elseif($from_date=="" && $to_date !=""){
		$Psql.=" AND created_date <= '$to_date'";
	}elseif($from_date!="" && $to_date !=""){
		$Psql.=" AND created_date BETWEEN '$from_date' AND '$to_date'";
	}
	$pres = mysql_query($Psql);
	$cnum = mysql_num_rows($pres);
	if($cnum>0){
	$prow = mysql_fetch_object($pres);
	$receipt_amount = $prow->receipt_amount;
	}else{ $receipt_amount =0; }
	
	$receipt_amount = $receipt_amount - $return_amount;
	if($receipt_amount <0){ $receipt_amount=0;}
	
	return $receipt_amount; 
}
function getTotalDivisionBedDebtAmount($division_id,$project_id){	 
	$from_date 	= formatDate(getRequest('date_from'));
	$to_date 	= formatDate(getRequest('date_to'));
	$area 	 	= getRequest('district');
	$trt 	 	= getRequest('area');
	$sales_type 	= getRequest('sales_type');
	$BedDebtAmount=0;
	//======= Get Total BedDebt Amount =========
	$Psql= "SELECT SUM(return_amount) AS BedDebtAmount FROM ".CUSTOMER_SALES_RETURN_DETAILS_VIEW." WHERE project_id='$project_id' AND product_status='Yes' ";	
	if($area!="" && $trt==""){
		$Psql.=" AND area_id='$area' ";
	}elseif($area!="" && $trt!=""){
		$Psql.=" AND trt_id='$trt' ";
	}elseif($area=="" && $trt==""){
		$Psql.=" AND division_id='$division_id' ";
	}
	if($sales_type!=""){
		$Psql.=" AND sales_type = '$sales_type'";
	}
	if($from_date!="" && $to_date ==""){
		$Psql.=" AND return_date >= '$from_date'";
	}elseif($from_date=="" && $to_date !=""){
		$Psql.=" AND return_date <= '$to_date'";
	}elseif($from_date!="" && $to_date !=""){
		$Psql.=" AND return_date BETWEEN '$from_date' AND '$to_date'";
	}
	$pres = mysql_query($Psql);
	$cnum = mysql_num_rows($pres);
	if($cnum>0){
	$prow = mysql_fetch_object($pres);
	$BedDebtAmount = $prow->BedDebtAmount;
	}
	
	//======= Get Total BedDebts from ledger  =========
	$Bsql= "SELECT SUM(cr) AS beddebts_amount FROM ".CUSTOMER_LEDGER_VIEW." WHERE project_id='$project_id' AND cr >0 AND description!='OB' AND adjustment =0 AND beddebts =1";
	if($area!="" && $trt==""){
		$Bsql.=" AND area_id='$area' ";
	}elseif($area!="" && $trt!=""){
		$Bsql.=" AND trt_id='$trt' ";
	}elseif($area=="" && $trt==""){
		$Bsql.=" AND division_id='$division_id' ";
	}
	if($sales_type!=""){
		$Bsql.=" AND sales_type = '$sales_type'";
	}
	if($from_date!="" && $to_date ==""){
		$Bsql.=" AND created_date >= '$from_date'";
	}elseif($from_date=="" && $to_date !=""){
		$Bsql.=" AND created_date <= '$to_date'";
	}elseif($from_date!="" && $to_date !=""){
		$Bsql.=" AND created_date BETWEEN '$from_date' AND '$to_date'";
	}
	$bres = mysql_query($Bsql); 
	$bnum = mysql_num_rows($bres);
	if($bnum >0){
	$brow = mysql_fetch_object($bres);
	$beddebts_amount = $brow->beddebts_amount;
	}else{ $beddebts_amount =0; }
	$BedDebtAmount+=$beddebts_amount;	
	return $BedDebtAmount; 
}

function getTotalSupplierDivisionBedDebtAmount($division_id,$project_id){	 
	$from_date 	= formatDate(getRequest('date_from'));
	$to_date 	= formatDate(getRequest('date_to'));
	$area 	 	= getRequest('district');
	$trt 	 	= getRequest('area');
	$sales_type 	= getRequest('sales_type'); 	
	$BedDebtAmount=0;
	//======= Get Total BedDebt Amount =========
	$Psql= "SELECT SUM(return_amount) AS BedDebtAmount FROM ".SUPPLIER_SALES_RETURN_DETAILS_VIEW." WHERE project_id='$project_id' AND product_status='Yes' ";	
	if($area!="" && $trt==""){
		$Psql.=" AND area_id='$area' ";
	}elseif($area!="" && $trt!=""){
		$Psql.=" AND trt_id='$trt' ";
	}elseif($area=="" && $trt==""){
		$Psql.=" AND division_id='$division_id' ";
	}
	if($sales_type!=""){
		$Psql.=" AND sales_type = '$sales_type'";
	}
	if($from_date!="" && $to_date ==""){
		$Psql.=" AND return_date >= '$from_date'";
	}elseif($from_date=="" && $to_date !=""){
		$Psql.=" AND return_date <= '$to_date'";
	}elseif($from_date!="" && $to_date !=""){
		$Psql.=" AND return_date BETWEEN '$from_date' AND '$to_date'";
	}
	$pres = mysql_query($Psql);
	$cnum = mysql_num_rows($pres);
	if($cnum>0){
	$prow = mysql_fetch_object($pres);
	$BedDebtAmount = $prow->BedDebtAmount;
	}
	//======= Get Total BedDebts from ledger  =========
	$Bsql= "SELECT SUM(cr) AS beddebts_amount FROM ".SUPPLIER_LEDGER_VIEW." WHERE project_id='$project_id' AND cr >0 AND description!='OB' AND adjustment =0 AND beddebts =1";
	if($area!="" && $trt==""){
		$Bsql.=" AND area_id='$area' ";
	}elseif($area!="" && $trt!=""){
		$Bsql.=" AND trt_id='$trt' ";
	}elseif($area=="" && $trt==""){
		$Bsql.=" AND division_id='$division_id' ";
	}
	if($sales_type!=""){
		$Bsql.=" AND sales_type = '$sales_type'";
	}
	if($from_date!="" && $to_date ==""){
		$Bsql.=" AND created_date >= '$from_date'";
	}elseif($from_date=="" && $to_date !=""){
		$Bsql.=" AND created_date <= '$to_date'";
	}elseif($from_date!="" && $to_date !=""){
		$Bsql.=" AND created_date BETWEEN '$from_date' AND '$to_date'";
	}
	$bres = mysql_query($Bsql); 
	$bnum = mysql_num_rows($bres);
	if($bnum >0){
	$brow = mysql_fetch_object($bres);
	$beddebts_amount = $brow->beddebts_amount;
	}else{ $beddebts_amount =0; }
	$BedDebtAmount+=$beddebts_amount;		
	return $BedDebtAmount; 
}
function getTotalDivisionSalesReturnAmount($division_id,$project_id){	 
	$from_date 	= formatDate(getRequest('date_from'));
	$to_date 	= formatDate(getRequest('date_to'));
	$area 	 	= getRequest('district');
	$trt 	 	= getRequest('area');
	$sales_type     = getRequest('sales_type'); 
	$SalesReturn=0;
	//======= Get Total BedDebt Amount =========
	$Psql= "SELECT SUM(return_amount) AS SalesReturn FROM ".CUSTOMER_SALES_RETURN_DETAILS_VIEW." WHERE project_id='$project_id' 
	AND product_status='No' ";	
	if($area!="" && $trt==""){
		$Psql.=" AND area_id='$area' ";
	}elseif($area!="" && $trt!=""){
		$Psql.=" AND trt_id='$trt' ";
	}elseif($area=="" && $trt==""){
		$Psql.=" AND division_id='$division_id' ";
	}
	if($sales_type!=""){
		$Psql.=" AND sales_type = '$sales_type'";
	}
	if($from_date!="" && $to_date ==""){
		$Psql.=" AND return_date >= '$from_date'";
	}elseif($from_date=="" && $to_date !=""){
		$Psql.=" AND return_date <= '$to_date'";
	}elseif($from_date!="" && $to_date !=""){
		$Psql.=" AND return_date BETWEEN '$from_date' AND '$to_date'";
	}
	$pres = mysql_query($Psql);
	$cnum = mysql_num_rows($pres);
	if($cnum>0){
	$prow = mysql_fetch_object($pres);
	$SalesReturn = $prow->SalesReturn;
	}	
	return $SalesReturn; 
}

function getTotalSupplierDivisionSalesReturnAmount($division_id,$project_id){	 
	$from_date 	= formatDate(getRequest('date_from'));
	$to_date 	= formatDate(getRequest('date_to'));
	$area 	 	= getRequest('district');
	$trt 	 	= getRequest('area');
	$sales_type = getRequest('sales_type'); 
	$SalesReturn=0;
	//======= Get Total BedDebt Amount =========
	$Psql= "SELECT SUM(return_amount) AS SalesReturn FROM ".SUPPLIER_SALES_RETURN_DETAILS_VIEW." WHERE project_id='$project_id' 
	AND product_status='No' ";	
	if($area!="" && $trt==""){
		$Psql.=" AND area_id='$area' ";
	}elseif($area!="" && $trt!=""){
		$Psql.=" AND trt_id='$trt' ";
	}elseif($area=="" && $trt==""){
		$Psql.=" AND division_id='$division_id' ";
	}
	if($sales_type!=""){
		$Psql.=" AND sales_type = '$sales_type'";
	}
	if($from_date!="" && $to_date ==""){
		$Psql.=" AND return_date >= '$from_date'";
	}elseif($from_date=="" && $to_date !=""){
		$Psql.=" AND return_date <= '$to_date'";
	}elseif($from_date!="" && $to_date !=""){
		$Psql.=" AND return_date BETWEEN '$from_date' AND '$to_date'";
	}
	$pres = mysql_query($Psql);
	$cnum = mysql_num_rows($pres);
	if($cnum>0){
	$prow = mysql_fetch_object($pres);
	$SalesReturn = $prow->SalesReturn;
	}	
	return $SalesReturn; 
}
function getTotalDivisionClosingAmount($division_id,$project_id){	 
	$from_date 	 = formatDate(getRequest('date_from'));
	$to_date 	 = formatDate(getRequest('date_to'));	  
	$area		 = getRequest('district');  
	$trt		 = getRequest('area'); 
	$sales_type  = getRequest('sales_type');  
	$ClosingBalance=0;
	//======= Get Total BedDebt Amount =========
	$Psql= "SELECT (SUM(dr)-SUM(cr)) AS ClosingBalance FROM ".CUSTOMER_LEDGER_VIEW." WHERE project_id='$project_id' ";	
	if($area!="" && $trt==""){
		$Psql.=" AND area_id='$area' ";
	}elseif($area!="" && $trt!=""){
		$Psql.=" AND trt_id='$trt' ";
	}elseif($area=="" && $trt==""){
		$Psql.=" AND division_id='$division_id' ";
	}
	if($sales_type!=""){
		$Psql.=" AND sales_type = '$sales_type'";
	}
	if($to_date !=""){
		$Psql.=" AND created_date <= '$to_date'";
	}
	$pres = mysql_query($Psql);
	$cnum = mysql_num_rows($pres);
	if($cnum>0){
	$prow = mysql_fetch_object($pres);
	$ClosingBalance = $prow->ClosingBalance;
	}	
	return $ClosingBalance; 
}

function getTotalSupplierDivisionClosingAmount($division_id,$project_id){	 
	$from_date 	 = formatDate(getRequest('date_from'));
	$to_date 	 = formatDate(getRequest('date_to'));	  
	$area		 = getRequest('district');  
	$trt		 = getRequest('area'); 
	$sales_type  = getRequest('sales_type');  
	$ClosingBalance=0;
	//======= Get Total BedDebt Amount =========
	$Psql= "SELECT (SUM(dr)-SUM(cr)) AS ClosingBalance FROM ".SUPPLIER_LEDGER_VIEW." WHERE project_id='$project_id' ";	
	if($area!="" && $trt==""){
		$Psql.=" AND area_id='$area' ";
	}elseif($area!="" && $trt!=""){
		$Psql.=" AND trt_id='$trt' ";
	}elseif($area=="" && $trt==""){
		$Psql.=" AND division_id='$division_id' ";
	}
	if($sales_type!=""){
		$Psql.=" AND sales_type = '$sales_type'";
	}
	if($to_date !=""){
		$Psql.=" AND created_date <= '$to_date'";
	}
	$pres = mysql_query($Psql);
	$cnum = mysql_num_rows($pres);
	if($cnum>0){
	$prow = mysql_fetch_object($pres);
	$ClosingBalance = $prow->ClosingBalance;
	}	
	return $ClosingBalance; 
}
//====== All Area Function =========
function getTotalAreaOB($district_id,$project_id){	 
	$from_date 	= formatDate(getRequest('date_from'));
	$to_date 	= formatDate(getRequest('date_to'));
	$sales_type  	= getRequest('sales_type');
	$opening_balance=0; $ob_dr=0; $ob_cr=0;
	if(($from_date=="") || ($from_date=="2014/01/01")){
		//======== Total Dr OB ======
		$dsql= "SELECT SUM(opening_balance) AS ob_dr FROM ".CUSTOMER_OB_VIEW." WHERE area_id='$district_id' AND project_id='$project_id' AND op_type='Dr'";
		if($sales_type!=""){
			$dsql.=" AND sales_type = '$sales_type'";
		}
		$dres = mysql_query($dsql);
		$dnum = mysql_num_rows($dres);
		if($dnum>0){
		$drow = mysql_fetch_object($dres);
		$ob_dr = $drow->ob_dr;
		}
		//======== Total Cr OB ======
		$csql= "SELECT SUM(opening_balance) AS ob_cr FROM ".CUSTOMER_OB_VIEW." WHERE area_id='$district_id' AND project_id='$project_id' AND op_type='Cr'";
		if($sales_type!=""){
			$csql.=" AND sales_type = '$sales_type'";
		}
		$cres = mysql_query($csql);
		$cnum = mysql_num_rows($cres);
		if($cnum>0){
		$crow = mysql_fetch_object($cres);
		$ob_cr = $crow->ob_cr;
		}
		$opening_balance = ($ob_dr-$ob_cr);			
	}else{
		$Psql= "SELECT (SUM(dr) - SUM(cr) )AS customer_ob FROM ".CUSTOMER_LEDGER_VIEW." WHERE area_id='$district_id' AND project_id='$project_id'";
		if($from_date!=""){
			$Psql.=" AND created_date < '$from_date'";
		}
		if($sales_type!=""){
			$Psql.=" AND sales_type = '$sales_type'";
		}
		$pres = mysql_query($Psql);
		$cnum = mysql_num_rows($pres);
		if($cnum>0){
		$prow = mysql_fetch_object($pres);
		$opening_balance = $prow->customer_ob;
		}
	}
	return $opening_balance; 
}
function getTotalSupplierAreaOB($district_id,$project_id){	 
	$from_date 	 = formatDate(getRequest('date_from'));
	$to_date 	 = formatDate(getRequest('date_to'));
	$sales_type  = getRequest('sales_type'); 
	$opening_balance=0; $ob_dr=0; $ob_cr=0;
	if(($from_date=="") || ($from_date=="2014/01/01")){
		//======== Total Dr OB ======
		$dsql= "SELECT SUM(opening_balance) AS ob_dr FROM ".SUPPLIER_OB_VIEW." WHERE area_id='$district_id' AND project_id='$project_id' AND op_type='Dr'";
		if($sales_type!=""){
			$dsql.=" AND sales_type = '$sales_type'";
		}
		$dres = mysql_query($dsql);
		$dnum = mysql_num_rows($dres);
		if($dnum>0){
		$drow = mysql_fetch_object($dres);
		$ob_dr = $drow->ob_dr;
		}
		//======== Total Cr OB ======
		$csql= "SELECT SUM(opening_balance) AS ob_cr FROM ".SUPPLIER_OB_VIEW." WHERE area_id='$district_id' AND project_id='$project_id' AND op_type='Cr'";
		if($sales_type!=""){
			$csql.=" AND sales_type = '$sales_type'";
		}
		$cres = mysql_query($csql);
		$cnum = mysql_num_rows($cres);
		if($cnum>0){
		$crow = mysql_fetch_object($cres);
		$ob_cr = $crow->ob_cr;
		}
		$opening_balance = ($ob_dr-$ob_cr);			
	}else{
		$Psql= "SELECT (SUM(dr) - SUM(cr) )AS customer_ob FROM ".SUPPLIER_LEDGER_VIEW." WHERE area_id='$district_id' AND project_id='$project_id'";
		if($from_date!=""){
			$Psql.=" AND created_date < '$from_date'";
		}
		if($sales_type!=""){
			$Psql.=" AND sales_type = '$sales_type'";
		}
		$pres = mysql_query($Psql);
		$cnum = mysql_num_rows($pres);
		if($cnum>0){
		$prow = mysql_fetch_object($pres);
		$opening_balance = $prow->customer_ob;
		}
	}
	return $opening_balance; 
}
function getTotalAreaSalesAmount($district_id,$project_id){	 
	$from_date 	 = formatDate(getRequest('date_from'));
	$to_date 	 = formatDate(getRequest('date_to'));
	$sales_type  	 = getRequest('sales_type');
	$odd_date 	 = getRequest('odd_date'); 	    
	$minus_odd_date  = getRequest('minus_odd_date'); 
	if($odd_date !="" || $minus_odd_date!=""){$sales_date="sales_date";}else{$sales_date="delivery_date";} 
	$sales_amount=0; 	
	$Psql= "SELECT SUM(sales_amount) AS sales_amount FROM ".CUSTOMER_SALES_LEDGER_VIEW." WHERE area_id='$district_id' AND project_id='$project_id'";	
	if($sales_type!=""){
		$Psql.=" AND sales_type = '$sales_type'";
	}
	if($from_date!="" && $to_date ==""){
		$Psql.=" AND $sales_date >= '$from_date'";
	}elseif($from_date=="" && $to_date !=""){
		$Psql.=" AND $sales_date <= '$to_date'";
	}elseif($from_date!="" && $to_date !=""){
		$Psql.=" AND $sales_date BETWEEN '$from_date' AND '$to_date'";
	}
	if($minus_odd_date!=""){
		$Psql.=" AND sales_date = value_date";
	}
	$pres = mysql_query($Psql); //echo $Psql;
	$cnum = mysql_num_rows($pres);
	if($cnum>0){
	$prow = mysql_fetch_object($pres);
	$sales_amount = $prow->sales_amount;
	}
	// SELECT SUM( dr ) AS sales FROM `vw_customer_ledger` WHERE `area_id` =1 AND `description` = "OS" 	
	return $sales_amount; 
}

function getTotalSupplierAreaSalesAmount($district_id,$project_id){	 
	$from_date 	 = formatDate(getRequest('date_from'));
	$to_date 	 = formatDate(getRequest('date_to'));
	$sales_type      = getRequest('sales_type'); 
	$odd_date 	 = getRequest('odd_date'); 
	$minus_odd_date  = getRequest('minus_odd_date'); 
	if($odd_date !="" || $minus_odd_date!=""){$sales_date="sales_date";}else{$sales_date="delivery_date";} 
	$sales_amount=0; 	
	$Psql= "SELECT SUM(sales_amount) AS sales_amount FROM ".SUPPLIER_SALES_LEDGER_VIEW." WHERE area_id='$district_id' AND project_id='$project_id'";	
	if($sales_type!=""){
		$Psql.=" AND sales_type = '$sales_type'";
	}
	if($from_date!="" && $to_date ==""){
		$Psql.=" AND $sales_date >= '$from_date'";
	}elseif($from_date=="" && $to_date !=""){
		$Psql.=" AND $sales_date <= '$to_date'";
	}elseif($from_date!="" && $to_date !=""){
		$Psql.=" AND $sales_date BETWEEN '$from_date' AND '$to_date'";
	}
	if($minus_odd_date!=""){
		$Psql.=" AND sales_date = value_date";
	}
	$pres = mysql_query($Psql); //echo $Psql;
	$cnum = mysql_num_rows($pres);
	if($cnum>0){
	$prow = mysql_fetch_object($pres);
	$sales_amount = $prow->sales_amount;
	}
	// SELECT SUM( dr ) AS sales FROM `vw_customer_ledger` WHERE `area_id` =1 AND `description` = "OS" 	
	return $sales_amount; 
}
function getTotalAreaReceiptAmount($district_id,$project_id){	 
	$from_date 	 = formatDate(getRequest('date_from'));
	$to_date 	 = formatDate(getRequest('date_to'));
	$sales_type      = getRequest('sales_type');    
	$issue_date 	= getRequest('issue_date');	
	if($issue_date!=""){$created_date="issue_date";}else{$created_date="created_date";} 
	$receipt_amount=0; 	$return_amount=0;
	//======= Get Total Receipt Amount =========
	$Psql= "SELECT SUM(cr) AS receipt_amount FROM ".CUSTOMER_LEDGER_VIEW." WHERE area_id='$district_id' AND project_id='$project_id' AND cr >0 AND description!='OB' AND adjustment =0 AND beddebts =0";	
	if($sales_type!=""){
		$Psql.=" AND sales_type = '$sales_type'";
	}
	if($from_date!="" && $to_date ==""){
		$Psql.=" AND $created_date >= '$from_date'";
	}elseif($from_date=="" && $to_date !=""){
		$Psql.=" AND $created_date <= '$to_date'";
	}elseif($from_date!="" && $to_date !=""){
		$Psql.=" AND $created_date BETWEEN '$from_date' AND '$to_date'";
	}
	$pres = mysql_query($Psql); //echo $Psql; exit;
	$cnum = mysql_num_rows($pres);
	if($cnum>0){
	$prow = mysql_fetch_object($pres);
	$receipt_amount = $prow->receipt_amount;
	}else{ $receipt_amount =0; }
	//======= Get Total Sales Return Amount =========			
	$Rsql= "SELECT SUM(return_amount) AS return_amount FROM ".CUSTOMER_SALES_RETURN_DETAILS_VIEW." WHERE area_id='$district_id' AND project_id='$project_id' "; // AND product_status='No' 
	if($sales_type!=""){
		$Rsql.=" AND sales_type = '$sales_type'";
	}
	if($from_date!="" && $to_date ==""){
		$Rsql.=" AND return_date >= '$from_date'";
	}elseif($from_date=="" && $to_date !=""){
		$Rsql.=" AND return_date <= '$to_date'";
	}elseif($from_date!="" && $to_date !=""){
		$Rsql.=" AND return_date BETWEEN '$from_date' AND '$to_date'";
	}
	$rres = mysql_query($Rsql); //echo $Rsql;
	$rnum = mysql_num_rows($rres);
	if($rnum>0){
	$rrow = mysql_fetch_object($rres);
	$return_amount = $rrow->return_amount;
	}else{ $return_amount =0; }
	$receipt_amount = $receipt_amount - $return_amount;
	if($receipt_amount <0){ $receipt_amount=0;}
	
	return $receipt_amount; 
}
// ==== getTotalAreaAdjustAmount =====
function getTotalAreaAdjustAmount($district_id,$project_id){	 
	$from_date 	 = formatDate(getRequest('date_from'));
	$to_date 	 = formatDate(getRequest('date_to'));
	$sales_type      = getRequest('sales_type');
	$receipt_amount=0; 	$return_amount=0;
	//======= Get Total Receipt Amount =========
	$Psql= "SELECT SUM(cr) AS receipt_amount FROM ".CUSTOMER_LEDGER_VIEW." WHERE area_id='$district_id' AND project_id='$project_id' AND cr >0 AND description!='OB' AND adjustment =1 AND beddebts =0";	
	if($sales_type!=""){
		$Psql.=" AND sales_type = '$sales_type'";
	}
	if($from_date!="" && $to_date ==""){
		$Psql.=" AND created_date >= '$from_date'";
	}elseif($from_date=="" && $to_date !=""){
		$Psql.=" AND created_date <= '$to_date'";
	}elseif($from_date!="" && $to_date !=""){
		$Psql.=" AND created_date BETWEEN '$from_date' AND '$to_date'";
	}
	$pres = mysql_query($Psql);
	$cnum = mysql_num_rows($pres);
	if($cnum>0){
	$prow = mysql_fetch_object($pres);
	$receipt_amount = $prow->receipt_amount;
	}else{ $receipt_amount =0; }
	
	if($receipt_amount <0){ $receipt_amount=0;}
	
	return $receipt_amount; 
}
function getTotalSupplierAreaReceiptAmount($district_id,$project_id){	 
	$from_date 	 = formatDate(getRequest('date_from'));
	$to_date 	 = formatDate(getRequest('date_to'));
	$sales_type  	 = getRequest('sales_type');     
	$issue_date 	= getRequest('issue_date');	
	if($issue_date!=""){$created_date="issue_date";}else{$created_date="created_date";} 
	$receipt_amount=0; 	$return_amount=0;
	//======= Get Total Receipt Amount =========
	$Psql= "SELECT SUM(cr) AS receipt_amount FROM ".SUPPLIER_LEDGER_VIEW." WHERE area_id='$district_id' AND project_id='$project_id' AND cr >0 AND description!='OB' AND adjustment =0 AND beddebts =0";	
	if($sales_type!=""){
		$Psql.=" AND sales_type = '$sales_type'";
	}
	if($from_date!="" && $to_date ==""){
		$Psql.=" AND $created_date >= '$from_date'";
	}elseif($from_date=="" && $to_date !=""){
		$Psql.=" AND $created_date <= '$to_date'";
	}elseif($from_date!="" && $to_date !=""){
		$Psql.=" AND $created_date BETWEEN '$from_date' AND '$to_date'";
	}
	$pres = mysql_query($Psql);
	$cnum = mysql_num_rows($pres);
	if($cnum>0){
	$prow = mysql_fetch_object($pres);
	$receipt_amount = $prow->receipt_amount;
	}else{ $receipt_amount =0; }
	//======= Get Total Sales Return Amount =========			
	$Rsql= "SELECT SUM(return_amount) AS return_amount FROM ".SUPPLIER_SALES_RETURN_DETAILS_VIEW." WHERE area_id='$district_id' AND project_id='$project_id' "; // AND product_status='No' 
	if($sales_type!=""){
		$Rsql.=" AND sales_type = '$sales_type'";
	}
	if($from_date!="" && $to_date ==""){
		$Rsql.=" AND return_date >= '$from_date'";
	}elseif($from_date=="" && $to_date !=""){
		$Rsql.=" AND return_date <= '$to_date'";
	}elseif($from_date!="" && $to_date !=""){
		$Rsql.=" AND return_date BETWEEN '$from_date' AND '$to_date'";
	}
	$rres = mysql_query($Rsql); 
	$rnum = mysql_num_rows($rres);
	if($rnum>0){
	$rrow = mysql_fetch_object($rres);
	$return_amount = $rrow->return_amount;
	}else{ $return_amount =0; }
	$receipt_amount = $receipt_amount - $return_amount;
	if($receipt_amount<0){ $receipt_amount=0;}
	
	return $receipt_amount; 
}
function getTotalSupplierAreaAdjustAmount($district_id,$project_id){	 
	$from_date 	 = formatDate(getRequest('date_from'));
	$to_date 	 = formatDate(getRequest('date_to'));
	$sales_type  	 = getRequest('sales_type'); 
	$receipt_amount=0; 	$return_amount=0;
	//======= Get Total Receipt Amount =========
	$Psql= "SELECT SUM(cr) AS receipt_amount FROM ".SUPPLIER_LEDGER_VIEW." WHERE area_id='$district_id' AND project_id='$project_id' AND cr >0 AND description!='OB'  AND adjustment =1 AND beddebts =0";	
	if($sales_type!=""){
		$Psql.=" AND sales_type = '$sales_type'";
	}
	if($from_date!="" && $to_date ==""){
		$Psql.=" AND created_date >= '$from_date'";
	}elseif($from_date=="" && $to_date !=""){
		$Psql.=" AND created_date <= '$to_date'";
	}elseif($from_date!="" && $to_date !=""){
		$Psql.=" AND created_date BETWEEN '$from_date' AND '$to_date'";
	}
	$pres = mysql_query($Psql);
	$cnum = mysql_num_rows($pres);
	if($cnum>0){
	$prow = mysql_fetch_object($pres);
	$receipt_amount = $prow->receipt_amount;
	}else{ $receipt_amount =0; }
		
	if($receipt_amount <0){ $receipt_amount=0;}
	
	return $receipt_amount; 
}
function getTotalAreaBedDebtAmount($district_id,$project_id){	 
	$from_date 	 = formatDate(getRequest('date_from'));
	$to_date 	 = formatDate(getRequest('date_to'));
	$sales_type  	 = getRequest('sales_type');
	$BedDebtAmount=0;
	//======= Get Total BedDebt Amount =========
	$Psql= "SELECT SUM(return_amount) AS BedDebtAmount FROM ".CUSTOMER_SALES_RETURN_DETAILS_VIEW." WHERE area_id='$district_id' AND project_id='$project_id' AND product_status='Yes' ";	
	if($sales_type!=""){
		$Psql.=" AND sales_type = '$sales_type'";
	}
	if($from_date!="" && $to_date ==""){
		$Psql.=" AND return_date >= '$from_date'";
	}elseif($from_date=="" && $to_date !=""){
		$Psql.=" AND return_date <= '$to_date'";
	}elseif($from_date!="" && $to_date !=""){
		$Psql.=" AND return_date BETWEEN '$from_date' AND '$to_date'";
	}
	$pres = mysql_query($Psql);
	$cnum = mysql_num_rows($pres);
	if($cnum >0){
	$prow = mysql_fetch_object($pres);
	$BedDebtAmount = $prow->BedDebtAmount;
	}
	//=== Get Bedbebts from Ledger ===	
	$Psql= "SELECT SUM(cr) AS bedbebts_amount FROM ".CUSTOMER_LEDGER_VIEW." WHERE area_id='$district_id' AND project_id='$project_id' AND cr >0 AND description!='OB' AND adjustment =0 AND beddebts =1";	
	if($sales_type!=""){
		$Psql.=" AND sales_type = '$sales_type'";
	}
	if($from_date!="" && $to_date ==""){
		$Psql.=" AND created_date >= '$from_date'";
	}elseif($from_date=="" && $to_date !=""){
		$Psql.=" AND created_date <= '$to_date'";
	}elseif($from_date!="" && $to_date !=""){
		$Psql.=" AND created_date BETWEEN '$from_date' AND '$to_date'";
	}
	$pres = mysql_query($Psql);
	$cnum = mysql_num_rows($pres);
	if($cnum >0){
	$prow = mysql_fetch_object($pres);
	$bedbebts_amount = $prow->bedbebts_amount;
	}else{ $bedbebts_amount =0; }
	$BedDebtAmount +=$bedbebts_amount;	
	return $BedDebtAmount; 
}
function getTotalSupplierAreaBedDebtAmount($district_id,$project_id){	 
	$from_date 	= formatDate(getRequest('date_from'));
	$to_date 	= formatDate(getRequest('date_to'));
	$sales_type  	= getRequest('sales_type');
	$BedDebtAmount=0;
	//======= Get Total BedDebt Amount =========
	$Psql= "SELECT SUM(return_amount) AS BedDebtAmount FROM ".SUPPLIER_SALES_RETURN_DETAILS_VIEW." WHERE area_id='$district_id' AND project_id='$project_id' 
	AND product_status='Yes' ";	
	if($sales_type!=""){
		$Psql.=" AND sales_type = '$sales_type'";
	}
	if($from_date!="" && $to_date ==""){
		$Psql.=" AND return_date >= '$from_date'";
	}elseif($from_date=="" && $to_date !=""){
		$Psql.=" AND return_date <= '$to_date'";
	}elseif($from_date!="" && $to_date !=""){
		$Psql.=" AND return_date BETWEEN '$from_date' AND '$to_date'";
	}
	$pres = mysql_query($Psql);
	$cnum = mysql_num_rows($pres);
	if($cnum>0){
	$prow = mysql_fetch_object($pres);
	$BedDebtAmount = $prow->BedDebtAmount;
	}	
	return $BedDebtAmount; 
}
function getTotalAreaSalesReturnAmount($district_id,$project_id){	 
	$from_date 	 = formatDate(getRequest('date_from'));
	$to_date 	 = formatDate(getRequest('date_to'));
	$sales_type  	 = getRequest('sales_type'); 
	$SalesReturn=0;
	//======= Get Total BedDebt Amount =========
	$Psql= "SELECT SUM(return_amount) AS SalesReturn FROM ".CUSTOMER_SALES_RETURN_DETAILS_VIEW." WHERE area_id='$district_id' AND project_id='$project_id' 
	AND product_status='No' ";	
	if($sales_type!=""){
		$Psql.=" AND sales_type = '$sales_type'";
	}
	if($from_date!="" && $to_date ==""){
		$Psql.=" AND return_date >= '$from_date'";
	}elseif($from_date=="" && $to_date !=""){
		$Psql.=" AND return_date <= '$to_date'";
	}elseif($from_date!="" && $to_date !=""){
		$Psql.=" AND return_date BETWEEN '$from_date' AND '$to_date'";
	}
	$pres = mysql_query($Psql);
	$cnum = mysql_num_rows($pres);
	if($cnum>0){
	$prow = mysql_fetch_object($pres);
	$SalesReturn = $prow->SalesReturn;
	}	
	return $SalesReturn; 
}
function getTotalSupplierAreaSalesReturnAmount($district_id,$project_id){	 
	$from_date 	 = formatDate(getRequest('date_from'));
	$to_date 	 = formatDate(getRequest('date_to'));
	$sales_type  = getRequest('sales_type'); 
	$SalesReturn=0;
	//======= Get Total BedDebt Amount =========
	$Psql= "SELECT SUM(return_amount) AS SalesReturn FROM ".SUPPLIER_SALES_RETURN_DETAILS_VIEW." WHERE area_id='$district_id' AND project_id='$project_id' 
	AND product_status='No' ";	
	if($sales_type!=""){
		$Psql.=" AND sales_type = '$sales_type'";
	}
	if($from_date!="" && $to_date ==""){
		$Psql.=" AND return_date >= '$from_date'";
	}elseif($from_date=="" && $to_date !=""){
		$Psql.=" AND return_date <= '$to_date'";
	}elseif($from_date!="" && $to_date !=""){
		$Psql.=" AND return_date BETWEEN '$from_date' AND '$to_date'";
	}
	$pres = mysql_query($Psql);
	$cnum = mysql_num_rows($pres);
	if($cnum>0){
	$prow = mysql_fetch_object($pres);
	$SalesReturn = $prow->SalesReturn;
	}	
	return $SalesReturn; 
}
function getTotalAreaClosingAmount($district_id,$project_id){	 
	$from_date 	= formatDate(getRequest('date_from'));
	$to_date 	= formatDate(getRequest('date_to'));
	$sales_type  	= getRequest('sales_type');
	$ClosingBalance=0;
	//======= Get Total BedDebt Amount =========
	$Psql= "SELECT (SUM(dr)-SUM(cr)) AS ClosingBalance FROM ".CUSTOMER_LEDGER_VIEW." WHERE area_id='$district_id' AND project_id='$project_id' ";	
	if($sales_type!=""){
		$Psql.=" AND sales_type = '$sales_type'";
	}
	if($to_date !=""){
		$Psql.=" AND created_date <= '$to_date'";
	}
	$pres = mysql_query($Psql);
	$cnum = mysql_num_rows($pres);
	if($cnum>0){
	$prow = mysql_fetch_object($pres);
	$ClosingBalance = $prow->ClosingBalance;
	}	
	return $ClosingBalance; 
}

function getTotalSupplierAreaClosingAmount($district_id,$project_id){	 
	$from_date 	 = formatDate(getRequest('date_from'));
	$to_date 	 = formatDate(getRequest('date_to'));
	$sales_type  = getRequest('sales_type'); 
	$ClosingBalance=0;
	//======= Get Total BedDebt Amount =========
	$Psql= "SELECT (SUM(dr)-SUM(cr)) AS ClosingBalance FROM ".SUPPLIER_LEDGER_VIEW." WHERE area_id='$district_id' AND project_id='$project_id' ";	
	if($sales_type!=""){
		$Psql.=" AND sales_type = '$sales_type'";
	}
	if($to_date !=""){
		$Psql.=" AND created_date <= '$to_date'";
	}
	$pres = mysql_query($Psql);
	$cnum = mysql_num_rows($pres);
	if($cnum>0){
	$prow = mysql_fetch_object($pres);
	$ClosingBalance = $prow->ClosingBalance;
	}	
	return $ClosingBalance; 
}
//====== All TRT Function =========
function getTotalTRTOB($trt_id,$project_id){	 
	$from_date 	 = formatDate(getRequest('date_from'));
	$to_date 	 = formatDate(getRequest('date_to'));
	$sales_type  = getRequest('sales_type'); 
	$opening_balance=0; $ob_dr=0; $ob_cr=0;
	if(($from_date=="") || ($from_date=="2014/01/01")){
		//======== Total Dr OB ======
		$dsql= "SELECT SUM(opening_balance) AS ob_dr FROM ".CUSTOMER_OB_VIEW." WHERE trt_id='$trt_id' AND project_id='$project_id' AND op_type='Dr'";
		if($sales_type!=""){
			$dsql.=" AND sales_type = '$sales_type'";
		}
		$dres = mysql_query($dsql);
		$dnum = mysql_num_rows($dres);
		if($dnum>0){
		$drow = mysql_fetch_object($dres);
		$ob_dr = $drow->ob_dr;
		} 
		//======== Total Cr OB ======
		$csql= "SELECT SUM(opening_balance) AS ob_cr FROM ".CUSTOMER_OB_VIEW." WHERE trt_id='$trt_id' AND project_id='$project_id' AND op_type='Cr'";
		if($sales_type!=""){
			$csql.=" AND sales_type = '$sales_type'";
		}
		$cres = mysql_query($csql);
		$cnum = mysql_num_rows($cres);
		if($cnum>0){
		$crow = mysql_fetch_object($cres);
		$ob_cr = $crow->ob_cr;
		} 
		$opening_balance = ($ob_dr-$ob_cr);			
	}else{
		$Psql= "SELECT (SUM(dr) - SUM(cr) )AS customer_ob FROM ".CUSTOMER_LEDGER_VIEW." WHERE trt_id='$trt_id' AND project_id='$project_id'";
		if($from_date!=""){
			$Psql.=" AND created_date < '$from_date'";
		}
		if($sales_type!=""){
			$Psql.=" AND sales_type = '$sales_type'";
		} 
		$pres = mysql_query($Psql);
		$cnum = mysql_num_rows($pres);
		if($cnum>0){
		$prow = mysql_fetch_object($pres);
		$opening_balance = $prow->customer_ob;
		}
	}
	return $opening_balance; 
}
function getTotalSupplierTRTOB($trt_id,$project_id){	 
	$from_date 	 = formatDate(getRequest('date_from'));
	$to_date 	 = formatDate(getRequest('date_to'));
	$sales_type  = getRequest('sales_type'); 
	$opening_balance=0; $ob_dr=0; $ob_cr=0;
	if(($from_date=="") || ($from_date=="2014/01/01")){
		//======== Total Dr OB ======
		$dsql= "SELECT SUM(opening_balance) AS ob_dr FROM ".SUPPLIER_OB_VIEW." WHERE trt_id='$trt_id' AND project_id='$project_id' AND op_type='Dr'";
		if($sales_type!=""){
			$dsql.=" AND sales_type = '$sales_type'";
		}
		$dres = mysql_query($dsql);
		$dnum = mysql_num_rows($dres);
		if($dnum>0){
		$drow = mysql_fetch_object($dres);
		$ob_dr = $drow->ob_dr;
		} 
		//======== Total Cr OB ======
		$csql= "SELECT SUM(opening_balance) AS ob_cr FROM ".SUPPLIER_OB_VIEW." WHERE trt_id='$trt_id' AND project_id='$project_id' AND op_type='Cr'";
		if($sales_type!=""){
			$csql.=" AND sales_type = '$sales_type'";
		}
		$cres = mysql_query($csql);
		$cnum = mysql_num_rows($cres);
		if($cnum>0){
		$crow = mysql_fetch_object($cres);
		$ob_cr = $crow->ob_cr;
		} 
		$opening_balance = ($ob_dr-$ob_cr);			
	}else{
		$Psql= "SELECT (SUM(dr) - SUM(cr) )AS customer_ob FROM ".SUPPLIER_LEDGER_VIEW." WHERE trt_id='$trt_id' AND project_id='$project_id'";
		if($from_date!=""){
			$Psql.=" AND created_date < '$from_date'";
		}
		if($sales_type!=""){
			$Psql.=" AND sales_type = '$sales_type'";
		} 
		$pres = mysql_query($Psql);
		$cnum = mysql_num_rows($pres);
		if($cnum>0){
		$prow = mysql_fetch_object($pres);
		$opening_balance = $prow->customer_ob;
		}
	}
	return $opening_balance; 
}
function getTotalTRTSalesAmount($trt_id,$project_id){	 
	$from_date 	 = formatDate(getRequest('date_from'));
	$to_date 	 = formatDate(getRequest('date_to'));
	$sales_type  	 = getRequest('sales_type'); 
	$odd_date 	 = getRequest('odd_date'); 
	$minus_odd_date  = getRequest('minus_odd_date'); 
	if($odd_date !="" || $minus_odd_date !=""){ $sales_date="sales_date";}else{$sales_date="delivery_date";}
	$sales_amount=0; 	
	$Psql= "SELECT SUM(sales_amount) AS sales_amount FROM ".CUSTOMER_SALES_LEDGER_VIEW." WHERE trt_id='$trt_id' AND project_id='$project_id'";	
	if($sales_type!=""){
		$Psql.=" AND sales_type = '$sales_type'";
	}
	if($from_date!="" && $to_date ==""){
		$Psql.=" AND $sales_date >= '$from_date'";
	}elseif($from_date=="" && $to_date !=""){
		$Psql.=" AND $sales_date <= '$to_date'";
	}elseif($from_date!="" && $to_date !=""){
		$Psql.=" AND $sales_date BETWEEN '$from_date' AND '$to_date'";
	}
	if($minus_odd_date!=""){
		$Psql.=" AND sales_date = value_date"; 
	}
	$pres = mysql_query($Psql); 
	$cnum = mysql_num_rows($pres);
	if($cnum>0){
	$prow = mysql_fetch_object($pres);
	$sales_amount = $prow->sales_amount;
	}
	// SELECT SUM( dr ) AS sales FROM `vw_customer_ledger` WHERE `area_id` =1 AND `description` = "OS" 	
	return $sales_amount; 
}

function getTotalSupplierTRTSalesAmount($trt_id,$project_id){	 
	$from_date 	 = formatDate(getRequest('date_from'));
	$to_date 	 = formatDate(getRequest('date_to'));
	$sales_type  	 = getRequest('sales_type'); 
	 
	$odd_date 	 = getRequest('odd_date'); 
	$minus_odd_date  = getRequest('minus_odd_date'); 
	if($odd_date !="" || $minus_odd_date !=""){ $sales_date="sales_date";}else{$sales_date="delivery_date";}
	$sales_amount=0; 	
	$Psql= "SELECT SUM(sales_amount) AS sales_amount FROM ".SUPPLIER_SALES_LEDGER_VIEW." WHERE trt_id='$trt_id' AND project_id='$project_id'";	
	if($sales_type!=""){
		$Psql.=" AND sales_type = '$sales_type'";
	}
	if($from_date!="" && $to_date ==""){
		$Psql.=" AND $sales_date >= '$from_date'";
	}elseif($from_date=="" && $to_date !=""){
		$Psql.=" AND $sales_date <= '$to_date'";
	}elseif($from_date!="" && $to_date !=""){
		$Psql.=" AND $sales_date BETWEEN '$from_date' AND '$to_date'";
	}
	if($minus_odd_date!=""){
		$Psql.=" AND sales_date = value_date";
	}
	$pres = mysql_query($Psql); 
	$cnum = mysql_num_rows($pres);
	if($cnum>0){
	$prow = mysql_fetch_object($pres);
	$sales_amount = $prow->sales_amount;
	}
	// SELECT SUM( dr ) AS sales FROM `vw_customer_ledger` WHERE `area_id` =1 AND `description` = "OS" 	
	return $sales_amount; 
}
function getTotalTRTReceiptAmount($trt_id,$project_id){	 
	$from_date 	 = formatDate(getRequest('date_from'));
	$to_date 	 = formatDate(getRequest('date_to'));
	$sales_type  	 = getRequest('sales_type'); 	    
	$issue_date 	= getRequest('issue_date');	
	if($issue_date!=""){$created_date="issue_date";}else{$created_date="created_date";} 
	$receipt_amount=0; 	$return_amount=0;
	//======= Get Total Receipt Amount =========
	$Psql= "SELECT SUM(cr) AS receipt_amount FROM ".CUSTOMER_LEDGER_VIEW." WHERE trt_id='$trt_id' AND project_id='$project_id' AND cr >0 AND description!='OB' AND adjustment =0 AND beddebts =0";	
	if($sales_type!=""){
		$Psql.=" AND sales_type = '$sales_type'";
	}
	if($from_date!="" && $to_date ==""){
		$Psql.=" AND $created_date >= '$from_date'";
	}elseif($from_date=="" && $to_date !=""){
		$Psql.=" AND $created_date <= '$to_date'";
	}elseif($from_date!="" && $to_date !=""){
		$Psql.=" AND $created_date BETWEEN '$from_date' AND '$to_date'";
	}
	$pres = mysql_query($Psql);
	$cnum = mysql_num_rows($pres);
	if($cnum>0){
	$prow = mysql_fetch_object($pres);
	$receipt_amount = $prow->receipt_amount;
	}else{ $receipt_amount =0; }
	//======= Get Total Return Amount =========
	$Rsql= "SELECT SUM(return_amount) AS return_amount FROM ".CUSTOMER_SALES_RETURN_DETAILS_VIEW." WHERE trt_id='$trt_id' AND project_id='$project_id'";
		
	if($sales_type!=""){
		$Rsql.=" AND sales_type = '$sales_type'";
	}
	if($from_date!="" && $to_date ==""){
		$Rsql.=" AND return_date >= '$from_date'";
	}elseif($from_date=="" && $to_date !=""){
		$Rsql.=" AND return_date <= '$to_date'";
	}elseif($from_date!="" && $to_date !=""){
		$Rsql.=" AND return_date BETWEEN '$from_date' AND '$to_date'";
	}
	$rres = mysql_query($Rsql);
	$rnum = mysql_num_rows($rres);
	if($rnum>0){
	$rrow = mysql_fetch_object($rres);
	$return_amount = $rrow->return_amount;
	}else{ $return_amount =0; }
	$receipt_amount = $receipt_amount - $return_amount;
	if($receipt_amount<0){ $receipt_amount=0;}
	
	return $receipt_amount; 
}
//==== getTotalTRTAdjustAmount ====
function getTotalTRTAdjustAmount($trt_id,$project_id){	 
	$from_date 	 = formatDate(getRequest('date_from'));
	$to_date 	 = formatDate(getRequest('date_to')); 
	$sales_type  	 = getRequest('sales_type');
	
	$receipt_amount=0; 	$return_amount=0;
	//======= Get Total Receipt Amount =========
	$Psql= "SELECT SUM(cr) AS receipt_amount FROM ".CUSTOMER_LEDGER_VIEW." WHERE trt_id='$trt_id' AND project_id='$project_id' AND cr >0 AND description!='OB' AND adjustment =1 AND beddebts =0";	
	if($sales_type!=""){
		$Psql.=" AND sales_type = '$sales_type'";
	}
	if($from_date!="" && $to_date ==""){
		$Psql.=" AND created_date >= '$from_date'";
	}elseif($from_date=="" && $to_date !=""){
		$Psql.=" AND created_date <= '$to_date'";
	}elseif($from_date!="" && $to_date !=""){
		$Psql.=" AND created_date BETWEEN '$from_date' AND '$to_date'";
	}
	$pres = mysql_query($Psql);
	$cnum = mysql_num_rows($pres);
	if($cnum>0){
	$prow = mysql_fetch_object($pres);
	$receipt_amount = $prow->receipt_amount;
	}else{ $receipt_amount =0; }
	
	if($receipt_amount<0){ $receipt_amount=0;}
	
	return $receipt_amount; 
}
function getTotalSupplierTRTReceiptAmount($trt_id,$project_id){	 
	$from_date 	 = formatDate(getRequest('date_from'));
	$to_date 	 = formatDate(getRequest('date_to'));
	$sales_type  	 = getRequest('sales_type');   
	$issue_date 	 = getRequest('issue_date');	
	if($issue_date!=""){$created_date="issue_date";}else{$created_date="created_date";} 
	$receipt_amount=0; 	$return_amount=0;
	//======= Get Total Receipt Amount =========
	$Psql= "SELECT SUM(cr) AS receipt_amount FROM ".SUPPLIER_LEDGER_VIEW." WHERE trt_id='$trt_id' AND project_id='$project_id' AND cr >0 AND description!='OB' AND adjustment =0 AND beddebts =0";	
	if($sales_type!=""){
		$Psql.=" AND sales_type = '$sales_type'";
	}
	if($from_date!="" && $to_date ==""){
		$Psql.=" AND $created_date >= '$from_date'";
	}elseif($from_date=="" && $to_date !=""){
		$Psql.=" AND $created_date <= '$to_date'";
	}elseif($from_date!="" && $to_date !=""){
		$Psql.=" AND $created_date BETWEEN '$from_date' AND '$to_date'";
	}
	$pres = mysql_query($Psql);
	$cnum = mysql_num_rows($pres);
	if($cnum>0){
	$prow = mysql_fetch_object($pres);
	$receipt_amount = $prow->receipt_amount;
	}else{ $receipt_amount =0; }
	//======= Get Total Return Amount =========
	$Rsql= "SELECT SUM(return_amount) AS return_amount FROM ".SUPPLIER_SALES_RETURN_DETAILS_VIEW." WHERE trt_id='$trt_id' AND project_id='$project_id'";
		
	if($sales_type!=""){
		$Rsql.=" AND sales_type = '$sales_type'";
	}
	if($from_date!="" && $to_date ==""){
		$Rsql.=" AND return_date >= '$from_date'";
	}elseif($from_date=="" && $to_date !=""){
		$Rsql.=" AND return_date <= '$to_date'";
	}elseif($from_date!="" && $to_date !=""){
		$Rsql.=" AND return_date BETWEEN '$from_date' AND '$to_date'";
	}
	$rres = mysql_query($Rsql);
	$rnum = mysql_num_rows($rres);
	if($rnum>0){
	$rrow = mysql_fetch_object($rres);
	$return_amount = $rrow->return_amount;
	}else{ $return_amount =0; }
	$receipt_amount = $receipt_amount - $return_amount;
	if($receipt_amount<0){ $receipt_amount=0;}
	
	return $receipt_amount; 
}
function getTotalSupplierTRTAdjustAmount($trt_id,$project_id){	 
	$from_date 	 = formatDate(getRequest('date_from'));
	$to_date 	 = formatDate(getRequest('date_to'));
	$sales_type  	 = getRequest('sales_type');
	$receipt_amount=0; 
	//======= Get Total Receipt Amount =========
	$Psql= "SELECT SUM(cr) AS receipt_amount FROM ".SUPPLIER_LEDGER_VIEW." WHERE trt_id='$trt_id' AND project_id='$project_id' AND cr >0 AND description!='OB' AND adjustment =1 AND beddebts =0";	
	if($sales_type!=""){
		$Psql.=" AND sales_type = '$sales_type'";
	}
	if($from_date!="" && $to_date ==""){
		$Psql.=" AND created_date >= '$from_date'";
	}elseif($from_date=="" && $to_date !=""){
		$Psql.=" AND created_date <= '$to_date'";
	}elseif($from_date!="" && $to_date !=""){
		$Psql.=" AND created_date BETWEEN '$from_date' AND '$to_date'";
	}
	$pres = mysql_query($Psql);
	$cnum = mysql_num_rows($pres);
	if($cnum>0){
	$prow = mysql_fetch_object($pres);
	$receipt_amount = $prow->receipt_amount;
	}else{ $receipt_amount =0; }
		
	return $receipt_amount; 
}
function getTotalTRTBedDebtAmount($trt_id,$project_id){	 
	$from_date 	 = formatDate(getRequest('date_from'));
	$to_date 	 = formatDate(getRequest('date_to'));
	$sales_type  	 = getRequest('sales_type');
	$BedDebtAmount=0;
	//======= Get Total BedDebt Amount =========
	$Psql= "SELECT SUM(return_amount) AS BedDebtAmount FROM ".CUSTOMER_SALES_RETURN_DETAILS_VIEW." WHERE trt_id='$trt_id' AND project_id='$project_id' AND product_status='Yes' ";	
	if($sales_type!=""){
		$Psql.=" AND sales_type = '$sales_type'";
	}
	if($from_date!="" && $to_date ==""){
		$Psql.=" AND return_date >= '$from_date'";
	}elseif($from_date=="" && $to_date !=""){
		$Psql.=" AND return_date <= '$to_date'";
	}elseif($from_date!="" && $to_date !=""){
		$Psql.=" AND return_date BETWEEN '$from_date' AND '$to_date'";
	}
	$pres = mysql_query($Psql); //echo $Psql;
	$cnum = mysql_num_rows($pres);
	if($cnum>0){
	$prow = mysql_fetch_object($pres);
	$BedDebtAmount = $prow->BedDebtAmount;
	}
	//=== Get Bedbebts from Ledger ===	
	$Psql= "SELECT SUM(cr) AS bedbebts_amount FROM ".CUSTOMER_LEDGER_VIEW." WHERE trt_id='$trt_id' AND project_id='$project_id' AND cr >0 AND description!='OB' AND adjustment =0 AND beddebts =1";	
	if($sales_type!=""){
		$Psql.=" AND sales_type = '$sales_type'";
	}
	if($from_date!="" && $to_date ==""){
		$Psql.=" AND created_date >= '$from_date'";
	}elseif($from_date=="" && $to_date !=""){
		$Psql.=" AND created_date <= '$to_date'";
	}elseif($from_date!="" && $to_date !=""){
		$Psql.=" AND created_date BETWEEN '$from_date' AND '$to_date'";
	}
	$pres = mysql_query($Psql);
	$cnum = mysql_num_rows($pres);
	if($cnum >0){
	$prow = mysql_fetch_object($pres);
	$bedbebts_amount = $prow->bedbebts_amount;
	}else{ $bedbebts_amount =0; }
	$BedDebtAmount +=$bedbebts_amount;
		
	return $BedDebtAmount; 
}

function getTotalSupplierTRTBedDebtAmount($trt_id,$project_id){	 
	$from_date 	= formatDate(getRequest('date_from'));
	$to_date 	= formatDate(getRequest('date_to'));
	$sales_type  	= getRequest('sales_type'); 
	$BedDebtAmount=0;
	//======= Get Total BedDebt Amount =========
	$Psql= "SELECT SUM(return_amount) AS BedDebtAmount FROM ".SUPPLIER_SALES_RETURN_DETAILS_VIEW." WHERE trt_id='$trt_id' AND project_id='$project_id' AND product_status='Yes' ";	
	if($sales_type!=""){
		$Psql.=" AND sales_type = '$sales_type'";
	}
	if($from_date!="" && $to_date ==""){
		$Psql.=" AND return_date >= '$from_date'";
	}elseif($from_date=="" && $to_date !=""){
		$Psql.=" AND return_date <= '$to_date'";
	}elseif($from_date!="" && $to_date !=""){
		$Psql.=" AND return_date BETWEEN '$from_date' AND '$to_date'";
	}
	$pres = mysql_query($Psql);
	$cnum = mysql_num_rows($pres);
	if($cnum>0){
	$prow = mysql_fetch_object($pres);
	$BedDebtAmount = $prow->BedDebtAmount;
	}	
	return $BedDebtAmount; 
}
function getTotalTRTSalesReturnAmount($trt_id,$project_id){	 
	$from_date 	 = formatDate(getRequest('date_from'));
	$to_date 	 = formatDate(getRequest('date_to'));
	$sales_type  	 = getRequest('sales_type'); 
	$SalesReturn=0;
	//======= Get Total BedDebt Amount =========
	$Psql= "SELECT SUM(return_amount) AS SalesReturn FROM ".CUSTOMER_SALES_RETURN_DETAILS_VIEW." WHERE trt_id='$trt_id' AND project_id='$project_id' 
	AND product_status='No' ";	
	if($sales_type!=""){
		$Psql.=" AND sales_type = '$sales_type'";
	}
	if($from_date!="" && $to_date ==""){
		$Psql.=" AND return_date >= '$from_date'";
	}elseif($from_date=="" && $to_date !=""){
		$Psql.=" AND return_date <= '$to_date'";
	}elseif($from_date!="" && $to_date !=""){
		$Psql.=" AND return_date BETWEEN '$from_date' AND '$to_date'";
	}
	$pres = mysql_query($Psql); //echo $Psql;
	$cnum = mysql_num_rows($pres);
	if($cnum>0){
	$prow = mysql_fetch_object($pres);
	$SalesReturn = $prow->SalesReturn;
	}	
	return $SalesReturn; 
}

function getTotalSupplierTRTSalesReturnAmount($trt_id,$project_id){	 
	$from_date 	 = formatDate(getRequest('date_from'));
	$to_date 	 = formatDate(getRequest('date_to'));
	$sales_type  = getRequest('sales_type'); 
	$SalesReturn=0;
	//======= Get Total BedDebt Amount =========
	$Psql= "SELECT SUM(return_amount) AS SalesReturn FROM ".SUPPLIER_SALES_RETURN_DETAILS_VIEW." WHERE trt_id='$trt_id' AND project_id='$project_id' 
	AND product_status='No' ";	
	if($sales_type!=""){
		$Psql.=" AND sales_type = '$sales_type'";
	}
	if($from_date!="" && $to_date ==""){
		$Psql.=" AND return_date >= '$from_date'";
	}elseif($from_date=="" && $to_date !=""){
		$Psql.=" AND return_date <= '$to_date'";
	}elseif($from_date!="" && $to_date !=""){
		$Psql.=" AND return_date BETWEEN '$from_date' AND '$to_date'";
	}
	$pres = mysql_query($Psql);
	$cnum = mysql_num_rows($pres);
	if($cnum>0){
	$prow = mysql_fetch_object($pres);
	$SalesReturn = $prow->SalesReturn;
	}	
	return $SalesReturn; 
}
function getTotalTRTClosingAmount($trt_id,$project_id){	 
	$from_date 	 = formatDate(getRequest('date_from'));
	$to_date 	 = formatDate(getRequest('date_to'));
	$sales_type  = getRequest('sales_type'); 
	$ClosingBalance=0;
	//======= Get Total BedDebt Amount =========
	$Psql= "SELECT (SUM(dr)-SUM(cr)) AS ClosingBalance FROM ".CUSTOMER_LEDGER_VIEW." WHERE trt_id='$trt_id' AND project_id='$project_id' ";	
	if($sales_type!=""){
		$Psql.=" AND sales_type = '$sales_type'";
	}
	if($to_date !=""){
		$Psql.=" AND created_date <= '$to_date'";
	}
	$pres = mysql_query($Psql);
	$cnum = mysql_num_rows($pres);
	if($cnum>0){
	$prow = mysql_fetch_object($pres);
	$ClosingBalance = $prow->ClosingBalance;
	}	
	return $ClosingBalance; 
}
function getTotalSupplierTRTClosingAmount($trt_id,$project_id){	 
	$from_date 	 = formatDate(getRequest('date_from'));
	$to_date 	 = formatDate(getRequest('date_to'));
	$sales_type  = getRequest('sales_type'); 
	$ClosingBalance=0;
	//======= Get Total BedDebt Amount =========
	$Psql= "SELECT (SUM(dr)-SUM(cr)) AS ClosingBalance FROM ".SUPPLIER_LEDGER_VIEW." WHERE trt_id='$trt_id' AND project_id='$project_id' ";	
	if($sales_type!=""){
		$Psql.=" AND sales_type = '$sales_type'";
	}
	if($to_date !=""){
		$Psql.=" AND created_date <= '$to_date'";
	}
	$pres = mysql_query($Psql);
	$cnum = mysql_num_rows($pres);
	if($cnum>0){
	$prow = mysql_fetch_object($pres);
	$ClosingBalance = $prow->ClosingBalance;
	}	
	return $ClosingBalance; 
}

//===== All Customer Function =======
function getCustomerTotalOB($customer_id,$project_id){	 
	$from_date 	 = formatDate(getRequest('date_from'));
	$to_date 	 = formatDate(getRequest('date_to'));
	$sales_type  = getRequest('sales_type'); 
	$opening_balance=0;
	if(($from_date=="") || ($from_date=="2014/01/01")){
		//======== Total Dr OB ======
		$dsql= "SELECT SUM(opening_balance) AS opening_balance, op_type FROM ".CUSTOMER_OB_VIEW." WHERE sub_id='$customer_id' AND project_id='$project_id'";
		if($sales_type!=""){
			$dsql.=" AND sales_type = '$sales_type'";
		}
		$dres = mysql_query($dsql);
		$dnum = mysql_num_rows($dres);
		if($dnum>0){
			$drow = mysql_fetch_object($dres);
			if($drow->op_type=="Cr"){
			$opening_balance = "-".$drow->opening_balance;
			}else{
			$opening_balance = $drow->opening_balance;
			}
		}						
	}else{
		$Psql= "SELECT (SUM(dr) - SUM(cr) )AS opening_balance FROM ".CUSTOMER_LEDGER_VIEW." WHERE sub_id='$customer_id' AND project_id='$project_id'";
		if($from_date!=""){
			$Psql.=" AND created_date < '$from_date'";
		}
		if($sales_type!=""){
			$Psql.=" AND sales_type = '$sales_type'";
		}
		$pres = mysql_query($Psql);
		$cnum = mysql_num_rows($pres); 
		if($cnum>0){
		$prow = mysql_fetch_object($pres);
		$opening_balance = $prow->opening_balance;
		}
	}
	return $opening_balance; 
}
function getSupplierTotalOB($customer_id,$project_id){	 
	$from_date 	 = formatDate(getRequest('date_from'));
	$to_date 	 = formatDate(getRequest('date_to'));
	$sales_type  = getRequest('sales_type'); 
	$opening_balance=0;
	if(($from_date=="") || ($from_date=="2014/01/01")){
		//======== Total Dr OB ======
		$dsql= "SELECT SUM(opening_balance) AS opening_balance, op_type FROM ".SUPPLIER_OB_VIEW." WHERE sub_id='$customer_id' AND project_id='$project_id'";
		if($sales_type!=""){
			$dsql.=" AND sales_type = '$sales_type'";
		}
		$dres = mysql_query($dsql);
		$dnum = mysql_num_rows($dres);
		if($dnum>0){
			$drow = mysql_fetch_object($dres);
			if($drow->op_type=="Cr"){
			$opening_balance = "-".$drow->opening_balance;
			}else{
			$opening_balance = $drow->opening_balance;
			}
		}						
	}else{
		$Psql= "SELECT (SUM(dr) - SUM(cr) )AS opening_balance FROM ".SUPPLIER_LEDGER_VIEW." WHERE sub_id='$customer_id' AND project_id='$project_id'";
		if($from_date!=""){
			$Psql.=" AND created_date < '$from_date'";
		}
		if($sales_type!=""){
			$Psql.=" AND sales_type = '$sales_type'";
		}
		$pres = mysql_query($Psql);
		$cnum = mysql_num_rows($pres); 
		if($cnum>0){
		$prow = mysql_fetch_object($pres);
		$opening_balance = $prow->opening_balance;
		}
	}
	return $opening_balance; 
}
function getCustomerTotalSalesAmount($customer_id,$project_id){	 
	$from_date 	 = formatDate(getRequest('date_from'));
	$to_date 	 = formatDate(getRequest('date_to'));
	$sales_type  	 = getRequest('sales_type'); 
	 
	$odd_date 	 = getRequest('odd_date'); 
	$minus_odd_date  = getRequest('minus_odd_date'); 
	if($odd_date !="" || $minus_odd_date !=""){ $sales_date="sales_date";}else{$sales_date="delivery_date";}
	$sales_amount=0;
	$Psql= "SELECT SUM(sales_amount) AS sales_amount FROM ".CUSTOMER_SALES_LEDGER_VIEW." WHERE `sub_id`='$customer_id' AND project_id='$project_id' ";
	if($sales_type!=""){
		$Psql.=" AND sales_type = '$sales_type'";
	}
	if($from_date!="" && $to_date ==""){
		$Psql.=" AND $sales_date >= '$from_date'";
	}elseif($from_date=="" && $to_date !=""){
		$Psql.=" AND $sales_date <= '$to_date'";
	}elseif($from_date!="" && $to_date !=""){
		$Psql.=" AND $sales_date BETWEEN '$from_date' AND '$to_date'";
	}
	if($minus_odd_date!=""){
		$Psql.=" AND sales_date = value_date";
	}
	$pres = mysql_query($Psql); 
	if($customer_id=="A001269"){
	//echo $Psql;
	}
	$cnum = mysql_num_rows($pres);
	if($cnum>0){
	$prow = mysql_fetch_object($pres);
	$sales_amount = $prow->sales_amount;
	}		
	return $sales_amount;
}

function getSupplierTotalSalesAmount($customer_id,$project_id){	 
	$from_date 	 = formatDate(getRequest('date_from'));
	$to_date 	 = formatDate(getRequest('date_to'));
	$sales_type   	 = getRequest('sales_type'); 
	 
	$odd_date 	 = getRequest('odd_date'); 
	$minus_odd_date  = getRequest('minus_odd_date'); 
	if($odd_date !="" || $minus_odd_date!=""){ $sales_date="sales_date";}else{$sales_date="delivery_date";}  
	$sales_amount=0;
	$Psql= "SELECT SUM(sales_amount) AS sales_amount FROM ".SUPPLIER_SALES_LEDGER_VIEW." WHERE `sub_id`='$customer_id' AND project_id='$project_id' ";
	if($sales_type!=""){
		$Psql.=" AND sales_type = '$sales_type'";
	}
	if($from_date!="" && $to_date ==""){
		$Psql.=" AND $sales_date >= '$from_date'";
	}elseif($from_date=="" && $to_date !=""){
		$Psql.=" AND $sales_date <= '$to_date'";
	}elseif($from_date!="" && $to_date !=""){
		$Psql.=" AND $sales_date BETWEEN '$from_date' AND '$to_date'";
	}
	if($minus_odd_date!=""){
		$Psql.=" AND sales_date = value_date";
	}
	$pres = mysql_query($Psql);
	$cnum = mysql_num_rows($pres);
	if($cnum>0){
	$prow = mysql_fetch_object($pres);
	$sales_amount = $prow->sales_amount;
	}		
	return $sales_amount;
}
function getCustomerTotalReceipt($customer_id,$project_id){	 
	$from_date 	 = formatDate(getRequest('date_from'));
	$to_date 	 = formatDate(getRequest('date_to'));
	$sales_type  	 = getRequest('sales_type');     
	$issue_date 	 = getRequest('issue_date');	
	if($issue_date!=""){$created_date="issue_date";}else{$created_date="created_date";}  
	$receipt_amount=0;
	$Psql= "SELECT SUM(cr) AS receipt_amount FROM ".ACCOUNT_JOURNAL_TBL." WHERE sub_id='$customer_id' AND project_id='$project_id' AND cr >0 AND description!='OB' AND adjustment =0 AND beddebts =0";
	
	if($from_date!="" && $to_date ==""){
		$Psql.=" AND $created_date >= '$from_date'";
	}elseif($from_date=="" && $to_date !=""){
		$Psql.=" AND $created_date <= '$to_date'";
	}elseif($from_date!="" && $to_date !=""){
		$Psql.=" AND $created_date BETWEEN '$from_date' AND '$to_date'";
	}
	$pres = mysql_query($Psql);
	$cnum = mysql_num_rows($pres);
	if($cnum>0){
	$prow = mysql_fetch_object($pres);
	$receipt_amount = $prow->receipt_amount;
	}
	
	//====== Total Return Amount =======
	$Rsql= "SELECT SUM(net_payble) AS return_amount FROM ".SALES_RETURN_MASTER_TBL." WHERE customer='$customer_id' AND project_id='$project_id' ";
	
	if($from_date!="" && $to_date ==""){
		$Rsql.=" AND return_date >= '$from_date'";
	}elseif($from_date=="" && $to_date !=""){
		$Rsql.=" AND return_date <= '$to_date'";
	}elseif($from_date!="" && $to_date !=""){
		$Rsql.=" AND return_date BETWEEN '$from_date' AND '$to_date'";
	}
	$rres = mysql_query($Rsql);
	$rnum = mysql_num_rows($rres);
	if($rnum>0){
	$rrow = mysql_fetch_object($rres);
	$return_amount = $rrow->return_amount;
	}else{ $return_amount = 0;}
	
	$receipt_amount = ($receipt_amount - $return_amount); 
	if($receipt_amount<0){ $receipt_amount =0;}
	
	return $receipt_amount;
}
//===== getCustomerTotalAdjust ====
function getCustomerTotalAdjust($customer_id,$project_id){	 
	$from_date 	= formatDate(getRequest('date_from'));
	$to_date 	= formatDate(getRequest('date_to'));
	$sales_type  	= getRequest('sales_type');
	$receipt_amount=0;
	$Psql= "SELECT SUM(cr) AS receipt_amount FROM ".ACCOUNT_JOURNAL_TBL." WHERE sub_id='$customer_id' AND project_id='$project_id' AND cr >0 AND description!='OB' AND adjustment =1 AND beddebts =0";
	if($from_date!="" && $to_date ==""){
		$Psql.=" AND created_date >= '$from_date'";
	}elseif($from_date=="" && $to_date !=""){
		$Psql.=" AND created_date <= '$to_date'";
	}elseif($from_date!="" && $to_date !=""){
		$Psql.=" AND created_date BETWEEN '$from_date' AND '$to_date'";
	}
	$pres = mysql_query($Psql);
	$cnum = mysql_num_rows($pres);
	if($cnum >0){
		$prow = mysql_fetch_object($pres);
		$receipt_amount = $prow->receipt_amount;
		$ReceivedType = " Cr";
	}
	
	if($receipt_amount ==0){
		$Psql= "SELECT SUM(dr) AS receipt_amount FROM ".ACCOUNT_JOURNAL_TBL." WHERE sub_id='$customer_id' AND project_id='$project_id' AND dr >0 AND description!='OB' AND adjustment =1 AND beddebts =0";
		if($from_date!="" && $to_date ==""){
			$Psql.=" AND created_date >= '$from_date'";
		}elseif($from_date=="" && $to_date !=""){
			$Psql.=" AND created_date <= '$to_date'";
		}elseif($from_date!="" && $to_date !=""){
			$Psql.=" AND created_date BETWEEN '$from_date' AND '$to_date'";
		}
		$pres = mysql_query($Psql);
		$cnum = mysql_num_rows($pres);
		if($cnum >0){
		$prow = mysql_fetch_object($pres);
		$receipt_amount = $prow->receipt_amount;
		$ReceivedType =" Dr";
		}
	}
	
	//====== Total Return Amount =======
	$Rsql= "SELECT SUM(net_payble) AS return_amount FROM ".SALES_RETURN_MASTER_TBL." WHERE customer='$customer_id' AND project_id='$project_id' ";
	if($from_date!="" && $to_date ==""){
		$Rsql.=" AND return_date >= '$from_date'";
	}elseif($from_date=="" && $to_date !=""){
		$Rsql.=" AND return_date <= '$to_date'";
	}elseif($from_date!="" && $to_date !=""){
		$Rsql.=" AND return_date BETWEEN '$from_date' AND '$to_date'";
	}
	$rres = mysql_query($Rsql);
	$rnum = mysql_num_rows($rres);
	if($rnum>0){
	$rrow = mysql_fetch_object($rres);
	$return_amount = $rrow->return_amount;
	}else{ $return_amount = 0;}
	
	$receipt_amount = ($receipt_amount - $return_amount); 
	if($receipt_amount<0){ $receipt_amount =0;}
	$receipt_amount = number_format($receipt_amount, 2, '.', '');
	$receivedAmount = $receipt_amount.$ReceivedType;
	return $receivedAmount;
}
function getSupplierTotalReceipt($customer_id,$project_id){	 
	$from_date 	 = formatDate(getRequest('date_from'));
	$to_date 	 = formatDate(getRequest('date_to'));
	     
	$issue_date 	 = getRequest('issue_date');	
	if($issue_date!=""){$created_date="issue_date";}else{$created_date="created_date";}  
	$receipt_amount=0;
	$Psql= "SELECT SUM(cr) AS receipt_amount FROM ".ACCOUNT_JOURNAL_TBL." WHERE sub_id='$customer_id' AND project_id='$project_id' AND cr >0 AND description!='OB' AND adjustment =0 AND beddebts =0";
	if($from_date!="" && $to_date ==""){
		$Psql.=" AND $created_date >= '$from_date'";
	}elseif($from_date=="" && $to_date !=""){
		$Psql.=" AND $created_date <= '$to_date'";
	}elseif($from_date!="" && $to_date !=""){
		$Psql.=" AND $created_date BETWEEN '$from_date' AND '$to_date'";
	}
	$pres = mysql_query($Psql);
	$cnum = mysql_num_rows($pres);
	if($cnum>0){
	$prow = mysql_fetch_object($pres);
	$receipt_amount = $prow->receipt_amount;
	}
	
	//====== Total Return Amount =======
	$Rsql= "SELECT SUM(net_payble) AS return_amount FROM ".SALES_RETURN_MASTER_TBL." WHERE customer='$customer_id' AND project_id='$project_id' ";
	if($from_date!="" && $to_date ==""){
		$Rsql.=" AND return_date >= '$from_date'";
	}elseif($from_date=="" && $to_date !=""){
		$Rsql.=" AND return_date <= '$to_date'";
	}elseif($from_date!="" && $to_date !=""){
		$Rsql.=" AND return_date BETWEEN '$from_date' AND '$to_date'";
	}
	$rres = mysql_query($Rsql);
	$rnum = mysql_num_rows($rres);
	if($rnum>0){
	$rrow = mysql_fetch_object($rres);
	$return_amount = $rrow->return_amount;
	}else{ $return_amount = 0;}
	
	$receipt_amount = ($receipt_amount - $return_amount); 
	if($receipt_amount<0){ $receipt_amount =0;}
	
	return $receipt_amount;
}
function getSupplierTotalAdjust($customer_id,$project_id){	 
	$from_date 	 = formatDate(getRequest('date_from'));
	$to_date 	 = formatDate(getRequest('date_to'));
	
	$receipt_amount=0;
	$Psql= "SELECT SUM(cr) AS receipt_amount FROM ".ACCOUNT_JOURNAL_TBL." WHERE sub_id='$customer_id' AND project_id='$project_id' AND cr >0 AND description!='OB' AND adjustment =1 AND beddebts =0";
	if($from_date!="" && $to_date ==""){
		$Psql.=" AND created_date >= '$from_date'";
	}elseif($from_date=="" && $to_date !=""){
		$Psql.=" AND created_date <= '$to_date'";
	}elseif($from_date!="" && $to_date !=""){
		$Psql.=" AND created_date BETWEEN '$from_date' AND '$to_date'";
	}
	$pres = mysql_query($Psql);
	$cnum = mysql_num_rows($pres);
	if($cnum>0){
	$prow = mysql_fetch_object($pres);
	$receipt_amount = $prow->receipt_amount;
	$ReceivedType   = " Cr";
	}
		
	if($receipt_amount ==0){
		$Psql= "SELECT SUM(dr) AS receipt_amount FROM ".ACCOUNT_JOURNAL_TBL." WHERE sub_id='$customer_id' AND project_id='$project_id' AND dr >0 AND description!='OB' AND adjustment =1 AND beddebts =0";
		if($from_date!="" && $to_date ==""){
			$Psql.=" AND created_date >= '$from_date'";
		}elseif($from_date=="" && $to_date !=""){
			$Psql.=" AND created_date <= '$to_date'";
		}elseif($from_date!="" && $to_date !=""){
			$Psql.=" AND created_date BETWEEN '$from_date' AND '$to_date'";
		}
		$pres = mysql_query($Psql);
		$cnum = mysql_num_rows($pres);
		if($cnum >0){
		$prow = mysql_fetch_object($pres);
		$receipt_amount = $prow->receipt_amount;
		$ReceivedType =" Dr";
		}
	}	
	
	//====== Total Return Amount =======
	$Rsql= "SELECT SUM(net_payble) AS return_amount FROM ".SALES_RETURN_MASTER_TBL." WHERE customer='$customer_id' AND project_id='$project_id' ";
	if($from_date!="" && $to_date ==""){
		$Rsql.=" AND return_date >= '$from_date'";
	}elseif($from_date=="" && $to_date !=""){
		$Rsql.=" AND return_date <= '$to_date'";
	}elseif($from_date!="" && $to_date !=""){
		$Rsql.=" AND return_date BETWEEN '$from_date' AND '$to_date'";
	}
	$rres = mysql_query($Rsql);
	$rnum = mysql_num_rows($rres);
	if($rnum>0){
	$rrow = mysql_fetch_object($rres);
	$return_amount = $rrow->return_amount;
	}else{ $return_amount = 0;}
		
	$receipt_amount = ($receipt_amount - $return_amount); 
	if($receipt_amount <0){ $receipt_amount =0;}
	$receipt_amount = number_format($receipt_amount, 2, '.', '');
	$receivedAmount = $receipt_amount.$ReceivedType;
	return $receivedAmount;
}

function getCustomerTotalSalesReturn($customer_id,$project_id){	 
	$from_date 	 = formatDate(getRequest('date_from'));
	$to_date 	 = formatDate(getRequest('date_to'));
	$SalesReturn=0;
	$Psql= "SELECT SUM(net_amount) AS SalesReturn FROM ".SALES_RETURN_TBL." WHERE customer_id='$customer_id' AND project_id='$project_id' AND product_status='No'";
	if($from_date!="" && $to_date ==""){
		$Psql.=" AND return_date >= '$from_date'";
	}elseif($from_date=="" && $to_date !=""){
		$Psql.=" AND return_date <= '$to_date'";
	}elseif($from_date!="" && $to_date !=""){
		$Psql.=" AND return_date BETWEEN '$from_date' AND '$to_date'";
	}
	$pres = mysql_query($Psql); //echo $Psql;
	$cnum = mysql_num_rows($pres);
	if($cnum>0){
	$prow = mysql_fetch_object($pres);
	$SalesReturn = $prow->SalesReturn;
	}
	return $SalesReturn;
}

function getSupplierTotalSalesReturn($customer_id,$project_id){	 
	$from_date 	 = formatDate(getRequest('date_from'));
	$to_date 	 = formatDate(getRequest('date_to'));
	$SalesReturn=0;
	$Psql= "SELECT SUM(net_amount) AS SalesReturn FROM ".SALES_RETURN_TBL." WHERE customer_id='$customer_id' AND project_id='$project_id' AND product_status='No'";
	if($from_date!="" && $to_date ==""){
		$Psql.=" AND return_date >= '$from_date'";
	}elseif($from_date=="" && $to_date !=""){
		$Psql.=" AND return_date <= '$to_date'";
	}elseif($from_date!="" && $to_date !=""){
		$Psql.=" AND return_date BETWEEN '$from_date' AND '$to_date'";
	}
	$pres = mysql_query($Psql);
	$cnum = mysql_num_rows($pres);
	if($cnum>0){
	$prow = mysql_fetch_object($pres);
	$SalesReturn = $prow->SalesReturn;
	}
	return $SalesReturn;
}
function getCustomerTotalBaddebts($customer_id,$project_id){	 
	$from_date 	 = formatDate(getRequest('date_from'));
	$to_date 	 = formatDate(getRequest('date_to'));
	$sales_type  = getRequest('sales_type'); 
	$baddebts=0;
	$Psql= "SELECT SUM(net_amount) AS baddebts FROM ".SALES_RETURN_TBL." WHERE customer_id='$customer_id' AND project_id='$project_id' AND product_status='Yes'";
	if($from_date!="" && $to_date ==""){
		$Psql.=" AND return_date >= '$from_date'";
	}elseif($from_date=="" && $to_date !=""){
		$Psql.=" AND return_date <= '$to_date'";
	}elseif($from_date!="" && $to_date !=""){
		$Psql.=" AND return_date BETWEEN '$from_date' AND '$to_date'";
	}
	$pres = mysql_query($Psql);
	$cnum = mysql_num_rows($pres);
	if($cnum>0){
	$prow = mysql_fetch_object($pres);
	$baddebts = $prow->baddebts;
	}
	//=== Get Bedbebts from Ledger ===	
	$Psql= "SELECT SUM(cr) AS bedbebts_amount FROM ".CUSTOMER_LEDGER_VIEW." WHERE sub_id='$customer_id' AND project_id='$project_id' AND cr >0 AND description!='OB' AND adjustment =0 AND beddebts =1";	
	if($sales_type!=""){
		$Psql.=" AND sales_type = '$sales_type'";
	}
	if($from_date!="" && $to_date ==""){
		$Psql.=" AND created_date >= '$from_date'";
	}elseif($from_date=="" && $to_date !=""){
		$Psql.=" AND created_date <= '$to_date'";
	}elseif($from_date!="" && $to_date !=""){
		$Psql.=" AND created_date BETWEEN '$from_date' AND '$to_date'";
	}
	$pres = mysql_query($Psql);
	$cnum = mysql_num_rows($pres);
	if($cnum >0){
	$prow = mysql_fetch_object($pres);
	$bedbebts_amount = $prow->bedbebts_amount;
	}else{ $bedbebts_amount =0; }
	$baddebts +=$bedbebts_amount;
	
	return $baddebts;
}

function getSupplierTotalBaddebts($customer_id,$project_id){	 
	$from_date 	 = formatDate(getRequest('date_from'));
	$to_date 	 = formatDate(getRequest('date_to'));
	$baddebts=0;
	$Psql= "SELECT SUM(net_amount) AS baddebts FROM ".SALES_RETURN_TBL." WHERE customer_id='$customer_id' AND project_id='$project_id' AND product_status='Yes'";
	if($from_date!="" && $to_date ==""){
		$Psql.=" AND return_date >= '$from_date'";
	}elseif($from_date=="" && $to_date !=""){
		$Psql.=" AND return_date <= '$to_date'";
	}elseif($from_date!="" && $to_date !=""){
		$Psql.=" AND return_date BETWEEN '$from_date' AND '$to_date'";
	}
	$pres = mysql_query($Psql);
	$cnum = mysql_num_rows($pres);
	if($cnum>0){
	$prow = mysql_fetch_object($pres);
	$baddebts = $prow->baddebts;
	}
	return $baddebts;
}
function getCustomerTotalClosingAmount($sub_id,$project_id){	 
	$from_date 	 = formatDate(getRequest('date_from'));
	$to_date 	 = formatDate(getRequest('date_to'));
	$sales_type  = getRequest('sales_type'); 
	$ClosingBalance=0;
	//======= Get Total BedDebt Amount =========
	$Psql= "SELECT (SUM(dr)-SUM(cr)) AS ClosingBalance FROM ".CUSTOMER_LEDGER_VIEW." WHERE sub_id='$sub_id' AND project_id='$project_id' ";	
	if($sales_type!=""){
		$Psql.=" AND sales_type = '$sales_type'";
	}
	if($to_date !=""){
		$Psql.=" AND created_date <= '$to_date'";
	}
	$pres = mysql_query($Psql); //echo $Psql."<br>";
	$cnum = mysql_num_rows($pres);
	if($cnum>0){
	$prow = mysql_fetch_object($pres);
	$ClosingBalance = $prow->ClosingBalance;
	}	
	return $ClosingBalance; 
}
function getSupplierTotalClosingAmount($sub_id,$project_id){	 
	$from_date 	 = formatDate(getRequest('date_from'));
	$to_date 	 = formatDate(getRequest('date_to'));
	$sales_type  = getRequest('sales_type'); 
	$ClosingBalance=0;
	//======= Get Total BedDebt Amount =========
	$Psql= "SELECT (SUM(dr)-SUM(cr)) AS ClosingBalance FROM ".SUPPLIER_LEDGER_VIEW." WHERE sub_id='$sub_id' AND project_id='$project_id' ";	
	if($sales_type!=""){
		$Psql.=" AND sales_type = '$sales_type'";
	}
	if($to_date !=""){
		$Psql.=" AND created_date <= '$to_date'";
	}
	$pres = mysql_query($Psql);
	$cnum = mysql_num_rows($pres);
	if($cnum>0){
	$prow = mysql_fetch_object($pres);
	$ClosingBalance = $prow->ClosingBalance;
	}	
	return $ClosingBalance; 
}

//===== All Customer Monthly Function =======

function getTotalMonthlyOB($project_id,$division,$area,$trt,$typeName,$partyId=NULL){
	$opening_balance=0; 
	
	if($partyId !=""){
		$Psql= "SELECT OB AS ob_amount FROM ".MONTHLY_CUSTOMER_STATUS_VIEW." WHERE project_id='$project_id' AND sub_id='$partyId'";
	}else{
		$Psql= "SELECT SUM(OB) AS ob_amount FROM ".MONTHLY_CUSTOMER_STATUS_VIEW." WHERE project_id='$project_id'";
		
		if($division !="" && $typeName =="Division"){
			$Psql.=" AND  division_id='$division' ";
		}
		elseif($area!="" && $typeName =="Area"){
			$Psql.=" AND area_id ='$area' ";
		}
		elseif($trt !="" && $typeName =="TRT"){
			$Psql.=" AND trt_id='$trt' ";
		}
	}		
	//echo $Psql;
	$pres = mysql_query($Psql);
	$cnum = mysql_num_rows($pres);
	if($cnum>0){
	$prow = mysql_fetch_object($pres);
	$opening_balance = $prow->ob_amount;
	}
	
	return $opening_balance; 
}
function getMonthlyAmount($project_id,$division,$area,$trt,$month_name,$type,$partyId=NULL){
	$total_amount  = 0; 
	
	if($partyId !="" ){
		$Psql= "SELECT $month_name AS total_amount FROM ".MONTHLY_CUSTOMER_STATUS_VIEW." WHERE project_id='$project_id' AND sub_id='$partyId'";
	}else{
		$Psql= "SELECT SUM($month_name) AS total_amount FROM ".MONTHLY_CUSTOMER_STATUS_VIEW." WHERE project_id='$project_id'";
		
		if($division !="" && $type =="Division"){
			$Psql.=" AND  division_id='$division' ";
		}
		elseif($area!="" && $type =="Area"){
			$Psql.=" AND area_id ='$area' ";
		}
		elseif($trt !="" && $type =="TRT"){
			$Psql.=" AND trt_id='$trt' ";
		}
	}		
	
	$pres = mysql_query($Psql);
	$cnum = mysql_num_rows($pres);
	if($cnum>0){
	$prow = mysql_fetch_object($pres);
	$total_amount = $prow->total_amount;
	}
	
	return $total_amount; 
}
function getTotalSalesOrderQtys($voucher_no=NULL,$product){	  
	$division 	= getRequest('division_id');  
	$district 	= getRequest('district');  
	$area 		= getRequest('area'); 		
	$from_date 	= formatDate(getRequest('date_from'));  
	$to_date 	= formatDate(getRequest('date_to'));
	$stock_id 	  = getRequest('delivery_point'); $order_qty = 0; 
	$Psql= "SELECT SUM(sd.qty) AS order_qty,SUM(sd.undelivery_qty) AS undelivery_qty FROM ".SALES_MASTER_TBL." as sm,".SALES_DETAILS_TBL." as sd 
	WHERE sm.voucher_no = sd.voucher_no AND sd.product='$product' AND sm.item_delivery_amount >0 AND sm.status=1 ";
	if($voucher_no!=""){$Psql.=" AND sd.voucher_no='$voucher_no'";}
	if($stock_id!=""){$Psql.=" AND sm.delivery_point='$stock_id'";}
	
	if($division!=""){
		$Psql.=" AND sm.division = '$division'";
	}
	if($district!=""){
		$Psql.=" AND sm.district = '$district'";
	}
	if($area!=""){
		$Psql.=" AND sm.area = '$area'";
	}
	if($from_date!="" && $to_date ==""){
		$Psql.=" AND sm.sales_date >= '$from_date'";
	}elseif($from_date=="" && $to_date !=""){
		$Psql.=" AND sm.sales_date <= '$to_date'";
	}elseif($from_date!="" && $to_date !=""){
		$Psql.=" AND sm.sales_date BETWEEN '$from_date' AND '$to_date'";
	}	
	$Psql.=" GROUP BY sd.product "; //echo $Psql;
	$pres = mysql_query($Psql);
	$prow = mysql_fetch_object($pres);
	$order_qty   = ($prow->order_qty+$prow->undelivery_qty);	
	if(intval($order_qty)==""){$order_qty=0;}
	return $order_qty;
}
function getTotalSalesDeliveryQty($voucher_no=NULL,$product){	  
	$division 	= getRequest('division_id');  
	$district 	= getRequest('district');  
	$area 		= getRequest('area');			
	$from_date 	= formatDate(getRequest('date_from'));  
	$to_date 	= formatDate(getRequest('date_to'));
	$stock_id 	= getRequest('delivery_point'); 		$delivery_qty	= 0; 
	$Psql= "SELECT SUM(dc.delivery_qty) AS delivery_qty FROM ".SALES_MASTER_TBL." as sm,".SALES_DELIVERY_MASTER_TBL." as sd,".SALES_DELIVERY_CHALLAN_TBL." as dc 
	WHERE sm.voucher_no = sd.voucher_no AND sd.voucher_no = dc.voucher_no AND dc.product='$product' ";
	if($voucher_no!=""){$Psql.=" AND dc.voucher_no='$voucher_no'";}
	if($stock_id!=""){$Psql.=" AND dc.delivery_point='$stock_id'";}	
	if($division!=""){
		$Psql.=" AND sm.division = '$division'";
	}
	if($district!=""){
		$Psql.=" AND sm.district = '$district'";
	}
	if($area!=""){
		$Psql.=" AND sm.area = '$area'";
	}
	if($from_date!="" && $to_date ==""){
		$Psql.=" AND sd.delivery_date >= '$from_date'";
	}elseif($from_date=="" && $to_date !=""){
		$Psql.=" AND sd.delivery_date <= '$to_date'";
	}elseif($from_date!="" && $to_date !=""){
		$Psql.=" AND sd.delivery_date BETWEEN '$from_date' AND '$to_date'";
	}	
	$Psql.=" GROUP BY dc.product "; 
	$pres = mysql_query($Psql);
	$prow = mysql_fetch_object($pres);
	$delivery_qty   = $prow->delivery_qty;	
	if(intval($delivery_qty)==""){$delivery_qty=0;}
	return $delivery_qty;
}
function getTotalSalesUnDeliveryQty($voucher_no=NULL,$product){	  
	$division 	= getRequest('division_id');  
	$district 	= getRequest('district');  
	$area 		= getRequest('area');			
	$from_date 	= formatDate(getRequest('date_from'));  
	$to_date 	= formatDate(getRequest('date_to'));
	$stock_id 	= getRequest('delivery_point'); $order_qty = 0; 
	$Psql= "SELECT SUM(sd.undelivery_qty) AS undelivery_qty FROM ".SALES_MASTER_TBL." as sm,".SALES_DETAILS_TBL." as sd 
	WHERE sm.voucher_no = sd.voucher_no AND sd.product='$product' AND sm.item_delivery_amount >0 AND sm.status=1 ";
	if($voucher_no!=""){$Psql.=" AND sd.voucher_no='$voucher_no'";}
	if($stock_id!=""){$Psql.=" AND sm.delivery_point='$stock_id'";}
	
	if($division!=""){
		$Psql.=" AND sm.division = '$division'";
	}
	if($district!=""){
		$Psql.=" AND sm.district = '$district'";
	}
	if($area!=""){
		$Psql.=" AND sm.area = '$area'";
	}
	if($from_date!="" && $to_date ==""){
		$Psql.=" AND sm.sales_date >= '$from_date'";
	}elseif($from_date=="" && $to_date !=""){
		$Psql.=" AND sm.sales_date <= '$to_date'";
	}elseif($from_date!="" && $to_date !=""){
		$Psql.=" AND sm.sales_date BETWEEN '$from_date' AND '$to_date'";
	}	
	$Psql.=" GROUP BY sd.product "; //echo $Psql;
	$pres = mysql_query($Psql);
	$prow = mysql_fetch_object($pres);
	$order_qty   = ($prow->undelivery_qty);	
	if(intval($order_qty)==""){$order_qty=0;}
	return $order_qty;
}
//====== Start Customer Sales Status by TRT =======

function getAreaPreviousSales($district_id,$project_id){	 
	$from_date 	 = formatDate(getRequest('date_from'));
	$to_date 	 = formatDate(getRequest('date_to'));
	$sales_type  = getRequest('sales_type');	
	$division_id = getRequest('division_id'); 
	$opening_balance=0; $free_qty=0;
	if($division_id == 8){
	$Psql= "SELECT SUM(delivery_qty) as delivery_qty, SUM(free_qty) AS free_qty FROM ".SUPPLIER_DELIVERY_SALES_STATUS_VIEW;
	}else{
	$Psql= "SELECT SUM(delivery_qty) as delivery_qty, SUM(free_qty) AS free_qty FROM ".CUSTOMER_DELIVERY_SALES_STATUS_VIEW;	
	}
	$Psql.=" WHERE area_id='$district_id' AND project_id='$project_id'";
	if($from_date!=""){
		$Psql.=" AND delivery_date < '$from_date'";
	}
	if($sales_type!=""){
		$Psql.=" AND sales_type = '$sales_type'";
	}
	$pres = mysql_query($Psql);
	$cnum = mysql_num_rows($pres);
	if($cnum>0){
	$prow = mysql_fetch_object($pres);
	$opening_balance = ($prow->delivery_qty+$prow->free_qty);
	}	
	return $opening_balance; 
}
function getAreaCurrentSales($district_id,$project_id){	 
	$from_date 	 = formatDate(getRequest('date_from'));
	$to_date 	 = formatDate(getRequest('date_to'));
	$sales_type  = getRequest('sales_type'); 	
	$division_id = getRequest('division_id'); 
	$current_balance=0; $free_qty=0;
	if($division_id == 8){
	$Psql= "SELECT SUM(delivery_qty) as delivery_qty, SUM(free_qty) AS free_qty FROM ".SUPPLIER_DELIVERY_SALES_STATUS_VIEW;
	}else{
	$Psql= "SELECT SUM(delivery_qty) as delivery_qty, SUM(free_qty) AS free_qty FROM ".CUSTOMER_DELIVERY_SALES_STATUS_VIEW;	
	}
	$Psql.=" WHERE area_id='$district_id' AND project_id='$project_id'";
	if($from_date!="" && $to_date!=""){
		$Psql.=" AND delivery_date BETWEEN '$from_date' AND '$to_date'";
	}
	if($sales_type!=""){
		$Psql.=" AND sales_type = '$sales_type'";
	}
	$pres = mysql_query($Psql);
	$cnum = mysql_num_rows($pres);
	if($cnum>0){
	$prow = mysql_fetch_object($pres);
	$current_balance = ($prow->delivery_qty+$prow->free_qty);
	}	
	return $current_balance; 
}
function getTotalAreaSales($district_id,$project_id){	 
	$from_date 	 = formatDate(getRequest('date_from'));
	$to_date 	 = formatDate(getRequest('date_to'));
	$sales_type  = getRequest('sales_type'); 
	$division_id = getRequest('division_id'); 
	$total_qty=0; $free_qty=0;
	if($division_id == 8){
	$Psql= "SELECT SUM(delivery_qty) as delivery_qty, SUM(free_qty) AS free_qty FROM ".SUPPLIER_DELIVERY_SALES_STATUS_VIEW;
	}else{
	$Psql= "SELECT SUM(delivery_qty) as delivery_qty, SUM(free_qty) AS free_qty FROM ".CUSTOMER_DELIVERY_SALES_STATUS_VIEW;	
	}
	$Psql.=" WHERE area_id='$district_id' AND project_id='$project_id'";
	if($to_date!=""){
		$Psql.=" AND delivery_date <= '$to_date'";
	}
	if($sales_type!=""){
		$Psql.=" AND sales_type = '$sales_type'";
	}
	$pres = mysql_query($Psql);
	$cnum = mysql_num_rows($pres);
	if($cnum >0){
		$prow = mysql_fetch_object($pres);
		$total_qty = ($prow->delivery_qty+$prow->free_qty);
	}	
	return $total_qty; 
}

function getTRTPreviousSales($trt_id,$project_id){	 
	$from_date 	 = formatDate(getRequest('date_from'));
	$to_date 	 = formatDate(getRequest('date_to'));
	$sales_type  = getRequest('sales_type');	
	$division_id = getRequest('division_id'); 
	$opening_balance=0; $free_qty=0; 
	if($division_id == 8){
	$Psql= "SELECT SUM(delivery_qty) as delivery_qty, SUM(free_qty) AS free_qty FROM ".SUPPLIER_DELIVERY_SALES_STATUS_VIEW;
	}else{
	$Psql= "SELECT SUM(delivery_qty) as delivery_qty, SUM(free_qty) AS free_qty FROM ".CUSTOMER_DELIVERY_SALES_STATUS_VIEW;	
	}
	$Psql.=" WHERE trt_id='$trt_id' AND project_id='$project_id'";
	if($from_date!=""){
		$Psql.=" AND delivery_date < '$from_date'";
	}
	if($sales_type!=""){
		$Psql.=" AND sales_type = '$sales_type'";
	}
	$pres = mysql_query($Psql);
	$cnum = mysql_num_rows($pres);
	if($cnum>0){
	$prow = mysql_fetch_object($pres);
	$opening_balance = ($prow->delivery_qty+$prow->free_qty);
	}	
	return $opening_balance; 
}
function getTRTCurrentSales($trt_id,$project_id){	 
	$from_date 	 = formatDate(getRequest('date_from'));
	$to_date 	 = formatDate(getRequest('date_to'));
	$sales_type  = getRequest('sales_type');	
	$division_id = getRequest('division_id'); 
	$current_balance=0; $free_qty=0;
	if($division_id == 8){
	$Psql= "SELECT SUM(delivery_qty) as delivery_qty, SUM(free_qty) AS free_qty FROM ".SUPPLIER_DELIVERY_SALES_STATUS_VIEW;
	}else{
	$Psql= "SELECT SUM(delivery_qty) as delivery_qty, SUM(free_qty) AS free_qty FROM ".CUSTOMER_DELIVERY_SALES_STATUS_VIEW;	
	}
	$Psql.=" WHERE trt_id='$trt_id' AND project_id='$project_id'";
	if($from_date!="" && $to_date!=""){
		$Psql.=" AND delivery_date BETWEEN '$from_date' AND '$to_date'";
	}
	if($sales_type!=""){
		$Psql.=" AND sales_type = '$sales_type'";
	}
	$pres = mysql_query($Psql);
	$cnum = mysql_num_rows($pres);
	if($cnum>0){
	$prow = mysql_fetch_object($pres);
	$current_balance = ($prow->delivery_qty+$prow->free_qty);
	}	
	return $current_balance; 
}
function getTotalTRTSales($trt_id,$project_id){	 
	$from_date 	 = formatDate(getRequest('date_from'));
	$to_date 	 = formatDate(getRequest('date_to'));
	$sales_type  = getRequest('sales_type'); 
	$division_id = getRequest('division_id'); 
	$total_qty=0; $free_qty=0;
	if($division_id == 8){
	$Psql= "SELECT SUM(delivery_qty) as delivery_qty, SUM(free_qty) AS free_qty FROM ".SUPPLIER_DELIVERY_SALES_STATUS_VIEW;
	}else{
	$Psql= "SELECT SUM(delivery_qty) as delivery_qty, SUM(free_qty) AS free_qty FROM ".CUSTOMER_DELIVERY_SALES_STATUS_VIEW;	
	}
	$Psql.=" WHERE trt_id='$trt_id' AND project_id='$project_id'";
	if($to_date!=""){
		$Psql.=" AND delivery_date <= '$to_date'";
	}
	if($sales_type!=""){
		$Psql.=" AND sales_type = '$sales_type'";
	}
	$pres = mysql_query($Psql);
	$cnum = mysql_num_rows($pres);
	if($cnum >0){
		$prow = mysql_fetch_object($pres);
		$total_qty = ($prow->delivery_qty+$prow->free_qty);
	}	
	return $total_qty; 
}

function getCustomerPreviousSales($customer,$project_id){	 
	$from_date 	 = formatDate(getRequest('date_from'));
	$to_date 	 = formatDate(getRequest('date_to'));
	$sales_type  = getRequest('sales_type');
	$division_id = getRequest('division_id'); 
	$opening_balance=0; $free_qty=0;
	if($division_id == 8){
	$Psql= "SELECT SUM(delivery_qty) as delivery_qty, SUM(free_qty) AS free_qty FROM ".SUPPLIER_DELIVERY_SALES_STATUS_VIEW;
	}else{
	$Psql= "SELECT SUM(delivery_qty) as delivery_qty, SUM(free_qty) AS free_qty FROM ".CUSTOMER_DELIVERY_SALES_STATUS_VIEW;	
	}
	$Psql.=" WHERE customer='$customer' AND project_id='$project_id'";
	if($from_date!=""){
		$Psql.=" AND delivery_date < '$from_date'";
	}
	if($sales_type!=""){
		$Psql.=" AND sales_type = '$sales_type'";
	}
	$pres = mysql_query($Psql); 
	$cnum = mysql_num_rows($pres); 
	if($cnum>0){
	$prow = mysql_fetch_object($pres);
	$opening_balance = ($prow->delivery_qty+$prow->free_qty);
	}	
	return $opening_balance; 
}
function getCustomerCurrentSales($customer,$project_id){	 
	$from_date 	 = formatDate(getRequest('date_from'));
	$to_date 	 = formatDate(getRequest('date_to'));
	$sales_type  = getRequest('sales_type');	
	$division_id = getRequest('division_id'); 
	$current_balance=0; $free_qty=0;
	if($division_id == 8){
	$Psql= "SELECT SUM(delivery_qty) as delivery_qty, SUM(free_qty) AS free_qty FROM ".SUPPLIER_DELIVERY_SALES_STATUS_VIEW;
	}else{
	$Psql= "SELECT SUM(delivery_qty) as delivery_qty, SUM(free_qty) AS free_qty FROM ".CUSTOMER_DELIVERY_SALES_STATUS_VIEW;	
	}
	$Psql.=" WHERE customer='$customer' AND project_id='$project_id'";
	if($from_date!="" && $to_date!=""){
		$Psql.=" AND delivery_date BETWEEN '$from_date' AND '$to_date'";
	}
	if($sales_type!=""){
		$Psql.=" AND sales_type = '$sales_type'";
	}
	$pres = mysql_query($Psql);
	$cnum = mysql_num_rows($pres);
	if($cnum>0){
	$prow = mysql_fetch_object($pres);
	$current_balance = ($prow->delivery_qty+$prow->free_qty);
	}	
	return $current_balance; 
}
function getTotalCustomerSales($customer,$project_id){	 
	$from_date 	 = formatDate(getRequest('date_from'));
	$to_date 	 = formatDate(getRequest('date_to'));
	$sales_type  = getRequest('sales_type'); 		
	$division_id = getRequest('division_id'); 
	$total_qty=0; $free_qty=0;
	if($division_id == 8){
	$Psql= "SELECT SUM(delivery_qty) as delivery_qty, SUM(free_qty) AS free_qty FROM ".SUPPLIER_DELIVERY_SALES_STATUS_VIEW;
	}else{
	$Psql= "SELECT SUM(delivery_qty) as delivery_qty, SUM(free_qty) AS free_qty FROM ".CUSTOMER_DELIVERY_SALES_STATUS_VIEW;	
	}
	$Psql.=" WHERE customer ='$customer' AND project_id='$project_id'";
	if($to_date!=""){
		$Psql.=" AND delivery_date <= '$to_date'";
	}
	if($sales_type!=""){
		$Psql.=" AND sales_type = '$sales_type'";
	}
	$pres = mysql_query($Psql);// echo $Psql;
	$cnum = mysql_num_rows($pres);
	if($cnum >0){
		$prow = mysql_fetch_object($pres);
		$total_qty = ($prow->delivery_qty+$prow->free_qty);
	}	
	return $total_qty; 
}

function getTotalDivisionPreviousSales($division,$project_id){	 
	$from_date 	 = formatDate(getRequest('date_from'));
	$to_date 	 = formatDate(getRequest('date_to'));
	$sales_type  = getRequest('sales_type');	
	$division_id = getRequest('division_id');  
	$opening_balance=0; $free_qty=0;
	if($division_id == 8){
	$Psql= "SELECT SUM(delivery_qty) as delivery_qty, SUM(free_qty) AS free_qty FROM ".SUPPLIER_DELIVERY_SALES_STATUS_VIEW;
	}else{
	$Psql= "SELECT SUM(delivery_qty) as delivery_qty, SUM(free_qty) AS free_qty FROM ".CUSTOMER_DELIVERY_SALES_STATUS_VIEW;	
	}
	$Psql.=" WHERE division='$division' AND project_id='$project_id'";
	if($from_date!=""){
		$Psql.=" AND delivery_date < '$from_date'";
	}
	if($sales_type!=""){
		$Psql.=" AND sales_type = '$sales_type'";
	}
	$pres = mysql_query($Psql);
	$cnum = mysql_num_rows($pres);
	if($cnum >0){
	$prow = mysql_fetch_object($pres);
	$opening_balance = ($prow->delivery_qty+$prow->free_qty);
	}	
	return $opening_balance; 
}
function getTotalDivisionCurrentSales($division,$project_id){	 
	$from_date 	 = formatDate(getRequest('date_from'));
	$to_date 	 = formatDate(getRequest('date_to'));
	$sales_type  = getRequest('sales_type'); 
	$division_id = getRequest('division_id');
	$current_balance=0; $free_qty=0;	
	if($division_id == 8){
	$Psql= "SELECT SUM(delivery_qty) as delivery_qty, SUM(free_qty) AS free_qty FROM ".SUPPLIER_DELIVERY_SALES_STATUS_VIEW;
	}else{
	$Psql= "SELECT SUM(delivery_qty) as delivery_qty, SUM(free_qty) AS free_qty FROM ".CUSTOMER_DELIVERY_SALES_STATUS_VIEW;	
	}
	$Psql.=" WHERE division='$division' AND project_id='$project_id'";
	if($from_date!="" && $to_date!=""){
		$Psql.=" AND delivery_date BETWEEN '$from_date' AND '$to_date'";
	}
	if($sales_type!=""){
		$Psql.=" AND sales_type = '$sales_type'";
	}
	$pres = mysql_query($Psql);
	$cnum = mysql_num_rows($pres);
	if($cnum>0){
	$prow = mysql_fetch_object($pres);
	$current_balance = ($prow->delivery_qty+$prow->free_qty);
	}	
	return $current_balance; 
}
function getTotalDivisionSales($division,$project_id){	 
	$from_date 	 = formatDate(getRequest('date_from'));
	$to_date 	 = formatDate(getRequest('date_to'));
	$sales_type  = getRequest('sales_type'); 
	$division_id = getRequest('division_id'); 
	$total_qty=0; $free_qty=0;
	if($division_id == 8){
	$Psql= "SELECT SUM(delivery_qty) as delivery_qty, SUM(free_qty) AS free_qty FROM ".SUPPLIER_DELIVERY_SALES_STATUS_VIEW;
	}else{
	$Psql= "SELECT SUM(delivery_qty) as delivery_qty, SUM(free_qty) AS free_qty FROM ".CUSTOMER_DELIVERY_SALES_STATUS_VIEW;	
	}
	$Psql.=" WHERE division='$division' AND project_id='$project_id'";
	if($to_date!=""){
		$Psql.=" AND delivery_date <= '$to_date'";
	}
	if($sales_type!=""){
		$Psql.=" AND sales_type = '$sales_type'";
	}
	$pres = mysql_query($Psql);
	$cnum = mysql_num_rows($pres);
	if($cnum>0){
		$prow = mysql_fetch_object($pres);
		$total_qty = ($prow->delivery_qty+$prow->free_qty);
	}	
	return $total_qty; 
}
//===== Start Sales Target Status =========
function getTotalSalesTargetQtyByGroup($group_id,$catagory=NULL,$Area=NULL,$Trt=NULL){
	$project_id = getFromSession('project_id');		
	$from_date 		= formatDate(getRequest('date_from'));  $to_date = formatDate(getRequest('date_to'));
	$target_qty	= 0; 
	$Psql= "SELECT SUM(target_qty) AS target_qty FROM ".SALES_TARGET_TBL." WHERE  project_id = '$project_id' AND group_id = '$group_id'";
	if($catagory !=""){$Psql.=" AND catagory_id='$catagory'";}
	if($Area !=""){$Psql.=" AND district_id='$Area'";}
	if($Trt !=""){$Psql.=" AND area_id='$Trt'";}
	if($from_date !=""){
		$Psql.=" AND target_from ='$from_date'";
	}
	if($to_date !=""){
		$Psql.=" AND target_to = '$to_date'";
	}
	
	if($group_id !="" && $Area =="" && $Trt ==""){
		$Psql.=" GROUP BY group_id";
	}elseif($Area !="" && $Trt ==""){
		$Psql.=" GROUP BY district_id"; 
	}elseif($Area !="" && $Trt !=""){
		$Psql.=" GROUP BY area_id"; 
	}
	
	$pres = mysql_query($Psql);
	$prow = mysql_fetch_object($pres);
	$target_qty   = $prow->target_qty;	
	if(intval($target_qty)==""){$target_qty=0;}
	return $target_qty;
}
function getTotalSalesDeliveryQtyByGroup($group_id,$product=NULL,$catagory=NULL,$Area=NULL,$Trt=NULL){
	$project_id = getFromSession('project_id');		
	$from_date 		= formatDate(getRequest('date_from'));  $to_date = formatDate(getRequest('date_to'));
	$delivery_qty	= 0; 
	$Psql= "SELECT SUM(pd.delivery_qty) AS delivery_qty FROM ".DELIVERY_PRODUCT_LEDGER_VIEW." as pd,".SALES_TARGET_TBL." as st  
	WHERE pd.district = st.district_id AND pd.area=st.area_id AND pd.product=st.product_id AND pd.project_id = '$project_id' AND pd.group_id = '$group_id'";
	if($product !=""){$Psql.=" AND pd.product='$product'";}
	if($Area !=""){$Psql.=" AND pd.district='$Area'";}
	if($Trt !=""){$Psql.=" AND pd.area='$Trt'";}
	if($catagory !=""){$Psql.=" AND pd.catagory='$catagory'";}
	if($from_date!="" && $to_date !=""){
		$Psql.=" AND pd.delivery_date BETWEEN '$from_date' AND '$to_date'";
	}
	
	if($product !=""){
		$Psql.=" GROUP BY pd.product"; 
	}elseif($group_id!="" && $Area =="" && $Trt ==""){
		$Psql.=" GROUP BY pd.group_id"; 
	}elseif($Area !="" && $Trt ==""){
		$Psql.=" GROUP BY pd.district"; 
	}elseif($Area !="" && $Trt !=""){
		$Psql.=" GROUP BY pd.area"; 
	}
	
	$pres = mysql_query($Psql);
	$prow = mysql_fetch_object($pres);
	$delivery_qty   = $prow->delivery_qty;	
	if(intval($delivery_qty)==""){$delivery_qty=0;}
	return $delivery_qty;
}

function getTotalSalesValueByGroup($group_id,$product=NULL,$catagory=NULL,$Area=NULL,$Trt=NULL){
	$project_id 	= getFromSession('project_id');		
	$from_date 		= formatDate(getRequest('date_from'));  $to_date = formatDate(getRequest('date_to'));
	$total_amount	= 0; 
	$Psql= "SELECT SUM(pd.total_amount) AS total_amount FROM ".DELIVERY_PRODUCT_LEDGER_VIEW."  as pd,".SALES_TARGET_TBL." as st
	WHERE pd.district = st.district_id AND pd.area=st.area_id AND pd.product=st.product_id AND pd.project_id = '$project_id' AND pd.group_id = '$group_id'";
	if($product !=""){$Psql.=" AND pd.product='$product'";}
	if($Area !=""){$Psql.=" AND pd.district='$Area'";}
	if($Trt !=""){$Psql.=" AND pd.area='$Trt'";}
	if($catagory !=""){$Psql.=" AND pd.catagory='$catagory'";}
	if($from_date!="" && $to_date !=""){
		$Psql.=" AND pd.delivery_date BETWEEN '$from_date' AND '$to_date'";
	}
	if($product !=""){
		$Psql.=" GROUP BY pd.product"; 
	}elseif($Area !="" && $Trt ==""){
		$Psql.=" GROUP BY pd.district"; 
	}elseif($Area !="" && $Trt !=""){
		$Psql.=" GROUP BY pd.area"; 
	}else{
		$Psql.=" GROUP BY pd.group_id"; 
	}	
	$pres = mysql_query($Psql);
	$prow = mysql_fetch_object($pres);
	$total_amount   = $prow->total_amount;	
	if(intval($total_amount)==""){$total_amount=0;}
	return $total_amount;
}

//===== Start Sales Target Status (Category) =========
function getTotalSalesCatTargetByGroup($catagory,$product=NULL,$srcproducts1=NULL,$Division=NULL,$Area=NULL,$Trt=NULL,$group_by){
	$project_id = getFromSession('project_id');		
	$from_date  = formatDate(getRequest('date_from'));  $to_date = formatDate(getRequest('date_to'));
	$target_qty	= 0; 
	$Psql= "SELECT SUM(target_qty) AS target_qty FROM ".SALES_TARGET_CATAGORY_TBL." WHERE  project_id = '$project_id' ";
	if($catagory !=""){$Psql.=" AND catagory_id='$catagory'";}
	if($product !=""){$Psql.=" AND (product='$product')";}
	else{
	  if($srcproducts1 !=""){ $Psql.=" AND ($srcproducts1) "; }
	}
	if($Division !=""){$Psql.=" AND division_id='$Division'";}
	if($Area !=""){$Psql.=" AND district_id='$Area'";}
	if($Trt !=""){$Psql.=" AND area_id='$Trt'";}
	if($from_date!="" && $to_date !=""){
		$Psql.=" AND `target_from` ='$from_date' AND `target_to`='$to_date'";
	}
	$Psql.=" GROUP BY $group_by";
	
	$pres = mysql_query($Psql); //echo $Psql;
	$prow = mysql_fetch_object($pres);
	$target_qty   = $prow->target_qty;	
	if(intval($target_qty)==""){$target_qty=0;}
	return $target_qty;
}
function getTotalCatSalesQtyByGroup($catagory,$product=NULL,$srcproducts3=NULL,$Division=NULL,$Area=NULL,$Trt=NULL,$group_by){
	$project_id = getFromSession('project_id');		
	$from_date  = formatDate(getRequest('date_from'));  $to_date = formatDate(getRequest('date_to'));
	$delivery_qty	= 0; $total_amount=0;
	$Psql= "SELECT SUM(pd.delivery_qty) AS delivery_qty,SUM(pd.total_amount) AS total_amount FROM ".DELIVERY_CATEGORY_LEDGER_VIEW." as pd  
	WHERE pd.project_id = '$project_id'";
	if($catagory !=""){$Psql.=" AND pd.catagory='$catagory'";}
	if($product !=""){$Psql.=" AND (pd.product='$product')";}
	else{
	 if($srcproducts3 !=""){ $Psql.=" AND ($srcproducts3) "; }
	}
	if($Division !=""){$Psql.=" AND pd.`division`='$Division'";}
	if($Area !=""){$Psql.=" AND pd.district='$Area'";}
	if($Trt !=""){$Psql.=" AND pd.area='$Trt'";}
	if($from_date!="" && $to_date !=""){
		$Psql.=" AND pd.delivery_date BETWEEN '$from_date' AND '$to_date'";
	}
	$Psql.=" GROUP BY pd.$group_by";
	
	$pres = mysql_query($Psql);
	$prow = mysql_fetch_object($pres);
	$delivery_qty   = $prow->delivery_qty;
	$total_amount   = $prow->total_amount;		
	if(intval($delivery_qty)==""){$delivery_qty=0;} if(intval($total_amount)==""){$total_amount=0;}
	return $delivery_qty."####".$total_amount;
}

function getTotalCatSalesValueByGroup($catagory,$product=NULL,$srcproducts1=NULL,$Division=NULL,$Area=NULL,$Trt=NULL,$group_by){
	$project_id 	= getFromSession('project_id');		
	$from_date 		= formatDate(getRequest('date_from'));  $to_date = formatDate(getRequest('date_to'));
	$total_amount	= 0; 
	$Psql= "SELECT SUM(pd.total_amount) AS total_amount FROM ".DELIVERY_CATEGORY_LEDGER_VIEW."  as pd
	WHERE pd.project_id = '$project_id' ";
	
	if($catagory !=""){$Psql.=" AND pd.catagory='$catagory'";}
	if($product !=""){$Psql.=" AND pd.product='$product'";}else{
	if($srcproducts1 !=""){ $Psql.=" AND ($srcproducts1) "; }
	}
	if($Division !=""){$Psql.=" AND pd.`division`='$Division'";}
	if($Area !=""){$Psql.=" AND pd.district='$Area'";}
	if($Trt !=""){$Psql.=" AND pd.area='$Trt'";}
	if($from_date!="" && $to_date !=""){
		$Psql.=" AND pd.delivery_date BETWEEN '$from_date' AND '$to_date'";
	}
	$Psql.=" GROUP BY pd.$group_by";
	 	
	$pres = mysql_query($Psql);
	$prow = mysql_fetch_object($pres);
	$total_amount   = $prow->total_amount;	
	if(intval($total_amount)==""){$total_amount=0;}
	return $total_amount;
}
//======== Start Sales Status Top Sheet ========
function getTotalSalesDeliveryQtyByCat($catagory,$group_by,$subcatagory=NULL,$division=NULL,$product_type=NULL,$store_id=NULL,$product=NULL){
	$project_id 	= getFromSession('project_id');		
	$from_date 	= formatDate(getRequest('date_from'));  $to_date = formatDate(getRequest('date_to'));
	$delivery_qty	= 0; 
	$Psql= "SELECT SUM(pd.delivery_qty) AS delivery_qty FROM ".DELIVERY_SALES_STATUS_VIEW." as pd  
	WHERE pd.project_id = '$project_id' AND pd.catagory = '$catagory'";
	if($subcatagory !=""){$Psql.=" AND pd.subcatagory='$subcatagory'";}
	if($product !=""){$Psql.=" AND pd.product='$product'";}
	if($division !=""){$Psql.=" AND pd.division='$division'";}
	if($product_type !=""){$Psql.=" AND pd.product_type='$product_type'";}
	if($store_id !=""){$Psql.=" AND pd.store_id='$store_id'";}
	
	if($from_date!="" && $to_date !=""){
		$Psql.=" AND pd.delivery_date BETWEEN '$from_date' AND '$to_date'";
	}
	
	if($group_by !=""){
		$Psql.=" GROUP BY pd.$group_by"; 
	}else{
		$Psql.=" GROUP BY pd.catagory"; 
	}
	//echo $Psql;
	
	$pres = mysql_query($Psql);
	$prow = mysql_fetch_object($pres);
	$delivery_qty = $prow->delivery_qty;
	if(intval($delivery_qty)==""){$delivery_qty=0;}
	return $delivery_qty;
}
function getTotalSalesDeliveryFreeQtyByCat($catagory,$group_by,$subcatagory=NULL,$division=NULL,$product_type=NULL,$store_id=NULL,$product=NULL){
	$project_id 	= getFromSession('project_id');		
	$from_date 		= formatDate(getRequest('date_from'));  $to_date = formatDate(getRequest('date_to'));
	$free_qty		= 0; 
	$Psql= "SELECT SUM(pd.free_qty) AS free_qty FROM ".DELIVERY_SALES_STATUS_VIEW." as pd  
	WHERE pd.project_id = '$project_id' AND pd.catagory = '$catagory'";
	if($subcatagory !=""){$Psql.=" AND pd.subcatagory='$subcatagory'";}
	if($division !=""){$Psql.=" AND pd.division='$division'";}
	if($product_type !=""){$Psql.=" AND pd.product_type='$product_type'";}
	if($store_id !=""){$Psql.=" AND pd.store_id='$store_id'";}
	
	if($from_date!="" && $to_date !=""){
		$Psql.=" AND pd.delivery_date BETWEEN '$from_date' AND '$to_date'";
	}
	
	if($group_by !=""){
		$Psql.=" GROUP BY pd.$group_by"; 
	}else{
		$Psql.=" GROUP BY pd.catagory"; 
	}
	//echo $Psql;
	
	$pres = mysql_query($Psql);
	$prow = mysql_fetch_object($pres);
	$free_qty   = $prow->free_qty;	
	if(intval($free_qty)==""){$free_qty=0;}
	return $free_qty;
}
function getTotalSalesValueByCat($catagory,$group_by,$subcatagory=NULL,$division=NULL,$product_type=NULL,$store_id=NULL,$product=NULL){
	$project_id 	= getFromSession('project_id');		
	$from_date 		= formatDate(getRequest('date_from'));  $to_date = formatDate(getRequest('date_to'));
	$total_amount	= 0; 
	$Psql= "SELECT SUM(pd.total_amount) AS total_amount FROM ".DELIVERY_SALES_STATUS_VIEW."  as pd
	WHERE pd.project_id = '$project_id' AND pd.catagory = '$catagory'";	
	if($subcatagory !=""){$Psql.=" AND pd.subcatagory='$subcatagory'";}
	if($product !=""){$Psql.=" AND pd.product='$product'";}
	if($division !=""){$Psql.=" AND pd.division='$division'";}
	if($product_type !=""){$Psql.=" AND pd.product_type='$product_type'";}
	if($store_id !=""){$Psql.=" AND pd.store_id='$store_id'";}
	
	if($from_date!="" && $to_date !=""){
		$Psql.=" AND pd.delivery_date BETWEEN '$from_date' AND '$to_date'";
	}
	if($group_by !=""){
		$Psql.=" GROUP BY pd.$group_by"; 
	}else{
		$Psql.=" GROUP BY pd.catagory"; 
	}	
	$pres = mysql_query($Psql);
	$prow = mysql_fetch_object($pres);
	$total_amount   = $prow->total_amount;	
	if(intval($total_amount)==""){$total_amount=0;}
	return $total_amount;
}

function getGrandTotalSalesDeliveryQty($product_type=NULL,$store_id=NULL){
	$project_id 	= getFromSession('project_id');	
	$divisionid 	= getRequest('division_id');
	$catagory 	= getRequest('catagory');
	$subcatagory 	= getRequest('subcatagory');
	$product 	= getRequest('product');
	$from_date 	= formatDate(getRequest('date_from'));  $to_date = formatDate(getRequest('date_to'));
	$delivery_qty	= 0; 
	$Psql= "SELECT SUM(pd.delivery_qty) AS delivery_qty ,SUM(pd.free_qty) AS free_qty FROM ".DELIVERY_SALES_STATUS_VIEW." as pd  
	WHERE pd.project_id = '$project_id' ";
	if($divisionid !=""){ $Psql.=" AND pd.division='$divisionid' "; }           
	if($product_type !=""){$Psql.=" AND pd.product_type='$product_type'";}
	if($catagory !=""){$Psql.=" AND pd.catagory='$catagory'";}
	if($subcatagory !=""){$Psql.=" AND pd.subcatagory='$subcatagory'";}
	if($product !=""){$Psql.=" AND pd.product='$product'";}
	if($store_id !=""){$Psql.=" AND pd.store_id='$store_id'";}
	
	if($from_date!="" && $to_date !=""){
		$Psql.=" AND pd.delivery_date BETWEEN '$from_date' AND '$to_date' ";
	}	
	//echo $Psql;
	
	$pres = mysql_query($Psql);
	$prow = mysql_fetch_object($pres);
	$delivery_qty   = $prow->delivery_qty;	
	if(intval($delivery_qty)==""){$delivery_qty=0;}
	return $delivery_qty;
}
function getGrandTotalSalesDeliveryFreeQty($product_type=NULL,$store_id=NULL){
	$project_id 	= getFromSession('project_id');		
	$divisionid 	= getRequest('division_id');
	$catagory 	= getRequest('catagory');
	$subcatagory 	= getRequest('subcatagory');
	$product 	= getRequest('product');		
	$from_date 	= formatDate(getRequest('date_from'));  $to_date = formatDate(getRequest('date_to'));
	$free_qty	= 0; 
	$Psql= "SELECT SUM(pd.free_qty) AS free_qty FROM ".DELIVERY_SALES_STATUS_VIEW." as pd  
	WHERE pd.project_id = '$project_id'";
	if($divisionid !=""){ $Psql.=" AND pd.division='$divisionid' "; } 
	if($product_type !=""){$Psql.=" AND pd.product_type='$product_type'";}
	if($catagory !=""){$Psql.=" AND pd.catagory='$catagory'";}
	if($subcatagory !=""){$Psql.=" AND pd.subcatagory='$subcatagory'";}
	if($product !=""){$Psql.=" AND pd.product='$product'";}
	if($store_id !=""){$Psql.=" AND pd.store_id='$store_id'";}
	
	if($from_date!="" && $to_date !=""){
		$Psql.=" AND pd.delivery_date BETWEEN '$from_date' AND '$to_date'";
	}	
	//echo $Psql;
	
	$pres = mysql_query($Psql);
	$prow = mysql_fetch_object($pres);
	$free_qty   = $prow->free_qty;	
	if(intval($free_qty)==""){$free_qty=0;}
	return $free_qty;
}
function getGrandTotalSalesDeliveryValue($product_type=NULL,$store_id=NULL){
	$project_id 	= getFromSession('project_id');		
	$divisionid 	= getRequest('division_id');
	$catagory 	= getRequest('catagory');
	$subcatagory 	= getRequest('subcatagory');
	$product 	= getRequest('product');		
	$from_date 	= formatDate(getRequest('date_from'));  $to_date = formatDate(getRequest('date_to'));
	$total_amount	= 0; 
	$Psql= "SELECT SUM(pd.total_amount) AS total_amount FROM ".DELIVERY_SALES_STATUS_VIEW."  as pd
	WHERE pd.project_id = '$project_id'";
	if($divisionid !=""){ $Psql.=" AND pd.division='$divisionid' "; } 	
	if($product_type !=""){$Psql.=" AND pd.product_type='$product_type'";}
	if($catagory !=""){$Psql.=" AND pd.catagory='$catagory'";}
	if($subcatagory !=""){$Psql.=" AND pd.subcatagory='$subcatagory'";}
	if($product !=""){$Psql.=" AND pd.product='$product'";}
	if($store_id !=""){$Psql.=" AND pd.store_id='$store_id'";}
	
	if($from_date!="" && $to_date !=""){
		$Psql.=" AND pd.delivery_date BETWEEN '$from_date' AND '$to_date'";
	}		
	$pres = mysql_query($Psql);
	$prow = mysql_fetch_object($pres);
	$total_amount   = $prow->total_amount;	
	if(intval($total_amount)==""){$total_amount=0;}
	return $total_amount;
}

//======== Start Delivery Sales Status =============

function getTotalSalesDeliveryQtyByTRT($division,$product=NULL,$catagory=NULL,$subcatagory=NULL,$Area=NULL,$Trt=NULL,$groupby=0){
	$project_id = getFromSession('project_id');		
	$from_date 		= formatDate(getRequest('date_from'));  $to_date = formatDate(getRequest('date_to'));
	$delivery_qty	= 0; 
	$Psql= "SELECT SUM(pd.delivery_qty) AS delivery_qty ,SUM(pd.free_qty) AS free_qty FROM ".DELIVERY_SALES_STATUS_VIEW." as pd  
	WHERE pd.project_id = '$project_id' AND pd.division = '$division'";
	if($product !=""){$Psql.=" AND pd.product='$product'";}
	if($Area !=""){$Psql.=" AND pd.district='$Area'";}
	if($Trt !=""){$Psql.=" AND pd.area='$Trt'";}
	if($catagory !=""){$Psql.=" AND pd.catagory='$catagory'";}
	if($subcatagory !=""){$Psql.=" AND pd.subcatagory='$subcatagory'";}
	if($from_date!="" && $to_date !=""){
		$Psql.=" AND pd.delivery_date BETWEEN '$from_date' AND '$to_date'";
	}
	/*
	if($product !=""){
		$Psql.=" GROUP BY pd.product"; 
	}elseif($division!="" && $Area =="" && $Trt ==""){
		$Psql.=" GROUP BY pd.division"; 
	}elseif($Area !="" && $Trt ==""){
		$Psql.=" GROUP BY pd.district"; 
	}elseif($Area !="" && $Trt !=""){
		$Psql.=" GROUP BY pd.area"; 
	}
	*/
	if($groupby==1){
		$Psql.=" GROUP BY pd.division"; 
	}elseif($Area !="" && $Trt ==""){
		$Psql.=" GROUP BY pd.district"; 
	}elseif($Area !="" && $Trt !=""){
		$Psql.=" GROUP BY pd.area"; 
	}elseif($product !=""){
		$Psql.=" GROUP BY pd.product"; 
	}
	
	//echo $Psql;
	
	$pres = mysql_query($Psql);
	$prow = mysql_fetch_object($pres);
	$delivery_qty   = ($prow->delivery_qty + $prow->free_qty);	
	if(intval($delivery_qty)==""){$delivery_qty=0;}
	return $delivery_qty;
}

function getTotalSalesValueByTRT($division,$product=NULL,$catagory=NULL,$subcatagory=NULL,$Area=NULL,$Trt=NULL,$groupby=0){
	$project_id 	= getFromSession('project_id');		
	$from_date 	= formatDate(getRequest('date_from'));  $to_date = formatDate(getRequest('date_to'));
	$total_amount	= 0; 
	$Psql= "SELECT SUM(pd.total_amount) AS total_amount FROM ".DELIVERY_SALES_STATUS_VIEW."  as pd
	WHERE pd.project_id = '$project_id' AND pd.division = '$division'";
	if($product !=""){$Psql.=" AND pd.product='$product'";}
	if($Area !=""){$Psql.=" AND pd.district='$Area'";}
	if($Trt !=""){$Psql.=" AND pd.area='$Trt'";}
	if($catagory !=""){$Psql.=" AND pd.catagory='$catagory'";}
	if($subcatagory !=""){$Psql.=" AND pd.subcatagory='$subcatagory'";}
	if($from_date!="" && $to_date !=""){
		$Psql.=" AND pd.delivery_date BETWEEN '$from_date' AND '$to_date'";
	}
	/*
	if($product !=""){
		$Psql.=" GROUP BY pd.product"; 
	}elseif($Area !="" && $Trt ==""){
		$Psql.=" GROUP BY pd.district"; 
	}elseif($Area !="" && $Trt !=""){
		$Psql.=" GROUP BY pd.area"; 
	}else{
		$Psql.=" GROUP BY pd.division"; 
	}
	*/
	if($groupby==1){
		$Psql.=" GROUP BY pd.division"; 
	}elseif($Area !="" && $Trt ==""){
		$Psql.=" GROUP BY pd.district"; 
	}elseif($Area !="" && $Trt !=""){
		$Psql.=" GROUP BY pd.area"; 
	}elseif($product !=""){
		$Psql.=" GROUP BY pd.product"; 
	}

	
	$pres = mysql_query($Psql);
	$prow = mysql_fetch_object($pres);
	$total_amount   = $prow->total_amount;	
	if(intval($total_amount)==""){$total_amount=0;}
	return $total_amount;
}
?>
