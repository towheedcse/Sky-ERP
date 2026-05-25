<?php 
class Company_settings_model extends CI_Model {
		
	function __construct()
	{
		parent::__construct();
	}	
	
	function InsertRecord(){
		$companyName			=$this->input->post('company_name');
		$address				=$this->input->post('address');
		$phone					=$this->input->post('phone');
		$mobile					=$this->input->post('mobile');
		$email					=$this->input->post('email');
		$site_url				=$this->input->post('site_url');
		$ssl_url				=$this->input->post('ssl_url');
		$backend_title			=$this->input->post('backend_title');
		$frontend_title			=$this->input->post('frontend_title');
		$short_title			=$this->input->post('short_title');
		$copyright				=$this->input->post('copyright');
		$keywords				=$this->input->post('keywords');
		$meta_description		=$this->input->post('meta_description');
		$currency_sign			=$this->input->post('currency_sign');
		$currency_code			=$this->input->post('currency_code');
		$default_language		=$this->input->post('default_language');
		$license_key			=$this->input->post('license_key');
		$secret_key				=$this->input->post('secret_key');
		$site_offline			=$this->input->post('site_offline');
		$offline_message		=$this->input->post('offline_msg');
		$allow_registration		=$this->input->post('allow_registration');
		$booking_cancellation	=$this->input->post('booking_cancellation');
		$admission_head			=$this->input->post('admission_head');
		$discount_head			=$this->input->post('discount_head');
		$f_scholarship_head		=$this->input->post('fullscholarship_head');
		$p_scholarship_head		=$this->input->post('partialscholarship_head');		
		$concession_hreads		=implode(",", $this->input->post('concession_hreads'));
		$absent_head			=$this->input->post('absent_head');
		$late_payment_head		=$this->input->post('late_payment_head');
		$due_payment_head		=$this->input->post('due_payment_head');
		$defaulter_head			=$this->input->post('defaulter_head');
		$default_shift			=$this->input->post('default_shift');		
		$weekend				=implode(",", $this->input->post('weekend'));
		$one_day_deduction		=$this->input->post('one_day_deduction');
		$created_by				=$this->session->userdata('id');
		$companyId				=$this->input->post('company_id');
	    
		//$data			=stripslashes($this->input->post('data'));
		//$data			=$this->input->post('company');
		//echo $address	=$this->input->post('address');
		if($companyId==''){
			/*$data = json_decode($data,true);
			print_r($data);
			exit;
			$data = json_decode($data);
			print_r($data);*/
			$data = array(
			    'companyName' 			=>$companyName,
			    'address' 				=>$address,
			    'phone' 				=>$phone,
			    'mobile' 				=>$mobile,
			    'email' 				=>$email,
			    'site_url' 				=>$site_url,
			    'ssl_url' 				=>$ssl_url,
			    'backend_title' 		=>$backend_title,
			    'frontend_title' 		=>$frontend_title,
			    'short_title' 			=>$short_title,
			    'copyright' 			=>$copyright,
			    'keywords' 				=>$keywords,
			    'meta_description' 		=>$meta_description,
			    'currency_sign' 		=>$currency_sign,
			    'currency_code' 		=>$currency_code,
			    'default_language' 		=>$default_language,
			    'license_key' 			=>$license_key,
			    'secret_key' 			=>$secret_key,
			    'site_offline' 			=>$site_offline,
			    'offline_msg' 			=>$offline_message,
			    'allow_registration' 	=>$allow_registration,
			    'booking_cancellation' 	=>$booking_cancellation,
			    'admission_head' 		=>$admission_head,
			    'discount_head' 		=>$discount_head,
			    'f_scholarship_head' 	=>$f_scholarship_head,
			    'p_scholarship_head' 	=>$p_scholarship_head,
			    'concession_hreads' 	=>$concession_hreads,
			    'absent_head' 			=>$absent_head,
			    'late_payment_head' 	=>$late_payment_head,
			    'due_payment_head' 		=>$due_payment_head,
			    'defaulter_head' 		=>$defaulter_head,
			    'default_shift' 		=>$default_shift,
			    'weekend' 				=>$weekend,
			    'one_day_deduction'     =>$one_day_deduction,
			    'created_by' =>$created_by
        		);
			$this->db->insert(COMPANY_SETTINGS_TBL, $data);
		}else{
			$this->EditRecord($companyId);
		}
		//print  $this->db->last_query();
   	}
	
    function EditRecord($companys_id){
        $companyName			=$this->input->post('company_name');
        $address				=$this->input->post('address');
        $phone					=$this->input->post('phone');
        $mobile					=$this->input->post('mobile');
        $email					=$this->input->post('email');
        $site_url				=$this->input->post('site_url');
        $ssl_url				=$this->input->post('ssl_url');
        $backend_title			=$this->input->post('backend_title');
        $frontend_title			=$this->input->post('frontend_title');
        $short_title			=$this->input->post('short_title');
        $copyright				=$this->input->post('copyright');
        $keywords				=$this->input->post('keywords');
        $meta_description		=$this->input->post('meta_description');
        $currency_sign			=$this->input->post('currency_sign');
        $currency_code			=$this->input->post('currency_code');
        $default_language		=$this->input->post('default_language');
        $license_key			=$this->input->post('license_key');
        $secret_key				=$this->input->post('secret_key');
        $site_offline			=$this->input->post('site_offline');
        $offline_message		=$this->input->post('offline_msg');
        $allow_registration		=$this->input->post('allow_registration');
        $booking_cancellation	=$this->input->post('booking_cancellation');
		$admission_head			=$this->input->post('admission_head');
		$discount_head			=$this->input->post('discount_head');
		$f_scholarship_head		=$this->input->post('fullscholarship_head');
		$p_scholarship_head		=$this->input->post('partialscholarship_head');
		$concession_hreads		=implode(",", $this->input->post('concession_hreads'));
		$absent_head			=$this->input->post('absent_head');
		$late_payment_head		=$this->input->post('late_payment_head');
		$due_payment_head		=$this->input->post('due_payment_head');
		$defaulter_head			=$this->input->post('defaulter_head');
		$default_shift			=$this->input->post('default_shift');		
		$weekend				=implode(",", $this->input->post('weekend'));
		$one_day_deduction		=$this->input->post('one_day_deduction');
        $modified_by			=$this->session->userdata('id');

		if($companys_id !="") {
			$data = array(
				'company_name'          =>$companyName,
		        'address'               =>$address,
		        'phone'                 =>$phone,
		        'mobile'                =>$mobile,
		        'email'                 =>$email,
		        'site_url'              =>$site_url,
		        'ssl_url'               =>$ssl_url,
		        'backend_title'         =>$backend_title,
		        'frontend_title'        =>$frontend_title,
		        'short_title'           =>$short_title,
		        'copyright'             =>$copyright,
		        'keywords'              =>$keywords,
		        'meta_description'      =>$meta_description,
		        'currency_sign'         =>$currency_sign,
		        'currency_code'         =>$currency_code,
		        'default_language'      =>$default_language,
		        'license_key'           =>$license_key,
		        'secret_key'            =>$secret_key,
		        'site_offline'          =>$site_offline,
		        'offline_msg'           =>$offline_message,
		        'allow_registration'    =>$allow_registration,
		        'booking_cancellation'  =>$booking_cancellation,
			    'admission_head' 		=>$admission_head,
			    'discount_head' 		=>$discount_head,
			    'f_scholarship_head' 	=>$f_scholarship_head,
			    'p_scholarship_head' 	=>$p_scholarship_head,
			    'concession_hreads' 	=>$concession_hreads,
			    'absent_head' 			=>$absent_head,
			    'late_payment_head' 	=>$late_payment_head,
			    'due_payment_head' 		=>$due_payment_head,
			    'defaulter_head' 		=>$defaulter_head,
			    'default_shift' 		=>$default_shift,
			    'weekend' 				=>$weekend,
			    'one_day_deduction'     =>$one_day_deduction,
		        'modified_by'           =>$modified_by
		    );
		}
		$this->db->where('company_id',$companys_id);
		$this->db->update(COMPANY_SETTINGS_TBL, $data); // print $this->db->last_query(); exit;
	}
	function GetRecordGrid(){
	    $menu_slug= $this->uri->segment(1);
	    $this->load->model('Site_model');
	    $hasEditPM = $this->Site_model->hasOptionPermission($menu_slug,"Edit");
	    $hasDelPM  = $this->Site_model->hasOptionPermission($menu_slug,"Delete");
	    $from      = $this->input->post('from');
	    $to	       = $this->input->post('to');
	    if($from==""){ $from=0;} if($to==""){ $to=20;} 
	    
	    $this->db->select('*');
	    $this->db->from(COMPANY_SETTINGS_TBL);
	    $this->db->order_by('company_name','ASC');
	    $this->db->limit($to,$from);
    	    $query = $this->db->get();
	    //echo $this->db->last_query();
	    $totalrecord = $this->GetTotalRecord();
	    $perPage=20; $Pagination="";
	    if($totalrecord >0){
	        $Pagination = $this->getPagination($totalrecord,$perPage);
	    }
	    echo '<table width="100%"  border="0" class="table table-responsive table-bordered table-hover custab">
		  <thead>
		  <tr class="active">
			<th width="2%">'.$this->lang->line("sl").'</th>
		  	<th width="6%">'.$this->lang->line("small_logo").'</th>
		  	<th width="6%">'.$this->lang->line("large_logo").'</th>
			<th width="25%">'.$this->lang->line("company_name").'</th>
			<th width="25%">'.$this->lang->line("contact").'</th>
			<th width="9%">'.$this->lang->line("backend_title").'</th>
			<th width="6%">'.$this->lang->line("currency").'</th>
			<th width="6%">'.$this->lang->line("language").'</th>
			<th width="18%" class="text-center">'.$this->lang->line("options").'</th>
		  </tr>
		  </thead>';
		  $i=1;
		  foreach($query->result() as $row){
		  //if($i%2==0){ $tblrow="success";}else{$tblrow="warning";}
		  echo "<tr class='default'>
			<td>".$i."</td>
		  	<td><img src='".base_url().ASSETS.'/img/company/'.$row->sm_logo."' height='20'/></td>
			<td><img src='".base_url().ASSETS.'/img/company/'.$row->md_logo."' height='20'/></td>
			<td>".$row->company_name."</td>
			<td>".$row->address."<br>".$row->mobile.", ".$row->phone."<br>".$row->email."</td>
			<td>".$row->backend_title."</td>
			<td>".$row->currency_code."<br>".$row->currency_sign."</td>
			<td>".$row->default_language."</td>			
			<td class='text-center align-middle'>
			";
			if($hasEditPM){			
			echo "<span data-toggle='tooltip' data-placement='top' data-original-title='Edit'> <a class='btn btn-info btn-xs' onclick=myFunction('".$row->company_id."') id='".$row->company_id."' href='#edit'><i class='fas fa-edit'></i></a></span>&nbsp;";
			}
			if($hasDelPM){	
			echo "&nbsp;<span data-toggle='tooltip' data-placement='top' data-original-title='Delete'><a class='btn btn-danger btn-xs'  data-toggle='modal' onclick=deleteRecord('".$row->company_id."') id='".$row->company_id."' href='#deleteModal'><i class='fas fa-trash'></i></a></span>";
			}
            		echo "</td>
		  </tr>";
		  $i++;
		  }
		 echo '</table>';
		 echo "<div class='float-right'>$Pagination</div>";
	}
	//============== Company Retrive by Ajax================
	function GetTotalRecord(){
		$this->db->select('*');
		$this->db->from(COMPANY_SETTINGS_TBL);
		$this->db->order_by('company_name','ASC');
		$query = $this->db->get();
		return $query->num_rows();
	}
        function DelRecord(){
		$id =$this->input->post('id');
		$this->db->where('company_id',$id);
		$this->db->delete(COMPANY_SETTINGS_TBL);
        }
	/*function DelRecord(){
        $id =$this->uri->segment(3);
        $this->db->where('company_id',$id);
        $this->db->delete(COMPANY_SETTINGS_TBL);
        $this->session->set_flashdata('msg', 'Records successfully Deleted !');
        redirect('company_settings', 'location');
	}*/
	//------------------
	function FillRecord(){
        $companys_id =$this->input->post('id');
        $this->db->select('*');
        $this->db->from(COMPANY_SETTINGS_TBL);
        $this->db->where('company_id', $companys_id);
        $query = $this->db->get();
        return $query->row();
	}
    //======Start Common Function=======
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

	
	function formatDate($dt){
		if(trim($dt)){
			$day   	= substr($dt,0,2);
			$month 	= substr($dt,3,2);
			$year  	= substr($dt,6,4);
			$hour	= substr($dt,11,2);
			$minute = substr($dt,14,2);
			$second = substr($dt,17,2);
			$ampm	= substr($dt,20,2);
			//echo $ampm;
			if($hour=='' AND $minute=='' AND $second==''){
				return $year."/".$month."/".$day;	
			}else{
				if(strtoupper($ampm) == 'PM'){
					$hour = intval($hour)+12;
					return $year."/".$month."/".$day.' '.$hour.':'.$minute.':'.$second;
				}else{
					return $year."/".$month."/".$day.' '.$hour.':'.$minute.':'.$second;
				}	
			}
		}
  	}
	function formatDateTimeDMY($dt){
		if(trim($dt)){						
			$year 	= substr($dt,0,4);
			$month 	= substr($dt,5,2);
			$day 	= substr($dt,8,2);			
			$hour  	= substr($dt,11,2);
			$minute	= substr($dt,14,2);
			$second	= substr($dt,17,2);
			$ampm  	= substr($dt,20,2);
			if($hour=='' AND $minute=='' AND $second==''){
				return $year."/".$month."/".$day;	
			}else{
				if(strtoupper($ampm) == 'PM'){
					$hour = intval($hour)+12;
					return $day."/".$month."/".$year.' '.$hour.':'.$minute.':'.$second;
				}else{					
					return $day."/".$month."/".$year.' '.$hour.':'.$minute.':'.$second;
				}	
			}
		}
  	}	
  	function formatDateDMY($val){
		if($val){
			$yy = substr($val,0,4);
			$mm = substr($val,5,2);
			$dd = substr($val,8,2);
			return $dd.'/'.$mm.'/'.$yy;
		}
  	}
	function dateInputFormatDMY($val){
		if($val){
			$yy = substr($val,0,4);
			$mm = substr($val,5,2);
			$dd = substr($val,8,2);
			return $dd.'-'.$mm.'-'.$yy;
		}
	}
	   
   //End Class
}
