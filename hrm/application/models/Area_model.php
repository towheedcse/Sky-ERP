<?php 
class Area_model extends CI_Model {
		
	function __construct()
	{
		parent::__construct();
	}	
	
	function InsertRecord(){
		$company_id	=$this->input->post('company_id');
        	$division_id	=$this->input->post('division_id');
        	$area_name      =$this->input->post('area_name');
		$area_status	=$this->input->post('area_status');
		$area_id	=$this->input->post('area_id');

		if($area_id==''){
			$data = array(
			'company_id'    =>$company_id,
			'division_id'   =>$division_id,
			'area_name'     =>$area_name,
			'area_status'   =>$area_status
			);
			$this->db->insert(AREA_TBL, $data);
		}else{
			$this->EditRecord($area_id);
		}
		//print  $this->db->last_query();
   	}
	function EditRecord($area_id){
		$company_id     =$this->input->post('company_id');
		$division_id	=$this->input->post('division_id');
		$area_name      =$this->input->post('area_name');
		$area_status	=$this->input->post('area_status');

		$data = array(
		    'company_id'        =>$company_id,
		    'division_id'   	=>$division_id,
		    'area_name'         =>$area_name,
		    'area_status'       =>$area_status
		);
		$this->db->where('id',$area_id);
		$this->db->update(AREA_TBL, $data);
        }
	
	function DelRecord(){
		$id =$this->input->post('id');
		$this->db->where('id',$id);
		$this->db->delete(AREA_TBL);
	}
	
	function FillRecord(){
        $area_id	=$this->input->post('id');
		$this->db->select('*');
		$this->db->from(AREA_TBL);
		$this->db->where('id', $area_id);
		$query = $this->db->get();
		return $query->row();
	}
   	//============== Category Retrive by Ajax================
   	function GetRecordGrid(){
		$menu_slug= $this->uri->segment(1);
		$this->load->model('Site_model');
		$hasEditPM = $this->Site_model->hasOptionPermission($menu_slug,"Edit");
		$hasDelPM  = $this->Site_model->hasOptionPermission($menu_slug,"Delete");
	   	$from 	  =$this->input->post('from');
		$to	  =$this->input->post('to');
		if($from==""){ $from=0;} if($to==""){ $to=50;}
		$this->db->select('a.*,c.company_name,d.division_name');
		$this->db->from(AREA_TBL." AS a");
		$this->db->join(COMPANY_SETTINGS_TBL.' AS c', 'c.company_id=a.company_id','LEFT');
		$this->db->join(DIVISION_TBL.' AS d', 'd.id=a.division_id','LEFT');
		$this->db->group_by('a.id');
		$this->db->order_by('area_name','ASC');
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
        		<th width="31%">'.$this->lang->line("company_name").'</th>
		  	<th width="20%">'.$this->lang->line("division").'</th>
		  	<th width="20%">'.$this->lang->line("area").'</th>
			<th width="11%">'.$this->lang->line("status").'</th>
			<th width="16%" class="text-center">'.$this->lang->line("options").'</th>
		  </tr>
		</thead>';
		  $i=1;
		  foreach($query->result() as $row){
		    if($row->area_status==1){ 
			$status=$this->lang->line("active");
		    }elseif($row->area_status==0){ $status=$this->lang->line("inactive");}
		    echo "<tr class='default'>
		  	<td>".$i."</td>
			<td>".$row->company_name."</td>
		  	<td>".$row->division_name."</td>
		  	<td>".$row->area_name."</td>
			<td>".$status."</td>
			<td class='text-center align-middle'>";
			if($hasEditPM){
			echo "<span data-toggle='tooltip' data-placement='top' data-original-title='Edit'><a class='btn btn-info btn-xs' onclick=editRecord('".$row->id."') id='".$row->id."' href='#'><i class='fas fa-edit'></i></a></span>&nbsp;";
			}
			if($hasDelPM){
			echo "<span data-toggle='tooltip' data-placement='top' data-original-title='Delete'><a class='btn btn-danger btn-xs' data-toggle='modal' onclick=deleteRecord('".$row->id."') id='".$row->id."' data-target='#deleteModal'><i class='fa fa-trash'></i></a></span>";
			}
			echo "</td>
		  </tr>";
		  $i++;
		  }
		  echo '</table>';
	    echo "<div class='float-right'>$Pagination</div>";
	}

	function GetTotalRecord(){
		$this->db->select('*');
		$this->db->from(AREA_TBL);
		$this->db->order_by('area_name','ASC');
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
}
