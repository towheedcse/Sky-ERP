<?php 
class Leavemanage_model extends CI_Model {
		
	function __construct()
	{
		parent::__construct();
	}	
	
	function InsertRecord(){
	    $total_days=0; $session_id=0; $from_date=""; $leave_to="";
	    $session_id     =$this->session->userdata('default_session');
		$leave_id	    =$this->input->post('leave_id');
		$employee_id	=$this->input->post('employee_id');
		$leave_nature	=$this->input->post('leave_nature');
		$leave_type	    =$this->input->post('leave_type');
		$leave_from   	=$this->formatDate($this->input->post('leave_from'));
		$leave_to   	=$this->formatDate($this->input->post('leave_to'));
		$application_date=$this->formatDate($this->input->post('application_date'));
		$recommended_by	=$this->input->post('recommended_by');
		$section_chief	=$this->input->post('section_chief');
		$dept_head	    =$this->input->post('dept_head');
		$leave_address	=str_replace("U 0026", '&', $this->input->post('leave_address'));
		$leave_purpose	=str_replace("U 0026", '&', $this->input->post('leave_purpose'));
		$leave_mobile	=$this->input->post('leave_mobile');
		$leave_year     =$this->LeaveYear($leave_to);
		if($leave_type==1){
		$total_days     =$this->getTotalDays($leave_from,$leave_to);
		$remarks        = $leave_purpose;
		}elseif($leave_type==2){
		$total_days     =0.5;
		$remarks        ="Half Leave";
		}elseif($leave_type==3){
		$total_days     =0.25;
		$remarks        ="Early Leave";
		}
		$created_by		=$this->session->userdata('created_by');
		
		if($employee_id >0){
		  $ESQL = "SELECT * FROM ".EMPLOYEE_TBL." WHERE employee_id=".$employee_id." AND status =1";
		  $ERES = $this->db->query($ESQL);
		  if($ERES->num_rows() >0){
		      $company_id     = $ERES->row()->company_id; 
		      $branch_id      = $ERES->row()->branch_id;
		      $department_id  = $ERES->row()->department_id;
		      $section_id     = $ERES->row()->section_id;
		      $shift_id       = $ERES->row()->shift_id;
		  }
		}
		
		if(empty($shift_id)){ $shift_id =$this->session->userdata('default_shift'); }
	    if(empty($session_id)){ $session_id =$this->session->userdata('default_session'); }
	    
		if($leave_id==""){						
			$data = array(
			'company_id'    		=>$company_id,
			'branch_id'    			=>$branch_id,
			'department_id'    		=>$department_id,	
			'section_id'    		=>$section_id,
			'employee_id'    		=>$employee_id,
			'session_id'    		=>$session_id,
			'leave_nature'    		=>$leave_nature,
			'leave_type'    		=>$leave_type,	
			'leave_from'    		=>$leave_from,	
			'leave_to'    		    =>$leave_to,
			'application_date'		=>$application_date,
			'total_days'			=>$total_days,
			'leave_year'			=>$leave_year,
			'recommended_by'		=>$recommended_by,
			'section_chief'		    =>$section_chief,
			'dept_head'		        =>$dept_head,
			'leave_address'		    =>$leave_address,
			'leave_mobile'		    =>$leave_mobile,
			'leave_purpose'		    =>$leave_purpose,
			'created_by'     		=>$created_by
			);
			//=== Remove empty field ====
			$data = array_filter($data);
			//=== Remove unexpected field by value (e.g value) ====
			if (($key = array_search('value', $data)) !== false) { unset($data[$key]);}						
			$this->db->insert(LEAVE_TBL, $data);
			$leave_id  = $this->db->insert_id();
			//===== Start Leave Attendance =======
			if($leave_id >0 && $leave_type < 2){
			    //===== Get Shift ID =======
        		if($shift_id >0){
        		  $TSQL = "SELECT * FROM ".SHIFT_TBL." WHERE shift_id=".$shift_id." AND institute_id=".$company_id." AND status =1";
        		  $TRES = $this->db->query($TSQL);
        		  if($TRES->num_rows() >0){$in_time = $TRES->row()->shift_start; $out_time = $TRES->row()->shift_end;}else{$in_time=date("H:i:s");}
        		}
        		
        		$MSQL   ="SELECT * FROM ".MONTH_DAYS_TBL." WHERE `date_field` BETWEEN '$leave_from' AND '$leave_to'"; 
			    $mquery = $this->db->query($MSQL);
			    if($mquery->num_rows() >0){
			     foreach($mquery->result() as $mrow){
			        $attendance_date    = $mrow->date_field; 
			        $present = 0; $day_type=4;
			        
			        $LSQL = "SELECT * FROM ".ATTENDANCE_TBL." WHERE account_id=".$employee_id." AND attendance_date='".$attendance_date."' AND institute_id=".$company_id." AND day_type =4";
        		    $LRES = $this->db->query($LSQL);
        		    if($LRES->num_rows() >0){
        		       $DASQL="DELETE FROM ".ATTENDANCE_TBL." WHERE account_id = $employee_id AND day_type=4 AND `attendance_date`='".$attendance_date."'"; 
		               $this->db->query($DASQL); 
        		    }
        		  
            		$check_in_datetime  = "";
            		$check_out_datetime = "";
            		$raw_in_time 		= 0;
            		$raw_out_time 		= 0;
            		$total_hour         = 0;
            		
        			$data = array(
                    "institute_id"		=>$company_id,
                    "branch_id"   		=>$branch_id,
                    "department_id"    	=>$department_id,
                    "session_id"   		=>$session_id,
                    "shift_id"    		=>$shift_id,
                    "section_id"    	=>$section_id,
                    "account_id"    	=>$employee_id,
                    "attendance_date"   =>$attendance_date,
                    "check_in_datetime" =>$check_in_datetime,
                    "check_out_datetime"=>$check_out_datetime,
                    "raw_in_time" 		=>$raw_in_time,
                    "raw_out_time" 		=>$raw_out_time,
                    "in_time"    		=>"",
                    "out_time"    		=>"",
                    "day_type"    	    =>$day_type,
                    "total_hour"    	=>$total_hour,
                    "inout_status"    	=>0,
                    "present"    		=>$present,
                    "remarks"    		=>$remarks,
    				"created_by" 		=>$created_by
    			    );
    			    $this->db->insert(ATTENDANCE_TBL, $data); 
			     }//end foreach
			    }//end if mquery
			}//end if leave_id
			//===== End Leave Attendance =======
		}else{
			$this->EditRecord($leave_id);
		}
		//print  $this->db->last_query();
   	}
	function EditRecord($leave_id){
	    $total_days=0; $session_id=0; $from_date=""; $leave_to="";
	    $session_id     =$this->session->userdata('default_session');
		$employee_id	=$this->input->post('employee_id');
		$leave_nature	=$this->input->post('leave_nature');
		$leave_type	    =$this->input->post('leave_type');
		$leave_from   	=$this->formatDate($this->input->post('leave_from'));
		$leave_to   	=$this->formatDate($this->input->post('leave_to'));
		$application_date=$this->formatDate($this->input->post('application_date'));
		$recommended_by	=$this->input->post('recommended_by');
		$section_chief	=$this->input->post('section_chief');
		$dept_head	    =$this->input->post('dept_head');
		$leave_address	=str_replace("U 0026", '&', $this->input->post('leave_address'));
		$leave_purpose	=str_replace("U 0026", '&', $this->input->post('leave_purpose'));
		$leave_mobile	=$this->input->post('leave_mobile');
		$leave_year     =$this->LeaveYear($leave_to);
		if($leave_type==1){
		$total_days     =$this->getTotalDays($leave_from,$leave_to);
		$remarks        =$leave_purpose;
		}elseif($leave_type==2){
		$total_days     =0.5;
		$remarks        ="Half Leave";
		}elseif($leave_type==3){
		$total_days     =0.25;
		$remarks        ="Early Leave";
		}
		$created_by		=$this->session->userdata('created_by');
		$modified_by	=$this->session->userdata('created_by');
		$modified_time  =date("Y-m-d H:i:s");
		if($employee_id >0){
		  $ESQL = "SELECT * FROM ".EMPLOYEE_TBL." WHERE employee_id=".$employee_id." AND status =1";
		  $ERES = $this->db->query($ESQL);
		  if($ERES->num_rows() >0){
		      $company_id     = $ERES->row()->company_id; 
		      $branch_id      = $ERES->row()->branch_id;
		      $department_id  = $ERES->row()->department_id;
		      $section_id     = $ERES->row()->section_id;
		      $shift_id       = $ERES->row()->shift_id;
		  }
		}
		
		if(empty($shift_id)){ $shift_id =$this->session->userdata('default_shift'); }
		
		$data = array(
			'company_id'    		=>$company_id,
			'branch_id'    			=>$branch_id,
			'department_id'    		=>$department_id,	
			'section_id'    		=>$section_id,
			'employee_id'    		=>$employee_id,
			'session_id'    		=>$session_id,
			'leave_nature'    		=>$leave_nature,
			'leave_type'    		=>$leave_type,	
			'leave_from'    		=>$leave_from,	
			'leave_to'    		    =>$leave_to,
			'application_date'		=>$application_date,
			'total_days'			=>$total_days,
			'leave_year'			=>$leave_year,
			'recommended_by'		=>$recommended_by,
			'section_chief'		    =>$section_chief,
			'dept_head'		        =>$dept_head,
			'leave_address'		    =>$leave_address,
			'leave_mobile'		    =>$leave_mobile,
			'leave_purpose'		    =>$leave_purpose,
			'modified_by'     	    =>$modified_by,
			'modified_time'     	=>$modified_time
		);
		$this->db->where('leave_id',$leave_id);
		$this->db->update(LEAVE_TBL, $data); //print  $this->db->last_query(); exit;
		
		//===== Start Leave Attendance =======
		if($leave_id >0 && $leave_type < 2){
		    //===== Get Shift ID =======
    		if($shift_id >0){
    		  $TSQL = "SELECT * FROM ".SHIFT_TBL." WHERE shift_id=".$shift_id." AND institute_id=".$company_id." AND status =1";
    		  $TRES = $this->db->query($TSQL);
    		  if($TRES->num_rows() >0){$in_time = $TRES->row()->shift_start; $out_time = $TRES->row()->shift_end;}else{$in_time=date("H:i:s");}
    		}
    		
    		$MSQL   ="SELECT * FROM ".MONTH_DAYS_TBL." WHERE `date_field` BETWEEN '$leave_from' AND '$leave_to'"; 
		    $mquery = $this->db->query($MSQL);
		    if($mquery->num_rows() >0){
		     foreach($mquery->result() as $mrow){
		        $attendance_date    = $mrow->date_field; 
		        $present = 0; $day_type=4;
        		$check_in_datetime  = "";
        		$check_out_datetime = "";
        		$raw_in_time 		= 0;
        		$raw_out_time 		= 0;
        		$total_hour         = 0;
			        
		        $LSQL = "SELECT * FROM ".ATTENDANCE_TBL." WHERE account_id=".$employee_id." AND attendance_date='".$attendance_date."' AND institute_id=".$company_id." AND day_type =4";
    		    $LRES = $this->db->query($LSQL);
    		    if($LRES->num_rows() >0){
    		       $DASQL="DELETE FROM ".ATTENDANCE_TBL." WHERE account_id = $employee_id AND day_type=4 AND `attendance_date`='".$attendance_date."'"; 
	               $this->db->query($DASQL); 
    		    }
        		
    			$data = array(
                "institute_id"		=>$company_id,
                "branch_id"   		=>$branch_id,
                "department_id"    	=>$department_id,
                "session_id"   		=>$session_id,
                "shift_id"    		=>$shift_id,
                "section_id"    	=>$section_id,
                "account_id"    	=>$employee_id,
                "attendance_date"   =>$attendance_date,
                "check_in_datetime" =>$check_in_datetime,
                "check_out_datetime"=>$check_out_datetime,
                "raw_in_time" 		=>$raw_in_time,
                "raw_out_time" 		=>$raw_out_time,
                "in_time"    		=>"",
                "out_time"    		=>"",
                "day_type"    	    =>$day_type,
                "total_hour"    	=>$total_hour,
                "inout_status"    	=>0,
                "present"    		=>$present,
                "remarks"    		=>$remarks,
				"created_by" 		=>$created_by,
				'modified_by' 		=>$modified_by,
				'modified_time' 	=>$modified_time
			    );
			    $this->db->insert(ATTENDANCE_TBL, $data); 
		     }//end foreach
		    }//end if mquery
		}//end if leave_id
		//===== End Leave Attendance =======
		
    }
	function LeaveYear($leave_to){
	    $LSQL="SELECT YEAR('$leave_to') AS leave_year";
        $TRES = $this->db->query($LSQL);
        return $TRES->row()->leave_year;
	}
	function getTotalDays($leave_from,$leave_to){
	    $LSQL="SELECT DATEDIFF( '$leave_to' , '$leave_from' )+1 AS total_days";
        $RES = $this->db->query($LSQL);
    	$total_days = $RES->row()->total_days;
    	//$total_days = ($total_days+1);
        return $total_days;
	}
	function getEmployInfo($employee_id){
	    if($employee_id >0){
		  $ESQL = "SELECT * FROM ".EMPLOYEE_TBL." WHERE employee_id=".$employee_id." AND status =1";
		  $query = $this->db->query($ESQL);
		  if($query->num_rows() >0){
		      return $query->row();
		  }
		}else{
		    return false;
		}
	}
	function getDepartmentName($department_id,$company_id){
	    $LSQL = "SELECT department_name FROM ".DEPARTMENT_TBL." WHERE `department_id` ='$department_id' AND company_id=$company_id";
        $RES = $this->db->query($LSQL);
    	return $RES->row()->department_name;
	}
	function getDepartmentHead($department_id,$company_id){
	    $LSQL = "SELECT head_of_department FROM ".DEPARTMENT_TBL." WHERE `department_id` ='$department_id' AND company_id=$company_id";
        $RES = $this->db->query($LSQL);
    	return $RES->row()->head_of_department;
	}
	function getSectionName($section_id,$department_id,$company_id){
	    $LSQL = "SELECT section_name FROM ".SECTION_TBL." WHERE `section_id` ='$section_id' AND `department_id` ='$department_id' AND company_id=$company_id";
        $RES = $this->db->query($LSQL);
    	return $RES->row()->section_name;
	}
	function getSectionHead($section_id,$department_id,$company_id){
	    $LSQL = "SELECT section_head FROM ".SECTION_TBL." WHERE `section_id` ='$section_id' AND `department_id` ='$department_id' AND company_id=$company_id";
        $RES = $this->db->query($LSQL);
    	return $RES->row()->section_head;
	}
	function DelRecord(){
		$leave_id = $this->input->post('id');		
		$this->db->select('employee_id,leave_from,leave_to');
		$this->db->from(LEAVE_TBL);
		$this->db->where('leave_id', $leave_id);
		$query = $this->db->get(); //print  $this->db->last_query(); 
		$employee_id = $query->row()->employee_id;
		$leave_from  = $query->row()->leave_from; 
		$leave_to    = $query->row()->leave_to;
		if($employee_id>0){
		$SQLM="DELETE FROM ".ATTENDANCE_TBL." WHERE account_id = $employee_id AND day_type=4 AND `attendance_date` BETWEEN '".$leave_from."' AND '".$leave_to."'"; 
		$this->db->query($SQLM);
			
		$this->db->where('leave_id',$leave_id);
		$this->db->delete(LEAVE_TBL);
		}
	}
	
	function FillRecord(){
        $leave_id=$this->input->post('id');
		$this->db->select('*');
		$this->db->from(LEAVE_TBL);
		$this->db->where('leave_id', $leave_id);
		$query = $this->db->get();
		$row   = $query->row();
		return $row;
	}
	
   	//============== Category Retrive by Ajax================
   	function GetRecordGrid(){
		$menu_slug  = $this->uri->segment(1);
		$this->load->model('Site_model');
		$hasEditPM  = $this->Site_model->hasOptionPermission($menu_slug,"Edit");
		$hasDelPM   = $this->Site_model->hasOptionPermission($menu_slug,"Delete");
		$hasPrintPM = $this->Site_model->hasOptionPermission($menu_slug,"Print");
	   	$employeeId= $this->input->post('src-employee-id');
	   	$srcFrom   = $this->formatDate($this->input->post('srcFrom'));
	   	$srcTo 	   = $this->formatDate($this->input->post('srcTo'));
	   	$from 	    = $this->input->post('from');
		$to	        = $this->input->post('to');
		if($from==""){ $from=0;} if($to==""){ $to=50;}
		$this->db->select('l.leave_id,l.employee_id,l.session_id,l.leave_nature,l.leave_type as leave_category,DATE_FORMAT(l.application_date ,"%d-%m-%Y") as application_date,DATE_FORMAT(l.leave_from ,"%d-%m-%Y") as leave_from,DATE_FORMAT(l.leave_to ,"%d-%m-%Y") as leave_to,l.leave_from as from_date, l.leave_to as to_date, l.total_days,lc.leave_type,lc.total_leave,c.company_id,c.company_name,b.branch_name,d.department_name,e.card_id,e.employee_name,e.designation');
		$this->db->from(LEAVE_TBL." AS l");
		$this->db->join(COMPANY_SETTINGS_TBL.' AS c', 'c.company_id=l.company_id','LEFT');
	  	$this->db->join(BRANCH_TBL.' AS b', 'b.branch_id=l.branch_id','LEFT');
	  	$this->db->join(DEPARTMENT_TBL.' AS d', 'd.department_id=l.department_id','LEFT');
	  	$this->db->join(EMPLOYEE_TBL.' AS e', 'e.employee_id=l.employee_id','LEFT');
	  	$this->db->join(LEAVE_CATEGORY_TBL.' AS lc', 'lc.category_id=l.leave_nature','LEFT');
	  	
		if($employeeId >0){
			  $this->db->where("l.employee_id", $employeeId);  	
		}
		if($srcFrom!="" && $srcTo==""){
		   $this->db->where("l.leave_from >=", $srcFrom);
		}elseif($srcFrom=="" && $srcTo!=""){
		   $this->db->where("l.leave_from <=", $srcTo);
		}elseif($srcFrom!="" && $srcTo!=""){
		   $this->db->where("l.leave_from BETWEEN '$srcFrom' AND '$srcTo'",NULL,FALSE);
		}
	  	
		if($this->session->userdata('user_role') ==4){
		$this->db->where("l.employee_id", $this->session->userdata('user_ref_id')); 
		}
		$this->db->group_by('l.leave_id');
		$this->db->order_by('l.leave_from','DESC');
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
			  	<th width="20%">'.$this->lang->line("employee").' '.$this->lang->line("details").'</th>
				<th width="10%">'.$this->lang->line("department").'</th>
				<th width="10%">'.$this->lang->line("leave_nature").'</th>
				<th width="10%">'.$this->lang->line("leave_from").'</th>
				<th width="10%">'.$this->lang->line("leave_to").'</th>
				<th width="10%">'.$this->lang->line("total_leave").'</th>
				<th width="8%" class="text-center">'.$this->lang->line("options").'</th>
			  </tr>
		</thead>';
		  $i=1;
		  foreach($query->result() as $row){
		      if($row->leave_category==1){
    		  $total_days     =$this->getTotalDays($row->from_date,$row->to_date);
    		  }elseif($row->leave_category==2){
    		  $total_days     =0.5;
    		  }elseif($row->leave_category==3){
    		  $total_days     =0.25;
    		  }
		    echo "<tr class='default'>
		  	<td>".$i."</td>
			<td>".$row->company_name.",<br>".$row->branch_name."</td>
		  	<td>".$row->employee_name."<br> ".$row->designation."</td>
			<td>".$row->department_name."</td>
			<td>".$row->leave_type."</td>
			<td>".$row->leave_from."</td>
			<td>".$row->leave_to."</td>
			<td>".$total_days."</td>
			<td class='text-center align-middle'>";
			if($hasEditPM){
			echo "<span data-toggle='tooltip' data-placement='top' data-original-title='Edit'><a class='btn btn-info btn-xs' onclick=editRecord('".$row->leave_id."') id='".$row->leave_id."' href='#'><i class='fas fa-edit'></i></a></span>&nbsp;";
			}
			if($hasDelPM){
			echo "<span data-toggle='tooltip' data-placement='top' data-original-title='Delete'><a class='btn btn-danger btn-xs' data-toggle='modal' onclick=deleteRecord('".$row->leave_id."') id='".$row->leave_id."' data-target='#deleteModal'><i class='fa fa-trash'></i></a></span>";
			}
			if($hasPrintPM){
			    echo "<div class='clearfix'></div><br><span data-toggle='tooltip' data-original-title='View Details'><a class='btn btn-success btn-sm' href='".base_url()."leavemanage/ViewDetails/".$row->leave_id."'><i class='fa fa-print'></i> ".$this->lang->line("leave")." ".$this->lang->line("details")."</a></span>";
			
			}
			echo "</td>
		  </tr>";
		  $i++;
		  }
		  echo '</table>';
	    echo "<div class='float-right'>$Pagination</div>";
	}

	function GetTotalRecord(){
	   	$employeeId= $this->input->post('src-employee-id');
	   	$srcFrom   = $this->formatDate($this->input->post('srcFrom'));
	   	$srcTo 	   = $this->formatDate($this->input->post('srcTo'));
		$this->db->select('l.leave_id');	
		$this->db->from(LEAVE_TBL." AS l");
		$this->db->join(COMPANY_SETTINGS_TBL.' AS c', 'c.company_id=l.company_id','LEFT');
	  	$this->db->join(BRANCH_TBL.' AS b', 'b.branch_id=l.branch_id','LEFT');
	  	$this->db->join(DEPARTMENT_TBL.' AS d', 'd.department_id=l.department_id','LEFT');
	  	$this->db->join(EMPLOYEE_TBL.' AS e', 'e.employee_id=l.employee_id','LEFT');
	  	$this->db->join(LEAVE_CATEGORY_TBL.' AS lc', 'lc.category_id=l.leave_nature','LEFT');
	  	
		if($employeeId >0){
			  $this->db->where("l.employee_id", $employeeId);  	
		}
		if($srcFrom!="" && $srcTo==""){
		   $this->db->where("l.leave_from >=", $srcFrom);
		}elseif($srcFrom=="" && $srcTo!=""){
		   $this->db->where("l.leave_from <=", $srcTo);
		}elseif($srcFrom!="" && $srcTo!=""){
		   $this->db->where("l.leave_from BETWEEN '$srcFrom' AND '$srcTo'",NULL,FALSE);
		}
	  	
		if($this->session->userdata('user_role') ==4){
		$this->db->where("l.employee_id", $this->session->userdata('user_ref_id')); 
		}
		$this->db->group_by('l.leave_id');
		$this->db->order_by('l.leave_from','DESC');
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
