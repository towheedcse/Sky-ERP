<?php
/**
 * File: sales.class.php
 * This application is used to authenticate users
 *
 */
class UsedForFeed
{
   
   function run()
   {         

      $cmd = getRequest('cmd');
	  $u_t_id = getFromSession('u_type_id');
      if( ($u_t_id == 101) || ($u_t_id ==102) || ($u_t_id == 103)) // 1 = sysadmin, 2 = admin, 3 = project admin
      {

      	switch ($cmd)
      	{
      	   case 'add'					: $this->showEditor(); break;
		   case 'edit'					: $this->showEditor(); break;
		   case 'sal_dtl'				: $this->showEditor4SalesDetails(); break;
      	   case 'loadProduct'  			: $this->loadProduct4Catagory(trim(getRequest('catagory_id'))); break;  
		   case 'print_report'			: $screen = $this->showPrintEditor($msg); break;  
		   case 'delete'             	: $screen = $this->deleteRecord(getRequest('id')); break;
      	   default                   	:$cmd = 'list'; $screen = $this->showEditor();   break;

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
       require_once(CURRENT_APP_SKIN_FILE);
      } 
      return true;
   } 
  
   function showPrintEditor($msg = null) {      	  
      
	  $production_id 	= getRequest('production_id');  
	  if ($production_id) {
		$advArr 					= $this->getProductionMasterInfo($production_id);
		$advArr 					= parseThisValue($advArr); 
		$data   					= array_merge(array(), $advArr); 
      
		$data['item_list']	= $this->getProductionDetailsList($production_id);
		$data['message'] = $msg;
		$data['cmd']     = getRequest('cmd');
		require_once(PRINT_PRODUCTION_REPORT_SKIN);      
		return true;
	 }else{
		require_once(SHOW_PRINT_PRODUCTION_SKIN);
	  }
   }
     
   function showEditor($msg = null) {
      
   	   $data                	= array();
       
	   $data['finish_list'] 	= $this->getFinishProductList();	
	   $data['cat_list'] 		= $this->getCatagoryList();	
	   $data['currency_list']   = $this->getCurrencyList();
	 	if(getRequest('submit')){
  			$this->insertProductionMaster();
		}
	   $data['cmd']         	= getRequest('cmd');   
	   require_once(CURRENT_APP_SKIN_FILE); 
	   return $data[0];
   }

  function insertProductionDetails($production_id)
  {
		$requestdata 				= array();
		$arr_catagory_product_id	= array();

		$project_id  				= getFromSession('project_id');
      	$currency        			= getRequest('currency');

      	$arr_catagory_product_id	= getRequest('input_catagory_product_id');
      	$arr_m_unit        			= getRequest('input_m_unit');
      	$arr_amount					= getRequest('input_amount');
      	$arr_qty      				= getRequest('input_qty');
      	$arr_currency     			= getRequest('input_currency');
      

      	for($i=0;$i<count($arr_catagory_product_id);$i++)
      	{

		  $catagory_product_sep = $arr_catagory_product_id[$i];		
		  $requestdata['project_id'] = $project_id;       	  

      	  for($j=0;$j<count($catagory_product_sep);$j++)
		  {
				$catagory_product = explode("###",$catagory_product_sep);
				$catagoryid  = array();
				$productid = array();
				
				$staff_no = array();
				$catagoryid['c'] = $catagory_product[0];				
				$productid['p'] = $catagory_product[1];

			}

		   foreach($catagoryid as $val)
      	   {
      		    $requestdata['catagory'] = $val;	
      	   }      				

      	   foreach($productid as $val)
      	   {
      		    $requestdata['product'] = $val;	
				$product_id 			= $val;
      	   }
	   

		   foreach($arr_m_unit as $key => $val)
      	   {
      	   	  if($catagory_product_sep==$key)
      	   	  {
      		   	$requestdata['m_unit'] = $val;	
				$m_unit = $val;
      		  }

      	   }

      	   foreach($arr_qty as $key => $val)
      	   {
				if($catagory_product_sep==$key)
				{
					 $requestdata['qty'] = $val;	
				}
      	   }

      	   foreach($arr_currency as $key => $val)
      	   {
				if($catagory_product_sep==$key)
				{
      		     $requestdata['currency'] = $val;	
      		  	}
      	   }
     	         	  
		   foreach($arr_amount as $key => $val)
      	   {
      	   	  if($catagory_product_sep==$key)
      	   	  {
      		   	$requestdata['amount'] = $val;	
      		  }
      	   }
			//$rowCnt = 0;
		    $requestdata['created_by'] 		  = getFromSession('userid');
		    $requestdata['created_time']      = date('Y-m-d h:i:s');  
			$project_id						  = getFromSession('project_id'); 
		    $requestdata['project_id']        = $project_id;
		    $requestdata['production_id']     = $production_id;
 
		    $info        					  =  array();
		    $info['table']					  = PRODUCTION_DETAILS_TBL;
	  	    $info['data'] 	= $requestdata;      
			//echo "<br>";
			//dumpvar($info);	     
			    
		     $info['debug']  	=  true;
	    	 $res = insert($info);
			 if($res){
				 $used_item = $requestdata['qty'];
				 $totalCR  = $this->getTotalCreditStock($product_id,getFromSession('project_id'));
				 $totalDR  = $this->getTotalDebitStock($product_id,getFromSession('project_id'));					 
				 $balance  = ($totalDR - ($totalCR+$used_item));	
				 $used_date = formatDate(getRequest('used_date'));					
				 $this->saveStockJournal($production_id,$project_id,$product_id,$requestdata['amount'],$requestdata['m_unit'],0,$used_item,$balance,$used_date);
				
			  }
	   }
		if(getRequest('production_type')=="Finish"){
 	 	 $finish_product = getRequest('finish_product');
		 $finish_qty = getRequest('finish_qty');
		 $sold_cost = getRequest('sold_cost');
		 $totalFCR  = $this->getTotalCreditStock($finish_product,getFromSession('project_id'));
		 $totalFDR  = $this->getTotalDebitStock($finish_product,getFromSession('project_id'));					 
		 $balanceF  = ($totalFDR - ($totalFCR+$finish_qty));	
		 $used_date = formatDate(getRequest('used_date'));					
		 $this->saveStockJournal($production_id,$project_id,$finish_product,$sold_cost,$m_unit,0,$finish_qty,$balanceF,$used_date);
		}

  } //End of the function savePaymentDetails()

	
	function insertProductionMaster(){
	  $requestdata 						= array();	
	  $requestdata 						= getUserDataSet(PRODUCTION_MASTER_TBL); 
	  $requestdata['used_date'] 		= formatDate(getRequest('used_date'));
	  $requestdata['project_id']        = getFromSession('project_id');    
	  $requestdata['created_by']        = getFromSession('userid');
 	  $production_id 					= $this->createProductionID();
	  $requestdata['created_date']      = date('Y-m-d h:i:s');
	  if($production_id != -1)
      {
      	$requestdata['production_id']      = $production_id;
      }
      else
      {
      	$msg = "ID overflow !!!";
      	header("location:index.php?app=user_home&msg=$msg");
      	exit;
      }
	  $info        		=  array();
	  $info['table']	= PRODUCTION_MASTER_TBL;
	  $info['data'] 	= $requestdata;     
	  //$info['debug']  =  true;
	  $res = insert($info);
	  if($res)
      {
		$this->insertProductionDetails($production_id);	 
				
         header("location:index.php?app=used4_feed&cmd=print_report&production_id=".$production_id); 
      }else{
	  	 header("location:?app=used4_feed");
	  }    
	}

	function getProductionMasterInfo($id){		
		   
		$project_id     = getFromSession('project_id');  
		$info           = array();    
		$info['table']  = PRODUCTION_MASTER_TBL.' pm,'.PROJECT_TBL.' pa,'.PRODUCT_TBL.' p,'.CURRENCY_TBL.' c';	
		$info['fields'] = array('pm.production_id','pa.project_name','pa.location','pm.overhead_cost','pm.sold_cost','pm.total_value','pm.m_unit','p.product_name','pm.finish_qty',"DATE_FORMAT(pm.used_date,'%d %b %y' ) as used_date",'pm.production_type','c.curr_symble','pm.created_time');
		
		$sql="pm.finish_product = p.product_id AND pm.project_id = pa.project_id AND pm.currency = c.currency_id AND pm.project_id = '".$project_id."' AND pm.production_id = '$id'";
							
		$info['where']  =$sql;	  	
	    $info['groupby'] = array("pm.production_id");
		//$info['debug']  = true;
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
   
        
   function getProductionDetailsList($id) {  

		$info           = array();    
		$info['table']  =  PRODUCTION_DETAILS_TBL.' pd,'.PRODUCT_TBL.' p';	
		$info['fields'] = array('pd.qty','pd.m_unit','pd.amount','p.product_name','pd.created_time');
		
		$sql="pd.product = p.product_id AND pd.production_id = '$id'";
		
		$info['where']  = $sql;	  	
	    $info['groupby'] = array("pd.pro_detail_id");
		$info['orderby'] = array("pd.pro_detail_id asc");
		//$info['debug']  = true;
		$result         = select($info);
		$data           = array();
		$cnt = count($result);  	     
		
		if($cnt) {
			foreach($result as $value)  {				
			$data[]	= $value;	
			}
		} 
		
		return $data; 
   }  

  function getFinishProductList() {  

		$info           = array();    
		$info['table']  =  PRODUCT_TBL;	
		$sql="catagory  = 'C310007'"; // feed
		
		$info['where']  = $sql;	  	
	    $info['groupby'] = array("product_id");
		$info['orderby'] = array("product_name asc");
		//$info['debug']  = true;
		$result         = select($info);
		$data           = array();
		$cnt = count($result);  	     
		
		if($cnt) {
			foreach($result as $value)  {				
			$data[]	= $value;	
			}
		} 
		
		return $data; 
   }  
	function loadProduct4Catagory($catagory)
	{	  
		
	  	  $project_id = getFromSession('project_id');  
		  $info            = array();
		  $info['table']   = PRODUCT_TBL;
		  $info['fields']  =  array('product_id','product_name');
		  $info['where']   = "catagory = '$catagory' AND project_id = '$project_id'";
		  $info['groupby'] = array("product_id");
		  $info['debug']   = false;
	
		  $result          = select($info);
		  $data            = array();
	
		  if(count($result))
		  {
			 foreach($result as $key=>$value)
			 {
				$data[$key][]        = $value;
			 }
		  }
				
		  foreach($data as $i=>$v)
		  {
			 $subject_idname .= $v[0]->product_id.'-'.$v[0]->product_name.',';
		  }
		  echo $subject_idname;	
	}

   function getCurrencyList()
   {

      $info            = array();
      $info['table']   = CURRENCY_TBL;
      $info['debug']   = false;
      $result          = select($info);

      //dBug($result);

      $data            = array();

      if(count($result))
      {

         foreach($result as $i=>$v)
         {
            $data[$i] = $v;   
         }

      }

      return $data;

   } 

   function getCatagoryList()
   {	
	  $project_id  		= getFromSession('project_id');
      $data 			= array(); 
      $info        		= array();
      $info['table']	= CATAGORY_TBL;
	  $info['where']    = "project_id = '$project_id'";
      $res            	=	 select($info);      

      if(count($res))
      {
         foreach($res as $i=>$v)
         {
            $data[$i] = $v;             
         }
      }
	  //dumpVar($data);
      return $data;	
   }
    
   function getCashId($project_id){
		$sql = "SELECT sub_id FROM ".SUB_ACC_HEAD_TBL." WHERE head_type = 'Cash' AND project_id = '$project_id'";

		$row = mysql_fetch_object(mysql_query($sql));

		return $sub_id = $row->sub_id;
	}
	function getRecievableId($project_id){
		$sql = "SELECT sub_id FROM ".SUB_ACC_HEAD_TBL." WHERE head_type = 'Accounts Recievable' AND project_id = '$project_id'";

		$row = mysql_fetch_object(mysql_query($sql));

		return $sub_id = $row->sub_id;
	}
	function getPayableId($project_id){
		$sql = "SELECT sub_id FROM ".SUB_ACC_HEAD_TBL." WHERE head_type = 'Accounts Payable' AND project_id = '$project_id'";

		$row = mysql_fetch_object(mysql_query($sql));

		return $sub_id = $row->sub_id;
	}
    function getTotalCreditAmount($acc_head,$project_id){

   		$sql = "SELECT sum(`cr`) as credit_amount FROM ".ACCOUNT_JOURNAL_TBL." WHERE sub_id = '$acc_head' AND project_id = '$project_id'";

		$row = mysql_fetch_object(mysql_query($sql));

		$credit_amount = $row->credit_amount;

		if(empty($credit_amount)){

			$credit_amount = 0;

		}

		return $credit_amount;

   }
  
   function getTotalDebitAmount($acc_head,$project_id){

   		$sql = "SELECT sum(`dr`) as debit_amount FROM ".ACCOUNT_JOURNAL_TBL." WHERE sub_id = '$acc_head' AND project_id = '$project_id'";

		$row = mysql_fetch_object(mysql_query($sql));

		$debit_amount = $row->debit_amount;

		if(empty($debit_amount)){

			$debit_amount = 0;

		}

		return $debit_amount;

   } 
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
	function saveStockJournal($voucher_no,$project_id,$product_id,$unit_price=NULL,$m_unit,$DR=NULL,$CR=NULL,$balance,$create_date=NULL){
		$sql = "INSERT INTO ".STOCK_LEDGER_TBL." (voucher_no,project_id,product_id,unit_price,m_unit,dr,cr,balance,create_date) VALUES('".$voucher_no."','".$project_id."','".$product_id."','".$unit_price."','".$m_unit."','".$DR."','".$CR."','".$balance."','".$create_date."')";
		mysql_query($sql);
	}
       
   function createProductionID()
   {
      $info = array();
      $info['table'] = PRODUCTION_MASTER_TBL;
      $info['fields'] = array('max(production_id) as maxProduction');
      
      $res = select($info);
      
      $maxProductionId = 'P0000000';
      
      if(count($res))
      {
         foreach($res as $v)
         {
         	 if($v->maxProduction)
         	 {
             $maxProductionId = $v->maxProduction;
             }
             break;   	
         }
      
      }
      
      $maxProductionId = generateID("P",$maxProductionId,8);
      return $maxProductionId;
   }  

  function deleteRecord($id)
  {
   	  if(getRequest('id'))
      { 
      	$info = array();
      	$info['table'] = BANK_ACCOUNT_TBL;
      	$info['where'] = "purchase_no='$id'";
      	$info['debug'] = false;
      	$res = delete($info);      	

      	if($res)
      	{
      	  $msg="Successfully delete Record !!!";
          header("location:?app=bank_account&cmd=view&msg=$msg");     	   

      	} else{
      		 header("location:?app=bank_account&cmd=view&cmd=list&deleted=no");
      	}      	

      }

   } 
 //================= Start Sales Details ====================

function showEditor4SalesDetails($msg = null) {        

	  $data                				= array();
	  $data['cmd']         				= getRequest('cmd');
	  $data['record_list'] 				= $this->getSalesDetailsList(getRequest('from'),getRequest('to'));
	  $data['totalrecord']				= $this->getTotalSalesDetailsList(getRequest('from'),getRequest('to'));	
		
	   require_once(SALES_DETAILS_SKIN); 

	   return $data[0];

   }

	function getSalesDetailsList($from,$to) { 

		if($from == "" && $to == ""){$from=0; $to=500;}
		$date_from 			= formatDate(getRequest('date_from'));
		$date_to 			= formatDate(getRequest('date_to'));				
		$project_id     = getFromSession('project_id');  
		$info           = array();    
		$info['table']  = SALES_MASTER_TBL.' pm,'.SUB_ACC_HEAD_TBL.' s,'.PROJECT_TBL.' p,'.CURRENCY_TBL.' c';	
		$info['fields'] = array('pm.voucher_no','p.project_name','p.location','s.sub_id','s.sub_head_name','s.head_details','pm.gate_pass','pm.track_no','pm.salse_type','pm.total_value',"DATE_FORMAT(pm.sales_date,'%d %b %y' ) as sales_date",'pm.mode_of_payment','c.curr_symble','pm.bank_name','pm.acc_no','pm.check_no','pm.check_no','pm.discount','pm.net_payble','pm.paid_amount','pm.due','pm.ref_no','pm.created_date',"DATE_FORMAT(pm.created_date,'%d %b %y' ) as date");
		
		$sql="pm.customer = s.sub_id AND pm.project_id = p.project_id AND pm.currency = c.currency_id AND pm.project_id = '".$project_id."'";
							
		
		if($date_from!="" && $date_to ==""){

			$sql.=" AND pm.created_date >= '$date_from'";

		}elseif($date_from=="" && $date_to !=""){

			$sql.=" AND pm.created_date <= '$date_to'";

		}elseif($date_from!="" && $date_to !=""){

			$sql.=" AND pm.created_date BETWEEN '$date_from' AND '$date_to'";

		}

		$info['where']  =$sql;		

		$info['orderby'] = array("pm.voucher_no asc LIMIT $from,$to");

		//$info['debug']  = true;

		$result         = select($info);

		$data           = array();

		$cnt = count($result);  	     

		if($cnt) {

			foreach($result as $value)  {				

			$data[]	= $value;	

			}

		} 
	

		return $data; 

   } 

   function getTotalSalesDetailsList($from,$to) {  
		
		$date_from 			= formatDate(getRequest('date_from'));
		$date_to 			= formatDate(getRequest('date_to'));				
		$project_id     = getFromSession('project_id');  
		$info           = array();    
		$info['table']  = SALES_MASTER_TBL.' pm,'.SUB_ACC_HEAD_TBL.' s,'.PROJECT_TBL.' p,'.CURRENCY_TBL.' c';	
		$info['fields'] = array('pm.voucher_no');
		
		$sql="pm.customer = s.sub_id AND pm.project_id = p.project_id AND pm.currency = c.currency_id AND pm.project_id = '".$project_id."'";
							
		
		if($date_from!="" && $date_to ==""){

			$sql.=" AND pm.created_date >= '$date_from'";

		}elseif($date_from=="" && $date_to !=""){

			$sql.=" AND pm.created_date <= '$date_to'";

		}elseif($date_from!="" && $date_to !=""){

			$sql.=" AND pm.created_date BETWEEN '$date_from' AND '$date_to'";

		}
		$info['where']  =$sql;	
  

		$info['orderby'] 	= array("pm.created_date asc");

		//$info['debug']  	= true;

		$result         	= select($info);

		$data           	= array();     

	    $cnt = count($result);  	

      if($cnt) {

        return $cnt;

      } 

	  else {

	  return 0;

	 }    
      

   }      
//==================== End Sales Details =====================
   
} // End class


?>