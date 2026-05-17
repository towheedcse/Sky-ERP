<?php
class AdvancedPaymentVouchar
{
   
   function run()
   {         
      $cmd = getRequest('cmd');
	  $u_t_id = getFromSession('u_type_id');

      if( ($u_t_id == 101) || ($u_t_id == 102) || ($u_t_id == 104)) //101 = sysadmin  102 = admin, 104= ac
      {

      	switch ($cmd)
      	{
      	   case 'add'                	: $screen = $this->showEditor($msg); break;
      	   case 'edit'               	: $screen = $this->showEditor("Edit Page");    break;	
      	   case 'vouchar_print'         : $screen = $this->showPrintEditor(getRequest('voucher_no'));   break;
		   case 'list'               	: $screen = $this->showList($msg);   break;
      	   default                   	: $screen = $this->showEditor($msg);   break;

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
	  $data                				= array();
	  $data['cmd']         				= getRequest('cmd');
	  $data['TotalDebitAmount']			= $this->getTotalDebitAmount();
	  $data['TotalCreditAmount']		= $this->getTotalCreditAmount(); 
	   if(getRequest('deleted')=='yes') {
		  $data['message'] = "Item Deleted Successfully";
	   }elseif(getRequest('deleted')=='no') {
		  $data['message'] = "Item Not Deleted";
	   }
	   require_once(CURRENT_APP_SKIN_FILE); 
	   return $data[0];
   }
   
	function showPrintEditor($ID) { 
	  if ($ID) {
         $advArr = $this->getDebitVoucharDetails($ID);
         $advArr = parseThisValue($advArr);  
         $data   = array_merge(array(), $advArr); 
		 $data['message'] = $msg;
      	 $data['cmd']     = getRequest('cmd');
	  	 require_once(VOUCHAR_PRINT_SKIN);
      }else{
		require_once(PRINT_VOUCHAR_SKIN);
	  }	        

      return true;

   }
   

//================ End Due Received List ===============
   function showEditor($msg = null) { 
      $ID = getRequest('id');
	  if ($ID) {
         $advArr = $this->getAccJournalInfo($ID);
         $advArr = parseThisValue($advArr);  
         $data   = array_merge(array(), $advArr); 
      }else{
         if(getRequest('submit')){
            $this->saveDebitVouchar();	
         }
      }	 
	  $data['head_list']   				= $this->getSubAccHeadList();	
	  $data['currency_list']   	 		= $this->getCurrencyList();
      $data['message'] = $msg;
      $data['cmd']     = getRequest('cmd');
	  require_once(CURRENT_APP_SKIN_FILE);      
      return true;

   }
//==================== saveDebitVouchar ====================
 	function saveDebitVouchar()
 	{     
		  $requestdata = array();
		  $mode_of_payment = getRequest('mode_of_payment');	 
		  $requestdata = getUserDataSet(DEVIT_VOUCHAR_TBL);	
		  if($mode_of_payment =="Check"){
		  $requestdata['mode_of_payment'] 	= "Bank";
		  }
		  $requestdata['account_head']      = getRequest('dr_account');   		  	  		    
	  	  $requestdata['head_type']     	= getHeadType(getRequest('dr_account'));   
		  $requestdata['debit']        		= getRequest('amount');    
		  $requestdata['credit']        	= 0; 
     	  
		  $requestdata['project_id']        = getFromSession('project_id');    
		  $requestdata['created_by']        = getFromSession('userid'); 
		  $requestdata['created_date']      = formatDate(getRequest('created_date'));
		  //$requestdata['created_date']      = date('Y-m-d h:i:s');
	  	  $requestdata['paid_amount']   = getRequest('amount'); 
		  $voucher_no = $this->createVoucharID();
	
		 if($voucher_no != -1){
			$requestdata['voucher_no']   	= $voucher_no;
		  }else{
			$msg = "ID overflow !!!";
			header("location:index.php?app=user_home&msg=$msg");
			exit;
		  }
		  $info        		=  array();
		  $info['table']	= DEVIT_VOUCHAR_TBL;
		  $info['data'] 	= $requestdata;     
		  //$info['debug']  	=  true;
		  $res = insert($info);
		  if($res['affected_rows']) {
			$this->saveCreditVouchar($voucher_no);
		  }else {	
			header("location:index.php?app=journal&cmd=add");	
		  }  
	 

    }//EOFn  

    function saveCreditVouchar($voucher_no)
 	{     
	  $requestdata = array();
	  $mode_of_payment = getRequest('mode_of_payment');	
	  $requestdata = getUserDataSet(CREDIT_VOUCHAR_TBL);
	  if($mode_of_payment =="Check"){
	  $requestdata['mode_of_payment'] 	= "Bank";
	  }
	  $requestdata['account_head']      = getRequest('cr_account');    		  	  		    
	  $requestdata['head_type']     	= getHeadType(getRequest('cr_account'));   
	  $requestdata['debit']        		= 0; 
	  $requestdata['credit']        	= getRequest('amount'); 
	  $requestdata['project_id']        = getFromSession('project_id');    
	  $requestdata['created_by']        = getFromSession('userid'); 			 
	  $requestdata['created_date']      = formatDate(getRequest('created_date'));
	  //$requestdata['created_date']      = date('Y-m-d h:i:s');	
	  $requestdata['voucher_no']   		= $voucher_no;
	  $info        		=  array();
	  $info['table']	= CREDIT_VOUCHAR_TBL;
	  $info['data'] 	= $requestdata;     
	  $info['debug']  	=  true;
	  $res = insert($info);
		

	  if($res['affected_rows']) {
		$CrAmount = getRequest('amount');
		$created_date = formatDate(getRequest('created_date'));
		$project_id = getFromSession('project_id');	
		//======= Dr Account ======	
		$DrHType = getHeadType(getRequest('dr_account')); 
		$PartyAcc_head = getRequest('dr_account'); 
		$totalPartyCR  = $this->getTotalCreditAmount($PartyAcc_head,getFromSession('project_id'));
		$totalPartyDR  = $this->getTotalDebitAmount($PartyAcc_head,getFromSession('project_id'));					 
		$PartyBalance  = ($totalPartyDR-($totalPartyCR+$CrAmount));					 
		$this->saveAccountJournal($voucher_no,$PartyAcc_head,$DrHType,getFromSession('project_id'),getRequest('description'),$CrAmount,0,$PartyBalance,1,$created_date);	

		//============== Cr Account ===============
		$CrHType = getHeadType(getRequest('cr_account')); 
		$acc_head = getRequest('cr_account'); 
		$totalCR  = $this->getTotalCreditAmount($acc_head,getFromSession('project_id'));
		$totalDR  = $this->getTotalDebitAmount($acc_head,getFromSession('project_id'));					 
		$balance  = ($totalDR-($totalCR+$CrAmount));					 
		$this->saveAccountJournal($voucher_no,$acc_head,$CrHType,getFromSession('project_id'),getRequest('description'),0,$CrAmount,$balance,1,$created_date);	
		//====== Make Party Receibavle ======
		if($CrAmount>0){
		$supplier = getRequest('dr_account'); $return_date = $requestdata['created_date']; $return_by = getFromSession('userid'); 
		$currency = getRequest('currency');	  $vouchar_type = getRequest('vouchar_type');
		$RMSQL="INSERT INTO ".PURCHASE_RETURN_RECEIBAVLE_TBL."(voucher_no,project_id,supplier,currency,return_amount,paid_amount,due,payment_source,return_date,created_by)  	VALUES('$voucher_no','$project_id','$supplier','$currency','$CrAmount','0','$CrAmount','$vouchar_type','$return_date','$return_by')";
		mysql_query($RMSQL); 
		}
		/*
		//=========== Cr Capital =======
		$transaction_type = getRequest('transaction_type');
		if($transaction_type=="Administrative Cost"){	
		 $capital_head = $this->getCapitalId(getFromSession('project_id'));
		 $totalCapitalCR  = $this->getTotalCreditAmount($capital_head,getFromSession('project_id'));
		 $totalCapitalDR  = $this->getTotalDebitAmount($capital_head,getFromSession('project_id'));					 
		 $Capitalbalance  = ($totalCapitalDR-($totalCapitalCR+$DrAmount));					 
		 $this->saveAccountJournal($voucher_no,$capital_head,"Acc",$project_id,getRequest('description'),0,$DrAmount,$Capitalbalance,0,$created_date);	
		} 
		*/			
		header("location:index.php?app=journal&cmd=vouchar_print&voucher_no=".$voucher_no);
	  }else {	
		header("location:index.php?app=journal&cmd=add");

	  }  
	 

    }//EOFn      

	function saveAccountJournal($voucher_no,$sub_id,$head_type,$project_id,$description,$DR=NULL,$CR=NULL,$balance,$status,$created_date){
		$sql = "INSERT INTO ".ACCOUNT_JOURNAL_TBL." (voucher_no,created_date,sub_id,head_type,project_id,description,dr,cr,balance,status) VALUES('".$voucher_no."','".$created_date."','".$sub_id."','".$head_type."','".$project_id."','".$description."','".$DR."','".$CR."','".$balance."','".$status."')";
		mysql_query($sql);
	}

   function getDebitVoucharDetails($voucher_no)
   {

   	   $data           =  array();                  

       $info           =  array();     

       $info['table']  =  DEVIT_VOUCHAR_TBL;

       $info['where']  =  "voucher_no='".$voucher_no."' ";

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

	// ==== function createVoucharID ===============

    function createVoucharID()
   {
      $info = array();
      $info['table'] = DEVIT_VOUCHAR_TBL;
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
   
   function getSubAccHeadList()
   {

      $info            = array();
 	  $project_id 	   = getFromSession('project_id');
      $info['table']   = SUB_ACC_HEAD_TBL;

      $info['fields']  = array('sub_id', 'sub_head_name','head_details','head_type'); 	
      $info['where']   =  "project_id = '$project_id'"; 
	  $info['orderby'] = array("sub_head_name ASC");

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

   

   function getCurrencyList()
   {

      $info            = array();

      $info['table']   = CURRENCY_TBL;

      //$info['fields'] = array('currency_id', 'name'); 

	  $info['orderby'] = array("currency_name ASC");

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
   function getCashId($project_id){
		$sql = "SELECT sub_id FROM ".SUB_ACC_HEAD_TBL." WHERE head_type ='Cash' AND project_id = '$project_id'";

		$row = mysql_fetch_object(mysql_query($sql));

		return $sub_id = $row->sub_id;
	}
   function getCapitalId($project_id){
		$sql = "SELECT sub_id FROM ".SUB_ACC_HEAD_TBL." WHERE head_type ='Capital' AND project_id = '$project_id'";

		$row = mysql_fetch_object(mysql_query($sql));

		return $sub_id = $row->sub_id;
	}
	function getRecievableId($project_id){
		$sql = "SELECT sub_id FROM ".SUB_ACC_HEAD_TBL." WHERE sub_head_name = 'Recievable' AND project_id = '$project_id'";

		$row = mysql_fetch_object(mysql_query($sql));

		return $sub_id = $row->sub_id;
	}
	function getPayableId($project_id){
		$sql = "SELECT sub_id FROM ".SUB_ACC_HEAD_TBL." WHERE sub_head_name = 'Payable' AND project_id = '$project_id'";

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

//=============End =============

} // End class
?>