<?
//****************************************
   //============ Start MGBD ================
   function saveMGBDSizeDetail($order_ref_no)
   {       
	   $totalsize = getRequest('totalsize');
	   $gbdmeasurement_unit = getRequest('gbdmeasurement_unit');
	   for($x=0; $x<$totalsize; $x++)
		{
			 $size_ratio.= getRequest('size_ratio'.$x).',';
			 $subtotalsize.= getRequest('subtotalsize'.$x).',';
		}
		$gbd_sizeratio 	 		= substr($size_ratio, 0, -1);
		$gbd_total_sizeqty 	 	= substr($subtotalsize, 0, -1);
		$gbd_total_sizeratio 	= getRequest('totalsize_ratio');		
		$total_color_ratio 	= getRequest('totalcolor_qty');
		$sql = "UPDATE ".BUYER_ORDER_TBL." SET gbdmeasurement_unit = '$gbdmeasurement_unit', gbd_sizeratio ='$gbd_sizeratio', gbd_total_sizeratio = '$gbd_total_sizeratio', total_color_ratio='$total_color_ratio', gbd_total_sizeqty = '$gbd_total_sizeqty' WHERE  order_ref_no = '$order_ref_no'";
   		mysql_query($sql);
   }
   
   function saveMGBDColorDetail($order_ref_no)
   {   
   		$sql4del = "DELETE FROM ".GBD_COLOR_DETAIL_TBL." WHERE order_ref_no = '$order_ref_no'";
		mysql_query($sql4del);		
			     
		$totalsize 			= getRequest('totalsize');
		$color_qty 			= getRequest('color_qty');
		$created_by			= getFromSession('userid');
		$created_date       = date('Y-m-d h:i:s');
		for($y=0; $y<$color_qty; $y++)
		{
		    $size_qty="";
			$color_code 	= getRequest('color'.$y);			
			$color_ratio 	= getRequest('color_ratio'.$y);			
						
			if($totalsize>0 && $totalsize <=10){
			$size_qty.= getRequest('a'.$y).',';		
			}				
			if($totalsize>1 && $totalsize <=10){
				$size_qty.= getRequest('b'.$y).',';		
			}	
			if($totalsize>2 && $totalsize <=10){
				$size_qty.= getRequest('c'.$y).',';		
			}
			if($totalsize>3 && $totalsize <=10){
				$size_qty.= getRequest('d'.$y).',';		
			}
			if($totalsize>4 && $totalsize <=10){
				$size_qty.= getRequest('e'.$y).',';	
			}
			if($totalsize>5 && $totalsize <=10){
				$size_qty.= getRequest('f'.$y).',';		
			}
			if($totalsize>6 && $totalsize <=10){
				$size_qty.= getRequest('g'.$y).',';		
			}
			if($totalsize>7 && $totalsize <=10){
				$size_qty.= getRequest('h'.$y).',';		
			}
			if($totalsize>8 && $totalsize <=10){
				$size_qty.= getRequest('i'.$y).',';		
			}			
			if($totalsize>9 && $totalsize <=10){
				$size_qty.= getRequest('j'.$y).',';		
			}
			$size_qty 			= substr($size_qty, 0, -1);			
			$total_colorqty 	= getRequest('totalcolorqty'.$y);
			
			$sql = "INSERT INTO ".GBD_COLOR_DETAIL_TBL." (`order_ref_no`, `color_code`, `color_ratio`, `size_qty`, `total_colorqty`, `created_by`, `created_date`) VALUES ('$order_ref_no', '$color_code', '$color_ratio', '$size_qty', '$total_colorqty', '$created_by', '$created_date')";
			mysql_query($sql);
			
		}//end for        
   				
 }
 
 //====================Start MFBD =============================
 
  function saveMFBDMaster($order_ref_no)
  {  			   
	   $totalsize 		= getRequest('totalsize');
	   $fabrics_code 	= getRequest('fbdfabrics_code');
	   $supplier_code 	= getRequest('fabrics_supplier');
	   $m_unit		  	= getRequest('fbdunit');
	   $width_type 		= getRequest('fbdwidth_type');
	   $unit_price 		= getRequest('fab_uprice');
	   $total_qty 		= getRequest('fbdtotalqty');
	   $total_value 	= getRequest('fabtotal_value');  
	   
	   $created_by		= getFromSession('userid');
	   $created_date    = date('Y-m-d h:i:s');
	   
	   for($x=0; $x<$totalsize; $x++)
		{
			 $size_con.= getRequest('size_con'.$x).',';
			 $subtotal_consumtion.= getRequest('subtotal_consumtion'.$x).',';
			 $width_value.= getRequest('fbdwidth_val'.$x).',';			 
		}
		$size_consumption	= substr($size_con, 0, -1);
		$sub_sizeqty 	 	= substr($subtotal_consumtion, 0, -1);
		$width_value 	 	= substr($width_value, 0, -1);	
		$fabrics_order_no = $order_ref_no.$fabrics_code;
		$sql4delfbd = "DELETE FROM ".SHOW_FBD_MASTER_TBL." WHERE order_ref_no = '$order_ref_no' AND fabrics_code ='$fabrics_code'";
		mysql_query($sql4delfbd);	
		
		$sql = "INSERT INTO ".SHOW_FBD_MASTER_TBL." (`fabrics_order_no`,`order_ref_no`, `fabrics_code`, `supplier_code`, `m_unit`, `width_type`, `width_value`, `size_consumption`, `sub_sizeqty`,`unit_price`, `total_qty`, `total_value`, `created_by`, `created_date`) VALUES ('$fabrics_order_no', '$order_ref_no', '$fabrics_code', '$supplier_code', '$m_unit', '$width_type', '$width_value', '$size_consumption', '$sub_sizeqty', '$unit_price', '$total_qty', '$total_value', '$created_by', '$created_date')";
   		mysql_query($sql);
   }
   
  function saveMYarnBDMaster($order_ref_no)
  {       
	   $totalsize 		= getRequest('totalsize');
	   $yearn_code 		= getRequest('fbdyearn_code');
	   $supplier_code 	= getRequest('yern_supplier');
	   $m_unit		  	= getRequest('fbdunit');
	   $unit_price 		= getRequest('yern_uprice');
	   $total_qty 		= getRequest('fbdtotalqty');
	   $total_value 	= getRequest('yerntotal_value'); 
	   
	   $created_by		= getFromSession('userid');
	   $created_date    = date('Y-m-d h:i:s');
		
	   for($x=0; $x<$totalsize; $x++)
		{
			 $size_con.= getRequest('size_con'.$x).',';
			 $subtotal_consumtion.= getRequest('subtotal_consumtion'.$x).',';
		}
		$size_consumption	= substr($size_con, 0, -1);
		$sub_sizeqty 	 	= substr($subtotal_consumtion, 0, -1);
		$yearn_order_no = $order_ref_no.$yearn_code;
		$sql4delfbd = "DELETE FROM ".SHOW_YBD_MASTER_TBL." WHERE order_ref_no = '$order_ref_no' AND yearn_code ='$yearn_code'";
		mysql_query($sql4delfbd);	
		
		$sql = "INSERT INTO ".SHOW_YBD_MASTER_TBL." (`yearn_order_no`,`order_ref_no`, `yearn_code`, `supplier_code`, `m_unit`, `size_consumption`, `sub_sizeqty`,`unit_price`, `total_qty`, `total_value`, `created_by`, `created_date`) VALUES ('$yearn_order_no', '$order_ref_no', '$yearn_code', '$supplier_code', '$m_unit', '$size_consumption', '$sub_sizeqty', '$unit_price', '$total_qty', '$total_value', '$created_by', '$created_date')";
   		mysql_query($sql);
   }
   
   //==========Description: if select fabrics SAVE at show_fbd_detail and cal_fbd_detail tbl else if select Yarn than SAVE at show_yarnbd_detail and cal_yarnbd_detail tbl =============
   function saveMFBDDetail($order_ref_no)
   {      				
		$fbdfabrics_code 	= getRequest('fbdfabrics_code');
		$fbdyearn_code 		= getRequest('fbdyearn_code');	
		$m_unit		  		= getRequest('fbdunit');
			
		if($fbdfabrics_code!=""){
			$sql4delfbd = "DELETE FROM ".SHOW_FBD_DETAIL_TBL." WHERE order_ref_no = '$order_ref_no' AND fabrics_code ='$fbdfabrics_code'";
			mysql_query($sql4delfbd);	
			$calsql4delfbd = "DELETE FROM ".CAL_FBD_DETAIL_TBL." WHERE order_ref_no = '$order_ref_no' AND fabrics_code ='$fbdfabrics_code'";
			mysql_query($calsql4delfbd);			
	    	$supplier_code 	= getRequest('fabrics_supplier');
	    	$width_type 		= getRequest('fbdwidth_type');
		}else if($fbdyearn_code!=""){
			$sql4delybd = "DELETE FROM ".SHOW_YARN_DETAIL_TBL." WHERE order_ref_no = '$order_ref_no' AND yearn_code ='$fbdyearn_code'";
			mysql_query($sql4delybd);
			$calsql4delybd = "DELETE FROM ".CAL_YBD_DETAIL_TBL." WHERE order_ref_no = '$order_ref_no' AND yearn_code ='$fbdyearn_code'";
			mysql_query($calsql4delybd);	
			$supplier_code 	= getRequest('yern_supplier');
		}
					        
		$totalsize 			= getRequest('totalsize');
		$color_qty 			= getRequest('color_qty');
		$created_by			= getFromSession('userid');
		$created_date       = date('Y-m-d h:i:s');
		for($y=0; $y<$color_qty; $y++)
		{
		    $size_qty		="";
			$width_value 	="";
			$size_name		="";
			$color_code 	= getRequest('color'.$y);		
						
			if($totalsize>0 && $totalsize <=10){
				$size_qty.= getRequest('aa'.$y).',';	
				$width_value.= getRequest('fbdwidth_val0').'#';			
				$size_name.= getRequest('size_name0').'#';						
			}				
			if($totalsize>1 && $totalsize <=10){
				$size_qty.= getRequest('bb'.$y).',';				
				$width_value.= getRequest('fbdwidth_val1').'#';		
				$size_name.= getRequest('size_name1').'#';						
			}	
			if($totalsize>2 && $totalsize <=10){
				$size_qty.= getRequest('cc'.$y).',';				
				$width_value.= getRequest('fbdwidth_val2').'#';		
				$size_name.= getRequest('size_name2').'#';						
			}
			if($totalsize>3 && $totalsize <=10){
				$size_qty.= getRequest('dd'.$y).',';						
				$width_value.= getRequest('fbdwidth_val3').'#';		
				$size_name.= getRequest('size_name3').'#';						
			}
			if($totalsize>4 && $totalsize <=10){
				$size_qty.= getRequest('ee'.$y).',';					
				$width_value.= getRequest('fbdwidth_val4').'#';		
				$size_name.= getRequest('size_name4').'#';						
			}
			if($totalsize>5 && $totalsize <=10){
				$size_qty.= getRequest('ff'.$y).',';						
				$width_value.= getRequest('fbdwidth_val5').'#';		
				$size_name.= getRequest('size_name5').'#';							
			}
			if($totalsize>6 && $totalsize <=10){
				$size_qty.= getRequest('gg'.$y).',';							
				$width_value.= getRequest('fbdwidth_val6').'#';		
				$size_name.= getRequest('size_name6').'#';							
			}
			if($totalsize>7 && $totalsize <=10){
				$size_qty.= getRequest('hh'.$y).',';						
				$width_value.= getRequest('fbdwidth_val7').'#';			
				$size_name.= getRequest('size_name7').'#';						
			}
			if($totalsize>8 && $totalsize <=10){
				$size_qty.= getRequest('ii'.$y).',';						
				$width_value.= getRequest('fbdwidth_val8').'#';		
				$size_name.= getRequest('size_name8').'#';							
			}			
			if($totalsize>9 && $totalsize <=10){
				$size_qty.= getRequest('jj'.$y).',';					
				$width_value.= getRequest('fbdwidth_val9').'#';		
				$size_name.= getRequest('size_name9').'#';							
			}
			$fab_sizeqty 			= substr($size_qty, 0, -1);	
			$width_value 			= substr($width_value, 0, -1);	
			$size_name 				= substr($size_name, 0, -1);			
			$fabtotal_color_qty 	= getRequest('fbdtotalcolorqty'.$y);
			if($fbdfabrics_code!=""){		
	    		$sql = "INSERT INTO ".SHOW_FBD_DETAIL_TBL." (`order_ref_no`, `fabrics_code`,`m_unit`,`width_type`,`color_code`, `fab_sizeqty`, `fabtotal_color_qty`, `created_by`, `created_date`) VALUES ('$order_ref_no', '$fbdfabrics_code','$m_unit','$width_type','$color_code', '$fab_sizeqty', '$fabtotal_color_qty', '$created_by', '$created_date')";
			}else if($fbdyearn_code!=""){
				$sql = "INSERT INTO ".SHOW_YARN_DETAIL_TBL." (`order_ref_no`, `yearn_code`,`m_unit`,`width_type`,`color_code`, `yarn_sizeqty`, `ytotal_color_qty`, `created_by`, `created_date`) VALUES ('$order_ref_no', '$fbdyearn_code','$m_unit','$width_type','$color_code', '$fab_sizeqty', '$fabtotal_color_qty', '$created_by', '$created_date')";
			}
			mysql_query($sql);
			
			$widthValueArr  = explode("#",$width_value);
			$sizeQtyArr  	= explode(",",$size_qty);
			$sizeNameArr  	= explode("#",$size_name);
			for($x=0; $x<count($widthValueArr); $x++) {				
				if($fbdfabrics_code!=""){
					$CALSQL = "INSERT INTO ".CAL_FBD_DETAIL_TBL." (`order_ref_no`, `fabrics_code`,`color_code`,`size_name`, `m_unit`,`width_type`,`width_value`,`size_qty`, `created_by`, `created_date`) VALUES ('$order_ref_no', '$fbdfabrics_code', '$color_code','".$sizeNameArr[$x]."','$m_unit','$width_type','".$widthValueArr[$x]."','".$sizeQtyArr[$x]."', '$created_by', '$created_date')";
			    }else if($fbdyearn_code!=""){
					$CALSQL = "INSERT INTO ".CAL_YBD_DETAIL_TBL." (`order_ref_no`, `yearn_code`,`color_code`,`size_name`, `m_unit`,`width_type`,`size_qty`, `created_by`, `created_date`) VALUES ('$order_ref_no', '$fbdyearn_code', '$color_code','".$sizeNameArr[$x]."','$m_unit','$width_type','".$sizeQtyArr[$x]."', '$created_by', '$created_date')";
				}
				mysql_query($CALSQL);							
			}			
		}//end for   				
 }
 //============ End MFBD ==============
 
 //======= Start Trim Backdown ========
 function insertTrimBD()
 {     
 	  $order_ref_no 	= getRequest('order_ref_no');
	  $sample_code 		= getRequest('sample_code');	
	  $tbdtrim_code 	= getRequest('tbdtrim_code');	
		  
	  $requestdata 						= array();
      $requestdata 						= getUserDataSet(TRIM_BRACKDOWN_TBL);      
	  $requestdata['created_by']        = getFromSession('userid');
	  $requestdata['created_date']      = date('Y-m-d h:i:s');	     
	  $requestdata['modified_by']       = getFromSession('userid');
	  $requestdata['modified_time']     = date('Y-m-d h:i:s');	                
      $requestdata['trim_order_no']   	= $order_ref_no.$tbdtrim_code;     
	  
   	  $info        						=  array();
      $info['table']					= TRIM_BRACKDOWN_TBL; 
	  $info['data'] 					= $requestdata;  
	  $info['debug']  					=  true;  
	  $res = insert($info); 
	  
      if($res)
      {
        header("location:?app=marchandising&cmd=tbd&order_ref_no=".$order_ref_no."&sample_code=".$sample_code);
      }else{
	  	header("location:?app=marchandising&cmd=tbd&order_ref_no=".$order_ref_no."&sample_code=".$sample_code);
	  }       

   }//EOFn   
   
 function updateTrimBD()
 {     
 	  $order_ref_no 	= getRequest('order_ref_no');
 	  $trim_order_no 	= getRequest('trim_order_no');
	  $sample_code 		= getRequest('sample_code');		
   	  $requestdata = array();
      $requestdata = getUserDataSet(TRIM_BRACKDOWN_TBL); 
	  $requestdata['modified_by']       = getFromSession('userid');
	  $requestdata['modified_time']     = date('Y-m-d h:i:s');    
	  
   	  $info        						=  array();
      $info['table']					= TRIM_BRACKDOWN_TBL; 	
      //dBug($requestdata);
	  $info['data'] 					= $requestdata;
	  $info['where']					= "trim_order_no ='".$trim_order_no."'";  
	   $info['debug']  					=  false;    
	  $res = update($info);
	  
      if($res)
      {
        header("location:?app=marchandising&cmd=tbd&order_ref_no=".$order_ref_no."&sample_code=".$sample_code);
      }else{
	  	header("location:?app=marchandising&cmd=tbd&order_ref_no=".$order_ref_no."&sample_code=".$sample_code);
	  }       

   }//EOFn  

   function getTrimBDList($order_ref_no,$trim_order_no=null,$from=null,$to=null)
   {
	   if($from == "" && $to == ""){$from=0; $to=10;}  
	   $data            = array();	  
  	   $info            = array();
	   $info['table']   = TRIM_BRACKDOWN_TBL.' tbd,'.TRIM_TBL.' t,'.SUPPLIER_TBL.' s';	  
	   $info['fields'] = array('tbd.trim_order_no','tbd.order_ref_no','tbd.tbdtrim_code','t.trim_desc','tbd.trim_supplier','s.name','tbd.consumtion','tbd.total_qty','tbd.total_trim_qty','tbd.unit_price','tbd.total_value','tbd.remarks');    		
	   if($trim_order_no==""){
	  		$info['where']   = "tbd.tbdtrim_code=t.trim_code AND tbd.trim_supplier = s.supplier_code AND tbd.order_ref_no = '".$order_ref_no."'";
	   }else{
	  		$info['where']   = "tbd.tbdtrim_code=t.trim_code AND tbd.trim_supplier = s.supplier_code AND tbd.order_ref_no = '".$order_ref_no."' AND tbd.trim_order_no='".$trim_order_no."'";
	   }    
       $info['orderby'] = array("trim_order_no asc LIMIT $from,$to");
	   $info['debug']   = false;			 

	   $res            =	select($info);   

	   if(count($res))
	   {
		  foreach($res as $i=>$v)
		  {
			 $data[$i] = $v;
		  }
	   }
	   if($trim_order_no==""){
      	return $data; // for list
	  }else{
	  	return $data[0];	// for view
	  }

  }
 ?>