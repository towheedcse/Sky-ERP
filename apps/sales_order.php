<?php
    /***********************************************************
    *  Filename: purchase.php
    *
    *  Author  : arefin@yahoo.com
    *
    *  Version : $Id$
    *
    *  Purpose : This the application file that is invoked to start the it system manager application
    *
    *  Copyright (c) 2006 by 
    ***********************************************************/
   require_onces();
   // Instantiate the Sales class
   $thisApp  = new SalesOrder();

   // Instanciate the user class
   $thisUser = new User();

   //Including header
	if(getRequest('cmd') != 'loadProduct' && getRequest('cmd') != 'add_undelivery' && getRequest('cmd') != 'get_dtl')
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
	if(getRequest('cmd') != 'loadProduct' && getRequest('cmd') != 'add_undelivery' && getRequest('cmd') != 'get_dtl')
	{
	//require_once(FOOTER);
	}

   
?>
