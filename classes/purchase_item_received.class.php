<?php
class PurchaseItemReceived
{   
   function run()
   {
      $cmd = getRequest('cmd');
	  $u_t_id = getFromSession('u_type_id');
      if( ($u_t_id == 101) || ($u_t_id ==102) || ($u_t_id == 103) || ($u_t_id == 106)) // 1 = sysadmin, 2 = admin, 3 = project admin
      {
      	switch ($cmd)
      	{
      	   case 'add'		: $this->showEditor(); break;
	   case 'addlc'		: $this->showEditor(); break;
	   case 'edit'		: $this->showEditor(); break;
	   case 'pur_dtl'	: $this->showEditor4PurchaseDetails(); break;
      	   case 'loadProduct'  	: $this->loadProduct4Catagory(trim(getRequest('brand_id'))); break; 
      	   case 'loadPOInfo'  	: $this->loadPOInfo(trim(getRequest('supplier'))); break;  
      	   case 'get_uprice'  	: $this->loadUnitePrice(trim(getRequest('product_id'))); break;  
	   case 'getProductDtl' : $this->loadProductDtl(trim(getRequest('product_id'))); break;
	   case 'savePurchase'	: $this->saveReceivedItem(); break;
	   case 'saveGRNPurchase'	: $this->savePurchaseItem(); break;
	   case 'print_vouchar'	: $screen = $this->showPrintEditor($msg); break;  
	   case 'delete'        : $screen = $this->deleteRecord(getRequest('id')); break;
	   case 'save_tmp_grn'  : $this->saveTempGRN(); break;
	   case 'delTempGrn'    : $this->delTempGRN(); break;
	   case 'editTempPoItem': $this->editTempPoItem(); break;
	   case 'addPOProduct'  : $this->addPOProduct(); break;

      	   default              :$cmd = 'list'; $screen = $this->showEditor();   break;
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
  
   function showPrintEditor($msg = null) {      	  
      
		$voucher_no 	= getRequest('voucher_no');  
		if ($voucher_no) {
			$advArr 					= $this->getPurchaseMasterInfo($voucher_no);
			$advArr 					= parseThisValue($advArr); 
			$data   					= array_merge(array(), $advArr); 
		  
			$data['item_list']	= $this->getProductList($voucher_no);
			$data['message'] = $msg;
			$data['cmd']     = getRequest('cmd');
			require_once(PURCHASE_VOUCHAR_SKIN);      
			return true;
	   }else{
		require_once(PRINT_VOUCHAR_SKIN);
	   }
   }
     
   function showEditor($msg = null) {
	   require_once(CLASS_DIR.'/common.list.class.php');	
	   $comListApp 	= new CommonList();
   	   $data                	= array();  
	  
	   $supplier_list 	        = $comListApp->getSupplierList(); 
	   $supplier_list_payable 	= $comListApp->getSupplierListPayable(); 
	   $data['supplier_list'] 	= array_merge($supplier_list, $supplier_list_payable);
    	
	   $data['product_list'] 	= $comListApp->getProductList();	
	   $data['cat_list'] 		= $this->getCatagoryList();	      
	   $data['brand_list'] 		= $comListApp->getBrandList();	   
	   $data['currency_list']       = $this->getCurrencyList();    	
	   $data['depo_list'] 		= $comListApp->getDeliveryPointList();  	
	   $data['equipment_list'] 	= $comListApp->getAccountHeadList("Non Current Assets","S126");
	   $data['raw_material_list']   = $comListApp->getAccountHeadList("Current Assets", NULL, "C000055");
           $data['fg_list']             = $comListApp->getAccountHeadList("Current Assets", NULL, "C000056");
           $data['maintanance_list']    = $comListApp->getAccountHeadList("Current Assets", NULL, "C000154");
	   $data['cogsheadlist'] 	= $comListApp->getAccountHeadList("Cost Center");
	   $data['po_list']		= $this->getPOList();
	   $data['tmp_items']		= $this->getTempGRNPurchase();

	   $data['cmd']         	= getRequest('cmd');
		if(getRequest('cmd')=="addlc"){       
			
      		require_once(CLASS_DIR.'/supplier.class.php');	
	  		$supApp = new Supplier(); 
        		$data['country_list']     = $supApp->getCountryList();
	   		require_once(LC_OPENING_SKIN); 
		}else{			
	   		require_once(CURRENT_APP_SKIN_FILE); 
		}
	   return $data[0];
   }

   function getPOList(){
	$info = array();
        $info['table'] = PURCHASE_OREDR_MASTER_TBL;
        $info['where'] = "complete_status = 0 AND approved_status = 1";
        $info['orderby'] = array("voucher_no");

        //$info['debug']  = true;
        $result = select($info);
        $data = array();

        if (count($result)) {
            foreach ($result as $value) {
                $data[] = $value;
            }
        }

	return $data;
   }

   function getTempGRNPurchase(){
	$project_id  	= getFromSession('project_id');
	$str1="<table width='100%' align='center' bgcolor='#99CC66'>
	<tr style='color:#fff;' bgcolor='#00B000' height=28>
	  <td width='1%' nowrap><div align='left'>SL</div></td>
	  <td width='20%' nowrap><div align='left'>Product Name</div></td>
	  <td width='10%' nowrap><div align='left'>Catagory</div></td>
	  <td width='10%' nowrap><div align='left'>Brand</div></td>
	  <td width='8%' nowrap><div align='right'>Serial</div></td>
	  <td width='8%' nowrap><div align='right'>Warranty</div></td>		  
	  <td width='10%' nowrap><div align='right'>Qty</div></td>				  
	  <td width='7%' nowrap align='center'>Option</td>
	</tr>";
	$total_value = 0; $product_discount=0; $TotalQty=0; $TotalFreeQty=0; $sl=1;
	$getSql		= "SELECT * FROM ".TEMP_GRN_TBL." WHERE created_by = '".getFromSession('userid')."' AND project_id='".$project_id."'";
	$gres 		= mysql_query($getSql);
	$supplier_id = "";
	$storeId = "";
	$purchaseDate = "";
	$poVoucherNoArr = [];
	while($row = mysql_fetch_array($gres)){
	extract($row);
	$TotalQty+=$qty; 
	$total_value+=$total;
	$poVoucherNoArr[] = $po_voucher_no;
	if($supplier_id == ""){
		$supplier_id = $supplier;
	}
	if($storeId == ""){
		$storeId = $store_id;
	}
	if($purchaseDate == "" && $purchase_date != "0000-00-00"){
		$purchaseDate = $purchase_date;
	}
	$str2.="
	<tr style='color:#000000' bgcolor='#fff'>
	  <td width='1%' nowrap>$sl</td>
	  <td width='20%' nowrap align='left'>$product_name</td>
	  <td width='10%' nowrap align='left'>$catagoryname</td>
	  <td width='10%' nowrap align='left'>$brandname</td>
	  <td width='8%' nowrap><div align='right'>$serial</div></td>
	  <td width='8%' nowrap><div align='right'>$warranty</div></td>
	  <td width='10%' nowrap><div align='right'>$qty $munit</div></td>				  				  
	  <td width='7%' nowrap align='center'><a href=\"javascript:void(0)\" onclick=\"editTempPo('$tmp_id')\" style=\"margin: 3px 6px;\"><img src=\"images/common/icons/edit.gif\"></a><a href=\"?app=purchase_item_received&cmd=delTempGrn&id=$tmp_id&pod_id=$pod_id&po_voucher_no=$po_voucher_no\"><img src=\"images/common/icons/delete.gif\"></a></td>
	</tr>"; $sl++;
	}
	$str3="
	<tr style='color:#000;' bgcolor='#CCCCCC' height=25>
	  <td colspan='6' nowrap><div align='right'>Total </div></td>
	  <td nowrap align='right'>$TotalQty $munit</td>
	  <td nowrap align='center'>&nbsp;</td>
	</tr>
	</table>";
	$poVoucherNoArr = array_values(array_unique($poVoucherNoArr));
        $poVoucherNoArr = implode(",", $poVoucherNoArr);
	$poVoucherNoArr =  trim($poVoucherNoArr, ",");

	if ($purchaseDate) {
	    $date = DateTime::createFromFormat('Y-m-d', $purchaseDate);
	    $purchaseDate = $date->format('d-m-Y');
	}

	$response = [
	     "table"=> $str1.$str2.$str3,
	     "total_value"=> $total_value,
	     "supplier_id"=> $supplier_id,
	     "storeId"=> $storeId,
	     "purchase_date"=> $purchaseDate,
	     "poVoucherNo"=> $poVoucherNoArr,
	];
	return $response;
  }


  function saveTempGRN(){
	if (ob_get_level()) ob_end_clean();
	ob_start(); // Start buffering

	$edit_product_id = getRequest('edit_product_id');
	$tmp_id = getRequest('tmp_id');
	$po_voucher_no = getRequest('po_voucher_no');
	$pod_id = getRequest('pod_id');
	$max_qty = getRequest('max_qty');
	$edit_product_qty = getRequest('edit_product_qty');

	$project_id = getFromSession('project_id');

	if($tmp_id != ""){
		if($edit_product_id != getRequest('productid')){	
			$requestdata = array();
			$requestdata['productid'] 	= getRequest('productid');
			$sql 		= "SELECT product_name,catagory,brand_code,m_unit FROM ".PRODUCT_TBL." WHERE product_id = '".$requestdata['productid']."'";
			$row 		= mysql_fetch_object(mysql_query($sql));
			$requestdata['product_name'] 	= $row->product_name;	
			$requestdata['catagory'] 	= $row->catagory;		
			$requestdata['catagoryname'] 	= getRequest('catagoryname');
			$requestdata['brand_id'] 	= $row->brand_code;	
			$requestdata['brandname'] 	= getRequest('brandname');
			$requestdata['details'] 	= getRequest('details');
			$requestdata['munit'] 		= $row->m_unit;
			$requestdata['serial'] 		= getRequest('serial');
			$requestdata['warranty'] 	= getRequest('warranty');
			$requestdata['qty'] 		= getRequest('qty');
			$requestdata['free_qty'] 	= getRequest('free_qty');
			$requestdata['unit_price'] 	= getRequest('unit_price');
			$requestdata['unit_discount'] 	= getRequest('unit_discount');
			$requestdata['discount_amount'] = getRequest('discount_amount');
			$requestdata['total'] 		= getRequest('total');
			
			$requestdata['po_voucher_no'] 	= "";	
			$requestdata['pod_id'] 		= "";
			$requestdata['max_qty'] 	= "";
	
			$info        		=  array();
			$info['table']	= TEMP_GRN_TBL;
			$info['data'] 	= $requestdata;    
			$info['where']	= "tmp_id ='$tmp_id'"; 
			//$info['debug']  	=  true;
			$res = update($info);

			$this->updatePOdetailsQty($edit_product_qty,$po_voucher_no,$pod_id,$max_qty);
		}else{
			$requestdata = array();
			$requestdata['details'] 	= getRequest('details');
			$requestdata['serial'] 		= getRequest('serial');
			$requestdata['warranty'] 	= getRequest('warranty');
			$requestdata['qty'] 		= getRequest('qty');
			$requestdata['free_qty'] 	= getRequest('free_qty');
			$requestdata['unit_price'] 	= getRequest('unit_price');
			$requestdata['unit_discount'] 	= getRequest('unit_discount');
			$requestdata['discount_amount'] = getRequest('discount_amount');
			$requestdata['total'] 		= getRequest('total');
			
			$info        	=  array();
			$info['table']	= TEMP_GRN_TBL;
			$info['data'] 	= $requestdata;    
			$info['where']	= "tmp_id ='$tmp_id'"; 
			//$info['debug']  	=  true;
			$res = update($info);

			$qty = getRequest('qty');
			if($qty < $max_qty){
				$newQty = (float)$max_qty - (float)$qty;
				$this->updatePOdetailsQty($newQty,$po_voucher_no,$pod_id,$max_qty);
			}
		}
	}else{
		//======= Insert into tamp ========	
		$requestdata = array();
		$requestdata = getUserDataSet(TEMP_GRN_TBL);
		
		$requestdata['project_id'] 	= $project_id;
		$requestdata['supplier'] 	= getRequest('supplier');
		$requestdata['store_id'] 	= getRequest('store_id');
		$requestdata['purchase_date'] 	= formatDate(getRequest('purchase_date'));
		$requestdata['quotation_no'] 	= getRequest('quotation_no');
		$requestdata['truck_no'] 	= getRequest('truck_no');
		$requestdata['productid'] 	= getRequest('productid');

		$sql 		= "SELECT product_name,catagory,brand_code,m_unit FROM ".PRODUCT_TBL." WHERE product_id = '".$requestdata['productid']."'";
		$row 		= mysql_fetch_object(mysql_query($sql));

		$requestdata['product_name'] 	= $row->product_name;	
		$requestdata['catagory'] 	= $row->catagory;		
		$requestdata['catagoryname'] 	= getRequest('catagoryname');
		$requestdata['brand_id'] 	= $row->brand_code;	
		$requestdata['brandname'] 	= getRequest('brandname');
		$requestdata['details'] 	= getRequest('details');
		$requestdata['munit'] 		= $row->m_unit;
		$requestdata['serial'] 		= getRequest('serial');
		$requestdata['warranty'] 	= getRequest('warranty');
		$requestdata['qty'] 		= getRequest('qty');
		$requestdata['free_qty'] 	= getRequest('free_qty');
		$requestdata['unit_price'] 	= getRequest('unit_price');
		$requestdata['unit_discount'] 	= getRequest('unit_discount');
		$requestdata['discount_amount'] = getRequest('discount_amount');
		$requestdata['total'] 		= getRequest('total');
		
		$requestdata['created_by'] 	= getFromSession('userid');	

		$info        =  array();
		$info['table']	= TEMP_GRN_TBL;
		$info['data'] 	= $requestdata;     
		//$info['debug']  	=  true;
		$res = insert($info);
	}

	$response = $this->getTempGRNPurchase();
	header('Content-Type: application/json');
        echo json_encode($response);
        exit();
   }

   function updatePOdetailsQty($addQty,$po_voucher_no,$pod_id,$max_qty){
	    if ($pod_id != "") {
	        $sql = "SELECT * FROM ".PURCHASE_ORDER_DETAILS_TBL." WHERE id = '$pod_id'";
	        $result = mysql_fetch_object(mysql_query($sql));
		$poInitQty = (float)$result->init_qty;
		$poTotalQty = (float)$result->qty;

		if($poInitQty != (float)$max_qty){
			$newPOQty = $poTotalQty + (float)$addQty;
		}else{
			$newPOQty = (float)$addQty;
		}

		$requestSPDdata['qty'] = $newPOQty;
		$requestSPDdata['complete_status'] = 0;
		$info = array();
		$info['table'] = PURCHASE_ORDER_DETAILS_TBL;
		$info['data'] = $requestSPDdata;
		$info['where'] = "id='$pod_id'";
		//$info['debug']  	=  true;
		update($info);
	    }
	    if ($po_voucher_no != "") {
		$requestMasterdata['complete_status'] = 0;
		$info = array();
		$info['table'] = PURCHASE_OREDR_MASTER_TBL;
		$info['data'] = $requestMasterdata;
		$info['where'] = "voucher_no='$po_voucher_no'";
		//$info['debug']  	=  true;
		update($info);
	    }
   }

   function addPOProduct()
    {
        if (ob_get_level()) ob_end_clean();
        ob_start(); // Start buffering

        $input = json_decode(file_get_contents('php://input'), true);

        // Validate input
        if (json_last_error() !== JSON_ERROR_NONE || !isset($input['voucher_no'])) {
            die(json_encode([
                'status' => false,
                'message' => 'Invalid JSON data received'
            ]));
        }

        $project_id = getFromSession('project_id');
        $voucher_no = trim($input['voucher_no']);

        $purchase_date = trim($input['purchase_date']);
        $quotation_no = trim($input['quotation_no']);
        $warranty = trim($input['warranty']);

        if ($voucher_no != "") {
            $info = array();
            $info['table'] = PURCHASE_ORDER_DETAILS_TBL;
            $info['where'] = "voucher_no = '$voucher_no' AND complete_status = '0'";
            //$info['debug']  = true;
            $result = select($info);

	    $sql = "SELECT pod.*,pom.supplier_id,sprm.store_id FROM " . PURCHASE_ORDER_DETAILS_TBL . " AS pod
		LEFT JOIN " . PURCHASE_OREDR_MASTER_TBL . " AS pom ON pom.voucher_no=pod.voucher_no
		LEFT JOIN " . SPR_PURCHASE_MASTER_TBL . " AS sprm ON sprm.voucher_no=pod.spr_no WHERE pod.voucher_no = '$voucher_no' AND pod.complete_status = '0'";
            $result = mysql_query($sql);


            while($row = mysql_fetch_assoc($result)) {
		    $value = (object)$row;
                    $pod_id = $value->id;
		    $product_id = $value->product_id;

                    $requestdata = array();
                    $requestdata = getUserDataSet(TEMP_GRN_TBL);
                    $requestdata['po_voucher_no'] = $voucher_no;
                    $requestdata['pod_id'] = $pod_id;
                    $requestdata['project_id'] = $project_id;
                    $requestdata['supplier'] = $value->supplier_id;
                    $requestdata['store_id'] = $value->store_id;
                    $requestdata['purchase_date'] = formatDate($purchase_date);
                    $requestdata['quotation_no'] = $quotation_no;
                    $requestdata['payment_note'] = $payment_note;
                    $requestdata['warranty'] = $warranty;

                    $sql = "SELECT po.product_name,ct.catagory_name,b.brand_name FROM " . PRODUCT_TBL . " AS po
                        LEFT JOIN " . CATAGORY_TBL . " AS ct ON ct.catagory_code  = po.catagory
                        LEFT JOIN " . BRAND_TBL . " AS b ON b.brand_id  = po.brand_code";
                    $sql .= " WHERE product_id = '$product_id '";

                    $row = mysql_fetch_object(mysql_query($sql));

		    $requestdata['productid'] = $product_id;
                    $requestdata['product_name'] = $row->product_name;
                    $requestdata['catagory'] = $value->catagory_id;
                    $requestdata['catagoryname'] = $row->catagory_name;
                    $requestdata['brand_id'] = $value->brand_id;
                    $requestdata['brandname'] = $row->brand_name;
                    $requestdata['munit'] = $value->m_unit;
                    $requestdata['qty'] = (float)$value->qty;
                    $requestdata['max_qty'] = $requestdata['qty'];
                    $requestdata['unit_price'] = (float)$value->unit_price;
                    $requestdata['total'] = $requestdata['unit_price'] * $requestdata['qty'];
                    $requestdata['created_by'] = getFromSession('userid');
                    $info = array();
                    $info['table'] = TEMP_GRN_TBL;
                    $info['data'] = $requestdata;
                    //$info['debug']  	=  true;
                    $res = insert($info);

                    $requestSPDdata['complete_status'] = 1;
                    $info = array();
                    $info['table'] = PURCHASE_ORDER_DETAILS_TBL;
                    $info['data'] = $requestSPDdata;
                    $info['where'] = "voucher_no='$voucher_no' AND id='$pod_id'";
                    //$info['debug']  	=  true;
                    update($info);
            }

            $info = array();
            $info['table'] = PURCHASE_ORDER_DETAILS_TBL;
            $info['where'] = "voucher_no = '$voucher_no' AND complete_status = '0'";
            //$info['debug']  = true;
            $result = select($info);

            if (count($result) <= 0) {
                $requestMasterdata['complete_status'] = 1;
                $info = array();
                $info['table'] = PURCHASE_OREDR_MASTER_TBL;
                $info['data'] = $requestMasterdata;
                $info['where'] = "voucher_no='$voucher_no'";
                //$info['debug']  	=  true;
                update($info);
            }

            $response = [
                'status' => true,
                'message' => "Product Added Successfully!!!",
                'data' => $this->getTempGRNPurchase()
            ];
        } else {
            $response = [
                'status' => false,
                'message' => "Missing Voucher No !!!"
            ];
        }

        // Output response
        header('Content-Type: application/json');
        echo json_encode($response);
        exit();

    }

   function editTempPoItem(){
	if (ob_get_level()) ob_end_clean();
        ob_start(); // Start buffering

        $input = json_decode(file_get_contents('php://input'), true);

        // Validate input
        if (json_last_error() !== JSON_ERROR_NONE || !isset($input['tmpId'])) {
            die(json_encode([
                'status' => false,
                'message' => 'Invalid JSON data received'
            ]));
        }

        $project_id = getFromSession('project_id');
        $tmp_id = trim($input['tmpId']);
        

        if ($tmp_id != "") {
            $sql = "SELECT * FROM " . TEMP_GRN_TBL . " WHERE tmp_id = '$tmp_id'";
            $result = mysql_fetch_object(mysql_query($sql));

	    if(isset($result->tmp_id)){
		$response = [
			'status' => true,
			'message' => "Successful!!",
			'data' => $result
	    	];
	    }else{
		    $response = [
			'status' => false,
			'message' => "Record not found!!"
		    ];
	    }
        } else {
            $response = [
                'status' => false,
                'message' => "Missing Voucher No !!!"
            ];
        }

        // Output response
        header('Content-Type: application/json');
        echo json_encode($response);
        exit();
    }



   function delTempGRN(){
	$tmp_id = $_REQUEST['id'];
	$pod_id = $_REQUEST['pod_id'];
        $po_voucher_no = $_REQUEST['po_voucher_no'];

	if($tmp_id!=""){
	    if ($pod_id != "") {
		$sqlTmp = "SELECT * FROM ".TEMP_GRN_TBL." WHERE tmp_id ='$tmp_id'";
        	$tmpResult = mysql_fetch_object(mysql_query($sqlTmp));
		$delQty = $tmpResult->qty;

		$sql = "SELECT * FROM ".PURCHASE_ORDER_DETAILS_TBL." WHERE id='$pod_id'";
        	$spdResult = mysql_fetch_object(mysql_query($sql));
		$spdPrevQty = $spdResult->qty;
		$spdInitQty = $spdResult->init_qty;

		if($spdPrevQty != $spdInitQty){
			$newQty = (float)$spdPrevQty + (float)$delQty;
			$requestSPDdata['qty']= $newQty;
		}
		
                $requestSPDdata['complete_status'] = 0;
                $info = array();
                $info['table'] = PURCHASE_ORDER_DETAILS_TBL;
                $info['data'] = $requestSPDdata;
                $info['where'] = "id='$pod_id'";
                //$info['debug']  	=  true;
                update($info);
            }
            if ($po_voucher_no != "") {
                $requestMasterdata['complete_status'] = 0;
                $info = array();
                $info['table'] = PURCHASE_OREDR_MASTER_TBL;
                $info['data'] = $requestMasterdata;
                $info['where'] = "voucher_no='$po_voucher_no'";
                //$info['debug']  	=  true;
                update($info);
            }
	    $dsql = "DELETE FROM ".TEMP_GRN_TBL." WHERE tmp_id ='".$tmp_id."'";
	    mysql_query($dsql);
	}		
	header("location:?app=purchase_item_received&cmd=add");
   }

  function insertPurchaseDetails($voucher_no)
  {
		require_once(CLASS_DIR.'/common.list.class.php');	
		$comlistApp 				= new CommonList();
		$requestdata 				= array();
		$arr_catagory_product_id	= array();

		$project_id  				= getFromSession('project_id');
		$store_id					= getRequest('store_id');

      	$arr_catagory_product_id	= getRequest('input_catagory_product_id');
      	$arr_podtl_id        		= getRequest('input_podtl_id');
      	$arr_serial        			= getRequest('input_serial');
      	$arr_warranty        		= getRequest('input_warranty');
      	$arr_m_unit        			= getRequest('input_m_unit');
      	$arr_total_unit        		= getRequest('input_total_unit');
      	$arr_unit_price				= getRequest('input_unit_price');
      	$arr_qty      				= getRequest('input_qty');
      	$arr_total_bag      		= getRequest('input_total_bag');
      	$arr_total_value       		= getRequest('input_total_value');      

      	for($i=0;$i<count($arr_catagory_product_id);$i++)
      	{

		  $catagory_product_sep = $arr_catagory_product_id[$i];		
		  $requestdata['project_id'] = $project_id;       	  

      	  for($j=0;$j<count($catagory_product_sep);$j++)
		  {
				$catagory_product = explode("###",$catagory_product_sep);
				$catagoryid  = array();
				$productid = array();				
				$staff_no = array();			  

				$catagoryid['c'] = $catagory_product[0];				
				$brandid['b']  	 = $catagory_product[1];				
				$productid['p']  = $catagory_product[2];
			}

		   foreach($catagoryid as $val){
      		    $requestdata['catagory'] = $val; $catagory_id = $val;	
      	   }      				
		   foreach($brandid as $val){
      		    $requestdata['brand_id']=$val; $brand_id = $val;
      	   }
      	   foreach($productid as $val){
      		    $requestdata['product'] =$val; $product_id = $val;
      	   }	   
		   foreach($arr_serial as $key => $val){
      	   	  if($catagory_product_sep==$key){
      		   	$requestdata['serial'] = $val; $serial = $val;	
      		  }
      	   }		   
		  foreach($arr_podtl_id as $key => $val){
      	   	  if($catagory_product_sep==$key){
      		   	$requestdata['po_detail_id'] = $val; $po_detail_id = $val;	
      		  }
      	   }  
		   foreach($arr_warranty as $key => $val){
      	   	  if($catagory_product_sep==$key){
      		   	$requestdata['warranty'] = $val; $warranty = $val;	
      		  }
      	   }	   
		   foreach($arr_m_unit as $key => $val){
      	   	  if($catagory_product_sep==$key){
      		   	$requestdata['m_unit'] = $val;	
      		  }
      	   }	   
		   foreach($arr_total_unit as $key => $val){
      	   	  if($catagory_product_sep==$key){
      		   	$requestdata['total_unit'] = $val;	
      		  }
      	   }
		   foreach($arr_unit_price as $key => $val){
      	   	  if($catagory_product_sep==$key){
      		   	$requestdata['unit_price'] = $val;	
      		  }
      	   }
      	   foreach($arr_qty as $key => $val){
				if($catagory_product_sep==$key){
					 $requestdata['rec_qty'] = $val; $productQty = $val;
				}
      	   }
      	   foreach($arr_total_bag as $key => $val){
				if($catagory_product_sep==$key){
					 $requestdata['total_bag'] = $val;	
				}
      	   }
      	   $requestdata['currency'] = $currency;	
      	   foreach($arr_total_value as $key => $val){
      	   	  if($catagory_product_sep==$key){
      		     $requestdata['total'] = $val;	
      		  }
      	   }
			//$rowCnt = 0;
		    $requestdata['created_by'] 		  = getFromSession('userid');
		    $requestdata['created_date']      = date('Y-m-d h:i:s');  
			$project_id						  = getFromSession('project_id'); 
		    $requestdata['project_id']        = $project_id;
		    $requestdata['voucher_no']        = getRequest('voucher_no');
		    $requestdata['po_no']        	  = getRequest('po_no');
		    $requestdata['lc_no']        	  = getRequest('lc_no');
			
		    $info        	=  array();
		    $info['table']	= PURCHASE_RECEIVED_TBL;
	  	    $info['data'] 	= $requestdata;      
			//dumpvar($info);	     
			    
		    //$info['debug']  	=  true;
	    	$res = insert($info);	
			$totalCR  = $this->getTotalCreditStock($product_id,getFromSession('project_id'));
			$totalDR  = $this->getTotalDebitStock($product_id,getFromSession('project_id'));					 
			$balance  = (($totalDR + $productQty) - $totalCR);	
			$purchase_date = formatDate(getRequest('purchase_date')); 
			
			$PDsql = "SELECT SUM(rec_qty) as ttl_rec_qty FROM ".PURCHASE_RECEIVED_TBL." WHERE 
			voucher_no ='".getRequest('voucher_no')."' AND po_detail_id = '$po_detail_id' AND project_id = '$project_id'";
			$PDrow 			= mysql_fetch_object(mysql_query($PDsql));
			$ttl_rec_qty 	= $PDrow->ttl_rec_qty;
			
			$getPrsql = "SELECT discount_amount FROM ".PURCHASE_DETAILS_TBL." WHERE pur_detail_id='".$po_detail_id."' AND project_id = '$project_id' AND product='".$product_id."'";
			$getPrrow = mysql_fetch_object(mysql_query($getPrsql));
			$discount_amount= $getPrrow->discount_amount;
			$StockAmount 	= ($requestdata['unit_price']*$productQty);
			if($discount_amount>0){
			$DisAmount = ($discount_amount*$productQty);
			$description = "Get Discount from Purchase";
			//========= Capital Dr ==========
			$capital_head 	 = $comlistApp->getMainCapitalId(getFromSession('project_id'));
			$totalCapitalCR  = $this->getTotalCreditAmount($capital_head,getFromSession('project_id'));
			$totalCapitalDR  = $this->getTotalDebitAmount($capital_head,getFromSession('project_id'));					 
			$Capitalbalance  = (($totalCapitalDR+$DisAmount)-$totalCapitalCR);					 
			$comlistApp->saveAccJournal($voucher_no,$capital_head,"Acc","Purchase Discount",getFromSession('project_id'),$description,$DisAmount,0,$Capitalbalance,0,$purchase_date);
			}
			
			$pdusql = "UPDATE ".PURCHASE_DETAILS_TBL." SET serial='$serial',warranty='$warranty',rec_qty='".$ttl_rec_qty."' WHERE 
			voucher_no='".getRequest('voucher_no')."' AND pur_detail_id='".$po_detail_id."' AND product='".$product_id."'";
			mysql_query($pdusql);
			$po_detail_id="";		
			$Prosql = "SELECT product_type FROM ".PRODUCT_TBL." WHERE product_id = '$product_id' AND project_id = '$project_id'";
			$Prorow = mysql_fetch_object(mysql_query($Prosql));
			$product_type 		= $Prorow->product_type;
			$equipment_auto_out = getFromSession('equipment_auto_out');  
			$po_no 				= getRequest('po_no');
			if($equipment_auto_out==1 && $product_type=="Equipment"){				
				$this->saveStockJournal($po_no,$voucher_no,$project_id,$product_id,$serial,$warranty,$product_type,$requestdata['unit_price'],$requestdata['m_unit'],$productQty,0,$balance,$purchase_date,$store_id);
				$Autobalance  = ($totalDR - ($totalCR+$productQty));
				$this->saveStockJournal($po_no,$voucher_no,$project_id,$product_id,$serial,$warranty,$product_type,$requestdata['unit_price'],$requestdata['m_unit'],0,$productQty,$Autobalance,$purchase_date,$store_id);
			}	
					
			$inventory_auto_out = getFromSession('inventory_auto_out'); 
			if($inventory_auto_out==1 && $product_type=="Invetory Item"){				
				$this->saveStockJournal($po_no,$voucher_no,$project_id,$product_id,$serial,$warranty,$product_type,$requestdata['unit_price'],$requestdata['m_unit'],$productQty,0,$balance,$purchase_date,$store_id);
				$Autobalance  = ($totalDR - ($totalCR+$productQty));
				$this->saveStockJournal($po_no,$voucher_no,$project_id,$product_id,$serial,$warranty,$product_type,$requestdata['unit_price'],$requestdata['m_unit'],0,$productQty,$Autobalance,$purchase_date,$store_id);
			}elseif($inventory_auto_out==0 && $product_type=="Invetory Item"){
			//=== Stock Dr =====
			$StockId 	 = $comlistApp->getStockId(getFromSession('project_id'));
			$totalStockCr  = $this->getTotalCreditAmount($StockId,getFromSession('project_id'));
			$totalStockDr  = $this->getTotalDebitAmount($StockId,getFromSession('project_id'));					 
			$StockBalance  = (($totalStockDr+$StockAmount)-$totalStockCr);	$description = "Purchase Product";				 
			$comlistApp->saveAccJournal($voucher_no,$StockId,"Stock","Purchase Product",getFromSession('project_id'),$description,$StockAmount,0,$StockBalance,0,$purchase_date);	
			$this->saveStockJournal($po_no,$voucher_no,$project_id,$product_id,$serial,$warranty,$product_type,$requestdata['unit_price'],$requestdata['m_unit'],$productQty,0,$balance,$purchase_date,$store_id);
			}
			
			if($product_type=="Sales Item" || $product_type=="Raw Materials"){
			//=== Stock Dr =====
			$StockId 	   = $comlistApp->getStockId(getFromSession('project_id'));
			$totalStockCr  = $this->getTotalCreditAmount($StockId,getFromSession('project_id'));
			$totalStockDr  = $this->getTotalDebitAmount($StockId,getFromSession('project_id'));					 
			$StockBalance  = (($totalStockDr+$StockAmount)-$totalStockCr);	$description = "Purchase Product";				 
			$comlistApp->saveAccJournal($voucher_no,$StockId,"Stock","Purchase Product",getFromSession('project_id'),$description,$StockAmount,0,$StockBalance,0,$purchase_date);
				
			$this->saveStockJournal($po_no,$voucher_no,$project_id,$product_id,$serial,$warranty,$product_type,$requestdata['unit_price'],$requestdata['m_unit'],$productQty,0,$balance,$purchase_date,$store_id);
			}
	   } // end 1st foreach 

  } //End of the function savePaymentDetails()


  function savePurchaseItem(){
		mysql_query("START TRANSACTION;");
		$store_id = getRequest('store_id');

		if($store_id!=""){
			$grn_voucher = $this->createGRNVoucharID();
			$voucher_no = $this->saveDebitVouchar();
			$PreviousPartyBalance = $this->saveCreditVouchar($voucher_no,$grn_voucher);
			$this->insertPurchaseMaster($voucher_no,$PreviousPartyBalance,$grn_voucher);
			$this->insertGRNPurchaseDetails($voucher_no); 
		}		
		if($voucher_no!=""){
			mysql_query("COMMIT;");
			header("location:index.php?app=purchase&cmd=print_vouchar&voucher_no=".$voucher_no);
		//header("location:index.php?app=purchase_item_received&cmd=print_vouchar&voucher_no=".$voucher_no);	
		}else{
			mysql_query("ROLLBACK;");
			header("location:index.php?app=purchase_item_received&cmd=add");
		}
   }

function insertPurchaseMaster($voucher_no,$PreviousPartyBalance,$grn_voucher){
	require_once(CLASS_DIR.'/purchase.class.php');	
	$parchApp = new Purchase();
	$project_id  = getFromSession('project_id');
	$requestdata = array();	
	$requestdata = getUserDataSet(PURCHASE_MASTER_TBL);	
	if($mode_of_payment =="Check"){
		$requestdata['check_no'] = formatDate(getRequest('check_no'));
		$requestdata['check_issue_date'] = formatDate(getRequest('check_issue_date'));
	}
	if(getRequest('lcopening_date')!=""){
		$requestdata['lcopening_date'] = formatDate(getRequest('lcopening_date'));
	}
	if(getRequest('advanced_paid_amount')>0){
		$requestdata['purchase_type']  	= "Advanced Paid";
		$requestdata['paid_amount']  	= (getRequest('paid_amount')+getRequest('advanced_paid_amount'));
	}else{
		$requestdata['purchase_type']  	= getRequest('purchase_type');
	}
	
	$requestdata['cost_center']= getRequest('cost_center');		  
	$requestdata['item_received_amount']= getRequest('total_value');
	$requestdata['transaction_type']  = "Payment";    
	$requestdata['purchase_date'] 	= formatDate(getRequest('purchase_date')); 
	$requestdata['voucher_no']        = $voucher_no; 
	$dueAmount = $requestdata['item_received_amount']-getRequest('paid_amount');
	if($PreviousPartyBalance>0 && $dueAmount>=0){
		$supplier = getRequest('supplier'); $purchase_date = formatDate(getRequest('purchase_date')); 			
		$restofAmount	= $parchApp->saveAdjustSupplierReceibavle($supplier,$voucher_no,$dueAmount,$purchase_date); 
		$adjustAmount 	= ($requestdata['item_received_amount']-$restofAmount);
		$requestdata['due'] = ($requestdata['net_payble']-$adjustAmount);
	}else{
		$adjustAmount = $PreviousPartyBalance;	 
	}
	$purchase_date = formatDate(getRequest('purchase_date')); 	  
	$general_discount_amount 	= getRequest('general_discount_amount');
	$exclusive_discount_amount 	= getRequest('exclusive_discount_amount');
	$additional_discount 		= getRequest('additional_discount');
	$product_discount 		= getRequest('discount');
	$requestdata['total_value']     = getRequest('total_value');
	$requestdata['product_discount'] = getRequest('discount');
	$TotalDiscount = ($general_discount_amount+$exclusive_discount_amount+$additional_discount+$product_discount);	
	$requestdata['discount']	= $TotalDiscount;
	
	require_once(CLASS_DIR.'/common.list.class.php');	
	$comlistApp  = new CommonList();
	
	if($TotalDiscount >0){
	 $DisAmount   = $TotalDiscount;
	 //========= Purchase Discount Cr =========
	 $DiscountId 	  = $comlistApp->getPurchaseDiscountId($project_id);
	 if($DiscountId){	 	 
	 $description	  = "Give discount with purchase item";
	 $DiscountBL 	  = $comlistApp->getAccounceBalance($DiscountId,$project_id);
	 $DiscountBalance = ($DiscountBL-$TotalDiscount);
	 $comlistApp->saveAccJournal($voucher_no,$DiscountId,"Purchase","Purchase discount",$project_id,$description,0,$TotalDiscount,$DiscountBalance,0,$purchase_date);
	 }
	}//End TotalDiscount

	$totalVatAmount = getRequest('vat_amount');
        $vatHeadID = getRequest('vat_type');
        if ($totalVatAmount > 0 && $vatHeadID != "") {
            $totalPartyCR = $comlistApp->getTotalCreditAmount($vatHeadID, $project_id);
            $totalPartyDR = $comlistApp->getTotalDebitAmount($vatHeadID, $project_id);
            $balance = (($totalPartyDR + $totalVatAmount) - $totalPartyCR);
            $transaction_type = "VAT on purchase";
            $description = "";
            $comlistApp->saveAccJournal($voucher_no, $vatHeadID, "Purchase", $transaction_type, $project_id, $description, $totalVatAmount, 0, $balance, 0, $purchase_date);
        }
        $totalATAmount = getRequest('AT_amount');
        $ATHeadID = getRequest('AT_type');
        if ($totalATAmount > 0 && $ATHeadID != "") {
            $totalPartyCR = $comlistApp->getTotalCreditAmount($ATHeadID, $project_id);
            $totalPartyDR = $comlistApp->getTotalDebitAmount($ATHeadID, $project_id);
            $balance = (($totalPartyDR + $totalATAmount) - $totalPartyCR);
            $transaction_type = "Advance Tax";
            $description = "";
            $comlistApp->saveAccJournal($voucher_no, $ATHeadID, "Purchase", $transaction_type, $project_id, $description, $totalATAmount, 0, $balance, 0, $purchase_date);
        }
		
        $requestdata['vat_percentage'] = getRequest('vat_percent');  
	$requestdata['vat_amount'] = getRequest('vat_amount');
	$requestdata['at_percentage'] = getRequest('AT_percent');  
	$requestdata['at_amount'] = getRequest('AT_amount');

	$requestdata['grn_voucher']= $grn_voucher;

	$requestdata['previour_balance']= $PreviousPartyBalance;		
	$requestdata['project_id']      = getFromSession('project_id');    
	$requestdata['created_by']      = getFromSession('userid');
	$requestdata['created_date']    = date('Y-m-d h:i:s');
	$info        		=  array();
	$info['table']	= PURCHASE_MASTER_TBL;
	$info['data'] 	= $requestdata;     
	//$info['debug']  	=  true;
	$res = insert($info);
			
   }

   //==================== saveDebitVouchar ====================
  function saveDebitVouchar()
  {     
	require_once(CLASS_DIR.'/common.list.class.php');	
	$comlistApp = new CommonList();		  
	$mode_of_payment = "Payable"; //getRequest('mode_of_payment');
	$requestdata = array();
	$requestdata = getUserDataSet(DEVIT_VOUCHAR_TBL);	
	if($mode_of_payment =="Check"){
		$requestdata['bank_name'] 		= getRequest('bank_name');
		$requestdata['acc_no'] 			= getRequest('acc_no');
		$requestdata['check_no'] 		= getRequest('check_no');
		$requestdata['check_issue_date'] 	= formatDate(getRequest('check_issue_date'));     
		$requestdata['account_head']     	= getRequest('supplier');         
		$requestdata['head_type']     	 	= "Supplier";  
		$requestdata['debit']        	 	= getRequest('paid_amount'); 
		$requestdata['credit']        	 	= 0; 
	}elseif($mode_of_payment=="Cash"){
		$requestdata['bank_name'] 		= "";
		$requestdata['acc_no'] 			= "";
		$requestdata['check_no'] 		= "";
		$requestdata['check_issue_date'] 	= "";     
		$requestdata['account_head']     	= getRequest('supplier'); 
		$requestdata['debit']        	 	= getRequest('paid_amount'); 
		$requestdata['credit']        	 	= 0;         
		$requestdata['head_type']     	 	= "Supplier";  
	}elseif($mode_of_payment=="Payable"){
	//======= Stock Cr ======
		$DrAccountId 	 = "S126"; //getRequest('inventory_type'); 
		if($DrAccountId=="S126"){
		 $DrAccountId 	 = getRequest('inventory_id');
		}
		$requestdata['bank_name'] = "";
		$requestdata['acc_no'] = "";
		$requestdata['check_no'] = "";
		$requestdata['check_issue_date'] = "";
		$requestdata['account_head']     	= $DrAccountId;
		$requestdata['debit']        		= getRequest('due'); 
		$requestdata['credit']        		= 0;     
		$requestdata['head_type']     		= "Acc"; 
	}
	$requestdata['transaction_type']  = "Payment"; 
	$requestdata['project_id']        = getFromSession('project_id');    
	$requestdata['created_by']        = getFromSession('userid');	
	$requestdata['created_date']      = formatDate(getRequest('purchase_date'));

	$voucher_no = $this->createPIVoucharID();
	if($voucher_no!="" && $voucher_no!="PI999999"){
		$requestdata['voucher_no']  = $voucher_no;
	}else{
		if($voucher_no=="PI999999"){
		$msg = "ID overflow !!!"; header("location:index.php?app=purchase_item_received&cmd=add&msg=$msg");
		}elseif($voucher_no==""){
		$msg = "ID is Empty !!! Try again"; header("location:index.php?app=purchase_item_received&cmd=add&msg=$msg");
		}
		exit;
	}
	$info        		=  array();
	$info['table']	= DEVIT_VOUCHAR_TBL;
	$info['data'] 	= $requestdata;     
	//$info['debug']  	=  true;
	$res = insert($info);

	if($res['affected_rows']) {
		return $voucher_no;
	}else {	
		header("location:index.php?purchase_item_received&cmd=add");	
	} 

 }//EOFn 


function saveCreditVouchar($voucher_no,$grn_voucher)
 {
   require_once(CLASS_DIR.'/common.list.class.php');	
   $comlistApp = new CommonList();      
   $mode_of_payment = "Payable"; //getRequest('mode_of_payment');
   $project_id  = getFromSession('project_id'); 
   $requestdata = array();
   $requestdata = getUserDataSet(CREDIT_VOUCHAR_TBL);	
   if($mode_of_payment =="Check"){
	$requestdata['bank_name'] 		= getRequest('bank_name');
	$requestdata['acc_no'] 			= getRequest('acc_no');
	$requestdata['check_no'] 		= getRequest('check_no');
	$requestdata['check_issue_date'] 	= formatDate(getRequest('check_issue_date'));
	$requestdata['account_head']     	= getRequest('acc_no'); 
	$requestdata['debit']        		= 0; 
	$requestdata['credit']        		= getRequest('paid_amount');     
	$requestdata['head_type']     		= "Check";   
   }elseif($mode_of_payment=="Cash"){
	$requestdata['bank_name'] = "";
	$requestdata['acc_no'] = "";
	$requestdata['check_no'] = "";
	$requestdata['check_issue_date'] = "";
	if(getFromSession('u_type_id')==102){ // 102 = Pharmecy
	$requestdata['account_head']    = getFromSession('cash_id'); 	
	}else{
	$requestdata['account_head']    = $comlistApp->getCashId($project_id); 
	}			
	$requestdata['debit']        	= 0; 
	$requestdata['credit']        	= getRequest('paid_amount');     
	$requestdata['head_type']     	= "Acc";   
   }elseif($mode_of_payment=="Payable"){
	//======= Party Dr ======
	$requestdata['bank_name'] = "";
	$requestdata['acc_no'] = "";
	$requestdata['check_no'] = "";
	$requestdata['check_issue_date'] = "";
	$requestdata['account_head']     = getRequest('supplier'); 
	$requestdata['credit']        	 = getRequest('due'); 
	$requestdata['debit']        	 = 0;     
	$requestdata['head_type']     	 = "Supplier"; 
  }
  $requestdata['transaction_type']  = "Payment"; 
  $requestdata['project_id']        = getFromSession('project_id');    
  $requestdata['created_by']        = getFromSession('userid'); 
  $requestdata['created_date']      = formatDate(getRequest('purchase_date')); 	
  $requestdata['voucher_no']   	    = $voucher_no;
 
  $info        		=  array();
  $info['table']	= CREDIT_VOUCHAR_TBL;
  $info['data'] 	= $requestdata;     
  //$info['debug']  	=  true;
  $res = insert($info);
  $created_date = $requestdata['created_date']; 
  $cost_center = ""; //getRequest('cost_center');

  if($res['affected_rows']) {
	$CrAmount    = getRequest('paid_amount');
	$due 	     = getRequest('due');
	$project_id  = getFromSession('project_id');
	$description = getRequest('description');
	if(getRequest('advanced_paid_amount')==0||getRequest('advanced_paid_amount')==""){
	  if($mode_of_payment=="Cash"){ 
		if(getRequest('due') >0){					
		//======= Supplier Cr ======	
		$description = getRequest('description');
		if($description==""){ $description = "Amount payable against purchase item";}	
		 $fullCr 	= getRequest('net_payble');
		 $PartyAcc_head = getRequest('supplier'); 
		 $totalPartyCR  = $comlistApp->getTotalCreditAmount($PartyAcc_head,$project_id);
		 $totalPartyDR  = $comlistApp->getTotalDebitAmount($PartyAcc_head,$project_id);	
		 $PreviousPartyBalance = ($totalPartyDR-$totalPartyCR);					 
		 $PartyBalance  = ($totalPartyDR-($totalPartyCR+$fullCr));					 
		 $this->saveAccountJournal($voucher_no,$PartyAcc_head,"Supplier",$project_id,$description,0,$fullCr,$PartyBalance,1,$created_date,$cost_center,$grn_voucher);
		 //======= Supplier Dr ======	
		 $description = getRequest('description');
		 if($description==""){ $description = "Paid amount by cash against purchase item";}			
		 $DrAmount = getRequest('paid_amount');
		 $PartyAcc_head1 = getRequest('supplier'); 
		 $totalPartyCR1  = $comlistApp->getTotalCreditAmount($PartyAcc_head1,$project_id);
		 $totalPartyDR1  = $comlistApp->getTotalDebitAmount($PartyAcc_head1,$project_id);
		 $PartyBalance1  = (($totalPartyDR1+$DrAmount)-$totalPartyCR1);					 
		 $this->saveAccountJournal($voucher_no,$PartyAcc_head1,"Supplier",$project_id,$description,$DrAmount,0,$PartyBalance1,1,$created_date);	
		//============== Cash Cr ===============
		$description = getRequest('description');
		if($description==""){ $description = "Paid amount by cash against purchase item";}		
		if(getFromSession('u_type_id')==102){ // 102 = Pharmecy
		$acc_head     	= getFromSession('cash_id'); 	
		}else{
		$acc_head     	= $comlistApp->getCashId($project_id); 
		}
		$totalCR  = $comlistApp->getTotalCreditAmount($acc_head,$project_id);
		$totalDR  = $comlistApp->getTotalDebitAmount($acc_head,$project_id);
		$balance  = ($totalDR-($totalCR+$CrAmount));					 
		$this->saveAccountJournal($voucher_no,$acc_head,"Cash",$project_id,$description,0,$CrAmount,$balance,0,$created_date);	
								
		}elseif(getRequest('due')==0){	
		//======= Supplier Cr ======	
		$description = getRequest('description');
		if($description==""){ $description = "Amount payable against purchase item";}
		$fullCr = getRequest('net_payble');
		$PartyAcc_head1 = getRequest('supplier'); 
		$totalPartyCR1  = $comlistApp->getTotalCreditAmount($PartyAcc_head1,$project_id);
		$totalPartyDR1  = $comlistApp->getTotalDebitAmount($PartyAcc_head1,$project_id);
		$PreviousPartyBalance = ($totalPartyDR1-$totalPartyCR1);
		$PartyBalance1  = ($totalPartyDR1-($totalPartyCR1+$fullCr));					 
		$this->saveAccountJournal($voucher_no,$PartyAcc_head1,"Supplier",$project_id,$description,0,$fullCr,$PartyBalance1,0,$created_date,$cost_center,$grn_voucher);
		//======= Supplier Dr ======
		$description = getRequest('description');
		if($description==""){ $description = "Paid amount by cash against purchase item";}			
		$DrAmount = getRequest('paid_amount');
		$PartyAcc_head = getRequest('supplier'); 
		$totalPartyCR  = $comlistApp->getTotalCreditAmount($PartyAcc_head,$project_id);
		$totalPartyDR  = $comlistApp->getTotalDebitAmount($PartyAcc_head,$project_id);
		$PartyBalance  = (($totalPartyDR+$DrAmount)-$totalPartyCR);					 
		$this->saveAccountJournal($voucher_no,$PartyAcc_head,"Supplier",$project_id,$description,$DrAmount,0,$PartyBalance,0,$created_date);	
		//============== Cash Cr ===============
		$description = getRequest('description');
		if($description==""){ $description = "Paid amount by cash against purchase item";}	
		if(getFromSession('u_type_id')==102){ // 102 = Pharmecy
		$acc_head     	= getFromSession('cash_id'); 	
		}else{
		$acc_head     	= $comlistApp->getCashId($project_id); 
		}
		$totalCR  = $comlistApp->getTotalCreditAmount($acc_head,$project_id);
		$totalDR  = $comlistApp->getTotalDebitAmount($acc_head,$project_id);
		$balance  = ($totalDR-($totalCR+$CrAmount));					 
		$this->saveAccountJournal($voucher_no,$acc_head,"Cash",$project_id,$description,0,$CrAmount,$balance,0,$created_date);	
	     }
	 }elseif($mode_of_payment=="Check"){
	//====== save payable_check ======
	$this->saveGRNPayableCheck($voucher_no,$voucher_no,"Payment",getRequest('paid_amount'));
	//======= Supplier Cr ======
	$description = getRequest('description');
	if($description==""){ $description = "Amount payable against purchase item";}	
	$fullCr = getRequest('net_payble');
	$PartyAcc_head1 = getRequest('supplier'); 
	$totalPartyCR1  = $comlistApp->getTotalCreditAmount($PartyAcc_head1,$project_id);
	$totalPartyDR1  = $comlistApp->getTotalDebitAmount($PartyAcc_head1,$project_id);
	$PreviousPartyBalance = ($totalPartyDR1-$totalPartyCR1);						 				 
	$PartyBalance1  = ($totalPartyDR1-($totalPartyCR1+$fullCr));					 
	$this->saveAccountJournal($voucher_no,$PartyAcc_head1,"Supplier",$project_id,$description,0,$fullCr,$PartyBalance1,0,$created_date,$cost_center,$grn_voucher);
	//======= Supplier Dr ======
	$description = getRequest('description');
	if($description==""){ $description = "Paid amount by cheque against purchase item";}			
	$DrAmount = getRequest('paid_amount');
	$PartyAcc_head = getRequest('supplier'); 
	$totalPartyCR  = $comlistApp->getTotalCreditAmount($PartyAcc_head,$project_id);
	$totalPartyDR  = $comlistApp->getTotalDebitAmount($PartyAcc_head,$project_id);
	$PartyBalance  = (($totalPartyDR+$DrAmount)-$totalPartyCR);					 
	$this->saveAccountJournal($voucher_no,$PartyAcc_head,"Supplier",$project_id,$description,$DrAmount,0,$PartyBalance,0,$created_date);	
	}elseif($mode_of_payment=="Payable"){
	//======= Supplier Cr ======	
	$description = getRequest('description');
	if($description==""){ $description = "Amount payable against purchase item";}
	$fullCr = getRequest('net_payble');
	$PartyAcc_head1 = getRequest('supplier'); 
	$totalPartyCR1  = $comlistApp->getTotalCreditAmount($PartyAcc_head1,$project_id);
	$totalPartyDR1  = $comlistApp->getTotalDebitAmount($PartyAcc_head1,$project_id);
	$PreviousPartyBalance = ($totalPartyDR1-$totalPartyCR1);					 
	$PartyBalance1  = ($totalPartyDR1-($totalPartyCR1+$fullCr));					 
	$this->saveAccountJournal($voucher_no,$PartyAcc_head1,"Supplier",$project_id,$description,0,$fullCr,$PartyBalance1,0,$created_date,$cost_center,$grn_voucher);				
	}
		
 }
return $PreviousPartyBalance;					
}else {	
return 0;	
}

}//EOFn   


function insertGRNPurchaseDetails($voucher_no){
	require_once(CLASS_DIR.'/common.list.class.php');	
	$comlistApp 			= new CommonList();
	$requestdata 			= array();
	$arr_catagory_product_id	= array();	
	$project_id  			= getFromSession('project_id');
	$currency        		= getRequest('currency');
	$TotalFreeAmount		= 0; $TotalStockAmount=0;
	$getSql	= "SELECT * FROM ".TEMP_GRN_TBL." WHERE created_by = '".getFromSession('userid')."' AND project_id='".getFromSession('project_id')."'";
	$gres 	= mysql_query($getSql);
	if(mysql_num_rows($gres)>0){
		while($row = mysql_fetch_object($gres)){
		$requestdata['project_id']= $project_id;       	  
		$requestdata['catagory'] = $row->catagory;       	  
		$requestdata['brand_id'] = $row->brand_id;  
		$brand_id = $row->brand_id;     	  
		$requestdata['product'] = $row->productid; 
		$product_id		= $row->productid;
		if($row->warranty!=""){   
		$requestdata['warranty']= $row->warranty;
		}else{
		$requestdata['warranty']= 0;
		}
		if($row->serial!=""){   
		$requestdata['serial'] 	= $row->serial;
		}else{
		$requestdata['serial']  = 0;
		}
		$serial = $row->serial; $warranty=$row->warranty;      	  
		$requestdata['discount_per_qty'] = $row->unit_discount;
		$requestdata['details'] 	= $row->details;   	  
		$requestdata['unit_price'] 	= $row->unit_price;       	  
		$requestdata['qty'] 		= $row->qty;  $productQty = ($row->free_qty + $row->qty);  	  
		$requestdata['free_qty'] 	= $row->free_qty;        	  
		$requestdata['rec_qty'] 	= ($row->free_qty + $row->qty);        	  
		$requestdata['m_unit'] 		= $row->munit;       	  
		$requestdata['total'] 		= $row->total;
		$requestdata['discount_amount'] = (($row->unit_price / 100) * $requestdata['discount_per_qty']);
		$requestdata['created_by'] 	= getFromSession('userid');
		$requestdata['po_voucher_no']   = $row->po_voucher_no;
                $requestdata['pod_id'] 		= $row->pod_id;
		$requestdata['created_date']    = date('Y-m-d h:i:s');
		$requestdata['project_id']      = $project_id;
		
		$requestdata['voucher_no']      = $voucher_no;
		$requestdata['lc_no']           = getRequest('lc_no');
		$requestdata['supplier']        = getRequest('supplier');
		$supplier    	= getRequest('supplier');
		$created_date 	= formatDate(getRequest('purchase_date'));
		$info        	=  array();
		$info['table']	= PURCHASE_DETAILS_TBL;
		$info['data'] 	= $requestdata;  
		//$info['debug']  	=  true;    
		$res = insert($info);		
		if($res){
		
		$m_unit = $requestdata['m_unit']; $unit_price = $requestdata['unit_price']; 
		$StockAmount = ($unit_price * $productQty);
		$TotalStockAmount+=$StockAmount;
		$this->saveAVGPurchasePrice($voucher_no,$project_id,$product_id,$unit_price);
		$totalCR  = $this->getTotalCreditStock($product_id,getFromSession('project_id'));
		$totalDR  = $this->getTotalDebitStock($product_id,getFromSession('project_id'));					 
		$balance  = (($totalDR + $productQty) - $totalCR);	
		$Prosql = "SELECT product_type FROM ".PRODUCT_TBL." WHERE product_id = '$product_id' AND project_id = '$project_id'";
		$Prorow = mysql_fetch_object(mysql_query($Prosql));
		$product_type 	    = $Prorow->product_type;
		$inventory_auto_out = getFromSession('inventory_auto_out'); 
		$store_id = getRequest('store_id');
		if($inventory_auto_out==1 && $product_type=="Invetory Item"){				
		$this->saveStockJournal($voucher_no,$project_id,$store_id,$product_id,$product_type,$serial,$warranty,$unit_price,$m_unit,$productQty,0,$balance,$created_date);
		$Autobalance  = ($totalDR - ($totalCR+$productQty));
		$this->saveStockJournal($voucher_no,$project_id,$store_id,$product_id,$product_type,$serial,$warranty,$unit_price,$m_unit,0,$productQty,$Autobalance,$created_date);
		}else{	
		//=== Stock Dr =====		
		$this->saveStockJournal($voucher_no,$project_id,$store_id,$product_id,$product_type,$serial,$warranty,$unit_price,$m_unit,$productQty,0,$balance,$created_date);
		}
		if($row->free_qty>0){
			$FreeDrAmount = ($unit_price * $row->free_qty);
			$TotalFreeAmount+=$FreeDrAmount;
			$FreeDrAmount=0;
		}

		    $inventory_type = getRequest('inventory_type');
                    if (isset($inventory_type) && $inventory_type != "" && $inventory_type != "A000036") {
                        $productRequestData['unit_price'] = $row->unit_price;
                    }
		
                    $productRequestData['purchase_unit_price'] = $row->unit_price;

                    $infoData = array();
                    $infoData['table'] = PRODUCT_TBL;
                    $infoData['data'] = $productRequestData;
                    $infoData['where'] = "product_id ='" . $product_id . "'";
                    //$infoData['debug']  	=  true;
                    $productRes = update($infoData);
	  }// end purchase save
	  
	 }// end while 
	//=== Stock Dr Amount =====
	if($TotalStockAmount >0){	  
	  $StockId 	 = "S126"; //getRequest('inventory_type'); 
	  if($StockId=="S126"){
	  $StockId 	 = getRequest('inventory_id');
	  } 
	  $TotalStock    = $comlistApp->getAccounceBalance($StockId,$project_id);
	  $StockBalance  = ($TotalStock+$TotalStockAmount);	
	  $description   = "Purchase Item";				 
	  $comlistApp->saveAccJournal($voucher_no,$StockId,"Stock","Purchase Item",$project_id,$description,$TotalStockAmount,0,$StockBalance,0,$created_date);
	}
	//========= Free Product Cr ==========
	if($TotalFreeAmount >0){		
	  $description     = "Receipt free with purchase item";		
	  $freeItemhead    = $comlistApp->getPurchaseDiscountId($project_id);
	  $TotalFreeBL 	   = $comlistApp->getAccounceBalance($freeItemhead,$project_id);
	  $freeItemBalance = ($TotalFreeBL -$TotalFreeAmount);
	  $comlistApp->saveAccJournal($voucher_no,$freeItemhead,"Acc","Free Item",$project_id,$description,0,$TotalFreeAmount,$freeItemBalance,0,$created_date,0);
	}
    }// end if
    if($res){ 
	 $dsql = "DELETE FROM ".TEMP_GRN_TBL." WHERE created_by = '".getFromSession('userid')."' AND project_id='".getFromSession('project_id')."'";
	 mysql_query($dsql);
    }
  } //End of the function insertSalesDetails()



function saveAVGPurchasePrice($voucher_no,$project_id,$product_id,$purchase_price){	
		$sql = "INSERT INTO ".AVG_PURCHASE_PRICE_TBL."(voucher_no,project_id,product_id,purchase_price) 
		VALUES('".$voucher_no."','".$project_id."','".$product_id."','".$purchase_price."')"; 
		$ires = mysql_query($sql);
		$avg_purchase_price	=0; 
		if($ires){
			$Prosql = "SELECT purchase_price  FROM ".AVG_PURCHASE_PRICE_TBL." WHERE product_id = '$product_id' AND project_id = '$project_id' ORDER BY `id` DESC LIMIT 0 , 2";
			$pres 	= mysql_query($Prosql);
			$ttl_product 	= mysql_num_rows($pres);
			if($ttl_product >0){
				while($prow = mysql_fetch_object($pres)){
					$avg_purchase_price += $prow->purchase_price;
				}		
				$avg_purchase_price = ($avg_purchase_price / $ttl_product);
			}
			if(intval($avg_purchase_price)==""){ $avg_purchase_price=0;}			
			
			if($avg_purchase_price ==0){
				$avg_purchase_price = $purchase_price;
			}
			$USQL 	= "UPDATE ".PRODUCT_TBL." SET purchase_unit_price = $avg_purchase_price WHERE product_id = '$product_id' AND project_id = '$project_id'";
			//mysql_query($USQL);
		}
   }



 function getTotalCreditStock($acc_head,$project_id){
   		$sql = "SELECT sum(`cr`) as credit_amount FROM ".STOCK_LEDGER_TBL." WHERE product_id = '$acc_head' AND project_id = '$project_id'";
		$row = mysql_fetch_object(mysql_query($sql));
		$credit_amount = $row->credit_amount;
		if(empty($credit_amount)){
			$credit_amount = 0;
		}
		return $credit_amount;

   }  
   function getTotalDebitStock($acc_head,$project_id){
   		$sql = "SELECT sum(`dr`) as debit_amount FROM ".STOCK_LEDGER_TBL." WHERE product_id = '$acc_head' AND project_id = '$project_id'";
		$row = mysql_fetch_object(mysql_query($sql));
		$debit_amount = $row->debit_amount;
		if(empty($debit_amount)){
			$debit_amount = 0;
		}
		return $debit_amount;
   }



function saveStockJournal($voucher_no,$project_id,$store_id,$product_id,$product_type,$serial=NULL,$warranty=NULL,$unit_price=NULL,$m_unit,$DR=NULL,$CR=NULL,$balance,$create_date=NULL){
	$created_by = getFromSession('userid');
		$sql = "INSERT INTO ".STOCK_LEDGER_TBL." (voucher_no,project_id,store_id,product_id,product_type,serial,warranty,note,unit_price,m_unit,dr,cr,balance,create_date,created_by) VALUES('".$voucher_no."','".$project_id."','".$store_id."','".$product_id."','".$product_type."','".$serial."','".$warranty."','Purchase Item','".$unit_price."','".$m_unit."','".$DR."','".$CR."','".$balance."','".$create_date."','".$created_by."')";
		mysql_query($sql);
	}





 function saveGRNPayableCheck($voucher_no,$pvoucher_no,$transaction_type,$paid_amount){
	  $requestdata = array();
	  $requestdata = getUserDataSet(PAYABLE_CHECK_TBL);	
	  $requestdata['check_no'] 			= getRequest('check_no');
	  $requestdata['check_issue_date']  = formatDate(getRequest('check_issue_date')); 
	  $requestdata['created_date']      = formatDate(getRequest('purchase_date'));
	  $requestdata['acc_head'] 			= getRequest('supplier'); 
	  $requestdata['head_type'] 		= "Check"; 
	  $requestdata['voucher_no']        = $voucher_no;  
	  $requestdata['pvoucher_no']       = $pvoucher_no; 
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

       function createPIVoucharID()
	   {
	      $info = array();
	      $info['table'] = DEVIT_VOUCHAR_TBL;
	      $info['fields'] = array('max(voucher_no) as maxvoucher');  
	      $info['where']  = "voucher_no LIKE '%PI%'";          
	      $res = select($info);      
	      $maxvoucherId = 'PI0000000';      
	      if(count($res)){
		 foreach($res as $v){
		 	 if($v->maxvoucher){
		     $maxvoucherId = $v->maxvoucher;
		     }
		     break;   	
		 }
	      }
	      $maxvoucherId = generateID("PI",$maxvoucherId,9);
	      return $maxvoucherId;
	  } 

	function createGRNVoucharID()
	   {
	      $info = array();
	      $info['table'] = PURCHASE_MASTER_TBL;
	      $info['fields'] = array('max(grn_voucher) as maxvoucher');        
	      $res = select($info);      
	      $maxvoucherId = 'GRN000000';      
	      if(count($res)){
		 foreach($res as $v){
		 	 if($v->maxvoucher){
		     $maxvoucherId = $v->maxvoucher;
		     }
		     break;   	
		 }
	      }
	      $maxvoucherId = generateID("GRN",$maxvoucherId,9);
	      return $maxvoucherId;
	  } 

	function saveReceivedItem(){
		mysql_query("START TRANSACTION;");
		$voucher_no = getRequest("voucher_no");		
		$this->insertPurchaseDetails($voucher_no);	
	        $project_id  		= getFromSession('project_id');
	        $total_received_value 	= getRequest('total_value');
		$exceed_received_amount = getRequest('exceed_received_amount');
		$actual_received_amount = $total_received_value-$exceed_received_amount;
		$PMsql = "SELECT voucher_no, net_payble,paid_amount,due,discount,item_received_amount FROM ".PURCHASE_MASTER_TBL." WHERE 
		voucher_no ='".getRequest('voucher_no')."' AND project_id = '$project_id'";
		$PMrow 			= mysql_fetch_object(mysql_query($PMsql));
		$paid_amount 	= $PMrow->paid_amount;
		$existing_due 	= $PMrow->due;
		$item_received_amount 	= $PMrow->item_received_amount;
		$total_received_amount 	= ($actual_received_amount+$item_received_amount);
		//$present_due 			= ($total_received_amount - $existing_due); old
		$present_due 			= ($total_received_amount - ($paid_amount+$PMrow->discount)); // new
		$PMUpdate = "UPDATE ".PURCHASE_MASTER_TBL." SET due = '$present_due', 
		item_received_amount = '$total_received_amount' WHERE 
		voucher_no ='".getRequest('voucher_no')."' AND project_id = '$project_id' AND  voucher_no='".getRequest('voucher_no')."'";
		mysql_query($PMUpdate);		
		mysql_query("COMMIT;");
		header("location:index.php?app=purchase_item_received&cmd=print_vouchar&voucher_no=".$voucher_no);	 
	
	}//EOFn  

	
	function getPurchaseMasterInfo($id){		
		   
		$project_id     = getFromSession('project_id');  
		$info           = array();    
		$info['table']  = PURCHASE_MASTER_TBL.' pm,'.SUPPLIER_TBL.' s,'.PROJECT_TBL.' p,'.CURRENCY_TBL.' c';	
		$info['fields'] = array('pm.voucher_no','pm.voucher_no','pm.po_no','p.location','s.name','s.address','pm.quotation_no','pm.lc_no','pm.lcopener','pm.lcopening_bank',"DATE_FORMAT(pm.lcopening_date,'%d %b %y' ) as lcopening_date",'pm.country','pm.lc_details','pm.track_no','pm.van_no','pm.total_value',"DATE_FORMAT(pm.purchase_date,'%d %b %y' ) as purchase_date",'pm.mode_of_payment','c.curr_symble','pm.bank_name','pm.acc_no','pm.check_no','pm.check_no','pm.discount','pm.net_payble','pm.paid_amount','pm.due','pm.ref_no','pm.created_date');
		
		$sql="pm.supplier = s.supplier_code AND pm.project_id = p.project_id AND pm.currency = c.currency_id AND pm.project_id = '".$project_id."' AND pm.voucher_no = '$id'";
							
		$info['where']   = $sql;	  	
	    $info['groupby'] = array("pm.voucher_no");
		//$info['debug']  = true;
		$res            =	select($info);
		if(count($res))
		{
			foreach($res as $i=>$v)
			{
				$data[$i] = $v;             
			}
		}
		  //dumpVar($data);
		  return $data[0];
   } 
           
   function getProductList($id) { 
		$info           = array();    
		$info['table']  =  PURCHASE_DETAILS_TBL.' pd,'.PRODUCT_TBL.' p,'.BRAND_TBL.' b';	
		$info['fields'] = array('pd.pur_detail_id','pd.voucher_no','pd.project_id','pd.serial','pd.warranty','pd.catagory','b.brand_name','pd.product','pd.details','p.product_name','p.product_desc','pd.m_unit','pd.unit_price','pd.qty','pd.rec_qty','pd.total_bag','pd.total','pd.created_time');
		
		$sql="pd.product = p.product_id AND p.brand_code = b.brand_id AND pd.voucher_no = '$id'";
		
		$info['where']  = $sql;	  	
	    $info['groupby'] = array("pd.pur_detail_id");
		$info['orderby'] = array("pd.product asc");
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

	function loadProduct4Catagory($brand_id)
	{	 
		  $voucher_no=trim(getRequest('voucher_no'));
		  $catagory=trim(getRequest('catagory_id'));
	  	  $project_id = getFromSession('project_id');  
		  $info            = array();
		  $info['table']   = PRODUCT_TBL." p,".PURCHASE_MASTER_TBL." pm,".PURCHASE_DETAILS_TBL." pd";
		  $info['fields']  =  array('p.product_id', 'p.product_code','p.product_name','p.product_desc','pd.details');
		  //$where= "pm.voucher_no=pd.voucher_no AND pd.product=p.product_id AND p.catagory='$catagory' AND p.brand_code='$brand_id' AND p.project_id='$project_id' AND pd.voucher_no='$voucher_no'";
		  $where= "pm.voucher_no=pd.voucher_no AND pd.product=p.product_id AND p.brand_code='$brand_id' AND p.project_id='$project_id' AND pd.voucher_no='$voucher_no'";
		  $info['where']   = $where;
		  $info['groupby'] = array("p.product_id");
		  //$info['debug']   = true;
	
		  $result          = select($info);
		  $data            = array();
	
		  if(count($result))
		  {
			 foreach($result as $key=>$value)
			 {
				$data[$key][]        = $value;
			 }
		  }
			
		  require_once(CLASS_DIR . '/common.list.class.php');	
		  foreach($data as $i=>$v)
		  {
			$productName = (new CommonList())->normalizeProductName($v[0]->product_code, $v[0]->product_name);
			 $subject_idname .= $v[0]->product_id.'#####'.$productName.'#####'.$v[0]->details.'#####'.$v[0]->product_desc.'@@@';
		  }
		  echo $subject_idname;	
	} 

	function loadPOInfo($supplier)
	{	  
		
	  	  $project_id 	   = getFromSession('project_id');  
		  $info            = array();
		  $info['table']   = PURCHASE_MASTER_TBL." pm, ".SUPPLIER_TBL." s";
		  $info['fields']  = array('s.name','s.address','pm.total_value','pm.voucher_no',"DATE_FORMAT(pm.purchase_date,'%d %b %y' ) as purchase_date");
		  $info['where']   = "pm.supplier=s.supplier_code AND pm.supplier = '$supplier' AND pm.project_id = '$project_id' AND pm.net_payble>pm.item_received_amount";
		  $info['groupby'] = array("pm.voucher_no");
		  $info['orderby'] = array("pm.purchase_date desc");
		  //$info['debug'] = true;
	
		  $result          = select($info);
		  $data            = array();
	
		  if(count($result))
		  {
			 foreach($result as $key=>$value)
			 {
				$data[$key][]= $value;
			 }
		  }
				
		  foreach($data as $i=>$v)
		  {
			 $subject_idname.= $v[0]->voucher_no.'#####'.$v[0]->total_value.'#####'.$v[0]->purchase_date.'@@@';
		  }
		  echo $subject_idname;	
	}
	function loadUnitePrice($product_id){
		  $project_id = getFromSession('project_id');  		 
		  $info            = array();
		  $info['table']   = PRODUCT_TBL;
		  $info['where']   = "product_id = '$product_id' AND project_id = '$project_id'";
		  //$info['debug']   = true;
	
		  $result          = select($info);
		  $data            = array();
	
		  if(count($result))
		  {
			 foreach($result as $key=>$value)
			 {
				$data[$key][]        = $value;
			 }
		  }
				
		  foreach($data as $i=>$v)
		  {
			 $unit_price.= $v[0]->m_unit."###".$v[0]->qty."###".$v[0]->unit_price."###".$v[0]->product_catagory;
		  }

		  echo $unit_price;	
	}

	function loadProductDtl($product_id){
	   if (ob_get_level()) ob_end_clean();
           ob_start(); // Start buffering


	  $project_id = getFromSession('project_id'); 
          $store_id = getRequest('store_id'); 		 
	  $info            = array();	  	  
	  $info['table']   = PRODUCT_TBL." p,".CATAGORY_TBL.' c,'.BRAND_TBL.' b';
	  $info['fields']  =  array('p.product_name','p.m_unit','p.product_desc','p.unit_price','p.purchase_unit_price','p.product_catagory','p.catagory','c.catagory_name','p.brand_code','b.brand_name');
	  $info['where']   = "p.catagory =c.catagory_code AND p.brand_code =b.brand_id AND p.product_id = '$product_id'  AND p.project_id = '$project_id'";
	  $info['groupby'] = array("p.product_id");		  
	  //$info['debug']   = true;
	  $result          = select($info);
	  $data            = array();

	$sql = "SELECT (sum(`dr`) - sum(`cr`)) as balance_qty FROM stock_ledger WHERE product_id = '$product_id' AND project_id = '$project_id'";
        if ($store_id != "") {
            $sql .= " AND store_id ='$store_id'";
        }
        $row = mysql_fetch_object(mysql_query($sql));
        $balance_qty = $row->balance_qty;
        if (empty($balance_qty)) {
            $balance_qty = 0;
        }

	  if(count($result)){
		 foreach($result as $key=>$value){
			$data[$key][]        = $value;
		 }
	  }

	  $resultData = [];
			
	  foreach($data as $i=>$v){
		$resultData =[
			"product_name" => $v[0]->product_name,
			"m_unit" => $v[0]->m_unit,
			"product_desc" => $v[0]->product_desc,
			"unit_price" => $v[0]->unit_price,
			"purchase_unit_price" => $v[0]->purchase_unit_price,
			"catagory" => $v[0]->catagory,
			"catagory_name" => $v[0]->catagory_name,
			"brand_code" => $v[0]->brand_code,
			"brand_name" => $v[0]->brand_name,
		];

	  }


	$psql = "SELECT pm.purchase_date as last_date, SUM(qty) as last_qty  FROM purchase_details AS pd JOIN purchase_master AS pm ON pd.voucher_no = pm.voucher_no WHERE pm.purchase_date = ( SELECT MAX(purchase_date) FROM purchase_master JOIN purchase_details ON purchase_master.voucher_no = purchase_details.voucher_no WHERE purchase_master.voucher_no LIKE 'PI%' AND purchase_details.product = '$product_id') AND pd.product = '$product_id' GROUP BY pd.product";

	$pResult = $sprResult = mysql_fetch_object(mysql_query($psql));
	$lastPurchaseDate = isset($pResult->last_date) ? $pResult->last_date : "";
	$resultData["balance_qty"] = $balance_qty;
	$resultData["lastPurchaseDate"] = $lastPurchaseDate;


	header('Content-Type: application/json');
        echo json_encode($resultData);
        exit();	
   }
	
	function getBankAccountList($purchase_no=null)
	{
	   if($from == "" && $to == ""){$from=0; $to=40;}  
	   $data            = array();	  
	   $info            = array();
	   $info['table']   = BANK_ACCOUNT_TBL.' ba,'.BANK_TBL.' b';	
	   $info['fields'] = array('ba.bank_code','b.bank_name','ba.purchase_no','ba.account_name','ba.account_type','ba.phone','ba.fax');
	   if($purchase_no!=""){				
			$info['where']   = "ba.bank_code = b.bank_id AND ba.purchase_no = '".$purchase_no."'";
	   }else{
			$info['where']   = "ba.bank_code = b.bank_id";
	   }    
	   $info['orderby'] = array("ba.purchase_no asc LIMIT $from,$to");
	   $info['debug']   = false;			 
	
	   $res            =	select($info);   
	
	   if(count($res))
	   {
		  foreach($res as $i=>$v)
		  {
			 $data[$i] = $v;
		  }
	   }
	   if($purchase_no==""){
		return $data; // for list
	  }else{
		return $data[0];	// for view
	  }
	
	}
   function getCurrencyList()
   {

      $info            = array();
      $info['table']   = CURRENCY_TBL;
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
   function getCatagoryList()
   {	
	  $project_id  		= getFromSession('project_id');
      $data 			= array(); 
      $info        		=  array();
      $info['table']	= CATAGORY_TBL;
	  $info['where']   = "project_id = '$project_id'";
      $res            	=	 select($info);      

      if(count($res))
      {
         foreach($res as $i=>$v)
         {
            $data[$i] = $v;             
         }
      }
	  //dumpVar($data);
      return $data;	
   }
 
 
  function getSupplierList()
  {	
	  $project_id  		= getFromSession('project_id');
      $data 			= array(); 
      $info        		= array();
      $info['table']	= SUPPLIER_TBL;
	  $info['where']   = "project_id = '$project_id'";
      $res            	= select($info);      

      if(count($res))
      {
         foreach($res as $i=>$v)
         {
            $data[$i] = $v;             
         }
      }
	  //dumpVar($data);
      return $data;	
   } 
   function saveAccountJournal($voucher_no,$sub_id,$head_type,$project_id,$description,$DR=NULL,$CR=NULL,$balance,$status=NULL,$purchare_date, $cost_center = "",$grn_voucher =""){	
	$created_by = getFromSession('userid');$head_type = getHeadType($sub_id); $transaction_type = "";		
	$sql = "INSERT INTO ".ACCOUNT_JOURNAL_TBL." 
	(voucher_no,created_date,sub_id,head_type,transaction_type,project_id,description,dr,cr,balance,status,created_by,cost_center,grn_voucher) 
	VALUES('".$voucher_no."','".$purchare_date."','".$sub_id."','".$head_type."','".$transaction_type."','".$project_id."','".$description."','".
	$DR."','".$CR."','".$balance."','".$status."','" . $created_by . "','" . $cost_center . "','" . $grn_voucher . "')";
	mysql_query($sql);
   }
   function getCashId($project_id){
		$sql = "SELECT sub_id FROM ".SUB_ACC_HEAD_TBL." WHERE head_type = 'Cash' AND project_id = '$project_id'";

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
  function deleteRecord($id)
  {
   	  if(getRequest('id'))
      { 
      	$info = array();
      	$info['table'] = BANK_ACCOUNT_TBL;
      	$info['where'] = "purchase_no='$id'";
      	$info['debug'] = false;
      	$res = delete($info);      	

      	if($res)
      	{
      	  $msg="Successfully delete Record !!!";
          header("location:?app=bank_account&cmd=view&msg=$msg");     	   

      	} else{
      		 header("location:?app=bank_account&cmd=view&cmd=list&deleted=no");
      	}      	

      }

   } 
 //====================== Start Purchase Details ===============

  function showEditor4PurchaseDetails($msg = null) {        

	  $data                				= array();
	  $data['cmd']         				= getRequest('cmd');
	  $data['record_list'] 				= $this->getPurchaseDetailsList(getRequest('from'),getRequest('to'));
	  $data['totalrecord']				= $this->getTotalPurchaseDetailsList(getRequest('from'),getRequest('to'));	
	  require_once(PURCHASE_DETAILS_SKIN); 
	  return $data[0];

   }
 function getPurchaseDetailsList($from,$to) { 

		if($from == "" && $to == ""){$from=0; $to=500;}
		$date_from 			= formatDate(getRequest('date_from'));
		$date_to 			= formatDate(getRequest('date_to'));				
		$project_id     = getFromSession('project_id');  
		$info           = array();    
		$info['table']  = PURCHASE_MASTER_TBL.' pm,'.SUPPLIER_TBL.' s,'.PROJECT_TBL.' p,'.CURRENCY_TBL.' c';	
		$info['fields'] = array('pm.voucher_no','p.project_name','p.location','s.supplier_code','s.name','s.address','pm.quotation_no','pm.lc_no','pm.lcopener','pm.lcopening_bank',"DATE_FORMAT(pm.lcopening_date,'%d %b %y' ) as lcopening_date",'pm.country','pm.lc_details','pm.track_no','pm.van_no','pm.total_value',"DATE_FORMAT(pm.purchase_date,'%d %b %y' ) as purchase_date",'pm.mode_of_payment','c.curr_symble','pm.bank_name','pm.acc_no','pm.check_no','pm.check_no','pm.discount','pm.net_payble','pm.paid_amount','pm.due','pm.ref_no',"DATE_FORMAT(pm.created_date,'%d %b %y' ) as date",'pm.created_date');
		
		$sql="pm.supplier = s.supplier_code AND pm.project_id = p.project_id AND pm.currency = c.currency_id AND pm.project_id = '".$project_id."'";
									
		if($date_from!="" && $date_to ==""){
			$sql.=" AND pm.created_date >= '$date_from'";
		}elseif($date_from=="" && $date_to !=""){
			$sql.=" AND pm.created_date <= '$date_to'";
		}elseif($date_from!="" && $date_to !=""){
			$sql.=" AND pm.created_date BETWEEN '$date_from' AND '$date_to'";
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

   function getTotalPurchaseDetailsList($from,$to) {  

		$date_from 			= formatDate(getRequest('date_from'));
		$date_to 			= formatDate(getRequest('date_to'));				
		$project_id     = getFromSession('project_id');  
		$info           = array();    
		$info['table']  = PURCHASE_MASTER_TBL.' pm,'.SUPPLIER_TBL.' s,'.PROJECT_TBL.' p,'.CURRENCY_TBL.' c';	
		$info['fields'] = array('pm.voucher_no');
		
		$sql="pm.supplier = s.supplier_code AND pm.project_id = p.project_id AND pm.currency = c.currency_id AND pm.project_id = '".$project_id."'";
									
		if($date_from!="" && $date_to ==""){
			$sql.=" AND pm.created_date >= '$date_from'";
		}elseif($date_from=="" && $date_to !=""){
			$sql.=" AND pm.created_date <= '$date_to'";
		}elseif($date_from!="" && $date_to !=""){
			$sql.=" AND pm.created_date BETWEEN '$date_from' AND '$date_to'";
		}
		$info['where']  =$sql;	

		$info['orderby'] 	= array("pm.created_date asc");
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
	function savePayableCheck($voucher_no,$transaction_type,$paid_amount){
	 $requestdata = array();

	  $requestdata = getUserDataSet(PAYABLE_CHECK_TBL);	
	  $requestdata['check_no'] 			= getRequest('check_no');
	  $requestdata['check_issue_date']  = formatDate(getRequest('check_issue_date')); 
	  $requestdata['created_date']      = formatDate(getRequest('purchase_date'));
	  $requestdata['acc_head'] 			= getRequest('supplier'); 
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
   
} // End class
?>
