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
    *  Copyright (c) 2006 by Arefin
    ***********************************************************/
   require_onces();
   // Instantiate the AccountsLedger class
   $thisApp  = new AccountsLedger();

   // Instanciate the user class
   $thisUser = new User();

   //Including header
   if(getRequest('cmd') != 'loadhead' && getRequest('cmd') != 'loadheads' && getRequest('cmd') != 'loadsl2' && getRequest('cmd') != 'loadsl3'){
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
	if(getRequest('cmd') != 'loadProduct' && getRequest('cmd') != 'loadsl2' && getRequest('cmd') != 'loadsl3')
	{
	require_once(FOOTER);
	}

   
?>
