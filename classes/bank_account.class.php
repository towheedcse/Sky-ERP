<?php
class BankAccount
{
   function run()
   {         

      $cmd = getRequest('cmd');
	  $u_t_id = getFromSession('u_type_id');
      if(($u_t_id == 101)) // 1 = sysadmin, 2 = admin, 3 = project admin
      {

      	switch ($cmd)
      	{
      	   case 'add'					: $this->AddEditBankAccount(); break;
		   case 'edit'					: $this->AddEditBankAccount(); break;
		   case 'doUpdate'				: $this->updateBankAccount(); break;
		   case 'delete'             	: $screen = $this->deleteRecord(getRequest('id')); break;
      	   default                   	:$cmd = 'list'; $screen = $this->AddEditBankAccount();   break;

      	}

      }else if(($u_t_id == 107)) // 1 = sysadmin, 2 = admin, 3 = project admin
      {

      	switch ($cmd)
      	{
      	   case 'add'					: $this->AddEditBankAccount(); break;
		   case 'edit'					: $this->AddEditBankAccount(); break;
		   case 'doUpdate'				: $this->updateBankAccount(); break;
		   
      	   default                   	:$cmd = 'list'; $screen = $this->AddEditBankAccount();   break;

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

     
   function AddEditBankAccount($bank_account_no = null) {
      
   	   $data                	= array();
       if(getRequest('save'))
       {          
		  if(getRequest('id')==""){
		  	$this->insertBankAccount();
		  }else{
		  	$this->updateBankAccount();
		  }
       }   
	   if(getRequest('bank_account_no')!=""){
	   		$TBDArr				= $this->getBankAccountList(getRequest('bank_account_no'));      
	   		$TBDArr 			= parseThisValue($TBDArr);
			$data        		= array_merge(array(),$TBDArr);   
	   } 
	   $data['bankaccount_list']= $this->getBankAccountList();	 
	   $data['bank_list'] 		= $this->getBankList();	
	   $data['cmd']         	= getRequest('cmd');   
	   require_once(CURRENT_APP_SKIN_FILE); 
	   return $data[0];
   }
   //====================
 	function insertBankAccount()
 	{     
 	    
	  $requestdata 						= array();
      $requestdata 						= getUserDataSet(BANK_ACCOUNT_TBL);    
	  $requestdata['project_id']        = getFromSession('project_id');     
	  $requestdata['created_by']        = getFromSession('userid');
	  $requestdata['created_date']      = date('Y-m-d h:i:s');	     
	  $requestdata['modified_by']       = getFromSession('userid');
	  $requestdata['modified_time']     = date('Y-m-d h:i:s');   
	  
   	  $info        						=  array();
      $info['table']					= BANK_ACCOUNT_TBL; 
	  $info['data'] 					= $requestdata;  
	  $info['debug']  				=  true;  
	  $res = insert($info); 
	  
      if($res)
      {
        header("location:?app=bank_account&cmd=add");
      }else{
	  	header("location:?app=bank_account&cmd=add");
	  }       

   }//EOFn   
   
	function updateBankAccount()
	{     
	  $bank_account_no 	= getRequest('id');	
	  $requestdata = array();
	  $requestdata = getUserDataSet(BANK_ACCOUNT_TBL); 
	  $requestdata['modified_by']       = getFromSession('userid');
	  $requestdata['modified_time']     = date('Y-m-d h:i:s');    
	  
	  $info        						=  array();
	  $info['table']					= BANK_ACCOUNT_TBL; 	
	  //dBug($requestdata);
	  $info['data'] 					= $requestdata;
	  $info['where']					= "bank_account_no ='".$bank_account_no."'";  
	   $info['debug']  					=  false;    
	  $res = update($info);
	  
	  if($res)
	  {
		header("location:?app=bank_account&cmd=add");
	  }else{
		header("location:?app=bank_account&cmd=edit&bank_account_no=".$bank_account_no);
	  }         
	
	}//EOFn  
	
	function getBankAccountList($bank_account_no=null)
	{
	   if($from == "" && $to == ""){$from=0; $to=40;}  
	  $project_id 		= getFromSession('project_id');
	   $data            = array();	  
	   $info            = array();
	   $info['table']   = BANK_ACCOUNT_TBL.' ba,'.BANK_TBL.' b';	
	   $info['fields'] = array('ba.bank_code','b.bank_name','ba.bank_account_no','ba.branch_location','ba.account_name','ba.account_type','ba.phone','ba.fax');
	   if($bank_account_no!=""){				
			$info['where']   = "ba.bank_code = b.bank_id AND ba.bank_account_no = '".$bank_account_no."' AND ba.project_id='$project_id'";
	   }else{
			$info['where']   = "ba.bank_code = b.bank_id AND ba.project_id='$project_id'";
	   }    
	   $info['orderby'] = array("ba.bank_account_no asc LIMIT $from,$to");
	   //$info['debug']   = true;			 
	
	   $res            =	select($info);   
	
	   if(count($res))
	   {
		  foreach($res as $i=>$v)
		  {
			 $data[$i] = $v;
		  }
	   }
	   if($bank_account_no==""){
		return $data; // for list
	  }else{
		return $data[0];	// for view
	  }
	
	}
   function getBankList()
   {	
      $data 			= array(); 
      $info        		=  array();	
	  $project_id 		= getFromSession('project_id');
      $info['table']	= BANK_TBL;
	  $info['fields'] = array('bank_id','bank_name');
	  $info['where']  =  "project_id='".$project_id."'";
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
  function deleteRecord($id)
  {
   	  if(getRequest('id'))
      { 
      	$info = array();
      	$info['table'] = BANK_ACCOUNT_TBL;
      	$info['where'] = "bank_account_no='$id'";
      	$info['debug'] = false;
      	$res = delete($info);      	

      	if($res)
      	{
      	  $msg="Successfully delete Record !!!";
          header("location:?app=bank_account&cmd=view&msg=$msg");     	   

      	} else{
      		 header("location:?app=bank_account&cmd=view&cmd=list&deleted=no");
      	}      	

      }

   } 
 
   
} // End class


?>

