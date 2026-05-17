<?php
class ContraVoucher
{
   
   function run()
   {         
      $cmd = getRequest('cmd');
      $u_t_id = getFromSession('u_type_id');
      if( ($u_t_id == 101) || ($u_t_id == 102) || ($u_t_id == 104)) //1 = admin 2 = Sales man
      {

      	switch ($cmd)
      	{
      	   case 'add'                	: $screen = $this->showEditor($msg); break;
   	   case 'add_old'               : $screen = $this->showEditorOld($msg); break;
      	   case 'edit'               	: $screen = $this->showEditor("Edit Page");    break;	
      	   case 'print_vouchar'         : $screen = $this->showPrintEditor($msg); break;
	   case 'get_temp_dtl' 		: $this->getTempDetails(trim(getRequest('item_id'))); break; 
	   case 'save_tmp'  		: $this->saveTempVoucher(); break;   
	   case 'deltemp'		: $this->delTempVoucher(); break;     
	   case 'save_vouchar'		: $this->saveVoucherOld(); break;
	   case 'approved_vouchar'	: $this->approvedVoucher(); break;
	   case 'approved_money_recipt'	: $this->approvedMoneyRecipt(); break;
	   case 'save_pending_vouchar'  : $this->savePendingVoucher(); break;
	   case 'delete_pending_vouchar': $this->deletePendingVoucher(); break;
	   case 'list'               	: $screen = $this->showList($msg);   break;
	   case 'check-mr' 		: $this->checkDuplicateMR(trim(getRequest('mr-number'))); break; 
      	   default                   	: $screen = $this->showEditor($msg); break;
      	}

      }else {
      	header("location:index.php?app=user_home&msg=You are not authorised !!!");
      }     	          
      return true;
  }  
 
  function checkDuplicateMR($mr_number){
	$chktmp= "SELECT * FROM ".TMP_GRVDETAILS_TBL." WHERE created_by = '".getFromSession('userid')."' 
	AND project_id='".getFromSession('project_id')."' AND tmp_grvid='".$_SESSION['tmp_grvid']."' AND description='$mr_number'";
	$restemp 	= mysql_query($chktmp);
	if(mysql_num_rows($restemp)>0){
		echo "1";
	}else{
		$chkcd= "SELECT * FROM ".CONTRA_DETAILS_TBL." WHERE project_id='".getFromSession('project_id')."' AND description='MR No. $mr_number' AND dealer_payment=1";
		$rescd 	= mysql_query($chkcd);
		if(mysql_num_rows($rescd)>0){
			echo "1";
		}else{
			echo "0";
		}
	}
	
  }
  function showList($msg = null) {  
	  $data                		= array();
	  $data['cmd']         		= getRequest('cmd');
	  $data['voucher_list']		= $this->getContraVoucherList(getRequest('from'),getRequest('to'));
	  $data['totalrecord']		= $this->getTotalContraVoucherList(); 
	  require_once(CONTRA_VOUCHER_SKIN_LIST); 
	  return $data[0];
  } 
  function showPrintEditor($msg = null) {   	  
	  $contra_id 	= getRequest('contra_id');  
	  if ($contra_id) {
         	$advArr 		= $this->getContraMasterInfo($contra_id);
         	$advArr 		= parseThisValue($advArr); 
		 $data   		= array_merge(array(), $advArr); 
		 $data['item_list']	= $this->getContraDetails($contra_id);
		 $data['message'] 	= $msg;
		 $data['cmd']     	= getRequest('cmd');
		 require_once(PRNIT_CONTRA_VOUCHER_SKIN);      
		 return true;
	 }
   }


	function savePendingVoucher()
    {
	if (ob_get_level()) ob_end_clean();
        mysql_query("START TRANSACTION;");

        $created_by = getFromSession('userid');
        $project_id = getFromSession('project_id');
        $tmp_grvid = $_SESSION['tmp_grvid'];
        $headtypes = $_SESSION['headtypes'];
        $dr_account = "";
        $cr_account = "";
        $mr_no = getRequest('mr_no');

        if (isset($tmp_grvid) && $tmp_grvid != "") {
            $requestdata = array();
            $requestdata = getUserDataSet(PENDING_CVMASTER_TBL);

            $chkcd = "SELECT * FROM " . TMP_GRVMASTER_TBL . " WHERE project_id='$project_id' AND created_by='$created_by' AND tmp_grvid='$tmp_grvid'";
            $rescd = mysql_query($chkcd);
            if (mysql_num_rows($rescd) > 0) {
                $query = mysql_fetch_object($rescd);

                $headtypes = $query->headtypes;
                $dr_account = $query->dr_account;
                $cr_account = $query->cr_account;

                $requestdata['dr_account'] = $dr_account;
                $requestdata['cr_account'] = $cr_account;
            }

            $requestdata['headtypes'] = $headtypes;
            $requestdata['mr_no'] = $mr_no;
            $requestdata['dr_amount'] = getRequest('totaldr_amount');
            $requestdata['mode_of_payment'] = getRequest('mode_of_payment');
            $requestdata['bank_journal'] = getRequest('bank_journal');
            $requestdata['currency'] = getRequest('currency');
            $requestdata['currencyName'] = getRequest('currencyName');
            $requestdata['invoice_no'] = "";
            $requestdata['created_date'] = formatDate(getRequest('created_date'));
            $requestdata['project_id'] = getFromSession('project_id');
            $requestdata['created_by'] = getFromSession('userid');
            $requestdata['vouchar_type'] = getRequest('vouchar_type');
            $requestdata['description'] = getRequest('details');
            $requestdata['adjustment'] = getRequest('adjustment');
            $requestdata['beddebts'] = getRequest('beddebts');
            $requestdata['cost_center'] = getRequest('cost_center');
	    $requestdata['collection_source'] = getRequest('collection_source');

            $info = array();
            $info['table'] = PENDING_CVMASTER_TBL;
            $info['data'] = $requestdata;
            //$info['debug']  	=  true;
            $resMaster = insert($info);

            // Prepare master data for the voucher snapshot
            $voucher_master_data = [
                'project_id' => $project_id,  // Project ID
                'mr_no' => $mr_no,  // Voucher Number
                'headtypes' => $headtypes,  // Head type
                'dr_account' => $dr_account,  // Debit Account
                'cr_account' => $cr_account,
                'dr_amount' => getRequest('totaldr_amount'),  // Debit amount
                'mode_of_payment' => getRequest('mode_of_payment'),  // Mode of payment
                'vouchar_type' => getRequest('vouchar_type'),  // Voucher type (from session)
                'bank_journal' => getRequest('bank_journal'),  // Bank journal status (Y/N)
                'invoice_no' => "",  // Transaction type (can be adjusted if needed)
                'currency' => getRequest('currency'),  // Currency code (e.g. USD)
                'currencyName' => getRequest('currencyName'),  // Currency name (e.g. US Dollar)
                'description' => getRequest('details'),  // Description of the voucher
                'created_by' => getFromSession('userid'),  // User who created the voucher (from session)
                'created_date' => formatDate(getRequest('created_date')),  // Created date (formatted)
                'adjustment' => getRequest('adjustment'),  // Adjustment amount (if any)
                'beddebts' => getRequest('beddebts'),  // Bad debts
                'cost_center' => getRequest('cost_center'),  // Cost center ID
		'collection_source' => getRequest('collection_source'),  // collection_source
                'is_money_recipt' => 0,  // Cost center ID
            ];

            // Prepare an array for voucher details data
            $voucher_details_data = [];

            $contra_id = "";
            if ($resMaster) {
                $contra_id = mysql_insert_id();

                if (isset($_FILES['attachment']) && $_FILES['attachment']['error'] == 0) {
                    $fileName = $this->voucherFileUpload($contra_id);

                    if (strpos($fileName, '.') !== false) {
                        $requestdata['attachment'] = $fileName;
                        $voucher_master_data['attachment'] = $fileName;

                        $info = array();
                        $info['table'] = PENDING_CVMASTER_TBL;
                        $info['data'] = $requestdata;
                        $info['where'] = "tmp_grvid ='$contra_id'";
                        update($info);
                    }
                }

                $getSql = "SELECT * FROM " . TMP_GRVDETAILS_TBL . " WHERE created_by = '$created_by' AND project_id='$project_id' AND tmp_grvid='$tmp_grvid'";
                $gres = mysql_query($getSql);
                if (mysql_num_rows($gres) > 0) {
                    while ($row = mysql_fetch_object($gres)) {
                        $requestdata = getUserDataSet(PENDING_CVDETAILS_TBL);

                        $requestdata['tmp_grvid'] = $contra_id;
                        $requestdata['project_id'] = $project_id;
                        $requestdata['headtypes'] = $row->headtypes;
                        $requestdata['dr_account'] = $row->dr_account;
                        $requestdata['currency'] = $row->currency;
                        $requestdata['currencyName'] = $row->currencyName;
                        $requestdata['cr_account'] = $row->cr_account;
                        $requestdata['cr_acname'] = $row->cr_acname;
                        $requestdata['cr_amount'] = $row->cr_amount;
                        $requestdata['bank_name'] = $row->bank_name;
                        $requestdata['acc_no'] = $row->acc_no;
                        $requestdata['check_no'] = $row->check_no;
                        $requestdata['check_issue_date'] = $row->check_issue_date;
                        $requestdata['cheque_type'] = $row->cheque_type;
                        $requestdata['vouchar_type'] = getRequest('vouchar_type');
                        $requestdata['consignee'] = $row->consignee;
                        $requestdata['consignee_name'] = $row->consignee_name;

                        if ($row->dealer_payment == 1) {
                            $requestdata['description'] = "MR No. " . $row->description;
                        } else {
                            $requestdata['description'] = $row->description;
                        }
                        $requestdata['created_by'] = $created_by;
                        $requestdata['dealer_payment'] = $row->dealer_payment;

                        $info = array();
                        $info['table'] = PENDING_CVDETAILS_TBL;
                        $info['data'] = $requestdata;
                        //$info['debug']  	=  true;
                        $res = insert($info);

                        $voucher_details_data[] = [
                            'tmp_grvid' => $contra_id,
                            'project_id' => $project_id,
                            'headtypes' => $row->headtypes,
                            'dr_account' => $row->dr_account,
                            'currency' => $row->currency,
                            'currencyName' => $row->currencyName,
                            'cr_account' => $row->cr_account,
                            'cr_acname' => $row->cr_acname,
                            'cr_amount' => $row->cr_amount,
                            'bank_name' => $row->bank_name,
                            'acc_no' => $row->acc_no,
                            'check_no' => $row->check_no,
                            'check_issue_date' => $row->check_issue_date,
                            'cheque_type' => $row->cheque_type,
                            'vouchar_type' => getRequest('vouchar_type'),
                            'consignee' => $row->consignee,
                            'consignee_name' => $row->consignee_name,
                            'description' => $requestdata['description'],
                            'dealer_payment' => $row->dealer_payment,
                            'created_by' => $created_by
                        ];

                    }// end while
                }// end if

                // Prepare the voucher snapshot
                $voucher_snapshot = [
                    'master' => $voucher_master_data,
                    'details' => $voucher_details_data
                ];

                // Encode the snapshot to JSON
                $requestdata['voucher_snapshot'] = json_encode($voucher_snapshot);

                $info = array();
                $info['table'] = PENDING_CVMASTER_TBL;
                $info['data'] = $requestdata;
                $info['where'] = "tmp_grvid ='$contra_id'";
                update($info);

               
		    $dsql = "DELETE FROM " . TMP_GRVDETAILS_TBL . " WHERE created_by = '$created_by' AND project_id='$project_id' AND tmp_grvid='$tmp_grvid'";
		    mysql_query($dsql);
		    $dmsql = "DELETE FROM " . TMP_GRVMASTER_TBL . " WHERE created_by='$created_by' AND project_id='$project_id' AND tmp_grvid='$tmp_grvid'";
		    mysql_query($dmsql);
		    $_SESSION['tmp_grvid'] = "";
		    $_SESSION['headtypes'] = "";
		    $_SESSION['Drheadtypes'] = "";
		    $_SESSION['vouchar_type'] = "";
		    $_SESSION['transaction_name'] = "";

		    mysql_query("COMMIT;");

		   // Send only the contra_id as a response
		    echo $contra_id;
		    exit();
                
            } else {
                mysql_query("ROLLBACK;");
                echo "error";
                exit();
            }
        }
	echo "error";
        exit();
    }

  
	function voucherFileUpload($maxId)
	{
	    // Directory to save the file
	    $saveDir = IMAGES_DIR.'/voucher/';
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
		$newFileName = "contra_voucher" . $maxId . '.' . $fileType;
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

   function approvedVoucher()
    {
        $ref_id = getRequest('ref_id');
        $project_id = getFromSession('project_id');
        if (empty($ref_id)) {
            $ref_id = 0;
        }

        if ($ref_id > 0) {
	    $this->saveVoucher($ref_id);
        }

        header("location:index.php?app=sales.report&cmd=pending.voucher");
        exit();
    }

    function approvedMoneyRecipt()
    {
        $ref_id = getRequest('ref_id');
        $project_id = getFromSession('project_id');
        if (empty($ref_id)) {
            $ref_id = 0;
        }

        if ($ref_id > 0) {
	    $this->saveVoucher($ref_id, true);
        }

        header("location:index.php?app=sales.report&cmd=pending.money.receipt");
        exit();
    }

    function deletePendingVoucher()
    {
        $project_id = getFromSession('project_id');
        $tmp_grvid = getRequest('ref_id');
        if (empty($tmp_grvid)) {
            $tmp_grvid = 0;
        }

        if ($tmp_grvid > 0) {
            $getSql = "SELECT * FROM " . PENDING_CVMASTER_TBL . " WHERE project_id='$project_id' AND tmp_grvid='$tmp_grvid' LIMIT 0 , 1";

            $gres = mysql_query($getSql);
            if (mysql_num_rows($gres) > 0) {
                $grow = mysql_fetch_object($gres);
                if ($grow->attachment != "") {
                    $attachment = $grow->attachment;
                    // Define the path to the file
                    $filePath = IMAGES_DIR . '/voucher/' . $attachment;
                    // Path to file

                    if ($grow->is_money_recipt == 1) {
                        $filePath = DOCUMENT_ROOT . '/../hera/assets/attachment/' . $attachment;
                    }

                    // Check if the file exists
                    if (file_exists($filePath)) {
                        // Unlink (delete) the file
                        unlink($filePath);
                    }
                }

                $redirectURL = "pending.voucher";
                if ($grow->is_money_recipt == 1) {
                    $redirectURL = "pending.money.receipt";
                }

                $dsql = "DELETE FROM " . PENDING_CVDETAILS_TBL . " WHERE project_id='$project_id' AND tmp_grvid='$tmp_grvid'";
                mysql_query($dsql);
                $dmsql = "DELETE FROM " . PENDING_CVMASTER_TBL . " WHERE project_id='$project_id' AND tmp_grvid='$tmp_grvid'";
                mysql_query($dmsql);

                $message = "Recoed has been deleted successfully";
                header("location:index.php?app=sales.report&cmd=$redirectURL&msg=$message");
                exit();
            }
        }
    }

    function saveVoucher($tmp_grvid, $moneyReceipt = false)
    {
	//exit();
        $project_id = getFromSession('project_id');
        $voucher_no = $this->createVoucharID($moneyReceipt);

        if ($voucher_no != "") {
            mysql_query("START TRANSACTION;");
            $getSql = "SELECT * FROM " . PENDING_CVMASTER_TBL . " WHERE project_id='$project_id' AND tmp_grvid='$tmp_grvid'";
            if ($moneyReceipt) {
                $getSql .= " AND is_money_recipt='1'";
            } else {
                $getSql .= " AND is_money_recipt='0'";
            }
            $getSql .= " LIMIT 0 , 1";

            $gres = mysql_query($getSql);
            if (mysql_num_rows($gres) > 0) {
                $grow = mysql_fetch_object($gres);

		if ($grow->dr_account == "") {
                    $getDSql = "SELECT * FROM " . PENDING_CVDETAILS_TBL . " WHERE project_id='$project_id' AND tmp_grvid='$tmp_grvid' AND headtypes='Dr' LIMIT 0 , 1";
                    $gDres = mysql_query($getDSql);
                    $drow = mysql_fetch_object($gDres);
                    $dr_account = $drow->dr_account;
		    $grow->dr_account = $dr_account;
		    $grow->headtypes = "Dr";
                }

		if ($grow->cr_account == "") {
		     $voucher_snapshot = json_decode($grow->voucher_snapshot, true);
		     $cr_account = isset($voucher_snapshot['master']['cr_account']) ? $voucher_snapshot['master']['cr_account'] : "";
		     $grow->cr_account = $cr_account;
		}

                if ($grow->dr_account != "") {
                    $vouchar_type = $grow->vouchar_type;
                    $drHeadTypes = getHeadType($grow->dr_account);
                    $_SESSION['Drheadtypes'] = $drHeadTypes;
                    if (($drHeadTypes == "Cash" || $drHeadTypes == "Bank") && ($vouchar_type == "Others Vouchar")) {
                        $_SESSION['vouchar_type'] = "Received Vouchar";
                        $_SESSION['transaction_name'] = "Received";
                    } elseif (($drHeadTypes != "Cash" && $drHeadTypes != "Bank") && ($vouchar_type == "Others Vouchar")) {
                        $_SESSION['vouchar_type'] = "Payment Vouchar";
                        $_SESSION['transaction_name'] = "Payment";
                    } else {
                        $_SESSION['vouchar_type'] = $vouchar_type;

                        if ($vouchar_type == "Journal Vouchar") {
                            $_SESSION['transaction_name'] = "Journal";
                        }
                    }
                }

                $dr_amount = $grow->dr_amount;
                $this->createVoucher($voucher_no, $_SESSION['vouchar_type'], $dr_amount, $grow);
print_r("<pre>");
print_r($voucher_no . "</br>");
print_r($grow);
print_r("<pre>");
exit();
		$contra_id = $this->saveContraMasterVouchar($voucher_no, $grow);
                $this->saveContraDetailsVouchar($contra_id, $voucher_no, $tmp_grvid, $grow, $moneyReceipt);
                mysql_query("COMMIT;");

                return true;
            }
        }
        return false;
    }

   function saveVoucherOld($moneyReceipt = false){	
	$voucher_no 	= $this->createVoucharID($moneyReceipt);
	if($voucher_no!=""){
	mysql_query("START TRANSACTION;");		
	$getSql	= "SELECT * FROM ".TMP_GRVDETAILS_TBL." WHERE created_by = '".getFromSession('userid')."' 
	AND project_id='".getFromSession('project_id')."' AND tmp_grvid='".$_SESSION['tmp_grvid']."' AND headtypes='Dr' LIMIT 0 , 1";
	$gres 	= mysql_query($getSql); $grow 	=  mysql_fetch_object($gres);
	if($grow->dr_account!=""){		
		$_SESSION['Drheadtypes'] = getHeadType($grow->dr_account);					
		if((getHeadType($grow->dr_account)=="Cash" || getHeadType($grow->dr_account)=="Bank") && (getRequest('vouchar_type')=="Others Vouchar")){
		$_SESSION['vouchar_type'] 		= "Received Vouchar"; $_SESSION['transaction_name'] 	= "Received";
		}elseif((getHeadType($grow->dr_account)!="Cash" && getHeadType($grow->dr_account)!="Bank") && (getRequest('vouchar_type')=="Others Vouchar")){
		$_SESSION['vouchar_type'] = "Payment Vouchar"; 		$_SESSION['transaction_name'] = "Payment";	
		}else{
		$_SESSION['vouchar_type'] = getRequest('vouchar_type');
		}
	}

	$dr_amount = getRequest('totaldr_amount');
	$this->createVoucherOld($voucher_no,$_SESSION['vouchar_type'],$dr_amount);
	$contra_id 	= $this->saveContraMasterVoucharOld($voucher_no);	
	$this->saveContraDetailsVoucharOld($contra_id,$voucher_no); 
	mysql_query("COMMIT;");
	}
	if($contra_id!=""){ echo $contra_id;
	//header("location:index.php?app=contra.voucher.new&cmd=print_vouchar&contra_id=".$contra_id);	
	}else{
	header("location:index.php?app=contra.voucher.new&cmd=add");
	}
   }

   function saveContraMasterVouchar($voucher_no, $master)
    {
        $requestdata = array();
        $requestdata = getUserDataSet(CONTRA_MASTER_TBL);
        $requestdata['mr_no'] = $master->mr_no;
        //$requestdata['headtypes'] = $master->headtypes;
        $requestdata['dr_account'] = $master->dr_account;
        $requestdata['dr_amount'] = $master->dr_amount;
        $requestdata['cr_amount'] = $master->dr_amount;
        $requestdata['mode_of_payment'] = $master->mode_of_payment;
        $requestdata['bank_journal'] = $master->bank_journal;
        $requestdata['currency'] = $master->currency;
        $requestdata['voucher_no'] = $voucher_no;
        $requestdata['adjustment'] = $master->adjustment;
        $requestdata['beddebts'] = $master->beddebts;
        $requestdata['created_date'] = $master->created_date;
        $requestdata['project_id'] = getFromSession('project_id');
        $requestdata['created_by'] = getFromSession('userid');
        $requestdata['vouchar_type'] = isset($_SESSION['vouchar_type']) ? $_SESSION['vouchar_type'] : $master->vouchar_type;
        //$requestdata['transaction_name'] = isset($_SESSION['transaction_name']) ? $_SESSION['transaction_name'] : "";
        $requestdata['description'] = $master->description;
        $requestdata['attachment'] = $master->attachment;
        $requestdata['cr_account'] = $master->cr_account;
        $requestdata['invoice_no'] = $master->invoice_no;
        $requestdata['voucher_snapshot'] = $master->voucher_snapshot;
        $requestdata['is_money_recipt'] = $master->is_money_recipt;
        $requestdata['cost_center'] = $master->cost_center;

        $info = array();
        $info['table'] = CONTRA_MASTER_TBL;
        $info['data'] = $requestdata;
        //$info['debug']  	=  true;
        $res = insert($info);
        if ($res) {
            return mysql_insert_id();
        } else {
            return false;
        }

    }
   
   function saveContraMasterVoucharOld($voucher_no){
	$requestdata = array();
	$requestdata = getUserDataSet(CONTRA_MASTER_TBL);
	$requestdata['dr_amount'] 		= getRequest('totaldr_amount');
	$requestdata['cr_amount'] 		= getRequest('totalcr_amount');
	$requestdata['mode_of_payment'] 	= getRequest('mode_of_payment');
	$requestdata['bank_journal']		= getRequest('bank_journal');		
	$currency = getRequest('currency');
	$currencyArr = explode("###",$currency);
	$requestdata['currency'] 		= $currencyArr[0];			
	$requestdata['voucher_no'] 		= $voucher_no;
	$requestdata['created_date']		= formatDate(getRequest('created_date'));		
	$requestdata['project_id'] 		= getFromSession('project_id');		
	$requestdata['created_by'] 		= getFromSession('userid');		
	$requestdata['vouchar_type'] 		= $_SESSION['vouchar_type']; 
	$requestdata['transaction_name']	= $_SESSION['transaction_name'];
	$requestdata['description'] 		= getRequest('details');
	$info        		=  array();
	$info['table']	= CONTRA_MASTER_TBL;
	$info['data'] 	= $requestdata;     
	//$info['debug']  	=  true;
	$res = insert($info);
	if($res){
	return mysql_insert_id();		
	
	}else{ return false;}
			
    }

    function createVoucher($voucher_no, $vouchar_type, $dr_amount, $master)
    {
        // ===Start Create Voucher===
        $payment_mode = $master->mode_of_payment;
        $headtypes = "Contra Voucher";
        $dr_account = $master->dr_account;
        $cr_account = $master->cr_account;
        $cr_amount = $dr_amount;
        $created_date = $master->created_date;
        $bank_name = "";
        $acc_no = "";
        $check_no = "";
        $check_date = "";
        $description = "Contra Voucher";
	$collection_source = $master->collection_source;
exit();
        $dvres = $this->saveDebitVouchar($voucher_no, $payment_mode, $vouchar_type, $dr_account, $dr_amount, $cr_account, $created_date, $bank_name, $acc_no, $check_no, $check_date,$collection_source);
        if ($dvres) {
            $this->saveCreditVouchar($voucher_no, $payment_mode, $vouchar_type, $dr_account, $cr_account, $cr_amount, $created_date, $bank_name, $acc_no, $check_no, $check_date, $description,$collection_source);
            return true;
        }
        // ===End Create Voucher===
    }

    function createVoucherOld($voucher_no,$vouchar_type,$dr_amount){
	// ===Start Create Voucher===
	$payment_mode 	= getRequest('mode_of_payment');
	$headtypes		= "Contra Voucher";
	$dr_account		= "";
	$cr_account		= "";
	$cr_amount 		= $dr_amount;
	$created_date	= formatDate(getRequest('created_date'));
	$bank_name		= "";
	$acc_no			= "";
	$check_no		= "";
	$check_date		= "";
	$description	= "Contra Voucher";
	
	$dvres = $this->saveDebitVouchar($voucher_no,$payment_mode,$vouchar_type,$dr_account,$dr_amount,$cr_account,$created_date,$bank_name,$acc_no,$check_no,$check_date);
	if($dvres){
	$this->saveCreditVouchar($voucher_no,$payment_mode,$vouchar_type,$dr_account,$cr_account,$cr_amount,$created_date,$bank_name,$acc_no,$check_no,$check_date,$description);	
	return true;
	}
	// ===End Create Voucher===		
    }

    function saveContraDetailsVouchar($contra_id, $voucher_no, $tmp_grvid, $master, $moneyReceipt)
    {
        require_once(CLASS_DIR . '/common.list.class.php');
        $clistApp = new CommonList();
        $requestdata = array();

        $created_by = getFromSession('userid');
        $project_id = getFromSession('project_id');

        $getSql = "SELECT * FROM " . PENDING_CVDETAILS_TBL . " WHERE project_id='$project_id' AND tmp_grvid='$tmp_grvid'";
        $gres = mysql_query($getSql);
        if (mysql_num_rows($gres) > 0) {
            while ($row = mysql_fetch_object($gres)) {
                $requestdata = getUserDataSet(CONTRA_DETAILS_TBL);
                $requestdata['contra_id'] = $contra_id;
                $requestdata['voucher_no'] = $voucher_no;
                $requestdata['project_id'] = $project_id;
                $requestdata['headtypes'] = $row->headtypes;
                if ($row->headtypes == "Dr") {
                    $requestdata['dr_account'] = $row->dr_account;
                }
                $requestdata['currency'] = $row->currency;
                $requestdata['cr_account'] = $row->cr_account;
                $requestdata['cr_amount'] = $row->cr_amount;
                $requestdata['bank_name'] = $row->bank_name;
                $requestdata['acc_no'] = $row->acc_no;
                $requestdata['check_no'] = $row->check_no;
                $requestdata['check_issue_date'] = $row->check_issue_date;
                $requestdata['cheque_type'] = $row->cheque_type;
                $requestdata['vouchar_type'] = isset($_SESSION['vouchar_type']) ? $_SESSION['vouchar_type'] : $master->vouchar_type;
                $requestdata['consignee'] = $row->consignee;
                $requestdata['transaction_name'] = isset($_SESSION['transaction_name']) ? $_SESSION['transaction_name'] : "";

                if ($row->dealer_payment == 1) {
                    $requestdata['description'] = "MR No. " . $row->description;
                } else {
                    $requestdata['description'] = $row->description;
                }
                $requestdata['dealer_payment'] = $row->dealer_payment;
                $requestdata['created_by'] = getFromSession('userid');

                $info = array();
                $info['table'] = CONTRA_DETAILS_TBL;
                $info['data'] = $requestdata;
                //$info['debug']  	=  true;
                $res = insert($info);

                $adjustment = isset($master->adjustment) ? $master->adjustment : "";
                $beddebts = isset($master->adjustment) ? $master->beddebts : "";

                if ($res) {
                    $dealer_payment = $row->dealer_payment;
                    $description = $row->description;
                    if ($dealer_payment == 1) {
                        $description = "MR No. " . $description;
                    }
                    $created_date = $master->created_date;
                    $issue_date = $row->check_issue_date;
                    $vouchar_type = isset($_SESSION['vouchar_type']) ? $_SESSION['vouchar_type'] : $master->vouchar_type;
                    $transaction_type = isset($_SESSION['transaction_name']) ? $_SESSION['transaction_name'] : "";

                    if ($row->consignee != "") {
                        $consignee = $row->consignee;
                        $CrAmount = $row->cr_amount;
                        $totalCR = $this->getTotalCreditAmount($consignee, getFromSession('project_id'));
                        $totalDR = $this->getTotalDebitAmount($consignee, getFromSession('project_id'));
                        $balance = ($totalDR - ($totalCR + $CrAmount));
                        $this->saveRetailerJournal($voucher_no, $consignee, $transaction_type, $project_id, $description, 0, $CrAmount, $balance, 1, $created_date, $issue_date);
                        $CrAmount = 0;
                        $balance = 0;
                    }

                    if ($row->headtypes == "Dr") {
                        $dr_account = $row->dr_account;
                        $DrAmount = $row->cr_amount;
                        //======= Dr Account ======
                        $totalPartyCR = $this->getTotalCreditAmount($dr_account, getFromSession('project_id'));
                        $totalPartyDR = $this->getTotalDebitAmount($dr_account, getFromSession('project_id'));
                        $PartyBalance = (($totalPartyDR + $DrAmount) - $totalPartyCR);
                        $this->saveAccountJournal($voucher_no, $dr_account, $transaction_type, $project_id, $description, $DrAmount, 0, $PartyBalance, 0, $created_date, $issue_date, $adjustment, $beddebts);
                        //=========== Cr Capital =======
                        $HeadType = getHeadType($dr_account);
                        if ($HeadType == "Administrative Cost") {
                            $capital_head = $clistApp->getCapitalId(getFromSession('project_id'));
                            if ($capital_head) {
                                /*
                                $totalCapitalCR  = $this->getTotalCreditAmount($capital_head,getFromSession('project_id'));
                                $totalCapitalDR  = $this->getTotalDebitAmount($capital_head,getFromSession('project_id'));
                                $Capitalbalance  = ($totalCapitalDR-($totalCapitalCR+$DrAmount));
                                $this->saveAccountJournal($voucher_no,$capital_head,$transaction_type,$project_id,$description,0,$DrAmount,$Capitalbalance,0,$created_date, $adjustment, $beddebts);
                                */
                            }

                        }
                        //if($vouchar_type!="Payable Vouchar" && $vouchar_type!="Recievable Vouchar"){
                        $HeadType = $_SESSION['Drheadtypes'];
                        if ($HeadType != "Cash" || $HeadType != "Bank") {
                            require_once(CLASS_DIR . '/advanced_payment.class.php');
                            $advpApp = new AdvancedPayment();
                            $head_type = getHeadType($dr_account);
                            $account_head = $dr_account;
                            if ($head_type == "Supplier") {
                                $advpApp->adjustSupplierPayble($voucher_no, $dr_account, $DrAmount, $created_date);
                            } elseif ($head_type == "Customer") {
                                $advpApp->adjustCustomerPayble($voucher_no, $dr_account, $DrAmount, $created_date);
                            }
                        }
                        //}//end vouchar_type

                    } elseif ($row->headtypes == "Cr") {
                        //========= Cr Account ========
                        $cr_account = $row->cr_account;
                        $CrAmount = $row->cr_amount;
                        $totalCR = $this->getTotalCreditAmount($cr_account, getFromSession('project_id'));
                        $totalDR = $this->getTotalDebitAmount($cr_account, getFromSession('project_id'));
                        $balance = ($totalDR - ($totalCR + $CrAmount));
                        $this->saveAccountJournal($voucher_no, $cr_account, $transaction_type, $project_id, $description, 0, $CrAmount, $balance, 0, $created_date, $issue_date, $adjustment, $beddebts);

                        //if($vouchar_type!="Payable Vouchar" && $vouchar_type!="Recievable Vouchar"){
                        $HeadType = $_SESSION['Drheadtypes'];
                        if ($HeadType == "Cash" || $HeadType == "Bank") {
                            require_once(CLASS_DIR . '/general_vouchar.class.php');
                            $gvApp = new GeneralVouchar();
                            $head_type = getHeadType($cr_account);
                            if ($head_type == "Customer") {
                                $gvApp->adjustCustomerReceibavle($cr_account, $voucher_no, $CrAmount, $created_date);
                            } elseif ($head_type == "Supplier") {
                                $gvApp->adjustSupplierReceibavle($cr_account, $voucher_no, $CrAmount, $created_date);
                            }

                            if ($head_type == "Customer") {
                                $Csql = "SELECT mobile,att_mobile1,sub_head_name FROM " . SUB_ACC_HEAD_TBL . " WHERE sub_id ='" . $cr_account . "' AND project_id = '$project_id'";
                                $Crow = mysql_fetch_object(mysql_query($Csql));
                                if (trim($Crow->mobile) != "" && trim($Crow->att_mobile1) != "") {
                                    $recipients = $Crow->mobile . "," . $Crow->att_mobile1;
                                } elseif (trim($Crow->mobile) != "" && trim($Crow->att_mobile1) == "") {
                                    $recipients = $Crow->mobile;
                                } elseif (trim($Crow->mobile) == "" && trim($Crow->att_mobile1) != "") {
                                    $recipients = $Crow->att_mobile1;
                                } else {
                                    $recipients = "";
                                }
                            } elseif ($head_type == "Supplier") {
                                $Csql = "SELECT mobile,name as sub_head_name FROM " . SUPPLIER_TBL . " WHERE sub_id ='" . $cr_account . "' AND project_id = '$project_id'";
                                $Crow = mysql_fetch_object(mysql_query($Csql));
                                $recipients = $Crow->mobile;
                            }
                            if ($recipients != "") {
                                if ($balance > 0) {
                                    $LastPartyBalance = $balance . " Dr";
                                } else {
                                    $LastPartyBalance = abs($balance) . " Cr";
                                }
                                $sms_text = "Dear sir, We have received " . $CrAmount . " TK. From party  code " . $Crow->sub_head_name;
                                //$this->sendSMS(COMPANY_NAME, $recipients, $sms_text);
				require_once(CLASS_DIR . '/common.list.class.php');
                                  $response = (new CommonList())->sendSMS($recipients, $sms_text);
                            }

                        }
                        //}//end vouchar_type

                        //=========== Dr Capital =======
                        $collection_source = isset($master->collection_source) && !empty($master->collection_source) ? $master->collection_source : "Others";
                        if ($collection_source != "Others") {

                            $capital_head = $clistApp->getCapitalId(getFromSession('project_id'));
                            if ($capital_head) {
                                /*
                                $totalCapitalCR  = $this->getTotalCreditAmount($capital_head,getFromSession('project_id'));
                                $totalCapitalDR  = $this->getTotalDebitAmount($capital_head,getFromSession('project_id'));
                                $Capitalbalance  = (($totalCapitalDR+$CrAmount)-$totalCapitalCR);

                                $this->saveAccountJournal($voucher_no,$capital_head,$transaction_type,$project_id,$description,$CrAmount,0,$Capitalbalance,0,$created_date, $adjustment, $beddebts);
                                */
                            }


                            if ($collection_source == "Servicing") {
                                $rsql = "SELECT warranty_id,service_bill,paid_amount,due FROM " . WARRANTY_TBL . " WHERE customer_id='" . $cr_account . "' AND due >0";
                                $rres = mysql_query($rsql);
                                while ($srow = mysql_fetch_object($rres)) {
                                    $warranty_id = $srow->warranty_id;
                                    if ($CrAmount >= $srow->due) {
                                        $CrAmount = $CrAmount - $srow->due;
                                        $totalPaidAmount = $srow->paid_amount + $srow->due;
                                        if ($totalPaidAmount == $srow->service_bill) {
                                            $pusql = "UPDATE " . WARRANTY_TBL . " SET paid_amount='" . $totalPaidAmount . "',due='0' WHERE warranty_id='$warranty_id'";
                                            mysql_query($pusql);
                                        }
                                    } elseif ($CrAmount < $srow->due) {
                                        $presentDue = $srow->due - $CrAmount;
                                        $PaidAmount = $srow->paid_amount + $CrAmount;
                                        if ($PaidAmount < $srow->service_bill) {
                                            $pusql2 = "UPDATE " . WARRANTY_TBL . " SET paid_amount='" . $PaidAmount . "',due='$presentDue' WHERE warranty_id='$warranty_id'";
                                            mysql_query($pusql2);
                                        }
                                    }
                                }// end while
                            }
                        } // end collection source

                    }// end headtype Cr

                    //==== Start Adjust Sales/Purchase =====

                    $HeadType = $_SESSION['Drheadtypes'];
                    if ($HeadType == "Cash" || $HeadType == "Bank") {
                        $cr_account = $row->cr_account;
                        $CrAmount = $row->cr_amount;
                        //==== Sales Collection ======
                        $head_type = getHeadType($cr_account);
                        if ($head_type == "Customer" || $head_type == "Supplier") {
                            $this->adjustACReceibavle($voucher_no, $CrAmount, $created_date);
                        }
                    } elseif ($HeadType != "Cash" || $HeadType != "Bank") {
                        $dr_account = $row->dr_account;
                        $DrAmount = $row->cr_amount;
                        //==== Purchase Payment ======
                        $head_type = getHeadType($dr_account);
                        if ($head_type == "Customer" || $head_type == "Supplier") {
                            $this->adjustACPayable($voucher_no, $DrAmount, $created_date);
                        }
                    }
                }// save
            }// end while

        }// end if

        if ($res) {
            if ($moneyReceipt) {
                $requestdata['status'] = '1';
                $info = array();
                $info['table'] = PENDING_CVMASTER_TBL;
                $info['data'] = $requestdata;
                $info['where'] = "tmp_grvid ='$tmp_grvid'";
                update($info);
            } else {
                $dsql = "DELETE FROM " . PENDING_CVDETAILS_TBL . " WHERE project_id='$project_id' AND tmp_grvid='$tmp_grvid'";
                mysql_query($dsql);
                $dmsql = "DELETE FROM " . PENDING_CVMASTER_TBL . " WHERE project_id='$project_id' AND tmp_grvid='$tmp_grvid'";
                mysql_query($dmsql);
            }

            $_SESSION['tmp_grvid'] = "";
            $_SESSION['headtypes'] = "";
            $_SESSION['Drheadtypes'] = "";
            $_SESSION['vouchar_type'] = "";
            $_SESSION['transaction_name'] = "";
        }

    }

    function saveContraDetailsVoucharOld($contra_id,$voucher_no){	
	 require_once(CLASS_DIR.'/common.list.class.php');	
	 $clistApp = new CommonList();
	 $requestdata 				= array();	
	
	 $getSql= "SELECT * FROM ".TMP_GRVDETAILS_TBL." WHERE created_by = '".getFromSession('userid')."' 
	AND project_id='".getFromSession('project_id')."' AND tmp_grvid='".$_SESSION['tmp_grvid']."'";
	 $gres 	= mysql_query($getSql);
	 if(mysql_num_rows($gres)>0){
	  while($row = mysql_fetch_object($gres)){
		$requestdata = getUserDataSet(CONTRA_DETAILS_TBL);		
		$requestdata['contra_id'] 	= $contra_id;	
		$requestdata['voucher_no'] 	= $voucher_no;				
		$requestdata['project_id'] 	= getFromSession('project_id');	
		$requestdata['headtypes'] 	= $row->headtypes;	
		if($row->headtypes=="Dr"){	
		$requestdata['dr_account'] 	= $row->dr_account;
		}
		$requestdata['currency'] 	= $row->currency;		
		$requestdata['cr_account']	= $row->cr_account;
		$requestdata['cr_amount'] 	= $row->cr_amount;
		$requestdata['bank_name'] 	= $row->bank_name;
		$requestdata['acc_no'] 		= $row->acc_no;
		$requestdata['check_no'] 	= $row->check_no;
		$requestdata['check_issue_date']= $row->check_issue_date;
		$requestdata['cheque_type'] 	= $row->cheque_type;
		$requestdata['vouchar_type'] 	= $_SESSION['vouchar_type']; 		
		$requestdata['consignee']	= $row->consignee;
		$requestdata['transaction_name']= $_SESSION['transaction_name'];
		if($row->dealer_payment==1){
		$requestdata['description'] 	= "MR No. ".$row->description;
		}else{	
		$requestdata['description'] 	= $row->description;
		}
		$requestdata['dealer_payment'] 	= $row->dealer_payment;
		$requestdata['created_by'] 	= getFromSession('userid');
		
		$info        		=  array();
		$info['table']		= CONTRA_DETAILS_TBL;
		$info['data'] 		= $requestdata;
		//$info['debug']  	=  true;   
		$res = insert($info);		
		if($res){
			$dealer_payment = $row->dealer_payment;			
			$description    = $row->description;
			if($dealer_payment==1){
			$description = "MR No. ".$description;
			}
			$created_date   = formatDate(getRequest('created_date'));
			$issue_date 	= $row->check_issue_date;			
			$vouchar_type   = $_SESSION['vouchar_type']; 
			$transaction_type= $_SESSION['transaction_name'];
			$project_id	 = getFromSession('project_id');
			
			if($row->consignee !=""){
			$consignee= $row->consignee;
			$CrAmount = $row->cr_amount;
			$totalCR  = $this->getTotalCreditAmount($consignee,getFromSession('project_id'));
			$totalDR  = $this->getTotalDebitAmount($consignee,getFromSession('project_id'));					 
			$balance  = ($totalDR-($totalCR+$CrAmount));					 
			$this->saveRetailerJournal($voucher_no,$consignee,$transaction_type,$project_id,$description,0,$CrAmount,$balance,1,$created_date,$issue_date);
			$CrAmount =0; $balance=0;	
			}
			
			if($row->headtypes=="Dr"){	
			$dr_account    = $row->dr_account;
			$DrAmount      = $row->cr_amount;
			//======= Dr Account ======	 
			$totalPartyCR  = $this->getTotalCreditAmount($dr_account,getFromSession('project_id'));
			$totalPartyDR  = $this->getTotalDebitAmount($dr_account,getFromSession('project_id'));
			$PartyBalance  = (($totalPartyDR+$DrAmount)-$totalPartyCR);
			$this->saveAccountJournal($voucher_no,$dr_account,$transaction_type,$project_id,$description,$DrAmount,0,$PartyBalance,0,$created_date,$issue_date);	
			//=========== Cr Capital =======
			$HeadType 		  = getHeadType($dr_account);  
			if($HeadType=="Administrative Cost"){
			 $capital_head    = $clistApp->getCapitalId(getFromSession('project_id'));
			 if($capital_head){			 
			 /*			 
			 $totalCapitalCR  = $this->getTotalCreditAmount($capital_head,getFromSession('project_id'));
			 $totalCapitalDR  = $this->getTotalDebitAmount($capital_head,getFromSession('project_id'));
			 $Capitalbalance  = ($totalCapitalDR-($totalCapitalCR+$DrAmount));					 
			 $this->saveAccountJournal($voucher_no,$capital_head,$transaction_type,$project_id,$description,0,$DrAmount,$Capitalbalance,0,$created_date);
			 */
			 }	

			}
			//if($vouchar_type!="Payable Vouchar" && $vouchar_type!="Recievable Vouchar"){ 
			$HeadType 		  = $_SESSION['Drheadtypes'];
			if($HeadType!="Cash" || $HeadType!="Bank"){
				require_once(CLASS_DIR.'/advanced_payment.class.php');	
				$advpApp 	= new AdvancedPayment();
				$head_type 	= getHeadType($dr_account);
				$account_head 	= $dr_account;
				if($head_type=="Supplier"){
					$advpApp->adjustSupplierPayble($voucher_no,$dr_account,$DrAmount,$created_date);
				}elseif($head_type=="Customer"){		
					$advpApp->adjustCustomerPayble($voucher_no,$dr_account,$DrAmount,$created_date);
				}	
			}
			//}//end vouchar_type
			
			}elseif($row->headtypes=="Cr"){	
			//========= Cr Account ========
			$cr_account= $row->cr_account;
			$CrAmount  = $row->cr_amount;
			$totalCR   = $this->getTotalCreditAmount($cr_account,getFromSession('project_id'));
			$totalDR   = $this->getTotalDebitAmount($cr_account,getFromSession('project_id'));
			$balance   = ($totalDR-($totalCR+$CrAmount));					 
			$this->saveAccountJournal($voucher_no,$cr_account,$transaction_type,$project_id,$description,0,$CrAmount,$balance,0,$created_date,$issue_date);
			
			//if($vouchar_type!="Payable Vouchar" && $vouchar_type!="Recievable Vouchar"){ 
			$HeadType 		  = $_SESSION['Drheadtypes'];
			if($HeadType=="Cash" || $HeadType=="Bank"){
				require_once(CLASS_DIR.'/general_vouchar.class.php');	
				$gvApp 	    = new GeneralVouchar();
				$head_type  = getHeadType($cr_account);
				if($head_type=="Customer"){
				$gvApp->adjustCustomerReceibavle($cr_account,$voucher_no,$CrAmount,$created_date);
				}elseif($head_type=="Supplier"){
				$gvApp->adjustSupplierReceibavle($cr_account,$voucher_no,$CrAmount,$created_date);
				}
				
				if($head_type=="Customer"){
				 $Csql = "SELECT mobile,att_mobile1,sub_head_name FROM ".SUB_ACC_HEAD_TBL." WHERE sub_id ='".$cr_account."' AND project_id = '$project_id'";
	 			 $Crow = mysql_fetch_object(mysql_query($Csql));
				 if(trim($Crow->mobile)!="" && trim($Crow->att_mobile1)!=""){
				 $recipients	= $Crow->mobile.",".$Crow->att_mobile1;
				 }elseif(trim($Crow->mobile)!="" && trim($Crow->att_mobile1)==""){
				 $recipients	= $Crow->mobile;
				 }elseif(trim($Crow->mobile)=="" && trim($Crow->att_mobile1)!=""){
				 $recipients	= $Crow->att_mobile1;
				 }else{
				 $recipients	= "";
				 }					
				}elseif($head_type=="Supplier"){
				 $Csql = "SELECT mobile,name as sub_head_name FROM ".SUPPLIER_TBL." WHERE sub_id ='".$cr_account."' AND project_id = '$project_id'";								
				 $Crow = mysql_fetch_object(mysql_query($Csql));		 
				 $recipients	= $Crow->mobile;
				}
				if($recipients !=""){
				  if($balance >0){
					$LastPartyBalance = $balance." Dr";
				  }else{
					 $LastPartyBalance = abs($balance)." Cr";
				  }
				  $sms_text = "Dear sir, We have received ".$CrAmount." TK. From party  code ".$Crow->sub_head_name.". (" . COMPANY_NAME . ")";
			 	  //$this->sendSMS(COMPANY_NAME,$recipients,$sms_text);
				  require_once(CLASS_DIR . '/common.list.class.php');
                                  $response = (new CommonList())->sendSMS($recipients, $sms_text);
				}
					
			}
			//}//end vouchar_type
			
			//=========== Dr Capital =======
			$collection_source = getRequest('collection_source');
			if($collection_source !="Others"){
			 
			 $capital_head 	  = $clistApp->getCapitalId(getFromSession('project_id'));
			 if($capital_head){
			 /*	
			 $totalCapitalCR  = $this->getTotalCreditAmount($capital_head,getFromSession('project_id'));
			 $totalCapitalDR  = $this->getTotalDebitAmount($capital_head,getFromSession('project_id'));
			 $Capitalbalance  = (($totalCapitalDR+$CrAmount)-$totalCapitalCR);	 
			 
			 $this->saveAccountJournal($voucher_no,$capital_head,$transaction_type,$project_id,$description,$CrAmount,0,$Capitalbalance,0,$created_date);
			 */
			 }
			 

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

			$HeadType = $_SESSION['Drheadtypes'];
			if($HeadType=="Cash" || $HeadType=="Bank"){				
				$cr_account = $row->cr_account;
				$CrAmount   = $row->cr_amount;
				//==== Sales Collection ======				
				$head_type  = getHeadType($cr_account);
				if($head_type=="Customer" || $head_type=="Supplier"){
				$this->adjustACReceibavle($voucher_no,$CrAmount,$created_date);
				}	
			}elseif($HeadType !="Cash" || $HeadType !="Bank"){
				$dr_account = $row->dr_account;
				$DrAmount   = $row->cr_amount;
				//==== Purchase Payment ======	
				$head_type  = getHeadType($dr_account);
				if($head_type=="Customer" || $head_type=="Supplier"){
				$this->adjustACPayable($voucher_no,$DrAmount,$created_date);
				}
			}			
			

		}// save
	  }// end while 
		
    }// end if
	
    if($res){ 
	 $dsql = "DELETE FROM ".TMP_GRVDETAILS_TBL." WHERE created_by = '".getFromSession('userid')."' 
	 AND project_id='".getFromSession('project_id')."' AND tmp_grvid='".$_SESSION['tmp_grvid']."'";
	 mysql_query($dsql);
	 $dmsql="DELETE FROM ".TMP_GRVMASTER_TBL." WHERE created_by='".getFromSession('userid')."' AND project_id='".getFromSession('project_id')."' 
	 AND tmp_grvid='".$_SESSION['tmp_grvid']."'";
	 mysql_query($dmsql);
	 $_SESSION['tmp_grvid']=""; $_SESSION['headtypes']=""; $_SESSION['Drheadtypes']=""; $_SESSION['vouchar_type']=""; $_SESSION['transaction_name']="";
    }
	
  } //End of the function insertSalesDetails()
  function adjustACPayable($voucher_no,$DrAmount,$created_date){
	//======= AC Payable Dr ======
	/*
	$project_id 	 = getFromSession('project_id');
	$PayableId 	 = $this->getACPayableId(getFromSession('project_id'));
	$description 	 = "Paid payable against cost of goods purchased";
	$ACPayableAmount = $this->getAccounceBalance($PayableId,$project_id);
	$PayableBalance  = ($ACPayableAmount+$DrAmount);					 
	$this->saveAccountJournal($voucher_no,$PayableId,"Accounts Payable",$project_id,$description,$DrAmount,0,$PayableBalance,0,$created_date);
	*/
  }
  function adjustACReceibavle($voucher_no,$CrAmount,$created_date){
	 //======= AC Recievable Cr ======
	 /*
	 $ACRecievableId 	= $this->getACRecievableId(getFromSession('project_id'));
	 $description 	 	= "Collection receivable against cost of goods sold"; 
	 $ACRecievableAmount	= $this->getAccounceBalance($ACRecievableId,$project_id);
	 $RecievableBalance  	= ($ACRecievableAmount-$CrAmount);					 
	 $this->saveAccountJournal($voucher_no,$ACRecievableId,"Account Recievable",$project_id,$description,0,$CrAmount,$RecievableBalance,0,$created_date);
	 */
  } 
   
     
  function sendSMS($sender,$recipients,$message){	
	$token = SMS_TOKEN;
	$url = "http://api.greenweb.com.bd/api.php";
	$data= array(
	'to'=>"$recipients",
	'message'=>"$message",
	'token'=>"$token"
	); // Add parameters in key value
	$ch = curl_init(); // Initialize cURL
	curl_setopt($ch, CURLOPT_URL,$url);
	curl_setopt($ch, CURLOPT_ENCODING, '');
	curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	$smsresult = curl_exec($ch);
	//Result
	//echo $smsresult;
	//Error Display
	//echo curl_error($ch);
  }

   /*
  
  function sendSMS($sender,$recipients,$message){	
	$token = SMS_TOKEN;
	$url = "https://24smsbd.com/api/bulkSmsApi";
	$data= array(
	'sender_id'=>"1903",
	'apiKey'=>"$token",
	'mobileNo'=>"$recipients",
	'message'=>"$message"
	); // Add parameters in key value
	$curl = curl_init($url);
	curl_setopt($curl, CURLOPT_POST, true);
	curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
	curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);
	curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
	curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
	$output = curl_exec($curl);
	curl_close($curl);	
	//echo $output;
  }*/
  
   //================ End Due Received List ===============
   function showEditor($msg = NULL) { 
        require_once(CLASS_DIR.'/common.list.class.php');	
	$clistApp 	= new CommonList(); 
	$ref_id   	= getRequest('ref_id');
	$project_id 	= getFromSession('project_id');
	if(empty($ref_id)){ $ref_id=0;}
	if($ref_id >0){
		$this->getPendingOnlineVoucher($project_id,$ref_id);
	}
	$getGM="SELECT * FROM ".TMP_GRVMASTER_TBL." WHERE created_by = '".getFromSession('userid')."'";
	$gres = mysql_query($getGM);
	if(mysql_num_rows($gres)>0){
	$grow = mysql_fetch_object($gres);
	$_SESSION['tmp_grvid']=$grow->tmp_grvid; $_SESSION['headtypes']=$grow->headtypes;
	}else{$_SESSION['tmp_grvid']=""; $_SESSION['headtypes']="";}		 
	 
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
	$data['tmp_voucher']	= $this->getTempVoucher();	   
	$data['message'] = $msg;
	$data['cmd']     = getRequest('cmd');


//$this->insertNewVoucher();




	require_once(CURRENT_APP_SKIN_FILE);      
	
	return true;

   }

function insertNewVoucher(){

$getSql = "SELECT * FROM " . CONTRA_MASTER_TBL . " WHERE project_id='P0005' AND contra_id >='16072' AND contra_id <='16093'";
$gres = mysql_query($getSql);
if (mysql_num_rows($gres) > 0) {
        while ($row = mysql_fetch_object($gres)) {
$voucher_snapshot = json_decode($row->voucher_snapshot, true);
$description=$voucher_snapshot['master']['description'];

                //$getSqlD = "UPDATE " . CONTRA_MASTER_TBL . " SET description = '$description' WHERE project_id='P0005' AND contra_id='$row->contra_id'";
                //$grest = mysql_query($getSqlD);
                //$drow = mysql_fetch_object($grest);
	

print_r($row->description . "</br></br>");



	}
}
print_r("ok");
exit();


}

   function showEditorOld($msg = NULL) { 
        require_once(CLASS_DIR.'/common.list.class.php');	
	$clistApp 	= new CommonList(); 
	$ref_id   	= getRequest('ref_id');
	$project_id 	= getFromSession('project_id');
	if(empty($ref_id)){ $ref_id=0;}
	if($ref_id >0){
		$this->getPendingOnlineVoucher($project_id,$ref_id);
	}
	$getGM="SELECT * FROM ".TMP_GRVMASTER_TBL." WHERE created_by = '".getFromSession('userid')."'";
	$gres = mysql_query($getGM);
	if(mysql_num_rows($gres)>0){
	$grow = mysql_fetch_object($gres);
	$_SESSION['tmp_grvid']=$grow->tmp_grvid; $_SESSION['headtypes']=$grow->headtypes;
	}else{$_SESSION['tmp_grvid']=""; $_SESSION['headtypes']="";}		 
	 
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
	$data['tmp_voucher']	= $this->getTempVoucher();	   
	$data['message'] = $msg;
	$data['cmd']     = getRequest('cmd');
	require_once(TEMPLATES_SKINS . '/contra.voucher.new_old.html');      
	
	return true;

   }

   function getPendingOnlineVoucher($project_id,$tmp_grvid){
	
	$SQLCM="SELECT * FROM ".PENDING_CVMASTER_TBL." WHERE project_id='".$project_id."' AND tmp_grvid=".$tmp_grvid;
	$query = mysql_query($SQLCM);
	$num = mysql_num_rows($query);
	if($num >0){
		 $grow 			= mysql_fetch_object($query);
		 $project_id 		= $grow->project_id;
		 $mr_no 		= $grow->mr_no;
		 $headtypes 		= $grow->headtypes;
		 $dr_account    	= $grow->dr_account;
		 $dr_amount 		= $grow->dr_amount;
		 $mode_of_payment	= $grow->mode_of_payment;
		 $vouchar_type 		= $grow->vouchar_type;
		 $bank_journal		= $grow->bank_journal;
		 $transaction_type	= $grow->transaction_type;
		 $currency		= $grow->currency;
		 $currency_name		= $grow->currencyName;
		 $description 		= $grow->description;
		 $attachment		= $grow->attachment;
		 $created_date		= $grow->created_date;
		 $created_by		= getFromSession('userid');

		 $SQL= "INSERT INTO ".TMP_GRVMASTER_TBL."(project_id,mr_no,headtypes,dr_account,dr_amount,mode_of_payment,vouchar_type,bank_journal,transaction_type,currency,currencyName,description,attachment,created_by,created_date) ";
		$SQL.= " VALUES('". $project_id."','". $mr_no."','". $headtypes."','".$dr_account."','".$dr_amount."','".$mode_of_payment."','".$vouchar_type."','".$bank_journal."','".$transaction_type."','".$currency."','".$currency_name."','".$description."','".$attachment."','".$created_by."','".$created_date."')";
		//echo $SQL;
		$res = mysql_query($SQL);
		if($res){
			$contra_mid = mysql_insert_id();
			$_SESSION['tmp_grvid']=$contra_mid;
			$_SESSION['headtypes']=$headtypes;

			$CSQL="SELECT * FROM ".PENDING_CVDETAILS_TBL." WHERE project_id='$project_id' AND tmp_grvid='$tmp_grvid'";
			$cquery = mysql_query($CSQL);
			$dnum = mysql_num_rows($cquery);
			if($dnum >0){
				while($v = mysql_fetch_object($cquery)){
				$SQLD= "INSERT INTO ".TMP_GRVDETAILS_TBL."(tmp_grvid,project_id,headtypes,dr_account,currency,currencyName,cr_account,cr_acname,cr_amount,bank_name,acc_no,check_no,check_issue_date,cheque_type,vouchar_type,description,created_by) VALUES('". $contra_mid."','".$project_id."','". $v->headtypes."','". $v->dr_account."','".$v->currency."','".$v->currencyName."','". $v->cr_account."','". $v->cr_acname."','". $v->cr_amount."','".$v->bank_name."','".$v->acc_no."','".$v->check_no."','".$v->check_issue_date."','".$v->cheque_type."','".$v->vouchar_type."','".$v->description."','".$created_by."')";				
				$dres = mysql_query($SQLD);
				}
				$tmpsql = "DELETE FROM ".PENDING_CVMASTER_TBL." WHERE project_id='".$project_id."' AND tmp_grvid=".$tmp_grvid;
				mysql_query($tmpsql);

				$tdpsql = "DELETE FROM ".PENDING_CVDETAILS_TBL." WHERE project_id='".$project_id."' AND tmp_grvid=".$tmp_grvid;
				mysql_query($tdpsql);
			}
		}


	}// end if num
   }
     
   //==================== saveDebitVouchar ====================
   function saveDebitVouchar($voucher_no,$payment_mode,$vouchar_type,$dr_account,$dr_amount,$cr_account,$created_date,$bank_name=NULL,$acc_no=NULL,$check_no=NULL,$check_date=NULL,$collection_source=NULL)
   {     
	  $requestdata = array();
	  $mode_of_payment = $payment_mode;
	  $requestdata = getUserDataSet(DEVIT_VOUCHAR_TBL);			  	  		    
	  $requestdata['head_type']     	= "";   
	  $requestdata['account_head']      = $dr_account; 
	  $requestdata['debit']        		= $dr_amount;    
	  $requestdata['credit']        	= 0; 
	  $requestdata['mode_of_payment'] = $payment_mode;
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
	  $requestdata['created_date']    = $created_date;		  
	  $requestdata['paid_amount']     = $dr_amount;
	  $requestdata['due']   	  = 0;
	  
	  $requestdata['vouchar_type']= $vouchar_type;
	  $requestdata['collection_source'] = $collection_source;
          if (!isset($collection_source)) {
            $requestdata['collection_source'] = "Others";
          }
	  		
	  $requestdata['description']="Contra Voucher";
	  $requestdata['branch_id'] = getFromSession('branch_id');
	 if($voucher_no != ""){
		$requestdata['voucher_no']   	= $voucher_no;
	  }else{
		$msg = "ID overflow !!!"; header("location:index.php?app=user_home&msg=$msg"); exit;
	  }	 
	  $info        		=  array();
	  $info['table']	= DEVIT_VOUCHAR_TBL;
	  $info['data'] 	= $requestdata;     
	  //$info['debug']  	=  true;
	  $res = insert($info);			

	  if($res['affected_rows']) {
		return true;
	  }else {	
		return false;	
	  }  

    }//EOFn  

    function saveCreditVouchar($voucher_no,$payment_mode,$vouchar_type,$dr_account,$cr_account,$cr_amount,$created_date,$bank_name=NULL,$acc_no=NULL,$check_no=NULL,$check_date=NULL,$description=NULL,$collection_source=NULL)
    {     
	  $mode_of_payment = $payment_mode;
	  $requestdata = array();
	  $requestdata = getUserDataSet(CREDIT_VOUCHAR_TBL);
	  $requestdata['head_type']     	= "";   
	  $requestdata['account_head']      	= $cr_account; 
	  $requestdata['debit']        		= 0; 
	  $requestdata['credit']        	= $cr_amount; 
	  $requestdata['mode_of_payment'] = $payment_mode;
	  if($mode_of_payment =="Check"){
		$requestdata['mode_of_payment'] = "Bank";
		$requestdata['bank_name'] 	= $bank_name;
		$requestdata['acc_no'] 		= $acc_no;
		$requestdata['check_no'] 	= $check_no;
		$requestdata['check_issue_date']= $check_date;	
	  }else{
		$requestdata['bank_name'] = "";
		$requestdata['acc_no'] = "";
		$requestdata['check_no'] = "";
		$requestdata['check_issue_date'] = "";  
	  }
	  $requestdata['project_id']        = getFromSession('project_id');    
	  $requestdata['created_by']        = getFromSession('userid'); 			 
	  $requestdata['created_date']      = $created_date;
	  //$requestdata['created_date']    = date('Y-m-d h:i:s');	
	  $requestdata['voucher_no']   	    = $voucher_no;
	  $requestdata['vouchar_type'] 	    = $vouchar_type;
	  $requestdata['description']	    ="Contra Voucher";
	  $requestdata['branch_id'] 	    = getFromSession('branch_id');
	  $requestdata['collection_source'] = $collection_source;
          if (!isset($collection_source)) {
            $requestdata['collection_source'] = "Others";
          }
	  
	  $info        		=  array();
	  $info['table']	= CREDIT_VOUCHAR_TBL;
	  $info['data'] 	= $requestdata;     
	  //$info['debug']  	=  true;
	  $res = insert($info);	  

	  if($res['affected_rows']) {
		return true;
	  }else {	
		return false;
	  }  

    }//EOFn

    //========== SaveIn Temp Tbl ======
	
    function saveTempGRVMaster($strArr){
	$requestdata = array();
	$requestdata = getUserDataSet(TMP_GRVMASTER_TBL);		
	$requestdata['headtypes'] 	= getRequest('headtypes');
	$requestdata['dr_account'] 	= getRequest('dr_account');
	$requestdata['dr_amount'] 	= getRequest('dr_amount');
	$requestdata['mode_of_payment'] = getRequest('mode_of_payment');
	$requestdata['vouchar_type'] 	= getRequest('vouchar_type');
	$requestdata['bank_journal']	= getRequest('bank_journal');
	$requestdata['currency'] 	= getRequest('currency');
	$requestdata['currencyName']	= getRequest('currencyName');
	$requestdata['created_date']	= formatDate(getRequest('created_date'));		
	$requestdata['project_id'] 	= getFromSession('project_id');		
	$requestdata['created_by'] 	= getFromSession('userid');
	$requestdata['mr_no'] = getRequest('mr_no');

        $head_id = getRequest('cr_account');

        $dr_account = "";
        $cr_account = "";
        if ($requestdata['headtypes'] == "Dr") {
            $dr_account = $head_id;
            $requestdata['dr_amount'] = getRequest('cr_amount');
        } elseif ($requestdata['headtypes'] == "Cr") {
            $cr_account = $head_id;
        }

        $requestdata['dr_account'] = $dr_account;
        $requestdata['cr_account'] = $cr_account;

	$info        		=  array();
	$info['table']	= TMP_GRVMASTER_TBL;
	$info['data'] 	= $requestdata;     
	//$info['debug']  	=  true;
	$res = insert($info);
	if($res){
	$_SESSION['tmp_grvid']=mysql_insert_id();
	$_SESSION['headtypes']=getRequest('headtypes');
	}
		
    }
    function saveTempVoucher(){
	$str 			= getRequest('str');
	$strArr 		= explode("####",$str);

	if($_SESSION['tmp_grvid']==""){
	 $getGM="SELECT * FROM ".TMP_GRVMASTER_TBL." WHERE created_by = '".getFromSession('userid')."' AND project_id='".getFromSession('project_id')."'";
	 $gres = mysql_query($getGM);
	 if(mysql_num_rows($gres)==0){
	 $this->saveTempGRVMaster($strArr);
	 }else{ 
	 $grow = mysql_fetch_object($gres);
	 $_SESSION['tmp_grvid']=$grow->tmp_grvid;
	 }
	} else {
            $getGM = "SELECT * FROM " . TMP_GRVMASTER_TBL . " WHERE tmp_grvid = '" . $_SESSION['tmp_grvid'] . "'";
            $gres = mysql_query($getGM);
            if (mysql_num_rows($gres) > 0) {
                if (getRequest('headtypes') == "Dr") {
                    $requestdata['dr_account'] = getRequest('cr_account');
                    $requestdata['dr_amount'] = getRequest('cr_amount');
		    $requestdata['headtypes'] = getRequest('headtypes');

                    $info = array();
                    $info['table'] = TMP_GRVMASTER_TBL;
                    $info['data'] = $requestdata;
                    $info['where'] = "tmp_grvid ='" . $_SESSION['tmp_grvid'] . "'";
                    //$info['debug']=  true;
                    update($info);
                }

                if (getRequest('headtypes') == "Cr") {
                    $requestdata['cr_account'] = getRequest('cr_account');

                    $info = array();
                    $info['table'] = TMP_GRVMASTER_TBL;
                    $info['data'] = $requestdata;
                    $info['where'] = "tmp_grvid ='" . $_SESSION['tmp_grvid'] . "'";
                    //$info['debug']=  true;
                    update($info);
                }
            }
        }
 
	$tmp_id 			= getRequest('tmp_id');
	//======= Insert into tamp ========	
	$requestdata = array();
	$requestdata = getUserDataSet(TMP_GRVDETAILS_TBL);
	$requestdata['tmp_grvid'] 	= $_SESSION['tmp_grvid'];	
	$requestdata['headtypes'] 	= getRequest('headtypes');	
	$requestdata['project_id'] 	= getFromSession('project_id');
	if(getRequest('headtypes')=="Dr"){	
	$requestdata['cr_account'] = "";
	$requestdata['dr_account'] 	= getRequest('cr_account');
	$_SESSION['headtypes'] 		= getRequest('headtypes');	
	}elseif(getRequest('headtypes')=="Cr"){			
	$requestdata['cr_account']	= getRequest('cr_account');
	$requestdata['dr_account'] = "";
	}	
	
	$requestdata['cr_acname'] = $this->getHeadName(getRequest('cr_account'));		
	$requestdata['consignee'] 	= getRequest('consignee');
	$requestdata['consignee_name']  = $this->getRetailerName(getRequest('consignee'));
	$requestdata['currency'] 	= getRequest('currency');
	$requestdata['currencyName'] 	= getRequest('currencyName');
	$requestdata['cr_amount'] 	= getRequest('cr_amount');
	$requestdata['bank_name'] 	= getRequest('bank_name');
	$requestdata['acc_no'] 		= getRequest('acc_no');
	$requestdata['check_no'] 	= getRequest('check_no');
	$requestdata['check_issue_date']= formatDate(getRequest('check_issue_date'));
	$requestdata['cheque_type'] 	= getRequest('cheque_type');
	$requestdata['vouchar_type'] 	= getRequest('vouchar_type');
	$requestdata['description'] 	= getRequest('description');
	$requestdata['dealer_payment'] 	= getRequest('dealer_payment');
	$requestdata['created_by'] 	= getFromSession('userid');	
	
	$info        	=  array();
	$info['table']	= TMP_GRVDETAILS_TBL;
	$info['data'] 	= $requestdata;     
	//$info['debug']=  true;
	//$res = insert($info);
	if($tmp_id >0){
		$info        	= array();
		$info['table']	= TMP_GRVDETAILS_TBL;
		$info['data'] 	= $requestdata; 
		$info['where']	= "tmp_id ='".$tmp_id."'";      
		//$info['debug']=  true;
		$res = update($info);	
	}else{
		$info        	=  array();
		$info['table']	= TMP_GRVDETAILS_TBL;
		$info['data'] 	= $requestdata;     
		//$info['debug']  =  true;
		$res = insert($info);
	}

	echo $this->getTempVoucher();
	/*  
	$str1="<table width='100%' align='center' bgcolor='#99CC66'>
	<tr style='color:#fff;' bgcolor='#00B000' height=28>
	  <td width='25%' align='left' nowrap>Account Head Name</td>
	  <td width='6%' align='left' nowrap>Head Type</td>
	  <td width='10%' align='right' nowrap>Amount </td>
	  <td width='11%' align='center' nowrap>Bank Name</td>
	  <td width='11%' align='left' nowrap>Branch Name</td>
	  <td width='10%' align='left' nowrap>Cheque No.</td>
	  <td width='10%' align='left' nowrap>Cheque Issue Date</td>
	  <td width='12%' align='left' nowrap>Note</td>					  
	  <td width='5%' align='center' nowrap>Option</td>
	</tr>";
	$totalCr_amount = 0; $totalDr_amount = 0;
	$getSql	= "SELECT * FROM ".TMP_GRVDETAILS_TBL." WHERE created_by='".getFromSession('userid')."' AND project_id='".getFromSession('project_id')."' 
	AND tmp_grvid='".$_SESSION['tmp_grvid']."'";
	$gres 	= mysql_query($getSql);
	while($row = mysql_fetch_array($gres)){
	extract($row);
	if($headtypes=="Cr"){
	$totalCr_amount+=$cr_amount;
	}elseif($headtypes=="Dr"){
	$totalDr_amount+=$cr_amount;
	}
	if($consignee_name !=""){
		$consignee_name = "<br>Retailer : $consignee_name";
	}
	$str2.="
	<tr style='color:#000000' bgcolor='#CCCCCC'>
	  <td width='25%' align='left' nowrap>$cr_acname $consignee_name</td>
	  <td width='6%' align='left' nowrap>$headtypes</td>
	  <td width='10%' align='right' nowrap>$cr_amount $currencyName</td>
	  <td width='11%' align='center' nowrap>$acc_no</td>
	  <td width='11%' align='center' nowrap>$branch_name</td>
	  <td width='10%' align='left' nowrap>$check_no</td>
	  <td width='10%' align='left' nowrap>$check_issue_date</td>
	  <td width='12%' align='left' nowrap>$description</td>				  
	  <td width='5%' align='center' nowrap>
	  <a href=\"?app=contra.voucher.new&cmd=deltemp&id=$tmp_id\"><img src=\"images/common/icons/delete.gif\"></a>
	  </td>
	</tr>";
	}
	$str3="</table>";
	echo $str1.$str2.$str3."####-@@@@".$totalCr_amount."####-@@@@".$totalDr_amount;
	*/
    }
    function delTempVoucher(){
	$tmp_id = $_REQUEST['id'];
	if($tmp_id!=""){
	 $dsql = "DELETE FROM ".TMP_GRVDETAILS_TBL." WHERE tmp_id ='".$tmp_id."'";
	 mysql_query($dsql);
	 
	 $gsql = "SELECT * FROM ".TMP_GRVDETAILS_TBL." WHERE tmp_grvid='".$_SESSION['tmp_grvid']."'";
	 $NUM = mysql_num_rows(mysql_query($gsql));
	 if($NUM==0){
		$dsql = "DELETE FROM ".TMP_GRVMASTER_TBL." WHERE tmp_grvid ='".$_SESSION['tmp_grvid']."'";
	 	mysql_query($dsql); 
		$_SESSION['tmp_grvid']="";
		$_SESSION['headtypes']="";
	 }
	 
	}		
	header("location:?app=contra.voucher.new&cmd=add");
    }
    function getTempVoucher(){
	$str1="<table width='100%' align='center' bgcolor='#99CC66'>
	<tr style='color:#fff;' bgcolor='#00B000' height=28>
	  <td width='25%' align='left' nowrap>Account Head Name</td>
	  <td width='6%' align='left' nowrap>Head Type</td>
	  <td width='10%' align='right' nowrap>Amount </td>
	  <td width='11%' align='center' nowrap>Bank Name</td>
	  <td width='11%' align='left' nowrap>Branch Name</td>
	  <td width='10%' align='left' nowrap>Cheque No.</td>
	  <td width='10%' align='left' nowrap>Cheque Issue Date</td>
	  <td width='12%' align='left' nowrap>Note</td>					  
	  <td width='5%' align='center' nowrap>Option</td>
	</tr>";
	$totalCr_amount = 0; $totalDr_amount=0;
	$created_by = getFromSession('userid');
	$getSql		= "SELECT * FROM ".TMP_GRVDETAILS_TBL." WHERE created_by='".getFromSession('userid')."' AND project_id='".getFromSession('project_id')."' 
	AND tmp_grvid='".$_SESSION['tmp_grvid']."'";
	$gres 		= mysql_query($getSql);
	while($row = mysql_fetch_array($gres)){
	extract($row);
	if($headtypes=="Cr"){
	$totalCr_amount+=$cr_amount;
	}elseif($headtypes=="Dr"){
	$totalDr_amount+=$cr_amount;
	}
	if($consignee_name !=""){
		$consignee_name = "<br>Retailer : $consignee_name";
	}
	$str2.="
	<tr style='color:#000000' bgcolor='#CCCCCC'>
	  <td width='25%' align='left' nowrap>$cr_acname $consignee_name</td>
	  <td width='6%' align='left' nowrap>$headtypes</td>
	  <td width='10%' align='right' nowrap>$cr_amount $currencyName</td>
	  <td width='11%' align='center' nowrap>$acc_no</td>
	  <td width='11%' align='center' nowrap>$branch_name</td>
	  <td width='10%' align='left' nowrap>$check_no</td>
	  <td width='10%' align='left' nowrap>$check_issue_date</td>
	  <td width='12%' align='left' nowrap>$description</td>				  
	  <td width='5%' align='center' nowrap>
	  <a href=\"#\"  title='Edit' onclick=\"ItemEdit($tmp_id)\"><img src=\"images/common/icons/edit.gif\"></a> &nbsp;
	  <a href=\"?app=contra.voucher.new&cmd=deltemp&id=$tmp_id\"><img src=\"images/common/icons/delete.gif\"></a>
	  </td>
	</tr>";
	}
	$str3="</table>";
	$total_salesStr = $str1.$str2.$str3."####-@@@@".$totalCr_amount."####-@@@@".$totalDr_amount;
	return $total_salesStr;
    }
    function getTempDetails($tmp_id){
	$project_id 	= getFromSession('project_id');
	$sql = "SELECT * FROM ".TMP_GRVDETAILS_TBL." WHERE tmp_id = '".$tmp_id."' AND project_id='".$project_id."'";
	$row = mysql_fetch_object(mysql_query($sql));
	//dr_account,currency,currencyName,cr_account,cr_acname,cr_amount,bank_name,acc_no,check_no,check_issue_date,cheque_type,vouchar_type,description

	$str = $row->tmp_id."#####".$row->headtypes."#####".$row->cr_account."#####".$row->consignee."#####".$row->cr_amount."#####".$row->bank_name."#####".$row->acc_no."#####".$row->check_no."#####".dateInputFormatDMY($row->check_issue_date)."#####".$row->description;
	echo $str;
    }
    //======= End SaveIn Temp Tbl ========     

    function getContraMasterInfo($id){		
	$project_id     = getFromSession('project_id');  
	$info           = array();    
	$info['table']  = CONTRA_MASTER_TBL.' cm,'.PROJECT_TBL.' p,'.CURRENCY_TBL.' c';	
	$info['fields'] = array('cm.contra_id','cm.voucher_no','cm.headtypes','cm.adjustment','cm.beddebts','cm.dr_account','p.project_name','p.location','p.project_logo','cm.dr_amount','cm.mode_of_payment','cm.vouchar_type','cm.transaction_type','cm.description',"DATE_FORMAT(cm.created_date,'%d %b %y' ) as created_date",'c.curr_symble','cm.created_by','cm.created_time');	
	$sql="cm.project_id = p.project_id AND cm.currency = c.currency_id AND cm.project_id = '".$project_id."' AND cm.contra_id = '$id'";							
	$info['where']   = $sql;	  	
    $info['groupby'] = array("cm.contra_id");
	//$info['debug']  = true;
	$res            =	select($info);
	if(count($res)){
		foreach($res as $i=>$v){
			$data[$i] = $v;             
		}
	}
	  //dumpVar($data);
	return $data[0];
    }   
        
    function getContraDetails($id) {
	$info           = array();    
	$info['table']  = CONTRA_DETAILS_TBL.' cd,'.CURRENCY_TBL.' c';	
	$info['fields'] = array('cd.details_id','cd.contra_id','cd.headtypes','cd.dr_account','cd.cr_account','cd.cr_amount','cd.bank_name','cd.acc_no',
	'cd.check_no',"DATE_FORMAT(cd.check_issue_date,'%d %b %y' ) as check_issue_date",'cd.cheque_type','cd.vouchar_type','c.curr_symble','cd.description','cd.created_by');		
	$sql="cd.currency = c.currency_id AND cd.contra_id = '$id'";		
	$info['where']   = $sql;
    	$info['groupby'] = array("cd.details_id");
	$info['orderby'] = array("cd.details_id asc");
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
        $info['groupby'] = array("cm.`contra_id`");
	$info['orderby'] = array("cm.`contra_id` DESC LIMIT $from,$to");
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
   function  getTotalContraVoucherList(){
	$date_from 	= formatDate(getRequest('date_from'));
	$date_to 	= formatDate(getRequest('date_to'));				
	$project_id     = getFromSession('project_id');
	$info           = array();    
	$info['table']  =  CONTRA_MASTER_TBL.' cm,'.CURRENCY_TBL.' c';	
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
   	
  function saveAccountJournal($voucher_no,$sub_id,$transaction_type,$project_id,$description,$DR=NULL,$CR=NULL,$balance,$status,$created_date,$issue_date, $adjustment = NULL, $beddebts = NULL){
	$head_type	= getHeadType($sub_id);   $created_by = getFromSession('userid'); 
	$adjustment = isset($adjustment) ? $adjustment : getRequest('adjustment');
        $beddebts = isset($beddebts) ? $beddebts : getRequest('beddebts');
	$sql = "INSERT INTO ".ACCOUNT_JOURNAL_TBL." (voucher_no,created_date,issue_date,sub_id,head_type,transaction_type,project_id,description,adjustment,beddebts,dr,cr,balance,status,created_by) 
	 VALUES('".$voucher_no."','".$created_date."','".$issue_date."','".$sub_id."','".$head_type."','".$transaction_type."','".$project_id."','".$description."','".$adjustment."','".$beddebts."','".$DR."','".$CR."','".$balance."','".$status."','".$created_by."')";
	mysql_query($sql);
  }

  function saveRetailerJournal($voucher_no,$sub_id,$transaction_type,$project_id,$description,$DR=NULL,$CR=NULL,$balance,$status,$created_date,$issue_date=NULL){
	$head_type  = "Retailer";   $created_by = getFromSession('userid'); 
	$sql = "INSERT INTO ".ACCOUNT_JOURNAL_TBL." (voucher_no,created_date,issue_date,sub_id,head_type,transaction_type,project_id,description,dr,cr,balance,status,created_by) 
	 VALUES('".$voucher_no."','".$created_date."','".$issue_date."','".$sub_id."','".$head_type."','".$transaction_type."','".$project_id."','".$description."','".$DR."','".$CR."','".$balance."','".$status."','".$created_by."')";
	mysql_query($sql);
   }
   // ======== Create Voucher ID =======
    function createVoucharID($moneyReceipt)
    {
        $vPrefix = "C";
        if ($moneyReceipt) {
            $vPrefix = "MR";
        }
        $user_sl = getFromSession('user_sl');
        if ($user_sl != "") {
            $prefix = $vPrefix . $user_sl;
        } else {
            $prefix = $vPrefix;
        }

        $info = array();
        $info['table'] = CONTRA_MASTER_TBL;
        $info['fields'] = array('max(voucher_no) as maxvoucher');
        $info['where'] = "transaction_type = 'Contra Voucher' AND voucher_no LIKE '%" . $prefix . "%'";
        $res = select($info);
        $maxvoucherId = $prefix . '0000000';
        if (count($res)) {
            foreach ($res as $v) {
                if ($v->maxvoucher) {
                    $maxvoucherId = $v->maxvoucher;
                }
                break;
            }
        }
        $maxvoucherId = generateID("$prefix", $maxvoucherId, 9);
        return $maxvoucherId;
    }
     
   
   function getSubAccHeadList(){
      $info            = array();
      $project_id      = getFromSession('project_id');
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
    function getRetailerName($head_id){
	$project_id = getFromSession('project_id');
	$headName   = "";
	$acsql	= "SELECT retailer_name FROM ".RETAILER_TBL." WHERE retailer_id  = '".$head_id."' AND project_id = '$project_id'";
	$acres 	= mysql_query($acsql);
	$acnum 	= mysql_num_rows($acres);
	if($acnum >0){
		$row = mysql_fetch_object($acres);
		 $headName= $row->retailer_name;
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
    function getACRecievableId($project_id){
	$sql = "SELECT sub_id FROM ".SUB_ACC_HEAD_TBL." WHERE sub_id='A000024' AND head_type = 'Accounts Recievable' AND project_id = '$project_id'";
	$row = mysql_fetch_object(mysql_query($sql));
	return $sub_id = $row->sub_id;
   }
   function getACPayableId($project_id){
	$sql = "SELECT sub_id FROM ".SUB_ACC_HEAD_TBL." WHERE sub_id='A000028' AND head_type = 'Accounts Payable' AND project_id = '$project_id'";
	$row = mysql_fetch_object(mysql_query($sql));
	return $sub_id = $row->sub_id;
   }
   function getAccounceBalance($account_id,$project_id){
	$sql = "SELECT (sum(`dr`) - sum(`cr`)) as balance_amount FROM ".ACCOUNT_JOURNAL_TBL." WHERE sub_id = '$account_id' AND project_id = '$project_id'";
	
	$row = mysql_fetch_object(mysql_query($sql));
	$balance_amount = $row->balance_amount;
	if(empty($balance_amount)){
		$balance_amount = 0;
	}
	return $balance_amount;
  }
  //=============End =============
} // End class
?>
