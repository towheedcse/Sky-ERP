<?php
class BalanceSheet
{
   
   function run()
   {     
		$cmd = getRequest('cmd');
		$u_t_id = getFromSession('u_type_id');	
		if($u_t_id == 101 || $u_t_id == 102 || $u_t_id == 104){      
		  switch($cmd){ 
		  	 case 'view'                : $screen = $this->showEditor($msg); break;
			 case 'inst'                : $screen = $this->showIncomeStatement($msg); break;
			 default                   	: $cmd = 'list'; $screen = $this->showEditor($msg);   break;
		  }	
		}else {
      		header("location:index.php?app=user_home&msg=You are not authorised !!!");
      	} 		
		if($cmd == 'list'){
			 require_once(CURRENT_APP_SKIN_FILE);
		}
		return true;
   }

  function showEditor(){		 
	  $data                		= array();	     
	  $data['message'] 			= $msg;
	  $data['cmd']     			= getRequest('cmd'); 
	  //require_once(CURRENT_APP_SKIN_FILE);
	  require_once(BALANCE_SHEET_SKIN);
	  return $data[0];
  }
  function showIncomeStatement(){	
	$project_id     = getFromSession('project_id'); 
	$fyear     		= getFromSession('fyear');
	if($fyear==""){ $fyear="FY001";}
	//========== All Head Setting for Samrat ==============
	$data                			= array();
	$data['TotalSalesAmount'] 		= getISTotalSalesAmount($project_id);
	$data['TotalVATAmount'] 		= getISHeadsBalance($project_id,"Overhead Cost","A001127"); // VAT (A002103 Lira & A001127 Samrat)
	
	$data['TotalRMOB'] 				= getISProductOpeingValue($project_id,"Raw Materials",$fyear); // FY001 is Financial Year
	$data['TotalRMPB'] 				= getISProductPurchaseValue($project_id,"Raw Materials");
	$data['TotalRMCB'] 				= getISProductClosingValue($project_id,"Raw Materials");
	
	$data['TotalFGOB'] 				= getISProductOpeingValue($project_id,"Sales Item",$fyear);
	$data['TotalFGCB'] 				= getISProductClosingValue($project_id,"Sales Item");	
	$data['TotalFOVC'] 				= getBSFixedAsseteBalance("Overhead Cost");
	
	$data['TotalADEX'] 				= getBSFixedAsseteBalance("Administrative Cost","S117");
	$data['TotalSDEX'] 				= getBSFixedAsseteBalance("Administrative Cost","S118");
	$data['TotalFIEX'] 				= getBSFixedAsseteBalance("Administrative Cost","S119");
	$data['OthersIncome'] 			= getISHeadsBalance($project_id,"Incomes","A000966|A001072"); 
	// Others Income 				(Lira A002312|A002323 & Samrat A000966|A001072)
	$data['OthersExpenses'] 		= getISHeadsBalance($project_id,"Administrative Cost","A001126"); 
	// Others Expenses 				(Lira A002131 Overhead Cost & Samrat A001126 Administrative Cost) 
	
	$data['TotalExpanceAmount'] 	= getTotalExpanceAmount($project_id,$fyear);	     
	$data['TotalPurchaseAmount'] 	= getTotalPurchaseAmount($project_id,$fyear);
	$data['cmd']     				= getRequest('cmd'); 
	require_once(INCOME_STATEMENT_SKIN);
	return $data[0];
  }
      
} // End class
?>