<?php
class Change_Pass
{
   function run()
   {
      $cmd = getRequest('cmd');
      
      switch($cmd)
      {      
         case 'add':        	$this->showEditor("change pass"); break;
         case 'change':     	$this->showSuccessText(); break;
		 case 'forget_pass':	$this->showForgetPassword(); break;
         case 'fail':       	$this->showFailureText(); break;
         default:           	$cmd = 'add'; $this->showEditor("change pass");
      }
      
      if($cmd=='add')
      {
         include_once(CURRENT_APP_SKIN_FILE);
      }
      
      return true;      
   } 
   
   //=========function showEditor===================== 
   function showEditor($msg=null)
   {
     $data['message'] = $msg;
          
     if(getRequest('submit'))
     {                
        $this->changePass();           
     }                  
   
   } 
   //=========function showEditor===================== 
   function showForgetPassword()
   {
     $data['message'] = $msg;
          
     if(getRequest('submit'))
     {                
        $this->setPass();           
     } 
	 include_once(FORGET_PASSWORD_SKIN_FILE);                 
   
   } 
   
   function changePass()
   {	
   	//==================values get from session==========================
   	
   		$userid            =  getFromSession('userid');
    	$old_password      =  getFromSession('password');
    	
    //==========values get from skin=================================== 
    	$current_password  =  md5(getRequest('current_password'));
    	$new_password      =  md5(getRequest('new_password'));
			
			$requestdata = array();
			$requestdata['new_password'] = $new_password;
		
   	if($new_password !="")
   	{ 		
   		 if($current_password == $old_password)
   			{
   				if($new_password!=$current_password)
   				{  
   		 		   $info['table']   = USER_TBL;
   		 		   $info['where']   = "userid = '$userid'";
   		 		   $info['data']    = array("password"=>"$requestdata[new_password]");	
   		 		   $info['debug']   = false;
   		 		   //dBug($info);
   		 		   if(update($info))
   		 		   {
   		 			   header("location:index.php?app=change_pass&cmd=change");	
   		 		   }
   		 		}header("location:index.php?app=change_pass&cmd=change");
   			}else header("location:index.php?app=change_pass&cmd=fail");
   			
   	}else header("location:index.php?app=change_pass&cmd=fail");
   }//==============EOFf changePass()========================
   
   function showSuccessText()
   {
         $data['message']  = "Password Changed Successfully.";

         include_once(CHANGE_PASS_SUCCESS_SKIN);
   }
   function showFailureText()
   {
         $data['message']  = "Password has not changed. Please, try again.";

         include_once(CHANGE_PASS_FAIL_SKIN);
   }
   
   
   function setPass()
   {	
   	//==================values get from session==========================
   	
   		$userid            =  getRequest('userid');    
    	
    //==========values get from skin===================================     
    	$password      =  md5(getRequest('password'));
			
		$requestdata = array();
		$requestdata['password'] = $password;
		
		if($password !="" && $userid!="")
		{ 	
			 
		   $info['table']   = USER_TBL;
		   $info['where']   = "userid = '$userid'";
		   $info['data']    = array("password"=>"$requestdata[password]");	
		   $info['debug']   = true;
		   //dBug($info);
		   if(update($info))
		   {
			   header("location:index.php?app=login");	
		   }
		   else{ header("location:index.php?app=change_pass&cmd=forget_pass");
		   }
	   
	   }
	   else{ header("location:index.php?app=change_pass&cmd=forget_pass");
	   }
	   
	   //==============EOFf changePass()========================
 }
   
} // End class

?>
