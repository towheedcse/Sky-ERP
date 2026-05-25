<?php 
class Menu_model extends CI_Model {
		
	function __construct()
	{
		parent::__construct();
	}

	function InsertRecord(){
		$company_id	    =$this->input->post('company_id');
		$module_id	    =$this->input->post('module_id');
		$menu_slug	    =$this->input->post('menu_slug');
		$menu_name	    =$this->input->post('menu_name');
		$order_no       =$this->input->post('order_no');
		$menu_status	=$this->input->post('menu_status');
		$menu_id	    =$this->input->post('menu_id');

		if($menu_id==''){
			$data = array(
		        'company_id'    =>$company_id,
		        'module_id'     =>$module_id,
		        'menu_slug' 	=>$menu_slug,
		        'menu_name'	=>$menu_name,
		        'order_no'      =>$order_no,
		        'menu_status'	=>$menu_status
			);
		        $this->db->insert(MENU_TBL, $data);
		}else{
			$this->EditRecord($menu_id);
		}
		//print  $this->db->last_query();
   	}
	function EditRecord($menu_id){
		$company_id	=$this->input->post('company_id');
		$module_id      =$this->input->post('module_id');
		$menu_slug      =$this->input->post('menu_slug');
		$menu_name      =$this->input->post('menu_name');
		$order_no       =$this->input->post('order_no');
		$menu_status    =$this->input->post('menu_status');

		$data = array(
		    'company_id'    =>$company_id,
		    'module_id'     =>$module_id,
		    'menu_slug'     =>$menu_slug,
		    'menu_name'     =>$menu_name,
		    'order_no'      =>$order_no,
		    'menu_status'   =>$menu_status
		);
		$this->db->where('menu_id',$menu_id);
		$this->db->update(MENU_TBL, $data);
		if($menu_id >0){
			$this->UpdateChildInfo($company_id,$module_id,$menu_id);
		}
    }
	function UpdateChildInfo($company_id,$module_id,$menu_id){
		//===== Update Menu Permission ====
		$mpdata = array(
		    'company_id'    =>$company_id,
		    'module_id'     =>$module_id
		);
		$this->db->where('menu_id',$menu_id);
		$this->db->update(MENU_PERMISSION_TBL, $mpdata);		
		//===== Update Option ====
		$odata = array(
		    'company_id'    =>$company_id,
		    'module_id'     =>$module_id
		);
		$this->db->where('menu_id',$menu_id);
		$this->db->update(OPTIONS_TBL, $odata);		
		//===== Update Option Permission ====
		$opdata = array(
		    'company_id'    =>$company_id,
		    'module_id'     =>$module_id
		);
		$this->db->where('menu_id',$menu_id);
		$this->db->update(OPTIONS_PERMISSION_TBL, $opdata);
	}
	function DelRecord(){
		$id =$this->input->post('id');
		$this->db->where('menu_id',$id);
		$this->db->delete(MENU_TBL);
	}
	
	function FillRecord(){
		$menu_id	=$this->input->post('id');
		$this->db->select('*');
		$this->db->from(MENU_TBL);
		$this->db->where('menu_id', $menu_id);
		$query = $this->db->get();
		return $query->row();
	}
   	//============== Category Retrive by Ajax================
   	function GetRecordGrid(){
		$menu_slug= $this->uri->segment(1);
		$this->load->model('Site_model');
		$hasEditPM = $this->Site_model->hasOptionPermission($menu_slug,"Edit");
		$hasDelPM  = $this->Site_model->hasOptionPermission($menu_slug,"Delete");
		$company_id		=$this->input->post('company-id');
        $srcmodule_id	=$this->input->post('src-module');
        $srcmenu_id		=$this->input->post('src-menu');
		$myrole 		=$this->session->userdata('user_role');
        if(empty($company_id)){$company_id = $this->session->userdata('company_id');}
	   	$from	=$this->input->post('from');
		$to	=$this->input->post('to');
		if($from==""){ $from=0;} if($to==""){ $to=50;}
		$this->db->select('m.*,c.company_name,md.module_name');
		$this->db->from(MENU_TBL.' AS m');
        $this->db->join(MODULE_TBL.' AS md', 'md.module_id=m.module_id','LEFT');
	  	$this->db->join(COMPANY_SETTINGS_TBL.' AS c', 'c.company_id=m.company_id','LEFT');
		if($company_id >0){
            $this->db->where('m.company_id',$company_id);
        }
        if($srcmodule_id >0){
            $this->db->where('m.module_id',$srcmodule_id);
        }
        if($srcmenu_id >0){
            $this->db->where('m.menu_id',$srcmenu_id);
        }
		$this->db->group_by('m.menu_id');
		$this->db->order_by('m.order_no','ASC');
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
		  	<th width="23%">'.$this->lang->line("company_name").'</th>
			<th width="13%">'.$this->lang->line("module_name").'</th>
			<th width="17%">'.$this->lang->line("menu").' '.$this->lang->line("slug").'</th>
			<th width="16%">'.$this->lang->line("menu_name").'</th>
        		<th width="7%">'.$this->lang->line("order").'</th>
			<th width="8%">'.$this->lang->line("status").'</th>
			<th width="14%" class="text-center">'.$this->lang->line("options").'</th>
		  </tr>
		</thead>';
		  $i=1;
		  foreach($query->result() as $row){
                  if($row->menu_status==1){ $status=$this->lang->line("active");}elseif($row->menu_status==0){ $status=$this->lang->line("inactive");}
		  echo "<tr class='default'>
                    <td>".$i."</td>
                    <td>".$row->company_name."</td>
                    <td>".$row->module_name."</td>
                    <td>".$row->menu_slug."</td>
                    <td>".$row->menu_name."</td>
                    <td>".$row->order_no."</td>
                    <td>".$status."</td>
                    <td class='text-center align-middle'>";
		    if($hasEditPM){
		    echo "<span data-toggle='tooltip' data-placement='top' data-original-title='Edit'><a class='btn btn-info btn-xs' onclick=editRecord('".$row->menu_id."') id='".$row->menu_id."' href='#'><i class='fas fa-edit'></i></a></span>&nbsp;";
		    }
		    if($hasDelPM){
		    echo "<span data-toggle='tooltip' data-placement='top' data-original-title='Delete'><a class='btn btn-danger btn-xs' data-toggle='modal' onclick=deleteRecord('".$row->menu_id."') id='".$row->menu_id."' data-target='#deleteModal'><i class='fa fa-trash'></i></a></span>";
		    }
		    echo "</td>
                  </tr>";
			    $i++;
			  }
		echo '</table>';
	    	echo "<div class='float-right'>$Pagination</div>";
	}

	function GetTotalRecord(){		
		$company_id		=$this->input->post('company-id');
        $srcmodule_id	=$this->input->post('src-module');
        $srcmenu_id		=$this->input->post('src-menu');
		$myrole 		=$this->session->userdata('user_role');
        if(empty($company_id)){$company_id = $this->session->userdata('company_id');}
		
		$this->db->select('*');
		$this->db->from(MENU_TBL);
		if($company_id >0){
            $this->db->where('company_id',$company_id);
        }
        if($srcmodule_id >0){
            $this->db->where('module_id',$srcmodule_id);
        }
        if($srcmenu_id >0){
            $this->db->where('menu_id',$srcmenu_id);
        }
		$this->db->order_by('menu_name','ASC');
		$query = $this->db->get();
		if($query->num_rows() >0){
			return $query->num_rows();
		}else{
			return 0;
		}//echo $this->db->last_query();
	}
    function GetPermissionRecordGrid(){
        $company_id		=$this->input->post('company_id');
        $module_id		=$this->input->post('module_id');
        $menu_status	=$this->input->post('status');
		$myrole 		=$this->session->userdata('user_role');
        if(empty($company_id)){$company_id = $this->session->userdata('company_id');}
        if(empty($menu_status)){$menu_status=0;}
        $from	=$this->input->post('from');
        $to		=$this->input->post('to');
        if($from==""){ $from=0;} if($to==""){ $to=50;}
        $this->db->select('m.*,md.module_name');
        $this->db->from(MENU_TBL.' AS m');
        $this->db->join(MODULE_TBL.' AS md', 'md.module_id=m.module_id','LEFT');
        if($company_id >0){
            $this->db->where('md.company_id',$company_id);
        }
        if($module_id >0){
            $this->db->where('m.module_id',$module_id);
        }
        if($menu_status >0){
            $this->db->where('m.menu_status',$menu_status);
        }
        $this->db->group_by('m.menu_id');
        $this->db->order_by('m.order_no','ASC');
        $this->db->limit($to,$from);
        $query = $this->db->get();//print  $this->db->last_query();
        $totalrecord = $this->GetTotalPermissionRecordGrid();
        $perPage=50; $Pagination="";
        if($totalrecord >0){
            $Pagination = $this->getPagination($totalrecord,$perPage);
        } //print  $this->db->last_query();
        //==== Get User Role =======
        $this->db->select('*');
        $this->db->from(USER_ROLE_TBL);
        $this->db->where('role_status', 1);
        $rquery 	= $this->db->get();
        $totalroles 	= $rquery->num_rows();
	if($totalroles >0){
        $width		= (66/$totalroles);
	}else{
	$width		= 66;
	}
        $grid = "";
        $grid = "<table width='100%'  border='0' class='table table-responsive table-bordered table-hover custab'>
	<thead>
	<tr class='bg-light'>
  	<th width='2%' rowspan='2' class='align-middle'>".$this->lang->line("sl")."</th>
  	<th width='16%' rowspan='2' class='align-middle'>".$this->lang->line("module_name")."</th>
  	<th width='16%' rowspan='2' class='align-middle'>".$this->lang->line("menu_name")."</th>
  	<th width='66%' colspan='".$totalroles."' class='text-center'>".$this->lang->line("user_role")."</th>
	</tr>
	<tr class='bg-light'>
	";
	if($totalroles >0){
	foreach($rquery->result() as $rrow){
	$grid.= "<th width='".$width."%' class='text-center'>".$rrow->role_name."</th>";
	}
	}
	$grid.= "
	  </tr>
	</thead>";
        $i=1;
        foreach($query->result() as $row){
            $module_id = $row->module_id;
            $menu_id = $row->menu_id;
            $grid.="<tr class='default'>
		<td>".$i."</td>
		<td>".$row->module_name."</td>
        	<td>".$row->menu_name."</td>";
            if($totalroles >0){
                foreach($rquery->result() as $rrow){
                    $role_id = $rrow->role_id; $checked ="";
                    $CSQL = "SELECT * FROM ".MENU_PERMISSION_TBL." WHERE company_id=".$company_id." AND module_id=".$module_id." AND menu_id=".$menu_id." AND role_id=".$role_id;
                    $CRES = $this->db->query($CSQL);
                    if($CRES->num_rows() >0){ $checked="checked";}else{ $checked="";}
		    if($myrole >=$role_id){$disabled="disabled";}else{$disabled="";}
		    if($myrole ==1){$disabled="";}
                    $grid.= "<td class='text-center'><input  type='checkbox' $checked onclick=savePermission('".$module_id."','".$menu_id."','".$role_id."',this) $disabled /></td>";
                }
            }
            $grid.= "</tr>";
            $i++;
        }
        $grid.= "</table>";
        $grid.= "<div class='float-right'>$Pagination</div>";
        return $grid;
    }

    function GetTotalPermissionRecordGrid(){
        $company_id		=$this->input->post('company_id');
        $module_id		=$this->input->post('module_id');
        $menu_status	=$this->input->post('status');
        if(empty($company_id)){$company_id = $this->session->userdata('company_id');}
        if(empty($menu_status)){$menu_status=0;}
        $this->db->select('*');
        $this->db->from(MENU_TBL);
        if($company_id >0){
            $this->db->where('company_id',$company_id);
        }
        if($module_id >0){
            $this->db->where('module_id',$module_id);
        }
        if($menu_status >0){
            $this->db->where('menu_status',$menu_status);
        }
        $this->db->group_by('menu_id');
        $this->db->order_by('order_no','ASC');
        $query = $this->db->get();
        if($query->num_rows() >0){
            return $query->num_rows();
        }else{
            return 0;
        }//echo $this->db->last_query();
    }

    function InsertPermissionRecord(){
        $company_id	    =$this->input->post('company_id');
        $module_id	    =$this->input->post('module_id');
        $menu_id	    =$this->input->post('menu_id');
        $role_id	    =$this->input->post('role_id');
        $action_type	    =$this->input->post('action_type');
        if($company_id==0){
            $company_id=$this->session->userdata('company_id');
        }
        if($action_type=='insert'){
            $data = array(
                'company_id'  =>$company_id,
                'module_id'   =>$module_id,
                'menu_id'     =>$menu_id,
                'role_id'     =>$role_id
            );
            $this->db->insert(MENU_PERMISSION_TBL, $data);
        }else{
            $this->db->where('company_id',$company_id);
            $this->db->where('module_id',$module_id);
            $this->db->where('menu_id',$menu_id);
            $this->db->where('role_id',$role_id);
            $this->db->delete(MENU_PERMISSION_TBL);
        }
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
