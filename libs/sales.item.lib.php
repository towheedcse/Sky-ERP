<? 
function insertSalesCollection($sales_no,$bill_no,$patient_no,$received_amount,$received_date,$details){
 	require_once(CLASS_DIR.'/common.list.class.php');	
	$comlistApp 	= new CommonList();     
	$project_id 	= getFromSession('project_id'); 
	$branch_id		= getFromSession('branch_id');  
	$created_by		= getFromSession('userid'); 
	$created_date	= date('Y-m-d h:i:s');	
	$collection_from = "Admission";
		  
	$sqlRP="INSERT INTO ".DIAGNOSIS_COLLECTION_TBL."(project_id,branch_id,bill_no,voucher_no,patient_no,received_amount,received_date,collection_from,
	created_by,created_date) VALUES('$project_id','$branch_id','$bill_no','$sales_no','$patient_no','$received_amount','$received_date',
	'Sales','$created_by','$created_date')";
	$res=mysql_query($sqlRP);
	if($res){ return true;}else{return false;}	
}
?>