<?php 
class Importer_model extends CI_Model {
		
	function __construct()
	{
		parent::__construct();
	}	

	function InsertAccountHead(){
		$company_id = $this->input->post('institute_id');
		$branch_id 	= $this->input->post('branch_id');
		if($company_id=="" || $branch_id ==""){
			$company_id 	= $this->session->userdata('company_id');
			$branch_id 		= $this->session->userdata('branch_id'); 
		}
		$groupId			= $this->input->post('group_id');
		if($groupId==""){$groupId = 1; }
		$account_name		= $this->input->post('importer_full_name');
		$billing_address	= $this->input->post('billing_address');
		$shipping_address	= $this->input->post('shipping_address');
		$present_address	= $this->input->post('present_address');
		$permanent_address	= $this->input->post('permanent_address');
		$contact_person		= $this->input->post('contact_person');
		$designation		= $this->input->post('designation');
		$gender				= $this->input->post('gender');
		$nationality		= $this->input->post('nationality');
		$salesman_id		= $this->input->post('salesman_id');
		$mobile				= $this->input->post('mobile');
		$phone			    = $this->input->post('phone');
		$email				= $this->input->post('email');
		$account_type		= $this->input->post('account_type');
		$subsidiary_level1	= 1;
		$subsidiary_level2	= 3;
		$subsidiary_level3	= 1;
		if($account_type==1){$prefix="M";}elseif($account_type==2){$prefix="A";}elseif($account_type==4){$prefix="C";}elseif($account_type==5){$prefix="B";}elseif($account_type==6){$prefix="T";}elseif($account_type==8){$prefix="R";}elseif($account_type==10){$prefix="E";}elseif($account_type==11){$prefix="S";}elseif($account_type==12 || $account_type==13){$prefix="I";}elseif($account_type==26){$prefix="P";}elseif($account_type==27){$prefix="L";}else{$prefix="H";}
		
		$head_id		= $this->getHeadID($account_type,$prefix);
    	$created_by		= $this->session->userdata('created_by');
		$accountId		= $this->input->post('distributor_id');
        
		if($accountId==""){
			$data = array(
			'company_id'    	=>$company_id,
			'branch_id'    		=>$branch_id,
			'head_id'    		=>$head_id,
			'group_id'    		=>$groupId,
			'subsidiary_level1' =>$subsidiary_level1,
			'subsidiary_level2' =>$subsidiary_level2,
			'subsidiary_level3' =>$subsidiary_level3,
			'head_type'    		=>$account_type,	
			'account_name'    	=>$account_name,
			'bangla_name'    	=>$account_name,
			'account_details' 	=>$billing_address,
			'permanent_address' =>$shipping_address,
			'nationality'     	=>$nationality,
			'father_name'    	=>$contact_person,
			'mother_name'    	=>$designation,
			'mobile'     		=>$mobile,
			'email'     		=>$email,
			'gender'     		=>$gender,
			'created_by'     	=>$created_by
			);
			$this->db->insert(ACC_HEAD_TBL, $data);
			return $this->db->insert_id();
		}
		//print  $this->db->last_query();
   	}
	
	function InsertRecord(){		
		$created_by			=$this->session->userdata('created_by');
		$importerid		    =$this->input->post('importer_id'); 
		$importer_code	    =$this->input->post('importer_code');
		$short_code		    =$this->input->post('short_code');
		$institute_id		=$this->input->post('institute_id');
		$branch_id			=$this->input->post('branch_id');
		if($institute_id=="" || $branch_id ==""){
			$institute_id 	= $this->session->userdata('company_id');
			$branch_id 		= $this->session->userdata('branch_id'); 
		}
		$importer_type	    = $this->input->post('importer_type');
		$importer_full_name	= $this->input->post('importer_full_name');
		$billing_address	= $this->input->post('billing_address');
		$shipping_address	= $this->input->post('shipping_address');
		$present_address	= $this->input->post('present_address');
		$permanent_address	= $this->input->post('permanent_address');
		$division		    = $this->input->post('division');
		$district		    = $this->input->post('district');
		$thana		        = $this->input->post('thana');
		$contact_person		= $this->input->post('contact_person');
		$designation		= $this->input->post('designation');
		$gender				= $this->input->post('gender');
		$nationality		= $this->input->post('nationality');
		$salesman_id		= $this->input->post('salesman_id');
		$mobile				= $this->input->post('mobile');
		$phone			    = $this->input->post('phone');
		$fax			    = $this->input->post('fax');
		$email				= $this->input->post('email');
		$currency			= $this->input->post('currency');
		$login_id			= $this->input->post('login_id');
		$password			= $this->input->post('confirm_password');
		$rkey				= mt_rand(10,99);
		$accesskey			= $password.$rkey;
		
		if($importerid==''){
			$importer_id 		= $this->InsertAccountHead();
			$photo 					= $this->UploadPhoto($importer_id);
			//$passport_attach	    = $this->UploadPassport($importer_id);
			//$others_attach	        = $this->UploadOthers($importer_id);
			if(empty($distributor_code)){
				$ssql = "SELECT head_id FROM ".ACC_HEAD_TBL." WHERE account_id = $importer_id AND company_id = $institute_id AND branch_id = $branch_id";
				$squery = $this->db->query($ssql);				
				if($squery->num_rows() >0){				   
				   $distributor_code = $squery->row()->head_id;
				}
			}			
			$data = array(
			'importer_id'		    =>$importer_id,
			'importer_code'		    =>$importer_code,
			'short_code'			=>$short_code,
			'company_id'    		=>$institute_id,
			'branch_id'    			=>$branch_id,
			'importer_type'		    =>$importer_type,
			'salesman_id'    		=>$salesman_id,	
			'division'    		    =>$division,	
			'district'    		    =>$district,	
			'thana'    		        =>$thana,	
			'importer_full_name'    =>$importer_full_name,
			'billing_address'       =>$billing_address,
			'shipping_address'      =>$shipping_address,	
			'contact_person'        =>$contact_person,	
			'designation'           =>$designation,
			'address'    			=>$present_address,
			'permanent_address'    	=>$permanent_address,
			'photo'    				=>$photo,
			'currency'     		    =>$currency,
			'nationality'     		=>$nationality,
			'gender'                =>$gender,
			'phone'                 =>$phone,
			'fax'                   =>$fax,
			'mobile'     	        =>$mobile,
			'email'     	        =>$email,
			'login_id'    			=>$login_id,
			'password'     			=>$accesskey,
			'status'     			=>1,
			'created_by'     		=>$created_by
			);
			//=== Remove empty field ====
			$data = array_filter($data);
			//=== Remove unexpected field by value (e.g value) ====
			if (($key = array_search('value', $data)) !== false) { unset($data[$key]);}						
			$this->db->insert(IMPORTER_TBL, $data);			
			$email = $this->input->post('email');
			$this->ManageUser($importer_id,$institute_id,$branch_id,$login_id,$password,$email,1,$importer_full_name,5,"I");			
		}else{			
			$email = $this->input->post('email');
			$this->EditRecord($importerid,$institute_id,$branch_id);			
			$this->updateAccountHead($importerid,$institute_id,$branch_id);
			$this->ManageUser($importerid,$institute_id,$branch_id,$login_id,$password,$email,1,$importer_full_name,5,"U");
		}		
		//print  $this->db->last_query();
   	}
   	
	function UpdateRecord(){
		$importerid		    =$this->input->post('importer_id');
		$institute_id		=$this->input->post('institute_id');
		$branch_id			=$this->input->post('branch_id');
		if($institute_id=="" || $branch_id ==""){
			$institute_id 	=$this->session->userdata('company_id');
			$branch_id 		=$this->session->userdata('branch_id'); 
		}		
		$login_id			=$this->input->post('login_id');
		$password			=$this->input->post('confirm_password');
		$email 				=$this->input->post('email');
		$importer_full_name =$this->input->post('importer_full_name');
		if($importerid >0){
		$this->EditRecord($importerid,$institute_id,$branch_id);			
		$this->updateAccountHead($importerid,$institute_id,$branch_id);
		$this->ManageUser($importerid,$institute_id,$branch_id,$login_id,$password,$email,1,$importer_full_name,5,"U");
		}
	}
	
	function updateAccountHead($account_id,$institute_id,$branch_id){
		    $update_by			= $this->session->userdata('created_by');
			$update_time 		= date("Y-m-d H:i:s");
    		$institute_id		=$this->input->post('institute_id');
    		$branch_id			=$this->input->post('branch_id');
    		if($institute_id=="" || $branch_id ==""){
    			$institute_id 	= $this->session->userdata('company_id');
    			$branch_id 		= $this->session->userdata('branch_id'); 
    		}
    		$importer_full_name	= $this->input->post('importer_full_name');
    		$billing_address	= $this->input->post('billing_address');
    		$shipping_address	= $this->input->post('shipping_address');
    		$present_address	= $this->input->post('present_address');
    		$permanent_address	= $this->input->post('permanent_address');
    		$division		    = $this->input->post('division');
    		$district		    = $this->input->post('district');
    		$thana		        = $this->input->post('thana');
    		$contact_person		= $this->input->post('contact_person');
    		$designation		= $this->input->post('designation');
    		$gender				= $this->input->post('gender');
    		$nationality		= $this->input->post('nationality');
    		$salesman_id		= $this->input->post('salesman_id');
    		$mobile				= $this->input->post('mobile');
    		$phone			    = $this->input->post('phone');
    		$fax			    = $this->input->post('fax');
    		$email				= $this->input->post('email');
						
			$data = array(	
			'account_name'    	=>$importer_full_name,
			'bangla_name'    	=>$importer_full_name,
			'account_details' 	=>$billing_address,
			'permanent_address' =>$shipping_address,
			'nationality'     	=>$nationality,
			'father_name'    	=>$contact_person,
			'mother_name'    	=>$designation,
			'mobile'     		=>$mobile,
			'email'     		=>$email,
			'gender'     		=>$gender,
			'update_by'     	=>$update_by,
			'update_time'     	=>$update_time
			);
			$this->db->where('account_id',$account_id);
			$this->db->where('company_id',$institute_id);
			$this->db->where('branch_id',$branch_id);
			$this->db->update(ACC_HEAD_TBL, $data);
	}
	function EditRecord($importer_id,$institute_id,$branch_id){
		$modified_by		= $this->session->userdata('created_by');
		$modified_time 		= date("Y-m-d H:i:s"); 
		
		$short_code		    = $this->input->post('short_code');
		$importer_type	    = $this->input->post('importer_type');
		$importer_full_name	= $this->input->post('importer_full_name');
		$billing_address	= $this->input->post('billing_address');
		$shipping_address	= $this->input->post('shipping_address');
		$present_address	= $this->input->post('present_address');
		$permanent_address	= $this->input->post('permanent_address');
		$division		    = $this->input->post('division');
		$district		    = $this->input->post('district');
		$thana		        = $this->input->post('thana');
		$contact_person		= $this->input->post('contact_person');
		$designation		= $this->input->post('designation');
		$gender				= $this->input->post('gender');
		$nationality		= $this->input->post('nationality');
		$salesman_id		= $this->input->post('salesman_id');
		$mobile				= $this->input->post('mobile');
		$phone			    = $this->input->post('phone');
		$fax			    = $this->input->post('fax');
		$email				= $this->input->post('email');
		$currency			= $this->input->post('currency');
		$login_id			= $this->input->post('login_id');
		$password			= $this->input->post('confirm_password');
		$rkey				= mt_rand(10,99);
		$accesskey			= $password.$rkey;
		$importer_photo     = $_FILES['importer_photo'];
		if($importer_photo!=""){
			$photo 			=$this->UploadPhoto($importer_id);				
		}else{
			$ssql   = "SELECT photo FROM ".IMPORTER_TBL." WHERE importer_id = $importer_id AND company_id = $institute_id AND branch_id = $branch_id";
			$squery = $this->db->query($ssql);				
			if($squery->num_rows() >0){				   
			   $photo = $squery->row()->photo;
			}
		}  
		  
					
		$data = array(
			'short_code'			=>$short_code,
			'importer_type'		    =>$importer_type,
			'salesman_id'    		=>$salesman_id,	
			'division'    		    =>$division,	
			'district'    		    =>$district,	
			'thana'    		        =>$thana,	
			'importer_full_name'    =>$importer_full_name,
			'billing_address'       =>$billing_address,
			'shipping_address'      =>$shipping_address,	
			'contact_person'        =>$contact_person,	
			'designation'           =>$designation,
			'address'    			=>$present_address,
			'permanent_address'    	=>$permanent_address,
			'photo'    				=>$photo,
			'currency'     		    =>$currency,
			'nationality'     		=>$nationality,
			'gender'                =>$gender,
			'phone'                 =>$phone,
			'fax'                   =>$fax,
			'mobile'     	        =>$mobile,
			'email'     	        =>$email,
			'login_id'    			=>$login_id,
			'password'     			=>$accesskey,
			'modified_by'     		=>$modified_by,
			'modified_time'     	=>$modified_time
		);
		//=== Remove empty field ====
		$data = array_filter($data);
		//=== Remove unexpected field by value (e.g value) ====
		if (($key = array_search('value', $data)) !== false) { unset($data[$key]);}
		$this->db->where('importer_id',$importer_id);
		$this->db->where('company_id',$institute_id);
		$this->db->where('branch_id',$branch_id);
		$this->db->update(IMPORTER_TBL, $data); //print  $this->db->last_query(); exit;
    }
	
	function ManageUser($importer_id,$institute_id,$branch_id,$login_id,$password,$email,$status,$importer_name,$user_role,$mode){
		$created_by	= $this->session->userdata('created_by');				
		$rkey		= mt_rand(10,99);
		$access_key = $password.$rkey; 
		$password   = $this->encrypt->encode($password); 		
		if($mode=="I"){		
		  $SQL="INSERT INTO ".USERS_TBL."(ref_id,company_id,branch_id,user_name,password,email,user_status,display_name,access_key,user_role,created_by) ";
		  $SQL.="VALUES('".$importer_id."','".$institute_id."','".$branch_id."','".$login_id."','".$password."','".$email."','".$status."','".$importer_name."','".$access_key."','".$user_role."','".$created_by."')";
		  $this->db->query($SQL);
		}else{
		  $CSQL= "UPDATE ".USERS_TBL." SET company_id='".$institute_id."', branch_id='".$branch_id."', password='".$password."', email='".$email."', user_status='".$status."', display_name='".$importer_name."', access_key='".$access_key."' WHERE ref_id='".$importer_id."' AND user_name = '".$login_id."'";
		  $this->db->query($CSQL);
		}

	}    	
	function UploadPhoto($img_id){
	    if($_FILES['customer_photo']){
            $file_name=''; $saveDir = ASSETS.'/img/photo/';
            $config['file_name']		= $img_id;
            $config['overwrite']		= TRUE;
            $config['upload_path'] 		= ASSETS.'/img/photo/';
            $config['allowed_types'] 	= 'gif|jpg|png|jpeg';
            $config['max_size'] 		= '144400';
            $config['max_width']  		= '1024';
            $config['max_height']  		= '768';
            $config['maintain_ratio']  	= FALSE;
            $config['width']  		= '185';
            $config['height']  		= '200';
            $this->load->library("upload",$config);
            //print_r($_FILES['customer_photo']['type']);
            //$field = $_FILES['importer_photo'];
		  
            // No problems with the file
            
            // So lets upload 
            if ($this->upload->do_upload('importer_photo')){ //print_r($field);
            	$data =  $this->upload->data();					
            	$file_name = $data['orig_name'];
            	//==== Resize Image ======
            	$config2 = array(
            		'source_image' => $data['full_path'],
            		'new_image'    => $saveDir,
            		'maintain_ratio' => FALSE,
            		'width' => 185,
            		'height' => 200
            	);			
            	$this->load->library('image_lib', $config2);
            	$this->image_lib->resize();					
            	return trim("photo/".$file_name);
            	                    
            }else{
            	//echo $errors = $this->upload->display_errors();
            	return false;
            }
	    }else{
			return false;
		}
		
	}
	    	
	function UploadPassport($pdf_id){
	    if($_FILES['passport_attach']){
			$targetfolder = ASSETS.'/pdf/passport/';
            $targetfolder = $targetfolder.$pdf_id ;
            $ok=1;
            $file_type=$_FILES['passport_attach']['type'];
            
            if ($file_type=="application/pdf" || $file_type=="image/gif" || $file_type=="image/jpeg") {
            
                if(move_uploaded_file($_FILES['passport_attach']['tmp_name'], $targetfolder))
                
                {
                
                echo "The file ". basename( $_FILES['passport_attach']['name']). " is uploaded";
                
                }
                
                else {
                
                echo "Problem uploading file";
                
                }
            }else {
             echo "You may only upload PDFs, JPEGs or GIF files.<br>";
            }
		  
		}else{
		    return false;
		}
	}
	function UploadOthers($pdf_id){
	    if($_FILES['others_attach']){
			$targetfolder = ASSETS.'/pdf/others/';
            $targetfolder = $targetfolder.$pdf_id ;
            $ok=1;
            $file_type=$_FILES['others_attach']['type'];
            
            if ($file_type=="application/pdf" || $file_type=="image/gif" || $file_type=="image/jpeg") {
            
                if(move_uploaded_file($_FILES['others_attach']['tmp_name'], $targetfolder))
                
                {
                
                echo "The file ". basename( $_FILES['others_attach']['name']). " is uploaded";
                
                }
                
                else {
                
                echo "Problem uploading file";
                
                }
            }else {
             echo "You may only upload PDFs, JPEGs or GIF files.<br>";
            }
		  
		}else{
		    return false;
		}
		
	}
	function FillAccountHead(){
		$account_id =$this->input->post('id');
		$this->db->select('*');
		$this->db->from(ACC_HEAD_TBL);
		$this->db->where('status', 1);
		$this->db->where('account_id', $account_id);
		$query = $this->db->get();
		return $query->row();
	}
	function FillRecord(){
		$importer_id_id =$this->input->post('id');
		$this->db->select('*');
		$this->db->from(IMPORTER_TBL);
		$this->db->where('status', 1);
		$this->db->where('importer_id', $importer_id_id);
		$query = $this->db->get();
		return $query->row();
	}		
	function GetAjaxImporterList($importer_id){		
		$PSQL= "SELECT * FROM ".ACC_HEAD_TBL." WHERE head_type IN(3) AND status=1 ";			
		$PSQL.= " GROUP BY account_id ORDER BY account_name ASC";
		$query = $this->db->query($PSQL);
		$options = "<option value='0'>".$this->lang->line('select')." ".$this->lang->line('importer')."</option>";
		foreach($query->result() as $irow){			
			if($employee_id >0 && $employee_id == $irow->account_id){ 
				$selected = "selected='selected'"; 
			}else{ $selected = ""; }			
			$options.="<option  value='".$irow->account_id."' $selected >".$irow->account_name."</option>";
		}
		return $options;
	}		
	
   	//============== Category Retrive by Ajax================
   	function GetRecordGrid(){
		$menu_slug= $this->uri->segment(1);
		$this->load->model('Site_model');
		$hasEditPM = $this->Site_model->hasOptionPermission($menu_slug,"Edit");
		$hasDelPM  = $this->Site_model->hasOptionPermission($menu_slug,"Delete");
		$hasPrintPM= $this->Site_model->hasOptionPermission($menu_slug,"Print");
	   	$from	=$this->input->post('from');
		$to	=$this->input->post('to');
		if($from==""){ $from=0;} if($to==""){ $to=50;}			
		$head_types = array(3);	
		$this->db->select('a.*,p.account_id,p.group_id,p.subsidiary_level1,p.subsidiary_level2,p.head_type,p.account_name,p.nationality,p.mobile,p.email,p.gender,i.company_name,b.branch_name,b.branch_code',FALSE);
		$this->db->from(IMPORTER_TBL." AS a");
		$this->db->join(ACC_HEAD_TBL.' AS p', 'p.account_id=a.importer_id','LEFT');
	  	$this->db->join(COMPANY_SETTINGS_TBL.' AS i', 'i.company_id=a.company_id','LEFT');
	  	$this->db->join(BRANCH_TBL.' AS b', 'b.branch_id=a.branch_id','LEFT');
		$this->db->where_in('p.head_type',$head_types);
		$this->db->where('a.status >0');
		$this->db->group_by('a.importer_id');
		$this->db->order_by('a.importer_code','ASC');
		$this->db->limit($to,$from);
		$query = $this->db->get(); //print  $this->db->last_query();
		$totalrecord = $this->GetTotalRecord();
	    	$perPage=50; $Pagination="";
	    	if($totalrecord >0){
		   	$Pagination = $this->getPagination($totalrecord,$perPage);
	    	} //print  $this->db->last_query();
		echo 
		'<table width="100%"  border="0" class="table table-responsive table-bordered table-hover custab">
			<thead>
			  <tr class="active">
			  	<th width="2%">'.$this->lang->line("sl").'</th>
				<th width="20%">'.$this->lang->line("importer").' '.$this->lang->line("details").'</th>
			  	<th width="20%">'.$this->lang->line("billing_address").'</th>
			  	<th width="20%">'.$this->lang->line("shipping_address").'</th>
				<th width="15%">'.$this->lang->line("contact_person").'</th>
				<th width="15%">'.$this->lang->line("contact").' '.$this->lang->line("details").'</th>
				<th width="8%" class="text-center">'.$this->lang->line("options").'</th>
			  </tr>
			</thead>';
			  $i=1; $visa_type="";  $midea_name = "N/A";
			  foreach($query->result() as $row){
			  //if($i%2==0){ $tblrow="success";}else{$tblrow="warning";}
			  
			  if($row->status==1){ $tblrow="bg-success";}else{$tblrow="bg-danger";}
			  $salesman_name = $this->GetHeadName($row->salesman_id);
			  echo "<tr class='default'>
			  	<td class='".$tblrow."'>".$i."</td>
				<td><img src='".base_url().ASSETS.'/img/'.$row->photo."' height='30'/><br>".$row->account_name.",<br>A/C No.: ".$row->account_id."<br> Code: ".$row->short_code."
				</td>
			  	<td>".$row->billing_address."</td>
			  	<td>".$row->shipping_address."</td>
				<td>".$row->contact_person."</td>
				<td>Phone:".$row->phone."<br>Fax:".$row->fax."<br>Mob:".$row->mobile."<br>".$row->email."</td>
				<td class='text-center align-middle'>";
		    	if($hasEditPM){
				echo "<span data-toggle='tooltip' data-placement='top' data-original-title='Edit'><a class='btn btn-info btn-xs' onclick=editRecord('".$row->importer_id."') id='".$row->importer_id."' href='#'><i class='fas fa-edit'></i></a></span>&nbsp;";
				}
				if($hasDelPM){				
				echo "<span data-toggle='tooltip' data-placement='top' data-original-title='Delete'><a class='btn btn-danger btn-xs' data-toggle='modal' onclick=deleteRecord('".$row->importer_id."') id='".$row->importer_id."' data-target='#deleteModal'><i class='fa fa-trash'></i></a></span>";
				}
				if($hasPrintPM){
				echo "<div class='clearfix'></div><br><span data-toggle='tooltip' data-original-title='View Form'><a class='btn btn-success btn-sm' target='_blank' href='".base_url()."importer/ViewProfile/".$row->importer_id."'><i class='fa fa-print'></i> Profile</a></span>";
				}
			    echo "</td>
			  </tr>";
			  $i++;
			  }
		echo '</table>';
	    	echo "<div class='float-right'>$Pagination</div>";
	}
    
	function GetTotalRecord(){
		$head_types = array(3);	
		$this->db->select('a.*,p.account_id,p.group_id,p.subsidiary_level1,p.subsidiary_level2,p.head_type,p.account_name,p.nationality,p.mobile,p.email,p.gender,i.company_name,b.branch_name,b.branch_code',FALSE);
		$this->db->from(IMPORTER_TBL." AS a");
		$this->db->join(ACC_HEAD_TBL.' AS p', 'p.account_id=a.importer_id','LEFT');
	  	$this->db->join(COMPANY_SETTINGS_TBL.' AS i', 'i.company_id=a.company_id','LEFT');
	  	$this->db->join(BRANCH_TBL.' AS b', 'b.branch_id=a.branch_id','LEFT');
		$this->db->where_in('p.head_type',$head_types);
		$this->db->where('a.status >0');
		$this->db->group_by('a.importer_id');
		$this->db->order_by('a.importer_code','ASC');
		$query = $this->db->get();
		if($query->num_rows() >0){
			return $query->num_rows();
		}else{
			return 0;
		}//echo $this->db->last_query();
	}
	
	function DelRecord(){
		$importer_id =$this->input->post('id');
		$status =$this->input->post('status');
		if(!isset($status)){$status=0;}
		$ESQL= "UPDATE ".IMPORTER_TBL." SET status='".$status."'WHERE importer_id='".$importer_id."'";
		$this->db->query($ESQL);
		
		$ASQL= "UPDATE ".ACC_HEAD_TBL." SET status='".$status."'WHERE account_id='".$importer_id."'";
		$this->db->query($ASQL);
		
		$USQL= "UPDATE ".USERS_TBL." SET user_status='".$status."'WHERE ref_id='".$importer_id."'";
		$this->db->query($USQL);
		/*
		//=====Start Delete ======
		$this->db->where('importer_id',$importer_id);
		$this->db->delete(IMPORTER_TBL);
		
		$this->db->where('account_id',$importer_id);
		$this->db->delete(ACC_HEAD_TBL);
		
		$this->db->where('ref_id',$importer_id);
		$this->db->delete(USERS_TBL);
		*/
	}
	function GetHeadName($customer_id){
		if($customer_id >0){
			$this->db->select('account_name,account_details');
			$this->db->from(ACC_HEAD_TBL);
			$this->db->where('account_id', $customer_id);
			$query = $this->db->get();
			$row = $query->row();
			$Customer = $row->account_name;
			if($row->account_details !=""){
			$Customer.="<br>".$row->account_details;
			}
			return $Customer;
		}else{
			return "";
		}
	}
    /*======Start Common Function for pagination=======*/
    function getHeadID($category,$prefix){
		$SQL = "SELECT max(head_id) AS maxhead FROM ".ACC_HEAD_TBL." WHERE head_type IN(".$category.")
		 AND `head_id` LIKE '".$prefix."%' ORDER BY `head_id` DESC";
		$query = $this->db->query($SQL);
		$maxheadId = $prefix."00000000";
		if($query->num_rows() >0){
			foreach($query->result() as $v){
				if($v->maxhead){
				 $maxheadId = $v->maxhead;
				}
				break;
			}
		}		
		return $maxheadId = $this->generateID("$prefix",$maxheadId,9);		
    }
    function generateID($priFix, $maxId, $len){
		$nextIdNum = trim($maxId,$priFix) + 1;
		$padlen = $len - (strlen($priFix) + strlen($nextIdNum)) +1 ;
    		$nextID = str_pad($priFix, $padlen, "0").$nextIdNum;	
		if	(strlen($nextID) <= $len)
			return $nextID;
		else
			return "ID over flow !!!";
    }
    function getPagination($totalrecord, $block)
    {
        $from_rs = $this->input->post('from');
        if ($from_rs == "") {
            $from_rs = 0;
        }
        if ($block == "") {
            $block = 12;
        }
        $to_rs = $from_rs + $block;
        if ($from_rs >= $block) {
            $from_rs = $from_rs + 1;
        }
        if ($from_rs == "" || $from_rs == 0) {
            $from_rs = 1;
        }
        if ($to_rs == "" || $totalrecord < $block) {
            $to_rs = $totalrecord;
        } else if ($to_rs == "" && $totalrecord > $block) {
            $to_rs = $block;
        }
        if ($to_rs > $totalrecord) {
            $to_rs = $totalrecord;
        }
        if ($totalrecord == 0) {
            $from_rs = 0;
        }

        $plink = $this->input->post('page_no');
        if ($plink == "") {
            $plink = 1;
        }
        if ($totalrecord > $block) {
            $res = $totalrecord / $block;
            $res = (int)$res;
            if (($totalrecord % $block) != 0) {
                $totalpage = $res + 1;
            } else {
                $totalpage = $res;
            }
        } else {
            $totalpage = 1;
        }
        $paginationStr = "";
        $paginationStr .= "<ul class='pagination pagination-sm m-0'>";

        if ($totalrecord > $block) {
            $two = $this->input->post('from');
            if ($two == "") {
                $two = 0;
            }
            $pno = $this->input->post('page_no');
            if ($pno == "") {
                $pno = 0;
            }
            $pno = $pno - 1;
            $frm = $two - $block;
            $to = $block;
            if ($pno <= $totalpage && $pno > 0) {
                $paginationStr .= "<li class='page-item'><a class='page-link' onclick=nextPage($frm,$to,$pno) href='#'>&laquo;</a></li>";
            }
        } else {
            $paginationStr .= "<li class='page-item disabled'><a class='page-link' href='#'>&laquo;</a></li>";
        }
        if ($totalpage >= 1) {
            $i = 1;
            $from = 0;
            $to = $block;
            while ($i <= $totalpage) {
                if ($from == 0) {
                    $paginationStr .= "<li class='page-item'";
                    if ($i == $plink) {
                        $paginationStr .= "class='active'";
                    }
                    $paginationStr .= ">";
                    $paginationStr .= "<a class='page-link' onclick=nextPage($from,$to,$i) href='#'>$i</a></li>";
                } else {
                    $paginationStr .= "<li class='page-item'";
                    if ($i == $plink) {
                        $paginationStr .= "class='active' ";
                    }
                    $paginationStr .= ">";
                    $paginationStr .= "<a class='page-link' onclick=nextPage($from,$to,$i) href='#'>$i</a></li>";
                }
                $i++;
                $from = $from + $block;
                if ($to > $totalrecord) {
                    $to = $totalrecord;
                }
            }
        }
        if ($totalrecord > $block) {
            $f = $this->input->post('from');
            $page = $this->input->post('page_no');
            $page = $page + 1;
            if ($f == "" || $f == 0) {
                $f = $block;
                $page = 2;
            } else {
                $f = $f + $block;
            }
            $t = $block;
            if ($t > $totalrecord) {
                $t = $totalrecord;
            }
            if ($page <= $totalpage) {
                $paginationStr .= "<li class='page-item'><a class='page-link' onclick=nextPage($f,$t,$page) href='#'>&raquo;</a></li>";
            }
        } else {
            $paginationStr .= "<li class='page-item disabled'><a class='page-link' href='#'>&raquo;</a></li>";
        }

        $paginationStr .= "</ul>";
        return $paginationStr;
    }

    function formatDate($dt)
    {
	if (trim($dt)) {
		$day = substr($dt, 0, 2);
		$month = substr($dt, 3, 2);
		$year = substr($dt, 6, 4);
		$hour = substr($dt, 11, 2);
		$minute = substr($dt, 14, 2);
		$second = substr($dt, 17, 2);
		$ampm = substr($dt, 20, 2);
		//echo $ampm;
		if ($hour == '' AND $minute == '' AND $second == '') {
			return $year . "-" . $month . "-" . $day;
		} else {
			if (strtoupper($ampm) == 'PM') {
				$hour = intval($hour) + 12;
				return $year . "-" . $month . "-" . $day . ' ' . $hour . ':' . $minute . ':' . $second;
			} else {
				return $year . "-" . $month . "-" . $day . ' ' . $hour . ':' . $minute . ':' . $second;
			}
		}
	}
    }

    function formatDateTimeDMY($dt)
    {
	if (trim($dt)) {
		$year = substr($dt, 0, 4);
		$month = substr($dt, 5, 2);
		$day = substr($dt, 8, 2);
		$hour = substr($dt, 11, 2);
		$minute = substr($dt, 14, 2);
		$second = substr($dt, 17, 2);
		$ampm = substr($dt, 20, 2);
		if ($hour == '' AND $minute == '' AND $second == '') {
			return $year . "-" . $month . "-" . $day;
		} else {
			if (strtoupper($ampm) == 'PM') {
				$hour = intval($hour) + 12;
				return $day . "-" . $month . "-" . $year . ' ' . $hour . ':' . $minute . ':' . $second;
			} else {
				return $day . "-" . $month . "-" . $year . ' ' . $hour . ':' . $minute . ':' . $second;
			}
		}
	}
    }
    function formatDateDMY($val)
    {
	if ($val) {
		$yy = substr($val, 0, 4);
		$mm = substr($val, 5, 2);
		$dd = substr($val, 8, 2);
		return $dd . '-' . $mm . '-' . $yy;
	}
    }
    function dateInputFormatDMY($val)
    {
		if ($val) {
			$yy = substr($val, 0, 4);
			$mm = substr($val, 5, 2);
			$dd = substr($val, 8, 2);
			return $dd . '-' . $mm . '-' . $yy;
		}
    }
}
