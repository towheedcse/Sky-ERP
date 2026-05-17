<?php
class FGOut
{
   function run()
   {         
      $cmd = getRequest('cmd');
	  $u_t_id = getFromSession('u_type_id');
      if( ($u_t_id ==102) || ($u_t_id == 105)) //2 = admin, 3 = project admin
      {
      	switch ($cmd)
      	{
      	   case 'out'			: $this->showEditor(); break;
	   case 'infg'			: $this->showEditor4FinishProduction(); break;
	   case 'transfer'		: $this->showEditor4StockTransfer(); break;
	   case 'pro_dtl'		: $this->showEditor4ProductionDetails(); break;
      	   case 'loadProduct'  		: $this->loadProduct4Catagory(trim(getRequest('catagory_id'))); break;
	   case 'print_report'		: $screen = $this->showPrintEditor($msg); break;  
	   case 'load_stock'            : $this->loadProductStock(trim(getRequest('product_id')));break;  
      	   case 'get_productinfo'  	: $this->loadProductInfo(trim(getRequest('product_id'))); break;
      	   default                   	: $cmd = 'out'; $screen = $this->showEditor();   break;
      	}
      }else if( ($u_t_id == 101 || $u_t_id == 107)) // 1 = sysadmin, 2 = admin, 3 = project admin
      {
      	switch ($cmd)
      	{      	   
		   case 'out'		: $this->showEditor(); break;
		   case 'infg'		: $this->showEditor4FinishProduction(); break;
		   case 'transfer'	: $this->showEditor4StockTransfer(); break;
		   case 'pro_dtl'	: $screen = $this->showEditor4ProductionDetails(); break;
      	   	   case 'loadProduct'  	: $this->loadProduct4Catagory(trim(getRequest('catagory_id'))); break;
		   case 'print_report'	: $screen = $this->showPrintEditor($msg); break;  
		   case 'load_stock'    : $this->loadProductStock(trim(getRequest('product_id')));break;  
      	   	   case 'get_productinfo': $this->loadProductInfo(trim(getRequest('product_id'))); break;
		   case 'delete'        : $this->deleteProduction("Delete Page");    break;		  
      	   	   default              : $cmd = 'out'; $screen = $this->showEditor();   break;
      	}
      }else {
      	header("location:index.php?app=user_home&msg=You are not authorised !!!");
      }     	          

      if($cmd == 'out') {
       //require_once(OUT_RAWMATERIALS_SKIN);
      } 
      return true;
   } 
  
   function showPrintEditor($msg = null) { 	  
	  $production_id 	= getRequest('production_id');  
	  if ($production_id) {
		$advArr 			= $this->getProductionMasterInfo($production_id);
		$advArr 			= parseThisValue($advArr); 
		$data   			= array_merge(array(), $advArr); 
		$data['item_list']	= $this->getProductionDetailsList($production_id);
		$data['message'] = $msg;
		$data['cmd']     = getRequest('cmd');
		require_once(PRINT_RAWMATERIALS_USED_SKIN);      
		return true;
	 }else{
		require_once(SHOW_PRINT_PRODUCTION_SKIN);
	  }
   }
   function getProductionMasterInfo($id){		   
	$project_id     = getFromSession('project_id');  
	$info           = array();    
	$info['table']  = PRODUCTION_MASTER_TBL.' pm,'.DELIVERY_POINT_TBL.' st,'.FACTORY_TBL.' f,'.PROJECT_TBL.' pa,'.PRODUCT_TBL.' p,'.CURRENCY_TBL.' c';	
	$info['fields'] = array('pm.production_id','pm.batch_no','pm.version_no','pa.project_name','pa.location','pm.total_value',"DATE_FORMAT(pm.used_date,'%d %b %y' ) as used_date",
	'c.curr_symble','st.delivery_point_name as out_store','f.factory_name','pm.created_time');
	
	$sql="pm.factory_id = f.factory_id  AND pm.out_store_id = st.delivery_pid  AND pm.project_id = pa.project_id AND pm.currency = c.currency_id AND pm.project_id = '".$project_id."' AND pm.production_id = '$id'";
						
	$info['where']  =$sql;	  	
	$info['groupby'] = array("pm.production_id");
	//$info['debug']  = true;
	$res            =	select($info);
	if(count($res)){
		foreach($res as $i=>$v){
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
   function deleteProduction(){ 
	  
	if(userCondition()){
	    mysql_query("START TRANSACTION;");
	 	$project_id = getFromSession('project_id');
		$voucher_no = getRequest('id');  
		$Dsql="DELETE FROM ".PURCHASE_MASTER_TBL." WHERE voucher_no='".$voucher_no."' AND project_id='".$project_id."'";
		$res1 = mysql_query($Dsql); 
		$Csql="DELETE FROM ".PURCHASE_DETAILS_TBL." WHERE voucher_no='".$voucher_no."' AND project_id='".$project_id."'";
		$res2 = mysql_query($Csql);
		$Jsql="DELETE FROM ".ACCOUNT_JOURNAL_TBL." WHERE voucher_no='".$voucher_no."' AND project_id='".$project_id."'";
		$res3 = mysql_query($Jsql); 		
		$Ssql="DELETE FROM ".STOCK_LEDGER_TBL." WHERE voucher_no='".$voucher_no."' AND project_id='".$project_id."'";
		$res4 = mysql_query($Ssql);		
		$Hsql="DELETE FROM ".PRODUCTION_FG_TBL." WHERE batch_no='".$voucher_no."' AND project_id='".$project_id."'";
		$res5 = mysql_query($Hsql);  
	   
		if($res1==1 && $res2==1 && $res3==1 && $res4==1 && $res5==1){
		mysql_query("COMMIT"); 
		header("location:index.php?app=fg.production&cmd=pro_dtl&msg=Successfully record deleted!!!");		
		}else{
		mysql_query("ROLLBACK;");	
		header("location:index.php?app=fg.production&cmd=pro_dtl&msg=Failed to delete record. Please try again");		
		}
	}else{
	     header("location:index.php?app=fg.production&cmd=pro_dtl&msg=You are not authorised !!!");		
	}
   }
   
   //======= Out Rawmaterials =======
   function showEditor($msg = null) { 
   	   require_once(CLASS_DIR.'/common.list.class.php');	
	   $comListApp 	= new CommonList();     
   	   $data                	= array();       
	   $data['finish_list'] 	= $comListApp->getFinishProductList();	
	   $data['product_list'] 	= $this->getProductList();  
	   $data['cat_list'] 		= $this->getCatagoryList();  
	   $data['brand_list'] 		= $comListApp->getBrandList();	
	   $data['currency_list']   	= $this->getCurrencyList();
	   $data['factory_list'] 	= $comListApp->getProductionFactoryList();    	
	   $data['depo_list'] 		= $comListApp->getDeliveryPointList();
	   $data['batch_no'] 		= $this->getProductionBatchID(); 
	 	if(getRequest('submit')){
  			$this->insertProductionMaster();
		}
	   $data['cmd']         	= getRequest('cmd');   
	   require_once(OUT_FINISHGOODS_SKIN); 
	   return $data[0];
  }   
  
  function insertProductionDetails($production_id)
  {
	require_once(CLASS_DIR.'/common.list.class.php');	
	$comlistApp 			= new CommonList();
	$store_id 			= getRequest('factory_id');
	$out_store_id 			= getRequest('out_store_id');
	$requestdata 			= array();
	$arr_catagory_product_id	= array();
	$project_id  			= getFromSession('project_id');
	$currency        		= getRequest('currency');
	$arr_catagory_product_id	= getRequest('input_catagory_product_id');
	$arr_brand        		= getRequest('input_brand');
	$arr_pvno        		= getRequest('input_pvoucher_no');
	$arr_m_unit        		= getRequest('input_m_unit');
	$arr_amount			= getRequest('input_amount');
	$arr_qty      			= getRequest('input_qty');
	$arr_currency     		= getRequest('input_currency');
	for($i=0;$i<count($arr_catagory_product_id);$i++)
	{
	   $catagory_product_sep = $arr_catagory_product_id[$i];		
	   $requestdata['project_id'] = $project_id;       	  
	   for($j=0;$j<count($catagory_product_sep);$j++){
			$catagory_product = explode("###",$catagory_product_sep);
			$catagoryid  = array();
			$productid = array();
			$brandid 	      = array();			  
			$catagoryid['c']  = $catagory_product[0];				
			$brandid['b']  	  = $catagory_product[1];				
			$productid['p']   = $catagory_product[2];
	    }
	    foreach($catagoryid as $val){
			$requestdata['catagory'] = $val;	
	    }
	    foreach($brandid as $val){
			$requestdata['brand_id']= $val; $brand_id = $val;
	    } 				
	    foreach($productid as $val){
			$requestdata['product'] = $val;	$product_id = $requestdata['product'];
	    }
	    foreach($arr_m_unit as $key => $val){
		  if($catagory_product_sep==$key) {
			$requestdata['m_unit'] = $val;	
			$m_unit = $val;
		  }
	    }
	    foreach($arr_pvno as $key => $val){
		  if($catagory_product_sep==$key){
			$pvoucher_no = $val; $requestdata['pvoucher_no'] = $val;	
		  }
	    }
	    foreach($arr_qty as $key => $val){
			if($catagory_product_sep==$key){
				 $requestdata['qty'] = $val;	
			}
	    }
	    foreach($arr_currency as $key => $val){
			if($catagory_product_sep==$key){
			 $requestdata['currency'] = $val;	
			}
	    }				  
	    foreach($arr_amount as $key => $val)
	    {
		  if($catagory_product_sep==$key){
			$requestdata['amount'] = $val;	
		  }
	    }
		
		$requestdata['created_by'] 		  = getFromSession('userid');
		$requestdata['created_time']      = date('Y-m-d h:i:s');  
		$project_id						  = getFromSession('project_id'); 
		$requestdata['project_id']        = $project_id;
		$requestdata['factory_id']        = $store_id;
		$requestdata['out_store_id']      = $out_store_id;
		$requestdata['pvoucher_no']       = $pvoucher_no;
		$requestdata['production_id']     = $production_id;
		$info        					  =  array();
		$info['table']					  = PRODUCTION_DETAILS_TBL;
		$info['data'] 	= $requestdata;      
		//$info['debug']  	=  true;
		 $res = insert($info);
		 if($res){
			$Prosql = "SELECT product_type FROM ".PRODUCT_TBL." WHERE product_id = '$product_id' AND project_id = '$project_id'";
			$Prorow = mysql_fetch_object(mysql_query($Prosql));
			$product_type 	= $Prorow->product_type;
			$used_qty 		= $requestdata['qty'];
			$used_date 		= formatDate(getRequest('used_date'));	
			/*
			$PUSql="SELECT pur_detail_id,sales_qty FROM ".PURCHASE_DETAILS_TBL." WHERE product='$product_id' AND brand_id='$brand_id' 	   	
			AND project_id='$project_id' AND voucher_no='$pvoucher_no'";
			$Prorow = mysql_fetch_object(mysql_query($PUSql));
			$pur_detail_id 	= $Prorow->pur_detail_id;	   		
			$TTLUsedQty 	= ($Prorow->sales_qty+$used_qty);
			$pdusql = "UPDATE ".PURCHASE_DETAILS_TBL." SET sales_qty='".$TTLUsedQty."' WHERE pur_detail_id='$pur_detail_id'"; 
			$pdres = mysql_query($pdusql);
			*/
			
			if($product_id !=""){
			$totalCR  = $this->getTotalCreditStock($product_id,getFromSession('project_id'));
			$totalDR  = $this->getTotalDebitStock($product_id,getFromSession('project_id'));					 
			$balance  = ($totalDR - ($totalCR+$used_qty));					
			$this->saveStockJournal($production_id,$pvoucher_no,$project_id,$out_store_id,$product_id,$product_type,"Used for production",$requestdata['amount'],$requestdata['m_unit'],0,$used_qty,$balance,$used_date);
			}// end if
		  }//end res
   	  }// end for
	  
	  //=== Stock Cr of Raw Materials =====
	  $StockAmount	= getRequest('total_value');
	  if($out_store_id=="D0026"){
	  $StockId 	  = $comlistApp->getWPStockId(getFromSession('project_id'));
	  }else{
	  $StockId 	  = $comlistApp->getFGStockId(getFromSession('project_id'));
	  }
	  $totalStockCr   = $this->getTotalCreditAmount($StockId,getFromSession('project_id'));
	  $totalStockDr   = $this->getTotalDebitAmount($StockId,getFromSession('project_id'));
	  $StockBalance   = ($totalStockDr-($totalStockCr+$StockAmount));	$description = "Used finish goods for Production";				 
	  $comlistApp->saveAccJournal($production_id,$StockId,"Stock","Used Raw Materials",getFromSession('project_id'),$description,0,$StockAmount,$StockBalance,0,$used_date);

  } //End of the function savePaymentDetails()
	
	function insertProductionMaster(){
	  mysql_query("START TRANSACTION;"); 
	  mysql_query("SET autocommit=0;");
	  $project_id  				= getFromSession('project_id');
	  $requestdata 				= array();	
	  $requestdata 				= getUserDataSet(PRODUCTION_MASTER_TBL); 
	  $requestdata['used_date'] 		= formatDate(getRequest('used_date'));
	  
	  if(getRequest('production_type')!="Finish"){
	  $requestdata['finish_qty']   		= 0;
	  $requestdata['production_amount'] = 0;
	  }
	  $requestdata['project_id']        = getFromSession('project_id');    
	  $requestdata['created_by']        = getFromSession('userid');
 	  $production_id 					= $this->createProductionID();
	  $requestdata['created_date']      = date('Y-m-d h:i:s');
	  if($production_id !="")
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
	  if($res){
		$this->insertProductionDetails($production_id);
		mysql_query("COMMIT;");				
              	header("location:index.php?app=fg.production&cmd=print_report&production_id=".$production_id); 
          }else{
	  	 header("location:?app=fg.production&cmd=out");
	  }    
	}
	//=======End Out Rawmaterials =======
	//====== Start FG In ========
   function showEditor4FinishProduction($msg = null) {      
	 require_once(CLASS_DIR.'/common.list.class.php');	
	$comListApp 	= new CommonList();     
	$data                	= array();       
	
	if(getRequest('submit')){
		mysql_query("START TRANSACTION;"); 
		mysql_query("SET autocommit=0;");
		$production_id = $this->saveInFinishGoods();
		if($production_id !=""){
		$this->saveFinishProduction($production_id);
		$msg = "Successfully saved finish goods in !!!";
		mysql_query("COMMIT;");	
		header("location:index.php?app=fg.production&cmd=infg&msg=$msg"); 
		
		}else{
		mysql_query("ROLLBACK;");
		$msg = "Failed finish goods in !!! Please try again";
      		header("location:index.php?app=fg.production&cmd=infg&msg=$msg");	
		}
		
	}else{
		$data['finish_list'] 	= $comListApp->getProductList();	
		$data['cat_list'] 	= $this->getCatagoryList();  
		$data['brand_list'] 	= $comListApp->getBrandList();	
		$data['currency_list']  = $this->getCurrencyList();
		$data['factory_list'] 	= $comListApp->getProductionFactoryList();    	
		$data['depo_list'] 	= $comListApp->getDeliveryPointList();
		$data['batch_no'] 	= $this->getProductionBatchID(); 
	}
	$data['cmd']         	= getRequest('cmd'); 	     	
	$data['depo_list'] 		= $comListApp->getDeliveryPointList();  
	require_once(IN_FINISH_GOODS_SKIN); 
	return $data[0];
   }
   function saveInFinishGoods(){
	  $project_id  				= getFromSession('project_id');
	  $requestdata 				= array();	
	  $requestdata 				= getUserDataSet(PRODUCTION_FG_TBL); 
	  $requestdata['production_date'] 	= formatDate(getRequest('production_date'));
	  $finish_product 			= getRequest('finish_product');
	  $finish_qty 				= getRequest('finish_qty');
	  $Prosql 	= "SELECT catagory,brand_code,m_unit,unit_price FROM ".PRODUCT_TBL." WHERE product_id = '$finish_product' AND project_id = '$project_id'";
	  $Prorow 	= mysql_fetch_object(mysql_query($Prosql));
	  $requestdata['catagory']   = $Prorow->catagory; $requestdata['brand_code'] = $Prorow->brand_code; 		  $requestdata['m_unit']= $Prorow->m_unit;
	  $requestdata['unit_price'] = $Prorow->unit_price;
	  $requestdata['production_qty'] =getRequest('finish_qty');
	  $requestdata['total_value'] = ($requestdata['unit_price']*$finish_qty);
	 
	  $requestdata['project_id']        = getFromSession('project_id');    
	  $requestdata['created_by']        = getFromSession('userid');
 	  $production_id 					= $this->createFGBatchNo();
	  $requestdata['created_time']      = date('Y-m-d h:i:s');
	  if($production_id != ""){
      	  $requestdata['batch_no']      = $production_id;
          }else{
      	  $msg = "ID overflow !!!";
      	  header("location:index.php?app=user_home&msg=$msg");
      	  exit;
          }
	  $info        		=  array();
	  $info['table']	= PRODUCTION_FG_TBL;
	  $info['data'] 	= $requestdata;     
	  //$info['debug']  =  true;
	  $res = insert($info);
	  if($res){
		return $production_id;
      	  }else{
	  mysql_query("ROLLBACK;");		 
	  $msg = "Failed finish goods in !!! Please try again";
      	  header("location:index.php?app=fg.production&cmd=infg&msg=$msg");
	  } 
		  
   }  
   function saveFinishProduction($production_id){
         require_once(CLASS_DIR.'/common.list.class.php');	
	 $comListApp 	= new CommonList();     
         $store_id 		= getRequest('store_id');
   	 $project_id 		= getFromSession('project_id');
  	 $finish_product 	= getRequest('finish_product');
	 $finish_qty 		= getRequest('finish_qty');
	 $Prosql 	= "SELECT catagory,brand_code,m_unit,unit_price FROM ".PRODUCT_TBL." WHERE product_id = '$finish_product' AND project_id = '$project_id'";
	 $Prorow 	= mysql_fetch_object(mysql_query($Prosql));
	 $catagory 	= $Prorow->catagory; $brand_id 	= $Prorow->brand_code; $m_unit 	= $Prorow->m_unit;
	 $unit_price = $Prorow->unit_price;
	
	 $total_value 	    = ($unit_price*$finish_qty);
	 $production_amount = ($unit_price*$finish_qty);
	  	  
	 $balanceQty= $this->getStockBalanceQty($finish_product,$project_id,$store_id);	
	 
	 $balanceF  = ($balanceQty+$finish_qty);	
	 $production_date = formatDate(getRequest('production_date'));					
	 
	 $net_payble=$total_value; $purchase_date = formatDate(getRequest('production_date'));
	  
	  //$voucher_no= $this->saveInPurchaseTbl($production_id,$net_payble,$purchase_date,$catagory,$brand_id,$finish_product,$m_unit,$finish_qty,$unit_price); 
	  $this->saveStockJournal($production_id,$production_id,$project_id,$store_id,$finish_product,"Sales Item","Production",$unit_price,$m_unit,$finish_qty,0,$balanceF,$production_date);
	  //=== Stock Dr =====
	  $StockAmount	 = $total_value;
	  if($store_id=="D0026"){
	  $StockId 	 = $comListApp->getWPStockId(getFromSession('project_id'));
	  }elseif($store_id=="D0027"){
	  $StockId 	 = $comListApp->getMXStockId(getFromSession('project_id'));
	  }else{
	  $StockId 	 = $comListApp->getFGStockId(getFromSession('project_id'));
	  }	  
	  $StockPvBalance= $this->getTotalBalanceAmount($StockId,$project_id);			 
	  $StockBalance  = ($StockPvBalance+$StockAmount);	
	  $description   = "FGP";				 
	  $comListApp->saveAccJournal($production_id,$StockId,"Stock","Finish Goods",getFromSession('project_id'),$description,$StockAmount,0,$StockBalance,0,$production_date);
	 
   }

   function saveInPurchaseTbl($voucher_no,$net_payble,$purchase_date,$catagory,$brand_id,$product,$m_unit,$qty,$unit_price){
		$created_date   = date('Y-m-d h:i:s');
		$project_id     = getFromSession('project_id');    
	    $created_by     = getFromSession('userid');
		/*
		$sqlDV="INSERT INTO ".CREDIT_VOUCHAR_TBL."(voucher_no,project_id,transaction_type,vouchar_type,transaction_name,credit,list_view,created_by,created_date) 
		VALUES('$voucher_no','$project_id','Production','Others Vouchar','Production','$net_payble','Hidden','$created_by','$created_date')";
		$res1= mysql_query($sqlDV);
		$sqlCV="INSERT INTO ".DEVIT_VOUCHAR_TBL."(voucher_no,project_id,transaction_type,vouchar_type,transaction_name,debit,paid_amount,due,list_view,created_by,created_date,status) 
		VALUES('$voucher_no','$project_id','Production','Others Vouchar','Production','$net_payble','$net_payble','0','Hidden','$created_by','$created_date','1')";
		$res2=mysql_query($sqlCV);
		*/
		$sqlM="INSERT INTO ".PURCHASE_MASTER_TBL."(voucher_no,project_id,purchase_date,purchase_type,total_value,net_payble,paid_amount,due,item_received_amount,created_by,created_date) 
		VALUES('$voucher_no','$project_id','$purchase_date','Production','$net_payble','$net_payble','$net_payble','0','$net_payble','$created_by','$created_date')";
		$res3=mysql_query($sqlM);
		
		$sqlD="INSERT INTO ".PURCHASE_DETAILS_TBL."(voucher_no,project_id,catagory,brand_id,product,m_unit,unit_price,qty,rec_qty,total,created_by) 
		VALUES('$voucher_no','$project_id','$catagory','$brand_id','$product','$m_unit','$unit_price','$qty','$qty','$net_payble','$created_by')";
		$res4=mysql_query($sqlD);
		
		return $voucher_no;
	}
	//========= End FG In ===========
   //=============FG Production List================
    function showEditor4ProductionDetails($msg = null) {
	  require_once(CLASS_DIR.'/common.list.class.php');	
	  $comListApp 	= new CommonList(); 
	  $data                			= array();
	  $data['cmd']         			= getRequest('cmd');
	  $data['record_list'] 			= $this->getProductionList(getRequest('from'),getRequest('to'));
	  $data['totalrecord']			= $this->getTotalProductionList();	    	
	  $data['depo_list'] 		    	= $comListApp->getDeliveryPointList();	    	
	  $data['catagory_list'] 		= $comListApp->getCatagoryList();
	  $data['finish_list'] 	= $comListApp->getFinishProductList();	
	  require_once(SHOW_PRODUCTION_FG_LIST_SKIN); 
	  return $data[0];
   }
   function getProductionList($from,$to){		
		if($from == "" && $to == ""){$from=0; $to=100;}
		$date_from= formatDate(getRequest('date_from'));
		$date_to  = formatDate(getRequest('date_to'));    
		$catagory = getRequest('catagory');     
		$product  = getRequest('product');   
		$store_id = getRequest('store_id');   
		$summaryby= getRequest('summaryby');    
		  
		$production_type= "Finish";  
		$project_id     = getFromSession('project_id');  
		$info           = array();    
		$info['table']  = PRODUCTION_FG_TBL.' pm,'.PROJECT_TBL.' pa,'.PRODUCT_TBL.' p,'.DELIVERY_POINT_TBL.' st,'.FACTORY_TBL.' f,'.CURRENCY_TBL.' c';	
		$info['fields'] = array('pm.batch_no','pm.finish_product','pa.project_name','pa.location','f.factory_name','f.address','st.delivery_point_name as in_store','SUM(pm.total_value) as total_value','pm.unit_price','pm.m_unit',
		'p.product_name','SUM(pm.production_qty) as finish_qty',"DATE_FORMAT(pm.production_date ,'%d %b %y' ) as used_date",'pm.production_type','c.curr_symble','pm.created_by','pm.created_time');
		
		$sql="pm.finish_product = p.product_id AND pm.store_id = st.delivery_pid  AND pm.project_id = pa.project_id AND pm.factory_id = f.factory_id AND pm.currency = c.currency_id AND pm.project_id = '".$project_id."' AND pm.production_type='".$production_type."'";	
		if($store_id!=""){
			$sql.=" AND pm.store_id = '$store_id'";
		}
		if($catagory!=""){
			$sql.=" AND p.catagory = '$catagory'";
		}
		if($product!=""){
			$sql.=" AND pm.finish_product = '$product'";
		}
		if($date_from!="" && $date_to ==""){
			$sql.=" AND pm.production_date >= '$date_from'";
		}elseif($date_from=="" && $date_to !=""){
			$sql.=" AND pm.production_date <= '$date_to'";
		}elseif($date_from!="" && $date_to !=""){
			$sql.=" AND pm.production_date BETWEEN '$date_from' AND '$date_to'";
		}						
		$info['where']   = $sql;
		if($summaryby==1){
		$info['groupby']= array("pm.production_date,p.product_id");
		}elseif($summaryby==2){
		$info['groupby']= array("p.product_id");
		}else{
		$info['groupby']= array("pm.batch_no");
		}	  	
		$info['orderby'] = array("pm.batch_no asc LIMIT $from,$to");
		//$info['debug'] = true;
		$result          =	select($info); 
		$cnt = count($result);  	
		if($cnt) {
		foreach($result as $value)  {				
		$data[]	= $value;	
		}
		} 
		
		return $data; 
   } 
   function getTotalProductionList() { 		
		$date_from= formatDate(getRequest('date_from'));
		$date_to  = formatDate(getRequest('date_to'));     
		$catagory = getRequest('catagory');   
		$product  = getRequest('product');   
		$store_id = getRequest('store_id');    
		$summaryby= getRequest('summaryby');   
		
		$production_type = "Finish";  
		$project_id     = getFromSession('project_id');  
		$info           = array();    
		$info['table']  = PRODUCTION_FG_TBL.' pm,'.PROJECT_TBL.' pa,'.PRODUCT_TBL.' p,'.DELIVERY_POINT_TBL.' st,'.FACTORY_TBL.' f,'.CURRENCY_TBL.' c';	
		$info['fields'] = array('pm.batch_no','pm.finish_product','pa.project_name','pa.location','f.factory_name','f.address','st.delivery_point_name as in_store','SUM(pm.total_value) as total_value','pm.unit_price','pm.m_unit',
		'p.product_name','SUM(pm.production_qty) as finish_qty',"DATE_FORMAT(pm.production_date ,'%d %b %y' ) as used_date",'pm.production_type','c.curr_symble','pm.created_by','pm.created_time');
		
		$sql="pm.finish_product = p.product_id AND pm.store_id = st.delivery_pid  AND pm.project_id = pa.project_id AND pm.factory_id = f.factory_id AND pm.currency = c.currency_id AND pm.project_id = '".$project_id."' AND pm.production_type='".$production_type."'";	
		if($store_id!=""){
			$sql.=" AND pm.store_id = '$store_id'";
		}
		if($catagory!=""){
			$sql.=" AND p.catagory = '$catagory'";
		}
		if($product!=""){
			$sql.=" AND pm.finish_product = '$product'";
		}
		if($date_from!="" && $date_to ==""){
			$sql.=" AND pm.production_date >= '$date_from'";
		}elseif($date_from=="" && $date_to !=""){
			$sql.=" AND pm.production_date <= '$date_to'";
		}elseif($date_from!="" && $date_to !=""){
			$sql.=" AND pm.production_date BETWEEN '$date_from' AND '$date_to'";
		}						
		$info['where']   = $sql;
		if($summaryby==1){
		$info['groupby']= array("pm.production_date,p.product_id");
		}elseif($summaryby==2){
		$info['groupby']= array("p.product_id");
		}else{
		$info['groupby']= array("pm.batch_no");
		}
		//$info['debug'] = true;
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
   //=======End FG Production List=======
 
   //========= Stock Transfer =======
   function showEditor4StockTransfer($msg = null) {      
	 require_once(CLASS_DIR.'/common.list.class.php');	
	$comListApp 	= new CommonList();     
	$data                	= array();       
	$data['finish_list'] 	= $comListApp->getFinishProductList();	
	$data['cat_list'] 		= $this->getCatagoryList();  
	$data['brand_list'] 	= $comListApp->getBrandList();	    	
	$data['depo_list'] 		= $comListApp->getDeliveryPointList();
	if(getRequest('submit')){
		mysql_query("START TRANSACTION;"); 
		mysql_query("SET autocommit=0;");
		$moveres = $this->moveStockQty();
		mysql_query("COMMIT;");	
		if($moveres){
		header("location:index.php?app=sales.report&cmd=stock_status&msg=Successfully Transfer Stock"); 
		}else{
		header("location:index.php?app=fg.production&cmd=transfer&msg=Have Not Sufficient Stock Balance"); 
		}
	}
	$data['cmd']         	= getRequest('cmd'); 	     	
	$data['depo_list'] 		= $comListApp->getDeliveryPointList();  
	require_once(STOCK_TRANSFER_SKIN); 
	return $data[0];
   }
   function moveStockQty(){
    $project_id = getFromSession('project_id');
	$transfer_from      = getRequest('transfer_from'); 
	$store_id         	= getRequest('store_id'); 
	$product         	= getRequest('transfer_product'); 
	$stock_qty         	= getRequest('stock_qty');  
	$transfer_qty       = getRequest('transfer_qty');  
	$transfer_date = formatDate(getRequest('transfer_date'));	
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
	$this->saveStockJournal($transfer_from,"TS",$project_id,$transfer_from,$product,$product_type,"Transfer Stock",$unit_price,$m_unit,0,$transfer_qty,$TFbalance,$transfer_date);
	//===== Dr Stock ======
	$totalFCR  = $this->getTotalCreditStock($product,getFromSession('project_id'));
	$totalFDR  = $this->getTotalDebitStock($product,getFromSession('project_id'));					 
	$TTbalance = (($totalFDR+$transfer_qty) - $totalFCR);	
	$this->saveStockJournal($transfer_from,"RS",$project_id,$store_id,$product,$product_type,"Received Stock",$unit_price,$m_unit,$transfer_qty,0,$TTbalance,$transfer_date);
	return true;
	}else{
	return false;
	}
   }
   function loadProductStock($product_id){
	  $project_id = getFromSession('project_id');  	
	  $transfer_stock = trim(getRequest('transfer_stock'));  		 
	  $info            = array();
	  $info['table']   = STORE_STOCK_VIEW;
	  $info['fields']  =  array('balance');
	  $where= "product_id = '".$product_id."' AND store_id = '$transfer_stock' AND project_id = '$project_id'";
	  $info['where']   = $where;
	  $result          = select($info);
	  $data            = array();
	  if(count($result)){
		 foreach($result as $key=>$value){
			$data[$key][]        = $value;
		 }
	  }
	  foreach($data as $i=>$v){
		 $str = $v[0]->balance."#####";
	  }
	  echo $str;	
    }
	//=========End Stock Transfer =======
   function getProductList()
   {	
      $project_id  		= getFromSession('project_id');
	  $data = array(); 
      $info        		=  array();
      $info['table']   = PRODUCT_TBL." p,".CATAGORY_TBL.' c,'.BRAND_TBL.' b';
	  $info['fields']  =  array('p.m_unit','p.unit_price','p.product_id','p.product_name','p.catagory','c.catagory_name','p.brand_code','b.brand_name');
	  $where= "p.catagory = c.catagory_code AND p.brand_code= b.brand_id AND p.project_id='$project_id' AND p.product_type !='Sales Item' AND p.approval_status = 1";
	  $info['where']   = $where;
	  $info['groupby'] = array("p.product_id");
	  $info['orderby'] = array("p.product_name ASC");
	  //$info['debug']   = true;
      $res            	=	 select($info); 
      if(count($res)){
         foreach($res as $i=>$v){
            $data[$i] = $v;             
         }
      }
      return $data;	
   }  
	function loadProductInfo($product_id){
	  $project_id = getFromSession('project_id');
	  $store_id   = getRequest('store_id');  
	  $totalCRStock  = $this->getTotalCreditStockQty($product_id,$project_id,$store_id);
	  $totalDRStock  = $this->getTotalDebitStockQty($product_id,$project_id,$store_id);		 
	  $Stockbalance  = ($totalDRStock - $totalCRStock); 
	  $info            = array();
	  $info['table']   = PRODUCT_TBL." p,".CATAGORY_TBL.' c,'.BRAND_TBL.' b';
	  $info['fields']  =  array('p.m_unit','p.unit_price','p.catagory','c.catagory_name','p.brand_code','b.brand_name');
	  $where= "p.catagory=c.catagory_code AND p.brand_code=b.brand_id AND p.product_id = '$product_id' AND p.project_id='$project_id'";
	  
	  $info['where']   = $where;
	  $info['groupby'] = array("p.product_id");
	  $result          = select($info);
	  $data            = array();

	  if(count($result)){
		 foreach($result as $key=>$value){
			$data[$key][]        = $value;
		 }
	  }
			
	  foreach($data as $i=>$v)
	  {
		 
		 $str=$v[0]->unit_price."#####".$Stockbalance."#####".$v[0]->m_unit."#####".$v[0]->catagory."###".$v[0]->catagory_name."#####".$v[0]->brand_code."###".$v[0]->brand_name;
	  }
	  echo $str;	
    }
	
   function loadProduct4Catagory($catagory)
   {	  
	  	  $project_id = getFromSession('project_id');  
		  $info            = array();
		  $info['table']   = PRODUCT_TBL;
		  $info['fields']  =  array('product_id','product_name');
		  $info['where']   = "catagory = '$catagory' AND project_id = '$project_id' AND approval_status = 1";
		  $info['groupby'] = array("product_id");
		  $info['debug']   = false;
		  $result          = select($info);
		  $data            = array();
		  if(count($result)){
			 foreach($result as $key=>$value){
				$data[$key][]        = $value;
			 }
		  }
		  foreach($data as $i=>$v){
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
      if(count($res)){
         foreach($res as $i=>$v){
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
   
   function getTotalCreditStockQty($acc_head,$project_id,$store_id){
   		$sql = "SELECT sum(`cr`) as credit_amount FROM ".STOCK_LEDGER_TBL." WHERE product_id = '$acc_head' AND project_id = '$project_id' AND store_id='$store_id'";
		$row = mysql_fetch_object(mysql_query($sql));
		$credit_amount = $row->credit_amount;
		if(empty($credit_amount)){
			$credit_amount = 0;
		}
		return $credit_amount;
   }
   function getTotalDebitStockQty($acc_head,$project_id,$store_id){
   		$sql = "SELECT sum(`dr`) as debit_amount FROM ".STOCK_LEDGER_TBL." WHERE product_id = '$acc_head' AND project_id = '$project_id' AND store_id='$store_id'";
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
   function getTotalBalanceAmount($acc_head,$project_id){
	$sql = "SELECT (sum(`dr`) - sum(`cr`)) as balance_amount FROM ".ACCOUNT_JOURNAL_TBL." WHERE sub_id = '$acc_head' AND project_id = '$project_id'";	
	$row = mysql_fetch_object(mysql_query($sql));
	$balance_amount = $row->balance_amount;
	if(empty($balance_amount)){
		$balance_amount = 0;
	}
	return $balance_amount;
    }
   function getStockBalanceQty($acc_head,$project_id,$store_id){
	$sql = "SELECT (sum(`dr`) - sum(`cr`)) as balance_qty FROM ".STOCK_LEDGER_TBL." WHERE product_id = '$acc_head' AND project_id = '$project_id'";
	if($store_id !=""){
	$sql.= " AND store_id ='$store_id'";
	}
	$row = mysql_fetch_object(mysql_query($sql));
	$balance_qty = $row->balance_qty;
	if(empty($balance_qty)){
		$balance_qty = 0;
	}
	return $balance_qty;
   }
   function saveStockJournal($voucher_no,$pvoucher_no,$project_id,$store_id,$product_id,$product_type,$note,$unit_price=NULL,$m_unit,$DR=NULL,$CR=NULL,$balance,$create_date=NULL){
    $created_by = getFromSession('userid');
	$sql = "INSERT INTO ".STOCK_LEDGER_TBL." (voucher_no,po_no,project_id,store_id,product_id,product_type,note,unit_price,m_unit,dr,cr,balance,created_by,create_date)
	 VALUES('".$voucher_no."','".$pvoucher_no."','".$project_id."','".$store_id."','".$product_id."','".$product_type."','".$note."','".$unit_price."','".$m_unit."','".$DR."','".$CR."','".$balance."','".$created_by."','".$create_date."')";
	mysql_query($sql); 
   }  
   function createFGBatchNo() {
   	  $project_id  	  = getFromSession('project_id');
      $info = array();
      $info['table'] = PRODUCTION_FG_TBL;
      $info['fields'] = array('max(batch_no) as maxProduction');      
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
   function createProductionID() {
   	  $project_id  	  = getFromSession('project_id');
      $info = array();
      $info['table'] = PRODUCTION_MASTER_TBL;
      $info['fields'] = array('max(production_id) as maxProduction');      
      $res = select($info);      
      $maxProductionId = 'P0000000';      
      if(count($res)){
         foreach($res as $v){
		 if($v->maxProduction){
		 $maxProductionId = $v->maxProduction;
		 }
		 break;   	
         }
      }
      $maxProductionId = generateID("P",$maxProductionId,8);
      return $maxProductionId;
   }  
  function getProductionBatchID() {
   	  $project_id  	  = getFromSession('project_id');
      $info = array();
      $info['table']  = PRODUCTION_MASTER_TBL;
      $info['fields'] = array('max(batch_no) as maxProduction'); 
	  $info['where']  = "project_id = '$project_id'";     
      $res = select($info);      
      $maxProductionId = 'B000000';      
      if(count($res)){
         foreach($res as $v){
		 if($v->maxProduction){
		 $maxProductionId = $v->maxProduction;
		 }
		 break;   	
         }
      }
      $maxProductionId = generateID("B",$maxProductionId,7);
      return $maxProductionId;
  } 
      
} // End class


?>
