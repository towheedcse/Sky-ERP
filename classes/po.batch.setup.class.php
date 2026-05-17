<?php
class BatchSetup
{
   
   function run()
   {         
      $cmd 		= getRequest('cmd');
      $u_t_id 		= getFromSession('u_type_id');
        
      if( ($u_t_id ==101) || ($u_t_id == 102) || ($u_t_id == 103) || ($u_t_id == 105)) // 101 = sysadmin, 102 = admin, 103= salesman, 105=store
      {

      	switch ($cmd)
      	{
      	   case 'add'			: $this->showEditor(); break;
	   case 'edit'			: $this->showEditEditor(); break;
	   case 'sal_dtl'		: $this->showEditor4SalesDetails(); break;
	   case 'admin_sal_dtl'		: $this->showAllCompaniesSalesDetails(); break;
      	   case 'loadProduct'  		: $this->loadProduct4Catagory(trim(getRequest('catagory_id'))); break;
      	   case 'get_dtl'  		: $this->loadProductDtl(trim(getRequest('product_id'))); break;  
      	   case 'save_tmp'  		: $this->saveTempSales(); break;   
	   case 'deltemp'		: $this->delTempSales(); break;    
	   case 'save_batch'		: $this->saveSalesItem(); break; 
	   case 'print_batch'		: $this->showPrintEditor($msg); break;  
	   case 'delete'             	: $this->DeleteStockTransfer(getRequest('id')); break;	 
	   case 'load_stock'            : $this->loadProductStock(trim(getRequest('product_id')));break; 
	   case 'loadstockqty'  	: $this->loadProductStockQty(trim(getRequest('product_id'))); break;
      	   default                   	: $cmd = 'list'; $screen = $this->showEditor();   break; 
      	}
      }elseif($u_t_id == 104) // 104 = acc
      {
      	switch ($cmd)
      	{
	   case 'sal_dtl'	: $this->showEditor4SalesDetails(); break;
	   case 'print_batch'	: $screen = $this->showPrintEditor($msg); break;
      	   default              : $cmd = 'list'; $screen = $this->showEditor();   break;
      	}

      }elseif($u_t_id == 107) // 104 = acc
      {
      	switch ($cmd)
      	{
	   case 'delete'        : $this->DeleteStockTransfer(getRequest('id')); break;	 
      	   default              : $cmd = 'list'; $screen = $this->showEditor();   break;
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
   //======= Start Edit Transfer Order =======   
   function showEditEditor($msg = null) {   	  
	  $transfer_no 	= getRequest('transfer_no');
	  require_once(CLASS_DIR.'/common.list.class.php');	
	  $comListApp 	= new CommonList(); 
	  if(getRequest('submit')) {
		$this->updateStockTransfer();
	  }
	  if(getRequest('tid') >0) {
		$this->deleteItem(getRequest('tid')); 
	  } 
	  if ($transfer_no) {
         	 $advArr 		= $this->getTransferMasterInfo($transfer_no);
         	 $advArr 		= parseThisValue($advArr); 
		 $data   		= array_merge(array(), $advArr); 
		 $data['item_list']	= $this->getProductList($transfer_no);
		 $data['brand_list']	= $comListApp->getBrandList();	   
	   	 $data['product_list'] 	= $comListApp->getProductList();  	
	   	 $data['depo_list'] 	= $comListApp->getDeliveryPointList(); 
		 $data['message'] 	= $msg;
		 $data['cmd']     	= getRequest('cmd');
		 require_once(EDIT_STOCK_TRANSFER_SKIN);      
		 return true;
	 }
   }

   function updateStockTransfer(){
   	$transfer_no 	= getRequest('transfer_no'); $delivery_point  = getRequest('delivery_point'); 
	$ttlfield 	= getRequest('ttlfield');    $transfer_from   = getRequest('transfer_from'); 
	$transfer_date  = formatDate(getRequest('transfer_date')); 
	$total_amount   = getRequest('total_amount'); 
	$updated_by 	= getFromSession('userid');
	$updated_time   = date('Y-m-d h:i:s');  $currency = getRequest('currency'); 
	$project_id 	= getFromSession('project_id'); 
	if($transfer_no !=""){	
	$j=1;
	for($j; $j<$ttlfield; $j++){
		$transfer_id    = getRequest("old_id$j"); 
		$catagory 	= getRequest("catagory$j"); 
		$brand_id 	= getRequest("brand$j");  
		$product 	= getRequest("product$j"); 
		$m_unit 	= getRequest("m_unit$j"); 
		$unit_price 	= getRequest("unit_price$j");
		$qty 		= getRequest("qty$j");				
		$total 		= ($unit_price*$qty); 
		$Pcsql="SELECT catagory,brand_code,m_unit FROM ".PRODUCT_TBL." WHERE product_id='$product' AND project_id='$project_id'";
		$Pcrow = mysql_fetch_object(mysql_query($Pcsql));
		$m_unit 	= $Pcrow->m_unit;
		$brand_id 	= $Pcrow->brand_code;
		$catagory 	= $Pcrow->catagory;

		if($transfer_id >0 && $qty >0){
		$usql = "UPDATE ".STOCK_TRANSFER_DETAILS_TBL." SET transfer_from='$transfer_from', 	delivery_point='$delivery_point',catagory='$catagory',brand_id='$brand_id',product='$product',m_unit='$m_unit',
		unit_price='$unit_price',qty='$qty',total='$total',
		transfer_date='$transfer_date',created_by='$updated_by',created_date='$updated_time' WHERE transfer_id=$transfer_id AND transfer_no='$transfer_no'";
		mysql_query($usql);
		$this->updateStockJournal($transfer_no,$transfer_id,$project_id,$transfer_from,$delivery_point,$product,$qty);
		}elseif($transfer_id ==0 && $product !="" && $qty >0){
		$isql = "INSERT INTO ".STOCK_TRANSFER_DETAILS_TBL." (transfer_no,project_id,transfer_from,delivery_point,catagory,brand_id,product,m_unit,
		unit_price,qty,total,created_by,created_date) VALUES(		
		'$transfer_no','$project_id','$transfer_from','$delivery_point','$catagory','$brand_id','$product','$m_unit',
		'$unit_price','$qty','$total','$updated_by','$updated_time')";
		mysql_query($isql); 
		$transfer_id = mysql_insert_id();
		$this->InsertStockJournal($transfer_no,$transfer_id,$project_id,$transfer_from,$delivery_point,$product,$qty);	
		}

		
	}
		
  	$usql = "UPDATE ".STOCK_TRANSFER_MASTER_TBL." SET transfer_from='$transfer_from', delivery_point= '$delivery_point',total_amount='$total_amount',transfer_date='$transfer_date',updated_by='$updated_by',updated_time='$updated_time' WHERE transfer_no ='$transfer_no'";
  	$smres = mysql_query($usql);
	
	if($smres){ return true;  }else{ return false; }
	}
	   
   }	
   function updateStockJournal($voucher_no,$po_no,$project_id,$transfer_from,$transfer_to,$product_id,$qty){
	
	$Ssql = "SELECT balance FROM ".STORE_STOCK_VIEW." WHERE product_id = '".$product_id."' AND store_id = '$transfer_from' AND project_id = '$project_id'";
	$Srow = mysql_fetch_object(mysql_query($Ssql));
	$stock_qty = $Srow->balance;  
	if(($stock_qty >0) && ($stock_qty >=$qty)){
	$Pcsql="SELECT product_type,m_unit,unit_price FROM ".PRODUCT_TBL." WHERE product_id='$product_id' AND project_id='$project_id'";
	$Pcrow = mysql_fetch_object(mysql_query($Pcsql));
	$m_unit 	= $Pcrow->m_unit;
	$product_type 	= $Pcrow->product_type;
	$unit_price 	= $Pcrow->unit_price;
	
	if($transfer_from !="" && $po_no >0){
	//===== Cr Stock ======
	$totalFCR  = $this->getTotalCreditStock($product,$project_id);
	$totalFDR  = $this->getTotalDebitStock($product,$project_id);					 
	$TFbalance = ($totalFDR - ($totalFCR+$transfer_qty));

	$note="Transfer Stock";
	$sql1= "UPDATE ".STOCK_LEDGER_TBL." SET store_id='$transfer_from',product_id='$product_id', product_type='$product_type',note='$note',unit_price='$unit_price',m_unit='$m_unit',dr=0,cr=$qty,balance='$TFbalance' WHERE voucher_no='".$voucher_no."' AND po_no='".$po_no."'";
	mysql_query($sql1); 
	}
	
	if($transfer_from !="" && $po_no >0){
	//===== Dr Stock ======
	$totalFCR  = $this->getTotalCreditStock($product,$project_id);
	$totalFDR  = $this->getTotalDebitStock($product,$project_id);					 
	$TTbalance = (($totalFDR+$transfer_qty) - $totalFCR);

	$note="Received Stock";
	$sql2= "UPDATE ".STOCK_LEDGER_TBL." SET store_id='$transfer_from',product_id='$product_id', product_type='$product_type',note='$note',unit_price='$unit_price',m_unit='$m_unit',dr=$qty,cr=0,balance='$TTbalance' WHERE voucher_no='".$voucher_no."' AND po_no='".$po_no."'";
	mysql_query($sql2); 
	}

	}
  } 
  function InsertStockJournal($voucher_no,$po_no,$project_id,$transfer_from,$transfer_to,$product_id,$qty){
	
	$Ssql = "SELECT balance FROM ".STORE_STOCK_VIEW." WHERE product_id = '".$product_id."' AND store_id = '$transfer_from' AND project_id = '$project_id'";
	$Srow = mysql_fetch_object(mysql_query($Ssql));
	$stock_qty = $Srow->balance;  
	if(($stock_qty >0) && ($stock_qty >=$qty)){
	$Pcsql="SELECT product_type,m_unit,unit_price FROM ".PRODUCT_TBL." WHERE product_id='$product_id' AND project_id='$project_id'";
	$Pcrow = mysql_fetch_object(mysql_query($Pcsql));
	$m_unit 	= $Pcrow->m_unit;
	$product_type 	= $Pcrow->product_type;
	$unit_price 	= $Pcrow->unit_price;
	
	if($transfer_from !="" && $po_no >0){
	//===== Cr Stock ======
	$totalFCR  = $this->getTotalCreditStock($product,$project_id);
	$totalFDR  = $this->getTotalDebitStock($product,$project_id);					 
	$TFbalance = ($totalFDR - ($totalFCR+$transfer_qty));

	$note="Transfer Stock";
	$created_by = getFromSession('userid'); $create_date = date('Y-m-d h:i:s'); $DR=0; $CR=$qty;
	$sql1 = "INSERT INTO ".STOCK_LEDGER_TBL." (voucher_no,po_no,project_id,store_id,product_id,product_type,note,unit_price,m_unit,dr,cr,balance,created_by,create_date) 
	VALUES('".$voucher_no."','".$po_no."','".$project_id."','".$transfer_from."','".$product_id."','".$product_type."','".$note."','".$unit_price."','".$m_unit."','".$DR."','".$CR."','".$TFbalance."','".$created_by."','".$create_date."')";
	mysql_query($sql1); 

	}
	
	if($transfer_from !="" && $po_no >0){
	//===== Dr Stock ======
	$totalFCR  = $this->getTotalCreditStock($product,$project_id);
	$totalFDR  = $this->getTotalDebitStock($product,$project_id);					 
	$TTbalance = (($totalFDR+$transfer_qty) - $totalFCR);

	$note="Received Stock";
	$created_by = getFromSession('userid'); $create_date = date('Y-m-d h:i:s'); $DR=$qty; $CR=0;
	$sql2 = "INSERT INTO ".STOCK_LEDGER_TBL." (voucher_no,po_no,project_id,store_id,product_id,product_type,note,unit_price,m_unit,dr,cr,balance,created_by,create_date) 
	VALUES('".$voucher_no."','".$po_no."','".$project_id."','".$transfer_from."','".$product_id."','".$product_type."','".$note."','".$unit_price."','".$m_unit."','".$DR."','".$CR."','".$TTbalance."','".$created_by."','".$create_date."')";
	mysql_query($sql2);
	}

	}
   } 

   function deleteItem($transfer_id){
	$updated_by 	= getFromSession('userid');
	$updated_time   = date('Y-m-d h:i:s'); 	
	$project_id 	= getFromSession('project_id');
	$sql = "SELECT * FROM ".STOCK_TRANSFER_DETAILS_TBL." WHERE transfer_id=$transfer_id 
	AND project_id='".$project_id."'";
	$res = mysql_query($sql);
	$num = mysql_num_rows($res);
	if($num >0){
	$row  		= mysql_fetch_object($res);
	$total		= $row->total;	
	$transfer_no	= $row->transfer_no; 
	//===== Delete Contra Voucher Item =====
	$tsql1="DELETE FROM ".STOCK_TRANSFER_DETAILS_TBL." WHERE transfer_id=$transfer_id AND transfer_no='$transfer_no' AND project_id='".$project_id."'";
	mysql_query($tsql1); 
	$tsql2="DELETE FROM ".STOCK_LEDGER_TBL." WHERE transfer_no='$transfer_no' AND po_no=$transfer_id AND project_id='".$project_id."'";
	mysql_query($tsql2);

	//==== Update Transfer Master =====

	$msql = "SELECT * FROM ".STOCK_TRANSFER_MASTER_TBL." WHERE transfer_no ='$transfer_no' 
	AND project_id='".$project_id."'";
	$mres = mysql_query($msql);
	$mrow  		= mysql_fetch_object($mres);
	$total_amount	= ($mrow->total_amount-$total);	

	$usql = "UPDATE ".STOCK_TRANSFER_MASTER_TBL." SET total_amount='$total_amount', updated_by='$updated_by', updated_time='$updated_time' WHERE transfer_no ='$transfer_no'";
  	$smres = mysql_query($usql);

	}
	
   }	
   //===== End Edit Transfer Order =======

   function showPrintEditor($msg = null) {   	  
	  $batch_id = getRequest('batch_id');  
	  if ($batch_id) {
         	 $advArr 		= $this->getPOMasterInfo($batch_id);
         	 $advArr 		= parseThisValue($advArr); 
		 $data   		= array_merge(array(), $advArr); 
		 $data['item_list']	= $this->getProductList($batch_id);
		 $data['message'] 	= $msg;
		 $data['cmd']     	= getRequest('cmd');
		 require_once(PRNIT_PO_BATCH_SKIN);      
		 return true;
	 }
   }
     
   function showEditor($msg = null) {
   	   $data                	= array();
       	   require_once(CLASS_DIR.'/common.list.class.php');	
	   $comListApp 	= new CommonList();
	  
	   $data['product_list'] 	= $comListApp->getProductList(); 
	   $data['brand_list'] 		= $comListApp->getBrandList();	   
	   $data['currency_list']   	= $this->getCurrencyList();  	
	   $data['depo_list'] 		= $comListApp->getDeliveryPointList(); 
	   $data['tmp_sales']		= $this->getTempSales();	   
	   
	   $data['cmd']         	= getRequest('cmd');   
	   require_once(CURRENT_APP_SKIN_FILE); 
	   return $data[0];
   }
   		
   //===== Start PO Batch Setup ====
   function insertPOBatchDetails($batch_id){
	$requestdata 		= array();	
	$project_id  		= getFromSession('project_id');
	$currency        	= getRequest('currency');
	$getSql	= "SELECT * FROM ".TEMP_BATCH_SETUP_TBL." WHERE created_by ='".getFromSession('userid')."' AND project_id='".getFromSession('project_id')."'";
	$gres 	= mysql_query($getSql);
	if(mysql_num_rows($gres)>0){
		while($row = mysql_fetch_object($gres)){
		$requestdata['batch_id']		= $batch_id;
		$requestdata['project_id']		= $project_id;    
		$requestdata['out_from']		= getRequest('out_from');    	  
		$requestdata['catagory_id'] 		= $row->catagory;       	  
		$requestdata['brand_id'] 		= $row->brand_id; 
		$requestdata['product_id'] 		= $row->productid; $product_id	= $row->productid;
		$requestdata['day_qty'] 		= $row->day_qty;    	  
		$requestdata['day_wastage_persent'] 	= $row->day_wastage_persent;       	  
		$requestdata['day_wastage_qty'] 	= $row->day_wastage_qty;       	  
		$requestdata['night_qty'] 		= $row->night_qty;         	  
		$requestdata['night_wastage_persent'] 	= $row->night_wastage_persent;         	  
		$requestdata['night_wastage_qty'] 	= $row->night_wastage_qty;     
		$requestdata['total_day'] 		= $row->total_day;	     
		$requestdata['total_night'] 		= $row->total_night;	    	
		$requestdata['created_by'] 		= getFromSession('userid');		
		
		$info        	= array();
		$info['table']	= PO_BATCH_DETAILS_TBL;
		$info['data'] 	= $requestdata;
		//$info['debug']=  true;   
		$res = insert($info);		
		}// end while 
    	}// end if
	
	if($res){ 
	 $dsql = "DELETE FROM ".TEMP_BATCH_SETUP_TBL." WHERE created_by = '".getFromSession('userid')."' AND project_id='".getFromSession('project_id')."'";
	 mysql_query($dsql);
	}
   } //End of the function insertSalesDetails()

  
   function insertPOBatchMaster(){
	  $project_id  = getFromSession('project_id');    
	  $requestdata = array();	
	  $requestdata = getUserDataSet(PO_BATCH_MASTER_TBL); 	  
	  $requestdata['project_id']    = getFromSession('project_id');    
	  $requestdata['created_by']    = getFromSession('userid');
	  $batch_id = $this->createBatchID();
	  $requestdata['batch_id']= $batch_id;	  
	  $info        		  =  array();
	  $info['table']	  = PO_BATCH_MASTER_TBL;
	  $info['data'] 	  = $requestdata;     
	  //$info['debug']        =  true;
	  $res = insert($info);
	  if($res){
	  	return $batch_id;
	  }else{
	  	return 0;
	  }
   }
	
   function saveSalesItem(){
	mysql_query("START TRANSACTION;");
	mysql_query("SET autocommit=0;");
	$batch_id = $this->insertPOBatchMaster();
	$this->insertPOBatchDetails($batch_id);
	mysql_query("COMMIT;");
	if($batch_id!=""){
	header("location:index.php?app=po.batch.setup&cmd=print_batch&batch_id=".$batch_id);	
	}else{
	header("location:index.php?app=po.batch.setup&cmd=add");
	}
  }

  function getPOMasterInfo($id){		
	$project_id     = getFromSession('project_id');  
	$info           = array();    
	$info['table']  = PO_BATCH_MASTER_TBL.' bm,'.PRODUCT_TBL.' po,'.DELIVERY_POINT_TBL.' d,'.PROJECT_TBL.' p,'.CURRENCY_TBL.' c';	
	$info['fields'] = array('bm.batch_id','bm.batch_name','bm.total_day_qty','bm.total_night_qty','d.delivery_point_name','d.details','p.project_name','p.location','po.product_name','c.curr_symble','bm.created_by','bm.created_date');

	$sql="bm.out_from = d.delivery_pid AND bm.project_id = p.project_id AND bm.finish_goods = po.product_id AND bm.currency = c.currency_id AND bm.project_id = '".$project_id."' 
	AND bm.batch_id = '$id'";
						
	$info['where']  = $sql;	  	
    	$info['groupby']= array("bm.batch_id");
	//$info['debug']= true;
	$res            = select($info);
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
	$info['table']  =  PO_BATCH_DETAILS_TBL.' bd,'.PRODUCT_TBL.' p,'.BRAND_TBL.' b';	
	$info['fields'] = array('bd.detail_id','bd.batch_id','bd.product_id','bd.project_id','bd.out_from','bd.catagory_id','b.brand_name','bd.brand_id','bd.day_qty','bd.day_wastage_persent','bd.day_wastage_qty','bd.night_qty','bd.night_wastage_persent','bd.night_wastage_qty','bd.total_day','bd.total_night',
	'p.product_name','p.product_desc','p.m_unit','p.product_type','p.unit_price');
	
	$sql="bd.product_id = p.product_id AND p.brand_code = b.brand_id AND bd.batch_id = '$id'";
	
	$info['where']   = $sql;
        $info['groupby'] = array("bd.detail_id");
	$info['orderby'] = array("bd.product_id ASC");
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
  function saveTempSales(){
	$str 			= getRequest('str');
	$strArr 		= explode("####",$str);
	//======= Insert into tamp ========	
	$requestdata = array();
	$requestdata = getUserDataSet(TEMP_BATCH_SETUP_TBL);
	$project_id  				= getFromSession('project_id');
	$requestdata['project_id'] 		= $project_id;
	$requestdata['batch_name'] 		= getRequest('batch_name');
	$requestdata['finish_goods'] 		= getRequest('finish_goods');
	$requestdata['currency'] 		= getRequest('currency');
	$requestdata['currencyname'] 		= getRequest('currencyName');
	$requestdata['out_from'] 		= getRequest('out_from');
	$requestdata['productid'] 		= getRequest('productid');
	$sql = "SELECT product_name,m_unit,catagory,brand_code FROM ".PRODUCT_TBL." WHERE product_id = '".$requestdata['productid']."'";
	$row 		= mysql_fetch_object(mysql_query($sql));
	$requestdata['product_name'] 		= $row->product_name;
	$requestdata['catagory'] 		= $row->catagory;
	$requestdata['brand_id'] 		= $row->brand_code;
	$requestdata['m_unit'] 			= $row->m_unit;
	$requestdata['day_qty'] 		= getRequest('day_qty');
	$requestdata['day_wastage_persent'] 	= getRequest('day_wastage');
	$requestdata['day_wastage_qty'] 	= getRequest('day_wastage_qty');
	$requestdata['night_qty'] 		= getRequest('night_qty');
	$requestdata['night_wastage_persent'] 	= getRequest('night_wastage');
	$requestdata['night_wastage_qty'] 	= getRequest('night_wastage_qty');
	$requestdata['total_day'] 		= getRequest('total_day');
	$requestdata['total_night'] 		= getRequest('total_night');
	
	$requestdata['created_by'] 		= getFromSession('userid');		
	$info        		=  array();
	$info['table']	= TEMP_BATCH_SETUP_TBL;
	$info['data'] 	= $requestdata;     
	//$info['debug']  	=  true;
	$res = insert($info);
	$str1=""; $str2=""; $str3="";  
	$str1="<table width='100%' align='center' bgcolor='#99CC66'>
	<tr style='color:#fff;' bgcolor='#00B000' height=28>
	  <td width='2%' nowrap><div align='left'>SL</div></td>
	  <td width='26%' nowrap><div align='left'>Product Name</div></td>
	  <td width='10%' nowrap><div align='left'>Day Qty (gram)</div></td>
	  <td width='10%' nowrap><div align='left'>Day Wastage (%)</div></td>
	  <td width='10%' nowrap><div align='right'>Night Quantity (gram)</div></td>
	  <td width='10%' nowrap><div align='right'>Night Wastage (%)</div></td>	  
	  <td width='12%' nowrap><div align='right'>Total Day Qty (gram)</div></td>
 	  <td width='12%' nowrap><div align='right'>Total Night Qty (gram)</div></td>
	  <td width='8%' nowrap align='center'>Option</td>
	</tr>";
	$sl=1; $TotalDayQty = 0; $TotalNightQty=0; $TotalDayWastage = 0; $TotalNightWastage=0;
	$getSql	= "SELECT * FROM ".TEMP_BATCH_SETUP_TBL." WHERE created_by = '".getFromSession('userid')."' AND project_id='".$project_id."'";
	$gres 	= mysql_query($getSql);
	while($row = mysql_fetch_array($gres)){
	extract($row);
	$TotalDayQty+=$total_day; $TotalNightQty+=$total_night;
	$TotalDayWastage+= $day_wastage_qty; $TotalNightWastage+= $night_wastage_qty; 
	$str2.="
	<tr style='color:#000000' bgcolor='#fff'>
	  <td width='2%' nowrap align='left'>$sl</td>
	  <td width='26%' nowrap align='left'>$product_name</td>
	  <td width='10%' nowrap align='left'>$day_qty $munit</td>
	  <td width='10%' nowrap align='left'>$day_wastage_persent</td>
	  <td width='10%' nowrap><div align='right'>$night_qty $munit</div></td>
	  <td width='10%' nowrap align='right'>$night_wastage_persent</td>	  
	  <td width='12%' nowrap align='right'>$total_day</td>		  
	  <td width='12%' nowrap align='right'>$total_night</td>	  				  
	  <td width='8%' nowrap align='center'><a href=\"?app=po.batch.setup&cmd=deltemp&id=$tmp_id\"><img src=\"images/common/icons/delete.gif\"></a></td>
	</tr>";  $sl++;
	}
	$str3="
	<tr style='color:#000;' bgcolor='#CCCCCC' height=25>
	  <td colspan='6' nowrap><div align='right'>Total </div></td>
	  <td nowrap align='right'>$TotalDayQty $munit</td>
	  <td nowrap align='right'>$TotalNightQty $munit</td>
	  <td nowrap align='center'>&nbsp;</td>
	</tr>
	</table>";
	echo $str1.$str2.$str3."####-@@@@".$TotalDayQty."####-@@@@".$TotalNightQty."####-@@@@".$TotalDayWastage."####-@@@@".$TotalNightWastage;
  }
  function delTempSales(){
	$tmp_id = $_REQUEST['id'];
	if($tmp_id!=""){
	 $dsql = "DELETE FROM ".TEMP_BATCH_SETUP_TBL." WHERE tmp_id ='".$tmp_id."'";
	 mysql_query($dsql);
	}		
	header("location:?app=po.batch.setup&cmd=add");
  }
  function getTempSales(){
	$project_id  = getFromSession('project_id');
	$BatchStr    =""; $str1=""; $str2=""; $str3="";
	$str1="<table width='100%' align='center' bgcolor='#99CC66'>
	<tr style='color:#fff;' bgcolor='#00B000' height=28>
	  <td width='2%' nowrap><div align='left'>SL</div></td>
	  <td width='26%' nowrap><div align='left'>Product Name</div></td>
	  <td width='10%' nowrap><div align='left'>Day Qty (gram)</div></td>
	  <td width='10%' nowrap><div align='left'>Day Wastage (%)</div></td>
	  <td width='10%' nowrap><div align='right'>Night Quantity (gram)</div></td>
	  <td width='10%' nowrap><div align='right'>Night Wastage (%)</div></td>	  
	  <td width='12%' nowrap><div align='right'>Total Day Qty (gram)</div></td>
 	  <td width='12%' nowrap><div align='right'>Total Night Qty (gram)</div></td>
	  <td width='8%' nowrap align='center'>Option</td>
	</tr>";
	$sl=1; $TotalDayQty = 0; $TotalNightQty=0; $TotalDayWastage=0; $TotalNightWastage=0;
	$getSql		= "SELECT * FROM ".TEMP_BATCH_SETUP_TBL." WHERE created_by = '".getFromSession('userid')."' AND project_id='".$project_id."'";
	$gres 		= mysql_query($getSql);
	while($row = mysql_fetch_array($gres)){
	extract($row);
	$TotalDayQty+=$total_day; $TotalNightQty+=$total_night;
	$TotalDayWastage+= $day_wastage_qty; $TotalNightWastage+= $night_wastage_qty; 
	$str2.="
	<tr style='color:#000000' bgcolor='#fff'>
	  <td width='2%' nowrap align='left'>$sl</td>
	  <td width='26%' nowrap align='left'>$product_name</td>
	  <td width='10%' nowrap align='left'>$day_qty $munit</td>
	  <td width='10%' nowrap align='left'>$day_wastage_persent</td>
	  <td width='10%' nowrap><div align='right'>$night_qty $munit</div></td>
	  <td width='10%' nowrap align='right'>$night_wastage_persent</td>	  
	  <td width='12%' nowrap align='right'>$total_day</td>		  
	  <td width='12%' nowrap align='right'>$total_night</td>	  				  
	  <td width='8%' nowrap align='center'><a href=\"?app=po.batch.setup&cmd=deltemp&id=$tmp_id\"><img src=\"images/common/icons/delete.gif\"></a></td>
	</tr>";  $sl++;
	}
	$str3="
	<tr style='color:#000;' bgcolor='#CCCCCC' height=25>
	  <td colspan='6' nowrap><div align='right'>Total </div></td>
	  <td nowrap align='right'>$TotalDayQty $munit</td>
	  <td nowrap align='right'>$TotalNightQty $munit</td>
	  <td nowrap align='center'>&nbsp;</td>
	</tr>
	</table>";
	$BatchStr = $str1.$str2.$str3."####-@@@@".$TotalDayQty."####-@@@@".$TotalNightQty."####-@@@@".$TotalDayWastage."####-@@@@".$TotalNightWastage;
	return $BatchStr;
  }
  	
  //====== End PO Batch =====

  function moveStockQty($transfer_id,$voucher_no,$transfer_from,$store_id,$product,$transfer_qty,$transfer_date){
    $project_id = getFromSession('project_id');
	$Ssql = "SELECT balance FROM ".STORE_STOCK_VIEW." WHERE product_id = '".$product."' AND store_id = '$transfer_from' AND project_id = '$project_id'";
	$Srow = mysql_fetch_object(mysql_query($Ssql));
	$stock_qty = $Srow->balance;  
	if(($stock_qty>0) && ($stock_qty>=$transfer_qty)){
	$Pcsql="SELECT product_type,m_unit,unit_price FROM ".PRODUCT_TBL." WHERE product_id='$product' AND project_id='$project_id'";
	$Pcrow = mysql_fetch_object(mysql_query($Pcsql));
	$m_unit 		= $Pcrow->m_unit;
	$product_type 	= $Pcrow->product_type;
	$unit_price 	= $Pcrow->unit_price;
	//===== Cr Stock ======
	$totalFCR  = $this->getTotalCreditStock($product,getFromSession('project_id'));
	$totalFDR  = $this->getTotalDebitStock($product,getFromSession('project_id'));					 
	$TFbalance = ($totalFDR - ($totalFCR+$transfer_qty));	
	$this->saveStockJournal($voucher_no,$transfer_id,$project_id,$transfer_from,$product,$product_type,"Transfer Stock",$unit_price,$m_unit,0,$transfer_qty,$TFbalance,$transfer_date);
	//===== Dr Stock ======
	$totalFCR  = $this->getTotalCreditStock($product,getFromSession('project_id'));
	$totalFDR  = $this->getTotalDebitStock($product,getFromSession('project_id'));					 
	$TTbalance = (($totalFDR+$transfer_qty) - $totalFCR);	
	$this->saveStockJournal($voucher_no,$transfer_id,$project_id,$store_id,$product,$product_type,"Received Stock",$unit_price,$m_unit,$transfer_qty,0,$TTbalance,$transfer_date);
	return true;
	}else{
	return false;
	}
  }
  
  
   function DeleteStockTransfer($transfer_no){
	$userid = getFromSession('userid');
	  
    mysql_query("START TRANSACTION;");
    $project_id = getFromSession('project_id');	
	//========== Delete All ===========
	$Dsql="DELETE FROM ".STOCK_LEDGER_TBL." WHERE binary voucher_no ='".$transfer_no."' AND project_id='".$project_id."'";
	$res1 = mysql_query($Dsql); 
	$Csql="DELETE FROM ".STOCK_TRANSFER_DETAILS_TBL." WHERE transfer_no='".$transfer_no."' AND project_id='".$project_id."'";
	$res2 = mysql_query($Csql);
	$Stsql="DELETE FROM ".STOCK_TRANSFER_MASTER_TBL." WHERE transfer_no='".$transfer_no."' AND project_id='".$project_id."'";
	$res3 = mysql_query($Stsql); 
	if(($res1) && ($res2) && ($res3)) {	
		mysql_query("COMMIT;"); 
		header("location:index.php?app=sales.report&cmd=transfer_list&msg=Successfully Deleted Stock Transfer!!!");
	}else{
		mysql_query("ROLLBACK;");
		header("location:index.php?app=sales.report&cmd=transfer_list&msg=Failed Delete Stock Transfer. Try again!!!");		
	}
	
  }
  
   function loadProductStock($product_id){
	  $project_id = getFromSession('project_id');  	
	  $transfer_stock = trim(getRequest('transfer_stock'));  		 
	  
	  $Ssql = "SELECT balance FROM ".STORE_STOCK_VIEW." WHERE product_id = '".$product_id."' AND store_id = '$transfer_stock' AND project_id = '$project_id'";
	  $Srow = mysql_fetch_object(mysql_query($Ssql));
	  
	  $Prosql = "SELECT unit_price,m_unit FROM ".PRODUCT_TBL." WHERE product_id = '$product_id' AND project_id = '$project_id'";
	  $Prorow = mysql_fetch_object(mysql_query($Prosql));
	  
	  echo $Srow->balance."#####".$Prorow->unit_price."#####".$Prorow->m_unit;	
   }
   function loadProductStockQty($product_id){
	  $project_id 	  = getFromSession('project_id');  	
	  $transfer_stock = trim(getRequest('transfer_stock'));  		 
	  $sl 	= trim(getRequest('sl'));  
	  $Ssql = "SELECT balance FROM ".STORE_STOCK_VIEW." WHERE product_id = '".$product_id."' AND store_id = '$transfer_stock' AND project_id = '$project_id'";
	  $Srow = mysql_fetch_object(mysql_query($Ssql));
	  
	  $Prosql = "SELECT unit_price,m_unit FROM ".PRODUCT_TBL." WHERE product_id = '$product_id' AND project_id = '$project_id'";
	  $Prorow = mysql_fetch_object(mysql_query($Prosql));
	  
	  echo $Srow->balance."#####".$Prorow->unit_price."#####".$Prorow->m_unit."#####".$sl;	
    }		
   function saveAccountJournal($voucher_no,$sub_id,$head_type,$project_id,$description,$DR=NULL,$CR=NULL,$balance,$status=NULL,$purchare_date=NULL){
	$rsql= "SELECT head_type FROM ".SUB_ACC_HEAD_TBL." WHERE sub_id='".$sub_id."'";  
	$rres = mysql_query($rsql);
	$hnum = mysql_num_rows($rres);
	if($hnum>0){ 
	$hrow = mysql_fetch_object($rres);
	$head_type= $hrow->head_type;
	}else{ 	$head_type= "Supplier"; }		
	$sql = "INSERT INTO ".ACCOUNT_JOURNAL_TBL." (voucher_no,created_date,sub_id,head_type,project_id,description,dr,cr,balance,status) VALUES('".$voucher_no."','".$purchare_date."','".$sub_id."','".$head_type."','".$project_id."','".$description."','".$DR."','".$CR."','".$balance."','".$status."')";
	mysql_query($sql);
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
  function saveStockJournal($voucher_no,$pvoucher_no,$project_id,$store_id,$product_id,$product_type,$note,$unit_price=NULL,$m_unit,$DR=NULL,$CR=NULL,$balance,$create_date=NULL){
	$created_by = getFromSession('userid');
	$sql = "INSERT INTO ".STOCK_LEDGER_TBL." (voucher_no,po_no,project_id,store_id,product_id,product_type,note,unit_price,m_unit,dr,cr,balance,created_by,create_date) 
	VALUES('".$voucher_no."','".$pvoucher_no."','".$project_id."','".$store_id."','".$product_id."','".$product_type."','".$note."','".$unit_price."','".$m_unit."','".$DR."','".$CR."','".$balance."','".$created_by."','".$create_date."')";
	mysql_query($sql); 
  } 
  
  function createBatchID()
   {
      $info = array();
      $info['table']  = PO_BATCH_MASTER_TBL; 
      $info['fields'] = array('max(batch_id) as maxvoucher');
      $res = select($info);
      $maxvoucherId = 'B0000000';
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
      
      $maxvoucherId = generateID("B",$maxvoucherId,8);
      return $maxvoucherId;
   }  
  
   //==================== End Sales Details =====================
   
} // End class


?>
