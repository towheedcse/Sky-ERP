<?php
class SubCatagory
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
      	     case 'edit'               	: $screen = $this->showEditor("Edit Page");    break;			 
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
	 $comListApp 	 = new CommonList(); 
	 $subcatagory_id = getRequest('id');	 
	 $data               = array();		
	 if($subcatagory_id){
	 $TBDArr			= $comApp->getRecordInfo(SUB_CATAGORY_TBL,"subcatagory_id",$subcatagory_id);      
	 $TBDArr 			= parseThisValue($TBDArr);
	 $data        		= array_merge(array(),$TBDArr);	
	 $data['main_catagory_id'] = $this->mainCategoryCode($data['catagory_id']);	
		
	 if(getRequest('save')){
		$comApp->updateRecord(SUB_CATAGORY_TBL,"subcatagory_id",$subcatagory_id,"","","modefied_by","modefied_date","catagory","list");
		$msg="Successfully Update Record !!!";
		header("location:?app=subcatagory&cmd=list&msg=$msg");	      	
	 } 
	} else {		
	if(getRequest('save')) {
		$accessories_id = $comApp->NewID(SUB_CATAGORY_TBL,"subcatagory_id","S100000","S",7);
		$comApp->saveRecord(SUB_CATAGORY_TBL,"subcatagory_id",$accessories_id,"","","created_by","created_time","subcatagory","list");
		$msg="Successfully Save Record !!!";
		header("location:?app=subcatagory&cmd=list&msg=$msg");     		       		      	
	 }			 
	}

	$f1Value = getRequest('srckey');
	$from 	 = getRequest('from'); 

	$data['srcmaincatagory'] = getRequest('srcmaincatagory');
	$data['srccatagory'] = getRequest('srccatagory');
	$data['srckey'] = getRequest('srckey');


	if($from==""){ $from = 0;}
	$to 	 = getRequest('to'); if($to==""){ $to = 45;}

	$result = $this->getSubCatagoryList($from,$to);
	$data['record_list']  	= $result["data"];
	$data['totalrecord']   = $result["total"];
	//$data['totalrecord']  	= $this->getTotalSubCatagoryList(); 
	//$data['catagory_list']  = $this->getMainSubCatagoryList();
	$data['main_catagory_list'] = $comListApp->getMainCatagoryList();
	$data['project_id'] 	= getFromSession('project_id');
	$data['message'] 		= $msg;
	$data['cmd']     		= getRequest('cmd'); 
	require_once(CURRENT_APP_SKIN_FILE);
	return $data[0];
   } 
   

   function getSubCatagoryList($from, $to)
{
    if ($from == "" && $to == "") {
        $from = 0;
        $to = 45;
    }

    $srccatagory = trim(getRequest('srccatagory'));
    $srckey      = trim(getRequest('srckey'));
    $project_id  = getFromSession('project_id');

    if ($srccatagory != "" || $srckey != "") {
        $from = 0;
    }

    // ---------------- MAIN QUERY ----------------
    $info = array();
    $info['table'] = SUB_CATAGORY_TBL . " s 
        LEFT JOIN " . CATAGORY_TBL . " c 
        ON s.catagory_id = c.catagory_code";   // <-- FIXED JOIN

    $info['fields'] = array(
        's.subcatagory_id',
        's.catagory_id',
        's.project_id',
        's.subcatagory_name',
        'c.catagory_name',
        's.created_by',
        's.created_time',
        's.modefied_by',
        's.modefied_date'
    );

    $sql = "s.project_id = '$project_id'";

    if ($srccatagory != "") {
        $sql .= " AND s.catagory_id = '$srccatagory'";
    }

    if ($srckey != "") {
        $sql .= " AND s.subcatagory_name LIKE '%$srckey%'";
    }

    $info['where'] = $sql;

    // ---------------- COUNT QUERY ----------------
    $countInfo = array();
    $countInfo['table'] = SUB_CATAGORY_TBL . " s 
        LEFT JOIN " . CATAGORY_TBL . " c 
        ON s.catagory_id = c.catagory_code";

    $countInfo['fields'] = array("COUNT(DISTINCT s.subcatagory_id) AS total");
    $countInfo['where']  = $sql;

    $countResult = select($countInfo);
    $total = isset($countResult[0]->total) ? $countResult[0]->total : 0;

    // ---------------- ORDER + LIMIT ----------------
    $info['orderby'] = array("s.subcatagory_id DESC LIMIT $from,$to");

    $result = select($info);

    $data = array();
    if (count($result)) {
        foreach ($result as $value) {
            $data[] = $value;
        }
    }

    return ["data" => $data, "total" => $total];
}


   
   function getTotalSubCatagoryList() { 
		$srccatagory 	= getRequest('srccatagory');
		$srckey 		= getRequest('srckey');				
		$project_id     = getFromSession('project_id');  
		$info           = array();    
		$info['table']  = SUB_CATAGORY_TBL.' s,'.CATAGORY_TBL.' c';	
		$info['fields'] = array('s.subcatagory_id','s.catagory_id','s.project_id','s.subcatagory_name','c.catagory_name','s.created_by','s.created_time','s.modefied_by','s.modefied_date');		
		$sql="s.catagory_id=c.catagory_code AND s.project_id = '".$project_id."'";
		if($srccatagory !=""){
			$sql.=" AND s.catagory_id = '$srccatagory'";
		}
		if($srckey !=""){
			$sql.=" AND s.subcatagory_name LIKE '%$srckey%'";
		}
		$info['where']  = $sql;
		$info['orderby']= array("s.subcatagory_id DESC LIMIT $from,$to");
		//$info['debug']= true;
		$result         = select($info);
	    $cnt = count($result);  	
        if($cnt) {
        	return $cnt;
        }else {
	    	return 0;
	    }    
   }
     
   function deleteItem(){
	require_once(CLASS_DIR.'/common.class.php');	
	$comApp = new Common(); 
	if(!userCondition(true)){
		$msg = "You are not authorized !!!";
	      	header("location:index.php?app=subcatagory&msg=$msg");
	      	exit;
	    }
	$subcatagory_id = getRequest('id');
	$comApp->deleteRecord(SUB_CATAGORY_TBL,"subcatagory_id",$subcatagory_id,"subcatagory","list"); 
   }  


    function getMainSubCatagoryList(){
	$project_id = getFromSession('project_id'); 
        $main_catagory_id = getRequest('srccatagory');

	if(empty($main_catagory_id)){
	   return [];
	}
        $data = array();
        $info = array();
        $info['table'] = CATAGORY_TBL;
        $info['where'] = "project_id = '$project_id' AND main_catagory_id='$main_catagory_id'";
        $result = select($info);
        //dBug($result);
        //$info['debug']  	= true;
        $data = array();
        if (count($result)) {
            foreach ($result as $i => $v) {
                $data[$i] = $v;
            }
        }

        return $data;
    }

    function mainCategoryCode($catagory_code){
	$project_id = getFromSession('project_id'); 
	$rsql= "SELECT main_catagory_id FROM ".CATAGORY_TBL." WHERE catagory_code='$catagory_code' AND project_id='".$project_id."'";  
	$res = mysql_fetch_object(mysql_query($rsql));

	$data = "";
	if(isset($res->main_catagory_id)){
	     $data = $res->main_catagory_id;
	}
	return $data;
    }

} // End class
?>
