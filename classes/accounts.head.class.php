<?php
class AccountHead
{
   function run() {     
		$cmd    = getRequest('cmd');
		$u_t_id = getFromSession('u_type_id');
		if($u_t_id == 101) 
		{      
		  switch($cmd) { 
		  case 'add'            : $screen = $this->showEditor($msg); break;
      	     	  case 'loadsubhtype'  	: $this->loadSubHeadType(trim(getRequest('head_type'))); break;
		  case 'loadchildhtype' : $this->loadChildHeadType(); break;
		  case 'loadSL3Htype'   : $this->loadSubsidiary3Htype(); break; 	 	
      	     	  case 'edit'           : $screen = $this->showEditor("Edit Page");  break; 
      	   	  case 'doUpdate'       : $screen = $this->showEditor($msg); break;
		  case 'delete'         : $screen = $this->deleteItem(); break;
		  case 'bulk_import'    : $screen = $this->COABulkImport(); break;
		  case 'check_head_code': $screen = $this->checkHeadCode(); break;
		  case 'getAccountToExport': $screen = $this->getAccountToExport(); break;
		  case 'coa_tree'    	: $screen = $this->COATree(); break;
		  default               : $cmd = 'list'; $screen = $this->showEditor($msg);   break;
		  }
		}else if($u_t_id == 107) 
		{      
		  switch($cmd) { 
		  case 'add'                	: $this->showEditor($msg); break;
      	     	  case 'loadsubhtype'  		: $this->loadSubHeadType(trim(getRequest('head_type'))); break;
		  case 'loadchildhtype' 	: $this->loadChildHeadType(); break;
		  case 'loadSL3Htype'   	: $this->loadSubsidiary3Htype(); break;  	
      	     	  case 'edit'               	: $this->showEditor("Edit Page");    break;
      	   	  case 'doUpdate'           	: $screen = $this->showEditor($msg); break;
		  case 'bulk_import'    	: $screen = $this->COABulkImport(); break;
		  case 'check_head_code'	: $screen = $this->checkHeadCode(); break;
		  case 'getAccountToExport'	: $screen = $this->getAccountToExport(); break;
		  case 'coa_tree'    		: $screen = $this->COATree(); break;
		     
		  default                   	: $cmd = 'list'; $screen = $this->showEditor($msg);   break;
		  }
		}elseif($u_t_id == 102) 
		{      
		  switch($cmd) { 
		  case 'add'                	: $screen = $this->showEditor($msg); break; 
		  case 'loadchildhtype' 	: $this->loadChildHeadType(); break; 
		  case 'loadSL3Htype'   	: $this->loadSubsidiary3Htype(); break; 		
      	     	  case 'edit'               	: $screen = $this->showEditor("Edit Page");    break;
      	   	  case 'doUpdate'           	: $screen = $this->showEditor($msg); break;
		  case 'bulk_import'    	: $screen = $this->COABulkImport(); break;
		  case 'check_head_code'	: $screen = $this->checkHeadCode(); break;
		  case 'getAccountToExport'	: $screen = $this->getAccountToExport(); break;
		  case 'coa_tree'    		: $screen = $this->COATree(); break;

      	     	  case 'loadsubhtype'  		: $this->loadSubHeadType(trim(getRequest('head_type'))); break;
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
	 $sub_id = getRequest('id');	
	 $unapproved_id = getRequest('unapproved_id');
	 $approved = getRequest('approved');
 
	 $data          = array();		
	 
	if ($sub_id && !$unapproved_id) {
            $TBDArr = $comApp->getRecordInfo(SUB_ACC_HEAD_TBL, "sub_id", $sub_id);
            $TBDArr = parseThisValue($TBDArr);
            $data = array_merge(array(), $TBDArr);
            if (getRequest('save')) {
                $ures = $this->updateRecord($sub_id);
                if ($ures) {
                    $msg = "Successfully Update Record !!!";
                    header("location:?app=accounts.head&cmd=list&msg=$msg");
                } else {
                    $msg = "Please try again !!!";
                }
            }
        } else if (!$sub_id && $unapproved_id) {
            // update unapproved data
            $TBDArr = $comApp->getRecordInfo("unapproved_sub_acc_head", "id", $unapproved_id);
            $TBDArr = parseThisValue($TBDArr);
            $data = array_merge(array(), $TBDArr);
            $data['unapproved_id'] = $data['id'];
            if (getRequest('save')) {
                $requestdata = array();
                $requestdata = getUserDataSet("unapproved_sub_acc_head");
                $requestdata['project_id'] = getFromSession('project_id');
                $requestdata["modified_by"] = getFromSession('userid');
                $requestdata["modified_time"] = date('Y-m-d h:i:s');
                $info = array();
                $info['table'] = "unapproved_sub_acc_head";
                if (getRequest('sub_headtype') == "") {
                    $requestdata["sub_headtype"] = " ";
                }
                if (getRequest('sl_three_head') == "") {
                    $requestdata["sl_three_head"] = " ";
                } else {
                    $requestdata["sl_three_head"] = getRequest('sl_three_head');
                }
                $head_type = getRequest('head_type');
                if ($head_type == "Current Assets" || $head_type == "Non Current Assets") {
                    $requestdata["group_ledger"] = "ASSETS";
                } elseif ($head_type == "Current Liabilities" || $head_type == "Non Current Liabilities") {
                    $requestdata["group_ledger"] = "LIABILITIES";
                } elseif ($head_type == "Capital" || $head_type == "Retained earnings" || $head_type == "Retained Earnings") {
                    $requestdata["group_ledger"] = "EQUITY";
                } elseif ($head_type == "Operating Revenue" || $head_type == "Non-Operating Revenue") {
                    $requestdata["group_ledger"] = "REVENUE";
                } elseif ($head_type == "Direct Expenses" || $head_type == "Indirect Expenses") {
                    $requestdata["group_ledger"] = "EXPENSES";
                }

		if (getRequest('cost_center_required') == 1) {
                    $requestdata["cost_center_required"] = 1;
                }else{
		    $requestdata["cost_center_required"] = 0;
		}
		if (getRequest('make_cost_center') == 1) {
                    $requestdata["make_cost_center"] = 1;
                }else{
		    $requestdata["make_cost_center"] = 0;
		}

                $info['data'] = $requestdata;
                $info['where'] = "id ='$unapproved_id'";

                $res = update($info);
                if (!$res) {
                    $msg = "Please try again !!!";
                } else {
                    $msg = "Successfully Update Record !!!";
                    header("location:?app=accounts.head&cmd=list&msg=$msg");
                }
            }
        } else {	
	    if (getRequest('save')) {
                $data = getUserDataSet("unapproved_sub_acc_head");

                // Set creator fields
                $data["sub_headtype"] = getRequest('sub_headtype');
                $data["child_head"] = getRequest('child_head');
                $data["sl_three_head"] = getRequest('sl_three_head');
                $data["sub_head_name"] = getRequest('sub_head_name');
                $data["head_details"] = getRequest('head_details');
                $data["head_type"] = getRequest('head_type');

                $data["project_id"] = getFromSession('project_id');
                $data["created_by"] = getFromSession('userid');
                $data["created_time"] = date('Y-m-d H:i:s');

                $this->approvedRecordSave("unapproved_sub_acc_head", $data);

                $msg = "Successfully Save Record !!!";
                header("location:?app=accounts.head&cmd=list&msg=$msg");
            }
	
            if ($approved) {
                $TBDArr = $comApp->getRecordInfo("unapproved_sub_acc_head", "id", $approved);
                $data = parseThisValue($TBDArr);

                $accessories_id = $comApp->NewID(SUB_ACC_HEAD_TBL, "sub_id", "A000001", "A", 7);
                $data['sub_id'] = $accessories_id;

                $this->approvedRecordSave(SUB_ACC_HEAD_TBL, $data, true);

                $info = array();
                $info['table'] = "unapproved_sub_acc_head";
                $info['where'] = "id='$approved'";
                delete($info);

                $msg = "Successfully Save Record !!!";
                header("location:?app=accounts.head&cmd=list&msg=$msg");
            }			 
	}
	
	$comdition="head_type!='Customer' AND head_type!='Staff' AND head_type!='Reference' ";
	$srckey = getRequest('srckey');
	if($srckey !=""){$comdition.= " AND (sub_head_name LIKE '%".$srckey."%' OR code LIKE '%" . $srckey . "%')";}

	$GLValue = getRequest('srchead_type');
	if($GLValue!=""){
	    if ($GLValue == "Cost Center") {
		$comdition .= " AND (head_type = '".$GLValue."' OR cost_center_required = '1')";
	    } else {
		$comdition .= " AND head_type = '".$GLValue."'";
	    }
	}
	
	$SLValue = getRequest('srcsub_headtype');
	if($SLValue!=""){$comdition.= " AND sub_headtype ='".$SLValue."'";}

	$f1Name  = "child_head";
	$f1Value = getRequest('srcchild_id');

	$f2Name  = "sl_three_head";
	$f2Value = getRequest('srcsl_three_head');

	$from = getRequest('from'); if($from==""){ $from = 0;}
	$to = getRequest('to'); if($to==""){ $to = 100;}
	$data['brand_list']  	= $comApp->getRecords(SUB_ACC_HEAD_TBL,"sub_id",$comdition,$f1Name,$f1Value,$f2Name,$f2Value,$from,$to);
	$data['upapproved_list'] = $comApp->getRecords("unapproved_sub_acc_head", "id", $comdition, $f1Name, $f1Value, $f2Name, $f2Value, $from, $to);
	$data['totalrecord']  	= $comApp->getTotalRecords(SUB_ACC_HEAD_TBL,"sub_id",$comdition,$f1Name,$f1Value,$f2Name,$f2Value); 
	$data['totalupapprovedrecord'] = $comApp->getTotalRecords("unapproved_sub_acc_head", "id", $comdition, $f1Name, $f1Value, $f2Name, $f2Value);
        $data['totalrecord'] = $data['totalrecord'] + (int)$data['totalupapprovedrecord'];
	$data['catagory_list']	= $comListApp->getCatagoryList();
	$data['project_id'] 	= getFromSession('project_id');
	$data['message'] 	= $msg;
	$data['cmd']     	= getRequest('cmd'); 
	require_once(CURRENT_APP_SKIN_FILE);
	return $data[0];
   } 

   function approvedRecordSave($table, $data = array(), $saveSUB = false)
    {
        $head_type = $data["head_type"];
        if ($head_type == "Current Assets" || $head_type == "Non Current Assets") {
            $data["group_ledger"] = "ASSETS";
        } elseif ($head_type == "Current Liabilities" || $head_type == "Non Current Liabilities") {
            $data["group_ledger"] = "LIABILITIES";
        } elseif ($head_type == "Capital" || $head_type == "Retained earnings" || $head_type == "Retained Earnings") {
            $data["group_ledger"] = "EQUITY";
        } elseif ($head_type == "Operating Revenue" || $head_type == "Non-Operating Revenue") {
            $data["group_ledger"] = "REVENUE";
        } elseif ($head_type == "Direct Expenses" || $head_type == "Indirect Expenses") {
            $data["group_ledger"] = "EXPENSES";
        }

        $info = [
            'table' => $table,
            'data' => $data
        ];

	$costData = $data;

        insert($info);

	if($saveSUB && $costData['make_cost_center'] == 1){
		require_once(CLASS_DIR.'/common.class.php');	
	 	$comApp = new Common(); 
		$sub_id = $comApp->NewID(SUB_ACC_HEAD_TBL, "sub_id", "A000001", "A", 7);
		$costData['sub_id'] = $sub_id;
		$costData['head_type'] = "Cost Center";
		$costData['sub_headtype'] = "S148";
		$costData['child_head'] = "C000164";
		$costData['sl_three_head'] = "S300249";
		unset($costData['code']);
		unset($costData['make_cost_center']);
		unset($costData['cost_center_required']);

		$info2 = [
		    'table' => $table,
		    'data' => $costData
		];
  		//$info2['debug']  =  true;

		insert($info2);
	}
    }
   
   function updateRecord($id)
   {       
	  $requestdata = array();
      	  $requestdata = getUserDataSet(SUB_ACC_HEAD_TBL); 
	  $requestdata['project_id']   = getFromSession('project_id');
	  $requestdata["modified_by"]  = getFromSession('userid');
	  $requestdata["modified_time"]= date('Y-m-d h:i:s');
   	  $info        		       = array();
      	  $info['table']	       = SUB_ACC_HEAD_TBL; 
	  if(getRequest('sub_headtype')==""){
	  $requestdata["sub_headtype"] = " ";
	  }
	  if(getRequest('sl_three_head')==""){
	  $requestdata["sl_three_head"] = " ";
	  }else{
	  $requestdata["sl_three_head"] = getRequest('sl_three_head');
	  }
	  $head_type = getRequest('head_type');	  
	  if($head_type=="Current Assets" || $head_type=="Non Current Assets"){
	  $requestdata["group_ledger"] = "ASSETS"; 
	  }elseif($head_type=="Current Liabilities" || $head_type=="Non Current Liabilities"){
	  $requestdata["group_ledger"] = "LIABILITIES"; 
	  }elseif($head_type=="Capital" || $head_type=="Retained earnings" || $head_type=="Retained Earnings"){
	  $requestdata["group_ledger"] = "EQUITY"; 
	  }elseif($head_type=="Operating Revenue" || $head_type=="Non-Operating Revenue"){
	  $requestdata["group_ledger"] = "REVENUE"; 
	  }elseif($head_type=="Direct Expenses" || $head_type=="Indirect Expenses"){
	  $requestdata["group_ledger"] = "EXPENSES"; 
	  } 	

	if (getRequest('cost_center_required') == 1) {
	    $requestdata["cost_center_required"] = 1;
	}else{
	    $requestdata["cost_center_required"] = 0;
	}	
	
          //dBug($requestdata);
	  $info['data'] 	       = $requestdata;
	  $info['where']	       = "sub_id ='$id'";  
	  //$info['debug']  	       =  true;    
	  $res = update($info); 
          if(!$res) {
        	return false;
          }else{
		return true;  
	  }
   }//EOFn  


/// Start export account head code

// Get Ledger data
function getLedgerData($head_type, $sub_head, $child_head, $sl_three_id) {
    $project_id = getFromSession('project_id');
    $SQL = "head_type='$head_type' AND sub_headtype='$sub_head' AND child_head='$child_head' AND sl_three_head='$sl_three_id' AND project_id='$project_id'";
    $result = select([
        'table' => SUB_ACC_HEAD_TBL,
        'fields' => ['sub_head_name'],
        'where' => $SQL
    ]);

    $data = [];
    foreach ($result as $row) {
        if (!empty($row->sub_head_name)) {
            $data[] = [
                'ledger' => $row->sub_head_name
            ];
        }
    }

    return $data; // Can be empty if no ledger exists
}

// Get SL3 data
function getSL3Data($head_type, $sub_head, $child_head) {
    $project_id = getFromSession('project_id');
    $SQL = "head_type='$head_type' AND sub_head='$sub_head' AND child_id='$child_head' AND project_id='$project_id'";
    $result = select([
        'table' => SUBSIDIARY_STEP3_TBL,
        'fields' => ['sl_three_id', 'sl_three_name'],
        'where' => $SQL,
        'groupby' => ['sl_three_id'],
        'orderby' => ['sl_three_name ASC']
    ]);

    $data = [];
    foreach ($result as $row) {
        $ledgers = $this->getLedgerData($head_type, $sub_head, $child_head, $row->sl_three_id);

        // If ledger exists, add each ledger
        foreach ($ledgers as $ledger) {
            $data[] = [
                'sl3' => $row->sl_three_name ?: '',
                'ledger' => $ledger['ledger']
            ];
        }

        // If no ledger but SL3 name exists, add row with empty ledger
        if (empty($ledgers) && !empty($row->sl_three_name)) {
            $data[] = [
                'sl3' => $row->sl_three_name,
                'ledger' => ''
            ];
        }
        // If SL3 name also empty, skip
    }

    return $data;
}

// Get SL2 data
function getSL2Data($head_type, $sub_head) {
    $project_id = getFromSession('project_id');
    $SQL = "head_type='$head_type' AND sub_head='$sub_head' AND project_id='$project_id'";
    $result = select([
        'table' => CHILD_HEAD_TYPE_TBL,
        'fields' => ['child_id', 'child_head_name'],
        'where' => $SQL,
        'groupby' => ['child_id'],
        'orderby' => ['child_head_name ASC']
    ]);

    $data = [];
    foreach ($result as $row) {
        $sl3Data = $this->getSL3Data($head_type, $sub_head, $row->child_id);

        // Only add if SL3 or Ledger exists
        foreach ($sl3Data as $item) {
            $data[] = [
                'sl2' => $row->child_head_name ?: '',
                'sl3' => $item['sl3'] ?: '',
                'ledger' => $item['ledger'] ?: ''
            ];
        }

        // If no SL3 data but SL2 exists
        if (empty($sl3Data) && !empty($row->child_head_name)) {
            $data[] = [
                'sl2' => $row->child_head_name,
                'sl3' => '',
                'ledger' => ''
            ];
        }
        // Else skip entirely
    }

    return $data;
}

// Get SL1 data
function getSL1Data($head_type) {
    $project_id = getFromSession('project_id');
    $SQL = "head_type='$head_type' AND project_id='$project_id'";
    $result = select([
        'table' => SUB_HEAD_TYPE_TBL,
        'fields' => ['sub_htid', 'sub_head_type'],
        'where' => $SQL,
        'groupby' => ['sub_htid'],
        'orderby' => ['sub_head_type ASC']
    ]);

    $data = [];
    foreach ($result as $row) {
        $sl2Data = $this->getSL2Data($head_type, $row->sub_htid);

        foreach ($sl2Data as $item) {
            // Only include row if at least one value is non-empty
            if (!empty($row->sub_head_type) || !empty($item['SL2']) || !empty($item['SL3']) || !empty($item['Ledger'])) {
                $data[] = [
                    'sl1' => $row->sub_head_type ?: '',
                    'sl2' => $item['sl2'] ?: '',
                    'sl3' => $item['sl3'] ?: '',
                    'ledger' => $item['ledger'] ?: ''
                ];
            }
        }

        // If no SL2 data but SL1 exists, include row with empty children
        if (empty($sl2Data) && !empty($row->sub_head_type)) {
            $data[] = [
                'sl1' => $row->sub_head_type,
                'sl2' => '',
                'sl3' => '',
                'ledger' => ''
            ];
        }
    }

    return $data;
}



    function getAccountToExport()
    {
        if (ob_get_level()) ob_end_clean();
        ob_start(); // Start buffering

        $input = json_decode(file_get_contents('php://input'), true);

        // Validate input
        if (json_last_error() !== JSON_ERROR_NONE || !isset($input['columns'])) {
            echo json_encode([
                'status' => false,
                'message' => 'Invalid JSON data received'
            ]);
            exit;
        }

        $selectedColumns = $input['columns'];
        if (count($selectedColumns) <= 0) {
            echo json_encode([
                'status' => false,
                'message' => 'Select at least one column'
            ]);
            exit;
        }

	$columnMap = [
	    'gl_head' => 'gl_head',
	    'sl1' => 'sl1',
	    'sl2' => 'sl2',
	    'sl3' => 'sl3',
	    'ledger' => 'ledger'
	];

 
	$glHeads = [
	    "Non Current Assets",
	    "Current Assets",
	    "Non Current Liabilities",
	    "Current Liabilities",
	    "Capital",
	    "Retained earnings",
	    "Operating Revenue",
	    "Non-Operating Revenue",
	    "Direct Expenses",
	    "Indirect Expenses",
	    "Opening Balance",
	    "Closing Balance",
	    "Adjustments Balance",
	    "Cost Center"
	];

	$exportData = [];

	foreach($glHeads as $gl) {
	    $sl1Data = $this->getSL1Data($gl);
	    foreach($sl1Data as $row) {
		$row['gl_head'] = $gl; // add GL Head
		$filteredRow = [];
		foreach($selectedColumns as $col) {
		    if(isset($row[$col])) {
		        $filteredRow[$columnMap[$col]] = $row[$col];
		    }else{
			$filteredRow[$columnMap[$col]] = "";
		    }
		}

		// Check if at least one selected column has value
		$hasValue = false;
		foreach ($filteredRow as $value) {
		    if ($value !== '' && $value !== null) {
		        $hasValue = true;
		        break;
		    }
		}

		// Only push non-empty rows
		if ($hasValue) {
		    $exportData[] = $filteredRow;
		}
	    }
	}

        // Output response
        header('Content-Type: application/json');
	echo json_encode([
                'status' => true,
                'data' => $exportData
            ]);
        exit();
    }
/// End export account head code

   function checkHeadCode()
    {
        if (ob_get_level()) ob_end_clean();
        ob_start(); // Start buffering

        $input = json_decode(file_get_contents('php://input'), true);

        // Validate input
        if (json_last_error() !== JSON_ERROR_NONE || !isset($input['code'])) {
            die(json_encode([
                'status' => false,
                'message' => 'Invalid JSON data received'
            ]));
        }

        $code = trim($input['code']);
        $sub_id = trim($input['sub_id']);
        $unapproved_id = trim($input['unapproved_id']);

        $subAccSql = "SELECT 'code' FROM " . SUB_ACC_HEAD_TBL . " WHERE code='$code'";
        if ($sub_id != "") {
            $subAccSql .= " AND sub_id !='$sub_id'";
        }
        $existingSubAcc = mysql_query($subAccSql);

        $unapprovedSubAccSql = "SELECT 'code' FROM unapproved_sub_acc_head WHERE code='$code'";
        if ($unapproved_id != "") {
            $unapprovedSubAccSql .= " AND id !='$unapproved_id'";
        }
        $existingUnapprovedSubAcc = mysql_query($unapprovedSubAccSql);

        if (mysql_num_rows($existingSubAcc) > 0 || mysql_num_rows($existingUnapprovedSubAcc) > 0) {
            $response = [
                'status' => true,
                'message' => 'Head Code Already Exists'
            ];
        } else {
            $response = [
                'status' => false,
                'message' => 'Head Code is Available'
            ];
        }

        // Output response
        header('Content-Type: application/json');
        echo json_encode($response);
        exit();
    }



   function COABulkImport()
    {
        if (ob_get_level()) ob_end_clean();
        ob_start(); // Start buffering

        require_once(CLASS_DIR . '/common.class.php');
        $comApp = new Common();

        $input = json_decode(file_get_contents('php://input'), true);

        // Validate input
        if (json_last_error() !== JSON_ERROR_NONE || !isset($input['rows'])) {
            die(json_encode([
                'status' => 'error',
                'message' => 'Invalid JSON data received'
            ]));
        }

        // Prepare the response array
        $response = [
            'status' => 'success',
            'total' => count($input['rows']),
            'success' => 0,
            'failed' => []
        ];

        // Process each row
        foreach ($input['rows'] as $index => $row) {
            $account_id = trim($row['account_id']);
            $code = trim($row['code']);
            $existingCheck = mysql_query("SELECT * FROM " . SUB_ACC_HEAD_TBL . " WHERE sub_id='$account_id'");

	    $codeEscaped = mysql_real_escape_string($code);

            if ($account_id && mysql_num_rows($existingCheck) > 0) {
                $codeCheckSql = "SELECT 1 FROM " . SUB_ACC_HEAD_TBL . " WHERE code = '$codeEscaped' AND sub_id != '$account_id'";
            } else {
                $codeCheckSql = "SELECT 1 FROM " . SUB_ACC_HEAD_TBL . " WHERE code = '$codeEscaped'";
            }
            $codeCheck = mysql_query($codeCheckSql);

            if (mysql_num_rows($codeCheck) > 0) {
                $response['failed'][] = [
                    'index' => $index,
                    'error' => "Code already exists. Code must be unique."
                ];
                continue;
            }

            $fields = [
                'sub_id' => trim($row['account_id']),
                'code' => trim($row['code']),
                'head_type' => trim($row['gl_head']),
                'sub_headtype' => trim($row['sl_1_head']),
                'child_head' => trim($row['sl_2_head']),
                'sl_three_head' => trim($row['sl_3_head']),
                'sub_head_name' => trim($row['head_name']),
                'head_details' => trim($row['head_details'])
            ];

            $head_type = $fields['head_type'];
            if ($head_type == "Current Assets" || $head_type == "Non Current Assets") {
                $fields["group_ledger"] = "ASSETS";
            } elseif ($head_type == "Current Liabilities" || $head_type == "Non Current Liabilities") {
                $fields["group_ledger"] = "LIABILITIES";
            } elseif ($head_type == "Capital" || $head_type == "Retained earnings" || $head_type == "Retained Earnings") {
                $fields["group_ledger"] = "EQUITY";
            } elseif ($head_type == "Operating Revenue" || $head_type == "Non-Operating Revenue") {
                $fields["group_ledger"] = "REVENUE";
            } elseif ($head_type == "Direct Expenses" || $head_type == "Indirect Expenses") {
                $fields["group_ledger"] = "EXPENSES";
            }

            // Escaping and filtering only non-empty values
            $filteredFields = [];
            foreach ($fields as $key => $value) {
                if ($value !== '' && $value !== null) {
                    $filteredFields[$key] = is_numeric($value) ? floatval($value) : "'" . mysql_real_escape_string($value) . "'";
                }
            }

            if ($account_id && mysql_num_rows($existingCheck) > 0) {
                // Build dynamic UPDATE statement
                $modified_by = mysql_real_escape_string(getFromSession('userid'));
                $modified_time = date('Y-m-d H:i:s');
                $filteredFields['modified_by'] = "'$modified_by'";
                $filteredFields['modified_time'] = "'$modified_time'";

                $setClause = [];
                foreach ($filteredFields as $col => $val) {
                    $setClause[] = "$col = $val";
                }

                if (!empty($setClause)) {
                    $sql = "UPDATE " . SUB_ACC_HEAD_TBL . " SET " . implode(', ', $setClause) . " WHERE sub_id='$account_id'";
                    $result = mysql_query($sql);
                } else {
                    $result = true; // nothing to update, but consider as success
                }

            } else {
		if ($filteredFields['head_type'] == "" || $filteredFields['sub_headtype'] == "" || $filteredFields['child_head'] == "" || $filteredFields['sl_three_head'] == "" || $filteredFields['sub_head_name'] == "") {
                    $response['failed'][] = [
                        'index' => $index,
                        'error' => "GL Head/SL-1 Head/SL-2 Head/SL-3 Head/Head Name must be fillable"
                    ];
                    continue;
                }

                // Generate new ID
                $new_id = $comApp->NewID(SUB_ACC_HEAD_TBL, "sub_id", "A000001", "A", 7);
                $created_by = mysql_real_escape_string(getFromSession('userid'));
                $created_at = date('Y-m-d H:i:s');

                // Add required insert fields
		$project_id = getFromSession('project_id');
                $filteredFields['project_id'] = "'$project_id'";
                $filteredFields['sub_id'] = "'$new_id'";
                $filteredFields['created_by'] = "'$created_by'";
                $filteredFields['created_time'] = "'$created_at'";

                $columns = implode(', ', array_keys($filteredFields));
                $values = implode(', ', array_values($filteredFields));

                $sql = "INSERT INTO " . SUB_ACC_HEAD_TBL . " ($columns) VALUES ($values)";

                $result = mysql_query($sql);
            }


            if (!$result) {
                $response['failed'][] = [
                    'index' => $index,
                    'error' => mysql_error()
                ];
            } else {
                if (mysql_affected_rows() <= 0) {
                    $response['failed'][] = [
                        'index' => $index,
                        'error' => 'No rows inserted or duplicate entry'
                    ];
                } else {
                    $response['success']++;
                }
            }
        }

        // Output response
        header('Content-Type: application/json');
        echo json_encode($response);
        exit();
    }

   function COATree(){
	$data['cmd'] = getRequest('cmd');
        require_once(TEMPLATES_SKINS . '/coa_tree_preview.html');
        return true;
   }


   function loadSubHeadType($head_type)
   {	  
	  $project_id 	   = getFromSession('project_id');  
	  $info            = array();
	  $info['table']   = SUB_HEAD_TYPE_TBL;
	  $info['fields']  =  array('sub_htid','sub_head_type');
	  $SQL = "head_type='$head_type' AND project_id='$project_id' ";
	  $info['where']   = $SQL; 
	  $info['groupby'] = array("sub_htid");
	  $info['orderby'] = array("sub_head_type ASC");
	  //$info['debug']   = true;	
	  $result          = select($info);
	  $data            = array();	
	  if(count($result)){
		 foreach($result as $key=>$value){
			$data[$key][]        = $value;
		 }
	  }				
	  foreach($data as $i=>$v){
		 $subject_idname .= $v[0]->sub_htid.'#####'.$v[0]->sub_head_type.'@@@';
	  }
	  echo $subject_idname;	
   }   
   function loadChildHeadType()
   {	  
	  $project_id 	   = getFromSession('project_id'); 
	  $head_type 	   = getRequest('head_type'); 
	  $sub_head 	   = getRequest('sub_head');
	  $info            = array();
	  $info['table']   = CHILD_HEAD_TYPE_TBL;
	  $info['fields']  =  array('child_id','child_head_name');
	  $SQL = "head_type='$head_type' AND sub_head = '$sub_head' AND project_id='$project_id'";
	  $info['where']   = $SQL; 
	  $info['groupby'] = array("child_id");
	  $info['orderby'] = array("child_head_name ASC");
	  //$info['debug']   = true;	
	  $result          = select($info);
	  $data            = array();	
	  if(count($result)){
		 foreach($result as $key=>$value){
			$data[$key][] = $value;
		 }
	  }				
	  foreach($data as $i=>$v){
		 $subject_idname .= $v[0]->child_id.'#####'.$v[0]->child_head_name.'@@@';
	  }
	  echo $subject_idname;	
   }   
   function loadSubsidiary3Htype()
   {	  
	  $project_id 	   = getFromSession('project_id'); 
	  $head_type 	   = getRequest('head_type'); 
	  $sub_head 	   = getRequest('sub_head');
	  $child_head 	   = getRequest('child_head');
	  $info            = array();
	  $info['table']   = SUBSIDIARY_STEP3_TBL;
	  $info['fields']  = array('sl_three_id','sl_three_name');
	  $SQL = "head_type='$head_type' AND sub_head = '$sub_head' AND child_id = '$child_head' AND project_id='$project_id'";
	  $info['where']   = $SQL; 
	  $info['groupby'] = array("sl_three_id");
	  $info['orderby'] = array("sl_three_name ASC");
	  //$info['debug']   = true;	
	  $result          = select($info);
	  $data            = array();	
	  if(count($result)){
		 foreach($result as $key=>$value){
			$data[$key][] = $value;
		 }
	  }				
	  foreach($data as $i=>$v){
		 $subject_idname .= $v[0]->sl_three_id.'#####'.$v[0]->sl_three_name.'@@@';
	  }
	  echo $subject_idname;	
   }
  
   function deleteItem(){
	require_once(CLASS_DIR.'/common.class.php');	
	$comApp = new Common(); 
	$sub_id = getRequest('id'); 
	$unapproved_id = getRequest('unapproved_id');
        if ($sub_id && !$unapproved_id) {
	    if(!userCondition()){
		$msg = "You are not authorized !!!";
	      	header("location:index.php?accounts.head&cmd=add&msg=$msg");
	      	exit;
	    }
            $comApp->deleteRecord(SUB_ACC_HEAD_TBL, "sub_id", $sub_id, "accounts.head", "list");
        } else if (!$sub_id && $unapproved_id) {
            $comApp->deleteRecord("unapproved_sub_acc_head", "id", $unapproved_id, "accounts.head", "list");
        } else {
            header("location:?app=$redirect&cmd=$cmd&msg=$msg&deleted=no");
        }
   }  
} // End class
?>
