<?php
/**
 * File: flat_sales.class.php
 * This application is used to authenticate users
 *
 */
require_once('journal.class.php');
class FlatSales extends Journal
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
		   case 'savePurchase'			: $this->saveDebitVouchar(); break;
		   case 'print_vouchar'			: $screen = $this->showPrintEditor($msg); break;  
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
      
	  $voucher_no 	= getRequest('voucher_no');  
	  if ($voucher_no) {
         	$advArr 					= $this->getSalesMasterInfo($voucher_no);
         	$advArr 					= parseThisValue($advArr); 
			$data   					= array_merge(array(), $advArr); 
      
		  $data['item_list']	= $this->getProductList($voucher_no);
		  $data['message'] = $msg;
		  $data['cmd']     = getRequest('cmd');
		  require_once(SALES_VOUCHAR_SKIN);      
		  return true;
	 }else{
		require_once(PRINT_VOUCHAR_SKIN);
	  }
   }
     
   function showEditor($msg = null) {
      
   	   $data                	= array();
       
	   $data['customer_list'] 	= $this->getCustomerList();	
	   $data['cat_list'] 		= $this->getCatagoryList();	
	   $data['currency_list']   = $this->getCurrencyList();
	 	if(getRequest('submit')){
  			//$this->insertSalesMaster();
		}
	   $data['cmd']         	= getRequest('cmd');   
	   require_once(CURRENT_APP_SKIN_FILE); 
	   return $data[0];
   }

  function insertSalesDetails($voucher_no)
  {

		$requestdata 				= array();
		$arr_catagory_product_id	= array();

		$project_id  				= getFromSession('project_id');
      	$currency        			= getRequest('currency');

      	$arr_catagory_product_id	= getRequest('input_catagory_product_id');
      	$arr_m_unit        			= getRequest('input_m_unit');
      	$arr_unit_price				= getRequest('input_unit_price');
      	$arr_qty      				= getRequest('input_qty');
      	$arr_total_bag      		= getRequest('input_total_bag');
      	$arr_currency     			= getRequest('input_currency');
      	$arr_total_value       		= getRequest('input_total_value');

      

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
      		    $requestdata['product'] =$val;	
				$product_id				=$val;
      	   }
	   

		   foreach($arr_m_unit as $key => $val)
      	   {

      	   	  if($catagory_product_sep==$key)
      	   	  {
      		   	$requestdata['m_unit'] = $val;	
      		  }

      	   }
     	         	  
		   foreach($arr_unit_price as $key => $val)
      	   {
      	   	  if($catagory_product_sep==$key)
      	   	  {
      		   	$requestdata['unit_price'] = $val;	
      		  }
      	   }

      	   foreach($arr_qty as $key => $val)
      	   {
				if($catagory_product_sep==$key)
				{
					 $requestdata['qty'] = $val;	
					 $productQty		 = $val;
				}
      	   }

      	   foreach($arr_total_bag as $key => $val)
      	   {
				if($catagory_product_sep==$key)
				{
					 $requestdata['total_bag'] = $val;	
				}
      	   }

      	   foreach($arr_currency as $key => $val)
      	   {
				if($catagory_product_sep==$key)
				{
      		     $requestdata['currency'] = $val;	
      		  	}
      	   }
		
      	   foreach($arr_total_value as $key => $val)
      	   {
      	   	  if($catagory_product_sep==$key)
      	   	  {
      		     $requestdata['total'] = $val;	
      		  }

      	   }
			//$rowCnt = 0;
		    $requestdata['created_by'] 		  = getFromSession('userid');
		    $requestdata['created_date']      = date('Y-m-d h:i:s');  
			$project_id						  = getFromSession('project_id'); 
		    $requestdata['project_id']        = $project_id;
		    $requestdata['voucher_no']        = $voucher_no;
 
		    $info        		=  array();
		    $info['table']	= SALES_DETAILS_TBL;
	  	    $info['data'] 	= $requestdata;      
			//echo "<br>";
			//dumpvar($info);	     
			    
		     $info['debug']  	=  true;
	    	 $res = insert($info);	
			 if(getRequest('salse_type')=="Sales"){
				 $totalCR  = $this->getTotalCreditStock($product_id,getFromSession('project_id'));
				 $totalDR  = $this->getTotalDebitStock($product_id,getFromSession('project_id'));					 
				 $balance  = ($totalDR - ($totalCR+$productQty));						
				 $this->saveStockJournal($voucher_no,$project_id,$product_id,$requestdata['unit_price'],$requestdata['m_unit'],0,$productQty,$balance);
				//header("location:index.php?app=sales&cmd=print_vouchar&voucher_no=".$voucher_no); 
			}
	   }
 

  } //End of the function savePaymentDetails()

//============== New ==============
 //==================== saveDebitVouchar ====================
 	function saveDebitVouchar()
 	{     
 	   	  $mode_of_payment = getRequest('mode_of_payment');
		  
		  $requestdata = array();
	
		  $requestdata = getUserDataSet(DEVIT_VOUCHAR_TBL);	
		  if($mode_of_payment =="Check"){
			$requestdata['bank_name'] 			= getRequest('bank_name');
			$requestdata['acc_no'] 				= getRequest('acc_no');
			$requestdata['check_no'] 			= getRequest('check_no');
			$requestdata['check_issue_date'] 	= formatDate(getRequest('check_issue_date'));
    
		  	$requestdata['account_head']     	= getRequest('acc_no'); 
		  	$requestdata['debit']        		= getRequest('paid_amount');  
		  	$requestdata['credit']        		=  0;  
		  	$requestdata['head_type']     		= "Check";   
		  }else{
			$requestdata['bank_name'] = "";
			$requestdata['acc_no'] = "";
			$requestdata['check_no'] = "";
			$requestdata['check_issue_date'] = "";

			$requestdata['account_head']     	= $this->getCashId(getFromSession('project_id')); 
		  	$requestdata['debit']        		= getRequest('paid_amount'); 
		  	$requestdata['credit']        		= 0;     
		  	$requestdata['head_type']     		= "Acc";   
		  }
  
		  $requestdata['transaction_type']  = "Received";
		  $requestdata['project_id']        = getFromSession('project_id');    
		  $requestdata['created_by']        = getFromSession('userid'); 
	
		  $requestdata['created_date']      = date('Y-m-d h:i:s');
	
		  $voucher_no = $this->createVoucharID();
	
		 if($voucher_no != -1)
		  {
			$requestdata['voucher_no']   	= $voucher_no;
		  }
		  else
		  {
			$msg = "ID overflow !!!";
			header("location:index.php?app=user_home&msg=$msg");
			exit;
		  }
	 
		  $info        		=  array();
		  $info['table']	= DEVIT_VOUCHAR_TBL;
		  $info['data'] 	= $requestdata;     
		  //$info['debug']  	=  true;
		  $res = insert($info);
			
	
		  if($res['affected_rows']) {
			$this->saveCreditVouchar($voucher_no);
		  }else {	
			header("location:index.php?app=sales&cmd=add");	
		  }  
	 

    }//EOFn  

    function saveCreditVouchar($voucher_no)
 	{     
 	   	  $mode_of_payment = getRequest('mode_of_payment');
		  
		  $requestdata = array();
	
		  $requestdata = getUserDataSet(CREDIT_VOUCHAR_TBL);
		if($mode_of_payment =="Check"){
			$requestdata['bank_name'] 			= getRequest('bank_name');
			$requestdata['acc_no'] 				= getRequest('acc_no');
			$requestdata['check_no'] 			= getRequest('check_no');
			$requestdata['check_issue_date'] 	= formatDate(getRequest('check_issue_date'));
		  }else{
			$requestdata['bank_name'] = "";
			$requestdata['acc_no'] = "";
			$requestdata['check_no'] = "";
			$requestdata['check_issue_date'] = "";
		  }
	 	  $requestdata['transaction_type']  = "Received";     
		  $requestdata['account_head']      = getRequest('customer'); 
		  $requestdata['credit']        	= getRequest('paid_amount');     
		  $requestdata['head_type']     	= "Acc"; 
		  $requestdata['project_id']        = getFromSession('project_id');    
		  $requestdata['created_by']        = getFromSession('userid'); 
	
		  $requestdata['created_date']      = date('Y-m-d h:i:s');	
		  $requestdata['voucher_no']   	= $voucher_no;
		 
		  $info        		=  array();
		  $info['table']	= CREDIT_VOUCHAR_TBL;
		  $info['data'] 	= $requestdata;     
		  $info['debug']  	=  true;
		  $res = insert($info);
			
	
		  if($res['affected_rows']) {
			 $DrAmount = getRequest('paid_amount');
    		 $due = getRequest('due');
			 if($mode_of_payment=="Cash"){ 
				if(getRequest('due')>0){
					 //=========== Receivable Dr ========
					 $fullReceivable		= getRequest('net_payble');
					 $receivable_head 		= $this->getRecievableId(getFromSession('project_id'));
					 $totalReceivableCR  	= $this->getTotalCreditAmount($receivable_head,getFromSession('project_id'));
					 $totalReceivableDR  	= $this->getTotalDebitAmount($receivable_head,getFromSession('project_id'));					 
					 $receivableBalance  	= (($totalReceivableDR+$fullReceivable)-$totalReceivableCR);					 
					 $this->saveAccountJournal($voucher_no,$receivable_head,"Acc",getFromSession('project_id'),getRequest('description'),$fullReceivable,0,$receivableBalance,1);	
					//========= Receivable Cr ==========	
					 $Receivable	= getRequest('paid_amount');
					 $rblAcc_head = $this->getRecievableId(getFromSession('project_id'));
					 $totalRblCR  = $this->getTotalCreditAmount($rblAcc_head,getFromSession('project_id'));
					 $totalRblDR  = $this->getTotalDebitAmount($rblAcc_head,getFromSession('project_id'));					 
					 $rblBalance  = ($totalRblDR-($totalRblCR+$Receivable));					 
					 $this->saveAccountJournal($voucher_no,$rblAcc_head,"Acc",getFromSession('project_id'),getRequest('description'),0,$Receivable,$rblBalance,1);	//before 0
			
					//============== Cash Dr ===============
					 $acc_head = $this->getCashId(getFromSession('project_id'));
					 $totalCR  = $this->getTotalCreditAmount($acc_head,getFromSession('project_id'));
					 $totalDR  = $this->getTotalDebitAmount($acc_head,getFromSession('project_id'));					 
					 $balance  = (($totalDR+$DrAmount)-$totalCR);					 
					 $this->saveAccountJournal($voucher_no,$acc_head,"Acc",getFromSession('project_id'),getRequest('description'),$DrAmount,0,$balance,1);	
					 
					//======== Party Cr ============== 
					/*
					$due = getRequest('due');
				 	$PartyAcc_head = getRequest('customer'); 
					$totalPartyCR  = $this->getTotalCreditAmount($PartyAcc_head,getFromSession('project_id'));
					$totalPartyDR  = $this->getTotalDebitAmount($PartyAcc_head,getFromSession('project_id'));					 
					$PartyBalance  = (($totalPartyDR+$due)-$totalPartyCR);					 
					$this->saveAccountJournal($voucher_no,$PartyAcc_head,"Acc",getFromSession('project_id'),getRequest('description'),$due,0,$PartyBalance,1);
					*/
					//============= Done ============		
				}elseif(getRequest('due')==0){		
					//======= Party Cr ======			
			 		$CrAmount1 = getRequest('paid_amount');
				 	$PartyAcc_head = getRequest('customer');  
					$totalPartyCR  = $this->getTotalCreditAmount($PartyAcc_head,getFromSession('project_id'));
					$totalPartyDR  = $this->getTotalDebitAmount($PartyAcc_head,getFromSession('project_id'));					 
					$PartyBalance  = ($totalPartyDR-($totalPartyCR+$CrAmount1));					 
					$this->saveAccountJournal($voucher_no,$PartyAcc_head,"Acc",getFromSession('project_id'),getRequest('description'),0,$CrAmount1,$PartyBalance,1);	

					//============== Cash Dr ===============
					$DrAmount1 = $CrAmount1;
					$acc_head = $this->getCashId(getFromSession('project_id'));
				 	$totalCR  = $this->getTotalCreditAmount($acc_head,getFromSession('project_id'));
				 	$totalDR  = $this->getTotalDebitAmount($acc_head,getFromSession('project_id'));					 
				 	$balance  = (($totalDR+$DrAmount1)-$totalCR);					 
				 	$this->saveAccountJournal($voucher_no,$acc_head,"Acc",getFromSession('project_id'),getRequest('description'),$DrAmount1,0,$balance,1);	
					 
				}
				// header("location:index.php?app=sales&cmd=print_vouchar&voucher_no=".$voucher_no);
			 }elseif($mode_of_payment=="Check"){
				//====== save payable_check ======
				require_once(CLASS_DIR.'/journal.class.php');	
	   			$journalApp = new Journal(); 
				$journalApp->savePayableCheck($voucher_no,"Received",getRequest('paid_amount'));
			}			
			$this->insertSalesMaster($voucher_no);
			$this->insertSalesDetails($voucher_no);
			header("location:index.php?app=sales&cmd=print_vouchar&voucher_no=".$voucher_no);
		  }else {	
			header("location:index.php?app=sales&cmd=add");
	
		  }  
	 

    }//EOFn  
	
/* ======= End New ===========*/

	function insertSalesMaster($voucher_no){
    	 $requestdata = array();
	
		  $requestdata = getUserDataSet(SALES_MASTER_TBL);	
		  if($mode_of_payment =="Check"){
			$requestdata['check_no'] = formatDate(getRequest('check_no'));
			$requestdata['check_issue_date'] = formatDate(getRequest('check_issue_date'));
		  }
		  $requestdata['transaction_type']  = "Received";  
		  $requestdata['sales_date'] 		= formatDate(getRequest('sales_date')); 
		  $requestdata['voucher_no']        = $voucher_no;   
		  $requestdata['project_id']        = getFromSession('project_id');    
		  $requestdata['created_by']        = getFromSession('userid');
	
		  $requestdata['created_date']      = date('Y-m-d h:i:s');
	
		  $info        		=  array();
		  $info['table']	= SALES_MASTER_TBL;
		  $info['data'] 	= $requestdata;     
		  //$info['debug']  =  true;
		  $res = insert($info);
			
	}
	function updatePurchaseItem()
	{     	    
	
	}//EOFn 
	function getSalesMasterInfo($id){		
		   
		$project_id     = getFromSession('project_id');  
		$info           = array();    
		$info['table']  = SALES_MASTER_TBL.' pm,'.SUB_ACC_HEAD_TBL.' s,'.PROJECT_TBL.' p,'.CURRENCY_TBL.' c';	
		$info['fields'] = array('pm.voucher_no','p.project_name','p.location','s.sub_head_name','s.head_details','pm.gate_pass','pm.track_no','pm.salse_type','pm.total_value',"DATE_FORMAT(pm.sales_date,'%d %b %y' ) as sales_date",'pm.mode_of_payment','c.curr_symble','pm.bank_name','pm.acc_no','pm.check_no','pm.check_no','pm.discount','pm.net_payble','pm.paid_amount','pm.due','pm.ref_no','pm.created_date');
		
		$sql="pm.customer = s.sub_id AND pm.project_id = p.project_id AND pm.currency = c.currency_id AND pm.project_id = '".$project_id."' AND pm.voucher_no = '$id'";
							
		$info['where']  =$sql;	  	
	    $info['groupby'] = array("pm.voucher_no");
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
   
        
   function getProductList($id) {  

		$info           = array();    
		$info['table']  =  SALES_DETAILS_TBL.' sd,'.PRODUCT_TBL.' p';	
		$info['fields'] = array('sd.sal_detail_id','sd.voucher_no','sd.project_id','sd.catagory','sd.product','p.product_name','sd.m_unit','sd.unit_price','sd.qty','sd.total_bag','sd.total','sd.created_time');
		
		$sql="sd.product = p.product_id AND sd.voucher_no = '$id'";
		
		$info['where']  = $sql;	  	
	    $info['groupby'] = array("sd.sal_detail_id");
		$info['orderby'] = array("sd.product asc");
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

	function getBankAccountList($purchase_no=null)
	{
	   if($from == "" && $to == ""){$from=0; $to=40;}  
	   $data            = array();	  
	   $info            = array();
	   $info['table']   = BANK_ACCOUNT_TBL.' ba,'.BANK_TBL.' b';	
	   $info['fields'] = array('ba.bank_code','b.bank_name','ba.purchase_no','ba.account_name','ba.account_type','ba.phone','ba.fax');
	   if($purchase_no!=""){				
			$info['where']   = "ba.bank_code = b.bank_id AND ba.purchase_no = '".$purchase_no."'";
	   }else{
			$info['where']   = "ba.bank_code = b.bank_id";
	   }    
	   $info['orderby'] = array("ba.purchase_no asc LIMIT $from,$to");
	   $info['debug']   = false;			 
	
	   $res            =	select($info);   
	
	   if(count($res))
	   {
		  foreach($res as $i=>$v)
		  {
			 $data[$i] = $v;
		  }
	   }
	   if($purchase_no==""){
		return $data; // for list
	  }else{
		return $data[0];	// for view
	  }
	
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
      $info        		=  array();
      $info['table']	= CATAGORY_TBL;
	  $info['where']   = "project_id = '$project_id'";
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
 
 
  function getCustomerList()
  {		  
	  $project_id  		= getFromSession('project_id');
      $data 			= array(); 
      $info        		= array();
      $info['table']	= SUB_ACC_HEAD_TBL;
	  $info['where']  	= "head_type = 'Customer' AND project_id='".$project_id."'";	  	
      $res            	= select($info);      

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