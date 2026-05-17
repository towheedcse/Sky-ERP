<?php
class QueeckQuotation 
{
   
   function run()
   {         

      $cmd = getRequest('cmd');
	  $u_t_id = getFromSession('u_type_id');
      if( ($u_t_id ==101) || ($u_t_id == 102) || ($u_t_id == 103) || ($u_t_id == 105) || ($u_t_id == 106)) // 101 = sysadmin, 102 = admin
      {

      	switch ($cmd)
      	{
      	   case 'add'					: $this->showEditor(); break;
		   case 'edit'					: $this->showEditor(); break;
		   case 'qqt_list'				: $this->showListEditor(); break;
      	   case 'loadProduct'  			: $this->loadProduct4Catagory(trim(getRequest('catagory_id'))); break; 
      	   case 'get_dtl'  				: $this->loadProductDtl(trim(getRequest('product_id'))); break;   
		   case 'save_qqt'				: $this->saveQueeckQuotation(); break; 
		   case 'print_vouchar'			: $screen = $this->showPrintEditor($msg); break;  
		   case 'delete'             	: $screen = $this->deleteRecord(getRequest('id')); break;
      	   default                   	: $cmd = 'list'; $screen = $this->showEditor();   break;

      	}

      }elseif($u_t_id == 101) // 101 = sysadmin, 102 = admin
      {

      	switch ($cmd)
      	{
      	   case 'add'					: $this->showEditor(); break;
		   case 'edit'					: $this->showEditor(); break;
		   case 'sal_dtl'				: $this->showListEditor(); break;
      	   case 'loadProduct'  			: $this->loadProduct4Catagory(trim(getRequest('catagory_id'))); break;  
      	   case 'get_dtl'  				: $this->loadProductDtl(trim(getRequest('product_id'))); break;  
		   case 'save_qqt'			: $this->saveQueeckQuotation(); break;
		   case 'print_vouchar'			: $screen = $this->showPrintEditor($msg); break;  
		   case 'delete'             	: $screen = $this->deleteRecord(getRequest('id')); break;
      	   default                   	:$cmd = 'list'; $screen = $this->showEditor();   break;

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
      
	  $quotation_no 	= getRequest('quotation_no');  
	  if ($quotation_no) {
         $advArr 					= $this->getQuotationMasterInfo($quotation_no);
         $advArr 					= parseThisValue($advArr); 
		 $data   					= array_merge(array(), $advArr); 
      
		 $data['item_list']	= $this->getProductList($quotation_no);
		 $data['message'] 	= $msg;
		 $data['cmd']     	= getRequest('cmd');
		 require_once(PRINT_SALES_QUOTATION_SKIN);      
		 return true;
	 }else{
		require_once(PRINT_VOUCHAR_SKIN);
	  }
   }
     
   function showEditor($msg = null) {
      
   	   $data                	= array();
       require_once(CLASS_DIR.'/common.list.class.php');	
	   $comListApp 	= new CommonList();
	   $data['customer_list'] 	= $this->getCustomerList();	
	   $data['reference_list'] 	= $comListApp->getReferenceList();	
	   $data['cat_list'] 		= $this->getCatagoryList();	      
	   $data['brand_list'] 		= $comListApp->getBrandList();	   
	   $data['currency_list']   = $this->getCurrencyList();
	 	
	   $data['cmd']         	= getRequest('cmd');   
	   require_once(CURRENT_APP_SKIN_FILE); 
	   return $data[0];
   }

  function insertQuotationDetails($quotation_no){
		$requestdata 				= array();
		$arr_catagory_product_id	= array();	
		$project_id  				= getFromSession('project_id');
		$currency        			= getRequest('currency');
	
		$arr_catagory_product_id	= getRequest('input_catagory_product_id');
		$arr_brand        			= getRequest('input_brand');
    	$arr_pdetails        		= getRequest('input_pdetails');
		$arr_warranty        		= getRequest('input_warranty');
		$arr_m_unit        			= getRequest('input_m_unit');
		$arr_unit_price				= getRequest('input_unit_price');
		$arr_qty      				= getRequest('input_qty');
		$arr_total_value       		= getRequest('input_total_value');
	
		for($i=0;$i<count($arr_catagory_product_id);$i++)
		{
		  $catagory_product_sep 	= $arr_catagory_product_id[$i];		
		  $requestdata['project_id']= $project_id;       	  
	
		  for($j=0;$j<count($catagory_product_sep);$j++){
				$catagory_product = explode("###",$catagory_product_sep);
				$catagoryid  	  = array();
				$productid 	      = array();				
				$brandid 	      = array();			  
				$catagoryid['c']  = $catagory_product[0];				
				$brandid['b']  	  = $catagory_product[1];				
				$productid['p']   = $catagory_product[2];
			}
	
		   foreach($catagoryid as $val)
		   {
				$requestdata['catagory'] = $val;	
		   }
		   foreach($brandid as $val){
				$requestdata['brand_id']= $val; $brand_id = $val;
		   }	
		   foreach($productid as $val)
		   {
				$requestdata['product'] =$val;	
				$product_id				=$val;
		   }
		   foreach($arr_m_unit as $key => $val)
		   {
			  if($catagory_product_sep==$key)
			  {
				$requestdata['m_unit'] = $val;	
			  }
		   }	    
		   foreach($arr_pdetails as $key => $val){
			  if($catagory_product_sep==$key){
				$requestdata['details'] = $val;
			  }
		   }
		   
		   foreach($arr_warranty as $key => $val)
		   {
			  if($catagory_product_sep==$key)
			  {
				$requestdata['warranty'] = $val;  $warranty = $val; 
			  }
		   }   	  
		   foreach($arr_unit_price as $key => $val)
		   {
			  if($catagory_product_sep==$key)
			  {
				$requestdata['unit_price'] = $val;	
			  }
		   }
		   foreach($arr_qty as $key => $val)
		   {
				if($catagory_product_sep==$key)
				{
					 $requestdata['qty'] = $val;	
					 $productQty		 = $val;
				}
		   }	
		   foreach($arr_total_value as $key => $val)
		   {
			  if($catagory_product_sep==$key)
			  {
				 $requestdata['total'] = $val; 	
			  }
		   }
		   			
		    $requestdata['created_by'] 		  = getFromSession('userid');
		    $requestdata['created_date']      = date('Y-m-d h:i:s');  
			$project_id						  = getFromSession('project_id'); 
		    $requestdata['project_id']        = $project_id;			
		    $requestdata['quotation_no']      = $quotation_no;
			$requestdata['customer']          = getRequest('customer');
			$requestdata['reference']         = getRequest('reference');
		    //$info['debug']  	=  true;
			
			$info        		=  array();
			$info['table']	= QUOTATION_DETAILS_TBL;
			$info['data'] 	= $requestdata;   
			$res = insert($info);				
	   } 

  } //End of the function insertQuotationDetails()

 	 
	function insertQuotationMaster($quotation_no){
    	  $requestdata = array();	
		  $requestdata = getUserDataSet(QUOTATION_MASTER_TBL);		 
		  //$requestdata['type']  = "Queeck Quotation";  
		  $requestdata['quotation_date'] 	= formatDate(getRequest('quotation_date')); 
		  $requestdata['quotation_no']      = $quotation_no; 
		  		
		  $requestdata['project_id']        = getFromSession('project_id');    
		  $requestdata['created_by']        = getFromSession('userid');	
		  $requestdata['created_date']      = date('Y-m-d h:i:s');
	
		  $info        		=  array();
		  $info['table']	= QUOTATION_MASTER_TBL;
		  $info['data'] 	= $requestdata;     
		  //$info['debug']  =  true;
		  $res = insert($info);
			
	}
   function saveQueeckQuotation(){
		mysql_query("START TRANSACTION;");
		$quotation_no = $this->createVoucharID();
		$this->insertQuotationMaster($quotation_no);
		$this->insertQuotationDetails($quotation_no); 
		mysql_query("COMMIT;");
		if($quotation_no!=""){
		header("location:index.php?app=queeck.quotation&cmd=print_vouchar&quotation_no=".$quotation_no);	
		}else{
		header("location:index.php?app=queeck.quotation&cmd=add");
		}
   }
   
	function getQuotationMasterInfo($id){		
		   
		$project_id     = getFromSession('project_id');  
		$info           = array();    
		$info['table']  = QUOTATION_MASTER_TBL.' qm,'.SUB_ACC_HEAD_TBL.' s,'.PROJECT_TBL.' p,'.CURRENCY_TBL.' c';	
		$info['fields'] = array('qm.quotation_no','p.project_name','p.location','s.sub_head_name','s.head_details','s.phone','s.mobile','s.email','s.att_name1','s.att_designation1','s.att_mobile1','qm.reference','qm.total_value','qm.net_payble',"DATE_FORMAT(qm.quotation_date,'%d %b %y' ) as quotation_date",'c.curr_symble','qm.discount','qm.net_payble','qm.created_date');	
		$sql="qm.customer = s.sub_id AND qm.project_id = p.project_id AND qm.currency = c.currency_id AND qm.project_id = '".$project_id."' AND qm.quotation_no = '$id'";
							
		$info['where']  =$sql;	  	
	    $info['groupby'] = array("qm.quotation_no");
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
		$info['table']  =  QUOTATION_DETAILS_TBL.' sd,'.PRODUCT_TBL.' p,'.CURRENCY_TBL.' c,'.BRAND_TBL.' b';	
		$info['fields'] = array('sd.details_id','sd.quotation_no','sd.project_id','sd.warranty','sd.catagory','b.brand_name','sd.product','sd.details','p.product_name','p.product_desc','sd.m_unit','sd.unit_price','c.curr_symble','sd.qty','sd.total','sd.created_date');
		
		$sql="sd.product = p.product_id AND p.brand_code = b.brand_id AND sd.currency = c.currency_id AND sd.quotation_no = '$id'";
		
		$info['where']  = $sql;
	    $info['groupby'] = array("sd.details_id");
		$info['orderby'] = array("sd.details_id asc");
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
		  $brand_id		   = trim(getRequest('brand_id'));
	  	  $project_id = getFromSession('project_id');  
		  $info            = array();
		  $info['table']   = PRODUCT_TBL;
		  $info['fields']  =  array('product_id','product_name','product_desc');
		  $info['where']   = "`brand_code`='$brand_id' AND project_id = '$project_id' AND approval_status = 1";
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
			 $subject_idname .= $v[0]->product_id.'#####'.$v[0]->product_name.'#####'.$v[0]->product_desc.'@@@';
		  }
		  echo $subject_idname;	
	}

 function loadProductDtl($product_id){
	  $project_id = getFromSession('project_id');  		 
	  $info            = array();
	  $info['table']   = PRODUCT_TBL;
	  $info['fields']  = array('m_unit','product_desc');
	  $where= "product_id = '$product_id' AND project_id = '$project_id' ";
	  $info['where']   = $where;
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
		 $str = $v[0]->m_unit."#####".$v[0]->product_desc."#####";
	  }
	  echo $str;	
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
        
        	  
   function createVoucharID()
   {
      $info = array();
      $info['table'] = QUOTATION_MASTER_TBL;
      $info['fields'] = array('max(quotation_no) as maxvoucher');
      
      $res = select($info);
      
      $maxvoucherId = 'Q0000000';
      
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
      
      $maxvoucherId = generateID("Q",$maxvoucherId,8);
      return $maxvoucherId;
   }  
   
 //================= Quotation List ====================

  function showListEditor($msg = null) { 
	  $data                				= array();
	  $data['cmd']         				= getRequest('cmd');
	  $data['record_list'] 				= $this->getQuotationList(getRequest('from'),getRequest('to'));
	  $data['totalrecord']				= $this->getTotalQuotationList();		
	   require_once(QUOTATION_LIST_SKIN); 
	   return $data[0];

   }
   function getQuotationList($from,$to) { 
		if($from == "" && $to == ""){$from=0; $to=500;}
		$date_from 		= formatDate(getRequest('date_from'));
		$date_to 		= formatDate(getRequest('date_to'));				
		$project_id     = getFromSession('project_id');  
		$info           = array();    
		$info['table']  = QUOTATION_MASTER_TBL.' qm,'.SUB_ACC_HEAD_TBL.' s,'.PROJECT_TBL.' p,'.CURRENCY_TBL.' c';	
		$info['fields'] = array('qm.quotation_no','p.project_name','p.location','s.sub_id','s.sub_head_name','s.head_details','qm.total_value','qm.discount','qm.net_payble','qm.description',"DATE_FORMAT(qm.quotation_date,'%d %b %y' ) as quotation_date",'c.curr_symble','qm.quotation_status',"DATE_FORMAT(qm.created_date,'%d %b %y' ) as created_date");
		
		$sql="qm.customer = s.sub_id AND qm.project_id = p.project_id AND qm.currency = c.currency_id AND qm.project_id = '".$project_id."'";
		if($date_from!="" && $date_to ==""){
			$sql.=" AND qm.quotation_date >= '$date_from'";
		}elseif($date_from=="" && $date_to !=""){
			$sql.=" AND qm.quotation_date <= '$date_to'";
		}elseif($date_from!="" && $date_to !=""){
			$sql.=" AND qm.quotation_date BETWEEN '$date_from' AND '$date_to'";
		}
		$info['where']  =$sql;
		$info['orderby'] = array("qm.quotation_no asc LIMIT $from,$to");
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

   function getTotalQuotationList() { 		
		$date_from 		= formatDate(getRequest('date_from'));
		$date_to 		= formatDate(getRequest('date_to'));				
		$project_id     = getFromSession('project_id');  
		$info           = array();    
		$info['table']  = QUOTATION_MASTER_TBL.' qm,'.SUB_ACC_HEAD_TBL.' s,'.PROJECT_TBL.' p,'.CURRENCY_TBL.' c';	
		$info['fields'] = array('qm.quotation_no');
		
		$sql="qm.customer = s.sub_id AND qm.project_id = p.project_id AND qm.currency = c.currency_id AND qm.project_id = '".$project_id."'";
		if($date_from!="" && $date_to ==""){
			$sql.=" AND qm.quotation_date >= '$date_from'";
		}elseif($date_from=="" && $date_to !=""){
			$sql.=" AND qm.quotation_date <= '$date_to'";
		}elseif($date_from!="" && $date_to !=""){
			$sql.=" AND qm.quotation_date BETWEEN '$date_from' AND '$date_to'";
		}
		$info['where']  =$sql;
		$info['orderby'] = array("qm.quotation_no asc");

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
//==================== End Sales Details =====================
   
} // End class


?>