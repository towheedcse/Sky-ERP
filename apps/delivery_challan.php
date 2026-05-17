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
   $thisApp  = new DeliveryChallan();

   // Instanciate the user class
   $thisUser = new User();

   //Including header
	if(getRequest('cmd') != 'loadProduct' && getRequest('cmd') != 'loadSOInfo' && getRequest('cmd')!="getOrderInf" && getRequest('cmd')!="get_stock_qty" && getRequest('cmd')!="loadWOInfo")
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
	if(getRequest('cmd') != 'loadProduct' && getRequest('cmd') != 'loadSOInfo' && getRequest('cmd')!="getOrderInf" && getRequest('cmd')!="get_stock_qty" && getRequest('cmd')!="loadWOInfo")
	{
	//require_once(FOOTER);
	}

   
?>
