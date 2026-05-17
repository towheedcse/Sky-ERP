<?php
    /***********************************************************
    *  Filename: purchase.php
    *
    *  Author  : md_arefin@yahoo.com
    *
    *  Version : $Id$
    *
    *  Purpose : This the application file that is invoked to start the it system manager application
    *
    *  Copyright (c) 2006 by Arefin
    ***********************************************************/
   require_onces();
   // Instantiate the Production class
   $thisApp  = new FGOut();

   // Instanciate the user class
   $thisUser = new User();

   //Including header
	if(getRequest('cmd') != 'loadProduct' && getRequest('cmd') != 'load_stock' && getRequest('cmd') != 'get_productinfo')
	{
		if(getRequest('cmd') == 'pro_dtl'){
		require_once(HEADERS);
		}else{
		require_once(HEADER);
		}
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
	if(getRequest('cmd') != 'loadProduct' && getRequest('cmd') != 'load_stock' && getRequest('cmd') != 'get_productinfo')
	{
	require_once(FOOTER);
	}

   
?>
