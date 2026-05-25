<?php 
class SubsidiaryLedger_model extends CI_Model {
		
	function __construct()
	{
			parent::__construct();
	}	
		
	function InsertRecord(){
		
		$company_id	= $this->input->post('company-name');			
		$subsidiary_id	= $this->input->post('subsidiary-id');
		$group_head	= $this->input->post('group-head');
		
		$subsidiary_name= str_replace("U 0026", '&', $this->input->post('subsidiary-name'));
		$subsidiary_name= $this->db->escape_str($subsidiary_name);

		$created_by	= $this->session->userdata('created_by');
		
		$SQL= "INSERT INTO ".SUB_HEAD_L1_TBL."(company_id,parents_id,sub_head_name,created_by) VALUES('".$company_id."','".$group_head."','".$subsidiary_name."','".$created_by."')";
		if($subsidiary_id ==""){
		$this->db->query($SQL);
		//print  $this->db->last_query();
		}else{
			$this->EditRecord($subsidiary_id);
		}
		//print  $this->db->last_query();
	   }
       	   function EditRecord($subsidiary_id){
		$company_id	= $this->input->post('company-name');
		$group_head	= $this->input->post('group-head');		
		$subsidiary_name= str_replace("U 0026", '&', $this->input->post('subsidiary-name'));
		$subsidiary_name= $this->db->escape_str($subsidiary_name);
		$modified_by	= $this->session->userdata('created_by');			
		$modified_time 	= date("Y-m-d H:i:s");

		$SQL= "UPDATE ".SUB_HEAD_L1_TBL." SET company_id ='".$company_id."',parents_id ='".$group_head."',sub_head_name ='".$subsidiary_name."',modified_by = '".$modified_by."', modified_time ='".$modified_time."' WHERE sub_id = ".$subsidiary_id;
		$this->db->query($SQL);
		//print  $this->db->last_query();
       	  }
	   //============== Category Retrive by Ajax================
	    function GetRecordGrid(){
		$menu_slug= $this->uri->segment(1);
		$this->load->model('Site_model');
		$hasEditPM = $this->Site_model->hasOptionPermission($menu_slug,"Edit");
		$hasDelPM  = $this->Site_model->hasOptionPermission($menu_slug,"Delete");

		$group_head	= $this->input->post('src-top-head');
		$from		= $this->input->post('from');
		$to		= $this->input->post('to');
		if($from==""){ $from=0;} if($to==""){ $to=50;}

		$this->db->select('s.*, g.group_name');		
		$this->db->from(SUB_HEAD_L1_TBL.' AS s');				
		$this->db->join(GROUP_HEAD_TBL.' AS g', 'g.group_id=s.parents_id','LEFT');
		if($group_head >0){
		   $this->db->where("s.parents_id", $group_head);
		}		
		$this->db->where("g.status", 1); // 1=Active
		$this->db->group_by('s.sub_id');				
		$this->db->order_by('s.sub_id','ASC');
		$this->db->limit($to,$from);
		$query = $this->db->get(); //echo $this->db->last_query();

		$totalrecord = $this->GetTotalRecords();

		$perPage=50; $Pagination="";
		if($totalrecord >0){
		   $Pagination = $this->getPagination($totalrecord,$perPage);
		} //print  $this->db->last_query();
		echo 
		'<table width="100%" id="data-table" class="table table-responsive table-hover table-bordered">
		<thead class="bg-primary">
		  <tr>
		  	<th width="2%" class="text-center">'.$this->lang->line("sl").'</th>
			<th width="30%">'.$this->lang->line("gl")." ".$this->lang->line("name").'</th>
			<th width="52%">'.$this->lang->line("sl_level")."-".$this->lang->line("1")." ".$this->lang->line("name").'</th>
			<th width="15%" class="text-center">'.$this->lang->line("options").'</th>
		  </tr>
		</thead>';
		$sl=0;
	    	foreach($query->result() as $row){
		$sl++;
		echo "<tr>
			<td align='center'>".$sl."</td>
			<td>".$row->group_name."</td>
			<td><strong>".$row->sub_head_name."</strong></td>
			
                    	<td class='text-center align-middle'>";
		    	if($hasEditPM){			
			echo "<span data-toggle='tooltip' data-placement='top' data-original-title='Edit'><a class='btn btn-info btn-xs' onclick=editRecord('".$row->sub_id."') id='".$row->sub_id."' href='#'><i class='fas fa-edit'></i></a></span>&nbsp;";
			}
			if($hasDelPM){
			echo "<span data-toggle='tooltip' data-original-title='Delete'><a class='btn btn-danger btn-sm' data-toggle='modal' onclick=deleteRecord('".$row->sub_id."') id='".$row->sub_id."' href='#deleteModal'><i class='fas fa-trash'></i></a></span>";
			}
			
		echo "
			</td>
		</tr>";
		}
		echo '</table>';
	   echo "<div class='text-right float-right'>$Pagination</div>";
	}

	function GetTotalRecords(){
		$group_head	= $this->input->post('src-top-head');
		$this->db->select('s.*, g.group_name');		
		$this->db->from(SUB_HEAD_L1_TBL.' AS s');				
		$this->db->join(GROUP_HEAD_TBL.' AS g', 'g.group_id=s.parents_id','LEFT');
		if($group_head >0){
		   $this->db->where("s.parents_id", $group_head);
		}		
		$this->db->where("g.status", 1); // 1=Active
		$this->db->group_by('s.sub_id');				
		$this->db->order_by('s.sub_id','ASC');
		$query = $this->db->get();
		if($query->num_rows() >0){
			return $query->num_rows();
		}else{
			return 0;
		}//echo $this->db->last_query();
    }
    function DeleteRecord(){
        $id =$this->input->post('id');
        $this->db->where('sub_id',$id);
        $this->db->delete(SUB_HEAD_L1_TBL);
    }

    function FillRecord(){
        $customer_id = $this->input->post('id');
        $this->db->select('*');
        $this->db->from(SUB_HEAD_L1_TBL);
        $this->db->where('sub_id', $customer_id);
        $query = $this->db->get();
        return $query->row();
    }
    	    
    /*======Start Common Function for pagination=======*/
    function getHeadID($category,$prefix){
		$SQL = "SELECT max(head_id) AS maxhead FROM ".ACC_HEAD_TBL." WHERE head_type='".$category."'
		 AND `head_id` LIKE 'H%' ORDER BY `head_id` DESC";
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
	function dateDisplayFormat($input_date)
	{
		if ($input_date) {
			$query = $this->db->query("SELECT DATE_FORMAT( '$input_date', '%d %b %Y' ) AS ctc_date");				
			return $query->row()->ctc_date;
		}
	}
        /*======End Common Function for pagination=======*/
   //End Class
}
