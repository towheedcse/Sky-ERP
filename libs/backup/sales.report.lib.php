<?   
function getTotalCatagoryOBQty($catagory,$project_id){	
	$product_type = getRequest('product_type'); 
	$from_date 	  = formatDate(getRequest('date_from')); $to_date = formatDate(getRequest('date_to')); 
	$ob_balance=0;
    if($from_date==""){   
		$Psql= "SELECT SUM(op_qty) as op_qty FROM ".PRODUCT_STATUS_BY_CATAGORY_VIEW." WHERE catagory='$catagory' AND project_id='$project_id' ";
		if($product_type!=""){$Psql.=" AND product_type='$product_type'";}
		$pres = mysql_query($Psql);
		$prow = mysql_fetch_object($pres);
		$ob_balance   = $prow->op_qty;	
	}else{
		$csql= "SELECT * FROM ".PRODUCT_TBL." WHERE catagory = '$catagory' ";
		if($product_type!=""){$csql.=" AND product_type='$product_type'";}
		$cres = mysql_query($csql);		
		while($crow = mysql_fetch_object($cres)){
		$Psql= "SELECT (SUM(dr)- SUM(cr)) AS ob_balance FROM ".STOCK_LEDGER_TBL." WHERE product_id='$crow->product_id' AND project_id='$project_id' ";
		if($from_date!="" && $to_date ==""){
			$Psql.=" AND create_date < '$from_date'";
		}
		$pres = mysql_query($Psql);
		$prow = mysql_fetch_object($pres);
		$ob_balance+= $prow->ob_balance;
		}
	}
	return $ob_balance;
}
function getAvgCatagoryOBRate($catagory,$project_id){
	$product_type = getRequest('product_type'); 	 
	$closing_rate=0;
	$from_date 	 = formatDate(getRequest('date_from')); $to_date = formatDate(getRequest('date_to'));
    if($from_date==""){   
		$Psql= "SELECT AVG(unit_price) as closing_rate FROM ".PRODUCT_TBL." WHERE catagory='$catagory' AND project_id='$project_id' ";
		if($product_type!=""){$Psql.=" AND product_type='$product_type'";}
		$pres = mysql_query($Psql);
		$prow = mysql_fetch_object($pres);
		$closing_rate   = $prow->closing_rate;	
	}else{
		$csql= "SELECT * FROM ".PRODUCT_TBL." WHERE catagory = '$catagory'";
		if($product_type!=""){$csql.=" AND product_type='$product_type'";}
		$cres = mysql_query($csql);
		$sl=0;	
		while($crow = mysql_fetch_object($cres)){
		$Psql= "SELECT AVG(unit_price) AS closing_rate FROM ".STOCK_LEDGER_TBL." WHERE dr >0 AND product_id='$crow->product_id' AND project_id='$project_id' ";
		if($from_date!="" && $to_date ==""){
			$Psql.=" AND create_date < '$from_date'";
		}elseif($from_date=="" && $to_date !=""){
			$Psql.=" AND create_date <= '$to_date'";
		}
		$pres = mysql_query($Psql);
		$prow = mysql_fetch_object($pres);
		$closing_rate+= $prow->closing_rate;
		$sl++;
		}
		$closing_rate=($closing_rate/$sl);
	}
	return $closing_rate;
}
function getAvgCatagoryInRate($catagory,$project_id){	
	$product_type = getRequest('product_type'); 	 
	$closing_rate=0;
	$from_date 	 = formatDate(getRequest('date_from')); $to_date = formatDate(getRequest('date_to'));
    if($from_date==""){  
		$csql= "SELECT * FROM ".PRODUCT_TBL." WHERE catagory = '$catagory'";
		if($product_type!=""){$csql.=" AND product_type='$product_type'";}
		$cres = mysql_query($csql); 
		$sl=0;	
		while($crow = mysql_fetch_object($cres)){
		$Psql= "SELECT AVG(unit_price) AS closing_rate FROM ".STOCK_LEDGER_TBL." WHERE dr >0 AND product_id='$crow->product_id' AND project_id='$project_id'";
		$pres = mysql_query($Psql);
		$prow = mysql_fetch_object($pres);
		$closing_rate+= $prow->closing_rate;
		$sl++;
		}
		$closing_rate=($closing_rate/$sl);	
	}else{
		$csql= "SELECT * FROM ".PRODUCT_TBL." WHERE catagory = '$catagory'";
		if($product_type!=""){$csql.=" AND product_type='$product_type'";}
		$cres = mysql_query($csql);
		$sl=0;	
		while($crow = mysql_fetch_object($cres)){
		$Psql= "SELECT AVG(unit_price) AS closing_rate FROM ".STOCK_LEDGER_TBL." WHERE dr >0 AND product_id='$crow->product_id' AND project_id='$project_id' ";		
		if($from_date!="" && $to_date ==""){
			$Psql.=" AND create_date >= '$from_date'";
		}elseif($from_date=="" && $to_date !=""){
			$Psql.=" AND create_date <= '$to_date'";
		}elseif($from_date!="" && $to_date !=""){
			$Psql.=" AND create_date BETWEEN '$from_date' AND '$to_date'";
		}
		$pres = mysql_query($Psql);
		$prow = mysql_fetch_object($pres);
		$closing_rate+= $prow->closing_rate;
		$sl++;
		}
		$closing_rate=($closing_rate/$sl);
	}
	return $closing_rate;
}
function getAvgCatagoryOutRate($catagory,$project_id){	
	$product_type = getRequest('product_type');  
	$closing_rate=0;
	$from_date 	 = formatDate(getRequest('date_from')); $to_date = formatDate(getRequest('date_to'));
    if($from_date==""){  
		$csql= "SELECT * FROM ".PRODUCT_TBL." WHERE catagory = '$catagory'";
		if($product_type!=""){$csql.=" AND product_type='$product_type'";}
		$cres = mysql_query($csql); 
		$sl=0;	
		while($crow = mysql_fetch_object($cres)){
		$Psql= "SELECT AVG(unit_price) AS closing_rate FROM ".STOCK_LEDGER_TBL." WHERE cr >0 AND product_id='$crow->product_id' AND project_id='$project_id'";
		$pres = mysql_query($Psql);
		$prow = mysql_fetch_object($pres);
		$closing_rate+= $prow->closing_rate;
		$sl++;
		}
		$closing_rate=($closing_rate/$sl);	
	}else{
		$csql= "SELECT * FROM ".PRODUCT_TBL." WHERE catagory = '$catagory'";
		if($product_type!=""){$csql.=" AND product_type='$product_type'";}
		$cres = mysql_query($csql);
		$sl=0;	
		while($crow = mysql_fetch_object($cres)){
		$Psql= "SELECT AVG(unit_price) AS closing_rate FROM ".STOCK_LEDGER_TBL." WHERE cr >0 AND product_id='$crow->product_id' AND project_id='$project_id' ";
		if($from_date!="" && $to_date ==""){
			$Psql.=" AND create_date >= '$from_date'";
		}elseif($from_date=="" && $to_date !=""){
			$Psql.=" AND create_date <= '$to_date'";
		}elseif($from_date!="" && $to_date !=""){
			$Psql.=" AND create_date BETWEEN '$from_date' AND '$to_date'";
		}
		$pres = mysql_query($Psql);
		$prow = mysql_fetch_object($pres);
		$closing_rate+= $prow->closing_rate;
		$sl++;
		}
		$closing_rate=($closing_rate/$sl);
	}
	return $closing_rate;
}
function getAvgCatagoryClosingRate($catagory,$project_id){	
	$product_type = getRequest('product_type');  
	$closing_rate=0;
	$from_date 	 = formatDate(getRequest('date_from')); $to_date = formatDate(getRequest('date_to'));
    if($from_date==""){   
		$Psql= "SELECT AVG(unit_price) as closing_rate FROM ".PRODUCT_TBL." WHERE catagory='$catagory' AND project_id='$project_id' ";
		if($product_type!=""){$Psql.=" AND product_type='$product_type'";}
		$pres = mysql_query($Psql);
		$prow = mysql_fetch_object($pres);
		$closing_rate   = $prow->closing_rate;	
	}else{
		$csql= "SELECT * FROM ".PRODUCT_TBL." WHERE catagory = '$catagory'";
		if($product_type!=""){$csql.=" AND product_type='$product_type'";}
		$cres = mysql_query($csql);
		$sl=0;	
		while($crow = mysql_fetch_object($cres)){
		$Psql= "SELECT AVG(unit_price) AS closing_rate FROM ".STOCK_LEDGER_TBL." WHERE dr >0 AND product_id='$crow->product_id' AND project_id='$project_id' ";
		
		if($from_date!="" && $to_date ==""){
			$Psql.=" AND create_date >= '$from_date'";
		}elseif($from_date=="" && $to_date !=""){
			$Psql.=" AND create_date <= '$to_date'";
		}elseif($from_date!="" && $to_date !=""){
			$Psql.=" AND create_date BETWEEN '$from_date' AND '$to_date'";
		}
		$pres = mysql_query($Psql);
		$prow = mysql_fetch_object($pres);
		$closing_rate+= $prow->closing_rate;
		$sl++;
		}
		$closing_rate=($closing_rate/$sl);
	}
	return $closing_rate;
}
function getAvgProductOBRate($product_id,$project_id){
	$unit_price=0;
	$from_date 	 = formatDate(getRequest('date_from')); $to_date = formatDate(getRequest('date_to'));
    if($from_date==""){   
		$Psql= "SELECT unit_price as closing_rate FROM ".PRODUCT_TBL." WHERE product_id='$product_id' AND project_id='$project_id' ";
		$pres = mysql_query($Psql);
		$prow = mysql_fetch_object($pres);
		$unit_price   = $prow->closing_rate;
	}else{
		$Psql= "SELECT AVG(unit_price) AS unit_price FROM ".STOCK_LEDGER_TBL." WHERE dr >0 AND product_id='$product_id' AND project_id='$project_id' ";
		if($from_date!="" && $to_date ==""){
			$Psql.=" AND create_date < '$from_date'";
		}
		$pres = mysql_query($Psql);
		$prow = mysql_fetch_object($pres);
		$unit_price = $prow->unit_price;
	}
	return $unit_price;
}
function getAvgProductInRate($product_id,$project_id){	 
	$unit_price=0; $from_date = formatDate(getRequest('date_from')); $to_date = formatDate(getRequest('date_to'));
    if($from_date==""){   
		$Psql= "SELECT AVG(unit_price) AS unit_price FROM ".STOCK_LEDGER_TBL." WHERE dr >0 AND product_id='$product_id' AND project_id='$project_id' ";
		$pres = mysql_query($Psql);
		$prow = mysql_fetch_object($pres);
		$unit_price   = $prow->unit_price;
	}else{
		$Psql= "SELECT AVG(unit_price) AS unit_price FROM ".STOCK_LEDGER_TBL." WHERE dr >0 AND product_id='$product_id' AND project_id='$project_id' ";
		if($from_date!="" && $to_date ==""){
			$Psql.=" AND create_date >= '$from_date'";
		}elseif($from_date=="" && $to_date !=""){
			$Psql.=" AND create_date <= '$to_date'";
		}elseif($from_date!="" && $to_date !=""){
			$Psql.=" AND create_date BETWEEN '$from_date' AND '$to_date'";
		}
		$pres = mysql_query($Psql);
		$prow = mysql_fetch_object($pres);
		$unit_price = $prow->unit_price;
	}
	return $unit_price;
}
function getAvgProductOutRate($product_id,$project_id){	 
	$unit_price=0; $from_date = formatDate(getRequest('date_from')); $to_date = formatDate(getRequest('date_to'));
    if($from_date==""){   
		$Psql= "SELECT AVG(unit_price) AS unit_price FROM ".STOCK_LEDGER_TBL." WHERE cr >0 AND product_id='$product_id' AND project_id='$project_id' ";
		$pres = mysql_query($Psql);
		$prow = mysql_fetch_object($pres);
		$unit_price   = $prow->unit_price;
	}else{
		$Psql= "SELECT AVG(unit_price) AS unit_price FROM ".STOCK_LEDGER_TBL." WHERE cr >0 AND product_id='$product_id' AND project_id='$project_id' ";		
		if($from_date!="" && $to_date ==""){
			$Psql.=" AND create_date >= '$from_date'";
		}elseif($from_date=="" && $to_date !=""){
			$Psql.=" AND create_date <= '$to_date'";
		}elseif($from_date!="" && $to_date !=""){
			$Psql.=" AND create_date BETWEEN '$from_date' AND '$to_date'";
		}
				
		$pres = mysql_query($Psql);
		$prow = mysql_fetch_object($pres);
		$unit_price = $prow->unit_price;
	}
	return $unit_price;
}
function getTotalCatagoryInStockQty($catagory,$project_id){	
	$product_type= getRequest('product_type'); 
	$from_date 	 = formatDate(getRequest('date_from'));
	$to_date 	 = formatDate(getRequest('date_to'));
	$instock=0;
    if($from_date==""){   
		$Psql="SELECT SUM(op_qty) as op_qty,SUM(instock) as instock FROM ".PRODUCT_STATUS_BY_CATAGORY_VIEW." WHERE catagory='$catagory' AND project_id='$project_id' ";
		if($product_type!=""){$Psql.=" AND product_type='$product_type'";}
		$pres 	= mysql_query($Psql);
		$prow 	= mysql_fetch_object($pres);
		$instock   = ($prow->instock-$prow->op_qty);	
	}else{
		$csql= "SELECT * FROM ".PRODUCT_TBL." WHERE catagory = '$catagory'";
		if($product_type!=""){$csql.=" AND product_type='$product_type'";}
		$cres = mysql_query($csql);		
		while($crow = mysql_fetch_object($cres)){
		$Psql= "SELECT SUM(dr) AS in_qty FROM ".STOCK_LEDGER_TBL." WHERE product_id='$crow->product_id' AND project_id='$project_id' ";	
		if($from_date!="" && $to_date ==""){
			$Psql.=" AND create_date >= '$from_date'";
		}elseif($from_date=="" && $to_date !=""){
			$Psql.=" AND create_date <= '$to_date'";
		}elseif($from_date!="" && $to_date !=""){
			$Psql.=" AND create_date BETWEEN '$from_date' AND '$to_date'";
		}	
		$pres = mysql_query($Psql);
		$prow = mysql_fetch_object($pres);
		$instock+= $prow->in_qty;
		}
	}
	return $instock;
}
function getTotalCatagoryOutStockQty($catagory,$project_id){	 
	$product_type= getRequest('product_type'); 
	$from_date 	 = formatDate(getRequest('date_from'));
	$to_date 	 = formatDate(getRequest('date_to'));
	$outstock=0;
    if($from_date==""){   
		$Psql		= "SELECT SUM(outstock) as outstock FROM ".PRODUCT_STATUS_BY_CATAGORY_VIEW." WHERE catagory='$catagory' AND project_id='$project_id' ";
		if($product_type!=""){$Psql.=" AND product_type='$product_type'";}
		$pres 		= mysql_query($Psql);
		$prow 		= mysql_fetch_object($pres);
		$outstock 	= $prow->outstock;
	}else{
		$csql= "SELECT * FROM ".PRODUCT_TBL." WHERE catagory = '$catagory'";
		if($product_type!=""){$csql.=" AND product_type='$product_type'";}
		$cres = mysql_query($csql);		
		while($crow = mysql_fetch_object($cres)){
		$Psql= "SELECT SUM(cr) AS out_qty FROM ".STOCK_LEDGER_TBL." WHERE product_id='$crow->product_id' AND project_id='$project_id' ";
		if($from_date!="" && $to_date ==""){
			$Psql.=" AND create_date >= '$from_date'";
		}elseif($from_date=="" && $to_date !=""){
			$Psql.=" AND create_date <= '$to_date'";
		}elseif($from_date!="" && $to_date !=""){
			$Psql.=" AND create_date BETWEEN '$from_date' AND '$to_date'";
		}	
		$pres = mysql_query($Psql);
		$prow = mysql_fetch_object($pres);
		$outstock+= $prow->out_qty;
		}
	}
	return $outstock;
}
function getTotalProductOBQty($product_id,$project_id){	
	$product_type= getRequest('product_type'); 
	$from_date 	 = formatDate(getRequest('date_from'));
	$to_date 	 = formatDate(getRequest('date_to'));
	$op_qty=0;
    if($from_date==""){   
		$Psql="SELECT op_qty FROM ".PRODUCT_STATUS_BY_CATAGORY_VIEW." WHERE product_id='$product_id' AND project_id='$project_id' ";
		$pres 	= mysql_query($Psql);
		$prow 	= mysql_fetch_object($pres);
		$op_qty   = $prow->op_qty;	
	}else{		
		$Psql= "SELECT (SUM(dr)- SUM(cr)) AS op_qty FROM ".STOCK_LEDGER_TBL." WHERE product_id='$product_id' AND project_id='$project_id' ";	
		if($from_date!="" && $to_date ==""){
			$Psql.=" AND create_date < '$from_date'";
		}	
		$pres = mysql_query($Psql); 
		$prow = mysql_fetch_object($pres);
		$op_qty = $prow->op_qty;
		
	}
	return $op_qty;
}
function getTotalProductInStockQty($product_id,$project_id){	
	$product_type= getRequest('product_type'); 
	$from_date 	 = formatDate(getRequest('date_from'));
	$to_date 	 = formatDate(getRequest('date_to'));
	$instock=0;
    if($from_date==""){   
		$Psql="SELECT SUM(op_qty) as op_qty,SUM(instock) as instock FROM ".STOCK_STATUS_BY_DATE_VIEW." WHERE product_id='$product_id' AND project_id='$project_id' ";
		$pres 	= mysql_query($Psql);
		$prow 	= mysql_fetch_object($pres);
		$instock   = ($prow->instock-$prow->op_qty);	
	}else{		
		$Psql= "SELECT SUM(dr) AS instock FROM ".STOCK_LEDGER_TBL." WHERE product_id='$product_id' AND project_id='$project_id' ";	
		if($from_date!="" && $to_date ==""){
			$Psql.=" AND create_date >= '$from_date'";
		}elseif($from_date=="" && $to_date !=""){
			$Psql.=" AND create_date <= '$to_date'";
		}elseif($from_date!="" && $to_date !=""){
			$Psql.=" AND create_date BETWEEN '$from_date' AND '$to_date'";
		}	//echo $Psql;
		$pres = mysql_query($Psql);
		$prow = mysql_fetch_object($pres);
		$instock = $prow->instock;
		
	}
	return $instock;
}
function getTotalProductOutStockQty($product_id,$project_id){	 
	$product_type= getRequest('product_type'); 
	$from_date 	 = formatDate(getRequest('date_from'));
	$to_date 	 = formatDate(getRequest('date_to'));
	$outstock=0;
    if($from_date==""){   
		$Psql= "SELECT SUM(cr) AS out_qty FROM ".STOCK_LEDGER_TBL." WHERE product_id='$product_id' AND project_id='$project_id' ";
		$pres 		= mysql_query($Psql);
		$prow 		= mysql_fetch_object($pres);
		$outstock 	= $prow->out_qty;
	}else{		
		$Psql= "SELECT SUM(cr) AS out_qty FROM ".STOCK_LEDGER_TBL." WHERE product_id='$product_id' AND project_id='$project_id' ";
		if($from_date!="" && $to_date ==""){
			$Psql.=" AND create_date >= '$from_date'";
		}elseif($from_date=="" && $to_date !=""){
			$Psql.=" AND create_date <= '$to_date'";
		}elseif($from_date!="" && $to_date !=""){
			$Psql.=" AND create_date BETWEEN '$from_date' AND '$to_date'";
		}	
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
	$opening_balance=0; $ob_dr=0; $ob_cr=0;
	if(($from_date=="") || ($from_date=="2014/01/01" || $from_date=="2015/01/01")){
		//======== Total Dr OB ======
		$dsql= "SELECT SUM(opening_balance) AS ob_dr FROM ".CUSTOMER_OB_VIEW." WHERE division_id='$division_id' AND project_id='$project_id' AND op_type='Dr'";
		if($area!=""){
			$dsql.=" AND area_id='$area' ";
		}
		if($trt!=""){
			$dsql.=" AND trt_id='$trt' ";
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
		$csql= "SELECT SUM(opening_balance) AS ob_cr FROM ".CUSTOMER_OB_VIEW." WHERE division_id='$division_id' AND project_id='$project_id' AND op_type='Cr'";
		if($area!=""){
			$csql.=" AND area_id='$area' ";
		}
		if($trt!=""){
			$csql.=" AND trt_id='$trt' ";
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
		$Psql= "SELECT (SUM(dr) - SUM(cr) )AS customer_ob FROM ".CUSTOMER_LEDGER_VIEW." WHERE division_id='$division_id' AND project_id='$project_id'";
		if($from_date!=""){
			$Psql.=" AND created_date < '$from_date'";
		}		
		if($area!=""){
			$Psql.=" AND area_id='$area' ";
		}
		if($trt!=""){
			$Psql.=" AND trt_id='$trt' ";
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
function getTotalDivisionSalesAmount($division_id,$project_id){	 
	$from_date 	 = formatDate(getRequest('date_from'));
	$to_date 	 = formatDate(getRequest('date_to'));
	$area 	 	= getRequest('district');
	$trt 	 	= getRequest('area');
	$sales_amount=0; 	
	$Psql= "SELECT SUM(sales_amount) AS sales_amount FROM ".CUSTOMER_SALES_LEDGER_VIEW." WHERE division_id='$division_id' AND project_id='$project_id'";	
	
	if($area!=""){
		$Psql.=" AND area_id='$area' ";
	}
	if($trt!=""){
		$Psql.=" AND trt_id='$trt' ";
	}		
	if($sales_type!=""){
		$Psql.=" AND sales_type = '$sales_type'";
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
	// SELECT SUM( dr ) AS sales FROM `vw_customer_ledger` WHERE `area_id` =1 AND `description` = "OS" 	
	return $sales_amount; 
}
function getTotalDivisionReceiptAmount($division_id,$project_id){	 
	$from_date 	= formatDate(getRequest('date_from'));
	$to_date 	= formatDate(getRequest('date_to'));
	$area 	 	= getRequest('district');
	$trt 	 	= getRequest('area');
	$receipt_amount=0; 	$return_amount=0;
	//======= Get Total Receipt Amount =========
	$Psql= "SELECT SUM(cr) AS receipt_amount FROM ".CUSTOMER_LEDGER_VIEW." WHERE division_id='$division_id' AND project_id='$project_id' AND cr >0 AND description!='OB'";
	if($area!=""){
		$Psql.=" AND area_id='$area' ";
	}
	if($trt!=""){
		$Psql.=" AND trt_id='$trt' ";
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
	$Rsql= "SELECT SUM(return_amount) AS return_amount FROM ".CUSTOMER_SALES_RETURN_LEDGER_VIEW." WHERE division_id='$division_id' AND project_id='$project_id'";
	if($area!=""){
		$Rsql.=" AND area_id='$area' ";
	}
	if($trt!=""){
		$Rsql.=" AND trt_id='$trt' ";
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
function getTotalDivisionBedDebtAmount($division_id,$project_id){	 
	$from_date 	 = formatDate(getRequest('date_from'));
	$to_date 	 = formatDate(getRequest('date_to'));
	$BedDebtAmount=0;
	//======= Get Total BedDebt Amount =========
	$Psql= "SELECT SUM(return_amount) AS BedDebtAmount FROM ".CUSTOMER_SALES_RETURN_DETAILS_VIEW." WHERE division_id='$division_id' AND project_id='$project_id' 
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
function getTotalDivisionSalesReturnAmount($division_id,$project_id){	 
	$from_date 	 = formatDate(getRequest('date_from'));
	$to_date 	 = formatDate(getRequest('date_to'));
	$SalesReturn=0;
	//======= Get Total BedDebt Amount =========
	$Psql= "SELECT SUM(return_amount) AS SalesReturn FROM ".CUSTOMER_SALES_RETURN_DETAILS_VIEW." WHERE division_id='$division_id' AND project_id='$project_id' 
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
function getTotalDivisionClosingAmount($division_id,$project_id){	 
	$from_date 	 = formatDate(getRequest('date_from'));
	$to_date 	 = formatDate(getRequest('date_to'));
	$ClosingBalance=0;
	//======= Get Total BedDebt Amount =========
	$Psql= "SELECT (SUM(dr)-SUM(cr)) AS ClosingBalance FROM ".CUSTOMER_LEDGER_VIEW." WHERE division_id='$division_id' AND project_id='$project_id' ";	
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
	$from_date 	 = formatDate(getRequest('date_from'));
	$to_date 	 = formatDate(getRequest('date_to'));
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
function getTotalAreaSalesAmount($district_id,$project_id){	 
	$from_date 	 = formatDate(getRequest('date_from'));
	$to_date 	 = formatDate(getRequest('date_to'));
	$sales_amount=0; 	
	$Psql= "SELECT SUM(sales_amount) AS sales_amount FROM ".CUSTOMER_SALES_LEDGER_VIEW." WHERE area_id='$district_id' AND project_id='$project_id'";	
	if($sales_type!=""){
		$Psql.=" AND sales_type = '$sales_type'";
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
	// SELECT SUM( dr ) AS sales FROM `vw_customer_ledger` WHERE `area_id` =1 AND `description` = "OS" 	
	return $sales_amount; 
}
function getTotalAreaReceiptAmount($district_id,$project_id){	 
	$from_date 	 = formatDate(getRequest('date_from'));
	$to_date 	 = formatDate(getRequest('date_to'));
	$receipt_amount=0; 	$return_amount=0;
	//======= Get Total Receipt Amount =========
	$Psql= "SELECT SUM(cr) AS receipt_amount FROM ".CUSTOMER_LEDGER_VIEW." WHERE area_id='$district_id' AND project_id='$project_id' AND cr >0 AND description!='OB'";	
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
	$Rsql= "SELECT SUM(return_amount) AS return_amount FROM ".CUSTOMER_SALES_RETURN_LEDGER_VIEW." WHERE area_id='$district_id' AND project_id='$project_id'";	
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
function getTotalAreaBedDebtAmount($district_id,$project_id){	 
	$from_date 	 = formatDate(getRequest('date_from'));
	$to_date 	 = formatDate(getRequest('date_to'));
	$BedDebtAmount=0;
	//======= Get Total BedDebt Amount =========
	$Psql= "SELECT SUM(return_amount) AS BedDebtAmount FROM ".CUSTOMER_SALES_RETURN_DETAILS_VIEW." WHERE area_id='$district_id' AND project_id='$project_id' 
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
function getTotalAreaClosingAmount($district_id,$project_id){	 
	$from_date 	 = formatDate(getRequest('date_from'));
	$to_date 	 = formatDate(getRequest('date_to'));
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

//====== All TRT Function =========
function getTotalTRTOB($trt_id,$project_id){	 
	$from_date 	 = formatDate(getRequest('date_from'));
	$to_date 	 = formatDate(getRequest('date_to'));
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
function getTotalTRTSalesAmount($trt_id,$project_id){	 
	$from_date 	 = formatDate(getRequest('date_from'));
	$to_date 	 = formatDate(getRequest('date_to'));
	$sales_amount=0; 	
	$Psql= "SELECT SUM(sales_amount) AS sales_amount FROM ".CUSTOMER_SALES_LEDGER_VIEW." WHERE trt_id='$trt_id' AND project_id='$project_id'";	
	if($sales_type!=""){
		$Psql.=" AND sales_type = '$sales_type'";
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
	// SELECT SUM( dr ) AS sales FROM `vw_customer_ledger` WHERE `area_id` =1 AND `description` = "OS" 	
	return $sales_amount; 
}
function getTotalTRTReceiptAmount($trt_id,$project_id){	 
	$from_date 	 = formatDate(getRequest('date_from'));
	$to_date 	 = formatDate(getRequest('date_to'));
	$receipt_amount=0; 	$return_amount=0;
	//======= Get Total Receipt Amount =========
	$Psql= "SELECT SUM(cr) AS receipt_amount FROM ".CUSTOMER_LEDGER_VIEW." WHERE trt_id='$trt_id' AND project_id='$project_id' AND cr >0 AND description!='OB'";	
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
	//$Rsql= "SELECT SUM(return_amount) AS return_amount FROM ".CUSTOMER_SALES_RETURN_LEDGER_VIEW." WHERE trt_id='$trt_id' AND project_id='$project_id'";
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
function getTotalTRTBedDebtAmount($trt_id,$project_id){	 
	$from_date 	 = formatDate(getRequest('date_from'));
	$to_date 	 = formatDate(getRequest('date_to'));
	$BedDebtAmount=0;
	//======= Get Total BedDebt Amount =========
	$Psql= "SELECT SUM(return_amount) AS BedDebtAmount FROM ".CUSTOMER_SALES_RETURN_DETAILS_VIEW." WHERE trt_id='$trt_id' AND project_id='$project_id' 
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
function getTotalTRTSalesReturnAmount($trt_id,$project_id){	 
	$from_date 	 = formatDate(getRequest('date_from'));
	$to_date 	 = formatDate(getRequest('date_to'));
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

//===== All Customer Function =======
function getCustomerTotalOB($customer_id,$project_id){	 
	$from_date 	 = formatDate(getRequest('date_from'));
	$to_date 	 = formatDate(getRequest('date_to'));
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
function getCustomerTotalSalesAmount($customer_id,$project_id){	 
	$from_date 	 = formatDate(getRequest('date_from'));
	$to_date 	 = formatDate(getRequest('date_to'));
	$sales_amount=0;
	$Psql= "SELECT SUM(sales_amount) AS sales_amount FROM ".CUSTOMER_SALES_LEDGER_VIEW." WHERE `sub_id`='$customer_id' AND project_id='$project_id' ";
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
	return $sales_amount;
}

function getCustomerTotalReceipt($customer_id,$project_id){	 
	$from_date 	 = formatDate(getRequest('date_from'));
	$to_date 	 = formatDate(getRequest('date_to'));
	$receipt_amount=0;
	$Psql= "SELECT SUM(cr) AS receipt_amount FROM ".ACCOUNT_JOURNAL_TBL." WHERE sub_id='$customer_id' AND project_id='$project_id' AND cr >0 AND description!='OB'";
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
	$ClosingBalance=0;
	//======= Get Total BedDebt Amount =========
	$Psql= "SELECT (SUM(dr)-SUM(cr)) AS ClosingBalance FROM ".CUSTOMER_LEDGER_VIEW." WHERE sub_id='$sub_id' AND project_id='$project_id' ";	
	if($sales_type!=""){
		$Psql.=" AND sales_type = '$sales_type'";
	}
	if($to_date !=""){
		$Psql.=" AND created_date <= '$to_date'";
	}
	/*
	if($from_date!="" && $to_date ==""){
		$Psql.=" AND created_date >= '$from_date'";
	}elseif($from_date=="" && $to_date !=""){
		$Psql.=" AND created_date <= '$to_date'";
	}elseif($from_date!="" && $to_date !=""){
		$Psql.=" AND created_date BETWEEN '$from_date' AND '$to_date'";
	}
	*/
	$pres = mysql_query($Psql);
	$cnum = mysql_num_rows($pres);
	if($cnum>0){
	$prow = mysql_fetch_object($pres);
	$ClosingBalance = $prow->ClosingBalance;
	}	
	return $ClosingBalance; 
}

?>