<?php


function SaveActivityLog($activity_type,$voucher_no,$perform_type,$perform_by,$befoure_change=0,$after_change=0){
	$isql = "INSERT INTO ".ACTIVITY_LOG_TBL." (activity_type,voucher_no,befoure_change,after_change,perform_type,perform_by) VALUES('$activity_type','$voucher_no','$befoure_change','$after_change','$perform_type','$perform_by')";
	mysql_query($isql);
}


function getPartyDetails($customer_id){
	require_once(CLASS_DIR . '/common.list.class.php');

	$sql = "SELECT 
				c.sub_head_name,
				c.code,
				c.head_details,
				dv.division_name_eng,
				d.district_name,
				a.area_name
			FROM ".SUB_ACC_HEAD_TBL." c
			LEFT JOIN ".DISTRICT_TBL." d ON c.district = d.district_id
			LEFT JOIN ".DIVISION_TBL." dv ON d.division_id = dv.division_id
			LEFT JOIN ".AREA_TBL." a ON c.area = a.area_id
			WHERE c.sub_id = '$customer_id'";

	$res = mysql_query($sql);

	if(mysql_num_rows($res) > 0){

		$crow = mysql_fetch_object($res);
		$PartyDetails = "";
		$PartyDetails = (new CommonList())->normalizeUserName($crow->code, $crow->sub_head_name, "-") . "<br>";

		$parts = [];

		if(!empty($crow->head_details)) $PartyDetails .= "Address: ".$crow->head_details . "<br>";
		if(!empty($crow->district_name)) $parts[] = $crow->district_name;
		if(!empty($crow->area_name)) $parts[] = $crow->area_name;

		$PartyDetails .= implode(", ", $parts);

		return $PartyDetails;

	}else{

		$sql = "SELECT 
					c.name AS sub_head_name,
					c.address AS head_details,
					dv.division_name_eng,
					d.district_name,
					a.area_name
				FROM ".SUPPLIER_TBL." c
				LEFT JOIN ".DISTRICT_TBL." d ON c.district = d.district_id
				LEFT JOIN ".DIVISION_TBL." dv ON d.division_id = dv.division_id
				LEFT JOIN ".AREA_TBL." a ON c.area = a.area_id
				WHERE c.supplier_code = '$customer_id'";

		$res = mysql_query($sql);

		if(mysql_num_rows($res) > 0){

			$crow = mysql_fetch_object($res);
			$PartyDetails = "";
			$PartyDetails = $crow->sub_head_name . "<br>";

			$parts = [];

			if(!empty($crow->head_details)) $PartyDetails .= $crow->head_details . "<br>";
			if(!empty($crow->district_name)) $parts[] = $crow->district_name;
			if(!empty($crow->area_name)) $parts[] = $crow->area_name;

			$PartyDetails .= implode(", ", $parts);

			return $PartyDetails;
		}
	}
}


function getPartyDetailsOld($customer_id){
	require_once(CLASS_DIR . '/common.list.class.php');
	$sql="SELECT c.sub_head_name,c.code,c.head_details,dv.division_name_eng,d.district_name,a.area_name FROM ".SUB_ACC_HEAD_TBL." as c,".DIVISION_TBL." as dv,".DISTRICT_TBL." as d,".AREA_TBL." as a WHERE c.district=d.district_id AND d.division_id=dv.division_id AND c.area=a.area_id AND c.sub_id='$customer_id'";
   	$res = mysql_query($sql);
	if(mysql_num_rows($res) >0){
		$crow = mysql_fetch_object($res);
		$PartyDetails="";
		$PartyDetails= (new CommonList())->normalizeUserName($crow->code, $crow->sub_head_name,"-")."<br>";
		if($crow->head_details !=""){
			$PartyDetails.= $crow->head_details.", ".$crow->district_name.", ".$crow->area_name;
		}else{
			$PartyDetails.= $crow->district_name.", ".$crow->area_name;
		}
		return $PartyDetails;
	}else{ 
		$sql="SELECT c.name as sub_head_name,c.address as head_details,dv.division_name_eng,d.district_name,a.area_name FROM ".SUPPLIER_TBL." as c,".DIVISION_TBL." as dv,".DISTRICT_TBL." as d,".AREA_TBL." as a WHERE c.district=d.district_id AND d.division_id=dv.division_id AND c.area=a.area_id AND c.supplier_code='$customer_id'";
		$res = mysql_query($sql);
		if(mysql_num_rows($res)>0){
			$crow = mysql_fetch_object($res);
			$PartyDetails="";
			$PartyDetails= $crow->sub_head_name."<br>";
			if($crow->head_details !=""){
				$PartyDetails.= $crow->head_details.", ".$crow->district_name.", ".$crow->area_name;
			}else{
				$PartyDetails.= $crow->district_name.", ".$crow->area_name;
			}
			return $PartyDetails;
		}
	} 
}
function getPerviousBalance($account_id,$from_date){
	require_once(CLASS_DIR.'/common.list.class.php');	
	$comlistApp 	= new CommonList();
	$totalCrAmount  = $comlistApp->getTotalCreditAmountByDate($account_id,getFromSession('project_id'),$from_date);
	$totalDrAmount  = $comlistApp->getTotalDebitAmountByDate($account_id,getFromSession('project_id'),$from_date);					 
	$PrvBalance  	= ($totalDrAmount-$totalCrAmount);
	return $PrvBalance;
}
function getStockBalance($project_id,$fyear=0){
	require_once(CLASS_DIR.'/common.list.class.php');	
	$comlistApp 	= new CommonList();
	$StockId 	= $comlistApp->getStockId(getFromSession('project_id'));
	$totalStockCr  	= $comlistApp->getTotalCreditAmount($StockId,getFromSession('project_id'));
	$totalStockDr  	= $comlistApp->getTotalDebitAmount($StockId,getFromSession('project_id'));		 
	$StockBalance  	= ($totalStockDr-$totalStockCr);
	return $StockBalance;
}
function getTotalDirectIncome($project_id,$fyear){
	require_once(CLASS_DIR.'/common.list.class.php');	
	$comlistApp 		= new CommonList();
   	$SalesIncomeId 	 	= $comlistApp->getProductSalesIncomeId(getFromSession('project_id'));
	$totalSalesIncomeCR = $comlistApp->getTotalCreditAmount($SalesIncomeId,getFromSession('project_id'));
	$totalSalesIncomeDR = $comlistApp->getTotalDebitAmount($SalesIncomeId,getFromSession('project_id'));					 
	$SalesIncomeBalance = ($totalSalesIncomeDR-$totalSalesIncomeCR);
	return $SalesIncomeBalance;
}
function getRunningProductionAmount($project_id){
	$prom_sql = "SELECT total_value,production_amount FROM ".PRODUCTION_MASTER_TBL." WHERE `project_id` = '$project_id' AND production_type='Running'";
	$prom_res = mysql_query($prom_sql);		
	$prom_num = mysql_num_rows($prom_res);
	$totalOutValue =0; $totalFinishValue =0; $runningProduction=0;
	if($prom_num>0){			
		while($prom_row = mysql_fetch_object($prom_res)){
			$totalOutValue +=$prom_row->total_value;
			$totalFinishValue +=$prom_row->production_amount;
		}
		$runningProduction = ($totalOutValue-$production_amount);
	}
	return $runningProduction;
} 
function getFinishProductionAmount($project_id){
	$prom_sql = "SELECT SUM(total_value) AS production_amount FROM ".PRODUCTION_FG_TBL." WHERE `project_id` = '$project_id' AND production_type='Finish'";
	$prom_res = mysql_query($prom_sql);		
	$prom_num = mysql_num_rows($prom_res);
	$totalFinishValue =0; $runningProduction=0;
	if($prom_num>0){			
		$prom_row = mysql_fetch_object($prom_res);
		$totalFinishValue =$prom_row->production_amount;
	}
	
	$prom_sql = "SELECT SUM(total_value) as total_value FROM ".PRODUCTION_MASTER_TBL." WHERE `project_id` = '$project_id' AND production_type='Running'";
	$prom_res = mysql_query($prom_sql);		
	$prom_num = mysql_num_rows($prom_res);
	$totalOutValue =0; 
	if($prom_num>0){			
		$prom_row = mysql_fetch_object($prom_res);
		$totalOutValue =$prom_row->total_value;
	} 
	$runningProduction = ($totalOutValue-$totalFinishValue);
	return $runningProduction;
}
function getStockQty($brand_id,$product){
	$project_id = getFromSession('project_id');
	$Sql="SELECT SUM(stock_qty) as stock_qty FROM ".PURCHASE_ITEM_VIEW." WHERE `project_id`='$project_id' AND brand_id='$brand_id' AND product='$product'";
	$crow = mysql_fetch_object(mysql_query($Sql));
	return $crow->stock_qty;
}
function getStoreStockQty($store_id,$product){
	$project_id = getFromSession('project_id');
	$Sql="SELECT balance as stock_qty FROM ".STORE_STOCK_VIEW." WHERE `project_id`='$project_id' AND store_id='$store_id' AND product_id='$product'";
	$crow = mysql_fetch_object(mysql_query($Sql));
	return $crow->stock_qty;
}
function getDeliveryPointName($delivery_pid){
	$delivery_point_name="";
	if($delivery_pid!=""){
	$getSql	= "SELECT * FROM ".DELIVERY_POINT_TBL." WHERE delivery_pid = '".$delivery_pid."'";
	$gres = mysql_query($getSql);
	$tfrow = mysql_fetch_object($gres);
	$delivery_point_name = $tfrow->delivery_point_name;
	if($tfrow->details!=""){ $delivery_point_name.="<br>".$tfrow->details;}
	}
	return $delivery_point_name;
}
function getCustomerName($customer_id){ 
	require_once(CLASS_DIR . '/common.list.class.php');
   	$crow = mysql_fetch_object(mysql_query("SELECT sub_head_name,code FROM ".SUB_ACC_HEAD_TBL." WHERE sub_id='$customer_id'"));
	return (new CommonList())->normalizeUserName($crow->code, $crow->sub_head_name, "-");
}

function getRetailerName($retailer_id)
{
	require_once(CLASS_DIR . '/common.list.class.php');
	$crow = mysql_fetch_object(mysql_query("SELECT retailer_name,address,mobile,code  FROM " . RETAILER_TBL . " WHERE retailer_id='$retailer_id'"));
    	return (new CommonList())->normalizeUserName($crow->code, $crow->retailer_name, "-");
}

function getRetailerMobile($retailer_id){ 
   	$crow = mysql_fetch_object(mysql_query("SELECT retailer_name,address,mobile  FROM ".RETAILER_TBL." WHERE retailer_id='$retailer_id'"));	
	
	return $crow->mobile;	
}
function getRetailerDetails($retailer_id){ 
   	$crow = mysql_fetch_object(mysql_query("SELECT retailer_name,address,mobile  FROM ".RETAILER_TBL." WHERE retailer_id='$retailer_id'"));
	$retailer_details ="";
	if($crow->address!=""){ $retailer_details =  $crow->address;}
	return $retailer_details;	
}
function getCustomerAreaTRT($customer_id){
	$sql="SELECT dv.division_name_eng,d.district_name,a.area_name FROM ".SUB_ACC_HEAD_TBL." as c,".DIVISION_TBL." as dv,".DISTRICT_TBL." as d,".AREA_TBL." as a WHERE c.district=d.district_id AND d.division_id=dv.division_id AND c.area=a.area_id AND c.sub_id='$customer_id'";
   	$res = mysql_query($sql);
	if(mysql_num_rows($res)>0){
	$crow = mysql_fetch_object($res);
	return $crow->division_name_eng.", ".$crow->district_name.", ".$crow->area_name;
	}else{ return false;} 
}
function updateServicingCustomerName($customer_id,$warranty_id){
   	$crow = mysql_fetch_object(mysql_query("SELECT sub_head_name FROM ".SUB_ACC_HEAD_TBL." WHERE sub_id='$customer_id'"));
	$customer_name = $crow->sub_head_name;
	$sql = "UPDATE ".WARRANTY_TBL." SET customer_name='$customer_name' WHERE status=0 AND warranty_id=".$warranty_id;
	mysql_query($sql);
} 
function getPProductSerial($voucher_no,$product){
	$sql ="SELECT serial FROM ".PURCHASE_DETAILS_TBL." WHERE voucher_no='$voucher_no' AND product='$product'";
	$res = mysql_query($sql);
	$j=0;
   	while($crow = mysql_fetch_object($res)){
		if($j==0){
			if($crow->serial!=""){
			$str = $crow->serial; $j++;
			}
		}else{
			if($crow->serial!=""){
			$str.= ", ".$crow->serial; $j++;
			}
		}
	}
	return $str;
}
function getSProductSerial($voucher_no,$product){
	$sql ="SELECT serial FROM ".SALES_DETAILS_TBL." WHERE voucher_no='$voucher_no' AND product='$product'";
	$res = mysql_query($sql);
	$j=0;
   	while($crow = mysql_fetch_object($res)){
		if($j==0){
			if($crow->serial!=""){
			$str = $crow->serial; $j++;
			}
		}else{
			if($crow->serial!=""){
			$str.= ", ".$crow->serial; $j++;
			}
		}
	}
	return $str;
}
function getSalesRefName($customer_id){
   	$crow = mysql_fetch_object(mysql_query("SELECT sub_head_name,mobile FROM ".SUB_ACC_HEAD_TBL." WHERE sub_id='$customer_id'"));
	$str = $crow->sub_head_name;
	if($crow->mobile!=""){
	$str.= ", ".$crow->mobile;
	}
	return $str;
} 
function getSalesRefID($customer_id){
   	$crow = mysql_fetch_object(mysql_query("SELECT employee_id,DATE_FORMAT(joining_date,'%d %b %Y' ) as joining_date FROM ".SUB_ACC_HEAD_TBL." WHERE sub_id='$customer_id'"));
	$str = $crow->employee_id;
	if($crow->joining_date!="0000-00-00"){
	$str.= "/".$crow->joining_date;
	}
	return $str;
}
function getHeadType($head_id){
	$project_id = getFromSession('project_id');   
	$rsql= "SELECT head_type,sub_headtype,child_head,sl_three_head FROM ".SUB_ACC_HEAD_TBL." WHERE sub_id='".$head_id."' AND project_id = '$project_id'";  
	$rres = mysql_query($rsql);
	$hnum = mysql_num_rows($rres);
	if($hnum >0){ 
		$hrow = mysql_fetch_object($rres);
		$headtype	= $hrow->head_type;
		$sub_headtype	= $hrow->sub_headtype;
		$child_head	= $hrow->child_head;
		$sl_three_head	= $hrow->sl_three_head;
		if($headtype=="Current Assets" && $sub_headtype=="S128" && $child_head=="C000105"){
		$head_type= "Customer";
		}elseif($headtype=="Current Liabilities" && $sub_headtype=="S137" && $child_head=="C000116"){
		$head_type= "Supplier";
		}elseif($headtype=="Current Assets" && $sub_headtype=="S130" && $child_head=="C000103"){
		$head_type= "Bank";
		}elseif($headtype=="Current Assets" && $sub_headtype=="S130" && $child_head=="C000064"){
		$head_type= "Cash";
		}else{
		$head_type	= $hrow->head_type;
		}
		
	}else{
		$rsql= "SELECT retailer_name FROM ".RETAILER_TBL." WHERE retailer_id  = '".$head_id."' AND project_id = '$project_id'";
		$rres = mysql_query($rsql);
		$rnum = mysql_num_rows($rres);
		if($rnum >0){
			$head_type= "Retailer";
		}else{
			$head_type= "";
		}
	}
	return $head_type;
}
function getTotalPurchaseAmount($project_id,$fyear){
   	$crow = mysql_fetch_object(mysql_query("SELECT SUM(purchase_price) as TotalPurchase FROM ".SALES_DETAILS_TBL." WHERE project_id='$project_id' AND fyear='$fyear'"));
	if($crow->TotalPurchase!=""){
	$TotalPurchase = $crow->TotalPurchase;
	}else{
	$TotalPurchase = 0;
	}
	return $TotalPurchase;
}
function getTotalSalesAmount($project_id,$fyear){
   	$crow = mysql_fetch_object(mysql_query("SELECT SUM(unit_price) as TotalSales FROM ".SALES_DETAILS_TBL." WHERE project_id='$project_id' AND fyear='$fyear'"));
	if($crow->TotalSales!=""){
	$TotalSales = $crow->TotalSales;
	}else{
	$TotalSales = 0;
	}
	return $TotalSales;
}
function getTotalExpanceAmount($project_id,$fyear){
	$GTExpence = 0;
	$hsql = "SELECT sub_id,head_type FROM ".SUB_ACC_HEAD_TBL." WHERE project_id='$project_id' AND head_type='Administrative Cost' OR head_type='Overhead Cost' OR head_type='Staff' OR head_type='Reference'";
	$hres = mysql_query($hsql);
   	while($hrow = mysql_fetch_object($hres)){	
		$acc_id = $hrow->sub_id;
		$esql = "SELECT SUM(dr) as TotalExp FROM ".ACCOUNT_JOURNAL_TBL." WHERE project_id='$project_id' AND sub_id='$acc_id' AND fyear='$fyear'";
		$eres = mysql_query($esql);
		if(mysql_num_rows($eres)>0){
		$erow = mysql_fetch_object($eres);		
		$GTExpence+= $erow->TotalExp;
		}
	}
	return $GTExpence;
} 
function getDistrictName($district_id){
   	$crow = mysql_fetch_object(mysql_query("SELECT district_name FROM ".DISTRICT_TBL." WHERE district_id='$district_id'"));
	$str = $crow->district_name;
	return $str;
}
//========= produce iFrame for dtPicker=============

 function dateIFrame($PATH)
 {
	$html .= "<iframe width=174 height=189 name='gToday:normal:agenda.js' id='gToday:normal:agenda.js' ";
	$html .= " src='".$PATH."/date/ipopeng.htm' ";
	$html .= " scrolling='no' frameborder='0' "; 
	$html .= "style='visibility:visible; z-index:999; position:absolute; left:-500px; top:0px;'> ";
	$html .= "</iframe>";
	echo $html;
 }
  //=============function dateFormat()=========================
   function formatDate($dt)
  {
  	if(trim($dt))
  	{
  	$munite="";	//echo $dt; 01-02-2007 09:08:00 PM
    	$day   = substr($dt,0,2);
    	$month = substr($dt,3,2);
    	$year  = substr($dt,6,4);
    	$hour	 = substr($dt,11,2);
    	$minute = substr($dt,14,2);
    	$second = substr($dt,17,2);
    	$ampm		= substr($dt,20,2);
    	//echo $ampm;
    	if($hour=='' AND $munite=='' AND $second=='')
    	{
    		return $year."/".$month."/".$day;	
    	}
    	else
    	{
    	if(strtoupper($ampm) == 'PM')
    	{
    		$hour = intval($hour)+12;
    		return $year."/".$month."/".$day.' '.$hour.':'.$minute.':'.$second;
    	}
    	else
    	{
    		return $year."/".$month."/".$day.' '.$hour.':'.$minute.':'.$second;
    	}	
    		
    	}
    	
    }
  }
 function formatDateDMY($val)
	{
		if($val)
		{
			$yy = substr($val,0,4);
			$mm = substr($val,5,2);
			$dd = substr($val,8,2);
			return $dd.'/'.$mm.'/'.$yy;
		}
 }
 function formatDate4Display($val)
 { 
	$drow = mysql_fetch_object(mysql_query("SELECT DATE_FORMAT('$val','%d %b %Y' ) as dval")); 
	return $drow->dval;		
 }
 function dateInputFormatDMY($val)
 {
	if($val)
	{
		$yy = substr($val,0,4);
		$mm = substr($val,5,2);
		$dd = substr($val,8,2);
		return $dd.'-'.$mm.'-'.$yy;
	}
 }

function dateInputFormatYMD($val)
{
	if ($val) {
	    $date = DateTime::createFromFormat('d-m-Y', $val);
	    $formattedDate = $date->format('Y-m-d');

	    return $formattedDate;
	}
}

  //============function regCodeGeneration()==================== 
  function regCodeGeneration()
	{
		return rand(100000,999999);
	}

   function getUserName_PhotoPath_Email($TBL, $WHERE)
   {
    	$data 				= array();
      $info         = array();
		
   		$info['table']= $TBL;
	 		$info['where']= $WHERE;

	 		$info['fields'] = array('firstname','middlename','lastname','photopath','email');
   		$res = select($info);
      if(count($res))
      {
     		foreach($res as $k => $v)
     		{
     			$data[$k][] = $v;
     		}
  		}   	
      foreach($data as $k => $v)
      {
      	$namePhotoEmail .= $v[0]->firstname.' '.$v[0]->middlename.' '.$v[0]->lastname.'###'.$v[0]->photopath.'###'.$v[0]->email;
      }
   	return $namePhotoEmail;	
   }

   function msg()
   {
    	if(getRequest('msg'))
    	{
    		$custMsg = "&nbsp;".getRequest('msg');
    	}
    	else if (getFromSession('username'))
    	{
    		$custMsg = "&nbsp;Welcome ".getFromSession('username');
    	}
    	echo "<table width='100%' border='0' cellpadding='0' cellspacing='0'>
   		<tr><td><strong>
   		<font color='#FF5500'>".$custMsg."</font></strong></td></tr></table>";
   }

	function generateID($priFix, $maxId, $len)
	{
		$nextIdNum = trim($maxId,$priFix) + 1;
		$padlen = $len - (strlen($priFix) + strlen($nextIdNum)) +1 ;
    	$nextID = str_pad($priFix, $padlen, "0").$nextIdNum;	
		if	(strlen($nextID) <= $len)
			return $nextID;
		else
			return "ID over flow !!!";
   	}

	function format_amount_exact($amount) {
	    // Convert to string
	    $amount = number_format($amount, 2, '.', '');
	    $amount = (string)$amount;

	    // Split integer and decimal
	    if (strpos($amount, '.') !== false) {
		list($int, $dec) = explode('.', $amount);
	    } else {
		$int = $amount;
		$dec = '';
	    }

	    // Ensure 2 decimal digits WITHOUT rounding
	    $dec = substr($dec . '00', 0, 2);

	    return $int . '.' . $dec;
	}

   	
   	function convert_number($number)
	{
		if (($number < 0) || ($number > 999999999))
		{
		throw new Exception("Number is out of range");
		}
		
		$Gn = floor($number / 100000);  /* Millions (giga) */
		$number -= $Gn * 100000;
		$kn = floor($number / 1000);     /* Thousands (kilo) */
		$number -= $kn * 1000;
		$Hn = floor($number / 100);      /* Hundreds (hecto) */
		$number -= $Hn * 100;
		$Dn = floor($number / 10);       /* Tens (deca) */
		$n = $number % 10;               /* Ones */
		
		$res = "";
		
		if ($Gn)
		{
		$res .= convert_number($Gn) . " Lacs";
		}
		
		if ($kn)
		{
		$res .= (empty($res) ? "" : " ") .
		convert_number($kn) . " Thousand";
		}
		
		if ($Hn)
		{
		$res .= (empty($res) ? "" : " ") .
		convert_number($Hn) . " Hundred";
		}
		
			$ones = array(
			    "", "One", "Two", "Three", "Four", "Five", "Six",
			    "Seven", "Eight", "Nine", "Ten", "Eleven", "Twelve", "Thirteen",
			    "Fourteen", "Fifteen", "Sixteen", "Seventeen", "Eighteen",
			    "Nineteen"
			);

			$tens = array(
			    "", "", "Twenty", "Thirty", "Forty", "Fifty", "Sixty",
			    "Seventy", "Eighty", "Ninety"
			);
		
		if ($Dn || $n)
		{
		if (!empty($res))
		{
		$res .= " and ";
		}
		
		if ($Dn < 2)
		{
		$res .= $ones[$Dn * 10 + $n];
		}
		else
		{
		$res .= $tens[$Dn];
		
		if ($n)
		{
		$res .= "-" . $ones[$n];
		}
		}
		}
		
		if (empty($res))
		{
		$res = "zero";
		}
		return $res;
		
	}

?>
