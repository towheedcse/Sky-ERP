<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class AllList {
    private $CI;
    public function __construct()
    {
		// Assign the CodeIgniter super-object
		$this->CI =& get_instance();
    }
    function GetCompanyList(){
		$this->CI->db->select('*');
		$this->CI->db->from(COMPANY_SETTINGS_TBL);
		if($this->CI->session->userdata('user_role') >1){
		$this->CI->db->where("company_id", $this->CI->session->userdata('company_id')); 
		}
		$this->CI->db->order_by('company_name','ASC');
		$query = $this->CI->db->get();
		return $query;
    }     
    function GetBranchList(){
		$this->CI->db->select('*');
		$this->CI->db->from(BRANCH_TBL);
		if($this->CI->session->userdata('user_role') >1){
			$this->CI->db->where("company_id", $this->CI->session->userdata('company_id')); 
		}
		if($this->CI->session->userdata('user_role') >3){
			$this->CI->db->where("branch_id", $this->CI->session->userdata('branch_id')); 
		}
		$this->CI->db->order_by('branch_name','ASC');
		$query = $this->CI->db->get();
		return $query;
    }       
    function GetAjaxBranchList($company_id,$branch_id=0){	
		$this->CI->db->select('*');
		$this->CI->db->from(BRANCH_TBL);
		if($this->CI->session->userdata('user_role') >1){
		$this->CI->db->where("company_id", $this->CI->session->userdata('company_id')); 
		}
		if($company_id >0){
		   $this->CI->db->where("company_id", $company_id); 
		}
		if($this->CI->session->userdata('user_role') >3){
		$this->CI->db->where("branch_id", $this->CI->session->userdata('branch_id')); 
		}
		$this->CI->db->order_by('branch_name','ASC');
		$query = $this->CI->db->get(); //echo $this->CI->db->last_query();
		$options = "<option value='0'>".$this->CI->lang->line("select")." ".$this->CI->lang->line("branch")."</option>";
		foreach($query->result() as $irow){	
		if($branch_id >0 && $branch_id==$irow->branch_id){$selected = "selected='selected'";}else{$selected = "";}
		$options.="<option  value='".$irow->branch_id."' $selected>".$irow->branch_name."</option>";
	    }
	    return $options;
    }
           
    function GetAjaxDistrictList($division_id,$district_id=0){	
		$this->CI->db->select('*');
		$this->CI->db->from(AREA_TBL);
		if($this->CI->session->userdata('user_role') >1){
		$this->CI->db->where("company_id", $this->CI->session->userdata('company_id')); 
		}
		if($division_id >0){
		   $this->CI->db->where("division_id", $division_id); 
		}
		$this->CI->db->order_by('area_name','ASC');
		$query = $this->CI->db->get(); //echo $this->CI->db->last_query();
		$options = "<option value='0'>".$this->CI->lang->line("select")." ".$this->CI->lang->line("district")."</option>";
		foreach($query->result() as $irow){	
		if($district_id >0 && $district_id==$irow->area_id){$selected = "selected='selected'";}else{$selected = "";}
		$options.="<option  value='".$irow->area_id."' $selected>".$irow->area_name."</option>";
	    }
	    return $options;
    }
           
    function GetAjaxAreaList($district_id,$trt_id=0){	
		$this->CI->db->select('*');
		$this->CI->db->from(TRT_AREA_TBL);
		if($this->CI->session->userdata('user_role') >1){
		$this->CI->db->where("company_id", $this->CI->session->userdata('company_id')); 
		}
		if($district_id >0){
		   $this->CI->db->where("area_id", $district_id); 
		}
		$this->CI->db->order_by('trt_name','ASC');
		$query = $this->CI->db->get(); //echo $this->CI->db->last_query();
		$options = "<option value='0'>".$this->CI->lang->line("select")." ".$this->CI->lang->line("thana")."</option>";
		foreach($query->result() as $irow){	
		if($trt_id >0 && $trt_id==$irow->trt_id){$selected = "selected='selected'";}else{$selected = "";}
		$options.="<option  value='".$irow->trt_id."' $selected>".$irow->trt_name."</option>";
	    }
	    return $options;
    }
    function GetCurrencyList(){
		$this->CI->db->select('*');
		$this->CI->db->from(CURRENCY_TBL);
		$this->CI->db->order_by('currency_id','ASC');
		$query = $this->CI->db->get(); //echo $this->CI->db->last_query();
		return $query;
    } 
    function GetCountryList(){
		$this->CI->db->select('*');
		$this->CI->db->from(COUNTRY_TBL);
		$this->CI->db->order_by('country','ASC');
		$query = $this->CI->db->get(); //echo $this->CI->db->last_query();
		return $query;
    }
    function GetLeaveTypeList(){
		$this->CI->db->select('*');
		$this->CI->db->from(LEAVE_CATEGORY_TBL);
		$this->CI->db->where("status", 1); 
		$this->CI->db->order_by('category_id','ASC');
		$query = $this->CI->db->get(); //echo $this->CI->db->last_query();
		return $query;
    }     
    function GetCaseTypeList(){
		$this->CI->db->select('*');
		$this->CI->db->from(CASETYPE_TBL);	
		if($this->CI->session->userdata('user_role') >1){
		$this->CI->db->where("institute_id", $this->CI->session->userdata('company_id')); 
		}
		$this->CI->db->where("status", 1); 
		$this->CI->db->order_by('casetype_name','ASC');
		$query = $this->CI->db->get(); //echo $this->CI->db->last_query();
		return $query;
    }
        
    function GetCaseList($status){
		$this->CI->db->select('*');
		$this->CI->db->from(CASE_TBL);	
		if($this->CI->session->userdata('user_role') >1){
		$this->CI->db->where("institute_id", $this->CI->session->userdata('company_id')); 
		}
		$this->CI->db->where("case_no!='0'");
		$this->CI->db->where("status", $status); 
		$this->CI->db->order_by('name_of_parties','ASC');
		$query = $this->CI->db->get(); //echo $this->CI->db->last_query();
		return $query;
    }
           
    function GetDistrictList(){
		$this->CI->db->select('*');
		$this->CI->db->from(DISTRICT_TBL);	
		if($this->CI->session->userdata('user_role') >1){
		$this->CI->db->where("company_id", $this->CI->session->userdata('company_id')); 
		}
		$this->CI->db->where("status", 1); 
		$this->CI->db->order_by('district_name','ASC');
		$query = $this->CI->db->get(); //echo $this->CI->db->last_query();
		return $query;
    }
    function GetPartyList(){
		$this->CI->db->select('*');
		$this->CI->db->from(ACC_HEAD_TBL);	
		if($this->CI->session->userdata('user_role') >1){
		$this->CI->db->where("company_id", $this->CI->session->userdata('company_id')); 
		}
		$this->CI->db->where("head_type", 1); //1=Party
		$this->CI->db->order_by('account_name','ASC');
		$query = $this->CI->db->get(); //echo $this->CI->db->last_query();
		return $query;
    }   
    function GetReferenceList(){
		$this->CI->db->select('*');
		$this->CI->db->from(ACC_HEAD_TBL);	
		if($this->CI->session->userdata('user_role') >1){
		$this->CI->db->where("company_id", $this->CI->session->userdata('company_id')); 
		}
		$head_type = array(1,2);
		$this->CI->db->where_in("head_type", $head_type); //2=Reference
		$this->CI->db->order_by('account_name','ASC');
		$query = $this->CI->db->get(); //echo $this->CI->db->last_query();
		return $query;
    }  
    function GetCustomerList(){
		$this->CI->db->select('*');
		$this->CI->db->from(ACC_HEAD_TBL);	
		if($this->CI->session->userdata('user_role') >1){
		$this->CI->db->where("company_id", $this->CI->session->userdata('company_id')); 
		}
		$this->CI->db->where("head_type", 11); //11=Student
		$this->CI->db->order_by('account_name','ASC');
		$query = $this->CI->db->get(); //echo $this->CI->db->last_query();
		return $query;
    }       
    function GetAjaxBuildingList($company_id,$branch_id,$building_id=0){	
		$this->CI->db->select('*');
		$this->CI->db->from(BUILDING_TBL);
		
		if($company_id >0){
		   $this->CI->db->where("company_id", $company_id); 
		}elseif($this->CI->session->userdata('user_role') >1){
		   $this->CI->db->where("company_id", $this->CI->session->userdata('company_id')); 
		}
		if($branch_id >0){
		   $this->CI->db->where("branch_id", $branch_id); 
		}elseif($this->CI->session->userdata('user_role') >2){
		   $this->CI->db->where("branch_id", $this->CI->session->userdata('branch_id')); 
		}
		
		if($building_id >0){
		   $this->CI->db->where("building_id", $building_id); 
		}
		$this->CI->db->order_by('building_id','ASC');
		$query = $this->CI->db->get(); //echo $this->CI->db->last_query();
		$options = "<option value='0'>".$this->CI->lang->line("select")." ".$this->CI->lang->line("branch")."</option>";
		foreach($query->result() as $irow){	
		if($building_id >0 && $building_id==$irow->building_id){$selected = "selected='selected'";}else{$selected = "";}
		$options.="<option  value='".$irow->building_id."' $selected>".$irow->building_name."</option>";
	    }
	    return $options;
    }        
    function GetAjaxFloorList($company_id,$branch_id,$building_id,$floor_no=0){	
		$this->CI->db->select('*');
		$this->CI->db->from(BUILDING_TBL);
		
		if($company_id >0){
		   $this->CI->db->where("company_id", $company_id); 
		}elseif($this->CI->session->userdata('user_role') >1){
		$this->CI->db->where("company_id", $this->CI->session->userdata('company_id')); 
		}
		if($branch_id >0){
		   $this->CI->db->where("branch_id", $branch_id); 
		}elseif($this->CI->session->userdata('user_role') >2){
		$this->CI->db->where("branch_id", $this->CI->session->userdata('branch_id')); 
		}
		
		if($building_id >0){
		   $this->CI->db->where("building_id", $building_id); 
		}
		$this->CI->db->order_by('building_id','ASC');
		$query = $this->CI->db->get(); 
		if($query->num_rows() >0){
		    $fl= $query->row()->total_floor;
		}else{
		    $fl= 0; 
		}
		$f=1;
		$options = "<option value='0'>".$this->CI->lang->line("select")." ".$this->CI->lang->line("floor_no")."</option>";
		while($f <=$fl){	
		if($floor_no >0 && $floor_no==$f){$selected = "selected='selected'";}else{$selected = "";}
		$options.="<option  value='".$f."' $selected>".$this->CI->lang->line("floor")."- ".$f."</option>";
		$f++;
		}
		return $options;
    }  
            
    function GetRoomList($company_id=0,$branch_id=0,$building_id=0,$floor_no=0,$room_id=0){	
		$this->CI->db->select('*');
		$this->CI->db->from(HOSTEL_ROOM_TBL);
		
		if($company_id >0){
		   $this->CI->db->where("company_id", $company_id); 
		}elseif($this->CI->session->userdata('user_role') >1){
		$this->CI->db->where("company_id", $this->CI->session->userdata('company_id')); 
		}
		if($branch_id >0){
		   $this->CI->db->where("branch_id", $branch_id); 
		}elseif($this->CI->session->userdata('user_role') >2){
		$this->CI->db->where("branch_id", $this->CI->session->userdata('branch_id')); 
		}
		
		if($building_id >0){
		   $this->CI->db->where("building_id", $building_id); 
		}
		
		if($floor_no >0){
		   $this->CI->db->where("floor_no", $floor_no); 
		}
		$this->CI->db->order_by('room_no,floor_no,building_id','ASC');
		$query = $this->CI->db->get();			
		return $query;
    }      
    function GetAjaxRoomList($company_id,$branch_id,$building_id,$floor_no=0,$room_id=0){	
		$this->CI->db->select('*');
		$this->CI->db->from(HOSTEL_ROOM_TBL);
		
		if($company_id >0){
		   $this->CI->db->where("company_id", $company_id); 
		}elseif($this->CI->session->userdata('user_role') >1){
		$this->CI->db->where("company_id", $this->CI->session->userdata('company_id')); 
		}
		if($branch_id >0){
		   $this->CI->db->where("branch_id", $branch_id); 
		}elseif($this->CI->session->userdata('user_role') >2){
		$this->CI->db->where("branch_id", $this->CI->session->userdata('branch_id')); 
		}
		
		if($building_id >0){
		   $this->CI->db->where("building_id", $building_id); 
		}
		
		if($floor_no >0){
		   $this->CI->db->where("floor_no", $floor_no); 
		}
		$this->CI->db->order_by('room_no','ASC');
		$query = $this->CI->db->get(); 
		$condition="";
		if($query->num_rows() >0){
		    $options = "<option value='0'>".$this->CI->lang->line("select")." ".$this->CI->lang->line("room_no")."</option>";
		   foreach($query->result() as $irow){
		     if($irow->room_condition==1){ $condition="AC";}else{$condition="Non AC";}	
		     if($room_id >0 && $room_id==$irow->room_id){$selected = "selected='selected'";}else{$selected = "";}
		     $options.="<option  value='".$irow->room_id."' $selected>".$irow->room_no.", ".$condition."</option>";
		   }
		}else{
		    $options= ""; 
		}
			
		return $options;
   }
   function GetProjectList(){
	$this->CI->db->select('*');
	$this->CI->db->from(PROJECT_TBL);
	if($this->CI->session->userdata('user_role') >1){
	$this->CI->db->where("company_id", $this->CI->session->userdata('company_id')); 
	}
	$this->CI->db->order_by('project_name','ASC');
	$query = $this->CI->db->get();
	return $query;
   }
   function GetBuildingList(){
	$this->CI->db->select('*');
	$this->CI->db->from(BUILDING_TBL);
	if($this->CI->session->userdata('user_role') >1){
	$this->CI->db->where("company_id", $this->CI->session->userdata('company_id')); 
	}
	$this->CI->db->order_by('building_name','ASC');
	$query = $this->CI->db->get();
	return $query;
   }
   function GetSessionList(){
	$this->CI->db->select('*');
	$this->CI->db->from(SESSION_TBL);
	if($this->CI->session->userdata('user_role') >1){
	$this->CI->db->where("institute_id", $this->CI->session->userdata('company_id')); 
	}
	$this->CI->db->order_by('sessions_id','ASC');
	$query = $this->CI->db->get();
	return $query;
   }
   
   function GetMideaList(){
	    $head_type = array(1);
		$this->CI->db->select('*');
		$this->CI->db->from(ACC_HEAD_TBL);
		if($this->CI->session->userdata('user_role') >1){
		$this->CI->db->where("company_id", $this->CI->session->userdata('company_id')); 
		}
		if($this->CI->session->userdata('user_role') >2){
			$this->CI->db->where("branch_id", $this->CI->session->userdata('branch_id')); 
		}
		$this->CI->db->where_in("head_type", $head_type); 
		$this->CI->db->where("status", 1); 
		$this->CI->db->order_by('account_name','ASC');
		$query = $this->CI->db->get();
        return $query;
   }
   
   function GetAgentList(){
	    $head_type = array(2);
		$this->CI->db->select('*');
		$this->CI->db->from(ACC_HEAD_TBL);
		if($this->CI->session->userdata('user_role') >1){
		$this->CI->db->where("company_id", $this->CI->session->userdata('company_id')); 
		}
		if($this->CI->session->userdata('user_role') >2){
			$this->CI->db->where("branch_id", $this->CI->session->userdata('branch_id')); 
		}
		$this->CI->db->where_in("head_type", $head_type); 
		$this->CI->db->where("status", 1); 
		$this->CI->db->order_by('account_name','ASC');
		$query = $this->CI->db->get();
        return $query;
   }
   function GetVersionList(){
		$this->CI->db->select('*');
		$this->CI->db->from(VERSION_TBL);
		if($this->CI->session->userdata('user_role') >1){
		$this->CI->db->where("institute_id", $this->CI->session->userdata('company_id')); 
		}
		$this->CI->db->order_by('version_name','ASC');
		$query = $this->CI->db->get();
		return $query;
    }
    function GetClassList(){
        $this->CI->db->select('c.*');
        $this->CI->db->from(CLASS_TBL.' AS c');
        if($this->CI->session->userdata('user_role') >1){
			$this->CI->db->join(CLASS_PERMISSION_TBL.' AS p', 'p.class_id=p.class_id','LEFT');
            $this->CI->db->where("c.institute_id", $this->CI->session->userdata('company_id'));
            $this->CI->db->where("p.branch_id", $this->CI->session->userdata('branch_id'));
        }
		$this->CI->db->group_by('c.class_id');
        $this->CI->db->order_by('c.class_id','ASC');
        $query = $this->CI->db->get();
        return $query;
    }
    function GetGroupsList(){
        $this->CI->db->select('*');
        $this->CI->db->from(GROUPS_TBL);
        if($this->CI->session->userdata('user_role') >2){
            $this->CI->db->where("group_id", $this->CI->session->userdata('group_name'));
        }
        $this->CI->db->order_by('group_id','ASC');
        $query = $this->CI->db->get(); //print  $this->CI->db->last_query();exit;
        return $query;
    }
   function GetDepartmentList(){
		$this->CI->db->select('*');
		$this->CI->db->from(DEPARTMENT_TBL);
		if($this->CI->session->userdata('user_role') >1){
		$this->CI->db->where("company_id", $this->CI->session->userdata('company_id')); 
		}
		$this->CI->db->order_by('department_name','ASC');
		$query = $this->CI->db->get();
		return $query;
    }
    function GetSectionList(){
        $this->CI->db->select('s.*');
        $this->CI->db->from(SECTION_TBL.' AS s');
        if($this->CI->session->userdata('user_role') >1){
            $this->CI->db->where("s.company_id", $this->CI->session->userdata('company_id'));
        }
		$this->CI->db->group_by('s.section_id');
        $this->CI->db->order_by('s.section_id','ASC');
        $query = $this->CI->db->get();
        return $query;
    }
   
   function GetAjaxSectionList($institute_id,$department_id,$section_id=0){	
		$this->CI->db->select('s.*');
		$this->CI->db->from(SECTION_TBL." AS s");
		if($this->CI->session->userdata('user_role') >1){
		$this->CI->db->where("s.company_id", $this->CI->session->userdata('company_id')); 
		}
		if($institute_id >0){
		   $this->CI->db->where("s.company_id", $institute_id); 
		}
		if($department_id >0){
		   $this->CI->db->where("s.department_id", $department_id); 
		}
		$this->CI->db->where("s.section_status", 1); 
		$this->CI->db->order_by('s.section_name','ASC');
		$query = $this->CI->db->get(); //echo $this->CI->db->last_query();
		$options = "<option value='0'>".$this->CI->lang->line("select")." ".$this->CI->lang->line("section_name")."</option>";
		foreach($query->result() as $irow){	
		if($section_id >0 && $section_id==$irow->section_id){$selected = "selected='selected'";}else{$selected = "";}		
		$options.="<option  value='".$irow->section_id."' $selected>".$irow->section_name."</option>";
		}
		return $options;
   }
    function GetShiftList(){
        $this->CI->db->select('*');
        $this->CI->db->from(SHIFT_TBL);
        if($this->CI->session->userdata('user_role') >1){
            $this->CI->db->where("institute_id", $this->CI->session->userdata('company_id'));
        }
        $this->CI->db->where("status", 1);
        $this->CI->db->order_by('shift_name','ASC');
        $query = $this->CI->db->get();
        return $query;
   }
   function GetExamTimesList(){
        $this->CI->db->select('*');
        $this->CI->db->from(EXAM_TIMES_TBL);
        if($this->CI->session->userdata('user_role') >1){
            $this->CI->db->where("institute_id", $this->CI->session->userdata('company_id'));
        }
		if($this->CI->session->userdata('user_role') >2){
		$this->CI->db->where("branch_id", $this->CI->session->userdata('branch_id')); 
		}
		$this->CI->db->where("status", 1);
        $this->CI->db->order_by('order_no','ASC');
        $query = $this->CI->db->get();
        return $query;
   }
   function GetExamCategoryList(){
        $this->CI->db->select('*');
        $this->CI->db->from(EXAM_CATEGORY_TBL);
        if($this->CI->session->userdata('user_role') >1){
            $this->CI->db->where("institute_id", $this->CI->session->userdata('company_id'));
        }
		if($this->CI->session->userdata('user_role') >2){
		$this->CI->db->where("branch_id", $this->CI->session->userdata('branch_id')); 
		}
		$this->CI->db->where("status", 1);
        $this->CI->db->order_by('order_no','ASC');
        $query = $this->CI->db->get();
        return $query;
   }
   function GetExamTypeList(){
        $this->CI->db->select('*');
        $this->CI->db->from(EXAMTYPE_TBL);
        if($this->CI->session->userdata('user_role') >1){
            $this->CI->db->where("institute_id", $this->CI->session->userdata('company_id'));
        }
		if($this->CI->session->userdata('user_role') >2){
		$this->CI->db->where("branch_id", $this->CI->session->userdata('branch_id')); 
		}
		$this->CI->db->where("status <", 3);
        $this->CI->db->order_by('order_no','ASC');
        $query = $this->CI->db->get();
        return $query;
   }       
   function GetAjaxExamTypeList($company_id,$branch_id,$session_id,$version_id,$exam_type_id=0,$status=0){	
		$this->CI->db->select('*');
		$this->CI->db->from(EXAMTYPE_TBL);		
		if($company_id >0){
		   $this->CI->db->where("institute_id", $company_id); 
		}elseif($this->CI->session->userdata('user_role') >1){
		   $this->CI->db->where("institute_id", $this->CI->session->userdata('company_id')); 
		}
		if($branch_id >0){
		   $this->CI->db->where("branch_id", $branch_id); 
		}elseif($this->CI->session->userdata('user_role') >2){
		$this->CI->db->where("branch_id", $this->CI->session->userdata('branch_id')); 
		}
		if($session_id >0){
		   $this->CI->db->where("session_id", $session_id); 
		}
		if($version_id >0){
		   $this->CI->db->where("version_id", $version_id); 
		}
		if($status>0){
			$this->CI->db->where("status", $status);
		}else{
			$this->CI->db->where("status <", 3);
		}
		$this->CI->db->order_by('order_no','ASC');
		$query = $this->CI->db->get(); //echo $this->CI->db->last_query();
		$options = "<option value='0'>".$this->CI->lang->line("select")." ".$this->CI->lang->line("exam_type")."</option>";
		foreach($query->result() as $irow){	
		if($exam_type_id >0 && $exam_type_id==$irow->exam_type_id){$selected = "selected='selected'";}else{$selected = "";}
		$options.="<option  value='".$irow->exam_type_id."' $selected>".$irow->exam_name."</option>";
		}
		return $options;
   }    
   function GetAjaxPublishExamTypeList($company_id,$branch_id,$session_id,$version_id,$examcat_id=0,$exam_type_id=0,$status=0){	
		$this->CI->db->select('*');
		$this->CI->db->from(EXAMTYPE_TBL);		
		if($company_id >0){
		   $this->CI->db->where("institute_id", $company_id); 
		}elseif($this->CI->session->userdata('user_role') >1){
		   $this->CI->db->where("institute_id", $this->CI->session->userdata('company_id')); 
		}
		if($branch_id >0){
		    $this->CI->db->where("branch_id", $branch_id); 
		}elseif($this->CI->session->userdata('user_role') >2){
			$this->CI->db->where("branch_id", $this->CI->session->userdata('branch_id')); 
		}
		if($session_id >0){
		   $this->CI->db->where("session_id", $session_id); 
		}
		if($version_id >0){
		   $this->CI->db->where("version_id", $version_id); 
		}
		if($examcat_id >0){
		   $this->CI->db->where("examcat_id", $examcat_id); 
		}
		if($status>0){
			$this->CI->db->where("status", $status);
		}else{
			$this->CI->db->where("status <", 3);
		}
		$this->CI->db->order_by('order_no','ASC');
		$query = $this->CI->db->get(); echo $this->CI->db->last_query();
		$options = "<option value='0'>".$this->CI->lang->line("select")." ".$this->CI->lang->line("exam_type")."</option>";
		foreach($query->result() as $irow){	
		if($exam_type_id >0 && $exam_type_id==$irow->exam_type_id){$selected = "selected='selected'";}else{$selected = "";}
		$options.="<option  value='".$irow->exam_type_id."' $selected>".$irow->exam_name."</option>";
		}
		return $options;
   }   
   function GetAjaxExamList($company_id,$branch_id,$session_id,$version_id,$class_id,$group_id,$shift_id,$exam_type_id=0,$examcat_id=0){	
		$this->CI->db->select('e.*');
		$this->CI->db->from(EXAMTYPE_TBL." as e");
		$this->CI->db->join(EXAM_MARKS_BREAKDOWN_TBL.' AS m', 'm.exam_type_id=e.exam_type_id','LEFT');
		$this->CI->db->where("m.total_marks >", 0);
		$this->CI->db->where("e.status <", 3);
		$this->CI->db->where("m.status", 1); 		
		if($company_id >0){
		   $this->CI->db->where("e.institute_id", $company_id); 
		}elseif($this->CI->session->userdata('user_role') >1){
		   $this->CI->db->where("e.institute_id", $this->CI->session->userdata('company_id')); 
		}
		if($branch_id >0){
		   $this->CI->db->where("e.branch_id", $branch_id); 
		}elseif($this->CI->session->userdata('user_role') >2){
		$this->CI->db->where("e.branch_id", $this->CI->session->userdata('branch_id')); 
		}
		if($session_id >0){
		   $this->CI->db->where("e.session_id", $session_id); 
		}
		if($version_id >0){
		   $this->CI->db->where("e.version_id", $version_id); 
		}
		if($class_id >0){
		   $this->CI->db->where("m.class_id", $class_id); 
		}
		if($group_id >0){
		   $this->CI->db->where("m.group_id", $group_id); 
		}
		if($shift_id >0){
		   $this->CI->db->where("m.shift_id", $shift_id); 
		}
		if($examcat_id >0){
		   $this->CI->db->where("e.examcat_id", $examcat_id); 
		}
		$this->CI->db->group_by('e.exam_type_id');
		$this->CI->db->order_by('e.order_no','ASC');
		$query = $this->CI->db->get(); //echo $this->CI->db->last_query();
		$options = "<option value='0'>".$this->CI->lang->line("select")." ".$this->CI->lang->line("exam_type")."</option>";
		foreach($query->result() as $irow){	
		if($exam_type_id >0 && $exam_type_id==$irow->exam_type_id){$selected = "selected='selected'";}else{$selected = "";}
		$options.="<option  value='".$irow->exam_type_id."' $selected>".$irow->exam_name."</option>";
		}
		return $options;
   }   
   function GetAjaxExamSubjectList($company_id,$branch_id,$session_id,$version_id,$class_id,$group_id,$shift_id,$examtype_id,$subject_id=0){	
		$this->CI->db->select('sm.exam_id,sm.subject_id,s.subject_name');
        $this->CI->db->from(EXAM_MARKS_BREAKDOWN_TBL.' AS sm');
	    $this->CI->db->join(SUBJECT_TBL.' AS s', 's.subject_id=sm.subject_id','LEFT');
        $this->CI->db->where("sm.total_marks >", 0);
		$this->CI->db->where('sm.status', 1);	
		if($company_id >0){
		    $this->CI->db->where('sm.institute_id',$company_id);
		}elseif($this->CI->session->userdata('user_role') >1){
		   $this->CI->db->where("sm.institute_id", $this->CI->session->userdata('company_id')); 
		}
		if($branch_id >0){
		   $this->CI->db->where('sm.branch_id', $branch_id);
		}elseif($this->CI->session->userdata('user_role') >2){
		   $this->CI->db->where("sm.branch_id", $this->CI->session->userdata('branch_id')); 
		}
		if($session_id >0){
		   $this->CI->db->where("sm.sessions_id", $session_id); 
		}
		if($version_id >0){
		   $this->CI->db->where("sm.version_id", $version_id); 
		}
		if($class_id >0){
		   $this->CI->db->where("sm.class_id", $class_id); 
		}
		if($group_id >0){
		   $this->CI->db->where("sm.group_id", $group_id); 
		}
		if($shift_id >0){
		   $this->CI->db->where("sm.shift_id", $shift_id); 
		}
		if($examtype_id >0){
		   $this->CI->db->where("sm.exam_type_id", $examtype_id); 
		}		
		$this->CI->db->group_by('s.subject_id');
        $this->CI->db->order_by('s.subject_name','ASC');
		$query = $this->CI->db->get(); //echo $this->CI->db->last_query();
		$options = "<option value='0'>".$this->CI->lang->line("select")." ".$this->CI->lang->line("subject_name")."</option>";
		foreach($query->result() as $irow){	
		if($subject_id >0 && $subject_id==$irow->subject_id){$selected = "selected='selected'";}else{$selected = "";}
		$options.="<option  value='".$irow->exam_id."' $selected>".$irow->subject_name."</option>";
	    }
	    return $options;
   }
   
   function GetFeesList(){
	    $head_type = array(14,15,16);
		$this->CI->db->select('*');
		$this->CI->db->from(ACC_HEAD_TBL);
		if($this->CI->session->userdata('user_role') >1){
		$this->CI->db->where("company_id", $this->CI->session->userdata('company_id')); 
		}
		if($this->CI->session->userdata('user_role') >2){
			$this->CI->db->where("branch_id", $this->CI->session->userdata('branch_id')); 
		}
		$this->CI->db->where_in("head_type", $head_type); 
		$this->CI->db->where("status", 1); 
		$this->CI->db->order_by('account_name','ASC');
		$query = $this->CI->db->get();
        return $query;
   }
   function GetFineList(){
	    $head_type = array(16);
		$this->CI->db->select('*');
		$this->CI->db->from(ACC_HEAD_TBL);
		if($this->CI->session->userdata('user_role') >1){
		$this->CI->db->where("company_id", $this->CI->session->userdata('company_id')); 
		}
		if($this->CI->session->userdata('user_role') >2){
			$this->CI->db->where("branch_id", $this->CI->session->userdata('branch_id')); 
		}
		$this->CI->db->where_in("head_type", $head_type); 
		$this->CI->db->where("status", 1); 
		$this->CI->db->order_by('account_name','ASC');
		$query = $this->CI->db->get();
        return $query;
   }
   function GetProductList($head_type=0){
	    $head_types = array(12,13);
		$this->CI->db->select('*');
		$this->CI->db->from(ACC_HEAD_TBL);
		if($this->CI->session->userdata('user_role') >1){
		$this->CI->db->where("company_id", $this->CI->session->userdata('company_id')); 
		}
		if($this->CI->session->userdata('user_role') >2){
			$this->CI->db->where("branch_id", $this->CI->session->userdata('branch_id')); 
		}
		if($head_type >0){
			$this->CI->db->where("head_type", $head_type); 
		}else{
			$this->CI->db->where_in("head_type", $head_types); 
		}		
		$this->CI->db->where("status", 1); 
		$this->CI->db->order_by('account_name','ASC');
		$query = $this->CI->db->get();
        return $query;
   }
   
   
   function GetStoreList(){
		$this->CI->db->select('*');
		$this->CI->db->from(STORE_TBL);
		if($this->CI->session->userdata('user_role') >1){
		$this->CI->db->where("institute_id", $this->CI->session->userdata('company_id')); 
		}
		if($this->CI->session->userdata('user_role') >2){
			$this->CI->db->where("branch_id", $this->CI->session->userdata('branch_id')); 
		}		
		$this->CI->db->where("status", 1); 
		$this->CI->db->order_by('store_name','ASC');
		$query = $this->CI->db->get();
        return $query;
   }
   function GetCustomerHeadList(){
	    $head_type = array(15);
		$this->CI->db->select('*');
		$this->CI->db->from(ACC_HEAD_TBL);
		if($this->CI->session->userdata('user_role') >1){
		$this->CI->db->where("company_id", $this->CI->session->userdata('company_id')); 
		}
		if($this->CI->session->userdata('user_role') >2){
			$this->CI->db->where("branch_id", $this->CI->session->userdata('branch_id')); 
		}
		$this->CI->db->where_in("head_type", $head_type); 
		$this->CI->db->where("status", 1); 
		$this->CI->db->order_by('account_name','ASC');
		$query = $this->CI->db->get();
        return $query;
   }
   function GetConcessionHeadList(){
	    $head_type = array(17,18);
		$this->CI->db->select('*');
		$this->CI->db->from(ACC_HEAD_TBL);
		if($this->CI->session->userdata('user_role') >1){
		$this->CI->db->where("company_id", $this->CI->session->userdata('company_id')); 
		}
		if($this->CI->session->userdata('user_role') >2){
			$this->CI->db->where("branch_id", $this->CI->session->userdata('branch_id')); 
		}
		$this->CI->db->where_in("head_type", $head_type); 
		$this->CI->db->where("status", 1); 
		$this->CI->db->order_by('account_name','ASC');
		$query = $this->CI->db->get();
        return $query;
   }
   function GetFeePeriodList(){
		$this->CI->db->select('*');
		$this->CI->db->from(PERIOD_TBL);
		if($this->CI->session->userdata('user_role') >1){
		$this->CI->db->where("institute_id", $this->CI->session->userdata('company_id')); 
		}
		if($this->CI->session->userdata('user_role') >2){
			$this->CI->db->where("branch_id", $this->CI->session->userdata('branch_id')); 
		}
		$this->CI->db->where("status", 1); 
		$this->CI->db->order_by('period_no','ASC');
		$query = $this->CI->db->get();
        return $query;
   }
   
   function GetRegCustomerList(){ 	
		$this->CI->db->select('s.passport_no,s.visa_no,a.account_id,a.head_id,a.account_name,a.mobile');
		$this->CI->db->from(CLIENT_TBL." AS s");
		$this->CI->db->join(ACC_HEAD_TBL.' AS a', 'a.account_id=s.customer_id','LEFT');		
		if($this->CI->session->userdata('user_role') >1){
		$this->CI->db->where("s.company_id", $this->CI->session->userdata('company_id')); 
		}
		if($this->CI->session->userdata('user_role') >4){
		$this->CI->db->where("a.account_id", $this->CI->session->userdata('user_ref_id'));
		}
		if($this->CI->session->userdata('user_role') >2){
		$this->CI->db->where("s.branch_id", $this->CI->session->userdata('branch_id')); 
		}		
		$this->CI->db->where("s.status", 1);
		$this->CI->db->where("a.head_type", 11); //11=Student
		$this->CI->db->order_by('a.account_name','ASC');
		$query = $this->CI->db->get(); //echo $this->CI->db->last_query(); exit;
		
		return $query;
   }
   
   function GetAjaxRegCustomerList($institute_id,$branch_id,$customer_id=0,$passport_no=NULL,$visa_no=NULL){	
		$this->CI->db->select('s.passport_no,s.visa_no,a.account_id,a.head_id,a.account_name,a.mobile');
		$this->CI->db->from(CLIENT_TBL." AS s");
		$this->CI->db->join(ACC_HEAD_TBL.' AS a', 'a.account_id=s.customer_id','LEFT');
		if($this->CI->session->userdata('user_role') >1){
		$this->CI->db->where("s.company_id", $this->CI->session->userdata('company_id')); 
		}
		if($this->CI->session->userdata('user_role') >4){
		$this->CI->db->where("a.account_id", $this->CI->session->userdata('user_ref_id'));
		}
		if($institute_id >0){
		   $this->CI->db->where("s.company_id", $institute_id); 
		}
		if($this->CI->session->userdata('user_role') >2){
		$this->CI->db->where("a.branch_id", $this->CI->session->userdata('branch_id')); 
		}
		if($branch_id >0){
		   $this->CI->db->where("s.branch_id", $branch_id); 
		}
		if($passport_no!=""){
		   $this->CI->db->where("s.passport_no", $passport_no); 
		}
		if($visa_no !=""){
		   $this->CI->db->where("s.visa_no", $visa_no); 
		}
		$this->CI->db->where("s.status", 1); 
		$this->CI->db->order_by('a.account_name','ASC');
		$query = $this->CI->db->get(); //echo $this->CI->db->last_query();
		$options = "<option value='0'>".$this->CI->lang->line("select")." ".$this->CI->lang->line("customer")."</option>";
		foreach($query->result() as $irow){	
		if($customer_id >0 && $customer_id==$irow->account_id){$selected = "selected='selected'";}else{$selected = "";}
		$account_name =$irow->account_name.", CN: ".$irow->head_id;
		if($irow->passport_no!=""){
		 $account_name.=", Pass: ".$irow->passport_no.", Mob: ".$irow->mobile;
		}else{
		 $account_name.=", Mob - ".$irow->mobile; 
		}
		$options.="<option  value='".$irow->account_id."' $selected>".$account_name."</option>";
		}
		//==== Get Midea =====
		
	    $head_type = array(1);
		$this->CI->db->select('*');
		$this->CI->db->from(ACC_HEAD_TBL);
		if($this->CI->session->userdata('user_role') >1){
		$this->CI->db->where("company_id", $this->CI->session->userdata('company_id')); 
		}
		if($this->CI->session->userdata('user_role') >2){
			$this->CI->db->where("branch_id", $this->CI->session->userdata('branch_id')); 
		}
		$this->CI->db->where_in("head_type", $head_type); 
		$this->CI->db->where("status", 1); 
		$this->CI->db->order_by('account_name','ASC');
		$query = $this->CI->db->get();
		foreach($query->result() as $irow){	
		if($customer_id >0 && $customer_id==$irow->account_id){$selected = "selected='selected'";}else{$selected = "";}
		$account_name =$irow->account_name.", CN: ".$irow->head_id;
		if($irow->mobile!=""){
		 $account_name.=", Mob: ".$irow->mobile;
		}else{
		 $account_name.=", ".$irow->account_details; 
		}
		$options.="<option  value='".$irow->account_id."' $selected>".$account_name."</option>";
		}
		return $options;
   }   
   function GetAjaxFeePeriodList($institute_id,$branch_id,$period_num=0){	
		$this->CI->db->select('*');
		$this->CI->db->from(PERIOD_TBL);
		if($this->CI->session->userdata('user_role') >1){
		$this->CI->db->where("institute_id", $this->CI->session->userdata('company_id')); 
		}
		if($institute_id >0){
		   $this->CI->db->where("institute_id", $institute_id); 
		}
		if($this->CI->session->userdata('user_role') >2){
		$this->CI->db->where("branch_id", $this->CI->session->userdata('branch_id')); 
		}
		if($branch_id >0){
		   $this->CI->db->where("branch_id", $branch_id); 
		}
		$this->CI->db->where("status", 1); 
		$this->CI->db->order_by('period_no','ASC');
		$query = $this->CI->db->get(); //echo $this->CI->db->last_query();
		$options = "<option value='0'>".$this->CI->lang->line("select")." ".$this->CI->lang->line("fee_period")."</option>";
		foreach($query->result() as $irow){	
		if($period_num >0 && $period_num==$irow->period_no){$selected = "selected='selected'";}else{$selected = "";}
		if($this->CI->session->userdata('language')=="en"){
			$period_name = $irow->period_name_en;
		}else{
			$period_name = $irow->period_name_bn;
		}
		$options.="<option  value='".$irow->period_no."' $selected>".$period_name."</option>";
		}
		return $options;
   }       
   function GetAjaxBranchClassList($company_id,$branch_id,$class_id=0){	
        $this->CI->db->select('c.*');
        $this->CI->db->from(CLASS_TBL.' AS c');
	    $this->CI->db->join(CLASS_PERMISSION_TBL.' AS p', 'p.class_id=c.class_id','LEFT');
        if($this->CI->session->userdata('user_role') >1){
            $this->CI->db->where("c.institute_id", $this->CI->session->userdata('company_id'));
        }
        if($company_id >0){
            $this->CI->db->where("c.institute_id", $company_id);
        }
        if($this->CI->session->userdata('user_role') >2){
            $this->CI->db->where("p.branch_id", $this->CI->session->userdata('branch_id'));
        }
        if($branch_id >0){
           $this->CI->db->where("p.branch_id", $branch_id);
        }
		$this->CI->db->group_by('c.class_id');
		$this->CI->db->order_by('c.class_id','ASC');
		$query = $this->CI->db->get(); //echo $this->CI->db->last_query();
		$options = "<option value='0'>".$this->CI->lang->line("select")." ".$this->CI->lang->line("class_name")."</option>";
		foreach($query->result() as $irow){	
		if($class_id >0 && $class_id==$irow->class_id){$selected = "selected='selected'";}else{$selected = "";}
			$options.="<option  value='".$irow->class_id."' $selected>".$irow->class_name."</option>";
		}
		return $options;
   }       
   function GetAjaxClassGroupList($company_id,$class_id,$group_id=0){	
	
		$this->CI->db->select('g.*');
		$this->CI->db->from(GROUPS_TBL.' AS g');
		$this->CI->db->join(GROUPS_MAPPING_TBL.' AS m', 'm.group_id=g.group_id','LEFT');
		if($this->CI->session->userdata('user_role') >1){
		$this->CI->db->where("g.institute_id", $this->CI->session->userdata('company_id')); 
		}
		if($company_id >0){
		   $this->CI->db->where("g.institute_id", $company_id); 
		}
		if($class_id >0){
		   $this->CI->db->where("m.class_id", $class_id); 
		}
		$this->CI->db->group_by('g.group_id');
		$this->CI->db->order_by('g.group_id','ASC');
		$query = $this->CI->db->get(); //echo $this->CI->db->last_query();
		$options = "<option value='0'>".$this->CI->lang->line("select")." ".$this->CI->lang->line("group_name")."</option>";
		foreach($query->result() as $irow){	
		if($group_id >0 && $group_id==$irow->group_id){$selected = "selected='selected'";}else{$selected = "";}
		$options.="<option  value='".$irow->group_id."' $selected>".$irow->group_name."</option>";
		}
		
		return $options;
   }
    
   function GetModuleList(){
		$this->CI->db->select('*');
		$this->CI->db->from(MODULE_TBL);
		if($this->CI->session->userdata('user_role') >1){
		$this->CI->db->where("company_id", $this->CI->session->userdata('company_id')); 
		}
		$this->CI->db->order_by('module_name','ASC');
		$query = $this->CI->db->get();
		return $query;
    }
    function GetMenuList(){
        $this->CI->db->select('*');
        $this->CI->db->from(MENU_TBL);
		if($this->CI->session->userdata('user_role') >1){
		$this->CI->db->where("company_id", $this->CI->session->userdata('company_id')); 
		}
        $this->CI->db->order_by('menu_name','ASC');
        $query = $this->CI->db->get();
        return $query;
    }
    function GetDivisionList(){
        $this->CI->db->select('*');
        $this->CI->db->from(DIVISION_TBL);
		if($this->CI->session->userdata('user_role') >1){
		$this->CI->db->where("company_id", $this->CI->session->userdata('company_id')); 
		}
		$this->CI->db->where("division_status", 1); 
        $this->CI->db->order_by('division_name','ASC');
        $query = $this->CI->db->get();
        return $query;
    }
    function GetAreaList(){
        $this->CI->db->select('*');
        $this->CI->db->from(AREA_TBL);
		if($this->CI->session->userdata('user_role') >1){
		$this->CI->db->where("company_id", $this->CI->session->userdata('company_id')); 
		}
        $this->CI->db->order_by('area_name','ASC');
        $query = $this->CI->db->get();
        return $query;
    }
    function GetEmployeeList($employee_type=0){
        $this->CI->db->select('*');
        $this->CI->db->from(EMPLOYEE_TBL);
		if($employee_type>0){
		$this->CI->db->where('employee_type',$employee_type);
		}
		$this->CI->db->where('status',1);
        $this->CI->db->order_by('employee_name','ASC');
        $query = $this->CI->db->get();
        return $query;
    }
	function GetAccountList($head_type=NULL){		
        $this->CI->db->select('*');
        $this->CI->db->from(ACC_HEAD_TBL);		
		if(is_array($head_type)){
			$this->CI->db->where_in("head_type", $head_type);
		}elseif($head_type >0){
			$this->CI->db->where("head_type", $head_type);
		}
		$this->CI->db->where('status',1);
        $this->CI->db->order_by('account_name','ASC');
        $query = $this->CI->db->get();
        return $query;
    }
	
    function GetUserGroupList(){
        $this->CI->db->select('*');
        $this->CI->db->from(USER_ROLE_TBL);
        $this->CI->db->where('role_status',1);
        $this->CI->db->order_by('role_name','ASC');
        $query = $this->CI->db->get();
        return $query;
    }
            
    function GetAjaxSubAccountList($company_id,$top_head,$sub_head=0){
        $this->CI->db->select('*');
        $this->CI->db->from(SUB_HEAD_L1_TBL);
        if($company_id >0){
            $this->CI->db->where("company_id", $company_id);
        }else{
            $this->CI->db->where("company_id", $this->CI->session->userdata('company_id'));
            }
        if($top_head >0){
            $this->CI->db->where("parents_id", $top_head);
        }
        $this->CI->db->order_by('sub_head_name','ASC');
        $query = $this->CI->db->get(); //echo $this->CI->db->last_query();
        $options = "<option value='0'>".$this->CI->lang->line("select")." ".$this->CI->lang->line("sl_level")."-".$this->CI->lang->line("1")."</option>";
	    foreach($query->result() as $irow){
            if($sub_head >0 && $sub_head==$irow->sub_id){ $selected = "selected='selected'"; }else{ $selected = ""; }
            $options.="<option  value='".$irow->sub_id."' $selected>".$irow->sub_head_name."</option>";
        }
	    return $options;
    }
            
    function GetAjaxChildAccountList($company_id,$top_head,$sub_head,$child_head=0){
		$this->CI->db->select('*');
		$this->CI->db->from(SUB_HEAD_L2_TBL);
		if($company_id >0){
			$this->CI->db->where("company_id", $company_id);
		}else{
			$this->CI->db->where("company_id", $this->CI->session->userdata('company_id')); 
			}
		if($top_head >0){
			$this->CI->db->where("group_id", $top_head);
		}
		if($sub_head >0){
			$this->CI->db->where("sub_id", $sub_head);
		}
		$this->CI->db->order_by('subsidiary_name2','ASC');
		$query = $this->CI->db->get(); //echo $this->CI->db->last_query();
		$options = "<option value='0'>".$this->CI->lang->line("select")." ".$this->CI->lang->line("sl_level")."-".$this->CI->lang->line("2")."</option>";
		foreach($query->result() as $irow){	
		if($child_head >0 && $child_head==$irow->sub2_id){ 
			$selected = "selected='selected'"; 
		}else{ $selected = ""; }
		$options.="<option  value='".$irow->sub2_id."' $selected>".$irow->subsidiary_name2."</option>";
		}
		return $options;
    }        
    function GetAjaxSubChildAccountList($company_id,$group_id,$sub1_id,$sub2_id,$sub3_id=0){		
		$this->CI->db->select('*');
		$this->CI->db->from(SUB_HEAD_L3_TBL);
		if($company_id >0){
			$this->CI->db->where("company_id", $company_id);
		}else{
			$this->CI->db->where("company_id", $this->CI->session->userdata('company_id')); 
			}
		if($group_id >0){
			$this->CI->db->where("group_id", $group_id);
		}
		if($sub1_id >0){
			$this->CI->db->where("sub1_id", $sub1_id);
		}
		if($sub2_id >0){
			$this->CI->db->where("sub2_id", $sub2_id);
		}
		$this->CI->db->order_by('subsidiary_name3','ASC');
		$query = $this->CI->db->get(); //echo $this->CI->db->last_query();
		$options = "<option value='0'>".$this->CI->lang->line("select")." ".$this->CI->lang->line("sl_level")."-".$this->CI->lang->line("3")."</option>";
		foreach($query->result() as $irow){	
		if($sub3_id >0 && $sub3_id==$irow->sub3_id){ 
			$selected = "selected='selected'"; 
		}else{ $selected = ""; }
		$options.="<option  value='".$irow->sub3_id."' $selected>".$irow->subsidiary_name3."</option>";
		}
		return $options;
    }
    function GetAccountInfo($head_type,$group_id,$sub1_id,$sub2_id,$sub3_id=0,$company_id=0){		
        $this->CI->db->select('*');
        $this->CI->db->from(ACC_HEAD_TBL);
		if($company_id >0){
			$this->CI->db->where("company_id", $company_id);
		}else{
			$this->CI->db->where("company_id", $this->CI->session->userdata('company_id')); 
			}
		if($group_id >0){
			$this->CI->db->where("group_id", $group_id);
		}
		if($sub1_id >0){
			$this->CI->db->where("subsidiary_level1", $sub1_id);
		}
		if($sub2_id >0){
			$this->CI->db->where("subsidiary_level2", $sub2_id);
		}
		if($sub3_id >0){
			$this->CI->db->where("subsidiary_level3", $sub3_id);
		}
		if(is_array($head_type)){
			$this->CI->db->where_in("head_type", $head_type);
		}elseif($head_type >0){
			$this->CI->db->where("head_type", $head_type);
		}
		$this->CI->db->where('status',1);
        $this->CI->db->order_by('account_name','ASC');
        $query = $this->CI->db->get();
        return $query;
    }    
    function GetGroupLedgerList(){	
		$this->CI->db->select('*');
		$this->CI->db->from(GROUP_HEAD_TBL);
		$this->CI->db->where("status", 1);		
		$this->CI->db->order_by('group_id','ASC');
		$query = $this->CI->db->get();
		return $query;
    }
    function GetCollectionFeeList(){
        $this->CI->db->select('*');
        $this->CI->db->from(ACC_HEAD_TBL);
        $this->CI->db->where('head_type',14);
        $this->CI->db->where('status',1);
        $this->CI->db->order_by('account_name,subsidiary_level2,subsidiary_level3','ASC');
        $query = $this->CI->db->get();
        return $query;
    }
    //======= =========
    function GetAjaxAccountList(){
		$top_head  =$this->input->post('top-head');
		$sub_head  =$this->input->post('sub-head');
		$this->CI->db->select('*');
		$this->CI->db->from(ACC_HEAD_TBL);
		if($top_head >0){
			$this->CI->db->where("top_head", $top_head);
		}	
		$this->CI->db->order_by('account_name','ASC');
		$query = $this->CI->db->get();
		$options = "<option value='0'>Select Sub Head</option>";
		foreach($query->result() as $irow){	
		if($sub_head >0 && $sub_head==$irow->account_id){$selected="selected='selected'";}else{$selected= "";}
		$options.="<option  value='".$irow->account_id."' $selected>".$irow->account_name."</option>";
		}
		return $options;
    }	
    
    function GetAjaxSpecialAccountList($category){
		$top_head  =$this->input->post('top-head');
		$sub_head  =$this->input->post('sub-head');
		$this->CI->db->select('*');
		$this->CI->db->from(ACC_HEAD_TBL);
		if($top_head >0){
			$this->CI->db->where("top_head", $top_head);
		}
		$this->CI->db->where("category", $category); 
		//1=Customer,2=Agent,3=Supplier,4=Cash,5=Bank,6=Tax,7=Sales,8=Revenue,9=Expense		
		$this->CI->db->order_by('account_name','ASC');
		$query = $this->CI->db->get();
		$options = "<option value='0'>Select Sub Head</option>";
		foreach($query->result() as $irow){	
		if($sub_head >0 && $sub_head==$irow->account_id){$selected="selected='selected'";}else{$selected= "";}
		$options.="<option  value='".$irow->account_id."' $selected>".$irow->account_name."</option>";
		}
		return $options;
    }
    function GetEmployeeAccountList(){
		$head_id  =$this->input->post('head-id');
		$this->CI->db->select('*');
		$this->CI->db->from(ACC_HEAD_TBL);
		$this->CI->db->where("category", 10); // Employee	
		$this->CI->db->order_by('account_name','ASC');
		$query = $this->CI->db->get(); //echo $this->CI->db->last_query();
		
		return $query;
    }
    function GetAjaxEmployeeAccountList(){
		$head_id  =$this->input->post('head-id');
		$this->CI->db->select('*');
		$this->CI->db->from(ACC_HEAD_TBL);
		$this->CI->db->where("category", 10); // Employee	
		$this->CI->db->order_by('account_name','ASC');
		$query = $this->CI->db->get(); //echo $this->CI->db->last_query();
		$options = "<option value='0'>Select Employee Name</option>";
		foreach($query->result() as $irow){	
		if($head_id >0 && $head_id==$irow->account_id){$selected="selected='selected'";}else{$selected= "";}
		$options.="<option  value='".$irow->account_id."' $selected>".$irow->account_name."</option>";
		}
		return $options;
    }
    function GetAjaxEmployeeList($employee_id,$company_id,$branch_id,$department_id,$section_id,$shift_id){
	
		$this->CI->db->select('employee_id,card_id,employee_name,designation');
		$this->CI->db->from(EMPLOYEE_TBL);
		$this->CI->db->where("company_id", $company_id);
		$this->CI->db->where("branch_id", $branch_id);
		$this->CI->db->where("department_id", $department_id);
		$this->CI->db->where("section_id", $section_id); 
		$this->CI->db->where("shift_id", $shift_id); 	
		$this->CI->db->order_by('employee_name','ASC');
		$query = $this->CI->db->get(); //echo $this->CI->db->last_query();
		$options = "<option value='0'>Select Employee Name</option>";
		foreach($query->result() as $irow){	
		if($employee_id >0 && $employee_id==$irow->employee_id){$selected="selected='selected'";}else{$selected= "";}
		$options.="<option  value='".$irow->employee_id."' $selected>".$irow->card_id.", ".$irow->employee_name."</option>";
		}
		return $options;
    }   
    function GetSubjectList(){
		$this->CI->db->select('*');
		$this->CI->db->from(SUBJECT_TBL);	
		if($this->CI->session->userdata('user_role') >1){
		$this->CI->db->where("institute_id", $this->CI->session->userdata('company_id')); 
		}
		$this->CI->db->where("status", 1);		
		$this->CI->db->order_by('subject_name','ASC');
		$query = $this->CI->db->get(); //echo $this->CI->db->last_query();
		return $query;
    }  
    function GetQualificationList(){
		$this->CI->db->select('*');
		$this->CI->db->from(QUALIFICATION_TBL);	
		if($this->CI->session->userdata('user_role') >1){
		$this->CI->db->where("institute_id", $this->CI->session->userdata('company_id')); 
		}
		$this->CI->db->where("status", 1);		
		$this->CI->db->order_by('qualification_name','ASC');
		$query = $this->CI->db->get(); //echo $this->CI->db->last_query();
		return $query;
    }
	function GetTotalMonthDays($month,$year){
		$DSQL  = "SELECT COUNT(`date_field`) as total_days FROM ".MONTH_DAYS_TBL." WHERE MONTH(`date_field`)='".$month."' AND YEAR(`date_field`)='".$year."'";
		$query = $this->CI->db->query($DSQL);
		if($query->num_rows() >0){			
			return $query->row()->total_days;
		}else{
			return 0;
		}
	}
	function GetTotalWeekend($month,$year,$day1,$day2=NULL){
		$DSQL = "SELECT COUNT(`date_field`) as total_weekend FROM ".MONTH_DAYS_TBL." WHERE MONTH(`date_field`)=".$month." AND YEAR(`date_field`)=".$year." AND `day_name` LIKE '%".$day1."%'";
		if($day2 !=""){
		$DSQL.=" OR `day_name` LIKE '%".$day2."%'";
		}
		$query = $this->CI->db->query($DSQL);
		if($query->num_rows() >0){			
			return $query->row()->total_weekend;
		}else{
			return 0;
		}
	}
	function GetTotalPresents($institute_id,$branch_id,$account_id,$month,$year){
		$DSQL = "SELECT COUNT(`attendance_date`) as total_present FROM ".ATTENDANCE_TBL." WHERE institute_id = ".$institute_id." AND branch_id = ".$branch_id." AND account_id = ".$account_id." AND MONTH(`attendance_date`)=".$month." AND YEAR(`attendance_date`)=".$year." AND `present`=1";
		$query = $this->CI->db->query($DSQL);
		if($query->num_rows() >0){			
			return $query->row()->total_present;
		}else{
			return 0;
		}
	}
	 function GetPeriodEnList(){
        $this->CI->db->select('*');
        $this->CI->db->from(PERIOD_TBL);
		if($this->CI->session->userdata('user_role') >1){
		$this->CI->db->where("period_id", $period_id); 
		}
        $this->CI->db->order_by('period_id','ASC');
        $query = $this->CI->db->get();
        return $query;
    }
     function GetPeriodBnList(){
        $this->CI->db->select('*');
        $this->CI->db->from(PERIOD_TBL);
		if($this->CI->session->userdata('user_role') >1){
		$this->CI->db->where("period_id", $period_id); 
		}
        $this->CI->db->order_by('period_id','ASC');
        $query = $this->CI->db->get();
        return $query;
    }
	   
   function GetAjaxClassSubjectList($company_id,$branch_id,$session_id,$version_id,$class_id,$group_id,$subject_id=0){	
		$this->CI->db->select('sm.subject_id,s.subject_name');
        $this->CI->db->from(CLASS_SUBJECT_MAPPING_TBL.' AS sm');
	    $this->CI->db->join(SUBJECT_TBL.' AS s', 's.subject_id=sm.subject_id','LEFT');
		$this->CI->db->where('sm.status', 1);	
		if($company_id >0){
		    $this->CI->db->where('sm.institute_id',$company_id);
		}elseif($this->CI->session->userdata('user_role') >1){
		   $this->CI->db->where("sm.institute_id", $this->CI->session->userdata('company_id')); 
		}
		if($branch_id >0){
		   $this->CI->db->where('sm.branch_id', $branch_id);
		}elseif($this->CI->session->userdata('user_role') >2){
		   $this->CI->db->where("sm.branch_id", $this->CI->session->userdata('branch_id')); 
		}
		if($session_id >0){
		   $this->CI->db->where("sm.session_id", $session_id); 
		}
		if($version_id >0){
		   $this->CI->db->where("sm.version_id", $version_id); 
		}
		if($class_id >0){
		   $this->CI->db->where("sm.class_id", $class_id); 
		}
		if($group_id >0){
		   $this->CI->db->where("sm.group_id", $group_id); 
		}		
		$this->CI->db->group_by('s.subject_id');
        $this->CI->db->order_by('s.subject_name','ASC');
		$query = $this->CI->db->get(); //echo $this->CI->db->last_query();
		$options = "<option value='0'>".$this->CI->lang->line("select")." ".$this->CI->lang->line("subject_name")."</option>";
		foreach($query->result() as $irow){	
		if($subject_id >0 && $subject_id==$irow->subject_id){$selected = "selected='selected'";}else{$selected = "";}
		$options.="<option  value='".$irow->subject_id."' $selected>".$irow->subject_name."</option>";
	    }
	    return $options;
   }
   function GetActivityTypeList(){
		$this->CI->db->select('*');
		$this->CI->db->from(ACTIVITY_TYPE_TBL);
		$this->CI->db->where("status", 1); 
		$this->CI->db->order_by('order_no','ASC');
		$query = $this->CI->db->get();
		return $query;
   }
}
