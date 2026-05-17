<?php
    
   require_onces();
   $thisApp  = new Factory();

   // Instanciate the user class
   $thisUser = new User();

   //Including header
   require_once(HEADER);

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
   require_once(FOOTER); 
?>
