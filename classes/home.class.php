<?php
class HomeApp
{
  function run()
  {
     $cmd = getRequest('cmd');

      switch ($cmd)
      {
      	 case 'add'                : $this->addFaq($msg); 						break;         	
         case 'admission'          : $this->adminission(); 						break;
         case 'photo'              : $this->photo();  							break;
         case 'home'               : $screen = $this->home();   				break;
         case 'login'	           : $this->login(); 							break;	 
		 case 'email_matching'	   : $this->getSendEmailStatus(); 				break;       
         default                   : $cmd = 'home'; $screen = $this->home();	break;
      }
  }
  function getSendEmailStatus()
  {   	               
       $info           =  array();     
       $info['table']  =  SENDMAIL_STATUS_TBL; 
	   $info['where'] = "sending_date != curDate()";       
       $info['debug']  =  false;                     
       $res            =	select($info);
       $week_start = $this->getWeekStartDay();
       if(count($res))
       {	     			 	   	 
          
		  foreach($res as $val)
          {    
		     $this->EmailMatching();   
			 if($week_start!="")
			 {
				$this->EmailMatching4WeeklyMember(); 			
			 }	
		       
			 $sending_date  	 =$val->sending_date;
			 $date_sql = "SELECT curDate() AS nowdate";
		     $date_res = mysql_query($date_sql);
		     $date_row = mysql_fetch_array($date_res); 
		     $nowdate = $date_row['nowdate'];
	   	
			 $date =date('Y-m-d');
			 $sql ="Update send_mail_status SET sending_date='$nowdate' WHERE sending_date ='$sending_date'";
			 mysql_query($sql);
		  }	  
		  	 
	 
       }        
	  
  }
  function EmailMatching()
  {  
  	   $info           = array();   
      $info['table']   =  DRAFT_EMAIL_TBL;    
	  $info['where']   =  "status ='Active'";  	 
      $info['debug']   = false;      
      $result          = select($info);
      $data            = array();
      
      if(count($result))
      {
         foreach($result as $value)
         {            
			 $subject  	 =$value->subject;	
			 $mail_body  =$value->mail_body;	
			 $sender = $value->created_by;	
			 if($subject!="") {	
			 	$this->getActiveMember($sender,$subject,$mail_body);			
			 }				 
		 }
	  }
  	  
  }
  function EmailMatching4WeeklyMember()
  {  
  	   $info           = array();   
      $info['table']   =  DRAFT_EMAIL_TBL;    
	  $info['where']   =  "status ='Active'";
	    	 
      $info['debug']   = false;      
      $result          = select($info);
      $data            = array();
      
      if(count($result))
      {
         foreach($result as $value)
         {            
			 $subject  	 =$value->subject;	
			 $mail_body  =$value->mail_body;	
			 $sender = $value->created_by;		
			 $this->getActiveWeeklyMember($sender,$subject,$mail_body);				 
		 }
	  }
  	  
  }
  function getActiveMember($sender = null,$subject=null,$mail_body=null)
  {
  	  $info            = array();   
	  $info['table']   =  USER_TBL.' u,'.USER_PROFILE_TBL.' p';
	  $info['fields']  =   array('u.userid','p.email_alert');          
	  $info['where']   =  "u.userid = p.userid AND u.status ='Active' and u.u_type_id ='0' AND p.email_alert='Daily'";  	 
	  $info['debug']   = false;      
	  $result          = select($info);
	  $data            = array();
	  
	  if(count($result))
	  {
		 foreach($result as $value)
		 { 
			$receiver  	 =$value->userid;
			$this->sendEmail($sender,$receiver,$subject,$mail_body);	
		 }
	  }
  }
  function getActiveWeeklyMember($sender = null,$subject=null,$mail_body=null)
  {
  	  $info            = array();   
	  $info['table']   =  USER_TBL.' u,'.USER_PROFILE_TBL.' p';
	  $info['fields']  =   array('u.userid','p.email_alert');          
	  $info['where']   =  "u.userid = p.userid AND u.status ='Active' and u.u_type_id !='1' AND p.email_alert='Weekly'";  	 
	  $info['debug']   = false;      
	  $result          = select($info);
	  $data            = array();
	  
	  if(count($result))
	  {
		 foreach($result as $value)
		 { 
			$receiver  	 =$value->userid;
			$this->sendEmail($sender,$receiver,$subject,$mail_body);	
		 }
	  }
  }
  function sendEmail($sender = null,$receiver = null,$subject=null,$mail_body=null)
  {   	 
	  $sql="Insert into ".BUDDY_EMAIL_TBL."(sender,receiver,subject,message) values('$sender','$receiver','$subject','$mail_body')";
	  mysql_query($sql);
   } 
   
  function getWeekStartDay()
  {
	   $sql = "SELECT date_format( curDate( ) , '%W' ) AS day_name";
	   $result = mysql_query($sql);
	   $row = mysql_fetch_array($result); 
	   $day_name = $row['day_name'];
	   if($day_name!="Saturday")
	   {
	   		$day_name="";
	   }	   		
	   
	   return $day_name;	   
  }
  function home()
  {    
     $this->getSendEmailStatus();
	
     require_once(LOGIN_SKIN);
	//require_once(CURRENT_APP_SKIN_FILE);
  }
    
  function login()
  {
  	require_once(LOGIN_SKIN);
  }  
    
  
} // End class

?>
