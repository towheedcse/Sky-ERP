<?php
class PurchaseItemReceived
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
 		   case 'addlc'					: $this->showEditor(); break;
		   case 'edit'					: $this->showEditor(); break;
		   case 'pur_dtl'				: $this->showEditor4PurchaseDetails(); break;
      	   case 'loadProduct'  			: $this->loadProduct4Catagory(trim(getRequest('catagory_id'))); break; 
      	   case 'loadPOInfo'  			: $this->loadPOInfo(trim(getRequest('po_no'))); break;  
      	   case 'get_uprice'  			: $this->loadUnitePrice(trim(getRequest('product_id'))); break;  
		   case 'savePurchase'			: $this->saveReceivedItem(); break;
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
			$advArr 					= $this->getPurchaseMasterInfo($voucher_no);
			$advArr 					= parseThisValue($advArr); 
			$data   					= array_merge(array(), $advArr); 
		  
			$data['item_list']	= $this->getProductList($voucher_no);
			$data['message'] = $msg;
			$data['cmd']     = getRequest('cmd');
			require_once(PURCHASE_VOUCHAR_SKIN);      
			return true;
	   }else{
		require_once(PRINT_VOUCHAR_SKIN);
	   }
   }
     
   function showEditor($msg = null) {

   	   $data                	= array();
       
	   $data['supplier_list'] 	= $this->getSupplierList();	
	   $data['cat_list'] 		= $this->getCatagoryList();	
	   $data['currency_list']   = $this->getCurrencyList();
	 	
	   $data['cmd']         	= getRequest('cmd');
		if(getRequest('cmd')=="addlc"){       
			
      		require_once(CLASS_DIR.'/supplier.class.php');	
	  		$supApp = new Supplier(); 
        	$data['country_list']     = $supApp->getCountryList();
	   		require_once(LC_OPENING_SKIN); 
		}else{			
	   		require_once(CURRENT_APP_SKIN_FILE); 
		}
	   return $data[0];
   }

  function insertPurchaseDetails($voucher_no)
  {

		$requestdata 				= array();
		$arr_catagory_product_id	= array();

		$project_id  				= getFromSession('project_id');
      	$currency        			= getRequest('currency');

      	$arr_catagory_product_id	= getRequest('input_catagory_product_id');
      	$arr_m_unit        			= getRequest('input_m_unit');
      	$arr_total_unit        		= getRequest('input_total_unit');
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

		   foreach($arr_total_unit as $key => $val)
      	   {

      	   	  if($catagory_product_sep==$key)
      	   	  {
      		   	$requestdata['total_unit'] = $val;	
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
					 $requestdata['rec_qty'] = $val;	
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
		    $requestdata['voucher_no']        = getRequest('voucher_no');
		    $requestdata['po_no']        	  = getRequest('po_no');
		    $requestdata['lc_no']        	  = getRequest('lc_no');
			
		    $info        	=  array();
		    $info['table']	= PURCHASE_RECEIVED_TBL;
	  	    $info['data'] 	= $requestdata;      
			//dumpvar($info);	     
			    
		    //$info['debug']  	=  true;
	    	$res = insert($info);	
			$totalCR  = $this->getTotalCreditStock($product_id,getFromSession('project_id'));
			$totalDR  = $this->getTotalDebitStock($product_id,getFromSession('project_id'));					 
			$balance  = (($totalDR + $productQty) - $totalCR);	
			$purchase_date = formatDate(getRequest('purchase_date')); 
			$sql = "UPDATE ".PURCHASE_DETAILS_TBL." SET rec_qty='".$productQty."' WHERE voucher_no='".getRequest('voucher_no')."' AND ";
			$sql.=" po_no='".getRequest('po_no')."' AND product='".$product_id."'";
			mysql_query($sql);
			
			$total_received_value 	= getRequest('total_value');
			$exceed_received_amount = getRequest('exceed_received_amount');
			$actual_received_amount = $total_received_value-$exceed_received_amount;
			$PMsql = "SELECT voucher_no, net_payble,paid_amount,due,item_received_amount FROM ".PURCHASE_MASTER_TBL." WHERE po_no ='".getRequest('po_no')."' AND project_id = '$project_id'";
			$PMrow 			= mysql_fetch_object(mysql_query($PMsql));
			$paid_amount 	= $PMrow->paid_amount;
			$existing_due 	= $PMrow->due;
			$item_received_amount 	= $PMrow->item_received_amount;
			$total_received_amount 	= ($actual_received_amount+$item_received_amount);
			//$present_due 			= ($total_received_amount - $existing_due); old
			$present_due 			= ($total_received_amount - $paid_amount); // new
			$PMUpdate = "UPDATE ".PURCHASE_MASTER_TBL." SET due = '$present_due', item_received_amount = '$total_received_amount' WHERE po_no ='".getRequest('po_no')."' AND project_id = '$project_id' AND  voucher_no='".getRequest('voucher_no')."'";
			mysql_query($PMUpdate);
			
			$Prosql = "SELECT product_type FROM ".PRODUCT_TBL." WHERE product_id = '$product_id' AND project_id = '$project_id'";
			$Prorow = mysql_fetch_object(mysql_query($Prosql));
			$product_type 		= $Prorow->product_type;
			$equipment_auto_out = getFromSession('equipment_auto_out');  
			$po_no 				= getRequest('po_no');
			if($equipment_auto_out==1 && $product_type=="Equipment"){				
				$this->saveStockJournal($po_no,$voucher_no,$project_id,$product_id,$product_type,$requestdata['unit_price'],$requestdata['m_unit'],$productQty,0,$balance,$purchase_date);
				$Autobalance  = ($totalDR - ($totalCR+$productQty));
				$this->saveStockJournal($po_no,$voucher_no,$project_id,$product_id,$product_type,$requestdata['unit_price'],$requestdata['m_unit'],0,$productQty,$Autobalance,$purchase_date);
			}	
					
			$inventory_auto_out = getFromSession('inventory_auto_out'); 
			if($inventory_auto_out==1 && $product_type=="Invetory Item"){				
				$this->saveStockJournal($po_no,$voucher_no,$project_id,$product_id,$product_type,$requestdata['unit_price'],$requestdata['m_unit'],$productQty,0,$balance,$purchase_date);
				$Autobalance  = ($totalDR - ($totalCR+$productQty));
				$this->saveStockJournal($po_no,$voucher_no,$project_id,$product_id,$product_type,$requestdata['unit_price'],$requestdata['m_unit'],0,$productQty,$Autobalance,$purchase_date);
			}elseif($inventory_auto_out==0 && $product_type=="Invetory Item"){	
			$this->saveStockJournal($po_no,$voucher_no,$project_id,$product_id,$product_type,$requestdata['unit_price'],$requestdata['m_unit'],$productQty,0,$balance,$purchase_date);
			}
			
			if($product_type=="Sales Item" || $product_type=="Raw Materials"){	
			$this->saveStockJournal($po_no,$voucher_no,$project_id,$product_id,$product_type,$requestdata['unit_price'],$requestdata['m_unit'],$productQty,0,$balance,$purchase_date);
			}
	   } 

  } //End of the function savePaymentDetails()
	function saveReceivedItem(){
		$voucher_no = getRequest("voucher_no");		
		$this->insertPurchaseDetails($voucher_no);
		header("location:index.php?app=purchase_item_received&cmd=print_vouchar&voucher_no=".$voucher_no);	 
	
	}//EOFn  

	
	function getPurchaseMasterInfo($id){		
		   
		$project_id     = getFromSession('project_id');  
		$info           = array();    
		$info['table']  = PURCHASE_MASTER_TBL.' pm,'.SUPPLIER_TBL.' s,'.PROJECT_TBL.' p,'.CURRENCY_TBL.' c';	
		$info['fields'] = array('pm.voucher_no','pm.voucher_no','pm.po_no','p.location','s.name','s.address','pm.quotation_no','pm.lc_no','pm.lcopener','pm.lcopening_bank',"DATE_FORMAT(pm.lcopening_date,'%d %b %y' ) as lcopening_date",'pm.country','pm.lc_details','pm.track_no','pm.van_no','pm.total_value',"DATE_FORMAT(pm.purchase_date,'%d %b %y' ) as purchase_date",'pm.mode_of_payment','c.curr_symble','pm.bank_name','pm.acc_no','pm.check_no','pm.check_no','pm.discount','pm.net_payble','pm.paid_amount','pm.due','pm.ref_no','pm.created_date');
		
		$sql="pm.supplier = s.supplier_code AND pm.project_id = p.project_id AND pm.currency = c.currency_id AND pm.project_id = '".$project_id."' AND pm.voucher_no = '$id'";
							
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
		$info['table']  =  PURCHASE_DETAILS_TBL.' pd,'.PRODUCT_TBL.' p';	
		$info['fields'] = array('pd.pur_detail_id','pd.voucher_no','pd.project_id','pd.catagory','pd.product','p.product_name','pd.m_unit','pd.unit_price','pd.qty','pd.total_bag','pd.total','pd.created_time');
		
		$sql="pd.product = p.product_id AND pd.voucher_no = '$id'";
		
		$info['where']  = $sql;	  	
	    $info['groupby'] = array("pd.pur_detail_id");
		$info['orderby'] = array("pd.product asc");
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
		  //$info['debug']   = true;
	
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
			 $subject_idname .= $v[0]->product_id.'#####'.$v[0]->product_name.'@@@';
		  }
		  echo $subject_idname;	
	}  

	function loadPOInfo($po_no)
	{	  
		
	  	  $project_id 	   = getFromSession('project_id');  
		  $info            = array();
		  $info['table']   = PURCHASE_MASTER_TBL." pm, ".SUPPLIER_TBL." s";
		  $info['fields']  = array('s.name','s.address','s.supplier_code','pm.voucher_no','pm.purchase_date');
		  $info['where']   = "pm.supplier=s.supplier_code AND pm.po_no = '$po_no' AND pm.project_id = '$project_id'";
		  $info['groupby'] = array("po_no");
		  //$info['debug'] = true;
	
		  $result          = select($info);
		  $data            = array();
	
		  if(count($result))
		  {
			 foreach($result as $key=>$value)
			 {
				$data[$key][]= $value;
			 }
		  }
				
		  foreach($data as $i=>$v)
		  {
			 $subject_idname.= $v[0]->name.",".$v[0]->address.'#####'.$v[0]->supplier_code.'#####'.$v[0]->voucher_no;
		  }
		  echo $subject_idname;	
	}
	function loadUnitePrice($product_id){
		  $project_id = getFromSession('project_id');  		 
		  $info            = array();
		  $info['table']   = PURCHASE_DETAILS_TBL;
		  $info['fields']  =  array('m_unit','unit_price','qty');
		  $info['where']   = "po_no = '".$_REQUEST['po_no']."' AND product = '$product_id' AND project_id = '$project_id'";
		  $info['groupby'] = array("po_no");
		  //$info['debug']   = true;
	
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
			 $unit_price.= $v[0]->m_unit."###".$v[0]->unit_price;
		  }
		  echo $unit_price;	
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
 
 
  function getSupplierList()
  {	
	  $project_id  		= getFromSession('project_id');
      $data 			= array(); 
      $info        		= array();
      $info['table']	= SUPPLIER_TBL;
	  $info['where']   = "project_id = '$project_id'";
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
   function saveAccountJournal($voucher_no,$sub_id,$head_type,$project_id,$description,$DR=NULL,$CR=NULL,$balance,$status=NULL,$purchare_date=NULL){		
		$sql = "INSERT INTO ".ACCOUNT_JOURNAL_TBL." (voucher_no,created_date,sub_id,head_type,project_id,description,dr,cr,balance,status) VALUES('".$voucher_no."','".$purchare_date."','".$sub_id."','".$head_type."','".$project_id."','".$description."','".$DR."','".$CR."','".$balance."','".$status."')";
		mysql_query($sql);
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
	function saveStockJournal($po_no,$voucher_no,$project_id,$product_id,$product_type,$unit_price=NULL,$m_unit,$DR=NULL,$CR=NULL,$balance,$create_date=NULL){
		$sql = "INSERT INTO ".STOCK_LEDGER_TBL." (po_no,voucher_no,project_id,product_id,product_type,unit_price,m_unit,dr,cr,balance,create_date) VALUES('".$po_no."','".$voucher_no."','".$project_id."','".$product_id."','".$product_type."','".$unit_price."','".$m_unit."','".$DR."','".$CR."','".$balance."','".$create_date."')";
		mysql_query($sql);
	}
   function createVoucharID()
   {
      $info = array();
      $info['table'] = DEVIT_VOUCHAR_TBL;
      $info['fields'] = array('max(voucher_no) as maxvoucher');
      $res = select($info);
      $maxvoucherId = 'D00000';
      if(count($res))
      {
         foreach($res as $v)
         {
         	 if($v->maxvoucher)
         	 {
             $maxvoucherId = $v->maxvoucher;
             }
             break;   	
         }
      
      }
      $maxvoucherId = generateID("D",$maxvoucherId,6);
      return $maxvoucherId;
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
 //====================== Start Purchase Details ===============

  function showEditor4PurchaseDetails($msg = null) {        

	  $data                				= array();
	  $data['cmd']         				= getRequest('cmd');
	  $data['record_list'] 				= $this->getPurchaseDetailsList(getRequest('from'),getRequest('to'));
	  $data['totalrecord']				= $this->getTotalPurchaseDetailsList(getRequest('from'),getRequest('to'));	
	  require_once(PURCHASE_DETAILS_SKIN); 
	  return $data[0];

   }
 function getPurchaseDetailsList($from,$to) { 

		if($from == "" && $to == ""){$from=0; $to=500;}
		$date_from 			= formatDate(getRequest('date_from'));
		$date_to 			= formatDate(getRequest('date_to'));				
		$project_id     = getFromSession('project_id');  
		$info           = array();    
		$info['table']  = PURCHASE_MASTER_TBL.' pm,'.SUPPLIER_TBL.' s,'.PROJECT_TBL.' p,'.CURRENCY_TBL.' c';	
		$info['fields'] = array('pm.voucher_no','p.project_name','p.location','s.supplier_code','s.name','s.address','pm.quotation_no','pm.lc_no','pm.lcopener','pm.lcopening_bank',"DATE_FORMAT(pm.lcopening_date,'%d %b %y' ) as lcopening_date",'pm.country','pm.lc_details','pm.track_no','pm.van_no','pm.total_value',"DATE_FORMAT(pm.purchase_date,'%d %b %y' ) as purchase_date",'pm.mode_of_payment','c.curr_symble','pm.bank_name','pm.acc_no','pm.check_no','pm.check_no','pm.discount','pm.net_payble','pm.paid_amount','pm.due','pm.ref_no',"DATE_FORMAT(pm.created_date,'%d %b %y' ) as date",'pm.created_date');
		
		$sql="pm.supplier = s.supplier_code AND pm.project_id = p.project_id AND pm.currency = c.currency_id AND pm.project_id = '".$project_id."'";
									
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

   function getTotalPurchaseDetailsList($from,$to) {  

		$date_from 			= formatDate(getRequest('date_from'));
		$date_to 			= formatDate(getRequest('date_to'));				
		$project_id     = getFromSession('project_id');  
		$info           = array();    
		$info['table']  = PURCHASE_MASTER_TBL.' pm,'.SUPPLIER_TBL.' s,'.PROJECT_TBL.' p,'.CURRENCY_TBL.' c';	
		$info['fields'] = array('pm.voucher_no');
		
		$sql="pm.supplier = s.supplier_code AND pm.project_id = p.project_id AND pm.currency = c.currency_id AND pm.project_id = '".$project_id."'";
									
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
	function savePayableCheck($voucher_no,$transaction_type,$paid_amount){
	 $requestdata = array();

	  $requestdata = getUserDataSet(PAYABLE_CHECK_TBL);	
	  $requestdata['check_no'] 			= getRequest('check_no');
	  $requestdata['check_issue_date']  = formatDate(getRequest('check_issue_date')); 
	  $requestdata['created_date']      = formatDate(getRequest('purchase_date'));
	  $requestdata['acc_head'] 			= getRequest('supplier'); 
	  $requestdata['head_type'] 		= "Check"; 
	  $requestdata['voucher_no']        = $voucher_no;  
	  $requestdata['paid_amount']  		= $paid_amount;   
	  $requestdata['transaction_type']  = $transaction_type;   
	  $requestdata['project_id']        = getFromSession('project_id');    
	  $requestdata['created_by']        = getFromSession('userid');

	  $info        		=  array();
	  $info['table']	= PAYABLE_CHECK_TBL;
	  $info['data'] 	= $requestdata;     
	  //$info['debug']  	=  true;
	  $res = insert($info);
		
	}
   
} // End class
?>