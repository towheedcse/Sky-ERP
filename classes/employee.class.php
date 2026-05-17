<?php
/**
 * File: employee.class.php
 * This application is used to authenticate users
 *
 */
class Employee
{
  
   function run()
   {         

      $cmd = getRequest('cmd');
	  $u_t_id = getFromSession('u_type_id');
      if(($u_t_id == 101) || ($u_t_id == 107)) //101 = sysadmin  107 = hr
      {

      	switch ($cmd)
      	{
      	   case 'add'                	: $screen = $this->showEditor($msg); break;
      	   case 'edit'               	: $screen = $this->showEditor("Edit Page");    break;	
      	   case 'doUpdate'           	: $this->updateEmployee(); break;
		   case 'delete'             	: $screen = $this->deleteEmployee(); break;
      	   case 'list'               	: $screen = $this->showList($msg);   break;
      	   default                   	:$cmd = 'list'; $screen = $this->showList($msg);   break;

      	}

      }else {
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
	  $data['record_list'] 	= $this->getEmployeeList(getRequest('from'),getRequest('to'));    
	  $data['totalrecord']	= $this->getTotalEmployeeList(getRequest('from'),getRequest('to'));	 
	  //$data['buyer_list']	= $orderApp->getBuyerList();   
	  
	   if(getRequest('deleted')=='yes') {
		  $data['message'] = "Item Deleted Successfully";
	   }elseif(getRequest('deleted')=='no') {
		  $data['message'] = "Item Not Deleted";
	   }
	   require_once(CURRENT_APP_SKIN_FILE); 
	   return $data[0];
   }
   
   function getProjectList()
   {	

      $data 			= array(); 
      $info        		=  array();
      $info['table']	= PROJECT_TBL;
	  $info['fields'] 	= array('project_id','project_name');
	  $info['where']  	= "status=1";	
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
   function getEmployeeList($from,$to) {  

		if($from == "" && $to == ""){$from=0; $to=35;}
		$srckey 	= getRequest('srckey');
		
		$info           = array();    
		$info['table']  =  EMPLOYEE_TBL.' e,'.PROJECT_TBL.' p';	 
		$sql="list_view='Active' AND p.project_id = e.project_id";
		
		if($srckey!=""){
			$sql.=" AND (name LIKE '%$srckey%' OR employee_id LIKE '%$srckey%')";
		}
			
		$info['where']  =$sql;	
	  
		$info['orderby'] = array("emp_code asc LIMIT $from,$to");
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
   
   function getTotalEmployeeList($from,$to) {  

   	 if($from == "" && $to == ""){$from=0; $to=35;}
		$srckey 	= getRequest('srckey');
		
		$info           = array();    
		$info['table']  =  EMPLOYEE_TBL;	 
		$sql="list_view='Active'";
		
		if($srckey!=""){
			$sql.=" AND (name LIKE '%$srckey%' OR employee_id LIKE '%$srckey%')";
		}
			
		$info['where']  =$sql;	
	  
		$info['orderby'] = array("emp_code asc LIMIT $from,$to");
		$info['debug']  = false;
		$result         = select($info);
		$data           = array();     

      if($cnt) {
        return $cnt;
      } 
	  else {
	  return 0;
	 }      
      
   }       
     
   /**
   * Shows editor for Employee system
   * @paran null
   * @return none
   */

   function showEditor($msg = null) { 
     	  
      $ID = getRequest('id');
	  if ($ID) {
         $advArr = $this->getEmployeeInfo($ID);
         $advArr = parseThisValue($advArr);  
         $data   = array_merge(array(), $advArr); 
      }
      else
      {
         if(getRequest('submit'))
         {
            $this->addEmployee();	
         }
      }	
	  require_once(CLASS_DIR.'/common.list.class.php');	
	  $comListApp 	= new CommonList(); 
	  $data['branch_list']   	 		= $comListApp->getBranchList();   
	  $data['project_list']   	 		= $this->getProjectList();
	 
      $data['message'] = $msg;
      $data['cmd']     = getRequest('cmd');
	  require_once(EMPLOYEE_ADD_EDIT_SKIN);      
      return true;
   }
        
   function addEmployee($msg = null)
   {    	  
   	  $requestdata = array();
      $requestdata = getUserDataSet(EMPLOYEE_TBL);	
      //dumpvar($requestdata);	
	  $requestdata['photo_path'] 		= $this->photoUpload('photo_path', getRequest('mobile'));
	  $requestdata['name']   			= getRequest('sub_head_name');	
	  $requestdata['dob']   			= formatDate(getRequest('dob'));	
	  $requestdata['joining_date']   	= formatDate(getRequest('joining_date'));	              
      $requestdata['created_by']        = getFromSession('userid');
	  $requestdata['created_date']      = date('Y-m-d h:i:s');
      $requestdata['modified_by']       = getFromSession('userid');
	  $requestdata['modified_time']     = date('Y-m-d h:i:s');
		 
      $info        		=  array();
      $info['table']	= EMPLOYEE_TBL;
      $info['data'] 	= $requestdata;     
      $info['debug']  	=  false;                     
      $res = insert($info);
      //dBug($info);
      //dBug($requestdata);
	  if($res['affected_rows']) {
	   
	  	require_once(CLASS_DIR.'/common.class.php');	
	 	$comApp = new Common(); 
		$Acc_id = $comApp->NewID(SUB_ACC_HEAD_TBL,"sub_id","A000001","A",7);
	  	$comApp->saveRecord(SUB_ACC_HEAD_TBL,"sub_id",$Acc_id,"joining_date","photo_path","created_by","created_time","","");
		
	  	header("location:index.php?app=employee");
	  }else {	 
	    header("location:index.php?app=employee&cmd=add");
	  }      
   }
   
   function updateEmployee() {
   	  $id = getRequest('id');
   	  $requestdata = array();
      $requestdata = getUserDataSet(EMPLOYEE_TBL);	
      //dumpvar($requestdata);	 
	  $requestdata['name']   			= getRequest('sub_head_name');		    
	  $requestdata['photo_path'] 		= $this->photoUpload('photo_path', getRequest('mobile'));
	  $requestdata['dob']   			= formatDate(getRequest('dob'));	
	  $requestdata['joining_date']   	= formatDate(getRequest('joining_date'));  	
	  $requestdata['resign_date']   	= formatDate(getRequest('resign_date'));   	 	     
	  $requestdata['modified_by']       = getFromSession('userid');
	  $requestdata['modified_time']     =  date('Y-m-d h:i:s');
	 
	  $info        		=  array();
      $info['table']	= EMPLOYEE_TBL;
      $info['data'] 	= $requestdata;    	  
      $info['where']	= "emp_code=$id";     
      $info['debug']  	=  true;    
      $res = update($info);
      
      if($res)
      {	  	
         header("location:index.php?app=employee");
      }
	  else
	  {
	  	header("location:index.php?app=employee&id=".getRequest('id'));
	  }     
                
   }//EOFn
   
   function getEmployeeInfo($id)
   {
   	   $data           =  array();                  
       $info           =  array();     
       $info['table']  =  EMPLOYEE_TBL;
       $info['where']  =  "emp_code='".$id."' ";
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

	   
   function deleteEmployee() {
      if(getRequest('id'))
      {
			$id = getRequest('id'); 
			             	
			$info = array();
			$info['table']  =  EMPLOYEE_TBL;
       		$info['where']  =  "emp_code='".$id."' ";
			$info['debug'] = false;      	
			$res = delete($info);
			 if($res)
			  {	  	
				 header("location:index.php?app=employee");
			  }
			  else
			  {
				header("location:index.php?app=employee&id=".getRequest('id'));
			  }    	
      	}	
   }
    
   function photoUpload($name, $id)
   {
       $source = $_FILES[$name]['tmp_name'];
       $name   = $_FILES[$name]['name'];
       $ext    = array_pop(explode('.', $name));
       $img_name = $id.'.'.$ext;
       //echo "<br>Name : ".$name;       
		$dest = "employee";		
       if(file_exists(IMAGES_DIR.'/'.$dest)){
          $dir = 1;
       }else{
         if(mkdir(IMAGES_DIR.'/'.$dest)){
            $dir = 1; 
         }else{
            $dir = 0;	
         } 	
       }      

       if($dir){
       	  //$ext = array_pop(explode('.', $name));
          $arr = getimagesize($source);           
          if(is_array($arr)){            	              	                             
             $dest = $dest.'/'.$img_name;
             $this->file_dest = $dest;
             if(move_uploaded_file($source, IMAGES_DIR.'/'.$dest)){
                 return $dest; 
             }
          }else {
             echo "Not An Image";	
          }	
       }	
    }
//=============End Photo Upload=============
       	
} // End class


?>

