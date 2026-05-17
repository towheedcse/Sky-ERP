<?php
    /***********************************************************
    *  Filename: used_item.php
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
   // Instantiate the UsedItem class
   $thisApp  = new UsedItem();

   // Instanciate the user class
   $thisUser = new User();

   //Including header
	if(getRequest('cmd') != 'loadStock')
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
	if(getRequest('cmd') != 'loadStock')
	{
	require_once(FOOTER);
	}

   
?>
