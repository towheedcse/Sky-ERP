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
   $thisApp  = new ContraVoucher();

   // Instanciate the user class
   $thisUser = new User();

   //Including header
    if(getRequest('cmd') != 'save_tmp' ){
	require_once(HEADER);
	}

   // Checks the user authentication
   if($thisUser->isAuthenticated()) {           
      $thisApp->run();     
   }
   else
   {
      $thisUser->goLogin();
   }
   
   //Including footer
   if(getRequest('cmd') != 'save_tmp' )
   {
   require_once(FOOTER); 
   }
?>
