<?php
class SalesAreaUpdate
{
   function run() {     
		$cmd    = getRequest('cmd');
		$u_t_id = getFromSession('u_type_id');
		if($u_t_id == 101) 
		{      
		  switch($cmd) { 
		  	 case 'add'                	: $screen = $this->showEditor($msg); break;
			 default                   	: $cmd = 'list'; $screen = $this->showEditor($msg);   break;
		  }
		}else {
      		header("location:index.php?app=user_home&msg=You are not authorised !!!");
      	} 
		
		return true;
  }
  function showEditor()
  {
	 $this->updateSalesArea("2016-01-01");
	 echo "==Sales Area Update Done==";
		
   }
   function updateSalesArea($sales_date){	 	
		$project_id  = getFromSession('project_id'); 	 	
	
		$SQL="SELECT voucher_no,customer FROM ".SALES_MASTER_TBL." WHERE item_delivery_amount >0 AND `sales_date` >= '$sales_date' GROUP BY `voucher_no`";
		$dvres 	= mysql_query($SQL);
		while($smrow = mysql_fetch_object($dvres)){
			$voucher_no = $smrow->voucher_no;
			$customer = $smrow->customer;
			
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
			$USQL="UPDATE ".SALES_DELIVERY_CHALLAN_TBL." SET division= '$division',district= '$district',area= '$area'  WHERE `voucher_no` = '$voucher_no'";
			 mysql_query($USQL);			
		}
		
		  
   }        
} // End class
?>