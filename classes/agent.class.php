<?php
class Agent
{
   function run()
   {     

		$cmd = getRequest('cmd');
		$u_t_id = getFromSession('u_type_id');
	
		if($u_t_id == 101) 
		{      
		  switch($cmd)
		  { 
		  	 case 'add'                	: $screen = $this->showEditor($msg); break;
      	     case 'edit'               	: $screen = $this->showEditor("Edit Page");    break;			 
      	   	 case 'doUpdate'           	: $this->updateRecord(); break;
		     case 'delete'             	: $screen = $this->deleteRecord(getRequest('id')); break;
			 default                   	: $cmd = 'list'; $screen = $this->showEditor($msg);   break;
	
		  }
	
		}elseif($u_t_id == 103) 
		{      
		  switch($cmd)
		  { 
		  	 case 'view_profile'        : $screen = $this->viewProfileEditor($msg); break;
			 case 'new_user'        	: $screen = $this->userEditor($msg); break;
			 case 'view_user'        	: $screen = $this->showUserList($msg); break;
			 case 'bank_deposit'       	: $screen = $this->bankDepositEditor($msg); break;
			 case 'my_account_status'  	: $screen = $this->myAccountStatusEditor($msg); break;		 
			 case 'posting'        	: $screen = $this->postingAmountEditor($msg); break;
		  }
	
		}elseif($u_t_id == 2 || $u_t_id == 3) 
		{      
		  switch($cmd)
		  { 
		  	 case 'posting'        	: $screen = $this->postingAmountEditor($msg); break;
			 //case 'my_account_status'  	: $screen = $this->myAccountStatusEditor($msg); break;		 
			 
		  }
	
		}else {
      		header("location:index.php?app=user_home&msg=You are not authorised !!!");
      	} 

		
		if($cmd == 'list')
		{
			 if($deleted = getRequest('deleted'))
			 {
				if($deleted == 'yes')
				{
				   $screen['message'] = "Item Deleted Successfully";
				}
	
				else
				{
					  $screen['message'] = "Item Deletion Failure";
				}
			 }
			 require_once(CURRENT_APP_SKIN_FILE);
			 
		}
		return true;
   }
   
  // ======== Start Top Skills ==========       

  function showEditor()
  {
		 $party_id = getRequest('id');
		
		 if($party_id)
		 {		 			
			 if(getRequest('save'))
			 {  
				$this->updateRecord($party_id);
				$msg="Successfully Update Record !!!";
        		header("location:?app=agent&cmd=view&msg=$msg");		      	
			 } 

		 } else {
			
			if(getRequest('save')) {
				$this->insertRecord();
				$msg="Successfully Save Record !!!";
        		header("location:?app=agent&cmd=view&msg=$msg");	     		       		      	
			 }			 
		 }	 

	  $data                		= array();
	  $data['agent_list']  	= array($this->getRecords(getRequest('from'),getRequest('to')));       
      $data['district_list']     = $this->getDistrictList();
	  $data['totalrecord']  	= $this->getTotalRecords(); 
	  
	  $data['message'] 			= $msg;
	  $data['cmd']     			= getRequest('cmd'); 
	  require_once(CURRENT_APP_SKIN_FILE);
	  return $data[0];

   }

   function updateRecord($id)
   {       
	  $requestdata = array();
      $requestdata = getUserDataSet(PARTY_INFO_TBL); 
     
	  $district = getRequest('district');
	  $requestdata['date_of_birth']     = formatDate(getRequest('date_of_birth'));
   	  $info        						=  array();
      $info['table']					= PARTY_INFO_TBL; 	
      //dBug($requestdata);
	  $info['data'] 					= $requestdata;
	  $info['where']					= "party_id ='$id'";  
	   $info['debug']  					=  false;    
	  $res = update($info);
	  
      if(!$res) {
        header("location:?app=agent&cmd=view");
      }               

   }//EOFn 

   function insertRecord()
   {       
	  $requestdata 						= array();
      $requestdata 						= getUserDataSet(PARTY_INFO_TBL);    
      $party_id = $this->createID();
	  $requestdata['date_of_birth']      = formatDate(getRequest('date_of_birth'));
	 
	  $district = getRequest('district');
	  $requestdata['created_by']        = getFromSession('userid');
	  $requestdata['created_time']      = date('Y-m-d h:i:s');
	   
      if($$party_id != -1)
      {
      	$requestdata['party_id']   	= $party_id;
      }
      else
      {
      	$msg = "ID overflow !!!";
      	header("location:index.php?app=user_home&msg=$msg");
      	exit;
      }
	  
   	  $info        						=  array();
      $info['table']					= PARTY_INFO_TBL; 
	  $info['data'] 					= $requestdata;  
	  $info['debug']  					=  false;  
	  $res = insert($info); 
	  /*
      if($res)
      {
       $this->createUser($party_id);
      } 
	  */       

   }//EOFn   
   
   function getRecords($from=null,$to=null)
   {
	   if($from == "" && $to == ""){$from=0; $to=10;}  
	   $party_id = getRequest('party_id'); 
	   $srckey = getRequest('srckey');

  	   $data            = array();	  
  	   $info            = array();
	   $info['table']   = PARTY_INFO_TBL; 
	   if($party_id !=""){    
       	 $info['where']  =  "party_id='".$party_id."' ";
	   }elseif($srckey !=""){    

       	 $info['where']  =  "party_id = '".$srckey."' OR name like '".$srckey."%' OR mname like '".$srckey."%'";

	   }
	   $info['orderby'] = array("party_id asc LIMIT $from,$to");
	   $info['debug']   = false;			 

	   $res            =	select($info);   

	   if(count($res))
	   {
		  foreach($res as $i=>$v)
		  {
			 $data[$i][] = $v;
		  }
	   }
	   return $data;

  }
  
  function getTotalRecords()
   {	    
	   $party_id = getRequest('party_id');
  	   $data            = array();	  
  	   $info            = array();
	   $info['table']   = PARTY_INFO_TBL;
	   if($party_id !=""){    
       	 $info['where']  =  "party_id='".$party_id."' ";
	   }
	   $info['orderby'] = array('party_id ASC');
	   $info['debug']   = false;
	   $res            =	select($info);
	   if(count($res))
	   {
		  $total_job = count($res);
	   }                 

      return $total_job;

  }
  function deleteRecord($id)
  {
   	  if(getRequest('id'))
      { 
      	$info = array();
      	$info['table'] = PARTY_INFO_TBL;
      	$info['where'] = "party_id='$id'";
      	$info['debug'] = false;
      	$res = delete($info);      	

      	if($res)
      	{
      	  $msg="Successfully delete Record !!!";
          header("location:?app=agent&cmd=view&msg=$msg");     	   

      	} else{
      		 header("location:?app=agent&cmd=view&cmd=list&deleted=no");
      	}      	

      }

   } 
    // ==== function createJobSeekerID==================
   function createID()
   {
      $info = array();
      $info['table'] = PARTY_INFO_TBL;
      $info['fields'] = array('max(party_id) as maxagent');
      
      $res = select($info);
      
      $maxagentId = 'P000000';
      
      if(count($res))
      {
         foreach($res as $v)
         {
         	 if($v->maxagent)
         	 {
             $maxagentId = $v->maxagent;
             }
             break;   	
         }
      
      }
      
      $maxagentId = generateID("P",$maxagentId,7);
      return $maxagentId;
   }   
   
   function photoUpload($name, $id)
   {
       $source = $_FILES[$name]['tmp_name'];
       $name   = $_FILES[$name]['name'];
       $ext    = array_pop(explode('.', $name));
       $img_name = $id.'.'.$ext;
       //echo "<br>Name : ".$name;       
		$dest = "style image";		
       if(file_exists(IMAGES_DIR.'/'.$dest))
       {
          $dir = 1;
       }
       else
       {
         if(mkdir(IMAGES_DIR.'/'.$dest))
         {
            $dir = 1; 
         }
         else
         {
            $dir = 0;	
         } 	
       }      

       if($dir)
       {
       	  //$ext = array_pop(explode('.', $name));
          $arr = getimagesize($source);           
          if(is_array($arr))
          {            	              	                             
             $dest = $dest.'/'.$img_name;
             $this->file_dest = $dest;
             if(move_uploaded_file($source, IMAGES_DIR.'/'.$dest))
             {
                 return $dest; 
             }
          }else {
             echo "Not An Image";	
          }	
       }	
    }
//=============End Photo Upload=============
       
   function getDistrictList()
   {
   		$info            = array();
      $info['table']   = DISTRICT_TBL;
      $info['fields'] = array('district_name'); 
	  //$info['where'] = "district_name IN('Rangpur','Bogra')"; 
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
	function agentInfo()
    {	  
	   $party_id = getFromSession('party_id');
  	   $data            = array();	  
  	   $info            = array();
	   $info['table']   = PARTY_INFO_TBL.' a,'.USER_TBL.' u';
	     
       $info['where']  =  "a.party_id=u.party_id AND u.party_id = '".$party_id."' "; 
	   
	   $info['debug']   = false; 
	                       
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
//======== Create User ===========
   function createUser($party_id)
   {       
	  $requestdata 						= array();
      $requestdata 						= getUserDataSet(USER_TBL);    
      $requestdata['party_id']      	= $party_id;
	  $requestdata['userid']      		= getRequest('userid');
	  $requestdata['password']      	= md5(getRequest('password'));
	  $agent_code 						= getRequest('agent_code');
	  $district 						= getRequest('district');
	  $requestdata['district']      	= $district;
	  $requestdata['u_type_id']      	= "2"; // 2 is agent
	  $requestdata['agent_code']        = $district.'-'.$agent_code;
	  $requestdata['created_by']        = getFromSession('userid');
	  $requestdata['created_time']      = date('Y-m-d h:i:s');
	   
       
   	  $info        						=  array();
      $info['table']					= USER_TBL; 
	  $info['data'] 					= $requestdata;  
	  $info['debug']  					=  false;  
	  $res = insert($info); 
	      
   }//EOFn   
   
function viewProfileEditor($msg = null) { 
	      
	 $advArr = $this->agentInfo();
	 //dumpvar($employeeArr);
	 $advArr = parseThisValue($advArr);  
	 $data        = array_merge(array(), $advArr);                  

  
	 if(getRequest('save'))
	 {
		$this->updateMyProfile();	
	 }
        
      $data['message'] = $msg;

      $data['cmd']     = getRequest('cmd');    

	  //dumpVar($data);

      require_once(MY_PROFILE_SKIN);      

      return true;
   }
   function updateMyProfile()
   {  
   	  $party_id = getFromSession('party_id');     
	  $requestdata = array();
      $requestdata = getUserDataSet(PARTY_INFO_TBL); 
   	  $info        						=  array();
      $info['table']					= PARTY_INFO_TBL; 	
      //dBug($requestdata);
	  $info['data'] 					= $requestdata;
	  $info['where']					= "party_id ='$party_id'";  
	   $info['debug']  					=  false;    
	  $res = update($info);
	  
      if(!$res) {
        //header("location:?app=agent&cmd=view");
      }               

   }//EOFn 
   
   
  //================ new User ===============
  function userEditor()
  {
		 $userid = getRequest('id');
		
		 if($userid)
		 {		 			
			 if(getRequest('save'))
			 {  
				$this->updateUser($userid);
				$msg="Successfully Update Record !!!";
        		header("location:?app=agent&cmd=view_user&msg=$msg");		      	
			 } 

		 } else {
			
			if(getRequest('save')) {
				$this->insertUser();
				$msg="Successfully Save Record !!!";
        		header("location:?app=agent&cmd=view_user&msg=$msg");	     		       		      	
			 }			 
		 }	 

	  $data                		= array();
	  //$data['user_list']  	= array($this->getRecords(getRequest('from'),getRequest('to')));
	  
	  $data['message'] 			= $msg;
	  $data['cmd']     			= getRequest('cmd'); 
	  require_once(USER_SKIN_FILE);
	  return $data[0];

   }

   function updateUser($id)
   {       
	  $requestdata = array();
      $requestdata = getUserDataSet(USER_TBL);      
	  $requestdata['user_date_of_birth']     = formatDate(getRequest('user_date_of_birth'));
   	  $info        						=  array();
      $info['table']					= USER_TBL; 	
      //dBug($requestdata);
	  $info['data'] 					= $requestdata;
	  $info['where']					= "userid ='$id'";  
	   $info['debug']  					=  false;    
	  $res = update($info);
	  
      if(!$res) {
        header("location:?app=agent&cmd=view_user");
      }               

   }//EOFn 

   function insertUser()
   {       
	  $requestdata 						= array();
      $requestdata 						= getUserDataSet(USER_TBL);    
	  $requestdata['user_date_of_birth']     = formatDate(getRequest('user_date_of_birth'));
	  $requestdata['party_id']        	= getFromSession('party_id');
	  
	  $requestdata['password']      	= md5(getRequest('password'));
	  $requestdata['u_type_id']      	= "3"; // 2 is user
	  
	  $requestdata['created_by']        = getFromSession('userid');
	  $requestdata['created_time']      = date('Y-m-d h:i:s');
	   
     	  
   	  $info        						=  array();
      $info['table']					= USER_TBL; 
	  $info['data'] 					= $requestdata;  
	  $info['debug']  					=  false;  
	  $res = insert($info); 
	  
      if($res)
      {
       	 header("location:?app=agent&cmd=view_user");
      }        

   }//EOFn 
   
   function showUserList($msg = null) {  
     
	  $data                = array();
	  $data['cmd']         = getRequest('cmd');
	  $data['record_list'] = $this->getUserList(getRequest('from'),getRequest('to'));
	   
	   if(getRequest('deleted')=='yes') {
		  $data['message'] = "Item Deleted Successfully";
	   }elseif(getRequest('deleted')=='no') {
		  $data['message'] = "Item Not Deleted";
	   }
	   require_once(VIEW_USER_SKIN_FILE); 
	   return $data[0];
   }
   
   function getUserList($from=null,$to=null)
   {
	   if($from == "" && $to == ""){$from=0; $to=10;}  
	   $party_id = getFromSession('party_id');
  	   $data            = array();	  
  	   $info            = array();
	   $info['table']   = USER_TBL; 
	   if($party_id !=""){    
       	 $info['where']  =  "party_id='".$party_id."' AND u_type_id =3 ";
	   }
	   $info['orderby'] = array("party_id asc LIMIT $from,$to");
	   $info['debug']   = false;			 

	   $res            =	select($info);   

	   if(count($res))
	   {
		  foreach($res as $i=>$v)
		  {
			 $data[] = $v;
		  }
	   }
	   return $data;

  }
  //================= Deposit ====================
  function bankDepositEditor()
  {
		 $userid = getRequest('id');
		
		if(getRequest('save')) {
			$this->insertBankDeposit();
			$msg="Successfully Save Record !!!";
			header("location:?app=agent&cmd=bank_deposit&msg=$msg");	     		       		      	
		 }			 
		 

	  $data                		= array();
	  //$data['user_list']  	= array($this->getRecords(getRequest('from'),getRequest('to')));
	  
	  $data['message'] 			= $msg;
	  $data['cmd']     			= getRequest('cmd'); 
	  require_once(BANK_DEPOSIT_SKIN_FILE);
	  return $data[0];

   }

  
   function insertBankDeposit()
   {       
	  $requestdata 						= array();
      $requestdata 						= getUserDataSet(BANK_DEPOSIT_TBL); 
	  $requestdata['party_id']        	= getFromSession('party_id');
	  
	  $requestdata['created_by']        = getFromSession('userid');
	  $requestdata['created_time']      = date('Y-m-d h:i:s');
	   
     	  
   	  $info        						=  array();
      $info['table']					= BANK_DEPOSIT_TBL; 
	  $info['data'] 					= $requestdata;  
	  $info['debug']  					=  false;  
	  $res = insert($info); 
	  
      if($res)
      {
       	 header("location:?app=agent&cmd=bank_deposit");
      }        

   }//EOFn 
   //============= Agent Account Status ===============
   
  function myAccountStatusEditor()
  {	 
	  require_once(AGENT_ACCOUNT_STATUS_SKIN_FILE);
	  return $data[0];
  }
  //========== Posting ============
    
  function postingAmountEditor($msg=null)
  {			
	if(getRequest('save')) {
		$this->insertPostingAmount();
		$msg="Successfully Save Record !!!";
		header("location:?app=agent&cmd=bank_deposit&msg=$msg");	     		       		      	
	}			 
	
	
	$data                		= array();
	//$data['user_list']  	= array($this->getRecords(getRequest('from'),getRequest('to')));
	      
    $data['district_list']     = $this->getDistrictList();
	$data['message'] 			= $msg;
	$data['cmd']     			= getRequest('cmd'); 
	require_once(POSTING_AMOUNT_SKIN_FILE);
	return $data[0];

   }
  
   function insertPostingAmount()
   {       
	  $requestdata 						= array();
      $requestdata 						= getUserDataSet(BANK_DEPOSIT_TBL); 
	  $requestdata['party_id']        	= getFromSession('party_id');
	  
	  $requestdata['created_by']        = getFromSession('userid');
	  $requestdata['created_time']      = date('Y-m-d h:i:s');
	   
     	  
   	  $info        						=  array();
      $info['table']					= BANK_DEPOSIT_TBL; 
	  $info['data'] 					= $requestdata;  
	  $info['debug']  					=  false;  
	  $res = insert($info); 
	  
      if($res)
      {
       	 header("location:?app=agent&cmd=bank_deposit");
      }        

   }//EOFn 
   
} // End class



?>

