<?php
class SalesOpening
{
   function run() {     
		$cmd    = getRequest('cmd');
		$u_t_id = getFromSession('u_type_id');
		if($u_t_id == 101) 
		{      
		  switch($cmd) { 
		  	 case 'add'                	: $screen = $this->showEditor($msg); break;
			 case 'set_area'                : $screen = $this->updateSalesArea($msg); break;	
			 default                   	: $cmd = 'list'; $screen = $this->showEditor($msg);   break;
		  }
		}else {
      		header("location:index.php?app=user_home&msg=You are not authorised !!!");
      	} 
		
		return true;
  }
  function showEditor()
  {
	 //$this->updateSalesOrderDate();
	 //$this->saveSalesOrder("2016-01-01");
	 echo "==Sales Move Done==";
	
   }

   function updateSalesOrderDate(){
  	$SQL="SELECT sm.`voucher_no` , sm.sales_date, sdm.`delivery_date`
	FROM ".SALES_MASTER_TBL." AS sm, ".SALES_DELIVERY_MASTER_TBL." sdm
	WHERE sm.`voucher_no` = sdm.`voucher_no`  AND sdm.`delivery_date`='2016-01-01'
	GROUP BY sm.`voucher_no`";
	$dvres 	= mysql_query($SQL);
	while($smrow = mysql_fetch_object($dvres)){
		$voucher_no = $smrow->voucher_no;
		$sales_date = $smrow->delivery_date;
		
		$USQL="UPDATE ".SALES_MASTER_TBL." SET sales_date = '$sales_date' WHERE `voucher_no` = '$voucher_no'";
		 mysql_query($USQL);			
	}
	echo "===Done===";

   }
  function updateSalesArea(){
  	$SQL="SELECT `voucher_no`, customer FROM ".SALES_MASTER_TBL." WHERE `delivery_date`>='2016-01-01' GROUP BY `voucher_no` ORDER BY `voucher_no` ASC";
	$dvres 	= mysql_query($SQL);
	while($smrow = mysql_fetch_object($dvres)){
		$voucher_no = $smrow->voucher_no;
		$customer = $smrow->customer;
		$CSql="SELECT division,district,area FROM ".SUB_ACC_HEAD_TBL." WHERE head_type='Customer' AND sub_id='$customer' ";
		$Crow = mysql_fetch_object(mysql_query($CSql));
		$division 	= $Crow->division;
		$district 	= $Crow->district;
		$area	    	= $Crow->area;

		$USQL="UPDATE ".SALES_MASTER_TBL." SET division = '$division',district = '$district',area = '$area' WHERE `voucher_no` = '$voucher_no'";
		mysql_query($USQL);			
	}

	$SQL="SELECT sm.`voucher_no`, sm.customer FROM ".SALES_DELIVERY_MASTER_TBL." AS sm, ".SALES_DELIVERY_CHALLAN_TBL." sd
	WHERE sm.`voucher_no` = sd.`voucher_no`  AND sm.`delivery_date`>='2016-01-01' GROUP BY sm.`voucher_no`";
	$dvres 	= mysql_query($SQL);
	while($smrow = mysql_fetch_object($dvres)){
		$voucher_no = $smrow->voucher_no;
		$customer = $smrow->customer;
		$CSql="SELECT division,district,area FROM ".SUB_ACC_HEAD_TBL." WHERE head_type='Customer' AND sub_id='$customer' ";
		$Crow = mysql_fetch_object(mysql_query($CSql));
		$division 	= $Crow->division;
		$district 	= $Crow->district;
		$area	    	= $Crow->area;

		$USQL="UPDATE ".SALES_DELIVERY_CHALLAN_TBL." SET division = '$division',district = '$district',area = '$area' WHERE `voucher_no` = '$voucher_no'";
		mysql_query($USQL);			
	}
	echo "===Done===";

   }
   //===== First Opening =====
      
   function createVoucher($voucher_no,$created_date){
	   
	$dvsql	= "SELECT * FROM ".DEVIT_VOUCHAR_TBL." WHERE voucher_no = '$voucher_no' ";
	$dvres 	= mysql_query($dvsql);
	$dvrow	= mysql_fetch_object($dvres);
	 
	$account_head  		= $dvrow->account_head;
	$project_id  		= $dvrow->project_id;
	$branch_id  		= $dvrow->branch_id;
	$headtype  			= $dvrow->head_type;
	$mode_of_payment  	= $dvrow->mode_of_payment;
	$transaction_type  	= $dvrow->transaction_type;
	$vouchar_type  		= $dvrow->vouchar_type;
	$transaction_name  	= $dvrow->transaction_name;
	$debit  			= $dvrow->debit;
	$description 		= $dvrow->description;
	$created_by 		= $dvrow->created_by;
	
	$sqlDV="INSERT INTO ".NDB_NAME.".cs_delivery_product (voucher_no,account_head,project_id,branch_id,head_type,mode_of_payment,transaction_type,
	vouchar_type,transaction_name,debit,description,created_by,created_date) VALUES('$voucher_no','$account_head','$project_id','$branch_id',
	'$headtype','$mode_of_payment','$transaction_type','$vouchar_type','$transaction_name','$debit','$description','$created_by','$created_date')";
	$res1= mysql_query($sqlDV);
	if($res1){	
	$cvsql= "SELECT * FROM ".CREDIT_VOUCHAR_TBL." WHERE voucher_no = '$voucher_no' ";
	$cvres = mysql_query($cvsql);
	$cvrow=mysql_fetch_object($cvres); 
	$account_head  		= $cvrow->account_head;
	$head_type  		= $cvrow->head_type;
	$project_id  		= $cvrow->project_id;
	$branch_id  		= $cvrow->branch_id;
	$mode_of_payment  	= $cvrow->mode_of_payment;
	$transaction_type  	= $cvrow->transaction_type;
	$vouchar_type  		= $cvrow->vouchar_type;
	$transaction_name  	= $cvrow->transaction_name;
	$credit  			= $cvrow->credit;
	$debit 				= $cvrow->debit;
	$description 		= $cvrow->description;
	$created_by 		= $cvrow->created_by;
	
	$sqlCV="INSERT INTO ".NDB_NAME.".credit_vouchar (voucher_no,account_head,head_type,project_id,branch_id,mode_of_payment,transaction_type,
	vouchar_type,transaction_name,credit,debit,description,created_by,created_date) VALUES('$voucher_no','$account_head','$head_type',
	'$project_id','$branch_id','$mode_of_payment','$transaction_type','$vouchar_type','$transaction_name','$credit','$debit',
	'$description','$created_by','$created_date')";
	$res2= mysql_query($sqlCV);
	}	
	
	if($res2){
		return true;	
	}else{
		return false;
	}
	
   }//EOF
   function deleteSalesOrder($voucher_no,$refNo){
	    mysql_query("START TRANSACTION;");	   	   
		if($voucher_no!=""){
		$this->deleteRecord(NDB_NAME.".sales_master","voucher_no",$voucher_no,"po_no","$refNo"); 
		$this->deleteRecord(NDB_NAME.".sales_details","voucher_no",$voucher_no); 
		$this->deleteRecord(NDB_NAME.".sales_delivery_item_master","voucher_no",$voucher_no);	  
		$this->deleteRecord(NDB_NAME.".sales_delivery_item","voucher_no",$voucher_no);			  
		$this->deleteRecord(NDB_NAME.".account_journal","voucher_no",$voucher_no);
		}
		mysql_query("COMMIT;");
   }
   function deleteRecord($TBL,$voucherNo,$voucherValue,$idName=NULL,$idValue=NULL){
	   $sql = "";
	   if($voucherNo !="" && $voucherValue !="" ){
		$sql = " BINARY $voucherNo = '$voucherValue' ";
	   }
	   
	   if($idName !="" && $idValue !="" ){
		$sql.= " AND BINARY $idName = '$idValue'";
	   }
	   
   	   if($sql != ""){		
      	$info = array();
      	$info['table'] = $TBL;
      	$info['where'] = $sql;
      	//$info['debug'] = true;
      	$res = delete($info);      	
      	if($res){
      	  return true;    	   
      	}else{
      	  return false;
      	}      	
      }
   }
   
   function saveSalesOrder($sales_date){
	$failed="";
	//$smsql = "SELECT * FROM ".SALES_MASTER_TBL." WHERE `sales_date` >= '2015-01-01'";
	$smsql = "SELECT * FROM ".SALES_MASTER_TBL." WHERE `sales_date` >= '$sales_date' ORDER BY voucher_no ASC";
	$smres = mysql_query($smsql);
	if(mysql_num_rows($smres)>0){ 
		while($smrow		= mysql_fetch_object($smres)){
		$refNo  			= $smrow->voucher_no;
		$wo_no  			= $smrow->wo_no;
		$project_id  		= $smrow->project_id;
		$district  			= $smrow->district;
		$area  				= $smrow->area;
		$delivery_point  	= $smrow->delivery_point;
		$customer  			= $smrow->customer;
		$salse_type  		= $smrow->salse_type;
		$order_type  		= $smrow->order_type;
		$sales_date  		= $smrow->sales_date;
		$delivery_date  	= $smrow->delivery_date;
		$total_value 		= $smrow->total_value;
		$mode_of_payment 	= $smrow->mode_of_payment;
		$gd_percent 		= $smrow->general_discount_percent;
		$gd_amount 			= $smrow->general_discount_amount;
		$ed_percent 		= $smrow->exclusive_discount_percent;
		$ed_amount 			= $smrow->exclusive_discount_amount;
		$additional_discount= $smrow->additional_discount;
		$product_discount 	= $smrow->product_discount;
		$discount 			= $smrow->discount;
		$net_payble 		= $smrow->net_payble;
		$paid_amount 		= $smrow->paid_amount;
		$adjust 			= $smrow->adjust;
		$delivery_amount 	= $smrow->item_delivery_amount;
		$due 				= $smrow->due;
		$description 		= $smrow->description;
		$description 		= str_replace('"',"&ldquo;",$description);
		$description 		= str_replace("'","&#8217;",$description);
				  
		$created_by 		= $smrow->created_by;
		$created_date 		= $smrow->created_date;
		 
		$nsmsql = "SELECT voucher_no FROM ".NDB_NAME.".sales_master WHERE BINARY `po_no` = '$refNo' ";
		$nres 	= mysql_query($nsmsql);
		if(mysql_num_rows($nres)>0){
			$nsmrow	= mysql_fetch_object($nres);
			$voucher_no = $nsmrow->voucher_no;
			$this->deleteSalesOrder($voucher_no,$refNo);
		}else{
	 		$voucher_no = $this->createOBSalesID();	
		}
		
		if($voucher_no!=""){   
		$sqlM="INSERT INTO ".NDB_NAME.".`sales_master` (voucher_no,po_no,wo_no,project_id,district,area,delivery_point,customer,salse_type,order_type,
		sales_date,delivery_date,total_value,mode_of_payment,general_discount_percent,general_discount_amount,exclusive_discount_percent,
		exclusive_discount_amount,additional_discount,product_discount,discount,net_payble,paid_amount,adjust,item_delivery_amount,due,description,
		created_by,created_date) VALUES('$voucher_no','$refNo','$wo_no','$project_id','$district','$area','$delivery_point','$customer','$salse_type',
		'$order_type','$sales_date','$delivery_date','$total_value','$mode_of_payment','$gd_percent','$gd_amount','$ed_percent','$ed_amount',
		'$additional_discount','$product_discount','$discount','$net_payble','$paid_amount','$adjust','$delivery_amount','$due','$description',
		'$created_by','$created_date')";
		$res3=mysql_query($sqlM); 
		if($res3){
			$this->saveSalesDetails($voucher_no,$refNo);			
			$this->saveSalesDeliveryMaster($voucher_no,$refNo);			
		}else{ $failed.=$refNo."|";} 
		}//END if voucher_no
		$voucher_no="";
		}//End While 
		
	}else{ // End Num Rows
		echo "Out";
	}
	if($failed!=""){ echo $failed."<br>";}
	
   }// EOF
   
   function saveSalesDetails($voucher_no,$refNo){
	
	$sdsql = "SELECT * FROM ".SALES_DETAILS_TBL." WHERE `voucher_no` = '$refNo' ";
	$sdres = mysql_query($sdsql);
	if(mysql_num_rows($sdres)>0){		
		while($smrow		= mysql_fetch_object($sdres)){
		$pvoucher_no  		= $smrow->pvoucher_no;
		$project_id  		= $smrow->project_id;
		$customer  			= $smrow->customer;
		$catagory  			= $smrow->catagory;
		$brand_id  			= $smrow->brand_id;
		$product  			= $smrow->product;
		$m_unit  			= $smrow->m_unit;
		$unit_price  		= $smrow->unit_price;
		
		$purchase_price  	= $smrow->purchase_price;
		$discount_per_qty  	= $smrow->discount_per_qty;
		$discount_amount 	= $smrow->discount_amount;
		$unit_profit 		= $smrow->unit_profit;
		$qty 				= $smrow->qty;
		$delivery_qty 		= $smrow->delivery_qty;
		$free_qty 			= $smrow->free_qty;
		$undelivery_qty 	= $smrow->undelivery_qty;
		$total				= $smrow->total;
		$district 			= $smrow->district;
		$area 				= $smrow->area;
		$created_by 		= $smrow->created_by;	
		  
		$sqlD="INSERT INTO ".NDB_NAME.".`sales_details` (voucher_no,pvoucher_no,project_id,customer,catagory,brand_id,product,details,m_unit,unit_price,
		purchase_price,discount_per_qty,discount_amount,unit_profit,qty,delivery_qty,free_qty,undelivery_qty,total,district,area,created_by) 
		VALUES('$voucher_no','$pvoucher_no','$project_id','$customer','$catagory','$brand_id','$product','$refNo','$m_unit','$unit_price',
		'$purchase_price','$discount_per_qty','$discount_amount','$unit_profit','$qty','$delivery_qty','$free_qty','$undelivery_qty','$total',
		'$district','$area','$created_by')";
		mysql_query($sqlD);		
		}// End While
		
		}// End Num rows
			
	}//EOF
	
   function saveSalesDeliveryMaster($voucher_no,$refNo){
	
	$sdmsql = "SELECT * FROM ".SALES_DELIVERY_MASTER_TBL." WHERE `voucher_no` = '$refNo' ";
	$sdmres = mysql_query($sdmsql);
	if(mysql_num_rows($sdmres) >0){		
		while($smrow		= mysql_fetch_object($sdmres)){
		$delivery_master_id	= $smrow->sales_delivery_master_id;
		$project_id  		= $smrow->project_id;
		$customer  			= $smrow->customer;
		$challan_no  		= $smrow->challan_no;
		$delivery_point  	= $smrow->delivery_point;
		$delivery_date  	= $smrow->delivery_date;
		$total_value  		= $smrow->total_value;
		$previour_balance  	= $smrow->previour_balance;		
		$roa  				= $smrow->roa;
		$created_by  		= $smrow->created_by;
		$created_date 		= $smrow->created_date;	
		  
		$sqlSD="INSERT INTO ".NDB_NAME.".`sales_delivery_item_master` (voucher_no,project_id,customer,challan_no,delivery_point,delivery_date,
		total_value,previour_balance,roa,created_by,created_date) 
		VALUES('$voucher_no','$project_id','$customer','$challan_no','$delivery_point','$delivery_date','$total_value','$previour_balance',
		'$roa','$created_by','$created_date')";
		$res = mysql_query($sqlSD);	
		$delivery_masterid = mysql_insert_id();
		if($res){
			if($total_value >0){ // === Dr ====	Opening Sales is OS		
			$totalPartyCR  = $this->getTotalCreditAmount($customer,getFromSession('project_id'));
			$totalPartyDR  = $this->getTotalDebitAmount($customer,getFromSession('project_id'));					 
			$PartyBalance  = (($totalPartyDR+$total_value)-$totalPartyCR);						 
			$this->saveAccountJournal($voucher_no,$delivery_masterid,$customer,"Customer",$project_id,"Opening Sales Delivery",$total_value,0,$PartyBalance,1,$delivery_date);
			}
			// ==== Save  Sales Delivery Details =====
			
			$sddmsql = "SELECT * FROM ".SALES_DELIVERY_CHALLAN_TBL." WHERE `voucher_no` = '$refNo' AND delivery_master_id=$delivery_master_id";
			$sddmres = mysql_query($sddmsql);
			if(mysql_num_rows($sddmres) >0){
				//===== Get Party Area Info New 2016=====
				$CSql="SELECT division,district,area FROM ".SUB_ACC_HEAD_TBL." WHERE sub_id ='".$customer."' AND project_id = '$project_id' AND head_type='Customer'";
				$CRes = mysql_query($CSql);
				if(mysql_num_rows($CRes) >0){
					$crow = mysql_fetch_object($CRes);	
					$division = $crow->division;	
					$district = $crow->district;	
					$area = $crow->area;
				}else{
					$CSql="SELECT division,district,area FROM ".SUPPLIER_TBL." WHERE supplier_code ='".$customer."' AND project_id = '$project_id'";
					$CRes = mysql_query($CSql);
					if(mysql_num_rows($CRes) >0){
						$crow = mysql_fetch_object($CRes);	
						$division = $crow->division;	
						$district = $crow->district;	
						$area = $crow->area;
					}
				}
										
				while($smrow		= mysql_fetch_object($sddmres)){
				$pvoucher_no		= $smrow->pvoucher_no;
				$delivery_point  	= $smrow->delivery_point;
				$project_id  		= $smrow->project_id;
				$catagory  			= $smrow->catagory;
				$brand_id  			= $smrow->brand_id;
				$product  			= $smrow->product;
				$m_unit  			= $smrow->m_unit;
				$unit_price  		= $smrow->unit_price;		
				$discount_per_qty  	= $smrow->discount_per_qty;
				$discount_amount  	= $smrow->discount_amount;				
				$overall_discount  	= $smrow->overall_discount;
				
				$overall_discount_amount = $smrow->overall_discount_amount;
				$unit_profit  		= $smrow->unit_profit;
				$delivery_qty  		= $smrow->delivery_qty;
				$total_bag  		= $smrow->total_bag;
				$total_amount  		= $smrow->total_amount;
				$created_by  		= $smrow->created_by;	
				  
				$sqlID="INSERT INTO ".NDB_NAME.".`sales_delivery_item` (delivery_master_id,voucher_no,pvoucher_no,delivery_point,project_id,catagory,brand_id,product,m_unit,
				unit_price,discount_per_qty,discount_amount,overall_discount,overall_discount_amount,unit_profit,delivery_qty,total_bag,total_amount,division,district,area,created_by) 
				VALUES('$delivery_masterid','$voucher_no','$pvoucher_no','$delivery_point','$project_id','$catagory','$brand_id','$product','$m_unit',
				'$unit_price','$discount_per_qty','$discount_amount','$overall_discount','$overall_discount_amount','$unit_profit','$delivery_qty','$total_bag','$total_amount','$division','$district','$area','$created_by')";
				mysql_query($sqlID);	
				
				}// End While
				
			}// End Num rows			
			
		}// End $res
		
		}// End While
		
		}// End Num rows
	
	}// EOF
	
	function saveAccountJournal($voucher_no,$delivery_id,$sub_id,$head_type,$project_id,$description,$DR=NULL,$CR=NULL,$balance,$status,$created_date){
		$created_by = getFromSession('userid');
		$sql = "INSERT INTO ".NDB_NAME.".account_journal (voucher_no,delivery_id,created_date,sub_id,head_type,transaction_type,
		project_id,description,dr,cr,balance,status,created_by) VALUES('".$voucher_no."','".$delivery_id."','".$created_date."','"
		.$sub_id."','".$head_type."','".$description."','".$project_id."','".$description."','".$DR."','".$CR."','".$balance."','"
		.$status."','".$created_by."')";
		mysql_query($sql); 
   }
   function getTotalCreditAmount($acc_head,$project_id){
   		$sql = "SELECT sum(`cr`) as credit_amount FROM ".NDB_NAME.".account_journal WHERE BINARY sub_id = '$acc_head' AND project_id = '$project_id'";
		$row = mysql_fetch_object(mysql_query($sql));
		$credit_amount = $row->credit_amount;
		if(empty($credit_amount)){
			$credit_amount = 0;
		}
		return $credit_amount;
   }
  
   function getTotalDebitAmount($acc_head,$project_id){
   		$sql = "SELECT sum(`dr`) as debit_amount FROM ".NDB_NAME.".account_journal WHERE BINARY sub_id = '$acc_head' AND project_id = '$project_id'";
		$row = mysql_fetch_object(mysql_query($sql));
		$debit_amount = $row->debit_amount;
		if(empty($debit_amount)){
			$debit_amount = 0;
		}
		return $debit_amount;
   } 
   function createOBSalesID()
   {
      $info = array();
      $info['table']  = NDB_NAME.".sales_master"; 
      $info['fields'] = array('max(voucher_no) as maxvoucher');
	  //$info['where']   = "status=0";
	  $info['where']   = "po_no !=''";
      $res = select($info);
      $maxvoucherId = 'S0000000';
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
      
      $maxvoucherId = generateID("S",$maxvoucherId,8);
      return $maxvoucherId;
   } 
     
} // End class
?>
