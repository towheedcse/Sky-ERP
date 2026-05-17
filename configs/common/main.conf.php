<?php
   /***********************************************************
    *
    *  Main Configuration File
    *
    *  Purpose: this file is used to load all configuration
    *           related to current project
    *
    *  CVS ID: $Id$
    *
    ***********************************************************/
   // ====== SMS Token =======
   define('SMS_TOKEN', '51d14171f206f0018fcb868dbfdaab9c');
   //define('SMS_TOKEN', '17520846309571772022/12/1301:55:12pm2#GIeLM&');

   // Default date/time format
   define('DATE_FORMAT', 'm/d/Y');
   define('TIME_FORMAT', 'h:i:s T');
   
   //Default Profile Mode
   define('PROFILE_MODE',FALSE);
   
   // Server name
   define('SERVER_NAME', $_SERVER['SERVER_NAME']);

   // Set server OS to Unix or Windows
   if (empty($_SERVER['WINDIR']))
       define('SERVER_OS', 'Unix');
   else
       define('SERVER_OS', 'Windows');

   // Server protocol and port
   if (preg_match("/HTTP\//i", $_SERVER['SERVER_PROTOCOL']))
       define('SERVER_PROTOCOL', 'http');
   else
       define('SERVER_PROTOCOL', 'https');

   define('SERVER_PORT', $_SERVER['SERVER_PORT']);

   // Script name
   define('PHP_SELF', $_SERVER['PHP_SELF']);

   // Site URL
   define('SITE_URL', SERVER_PROTOCOL . '://' . SERVER_NAME);

   // Start session
   session_start();
   
   // define absolute path      
   define('DOCUMENT_ROOT',             $_SERVER['DOCUMENT_ROOT'].PROJECT_DIR);
   define('APP_DIR',                   DOCUMENT_ROOT . '/apps');
   define('CONFIG_DIR',                DOCUMENT_ROOT . '/configs');
   define('CLASS_DIR',                 DOCUMENT_ROOT . '/classes');
   define('LIB_DIR',                   DOCUMENT_ROOT . '/libs');
   define('TEMPLATES_DIR',             DOCUMENT_ROOT . '/contents');
   define('TEMPLATES_SKINS',           DOCUMENT_ROOT . '/contents/skins');
   define('TEMPLATES_CSS',             DOCUMENT_ROOT . '/contents/css');
   define('TEMPLATES_JS',              DOCUMENT_ROOT . '/contents/js');
   define('IMAGES_DIR',                DOCUMENT_ROOT . '/images');
   define('EXT_DIR',                   DOCUMENT_ROOT . '/externals');
   define('DOCUMENTS_DIR',             DOCUMENT_ROOT . '/docs');
   define('AJAX_DIR',                  EXT_DIR . '/ajax');
   define('CHART_DIR',                 DOCUMENT_ROOT . '/chart');
   
   // Defining Relative path 
   define('REL_APP_DIR',                   PROJECT_DIR);
   
   define('REL_EXT_DIR',                   REL_APP_DIR.'/externals');
   define('REL_CONTENT_DIR',               REL_APP_DIR.'/contents');
   define('REL_CHART_DIR',				   REL_APP_DIR.'/chart');
   

   // phpMailer confoguration
   define('PHPMAILER_DIR',             EXT_DIR . '/phpmailer');
   require_once(PHPMAILER_DIR .        '/class.phpmailer.php');

   // PDF configuration
   define('HTML2PDF_DIR',              EXT_DIR.'/html2fpdf/'); //by Imran
   require_once(HTML2PDF_DIR .         'class.html2fpdf.php'); //by imran

 
   // Common directories
   define('COMMON_DIR',                    APP_DIR     . '/common');
   define('COMMON_CONFIG_DIR',             CONFIG_DIR  . '/common');
   define('COMMON_CLASS_DIR',              CLASS_DIR   . '/common');
   define('COMMON_LIB_DIR',                LIB_DIR     . '/common');
	 
   // File upload directory
   define('DOCUMENT_REPOSITORY',         DOCUMENT_ROOT . '/docs');
	 
	 define('CHART_LIB_DIR',                 CHART_DIR   . '/lib');
	 //echo CHART_LIB_DIR;
	 //require_once(CHART_LIB_DIR .   '/phpchartdir.php');
	 //echo CHART_LIB_DIR .   '/phpchartdir.php';
   // Image directory
   define('IMAGE_DIR',                   REL_APP_DIR . '/images');
   //define('IMAGE_DIR',                   '/images');

   // Font directory
   define('FPDF_FONTPATH',               EXT_DIR . '/font/');

   //Set OS based on client's request header
   define('CLIENT_OS', strpos(strtolower($_SERVER['HTTP_USER_AGENT']),'win') ? 'Windows' : 'Other');

   // Load required files
   require_once(COMMON_CONFIG_DIR. '/database.conf.php');
   require_once(COMMON_LIB_DIR .   '/common.lib.php');
   require_once(COMMON_LIB_DIR .   '/general.lib.php');
	 
   // Common class names
   define('USER_CLASS',                  COMMON_CLASS_DIR . '/User.class.php');
   //define('DOCUMENT_CLASS',            COMMON_CLASS_DIR . '/Document.class.php');
   require_once(USER_CLASS);
   // Application Specific Configuration Loader
   //

   // Get the name of the script used to load your application
   define('CURRENT_APP_NAME', basename($_SERVER['PHP_SELF']));

   // Get application directory
   define('CURRENT_APP_DIR', dirname($_SERVER['SCRIPT_FILENAME']));

   // Get the application prefix (i.e. login.php has 'login' prefix)
   //list($appPrefix, $appExt) = explode('.', CURRENT_APP_NAME);
   $appPrefix = getRequest('app');
   
   define('CURRENT_APP_PREFIX', 			$appPrefix);
   define('CURRENT_APP_CONFIG_FILE', 	CONFIG_DIR . '/' .  CURRENT_APP_PREFIX . '.conf.php');
   define('CURRENT_APP_LIB_FILE',    	LIB_DIR .    '/' .  CURRENT_APP_PREFIX . '.lib.php');
   define('CURRENT_APP_CLASS_FILE',  	CLASS_DIR .  '/' .  CURRENT_APP_PREFIX . '.class.php');
   define('CURRENT_APP_SKIN_FILE',   	TEMPLATES_SKINS .  '/' .  CURRENT_APP_PREFIX . '.html');
   define('CURRENT_APP_JS_FILE',   		TEMPLATES_JS .  '/' .  CURRENT_APP_PREFIX . '.js');
   
   // Common URLs
   define('LOGIN_URL',     	'index.php?app=login');
   define('LOGOUT_URL',    	'index.php?app=logout');
   define('USER_HOME_URL', 	'index.php?app=user_home');
   define('HOME_URL', 			'index.php?app=home');

   // Load the application configuration file (if any)
   if (file_exists(CURRENT_APP_CONFIG_FILE))
   {
      require_once(CURRENT_APP_CONFIG_FILE);
   }

   // Load the application library file (if any)
   if (file_exists(CURRENT_APP_LIB_FILE))
   {
      require_once(CURRENT_APP_LIB_FILE);
   }   

   // Load the application class file (if any)
   if (file_exists(CURRENT_APP_CLASS_FILE))
   {
      require_once(CURRENT_APP_CLASS_FILE);
   }
   /*
   if (file_exists(CURRENT_APP_JS_FILE))
   {
      require_once(CURRENT_APP_JS_FILE);
   }
   */
   // User related configuration
   /*
   define('USER_HOME_DIR',                STANDARD_CONTENTS_DIR . '/user_home');
   define('NO_PERMISSION_TEMPLATE',       USER_HOME_DIR . '/permission_denied.html');
   */
   
   define('HEADER',                       TEMPLATES_SKINS . '/header.html');
   //define('HEADERS',                      TEMPLATES_SKINS . '/header4ledger.html');
   define('HEADERS',                      TEMPLATES_SKINS . '/header.html');
   define('FOOTER',                       TEMPLATES_SKINS . '/footer.html');

/*
   define('DEFAULT_HOME_TEMPLATE',        USER_HOME_DIR . '/default_home.html');
   define('DEFAULT_HEADER_TEMPLATE',      USER_HOME_DIR . '/default_header.html');
   define('DEFAULT_NAVIGATION_TEMPLATE',  USER_HOME_DIR . '/default_navigation.html');
   
*/

define('ACTIVE_USER_STATUS', 'Active');


?>
