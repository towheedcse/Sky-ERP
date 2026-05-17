<?php
class UserApp
{
   
   function run()
   {         
      $cmd = getRequest('cmd');
	  $u_t_id = getFromSession('u_type_id');
      if($u_t_id == 101 || $u_t_id == 102)  // 101 super, 102 admin
      {
      	switch ($cmd){
      	   case 'add'                	: $screen = $this->showEditor($msg); break;
      	   case 'edit'               	: $screen = $this->showEditor("Edit Page");    break;	
      	   case 'doUpdate'           	: $this->updateEmployee(); break;
	   case 'delete'             	: $screen = $this->deleteUser(); break;
	   case 'checkUser'		: $this->checkUserID(getRequest('userid')); break;
	   case 'unblock'		: $this->unBlockUser(getRequest('id')); break;
	   case 'block'			: $this->blockUser(getRequest('id')); break;
      	   case 'list'               	: $screen = $this->showList($msg);   break;
      	   default                   	:$cmd = 'list'; $screen = $this->showList($msg);   break;
      	}
      }elseif($u_t_id == 102){
      	switch ($cmd){
      	   case 'list'               	: $screen = $this->showList($msg);   break;
      	   default                   	:$cmd = 'list'; $screen = $this->showList($msg);   break;
      	}
      }else{
      	header("location:index.php?app=user_home&msg=You are not authorised !!!");
      }     	          

      if($cmd == 'list') {

         if($deleted = getRequest('deleted')) {
            if($deleted == 'yes') {
               $screen['message'] = "Item Deleted Successfully";
            } else {
            	  $screen['message'] = "Item Deletion Failure";	
            }
        }
       require_once(CURRENT_APP_SKIN_FILE);
      } 
      return true;
   }   

   function showList($msg = null) {  
      
	  $data                	= array();
	  $data['cmd']         	= getRequest('cmd');
	  $data['record_list'] 	= $this->getUserList(getRequest('from'),getRequest('to'));    
	  $data['totalrecord']	= $this->getTotalUserList();	
	  
	   if(getRequest('deleted')=='yes') {
		  $data['message'] = "Item Deleted Successfully";
	   }elseif(getRequest('deleted')=='no') {
		  $data['message'] = "Item Not Deleted";
	   }
	   require_once(CURRENT_APP_SKIN_FILE); 
	   return $data[0];
   }
   
   function getEmpList()
   {	
      $data 			= array(); 
      $info        		=  array();
      $info['table']	= EMPLOYEE_TBL;
	  $info['fields'] = array('employee_id','name');
      $res            	=	 select($info);      

      if(count($res))
      {
         foreach($res as $i=>$v)
         {
            $data[$i] = $v;             
         }
      }
	  //dumpVar($data);
      return $data;	
   }   
   
   function getUserTypeList()
   {	
      $data 			= array(); 
      $info        		=  array();
      $info['table']	= USERTYPE_TBL;
	  $info['fields'] 	= array('u_type_id','u_type_name');
	  $info['where']	= "u_type_id NOT IN(101)";
      $res            	=	 select($info);      

      if(count($res))
      {
         foreach($res as $i=>$v)
         {
            $data[$i] = $v;    
         }
      }
	  //dumpVar($data);
      return $data;	

   }   
   function getUserList($from,$to) {  

		if($from == "" && $to == ""){$from=0; $to=50;}
		$srckey 	= getRequest('srckey');
		
		$info           = array();    
		//$info['table']  =  USER_TBL.' u,'.USERTYPE_TBL.' ut,'.EMPLOYEE_TBL.' e,'.PROJECT_TBL.' p';	 
		//$info['fields'] = array('u.userid','u.user_sl','e.emp_code','e.name','e.f_name','e.mobile','e.designation','e.department','e.photo_path','ut.u_type_name','p.project_name','p.project_type','u.status');
		//$sql="ut.u_type_id = u.u_type_id AND u.employee_id = e.emp_code AND e.project_id = p.project_id AND u.userid !='admin'";

		$info['table'] = USER_TBL.' u 
		    LEFT JOIN '.USERTYPE_TBL.' ut ON ut.u_type_id = u.u_type_id
		    LEFT JOIN '.EMPLOYEE_TBL.' e ON u.employee_id = e.emp_code
		    LEFT JOIN '.PROJECT_TBL.' p ON e.project_id = p.project_id';

		$info['fields']= array('u.userid','u.user_sl','e.emp_code','e.name','e.f_name','e.mobile','e.designation','e.department','e.photo_path','ut.u_type_name','p.project_name','p.project_type','u.status');

		$sql = "u.userid != 'admin'";
		
		if($srckey!=""){
			$sql.=" AND (e.name LIKE '%$srckey%' OR ut.u_type_name LIKE '%$srckey%' OR p.project_name LIKE '%$srckey%')";
		}
			
		$info['where']  =$sql;	
	  	$info['orderby'] = array("e.name asc LIMIT $from,$to");
		$info['debug']  = false;
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
   
   function getTotalUserList() {  
		$srckey 	= getRequest('srckey');
		
		$info           = array();    
		$info['table'] = USER_TBL.' u 
		    LEFT JOIN '.USERTYPE_TBL.' ut ON ut.u_type_id = u.u_type_id
		    LEFT JOIN '.EMPLOYEE_TBL.' e ON u.employee_id = e.emp_code
		    LEFT JOIN '.PROJECT_TBL.' p ON e.project_id = p.project_id';

		$info['fields']= array('u.userid','u.user_sl','e.emp_code','e.name','e.f_name','e.mobile','e.designation','e.department','e.photo_path','ut.u_type_name','p.project_name','p.project_type','u.status');

		$sql = "u.userid != 'admin'";
		
		if($srckey!=""){
			$sql.=" AND (e.name LIKE '%$srckey%' OR ut.u_type_name LIKE '%$srckey%' OR p.project_name LIKE '%$srckey%')";
		}
			
		$info['where']  =$sql;	
	  	$info['orderby'] = array("e.name asc");
		$info['debug']  = false;
		$result         = select($info);
		$data           = array();  
		$cnt = count($result);   

		if($cnt) {
		  return $cnt;
		}else {
		  return 0;
		}      
      
   }

   function showEditor($msg = null) { 
     require_once(CLASS_DIR.'/employee.class.php');	
	   $empApp = new Employee();   

        require_once(CLASS_DIR . '/common.list.class.php');
        $comListApp = new CommonList();

      $ID = getRequest('id');

	  if ($ID) {
         $advArr = $this->getUserInfo($ID);
         $advArr = parseThisValue($advArr);  
         $data   = array_merge(array(), $advArr); 
      }
      else
      {	 
         if(getRequest('submit'))
         {
            $this->addUser();	
         }
      }	   
	  $data['project_list']   	 		= $empApp->getProjectList();
	  $data['employee_list']   	 		= $this->getEmpList();
	  $data['u_type_list']   	 	    = $this->getUserTypeList();
	 
      $data['message'] = $msg;
      $data['cmd']     = getRequest('cmd');

      $data['store_list'] = $comListApp->getDeliveryPointList();

	  require_once(USER_ADD_EDIT_SKIN);      
      return true;
   }
        
   function addUser($msg = null)
   {    	  
   	  $requestdata = array();
      $requestdata = getUserDataSet(USER_TBL);	
      //dumpvar($requestdata);
	  $requestdata['password']        	= md5(getRequest('conf_password'));		  
      $requestdata['created_by']        = getFromSession('userid');
	  $requestdata['created_date']      = date('Y-m-d h:i:s');

       if (getRequest('approved_permission') == 1) {
           $requestdata["approved_permission"] = 1;
       }else{
           $requestdata["approved_permission"] = 0;
       }
       
       if (getRequest('checked_permission') == 1) {
           $requestdata["checked_permission"] = 1;
       }else{
           $requestdata["checked_permission"] = 0;
       }

        if (getRequest('app_special')) {
            $requestdata["app_special"] = getRequest('app_special');
        } else {
            $requestdata["app_special"] = 0;
        }

	$store_ids = array_filter((array) getRequest('store'));
	$store_ids = implode(',', $store_ids);
	if(!empty($store_ids)){
	    $requestdata["store_ids"] = $store_ids;
	}
	
      $info        		=  array();
      $info['table']	= USER_TBL;
      $info['data'] 	= $requestdata;     
      $info['debug']  	=  false;                     
      $res = insert($info);
      //dBug($info);
      //dBug($requestdata);
	  if($res['affected_rows']) {
	  	  header("location:index.php?app=user");
	  }else {	 
	    header("location:index.php?app=user&cmd=add");
	  }      
   }
   
   function updateEmployee() {
   	  $id = getRequest('id');
   	  $requestdata = array();
      $requestdata = getUserDataSet(USER_TBL);      

	$requestdata['password'] = md5(getRequest('conf_password')); 
	  //$requestdata['modified_by']       		= getFromSession('userid');
	  //$requestdata['modified_time']     		=  date('Y-m-d h:i:s');

	if (getRequest('approved_permission') == 1) {
            $requestdata["approved_permission"] = 1;
        } else {
            $requestdata["approved_permission"] = 0;
        }

        if (getRequest('checked_permission') == 1) {
            $requestdata["checked_permission"] = 1;
        } else {
            $requestdata["checked_permission"] = 0;
        }

        if (getRequest('app_special')) {
            $requestdata["app_special"] = getRequest('app_special');
        } else {
            $requestdata["app_special"] = 0;
        }
	 
	$store_ids = array_filter((array) getRequest('store'));
	$store_ids = implode(',', $store_ids);
	if(!empty($store_ids)){
	    $requestdata["store_ids"] = $store_ids;
	}

	  $info        		=  array();
      $info['table']	= USER_TBL;
      $info['data'] 	= $requestdata;    	  
      $info['where']	= "userid='$id'";     
      //$info['debug']  	=  true;    
      $res = update($info);
      
      if($res)
      {	  	
         header("location:index.php?app=user");
      }
	  else
	  {
	  	header("location:index.php?app=user&id=".getRequest('id'));
	  }     
                
   }//EOFn
   
   function checkUserID($userid){
   	   
	   $sql = "SELECT * FROM ".USER_TBL." WHERE userid='".$userid."'";
	   $res = mysql_query($sql);
       if(mysql_num_rows($res)>0)
       {
          	echo "Invalid"; 
       }else{
	   		echo "valid"; 
	   }
   }
   
   function getUserInfo($id)
   {
   	   $data           =  array();                  
       $info           =  array();     
       $info['table']  =  USER_TBL;
       $info['where']  =  "userid='".$id."' ";
       $info['debug']  =  false;                     
       $res            =	select($info);
       if(count($res))
       {
          foreach($res as $i=>$v)
          {
             $data[$i] = $v;             
          }
       }
       //dumpVar($data);
       return $data[0];
   }

	   
   function deleteUser() {
      if(getRequest('id'))
      {
			$id = getRequest('id'); 
			             	
			$info = array();
			$info['table']  =  USER_TBL;
       		$info['where']  =  "userid='".$id."' ";
			$info['debug'] = false;      	
			$res = delete($info);
			 if($res)
			  {	  	
				 header("location:index.php?app=user");
			  }
			  else
			  {
				header("location:index.php?app=user&id=".getRequest('id'));
			  }    	
      	}	
   }
   
   function blockUser($userid){
   	   
	   $sql = "UPDATE ".USER_TBL." SET status ='Inactive' WHERE userid='".$userid."'";
	   $res = mysql_query($sql);
       header("location:index.php?app=user&id=".getRequest('id'));
   } 
   function unBlockUser($userid){
   	   
	   $sql = "UPDATE ".USER_TBL." SET status ='Active' WHERE userid='".$userid."'";
	   $res = mysql_query($sql);
       header("location:index.php?app=user&id=".getRequest('id'));
   } 
         	
} // End class
?>
