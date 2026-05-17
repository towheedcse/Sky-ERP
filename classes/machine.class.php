<?php
class Machine
{
   function run() {     
		$cmd    = getRequest('cmd');
		$u_t_id = getFromSession('u_type_id');
		if($u_t_id == 101 || $u_t_id == 102 || $u_t_id == 106) 
		{      
		  switch($cmd) { 
		  	case 'add'     : $screen = $this->showEditor($msg); break;
      	     		case 'edit'    : $screen = $this->showEditor("Edit Page"); break;
      	   	 	case 'doUpdate': $screen = $this->showEditor($msg); break;
		     	case 'delete'  : $screen = $this->deleteItem(); break;
			default        : $cmd = 'list'; $screen = $this->showEditor($msg); break;
		  }
		}else if($u_t_id == 107) 
		{      
		  switch($cmd) { 
		  	 case 'add'     : $screen = $this->showEditor($msg); break;
      	     		 case 'edit'    : $screen = $this->showEditor("Edit Page"); break;
      	   	 	 case 'doUpdate': $screen = $this->showEditor($msg); break;		     
			 default        : $cmd = 'list'; $screen = $this->showEditor($msg); break;
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
	 $machine_id    = getRequest('id');	 
	 $data               = array();		
	 if($machine_id){
	 $TBDArr		= $comApp->getRecordInfo(MACHINE_TBL,"machine_id",$machine_id);      
	 $TBDArr 		= parseThisValue($TBDArr);
	 $data        		= array_merge(array(),$TBDArr);		 			
	 if(getRequest('save')){
		$comApp->updateRecord(MACHINE_TBL,"machine_id",$machine_id,"","","modefied_by","modefied_date","machine","list");
		$msg="Successfully Update Record !!!";
		header("location:?app=machine&cmd=list&msg=$msg");	      	
	 } 
	} else {		
	if(getRequest('save')) {
		$machine_id = $comApp->NewID(MACHINE_TBL,"machine_id","M000","M",4);
		$comApp->saveRecord(MACHINE_TBL,"machine_id","$machine_id","","","created_by","created_date","machine","list");
		$msg="Successfully Save Record !!!";
		header("location:?app=machine&cmd=list&msg=$msg");     		       		      	
	 }			 
	}
	$f1Value = getRequest('srckey');
	$from =getRequest('from'); if($from==""){ $from=0;} $to =getRequest('to'); if($to==""){ $to=20;}
	$data['record_list']  = $comApp->getRecords(MACHINE_TBL,"machine_id","","machine_name",$f1Value,"","",$from,$to);
	$data['totalrecord']  = $comApp->getTotalRecords(MACHINE_TBL,"machine_id","","machine_name",$f1Value,"","");
	$data['message'] 		= $msg;
	$data['cmd']     		= getRequest('cmd'); 
	require_once(CURRENT_APP_SKIN_FILE);
	return $data[0];
   }   
   function deleteItem(){
	require_once(CLASS_DIR.'/common.class.php');	
	$comApp 	= new Common(); 
	$machine_id 	= getRequest('id');
	$comApp->deleteRecord(MACHINE_TBL,"machine_id",$machine_id,"machine","list"); 
   }
     
} // End class
?>
