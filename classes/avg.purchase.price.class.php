<?php
class AVGPurchasePrice
{
   function run() {     
		$cmd    = getRequest('cmd');
		$u_t_id = getFromSession('u_type_id');
		if($u_t_id == 101 || $u_t_id == 102 || $u_t_id == 103) 
		{      
		  switch($cmd) { 		 
      	   	 case 'doUpdate'           	: $screen = $this->showEditor($msg); break;
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
	
	 $data              = array();		 
	$data['cmd']     	= getRequest('cmd'); 	
	
	$project_id     = getFromSession('project_id');   
	$posql= "SELECT product_id,unit_price FROM ".PRODUCT_TBL." WHERE project_id = '$project_id' ORDER BY  product_id ";
	$pores = mysql_query($posql);
	while($arow=mysql_fetch_object($pores)){	
	    $product_id = $arow->product_id;
		$unit_price = $arow->unit_price;
		$sql= "SELECT verify_no as voucher_no  FROM ".STOCK_VERIFY_DETAILS_TBL." WHERE project_id = '$project_id' AND product='$product_id' GROUP BY product ";
		$res = mysql_query($sql);
		while($arow=mysql_fetch_object($res)){		
			$voucher_no = $arow->voucher_no;
			$this->saveAVGPurchasePrice($voucher_no,$project_id,$product_id,$unit_price);
		}
	}
	echo "Successfully Done.";	
   }   
     
   function saveAVGPurchasePrice($voucher_no,$project_id,$product_id,$purchase_price){	
		$sql = "INSERT INTO ".NDB_NAME.".`avg_purchase_price` (voucher_no,project_id,product_id,purchase_price) 
		VALUES('".$voucher_no."','".$project_id."','".$product_id."','".$purchase_price."')"; 
		$ires = mysql_query($sql);
		$avg_purchase_price	=0; 
		if($ires){
			$Prosql 		= "SELECT purchase_price  FROM ".NDB_NAME.".`avg_purchase_price` WHERE product_id = '$product_id' AND project_id = '$project_id' ORDER BY `id` DESC LIMIT 0 , 2";
			$pres 			= mysql_query($Prosql);
			$ttl_product 	= mysql_num_rows($pres);
			if($ttl_product >0){
				while($prow = mysql_fetch_object($pres)){
					$avg_purchase_price += $prow->purchase_price;
				}		
				$avg_purchase_price = ($avg_purchase_price / $ttl_product);
			}
			if(intval($avg_purchase_price)==""){ $avg_purchase_price=0;}			
			
			if($avg_purchase_price ==0){
				$avg_purchase_price = $purchase_price;
			}
			$USQL 	= "UPDATE ".NDB_NAME.".product SET purchase_unit_price = $avg_purchase_price WHERE product_id = '$product_id' AND project_id = '$project_id'";
			mysql_query($USQL);
		}
   }
       
} // End class
?>