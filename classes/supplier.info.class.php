<?php
class Supplier
{
   function run() {     
		$cmd    = getRequest('cmd');
		$u_t_id = getFromSession('u_type_id');
		if($u_t_id == 101 || $u_t_id == 102 || $u_t_id == 106) 
		{      
		  switch($cmd) { 
		  	 case 'add'                	: $screen = $this->showEditor($msg); break;
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
  function showEditor()
  {
	 require_once(CLASS_DIR.'/common.class.php');	
	 $comApp = new Common(); 
	 require_once(CLASS_DIR.'/common.list.class.php');	
	 $comListApp 	= new CommonList(); 
	 $supplier_code = getRequest('id');	 
	 $data               = array();		
	 if($supplier_code){
	 $TBDArr			= $comApp->getRecordInfo(SUPPLIER_TBL,"supplier_code",$supplier_code);      
	 $TBDArr 			= parseThisValue($TBDArr);
	 $data        		= array_merge(array(),$TBDArr);		 			
	 if(getRequest('save')){
		$comApp->updateRecord(SUPPLIER_TBL,"supplier_code",$supplier_code,"","","","","supplier.info","list");
		$msg="Successfully Update Record !!!";
		header("location:?app=supplier.info&cmd=list&msg=$msg");	      	
	 } 
	} else {		
	if(getRequest('save')) {
		$accessories_id = $comApp->NewID(SUPPLIER_TBL,"supplier_code","S520000","S",7);
		$comApp->saveRecord(SUPPLIER_TBL,"supplier_code",$accessories_id,"","","created_by","created_date","supplier.info","list");
		$msg="Successfully Save Record !!!";
		header("location:?app=supplier.info&cmd=list&msg=$msg");     		       		      	
	 }			 
	}
	$f1Value = getRequest('srckey');
	$data['supplier_list']  = $comApp->getRecords(SUPPLIER_TBL,"supplier_code","","name",$f1Value,"","",getRequest('from'),getRequest('to'));
	$data['totalrecord']  	= $comApp->getTotalRecords(SUPPLIER_TBL,"supplier_code","","name",$f1Value,"",""); 
	$data['country_list']	= $comListApp->getCountryList();
	$data['message'] 		= $msg;
	$data['cmd']     		= getRequest('cmd'); 
	require_once(CURRENT_APP_SKIN_FILE);
	return $data[0];
   }   
   function deleteItem(){
	require_once(CLASS_DIR.'/common.class.php');	
	$comApp = new Common(); 
	$supplier_code = getRequest('id');
	$comApp->deleteRecord(SUPPLIER_TBL,"supplier_code",$supplier_code,"supplier.info","list"); 
   }  
} // End class
?>