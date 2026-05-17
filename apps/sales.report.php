<?php
    /***********************************************************
    *  Filename: bank_account.php
    *
    *  Author  : md_arefin@yahoo.com
    *
    *  Version : $Id$
    *
    *  Purpose : This the application file that is invoked to start the it system manager application
    *
    *  Copyright (c) 2010 by Arefin
    ***********************************************************/
   require_onces();
   // Instantiate the GeneralVouchar class
   $thisApp  = new SalesReport();

   // Instanciate the user class
   $thisUser = new User();

   //Including header
   if (getRequest('cmd') != 'monthly_trt_status') 
   {
	if(getRequest('cmd') == 'undelivered_list' || getRequest('cmd') =="batchlist" || getRequest('cmd') =="product_wise_sl_sum" || getRequest('cmd') =="pending_order_list" || getRequest('cmd') =="order_list" || getRequest('cmd') =="sales.status_by_cat"){
        	require_once(HEADERS);
	}else{
		require_once(HEADER);
	}
   }

   // Checks the user authentication
   if($thisUser->isAuthenticated())
   {
	if(getRequest('cmd') == "order.po.status"){
		require_once(TEMPLATES_SKINS . '/requiredproduction.php'); 
	}elseif(getRequest('cmd') =="aging_report"){
		require_once(TEMPLATES_SKINS . '/aging_report.php'); 
	}elseif(getRequest('cmd') =="show_lc_report"){
		$path = DOCUMENT_ROOT . '/forms/show_lc_report.php';
		require_once($path);
	}else{
		$thisApp->run();
	}
      
   }
   else
   {
      $thisUser->goLogin();
   }
   
   //Including footer
   require_once(FOOTER); 
?>
