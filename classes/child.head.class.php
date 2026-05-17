<?php
class ChildHead
{
   function run() {     
		$cmd    = getRequest('cmd');
		$u_t_id = getFromSession('u_type_id');
		if($u_t_id == 101) 
		{      
		  switch($cmd) { 
		  case 'add'              : $screen = $this->showEditor($msg); break;
      	          case 'loadsubhtype'  	  : $this->loadSubHeadType(trim(getRequest('head_type'))); break; 			  case 'edit'             : $screen = $this->showEditor("Edit Page");    break;
      	   	  case 'doUpdate'         : $screen = $this->showEditor($msg); break;
		  case 'delete'           : $screen = $this->deleteItem(); break;
		  default                 : $cmd = 'list'; $screen = $this->showEditor($msg);   break;
		  }
		}else if($u_t_id == 107) 
		{      
		  switch($cmd) { 
		  case 'add'              : $screen = $this->showEditor($msg); break;
      	          case 'loadsubhtype'  	  : $this->loadSubHeadType(trim(getRequest('head_type'))); break; 			  case 'edit'             : $screen = $this->showEditor("Edit Page");    break;	 
      	   	  case 'doUpdate'         : $screen = $this->showEditor($msg); break;
		  default           	  : $cmd = 'list'; $screen = $this->showEditor($msg);   break;
		  }
		}elseif($u_t_id == 102) 
		{      
		  switch($cmd) { 
		  case 'add'              : $screen = $this->showEditor($msg); break; 	
      	          case 'edit'             : $screen = $this->showEditor("Edit Page"); break;
      	   	  case 'doUpdate'         : $screen = $this->showEditor($msg); break;
       	    	  case 'loadsubhtype'  	  : $this->loadSubHeadType(trim(getRequest('head_type'))); break;
		  default                 : $cmd = 'list'; $screen = $this->showEditor($msg);   break;
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
	 $child_id 	= getRequest('id');	 
	 $data          = array();		
	 if($child_id){
	 $TBDArr	= $comApp->getRecordInfo(CHILD_HEAD_TYPE_TBL,"child_id",$child_id);      
	 $TBDArr 	= parseThisValue($TBDArr);
	 $data        	= array_merge(array(),$TBDArr);		 			
	 if(getRequest('save')){
		$ures = $this->updateRecord($child_id);
		if($ures){
		$msg="Successfully Update Record !!!";
		header("location:?app=child.head&cmd=list&msg=$msg");	
		}else{
			$msg="Please try again !!!";
		}			      	
	 } 
	} else {		
	if(getRequest('save')) {
		$accessories_id = $comApp->NewID(CHILD_HEAD_TYPE_TBL,"child_id","C000000","C",7);
		$comApp->saveRecord(CHILD_HEAD_TYPE_TBL,"child_id",$accessories_id,"","","created_by","created_time","accounts.head","list");
		$msg="Successfully Save Record !!!";
		header("location:?app=child.head&cmd=list&msg=$msg");     		       		      	
	 }			 
	}
	$srckey = getRequest('srckey');
	if($srckey !=""){$comdition = "child_head_name LIKE '%".$srckey."%'";}else{$comdition="";}
	$f1Name  = "head_type";
	$f1Value = getRequest('srchead_type');
	if($comdition !="" && $f1Value!=""){$comdition.= " AND head_type= '".$f1Value."'";}
	elseif($comdition =="" && $f1Value!=""){$comdition.= "head_type= '".$f1Value."'";}

	$f1Name  = "sub_head";
	$f1Value = getRequest('srcsub_headtype');
	$from = getRequest('from'); if($from==""){ $from = 0;}
	$to = getRequest('to'); if($to==""){ $to = 45;}
	$data['brand_list']  	= $comApp->getRecords(CHILD_HEAD_TYPE_TBL,"child_id",$comdition,$f1Name,$f1Value,"","",$from,$to);
	$data['totalrecord']  	= $comApp->getTotalRecords(CHILD_HEAD_TYPE_TBL,"child_id",$comdition,$f1Name,$f1Value,"",""); 
	$data['catagory_list']	= $comListApp->getCatagoryList();
	$data['project_id'] 	= getFromSession('project_id');
	$data['message'] 	= $msg;
	$data['cmd']     	= getRequest('cmd'); 
	require_once(CURRENT_APP_SKIN_FILE);
	return $data[0];
   } 
   
   function updateRecord($id)
   {       
	$requestdata = array();
	$requestdata = getUserDataSet(CHILD_HEAD_TYPE_TBL); 
	$requestdata['project_id']   	= getFromSession('project_id');
	$requestdata["modified_by"]  	= getFromSession('userid');
	$requestdata["modified_time"]	= date('Y-m-d h:i:s');
	$info        			=  array();
	$info['table']			= CHILD_HEAD_TYPE_TBL; 
	if(getRequest('sub_headtype')==""){
	$requestdata["sub_headtype"] = " ";
	}	
	//dBug($requestdata);
	$info['data'] 		= $requestdata;
	$info['where']		= "child_id ='$id'";  
	//$info['debug']  		=  true;    
	$res = update($info);
	if(!$res) {
		return false;
	}else{
		return true;  
	}
   }//EOFn  
   function loadSubHeadType($head_type)
   {	  
	  $project_id 	   = getFromSession('project_id');  
	  $info            = array();
	  $info['table']   = SUB_HEAD_TYPE_TBL;
	  $info['fields']  =  array('sub_htid','sub_head_type');
	  $SQL = "head_type='$head_type' AND project_id='$project_id' ";
	  $info['where']   = $SQL; 
	  $info['groupby'] = array("sub_htid");
	  $info['orderby'] = array("sub_head_type ASC");
	  //$info['debug']   = true;	
	  $result          = select($info);
	  $data            = array();	
	  if(count($result)){
		 foreach($result as $key=>$value){
			$data[$key][]        = $value;
		 }
	  }				
	  foreach($data as $i=>$v){
		 $subject_idname .= $v[0]->sub_htid.'#####'.$v[0]->sub_head_type.'@@@';
	  }
	  echo $subject_idname;	
	} 
  
   function deleteItem(){
	require_once(CLASS_DIR.'/common.class.php');	
	$comApp = new Common(); 
	$child_id = getRequest('id');
	$comApp->deleteRecord(CHILD_HEAD_TYPE_TBL,"child_id",$child_id,"child.head","list"); 
   }  
} // End class
?>
