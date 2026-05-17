<?php
class DeliveryProduct
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
		   case 'print_vouchar'			: $screen = $this->showPrintEditor($msg); break;  
		   case 'print_summary'			: $screen = $this->showPrintHimmagarSummary($msg); break;      
		   case 'loan'					: $screen = $this->showLoanEditor($msg); break;        	   	   
      	   case 'list'               	: $screen = $this->showList($msg);  break;
		   case 'get_booking_info'		: $this->showBookingList();  break;
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
   		$data = array();
   	  	$ID = getRequest('srckey');
	  	if ($ID) {
         	$advArr = $this->getPartyInfo($ID);
         	$advArr = parseThisValue($advArr);  
         	$data   = array_merge(array(), $advArr); 
      	}       
	  
	  	$data['record_list'] 	= $this->getProductReceivedList(getRequest('from'),getRequest('to'));    
	   	require_once(CURRENT_APP_SKIN_FILE);
	   	return true;
	   
   }   
   function showEditor($msg = null) { 
     	  
      $data 		= array();
   	  $received_id 	= getRequest('id');
	  if ($received_id) {
	  		$credit_amount 				= $this->getDeedWiseTotalPartyCreditAmount($received_id); 
			$delivery_bag_qty 			= $this->getDeedWiseTotalDeliveryBag($received_id);
         	$advArr 					= $this->getProductReceivedInfo($received_id);
         	$advArr 					= parseThisValue($advArr); 
			$data   					= array_merge(array(), $advArr); 
			$data['credit_amount'] 		= $credit_amount; 
			$data['total_delivery_bag'] = $delivery_bag_qty;
      }
	 if(getRequest('submit'))
	 {
		$this->addDelivery();	
	 }
      	 
      $data['message'] = $msg;
      $data['cmd']     = getRequest('cmd');
	  require_once(CS_DELIVERY_ADD_EDIT_SKIN);      
      return true;
   }
   
   function showPrintEditor($msg = null) {      	  
      
	  $received_id 	= getRequest('received_id');  
	  if ($received_id) {
	  		$credit_amount 				= $this->getDeedWiseTotalPartyCreditAmount($received_id); 
			$delivery_bag_qty 			= $this->getDeedWiseTotalDeliveryBag($received_id);
         	$advArr 					= $this->getProductReceivedInfo($received_id);
         	$advArr 					= parseThisValue($advArr); 
			$data   					= array_merge(array(), $advArr); 
			$data['credit_amount'] 		= $credit_amount; 
			$data['total_delivery_bag'] = $delivery_bag_qty;
      }
      
      $data['message'] = $msg;
      $data['cmd']     = getRequest('cmd');
	  require_once(CS_DELIVERY_INVOICE_SKIN);      
      return true;
   }
   function showLoanEditor($msg = null) { 
     	  
      $data 		= array();
   	  $received_id 	= getRequest('id');
	  if ($received_id) {
	  		$credit_amount 				= $this->getDeedWiseTotalPartyCreditAmount($received_id); 
			$delivery_bag_qty 			= $this->getDeedWiseTotalDeliveryBag($received_id);
         	$advArr 					= $this->getProductReceivedInfo($received_id);
         	$advArr 					= parseThisValue($advArr); 
			$data   					= array_merge(array(), $advArr); 
			$data['credit_amount'] 		= $credit_amount; 
			$data['total_delivery_bag'] = $delivery_bag_qty;
      }
	 if(getRequest('submit'))
	 {
		$this->addDelivery();	
	 }
      	 
      $data['message'] = $msg;
      $data['cmd']     = getRequest('cmd');
	  require_once(CS_LOAN_RECEIVED_SKIN);      
      return true;
   }
    function showPrintHimmagarSummary(){
		require_once(CS_HIMMAGAR_SUMMARY_SKIN);  
	}
 
   function addLoan($voucher_no)
   {    	  
	  $received_id    = getRequest('received_id');  
	  $Cr = getRequest('debit'); 
	  $requestdata = array();
      $requestdata = getUserDataSet(CS_LOAN_RECEIVED_TBL);	
      //dumpvar($requestdata);	
	 
	  $requestdata['account_head']      = getRequest('customer_code');                         
      $requestdata['received_id']       = getRequest('received_id');
      $requestdata['project_id']        = getFromSession('project_id');                         
      $requestdata['credit']        	= $Cr;
      $requestdata['created_by']        = getFromSession('userid');
	  $requestdata['created_date']      = date('Y-m-d h:i:s');
	  $requestdata['voucher_no']   		= $voucher_no;
	  	   
      $info        		=  array();
      $info['table']	= CS_LOAN_RECEIVED_TBL;
      $info['data'] 	= $requestdata;     
     // $info['debug']  	=  true;                     
      $res = insert($info);
     
   } 
   function addDelivery($msg = null)
   {    	  
	  $received_id    = getRequest('received_id');   
	  $service_charge = getRequest('service_charge');  
	  $CR = getRequest('debit');
	  $CR = $CR - $service_charge;  
	  $requestdata = array();
      $requestdata = getUserDataSet(CS_DELIVERY_PRODUCT_TBL);	
      //dumpvar($requestdata);	
	  $voucher_no = $this->createID();
	  $requestdata['account_head']      = getRequest('customer_code');                         
      $requestdata['received_id']       = getRequest('received_id');
      $requestdata['project_id']        = getFromSession('project_id');                         
      $requestdata['credit']        	= $CR;
	  $requestdata['debit']        		= 0;
      $requestdata['created_by']        = getFromSession('userid');
	  $requestdata['created_date']      = date('Y-m-d h:i:s');
	  if($voucher_no != -1)
      {
      	$requestdata['voucher_no']   	= $voucher_no;
      }
      else
      {
      	$msg = "ID overflow !!!";
      	header("location:index.php?app=user_home&msg=$msg");
      	exit;
      }
	   
      $info        		=  array();
      $info['table']	= CS_DELIVERY_PRODUCT_TBL;
      $info['data'] 	= $requestdata;     
      //$info['debug']  	=  true;                     
      $res = insert($info);
	
	  if($res['affected_rows']) {	
		 $mode_of_payment = getRequest('mode_of_payment');
		 if($mode_of_payment=="Cash"){  
			 $acc_head = $this->getCashId(getFromSession('project_id'));
			 $totalCR  = $this->getTotalCreditAmount($acc_head,getFromSession('project_id'));
   			 $totalDR  = $this->getTotalDebitAmount($acc_head,getFromSession('project_id'));
			 $DR = $CR;
			 $balance = (($totalDR+$DR)-$totalCR);
			 $CR = 0; 
			 $this->saveAccountJournal($voucher_no,$acc_head,getFromSession('project_id'),getRequest('description'),$DR,$CR,$balance);
			 if(getRequest('only_loan')=="only loan"){
			 	$this->addLoan($voucher_no);	
			 }
		 } 		 
	  	 header("location:index.php?app=cs_delivery_product&cmd=print_vouchar&received_id=".$received_id);
	  }else {	 
	    header("location:index.php?app=cs_delivery_product&cmd=add");
	  }      
   }
	
	function saveAccountJournal($voucher_no,$sub_id,$project_id,$description,$DR=NULL,$CR=NULL,$balance){
		$created_date = date('Y-m-d');
		$sql = "INSERT INTO ".ACCOUNT_JOURNAL_TBL." (voucher_no,created_date,sub_id,head_type,project_id,description,dr,cr,balance) VALUES('".$voucher_no."','$created_date','".$sub_id."','Party','".$project_id."','".$description."','".$DR."','".$CR."','".$balance."')";
		mysql_query($sql);
	}

   function createID()
   {
      $info = array();
      $info['table'] = CS_DELIVERY_PRODUCT_TBL;
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
   	
   function getPartyInfo($id)
   {	  
      $project_id     = getFromSession('project_id'); 
   	  $data           =  array();
	  $info           = array();    
	  $info['table']  =  PARTY_INFO_TBL;	
	  $info['fields'] = array('name','fname','mname','mobile','address');
	  $sql="party_id = '".$id."' AND project_id = '$project_id'";
	  $info['where']  =$sql;	  	
	  //$info['debug']  =  true;                     
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
   
   function getDeedWiseTotalPartyCreditAmount($received_id){
   		$this_year 		= date('Y');
        $project_id   	= getFromSession('project_id'); 
   		$sql = "SELECT sum(`credit`) as credit_amount FROM ".CS_DELIVERY_PRODUCT_TBL." WHERE `transaction_type`='Recieved' AND received_id = '$received_id' AND project_id = '$project_id' AND created_date LIKE '%$this_year%'";
		$row = mysql_fetch_object(mysql_query($sql));
		$credit_amount = $row->credit_amount;
		if(empty($credit_amount)){
			$credit_amount = 0;
		}
		return $credit_amount;
   }
   function getDeedWiseTotalDeliveryBag($received_id){
   		$this_year 		= date('Y');
        $project_id   	= getFromSession('project_id'); 
   		$sql = "SELECT sum(`delivery_bag_qty`) as delivery_bag_qty FROM ".CS_DELIVERY_PRODUCT_TBL." WHERE `transaction_type`='Recieved' AND received_id = '$received_id' AND project_id = '$project_id' AND created_date LIKE '%$this_year%'";
		$row = mysql_fetch_object(mysql_query($sql));
		$delivery_bag_qty = $row->delivery_bag_qty;
		if(empty($delivery_bag_qty)){
			$delivery_bag_qty = 0;
		}
		return $delivery_bag_qty;
   }
   function getProductReceivedInfo($id) {  

		$srckey 		= getRequest('srckey');
		$this_year 		= date('Y');
        $project_id   	= getFromSession('project_id'); 
					
		$info           = array();    
		$info['table']  =  CS_PRODUCT_RECEIVED_TBL.' pr,'.CUSTOMER_BOOKING_TBL.' cb,'.PARTY_INFO_TBL.' p';	
		$info['fields'] = array('pr.received_id','pr.booking_id','pr.customer_code','pr.agent_code','pr.received_bag_qty','pr.rent_per_bag','pr.product_details','pr.floor_no','pr.room_no','pr.row_no','pr.cabin_no','pr.sr_no','pr.posting_sr','p.name','p.fname','p.mname','p.mobile','p.address','cb.booking_id','cb.bag_qty as booking_bag_qty','cb.carring_per_bag','cb.total_carring_cost','cb.carring_interest','cb.empty_bag_qty','cb.empty_bag_u_price','cb.total_empty_bag_cost','cb.booking_amount','cb.created_date as booking_date');
		
		$sql="pr.list_view='Active' AND  project_id = '$project_id' AND pr.booking_id = cb.booking_id AND cb.customer_code = p.party_id AND pr.received_id = '".$id."' AND pr.created_date LIKE '%$this_year%'";
							
		$info['where']  =$sql;	  	
	    $info['groupby'] = array("pr.received_id");
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
   
        
   function getProductReceivedList($from,$to) {  

		if($from == "" && $to == ""){$from=0; $to=1000;}
		$srckey 		= getRequest('srckey');
		$this_year 		= date('Y');
        $project_id   	= getFromSession('project_id'); 
		
		$info           = array();    
		$info['table']  =  CS_PRODUCT_RECEIVED_TBL.' pr,'.CUSTOMER_BOOKING_TBL.' cb,'.PARTY_INFO_TBL.' p';	
		$info['fields'] = array('pr.received_id','pr.booking_id','pr.customer_code','pr.agent_code','pr.received_bag_qty','pr.rent_per_bag','pr.product_details','pr.floor_no','pr.room_no','pr.row_no','pr.cabin_no','pr.sr_no','pr.posting_sr','p.name','cb.booking_id','cb.total_carring_cost','cb.carring_interest','cb.total_empty_bag_cost','cb.booking_amount','cb.created_date as booking_date');
		
		$sql="pr.list_view='Active' AND  project_id = '$project_id' AND pr.booking_id = cb.booking_id AND cb.customer_code = p.party_id ";
		
		if($srckey!=""){
			$sql.=" AND ( pr.received_id LIKE '%$srckey%' OR p.name LIKE '%$srckey%' OR p.party_id LIKE '%$srckey%' OR p.party_type LIKE '%$srckey%' OR pr.booking_id LIKE '%$srckey%') AND pr.created_date LIKE '%$this_year%'";
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
	function getCashId($project_id){
		$sql = "SELECT sub_id FROM ".SUB_ACC_HEAD_TBL." WHERE sub_head_name = 'Cash' AND project_id = '$project_id'";

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
             	
} // End class
?>