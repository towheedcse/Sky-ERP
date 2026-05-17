<?php
   require_onces();
   $thisApp  = new AttachProduct();

   // Instanciate the user class
   $thisUser = new User();

   //Including header
   if(getRequest('cmd') != 'loadProduct' && getRequest('cmd') != 'save_tmp' && getRequest('cmd') != 'loadGPG')
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
   
   //Including footer
   if(getRequest('cmd') != 'loadProduct' && getRequest('cmd') != 'save_tmp' && getRequest('cmd') != 'loadGPG')
   {
   require_once(FOOTER); 
   }
?>
