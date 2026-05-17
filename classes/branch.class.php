<?php
class Branch
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
	 $branch_id = getRequest('id');	 
	 $data               = array();		
	 if($branch_id){
	 $TBDArr			= $comApp->getRecordInfo(BRANCH_TBL,"branch_id",$branch_id);      
	 $TBDArr 			= parseThisValue($TBDArr);
	 $data        		= array_merge(array(),$TBDArr);		 			
	 if(getRequest('save')){
		$comApp->updateRecord(BRANCH_TBL,"branch_id",$branch_id,"","","modefied_by","modefied_time","branch","list");
		$msg="Successfully Update Record !!!";
		header("location:?app=branch&cmd=list&msg=$msg");	      	
	 } 
	} else {		
	if(getRequest('save')) {
		$branch_code = $comApp->NewID(BRANCH_TBL,"branch_id","D1000","D",5);
		$comApp->saveRecord(BRANCH_TBL,"branch_id",$branch_code,"","","created_by","created_date","branch","list");
		$msg="Successfully Save Record !!!";
		header("location:?app=branch&cmd=list&msg=$msg");     		       		      	
	 }			 
	}
	$f1Value = getRequest('srckey');
	$data['supplier_list']  = $comApp->getRecords(BRANCH_TBL,"branch_id","","name",$f1Value,"","",getRequest('from'),getRequest('to'));
	$data['totalrecord']  	= $comApp->getTotalRecords(BRANCH_TBL,"branch_id","","name",$f1Value,"",""); 
	$data['country_list']	= $comListApp->getCountryList();
	$data['message'] 		= $msg;
	$data['cmd']     		= getRequest('cmd'); 
	require_once(CURRENT_APP_SKIN_FILE);
	return $data[0];
   }   
   function deleteItem(){
	require_once(CLASS_DIR.'/common.class.php');	
	$comApp = new Common(); 
	$branch_id = getRequest('id');
	$comApp->deleteRecord(BRANCH_TBL,"branch_id",$branch_id,"branch","list"); 
   }  
} // End class
?>