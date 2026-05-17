<?php
class SubsidiaryStep3
{
   function run() {     
		$cmd    = getRequest('cmd');
		$u_t_id = getFromSession('u_type_id');
		if($u_t_id == 101) 
		{      
		  switch($cmd) { 
			case 'add'           : $screen = $this->showEditor($msg); break;
			case 'loadsubhtype'  : $this->loadSubHeadType(trim(getRequest('head_type'))); break; 	
			case 'loadSL2' 	     : $this->loadSubsidiaryStep2(trim(getRequest('head_type'))); break;
			case 'edit'          : $screen = $this->showEditor("Edit Page");    break;
			case 'doUpdate'      : $screen = $this->showEditor($msg); break;
			case 'delete'        : $screen = $this->deleteItem(); break;
			default              : $cmd = 'list'; $screen = $this->showEditor($msg);   break;
		  }
		}else if($u_t_id == 107) 
		{      
		  switch($cmd) { 
			case 'add'           : $screen = $this->showEditor($msg); break;
			case 'loadsubhtype'  : $this->loadSubHeadType(trim(getRequest('head_type'))); break;  	
			case 'loadSL2'       : $this->loadSubsidiaryStep2(trim(getRequest('head_type'))); break;
			case 'edit'          : $screen = $this->showEditor("Edit Page");    break;	 
			case 'doUpdate'      : $screen = $this->showEditor($msg); break;
			default              : $cmd = 'list'; $screen = $this->showEditor($msg);   break;
		  }
		}elseif($u_t_id == 102) 
		{      
		  switch($cmd) { 
			case 'add'          : $screen = $this->showEditor($msg); break; 	
			case 'edit'         : $screen = $this->showEditor("Edit Page"); break;
			case 'doUpdate'     : $screen = $this->showEditor($msg); break;
			case 'loadsubhtype' : $this->loadSubHeadType(trim(getRequest('head_type'))); break; 	
			case 'loadSL2'      : $this->loadSubsidiaryStep2(trim(getRequest('head_type'))); break;
			default             : $cmd = 'list'; $screen = $this->showEditor($msg);   break;
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
	 $sl_three_id 	= getRequest('id');	 
	 $data          = array();		
	 if($sl_three_id){
	 $TBDArr	= $comApp->getRecordInfo(SUBSIDIARY_STEP3_TBL,"sl_three_id",$sl_three_id);      
	 $TBDArr 	= parseThisValue($TBDArr);
	 $data        	= array_merge(array(),$TBDArr);		 			
	 if(getRequest('save')){
		$ures = $this->updateRecord($sl_three_id);
		if($ures){
		$msg="Successfully Update Record !!!";
		header("location:?app=subsidiary.step3&cmd=list&msg=$msg");	
		}else{
			$msg="Please try again !!!";
		}			      	
	 } 
	} else {		
	if(getRequest('save')) {
		$sl3_id = $comApp->NewID(SUBSIDIARY_STEP3_TBL,"sl_three_id","S300000","S",7);
		$comApp->saveRecord(SUBSIDIARY_STEP3_TBL,"sl_three_id",$sl3_id,"","","created_by","created_time","subsidiary.step3","list");

		$this->saveAsCostCenter();

		$msg="Successfully Save Record !!!";
		header("location:?app=subsidiary.step3&cmd=list&msg=$msg");     		       		      	
	 }			 
	}
	$comdition="";
	$srckey = getRequest('srckey');
	if($srckey !=""){$comdition = "sl_three_name LIKE '%".$srckey."%'";}else{$comdition="";}

	$GLValue = getRequest('srchead_type');
	if($comdition !="" && $GLValue!=""){$comdition.= "AND head_type ='".$GLValue."'";}
	elseif($comdition =="" && $GLValue!=""){$comdition= " head_type ='".$GLValue."'";}

	$f1Name  = "sub_head";
	$f1Value = getRequest('srcsub_headtype');

	$f2Name  = "child_id";
	$f2Value = getRequest('srcchild_id');
	$from = getRequest('from'); if($from==""){ $from = 0;}
	$to = getRequest('to'); if($to==""){ $to = 45;}
	$data['brand_list']  	= $comApp->getRecords(SUBSIDIARY_STEP3_TBL,"child_id",$comdition,$f1Name,$f1Value,$f2Name,$f2Value,$from,$to);

	$data['totalrecord']  	= $comApp->getTotalRecords(SUBSIDIARY_STEP3_TBL,"sl_three_id",$comdition,$f1Name,$f1Value,$f2Name,$f2Value,$from,$to); 
	$data['catagory_list']	= $comListApp->getCatagoryList();
	$data['project_id'] 	= getFromSession('project_id');
	$data['message'] 	= $msg;
	$data['cmd']     	= getRequest('cmd'); 
	require_once(CURRENT_APP_SKIN_FILE);
	return $data[0];
   } 

   function saveAsCostCenter()
    {
        $make_cost_center = getRequest("make_cost_center");
        if ($make_cost_center == 1) {
            require_once(CLASS_DIR . '/common.class.php');
            $comApp = new Common();
            $sub_id = $comApp->NewID(SUB_ACC_HEAD_TBL, "sub_id", "A000001", "A", 7);
            $costData['sub_id'] = $sub_id;
            $costData['head_type'] = "Cost Center";
            $costData['sub_headtype'] = "S148";
            $costData['child_head'] = "C000164";
            $costData['sl_three_head'] = "S300249";
            $costData["sub_head_name"] = getRequest('sl_three_name');
            $costData["project_id"] = getFromSession('project_id');
            $costData["created_by"] = getFromSession('userid');
            $costData["created_time"] = date('Y-m-d H:i:s');

            $info = [
                'table' => SUB_ACC_HEAD_TBL,
                'data' => $costData
            ];

            insert($info);
        }
    }
   
   function updateRecord($id)
   {       
	$requestdata = array();
	$requestdata = getUserDataSet(SUBSIDIARY_STEP3_TBL); 
	$requestdata['project_id']   	= getFromSession('project_id');
	$requestdata["modified_by"]  	= getFromSession('userid');
	$requestdata["modified_time"]	= date('Y-m-d h:i:s');
	$info        			=  array();
	$info['table']			= SUBSIDIARY_STEP3_TBL; 
	if(getRequest('sub_headtype')==""){
	$requestdata["sub_headtype"] = " ";
	}	
	//dBug($requestdata);
	$info['data'] 		= $requestdata;
	$info['where']		= "sl_three_id ='$id'";  
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
    
   function loadSubsidiaryStep2($GL_Head,$SL_Head)
   {	  
	  $project_id 	   	= getFromSession('project_id');  
	  $info            		= array();
	  $info['table']   	= CHILD_HEAD_TYPE_TBL;
	  $info['fields']  		=  array('child_id','child_head_name');
	  $SQL = "head_type='$GL_Head' AND sub_htid='$SL_Head' AND project_id='$project_id' ";
	  $info['where']   	= $SQL; 
	  $info['groupby'] 	= array("sl_three_id");
	  $info['orderby'] 	= array("sl_three_name ASC");
	  //$info['debug']   = true;	
	  $result          		= select($info);
	  $data            		= array();	
	  if(count($result)){
		 foreach($result as $key=>$value){
			$data[$key][] = $value;
		 }
	  }				
	  foreach($data as $i=>$v){
		 $subject_idname .= $v[0]->sl_three_id.'#####'.$v[0]->sl_three_name.'@@@';
	  }
	  echo $subject_idname;	
	} 
   function deleteItem(){
	require_once(CLASS_DIR.'/common.class.php');	
	$comApp = new Common(); 
	$sl_three_id = getRequest('id');
	$comApp->deleteRecord(SUBSIDIARY_STEP3_TBL,"sl_three_id",$sl_three_id,"subsidiary.step3","list"); 
   }  
} // End class
?>
