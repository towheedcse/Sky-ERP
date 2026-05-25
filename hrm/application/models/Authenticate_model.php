<?php 
class Authenticate_model extends CI_Model {
		
	function __construct()
	{
		parent::__construct();
	}	
			
	function Login()
	{
        $user_name      = $this->security->xss_clean($this->input->post('user_name'));
		$user_password 	= $this->security->xss_clean($this->input->post('password'));
		//$password =$this->encrypt->encode($pass);

		$this->db->where('user_name', $user_name);
		$this->db->where('user_status',1);
		$query = $this->db->get(USER_TBL);
		//print $this->db->last_query(); exit;
		 
		if($query->num_rows() == 1)
		{            
			$row = $query->row();
			$CheckPass=$row->password;
			$password =$this->encrypt->decode($CheckPass); 

			if($user_password==$password)
			{			
				$data = array(
					'created_by'	=> $row->user_id,
					'user_ref_id' 	=> $row->ref_id,
					'company_id' 	=> $row->company_id,
					'branch_id' 	=> $row->branch_id,
					'user_name' 	=> $row->user_name,
					'user_role' 	=> $row->user_role,
					'display_name' 	=> $row->display_name,
					'validate'  	=> true
				);
				if($row->user_role==5){
					 
					$data['shift_id']	 =1;
				}
				$sess=$this->session->set_userdata($data);
				$msg=$this->session->set_flashdata('msg', '<strong>Well done! </strong> You Login Successfully!');
				return true; 
			}else{
				return false;
			}
		}else{ 
			return false;
		}
	}

	function LoadCompanyInfo()
	{
		$psql   = "SELECT * FROM ".COMPANY_SETTINGS_TBL." WHERE company_id =1";
		$query = $this->db->query($psql);
		//print $this->db->last_query(); exit;

		if($query->num_rows() == 1)
		{
		$row = $query->row();
		$data = array(
		'company_name'	    	=> $row->company_name,
		'address' 	    		=> $row->address,
		'phone' 	    		=> $row->phone,
		'mobile' 		    	=> $row->mobile,
		'email' 	    		=> $row->email,
		'website' 	    		=> $row->site_url,
		'software_name' 		=> $row->backend_title,
		'short_name' 			=> $row->short_title,
		'sm_logo' 	    		=> $row->sm_logo,
		'md_logo' 		    	=> $row->md_logo,
		'language' 		    	=> $row->default_language,
		'admission_head' 		=> $row->admission_head,
		'discount_head' 		=> $row->discount_head,
		'full_scholarship' 		=> $row->f_scholarship_head,
		'partial_scholarship' 	=> $row->p_scholarship_head,
		'concession_hreads' 	=> $row->concession_hreads,
		'absent_head' 			=> $row->absent_head,
		'late_payment_head' 	=> $row->late_payment_head,
		'due_payment_head' 		=> $row->due_payment_head,
		'defaulter_head' 		=> $row->defaulter_head,
		'default_shift' 		=> $row->default_shift,
		'default_session' 		=> $row->default_session,
		'default_version' 		=> $row->default_version,
		'weekend' 				=> $row->weekend,
		'one_day_deduction' 	=> $row->one_day_deduction
		);
		$this->session->set_userdata($data);
		}else{
		return false;
		}
	}
   //End Class
}
