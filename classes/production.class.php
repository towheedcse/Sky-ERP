<?php
class Production
{
   function run()
   {         
      $cmd = getRequest('cmd');
	  $u_t_id = getFromSession('u_type_id');
      if( ($u_t_id == 101) || ($u_t_id ==102) || ($u_t_id == 105)) // 1 = sysadmin, 2 = admin, 3 = project admin
      {
      	switch ($cmd)
      	{
      	   case 'add'					: $this->showEditor(); break;
		   case 'finish_pro'			: $this->showEditor4FinishProduction(); break;
		   case 'edit'					: $this->showEditor(); break;
		   case 'pro_dtl'				: $screen = $this->showEditor4ProductionDetails(); break;
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
		require_once(PRINT_RAWMATERIALS_USED_SKIN);      
		return true;
	 }else{
		require_once(SHOW_PRINT_PRODUCTION_SKIN);
	  }
   }
   
   function showEditor($msg = null) { 
   	   require_once(CLASS_DIR.'/common.list.class.php');	
	   $comListApp 	= new CommonList();     
   	   $data                	= array();       
	   $data['finish_list'] 	= $comListApp->getFinishProductList();	
	   $data['cat_list'] 		= $this->getCatagoryList();  
	   $data['brand_list'] 		= $comListApp->getBrandList();	
	   $data['currency_list']   = $this->getCurrencyList();
	   $data['factory_list'] 	= $comListApp->getProductionFactoryList();    	
	   $data['depo_list'] 		= $comListApp->getDeliveryPointList();
	   $data['batch_no'] 		= $this->getProductionBatchID(); 
	 	if(getRequest('submit')){
  			$this->insertProductionMaster();
		}
	   $data['cmd']         	= getRequest('cmd');   
	   require_once(CURRENT_APP_SKIN_FILE); 
	   return $data[0];
  }   
  
  function insertProductionDetails($production_id)
  {
	require_once(CLASS_DIR.'/common.list.class.php');	
	$comlistApp 				= new CommonList();
	$store_id 		= getRequest('store_id');
	$out_store_id 	= getRequest('out_store_id');
	$requestdata 				= array();
	$arr_catagory_product_id	= array();
	$project_id  				= getFromSession('project_id');
	$currency        			= getRequest('currency');
	$arr_catagory_product_id	= getRequest('input_catagory_product_id');
	$arr_brand        			= getRequest('input_brand');
	$arr_pvno        			= getRequest('input_pvoucher_no');
	$arr_m_unit        			= getRequest('input_m_unit');
	$arr_amount					= getRequest('input_amount');
	$arr_qty      				= getRequest('input_qty');
	$arr_currency     			= getRequest('input_currency');

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
			$requestdata['product'] = $val;	
			$product_id 			= $val;
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
			 
			$PUSql="SELECT pur_detail_id,sales_qty FROM ".PURCHASE_DETAILS_TBL." WHERE product='$product_id' AND brand_id='$brand_id' 	   	
			AND project_id='$project_id' AND voucher_no='$pvoucher_no'";
	   		$Prorow = mysql_fetch_object(mysql_query($PUSql));
	   		$pur_detail_id 	= $Prorow->pur_detail_id;	   		
	   		$TTLUsedQty 	= ($Prorow->sales_qty+$used_qty);
			$pdusql = "UPDATE ".PURCHASE_DETAILS_TBL." SET sales_qty='".$TTLUsedQty."' WHERE pur_detail_id='$pur_detail_id'";
			$pdres = mysql_query($pdusql);
			if($pdres){
			 $totalCR  = $this->getTotalCreditStock($product_id,getFromSession('project_id'));
			 $totalDR  = $this->getTotalDebitStock($product_id,getFromSession('project_id'));					 
			 $balance  = ($totalDR - ($totalCR+$used_qty));					
			$this->saveStockJournal($production_id,$pvoucher_no,$project_id,$out_store_id,$product_id,$product_type,$requestdata['amount'],$requestdata['m_unit'],0,$used_qty,$balance,$used_date);
			
			}
			
		  }
   	  }// end for
	  if(getRequest('production_type')=="Finish"){
		  $finish_product = getRequest('finish_product');
		  $finish_qty = getRequest('finish_qty');
		  $unit_price = (getRequest('total_value')/$finish_qty);
		  $totalFCR  = $this->getTotalCreditStock($finish_product,getFromSession('project_id'));
		  $totalFDR  = $this->getTotalDebitStock($finish_product,getFromSession('project_id'));					 
		  $balanceF  = (($totalFDR+$finish_qty) - $totalFCR);	
		  $production_date = formatDate(getRequest('used_date'));					
		  
		  $net_payble=getRequest('total_value'); $purchase_date = $production_date;
		  $Prosql 	= "SELECT catagory,brand_code,m_unit FROM ".PRODUCT_TBL." WHERE product_id = '$finish_product' AND project_id = '$project_id'";
		  $Prorow 	= mysql_fetch_object(mysql_query($Prosql));
		  $catagory = $Prorow->catagory; $brand_id 	= $Prorow->brand_code; $m_unit 	= $Prorow->m_unit;
		  $voucher_no= $this->saveInPurchaseTbl($net_payble,$purchase_date,$catagory,$brand_id,$finish_product,$m_unit,$finish_qty,$unit_price); 
		  $this->saveStockJournal($voucher_no,$production_id,$project_id,$store_id,$finish_product,"Sales Item",$unit_price,$m_unit,$finish_qty,0,$balanceF,$production_date);
		  //=== Stock Dr =====
		  $StockAmount	= getRequest('total_value');
		  $StockId 	 = $comlistApp->getStockId(getFromSession('project_id'));
		  $totalStockCr  = $this->getTotalCreditAmount($StockId,getFromSession('project_id'));
		  $totalStockDr  = $this->getTotalDebitAmount($StockId,getFromSession('project_id'));					 
		  $StockBalance  = (($totalStockDr+$StockAmount)-$totalStockCr);	$description = "Finish Goods Production";				 
		  $comlistApp->saveAccJournal($voucher_no,$StockId,"Stock","Finish Goods",getFromSession('project_id'),$description,$StockAmount,0,$StockBalance,0,$created_date);
	  }
	  //=== Stock Cr of Raw Materials =====
		$StockAmount	= getRequest('total_value');
		$StockId 	 	= $comlistApp->getStockId(getFromSession('project_id'));
		$totalStockCr  = $this->getTotalCreditAmount($StockId,getFromSession('project_id'));
		$totalStockDr  = $this->getTotalDebitAmount($StockId,getFromSession('project_id'));					 
		$StockBalance  = ($totalStockDr-($totalStockCr+$StockAmount));	$description = "Used for Production";				 
		$comlistApp->saveAccJournal($voucher_no,$StockId,"Stock","Used Raw Materials",getFromSession('project_id'),$description,0,$StockAmount,$StockBalance,0,$created_date);

  } //End of the function savePaymentDetails()
	
	function insertProductionMaster(){
	  mysql_query("START TRANSACTION;"); 
	  mysql_query("SET autocommit=0;");
	  $project_id  					= getFromSession('project_id');
	  $requestdata 						= array();	
	  $requestdata 						= getUserDataSet(PRODUCTION_MASTER_TBL); 
	  $requestdata['used_date'] 		= formatDate(getRequest('used_date'));
	  $finish_qty 						= getRequest('finish_qty');
	  $sold_cost						= getRequest('sold_cost');	
	  $product_id 						= getRequest('finish_product');
	  $Prosql = "SELECT catagory,m_unit,unit_price FROM ".PRODUCT_TBL." WHERE product_id = '$product_id' AND project_id = '$project_id'";
	  $Prorow = mysql_fetch_object(mysql_query($Prosql));
	  $catagory 						= $Prorow->catagory;
	  $requestdata['catagory']   		= $catagory;
	  $requestdata['target_qty']   		= $finish_qty;	   	   
	  $requestdata['sales_price']   	= $Prorow->unit_price;
	  $requestdata['unit_price']   		= (getRequest('total_value')/$finish_qty);
	  if(getRequest('production_type')!="Finish"){
	  $requestdata['finish_qty']   		= 0;
	  $requestdata['production_amount'] = 0;
	  }else{
	  $requestdata['finish_qty']   		= $finish_qty;
	  $requestdata['production_amount'] = ($finish_qty*$requestdata['unit_price']);
	  }
	  $requestdata['m_unit']   			= $Prorow->m_unit;   
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
	  if($res){
		 $this->insertProductionDetails($production_id);	
		 mysql_query("COMMIT;");				
         header("location:index.php?app=production&cmd=print_report&production_id=".$production_id); 
      }else{
	  	 header("location:?app=used4_feed");
	  }    
	}
	function saveInPurchaseTbl($net_payble,$purchase_date,$catagory,$brand_id,$product,$m_unit,$qty,$unit_price){
		require_once(CLASS_DIR.'/purchase.class.php');	
		$PurApp 		= new Purchase();  
		$voucher_no 	= $PurApp->createVoucharID();
		$created_date   = date('Y-m-d h:i:s');
		$project_id     = getFromSession('project_id');    
	    $created_by     = getFromSession('userid');
		
		$sqlDV="INSERT INTO ".CREDIT_VOUCHAR_TBL."(voucher_no,project_id,transaction_type,vouchar_type,transaction_name,credit,list_view,created_by,created_date) 
		VALUES('$voucher_no','$project_id','Production','Others Vouchar','Production','$net_payble','Hidden','$created_by','$created_date')";
		$res1= mysql_query($sqlDV);
		$sqlCV="INSERT INTO ".DEVIT_VOUCHAR_TBL."(voucher_no,project_id,transaction_type,vouchar_type,transaction_name,debit,paid_amount,due,list_view,created_by,created_date,status) 
		VALUES('$voucher_no','$project_id','Production','Others Vouchar','Production','$net_payble','$net_payble','0','Hidden','$created_by','$created_date','1')";
		$res2=mysql_query($sqlCV);
		
		$sqlM="INSERT INTO ".PURCHASE_MASTER_TBL."(voucher_no,project_id,purchase_date,purchase_type,total_value,net_payble,paid_amount,due,item_received_amount,created_by,created_date) 
		VALUES('$voucher_no','$project_id','$purchase_date','Production','$net_payble','$net_payble','$net_payble','0','$net_payble','$created_by','$created_date')";
		$res3=mysql_query($sqlM);
		
		$sqlD="INSERT INTO ".PURCHASE_DETAILS_TBL."(voucher_no,project_id,catagory,brand_id,product,m_unit,unit_price,qty,rec_qty,total,created_by) 
		VALUES('$voucher_no','$project_id','$catagory','$brand_id','$product','$m_unit','$unit_price','$qty','$qty','$net_payble','$created_by')";
		$res4=mysql_query($sqlD);
		
		return $voucher_no;
	}
	function getProductionMasterInfo($id){		   
		$project_id     = getFromSession('project_id');  
		$info           = array();    
		$info['table']  = PRODUCTION_MASTER_TBL.' pm,'.DELIVERY_POINT_TBL.' st,'.FACTORY_TBL.' f,'.PROJECT_TBL.' pa,'.PRODUCT_TBL.' p,'.CURRENCY_TBL.' c';	
		$info['fields'] = array('pm.production_id','pm.batch_no','pm.version_no','pa.project_name','pa.location','pm.total_value',"DATE_FORMAT(pm.used_date,'%d %b %y' ) as used_date",'c.curr_symble','st.delivery_point_name as out_store','f.factory_name','pm.created_time');
		
		$sql="pm.factory_id = f.factory_id  AND pm.out_store_id = st.delivery_pid  AND pm.project_id = pa.project_id AND pm.currency = c.currency_id AND pm.project_id = '".$project_id."' AND pm.production_id = '$id'";
							
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
  function showEditor4ProductionDetails($msg = null) {    
	  $data                				= array();
	  $data['cmd']         				= getRequest('cmd');
	  $data['record_list'] 				= $this->getProductionList(getRequest('from'),getRequest('to'));
	  $data['totalrecord']				= $this->getTotalProductionList();	
	  require_once(SHOW_PRODUCTION_DETAILS_SKIN); 
	  return $data[0];

   }
   function getProductionList($from,$to){		
		if($from == "" && $to == ""){$from=0; $to=40;}
		//$production_type = getRequest('production_type'); 
		$production_type = "Finish";  
		$project_id     = getFromSession('project_id');  
		$info           = array();    
		$info['table']  = PRODUCTION_MASTER_TBL.' pm,'.PROJECT_TBL.' pa,'.PRODUCT_TBL.' p,'.FACTORY_TBL.' f,'.CURRENCY_TBL.' c';	
		$info['fields'] = array('pm.production_id','pm.batch_no','pm.finish_product','pa.project_name','pa.location','f.factory_name ','f.address','pm.total_value','pm.production_amount','pm.unit_price','pm.sales_price','pm.m_unit',
		'p.product_name','pm.target_qty','pm.finish_qty',"DATE_FORMAT(pm.used_date,'%d %b %y' ) as used_date",'pm.production_type','c.curr_symble','pm.created_time');
		
		$sql="pm.finish_product = p.product_id AND pm.project_id = pa.project_id AND pm.factory_id = f.factory_id AND pm.currency = c.currency_id AND pm.project_id = '".$project_id."' AND
		 pm.production_type='".$production_type."'";							
		$info['where']   = $sql;	  	
	    $info['orderby'] = array("pm.production_id asc LIMIT $from,$to");
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
		//$production_type = getRequest('production_type');  
	   $production_type = "Finish";  
	   $project_id     = getFromSession('project_id');  
	   $info           = array();    
	   $info['table']  = PRODUCTION_MASTER_TBL.' pm,'.PROJECT_TBL.' pa,'.PRODUCT_TBL.' p,'.CURRENCY_TBL.' c';	
	   $info['fields'] = array('pm.production_id');		
	   $sql="pm.finish_product = p.product_id AND pm.project_id = pa.project_id AND pm.currency = c.currency_id AND pm.project_id = '".$project_id."' AND pm.production_type='".$production_type."'";							
	   $info['where']   = $sql;	  	
	   $info['orderby'] = array("pm.production_id");
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
   function showEditor4FinishProduction($msg = null) {      
	 require_once(CLASS_DIR.'/common.class.php');	
	 $comApp = new Common(); 
	 require_once(CLASS_DIR.'/common.list.class.php');	
	 $comListApp 	= new CommonList(); 
	   
	 $Product_id 	= getRequest('finish_product');	
	 $production_id = getRequest('production_id');	  
	 $data               = array();		
	 if($Product_id){
	 $TBDArr			= $comApp->getRecordInfo(PRODUCT_TBL,"Product_id",$Product_id);      
	 $TBDArr 			= parseThisValue($TBDArr);
	 $PMDArr			= $comApp->getRecordInfo(PRODUCTION_MASTER_TBL,"production_id",$production_id);      
	 $PMDArr 			= parseThisValue($PMDArr);
	 $data        		= array_merge(array(),$TBDArr,$PMDArr); 
	}	 
	if(getRequest('submit')){
		$this->saveFinishProduction();
	}
	$data['cmd']         	= getRequest('cmd'); 	     	
	$data['depo_list'] 		= $comListApp->getDeliveryPointList();  
	require_once(FINISH_PRODUCTION_SKIN_FILE); 
	return $data[0];
   }  
   function saveFinishProduction(){
     $store_id = getRequest('store_id');
   	 $project_id 		= getFromSession('project_id');
  	 $production_id 	= getRequest('production_id');
  	 $finish_product 	= getRequest('finish_product');
	 $finish_qty 		= getRequest('finish_qty');
	 $m_unit 		    = getRequest('m_unit');
	 $unit_price 		= getRequest('unit_price');
	 $total_value 		= getRequest('total_value');
	 $production_amount = getRequest('production_amount');
	 $target_qty		= getRequest('target_qty');
	 $totalFCR  		= $this->getTotalCreditStock($finish_product,getFromSession('project_id'));
	 $totalFDR  		= $this->getTotalDebitStock($finish_product,getFromSession('project_id'));					 
	 $balanceF  		= (($totalFDR+$finish_qty) - $totalFCR);	
	 $production_date 	= formatDate(getRequest('finish_date'));
	 
	 $net_payble=$total_value; $purchase_date = $production_date;
	 $Prosql ="SELECT catagory,brand_code,m_unit FROM ".PRODUCT_TBL." WHERE product_id='$finish_product' AND project_id= '$project_id'";
	 $Prorow 	= mysql_fetch_object(mysql_query($Prosql));
	 $catagory = $Prorow->catagory; $brand_id 	= $Prorow->brand_code; $m_unit 	= $Prorow->m_unit;
	 $voucher_no= $this->saveInPurchaseTbl($net_payble,$purchase_date,$catagory,$brand_id,$finish_product,$m_unit,$finish_qty,$unit_price); 
	
	$this->saveStockJournal($voucher_no,$production_id,$project_id,$store_id,$finish_product,"Sales Item",$unit_price,$m_unit,$finish_qty,0,$balanceF,$production_date);		  					
	 
	 $sql="UPDATE ".PRODUCTION_MASTER_TBL." SET finish_qty='$finish_qty',production_type='Finish',production_amount='$total_value',finish_date='".$production_date."' WHERE 
	 production_id='".$production_id."'";
	 $res = mysql_query($sql);
	 header("location:index.php?app=production&cmd=pro_dtl"); 
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
   function saveStockJournal($voucher_no,$pvoucher_no,$project_id,$store_id,$product_id,$product_type,$unit_price=NULL,$m_unit,$DR=NULL,$CR=NULL,$balance,$create_date=NULL){
		$sql = "INSERT INTO ".STOCK_LEDGER_TBL." (voucher_no,po_no,project_id,store_id,product_id,product_type,unit_price,m_unit,dr,cr,balance,create_date) VALUES('".$voucher_no."','".$pvoucher_no."','".$project_id."','".$store_id."','".$product_id."','".$product_type."','".$unit_price."','".$m_unit."','".$DR."','".$CR."','".$balance."','".$create_date."')";
		mysql_query($sql); 

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