<?php
class District
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
		}else if($u_t_id == 107) 
		{      
		  switch($cmd) { 
		  case 'add'                	: $screen = $this->showEditor($msg); break;
      	     	  case 'edit'               	: $screen = $this->showEditor("Edit Page"); break;	 
      	   	  case 'doUpdate'           	: $screen = $this->showEditor($msg); break;		     
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
	 $district_id = getRequest('id');	 
	 $data               = array();		
	 if($district_id){
	 $TBDArr			= $comApp->getRecordInfo(DISTRICT_TBL,"district_id",$district_id);      
	 $TBDArr 			= parseThisValue($TBDArr);
	 $data        		= array_merge(array(),$TBDArr);		 			
	 if(getRequest('save')){
		$this->areaReset($district_id); 
		$comApp->updateRecord(DISTRICT_TBL,"district_id",$district_id,"","","","","district","list");
		$msg="Successfully Update Record !!!";
		header("location:?app=district&cmd=list&msg=$msg");	      	
	 } 
	} else {		
	if(getRequest('save')) {
		$comApp->saveRecord(DISTRICT_TBL,"","","","","created_by","created_date","district","list");
		$msg="Successfully Save Record !!!";
		header("location:?app=district&cmd=list&msg=$msg");     		       		      	
	 }			 
	}
	$f1Value = getRequest('srckey');
	$from = getRequest('from'); if($from==""){ $from = 0;}
	$to = getRequest('to'); if($to==""){ $to = 45;}
	$data['brand_list']  	= $comApp->getRecords(DISTRICT_TBL,"district_id","","district_name",$f1Value,"","",$from,$to);
	$data['totalrecord']  	= $comApp->getTotalRecords(DISTRICT_TBL,"district_id","","district_name",$f1Value,"",""); 
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
		$district_id = getRequest('id');
		
		$project_id  = getFromSession('project_id');
		$SSql="SELECT * FROM ".SUPPLIER_TBL." WHERE district='$district_id' AND project_id='$project_id'"; 
		$sres = mysql_query($SSql);
		$SNUM = mysql_num_rows($sres);
		
		$CSql="SELECT * FROM ".SUB_ACC_HEAD_TBL." WHERE district='$district_id' AND project_id='$project_id' AND head_type='Customer'"; 
		$cres = mysql_query($CSql);
		$CNUM = mysql_num_rows($cres);
		
		if($SNUM ==0 && $CNUM ==0){	 
			$comApp->deleteRecord(DISTRICT_TBL,"district_id",$district_id,"district","list"); 
		}else{
			$msg="You can not delete this area !!!";
			header("location:?app=district&cmd=list&msg=$msg");
		}		
	
   }
   
   function areaReset($district_id){
	   $division_id 	= getRequest('division_id');
	   $old_division 	= getRequest('old_division');
	   $project_id  	= getFromSession('project_id');
	   $SSql="SELECT * FROM ".SUPPLIER_TBL." WHERE division='$old_division' AND district='$district_id' AND project_id='$project_id'"; 
		$sres = mysql_query($SSql);
		$SNUM = mysql_num_rows($sres);
		
		if($SNUM >0){	 
			$susql="UPDATE ".SUPPLIER_TBL." SET division ='$division_id'  WHERE division ='$old_division' AND district='$district_id' AND project_id='$project_id'";
			mysql_query($susql); 
		}
				
		$CSql="SELECT * FROM ".SUB_ACC_HEAD_TBL." WHERE district='$district_id' AND project_id='$project_id' AND head_type='Customer'"; 
		$cres = mysql_query($CSql);
		$CNUM = mysql_num_rows($cres);
		
		if($CNUM >0){	 
			$cusql="UPDATE ".SUB_ACC_HEAD_TBL." SET division ='$division_id'  WHERE division ='$old_division' AND district ='$district_id' AND project_id='$project_id'";
			mysql_query($cusql); 
		}
   }
   
} // End class
?>
