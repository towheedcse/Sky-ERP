<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Company_settings extends CI_Controller {	
	function __construct(){
		parent::__construct();
		$this->load->model("Company_settings_model");
		$this->load->library('Alllist');
	}
	function index(){	
		$menu_slug= $this->uri->segment(1);	
		$this->Site_model->has_menupermission($menu_slug);			    
		$data['hasCreateOption']= $this->Site_model->hasOptionPermission($menu_slug,"Create");	    
		$data['hasViewOption']  = $this->Site_model->hasOptionPermission($menu_slug,"View");	
		//$data['ahquery']		= $this->alllist->GetAdmissionHeadList();	
		$data['chquery']		= $this->alllist->GetConcessionHeadList();
		$data['shquery']		= $this->alllist->GetShiftList();			
		$this->load->view('company_settings',$data);
	}
	function AddRecord(){// Function Standard PascalCase
		$this->Site_model->has_menupermission($this->uri->segment(1));
		
		$this->Company_settings_model->InsertRecord();
		$this->Company_settings_model->GetRecordGrid();
	}
	function GetRecord(){
		$this->Site_model->has_menupermission($this->uri->segment(1));
		$this->Company_settings_model->GetRecordGrid();
	}
	function DelRecord(){
		$this->Site_model->has_menupermission($this->uri->segment(1));
		$this->Company_settings_model->DelRecord();
	}
	function FillRecord(){
		$this->Site_model->has_menupermission($this->uri->segment(1));
		$row = $this->Company_settings_model->FillRecord(); // $row->sm_logo."##&##".$row->md_logo."##&##".
		echo $row->company_id."##&##".$row->company_name."##&##".$row->address."##&##".$row->phone."##&##".$row->mobile."##&##".$row->email."##&##".$row->site_url."##&##".$row->ssl_url."##&##".$row->backend_title."##&##".$row->frontend_title."##&##".$row->short_title."##&##".$row->copyright."##&##".$row->keywords."##&##".$row->meta_description."##&##".$row->currency_sign."##&##".$row->currency_code."##&##".$row->default_language."##&##".$row->license_key."##&##".$row->secret_key."##&##".$row->site_offline."##&##".$row->offline_msg."##&##".$row->allow_registration."##&##".$row->booking_cancellation."##&##".$row->admission_head."##&##".$row->discount_head."##&##".$row->f_scholarship_head."##&##".$row->p_scholarship_head."##&##".$row->concession_hreads."##&##".$row->default_shift."##&##".$row->weekend."##&##".$row->one_day_deduction;
	}
	function EditRecord(){
		$this->Site_model->has_menupermission($this->uri->segment(1));
		$this->Company_settings_model->EditRecord();
	}
	function saveRecord(){
		$this->Site_model->has_menupermission($this->uri->segment(1));
		$companyName			=$this->input->post('company_name');
		$address			=$this->input->post('address');
		$phone				=$this->input->post('phone');
		$mobile				=$this->input->post('mobile');
		$email				=$this->input->post('email');
		$site_url			=$this->input->post('site_url');
		$ssl_url			=$this->input->post('ssl_url');
		$backend_title			=$this->input->post('backend_title');
		$frontend_title			=$this->input->post('frontend_title');
		$short_title			=$this->input->post('short_title');
		$copyright			=$this->input->post('copyright');
		$keywords			=$this->input->post('keywords');
		$meta_description		=$this->input->post('meta_description');
		$currency_sign			=$this->input->post('currency_sign');
		$currency_code			=$this->input->post('currency_code');
		$default_language		=$this->input->post('default_language');
		$license_key			=$this->input->post('license_key');
		$secret_key			=$this->input->post('secret_key');
		$site_offline			=$this->input->post('site_offline');
		$offline_message		=$this->input->post('offline_msg');
		$allow_registration		=$this->input->post('allow_registration');
		$booking_cancellation		=$this->input->post('booking_cancellation');
		$companyId			=$this->input->post('company_id');

		if ($companyId=="" && $companyName!="" && $site_url !="")
		{
			$data = array('company_name' =>$companyName,'address' =>$address,'phone' =>$phone,'mobile' =>$mobile,'email' =>$email,'site_url' =>$site_url,'ssl_url' =>$ssl_url,'backend_title' =>$backend_title,'frontend_title' =>$frontend_title,'short_title' =>$short_title,'copyright' =>$copyright,'keywords' =>$keywords,'meta_description' =>$meta_description,'currency_sign' =>$currency_sign,'currency_code' =>$currency_code,'default_language' =>$default_language,'license_key' =>$license_key,'secret_key' =>$secret_key,'site_offline' =>$site_offline,'offline_msg' =>$offline_message,'allow_registration' =>$allow_registration,'booking_cancellation' =>$booking_cancellation);

			if($companyId ==""){
				//$this->db->trans_start();
				$this->db->insert(COMPANY_SETTINGS_TBL, $data);
				$company_id = $this->db->insert_id();
				$up2 = $this->upload_files($company_id);
				//$up1 = $this->DoUploadSM($company_id);
				if($up2){
					$this->session->set_flashdata('msg', 'Company Settings Successfully saved!!!');
				}else{
					$this->session->set_flashdata('msg', 'Company Image upload failed. Please try again!!!');
				}
				//$this->db->trans_complete();
				redirect(SERVER.'/company_settings');
			}else{
				$this->db->trans_start();
				$this->db->where('company_id',$companyId);
				$this->db->update(COMPANY_SETTINGS_TBL, $data);

				if($this->upload_files($companyId)){
					$this->session->set_flashdata('msg', 'Company Settings Successfully saved !!!');
				}else{
					$this->session->set_flashdata('msg', 'Successfully updated record !!!');
				}
				//print  $this->db->last_query(); die;
				$this->db->trans_complete();
				redirect(SERVER.'/company_settings');
			}

		}else{
			$this->Company_settings_model->EditRecord($companyId);
			redirect(SERVER.'/company_settings');
		}
	}
	private function upload_files($maxid){
		$path  = ASSETS.'/img/company/';
				
        $config = array(
            'upload_path'   => $path,
            'allowed_types' => 'jpg|gif|png',
            'overwrite'     => true,                       
        );

        $this->load->library('upload', $config);

        $images = array(); $imgdata=array();
		$i=0; $sm_logo_name=""; $md_logo_name=""; $isUpload=false;
        foreach ($_FILES as $key => $image) {
			//echo $image['name']." -". $image['type']."<br>";
			$ext = explode(".",$image['name']);
            $_FILES['images[]']['name']= $image['name'];
            $_FILES['images[]']['type']= $image['type'];
            $_FILES['images[]']['tmp_name']= $image['tmp_name'];
            $_FILES['images[]']['error']= $image['error'];
            $_FILES['images[]']['size']= $image['size'];
			if($i==0){
            $fileName = "sm-".$maxid.".".$ext[1];
			$sm_logo_name = $fileName;
			}elseif($i==1){
            $fileName = "md-".$maxid.".".$ext[1];
			$md_logo_name = $fileName;
			}
            $images[] = $fileName;

            $config['file_name'] = $fileName;

            $this->upload->initialize($config);
			//print_r($images); exit;
			
            if ($this->upload->do_upload('images[]')) {				
                $isUpload=true;
            } else {
                $isUpload=false;
            }
			$i++;
        }
		if($isUpload){
			//print_r($images);  echo $sm_logo_name . " - ".$md_logo_name;	
			if($sm_logo_name!=""){
				$config1 = array(
					'source_image' => $path.$sm_logo_name,
					'new_image' => $path,
					'maintain_ratio' => false,
					'width' => 100,
					'height' => 53
				);			
				$this->load->library('image_lib', $config1);
				$this->image_lib->resize();
			}
			if($md_logo_name!=""){
				$config2 = array(
					'source_image' => $path.$md_logo_name,
					'new_image' => $path,
					'maintain_ratio' => true,
					'width' => 200,
					'height' => 106
				);			
				$this->load->library('image_lib', $config2);
				$this->image_lib->resize();
			}		
			if($sm_logo_name !="" && $md_logo_name!=""){
				$imgdata = array('sm_logo'=>$sm_logo_name,'md_logo'=>$md_logo_name);
				$this->db->where('company_id',$maxid);
				$this->db->update(COMPANY_SETTINGS_TBL, $imgdata);
				//print  $this->db->last_query(); exit;
			}elseif($sm_logo_name !="" && $md_logo_name==""){
				$imgdata = array('sm_logo'=>$sm_logo_name);
				$this->db->where('company_id',$maxid);
				$this->db->update(COMPANY_SETTINGS_TBL, $imgdata);
				//print  $this->db->last_query(); die;
			}elseif($sm_logo_name =="" && $md_logo_name !=""){
				$imgdata = array('md_logo'=>$md_logo_name);
				$this->db->where('company_id',$maxid);
				$this->db->update(COMPANY_SETTINGS_TBL, $imgdata);
				//print  $this->db->last_query(); die;
			}
			return true;
		}else{
			return false;
		}
    }
	function DoUploadSM($MaxId){
		$file_name=''; $saveDir = ASSETS.'/img/company/';
		if($_FILES['sm_logo']['name'] == 0){
			$config['file_name']		="sm-".$MaxId;
			$config['overwrite']		=TRUE;
			$config['upload_path'] 		= ASSETS.'/img/company/';
			$config['allowed_types'] 	= 'gif|jpg|png|jpeg';
			$config['max_size'] 		= '2000';
			$config['max_width']  		= '2000';
			$config['max_height']  		= '2000';
			$config['maintain_ratio']  	= FALSE;
			$config['width']  			= '272';
			$config['height']  			= '136';
			$this->load->library("upload",$config); 
						
			// No problems with the file
			if($_FILES['sm_logo']['error'] == 0){
				//print_r($field); 
				if ($this->upload->do_upload("sm_logo")){
					$datas =  $this->upload->data();					
					$file_name = $datas['orig_name']; 
					//==== Resize Image ======
					$config2 = array(
						'source_image' => $datas['full_path'],
						'new_image' => $saveDir,
						'maintain_ratio' => false,
						'width' => 272,
						'height' => 136
					);			
					$this->load->library('image_lib', $config2);
					$this->image_lib->resize();
		
					$data = array('image_name'=>$file_name, 'img_id'=>$MaxId); 	
					$this->session->set_userdata($data);
					if($file_name !=""){
						$data = array('sm_logo'=>$file_name);
						$this->db->where('company_id',$MaxId);
						$this->db->update(COMPANY_SETTINGS_TBL, $data);
						//print  $this->db->last_query(); die;
					}
					return true;	
					$this->image_lib->clear();	
				}				
			}else{
				return false;
			}
		}
	}
	
	function DoUploadMD($MaxId){
		$file_name=''; $saveDir = ASSETS.'/img/company/';
		if($_FILES['md_logo']['name'] == 0){
			$config['file_name']		="md-".$MaxId;
			$config['overwrite']		=TRUE;
			$config['upload_path'] 		= ASSETS.'/img/company/';
			$config['allowed_types'] 	= 'gif|jpg|png|jpeg';
			$config['max_size'] 		= '2000';
			$config['max_width']  		= '2000';
			$config['max_height']  		= '2000';
			$config['maintain_ratio']  	= FALSE;
			$config['width']  			= '272';
			$config['height']  			= '136';
			$this->load->library("upload",$config); 
						
			// No problems with the file
			if($_FILES['md_logo']['error'] == 0){
				//print_r($field); 
				if ($this->upload->do_upload("md_logo")){
					$datas =  $this->upload->data();					
					$file_name = $datas['orig_name']; 
					//==== Resize Image ======
					$config2 = array(
						'source_image' => $datas['full_path'],
						'new_image' => $saveDir,
						'maintain_ratio' => false,
						'width' => 272,
						'height' => 136
					);			
					$this->load->library('image_lib', $config2);
					$this->image_lib->resize();
		
					$data = array('image_name'=>$file_name, 'img_id'=>$MaxId); 	
					$this->session->set_userdata($data);
					if($file_name !=""){
						$data = array('md_logo'=>$file_name);
						$this->db->where('company_id',$MaxId);
						$this->db->update(COMPANY_SETTINGS_TBL, $data);
						//print  $this->db->last_query(); die;
					}
					return true;
				    $this->image_lib->clear();
				}				
			}else{
				return false;	
			}
		}
	}
	function DoUpload($MaxId){
        $file_name = '';
        $saveDir = ASSETS . '/img/company/';
        if ($_FILES['sm_logo']['name'] == 0) {
            $config['file_name'] = "sm-" . $MaxId;
            $config['overwrite'] = TRUE;
            $config['upload_path'] = ASSETS . '/img/company/';
            $config['allowed_types'] = 'gif|jpg|png|jpeg';
            $config['max_size'] = '2000';
            $config['max_width'] = '2000';
            $config['max_height'] = '2000';
            $config['maintain_ratio'] = FALSE;
            $config['width'] = '272';
            $config['height'] = '136';
            $this->load->library("upload", $config);

            // No problems with the file
            if ($_FILES['sm_logo']['error'] == 0) {
                //print_r($field);
                if ($this->upload->do_upload("sm_logo")) {
                    $datas = $this->upload->data();
                    $file_name = $datas['orig_name'];
                    //==== Resize Image ======
                    $config2 = array(
                        'source_image' => $datas['full_path'],
                        'new_image' => $saveDir,
                        'maintain_ratio' => false,
                        'width' => 272,
                        'height' => 136
                    );
                    $this->load->library('image_lib', $config2);
                    $this->image_lib->resize();

                    $data = array('image_name' => $file_name, 'img_id' => $MaxId);
                    $this->session->set_userdata($data);
                    if ($file_name != "") {
                        $data = array('sm_logo' => $file_name);
                        $this->db->where('company_id', $MaxId);
                        $this->db->update(COMPANY_SETTINGS_TBL, $data);
                        //print  $this->db->last_query(); die;
                    }

                }

            }
        }

        if ($_FILES['md_logo']['name'] == 0) {
            $config['file_name'] = "md-" . $MaxId;
            $config['overwrite'] = TRUE;
            $config['upload_path'] = ASSETS . '/img/company/';
            $config['allowed_types'] = 'gif|jpg|png|jpeg';
            $config['max_size'] = '2000';
            $config['max_width'] = '2000';
            $config['max_height'] = '2000';
            $config['maintain_ratio'] = FALSE;
            $config['width'] = '272';
            $config['height'] = '136';
            $this->load->library("upload", $config);

            // No problems with the file
            if ($_FILES['md_logo']['error'] == 0) {
                //print_r($field);
                if ($this->upload->do_upload("md_logo")) {
                    $datas = $this->upload->data();
                    $file_name = $datas['orig_name'];
                    //==== Resize Image ======
                    $config2 = array(
                        'source_image' => $datas['full_path'],
                        'new_image' => $saveDir,
                        'maintain_ratio' => false,
                        'width' => 272,
                        'height' => 136
                    );
                    $this->load->library('image_lib', $config2);
                    $this->image_lib->resize();

                    $data = array('image_name' => $file_name, 'img_id' => $MaxId);
                    $this->session->set_userdata($data);
                    if ($file_name != "") {
                        $data = array('md_logo' => $file_name);
                        $this->db->where('company_id', $MaxId);
                        $this->db->update(COMPANY_SETTINGS_TBL, $data);
                        //print  $this->db->last_query(); die;
                    }

                }

            }
        }
    }
		/*
		if($_FILES['sm_logo'] !=""){			
			$config['file_name']		="sm-".$MaxId;
			$config['overwrite']		=TRUE;
			$config['upload_path'] 		= ASSETS.'/img/company/';
			$config['allowed_types'] 	= 'gif|jpg|png|jpeg';
			$config['max_size'] 		= '2000';
			$config['max_width']  		= '2000';
			$config['max_height']  		= '2000';
			$config['maintain_ratio']  	= FALSE;
			$config['width']  			= '272';
			$config['height']  			= '136';
			$this->load->library("upload",$config); 
			print_r($_FILES['sm_logo']); echo "error:".$_FILES['sm_logo']['error'];	
				// No problems with the file
				if($_FILES['sm_logo']['error'] == 0){ 
					// So lets upload 
					//print_r($_FILES['sm_logo']); echo "<br>"; exit;
					if ($this->upload->do_upload($_FILES['sm_logo'])){
						$datas =  $this->upload->data();					
						echo $file_name = $datas['orig_name']; 
						//==== Resize Image ======
						$config2 = array(
							'source_image' => $datas['full_path'],
							'new_image' => $saveDir,
							'maintain_ratio' => false,
							'width' => 272,
							'height' => 136
						);			
						$this->load->library('image_lib', $config2);
						$this->image_lib->resize();
			
						$data = array('image_name'=>$file_name, 'img_id'=>"sm-".$MaxId); 	
						$this->session->set_userdata($data);
						if($file_name !=""){
							$data = array("sm-$MaxId"=>$file_name);
							$this->db->where('company_id',$MaxId);
							$this->db->update(COMPANY_SETTINGS_TBL, $data);
							//print  $this->db->last_query(); die;						
						}
						exit;			
					}else{
						$errors = $this->upload->display_errors();					
					}
				}else{
					return false;
				}
			
		}//end if sm logo
		
		if($_FILES['md_logo'] !=""){
			$config['file_name']		="md-".$MaxId;
			$config['overwrite']		=TRUE;
			$config['upload_path'] 		= ASSETS.'/img/company/';
			$config['allowed_types'] 	= 'gif|jpg|png|jpeg';
			$config['max_size'] 		= '2000';
			$config['max_width']  		= '2000';
			$config['max_height']  		= '2000';
			$config['maintain_ratio']  	= FALSE;
			$config['width']  			= '272';
			$config['height']  			= '136';
			$this->load->library("upload",$config);
			//print_r($_FILES); exit;
			foreach($_FILES['md_logo'] as $field => $file){
				// No problems with the file
				if($_FILES['md_logo']['error'] == 0){
					// So lets upload
					if ($this->upload->do_upload($field)){
						$data =  $this->upload->data();					
						$file_name = $data['orig_name']; 
						//==== Resize Image ======
						$config2 = array(
							'source_image' => $data['full_path'],
							'new_image' => $saveDir,
							'maintain_ratio' => false,
							'width' => 272,
							'height' => 136
						);			
						$this->load->library('image_lib', $config2);
						$this->image_lib->resize();
			
						$data = array('image_name'=>$file_name, 'img_id'=>"md-".$MaxId); 	
						$this->session->set_userdata($data);
						if($file_name !=""){
							$data = array("md-$MaxId"=>$file_name);
							$this->db->where('company_id',$MaxId);
							$this->db->update(COMPANY_SETTINGS_TBL, $data);
							//print  $this->db->last_query(); die;						
						}					                  
					}else{
						$errors = $this->upload->display_errors();					
					}
				}else{
					return false;
				}
			}// end foreach
		}//end if md logo
		*/
//End Class	
}
