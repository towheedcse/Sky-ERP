<?php
class AttachProduct
{
   function run()
   {         
      $cmd = getRequest('cmd');
	  $u_t_id = getFromSession('u_type_id');
      if( ($u_t_id ==101) || ($u_t_id == 102) || ($u_t_id == 103)) // 101 = sysadmin, 102 = admin, 103= salesman
      {
      	switch ($cmd)
      	{
      	   case 'add'					: $this->showEditor(); break;
      	   case 'loadProduct'  			: $this->loadProduct4Catagory(trim(getRequest('catagory_id'))); break; 
      	   case 'save_tmp'  			: $this->saveTempSales(); break; 
		   case 'loadGPG'				: $this->getGroupProductGride(); break; 
		   case 'deltemp'				: $this->delTempSales(); break;  
		   case 'allClear'	  			: $this->deleteAllTempSales(); break;  
		   case 'saveSales'				: $this->saveSalesItem(); break; 
		   case 'print_vouchar'			: $screen = $this->showPrintEditor($msg); break;  
		   case 'delete'             	: $screen = $this->deleteRecord(getRequest('id')); break; 
		   case 'clear_all'             : $screen = $this->clearAll(getRequest('id')); break;
		   
      	   default                   	: $cmd = 'list'; $screen = $this->showEditor();   break;
      	}
      }elseif($u_t_id == 104) // 104 = acc
      {
      	switch ($cmd)
      	{
		   case 'sal_dtl'				: $this->showEditor4SalesDetails(); break;
		   case 'print_vouchar'			: $screen = $this->showPrintEditor($msg); break;
      	   default                   	: $cmd = 'list'; $screen = $this->showEditor();   break;
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
	  $voucher_no 	= getRequest('voucher_no');  
	  if ($voucher_no) {
         $advArr 			= $this->getSalesMasterInfo($voucher_no);
         $advArr 			= parseThisValue($advArr); 
		 $data   			= array_merge(array(), $advArr); 
		 $data['item_list']	= $this->getProductList($voucher_no);
		 $data['message'] 	= $msg;
		 $data['cmd']     	= getRequest('cmd');
		if(getFromSession('u_type_id')==102){
		 require_once(POS_SALES_VOUCHAR_SKIN);
		}else{
		 require_once(SALES_VOUCHAR_SKIN);
		}
		 return true;
	 }else{
		require_once(PRINT_VOUCHAR_SKIN);
	  }
   }
   
   function showEditor($msg = null) {
   	   $data                	= array();
       require_once(CLASS_DIR.'/common.list.class.php');	
	   $comListApp 	= new CommonList();      
	   $data['group_list'] 		= $this->getGroupList();	      
	   $data['cat_list'] 		= $comListApp->getCatagoryList();		
	   $data['product_list'] 	= $comListApp->getProductList();	   	
	   $data['area_list'] 		= $comListApp->getAreaList(); 
	   $data['group_items']		= $this->getTempSales();  
	   
	   $data['cmd']         	= getRequest('cmd');   
	   require_once(CURRENT_APP_SKIN_FILE); 
	   return $data[0];
   }
    //===== Saart Save Sales ====
	function saveTempSales(){
		//======= Insert into tamp ========	
		
		$requestdata = array();
		$requestdata = getUserDataSet(GROUP_WISE_PRODUCT_TBL);
		$project_id  	= getFromSession('project_id');
		$group_id 		= getRequest('group_id');
		$catagory_id 	= getRequest('catagoryid');
		$product_id 	= getRequest('productid');
		if($product_id !="0" && $catagory_id !=""){
		$requestdata['project_id'] 		= $project_id;
		$requestdata['group_id'] 		= getRequest('group_id');
		$requestdata['catagory_id'] 	= getRequest('catagoryid');
		$requestdata['product_id'] 		= getRequest('productid');
		$requestdata['created_by'] 		= getFromSession('userid');		
		$info        	=  array();
		$info['table']	= GROUP_WISE_PRODUCT_TBL;
		$info['data'] 	= $requestdata;     
		//$info['debug']  	=  true;
		$res = insert($info);
		}elseif($product_id =="0" && $catagory_id !=""){
		$PuSql="SELECT * FROM ".PRODUCT_TBL." WHERE catagory='$catagory_id' AND project_id='$project_id' ORDER BY product_name ASC"; 
		$pres = mysql_query($PuSql);
			if(mysql_num_rows($pres)>0){	 
				while($Purow = mysql_fetch_object($pres)){
					$product_id  = $Purow->product_id;				
					$sql="INSERT INTO ".GROUP_WISE_PRODUCT_TBL." (project_id,group_id,catagory_id,product_id,created_by) 
					VALUES('".$project_id."','".$group_id."','".$catagory_id."','".$product_id."','".getFromSession('userid')."')";
					mysql_query($sql);
				}
			}
		}
		$str1="<table width='100%' align='center' bgcolor='#99CC66'>
		<tr style='color:#fff;' bgcolor='#007DFB' height=28>
		  <td width='2%' nowrap><div align='left'>SL</div></td>
		  <td width='20%' nowrap><div align='left'>Group Name</div></td>
		  <td width='25%' nowrap><div align='left'>Catagory</div></td>
		  <td width='40%' nowrap><div align='left'>Product Name</div></td>			  
		  <td width='13%' nowrap align='center'>Option</td>
		</tr>";
		
		$sl=1;
		$getSql		= "SELECT gp.id,g.group_name,c.catagory_name,p.product_name FROM ".GROUP_WISE_PRODUCT_TBL." as gp,".PRODUCT_GROUP_TBL." as g,".PRODUCT_TBL." as p,".CATAGORY_TBL." as c WHERE gp.group_id = g.group_id AND gp.product_id=p.product_id AND p.catagory=c.catagory_code AND gp.group_id = '".$group_id."' AND gp.project_id='".$project_id."' ORDER BY c.`catagory_name`,p.product_name ASC";
		$gres 		= mysql_query($getSql);
		while($row = mysql_fetch_array($gres)){
		extract($row);		
		$str2.="
		<tr style='color:#000000' bgcolor='#fff'>
		  <td width='2%' nowrap align='left'>$sl</td>
		  <td width='20%' nowrap align='left'>$group_name</td>
		  <td width='25%' nowrap align='left'>$catagory_name</td>
		  <td width='40%' nowrap align='left'>$product_name</td>		  				  
		  <td width='13%' nowrap align='center'><a href=\"?app=attach.product&cmd=deltemp&id=$id\"><img src=\"images/common/icons/delete.gif\"></a></td>
		</tr>";  $sl++;
		}
		
		echo $str1.$str2;
	}
	
	function delTempSales(){
		$tmp_id = $_REQUEST['id'];
		if($tmp_id!=""){
		 $dsql = "DELETE FROM ".GROUP_WISE_PRODUCT_TBL." WHERE id ='".$tmp_id."'";
		 mysql_query($dsql);
		}		
		header("location:?app=attach.product");
	}
	function clearAll(){
		$group_id = $_REQUEST['id'];
		if($group_id!=""){
		 $dsql = "DELETE FROM ".GROUP_WISE_PRODUCT_TBL." WHERE group_id ='".$group_id."'";
		 mysql_query($dsql);
		}		
		header("location:?app=attach.product&msg=Records successfully deleted");
	}
	function getGroupProductGride(){
		$project_id  	= getFromSession('project_id');
		$group_id 		= getRequest('group_id');
		$str1="<table width='100%' align='center' bgcolor='#99CC66'>
		<tr style='color:#fff;' bgcolor='#007DFB' height=28>
		  <td width='2%' nowrap><div align='left'>SL</div></td>
		  <td width='20%' nowrap><div align='left'>Group Name</div></td>
		  <td width='25%' nowrap><div align='left'>Catagory</div></td>
		  <td width='40%' nowrap><div align='left'>Product Name</div></td>			  
		  <td width='13%' nowrap align='center'>Option</td>
		</tr>";
		
		$sl=1;
		$getSql		= "SELECT gp.id,g.group_name,c.catagory_name,p.product_name FROM ".GROUP_WISE_PRODUCT_TBL." as gp,".PRODUCT_GROUP_TBL." as g,".PRODUCT_TBL." as p,".CATAGORY_TBL." as c WHERE gp.group_id = g.group_id AND gp.product_id=p.product_id AND p.catagory=c.catagory_code";
		if($group_id !=""){ 
		$getSql.= " AND gp.group_id = '".$group_id."' AND gp.project_id='".$project_id."' ORDER BY c.`catagory_name`,p.product_name ASC";
		}else{
		$getSql.= " AND gp.project_id='".$project_id."' ORDER BY c.`catagory_name`,p.product_name ASC";	
		}
		
		$gres 		= mysql_query($getSql);
		while($row = mysql_fetch_array($gres)){
		extract($row);		
		$str2.="
		<tr style='color:#000000' bgcolor='#fff'>
		  <td width='2%' nowrap align='left'>$sl</td>
		  <td width='20%' nowrap align='left'>$group_name</td>
		  <td width='25%' nowrap align='left'>$catagory_name</td>
		  <td width='40%' nowrap align='left'>$product_name</td>		  				  
		  <td width='13%' nowrap align='center'><a href=\"?app=attach.product&cmd=deltemp&id=$id\"><img src=\"images/common/icons/delete.gif\"></a></td>
		</tr>";  $sl++;
		}
		echo $str1.$str2;
	}
	
	function getTempSales(){
		$project_id  	= getFromSession('project_id');
		$group_id 		= getRequest('group_id');
		$str1="<table width='100%' align='center' bgcolor='#99CC66'>
		<tr style='color:#fff;' bgcolor='#007DFB' height=28>
		  <td width='2%' nowrap><div align='left'>SL</div></td>
		  <td width='20%' nowrap><div align='left'>Group Name</div></td>
		  <td width='25%' nowrap><div align='left'>Catagory</div></td>
		  <td width='40%' nowrap><div align='left'>Product Name</div></td>			  
		  <td width='13%' nowrap align='center'>Option</td>
		</tr>";
		
		$sl=1;
		$getSql		= "SELECT gp.id,g.group_name,c.catagory_name,p.product_name FROM ".GROUP_WISE_PRODUCT_TBL." as gp,".PRODUCT_GROUP_TBL." as g,".PRODUCT_TBL." as p,".CATAGORY_TBL." as c WHERE gp.group_id = g.group_id AND gp.product_id=p.product_id AND p.catagory=c.catagory_code";
		if($group_id !=""){ 
		$getSql.= " AND gp.group_id = '".$group_id."' AND gp.project_id='".$project_id."' ORDER BY c.`catagory_name`,p.product_name ASC";
		}else{
		$getSql.= " AND gp.project_id='".$project_id."' ORDER BY c.`catagory_name`,p.product_name ASC";	
		}
		
		$gres 		= mysql_query($getSql);
		while($row = mysql_fetch_array($gres)){
		extract($row);		
		$str2.="
		<tr style='color:#000000' bgcolor='#fff'>
		  <td width='2%' nowrap align='left'>$sl</td>
		  <td width='20%' nowrap align='left'>$group_name</td>
		  <td width='25%' nowrap align='left'>$catagory_name</td>
		  <td width='40%' nowrap align='left'>$product_name</td>		  				  
		  <td width='13%' nowrap align='center'><a href=\"?app=attach.product&cmd=deltemp&id=$id\"><img src=\"images/common/icons/delete.gif\"></a></td>
		</tr>";  $sl++;
		}
		
		return $str1.$str2;
	}
  //====== End Save Sales =====
  
    
function getSalesMasterInfo($id){		   
	$project_id     = getFromSession('project_id');  
	$info           = array();    
	$info['table']  = SALES_MASTER_TBL.' pm,'.SUB_ACC_HEAD_TBL.' s,'.PROJECT_TBL.' p,'.CURRENCY_TBL.' c';	
	$info['fields'] = array('pm.voucher_no','pm.po_no','pm.wo_no','pm.delivery_point','p.project_name','p.location','p.project_logo','pm.customer','s.sub_head_name','s.head_details','s.phone','s.mobile','s.email','s.att_name1','s.att_designation1','s.att_mobile1','pm.reference','pm.gate_pass','pm.track_no','pm.salse_type','pm.total_value',"DATE_FORMAT(pm.sales_date,'%d %b %y' ) as sales_date","pm.sales_date as salesdate","DATE_FORMAT(pm.delivery_date,'%d %b %y' ) as delivery_date",'pm.mode_of_payment','c.curr_symble','pm.bank_name','pm.acc_no','pm.check_no','pm.check_no','pm.general_discount_percent','pm.discount','pm.service_charge','pm.net_payble','pm.previour_balance','pm.adjust','pm.item_delivery_amount','pm.paid_amount','pm.due','pm.ref_no','pm.created_date');	
	$sql="pm.customer = s.sub_id AND pm.project_id = p.project_id AND pm.currency = c.currency_id AND pm.project_id = '".$project_id."' AND pm.voucher_no = '$id'";						
	$info['where']  =$sql;	  	
	$info['groupby'] = array("pm.voucher_no");
	$res            =	select($info);
	if(count($res)){
		foreach($res as $i=>$v){
			$data[$i] = $v;             
		}
	}
	return $data[0];
} 
  function getGroupList()
   {
      $info            = array();
      $info['table']   = PRODUCT_GROUP_TBL;
      //$info['fields'] = array('currency_id', 'name'); 
	  $info['orderby'] = array("group_name ASC");
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
   
   function getProductList($id) { 
		$info           = array();    
		$info['table']  =  SALES_DETAILS_TBL.' sd,'.PRODUCT_TBL.' p,'.CURRENCY_TBL.' c,'.BRAND_TBL.' b';	
		$info['fields'] = array('sd.sal_detail_id','sd.voucher_no','sd.project_id','sd.serial','sd.warranty','sd.catagory','b.brand_name','sd.product','sd.details','p.product_name','p.product_desc','sd.m_unit','sd.unit_price','c.curr_symble','sd.discount_per_qty','sd.qty','sd.delivery_qty','sd.total_bag','sd.total','sd.created_time');
		
		$sql="sd.product = p.product_id AND p.brand_code = b.brand_id AND sd.currency = c.currency_id AND sd.voucher_no = '$id'";
		
		$info['where']  = $sql;
	    $info['groupby'] = array("sd.sal_detail_id");
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
	function loadProduct4Catagory($catagory)
	{		  
	  	  $project_id = getFromSession('project_id');  
		  $info            = array();
		  $info['table']   = PRODUCT_TBL;
		  $info['fields']  =  array('product_id','product_name','product_desc');
		  $info['where']   = "`catagory`='$catagory' AND project_id = '$project_id'";
		  $info['groupby'] = array("product_id");
		  $info['orderby'] = array("product_name");
		  //$info['debug']   = true;	
		  $result          = select($info);
		  $data            = array();	
		  if(count($result)){
			 foreach($result as $key=>$value){
				$data[$key][]        = $value;
			 }
		  }
		  foreach($data as $i=>$v){
			 $subject_idname .= $v[0]->product_id.'#####'.$v[0]->product_name.'#####'.$v[0]->product_desc.'@@@';
		  }
		  echo $subject_idname;	
	}

  function loadProductDtl($product_id){
	$project_id = getFromSession('project_id');  	$stock_qty=0;	 
	$info            = array();	  	  
	$info['table']   = PRODUCT_TBL." p,".CATAGORY_TBL.' c,'.BRAND_TBL.' b';
	$info['fields']  =  array('p.m_unit','p.product_desc','p.unit_price','p.product_catagory','p.catagory','c.catagory_name','p.brand_code','b.brand_name');
	$info['where']   = "p.catagory =c.catagory_code AND p.brand_code =b.brand_id AND p.product_id = '$product_id'  AND p.project_id = '$project_id'";
	$info['groupby'] = array("p.product_id");		  
	//$info['debug']   = true;
	$result          = select($info);
	$data            = array();
	if(count($result)){
	 foreach($result as $key=>$value){
		$data[$key][]        = $value;
	 }
	}
	
	foreach($data as $i=>$v){
	$str = $v[0]->m_unit."#####".$v[0]->product_desc."#####".$v[0]->unit_price."#####".$v[0]->catagory."###".$v[0]->catagory_name."#####".$v[0]->brand_code."###".$v[0]->brand_name;
	}
	$stock_qty = $this->loadStockQty($product_id); 
	echo $str."#####".$stock_qty;	
  }
	  
   //==================== End Sales Details =====================
  
} // End class
?>