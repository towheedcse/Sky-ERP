<? 
function getGLOpeningBalance($project_id,$glhead,$subhead_type=NULL,$childheadtype=NULL){
	$from_date = formatDate(getRequest('date_from')); $to_date = formatDate(getRequest('date_to'));
	$totalAmount	= 0;	
	$bsql="SELECT (SUM(a.dr)- SUM(a.cr)) AS balance FROM ".ACCOUNT_JOURNAL_TBL." AS a,".SUB_ACC_HEAD_TBL." AS s WHERE BINARY a.`sub_id` = s.`sub_id` AND s.project_id = '$project_id' AND s.`group_ledger` = '$glhead' ";
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
function getGLDrBalance($project_id,$glhead,$subhead_type=NULL,$childheadtype=NULL){
	$from_date = formatDate(getRequest('date_from')); $to_date = formatDate(getRequest('date_to'));
	$totalAmount	= 0;	
	$bsql="SELECT SUM(a.dr) AS balance FROM ".ACCOUNT_JOURNAL_TBL." AS a,".SUB_ACC_HEAD_TBL." AS s WHERE BINARY a.`sub_id` = s.`sub_id` AND s.project_id = '$project_id' AND s.`group_ledger` = '$glhead' ";
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

function getGLCrBalance($project_id,$glhead,$subhead_type=NULL,$childheadtype=NULL){
	$from_date = formatDate(getRequest('date_from')); $to_date = formatDate(getRequest('date_to'));
	$totalAmount	= 0;	
	$bsql="SELECT SUM(a.cr) AS balance FROM ".ACCOUNT_JOURNAL_TBL." AS a,".SUB_ACC_HEAD_TBL." AS s WHERE BINARY a.`sub_id` = s.`sub_id` AND s.project_id = '$project_id' AND s.`group_ledger` = '$glhead' ";
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
function getGLClosingBalance($project_id,$glhead,$subhead_type=NULL,$childheadtype=NULL){
	$from_date = formatDate(getRequest('date_from')); $to_date = formatDate(getRequest('date_to'));
	$totalAmount	= 0;	
	$bsql="SELECT (SUM(a.dr)- SUM(a.cr)) AS balance FROM ".ACCOUNT_JOURNAL_TBL." AS a,".SUB_ACC_HEAD_TBL." AS s WHERE BINARY a.`sub_id` = s.`sub_id` AND s.project_id = '$project_id' AND s.`group_ledger` = '$glhead' ";
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

function getACOpeningBalance($project_id,$head_type,$subhead_type,$sub_id){
	$from_date = formatDate(getRequest('date_from')); $to_date = formatDate(getRequest('date_to'));
	$totalAmount	= 0;	
	$bsql="SELECT (SUM(a.dr)- SUM(a.cr)) AS balance FROM ".ACCOUNT_JOURNAL_TBL." AS a,".SUB_ACC_HEAD_TBL." AS s WHERE BINARY a.`sub_id` = s.`sub_id` AND s.project_id = '$project_id' AND s.`head_type` = '$head_type' ";
	if($subhead_type!=""){ $bsql.=" AND s.sub_headtype='$subhead_type'"; }	
	if($sub_id!=""){ $bsql.=" AND s.sub_id='$sub_id'"; }	
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
function getACDrBalance($project_id,$head_type,$subhead_type,$sub_id){
	$from_date = formatDate(getRequest('date_from')); $to_date = formatDate(getRequest('date_to'));
	$totalAmount	= 0;	
	$bsql="SELECT SUM(a.dr) AS balance FROM ".ACCOUNT_JOURNAL_TBL." AS a,".SUB_ACC_HEAD_TBL." AS s WHERE BINARY a.`sub_id` = s.`sub_id` AND s.project_id = '$project_id' AND s.`head_type` = '$head_type' ";
	if($subhead_type!=""){ $bsql.=" AND s.sub_headtype='$subhead_type'"; }	
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

function getACCrBalance($project_id,$head_type,$subhead_type,$sub_id){
	$from_date = formatDate(getRequest('date_from')); $to_date = formatDate(getRequest('date_to'));
	$totalAmount	= 0;	
	$bsql="SELECT SUM(a.cr) AS balance FROM ".ACCOUNT_JOURNAL_TBL." AS a,".SUB_ACC_HEAD_TBL." AS s WHERE BINARY a.`sub_id` = s.`sub_id` AND s.project_id = '$project_id' AND s.`head_type` = '$head_type' ";
	if($subhead_type!=""){ $bsql.=" AND s.sub_headtype='$subhead_type'"; }	
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
function getACClosingBalance($project_id,$head_type,$subhead_type,$sub_id){
	$from_date = formatDate(getRequest('date_from')); $to_date = formatDate(getRequest('date_to'));
	$totalAmount	= 0;	
	$bsql="SELECT (SUM(a.dr)- SUM(a.cr)) AS balance FROM ".ACCOUNT_JOURNAL_TBL." AS a,".SUB_ACC_HEAD_TBL." AS s WHERE BINARY a.`sub_id` = s.`sub_id` AND s.project_id = '$project_id' AND s.`head_type` = '$head_type' ";
	if($subhead_type!=""){ $bsql.=" AND s.sub_headtype='$subhead_type'"; }
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
?>
