<?php
class Customer
{
   function run() {     
		$cmd    = getRequest('cmd');
		$u_t_id = getFromSession('u_type_id');
		if($u_t_id == 101) 
		{      
		  switch($cmd) { 
		  	case 'add'                	: $screen = $this->showEditor($msg); break;
      	    		case 'edit'               	: $screen = $this->showEditor("Edit Page");    break;
      	    		case 'loadDistrict'  		: $this->loadDistrict(trim(getRequest('division_id'))); break;
      	    		case 'loadArea'  			: $this->loadArea(trim(getRequest('district'))); break; 					 
      	   		case 'doUpdate'           	: $screen = $this->showEditor($msg); break;
		    	case 'delete'             	: $screen = $this->deleteItem(); break;
			default                   	: $cmd = 'list'; $screen = $this->showEditor($msg);   break;
		  }
		}else if($u_t_id == 107) 
		{      
		  switch($cmd) { 
		  case 'add'                	: $screen = $this->showEditor($msg); break;
      	          case 'edit'               	: $screen = $this->showEditor("Edit Page");    break;
      	          case 'loadDistrict'  		: $this->loadDistrict(trim(getRequest('division_id'))); break;
      	          case 'loadArea'  		: $this->loadArea(trim(getRequest('district'))); break; 					 
      	   	  case 'doUpdate'           	: $screen = $this->showEditor($msg); break;
		  default                   	: $cmd = 'list'; $screen = $this->showEditor($msg);   break;
		  }
		}elseif($u_t_id == 102) 
		{      
		  switch($cmd) { 
		        case 'add'              : $this->showEditor($msg); break;      	     
      	     		case 'loadDistrict'  	: $this->loadDistrict(trim(getRequest('division_id'))); break;
      	     		case 'loadArea'  	: $this->loadArea(trim(getRequest('district'))); break; 					 
      	   	        default                	: $cmd = 'list'; $screen = $this->showEditor($msg);   break;
		  }
		}elseif($u_t_id == 109) 
		{      
		  switch($cmd) { 
		        case 'loadDistrict'  	: $this->loadDistrict(trim(getRequest('division_id'))); break;
      	     		case 'loadArea'  	: $this->loadArea(trim(getRequest('district'))); break;
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
	 $data        			= array_merge(array(),$TBDArr);	
	 $district_id 			= $data['district'];	
	 $DVArr				= $comApp->getRecordInfo(DISTRICT_TBL,"district_id",$district_id);      
	 $DVArr 			= parseThisValue($DVArr);
	 $data['division'] 		= $DVArr['division_id'];
	 if(getRequest('save')){
		if(getFromSession('u_type_id') ==101 || getFromSession('u_type_id') ==107){ 
			$comApp->updateRecord(SUB_ACC_HEAD_TBL,"sub_id",$sub_id,"","","modified_by","modified_time","customer","");
			$this->updateCelling();
			$msg="Successfully Update Record !!!";
		}
		header("location:?app=customer&cmd=list&msg=$msg");	      	
	 } 
	} else {		
	if(getRequest('save')) {
		$Acc_id = $comApp->NewID(SUB_ACC_HEAD_TBL,"sub_id","A000001","A",7);
		$comApp->saveRecord(SUB_ACC_HEAD_TBL,"sub_id",$Acc_id,"","","created_by","created_date","customer","");
		$recipients = "<gsm>".getRequest('mobile')."</gsm>";
		$recipients.= "<gsm>".getRequest('att_mobile1')."</gsm>";

		$Csql = "SELECT sub_head_name,code FROM " . SUB_ACC_HEAD_TBL . " WHERE sub_id ='$Acc_id'";
                $Crow = mysql_fetch_object(mysql_query($Csql));
                $userCode = $Crow->code;
                $subHeadName = $Crow->sub_head_name;

		$sms_text = "Dear Client, Your customer account has created.";
                
                if(isset($userCode) && !empty($userCode)){
                    $sms_text = "Dear Client, Your new customer id is " . $userCode . " has created.";
                }
		if (isset($subHeadName) && !empty($subHeadName)) {
                    $sms_text .= " Party name is ".$subHeadName.". (" . COMPANY_NAME . ")";
                }

	 	//$this->sendSMS(COMPANY_NAME,$recipients,$sms_text);
		require_once(CLASS_DIR . '/common.list.class.php');
		$numbers = [getRequest('mobile'), getRequest('att_mobile1')];
                foreach ($numbers as $recipients) {
                    if ($recipients != "") {
                        $response = (new CommonList())->sendSMS($recipients, $sms_text);
                    }
                }
		$msg="Successfully Save Record !!!";
		header("location:?app=customer&cmd=list&msg=$msg");     		       		      	
	 }			 
	}
		
	$from =getRequest('from'); if($from==""){ $from=0;} $to =getRequest('to'); if($to==""){ $to=100;}
	$data['customer_list']  = $this->getRecords($from,$to);
	$data['totalrecord']  	= $this->getTotalRecords(); 
	$data['district_list']	= $comListApp->getDistrictList();
	$data['area_list'] 	= $comListApp->getAreaList();
	$data['message'] 	= $msg;
	$data['cmd']     	= getRequest('cmd'); 
	/*======== Start SMS =========*/
	/*
	$project_id = getFromSession('project_id'); 
	$Csql = "SELECT att_mobile1,sub_head_name FROM ".SUB_ACC_HEAD_TBL." WHERE status=1 AND project_id = '$project_id' AND att_mobile1!=''"; 
	$cres = mysql_query($Csql); $msl=0;
	while($Crow = mysql_fetch_object($cres)){		 
	 $recipients	= trim($Crow->att_mobile1);
	 if($recipients !=""){
	 $message = "সম্মানিত ডিলারবৃন্দ, বর্তমান পরিস্থিতিতে সচেতন থাকুন, স্বাস্থ্যবিধি মেনে চলুন। মহান সৃষ্টিকর্তা আপনার ও আপনার পরিবারের সকলকে সুস্থ্য ও নিরাপদ রাখুন, আমীন। (লিরা গ্রুপ) ";
 	 $this->sendSMS(COMPANY_NAME,$recipients,$message); $msl++;
	 }
	}
        echo "Successfully send ($msl) SMS";  
	*/
	/*======== End SMS =========*/
	require_once(CURRENT_APP_SKIN_FILE);
	return $data[0];
   }     
    
   function updateCelling(){
	$sub_id 	 = getRequest('id');
	$ceilling_amount = getRequest('ceilling_amount');
	$yc_amount 	 = getRequest('yc_amount');
	$stc_amount 	 = getRequest('stc_amount');
	
	$sqlc="UPDATE ".SUB_ACC_HEAD_TBL." SET ceilling_amount='".$ceilling_amount."',yc_amount='".$yc_amount."',stc_amount='".$stc_amount."' WHERE sub_id='".$sub_id."'";
	mysql_query($sqlc);
   }
   
  
   function sendSMS($sender,$recipients,$message){	
	$token = SMS_TOKEN;
	$url = "https://24smsbd.com/api/bulkSmsApi";
	$data= array(
	'sender_id'=>"1903",
	'apiKey'=>"$token",
	'mobileNo'=>"$recipients",
	'message'=>"$message"
	); // Add parameters in key value
	$curl = curl_init($url);
	curl_setopt($curl, CURLOPT_POST, true);
	curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
	curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);
	curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
	curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
	$output = curl_exec($curl);
	curl_close($curl);	
	//echo $output;
   }
   function loadDistrict($division_id){
	  $project_id 	   = getFromSession('project_id');  
	  $info            = array();
	  $info['table']   = DISTRICT_TBL;
	  $info['fields']  =  array('district_id','district_name');
	  $SQL = "division_id='$division_id' AND project_id='$project_id' ";
	  $info['where']   = $SQL; 
	  $info['groupby'] = array("district_id");
	  $info['orderby'] = array("district_name ASC");
	  //$info['debug']   = true;	
	  $result          = select($info);
	  $data            = array();	
	  if(count($result)){
		 foreach($result as $key=>$value){
			$data[$key][]        = $value;
		 }
	  }				
	  foreach($data as $i=>$v){
		 $subject_idname .= $v[0]->district_id.'#####'.$v[0]->district_name.'@@@';
	  }
	  echo $subject_idname;
   }
   function loadArea($district)
   {	  
	  $project_id 	   = getFromSession('project_id');  
	  $info            = array();
	  $info['table']   = AREA_TBL;
	  $info['fields']  =  array('area_id','area_name');
	  $SQL = "district='$district' AND project_id='$project_id' ";
	  $info['where']   = $SQL; 
	  $info['groupby'] = array("area_id");
	  $info['orderby'] = array("district,area_name ASC");
	  //$info['debug']   = true;	
	  $result          = select($info);
	  $data            = array();	
	  if(count($result)){
		 foreach($result as $key=>$value){
			$data[$key][]        = $value;
		 }
	  }				
	  foreach($data as $i=>$v){
		 $subject_idname .= $v[0]->area_id.'#####'.$v[0]->area_name.'@@@';
	  }
	  echo $subject_idname;	
	}   
   function deleteItem(){
	require_once(CLASS_DIR.'/common.class.php');	
	$comApp = new Common(); 
	$sub_id = getRequest('id');
	$comApp->deleteRecord(SUB_ACC_HEAD_TBL,"sub_id",$sub_id,"customer","list"); 
	$comApp->deleteRecord(SUPPLIER_TBL,"supplier_code",$sub_id,"customer","list"); 
   }
   
   function getRecords($from,$to) { 
       if($from == "" && $to == ""){$from=0; $to=100;} 
   		$divisionid 	= getRequest('divisionid');
		$areaid 		= getRequest('areaid');
		$trtid 			= getRequest('trt');		
		$srckey 		= getRequest('srckey');
				
		$project_id     = getFromSession('project_id');
		$info           = array();    
		$info['table'] = SUB_ACC_HEAD_TBL . " c
		    LEFT JOIN " . DISTRICT_TBL . " a ON c.district = a.district_id
		    LEFT JOIN " . AREA_TBL . " t ON c.area = t.area_id";

		$info['fields'] = array(
		    'c.sub_id','c.sub_head_name','c.head_details','c.customer_type',
		    'a.division_id','a.district_id','c.area','c.head_type',
		    'c.phone','c.ceilling_amount','c.yc_amount','c.stc_amount',
		    'c.mobile','c.email',
		    'c.att_name1','c.att_designation1','c.att_email1','c.att_mobile1',
		    'c.att_name2','c.att_designation2','c.att_email2','c.att_mobile2',
		    'c.created_time','c.status','c.code','c.p_old_price','c.overdue_invoice','c.credit_days'
		);

		$sql = "c.head_type = 'Current Assets' 
			AND c.sub_headtype = 'S128'
			AND c.project_id = '".$project_id."'";

		if($divisionid != ""){
		    $sql .= " AND a.division_id = '$divisionid'";
		}
		if($areaid != ""){
		    $sql .= " AND c.district = '$areaid'";
		}
		if($trtid != ""){
		    $sql .= " AND c.area = '$trtid'";
		}
		if($srckey != ""){
		    $sql .= " AND (
			c.sub_head_name LIKE '%".$srckey."%' 
			OR c.code LIKE '%".$srckey."%' 
			OR c.sub_id LIKE '%".$srckey."%'
		    )";
		}

		$info['where']   = $sql;
		$info['groupby'] = array("c.sub_id");
		$info['orderby'] = array("c.sub_head_name ASC LIMIT $from,$to");
		//$info['debug']  = true;
		$result         = select($info);
		$data           = array();
		$cnt = count($result);  	     
		
		if($cnt) {
			foreach($result as $value)  {				
			$data[]	= $value;	
			}
		} 
		
		return $data; 
   }
  function  getTotalRecords(){
   		$divisionid 	= getRequest('divisionid');
		$areaid 		= getRequest('areaid');
		$trtid 			= getRequest('trt');					
		$srckey 		= getRequest('srckey');
				
		$project_id     = getFromSession('project_id');
		$info           = array();    
		$info['table'] = SUB_ACC_HEAD_TBL . " c
		    LEFT JOIN " . DISTRICT_TBL . " a ON c.district = a.district_id
		    LEFT JOIN " . AREA_TBL . " t ON c.area = t.area_id";

		$info['fields'] = array(
		    'c.sub_id','c.sub_head_name','c.head_details','c.customer_type',
		    'a.division_id','a.district_id','c.area','c.head_type',
		    'c.phone','c.ceilling_amount','c.yc_amount','c.stc_amount',
		    'c.mobile','c.email',
		    'c.att_name1','c.att_designation1','c.att_email1','c.att_mobile1',
		    'c.att_name2','c.att_designation2','c.att_email2','c.att_mobile2',
		    'c.created_time','c.status','c.code','c.p_old_price','c.overdue_invoice','c.credit_days'
		);

		$sql = "c.head_type = 'Current Assets' 
			AND c.sub_headtype = 'S128'
			AND c.project_id = '".$project_id."'";

		if($divisionid != ""){
		    $sql .= " AND a.division_id = '$divisionid'";
		}
		if($areaid != ""){
		    $sql .= " AND c.district = '$areaid'";
		}
		if($trtid != ""){
		    $sql .= " AND c.area = '$trtid'";
		}
		if($srckey != ""){
		    $sql .= " AND (
			c.sub_head_name LIKE '%".$srckey."%' 
			OR c.code LIKE '%".$srckey."%' 
			OR c.sub_id LIKE '%".$srckey."%'
		    )";
		}

		$info['where']   = $sql;
		$info['groupby'] = array("c.sub_id");
		//$info['debug']  = true;
		$result         = select($info);
		$data           = array();
		$cnt = count($result);  
		if($cnt) {
			return $cnt;
		}else {
		  return 0;
		}   
  }
    
} // End class
?>
