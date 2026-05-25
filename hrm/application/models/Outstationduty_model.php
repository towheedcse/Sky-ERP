<?php 
class Outstationduty_model extends CI_Model {
		
	function __construct()
	{
		parent::__construct();
	}	
	
	function InsertRecord(){
		$company_id	    =$this->input->post('company_id');
		$branch_id		=$this->input->post('branch_id');
		$department_id	=$this->input->post('department_id');
		$section_id		=$this->input->post('section_id');
		$session_id		=$this->input->post('session_id');
		$shift_id		=$this->input->post('shift_id');
		$employee_id	=$this->input->post('employee_id');
		$from_date   	=$this->formatDate($this->input->post('od_from'));
		$to_date   		=$this->formatDate($this->input->post('od_to'));
		$in_timeArr = explode(":",$this->input->post('in_time'));
		if(count($in_timeArr)==2){
		$in_time		=$this->input->post('in_time').":00";
		}else{
		$in_time		=$this->input->post('in_time');    
		}
		$out_timeArr     = explode(":",$this->input->post('out_time'));
		if(count($out_timeArr)==2){
		$out_time		=$this->input->post('out_time').":00";
		}else{
		$out_time		=$this->input->post('out_time');    
		}
		$remarks		=str_replace("U 0026", '&', $this->input->post('remarks'));		
		$od_id		    =$this->input->post('od_id');
		$day_type       =6; //6=OD
		$created_by		=$this->session->userdata('created_by');
		
		if($employee_id>0){
		  $ESQL = "SELECT company_id,branch_id,department_id,section_id,shift_id FROM ".EMPLOYEE_TBL." WHERE employee_id='".$employee_id."' AND status =1";
		  $ERES = $this->db->query($ESQL);
		  if($ERES->num_rows() >0){
		      $company_id     = $ERES->row()->company_id; 
		      $branch_id      = $ERES->row()->branch_id;
		      $department_id  = $ERES->row()->department_id;
		      $shift_id       = $ERES->row()->shift_id;
		  }
		}
        		
		if($od_id==''){						
			$data = array(
			'company_id'    		=>$company_id,
			'branch_id'    			=>$branch_id,
			'department_id'    		=>$department_id,	
			'section_id'    		=>$section_id,
			'session_id'    		=>$session_id,
			'shift_id'    		    =>$shift_id,
			'employee_id'    		=>$employee_id,
			'from_date'    			=>$from_date,	
			'to_date'    			=>$to_date,
			'in_time'			    =>$in_time,
			'out_time'			    =>$out_time,
			'remarks'			    =>$remarks,
			'created_by'     		=>$created_by
			);
			//=== Remove empty field ====
			//$data = array_filter($data);
			//=== Remove unexpected field by value (e.g value) ====
			//if (($key = array_search('value', $data)) !== false) { unset($data[$key]);}						
			$this->db->insert(OUTSATTION_TBL, $data); //print  $this->db->last_query(); 
			$od_id  = $this->db->insert_id();
			//===== Start OD Attendance =======
			if($od_id >0){//===== Get Shift ID =======
			    if($shift_id==0){
        		   $shift_id =$this->session->userdata('default_shift');
			    }
        		if(($in_time=="" || $out_time=="") && $shift_id >0){
        		  $TSQL = "SELECT * FROM ".SHIFT_TBL." WHERE shift_id=".$shift_id." AND institute_id=".$company_id." AND status =1";
        		  $TRES = $this->db->query($TSQL);
        		  if($TRES->num_rows() >0){$in_time = $TRES->row()->shift_start; $out_time = $TRES->row()->shift_end;}else{$in_time=date("H:i:s");}
        		}
        		
        		$MSQL   ="SELECT * FROM ".MONTH_DAYS_TBL." WHERE `date_field` BETWEEN '$from_date' AND '$to_date'"; 
			    $mquery = $this->db->query($MSQL);
			    if($mquery->num_rows() >0){
			     foreach($mquery->result() as $mrow){
			        $attendance_date    = $mrow->date_field; 
			        if($mrow->day_name=="Friday" || $mrow->is_holiday==1){
			          $present = 0;
			        }else{
			          $present = 1;
			        }		
            		$IntimeArr 			= explode(":",$in_time);
            		$OuttimeArr 		= explode(":",$out_time);
            		$check_in_datetime  = $attendance_date." ".$in_time;
            		$check_out_datetime = $attendance_date." ".$out_time;
            		$raw_in_time 		= (($IntimeArr[0] * 3600) + ($IntimeArr[1] * 60) + $IntimeArr[2]);
            		$raw_out_time 		= (($OuttimeArr[0] * 3600) + ($OuttimeArr[1] * 60) + $OuttimeArr[2]);
            		$date1 = new DateTime($check_in_datetime);
            		$date2 = new DateTime($check_out_datetime);
            
            		$diff       = $date2->diff($date1);
            		$hours      = $diff->h;
            		$total_hour = $hours + ($diff->days*24);
            		
            		$ATSQL = "SELECT id, attendance_date FROM ".ATTENDANCE_TBL." WHERE account_id='".$employee_id."' AND attendance_date ='".$attendance_date."'";
        		    $ATSQL = $this->db->query($ATSQL);
        		    if($ATSQL->num_rows()==0){
            			$data = array(
                        "institute_id"		=>$company_id,
                        "branch_id"   		=>$branch_id,
                        "department_id"    	=>$department_id,
                        "session_id"   		=>$session_id,
                        "version_id"   		=>$od_id,
                        "shift_id"    		=>$shift_id,
                        "section_id"    	=>$section_id,
                        "account_id"    	=>$employee_id,
                        "attendance_date"   =>$attendance_date,
                        "check_in_datetime" =>$check_in_datetime,
                        "check_out_datetime"=>$check_out_datetime,
                        "raw_in_time" 		=>$raw_in_time,
                        "raw_out_time" 		=>$raw_out_time,
                        "in_time"    		=>$in_time,
                        "out_time"    		=>$out_time,
                        "day_type"    	    =>$day_type,
                        "total_hour"    	=>$total_hour,
                        "inout_status"    	=>1,
                        "present"    		=>$present,
                        "remarks"    		=>$remarks,
        				"created_by" 		=>$created_by
        			    );
        			    $this->db->insert(ATTENDANCE_TBL, $data); 
        		    }elseif($ATSQL->num_rows()==1){
        		        $id = $ATSQL->row()->id;
            			$data = array(
                        "branch_id"   		=>$branch_id,
                        "department_id"    	=>$department_id,
                        "session_id"   		=>$session_id,
                        "version_id"   		=>$od_id,
                        "shift_id"    		=>$shift_id,
                        "section_id"    	=>$section_id,
                        "account_id"    	=>$employee_id,
                        "attendance_date"   =>$attendance_date,
                        "check_in_datetime" =>$check_in_datetime,
                        "check_out_datetime"=>$check_out_datetime,
                        "raw_in_time" 		=>$raw_in_time,
                        "raw_out_time" 		=>$raw_out_time,
                        "in_time"    		=>$in_time,
                        "out_time"    		=>$out_time,
                        "day_type"    	    =>$day_type,
                        "total_hour"    	=>$total_hour,
                        "inout_status"    	=>1,
                        "present"    		=>$present,
                        "remarks"    		=>$remarks,
        				"created_by" 		=>$created_by
        			    );
            			$this->db->where('account_id',$employee_id);
            			$this->db->where('id',$id);
            			$this->db->update(ATTENDANCE_TBL, $data);  
        		    }elseif($ATSQL->num_rows() >1){
        		        $this->db->where('account_id',$employee_id);
        		        $this->db->where('attendance_date',$attendance_date);
		                $this->db->delete(ATTENDANCE_TBL);
		                
		                $data = array(
                        "institute_id"		=>$company_id,
                        "branch_id"   		=>$branch_id,
                        "department_id"    	=>$department_id,
                        "session_id"   		=>$session_id,
                        "version_id"   		=>$od_id,
                        "shift_id"    		=>$shift_id,
                        "section_id"    	=>$section_id,
                        "account_id"    	=>$employee_id,
                        "attendance_date"   =>$attendance_date,
                        "check_in_datetime" =>$check_in_datetime,
                        "check_out_datetime"=>$check_out_datetime,
                        "raw_in_time" 		=>$raw_in_time,
                        "raw_out_time" 		=>$raw_out_time,
                        "in_time"    		=>$in_time,
                        "out_time"    		=>$out_time,
                        "day_type"    	    =>$day_type,
                        "total_hour"    	=>$total_hour,
                        "inout_status"    	=>1,
                        "present"    		=>$present,
                        "remarks"    		=>$remarks,
        				"created_by" 		=>$created_by
        			    );
        			    $this->db->insert(ATTENDANCE_TBL, $data); 
        		    }
        		    
			     }//end foreach
			    }//end if mquery
			}//end if od_id
			//===== End OD Attendance =======
		}else{
			$this->EditRecord($od_id);
		}
		//print $this->db->last_query();
   	}
	function EditRecord($od_id){
		$company_id	    =$this->input->post('company_id');
		$branch_id		=$this->input->post('branch_id');
		$department_id	=$this->input->post('department_id');
		$section_id		=$this->input->post('section_id');
		$session_id		=$this->input->post('session_id');
		$shift_id		=$this->input->post('shift_id');
		$employee_id	=$this->input->post('employee_id');
		$from_date   	=$this->formatDate($this->input->post('od_from'));
		$to_date   		=$this->formatDate($this->input->post('od_to'));
		$in_timeArr     = explode(":",$this->input->post('in_time'));
		if(count($in_timeArr)==2){
		$in_time		=$this->input->post('in_time').":00";
		}else{
		$in_time		=$this->input->post('in_time');    
		}
		$out_timeArr     = explode(":",$this->input->post('out_time'));
		if(count($out_timeArr)==2){
		$out_time		=$this->input->post('out_time').":00";
		}else{
		$out_time		=$this->input->post('out_time');    
		}
		$remarks		=str_replace("U 0026", '&', $this->input->post('remarks'));
        $day_type       =6; // 6=OD
		$od_id		    =$this->input->post('od_id');
		$created_by		=$this->session->userdata('created_by');
		$modified_by	=$this->session->userdata('created_by');
		$modified_time  =date("Y-m-d H:i:s");
		
		if($employee_id>0){
		  $ESQL = "SELECT company_id,branch_id,department_id,section_id,shift_id FROM ".EMPLOYEE_TBL." WHERE employee_id='".$employee_id."' AND status =1";
		  $ERES = $this->db->query($ESQL);
		  if($ERES->num_rows() >0){
		      $company_id     = $ERES->row()->company_id; 
		      $branch_id      = $ERES->row()->branch_id;
		      $department_id  = $ERES->row()->department_id;
		      $shift_id       = $ERES->row()->shift_id;
		  }
		}
		
		$data = array(
			'company_id'    		=>$company_id,
			'branch_id'    			=>$branch_id,
			'department_id'    		=>$department_id,	
			'section_id'    		=>$section_id,
			'session_id'    		=>$session_id,
			'shift_id'    		    =>$shift_id,
			'employee_id'    		=>$employee_id,
			'from_date'    			=>$from_date,	
			'to_date'    			=>$to_date,
			'in_time'			    =>$in_time,
			'out_time'			    =>$out_time,
			'remarks'			    =>$remarks,
			'modified_by'     	    =>$modified_by,
			'modified_time'     	=>$modified_time
		);
		$this->db->where('od_id',$od_id);
		$this->db->update(OUTSATTION_TBL, $data); 
		//===== Start OD Attendance =======
		if($od_id >0){//===== Get Shift ID =======
    		if($shift_id==0){
        		   $shift_id =$this->session->userdata('default_shift');
			}
    		if(($in_time=="" || $out_time=="") && $shift_id>0){
    		  $TSQL = "SELECT * FROM ".SHIFT_TBL." WHERE shift_id=".$shift_id." AND institute_id=".$company_id." AND status =1";
    		  $TRES = $this->db->query($TSQL);
    		  if($TRES->num_rows() >0){$in_time = $TRES->row()->shift_start; $out_time = $TRES->row()->shift_end;}else{$in_time=date("H:i:s");}
    		}
    		
    		$MSQL   ="SELECT * FROM ".MONTH_DAYS_TBL." WHERE `date_field` BETWEEN '$from_date' AND '$to_date'"; 
		    $mquery = $this->db->query($MSQL);
		    if($mquery->num_rows() >0){
		     foreach($mquery->result() as $mrow){
		        $attendance_date    = $mrow->date_field; 
		        if($mrow->day_name=="Friday" || $mrow->is_holiday==1){
		          $present = 0;
		        }else{
		          $present = 1;
		        }
        		$check_in_datetime  = $attendance_date." ".$in_time;
        		$check_out_datetime = $attendance_date." ".$out_time;		
        		$IntimeArr 			= explode(":",$in_time);
        		$raw_in_time 		= (($IntimeArr[0] * 3600) + ($IntimeArr[1] * 60) + $IntimeArr[2]);
        		$OuttimeArr 		= explode(":",$out_time);
        		$raw_out_time 		= (($OuttimeArr[0] * 3600) + ($OuttimeArr[1] * 60) + $OuttimeArr[2]);
        		$date1 = new DateTime($check_in_datetime);
        		$date2 = new DateTime($check_out_datetime);
        
        		$diff       = $date2->diff($date1);
        		$hours      = $diff->h;
        		$total_hour = $hours + ($diff->days*24);
        		$ATSQL = "SELECT id, attendance_date FROM ".ATTENDANCE_TBL." WHERE account_id='".$employee_id."' AND attendance_date ='".$attendance_date."'";
    		    $ATSQL = $this->db->query($ATSQL);
    		    if($ATSQL->num_rows()==0){
        			$data = array(
                    "institute_id"		=>$company_id,
                    "branch_id"   		=>$branch_id,
                    "department_id"    	=>$department_id,
                    "session_id"   		=>$session_id,
                    "version_id"   		=>$od_id,
                    "shift_id"    		=>$shift_id,
                    "section_id"    	=>$section_id,
                    "account_id"    	=>$employee_id,
                    "attendance_date"   =>$attendance_date,
                    "check_in_datetime" =>$check_in_datetime,
                    "check_out_datetime"=>$check_out_datetime,
                    "raw_in_time" 		=>$raw_in_time,
                    "raw_out_time" 		=>$raw_out_time,
                    "in_time"    		=>$in_time,
                    "out_time"    		=>$out_time,
                    "day_type"    	    =>$day_type,
                    "total_hour"    	=>$total_hour,
                    "inout_status"    	=>1,
                    "present"    		=>$present,
                    "remarks"    		=>$remarks,
    				"created_by" 		=>$created_by,
    				'modified_by' 		=>$modified_by,
    				'modified_time' 	=>$modified_time
    			    );
    			    $this->db->insert(ATTENDANCE_TBL, $data); 
    		    }elseif($ATSQL->num_rows()==1){
    		        $id = $ATSQL->row()->id;
        			$data = array(
                    "branch_id"   		=>$branch_id,
                    "department_id"    	=>$department_id,
                    "session_id"   		=>$session_id,
                    "version_id"   		=>$od_id,
                    "shift_id"    		=>$shift_id,
                    "section_id"    	=>$section_id,
                    "account_id"    	=>$employee_id,
                    "attendance_date"   =>$attendance_date,
                    "check_in_datetime" =>$check_in_datetime,
                    "check_out_datetime"=>$check_out_datetime,
                    "raw_in_time" 		=>$raw_in_time,
                    "raw_out_time" 		=>$raw_out_time,
                    "in_time"    		=>$in_time,
                    "out_time"    		=>$out_time,
                    "day_type"    	    =>$day_type,
                    "total_hour"    	=>$total_hour,
                    "inout_status"    	=>1,
                    "present"    		=>$present,
                    "remarks"    		=>$remarks,
    				"created_by" 		=>$created_by,
    				'modified_by' 		=>$modified_by,
    				'modified_time' 	=>$modified_time
    			    );
        			$this->db->where('account_id',$employee_id);
        			$this->db->where('id',$id);
        			$this->db->update(ATTENDANCE_TBL, $data);  
    		    }elseif($ATSQL->num_rows() >1){
    		        $this->db->where('account_id',$employee_id);
    		        $this->db->where('attendance_date',$attendance_date);
	                $this->db->delete(ATTENDANCE_TBL);
	                
	                $data = array(
                    "institute_id"		=>$company_id,
                    "branch_id"   		=>$branch_id,
                    "department_id"    	=>$department_id,
                    "session_id"   		=>$session_id,
                    "version_id"   		=>$od_id,
                    "shift_id"    		=>$shift_id,
                    "section_id"    	=>$section_id,
                    "account_id"    	=>$employee_id,
                    "attendance_date"   =>$attendance_date,
                    "check_in_datetime" =>$check_in_datetime,
                    "check_out_datetime"=>$check_out_datetime,
                    "raw_in_time" 		=>$raw_in_time,
                    "raw_out_time" 		=>$raw_out_time,
                    "in_time"    		=>$in_time,
                    "out_time"    		=>$out_time,
                    "day_type"    	    =>$day_type,
                    "total_hour"    	=>$total_hour,
                    "inout_status"    	=>1,
                    "present"    		=>$present,
                    "remarks"    		=>$remarks,
    				"created_by" 		=>$created_by,
    				'modified_by' 		=>$modified_by,
    				'modified_time' 	=>$modified_time
    			    );
    			    $this->db->insert(ATTENDANCE_TBL, $data); 
    		    }
        		    
		     }//end foreach
		    }//end if mquery
		}//end if od_id
		//===== End OD Attendance =======
    }
	
	function DelRecord(){
		$od_id = $this->input->post('id');		
		$this->db->select('*');
		$this->db->from(OUTSATTION_TBL);
		$this->db->where('od_id', $od_id);
		$query = $this->db->get();
		$SQLM="DELETE FROM ".ATTENDANCE_TBL." WHERE version_id = $od_id AND day_type=6 AND `attendance_date` BETWEEN '$query->row()->from_date' AND '$query->row()->to_date'"; 
		$this->db->query($SQLM);
			
		$this->db->where('od_id',$od_id);
		$this->db->delete(OUTSATTION_TBL);
	}
	
	function FillRecord(){
        $od_id	=$this->input->post('id');
		$this->db->select('*');
		$this->db->from(OUTSATTION_TBL);
		$this->db->where('od_id', $od_id);
		$query = $this->db->get();
		$DASQL="DELETE FROM ".ATTENDANCE_TBL." WHERE version_id = $od_id AND day_type=6 AND `attendance_date` BETWEEN '".$query->row()->from_date."' AND '".$query->row()->to_date."'"; 
		$this->db->query($DASQL);
		return $query->row();
	}
   	//============== Category Retrive by Ajax================
   	function GetRecordGrid(){
		$menu_slug= $this->uri->segment(1);
		$this->load->model('Site_model');
		$hasEditPM = $this->Site_model->hasOptionPermission($menu_slug,"Edit");
		$hasDelPM  = $this->Site_model->hasOptionPermission($menu_slug,"Delete");
		$hasPrintPM= $this->Site_model->hasOptionPermission($menu_slug,"Print");
	   	$employeeId= $this->input->post('src-employee-id');
	   	$srcFrom   = $this->formatDate($this->input->post('srcFrom'));
	   	$srcTo 	   = $this->formatDate($this->input->post('srcTo'));
	   	
	   	$from 	   = $this->input->post('from');
		$to	  =$this->input->post('to');
		if($from==""){ $from=0;} if($to==""){ $to=50;}
		$this->db->select('h.od_id,DATE_FORMAT(h.from_date ,"%d-%m-%Y") as date_from,DATE_FORMAT(h.to_date ,"%d-%m-%Y") as date_to,DATEDIFF(h.to_date,h.from_date)+1 as total_days,c.company_name,b.branch_name,b.branch_code,d.department_name,e.card_id,e.employee_name,e.designation');
		$this->db->from(OUTSATTION_TBL." AS h");
		$this->db->join(COMPANY_SETTINGS_TBL.' AS c', 'c.company_id=h.company_id','LEFT');
	  	$this->db->join(BRANCH_TBL.' AS b', 'b.branch_id=h.branch_id','LEFT');
	  	$this->db->join(DEPARTMENT_TBL.' AS d', 'd.department_id=h.department_id','LEFT');
	  	$this->db->join(EMPLOYEE_TBL.' AS e', 'e.employee_id=h.employee_id','LEFT');
	  	
		if($employeeId >0){
			  $this->db->where("h.employee_id", $employeeId);  	
		}
		if($srcFrom!="" && $srcTo==""){
		   $this->db->where("h.from_date >=", $srcFrom);
		}elseif($srcFrom=="" && $srcTo!=""){
		   $this->db->where("h.from_date <=", $srcTo);
		}elseif($srcFrom!="" && $srcTo!=""){
		   $this->db->where("h.from_date BETWEEN '$srcFrom' AND '$srcTo'",NULL,FALSE);
		}
		
		$this->db->group_by('h.od_id');
		$this->db->order_by('h.from_date','DESC');
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
				<th width="23%">'.$this->lang->line("company").' '.$this->lang->line("details").'</th>
			  	<th width="22%">'.$this->lang->line("employee").' '.$this->lang->line("details").'</th>
				<th width="12%">'.$this->lang->line("department").'</th>
				<th width="11%">'.$this->lang->line("from_date").'</th>
				<th width="11%">'.$this->lang->line("to_date").'</th>
				<th width="11%">'.$this->lang->line("total")." ".$this->lang->line("od").'</th>
				<th width="8%" class="text-center">'.$this->lang->line("options").'</th>
			  </tr>
		</thead>';
		  $i=1;
		  foreach($query->result() as $row){
		    echo "<tr class='default'>
		  	<td>".$i."</td>
			<td>".$row->company_name.",<br>".$row->branch_name."</td>
		  	<td>".$row->employee_name."<br> ".$row->designation."</td>
			<td>".$row->department_name."</td>
			<td>".$row->date_from."</td>
			<td>".$row->date_to."</td>
			<td>".$row->total_days."</td>
			<td class='text-center align-middle'>";
			if($hasEditPM){
			echo "<span data-toggle='tooltip' data-placement='top' data-original-title='Edit'><a class='btn btn-info btn-xs' onclick=editRecord('".$row->od_id."') id='".$row->od_id."' href='#'><i class='fas fa-edit'></i></a></span>&nbsp;";
			}
			if($hasDelPM){
			echo "<span data-toggle='tooltip' data-placement='top' data-original-title='Delete'><a class='btn btn-danger btn-xs' data-toggle='modal' onclick=deleteRecord('".$row->od_id."') id='".$row->od_id."' data-target='#deleteModal'><i class='fa fa-trash'></i></a></span>";
			}
			if($hasPrintPM){
			    echo "<div class='clearfix'></div><br><span data-toggle='tooltip' data-original-title='View Details'><a class='btn btn-success btn-sm' href='".base_url()."outstationduty/ViewDetails/".$row->od_id."'><i class='fa fa-print'></i> ".$this->lang->line("od")." ".$this->lang->line("details")."</a></span>";
			
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
		$this->db->select('h.od_id');
		$this->db->from(OUTSATTION_TBL." AS h");
		$this->db->join(COMPANY_SETTINGS_TBL.' AS c', 'c.company_id=h.company_id','LEFT');
	  	$this->db->join(BRANCH_TBL.' AS b', 'b.branch_id=h.branch_id','LEFT');
	  	$this->db->join(DEPARTMENT_TBL.' AS d', 'd.department_id=h.department_id','LEFT');
	  	$this->db->join(EMPLOYEE_TBL.' AS e', 'e.employee_id=h.employee_id','LEFT');
	  	
		if($employeeId >0){
			  $this->db->where("h.employee_id", $employeeId);  	
		}
		if($srcFrom!="" && $srcTo==""){
		   $this->db->where("h.from_date >=", $srcFrom);
		}elseif($srcFrom=="" && $srcTo!=""){
		   $this->db->where("h.from_date <=", $srcTo);
		}elseif($srcFrom!="" && $srcTo!=""){
		   $this->db->where("h.from_date BETWEEN '$srcFrom' AND '$srcTo'",NULL,FALSE);
		}
		
		$this->db->group_by('h.od_id');
		$this->db->order_by('h.from_date','DESC');
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
