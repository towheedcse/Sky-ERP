<?php
class ClearYearEnding
{
   function run() {     
		$cmd    = getRequest('cmd');
		$u_t_id = getFromSession('u_type_id');
		if($u_t_id == 101) 
		{      
		  switch($cmd) { 
		  	 case 'clear'         : $screen = $this->showEditor($msg); break;
		  }
		}else {
      		header("location:index.php?app=user_home&msg=You are not authorised !!!");
      	} 
		
		return true;
  }
  
  function showEditor($msg=NULL)
  {
	 	
	/* ====== Clear NDB for Year Ending Untill 31 Dec 2017 : 1-20 23738  : 1-1 156=======*/
	mysql_query("START TRANSACTION;");
	$project_id     = getFromSession('project_id'); 
	$pid = " AND project_id='$project_id'"; 
	// http://localhost/liraerp/index.php?app=clear.yearending&cmd=clear

	/*
	$SQL1= "SELECT * FROM ".ACCOUNT_JOURNAL_TBL." WHERE `created_date` <= '2017-12-31' AND project_id='$project_id' ORDER BY created_date ASC";		
	$res1    = mysql_query($SQL1);
	$numrow1 = mysql_num_rows($res1);
	if($numrow1 >0){
		while($arow=mysql_fetch_object($res1)){
		$voucher_no  = $arow->voucher_no;
		$DSQL1= "DELETE FROM ".NDB_NAME.".adjust_invoice_history WHERE `voucher_no`='$voucher_no' $pid";
		mysql_query($DSQL1);
		$DSQL2= "DELETE FROM ".NDB_NAME.".adjust_voucher_history WHERE `voucher_no`='$voucher_no' $pid";
		mysql_query($DSQL2);
		$DSQL3= "DELETE FROM ".NDB_NAME.".avg_purchase_price WHERE `voucher_no`='$voucher_no' $pid";
		mysql_query($DSQL3);
		$DSQL4= "DELETE FROM ".NDB_NAME.".sales_return_payble WHERE `voucher_no`='$voucher_no' $pid";
		mysql_query($DSQL4);
		$DSQL5= "DELETE FROM ".NDB_NAME.".sales_return_master WHERE `voucher_no`='$voucher_no' $pid";
		mysql_query($DSQL5);
		$DSQL6= "DELETE FROM ".NDB_NAME.".sales_return WHERE `voucher_no`='$voucher_no' $pid";
		mysql_query($DSQL6);
		$DSQL7= "DELETE FROM ".NDB_NAME.".stock_ledger WHERE `voucher_no`='$voucher_no' $pid";
		mysql_query($DSQL7);
		$DSQL8= "DELETE FROM ".NDB_NAME.".purchase_master WHERE `voucher_no` = '$batch_no' $pid";
		mysql_query($DSQL8);
		$DSQL9= "DELETE FROM ".NDB_NAME.".purchase_details WHERE `voucher_no` = '$batch_no' $pid";
		mysql_query($DSQL9);
		
		
		}
		
		$DSQL4= "DELETE FROM ".NDB_NAME.".account_journal WHERE `created_date` <= '2017-12-31' $pid";
		mysql_query($DSQL4);
	}
	// End Num
	echo "Total JOURNAL =".$numrow." Records Deleted <br>";
	*/

	/*
	$SQL2= "SELECT * FROM ".DEVIT_VOUCHAR_TBL." WHERE `created_date` <= '2017-12-31' $pid ORDER BY created_date ASC";	
	$res2    = mysql_query($SQL2);
	$numrow2 = mysql_num_rows($res2);
	if($numrow2 >0){
		while($arow=mysql_fetch_object($res2)){
		$voucher_no  = $arow->voucher_no;
		$DSQL1= "DELETE FROM ".NDB_NAME.".adjust_invoice_history WHERE `voucher_no`='$voucher_no' $pid";
		mysql_query($DSQL1);
		$DSQL2= "DELETE FROM ".NDB_NAME.".adjust_voucher_history WHERE `voucher_no`='$voucher_no' $pid";
		mysql_query($DSQL2);
		$DSQL3= "DELETE FROM ".NDB_NAME.".avg_purchase_price WHERE `voucher_no`='$voucher_no' $pid";
		mysql_query($DSQL3);
		$DSQL4= "DELETE FROM ".NDB_NAME.".sales_return_payble WHERE `voucher_no`='$voucher_no' $pid";
		mysql_query($DSQL4);
		$DSQL5= "DELETE FROM ".NDB_NAME.".sales_return_master WHERE `voucher_no`='$voucher_no' $pid";
		mysql_query($DSQL5);
		$DSQL6= "DELETE FROM ".NDB_NAME.".sales_return WHERE `voucher_no`='$voucher_no' $pid";
		mysql_query($DSQL6);
		$DSQL7= "DELETE FROM ".NDB_NAME.".stock_ledger WHERE `voucher_no`='$voucher_no' $pid";
		mysql_query($DSQL7);
		$DSQL8= "DELETE FROM ".NDB_NAME.".purchase_master WHERE `voucher_no` = '$batch_no' $pid";
		mysql_query($DSQL8);
		$DSQL9= "DELETE FROM ".NDB_NAME.".purchase_details WHERE `voucher_no` = '$batch_no' $pid";
		mysql_query($DSQL9);		
		}
		$DSQL1= "DELETE FROM ".NDB_NAME.".cs_delivery_product WHERE `created_date` <= '2017-12-31' $pid";
		mysql_query($DSQL1);
		$DSQL2= "DELETE FROM ".NDB_NAME.".credit_vouchar WHERE `created_date` <= '2017-12-31' $pid";
		mysql_query($DSQL2);
	}
	// End Num
	echo "Total VOUCHER =".$numrow2." Records Deleted <br>";
	*/

	/*
	$SQL3= "SELECT * FROM ".CONTRA_MASTER_TBL." WHERE `created_date` <= '2017-12-31' $pid ORDER BY created_date ASC";		
	$res3    = mysql_query($SQL3);
	$numrow3 = mysql_num_rows($res3);
	if($numrow3 >0){
		while($arow=mysql_fetch_object($res3)){
		$voucher_no  = $arow->voucher_no;
		$DSQL1= "DELETE FROM ".NDB_NAME.".contra_details WHERE `voucher_no` = '$voucher_no' $pid";		
		mysql_query($DSQL1);
		$DSQL2= "DELETE FROM ".NDB_NAME.".account_journal WHERE `voucher_no` = '$voucher_no' $pid";		
		mysql_query($DSQL2);
		$DSQL1= "DELETE FROM ".NDB_NAME.".cs_delivery_product WHERE `voucher_no` = '$voucher_no' $pid";		
		mysql_query($DSQL1);
		$DSQL2= "DELETE FROM ".NDB_NAME.".credit_vouchar WHERE `voucher_no` = '$voucher_no' $pid";		
		mysql_query($DSQL2);
		}
		$DSQL2= "DELETE FROM ".NDB_NAME.".contra_master WHERE `created_date` <= '2017-12-31' $pid";		
		mysql_query($DSQL2);
	}
	// End Num
	echo "Total CONTRA =".$numrow3." Records Deleted <br>";
	*/

	/*
	$SQL4= "SELECT * FROM ".OPENING_BALANCE_TBL." WHERE project_id='$project_id'";	
	$res4    = mysql_query($SQL4);
	$numrow4 = mysql_num_rows($res4);
	if($numrow4 >0){
		while($arow=mysql_fetch_object($res4)){
		$voucher_no  = $arow->voucher_no;
		$DSQL2= "DELETE FROM ".NDB_NAME.".account_journal WHERE `voucher_no` = '$voucher_no' $pid";
		mysql_query($DSQL2);
		$DSQL1= "DELETE FROM ".NDB_NAME.".cs_delivery_product WHERE `voucher_no` = '$voucher_no' $pid";
		mysql_query($DSQL1);
		$DSQL2= "DELETE FROM ".NDB_NAME.".credit_vouchar WHERE `voucher_no` = '$voucher_no' $pid";
		mysql_query($DSQL2);
		$DSQL3= "DELETE FROM ".NDB_NAME.".stock_ledger WHERE `voucher_no` = '$voucher_no' $pid";
		mysql_query($DSQL3);
		}
		$DSQL1= "DELETE FROM ".NDB_NAME.".opening_balance WHERE project_id='$project_id'";		
		mysql_query($DSQL1);		
	}
	// End Num	
	echo "Total OPENING =".$numrow4." Records Deleted <br>";
	*/
	/*
	$SQL5= "SELECT * FROM ".PENDING_CVMASTER_TBL." WHERE `created_date` <= '2017-12-31' $pid ORDER BY created_date ASC";		
	$res5    = mysql_query($SQL5);
	$numrow5 = mysql_num_rows($res5);
	if($numrow5 >0){
		while($arow=mysql_fetch_object($res5)){
		$tmp_grvid  = $arow->tmp_grvid;
		echo $DSQL1= "DELETE FROM ".NDB_NAME.".pending_contra_details WHERE `tmp_grvid` = '$tmp_grvid' $pid";
		mysql_query($DSQL1); 
		}
		$DSQL1= "DELETE FROM ".NDB_NAME.".pending_contra_master WHERE `created_date` <= '2017-12-31' $pid";
		mysql_query($DSQL1);
	}
	// End Num
	echo "Total PENDING CONTRA =".$numrow5." Records Deleted <br>";
	*/
	/*
	$SQL6= "SELECT * FROM ".PRODUCTION_MASTER_TBL." WHERE `used_date` <= '2017-12-31' $pid ORDER BY used_date ASC";		
	$res6    = mysql_query($SQL6);
	$numrow6 = mysql_num_rows($res6); 
	if($numrow6 >0){
		while($arow=mysql_fetch_object($res6)){
		$production_id  = $arow->production_id;		
		$DSQL2= "DELETE FROM ".NDB_NAME.".stock_ledger WHERE `voucher_no`='$production_id' $pid";
		mysql_query($DSQL2);
		$DSQL3= "DELETE FROM ".NDB_NAME.".production_fg WHERE `production_id`='$production_id' $pid";
		mysql_query($DSQL3);
		$DSQL4= "DELETE FROM ".NDB_NAME.".account_journal WHERE `voucher_no`='$production_id' $pid";
		mysql_query($DSQL4);
		$DSQL0= "DELETE FROM ".NDB_NAME.".avg_purchase_price WHERE `voucher_no`='$production_id' $pid";
		mysql_query($DSQL0);
		$DSQL1= "DELETE FROM ".NDB_NAME.".purchase_master WHERE `voucher_no` = '$production_id' $pid";
		mysql_query($DSQL1);
		$DSQL2= "DELETE FROM ".NDB_NAME.".purchase_details WHERE `voucher_no` = '$production_id' $pid";
		mysql_query($DSQL2);
		}
		$DSQL2= "DELETE FROM ".NDB_NAME.".production_master WHERE `used_date` <= '2017-12-31' $pid";
		mysql_query($DSQL2);
	}
	// End Num
	echo "Total PRODUCTION =".$numrow6." Records Deleted <br>";
	*/

	/*
	$SQL7= "SELECT * FROM ".PRODUCTION_FG_TBL." WHERE `production_date` <= '2017-12-31' $pid ORDER BY production_date ASC";		
	$res7    = mysql_query($SQL7);
	$numrow7 = mysql_num_rows($res7);
	if($numrow7 >0){
		while($arow=mysql_fetch_object($res7)){
		$batch_no  = $arow->batch_no;
		$DSQL0= "DELETE FROM ".NDB_NAME.".avg_purchase_price WHERE `voucher_no`='$batch_no' $pid";
		mysql_query($DSQL0);
		$DSQL1= "DELETE FROM ".NDB_NAME.".purchase_master WHERE `voucher_no` = '$batch_no' $pid";
		mysql_query($DSQL1);
		$DSQL2= "DELETE FROM ".NDB_NAME.".purchase_details WHERE `voucher_no` = '$batch_no' $pid";
		mysql_query($DSQL2);
		$DSQL2= "DELETE FROM ".NDB_NAME.".stock_ledger WHERE `voucher_no`='$batch_no' $pid";
		mysql_query($DSQL2);
		$DSQL3= "DELETE FROM ".NDB_NAME.".account_journal WHERE `voucher_no`='$batch_no' $pid";
		mysql_query($DSQL3);
		}
		$DSQL4= "DELETE FROM ".NDB_NAME.".production_fg WHERE `production_date` <= '2017-12-31' $pid";
		mysql_query($DSQL4);
	}
	// End Num
	echo "Total FG PRODUCTION =".$numrow7." Records Deleted <br>";
	*/

	/*
	$SQL7= "SELECT * FROM ".PURCHASE_MASTER_TBL." WHERE `purchase_date` <= '2017-12-31' $pid ORDER BY purchase_date ASC";		
	$res7    = mysql_query($SQL7);
	$numrow7 = mysql_num_rows($res7);
	if($numrow7 >0){
		while($arow=mysql_fetch_object($res7)){
		$voucher_no  = $arow->voucher_no;
		$DSQL0= "DELETE FROM ".NDB_NAME.".avg_purchase_price WHERE `voucher_no`='$voucher_no' $pid";
		mysql_query($DSQL0);
		$DSQL1= "DELETE FROM ".NDB_NAME.".purchase_details WHERE `voucher_no` = '$voucher_no' $pid";
		mysql_query($DSQL1);
		$DSQL2= "DELETE FROM ".NDB_NAME.".production_master WHERE `production_id` = '$voucher_no' $pid";
		mysql_query($DSQL2);
		$DSQL3= "DELETE FROM ".NDB_NAME.".production_details WHERE `production_id` = '$voucher_no' $pid";
		mysql_query($DSQL3);
		$DSQL4= "DELETE FROM ".NDB_NAME.".stock_ledger WHERE `voucher_no`='$voucher_no' $pid";
		mysql_query($DSQL4);
		$DSQL5= "DELETE FROM ".NDB_NAME.".account_journal WHERE `voucher_no`='$batch_no' $pid";
		mysql_query($DSQL5);
		$DSQL6= "DELETE FROM ".NDB_NAME.".production_fg WHERE `batch_no`='$batch_no' $pid";
		mysql_query($DSQL6);
		}
		$DSQL4= "DELETE FROM ".NDB_NAME.".purchase_master WHERE `purchase_date` <= '2017-12-31' $pid";
		mysql_query($DSQL4);
	}
	// End Num
	echo "Total PURCHASE =".$numrow7." Records Deleted <br>";
	*/

	/*
	$SQL8= "SELECT * FROM ".SALES_MASTER_TBL." WHERE `sales_date` <= '2017-12-31' $pid ORDER BY sales_date ASC";		
	$res8    = mysql_query($SQL8);
	$numrow8 = mysql_num_rows($res8); 
	if($numrow8 >0){
		while($arow=mysql_fetch_object($res8)){
		$voucher_no  = $arow->voucher_no;
		$DSQL1= "DELETE FROM ".NDB_NAME.".sales_details WHERE `voucher_no` = '$voucher_no' $pid";
		mysql_query($DSQL1);
		$DSQL2= "DELETE FROM ".NDB_NAME.".sales_delivery_item_master WHERE `voucher_no`='$voucher_no' $pid";
		mysql_query($DSQL2);
		$DSQL3= "DELETE FROM ".NDB_NAME.".sales_delivery_item WHERE `voucher_no` = '$voucher_no' $pid";
		mysql_query($DSQL3);
		$DSQL4= "DELETE FROM ".NDB_NAME.".stock_ledger WHERE `voucher_no`='$batch_no' $pid";
		mysql_query($DSQL4);
		$DSQL5= "DELETE FROM ".NDB_NAME.".account_journal WHERE `voucher_no`='$batch_no' $pid";
		mysql_query($DSQL5);
		$DSQL6= "DELETE FROM ".NDB_NAME.".cs_delivery_product WHERE `voucher_no` = '$voucher_no' $pid";
		mysql_query($DSQL6);
		$DSQL7= "DELETE FROM ".NDB_NAME.".credit_vouchar WHERE `voucher_no` = '$voucher_no' $pid";
		mysql_query($DSQL7);
		}
		$DSQL4= "DELETE FROM ".NDB_NAME.".sales_master WHERE `sales_date` <= '2017-12-31' $pid";
		mysql_query($DSQL4);
	}
	// End Num
	echo "Total SALES =".$numrow8." Records Deleted <br>";
	*/
	$SQL8= "SELECT * FROM ".SALES_DELIVERY_MASTER_TBL." WHERE `delivery_date` <= '2017-12-31' $pid ORDER BY delivery_date ASC";		
	$res8    = mysql_query($SQL8);
	$numrow8 = mysql_num_rows($res8); 
	if($numrow8 >0){
		while($arow=mysql_fetch_object($res8)){
		$voucher_no  = $arow->voucher_no;
		$DSQL1= "DELETE FROM ".NDB_NAME.".sales_details WHERE `voucher_no` = '$voucher_no' $pid";
		mysql_query($DSQL1);		
		$DSQL3= "DELETE FROM ".NDB_NAME.".sales_delivery_item WHERE `voucher_no` = '$voucher_no' $pid";
		mysql_query($DSQL3);
		$DSQL4= "DELETE FROM ".NDB_NAME.".stock_ledger WHERE `voucher_no`='$batch_no' $pid";
		mysql_query($DSQL4);
		$DSQL5= "DELETE FROM ".NDB_NAME.".account_journal WHERE `voucher_no`='$batch_no' $pid";
		mysql_query($DSQL5);
		$DSQL6= "DELETE FROM ".NDB_NAME.".cs_delivery_product WHERE `voucher_no` = '$voucher_no' $pid";
		mysql_query($DSQL6);
		$DSQL7= "DELETE FROM ".NDB_NAME.".credit_vouchar WHERE `voucher_no` = '$voucher_no' $pid";
		mysql_query($DSQL7);
		}
		
		$DSQL4= "DELETE FROM ".NDB_NAME.".sales_delivery_item_master WHERE `delivery_date` <= '2017-12-31' $pid";
		mysql_query($DSQL4);
	}
	// End Num
	echo "Total SALES DELIVERY =".$numrow8." Records Deleted <br>";


	/*
	$SQL9= "SELECT * FROM ".STOCK_VERIFY_MASTER_TBL." WHERE `verification_date` <= '2017-12-31' $pid ORDER BY verification_date ASC";		
	$res9    = mysql_query($SQL9);
	$numrow9 = mysql_num_rows($res9);
	if($numrow9 >0){
		while($arow=mysql_fetch_object($res9)){
		$verify_no = $arow->verify_no;
		$DSQL1="DELETE FROM ".NDB_NAME.".purchase_master WHERE `voucher_no` = '$verify_no' $pid";
		mysql_query($DSQL1);
		$DSQL2= "DELETE FROM ".NDB_NAME.".purchase_details WHERE `voucher_no` = '$verify_no' $pid";
		mysql_query($DSQL2);
		$DSQL2="DELETE FROM ".NDB_NAME.".stock_ledger WHERE `voucher_no`='$verify_no' $pid";
		mysql_query($DSQL2);
		$DSQL3="DELETE FROM ".NDB_NAME.".account_journal WHERE `voucher_no`='$verify_no' $pid";
		mysql_query($DSQL3);
		}
		$DSQL4="DELETE FROM ".NDB_NAME.".stock_verify_master WHERE `verification_date` <='2017-12-31' $pid";
		mysql_query($DSQL4);
		$DSQL5= "DELETE FROM ".NDB_NAME.".stock_verify_details WHERE `verification_date` <='2017-12-31' $pid";
		mysql_query($DSQL5);
	}
	// End Num
	echo "Total VERIFY =".$numrow9." Records Deleted <br>";

	$SQL10= "SELECT * FROM ".STOCK_TRANSFER_MASTER_TBL." WHERE `transfer_date` <= '2017-12-31' $pid ORDER BY transfer_date ASC";		
	$res10    = mysql_query($SQL10);
	$numrow10 = mysql_num_rows($res10);
	if($numrow9 >0){
		while($arow=mysql_fetch_object($res10)){
		$transfer_no  = $arow->transfer_no;
		$DSQL1= "DELETE FROM ".NDB_NAME.".purchase_master WHERE `voucher_no` = '$transfer_no' $pid";
		mysql_query($DSQL1);
		$DSQL2= "DELETE FROM ".NDB_NAME.".purchase_details WHERE `voucher_no` = '$transfer_no' $pid";
		mysql_query($DSQL2);
		$DSQL2= "DELETE FROM ".NDB_NAME.".stock_ledger WHERE `voucher_no`='$transfer_no' $pid";
		mysql_query($DSQL2);
		$DSQL3= "DELETE FROM ".NDB_NAME.".account_journal WHERE `voucher_no`='$transfer_no' $pid";
		mysql_query($DSQL3);
		}
		$DSQL4= "DELETE FROM ".NDB_NAME.".stock_transfer_master WHERE `transfer_date` <= '2017-12-31' $pid";
		mysql_query($DSQL4);
		$DSQL5= "DELETE FROM ".NDB_NAME.".stock_transfer_details WHERE `transfer_date` <= '2017-12-31' $pid";
		mysql_query($DSQL5);
	}
	// End Num
	echo "Total VERIFY =".$numrow9." Records Deleted <br>";
	*/
	mysql_query("COMMIT;");
	echo "<br>====== Done =======";	
	//======= End Clear Closing =======		
   }
   
   
       
} // End class
?>
