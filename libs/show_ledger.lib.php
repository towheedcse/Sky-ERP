<? 
function getGLOpeningBalance($project_id,$head_type,$subhead_type=NULL,$childheadtype=NULL){
	$from_date = formatDate(getRequest('date_from')); $to_date = formatDate(getRequest('date_to'));
	$totalAmount	= 0;	
	$bsql="SELECT (SUM(a.dr)- SUM(a.cr)) AS balance FROM ".ACCOUNT_JOURNAL_TBL." AS a,".SUB_ACC_HEAD_TBL." AS s WHERE BINARY a.`sub_id` = s.`sub_id` AND s.project_id = '$project_id' AND s.`head_type` = '$head_type' ";
	if($subhead_type!=""){ $bsql.=" AND s.sub_headtype='$subhead_type'"; }	
	if($childheadtype!=""){ $bsql.=" AND s.child_head='$childheadtype'"; }	
	if($from_date!=""){
	$bsql.=" AND a.created_date < '$from_date'";
	}else{
	$from_date = date('Y-m-d');
	$bsql.=" AND a.created_date < '$from_date'";
	}
	$bres= mysql_query($bsql);
	if(mysql_num_rows($bres)>0){
		$brow = mysql_fetch_object($bres);
		if($brow->balance!=""){$totalAmount=$brow->balance;}else{ $totalAmount=0;}			
	}	
	return $totalAmount;	
}
function getGLDrBalance($project_id,$head_type,$subhead_type=NULL,$childheadtype=NULL){
	$from_date = formatDate(getRequest('date_from')); $to_date = formatDate(getRequest('date_to'));
	$totalAmount	= 0;	
	$bsql="SELECT SUM(a.dr) AS balance FROM ".ACCOUNT_JOURNAL_TBL." AS a,".SUB_ACC_HEAD_TBL." AS s WHERE BINARY a.`sub_id` = s.`sub_id` AND s.project_id = '$project_id' AND s.`head_type` = '$head_type' ";
	if($subhead_type!=""){ $bsql.=" AND s.sub_headtype='$subhead_type'"; }	
	if($childheadtype!=""){ $bsql.=" AND s.child_head='$childheadtype'"; }	
	if($from_date!="" && $to_date !=""){
	$bsql.=" AND a.created_date BETWEEN '$from_date' AND '$to_date'";
	}else{
	$from_date = date('Y-m-d');
	$bsql.=" AND a.created_date > '$from_date'";
	}
	$bres= mysql_query($bsql);
	if(mysql_num_rows($bres)>0){
		$brow = mysql_fetch_object($bres);
		if($brow->balance!=""){$totalAmount=$brow->balance;}else{ $totalAmount=0;}			
	}	
	return $totalAmount;	
}

function getGLCrBalance($project_id,$head_type,$subhead_type=NULL,$childheadtype=NULL){
	$from_date = formatDate(getRequest('date_from')); $to_date = formatDate(getRequest('date_to'));
	$totalAmount	= 0;	
	$bsql="SELECT SUM(a.cr) AS balance FROM ".ACCOUNT_JOURNAL_TBL." AS a,".SUB_ACC_HEAD_TBL." AS s WHERE BINARY a.`sub_id` = s.`sub_id` AND s.project_id = '$project_id' AND s.`head_type` = '$head_type' ";
	if($subhead_type!=""){ $bsql.=" AND s.sub_headtype='$subhead_type'"; }	
	if($childheadtype!=""){ $bsql.=" AND s.child_head='$childheadtype'"; }	
	if($from_date!="" && $to_date !=""){
	$bsql.=" AND a.created_date BETWEEN '$from_date' AND '$to_date'";
	}else{
	$from_date = date('Y-m-d');
	$bsql.=" AND a.created_date > '$from_date'";
	}
	$bres= mysql_query($bsql);
	if(mysql_num_rows($bres)>0){
		$brow = mysql_fetch_object($bres);
		if($brow->balance!=""){$totalAmount=$brow->balance;}else{ $totalAmount=0;}			
	}	
	return $totalAmount;	
}
function getGLClosingBalance($project_id,$head_type,$subhead_type=NULL,$childheadtype=NULL){
	$from_date = formatDate(getRequest('date_from')); $to_date = formatDate(getRequest('date_to'));
	$totalAmount	= 0;	
	$bsql="SELECT (SUM(a.dr)- SUM(a.cr)) AS balance FROM ".ACCOUNT_JOURNAL_TBL." AS a,".SUB_ACC_HEAD_TBL." AS s WHERE BINARY a.`sub_id` = s.`sub_id` AND s.project_id = '$project_id' AND s.`head_type` = '$head_type' ";
	if($subhead_type!=""){ $bsql.=" AND s.sub_headtype='$subhead_type'"; }
	if($childheadtype!=""){ $bsql.=" AND s.child_head='$childheadtype'"; }	
	if($from_date!="" && $to_date !=""){
	$bsql.=" AND a.created_date <= '$to_date'";
	}else{
	$to_date = date('Y-m-d');
	$bsql.=" AND a.created_date <= '$to_date'";
	} //echo $bsql;
	$bres= mysql_query($bsql);
	if(mysql_num_rows($bres)>0){
		$brow = mysql_fetch_object($bres);
		if($brow->balance!=""){$totalAmount=$brow->balance;}else{ $totalAmount=0;}			
	}	
	return $totalAmount;	
}

//===== Start SL =======

function getSLOpeningBalance($project_id,$head_type,$subhead_type=NULL,$childheadtype=NULL){
	$from_date = formatDate(getRequest('date_from')); $to_date = formatDate(getRequest('date_to'));
	$totalAmount	= 0;	
	$bsql="SELECT (SUM(a.dr)- SUM(a.cr)) AS balance FROM ".ACCOUNT_JOURNAL_TBL." AS a,".SUB_ACC_HEAD_TBL." AS s WHERE BINARY a.`sub_id` = s.`sub_id` AND s.project_id = '$project_id' AND s.`head_type` = '$head_type' ";
	if($subhead_type!=""){ $bsql.=" AND s.sub_headtype='$subhead_type'"; }	
	if($childheadtype!=""){ $bsql.=" AND s.child_head='$childheadtype'"; }	
	if($from_date!=""){
	$bsql.=" AND a.created_date < '$from_date'";
	}else{
	$from_date = date('Y-m-d');
	$bsql.=" AND a.created_date < '$from_date'";
	}
	$bres= mysql_query($bsql);
	if(mysql_num_rows($bres)>0){
		$brow = mysql_fetch_object($bres);
		if($brow->balance!=""){$totalAmount=$brow->balance;}else{ $totalAmount=0;}			
	}	
	return $totalAmount;	
}
function getSLDrBalance($project_id,$head_type,$subhead_type=NULL,$childheadtype=NULL){
	$from_date = formatDate(getRequest('date_from')); $to_date = formatDate(getRequest('date_to'));
	$totalAmount	= 0;	
	$bsql="SELECT SUM(a.dr) AS balance FROM ".ACCOUNT_JOURNAL_TBL." AS a,".SUB_ACC_HEAD_TBL." AS s WHERE BINARY a.`sub_id` = s.`sub_id` AND s.project_id = '$project_id' AND s.`head_type` = '$head_type' ";
	if($subhead_type!=""){ $bsql.=" AND s.sub_headtype='$subhead_type'"; }	
	if($childheadtype!=""){ $bsql.=" AND s.child_head='$childheadtype'"; }	
	if($from_date!="" && $to_date !=""){
	$bsql.=" AND a.created_date BETWEEN '$from_date' AND '$to_date'";
	}else{
	$from_date = date('Y-m-d');
	$bsql.=" AND a.created_date > '$from_date'";
	}
	$bres= mysql_query($bsql);
	if(mysql_num_rows($bres)>0){
		$brow = mysql_fetch_object($bres);
		if($brow->balance!=""){$totalAmount=$brow->balance;}else{ $totalAmount=0;}			
	}	
	return $totalAmount;	
}

function getSLCrBalance($project_id,$head_type,$subhead_type=NULL,$childheadtype=NULL){
	$from_date = formatDate(getRequest('date_from')); $to_date = formatDate(getRequest('date_to'));
	$totalAmount	= 0;	
	$bsql="SELECT SUM(a.cr) AS balance FROM ".ACCOUNT_JOURNAL_TBL." AS a,".SUB_ACC_HEAD_TBL." AS s WHERE BINARY a.`sub_id` = s.`sub_id` AND s.project_id = '$project_id' AND s.`head_type` = '$head_type' ";
	if($subhead_type!=""){ $bsql.=" AND s.sub_headtype='$subhead_type'"; }	
	if($childheadtype!=""){ $bsql.=" AND s.child_head='$childheadtype'"; }	
	if($from_date!="" && $to_date !=""){
	$bsql.=" AND a.created_date BETWEEN '$from_date' AND '$to_date'";
	}else{
	$from_date = date('Y-m-d');
	$bsql.=" AND a.created_date > '$from_date'";
	}
	$bres= mysql_query($bsql);
	if(mysql_num_rows($bres)>0){
		$brow = mysql_fetch_object($bres);
		if($brow->balance!=""){$totalAmount=$brow->balance;}else{ $totalAmount=0;}			
	}	
	return $totalAmount;	
}
function getSLClosingBalance($project_id,$head_type,$subhead_type=NULL,$childheadtype=NULL){
	$from_date = formatDate(getRequest('date_from')); $to_date = formatDate(getRequest('date_to'));
	$totalAmount	= 0;	
	$bsql="SELECT (SUM(a.dr)- SUM(a.cr)) AS balance FROM ".ACCOUNT_JOURNAL_TBL." AS a,".SUB_ACC_HEAD_TBL." AS s WHERE BINARY a.`sub_id` = s.`sub_id` AND s.project_id = '$project_id' AND s.`head_type` = '$head_type' ";
	if($subhead_type!=""){ $bsql.=" AND s.sub_headtype='$subhead_type'"; }
	if($childheadtype!=""){ $bsql.=" AND s.child_head='$childheadtype'"; }	
	if($from_date!="" && $to_date !=""){
	$bsql.=" AND a.created_date <= '$to_date'";
	}else{
	$to_date = date('Y-m-d');
	$bsql.=" AND a.created_date <= '$to_date'";
	} //echo $bsql;
	$bres= mysql_query($bsql);
	if(mysql_num_rows($bres)>0){
		$brow = mysql_fetch_object($bres);
		if($brow->balance!=""){$totalAmount=$brow->balance;}else{ $totalAmount=0;}			
	}	
	return $totalAmount;	
}
//===== Start A/C =======

function getACOpeningBalance($project_id,$head_type,$subhead_type,$child_id,$sl_three_id,$sub_id){
	$from_date = formatDate(getRequest('date_from')); $to_date = formatDate(getRequest('date_to'));
	$totalAmount	= 0;	
	$bsql="SELECT (SUM(a.dr)- SUM(a.cr)) AS balance FROM ".ACCOUNT_JOURNAL_TBL." AS a,".SUB_ACC_HEAD_TBL." AS s WHERE BINARY a.`sub_id` = s.`sub_id` AND s.project_id = '$project_id' AND s.`head_type` = '$head_type' ";
	if($subhead_type!=""){ $bsql.=" AND s.sub_headtype='$subhead_type'"; }		
	if($child_id!=""){ $bsql.=" AND s.child_head='$child_id'"; }		
	if($sl_three_id!=""){ $bsql.=" AND s.sl_three_head='$sl_three_id'"; }
	if($sub_id!=""){ $bsql.=" AND s.sub_id='$sub_id'"; }	
	if($from_date!=""){
	$bsql.=" AND a.created_date < '$from_date'";
	}else{
	$from_date = date('Y-m-d');
	$bsql.=" AND a.created_date < '$from_date'";
	} //echo $bsql;
	$bres= mysql_query($bsql);
	if(mysql_num_rows($bres)>0){
		$brow = mysql_fetch_object($bres);
		if($brow->balance!=""){$totalAmount=$brow->balance;}else{ $totalAmount=0;}			
	}	
	return $totalAmount;	
}
function getACDrBalance($project_id,$head_type,$subhead_type,$child_id,$sl_three_id,$sub_id){
	$from_date = formatDate(getRequest('date_from')); $to_date = formatDate(getRequest('date_to'));
	$totalAmount	= 0;	
	$bsql="SELECT SUM(a.dr) AS balance FROM ".ACCOUNT_JOURNAL_TBL." AS a,".SUB_ACC_HEAD_TBL." AS s WHERE BINARY a.`sub_id` = s.`sub_id` AND s.project_id = '$project_id' AND s.`head_type` = '$head_type' ";
	if($subhead_type!=""){ $bsql.=" AND s.sub_headtype='$subhead_type'"; }		
	if($child_id!=""){ $bsql.=" AND s.child_head='$child_id'"; }		
	if($sl_three_id!=""){ $bsql.=" AND s.sl_three_head='$sl_three_id'"; }
	if($sub_id!=""){ $bsql.=" AND s.sub_id='$sub_id'"; }	
	if($from_date!="" && $to_date !=""){
	$bsql.=" AND a.created_date BETWEEN '$from_date' AND '$to_date'";
	}else{
	$from_date = date('Y-m-d');
	$bsql.=" AND a.created_date > '$from_date'";
	}
	$bres= mysql_query($bsql);
	if(mysql_num_rows($bres)>0){
		$brow = mysql_fetch_object($bres);
		if($brow->balance!=""){$totalAmount=$brow->balance;}else{ $totalAmount=0;}			
	}	
	return $totalAmount;	
}

function getACCrBalance($project_id,$head_type,$subhead_type,$child_id,$sl_three_id,$sub_id){
	$from_date = formatDate(getRequest('date_from')); $to_date = formatDate(getRequest('date_to'));
	$totalAmount	= 0;	
	$bsql="SELECT SUM(a.cr) AS balance FROM ".ACCOUNT_JOURNAL_TBL." AS a,".SUB_ACC_HEAD_TBL." AS s WHERE BINARY a.`sub_id` = s.`sub_id` AND s.project_id = '$project_id' AND s.`head_type` = '$head_type' ";
	if($subhead_type!=""){ $bsql.=" AND s.sub_headtype='$subhead_type'"; }		
	if($child_id!=""){ $bsql.=" AND s.child_head='$child_id'"; }		
	if($sl_three_id!=""){ $bsql.=" AND s.sl_three_head='$sl_three_id'"; }
	if($sub_id!=""){ $bsql.=" AND s.sub_id='$sub_id'"; }	
	if($from_date!="" && $to_date !=""){
	$bsql.=" AND a.created_date BETWEEN '$from_date' AND '$to_date'";
	}else{
	$from_date = date('Y-m-d');
	$bsql.=" AND a.created_date > '$from_date'";
	}
	$bres= mysql_query($bsql);
	if(mysql_num_rows($bres)>0){
		$brow = mysql_fetch_object($bres);
		if($brow->balance!=""){$totalAmount=$brow->balance;}else{ $totalAmount=0;}			
	}	
	return $totalAmount;	
}
function getACClosingBalance($project_id,$head_type,$subhead_type,$child_id,$sl_three_id,$sub_id){
	$from_date = formatDate(getRequest('date_from')); $to_date = formatDate(getRequest('date_to'));
	$totalAmount	= 0;	
	$bsql="SELECT (SUM(a.dr)- SUM(a.cr)) AS balance FROM ".ACCOUNT_JOURNAL_TBL." AS a,".SUB_ACC_HEAD_TBL." AS s WHERE BINARY a.`sub_id` = s.`sub_id` AND s.project_id = '$project_id' AND s.`head_type` = '$head_type' ";
	if($subhead_type!=""){ $bsql.=" AND s.sub_headtype='$subhead_type'"; }	
	if($child_id!=""){ $bsql.=" AND s.child_head='$child_id'"; }		
	if($sl_three_id!=""){ $bsql.=" AND s.sl_three_head='$sl_three_id'"; }
	if($sub_id!=""){ $bsql.=" AND s.sub_id='$sub_id'"; }	
	if($from_date!="" && $to_date !=""){
	$bsql.=" AND a.created_date <= '$to_date'";
	}else{
	$to_date = date('Y-m-d');
	$bsql.=" AND a.created_date <= '$to_date'";
	} //echo $bsql;
	$bres= mysql_query($bsql);
	if(mysql_num_rows($bres)>0){
		$brow = mysql_fetch_object($bres);
		if($brow->balance!=""){$totalAmount=$brow->balance;}else{ $totalAmount=0;}			
	}	
	return $totalAmount;	
}

function getBSBankBalance($project_id){
	$totalAmount	= 0;	
	$bsql="SELECT (SUM(a.dr)- SUM(a.cr)) AS balance FROM ".ACCOUNT_JOURNAL_TBL." AS a,".BANK_ACCOUNT_TBL." AS b WHERE BINARY 
	a.sub_id=b.bank_account_no AND b.project_id='$project_id'";
	$bres= mysql_query($bsql);
	if(mysql_num_rows($bres)>0){
		$brow = mysql_fetch_object($bres);
		if($brow->balance!=""){$totalAmount=$brow->balance;}else{ $totalAmount=0;}			
	}		
	return $totalAmount;
}

//====== Income Statement =======
function getISTotalSalesAmount($project_id,$head_type,$head_id=NULL){
	$from_date = formatDate(getRequest('date_from')); $to_date = formatDate(getRequest('date_to'));
	$sql="SELECT (SUM(a.dr)- SUM(a.cr)) AS TotalSales FROM ".ACCOUNT_JOURNAL_TBL." AS a,".SUB_ACC_HEAD_TBL." AS s WHERE BINARY a.`sub_id` = s.`sub_id` AND a.project_id = '$project_id' AND s.project_id = '$project_id' AND s.`head_type` = '$head_type' ";
	if($head_id!=""){ $sql.=" AND $head_id"; }	
	if($from_date!="" && $to_date !=""){
	$sql.=" AND a.created_date BETWEEN '$from_date' AND '$to_date'";
	}
	
   	$crow = mysql_fetch_object(mysql_query($sql)); //echo $sql;
	if($crow->TotalSales!=""){
	$TotalSales = abs($crow->TotalSales);
	}else{
	$TotalSales = 0;
	}
	return $TotalSales;
}
function getISHeadsBalance($project_id,$head_type,$head_id=NULL){
	$totalAmount	= 0; $from_date = formatDate(getRequest('date_from')); $to_date = formatDate(getRequest('date_to'));	
	$bsql="SELECT (SUM(a.dr)- SUM(a.cr)) AS balance FROM ".ACCOUNT_JOURNAL_TBL." AS a,".SUB_ACC_HEAD_TBL." AS s WHERE BINARY a.`sub_id` = s.`sub_id` AND a.project_id = '$project_id'  AND s.project_id = '$project_id' AND s.`head_type` = '$head_type' ";
	if($head_id!=""){ $bsql.=" AND $head_id ";}	
	if($from_date!="" && $to_date !=""){
	$bsql.=" AND a.created_date BETWEEN '$from_date' AND '$to_date'";
	}//echo $bsql;
	$bres= mysql_query($bsql);
	if(mysql_num_rows($bres)>0){
		$brow = mysql_fetch_object($bres);
		if($brow->balance!=""){$totalAmount=$brow->balance;}else{ $totalAmount=0;}			
	}	
	return $totalAmount;	
}
function getISProductOpeingValue($project_id,$head_type,$head_id=NULL){
	
	$totalQty = 0; $avgUnitPrice = 0; $totalAmount = 0; $from_date = formatDate(getRequest('date_from'));
	$sql="SELECT (SUM(a.dr)- SUM(a.cr)) AS totalAmount FROM ".ACCOUNT_JOURNAL_TBL." AS a,".SUB_ACC_HEAD_TBL." AS s WHERE BINARY a.`sub_id` = s.`sub_id` AND a.project_id = '$project_id' AND s.project_id = '$project_id' AND s.`head_type` = '$head_type' ";
	if($head_id!=""){ $sql.=" AND $head_id"; }	
	if($from_date!=""){
	$sql.=" AND a.created_date < '$from_date'";
	}
	$bres= mysql_query($sql);
	if(mysql_num_rows($bres)>0){
		$brow = mysql_fetch_object($bres);
		if($brow->totalAmount != ""){$totalAmount=$brow->totalAmount;}
		else{ $totalAmount=0;}			
	}
	if($totalAmount == 0){
		$rbsql="SELECT opening_balance FROM ".OPENING_BALANCE_TBL." WHERE project_id='$project_id' 
		AND `head_type`='Raw Materials'";
		$rbres= mysql_query($rbsql);
		if(mysql_num_rows($rbres)>0){
			$rbrow = mysql_fetch_object($rbres);
			if($rbrow->opening_balance!=""){$totalAmount=$rbrow->opening_balance;}
			else{ $totalAmount=0;}			
		}	
	}
	
	return $totalAmount;	
}
function getISProductPurchaseValue($project_id,$head_type,$head_id){
	$totalPVAmount = 0; $from_date = formatDate(getRequest('date_from')); 
	$to_date = formatDate(getRequest('date_to'));
		
	$sql="SELECT (SUM(a.dr)) AS totalAmount FROM ".ACCOUNT_JOURNAL_TBL." AS a,".SUB_ACC_HEAD_TBL." AS s WHERE BINARY a.`sub_id` = s.`sub_id` AND a.project_id = '$project_id' AND s.project_id = '$project_id' AND s.`head_type` = '$head_type' ";
	if($head_id!=""){ $sql.=" AND $head_id "; }	
	if($from_date != "" && $to_date != ""){
	$sql.=" AND a.created_date BETWEEN '$from_date' AND '$to_date'";
	} //echo $sql;
	$bres= mysql_query($sql);
	if(mysql_num_rows($bres)>0){
		$brow = mysql_fetch_object($bres);
		if($brow->totalAmount != ""){ $totalPVAmount = $brow->totalAmount;}
		else{ $totalPVAmount=0;}			
	}
	
			
	return $totalPVAmount;	
}
function getISProductClosingValue($project_id,$head_type,$head_id=NULL){
	$closing_value = 0; $from_date = formatDate(getRequest('date_from')); 
	$to_date = formatDate(getRequest('date_to'));
	$sql="SELECT (SUM(a.dr)- SUM(a.cr)) AS closing_value FROM ".ACCOUNT_JOURNAL_TBL." AS a,".SUB_ACC_HEAD_TBL." AS s WHERE BINARY a.`sub_id` = s.`sub_id` AND a.project_id = '$project_id' AND s.project_id = '$project_id' AND s.`head_type` = '$head_type' ";
	if($head_id!=""){ $sql.=" AND $head_id"; }	
	if($to_date != ""){
	$sql.=" AND a.created_date <= '$to_date'";
	} //echo $sql;
	$bres= mysql_query($sql);
	
	if(mysql_num_rows($bres)>0){
		$brow = mysql_fetch_object($bres);
		if($brow->closing_value !=""){
		$closing_value=$brow->closing_value;
		}else{ $closing_value=0;}			
	}
			
	return $closing_value;		
}

//==== Finish Goods =====

//==== Not Used ====
function getPurchaseAmount($project_id,$product_type){
	$totalAmount	= 0;
	$sql= "SELECT SUM( pm.paid_amount ) as po_balance FROM ".PURCHASE_MASTER_TBL." AS pm, ".PURCHASE_DETAILS_TBL." AS pd, ".PRODUCT_TBL." AS p
	WHERE pm.voucher_no=pd.voucher_no AND pd.product=p.product_id AND p.product_type='$product_type' AND pm.`supplier`!= '' AND pm.paid_amount >0
	AND pm.project_id = '$project_id'";	
	$bres= mysql_query($sql);
	if(mysql_num_rows($bres)>0){
		$brow = mysql_fetch_object($bres);
		if($brow->po_balance!=""){$totalAmount=$brow->po_balance;}else{ $totalAmount=0;}			
	}	
	return $totalAmount;
}

?>
