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
   // Instantiate the ItSystemManager class
   require_onces();
   $thisApp  = new UserApp();

   // Instanciate the user class

   $thisUser = new User();



   //Including header

   if (getRequest('cmd') != 'checkUser') 
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

   if (getRequest('cmd') != 'checkUser' && getRequest('cmd') != 'showstylelist')

   {  

     require_once(FOOTER);

   }

  

?>

