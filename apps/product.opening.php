<?php
     /***********************************************************
    *  Filename: employee.php
    *
    *  Author  : md_aefin@yahoo.com
    *
    *  Version : $Id$
    *
    *  Purpose : This the application file that is invoked to start the it system manager application
    *
    *  Copyright (c) 2006 by Arefin (md_aefin@yahoo.com)
    ***********************************************************/
   require_onces();
   // Instantiate the Product class
   $thisApp  = new ProductOpening();

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
