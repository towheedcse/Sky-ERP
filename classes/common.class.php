<?php
class Common
{
   function run()
   {     
		$cmd = getRequest('cmd');
		$u_t_id = getFromSession('u_type_id');
		if($u_t_id == 101 || $u_t_id == 102 || $u_t_id == 103 || $u_t_id == 104 || $u_t_id == 105|| $u_t_id == 106 || $u_t_id == 107 || $u_t_id == 108) 
		{      
		  switch($cmd)
		  { 
		  	 case 'add'                	: $screen = $this->showEditor($msg); break;
      	     case 'edit'               	: $screen = $this->showEditor("Edit Page");    break;			 
      	   	 case 'doUpdate'           	: $this->updateStyle(); break;
		     case 'delete'             	: $screen = $this->deleteStyle(getRequest('id')); break;
			 default                   	: $cmd = 'list'; $screen = $this->showEditor($msg);   break;
		  }
		}else {
      		header("location:index.php?app=user_home&msg=You are not authorised !!!");
      	} 
		
		return true;
   }
  	//============== Common Function =================
   function updateRecord($TBL,$idName,$idValue,$dateFName=NULL,$photoFName=NULL,$modifiedbyFName=NULL,$modifiedDateFName=NULL,$redirect,$cmd,$sl_three_head = NULL, $extraData = array())
   {       
	  $requestdata = array();
          $requestdata = getUserDataSet($TBL); 
	  if($dateFName!=""){
		  $dateFNameArr					= explode(",",$dateFName); $f=0;
		  $totalDateF = count($dateFNameArr);
		  while($f<=$totalDateF){
		  $requestdata["$dateFNameArr[$f]"]= formatDate(getRequest("$dateFNameArr[$f]")); $f++;
		  }
	  }
	  $requestdata["$modifiedbyFName"]  = getFromSession('userid');
	  $requestdata["$modifiedDateFName"]= date('Y-m-d h:i:s');
	  if(!empty($sl_three_head)){
		$requestdata["sl_three_head"] = $sl_three_head;
	}elseif (!isset($requestdata["sl_three_head"]) || $requestdata["sl_three_head"] == "") {
            $requestdata["sl_three_head"] = "S300158";
          }

	  if (!empty($extraData)) {
		foreach ($extraData as $key => $value) {
		    $requestdata[$key] = $value;
		}
          }


	if ($TBL == SUB_ACC_HEAD_TBL) {
		$group_ledger = $this->getGroupHead($requestdata["head_type"]);
	        if($group_ledger){
 			$requestdata["group_ledger"] = $group_ledger;
		}

		$p_old_price = getRequest('p_old_price');
		if (!empty($p_old_price)) {
			$requestdata["p_old_price"] = 1;
		} else {
			$requestdata["p_old_price"] = 0;
		}

		$overdue_invoice = getRequest('overdue_invoice');
		if (!empty($overdue_invoice)) {
			$requestdata["overdue_invoice"] = 1;
		} else {
			$requestdata["overdue_invoice"] = 0;
		}
	}

	if($TBL==SUB_HEAD_TYPE_TBL){ 
		$gl_id = $this->getGroupHead($requestdata["head_type"]);
	        if($gl_id){
 			$requestdata["gl_id"] = $gl_id;
		}
	}


   	  $info        						=  array();
          $info['table']					= $TBL; 
          //dBug($requestdata);
	  $info['data'] 					= $requestdata;
	  $info['where']					= "$idName ='$idValue'";  
	  $info['debug']  					=  true;    
	  $res = update($info);
          if(!$res) {
	    	$msg="Please try again !!!";
		if($cmd!=""){
        	header("location:?app=$redirect&cmd=$cmd&msg=$msg");
		}        
      	  }else{
		$msg="Successfully Update Record !!!";
		if($cmd!=""){
        	header("location:?app=$redirect&cmd=$cmd&msg=$msg");
		}
	  }               
   }//EOFn 

   function saveRecord($TBL,$idName,$idValue=NULL,$dateFName=NULL,$photoFName=NULL,$createdbyFName,$createdDateFName,$redirect,$cmd, $sl_three_head = NULL, $extraData = array())
   {       
	  $requestdata 						= array();
      	  $requestdata 						= getUserDataSet($TBL); 	  
	  if($dateFName!=""){
		  $dateFNameArr					= explode(",",$dateFName); $f=0;
		  $totalDateF = count($dateFNameArr);
		  while($f<=$totalDateF){
		  $requestdata["$dateFNameArr[$f]"]= formatDate(getRequest("$dateFNameArr[$f]")); $f++;
		  }
	  }
	  $requestdata["$createdbyFName"]   = getFromSession('userid');
	  $requestdata["$createdDateFName"] = date('Y-m-d h:i:s');
	  if($idValue!= ""){
		$requestdata["$idName"]   		= $idValue;
	  }elseif($idValue==-1){
		$msg = "ID overflow !!!";
		header("location:index.php?app=user_home&msg=$msg");
		exit;
	  }

	  if (!empty($extraData)) {
		foreach ($extraData as $key => $value) {
		    $requestdata[$key] = $value;
		}
          }
		  
	  if($TBL==SUB_ACC_HEAD_TBL){ 
		$group_ledger = $this->getGroupHead($requestdata["head_type"]);
	        if($group_ledger){
 			$requestdata["group_ledger"] = $group_ledger;
		}	  
		 
		$p_old_price = getRequest('p_old_price');
		if(!empty($p_old_price)){
			$requestdata["p_old_price"] = 1;
		}else{
			$requestdata["p_old_price"] = 0;
		}
		$overdue_invoice = getRequest('overdue_invoice');
		if(!empty($overdue_invoice)){
			$requestdata["overdue_invoice"] = 1;
		}else{
			$requestdata["overdue_invoice"] = 0;
		}
	  }

	if($TBL==SUB_HEAD_TYPE_TBL){ 
		$gl_id = $this->getGroupHead($requestdata["head_type"]);
	        if($gl_id){
 			$requestdata["gl_id"] = $gl_id;
		}
	}

	if(!empty($sl_three_head)){
		$requestdata["sl_three_head"] = $sl_three_head;
	}elseif (!isset($requestdata["sl_three_head"]) || $requestdata["sl_three_head"] == "") {
            $requestdata["sl_three_head"] = "S300158";
          }

   	  $info        						=  array();
          $info['table']					= $TBL; 
	  $info['data'] 					= $requestdata;  
	  $info['debug']  					=  true;  
	  $res = insert($info); 
	  if(!$res){
	         $msg="Please try again !!!";
		if($cmd!=""){
	         header("location:?app=$redirect&cmd=$cmd");
		}
	  }else{
	        $msg="Successfully Update Record !!!";
		if($cmd!=""){
		header("location:?app=$redirect&cmd=$cmd&msg=$msg");
		}
	  }        
   }//EOFn 


   function getGroupHead($head_type = null){
        $glHead = null;
	if($head_type=="Current Assets" || $head_type=="Non Current Assets"){
		$glHead = "ASSETS";
	}elseif($head_type=="Current Liabilities" || $head_type=="Non Current Liabilities"){
		$glHead = "LIABILITIES";
	}elseif($head_type=="Capital" || $head_type=="Retained earnings" || $head_type=="Retained Earnings"){
		$glHead = "EQUITY";
	}elseif($head_type=="Operating Revenue" || $head_type=="Non-Operating Revenue"){
		$glHead = "REVENUE";
	}elseif($head_type=="Direct Expenses" || $head_type=="Indirect Expenses"){
		$glHead = "EXPENSES";
	}
	
	return $glHead;
   }


   function getRecords($TBL,$idName,$comdition=NULL,$fName1=NULL,$fValue1=NULL,$fName2=NULL,$fValue2=NULL,$from=NULL,$to=NULL){ 
	   if($from == "" && $to == ""){$from=0; $to=50;}  
  	   $data            = array();	  
  	   $info            = array();
	   $info['table']   = $TBL; 
	   if($fName1!="" && $fValue1!=""){    
            $where= " $fName1 like '%".$fValue1."%'";
	   }
	   if($fName2!="" && $fValue2!=""){
	   	 if($fName1!="" && $fValue1!=""){     
                 $where.= " AND $fName2 like '%".$fValue2."%'";
		 }elseif($fName1=="" && $fValue1==""){ 
		 $where.= " $fName2 like '%".$fValue2."%'";
		 }
	   }
	   if($where!="" && $comdition!=""){ $where.=" AND ".$comdition;}elseif($where=="" && $comdition!=""){ $where=$comdition;}
	   $info['where']  = $where;
	   $info['orderby']= array("$idName LIMIT $from,$to");
	   //$info['debug']  = true;			 
	   $res            = select($info);   
	   $data           = array();
	   $cnt = count($res);  	     
	   if($cnt) {
	    foreach($res as $value)  {				
	     $data[]	= $value;	
	    }
	  }
	  return $data;
  }

  function getTotalRecords($TBL,$idName,$comdition=NULL,$fName1=NULL,$fValue1=NULL,$fName2=NULL,$fValue2=NULL)
  {
  	   $data            = array();	  
  	   $info            = array();
	   $info['table']   = $TBL; 
	   if($fName1!="" && $fValue1!=""){    
           $where= " $fName1 like '%".$fValue1."%'";
	   }
	   if($fName2!="" && $fValue2!=""){
	   	 if($fName1!="" && $fValue1!=""){     
         	 $where.= " AND $fName2 like '%".$fValue2."%'";
		 }elseif($fName1=="" && $fValue1==""){ 
		 $where.= " $fName2 like '%".$fValue2."%'";
		 }
	   }
	   if($where!="" && $comdition!=""){ $where.=" AND ".$comdition;}elseif($where=="" && $comdition!=""){ $where=$comdition;}
	   $info['where']  = $where;
	   $info['orderby'] = array("$idName");
	   //$info['debug']   = true;	
	   $res            =	select($info);

	   if(count($res)){
		  $total_job = count($res);
	   }                 
      return $total_job;
  }
  function deleteRecord($TBL,$idName,$idValue,$redirect,$cmd){
   	if($idValue!=""){ 
      	$info = array();
      	$info['table'] = $TBL;
      	$info['where'] = "$idName='$idValue'";
      	$info['debug'] = true;
      	$res = delete($info);   

	$pageRedirect = "";
            $page = getRequest('page');
            if ($page != '') {
                $pageRedirect = "&page=$page";
            }
   	
      	if($res){
      	  $msg="Successfully delete Record !!!";
          header("location:?app=$redirect&cmd=$cmd&msg=$msg". $pageRedirect);     	   
      	}else{
      	  header("location:?app=$redirect&cmd=$cmd&deleted=no". $pageRedirect);
      	}      	
      }
   }
   function getRecordInfo($TBL,$idName,$idValue,$fName=NULL,$fValue=NULL)
   {
	$data           =  array();                  
	$info           =  array();     
	$info['table']  =  $TBL;
	if($fName==""){
	$info['where']  =  "$idName='".$idValue."'";
	}else{
	$info['where']  =  "$idName='".$idValue."' AND $fName='".$fValue."'";
	}
	//$info['debug']  =  true;                     
	$res            =	select($info);
	if(count($res)){
	foreach($res as $i=>$v){
	$data[$i] = $v;             
	}
	}
	return $data[0];
   }
   function NewID($TBL,$idName,$default,$prefix,$length){
      $info = array();
      $info['table'] = $TBL;
      $info['fields'] = array("max($idName) as maxstyle");
      $info['where']  = "$idName LIKE '%$prefix%'";
      //$info['debug']  = true;   
      $res = select($info);
      $maxstyleId = $default;
      if(count($res)){
         foreach($res as $v){
         	 if($v->maxstyle){
             $maxstyleId = $v->maxstyle;
             }
             break;   	
         }
      }
      $maxstyleId = generateID("$prefix",$maxstyleId,$length);
      return $maxstyleId;
   } 
   //======= End Common Function =========
   
} // End class
?>
