<?php
function checkRegCode($regCode, $traceId, $ret = "no")
{
      $regCode = trim($regCode);
      $traceId = trim($traceId);

   	  $textStartChar = array('C'=>'center','E'=>'employee','D'=>'student');
   	  $firstChar = substr($traceId,0,1);
   	  
   	  $text = ""; 
   	  
   	  if(trim($traceId)=='')
   	  {
   	     $text = "Invalid";	
   	  }
      elseif(isset($textStartChar[$firstChar]))
      {         
         $info = array();
         $info['table'] = $textStartChar[$firstChar];
         
         if($textStartChar[$firstChar]=='center')
         {
            $info['fields'] = array('centerid');
            $info['where']  = "centerid='$traceId' AND regcode='$regCode'";
         }
         elseif($textStartChar[$firstChar]=='student')
         {
            $info['fields'] = array('studentid');
            $info['where']  = "studentid='$traceId' AND regcode='$regCode'";
         }
         elseif($textStartChar[$firstChar]=='employee')
         {
            $info['fields'] = array('empid');
            $info['where']  = "empid='$traceId' AND regcode='$regCode'";
         }
        
         $info['debug'] = false;         
         $res = select($info);                
         
         if(count($res))
         {
            $text = "Valid";
         }
         else
         {
            $text = "Invalid";
         }
         
      }
      else
      {
      	 $text = "Invalid";
      }
      
      if($ret == "yes")
      {
         return $text;
      }
      else
      {
         echo $text;
      }        	
      
}

function checkUser($userId, $ret = "no")
{
   $userId = trim($userId);

   $info = array();
   $info['table'] = USER_TBL;
   $info['where'] = "TRIM(userid)='$userId'";

   $res = select($info);

   $text = "";

   if(count($res))
   {
      $text = "No";
   }
   else
   {
      $text = "Yes";
   }
   
   if($ret == "yes")
   {
      return $text;
   }
   else
   {
      echo $text;
   }
}
?>
