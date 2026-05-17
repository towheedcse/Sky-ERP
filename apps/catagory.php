<?php
    /***********************************************************
    *  Filename: catagory.php
    *
    *  Author  : arefin@yahoo.com
    *
    *  Version : $Id$
    *
    *  Purpose : This the application file that is invoked to start the it system manager application
    *
    *  Copyright (c) 2006 by Arefin
    ***********************************************************/
   require_onces();
   // Instantiate the ItSystemManager class
   $thisApp  = new Catagory();

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
