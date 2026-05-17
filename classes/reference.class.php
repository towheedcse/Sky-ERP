<?php
class Reference
{
   function run() {     
		$cmd    = getRequest('cmd');
		$u_t_id = getFromSession('u_type_id');
		if($u_t_id == 101) 
		{      
		  switch($cmd) { 
		  case 'add'               : $screen = $this->showEditor($msg); break;
		  case 'edit'              : $screen = $this->showEditor("Edit Page");    break;	
		  case 'doUpdate'          : $screen = $this->showEditor($msg); break;
		  case 'delete'            : $screen = $this->deleteItem(); break;
		  default                  : $cmd = 'list'; $screen = $this->showEditor($msg);   break;
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
	 $sub_id = getRequest('id');	 
	 $data               = array();		
	 if($sub_id){
	 $TBDArr			= $comApp->getRecordInfo(SUB_ACC_HEAD_TBL,"sub_id",$sub_id);      
	 $TBDArr 			= parseThisValue($TBDArr);
	 $data        		= array_merge(array(),$TBDArr);		 			
	 if(getRequest('save')){
		$comApp->updateRecord(SUB_ACC_HEAD_TBL,"sub_id",$sub_id,"joining_date","","","","reference","list");
		$msg="Successfully Update Record !!!";
		header("location:?app=reference&cmd=list&msg=$msg");	      	
	 } 
	} else {		
	if(getRequest('save')) {
		$comApp->saveRecord(SUB_ACC_HEAD_TBL,"sub_id","","joining_date","","created_by","created_date","reference","list");
		$msg="Successfully Save Record !!!";
		header("location:?app=reference&cmd=list&msg=$msg");     		       		      	
	 }			 
	}
	$f1Value = getRequest('srckey');
	$comdition = "head_type='Reference' ";
	$from =getRequest('from'); if($from==""){ $from=0;} $to =getRequest('to'); if($to==""){ $to=20;}
	$data['customer_list']  = $comApp->getRecords(SUB_ACC_HEAD_TBL,"sub_id",$comdition,"sub_head_name",$f1Value,"","",$from,$to);
	$data['totalrecord']  	= $comApp->getTotalRecords(SUB_ACC_HEAD_TBL,"sub_id",$comdition,"sub_head_name",$f1Value,"",""); 
	$data['catagory_list']	= $comListApp->getCatagoryList();
	$data['brand_list'] 	= $comListApp->getBrandList();
	$data['message'] 		= $msg;
	$data['cmd']     		= getRequest('cmd'); 
	require_once(CURRENT_APP_SKIN_FILE);
	return $data[0];
   }   
   function deleteItem(){
	require_once(CLASS_DIR.'/common.class.php');	
	$comApp = new Common(); 
	$sub_id = getRequest('id');
	$comApp->deleteRecord(SUB_ACC_HEAD_TBL,"sub_id",$sub_id,"reference","list"); 
   }  
} // End class
?>
