<?php
class SalesOrderOpening
{
   function run() {     
		$cmd    = getRequest('cmd');
		$u_t_id = getFromSession('u_type_id');
		if($u_t_id == 101) 
		{      
		  switch($cmd) { 
			 case 'set.division'      : $this->showSetSalesDivisionEditor("Report Page");   break; 				 case 'movesales'      	  : $this->showMoveSalesEditor("Report Page");   break; 	 
			 case 'sales.opening.trt' : $this->showOPningByTRT("Report Page");   break;		 
			 case 'sales.cogs' 	  : $this->updateSalesCostPrice("Report Page");   break; 
			 case 'del.double.ledger' : $this->DeletePurDouble();   break;       
		  }
		}else {
      		header("location:index.php?app=user_home&msg=You are not authorised !!!");
      	} 
		
      return true;
  }
  function DeletePurDouble()
  {	
		//===== Delete Double Purchase =======
		$sqlm="SELECT id,`voucher_no`,`sub_id`,project_id,COUNT(`sub_id`) as double_hit FROM ".ACCOUNT_JOURNAL_TBL." WHERE `voucher_no` LIKE '%PI0%' AND `head_type`='Supplier' AND description='Amount payable against purchase item' GROUP BY `voucher_no`,`sub_id` ORDER BY COUNT(`sub_id`) DESC ";
		$MRES =mysql_query($sqlm);
		$sl=0; $ids="";
		$NumRows = mysql_num_rows($MRES);
		if($NumRows >0){
		while($r = mysql_fetch_object($MRES)){
			$id		= $r->id;
			$voucher_no	= $r->voucher_no;
			$sub_id 	= $r->sub_id;
			$project_id	= $r->project_id;
			if($r->double_hit >1){ 
			$dsql = "DELETE FROM ".ACCOUNT_JOURNAL_TBL." WHERE id = '".$id."' AND voucher_no='".$voucher_no."' AND sub_id='".$sub_id."' AND project_id='".$project_id."'";
	 		mysql_query($dsql); $sl++; $ids.="$id,";
			}			
		}//end while 
		
		}// end if num row
		echo "<br>==Successfully Delete $sl Double Purchase Ledger ==<br> ids: $ids";

	
   }
  function showSetSalesDivisionEditor()
  {	 	
		//$sql="SELECT m.customer,d.* FROM ".SALES_DELIVERY_MASTER_TBL." AS m, ".SALES_DELIVERY_CHALLAN_TBL." AS d WHERE m.voucher_no=d.voucher_no AND d.`district` = 43 AND d.`area` LIKE 'A0063' AND MONTH(m.delivery_date) = '02' AND YEAR(m.delivery_date) = '2020' ORDER BY d.`sales_delivery_id` ASC";

		$sql="SELECT m.customer,d.* FROM ".SALES_DELIVERY_MASTER_TBL." AS m, ".SALES_DELIVERY_CHALLAN_TBL." AS d WHERE m.voucher_no=d.voucher_no AND d.`division`=0 ORDER BY d.`sales_delivery_id` ASC";
		$RES =mysql_query($sql);
		$sl=0;
		$NumRow = mysql_num_rows($RES);
		if($NumRow >0){
		while($r = mysql_fetch_object($RES)){
			$delivery_id 	= $r->sales_delivery_id;
			$voucher_no	= $r->voucher_no;
			$customer 	= $r->customer;
			$district 	= $r->district;
			if($district ==0){
				$csql="SELECT * FROM `sub_acc_head` WHERE `sub_id` ='$customer'";
				$CRES =mysql_query($csql);
				if(mysql_num_rows($CRES) >0){ 
				$crow = mysql_fetch_object($CRES);
				$division	= $crow->division;
				$district 	= $crow->district;
				$area 		= $crow->area;
				}else{
				$ssql = "SELECT * FROM ".SUPPLIER_TBL." WHERE `supplier_code` = '$customer'";
				$SRES = mysql_query($ssql);
				$srow = mysql_fetch_object($SRES);
				$division	= $srow->division;
				$district 	= $srow->district;
				$area 		= $srow->area;
				}			
			}else{
				$csql="SELECT district_id,division_id FROM district WHERE district_id='$district'";
				$cres =mysql_query($csql);
				$cr = mysql_fetch_object($cres);
				$division = $cr->division_id;
				$district = $cr->district_id;
				$area = $cr->area;
			}
			mysql_query("UPDATE sales_delivery_item SET division='$division',district='$district',area='$area' WHERE sales_delivery_id='$delivery_id'");

			$USQL1="UPDATE ".SALES_MASTER_TBL." SET division=$division WHERE `voucher_no`= '$voucher_no'";
			mysql_query($USQL1);
			$csql="SELECT * FROM `sub_acc_head` WHERE `sub_id` ='$customer'";
			$CRES =mysql_query($csql);
			if(mysql_num_rows($CRES) >0){ 
			  $crow = mysql_fetch_object($CRES);
			  $cdivision	= $crow->division;
			  $cdistrict 	= $crow->district;
			  $carea 		= $crow->area;
			  if($cdivision==0 || $cdistrict){
			  $USQL2="UPDATE `sub_acc_head` SET division=$division,district=$district WHERE `sub_id` ='$customer'";
			  mysql_query($USQL2);
			  }
			}

			$sl++;
		}// end while
		}// end if numrow

	 	echo "==Successfully Set Sales Division $sl ==";
		
		//===== Set Sales Master =======
		$sqlm="SELECT * FROM ".SALES_MASTER_TBL." WHERE `division`=0 ORDER BY voucher_no ASC";
		$MRES =mysql_query($sqlm);
		$sl=0;
		$NumRows = mysql_num_rows($MRES);
		if($NumRows >0){
		while($r = mysql_fetch_object($MRES)){
			$voucher_no	= $r->voucher_no;
			$customer 	= $r->customer;
			$csql="SELECT * FROM `sub_acc_head` WHERE `sub_id` ='$customer'";
			$CRES =mysql_query($csql);
			if(mysql_num_rows($CRES) >0){ 
			$crow = mysql_fetch_object($CRES);
			$division	= $crow->division;
			$district 	= $crow->district;
			$area 		= $crow->area;
			}else{
			$ssql = "SELECT * FROM ".SUPPLIER_TBL." WHERE `supplier_code` = '$customer'";
			$SRES = mysql_query($ssql);
			$srow = mysql_fetch_object($SRES);
			$division	= $srow->division;
			$district 	= $srow->district;
			$area 		= $srow->area;
			}
			
			$SMSQL="UPDATE ".SALES_MASTER_TBL." SET division=$division, district = '$district', area = '$area' WHERE `voucher_no` = '$voucher_no'";
			mysql_query($SMSQL);

			$SDSQL="UPDATE ".SALES_DELIVERY_CHALLAN_TBL." SET division=$division, district = '$district', area = '$area' WHERE `voucher_no` = '$voucher_no'";
			mysql_query($SDSQL);
			$sl++;
		}//end while 
		
		}// end if num row
		echo "<br>==Successfully Set Sales Master Division $sl ==";
		/*
		//==== When Sales Opening =====
		
		$date_from = "2019-01-01"; $NumRowsSet=0;
		$SQL="SELECT sm.`voucher_no` , sm.sales_date, sm.`delivery_date`,sdm.`delivery_date` as posting_date, sdc.division FROM ".SALES_MASTER_TBL." AS sm, ".SALES_DELIVERY_MASTER_TBL." AS sdm, ".SALES_DELIVERY_CHALLAN_TBL." AS sdc WHERE sm.`voucher_no` = sdm.`voucher_no` AND sdm.`voucher_no` = sdc.`voucher_no` AND sm.`delivery_date` ='$date_from' AND MONTH(sales_date) ='12' AND YEAR(sales_date) ='2018' GROUP BY sm.`voucher_no` ORDER BY sm.`voucher_no` ASC";
		$dvres 	 = mysql_query($SQL);
		$NumRows = mysql_num_rows($dvres);
		if($NumRows >0){
		   while($smrow = mysql_fetch_object($dvres)){
			$voucher_no 	= $smrow->voucher_no;
			$division 	= $smrow->division;
			$delivery_date 	= $smrow->sales_date;			
			$USQL1="UPDATE ".SALES_MASTER_TBL." SET delivery_date = '$delivery_date' WHERE `voucher_no` = '$voucher_no'";
			mysql_query($USQL1);
			$USQL1="UPDATE ".SALES_DELIVERY_MASTER_TBL." SET delivery_date = '$delivery_date' WHERE `voucher_no` = '$voucher_no'";
			mysql_query($USQL1);
			$USQL1="UPDATE ".ACCOUNT_JOURNAL_TBL." SET created_date = '$delivery_date' WHERE `voucher_no` = '$voucher_no'";
			mysql_query($USQL1); $NumRowsSet++;					
		   }
		}
		$msg="===Set ($NumRowsSet) Sales Date Done <br>===";
		*/

	
   }
   function ShowMoveSalesEditor(){
	require_once(CLASS_DIR.'/common.list.class.php');	
	$comListApp = new CommonList(); 

	$data                		= array();
	$data['cmd']         		= getRequest('cmd');
	$data['area_list']		= $comListApp->getDistrictList();
	$data['district_list']		= $comListApp->getDistrictList();
	$data['trt_list'] 		= $comListApp->getAreaList(); 
	$date_from 			= formatDate(getRequest('date_from'));
	$date_to 			= formatDate(getRequest('date_to')); 
	$division_id			= getRequest('division_id'); 
	$district_id			= getRequest('district'); 
	$area_id			= getRequest('area');
	if(getRequest('start'))
	{
	   if($date_from !="" && $date_to !=""){
		$csql="SELECT * FROM ".SUB_ACC_HEAD_TBL." WHERE status=1 AND `head_type` = 'Current Assets' AND `sub_headtype` = 'S128' AND `child_head` = 'C000105' ";
		if($division_id >0){
		$csql.=" AND division=$division_id";
		}
		if($district_id >0){
		$csql.=" AND district=$district_id";
		}
		if($area_id !=""){
		$csql.=" AND area='$area_id'";
		}
		$csql.=" ORDER BY sub_id ASC "; 
		$CRES =mysql_query($csql);   
		if(mysql_num_rows($CRES) >0){ 
		 while($crow = mysql_fetch_object($CRES)){
		 $customer	= $crow->sub_id;
		 $division	= $crow->division;
		 $district 	= $crow->district;
		 $area 		= $crow->area;

		 $SMSQL="UPDATE ".SALES_MASTER_TBL." SET division=$division, district = '$district', area = '$area' WHERE `delivery_date` BETWEEN '$date_from' AND '$date_to'";
		 if($division_id >0){
		 $SMSQL.=" AND division=$division_id";
		 }
		 if($district_id >0){
		 $SMSQL.=" AND district=$district_id";
		 }
		 if($area_id !=""){
		 $SMSQL.=" AND area='$area_id'";
		 }
		 mysql_query($SMSQL);

		 //=== Start Move Delivery Items =====
		 $SQL="SELECT * FROM ".SALES_MASTER_TBL." WHERE customer='$customer' AND `delivery_date` BETWEEN '$date_from' AND '$date_to' GROUP BY `voucher_no` ORDER BY `voucher_no` ASC";
		 $dvres 	 = mysql_query($SQL);
		 $NumRows = mysql_num_rows($dvres);
		 if($NumRows >0){
	   	 while($smrow = mysql_fetch_object($dvres)){
		 $voucher_no 	= $smrow->voucher_no;

		 $SDSQL="UPDATE ".SALES_DELIVERY_CHALLAN_TBL." SET division=$division, district = '$district', area = '$area' WHERE `voucher_no` = '$voucher_no'";
		 mysql_query($SDSQL);
		 }//end while
		 }//end if
		 //=== End Move Delivery Items =====

		 }//end while
		
		}else{
		$ssql = "SELECT supplier_code as sub_id,division,district,area FROM ".SUPPLIER_TBL;
		if($division_id >0){
		$ssql.=" WHERE division=$division_id";
		}
		if($district_id >0){
		$ssql.=" AND district=$district_id";
		}
		if($area_id !=""){
		$ssql.=" AND area='$area_id'";
		}
		$ssql.=" ORDER BY supplier_code ASC";
		$SRES = mysql_query($ssql);
		$srow = mysql_fetch_object($SRES);
		while($srow = mysql_fetch_object($SRES)){
		$customer	= $crow->sub_id;
		$division	= $srow->division;
		$district 	= $srow->district;
		$area 		= $srow->area;
		
		$SMSQL="UPDATE ".SALES_MASTER_TBL." SET division=$division, district = '$district', area = '$area' WHERE `delivery_date` BETWEEN '$date_from' AND '$date_to'";
		if($division_id >0){
		$SMSQL.=" AND division=$division_id";
		}
		if($district_id >0){
		$SMSQL.=" AND district=$district_id";
		}
		if($area_id !=""){
		$SMSQL.=" AND area='$area_id'";
		}
		mysql_query($SMSQL);

		//=== Start Move Delivery Items =====
		$SQL="SELECT * FROM ".SALES_MASTER_TBL." WHERE customer='$customer' AND `delivery_date` BETWEEN '$date_from' AND '$date_to' GROUP BY `voucher_no` ORDER BY `voucher_no` ASC";
		$dvres 	 = mysql_query($SQL);
		$NumRows = mysql_num_rows($dvres);
		if($NumRows >0){
	   	while($smrow = mysql_fetch_object($dvres)){
		$voucher_no 	= $smrow->voucher_no;

		$SDSQL="UPDATE ".SALES_DELIVERY_CHALLAN_TBL." SET division=$division, district = '$district', area = '$area' WHERE `voucher_no` = '$voucher_no'";
		mysql_query($SDSQL);
		}//end while
		}//end if
		//=== End Move Delivery Items =====
		}//end while
		} // end if NumRows	
				
		echo $msg.="===Move Sales Division ($NumRows) Done ===<br>";
		
		$date_froms 		= getRequest('date_from');
		$date_tos 		= getRequest('date_to'); 
		header("location:?app=sales.opening.by.trt&cmd=movesales&date_from=$date_froms&date_to=$date_tos&division_id=$division_id&district=$district_id&msg=$msg");	
		
	     }
          }
	  require_once(MOVE_SALES_BY_TRT_SKIN); 	 
	  return $data[0];
   }
   function showOPningByTRT($msg = NULL) { 
  	  require_once(CLASS_DIR.'/common.list.class.php');	
	  $comListApp = new CommonList(); 
	        
	  $data                		= array();
	  $data['cmd']         		= getRequest('cmd');
	  $data['area_list']		= $comListApp->getDistrictList();
	  $data['district_list']	= $comListApp->getDistrictList();
	  $data['trt_list'] 		= $comListApp->getAreaList(); 	  
	  $division_id         		= getRequest('division_id');
	  $district_id         		= getRequest('district');
	  $area_id         		= getRequest('area');
	  $sales_type 			= getRequest('sales_type'); 
	  $date_from 			= formatDate(getRequest('date_from'));
	  $date_to 			= formatDate(getRequest('date_to'));	
	  $sales_date 			= formatDate(getRequest('sales_date')); 

	  $date_froms 			= getRequest('date_from');
	  $date_tos 			= getRequest('date_to');	
	  $sales_dates 			= getRequest('sales_date'); 
	  if(getRequest('start'))
	  {  
		//$this->updateSalesOrderDate($date_from,$date_to,$division_id);  // Run Before exit;
		if($date_from != "" && $date_to !="" && $sales_date != "" && $division_id !=""){
	 	$this->saveSalesOrder($date_from,$date_to,$sales_date,$sales_type,$division_id,$district_id,$area_id);
		}
		$msg="Successfully Done Sales Opening!!!";
		header("location:?app=sales.opening.by.trt&cmd=sales.opening.trt&date_from=$date_froms&date_to=$date_tos&sales_date=$sales_dates&sales_type=$sales_type&division_id=$division_id&msg=$msg");		      	
	  }
	  require_once(CURRENT_APP_SKIN_FILE); 	 
	  return $data[0];
   }

   /*
   DELETE FROM `sales_master` WHERE `voucher_no` LIKE '%S%';
   DELETE FROM `sales_delivery_item_master` WHERE `voucher_no` LIKE '%S%';
   DELETE FROM `sales_delivery_item` WHERE `voucher_no` LIKE '%S%';
   DELETE FROM `sales_details` WHERE `voucher_no` LIKE '%S%';
   DELETE  FROM `account_journal` WHERE `voucher_no` LIKE '%S%';

   SELECT sm.`voucher_no` , sm.sales_date, sm.`delivery_date`,sdm.`delivery_date` as posting_date, sdc.division FROM liraerp2017.sales_master AS sm, liraerp2017.sales_delivery_item_master sdm, liraerp2017.sales_delivery_item sdc WHERE sm.`voucher_no` = sdm.`voucher_no` AND sdm.`voucher_no` = sdc.`voucher_no` AND sm.`delivery_date` BETWEEN '2017/12/16' AND '2017/12/31' AND sdc.division=6 GROUP BY sm.`voucher_no` ORDER BY sm.`voucher_no` ASC 
   */

   function updateSalesOrderDate($date_from,$date_to,$division_id){
	   if($date_from !="" && $date_to !=""){
		$SQL="SELECT sm.`voucher_no` , sm.sales_date, sm.`delivery_date`,sdm.`delivery_date` as posting_date, sdc.division FROM ".SALES_MASTER_TBL." AS sm, ".SALES_DELIVERY_MASTER_TBL." AS sdm, ".SALES_DELIVERY_CHALLAN_TBL." AS sdc WHERE sm.`voucher_no` = sdm.`voucher_no` AND sdm.`voucher_no` = sdc.`voucher_no` AND sdm.`delivery_date` BETWEEN '$date_from' AND '$date_to' AND sdc.division=$division_id GROUP BY sm.`voucher_no` ORDER BY sm.`voucher_no` ASC";
		$dvres 	 = mysql_query($SQL);
		$NumRows = mysql_num_rows($dvres);
		if($NumRows >0){
		   while($smrow = mysql_fetch_object($dvres)){
			$voucher_no 	= $smrow->voucher_no;
			$division 	= $smrow->division;
			$delivery_date 	= $smrow->posting_date;			
			$USQL1="UPDATE ".SALES_MASTER_TBL." SET division=$division, sales_date = '$delivery_date', delivery_date = '$delivery_date' WHERE `voucher_no` = '$voucher_no'";
			mysql_query($USQL1);
			$USQL1="UPDATE ".SALES_DELIVERY_MASTER_TBL." SET delivery_date = '$delivery_date' WHERE `voucher_no` = '$voucher_no'";
			mysql_query($USQL1);
			//$this->deleteSalesMaster($voucher_no);					
		   }
		}
		 $msg="===Set SM ($NumRowsSet) Sales Date Done <br>===";
		/*
		$SQL="SELECT * FROM ".SALES_MASTER_TBL." WHERE  `delivery_date` BETWEEN '$date_from' AND '$date_to' GROUP BY `voucher_no` ORDER BY `voucher_no` ASC";
		$dvres 	 = mysql_query($SQL);
		$NumRows = mysql_num_rows($dvres);
		if($NumRows >0){
		   while($smrow = mysql_fetch_object($dvres)){
			$voucher_no 	= $smrow->voucher_no;
			$customer 	= $smrow->customer;

			$csql="SELECT * FROM `sub_acc_head` WHERE `sub_id` ='$customer' AND `head_type` = 'Current Assets', `sub_headtype` = 'S128', `child_head` = 'C000105'";
			$CRES =mysql_query($csql);
			if(mysql_num_rows($CRES) >0){ 
			$crow = mysql_fetch_object($CRES);
			$division	= $crow->division;
			$district 	= $crow->district;
			$area 		= $crow->area;
			}else{
			$ssql = "SELECT * FROM ".SUPPLIER_TBL." WHERE `supplier_code` = '$customer'";
			$SRES = mysql_query($ssql);
			$srow = mysql_fetch_object($SRES);
			$division	= $srow->division;
			$district 	= $srow->district;
			$area 		= $srow->area;
			}	
		
			$SMSQL="UPDATE ".SALES_MASTER_TBL." SET division=$division, district = '$district', area = '$area' WHERE `voucher_no` = '$voucher_no'";
			mysql_query($SMSQL);

			$SDSQL="UPDATE ".SALES_DELIVERY_CHALLAN_TBL." SET division=$division, district = '$district', area = '$area' WHERE `voucher_no` = '$voucher_no'";
			mysql_query($SDSQL);					
		   }
		}
		echo $msg.="===Set SM ($NumRows) Sales Division Done ===<br>";
		*/
		$date_froms 		= getRequest('date_from');
		$date_tos 		= getRequest('date_to');	
		$sales_dates 		= getRequest('sales_date'); 
		header("location:?app=sales.opening.by.trt&cmd=sales.opening.trt&date_from=$date_froms&date_to=$date_tos&sales_date=$sales_dates&sales_type=$sales_type&division_id=$division_id&msg=$msg");	
		
	   }

   }
   //===== First Opening =====
      
   function deleteOldSalesOrder($voucher_no){
	mysql_query("START TRANSACTION;");	   	   
	if($voucher_no !=""){
		$this->deleteRecord(DB_NAME.".sales_master","voucher_no",$voucher_no); 
		$this->deleteRecord(DB_NAME.".sales_details","voucher_no",$voucher_no); 
		$this->deleteRecord(DB_NAME.".sales_delivery_item_master","voucher_no",$voucher_no);
		$this->deleteRecord(DB_NAME.".sales_delivery_item","voucher_no",$voucher_no);
		$this->deleteRecord(DB_NAME.".account_journal","voucher_no",$voucher_no);
	}
	mysql_query("COMMIT;");
   }
   function deleteSalesMaster($voucher_no){
	mysql_query("START TRANSACTION;");	   	   
	if($voucher_no!=""){
		$this->deleteRecord(NDB_NAME.".sales_master","voucher_no",$voucher_no); 
		$this->deleteRecord(NDB_NAME.".sales_details","voucher_no",$voucher_no); 
		$this->deleteRecord(NDB_NAME.".sales_delivery_item_master","voucher_no",$voucher_no);	  
		$this->deleteRecord(NDB_NAME.".sales_delivery_item","voucher_no",$voucher_no);
		$this->deleteRecord(NDB_NAME.".account_journal","voucher_no",$voucher_no);
	}
	mysql_query("COMMIT;");
   }
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
   
   function saveSalesOrder($date_from,$date_to,$sales_delivery_date,$head_type,$division_id,$district_id=NULL,$area_id=NULL){
	$failed="";
	if($head_type =="Customer"){
		$csql = "SELECT * FROM ".SUB_ACC_HEAD_TBL." WHERE `head_type` = 'Current Assets' AND `sub_headtype` = 'S128' AND `child_head` = 'C000105' AND `division` = '$division_id'";
		if($district_id !=""){
		$csql.=" AND district = '$district_id'";	
		}
		if($area_id !=""){
		$csql.=" AND area = '$area_id'";	
		}
	}else{
		$csql = "SELECT * FROM ".SUB_ACC_HEAD_TBL." WHERE head_type = 'Current Liabilities' AND sub_headtype = 'S137' AND `child_head` = 'C000116' AND `division` = '$division_id'";
		if($district_id !=""){
		$csql.=" AND district = '$district_id'";	
		}
		if($area_id !=""){
		$csql.=" AND area = '$area_id'";	
		}
	}
	//echo $csql; exit;
	$cres = mysql_query($csql);
	if(mysql_num_rows($cres)>0){ 
		while($crow= mysql_fetch_object($cres)){			
			$customer = $crow->sub_id;
			$smsql = "SELECT * FROM ".SALES_MASTER_TBL." WHERE customer = '$customer' AND division=$division_id AND `sales_date` BETWEEN '$date_from' AND '$date_to' ORDER BY voucher_no ASC"; 
			$smres   = mysql_query($smsql);			
			if(mysql_num_rows($smres) >0){
				while($smrow		= mysql_fetch_object($smres)){
					$refNo  		= $smrow->voucher_no;
					$wo_no  		= $smrow->wo_no;
					$project_id  		= $smrow->project_id;
					$division		= $smrow->division;
					$district  		= $smrow->district;
					$area  			= $smrow->area;
					$delivery_point  	= $smrow->delivery_point;
					$customer  		= $smrow->customer;
					$salse_type  		= $smrow->salse_type;
					$order_type  		= $smrow->order_type;
					$sales_date  		= $smrow->sales_date;
					$delivery_date  	= $smrow->delivery_date;
					$total_value 		= $smrow->total_value;
					$mode_of_payment 	= $smrow->mode_of_payment;
					$gd_percent 		= $smrow->general_discount_percent;
					$gd_amount 		= $smrow->general_discount_amount;
					$ed_percent 		= $smrow->exclusive_discount_percent;
					$ed_amount 		= $smrow->exclusive_discount_amount;
					$additional_discount	= $smrow->additional_discount;
					$product_discount 	= $smrow->product_discount;
					$discount 		= $smrow->discount;
					$net_payble 		= $smrow->net_payble;
					$paid_amount 		= $smrow->paid_amount;
					$adjust 		= $smrow->adjust;
					$delivery_amount 	= $smrow->item_delivery_amount;
					$due 			= $smrow->due;
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
					
					if($voucher_no !=""){   
						$sqlM="INSERT INTO ".NDB_NAME.".`sales_master` (voucher_no,po_no,wo_no,project_id,division,district,area,delivery_point,customer,salse_type,order_type,
						sales_date,delivery_date,total_value,mode_of_payment,general_discount_percent,general_discount_amount,exclusive_discount_percent,
						exclusive_discount_amount,additional_discount,product_discount,discount,net_payble,paid_amount,adjust,item_delivery_amount,due,description,
						created_by,created_date) VALUES('$voucher_no','$refNo','$wo_no','$project_id','$division','$district','$area','$delivery_point','$customer','$salse_type',
						'$order_type','$sales_date','$sales_delivery_date','$total_value','$mode_of_payment','$gd_percent','$gd_amount','$ed_percent','$ed_amount',
						'$additional_discount','$product_discount','$discount','$net_payble','$paid_amount','$adjust','$delivery_amount','$due','$description',
						'$created_by','$created_date')"; //echo "<br>";
						$res3=mysql_query($sqlM); 
						if($res3){
							$this->saveSalesDetails($voucher_no,$refNo);
							$this->saveSalesDeliveryMaster($voucher_no,$refNo,$head_type,$sales_delivery_date);
							//if($voucher_no =="S0001179"){ exit;} //test
						}else{ $failed.=$refNo."|";} 
					}//END if voucher_no
					$voucher_no=""; //exit;
				}//End While 
				
			}else{ // End Num Rows
				echo "Out";
			}
		}
	}
		
	if($failed !=""){ echo $failed."<br>";}
	
   }// EOF
   
   function saveSalesDetails($voucher_no,$refNo){
	
	$sdsql = "SELECT * FROM ".SALES_DETAILS_TBL." WHERE `voucher_no` = '$refNo' ";
	$sdres = mysql_query($sdsql);
	if(mysql_num_rows($sdres)>0){		
		while($smrow		= mysql_fetch_object($sdres)){
			$pvoucher_no  		= $smrow->pvoucher_no;
			$project_id  		= $smrow->project_id;
			$customer  		= $smrow->customer;
			$catagory  		= $smrow->catagory;
			$brand_id  		= $smrow->brand_id;
			$product  		= $smrow->product;
			$m_unit  		= $smrow->m_unit;
			$unit_price  		= $smrow->unit_price;
			
			$purchase_price  	= $smrow->purchase_price;
			$discount_per_qty  	= $smrow->discount_per_qty;
			$discount_amount 	= $smrow->discount_amount;
			$unit_profit 		= $smrow->unit_profit;
			$qty 			= $smrow->qty;
			$delivery_qty 		= $smrow->delivery_qty;
			$free_qty 		= $smrow->free_qty;
			$undelivery_qty 	= $smrow->undelivery_qty;
			$total			= $smrow->total;
			$district 		= $smrow->district;
			$area 			= $smrow->area;
			$created_by 		= $smrow->created_by;	
			  
			$sqlD="INSERT INTO ".NDB_NAME.".`sales_details` (voucher_no,pvoucher_no,project_id,customer,catagory,brand_id,product,details,m_unit,unit_price,
			purchase_price,discount_per_qty,discount_amount,unit_profit,qty,delivery_qty,free_qty,undelivery_qty,total,district,area,created_by) 
			VALUES('$voucher_no','$pvoucher_no','$project_id','$customer','$catagory','$brand_id','$product','$refNo','$m_unit','$unit_price',
			'$purchase_price','$discount_per_qty','$discount_amount','$unit_profit','$qty','$delivery_qty','$free_qty','$undelivery_qty','$total',
			'$district','$area','$created_by')"; //echo "<br>";
			mysql_query($sqlD);		
		}// End While
		
		}// End Num rows
			
	}//EOF
	
   function saveSalesDeliveryMaster($voucher_no,$refNo,$head_type,$sales_delivery_date){
	
	$sdmsql = "SELECT * FROM ".SALES_DELIVERY_MASTER_TBL." WHERE `voucher_no` = '$refNo' ";
	$sdmres = mysql_query($sdmsql);
	if(mysql_num_rows($sdmres) >0){		
		while($smrow		= mysql_fetch_object($sdmres)){
			$delivery_master_id	= $smrow->sales_delivery_master_id;
			$project_id  		= $smrow->project_id;
			$customer  		= $smrow->customer;
			$challan_no  		= $smrow->challan_no;
			$delivery_point  	= $smrow->delivery_point;
			$delivery_date  	= $smrow->delivery_date;
			$total_value  		= $smrow->total_value;
			$previour_balance  	= $smrow->previour_balance;		
			$roa  			= $smrow->roa;
			$created_by  		= $smrow->created_by;
			$created_date 		= $smrow->created_date;	
			  
			$sqlSD="INSERT INTO ".NDB_NAME.".`sales_delivery_item_master` (voucher_no,project_id,customer,challan_no,delivery_point,delivery_date,
			total_value,previour_balance,roa,created_by,created_date) 
			VALUES('$voucher_no','$project_id','$customer','$challan_no','$delivery_point','$sales_delivery_date','$total_value','$previour_balance',
			'$roa','$created_by','$created_date')"; //echo "<br>";
			$res = mysql_query($sqlSD);	
			$delivery_masterid = mysql_insert_id();
			if($res){
				if($total_value >0){ // === Dr ====	Opening Sales is OS		
					$totalPartyCR  = $this->getTotalCreditAmount($customer,getFromSession('project_id'));
					$totalPartyDR  = $this->getTotalDebitAmount($customer,getFromSession('project_id'));					 
					$PartyBalance  = (($totalPartyDR+$total_value)-$totalPartyCR);										 
					$this->saveAccountJournal($voucher_no,$delivery_masterid,$customer,$head_type,$project_id,"Opening Sales Delivery",$total_value,0,$PartyBalance,1,$sales_delivery_date);					
				}
				// ==== Save  Sales Delivery Details =====
				
				$sddmsql = "SELECT * FROM ".SALES_DELIVERY_CHALLAN_TBL." WHERE `voucher_no` = '$refNo' AND delivery_master_id=$delivery_master_id";
				$sddmres = mysql_query($sddmsql);
				if(mysql_num_rows($sddmres) >0){
					//===== Get Party Area Info New 2017=====
					$CSql="SELECT division,district,area FROM ".SUB_ACC_HEAD_TBL." WHERE sub_id ='".$customer."' AND project_id = '$project_id'";
					$CRes = mysql_query($CSql);
					if(mysql_num_rows($CRes) >0){
						$crow = mysql_fetch_object($CRes);	
						$division = $crow->division;	
						$district = $crow->district;	
						$area = $crow->area;
					}/*else{
						$CSql="SELECT division,district,area FROM ".SUPPLIER_TBL." WHERE supplier_code ='".$customer."' AND project_id = '$project_id'";
						$CRes = mysql_query($CSql);
						if(mysql_num_rows($CRes) >0){
							$crow = mysql_fetch_object($CRes);	
							$division = $crow->division;	
							$district = $crow->district;	
							$area = $crow->area;
						}
					}*/
											
					while($smrow		= mysql_fetch_object($sddmres)){
						$pvoucher_no		= $smrow->pvoucher_no;
						$delivery_point  	= $smrow->delivery_point;
						$project_id  		= $smrow->project_id;
						$catagory  		= $smrow->catagory;
						$brand_id  		= $smrow->brand_id;
						$product  		= $smrow->product;
						$m_unit  		= $smrow->m_unit;
						$unit_price  		= $smrow->unit_price;		
						$discount_per_qty  	= $smrow->discount_per_qty;
						$discount_amount  	= $smrow->discount_amount;
						$overall_discount  	= $smrow->overall_discount;
						
						$overall_discount_amount= $smrow->overall_discount_amount;
						$unit_profit  		= $smrow->unit_profit;
						$delivery_qty  		= $smrow->delivery_qty;
						$total_bag  		= $smrow->total_bag;
						$total_amount  		= $smrow->total_amount;
						$created_by  		= $smrow->created_by;	
						  
						$sqlID="INSERT INTO ".NDB_NAME.".`sales_delivery_item` (delivery_master_id,voucher_no,pvoucher_no,delivery_point,project_id,catagory,brand_id,product,m_unit,
						unit_price,discount_per_qty,discount_amount,overall_discount,overall_discount_amount,unit_profit,delivery_qty,total_bag,total_amount,division,district,area,created_by) 
						VALUES('$delivery_masterid','$voucher_no','$pvoucher_no','$delivery_point','$project_id','$catagory','$brand_id','$product','$m_unit',
						'$unit_price','$discount_per_qty','$discount_amount','$overall_discount','$overall_discount_amount','$unit_profit','$delivery_qty','$total_bag','$total_amount','$division','$district','$area','$created_by')";
						//echo "<br>";
						mysql_query($sqlID);	
					
					}// End While
					
				}// End Num rows	
				//===== Delete Previous Order ======		
				//$this->deleteOldSalesOrder($refNo); // stop 2021
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
   //===== Start Set Latest COGS =====
   
  function updateSalesCostPrice(){ 
	$date_from ="2021-01-10"; $date_to="2021-08-09";
  	$msql="SELECT voucher_no,sales_delivery_master_id,delivery_date,project_id,created_by FROM ".SALES_DELIVERY_MASTER_TBL." WHERE delivery_date BETWEEN '$date_from' AND '$date_to' GROUP BY voucher_no ORDER BY voucher_no ASC";
	$MRES =mysql_query($msql);
	$sl=0;
	$NumRow = mysql_num_rows($MRES);
	if($NumRow >0){
	    while($mrow = mysql_fetch_object($MRES)){
		$voucher_no	= $mrow->voucher_no;
		$delivery_id	= $mrow->sales_delivery_master_id;
		$delivery_date	= $mrow->delivery_date;
		$project_id	= $mrow->project_id;
		$created_by	= $mrow->created_by;
		$psql="SELECT d.delivery_qty,p.purchase_unit_price FROM ".SALES_DELIVERY_CHALLAN_TBL." as d,".PRODUCT_TBL." as p WHERE d.product=p.product_id AND d.voucher_no='".$voucher_no."' GROUP BY sales_delivery_id";
		$pRES = mysql_query($psql);
		if(mysql_num_rows($pRES) >0){ 
		  $total_cost = 0; $total_cogs=0;
		  while($prow = mysql_fetch_object($pRES)){
		  $delivery_qty 	= $prow->delivery_qty;
		  $purchase_unit_price  = $prow->purchase_unit_price;
		  $total_cost 		= ($delivery_qty * $purchase_unit_price);
		  $total_cogs+=$total_cost;
		  }
		  if($total_cogs >0){
			$sdsql="DELETE FROM ".ACCOUNT_JOURNAL_TBL." WHERE `sub_id` ='A000036' AND voucher_no='".$voucher_no."'";
			mysql_query($sdsql);
			$cdsql="DELETE FROM ".ACCOUNT_JOURNAL_TBL." WHERE `sub_id` ='A006351' AND voucher_no='".$voucher_no."'";
			mysql_query($cdsql);
			$description = "Cost of goods sold (cogs)";
			$transaction_type = "Goods Sold";
			$this->saveCostJournal($voucher_no,$delivery_id,"A006351","Cost Center",$project_id,$transaction_type,$description,$total_cogs,0,$total_cogs,1,$delivery_date,$created_by);
			$description = "Sales delivery challan";
			$transaction_type = "Sales Product";
			$this->saveCostJournal($voucher_no,$delivery_id,"A000036","Current Assets",$project_id,$transaction_type,$description,0,$total_cogs,$total_cogs,1,$delivery_date,$created_by);
		  }
		  $total_cogs=0;
		}		

		$sl++;
	    }// end while
	}// end if numrow

 	echo "==Successfully Set Cost Price $sl ==";

  }
  function saveCostJournal($voucher_no,$delivery_id,$sub_id,$head_type,$project_id,$transaction_type,$description,$DR=NULL,$CR=NULL,$balance,$status,$created_date,$created_by){
	$sql = "INSERT INTO ".ACCOUNT_JOURNAL_TBL." (voucher_no,delivery_id,created_date,sub_id,head_type,transaction_type,
	project_id,description,dr,cr,balance,status,created_by) VALUES('".$voucher_no."','".$delivery_id."','".$created_date."','"
	.$sub_id."','".$head_type."','".$transaction_type."','".$project_id."','".$description."','".$DR."','".$CR."','".$balance."','"
	.$status."','".$created_by."')";
	mysql_query($sql); 
  }
  //===== End Set Latest COGS =====

} // End class
?>
