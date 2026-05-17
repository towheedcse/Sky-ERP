<?php
class Product
{
   function run() {     
		$cmd    = getRequest('cmd');
		$u_t_id = getFromSession('u_type_id');
		if($u_t_id == 101) 
		{      
		  switch($cmd) { 
		  case 'add'              : $screen = $this->showEditor($msg); break;
		  case 'edit'             : $screen = $this->showEditor("Edit Page");    break;			 
		  case 'doUpdate'         : $screen = $this->showEditor($msg); break;
		  case 'delete'           : $screen = $this->deleteItem(); break;
		  case 'approve'          : $screen = $this->approveProduct(); break;
		  case 'bulk_import'      : $screen = $this->productBulkImport(); break;
		  case 'check_product_code' : $screen = $this->checkProductCode(); break;
		  case 'getProductToExport' : $this->getProductToExport(); break;
		  case 'loadSubCatagory'  : $this->loadSubCatagory(trim(getRequest('catagory_id'))); break;
		  case 'loadCatagory'  : $this->loadCatagory(trim(getRequest('catagory_id'))); break; 
		  default                 : $cmd = 'list'; $screen = $this->showEditor($msg);   break;
		  }
		}else if($u_t_id == 107) 
		{      
		  switch($cmd) { 
			case 'add'            : $screen = $this->showEditor($msg); break;
			case 'edit'           : $screen = $this->showEditor("Edit Page");    break;
			case 'doUpdate'       : $screen = $this->showEditor($msg); break;
			case 'approve'        : $screen = $this->approveProduct(); break;
		        case 'check_product_code' : $screen = $this->checkProductCode(); break;
		  	case 'getProductToExport' : $this->getProductToExport(); break;

			case 'loadSubCatagory': $this->loadSubCatagory(trim(getRequest('catagory_id'))); break; 
		        case 'loadCatagory'  : $this->loadCatagory(trim(getRequest('catagory_id'))); break; 
			default               : $cmd = 'list'; $screen = $this->showEditor($msg);   break;
		  }
		}elseif($u_t_id == 102) 
		{      
		  switch($cmd) { 
			case 'add'        : $screen = $this->showEditor($msg); break;
			case 'edit'       : $screen = $this->showEditor("Edit Page");    break;			 
			case 'doUpdate'   : $screen = $this->showEditor($msg); break;
			case 'approve'    : $screen = $this->approveProduct(); break;
		  	case 'check_product_code' : $screen = $this->checkProductCode(); break;
		 	 case 'getProductToExport' : $this->getProductToExport(); break;
			case 'loadSubCatagory' : $this->loadSubCatagory(trim(getRequest('catagory_id'))); break;
		  	case 'loadCatagory'  : $this->loadCatagory(trim(getRequest('catagory_id'))); break; 
			default           : $cmd = 'list'; $screen = $this->showEditor($msg);   break;
		  }
		}elseif($u_t_id == 103 || $u_t_id == 104  || $u_t_id == 105 || $u_t_id == 106 || $u_t_id == 108 || $u_t_id == 109) 
		{      
		  switch($cmd) { 
		  case 'loadSubCatagory' : $this->loadSubCatagory(trim(getRequest('catagory_id'))); break;
		  case 'loadCatagory'  : $this->loadCatagory(trim(getRequest('catagory_id'))); break;    
		  }
		}else {
      		header("location:index.php?app=user_home&msg=You are not authorised !!!");
      	} 
		
		return true;
   }


   function getProductToExport()
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
            'product_id' => 'p.product_id',
            'product_code' => 'p.product_code',
            'product_name' => 'p.product_name',
            'product_desc' => 'p.product_desc',
            'catagory' => 'c.catagory_name',
            'subcatagory' => 'sc.subcatagory_name',
            'brand' => 'b.brand_name',
            'product_type' => 'p.product_type',
            'product_catagory' => 'p.product_catagory',
            'm_unit' => 'p.m_unit',
            'purchase_unit_price' => 'p.purchase_unit_price',
            'unit_old_price' => 'p.unit_old_price',
            'unit_price' => 'p.unit_price',
            'reorder_level' => 'p.reorder_level',
            'weight' => 'p.weight'
        ];

        /* ================= BUILD SELECT ================= */

        $selectFields = [];

        foreach ($selectedColumns as $col) {
            if (isset($columnMap[$col])) {
                $selectFields[] = $columnMap[$col] . " AS `$col`";
            }
        }

        if (empty($selectFields)) {
            $selectFields[] = "p.product_id AS product_id";
        }

        $selectSql = implode(", ", $selectFields);

        /* ================= SQL QUERY ================= */

        $sql = "
        SELECT $selectSql
        FROM " . PRODUCT_TBL . " AS p
        LEFT JOIN " . CATAGORY_TBL . " AS c 
            ON c.catagory_code = p.catagory
        LEFT JOIN subcatagory AS sc 
            ON sc.subcatagory_id = p.subcatagory
        LEFT JOIN " . BRAND_TBL . " AS b 
            ON b.brand_id = p.brand_code
        ORDER BY p.product_code ASC
    ";


        $query = mysql_query($sql);

        if (!$query) {
            echo json_encode([
                'status' => false,
                'message' => mysql_error()
            ]);
            exit;
        }

        $data = [];
        while ($row = mysql_fetch_assoc($query)) {
            $data[] = $row;
        }


        // Output response
        header('Content-Type: application/json');
	echo json_encode([
                'status' => true,
                'data' => $data
            ]);
        exit();
    }

   function checkProductCode()
    {
        if (ob_get_level()) ob_end_clean();
        ob_start(); // Start buffering

        $input = json_decode(file_get_contents('php://input'), true);

        // Validate input
        if (json_last_error() !== JSON_ERROR_NONE || !isset($input['product_code'])) {
            die(json_encode([
                'status' => false,
                'message' => 'Invalid JSON data received'
            ]));
        }

        $product_code = trim($input['product_code']);
        $product_id = trim($input['product_id']);

        if ($product_id != "") {
            $existingCheck = mysql_query("SELECT * FROM " . PRODUCT_TBL . " WHERE product_code='$product_code' AND product_id !='$product_id'");
        } else {
            $existingCheck = mysql_query("SELECT * FROM " . PRODUCT_TBL . " WHERE product_code='$product_code'");
        }

        if (mysql_num_rows($existingCheck) > 0) {
            $response = [
                'status' => true,
                'message' => 'Product Code Already Exists'
            ];
        } else {
            $response = [
                'status' => false,
                'message' => 'Product Code is Available'
            ];
        }

        // Output response
        header('Content-Type: application/json');
        echo json_encode($response);
        exit();
    }


  function showEditor()
  {
	 require_once(CLASS_DIR.'/common.class.php');	
	 $comApp = new Common(); 
	 require_once(CLASS_DIR.'/common.list.class.php');	
	 $comListApp 	= new CommonList(); 
	 $product_id = getRequest('id');	 
	 $data               = array();		
	 if($product_id){
	 $TBDArr			= $comApp->getRecordInfo(PRODUCT_TBL,"product_id",$product_id);      
	 $TBDArr 			= parseThisValue($TBDArr);
	 $data        		= array_merge(array(),$TBDArr);		 			
	 if(getRequest('save')){
		$comApp->updateRecord(PRODUCT_TBL,"product_id",$product_id,"","","modified_by","modified_time","product","list");
		$msg="Successfully Update Record !!!";
		header("location:?app=product&cmd=list&msg=$msg");	      	
	 } 
	} else {		
	if(getRequest('save')) {
		$accessories_id = $comApp->NewID(PRODUCT_TBL,"product_id","P000000","P",7);
		$comApp->saveRecord(PRODUCT_TBL,"product_id",$accessories_id,"","","created_by","created_time","product","list");
		$msg="Successfully Save Record !!!";
		header("location:?app=product&cmd=list&msg=$msg");     		       		      	
	 }			 
	}
	$f1Value = getRequest('srckey');
	$data['product_list']  	= $this->getProductList(getRequest('from'),getRequest('to'));
	$data['totalrecord']  	= $this->getTotalProductList(); 
	$data['main_catagory_list']= $comListApp->getMainCatagoryList();
	$data['catagory_list']	= $comListApp->getCatagoryList();
	$data['brand_list'] 	= $comListApp->getBrandList();
	//$data['pcatagory_list'] = $comListApp->getProductCatagoryList();
	$data['pclass_list'] 	= $comListApp->getProductClassList();
	$data['type_list'] 	= $comListApp->getProductTypeList();
	$data['uom_list'] 	= $comListApp->getUOMList();
	$data['message'] 		= $msg;
	$data['cmd']     		= getRequest('cmd'); 
	require_once(CURRENT_APP_SKIN_FILE);
	return $data[0];
   }  

   function productBulkImport()
    {
	if (ob_get_level()) ob_end_clean();
        ob_start(); // Start buffering

	require_once(CLASS_DIR . '/common.class.php');
	$comApp = new Common();

	require_once(CLASS_DIR . '/common.list.class.php');
	$clistApp = new CommonList();

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
            $product_id = trim($row['product_id']);
            $product_code = trim($row['product_code']);
            $existingCheck = mysql_query("SELECT * FROM " . PRODUCT_TBL . " WHERE product_id='$product_id' OR product_code ='$product_code'");

	    if(empty($product_id)){
		if ($prow = mysql_fetch_assoc($existingCheck)) {
		    $product_id = $prow['product_id'];
	        }
	    }

            $fields = [
                'product_code' => trim($row['product_code']),
		'catagory' => trim($row['category']),
                'subcatagory' => trim($row['subcategory']),
                'project_id' => trim($row['project_id']),
                'product_type' => trim($row['product_type']),
                'brand_code' => trim($row['brand_code']),
                'product_class' => trim($row['product_class']),
                'product_catagory' => trim($row['product_category']),
                'product_name' => trim($row['product_name']),
                'm_unit' => trim($row['unit']),
                'unit_price' => $row['unit_price'],
                'purchase_unit_price' => $row['purchase_price'],
                'reorder_level' => $row['reorder_level'],
                'weight' => trim($row['weight']),
                'status' => $row['status']
            ];

            // Escaping and filtering only non-empty values
            $filteredFields = [];
            foreach ($fields as $key => $value) {
                if ($value !== '' && $value !== null) {
                    $filteredFields[$key] = is_numeric($value) ? floatval($value) : "'" . mysql_real_escape_string($value) . "'";
                }
            }


            if ($product_id && mysql_num_rows($existingCheck) > 0) {
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
                    $sql = "UPDATE product SET " . implode(', ', $setClause) . " WHERE product_id = '$product_id'";
                    $result = mysql_query($sql);
                } else {
                    $result = true; // nothing to update, but consider as success
                }

            } else {
		if ($filteredFields['catagory'] == "" || $filteredFields['brand_code'] == "" || $filteredFields['product_name'] == "") {
                    $response['failed'][] = [
                        'index' => $index,
                        'error' => "Product Name/Category/Brand Code must be fillable"
                    ];
		    continue;
                }
                // Generate new ID
                $new_id = $comApp->NewID(PRODUCT_TBL, "product_id", "P000000", "P", 7);
                $created_by = mysql_real_escape_string(getFromSession('userid'));
                $created_at = date('Y-m-d H:i:s');

                // Add required insert fields
                $filteredFields['product_id'] = "'$new_id'";
                $filteredFields['created_by'] = "'$created_by'";
                $filteredFields['created_time'] = "'$created_at'";

                $columns = implode(', ', array_keys($filteredFields));
                $values = implode(', ', array_values($filteredFields));

                $sql = "INSERT INTO product ($columns) VALUES ($values)";

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



   function getProductList($from,$to){ 
		if($from == "" && $to == ""){$from=0; $to=50;}
		$srcbrand_code 	= getRequest('srcbrand_code');
		$src_main_catagory= getRequest('src_main_catagory');
		$srccatagory 	= getRequest('srccatagory');
		$src_subcatagory= getRequest('src_subcatagory');
		$srckey 	= getRequest('srckey');				
		$project_id     = getFromSession('project_id');  
		$info           = array(); 
		$info['table'] = PRODUCT_TBL.' p
		    LEFT JOIN '.MAIN_CATAGORY_TBL.' mc ON p.main_catagory = mc.main_cat_code
		    LEFT JOIN '.CATAGORY_TBL.' c ON p.catagory = c.catagory_code
		    LEFT JOIN '.BRAND_TBL.' b ON p.brand_code = b.brand_id'; 
		$info['fields'] = array('p.*,c.catagory_code','c.project_id','mc.main_cat_name','c.catagory_name','p.brand_code','b.brand_name','c.created_by','c.created_time');		
		$sql="c.project_id = '".$project_id."'";
		if($srcbrand_code !=""){
			$sql.=" AND p.brand_code = '$srcbrand_code'";
		}
		if($src_main_catagory !=""){
			$sql.=" AND p.main_catagory = '$src_main_catagory'";
		}
		if($srccatagory !=""){
			$sql.=" AND p.catagory = '$srccatagory'";
		}
		if($src_subcatagory !=""){
			$sql.=" AND p.subcatagory = '$src_subcatagory'";
		}
		if($srckey !=""){
			$sql.=" AND (p.product_name LIKE '%$srckey%' OR p.product_desc LIKE '%$srckey%' OR p.product_code LIKE '%$srckey%')";
		}
		$info['where']  =$sql;
		$info['orderby'] = array("p.approval_status ASC, p.product_id DESC LIMIT $from,$to");
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
   
   function getTotalProductList() { 
		$srcbrand_code 	= getRequest('srcbrand_code');
		$src_main_catagory= getRequest('src_main_catagory');
		$srccatagory 	= getRequest('srccatagory');
		$src_subcatagory= getRequest('src_subcatagory');	
		$srckey 		= getRequest('srckey');					
		$project_id     = getFromSession('project_id');  
		$info           = array();    
		$info['table'] = PRODUCT_TBL.' p
		    LEFT JOIN '.MAIN_CATAGORY_TBL.' mc ON p.main_catagory = mc.main_cat_code
		    LEFT JOIN '.CATAGORY_TBL.' c ON p.catagory = c.catagory_code
		    LEFT JOIN '.BRAND_TBL.' b ON p.brand_code = b.brand_id';
		$info['fields'] = array('p.*,c.catagory_code','c.project_id','mc.main_cat_name','c.catagory_name','p.brand_code','b.brand_name','c.created_by','c.created_time');		
		$sql="c.project_id = '".$project_id."'";
		if($srcbrand_code !=""){
			$sql.=" AND p.brand_code = '$srcbrand_code'";
		}
		if($src_main_catagory !=""){
			$sql.=" AND p.main_catagory = '$src_main_catagory'";
		}
		if($srccatagory !=""){
			$sql.=" AND p.catagory = '$srccatagory'";
		}
		if($src_subcatagory !=""){
			$sql.=" AND p.subcatagory = '$src_subcatagory'";
		}
		if($srckey !=""){
			$sql.=" AND (p.product_name LIKE '%$srckey%' OR p.product_desc LIKE '%$srckey%' OR p.product_code LIKE '%$srckey%')";
		}
		$info['where']  =$sql;
		$info['orderby'] = array("p.product_id DESC");
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
   function deleteItem(){
	require_once(CLASS_DIR.'/common.class.php');	
	$comApp = new Common(); 
	if(!userCondition(true)){
		$msg = "You are not authorized !!!";
	      	header("location:index.php?app=product&msg=$msg");
	      	exit;
	    }

	$product_id = getRequest('id');
	$comApp->deleteRecord(PRODUCT_TBL,"product_id",$product_id,"product","list"); 
   }  
   function loadSubCatagory($catagory_id)
   {	  
	if (ob_get_level()) ob_end_clean();
        ob_start(); // Start buffering

	  $catagory_id	   = trim($catagory_id);
	  $project_id 	   = getFromSession('project_id');  
	  $info            = array();
	  $info['table']   = SUB_CATAGORY_TBL;
	  $info['fields']  =  array('subcatagory_id','subcatagory_name');
	  $info['where']   = "`catagory_id`='$catagory_id' AND project_id = '$project_id'";
	  $info['groupby'] = array("subcatagory_name");
	  //$info['debug']   = true;	
	  $result          = select($info);
	  $data            = array();	
	  if(count($result)){
		 foreach($result as $key=>$value){
			$data[$key][]        = $value;
		 }
	  }
	  foreach($data as $i=>$v){
		 $catagory_idname .= $v[0]->subcatagory_id.'#####'.$v[0]->subcatagory_name.'@@@';
	  }
	// Output response
	header('Content-Type: application/json');
	echo $catagory_idname;
	exit();	
  }
   function loadCatagory($catagory_id)
   {	  
	if (ob_get_level()) ob_end_clean();
        ob_start(); // Start buffering

	  $catagory_id	   = trim($catagory_id);
	  $project_id 	   = getFromSession('project_id');  
	  $info            = array();
	  $info['table']   = CATAGORY_TBL;
	  $info['fields']  =  array('catagory_code','catagory_name');
	  $info['where']   = "`main_catagory_id`='$catagory_id' AND project_id = '$project_id'";
	  $info['groupby'] = array("catagory_name");
	  //$info['debug']   = true;	
	  $result          = select($info);
	  $data            = array();	
	  if(count($result)){
		 foreach($result as $key=>$value){
			$data[$key][]        = $value;
		 }
	  }
	  foreach($data as $i=>$v){
		 $catagory_idname .= $v[0]->catagory_code.'#####'.$v[0]->catagory_name.'@@@';
	  }
	 

	// Output response
	header('Content-Type: application/json');
	echo $catagory_idname;
	exit();	
  }
   // -------------------------------------------------------
   //  Approve a product  (sets approval_status = 1)
   // -------------------------------------------------------
   function approveProduct()
   {
       if (ob_get_level()) ob_end_clean();
       ob_start();

       $product_id  = trim(getRequest('id'));
       if (empty($product_id)) {
           header('Content-Type: application/json');
           echo json_encode(['status' => false, 'message' => 'Invalid product ID']);
           exit;
       }

       $approved_by = mysql_real_escape_string(getFromSession('userid'));
       $approved_at = date('Y-m-d H:i:s');

       $sql = "UPDATE " . PRODUCT_TBL . "
               SET    approval_status = 1,
                      approved_by     = '$approved_by',
                      approved_at     = '$approved_at'
               WHERE  product_id      = '$product_id'
               AND    approval_status = 0";

       $result = mysql_query($sql);

       header('Content-Type: application/json');
       if ($result && mysql_affected_rows() > 0) {
           echo json_encode(['status' => true,  'message' => 'Product approved successfully']);
       } else {
           echo json_encode(['status' => false, 'message' => mysql_error() ?: 'Already approved or product not found']);
       }
       exit;
   }

} // End class
?>
