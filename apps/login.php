<?php
     /***********************************************************
    *  Filename: employee.php
    *
    *  Author  : md_arefin@yahoo.com
    *
    *  Version : $Id$
    *
    *  Purpose : This the application file that is invoked to start the it system manager application
    *
    *  Copyright (c) 2006 by Arefin (md_arefin@yahoo.com)
    ***********************************************************/
	 //require_once(HEADER);
   // Create a login application object
   require_onces();
   $thisApp = new LoginApp();

   // Instanciate the user class
   $thisUser = new User();

    // Run the application
    if ($thisUser->isAuthenticated()) {
        $thisUser->goHome();
    } else {
   	$thisApp->run();
    }

   
   //require_once(FOOTER);
?>
