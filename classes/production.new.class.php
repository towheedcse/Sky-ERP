<?php
class Production
{
   
   function run()
   {         
      $cmd = getRequest('cmd');
	  $u_t_id = getFromSession('u_type_id');
      if( ($u_t_id ==101) || ($u_t_id == 102) || ($u_t_id == 103) || ($u_t_id == 105)) // 101 = sysadmin, 102 = admin, 103= salesman, 105=store
      {

      	switch ($cmd)
      	{
      	   case 'add'					: $this->showEditor(); break;
		   case 'edit'					: $this->showEditor(); break;
		   case 'sal_dtl'				: $this->showEditor4SalesDetails(); break;
		   case 'admin_sal_dtl'			: $this->showAllCompaniesSalesDetails(); break;
      	   case 'loadProduct'  			: $this->loadProduct4Catagory(trim(getRequest('catagory_id'))); break; 
      	   case 'get_dtl'  				: $this->loadProductDtl(trim(getRequest('product_id'))); break;  
      	   case 'save_tmp'  			: $this->saveTempProduction(); break;   
		   case 'deltemp'				: $this->delTempProduction(); break;    
		   case 'save_transfer'			: $this->saveProductionItem(); break; 
		   case 'print_challan'			: $screen = $this->showPrintEditor($msg); break;  
		   case 'delete'             	: $screen = $this->deleteRecord(getRequest('id')); break;
      	   default                   	: $cmd = 'list'; $screen = $this->showEditor();   break; 
		   case 'load_stock'            : $this->loadProductStock(trim(getRequest('product_id')));break; 
      	}
      }elseif($u_t_id == 104) // 104 = acc
      {
      	switch ($cmd)
      	{
		   case 'sal_dtl'				: $this->showEditor4SalesDetails(); break;
		   case 'print_challan'			: $screen = $this->showPrintEditor($msg); break;
      	   default                   	: $cmd = 'list'; $screen = $this->showEditor();   break;
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
	  $transfer_no 	= getRequest('transfer_no');  
	  if ($transfer_no) {
         $advArr 			= $this->getTransferMasterInfo($transfer_no);
         $advArr 			= parseThisValue($advArr); 
		 $data   			= array_merge(array(), $advArr); 
		 $data['item_list']	= $this->getProductList($transfer_no);
		 $data['message'] 	= $msg;
		 $data['cmd']     	= getRequest('cmd');
		 require_once(PRNIT_TRANSFER_CHALLAN_SKIN);      
		 return true;
	 }
   }
     
   function showEditor($msg = null) {
   	   $data                	= array();
       require_once(CLASS_DIR.'/common.list.class.php');	
	   $comListApp 	= new CommonList();
	  
	   $data['product_list'] 	= $comListApp->getProductList(); 
	   $data['brand_list'] 		= $comListApp->getBrandList();	   
	   $data['currency_list']   = $this->getCurrencyList();
	   $data['factory_list'] 	= $comListApp->getProductionFactoryList();    	
	   $data['depo_list'] 		= $comListApp->getDeliveryPointList(); 
	   $data['tmp_sales']		= $this->getTempProduction();	   
	   
	   $data['cmd']         	= getRequest('cmd');   
	   require_once(CURRENT_APP_SKIN_FILE); 
	   return $data[0];
   }
	
  //===== Saart Save Sales ====
  
	function saveTempProduction(){
		$str 			= getRequest('str');
		$strArr 		= explode("####",$str);
		//======= Insert into tamp ========	
		$requestdata = array();
		$requestdata = getUserDataSet(TEMP_PRODUCTION_TBL);
		$project_id  				= getFromSession('project_id');
		$requestdata['project_id'] 		= $project_id;
		$requestdata['factory_id'] 	= getRequest('factory_id');
		$requestdata['store_id'] 	= getRequest('store_id');
		$requestdata['production_date'] 	= formatDate(getRequest('production_date'));
		$requestdata['currency'] 		= getRequest('currency');
		$requestdata['currencyName'] 	= getRequest('currencyName');
		$requestdata['productid'] 		= getRequest('productid');
		$sql = "SELECT p.product_name,p.catagory,c.catagory_name,p.brand_code,b.brand_name,p.m_unit FROM ".PRODUCT_TBL." as p,".CATAGORY_TBL." as c,".BRAND_TBL." as b 
		WHERE p.catagory=c.catagory_code AND p.brand_code=b.brand_id AND p.product_id = '".$requestdata['productid']."'";
		$row 		= mysql_fetch_object(mysql_query($sql));
		$requestdata['product_name'] 	= $row->product_name;	
		$requestdata['catagory'] 		= $row->catagory;		
		$requestdata['catagoryname'] 	= $row->catagory_name;
		$requestdata['brand_id'] 		= $row->brand_code;	
		$requestdata['brandname'] 		= $row->brand_name;	
		//$requestdata['details'] 		= getRequest('details');
		$requestdata['munit'] 			= $row->m_unit;
		$requestdata['qty'] 			= getRequest('qty');
		$requestdata['unit_price'] 		= getRequest('unit_price');
		$requestdata['total'] 			= getRequest('total');
		
		$requestdata['created_by'] 		= getFromSession('userid');		
		$info        		=  array();
		$info['table']	= TEMP_PRODUCTION_TBL;
		$info['data'] 	= $requestdata;     
		//$info['debug']  	=  true;
		$res = insert($info);
		  
		$str1="<table width='100%' align='center' bgcolor='#99CC66'>
		<tr style='color:#fff;' bgcolor='#00B000' height=28>
		  <td width='2%' nowrap><div align='left'>SL</div></td>
		  <td width='30%' nowrap><div align='left'>Product Name</div></td>
		  <td width='15%' nowrap><div align='left'>Catagory</div></td>
		  <td width='15%' nowrap><div align='left'>Brand</div></td>
		  <td width='10%' nowrap><div align='right'>Production Qty</div></td>
		  <td width='10%' nowrap><div align='right'>Rate</div></td>	  
		  <td width='10%' nowrap><div align='right'>Amount</div></td>				  
		  <td width='8%' nowrap align='center'>Option</td>
		</tr>";
		$total_value = 0; $product_discount = 0; $sl=1; $TotalQty=0; $TotalFreeQty=0;
		$getSql		= "SELECT * FROM ".TEMP_PRODUCTION_TBL." WHERE created_by = '".getFromSession('userid')."' AND project_id='".$project_id."'";
		$gres 		= mysql_query($getSql);
		while($row = mysql_fetch_array($gres)){
		extract($row);
		$total_value+=$total; $TotalQty+=$qty;
		$str2.="
		<tr style='color:#000000' bgcolor='#fff'>
		  <td width='2%' nowrap align='left'>$sl</td>
		  <td width='30%' nowrap align='left'>$product_name</td>
		  <td width='15%' nowrap align='left'>$catagoryname</td>
		  <td width='15%' nowrap align='left'>$brandname</td>
		  <td width='10%' nowrap><div align='right'>$qty $munit</div></td>
		  <td width='10%' nowrap align='right'>$unit_price $currencyName</td>	  
		  <td width='10%' nowrap align='right'>$total</td>				  				  
		  <td width='8%' nowrap align='center'><a href=\"?app=production.new&cmd=deltemp&id=$tmp_id\"><img src=\"images/common/icons/delete.gif\"></a></td>
		</tr>";  $sl++;
		}
		$str3="
		<tr style='color:#000;' bgcolor='#CCCCCC' height=25>
		  <td colspan='4' nowrap><div align='right'>Total </div></td>
		  <td nowrap align='right'>$TotalQty $munit</td>
		  <td nowrap>&nbsp;</td>
		  <td nowrap align='right'>$total_value $currencyName</td>
		  <td nowrap align='center'>&nbsp;</td>
		</tr>
		</table>";
		echo $str1.$str2.$str3."####-@@@@".$total_value;
	}
	function delTempProduction(){
		$tmp_id = $_REQUEST['id'];
		if($tmp_id!=""){
		 $dsql = "DELETE FROM ".TEMP_PRODUCTION_TBL." WHERE tmp_id ='".$tmp_id."'";
		 mysql_query($dsql);
		}		
		header("location:?app=production.new&cmd=add");
	}
	function getTempProduction(){
		$project_id  	= getFromSession('project_id');
		$str1="<table width='100%' align='center' bgcolor='#99CC66'>
		<tr style='color:#fff;' bgcolor='#00B000' height=28>
		  <td width='2%' nowrap><div align='left'>SL</div></td>
		  <td width='30%' nowrap><div align='left'>Product Name</div></td>
		  <td width='15%' nowrap><div align='left'>Catagory</div></td>
		  <td width='15%' nowrap><div align='left'>Brand</div></td>
		  <td width='10%' nowrap><div align='right'>Production Qty</div></td>
		  <td width='10%' nowrap><div align='right'>Rate</div></td>	  
		  <td width='10%' nowrap><div align='right'>Amount</div></td>				  
		  <td width='8%' nowrap align='center'>Option</td>
		</tr>";
		$total_value = 0; $product_discount=0; $TotalQty=0; $TotalFreeQty=0; $sl=1;
		$getSql		= "SELECT * FROM ".TEMP_PRODUCTION_TBL." WHERE created_by = '".getFromSession('userid')."' AND project_id='".$project_id."'";
		$gres 		= mysql_query($getSql);
		while($row = mysql_fetch_array($gres)){
		extract($row);
		$total_value+=$total; $TotalQty+=$qty;
		$str2.="
		<tr style='color:#000000' bgcolor='#fff'>
		  <td width='2%' nowrap align='left'>$sl</td>
		  <td width='30%' nowrap align='left'>$product_name</td>
		  <td width='15%' nowrap align='left'>$catagoryname</td>
		  <td width='15%' nowrap align='left'>$brandname</td>
		  <td width='10%' nowrap><div align='right'>$qty $munit</div></td>
		  <td width='10%' nowrap align='right'>$unit_price $currencyName</td>	  
		  <td width='10%' nowrap align='right'>$total</td>				  				  
		  <td width='8%' nowrap align='center'><a href=\"?app=production.new&cmd=deltemp&id=$tmp_id\"><img src=\"images/common/icons/delete.gif\"></a></td>
		</tr>"; $sl++;
		}
		$str3="
		<tr style='color:#000;' bgcolor='#CCCCCC' height=25>
		  <td colspan='4' nowrap><div align='right'>Total </div></td>
		  <td nowrap align='right'>$TotalQty $munit</td>
		  <td nowrap>&nbsp;</td>
		  <td nowrap align='right'>$total_value $currencyName</td>
		  <td nowrap align='center'>&nbsp;</td>
		</tr>
		</table>";
		$total_salesStr = $str1.$str2.$str3."####-@@@@".$total_value."####-@@@@".$product_discount;
		return $total_salesStr;
	}
	
  //====== End Save Sales =====
  function moveStockQty($voucher_no,$factory_id,$store_id,$product,$transfer_qty,$production_date){
    $project_id = getFromSession('project_id');
	$Ssql = "SELECT balance FROM ".STORE_STOCK_VIEW." WHERE product_id = '".$product."' AND store_id = '$factory_id' AND project_id = '$project_id'";
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
	$this->saveStockJournal($voucher_no,"TS",$project_id,$factory_id,$product,$product_type,"Transfer Stock",$unit_price,$m_unit,0,$transfer_qty,$TFbalance,$production_date);
	//===== Dr Stock ======
	$totalFCR  = $this->getTotalCreditStock($product,getFromSession('project_id'));
	$totalFDR  = $this->getTotalDebitStock($product,getFromSession('project_id'));					 
	$TTbalance = (($totalFDR+$transfer_qty) - $totalFCR);	
	$this->saveStockJournal($voucher_no,"RS",$project_id,$store_id,$product,$product_type,"Received Stock",$unit_price,$m_unit,$transfer_qty,0,$TTbalance,$production_date);
	return true;
	}else{
	return false;
	}
  }
  
  function insertProductionDetails($voucher_no){
	$requestdata 				= array();
	$arr_catagory_product_id	= array();	
	$project_id  				= getFromSession('project_id');
	$currency        			= getRequest('currency');
	$getSql	= "SELECT * FROM ".TEMP_PRODUCTION_TBL." WHERE created_by = '".getFromSession('userid')."' AND project_id='".getFromSession('project_id')."'";
	$gres 	= mysql_query($getSql);
	if(mysql_num_rows($gres)>0){
		while($row = mysql_fetch_object($gres)){
		$requestdata['batch_no']	= $voucher_no;
		$requestdata['factory_id']	= getRequest('factory_id'); 
		$requestdata['store_id']	= getRequest('store_id'); $store_id = getRequest('store_id');  
		$requestdata['project_id']	= $project_id;       	  
		$requestdata['catagory'] 	= $row->catagory; $catagory =  $row->catagory;      	  
		$requestdata['brand_code'] 	= $row->brand_id; $brand_id =  $row->brand_id;
		$requestdata['finish_product'] 	= $row->productid; $product_id	= $row->productid; 
		$requestdata['m_unit'] 		= $row->munit;  $m_unit = $row->munit;    	  
		//$requestdata['details'] 	= $row->details;    	  
		$requestdata['unit_price'] 	= $row->unit_price; $unit_price=$row->unit_price;      	  
		$requestdata['production_qty'] 		= $row->qty; $qty = $row->qty;
		$requestdata['total_value'] 		= $row->total; $total = $row->total;				
		$requestdata['production_date']	= formatDate(getRequest('production_date'));
		$production_date = formatDate(getRequest('production_date'));	  	
		$requestdata['created_by'] 	= getFromSession('userid');
		$requestdata['created_date']= date('Y-m-d h:i:s');			
		
		$info        	=  array();
		$info['table']	= PRODUCTION_FG_TBL;
		$info['data'] 	= $requestdata;
		//$info['debug']  	=  true;   
		$res = insert($info);
		if($res){
		$sqlD="INSERT INTO ".PURCHASE_DETAILS_TBL."(voucher_no,project_id,catagory,brand_id,product,m_unit,unit_price,qty,rec_qty,total,created_by) 
		VALUES('$voucher_no','$project_id','$catagory','$brand_id','$product_id','$m_unit','$unit_price','$qty','$qty','$total','$created_by')";
		$resPD=mysql_query($sqlD);
		
		$totalFCR  = $this->getTotalCreditStock($product_id,getFromSession('project_id'));
		$totalFDR  = $this->getTotalDebitStock($product_id,getFromSession('project_id'));					 
		$STBalance = (($totalFDR+$qty) - $totalFCR);
		
		$this->saveStockJournal($voucher_no,$voucher_no,$project_id,$store_id,$product_id,"Sales Item","Production",$unit_price,$m_unit,$qty,0,$STBalance,$production_date);
		
		
		}
		}// end while 
    }// end if
	
	if($res){ 
	 $dsql = "DELETE FROM ".TEMP_PRODUCTION_TBL." WHERE created_by = '".getFromSession('userid')."' AND project_id='".getFromSession('project_id')."'";
	 mysql_query($dsql);
	}
  } //End of the function insertSalesDetails()

  
	function insertProductionMaster(){
	  $project_id  						= getFromSession('project_id');
	  $requestdata 						= array();	
	  $requestdata 						= getUserDataSet(PRODUCTION_MASTER_TBL); 
	  $requestdata['used_date'] 		= formatDate(getRequest('production_date')); 
	  $requestdata['finish_date'] 		= formatDate(getRequest('production_date'));	  
	  $purchase_date = formatDate(getRequest('production_date'));
	  $requestdata['out_store_id']      = getRequest('store_id');
	  $requestdata['total_value']       = getRequest('total_amount');
	  $requestdata['production_amount'] = getRequest('total_amount'); 
	  $net_payble = getRequest('total_amount'); 
	  $requestdata['production_type'] 	= "Finish";  
	  $requestdata['project_id']        = getFromSession('project_id');    
	  $requestdata['created_by']        = getFromSession('userid');
 	  $production_id 					= $this->createFGBatchNo(); 
	  $requestdata['created_date']      = date('Y-m-d h:i:s');
	  $created_by 		= getFromSession('userid');
	  $created_date     = date('Y-m-d h:i:s');
	  if($production_id !="")
      {
      	$requestdata['production_id']   = $production_id;
		$requestdata['batch_no']      	= $production_id;
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
	  if($res){
		$sqlM="INSERT INTO ".PURCHASE_MASTER_TBL."(voucher_no,project_id,purchase_date,purchase_type,total_value,net_payble,paid_amount,due,item_received_amount,created_by,created_date) 
		VALUES('$production_id','$project_id','$purchase_date','Production','$net_payble','$net_payble','$net_payble','0','$net_payble','$created_by','$created_date')";
		$resPM=mysql_query($sqlM);
		if($resPM){
	  		return $production_id;
		}else{
			return 0;	
		}
	  }else{
	  	return 0;
	  }
	}
	
   function saveProductionItem(){
		mysql_query("START TRANSACTION;");
		mysql_query("SET autocommit=0;");
		$transfer_no = $this->insertProductionMaster();
		$this->insertProductionDetails($transfer_no);
		mysql_query("COMMIT;");
		if($transfer_no!=""){
		header("location:index.php?app=production.new&cmd=print_challan&transfer_no=".$transfer_no);	
		}else{
		header("location:index.php?app=production.new&cmd=add");
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
	function getTransferMasterInfo($id){		
		$project_id     = getFromSession('project_id');  
		$info           = array();    
		$info['table']  = STOCK_TRANSFER_MASTER_TBL.' tm,'.DELIVERY_POINT_TBL.' d,'.PROJECT_TBL.' p,'.CURRENCY_TBL.' c';	
		$info['fields'] = array('tm.transfer_no','tm.factory_id','tm.store_id','d.delivery_point_name','d.details','p.project_name','p.location','tm.total_amount',"DATE_FORMAT(tm.production_date,'%d %b %y' ) as production_date",'c.curr_symble','tm.created_by','tm.created_date');
	
		$sql="tm.store_id = d.delivery_pid AND tm.project_id = p.project_id AND tm.currency = c.currency_id AND tm.project_id = '".$project_id."' 
		AND tm.transfer_no = '$id'";
							
		$info['where']  =$sql;	  	
	    $info['groupby'] = array("tm.transfer_no");
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
		$info['table']  =  STOCK_TRANSFER_DETAILS_TBL.' sd,'.PRODUCT_TBL.' p,'.CURRENCY_TBL.' c,'.BRAND_TBL.' b';	
		$info['fields'] = array('sd.transfer_id','sd.transfer_no','sd.project_id','sd.factory_id','sd.store_id','sd.catagory','b.brand_name','sd.product',
		'p.product_name','p.product_desc','p.m_unit','sd.unit_price','c.curr_symble','sd.qty','sd.total');
		
		$sql="sd.product = p.product_id AND p.brand_code = b.brand_id AND sd.currency = c.currency_id AND sd.transfer_no = '$id'";
		
		$info['where']  = $sql;
	    $info['groupby'] = array("sd.transfer_id");
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
  
  function createVoucharID()
   {
      $info = array();
      $info['table']  = STOCK_TRANSFER_MASTER_TBL; 
      $info['fields'] = array('max(transfer_no) as maxvoucher');
      $res = select($info);
      $maxvoucherId = 'T00000000';
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
      
      $maxvoucherId = generateID("T",$maxvoucherId,9);
      return $maxvoucherId;
  }
  function createFGBatchNo() {
   	  $project_id  	  = getFromSession('project_id');
      $info = array();
      $info['table'] = PRODUCTION_MASTER_TBL;
      $info['fields'] = array('max(production_id) as maxProduction');      
      $res = select($info);      
      $maxProductionId = 'FG000000000';      
      if(count($res)){
         foreach($res as $v){
		 if($v->maxProduction){
		 $maxProductionId = $v->maxProduction;
		 }
		 break;   	
         }
      }
      $maxProductionId = generateID("FG",$maxProductionId,11);
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
    
} // End class


?>