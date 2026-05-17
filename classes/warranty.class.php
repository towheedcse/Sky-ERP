<?php
class Warranty
{
   function run() {     
		$cmd    = getRequest('cmd');
		$u_t_id = getFromSession('u_type_id');
		if($u_t_id == 103 || $u_t_id == 2) 
		{      
		  switch($cmd) { 
		  	 case 'srcclaim'            : $screen = $this->search4Claim($msg); break;
			 case 'claim'            	: $screen = $this->Claim4Warranty($msg); break;
			 case 'claim_rvc'           : $screen = $this->ClaimReceived($msg); break;
			 case 'delivery'            : $screen = $this->WarrantyDelivery(getRequest('id')); break;
			 case 'servicing'       	: $screen = $this->Received4Servicing($msg); break;
			 case 'service.status'      : $screen = $this->ServicingStatus($msg); break;
			 case 'service.report'      : $screen = $this->ServicingReport($msg); break;
			 case 'service.bill'        : $screen = $this->ServicingBillPrepare($msg); break;
      	     case 'edit'               	: $screen = $this->showEditor("Edit Page");    break;			 
      	   	 case 'doUpdate'           	: $screen = $this->showEditor($msg); break;
		     case 'delete'             	: $screen = $this->deleteItem(); break;
			 default                   	: $cmd = 'list'; $screen = $this->showEditor($msg);   break;
		  }
		}else {
      		header("location:index.php?app=user_home&msg=You are not authorised !!!");
      	} 
		
		return true;
   }
   function search4Claim(){
		require_once(CLASS_DIR.'/common.class.php');	
		$comApp = new Common(); 
		require_once(CLASS_DIR.'/common.list.class.php');	
		$comListApp 	= new CommonList(); 
		$smid 		 = getRequest('smid');
		$sub_id	 = getRequest('sub_id');
		$id		 = getRequest('id'); 
		$data               = array();		
		if($sub_id!=""){
		$TBDArr			= $comApp->getRecordInfo(SUB_ACC_HEAD_TBL,"sub_id",$sub_id);      
		$TBDArr 			= parseThisValue($TBDArr);
		$data        		= array_merge(array(),$TBDArr);	
		}
		
		$f1Value = getRequest('customer_name'); $f2Value = getRequest('serial_no');
		$condition = "status<3 AND catagory='Warranty'";
		$data['product_list']  	= $comApp->getRecords(WARRANTY_TBL,"customer_id",$condition,"customer_name",$f1Value,"serial_no",$f2Value,getRequest('from'),getRequest('to'));
		$data['totalrecord']  	= $comApp->getTotalRecords(WARRANTY_TBL,"customer_id",$condition,"customer_name",$f1Value,"",""); 
		$data['catagory_list']	= $comListApp->getCatagoryList();
		$data['brand_list'] 	= $comListApp->getBrandList();
		$data['message'] 		= $msg;
		$data['cmd']     		= getRequest('cmd'); 
		require_once(WARRANTY_CLAIM_WAITING_SKIN);
		return $data[0];
   }
   function Claim4Warranty(){
		require_once(CLASS_DIR.'/common.class.php');	
		$comApp = new Common(); 
		require_once(CLASS_DIR.'/common.list.class.php');	
		$comListApp 	= new CommonList(); 
		$supplier_id 	= getRequest('supplier_id');
		$id		 = getRequest('id'); 
		$data               = array();		
		if($supplier_id!=""){
		$TBDArr				= $comApp->getRecordInfo(SUPPLIER_TBL,"supplier_code",$supplier_id);      
		$TBDArr 			= parseThisValue($TBDArr);
		$data        		= array_merge(array(),$TBDArr);	
		}
		if($id!=""){
		 $TBDArr			= $comApp->getRecordInfo(WARRANTY_TBL,"warranty_id",$id);      
		 $TBDArr 			= parseThisValue($TBDArr);
		 $data        		= array_merge(array(),$TBDArr);	
		 if(getRequest('save')){
			$comApp->updateRecord(WARRANTY_TBL,"warranty_id",$id,"claim_date,claim_receive_date","","","","warranty","list&customer_id=$sub_id");
			$msg="Successfully Update Record !!!";
			header("location:?app=warranty&cmd=srcclaim&msg=$msg");	      	
		 } 
		}
		
		$data['message'] 		= $msg;
		$data['cmd']     		= getRequest('cmd'); 
		require_once(WARRANTY_CLAIM_SKIN);
		return $data[0];
   }
   function ClaimReceived(){
		require_once(CLASS_DIR.'/common.class.php');	
		$comApp = new Common(); 
		require_once(CLASS_DIR.'/common.list.class.php');	
		$comListApp 	= new CommonList();
		require_once(CLASS_DIR.'/general_vouchar.class.php');	
		$gvApp = new GeneralVouchar();
		 
		$supplier_id 	= getRequest('supplier_id');
		$id		 = getRequest('id'); 
		$data               = array();		
		if($supplier_id!=""){
		$TBDArr				= $comApp->getRecordInfo(SUPPLIER_TBL,"supplier_code",$supplier_id);      
		$TBDArr 			= parseThisValue($TBDArr);
		$data        		= array_merge(array(),$TBDArr);	
		}
		if($id!=""){
		 $TBDArr			= $comApp->getRecordInfo(WARRANTY_TBL,"warranty_id",$id);      
		 $TBDArr 			= parseThisValue($TBDArr);
		 $data        		= array_merge(array(),$TBDArr);	
		 if(getRequest('save')){
			$comApp->updateRecord(WARRANTY_TBL,"warranty_id",$id,"","","","","warranty","list&customer_id=$sub_id");			
			//======= Dr Account ======	
			$DrAmount	   = getRequest('service_bill'); 
			$customer_id   = getRequest('customer_id'); 
			$totalPartyCR  = $gvApp->getTotalCreditAmount($customer_id,getFromSession('project_id'));
			$totalPartyDR  = $gvApp->getTotalDebitAmount($customer_id,getFromSession('project_id'));					 
			$PartyBalance  = (($totalPartyDR+$DrAmount)-$totalPartyCR);					 
			$gvApp->saveAccountJournal($id,$customer_id,"Acc",getFromSession('project_id'),getRequest('description'),$DrAmount,0,$PartyBalance,1,date("Y-m-d"));
			$sql = "UPDATE ".WARRANTY_TBL." SET due='$DrAmount' WHERE warranty_id=".$id;
			$res = mysql_query($sql);
			$msg="Successfully Saved Record !!!";
			header("location:?app=warranty&cmd=srcclaim&msg=$msg");	      	
		 } 
		}
		
		$data['message'] 		= $msg;
		$data['cmd']     		= getRequest('cmd'); 
		require_once(WARRANTY_CLAIM_RECEIVE_SKIN);
		return $data[0];
   }
   
   function WarrantyDelivery($id){
    $delivery_date = date("Y-m-d");
   	$sql = "UPDATE ".WARRANTY_TBL." SET delivery_date='$delivery_date',status=3 WHERE warranty_id=".$id;
	$res = mysql_query($sql);
	$msg="Successfully Saved Record !!!";
	header("location:?app=warranty&cmd=srcclaim&msg=$msg");	
   }
  function showEditor(){
	 require_once(CLASS_DIR.'/common.class.php');	
	 $comApp = new Common(); 
	 require_once(CLASS_DIR.'/common.list.class.php');	
	 $comListApp 	= new CommonList(); 
	 $smid 		 = getRequest('smid');
	 $sub_id	 = getRequest('sub_id');
	 $id		 = getRequest('id'); 
	 $data               = array();		
	 if($sub_id!=""){
	 $TBDArr			= $comApp->getRecordInfo(SUB_ACC_HEAD_TBL,"sub_id",$sub_id);      
	 $TBDArr 			= parseThisValue($TBDArr);
	 $data        		= array_merge(array(),$TBDArr);	
	 }
	 if($id!=""){
	 	 $TBDArr			= $comApp->getRecordInfo(WARRANTY_TBL,"warranty_id",$id);      
		 $TBDArr 			= parseThisValue($TBDArr);
		 $data        		= array_merge(array(),$TBDArr);	 			
		 if(getRequest('save')){
			$comApp->updateRecord(WARRANTY_TBL,"warranty_id",$id,"received_date","","","","warranty","list&customer_id=$sub_id");
			$msg="Successfully Update Record !!!";
			header("location:?app=warranty&cmd=list&msg=$msg");	      	
		 } 
	}else{		
		if(getRequest('save')) {
			$claim_id = $comApp->NewID(WARRANTY_TBL,"warranty_id","C000001","C",7);
			$comApp->saveRecord(WARRANTY_TBL,"warranty_id","$claim_id","received_date","","created_by","created_date","warranty","list&customer_id=$sub_id");
			$msg="Successfully Save Record !!!";
			header("location:?app=warranty&cmd=list&msg=$msg");     		       		      	
		 }			 
	}
	$f1Value = getRequest('srckey');
	if($f1Value=""){ $f1Value = getRequest('customer_id'); }
	$condition = "catagory='Warranty'";
	$data['product_list']  	= $comApp->getRecords(WARRANTY_TBL,"customer_id",$condition,"customer_name",$f1Value,"","",getRequest('from'),getRequest('to'));
	$data['totalrecord']  	= $comApp->getTotalRecords(WARRANTY_TBL,"customer_id",$condition,"customer_name",$f1Value,"",""); 
	$data['catagory_list']	= $comListApp->getCatagoryList();
	$data['brand_list'] 	= $comListApp->getBrandList();
	$data['message'] 		= $msg;
	$data['cmd']     		= getRequest('cmd'); 
	require_once(CURRENT_APP_SKIN_FILE);
	return $data[0];
   } 
   //========= Start Servicing ==============
   function Received4Servicing(){
   		require_once(CLASS_DIR.'/common.class.php');	
		$comApp = new Common(); 
		require_once(CLASS_DIR.'/common.list.class.php');	
		$comListApp 	= new CommonList();
				
		$id		 			= getRequest('id'); 
		$customer_id		= getRequest('customer_id'); 
		$data               = array();		
		
		if($id!=""){
	 	 $TBDArr			= $comApp->getRecordInfo(WARRANTY_TBL,"warranty_id",$id);      
		 $TBDArr 			= parseThisValue($TBDArr);
		 $data        		= array_merge(array(),$TBDArr);	 			
		 if(getRequest('save')){
			$comApp->updateRecord(WARRANTY_TBL,"warranty_id",$id,"received_date","","","","warranty","servicing&customer_id=$sub_id");
			$msg="Successfully Update Record !!!";
			header("location:?app=warranty&cmd=servicing&msg=$msg");	      	
		 } 
		}else{		
			if(getRequest('save')) {
			    $claim_id = $comApp->NewID(WARRANTY_TBL,"warranty_id","C000001","C",7);
				$comApp->saveRecord(WARRANTY_TBL,"warranty_id","$claim_id","received_date","","created_by","created_date","warranty","servicing&customer_id=$customer_id");
				$msg="Successfully Save Record !!!";
				header("location:?app=warranty&cmd=servicing&msg=$msg");     		       		      	
			 }			 
		}
		$f1Value = getRequest('srckey');
		if($f1Value=""){ $f1Value = getRequest('customer_id'); }
		$condition = "catagory='Servicing'";
		$data['product_list']  	= $comApp->getRecords(WARRANTY_TBL,"customer_id",$condition,"customer_name",$f1Value,"","",getRequest('from'),getRequest('to'));
		$data['totalrecord']  	= $comApp->getTotalRecords(WARRANTY_TBL,"customer_id",$condition,"customer_name",$f1Value,"",""); 
		$data['catagory_list']	= $comListApp->getCatagoryList();
		$data['brand_list'] 	= $comListApp->getBrandList();
		$data['message'] 		= $msg;
		$data['cmd']     		= getRequest('cmd'); 
		require_once(SERVICING_SKIN);
		return $data[0];
		
   }
   function ServicingStatus(){
		require_once(CLASS_DIR.'/common.class.php');	
		$comApp = new Common(); 
		require_once(CLASS_DIR.'/common.list.class.php');	
		$comListApp 	= new CommonList();
				
		$f1Value = getRequest('customer_name'); $f2Value = getRequest('serial_no');
		$condition = "status<3 AND catagory='Servicing'";
		$data['product_list']  	= $comApp->getRecords(WARRANTY_TBL,"customer_id",$condition,"customer_name",$f1Value,"serial_no",$f2Value,getRequest('from'),getRequest('to'));
		$data['totalrecord']  	= $comApp->getTotalRecords(WARRANTY_TBL,"customer_id",$condition,"customer_name",$f1Value,"",""); 
		$data['catagory_list']	= $comListApp->getCatagoryList();
		$data['brand_list'] 	= $comListApp->getBrandList();
		$data['message'] 		= $msg;
		$data['cmd']     		= getRequest('cmd'); 
		require_once(SERVICING_WAITING_LIST_SKIN);
		return $data[0];
   }
   function ServicingReport(){
		require_once(CLASS_DIR.'/common.class.php');	
		$comApp = new Common(); 
		require_once(CLASS_DIR.'/common.list.class.php');	
		$comListApp 	= new CommonList(); 
		$id		 = getRequest('id'); 
		$data               = array();		
		
		if($id!=""){
		 $TBDArr			= $comApp->getRecordInfo(WARRANTY_TBL,"warranty_id",$id);      
		 $TBDArr 			= parseThisValue($TBDArr);
		 $data        		= array_merge(array(),$TBDArr);	
		 if(getRequest('save')){
			$comApp->updateRecord(WARRANTY_TBL,"warranty_id",$id,"claim_receive_date","","","","warranty","service.status");
			$msg="Successfully Update Record !!!";
			header("location:?app=warranty&cmd=service.status&msg=$msg");	      	
		 } 
		}
		
		$data['message'] 		= $msg;
		$data['cmd']     		= getRequest('cmd'); 
		require_once(SERVICING_REPORT_SKIN);
		return $data[0];
   }
   function ServicingBillPrepare(){
		require_once(CLASS_DIR.'/common.class.php');	
		$comApp = new Common(); 
		require_once(CLASS_DIR.'/common.list.class.php');	
		$comListApp 	= new CommonList();
		require_once(CLASS_DIR.'/general_vouchar.class.php');	
		$gvApp = new GeneralVouchar();
		 
		$id		 = getRequest('id'); 
		$data               = array();		
		
		if($id!=""){
		 $TBDArr			= $comApp->getRecordInfo(WARRANTY_TBL,"warranty_id",$id);      
		 $TBDArr 			= parseThisValue($TBDArr);
		 $data        		= array_merge(array(),$TBDArr);	
		 if(getRequest('save')){
			$comApp->updateRecord(WARRANTY_TBL,"warranty_id",$id,"","","","","warranty","list&customer_id=$sub_id");			
			//======= Dr Account ======	
			$DrAmount	   = getRequest('service_bill'); 
			$customer_id   = getRequest('customer_id'); 
			$totalPartyCR  = $gvApp->getTotalCreditAmount($customer_id,getFromSession('project_id'));
			$totalPartyDR  = $gvApp->getTotalDebitAmount($customer_id,getFromSession('project_id'));					 
			$PartyBalance  = (($totalPartyDR+$DrAmount)-$totalPartyCR);					 
			$gvApp->saveAccountJournal($id,$customer_id,"Acc",getFromSession('project_id'),getRequest('description'),$DrAmount,0,$PartyBalance,1,date("Y-m-d"));
			$sql = "UPDATE ".WARRANTY_TBL." SET due='$DrAmount' WHERE warranty_id=".$id;
			$res = mysql_query($sql);
			$msg="Successfully Saved Record !!!";
			header("location:?app=warranty&cmd=service.status&msg=$msg");	      	
		 } 
		}
		
		$data['message'] 		= $msg;
		$data['cmd']     		= getRequest('cmd'); 
		require_once(SERVICING_BILL_SKIN);
		return $data[0];
   }
   function deleteItem(){
	require_once(CLASS_DIR.'/common.class.php');	
	$comApp = new Common(); 
	$product_id = getRequest('id');
	$comApp->deleteRecord(WARRANTY_TBL,"warranty_id",$warranty_id,"warranty","list"); 
   } 
    
} // End class
?>