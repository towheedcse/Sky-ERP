<?php 
class User_role_model extends CI_Model {
		
	function __construct()
	{
		parent::__construct();
	}	
	
	function InsertRecord(){
		$role_name	    =$this->input->post('role_name');
        	$role_description   =$this->input->post('role_description');
		$role_status	    =$this->input->post('role_status');
        	$role_id	    =$this->input->post('role_id');

		if($role_id==''){
			$data = array(
                'role_name'         =>$role_name,
                'role_description'  =>$role_description,
                'role_status'       =>$role_status
			);
		    $this->db->insert(USER_ROLE_TBL, $data);
		}else{
			$this->EditRecord($role_id);
		}
		//print  $this->db->last_query();
   	}
	function EditRecord($role_id){
		//$role_id	        =$this->input->post('role_id');
		$role_name	        =$this->input->post('role_name');
		$role_description   =$this->input->post('role_description');
		$role_status	    =$this->input->post('role_status');

		$data = array(
		    'role_name'         =>$role_name,
		    'role_description'  =>$role_description,
		    'role_status'       =>$role_status
		);
		$this->db->where('role_id',$role_id);
		$this->db->update(USER_ROLE_TBL, $data);
    	}
	
	function DelRecord(){
		$id =$this->input->post('id');
		$this->db->where('role_id',$id);
		$this->db->delete(USER_ROLE_TBL);
	}
	
	function FillRecord(){
        $role_id =$this->input->post('id');
		$this->db->select('*');
		$this->db->from(USER_ROLE_TBL);
		$this->db->where('role_id', $role_id);
		$query = $this->db->get();
		return $query->row();
	}
   	//============== Category Retrive by Ajax================
   	function GetRecordGrid(){
	   	$from	=$this->input->post('from');
		$to	=$this->input->post('to');
		if($from==""){ $from=0;} if($to==""){ $to=50;}
		$this->db->select('*');
		$this->db->from(USER_ROLE_TBL);
		$this->db->group_by('role_id','ASC');
		$this->db->order_by('role_name','ASC');
		$this->db->limit($to,$from);
		$query = $this->db->get();
		$totalrecord = $this->GetTotalRecord();
	    	$perPage=50; $Pagination="";
	    	if($totalrecord >0){
		   	$Pagination = $this->getPagination($totalrecord,$perPage);
	    	} //print  $this->db->last_query();
		echo
		"<table width='100%'  border='0' class='table table-responsive table-bordered table-hover custab'>
			<thead>
			  <tr class='active'>
			  	<th width='2%'>".$this->lang->line("sl")."</th>
				<th width='28%'>".$this->lang->line("role").$this->lang->line("r")." ".$this->lang->line("name")."</th>
                <th width='45%'>".$this->lang->line("role").$this->lang->line("r")." ".$this->lang->line("description")."</th>
				<th width='10%'>".$this->lang->line("status")."</th>
				<th width='15%' class='text-center'>".$this->lang->line("options")."</th>
			  </tr>
			</thead>";
			  $i=1;
			  foreach($query->result() as $row){
			  if($row->role_status==1){ $status="Active";}elseif($row->role_status==0){ $status="Inactive";}
			  echo "<tr class='default'>
			  	<td>".$i."</td>
				<td>".$row->role_name."</td>
				<td>".$row->role_description."</td>
				<td>".$status."</td>
				<td class='text-center'><span data-toggle='tooltip' data-placement='top' data-original-title='Edit'><a class='btn btn-info btn-xs' onclick=myFunction('".$row->role_id."') id='".$row->role_id."' href='#'><i class='fas fa-edit'></i></a></span>&nbsp;<span data-toggle='tooltip' data-placement='top' data-original-title='Delete'><a class='btn btn-danger btn-xs' data-toggle='modal' onclick=deleteRecord('".$row->role_id."') id='".$row->role_id."' data-target='#deleteModal'><i class='fa fa-trash'></i></a></span></td>
			  </tr>";
			  $i++;
			  }
		echo "</table>";
	    echo "<div class='float-right'>$Pagination</div>";
	}

	function GetTotalRecord(){
		$this->db->select('*');
		$this->db->from(USER_ROLE_TBL);
		$this->db->order_by('role_name','ASC');
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
