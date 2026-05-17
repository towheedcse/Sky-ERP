<?php
class Bank
{
   function run()
   {         
      $cmd = getRequest('cmd');
	  $u_t_id = getFromSession('u_type_id');
      if(($u_t_id == 101)) //1 = admin 2 = Sales man 3 = employer 4 = Customer 5 = Jobs Seeker
      {

      	switch ($cmd)
      	{
      	   case 'add'					: $this->AddEditBank(getRequest('bank_id')); break;
		   case 'edit'					: $this->AddEditBank(getRequest('bank_id')); break;
      	   default                   	:$cmd = 'list'; $screen = $this->AddEditBank(getRequest('bank_id'));   break;

      	}

      }else if(($u_t_id == 107)) //1 = admin 2 = Sales man 3 = employer 4 = Customer 5 = Jobs Seeker
      {

      	switch ($cmd)
      	{
      	   case 'add'					: $this->AddEditBank(getRequest('bank_id')); break;
		   case 'edit'					: $this->AddEditBank(getRequest('bank_id')); break;
      	   default                   	:$cmd = 'list'; $screen = $this->AddEditBank(getRequest('bank_id'));   break;

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

     
   function AddEditBank($bank_id = null) {
      
   	   $data                	= array();
       if(getRequest('save'))
       {          
		  if(getRequest('bank_id')==""){
		  	$this->insertBank();
		  }else{
		  	$this->updateBank();
		  }
       }   
	   if(getRequest('bank_id')!=""){
	   		$TBDArr				= $this->getBankList(getRequest('bank_id'));      
	   		$TBDArr 			= parseThisValue($TBDArr);
			$data        		= array_merge(array(),$TBDArr);   
	   } 
	   $data['bank_list'] 		= $this->getBankList();	
	   $data['cmd']         	= getRequest('cmd');   
	   require_once(CURRENT_APP_SKIN_FILE); 
	   return $data[0];
   }
   //====================
 	function insertBank()
 	{     
 	  $bank_id 	= getRequest('bank_id');
		  
	  $requestdata 						= array();
      $requestdata 						= getUserDataSet(BANK_TBL);  
	  $requestdata['project_id']		= getFromSession('project_id');    
	  $requestdata['created_by']        = getFromSession('userid');
	  $requestdata['created_date']      = date('Y-m-d h:i:s');	     
	  $requestdata['modified_by']       = getFromSession('userid');
	  $requestdata['modified_time']     = date('Y-m-d h:i:s');   
	  $bank_id = $this->createID();  
	  if($bank_id != -1)
      {
      	$requestdata['bank_id']   	= $bank_id;
      }
      else
      {
      	$msg = "ID overflow !!!";
      	header("location:index.php?app=user_home&msg=$msg");
      	exit;
      }
   	  $info        						=  array();
      $info['table']					= BANK_TBL; 
	  $info['data'] 					= $requestdata;  
	  //$info['debug']  					=  true;  
	  $res = insert($info); 
	  
      if($res)
      {
        header("location:?app=bank&cmd=add");
      }else{
	  	header("location:?app=bank&cmd=edit&bank_id=".$bank_id);
	  }       

   }//EOFn   
   
	function updateBank()
	{     
	  $bank_id 	= getRequest('bank_id');	
	  $requestdata = array();
	  $requestdata = getUserDataSet(BANK_TBL); 
	  $requestdata['modified_by']       = getFromSession('userid');
	  $requestdata['modified_time']     = date('Y-m-d h:i:s');    
	  
	  $info        						=  array();
	  $info['table']					= BANK_TBL; 	
	  //dBug($requestdata);
	  $info['data'] 					= $requestdata;
	  $info['where']					= "bank_id ='".$bank_id."'";  
	  $info['debug']  					=  false;    
	  $res = update($info);
	  
	  if($res)
	  {
		header("location:?app=bank&cmd=add");
	  }else{
		header("location:?app=bank&cmd=edit&bank_id=".$bank_id);
	  }         
	
	}//EOFn  
	
	function getBankList($bank_id=null)
	{
	   if($from == "" && $to == ""){$from=0; $to=40;}  
	   $data            = array();	  
	   $info            = array();
	   $info['table']   = BANK_TBL;	
	   $project_id = getFromSession('project_id');
	   if($bank_id!=""){				
			$info['where']   = "bank_id = '".$bank_id."' AND project_id='".$project_id."'";
	   }else{

		$info['where']  =  "project_id='".$project_id."'";

	   }    
	   $info['orderby'] = array("bank_id asc LIMIT $from,$to");
	   $info['debug']   = false;			 
	
	   $res            =	select($info);   
	
	   if(count($res))
	   {
		  foreach($res as $i=>$v)
		  {
			 $data[$i] = $v;
		  }
	   }
	   if($bank_id==""){
		return $data; // for list
	  }else{
		return $data[0];	// for view
	  }
	
	}
   function createID()
   {
      $info = array();
      $info['table'] = BANK_TBL;
      $info['fields'] = array('max(bank_id) as maxbank');
      
      $res = select($info);
      
      $maxbankId = 'B000';
      
      if(count($res))
      {
         foreach($res as $v)
         {
         	 if($v->maxbank)
         	 {
             $maxbankId = $v->maxbank;
             }
             break;   	
         }
      
      }
      
      $maxbankId = generateID("B",$maxbankId,4);
      return $maxbankId;
   }  
   
} // End class


?>

