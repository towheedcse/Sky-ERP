<?php
class SalesTarget
{
   function run()
   {         
      $cmd = getRequest('cmd');
	  $u_t_id = getFromSession('u_type_id');
      if( ($u_t_id ==101) || ($u_t_id == 102) || ($u_t_id == 103) || ($u_t_id == 104)) // 101 = sysadmin, 102 = admin, 103= salesman
      {
      	switch ($cmd)
      	{
      	   case 'add'			: $this->showEditor(); break;
	   case 'catagory_tagget'	: $this->showCatagoryEditor(); break;
	   case 'amount_target'		: $this->showAmountTargetEditor(); break;
	   case 'copy'			: $this->showCopySTEditor(); break;
	   case 'copySalesTarget'	: $this->copySalesTarget(); break;
      	   case 'loadProduct'  		: $this->loadProduct4Group(trim(getRequest('group_id'))); break; 
	   case 'loadGroupCatagory'	: $this->loadCatagory4Group(trim(getRequest('group_id'))); break;
      	   case 'save_tmp'  		: $this->saveTempSales(); break; 
	   case 'save_ast'  		: $this->saveAmountSalesTarget(); break; 
	   case 'save_cst'  		: $this->saveCatagorySalesTarget(); break; 
	   case 'loadGPG'		: $this->getGroupProductGride(); break;
	   case 'loadAST'		: $this->getAmountSalesTargetGride(); break; 
	   case 'loadCST'		: $this->getCatagorySalesTargetGride(); break; 
	   case 'deltemp'		: $this->delTempSales(); break; 
	   case 'delcst'		: $this->delCatagorySalesTarget(); break;
	   case 'delast'		: $this->delAmountSalesTarget(); break;  
	   case 'allClear'	  	: $this->deleteAllTempSales(); break;  
	   case 'saveSales'		: $this->saveSalesItem(); break; 
	   case 'print_vouchar'		: $screen = $this->showPrintEditor($msg); break;  
	   case 'delete'             	: $screen = $this->deleteRecord(getRequest('id')); break;
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
         	$advArr 		= $this->getSalesMasterInfo($voucher_no);
         	$advArr 		= parseThisValue($advArr); 
		$data   		= array_merge(array(), $advArr); 
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
   function showCatagoryEditor(){
	   $data                	= array();
           require_once(CLASS_DIR.'/common.list.class.php');	
	   $comListApp 	= new CommonList();      
	   $data['group_list'] 		= $this->getGroupList();	      
	   $data['catagory_list'] 	= $comListApp->getCatagoryList();		
	   $data['product_list'] 	= $comListApp->getProductList();	   	
	   $data['area_list'] 		= $comListApp->getAreaList(); 
	   $data['group_items']		= $this->getCatagorySalesTarget();  
	   
	   $data['cmd']         	= getRequest('cmd');   
	   require_once(CATAGORY_SALES_TARGET_SKIN_FILE); 
	   return $data[0];
   }

   function showAmountTargetEditor(){
	   $data                	= array();
	   $data['record_items']	= $this->getAmountSalesTargetGride(false);  
	   
//print_r(json_encode($data['record_items']));exit();
	   $data['cmd']         	= getRequest('cmd');   
	   require_once(AMOUNT_SALES_TARGET_SKIN_FILE); 
	   return $data[0];
   }

   function showCopySTEditor($msg = null) {
   	   $data                	= array();
           require_once(CLASS_DIR.'/common.list.class.php');	
	   $comListApp 	= new CommonList();      
	   $data['group_list'] 		= $this->getGroupList();	      
	   $data['cat_list'] 		= $comListApp->getCatagoryList();		
	   $data['product_list'] 	= $comListApp->getProductList();	   	
	   $data['area_list'] 		= $comListApp->getAreaList(); 
	   //$data['group_items']	= $this->getTempSales();  
	   
	   $data['cmd']         	= getRequest('cmd');   
	   require_once(COPY_SALES_TARGET_SKIN_FILE); 
	   return $data[0];
   }
    //===== Saart Save Sales ====
	function saveTempSales(){
		//======= Insert into tamp ========	
				
		$project_id  	= getFromSession('project_id');
		$division_id 	= getRequest('division_id');
		$date_from 	= formatDate(getRequest('date_from'));
		$date_to 	= formatDate(getRequest('date_to'));
		$district_id 	= getRequest('districtid');
		$area_id 	= getRequest('areaid');
		$catagory 	= getRequest('catagory');
		$product_id 	= getRequest('productid');
		$target_qty 	= intval(getRequest('target_qty'));
		if($product_id !="0" && $division_id !="" && $date_from !="" && $date_to!="" && $target_qty >0){
			$sql = "SELECT catagory,brand_code,m_unit FROM ".PRODUCT_TBL." WHERE product_id = '".$product_id."'";
			$row = mysql_fetch_object(mysql_query($sql));
			$requestdata = array();
			$requestdata = getUserDataSet(SALES_TARGET_TBL);	
			$requestdata['catagory_id'] 	= $row->catagory;			
			$requestdata['project_id'] 	= $project_id;
			$requestdata['division_id'] 	= $division_id;
			$requestdata['district_id'] 	= $district_id;
			$requestdata['area_id'] 	= $area_id;
			$requestdata['product_id'] 	= $product_id;
			$requestdata['target_from'] 	= $date_from;
			$requestdata['target_to'] 	= $date_to;
			$requestdata['target_qty'] 	= $target_qty;
			$requestdata['created_by'] 	= getFromSession('userid');		
			$info        	=  array();
			$info['table']	= SALES_TARGET_TBL;
			$info['data'] 	= $requestdata;     
			$info['debug']  	=  true;
			$res = insert($info);
		}elseif($catagory !="" && $group_id !="" && $date_from !="" && $date_to!="" && $target_qty >0){
			$sql= "SELECT p.product_id,p.catagory,p.brand_code,p.m_unit FROM ".PRODUCT_TBL." as p,".CATAGORY_TBL." as c WHERE p.catagory = c.catagory_code AND c.catagory_code='$catagory' AND gp.catagory_id = '$catagory' GROUP BY gp.product_id";
			$res = mysql_query($sql);
			while($row = mysql_fetch_object($res)){				
			$project_id 	= $project_id;
			$division_id 	= $division_id;
			$catagory_id 	= $row->catagory;
			$district_id	= $district_id;
			$area_id 	= $area_id;
			$product_id 	= $row->product_id;
			$target_from 	= $date_from;
			$target_to 	= $date_to;
			$target_qty 	= $target_qty;
			$created_by 	= getFromSession('userid');
			$isql="INSERT INTO ".SALES_TARGET_TBL." (project_id,division_id,catagory_id,district_id,area_id,product_id,target_from,target_to,target_qty,created_by) 
	VALUES('".$project_id."','".$division_id."','".$catagory_id."','".$district_id."','".$area_id."','".$product_id."','".$target_from."','".$target_to."','".$target_qty."','".$created_by."')";
			mysql_query($isql);

			}
		}
		$str1="<table width='100%' align='center' bgcolor='#99CC66'>
		<tr style='color:#fff;' bgcolor='#007DFB' height=28>
		  <td width='2%' nowrap><div align='left'>SL</div></td>
		  <td width='15%' nowrap><div align='left'>Catagory</div></td>
		  <td width='15%' nowrap><div align='left'>Division Name</div></td>
		  <td width='10%' nowrap><div align='left'>Area Name</div></td>
		  <td width='10%' nowrap><div align='left'>TRT Name</div></td>
		  <td width='20%' nowrap><div align='left'>Product Name</div></td>
		  <td width='12%' nowrap><div align='left'>Target Date</div></td>	
		  <td width='10%' nowrap><div align='left'>Target Qty</div></td>				  
		  <td width='6%' nowrap align='center'>Option</td>
		</tr>";
		
		$sl=1;
		$getSql		= "SELECT st.id,dv.division_name_eng as division_name,c.catagory_name,p.product_name,p.m_unit,d.district_name,a.area_name,DATE_FORMAT(st.target_from,'%d %b %y' ) as target_from,DATE_FORMAT(st.target_to,'%d %b %y' ) as target_to,st.target_qty FROM ".SALES_TARGET_TBL." as st,".PRODUCT_TBL." as p,".CATAGORY_TBL." as c,".DIVISION_TBL." as dv,".DISTRICT_TBL." as d,".AREA_TBL." as a WHERE st.product_id=p.product_id AND p.catagory=c.catagory_code AND st.division_id=dv.division_id AND st.district_id=d.district_id AND d.district_id=a.district AND st.area_id=a.area_id AND st.project_id='".$project_id."'";
		
		if($date_from !=""){
			$getSql		.=" AND st.target_from >= '".$date_from."'";
		}
		if($date_to !=""){
			$getSql		.=" AND st.target_to <= '".$date_to."'";
		}
		
		$getSql		.=" GROUP BY st.id ORDER BY g.`division_name_eng`,d.district_name,p.`product_name` ASC";
		
		$gres 	= mysql_query($getSql);
		while($row = mysql_fetch_array($gres)){
		extract($row);		
		$str2.="
		<tr style='color:#000000' bgcolor='#fff'>
		  <td width='2%' nowrap align='left'>$sl</td>
		  <td width='15%' nowrap align='left'>$catagory_name</td>
		  <td width='15%' nowrap align='left'>$division_name</td>
		  <td width='10%' nowrap align='left'>$district_name</td>
		  <td width='10%' nowrap align='left'>$area_name</td>
		  <td width='20%' nowrap align='left'>$product_name</td>
		  <td width='12%' nowrap align='left'>$target_from to $target_to</td>
		  <td width='10%' nowrap align='left'>$target_qty $m_unit</td>		  				  
		  <td width='6%' nowrap align='center'><a href=\"?app=sales_target&cmd=deltemp&id=$id&date_from=$date_from&date_to=$date_to\"><img src=\"images/common/icons/delete.gif\"></a></td>
		</tr>";  $sl++;
		}
		
		echo $str1.$str2;
	}
	
	function saveCatagorySalesTarget(){
		$project_id  	= getFromSession('project_id');
		$division_id 	= getRequest('division_id');
		$date_from 	= formatDate(getRequest('date_from'));
		$date_to 	= formatDate(getRequest('date_to'));
		$district_id 	= getRequest('districtid');
		$area_id 	= getRequest('areaid');
		$catagory 	= getRequest('catagory');
		$product 	= getRequest('product');
		$target_qty 	= intval(getRequest('target_qty'));
		if($catagory !="" && $division_id !="" && $date_from !="" && $date_to!="" && $target_qty >0){
			
			$psql = "SELECT brand_code,m_unit,unit_price FROM ".PRODUCT_TBL." WHERE product_id = '".$product."'";
			$pres = mysql_query($psql);
			if(mysql_num_rows($pres) >0){
			$prow 		= mysql_fetch_object($pres);
			$unit_price 	= $prow->unit_price;	
			$target_amount  = ($target_qty * $prow->unit_price);
			}else{
			$target_amount  = 0;
			}
			$project_id 	= $project_id;
			$division_id 	= $division_id;
			$catagory_id 	= $catagory;
			$product 	= $product;
			$district_id	= $district_id;
			$area_id 	= $area_id;
			$target_from 	= $date_from;
			$target_to 	= $date_to;
			$target_qty 	= $target_qty;
			$created_by 	= getFromSession('userid');
			$isql="INSERT INTO ".SALES_TARGET_CATAGORY_TBL." (project_id,division_id,catagory_id,product,district_id,area_id,target_from,target_to,target_qty,target_amount,created_by) 
	VALUES('".$project_id."','".$division_id."','".$catagory_id."','".$product."','".$district_id."','".$area_id."','".$target_from."','".$target_to."','".$target_qty."','".$target_amount."','".$created_by."')";
			mysql_query($isql);

			
		}
		$str1="<table width='100%' align='center' bgcolor='#99CC66'>
		<tr style='color:#fff;' bgcolor='#007DFB' height=28>
		  <td width='2%' nowrap><div align='left'>SL</div></td>
		  <td width='12%' nowrap><div align='left'>Catagory</div></td>
		  <td width='24%' nowrap><div align='left'>Product</div></td>
		  <td width='10%' nowrap><div align='left'>Division Name</div></td>
		  <td width='10%' nowrap><div align='left'>Area Name</div></td>
		  <td width='14%' nowrap><div align='left'>TRT Name</div></td>
		  <td width='12%' nowrap><div align='left'>Target Date</div></td>	
		  <td width='6%' nowrap><div align='left'>Target Qty</div></td>	
		  <td width='6%' nowrap><div align='left'>Target Value</div></td>
		  <td width='6%' nowrap align='center'>Option</td>
		</tr>";
		
		$sl=1;
		$getSql	 = "SELECT st.id,dv.division_name_eng as division_name,c.catagory_name,st.product,d.district_name,a.area_name,DATE_FORMAT(st.target_from,'%d %b %y' ) as target_from,DATE_FORMAT(st.target_to,'%d %b %y' ) as target_to,st.target_qty,st.target_amount FROM ".SALES_TARGET_CATAGORY_TBL." as st,".CATAGORY_TBL." as c,".DIVISION_TBL." as dv,".DISTRICT_TBL." as d,".AREA_TBL." as a WHERE st.catagory_id=c.catagory_code AND st.division_id=dv.division_id AND st.district_id=d.district_id AND d.district_id=a.district AND st.area_id=a.area_id AND st.project_id='".$project_id."'";
		
		if($date_from !=""){
			$getSql	.=" AND st.target_from >= '".$date_from."'";
		}
		if($date_to !=""){
			$getSql	.=" AND st.target_to <= '".$date_to."'";
		}
		
		$getSql	.=" GROUP BY st.id ORDER BY dv.`division_name_eng`,d.district_name,c.catagory_name ASC";
		
		$gres 		= mysql_query($getSql);
		while($row = mysql_fetch_array($gres)){
		extract($row);	
		$psql = "SELECT product_name,m_unit,unit_price FROM ".PRODUCT_TBL." WHERE product_id = '".$product."'";
		$pres = mysql_query($psql);
		if(mysql_num_rows($pres) >0){
		$prow 		= mysql_fetch_object($pres);
		$product_name 	= $prow->product_name;
		}else{
		$product_name   = "";
		}
	
		$str2.="
		<tr style='color:#000000' bgcolor='#fff'>
		  <td width='2%' nowrap align='left'>$sl</td>
		  <td width='12%' nowrap align='left'>$catagory_name</td>
		  <td width='24%' nowrap align='left'>$product_name</td>
		  <td width='10%' nowrap align='left'>$division_name</td>
		  <td width='10%' nowrap align='left'>$district_name</td>
		  <td width='14%' nowrap align='left'>$area_name</td>
		  <td width='12%' nowrap align='left'>$target_from to $target_to</td>
		  <td width='6%' nowrap align='left'>$target_qty $m_unit</td>
		  <td width='6%' nowrap align='left'>$target_amount</td>				  				  
		  <td width='6%' nowrap align='center'><a href=\"?app=sales_target&cmd=delcst&id=$id&date_from=$date_from&date_to=$date_to\"><img src=\"images/common/icons/delete.gif\"></a></td>
		</tr>";  $sl++;
		}
		
		echo $str1.$str2;
	}

	function saveAmountSalesTarget(){
		$project_id  	= getFromSession('project_id');
		$division_id 	= getRequest('division_id');
		$date_from 	= formatDate(getRequest('date_from'));
		$date_to 	= formatDate(getRequest('date_to'));
		$district_id 	= getRequest('district');
		$area_id 	= getRequest('area');
		$target_amount 	= intval(getRequest('target_amount'));
		if($division_id !="" && $date_from !="" && $date_to!="" && $target_amount >0){
			$project_id 	= $project_id;
			$division_id 	= $division_id;
			$catagory_id 	= $catagory;
			$product 	= $product;
			$district_id	= $district_id;
			$area_id 	= $area_id;
			$target_from 	= $date_from;
			$target_to 	= $date_to;
			$target_qty 	= $target_qty;
			$created_by 	= getFromSession('userid');
			$isql="INSERT INTO area_sales_target (project_id,division_id,district_id,area_id,target_from,target_to,target_amount,created_by) 
	VALUES('".$project_id."','".$division_id."','".$district_id."','".$area_id."','".$target_from."','".$target_to."','".$target_amount."','".$created_by."')";
			mysql_query($isql);

			
		}
		
		$this->getAmountSalesTargetGride();
	}


	
	function copySalesTarget(){
				
		$requestdata = array();
		$requestdata = getUserDataSet(SALES_TARGET_TBL);
		$project_id  	= getFromSession('project_id');		
		
		$date_from 	= formatDate(getRequest('date_from'));
		$date_to 	= formatDate(getRequest('date_to'));
		
		$copy_date_from = formatDate(getRequest('copy_date_from'));
		$copy_date_to   = formatDate(getRequest('copy_date_to'));
		
		$sl=1;
		$getSql		= "SELECT * FROM ".SALES_TARGET_CATAGORY_TBL."  WHERE project_id='".$project_id."'";
		if($date_from !=""){
			$getSql	.=" AND target_from >= '".$date_from."'";
		}
		if($date_to !=""){
			$getSql	.=" AND target_to <= '".$date_to."'";
		}
		$getSql		.=" GROUP BY id ORDER BY id ASC"; //echo $getSql;
		$gres 		= mysql_query($getSql);
		while($row = mysql_fetch_array($gres)){
			extract($row);	
			if($catagory_id !="" && $copy_date_from !="" && $copy_date_to!="" && $target_qty >0){	
				$requestdata['project_id'] 	= $project_id;		
				$requestdata['division_id'] 	= $division_id;		
				$requestdata['catagory_id'] 	= $catagory_id;			
				$requestdata['district_id'] 	= $district_id;
				$requestdata['area_id'] 	= $area_id;
				$requestdata['product_id'] 	= $product_id;
				$requestdata['target_from'] 	= $copy_date_from;
				$requestdata['target_to'] 	= $copy_date_to;
				$requestdata['target_qty'] 	= $target_qty;
				$requestdata['created_by'] 	= getFromSession('userid');		
				$info        	=  array();
				$info['table']	= SALES_TARGET_CATAGORY_TBL;
				$info['data'] 	= $requestdata;     
				//$info['debug']  	=  true;
				$res = insert($info);
			}	
			$sl++;
		}
		
		
		$str1="<table width='100%' align='center' bgcolor='#99CC66'>
		<tr style='color:#fff;' bgcolor='#007DFB' height=28>
		  <td width='2%' nowrap><div align='left'>SL</div></td>
		  <td width='15%' nowrap><div align='left'>Catagory</div></td>
		  <td width='15%' nowrap><div align='left'>Division Name</div></td>
		  <td width='10%' nowrap><div align='left'>Area Name</div></td>
		  <td width='10%' nowrap><div align='left'>TRT Name</div></td>
		  <td width='20%' nowrap><div align='left'>Product Name</div></td>
		  <td width='12%' nowrap><div align='left'>Target Date</div></td>	
		  <td width='10%' nowrap><div align='left'>Target Qty</div></td>
		</tr>";
		
		$sl=1;
		$getSql		= "SELECT st.id,dv.division_name_eng as division_name,c.catagory_name,p.product_name,p.m_unit,d.district_name,a.area_name,DATE_FORMAT(st.target_from,'%d %b %y' ) as target_from,DATE_FORMAT(st.target_to,'%d %b %y' ) as target_to,st.target_qty FROM ".SALES_TARGET_CATAGORY_TBL." as st,".PRODUCT_TBL." as p,".CATAGORY_TBL." as c,".DIVISION_TBL." as dv,".DISTRICT_TBL." as d,".AREA_TBL." as a WHERE st.product_id=p.product_id AND p.catagory=c.catagory_code AND st.division_id=dv.division_id AND st.district_id=d.district_id AND d.district_id=a.district AND st.area_id=a.area_id AND st.project_id='".$project_id."'";
		
		if($date_from !=""){
			$getSql		.=" AND st.target_from >= '".$copy_date_from."'";
		}
		if($date_to !=""){
			$getSql		.=" AND st.target_to <= '".$copy_date_to."'";
		}
		
		$getSql	.=" GROUP BY st.id ORDER BY g.`division_name_eng`,d.district_name,p.`product_name` ASC";
		
		$gres 		= mysql_query($getSql);
		while($row = mysql_fetch_array($gres)){
		extract($row);		
		$str2.="
		<tr style='color:#000000' bgcolor='#fff'>
		  <td width='2%' nowrap align='left'>$sl</td>
		  <td width='15%' nowrap align='left'>$catagory_name</td>
		  <td width='15%' nowrap align='left'>$division_name</td>
		  <td width='10%' nowrap align='left'>$district_name</td>
		  <td width='10%' nowrap align='left'>$area_name</td>
		  <td width='20%' nowrap align='left'>$product_name</td>
		  <td width='12%' nowrap align='left'>$target_from to $target_to</td>
		  <td width='10%' nowrap align='left'>$target_qty $m_unit</td>	  				  
		  
		</tr>";  $sl++;
		}
		
		echo $str1.$str2;
	}
	
	function delTempSales(){
		$tmp_id = $_REQUEST['id'];
		$date_from 		= getRequest('date_from');
		$date_to 		= getRequest('date_to');
		if($tmp_id!=""){
		 $dsql = "DELETE FROM ".SALES_TARGET_TBL." WHERE id ='".$tmp_id."'";
		 mysql_query($dsql);
		}		
		header("location:?app=sales_target&cmd=add&date_from=$date_from&date_to=$date_to");
	}
	

	function getGroupProductGride(){
		$project_id  	= getFromSession('project_id');
		$date_from 	= formatDate(getRequest('date_from'));
		$date_to 	= formatDate(getRequest('date_to'));
		$catagory 	= getRequest('catagory');		
		
		$str1="<table width='100%' align='center' bgcolor='#99CC66'>
		<tr style='color:#fff;' bgcolor='#007DFB' height=28>
		  <td width='2%' nowrap><div align='left'>SL</div></td>
		  <td width='15%' nowrap><div align='left'>Catagory</div></td>
		  <td width='15%' nowrap><div align='left'>Division Name</div></td>
		  <td width='10%' nowrap><div align='left'>Area Name</div></td>
		  <td width='10%' nowrap><div align='left'>TRT Name</div></td>
		  <td width='20%' nowrap><div align='left'>Product Name</div></td>
		  <td width='12%' nowrap><div align='left'>Target Date</div></td>	
		  <td width='10%' nowrap><div align='left'>Target Qty</div></td>
		  <td width='6%' nowrap align='center'>Option</td>
		</tr>";
		
		$sl=1;
		$getSql		= "SELECT st.id,dv.division_name_eng as division_name,c.catagory_name,p.product_name,p.m_unit,d.district_name,a.area_name,DATE_FORMAT(st.target_from,'%d %b %y' ) as target_from,DATE_FORMAT(st.target_to,'%d %b %y' ) as target_to,st.target_qty FROM ".SALES_TARGET_TBL." as st,".PRODUCT_TBL." as p,".CATAGORY_TBL." as c,".DIVISION_TBL." as dv,".DISTRICT_TBL." as d,".AREA_TBL." as a WHERE st.product_id=p.product_id AND p.catagory=c.catagory_code AND st.division_id=dv.division_id AND st.district_id=d.district_id AND d.district_id=a.district AND st.area_id=a.area_id AND st.project_id='".$project_id."'";
		if($date_from !=""){
			$getSql		.=" AND st.target_from >= '".$date_from."'";
		}
		if($date_to !=""){
			$getSql		.=" AND st.target_to <= '".$date_to."'";
		}
		if($catagory !=""){
			$getSql		.=" AND st.catagory_id = '".$catagory."'";
		}
		$getSql		.=" GROUP BY st.id ORDER BY g.`division_name_eng`,d.district_name,p.`product_name` ASC";
		$gres 		= mysql_query($getSql);
		while($row = mysql_fetch_array($gres)){
		extract($row);		
		$str2.="
		<tr style='color:#000000' bgcolor='#fff'>
		  <td width='2%' nowrap align='left'>$sl</td>
		  <td width='15%' nowrap align='left'>$catagory_name</td>
		  <td width='15%' nowrap align='left'>$division_name</td>
		  <td width='10%' nowrap align='left'>$district_name</td>
		  <td width='10%' nowrap align='left'>$area_name</td>
		  <td width='20%' nowrap align='left'>$product_name</td>
		  <td width='12%' nowrap align='left'>$target_from to $target_to</td>
		  <td width='10%' nowrap align='left'>$target_qty $m_unit</td>			  				  
		  <td width='6%' nowrap align='center'><a href=\"?app=sales_target&cmd=deltemp&id=$id\"><img src=\"images/common/icons/delete.gif\"></a></td>
		</tr>";  $sl++;
		}
		
		echo $str1.$str2;
	}
	function getTempSales(){
		$project_id  	= getFromSession('project_id');
		$catagory 	= getRequest('catagory');
		$str1="<table width='100%' align='center' bgcolor='#99CC66'>
		<tr style='color:#fff;' bgcolor='#007DFB' height=28>
		  <td width='2%' nowrap><div align='left'>SL</div></td>
		  <td width='15%' nowrap><div align='left'>Catagory</div></td>
		  <td width='15%' nowrap><div align='left'>Division Name</div></td>
		  <td width='10%' nowrap><div align='left'>Area Name</div></td>
		  <td width='10%' nowrap><div align='left'>TRT Name</div></td>
		  <td width='20%' nowrap><div align='left'>Product Name</div></td>
		  <td width='12%' nowrap><div align='left'>Target Date</div></td>	
		  <td width='10%' nowrap><div align='left'>Target Qty</div></td>
		</tr>";
		
		$sl=1;
		$getSql		= "SELECT st.id,dv.division_name_eng as division_name,c.catagory_name,p.product_name,p.m_unit,d.district_name,a.area_name,DATE_FORMAT(st.target_from,'%d %b %y' ) as target_from,DATE_FORMAT(st.target_to,'%d %b %y' ) as target_to,st.target_qty FROM ".SALES_TARGET_TBL." as st,".PRODUCT_TBL." as p,".CATAGORY_TBL." as c,".DIVISION_TBL." as dv,".DISTRICT_TBL." as d,".AREA_TBL." as a WHERE st.product_id=p.product_id AND p.catagory=c.catagory_code AND st.division_id=dv.division_id AND st.district_id=d.district_id AND d.district_id=a.district AND st.area_id=a.area_id AND st.project_id='".$project_id."'";
		if($date_from !=""){
			$getSql		.=" AND st.target_from >= '".$date_from."'";
		}
		if($date_to !=""){
			$getSql		.=" AND st.target_to <= '".$date_to."'";
		}
		if($catagory !=""){
			$getSql		.=" AND st.catagory_id = '".$catagory."'";
		}
		$getSql		.=" GROUP BY st.id ORDER BY dv.`division_name_eng`,d.district_name,p.`product_name` ASC";
		$gres 		= mysql_query($getSql);
		while($row = mysql_fetch_array($gres)){
		extract($row);		
		$str2.="
		<tr style='color:#000000' bgcolor='#fff'>
		  <td width='2%' nowrap align='left'>$sl</td>
		  <td width='15%' nowrap align='left'>$catagory_name</td>
		  <td width='15%' nowrap align='left'>$division_name</td>
		  <td width='10%' nowrap align='left'>$district_name</td>
		  <td width='10%' nowrap align='left'>$area_name</td>
		  <td width='20%' nowrap align='left'>$product_name</td>
		  <td width='12%' nowrap align='left'>$target_from to $target_to</td>
		  <td width='10%' nowrap align='left'>$target_qty $m_unit</td>			  				  
		  <td width='6%' nowrap align='center'><a href=\"?app=sales_target&cmd=deltemp&id=$id\"><img src=\"images/common/icons/delete.gif\"></a></td>
		</tr>";  $sl++;
		}
		
		return $str1.$str2;	
		
	}
  //====== End Save Sales =====


  function getCatagorySalesTargetGride(){
	$project_id  	= getFromSession('project_id');
	$date_from 	= formatDate(getRequest('date_from'));
	$date_to 	= formatDate(getRequest('date_to'));
	$catagory 	= getRequest('catagory');

	$str1="<table width='100%' align='center' bgcolor='#99CC66'>
		<tr style='color:#fff;' bgcolor='#007DFB' height=28>
		  <td width='2%' nowrap><div align='left'>SL</div></td>
		  <td width='12%' nowrap><div align='left'>Category</div></td>
		  <td width='28%' nowrap><div align='left'>Product Name</div></td>
		  <td width='10%' nowrap><div align='left'>Division Name</div></td>
		  <td width='10%' nowrap><div align='left'>Area Name</div></td>
		  <td width='14%' nowrap><div align='left'>TRT Name</div></td>
		  <td width='12%' nowrap><div align='left'>Target Date</div></td>	
		  <td width='6%' nowrap><div align='left'>Target Qty</div></td>
		  <td width='6%' nowrap align='center'>Option</td>
		</tr>";
		
		$sl=1;
		$getSql = "SELECT st.id,dv.division_name_eng as division_name,c.catagory_name,d.district_name,a.area_name,DATE_FORMAT(st.target_from,'%d %b %y' ) as target_from,DATE_FORMAT(st.target_to,'%d %b %y' ) as target_to,st.target_qty FROM ".SALES_TARGET_CATAGORY_TBL." as st,".CATAGORY_TBL." as c,".DIVISION_TBL." as dv,".DISTRICT_TBL." as d,".AREA_TBL." as a WHERE st.catagory_id=c.catagory_code AND st.division_id = dv.division_id AND st.district_id=d.district_id AND d.district_id=a.district AND st.area_id=a.area_id AND st.project_id='".$project_id."'";
		
		if($date_from !=""){
			$getSql		.=" AND st.target_from >= '".$date_from."'";
		}
		if($date_to !=""){
			$getSql		.=" AND st.target_to <= '".$date_to."'";
		}
		if($catagory !=""){
			$getSql		.=" AND st.catagory_id = '".$catagory."'";
		}
		$getSql	.=" GROUP BY st.id ORDER BY dv.`division_name_eng`,d.district_name,c.catagory_name ASC";
		
		$gres 		= mysql_query($getSql);
		while($row = mysql_fetch_array($gres)){
		extract($row);
		if($product!=""){
		  $acsql= "SELECT * FROM ".PRODUCT_TBL." WHERE product_id  = '".$product."' AND project_id = '$project_id'";
		  $acres = mysql_query($acsql);
		  $acnum = mysql_num_rows($acres);
		  if($acnum >0){
			$prow = mysql_fetch_object($acres);
			$product_name = $prow->product_name;
			$product = "";
		  }else{ $product_name="";}
		}		
		$str2.="
		<tr style='color:#000000' bgcolor='#fff'>
		  <td width='2%' nowrap align='left'>$sl</td>
		  <td width='12%' nowrap align='left'>$catagory_name</td>
		  <td width='28%' nowrap align='left'></td>
		  <td width='10%' nowrap align='left'>$division_name</td>
		  <td width='10%' nowrap align='left'>$district_name</td>
		  <td width='14%' nowrap align='left'>$area_name</td>
		  <td width='6%' nowrap align='left'>$target_from to $target_to</td>
		  <td width='8%' nowrap align='left'>$target_qty</td>		  				  
		  <td width='8%' nowrap align='center'><a href=\"?app=sales_target&cmd=delcst&id=$id&date_from=$date_from&date_to=$date_to\"><img src=\"images/common/icons/delete.gif\"></a></td>
		</tr>";  $sl++; $product = ""; $product_name="";
		}
		
		echo $str1.$str2;

 }  


function getAmountSalesTargetGride($ajax = true){
	if($ajax){
		if (ob_get_level()) ob_end_clean();
        	ob_start(); // Start buffering
	}

	$project_id  	= getFromSession('project_id');

	$date_from = !empty(getRequest('date_from')) ? formatDate(getRequest('date_from')) : date("Y-m-d");
	$date_to   = !empty(getRequest('date_to'))   ? formatDate(getRequest('date_to'))   : date("Y-m-d");

	$division_id 	= getRequest('division_id');
	$district_id 	= getRequest('district');
	$area_id 	= getRequest('area');

	$str1="<table width='100%' align='center' bgcolor='#99CC66'>
		<tr style='color:#fff;' bgcolor='#007DFB' height=28>
		  <td width='2%' nowrap><div align='left'>SL</div></td>
		  <td width='10%' nowrap><div align='left'>Division Name</div></td>
		  <td width='10%' nowrap><div align='left'>Area Name</div></td>
		  <td width='14%' nowrap><div align='left'>TRT Name</div></td>
		  <td width='12%' nowrap><div align='left'>Target Date</div></td>	
		  <td width='6%' nowrap><div align='left'>Target Amount</div></td>";

		if(getFromSession('u_type_id')==101){
		  $str1.="<td width='6%' nowrap align='center'>Option</td>";
		}

	$str1.="</tr>";
		
		$sl=1;

	$getSql = "SELECT st.id,dv.division_name_eng AS division_name,d.district_name,a.area_name, DATE_FORMAT(st.target_from, '%d %b %y') AS target_from, DATE_FORMAT(st.target_to, '%d %b %y') AS target_to, st.target_amount FROM area_sales_target AS st LEFT JOIN ".DIVISION_TBL." AS dv ON st.division_id = dv.division_id LEFT JOIN ".DISTRICT_TBL." AS d ON st.district_id = d.district_id LEFT JOIN ".AREA_TBL." AS a ON st.area_id = a.area_id WHERE st.project_id = '".$project_id."'";

		    if ($date_from != "" && $date_to == "") {
			$getSql .= " AND st.target_from >= '" . mysql_real_escape_string($date_from) . "'";
		    } elseif ($date_from == "" && $date_to != "") {
			$getSql .= " AND st.target_to <= '" . mysql_real_escape_string($to_date) . "'";
		    } elseif ($date_from != "" && $date_to != "") {
			$getSql .= " AND st.target_from >= '" . mysql_real_escape_string($date_from) . "'";
			$getSql .= " AND st.target_to <= '" . mysql_real_escape_string($date_to) . "'";
		    }

		
		if($division_id !=""){
			$getSql		.=" AND st.division_id = '".$division_id."'";
		}
		if($district_id !=""){
			$getSql		.=" AND st.district_id = '".$district_id."'";
		}
		if($area_id !=""){
			$getSql		.=" AND st.area_id = '".$area_id."'";
		}

		$getSql	.=" GROUP BY st.id ORDER BY dv.`division_name_eng`,d.district_name ASC";

		$gres 		= mysql_query($getSql);

$date_from = dateInputFormatDMY($date_from);
$date_to = dateInputFormatDMY($date_to);
$str2 ="" ;
		while($row = mysql_fetch_array($gres)){	
		extract($row);
		$str2.="
		<tr style='color:#000000' bgcolor='#fff'>
		  <td width='2%' nowrap align='left'>$sl</td>
		  <td width='10%' nowrap align='left'>$division_name</td>
		  <td width='10%' nowrap align='left'>$district_name</td>
		  <td width='14%' nowrap align='left'>$area_name</td>
		  <td width='6%' nowrap align='left'>$target_from to $target_to</td>
		  <td width='8%' nowrap align='left'>$target_amount</td>";		  			if(getFromSession('u_type_id')==101){  
		  $str2.="<td width='8%' nowrap align='center'><a href=\"?app=sales_target&cmd=delast&id=$id&date_from=$date_from&date_to=$date_to&division_id=$division_id&district=$district_id&area=$area_id\"><img src=\"images/common/icons/delete.gif\"></a></td>";
		}

		$str2.="</tr>";  $sl++;
		}

	if($ajax){
		header('Content-Type: application/json');
		echo json_encode($str1.$str2);
		exit();
	}else{
		return $str1.$str2;
	}
		

 } 


  function getCatagorySalesTarget(){
	$project_id  	= getFromSession('project_id');
	$date_from 	= formatDate(getRequest('date_from'));
	$date_to 	= formatDate(getRequest('date_to'));
	$catagory 	= getRequest('catagory');

	$str1="<table width='100%' align='center' bgcolor='#99CC66'>
		<tr style='color:#fff;' bgcolor='#007DFB' height=28>
		  <td width='2%' nowrap><div align='left'>SL</div></td>
		  <td width='12%' nowrap><div align='left'>Category</div></td>
		  <td width='24%' nowrap><div align='left'>Product Name</div></td>
		  <td width='10%' nowrap><div align='left'>Division Name</div></td>
		  <td width='10%' nowrap><div align='left'>Area Name</div></td>
		  <td width='14%' nowrap><div align='left'>TRT Name</div></td>
		  <td width='12%' nowrap><div align='left'>Target Date</div></td>	
		  <td width='6%' nowrap><div align='left'>Target Qty</div></td>	
		  <td width='6%' nowrap><div align='left'>Target Value</div></td>
		  <td width='4%' nowrap align='center'>Option</td>
		</tr>";
		$sl=1;
		$getSql = "SELECT st.id,dv.division_name_eng as division_name,c.catagory_name,st.product,d.district_name,a.area_name,DATE_FORMAT(st.target_from,'%d %b %y' ) as target_from,DATE_FORMAT(st.target_to,'%d %b %y' ) as target_to,st.target_qty,st.target_amount FROM ".SALES_TARGET_CATAGORY_TBL." as st,".CATAGORY_TBL." as c,".DIVISION_TBL." as dv,".DISTRICT_TBL." as d,".AREA_TBL." as a WHERE st.catagory_id=c.catagory_code AND st.division_id = dv.division_id AND st.district_id=d.district_id AND d.district_id=a.district AND st.area_id=a.area_id AND st.project_id='".$project_id."'";
		
		if($date_from !=""){
			$getSql		.=" AND st.target_from >= '".$date_from."'";
		}
		if($date_to !=""){
			$getSql		.=" AND st.target_to <= '".$date_to."'";
		}
		if($catagory !=""){
			$getSql		.=" AND st.catagory_id = '".$catagory."'";
		}
		$getSql	.=" GROUP BY st.id ORDER BY dv.`division_name_eng`,d.district_name,c.catagory_name ASC";
		
		$gres 		= mysql_query($getSql);
		while($row = mysql_fetch_array($gres)){
		extract($row);
		if($product!=""){
		  $acsql= "SELECT * FROM ".PRODUCT_TBL." WHERE product_id  = '".$product."' AND project_id = '$project_id'";
		  $acres = mysql_query($acsql);
		  $acnum = mysql_num_rows($acres);
		  if($acnum >0){
			$prow = mysql_fetch_object($acres);
			$product_name = $prow->product_name;
			$product = "";
		  }else{ $product_name="";}
		}
				
		$str2.="
		<tr style='color:#000000' bgcolor='#fff'>
		  <td width='2%' nowrap align='left'>$sl</td>
		  <td width='12%' nowrap align='left'>$catagory_name</td>
		  <td width='24%' nowrap align='left'>$product_name</td>
		  <td width='10%' nowrap align='left'>$division_name</td>
		  <td width='10%' nowrap align='left'>$district_name</td>
		  <td width='14%' nowrap align='left'>$area_name</td>
		  <td width='12%' nowrap align='left'>$target_from to $target_to</td>
		  <td width='6%' nowrap align='left'>$target_qty $m_unit</td>
		  <td width='6%' nowrap align='left'>$target_amount</td>			  				  
		  <td width='4%' nowrap align='center'><a href=\"?app=sales_target&cmd=delcst&id=$id&date_from=$date_from&date_to=$date_to\"><img src=\"images/common/icons/delete.gif\"></a></td>
		</tr>";  $sl++; $product = ""; $product_name="";
		}
		
		return $str1.$str2;

 } 

 function delCatagorySalesTarget(){
	$tmp_id = $_REQUEST['id'];
	$date_from 		= getRequest('date_from');
	$date_to 		= getRequest('date_to');
	if($tmp_id!=""){
	 $dsql = "DELETE FROM ".SALES_TARGET_CATAGORY_TBL." WHERE id ='".$tmp_id."'";
	 mysql_query($dsql);
	}		
	header("location:?app=sales_target&cmd=catagory_tagget&date_from=$date_from&date_to=$date_to");
 }

 function delAmountSalesTarget(){
	$id 		= getRequest('id');
	$date_from 	= getRequest('date_from');
	$date_to 	= getRequest('date_to');

	$division_id 	= getRequest('division_id');
	$district_id 	= getRequest('district');
	$area_id 	= getRequest('area');

	if($id!=""){
	 $dsql = "DELETE FROM area_sales_target WHERE id ='$id'";
	 mysql_query($dsql);
	}		
	header("location:?app=sales_target&cmd=amount_target&date_from=$date_from&date_to=$date_to&division_id=$division_id&district=$district_id&area=$area_id");
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
	function loadProduct4Group($group_id)
	{		  
	  	  $project_id = getFromSession('project_id');  
		  $info            = array();
		  $info['table']   = PRODUCT_TBL.' as p,'.GROUP_WISE_PRODUCT_TBL.' gp';
		  $info['fields']  =  array('p.product_id','p.product_name','p.product_desc');
		  $info['where']   = "p.product_id = gp.product_id AND gp.group_id='$group_id' AND p.project_id = '$project_id'";
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
			 $subject_idname .= $v[0]->product_id.'#####'.$v[0]->product_name.'#####'.$v[0]->product_desc.'@@@';
		  }
		  echo $subject_idname;	
  }
  function loadCatagory4Group($group_id)
  {		  
	  	  $project_id = getFromSession('project_id');  
		  $info            = array();
		  $info['table']   = CATAGORY_TBL.' as c,'.GROUP_WISE_PRODUCT_TBL.' gp';
		  $info['fields']  =  array('c.catagory_code','c.catagory_name');
		  $info['where']   = "c.catagory_code = gp.catagory_id AND gp.group_id='$group_id' AND c.project_id = '$project_id'";
		  $info['groupby'] = array("c.catagory_code");
		  //$info['debug']   = true;	
		  $result          = select($info);
		  $data            = array();	
		  if(count($result)){
			 foreach($result as $key=>$value){
				$data[$key][]        = $value;
			 }
		  }
		  foreach($data as $i=>$v){
			 $subject_idname .= $v[0]->catagory_code.'#####'.$v[0]->catagory_name.'@@@';
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
