<?php
class BalanceSheet
{
   
   function run()
   {     
		$cmd = getRequest('cmd'); $msg="";
		$u_t_id = getFromSession('u_type_id');	
		if($u_t_id == 101 || $u_t_id == 102 || $u_t_id == 104 || $u_t_id == 109){      
		  switch($cmd){ 
		  	 case 'view'                : $screen = $this->showEditor($msg); break;
		  	 case 'view2'               : $screen = $this->showBalanceSheetV2($msg); break;
			 case 'view3'               : $screen = $this->showBalanceSheetV3($msg); break;
			 case 'inst'                : $screen = $this->showIncomeStatement($msg); break;
			 case 'cogs'                : $screen = $this->showCOGSStatement($msg); break;
			 default                    : $cmd = 'list'; $screen = $this->showEditor($msg);   break;
		  }	
		}else {
      		header("location:index.php?app=user_home&msg=You are not authorised !!!");
      	} 		
		if($cmd == 'list'){
			 require_once(CURRENT_APP_SKIN_FILE);
		}
		return true;
   }

  function showEditor($msg=NULL){		 
	  $data                		= array();	     
	  $data['message'] 		= $msg;
	  $data['cmd']     		= getRequest('cmd'); 
	  //require_once(CURRENT_APP_SKIN_FILE);
	  require_once(BALANCE_SHEET_SKIN);
	  return $data[0];
  }
  function showBalanceSheetV2($msg=NULL){		 
	$data                		= array();	     
	$data['message'] 		= $msg;
	$data['cmd']     		= getRequest('cmd');  
	
	require_once(BALANCE_SHEET_V2_SKIN);
	return $data[0];
  }
 function showBalanceSheetV3($msg=NULL){		 
	$data                		= array();	     
	$data['message'] 		= $msg;
	$data['cmd']     		= getRequest('cmd');  
	
	require_once(BALANCE_SHEET_V3_SKIN);
	return $data[0];
  }
  function showIncomeStatement(){	
   $project_id     = getFromSession('project_id'); 
   $fyear     	   = getFromSession('fyear');
   
   $data                    = array();
   // ==== Sales Head (A000015) =====
   $slhead="s.sub_headtype='S124' AND s.child_head='C000127'";
   $TotalSalesAmount = getISTotalSalesAmount($project_id,"Operating Revenue",$slhead); 
   
   $slhead="s.sub_headtype='S124' AND s.child_head='C000128' AND s.sl_three_head='S300098'";
   $TotalSalesDiscount = getISTotalSalesAmount($project_id,"Operating Revenue",$slhead); 

   $data['TotalSalesAmount']  = $TotalSalesAmount + $TotalSalesDiscount;
   $data['TotalSalesDiscount']= $TotalSalesDiscount;

   $slrhead="s.sub_headtype='S124' AND s.child_head='C000129' AND s.sl_three_head='S300099'";
   $data['TotalSalesReturn']  = getISTotalSalesAmount($project_id,"Operating Revenue",$slrhead);
   $vhead="s.sub_headtype='S135' AND s.child_head='C000126' "; // AND s.sl_three_head='S300071' AND s.sub_id='A002103'
   $data['TotalVATAmount']    = getISTotalSalesAmount($project_id,"Direct Expenses",$vhead); // VAT Lira
   //$ihead="s.sub_headtype='S121' AND s.child_head='C000131' AND s.sl_three_head='S300064' AND s.sub_id='A000027'";
   $ihead="s.sub_headtype='S121'";
   $data['OthersIncome']= getISTotalSalesAmount($project_id,"Non-Operating Revenue",$ihead); // Others Income Lira
   $NetSales =($data['TotalSalesAmount']-($data['TotalVATAmount']+$data['TotalSalesReturn']+$data['TotalSalesDiscount'])); 
   $data['REVENUE']= $NetSales;
   $data['vatAmount'] = 0;

   //===== COGS =======
   $cogshead="s.sub_id='A006351'";
   $data['TotalCOGS'] 	= getISHeadsBalance($project_id,"Cost Center",$cogshead);  
   
   $advhead="s.`sub_headtype` = 'S139' AND s.child_head='C000120'";	
   $AdministrativeExp 	= getISHeadsBalance($project_id,"Indirect Expenses",$advhead); //Adv Exp
   $data['TotalADEX'] 	= $AdministrativeExp;
   $sndvhead="s.`sub_headtype` = 'S139' AND s.child_head='C000122'";
   $data['TotalSDEX'] 	= getISHeadsBalance($project_id,"Indirect Expenses",$sndvhead); // Sales & Delivery
   $finhead="s.`sub_headtype` = 'S139' AND s.child_head='C000121'";
   $data['TotalFIEX'] 	= getISHeadsBalance($project_id,"Indirect Expenses",$finhead); // Fin Exp 
   $mishead="s.`sub_headtype` = 'S139' AND s.child_head='C000117'";
   $data['OthersExpenses']= 0;//getISHeadsBalance($project_id,"Indirect Expenses",$mishead); //Mis Exp
   //========== Profit After Tax (Tax Value)==========
   $taxhead = "s.sub_headtype='S139' AND s.child_head='C000126'";//C000156 old
   $data['TotalTaxAmount']  = getISHeadsBalance($project_id,"Indirect Expenses",$taxhead); // Tax Lira
   $data['cosgAmount'] = $this->getCogsStatementAmount(); // This line added by towheed

   $data['dateFrom'] = getRequest('date_from');
   $data['dateTo'] = getRequest('date_to');
   $data['cmd']     	    = getRequest('cmd'); 
   require_once(INCOME_STATEMENT_SKIN);
   return $data[0];
  }

  function showCOGSStatement(){	
   $project_id     = getFromSession('project_id'); 
   $fyear     	   = getFromSession('fyear');
   
   $data                    = array();
	// ==== Sales Head (A000015) =====
//        $slhead="s.sub_headtype='S124' AND s.child_head='C000127'";
//        $TotalSalesAmount = getISTotalSalesAmount($project_id,"Operating Revenue",$slhead);
//
//        $slhead="s.sub_headtype='S124' AND s.child_head='C000128' AND s.sl_three_head='S300098'";
//        $TotalSalesDiscount = getISTotalSalesAmount($project_id,"Operating Revenue",$slhead);
//
//        $data['TotalSalesAmount']  = $TotalSalesAmount + $TotalSalesDiscount;
//        $data['TotalSalesDiscount']= $TotalSalesDiscount;
//        $data['TotalNetSales']     = $TotalSalesAmount;
//        $slrhead="s.sub_headtype='S124' AND s.child_head='C000129' AND s.sl_three_head='S300099'";
//        $data['TotalSalesReturn']  = getISTotalSalesAmount($project_id,"Operating Revenue",$slrhead);
//        $vhead="s.sub_headtype='S135' AND s.child_head='C000126' "; // AND s.sl_three_head='S300071' AND s.sub_id='A002103'
//        $data['TotalVATAmount']    = getISTotalSalesAmount($project_id,"Direct Expenses",$vhead); // VAT Lira
//        $ihead="s.sub_headtype='S121' AND s.child_head='C000131' AND s.sl_three_head='S300064' AND s.sub_id='A000027'";
//        $data['OthersIncome']= getISTotalSalesAmount($project_id,"Non-Operating Revenue",$ihead); // Others Income Lira
//        //===== Raw Materials =======
//        $rmhead="s.`sub_headtype` = 'S127' AND s.child_head='C000055' AND s.sl_three_head='S300030'";
//        $data['TotalRMOB'] 	= getISProductOpeingValue($project_id,"Current Assets",$rmhead); // A000017 is PO RM
//        $data['TotalRMPB'] 	= getISProductPurchaseValue($project_id,"Current Assets",$rmhead);
//        $data['TotalRMCB'] 	= getISProductClosingValue($project_id,"Current Assets",$rmhead);
//        $crhead="s.sub_headtype='S138' AND s.child_head='C000118' AND s.sl_three_head='S300104'";
//        $CarringInWardCost   = getISTotalSalesAmount($project_id,"Direct Expenses",$crhead);
//        $data['CarringInExp']= $CarringInWardCost;
//        //===== WIP =======
//        $wphead="s.`sub_headtype` = 'S127' AND s.child_head='C000057' AND s.sl_three_head='S300031'";
//        $data['TotalWPOB'] 	= getISProductOpeingValue($project_id,"Current Assets",$wphead); // A000018 is WIP
//        $data['TotalWPPB'] 	= getISProductPurchaseValue($project_id,"Current Assets",$wphead);
//        $data['TotalWPCB'] 	= getISProductClosingValue($project_id,"Current Assets",$wphead);
//        //===== Finished Goods =======
//        $fghead="s.`sub_headtype` = 'S127' AND s.child_head='C000056' AND s.sl_three_head='S300029'";
//        $data['TotalFGOB'] 	= getISProductOpeingValue($project_id,"Current Assets",$fghead); // A000036 is FG
//        $data['TotalFGPB'] 	= getISProductPurchaseValue($project_id,"Current Assets",$fghead);
//        $data['TotalFGCB'] 	= getISProductClosingValue($project_id,"Current Assets",$fghead);
//        $fgstockid="A000036";
//        $data['TotalCOGS'] 	= $this->getSalesOfCostAmount($fgstockid,$project_id);
//        //===== Purchase Discount =======
//        $pdhead="s.`sub_headtype` = 'S127' AND s.child_head='C000136' AND s.sl_three_head='S300075'";
//        $data['PurchaseDiscount'] = getISProductClosingValue($project_id,"Current Assets",$pdhead);
//        $prhead="s.`sub_headtype` = 'S127' AND s.child_head='C000135' AND s.sl_three_head='S300076'";
//        $data['PurchaseReturn']   = getISProductClosingValue($project_id,"Current Assets",$prhead);
//
//        $fovchead="s.`sub_headtype` = 'S138' AND s.child_head='C000118' AND s.sl_three_head='S300026'"; //Ov
//        $data['TotalFOVC'] 	     = getISHeadsBalance($project_id,"Direct Expenses",$fovchead);
//        $pakmhead="s.`sub_headtype` = 'S139' AND s.child_head='C000155'";
//
//        $data['TotalPackMat']     = getISHeadsBalance($project_id,"Indirect Expenses",$pakmhead);
//
//        $advhead="s.`sub_headtype` = 'S139' AND s.child_head='C000120'";
//        $AdministrativeExp 	= getISHeadsBalance($project_id,"Indirect Expenses",$advhead); //Adv Exp
//        $data['TotalADEX'] 	= $AdministrativeExp;
//        $sndvhead="s.`sub_headtype` = 'S139' AND s.child_head='C000122'";
//        $data['TotalSDEX'] 	= getISHeadsBalance($project_id,"Indirect Expenses",$sndvhead); // Sales & Delivery
//        $finhead="s.`sub_headtype` = 'S139' AND s.child_head='C000121'";
//        $data['TotalFIEX'] 	= getISHeadsBalance($project_id,"Indirect Expenses",$finhead); // Fin Exp
//        $mishead="s.`sub_headtype` = 'S139' AND s.child_head='C000117'";
//        $data['OthersExpenses']= getISHeadsBalance($project_id,"Indirect Expenses",$mishead); //Mis Exp
//        //========== Profit After Tax (Tax Value)==========
//        $taxhead = "s.sub_headtype='S139' AND s.child_head='C000156'";
//        $data['TotalTaxAmount']  = getISHeadsBalance($project_id,"Indirect Expenses",$taxhead); // Tax Lira


   $rmhead = "s.`sub_headtype` = 'S127' AND s.child_head='C000055' AND s.sl_three_head='S300030'";
        $data['TotalRMOB'] = getISProductOpeingValue($project_id, "Current Assets", $rmhead); // A000017 is PO RM
        $data['TotalRMPB'] = getISProductPurchaseValue($project_id, "Current Assets", $rmhead);
        $data['TotalRMCB'] = getISProductClosingValue($project_id, "Current Assets", $rmhead);

        $prhead = "s.`sub_headtype` = 'S127' AND s.child_head='C000135' AND s.sl_three_head='S300076'";
        $data['PurchaseReturn'] = getISProductClosingValue($project_id, "Current Assets", $prhead);

//        $fovchead = "s.`sub_headtype` = 'S138' AND s.child_head='C000118' AND s.sl_three_head='S300026'";
//        $TotalFOVC = $this->getCOGSISHeadsBalance($project_id, "Direct Expenses", $fovchead, $date_from, $date_to);
        $data['TotalFOVC'] = $this->getSLDrBalance($project_id, "Direct Expenses", "S138", "C000118");

        $pakmhead = "s.`sub_headtype` = 'S139' AND s.child_head='C000155'";
        $data['TotalPackMat'] = $this->getCOGSISHeadsBalance($project_id, "Indirect Expenses", $pakmhead);

        $wphead = "s.`sub_headtype` = 'S127' AND s.child_head='C000057' AND s.sl_three_head='S300031'";
        $data['TotalWPOB'] = getISProductOpeingValue($project_id, "Current Assets", $wphead); // A000018 is WIP
        $data['TotalWPCB'] = getISProductClosingValue($project_id, "Current Assets", $wphead);

        $fghead = "s.`sub_headtype` = 'S127' AND s.child_head='C000056' AND s.sl_three_head='S300029'";
        $data['TotalFGOB'] = getISProductOpeingValue($project_id, "Current Assets", $fghead); // A000036 is FG
//        $TotalFGPB = getISProductPurchaseValue($project_id, "Current Assets", $fghead);
        $data['TotalFGPB'] = 0;
        $data['TotalFGCB'] = getISProductClosingValue($project_id, "Current Assets", $fghead);

        $fgstockid = "A000036";
        $data['TotalCOGS'] = $this->getSalesOfCostAmount($fgstockid, $project_id);

	$data['dateFrom'] = getRequest('date_from');
        $data['dateTo'] = getRequest('date_to');

   $data['cmd']     	    = getRequest('cmd'); 
   require_once(COGS_STATEMENT_SKIN);
   return $data[0];
  }


  function getSalesOfCostAmount($acc_head,$project_id){
	$from_date = formatDate(getRequest('date_from')); $to_date = formatDate(getRequest('date_to'));
	$sql = "SELECT sum(`cr`) as credit_amount FROM ".ACCOUNT_JOURNAL_TBL." WHERE sub_id = '$acc_head' AND project_id = '$project_id'";
	if($from_date!="" && $to_date !=""){
	$sql.=" AND created_date BETWEEN '$from_date' AND '$to_date'";
	}
	$row = mysql_fetch_object(mysql_query($sql));
	$credit_amount = $row->credit_amount;
	if(empty($credit_amount)){
		$credit_amount = 0;
	}
	return $credit_amount;
   }


/**
     * This function added by towheed
     * get cogs statement start
     */
    function getCogsStatementAmount()
    {
        $project_id = getFromSession('project_id');

        $rmhead = "s.sub_headtype = 'S127' AND s.child_head='C000055' AND s.sl_three_head='S300030'";
        $TotalRMOB = getISProductOpeingValue($project_id, "Current Assets", $rmhead); // A000017 is PO RM
        $TotalRMPB = getISProductPurchaseValue($project_id, "Current Assets", $rmhead);
        $TotalRMCB = getISProductClosingValue($project_id, "Current Assets", $rmhead);

        $prhead = "s.sub_headtype = 'S127' AND s.child_head='C000135' AND s.sl_three_head='S300076'";
        $PurchaseReturn = getISProductClosingValue($project_id, "Current Assets", $prhead);

        $TotalFOVC = $this->getSLDrBalance($project_id, "Direct Expenses", "S138", "C000118");

        $pakmhead = "s.sub_headtype = 'S139' AND s.child_head='C000155'";
        $TotalPackMat = $this->getCOGSISHeadsBalance($project_id, "Indirect Expenses", $pakmhead);

        $wphead = "s.sub_headtype = 'S127' AND s.child_head='C000057' AND s.sl_three_head='S300031'";
        $TotalWPOB = getISProductOpeingValue($project_id, "Current Assets", $wphead); // A000018 is WIP
        $TotalWPCB = getISProductClosingValue($project_id, "Current Assets", $wphead);

        $fghead = "s.sub_headtype = 'S127' AND s.child_head='C000056' AND s.sl_three_head='S300029'";
        $TotalFGOB = getISProductOpeingValue($project_id, "Current Assets", $fghead); // A000036 is FG
        $TotalFGPB = 0;
        $TotalFGCB = getISProductClosingValue($project_id, "Current Assets", $fghead);

        $fgstockid = "A000036";
        $TotalCOGS = $this->getSalesOfCostAmount($fgstockid, $project_id);

        $CostOfGS = $TotalRMOB;
        $CostOfGS += $TotalRMPB;
        $CostOfGS = ($CostOfGS - abs($PurchaseReturn));
        $CostOfGS = ($CostOfGS - $TotalRMCB);
        $CostOfGS = ($CostOfGS + $TotalFOVC);
        $CostOfGS = ($CostOfGS + $TotalWPOB);
        $CostOfGS = ($CostOfGS - $TotalWPCB);
        $CostOfGS = ($CostOfGS + $TotalFGOB);
        $CostOfGS = ($CostOfGS + $TotalFGPB);
        $AvailableSales = $CostOfGS;
        $CostOfGS = ($CostOfGS - $TotalCOGS);

        if ($CostOfGS != "") {
            $CostOfGS = ($AvailableSales - $TotalFGCB);
            $returnAmount = (float)$CostOfGS;
        } else {
            $returnAmount = 0.00;
        }

        return $returnAmount;
    }

    /**
     * This function added by towheed
     *
     */

	function getSLDrBalance($project_id,$head_type,$subhead_type=NULL,$childheadtype=NULL){
		$from_date = formatDate(getRequest('date_from')); $to_date = formatDate(getRequest('date_to'));
		$totalAmount	= 0;	
		$bsql="SELECT SUM(a.dr) AS balance FROM ".ACCOUNT_JOURNAL_TBL." AS a,".SUB_ACC_HEAD_TBL." AS s WHERE BINARY a.`sub_id` = s.`sub_id` AND s.project_id = '$project_id' AND s.`head_type` = '$head_type' ";
		if($subhead_type!=""){ $bsql.=" AND s.sub_headtype='$subhead_type'"; }	
		if($childheadtype!=""){ $bsql.=" AND s.child_head='$childheadtype'"; }	
		if($from_date!="" && $to_date !=""){
		$bsql.=" AND a.created_date BETWEEN '$from_date' AND '$to_date'";
		}else{
		$from_date = date('Y-m-d');
		$bsql.=" AND a.created_date > '$from_date'";
		}
		$bres= mysql_query($bsql);
		if(mysql_num_rows($bres)>0){
			$brow = mysql_fetch_object($bres);
			if($brow->balance!=""){$totalAmount=$brow->balance;}else{ $totalAmount=0;}			
		}	
		return $totalAmount;	
	}


    function getCOGSISHeadsBalance($project_id, $head_type, $head_id = NULL)
    {
        $from_date = formatDate(getRequest('date_from'));
        $to_date = formatDate(getRequest('date_to'));
        $totalAmount = 0;
        $bsql = "SELECT SUM(a.dr) AS balance FROM " . ACCOUNT_JOURNAL_TBL . " AS a," . SUB_ACC_HEAD_TBL . " AS s WHERE BINARY a.sub_id = s.sub_id AND s.project_id = '$project_id' AND s.head_type = '$head_type' ";
        if ($head_id != "") {
            $bsql .= " AND $head_id ";
        }
        if ($from_date != "" && $to_date != "") {
            $bsql .= " AND a.created_date BETWEEN '$from_date' AND '$to_date'";
        } else {
            $from_date = date('Y-m-d');
            $bsql .= " AND a.created_date > '$from_date'";
        }
        $bres = mysql_query($bsql);
        if (mysql_num_rows($bres) > 0) {
            $brow = mysql_fetch_object($bres);
            if ($brow->balance != "") {
                $totalAmount = $brow->balance;
            } else {
                $totalAmount = 0;
            }
        }
        return $totalAmount;
    }
      
}








 // End class
?>
