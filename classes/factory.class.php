<?php
class Factory
{
   function run() {     
		$cmd    = getRequest('cmd');
		$u_t_id = getFromSession('u_type_id');
		if($u_t_id == 101 || $u_t_id == 102)  // 101 super, 102 admin
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
	 $factory_id = getRequest('id');	 
	 $data               = array();		
	 if($factory_id){
	 $TBDArr			= $comApp->getRecordInfo(FACTORY_TBL,"factory_id",$factory_id);      
	 $TBDArr 			= parseThisValue($TBDArr);
	 $data        		= array_merge(array(),$TBDArr);		 			
	 if(getRequest('save')){
		$comApp->updateRecord(FACTORY_TBL,"factory_id",$factory_id,"","","modefied_by","modefied_date","factory","list");
		$msg="Successfully Update Record !!!";
		header("location:?app=factory&cmd=list&msg=$msg");	      	
	 } 
	} else {		
	if(getRequest('save')) {
		$accessories_id = $comApp->NewID(FACTORY_TBL,"factory_id","F1000","F",5);
		$comApp->saveRecord(FACTORY_TBL,"factory_id",$accessories_id,"","","created_by","created_date","factory","list");
		$msg="Successfully Save Record !!!";
		header("location:?app=factory&cmd=list&msg=$msg");     		       		      	
	 }			 
	}
	$f1Value = getRequest('srckey');
	$from = getRequest('from'); if($from==""){ $from = 0;}
	$to = getRequest('to'); if($to==""){ $to = 45;}
	$data['brand_list']  	= $comApp->getRecords(FACTORY_TBL,"factory_id","","factory_name",$f1Value,"","",$from,$to);
	$data['totalrecord']  	= $comApp->getTotalRecords(FACTORY_TBL,"factory_id","","factory_name",$f1Value,"",""); 
	$data['catagory_list']	= $comListApp->getCatagoryList();
	$data['project_id'] 	= getFromSession('project_id');
	$data['message'] 		= $msg;
	$data['cmd']     		= getRequest('cmd'); 
	require_once(CURRENT_APP_SKIN_FILE);
	return $data[0];
   }   
   function deleteItem(){
	require_once(CLASS_DIR.'/common.class.php');	
	$comApp = new Common(); 
	$factory_id = getRequest('id');
	$comApp->deleteRecord(FACTORY_TBL,"factory_id",$factory_id,"factory","list"); 
   }  
} // End class
?>