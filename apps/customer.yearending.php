<?php
    
   require_onces();
   $thisApp  = new CustomerYearEnding();

   // Instanciate the user class
   $thisUser = new User();

   //Including header
   if(getRequest('cmd') != 'loadArea'){
	require_once(HEADER);
   }
   // Checks the user authentication
   if($thisUser->isAuthenticated())
   {
        
      $thisApp->run();
      
   }
   else
   {
      $thisUser->goLogin();
   }
   
   //Including footer
   if(getRequest('cmd') != 'loadArea'){
   require_once(FOOTER); 
   }
?>
