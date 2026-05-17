<?php
class Brand
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
	 $brand_id = getRequest('id');	 
	 $data               = array();		
	 if($brand_id){
	 $TBDArr			= $comApp->getRecordInfo(BRAND_TBL,"brand_id",$brand_id);      
	 $TBDArr 			= parseThisValue($TBDArr);
	 $data        		= array_merge(array(),$TBDArr);		 			
	 if(getRequest('save')){
		$comApp->updateRecord(BRAND_TBL,"brand_id",$brand_id,"","","","","brand","list");
		$msg="Successfully Update Record !!!";
		header("location:?app=brand&cmd=list&msg=$msg");	      	
	 } 
	} else {		
	if(getRequest('save')) {
		$accessories_id = $comApp->NewID(BRAND_TBL,"brand_id","B0001","B",5);
		$comApp->saveRecord(BRAND_TBL,"brand_id",$accessories_id,"","","created_by","created_date","brand","list");
		$msg="Successfully Save Record !!!";
		header("location:?app=brand&cmd=list&msg=$msg");     		       		      	
	 }			 
	}
	$f1Value = getRequest('srckey');
	$from = getRequest('from'); if($from==""){ $from = 0;}
	$to = getRequest('to'); if($to==""){ $to = 45;}
	$data['brand_list']  	= $comApp->getRecords(BRAND_TBL,"brand_id","","brand_name",$f1Value,"","",$from,$to);
	$data['totalrecord']  	= $comApp->getTotalRecords(BRAND_TBL,"brand_id","","brand_name",$f1Value,"",""); 
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
	if(!userCondition(true)){
		$msg = "You are not authorized !!!";
	      	header("location:index.php?app=brand&msg=$msg");
	      	exit;
	    }
	$brand_id = getRequest('id');
	$comApp->deleteRecord(BRAND_TBL,"brand_id",$brand_id,"brand","list"); 
   }  
} // End class
?>
