<?php
class BatchProduction
{
   
   function run()
   {         
      $cmd 		= getRequest('cmd');
      $u_t_id 		= getFromSession('u_type_id');
        
      if( ($u_t_id ==101) || ($u_t_id == 102) || ($u_t_id == 103) || ($u_t_id == 105)) // 101 = sysadmin, 102 = admin, 103= salesman, 105=store
      {

      	switch ($cmd)
      	{
		case 'add'		: $this->showEditor(); break; 
		case 'save_po'  	: $this->SaveBatchProduction(); break;   
		case 'delete'		: $this->deleteProduction(); break;
		case 'print_production'	: $this->showPrintEditor($msg); break;
		case 'loadProductBatch' : $this->loadProductBatch(trim(getRequest('product_id'))); break;
		case 'get_dtl'       	: $this->loadBatchInfo();break; 
		case 'getdaycapacity'	: $this->loadMachineInfo();break; 
		case 'get_po_dtl'	: $this->getAjaxBatchProductionDtl();break;
		case 'show_list'	: $this->showListEditor(); break;  
		case 'load_stock'       : $this->loadProductStock(trim(getRequest('product_id')));break; 
		case 'loadstockqty'  	: $this->loadProductStockQty(trim(getRequest('product_id'))); break;
		default                 : $cmd = 'list'; $screen = $this->showEditor();   break; 
      	}
      }elseif($u_t_id == 104) // 104 = acc
      {
      	switch ($cmd)
      	{
		case 'print_batch'   : $screen = $this->showPrintEditor($msg); break;
		case 'show_list'     : $this->showListEditor(); break;  
		default              : $cmd = 'list'; $screen = $this->showEditor();   break;
      	}

      }elseif($u_t_id == 107) // 104 = acc
      {
      	switch ($cmd)
      	{
	case 'add'		: $this->showEditor(); break; 
	case 'save_po'  	: $this->SaveBatchProduction(); break;   
	case 'delete'		: $this->deleteProduction(); break;
	case 'print_production'	: $this->showPrintEditor($msg); break;
	case 'loadProductBatch' : $this->loadProductBatch(trim(getRequest('product_id'))); break;
	case 'get_dtl'       	: $this->loadBatchInfo();break; 
	case 'getdaycapacity'	: $this->loadMachineInfo();break; 
	case 'get_po_dtl'	: $this->getAjaxBatchProductionDtl();break;
	case 'show_list'	: $this->showListEditor(); break;  
	case 'load_stock'       : $this->loadProductStock(trim(getRequest('product_id')));break; 
	case 'loadstockqty'  	: $this->loadProductStockQty(trim(getRequest('product_id'))); break;
	default                 : $cmd = 'list'; $screen = $this->showEditor();   break;
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
   function loadProductBatch($product_id)
	{		  
	  	  $project_id = getFromSession('project_id');  
		  $info            = array();
		  $info['table']   = PO_BATCH_MASTER_TBL.' as bt,'.PRODUCT_TBL.' p';
		  $info['fields']  =  array('bt.batch_id','bt.batch_name','p.product_name');
		  $info['where']   = "bt.finish_goods = p.product_id AND bt.finish_goods='$product_id' AND p.project_id = '$project_id'";
		  $info['groupby'] = array("bt.batch_id");
		  //$info['debug']   = true;	
		  $result          = select($info);
		  $data            = array();	
		  if(count($result)){
			 foreach($result as $key=>$value){
				$data[$key][]        = $value;
			 }
		  }
		  foreach($data as $i=>$v){
			 $subject_idname .= $v[0]->batch_id.'#####'.$v[0]->batch_name.'@@@';
		  }
		  echo $subject_idname;	
  }
  function loadBatchInfo(){
	$batch_id 	= trim(getRequest('batch_id'));  	 
	$product_id = trim(getRequest('product')); 		 
	$shift 		= trim(getRequest('shift')); 	 
	$project_id = getFromSession('project_id');
	$total_qty  = 0;
	$getSql	= "SELECT total_day_wastage, total_day_qty,total_night_wastage,total_night_qty FROM ".PO_BATCH_MASTER_TBL." WHERE batch_id='".$batch_id."' 
	AND finish_goods='$product_id' AND project_id='$project_id' GROUP BY batch_id";
	$gres 	= mysql_query($getSql);
	$row = mysql_fetch_object($gres);
	if($shift =="Day"){
		$total_qty = ($row->total_day_qty - $row->total_day_wastage);
	}else{
		$total_qty = ($row->total_night_qty-$row->total_night_wastage);
	}	
	if($total_qty==""){ $total_qty=0;}
	echo $total_qty;
		
  }
  function loadMachineInfo(){
	$machine_id = trim(getRequest('machine_id')); 
	$project_id = getFromSession('project_id');
	$daily_capacity = 0;
	$getSql	= "SELECT daily_capacity FROM ".MACHINE_TBL." WHERE machine_id ='".$machine_id."' 
	AND project_id='$project_id'";
	$gres 	= mysql_query($getSql);
	$row = mysql_fetch_object($gres);
	$daily_capacity = $row->daily_capacity;	
	if($daily_capacity==""){ $daily_capacity=0;}
	echo $daily_capacity;
		
  }
  function SaveBatchProduction(){
	mysql_query("START TRANSACTION;");
	mysql_query("SET autocommit=0;");
	$project_id 	= getFromSession('project_id');
	$production_id 	= getRequest('production_id');
	$machine_no     = getRequest('machine_no');
	$version_no  	= getRequest('version_no');
	$used_date   	= formatDate(getRequest('used_date'));    

	if($production_id==""){
	$production_id = $this->insertProductionMaster();
	}

	$getSql	= "SELECT production_id FROM ".PRODUCTION_MASTER_TBL." WHERE `used_date` = '$used_date' AND project_id='".$project_id."' AND machine_no='$machine_no' AND version_no='$version_no'";
	$gres 	= mysql_query($getSql);
	if(mysql_num_rows($gres)>0){
	$row = mysql_fetch_object($gres);
	$production_id = $row->production_id;
	
	}else{
	$production_id = $this->insertProductionMaster();
	}  

	$detail_id = $this->saveFinishGoods($production_id);
	$this->insertProductionDetails($production_id,$detail_id);
	mysql_query("COMMIT;");		
	$this->getAjaxBatchProductionDtl();
	
  }
  function insertProductionMaster(){
	  $project_id  				= getFromSession('project_id');
	  $requestdata 				= array();	
	  $requestdata 				= getUserDataSet(PRODUCTION_MASTER_TBL);
	  $requestdata['project_id']        	= getFromSession('project_id');
	  $requestdata['factory_id']      	= getRequest('factory_id');	   	  
	  $requestdata['in_store_id']      	= getRequest('in_stock'); 
	  $requestdata['out_store_id']      	= getRequest('out_stock');
	  $requestdata['machine_no']      	= getRequest('machine_no');
	  $requestdata['version_no']  		= getRequest('version_no');
	  $requestdata['batch_no']  		= getRequest('batch_no');
	  $finish_product 			= getRequest('productid');
	  $finish_qty 				= getRequest('production_qty');
	  $Prosql 	= "SELECT catagory,brand_code,m_unit,unit_price FROM ".PRODUCT_TBL." WHERE product_id = '$finish_product' AND project_id = '$project_id'";
	  $Prorow 	= mysql_fetch_object(mysql_query($Prosql)); 
	  $requestdata['used_date'] 		= formatDate(getRequest('used_date')); 
	  $requestdata['finish_date'] 		= formatDate(getRequest('used_date'));
	  $requestdata['total_value'] 		= ($requestdata['unit_price']*$finish_qty);
	  $requestdata['production_amount'] 	= ($requestdata['unit_price']*$finish_qty);
	  $requestdata['finish_product']   	= $finish_product; 
	  $requestdata['unit_price'] 		= $Prorow->unit_price;		  
	  $requestdata['m_unit']		= $Prorow->m_unit;	 	  
	  $requestdata['target_qty']   		= getRequest('target_qty'); 
	  $requestdata['finish_qty']   		= getRequest('production_qty');
	  $requestdata['project_id']        	= getFromSession('project_id');    
	  $requestdata['created_by']        	= getFromSession('userid');
 	  $production_id 			= $this->createProductionID();
	  $requestdata['created_date']      	= date('Y-m-d h:i:s');
	  if($production_id !="")
	  {
	  	$requestdata['production_id']      = $production_id;
	  }
	  else
	  {
	  	$msg = "ID overflow !!!";
	  	header("location:index.php?app=user_home&msg=$msg");
	  	exit;
	  }
	  $info        	  = array();
	  $info['table']  = PRODUCTION_MASTER_TBL;
	  $info['data']   = $requestdata;     
	  //$info['debug'] =  true;
	  $res = insert($info);
	  if($res){
		return $production_id;
          }else{
	  	 header("location:?app=batch.production&cmd=add");
          }    
  }
  function insertProductionDetails($production_id,$detail_id)
  {
	require_once(CLASS_DIR.'/common.list.class.php');	
	$comlistApp 		= new CommonList();
	$store_id 		= getRequest('factory_id');
	$out_store_id 		= getRequest('out_stock');
	$batch_id 		= getRequest('batch_no');
	$finish_qty 		= getRequest('production_qty');
	$total_value		=0;
	$getSql	= "SELECT * FROM ".PO_BATCH_DETAILS_TBL." WHERE batch_id ='".$batch_id."' AND project_id='".getFromSession('project_id')."'";
	$gres 	= mysql_query($getSql);
	if(mysql_num_rows($gres)>0){
	while($row = mysql_fetch_object($gres)){	  
	  $requestdata 			    = array();
	  $requestdata['created_by'] 	    = getFromSession('userid');
	  $requestdata['created_time']      = date('Y-m-d h:i:s');  
	  $project_id			    = getFromSession('project_id');
	  $requestdata['production_id']     = $production_id; 
	  $requestdata['pvoucher_no']       = $detail_id; 
	  $requestdata['project_id']        = $project_id;
	  $requestdata['factory_id']        = getRequest('factory_id');
	  $requestdata['machine_no']        = getRequest('machine_no');
	  $requestdata['production_shift']  = getRequest('version_no');
	  $requestdata['batch_no']  	    = getRequest('batch_no');
	  $requestdata['out_store_id']      = $out_store_id;
	  $requestdata['used_date']  	    = formatDate(getRequest('used_date'));
	  $requestdata['catagory']          = $row->catagory_id;
	  $requestdata['product']           = $row->product_id;
	  $product_id       		    = $row->product_id;  
	  $used_qty 			    = ($row->day_qty);
	  $wastage  			    = getRequest('wastage');
	  $wastage_qty 			    = (($used_qty /100) * $wastage);
	  $total_used_qty 		    = ((($used_qty + $wastage_qty) * $finish_qty)/1000);			  
	  $requestdata['qty']    	    = $total_used_qty;			  
	  $requestdata['wastage_qty']       = (($wastage_qty * $finish_qty)/1000);
	  $actual_wastage_qty 		    = (($used_qty /100) * $row->day_wastage_persent);			  
	  $requestdata['actual_wastage_qty']= (($actual_wastage_qty * $finish_qty)/1000); 
	  $Prosql 	= "SELECT product_type,m_unit,unit_price FROM ".PRODUCT_TBL." WHERE product_id = '$product_id' AND project_id = '$project_id'";
	  $Prorow 	= mysql_fetch_object(mysql_query($Prosql));		  
	  $requestdata['m_unit']	    = $Prorow->m_unit;
	  $requestdata['amount'] 	    = ($Prorow->unit_price * $total_used_qty);	  
	  $total_value+=($Prorow->unit_price * $total_used_qty);	
	  $info        			    = array();
	  $info['table']		    = PRODUCTION_DETAILS_TBL;
	  $info['data'] 	= $requestdata;      
	  //$info['debug']  	=  true;
	  $res = insert($info);
	  if($res){
		$product_type 	= $Prorow->product_type;
		$used_date 	= formatDate(getRequest('used_date'));											
		$totalCR  = $this->getTotalCreditStock($product_id,getFromSession('project_id'));
		$totalDR  = $this->getTotalDebitStock($product_id,getFromSession('project_id'));					 
		$balance  = ($totalDR - ($totalCR+$total_used_qty));					
		$this->saveStockJournal($production_id,$detail_id,$project_id,$out_store_id,$product_id,$product_type,"Used for production",$requestdata['amount'],$requestdata['m_unit'],0,$total_used_qty,$balance,$used_date);
			
	  }//end res
	} // end while

	//=== Stock Cr of Raw Materials =====
	$StockAmount   = $total_value;
	$StockId       = $comlistApp->getWPStockId(getFromSession('project_id'));
	$totalStockCr  = $this->getTotalCreditAmount($StockId,getFromSession('project_id'));
	$totalStockDr  = $this->getTotalDebitAmount($StockId,getFromSession('project_id'));					 
	$StockBalance  = ($totalStockDr-($totalStockCr+$StockAmount));	$description = "Used for Production";				 
	$comlistApp->saveAccJournal($production_id,$StockId,"Stock","Used Raw Materials",getFromSession('project_id'),$description,0,$StockAmount,$StockBalance,0,$used_date,$detail_id);

	}// end num row	  
  }
  function saveFinishGoods($production_id){
	  $project_id  				= getFromSession('project_id');
	  $requestdata 				= array();	
	  $requestdata 				= getUserDataSet(PRODUCTION_FG_TBL); 
	  $requestdata['batch_no']      	= getRequest('batch_no'); 
	  $requestdata['project_id']        	= getFromSession('project_id');
	  $requestdata['factory_id']      	= getRequest('factory_id'); 
	  $requestdata['store_id']      	= getRequest('in_stock');
	  $requestdata['machine_no']      	= getRequest('machine_no');
	  $requestdata['version_no']  		= getRequest('version_no');
	  $requestdata['production_date'] 	= formatDate(getRequest('used_date'));
	  $finish_product 			= getRequest('productid');
	  $finish_qty 				= getRequest('production_qty');
	  $Prosql 	= "SELECT catagory,brand_code,m_unit,unit_price FROM ".PRODUCT_TBL." WHERE product_id = '$finish_product' AND project_id = '$project_id'";
	  $Prorow 	= mysql_fetch_object(mysql_query($Prosql));
	  $requestdata['finish_product']   	= $finish_product; 
	  $requestdata['catagory']   		= $Prorow->catagory; 
	  $requestdata['brand_code'] 		= $Prorow->brand_code; 		  
	  $requestdata['m_unit']		= $Prorow->m_unit;
	  $requestdata['unit_price'] 		= $Prorow->unit_price;
	  $requestdata['used_qty']  		= getRequest('used_qty');
	  $requestdata['wastage']  		= getRequest('wastage');
	  $requestdata['wastage_qty'] 		= getRequest('wastage_qty');
	  $requestdata['total_used_qty'] 	= getRequest('total_used_qty');
	  $requestdata['production_qty'] 	= getRequest('production_qty');
	  $requestdata['total_value'] 		= ($requestdata['unit_price']*$finish_qty);
	  $requestdata['production_status'] 	= getRequest('production_status');	      
	  $requestdata['created_by']        	= getFromSession('userid');
	  $requestdata['created_time']      	= date('Y-m-d h:i:s');
	  if($production_id != ""){
      	  $requestdata['production_id'] = $production_id;
	  }else{
	      	  $msg = "ID overflow !!!";
	      	  header("location:index.php?app=user_home&msg=$msg");
	      	  exit;
	  }
	  $info        		= array();
	  $info['table']	= PRODUCTION_FG_TBL;
	  $info['data'] 	= $requestdata;     
	  //$info['debug']  	= true;
	  $res = insert($info);
	  if($res){
		$detail_id =  $res['newid'];
		$this->saveFinishProduction($production_id,$detail_id);
		return $detail_id;
          }else{
		//mysql_query("ROLLBACK;");		 
		$msg = "Failed finish goods in !!! Please try again";
      	        header("location:index.php?app=batch.production&cmd=add&msg=$msg");
	  } 
		  
   } 
     
   function saveFinishProduction($production_id,$detail_id){
     	 require_once(CLASS_DIR.'/common.list.class.php');	
	 $comListApp 		= new CommonList();     
     	 $store_id 		= getRequest('in_stock');
   	 $project_id 		= getFromSession('project_id');
  	 $finish_product 	= getRequest('productid');
	 $finish_qty 		= getRequest('production_qty');
	 $Prosql 		= "SELECT catagory,brand_code,m_unit,unit_price FROM ".PRODUCT_TBL." WHERE product_id = '$finish_product' AND project_id = '$project_id'";
	 $Prorow 		= mysql_fetch_object(mysql_query($Prosql));
	 $catagory 		= $Prorow->catagory; $brand_id 	= $Prorow->brand_code; $m_unit 	= $Prorow->m_unit;
	 $unit_price 		= $Prorow->unit_price;
	
	 $total_value 		= ($unit_price*$finish_qty);
	 $production_amount 	= ($unit_price*$finish_qty);	  	  
	 $balanceQty		= $this->getStockBalanceQty($finish_product,$project_id,$store_id);	 
	 $balanceF  		= ($balanceQty+$finish_qty);	
	 $production_date 	= formatDate(getRequest('used_date'));	 
	 $net_payble=$total_value; $purchase_date = formatDate(getRequest('used_date'));
	  
	 $this->saveStockJournal($production_id,$detail_id,$project_id,$store_id,$finish_product,"Sales Item","Production",$unit_price,$m_unit,$finish_qty,0,$balanceF,$production_date);
	 //=== Stock Dr =====
	 $StockAmount	 	= $total_value;
	 $StockId 	 	= $comListApp->getFGStockId(getFromSession('project_id'));	  
	 $StockPvBalance	= $this->getTotalBalanceAmount($StockId,$project_id);			 
	 $StockBalance  	= ($StockPvBalance+$StockAmount);	
	 $description   	= "BP";	//FGP			 
	 $comListApp->saveAccJournal($production_id,$StockId,"Stock","Finish Goods",getFromSession('project_id'),$description,$StockAmount,0,$StockBalance,0,$production_date,$detail_id);
	 
   }
   function deleteProduction(){
	$updated_by 	= getFromSession('userid');
	$updated_time   = date('Y-m-d h:i:s'); 	
	$project_id 	= getFromSession('project_id');
	$production_id	= getRequest('production_id');
	$detail_id	= getRequest('id');
	$sql = "SELECT * FROM ".PRODUCTION_FG_TBL." WHERE detail_id=$detail_id AND production_id='".$production_id."' AND project_id='".$project_id."'";
	$res = mysql_query($sql);
	$num = mysql_num_rows($res);
	if($num >0){
	mysql_query("START TRANSACTION;");
	mysql_query("SET autocommit=0;");	 
	//===== Delete Raw Materials =====
	$DSQL1="DELETE FROM ".PRODUCTION_DETAILS_TBL." WHERE pvoucher_no='".$detail_id."' AND production_id='".$production_id."' AND project_id='".$project_id."'";
	$dres1 = mysql_query($DSQL1); 
	//===== Delete Stock Ledger =====
	$DSQL2="DELETE FROM ".STOCK_LEDGER_TBL." WHERE voucher_no='$production_id' AND po_no='$detail_id' AND project_id='".$project_id."'";
	$dres2 = mysql_query($DSQL2);
	//===== Delete Account Ledger =====
	$DSQL3="DELETE FROM ".ACCOUNT_JOURNAL_TBL." WHERE voucher_no='$production_id' AND delivery_id='$detail_id' AND project_id='".$project_id."'";
	$dres3 = mysql_query($DSQL3);	
        //===== Delete FG Production =====
	$DSQL4="DELETE FROM ".PRODUCTION_FG_TBL." WHERE detail_id=$detail_id AND production_id='".$production_id."' AND project_id='".$project_id."'";
	$dres4 = mysql_query($DSQL4);
	$sql2 = "SELECT * FROM ".PRODUCTION_FG_TBL." WHERE production_id='".$production_id."' AND project_id='".$project_id."'";
	$res2 = mysql_query($sql2);
	$num2 = mysql_num_rows($res2);
	if($num2==0){
		$DSQL5="DELETE FROM ".PRODUCTION_MASTER_TBL." WHERE production_id='".$production_id."' AND project_id='".$project_id."'";
	mysql_query($DSQL5);
	}
	if(($dres1) && ($dres2) && ($dres3) && ($dres4)) {	
		mysql_query("COMMIT;"); 
	}else{
		mysql_query("ROLLBACK;");
	}

	}
	$this->getAjaxBatchProductionDtl();	
   } 
   function getBatchProductionDtl(){
	$project_id  	= getFromSession('project_id');
	$machine_no     = getRequest('machine_no');
	$version_no  	= getRequest('version_no');
	if(getRequest('used_date')!=""){
		$used_date   = formatDate(getRequest('used_date'));
	}else{
		$used_date   = date('Y-m-d');
	}
	$BatchStr    =""; $str1=""; $str2=""; $str3="";
	$str1="<table width='100%' align='center' bgcolor='#99CC66'>
	<tr style='color:#fff;' bgcolor='#00B000' height=28>
	  <td width='2%' nowrap><div align='left'>SL</div></td>
	  <td width='22%' nowrap><div align='left'>Product Name</div></td>
	  <td width='15%' nowrap><div align='left'>Machine Name</div></td>
	  <td width='8%' nowrap><div align='left'>Shift</div></td>
	  <td width='13%' nowrap><div align='right'>Used Qty (gram/pcs)</div></td>
	  <td width='5%' nowrap><div align='right'>Wastage (%)</div></td>
	  <td width='15%' nowrap><div align='right'>Production Quantity (pcs/day)</div></td>	  
	  <td width='12%' nowrap><div align='right'>Total Used Qty (kg/day)</div></td>
	  <td width='8%' nowrap align='center'>Option</td>
	</tr>";
	$sl=1; $TotalAmount = 0; $TotalUsedQty=0;
	$getSql	= "SELECT po.*,p.product_name,p.m_unit,m.machine_name FROM ".PRODUCTION_FG_TBL." as po, ".PRODUCT_TBL." p, ".MACHINE_TBL." as m WHERE po.finish_product = p.product_id AND po.machine_no = m.machine_id AND po.created_by = '".getFromSession('userid')."' AND po.project_id='".$project_id."' AND po.production_date = '$used_date' AND po.machine_no='$machine_no' AND po.version_no='$version_no' GROUP BY po.detail_id";
	$gres 		= mysql_query($getSql);
	while($row = mysql_fetch_array($gres)){
	extract($row);
	$TotalAmount+=$total_value; $TotalUsedQty+=$total_used_qty;
	$str2.="
	<tr style='color:#000000' bgcolor='#fff'>
	  <td width='2%' nowrap align='left'>$sl</td>
	  <td width='26%' nowrap align='left'>$product_name</td>
	  <td width='10%' nowrap align='left'>$machine_name</td>
	  <td width='10%' nowrap align='left'>$version_no</td>
	  <td width='10%' nowrap><div align='right'>$used_qty</div></td>
	  <td width='10%' nowrap align='right'>$wastage</td>	  
	  <td width='12%' nowrap align='right'>$production_qty</td>
	  <td width='12%' nowrap align='right'>$total_used_qty</td>	  				  
	  <td width='8%' nowrap align='center'><a href=\"#\" onclick=\"ItemDelete($detail_id)\"><img src=\"images/common/icons/delete.gif\"></a></td>
	</tr>";  $sl++;
	}
	$str3="
	<tr style='color:#000;' bgcolor='#CCCCCC' height=25>
	  <td colspan='7' nowrap><div align='right'>Total </div></td>
	  <td nowrap align='right'>$TotalUsedQty </td>
	  <td nowrap align='center'>&nbsp;</td>
	</tr>
	</table>";
	$BatchStr = $str1.$str2.$str3."####-@@@@".$production_id."####-@@@@".$TotalAmount;
	return $BatchStr;
  }
  
function getAjaxBatchProductionDtl(){
	$project_id  		= getFromSession('project_id');
	$machine_no      	= getRequest('machine_no');
	$version_no  		= getRequest('version_no');
	$factory_id		= getRequest('factory_id');
	$in_stock		= getRequest('in_stock');
	$out_stock		= getRequest('out_stock');
	if(getRequest('used_date')!=""){
		$used_date   = formatDate(getRequest('used_date'));
	}else{
		$used_date   = date('Y-m-d');
	}
	$BatchStr    =""; $str1=""; $str2=""; $str3=""; $totalProductionQty=0;
	$str1="<table width='100%' align='center' bgcolor='#99CC66'>
	<tr style='color:#fff;' bgcolor='#00B000' height=28>
	  <td width='2%' nowrap><div align='left'>SL</div></td>
	  <td width='22%' nowrap><div align='left'>Product Name</div></td>
	  <td width='15%' nowrap><div align='left'>Machine Name</div></td>
	  <td width='8%' nowrap><div align='left'>Shift</div></td>
	  <td width='13%' nowrap><div align='right'>Used Qty (gram/pcs)</div></td>
	  <td width='5%' nowrap><div align='right'>Wastage (%)</div></td>
	  <td width='15%' nowrap><div align='right'>Production Quantity (pcs/day)</div></td>	  
	  <td width='12%' nowrap><div align='right'>Total Used Qty (kg/day)</div></td>
	  <td width='8%' nowrap align='center'>Option</td>
	</tr>";
	$sl=1; $TotalAmount = 0; $TotalUsedQty=0;
	$getSql	= "SELECT po.*,p.product_name,p.m_unit,m.machine_name FROM ".PRODUCTION_FG_TBL." as po, ".PRODUCT_TBL." p, ".MACHINE_TBL." as m WHERE po.finish_product = p.product_id AND po.machine_no = m.machine_id AND po.created_by = '".getFromSession('userid')."' AND po.project_id='".$project_id."' AND po.production_date = '$used_date' AND po.machine_no='$machine_no' AND po.version_no='$version_no' GROUP BY po.detail_id";
	$gres 		= mysql_query($getSql);
	while($row = mysql_fetch_array($gres)){
	extract($row);
	$TotalAmount+=$total_value; $totalProductionQty+=$production_qty; $TotalUsedQty+=$total_used_qty;
	$str2.="
	<tr style='color:#000000' bgcolor='#fff'>
	  <td width='2%' nowrap align='left'>$sl</td>
	  <td width='26%' nowrap align='left'>$product_name</td>
	  <td width='10%' nowrap align='left'>$machine_name</td>
	  <td width='10%' nowrap align='left'>$version_no</td>
	  <td width='10%' nowrap><div align='right'>$used_qty</div></td>
	  <td width='10%' nowrap align='right'>$wastage</td>	  
	  <td width='12%' nowrap align='right'>$production_qty</td>
	  <td width='12%' nowrap align='right'>$total_used_qty</td>	  				  
	  <td width='8%' nowrap align='center'><a href=\"#\" onclick=\"ItemDelete($detail_id)\"><img src=\"images/common/icons/delete.gif\"></a></td>
	</tr>";  $sl++;
	}
	$str3="
	<tr style='color:#000;' bgcolor='#CCCCCC' height=25>
	  <td colspan='7' nowrap><div align='right'>Total </div></td>
	  <td nowrap align='right'>$TotalUsedQty</td>
	  <td nowrap align='center'>&nbsp;</td>
	</tr>
	</table>";
	$this->updateProductionMaster($production_id,$totalProductionQty,$TotalAmount);
	if($in_stock =="" && $out_stock ==""){
		$PMSQL	= "SELECT factory_id,in_store_id as in_stock,out_store_id as out_stock FROM ".PRODUCTION_MASTER_TBL." WHERE production_id='$production_id' AND `used_date` = '$used_date' AND project_id='".$project_id."' AND machine_no='$machine_no' AND version_no='$version_no'";
		$pmres 	= mysql_query($PMSQL);
		if(mysql_num_rows($pmres)>0){
			$prow = mysql_fetch_object($pmres);
			$factory_id 	= $prow->factory_id;
			$in_stock 	= $prow->in_stock;
			$out_stock 	= $prow->out_stock;	
		} 
	} 

	echo $BatchStr = $str1.$str2.$str3."####-@@@@".$production_id."####-@@@@".$TotalAmount."####-@@@@".$in_stock."####-@@@@".$out_stock;
	
  }
  function updateProductionMaster($production_id,$finish_qty,$assets_amount){
	mysql_query("START TRANSACTION;");
	mysql_query("SET autocommit=0;");
	$project_id  = getFromSession('project_id');
	$pSql= "SELECT SUM(amount) as production_amount FROM ".PRODUCTION_DETAILS_TBL." WHERE production_id='".$production_id."' AND project_id='".$project_id."'";
	$pres= mysql_query($pSql);
	
	$row = mysql_fetch_object($pres);
	$expense_amount = $row->production_amount;

	$SQL = "UPDATE ".PRODUCTION_MASTER_TBL." SET total_value='$assets_amount', production_amount='$expense_amount', finish_qty='$finish_qty' WHERE production_id='$production_id' AND project_id='$project_id'";
	mysql_query($SQL);
	mysql_query("COMMIT;"); 
	
  }
  function getTotalBalanceAmount($acc_head,$project_id){
	$sql = "SELECT (sum(`dr`) - sum(`cr`)) as balance_amount FROM ".ACCOUNT_JOURNAL_TBL." WHERE sub_id = '$acc_head' AND project_id = '$project_id'";	
	$row = mysql_fetch_object(mysql_query($sql));
	$balance_amount = $row->balance_amount;
	if(empty($balance_amount)){
		$balance_amount = 0;
	}
	return $balance_amount;
    }
   function getStockBalanceQty($acc_head,$project_id,$store_id){
	$sql = "SELECT (sum(`dr`) - sum(`cr`)) as balance_qty FROM ".STOCK_LEDGER_TBL." WHERE product_id = '$acc_head' AND project_id = '$project_id'";
	if($store_id !=""){
	$sql.= " AND store_id ='$store_id'";
	}
	$row = mysql_fetch_object(mysql_query($sql));
	$balance_qty = $row->balance_qty;
	if(empty($balance_qty)){
		$balance_qty = 0;
	}
	return $balance_qty;
   }	
   function createProductionID() {
      $project_id  	  = getFromSession('project_id');
      $info = array();
      $info['table'] = PRODUCTION_MASTER_TBL;
      $info['fields'] = array('max(production_id) as maxProduction');      
      $res = select($info);      
      $maxProductionId = 'P0000000';      
      if(count($res)){
         foreach($res as $v){
		 if($v->maxProduction){
		 $maxProductionId = $v->maxProduction;
		 }
		 break;   	
         }
      }
      $maxProductionId = generateID("P",$maxProductionId,8);
      return $maxProductionId;
   }   
   
   function showPrintEditor($msg = null) {   	  
	  $production_id = getRequest('production_id');  
	  if ($production_id) {
         	 $advArr 		= $this->getProductionMasterInfo($production_id);
         	 $advArr 		= parseThisValue($advArr); 
		 $data   		= array_merge(array(), $advArr); 
		 $data['item_list']	= $this->getProductList($production_id);
		 $data['message'] 	= $msg;
		 $data['cmd']     	= getRequest('cmd');
		 require_once(PRNIT_BATCH_PRODUCTION_SKIN);   
		 return true;
	 }
   }
     
   function showEditor($msg = null) {
   	   $data                	= array();
       	   require_once(CLASS_DIR.'/common.list.class.php');	
	   $comListApp 	= new CommonList();
	  
	   $data['product_list'] 	= $comListApp->getProductList(); 
	   $data['machine_list'] 	= $comListApp->getMachineList();
	   $data['factory_list'] 	= $comListApp->getProductionFactoryList();
	   $data['currency_list']   	= $this->getCurrencyList();  	
	   $data['depo_list'] 		= $comListApp->getDeliveryPointList(true); 
	   $data['tmp_sales']		= $this->getBatchProductionDtl();	   
	   
	   $data['cmd']         	= getRequest('cmd');   
	   require_once(CURRENT_APP_SKIN_FILE); 
	   return $data[0];
   }
   function getProductionMasterInfo($id){		
	$project_id     = getFromSession('project_id');  
	$info           = array();    
	$info['table']  = PRODUCTION_MASTER_TBL.' pm,'.DELIVERY_POINT_TBL.' st,'.MACHINE_TBL.' as m,'.FACTORY_TBL.' f,'.PROJECT_TBL.' pa,'.CURRENCY_TBL.' c';	
	$info['fields'] = array('pm.production_id','pm.version_no','m.machine_name','m.model ','pm.target_qty','pm.finish_qty','pm.total_value','pm.production_amount',"DATE_FORMAT(pm.used_date,'%d %b %y' ) as used_date",'pa.project_name','pa.location',
	'c.curr_symble','st.delivery_point_name as out_store','f.factory_name','pm.created_time');
	
	$sql="pm.factory_id = f.factory_id  AND pm.out_store_id = st.delivery_pid AND pm.machine_no = m.machine_id AND pm.project_id = pa.project_id AND pm.currency = c.currency_id AND pm.project_id = '".$project_id."' AND pm.production_id = '$id'";
						
	$info['where']  =$sql;	  	
	$info['groupby'] = array("pm.production_id");
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
        
   function getProductList($id) { 	  
	$production_type= "Finish";  
	$project_id     = getFromSession('project_id');  
	$info           = array();    
	$info['table']  = PRODUCTION_FG_TBL.' pm,'.PROJECT_TBL.' pa,'.PRODUCT_TBL.' p,'.DELIVERY_POINT_TBL.' st,'.FACTORY_TBL.' f,'.CURRENCY_TBL.' c';	
	$info['fields'] = array('pm.batch_no','pm.finish_product','pa.project_name','pa.location','f.factory_name','f.address','st.delivery_point_name as in_store','pm.total_value','pm.total_used_qty','pm.wastage_qty','pm.unit_price','pm.m_unit',
	'p.product_name','pm.production_qty',"DATE_FORMAT(pm.production_date ,'%d %b %y' ) as used_date",'pm.production_type','c.curr_symble','pm.created_by','pm.created_time');
	
	$sql="pm.finish_product = p.product_id AND pm.store_id = st.delivery_pid  AND pm.project_id = pa.project_id AND pm.factory_id = f.factory_id AND pm.currency = c.currency_id AND pm.project_id = '".$project_id."' AND pm.production_id='".$id."' AND pm.production_type='".$production_type."'";	
							
	$info['where']   = $sql;	  	
	$info['orderby'] = array("pm.detail_id ASC");
	//$info['debug'] = true;
	$result          =	select($info); 
	$cnt = count($result);  	
	if($cnt) {
	foreach($result as $value)  {				
	$data[]	= $value;	
	}
	} 
	
	return $data;
  } 
  //====== End PO Batch =====
      
   function loadProductStock($product_id){
	  $project_id = getFromSession('project_id');  	
	  $transfer_stock = trim(getRequest('transfer_stock'));  		 
	  
	  $Ssql = "SELECT balance FROM ".STORE_STOCK_VIEW." WHERE product_id = '".$product_id."' AND store_id = '$transfer_stock' AND project_id = '$project_id'";
	  $Srow = mysql_fetch_object(mysql_query($Ssql));
	  
	  $Prosql = "SELECT unit_price,m_unit FROM ".PRODUCT_TBL." WHERE product_id = '$product_id' AND project_id = '$project_id'";
	  $Prorow = mysql_fetch_object(mysql_query($Prosql));
	  
	  echo $Srow->balance."#####".$Prorow->unit_price."#####".$Prorow->m_unit;	
   }
   function loadProductStockQty($product_id){
	  $project_id 	  = getFromSession('project_id');  	
	  $transfer_stock = trim(getRequest('transfer_stock'));  		 
	  $sl 	= trim(getRequest('sl'));  
	  $Ssql = "SELECT balance FROM ".STORE_STOCK_VIEW." WHERE product_id = '".$product_id."' AND store_id = '$transfer_stock' AND project_id = '$project_id'";
	  $Srow = mysql_fetch_object(mysql_query($Ssql));
	  
	  $Prosql = "SELECT unit_price,m_unit FROM ".PRODUCT_TBL." WHERE product_id = '$product_id' AND project_id = '$project_id'";
	  $Prorow = mysql_fetch_object(mysql_query($Prosql));
	  
	  echo $Srow->balance."#####".$Prorow->unit_price."#####".$Prorow->m_unit."#####".$sl;	
    }		
   function saveAccountJournal($voucher_no,$sub_id,$head_type,$project_id,$description,$DR=NULL,$CR=NULL,$balance,$status=NULL,$purchare_date=NULL){
	$head_type= getHeadType($sub_id);	
	$sql = "INSERT INTO ".ACCOUNT_JOURNAL_TBL." (voucher_no,created_date,sub_id,head_type,project_id,description,dr,cr,balance,status) VALUES('".$voucher_no."','".$purchare_date."','".$sub_id."','".$head_type."','".$project_id."','".$description."','".$DR."','".$CR."','".$balance."','".$status."')";
	mysql_query($sql);
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
  function saveStockJournal($voucher_no,$pvoucher_no,$project_id,$store_id,$product_id,$product_type,$note,$unit_price=NULL,$m_unit,$DR=NULL,$CR=NULL,$balance,$create_date=NULL){
	$created_by = getFromSession('userid');
	$sql = "INSERT INTO ".STOCK_LEDGER_TBL." (voucher_no,po_no,project_id,store_id,product_id,product_type,note,unit_price,m_unit,dr,cr,balance,created_by,create_date) 
	VALUES('".$voucher_no."','".$pvoucher_no."','".$project_id."','".$store_id."','".$product_id."','".$product_type."','".$note."','".$unit_price."','".$m_unit."','".$DR."','".$CR."','".$balance."','".$created_by."','".$create_date."')";
	mysql_query($sql); 
  } 
    
   //==================== End Sales Details =====================
   
} // End class


?>
