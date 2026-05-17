<?php
    
   require_onces();
   $thisApp  = new SalesTarget();

   // Instanciate the user class
   $thisUser = new User();

   //Including header
   if(getRequest('cmd') != 'loadProduct' && getRequest('cmd') != 'save_tmp' && getRequest('cmd') != 'save_cst' && getRequest('cmd') != 'loadGPG' && getRequest('cmd') != 'loadCST' && getRequest('cmd') != 'copySalesTarget' && getRequest('cmd') != 'loadGroupCatagory')
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
   if(getRequest('cmd') != 'loadProduct' && getRequest('cmd') != 'save_tmp' && getRequest('cmd') != 'save_cst' && getRequest('cmd') != 'loadGPG' && getRequest('cmd') != 'loadCST' && getRequest('cmd') != 'copySalesTarget' && getRequest('cmd') != 'loadGroupCatagory')
   {
   require_once(FOOTER); 
   }
?>
