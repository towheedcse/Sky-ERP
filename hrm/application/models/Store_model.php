<?php 
class Store_model extends CI_Model {
		
	function __construct()
	{
		parent::__construct();
	}	
	
	function InsertRecord(){
		
		$institute_id	=$this->input->post('institute-id');
		$branch_id		=$this->input->post('branch-id');
		$store_name		=$this->input->post('store-name');
		$status			=$this->input->post('status');
		$store_id		=$this->input->post('store-id');
		$created_by		=$this->session->userdata('created_by');
		if($store_id==''){
			$data = array(
			'institute_id'   =>$institute_id,
			'branch_id' 	 =>$branch_id,
			'store_name' 	 =>$store_name,
			'status'     	 =>$status,
			'created_by'	 =>$created_by
			);
			$this->db->insert(STORE_TBL, $data); //print  $this->db->last_query();
		}else{
			$this->EditRecord($store_id);
		}
		
		print  $this->db->last_query();
   	}
	
	function EditRecord($store_id){
		$institute_id	=$this->input->post('institute-id');
		$branch_id		=$this->input->post('branch-id');
		$store_name		=$this->input->post('store-name');
		$status			=$this->input->post('status');
		$modified_by	= $this->session->userdata('created_by');
    	$modified_time 	= date("Y-m-d H:i:s");

		$data = array(
		    'institute_id'   =>$institute_id,
			'branch_id' 	 =>$branch_id,
			'store_name' 	 =>$store_name,
			'status'     	 =>$status,
		    'modified_by'    =>$modified_by,
		    'modified_time'  =>$modified_time
        	);
		$this->db->where('store_id',$store_id);
		$this->db->update(STORE_TBL, $data);
    }
   	//============== Category Retrive by Ajax================
   	function GetRecordGrid(){
		$menu_slug= $this->uri->segment(1);
		$this->load->model('Site_model');
		$hasEditPM = $this->Site_model->hasOptionPermission($menu_slug,"Edit");
		$hasDelPM  = $this->Site_model->hasOptionPermission($menu_slug,"Delete"); 
		$institute_id		=$this->input->post('src-institute');
		$branch_id			=$this->input->post('src-branch');
	   	$from	=$this->input->post('from');
		$to	=$this->input->post('to');
		if($from==""){ $from=0;} if($to==""){ $to=50;}
		$this->db->select('s.*,c.company_name,b.branch_code,b.branch_name,b.branch_address',FALSE);
		$this->db->from(STORE_TBL." AS s");
	  	$this->db->join(COMPANY_SETTINGS_TBL.' AS c', 'c.company_id=s.institute_id','LEFT');
	  	$this->db->join(BRANCH_TBL.' AS b', 'b.branch_id=s.branch_id','LEFT');
		if($this->session->userdata('user_role') >1){
			$this->db->where("s.institute_id", $this->session->userdata('company_id'));  
		}else{
			if($institute_id >0){
			  $this->db->where("s.institute_id", $institute_id);  	
			}
		}
		if($this->session->userdata('user_role') >2){
			$this->db->where("b.branch_id", $this->session->userdata('branch_id'));  
		}else{
			if($branch_id >0){
			  $this->db->where("b.branch_id", $branch_id);  	
			}			
		}
		$this->db->group_by('s.store_id');
		$this->db->order_by('s.store_name','ASC');
		$this->db->limit($to,$from);
		$query = $this->db->get();
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
		  	<th width="16%">'.$this->lang->line("company_name").'</th>
		  	<th width="12%">'.$this->lang->line("branch_name").'</th>
		  	<th width="12%">'.$this->lang->line("store_name").'</th>
			<th width="8%">'.$this->lang->line("status").'</th>
			<th width="15%" class="text-center">'.$this->lang->line("options").'</th>
		  </tr>
		</thead>';
		  $i=1;
		  foreach($query->result() as $row){
		  if($row->status==1){ $status=$this->lang->line("active");}elseif($row->status==0){ $status=$this->lang->line("inactive");}
		  if($row->status==0){ $disabled="disabled";}else{$disabled="";}
		  echo "<tr class='default'>
		  	<td>".$i."</td>
			<td>".$row->company_name."</td>
			<td>".$row->branch_name."</td>
			<td>".$row->store_name."</td>
			<td>".$status."</td>
			<td class='text-center align-middle'>";
	    	if($hasEditPM){
			echo "<span data-toggle='tooltip' data-placement='top' data-original-title='Edit'><a class='btn btn-info btn-xs' onclick=editRecord('".$row->store_id."') id='".$row->store_id."' href='#'><i class='fas fa-edit'></i></a></span>&nbsp;";
			}
			if($hasDelPM){				
			echo "<span data-toggle='tooltip' data-placement='top' data-original-title='Delete'><a class='btn btn-danger btn-xs ".$disabled."' data-toggle='modal' onclick=deleteRecord('".$row->store_id."') id='".$row->store_id."' data-target='#deleteModal'><i class='fa fa-trash'></i></a></span>";
			}else{
				echo "<span class='fa fa-times-circle'></span>";
			}
			echo "</td>
		  </tr>";
		  $i++;
		  }
		echo '</table>';
	    	echo "<div class='float-right'>$Pagination</div>";
	}

	function GetTotalRecord(){
		$institute_id		=$this->input->post('src-institute');
		$branch_id			=$this->input->post('src-branch');
		$this->db->select('s.*,c.company_name,b.branch_code,b.branch_name,b.branch_address',FALSE);
		$this->db->from(STORE_TBL." AS s");
	  	$this->db->join(COMPANY_SETTINGS_TBL.' AS c', 'c.company_id=s.institute_id','LEFT');
	  	$this->db->join(BRANCH_TBL.' AS b', 'b.branch_id=s.branch_id','LEFT');
		if($this->session->userdata('user_role') >1){
			$this->db->where("s.institute_id", $this->session->userdata('company_id'));  
		}else{
			if($institute_id >0){
			  $this->db->where("s.institute_id", $institute_id);  	
			}
		}
		if($this->session->userdata('user_role') >2){
			$this->db->where("b.branch_id", $this->session->userdata('branch_id'));  
		}else{
			if($branch_id >0){
			  $this->db->where("b.branch_id", $branch_id);  	
			}			
		}
		$this->db->group_by('s.store_id');
		$this->db->order_by('s.store_name','ASC');
		$query = $this->db->get();
		if($query->num_rows() >0){
			return $query->num_rows();
		}else{
			return 0;
		}//echo $this->db->last_query();
	}
	
	function DelRecord(){
		$id =$this->input->post('id');
		$this->db->where('store_id',$id);
		$this->db->delete(STORE_TBL);
	}
	
	function FillRecord(){
		$id	=$this->input->post('id');
		$this->db->select('*');
		$this->db->from(STORE_TBL);
		$this->db->where('store_id', $id);
		$query = $this->db->get();
		return $query->row();
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
    }function formatDate($dt)
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
