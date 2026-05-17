<?php
    /***********************************************************
    *  Filename: .php
    *
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
   // Instantiate the HomeApp class
   $thisApp  = new HomeApp();

   // Instanciate the user class
   $thisUser = new User();
   
   //Including header
   if (getRequest('cmd') != 'login' && getRequest('cmd') != 'user_comments' && getRequest('cmd') != 'deleteCommentsList')
   {
   	//require_once(HEADER);
   }

    // Run the application
    if ($thisUser->isAuthenticated()) {
        $thisUser->goHome();
    } else {
   	$thisApp->run();
    }

   //Including footer
   if (getRequest('cmd') != 'login' && getRequest('cmd') != 'user_comments' && getRequest('cmd') != 'deleteCommentsList')
   {
	   //require_once(FOOTER); 
	 }
?>
