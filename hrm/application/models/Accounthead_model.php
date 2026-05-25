<?php 
class AccountHead_model extends CI_Model {
		
	function __construct()
	{
			parent::__construct();
	}	
		
	function InsertRecord(){
			
		$account_id	= $this->input->post('account-id');
		$company_id	= $this->input->post('company-name');
		$group_id	= $this->input->post('group-id');
		$level_1	= $this->input->post('sl-level-1');	
		$level_2	= $this->input->post('sl-level-2');	
		$level_3	= $this->input->post('sl-level-3');
		if(empty($level_3)){$level_3=0;}
		$account_type	= $this->input->post('account-type');
		$count_unit		= $this->input->post('count-unit');
		$account_name 	= str_replace("U 0026", '&', $this->input->post('account-name'));
		$account_name	= $this->db->escape_str($account_name);

		$account_details= str_replace("U 0026", '&', $this->input->post('account-details'));
		$account_details= $this->db->escape_str($account_details);
		
		if($account_type=="" || $account_type=="0"){$account_type=20;}
		if($account_type==1){$prefix="M";}elseif($account_type==2){$prefix="A";}elseif($account_type==4){$prefix="C";}elseif($account_type==5){$prefix="B";}elseif($account_type==6){$prefix="T";}elseif($account_type==8){$prefix="R";}elseif($account_type==10){$prefix="E";}elseif($account_type==11){$prefix="S";}elseif($account_type==12 || $account_type==13){$prefix="I";}elseif($account_type==26){$prefix="P";}elseif($account_type==27){$prefix="L";}else{$prefix="H";}
		if($account_type==12 || $account_type==13){
			$heads_type = "12,13";
		}elseif($account_type==1 || $account_type==10){
			$heads_type = "1,10";
		}else{
			$heads_type = $account_type;
		}
		$head_id	= $this->getHeadID($heads_type,$prefix);
    	$created_by	= $this->session->userdata('created_by');
		
		$SQL= "INSERT INTO ".ACC_HEAD_TBL."(company_id,head_id,group_id,subsidiary_level1,subsidiary_level2,subsidiary_level3,head_type,account_name,account_details,count_unit,created_by) VALUES('".$company_id."','".$head_id."','".$group_id."','".$level_1."','".$level_2."','".$level_3."','".$account_type."','".$account_name."','".$account_details."','".$count_unit."','".$created_by."')";
		if($account_id ==""){
		$this->db->query($SQL);
		//print  $this->db->last_query();
		}else{
			$this->EditRecord($account_id);
		}
		//print  $this->db->last_query();
	   }
       function EditRecord($account_id){
		$company_id	= $this->input->post('company-name');
		$group_id	= $this->input->post('group-id');
		$level_1	= $this->input->post('sl-level-1');	
		$level_2	= $this->input->post('sl-level-2');	
		$level_3	= $this->input->post('sl-level-3');
		if(empty($level_3)){$level_3=0;}
		$account_type	= $this->input->post('account-type');
		$count_unit		= $this->input->post('count-unit');
		$account_name 	= str_replace("U 0026", '&', $this->input->post('account-name'));
		$account_name	= $this->db->escape_str($account_name);

		$account_details= str_replace("U 0026", '&', $this->input->post('account-details'));
		$account_details= $this->db->escape_str($account_details);
		$update_by	= $this->session->userdata('created_by');			
		$updatedTime 	= date("Y-m-d H:i:s");

		$SQL= "UPDATE ".ACC_HEAD_TBL." SET  company_id ='".$company_id."', group_id ='".$group_id."', subsidiary_level1 ='".$level_1."', subsidiary_level2 ='".$level_2."',subsidiary_level3 ='".$level_3."', head_type='".$account_type."', account_name ='".$account_name."',account_details='".$account_details."',count_unit='".$count_unit."', update_by = '". $update_by."', update_time ='". $updatedTime."' WHERE account_id = ".$account_id;
		
		$this->db->query($SQL);
		//print  $this->db->last_query();
       	  }
	  //============== Category Retrive by Ajax================
	  function GetRecordGrid(){
		$menu_slug= $this->uri->segment(1);
		$this->load->model('Site_model');
		$hasEditPM = $this->Site_model->hasOptionPermission($menu_slug,"Edit");
		$hasDelPM  = $this->Site_model->hasOptionPermission($menu_slug,"Delete");

		$company_id	= $this->input->post('src-company-id');
		$group_head	= $this->input->post('src-group-id');
		$sl_level1	= $this->input->post('src-level-1');
		$sl_level2	= $this->input->post('src-level-2');
		$from		= $this->input->post('from');
		$to		= $this->input->post('to');
		if($from==""){ $from=0;} if($to==""){ $to=50;}

		$this->db->select('a.*, g.group_name, s.sub_head_name, c.subsidiary_name2');
		
		$this->db->from(ACC_HEAD_TBL.' AS a');
		$this->db->where("a.head_type >0"); // 20=Others				
		$this->db->join(GROUP_HEAD_TBL.' AS g', 'g.group_id=a.group_id','LEFT');			
		$this->db->join(SUB_HEAD_L1_TBL.' AS s', 's.sub_id=a.subsidiary_level1','LEFT');
		$this->db->join(SUB_HEAD_L2_TBL.' AS c', 'c.sub2_id=a.subsidiary_level2','LEFT');
		if($company_id !=""){
		   $this->db->like("a.company_id", $company_id);
		}
		if($group_head >0){
		   $this->db->where("a.group_id", $group_head);
		}
		if($sl_level1 >0){
		   $this->db->where("a.subsidiary_level1", $sl_level1);
		}
		if($sl_level2 >0){
		   $this->db->where("a.subsidiary_level2", $sl_level2);
		}						
		$this->db->order_by('g.group_id,a.subsidiary_level1,a.subsidiary_level2,a.account_name','ASC');
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
				<th width="12%">'.$this->lang->line("gl")." ".$this->lang->line("name").'</th>
				<th width="19%">'.$this->lang->line("sl_level")."-".$this->lang->line("1")." ".$this->lang->line("name").'</th>
				<th width="19%">'.$this->lang->line("sl_level")."-".$this->lang->line("2")." ".$this->lang->line("name").'</th>
				<th width="18%">'.$this->lang->line("account")." ".$this->lang->line("name").'</th>
				<th width="18%">'.$this->lang->line("account")." ".$this->lang->line("details").'</th>
				<th width="13%" class="text-center">'.$this->lang->line("options").'</th>
			  </tr>
			</thead>';
			$sl=0;
		    foreach($query->result() as $row){
			$sl++;
			if($this->session->userdata('user_role') < 103){
				$disabled="";
			}else{
				$disabled="disabled";
			}
			echo "<tr>
				<td align='center'>".$sl."</td>
				<td>".$row->group_name."</td>				
				<td>".$row->sub_head_name."</td>				
				<td>".$row->subsidiary_name2."</td>				
				<td>".$row->account_name."</td>
				<td>".$row->account_details."</td>
				<td align='center'>";				 
				if($hasEditPM){
				echo "<span data-toggle='tooltip' data-original-title='Edit'><a class='btn btn-info btn-sm' data-toggle='modal' onclick=editRecord('".$row->account_id."','".$from."','".$to."') id='".$row->account_id."' href='#'><i class='fa fa-edit'></i></a></span> &nbsp;";
				}
				if($hasDelPM){
				echo "<span data-toggle='tooltip' data-original-title='Delete'><a class='btn btn-danger btn-sm' data-toggle='modal' onclick=deleteRecord('".$row->account_id."') id='".$row->account_id."' href='#deleteModal'><i class='fa fa-trash'></i></a></span>";
				}
			echo "</td>
			</tr>";
			}
			echo '</table>';
		   echo "<div class='text-right'>$Pagination</div>";
	}

	function GetTotalRecords(){
		$company_id	= $this->input->post('src-company-id');
		$group_head	= $this->input->post('src-group-id');
		$sl_level1	= $this->input->post('src-level-1');
		$sl_level2	= $this->input->post('src-level-2');
			

		$this->db->select('a.*, g.group_name, s.sub_head_name, c.subsidiary_name2');
		
		$this->db->from(ACC_HEAD_TBL.' AS a');
		$this->db->where("a.head_type >2"); // 20=Others				
		$this->db->join(GROUP_HEAD_TBL.' AS g', 'g.group_id=a.group_id','LEFT');			
		$this->db->join(SUB_HEAD_L1_TBL.' AS s', 's.sub_id=a.subsidiary_level1','LEFT');
		$this->db->join(SUB_HEAD_L2_TBL.' AS c', 'c.sub2_id=a.subsidiary_level2','LEFT');
		if($company_id !=""){
		   $this->db->like("a.company_id", $company_id);
		}
		if($group_head >0){
		   $this->db->where("a.group_id", $group_head);
		}
		if($sl_level1 >0){
		   $this->db->where("a.subsidiary_level1", $sl_level1);
		}
		if($sl_level2 >0){
		   $this->db->where("a.subsidiary_level2", $sl_level2);
		}						
		$this->db->order_by('g.group_id,a.subsidiary_level1,a.subsidiary_level2,a.account_name','ASC');
		$query = $this->db->get();
		if($query->num_rows() >0){
			return $query->num_rows();
		}else{
			return 0;
		}//echo $this->db->last_query();
    }
    function DeleteRecord(){
        $id =$this->input->post('id');
        $this->db->where('account_id',$id);
        $this->db->delete(ACC_HEAD_TBL);
    }

    function FillRecord(){
        $customer_id = $this->input->post('id');
        $this->db->select('*');
        $this->db->from(ACC_HEAD_TBL);
        $this->db->where('account_id', $customer_id);
        $query = $this->db->get();
        return $query->row();
    }
    
    /*======Start Common Function for pagination=======*/
    function getHeadID($category,$prefix){
		$SQL = "SELECT max(head_id) AS maxhead FROM ".ACC_HEAD_TBL." WHERE head_type='".$category."'
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
