<?php
require_once('journal.class.php');
class SalesOrder extends Journal
{
   
   function run()
   {         

      $cmd    = getRequest('cmd');
      $u_t_id = getFromSession('u_type_id');
      if( ($u_t_id ==101) || ($u_t_id == 107)) // 101 = sysadmin, 102 = admin, 103= salesman
      {

      	switch ($cmd)
      	{
      	   case 'add'			: $this->showEditor(); break;
	   case 'edit'			: $this->showEidtEditor(); break;
	   case 'sal_dtl'		: $this->showEditor4SalesDetails(); break;
	   case 'admin_sal_dtl'		: $this->showAllCompaniesSalesDetails(); break;
      	   case 'loadProduct'  		: $this->loadProduct4Catagory(trim(getRequest('catagory_id'))); break; 
      	   case 'get_dtl'  		: $this->loadProductDtl(trim(getRequest('product_id'))); break;   
	   case 'save_sales'		: $this->saveSalesItem(); break;    
	   case 'add_undelivery'	: $this->loadUndeliverySales(); break; 
	   case 'print_oc.vouchar'	: $screen = $this->showOriginalPrintEditor($msg); break; 
	   case 'print_vouchar'		: $screen = $this->showPrintEditor($msg); break;  
	   case 'delete'             	: $screen = $this->deleteRecord(getRequest('id')); break;  
	   case 'delete_order'          : $screen = $this->deleteOrder(getRequest('voucher_no')); break; 
	   case 'delete_unapprove'      : $screen = $this->deleteUnApproved(getRequest('voucher_no')); break;
      	   default                   	: $cmd = 'list'; $screen = $this->showEditor();   break;
      	}
      }elseif(($u_t_id == 102) || ($u_t_id == 103) || ($u_t_id == 105)) // 102 = admin, 103= salesman
      {

      	switch ($cmd)
      	{
      	   case 'add'			: $this->showEditor(); break;
	   case 'edit'			: $this->showEidtEditor(); break;
	   case 'sal_dtl'		: $this->showEditor4SalesDetails(); break;
	   case 'admin_sal_dtl'		: $this->showAllCompaniesSalesDetails(); break;
      	   case 'loadProduct'  		: $this->loadProduct4Catagory(trim(getRequest('catagory_id'))); break; 
      	   case 'get_dtl'  		: $this->loadProductDtl(trim(getRequest('product_id'))); break;   
	   case 'save_sales'		: $this->saveSalesItem(); break;    
	   case 'add_undelivery'	: $this->loadUndeliverySales(); break; 
	   case 'print_oc.vouchar'	: $screen = $this->showOriginalPrintEditor($msg); break; 
	   case 'print_vouchar'		: $screen = $this->showPrintEditor($msg); break;  
	   case 'delete'             	: $screen = $this->deleteRecord(getRequest('id')); break;
      	   default                   	: $cmd = 'list'; $screen = $this->showEditor();   break;
      	}
      }elseif($u_t_id == 104) // 104 = acc
      {
      	switch ($cmd)
      	{
	   case 'sal_dtl'		: $this->showEditor4SalesDetails(); break;
	   case 'print_vouchar'		: $screen = $this->showPrintEditor($msg); break; 
	   case 'print_oc.vouchar'	: $screen = $this->showOriginalPrintEditor($msg); break;
      	   default                   	: $cmd = 'list'; $screen = $this->showEditor();   break;
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
         	$advArr 		= $this->getSalesMasterInfo($voucher_no);
         	$advArr 		= parseThisValue($advArr); 
		$data   		= array_merge(array(), $advArr); 

		$data['item_list']	= $this->getProductList($voucher_no);
		$data['message'] 	= $msg;
		$data['cmd']     	= getRequest('cmd');
		require_once(SALES_VOUCHAR_SKIN);      
		return true;
	 }else{
		require_once(PRINT_VOUCHAR_SKIN);
	  }
   } 
  
   function showOriginalPrintEditor($msg = null) {    	  
	  $voucher_no 	= getRequest('voucher_no');  
	  if ($voucher_no) {
         	 $advArr 		= $this->getOriginalSalesMasterInfo($voucher_no);
         	 $advArr 		= parseThisValue($advArr); 
		 $data   		= array_merge(array(), $advArr); 
      
		 $data['item_list']	= $this->getOriginalProductList($voucher_no);
		 $data['message'] 	= $msg;
		 $data['cmd']     	= getRequest('cmd');
		 require_once(SALES_VOUCHAR_SKIN);      
		 return true;
	 }else{
		require_once(PRINT_VOUCHAR_SKIN);
	  }
   }
   function showEidtEditor($msg = null) { 
          require_once(CLASS_DIR.'/sales.class.php');	
	  $salesApp 	     = new Sales();
	  require_once(CLASS_DIR.'/common.list.class.php');	
	  $comlistApp 	     = new CommonList();
	  $voucher_no 	     = getRequest('voucher_no'); 
	  $customer_id 	     = getRequest('customer');  
	  $data['cmd']       = getRequest('cmd'); 

	  if(getRequest('submit')) {
		$total_balance = $this->getCustomerBalance($customer_id); 
		$net_payble    = getRequest('net_payble');  
		$pvbalance     = ($total_balance- $net_payble);
		$TotalBalance  = $pvbalance;
		$customer_limit   = $this->getCustomerSalesLimit($customer_id);
		$cellingType 	  = $this->getCustomerCellingType($customer_id);
		
		if($cellingType=="Cash"){
		$customer_limit = abs($TotalBalance); $TotalBalance =0;
		}
		$TotalBalance+=$net_payble; 
		if($customer_limit >=$TotalBalance){ 
		$sres = $this->updateSalesOrder();
		echo "<span style='color:#fff'>Previous Balance Amount ($pvbalance) Plus Present Order Amount ($net_payble)</span><br>"; 
		echo "<span style='color:#fff'>Successfully Update Order</span><br>"; 

		$page = getRequest('page');
		if($page != '' && $page == "order_list"){
			header("location:index.php?app=sales.report&cmd=order_list");	
		}
		if($page != '' && $page == "edit_list"){
			header("location:index.php?app=sales.report&cmd=edit_order_list");	
		}

		}else{
		//$sres = $this->updateSalesOrder(); // will be stop
		echo "<span style='color:#fff'>Previous Balance Amount ($pvbalance) Plus Present Order Amount ($net_payble)</span><br>"; 
		echo "<span style='color:#fff'>So, Total Balance Amount is ($TotalBalance) Greater than Ceilling Amount ($customer_limit)</span>"; 
		}
	  }
	  if(getRequest('did')) {
		$this->deleteItem();
	  }	 		  
	  $advArr 			= $salesApp->getSalesMasterInfo($voucher_no);
	  $advArr 			= parseThisValue($advArr); 
	  $data   			= array_merge($advArr); 

	  $customer_list         	= $comlistApp->getCustomerList();
	  $getCustomerListReceivable 	= $comlistApp->getCustomerListReceivable(); 
	  $data['customer_list'] 	= array_merge($customer_list, $getCustomerListReceivable);
		
	  $data['supplier_list'] 	= $comlistApp->getSupplierList(); 		      	
	  $data['product_list'] 	= $comlistApp->getProductList();	
	  $data['item_list']		= $salesApp->getProductList($voucher_no);
          $data['item_list_origin']     = $this->getOriginProductList($voucher_no);		     	
	  $data['retailer_list'] 	= $comlistApp->getRetailerList();     	
	  $data['depo_list'] 		= $comlistApp->getDeliveryPointList();
	  $data['page'] = getRequest('page');

	  require_once(SALES_ORDER_EDIT_SKIN_FILE); 
	  return $data[0];	    
   }

    function getOriginProductList($id)
    {
        $info = array();
        $info['table'] = SALES_DETAILS_APP_TBL;
        $info['fields'] = array('sal_detail_id', 'voucher_no', 'product');
        $sql = "voucher_no = '$id'";
        $info['where'] = $sql;
        $info['groupby'] = array("sal_detail_id");
        $info['orderby'] = array("sal_detail_id asc");
        //$info['debug']  = true;
        $result = select($info);

        $data = array();
        $cnt = count($result);
        if ($cnt) {
            foreach ($result as $value) {
                $key = $value->voucher_no . '_' . $value->product;
                $data[$key] = $value->sal_detail_id;
            }
        }
        return $data;
    }

   
   function getCustomerBalance($customer){
	require_once(CLASS_DIR.'/common.list.class.php');	
  	$comlistApp 	= new CommonList();
	$project_id 	= getFromSession('project_id');
	$NewSalesAmount = 0;
	if($customer !=""){			 
		 $PreviousPartyBalance = $comlistApp->getAccounceBalance($customer,$project_id);	 
		 $PartyBalance  = ($PreviousPartyBalance+$NewSalesAmount);	
		 return $PartyBalance;
	}else{
		return 0;
	}
   }
   function getCustomerSalesLimit($customer){
	$head_type  	= getHeadType($customer);
	$project_id 	= getFromSession('project_id');
	if($head_type=="Customer"){
	$getSql	= "SELECT ceilling_amount FROM ".SUB_ACC_HEAD_TBL." WHERE project_id='".$project_id."'
	 AND sub_id ='".$customer."' ";
	}else{
	$getSql	= "SELECT ceilling_amount FROM ".SUPPLIER_TBL." WHERE project_id='".$project_id."' 
	 AND supplier_code ='".$customer."' ";
	}
	$gres 		= mysql_query($getSql);
	$ceilling_amount = 0;
	$row = mysql_fetch_object($gres);
	$ceilling_amount = $row->ceilling_amount;
	if($ceilling_amount >=0){
	return $ceilling_amount;
	}else{
	return 0;	
	}
   }
   function getCustomerCellingType($customer){
	$head_type  	= getHeadType($customer);
	$project_id 	= getFromSession('project_id');

	if($head_type=="Customer"){
	$getSql	= "SELECT ceilling_amount FROM ".SUB_ACC_HEAD_TBL." WHERE project_id='".$project_id."'
	 AND sub_id ='".$customer."' ";
	}else{
	$getSql	= "SELECT ceilling_amount FROM ".SUPPLIER_TBL." WHERE project_id='".$project_id."' 
	 AND supplier_code ='".$customer."' ";
	}
	$gres 		= mysql_query($getSql);
	$ceilling_amount = 0;
	$row = mysql_fetch_object($gres);
	$ceilling_amount = $row->ceilling_amount;
	if($ceilling_amount >0){
	return "Credit";
	}else{
	return "Cash";	
	}
   }  
   function deleteItem(){
	require_once(CLASS_DIR.'/common.class.php');	
	$comApp = new Common(); 
	$sal_detail_id 	= getRequest('did');
	$voucher_no 	= getRequest('voucher_no');
	$comApp->deleteRecord(SALES_DETAILS_TBL,"sal_detail_id",$sal_detail_id,"sales_order","edit&voucher_no=$voucher_no"); 
   }
   function deleteOrder($voucher_no){
	 require_once(CLASS_DIR.'/common.class.php');	
	 $comApp = new Common(); 
	 if($voucher_no!=""){
	    $comApp->deleteRecord(DEVIT_VOUCHAR_TBL,"voucher_no",$voucher_no,"",""); 
	    $comApp->deleteRecord(CREDIT_VOUCHAR_TBL,"voucher_no",$voucher_no,"",""); 
	    $comApp->deleteRecord(SALES_MASTER_TBL,"voucher_no",$voucher_no,"","");
            $comApp->deleteRecord(SALES_MASTER_APP_TBL, "voucher_no", $voucher_no, "", "");
            $comApp->deleteRecord(SALES_DETAILS_APP_TBL, "voucher_no", $voucher_no, "", ""); 
	    $comApp->deleteRecord(SALES_DETAILS_TBL,"voucher_no",$voucher_no,"sales.report","order_list");

	    $msg = "Record Deleted Successfully";
            header("location:index.php?app=sales.report&cmd=edit_order_list&msg=$msg");
	    exit();
	 }
   }
   function deleteUnApproved($voucher_no){
	 require_once(CLASS_DIR.'/common.class.php');	
	 $comApp = new Common(); 
	 if($voucher_no!=""){
	 $comApp->deleteRecord(DEVIT_VOUCHAR_TBL,"voucher_no",$voucher_no,"",""); 
	 $comApp->deleteRecord(CREDIT_VOUCHAR_TBL,"voucher_no",$voucher_no,"",""); 
	 $comApp->deleteRecord(SALES_MASTER_TBL,"voucher_no",$voucher_no,"",""); 
         $comApp->deleteRecord(SALES_MASTER_APP_TBL, "voucher_no", $voucher_no, "", "");
         $comApp->deleteRecord(SALES_DETAILS_APP_TBL, "voucher_no", $voucher_no, "", "");
	 $comApp->deleteRecord(SALES_DETAILS_TBL,"voucher_no",$voucher_no,"sales.report","pending_order_list");
	 }
   }
   function updateSalesOrder(){
   	$voucher_no 	= getRequest('voucher_no'); $delivery_point  = getRequest('delivery_point'); 
	$ttlfield 	= getRequest('ttlfield');   $customer	     = getRequest('customer'); 
	$total_amount =0; $totalOrderPrice =0; $totalProductDis=0; $unitDiscountAmount=0; $total_order_qty=0;
	$total_free_qty=0; $project_id     = getFromSession('project_id');  $created_by = getFromSession('userid');

	$msql = "SELECT * FROM ".SALES_MASTER_TBL." WHERE voucher_no = '".$voucher_no."' AND project_id = '".$project_id."' AND customer = '".$customer."' GROUP BY voucher_no";
	$mrow = mysql_fetch_object(mysql_query($msql));
	$befoure_amount = $mrow->net_payble;
        $approvedStatus = $mrow->approved_by;
	
	$j=1;
	for($j; $j<$ttlfield; $j++){
		$details_id = getRequest("details_id$j"); 
		$product 	= getRequest("product$j"); 
		$catagory 	= getRequest("catagory$j"); 
		$brand 		= getRequest("brand$j");  
		$m_unit 	= getRequest("m_unit$j"); 
		$order_qty 	= getRequest("order_qty$j"); 
		$free_qty 	= getRequest("free_qty$j");  
		$origin_sal_detail_id 	= getRequest("origin_sal_detail_id$j");  
		$undelivery_qty 	= getRequest("undelivery_qty$j"); 
		$unit_price = getRequest("unit_price$j"); 
		$discount_persent 	= getRequest("discount_per_qty$j"); 
                $vat_percent = getRequest("vat_per_qty$j");
		 
		$unitDiscountAmount = (($unit_price/100)*$discount_persent);
		$totalDiscount 		= ($unitDiscountAmount*$order_qty);
		
		$totalAmount 		= ($unit_price*$order_qty); 
                $unitVatAmount = (($totalAmount / 100) * $vat_percent);
                $totalPrice = ($totalAmount + $unitVatAmount - $totalDiscount);

                $totalOrderPrice += $totalPrice;

		if($details_id!="" && $order_qty>=0){
			$is_order_complete = 1;
			if($order_qty > 0){
			    $is_order_complete = 0;
			}

		$usql = "UPDATE ".SALES_DETAILS_TBL." SET customer='$customer',product='$product',catagory='$catagory',brand_id='$brand',m_unit='$m_unit',
		unit_price='$unit_price',is_order_complete='$is_order_complete',discount_per_qty='$discount_persent',discount_amount='$unitDiscountAmount',qty='$order_qty',
		free_qty='$free_qty',undelivery_qty='$undelivery_qty',total='$totalPrice',vat='$vat_percent',vat_amount='$unitVatAmount' WHERE sal_detail_id=$details_id AND voucher_no='$voucher_no'";
		mysql_query($usql);

		        if (empty($approvedStatus)) {
		            $usql = "UPDATE " . SALES_DETAILS_APP_TBL . " SET customer='$customer',product='$product',catagory='$catagory',brand_id='$brand',m_unit='$m_unit',
			unit_price='$unit_price',discount_per_qty='$discount_persent',discount_amount='$unitDiscountAmount',qty='$order_qty',
			free_qty='$free_qty',undelivery_qty='$undelivery_qty',total='$totalPrice',vat='$vat_percent',vat_amount='$unitVatAmount' WHERE sal_detail_id=$origin_sal_detail_id  AND voucher_no='$voucher_no'";
		            mysql_query($usql);
		        }

		}elseif($product!="" && $order_qty>0){
		$isql = "INSERT INTO ".SALES_DETAILS_TBL." (voucher_no,project_id,customer,catagory,brand_id,product,m_unit,
		unit_price,discount_per_qty,discount_amount,qty,free_qty,total,vat,vat_amount,created_by) VALUES(		
		'$voucher_no','$project_id','$customer','$catagory','$brand','$product','$m_unit',
		'$unit_price','$discount_persent','$unitDiscountAmount','$order_qty','$free_qty','$totalPrice','$vat_percent','$unitVatAmount','$created_by' 
		)";
		mysql_query($isql);


		        if (empty($approvedStatus)) {
		            $isql = "INSERT INTO " . SALES_DETAILS_APP_TBL . " (voucher_no,project_id,customer,catagory,brand_id,product,m_unit,
			unit_price,discount_per_qty,discount_amount,qty,free_qty,total,vat,vat_amount,created_by) VALUES(		
			'$voucher_no','$project_id','$customer','$catagory','$brand','$product','$m_unit',
			'$unit_price','$discount_persent','$unitDiscountAmount','$order_qty','$free_qty','$totalPrice','$vat_percent','$unitVatAmount','$created_by' 
			)";
		            mysql_query($isql);
		        }	
		}
		$total_order_qty+=$order_qty;
		$total_free_qty+=$free_qty;
		$total_order_amount+=$totalPrice;
		$totalProductDis+=$totalDiscount; 
	}


	$general_discount_percent 	= getRequest('general_discount_percent'); 
	$GDiscountAmount=(($total_order_amount/100)*$general_discount_percent);
	
	$exclusive_discount_percent	 = getRequest('exclusive_discount_percent'); 
	$deliveryAmountAfterDiscount = ($total_order_amount - $GDiscountAmount);
	$EDiscountAmount = (($deliveryAmountAfterDiscount/100)*$exclusive_discount_percent);
	
	$additional_discount_percent 	= getRequest('additional_discount_percent');
	$additional_discount 		= getRequest('additional_discount');
	$description	= getRequest('description');
	$description 	= str_replace('"',"&ldquo;",$description);
	$description 	= str_replace("'","&#8217;",$description);
	
	$sales_date	= formatDate(getRequest('sales_date'));
	$delivery_date	= formatDate(getRequest('delivery_date'));
        $aging_date = formatDate(getRequest('aging_date'));

	$additional_cost = getRequest('additional_cost');

        $vat_type = getRequest('vat_type');
        $ref_voucher = getRequest('ref_voucher');
        $total_vat_percent = getRequest('total_vat_percent');
        $total_vat_amount = getRequest('total_vat_amount');

  	$discount = ($GDiscountAmount+$EDiscountAmount+$additional_discount);
	$net_payble = (($totalOrderPrice+$additional_cost+$total_vat_amount) -$discount);

  	$usql = "UPDATE ".SALES_MASTER_TBL." SET customer='$customer', sales_date='$sales_date', aging_date='$aging_date', delivery_date='$delivery_date', wo_no='$ref_voucher', ref_voucher='$ref_voucher', total_value= '$totalOrderPrice',general_discount_percent='$general_discount_percent',general_discount_amount='$GDiscountAmount',exclusive_discount_percent='$exclusive_discount_percent',exclusive_discount_amount='$EDiscountAmount',additional_cost='$additional_cost',product_discount = '$totalProductDis',discount='$discount',additional_discount_percent='$additional_discount_percent',additional_discount='$additional_discount',delivery_point='$delivery_point',net_payble='$net_payble',due='$net_payble',vat_type='$vat_type',additional_vat_percent='$total_vat_percent',additional_vat_amount='$total_vat_amount',description='$description' WHERE voucher_no='$voucher_no'";
  	$smres = mysql_query($usql);


        if (empty($approvedStatus)) {
            $usql = "UPDATE " . SALES_MASTER_APP_TBL . " SET customer='$customer', sales_date='$sales_date', aging_date='$aging_date', delivery_date='$delivery_date', wo_no='$ref_voucher', ref_voucher='$ref_voucher', total_value= '$totalOrderPrice',general_discount_percent='$general_discount_percent',general_discount_amount='$GDiscountAmount',exclusive_discount_percent='$exclusive_discount_percent',exclusive_discount_amount='$EDiscountAmount',additional_cost='$additional_cost',product_discount = '$totalProductDis',discount='$discount',additional_discount_percent='$additional_discount_percent',additional_discount='$additional_discount',delivery_point='$delivery_point',net_payble='$net_payble',due='$net_payble',vat_type='$vat_type',additional_vat_percent='$total_vat_percent',additional_vat_amount='$total_vat_amount',description='$description' WHERE voucher_no='$voucher_no'";
            $smres = mysql_query($usql);
        }


	$this->updateSalesVoucher($voucher_no,$customer,$net_payble);
	SaveActivityLog("Sales Order",$voucher_no,"Edit",$created_by,$befoure_amount,$net_payble);
	if($smres){ return true;  }else{ return false; }
	   
   }
   function loadUndeliverySales(){
	$voucher_no 	= getRequest('voucher_no'); $delivery_point  = getRequest('delivery_point'); 
	$customer_id	= getRequest('customer_id'); 
	$total_amount =0; $totalOrderPrice =0; $totalProductDis=0; $unitDiscountAmount=0; $total_order_qty=0;
	$total_free_qty=0; $project_id     = getFromSession('project_id');  $created_by = getFromSession('userid');
	//======= Insert Start ========	
	$sql = "SELECT s.sal_detail_id,s.voucher_no,s.product,p.product_name,p.product_desc,p.catagory,c.catagory_name,p.brand_code,b.brand_name,p.m_unit,s.currency,s.undelivery_qty,s.unit_price,s.discount_per_qty,s.discount_amount  FROM ".PRODUCT_TBL." as p, ".CATAGORY_TBL." as c, ".BRAND_TBL." as b, ".SALES_DETAILS_TBL." as s WHERE s.product = p.product_id AND p.brand_code = b.brand_id AND p.catagory = c.catagory_code AND s.undelivery_qty >0 AND s.customer = '".$customer_id."' GROUP BY s.sal_detail_id";		
	$gres= mysql_query($sql);
	while($row = mysql_fetch_object($gres)){						
		$sales_no 			= $row->voucher_no;						
		$sal_detail_id 			= $row->sal_detail_id;						
		$product 			= $row->product;
		$catagory 			= $row->catagory;
		$brand 				= $row->brand_code;	
		$m_unit 			= $row->m_unit;
		$order_qty			= $row->undelivery_qty;
		$free_qty 			= 0;
		$unit_price 			= $row->unit_price;
		$discount_persent 		= $row->discount_per_qty; 
		$discount_amount		= $row->discount_amount;
		$totalPrice			= ($unit_price*$order_qty);

		$sdsql = "SELECT * FROM ".SALES_DETAILS_TBL." WHERE voucher_no = '".$voucher_no."' AND project_id = '".$project_id."' AND customer = '".$customer_id."' AND product='".$product."' GROUP BY product";
		$sdres = mysql_query($sdsql);
		if(mysql_num_rows($sdres)>0){
		$sdrow = mysql_fetch_object($sdres);
	 	$details_id = $sdrow->sal_detail_id;
		$order_qty  = ($row->undelivery_qty + $sdrow->qty);
		$OrderPrice = ($unit_price*$order_qty);
		$usql = "UPDATE ".SALES_DETAILS_TBL." SET customer='$customer',product='$product',catagory='$catagory',brand_id='$brand',m_unit='$m_unit',
		unit_price='$unit_price',discount_per_qty='$discount_persent',discount_amount='$unitDiscountAmount',qty='$order_qty',
		free_qty='$free_qty',total='$OrderPrice' WHERE sal_detail_id=$details_id AND voucher_no='$voucher_no'";
		mysql_query($usql);
		}else{
		$isql = "INSERT INTO ".SALES_DETAILS_TBL." (voucher_no,project_id,customer,catagory,brand_id,product,m_unit,
		unit_price,discount_per_qty,discount_amount,qty,free_qty,total,created_by) VALUES(		
		'$voucher_no','$project_id','$customer_id','$catagory','$brand','$product','$m_unit',
		'$unit_price','$discount_persent','$discount_amount','$order_qty','$free_qty','$totalPrice','$created_by' 
		)";
		mysql_query($isql);
		} 
		$udsql = "UPDATE ".SALES_DETAILS_TBL." SET undelivery_qty='0' WHERE sal_detail_id=$sal_detail_id AND voucher_no='$sales_no'";
		mysql_query($udsql);
		$totalOrderPrice	+=$totalPrice;
	}
	$msql = "SELECT * FROM ".SALES_MASTER_TBL." WHERE voucher_no = '".$voucher_no."' AND project_id = '".$project_id."' AND customer = '".$customer_id."' GROUP BY voucher_no";		
	
	$mrow = mysql_fetch_object(mysql_query($msql));

	$totalOrderPrice+= $mrow->total_value;	
	
	$general_discount_percent 	= $mrow->general_discount_percent; 
	$GDiscountAmount=(($totalOrderPrice/100)*$general_discount_percent);
	
	$exclusive_discount_percent	= $mrow->exclusive_discount_percent; 
	$deliveryAmountAfterDiscount 	= ($totalOrderPrice - $GDiscountAmount);
	$EDiscountAmount = (($deliveryAmountAfterDiscount/100)*$exclusive_discount_percent);
	
	$additional_discount_percent 	= $mrow->additional_discount_percent;
	$additional_discount 		= $mrow->additional_discount;
	
	$sales_date	= $mrow->sales_date;
	$delivery_date	= $mrow->delivery_date;
	$net_payble     = ($totalOrderPrice-($GDiscountAmount+$EDiscountAmount+$totalProductDis+$additional_discount));
  	$discount       = ($GDiscountAmount+$EDiscountAmount+$totalProductDis+$additional_discount);
  	$umsql = "UPDATE ".SALES_MASTER_TBL." SET total_value= '$totalOrderPrice',general_discount_percent='$general_discount_percent',general_discount_amount='$GDiscountAmount',exclusive_discount_percent='$exclusive_discount_percent',exclusive_discount_amount='$EDiscountAmount',product_discount = '$totalProductDis',discount='$discount',additional_discount_percent='$additional_discount_percent',additional_discount='$additional_discount',delivery_point='$delivery_point',net_payble='$net_payble',due='$net_payble' WHERE voucher_no='$voucher_no' AND project_id = '".$project_id."'";
  	mysql_query($umsql);
	$this->updateSalesVoucher($voucher_no,$customer,$net_payble);
	echo $voucher_no;
   }
   function updateSalesVoucher($voucher_no,$customer,$order_amount){
    	$project_id = getFromSession('project_id');
	$created_date = formatDate(getRequest('sales_date')); 
  	$DrVUpdate="UPDATE ".DEVIT_VOUCHAR_TBL." SET account_head='$customer',debit='$order_amount',created_date='$created_date' WHERE voucher_no='".$voucher_no."' AND project_id = '$project_id'";
	 mysql_query($DrVUpdate);	 
	 $CrVUpdate="UPDATE ".CREDIT_VOUCHAR_TBL." SET credit='$order_amount',created_date='$created_date' WHERE voucher_no='".$voucher_no."' AND project_id = '$project_id'";
	 mysql_query($CrVUpdate);
  } 
   function showEditor($msg = null) {
      
   	   $data                	= array();
       require_once(CLASS_DIR.'/common.list.class.php');	
	   $comListApp 	= new CommonList();
	   $data['customer_list'] 	= $comListApp->getCustomerList();	
	   $data['reference_list'] 	= $comListApp->getReferenceList();     	
	   $data['product_list'] 	= $comListApp->getProductList();	
	   $data['cat_list'] 		= $this->getCatagoryList();	      
	   $data['brand_list'] 		= $comListApp->getBrandList();	   
	   $data['currency_list']       = $this->getCurrencyList();  	
	   $data['area_list'] 		= $comListApp->getAreaList();      	
	   $data['depo_list'] 		= $comListApp->getDeliveryPointList();      	
	   $data['retailer_list'] 	= $comListApp->getRetailerList();
	 	
	   $data['cmd']         	= getRequest('cmd');   
	   require_once(CURRENT_APP_SKIN_FILE); 
	   return $data[0];
   }

  function insertSalesDetails($voucher_no){
		$requestdata 			= array();
		$arr_catagory_product_id	= array();	
		$project_id  			= getFromSession('project_id');
		$currency        		= getRequest('currency');
	
		$arr_catagory_product_id	= getRequest('input_catagory_product_id');
		$arr_brand        		= getRequest('input_brand');
    		$arr_pdetails        		= getRequest('input_pdetails');
		$arr_serial        		= getRequest('input_serial');
		$arr_warranty        		= getRequest('input_warranty');
		$arr_pvno        		= getRequest('input_pvoucher_no');
		$arr_m_unit        		= getRequest('input_m_unit');
		$arr_unit_price			= getRequest('input_unit_price');
		$arr_qty      			= getRequest('input_qty');
		$arr_total_bag      		= getRequest('input_total_bag');
		$arr_total_value       		= getRequest('input_total_value');
	
		for($i=0;$i<count($arr_catagory_product_id);$i++)
		{
		  $catagory_product_sep 	= $arr_catagory_product_id[$i];		
		  $requestdata['project_id']= $project_id;       	  
	
		  for($j=0;$j<count($catagory_product_sep);$j++){ echo $catagory_product_sep;
			$catagory_product = explode("###",$catagory_product_sep);
			$catagoryid  	  = array();
			$productid 	  = array();				
			$brandid 	  = array();					
			$discountu 	  = array();		  
			$catagoryid['c']  = $catagory_product[0];				
			$brandid['b']  	  = $catagory_product[1];				
			$productid['p']   = $catagory_product[2];				
			$discountu['d']   = $catagory_product[4];
		   }
	
		   foreach($catagoryid as $val)
		   {
				$requestdata['catagory'] = $val;	
		   }
		   foreach($brandid as $val){
				$requestdata['brand_id']= $val; $brand_id = $val;
		   }	
		   foreach($productid as $val){
				$requestdata['product'] =$val;	
				$product_id				=$val;
		   }	
		   foreach($discountu as $val){
				echo $requestdata['discount_per_qty'] =$val;				
		   }
		   foreach($arr_m_unit as $key => $val){
			  if($catagory_product_sep==$key){
				$requestdata['m_unit'] = $val;	
			  }
		   }	    
		   foreach($arr_pdetails as $key => $val){
			  if($catagory_product_sep==$key){
				$requestdata['details'] = $val;
			  }
		   }
		   $requestdata['serial']=0; $serial = 0;
		   foreach($arr_warranty as $key => $val){
			  if($catagory_product_sep==$key){
				$requestdata['warranty'] = $val;  $warranty = $val; 
			  }
		   }
		   foreach($arr_pvno as $key => $val){
			  if($catagory_product_sep==$key){
				$pvno = $val; 
			  }
		   }   	  
		   foreach($arr_unit_price as $key => $val){
			  if($catagory_product_sep==$key){
				$requestdata['unit_price'] = $val;	
			  }
		   }
		   foreach($arr_qty as $key => $val){
				if($catagory_product_sep==$key){
					 $requestdata['qty'] = $val;	
					 $productQty	     = $val;
				}
		   }		      	  
		   foreach($arr_total_bag as $key => $val){
				if($catagory_product_sep==$key){
					 $requestdata['total_bag'] = $val;	
				}
		   }	
		   foreach($arr_total_value as $key => $val){
			  if($catagory_product_sep==$key){
				 $requestdata['total'] = $val; 	
			  }
		   }
		   	
		   $requestdata['discount_amount'] = (($requestdata['unit_price']/100)*$requestdata['discount_per_qty']);		
		    $requestdata['created_by'] 		= getFromSession('userid');
		    $requestdata['created_date']      	= date('Y-m-d h:i:s');  
		    $project_id				= getFromSession('project_id'); 
		    $requestdata['project_id']        	= $project_id;
		    if(getRequest('wo_no')!=""){
		    $requestdata['wo_no']        	= getRequest('wo_no');
		    }else{
		    $requestdata['wo_no']        	= $voucher_no;
		    }
		    $requestdata['voucher_no']        	= $voucher_no;
		    $requestdata['lc_no']        	= getRequest('lc_no');
		    $requestdata['customer']        	= getRequest('customer');
		    $requestdata['reference']       	= getRequest('reference');
 		    $customer    			= getRequest('customer');
		    $CSql="SELECT division,district,area FROM ".SUB_ACC_HEAD_TBL." WHERE head_type='Customer' AND sub_id='$customer' AND project_id='$project_id' ";
		    $Crow = mysql_fetch_object(mysql_query($CSql));
		    $requestdata['division'] 	= $Crow->division;
		    $requestdata['district'] 	= $Crow->district;
		    $requestdata['area']	= $Crow->area;
		       
		    //$info['debug']  	=  true;
		   $Prosql = "SELECT product_catagory FROM ".PRODUCT_TBL." WHERE product_id = '$product_id' AND project_id = '$project_id'";
		   $Prorow = mysql_fetch_object(mysql_query($Prosql));
		   $product_catagory 		= $Prorow->product_catagory;
		   if($product_catagory=="Serial"){
			$pq=1;
			while($pq<=$productQty){
			$requestdata['qty'] 	= 1;
			$requestdata['total'] 	= ($requestdata['unit_price']*1);
			$requestdata['delivery_qty'] = 0;
			$requestdata['serial'] = $pq;
			$info        		=  array();
			$info['table']	= SALES_DETAILS_TBL;
			$info['data'] 	= $requestdata; 
			$res = insert($info);
			$pq++;	
			}			
		   }else{
			$info        		=  array();
			$info['table']	= SALES_DETAILS_TBL;
			$info['data'] 	= $requestdata;   
			$res = insert($info);	
		   }
	   }
 

  } //End of the function insertSalesDetails()

 //==================== saveDebitVouchar ====================
 	function saveDebitVouchar()
 	{     
 	   	  $mode_of_payment = getRequest('mode_of_payment');
		  
		  $requestdata = array();
	
		  $requestdata = getUserDataSet(DEVIT_VOUCHAR_TBL);	
		  if($mode_of_payment=="Recievable"){
			//======= Party Dr ======
			$requestdata['account_head']= getRequest('customer');  
		  	$requestdata['debit']       = getRequest('net_payble'); 
		  	$requestdata['credit']      = 0;   
		  	$requestdata['paid_amount'] = 0;      
		  	$requestdata['due']         = 0;        
		  	$requestdata['head_type']   = "Acc"; 
		  }
  
		  $requestdata['transaction_type']  = "Sales Order";
		  $requestdata['vouchar_type']      = "Sales Order";		  
		  $requestdata['project_id']        = getFromSession('project_id');    
		  $requestdata['created_by']        = getFromSession('userid'); 
	
		  $requestdata['created_date']      = formatDate(getRequest('sales_date')); //date('Y-m-d h:i:s');
	
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
			return $voucher_no;
		  }else {	
			return "";
		  }  
	 

    }//EOFn  

    function saveCreditVouchar($voucher_no)
 	{     
 	   	 $mode_of_payment = getRequest('mode_of_payment');		  
		 $requestdata = array();	
		 $requestdata = getUserDataSet(CREDIT_VOUCHAR_TBL);
		 if($mode_of_payment=="Recievable"){
			//======= Party Dr ======
			$requestdata['account_head']     	= $this->getRecievableId(getFromSession('project_id')); 
		  	$requestdata['credit']        		= getRequest('net_payble'); 
		  	$requestdata['debit']        		= 0;     
		  	$requestdata['head_type']     		= "Acc"; 
		 }
	 	  $requestdata['transaction_type']  = "Sales Order";     
		  $requestdata['head_type']     	= "Acc"; 
		  $requestdata['project_id']        = getFromSession('project_id');    
		  $requestdata['created_by']        = getFromSession('userid'); 
	
		  $requestdata['created_date']      = formatDate(getRequest('sales_date')); //date('Y-m-d h:i:s');	
		  $requestdata['voucher_no']   	= $voucher_no;
		 
		  $info        		=  array();
		  $info['table']	= CREDIT_VOUCHAR_TBL;
		  $info['data'] 	= $requestdata;     
		  //$info['debug']  	=  true;
		  $res = insert($info);
		  $created_date = $requestdata['created_date']; 
	
		  if($res['affected_rows']) {
			
			return true;
			
		  }else {	
			return false;
	
		  } 	 

    }//EOFn  
	function insertSalesMaster($voucher_no){
    	  $requestdata = array();	
		  $requestdata = getUserDataSet(SALES_MASTER_TBL);		 
		  $requestdata['transaction_type']  = "Sales Order";  
		  $requestdata['sales_date'] 		= formatDate(getRequest('sales_date'));  
		  $requestdata['delivery_date'] 	= formatDate(getRequest('delivery_date')); 
		  $requestdata['voucher_no']        = $voucher_no; 
		  if(getRequest('wo_no')!=""){
		  $requestdata['wo_no']        	  	= getRequest('wo_no');
		  }else{
		  $requestdata['wo_no']        		= $voucher_no;
		  }
		  //======== Sales Commission ============
		  if(getRequest('reference')!=""){
			  $cSql="SELECT * FROM ".COMMISSION_SLOT_TBL." WHERE cid=1 AND project_id='$project_id'";
			  $crow = mysql_fetch_object(mysql_query($cSql));
			  if($requestdata['net_payble']<=$crow->slot_range1){
			  $commission_slot = $crow->slot1;
			  }elseif($requestdata['net_payble']<=$crow->slot_range2){
			  $commission_slot = $crow->slot2;
			  }elseif($requestdata['net_payble']<=$crow->slot_range3){
			  $commission_slot = $crow->slot3;
			  }elseif($requestdata['net_payble']<=$crow->slot_range4){
			  $commission_slot = $crow->slot4;
			  }
			  $total_commission = (($requestdata['net_payble']/100)*$commission_slot); 
			  $commission_total_due = $total_commission;  
			  $requestdata['commission_slot'] 	= $commission_slot;
			  $requestdata['total_commission'] 	= $total_commission;
			  $requestdata['commission_total_due'] = $commission_total_due;
		  }
		  $customer    = getRequest('customer');
		  $project_id  = getFromSession('project_id');    
		  $CSql="SELECT division,district,area FROM ".SUB_ACC_HEAD_TBL." WHERE head_type='Customer' AND sub_id='$customer' AND project_id='$project_id' ";
		  $Crow = mysql_fetch_object(mysql_query($CSql));
		  $requestdata['division'] 	= $Crow->division;
		  $requestdata['district'] 	= $Crow->district;
		  $requestdata['area']	    	= $Crow->area;

		  $general_discount_amount 		= getRequest('general_discount_amount');
		  $exclusive_discount_amount 	= getRequest('exclusive_discount_amount');
		  $additional_discount 			= getRequest('additional_discount');
		  $product_discount 			= getRequest('discount');
		  $requestdata['product_discount'] = getRequest('discount');
		  $TotalDiscount = ($general_discount_amount+$exclusive_discount_amount+$additional_discount+$product_discount);	
		  $requestdata['discount']	    = $TotalDiscount;	
		  $requestdata['project_id']    = getFromSession('project_id');    
		  $requestdata['created_by']    = getFromSession('userid');	
		  $requestdata['created_date']  = date('Y-m-d h:i:s');
	
		  $info        		=  array();
		  $info['table']	= SALES_MASTER_TBL;
		  $info['data'] 	= $requestdata;     
		  //$info['debug']  =  true;
		  $res = insert($info);
			
	}
   function saveSalesItem(){
		mysql_query("START TRANSACTION;");
		$voucher_no = $this->saveDebitVouchar();	
		$this->saveCreditVouchar($voucher_no);
		$this->insertSalesMaster($voucher_no);
		$this->insertSalesDetails($voucher_no);
		mysql_query("COMMIT;");
		if($voucher_no!=""){
		header("location:index.php?app=sales_order&cmd=print_vouchar&voucher_no=".$voucher_no);	
		}else{
		header("location:index.php?app=sales_order&cmd=add");
		}
   }
   function saveAccountJournal($voucher_no,$sub_id,$head_type,$project_id,$description,$DR=NULL,$CR=NULL,$balance,$status=NULL,$purchare_date=NULL){
		$rsql= "SELECT head_type FROM ".SUB_ACC_HEAD_TBL." WHERE sub_id='".$sub_id."'";  
		$rres = mysql_query($rsql);
		$hnum = mysql_num_rows($rres);
		if($hnum>0){ 
		$hrow = mysql_fetch_object($rres);
		$head_type= $hrow->head_type;
		}else{ 	$head_type= "Supplier"; }		
		$sql = "INSERT INTO ".ACCOUNT_JOURNAL_TBL." (voucher_no,created_date,sub_id,head_type,project_id,description,dr,cr,balance,status) VALUES('".$voucher_no."','".$purchare_date."','".$sub_id."','".$head_type."','".$project_id."','".$description."','".$DR."','".$CR."','".$balance."','".$status."')";
		mysql_query($sql);
	}
   function getSalesMasterInfo($id){	
		$project_id     = getFromSession('project_id');  
		$SQLMain = "";  
		$SQL = "
		SELECT pm.voucher_no,pm.delivery_point,pm.po_no,pm.wo_no,p.project_name,p.project_logo,p.location,pm.customer,COALESCE((s.sub_id), (sp.supplier_code)) as sub_id,COALESCE((s.sub_head_name), (sp.name)) as sub_head_name,s.code,COALESCE((s.head_details), (sp.address)) as head_details,COALESCE((s.phone), (sp.phone)) as phone,COALESCE((s.mobile), (sp.mobile)) as mobile,COALESCE((s.email), (sp.email)) as email,COALESCE((s.att_name1), (sp.contact_person)) as att_name1,COALESCE((s.att_designation1), (sp.designation)) as att_designation1,COALESCE((s.att_mobile1), (sp.contact_person_mobile)) as att_mobile1,pm.gate_pass,pm.track_no,pm.salse_type,pm.total_value as order_amount,pm.total_value,pm.sales_date as order_date,DATE_FORMAT(pm.sales_date,'%d %b %y' ) as sales_date,DATE_FORMAT(pm.delivery_date,'%d %b %y' ) as delivery_date,pm.service_charge,pm.mode_of_payment,c.curr_symble,pm.bank_name,pm.acc_no,pm.check_no,pm.adjust,pm.general_discount_percent,pm.general_discount_amount,pm.exclusive_discount_percent,pm.exclusive_discount_amount,pm.additional_discount_percent,pm.additional_discount,pm.product_discount, pm.discount,pm.additional_cost,pm.net_payble,pm.paid_amount,pm.due,pm.item_delivery_amount,pm.return_amount,pm.ref_no,pm.created_date,DATE_FORMAT(pm.created_date,'%d %b %y' ) as date,pm.reference,pm.description,pm.vat_type,pm.additional_vat_percent,pm.additional_vat_amount,r.retailer_name,pm.created_by,pm.approved_by,pm.contact_person,pm.ref_voucher";
		if($delivery_master_id !=""){
		  $SQL.=",sdm.total_value as delivery_amount,sdm.previour_balance,sdm.challan_no,sdm.consignee,sdm.sales_delivery_master_id,DATE_FORMAT(sdm.delivery_date,'%d %b %y' ) as delivery_date, sdm.challan_no, sdm.consignee ";
		}
		
		$SQLTBL="
		FROM ".SALES_MASTER_TBL." pm
		LEFT JOIN ".SUB_ACC_HEAD_TBL." s ON BINARY s.sub_id =pm.customer
		LEFT JOIN ".SUPPLIER_TBL." sp ON BINARY sp.supplier_code = pm.customer
		LEFT JOIN ".PROJECT_TBL." p ON p.project_id  =pm.project_id
		LEFT JOIN ".CURRENCY_TBL." c ON c.currency_id  =pm.currency
		LEFT JOIN ".RETAILER_TBL." r ON r.retailer_id  =pm.retailer_id
		";
		if($delivery_master_id !=""){
		  $SQLTBL.=" LEFT JOIN ".SALES_DELIVERY_MASTER_TBL." sdm ON sdm.voucher_no = pm.voucher_no ";
		}
		
		$SQLWhere =" WHERE pm.project_id = '".$project_id."' AND pm.voucher_no = '".$id."'";		
		
		if($delivery_master_id !=""){
		  $SQLWhere.=" AND sdm.sales_delivery_master_id='$delivery_master_id'";
		}
		$SQLMain = 	$SQL.$SQLTBL.$SQLWhere." GROUP BY pm.voucher_no";				
		
		$res     = query($SQLMain);		
		$data    = array();
			
		if(count($res) >0){
			foreach($res as $i=>$v){
				$data[$i] = $v;             
			}
		}
		  //dumpVar($data);
		return $data[0];
   } 
        
   function getProductList($id) {  

		$info           = array();    
		$info['table']  =  SALES_DETAILS_TBL.' sd,'.PRODUCT_TBL.' p,'.CURRENCY_TBL.' c,'.BRAND_TBL.' b';	
		$info['fields'] = array('sd.sal_detail_id','sd.voucher_no','sd.project_id','sd.serial','sd.warranty','sd.catagory','b.brand_name','sd.product','sd.details','p.product_name','p.product_desc','p.product_code','sd.m_unit','sd.unit_price','c.curr_symble','sd.discount_per_qty','sd.qty','sd.free_qty','sd.delivery_qty','sd.total_bag','sd.total','sd.vat','sd.vat_amount','sd.is_order_complete','sd.created_time');
		
		$sql="sd.product = p.product_id AND p.brand_code = b.brand_id AND sd.currency = c.currency_id AND sd.voucher_no = '$id'";
		
		$info['where']  = $sql;
	    $info['groupby'] = array("sd.sal_detail_id");
		$info['orderby'] = array("sd.sal_detail_id asc");
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
     //==== Start Original Sales Copy ======
     	
     function getOriginalSalesMasterInfo($id){	
	$project_id     = getFromSession('project_id');  
	$SQLMain = "";  
	$SQL = "
	SELECT pm.voucher_no,pm.delivery_point,pm.po_no,pm.wo_no,p.project_name,p.project_logo,p.location,pm.customer,COALESCE((s.sub_id), (sp.supplier_code)) as sub_id,COALESCE((s.sub_head_name), (sp.name)) as sub_head_name,COALESCE((s.head_details), (sp.address)) as head_details,COALESCE((s.phone), (sp.phone)) as phone,COALESCE((s.mobile), (sp.mobile)) as mobile,COALESCE((s.email), (sp.email)) as email,COALESCE((s.att_name1), (sp.contact_person)) as att_name1,COALESCE((s.att_designation1), (sp.designation)) as att_designation1,COALESCE((s.att_mobile1), (sp.contact_person_mobile)) as att_mobile1,pm.gate_pass,pm.track_no,pm.salse_type,pm.total_value as order_amount,pm.total_value,DATE_FORMAT(pm.sales_date,'%d %b %y' ) as sales_date,pm.service_charge,pm.mode_of_payment,c.curr_symble,pm.bank_name,pm.acc_no,pm.check_no,pm.adjust,pm.general_discount_percent,pm.general_discount_amount,pm.exclusive_discount_percent,pm.exclusive_discount_amount,pm.additional_discount_percent,pm.additional_discount,pm.product_discount, pm.discount,pm.net_payble,pm.paid_amount,pm.due,pm.item_delivery_amount,pm.return_amount,pm.ref_no,pm.created_date,DATE_FORMAT(pm.created_date,'%d %b %y' ) as date,pm.reference,pm.description";
	if($delivery_master_id !=""){
	  $SQL.=",sdm.total_value as delivery_amount,sdm.previour_balance,sdm.challan_no,sdm.consignee,sdm.sales_delivery_master_id,DATE_FORMAT(sdm.delivery_date,'%d %b %y' ) as delivery_date, sdm.challan_no, sdm.consignee ";
	}
	
	$SQLTBL="
	FROM ".SALES_MASTER_APP_TBL." pm
	LEFT JOIN ".SUB_ACC_HEAD_TBL." s ON BINARY s.sub_id =pm.customer
	LEFT JOIN ".SUPPLIER_TBL." sp ON BINARY sp.supplier_code = pm.customer
	LEFT JOIN ".PROJECT_TBL." p ON p.project_id  =pm.project_id
	LEFT JOIN ".CURRENCY_TBL." c ON c.currency_id  =pm.currency
	";
	if($delivery_master_id !=""){
	  $SQLTBL.=" LEFT JOIN ".SALES_DELIVERY_MASTER_TBL." sdm ON sdm.voucher_no = pm.voucher_no ";
	}
	
	$SQLWhere =" WHERE pm.project_id = '".$project_id."' AND pm.voucher_no = '".$id."'";		
	
	if($delivery_master_id !=""){
	  $SQLWhere.=" AND sdm.sales_delivery_master_id='$delivery_master_id'";
	}
	$SQLMain = 	$SQL.$SQLTBL.$SQLWhere." GROUP BY pm.voucher_no";				
	
	$res     = query($SQLMain);		
	$data    = array();
		
	if(count($res) >0){
		foreach($res as $i=>$v){
			$data[$i] = $v;             
		}
	}
	  //dumpVar($data);
	return $data[0];
    } 
        
    function getOriginalProductList($id) {  

	$info           = array();    
	$info['table']  =  SALES_DETAILS_APP_TBL.' sd,'.PRODUCT_TBL.' p,'.CURRENCY_TBL.' c,'.BRAND_TBL.' b';	
	$info['fields'] = array('sd.sal_detail_id','sd.voucher_no','sd.project_id','sd.serial','sd.warranty','sd.catagory','b.brand_name','sd.product','sd.details','p.product_name','p.product_desc','sd.m_unit','sd.unit_price','c.curr_symble','sd.discount_per_qty','sd.qty','sd.free_qty','sd.delivery_qty','sd.total_bag','sd.total','sd.created_time');
	
	$sql="sd.product = p.product_id AND p.brand_code = b.brand_id AND sd.currency = c.currency_id AND sd.voucher_no = '$id'";
	
	$info['where']  = $sql;
        $info['groupby']= array("sd.sal_detail_id");
	$info['orderby']= array("sd.sal_detail_id asc");
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
  
	function loadProduct4Catagory($catagory)
	{	  
		  $brand_id		   = trim(getRequest('brand_id'));
	  	  $project_id = getFromSession('project_id');  
		  $info            = array();
		  $info['table']   = PRODUCT_TBL;
		  $info['fields']  =  array('product_id','product_name','product_desc');
		  $info['where']   = "`brand_code`='$brand_id' AND project_id = '$project_id'";
		  $info['groupby'] = array("product_id");
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
			 $subject_idname .= $v[0]->product_id.'#####'.$v[0]->product_code.'-'.$v[0]->product_name.'#####'.$v[0]->product_desc.'@@@';
		  }
		  echo $subject_idname;	
	}

 function loadProductDtl($product_id){
	  $project_id = getFromSession('project_id');  		 
	  $info            = array();
	  	  
	  $info['table']   = PRODUCT_TBL." p,".CATAGORY_TBL.' c,'.BRAND_TBL.' b';
	  $info['fields']  =  array('p.m_unit','p.product_desc','p.unit_price','p.product_catagory','p.catagory','c.catagory_name','p.brand_code','b.brand_name');
	  $info['where']   = "p.catagory =c.catagory_code AND p.brand_code =b.brand_id AND p.product_id = '$product_id'  AND p.project_id = '$project_id'";
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
			
	  foreach($data as $i=>$v)
	  {
		$str = $v[0]->m_unit."#####".$v[0]->product_desc."#####".$v[0]->unit_price."#####".$v[0]->catagory."###".$v[0]->catagory_name."#####".$v[0]->brand_code."###".$v[0]->brand_name;
	  }
	  echo $str;	
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
 
 
  function getCustomerList()
  {		  
	  $project_id  		= getFromSession('project_id');
      $data 			= array(); 
      $info        		= array();
      $info['table']	= SUB_ACC_HEAD_TBL;
	  $info['where']  	= "head_type = 'Customer' AND project_id='".$project_id."'";	  	
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
	function saveStockJournal($voucher_no,$project_id,$product_id,$unit_price=NULL,$m_unit,$DR=NULL,$CR=NULL,$balance,$create_date=NULL){
		$sql = "INSERT INTO ".STOCK_LEDGER_TBL." (voucher_no,project_id,product_id,unit_price,m_unit,dr,cr,balance,create_date) VALUES('".$voucher_no."','".$project_id."','".$product_id."','".$unit_price."','".$m_unit."','".$DR."','".$CR."','".$balance."','".$create_date."')";
		mysql_query($sql);
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
 //================= Start Sales Details ====================

function showEditor4SalesDetails($msg = null) {        

	  $data                				= array();
	  $data['cmd']         				= getRequest('cmd');
	  $data['record_list'] 				= $this->getSalesDetailsList(getRequest('from'),getRequest('to'));
	  $data['totalrecord']				= $this->getTotalSalesDetailsList(getRequest('from'),getRequest('to'));	
		
	   require_once(SALES_DETAILS_SKIN); 

	   return $data[0];

   }
	function showAllCompaniesSalesDetails($msg = null) {        

	  $data                				= array();
	  $data['cmd']         				= getRequest('cmd');
	  $data['record_list'] 				= $this->getAllSalesDetailsList(getRequest('from'),getRequest('to'));
	  $data['totalrecord']				= $this->getAllTotalSalesDetailsList(getRequest('from'),getRequest('to'));		
	  require_once(ADMIN_SALES_DETAILS_SKIN); 
		
	  return $data[0];

   }

	function getSalesDetailsList($from,$to) { 

		if($from == "" && $to == ""){$from=0; $to=500;}
		$date_from 			= formatDate(getRequest('date_from'));
		$date_to 			= formatDate(getRequest('date_to'));				
		$project_id     = getFromSession('project_id');  
		$info           = array();    
		$info['table']  = SALES_MASTER_TBL.' pm,'.SUB_ACC_HEAD_TBL.' s,'.PROJECT_TBL.' p,'.CURRENCY_TBL.' c';	
		$info['fields'] = array('pm.voucher_no','p.project_name','p.location','s.sub_id','s.sub_head_name','s.head_details','pm.po_no','pm.wo_no','pm.gate_pass','pm.track_no','pm.salse_type','pm.total_value',"DATE_FORMAT(pm.sales_date,'%d %b %y' ) as sales_date","DATE_FORMAT(pm.delivery_date,'%d %b %y' ) as delivery_date",'pm.mode_of_payment','c.curr_symble','pm.bank_name','pm.acc_no','pm.check_no','pm.check_no','pm.discount','pm.net_payble','pm.paid_amount','pm.due','pm.ref_no','pm.created_date',"DATE_FORMAT(pm.created_date,'%d %b %y' ) as date");
		
		$sql="pm.customer = s.sub_id AND pm.project_id = p.project_id AND pm.currency = c.currency_id AND pm.project_id = '".$project_id."'";
							
		
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

   function getTotalSalesDetailsList($from,$to) {  
		
		$date_from 			= formatDate(getRequest('date_from'));
		$date_to 			= formatDate(getRequest('date_to'));				
		$project_id     = getFromSession('project_id');  
		$info           = array();    
		$info['table']  = SALES_MASTER_TBL.' pm,'.SUB_ACC_HEAD_TBL.' s,'.PROJECT_TBL.' p,'.CURRENCY_TBL.' c';	
		$info['fields'] = array('pm.voucher_no');
		
		$sql="pm.customer = s.sub_id AND pm.project_id = p.project_id AND pm.currency = c.currency_id AND pm.project_id = '".$project_id."'";
							
		
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

	function getAllSalesDetailsList($from,$to) { 

		if($from == "" && $to == ""){$from=0; $to=500;}
		$date_from 			= formatDate(getRequest('date_from'));
		$date_to 			= formatDate(getRequest('date_to'));				
		$project_id     = getFromSession('project_id');  
		$info           = array();    
		$info['table']  = SALES_MASTER_TBL.' pm,'.SUB_ACC_HEAD_TBL.' s,'.CURRENCY_TBL.' c';	
		$info['fields'] = array('pm.voucher_no','pm.project_id','s.sub_id','s.sub_head_name','s.head_details','pm.gate_pass','pm.track_no','pm.salse_type','pm.total_value',"DATE_FORMAT(pm.sales_date,'%d %b %y' ) as sales_date",'pm.mode_of_payment','c.curr_symble','pm.bank_name','pm.acc_no','pm.check_no','pm.check_no','pm.discount','pm.net_payble','pm.paid_amount','pm.due','pm.ref_no','pm.created_date',"DATE_FORMAT(pm.created_date,'%d %b %y' ) as date");
		
		$sql="pm.customer = s.sub_id AND pm.currency = c.currency_id";
							
		
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

   function getAllTotalSalesDetailsList($from,$to) {  
		
		$date_from 			= formatDate(getRequest('date_from'));
		$date_to 			= formatDate(getRequest('date_to'));				
		$project_id     = getFromSession('project_id');  
		$info           = array();    
		$info['table']  = SALES_MASTER_TBL.' pm,'.SUB_ACC_HEAD_TBL.' s,'.CURRENCY_TBL.' c';	
		$info['fields'] = array('pm.voucher_no');
		
		$sql="pm.customer = s.sub_id AND pm.currency = c.currency_id";
							
		
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
	  $requestdata['created_date']      = formatDate(getRequest('sales_date'));
	  $requestdata['acc_head'] 			= getRequest('customer'); 
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
//==================== End Sales Details =====================
   
} // End class


?>
