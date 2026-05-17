<?php
require_onces();
class AccountHead
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

  function showEditor()

  {

		 $sub_id = getRequest('id');

		

		 if($sub_id)

		 {		 			

			 if(getRequest('save'))

			 {  

				$this->updateRecord($sub_id);

				$msg="Successfully Update Record !!!";

        		header("location:?app=account_head&cmd=view&msg=$msg");		      	

			 } 



		 } else {

			

			if(getRequest('save')) {

				$this->insertRecord();

				$msg="Successfully Save Record !!!";

        		header("location:?app=account_head&cmd=view&msg=$msg");	     		       		      	

			 }			 

		 }	 



	  $data                		= array();

	  $data['fabrics_list']  	= array($this->getRecords(getRequest('from'),getRequest('to'))); 

	  $data['totalrecord']  	= $this->getTotalRecords(); 

	  

	  $data['message'] 			= $msg;

	  $data['cmd']     			= getRequest('cmd'); 

	  require_once(CURRENT_APP_SKIN_FILE);

	  return $data[0];



   }



   function updateRecord($id)

   {       

	  $requestdata = array();

      $requestdata = getUserDataSet(SUB_ACC_HEAD_TBL); 

	  $requestdata['project_id']        = getFromSession('project_id');
   	  $info        						=  array();

      $info['table']					= SUB_ACC_HEAD_TBL; 	

      //dBug($requestdata);

	  $info['data'] 					= $requestdata;

	  $info['where']					= "sub_id ='$id'";  

	   $info['debug']  					=  false;    

	  $res = update($info);

	  

      if(!$res) {

        header("location:?app=account_head&cmd=view");

      }               



   }//EOFn 



   function insertRecord()

   {       

	  $requestdata 						= array();

      $requestdata 						= getUserDataSet(SUB_ACC_HEAD_TBL);    

      //$sub_id = $this->createID();
	  $requestdata['project_id']        = getFromSession('project_id');
	  $requestdata['created_by']        = getFromSession('userid');

	  $requestdata['created_time']      = date('Y-m-d h:i:s');
      /*if($$sub_id != -1)
      {
      	$requestdata['sub_id']   	= $sub_id;
      }
      else
      {
      	$msg = "ID overflow !!!";
      	header("location:index.php?app=user_home&msg=$msg");
      	exit;
      }*/
	  

   	  $info        						=  array();

      $info['table']					= SUB_ACC_HEAD_TBL; 

	  $info['data'] 					= $requestdata;  

	  $info['debug']  					=  false;  

	  $res = insert($info); 
	  if($res['affected_rows']) {
		//$sub_id 	= mysql_insert_id();
		//$project_id = getFromSession('project_id');
		//$sql = "INSERT INTO ".ACCOUNT_JOURNAL_TBL." (sub_id,project_id) VALUES('$sub_id','$$project_id')";
		 header("location:?app=account_head&cmd=view");
	  }else{

        header("location:?app=account_head&cmd=view");

      }        



   }//EOFn   



   function getRecords($from=null,$to=null)

   {

	   if($from == "" && $to == ""){$from=0; $to=25;}  

	   $sub_id = getRequest('sub_id');  

	   $srckey = getRequest('srckey');

  	   $data            = array();	  

  	   $info            = array();

	   $info['table']   = SUB_ACC_HEAD_TBL; 
	   $project_id = getFromSession('project_id');
	   if($sub_id !=""){    

       	 $info['where']  =  "sub_id='".$sub_id."' AND project_id = '$project_id'";

	   }elseif($srckey !=""){    

       	 $info['where']  =  " project_id = '$project_id' AND (sub_id = '".$srckey."' OR sub_head_name like '%".$srckey."%' OR head_type like '%".$srckey."%')";

	   }else{
		   $info['where']  =  "project_id = '$project_id'";
		}

	   $info['orderby'] = array("sub_head_name asc LIMIT $from,$to");

	   //$info['debug']   = true;			 



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

	   $sub_id = getRequest('sub_id');  

	   $srckey = getRequest('srckey');

  	   $data            = array();	  

  	   $info            = array();

	   $info['table']   = SUB_ACC_HEAD_TBL;

	   $project_id = getFromSession('project_id');
	   if($sub_id !=""){    

       	 $info['where']  =  "sub_id='".$sub_id."' AND project_id = '$project_id'";

	   }elseif($srckey !=""){    

       	 $info['where']  =  " project_id = '$project_id' AND (sub_id = '".$srckey."' OR sub_head_name like '%".$srckey."%')";

	   }else{
		   $info['where']  =  "project_id = '$project_id'";
		}

	   $info['orderby'] = array('sub_head_name ASC');

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

      	$info['table'] = SUB_ACC_HEAD_TBL;

      	$info['where'] = "sub_id='$id'";

      	$info['debug'] = false;

      	$res = delete($info);      	



      	if($res)

      	{

      	  $msg="Successfully delete Record !!!";

          header("location:?app=account_head&cmd=view&msg=$msg");     	   



      	} else{

      		 header("location:?app=account_head&cmd=view&cmd=list&deleted=no");

      	}      	



      }



   } 

    // ==== function createJobSeekerID==================

   function createID()

   {

      $info = array();

      $info['table'] = SUB_ACC_HEAD_TBL;

      $info['fields'] = array('max(sub_id) as maxfabrics');

      

      $res = select($info);

      

      $maxfabricsId = 'F310000';

      

      if(count($res))

      {

         foreach($res as $v)

         {

         	 if($v->maxfabrics)

         	 {

             $maxfabricsId = $v->maxfabrics;

             }

             break;   	

         }

      

      }

      

      $maxfabricsId = generateID("F",$maxfabricsId,7);

      return $maxfabricsId;

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

} // End class
?>
