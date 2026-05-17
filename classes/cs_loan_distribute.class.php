<?php
/**
 * File: cs_received_product.class.php
 * This application is used to authenticate users
 *
 */
class LoanDistribute
{
   /**
   * This is the "main" function which is called to run the application
   *
   * @param none
   * @return true if successful, else returns false
   */
    function run()
   {         

      $cmd = getRequest('cmd');
	  $u_t_id = getFromSession('u_type_id');
      if($u_t_id == 101||$u_t_id == 102||$u_t_id == 104)  //103 = project admin
      {

      	switch ($cmd)
      	{
      	   case 'add'                	: $screen = $this->showEditor($msg); break;
		   case 'print_invoice'			: $screen = $this->showPrintEditor($msg); break;
      	   case 'edit'               	: $screen = $this->showEditor("Edit Page");    break;	
      	   case 'doUpdate'           	: $this->updateLoan(); break;
		   case 'delete'             	: $screen = $this->deleteLoan(); break;
      	   case 'list'               	: $screen = $this->showList($msg);   break;
		   case 'get_recieved_info'		: $this->showRecievedProductList();   break;
      	   default                   	:$cmd = 'list'; $screen = $this->showList($msg);   break;

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
   function showList($msg = null) {  
      
	  $data                	= array();
	  $data['cmd']         	= getRequest('cmd');
	  $data['record_list'] 	= $this->getLoanDistributeList(getRequest('from'),getRequest('to'));    
	  $data['totalrecord']	= $this->getTotalLoanDistributeList(getRequest('from'),getRequest('to'));	  
	   if(getRequest('deleted')=='yes') {
		  $data['message'] = "Item Deleted Successfully";
	   }elseif(getRequest('deleted')=='no') {
		  $data['message'] = "Item Not Deleted";
	   }
	   require_once(CURRENT_APP_SKIN_FILE); 
	   return $data[0];
   }
   
      
   function getLoanDistributeList($from,$to) {  

		if($from == "" && $to == ""){$from=0; $to=35;}
		$srckey 	= getRequest('srckey'); 		
	    $project_id 		= getFromSession('project_id');
		
		$info           = array();    
		$info['table']  = CS_LOAN_DISTRIBUTE_TBL.' l,'.CS_PRODUCT_RECEIVED_TBL.' pr,'.PARTY_INFO_TBL.' p';	
		$info['fields'] = array('l.loan_id', 'l.received_id', 'l.customer_code','l.agent_code','l.received_bag_qty','pr.rent_per_bag','l.loan_per_bag','l.interest_per_bag','l.service_charge',"DATE_FORMAT(l.loan_date,'%d %b %Y' ) as loan_date","DATE_FORMAT(l.est_return_date,'%d %b %Y' ) as est_return_date",'p.name','p.fname','p.mobile','p.address','l.created_by','l.created_date','l.modified_by','l.modified_time');
		
		$sql="l.list_view='Active' AND l.received_id = pr.received_id AND l.customer_code = p.party_id AND l.project_id = '$project_id' ";
		
		if($srckey!=""){
			$sql.=" AND ( pr.posting_sr LIKE '%$srckey%' OR l.received_id LIKE '%$srckey%' OR p.name LIKE '%$srckey%' OR p.party_id LIKE '%$srckey%' OR p.party_type LIKE '%$srckey%' OR pr.booking_id LIKE '%$srckey%')";
		}
			
		$info['where']  =$sql;	  	
	    $info['groupby'] = array("l.loan_id");
		$info['orderby'] = array("l.loan_id desc LIMIT $from,$to");
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
   
   function getTotalLoanDistributeList($from,$to) {  

   	 if($from == "" && $to == ""){$from=0; $to=35;}
		$srckey 	= getRequest('srckey');		
	    $project_id 		= getFromSession('project_id');
		
		$info           = array();    
		$info['table']  = CS_LOAN_DISTRIBUTE_TBL.' l,'.CS_PRODUCT_RECEIVED_TBL.' pr,'.PARTY_INFO_TBL.' p';	
		$info['fields'] = array('l.loan_id');
		
		$sql="l.list_view='Active' AND l.received_id = pr.received_id AND l.customer_code = p.party_id AND l.project_id = '$project_id' ";
		
		if($srckey!=""){
			$sql.=" AND ( l.received_id LIKE '%$srckey%' OR p.name LIKE '%$srckey%' OR p.party_id LIKE '%$srckey%' OR p.party_type LIKE '%$srckey%' OR pr.booking_id LIKE '%$srckey%')";
		}
			
		$info['where']  =$sql;	  	
	    $info['groupby'] = array("l.loan_id");
		$info['orderby'] = array("l.loan_id desc LIMIT $from,$to");
		//$info['debug']  = true;
		$result         = select($info);
		$data           = array();     
		$cnt = count($result);  	
		
      if($cnt) {
        return $cnt;
      } 
	  else {
	  return 0;
	 }      
      
   }      
   function showEditor($msg=NULL)
   { 
   	  $ID = getRequest('id');
	  if ($ID) {
         $advArr = $this->getLoanDistributeInfo($ID);
         $advArr = parseThisValue($advArr);  
         $data   = array_merge(array(), $advArr); 
      }
      else
      {
         if(getRequest('save'))
         {
            $this->addLoan();	
         }
      }	   
	  //$data['agent_list']  = $this->getAgentList();
	 
      $data['message'] = $msg;
      $data['cmd']     = getRequest('cmd');
   	 require_once(LOAN_ADD_EDIT_SKIN); 
	 return true;
   }
   
   function showPrintEditor($msg = null) { 
     	  
      $ID = getRequest('id');
	  if ($ID) {
         $advArr = $this->getLoanDistributeInfo($ID);
         $advArr = parseThisValue($advArr);  
         $data   = array_merge(array(), $advArr); 
      }
      
      $data['message'] = $msg;
      $data['cmd']     = getRequest('cmd');
	  require_once(CS_RECEIVED_INVOICE_SKIN);      
      return true;
   }
           
   function addLoan($msg = null)
   {    	  
   	  $requestdata = array();
      $requestdata = getUserDataSet(CS_LOAN_DISTRIBUTE_TBL);	
      //dumpvar($requestdata);	
	  $requestdata['loan_date']     = formatDate(getRequest('loan_date'));
	  $requestdata['est_return_date']     = formatDate(getRequest('est_return_date'));
      $requestdata['project_id']        = getFromSession('project_id');    

      $requestdata['created_by']        = getFromSession('userid');
	  $requestdata['created_date']      = date('Y-m-d h:i:s');
      $requestdata['modified_by']       = getFromSession('userid');
	  $requestdata['modified_time']     = date('Y-m-d h:i:s');
	 	   
      $info        		=  array();
      $info['table']	= CS_LOAN_DISTRIBUTE_TBL;
      $info['data'] 	= $requestdata;     
      //$info['debug']  	=  true;                     
      $res = insert($info);
      //dBug($info);
      //dBug($requestdata);
	  if($res['affected_rows']) {	
	  	$loan_id = mysql_insert_id();  	 		 
	  	//header("location:index.php?app=cs_loan_distribute&cmd=print_invoice&id=".$loan_id);
		header("location:index.php?app=cs_loan_distribute");
	  }else {	 
	    header("location:index.php?app=cs_loan_distribute&cmd=add");
	  }      
   }
   
   function updateLoan() {
   	  $id = getRequest('id');
   	  $requestdata = array();
      $requestdata = getUserDataSet(CS_LOAN_DISTRIBUTE_TBL);	
      //dumpvar($requestdata);		
	  $requestdata['loan_date']     		= formatDate(getRequest('loan_date'));
	  $requestdata['est_return_date']     	= formatDate(getRequest('est_return_date'));	
      $requestdata['project_id']        	= getFromSession('project_id');        
	  $requestdata['modified_by']       	= getFromSession('userid');
	  $requestdata['modified_time']     	=  date('Y-m-d h:i:s');
	 
	  $info        		=  array();
      $info['table']	= CS_LOAN_DISTRIBUTE_TBL;
      $info['data'] 	= $requestdata;    	  
      $info['where']	= "loan_id='$id'";     
      //$info['debug']  	=  true;    
      $res = update($info);
      
      if($res)
      {	  	
         header("location:index.php?app=cs_loan_distribute");
      }
	  else
	  {
	  	header("location:index.php?app=cs_loan_distribute&id=".getRequest('id'));
	  }     
                
   }//EOFn
   
   function getLoanDistributeInfo($id)
   {
   	  $data           =  array();		
	  $project_id 		= getFromSession('project_id');
	  $info           = array();    
	  $info['table']  =  CS_LOAN_DISTRIBUTE_TBL.' l,'.CS_PRODUCT_RECEIVED_TBL.' pr,'.PARTY_INFO_TBL.' p';	
	  $info['fields'] = array('l.received_id','l.received_id','l.customer_code','l.agent_code','pr.received_bag_qty','pr.rent_per_bag','l.loan_per_bag','l.interest_per_bag','l.service_charge','l.loan_date','l.est_return_date','p.name','p.fname','p.mobile','p.address');
	  $sql="l.list_view='Active' AND l.received_id = pr.received_id AND l.customer_code = p.party_id AND l.loan_id = '".$id."' AND l.project_id = '$project_id'";
	  $info['where']  =$sql;	  	
	  $info['debug']  =  false;                     
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
      
   function deleteLoan() {
      if(getRequest('id'))
      {
			$id = getRequest('id'); 		
	   		$project_id 		= getFromSession('project_id');
			             	
			$info = array();
			$info['table']  =  CS_LOAN_DISTRIBUTE_TBL;
       		$info['where']  =  "loan_id='".$id."' AND project_id = '$project_id'";
			$info['debug'] = false;      	
			$res = delete($info);
			 if($res)
			  {	  	
				 header("location:index.php?app=cs_loan_distribute");
			  }
			  else
			  {
				header("location:index.php?app=cs_loan_distribute&id=".getRequest('id'));
			  }    	
      	}	
   }
   
   //============= Show Product Received ====================
      
   function showRecievedProductList($msg = null)
   { 
     
	  $data                = array();
	  $srckey 	= getRequest('srckey');	
	  if($srckey!=""){ 
	   $data['record_list'] 				= array($this->getRecievedProductList(getRequest('from'),getRequest('to')));    
	   $data['totalrecord']					= $this->getTotalRecievedProductList(getRequest('from'),getRequest('to'));
	  }else{
	   $data['record_list'] ="";
	   $data['totalrecord'] ='';
	  }     
      $data['message'] = $msg;
      $data['cmd']     = getRequest('cmd');     
      require_once(SHOW_PRODUCT_RECIEVED_INFO);    
      return $data;
    }
	
   function getRecievedProductList($from,$to) {  

   	  if($from == "" && $to == ""){$from=0; $to=300;}
	  $srckey 	= getRequest('srckey');	 		
	  $project_id 		= getFromSession('project_id');
  	  $data            = array();	  
  	  $info            = array();
	  $info['table']  =  CS_PRODUCT_RECEIVED_TBL.' pr,'.PARTY_INFO_TBL.' p';	
	  $info['fields'] = array('p.party_id','p.name','p.fname','p.mobile','p.address','pr.posting_sr','pr.received_id','pr.agent_code','pr.received_bag_qty','pr.rent_per_bag');
		
	  		
	  if($srckey!=""){
	    $sql="pr.list_view='Active' AND pr.customer_code = p.party_id AND pr.project_id = '$project_id' ";
		$sql.=" AND (p.name LIKE '%$srckey%' OR p.party_id LIKE '%$srckey%' OR p.party_type LIKE '%$srckey%' OR pr.received_id LIKE '%$srckey%')";
	    $info['where'] = $sql;
		$info['groupby'] = array("pr.received_id");
	    $info['orderby'] = array("pr.received_id desc LIMIT $from,$to");		
	  }else{
			$info['where']  =  "pr.project_id = '$project_id'";
	  }		  
	  //$info['debug']  = true;
      $result         = select($info);
	  $data           = array();
	  $cnt = count($result);  	     

      if($cnt) {
         foreach($result as $key=>$value)
         {
            $data[$key][]	= $value;
         }	
      }      
      return $data; 
   } 
      
   function getTotalRecievedProductList($from,$to) {  

   	 	if($from == "" && $to == ""){$from=0; $to=300;}
		  $srckey 	= getRequest('srckey');		
	   	  $project_id 		= getFromSession('project_id');	 
		  $data            = array();	  
		  $info            = array();
		  $info['table']  =  CS_PRODUCT_RECEIVED_TBL.' pr,'.PARTY_INFO_TBL.' p';	
		  $info['fields'] = array('p.party_id','p.name','p.fname','p.mobile','p.address','pr.received_id','pr.agent_code','pr.received_bag_qty','pr.rent_per_bag');
			
				
		  if($srckey!=""){
			$sql="pr.list_view='Active' AND pr.customer_code = p.party_id AND pr.project_id = '$project_id' ";
			$sql.=" AND (p.name LIKE '%$srckey%' OR p.party_id LIKE '%$srckey%' OR p.party_type LIKE '%$srckey%' OR pr.received_id LIKE '%$srckey%')";
			$info['where'] = $sql;
			$info['groupby'] = array("pr.received_id");
			$info['orderby'] = array("pr.received_id desc LIMIT $from,$to");
			
		  }else{
			$info['where']  =  "pr.project_id = '$project_id'";
	  	 }	  
      	$result         = select($info);
	  	$data           = array();
	  	$cnt = count($result);  	     

      	if($cnt) {
        	return $cnt;
      	} 
	  	else {
	  		return 0;
	 	}      
      
   }    
   
} 
?>