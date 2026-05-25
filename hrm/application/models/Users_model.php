<?php 
class Users_model extends CI_Model {
		
	function __construct()
	{
		parent::__construct();
	}	
		
	function InsertRecord(){
		$employee		=$this->input->post('employee-id');
		$company		=$this->input->post('company-id');
		$branch			=$this->input->post('branch-id');
		$user_name		=$this->input->post('user-name');
		$access_key		=$this->input->post('password');
		$password 		=$this->encrypt->encode($access_key);
		$email			="";	
		$user_role		=$this->input->post('role-id');
		$user_status		=$this->input->post('status');
		$user_id		=$this->input->post('user-id');
        	if(($user_id=="" && $user_name!="" && $password!="") && ($employee >0 && $company >0 && $branch >0)){
             	$data = array(
                'ref_id'        =>$employee,
                'company_id'    =>$company,
                'branch_id'     =>$branch,
                'user_name' 	=>$user_name,
                'password'	    =>$password,
                'access_key' 	=>$access_key,
                'email'	        =>$email,
                'user_role'     =>$user_role,
                'user_status'	=>$user_status
            	);
            	$this->db->insert(USER_TBL, $data);
        	}else{
            	$this->EditRecord($user_id);
        	}
		//print  $this->db->last_query();
	}
	//============== User Retrive by Ajax================
	function GetTotalUsers(){					
		$this->db->select('u.*,e.account_name,e.account_details,r.role_name');
		$this->db->from(USER_TBL.' as u');
        $this->db->join(ACC_HEAD_TBL.' AS e', 'e.account_id=u.ref_id','LEFT');
        $this->db->join(USER_ROLE_TBL.' AS r', 'r.role_id=u.user_role','LEFT');
		//$this->db->where('Username !=', "admin");
		$this->db->group_by('u.user_id');
		$this->db->order_by('u.user_role,e.account_name','ASC'); 
		$query = $this->db->get();
		return $query->num_rows();
	}
	   
	function GetRecordGrid(){
		$menu_slug= $this->uri->segment(1);
		$this->load->model('Site_model');
		$hasEditPM = $this->Site_model->hasOptionPermission($menu_slug,"Edit");
		$hasDelPM  = $this->Site_model->hasOptionPermission($menu_slug,"Delete");		   
		$from		=$this->input->post('from');
		$to		=$this->input->post('to');
		if($from==""){ $from=0;} if($to==""){ $to=12;}
		$head_type =array(1,2,10,11);
		$this->db->select('u.*,e.account_name,e.account_details,r.role_name');
		$this->db->from(USER_TBL.' as u');
        $this->db->join(ACC_HEAD_TBL.' AS e', 'e.account_id=u.ref_id','LEFT');
        $this->db->join(USER_ROLE_TBL.' AS r', 'r.role_id=u.user_role','LEFT');
		$this->db->where_in('e.head_type', $head_type);
		$this->db->group_by('u.user_id');
		$this->db->order_by('u.user_role,e.account_name','ASC'); 
		$this->db->limit($to,$from);
		$query = $this->db->get(); 
		//print $this->db->last_query();
		$totalrecord = $this->GetTotalUsers();
		$perPage=12; $Pagination="";
		if($totalrecord >0){
		$Pagination = $this->getPagination($totalrecord,$perPage);
		}
		echo "<div>$Pagination</div><br class='clear4'>".
		'<table width="100%"  border="0" class="table table-bordered">
		  <tr class="active">
			<th width="2%">'.$this->lang->line("sl").'</th>
			<th width="30%">'.$this->lang->line("user").$this->lang->line("r").' '.$this->lang->line("name").'</th>
			<th width="27%">'.$this->lang->line("login").' '.$this->lang->line("id").'</th>
			<th width="28%">'.$this->lang->line("user").' '.$this->lang->line("role").'</th>
			<th width="10%" class="text-center">'.$this->lang->line("options").'</th>
		  </tr>';
		  $i=1;
		  foreach($query->result() as $row){
		  if($i%2==0){ $tblrow="success";}else{$tblrow="warning";}		  
		  echo "<tr class='".$tblrow."'>
			<td>".$i."</td>
			<td>".$row->account_name."</td>
			<td>".$row->user_name."</td>
			<td>".$row->role_name."</td>
			<td align='center align-middle'>";
			if($hasEditPM){
			echo "&nbsp;<span data-toggle='tooltip' data-original-title='Edit'><a class='btn btn-info btn-md' data-toggle='modal' onclick=editRecord('".$row->user_id."') id='".$row->user_id."' href='#addModal'><i class='fa fa-edit'></i></a></span>&nbsp;";
			} 
			echo "	
			</td>
		  </tr>";
		  $i++;
		  }
		echo '</table>';
	}
	function DelRecord(){
		$id 	=$this->uri->segment(3);
		$this->db->where('ID',$id);
		$this->db->delete(USER_TBL);
		$this->session->set_flashdata('msg', 'Records successfully Deleted !');
		redirect('users', 'location');
	}
	//------------------
	function FillRecord(){
		$user_id	=$this->input->post('id');
		$this->db->select('*');
		$this->db->from(USER_TBL);
		$this->db->where('user_id', $user_id);
		$query = $this->db->get();
		return $query->row();
	}
	function GetUserInfo(){
		$user_id	=$this->session->userdata('created_by');		
		$query = $this->db->query("SELECT * FROM ".USER_TBL." WHERE user_id = $user_id",FALSE);
		//echo $this->db->last_query();
		return $query->row();
	}
	function ChangePassword(){
		$user_id		=$this->session->userdata('created_by');
		$access_key		=$this->input->post('user_password');
		$password 		=$this->encrypt->encode($access_key);		
		$data = array('password' =>$password,'access_key'=>$access_key);	
		$this->db->where('user_id',$user_id);
		$result = $this->db->update(USER_TBL, $data);
		echo $this->db->last_query();
		return $result;
	}
	function EditRecord($user_id){
		$employee		=$this->input->post('employee-id');
		$company		=$this->input->post('company-id');
		$branch			=$this->input->post('branch-id');
		$user_name		=$this->input->post('user-name');
		$access_key		=$this->input->post('password');
		$password 		=$this->encrypt->encode($access_key);
		$email			="";	
		$user_role		=$this->input->post('role-id');
		$user_status	=$this->input->post('status');
		$data = array(
                'ref_id'        =>$employee,
                'company_id'    =>$company,
                'branch_id'     =>$branch,
                'user_name' 	=>$user_name,
                'password'	    =>$password,
                'access_key' 	=>$access_key,
                'email'	        =>$email,
                'user_role'     =>$user_role,
                'user_status'	=>$user_status
		    );
		$this->db->where('user_id',$user_id);
		$this->db->update(USER_TBL, $data);
		//echo $this->db->last_query();
	}
	function GetUserList(){
		$this->db->select('*');
		$this->db->from(USER_TBL);
		$this->db->order_by('user_id','ASC');
		$query = $this->db->get();
		return $query;
	}
	
	function chkUserName(){
		$userName =$this->input->post('user-name');
		$this->db->select('*');
		$this->db->from(USER_TBL);
		$this->db->where('user_name', $userName);
		$query = $this->db->get(); //echo $this->db->last_query();
		echo $query->num_rows();
		//return $query->row();

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
   //End Class
}
