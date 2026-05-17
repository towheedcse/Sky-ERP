<?php
class Area
{
   function run() {     
		$cmd    = getRequest('cmd');
		$u_t_id = getFromSession('u_type_id');
		if($u_t_id == 101) 
		{      
		  switch($cmd) { 
		  	case 'add'             : $this->showEditor($msg); break;
      	     		case 'edit'            : $this->showEditor("Edit Page");    break;
      	   	 	case 'doUpdate'        : $this->showEditor($msg); break;
		     	case 'delete'          : $this->deleteItem(); break;
			default                : $cmd = 'list'; $screen = $this->showEditor($msg); break;
		  }
		}elseif($u_t_id == 107) 
		{      
		  switch($cmd) { 
		     case 'add'                	: $screen = $this->showEditor($msg); break;
      	     	     case 'edit'               	: $screen = $this->showEditor("Edit Page"); break;	     			     case 'doUpdate'           	: $screen = $this->showEditor($msg); break;	
	      	     case 'loadDistrict'  	: $this->loadDistrict(trim(getRequest('division_id'))); break;
	      	     case 'loadArea'  		: $this->loadArea(trim(getRequest('district'))); break;	      	   	 
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
	 $area_id = getRequest('id');	 
	 $data               = array();		
	 if($area_id){
	 $TBDArr			= $comApp->getRecordInfo(AREA_TBL,"area_id",$area_id);      
	 $TBDArr 			= parseThisValue($TBDArr);
	 $data        		= array_merge(array(),$TBDArr);		 			
	 if(getRequest('save')){
		$this->TRTReset($area_id);
		$comApp->updateRecord(AREA_TBL,"area_id",$area_id,"","","","","area","list");
		$msg="Successfully Update Record !!!";
		header("location:?app=area&cmd=list&msg=$msg");	      	
	 } 
	} else {		
	if(getRequest('save')) {
		$accessories_id = $comApp->NewID(AREA_TBL,"area_id","A0001","A",5);
		$comApp->saveRecord(AREA_TBL,"area_id",$accessories_id,"","","created_by","created_date","area","list");
		$msg="Successfully Save Record !!!";
		header("location:?app=area&cmd=list&msg=$msg");     		       		      	
	 }			 
	}
	$f1Value = getRequest('srckey');
	$from = getRequest('from'); if($from==""){ $from = 0;}
	$to = getRequest('to'); if($to==""){ $to = 45;}
	$data['area_list']  	= $comApp->getRecords(AREA_TBL,"area_id","","brand_name",$f1Value,"","",$from,$to);
	$data['totalrecord']  	= $comApp->getTotalRecords(AREA_TBL,"area_id","","brand_name",$f1Value,"",""); 
	$data['district_list']	= $comListApp->getDistrictList();
	$data['project_id'] 	= getFromSession('project_id');
	$data['message'] 		= $msg;
	$data['cmd']     		= getRequest('cmd'); 
	require_once(CURRENT_APP_SKIN_FILE);
	return $data[0];
   }   
   function deleteItem(){
		require_once(CLASS_DIR.'/common.class.php');	
		$comApp = new Common(); 
		$area_id = getRequest('id');
		
		$project_id  = getFromSession('project_id');
		$SSql="SELECT * FROM ".SUPPLIER_TBL." WHERE area='$area_id' AND project_id='$project_id'"; 
		$sres = mysql_query($SSql);
		$SNUM = mysql_num_rows($sres);
		
		$CSql="SELECT * FROM ".SUB_ACC_HEAD_TBL." WHERE area='$area_id' AND project_id='$project_id' AND head_type='Customer'"; 
		$cres = mysql_query($CSql);
		$CNUM = mysql_num_rows($cres);
		
		if($SNUM ==0 && $CNUM ==0){	 
			$comApp->deleteRecord(AREA_TBL,"area_id",$area_id,"area","list"); 
		}else{
			$msg="You can not delete this TRT !!!";
			header("location:?app=area&cmd=list&msg=$msg");
		}
		
   }  
   function TRTReset($area_id){
	   $division_id 	= getRequest('division_id');
	   $old_division 	= getRequest('old_division');
	   $district_id 	= getRequest('district');
	   $old_district 	= getRequest('old_district');
	   $project_id  	= getFromSession('project_id');
	   $SSql="SELECT * FROM ".SUPPLIER_TBL." WHERE district='$old_district' AND area='$area_id' AND project_id='$project_id'"; 
	   $sres = mysql_query($SSql);
	   $SNUM = mysql_num_rows($sres);
		
		if($SNUM >0){
			if($division_id == $old_division){ 	 
			$susql="UPDATE ".SUPPLIER_TBL." SET district ='$district_id'  WHERE district ='$old_district' AND area='$area_id' AND project_id='$project_id'";
			}else{
			$susql="UPDATE ".SUPPLIER_TBL." SET division ='$division_id', district ='$district_id' WHERE division ='$old_division' AND district ='$old_district' AND area='$area_id' AND project_id='$project_id'";				
			}
			mysql_query($susql); 
		}
				
		$CSql="SELECT * FROM ".SUB_ACC_HEAD_TBL." WHERE district='$old_district' AND area='$area_id' AND project_id='$project_id' AND head_type='Customer'"; 
		$cres = mysql_query($CSql);
		$CNUM = mysql_num_rows($cres);
		
		if($CNUM >0){				
			if($division_id == $old_division){ 	 
			$cusql="UPDATE ".SUB_ACC_HEAD_TBL." SET district ='$district_id'  WHERE district ='$old_district' AND area ='$area_id' AND project_id='$project_id'";
			}else{
			$susql="UPDATE ".SUB_ACC_HEAD_TBL." SET division ='$division_id', district ='$district_id' WHERE division ='$old_division' AND district ='$old_district' AND area='$area_id' AND project_id='$project_id'";				
			}
			mysql_query($cusql); 
		}
   }
   
} // End class
?>
