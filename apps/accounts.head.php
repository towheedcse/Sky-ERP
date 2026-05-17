<?php
    
   require_onces();
   $thisApp  = new AccountHead();

   // Instanciate the user class
   $thisUser = new User();

   //Including header 
   if(getRequest('cmd') != 'loadsubhtype' && getRequest('cmd') != 'loadchildhtype' && getRequest('cmd') != 'loadSL3Htype')
   {
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
  if(getRequest('cmd') != 'loadsubhtype' && getRequest('cmd') != 'loadchildhtype' && getRequest('cmd') != 'loadSL3Htype')
   { 
   //Including footer
   require_once(FOOTER); 
   }
?>
