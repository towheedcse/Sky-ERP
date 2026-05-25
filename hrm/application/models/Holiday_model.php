<?php 
class Holiday_model extends CI_Model {
		
	function __construct()
	{
		parent::__construct();
	}	
	
	function InsertRecord(){
		$institute_id	=$this->input->post('institute_id');
		$branch_id		=$this->input->post('branch_id');
		$session_id		=$this->input->post('session_id');
		$version_id		=$this->input->post('version_id');
		$holiday_name   =$this->input->post('holiday_name');
		$from_date   	=$this->formatDate($this->input->post('from_date'));
		$to_date   		=$this->formatDate($this->input->post('to_date'));
		$is_holiday		=$this->input->post('is_holiday');
		$status			=$this->input->post('status');
		$holiday_id		=$this->input->post('holiday_id');
		$created_by		=$this->session->userdata('created_by');
		if(empty($status)){$status=1;}
		if($holiday_id==''){						
			$data = array(
			'institute_id'    		=>$institute_id,
			'branch_id'    			=>$branch_id,
			'session_id'    		=>$session_id,	
			'version_id'    		=>$version_id,
			'holiday_name'    		=>$holiday_name,
			'from_date'    			=>$from_date,	
			'to_date'    			=>$to_date,
			'is_holiday'			=>$is_holiday,
			'created_by'     		=>$created_by,
			'status'    			=>$status
			);
			//=== Remove empty field ====
			$data = array_filter($data);
			//=== Remove unexpected field by value (e.g value) ====
			if (($key = array_search('value', $data)) !== false) { unset($data[$key]);}						
			$this->db->insert(HOLIDAY_TBL, $data);
			$holiday_id  = $this->db->insert_id();
			$holiday_image = $this->UploadPhoto($holiday_id);
			if($holiday_image!=""){
			 $PSQL= "UPDATE ".HOLIDAY_TBL." SET notice_image='".$holiday_image."' 
			 WHERE holiday_id='".$holiday_id."' AND institute_id=$institute_id AND branch_id=$branch_id";
			 $this->db->query($PSQL);
			}
			if($is_holiday==1){ 
			 $SQLM="UPDATE ".MONTH_DAYS_TBL." SET is_holiday=1 WHERE `date_field` BETWEEN '$from_date' AND '$to_date'"; 
			 $this->db->query($SQLM);
			}
		}else{
			$this->EditRecord($holiday_id);
		}
		//print  $this->db->last_query();
   	}
	function EditRecord($holiday_id){
		$institute_id	=$this->input->post('institute_id');
		$branch_id	=$this->input->post('branch_id');
		$session_id	=$this->input->post('session_id');
		$version_id	=$this->input->post('version_id');
		$holiday_name   =$this->input->post('holiday_name');
		$from_date   	=$this->formatDate($this->input->post('from_date'));
		$to_date   	=$this->formatDate($this->input->post('to_date'));
		$is_holiday	=$this->input->post('is_holiday');
		$status		=$this->input->post('status');
		$modified_by	=$this->session->userdata('created_by');
		$modified_time  =date("Y-m-d H:i:s");
		$data = array(
			'institute_id'    	=>$institute_id,
			'branch_id'    		=>$branch_id,
			'session_id'    	=>$session_id,	
			'version_id'    	=>$version_id,
			'holiday_name'    	=>$holiday_name,
			'from_date'    		=>$from_date,	
			'to_date'    		=>$to_date,
			'is_holiday'		=>$is_holiday,
			'modified_by'     	=>$modified_by,
			'modified_time'     	=>$modified_time,
			'status'    		=>$status
		);
		$this->db->where('holiday_id',$holiday_id);
		$this->db->update(HOLIDAY_TBL, $data);
		$holiday_image = $this->UploadPhoto($holiday_id);
		if($holiday_image!=""){
		 $PSQL= "UPDATE ".HOLIDAY_TBL." SET notice_image='".$holiday_image."' 
		 WHERE holiday_id='".$holiday_id."' AND institute_id=$institute_id AND branch_id=$branch_id";
		 $this->db->query($PSQL);
		}

		if($is_holiday==1){ 
			 $SQLM="UPDATE ".MONTH_DAYS_TBL." SET is_holiday=1 WHERE `date_field` BETWEEN '$from_date' AND '$to_date'"; 
			 $this->db->query($SQLM);
		}else{
			$SQLM="UPDATE ".MONTH_DAYS_TBL." SET is_holiday=0 WHERE `date_field` BETWEEN '$from_date' AND '$to_date'"; 
			$this->db->query($SQLM);
		}
    }
	   	
	function UploadPhoto($img_id){
		$file_name=''; $saveDir = ASSETS.'/img/notice/';
		$config['file_name']		= $img_id;
		$config['overwrite']		= TRUE;
		$config['upload_path'] 		= ASSETS.'/img/notice/';
		$config['allowed_types'] 	= 'gif|jpg|png|jpeg';
		$config['max_size'] 		= '144400';
		$config['max_width']  		= '144400';
		$config['max_height']  		= '144400';
		$config['maintain_ratio']  	= FALSE;
		$config['width']  		= '2057';
		$config['height']  		= '2500';
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
						'width' => 2057,
						'height' => 2500
					);			
					$this->load->library('image_lib', $config2);
					$this->image_lib->resize();					
					return trim("notice/".$file_name);					                    
				}else{
					echo $errors = $this->upload->display_errors();
					return false;
				}
			}
		}// end foreach		
	}
	function DelRecord(){
		$id = $this->input->post('id');		
		$this->db->select('*');
		$this->db->from(HOLIDAY_TBL);
		$this->db->where('holiday_id', $id);
		$query = $this->db->get();
		$SQLM="UPDATE ".MONTH_DAYS_TBL." SET is_holiday=0 WHERE `date_field` BETWEEN '$query->row()->from_date' AND '$query->row()->to_date'"; 
		$this->db->query($SQLM);
			
		$this->db->where('holiday_id',$id);
		$this->db->delete(HOLIDAY_TBL);
	}
	
	function FillRecord(){
        $holiday_id	=$this->input->post('id');
		$this->db->select('*');
		$this->db->from(HOLIDAY_TBL);
		$this->db->where('holiday_id', $holiday_id);
		$query = $this->db->get();
		$SQLM="UPDATE ".MONTH_DAYS_TBL." SET is_holiday=0 WHERE `date_field` BETWEEN '".$query->row()->from_date."' AND '".$query->row()->to_date."'"; 
		$this->db->query($SQLM);
		return $query->row();
	}
   	//============== Category Retrive by Ajax================
   	function GetRecordGrid(){
		$menu_slug= $this->uri->segment(1);
		$this->load->model('Site_model');
		$hasEditPM = $this->Site_model->hasOptionPermission($menu_slug,"Edit");
		$hasDelPM  = $this->Site_model->hasOptionPermission($menu_slug,"Delete");
		$hasPrintPM  = $this->Site_model->hasOptionPermission($menu_slug,"Print");
		$is_holiday= $this->input->post('is_holiday');
	   	$from 	   = $this->input->post('from');
		$to	  =$this->input->post('to');
		if($from==""){ $from=0;} if($to==""){ $to=50;}
		$this->db->select('h.*,DATE_FORMAT(h.from_date ,"%d-%m-%Y") as date_from,DATE_FORMAT(h.to_date ,"%d-%m-%Y") as date_to,DATEDIFF(h.to_date,h.from_date)+1 as total_days,c.company_name,b.branch_name,b.branch_code,se.session_name');
		$this->db->from(HOLIDAY_TBL." AS h");
		$this->db->join(COMPANY_SETTINGS_TBL.' AS c', 'c.company_id=h.institute_id','LEFT');
	  	$this->db->join(BRANCH_TBL.' AS b', 'b.branch_id=h.branch_id','LEFT');
	  	$this->db->join(SESSION_TBL.' AS se', 'se.sessions_id=h.session_id','LEFT');
		$this->db->where('h.is_holiday',$is_holiday); // 1=Holiday Notice, 0= General Notice
		$this->db->group_by('holiday_id','ASC');
		$this->db->order_by('from_date','ASC');
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
				<th width="20%">'.$this->lang->line("company").' '.$this->lang->line("details").'</th>
			  	<th width="12%">'.$this->lang->line("session").'</th>
				<th width="15%">'.$this->lang->line("holiday_name").'</th>
				<th width="11%">'.$this->lang->line("from_date").'</th>
				<th width="11%">'.$this->lang->line("to_date").'</th>
				<th width="11%">'.$this->lang->line("total_days").'</th>
				<th width="10%">'.$this->lang->line("holiday_image").'</th>
				<th width="8%" class="text-center">'.$this->lang->line("options").'</th>
			  </tr>
		</thead>';
		  $i=1;
		  foreach($query->result() as $row){
		    if($row->status==1){ 
			$status=$this->lang->line("active");
		    }elseif($row->status==0){ $status=$this->lang->line("inactive");}
		    echo "<tr class='default'>
		  	<td>".$i."</td>
			<td>".$row->company_name.",<br>".$row->branch_name."</td>
		  	<td>".$row->session_name."</td>
			<td>".$row->holiday_name."</td>
			<td>".$row->date_from."</td>
			<td>".$row->date_to."</td>
			<td>".$row->total_days."</td>
			<td><img src='".base_url().ASSETS.'/img/'.$row->notice_image."' height='65'/></td>
			<td class='text-center align-middle'>";
			if($hasEditPM){
			echo "<span data-toggle='tooltip' data-placement='top' data-original-title='Edit'><a class='btn btn-info btn-xs' onclick=editRecord('".$row->holiday_id."') id='".$row->holiday_id."' href='#'><i class='fas fa-edit'></i></a></span>&nbsp;";
			}
			if($hasDelPM){
			echo "<span data-toggle='tooltip' data-placement='top' data-original-title='Delete'><a class='btn btn-danger btn-xs' data-toggle='modal' onclick=deleteRecord('".$row->holiday_id."') id='".$row->holiday_id."' data-target='#deleteModal'><i class='fa fa-trash'></i></a></span>";
			}
			if($hasPrintPM){
			 if($is_holiday==1){
			  echo "<div class='clearfix'></div><br><span data-toggle='tooltip' data-original-title='View Form'><a class='btn btn-success btn-sm' href='".base_url()."holiday/ViewNotice/".$row->holiday_id."'><i class='fa fa-print'></i> ".$this->lang->line("holiday")."</a></span>";
			 }else{
			  echo "<div class='clearfix'></div><br><span data-toggle='tooltip' data-original-title='View Form'><a class='btn btn-success btn-sm' href='".base_url()."holiday/ViewNotice/".$row->holiday_id."'><i class='fa fa-print'></i> ".$this->lang->line("notice")."</a></span>";
			 }
			
			}
			echo "</td>
		  </tr>";
		  $i++;
		  }
		  echo '</table>';
	    echo "<div class='float-right'>$Pagination</div>";
	}

	function GetTotalRecord(){
		$is_holiday= $this->input->post('is_holiday');
		$this->db->select('*');
		$this->db->from(HOLIDAY_TBL);
		$this->db->where('is_holiday',$is_holiday); // 1=Holiday Notice, 0= General Notice
		$this->db->order_by('from_date','ASC');
		$query = $this->db->get();
		if($query->num_rows() >0){
			return $query->num_rows();
		}else{
			return 0;
		}//echo $this->db->last_query();
	}

	/*======Start Common Function for pagination=======*/
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
		if (trim($dt) !="") {
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
