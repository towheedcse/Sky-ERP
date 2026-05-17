<?php
class Journal
{
   function run(){         
      $cmd = getRequest('cmd');
	  $u_t_id = getFromSession('u_type_id');

      if( ($u_t_id == 101) || ($u_t_id == 102) || ($u_t_id == 104)) //1 = admin 2 = Sales man
      {

      	switch ($cmd)
      	{
      	   case 'add'                : $screen = $this->showEditor($msg); break;
      	   case 'edit'               : $screen = $this->showEditor("Edit Page");    break;	
      	   case 'opening'            : $screen = $this->showEditor4OpeningBalance($msg);    break;	
      	   case 'doUpdate'           : $this->updateJournal(); break;
	   case 'delete'             : $screen = $this->deleteJournal(); break;
      	   case 'vouchar_print'      : $screen = $this->showPrintEditor(getRequest('voucher_no'));   break;
      	   case 'bnkdeposit'         : $screen = $this->showBankDepositEditor(getRequest('voucher_no'));   break;
      	   case 'transaction_lst'    : $screen = $this->showTransactionEditor();   break;  
      	   case 'general_journal'    : $screen = $this->showGeneralJournalEditor();   break;   
      	   case 'due_payment_list'   : $screen = $this->showDuePaymentEditor();   break;     
      	   case 'due_receive_list'   : $screen = $this->showDueReceivableEditor();   break;     
      	   case 'pbl_check'          : $screen = $this->showEditor4PayableCheck();   break;  
	   case 'rbl_check'          : $screen = $this->showEditor4ReceivableCheck();   break; 
	   case 'withdraw'           : $screen = $this->withdrawAmount(getRequest('v_no'), getRequest('acc_no'),getRequest('amount')); break;  
	   case 'deposit'           : $screen = $this->showdDepositEditor(getRequest('v_no')); break;
	   case 'savedeposit'       : $screen = $this->depositAmount(getRequest('v_no')); break;
	   case 'list'              : $screen = $this->showList($msg);   break;
      	   default                  : $screen = $this->showEditor($msg);   break;
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

   function showList($msg = null) {  
	  $data                				= array();
	  $data['cmd']         				= getRequest('cmd');
	  $data['TotalDebitAmount']			= $this->getTotalDebitAmount();
	  $data['TotalCreditAmount']		= $this->getTotalCreditAmount(); 
	   if(getRequest('deleted')=='yes') {
		  $data['message'] = "Item Deleted Successfully";
	   }elseif(getRequest('deleted')=='no') {
		  $data['message'] = "Item Not Deleted";
	   }
	   require_once(CURRENT_APP_SKIN_FILE); 
	   return $data[0];
   }
   
	function showPrintEditor($ID) { 
  
	   $project_id = getFromSession('project_id');
	  if ($ID) {
	$vouchar_type = getRequest('vouchar_type');
            if ($vouchar_type == "sales_invoice") {
                $sdSql = "SELECT sd.sales_delivery_master_id as deliveryid FROM " . SALES_DELIVERY_MASTER_TBL . " as sd, " . SALES_MASTER_TBL . " as sm WHERE sd.voucher_no = sm.voucher_no AND sd.voucher_no = '$ID' AND sd.project_id='$project_id'";
                $sdres = mysql_query($sdSql);
                $sdrow = mysql_fetch_object($sdres);
                if (isset($sdrow->deliveryid) && $sdrow->deliveryid != "") {
                    $sdm_id = $sdrow->deliveryid;
                    header("Location: ./index.php?app=delivery_challan&cmd=print_invoice&voucher_no=$ID&sdm_id=$sdm_id");
                    exit();
                }
            }
            if ($vouchar_type == "purchase_invoice") {
                header("Location: ./index.php?app=fg.production&cmd=print_report&production_id=$ID");
                exit();
            }
            if ($vouchar_type == "contra_voucher") {
                $cvSql = "SELECT contra_id FROM " . CONTRA_MASTER_TBL . " WHERE voucher_no = '$ID' AND project_id='$project_id'";
                $cvres = mysql_query($cvSql);
                $cvmrow = mysql_fetch_object($cvres);
                if (isset($cvmrow->contra_id) && $cvmrow->contra_id != "") {
                    $contra_id = $cvmrow->contra_id;
                    header("Location: ./index.php?app=contra.voucher.new&cmd=print_vouchar&contra_id=$contra_id");
                    exit();
                }
            }
         $advArr = $this->getDebitVoucharDetails($ID);
         $advArr = parseThisValue($advArr);  

         $data   = array_merge(array(), $advArr); 
		 $data['message'] = $msg;
      	 $data['cmd']     = getRequest('cmd');
	  	 require_once(VOUCHAR_PRINT_SKIN);

      }else{
		require_once(PRINT_VOUCHAR_SKIN);
	  }

      return true;
   }

	function showBankDepositEditor($ID) {     
	  if ($ID) {
         $advArr1 = $this->getTransactionDetails($ID);
         $advArr1 = parseThisValue($advArr1);  
         $data   = array_merge(array(),$advArr1); 
      }
	  $data['bank_list'] 		= $this->getBankList();	
	  $data['message'] = $msg;
      $data['cmd']     = getRequest('cmd');
	  require_once(BANK_DEPOSIT_SKIN);      
      return true;

   }
	function getBankList()
   	{	
		  $data 			= array(); 
		  $info        		=  array();
		  $info['table']	= BANK_TBL;
		  $info['fields'] = array('bank_id','bank_name');
		  $res            	=	 select($info);      
	
		  if(count($res)){
			 foreach($res as $i=>$v){
				$data[$i] = $v;             
			 }
		  }
		  return $data;	
   	}  
	function showTransactionEditor($msg = null) { 
	  $data                				= array();
	  $data['cmd']         				= getRequest('cmd');
	  $data['record_list'] 				= $this->getTransactionList(getRequest('from'),getRequest('to'));
	  $data['totalrecord']				= $this->getTotalTransactionList(getRequest('from'),getRequest('to'));	
	   require_once(SHOW_TRANSACTION_LIST_SKIN); 
	   return $data[0];

   }
	function showGeneralJournalEditor($msg = null) {        
	  $data                				= array();
	  $data['cmd']         				= getRequest('cmd');
	  $data['record_list'] 				= $this->getTransactionList(getRequest('from'),getRequest('to'));
	  $data['totalrecord']				= $this->getTotalTransactionList(getRequest('from'),getRequest('to'));	
	   require_once(SHOW_GENERAL_JOURNAL_SKIN); 
	   return $data[0];
   }
    function showDuePaymentEditor($msg = null) { 
	  require_once(CLASS_DIR.'/common.list.class.php');	
	  $clistApp 	= new CommonList();       
	  require_once(CLASS_DIR.'/sales.commission.class.php');	
	  $slsApp = new SalesCommission();
	  require_once(CLASS_DIR.'/salary.sheet.class.php');	
	  $salaryApp = new SalarySheet();
	  require_once(CLASS_DIR.'/sales.return.class.php');	
	  $slsReturnApp = new SalesReturn();
	  $data                				= array();
	  $data['cmd']         				= getRequest('cmd');
	  $data['record_list'] 				= $this->getDuePaymentList(getRequest('from'),getRequest('to'));	  
	  $data['comission_list'] 			= $slsApp->getSalesCommissionList(getRequest('from'),getRequest('to'));	  
	  $data['salary_list'] 				= $salaryApp->getApprovedSalaryList(getRequest('from'),getRequest('to'));
	  $data['totalrecord']				= $this->getTotalDuePaymentList(getRequest('from'),getRequest('to'));	
	  $data['sr_record_list'] 			= $slsReturnApp->getDueSalesReturnPaybleList(getRequest('from'),getRequest('to'));	  	
	  //$data['suplier_pbl_list'] 		= $slsReturnApp->getDueSupplierPaybleList(getRequest('from'),getRequest('to'));	
	  
	  
	$data['headlist1']   	= $clistApp->getAccountHeadList("Current Assets","S130"); // Cash & Bank
	$data['headlist2']   	= $clistApp->getAccountHeadList("Current Assets","S128","C000105"); // Party
	$data['headlist3']   	= $clistApp->getAccountHeadList("Non Current Assets","","","S130","C000105"); 
	$data['headlist4']   	= $clistApp->getAccountHeadList("Current Assets","","","S130","C000105");
	$data['headlist5']   	= $clistApp->getAccountHeadList("Liabilities");		
	$data['headlist6']   	= $clistApp->getAccountHeadList("Capital");	
	$data['headlist7']   	= $clistApp->getAccountHeadList("Retained earnings");	
	$data['headlist8']   	= $clistApp->getAccountHeadList("Sales");	
	$data['headlist9']   	= $clistApp->getAccountHeadList("Incomes");	
	$data['headlist10']   	= $clistApp->getAccountHeadList("Direct Expenses");		
	$data['headlist11']   	= $clistApp->getAccountHeadList("Indirect Expenses");	
	$data['headlist12']   	= $clistApp->getAccountHeadList("Opening Balance");	
	$data['headlist13']   	= $clistApp->getAccountHeadList("Adjustments Balance");	
	$data['headlist14']   	= $clistApp->getAccountHeadList("Closing Balance");	
	$data['supplier_list']  = $clistApp->getSupplierList();	
	   	
	  if(getFromSession('project_type')!='Group Company'){
	   	require_once(DUE_PAYMENT_LIST_SKIN); 
	  }else{
		require_once(ADMIN_DUE_PAYMENT_LIST_SKIN); 
	  }
	   return $data[0];

   }
    function showDueReceivableEditor($msg = null) {  
	require_once(CLASS_DIR.'/common.list.class.php');	
	$clistApp 	= new CommonList();         
	$data                	= array();
	$data['cmd']         	= getRequest('cmd');
	$data['headlist1']   	= $clistApp->getAccountHeadList("Current Assets","S130"); // Cash & Bank
	$data['headlist2']   	= $clistApp->getAccountHeadList("Current Assets","S128","C000105"); // Party
	$data['headlist3']   	= $clistApp->getAccountHeadList("Non Current Assets","","","S130","C000105"); 
	$data['headlist4']   	= $clistApp->getAccountHeadList("Current Assets","","","S130","C000105");
	$data['headlist5']   	= $clistApp->getAccountHeadList("Liabilities");		
	$data['headlist6']   	= $clistApp->getAccountHeadList("Capital");	
	$data['headlist7']   	= $clistApp->getAccountHeadList("Retained earnings");	
	$data['headlist8']   	= $clistApp->getAccountHeadList("Sales");	
	$data['headlist9']   	= $clistApp->getAccountHeadList("Incomes");	
	$data['headlist10']   	= $clistApp->getAccountHeadList("Direct Expenses");		
	$data['headlist11']   	= $clistApp->getAccountHeadList("Indirect Expenses");	
	$data['headlist12']   	= $clistApp->getAccountHeadList("Opening Balance");	
	$data['headlist13']   	= $clistApp->getAccountHeadList("Adjustments Balance");	
	$data['headlist14']   	= $clistApp->getAccountHeadList("Closing Balance");	
	$data['supplier_list']  = $clistApp->getSupplierList();	
	$data['record_list'] 	= $this->getDueReceivedList(getRequest('from'),getRequest('to'));
	$data['totalrecord']	= $this->getTotalDueReceivedList(getRequest('from'),getRequest('to'));	
	if(getFromSession('project_type')!='Group Company'){
	require_once(DUE_RECEIVABLE_LIST_SKIN); 
	}else{
	require_once(ADMIN_DUE_RECEIVABLE_LIST_SKIN); 
	}
	return $data[0];
   }
	
	function getTransactionList($from,$to) { 
		if($from == "" && $to == ""){$from=0; $to=1500;}
		$date_from 		= formatDate(getRequest('date_from'));
		$date_to 		= formatDate(getRequest('date_to'));
		$vouchar_type 	= getRequest('vouchar_type'); 
		$branch_id 		= getRequest('branch_id');
		$post_by 		= getRequest('created_by');				
		$project_id 	= getFromSession('project_id');
		$info           = array(); 
		$info['table']  =  DEVIT_VOUCHAR_TBL.' t,'.CURRENCY_TBL.' c,' . CONTRA_MASTER_TBL . ' cm';
	    $info['fields'] = array('t.voucher_no','t.branch_id','t.custom_voucher_no','t.account_head','t.project_id','t.head_type','t.received_id', 'c.curr_symble','t.mode_of_payment','t.bank_name','t.acc_no','t.check_no',"DATE_FORMAT(t.check_issue_date,'%d %b %y' ) as check_issue_date",'t.ref_no','t.vouchar_type','t.transaction_type','t.transaction_name','t.delivery_bag_qty','t.credit','t.debit','t.service_charge','t.description',"DATE_FORMAT(t.created_date,'%d %b %y' ) as created_date","t.created_date as createddate","cm.created_by", "cm.approved_by"); 
		$sql="t.project_id = '$project_id' AND t.currency = c.currency_id AND t.voucher_no = cm.voucher_no";
		if($branch_id!=""){
			$sql.=" AND t.branch_id = '$branch_id'";
		}
		if($post_by !=""){
			$sql.=" AND t.created_by = '$post_by'";
		}
		if($date_from!="" && $date_to ==""){
			$sql.=" AND t.created_date >= '$date_from'";
		}elseif($date_from=="" && $date_to !=""){
			$sql.=" AND t.created_date <= '$date_to'";
		}elseif($date_from!="" && $date_to !=""){
			$sql.=" AND t.created_date BETWEEN '$date_from' AND '$date_to'";
		}
		if($vouchar_type=="Journal Voucher"){ $sql.=" AND t.vouchar_type != 'Sales Order' AND t.transaction_name != 'Purchase' AND t.bank_journal != 'Yes'"; }
		elseif($vouchar_type=="Bank Journal Voucher"){ $sql.=" AND t.bank_journal = 'Yes'"; }
		elseif($vouchar_type=="Purchase"){ $sql.=" AND t.transaction_name = '$vouchar_type' "; }
		$info['where']  =$sql;	
		$info['orderby'] = array("t.voucher_no ASC LIMIT $from,$to");
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

   function getTotalTransactionList($from,$to) {  
		$date_from 		= formatDate(getRequest('date_from'));
		$date_to 		= formatDate(getRequest('date_to'));
		if($date_from==""){ $date_from = date("Y-m-d");}
		if($date_to==""){ $date_to = date("Y-m-d");}	
		$vouchar_type 	= getRequest('vouchar_type');	 
		$branch_id 		= getRequest('branch_id');
		$post_by 		= getRequest('created_by');					
		$project_id 	= getFromSession('project_id');
		$info           = array(); 
		$info['table']  =  DEVIT_VOUCHAR_TBL.' t';
	    $info['fields'] = array('t.voucher_no'); 
		$sql="t.project_id = '$project_id'";
		if($branch_id!=""){
			$sql.=" AND t.branch_id = '$branch_id'";
		}
		if($post_by !=""){
			$sql.=" AND t.created_by = '$post_by'";
		}
		if($date_from!="" && $date_to ==""){
			$sql.=" AND t.created_date >= '$date_from'";
		}elseif($date_from=="" && $date_to !=""){
			$sql.=" AND t.created_date <= '$date_to'";
		}elseif($date_from!="" && $date_to !=""){
			$sql.=" AND t.created_date BETWEEN '$date_from' AND '$date_to'";
		}
		if($vouchar_type=="Journal Voucher"){ $sql.=" AND t.vouchar_type != 'Sales Order' AND t.transaction_name != 'Purchase' AND t.bank_journal != 'Yes'"; }
		elseif($vouchar_type=="Bank Journal Voucher"){ $sql.=" AND t.bank_journal = 'Yes'"; }
		elseif($vouchar_type=="Purchase"){ $sql.=" AND t.transaction_name = '$vouchar_type' "; }
		$info['where']  =$sql;	
		$info['orderby'] = array("t.voucher_no asc");
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
	function getDuePaymentList($from,$to) { 

		if($from == "" && $to == ""){$from=0; $to=700;}
		$date_from 		= formatDate(getRequest('date_from'));
		$date_to 		= formatDate(getRequest('date_to'));
		if($date_from==""){ $date_from = date("Y-m-d");}
		if($date_to==""){ $date_to = date("Y-m-d");}					
		$project_id     = getFromSession('project_id'); 
		$cr_account 	= getRequest('cr_account'); 
		$info           = array();    
		$info['table']  = PURCHASE_MASTER_TBL.' pm,'.SUPPLIER_TBL.' s,'.PROJECT_TBL.' p,'.CURRENCY_TBL.' c';	
		$info['fields'] = array('pm.voucher_no','pm.po_no','pm.project_id','p.project_name','p.location','s.supplier_code','s.name','s.address','pm.quotation_no','pm.track_no','pm.van_no','pm.total_value',"DATE_FORMAT(pm.purchase_date,'%d %b %y' ) as purchase_date",'pm.mode_of_payment','c.curr_symble','pm.bank_name','pm.acc_no','pm.check_no','pm.check_no','pm.discount','pm.net_payble','pm.paid_amount','pm.due','pm.previour_balance','pm.ref_no',"DATE_FORMAT(pm.created_date,'%d %b %y' ) as date",'pm.created_date');
		if(getFromSession('project_type')!='Group Company'){
		$sql="pm.supplier = s.supplier_code AND pm.project_id = p.project_id AND pm.currency = c.currency_id AND pm.project_id = '".$project_id."' AND pm.due>0 ";
		}else{
		$sql="pm.supplier = s.supplier_code AND pm.project_id = p.project_id AND pm.currency = c.currency_id AND pm.due>0 ";
		}
		if($cr_account!="All" && $cr_account!="Customer" && $cr_account!="Supplier"){
			$sql.=" AND pm.supplier = '$cr_account' ";
		}
		if($date_from!="" && $date_to ==""){
			$sql.=" AND pm.purchase_date >= '$date_from'";
		}elseif($date_from=="" && $date_to !=""){
			$sql.=" AND pm.purchase_date <= '$date_to'";
		}elseif($date_from!="" && $date_to !=""){
			$sql.=" AND pm.purchase_date BETWEEN '$date_from' AND '$date_to'";
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

   function getTotalDuePaymentList($from,$to) {  
		$date_from 		= formatDate(getRequest('date_from'));
		$date_to 		= formatDate(getRequest('date_to'));				
		$project_id     = getFromSession('project_id'); 
		$cr_account 	= getRequest('cr_account');  
		$info           = array();    
		$info['table']  = PURCHASE_MASTER_TBL.' pm,'.SUPPLIER_TBL.' s,'.PROJECT_TBL.' p,'.CURRENCY_TBL.' c';	
		$info['fields'] = array('pm.voucher_no','p.project_name','p.location','s.name','s.address','pm.quotation_no','pm.track_no','pm.van_no','pm.total_value',"DATE_FORMAT(pm.purchase_date,'%d %b %y' ) as purchase_date",'pm.mode_of_payment','c.curr_symble','pm.bank_name','pm.acc_no','pm.check_no','pm.check_no','pm.discount','pm.net_payble','pm.paid_amount','pm.due','pm.ref_no','pm.created_date');
		if(getFromSession('project_type')!='Group Company'){
		$sql="pm.supplier = s.supplier_code AND pm.project_id = p.project_id AND pm.currency = c.currency_id AND pm.project_id = '".$project_id."' AND pm.due>0 ";
		}else{
		$sql="pm.supplier = s.supplier_code AND pm.project_id = p.project_id AND pm.currency = c.currency_id AND pm.due>0 ";
		}
		if($cr_account!=""){
			$sql.=" AND pm.supplier = '$cr_account' ";
		}							
		if($date_from!="" && $date_to ==""){
			$sql.=" AND pm.purchase_date >= '$date_from'";
		}elseif($date_from=="" && $date_to !=""){
			$sql.=" AND pm.purchase_date <= '$date_to'";
		}elseif($date_from!="" && $date_to !=""){
			$sql.=" AND pm.purchase_date BETWEEN '$date_from' AND '$date_to'";
		}
		$info['where']  =$sql;	
		$info['orderby'] 	= array("pm.purchase_date asc");
		//$info['debug']  	= true;
		$result         	= select($info);
		$data           	= array();     
	    $cnt = count($result);  	
        if($cnt) {
        	return $cnt;
        }else {
	  		return 0;
	    }    
   }      
	//================ End Due Payment List ===============
	//================ Due Received List ==========
	function getDueReceivedList($from,$to) { 
		$party_code = getRequest("party_code");
		$catagory   = getRequest("catagory");
		if($from == "" && $to == ""){$from=0; $to=500;}
		$date_from 		= formatDate(getRequest('date_from'));
		$date_to 		= formatDate(getRequest('date_to'));				
		$project_id     = getFromSession('project_id');  
		$info           = array(); // SALES_DETAILS_TBL   
		$info['table']  = SALES_MASTER_TBL.' pm,'.SUB_ACC_HEAD_TBL.' s,'.PROJECT_TBL.' p,'.CURRENCY_TBL.' c';	
		$info['fields'] = array('pm.voucher_no','pm.po_no','pm.wo_no','pm.project_id','p.project_name','p.location','s.sub_id','s.sub_head_name','s.head_details','pm.gate_pass','pm.track_no','pm.salse_type','pm.total_value',"DATE_FORMAT(pm.sales_date,'%d %b %y' ) as sales_date",'pm.mode_of_payment','c.curr_symble','pm.bank_name','pm.acc_no','pm.check_no','pm.check_no','pm.discount','pm.net_payble','pm.paid_amount','pm.due','pm.ref_no','pm.created_date',"DATE_FORMAT(pm.created_date,'%d %b %y' ) as date");
	    if(getFromSession('project_type')!='Group Company'){
			$sql="pm.customer = s.sub_id AND pm.project_id = p.project_id AND pm.currency = c.currency_id AND pm.project_id = '".$project_id."' AND pm.due>0";
		}else{
			$sql="pm.customer = s.sub_id AND pm.project_id = p.project_id AND pm.currency = c.currency_id AND pm.due>0";
		}
		if($party_code!=""){
		 $sql.=" AND pm.customer = '$party_code'";
		 }
		if($date_from!="" && $date_to ==""){
			$sql.=" AND pm.sales_date >= '$date_from'";
		}elseif($date_from=="" && $date_to !=""){
			$sql.=" AND pm.sales_date <= '$date_to'";
		}elseif($date_from!="" && $date_to !=""){
			$sql.=" AND pm.sales_date BETWEEN '$date_from' AND '$date_to'";
		}
		$info['where']  =$sql;		
		$info['orderby'] = array("pm.voucher_no asc LIMIT $from,$to");
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

   function getTotalDueReceivedList($from,$to) {  
		$date_from 			= formatDate(getRequest('date_from'));
		$date_to 			= formatDate(getRequest('date_to'));				
		$project_id     = getFromSession('project_id');  
		$info           = array();    
		$info['table']  = SALES_MASTER_TBL.' pm,'.SUB_ACC_HEAD_TBL.' s,'.PROJECT_TBL.' p,'.CURRENCY_TBL.' c';	
		$info['fields'] = array('pm.voucher_no','p.project_name','p.location','s.sub_head_name','s.head_details','pm.gate_pass','pm.track_no','pm.salse_type','pm.total_value',"DATE_FORMAT(pm.sales_date,'%d %b %y' ) as sales_date",'pm.mode_of_payment','c.curr_symble','pm.bank_name','pm.acc_no','pm.check_no','pm.check_no','pm.discount','pm.net_payble','pm.paid_amount','pm.due','pm.ref_no','pm.created_date',"DATE_FORMAT(pm.created_date,'%d %b %y' ) as date");
	    if(getFromSession('project_type')!='Group Company'){
			$sql="pm.customer = s.sub_id AND pm.project_id = p.project_id AND pm.currency = c.currency_id AND pm.project_id = '".$project_id."' AND pm.due>0";
		}else{
			$sql="pm.customer = s.sub_id AND pm.project_id = p.project_id AND pm.currency = c.currency_id AND pm.due>0";
		}
		if($date_from!="" && $date_to ==""){
			$sql.=" AND pm.sales_date >= '$date_from'";
		}elseif($date_from=="" && $date_to !=""){
			$sql.=" AND pm.sales_date <= '$date_to'";
		}elseif($date_from!="" && $date_to !=""){
			$sql.=" AND pm.sales_date BETWEEN '$date_from' AND '$date_to'";
		}
		$info['where']  =$sql;
		$info['orderby'] 	= array("pm.sales_date asc");
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
   function showEditor($msg = null) { 
      $ID = getRequest('id');
	 if ($ID) {
         $advArr = $this->getAccJournalInfo($ID);
         $advArr = parseThisValue($advArr);  
         $data   = array_merge(array(), $advArr); 
      }else{
         if(getRequest('submit')){
			   if(getRequest('cmd')=="addpv"){
			    mysql_query("START TRANSACTION;"); 
				$this->savePaymentDebitVouchar();	
			   }elseif(getRequest('cmd')=="addrv")
			   {
				$this->saveReceivedDebitVouchar();	
			   }
         }
      }	 
      require_once(CLASS_DIR.'/common.list.class.php');	
	$clistApp = new CommonList();   
	$data['headlist1']   	= $clistApp->getAccountHeadList("Current Assets","S130"); // Cash & Bank
	$data['headlist2']   	= $clistApp->getAccountHeadList("Current Assets","S128","C000105"); // Party
	$data['headlist3']   	= $clistApp->getAccountHeadList("Non Current Assets","","","S130","C000105"); 
	$data['headlist4']   	= $clistApp->getAccountHeadList("Current Assets","","","S130","C000105");
	$data['headlist5']   	= $clistApp->getAccountHeadList("Liabilities");		
	$data['headlist6']   	= $clistApp->getAccountHeadList("Capital");	
	$data['headlist7']   	= $clistApp->getAccountHeadList("Retained earnings");	
	$data['headlist8']   	= $clistApp->getAccountHeadList("Sales");	
	$data['headlist9']   	= $clistApp->getAccountHeadList("Incomes");	
	$data['headlist10']   	= $clistApp->getAccountHeadList("Direct Expenses");		
	$data['headlist11']   	= $clistApp->getAccountHeadList("Indirect Expenses");	
	$data['headlist12']   	= $clistApp->getAccountHeadList("Opening Balance");	
	$data['headlist13']   	= $clistApp->getAccountHeadList("Adjustments Balance");	
	$data['headlist14']   	= $clistApp->getAccountHeadList("Closing Balance");	
	$data['supplier_list']  = $clistApp->getSupplierList();	
        $data['currency_list']   	 		= $this->getCurrencyList();
        $data['message'] = $msg;
        $data['cmd']     = getRequest('cmd');
	require_once(JOURNAL_ADD_EDIT_SKIN);      
        return true;
   }
	//============== Save Vouchar ===========
	//==================== saveDebitVouchar ====================
 	function savePaymentDebitVouchar()
 	{     
 	   	  $mode_of_payment = getRequest('mode_of_payment');
		  $requestdata = array();
		  $requestdata = getUserDataSet(DEVIT_VOUCHAR_TBL);	
		  if($mode_of_payment =="Check"){
			$requestdata['bank_name'] 			= getRequest('bank_name');
			$requestdata['acc_no'] 				= getRequest('acc_no');
			$requestdata['check_no'] 			= getRequest('check_no');
			$requestdata['check_issue_date'] 	= formatDate(getRequest('check_issue_date'));
		  }else{
			$requestdata['bank_name'] = "";
			$requestdata['acc_no'] = "";
			$requestdata['check_no'] = "";
			$requestdata['check_issue_date'] = "";
		  }
		  if(getRequest('pref_no')!="" && getRequest('dir')=="purchase"){
			$requestdata['head_type']     		= "Supplier";
		  }else{			    
		  	$requestdata['head_type']     		= "Acc";   
		  }
		  $requestdata['transaction_type']  = "Payment";     
		  $requestdata['account_head']      = getRequest('account_head'); 
		  $requestdata['debit']        		= getRequest('debit');    
		  $requestdata['credit']        	= 0; 
     	  
		  $requestdata['project_id']        = getFromSession('project_id');    
		  $requestdata['created_by']        = getFromSession('userid'); 
		  $requestdata['created_date']      = formatDate(getRequest('created_date'));
		  //$requestdata['created_date']      = date('Y-m-d h:i:s');	
		  $voucher_no = $this->createVoucharID();	
		  if($voucher_no != -1){
			$requestdata['voucher_no']   	= $voucher_no;
		  }else{
			$msg = "ID overflow !!!";
			header("location:index.php?app=user_home&msg=$msg");
			exit;
		  }
		  $info        		=  array();
		  $info['table']	= DEVIT_VOUCHAR_TBL;
		  $info['data'] 	= $requestdata;     
		  //$info['debug']  	=  true;
		  $res = insert($info);
		  if($res['affected_rows']) {
			$this->savePaymentCreditVouchar($voucher_no);
		  }else {	
			header("location:index.php?app=journal&cmd=add");	
		  }  
    }//EOFn  

    function savePaymentCreditVouchar($voucher_no)
 	{     
 	   	  $mode_of_payment = getRequest('mode_of_payment');		  
		  $requestdata = array();	
		  $requestdata = getUserDataSet(CREDIT_VOUCHAR_TBL);	
		  if($mode_of_payment =="Check"){
			$requestdata['bank_name'] 			= getRequest('bank_name');
			$requestdata['acc_no'] 				= getRequest('acc_no');
			$requestdata['check_no'] 			= getRequest('check_no');
			$requestdata['check_issue_date'] 	= formatDate(getRequest('check_issue_date'));
    
		  	$requestdata['account_head']     	= getRequest('acc_no'); 
		  	$requestdata['debit']        		= 0; 
		  	$requestdata['credit']        		= getRequest('debit');     
		  	$requestdata['head_type']     		= "Check";   
		  }else{
			$requestdata['bank_name'] = "";
			$requestdata['acc_no'] = "";
			$requestdata['check_no'] = "";
			$requestdata['check_issue_date'] = "";

			$requestdata['account_head']     	= $this->getCashId(getFromSession('project_id')); 
		  	$requestdata['debit']        		= 0; 
		  	$requestdata['credit']        		= getRequest('debit');     
		  	$requestdata['head_type']     		= "Acc";   
		  }
		  $requestdata['transaction_type']  = "Payment"; 
		  $requestdata['project_id']        = getFromSession('project_id');    
		  $requestdata['created_by']        = getFromSession('userid'); 	 			 
		  $requestdata['created_date']      = formatDate(getRequest('created_date'));
		  $requestdata['voucher_no']   		= $voucher_no;		 
		  $info        		=  array();
		  $info['table']	= CREDIT_VOUCHAR_TBL;
		  $info['data'] 	= $requestdata;     
		  //$info['debug']  	=  true;
		  $res = insert($info);		  
		  $date =  formatDate(getRequest('created_date'));
	
		  if($res['affected_rows']) {
			 $DrAmount = getRequest('debit');
			 if($mode_of_payment=="Cash"){ 
				//======= Party Dr ======	
				$PartyAcc_head = getRequest('account_head'); 
				$totalPartyCR  = $this->getTotalCreditAmount($PartyAcc_head,getFromSession('project_id'));
				$totalPartyDR  = $this->getTotalDebitAmount($PartyAcc_head,getFromSession('project_id'));					 
				$PartyBalance  = (($totalPartyDR+$DrAmount)-$totalPartyCR);					 
				$this->saveAccountJournal($voucher_no,$PartyAcc_head,"Acc",getFromSession('project_id'),getRequest('description'),$DrAmount,0,$PartyBalance,1,$date);	
				//============== Cash Cr ===============
				 $acc_head = $this->getCashId(getFromSession('project_id'));
				 $totalCR  = $this->getTotalCreditAmount($acc_head,getFromSession('project_id'));
				 $totalDR  = $this->getTotalDebitAmount($acc_head,getFromSession('project_id'));					 
				 $balance  = ($totalDR-($totalCR+$DrAmount));					 
				 $this->saveAccountJournal($voucher_no,$acc_head,"Acc",getFromSession('project_id'),getRequest('description'),0,$DrAmount,$balance,1,$date);				 
			 }elseif($mode_of_payment=="Check"){
				//====== save payable_check ======
				if(getRequest('dir')=="PAC"){ // PAC is Pending Administrative Cost
				$this->savePayableCheck($voucher_no,"Payment",getRequest('debit'),1); // 1 is Yes
				}else{
				$this->savePayableCheck($voucher_no,"Payment",getRequest('debit'));
				}
			 }
			if(getRequest('dir')=="PAC"){				
				//========= Capital Cr ==========	
				$CrAmount		 = getRequest('debit'); $project_id = getFromSession('project_id');
				$description = getRequest('description');
				$CptAcc_head = $this->getCapitalId(getFromSession('project_id'));
				$totalCptCR  = $this->getTotalCreditAmount($CptAcc_head,getFromSession('project_id'));
				$totalCptDR  = $this->getTotalDebitAmount($CptAcc_head,getFromSession('project_id'));					 
				$CptBalance  = ($totalCptDR-($totalCptCR+$CrAmount));					 
				$this->saveAccountJournal($voucher_no,$CptAcc_head,"Acc",$project_id,$description,0,$CrAmount,$CptBalance,1,$date);	
				if(getRequest('cref_no')!=""){
					$cref_no = getRequest('cref_no');
					require_once(CLASS_DIR.'/sales.commission.class.php');	
					$scApp = new SalesCommission();
					$scApp->updateCommissionMaster($cref_no,$voucher_no,$CrAmount);					
				}elseif(getRequest('esref_no')!=""){
					$esref_no = getRequest('esref_no'); $detail_id = getRequest('detail_id');
					require_once(CLASS_DIR.'/salary.sheet.class.php');	
					$esApp = new SalarySheet();
					$esApp->updateSalaryTbl($voucher_no,$esref_no,$detail_id,$CrAmount);
				}

			}else{ // end PAC
				require_once(CLASS_DIR.'/advanced_payment.class.php');	
				$payApp = new AdvancedPayment();
				$head_type 		= getHeadType(getRequest('account_head'));
				$account_head 	= getRequest('account_head');
				$DrAmount 		= getRequest('debit');
				if($head_type=="Supplier"){
				$payApp->adjustSupplierPayble($voucher_no,$account_head,$DrAmount,$date);
				}elseif($head_type=="Customer"){		
				$payApp->adjustCustomerPayble($voucher_no,$account_head,$DrAmount,$date);
				}
			} 
			mysql_query("COMMIT;");	
			header("location:index.php?app=journal&cmd=vouchar_print&voucher_no=".$voucher_no);

		}else { // else of affects rows				
			header("location:index.php?app=journal&cmd=add");		   
		} // end if affect rows	 

    }//EOFn  

   //============= Save Received Debit Vouchar =====
 	function saveReceivedDebitVouchar()
 	{     
 	   	  $mode_of_payment = getRequest('mode_of_payment');		  
		  $requestdata = array();	
		  $requestdata = getUserDataSet(DEVIT_VOUCHAR_TBL);	
		  if($mode_of_payment =="Check"){
			$requestdata['bank_name'] 			= getRequest('bank_name');
			$requestdata['acc_no'] 				= getRequest('acc_no');
			$requestdata['check_no'] 			= getRequest('check_no');
			$requestdata['check_issue_date'] 	= formatDate(getRequest('check_issue_date'));    
		  	$requestdata['account_head']     	= getRequest('acc_no'); 
		  	$requestdata['debit']        		= getRequest('credit');   
		  	$requestdata['credit']        		= 0;     
		  	$requestdata['head_type']     		= "Check";   
		  }else{
			$requestdata['bank_name'] = "";
			$requestdata['acc_no'] = "";
			$requestdata['check_no'] = "";
			$requestdata['check_issue_date'] = "";

			$requestdata['account_head']     	= $this->getCashId(getFromSession('project_id'));  
		  	$requestdata['debit']        		= getRequest('credit');   
		  	$requestdata['credit']        		= 0;     
		  	$requestdata['head_type']     		= "Acc";   
		  }
		  $requestdata['transaction_type']  = "Received"; 
		  $requestdata['project_id']        = getFromSession('project_id');    
		  $requestdata['created_by']        = getFromSession('userid');	
		  $requestdata['created_date']      = formatDate(getRequest('created_date'));	
		  $voucher_no = $this->createVoucharID();	
		 if($voucher_no != -1){
			$requestdata['voucher_no']   	= $voucher_no;
		  }else{
			$msg = "ID overflow !!!";
			header("location:index.php?app=user_home&msg=$msg");
			exit;
		  }
		  $info        		=  array();
		  $info['table']	= DEVIT_VOUCHAR_TBL;
		  $info['data'] 	= $requestdata;     
		  //$info['debug']  	=  true;
		  $res = insert($info);
		  if($res['affected_rows']) {
			$this->saveReceivedCreditVouchar($voucher_no);
		  }else {	
			//header("location:index.php?app=journal&cmd=add");	 will be rollback
		  }  

    }//EOFn  

    function saveReceivedCreditVouchar($voucher_no)
 	{     
 	   	  $mode_of_payment = getRequest('mode_of_payment');		  
		  $requestdata = array();	
		  $requestdata = getUserDataSet(CREDIT_VOUCHAR_TBL);	
		  if($mode_of_payment =="Check"){
			$requestdata['bank_name'] 			= getRequest('bank_name');
			$requestdata['acc_no'] 				= getRequest('acc_no');
			$requestdata['check_no'] 			= getRequest('check_no');
			$requestdata['check_issue_date'] 	= formatDate(getRequest('check_issue_date'));
		  }else{
			$requestdata['bank_name'] = "";
			$requestdata['acc_no'] = "";
			$requestdata['check_no'] = "";
			$requestdata['check_issue_date'] = "";
		  }
		  $requestdata['transaction_type']  = "Received";     
		  $requestdata['account_head']      = getRequest('account_head'); 
		  $requestdata['debit']        		= 0;    
		  $requestdata['credit']        	= getRequest('credit');        
		  $requestdata['head_type']     	= "Acc"; 
		  $requestdata['project_id']        = getFromSession('project_id');    
		  $requestdata['created_by']        = getFromSession('userid'); 
		  $requestdata['created_date']      = formatDate(getRequest('created_date'));
		  $requestdata['voucher_no']   		= $voucher_no;		 
		  $info        		=  array();
		  $info['table']	= CREDIT_VOUCHAR_TBL;
		  $info['data'] 	= $requestdata;     
		  $info['debug']  	=  true;
		  $res = insert($info);
		  $date =  formatDate(getRequest('created_date'));	
		  if($res['affected_rows']) {
			 $DrAmount = getRequest('credit');
			 if($mode_of_payment=="Cash"){ 
				//============== Cash Dr ===============
				 $acc_head = $this->getCashId(getFromSession('project_id'));
				 $totalCR  = $this->getTotalCreditAmount($acc_head,getFromSession('project_id'));
				 $totalDR  = $this->getTotalDebitAmount($acc_head,getFromSession('project_id'));					 
				 $balance  = (($totalDR+$DrAmount)-$totalCR);					 
				 $this->saveAccountJournal($voucher_no,$acc_head,"Acc",getFromSession('project_id'),getRequest('description'),$DrAmount,0,$balance,1,$date);
				//======= Party Cr ======	
				$PartyAcc_head = getRequest('account_head'); 
				$totalPartyCR  = $this->getTotalCreditAmount($PartyAcc_head,getFromSession('project_id'));
				$totalPartyDR  = $this->getTotalDebitAmount($PartyAcc_head,getFromSession('project_id'));					 
				$PartyBalance  = ($totalPartyDR-($totalPartyCR+$DrAmount));					 
				$this->saveAccountJournal($voucher_no,$PartyAcc_head,"Acc",getFromSession('project_id'),getRequest('description'),0,$DrAmount,$PartyBalance,1,$date);	
				// header("location:index.php?app=purchase&cmd=print_vouchar&voucher_no=".$voucher_no);
			 }elseif($mode_of_payment=="Check"){
				//====== save payable_check ======
				$this->savePayableCheck($voucher_no,"Received",getRequest('credit'));				
			}
			if(getRequest('rref_no')!="" && getRequest('dir')=="sales"){
				//========= Receivable Cr ==========	
				 $Receivable	= getRequest('credit');
				 $rblAcc_head = $this->getRecievableId(getFromSession('project_id'));
				 $totalRblCR  = $this->getTotalCreditAmount($rblAcc_head,getFromSession('project_id'));
				 $totalRblDR  = $this->getTotalDebitAmount($rblAcc_head,getFromSession('project_id'));					 
				 $rblBalance  = ($totalRblDR-($totalRblCR+$Receivable));					 
				 $this->saveAccountJournal($voucher_no,$rblAcc_head,"Acc",getFromSession('project_id'),getRequest('description'),0,$Receivable,$rblBalance,1,$date);			
				 $rsql= "SELECT net_payble, paid_amount ,due FROM ".SALES_MASTER_TBL." WHERE voucher_no  = '".getRequest('rref_no')."' AND due >0";
				 $rres = mysql_query($rsql);
				 if(mysql_num_rows($rres)>0){
					$srow = mysql_fetch_object($rres);						
					$due = $srow->due;
					$net_payble = $srow->net_payble;
					$paidAmount = $srow->paid_amount;
					$total_due = $net_payble - ($paidAmount + $Receivable);
					$total_paid = ($paidAmount + $Receivable);
					$pusql= "UPDATE  ".SALES_MASTER_TBL." SET paid_amount ='".$total_paid."', due ='".$total_due."' WHERE voucher_no  = '".getRequest('rref_no')."' AND due >0";
					$pures = mysql_query($pusql);
				 }
		  	}
			if(getRequest('vref_no')!="" && getRequest('dir')=="general"){				
			 	$DrAmount = getRequest('credit');
				$rsql= "SELECT debit,paid_amount ,due FROM ".DEVIT_VOUCHAR_TBL." WHERE voucher_no  = '".getRequest('vref_no')."' AND due >0 AND status=0";
				$rres = mysql_query($rsql);
				$srow = mysql_fetch_object($rres);	
				$total_paid = $srow->paid_amount+$DrAmount;
				$total_due  = $srow->due-$DrAmount;
				if($total_paid==$srow->debit){
				$pusql= "UPDATE  ".DEVIT_VOUCHAR_TBL." SET  paid_amount = '".$total_paid."', due='".$total_due."', `status` = 1 WHERE voucher_no  = '".getRequest('vref_no')."'";
				}elseif($total_paid<$srow->debit){
				$pusql= "UPDATE  ".DEVIT_VOUCHAR_TBL." SET  paid_amount = '".$total_paid."', due='".$total_due."', `status` = 0 WHERE voucher_no  = '".getRequest('vref_no')."'";
				}
				$pures = mysql_query($pusql);					
			}
			header("location:index.php?app=journal&cmd=vouchar_print&voucher_no=".$voucher_no);
		  }else {	// else of affect rows
			header("location:index.php?app=journal&cmd=add");	
		  }	 

    }//EOFn  
//============== End Save Vouchar =======

function savePayableCheck($voucher_no,$transaction_type,$paid_amount,$isAdvCost=NULL){
	 $requestdata = array();

	  $requestdata = getUserDataSet(PAYABLE_CHECK_TBL);	
	  $requestdata['check_no'] 			= getRequest('check_no');
	  $requestdata['check_issue_date']  = formatDate(getRequest('check_issue_date')); 	 			 
	  $requestdata['created_date']      = formatDate(getRequest('created_date'));
	  $requestdata['acc_head'] 			= getRequest('account_head');
	  $requestdata['adv_cost'] 			= $isAdvCost;  
	  $requestdata['head_type'] 		= "Check"; 
	  $requestdata['voucher_no']        = $voucher_no;  
	  $requestdata['paid_amount']  		= $paid_amount;   
	  $requestdata['transaction_type']  = $transaction_type;   
	  $requestdata['project_id']        = getFromSession('project_id');    
	  $requestdata['created_by']        = getFromSession('userid');

	  $info        		=  array();
	  $info['table']	= PAYABLE_CHECK_TBL;
	  $info['data'] 	= $requestdata;     
	  //$info['debug']  	=  true;
	  $res = insert($info);
		
}

function saveAccountJournal($voucher_no,$sub_id,$head_type,$project_id,$description,$DR=NULL,$CR=NULL,$balance,$status=NULL,$purchare_date=NULL){		
		$sql = "INSERT INTO ".ACCOUNT_JOURNAL_TBL." (voucher_no,created_date,sub_id,head_type,project_id,description,dr,cr,balance,status) VALUES('".$voucher_no."','".$purchare_date."','".$sub_id."','".$head_type."','".$project_id."','".$description."','".$DR."','".$CR."','".$balance."','".$status."')";
		mysql_query($sql);
}

   function getAccJournalDetails($id)
   {
   	   $data           =  array();                  
       $info           =  array();     
       $info['table']  =  ACCOUNT_JOURNAL_TBL;
	   $info['fields'] = array('voucher_no','sub_id','project_id','description','dr','cr','balance',"DATE_FORMAT(transaction_date,'%d %b %y' ) as transaction_date"); 
       $info['where']  =  "voucher_no='".$id."' ";
       $info['debug']  =  false;                     
       $res            =	select($info);
       if(count($res))
       {
          foreach($res as $i=>$v)
          {
             $data[$i] = $v;             
          }
       }
       return $data[0];
   }
  //====== final ========
   function getDebitVoucharDetails($voucher_no)
   {
	   $project_id = getFromSession('project_id');    
   	   $data           =  array();                  
       $info           =  array();     
       $info['table']  =  DEVIT_VOUCHAR_TBL;
       $info['where']  =  "voucher_no='".$voucher_no."' AND project_id = '$project_id'";
       $info['debug']  =  false;                     
       $res            =	select($info);
       if(count($res)){
          foreach($res as $i=>$v){
             $data[$i] = $v;          
          }
       }
       return $data[0];
   }
    function createVoucharID(){
      $info = array();
      $info['table'] = DEVIT_VOUCHAR_TBL;
      $info['fields'] = array('max(voucher_no) as maxvoucher');      
      $res = select($info);      
      $maxvoucherId = 'D0000000';      
      if(count($res)){
         foreach($res as $v){
         	 if($v->maxvoucher){
             $maxvoucherId = $v->maxvoucher;
             }
             break;   	
         }
      }
      $maxvoucherId = generateID("D",$maxvoucherId,8);
      return $maxvoucherId;
   }  
   
   function getSubAccHeadList(){
      $info            = array();
 	  $project_id 	   = getFromSession('project_id');
      $info['table']   = SUB_ACC_HEAD_TBL;
      //$info['fields']  = array('sub_id', 'sub_head_name'); 	
      $info['where']   =  "project_id = '$project_id'"; 
	  $info['orderby'] = array("sub_head_name ASC");
      $info['debug']   = false;
      $result          = select($info);
      //dBug($result);
      $data            = array();
      if(count($result)){
         foreach($result as $i=>$v){
            $data[$i] = $v;             
         }
      }
      return $data;
   }

   function getCurrencyList(){
      $info            = array();
      $info['table']   = CURRENCY_TBL;
      //$info['fields'] = array('currency_id', 'name'); 
	  $info['orderby'] = array("currency_name ASC");
      $info['debug']   = false;
      $result          = select($info);
      //dBug($result);
      $data            = array();
      if(count($result)){
         foreach($result as $i=>$v){
           $data[$i] = $v;             
         }
      }
              
      return $data;
   }
   function getCashId($project_id){
		$sql = "SELECT sub_id FROM ".SUB_ACC_HEAD_TBL." WHERE head_type = 'Cash' AND project_id = '$project_id'";
		$row = mysql_fetch_object(mysql_query($sql));
		return $sub_id = $row->sub_id;
	}
	function getCapitalId($project_id){
		$sql = "SELECT sub_id FROM ".SUB_ACC_HEAD_TBL." WHERE head_type = 'Capital' AND project_id = '$project_id'";
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

//=============End =============
//============= Start Payable check ============
	function showEditor4PayableCheck($msg = null) {        
	  $data                				= array();
	  $data['cmd']         				= getRequest('cmd');
	  $data['record_list'] 				= $this->getPayableCheckList(getRequest('from'),getRequest('to'));
	  $data['totalrecord']				= $this->getTotalPayableCheckList(getRequest('from'),getRequest('to'));	
	   require_once(PAYABLE_CHECK_LIST_SKIN); 
	   return $data[0];
   }
   	function withdrawAmount($voucher_no, $acc_no,$amount,$isAdvCost=NULL){
		//========= Bank Account Cr ==========	
		 $Receivable	= $amount;
		 $rblAcc_head = $acc_no;
		 $totalRblCR  = $this->getTotalCreditAmount($rblAcc_head,getFromSession('project_id'));
		 $totalRblDR  = $this->getTotalDebitAmount($rblAcc_head,getFromSession('project_id'));					 
		 $rblBalance  = ($totalRblDR-($totalRblCR+$Receivable));					 
		 $this->saveAccountJournal($voucher_no,$rblAcc_head,"Check",getFromSession('project_id'),getRequest('description'),0,$Receivable,$rblBalance,1);	
		 $sql = "UPDATE ".PAYABLE_CHECK_TBL." SET status = 1 WHERE voucher_no = '$voucher_no' AND status = 0";
		 $res = mysql_query($sql);	
		
		 header("location:index.php?app=journal&cmd=pbl_check");
	}

	function depositAmount($voucher_no){
		$date = date("Y-m-d");
		$rsql= "SELECT customer, acc_head, net_payble, paid_amount ,due FROM ".PAYABLE_CHECK_TBL." WHERE voucher_no  = '".$voucher_no."' AND status=0 AND transaction_type='Received'";
		$rres = mysql_query($rsql);
		if(mysql_num_rows($rres)>0){
			$srow = mysql_fetch_object($rres);						
			$due = $srow->due;
			$net_payble = $srow->net_payble;
			$paidAmount = $srow->paid_amount;
			$acc_no		= getRequest('acc_no');
			$party_code	= $srow->customer;
			//============= journal ==========
			if($paidAmount>0){				 
				//========= Party Cr ==========	
				 $Receivable	= $paidAmount;				
				 $totalPartyCR  = $this->getTotalCreditAmount($party_code,getFromSession('project_id'));
				 $totalPartyDR  = $this->getTotalDebitAmount($party_code,getFromSession('project_id'));					 
				 $PartyBalance  = ($totalPartyDR-($totalPartyCR+$Receivable));					 
				 $this->saveAccountJournal($voucher_no,$party_code,"Acc",getFromSession('project_id'),getRequest('description'),0,$Receivable,$PartyBalance,1,$date);
				 //============== Bank Account Dr ===============
				 $acc_head = $acc_no;
				 $totalCR  = $this->getTotalCreditAmount($acc_head,getFromSession('project_id'));
				 $totalDR  = $this->getTotalDebitAmount($acc_head,getFromSession('project_id'));					 
				 $balance  = (($totalDR+$paidAmount)-$totalCR);					 
				 $this->saveAccountJournal($voucher_no,$acc_head,"Acc",getFromSession('project_id'),getRequest('description'),$paidAmount,0,$balance,1,$date);	
			}else{						
				 //========= Party Cr ==========	
				 $Receivable	= $paidAmount;				 
				 $totalPartyCR  = $this->getTotalCreditAmount($party_code,getFromSession('project_id'));
				 $totalPartyDR  = $this->getTotalDebitAmount($party_code,getFromSession('project_id'));					 
				 $PartyBalance  = ($totalPartyDR-($totalPartyCR+$Receivable));					 
				 $this->saveAccountJournal($voucher_no,$party_code,"Acc",getFromSession('project_id'),getRequest('description'),0,$Receivable,$PartyBalance,1,$date);
				 		
				//============== Bank Account Dr ===============
				 $acc_head = $acc_no;
				 $totalCR  = $this->getTotalCreditAmount($acc_head,getFromSession('project_id'));
				 $totalDR  = $this->getTotalDebitAmount($acc_head,getFromSession('project_id'));					 
				 $balance  = (($totalDR+$paidAmount)-$totalCR);					 
				 $this->saveAccountJournal($voucher_no,$acc_head,"Acc",getFromSession('project_id'),getRequest('description'),$paidAmount,0,$balance,1,$date);	
														 
			}
			
			$pusql= "UPDATE  ".PAYABLE_CHECK_TBL." SET status = 1 WHERE voucher_no  = '".$voucher_no."' AND status=0";
			$pures = mysql_query($pusql);			
		    header("location:index.php?app=journal&cmd=rbl_check");
		}
	}    	
	function getPayableCheckList($from,$to) { 

		if($from == "" && $to == ""){$from=0; $to=50;}
		$date_from 			= formatDate(getRequest('date_from'));
		$date_to 			= formatDate(getRequest('date_to'));				
		$project_id     = getFromSession('project_id');  
		$info           = array();    
		$info['table']  = PAYABLE_CHECK_TBL.' p,'.CURRENCY_TBL.' c';	
		$info['fields'] = array('p.voucher_no','c.curr_symble','p.net_payble','p.paid_amount','p.due','p.adv_cost','p.bank_name','p.acc_no','p.check_no',"DATE_FORMAT(p.check_issue_date,'%d %b %y' ) as check_issue_date","DATE_FORMAT(p.created_date,'%d %b %y' ) as date");
		
		$sql="p.currency = c.currency_id AND p.project_id = '".$project_id."' AND p.status =0 AND p.transaction_type = 'Payment'";
	
		if($date_from!="" && $date_to ==""){
			$sql.=" AND p.created_date >= '$date_from'";
		}elseif($date_from=="" && $date_to !=""){
			$sql.=" AND p.created_date <= '$date_to'";
		}elseif($date_from!="" && $date_to !=""){
			$sql.=" AND p.created_date BETWEEN '$date_from' AND '$date_to'";
		}
		$info['where']  =$sql;		
		$info['orderby'] = array("p.voucher_no asc LIMIT $from,$to");
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

   function getTotalPayableCheckList($from,$to) {  
		$date_from 			= formatDate(getRequest('date_from'));
		$date_to 			= formatDate(getRequest('date_to'));				
		$project_id     = getFromSession('project_id');  
		$info           = array();      
		$info['table']  = PAYABLE_CHECK_TBL.' p,'.CURRENCY_TBL.' c';	
		$info['fields'] = array('p.voucher_no');
		$sql="p.currency = c.currency_id AND p.project_id = '".$project_id."' AND p.status =0 AND p.transaction_type = 'Payment'";
		if($date_from!="" && $date_to ==""){
			$sql.=" AND p.created_date >= '$date_from'";
		}elseif($date_from=="" && $date_to !=""){
			$sql.=" AND p.created_date <= '$date_to'";
		}elseif($date_from!="" && $date_to !=""){
			$sql.=" AND p.created_date BETWEEN '$date_from' AND '$date_to'";
		}

		$info['where']  =$sql;	
		$info['orderby'] = array("p.voucher_no asc LIMIT $from,$to");
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
//============= End Payable check ============
//============= Start Receivable check ============
	function showEditor4ReceivableCheck($msg = null) {        
	  $data                				= array();
	  $data['cmd']         				= getRequest('cmd');
	  $data['record_list'] 				= $this->getReceivableCheckList(getRequest('from'),getRequest('to'));
	  $data['totalrecord']				= $this->getTotalReceivableCheckList(getRequest('from'),getRequest('to'));	
		
	   require_once(RECEIVABLE_CHECK_LIST_SKIN); 

	   return $data[0];

   }
   function showdDepositEditor($voucher_no) {        
	  require_once(CLASS_DIR.'/common.list.class.php');	
	  $clistApp = new CommonList(); 
	  $data                		= array();
	  $data['cmd']         		= getRequest('cmd'); 
	  $data['bank_list'] 		= $this->getBankList();	
	  $data['headlist1']   		= $clistApp->getAccountHeadList("Cash");
	  $data['voucher_no']       = $voucher_no;
		
	   require_once(BANK_DEPOSIT_SKIN); 

	   return $data[0];

   }
    	
	function getReceivableCheckList($from,$to) { 

		if($from == "" && $to == ""){$from=0; $to=50;}
		$date_from 			= formatDate(getRequest('date_from'));
		$date_to 			= formatDate(getRequest('date_to'));				
		$project_id     = getFromSession('project_id');  
		$info           = array();    
		$info['table']  = PAYABLE_CHECK_TBL.' p,'.CURRENCY_TBL.' c';	
		$info['fields'] = array('p.voucher_no','c.curr_symble','p.net_payble','p.paid_amount','p.due','p.bank_name','p.acc_no','p.check_no',"DATE_FORMAT(p.check_issue_date,'%d %b %y' ) as check_issue_date","DATE_FORMAT(p.created_date,'%d %b %y' ) as date");
		
		$sql="p.currency = c.currency_id AND p.project_id = '".$project_id."' AND p.status =0 AND p.transaction_type = 'Received'";
	
		if($date_from!="" && $date_to ==""){
			$sql.=" AND p.created_date >= '$date_from'";
		}elseif($date_from=="" && $date_to !=""){
			$sql.=" AND p.created_date <= '$date_to'";
		}elseif($date_from!="" && $date_to !=""){
			$sql.=" AND p.created_date BETWEEN '$date_from' AND '$date_to'";
		}
		$info['where']  =$sql;		
		$info['orderby'] = array("p.voucher_no asc LIMIT $from,$to");
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
   function getTotalReceivableCheckList($from,$to) {  
		$date_from 			= formatDate(getRequest('date_from'));
		$date_to 			= formatDate(getRequest('date_to'));				
		$project_id     = getFromSession('project_id');  
		$info           = array();      
		$info['table']  = PAYABLE_CHECK_TBL.' p,'.CURRENCY_TBL.' c';	
		$info['fields'] = array('p.voucher_no');
		$sql="p.currency = c.currency_id AND p.project_id = '".$project_id."' AND p.status =0 AND p.transaction_type = 'Received'";
		if($date_from!="" && $date_to ==""){
			$sql.=" AND p.created_date >= '$date_from'";
		}elseif($date_from=="" && $date_to !=""){
			$sql.=" AND p.created_date <= '$date_to'";
		}elseif($date_from!="" && $date_to !=""){
			$sql.=" AND p.created_date BETWEEN '$date_from' AND '$date_to'";
		}

		$info['where']  =$sql;	

		$info['orderby'] = array("p.voucher_no asc LIMIT $from,$to");

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
//============= End Receivable check ============
} // End class
?>
