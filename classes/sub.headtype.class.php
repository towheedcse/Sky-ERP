<?php
class SubHeadType
{
   function run() {     
		$cmd    = getRequest('cmd');
		$u_t_id = getFromSession('u_type_id');
		if($u_t_id == 101 || $u_t_id == 102 || $u_t_id == 104) 
		{      
		  switch($cmd) { 
		  	case 'add'             : $screen = $this->showEditor($msg); break;
      	     		case 'edit'            : $screen = $this->showEditor("Edit Page");    break;
      	   	 	case 'doUpdate'        : $screen = $this->showEditor($msg); break;
		     	case 'delete'          : $screen = $this->deleteItem(); break;
			default                : $cmd = 'list'; $screen = $this->showEditor($msg);   break;
		  }
		}elseif($u_t_id == 107) 
		{      
		  switch($cmd) { 
		  	case 'add'             : $screen = $this->showEditor($msg); break;
      	     		case 'edit'            : $screen = $this->showEditor("Edit Page");    break;
      	   	 	case 'doUpdate'        : $screen = $this->showEditor($msg); break;
			default                : $cmd = 'list'; $screen = $this->showEditor($msg);   break;
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
	 $sub_htid = getRequest('id');	 
	 $data               = array();		
	 if($sub_htid){
	 $TBDArr			= $comApp->getRecordInfo(SUB_HEAD_TYPE_TBL,"sub_htid",$sub_htid);      
	 $TBDArr 			= parseThisValue($TBDArr);
	 $data        		= array_merge(array(),$TBDArr);		 			
	 if(getRequest('save')){
		$comApp->updateRecord(SUB_HEAD_TYPE_TBL,"sub_htid",$sub_htid,"","","","","sub.headtype","list");
		$msg="Successfully Update Record !!!";
		header("location:?app=sub.headtype&cmd=list&msg=$msg");	      	
	 } 
	} else {		
	if(getRequest('save')) {
		$subhtid = $comApp->NewID(SUB_HEAD_TYPE_TBL,"sub_htid","S101","S",4);
		$comApp->saveRecord(SUB_HEAD_TYPE_TBL,"sub_htid",$subhtid,"","","created_by","created_date","sub.headtype","list");
		$msg="Successfully Save Record !!!";
		header("location:?app=sub.headtype&cmd=list&msg=$msg");     		       		      	
	 }			 
	}
	$srckey = getRequest('srckey');
	if($srckey !=""){$comdition = "sub_head_type LIKE '%".$srckey."%'";}else{$comdition="";}
	if($srckey !="" && getRequest('srchead_type') !=""){
	$comdition.= "AND head_type ='".getRequest('srchead_type')."'";
	}elseif($srckey =="" && getRequest('srchead_type') !=""){
	$comdition.= "head_type ='".getRequest('srchead_type')."'";
	}
	$f1Name  = "";
	$f1Value = "";

	$f2Name  = "";
	$f2Value = "";
	$from = getRequest('from'); if($from==""){ $from = 0;}
	$to = getRequest('to'); if($to==""){ $to = 45;}
	
	$data['headtype_list']  = $comApp->getRecords(SUB_HEAD_TYPE_TBL,"sub_htid",$comdition,$f1Name,$f1Value,$f2Name,$f2Value,$from,$to);
	$data['totalrecord']  	= $comApp->getTotalRecords(SUB_HEAD_TYPE_TBL,"sub_htid",$comdition,$f1Name,$f1Value,$f2Name,$f2Value); 
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
	$sub_htid = getRequest('id');
	$comApp->deleteRecord(SUB_HEAD_TYPE_TBL,"sub_htid",$sub_htid,"sub.headtype","list"); 
   }  
} // End class
?>
