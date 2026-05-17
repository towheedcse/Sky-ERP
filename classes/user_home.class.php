<?php
class UserHome
{
   /**
   * This is the "main" function which is called to run the application
   *
   * @param none
   * @return true if successful, else returns false
   */
   function run()
   {
   		 		
      
      $cmd = getRequest('cmd');            
      
      switch ($cmd)
      {
         case 'list'               : $screen = $this->showList($msg);   break;
         case 'mis_dashboard'      : $screen = $this->misDashboard();   break;
         default                   : $screen = $this->showList($msg);   break;
      }

      // Set the current navigation item
      //$this->setNavigation('ITSystem');
      

      return true;
   }

    
   function showList($msg = null)
   {      
      require_once(CURRENT_APP_SKIN_FILE);      
   }

    function misDashboard()
    {
        require_once(TEMPLATES_SKINS . '/mis_dashboard.php');
    }

} // End class

?>
