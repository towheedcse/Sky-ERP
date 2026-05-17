<?php

   /*******************************************************

    *  File name: database.conf.php

    *

    *  Purpose: this file is used to store database

    *           table name constants and it also starts

    *           the database connection

    *

    *  CVS ID: $Id$

    *

    ********************************************************/



   // If main configuration file which defines VERSION constant

   // is not loaded, die!

/*  

 if (! defined('VERSION'))

   {

      echo "You cannot access this file directly!";

      die();

   }

*/

   // Please note:

   // in production mode, the database authentication information

   // may vary.

   

   define('PRODUCTION_MODE', TRUE);

   

   if (PRODUCTION_MODE)

   {

     define('DB_USER', 'root');

     define('DB_PASS', 'thai_root_pass');



     define('DB_NAME', 'thai');

     define('DB_HOST', 'thai_db');

   }



//========  Table For SSGROUP  ===================
   define('USER_TBL',                     	DB_NAME . '.user'); 
   define('USERTYPE_TBL',                   	DB_NAME . '.usertype');           
   define('DISTRICT_TBL', 			DB_NAME . '.district');	        
   define('BANK_TBL',            		DB_NAME . '.bank');	  		 	        
   define('PARTY_INFO_TBL',            		DB_NAME . '.cs_party_info');  	  		 	        
   define('PARTY_TBL',            		DB_NAME . '.cs_party');  	 	        
   define('PROJECT_TBL',            		DB_NAME . '.project');  		 	        
   define('EMPLOYEE_TBL',            		DB_NAME . '.employee');  		 	        
   define('CUSTOMER_BOOKING_TBL',           DB_NAME . '.cs_customer_booking'); 		 	        
   define('CS_PRODUCT_RECEIVED_TBL',        DB_NAME . '.cs_received_product'); 		 	        
   define('CS_LOAN_DISTRIBUTE_TBL',         DB_NAME . '.cs_loan_distribute');   		 	        
   define('CS_LOAN_RECEIVED_TBL',           DB_NAME . '.cs_receive_loan');   		 	        
   define('CS_DELIVERY_PRODUCT_TBL',        DB_NAME . '.cs_delivery_product');   
		 	        
   define('DEVIT_VOUCHAR_TBL',        		DB_NAME . '.cs_delivery_product');   		 	        
   define('CREDIT_VOUCHAR_TBL',        		DB_NAME . '.credit_vouchar');   		 	        
   define('PAYABLE_CHECK_TBL',        		DB_NAME . '.payable_check'); 

   define('PRODUCT_TBL',					DB_NAME . '.product');     		 	        
   define('SUPPLIER_TBL',            		DB_NAME . '.supplier_info');      
   define('COUNTRY_TBL',               	  	DB_NAME . '.country');           
   define('CURRENCY_TBL',               	DB_NAME . '.currency');            
   define('CATAGORY_TBL',               	DB_NAME . '.catagory'); 
   define('MAIN_CATAGORY_TBL', 			DB_NAME . '.main_category');         
   define('BANK_TBL',               		DB_NAME . '.bank');            
   define('BANK_ACCOUNT_TBL',               DB_NAME . '.bank_account');         		 		 	        
   define('SUB_ACC_HEAD_TBL',   			DB_NAME . '.sub_acc_head');  
             		 		 	        
   define('ACCOUNT_JOURNAL_TBL',   			DB_NAME . '.account_journal');               		 		 	        
   define('PURCHASE_DETAILS_TBL',   		DB_NAME . '.purchase_details');               		 		 	        
   define('PURCHASE_MASTER_TBL',   			DB_NAME . '.purchase_master');                		 		 	        
   define('SALES_DETAILS_TBL',   			DB_NAME . '.sales_details');               		 		 	        
   define('SALES_MASTER_TBL',   			DB_NAME . '.sales_master'); 
              		 		 	        
   define('STOCK_LEDGER_TBL',   			DB_NAME . '.stock_ledger');  

   define('AUTO_CONNECT_TO_DATABASE', TRUE);

   

  if (AUTO_CONNECT_TO_DATABASE)

  {

      $dbcon = mysql_connect(DB_HOST, DB_USER, DB_PASS) or die("Could not connect: " . mysql_error());

      //$dbcon = mysql_connect('localhost', 'root', 'root') or die("Could not connect: " . mysql_error());

      mysql_select_db(DB_NAME, $dbcon) or die("Could not find: " . mysql_error());

  }



?>
