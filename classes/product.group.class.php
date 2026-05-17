<?php
class ProductGroup
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
	 $group_id = getRequest('id');	 
	 $data               = array();		
	 if($group_id){
	 $TBDArr			= $comApp->getRecordInfo(PRODUCT_GROUP_TBL,"group_id",$group_id);      
	 $TBDArr 			= parseThisValue($TBDArr);
	 $data        		= array_merge(array(),$TBDArr);		 			
	 if(getRequest('save')){
		$comApp->updateRecord(PRODUCT_GROUP_TBL,"group_id",$group_id,"","","modified_by","modified_time","product.group","list");
		$msg="Successfully Update Record !!!";
		header("location:?app=product.group&cmd=list&msg=$msg");	      	
	 } 
	} else {		
	if(getRequest('save')) {
		$accessories_id = $comApp->NewID(PRODUCT_GROUP_TBL,"group_id","G0000","G",5);
		$comApp->saveRecord(PRODUCT_GROUP_TBL,"group_id",$accessories_id,"","","created_by","created_date","product.group","list");
		$msg="Successfully Save Record !!!";
		header("location:?app=product.group&cmd=list&msg=$msg");     		       		      	
	 }			 
	}
	$f1Value = getRequest('srckey');
	$from = getRequest('from'); if($from==""){ $from = 0;}
	$to = getRequest('to'); if($to==""){ $to = 45;}
	$data['brand_list']  	= $comApp->getRecords(PRODUCT_GROUP_TBL,"group_id","","group_name",$f1Value,"","",$from,$to);
	$data['totalrecord']  	= $comApp->getTotalRecords(PRODUCT_GROUP_TBL,"group_id","","group_name",$f1Value,"",""); 
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
	$group_id = getRequest('id');
	$comApp->deleteRecord(PRODUCT_GROUP_TBL,"group_id",$group_id,"product.group","list"); 
   }  
} // End class
?>