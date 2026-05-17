<?php

   require_onces();
   // Instantiate the ItSystemManager class
   $thisApp  = new adminPanel();

   // Instanciate the user class 
      
  $thisUser = new User();
  $u_t_id = getFromSession('u_type_id');
  if($u_t_id == 1) 
  {     
	   //if ((getRequest('cmd') != 'email'))    
		//{
			require_once(HEADER);
		//}	
	
	   // Checks the user authentication
	   if($thisUser->isAuthenticated())
	   {
	   // Checks the user authentication
		 
		  $thisApp->run();
	   }  
	   
	  //Including footer 
	 //if ((getRequest('cmd') != 'email') && (getRequest('cmd') != 'addbuddy') && (getRequest('cmd') != 'block') && (getRequest('cmd') != 'myprofile') && (getRequest('cmd') != 'editbuddy') && (getRequest('cmd') != 'edit'))    
	 //{
		require_once(FOOTER); 
	 //} 
   }
   else{
   		header("location:index.php?app=home&msg=You are not authorised !!!");
   }
?>
