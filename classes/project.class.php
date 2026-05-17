<?php
/**
 * File: signup.class.php
 * This application is used to authenticate users
 *
 */
class Project
{
   
   function run()
   {     

		$cmd = getRequest('cmd');
		$u_t_id = getFromSession('u_type_id');
	
		if($u_t_id == 101) 
		{      
		  switch($cmd)
		  { 
		  	 case 'add'                	: $screen = $this->showEditor($msg); break;
      	     case 'edit'               	: $screen = $this->showEditor("Edit Page");    break;			 
      	   	 case 'doUpdate'           	: $this->updateRecord(); break;
		     case 'delete'             	: $screen = $this->deleteRecord(getRequest('id')); break;
			 default                   	: $cmd = 'list'; $screen = $this->showEditor($msg);   break;
	
		  }
	
		}else {
      		header("location:index.php?app=user_home&msg=You are not authorised !!!");
      	} 

		
		if($cmd == 'list')
		{
			 if($deleted = getRequest('deleted'))
			 {
				if($deleted == 'yes')
				{
				   $screen['message'] = "Item Deleted Successfully";
				}
	
				else
				{
					  $screen['message'] = "Item Deletion Failure";
				}
			 }
			 require_once(CURRENT_APP_SKIN_FILE);
			 
		}
		return true;
   }
   
  // ======== Start Top Skills ==========       

  function showEditor()
  {
		 $project_id = getRequest('id');
		
		 if($project_id)
		 {		 			
			 if(getRequest('save'))
			 {  
				$this->updateRecord($project_id);
				$msg="Successfully Update Record !!!";
        		header("location:?app=project&cmd=view&msg=$msg");		      	
			 } 

		 } else {
			
			if(getRequest('save')) {
				$this->insertRecord();
				$msg="Successfully Save Record !!!";
        		header("location:?app=project&cmd=view&msg=$msg");	     		       		      	
			 }			 
		 }	 

	  $data                		= array();
	  $data['project_list']  	= array($this->getRecords(getRequest('from'),getRequest('to')));      
	  $data['totalrecord']  	= $this->getTotalRecords(); 
	  
	  $data['message'] 			= $msg;
	  $data['cmd']     			= getRequest('cmd'); 
	  require_once(CURRENT_APP_SKIN_FILE);
	  return $data[0];

   }

   function updateRecord($id)
   {       
	  $requestdata = array();
      $requestdata = getUserDataSet(PROJECT_TBL);	
	  
	  $requestdata['header'] 			= $this->photoUpload('header', "header$id");
	  $requestdata['footer'] 			= $this->photoUpload('footer', "footer$id"); 
	  
   	  $info        						=  array();
      $info['table']					= PROJECT_TBL; 	
      //dBug($requestdata);
	  $info['data'] 					= $requestdata;
	  $info['where']					= "project_id ='$id'";  
	   $info['debug']  					=  false;    
	  $res = update($info);
	  
      if(!$res) {
        header("location:?app=project&cmd=view");
      }               

   }//EOFn 

   function insertRecord()
   {       
	  $requestdata 						= array();
      $requestdata 						= getUserDataSet(PROJECT_TBL);    
      $project_id 						= $this->createID(); 
	  $requestdata['header'] 			= $this->photoUpload('header', "header$id");
	  $requestdata['footer'] 			= $this->photoUpload('footer', "footer$id"); 
	  $requestdata['created_by']        = getFromSession('userid');
	  $requestdata['created_time']      = date('Y-m-d h:i:s');
	   
      if($$project_id != -1)
      {
      	$requestdata['project_id']   	= $project_id;
      }
      else
      {
      	$msg = "ID overflow !!!";
      	header("location:index.php?app=user_home&msg=$msg");
      	exit;
      }
	  
   	  $info        						=  array();
      $info['table']					= PROJECT_TBL; 
	  $info['data'] 					= $requestdata;  
	  $info['debug']  					=  false;  
	  $res = insert($info); 
	  if($res)
      {
       return true;
      }else{
	   return false;
	  } 
	         

   }//EOFn   
   
   function getRecords($from=null,$to=null)
   {
	   if($from == "" && $to == ""){$from=0; $to=10;}  
	   $project_id = getRequest('project_id');
	   
  	   $data            = array();	  
  	   $info            = array();
	   $info['table']   = PROJECT_TBL; 	  
	   $info['orderby'] = array("project_id asc LIMIT $from,$to");
	   $info['debug']   = false;			 

	   $res            =	select($info);   

	   if(count($res))
	   {
		  foreach($res as $i=>$v)
		  {
			 $data[$i][] = $v;
		  }
	   }
	   return $data;

  }
  
  function getTotalRecords()
   {	    
	    if($from == "" && $to == ""){$from=0; $to=10;}  
	   $project_id = getRequest('project_id');
	   
  	   $data            = array();	  
  	   $info            = array();
	   $info['table']   = PROJECT_TBL; 
	   if($project_id !=""){    
       	 $info['where']  =  "project_id='".$project_id."' ";
	   }
	   $info['orderby'] = array('project_id ASC');
	   $info['debug']   = false;
	   $res            =	select($info);
	   if(count($res))
	   {
		  $total_job = count($res);
	   }                 

      return $total_job;

  }
  function deleteRecord($id)
  {
   	  if(getRequest('id'))
      { 
      	$info = array();
      	$info['table'] = PROJECT_TBL;
      	$info['where'] = "project_id='$id'";
      	$info['debug'] = false;
      	$res = delete($info);      	

      	if($res)
      	{
      	  $msg="Successfully delete Record !!!";
          header("location:?app=project&cmd=view&msg=$msg");     	   

      	} else{
      		 header("location:?app=project&cmd=view&cmd=list&deleted=no");
      	}      	

      }

   } 
    // ==== function createJobSeekerID==================
   function createID()
   {
      $info = array();
      $info['table'] = PROJECT_TBL;
      $info['fields'] = array('max(project_id) as maxproject');
      
      $res = select($info);
      
      $maxprojectId = 'P0000';
      
      if(count($res))
      {
         foreach($res as $v)
         {
         	 if($v->maxproject)
         	 {
             $maxprojectId = $v->maxproject;
             }
             break;   	
         }
      
      }
      
      $maxprojectId = generateID("P",$maxprojectId,5);
      return $maxprojectId;
   }   
   
   function photoUpload($name, $id)
   {
       $source = $_FILES[$name]['tmp_name'];
       $name   = $_FILES[$name]['name'];
       $ext    = array_pop(explode('.', $name));
       $img_name = $id.'.'.$ext;
       //echo "<br>Name : ".$name;       
		$dest = "project image";		
       if(file_exists(IMAGES_DIR.'/'.$dest))
       {
          $dir = 1;
       }
       else
       {
         if(mkdir(IMAGES_DIR.'/'.$dest))
         {
            $dir = 1; 
         }
         else
         {
            $dir = 0;	
         } 	
       }      

       if($dir)
       {
       	  //$ext = array_pop(explode('.', $name));
          $arr = getimagesize($source);           
          if(is_array($arr))
          {            	              	                             
             $dest = $dest.'/'.$img_name;
             $this->file_dest = $dest;
             if(move_uploaded_file($source, IMAGES_DIR.'/'.$dest))
             {
                 return $dest; 
             }
          }else {
             echo "Not An Image";	
          }	
       }	
    }

	function projectInfo()
    {	  
	   $project_id = getFromSession('project_id');
  	   $data            = array();	  
  	   $info            = array();
	   $info['table']   = PROJECT_TBL;
	     
       $info['where']  =  "project_id='".$project_id."' "; 
	   
	   $info['debug']   = false; 
	                       
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
  
      
} // End class



?>

