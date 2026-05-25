<?php 
class Employee_model extends CI_Model {
		
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
		$account_name		= $this->input->post('employee_name_en');
		$bangla_name		= $this->input->post('employee_name_bn');
		$father_name		= $this->input->post('fathers_name');
		$mother_name		= $this->input->post('mothers_name');
		$account_details	= $this->input->post('present_address');
		$permanent_address	= $this->input->post('permanent_address');
		$dob				= $this->formatDate($this->input->post('dob'));
		$nationality		= $this->input->post('nationality');
		$gender				= $this->input->post('gender');
		$blood_group		= $this->input->post('blood_group');
		$religion			= $this->input->post('religion');
		$mobile				= $this->input->post('mobile');
		$email				= $this->input->post('email');
		$account_type		= $this->input->post('employee_type');
		$subsidiary_level1	= 3;
		$subsidiary_level2	= 15;
		$subsidiary_level3	= 22;
		if($account_type==2){$prefix="A";}elseif($account_type==4){$prefix="C";}elseif($account_type==5){$prefix="B";}elseif($account_type==6){$prefix="T";}elseif($account_type==8){$prefix="R";}elseif($account_type==1 || $account_type==10){$prefix="E";}elseif($account_type==11){$prefix="S";}elseif($account_type==12 || $account_type==13){$prefix="I";}elseif($account_type==26){$prefix="P";}elseif($account_type==27){$prefix="L";}else{$prefix="H";}
		if($account_type==1 || $account_type==10){
			$emp_type = "1,10";
		}else{
			$emp_type = $account_type;
		}
		$head_id		= $this->getHeadID($emp_type,$prefix);
    	$created_by		= $this->session->userdata('created_by');
		$accountId		= $this->input->post('teacher_id');

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
			'bangla_name'    	=>$bangla_name,
			'account_details' 	=>$account_details,
			'permanent_address' =>$permanent_address,
			'dob'     			=>$dob,
			'nationality'     	=>$nationality,
			'father_name'    	=>$father_name,
			'mother_name'    	=>$mother_name,
			'mobile'     		=>$mobile,
			'email'     		=>$email,
			'gender'     		=>$gender,
			'blood_group'     	=>$blood_group,
			'religion'     		=>$religion,
			'created_by'     	=>$created_by
			);
			$this->db->insert(ACC_HEAD_TBL, $data);
			return $this->db->insert_id();
		}
		//print  $this->db->last_query();
   	}
	
	function InsertRecord(){		
		$created_by			=$this->session->userdata('created_by');
		$employeeid			=$this->input->post('teacher_id'); 
		$employee_code		=$this->input->post('employee_code');
		$card_id			=$this->input->post('card_id');
		$shift_id			=$this->input->post('shift_id');
		$institute_id		=$this->input->post('institute_id');
		$branch_id			=$this->input->post('branch_id');
		if($institute_id=="" || $branch_id ==""){
			$institute_id 	= $this->session->userdata('company_id');
			$branch_id 		= $this->session->userdata('branch_id'); 
		}
		$department_id		=$this->input->post('department_id');
		$section_id			=$this->input->post('section_id');
		$employee_name		=$this->input->post('employee_name_en');
		$father_name		=$this->input->post('fathers_name');
		$mother_name		=$this->input->post('mothers_name');
		$spouse_name		=$this->input->post('spouse_name');
		$address			=$this->input->post('present_address');
		$permanent_address	=$this->input->post('permanent_address');
		$designation		=$this->input->post('designation');
		$employee_type		=$this->input->post('employee_type');
		
		$appointment_type	=$this->input->post('appointment_type');
		$appointment_date	=$this->formatDate($this->input->post('appointment_date'));
		$joining_date		=$this->formatDate($this->input->post('joining_date'));	
		$major_subject		=implode(",", $this->input->post('major_subject'));
		$weekend			=implode(",", $this->input->post('weekend'));
		$education_qualification =$this->input->post('education_qualification');
		$extra_qualification=$this->input->post('extra_qualification');
		$marital_status		=$this->input->post('marital_status');
		
		$cash_salary		=$this->input->post('cash_salary');
		$tnt_allowance      =$this->input->post('tnt_allowance');
		$others_payble  	=$this->input->post('others_payble');
		$total_fix_payble   =($cash_salary+$tnt_allowance+$others_payble);
		
		$basic_salary		=$this->input->post('basic_salary');
		$houserent_allowance=$this->input->post('house_rent_allowance');
		$medical_allowance	=$this->input->post('medical_allowance');
		$transport_allowance=$this->input->post('transport_allowance');
		$communication_allowance=$this->input->post('communication_allowance');
		$festival_bonus		=$this->input->post('festival_bonus');
		$others_allowance	=$this->input->post('others_allowance');
		$gross_salary		=$this->input->post('gross_salary');
		$provident_fund		=$this->input->post('provident_fund');
		$income_tax			=$this->input->post('income_tax');
		$income_tax_amount	=$this->input->post('income_tax_amount');
		$loan_and_adv		=$this->input->post('loan_and_adv');
		$total_loan_and_adv	=$this->input->post('total_loan_and_adv');
		$loan_total_paid	=$this->input->post('loan_total_paid');
		$gross_deduction	=$this->input->post('gross_deduction');
		$net_salary			=$this->input->post('net_salary');
		$pf_achead_mapping	= $this->input->post('pf_achead_mapping');
		$loan_achead_mapping= $this->input->post('loan_achead_mapping');
		$login_id			=$this->input->post('login_id');
		$password			=$this->input->post('confirm_password');
		$rkey				=mt_rand(10,99);
		$accesskey			=$password.$rkey;
		if(empty($shift_id)){ $shift_id=$this->session->userdata('default_shift');}
		if(empty($cash_salary)){$cash_salary=0;} if(empty($tnt_allowance)){$tnt_allowance=0;}
		if(empty($others_payble)){$others_payble=0;} if(empty($total_fix_payble)){$total_fix_payble=0;}
		
		if(empty($basic_salary)){$basic_salary=0;} if(empty($houserent_allowance)){$houserent_allowance=0;}
		if(empty($medical_allowance)){$medical_allowance=0;} if(empty($transport_allowance)){$transport_allowance=0;}
		if(empty($communication_allowance)){$communication_allowance=0;} if(empty($festival_bonus)){$festival_bonus=0;}
		if(empty($others_allowance)){$others_allowance=0;} if(empty($gross_salary)){$gross_salary=0;} 
		if(empty($provident_fund)){$provident_fund=0;} if(empty($income_tax)){$income_tax=0;} 
		if(empty($loan_and_adv)){$loan_and_adv=0;} if(empty($total_loan_and_adv)){$total_loan_and_adv=0;} if(empty($loan_total_paid)){$loan_total_paid=0;}
		if(empty($gross_deduction)){$gross_deduction=0;} if(empty($net_salary)){$net_salary=0;}   
		
		if($employeeid==''){
			$employee_id 			= $this->InsertAccountHead();
			$photo 					= $this->UploadPhoto($employee_id);
			if(empty($employee_code)){
				$ssql = "SELECT head_id FROM ".ACC_HEAD_TBL." WHERE account_id = $employee_id AND company_id = $institute_id";
				$squery = $this->db->query($ssql);				
				if($squery->num_rows() >0){				   
				   $employee_code = $squery->row()->head_id;
				}
			}			
			$data = array(
			'hrm_employee_id'		=>$employee_id,
			'employee_code'			=>$employee_code,
			'card_id'				=>$card_id,
			'shift_id'				=>$shift_id,
			'company_id'    		=>$institute_id,
			'branch_id'    			=>$branch_id,
			'department_id'    		=>$department_id,
			'section_id'    		=>$section_id,
			'employee_name'    		=>$employee_name,	
			'fathers_name'    		=>$father_name,
			'mothers_name'    		=>$mother_name,
			'spouse_name'    		=>$spouse_name,	
			'address'    			=>$address,
			'permanent_address'    	=>$permanent_address,
			'designation'    		=>$designation,	
			'employee_type'    		=>$employee_type,
			'appointment_type'    	=>$appointment_type,
			'appointment_date'		=>$appointment_date,
			'joining_date'			=>$joining_date,
			'photo'    				=>$photo,
			'major_subject'    		=>$major_subject,
			'weekend'    			=>$weekend,
			'education_qualification'=>$education_qualification,
			'extra_qualification'  	=>$extra_qualification,
			'marital_status'		=>$marital_status,
			'cash_salary'		    =>$cash_salary,
			'tnt_allowance'		    =>$tnt_allowance,
			'others_payble'		    =>$others_payble,
			'total_fix_payble'		=>$total_fix_payble,
			'basic_salary'     		=>$basic_salary,
			'houserent_allowance'   =>$houserent_allowance,
			'medical_allowance'     =>$medical_allowance,
			'transport_allowance'   =>$transport_allowance,
			'communication_allowance'=>$communication_allowance,
			'festival_bonus'     	=>$festival_bonus,
			'others_allowance'     	=>$others_allowance,
			'gross_salary'     		=>$gross_salary,
			'provident_fund'     	=>$provident_fund,
			'income_tax'     		=>$income_tax,
			'income_tax_amount'     =>$income_tax_amount,
			'total_loan_and_adv'    =>$total_loan_and_adv,
			'loan_and_adv'     		=>$loan_and_adv,
			'loan_total_paid'       =>$loan_total_paid,
			'gross_deduction'    	=>$gross_deduction,
			'net_salary'    		=>$net_salary,
			'pf_achead_mapping'		=>$pf_achead_mapping,
			'loan_achead_mapping'	=>$loan_achead_mapping,
			'login_id'    			=>$login_id,
			'password'     			=>$accesskey,
			'status'     			=>1,
			'created_by'     		=>$created_by
			);
			//=== Remove empty field ====
			//$data = array_filter($data);
			//=== Remove unexpected field by value (e.g value) ====
			//if (($key = array_search('value', $data)) !== false) { unset($data[$key]);}						
			$this->db->insert(EMPLOYEE_TBL, $data);			
			$email = $this->input->post('email');
			$this->ManageUser($employee_id,$institute_id,$branch_id,$login_id,$password,$email,1,$employee_name,4,"I");			
		}else{			
			$email = $this->input->post('email');
			$this->EditRecord($employeeid,$institute_id,$branch_id);			
			$this->updateAccountHead($employeeid,$institute_id,$branch_id);
			$this->ManageUser($employeeid,$institute_id,$branch_id,$login_id,$password,$email,1,$employee_name,4,"U");
		}		
		//print  $this->db->last_query();
   	}
	function UpdateRecord(){
		$employeeid			=$this->input->post('teacher_id');
		$institute_id		=$this->input->post('institute_id');
		$branch_id			=$this->input->post('branch_id');
		if($institute_id=="" || $branch_id ==""){
			$institute_id 	=$this->session->userdata('company_id');
			$branch_id 		=$this->session->userdata('branch_id'); 
		}		
		$login_id			=$this->input->post('login_id');
		$password			=$this->input->post('confirm_password');
		$email 				=$this->input->post('email');
		$employee_name		=$this->input->post('employee_name_en');
		if($employeeid >0){
		$this->EditRecord($employeeid,$institute_id,$branch_id);			
		$this->updateAccountHead($employeeid,$institute_id,$branch_id);
		$this->ManageUser($employeeid,$institute_id,$branch_id,$login_id,$password,$email,1,$employee_name,4,"U");
		}
	}
	function updateAccountHead($account_id,$institute_id,$branch_id){
		    $update_by			= $this->session->userdata('created_by');
			$update_time 		= date("Y-m-d H:i:s");
			
			$account_name		= $this->input->post('employee_name_en');
			$bangla_name		= $this->input->post('employee_name_bn');
			$father_name		= $this->input->post('fathers_name');
			$mother_name		= $this->input->post('mothers_name');
			$account_details	= $this->input->post('present_address');
			$permanent_address	= $this->input->post('permanent_address');
			$dob				= $this->formatDate($this->input->post('dob'));
			$nationality		= $this->input->post('nationality');
			$gender				= $this->input->post('gender');
			$blood_group		= $this->input->post('blood_group');
			$religion			= $this->input->post('religion');
			$mobile				= $this->input->post('mobile');
			$email				= $this->input->post('email');
						
			$data = array(
			'account_name'    	=>$account_name,
			'bangla_name'    	=>$bangla_name,
			'account_details' 	=>$account_details,
			'permanent_address' =>$permanent_address,
			'dob'     			=>$dob,
			'nationality'     	=>$nationality,
			'father_name'    	=>$father_name,
			'mother_name'    	=>$mother_name,
			'mobile'     		=>$mobile,
			'email'     		=>$email,
			'gender'     		=>$gender,
			'blood_group'     	=>$blood_group,
			'religion'     		=>$religion,
			'update_by'     	=>$update_by,
			'update_time'     	=>$update_time
			);
			$this->db->where('account_id',$account_id);
			$this->db->where('company_id',$institute_id);
			$this->db->update(ACC_HEAD_TBL, $data);
	}
	function EditRecord($employee_id,$institute_id,$branch_id){
		$modified_by		=$this->session->userdata('created_by');
		$modified_time 		=date("Y-m-d H:i:s");
		$department_id		=$this->input->post('department_id');
		$section_id			=$this->input->post('section_id'); 
		$card_id			=$this->input->post('card_id');
		$shift_id			=$this->input->post('shift_id');	
		$employee_name		=$this->input->post('employee_name_en');
		$father_name		=$this->input->post('fathers_name');
		$mother_name		=$this->input->post('mothers_name');
		$spouse_name		=$this->input->post('spouse_name');
		$address			=$this->input->post('present_address');
		$permanent_address	=$this->input->post('permanent_address');
		$designation		=$this->input->post('designation');
		$employee_type		=$this->input->post('employee_type');
		
		$appointment_type	=$this->input->post('appointment_type');
		$appointment_date	=$this->formatDate($this->input->post('appointment_date'));
		$joining_date		=$this->formatDate($this->input->post('joining_date'));	
		$major_subject		=implode(",", $this->input->post('major_subject'));
		$weekend			=implode(",", $this->input->post('weekend'));
		$education_qualification =$this->input->post('education_qualification');
		$extra_qualification=$this->input->post('extra_qualification');
		$marital_status		=$this->input->post('marital_status');
		
		$cash_salary		=$this->input->post('cash_salary');
		$tnt_allowance      =$this->input->post('tnt_allowance');
		$others_payble  	=$this->input->post('others_payble');
		$total_fix_payble   =($cash_salary+$tnt_allowance+$others_payble);		
		
		$basic_salary		=$this->input->post('basic_salary');
		$houserent_allowance=$this->input->post('house_rent_allowance');
		$medical_allowance	=$this->input->post('medical_allowance');
		$transport_allowance=$this->input->post('transport_allowance');
		$communication_allowance=$this->input->post('communication_allowance');
		$festival_bonus		=$this->input->post('festival_bonus');
		$others_allowance	=$this->input->post('others_allowance');
		$gross_salary		=$this->input->post('gross_salary');
		$provident_fund		=$this->input->post('provident_fund');
		$income_tax			=$this->input->post('income_tax');
		$income_tax_amount	=$this->input->post('income_tax_amount');
		$loan_and_adv		=$this->input->post('loan_and_adv');
		$total_loan_and_adv	=$this->input->post('total_loan_and_adv');
		$loan_total_paid	=$this->input->post('loan_total_paid');
		$gross_deduction	=$this->input->post('gross_deduction');
		$net_salary			=$this->input->post('net_salary');
		$pf_achead_mapping	= $this->input->post('pf_achead_mapping');
		$loan_achead_mapping= $this->input->post('loan_achead_mapping');
		$login_id			=$this->input->post('login_id');
		$password			=$this->input->post('confirm_password');
		$status             =$this->input->post('status');
		$employee_photo		=$_FILES['employee_photo'];		
		$rkey				=mt_rand(10,99);
		$accesskey			=$password.$rkey;
		
		if($employee_photo!=""){
			$photo 			=$this->UploadPhoto($employee_id);				
		}else{
			$ssql   = "SELECT photo FROM ".EMPLOYEE_TBL." WHERE hrm_employee_id = $employee_id AND company_id = $institute_id";
			$squery = $this->db->query($ssql);				
			if($squery->num_rows() >0){				   
			   $photo = $squery->row()->photo;
			}
		}
		if(empty($shift_id)){ $shift_id=$this->session->userdata('default_shift');}
		if(empty($cash_salary)){$cash_salary=0;} if(empty($tnt_allowance)){$tnt_allowance=0;}
		if(empty($others_payble)){$others_payble=0;} if(empty($total_fix_payble)){$total_fix_payble=0;}
		if(empty($basic_salary)){$basic_salary=0;} if(empty($houserent_allowance)){$houserent_allowance=0;}
		if(empty($medical_allowance)){$medical_allowance=0;} if(empty($transport_allowance)){$transport_allowance=0;}
		if(empty($communication_allowance)){$communication_allowance=0;} if(empty($festival_bonus)){$festival_bonus=0;}
		if(empty($others_allowance)){$others_allowance=0;} if(empty($gross_salary)){$gross_salary=0;} 
		if(empty($provident_fund)){$provident_fund=0;} if(empty($income_tax)){$income_tax=0;} 
		if(empty($loan_and_adv)){$loan_and_adv=0;} if(empty($total_loan_and_adv)){$total_loan_and_adv=0;} if(empty($loan_total_paid)){$loan_total_paid=0;}
		if(empty($gross_deduction)){$gross_deduction=0;} if(empty($net_salary)){$net_salary=0;}   
					
		$data = array(
			'card_id'				=>$card_id,
			'shift_id'				=>$shift_id,
			'company_id'    		=>$institute_id,
			'branch_id'    			=>$branch_id,
			'department_id'    		=>$department_id,
			'section_id'    		=>$section_id,
			'employee_name'    		=>$employee_name,	
			'fathers_name'    		=>$father_name,
			'mothers_name'    		=>$mother_name,
			'spouse_name'    		=>$spouse_name,	
			'address'    			=>$address,
			'permanent_address'    	=>$permanent_address,
			'designation'    		=>$designation,	
			'employee_type'    		=>$employee_type,
			'appointment_type'    	=>$appointment_type,
			'appointment_date'		=>$appointment_date,
			'joining_date'			=>$joining_date,
			'photo'    				=>$photo,
			'major_subject'    		=>$major_subject,
			'weekend'    			=>$weekend,
			'education_qualification'=>$education_qualification,
			'extra_qualification'  	=>$extra_qualification,
			'marital_status'		=>$marital_status,
			'cash_salary'		    =>$cash_salary,
			'tnt_allowance'		    =>$tnt_allowance,
			'others_payble'		    =>$others_payble,
			'total_fix_payble'		=>$total_fix_payble,
			'basic_salary'     		=>$basic_salary,
			'houserent_allowance'   =>$houserent_allowance,
			'medical_allowance'     =>$medical_allowance,
			'transport_allowance'   =>$transport_allowance,
			'communication_allowance'=>$communication_allowance,
			'festival_bonus'     	=>$festival_bonus,
			'others_allowance'     	=>$others_allowance,
			'gross_salary'     		=>$gross_salary,
			'provident_fund'     	=>$provident_fund,
			'income_tax'     		=>$income_tax,
			'income_tax_amount'     =>$income_tax_amount,
			'total_loan_and_adv'    =>$total_loan_and_adv,
			'loan_and_adv'     		=>$loan_and_adv,
			'loan_total_paid'       =>$loan_total_paid,
			'gross_deduction'    	=>$gross_deduction,
			'net_salary'    		=>$net_salary,
			'pf_achead_mapping'		=>$pf_achead_mapping,
			'loan_achead_mapping'	=>$loan_achead_mapping,
			'password'     			=>$accesskey,
			'status'     			=>$status,
			'modified_by'     		=>$modified_by,
			'modified_time'     	=>$modified_time
		);
		//=== Remove empty field ====
		//$data = array_filter($data);
		//=== Remove unexpected field by value (e.g value) ====
		//if (($key = array_search('value', $data)) !== false) { unset($data[$key]);}
		$this->db->where('hrm_employee_id',$employee_id);
		$this->db->where('company_id',$institute_id);
		$this->db->update(EMPLOYEE_TBL, $data); //print  $this->db->last_query(); exit;
    }
	
	function ManageUser($employee_id,$institute_id,$branch_id,$login_id,$password,$email,$status,$employee_name,$user_role,$mode){
		$created_by	= $this->session->userdata('created_by');				
		$rkey		= mt_rand(10,99);
		$access_key = $password.$rkey; 
		$password   = $this->encrypt->encode($password); 		
		if($mode=="I"){		
		  $SQL="INSERT INTO ".USERS_TBL."(ref_id,company_id,branch_id,user_name,password,email,user_status,display_name,access_key,user_role,created_by) ";
		  $SQL.="VALUES('".$employee_id."','".$institute_id."','".$branch_id."','".$login_id."','".$password."','".$email."','".$status."','".$employee_name."','".$access_key."','".$user_role."','".$created_by."')";
		  $this->db->query($SQL);
		}else{
		  $CSQL= "UPDATE ".USERS_TBL." SET company_id='".$institute_id."', branch_id='".$branch_id."', password='".$password."', email='".$email."', user_status='".$status."', display_name='".$employee_name."', access_key='".$access_key."' WHERE ref_id='".$employee_id."' AND user_name = '".$login_id."'";
		  $this->db->query($CSQL);
		}

	}   
	//======= Start Short Employee ====
	function AddAjaxRecord(){		
		$created_by			=$this->session->userdata('created_by');
		$employeeid			=$this->input->post('employee_id');
		$institute_id 	    = $this->session->userdata('company_id');
		$branch_id 		    = $this->session->userdata('branch_id'); 
		$employee_name		=$this->input->post('employee_name');
		$designation		=$this->input->post('designation');
		$login_id			=$this->input->post('login_id');
		$password			=$this->input->post('confirm_password');
		$rkey				=mt_rand(10,99);
		$accesskey			=$password.$rkey; $weekend="Friday";
		if(empty($institute_id)){$institute_id=1; $branch_id=1;}
		
		$shift_id=""; $basic_salary=0; $houserent_allowance=0; $medical_allowance=0; $transport_allowance=0; $communication_allowance=0; $festival_bonus=0; 
		$others_allowance=0; $gross_salary=0; $provident_fund=0; $income_tax=0; $loan_and_adv=0; $gross_deduction=0; $net_salary=0;
		if(empty($shift_id)){ $shift_id=$this->session->userdata('default_shift');}
		if(empty($basic_salary)){$basic_salary=0;} if(empty($houserent_allowance)){$houserent_allowance=0;}
		if(empty($medical_allowance)){$medical_allowance=0;} if(empty($transport_allowance)){$transport_allowance=0;}
		if(empty($communication_allowance)){$communication_allowance=0;} if(empty($festival_bonus)){$festival_bonus=0;}
		if(empty($others_allowance)){$others_allowance=0;} if(empty($gross_salary)){$gross_salary=0;} 
		if(empty($provident_fund)){$provident_fund=0;} if(empty($income_tax)){$income_tax=0;} 
		if(empty($loan_and_adv)){$loan_and_adv=0;} if(empty($gross_deduction)){$gross_deduction=0;} if(empty($net_salary)){$net_salary=0;}   
		
		$account_type       = 1;
		$groupId			= 2;
		$subsidiary_level1	= 3;
		$subsidiary_level2	= 15;
		$subsidiary_level3	= 22;
		if($account_type==2){$prefix="A";}elseif($account_type==4){$prefix="C";}elseif($account_type==5){$prefix="B";}elseif($account_type==6){$prefix="T";}elseif($account_type==8){$prefix="R";}elseif($account_type==1 || $account_type==10){$prefix="E";}elseif($account_type==11){$prefix="S";}elseif($account_type==12 || $account_type==13){$prefix="I";}elseif($account_type==26){$prefix="P";}elseif($account_type==27){$prefix="L";}else{$prefix="H";}
		if($account_type==1 || $account_type==10){
			$emp_type = "1,10";
		}
		$employee_code		= $this->getHeadID($emp_type,$prefix);

		if($employeeid==""){
			$data = array(
			'company_id'    	=>$institute_id,
			'branch_id'    		=>$branch_id,
			'head_id'    		=>$employee_code,
			'group_id'    		=>$groupId,
			'subsidiary_level1' =>$subsidiary_level1,
			'subsidiary_level2' =>$subsidiary_level2,
			'subsidiary_level3' =>$subsidiary_level3,
			'head_type'    		=>$account_type,	
			'account_name'    	=>$employee_name,
			'created_by'     	=>$created_by
			);
			$this->db->insert(ACC_HEAD_TBL, $data);
			$employee_id = $this->db->insert_id();
		}
		if($employee_id >0){			
			$data = array(
			'hrm_employee_id'		=>$employee_id,
			'employee_code'			=>$employee_code,
			'shift_id'				=>$shift_id,
			'company_id'    		=>$institute_id,
			'branch_id'    			=>$branch_id,
			'employee_name'    		=>$employee_name,
			'designation'    		=>$designation,	
			'employee_type'    		=>$account_type,
			'weekend'    			=>$weekend,
			'basic_salary'     		=>$basic_salary,
			'houserent_allowance'   =>$houserent_allowance,
			'medical_allowance'     =>$medical_allowance,
			'transport_allowance'   =>$transport_allowance,
			'communication_allowance'=>$communication_allowance,
			'festival_bonus'     	=>$festival_bonus,
			'others_allowance'     	=>$others_allowance,
			'gross_salary'     		=>$gross_salary,
			'provident_fund'     	=>$provident_fund,
			'income_tax'     		=>$income_tax,
			'loan_and_adv'     		=>$loan_and_adv,
			'gross_deduction'    	=>$gross_deduction,
			'net_salary'    		=>$net_salary,
			'login_id'    			=>$login_id,
			'password'     			=>$accesskey,
			'status'     			=>1,
			'created_by'     		=>$created_by
			);
			//=== Remove empty field ====
			$data = array_filter($data);
			//=== Remove unexpected field by value (e.g value) ====
			if (($key = array_search('value', $data)) !== false) { unset($data[$key]);}						
			$this->db->insert(EMPLOYEE_TBL, $data);			
			$email ="salesman@liragroup.com";
			$this->ManageUser($employee_id,$institute_id,$branch_id,$login_id,$password,$email,1,$employee_name,4,"I");			
		}
			
		$PSQL= "SELECT * FROM ".EMPLOYEE_TBL." WHERE employee_type IN(1) AND status=1 ";			
		$PSQL.= " GROUP BY hrm_employee_id ORDER BY employee_name ASC";
		$query = $this->db->query($PSQL);
		$options = "<option value='0'>".$this->lang->line('select')." ".$this->lang->line('sales_person')."</option>";
		foreach($query->result() as $irow){
			if($employee_id >0 && $employee_id == $irow->hrm_employee_id){
				$selected = "selected='selected'";
			}else{ $selected = ""; }
			$options.="<option  value='".$irow->hrm_employee_id."' $selected >".$irow->employee_name."</option>";
		}
		echo $options;
		
		//print  $this->db->last_query();
   	}
   	//======= End Short Employee ====
   	
	function UploadPhoto($img_id){
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
		//print_r($_FILES);
		foreach($_FILES as $field => $file){
			// No problems with the file
			if($file['error'] == 0){ //print_r($file);
				// So lets upload 
				if ($this->upload->do_upload($field)){ 
					$data =  $this->upload->data();					
					$file_name = $data['orig_name']; 
					//==== Resize Image ======
					$config2 = array(
						'source_image' => $data['full_path'],
						'new_image' => $saveDir,
						'maintain_ratio' => FALSE,
						'width' => 185,
						'height' => 200
					);			
					$this->load->library('image_lib', $config2);
					$this->image_lib->resize();					
					return trim("photo/".$file_name);
					                    
				}else{
					echo $errors = $this->upload->display_errors();
					return false;
				}
			}
		}// end foreach
		
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
	function FillEmployee(){
		$employee_id =$this->input->post('id');
		$this->db->select('*');
		$this->db->from(EMPLOYEE_TBL);
		$this->db->where('status', 1);
		$this->db->where('hrm_employee_id', $employee_id);
		$query = $this->db->get();
		return $query->row();
	}
	function GetAjaxEmployeeList($employee_id){		
		$PSQL= "SELECT * FROM ".ACC_HEAD_TBL." WHERE head_type IN(1,10) AND status=1 ";			
		$PSQL.= " GROUP BY account_id ORDER BY account_name ASC";
		$query = $this->db->query($PSQL);
		$options = "<option value='0'>".$this->lang->line('select')." ".$this->lang->line('student')."</option>";
		foreach($query->result() as $irow){			
			if($employee_id >0 && $employee_id == $irow->account_id){ 
				$selected = "selected='selected'"; 
			}else{ $selected = ""; }			
			$options.="<option  value='".$irow->account_id."' $selected >".$irow->account_name."</option>";
		}
		return $options;
	}		
	function GetSubjectNames($subject_id){
		$SubjectNames ="";
		if($subject_id !=""){
			$PSQL= "SELECT * FROM ".SUBJECT_TBL." WHERE subject_id IN($subject_id) AND status=1 ";			
			$PSQL.= " GROUP BY subject_id ORDER BY subject_name ASC";
			$query = $this->db->query($PSQL);
			$SubjectNames ="";
			foreach($query->result() as $irow){						
				$SubjectNames.=$irow->subject_name.", ";
			}
			$SubjectNames = substr($SubjectNames, 0, -2);
		}
		return $SubjectNames;
	}
   	//============== Category Retrive by Ajax================
   	function GetRecordGrid(){
		$menu_slug= $this->uri->segment(1);
		$this->load->model('Site_model');
		$hasEditPM = $this->Site_model->hasOptionPermission($menu_slug,"Edit");
		$hasDelPM  = $this->Site_model->hasOptionPermission($menu_slug,"Delete");
		$hasPrintPM= $this->Site_model->hasOptionPermission($menu_slug,"Print");
	   	$company_id	        =$this->input->post('company-id');
	   	$branch_id	        =$this->input->post('branch-id');
	   	$department_id	    =$this->input->post('department-id');
	   	$section_id	        =$this->input->post('section-id');
	   	$shift_id	        =$this->input->post('shift-id');
	   	$card_id	        =$this->input->post('card-id');
	   	$employee_name	    =$this->input->post('employee-name');
	   	$from	=$this->input->post('from');
		$to	=$this->input->post('to');
		if($from==""){ $from=0;} if($to==""){ $to=50;}			
		$head_types = array(1,10);	
		$this->db->select('a.*,p.account_id,p.group_id,p.subsidiary_level1,p.subsidiary_level2,p.head_type,p.account_name,p.bangla_name,p.nationality,p.mobile,p.email,p.gender,p.blood_group,p.religion,i.company_name,b.branch_name,b.branch_code,q.qualification_name,DATE_FORMAT(a.appointment_date ,"%d-%m-%Y") as appointment_dates,DATE_FORMAT(a.joining_date ,"%d-%m-%Y") as joining_dates,DATE_FORMAT(p.dob ,"%d-%m-%Y") as birthday',FALSE);
		$this->db->from(EMPLOYEE_TBL." AS a");
		$this->db->join(ACC_HEAD_TBL.' AS p', 'p.account_id=a.hrm_employee_id','LEFT');
	  	$this->db->join(COMPANY_SETTINGS_TBL.' AS i', 'i.company_id=a.company_id','LEFT');
	  	$this->db->join(BRANCH_TBL.' AS b', 'b.id=a.branch_id','LEFT');
	  	$this->db->join(QUALIFICATION_TBL.' AS q', 'q.qualification_id=a.education_qualification','LEFT');
		$this->db->where_in('p.head_type',$head_types);
		if($company_id>0){
		$this->db->where('a.company_id',$company_id);
		}
		if($branch_id>0){
		$this->db->where('a.branch_id',$branch_id);
		}
		if($department_id>0){
		$this->db->where('a.department_id',$department_id);
		}
		if($section_id>0){
		$this->db->where('a.section_id',$section_id);
		}
		if($shift_id>0){
		$this->db->where('a.shift_id',$shift_id);
		}
		if($card_id>0){
		$this->db->where('a.card_id',$card_id);
		}
		if($employee_name !=""){
		$this->db->like('a.employee_name',$employee_name);
		}

		$this->db->group_by('a.hrm_employee_id');
		$this->db->order_by('a.employee_code','ASC');
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
				<th width="13%">'.$this->lang->line("company").' '.$this->lang->line("details").'</th>
			  	<th width="14%">'.$this->lang->line("employee").' '.$this->lang->line("details").'</th>
				<th width="15%">'.$this->lang->line("contact").' '.$this->lang->line("details").'</th>
				<th width="13%">'.$this->lang->line("fixed_payble").'</th>
				<th width="12%">'.$this->lang->line("gross_salary").'</th>
				<th width="13%">'.$this->lang->line("gross_deduction").'</th>
				<th width="10%">'.$this->lang->line("net_salary").'</th>
				<th width="8%" class="text-center">'.$this->lang->line("options").'</th>
			  </tr>
			</thead>';
			  $i=1; 
			  foreach($query->result() as $row){
			  //if($i%2==0){ $tblrow="success";}else{$tblrow="warning";}
			  if($row->status==1){ $tblrow="bg-success";}else{$tblrow="bg-danger";}
			  echo "<tr class='default'>
			  	<td class='".$tblrow."'>".$i."</td>
				<td>
				".$row->company_name.",<br>".$row->branch_name."
				</td>
			  	<td>
				<img src='".base_url().ASSETS.'/img/'.$row->photo."' height='30'/><br>
				".$row->account_name.", Emp ID: ".$row->card_id."<br>ERP ID: ".$row->employee_code."				
				</td>
				<td>".$row->address."<br><i class='fas fa-user'></i> ".$row->login_id."<br><i class='fas fa-mobile'></i> ".$row->mobile."<br>".$row->email."<br>".$row->qualification_name."</td>
				<td>".$row->total_fix_payble."</td>
				<td>".$row->gross_salary."</td>
				<td>".$row->gross_deduction."</td>
				<td>".$row->net_salary."</td>
				<td class='text-center align-middle'>";
		    	if($hasEditPM){
				echo "<span data-toggle='tooltip' data-placement='top' data-original-title='Edit'><a class='btn btn-info btn-xs' onclick=editRecord('".$row->hrm_employee_id."') id='".$row->hrm_employee_id."' href='#'><i class='fas fa-edit'></i></a></span>&nbsp;";
				}
				if($hasDelPM){
				echo "<span data-toggle='tooltip' data-placement='top' data-original-title='Delete'><a class='btn btn-danger btn-xs' data-toggle='modal' onclick=deleteRecord('".$row->hrm_employee_id."') id='".$row->hrm_employee_id."' data-target='#deleteModal'><i class='fa fa-trash'></i></a></span>";
				}
				if($hasPrintPM){
				echo "<div class='clearfix' style='margin-top:6px;'></div><span data-toggle='tooltip' data-original-title='View Form'><a class='btn btn-success btn-sm' target='_blank' href='".base_url()."employee/ViewProfile/".$row->hrm_employee_id."'><i class='fa fa-print'></i> Profile</a></span>";
				echo "<div class='clearfix' style='margin-top:6px;'></div><span data-toggle='tooltip' data-original-title='View Form'><a class='btn btn-warning btn-sm' target='_blank' href='".base_url()."employee/ViewLeave/".$row->hrm_employee_id."'><i class='fa fa-print'></i> Leave &nbsp;</a></span>";
				}
			    echo "</td>
			  </tr>";
			  $i++;
			  }
		echo '</table>';
	    	echo "<div class='float-right'>$Pagination</div>";
	}
    
	function GetTotalRecord(){
	   	$company_id	        =$this->input->post('company-id');
	   	$branch_id	        =$this->input->post('branch-id');
	   	$department_id	    =$this->input->post('department-id');
	   	$section_id	        =$this->input->post('section-id');
	   	$shift_id	        =$this->input->post('shift-id');
	   	$card_id	        =$this->input->post('card-id');
		$head_types = array(1,10);	
		$this->db->select('a.*,p.account_id,p.group_id,p.subsidiary_level1,p.subsidiary_level2,p.head_type,p.account_name,p.bangla_name,p.nationality,p.mobile,p.email,p.gender,p.blood_group,p.religion,i.company_name,b.branch_name,b.branch_code',FALSE);
		$this->db->from(EMPLOYEE_TBL." AS a");
		$this->db->join(ACC_HEAD_TBL.' AS p', 'p.account_id=a.hrm_employee_id','LEFT');
	  	$this->db->join(COMPANY_SETTINGS_TBL.' AS i', 'i.company_id=a.company_id','LEFT');
	  	$this->db->join(BRANCH_TBL.' AS b', 'b.id=a.branch_id','LEFT');
		$this->db->where_in('p.head_type',$head_types);
		if($company_id>0){
		$this->db->where('a.company_id',$company_id);
		}
		if($branch_id>0){
		$this->db->where('a.branch_id',$branch_id);    
		}
		if($department_id>0){
		$this->db->where('a.department_id',$department_id);    
		}
		if($section_id>0){
		$this->db->where('a.section_id',$section_id);    
		}
		if($shift_id>0){
		$this->db->where('a.shift_id',$shift_id);    
		}
		if($card_id>0){
		$this->db->where('a.card_id',$card_id);    
		}
		$this->db->group_by('a.hrm_employee_id');
		$this->db->order_by('a.employee_code','ASC');
		$query = $this->db->get();
		if($query->num_rows() >0){
			return $query->num_rows();
		}else{
			return 0;
		}//echo $this->db->last_query();
	}
	
	function DelRecord(){
		$employee_id =$this->input->post('id');
		$status =$this->input->post('status');
		if(!isset($status)){$status=0;}
		//$this->db->where('employee_id',$employee_id);
		//$this->db->delete(EMPLOYEE_TBL);
		$ESQL= "UPDATE ".EMPLOYEE_TBL." SET status='".$status."' WHERE hrm_employee_id='".$employee_id."'";
		$this->db->query($ESQL);
		
		$ASQL= "UPDATE ".ACC_HEAD_TBL." SET status='".$status."'WHERE account_id='".$employee_id."'";
		$this->db->query($ASQL);
		
		$USQL= "UPDATE ".USERS_TBL." SET user_status='".$status."'WHERE ref_id='".$employee_id."'";
		$this->db->query($USQL);		
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
