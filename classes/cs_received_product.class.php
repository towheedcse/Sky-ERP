<?php
/**
 * File: cs_received_product.class.php
 * This application is used to authenticate users
 *
 */
class ReceivedProduct
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
      if($u_t_id == 101||$u_t_id == 102||$u_t_id == 103)  //103 = project admin
      {

      	switch ($cmd)
      	{
      	   case 'add'                	: $screen = $this->showEditor($msg); break;
		   case 'print_invoice'			: $screen = $this->showPrintEditor($msg); break;
      	   case 'edit'               	: $screen = $this->showEditor("Edit Page");    break;	
      	   case 'doUpdate'           	: $this->updateReceived(); break;
		   case 'delete'             	: $screen = $this->deleteReceived(); break;
      	   case 'list'               	: $screen = $this->showList($msg);   break;
		   case 'get_booking_info'		: $this->showBookingList();   break;
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
	  $data['record_list'] 	= $this->getProductReceivedList(getRequest('from'),getRequest('to'));    
	  $data['totalrecord']	= $this->getTotalProductReceivedList(getRequest('from'),getRequest('to'));	 
	  //$data['buyer_list']	= $orderApp->getBuyerList();   
	  
	   if(getRequest('deleted')=='yes') {
		  $data['message'] = "Item Deleted Successfully";
	   }elseif(getRequest('deleted')=='no') {
		  $data['message'] = "Item Not Deleted";
	   }
	   require_once(CURRENT_APP_SKIN_FILE); 
	   return $data[0];
   }
   
      
   function getProductReceivedList($from,$to) {  

		if($from == "" && $to == ""){$from=0; $to=20;}		
	    $project_id 		= getFromSession('project_id');
		$srckey 	= getRequest('srckey');
		$posting_sr = getRequest('posting_sr');
		$info           = array();    
		$info['table']  =  CS_PRODUCT_RECEIVED_TBL.' pr,'.CUSTOMER_BOOKING_TBL.' cb,'.PARTY_INFO_TBL.' p';	
		$info['fields'] = array('pr.received_id','pr.booking_id','pr.customer_code','pr.agent_code','pr.received_bag_qty','pr.rent_per_bag','pr.product_details','pr.floor_no','pr.room_no','pr.row_no','pr.cabin_no','pr.sr_no','pr.posting_sr','p.name','p.fname','p.mobile','p.address','cb.booking_id','cb.total_carring_cost','cb.total_empty_bag_cost','cb.booking_amount');
		
		$sql="pr.list_view='Active' AND pr.booking_id = cb.booking_id AND cb.customer_code = p.party_id AND pr.project_id = '$project_id'";
		
		if($srckey!=""){
			$sql.=" AND ( pr.received_id LIKE '%$srckey%' OR p.name LIKE '%$srckey%' OR p.party_id LIKE '%$srckey%' OR p.party_type LIKE '%$srckey%' OR pr.booking_id LIKE '%$srckey%')";
		}elseif($posting_sr!=""){
			$sql.=" AND pr.posting_sr ='$posting_sr'";
		}
			
		$info['where']  =$sql;	  	
	      $info['groupby'] = array("pr.received_id");
		$info['orderby'] = array("pr.received_id desc LIMIT $from,$to");
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
   
   function getTotalProductReceivedList($from,$to) {  

   		$srckey 	= getRequest('srckey');
				
	    $project_id 		= getFromSession('project_id');
		$info           = array(); 
		$info['table']  =  CS_PRODUCT_RECEIVED_TBL.' pr,'.CUSTOMER_BOOKING_TBL.' cb,'.PARTY_INFO_TBL.' p';	
		$info['fields'] = array('pr.received_id');
		
		$sql="pr.list_view='Active' AND pr.booking_id = cb.booking_id AND cb.customer_code = p.party_id AND pr.project_id = '$project_id'";
		
		if($srckey!=""){
			$sql.=" AND ( pr.received_id LIKE '%$srckey%' OR p.name LIKE '%$srckey%' OR p.party_id LIKE '%$srckey%' OR p.party_type LIKE '%$srckey%' OR pr.booking_id LIKE '%$srckey%')";
		}
			
		$info['where']  =$sql;	
	    $info['groupby'] = array("pr.received_id");
		$info['orderby'] = array("pr.received_id desc");
		$info['debug']  = false;
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
     
   /**
   * Shows editor for CustomerBooking system
   * @paran null
   * @return none
   */

   function showEditor($msg = null) { 
     	  
      $ID = getRequest('id');
	  if ($ID) {
         $advArr = $this->getProductReceivedInfo($ID);
         $advArr = parseThisValue($advArr);  
         $data   = array_merge(array(), $advArr); 
      }
      else
      {
         if(getRequest('save'))
         {
            $this->addReceived();	
         }
      }	   
	  //$data['agent_list']  = $this->getAgentList();
	 
      $data['message'] = $msg;
      $data['cmd']     = getRequest('cmd');
	  require_once(CS_RECEIVED_ADD_EDIT_SKIN);      
      return true;
   }
   function showPrintEditor($msg = null) { 
     	  
      $ID = getRequest('id');
	  if ($ID) {
         $advArr = $this->getProductReceivedInfo($ID);
         $advArr = parseThisValue($advArr);  
         $data   = array_merge(array(), $advArr); 
      }
      
      $data['message'] = $msg;
      $data['cmd']     = getRequest('cmd');
	  require_once(CS_RECEIVED_INVOICE_SKIN);      
      return true;
   }
           
   function addReceived($msg = null)
   {    	  
   	  $requestdata = array();
      $requestdata = getUserDataSet(CS_PRODUCT_RECEIVED_TBL);	
      //dumpvar($requestdata);	
	  $received_id = $this->createID();     
      $requestdata['project_id']        = getFromSession('project_id');    
  
      $requestdata['created_by']        = getFromSession('userid');
	  $requestdata['created_date']      = date('Y-m-d h:i:s');
      $requestdata['modified_by']       = getFromSession('userid');
	  $requestdata['modified_time']     = date('Y-m-d h:i:s');
	  if($$received_id != -1)
      {
      	$requestdata['received_id']   	= $received_id;
      }
      else
      {
      	$msg = "ID overflow !!!";
      	header("location:index.php?app=user_home&msg=$msg");
      	exit;
      }
	   
      $info        		=  array();
      $info['table']	= CS_PRODUCT_RECEIVED_TBL;
      $info['data'] 	= $requestdata;     
      $info['debug']  	=  true;                     
      $res = insert($info);
      //dBug($info);
      //dBug($requestdata);
	  if($res['affected_rows']) {	  	 		 
	  	 header("location:index.php?app=cs_received_product&cmd=print_invoice&id=".$received_id);
	  }else {	 
	    header("location:index.php?app=cs_received_product&cmd=add");
	  }      
   }
   
   function updateReceived() {
   	  $received_id = getRequest('received_id');
   	  $requestdata = array();
      $requestdata = getUserDataSet(CS_PRODUCT_RECEIVED_TBL);	
      //dumpvar($requestdata);	 	    
      $requestdata['project_id']        = getFromSession('project_id');    
	  $requestdata['modified_by']       = getFromSession('userid');
	  $requestdata['modified_time']     =  date('Y-m-d h:i:s');
	 
	  $info        		=  array();
      $info['table']	= CS_PRODUCT_RECEIVED_TBL;
      $info['data'] 	= $requestdata;    	  
      $info['where']	= "received_id='$received_id'";     
      $info['debug']  	=  true;    
      $res = update($info);
      
      if($res)
      {	  	
         header("location:index.php?app=cs_received_product");
      }
	  else
	  {
	  	header("location:index.php?app=cs_received_product&id=".getRequest('id'));
	  }     
                
   }//EOFn
   
   function getProductReceivedInfo($id)
   {		
	  $project_id 		= getFromSession('project_id');
   	  $data           =  array();
	  $info           = array();    
	  $info['table']  =  CS_PRODUCT_RECEIVED_TBL.' pr,'.CUSTOMER_BOOKING_TBL.' cb,'.PARTY_INFO_TBL.' p';	
	  $info['fields'] = array('pr.received_id','pr.booking_id','pr.customer_code','pr.agent_code','pr.received_bag_qty','pr.rent_per_bag','pr.product_details','pr.floor_no','pr.room_no','pr.row_no','pr.cabin_no','pr.sr_no','pr.posting_sr','p.name','p.fname','p.mobile','p.address','cb.booking_id','cb.total_carring_cost','cb.total_empty_bag_cost','cb.booking_amount',"DATE_FORMAT(pr.created_date,'%d %b %Y' ) as created_date");
	  $sql="pr.list_view='Active' AND pr.booking_id = cb.booking_id AND cb.customer_code = p.party_id AND pr.received_id = '".$id."' AND pr.project_id = '$project_id'";
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

	   
   function deleteReceived() {
      if(getRequest('id'))
      {
			$id = getRequest('id'); 		
	   		$project_id 		= getFromSession('project_id');
			             	
			$info = array();
			$info['table']  =  CS_PRODUCT_RECEIVED_TBL;
       		$info['where']  =  "received_id='".$id."' AND project_id = '$project_id'";
			$info['debug'] = false;      	
			$res = delete($info);
			 if($res)
			  {	  	
				 header("location:index.php?app=cs_received_product");
			  }
			  else
			  {
				header("location:index.php?app=cs_received_product&id=".getRequest('id'));
			  }    	
      	}	
   }
   
   function createID()
   {
      $info = array();
      $info['table'] = CS_PRODUCT_RECEIVED_TBL;
      $info['fields'] = array('max(received_id) as maxreceived');
      
      $res = select($info);
      
      $maxreceivedId = 'R00000';
      
      if(count($res))
      {
         foreach($res as $v)
         {
         	 if($v->maxreceived)
         	 {
             $maxreceivedId = $v->maxreceived;
             }
             break;   	
         }
      
      }
      
      $maxreceivedId = generateID("R",$maxreceivedId,6);
      return $maxreceivedId;
   }  
   
   function showBookingList($msg = null)
   { 
     
	  $data                = array();
	  $srckey 	= getRequest('srckey');			
	  $project_id 		= getFromSession('project_id');
	  if($srckey!=""){ 
	   $data['record_list'] 				= array($this->getBookingList(getRequest('from'),getRequest('to')));    
	   $data['totalrecord']					= $this->getTotalBookingList(getRequest('from'),getRequest('to'));
	  }else{
	   $data['record_list'] ="";
	   $data['totalrecord'] ='';
	  }     
      $data['message'] = $msg;
      $data['cmd']     = getRequest('cmd');     
      require_once(SHOW_BOOKING_INFO);    
      return $data;
    }
	
   function getBookingList($from,$to) {  

   	  if($from == "" && $to == ""){$from=0; $to=20;}
	  $srckey 	= getRequest('srckey');		
	  $project_id 		= getFromSession('project_id');	 
  	  $data            = array();	  
  	  $info            = array();
	  $info['table']  =  CUSTOMER_BOOKING_TBL.' cb,'.PARTY_INFO_TBL.' p';	
	  $info['fields'] = array('p.party_id','p.name','p.fname','p.mobile','p.address','cb.booking_id','cb.agent_code','cb.bag_qty','cb.total_carring_cost','cb.total_empty_bag_cost','cb.booking_amount');
		
	  		
	  if($srckey!=""){
	    $sql="cb.list_view='Active' AND cb.customer_code = p.party_id AND cb.project_id = '$project_id'";
		$sql.=" AND (p.name LIKE '%$srckey%' OR p.party_id LIKE '%$srckey%' OR p.party_type LIKE '%$srckey%' OR cb.booking_id LIKE '%$srckey%')";
	    $info['where'] = $sql;
		$info['groupby'] = array("cb.booking_id");
	    $info['orderby'] = array("cb.booking_id desc LIMIT $from,$to");
		
	  }else{
		$info['where']  =  "cb.project_id = '$project_id'";
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
      
   function getTotalBookingList($from,$to) {  

   	 if($from == "" && $to == ""){$from=0; $to=20;}
	   $srckey 	= getRequest('srckey');	 		
	   $project_id 		= getFromSession('project_id');
  	   $data            = array();	  
  	   $info            = array();
	  $info['table']  =  CUSTOMER_BOOKING_TBL.' cb,'.PARTY_INFO_TBL.' p';	
	  $info['fields'] = array('cb.booking_id');
		
	  		
	  if($srckey!=""){
	    $sql="cb.list_view='Active' AND cb.customer_code = p.party_id AND cb.project_id = '$project_id'";
		$sql.=" AND (p.name LIKE '%$srckey%' OR p.party_id LIKE '%$srckey%' OR p.party_type LIKE '%$srckey%' OR cb.booking_id LIKE '%$srckey%')";
	    $info['where'] = $sql;
		$info['groupby'] = array("cb.booking_id");
	    $info['orderby'] = array("cb.booking_id desc LIMIT $from,$to");
		
	  }else{
		$info['where']  =  "cb.project_id = '$project_id'";
	  }	  
	  $info['debug']  = false;
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
             	
} // End class

?>