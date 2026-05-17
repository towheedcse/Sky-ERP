<?php

/**
 * File: signup.class.php
 * This application is used to authenticate users
 *
 */

class adminPanel
{
   /**
   * This is the "main" function which is called to run the application
   *
   * @param none
   * @return true if successful, else returns false
   */
   function run()
   {
     
    $cmd = getRequest('cmd');     
	$u_t_id = getFromSession('u_type_id');
    if($u_t_id == 1) 
    {       
		  switch($cmd)
		  {            
			 case 'viewmember'       : $screen = $this->viewMember(); break;			 
			 case 'setting'          : $screen = $this->updateMember(getRequest('id'),getRequest('status')); break;	 
			 case 'deletebuddy'     : $screen = $this->deleteBuddy(getRequest('id'),getRequest('tbl')); break;
			 
			 case 'add'                   		: $screen = $this->showHelpCat($msg); break;
			 case 'edit'                  		: $screen = $this->showHelpCat("Edit Page"); break;
			 case 'update'                		: $this->updateHelpCat(); break;
			 case 'delete'                		: $screen = $this->deleteHelpCat(); break;
									 			 			 			 
			 default     					  : $cmd = 'helplist'; $screen = $this->showHelpCat($msg);   break; 
		  }
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
      else if($cmd == 'helplist')
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

         require_once(HELP_CAT_SKIN_FILE);      
      }     
      return true;
   }
     
   function getMemberList()
   {
   		$userid = getFromSession('userid');
        $info            = array();
        $info['table']   =  USER_TBL.' u,'.USER_PROFILE_TBL.' p,'.COUNTRY_TBL.' c';
	    $info['fields']  =   array('p.userid','p.headline','c.country','u.email','u.status',"date_format(u.create_date,'%d-%M-%Y') AS create_date");          
	    $info['where']   =  "u.userid = p.userid AND c.countrycode = u.country and u.userid !='$userid'";  	    
	    $info['orderby'] = array('p.headline ASC');
	    $info['debug']   = false;
			   
      //dumpvar($info);
      $result          = select($info);
      //dBug($result);
      $data            = array();
      
      if(count($result))
      {
         foreach($result as $key=>$value)
         {
            $data[$key][]	= $value;
            //dumpvar($value);
         }	
      }
       //dumpvar($value);           
      return $data;
   }
   function getCountryList()
   {
   		$info            = array();
      $info['table']   = COUNTRY_TBL;
      $info['fields'] = array('countrycode', 'country'); 
      $info['debug']   = false;
      
      $result          = select($info);
      //dBug($result);
      $data            = array();
      
      if(count($result))
      {
         foreach($result as $i=>$v)
         {
            $data[$i] = $v;             
         }
      }
                  
      return $data;
   }
   /**
   * Shows editor for Subject system
   * @paran null
   * @return none
   */
   function viewMember($msg = null)
   {   	    	  
	   $data                = array();
	   $data['member_list'] = array($this->getMemberList());                  
    
       $data['message'] = $msg;
       $data['cmd']     = getRequest('cmd');     
       require_once(CURRENT_APP_SKIN_FILE);     
       return $data;
   }
   // ===== start edit buddy ======
  
   function updateMember($id = null,$status=null)
   {  
	  $res = mysql_query(" UPDATE `user` SET `status` = '$status' WHERE userid = '$id'");    
	 
	  if($res>0)
	  {
	       header("location:index.php?app=admin_panel&cmd=viewmember&msg=y");	
	  }
	  else
	  {	 
	   		header("location:index.php?app=admin_panel&cmd=viewmember&msg=n");
	  }   
	 
   } 
   
   function deleteBuddy($id=null,$tbl=null)
   {
      // update needed
      $buddy_from        = getFromSession('userid');
      if($tbl=="buddy_block")
      {      	           	
      	$info = array();
      	$info['table'] = BUDDY_BLOCK_TBL;
      	$info['where'] = "buddy_to='$id' and buddy_from ='$buddy_from'";
      	$info['debug'] = false;      	
      	$res = delete($info);
      	
      	if($res)
      	{      	   
      	   header("location:index.php?app=member_profile&cmd=block_list&msg=Successfully delete buddy from your buddy list.");      	         	   
      	}      	
      	else
      	{
      		 header("location:index.php?app=member_profile&cmd=block_list&msg=delete fail.");      	   	
      	}      	
      }
	  else {
	     	           	
      	$info = array();
      	$info['table'] = BUDDY_LIST_TBL;
      	$info['where'] = "buddy_to='$id' and buddy_from ='$buddy_from'";
      	$info['debug'] = false;      	
      	$res = delete($info);
      	
      	if($res)
      	{      	   
      	   header("location:index.php?app=member_profile&cmd=online_buddies&msg=Successfully delete buddy from your buddy list.");      	         	   
      	}      	
      	else
      	{
      		 header("location:index.php?app=member_profile&cmd=online_buddies&msg=delete fail.");      	   	
      	}      	
      
	  }	
   }
   //======== end add edit buddy =======
      
  //======== end ======================   
    
  function formatDate($dt)
  {
  	if(trim($dt))
  	{
    	$day   = substr($dt,0,2);
    	$month = substr($dt,3,2);
    	$year  = substr($dt,6,4);
    	return $year."-".$month."-".$day;
    }
  }     
 
//================= Help ===============================
   
   function getHelpCatagoryList()
   {
      $info            = array();
      $info['table']   = HELP_TBL;   
	  $info['where']   = "cat_id=0";      
      $info['debug']   = false;
      
      $result          = select($info);
      //dBug($result);
      $data            = array();
      
      if(count($result))
      {
         foreach($result as $key=>$value)
         {
            $data[$key][]	= $value;
            //dumpvar($value);
         }	
      }
       //dumpvar($value);           
      return $data;
   }
  
   function showHelpCat($msg = null)
   {   	    	  
	   $data                = array();
	   $data['cat_list'] = array($this->getHelpCatagoryList());                   
      $ID = getRequest('id');
       
      if ($ID)
      {
         $helpCatArr = $this->getHelpCatagoryInfo($ID);
         $helpCatArr = parseThisValue($helpCatArr);       
         $data      = array_merge(array(), $helpCatArr);
        
		 //require_once(SUBJECT_ADD_EDIT_SKIN);     
         //dumpvar($data);             
      }
      else
      {
         if(getRequest('submit'))
         {
            $this->addHelpCat();	
         }
      }
      
      $data['message'] = $msg;
      $data['cmd']     = getRequest('cmd');
           
      require_once(HELP_CAT_SKIN_FILE); 
      return $data;
   }

   function addHelpCat($msg = null)
   {   	  
   	  
   	  $requestdata = array();
      $requestdata = getUserDataSet(HELP_TBL);	  	  
	 
      
      $info        =  array();
      $info['table']= HELP_TBL;
      $info['data'] = $requestdata;      
      
      $res = insert($info);
                 
      if($res['affected_rows'])
      {
      	 if($msg==""){$msg = "Successfully Save Record.";}
         header("location:index.php?app=admin_panel&cmd=helplist&msg=$msg");
      }
      else
      {
      	 $message = "Already Exist...";
         header("location:index.php?app=admin_panel&cmd=add&msg=$message");
      }
                	
   }
   
   function updateHelpCat()
   {
   	 
   	  $id = getRequest('id');
   	  
   	  $requestdata = array();
      $requestdata = getUserDataSet(HELP_TBL);
      $info        =  array();
      $info['table']= HELP_TBL;
      $info['data'] = $requestdata;
      $info['where']= "help_id='$id'";
      
      $res = update($info);
      
      if($res)
      {	  	 
		 if($msg==""){$msg = "Successfully Update Record";}	
         header("location:index.php?app=admin_panel&cmd=helplist&msg=$msg");
      }
      else
      {
         header("location:index.php?app=admin_panel&cmd=edit&id=$id");
      }
                
   }//EOFn
   
   
   function getHelpCatagoryInfo($id)
   {
       $data           =  array();
                          
       $info           =  array();     
       $info['table']  =  HELP_TBL;
       $info['where']  =  "help_id='$id'";
       $info['debug']  =  false;                     
       $res            =	select($info);
       
       if(count($res))
       {
          foreach($res as $i=>$v)
          {
             $data[$i] = $v;             
          }
       }
       return $data[0];
   }    
   
            
   function deleteHelpCat()
   {
      if(getRequest('id'))
      {
      	$id = getRequest('id');
      	
      	$info = array();
      	$info['table'] = HELP_TBL;
      	$info['where'] = "help_id='$id'";
      	$info['debug'] = false;
      	
      	$res = delete($info);
        
      	if($res)
      	{  
		  if($msg==""){$msg = "Successfully Delete Record";}	
           header("location:index.php?app=admin_panel&cmd=helplist&msg=$msg");		
      	}      	
      	else
      	{
      		 header("location:index.php?app=admin_panel&cmd=helplist&deleted=no");      	   	
      	}      	
      }	
   }
   //******* Find Record ***********
	
  
function getExamSchedule()
{  
   	  $id = getRequest('esid');
   	  
      $info = array();
      $info['table']= HELP_TBL;
      //$info['fields'] = array('subjectid','subjectname');       
      $info['where']   = "help_id='$id'";           
      $info['debug']   = false;
      //dumpvar($info);
      
            
      $result          = select($info);
      //$result = mysql_query($stmt);
      //dBug($result);
      $data            = array();
      
      if(count($result))
      {
         foreach($result as $key=>$value)
         {
            $data[$key][]	= $value;
            //dumpvar($value);
         }	
      }
      
       //dumpvar($value);           
      return $data;
   }
   //****** End *******
   
   
} // End class

?>
