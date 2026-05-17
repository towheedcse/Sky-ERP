<?php
/**
 * File: sales.class.php
 * This application is used to authenticate users
 *
 */
require_once('journal.class.php');
class UsedItem extends Journal
{
   
   function run()
   {         
      $cmd = getRequest('cmd');
      $u_t_id = getFromSession('u_type_id');
      if(($u_t_id == 101) || ($u_t_id ==102)) // 1 = sysadmin, 2 = admin, 3 = project admin
      {

      	switch ($cmd)
      	{
      	   case 'add'			: $this->showEditor(); break;
	   case 'edit'			: $this->showEditor(); break;
	   case 'add_used'		: $this->showEditor4UsedItem(); break; 
	   case 'saveUsed'		: $this->saveStock(); break;
      	   case 'loadStock'  		: $this->getStockBalanceList(trim(getRequest('product_id'))); break;  
	   case 'print_vouchar'		: $this->showPrintEditor($msg); break;   
	   case 'show_pro_ledger'	: $this->showProductPrintEditor($msg); break;    
	   case 'stock_ledger'		: $this->showProductStockEditor($msg); break;  
	   case 'delete'             	: $this->deleteRecord(getRequest('id')); break;
      	   default                   	: $screen = 'list'; $screen = $this->showEditor();   break;

      	}

      }elseif(($u_t_id == 103) || ($u_t_id ==104) || ($u_t_id == 105) || ($u_t_id == 106) || ($u_t_id == 109)){

      	switch ($cmd)
      	{
		   case 'print_vouchar'		: $screen = $this->showPrintEditor($msg); break;   
		   case 'show_pro_ledger'	: $screen = $this->showProductPrintEditor($msg); break;    
		   case 'stock_ledger'		: $screen = $this->showProductStockEditor($msg); break;  
      	   	   default                   	: $screen = 'list'; $screen = $this->showEditor(); break;

      	}

      }else {
      	header("location:index.php?app=user_home&msg=You are not authorised !!!");
      }      	          

      if($cmd == 'list') {

         if($deleted = getRequest('deleted')) {
            if($deleted == 'yes') {
               $screen['message'] = "Item Deleted Successfully";
            } else {
            	  $screen['message'] = "Item Deletion Failure";	
            }
        }
       require_once(STOCK_ITEM_SKIN_FILE);
      } 
      return true;
   } 
  
   function showPrintEditor($msg = null) {      	  
      
	  $product_id 	= getRequest('product_id');  
	  if ($product_id) {
          $advArr 					= $this->getProductInfo($product_id);
          $advArr 					= parseThisValue($advArr); 
		  $data   					= array_merge(array(), $advArr); 
		  $data['message'] = $msg;
		  $data['cmd']     = getRequest('cmd');
		  require_once(ACCOUNT_LEDGER_SKIN);      
		  return true;
	 }else{
		require_once(PRINT_VOUCHAR_SKIN);
	 }
   }

function showProductPrintEditor($msg = null) {      	  
      	  $data = array();
	  $product_id 	= getRequest('product_id');

	  if ($product_id){
		$advArr 	   = $this->getProductInfo($product_id);
		$advArr 	   = parseThisValue($advArr); 
		$data   	   = array_merge(array(), $advArr); 
		$data['message']   = $msg;
		$data['cmd']       = getRequest('cmd');
		$data['store_id']  = getRequest('store_id'); 
		$data['date_from'] = formatDate(getRequest('date_from'));
		$data['date_to']   = formatDate(getRequest('date_to'));
		require_once(ACCOUNT_LEDGER_SKIN);      
		return true;
	 }else{
	  	require_once(CLASS_DIR.'/common.list.class.php');	
	  	$comListApp = new CommonList();    	
	  	$data['depo_list']  = $comListApp->getDeliveryPointList(); 
		require_once(SHOW_PRODUCT_LEDGER_SKIN);	    
		return $data[0];	
	  }
   }
   function showProductStockEditor($msg = null) { 
		$data = array();	
		if(getRequest('submit')){ 		    
		  $data['date_from'] = formatDate(getRequest('date_from'));
		  $data['date_to']   = formatDate(getRequest('date_to'));
		  require_once(ACCOUNT_STOCK_LEDGER_SKIN);      
		  return true;
		}else{
			 require_once(SHOW_STOCK_PRINT_SKIN);      
		     return true;
		}	 
   }
   function getProductInfo($id)
   {
   	   $data           =  array();                  
       $info           =  array();     
       $info['table']  =  PRODUCT_TBL;
       $info['where']  =  "product_id='".$id."' ";
       $info['debug']  =  false;                     
       $res            =	select($info);
       if(count($res))
       {
          foreach($res as $i=>$v)
          {
             $data[$i] = $v;             
          }
       }
       //dumpVar($data);
       return $data[0];
   }
   function showEditor($msg = null) {
      
   	   $data                	= array();
	 	
	   $data['cmd']         	= getRequest('cmd');   
	   require_once(STOCK_ITEM_SKIN_FILE); 
	   return $data[0];
   }  
   function showEditor4UsedItem($msg = null) {
      
   	   $data                	= array();	
	   $data['cmd']         	= getRequest('cmd');   
	   require_once(CURRENT_APP_SKIN_FILE); 
	   return $data[0];
   }

    function saveStock()
 	{     
 	   	$voucher_no 	= rand();
		$project_id 	= getFromSession('project_id');  
	    $product_id     = getRequest('product_id');   
	    $m_unit     	= getRequest('m_unit');   
	    $qty     		= getRequest('qty');  
		$stockDr = $this->getTotalDebitStock($product_id,$project_id);
		$stockCr = $this->getTotalCreditStock($product_id,$project_id);
		$stockBalance = $stockDr-($stockCr+$qty);
		$this->saveStockJournal($voucher_no,$project_id,$product_id,0,$m_unit,0,$qty,$stockBalance);		
		header("location:index.php?app=used_item&cmd=print_vouchar&product_id=".$product_id);
     }//EOFn  
	
    
	function getTotalCreditStock($acc_head,$project_id){

   		$sql = "SELECT sum(`cr`) as credit_amount FROM ".STOCK_LEDGER_TBL." WHERE product_id = '$acc_head' AND project_id = '$project_id'";

		$row = mysql_fetch_object(mysql_query($sql));

		$credit_amount = $row->credit_amount;

		if(empty($credit_amount)){

			$credit_amount = 0;

		}

		return $credit_amount;

   }
  
   function getTotalDebitStock($acc_head,$project_id){

   		$sql = "SELECT sum(`dr`) as debit_amount FROM ".STOCK_LEDGER_TBL." WHERE product_id = '$acc_head' AND project_id = '$project_id'";

		$row = mysql_fetch_object(mysql_query($sql));

		$debit_amount = $row->debit_amount;

		if(empty($debit_amount)){

			$debit_amount = 0;

		}

		return $debit_amount;

   }
	function saveStockJournal($voucher_no,$project_id,$product_id,$unit_price=NULL,$m_unit,$DR=NULL,$CR=NULL,$balance){
		$sql = "INSERT INTO ".STOCK_LEDGER_TBL." (voucher_no,project_id,product_id,unit_price,m_unit,dr,cr,balance) VALUES('".$voucher_no."','".$project_id."','".$product_id."','".$unit_price."','".$m_unit."','".$DR."','".$CR."','".$balance."')";
		mysql_query($sql);
	}
	function getStockBalanceList($id){
		$project_id = getFromSession('project_id');  
		$stockDr = $this->getTotalDebitStock($id,$project_id);
		$stockCr = $this->getTotalCreditStock($id,$project_id);

		$stock = $stockDr-$stockCr;
		if($stock>0){
			echo $stock;
		}else{
			echo "0";
		}
	}
 
//==================== End Sales Details =====================
   
} // End class


?>
