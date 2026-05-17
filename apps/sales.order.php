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
   // Instantiate the Sales class
   $thisApp  = new SalesOrder();

   // Instanciate the user class
   $thisUser = new User();

   //Including header
	if(getRequest('cmd') != 'loadProduct' && getRequest('cmd') != 'get_dtl' && getRequest('cmd') != 'deltemp' && getRequest('cmd') != 'get_temp_dtl' && getRequest('cmd') != 'get_undelivery' && getRequest('cmd') != 'get_temp_order' && getRequest('cmd') != 'save_tmp' && getRequest('cmd') != 'loadcatProduct' && getRequest('cmd') != 'loadcustomer' && getRequest('cmd') != 'loadundelivery_inv')
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
	if(getRequest('cmd') != 'loadProduct' && getRequest('cmd') != 'get_dtl' && getRequest('cmd') != 'deltemp' && getRequest('cmd') != 'get_temp_dtl' && getRequest('cmd') != 'get_undelivery' && getRequest('cmd') != 'get_temp_order' && getRequest('cmd') != 'save_tmp' && getRequest('cmd') != 'loadcatProduct' && getRequest('cmd') != 'loadundelivery_inv')
	{
	//require_once(FOOTER);
	}

   
?>
