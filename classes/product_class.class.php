<?php
class ProductClass
{
   function run() {     
		$cmd    = getRequest('cmd');
		$u_t_id = getFromSession('u_type_id');
		if($u_t_id == 101 || $u_t_id == 102 || $u_t_id == 103 || $u_t_id == 106) 
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
	 $pclass_id = getRequest('id');	 
	 $data               = array();		
	 if($pclass_id){
	 $TBDArr			= $comApp->getRecordInfo(PRUDUCT_CLASS_TBL,"pclass_id",$pclass_id);      
	 $TBDArr 			= parseThisValue($TBDArr);
	 $data        		= array_merge(array(),$TBDArr);		 			
	 if(getRequest('save')){
		$comApp->updateRecord(PRUDUCT_CLASS_TBL,"pclass_id",$pclass_id,"","","modefied_by","modefied_date","product_class","list");
		$msg="Successfully Update Record !!!";
		header("location:?app=product_class&cmd=list&msg=$msg");	      	
	 } 
	} else {		
	if(getRequest('save')) {
		$accessories_id = $comApp->NewID(PRUDUCT_CLASS_TBL,"pclass_id","C2000","C",5);
		$comApp->saveRecord(PRUDUCT_CLASS_TBL,"pclass_id",$accessories_id,"","","created_by","created_date","product_class","list");
		$msg="Successfully Save Record !!!";
		header("location:?app=product_class&cmd=list&msg=$msg");     		       		      	
	 }			 
	}
	$f1Value = getRequest('srckey');
	$from = getRequest('from'); if($from==""){ $from = 0;}
	$to = getRequest('to'); if($to==""){ $to = 45;}
	$data['brand_list']  	= $comApp->getRecords(PRUDUCT_CLASS_TBL,"pclass_id","","product_class_name",$f1Value,"","",$from,$to);
	$data['totalrecord']  	= $comApp->getTotalRecords(PRUDUCT_CLASS_TBL,"pclass_id","","product_class_name",$f1Value,"",""); 
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
	$pclass_id = getRequest('id');
	$comApp->deleteRecord(PRUDUCT_CLASS_TBL,"pclass_id",$pclass_id,"product_class","list"); 
   }  
} // End class
?>