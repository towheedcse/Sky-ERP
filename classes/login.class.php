<?php
class LoginApp
{
   function run()
   {
      $cmd = getRequest('cmd');
      switch ($cmd)
      {      	 
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
	
      if($ok)
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
  function checkLicenceKey(){
	/*  
	ob_start(); // Turn on output buffering
	system("ipconfig /all"); //Execute external program to display output
	$mycom=ob_get_contents(); // Capture the output into a variable
	ob_clean(); // Clean (erase) the output buffer

	$findme = "Physical";
	$pmac = strpos($mycom, $findme); // Find the position of Physical text
	$mac=substr($mycom,($pmac+36),17); // Get Physical Address		
	if($mac=="D0-50-99-0A-5B-A8"){ 
		 return true;			
	} else {
	   //echo "Invalid System configuration $mac"; exit;
	   return false;
	}
	*/	
	$bangla_filename = "/etc/trusted-key/xpnrk.dll";
	$bangla_handle = fopen($bangla_filename, "rb");
	$bangla_contents = fread($bangla_handle, filesize($bangla_filename));
	fclose($bangla_handle);
	if(file_exists($bangla_filename)) {
	 $contentArr = explode("###",$bangla_contents);
	 $cdate = date("Y-m-d");
	 $sql = "SELECT DATEDIFF('".$contentArr[1]."','".$cdate."') AS Diff";
	 $res = mysql_query($sql);
	 $row = mysql_fetch_object($res); 
	 $result = $row->Diff;
	 if($contentArr[0]=="e9c0f552874cdcf6720668134743f7d4" && $result>0){ 
		 return true;
		}
	 } else {
	   //echo "Invalid System configuration"; exit;
	   return false;
	 }
	 
	 
} 
/* 
function checkLicenceKey(){
	$serverIP   = $_SERVER['REMOTE_ADDR'];
	$serverName = $_SERVER['SERVER_NAME'];
	if(($serverIP=="182.160.110.161") && ($serverName=="www.lsgbd.com" || $serverName=="lsgbd.com")) {
		 return true;
	} else {
	   return false;
	}
}
*/ 
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
			  $row 			= mysql_fetch_array($res);
			  
		   	  $project_id 		= $row['project_id'];
			  $project_name 	= $row['project_name'];
			  $project_type 	= $row['project_type'];
			  $location 		= $row['location'];
			  $equipment_auto_out 	= $row['equipment_auto_out'];
			  $inventory_auto_out 	= $row['inventory_auto_out'];
			  $header 		= $row['header'];
			  $footer 		= $row['footer'];
		  	  $ceiling_status 	= $row['ceiling_status'];
		  	  $overdue_invoice 	= $row['overdue_invoice'];
		  	  $overdue_bill 	= $row['overdue_bill'];
			  
			  insertIntoSession('project_id', $project_id);			  
			  insertIntoSession('project_name', $project_name);
			  insertIntoSession('project_type', $project_type);
			  insertIntoSession('location', $location);
			  insertIntoSession('equipment_auto_out', $equipment_auto_out);
			  insertIntoSession('inventory_auto_out', $inventory_auto_out);
			  insertIntoSession('header', $header);
			  insertIntoSession('footer', $footer);
            		  insertIntoSession('ceiling_status', $ceiling_status);
            		  insertIntoSession('overdue_invoice', $overdue_invoice);
            		  insertIntoSession('overdue_bill', $overdue_bill);
			  
		}
 }
 
} // End class

?>
