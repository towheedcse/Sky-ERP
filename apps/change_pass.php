<?php
   require_onces();
   	require_once(HEADER);
   // Create a login application object
   $thisApp = new Change_Pass();

   // Run the application
   $thisApp->run();
   require_once(FOOTER); 
?>
