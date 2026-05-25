<?php 
class Attendance_model extends CI_Model {
		
	function __construct()
	{
		parent::__construct();
	}	
	
	function getClassName($class_id,$institute_id){
		$this->db->select('class_name');
		$this->db->from(CLASS_TBL);
		$this->db->where('class_id', $class_id);
		$this->db->where('institute_id', $institute_id);
		$cquery = $this->db->get();
		return $cquery->row()->class_name;
	}
	   	
	function UploadDataFile($fineName){
		$file_name=''; $saveDir = ASSETS.'/data/';
		$config['file_name']		= $fineName;
		$config['overwrite']		= TRUE;
		$config['upload_path'] 		= ASSETS.'/data/';
		$config['allowed_types'] 	= 'txt|csv';
		$config['max_size'] 		= '144400';
		$config['maintain_ratio']  	= FALSE;
		$this->load->library("upload",$config);
		//print_r($_FILES);
		foreach($_FILES as $field => $file){
			// No problems with the file
			if($file['error'] == 0){ //print_r($file);
				// So lets upload 
				if ($this->upload->do_upload($field)){ 
					$data =  $this->upload->data();					
					$file_name = $data['orig_name'];				
					return trim("data/".$file_name);
					                    
				}else{
					echo $errors = $this->upload->display_errors();
					return false;
				}
			}
		}// end foreach		
	}
	function ProcessData($fineName){
		$institute_id	    =$this->input->post('company-id');
		$session_id			=$this->input->post('session-id');
		$shift_id	    	=$this->input->post('shift-id');
		$from_date			=$this->formatDate($this->input->post('date-from'));
		$to_date			=$this->formatDate($this->input->post('date-to'));
		$lineArr = array(); $inoutHour=0; $inoutMM=0; $inoutSS=0; $version_id=0;
        if(empty($institute_id)){$institute_id = $this->session->userdata('company_id');}
		$APMP="AM"; $sl=0;
		if($institute_id >0 && $fineName !=""){ 	 
			$handle = fopen(ASSETS."/data/$fineName.txt", "r");
			if ($handle) {
				while (($line = fgets($handle)) !== false) {
				    if($sl >0){
				    //$lineArr  = explode(" ",$line); // Old Devise
				    $lineArr  = explode(",",$line); // New Devise
				    //print_r($lineArr); 
					$lineArr = array_filter($lineArr); 
                    //print_r($lineArr); 
					$card_id 			= trim($lineArr[0]);
					//echo $lineArr[0]." ".$lineArr[3]." ".trim($lineArr[4]); echo "<hr>"; //exit;
					$inoutDateArr 		= explode("-", trim($lineArr[3]));
					$inoutTimeArr 		= explode(":", trim($lineArr[4]));
					$data_source 		= trim($lineArr[7]);
					
					$inout_date         = trim($lineArr[3]);
					
			        $inoutHour 		    = trim($inoutTimeArr[0]);
			        $inoutMM 			= trim($inoutTimeArr[1]);
			        $inoutSS 			= "00";
					if($inoutSS==""){$inoutSS="00";}
					
					$inout_datetime 	= $inout_date." ".$inoutHour.":".$inoutMM.":".$inoutSS;
					
					$check_in_date 		= $inout_date;	
					$check_intime 		= $inoutHour.":".$inoutMM.":".$inoutSS;
					$SaveTSql	="INSERT INTO temp_upload (card_id,inout_time,check_indate,check_intime,source) 
					VALUES('$card_id','$inout_datetime','$check_in_date','$check_intime','$data_source')";						
					$this->db->query($SaveTSql);
				    } // end if sl
				    $sl++;
				}//end while				
				fclose($handle);
			} else {
				echo "Error opening the file.";
			}
			
			$tmpsql="SELECT * FROM temp_upload WHERE card_id != '' AND check_indate BETWEEN '$from_date' AND '$to_date' ORDER BY inout_time,card_id ASC"; 
			$tquery = $this->db->query($tmpsql);				
			if($tquery->num_rows() >0){	
			    foreach($tquery->result() as $row){
			        $card_id 			= $row->card_id;
			        $check_in_date 		= $row->check_indate;
			        $inout_datetime 	= $row->inout_time;
			        $check_intime 	    = $row->check_intime;
			        $data_source 	    = $row->source;
					$inoutTimeArr 		= explode(":", trim($row->check_intime));
					
					$inoutHour 		    = trim($inoutTimeArr[0]);
			        $inoutMM 			= trim($inoutTimeArr[1]);
			        $inoutSS 			= trim($inoutTimeArr[2]);
					
					$start_from =-1;
					$FSQL = "SELECT DATEDIFF('".$check_in_date."', '".$from_date."') AS start_from";
        			$FRES = $this->db->query($FSQL);
        			if($FRES->num_rows() >0){
        			    $start_from = $FRES->row()->start_from;
        			}
			    
			        $end_to =-1;
					$ESQL = "SELECT DATEDIFF('".$to_date."', '".$check_in_date."') AS end_to";
        			$ERES = $this->db->query($ESQL);
        			if($ERES->num_rows() >0){
        			    $end_to = $ERES->row()->end_to;
        			}
			    
					//===== Start =====
					if($check_in_date !="" && $start_from >= 0 && $end_to >= 0 && $card_id !="0"){
						    
				        $psql="SELECT employee_id as person_id,shift_id,company_id,branch_id,department_id,section_id FROM ".EMPLOYEE_TBL." WHERE card_id = '$card_id'"; 
						$pquery = $this->db->query($psql);				
						if($pquery->num_rows() >0){	
							$row 				= $pquery->row();				   
							$employee_id 		= $row->person_id;
							$shift_id 			= $row->shift_id;
							$institute_id 		= $row->company_id;
							$branch_id 			= $row->branch_id;
							$department_id 		= $row->department_id;
							$session_id 		= $this->input->post('session-id');
							$class_id 			= 0;
							$group_id 			= 0;
							$section_id 		= $row->section_id;
						}else{				   
							$employee_id 		= 0;
							$shift_id 			= 0;
							$institute_id 		= 0;
							$branch_id 			= 0;
							$department_id 		= 0;
							$session_id 		= 0;
							$class_id 			= 0;
							$group_id 			= 0;
							$section_id 		= 0;
						}
						
						if(empty($shift_id)){
						$shift_id =$this->session->userdata('default_shift');
						}
						if(trim($shift_id)==""){
						$shift_id =1;
						}
						
						$sfsql		="SELECT * FROM ".SHIFT_TBL." WHERE shift_id = $shift_id";						
						$sfquery 	= $this->db->query($sfsql);
						$shift_start= $sfquery->row()->shift_start;
						$shift_end  = $sfquery->row()->shift_end;
						$shiftInTimeArr 	= explode(":", $shift_start);
						$shiftInHour 		= $shiftInTimeArr[0];
						$shiftInMM 			= $shiftInTimeArr[1];
						$shiftInSec			= (($shiftInHour * 3600) + ($shiftInMM * 60) + $shiftInTimeArr[2]);						
						
						$shiftOutTimeArr 	= explode(":", $shift_end);
						$shiftOutHour 		= $shiftOutTimeArr[0];
						$shiftOutMM 		= $shiftOutTimeArr[1];
						$shiftOutSec		= (($shiftOutHour * 3600) + ($shiftOutMM * 60) + $shiftOutTimeArr[2]);
						$shiftTotalHr 		= intval((($shiftOutSec-$shiftInSec)/3600));
						date_default_timezone_set('Asia/Dhaka');
						//$check_in_date = date("Y-m-d"); 
					
						$cHr = $inoutHour; $early_leave=0; $after_leave=0; $before_leave=0;
						
						if($shift_id < 15 && $employee_id>0){ // Morning to Evening
							//if(($cHr >= ($shiftInHour-2)) && $cHr <= $shiftInHour){								
								$asql	= "SELECT * FROM ".ATTENDANCE_TBL." WHERE account_id  = $employee_id AND total_hour <= 12 AND attendance_date='$check_in_date' ORDER BY `id` DESC LIMIT 0 , 1";						
								$aquery = $this->db->query($asql);
								$num 	= $aquery->num_rows();						
								$grace_intime   = 0; // 30 M= 1800 Sec
								if( $num ==0){			
									$in1hr		= $inoutHour;
									$InhrSec 	= ($in1hr * 3600);
									$in1min 	= $inoutMM;
									$InMinSec 	= ($in1min * 60);
									$CurrentInTotalSec = ($InhrSec+$InMinSec+$inoutSS);
									$check_in_datetime =  $inout_datetime;			
												
									$gsql		="SELECT DATE( DATE_ADD( '$check_in_datetime', INTERVAL $shiftTotalHr HOUR ) ) AS out_date, TIME( DATE_ADD( '$check_in_datetime', INTERVAL $shiftTotalHr HOUR ) ) AS out_time";						
									$gquery 	= $this->db->query($gsql);
									$grow		= $gquery->row();
									//$outtimeArr = explode(":",$grow->out_time); //for ATN
									$outtimeArr = explode(":",$shift_end);
									$check_out_date =$grow->out_date;
									
									$outhrSec 	= ($outtimeArr[0] * 3600);
									$outminSec 	= ($outtimeArr[1] * 60);
									//$schedule_out_time = $grow->out_date." ".$grow->out_time; //for ATN
									$schedule_out_time = $grow->out_date." ".$shift_end;
									$inout_status = 1; $present=1;
									$employee_intime = ($CurrentInTotalSec - $grace_intime);  
									if($employee_intime > $shiftInSec){$late=1;}else{$late=0;}
									if($late==1){
									    $ELSQL = "SELECT leave_type FROM ".LEAVE_TBL." WHERE employee_id=".$employee_id." AND leave_from='$check_in_date' AND leave_type IN (2)";
                            			$ELRES = $this->db->query($ELSQL);
                            			if($ELRES->num_rows() >0){
                            			   $late=0;
                            			}
									}
									$SaveSql	="INSERT INTO ".ATTENDANCE_TBL." (institute_id,branch_id,department_id,session_id,version_id,shift_id,class_id,group_id,section_id,account_id,attendance_date,check_in_datetime,schedule_out_date,schedule_out_time,raw_in_time,in_time,late,inout_status,present,data_source) 
									VALUES($institute_id,$branch_id,$department_id,$session_id,$version_id,$shift_id,$class_id,$group_id,$section_id,$employee_id,'$check_in_date','$check_in_datetime','$check_out_date','$schedule_out_time',$CurrentInTotalSec,'$check_intime','$late',$inout_status,$present,'$data_source')";						
									$sres	= $this->db->query($SaveSql);
												
								}elseif( $num ==1){	
									$arow		= $aquery->row();	
									$inout_sl	= $arow->id;
									$OutHrSec 	= ($inoutHour * 3600);
									$OutMinSec 	= ($inoutMM * 60);
									$CurrentOutTotalSec = ($OutHrSec+$OutMinSec+$inoutSS);
									$CheckOutTime =  $inout_datetime; $out_time=$check_intime;
									$total_hour =0;
									if($arow->inout_status==1){
										$inout_status =0;
									}else{
										$inout_status =1;				
									}
									$HSQL 		="SELECT TIME_TO_SEC( TIMEDIFF( '$CheckOutTime', '$arow->check_in_datetime' ) ) /3600 AS total_hour";
									$hquery 	= $this->db->query($HSQL);
									$hrow		= $hquery->row();
									$total_hour = $hrow->total_hour;
									
									$scheduleOutArr     = explode(" ",$arow->schedule_out_time);
									$scheduleOutTimeArr = explode(":",$scheduleOutArr[1]);
									$scheduleOutHour 	= $scheduleOutTimeArr[0];
									$scheduleOutMM 		= $scheduleOutTimeArr[1];
									$scheduleOutTime	= (($scheduleOutHour * 3600) + ($scheduleOutMM * 60) + $scheduleOutTimeArr[2]);
									$grace_hr = 0; $TotalHourWithGrace = $total_hour+$grace_hr;
									if(($scheduleOutTime <= ($CurrentOutTotalSec)) && (intval($TotalHourWithGrace) >=$shiftTotalHr)){ 
										$early_leave  = 0; //echo "EMP $employee_id >> In: Out 0:: $scheduleOutTime = $CurrentOutTotalSec & HR $TotalHourWithGrace/$shiftTotalHr<br>";
										$after_leave  = ($CurrentOutTotalSec - $scheduleOutTime);
										$before_leave = 0;
									}elseif(($scheduleOutTime > ($CurrentOutTotalSec)) && (intval($TotalHourWithGrace) < $shiftTotalHr) && (intval($total_hour) >0) ){
										$early_leave  = 1; // $early_leave  = 1;
										$after_leave  = 0; 
										$before_leave = (($shiftTotalHr - $total_hour) * 3600); 
									}elseif(($scheduleOutTime > ($CurrentOutTotalSec)) && (intval($TotalHourWithGrace) < $shiftTotalHr) && (intval($total_hour) >0) ){
										$early_leave  = 1; // $early_leave  = 1;
										$after_leave  = 0;
										$before_leave = (($shiftTotalHr - $total_hour) * 3600); 
									}elseif((intval($TotalHourWithGrace) > $shiftTotalHr ) && (intval($TotalHourWithGrace) >=9)){
										$early_leave  = 0; 
										$after_leave  = (($TotalHourWithGrace-$shiftTotalHr) * 3600); 
										$before_leave = 0;
									}else{ 
									    if(($scheduleOutTime > ($CurrentOutTotalSec)) && ($TotalHourWithGrace < $shiftTotalHr)){
									    $early_leave  = 1; // $early_leave  = 1;
										$after_leave  = 0; 
										$before_leave = (($shiftTotalHr - $total_hour) * 3600); 
									    } 
									}
									
									if($after_leave < 0){$after_leave=0;}
									
									if($early_leave==1){
									   
									    $slsql="SELECT total_days FROM ".LEAVE_TBL." WHERE employee_id=$employee_id AND leave_from = '$check_in_date' AND leave_type = 2"; 
                						$slquery = $this->db->query($slsql);				
                						if($slquery->num_rows() >0){	
                						   $early_leave=1; 
                						}else{
									       $elsql="SELECT total_days FROM ".LEAVE_TBL." WHERE employee_id=$employee_id AND leave_from = '$check_in_date' AND leave_type = 3"; 
                						   $elquery = $this->db->query($elsql);				
                						   if($elquery->num_rows() >0){	
                						     $early_leave=0; 
                						   }else{
                						      $early_leave=1;  
                						   }
                						}
									}									
									$SaveSql="UPDATE ".ATTENDANCE_TBL." SET check_out_datetime ='$CheckOutTime',raw_out_time = '$CurrentOutTotalSec',out_time='$out_time',early_leave=$early_leave,total_hour='$total_hour', after_leave=$after_leave, before_leave=$before_leave, inout_status = $inout_status WHERE id = $inout_sl AND account_id=$employee_id";						
									$this->db->query($SaveSql); 
								}
								
							//}
						}/*else{ // General (Incomplete)
														
							$asql	= "SELECT * FROM ".ATTENDANCE_TBL." WHERE account_id  = $employee_id AND total_hour <= 12 AND (attendance_date='$check_in_date' OR schedule_out_date = '$check_in_date') ORDER BY `id` DESC LIMIT 0 , 1";						
							$aquery = $this->db->query($asql);
							$num 	= $aquery->num_rows();						
							
							if( $num ==0){			
								$in1hr		= $inoutHour;
								$InhrSec 	= ($in1hr * 3600);
								$in1min 	= $inoutMM;
								$InMinSec 	= ($in1min * 60);
								$CurrentInTotalSec = ($InhrSec+$InMinSec+$inoutSS);
								$check_in_datetime =  $inout_datetime;			
											
								$gsql		="SELECT DATE( DATE_ADD( '$check_in_datetime', INTERVAL $shiftTotalHr HOUR ) ) AS out_date, TIME( DATE_ADD( '$check_in_datetime', INTERVAL $shiftTotalHr HOUR ) ) AS out_time";						
								$gquery 	= $this->db->query($gsql);
								$grow		= $gquery->row();
								$outtimeArr = explode(":",$grow->out_time);
								$check_out_date =$grow->out_date;
								
								$outhrSec 	= ($outtimeArr[0] * 3600);
								$outminSec 	= ($outtimeArr[1] * 60);
								$schedule_out_time = $grow->out_date." ".$grow->out_time;
								$inout_status = 1; $present=1; $late=0;
								$SaveSql	="INSERT INTO ".ATTENDANCE_TBL." (institute_id,branch_id,session_id,version_id,shift_id,class_id,group_id,section_id,account_id,attendance_date,check_in_datetime,schedule_out_date,schedule_out_time,raw_in_time,in_time,late,inout_status,present) 
								VALUES($institute_id,$branch_id,$session_id,$version_id,$shift_id,$class_id,$group_id,$section_id,$employee_id,'$check_in_date','$check_in_datetime','$check_out_date','$schedule_out_time',$CurrentInTotalSec,'$check_intime','$late',$inout_status,$present)";						
								$sres	= $this->db->query($SaveSql);
											
							}elseif( $num ==1){	
								$arow		=  $aquery->row();	
								$inout_sl	= $arow->id;
								$OutHrSec 	= ($inoutHour * 3600);
								$InMinSec 	= ($inoutMM * 60);
								$CurrentOutTotalSec = ($OutHrSec+$InMinSec+$inoutSS);
								$CheckOutTime =  $inout_datetime; $out_time=$check_intime;
								$total_hour =0;
								if($arow->inout_status==1){
									$inout_status =0;
								}else{
									$inout_status =1;				
								}								
								
								$HSQL 		="SELECT TIME_TO_SEC( TIMEDIFF( '$CheckOutTime', '$arow->check_in_datetime' ) ) /3600 AS total_hour";
								$hquery 	= $this->db->query($HSQL);
								$hrow		= $hquery->row();
								$total_hour = $hrow->total_hour;
								
								$scheduleOutArr     = explode(" ",$arow->schedule_out_time);
								$scheduleOutTimeArr = explode(":",$scheduleOutArr[1]);
								$scheduleOutHour 	= $scheduleOutTimeArr[0];
								$scheduleOutMM 		= $scheduleOutTimeArr[1];
								$scheduleOutTime	= (($scheduleOutHour * 3600) + ($scheduleOutMM * 60) + $scheduleOutTimeArr[2]);
								
								if(($scheduleOutTime <= $CurrentOutTotalSec) && (intval($total_hour) >=$shiftTotalHr)){ 
									$early_leave  = 0;
									$after_leave  = ($CurrentOutTotalSec - $scheduleOutTime);
									$before_leave = 0;
								}elseif(($scheduleOutTime <= $CurrentOutTotalSec) && (intval($total_hour) < $shiftTotalHr) && (intval($total_hour) >0) ){
									$early_leave  = 1;
									$after_leave  = 0;
									$before_leave = (($shiftTotalHr - $total_hour) * 3600); 
								}elseif(($scheduleOutTime > $CurrentOutTotalSec) && (intval($total_hour) < $shiftTotalHr) && (intval($total_hour) >0) ){
									$early_leave = 1;
									$after_leave = 0;
									$before_leave = (($shiftTotalHr - $total_hour) * 3600); 
								}elseif(intval($total_hour) > $shiftTotalHr ){
									$early_leave = 0;
									$after_leave = (($total_hour-$shiftTotalHr) * 3600); 
									$before_leave = 0;
								}
								
								$SaveSql="UPDATE ".ATTENDANCE_TBL." SET check_out_datetime ='$CheckOutTime',raw_out_time = '$CurrentOutTotalSec',out_time='$out_time',early_leave=$early_leave,total_hour='$total_hour', after_leave=$after_leave, before_leave=$before_leave, inout_status = $inout_status WHERE id = $inout_sl AND account_id=$employee_id";						
								$this->db->query($SaveSql);
							}
							
						}*/
						//==== End ======== 
					}//end if
					
				    }// end foreach
				    $tmpsql="TRUNCATE `temp_upload`"; 
    			    $this->db->query($tmpsql); 
			    }//end if num
		}//End Post
	}
	//===== Start Class Attendance =========
    function GetClassAttendanceRecordGrid(){
		$menu_slug= $this->uri->segment(1);
		$this->load->model('Site_model');		
		$hasCreatePM = $this->Site_model->hasOptionPermission($menu_slug,"Create");		
		$hasEditPM   = $this->Site_model->hasOptionPermission($menu_slug,"Edit");
        $institute_id	    =$this->input->post('company-id');
		$branch_id	    	=$this->input->post('branch-id');
		$session_id			=$this->input->post('session-id');
		$version_id			=$this->input->post('version-id');
		$class_id	    	=$this->input->post('class-id');
		$group_id	    	=$this->input->post('group-id');
		$shift_id	    	=$this->input->post('shift-id');
		$section_id	    	=$this->input->post('section-id');
		$attend_date		=$this->input->post('attendance-date');
		$attendance_date	=$this->formatDate($this->input->post('attendance-date'));
		
        if(empty($institute_id)){$institute_id = $this->session->userdata('company_id');}
        $from	=$this->input->post('from');
        $to	=$this->input->post('to');
        if($from==""){ $from=0;} if($to==""){ $to=100;}
        $this->db->select('a.admission_id,a.admission_no,s.account_name as student_name,c.class_name,g.group_name,sc.section_name,i.company_name');
        $this->db->from(ADMISSION_TBL.' AS a');
	    $this->db->join(COMPANY_SETTINGS_TBL.' AS i', 'i.company_id=a.institute_id','LEFT');
	    $this->db->join(CLASS_TBL.' AS c', 'c.class_id=a.class_id','LEFT');
	    $this->db->join(GROUPS_TBL.' AS g', 'g.group_id=a.group_id','LEFT');
	    $this->db->join(SECTION_TBL.' AS sc', 'sc.section_id=a.section_id','LEFT');
	    $this->db->join(ACC_HEAD_TBL.' AS s', 's.account_id=a.student_name_en','LEFT');
        if($institute_id >0){
            $this->db->where('i.company_id',$institute_id);
        }			
		$this->db->where('a.branch_id', $branch_id);				
		$this->db->where('a.session_id', $session_id);				
		$this->db->where('a.version_id', $version_id);				
		$this->db->where('a.class_id', $class_id);				
		$this->db->where('a.group_id', $group_id);				
		$this->db->where('a.section_id', $section_id);				
		$this->db->where('a.shift_id', $shift_id);
		$this->db->where('a.status',1);
        $this->db->group_by('a.admission_id');
        $this->db->order_by('a.admission_no','ASC');
        $this->db->limit($to,$from);
        $query = $this->db->get(); //print  $this->db->last_query();
        $totalrecord = $this->GetTotalClassAttendanceRecord();
        $perPage=100; $Pagination="";
        if($totalrecord >0){
            $Pagination = $this->getPagination($totalrecord,$perPage);
        } //print  $this->db->last_query();
        
        $grid = "<table width='100%'  border='0' class='table table-responsive table-bordered table-hover custab'>
		<thead>
			<tr class='bg-light'>
			<th width='2%' class='text-left'>".$this->lang->line("sl")."</th>
			<th width='30%' class='text-left'>".$this->lang->line("student_name")."</th>
			<th width='14%' class='text-left'>".$this->lang->line("admission_id")."</th>
			<th width='14%' class='text-left'>".$this->lang->line("class_name")."</th>
			<th width='15%' class='text-left'>".$this->lang->line("group_name")."</th>
			<th width='15%' class='text-left'>".$this->lang->line("section_name")."</th>
			<th width='10%' class='text-center'>".$this->lang->line("is_present")."</th>
			</tr>
		</thead>";
        $i=1; 
        foreach($query->result() as $row){
            $admission_id 	= $row->admission_id;			
            $grid.="<tr class='default'>
			<td>".$i."</td>
			<td>".$row->student_name."</td>
			<td>".$row->admission_no."</td>
			<td>".$row->class_name."</td>
			<td>".$row->group_name."</td>
			<td>".$row->section_name."</td>";                
			$checked ="";
			$CSQL = "SELECT *,DATEDIFF(NOW(), '".$attendance_date."') AS diffrence FROM ".ATTENDANCE_TBL." WHERE institute_id=".$institute_id." AND branch_id=".$branch_id." AND session_id=".$session_id." AND version_id=".$version_id." AND class_id=".$class_id." AND group_id=".$group_id." AND shift_id=".$shift_id." AND section_id=".$section_id." AND account_id=".$admission_id." AND attendance_date='".$attendance_date."'";
			$CRES = $this->db->query($CSQL);
			if($CRES->num_rows() >0){
				if($CRES->row()->present==1){$checked="checked";}else{$checked="";}
				$attend_id = $CRES->row()->id;
			}else{ 
				$checked=""; $attend_id =0;
			}
			if($hasEditPM){
				if($CRES->num_rows()>0 && $CRES->row()->diffrence==0){
				  $grid.= "<td class='text-center'><input  type='checkbox' $checked onclick=IsPresent('".$attend_id."','".$institute_id."','".$branch_id."','".$session_id."','".$version_id."','".$class_id."','".$group_id."','".$shift_id."','".$section_id."','".$admission_id."','".$attend_date."',this) /></td>";
				}elseif($CRES->num_rows()>0 && $CRES->row()->diffrence >0){
				  if($CRES->num_rows() >0 && $CRES->row()->present==1){$grid.= "<td class='text-center'><span class='fa fa-check-circle'></span></td>";}else{$grid.= "<td class='text-center'><span class='fa fa-close'></span></td>";}	
				}else{
					$DSQL = "SELECT DATEDIFF(NOW(), '".$attendance_date."') AS diffrence";
					$DRES = $this->db->query($DSQL);
					if($DRES->row()->diffrence >0){
						$grid.= "<td class='text-center'><span class='fa fa-close'></span></td>";
					}else{
			            $grid.= "<td class='text-center'><input  type='checkbox' $checked onclick=IsPresent('".$attend_id."','".$institute_id."','".$branch_id."','".$session_id."','".$version_id."','".$class_id."','".$group_id."','".$shift_id."','".$section_id."','".$admission_id."','".$attend_date."',this) /></td>";
					}
				}
			}elseif($hasCreatePM){
				if($CRES->num_rows()>0 && $CRES->row()->diffrence >=0){
				  if($CRES->num_rows() >0 && $CRES->row()->present==1){$grid.= "<td class='text-center'><span class='fa fa-check-circle'></span></td>";}else{$grid.= "<td class='text-center'><span class='fa fa-close'></span></td>";}	
				}else{
					$DSQL = "SELECT DATEDIFF(NOW(), '".$attendance_date."') AS diffrence";
					$DRES = $this->db->query($DSQL);
					if($DRES->row()->diffrence >0){
						$grid.= "<td class='text-center'><span class='fa fa-close'></span></td>";
					}else{
			            $grid.= "<td class='text-center'><input  type='checkbox' $checked onclick=IsPresent('".$attend_id."','".$institute_id."','".$branch_id."','".$session_id."','".$version_id."','".$class_id."','".$group_id."','".$shift_id."','".$section_id."','".$admission_id."','".$attend_date."',this) /></td>";
					}
				}
			}else{
				if($CRES->num_rows() >0 && $CRES->row()->present==1){$grid.= "<td class='text-center'><span class='fa fa-check-circle'></span></td>";}else{$grid.= "<td class='text-center'><span class='fa fa-close'></span></td>";}
			}		
			$grid.= "</tr>";
            $i++;
        }
        $grid.= "</table>";
        $grid.= "<div class='float-right'>$Pagination</div>";
        return $grid;
    }
	
    function saveClassAttendance(){
        $attend_id			=$this->input->post('attend-id');
        $institute_id	    =$this->input->post('company-id');
		$branch_id	    	=$this->input->post('branch-id');
		$session_id			=$this->input->post('session-id');
		$version_id			=$this->input->post('version-id');
		$class_id	    	=$this->input->post('class-id');
		$group_id	    	=$this->input->post('group-id');
		$shift_id	    	=$this->input->post('shift-id');
		$section_id			=$this->input->post('section-id');		
		$admission_id		=$this->input->post('admission-id');
		$attendance_date	=$this->formatDate($this->input->post('attendance-date'));
        $in_time	    	=$this->input->post('in-time');
        $out_time	    	=$this->input->post('out-time');
        $present	    	=$this->input->post('present');
		$created_by	    	=$this->session->userdata('created_by');
        if($institute_id==0){
            $institute_id=$this->session->userdata('company_id');
        }
		//===== Get Shift ID =======
		if(empty($shift_id)){
		$shift_id =$this->session->userdata('default_shift');
		}
		if(($in_time=="" || $out_time) && $shift_id>0){
		  $TSQL = "SELECT * FROM ".SHIFT_TBL." WHERE shift_id=".$shift_id." AND institute_id=".$institute_id." AND status =1";
		  $TRES = $this->db->query($TSQL);
		  if($TRES->num_rows() >0){$in_time = $TRES->row()->shift_start; $out_time = $TRES->row()->shift_end;}else{$in_time=date("H:i:s");}
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
        if($attend_id==0){
			$data = array(
                "institute_id"		=>$institute_id,
                "branch_id"   		=>$branch_id,
                "session_id"   		=>$session_id,
                "version_id"    	=>$version_id,
                "shift_id"    		=>$shift_id,
                "class_id"    		=>$class_id,
                "group_id"    		=>$group_id,
                "section_id"    	=>$section_id,
                "account_id"    	=>$admission_id,
                "attendance_date"   =>$attendance_date,
                "check_in_datetime" =>$check_in_datetime,
                "check_out_datetime"=>$check_out_datetime,
                "raw_in_time" 		=>$raw_in_time,
                "raw_out_time" 		=>$raw_out_time,
                "in_time"    		=>$in_time,
                "out_time"    		=>$out_time,
                "total_hour"    	=>$total_hour,
                "inout_status"    	=>1,
                "present"    		=>$present,
				"created_by" 		=>$created_by
			);	
			$this->db->insert(ATTENDANCE_TBL, $data);            
			
        }else{
			$modified_by	=$this->session->userdata('created_by');
			$modified_time 	= date("Y-m-d H:i:s");
			$data = array(
                "check_out_datetime"=>$check_out_datetime,
                "raw_out_time" 		=>$raw_out_time,
                "out_time"    		=>$out_time,
                "total_hour"    	=>$total_hour,
                "inout_status"    	=>0,
                "present"    		=>$present,
				'modified_by' 		=>$modified_by,
				'modified_time' 	=>$modified_time
			);
            $this->db->where('id',$attend_id);
            $this->db->where('institute_id',$institute_id);
            $this->db->where('branch_id',$branch_id);
            $this->db->where('account_id',$admission_id);
            $this->db->update(ATTENDANCE_TBL, $data);
        } //print $this->db->last_query();
    }
	function GetTotalClassAttendanceRecord(){
        $institute_id	    =$this->input->post('company-id');
		$branch_id	    	=$this->input->post('branch-id');
		$session_id			=$this->input->post('session-id');
		$version_id			=$this->input->post('version-id');
		$class_id	    	=$this->input->post('class-id');
		$group_id	    	=$this->input->post('group-id');
		$shift_id	    	=$this->input->post('shift-id');
		$section_id	    	=$this->input->post('section-id');
		$attendance_date	=$this->input->post('attendance-date');
		
        if(empty($institute_id)){$institute_id = $this->session->userdata('company_id');}
        $this->db->select('a.admission_id,a.admission_no,s.account_name as student_name,c.class_name,g.group_name,sc.section_name,i.company_name');
        $this->db->from(ADMISSION_TBL.' AS a');
	    $this->db->join(COMPANY_SETTINGS_TBL.' AS i', 'i.company_id=a.institute_id','LEFT');
	    $this->db->join(CLASS_TBL.' AS c', 'c.class_id=a.class_id','LEFT');
	    $this->db->join(GROUPS_TBL.' AS g', 'g.group_id=a.group_id','LEFT');
	    $this->db->join(SECTION_TBL.' AS sc', 'sc.section_id=a.section_id','LEFT');
	    $this->db->join(ACC_HEAD_TBL.' AS s', 's.account_id=a.student_name_en','LEFT');
        if($institute_id >0){
            $this->db->where('i.company_id',$institute_id);
        }			
		$this->db->where('a.branch_id', $branch_id);				
		$this->db->where('a.session_id', $session_id);				
		$this->db->where('a.version_id', $version_id);				
		$this->db->where('a.class_id', $class_id);				
		$this->db->where('a.group_id', $group_id);				
		$this->db->where('a.section_id', $section_id);				
		$this->db->where('a.shift_id', $shift_id);
		$this->db->where('a.status',1);
        $this->db->group_by('a.admission_id');
        $this->db->order_by('a.admission_no','ASC');
        $query = $this->db->get();
        if($query->num_rows() >0){
            return $query->num_rows();
        }else{
            return 0;
        }//echo $this->db->last_query();
    }
	//===== Start Auto Class Attendance =======	
    function saveAutoClassAttendance(){		
		$menu_slug= $this->uri->segment(1);
		$this->load->model('Site_model');		
		$hasCreatePM = $this->Site_model->hasOptionPermission($menu_slug,"Create");		
		$hasEditPM   = $this->Site_model->hasOptionPermission($menu_slug,"Edit");
        $institute_id	    =$this->input->post('company-id');
		$branch_id	    	=$this->input->post('branch-id');
		$session_id			=$this->input->post('session-id');
		$version_id			=$this->input->post('version-id');
		$class_id	    	=$this->input->post('class-id');
		$group_id	    	=$this->input->post('group-id');
		$shift_id	    	=$this->input->post('shift-id');
		$section_id	    	=$this->input->post('section-id');
		$attendance_date	=$this->formatDate($this->input->post('attendance-date'));
		
        $in_time	    	=$this->input->post('in-time');
        $out_time	    	=$this->input->post('out-time');
        $present	    	=$this->input->post('present');
		$created_by	    	=$this->session->userdata('created_by');
        if($institute_id==0){
            $institute_id=$this->session->userdata('company_id');
        }
		//===== Get Shift ID =======
		if(empty($shift_id)){
		$shift_id =$this->session->userdata('default_shift');
		}
		if(($in_time=="" || $out_time=="") && $shift_id>0){
		  $TSQL = "SELECT * FROM ".SHIFT_TBL." WHERE shift_id=".$shift_id." AND institute_id=".$institute_id." AND status =1";
		  $TRES = $this->db->query($TSQL);
		  if($TRES->num_rows() >0){$in_time = $TRES->row()->shift_start; $out_time = $TRES->row()->shift_end;}else{$in_time=date("H:i:s");}
		}
		$check_in_datetime  = $attendance_date." ".$in_time;
		$check_out_datetime = $attendance_date." ".$out_time;
		$IntimeArr 			= explode(":",$in_time);
		if(!empty($IntimeArr[2])){
			$raw_in_time 	= (($IntimeArr[0] * 3600) + ($IntimeArr[1] * 60) + $IntimeArr[2]);
		}else{
			$raw_in_time 	= (($IntimeArr[0] * 3600) + ($IntimeArr[1] * 60));	
		}
		$OuttimeArr 		= explode(":",$out_time);
		if(!empty($OuttimeArr[2])){
			$raw_out_time 	= (($OuttimeArr[0] * 3600) + ($OuttimeArr[1] * 60) + $OuttimeArr[2]);						
		}else{
			$raw_out_time 	= (($OuttimeArr[0] * 3600) + ($OuttimeArr[1] * 60));
		}			
		$date1 				= new DateTime($check_in_datetime);
		$date2 				= new DateTime($check_out_datetime);

		$diff       		= $date2->diff($date1);
		$hours      		= $diff->h;
		$total_hour 		= $hours + ($diff->days*24);
		$present			= 1;
        $from	=$this->input->post('from');
        $to	=$this->input->post('to');
        if($from==""){ $from=0;} if($to==""){ $to=100;}
        $this->db->select('a.admission_id,a.admission_no');
        $this->db->from(ADMISSION_TBL.' AS a');
	    $this->db->join(COMPANY_SETTINGS_TBL.' AS i', 'i.company_id=a.institute_id','LEFT');
        if($institute_id >0){
            $this->db->where('i.company_id',$institute_id);
        }			
		$this->db->where('a.branch_id', $branch_id);				
		$this->db->where('a.session_id', $session_id);				
		$this->db->where('a.version_id', $version_id);				
		$this->db->where('a.class_id', $class_id);				
		$this->db->where('a.group_id', $group_id);				
		$this->db->where('a.section_id', $section_id);				
		$this->db->where('a.shift_id', $shift_id);
		$this->db->where('a.status',1);
        $this->db->group_by('a.admission_id');
        $this->db->order_by('a.admission_no','ASC');
        $this->db->limit($to,$from);
        $query = $this->db->get(); //print  $this->db->last_query();        
		$i=1; 
        foreach($query->result() as $row){
            $admission_id 	= $row->admission_id;
			$CSQL = "SELECT *,DATEDIFF(NOW(), '".$attendance_date."') AS diffrence FROM ".ATTENDANCE_TBL." WHERE institute_id=".$institute_id." AND branch_id=".$branch_id." AND session_id=".$session_id." AND version_id=".$version_id." AND class_id=".$class_id." AND group_id=".$group_id." AND shift_id=".$shift_id." AND section_id=".$section_id." AND account_id=".$admission_id." AND attendance_date='".$attendance_date."'";
			$CRES = $this->db->query($CSQL);
			if($CRES->num_rows() >0){
				if($hasEditPM){
					if($CRES->row()->diffrence>=0){
						$attend_id 		= $CRES->row()->id;
						$modified_by	= $this->session->userdata('created_by');
						$modified_time 	= date("Y-m-d H:i:s");
						$data = array(
							"check_in_datetime" =>$check_in_datetime,
							"check_out_datetime"=>$check_out_datetime,
							"raw_in_time" 		=>$raw_in_time,
							"raw_out_time" 		=>$raw_out_time,
							"in_time"    		=>$in_time,
							"out_time"    		=>$out_time,
							"total_hour"    	=>$total_hour,
							"inout_status"    	=>0,
							"present"    		=>$present,
							'modified_by' 		=>$modified_by,
							'modified_time' 	=>$modified_time
						);
						$this->db->where('id',$attend_id);
						$this->db->where('institute_id',$institute_id);
						$this->db->where('branch_id',$branch_id);
						$this->db->where('session_id',$session_id);
						$this->db->where('version_id',$version_id);
						$this->db->where('account_id',$admission_id);
						$this->db->update(ATTENDANCE_TBL, $data);
					}
				}else{
					if($CRES->row()->diffrence==0){
						$attend_id 		= $CRES->row()->id;
						$modified_by	= $this->session->userdata('created_by');
						$modified_time 	= date("Y-m-d H:i:s");
						$data = array(
							"check_out_datetime"=>$check_out_datetime,
							"raw_out_time" 		=>$raw_out_time,
							"out_time"    		=>$out_time,
							"total_hour"    	=>$total_hour,
							"inout_status"    	=>0,
							"present"    		=>$present,
							'modified_by' 		=>$modified_by,
							'modified_time' 	=>$modified_time
						);
						$this->db->where('id',$attend_id);
						$this->db->where('institute_id',$institute_id);
						$this->db->where('branch_id',$branch_id);
						$this->db->where('session_id',$session_id);
						$this->db->where('version_id',$version_id);
						$this->db->where('account_id',$admission_id);
						$this->db->update(ATTENDANCE_TBL, $data);
					}
				}
			}else{
				if($hasCreatePM){					
					 $attend_id = 0;
					 $data = array(
						"institute_id"		=>$institute_id,
						"branch_id"   		=>$branch_id,
						"session_id"   		=>$session_id,
						"version_id"    	=>$version_id,
						"shift_id"    		=>$shift_id,
						"class_id"    		=>$class_id,
						"group_id"    		=>$group_id,
						"section_id"    	=>$section_id,
						"account_id"    	=>$admission_id,
						"attendance_date"   =>$attendance_date,
						"check_in_datetime" =>$check_in_datetime,
						"check_out_datetime"=>$check_out_datetime,
						"raw_in_time" 		=>$raw_in_time,
						"raw_out_time" 		=>$raw_out_time,
						"in_time"    		=>$in_time,
						"out_time"    		=>$out_time,
						"total_hour"    	=>$total_hour,
						"inout_status"    	=>1,
						"present"    		=>$present,
						"created_by" 		=>$created_by
					 );	
					 $this->db->insert(ATTENDANCE_TBL, $data);
				}
			}
			//print $this->db->last_query();
		}        
    }
	//===== Start Staff Attendance =========
    function GetStaffAttendanceRecordGrid(){
		$menu_slug= $this->uri->segment(1);
		$this->load->model('Site_model');		
		$hasCreatePM = $this->Site_model->hasOptionPermission($menu_slug,"Create");		
		$hasEditPM   = $this->Site_model->hasOptionPermission($menu_slug,"Edit");
        $institute_id	    =$this->input->post('company-id');
		$branch_id	    	=$this->input->post('branch-id');
		$session_id			=$this->input->post('session-id');
		$department_id		=$this->input->post('department-id');
		$shift_id	    	=$this->input->post('shift-id');
		$attend_date		=$this->input->post('attendance-date');
		$attendance_date	=$this->formatDate($this->input->post('attendance-date'));
		
        if(empty($institute_id)){$institute_id = $this->session->userdata('company_id');}
        if(empty($branch_id)){$branch_id=0;}
        $from	=$this->input->post('from');
        $to	=$this->input->post('to');
        if($from==""){ $from=0;} if($to==""){ $to=100;}
        $this->db->select('a.branch_id,a.department_id,a.employee_id,a.employee_code,a.card_id,a.designation,s.mobile,s.account_name as employee_name,a.designation,i.company_name');
        $this->db->from(EMPLOYEE_TBL.' AS a');
	    $this->db->join(COMPANY_SETTINGS_TBL.' AS i', 'i.company_id=a.company_id','LEFT');
	    $this->db->join(ACC_HEAD_TBL.' AS s', 's.account_id=a.employee_id','LEFT');
        if($institute_id >0){
            $this->db->where('i.company_id',$institute_id);
        }
        if($branch_id >0){
            $this->db->where('a.branch_id',$branch_id);
        }
        if($department_id >0){
            $this->db->where('a.department_id',$department_id);
        }			
		//$this->db->where('a.shift_id', $shift_id);
		$this->db->where('a.status',1);
        $this->db->group_by('a.employee_id');
        $this->db->order_by('a.employee_code','ASC');
        $this->db->limit($to,$from);
        $query = $this->db->get(); //print  $this->db->last_query();
        $totalrecord = $this->GetTotalStaffAttendanceRecord();
        $perPage=100; $Pagination="";
        if($totalrecord >0){
            $Pagination = $this->getPagination($totalrecord,$perPage);
        } //print  $this->db->last_query();
        
        $grid = "<table width='100%'  border='0' class='table table-responsive table-bordered table-hover custab'>
		<thead>
			<tr class='bg-light'>
			<th width='2%' class='text-left'>".$this->lang->line("sl")."</th>
			<th width='28%' class='text-left'>".$this->lang->line("employee_name")."</th>
			<th width='16%' class='text-left'>".$this->lang->line("mobile")."</th>
			<th width='13%' class='text-left'>".$this->lang->line("in_time")."</th>
			<th width='13%' class='text-left'>".$this->lang->line("out_time")."</th>
			<th width='13%' class='text-left'>".$this->lang->line("remarks")."</th>
			<th width='10%' class='text-center'>".$this->lang->line("is_present")."</th>
			</tr>
		</thead>";
        $i=1; 
        foreach($query->result() as $row){
            $employee_id 	= $row->employee_id;
            $branch_id 	    = $row->branch_id;
            $department_id 	= $row->department_id;
            $checked ="";
			$CSQL = "SELECT *,DATEDIFF(NOW(), '".$attendance_date."') AS diffrence FROM ".ATTENDANCE_TBL." WHERE institute_id=".$institute_id." AND session_id=".$session_id." AND shift_id=".$shift_id." AND account_id=".$employee_id." AND attendance_date='".$attendance_date."'";
			
            if($branch_id >0){
                $CSQL.=" AND branch_id=".$branch_id;
            }
            if($department_id >0){
                $CSQL.=" AND department_id=".$department_id;
            }
			$CRES = $this->db->query($CSQL);
			if($CRES->num_rows() >0){
				if($CRES->row()->present==1){$checked="checked";}else{$checked="";}
				$attend_id = $CRES->row()->id; $in_time=$CRES->row()->in_time; $out_time=$CRES->row()->out_time; $remarks= $CRES->row()->remarks;
			}else{ 
				$checked=""; $attend_id =0; $in_time=""; $out_time=""; $remarks="";
			}
			
            $grid.="<tr class='default'>
			<td>".$i."</td>
			<td>".$row->employee_name."<br>".$row->card_id."<br>".$row->designation."</td>
			<td>".$row->mobile."</td>
			<td><input  type='text' name='intime".$employee_id."' id='intime".$employee_id."' placeholder='09:00:00' class='form-control' value='".$in_time."' /></td>
			<td><input  type='text' name='outtime".$employee_id."' id='outtime".$employee_id."' placeholder='18:00:00' class='form-control' value='".$out_time."' /></td>
			<td><input  type='text' name='remarks".$employee_id."' id='remarks".$employee_id."' placeholder='Remarks' class='form-control' value='".$remarks."'/></td>";                
			
			if($hasEditPM){ 
				if($CRES->num_rows()>0 && $CRES->row()->diffrence>=0){ //if($CRES->num_rows()>0 && $CRES->row()->diffrence==0){
				  //$grid.= "<td class='text-center'><input  type='checkbox' $checked onclick=IsPresent('".$attend_id."','".$institute_id."','".$branch_id."','".$session_id."','".$department_id."','".$shift_id."','".$employee_id."','".$attend_date."',this) /></td>";
				  
				  if($CRES->num_rows() >0 && $CRES->row()->present==1){
				      $grid.= "<td class='text-center'><span class='fa fa-check-circle'></span></td>";
				  }elseif($CRES->num_rows() >0 && $CRES->row()->present==0 && $CRES->row()->day_type==4){
				      $grid.= "<td class='text-center'><span class='fa fa-sign-out'></span></td>";
				  }else{
				      $grid.= "<td class='text-center'><input  type='checkbox' $checked onclick=IsPresent('".$attend_id."','".$institute_id."','".$branch_id."','".$session_id."','".$department_id."','".$shift_id."','".$employee_id."','".$attend_date."',this) /></td>";
				  }
				}/*elseif($CRES->num_rows()>0 && $CRES->row()->diffrence >0){
				  if($CRES->num_rows() >0 && $CRES->row()->present==1){
				      $grid.= "<td class='text-center'><span class='fa fa-check-circle'></span></td>";
				  }elseif($CRES->num_rows() >0 && $CRES->row()->present==0 && $CRES->row()->day_type==4){
				      $grid.= "<td class='text-center'><span class='fa fa-sign-out'></span></td>";
				  }else{
				      $grid.= "<td class='text-center'><span class='fa fa-close'></span></td>";
				  }	
				}*/else{ 
					$DSQL = "SELECT DATEDIFF(NOW(), '".$attendance_date."') AS diffrence";
					$DRES = $this->db->query($DSQL);
					if($DRES->row()->diffrence >0){
					//	$grid.= "<td class='text-center'><span class='fa fa-close'></span></td>";
					$grid.= "<td class='text-center'><input  type='checkbox' $checked onclick=IsPresent('".$attend_id."','".$institute_id."','".$branch_id."','".$session_id."','".$department_id."','".$shift_id."','".$employee_id."','".$attend_date."',this) /></td>";
					}else{
			            $grid.= "<td class='text-center'><input  type='checkbox' $checked onclick=IsPresent('".$attend_id."','".$institute_id."','".$branch_id."','".$session_id."','".$department_id."','".$shift_id."','".$employee_id."','".$attend_date."',this) /></td>";
					}
				}
			}elseif($hasCreatePM){  
				if($CRES->num_rows()>0 && $CRES->row()->diffrence >=0){
				  if($CRES->num_rows() >0 && $CRES->row()->present==1){
				      $grid.= "<td class='text-center'><span class='fa fa-check-circle'></span></td>";
				  }elseif($CRES->num_rows() >0 && $CRES->row()->present==0 && $CRES->row()->day_type==4){
				      $grid.= "<td class='text-center'><span class='fa fa-sign-out'></span></td>";
				  }else{
				      //$grid.= "<td class='text-center'><span class='fa fa-close'></span></td>";
				      $grid.= "<td class='text-center'><input  type='checkbox' $checked onclick=IsPresent('".$attend_id."','".$institute_id."','".$branch_id."','".$session_id."','".$department_id."','".$shift_id."','".$employee_id."','".$attend_date."',this) /></td>";
				  }	
				}else{
					$DSQL = "SELECT DATEDIFF(NOW(), '".$attendance_date."') AS diffrence";
					$DRES = $this->db->query($DSQL);
					if($DRES->row()->diffrence >0){
						//$grid.= "<td class='text-center'><span class='fa fa-close'></span></td>";
						$grid.= "<td class='text-center'><input  type='checkbox' $checked onclick=IsPresent('".$attend_id."','".$institute_id."','".$branch_id."','".$session_id."','".$department_id."','".$shift_id."','".$employee_id."','".$attend_date."',this) /></td>";
					}else{
			            $grid.= "<td class='text-center'><input  type='checkbox' $checked onclick=IsPresent('".$attend_id."','".$institute_id."','".$branch_id."','".$session_id."','".$department_id."','".$shift_id."','".$employee_id."','".$attend_date."',this) /></td>";
					}
				}
			}else{ 
				if($CRES->num_rows() >0 && $CRES->row()->present==1){
				    $grid.= "<td class='text-center'><span class='fa fa-check-circle'></span></td>";
				}elseif($CRES->num_rows() >0 && $CRES->row()->present==0 && $CRES->row()->day_type==4){
			        $grid.= "<td class='text-center'><span class='fa fa-sign-out'></span></td>";
			    }else{
			        //$grid.= "<td class='text-center'><span class='fa fa-close'></span></td>";
			        $grid.= "<td class='text-center'><input  type='checkbox' $checked onclick=IsPresent('".$attend_id."','".$institute_id."','".$branch_id."','".$session_id."','".$department_id."','".$shift_id."','".$employee_id."','".$attend_date."',this) /></td>";
			    }
			}		
			$grid.= "</tr>";
            $i++;
        }
        $grid.= "</table>";
        $grid.= "<div class='float-right'>$Pagination</div>";
        return $grid;
    }
 
    function saveStaffAttendance(){
        $attend_id			=$this->input->post('attend-id');
        $institute_id	    =$this->input->post('company-id');
		$branch_id	    	=$this->input->post('branch-id');
		$session_id			=$this->input->post('session-id');
		$department_id		=$this->input->post('department-id');
		$group_id	    	=$this->input->post('group-id');
		$shift_id	    	=$this->input->post('shift-id');
		$section_id			=$this->input->post('section-id');		
		$employee_id		=$this->input->post('employee-id');
		$attendance_date	=$this->formatDate($this->input->post('attendance-date'));
        $in_time	    	=$this->input->post('in-time');
        $out_time	    	=$this->input->post('out-time');
        $remarks	    	=str_replace("U 0026", '&', $this->input->post('remarks'));
        $present	    	=$this->input->post('present');
        
        $ESQL = "SELECT branch_id,department_id,section_id,shift_id FROM ".EMPLOYEE_TBL." WHERE company_id=".$institute_id." AND employee_id=".$employee_id." AND status=1";
    	$ERES = $this->db->query($ESQL);
    	$branch_id      = $ERES->row()->branch_id;
    	$department_id  = $ERES->row()->department_id;
    	$section_id     = $ERES->row()->section_id;
    	$shift_id       = $ERES->row()->shift_id;
    	
		$created_by	    	=$this->session->userdata('created_by');
        if($institute_id==0){
            $institute_id=$this->session->userdata('company_id');
        }
		//===== Get Shift ID =======
		if($shift_id==0){
		  $shift_id =$this->session->userdata('default_shift');
		}
		if(($in_time=="" || $out_time=="") && $shift_id>0){
		  $TSQL = "SELECT * FROM ".SHIFT_TBL." WHERE shift_id=".$shift_id." AND institute_id=".$institute_id." AND status =1";
		  $TRES = $this->db->query($TSQL);
		  if($TRES->num_rows() >0){
		      $in_time = $TRES->row()->shift_start; $out_time = $TRES->row()->shift_end;
		      
		  }else{$in_time=date("H:i:s"); $out_time = "18:00:00";}
		  
		}
		//===== Get Shift In Out Time ====
		if($shift_id>0){
		  $TSQL = "SELECT * FROM ".SHIFT_TBL." WHERE shift_id=".$shift_id." AND institute_id=".$institute_id." AND status =1";
		  $TRES = $this->db->query($TSQL);
		  if($TRES->num_rows() >0){
		      $sin_time = $TRES->row()->shift_start; $sout_time = $TRES->row()->shift_end;
		  }else{$sin_time=date("H:i:s"); $sout_time = "18:00:00";}
		  $SIntimeArr 		= explode(":",$sin_time); $grace_intime = 1200; // 10 minute = 600, 30 m=1200
		  $shift_in_time 	= (($SIntimeArr[0] * 3600) + ($SIntimeArr[1] * 60) + $SIntimeArr[2]);
		  $SOuttimeArr 		= explode(":",$sout_time);
		  $shift_out_time 	= (($SOuttimeArr[0] * 3600) + ($SOuttimeArr[1] * 60) + $SOuttimeArr[2]);
		}  
		  
		$check_in_datetime  = $attendance_date." ".$in_time;
		$check_out_datetime = $attendance_date." ".$out_time;		
		$IntimeArr 			= explode(":",$in_time);
		$raw_in_time 		= (($IntimeArr[0] * 3600) + ($IntimeArr[1] * 60) + $IntimeArr[2]);
		$OuttimeArr 		= explode(":",$out_time);
		$raw_out_time 		= (($OuttimeArr[0] * 3600) + ($OuttimeArr[1] * 60) + $OuttimeArr[2]);
		$date1 = new DateTime($check_in_datetime);
		$date2 = new DateTime($check_out_datetime);
        
        if(($raw_in_time - $grace_intime) <= $shift_in_time){$late=0;}else{$late=1;}
        if($raw_out_time >= $shift_out_time){$early_leave=0;}else{$early_leave=1;}
        
	
		$diff       = $date2->diff($date1);
		$hours      = $diff->h;
		$total_hour = $hours + ($diff->days*24);
        if($attend_id==0){
			$data = array(
                "institute_id"		=>$institute_id,
                "branch_id"   		=>$branch_id,
                "session_id"   		=>$session_id,
                "department_id"    	=>$department_id,
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
                "late"    		    =>$late,
                "early_leave"    	=>$early_leave,
                "total_hour"    	=>$total_hour,
                "inout_status"    	=>1,
                "present"    		=>$present,
                "remarks"    		=>$remarks,
				"created_by" 		=>$created_by
			);	
			$this->db->insert(ATTENDANCE_TBL, $data);            
			
        }else{
			$modified_by	=$this->session->userdata('created_by');
			$modified_time 	= date("Y-m-d H:i:s");
			if($present==0){
			    $raw_in_time=0; $raw_out_time=0; $in_time=""; $out_time=""; $late=0; $early_leave=0; $total_hour=0;
			}
			$data = array(
                "branch_id"   		=>$branch_id,
                "session_id"   		=>$session_id,
                "department_id"    	=>$department_id,
                "shift_id"    		=>$shift_id,
                "section_id"    	=>$section_id,
                "check_out_datetime"=>$check_out_datetime,
                "raw_in_time" 		=>$raw_in_time,
                "raw_out_time" 		=>$raw_out_time,
                "in_time"    		=>$in_time,
                "out_time"    		=>$out_time,
                "late"    		    =>$late,
                "early_leave"    	=>$early_leave,
                "total_hour"    	=>$total_hour,
                "inout_status"    	=>0,
                "present"    		=>$present,
                "remarks"    		=>$remarks,
				'modified_by' 		=>$modified_by,
				'modified_time' 	=>$modified_time
			);
            $this->db->where('id',$attend_id);
            $this->db->where('institute_id',$institute_id);
            $this->db->where('account_id',$employee_id);
            $this->db->update(ATTENDANCE_TBL, $data);
            //print  $this->db->last_query();
        }
    }
    
			
	//===== Start Staff Attendance =========
    function GetDailyAttendanceRecordGrid(){
		$menu_slug= $this->uri->segment(1);
		$this->load->model('Site_model');		
		$hasCreatePM = $this->Site_model->hasOptionPermission($menu_slug,"Create");		
		$hasEditPM   = $this->Site_model->hasOptionPermission($menu_slug,"Edit");
        $institute_id	    =$this->input->post('company-id');
		$branch_id	    	=$this->input->post('branch-id');
		$session_id			=$this->input->post('session-id');
		$department_id		=$this->input->post('department-id');
		$shift_id	    	=$this->input->post('shift-id');
		$attend_date		=$this->input->post('attendance-date');
		$attendance_date	=$this->formatDate($this->input->post('attendance-date'));
		if($this->session->userdata('weekend')){
			$weekendArr   = explode(",",$this->session->userdata('weekend'));
		}else{$weekendArr = array("Friday");}
		
        if(empty($institute_id)){$institute_id = $this->session->userdata('company_id');}
        $from	=$this->input->post('from');
        $to	=$this->input->post('to');
        if($from==""){ $from=0;} if($to==""){ $to=100;}
        $this->db->select('a.employee_id,a.employee_code,a.card_id,a.designation,s.mobile,s.account_name as employee_name,a.designation,i.company_name');
        $this->db->from(EMPLOYEE_TBL.' AS a');
	    $this->db->join(COMPANY_SETTINGS_TBL.' AS i', 'i.company_id=a.company_id','LEFT');
	    $this->db->join(ACC_HEAD_TBL.' AS s', 's.account_id=a.employee_id','LEFT');
        if($institute_id >0){
            $this->db->where('i.company_id',$institute_id);
        }
        if($department_id >0){
            $this->db->where('a.department_id',$department_id);
        }			
		//$this->db->where('a.shift_id', $shift_id);
		$this->db->where('a.status',1);
        $this->db->group_by('a.employee_id');
        $this->db->order_by('a.employee_code','ASC');
        $this->db->limit($to,$from);
        $query = $this->db->get(); //print  $this->db->last_query();
        $totalrecord = $this->GetTotalStaffAttendanceRecord();
        $perPage=100; $Pagination="";
        if($totalrecord >0){
            $Pagination = $this->getPagination($totalrecord,$perPage);
        } //print  $this->db->last_query();
        
        $grid = "<table width='100%'  border='0' class='table table-responsive table-bordered table-hover custab'>
		<thead>
			<tr class='bg-light'>
			<th width='2%' class='text-left'>".$this->lang->line("sl")."</th>
			<th width='20%' class='text-left'>".$this->lang->line("employee_name")."</th>
			<th width='12%' class='text-left'>".$this->lang->line("attendance_date")."</th>
			<th width='8%' class='text-left'>".$this->lang->line("in_time")."</th>
			<th width='8%' class='text-left'>".$this->lang->line("out_time")."</th>
			<th width='6%' class='text-left'>".$this->lang->line("is_late")."</th>
			<th width='6%' class='text-left'>".$this->lang->line("early_out")."</th>
			<th width='10%' class='text-left'>".$this->lang->line("total")."</th>
			<th width='12%' class='text-left'>".$this->lang->line("remarks")."</th>
			<th width='16%' class='text-center'>".$this->lang->line("status")."</th>
			</tr>
		</thead>";
        $i=1; 
        foreach($query->result() as $row){
            $employee_id 	= $row->employee_id;
            $checked =""; $is_late=""; $early_out=""; $total_hours=0;$status="";
			$CSQL = "SELECT id,raw_in_time,raw_out_time,in_time,out_time,day_type,late,early_leave,after_leave,before_leave,total_hour,inout_status,present,remarks,DATE_FORMAT(attendance_date ,'%d-%M-%Y') as date_of_attendance,DATEDIFF(NOW(), '".$attendance_date."') AS diffrence FROM ".ATTENDANCE_TBL." WHERE institute_id=".$institute_id." AND session_id=".$session_id." AND department_id=".$department_id." AND shift_id=".$shift_id." AND account_id=".$employee_id." AND attendance_date='".$attendance_date."'";
			$CRES = $this->db->query($CSQL);
			if($CRES->num_rows() >0){
				if($CRES->row()->present==1){$checked="checked";}else{$checked="";}
				$check_in_date = $CRES->row()->date_of_attendance;
				$attend_id = $CRES->row()->id; $in_time=$CRES->row()->in_time; $out_time=$CRES->row()->out_time;
				$total_hours=$CRES->row()->total_hour;
				$remarks = $CRES->row()->remarks;
				if($CRES->row()->late==1){$is_late="Yes";}else{$is_late="No";}
				if($CRES->row()->early_leave==1){$early_out="Yes";}else{$early_out="No";}
				
				if($CRES->row()->diffrence >=0 && $CRES->row()->day_type==1 && $CRES->row()->present==1){
				    $status="Present"; $txtMark="text-success";
				}elseif($CRES->row()->diffrence >=0 && $CRES->row()->day_type==4 && $CRES->row()->present==0){
				    $status="Leave"; $txtMark="text-primary";
				}elseif($CRES->row()->diffrence >=0 && $CRES->row()->day_type==6 && $CRES->row()->present==1){
				    $status="OD"; $txtMark="text-muted"; $is_late="No";
				}else{$status="Absence";$txtMark="text-danger";}
			}else{ 
				$checked=""; $attend_id =0; $in_time=""; $out_time="";
				//==== Get Days In Month =======
        		$this->db->select('DATE_FORMAT(date_field ,"%d-%M-%Y") as date_of_attendance,DAY(date_field) as day,YEAR(date_field) as years,date_field,DATEDIFF(NOW(), date_field) AS diffrence,day_name,is_holiday',FALSE);
                $this->db->from(MONTH_DAYS_TBL);
        		$this->db->where('date_field',$attendance_date);
                $this->db->order_by('date_field','ASC');
                $rquery = $this->db->get();
                if($rquery->num_rows() >0){
                    $rrow = $rquery->row();
                    $check_in_date = $rrow->date_of_attendance;
                    if($rrow->diffrence >=0 && in_array($rrow->day_name, $weekendArr)){ 
    				  $status="Day Off"; $txtMark="text-warning";
    				}else if($rrow->diffrence >=0 && $rrow->is_holiday==1){
    					$status ="Holiday"; $txtMark="text-info";
    				}else if($rrow->diffrence >=0){
    					$status.= "Absence"; $txtMark="text-danger";
    				}else if($rrow->diffrence <0){
    			        $status.= "..."; $txtMark="text-dark";
    				}
                }
			}
			
            $grid.="<tr class='default'>
			<td>".$i."</td>
			<td>".$row->employee_name."<br>".$row->designation."</td>
			<td>".$check_in_date."</td>
			<td>".$in_time."</td>
			<td>".$out_time."</td>
			<td>".$is_late."</td>
			<td>".$early_out."</td>
			<td>".$total_hours." Hr</td>
			<td>".$remarks."</td>
			<td class='text-center $txtMark'>".$status."</td>";		
			$grid.= "</tr>";
            $i++;
        }
        $grid.= "</table>";
        $grid.= "<div class='float-right'>$Pagination</div>";
        return $grid;
    }
	function GetTotalStaffAttendanceRecord(){
        $institute_id	    =$this->input->post('company-id');
		$branch_id	    	=$this->input->post('branch-id');
		$session_id			=$this->input->post('session-id');
		$department_id		=$this->input->post('department-id');
		$shift_id	    	=$this->input->post('shift-id');
		$attend_date		=$this->input->post('attendance-date');
		$attendance_date	=$this->formatDate($this->input->post('attendance-date'));
		
        if(empty($institute_id)){$institute_id = $this->session->userdata('company_id');}
        $this->db->select('a.employee_id,a.employee_code,s.account_name as employee_name,a.designation,i.company_name');
        $this->db->from(EMPLOYEE_TBL.' AS a');
	    $this->db->join(COMPANY_SETTINGS_TBL.' AS i', 'i.company_id=a.company_id','LEFT');
	    $this->db->join(ACC_HEAD_TBL.' AS s', 's.account_id=a.employee_id','LEFT');
        if($institute_id >0){
            $this->db->where('i.company_id',$institute_id);
        }
        if($department_id >0){
            $this->db->where('a.department_id',$department_id);
        }			
		//$this->db->where('a.branch_id', $branch_id);			
		//$this->db->where('a.shift_id', $shift_id);
		$this->db->where('a.status',1);
        $this->db->group_by('a.employee_id');
        $this->db->order_by('a.employee_code','ASC');
        $query = $this->db->get();
        if($query->num_rows() >0){
            return $query->num_rows();
        }else{
            return 0;
        }//echo $this->db->last_query();
    }
    
	//===== Start Monthly Job Card ===============
	function GetMonthlyJobCardRecord(){
		$menu_slug= $this->uri->segment(1);
		$this->load->model('Site_model');		
		$hasViewPM          = $this->Site_model->hasOptionPermission($menu_slug,"View");		
		$hasPrintPM         = $this->Site_model->hasOptionPermission($menu_slug,"Print");
        $institute_id	    =$this->input->post('company-id');
		$branch_id	    	=$this->input->post('branch-id');
		$session_id			=$this->input->post('session-id');
		$department_id		=$this->input->post('department-id');
		$shift_id	    	=$this->input->post('shift-id');
		$employee_id		=$this->input->post('employee-id');
		$attendance_month	=$this->input->post('attendance-month');
		if($this->session->userdata('weekend')){
			$weekendArr   = explode(",",$this->session->userdata('weekend'));
		}else{$weekendArr = array("Friday");}
		
        if(empty($institute_id)){$institute_id = $this->session->userdata('company_id');}
        
        $this->db->select('a.employee_id,a.employee_code,a.card_id,a.designation,s.mobile,s.account_name as employee_name,a.designation,a.department_id,a.shift_id,i.company_id,i.company_name,d.department_name');
        $this->db->from(EMPLOYEE_TBL.' AS a');
	    $this->db->join(COMPANY_SETTINGS_TBL.' AS i', 'i.company_id=a.company_id','LEFT');
	    $this->db->join(ACC_HEAD_TBL.' AS s', 's.account_id=a.employee_id','LEFT');
	    $this->db->join(DEPARTMENT_TBL.' AS d', 'd.department_id=a.department_id','LEFT');
	    if($this->session->userdata('user_role') ==4){
		    $this->db->where("a.employee_id", $this->session->userdata('user_ref_id')); 
		}else{
    	    $this->db->where('a.employee_id',$employee_id);
            if($institute_id >0){
                $this->db->where('i.company_id',$institute_id);
            }
            if($department_id >0){
                $this->db->where('a.department_id',$department_id);
            }
            if($shift_id >0){
                $this->db->where('a.shift_id',$shift_id);
            }
		}
		$this->db->where('a.status',1);
        $query = $this->db->get(); //print  $this->db->last_query();
        if($query->num_rows() >0){
		 $row = $query->row();
		 if($this->session->userdata('user_role') ==4){
		    $employee_id    = $this->session->userdata('user_ref_id'); 
		    $company_id     = $row->company_id; 
		    $department_id  = $row->department_id; 
		    $shift_id       = $row->shift_id;
		 }		
        $grid = "
        <table width='100%'  border='0' class='table table-responsive table-bordered table-hover custab'>
		<thead>
			<tr class='bg-light'>
			<th width='20%' class='text-left'>".$this->lang->line("employee_name")."</th>
			<th width='30%'>".$row->employee_name."</th>
			<th width='20%' class='text-left'>".$this->lang->line("employee_id")."</th>
			<th width='30%'>".$row->card_id."</th>
			</tr>
			<tr class='bg-light'>
			<th class='text-left'>".$this->lang->line("designation")."</th>
			<th>".$row->designation."</th>
			<th class='text-left'>".$this->lang->line("department")."</th>
			<th>".$row->department_name."</th>
			</tr>
		</thead>
		<tbody>
		<tr class='default'>
			<td colspan=4>
            <table width='100%'  border='0' class='table table-responsive table-bordered table-hover custab'>
    		
    			<tr class='bg-light'>
    			<th width='2%' class='text-left'>".$this->lang->line("sl")."</th>
    			<th width='12%' class='text-left'>".$this->lang->line("attendance_date")."</th>
    			<th width='8%' class='text-left'>".$this->lang->line("in_time")."</th>
    			<th width='8%' class='text-left'>".$this->lang->line("out_time")."</th>
    			<th width='6%' class='text-left'>".$this->lang->line("is_late")."</th>
    			<th width='6%' class='text-left'>".$this->lang->line("early_out")."</th>
    			<th width='12%' class='text-left'>".$this->lang->line("total")." (Hr)</th>
    			<th width='15%' class='text-left'>".$this->lang->line("remarks")."</th>
    			<th width='16%' class='text-center'>".$this->lang->line("status")."</th>
    			</tr>
    		";
        $i=1;
        $checked =""; $is_late=""; $early_out=""; $total_hours=0; $status="";
		$this->db->select('date_field as attendance_date, DATE_FORMAT(date_field ,"%d-%M-%Y") as date_of_attendance,DAY(date_field) as day,YEAR(date_field) as years,date_field,DATEDIFF(NOW(), date_field) AS diffrence,day_name,is_holiday',FALSE);
        $this->db->from(MONTH_DAYS_TBL);
		$this->db->where('MONTH(date_field)',$attendance_month);
        $this->db->order_by('date_field','ASC');
        $rquery = $this->db->get();	//print  $this->db->last_query();
			
        foreach($rquery->result() as $rrow){
            $remarks= "";
            $attendance_date = $rrow->attendance_date;
            $check_in_date   = $rrow->attendance_date;
            $CSQL = "SELECT id,raw_in_time,raw_out_time,in_time,out_time,day_type,late,early_leave,after_leave,before_leave,total_hour,inout_status,present,DATE_FORMAT(attendance_date ,'%d-%M-%Y') as date_of_attendance, remarks FROM ".ATTENDANCE_TBL." WHERE institute_id=".$institute_id." AND department_id=".$department_id." AND shift_id=".$shift_id." AND account_id=".$employee_id." AND attendance_date='$attendance_date'";
			$CRES = $this->db->query($CSQL);
			if($CRES->num_rows() >0){
			   $prow = $CRES->row();
				if($prow->present==1){$checked="checked";}else{$checked="";}
				$attend_id  = $prow->id; $in_time=$prow->in_time; $out_time=$prow->out_time; $remarks=$prow->remarks;
				if($prow->total_hour < 10){
				    $total_hours= "0".$prow->total_hour;
				}else{
				    $total_hours= $prow->total_hour;
				}
				
				if($prow->late==1){$is_late="Yes";}else{$is_late="No";}
				if($prow->early_leave==1){$early_out="Yes";}else{$early_out="No";}
				
				if($prow->diffrence >=0 && $prow->day_type==1 && $prow->present==1){
				    $status="Present"; $txtMark="text-success";
				}elseif($prow->diffrence >=0 && $prow->day_type==4 && $prow->present==0){
				    $status="Leave"; $txtMark="text-primary"; $is_late="No";
				}elseif($prow->diffrence >=0 && $prow->day_type==6 && $prow->present==1){
				    $status="OD"; $txtMark="text-muted"; $is_late="No";
				}else{$status="Absence";$txtMark="text-danger";}
			}else{ 
				$checked=""; $attend_id =0; $in_time=""; $out_time="";
				//==== Get Days In Month =======
                if(in_array($rrow->day_name, $weekendArr)){ 
				  $status="Day Off"; $txtMark="text-warning";
				}else if($rrow->diffrence >=0 && $rrow->is_holiday==1){
					$status ="Holiday"; $txtMark="text-info";
				}else if($rrow->diffrence >=0){
					$status= "Absence"; $txtMark="text-danger";
				}else if($rrow->diffrence <0){
			        $status= ""; $txtMark="text-dark";
				}
			}
			
			$ELSQL = "SELECT leave_type FROM ".LEAVE_TBL." WHERE employee_id=".$employee_id." AND leave_from='$attendance_date' AND leave_type IN (2,3)";
			$ELRES = $this->db->query($ELSQL);
			if($ELRES->num_rows() >0){
			   $remarks="";
			   $elrow = $ELRES->row();
			   if($elrow->leave_type==2){
			   $is_late="No"; $remarks = "Half Leave";
			   $early_out="No";
			   }elseif($elrow->leave_type==3){
			   $early_out="No"; $remarks = "Early Leave";
			   }
			}
			
			if($status!="" && ($status=="Day Off" || $status=="Holiday" || $status=="Absence" || $status=="Leave")){
            $grid.="<tr class='default'>
			<td>".$i."</td>
			<td>".$check_in_date."</td>
			<td colspan=5>&nbsp;</td>
			<td>".$remarks."</td>
			<td class='text-center $txtMark'>".$status."</td>";		
			$grid.= "</tr>";    
			}elseif($status==""){
            $grid.="<tr class='default'>
			<td>".$i."</td>
			<td>".$check_in_date."</td>
			<td colspan=5>&nbsp;</td>
			<td>".$remarks."</td>
			<td class='text-center $txtMark'>".$status."</td>";		
			$grid.= "</tr>";    
			}else{
            $grid.="<tr class='default'>
			<td>".$i."</td>
			<td>".$check_in_date."</td>
			<td>".$in_time."</td>
			<td>".$out_time."</td>
			<td>".$is_late."</td>
			<td>".$early_out."</td>
			<td class='text-right'>".$total_hours."</td>
			<td>".$remarks."</td>
			<td class='text-center $txtMark'>".$status."</td>";		
			$grid.= "</tr>";
			}
            $i++;
        }
        $grid.= "</table>";
        $grid.= "
         </td>	
		 </tr>
        </tbody>
        <tfooter>
        
        </tfooter>
        </table>";
        return $grid;
        }else{
           return ""; 
        }
    }
	//===== Start Class Attendance Report=========
	function GetClassBasicInfo(){		
		$institute_id	    =$this->input->post('company-id');
		$branch_id	    	=$this->input->post('branch-id');
		$session_id			=$this->input->post('session-id');
		$version_id			=$this->input->post('version-id');
		$class_id	    	=$this->input->post('class-id');
		$group_id	    	=$this->input->post('group-id');
		$shift_id	    	=$this->input->post('shift-id');
		$section_id	    	=$this->input->post('section-id');
		if(empty($institute_id)){$institute_id = $this->session->userdata('company_id');}        
        $this->db->select('ss.session_name,vr.version_name,c.class_name,g.group_name,sc.section_name,sf.shift_name,i.company_name,i.address');
        $this->db->from(SECTION_MAPPING_TBL.' AS sm');
	    $this->db->join(COMPANY_SETTINGS_TBL.' AS i', 'i.company_id=sm.institute_id','LEFT');
	    $this->db->join(SESSION_TBL.' AS ss', 'ss.sessions_id=sm.sessions_id','LEFT');
	    $this->db->join(VERSION_TBL.' AS vr', 'vr.version_id=sm.version_id','LEFT');
	    $this->db->join(CLASS_TBL.' AS c', 'c.class_id=sm.class_id','LEFT');
	    $this->db->join(GROUPS_TBL.' AS g', 'g.group_id=sm.group_id','LEFT');
	    $this->db->join(SECTION_TBL.' AS sc', 'sc.section_id=sm.section_id','LEFT');
	    $this->db->join(SHIFT_TBL.' AS sf', 'sf.shift_id=sm.shift_id','LEFT');
        if($institute_id >0){
            $this->db->where('i.company_id',$institute_id);
        }			
		$this->db->where('sm.branch_id', $branch_id);				
		$this->db->where('sm.sessions_id', $session_id);				
		$this->db->where('sm.version_id', $version_id);				
		$this->db->where('sm.class_id', $class_id);				
		$this->db->where('sm.group_id', $group_id);				
		$this->db->where('sm.section_id', $section_id);				
		$this->db->where('sm.shift_id', $shift_id);
		$this->db->where('sm.status',1);
        $this->db->group_by('sm.section_map_id');
        $query = $this->db->get();
		return $query->row();
		
	}
    function GetMonthlyClassAttendanceList(){
		$menu_slug= $this->uri->segment(1);
		$this->load->model('Site_model');		
		$institute_id	    =$this->input->post('company-id');
		$branch_id	    	=$this->input->post('branch-id');
		$session_id			=$this->input->post('session-id');
		$version_id			=$this->input->post('version-id');
		$class_id	    	=$this->input->post('class-id');
		$group_id	    	=$this->input->post('group-id');
		$shift_id	    	=$this->input->post('shift-id');
		$section_id	    	=$this->input->post('section-id');
		$month_name			=$this->input->post('month-name');
		if($this->session->userdata('weekend')){
			$weekendArr   = explode(",",$this->session->userdata('weekend'));
		}else{$weekendArr = array("Friday");}
		
		if($month_name=="01"){$month_lang="jan";}elseif($month_name=="02"){$month_lang="feb";}elseif($month_name=="03"){$month_lang="mar";}elseif($month_name=="04"){$month_lang="apr";}elseif($month_name=="05"){$month_lang="may";}elseif($month_name=="06"){$month_lang="jun";}elseif($month_name=="07"){$month_lang="jul";}elseif($month_name=="08"){$month_lang="aug";}elseif($month_name=="09"){$month_lang="sep";}elseif($month_name==10){$month_lang="oct";}elseif($month_name==11){$month_lang="nov";}elseif($month_name==12){$month_lang="dec";}
        if(empty($institute_id)){$institute_id = $this->session->userdata('company_id');}
        $from	=$this->input->post('from');
        $to	=$this->input->post('to');
        if($from==""){ $from=0;} if($to==""){ $to=100;}
        $this->db->select('a.admission_id,a.admission_no,s.account_name as student_name,c.class_name,g.group_name,sc.section_name,i.company_name');
        $this->db->from(ADMISSION_TBL.' AS a');
	    $this->db->join(COMPANY_SETTINGS_TBL.' AS i', 'i.company_id=a.institute_id','LEFT');
	    $this->db->join(CLASS_TBL.' AS c', 'c.class_id=a.class_id','LEFT');
	    $this->db->join(GROUPS_TBL.' AS g', 'g.group_id=a.group_id','LEFT');
	    $this->db->join(SECTION_TBL.' AS sc', 'sc.section_id=a.section_id','LEFT');
	    $this->db->join(ACC_HEAD_TBL.' AS s', 's.account_id=a.student_name_en','LEFT');
        if($institute_id >0){
            $this->db->where('i.company_id',$institute_id);
        }
		if($this->session->userdata('user_role') >4){
			$this->db->where("s.account_id", $this->session->userdata('user_ref_id')); 
		}		
		$this->db->where('a.branch_id', $branch_id);				
		$this->db->where('a.session_id', $session_id);				
		$this->db->where('a.version_id', $version_id);				
		$this->db->where('a.class_id', $class_id);				
		$this->db->where('a.group_id', $group_id);				
		$this->db->where('a.section_id', $section_id);				
		$this->db->where('a.shift_id', $shift_id);
		$this->db->where('a.status',1);
        $this->db->group_by('a.admission_id');
        $this->db->order_by('a.admission_no','ASC');
        $this->db->limit($to,$from);
        $query = $this->db->get(); //print  $this->db->last_query();
        $totalrecord = $this->GetTotalMonthlyClassAttendanceRecord();
        $perPage=100; $Pagination="";
        if($totalrecord >0){
            $Pagination = $this->getPagination($totalrecord,$perPage);
        } 
        //==== Get Days In Month =======
		$this->db->select('DAY(date_field) as day,YEAR(date_field) as years,date_field,day_name,is_holiday',FALSE);
        $this->db->from(MONTH_DAYS_TBL);
		$this->db->where('MONTH(date_field)',$month_name);
        $this->db->order_by('date_field','ASC');
        $rquery 		= $this->db->get();
        $totalroles 	= $rquery->num_rows();
		if($totalroles >0){
			$width		= (73/$totalroles); $month_year = $rquery->row()->years;
		}else{
			$width		= 73; $month_year = date("Y");
		}
		$mrow = $this->GetClassBasicInfo();
        $grid = "
		<table width='100%'  border='0' class='table table-responsive table-bordered table-hover custab'>			
			<tr>
			<th style='width:5%;' class='text-left'>".$this->lang->line("class")."</th>
			<th style='width:8%;' class='text-left'>".$mrow->class_name."</th>
			<th style='width:5%;' class='text-left'>".$this->lang->line("group")."</th>
			<th style='width:8%;' class='text-left'>".$mrow->group_name."</th>
			<th style='width:4%;' class='text-left'>".$this->lang->line("section")."</th>
			<th style='width:3%;' class='text-left'>".$mrow->section_name."</th>
			<th style='width:6%;' class='text-left'>".$this->lang->line("version")."</th>
			<th style='width:11%;' class='text-left'>".$mrow->version_name."</th>
			<th style='width:10%;'>Color Marks:</th> 
			<td style='height:8px; width:8%;' class='bg-success'><span class='fa fa-check-circle'></span> Present</td> 
			<td style='height:8px; width:7%;' class='bg-danger'><span class='fa fa-close'></span> Absent</td> 
			<td style='height:8px; width:9%;' class='bg-warning'><span class='fa fa-ban'></span> Weekend</td> 
			<td style='height:8px; width:8%;' class='bg-info'><span class='fa fa-expeditedssl'></span> Holiday</td> 
			<td style='height:8px; width:7%;' class='bg-primary'><span class='fa fa-sign-out'></span> Leave</td>
			</tr>			
		</table>
		<table width='100%'  border='0' class='table table-responsive table-bordered table-hover custab'>
		<thead>
			<tr class='bg-light'>
			<th width='02%' rowspan='2' class='align-middle text-left'>".$this->lang->line("sl")."</th>
			<th width='25%' rowspan='2' class='align-middle text-left'>".$this->lang->line("student_name")."</th>
			<th width='70%' colspan='".$totalroles."' class='text-center'>".$this->lang->line("$month_lang")." ".$month_year."</th>
			</tr>
			<tr class='bg-light'>
		";
		if($totalroles >0){
		  foreach($rquery->result() as $rrow){
		   $grid.= "<th width='".$width."%' class='text-center'>".$rrow->day."</th>";
		  }
		}
		$grid.= "
		  </tr>
		</thead>";
		
        $i=1; 
        foreach($query->result() as $row){
            $admission_id 	= $row->admission_id;			
            $grid.="<tr class='default'>
			<td>".$i."</td>
			<td>".$row->student_name."<br>".$row->admission_no."</td>";                
			$isPresent ="";	$txtMark="";		
			if($totalroles >0){
			  foreach($rquery->result() as $rrow){				
				$DSQL    	= "SELECT DATEDIFF(NOW(), '".$rrow->date_field."') AS diffrence";
				$dquery  	= $this->db->query($DSQL);
				$diffrence 	= $dquery->row()->diffrence;					
			    $CSQL = "SELECT *,DATEDIFF(NOW(), '".$rrow->date_field."') AS diffrence FROM ".ATTENDANCE_TBL." WHERE institute_id=".$institute_id." AND branch_id=".$branch_id." AND session_id=".$session_id." AND version_id=".$version_id." AND class_id=".$class_id." AND group_id=".$group_id." AND shift_id=".$shift_id." AND section_id=".$section_id." AND account_id=".$admission_id." AND attendance_date='".$rrow->date_field."'";
				$CRES = $this->db->query($CSQL);
				if($CRES->num_rows() >0){
					if($CRES->row()->present==1 && $diffrence >=0){$isPresent="<span class='fa fa-check-circle'></span>";$txtMark="text-success";}elseif($CRES->row()->present==0 && $diffrence >=0){$isPresent="<span class='fa fa-close'></span>";$txtMark="text-danger";}elseif($diffrence <0){$isPresent="<span class='fa fa-circle-o'></span>";$txtMark="text-muted";}
				}else{ 
					if($diffrence >=0){$isPresent="<span class='fa fa-times-circle'></span>";$txtMark="text-danger";}elseif($diffrence <0){$isPresent="<span class='fa fa-circle-o'></span>"; $txtMark="text-muted";}
					if (in_array($rrow->day_name, $weekendArr)){
					  $isPresent="<span class='fa fa-ban'></span>"; $txtMark="text-warning";
					}
					if($rrow->is_holiday==1){
						$isPresent="<span class='fa fa-expeditedssl'></span>"; $txtMark="text-info";
					}
				}				
				$grid.= "<td class='text-center ".$txtMark."'>$isPresent</td>";
			  }
			}		
			$grid.= "</tr>";
            $i++;
        }
        $grid.= "</table>";
        $grid.= "<div class='float-right'>$Pagination</div>";
        return $grid;
    }
    function GetTotalMonthlyClassAttendanceRecord(){
		$menu_slug= $this->uri->segment(1);
		$this->load->model('Site_model');		
		$institute_id	    =$this->input->post('company-id');
		$branch_id	    	=$this->input->post('branch-id');
		$session_id			=$this->input->post('session-id');
		$version_id			=$this->input->post('version-id');
		$class_id	    	=$this->input->post('class-id');
		$group_id	    	=$this->input->post('group-id');
		$shift_id	    	=$this->input->post('shift-id');
		$section_id	    	=$this->input->post('section-id');
		$month_name			=$this->input->post('month-name');
		if($month_name=="01"){$month_lang="jan";}elseif($month_name=="02"){$month_lang="feb";}elseif($month_name=="03"){$month_lang="mar";}elseif($month_name=="04"){$month_lang="apr";}elseif($month_name=="05"){$month_lang="may";}elseif($month_name=="06"){$month_lang="jun";}elseif($month_name=="07"){$month_lang="jul";}elseif($month_name=="08"){$month_lang="aug";}elseif($month_name=="09"){$month_lang="sep";}elseif($month_name==10){$month_lang="oct";}elseif($month_name==11){$month_lang="nov";}elseif($month_name==12){$month_lang="dec";}
        if(empty($institute_id)){$institute_id = $this->session->userdata('company_id');}
        $this->db->select('a.admission_id,a.admission_no,s.account_name as student_name,c.class_name,g.group_name,sc.section_name,i.company_name');
        $this->db->from(ADMISSION_TBL.' AS a');
	    $this->db->join(COMPANY_SETTINGS_TBL.' AS i', 'i.company_id=a.institute_id','LEFT');
	    $this->db->join(CLASS_TBL.' AS c', 'c.class_id=a.class_id','LEFT');
	    $this->db->join(GROUPS_TBL.' AS g', 'g.group_id=a.group_id','LEFT');
	    $this->db->join(SECTION_TBL.' AS sc', 'sc.section_id=a.section_id','LEFT');
	    $this->db->join(ACC_HEAD_TBL.' AS s', 's.account_id=a.student_name_en','LEFT');
        if($institute_id >0){
            $this->db->where('i.company_id',$institute_id);
        }			
		$this->db->where('a.branch_id', $branch_id);				
		$this->db->where('a.session_id', $session_id);				
		$this->db->where('a.version_id', $version_id);				
		$this->db->where('a.class_id', $class_id);				
		$this->db->where('a.group_id', $group_id);				
		$this->db->where('a.section_id', $section_id);				
		$this->db->where('a.shift_id', $shift_id);
		$this->db->where('a.status',1);
        $this->db->group_by('a.admission_id');
        $this->db->order_by('a.admission_no','ASC');
        $query = $this->db->get();
        if($query->num_rows() >0){
            return $query->num_rows();
        }else{
            return 0;
        }//echo $this->db->last_query();
		
	}
	//===== Start Staff Attendance Report=========
	function GetStaffBasicInfo(){		
		$institute_id	    =$this->input->post('company-id');
		$branch_id	    	=$this->input->post('branch-id');
		$session_id			=$this->input->post('session-id');
		$department_id		=$this->input->post('department-id');
		$shift_id	    	=$this->input->post('shift-id');
		if(empty($institute_id)){$institute_id = $this->session->userdata('company_id');}        
        $this->db->select('ss.session_name,dr.department_name,b.branch_name,sf.shift_name,i.company_name,i.address');
        $this->db->from(COMPANY_SETTINGS_TBL.' AS i');
	    $this->db->join(BRANCH_TBL.' AS b', 'b.company_id=i.company_id','LEFT');
	    $this->db->join(SESSION_TBL.' AS ss', 'ss.institute_id=i.company_id','LEFT');
	    $this->db->join(DEPARTMENT_TBL.' AS dr', 'dr.company_id=i.company_id','LEFT');
	    $this->db->join(SHIFT_TBL.' AS sf', 'sf.shift_id=i.default_shift','LEFT');
        if($institute_id >0){
            $this->db->where('i.company_id',$institute_id);
        }			
		$this->db->where('b.branch_id', $branch_id);
		if($department_id >0){					
		$this->db->where('dr.department_id', $department_id);
		$this->db->where('dr.status',1);
		}
		$this->db->where('ss.sessions_id', $session_id);			
		$this->db->where('sf.shift_id', $shift_id);
		$this->db->where('ss.session_status >=',1);
		$this->db->where('sf.status',1);
        $this->db->group_by('i.company_id');
        $query = $this->db->get();
		return $query->row();		
	}	
    function GetMonthlyStaffAttendanceList(){
		$menu_slug= $this->uri->segment(1);
		$this->load->model('Site_model');		
		$institute_id	    =$this->input->post('company-id');
		$branch_id	    	=$this->input->post('branch-id');
		$department_id		=$this->input->post('department-id');
		$session_id			=$this->input->post('session-id');
		$shift_id	    	=$this->input->post('shift-id');
		$month_name			=$this->input->post('month-name');
		if($this->session->userdata('weekend')){
			$weekendArr   = explode(",",$this->session->userdata('weekend'));
		}else{$weekendArr = array("Friday");}
		if($month_name=="01"){$month_lang="jan";}elseif($month_name=="02"){$month_lang="feb";}elseif($month_name=="03"){$month_lang="mar";}elseif($month_name=="04"){$month_lang="apr";}elseif($month_name=="05"){$month_lang="may";}elseif($month_name=="06"){$month_lang="jun";}elseif($month_name=="07"){$month_lang="jul";}elseif($month_name=="08"){$month_lang="aug";}elseif($month_name=="09"){$month_lang="sep";}elseif($month_name==10){$month_lang="oct";}elseif($month_name==11){$month_lang="nov";}elseif($month_name==12){$month_lang="dec";}
        if(empty($institute_id)){$institute_id = $this->session->userdata('company_id');}
        $from	=$this->input->post('from');
        $to	=$this->input->post('to');
        if($from==""){ $from=0;} if($to==""){$to=200;}
        $this->db->select('a.employee_id,a.employee_code,a.designation,s.mobile,s.account_name as employee_name,a.designation,i.company_name,b.branch_name');
		$this->db->from(EMPLOYEE_TBL.' AS a');
	    $this->db->join(COMPANY_SETTINGS_TBL.' AS i', 'i.company_id=a.company_id','LEFT');
	    $this->db->join(BRANCH_TBL.' AS b', 'b.company_id=a.company_id','LEFT');
	    $this->db->join(ACC_HEAD_TBL.' AS s', 's.account_id=a.employee_id','LEFT');
        if($institute_id >0){
            $this->db->where('i.company_id',$institute_id);
        }
		if($this->session->userdata('user_role') >4){
			$this->db->where("s.account_id", $this->session->userdata('user_ref_id')); 
		}		
		if($branch_id >0){
		$this->db->where('a.branch_id', $branch_id);
		}
		if($department_id >0){
		$this->db->where('a.department_id', $department_id);
		}
		$this->db->where('i.default_shift', $shift_id);
		$this->db->where('a.status',1);
        $this->db->group_by('a.employee_id');
        $this->db->order_by('a.employee_code','ASC');
        $this->db->limit($to,$from);
        $query = $this->db->get(); //print  $this->db->last_query();
        $totalrecord = $this->GetTotalMonthlyStaffAttendanceRecord();
        $perPage=100; $Pagination="";
        if($totalrecord >0){
            $Pagination = $this->getPagination($totalrecord,$perPage);
        } 
        //==== Get Days In Month =======
		$this->db->select('DAY(date_field) as day,YEAR(date_field) as years,date_field,day_name,is_holiday',FALSE);
        $this->db->from(MONTH_DAYS_TBL);
		$this->db->where('MONTH(date_field)',$month_name);
        $this->db->order_by('date_field','ASC');
        $rquery 		= $this->db->get();
        $totalroles 	= $rquery->num_rows();
		if($totalroles >0){
			$width		= (73/$totalroles); $month_year = $rquery->row()->years;
		}else{
			$width		= 73; $month_year = date("Y");
		}
		$mrow = $this->GetStaffBasicInfo();
		
        $grid = "
		<table width='100%'  border='0' class='table table-responsive table-bordered table-hover custab'>			
			<tr>			
			<th style='width:7%;' class='text-left'>".$this->lang->line("branch")."</th>
			<th style='width:14%;' class='text-left'>".$mrow->branch_name."</th>
			<th style='width:7%;' class='text-left'>".$this->lang->line("department")."</th>
			<th style='width:14%;' class='text-left'>".$mrow->department_name."</th>
			<th style='width:13%;'>Color Marks:</th> 
			<td style='height:8px; width:9%;' class='bg-success'><span class='fa fa-check-circle'></span> Present</td> 
			<td style='height:8px; width:9%;' class='bg-danger'><span class='fa fa-close'></span> Absent</td> 
			<td style='height:8px; width:10%;' class='bg-warning'><span class='fa fa-ban'></span> Weekend</td> 
			<td style='height:8px; width:9%;' class='bg-info'><span class='fa fa-expeditedssl'></span> Holiday</td> 
			<td style='height:8px; width:8%;' class='bg-primary'><span class='fa fa-sign-out'></span> Leave</td>
			</tr>			
		</table>
		<table width='100%'  border='0' class='table table-responsive table-bordered table-hover custab'>
		<thead>
			<tr class='bg-light'>
			<th width='02%' rowspan='2' class='align-middle text-left'>".$this->lang->line("sl")."</th>
			<th width='25%' rowspan='2' class='align-middle text-left'>".$this->lang->line("employee_name")."</th>
			<th width='70%' colspan='".$totalroles."' class='text-center'>".$this->lang->line("$month_lang")." ".$month_year."</th>
			</tr>
			<tr class='bg-light'>
		";
		if($totalroles >0){
		  foreach($rquery->result() as $rrow){
		   $grid.= "<th width='".$width."%' class='text-center'>".$rrow->day."</th>";
		  }
		}
		$grid.= "
		  </tr>
		</thead>";		
        $i=1; 
        foreach($query->result() as $row){
            $employee_id 	= $row->employee_id;			
            $grid.="<tr class='default'>
			<td>".$i."</td>
			<td>".$row->employee_name.",<br>".$row->designation."</td>";                
			$isPresent ="";	$txtMark="";		
			if($totalroles >0){
			  foreach($rquery->result() as $rrow){				
				$DSQL    	= "SELECT DATEDIFF(NOW(), '".$rrow->date_field."') AS diffrence";
				$dquery  	= $this->db->query($DSQL);
				$diffrence 	= $dquery->row()->diffrence;					
			    $CSQL = "SELECT *,DATEDIFF(NOW(), '".$rrow->date_field."') AS diffrence FROM ".ATTENDANCE_TBL." WHERE institute_id=".$institute_id." AND session_id=".$session_id." AND shift_id=".$shift_id." AND account_id=".$employee_id." AND attendance_date='".$rrow->date_field."'";
				if($branch_id >0){
                    $CSQL.=" AND branch_id=".$branch_id;
                }
                if($department_id >0){
                    $CSQL.=" AND department_id=".$department_id;
                }
				$CRES = $this->db->query($CSQL);
				if($CRES->num_rows() >0){
					if($CRES->row()->present==1 && $CRES->row()->day_type==1 && $diffrence >=0){
					    $isPresent="<span class='fa fa-check-circle'></span>";$txtMark="text-success";
					}elseif($CRES->row()->present==1 && $CRES->row()->day_type==6 && $diffrence >=0){
					    $isPresent="<span class='fa fa-check-circle'></span>";$txtMark="text-muted";
					}elseif($CRES->row()->present==0 && $diffrence >=0){
					    if($CRES->row()->day_type==4){
					        $isPresent="<span class='fa fa-sign-out'></span>";$txtMark="text-primary";
					    }else{
					        $isPresent="<span class='fa fa-times-circle'></span>";$txtMark="text-danger";
					    }
					}elseif($diffrence <0){
					    if($CRES->row()->day_type==4){
					        $isPresent="<span class='fa fa-sign-out'></span>";$txtMark="text-primary";
					    }elseif($CRES->row()->day_type==6){
					        $isPresent="<span class='fa fa-check-circle'></span>";$txtMark="text-muted";
					    }else{
					        $isPresent="<span class='fa fa-circle-o'></span>";$txtMark="text-muted";
					    }
					}
				}else{ 
					if($diffrence >=0){
					    if($CRES->row()->day_type==4){
					        $isPresent="<span class='fa fa-sign-out'></span>";$txtMark="text-primary";
					    }elseif($CRES->row()->day_type==6){
					        $isPresent="<span class='fa fa-check-circle'></span>";$txtMark="text-muted";
					    }else{
					        $isPresent="<span class='fa fa-times-circle'></span>";$txtMark="text-danger";
					    }
					    //$isPresent="<span class='fa fa-times-circle'></span>";$txtMark="text-danger";
					}elseif($diffrence <0){
					    if($CRES->row()->day_type==4){
					        $isPresent="<span class='fa fa-sign-out'></span>";$txtMark="text-danger";
					    }elseif($CRES->row()->day_type==6){
					        $isPresent="<span class='fa fa-check-circle'></span>";$txtMark="text-muted";
					    }else{
					        $isPresent="<span class='fa fa-circle-o'></span>"; $txtMark="text-muted";
					    }
					}
					if (in_array($rrow->day_name, $weekendArr)){
					  $isPresent="<span class='fa fa-ban'></span>"; $txtMark="text-warning";
					}
					if($rrow->is_holiday==1){
						$isPresent="<span class='fa fa-expeditedssl'></span>"; $txtMark="text-info";
					}
				}				
				$grid.= "<td class='text-center ".$txtMark."'>$isPresent</td>";
			  }
			}		
			$grid.= "</tr>";
            $i++;
        }
        $grid.= "</table>";
        $grid.= "<div class='float-right'>$Pagination</div>";
        return $grid;
    }
    function GetTotalMonthlyStaffAttendanceRecord(){
		$menu_slug= $this->uri->segment(1);
		$this->load->model('Site_model');		
		$institute_id	    =$this->input->post('company-id');
		$branch_id	    	=$this->input->post('branch-id');
		$session_id			=$this->input->post('session-id');
		$department_id		=$this->input->post('department-id');
		$shift_id	    	=$this->input->post('shift-id');
		$month_name			=$this->input->post('month-name');
		if($this->session->userdata('weekend')){
			$weekendArr   = explode(",",$this->session->userdata('weekend'));
		}else{$weekendArr = array("Friday");}
		if($month_name=="01"){$month_lang="jan";}elseif($month_name=="02"){$month_lang="feb";}elseif($month_name=="03"){$month_lang="mar";}elseif($month_name=="04"){$month_lang="apr";}elseif($month_name=="05"){$month_lang="may";}elseif($month_name=="06"){$month_lang="jun";}elseif($month_name=="07"){$month_lang="jul";}elseif($month_name=="08"){$month_lang="aug";}elseif($month_name=="09"){$month_lang="sep";}elseif($month_name==10){$month_lang="oct";}elseif($month_name==11){$month_lang="nov";}elseif($month_name==12){$month_lang="dec";}
        if(empty($institute_id)){$institute_id = $this->session->userdata('company_id');}
        $this->db->select('a.employee_id,a.employee_code,a.designation,s.mobile,s.account_name as employee_name,a.designation,i.company_name,b.branch_name');
		$this->db->from(EMPLOYEE_TBL.' AS a');
	    $this->db->join(COMPANY_SETTINGS_TBL.' AS i', 'i.company_id=a.company_id','LEFT');
	    $this->db->join(BRANCH_TBL.' AS b', 'b.company_id=a.company_id','LEFT');
	    $this->db->join(ACC_HEAD_TBL.' AS s', 's.account_id=a.employee_id','LEFT');
        if($institute_id >0){
            $this->db->where('i.company_id',$institute_id);
        }
		if($this->session->userdata('user_role') >4){
			$this->db->where("s.account_id", $this->session->userdata('user_ref_id')); 
		}
		if($branch_id >0){
		$this->db->where('a.branch_id', $branch_id);
		}
		if($department_id >0){
		$this->db->where('a.department_id', $department_id);
		}				
		$this->db->where('i.default_shift', $shift_id);
		$this->db->where('a.status',1);
        $this->db->group_by('a.employee_id');
        $this->db->order_by('a.employee_code','ASC');
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
