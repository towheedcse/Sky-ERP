<?php
class CustomerBooking
{
   function run()
   {         

      $cmd = getRequest('cmd');
	  $u_t_id = getFromSession('u_type_id');
      if($u_t_id == 101)  //103 = project admin
      {

      	switch ($cmd)
      	{
      	   case 'add'                	: $screen = $this->showEditor($msg); break;
      	   case 'edit'               	: $screen = $this->showEditor("Edit Page");    break;
		   case 'print_invoice'			: $screen = $this->showPrintEditor($msg); break;	
      	   case 'doUpdate'           	: $this->updateBooking(); break;
		   case 'delete'             	: $screen = $this->deleteBooking(); break;
      	   case 'list'               	: $screen = $this->showList($msg);   break;
		   case 'get_customer'			: $this->showPartyList();   break;
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
	  $data['record_list'] 	= $this->getCustomerBookingList(getRequest('from'),getRequest('to'));    
	  $data['totalrecord']	= $this->getTotalCustomerBookingList(getRequest('from'),getRequest('to'));	 
	  //$data['buyer_list']	= $orderApp->getBuyerList();   
	  
	   if(getRequest('deleted')=='yes') {
		  $data['message'] = "Item Deleted Successfully";
	   }elseif(getRequest('deleted')=='no') {
		  $data['message'] = "Item Not Deleted";
	   }
	   require_once(CURRENT_APP_SKIN_FILE); 
	   return $data[0];
   }
   
   function getAgentList()
   {			
	  $project_id 		= getFromSession('project_id');
      $data 			= array(); 
      $info        		= array();
      $info['table']	= PARTY_INFO_TBL;
	  $info['fields'] 	= array('party_id','name','mobile');
	  $info['where']  	= "party_type = 'Agent' AND project_id = '$project_id'";
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
    
   function getCustomerBookingList($from,$to) {  

		if($from == "" && $to == ""){$from=0; $to=20;}
		$srckey 	= getRequest('srckey');		
		$project_id 		= getFromSession('project_id');
		
		$info           = array();    
		$info['table']  =  CUSTOMER_BOOKING_TBL.' cb,'.PARTY_INFO_TBL.' p';	
		$info['fields'] = array('p.party_id','cb.agent_code','p.name','p.fname','p.mname','p.mobile','p.address','cb.booking_id','cb.bag_qty','cb.carring_per_bag','cb.empty_bag_qty','cb.empty_bag_u_price','cb.total_carring_cost','cb.total_empty_bag_cost','cb.booking_amount');
		
		$sql="cb.list_view='Active' AND cb.customer_code = p.party_id AND cb.project_id = '$project_id'";
		
		if($srckey!=""){
			$sql.=" AND (p.name LIKE '%$srckey%' OR p.party_id LIKE '%$srckey%' OR p.party_type LIKE '%$srckey%' OR cb.agent_code  LIKE '%$srckey%')";
		}
			
		$info['where']  =$sql;	
	  
		$info['orderby'] = array("cb.booking_id asc LIMIT $from,$to");
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
   
   function getTotalCustomerBookingList($from,$to) {  

   		$srckey 	= getRequest('srckey');				
		$project_id 		= getFromSession('project_id');
		$info           = array();    
		$info['table']  =  CUSTOMER_BOOKING_TBL.' cb,'.PARTY_INFO_TBL.' p';	
		$info['fields'] = array('p.party_id');
		
		$sql="cb.list_view='Active' AND cb.customer_code = p.party_id AND cb.project_id = '$project_id'";
		
		if($srckey!=""){
			$sql.=" AND (p.name LIKE '%$srckey%' OR p.party_id LIKE '%$srckey%' OR p.party_type LIKE '%$srckey%' OR cb.agent_code  LIKE '%$srckey%')";
		}
			
		$info['where']  =$sql;	
	  
		$info['orderby'] = array("cb.booking_id asc");
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
     
   /**
   * Shows editor for CustomerBooking system
   * @paran null
   * @return none
   */

   function showEditor($msg = null) { 
     	  
      $ID = getRequest('id');
	  if ($ID) {
         $advArr = $this->getCustomerBookingInfo($ID);
         $advArr = parseThisValue($advArr);  
         $data   = array_merge(array(), $advArr); 
      }
      else
      {
         if(getRequest('save'))
         {
            $this->addBooking();	
         }
      }	   
	  $data['agent_list']  = $this->getAgentList();
	 
      $data['message'] = $msg;
      $data['cmd']     = getRequest('cmd');
	  require_once(BOOKING_ADD_EDIT_SKIN);      
      return true;
   }
   
   function showPrintEditor($msg = null) { 
     	  
      $ID = getRequest('id');
	  if ($ID) {
         $advArr = $this->getCustomerBookingInfo($ID);
         $advArr = parseThisValue($advArr);  
         $data   = array_merge(array(), $advArr); 
      }
      
      $data['message'] = $msg;
      $data['cmd']     = getRequest('cmd');
	  require_once(CS_LOAN_INVOICE_SKIN);      
      return true;
   }
        
   function addBooking($msg = null)
   {   
   	  $requestdata = array();
      $requestdata = getUserDataSet(CUSTOMER_BOOKING_TBL);	
      //dumpvar($requestdata);	
	  $booking_id = $this->createID();         
      $requestdata['project_id']        = getFromSession('project_id');    
      $requestdata['created_by']        = getFromSession('userid');
	  $requestdata['created_date']      = date('Y-m-d h:i:s');
      $requestdata['modified_by']       = getFromSession('userid');
	  $requestdata['modified_time']     = date('Y-m-d h:i:s');
	  if($$booking_id != -1)
      {
      	$requestdata['booking_id']   	= $booking_id;
      }
      else
      {
      	$msg = "ID overflow !!!";
      	header("location:index.php?app=user_home&msg=$msg");
      	exit;
      }
	   
      $info        		=  array();
      $info['table']	= CUSTOMER_BOOKING_TBL;
      $info['data'] 	= $requestdata;     
      //$info['debug']  	=  true;                     
      $res = insert($info);
      //dBug($info);
      //dBug($requestdata);
	  if($res['affected_rows']) {
	  	 header("location:index.php?app=customer_booking&cmd=print_invoice&id=".$booking_id);
	  }else {	 
	    header("location:index.php?app=customer_booking&cmd=add");
	  }      
   }
   
   function updateBooking() {
   	  $id = getRequest('id');		
	  $project_id 		= getFromSession('project_id');
   	  $requestdata = array();
      $requestdata = getUserDataSet(CUSTOMER_BOOKING_TBL);	
      //dumpvar($requestdata);	 	    
      $requestdata['project_id']        = getFromSession('project_id');    
	  $requestdata['modified_by']       		= getFromSession('userid');
	  $requestdata['modified_time']     		=  date('Y-m-d h:i:s');
	 
	  $info        		=  array();
      $info['table']	= CUSTOMER_BOOKING_TBL;
      $info['data'] 	= $requestdata;    	  
      $info['where']	= "booking_id='$id'";     
      //$info['debug']  	=  true;    
      $res = update($info);
      
      if($res)
      {	  	
         header("location:index.php?app=customer_booking");
      }
	  else
	  {
	  	header("location:index.php?app=customer_booking&id=".getRequest('id'));
	  }     
                
   }//EOFn
   
   function getCustomerBookingInfo($id)
   {		
	  $project_id 		= getFromSession('project_id');
   	  $data           =  array();                  
      $info           =  array();     
      $info['table']  =  CUSTOMER_BOOKING_TBL.' cb,'.PARTY_INFO_TBL.' p';	
	  $info['fields'] = array('p.party_id as customer_code','p.name','p.fname','p.mname','p.mobile','p.address','cb.booking_id','cb.bag_qty','cb.carring_per_bag','cb.carring_interest','cb.empty_bag_qty','cb.empty_bag_u_price','cb.total_carring_cost','cb.total_empty_bag_cost','cb.booking_amount',"DATE_FORMAT(cb.created_date,'%d %b %Y' ) as created_date");
	
	  $sql="cb.list_view='Active' AND cb.customer_code = p.party_id AND booking_id='".$id."' AND cb.project_id = '$project_id'";
		
       $info['where']  =  $sql;
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
	   
   function deleteBooking() {
      if(getRequest('id'))
      {
			$id = getRequest('id');		
			$project_id 		= getFromSession('project_id'); 
			             	
			$info = array();
			$info['table']  =  CUSTOMER_BOOKING_TBL;
       		$info['where']  =  "booking_id='".$id."' AND project_id = '$project_id'";
			$info['debug'] = false;      	
			$res = delete($info);
			 if($res)
			  {	  	
				 header("location:index.php?app=customer_booking");
			  }
			  else
			  {
				header("location:index.php?app=customer_booking&id=".getRequest('id'));
			  }    	
      	}	
   }
   
   function createID()
   {
      $info = array();
      $info['table'] = CUSTOMER_BOOKING_TBL;
      $info['fields'] = array('max(booking_id) as maxbooking');
      
      $res = select($info);
      
      $maxbookingId = 'B000000';
      
      if(count($res))
      {
         foreach($res as $v)
         {
         	 if($v->maxbooking)
         	 {
             $maxbookingId = $v->maxbooking;
             }
             break;   	
         }
      
      }
      
      $maxbookingId = generateID("B",$maxbookingId,7);
      return $maxbookingId;
   }  
   
   function showPartyList($msg = null)
   { 
     
	  $data                = array();
	  $data['record_list'] 					= array($this->getPartyList(getRequest('from'),getRequest('to')));    
	  $data['totalrecord']					= $this->getTotalPartyList(getRequest('from'),getRequest('to'));
	            
      $data['message'] = $msg;
      $data['cmd']     = getRequest('cmd');     
      require_once(GET_CUSTOMER_ID);    
      return $data;
    }
	
   function getPartyList($from,$to) {  

   	   if($from == "" && $to == ""){$from=0; $to=20;}		
	   $project_id 		= getFromSession('project_id');
	   $srckey 	= getRequest('srckey');	 
  	   $data            = array();	  
  	   $info            = array();
	   $info['table']   = PARTY_INFO_TBL; 
	   if($srckey !=""){    
       	 $info['where']  =  "project_id = '$project_id' AND party_id LIKE '%$srckey%' OR party_type LIKE '%$srckey%' OR name LIKE '%$srckey%'";
	   }else{
		$info['where']  =  "project_id = '$project_id'";
	  }
	   $info['orderby'] = array("party_id asc LIMIT $from,$to");
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
      
   function getTotalPartyList($from,$to) {  

   	 if($from == "" && $to == ""){$from=0; $to=20;}		
	  $project_id 		= getFromSession('project_id');
	  $srckey 	= getRequest('srckey');	 
  	  $data            = array();	  
  	  $info            = array();
	  $info['table']   = PARTY_INFO_TBL; 
	  if($srckey !=""){    
       	 $info['where']  =  "project_id = '$project_id' AND party_id LIKE '%$srckey%' OR party_type LIKE '%$srckey%' OR name LIKE '%$srckey%'";
	  }else{
		$info['where']  =  "project_id = '$project_id'";
	  }
	  $info['orderby'] = array("party_id asc LIMIT $from,$to");
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