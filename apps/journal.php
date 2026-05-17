<?php

    /***********************************************************
    *  Filename: employee.php
    *
    *  Author  : md_arefin@yahoo.com
    *
    *  Version : $Id$
    *
    *  Purpose : This the application file that is invoked to start the it system manager application
    *
    *  Copyright (c) 2006 by Arefin (md_arefin@yahoo.com)
    ***********************************************************/
   require_onces();
   $thisApp  = new Journal();

   // Instanciate the user class

   $thisUser = new User();



   //Including header

   if (getRequest('cmd') != 'preview') 
   {  
     require_once(HEADERS);
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

   if (getRequest('cmd') != 'preview' && getRequest('cmd') != 'showstylelist')
   {  

     require_once(FOOTER);

   }

  

?>

