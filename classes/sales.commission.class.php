<?php
class SalesCommission
{
   function run() {     
		$cmd    = getRequest('cmd');
		$u_t_id = getFromSession('u_type_id');
		if($u_t_id == 101||$u_t_id ==102) 
		{      
		  switch($cmd) { 
		  	 case 'add'                	: $screen = $this->showEditor($msg); break;
      	     case 'edit'               	: $screen = $this->showEditor("Edit Page");    break;			 
      	   	 case 'doUpdate'           	: $screen = $this->showEditor($msg); break;			 
      	   	 case 'capp'           		: $screen = $this->showEditor($msg); break;
			 default                   	: $cmd = 'list'; $screen = $this->showEditor($msg);   break;
		  }
		}else {
      		header("location:index.php?app=user_home&msg=You are not authorised !!!");
      	} 
		
		return true;
   }
  function showEditor()
  {
	 require_once(CLASS_DIR.'/common.class.php');	
	 $comApp = new Common(); 
	 require_once(CLASS_DIR.'/common.list.class.php');	
	 $comListApp 	= new CommonList(); 	 
	 
	 $cmd    = getRequest('cmd');
	 $voucher_no = getRequest('id');	 
	 $data               = array();		
	 if($voucher_no){
		 $TBDArr			= $comApp->getRecordInfo(SALES_MASTER_TBL,"voucher_no",$voucher_no);      
		 $TBDArr 			= parseThisValue($TBDArr);
		 $data        		= array_merge(array(),$TBDArr);		 			
		 if(getRequest('save')){
		 	if(getRequest('commission_status')==1){
			$this->approvedCommission($voucher_no);
			}
			$comApp->updateRecord(SALES_MASTER_TBL,"voucher_no",$voucher_no,"","","","","sales","com_list");
			$msg="Successfully Update Record !!!";
			header("location:?app=sales&cmd=com_list&msg=$msg");	      	
		 }
		 if($cmd=="capp"){
			 $this->approvedCommission($voucher_no);
			 $msg="Successfully Approved Commission !!!";
			 header("location:?app=sales&cmd=com_list&msg=$msg");	 
		 } 
	} 
	$data['project_id'] 	= getFromSession('project_id');
	$data['message'] 		= $msg;
	$data['cmd']     		= getRequest('cmd'); 
	require_once(CURRENT_APP_SKIN_FILE);
	return $data[0];
   } 
   function approvedCommission($voucher_no){
   	    require_once(CLASS_DIR.'/sales.class.php');	
	    $slsApp = new Sales();
	    $project_id = getFromSession('project_id');
	    $CrAmount 	= getRequest('total_commission');
	    $PartyAcc_head = getRequest('reference'); 
		if($CrAmount=="" || $PartyAcc_head==""){
		$sql = "SELECT reference,total_commission FROM ".SALES_MASTER_TBL." WHERE voucher_no='$voucher_no' AND project_id = '$project_id'";
		$row = mysql_fetch_object(mysql_query($sql));
		$PartyAcc_head 		= $row->reference;
		$CrAmount 			= $row->total_commission;
		} 
   		mysql_query("START TRANSACTION;");
   		//======= Party Cr ======	
		$created_date = date('Y-m-d h:i:s');	
		
		$totalPartyCR  = $slsApp->getTotalCreditAmount($PartyAcc_head,getFromSession('project_id'));
		$totalPartyDR  = $slsApp->getTotalDebitAmount($PartyAcc_head,getFromSession('project_id'));					 
		$PartyBalance  = ($totalPartyDR-($totalPartyCR+$CrAmount));					 
		$slsApp->saveAccountJournal($voucher_no,$PartyAcc_head,"Acc",getFromSession('project_id'),"Sales Commission",0,$CrAmount,$PartyBalance,1,$created_date);	
		
		
		$pdusql = "UPDATE ".SALES_MASTER_TBL." SET commission_status=1 WHERE voucher_no='$voucher_no'";
		mysql_query($pdusql);
		mysql_query("COMMIT;");	
   }
   function getSalesCommissionList($from,$to){ 
		if($from == "" && $to == ""){$from=0; $to=1000;}
		$date_from 		= formatDate(getRequest('date_from'));
		$date_to 		= formatDate(getRequest('date_to'));				
		$project_id     = getFromSession('project_id');  
		$info           = array();    
		$info['table']  = SALES_MASTER_TBL.' pm,'.SUB_ACC_HEAD_TBL.' s,'.PROJECT_TBL.' p,'.CURRENCY_TBL.' c';	
		$info['fields'] = array('pm.voucher_no','p.project_name','p.location','s.sub_id','s.sub_head_name','s.head_details','pm.gate_pass','pm.track_no','pm.salse_type','pm.total_value',"DATE_FORMAT(pm.sales_date,'%d %b %y' ) as sales_date",'pm.mode_of_payment','c.curr_symble','pm.bank_name','pm.acc_no','pm.check_no','pm.check_no','pm.discount','pm.net_payble','pm.paid_amount','pm.due','pm.ref_no','pm.created_date',"DATE_FORMAT(pm.created_date,'%d %b %y' ) as date","pm.reference","pm.commission_slot","pm.total_commission","pm.commission_adv_paid","pm.commission_total_paid","pm.commission_total_due as due","pm.commission_status");
		
		$sql="pm.reference = s.sub_id AND pm.project_id = p.project_id AND pm.currency = c.currency_id AND pm.project_id = '".$project_id."' AND commission_total_due>0 
		AND commission_status=1";
							
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
   function updateCommissionMaster($cref_no,$voucher_no,$CrAmount){
		$psql= "SELECT total_commission as net_payble,commission_total_paid as paid_amount ,commission_total_due as due FROM ".SALES_MASTER_TBL." WHERE 
		voucher_no  = '".$cref_no."' AND commission_total_due >0 AND commission_status=1";
		$pres = mysql_query($psql);
		if(mysql_num_rows($pres)>0){
			$srow = mysql_fetch_object($pres);						
			$due = $srow->due;
			$net_payble = $srow->net_payble;
			$paidAmount = $srow->paid_amount;
			$total_due = $net_payble - ($paidAmount + $CrAmount);
			$total_paid = ($paidAmount + $CrAmount);
			$pusql= "UPDATE ".SALES_MASTER_TBL." SET commission_total_paid ='".$total_paid."',commission_total_due ='".$total_due."' WHERE voucher_no = '".$cref_no."' AND 
			commission_total_due >0 AND commission_status=1";
			mysql_query($pusql);
	   } 
	}// end updateCommissionMaster
} // End class
?>