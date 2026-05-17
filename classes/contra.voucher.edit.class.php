<?php
class ContraVoucherEdit
{
   
   function run()
   {         
      $cmd = getRequest('cmd');
	  $u_t_id = getFromSession('u_type_id');

      if( ($u_t_id == 101)) //1 = admin 2 = Sales man
      {
      	switch ($cmd)
      	{
      	   case 'edit_vouchar'          : $screen = $this->showEditEditor($msg); break;
	   case 'edit_pending_vouchar'  : $this->editPendingVoucher(); break;
	   case 'delete_pending_vouchar'  : $this->deletePendingVoucher(); break;
	   case 'save_pending_vouchar'  : $this->savePendingVoucher(); break;
	   case 'save_vouchar'		: $this->saveVoucher(); break;
	   case 'delete'		: $this->deleteContraVoucher(); break;

      	}

      }elseif(($u_t_id == 102) || ($u_t_id == 104)) //1 = admin 2 = Sales man
      {
      	switch ($cmd)
      	{
      	   case 'edit_vouchar'          : $screen = $this->showEditEditor($msg); break;
	   case 'edit_pending_vouchar'  : $this->editPendingVoucher(); break;
	   case 'delete_pending_vouchar'  : $this->deletePendingVoucher(); break;
	   case 'save_pending_vouchar'  : $this->savePendingVoucher(); break;
	   case 'save_vouchar'		: $this->saveVoucher(); break;

      	}

      }else {
      	header("location:index.php?app=user_home&msg=You are not authorised !!!");
      }     	          
      return true;
   }  

   function showList($msg = null) {  
	  $data                			= array();
	  $data['cmd']         			= getRequest('cmd');
 	  $from         			= getRequest('from');
	  $to         				= getRequest('to');
	  $data['voucher_list']			= $this->getContraVoucherList($from,$to);
	  $data['totalrecord']			= $this->getTotalContraVoucherList(); 
	  require_once(CONTRA_VOUCHER_SKIN_LIST); 
	  return $data[0];

   }
   // =====Start Delete function===== 
   function deleteContraVoucher(){	
	$voucher_no 	= $_REQUEST['voucher_no']; $contra_id 	= $_REQUEST['contra_id']; $project_id = getFromSession('project_id');
	if($voucher_no!="" && $contra_id!=""){
	mysql_query("START TRANSACTION;");					
	$this->deleteAdjustAmount($contra_id,$voucher_no,$project_id);
	$this->deleteAllRecords($contra_id,$voucher_no);
	$created_by = getFromSession('userid');
	$msql = "SELECT * FROM ".CONTRA_MASTER_TBL." WHERE contra_id='".$contra_id."' AND voucher_no='".$voucher_no."' AND project_id='".$project_id."'";
	$mrow = mysql_fetch_object(mysql_query($msql));
	$befoure_amount = $mrow->dr_amount;
	
	SaveActivityLog("Contra Voucher",$voucher_no,"Delete",$created_by,$befoure_amount,0);
	mysql_query("COMMIT;");
	header("location:index.php?app=voucher.edit&cmd=list");	
	}else{		
	header("location:index.php?app=voucher.edit&cmd=list");	
	}
   }
   function getDelDrAccount($cid,$voucher_no,$project_id){				
		$cdSql="SELECT * FROM ".CONTRA_DETAILS_TBL." WHERE contra_id='".$cid."' AND voucher_no='".$voucher_no."' AND project_id='".$project_id."'
		AND headtypes='Dr' LIMIT 0 , 1";
		$cdres = mysql_query($cdSql);
		$cdrow = mysql_fetch_object($cdres);
		return  $cdrow->cr_account;
    }
    function getVoucherType($cid,$voucher_no,$project_id){
	$getcmSql= "SELECT * FROM ".CONTRA_MASTER_TBL." WHERE contra_id='".$cid."' AND voucher_no='".$voucher_no."' AND project_id='".$project_id."'";
	$cmres  = mysql_query($getcmSql);
	$cmrow = mysql_fetch_object($cmres);
	return $cmrow->vouchar_type;	
    }
    function deleteAdjustAmount($cid,$voucher_no,$project_id){
		$dr_account = $this->getDelDrAccount($cid,$voucher_no,$project_id);
		$vouchartype = $this->getVoucherType($cid,$voucher_no,$project_id);
		//if($vouchartype!="Payable Vouchar" && $vouchartype!="Recievable Vouchar"){ 
		$HeadType = getHeadType($dr_account);			
		$cmSql= "SELECT * FROM ".CONTRA_DETAILS_TBL." WHERE contra_id='".$cid."' AND voucher_no='".$voucher_no."' 
		AND project_id='".$project_id."'";
		$cmres  = mysql_query($cmSql);
		$numrow = mysql_num_rows($cmres);
		if($numrow>0){
		  while($cmrow = mysql_fetch_object($cmres)){
			if($cmrow->headtypes=="Cr"){
				$craccount 		= $cmrow->cr_account;
				if($HeadType=="Cash" || $HeadType=="Bank"){
					$head_type  = getHeadType($craccount);
					if($head_type=="Customer"){ 
					$this->rollbackCustomerReceibavle($voucher_no);
					}elseif($head_type=="Supplier"){
					$this->rollbackSupplierReceibavle($voucher_no);
					}	
				}
			}elseif($cmrow->headtypes=="Dr"){
				$draccount 		= $cmrow->cr_account;
				if($HeadType!="Cash" || $HeadType!="Bank"){
					$head_type 		= getHeadType($draccount);
					if($head_type=="Supplier"){ 
					$this->rollbackSupplierPayble($voucher_no);
					}elseif($head_type=="Customer"){
					$this->rollbackCustomerPayble($voucher_no);
					}	
				}	
			}
				
		  }//end while
		}//end if
	//}// end Vouchertype
  }
  function deleteAllRecords($cid,$voucher_no){
	$project_id = getFromSession('project_id');
	$Jsql="DELETE FROM ".ACCOUNT_JOURNAL_TBL." WHERE voucher_no='".$voucher_no."' AND project_id='".$project_id."'";
	mysql_query($Jsql); 
	$Cmsql="DELETE FROM ".CONTRA_MASTER_TBL." WHERE contra_id='".$cid."' AND voucher_no='".$voucher_no."' AND project_id='".$project_id."'";
	mysql_query($Cmsql); 
	$Csql="DELETE FROM ".CONTRA_DETAILS_TBL." WHERE contra_id='".$cid."' AND voucher_no='".$voucher_no."' AND project_id='".$project_id."'";
	mysql_query($Csql); 	
	$Hsql="DELETE FROM ".VOUCHER_ADJUST_HISTORY_TBL." WHERE voucher_no='".$voucher_no."' AND project_id='".$project_id."'";
	mysql_query($Hsql);
	$Dsql="DELETE FROM ".DEVIT_VOUCHAR_TBL." WHERE voucher_no='".$voucher_no."' AND project_id='".$project_id."'";
	mysql_query($Dsql); 
	$Csql="DELETE FROM ".CREDIT_VOUCHAR_TBL." WHERE voucher_no='".$voucher_no."' AND project_id='".$project_id."'";
	mysql_query($Csql);
  }
   //===== End Delete function ======
   
  function showEditEditor($msg = null) {   	  
	$contra_id 	= getRequest('contra_id');
	$details_id 	= getRequest('did');
	require_once(CLASS_DIR.'/common.list.class.php');
	require_once(CLASS_DIR.'/contra.voucher.new.php');	
	$clistApp = new CommonList();   
	$contraVoucher = new ContraVoucher(); 

	if ($contra_id) {
	if($contra_id > 0 && $details_id > 0){
	mysql_query("START TRANSACTION;");
	$this->deleteContraItem($details_id,$contra_id);
	mysql_query("COMMIT;");
	}
	$advArr 		= $this->getContraMasterInfo($contra_id);
	$advArr 		= parseThisValue($advArr); 
	$data   		= array_merge(array(), $advArr); 

	$data['item_list']	= $this->getContraDetails($contra_id);
	$data['headlist1']   	= $clistApp->getAccountHeadList("Current Assets","S130"); // Cash & Bank
	$data['headlist2']   	= $clistApp->getAccountHeadList("Current Assets","S128","C000105"); // Party
	$data['headlist3']   	= $clistApp->getAccountHeadList("Non Current Assets","","","S130","C000105"); 
	$data['headlist4']   	= $clistApp->getAccountHeadList("Current Assets","","","S130","C000105");
	$data['NLiabilities']   = $clistApp->getAccountHeadList("Non Current Liabilities"); 
	$data['CLiabilities']   = $clistApp->getAccountHeadList("Current Liabilities");
	$data['headlist6']   	= $clistApp->getAccountHeadList("Capital");	
	$data['headlist7']   	= $clistApp->getAccountHeadList("Retained earnings");	
	$data['headlist8']   	= $clistApp->getAccountHeadList("Sales");	
	$data['headlist9']   	= $clistApp->getAccountHeadList("Incomes");	
	$data['headlist10']   	= $clistApp->getAccountHeadList("Direct Expenses");		
	$data['headlist11']   	= $clistApp->getAccountHeadList("Indirect Expenses");	
	$data['headlist12']   	= $clistApp->getAccountHeadList("Opening Balance");	
	$data['headlist13']   	= $clistApp->getAccountHeadList("Adjustments Balance");	
	$data['headlist14']   	= $clistApp->getAccountHeadList("Closing Balance");	
	$data['cogsheadlist']   = $clistApp->getAccountHeadList("Cost Center");	
	$data['supplier_list']  = $clistApp->getSupplierList();	
	$data['retailer_list'] 	= $clistApp->getRetailerList();					
	$data['currency_list']  = $this->getCurrencyList();
        $data['invoice_list']   = $contraVoucher->getDueInvoices();	  
	$data['message'] 	= $msg;
	$data['cmd']     	= getRequest('cmd');
	require_once(CONTRA_VOUCHER_EDIT_SKIN);      
	return true;
	}
   }

   function editPendingVoucher($msg = null){
	require_once(CLASS_DIR.'/common.list.class.php');	
	$clistApp 	= new CommonList(); 
	$ref_id   	= getRequest('ref_id');
	$project_id 	= getFromSession('project_id');
	if(empty($ref_id)){ 
	   $ref_id=0;
	}
	if($ref_id >0){
	$advArr 		= $this->getPendingContraMasterInfo($ref_id);

	if(empty($advArr)){
	    header("location:index.php?app=sales.report&cmd=pending.voucher");
	    exit();
	}
	$advArr 		= parseThisValue($advArr); 
	$data   		= array_merge(array(), $advArr); 

	$data['item_list']	= $this->getPendingContraDetails($ref_id);	 
	 
	$data['headlist1']   	= $clistApp->getAccountHeadList("Current Assets","S130"); // Cash & Bank
	$data['headlist2']   	= $clistApp->getAccountHeadList("Current Assets","S128","C000105"); // Party
	$data['headlist3']   	= $clistApp->getAccountHeadList("Non Current Assets","","","S130","C000105"); 
	$data['headlist4']   	= $clistApp->getAccountHeadList("Current Assets","","","S130","C000105");
	$data['NLiabilities']   = $clistApp->getAccountHeadList("Non Current Liabilities"); 
	$data['CLiabilities']   = $clistApp->getAccountHeadList("Current Liabilities");
	$data['headlist6']   	= $clistApp->getAccountHeadList("Capital");	
	$data['headlist7']   	= $clistApp->getAccountHeadList("Retained earnings");	
	$data['headlist8']   	= $clistApp->getAccountHeadList("Sales");	
	$data['headlist9']   	= $clistApp->getAccountHeadList("Operating Revenue");	
	$data['headlist15']   	= $clistApp->getAccountHeadList("Non-Operating Revenue");	
	$data['headlist10']   	= $clistApp->getAccountHeadList("Direct Expenses");		
	$data['headlist11']   	= $clistApp->getAccountHeadList("Indirect Expenses");	
	$data['headlist12']   	= $clistApp->getAccountHeadList("Opening Balance");	
	$data['headlist13']   	= $clistApp->getAccountHeadList("Adjustments Balance");	
	$data['headlist14']   	= $clistApp->getAccountHeadList("Closing Balance");
	$data['cogsheadlist']   = $clistApp->getAccountHeadList("Cost Center");		
	$data['supplier_list']  = $clistApp->getSupplierList();			     	
	$data['retailer_list'] 	= $clistApp->getRetailerList();			
	$data['currency_list']  = $this->getCurrencyList();	   
	$data['message'] = $msg;
	$data['cmd']     = getRequest('cmd');

	require_once(PENDING_CONTRA_VOUCHER_EDIT_SKIN);     
	
	return true;

	}else{
	    header("location:index.php?app=sales.report&cmd=pending.voucher");
	    exit();
	}
	
   }

    function getPendingContraMasterInfo($id)
    {
        $project_id = getFromSession('project_id');
        $info = array();
        $info['table'] = PENDING_CVMASTER_TBL . ' cm,' . PROJECT_TBL . ' p,' . CURRENCY_TBL . ' c';
        $info['fields'] = array('cm.*', 'p.project_name', 'p.location', 'p.project_logo','c.curr_symble');
        $sql = "cm.project_id = p.project_id AND cm.currency = c.currency_id AND cm.project_id = '" . $project_id . "' AND cm.tmp_grvid = '$id'";
        $info['where'] = $sql;
        $info['groupby'] = array("cm.tmp_grvid");
        //$info['debug']= true;
        $res = select($info);
        if (count($res)) {
            foreach ($res as $i => $v) {
                $data[$i] = $v;
            }
        }
        //dumpVar($data);
        return $data[0];
    }

    function getPendingContraDetails($id)
    {
        $info = array();
        $info['table'] = PENDING_CVDETAILS_TBL . ' cd,' . CURRENCY_TBL . ' c';
        $info['fields'] = array('cd.*', 'c.curr_symble');
        $sql = "cd.currency = c.currency_id AND cd.tmp_grvid = '$id'";
        $info['where'] = $sql;
        $info['groupby'] = array("cd.tmp_id");
        $info['orderby'] = array("cd.tmp_id asc");
        //$info['debug']= true;
        $result = select($info);
        $data = array();
        $cnt = count($result);
        if ($cnt) {
            foreach ($result as $value) {
                $data[] = $value;
            }
        }

        return $data;
    }

    function deletePendingVoucher()
    {
        $tmp_grvid = getRequest('contra_id');
        $tmp_id = getRequest('did');

        if (empty($tmp_grvid)) {
            $tmp_grvid = 0;
        }
        if (empty($tmp_id)) {
            $tmp_id = 0;
        }

        if ($tmp_grvid > 0 && $tmp_id > 0) {
            mysql_query("START TRANSACTION;");

            $project_id = getFromSession('project_id');

            $Csql = "DELETE FROM " . PENDING_CVDETAILS_TBL . " WHERE tmp_id='" . $tmp_id . "' AND tmp_grvid = '$tmp_grvid' AND project_id='" . $project_id . "'";
            mysql_query($Csql);

            mysql_query("COMMIT;");
            
            header("location:index.php?app=contra.voucher.edit&cmd=edit_pending_vouchar&ref_id=$tmp_grvid");
            exit();
        } else {
            header("location:index.php?app=sales.report&cmd=pending.voucher");
            exit();
        }
    }

    function savePendingVoucher()
    {
        $tmp_grvid = $_REQUEST['tmp_grvid'];
        $project_id = getFromSession('project_id');
        if ($tmp_grvid != "") {
            mysql_query("START TRANSACTION;");
            $dr_account = $this->getDrAccount();
            $msql = "SELECT * FROM " . PENDING_CVMASTER_TBL . " WHERE tmp_grvid='$tmp_grvid' AND project_id='$project_id'";
            $mres = mysql_query($msql);
            if (mysql_num_rows($mres) == 0) {
                header("location:index.php?app=contra.voucher.edit&cmd=edit_pending_vouchar&ref_id=$tmp_grvid&msg=Voucher not found");
                exit();
            }
            $masterVoucher = mysql_fetch_object($mres);

            if ($dr_account == "") {
                header("location:index.php?app=contra.voucher.edit&cmd=edit_pending_vouchar&ref_id=$tmp_grvid&msg=Dr Account Not Found");
                exit();
            }

            if ($dr_account != "") {
                $drHeadtypes = getHeadType($dr_account);
                $voucharType = getRequest('vouchar_type');
                if (($drHeadtypes == "Cash" || $drHeadtypes == "Bank") && ($voucharType == "Others Vouchar")) {
                    $vouchar_type = "Received Vouchar";
                    $transaction_name = "Received";
                } elseif (($drHeadtypes != "Cash" && $drHeadtypes != "Bank") && ($voucharType == "Others Vouchar")) {
                    $vouchar_type = "Payment Vouchar";
                    $transaction_name = "Payment";
                } else {
                    $vouchar_type = getRequest('vouchar_type');
                    if ($voucharType == "Payable Vouchar") {
                        $transaction_name = "Payable";
                    }
                    if ($voucharType == "Recievable Vouchar") {
                        $transaction_name = "Recievable";
                    }
                    if ($voucharType == "Journal Vouchar") {
                        $transaction_name = "Journal";
                    }
                    if ($voucharType == "Contra Vouchar") {
                        $transaction_name = "Contra";
                    }
                }
            }

            $requestdata = array();
            $requestdata = getUserDataSet(PENDING_CVMASTER_TBL);
   
	    if ($masterVoucher->is_money_recipt == 1) {
		$requestdata['mr_no'] = getRequest('mr_no');
		$requestdata['dr_amount'] = (float)getRequest('totaldr_amount');
		$requestdata['cr_amount'] = (float)getRequest('totalcr_amount');
		$requestdata['mode_of_payment'] = getRequest('mode_of_payment');
		$requestdata['bank_journal'] = getRequest('bank_journal');
		//$requestdata['cost_center'] = getRequest('cost_center');
		//$requestdata['adjustment'] = getRequest('adjustment');
		//$requestdata['beddebts'] = getRequest('beddebts');
		$requestdata['created_date'] = formatDate(getRequest('created_date'));
		$requestdata['vouchar_type'] = $vouchar_type;
		$requestdata['transaction_name'] = $transaction_name;
		$requestdata['description'] = getRequest('details');
		$requestdata['edited_by'] = getFromSession('userid');
		$requestdata['edited_time'] = date('Y-m-d h:i:s');
		$requestdata['attachment'] = $masterVoucher->attachment;
            }else{
		$requestdata['dr_amount'] = (float)getRequest('totaldr_amount');
		$requestdata['cr_amount'] = (float)getRequest('totalcr_amount');
		$requestdata['mode_of_payment'] = getRequest('mode_of_payment');
		$requestdata['bank_journal'] = getRequest('bank_journal');
		$requestdata['cost_center'] = getRequest('cost_center');
		$requestdata['adjustment'] = getRequest('adjustment');
		$requestdata['beddebts'] = getRequest('beddebts');
		$requestdata['created_date'] = formatDate(getRequest('created_date'));
		$requestdata['vouchar_type'] = $vouchar_type;
		$requestdata['transaction_name'] = $transaction_name;
		$requestdata['description'] = getRequest('details');
		$requestdata['edited_by'] = getFromSession('userid');
		$requestdata['edited_time'] = date('Y-m-d h:i:s');
		$requestdata['attachment'] = $masterVoucher->attachment;
	    }
            

            if (isset($_FILES['attachment']) && $_FILES['attachment']['error'] == 0) {
                $fileName = $this->voucherFileUpload($tmp_grvid, $masterVoucher->is_money_recipt);

                if ($masterVoucher->attachment != "") {
                    $attachment = $masterVoucher->attachment;
                    // Define the path to the file
                    $filePath = IMAGES_DIR . '/voucher/' . $attachment;
                    // Path to file

                    if ($masterVoucher->is_money_recipt == 1) {
                        $filePath = DOCUMENT_ROOT . '/../hera/assets/attachment/' . $attachment;
                    }

                    // Check if the file exists
                    if (file_exists($filePath)) {
                        // Unlink (delete) the file
                        unlink($filePath);
                    }
                }

                if (strpos($fileName, '.') !== false) {
                    $requestdata['attachment'] = $fileName;
                }
            }

            $info = array();
            $info['table'] = PENDING_CVMASTER_TBL;
            $info['data'] = $requestdata;
            $info['where'] = "tmp_grvid ='$tmp_grvid'";
            $info['debug']  	=  true;
            $res = update($info);

                $requestDetailsdata = array();
                $sl = $_POST['sl'];
                $i = 1;
                if ($sl > 0) {
                    while ($i < $sl) {
                        if ($_POST["cr_account$i"] != "") {
                            $requestDetailsdata = getUserDataSet(PENDING_CVDETAILS_TBL);

                            $requestDetailsdata['tmp_grvid'] = $tmp_grvid;
                            if ($_POST["headtypes$i"] != "" && ($_POST["headtypes$i"] == "Dr" || $_POST["headtypes$i"] == "Cr")) {
                                if ($_POST["headtypes$i"] == "Dr") {
                                    $requestDetailsdata['headtypes'] = "Dr";
                                    $dr_account = $_POST["cr_account$i"];
                                    $requestDetailsdata['dr_account'] = $dr_account;
                                    $requestDetailsdata['cr_acname'] = $this->getHeadName($dr_account);
                                    $cr_amount = $_POST["dr_amount$i"];
                                } elseif ($_POST["headtypes$i"] == "Cr") {
                                    $requestDetailsdata['headtypes'] = "Cr";
                                    $cr_account = $_POST["cr_account$i"];
                                    $requestDetailsdata['cr_account'] = $cr_account;
                                    $requestDetailsdata['cr_acname'] = $this->getHeadName($cr_account);
                                    $cr_amount = $_POST["cr_amount$i"];
                                }
                            } else {
                                if ($_POST["dr_amount$i"] > 0) {
                                    $requestDetailsdata['headtypes'] = "Dr";
                                    $dr_account = $_POST["cr_account$i"];
                                    $requestDetailsdata['dr_account'] = $dr_account;
                                    $requestDetailsdata['cr_acname'] = $this->getHeadName($dr_account);
                                    $cr_amount = $_POST["dr_amount$i"];
                                } elseif ($_POST["cr_amount$i"] > 0) {
                                    $requestDetailsdata['headtypes'] = "Cr";
                                    $cr_account = $_POST["cr_account$i"];
                                    $requestDetailsdata['cr_account'] = $cr_account;
                                    $requestDetailsdata['cr_acname'] = $this->getHeadName($cr_account);
                                    $cr_amount = $_POST["cr_amount$i"];
                                }
                            }

                            $requestDetailsdata['currency'] = $_POST["currency"];
                            $requestDetailsdata['currencyName'] = isset($_POST["currencyName"]) ? $_POST["currencyName"] : "BDT";

			    if ($masterVoucher->is_money_recipt == 1) {
				$requestDetailsdata['cr_amount'] = $cr_amount;
				$requestDetailsdata['bank_name'] = ""; //$_POST["bank_name$i"];
				$requestDetailsdata['acc_no'] = ""; //$_POST["acc_no$i"];
				$requestDetailsdata['check_no'] = $_POST["check_no$i"];
				$requestDetailsdata['check_issue_date'] = formatDate($_POST["check_issue_date$i"]);
				$requestDetailsdata['cheque_type'] = $_POST["cheque_type$i"];
				$requestDetailsdata['vouchar_type'] = $vouchar_type;
				$requestDetailsdata['consignee'] = ""; //$_POST["consignee$i"];
				$requestDetailsdata['transaction_name'] = $transaction_name;
				$requestDetailsdata['description'] = $_POST["description$i"];
			    }else{
				$requestDetailsdata['cr_amount'] = $cr_amount;
				$requestDetailsdata['bank_name'] = $_POST["bank_name$i"];
				$requestDetailsdata['acc_no'] = $_POST["acc_no$i"];
				$requestDetailsdata['check_no'] = $_POST["check_no$i"];
				$requestDetailsdata['check_issue_date'] = formatDate($_POST["check_issue_date$i"]);
				$requestDetailsdata['cheque_type'] = $_POST["cheque_type$i"];
				$requestDetailsdata['vouchar_type'] = $vouchar_type;
				$requestDetailsdata['consignee'] = $_POST["consignee$i"];
				$requestDetailsdata['due_invoice'] = $_POST["due_invoice$i"];
				$requestDetailsdata['transaction_name'] = $transaction_name;
				$requestDetailsdata['description'] = $_POST["description$i"];
			    }

                            if ($_POST["tmp_id$i"] != "") {
                                $requestDetailsdata['edited_by'] = getFromSession('userid');
                            } else {
                                $requestDetailsdata['project_id'] = getFromSession('project_id');
                                $requestDetailsdata['created_by'] = getFromSession('userid');
                            }

                            $info = array();
                            $info['table'] = PENDING_CVDETAILS_TBL;
                            $info['data'] = $requestDetailsdata;
                            //$info['debug']  	=  true;

                            if ($_POST["tmp_id$i"] != "") {
                                $tmpID = $_POST["tmp_id$i"];
                                $info['where'] = "tmp_id ='$tmpID'";

                                update($info);
                            } else {
                                insert($info);
                            }
                        }
                        $i++;
                    }
                }
            

	    mysql_query("COMMIT;");

            header("location:index.php?app=contra.voucher.edit&cmd=edit_pending_vouchar&ref_id=$tmp_grvid");
            exit();
        } else {
            header("location:index.php?app=sales.report&cmd=pending.voucher");
            exit();
        }
    }


     function voucherFileUpload($maxId, $is_money_recipt = 0)
    {
        $saveDir = IMAGES_DIR . '/voucher/';
	$prefixName = "contra_voucher";
        if ($is_money_recipt) {
            $saveDir = DOCUMENT_ROOT . '/../hera/assets/attachment/';
	    $prefixName = "money_recipt";
        }

        $allowedTypes = ['doc', 'docx', 'pdf', 'gif', 'jpg', 'png', 'jpeg']; // Allowed file types
        $maxSize = 4000; // Maximum file size in KB (4MB)

        // Check if file was uploaded
        if (isset($_FILES['attachment'])) {
            $file = $_FILES['attachment'];
            $fileError = $file['error'];
            $fileTmpName = $file['tmp_name'];
            $fileName = $file['name'];
            $fileSize = $file['size'];
            $fileType = pathinfo($fileName, PATHINFO_EXTENSION); // Get file extension

            // Check for errors
            if ($fileError !== UPLOAD_ERR_OK) {
                return "Error during file upload.";
            }

            // Validate file size
            if ($fileSize > $maxSize * 1024) { // Size in bytes
                return "File is too large. Maximum size is {$maxSize} KB.";
            }

            // Validate file type
            if (!in_array(strtolower($fileType), $allowedTypes)) {
                return "Invalid file type. Allowed types are: " . implode(", ", $allowedTypes);
            }

            // Generate unique file name
            $newFileName = $prefixName . $maxId . '.' . $fileType;
            $uploadPath = $saveDir . $newFileName;

            // Move the uploaded file to the destination folder
            if (move_uploaded_file($fileTmpName, $uploadPath)) {
                // After successful upload, return the file name
                return $newFileName;
            } else {
                return "Failed to move uploaded file.";
            }
        }
        return "No file uploaded.";
    }


    

   function saveVoucher(){	
	$voucher_no 	= $_REQUEST['voucher_no']; $contra_id 	= $_REQUEST['contra_id']; $project_id = getFromSession('project_id');
	if($voucher_no!="" && $contra_id!=""){
	mysql_query("START TRANSACTION;");
	$dr_account = $this->getDrAccount();
	$msql = "SELECT * FROM ".CONTRA_MASTER_TBL." WHERE contra_id='".$contra_id."' AND voucher_no='".$voucher_no."' AND project_id='".$project_id."'";
	$mrow = mysql_fetch_object(mysql_query($msql));
	$befoure_amount = $mrow->dr_amount;

	if($dr_account!=""){		
		$_SESSION['Drheadtypes'] = getHeadType($dr_account);					
		if((getHeadType($dr_account)=="Cash" || getHeadType($dr_account)=="Bank") && (getRequest('vouchar_type')=="Others Vouchar")){
		$_SESSION['vouchar_type'] 		= "Received Vouchar"; $_SESSION['transaction_name'] 	= "Received";
		}elseif((getHeadType($dr_account)!="Cash" && getHeadType($dr_account)!="Bank") && (getRequest('vouchar_type')=="Others Vouchar")){
		$_SESSION['vouchar_type'] = "Payment Vouchar"; 		$_SESSION['transaction_name'] = "Payment";	
		}else{
		$_SESSION['vouchar_type'] = getRequest('vouchar_type');
		}
	}
	$this->roolbackAdjustAmount($voucher_no);
	$this->deletePreviousLedger($voucher_no);
	
	$dr_amount = getRequest('totaldr_amount');	
	$this->updateVoucher($voucher_no,$_SESSION['vouchar_type'],$dr_amount);
	$this->saveContraMasterVouchar($contra_id,$voucher_no);	
	$this->saveContraDetailsVouchar($contra_id,$voucher_no);  
	$created_by = getFromSession('userid');
	
	SaveActivityLog("Contra Voucher",$voucher_no,"Edit",$created_by,$befoure_amount,$dr_amount);
	mysql_query("COMMIT;");
	header("location:index.php?app=contra.voucher.new&cmd=print_vouchar&contra_id=".$contra_id);	
	}else{		
	header("location:index.php?app=voucher.edit&cmd=list");	
	}		
   }
   function saveContraMasterVouchar($contra_id,$voucher_no){
	$requestdata = array();
	$requestdata = getUserDataSet(CONTRA_MASTER_TBL);
	$requestdata['dr_amount'] 	= getRequest('totaldr_amount');
	$requestdata['cr_amount'] 	= getRequest('totalcr_amount');
	$requestdata['mode_of_payment'] = getRequest('mode_of_payment');
	$requestdata['bank_journal']	= getRequest('bank_journal');
	$requestdata['cost_center'] 	= getRequest('cost_center');
	$requestdata['voucher_no'] 	= $voucher_no;
	$requestdata['created_date']	= formatDate(getRequest('created_date'));		
	$requestdata['project_id'] 	= getFromSession('project_id');		
	$requestdata['created_by'] 	= getFromSession('userid');		
	$requestdata['vouchar_type'] 	= $_SESSION['vouchar_type']; 
	$requestdata['transaction_name']= $_SESSION['transaction_name'];
	$requestdata['description'] 	= getRequest('details');
	$info        		=  array();
	$info['table']	= CONTRA_MASTER_TBL;
	$info['data'] 	= $requestdata;  
	$info['where']	= "contra_id ='".$contra_id."'";    
	//$info['debug']  	=  true;
	$res = update($info);	
	if($res){
	return true;		
	}else{ return false;}
    }
    function updateVoucher($voucher_no,$vouchar_type,$dr_amount){
	// ===Start Update Voucher===
	$payment_mode 	= getRequest('mode_of_payment');
	$headtypes		= "Contra Voucher";
	$dr_account		= "";
	$cr_account		= "";
	$cr_amount 		= $dr_amount;
	$created_date	= getRequest('created_date');
	$bank_name		= "";
	$acc_no			= "";
	$check_no		= "";
	$check_date		= "";
	$description	= "Contra Voucher";
	
	$dvres = $this->saveDebitVouchar($voucher_no,$payment_mode,$vouchar_type,$dr_account,$dr_amount,$cr_account,$created_date,$bank_name,$acc_no,$check_no,$check_date);
	if($dvres){
	$this->saveCreditVouchar($voucher_no,$payment_mode,$vouchar_type,$dr_account,$cr_account,$cr_amount,$created_date,$bank_name,$acc_no,$check_no,$check_date,$description);	
	return true;
	}else{
		return false;
	}
	// ===End Update Voucher===		
    }
    function getDrAccount(){		
	$sl = $_REQUEST['sl']; $i=1;
	if($sl>0){
	  while($i < $sl){
		if($_REQUEST["dr_amount$i"]>0){
		$dr_account 		= $_REQUEST["cr_account$i"]; break;
		}
		$i++;	
	  }
	}
	return $dr_account;
    }
    function getPrvDrAccount(){		
	$sl = $_POST['sl']; $i=1;
	if($sl>0){
	  while($i < $sl){
		if($_POST["headtypes$i"]=="Dr"){
		$dr_account 		= $_POST["prv_head$i"]; break;
		}
		$i++;	
	  }
	}
	return $dr_account;
    }
    function roolbackAdjustAmount($voucher_no){
	//========= Rollback for Edit===========
	$dr_account = $this->getPrvDrAccount();
	$prv_vouchartype = getRequest('prv_vouchartype');
	if($prv_vouchartype!="Payable Vouchar" && $prv_vouchartype!="Recievable Vouchar"){ 
		$HeadType = getHeadType($dr_account);
		$sl = $_POST['sl']; $i=1;
		if($sl>0){
		  while($i < $sl){
			if($_POST["headtypes$i"]=="Cr"){
				$craccount 		= $_POST["prv_head$i"];
				if($HeadType=="Cash" || $HeadType=="Bank"){
					$head_type  = getHeadType($craccount);
					if($head_type=="Customer"){
					$this->rollbackCustomerReceibavle($voucher_no);
					}elseif($head_type=="Supplier"){
					$this->rollbackSupplierReceibavle($voucher_no);
					}	
				}
			}elseif($_POST["headtypes$i"]=="Dr"){
				$draccount 		= $_POST["prv_head$i"];
				if($HeadType!="Cash" || $HeadType!="Bank"){
					$head_type 		= getHeadType($draccount);
					if($head_type=="Supplier"){
					$this->rollbackSupplierPayble($voucher_no);
					}elseif($head_type=="Customer"){		
					$this->rollbackCustomerPayble($voucher_no);
					}	
				}	
			}
			$i++;	
		  }//end while
		}//end if
	}//end vouchar_type	
     }
	
     function saveContraDetailsVouchar($contra_id,$voucher_no){
	require_once(CLASS_DIR.'/common.list.class.php');	
	$clistApp = new CommonList();
	$requestdata = array();			
	$sl = $_POST['sl']; $i=1;
	if($sl>0){
	  while($i < $sl){
		if($_POST["cr_account$i"] !=""){
		$requestdata = getUserDataSet(CONTRA_DETAILS_TBL);		
		$requestdata['contra_id'] 		= $contra_id;	
		$requestdata['voucher_no'] 		= $voucher_no;				
		$requestdata['project_id'] 		= getFromSession('project_id');	
		
		if($_POST["dr_amount$i"]>0){
			$requestdata['headtypes'] 	= "Dr";
			$requestdata['dr_account'] 	= $_POST["cr_account$i"];
			$Draccount 			= $requestdata['dr_account'];
			$cr_amount			= $_POST["dr_amount$i"];
		}elseif($_POST["cr_amount$i"]>0){
			$requestdata['headtypes'] 	= "Cr";
			$cr_amount			= $_POST["cr_amount$i"];
			$Craccount 			= $_POST["cr_account$i"];
		}
		
		$requestdata['currency'] 	= $_POST["currency$i"];	
		$requestdata['cr_account']	= $_POST["cr_account$i"];
		$requestdata['cr_amount'] 	= $cr_amount;
		$requestdata['bank_name'] 	= $_POST["bank_name$i"];
		$requestdata['acc_no'] 		= $_POST["acc_no$i"];
		$requestdata['check_no'] 	= $_POST["check_no$i"];
		$requestdata['check_issue_date']= formatDate($_POST["check_issue_date$i"]);
		
		$requestdata['cheque_type'] 	= $_POST["cheque_type$i"];

		$requestdata['vouchar_type'] 	= $_SESSION['vouchar_type']; 
		$requestdata['consignee']	= $_POST["consignee$i"];
		$requestdata['transaction_name']= $_SESSION['transaction_name'];	
		$requestdata['description'] 	= $_POST["description$i"];
		$requestdata['created_by'] 	= getFromSession('userid');
		
		$info        		=  array();
		$info['table']		= CONTRA_DETAILS_TBL;
		$info['data'] 		= $requestdata;
		//$info['debug']  	=  true;   
		$res = insert($info);		
		if($res){
			$description  	= $requestdata['description'];
			$consignee 	= $requestdata['consignee'];
			$created_date 	= formatDate(getRequest('created_date'));
			$issue_date 	= $requestdata['check_issue_date'];			
			$vouchar_type 	= $_SESSION['vouchar_type']; 
			$transaction_type 	= $_SESSION['transaction_name'];
			$project_id	 = getFromSession('project_id');
			$cost_center = getRequest('cost_center');
			
			if($consignee !=""){
			$CrAmount = $cr_amount;
			$totalCR  = $this->getTotalCreditAmount($consignee,getFromSession('project_id'));
			$totalDR  = $this->getTotalDebitAmount($consignee,getFromSession('project_id'));					 
			$balance  = ($totalDR-($totalCR+$CrAmount));					 
			$this->saveRetailerJournal($voucher_no,$consignee,$transaction_type,$project_id,$description,0,$CrAmount,$balance,1,$created_date,$issue_date);
			$CrAmount =0; $balance=0;	
			}
			
			if($requestdata['headtypes']=="Dr"){	
			$dr_account 	= $Draccount;
			$DrAmount 	= $cr_amount;
			//======= Dr Account ======	 
			$totalPartyCR  = $this->getTotalCreditAmount($dr_account,getFromSession('project_id'));
			$totalPartyDR  = $this->getTotalDebitAmount($dr_account,getFromSession('project_id'));
			$PartyBalance  = (($totalPartyDR+$DrAmount)-$totalPartyCR);
			$this->saveAccountJournal($voucher_no,$dr_account,$transaction_type,$project_id,$description,$DrAmount,0,$PartyBalance,1,$created_date,$issue_date,$cost_center);	
			
			$HeadType 		  = getHeadType($dr_account);  
			
			//if($vouchar_type!="Payable Vouchar" && $vouchar_type!="Recievable Vouchar"){ 
			$HeadType 		  = $_SESSION['Drheadtypes'];
			if($HeadType!="Cash" || $HeadType!="Bank"){
				require_once(CLASS_DIR.'/advanced_payment.class.php');	
				$advpApp 		= new AdvancedPayment();
				$head_type 		= getHeadType($dr_account);
				$account_head 	= $dr_account;
				if($head_type=="Supplier"){
				$advpApp->adjustSupplierPayble($voucher_no,$dr_account,$DrAmount,$created_date);
				}elseif($head_type=="Customer"){		
				$advpApp->adjustCustomerPayble($voucher_no,$dr_account,$DrAmount,$created_date);
				}	
			}
			//}//end vouchar_type
			
			}elseif($requestdata['headtypes']=="Cr"){	
			//========= Cr Account ========
			$cr_account = $Craccount;
			$CrAmount   = $cr_amount;
			$totalCR    = $this->getTotalCreditAmount($cr_account,getFromSession('project_id'));
			$totalDR    = $this->getTotalDebitAmount($cr_account,getFromSession('project_id'));	 
			$balance  = ($totalDR-($totalCR+$CrAmount));					 
			$this->saveAccountJournal($voucher_no,$cr_account,$transaction_type,$project_id,$description,0,$CrAmount,$balance,1,$created_date,$issue_date,$cost_center);
			
			//if($vouchar_type!="Payable Vouchar" && $vouchar_type!="Recievable Vouchar"){ 
			$HeadType 		  = $_SESSION['Drheadtypes'];
			if($HeadType=="Cash" || $HeadType=="Bank"){
				require_once(CLASS_DIR.'/general_vouchar.class.php');	
				$gvApp 		= new GeneralVouchar();
				$head_type  = getHeadType($cr_account);
				if($head_type=="Customer"){
				$gvApp->adjustCustomerReceibavle($cr_account,$voucher_no,$CrAmount,$created_date);
				}elseif($head_type=="Supplier"){
				$gvApp->adjustSupplierReceibavle($cr_account,$voucher_no,$CrAmount,$created_date);
				}	
			}
			//}//end vouchar_type
			
			$collection_source = getRequest('collection_source');
			if($collection_source !="Others"){
			 
			 if($collection_source=="Servicing"){
				$rsql= "SELECT warranty_id,service_bill,paid_amount,due FROM ".WARRANTY_TBL." WHERE customer_id='".$cr_account."' AND due >0";  				
				$rres = mysql_query($rsql);
				while($srow = mysql_fetch_object($rres)){
				 $warranty_id = $srow->warranty_id;
				 if($CrAmount>=$srow->due){
					$CrAmount = $CrAmount - $srow->due;
					$totalPaidAmount = $srow->paid_amount+$srow->due;
					if($totalPaidAmount==$srow->service_bill){
					 $pusql="UPDATE ".WARRANTY_TBL." SET paid_amount='".$totalPaidAmount."',due='0' WHERE warranty_id='$warranty_id'";
					 mysql_query($pusql);
					}
				}elseif($CrAmount<$srow->due){
					$presentDue = $srow->due-$CrAmount;
					$PaidAmount = $srow->paid_amount+$CrAmount;
					if($PaidAmount<$srow->service_bill){
					 $pusql2="UPDATE ".WARRANTY_TBL." SET paid_amount='".$PaidAmount."',due='$presentDue' WHERE warranty_id='$warranty_id'";
					 mysql_query($pusql2);
					}
				 }
				}// end while
			 }
			} // end collection source
			}// end headtype Cr
			
			//==== Start Adjust Sales/Purchase =====
			require_once(CLASS_DIR.'/contra.voucher.new.class.php');	
			$contvApp = new ContraVoucher();
			$HeadType = $_SESSION['Drheadtypes'];
			if($HeadType=="Cash" || $HeadType=="Bank"){				
				//==== Sales Collection ======				
				$head_type  = getHeadType($cr_account);
				if($head_type=="Customer" || $head_type=="Supplier"){
				$contvApp->adjustACReceibavle($voucher_no,$CrAmount,$created_date);
				}	
			}elseif($HeadType !="Cash" || $HeadType !="Bank"){
				//==== Purchase Payment ======	
				$head_type  = getHeadType($dr_account);
				if($head_type=="Customer" || $head_type=="Supplier"){
				$contvApp->adjustACPayable($voucher_no,$DrAmount,$created_date);
				}
			}
			
		}// save
		
		} // end cr account
		$i++;
	  }// end while 
		
    }// end if
	
	if($res){ 
	 $_SESSION['headtypes']=""; $_SESSION['Drheadtypes']=""; $_SESSION['vouchar_type']=""; $_SESSION['transaction_name']="";
	}
	
  } //End of contraDetails()
  //====== saveDebitVouchar =======
  function saveDebitVouchar($voucher_no,$payment_mode,$vouchar_type,$dr_account,$dr_amount,$cr_account,$created_date,$bank_name=NULL,$acc_no=NULL,$check_no=NULL,$check_date=NULL)
  {     
	  $requestdata = array();
	  $mode_of_payment = $payment_mode;
	  $requestdata = getUserDataSet(DEVIT_VOUCHAR_TBL);			  	  		    
	  $requestdata['head_type']     	= "";   
	  $requestdata['account_head']      = $dr_account; 
	  $requestdata['debit']        		= $dr_amount;    
	  $requestdata['credit']        	= 0; 
	  if($mode_of_payment =="Check"){
		$requestdata['mode_of_payment'] = "Bank";
		$requestdata['bank_name'] 		= $bank_name;
		$requestdata['acc_no'] 			= $acc_no;
		$requestdata['check_no'] 		= $check_no;
		$requestdata['check_issue_date']= $check_date;	
	  }else{
		$requestdata['bank_name']= "";
		$requestdata['acc_no']	 = "";
		$requestdata['check_no'] = "";
		$requestdata['check_issue_date']= "";  
	  }
	  $requestdata['project_id']      = getFromSession('project_id');    
	  $requestdata['created_by']      = getFromSession('userid'); 
	  $requestdata['created_date']    = formatDate($created_date);
	  $requestdata['paid_amount']     = $dr_amount;	
	  $requestdata['due']     = 0;		
	  $requestdata['vouchar_type']	  = $vouchar_type;
	  		
	  $requestdata['description']="Contra Voucher";
	  $requestdata['branch_id'] = getFromSession('branch_id');
	  
	  $info        		=  array();
	  $info['table']	= DEVIT_VOUCHAR_TBL;
	  $info['data'] 	= $requestdata;  
	  $info['where']	= "voucher_no ='".$voucher_no."'";    
	  //$info['debug']  	=  true;
	  $res = update($info);			

	  if($res) {
		return true;
	  }else {	
		return false;	
	  }  

    }//EOFn  

    function saveCreditVouchar($voucher_no,$payment_mode,$vouchar_type,$dr_account,$cr_account,$cr_amount,$created_date,$bank_name=NULL,$acc_no=NULL,$check_no=NULL,$check_date=NULL,$description=NULL)
    {     
	  $mode_of_payment = $payment_mode;
	  $requestdata = array();
	  $requestdata = getUserDataSet(CREDIT_VOUCHAR_TBL);
	  $requestdata['head_type']     	= "";   
	  $requestdata['account_head']      = $cr_account; 
	  $requestdata['debit']        		= 0; 
	  $requestdata['credit']        	= $cr_amount;
	  if($mode_of_payment =="Check"){
		$requestdata['mode_of_payment'] = "Bank";
		$requestdata['bank_name'] 		= $bank_name;
		$requestdata['acc_no'] 			= $acc_no;
		$requestdata['check_no'] 		= $check_no;
		$requestdata['check_issue_date']= $check_date;	
	  }else{
		$requestdata['bank_name'] = "";
		$requestdata['acc_no'] = "";
		$requestdata['check_no'] = "";
		$requestdata['check_issue_date'] = "";  
	  }
	  $requestdata['project_id']        = getFromSession('project_id');    
	  $requestdata['created_by']        = getFromSession('userid'); 			 
	  $requestdata['created_date']      = formatDate($created_date);
	  //$requestdata['created_date']      = date('Y-m-d h:i:s');
	  $requestdata['vouchar_type'] 		= $vouchar_type;
	  $requestdata['description']="Contra Voucher";
	  $requestdata['branch_id'] = getFromSession('branch_id');
	  
	  $info        		=  array();
	  $info['table']	= CREDIT_VOUCHAR_TBL;
	  $info['data'] 	= $requestdata;
	  $info['where']	= "voucher_no ='".$voucher_no."'";         
	  //$info['debug']  	=  true;
	  $res = update($info);	  

	  if($res['affected_rows']) {
		return true;
	  }else {	
		return false;
	  }  

    }//EOFn
	
    function rollbackCustomerReceibavle($voucher_no){
	$project_id = getFromSession('project_id');
	$getSql	= "SELECT * FROM ".VOUCHER_ADJUST_HISTORY_TBL." WHERE voucher_no = '".$voucher_no."' AND project_id='".$project_id."'";
	$gres 	= mysql_query($getSql);
	if(mysql_num_rows($gres)>0){
	  while($row = mysql_fetch_object($gres)){
		 $adjust_tbl 	= $row->adjust_tbl; 
		 $adjust_ref 	= $row->adjust_ref;  
		 $adjust_amount = $row->adjust_amount; 
		 $adjust_type	= $row->adjust_type;
		 $adjust_tblArr = explode(".",$adjust_tbl);
		 $adjust_tbl_name = $adjust_tblArr[1];
		 //======= rollback previous sales amount =========
		 if($adjust_tbl_name=="sales_master" && $adjust_type=="+"){			 
			$getsSql= "SELECT * FROM ".SALES_MASTER_TBL." WHERE voucher_no = '".$adjust_ref."' AND project_id='".getFromSession('project_id')."'";
			$gsres  = mysql_query($getsSql);
			$srow = mysql_fetch_object($gsres);
			$paid_amount = ($srow->paid_amount-$adjust_amount);
			$due 		 = ($srow->due+$adjust_amount); 
			$Usql="UPDATE ".SALES_MASTER_TBL." SET paid_amount='$paid_amount',due='$due' WHERE voucher_no='".$adjust_ref."' AND project_id='".$project_id."'";
			mysql_query($Usql);
		 }elseif(($adjust_tbl_name=="cs_delivery_product" || $adjust_tbl_name=="devit_vouchar") && ($adjust_type=="+")){	 
		 	//======= rollback previous recievable amount =========		 
			$getdSql= "SELECT * FROM ".DEVIT_VOUCHAR_TBL." WHERE voucher_no = '".$adjust_ref."' AND project_id='".getFromSession('project_id')."'";
			$gdres  = mysql_query($getdSql);
			$drow = mysql_fetch_object($gdres);
			$paid_amount = ($drow->paid_amount-$adjust_amount);
			$due 		  = ($drow->due+$adjust_amount); 
			$Usql="UPDATE ".DEVIT_VOUCHAR_TBL." SET paid_amount='$paid_amount',due='$due',status=0 WHERE voucher_no='".$adjust_ref."' 
			AND project_id='".$project_id."'";
			mysql_query($Usql);
		 }elseif($adjust_tbl_name=="purchase_return_receivable" && $adjust_type=="+"){
			//======= rollback previous purchase return recievable amount =========			 
			$getdSql= "SELECT * FROM ".PURCHASE_RETURN_RECEIBAVLE_TBL." WHERE return_id='".$adjust_ref."' AND project_id='".$project_id."' 
			AND paid_amount >0";
			$gdres  = mysql_query($getdSql);
			$drow = mysql_fetch_object($gdres);
			$paid_amount = ($drow->paid_amount-$adjust_amount);
			$due 		 = ($drow->due+$adjust_amount); 
			$Usql="UPDATE ".PURCHASE_RETURN_RECEIBAVLE_TBL." SET paid_amount='$paid_amount',due='$due' WHERE return_id='".$adjust_ref."' 
			AND project_id='".$project_id."'";
			mysql_query($Usql);
		 }elseif($adjust_tbl_name=="sales_return_payble" && $adjust_type=="Payble ROA"){
			//======= delete previous advanced received payble amount =========			 
			$Usql="DELETE FROM ".SALES_RETURN_PAYBLE_TBL." WHERE voucher_no='".$adjust_ref."' AND project_id='".$project_id."'";
			mysql_query($Usql); 
		 }
	  }
	}
  }
  function rollbackCustomerPayble($voucher_no){
	$project_id = getFromSession('project_id');
	$getSql	= "SELECT * FROM ".VOUCHER_ADJUST_HISTORY_TBL." WHERE voucher_no = '".$voucher_no."' AND project_id='".$project_id."'";
	$gres 	= mysql_query($getSql);
	if(mysql_num_rows($gres)>0){
	  while($row = mysql_fetch_object($gres)){
		 $adjust_tbl 	= $row->adjust_tbl; 
		 $adjust_ref 	= $row->adjust_ref;  
		 $adjust_amount = $row->adjust_amount; 
		 $adjust_type	= $row->adjust_type;
		 $adjust_tblArr = explode(".",$adjust_tbl);
		 $adjust_tbl_name = $adjust_tblArr[1];
		 //======= rollback previous opening payble amount =========
		 if(($adjust_tbl_name=="cs_delivery_product" || $adjust_tbl_name=="devit_vouchar") && ($adjust_type=="-")){			 
			$getsSql= "SELECT * FROM ".DEVIT_VOUCHAR_TBL." WHERE voucher_no = '".$adjust_ref."' AND project_id='".getFromSession('project_id')."'";
			$gsres  = mysql_query($getsSql);
			$srow = mysql_fetch_object($gsres);
			$paid_amount = ($srow->paid_amount-$adjust_amount);
			$due 		  = ($srow->due+$adjust_amount); 
			$Usql="UPDATE ".DEVIT_VOUCHAR_TBL." SET paid_amount='$paid_amount',due='$due',`status`=0 WHERE voucher_no='".$adjust_ref."' 
			AND project_id='".$project_id."'";
			mysql_query($Usql);
		 }elseif(($adjust_tbl_name=="sales_return_payble") && ($adjust_type=="-")){	 
		 	//======= rollback previous sales return/advanced paid payble amount =========		 
			$getdSql= "SELECT * FROM ".SALES_RETURN_PAYBLE_TBL." WHERE return_id = '".$adjust_ref."' AND project_id='".$project_id."'";
			$gdres  = mysql_query($getdSql);
			$drow = mysql_fetch_object($gdres);
			$paid_amount = ($drow->paid_amount-$adjust_amount);
			$due 		  = ($drow->due+$adjust_amount); 
			$Usql="UPDATE ".SALES_RETURN_PAYBLE_TBL." SET paid_amount='$paid_amount',due='$due' WHERE return_id='".$adjust_ref."' 
			AND project_id='".$project_id."'";
			mysql_query($Usql);		
			
		 }elseif($adjust_tbl_name=="purchase_return_receivable" && $adjust_type=="Receibavle ROA"){
			//======= delete previous purchase return/advanced payment payble amount =========			 
			$Usql="DELETE FROM ".PURCHASE_RETURN_RECEIBAVLE_TBL." WHERE voucher_no='".$adjust_ref."' AND project_id='".$project_id."'";
			mysql_query($Usql);
		 }
	  }// end while
	}
  }
  
  function rollbackSupplierReceibavle($voucher_no){
	$project_id = getFromSession('project_id');
	$getSql	="SELECT * FROM ".VOUCHER_ADJUST_HISTORY_TBL." WHERE voucher_no = '".$voucher_no."' AND project_id='".$project_id."'";
	$gres 	= mysql_query($getSql);
	if(mysql_num_rows($gres)>0){
	  while($row = mysql_fetch_object($gres)){
		 $adjust_tbl 	= $row->adjust_tbl; 
		 $adjust_ref 	= $row->adjust_ref;  
		 $adjust_amount = $row->adjust_amount; 
		 $adjust_type	= $row->adjust_type;
		 $adjust_tblArr = explode(".",$adjust_tbl);
		 $adjust_tbl_name = $adjust_tblArr[1];
		 //======= rollback previous opening recievable amount =========
		 if(($adjust_tbl_name=="cs_delivery_product" || $adjust_tbl_name=="devit_vouchar") && ($adjust_type=="-")){			 
			$getsSql= "SELECT * FROM ".DEVIT_VOUCHAR_TBL." WHERE voucher_no = '".$adjust_ref."' AND project_id='".getFromSession('project_id')."'";
			$gsres  = mysql_query($getsSql);
			$srow = mysql_fetch_object($gsres);
			$paid_amount = ($srow->paid_amount-$adjust_amount);
			$due 		 = ($srow->due+$adjust_amount); 
			$Usql="UPDATE ".DEVIT_VOUCHAR_TBL." SET paid_amount='$paid_amount',due='$due',status=0 WHERE voucher_no='".$adjust_ref."' 
			AND project_id='".$project_id."'";
			mysql_query($Usql);
		 }elseif($adjust_tbl_name=="purchase_return_receivable" && $adjust_type=="-"){
			//======= rollback previous my purchase return recievable amount =========			 
			$getdSql= "SELECT * FROM ".PURCHASE_RETURN_RECEIBAVLE_TBL." WHERE return_id='".$adjust_ref."' AND project_id='".$project_id."' 
			AND paid_amount >0";
			$gdres  = mysql_query($getdSql);
			$drow = mysql_fetch_object($gdres);
			$paid_amount = ($drow->paid_amount-$adjust_amount);
			$due 		 = ($drow->due+$adjust_amount); 
			$Usql="UPDATE ".PURCHASE_RETURN_RECEIBAVLE_TBL." SET paid_amount='$paid_amount',due='$due' WHERE return_id='".$adjust_ref."' 
			AND project_id='".$project_id."'";
			mysql_query($Usql);
		 }elseif($adjust_tbl_name=="sales_return_payble" && $adjust_type=="Payble ROA"){
			//======= delete previous advanced received payble amount =========			 
			$Usql="DELETE FROM ".SALES_RETURN_PAYBLE_TBL." WHERE voucher_no='".$adjust_ref."' AND project_id='".$project_id."'";
			mysql_query($Usql);
		 }
	  }
	}  
  }
  function rollbackSupplierPayble($voucher_no){
	$project_id = getFromSession('project_id');
	$getSql	="SELECT * FROM ".VOUCHER_ADJUST_HISTORY_TBL." WHERE voucher_no = '".$voucher_no."' AND project_id='".getFromSession('project_id')."'";
	$gres 	= mysql_query($getSql);
	if(mysql_num_rows($gres)>0){
	  while($row = mysql_fetch_object($gres)){
		 $adjust_tbl 	= $row->adjust_tbl; 
		 $adjust_ref 	= $row->adjust_ref;  
		 $adjust_amount = $row->adjust_amount; 
		 $adjust_type	= $row->adjust_type;
		 $adjust_tblArr = explode(".",$adjust_tbl);
		 $adjust_tbl_name = $adjust_tblArr[1];
		 //======= rollback previous purchase amount =========
		 if(($adjust_tbl_name==" purchase_master") && ($adjust_type=="+")){			 
			$getsSql= "SELECT * FROM ".PURCHASE_MASTER_TBL." WHERE voucher_no = '".$adjust_ref."' AND project_id='".$project_id."'";
			$gsres  = mysql_query($getsSql);
			$srow = mysql_fetch_object($gsres);
			$paid_amount = ($srow->paid_amount-$adjust_amount);
			$due 		 = ($srow->due+$adjust_amount); 
			$Usql="UPDATE ".PURCHASE_MASTER_TBL." SET paid_amount='$paid_amount',due='$due' WHERE voucher_no='".$adjust_ref."' 
			AND project_id='".$project_id."'";
			mysql_query($Usql);
		 }elseif(($adjust_tbl_name=="cs_delivery_product" || $adjust_tbl_name=="devit_vouchar") && ($adjust_type=="+")){
			//======= rollback previous opening payable amount =========			 
			$getsSql= "SELECT * FROM ".DEVIT_VOUCHAR_TBL." WHERE voucher_no = '".$adjust_ref."' AND project_id='".getFromSession('project_id')."'";
			$gsres  = mysql_query($getsSql);
			$srow = mysql_fetch_object($gsres);
			$paid_amount = ($srow->paid_amount-$adjust_amount);
			$due 		 = ($srow->due+$adjust_amount); 
			$Usql="UPDATE ".DEVIT_VOUCHAR_TBL." SET paid_amount='$paid_amount',due='$due',status=0 WHERE voucher_no='".$adjust_ref."' 
			AND project_id='".$project_id."'";
			mysql_query($Usql);
		 }elseif($adjust_tbl_name=="sales_return_payble" && $adjust_type=="+"){
			//======= rollback previous my advanced paid payable amount =========			 
			$getdSql= "SELECT * FROM ".SALES_RETURN_PAYBLE_TBL." WHERE return_id='".$adjust_ref."' AND project_id='".$project_id."' 
			AND paid_amount >0";
			$gdres  = mysql_query($getdSql);
			$drow = mysql_fetch_object($gdres);
			$paid_amount = ($drow->paid_amount-$adjust_amount);
			$due 		 = ($drow->due+$adjust_amount); 
			$Usql="UPDATE ".SALES_RETURN_PAYBLE_TBL." SET paid_amount='$paid_amount',due='$due' WHERE return_id='".$adjust_ref."' 
			AND project_id='".$project_id."'";
			mysql_query($Usql);
		 }elseif($adjust_tbl_name=="purchase_return_receivable" && $adjust_type=="Receibavle ROA"){
			//======= delete previous advanced received payble amount =========			 
			$Usql="DELETE FROM ".PURCHASE_RETURN_RECEIBAVLE_TBL." WHERE voucher_no='".$adjust_ref."' AND project_id='".$project_id."'";
			mysql_query($Usql);
		 }
	  }
	}  
  }
  function deletePreviousLedger($voucher_no){
	$project_id = getFromSession('project_id');
	$Jsql="DELETE FROM ".ACCOUNT_JOURNAL_TBL." WHERE voucher_no='".$voucher_no."' AND project_id='".$project_id."'";
	mysql_query($Jsql); 
	$Csql="DELETE FROM ".CONTRA_DETAILS_TBL." WHERE voucher_no='".$voucher_no."' AND project_id='".$project_id."'";
	mysql_query($Csql); 	
	$Hsql="DELETE FROM ".VOUCHER_ADJUST_HISTORY_TBL." WHERE voucher_no='".$voucher_no."' AND project_id='".$project_id."'";
	mysql_query($Hsql);
  }
  function getContraMasterInfo($id){		
	$project_id     = getFromSession('project_id');  
	$info           = array();    
	$info['table']  = CONTRA_MASTER_TBL.' cm,'.PROJECT_TBL.' p,'.CURRENCY_TBL.' c';	
	$info['fields'] = array('cm.contra_id','cm.voucher_no','cm.headtypes','cm.adjustment','cm.beddebts','cm.dr_account','p.project_name','p.location','p.project_logo','cm.dr_amount','cm.mode_of_payment','cm.currency','cm.vouchar_type','cm.transaction_type','cm.bank_journal','cm.description',"cm.created_date",'c.curr_symble','cm.created_by','cm.created_time','cm.cost_center');	
	$sql="cm.project_id = p.project_id AND cm.currency = c.currency_id AND cm.project_id = '".$project_id."' AND cm.contra_id = '$id'";							
	$info['where']  = $sql;	  	
    $info['groupby']= array("cm.contra_id");
	//$info['debug']= true;
	$res            =	select($info);
	if(count($res)){
		foreach($res as $i=>$v){
			$data[$i] = $v;             
		}
	}
	  //dumpVar($data);
	return $data[0];
   }   
   
   function deleteContraItem($details_id,$contra_id){	
	$project_id = getFromSession('project_id');
	$dsql= "SELECT * FROM ".CONTRA_DETAILS_TBL." WHERE BINARY details_id='".$details_id."' AND contra_id = '$contra_id' AND project_id='".$project_id."'";
	$dres = mysql_query($dsql);
	$dnum = mysql_num_rows($dres);
	if($dnum >0){
	 $drow  	= mysql_fetch_object($dres);
	 $achead	= $drow->cr_account;
	 $voucher_no 	= $drow->voucher_no;
	 //===== Delete Contra Voucher Item =====
	 $Jsql="DELETE FROM ".ACCOUNT_JOURNAL_TBL." WHERE sub_id ='".$achead."' AND voucher_no = '$contra_id' AND project_id='".$project_id."'";
	mysql_query($Jsql); 
	$Csql="DELETE FROM ".CONTRA_DETAILS_TBL." WHERE details_id='".$details_id."' AND contra_id = '$contra_id' AND project_id='".$project_id."'";
	mysql_query($Csql); 

	}
	
   }
     
   function getContraDetails($id) {
	$info           = array();    
	$info['table']  =  CONTRA_DETAILS_TBL.' cd,'.CURRENCY_TBL.' c';	
	$info['fields'] = array('cd.details_id','cd.contra_id','cd.headtypes','cd.dr_account','cd.cr_account','cd.cr_amount','cd.bank_name','cd.acc_no',
	'cd.check_no',"cd.check_issue_date",'cd.cheque_type','cd.vouchar_type','cd.consignee','c.curr_symble','cd.description','cd.created_by');		
	$sql="cd.currency = c.currency_id AND cd.contra_id = '$id'";		
	$info['where']  = $sql;
        $info['groupby']= array("cd.details_id");
	$info['orderby']= array("cd.details_id asc");
	//$info['debug']= true;
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
   //========= Contra List View  ========
   function getContraVoucherList($from,$to) { 
       if($from == "" && $to == ""){$from=0; $to=100;} 
	$date_from 	= formatDate(getRequest('date_from'));
	$date_to 	= formatDate(getRequest('date_to'));				
	$project_id     = getFromSession('project_id');
	$info           = array();    
	$info['table']  =  CONTRA_MASTER_TBL.' cm,'.CURRENCY_TBL.' c';	
	$info['fields'] = array('cm.`contra_id`','cm.headtypes','cm.dr_account','cm.dr_amount','cm.mode_of_payment','cm.vouchar_type','cm.bank_journal','cm.description',"DATE_FORMAT(cm.created_date,'%d %b %y' ) as created_date",'c.curr_symble','cm.created_by','cm.created_time');
	$sql="cm.currency = c.currency_id AND cm.project_id = '".$project_id."'";
	if($date_from!="" && $date_to ==""){
		$sql.=" AND cm.created_date >= '$date_from'";
	}elseif($date_from=="" && $date_to !=""){
		$sql.=" AND cm.created_date <= '$date_to'";
	}elseif($date_from!="" && $date_to !=""){
		$sql.=" AND cm.created_date BETWEEN '$date_from' AND '$date_to'";
	}	
	$info['where']  = $sql;
        $info['groupby']= array("cm.`contra_id`");
	$info['orderby']= array("cm.`contra_id` DESC LIMIT $from,$to");
	//$info['debug']= true;
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
  function  getTotalContraVoucherList(){
	$date_from 	= formatDate(getRequest('date_from'));
	$date_to 	= formatDate(getRequest('date_to'));				
	$project_id     = getFromSession('project_id');
	$info           = array();    
	$info['table']  = CONTRA_MASTER_TBL.' cm,'.CURRENCY_TBL.' c';	
	$info['fields'] = array('cm.contra_id','cm.dr_account','cm.dr_amount','cm.mode_of_payment','cm.vouchar_type','cm.bank_journal','cm.description',"DATE_FORMAT(cm.created_date,'%d %b %y' ) as created_date",'c.curr_symble','cm.created_by','cm.created_time');
	$sql="cm.currency = c.currency_id AND cm.project_id = '".$project_id."'";
	if($date_from!="" && $date_to ==""){
		$sql.=" AND cm.created_date >= '$date_from'";
	}elseif($date_from=="" && $date_to !=""){
		$sql.=" AND cm.created_date <= '$date_to'";
	}elseif($date_from!="" && $date_to !=""){
		$sql.=" AND cm.created_date BETWEEN '$date_from' AND '$date_to'";
	}	
	$info['where']  = $sql;
    	$info['groupby'] = array("cm.contra_id");
	$info['orderby'] = array("cm.created_time,cm.contra_id ASC");
	//$info['debug']  = true;
	$result         = select($info);
	$data           = array();
	$cnt = count($result);  
	if($cnt) {
		return $cnt;
	}else {
	  return 0;
	}   
   }
   	
   function saveAccountJournal($voucher_no,$sub_id,$transaction_type,$project_id,$description,$DR=NULL,$CR=NULL,$balance,$status,$created_date,$issue_date=NULL,$cost_center){
	$head_type	= getHeadType($sub_id);   $created_by = getFromSession('userid');
	$adjustment = getRequest('adjustment');	$beddebts = getRequest('beddebts');	 
	$sql = "INSERT INTO ".ACCOUNT_JOURNAL_TBL." (voucher_no,created_date,issue_date,sub_id,head_type,transaction_type,project_id,description,adjustment,beddebts,dr,cr,balance,status,created_by,cost_center) 
	 VALUES('".$voucher_no."','".$created_date."','".$issue_date."','".$sub_id."','".$head_type."','".$transaction_type."','".$project_id."','".$description."','".$adjustment."','".$beddebts."','".$DR."','".$CR."','".$balance."','".$status."','".$created_by."','".$cost_center."')";
	mysql_query($sql);
   }
   function saveRetailerJournal($voucher_no,$sub_id,$transaction_type,$project_id,$description,$DR=NULL,$CR=NULL,$balance,$status,$created_date,$issue_date=NULL){
	$head_type			= "Retailer";   $created_by = getFromSession('userid'); 
	$sql = "INSERT INTO ".ACCOUNT_JOURNAL_TBL." (voucher_no,created_date,issue_date,sub_id,head_type,transaction_type,project_id,description,dr,cr,balance,status,created_by) 
	 VALUES('".$voucher_no."','".$created_date."','".$issue_date."','".$sub_id."','".$head_type."','".$transaction_type."','".$project_id."','".$description."','".$DR."','".$CR."','".$balance."','".$status."','".$created_by."')";
	mysql_query($sql);
   }
   // ======== Create Voucher ID =======
   function createVoucharID()
   {
      $info = array();
      $info['table'] = DEVIT_VOUCHAR_TBL;
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
   
   function getSubAccHeadList(){
      $info            = array();
      $project_id 	   = getFromSession('project_id');
      $info['table']   = SUB_ACC_HEAD_TBL;

      $info['fields']  = array('sub_id', 'sub_head_name','head_details','head_type'); 	
      $info['where']   =  "project_id = '$project_id'"; 
      $info['orderby'] = array("sub_head_name ASC");
      $info['debug']   = false;
      $result          = select($info);
      //dBug($result);
      $data            = array();
      if(count($result))
      {
         foreach($result as $i=>$v)
         {
            $data[$i] = $v;             
         }
      }
      return $data;
   }

   function getCurrencyList()
   {
      $info            = array();
      $info['table']   = CURRENCY_TBL;
      //$info['fields'] = array('currency_id', 'name'); 
	  $info['orderby'] = array("currency_name ASC");
      $info['debug']   = false;
      $result          = select($info);
      //dBug($result);
      $data            = array();
      if(count($result))
      {
         foreach($result as $i=>$v)
         {
            $data[$i] = $v;             
         }
      }
      return $data;
   }
   function getHeadName($head_id){
		$project_id = getFromSession('project_id');
		$headName   = "";
		$acsql= "SELECT sub_head_name ,head_details FROM ".SUB_ACC_HEAD_TBL." WHERE BINARY sub_id  = '".$head_id."' AND project_id = '$project_id'";
		$acres = mysql_query($acsql);
		$acnum = mysql_num_rows($acres);
		if($acnum>0){
			$row = mysql_fetch_object($acres);
			 $headName= $row->sub_head_name;
			 if($row->head_details!=""){
				 $headName.="<br>".$row->head_details;
			}
		}else{
			$sql= "SELECT name ,address FROM ".SUPPLIER_TBL." WHERE BINARY supplier_code = '".$head_id."' AND project_id = '$project_id'";
			$res = mysql_query($sql);
			$num = mysql_num_rows($res);
			if($num>0){
				$row = mysql_fetch_object($res);
				$headName= $row->name;
				 if($row->address!=""){
					$headName.="<br>".$row->address;
				}
			}else{
				$sql= "SELECT product_name,product_desc FROM ".PRODUCT_TBL." WHERE BINARY product_id = '".$head_id."' AND project_id = '$project_id'";
				$res = mysql_query($sql);
				$num = mysql_num_rows($res);
				if($num>0){
					$row = mysql_fetch_object($res);
					$headName= $row->product_name;
					 if($row->product_desc!=""){
						 $headName.="<br>".$row->product_desc;
					}
				}else{
					$asql= "SELECT b.bank_name, c.bank_account_no FROM ".BANK_TBL." b, ".BANK_ACCOUNT_TBL." c WHERE b.bank_id=c.bank_code AND BINARY c.bank_account_no ='".$head_id."' 
					AND b.project_id = '$project_id'";
					$ares = mysql_query($asql);
					$anum = mysql_num_rows($ares);
					if($anum>0){
					$arow=mysql_fetch_object($ares);
					$headName=$arow->bank_name.", Acc No. ".$arow->bank_account_no;
					}
				}
			}
		}
		return $headName;
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
} // End class
?>
