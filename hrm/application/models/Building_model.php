<?php 
class Building_model extends CI_Model {
		
	function __construct()
	{
		parent::__construct();
	}	
		
	function InsertRecord(){
		$companyName		=$this->input->post('company_name');
		$branchName	        =$this->input->post('branch_name');
		$buildingName	    	=$this->input->post('building_name');
		$buildingDescription	=$this->input->post('building_description');
		$total_floor	    	=$this->input->post('total_floor');
		$buildingId	        =$this->input->post('building_id');

		if($buildingId==''){
			$data = array(
			'company_id'          =>$companyName,
			'branch_id'           =>$branchName,
			'building_name'       =>$buildingName,
			'building_description'=>$buildingDescription,
			'total_floor'         =>$total_floor
			);
		        $this->db->insert(BUILDING_TBL, $data);
		}else{
			$this->EditRecord($buildingId);
		}
		//print  $this->db->last_query();
	}
        //============== Project Retrive by Ajax================
        function GetRecordGrid(){
		$menu_slug= $this->uri->segment(1);
		$this->load->model('Site_model');
		$hasEditPM = $this->Site_model->hasOptionPermission($menu_slug,"Edit");
		$hasDelPM  = $this->Site_model->hasOptionPermission($menu_slug,"Delete");
		$from =$this->input->post('from');
		$to   =$this->input->post('to');
		if($from==""){ $from=0;} if($to==""){ $to=50;}
		$this->db->select('p.*,b.branch_name,c.company_name');
		$this->db->from(BUILDING_TBL." AS p");
		$this->db->join(BRANCH_TBL.' AS b', 'b.branch_id=p.branch_id','LEFT');
		$this->db->join(COMPANY_SETTINGS_TBL.' AS c', 'c.company_id=b.company_id','LEFT');
		$this->db->group_by('p.building_id');
		$this->db->order_by('p.building_name','ASC');
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
                 <th width="17%">'.$this->lang->line("company_name").'</th>
                 <th width="17%">'.$this->lang->line("branch_name").'</th>
                 <th width="16%">'.$this->lang->line("building_name").'</th>
                 <th width="20%">'.$this->lang->line("building").' '.$this->lang->line("of").' '.$this->lang->line("description").'</th>
                 <th width="12%">'.$this->lang->line("total_floor").'</th>
                 <th width="16%" class="text-center">'.$this->lang->line("options").'</th>
            	</tr>
		</thead>';
		$i=1;
		foreach($query->result() as $row){
		//if($i%2==0){ $tblrow="success";}else{$tblrow="warning";}
		echo "<tr class='default'>
			<td>".$i."</td>
			<td>".$row->company_name."</td>
			<td>".$row->branch_name."</td>
			<td>".$row->building_name."</td>
			<td>".$row->building_description."</td>
			<td>".$row->total_floor."</td>
			<td class='text-center'>";
	    		if($hasEditPM){
			echo "<span data-toggle='tooltip' data-placement='top' data-original-title='Edit'> <a class='btn btn-info btn-xs' onclick=myFunction('".$row->building_id."') id='".$row->building_id."' href='#'><i class='fas fa-edit'></i></a></span>&nbsp;";
			}
			if($hasDelPM){				
			echo "<span data-toggle='tooltip' data-placement='top' data-original-title='Delete'><a class='btn btn-danger btn-xs'  data-toggle='modal' onclick=deleteRecord('".$row->building_id."') id='".$row->building_id."' href='#deleteModal'><i class='fas fa-trash'></i></a></span>";
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
		$this->db->from(BUILDING_TBL);
		$this->db->order_by('building_name','ASC');
		$query = $this->db->get();
		if($query->num_rows() >0){
		    return $query->num_rows();
		}else{
		    return 0;
		}//echo $this->db->last_query();
        }
		
        function DelRecord(){
		$id =$this->input->post('id');
		$this->db->where('building_id',$id);
		$this->db->delete(BUILDING_TBL);
        }
	
	function FillRecord(){
        $building_id	=$this->input->post('id');
        $this->db->select('*');
        $this->db->from(BUILDING_TBL);
        $this->db->where('building_id', $building_id);
        $query = $this->db->get();
        return $query->row();
	}
	function EditRecord($building_id){
		$companyName	    =$this->input->post('company_name');
		$branchName	    =$this->input->post('branch_name');
		$buildingName	    =$this->input->post('building_name');
		$buildingDescription=$this->input->post('building_description');
		$total_floor	    =$this->input->post('total_floor');

		$data = array(
		    'company_id'           =>$companyName,
		    'branch_id'            =>$branchName,
		    'building_name'        =>$buildingName,
		    'building_description' =>$buildingDescription,
		    'total_floor'          =>$total_floor
		);
		$this->db->where('building_id',$building_id);
		$this->db->update(BUILDING_TBL, $data);
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
       //End Class
}
