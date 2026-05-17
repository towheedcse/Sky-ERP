<?php
class ProductYearEnding
{
   function run() {   // Set phpini - max_input_time = -1  
		$cmd    = getRequest('cmd');
		$u_t_id = getFromSession('u_type_id');
		if($u_t_id == 101) 
		{      
		  switch($cmd) { 
		  	case 'add'             : $screen = $this->showEditor($msg); break;
			case 'verify'          : $screen = $this->showVerifyEditor($msg); break;
      	     		case 'edit'            : $screen = $this->showEditor("Edit Page");break;
      	   	 	case 'doUpdate'        : $screen = $this->showEditor($msg); break;
		     	case 'delete'          : $screen = $this->deleteItem(); break;
		     	case 'del-dublicate'   : $screen = $this->delDublicateStock(); break;
			default                : $cmd = 'list'; $screen = $this->showEditor($msg);   break;
		  }
		}else {
      		header("location:index.php?app=user_home&msg=You are not authorised !!!");
      	} 
		
		return true;
  }
  function delDublicateStock(){	 
	 
	echo "===== Start Opening =====";
	$SQL="SELECT `voucher_no`, count(`stock_id`) as ttl FROM ".NDB_NAME.".`stock_ledger` WHERE `delivery_id`>0 GROUP BY `voucher_no`,`product_id` ORDER BY `ttl` DESC LIMIT 0 , 255";
	$Pres 		= mysql_query($SQL); $sl=1;
	 while($Prorow 	= mysql_fetch_object($Pres)){	 
		 $voucher_no      = $Prorow->voucher_no;

		 $SQL2="SELECT delivery_id FROM ".NDB_NAME.".`stock_ledger` WHERE `voucher_no` = '$voucher_no' GROUP BY `voucher_no`,`delivery_id` ORDER BY `delivery_id` ASC";
		$Pres2 		= mysql_query($SQL2); 
	 	while($Prorow2 	= mysql_fetch_object($Pres2)){	 
		 $delivery_id      = $Prorow2->delivery_id;
			$sql3 = "SELECT * FROM ".NDB_NAME.".account_journal WHERE voucher_no = '$voucher_no' AND delivery_id='$delivery_id'";
			$res3 = mysql_query($sql3);
			if(mysql_num_rows($res3) ==0){
			  $DSQL="DELETE FROM ".NDB_NAME.".`stock_ledger` WHERE voucher_no = '$voucher_no' AND `delivery_id` IN($delivery_id)";
			  mysql_query($DSQL);
			}else{
			  break;
			}
		}
		  
	}
  }
  function showEditor()
  {
	require_once(CLASS_DIR.'/common.class.php');	
	$comApp = new Common(); 
	require_once(CLASS_DIR.'/common.list.class.php');	
	$comListApp 	= new CommonList(); 
	$product_id 	= getRequest('id');	 
	 
	echo "===== Start Opening =====";
	 
	//$this->saveOpeningStock(); // Process	previous year
	//$this->saveOpeningNewDeliveryStock(); // Process new year from previous db 
	
	/*
	$project_id    = getFromSession('project_id'); 
	echo $Prosql = "SELECT detail_id,batch_no,`production_type`,finish_product FROM ".NDB_NAME.".`production_fg` WHERE project_id = '$project_id' GROUP BY `detail_id`  ORDER BY `detail_id` DESC"; 
	
	 $Pres 		= mysql_query($Prosql); $sl=7527; // max id
	 while($Prorow 	= mysql_fetch_object($Pres)){	 	 
		 $detail_id       = $sl; 		 
		 $voucher_no      = $Prorow->batch_no;		 
		 $product_id      = $Prorow->finish_product;		 
		 $production_type = $Prorow->production_type;
		 $fgsql="UPDATE ".NDB_NAME.".`production_fg` SET  detail_id='$detail_id' WHERE detail_id='$Prorow->detail_id' AND batch_no='$voucher_no' AND finish_product='$product_id'"; 
		 mysql_query($fgsql); $sl--; 
	}
	*/
	
	/*
	$project_id  = getFromSession('project_id'); 
	echo $Prosql = "SELECT stock_id,voucher_no,`store_id`,product_id FROM ".NDB_NAME.".`stock_ledger` WHERE project_id = '$project_id' GROUP BY `stock_id`  ORDER BY `stock_id` DESC"; 
	
	 $Pres 		= mysql_query($Prosql); $sl=13528; // max id
	 while($Prorow 	= mysql_fetch_object($Pres)){	 	 
		 $stock_id        = $sl; 		 
		 $voucher_no      = $Prorow->voucher_no;		 
		 $product_id      = $Prorow->product_id;		 
		 $store_id 	  = $Prorow->store_id;
		 $fgsql="UPDATE ".NDB_NAME.".`stock_ledger` SET  stock_id='$stock_id' WHERE stock_id='$Prorow->stock_id' AND voucher_no='$voucher_no' AND store_id='$store_id' AND product_id='$product_id'"; 
		 mysql_query($fgsql); $sl--; 
	}
	*/
	echo "==== Done ======";
	
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
    
   function StockOpening(){
	 require_once(CLASS_DIR.'/common.list.class.php');	
	 $comListApp 	= new CommonList();  
	 require_once(CLASS_DIR.'/fg.production.class.php');	
	 $fgApp 	= new FGProduction(); 
	 $created_date  = "2017-12-31"; 
	 $StockId 	= $comListApp->getStockId(getFromSession('project_id'));
	 $voucher_no 	= $this->createVoucharID();
	 $totalStockCr  = $fgApp->getTotalCreditAmount($StockId,getFromSession('project_id'));
	 $totalStockDr  = $fgApp->getTotalDebitAmount($StockId,getFromSession('project_id'));	 
	 $StockBalance  = ($totalStockDr-$totalStockCr);	
	 $description = "Opening Stock 01 Jan 2022";	
	 
	 $sqlCV="INSERT INTO ".NDB_NAME.".credit_vouchar (voucher_no,account_head,project_id,head_type,mode_of_payment,vouchar_type,transaction_name,
	credit,description,list_view,created_by,created_date) VALUES('$voucher_no','A000014','$project_id','Opening Balance','Others',
	'Others Vouchar','OB','0','OB','Hidden','$created_by','$created_date')";
	$res1= mysql_query($sqlCV);
	$sqlDV="INSERT INTO ".NDB_NAME.".cs_delivery_product(voucher_no,account_head,project_id,head_type,mode_of_payment,vouchar_type,
	transaction_name,debit,paid_amount,due,description,list_view,created_by,created_date,status) 
	VALUES('$voucher_no','$StockId','$project_id','Stock','Others','Others Vouchar','OB','0','0','0',
	'OB','Hidden','$created_by','$created_date','1')";
	$res2=mysql_query($sqlDV);	 
	//======== Stock Dr ========	 			 
	//$this->saveAccJournal($voucher_no,$StockId,"Stock","Opening Stock",getFromSession('project_id'),$description,$StockBalance,0,$StockBalance,0,$created_date,0); 
   }
   
   function createVoucharID()
   {
      $info = array();
      $info['table'] = NDB_NAME.".cs_delivery_product";
      $info['fields'] = array('max(voucher_no) as maxvoucher');
      $res = select($info);
      $maxvoucherId = 'D0000000';
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
      $maxvoucherId = generateID("D",$maxvoucherId,8);
      return $maxvoucherId;
   }
   function saveOpeningStock(){
     require_once(CLASS_DIR.'/common.list.class.php');	
	 $comListApp 	= new CommonList();    
	 require_once(CLASS_DIR.'/fg.production.class.php');	
	 $fgApp 		= new FGProduction(); 
	 //DELETE FROM `stock_ledger` WHERE `note` LIKE 'Opening Stock' AND `create_date` = '2021-12-31';
	 //DELETE FROM `production_fg` WHERE `production_type` LIKE 'OP' AND production_date ="2021-12-31" 
	 //DELETE FROM `avg_purchase_price`;
	 $project_id    = getFromSession('project_id');   
	 echo $Prosql 	= "SELECT * FROM ".STOCK_STATUS_BY_DATE_VIEW." WHERE project_id = '$project_id' GROUP BY `product_id` , `store_id`";
	 $created_date  = "2021-12-31"; 
	 $Pres 		= mysql_query($Prosql);
	 while($Prorow 	= mysql_fetch_object($Pres)){
	 	 
		 $product = $Prorow->product_id; $catagory = $Prorow->catagory; $brand_id = $Prorow->brand_code; $m_unit = $Prorow->m_unit;		 
		 $store_id = $Prorow->store_id;	$product_type = $Prorow->product_type;	 
		 if($Prorow->store_id==""){ $store_id = "D0010"; }else{ $store_id = $Prorow->store_id;}
		 
		 $SQL1= "SELECT * FROM ".PRODUCT_TBL." WHERE `product_id` = '$product' AND project_id='$project_id'";
		$res1= mysql_query($SQL1);
		$arow=mysql_fetch_object($res1);
		$unit_price = $arow->unit_price;

		 //$totalFCR  	= $this->getTotalCreditStock($product,$project_id,$store_id,$created_date);
		 $closing_qty  	= $this->getStockBalance($product,$project_id,$store_id,$created_date);
		 //$closing_qty = ($totalFDR - $totalFCR);
		 $opening_qty   = abs($closing_qty);
		 $total_value 	= ($opening_qty*$unit_price);
		 $production_id = $this->createFGBatchNo();
		 $factory_id 	= "FY005";
		 $created_date  = "2021-12-31"; 
		 $created_by    = getFromSession('userid');
		 $production_date = "2021-12-31"; 
		 $fgsql="INSERT INTO  ".NDB_NAME.".production_fg (batch_no,project_id,factory_id,store_id,production_date,production_type,finish_product,catagory,brand_code,unit_price,production_qty,m_unit,
		 total_value,currency,created_by,created_time) VALUES('$production_id','$project_id','$factory_id','$store_id','$production_date','OP','$product','$catagory',
		 '$brand_id','$unit_price','$opening_qty','$m_unit','$total_value','1','$created_by','$created_date')";
		 $fgres=mysql_query($fgsql);
		
		 $production_amount = ($opening_qty*$unit_price);			  
		 $net_payble=$production_amount; $purchase_date = "2021-12-31"; 
		 $voucher_no = $production_id;
		 // No Need. $this->saveInPurchaseTbl($voucher_no,$store_id,$net_payble,$purchase_date,$catagory,$brand_id,$product,$m_unit,$closing_qty,$unit_price);
		 if($closing_qty >= 0){		 
		 $this->saveStockJournal($voucher_no,$production_id,$project_id,$store_id,$product,$product_type,"Opening Stock",$unit_price,$m_unit,$closing_qty,0,$closing_qty,$production_date);		 		  
		 }else{
		  $this->saveStockJournal($voucher_no,$production_id,$project_id,$store_id,$product,$product_type,"Opening Stock",$unit_price,$m_unit,0,$opening_qty,$closing_qty,$production_date); 
		}
		
		$dsql = "DELETE FROM ".NDB_NAME.".avg_purchase_price WHERE project_id = '".$project_id."' AND product_id='".$product."'";
		mysql_query($dsql);
			
		$sql3 = "INSERT INTO ".NDB_NAME.".avg_purchase_price (voucher_no,project_id,product_id,purchase_price) VALUES('".$voucher_no."','".$project_id."','".$product."','".$unit_price."')"; 
		$ires = mysql_query($sql3); 
		 
	 }
	 
	 echo "==== Done =======";
   }

   function saveOpeningNewDeliveryStock(){
     require_once(CLASS_DIR.'/common.list.class.php');	
	 $comListApp 	= new CommonList();    
	 require_once(CLASS_DIR.'/fg.production.class.php');	
	 $fgApp 		= new FGProduction(); 
	 //DELETE FROM `stock_ledger` WHERE `note` LIKE 'Opening Sales Delivery Stock' AND `create_date` = '2022-01-01';
	 //DELETE FROM `production_fg` WHERE `production_type` LIKE 'OP' AND production_date ="2022-01-01" 
	 
	 $project_id    = getFromSession('project_id');   
	 echo $Prosql 		= "SELECT `product_id`,`stock_id`,SUM(`dr`) as dr,SUM(`cr`) as cr FROM ".STOCK_LEDGER_TBL." WHERE project_id = '$project_id' AND create_date ='2022-01-01' GROUP BY `product_id` , `store_id` ORDER BY `product_id` , `store_id` ASC";
	 $created_date  = "2022-01-01"; 
	 $Pres 		= mysql_query($Prosql); $sl=0;
	 while($Prorow 	= mysql_fetch_object($Pres)){	 	 
		 $product     = $Prorow->product_id; 		 
		 $store_id    = $Prorow->store_id;	
		 $closing_qty = $Prorow->cr;		 
		 if($Prorow->store_id==""){ $store_id = "D0010"; }else{ $store_id = $Prorow->store_id;}
		 
		 $SQL1= "SELECT * FROM ".PRODUCT_TBL." WHERE `product_id` = '$product' AND project_id='$project_id'";
		$res1= mysql_query($SQL1);
		$arow=mysql_fetch_object($res1);
		$catagory = $arow->catagory; $brand_id = $arow->brand_code; $m_unit = $arow->m_unit;		
		$product_type = $arow->product_type;
		$unit_price = $arow->unit_price;

		 $total_value 	= ($closing_qty*$unit_price);
		 $production_id = $this->createFGBatchNo();
		 $factory_id 	= "FY005";
		 $created_date  = "2022-01-01"; 
		 $created_by    = getFromSession('userid');
		 $production_date = "2022-01-01"; 
		 $fgsql="INSERT INTO  ".NDB_NAME.".production_fg (batch_no,project_id,factory_id,store_id,production_date,production_type,finish_product,catagory,brand_code,unit_price,production_qty,m_unit,
		 total_value,currency,created_by,created_time) VALUES('$production_id','$project_id','$factory_id','$store_id','$production_date','OP','$product','$catagory',
		 '$brand_id','$unit_price','$closing_qty','$m_unit','$total_value','1','$created_by','$created_date')";
		 $fgres=mysql_query($fgsql);
		
		 $production_amount = ($closing_qty*$unit_price);			  
		 $net_payble=$production_amount; $purchase_date = "2022-01-01"; 
		 $voucher_no = $production_id;
		 
		 if($closing_qty > 0){	// cr	 
		 $this->saveStockJournal($voucher_no,$production_id,$project_id,$store_id,$product,$product_type,"Opening Sales Delivery Stock",$unit_price,$m_unit,0,$closing_qty,$closing_qty,$production_date); 		 $sl++;		  
		 }		
		 
		 
	 }
	 
	 echo "==== Done $sl Stock=======";
   }

   function saveInPurchaseTbl($voucher_no,$store_id,$net_payble,$purchase_date,$catagory,$brand_id,$product,$m_unit,$qty,$unit_price){
		$created_date   = "2017-12-31"; 
		$project_id     = getFromSession('project_id');    
	    $created_by     = getFromSession('userid');		
				
		$sqlM="INSERT INTO ".NDB_NAME.".purchase_master (voucher_no,project_id,store_id,purchase_date,purchase_type,total_value,net_payble,paid_amount,due,item_received_amount,created_by,created_date) 
		VALUES('$voucher_no','$project_id','$store_id','$purchase_date','Production','$net_payble','$net_payble','$net_payble','0','$net_payble','$created_by','$created_date')";
		$res1=mysql_query($sqlM);
		
		$sqlD="INSERT INTO ".NDB_NAME.".purchase_details (voucher_no,project_id,catagory,brand_id,product,m_unit,unit_price,qty,rec_qty,total,created_by) 
		VALUES('$voucher_no','$project_id','$catagory','$brand_id','$product','$m_unit','$unit_price','$qty','$qty','$net_payble','$created_by')";
		$res2=mysql_query($sqlD);
		
		$dsql = "DELETE FROM ".NDB_NAME.".avg_purchase_price WHERE project_id = '".$project_id."' AND product_id='".$product."'";
		mysql_query($dsql);
			
		$sql3 = "INSERT INTO ".NDB_NAME.".avg_purchase_price (voucher_no,project_id,product_id,purchase_price) VALUES('".$voucher_no."','".$project_id."','".$product."','".$unit_price."')"; 
		$ires = mysql_query($sql3); 
			
		if(($res1) && ($res2)){ 
		return true;
		}else{ return false;} 
	} 
	
	function saveAccJournal($voucher_no,$sub_id,$head_type,$transaction_type,$project_id,$description,$DR=NULL,$CR=NULL,$balance,$status,$created_date,$delivery_id=NULL){
		$head_type	= getHeadType($sub_id);   $created_by = getFromSession('userid'); if($delivery_id==""){ $delivery_id=0;}
		$sql = "INSERT INTO ".NDB_NAME.".account_journal (voucher_no,delivery_id,created_date,sub_id,head_type,transaction_type,project_id,description,dr,cr,balance,status,created_by)
		 VALUES('".$voucher_no."','".$delivery_id."','".$created_date."','".$sub_id."','".$head_type."','".$transaction_type."','".$project_id."','".$description."','".$DR."','".$CR."','".$balance."','".$status."','".$created_by."')";
		//mysql_query($sql); 
	}
	
	function saveStockJournal($voucher_no,$pvoucher_no,$project_id,$store_id,$product_id,$product_type,$note,$unit_price=NULL,$m_unit,$DR=NULL,$CR=NULL,$balance,$create_date=NULL){
    $created_by = getFromSession('userid');
	$sql = "INSERT INTO ".NDB_NAME.".stock_ledger (voucher_no,po_no,project_id,store_id,product_id,product_type,note,unit_price,m_unit,dr,cr,balance,created_by,create_date)
	 VALUES('".$voucher_no."','".$pvoucher_no."','".$project_id."','".$store_id."','".$product_id."','".$product_type."','".$note."','".$unit_price."','".$m_unit."','".$DR."','".$CR."','".$balance."','".$created_by."','".$create_date."')";
	mysql_query($sql); 
   }  
   
   function createFGBatchNo() {
   	  $project_id  	  = getFromSession('project_id');
      $info = array();
      $info['table'] = NDB_NAME.".production_fg";
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
   
   function getTotalCreditAmount($acc_head,$project_id){
   		$sql = "SELECT sum(`cr`) as credit_amount FROM ".NDB_NAME.".account_journal WHERE sub_id = '$acc_head' AND project_id = '$project_id'";
		$row = mysql_fetch_object(mysql_query($sql));
		$credit_amount = $row->credit_amount;
		if(empty($credit_amount)){
			$credit_amount = 0;
		}
		return $credit_amount;
   }
   
   function getTotalDebitAmount($acc_head,$project_id){
   		$sql = "SELECT sum(`dr`) as debit_amount FROM ".NDB_NAME.".account_journal WHERE sub_id = '$acc_head' AND project_id = '$project_id'";
		$row = mysql_fetch_object(mysql_query($sql));
		$debit_amount = $row->debit_amount;
		if(empty($debit_amount)){
			$debit_amount = 0;
		}
		return $debit_amount;
   } 
   
   function getStockBalance($acc_head,$project_id,$store_id,$created_date){	
	if(empty($project_id)){
	  $project_id = getFromSession('project_id');
	} 
	$sql = "SELECT (sum(`dr`) - sum(`cr`)) as balance_amount FROM ".STOCK_LEDGER_TBL." WHERE product_id = '$acc_head' AND project_id = '$project_id' AND store_id='$store_id' AND create_date <='$created_date'";
	
	$row = mysql_fetch_object(mysql_query($sql));
	$balance_amount = $row->balance_amount;
	
	if(empty($balance_amount)){
		$balance_amount = 0;
	}
	return $balance_amount;
   }
   function getTotalCreditStock($acc_head,$project_id,$store_id,$created_date){
   		$sql = "SELECT sum(`cr`) as credit_amount FROM ".STOCK_LEDGER_TBL." WHERE product_id = '$acc_head' AND project_id = '$project_id' AND store_id='$store_id' AND create_date <='$created_date'";
		$row = mysql_fetch_object(mysql_query($sql));
		$credit_amount = $row->credit_amount;
		if(empty($credit_amount)){
			$credit_amount = 0;
		}
		return $credit_amount;
   }
   
   function getTotalDebitStock($acc_head,$project_id,$store_id,$created_date){
   		$sql = "SELECT sum(`dr`) as debit_amount FROM ".STOCK_LEDGER_TBL." WHERE product_id = '$acc_head' AND project_id = '$project_id' AND store_id='$store_id' AND create_date <='$created_date'"; 
		$row = mysql_fetch_object(mysql_query($sql));
		$debit_amount = $row->debit_amount;
		if(empty($debit_amount)){
			$debit_amount = 0;
		}
		return $debit_amount;
   }
} // End class
?>
