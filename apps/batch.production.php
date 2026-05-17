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
   $thisApp  = new BatchProduction();

   // Instanciate the user class
   $thisUser = new User();

   //Including header
	if(getRequest('cmd') != 'loadProductBatch' && getRequest('cmd') != 'getdaycapacity' && getRequest('cmd') != 'get_dtl' && getRequest('cmd') != 'load_stock' && getRequest('cmd') != 'loadstockqty' && getRequest('cmd') != 'save_po' && getRequest('cmd') != 'delete' && getRequest('cmd') != 'get_po_dtl')
	{
	require_once(HEADER);
	}

   // Checks the user authentication
   if($thisUser->isAuthenticated())
   {
	if(getRequest('cmd') == "customize.production"){
		require_once(TEMPLATES_SKINS . '/customize.production.php'); 
	}else{
		$thisApp->run();
	}
      
   }
   else
   {
      $thisUser->goLogin();
   }
   
   //Including footer
	if(getRequest('cmd') != 'loadProductBatch' && getRequest('cmd') != 'getdaycapacity' && getRequest('cmd') != 'get_dtl' && getRequest('cmd') != 'load_stock' && getRequest('cmd') != 'loadstockqty' && getRequest('cmd') != 'save_po' && getRequest('cmd') != 'delete' && getRequest('cmd') != 'get_po_dtl')
	{
	//require_once(FOOTER);
	}

   
?>
