<?php
    
   require_onces();
   $thisApp  = new ChildHead();

   // Instanciate the user class
   $thisUser = new User();

   //Including header
   if(getRequest('cmd') != 'loadsubhtype'){
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
   if(getRequest('cmd') != 'loadsubhtype'){
   require_once(FOOTER); 
   }
?>
