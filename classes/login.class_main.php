<?php
class LoginApp
{
   function run()
   {
      $cmd = getRequest('cmd');
      switch ($cmd)
      {
      	 case 'memberlogin'		: $this->memberLogin(); 	break;   
		 case 'logout'			: $this->memberLogOut(); 	break;        
         default               	: $this->login($msg);  break;
      }
	  return true;
   }
   
   function login()
   {
   	 $credentials = array();
	  
      // Get the user supplied credentials
     
      $credentials = array();
		
      // Get the user supplied credentials
      $credentials['userid']      = getRequest('loginid');
      //$credentials['userid']    = getRequest('loginid');
      $credentials['password']    = getRequest('password');
      // Create a new user object with the credentials
      $thisUser = new User($credentials);
      $loginid = $credentials['userid'];
      // Authenticate the user
      $ok = $thisUser->authenticate();

      // If successful (i.e. user supplied valid credentials)
      // show user home

      if ($ok)
      {    
	  	  //if($_SERVER['HTTP_HOST']!="ssgroupbd.net"){ echo "Invalid User"; exit;}
		  $this->projectTypeInsertIntoSession();
	  	  mysql_query("Update user set online ='Y' where userid = '$loginid'");
          $thisUser->goHome();
      }
      // User supplied invalid credentials so show login form
      // again
      else
      {          
		  $_SESSION['msg_active']=1;
          include_once(CURRENT_APP_SKIN_FILE);
      }
	  
  }  
  
 function memberLogin()
 {
	  $credentials = array();
	  $str = getRequest('strdata');	
	  $direction = getRequest('dir');		
      if($str)
      {      	   
      	  $arr = explode('COLSEP', $str);
		  $loginid  = $arr[0];
		  $password = $arr[1];
		  
          $credentials['userid'] 	= $loginid; 	  
		  $credentials['password']	= $password;
		  
	  }      
      // Create a new user object with the credentials
      $thisUser = new User($credentials);

      // Authenticate the user
      $ok = $thisUser->authenticate();
	 	  
      if ($ok)
      {   
	  	mysql_query("Update user set online ='Y' where userid = '$loginid'");	
		echo "Success";
      }
      else
      {        
		  	echo "Invalid";	
			//$_SESSION['msg_text'] = "Invalid User ID and Password. Please try again !!!";
			$_SESSION['msg_active']=1;		 		   
      }

   }
 
 function memberLogOut()
 {
 	    $userid = getFromSession('userid');
 		$res = mysql_query("Update user set online ='N' where userid = '$userid'");
		if($res!=0)
		{
		   session_unset();
		   session_destroy();
		   header("Location:?app=home");
		}
 }
 
 function projectTypeInsertIntoSession()
 {
 	    $project_id = getFromSession('project_id');
		$sql = "SELECT * FROM ".PROJECT_TBL." where project_id = '$project_id'";
 		$res = mysql_query($sql);
		if($res!=0)
		{
			  $row 				= mysql_fetch_array($res);
			  
		   	  $project_id 			= $row['project_id'];
			  $project_name 		= $row['project_name'];
			  $project_type 		= $row['project_type'];
			  $location 			= $row['location'];
			  $equipment_auto_out 	= $row['equipment_auto_out'];
			  $inventory_auto_out 	= $row['inventory_auto_out'];
			  $header 				= $row['header'];
			  $footer 				= $row['footer'];
			  
			  insertIntoSession('project_id', $project_id);			  
			  insertIntoSession('project_name', $project_name);
			  insertIntoSession('project_type', $project_type);
			  insertIntoSession('location', $location);
			  insertIntoSession('equipment_auto_out', $equipment_auto_out);
			  insertIntoSession('inventory_auto_out', $inventory_auto_out);
			  insertIntoSession('header', $header);
			  insertIntoSession('footer', $footer);
			  
		}
 }
 
} // End class

?>
