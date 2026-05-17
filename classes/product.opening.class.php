<?php
class ProductOpening
{
   function run() {     
		$cmd    = getRequest('cmd');
		$u_t_id = getFromSession('u_type_id');
		if($u_t_id == 101) 
		{      
		  switch($cmd) { 
		  	 case 'add'                	: $screen = $this->showEditor($msg); break;
			 case 'verify'              : $screen = $this->showVerifyEditor($msg); break;
      	     case 'edit'               	: $screen = $this->showEditor("Edit Page");    break;			 
      	   	 case 'doUpdate'           	: $screen = $this->showEditor($msg); break;
		     case 'delete'             	: $screen = $this->deleteItem(); break;
			 default                   	: $cmd = 'list'; $screen = $this->showEditor($msg);   break;
		  }
		}else {
      		header("location:index.php?app=user_home&msg=You are not authorised !!!");
      	} 
		
		return true;
   }
  function showEditor()
  {
	 require_once(CLASS_DIR.'/common.class.php');	
	 $comApp = new Common(); 
	 require_once(CLASS_DIR.'/common.list.class.php');	
	 $comListApp 	= new CommonList(); 
	 $product_id = getRequest('id');	 
	 $data               = array();		
	 if($product_id){
	 $TBDArr			= $comApp->getRecordInfo(PRODUCT_TBL,"product_id",$product_id);      
	 $TBDArr 			= parseThisValue($TBDArr);
	 $data        		= array_merge(array(),$TBDArr);		 			
	 if(getRequest('save')){
		$comApp->updateRecord(PRODUCT_TBL,"product_id",$product_id,"","","","","product.opening","list");
		$msg="Successfully Update Record !!!";
		header("location:?app=product.opening&cmd=list&msg=$msg");	      	
	 } 
	} else {		
	if(getRequest('save')) {
		$accessories_id = $comApp->NewID(PRODUCT_TBL,"product_id","P000000","P",7);
		$comApp->saveRecord(PRODUCT_TBL,"product_id",$accessories_id,"","","created_by","created_date","product.opening","list");
		$msg="Successfully Save Record !!!";
		header("location:?app=product.opening&cmd=list&msg=$msg");     		       		      	
	 }			 
	}
	$f1Value = getRequest('srckey');
	//$this->updateUnitPrice();
	//$this->saveOpeningStock(); // Process
	$data['product_list']  	= $comApp->getRecords(PRODUCT_TBL,"product_id","","product_name",$f1Value,"","",getRequest('from'),getRequest('to'));
	$data['totalrecord']  	= $comApp->getTotalRecords(PRODUCT_TBL,"product_id","","product_name",$f1Value,"",""); 
	$data['catagory_list']	= $comListApp->getCatagoryList();
	$data['brand_list'] 	= $comListApp->getBrandList();
	//$data['pcatagory_list'] = $comListApp->getProductCatagoryList();
	$data['pclass_list'] 	= $comListApp->getProductClassList();
	$data['message'] 		= $msg;
	$data['cmd']     		= getRequest('cmd'); 
	require_once(CURRENT_APP_SKIN_FILE);
	return $data[0];
   } 
   function showVerifyEditor()
  {
	 require_once(CLASS_DIR.'/common.class.php');	
	 $comApp = new Common(); 
	 require_once(CLASS_DIR.'/common.list.class.php');	
	 $comListApp 	= new CommonList(); 
	 $product_id = getRequest('id');	 
	 $data               = array();		
	 if($product_id){
	 $TBDArr			= $comApp->getRecordInfo(PRODUCT_TBL,"product_id",$product_id);      
	 $TBDArr 			= parseThisValue($TBDArr);
	 $data        		= array_merge(array(),$TBDArr);		 			
	 if(getRequest('save')){
		$comApp->updateRecord(PRODUCT_TBL,"product_id",$product_id,"verify_date","","","","product.opening","verify");
		$msg="Successfully Update Record !!!";
		header("location:?app=product.opening&cmd=verify&msg=$msg");	      	
	 } 
	}
	$f1Value = getRequest('srckey');
	$data['product_list']  	= $comApp->getRecords(PRODUCT_TBL,"product_id","","product_name",$f1Value,"","",getRequest('from'),getRequest('to'));
	$data['totalrecord']  	= $comApp->getTotalRecords(PRODUCT_TBL,"product_id","","product_name",$f1Value,"",""); 
	$data['catagory_list']	= $comListApp->getCatagoryList();
	$data['brand_list'] 	= $comListApp->getBrandList();
	//$data['pcatagory_list'] = $comListApp->getProductCatagoryList();
	$data['pclass_list'] 	= $comListApp->getProductClassList();
	$data['message'] 		= $msg;
	$data['cmd']     		= getRequest('cmd'); 
	require_once(PHYSICAL_VERIFY_SKIN_FILE);
	return $data[0];
   }   
   function deleteItem(){
	require_once(CLASS_DIR.'/common.class.php');	
	$comApp = new Common(); 
	$product_id = getRequest('id');
	$comApp->deleteRecord(PRODUCT_TBL,"product_id",$product_id,"product.opening","list"); 
   }
   //=========Opening Stock========= 
   function updateUnitPrice(){
   	$project_id    = getFromSession('project_id'); 
   	 $Prosql 		= "SELECT * FROM ".PRODUCT_TBL." WHERE project_id = '$project_id' ";
	 $Pres 			= mysql_query($Prosql);
	 while($Prorow 	= mysql_fetch_object($Pres)){	 
		$product_id = $Prorow->product_id;  $closing_rate	= $Prorow->closing_rate;
		$fgsql="UPDATE ".PRODUCT_TBL." SET  unit_price='$closing_rate' WHERE product_id='$product_id'";
		mysql_query($fgsql);
	}
   }  
   
   function saveOpeningStock(){
     require_once(CLASS_DIR.'/common.list.class.php');	
	 $comListApp 	= new CommonList();    
	 require_once(CLASS_DIR.'/fg.production.class.php');	
	 $fgApp 		= new FGProduction(); 
	 
	 $project_id    = getFromSession('project_id');   
	 $Prosql 		= "SELECT * FROM ".PRODUCT_TBL." WHERE project_id = '$project_id' AND product_type ='Sales Item'";
	 $Pres 			= mysql_query($Prosql);
	 while($Prorow 	= mysql_fetch_object($Pres)){
	 
		 $product 		= $Prorow->product_id; $catagory = $Prorow->catagory; $brand_id = $Prorow->brand_code; $m_unit = $Prorow->m_unit;
		 //$finish_qty	= $Prorow->verify_qty;
		 $finish_qty	= $Prorow->closing_qty;
		 $unit_price 	= $Prorow->closing_rate;
		 $total_value 	= ($finish_qty*$unit_price);
		 $production_id = $fgApp->createFGBatchNo();
		 $store_id 		= "D0010"; $factory_id = "F0001";
		 $created_date  = "2014-02-28"; 
		 $created_by    = getFromSession('userid');
		 $production_date = "2014-02-28"; 
		 $fgsql="INSERT INTO ".PRODUCTION_FG_TBL."(batch_no,project_id,factory_id,store_id,production_date,production_type,finish_product,catagory,brand_code,unit_price,production_qty,m_unit,
		 total_value,currency,created_by,created_time) VALUES('$production_id','$project_id','$factory_id','$store_id','$production_date','OP','$product','$catagory',
		 '$brand_id','$unit_price','$finish_qty','$m_unit','$total_value','1','$created_by','$created_date')";
		 $fgres=mysql_query($fgsql);
		
		 $production_amount = ($finish_qty*$unit_price);
		  
		  $totalFCR  = $fgApp->getTotalCreditStock($product,getFromSession('project_id'));
		  $totalFDR  = $fgApp->getTotalDebitStock($product,getFromSession('project_id'));					 
		  $balanceF  = (($totalFDR+$finish_qty) - $totalFCR);	
			  
		  $net_payble=$production_amount; $purchase_date = "2014-02-28"; 
		  $voucher_no = $production_id;
		  $pores = $this->saveInPurchaseTbl($voucher_no,$net_payble,$purchase_date,$catagory,$brand_id,$product,$m_unit,$finish_qty,$unit_price); 
		  $fgApp->saveStockJournal($voucher_no,$production_id,$project_id,$store_id,$product,"Sales Item","Opening Stock",$unit_price,$m_unit,$finish_qty,0,$balanceF,$production_date);
		  //=== Stock Dr =====
		  $StockAmount	= $total_value;
		  $StockId 	 = $comListApp->getStockId(getFromSession('project_id'));
		  $totalStockCr  = $fgApp->getTotalCreditAmount($StockId,getFromSession('project_id'));
		  $totalStockDr  = $fgApp->getTotalDebitAmount($StockId,getFromSession('project_id'));					 
		  $StockBalance  = (($totalStockDr+$StockAmount)-$totalStockCr);	$description = "Finish Goods Opening Jan-1 to Feb 28";				 
		  $comListApp->saveAccJournal($voucher_no,$StockId,"Stock","Finish Goods",getFromSession('project_id'),$description,$StockAmount,0,$StockBalance,0,$created_date,0);
	 }
   }
   function saveInPurchaseTbl($voucher_no,$net_payble,$purchase_date,$catagory,$brand_id,$product,$m_unit,$qty,$unit_price){
		$created_date   = "2014-02-28"; 
		$project_id     = getFromSession('project_id');    
	    $created_by     = getFromSession('userid');		
				
		$sqlM="INSERT INTO ".PURCHASE_MASTER_TBL."(voucher_no,project_id,purchase_date,purchase_type,total_value,net_payble,paid_amount,due,item_received_amount,created_by,created_date) 
		VALUES('$voucher_no','$project_id','$purchase_date','Production','$net_payble','$net_payble','$net_payble','0','$net_payble','$created_by','$created_date')";
		$res1=mysql_query($sqlM);
		
		$sqlD="INSERT INTO ".PURCHASE_DETAILS_TBL."(voucher_no,project_id,catagory,brand_id,product,m_unit,unit_price,qty,rec_qty,total,created_by) 
		VALUES('$voucher_no','$project_id','$catagory','$brand_id','$product','$m_unit','$unit_price','$qty','$qty','$net_payble','$created_by')";
		$res2=mysql_query($sqlD);
		if(($res1) && ($res2)){ 
		return true;
		}else{ return false;} 
	} 
} // End class
?>