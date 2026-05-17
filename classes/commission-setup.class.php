<?php
class CommissionSetup
{
   function run() {     
		$cmd    = getRequest('cmd');
		$u_t_id = getFromSession('u_type_id');
		if($u_t_id == 101) 
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
	 $cid = 1;	 
	 $data               = array();		
	 if($cid){
	 $TBDArr			= $comApp->getRecordInfo(COMMISSION_SLOT_TBL,"cid",$cid);      
	 $TBDArr 			= parseThisValue($TBDArr);
	 $data        		= array_merge(array(),$TBDArr);		 			
	 if(getRequest('save')){
		$comApp->updateRecord(COMMISSION_SLOT_TBL,"cid",$cid,"","","modefied_by","modefied_date","commission-setup","list");
		$msg="Successfully Update Record !!!";
		header("location:?app=commission-setup&cmd=list&msg=$msg");	      	
	 } 
	} else {		
	if(getRequest('save')) {
		$comApp->saveRecord(COMMISSION_SLOT_TBL,"cid","","","","","","commission-setup","list");
		$msg="Successfully Save Record !!!";
		header("location:?app=commission-setup&cmd=list&msg=$msg");     		       		      	
	 }			 
	}
	
	$data['message'] 		= $msg;
	$data['cmd']     		= getRequest('cmd'); 
	require_once(CURRENT_APP_SKIN_FILE);
	return $data[0];
   }   
   function deleteItem(){
	require_once(CLASS_DIR.'/common.class.php');	
	$comApp = new Common(); 
	$sub_id = getRequest('id');
	$comApp->deleteRecord(COMMISSION_SLOT_TBL,"cid",$sub_id,"commission-setup","list"); 
   }  
} // End class
?>