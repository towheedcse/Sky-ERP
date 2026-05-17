<?  
function getBSFixedAsseteBalance($head_type,$subhead_type=NULL){
	$project_id  	= getFromSession('project_id');
	$totalAmount	= 0;	
	$bsql="SELECT (SUM(a.dr)- SUM(a.cr)) AS balance FROM ".ACCOUNT_JOURNAL_TBL." AS a,".SUB_ACC_HEAD_TBL." AS s WHERE BINARY 
	a.`sub_id` = s.`sub_id` AND s.project_id = '$project_id' AND s.`head_type` = '$head_type' ";
	if($subhead_type!=""){ $bsql.=" AND s.sub_headtype='$subhead_type'"; }	
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
function getBSAdvancedPaid($project_id){	
	$totalAdvanced_Paid = 0;
	$advPaysql = "SELECT SUM(due) as advancedPayment FROM ".PURCHASE_RETURN_RECEIBAVLE_TBL." WHERE `project_id` = '$project_id' AND due>0 
	AND fyear='FY001' AND (payment_source='Advanced Payment' OR payment_source='Advanced Payment Against Salary' 
	OR payment_source='Advanced Payment Against Commission')";
	$advPayres = mysql_query($advPaysql);		
	$advPaynum = mysql_num_rows($advPayres);
	if($advPaynum>0){			
		while($sr_row = mysql_fetch_object($advPayres)){
			$totalAdvanced_Paid +=$sr_row->advancedPayment;
		}
	}
	//=======================================
	$pblchk_sql = "SELECT SUM(due) as advanced_paid FROM ".DEVIT_VOUCHAR_TBL." WHERE `project_id`='$project_id' AND status=0 AND 
	vouchar_type = 'Advanced Payment Vouchar' AND fyear='FY001' AND due>0";
	$pblchk_res = mysql_query($pblchk_sql);		
	$pblchk_num = mysql_num_rows($pblchk_res);
	if($pblchk_num>0){			
		while($pblchk_row = mysql_fetch_object($pblchk_res)){
			$totalAdvanced_Paid +=$pblchk_row->advanced_paid;
		}
	}
	return $totalAdvanced_Paid;
}
function getBSSupplierBalance($project_id){
	$totalAmount	= 0;	
	$bsql="SELECT (SUM(a.dr)- SUM(a.cr)) AS balance FROM ".ACCOUNT_JOURNAL_TBL." AS a,".SUPPLIER_TBL." AS s WHERE BINARY 
	a.sub_id=s.supplier_code AND s.project_id='$project_id'";
	$bres= mysql_query($bsql);
	if(mysql_num_rows($bres)>0){
		$brow = mysql_fetch_object($bres);
		if($brow->balance!=""){$totalAmount=$brow->balance;}else{ $totalAmount=0;}			
	}		
	return $totalAmount;
}
//======= Others ========
function getBSAccountsRecievable($project_id){
	$totalRecievable = 0;
	$rec_sql = "SELECT SUM(due) as receivable FROM ".DEVIT_VOUCHAR_TBL." WHERE `project_id`='$project_id' AND vouchar_type='Recievable Vouchar' 
	AND status = 0 AND due >0";
	$rec_res = mysql_query($rec_sql);	
	$rec_num = mysql_num_rows($rec_res);
	if($rec_num>0){		
		while($rec_row = mysql_fetch_object($rec_res)){
			$totalRecievable +=$rec_row->receivable;
		}
	}
	$sales_sql = "SELECT SUM(due) as receivable_due FROM ".SALES_MASTER_TBL." WHERE `project_id` = '$project_id' AND due > 0";
	$sales_res = mysql_query($sales_sql);	
	$sales_num = mysql_num_rows($sales_res);
	if($sales_num>0){		
		while($sales_row = mysql_fetch_object($sales_res)){
			$totalRecievable +=$sales_row->receivable_due;
		}
	}
	// ===== Recievable Check =======
	$totalChkRecievable=0;
	$rblchkSql = "SELECT SUM(paid_amount) as payable FROM ".PAYABLE_CHECK_TBL." WHERE `project_id` = '$project_id' AND status =0 
	AND transaction_type = 'Received' AND paid_amount >0";
	$rblchkRes = mysql_query($rblchkSql);	
	$rblchk_num = mysql_num_rows($rblchkRes);
	if($rblchk_num>0){			
		while($rblchk_row = mysql_fetch_object($rblchkRes)){
			$totalChkRecievable +=$rblchk_row->payable;
		}
	}
	$totalRecievable= $totalRecievable+$totalChkRecievable;
	return $totalRecievable;	
}
//====== Income Statement =======
function getISTotalSalesAmount($project_id,$fyear){
	/*
	if($from_date!="" && $to_date ==""){
		$csql.=" AND s.create_date >= '$from_date'";
	}elseif($from_date=="" && $to_date !=""){
		$csql.=" AND s.create_date <= '$to_date'";
	}elseif($from_date!="" && $to_date !=""){
		$csql.=" AND s.create_date BETWEEN '$from_date' AND '$to_date'";
	}
	*/
   	$crow = mysql_fetch_object(mysql_query("SELECT  SUM(`sales_amount`) AS TotalSales FROM ".CUSTOMER_SALES_LEDGER_VIEW." WHERE project_id='$project_id'"));
	if($crow->TotalSales!=""){
	$TotalSales = $crow->TotalSales;
	}else{
	$TotalSales = 0;
	}
	return $TotalSales;
}
function getISHeadsBalance($project_id,$head_type,$head_id=NULL){
	$totalAmount	= 0;	
	$bsql="SELECT (SUM(a.dr)- SUM(a.cr)) AS balance FROM ".ACCOUNT_JOURNAL_TBL." AS a,".SUB_ACC_HEAD_TBL." AS s WHERE BINARY 
	a.`sub_id` = s.`sub_id` AND s.project_id = '$project_id' AND s.`head_type` = '$head_type' ";
	if($head_id!=""){ $bsql.=" AND s.sub_id REGEXP('$head_id')"; }	
	$bres= mysql_query($bsql);
	if(mysql_num_rows($bres)>0){
		$brow = mysql_fetch_object($bres);
		if($brow->balance!=""){$totalAmount=$brow->balance;}else{ $totalAmount=0;}			
	}	
	return $totalAmount;	
}
function getISProductOpeingValue($project_id,$product_type,$fyear=NULL){
	$totalAmount	= 0;	
	$bsql="SELECT SUM( op_value) AS op_balance FROM ".STOCK_VALUE_VIEW." WHERE project_id = '$project_id' AND `product_type` = '$product_type' ";
	$bres= mysql_query($bsql);
	if(mysql_num_rows($bres)>0){
		$brow = mysql_fetch_object($bres);
		if($brow->op_balance!=""){$totalAmount=$brow->op_balance;}else{ $totalAmount=0;}			
	}
	if($totalAmount==0 && $fyear!=""){
	$rbsql="SELECT opening_balance FROM ".OPENING_BALANCE_TBL." WHERE project_id='$project_id' AND `head_type`='$product_type' AND fyear='$fyear'";
	$rbres= mysql_query($rbsql);
	if(mysql_num_rows($rbres)>0){
		$rbrow = mysql_fetch_object($rbres);
		if($rbrow->opening_balance!=""){$totalAmount=$rbrow->opening_balance;}else{ $totalAmount=0;}			
	}	
	}
	return $totalAmount;	
}
function getISProductPurchaseValue($project_id,$producttype){
	$totalPVAmount = 0;	 $purchasePrice=0; $purchaseQty=0;
	$bsql="SELECT SUM(instock) AS instock FROM ".STOCK_VALUE_VIEW." WHERE project_id='$project_id' AND `product_type`='$producttype'";
	$bres= mysql_query($bsql);
	if(mysql_num_rows($bres)>0){
		$brow = mysql_fetch_object($bres);
		if($brow->instock!=""){$purchaseQty=$brow->instock;}else{ $purchaseQty=0;}			
	}
	$psql = "SELECT AVG(`s`.`unit_price`) AS `purchase_price` from ".STOCK_LEDGER_TBL." as `s`, ".PRODUCT_TBL." `p` 
	WHERE ((`s`.`product_id` = `p`.`product_id`) AND s.cr>0 AND p.project_id='$project_id' AND p.`product_type`='$producttype' ) group by `s`.`product_id`,`s`.`project_id`";
	
	$bres= mysql_query($psql);
	if(mysql_num_rows($bres)>0){
		$brow = mysql_fetch_object($bres);
		if($brow->purchase_price!=""){$purchasePrice=$brow->purchase_price;}else{ $purchasePrice=0;}			
	}
	$totalPVAmount = ($purchasePrice*$purchaseQty);		
	return $totalPVAmount;	
}
function getISProductClosingValue($project_id,$producttype){
	$totalPCAmount = 0;	
	$bsql="SELECT SUM(closing_value) AS closing_value FROM ".STOCK_VALUE_VIEW." WHERE project_id='$project_id' AND `product_type`='$producttype'";
	$bres= mysql_query($bsql);
	if(mysql_num_rows($bres)>0){
		$brow = mysql_fetch_object($bres);
		if($brow->closing_value!=""){$totalPCAmount=$brow->closing_value;}else{ $totalPCAmount=0;}			
	}		
	return $totalPCAmount;	
}
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