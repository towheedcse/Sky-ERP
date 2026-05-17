<?php
class PhysicalStockVerification
{
   
   function run()
   {         
      $cmd 	  = getRequest('cmd');
	  $u_t_id = getFromSession('u_type_id');
      if( ($u_t_id ==101) || ($u_t_id == 108)  ) // 101 = sysadmin, 102 = admin, 103= salesman, 101 = Physical Verification 
      {

      	switch ($cmd)
      	{
      	   case 'verify'		: $this->showEditor(); break;
	   case 'edit'			: $this->showEditor(); break;
      	   case 'loadProduct'  		: $this->loadProduct4Catagory(trim(getRequest('catagory_id'))); break; 
      	   case 'get_dtl'  		: $this->loadProductDtl(trim(getRequest('product_id'))); break;  
      	   case 'save_tmp'  		: $this->saveTempSales(); break;   
	   case 'deltemp'		: $this->delTempSales(); break;    
	   case 'save_transfer'		: $this->saveSalesItem(); break; 
	   case 'print_verification'	: $screen = $this->showPrintEditor($msg); break;
	   case 'get_product_info'	: $this->getProductInfo(); break;
	   case 'stock_bulk_import'	: $this->stockBulkImport(); break;
      	   default                   	: $cmd = 'list'; $screen = $this->showEditor();   break; 
	   case 'load_stock'            : $this->loadProductStock(trim(getRequest('product_id')));break; 
      	}
      }else {
      	header("location:index.php?app=user_home&msg=You are not authorised !!!");
      }     	          

      if($cmd == 'list') {         
       require_once(CURRENT_APP_SKIN_FILE);
      } 
      return true;
   } 
  
   function showPrintEditor($msg = null) {   	  
	  $verify_no 	= getRequest('verify_no');  
	  if ($verify_no) {
         	 $advArr 		= $this->getVerifyMasterInfo($verify_no);
         	 $advArr 		= parseThisValue($advArr); 
		 $data   		= array_merge(array(), $advArr); 
		 $data['item_list']	= $this->getProductList($verify_no);
		 $data['message'] 	= $msg;
		 $data['cmd']     	= getRequest('cmd');
		 require_once(PRNIT_STOCK_VERIFICATION_SKIN);      
		 return true;
	 }
   }
     
   function showEditor($msg = null) {
   	   $data                	= array();
           require_once(CLASS_DIR.'/common.list.class.php');	
	   $comListApp 	= new CommonList();
	  
	   $data['product_list'] 	= $comListApp->getProductList(); 
	   $data['brand_list'] 		= $comListApp->getBrandList();	   
	   $data['currency_list']   	= $this->getCurrencyList();  	
	   $data['depo_list'] 		= $comListApp->getDeliveryPointList(true); 
	   $data['tmp_sales']		= $this->getTempSales();

	   $supplierData = $comListApp->getSupplierData();
           $data['supplierData'] = json_encode($supplierData, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);

	   $data['equipment_list'] = $comListApp->getAccountHeadList("Non Current Assets", "S126");
           $data['raw_material_list'] = $comListApp->getAccountHeadList("Current Assets", NULL, "C000055");
           $data['fg_list'] = $comListApp->getAccountHeadList("Current Assets", NULL, "C000056");
           $data['maintanance_list'] = $comListApp->getAccountHeadList("Current Assets", NULL, "C000154");

           $data['opening_list'] = $comListApp->getAccountHeadList("Opening Balance");
           $data['closing_list'] = $comListApp->getAccountHeadList("Closing Balance");
           $data['adjustments_list'] = $comListApp->getAccountHeadList("Adjustments Balance");
           $data['cost_list'] = $comListApp->getAccountHeadList("Cost Center");	   
	   
	   $data['cmd']         	= getRequest('cmd');   
	   require_once(CURRENT_APP_SKIN_FILE); 
	   return $data[0];
   }
	
  //===== Saart Save Sales ====
  
	function saveTempSales(){
		$str 			= getRequest('str');
		$strArr 		= explode("####",$str);
		//======= Insert into tamp ========	
		$requestdata = array();
		$requestdata = getUserDataSet(TEMP_STOCK_VERIFY_TBL);
		$project_id  				= getFromSession('project_id');
		$requestdata['project_id'] 		= $project_id;
		$requestdata['delivery_point'] 	= getRequest('delivery_point');
		$requestdata['verification_date'] 	= formatDate(getRequest('verification_date'));
		$requestdata['currency'] 		= getRequest('currency');
		$requestdata['currencyName'] 	= getRequest('currencyName');
		$requestdata['productid'] 		= getRequest('productid');
		$sql = "SELECT p.product_name,p.catagory,c.catagory_name,p.brand_code,b.brand_name,p.m_unit FROM ".PRODUCT_TBL." as p,".CATAGORY_TBL." as c,".BRAND_TBL." as b 
		WHERE p.catagory=c.catagory_code AND p.brand_code=b.brand_id AND BINARY p.product_id = '".$requestdata['productid']."'";
		$row 		= mysql_fetch_object(mysql_query($sql));		
		$product_name	= $row->product_name;
		$product_name 	= str_replace('"',"&ldquo;",$product_name);
		$product_name 	= str_replace("'","&#8217;",$product_name);
		$requestdata['product_name'] 	= $product_name;  	
		$requestdata['catagory'] 		= $row->catagory;		
		$requestdata['catagoryname'] 	= $row->catagory_name;
		$requestdata['brand_id'] 		= $row->brand_code;	
		$requestdata['brandname'] 		= $row->brand_name;	
		//$requestdata['details'] 		= getRequest('details');
		$requestdata['munit'] 			= $row->m_unit;
		$requestdata['stock_qty']		= getRequest('stock_qty');
		$requestdata['qty'] 			= getRequest('qty');
		$requestdata['variation'] 		= ($requestdata['qty']-$requestdata['stock_qty']);
		$requestdata['unit_price'] 		= getRequest('unit_price');
		$requestdata['total'] 			= getRequest('total');
		
		$requestdata['created_by'] 		= getFromSession('userid');		
		$info        		=  array();
		$info['table']	= TEMP_STOCK_VERIFY_TBL;
		$info['data'] 	= $requestdata;     
		//$info['debug']  	=  true;
		$res = insert($info);
		  
		$str1="<table width='100%' align='center' bgcolor='#99CC66'>
		<tr style='color:#fff;' bgcolor='#00B000' height=28>
		  <td width='2%' nowrap><div align='left'>SL</div></td>
		  <td width='30%' nowrap><div align='left'>Product Name</div></td>
		  <td width='13%' nowrap><div align='left'>Catagory</div></td>
		  <td width='10%' nowrap><div align='left'>Brand</div></td>
		  <td width='10%' nowrap><div align='right'>Stock Qty</div></td>
		  <td width='10%' nowrap><div align='right'>Verified Qty</div></td>
		  <td width='8%' nowrap><div align='right'>Rate</div></td>	  
		  <td width='10%' nowrap><div align='right'>Amount</div></td>				  
		  <td width='7%' nowrap align='center'>Option</td>
		</tr>";
		$total_value = 0; $sl=1; $TotalQty=0;
		$getSql		= "SELECT * FROM ".TEMP_STOCK_VERIFY_TBL." WHERE BINARY created_by = '".getFromSession('userid')."' AND project_id='".$project_id."'";
		$gres 		= mysql_query($getSql);
		while($row = mysql_fetch_array($gres)){
		extract($row);
		$total_value+=$total; $TotalQty+=$qty;
		$str2.="
		<tr style='color:#000000' bgcolor='#fff'>
		  <td width='2%' nowrap align='left'>$sl</td>
		  <td width='30%' nowrap align='left'>$product_name</td>
		  <td width='13%' nowrap align='left'>$catagoryname</td>
		  <td width='10%' nowrap align='left'>$brandname</td>
		  <td width='10%' nowrap><div align='right'>$stock_qty $munit</div></td>
		  <td width='10%' nowrap><div align='right'>$qty $munit</div></td>
		  <td width='8%' nowrap align='right'>$unit_price $currencyName</td>	  
		  <td width='10%' nowrap align='right'>$total</td>				  				  
		  <td width='7%' nowrap align='center'><a href=\"?app=physical.stock.verification&cmd=deltemp&id=$tmp_id\"><img src=\"images/common/icons/delete.gif\"></a></td>
		</tr>";  $sl++;
		}
		$str3="
		<tr style='color:#000;' bgcolor='#CCCCCC' height=25>
		  <td colspan='5' nowrap><div align='right'>Total </div></td>
		  <td nowrap align='right'>$TotalQty $munit</td>
		  <td nowrap>&nbsp;</td>
		  <td nowrap align='right'>$total_value $currencyName</td>
		  <td nowrap align='center'>&nbsp;</td>
		</tr>
		</table>";
		echo $str1.$str2.$str3."####-@@@@".$total_value;
	}
	function delTempSales(){
		$tmp_id = $_REQUEST['id'];
		if($tmp_id!=""){
		 $dsql = "DELETE FROM ".TEMP_STOCK_VERIFY_TBL." WHERE tmp_id ='".$tmp_id."'";
		 mysql_query($dsql);
		}		
		header("location:?app=physical.stock.verification&cmd=verify");
	}
	function getTempSales(){
		$project_id  	= getFromSession('project_id');
		$str1="<table width='100%' align='center' bgcolor='#99CC66'>
		<tr style='color:#fff;' bgcolor='#00B000' height=28>
		  <td width='2%' nowrap><div align='left'>SL</div></td>
		  <td width='30%' nowrap><div align='left'>Product Name</div></td>
		  <td width='13%' nowrap><div align='left'>Catagory</div></td>
		  <td width='10%' nowrap><div align='left'>Brand</div></td>
		  <td width='10%' nowrap><div align='right'>Stock Qty</div></td>
		  <td width='10%' nowrap><div align='right'>Verified Qty</div></td>
		  <td width='8%' nowrap><div align='right'>Rate</div></td>	  
		  <td width='10%' nowrap><div align='right'>Amount</div></td>				  
		  <td width='7%' nowrap align='center'>Option</td>
		</tr>";
		$total_value = 0; $product_discount=0; $TotalQty=0; $TotalFreeQty=0; $sl=1;
		$getSql		= "SELECT * FROM ".TEMP_STOCK_VERIFY_TBL." WHERE BINARY created_by = '".getFromSession('userid')."' AND project_id='".$project_id."'";
		$gres 		= mysql_query($getSql);
		while($row = mysql_fetch_array($gres)){
		extract($row);
		$total_value+=$total; $TotalQty+=$qty;
		$str2.="
		<tr style='color:#000000' bgcolor='#fff'>
		  <td width='2%' nowrap align='left'>$sl</td>
		  <td width='30%' nowrap align='left'>$product_name</td>
		  <td width='13%' nowrap align='left'>$catagoryname</td>
		  <td width='10%' nowrap align='left'>$brandname</td>
		  <td width='10%' nowrap><div align='right'>$stock_qty $munit</div></td>
		  <td width='10%' nowrap><div align='right'>$qty $munit</div></td>
		  <td width='8%' nowrap align='right'>$unit_price $currencyName</td>	  
		  <td width='10%' nowrap align='right'>$total</td>				  				  
		  <td width='7%' nowrap align='center'><a href=\"?app=physical.stock.verification&cmd=deltemp&id=$tmp_id\"><img src=\"images/common/icons/delete.gif\"></a></td>
		</tr>"; $sl++;
		}
		$str3="
		<tr style='color:#000;' bgcolor='#CCCCCC' height=25>
		  <td colspan='5' nowrap><div align='right'>Total </div></td>
		  <td nowrap align='right'>$TotalQty $munit</td>
		  <td nowrap>&nbsp;</td>
		  <td nowrap align='right'>$total_value $currencyName</td>
		  <td nowrap align='center'>&nbsp;</td>
		</tr>
		</table>";
		$total_salesStr = $str1.$str2.$str3."####-@@@@".$total_value."####-@@@@".$product_discount;
		return $total_salesStr;
	}
	
  //====== End Save Sales =====
  function updateStockQty($voucher_no,$store_id,$product,$variation,$verification_date){
        $project_id = getFromSession('project_id');	
	require_once(CLASS_DIR.'/common.list.class.php');	
	$comlistApp 	= new CommonList();
	
	$Pcsql="SELECT product_type,catagory,brand_code,m_unit,unit_price FROM ".PRODUCT_TBL." WHERE BINARY product_id='$product' AND project_id='$project_id'";
	$Pcrow = mysql_fetch_object(mysql_query($Pcsql));
	$m_unit 		= $Pcrow->m_unit;   $productType 	= $Pcrow->product_type;
	$catagory 		= $Pcrow->catagory;	$brand_id 		= $Pcrow->brand_code;  $unit_price 	= $Pcrow->unit_price;
		
	if($variation>=0){
		//===== Dr Stock Qty ======
		$totalFCR  = $this->getTotalCreditStock($product,getFromSession('project_id'));
		$totalFDR  = $this->getTotalDebitStock($product,getFromSession('project_id'));					 
		$TTbalance = (($totalFDR+$variation) - $totalFCR);	
		$details   = "Stock Dr by PV";
		$tType 	   = "PV";
		$this->saveStockJournal($voucher_no,$tType,$project_id,$store_id,$product,$productType,$details,$unit_price,$m_unit,$variation,0,$TTbalance,$verification_date);
		//==== update in PD ====
		$this->saveInPurchaseDetails($voucher_no,$catagory,$brand_id,$product,$m_unit,$variation,$unit_price);
	}elseif($variation<0){
		$varify_qty = abs($variation);
		//===== Cr Stock Qty ======
		$totalFCR  = $this->getTotalCreditStock($product,getFromSession('project_id'));
		$totalFDR  = $this->getTotalDebitStock($product,getFromSession('project_id'));					 
		$SBalance  = ($totalFDR - ($totalFCR+$varify_qty));
		$details   = "Stock out by PV";	
		$tType 	   = "PV";
		$this->saveStockJournal($voucher_no,$tType,$project_id,$store_id,$product,$productType,$details,$unit_price,$m_unit,0,$varify_qty,$SBalance,$verification_date);
		//==== update in PD ====
		$this->outFromPurchaseDetails($catagory,$brand_id,$product,$m_unit,$varify_qty,$unit_price);
	}
  }
  
    
  function insertVerifyDetails($voucher_no){
	$requestdata 				= array();
	$arr_catagory_product_id	= array();	
	$project_id  				= getFromSession('project_id');
	$currency        			= getRequest('currency');
	$getSql	= "SELECT * FROM ".TEMP_STOCK_VERIFY_TBL." WHERE created_by = '".getFromSession('userid')."' AND project_id='".getFromSession('project_id')."'";
	$gres 	= mysql_query($getSql);
	if(mysql_num_rows($gres)>0){
		while($row = mysql_fetch_object($gres)){
		$requestdata['verify_no']	= $voucher_no;
		$requestdata['delivery_point']	= getRequest('delivery_point');  
		$requestdata['project_id']	= $project_id;       	  
		$requestdata['catagory'] 	= $row->catagory;       	  
		$requestdata['brand_id'] 	= $row->brand_id; 
		$requestdata['product'] 	= $row->productid; $product_id	= $row->productid; 
		$requestdata['m_unit'] 		= $row->munit;       	  
		//$requestdata['details'] 	= $row->details;    	  
		$requestdata['unit_price'] 	= $row->unit_price;       	  
		$requestdata['qty'] 		= $row->qty;
		$requestdata['stock_qty'] 	= $row->stock_qty;
		$requestdata['variation'] 	= $row->variation;   
		$requestdata['total'] 		= $row->total;			
		$requestdata['verification_date']	= formatDate(getRequest('verification_date'));	  	
		$requestdata['created_by'] 	= getFromSession('userid');
		$requestdata['created_date']= date('Y-m-d h:i:s');			
		
		$info        	=  array();
		$info['table']	= STOCK_VERIFY_DETAILS_TBL;
		$info['data'] 	= $requestdata;
		//$info['debug']  	=  true;   
		$res = insert($info);
		if($res){
			$this->updateUnitPrice($product_id,$row->unit_price);
			$this->updateStockQty($voucher_no,$requestdata['delivery_point'],$product_id,$row->variation,$requestdata['verification_date']);
			if($row->qty >0){
				$this->saveAVGPurchasePrice($voucher_no,$product_id,$row->unit_price);
			}
		}
		}// end while 
    }// end if
	
	if($res){ 
	 $dsql = "DELETE FROM ".TEMP_STOCK_VERIFY_TBL." WHERE created_by = '".getFromSession('userid')."' AND project_id='".getFromSession('project_id')."'";
	 mysql_query($dsql);
	}
  } //End of the function insertSalesDetails()
  function updateUnitPrice($product_id,$unit_price){	
	$fgsql="UPDATE ".PRODUCT_TBL." SET  unit_price='$unit_price' WHERE product_id='$product_id'";
	mysql_query($fgsql);	
  }
  function saveAVGPurchasePrice($voucher_no,$product_id,$purchase_price){
		$project_id = getFromSession('project_id');
		if($purchase_price >0){
			$dsql = "DELETE FROM ".AVG_PURCHASE_PRICE_TBL." WHERE project_id = '".$project_id."' AND product_id='".$product_id."'";
			mysql_query($dsql);

			$sql = "INSERT INTO ".AVG_PURCHASE_PRICE_TBL."(voucher_no,project_id,product_id,purchase_price) 
			VALUES('".$voucher_no."','".$project_id."','".$product_id."','".$purchase_price."')"; 
			$ires = mysql_query($sql); 
			if($ires){			
				$USQL 	= "UPDATE ".PRODUCT_TBL." SET purchase_unit_price = $purchase_price WHERE product_id = '$product_id' AND project_id = '$project_id'";
				mysql_query($USQL);
			}		
		}
   }
  function saveInPurchaseDetails($voucher_no,$catagory,$brand_id,$product,$m_unit,$qty,$unit_price){
		$project_id     = getFromSession('project_id');    
	    $created_by     = getFromSession('userid');	
		$total 			= ($qty*$unit_price);	
		$sqlD="INSERT INTO ".PURCHASE_DETAILS_TBL."(voucher_no,project_id,catagory,brand_id,product,m_unit,unit_price,qty,rec_qty,total,created_by) 
		VALUES('$voucher_no','$project_id','$catagory','$brand_id','$product','$m_unit','$unit_price','$qty','$qty','$total','$created_by')";
		$res2=mysql_query($sqlD);
		if($res2){ return true;	}else{ return false;} 
  }
  function outFromPurchaseDetails($catagory,$brand_id,$product,$m_unit,$out_qty,$unit_price){
	$project_id     = getFromSession('project_id');		
	$PuSql="SELECT pur_detail_id,voucher_no,sales_qty,stock_qty FROM ".PURCHASE_ITEM_VIEW." WHERE BINARY product='$product' 
	AND brand_id='$brand_id' AND project_id='$project_id' AND stock_qty >0 ORDER BY pur_detail_id ASC"; 
	$pres = mysql_query($PuSql);
	if(mysql_num_rows($pres)>0){
		while($Purow = mysql_fetch_object($pres)){		
			$pur_detail_id  = $Purow->pur_detail_id;
			if(($Purow->stock_qty>=$out_qty) && ($out_qty>0)){		
				$sales_qty 	= ($Purow->sales_qty+$out_qty);	
				$out_qty 	= 0;	
				$pdusql="UPDATE ".PURCHASE_DETAILS_TBL." SET sales_qty='".$sales_qty."' WHERE BINARY pur_detail_id='$pur_detail_id'";
				$pures = mysql_query($pdusql);
				break;
			}elseif(($Purow->stock_qty<$out_qty) && ($out_qty>0)){
				$sales_qty 	= ($Purow->sales_qty+$Purow->stock_qty);			
				$out_qty 	= $out_qty - $Purow->stock_qty;		
				$pdusql="UPDATE ".PURCHASE_DETAILS_TBL." SET sales_qty='".$sales_qty."' WHERE BINARY pur_detail_id='$pur_detail_id'";
				$pures = mysql_query($pdusql);
			}
		}// end while
	}
  }
  
  function insertVerifyMaster(){
  	  require_once(CLASS_DIR.'/common.list.class.php');	
	  $comlistApp 				= new CommonList();
	
	  if (getRequest('total_amount') <= 0) {
             return 0;
          }

	  $project_id  = getFromSession('project_id');    
	  $requestdata = array();	
	  $requestdata = getUserDataSet(STOCK_VERIFY_MASTER_TBL); 
	  $requestdata['verification_date']	= formatDate(getRequest('verification_date'));	  
	  
	  $requestdata['total_amount']  = getRequest('total_amount');
	  $requestdata['project_id']    = getFromSession('project_id');    
	  $requestdata['created_by']    = getFromSession('userid');	
	  $requestdata['created_date']  = date('Y-m-d h:i:s');
	  $verify_no = $this->createVoucharID();
	  $requestdata['verify_no']  	= $verify_no;	  
	  $info        		=  array();
	  $info['table']	= STOCK_VERIFY_MASTER_TBL;
	  $info['data'] 	= $requestdata;     
	  //$info['debug']  =  true;
	  $res = insert($info);
	  if($res){
	  	$getSql	= "SELECT * FROM ".TEMP_STOCK_VERIFY_TBL." WHERE created_by = '".getFromSession('userid')."' AND project_id='".getFromSession('project_id')."'";
		$gres 	= mysql_query($getSql);
		if(mysql_num_rows($gres)>0){
			$inAmount=0; $outAmount=0;
			while($vrow = mysql_fetch_object($gres)){		
				if($vrow->variation>0){ 
					$inAmount+=($vrow->unit_price*$vrow->variation);
				}elseif($vrow->variation<0){ 
					$varify_qty = abs($vrow->variation);
					$outAmount+=($vrow->unit_price*$varify_qty);
				}			
			}// end while

			$verification_date = formatDate(getRequest('verification_date'));

			if($inAmount>0){
			$created_by = getFromSession('userid');	
			$created_date = date('Y-m-d h:i:s');
	  		$sqlM="INSERT INTO ".PURCHASE_MASTER_TBL."(voucher_no,project_id,purchase_date,purchase_type,total_value,net_payble,paid_amount,due,item_received_amount,
			created_by,created_date) VALUES('$verify_no','$project_id','$purchase_date','Verification','$inAmount','$inAmount','$inAmount','0',
			'$inAmount','$created_by','$created_date')";
			mysql_query($sqlM);
			//======= Dr Stock Amount ======
			$StockAmount   = $inAmount;
			$StockId       = $comlistApp->getFGStockId(getFromSession('project_id'));
			if(getRequest('inventory_id') != ""){
			     $StockId = getRequest('inventory_id');
			}
			$totalStockCr  = $this->getTotalCreditAmount($StockId,getFromSession('project_id'));
			$totalStockDr  = $this->getTotalDebitAmount($StockId,getFromSession('project_id'));					 
			$StockBalance  = (($totalStockDr+$StockAmount)-$totalStockCr);	$description = "In by PV";					 
			$comlistApp->saveAccJournal($verify_no,$StockId,"Stock","PVI",getFromSession('project_id'),$description,$StockAmount,0,$StockBalance,0,$verification_date);
			}
			
			if($outAmount>0){
			//======= Cr Stock Amount ======
			$StockAmount = $outAmount;
			$StockId 	 = $comlistApp->getFGStockId(getFromSession('project_id'));
			if(getRequest('inventory_id') != ""){
			     $StockId = getRequest('inventory_id');
			}
			$totalStockCr  = $this->getTotalCreditAmount($StockId,getFromSession('project_id'));
			$totalStockDr  = $this->getTotalDebitAmount($StockId,getFromSession('project_id'));					 
			$StockBalance  = ($totalStockDr-($totalStockCr+$StockAmount));	$description = "Out by PV";						 
			$comlistApp->saveAccJournal($verify_no,$StockId,"Stock","PVO",getFromSession('project_id'),$description,0,$StockAmount,$StockBalance,0,$verification_date);
			}
		} // end num rows
	  	return $verify_no;
	  }else{
	  	return 0;
	  }
  }
	
  function saveSalesItem(){
	mysql_query("START TRANSACTION;");
	mysql_query("SET autocommit=0;");

	$verify_no = $this->insertVerifyMaster();
	$this->insertVerifyDetails($verify_no);
	mysql_query("COMMIT;");
	if($verify_no!=""){
	header("location:index.php?app=physical.stock.verification&cmd=print_verification&verify_no=".$verify_no);	
	}else{
	header("location:index.php?app=physical.stock.verification&cmd=verify");
	}
  }

  function stockBulkImport()
    {
        if (ob_get_level()) ob_end_clean();
        ob_start(); // Start buffering

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

        $validation = true;
        foreach ($input['rows'] as $index => $row) {
            $verification_id = trim($row['verification_id']);
            if ($verification_id == "") {
                $validation = false;
                $response['failed'][] = [
                    'index' => $index,
                    'error' => "verification ID must be fillable"
                ];
            }
        }

        if ($validation) {
            $masterFields = [
                'depo',
                'date',
                'inventory_id'
            ];

            $finalData = [];

            foreach ($input['rows'] as $index => $row) {
                $verification_id = trim($row['verification_id']);

                // Initialize array if voucher_id is not yet added
                if (!isset($finalData[$verification_id])) {
                    $finalData[$verification_id] = [
                        'master' => [
                            "total_amount" => 0
                        ],
                        'details' => []
                    ];
                }

                // Collect master data only once (from first matching row)
                foreach ($masterFields as $field) {
                    $value = trim($row[$field]);
                    if ($value !== '') {
                        $finalData[$verification_id]['master'][$field] = $value;
                    }
                }

                // Build detail row (fields not in masterFields and not voucher_id)
                $detailRow = [];
                foreach ($row as $key => $value) {
                    $detailRow["index"] = $index;

                    $value = trim($value);
                    if ($value === '') {
                        continue;
                    }

                    if (!in_array($key, $masterFields) && $key !== 'verification_id') {
                        $detailRow[$key] = $value;
                    }

		    if ($key == "amount" && $value != "") {
                        $finalData[$verification_id]['master']['total_amount'] += (float)$value;
                    }

                }

                // Add only if there's any detail data
                if (!empty($detailRow)) {
                    $finalData[$verification_id]['details'][] = $detailRow;
                }
            }

            // Convert associative array to indexed array
            $finalArray = array_values($finalData);

            foreach ($finalArray as $row) {
                $master = $row['master'];
                $details = $row['details'];

                $masterFailedValidation = false;

                $depo = trim($master['depo']);
                $date = $this->formatCustomDate(trim($master['date']));
                $inventory_id = trim($master['inventory_id']);

                if ($depo == "" || $date == "" || $inventory_id == "") {
                    $masterFailedValidation = true;
                }

                $processNext = false;

                foreach ($details as $dRow) {
                    $index = (int)trim($dRow['index']);
                    if ($masterFailedValidation) {
                        $response['failed'][] = [
                            'index' => $index,
                            'error' => "Voucher common data must be fillable any of the row"
                        ];

                        continue;
                    }

                    $product_id = trim($dRow['product_id']);
                    $stock_qty = trim($dRow['stock_qty']);
                    $qty = trim($dRow['qty']);
                    $unit_price = trim($dRow['unit_price']);

                    $detailsFailedValidation = false;

                    if ($product_id == "" || $stock_qty == "" || $qty == "" || $unit_price == "") {
                        $detailsFailedValidation = true;
                    }

                    if ($detailsFailedValidation) {
                        $processNext = false;
                        $response['failed'][] = [
                            'index' => $index,
                            'error' => "Product ID/Verified Qty/Rate must be fillable"
                        ];
                        continue;
                    } else {
                        $processNext = true;
                    }
                }

                if ($processNext) {
                    $result = $this->saveBulkStockData($master, $details);

                    if (!$result['status']) {
                        $response['failed'][] = [
                            'index' => $index,
                            'error' => $result['error']
                        ];
                    } else {
                        $response['success']++;
                    }
                }
            }
        }

        // Output response
        header('Content-Type: application/json');
        echo json_encode($response);
        exit();
    }

    function saveBulkStockData($master = [], $details = [])
    {
        $response = [
            "status" => false,
            "error" => "Something wrong",
        ];

	require_once(CLASS_DIR . '/common.list.class.php');
        $comlistApp = new CommonList();

        $verify_no = $this->createVoucharID();
        $project_id = getFromSession('project_id');
        $inventory_id = trim($master['inventory_id']);
        $delivery_point = trim($master['depo']);
        $verification_date = $this->formatCustomDate(trim($master['date']));

        $requestMasterData = array();
        $requestMasterData = getUserDataSet(STOCK_VERIFY_MASTER_TBL);
        $requestMasterData['verification_date'] = $verification_date;

        $requestMasterData['total_amount'] = trim($master['total_amount']);
        $requestMasterData['project_id'] = $project_id;
        $requestMasterData['created_by'] = getFromSession('userid');
        $requestMasterData['created_date'] = date('Y-m-d h:i:s');
        $requestMasterData['verify_no'] = $verify_no;
        $info = array();
        $info['table'] = STOCK_VERIFY_MASTER_TBL;
        $info['data'] = $requestMasterData;
        //$info['debug']  =  true;
        $res = insert($info);

        if ($res) {
            $inAmount = 0;
            $outAmount = 0;
            foreach ($details as $vrow) {
                if ($vrow['variation'] > 0) {
                    $inAmount += ($vrow['unit_price'] * $vrow['variation']);
                } elseif ($vrow['variation'] < 0) {
                    $varify_qty = abs($vrow['variation']);
                    $outAmount += ($vrow['unit_price'] * $varify_qty);
                }
            }// end while
            $purchase_date = "0000-00-00";
            if ($inAmount > 0) {
                $created_by = getFromSession('userid');
                $created_date = date('Y-m-d h:i:s');
                $sqlM = "INSERT INTO " . PURCHASE_MASTER_TBL . "(voucher_no,project_id,purchase_date,purchase_type,total_value,net_payble,paid_amount,due,item_received_amount,
        created_by,created_date) VALUES('$verify_no','$project_id','$purchase_date','Verification','$inAmount','$inAmount','$inAmount','0',
        '$inAmount','$created_by','$created_date')";
                mysql_query($sqlM);
                //======= Dr Stock Amount ======
                $StockAmount = $inAmount;
                $StockId = $comlistApp->getFGStockId($project_id);
                if ($inventory_id != "") {
                    $StockId = $inventory_id;
                }
                $totalStockCr = $this->getTotalCreditAmount($StockId, $project_id);
                $totalStockDr = $this->getTotalDebitAmount($StockId, $project_id);
                $StockBalance = (($totalStockDr + $StockAmount) - $totalStockCr);
                $description = "In by PV";
                $comlistApp->saveAccJournal($verify_no, $StockId, "Stock", "PVI", $project_id, $description, $StockAmount, 0, $StockBalance, 0, $verification_date);
            }

            if ($outAmount > 0) {
                //======= Cr Stock Amount ======
                $StockAmount = $outAmount;
                $StockId = $comlistApp->getFGStockId($project_id);
                if ($inventory_id != "") {
                    $StockId = $inventory_id;
                }
                $totalStockCr = $this->getTotalCreditAmount($StockId, $project_id);
                $totalStockDr = $this->getTotalDebitAmount($StockId, $project_id);
                $StockBalance = ($totalStockDr - ($totalStockCr + $StockAmount));
                $description = "Out by PV";
                $comlistApp->saveAccJournal($verify_no, $StockId, "Stock", "PVO", $project_id, $description, 0, $StockAmount, $StockBalance, 0, $verification_date);
            }


            $requestdata = array();
            foreach ($details as $row) {
                $requestdata['verify_no'] = $verify_no;
                $requestdata['delivery_point'] = $delivery_point;
                $requestdata['project_id'] = $project_id;
                $requestdata['catagory'] = $row['catagory'];
                $requestdata['brand_id'] = $row['brand_id'];
                $requestdata['product'] = $row['product_id'];
                $product_id = $row['product_id'];
                $requestdata['m_unit'] = $row['munit'];
                $requestdata['unit_price'] = $row['unit_price'];
                $requestdata['qty'] = $row['qty'];
                $requestdata['stock_qty'] = $row['stock_qty'];
                $requestdata['variation'] = $row['variation'];
                $requestdata['total'] = $row['amount'];
                $requestdata['verification_date'] = $verification_date;
                $requestdata['created_by'] = getFromSession('userid');
                $requestdata['created_date'] = date('Y-m-d h:i:s');

                $info = array();
                $info['table'] = STOCK_VERIFY_DETAILS_TBL;
                $info['data'] = $requestdata;
                //$info['debug']  	=  true;
                $res = insert($info);
                if ($res) {
                    $this->updateUnitPrice($product_id, $row['unit_price']);
                    $this->updateStockQty($verify_no, $requestdata['delivery_point'], $product_id, $row['variation'], $requestdata['verification_date']);
                    if ($row['qty'] > 0) {
                        $this->saveAVGPurchasePrice($verify_no, $product_id, $row['unit_price']);
                    }
                }
            }

		mysql_query("COMMIT;");
		$response = [
		    "status" => true,
		    "error" => "",
		];
        }

  	return $response;
  }


  function formatCustomDate($dt)
    {
        if (trim($dt)) {
            $munite = "";    //echo $dt; 01-02-2007 09:08:00 PM
            $day = substr($dt, 0, 2);
            $month = substr($dt, 3, 2);
            $year = substr($dt, 6, 4);
            $hour = substr($dt, 11, 2);
            $minute = substr($dt, 14, 2);
            $second = substr($dt, 17, 2);
            $ampm = substr($dt, 20, 2);
            //echo $ampm;
            if ($hour == '' and $munite == '' and $second == '') {
                return $year . "-" . $month . "-" . $day;
            } else {
                if (strtoupper($ampm) == 'PM') {
                    $hour = intval($hour) + 12;
                    return $year . "-" . $month . "-" . $day . ' ' . $hour . ':' . $minute . ':' . $second;
                } else {
                    return $year . "-" . $month . "-" . $day . ' ' . $hour . ':' . $minute . ':' . $second;
                }

            }

        }
    }

  function getProductInfo()
    {
        if (ob_get_level()) ob_end_clean();
        ob_start(); // Start buffering

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
            'status' => true,
            'data' => []
        ];

        $masterFields = [
            'depo',
        ];

        $finalData = [];

        foreach ($input['rows'] as $index => $row) {
            $stock_id = trim($row['stock_id']);

            // Initialize array if voucher_id is not yet added
            if (!isset($finalData[$stock_id])) {
                $finalData[$stock_id] = [
                    'master' => [],
                    'details' => []
                ];
            }

            // Collect master data only once (from first matching row)
            foreach ($masterFields as $field) {
                $value = trim($row[$field]);
                if ($value !== '') {
                    $finalData[$stock_id]['master'][$field] = $value;
                }
            }

            // Build detail row (fields not in masterFields and not voucher_id)
            $detailRow = [];
            foreach ($row as $key => $value) {
                $value = trim($value);
                if ($value === '') {
                    continue;
                }

                if (!in_array($key, $masterFields) && $key !== 'stock_id') {
                    $detailRow[$key] = $value;
                }
            }

            // Add only if there's any detail data
            if (!empty($detailRow)) {
                $finalData[$stock_id]['details'][] = $detailRow;
            }
        }

        // Convert associative array to indexed array
        $finalArray = array_values($finalData);

        foreach ($finalArray as $row) {
            $master = $row['master'];
            $details = $row['details'];
            $depo = trim($master['depo']);

            foreach ($details as $dRow) {
                $index = trim($dRow['index']);
                $product_id = trim($dRow['product_id']);
                $product_code = trim($dRow['product_code']);
		$verified_qty = trim($dRow['verified_qty']);

                if ($depo == "" || ($product_id == "" && $product_code == "")) {
                    continue;
                }

                $conditions = array();
                if (!empty($product_id)) {
                    $conditions[] = "BINARY p.product_id = '$product_id'";
                }
                if (!empty($product_code)) {
                    $conditions[] = "BINARY p.product_code = '$product_code'";
                }
                $whereExtra = '';
                if (!empty($conditions)) {
                    $whereExtra = " AND (" . implode(" OR ", $conditions) . ")";
                }

                $sql = "SELECT p.product_id,p.product_name,p.product_code,p.catagory,c.catagory_name,p.brand_code,b.brand_name,p.m_unit FROM " . PRODUCT_TBL . " as p," . CATAGORY_TBL . " as c," . BRAND_TBL . " as b 
		WHERE p.catagory=c.catagory_code AND p.brand_code=b.brand_id $whereExtra";
                $prow = mysql_fetch_object(mysql_query($sql));
                if ($prow->product_name) {
                    $product_name = $prow->product_name;
		    if(empty($product_id)){
		         $product_id = $prow->product_id;
		    }
                    $product_name = str_replace('"', "&ldquo;", $product_name);
                    $product_name = str_replace("'", "&#8217;", $product_name);

		    $productStockInfo = $this->getProductStock($product_id, $depo);

		    $productData['product_id'] = $prow->product_id;
		    $productData['product_code'] = $prow->product_code;
		    $productData['product_name'] = $product_name;
                    $productData['catagory'] = $prow->catagory;
                    $productData['catagoryname'] = $prow->catagory_name;
                    $productData['brand_id'] = $prow->brand_code;
                    $productData['brandname'] = $prow->brand_name;
                    $productData['munit'] = $prow->m_unit;
                    $productData['stock_qty'] = (float)$productStockInfo['stock'];
                    $productData['qty'] = (float)$verified_qty;
                    $productData['variation'] = ($productData['qty'] - $productData['stock_qty']);
                    $productData['unit_price'] = (float)$productStockInfo['unit_price'];
                    $productData['total'] = $productData['qty'] * $productData['unit_price'];

                    $response['data'][$product_id] = $productData;
                }
            }
        }

        // Output response
        header('Content-Type: application/json');
        echo json_encode($response);
        exit();
    }

  function getProductStock($product_id, $transfer_stock)
    {
        $project_id = getFromSession('project_id');

        $Ssql = "SELECT balance FROM " . STORE_STOCK_VIEW . " WHERE product_id = '" . $product_id . "' AND store_id = '$transfer_stock' AND project_id = '$project_id'";
        $Srow = mysql_fetch_object(mysql_query($Ssql));

        $Prosql = "SELECT unit_price,m_unit FROM " . PRODUCT_TBL . " WHERE product_id = '$product_id' AND project_id = '$project_id'";
        $Prorow = mysql_fetch_object(mysql_query($Prosql));

        $Prosql = "SELECT purchase_price  FROM " . AVG_PURCHASE_PRICE_TBL . " WHERE product_id = '$product_id' AND project_id = '$project_id' ORDER BY `id` DESC LIMIT 0 , 2";
        $pres = mysql_query($Prosql);
        $ttl_product = mysql_num_rows($pres);
        if ($ttl_product > 0) {
            while ($prow = mysql_fetch_object($pres)) {
                $avg_purchase_price += $prow->purchase_price;
            }
            $avg_purchase_price = ($avg_purchase_price / $ttl_product);
        }
        //=== No need avg_purchase_price ====
        $avg_purchase_price = 0;
        if (intval($avg_purchase_price) == "") {
            $avg_purchase_price = 0;
        }

        if ($avg_purchase_price == 0) {
            $avg_purchase_price = $Prorow->unit_price;
        }

        return [
            "stock" => $Srow->balance,
            "unit_price" => $avg_purchase_price,
        ];
    }
   
  function loadProductStock($product_id){
	  $project_id = getFromSession('project_id');  	
	  $transfer_stock = trim(getRequest('transfer_stock'));  		 
	  
	  $Ssql = "SELECT balance FROM ".STORE_STOCK_VIEW." WHERE product_id = '".$product_id."' AND store_id = '$transfer_stock' AND project_id = '$project_id'";
	  $Srow = mysql_fetch_object(mysql_query($Ssql));
	  
	  $Prosql = "SELECT unit_price,m_unit FROM ".PRODUCT_TBL." WHERE product_id = '$product_id' AND project_id = '$project_id'";
	  $Prorow = mysql_fetch_object(mysql_query($Prosql));
	  
	  $Prosql 		= "SELECT purchase_price  FROM ".AVG_PURCHASE_PRICE_TBL." WHERE product_id = '$product_id' AND project_id = '$project_id' ORDER BY `id` DESC LIMIT 0 , 2";
	  $pres 		= mysql_query($Prosql);
	  $ttl_product 	= mysql_num_rows($pres);
	  if($ttl_product >0){
		while($prow = mysql_fetch_object($pres)){
			$avg_purchase_price += $prow->purchase_price;
		}		
		$avg_purchase_price = ($avg_purchase_price / $ttl_product);
	  }
	  //=== No need avg_purchase_price ====
	  $avg_purchase_price =0;
	  if(intval($avg_purchase_price)==""){ $avg_purchase_price=0;}			
	
	  if($avg_purchase_price ==0){
		$avg_purchase_price = $Prorow->unit_price;
	  }
	  
	  echo $Srow->balance."#####".$avg_purchase_price."#####".$Prorow->m_unit;	
   }
   
function getVerifyMasterInfo($id){		
	$project_id     = getFromSession('project_id');  
	$info           = array();    
	$info['table']  = STOCK_VERIFY_MASTER_TBL.' tm,'.DELIVERY_POINT_TBL.' d,'.PROJECT_TBL.' p,'.CURRENCY_TBL.' c';	
	$info['fields'] = array('tm.verify_no','tm.delivery_point','d.delivery_point_name','d.details','p.project_name','p.location','tm.total_amount',"DATE_FORMAT(tm.verification_date,'%d %b %y' ) as verification_date",'c.curr_symble','tm.created_by','tm.created_date');

	$sql="tm.delivery_point = d.delivery_pid AND tm.project_id = p.project_id AND tm.currency = c.currency_id AND tm.project_id = '".$project_id."' 
	AND tm.verify_no = '$id'";
						
	$info['where']  =$sql;	  	
    $info['groupby'] = array("tm.verify_no");
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
		$info['table']  =  STOCK_VERIFY_DETAILS_TBL.' sd,'.PRODUCT_TBL.' p,'.CURRENCY_TBL.' c,'.BRAND_TBL.' b';	
		$info['fields'] = array('sd.details_id','sd.verify_no','sd.project_id','sd.delivery_point','sd.catagory','b.brand_name','sd.product',
		'p.product_name','p.product_desc','p.m_unit','sd.unit_price','c.curr_symble','sd.qty','sd.stock_qty','sd.variation','sd.total');
		
		$sql="sd.product = p.product_id AND p.brand_code = b.brand_id AND sd.currency = c.currency_id AND sd.verify_no = '$id'";
		
		$info['where']  = $sql;
	    $info['groupby'] = array("sd.details_id");
		$info['orderby'] = array("sd.product asc");
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
    
    function getTotalCreditAmount($acc_head,$project_id){
   		$sql = "SELECT sum(`cr`) as credit_amount FROM ".ACCOUNT_JOURNAL_TBL." WHERE BINARY sub_id = '$acc_head' AND project_id = '$project_id'";
		$row = mysql_fetch_object(mysql_query($sql));
		$credit_amount = $row->credit_amount;
		if(empty($credit_amount)){
			$credit_amount = 0;
		}
		return $credit_amount;
   }
  
   function getTotalDebitAmount($acc_head,$project_id){
   		$sql = "SELECT sum(`dr`) as debit_amount FROM ".ACCOUNT_JOURNAL_TBL." WHERE BINARY sub_id = '$acc_head' AND project_id = '$project_id'";
		$row = mysql_fetch_object(mysql_query($sql));
		$debit_amount = $row->debit_amount;
		if(empty($debit_amount)){
			$debit_amount = 0;
		}
		return $debit_amount;
   } 
   function getTotalCreditStock($acc_head,$project_id){
   		$sql = "SELECT sum(`cr`) as credit_amount FROM ".STOCK_LEDGER_TBL." WHERE BINARY product_id = '$acc_head' AND project_id = '$project_id'";
		$row = mysql_fetch_object(mysql_query($sql));
		$credit_amount = $row->credit_amount;
		if(empty($credit_amount)){
			$credit_amount = 0;
		}
		return $credit_amount;
   }
  
   function getTotalDebitStock($acc_head,$project_id){
   		$sql = "SELECT sum(`dr`) as debit_amount FROM ".STOCK_LEDGER_TBL." WHERE BINARY product_id = '$acc_head' AND project_id = '$project_id'";
		$row = mysql_fetch_object(mysql_query($sql));
		$debit_amount = $row->debit_amount;
		if(empty($debit_amount)){
			$debit_amount = 0;
		}
		return $debit_amount;

   }
  function saveStockJournal($voucher_no,$pvoucher_no,$project_id,$store_id,$product_id,$product_type,$note,$unit_price=NULL,$m_unit,$DR=NULL,$CR=NULL,$balance,$create_date=NULL){
	$created_by = getFromSession('userid');
	$sql = "INSERT INTO ".STOCK_LEDGER_TBL." (voucher_no,po_no,project_id,store_id,product_id,product_type,note,unit_price,m_unit,dr,cr,balance,created_by,create_date) 
	VALUES('".$voucher_no."','".$pvoucher_no."','".$project_id."','".$store_id."','".$product_id."','".$product_type."','".$note."','".$unit_price."','".$m_unit."','".$DR."','".$CR."','".$balance."','".$created_by."','".$create_date."')";
	mysql_query($sql); 
  } 
  
  function createVoucharID()
   {
      $info = array();
      $info['table']  = STOCK_VERIFY_MASTER_TBL; 
      $info['fields'] = array('max(verify_no) as maxvoucher');
      $res = select($info);
      $maxvoucherId = 'V00000000';
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
      
      $maxvoucherId = generateID("V",$maxvoucherId,9);
      return $maxvoucherId;
   }  
     
} // End class


?>
