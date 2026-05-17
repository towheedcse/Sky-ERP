<?php
class TrialBalance
{
   
   function run()
   {     
	$cmd = getRequest('cmd'); $msg="";
	$u_t_id = getFromSession('u_type_id');	
	if($u_t_id == 100 || $u_t_id == 101 || $u_t_id == 104 || $u_t_id == 109){      
	  switch($cmd){ 
	  	 case 'tbview1'       : $screen = $this->showTrialBalanceV1($msg); break;
		 default              : $cmd = 'list'; $screen = $this->showTrialBalanceV1($msg);   break;
	  }	
	}else {
	header("location:index.php?app=user_home&msg=You are not authorised !!!");
      	} 		
	if($cmd == 'list'){
		 require_once(CURRENT_APP_SKIN_FILE);
	}
	return true;
   }

  function showTrialBalanceV1($msg=NULL){
	$user_type 	= getFromSession('u_type_id'); 
	if($user_type==100){
	$project_id     = getRequest('project_id'); 
	}else{				
	$project_id     = getFromSession('project_id'); 
	}
	if(empty($project_id)){
	$project_id     = getFromSession('project_id'); 
	}		 
	$data                	= array();	     
	$data['project_id'] 	= $project_id;  
	$data['date_from'] 	= formatDate(getRequest('date_from'));
	$data['date_to'] 	= formatDate(getRequest('date_to'));	     
	$data['message'] 	= $msg;
	$data['cmd']     	= getRequest('cmd'); 
	require_once(CURRENT_APP_SKIN_FILE);
	return $data[0];
  }
      
} // End class
?>
