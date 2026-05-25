<?php 
class Salarysheet_model extends CI_Model {
		
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
	//===== Start Monthly Salary Sheet =========
    function GetSalarySheetRecordGrid(){
		$menu_slug= $this->uri->segment(1);
		$this->load->model('Site_model');
		$hasGenRPM   = $this->Site_model->hasOptionPermission($menu_slug,"Generate");
		$hasEditPM   = $this->Site_model->hasOptionPermission($menu_slug,"Edit");
        $institute_id	    =$this->input->post('company-id');
		$branch_id	    	=$this->input->post('branch-id');
		$session_id			=$this->input->post('session-id');
		$version_id			=$this->input->post('version-id');
		$salary_month		=$this->input->post('month-name');
		$salary_year		=$this->input->post('salary-year');
		$withbonus		    =$this->input->post('with-bonus');
		$search_type		=$this->input->post('search-type'); // 0=Search, 1=Generate, 2=Approved
		$current_month		=date("m");
        if(empty($institute_id)){$institute_id = $this->session->userdata('company_id');}
        $from	=$this->input->post('from');
        $to	=$this->input->post('to');
        if($from==""){ $from=0;} if($to==""){ $to=100;}
        $this->db->select('a.*,i.company_name');
        $this->db->from(EMPLOYEE_TBL.' AS a');
	    $this->db->join(COMPANY_SETTINGS_TBL.' AS i', 'i.company_id=a.company_id','LEFT');
	    $this->db->join(ACC_HEAD_TBL.' AS s', 's.account_id=a.hrm_employee_id','LEFT');
        if($institute_id >0){
            $this->db->where('i.company_id',$institute_id);
        }			
		$this->db->where('a.branch_id', $branch_id);
		$this->db->where('a.status',1);
        $this->db->group_by('a.hrm_employee_id');
        $this->db->order_by('a.hrm_employee_id','ASC');
        $this->db->limit($to,$from);
        $query = $this->db->get(); //print  $this->db->last_query();
        $totalrecord = $this->GetTotalSalarySheetRecord();
        $perPage=100; $Pagination="";
        if($totalrecord >0){
            $Pagination = $this->getPagination($totalrecord,$perPage);
        } //print  $this->db->last_query();
        
		$cwidth		= (14/2);
        $fwidth		= (47/6);
        $awidth		= (48/8);
		$dwidth		= (22/4);
		$GTFixPayble=0; $GTGross=0; $GTGPayble=0; $GTPFD=0; $PerdayTada=0; $Tada_Absent=0;
    	$GTLD=0; $GTITD=0; $GTAFD=0;  $GTGD=0; $GTA=0; $GTAD=0;	$GTAP=0; $GTADJ=0; $GTNET=0;
    			    
        $salarymonth=""; $income_tax_amount=0; $adjust_payable=0;
        if($salary_month=="01"){$salarymonth ="Jan";}elseif($salary_month=="02"){$salarymonth ="Feb";}
        elseif($salary_month=="03"){$salarymonth ="Mar";}elseif($salary_month=="04"){$salarymonth ="Apr";}
        elseif($salary_month=="05"){$salarymonth ="May";}elseif($salary_month=="06"){$salarymonth ="Jun";}
        elseif($salary_month=="07"){$salarymonth ="Jul";}elseif($salary_month=="08"){$salarymonth ="Agu";}
        elseif($salary_month=="09"){$salarymonth ="Sep";}elseif($salary_month=="10"){$salarymonth ="Oct";}
        elseif($salary_month=="11"){$salarymonth ="Nov";}elseif($salary_month=="12"){$salarymonth ="Dec";}
		$this->db->select('branch_name,branch_address');
		$this->db->from(BRANCH_TBL);
		$this->db->where('id', $branch_id);
		$bquery = $this->db->get();
		if($bquery->num_rows() >0){
		  $brow = $bquery->row();
		  $grid = "<div class='text-center'><h2>".$brow->branch_name."</h2>".$brow->branch_address."<br><strong>Salary statement for the month of $salarymonth ".$salary_year."</strong></div><br>";
		}
		if($withbonus==0){
        $grid .= "<table width='238%' border='0' class='table table-responsive table-bordered table-hover custab'>
		<thead>
			<tr class='bg-light'>
			<th width='1%' rowspan='2' class='align-middle text-left'>".$this->lang->line("sl")."</th>
			<th width='13%' rowspan='2' class='align-middle text-left'>".$this->lang->line("employee_name")."</th>
			<th width='14%' colspan='2' class='text-center'>".$this->lang->line("fixed_compensation")."</th>
			<th width='8%' rowspan='2' class='align-middle'>".$this->lang->line("fixed_payble")."</th>
			<th width='47%' colspan='6' class='text-center'>".$this->lang->line("bank_salary")."</th>
			<th width='8%' rowspan='2' class='align-middle'>".$this->lang->line("gross_salary")."</th>
			<th width='8%' rowspan='2' class='align-middle'>".$this->lang->line("total_salary")."</th>
			<th width='48%' colspan='7' class='text-center'>".$this->lang->line("attendance")."</th>
			<th width='22%' colspan='4' class='text-center'>".$this->lang->line("salary_deduction")."</th>
			<th width='7%' rowspan='2' class='align-middle'>".$this->lang->line("gross_deduction")."</th>
			<th width='8%' rowspan='2' class='align-middle'>".$this->lang->line("tnt_allowance")."</th>
			<th width='20%' colspan='2' class='text-center'>T&T</th>
			<th width='8%' rowspan='2' class='align-middle'>T&T Payable</th>
			<th width='8%' rowspan='2' class='align-middle'>".$this->lang->line("adjust_salary")."</th>
			<th width='8%' rowspan='2' class='align-middle'>".$this->lang->line("net_salary")."</th>
			<th width='10%' rowspan='2' class='align-middle'>".$this->lang->line("remarks")."</th>
			</tr>
			<tr class='bg-light'>
			";			
			$grid.= "
			<th width='".$cwidth."%' class='text-center'>C.S</th>
			<th width='".$cwidth."%' class='text-center'>O.P</th>
			
			<th width='".$fwidth."%' class='text-center'>B.S</th>
			<th width='".$fwidth."%' class='text-center'>H.R</th>
			<th width='".$fwidth."%' class='text-center'>M.A</th>
			<th width='".$fwidth."%' class='text-center'>C.A</th>
			<th width='".$fwidth."%' class='text-center'>O.A</th>
			<th width='".$fwidth."%' class='text-center'>F.B</th>
			
			<th width='".$fwidth."%' class='text-center'>W.D</th>
			<th width='".$fwidth."%' class='text-center'>P.D</th>
			<th width='".$fwidth."%' class='text-center'>L.D</th>
			<th width='".$fwidth."%' class='text-center'>A.B</th>
			<th width='".$fwidth."%' class='text-center'>L.T</th>
			<th width='".$fwidth."%' class='text-center'>E.O</th>
			<th width='".$fwidth."%' class='text-center'>S.D</th>
			
			<th width='".$dwidth."%' class='text-center'>P.F</th>
			<th width='".$dwidth."%' class='text-center'>L.A</th>
			<th width='".$dwidth."%' class='text-center'>I.T</th>
			<th width='".$dwidth."%' class='text-center'>D.S</th>
			
			<th width='".$cwidth."%' class='text-center'>TD</th>
			<th width='".$cwidth."%' class='text-center'>DDT</th>";
			$grid.= "
			  </tr>
			</thead>";
        $i=1; $salary_id=0; $total_days=0; $working_day=0; $present=0; $full_leave=0; $holiday=0; $late=0; $early_out=0; $absent=0; $salary_day=0;
        $MSQL   ="SELECT * FROM ".MONTH_DAYS_TBL." WHERE MONTH(`date_field`) ='$salary_month'"; 
	    $mquery = $this->db->query($MSQL);
	    if($mquery->num_rows() >0){
	     $total_days=$mquery->num_rows();    
	     foreach($mquery->result() as $mrow){
	        if($mrow->day_name!="Friday" && $mrow->is_holiday!=1){
	         $working_day+=1;
	        } 
	     }
	    }
        foreach($query->result() as $row){
            $present=0; $full_leave=0; $holiday=0; $late=0; $early_out=0; $absent=0; $salary_day=0;
            $employee_id 	= $row->hrm_employee_id;
            $PSQL   ="SELECT * FROM ".ATTENDANCE_TBL." WHERE account_id = '$employee_id' AND MONTH(`attendance_date`) ='$salary_month' AND day_type IN(1,6) AND present=1"; 
    	    $pquery = $this->db->query($PSQL); //if($employee_id==226){echo $PSQL;}
    	    if($pquery->num_rows() >0){
    	     $present=$pquery->num_rows();    
    	     foreach($pquery->result() as $prow){
    	        if($prow->late==1){
    	         $late+=1;
    	        }elseif($prow->early_leave==1){
    	         $early_out+=1;
    	        } 
    	     }
    	    }
    	    $late_absent = 0;
    	    if($this->session->userdata('one_day_deduction')){
    	        $late_deduction_by= $this->session->userdata('one_day_deduction');
    	    }else{
    	        $late_deduction_by=3;
    	    }
    	    if($late >=3){
    	      $late_absent = intval($late/$late_deduction_by);
    	    }
            $LSQL   ="SELECT * FROM ".ATTENDANCE_TBL." WHERE account_id = '$employee_id' AND MONTH(`attendance_date`) ='$salary_month' AND day_type IN(4) AND present=0"; 
    	    $lquery = $this->db->query($LSQL);
    	    if($lquery->num_rows() >0){
    	     $full_leave=$lquery->num_rows();
    	    }
    	    $early_out =0; // temporary 0
    	    
    	    $absent     = ($working_day - ($full_leave+$present));
    	    $salary_day = (($full_leave+$present)-($late_absent+$early_out));
    	    $Tada_Absent= ($absent + $full_leave);
            $grid.="<tr class='default'>
			<td>".$i."</td>
			<td>".$row->employee_name."<br>".$row->card_id."</td>";
			
			$checked =""; $total_salary=0;
			$CSQL = "SELECT * FROM ".SALARY_SHEET_TBL." WHERE institute_id=".$institute_id." AND branch_id=".$branch_id." AND session_id=".$session_id." AND salary_month=".$salary_month." AND salary_year=".$salary_year." AND employee_id=".$employee_id;
			$CRES = $this->db->query($CSQL);
			if($CRES->num_rows() >0){
				$salary_id 				= $CRES->row()->salary_sheet_id;
				$cash_salary 			= $CRES->row()->cash_salary;
				$others_payble 	        = $CRES->row()->others_payble;
				$total_fix_payble 	    = ($CRES->row()->cash_salary + $CRES->row()->others_payble);
				
				$basic_salary 			= $CRES->row()->basic_salary;
				$house_rent 			= $CRES->row()->house_rent;
				$medical_allowance 		= $CRES->row()->medical_allowance;
				$transport_allowance 	= $CRES->row()->transport_allowance;
				$communication			= 0; //$row->communication_allowance;
				$others_allowance 		= $CRES->row()->others_allowance;
				$festival_bonus 		= $CRES->row()->festival_bonus;
				$total_gross 			= $CRES->row()->total_gross;
				$gross_payble           = ($total_fix_payble + $total_gross);
				$pf_deduction 			= $CRES->row()->pf;
				$loan_deduction 		= $CRES->row()->loan;
				if($CRES->row()->income_tax >0){
				$income_tax_amount 		= $CRES->row()->income_tax;
				$income_tax_amount      = number_format($income_tax_amount, 2, '.', '');
				}else{
				$income_tax_amount	  	= (($row->basic_salary/100)*$row->income_tax);
				$income_tax_amount      = number_format($income_tax_amount, 2, '.', '');
				}
				$absent_deduction 		= $CRES->row()->absent_fine;
				$gross_deduction 		= $CRES->row()->total_deduction;
				
				$tnt_allowance 			= $CRES->row()->tnt_allowance;
				$tnt_td 			    = $CRES->row()->tnt_td;
				$tnt_ddt 			    = $CRES->row()->tnt_ddt;
				
				$tnt_payable 			= ($tnt_allowance-$tnt_ddt);
				
				$adjust_payable         = $CRES->row()->adjust_payable;
				$net_salary 			= $CRES->row()->net_salary;
				$status					= $CRES->row()->status;
				$remarks                = $CRES->row()->remarks;
			}else{ 
				$salary_id              = 0;
				$cash_salary 			= $row->cash_salary;
				$others_payble 	        = $row->others_payble;
				$total_fix_payble 	    = ($row->cash_salary+$row->others_payble);
				
				$basic_salary 			= $row->basic_salary;
				$house_rent 			= $row->houserent_allowance;
				$medical_allowance 		= $row->medical_allowance;
				$transport_allowance 	= $row->transport_allowance;
				$communication			= 0;//$row->communication_allowance;
				$others_allowance 		= $row->others_allowance;
				$festival_bonus 		= (($row->basic_salary/100)*$row->festival_bonus);
				$festival_bonus         = number_format($festival_bonus, 2, '.', '');
				$total_gross 			= ($basic_salary+$house_rent+$transport_allowance+$medical_allowance+$communication+$others_allowance+$festival_bonus);
				$gross_payble           = ($total_gross + $total_fix_payble);
				// temporary 30
				//$per_day_salary       = ($gross_payble/$total_days);
				$per_day_salary         = ($gross_payble/30);
				$per_day_salary         = number_format($per_day_salary, 2, '.', '');
				
				$pf_deduction 			= $row->provident_fund;
				$loan_deduction 		= $row->loan_and_adv;
				if($row->income_tax_amount >0){
				$income_tax_amount 		= $row->income_tax_amount;
				$income_tax_amount      = number_format($income_tax_amount, 2, '.', '');
				}else{
				$income_tax_amount	  	= (($row->basic_salary/100)*$row->income_tax);
				$income_tax_amount      = number_format($income_tax_amount, 2, '.', '');
				}
				
				$absent_deduction 		= (($absent+$late_absent+$early_out) * $per_day_salary); 
				$gross_deduction 		= ($pf_deduction+$loan_deduction+$income_tax_amount+$absent_deduction);
				
				$tnt_allowance 			= $row->tnt_allowance;
				if($row->tnt_allowance >0){
    				$PerdayTada         = ($row->tnt_allowance / 30);
    				$tnt_td 			= $Tada_Absent;
    				$tnt_ddt 			= ($PerdayTada * $Tada_Absent);
				}else{
				    $tnt_td 			= 0;
				    $tnt_ddt 			= 0;
				}
				$tnt_payable 			= ($tnt_allowance-$tnt_ddt);
				$adjust_payable         = 0;
				$net_salary 			= (($total_fix_payble+$total_gross+$tnt_payable+$adjust_payable) - $gross_deduction);
				$remarks                = "";
				//===== Start Insert =====
				if($hasGenRPM){ //echo "$salary_month < $current_month ";
				    $salary_day=30; // temporary
					if(($salary_month <=11) && ($salary_month <= $current_month)){
					$salary_id = $this->GenerateSalaryId($employee_id,$working_day,$present,$full_leave,$absent,$late,$early_out,$salary_day,$cash_salary,$tnt_allowance,$others_payble,$total_fix_payble,$basic_salary,$house_rent,$medical_allowance,$transport_allowance,$others_allowance,$festival_bonus,$total_gross,$pf_deduction,$loan_deduction,$income_tax_amount,$absent_deduction,$gross_deduction,$tnt_td,$tnt_ddt,$tnt_payable,$adjust_payable,$net_salary,$withbonus,$remarks);
					}elseif(($salary_month ==12) && ($salary_month > $current_month)){
					$salary_id = $this->GenerateSalaryId($employee_id,$working_day,$present,$full_leave,$absent,$late,$early_out,$salary_day,$cash_salary,$tnt_allowance,$others_payble,$total_fix_payble,$basic_salary,$house_rent,$medical_allowance,$transport_allowance,$others_allowance,$festival_bonus,$total_gross,$pf_deduction,$loan_deduction,$income_tax_amount,$absent_deduction,$gross_deduction,$tnt_td,$tnt_ddt,$tnt_payable,$adjust_payable,$net_salary,$withbonus,$remarks);
					}
				}
				$status					= 0;
			}
			if($CRES->num_rows()>0 && $CRES->row()->bill_id ==0 && $CRES->row()->status ==0 ){
				if($hasGenRPM){
				    $salary_day=30; // temporary
				    $total_fix_payble  = ($CRES->row()->cash_salary+$CRES->row()->others_payble);
				    
    			    $GTFixPayble+=$total_fix_payble; $GTGross+=$total_gross; $GTGPayble+=$gross_payble; $GTPFD+=$pf_deduction;
    			    $GTLD+=$loan_deduction; $GTITD+=$income_tax_amount; $GTAFD+=$absent_deduction;  $GTGD+=$gross_deduction; 
    			    $GTA+=$tnt_allowance; $GTAD+=$tnt_ddt;	$GTAP+=$tnt_payable; $GTADJ+=$adjust_payable; $GTNET+=$net_salary;
    			    
					$grid.= "
					<td class='text-center'><input type='text' style='width: 68px;' class='form-control grid-control' value='".$cash_salary."' onfocusout=setSalary('".$institute_id."','".$branch_id."','".$session_id."','".$employee_id."','".$salary_month."','".$salary_year."','1',this.value,'".$salary_id."') /></td>
					<td class='text-center'><input type='text' style='width: 68px;' class='form-control grid-control' value='".$others_payble."' onfocusout=setSalary('".$institute_id."','".$branch_id."','".$session_id."','".$employee_id."','".$salary_month."','".$salary_year."','2',this.value,'".$salary_id."') /></td>
					<td class='text-center'>".$total_fix_payble."</td>
					<td class='text-center'><input type='text' style='width: 68px;' class='form-control grid-control' value='".$basic_salary."' onfocusout=setSalary('".$institute_id."','".$branch_id."','".$session_id."','".$employee_id."','".$salary_month."','".$salary_year."','3',this.value,'".$salary_id."') /></td>
					<td class='text-center'><input type='text' style='width: 68px;' class='form-control grid-control' value='".$house_rent."' onfocusout=setSalary('".$institute_id."','".$branch_id."','".$session_id."','".$employee_id."','".$salary_month."','".$salary_year."','4',this.value,'".$salary_id."') /></td>
					<td class='text-center'><input type='text' style='width: 68px;' class='form-control grid-control' value='".$medical_allowance."' onfocusout=setSalary('".$institute_id."','".$branch_id."','".$session_id."','".$employee_id."','".$salary_month."','".$salary_year."','5',this.value,'".$salary_id."') /></td>
					<td class='text-center'><input type='text' style='width: 68px;' class='form-control grid-control' value='".$transport_allowance."' onfocusout=setSalary('".$institute_id."','".$branch_id."','".$session_id."','".$employee_id."','".$salary_month."','".$salary_year."','6',this.value,'".$salary_id."') /></td>
					<td class='text-center'><input type='text' style='width: 68px;' class='form-control grid-control' value='".$others_allowance."' onfocusout=setSalary('".$institute_id."','".$branch_id."','".$session_id."','".$employee_id."','".$salary_month."','".$salary_year."','7',this.value,'".$salary_id."') /></td>
					<td class='text-center'><input type='text' style='width: 68px;' class='form-control grid-control' value='".$festival_bonus."' onfocusout=setSalary('".$institute_id."','".$branch_id."','".$session_id."','".$employee_id."','".$salary_month."','".$salary_year."','8',this.value,'".$salary_id."') /></td>
					<td class='text-center'>".$total_gross."</td>
					<td class='text-center'>".$gross_payble."</td>
					<td class='text-center'><input type='text' style='width: 32px;' class='form-control grid-control' value='".$working_day."' onfocusout=setSalary('".$institute_id."','".$branch_id."','".$session_id."','".$employee_id."','".$salary_month."','".$salary_year."','9',this.value,'".$salary_id."') /></td>
					<td class='text-center'><input type='text' style='width: 32px;' class='form-control grid-control' value='".$present."' onfocusout=setSalary('".$institute_id."','".$branch_id."','".$session_id."','".$employee_id."','".$salary_month."','".$salary_year."','10',this.value,'".$salary_id."') /></td>
					<td class='text-center'><input type='text' style='width: 32px;' class='form-control grid-control' value='".$full_leave."' onfocusout=setSalary('".$institute_id."','".$branch_id."','".$session_id."','".$employee_id."','".$salary_month."','".$salary_year."','11',this.value,'".$salary_id."') /></td>
					<td class='text-center'><input type='text' style='width: 32px;' class='form-control grid-control' value='".$absent."' onfocusout=setSalary('".$institute_id."','".$branch_id."','".$session_id."','".$employee_id."','".$salary_month."','".$salary_year."','12',this.value,'".$salary_id."') /></td>
					<td class='text-center'><input type='text' style='width: 32px;' class='form-control grid-control' value='".$late."' onfocusout=setSalary('".$institute_id."','".$branch_id."','".$session_id."','".$employee_id."','".$salary_month."','".$salary_year."','13',this.value,'".$salary_id."') /></td>
					<td class='text-center'><input type='text' style='width: 32px;' class='form-control grid-control' value='".$early_out."' onfocusout=setSalary('".$institute_id."','".$branch_id."','".$session_id."','".$employee_id."','".$salary_month."','".$salary_year."','14',this.value,'".$salary_id."') /></td>
					<td class='text-center'><input type='text' style='width: 32px;' class='form-control grid-control' value='".$salary_day."' onfocusout=setSalary('".$institute_id."','".$branch_id."','".$session_id."','".$employee_id."','".$salary_month."','".$salary_year."','15',this.value,'".$salary_id."') /></td>
					
					<td class='text-center'><input type='text' style='width: 68px;' class='form-control grid-control' value='".$pf_deduction."' onfocusout=setSalary('".$institute_id."','".$branch_id."','".$session_id."','".$employee_id."','".$salary_month."','".$salary_year."','16',this.value,'".$salary_id."') />
					<td class='text-center'><input type='text' style='width: 68px;' class='form-control grid-control' value='".$loan_deduction."' onfocusout=setSalary('".$institute_id."','".$branch_id."','".$session_id."','".$employee_id."','".$salary_month."','".$salary_year."','17',this.value,'".$salary_id."') />
					<td class='text-center'><input type='text' style='width: 68px;' class='form-control grid-control' value='".$income_tax_amount."' onfocusout=setSalary('".$institute_id."','".$branch_id."','".$session_id."','".$employee_id."','".$salary_month."','".$salary_year."','18',this.value,'".$salary_id."') />
					<td class='text-center'><input type='text' style='width: 68px;' class='form-control grid-control' value='".$absent_deduction."' onfocusout=setSalary('".$institute_id."','".$branch_id."','".$session_id."','".$employee_id."','".$salary_month."','".$salary_year."','19',this.value,'".$salary_id."') />
					<td class='text-center'>".$gross_deduction."</td>
					<td class='text-center'><input type='text' style='width: 68px;' class='form-control grid-control' value='".$tnt_allowance."' onfocusout=setSalary('".$institute_id."','".$branch_id."','".$session_id."','".$employee_id."','".$salary_month."','".$salary_year."','20',this.value,'".$salary_id."') /></td>
					<td class='text-center'><input type='text' style='width: 68px;' class='form-control grid-control' value='".$tnt_td."' onfocusout=setSalary('".$institute_id."','".$branch_id."','".$session_id."','".$employee_id."','".$salary_month."','".$salary_year."','21',this.value,'".$salary_id."') /></td>
					<td class='text-center'><input type='text' style='width: 68px;' class='form-control grid-control' value='".$tnt_ddt."' onfocusout=setSalary('".$institute_id."','".$branch_id."','".$session_id."','".$employee_id."','".$salary_month."','".$salary_year."','22',this.value,'".$salary_id."') /></td>
					<td class='text-center'><input type='text' style='width: 68px;' class='form-control grid-control' value='".$tnt_payable."' onfocusout=setSalary('".$institute_id."','".$branch_id."','".$session_id."','".$employee_id."','".$salary_month."','".$salary_year."','23',this.value,'".$salary_id."') /></td>
					<td class='text-center'><input type='text' style='width: 68px;' class='form-control grid-control' value='".$adjust_payable."' onfocusout=setSalary('".$institute_id."','".$branch_id."','".$session_id."','".$employee_id."','".$salary_month."','".$salary_year."','24',this.value,'".$salary_id."') /></td>
					<td class='text-center'><strong>".$net_salary."</strong></td>
					<td class='text-center'><input type='text' style='width: 75px;' class='form-control grid-control' value='".$remarks."' onfocusout=setSalary('".$institute_id."','".$branch_id."','".$session_id."','".$employee_id."','".$salary_month."','".$salary_year."','25',this.value,'".$salary_id."') /></td>";
				}else{
				    $total_fix_payble  = ($CRES->row()->cash_salary+$CRES->row()->others_payble);
				    
    			    $GTFixPayble+=$total_fix_payble; $GTGross+=$total_gross; $GTGPayble+=$gross_payble; $GTPFD+=$pf_deduction;
    			    $GTLD+=$loan_deduction; $GTITD+=$income_tax_amount; $GTAFD+=$absent_deduction;  $GTGD+=$gross_deduction; 
    			    $GTA+=$tnt_allowance; $GTAD+=$tnt_ddt;	$GTAP+=$tnt_payable; $GTADJ+=$adjust_payable; $GTNET+=$net_salary;
    			    
					$grid.= "
					<td class='text-right'>".$cash_salary."</td>
					<td class='text-right'>".$others_payble."</td>
					<td class='text-right'>".$total_fix_payble."</td>
					
					<td class='text-right'>".$basic_salary."</td>
					<td class='text-right'>".$house_rent."</td>
					<td class='text-right'>".$medical_allowance."</td>
					<td class='text-right'>".$transport_allowance."</td>
					<td class='text-right'>".$others_allowance."</td>
					<td class='text-right'>".$festival_bonus."</td>
					<td class='text-right'>".$total_gross."</td>
					<td class='text-right'>".$gross_payble."</td>
					
					<td class='text-right'>".$working_day."</td>
					<td class='text-right'>".$present."</td>
					<td class='text-right'>".$full_leave."</td>
					<td class='text-right'>".$absent."</td>
					<td class='text-right'>".$late."</td>
					<td class='text-right'>".$early_out."</td>
					<td class='text-right'>".$salary_day."</td>
					
					<td class='text-right'>".$pf_deduction."</td>
					<td class='text-right'>".$loan_deduction."</td>
					<td class='text-right'>".$income_tax_amount."</td>
					<td class='text-right'>".$absent_deduction."</td>
					<td class='text-right'>".$gross_deduction."</td>
				
    				<td class='text-right text-danger'>".$tnt_allowance."</td>
    				<td class='text-right text-danger'>".$tnt_td."</td>
    				<td class='text-right text-danger'>".$tnt_ddt."</td>
    				<td class='text-right text-danger'>".$tnt_payable."</td>
    				<td class='text-right text-danger'>".$adjust_payable."</td>
    				
					<td class='text-right'>".$net_salary."</td>
					<td class='text-right'>".$remarks."</td>
					";
				}
			}elseif($CRES->num_rows()>0 && $CRES->row()->bill_id ==0 && $CRES->row()->status ==1){
				if($hasEditPM){
				    $total_fix_payble  = ($CRES->row()->cash_salary+$CRES->row()->others_payble);
    			    
    			    $GTFixPayble+=$total_fix_payble; $GTGross+=$total_gross; $GTGPayble+=$gross_payble; $GTPFD+=$pf_deduction;
    			    $GTLD+=$loan_deduction; $GTITD+=$income_tax_amount; $GTAFD+=$absent_deduction;  $GTGD+=$gross_deduction; 
    			    $GTA+=$tnt_allowance; $GTAD+=$tnt_ddt;	$GTAP+=$tnt_payable; $GTADJ+=$adjust_payable; $GTNET+=$net_salary;
    			    
					$grid.= "
					<td class='text-center'><input type='text' style='width: 68px;' class='form-control grid-control' value='".$cash_salary."' onfocusout=setSalary('".$institute_id."','".$branch_id."','".$session_id."','".$employee_id."','".$salary_month."','".$salary_year."','1',this.value,'".$salary_id."') /></td>
					<td class='text-center'><input type='text' style='width: 68px;' class='form-control grid-control' value='".$others_payble."' onfocusout=setSalary('".$institute_id."','".$branch_id."','".$session_id."','".$employee_id."','".$salary_month."','".$salary_year."','2',this.value,'".$salary_id."') /></td>
					<td class='text-center'>".$total_fix_payble."</td>
					
					<td class='text-center'><input type='text' style='width: 68px;' class='form-control grid-control' value='".$basic_salary."' onfocusout=setSalary('".$institute_id."','".$branch_id."','".$session_id."','".$employee_id."','".$salary_month."','".$salary_year."','3',this.value,'".$salary_id."') /></td>
					<td class='text-center'><input type='text' style='width: 68px;' class='form-control grid-control' value='".$house_rent."' onfocusout=setSalary('".$institute_id."','".$branch_id."','".$session_id."','".$employee_id."','".$salary_month."','".$salary_year."','4',this.value,'".$salary_id."') /></td>
					<td class='text-center'><input type='text' style='width: 68px;' class='form-control grid-control' value='".$medical_allowance."' onfocusout=setSalary('".$institute_id."','".$branch_id."','".$session_id."','".$employee_id."','".$salary_month."','".$salary_year."','5',this.value,'".$salary_id."') /></td>
					<td class='text-center'><input type='text' style='width: 68px;' class='form-control grid-control' value='".$transport_allowance."' onfocusout=setSalary('".$institute_id."','".$branch_id."','".$session_id."','".$employee_id."','".$salary_month."','".$salary_year."','6',this.value,'".$salary_id."') /></td>
					<td class='text-center'><input type='text' style='width: 68px;' class='form-control grid-control' value='".$others_allowance."' onfocusout=setSalary('".$institute_id."','".$branch_id."','".$session_id."','".$employee_id."','".$salary_month."','".$salary_year."','7',this.value,'".$salary_id."') /></td>
					<td class='text-center'><input type='text' style='width: 68px;' class='form-control grid-control' value='".$festival_bonus."' onfocusout=setSalary('".$institute_id."','".$branch_id."','".$session_id."','".$employee_id."','".$salary_month."','".$salary_year."','8',this.value,'".$salary_id."') /></td>
					<td class='text-center'>".$total_gross."</td>
					<td class='text-center'>".$gross_payble."</td>
					<td class='text-center'><input type='text' style='width: 32px;' class='form-control grid-control' value='".$working_day."' onfocusout=setSalary('".$institute_id."','".$branch_id."','".$session_id."','".$employee_id."','".$salary_month."','".$salary_year."','9',this.value,'".$salary_id."') /></td>
					<td class='text-center'><input type='text' style='width: 32px;' class='form-control grid-control' value='".$present."' onfocusout=setSalary('".$institute_id."','".$branch_id."','".$session_id."','".$employee_id."','".$salary_month."','".$salary_year."','10',this.value,'".$salary_id."') /></td>
					<td class='text-center'><input type='text' style='width: 32px;' class='form-control grid-control' value='".$full_leave."' onfocusout=setSalary('".$institute_id."','".$branch_id."','".$session_id."','".$employee_id."','".$salary_month."','".$salary_year."','11',this.value,'".$salary_id."') /></td>
					<td class='text-center'><input type='text' style='width: 32px;' class='form-control grid-control' value='".$absent."' onfocusout=setSalary('".$institute_id."','".$branch_id."','".$session_id."','".$employee_id."','".$salary_month."','".$salary_year."','12',this.value,'".$salary_id."') /></td>
					<td class='text-center'><input type='text' style='width: 32px;' class='form-control grid-control' value='".$late."' onfocusout=setSalary('".$institute_id."','".$branch_id."','".$session_id."','".$employee_id."','".$salary_month."','".$salary_year."','13',this.value,'".$salary_id."') /></td>
					<td class='text-center'><input type='text' style='width: 32px;' class='form-control grid-control' value='".$early_out."' onfocusout=setSalary('".$institute_id."','".$branch_id."','".$session_id."','".$employee_id."','".$salary_month."','".$salary_year."','14',this.value,'".$salary_id."') /></td>
					<td class='text-center'><input type='text' style='width: 32px;' class='form-control grid-control' value='".$salary_day."' onfocusout=setSalary('".$institute_id."','".$branch_id."','".$session_id."','".$employee_id."','".$salary_month."','".$salary_year."','15',this.value,'".$salary_id."') /></td>
					
					<td class='text-center'><input type='text' style='width: 68px;' class='form-control grid-control' value='".$pf_deduction."' onfocusout=setSalary('".$institute_id."','".$branch_id."','".$session_id."','".$employee_id."','".$salary_month."','".$salary_year."','16',this.value,'".$salary_id."') />
					<td class='text-center'><input type='text' style='width: 68px;' class='form-control grid-control' value='".$loan_deduction."' onfocusout=setSalary('".$institute_id."','".$branch_id."','".$session_id."','".$employee_id."','".$salary_month."','".$salary_year."','17',this.value,'".$salary_id."') />
					<td class='text-center'><input type='text' style='width: 68px;' class='form-control grid-control' value='".$income_tax_amount."' onfocusout=setSalary('".$institute_id."','".$branch_id."','".$session_id."','".$employee_id."','".$salary_month."','".$salary_year."','18',this.value,'".$salary_id."') />
					<td class='text-center'><input type='text' style='width: 68px;' class='form-control grid-control' value='".$absent_deduction."' onfocusout=setSalary('".$institute_id."','".$branch_id."','".$session_id."','".$employee_id."','".$salary_month."','".$salary_year."','19',this.value,'".$salary_id."') />
					<td class='text-center'>".$gross_deduction."</td>
					
					<td class='text-center'><input type='text' style='width: 68px;' class='form-control grid-control' value='".$tnt_allowance."' onfocusout=setSalary('".$institute_id."','".$branch_id."','".$session_id."','".$employee_id."','".$salary_month."','".$salary_year."','20',this.value,'".$salary_id."') /></td>
					
					<td class='text-center'><input type='text' style='width: 68px;' class='form-control grid-control' value='".$tnt_td."' onfocusout=setSalary('".$institute_id."','".$branch_id."','".$session_id."','".$employee_id."','".$salary_month."','".$salary_year."','21',this.value,'".$salary_id."') />
					<td class='text-center'><input type='text' style='width: 68px;' class='form-control grid-control' value='".$tnt_ddt."' onfocusout=setSalary('".$institute_id."','".$branch_id."','".$session_id."','".$employee_id."','".$salary_month."','".$salary_year."','22',this.value,'".$salary_id."') />
					
					<td class='text-center'><input type='text' style='width: 68px;' class='form-control grid-control' value='".$tnt_payable."' onfocusout=setSalary('".$institute_id."','".$branch_id."','".$session_id."','".$employee_id."','".$salary_month."','".$salary_year."','23',this.value,'".$salary_id."') />
					<td class='text-center'><input type='text' style='width: 68px;' class='form-control grid-control' value='".$adjust_payable."' onfocusout=setSalary('".$institute_id."','".$branch_id."','".$session_id."','".$employee_id."','".$salary_month."','".$salary_year."','24',this.value,'".$salary_id."') /></td>
					
					<td class='text-center'><strong>".$net_salary."</strong></td>
					<td class='text-center'><input type='text' style='width: 75px;' class='form-control grid-control' value='".$remarks."' onfocusout=setSalary('".$institute_id."','".$branch_id."','".$session_id."','".$employee_id."','".$salary_month."','".$salary_year."','25',this.value,'".$salary_id."') /></td>";
				}else{
    			    $GTFixPayble+=$total_fix_payble; $GTGross+=$total_gross; $GTGPayble+=$gross_payble; $GTPFD+=$pf_deduction;
    			    $GTLD+=$loan_deduction; $GTITD+=$income_tax_amount; $GTAFD+=$absent_deduction;  $GTGD+=$gross_deduction; 
    			    $GTA+=$tnt_allowance; $GTAD+=$tnt_ddt;	$GTAP+=$tnt_payable; $GTADJ+=$adjust_payable; $GTNET+=$net_salary;
					$grid.= "
					<td class='text-center text-warning'>".$cash_salary."</td>
					<td class='text-center text-warning'>".$others_payble."</td>
					<td class='text-center text-warning'>".$total_fix_payble."</td>
					
					<td class='text-center text-warning'>".$basic_salary."</td>
					<td class='text-center text-warning'>".$house_rent."</td>
					<td class='text-center text-warning'>".$medical_allowance."</td>
					<td class='text-center text-warning'>".$transport_allowance."</td>
					<td class='text-center text-warning'>".$others_allowance."</td>
					<td class='text-center text-warning'>".$festival_bonus."</td>
					<td class='text-center text-warning'>".$total_gross."</td>
					<td class='text-center text-warning'>".$gross_payble."</td>
					
					<td class='text-center text-warning'>".$working_day."</td>
					<td class='text-center text-warning'>".$present."</td>
					<td class='text-center text-warning'>".$full_leave."</td>
					<td class='text-center text-warning'>".$absent."</td>
					<td class='text-center text-warning'>".$late."</td>
					<td class='text-center text-warning'>".$early_out."</td>
					<td class='text-center text-warning'>".$salary_day."</td>
					
					<td class='text-center text-danger'>".$pf_deduction."</td>
					<td class='text-center text-danger'>".$loan_deduction."</td>
					<td class='text-center text-danger'>".$income_tax_amount."</td>
					<td class='text-center text-danger'>".$absent_deduction."</td>
					<td class='text-center text-danger'>".$gross_deduction."</td>
				
    				<td class='text-center text-warning'>".$tnt_allowance."</td>
    				
    				<td class='text-center text-danger'>".$tnt_td."</td>
    				<td class='text-center text-danger'>".$tnt_ddt."</td>
    				
    				<td class='text-center text-warning'>".$tnt_payable."</td>
				    <td class='text-center text-warning'>".$adjust_payable."</td>
					<td class='text-center text-warning'>".$net_salary."</td>
					<td class='text-center text-warning'>".$remarks."</td>
					";
				}
			}elseif($CRES->num_rows()>0 && $CRES->row()->status == 2){
			    $GTFixPayble+=$total_fix_payble; $GTGross+=$total_gross; $GTGPayble+=$gross_payble; $GTPFD+=$pf_deduction;
			    $GTLD+=$loan_deduction; $GTITD+=$income_tax_amount; $GTAFD+=$absent_deduction;  $GTGD+=$gross_deduction; 
			    $GTA+=$tnt_allowance; $GTAD+=$tnt_ddt;	$GTAP+=$tnt_payable; $GTADJ+=$adjust_payable; $GTNET+=$net_salary;
				$grid.= "
				<td class='text-center text-success'>".$cash_salary."</td>
				<td class='text-center text-success'>".$others_payble."</td>
				<td class='text-center text-success'>".$total_fix_payble."</td>
				
				<td class='text-center text-success'>".$basic_salary."</td>
				<td class='text-center text-success'>".$house_rent."</td>
				<td class='text-center text-success'>".$medical_allowance."</td>
				<td class='text-center text-success'>".$transport_allowance."</td>
				<td class='text-center text-success'>".$others_allowance."</td>
				<td class='text-center text-success'>".$festival_bonus."</td>
				<td class='text-center text-success'>".$total_gross."</td>
				<td class='text-center text-success'>".$gross_payble."</td>
				
				<td class='text-center text-success'>".$CRES->row()->working_day."</td>
				<td class='text-center text-success'>".$CRES->row()->present."</td>
				<td class='text-center text-success'>".$CRES->row()->full_leave."</td>
				<td class='text-center text-success'>".$CRES->row()->absent."</td>
				<td class='text-center text-success'>".$CRES->row()->late."</td>
				<td class='text-center text-success'>".$CRES->row()->early_out."</td>
				<td class='text-center text-success'>".$CRES->row()->salary_day."</td>
				
				<td class='text-center text-success'>".$pf_deduction."</td>
				<td class='text-center text-success'>".$loan_deduction."</td>
				<td class='text-center text-success'>".$income_tax_amount."</td>
				<td class='text-center text-success'>".$absent_deduction."</td>
				<td class='text-center text-success'>".$gross_deduction."</td>
				
				<td class='text-center text-danger'>".$tnt_allowance."</td>
				
				<td class='text-center text-danger'>".$tnt_td."</td>
				<td class='text-center text-danger'>".$tnt_ddt."</td>
				
				<td class='text-center text-danger'>".$tnt_payable."</td>
				<td class='text-center text-success'>".$adjust_payable."</td>
				<td class='text-center text-success'>".$net_salary."</td>
				<td class='text-center text-success'>".$remarks."</td>
				";
			}else{
			    $GTFixPayble+=$total_fix_payble; $GTGross+=$total_gross; $GTGPayble+=$gross_payble; $GTPFD+=$pf_deduction;
			    $GTLD+=$loan_deduction; $GTITD+=$income_tax_amount; $GTAFD+=$absent_deduction;  $GTGD+=$gross_deduction; 
			    $GTA+=$tnt_allowance; $GTAD+=$tnt_ddt;	$GTAP+=$tnt_payable; $GTADJ+=$adjust_payable; $GTNET+=$net_salary;
			    
				$grid.= "
				<td class='text-center text-danger'>".$cash_salary."</td>
				<td class='text-center text-danger'>".$others_payble."</td>
				<td class='text-center text-danger'>".$total_fix_payble."</td>
				
				<td class='text-center text-danger'>".$basic_salary."</td>
				<td class='text-center text-danger'>".$house_rent."</td>
				<td class='text-center text-danger'>".$medical_allowance."</td>
				<td class='text-center text-danger'>".$transport_allowance."</td>
				<td class='text-center text-danger'>".$others_allowance."</td>
				<td class='text-center text-danger'>".$festival_bonus."</td>
				<td class='text-center text-danger'>".$total_gross."</td>
				<td class='text-center text-danger'>".$gross_payble."</td>
				
				<td class='text-center text-danger'>".$working_day."</td>
				<td class='text-center text-danger'>".$present."</td>
				<td class='text-center text-danger'>".$full_leave."</td>
				<td class='text-center text-danger'>".$absent."</td>
				<td class='text-center text-danger'>".$late."</td>
				<td class='text-center text-danger'>".$early_out."</td>
				<td class='text-center text-danger'>".$salary_day."</td>
				
				<td class='text-center text-danger'>".$pf_deduction."</td>
				<td class='text-center text-danger'>".$loan_deduction."</td>
				<td class='text-center text-danger'>".$income_tax_amount."</td>
				<td class='text-center text-danger'>".$absent_deduction."</td>
				<td class='text-center text-danger'>".$gross_deduction."</td>
				
				<td class='text-center text-danger'>".$tnt_allowance."</td>
				
				<td class='text-center text-danger'>".$tnt_td."</td>
				<td class='text-center text-danger'>".$tnt_ddt."</td>
				
				<td class='text-center text-danger'>".$tnt_payable."</td>
				<td class='text-center text-success'>".$adjust_payable."</td>
				<td class='text-center text-success'>".$net_salary."</td>
				<td class='text-center text-success'>".$remarks."</td>
				";				
			}		
			$grid.= "</tr>";
            $i++;
        }
        $grid.= "
             <tr>
				<td colspan='4' class='text-right text-danger'>Grand Total</td>
				<td class='text-center text-danger'>".$GTFixPayble."</td>
				
				<td class='text-center text-danger'>&nbsp</td>
				<td class='text-center text-danger'>&nbsp</td>
				<td class='text-center text-danger'>&nbsp</td>
				<td class='text-center text-danger'>&nbsp</td>
				<td class='text-center text-danger'>&nbsp</td>
				<td class='text-center text-danger'>&nbsp</td>
				<td class='text-center text-danger'>".$GTGross."</td>
				<td class='text-center text-danger'>".$GTGPayble."</td>
				
				<td class='text-center text-danger'>&nbsp</td>
				<td class='text-center text-danger'>&nbsp</td>
				<td class='text-center text-danger'>&nbsp</td>
				<td class='text-center text-danger'>&nbsp</td>
				<td class='text-center text-danger'>&nbsp</td>
				<td class='text-center text-danger'>&nbsp</td>
				<td class='text-center text-danger'>&nbsp</td>
				
				<td class='text-center text-danger'>".$GTPFD."</td>
				<td class='text-center text-danger'>".$GTLD."</td>
				<td class='text-center text-danger'>".$GTITD."</td>
				<td class='text-center text-danger'>".$GTAFD."</td>
				<td class='text-center text-danger'>".$GTGD."</td>
				
				<td class='text-center text-danger'>".$GTA."</td>
				
				<td class='text-center text-danger'>&nbsp</td>
				<td class='text-center text-danger'>".$GTAD."</td>
				
				<td class='text-center text-danger'>".$GTAP."</td>
				<td class='text-center text-success'>".$GTADJ."</td>
				<td class='text-center text-success'>".$GTNET."</td>
				<td class='text-center text-success'>&nbsp</td>
			 </tr>";
        $grid.= "</table>";			
        $grid.= "</table><input type='hidden' id='status' value='".$status."'>";
		}else{
		    //======== Start Bonus Sheet ==========
		$grid .= "<table width='238%' border='0' class='table table-responsive table-bordered table-hover custab'>
		<thead>
			<tr class='bg-light'>
			<th width='1%' class='align-middle text-left'>".$this->lang->line("sl")."</th>
			<th width='25%' class='align-middle text-left'>".$this->lang->line("employee_name")."</th>
			<th width='15%' class='text-center'>".$this->lang->line("joining_date")."</th>
			<th width='15%' class='text-center'>".$this->lang->line("basic_salary")."</th>
			<th width='15%' class='align-middle'>".$this->lang->line("festival_bonus")." %</th>
			<th width='15%' class='align-middle'>".$this->lang->line("festival_bonus")." ".$this->lang->line("amount")."</th>
			<th width='14%' class='align-middle'>".$this->lang->line("remarks")."</th>
			</tr>
			</thead>";
        $i=1; $salary_id=0; $total_days=0; $working_day=0; $present=0; $full_leave=0; $holiday=0; $late=0; $early_out=0; $absent=0; $salary_day=0;
        $MSQL   ="SELECT * FROM ".MONTH_DAYS_TBL." WHERE MONTH(`date_field`) ='$salary_month'"; 
	    $mquery = $this->db->query($MSQL);
	    if($mquery->num_rows() >0){
	     $total_days=$mquery->num_rows();    
	     foreach($mquery->result() as $mrow){
	        if($mrow->day_name!="Friday" && $mrow->is_holiday!=1){
	         $working_day+=1;
	        } 
	     }
	    }
        foreach($query->result() as $row){
            $present=0; $full_leave=0; $holiday=0; $late=0; $early_out=0; $absent=0; $salary_day=0;
            $employee_id 	= $row->hrm_employee_id;
            $PSQL   ="SELECT * FROM ".ATTENDANCE_TBL." WHERE account_id = '$employee_id' AND MONTH(`attendance_date`) ='$salary_month' AND day_type IN(1,6) AND present=1"; 
    	    $pquery = $this->db->query($PSQL); //if($employee_id==226){echo $PSQL;}
    	    if($pquery->num_rows() >0){
    	     $present=$pquery->num_rows();    
    	     foreach($pquery->result() as $prow){
    	        if($prow->late==1){
    	         $late+=1;
    	        }elseif($prow->early_leave==1){
    	         $early_out+=1;
    	        } 
    	     }
    	    }
    	    $late_absent = 0;
    	    if($this->session->userdata('one_day_deduction')){
    	        $late_deduction_by= $this->session->userdata('one_day_deduction');
    	    }else{
    	        $late_deduction_by=3;
    	    }
    	    if($late >=3){
    	      $late_absent = intval($late/$late_deduction_by);
    	    }
            $LSQL   ="SELECT * FROM ".ATTENDANCE_TBL." WHERE account_id = '$employee_id' AND MONTH(`attendance_date`) ='$salary_month' AND day_type IN(4) AND present=0"; 
    	    $lquery = $this->db->query($LSQL);
    	    if($lquery->num_rows() >0){
    	     $full_leave=$lquery->num_rows();
    	    }
    	    $early_out =0; // temporary 0
    	    
    	    $absent     = ($working_day - ($full_leave+$present));
    	    $salary_day = (($full_leave+$present)-($late_absent+$early_out));
    	    
            $grid.="<tr class='default'>
			<td>".$i."</td>
			<td>".$row->employee_name."<br>".$row->card_id."</td>
			<td>".$row->date_of_joining."</td>";
			
			$checked =""; $total_salary=0; $bonus_percentage=0;
			$CSQL = "SELECT * FROM ".SALARY_SHEET_TBL." WHERE institute_id=".$institute_id." AND branch_id=".$branch_id." AND session_id=".$session_id." AND salary_month=".$salary_month." AND salary_year=".$salary_year." AND employee_id=".$employee_id." AND is_bonus=$withbonus";
			$CRES = $this->db->query($CSQL);
			if($CRES->num_rows() >0){
				$salary_id 				= $CRES->row()->salary_sheet_id;
				$cash_salary 			= $CRES->row()->cash_salary;
				$others_payble 	        = $CRES->row()->others_payble;
				$total_fix_payble 	    = ($CRES->row()->cash_salary + $CRES->row()->others_payble);
				
				$basic_salary 			= $CRES->row()->basic_salary;
				$house_rent 			= $CRES->row()->house_rent;
				$medical_allowance 		= $CRES->row()->medical_allowance;
				$transport_allowance 	= $CRES->row()->transport_allowance;
				$communication			= 0; //$row->communication_allowance;
				$others_allowance 		= $CRES->row()->others_allowance;
				if($row->month_experience < 12){
				 $bonus_percentage      = 50;
				}else{
				 $bonus_percentage      = 100;
				}
				$festival_bonus 		= $CRES->row()->festival_bonus;
				$total_gross 			= $CRES->row()->total_gross;
				$gross_payble           = ($total_fix_payble + $total_gross);
				$pf_deduction 			= $CRES->row()->pf;
				$loan_deduction 		= $CRES->row()->loan;
				if($CRES->row()->income_tax >0){
				$income_tax_amount 		= $CRES->row()->income_tax;
				$income_tax_amount      = number_format($income_tax_amount, 2, '.', '');
				}else{
				$income_tax_amount	  	= (($row->basic_salary/100)*$row->income_tax);
				$income_tax_amount      = number_format($income_tax_amount, 2, '.', '');
				}
				$absent_deduction 		= $CRES->row()->absent_fine;
				$gross_deduction 		= $CRES->row()->total_deduction;
				
				$tnt_allowance 			= $CRES->row()->tnt_allowance;
				$tnt_td 			    = $CRES->row()->tnt_td;
				$tnt_ddt 			    = $CRES->row()->tnt_ddt;
				
				$tnt_payable 			= ($tnt_allowance-$tnt_ddt);
				
				$adjust_payable         = $CRES->row()->adjust_payable;
				$net_salary 			= $CRES->row()->net_salary;
				$status					= $CRES->row()->status;
				$remarks                = $CRES->row()->remarks;
			}else{ 
				$salary_id              = 0; $bonus_percentage=0;
				$cash_salary 			= $row->cash_salary;
				$others_payble 	        = $row->others_payble;
				$total_fix_payble 	    = ($row->cash_salary+$row->others_payble);
				
				$basic_salary 			= $row->basic_salary;
				$house_rent 			= $row->houserent_allowance;
				$medical_allowance 		= $row->medical_allowance;
				$transport_allowance 	= $row->transport_allowance;
				$communication			= 0;//$row->communication_allowance;
				$others_allowance 		= $row->others_allowance;
				if($row->month_experience < 12){
				 $bonus_percentage      = 50;
				 $festival_bonus 		= (($row->basic_salary/100) * $bonus_percentage);   
				}else{
				 $bonus_percentage      = 100;
				 $festival_bonus 		= (($row->basic_salary/100) * $bonus_percentage); 
				}
				
				$festival_bonus         = number_format($festival_bonus, 2, '.', '');
				$total_gross 			= ($basic_salary+$house_rent+$transport_allowance+$medical_allowance+$communication+$others_allowance+$festival_bonus);
				$gross_payble           = ($total_gross + $total_fix_payble);
				// temporary 30
				//$per_day_salary       = ($gross_payble/$total_days);
				$per_day_salary         = ($gross_payble/30);
				$per_day_salary         = number_format($per_day_salary, 2, '.', '');
				
				$pf_deduction 			= $row->provident_fund;
				$loan_deduction 		= $row->loan_and_adv;
				if($row->income_tax_amount >0){
				$income_tax_amount 		= $row->income_tax_amount;
				$income_tax_amount      = number_format($income_tax_amount, 2, '.', '');
				}else{
				$income_tax_amount	  	= (($row->basic_salary/100)*$row->income_tax);
				$income_tax_amount      = number_format($income_tax_amount, 2, '.', '');
				}
				
				$absent_deduction 		= (($absent+$late_absent+$early_out) * $per_day_salary); 
				$gross_deduction 		= ($pf_deduction+$loan_deduction+$income_tax_amount+$absent_deduction);
				
				$tnt_allowance 			= $row->tnt_allowance;
				$tnt_td 			    = 0;
				$tnt_ddt 			    = 0;
				$tnt_payable 			= ($tnt_allowance-$tnt_ddt);
				$adjust_payable         = 0;
				$net_salary 			= (($total_fix_payble+$total_gross+$tnt_payable+$adjust_payable) - $gross_deduction);
				$remarks                = "";
				//===== Start Insert =====
				if($hasGenRPM){ //echo "$salary_month < $current_month ";
				    $salary_day=30; // temporary
					if(($salary_month <=11) && ($salary_month <= $current_month)){
					$salary_id = $this->GenerateSalaryId($employee_id,0,0,0,0,0,0,$salary_day,0,0,0,0,$basic_salary,$house_rent,$medical_allowance,$transport_allowance,$others_allowance,$festival_bonus,$total_gross,0,0,$income_tax_amount,0,$gross_deduction,0,0,0,0,$festival_bonus,$withbonus,$remarks);
					}elseif(($salary_month ==12) && ($salary_month > $current_month)){
					$salary_id = $this->GenerateSalaryId($employee_id,0,0,0,0,0,0,$salary_day,0,0,0,0,$basic_salary,$house_rent,$medical_allowance,$transport_allowance,$others_allowance,$festival_bonus,$total_gross,0,0,$income_tax_amount,0,$gross_deduction,0,0,0,0,$festival_bonus,$withbonus,$remarks);
					}
				}
				$status	= 0; 
			}
			if($CRES->num_rows()>0 && $CRES->row()->bill_id ==0 && $CRES->row()->status ==0 ){
				if($hasGenRPM){
				    $salary_day=30; // temporary
				    $GTBS+=$basic_salary; $GTFB+=$festival_bonus; 
					$grid.= "
					<td class='text-center'>".$basic_salary."</td>
					<td class='text-center'>".$bonus_percentage."</td>
					<td class='text-center'><input type='text' style='width: 68px;' class='form-control grid-control' value='".$festival_bonus."' onfocusout=setSalary('".$institute_id."','".$branch_id."','".$session_id."','".$employee_id."','".$salary_month."','".$salary_year."','8',this.value,'".$salary_id."') /></td>
					
					<td class='text-center'><input type='text' style='width: 75px;' class='form-control grid-control' value='".$remarks."' onfocusout=setSalary('".$institute_id."','".$branch_id."','".$session_id."','".$employee_id."','".$salary_month."','".$salary_year."','25',this.value,'".$salary_id."') /></td>";
				}else{
				    $GTBS+=$basic_salary; $GTFB+=$festival_bonus; 
					$grid.= "
				<td class='text-center'>".$basic_salary."</td>
					<td class='text-center'>".$bonus_percentage."%</td>
					<td class='text-right'>".$festival_bonus."</td>
					<td class='text-right'>".$remarks."</td>
					";
				}
			}elseif($CRES->num_rows()>0 && $CRES->row()->bill_id ==0 && $CRES->row()->status ==1){
				if($hasEditPM){
				    $GTBS+=$basic_salary; $GTFB+=$festival_bonus; 
					$grid.= "
					<td class='text-center'>".$basic_salary."</td>
					<td class='text-center'>".$bonus_percentage."%</td>
					<td class='text-center'><input type='text' style='width: 68px;' class='form-control grid-control' value='".$festival_bonus."' onfocusout=setSalary('".$institute_id."','".$branch_id."','".$session_id."','".$employee_id."','".$salary_month."','".$salary_year."','8',this.value,'".$salary_id."') /></td>
					
					<td class='text-center'><input type='text' style='width: 75px;' class='form-control grid-control' value='".$remarks."' onfocusout=setSalary('".$institute_id."','".$branch_id."','".$session_id."','".$employee_id."','".$salary_month."','".$salary_year."','25',this.value,'".$salary_id."') /></td>";
				}else{
    			    $GTBS+=$basic_salary; $GTFB+=$festival_bonus; 
					$grid.= "
					<td class='text-center'>".$basic_salary."</td>
					<td class='text-center'>".$bonus_percentage."%</td>
					<td class='text-center text-warning'>".$festival_bonus."</td>
					<td class='text-center text-warning'>".$remarks."</td>
					";
				}
			}elseif($CRES->num_rows()>0 && $CRES->row()->status == 2){
			     $GTBS+=$basic_salary; $GTFB+=$festival_bonus; 
				$grid.= "
				<td class='text-center'>".$basic_salary."</td>
					<td class='text-center'>".$bonus_percentage."%</td>
				<td class='text-center text-success'>".$festival_bonus."</td>
				<td class='text-center text-success'>".$remarks."</td>
				";
			}else{
			    $GTBS+=$basic_salary; $GTFB+=$festival_bonus; 
				$grid.= "
				<td class='text-center'>".$basic_salary."</td>
				<td class='text-center'>".$bonus_percentage."%</td>
				<td class='text-center text-danger'>".$festival_bonus."</td>
				<td class='text-center text-success'>".$remarks."</td>
				";				
			}		
			$grid.= "</tr>";
            $i++;
        }
        $grid.= "
             <tr>
				<td colspan='3' class='text-right text-danger'>Grand Total</td>
				<td class='text-center text-danger'>".$GTBS."</td>
				<td class='text-center text-success'>&nbsp</td>
				<td class='text-center text-danger'>".$GTFB."</td>
				<td class='text-center text-success'>&nbsp</td>
			 </tr>";
        $grid.= "</table>";			
        $grid.= "</table><input type='hidden' id='status' value='".$status."'>";
		//======== End Bonus Sheet =======    
		}
        $grid.= "<div class='float-right'>$Pagination</div>";
        return $grid;
    }
	
	function GetTotalSalarySheetRecord(){
        $institute_id	    =$this->input->post('company-id');
		$branch_id	    	=$this->input->post('branch-id');
		$session_id			=$this->input->post('session-id');
		$salary_month		=$this->input->post('month-name');
		
        if(empty($institute_id)){$institute_id = $this->session->userdata('company_id');}
        $from	=$this->input->post('from');
        $to	=$this->input->post('to');
        if($from==""){ $from=0;} if($to==""){ $to=100;}
        $this->db->select('a.*,i.company_name');
        $this->db->from(EMPLOYEE_TBL.' AS a');
	    $this->db->join(COMPANY_SETTINGS_TBL.' AS i', 'i.company_id=a.company_id','LEFT');
	    $this->db->join(ACC_HEAD_TBL.' AS s', 's.account_id=a.hrm_employee_id','LEFT');
        if($institute_id >0){
            $this->db->where('i.company_id',$institute_id);
        }			
		$this->db->where('a.branch_id', $branch_id);
		$this->db->where('a.status',1);
        $this->db->group_by('a.hrm_employee_id');
        $this->db->order_by('a.hrm_employee_id','ASC');
        $query = $this->db->get();
        if($query->num_rows() >0){
            return $query->num_rows();
        }else{
            return 0;
        }//echo $this->db->last_query();
    }
    
	//===== Start Cash Salary Sheet =========
    function GetCashSalarySheetRecordGrid(){
		$menu_slug= $this->uri->segment(1);
		$this->load->model('Site_model');
		$hasGenRPM   = $this->Site_model->hasOptionPermission($menu_slug,"Generate");
		$hasEditPM   = $this->Site_model->hasOptionPermission($menu_slug,"Edit");
        $institute_id	    =$this->input->post('company-id');
		$branch_id	    	=$this->input->post('branch-id');
		$session_id			=$this->input->post('session-id');
		$version_id			=$this->input->post('version-id');
		$salary_month		=$this->input->post('month-name');
		$salary_year		=$this->input->post('salary-year');
		$search_type		=$this->input->post('search-type'); // 0=Search, 1=Generate, 2=Approved
		$current_month		=date("m");
        if(empty($institute_id)){$institute_id = $this->session->userdata('company_id');}
        $from	=$this->input->post('from');
        $to	=$this->input->post('to');
        if($from==""){ $from=0;} if($to==""){ $to=100;}
        $this->db->select('a.*,i.company_name,DATE_FORMAT(a.joining_date ,"%d-%m-%Y") as joining_date');
        $this->db->from(EMPLOYEE_TBL.' AS a');
	    $this->db->join(COMPANY_SETTINGS_TBL.' AS i', 'i.company_id=a.company_id','LEFT');
	  	$this->db->join(ACC_HEAD_TBL.' AS s', 's.account_id=a.employee_id','LEFT');
        if($institute_id >0){
            $this->db->where('i.company_id',$institute_id);
        }			
		$this->db->where('a.branch_id', $branch_id);
		$this->db->where('a.status',1);
        $this->db->group_by('a.hrm_employee_id');
        $this->db->order_by('a.hrm_employee_id','ASC');
        $this->db->limit($to,$from);
        $query = $this->db->get(); //print  $this->db->last_query();
        $totalrecord = $this->GetTotalSalarySheetRecord();
        $perPage=100; $Pagination="";
        if($totalrecord >0){
            $Pagination = $this->getPagination($totalrecord,$perPage);
        } //print  $this->db->last_query();
        $salarymonth="";
        if($salary_month=="01"){$salarymonth ="Jan";}elseif($salary_month=="02"){$salarymonth ="Feb";}
        elseif($salary_month=="03"){$salarymonth ="Mar";}elseif($salary_month=="04"){$salarymonth ="Apr";}
        elseif($salary_month=="05"){$salarymonth ="May";}elseif($salary_month=="06"){$salarymonth ="Jun";}
        elseif($salary_month=="07"){$salarymonth ="Jul";}elseif($salary_month=="08"){$salarymonth ="Agu";}
        elseif($salary_month=="09"){$salarymonth ="Sep";}elseif($salary_month=="10"){$salarymonth ="Oct";}
        elseif($salary_month=="11"){$salarymonth ="Nov";}elseif($salary_month=="12"){$salarymonth ="Dec";}
		$cwidth		= (20/2);
		$twidth		= (20/2);
        $fwidth		= (54/6);
        $awidth		= (48/8);
		$dwidth		= (21/3);
		$this->db->select('branch_name,branch_address');
		$this->db->from(BRANCH_TBL);
		$this->db->where('id', $branch_id);
		$bquery = $this->db->get();
		if($bquery->num_rows() >0){
		  $brow = $bquery->row();
		  $grid = "<div class='text-center'><h2>".$brow->branch_name."</h2>".$brow->branch_address."<br><strong>Cash salary statement for the month of $salarymonth ".$salary_year."</strong></div><br>";
		}
        $grid .= "<table width='200%'  border='0' class='table table-responsive table-bordered table-hover custab'>
		<thead>
			<tr class='bg-light'>
			<th width='2%' rowspan='2' class='align-middle text-left'>".$this->lang->line("sl")."</th>
			<th width='23%' rowspan='2' class='align-middle text-left'>".$this->lang->line("employee_name")."</th>
			<th width='8%' rowspan='2' class='align-middle text-left'>".$this->lang->line("joining_date")."</th>
			<th width='20%' colspan='2' class='text-center'>".$this->lang->line("fixed_compensation")."</th>
			<th width='8%' rowspan='2' class='align-middle'>".$this->lang->line("fixed_payble")."</th>
			<th width='48%' colspan='7' class='text-center'>".$this->lang->line("attendance")."</th>
			<th width='22%' colspan='3' class='text-center'>".$this->lang->line("salary_deduction")."</th>
			<th width='8%' rowspan='2' class='align-middle'>".$this->lang->line("tnt_allowance")."</th>
			<th width='20%' colspan='2' class='text-center'>T&T</th>
			<th width='8%' rowspan='2' class='align-middle'>T&T Payable</th>
			<th width='13%' rowspan='2' class='text-center'>".$this->lang->line("cash_deduction")."</th>
			<th width='12%' rowspan='2' class='text-center'>".$this->lang->line("adjust_salary")."</th>
			<th width='13%' rowspan='2' class='align-middle'>".$this->lang->line("net_cash_salary")."</th>
			<th width='15%' rowspan='2' class='text-center'>".$this->lang->line("remarks")."</th>
			<th width='8%' rowspan='2' class='align-middle'>Signature</th>
			</tr>
			<tr class='bg-light'>
			";			
			$grid.= "
			<th width='".$cwidth."%' class='text-center'>".$this->lang->line("cash_salary")."</th>
			<th width='".$cwidth."%' class='text-center'>".$this->lang->line("others_payble")."</th>
			
			<th width='".$fwidth."%' class='text-center'>W.D</th>
			<th width='".$fwidth."%' class='text-center'>P.D</th>
			<th width='".$fwidth."%' class='text-center'>L.D</th>
			<th width='".$fwidth."%' class='text-center'>A.B</th>
			<th width='".$fwidth."%' class='text-center'>L.T</th>
			<th width='".$fwidth."%' class='text-center'>E.O</th>
			<th width='".$fwidth."%' class='text-center'>S.D</th>
			
			<th width='".$dwidth."%' class='text-center'>P.F</th>
			<th width='".$dwidth."%' class='text-center'>L.A</th>
			<th width='".$dwidth."%' class='text-center'>D.S</th>
			
			<th width='".$twidth."%' class='text-center'>TD</th>
			<th width='".$twidth."%' class='text-center'>DDT</th>";
			$grid.= "
			  </tr>
			</thead>";
        $i=1; $salary_id=0; $total_days=0; $working_day=0; $present=0; $full_leave=0; $holiday=0; $late=0; $early_out=0; $absent=0; $salary_day=0;
        $ttlcash_deduction=0; $ttlcash_adjust=0; $ttlnet_cash_salary=0;
        foreach($query->result() as $row){
            $present=0; $full_leave=0; $holiday=0; $late=0; $early_out=0; $absent=0; $salary_day=0; $TotalCnB=0;
            $employee_id 	= $row->hrm_employee_id;
            $TotalCnB       = $row->gross_salary+$row->total_fix_payble;
            $grid.="<tr class='default'>
			<td>".$i."</td>
			<td>".$row->employee_name."<br>".$row->designation."</td>
			<td>".$row->joining_date."</td>";
			
			$checked ="";
			$CSQL = "SELECT * FROM ".SALARY_SHEET_TBL." WHERE institute_id=".$institute_id." AND branch_id=".$branch_id." AND session_id=".$session_id." AND salary_month=".$salary_month." AND salary_year=".$salary_year." AND employee_id=".$employee_id; 
			$CRES = $this->db->query($CSQL);
			if($CRES->num_rows() >0){
				$salary_id 				= $CRES->row()->salary_sheet_id;
				$cash_salary 			= $CRES->row()->cash_salary;
				$others_payble 	        = $CRES->row()->others_payble;
				$total_fix_payble 	    = ($cash_salary+$others_payble); //$CRES->row()->total_fix_payble;
				
				$tnt_allowance 			= $CRES->row()->tnt_allowance;
				$tnt_td 			    = $CRES->row()->tnt_td;
				$tnt_ddt 			    = $CRES->row()->tnt_ddt;
				
				$basic_salary 			= $CRES->row()->basic_salary;
				$house_rent 			= $CRES->row()->house_rent;
				$medical_allowance 		= $CRES->row()->medical_allowance;
				$transport_allowance 	= $CRES->row()->transport_allowance;
				$communication			= 0; //$row->communication_allowance;
				$others_allowance 		= $CRES->row()->others_allowance;
				$festival_bonus 		= $CRES->row()->festival_bonus;
				$total_gross 			= $CRES->row()->total_gross;
				
				$working_day 			= $CRES->row()->working_day;
				$present 		        = $CRES->row()->present;
				$full_leave 			= $CRES->row()->full_leave;
				$absent 		        = $CRES->row()->absent;
				$late 		            = $CRES->row()->late;
				$early_out 		        = $CRES->row()->early_out;
				$salary_day 		    = 30; //$CRES->row()->salary_day;
				
				$pf_deduction 			= $CRES->row()->pf;
				$loan_deduction 		= $CRES->row()->loan;
				$income_tax 			= $CRES->row()->income_tax;
				$absent_deduction 		= $CRES->row()->absent_fine;
				$gross_deduction 		= ($CRES->row()->absent_fine+$CRES->row()->loan);
				if($gross_deduction>0){
				   if($CRES->row()->cashover_deduction>0){
				       $cash_deduction  = ($CRES->row()->cash_deduction - $CRES->row()->cashover_deduction);
				   }else{
				       $cash_deduction  = $CRES->row()->cash_deduction;
				   }
				}else{
				 $cash_deduction         = 0;    
				}
				
				$adjust_payable 		= $CRES->row()->adjust_payable;
				$net_cash_salary 		= ($total_fix_payble - $cash_deduction);
				$net_cash_salary        = ($net_cash_salary + $CRES->row()->tnt_payable + $CRES->row()->adjust_payable);
				if($net_cash_salary <0){$net_cash_salary="0.00";}
				
				$remarks 		        = $CRES->row()->remarks;
			}
			if($CRES->num_rows()>0 && $CRES->row()->bill_id >0 && $CRES->row()->status ==2 ){
			    
				$ttlcash_deduction+=$cash_deduction;
				$ttlcash_adjust+=$adjust_payable;
				$ttlnet_cash_salary+=$net_cash_salary;
				
				if($CRES->row()->cashover_deduction>0){
			       $cash_absent_deduction  = ($CRES->row()->cash_deduction - $CRES->row()->cashover_deduction);
			    }else{
			       $cash_absent_deduction  = ($CRES->row()->cash_deduction);
			    }
				$grid.= "
				<td class='text-right text-black'>".number_format($cash_salary, 0, '.', ',')."</td>
				<td class='text-right text-black'>".number_format($others_payble, 0, '.', ',')."</td>
				<td class='text-right text-black'>".number_format($total_fix_payble, 0, '.', ',')."</td>
					
				<td class='text-center text-success'>".$working_day."</td>
				<td class='text-center text-success'>".$present."</td>
				<td class='text-center text-success'>".$full_leave."</td>
				<td class='text-center text-success'>".$absent."</td>
				<td class='text-center text-success'>".$late."</td>
				<td class='text-center text-success'>".$early_out."</td>
				<td class='text-center text-success'>".$salary_day."</td>
				
				<td class='text-center text-danger'>".number_format($pf_deduction, 0, '.', ',')."</td>
				<td class='text-center text-danger'>".number_format($loan_deduction, 0, '.', ',')."</td>
				<td class='text-center text-danger'>".number_format($cash_absent_deduction, 0, '.', ',')."</td>
					
				<td class='text-right text-black'>".number_format($tnt_allowance, 0, '.', ',')."</td>
				<td class='text-right text-black'>".number_format($tnt_td, 0, '.', ',')."</td>
				<td class='text-right text-black'>".number_format($tnt_ddt, 0, '.', ',')."</td>
				<td class='text-right text-black'>".number_format($tnt_payable, 0, '.', ',')."</td>
				<td class='text-right text-black'>".number_format($cash_deduction, 0, '.', ',')."</td>
				<td class='text-right text-black'>".number_format($adjust_payable, 0, '.', ',')."</td>
				<td class='text-right text-success'>".number_format($net_cash_salary, 0, '.', ',')."</td>
				<td class='text-right text-black'>".$remarks."</td>
				<td class='text-right text-black'>&nbsp;</td>
				";				
			}else{
				$cash_deduction=0; $adjust_payable=0; $net_cash_salary=0;
			}		
			$grid.= "</tr>";
            $i++;
        }
        $grid.= "
        <tr class='bg-light'>
			<th colspan='20' class='text-right'>Grand Total</th>
			<th width='13%' class='text-right'>$ttlcash_deduction</th>
			<th width='12%' class='text-right'>$ttlcash_adjust</th>
			<th width='13%' class='text-right'>$ttlnet_cash_salary</th>
			<th width='15%' class='text-right'>&nbsp;</th>
			<th width='8%' class='text-right'>&nbsp;</th>
		</tr>";
        $grid.= "
        <tr>
        <td colspan='25' class='text-center'>
        <br>
        <table align='center' width='200%' border='0' class='table table-bordered table-hover custab'>
		<tr>
	       <td class='text-left' style='padding-top:20px; width:20%;'>
			Prepared By: Software<br>
			<div style='padding-top:25px;padding-bottom:0px'>----------------------------</div>
			Signature & Date
	        </td>
	       <td class='text-left' style='padding-top:20px; width: 20%;'>
			Verified By: HR<br>
			<div style='padding-top:25px;padding-bottom:0px'>----------------------------</div>
			Signature & Date
	        </td>
	       <td class='text-left' style='padding-top:20px; width:20%;'>
			Checked By: IT<br>
			<div style='padding-top:25px;padding-bottom:0px'>----------------------------</div>
			Signature & Date
	        </td>
	       <td class='text-left' style='padding-top:20px; width: 20%;'>
			Checked By: Acc & Finance<br>
			<div style='padding-top:25px;padding-bottom:0px'>----------------------------</div>
			Signature & Date
	        </td>
		    <td class='text-left' style='padding-top:20px; width: 20%;'>
			Approved By: 
			<div style='padding-top:25px;padding-bottom:0px'>----------------------------</div>
			Signature & Date 
	        </td>
	        </tr>				
		</table>
        </td>
        </tr>
        </table>";	
        $grid.="";
        $grid.= "<div class='float-right'>$Pagination</div>";
        return $grid;
    }
    
	//===== Start Bank Salary Sheet =========
    function GetBankSalarySheetRecordGrid(){
		$menu_slug= $this->uri->segment(1);
		$this->load->model('Site_model');
		$hasGenRPM   = $this->Site_model->hasOptionPermission($menu_slug,"Generate");
		$hasEditPM   = $this->Site_model->hasOptionPermission($menu_slug,"Edit");
        $institute_id	    =$this->input->post('company-id');
		$branch_id	    	=$this->input->post('branch-id');
		$session_id			=$this->input->post('session-id');
		$version_id			=$this->input->post('version-id');
		$salary_month		=$this->input->post('month-name');
		$salary_year		=$this->input->post('salary-year');
		$search_type		=$this->input->post('search-type'); // 0=Search, 1=Generate, 2=Approved
		$current_month		=date("m");
        if(empty($institute_id)){$institute_id = $this->session->userdata('company_id');}
        $from	=$this->input->post('from');
        $to	=$this->input->post('to');
        if($from==""){ $from=0;} if($to==""){ $to=100;}
        $this->db->select('a.*,i.company_name,DATE_FORMAT(a.joining_date ,"%d-%m-%Y") as joining_date');
        $this->db->from(EMPLOYEE_TBL.' AS a');
	    $this->db->join(COMPANY_SETTINGS_TBL.' AS i', 'i.company_id=a.company_id','LEFT');
	  	$this->db->join(ACC_HEAD_TBL.' AS s', 's.account_id=a.employee_id','LEFT');
        if($institute_id >0){
            $this->db->where('i.company_id',$institute_id);
        }			
		$this->db->where('a.branch_id', $branch_id);
		$this->db->where('a.status',1);
        $this->db->group_by('a.hrm_employee_id');
        $this->db->order_by('a.hrm_employee_id','ASC');
        $this->db->limit($to,$from);
        $query = $this->db->get(); //print  $this->db->last_query();
        $totalrecord = $this->GetTotalSalarySheetRecord();
        $perPage=100; $Pagination="";
        if($totalrecord >0){
            $Pagination = $this->getPagination($totalrecord,$perPage);
        } //print  $this->db->last_query();
        $salarymonth="";
        if($salary_month=="01"){$salarymonth ="Jan";}elseif($salary_month=="02"){$salarymonth ="Feb";}
        elseif($salary_month=="03"){$salarymonth ="Mar";}elseif($salary_month=="04"){$salarymonth ="Apr";}
        elseif($salary_month=="05"){$salarymonth ="May";}elseif($salary_month=="06"){$salarymonth ="Jun";}
        elseif($salary_month=="07"){$salarymonth ="Jul";}elseif($salary_month=="08"){$salarymonth ="Agu";}
        elseif($salary_month=="09"){$salarymonth ="Sep";}elseif($salary_month=="10"){$salarymonth ="Oct";}
        elseif($salary_month=="11"){$salarymonth ="Nov";}elseif($salary_month=="12"){$salarymonth ="Dec";}
		$cwidth		= (20/2);
		$twidth		= (20/2);
        $bwidth		= (50/5);
        $awidth		= (48/7);
		$dwidth		= (28/4);
		$this->db->select('branch_name,branch_address');
		$this->db->from(BRANCH_TBL);
		$this->db->where('id', $branch_id);
		$bquery = $this->db->get();
		if($bquery->num_rows() >0){
		  $brow = $bquery->row();
		  $grid = "<div class='text-center'><h2>".$brow->branch_name."</h2>".$brow->branch_address."<br><strong>Salary statement for the month of $salarymonth ".$salary_year."</strong></div><br>";
		}
        $grid .= "<table width='195%' border='0' class='table table-responsive table-bordered table-hover custab'>
		<thead>
			<tr class='bg-light'>
			<th width='2%' rowspan='2' class='align-middle text-left'>".$this->lang->line("sl")."</th>
			<th width='20%' rowspan='2' class='align-middle text-left'>".$this->lang->line("employee_name")."</th>
			<th width='8%' rowspan='2' class='align-middle text-left'>".$this->lang->line("joining_date")."</th>
			<th width='50%' colspan='5' class='text-center'>".$this->lang->line("bank_salary")."</th>
			<th width='7%' rowspan='2' class='align-middle'>".$this->lang->line("gross_salary")."</th>
			<th width='48%' colspan='7' class='text-center'>".$this->lang->line("attendance")."</th>
			<th width='28%' colspan='4' class='text-center'>".$this->lang->line("salary_deduction")."</th>
			<th width='12%' rowspan='2' class='text-center'>".$this->lang->line("bank_deduction")."</th>
			<th width='12%' rowspan='2' class='align-middle'>".$this->lang->line("net_bank_salary")."</th>
			<th width='8%' rowspan='2' class='align-middle'>Signature</th>
			</tr>
			<tr class='bg-light'>
			";			
			$grid.= "
			<th width='".$bwidth."%' class='text-center'>".$this->lang->line("basic")."</th>
			<th width='".$bwidth."%' class='text-center'>".$this->lang->line("house_rent")."</th>
			<th width='".$bwidth."%' class='text-center'>".$this->lang->line("medical_allo")."</th>
			<th width='".$bwidth."%' class='text-center'>".$this->lang->line("commu_allo")."</th>
			<th width='".$bwidth."%' class='text-center'>".$this->lang->line("others_allo")."</th>
			
			<th width='".$awidth."%' class='text-center'>W.D</th>
			<th width='".$awidth."%' class='text-center'>P.D</th>
			<th width='".$awidth."%' class='text-center'>L.D</th>
			<th width='".$awidth."%' class='text-center'>A.B</th>
			<th width='".$awidth."%' class='text-center'>L.T</th>
			<th width='".$awidth."%' class='text-center'>E.O</th>
			<th width='".$awidth."%' class='text-center'>S.D</th>
			
			<th width='".$dwidth."%' class='text-center'>P.F</th>
			<th width='".$dwidth."%' class='text-center'>I.T</th>
			<th width='".$dwidth."%' class='text-center'>D.S</th>
			<th width='".$dwidth."%' class='text-center'>O.D</th>";
			$grid.= "
			</tr>
		</thead>";
        $i=1; $salary_id=0; $total_days=0; $working_day=0; $present=0; $full_leave=0; $holiday=0; $late=0; $early_out=0; $absent=0; $salary_day=0;
        $ttlbank_deduction=0; $ttlnet_bank_salary=0; $cashover_deduction=0;
        foreach($query->result() as $row){
            $present=0; $full_leave=0; $holiday=0; $late=0; $early_out=0; $absent=0; $salary_day=0; $TotalCnB=0;
            $employee_id 	= $row->hrm_employee_id;
            $TotalCnB       = $row->gross_salary+$row->total_fix_payble;
            $grid.="<tr class='default'>
			<td>".$i."</td>
			<td>".$row->employee_name."<br>".$row->designation."</td>
			<td>".$row->joining_date."</td>";
			
			$checked ="";
			$CSQL = "SELECT * FROM ".SALARY_SHEET_TBL." WHERE institute_id=".$institute_id." AND branch_id=".$branch_id." AND session_id=".$session_id." AND salary_month=".$salary_month." AND salary_year=".$salary_year." AND employee_id=".$employee_id;
			$CRES = $this->db->query($CSQL);
			if($CRES->num_rows() >0){
				$salary_id 				= $CRES->row()->salary_sheet_id;
				$cash_salary 			= $CRES->row()->cash_salary;
				$tnt_allowance 			= $CRES->row()->tnt_allowance;
				$others_payble 	        = $CRES->row()->others_payble;
				$total_fix_payble 	    = ($cash_salary+$others_payble); //$CRES->row()->total_fix_payble;
				
				$basic_salary 			= $CRES->row()->basic_salary;
				$house_rent 			= $CRES->row()->house_rent;
				$medical_allowance 		= $CRES->row()->medical_allowance;
				$transport_allowance 	= $CRES->row()->transport_allowance;
				$communication			= 0; //$row->communication_allowance;
				$others_allowance 		= $CRES->row()->others_allowance;
				$festival_bonus 		= $CRES->row()->festival_bonus;
				//$total_gross 			= $CRES->row()->total_gross;
				$total_gross 			= ($basic_salary+$house_rent+$medical_allowance+$transport_allowance+$others_allowance);
				
				$working_day 			= $CRES->row()->working_day;
				$present 		        = $CRES->row()->present;
				$full_leave 			= $CRES->row()->full_leave;
				$absent 		        = $CRES->row()->absent;
				$late 		            = $CRES->row()->late;
				$early_out 		        = $CRES->row()->early_out;
				$salary_day 		    = 30; //$CRES->row()->salary_day;
				
				$pf_deduction 			= $CRES->row()->pf;
				$loan_deduction 		= $CRES->row()->loan;
				$income_tax 			= $CRES->row()->income_tax;
				$absent_deduction 		= $CRES->row()->absent_fine;
				$gross_deduction 		= ($CRES->row()->absent_fine + $CRES->row()->income_tax + $CRES->row()->pf);
				$cashover_deduction     = $CRES->row()->cashover_deduction;
				if($gross_deduction >0 || $CRES->row()->cashover_deduction >0){
				   if($CRES->row()->cashover_deduction>0){
				       $bank_deduction  = ($CRES->row()->bank_deduction + $CRES->row()->cashover_deduction);
				   }else{
				       $bank_deduction  = $CRES->row()->bank_deduction;
				   }
				}else{
				$bank_deduction         = 0;    
				}
				
				$net_bank_salary 		= ($total_gross - $bank_deduction); 
				if($net_bank_salary <0){$net_bank_salary="0.00";}
			}
			if($CRES->num_rows()>0 && $CRES->row()->bill_id >0 && $CRES->row()->status ==2 ){
			    $ttlbank_deduction+=$bank_deduction;
			    $ttlnet_bank_salary+=$net_bank_salary;
			    $bank_absent_deduction = $CRES->row()->bank_deduction; 
				$grid.= "
				<td class='text-right text-black'>".number_format($basic_salary, 0, '.', ',')."</td>
				<td class='text-right text-black'>".number_format($house_rent, 0, '.', ',')."</td>
				<td class='text-right text-black'>".number_format($medical_allowance, 0, '.', ',')."</td>
				<td class='text-right text-black'>".number_format($transport_allowance, 0, '.', ',')."</td>
				<td class='text-right text-black'>".number_format($others_allowance, 0, '.', ',')."</td>
				<td class='text-right text-black'>".number_format($total_gross, 0, '.', ',')."</td>
					
				<td class='text-center text-success'>".$working_day."</td>
				<td class='text-center text-success'>".$present."</td>
				<td class='text-center text-success'>".$full_leave."</td>
				<td class='text-center text-success'>".$absent."</td>
				<td class='text-center text-success'>".$late."</td>
				<td class='text-center text-success'>".$early_out."</td>
				<td class='text-center text-success'>".$salary_day."</td>
				
				<td class='text-center text-danger'>".number_format($pf_deduction, 0, '.', ',')."</td>
				<td class='text-center text-danger'>".number_format($income_tax, 0, '.', ',')."</td>
				<td class='text-center text-danger'>".number_format($bank_absent_deduction, 0, '.', ',')."</td>
				<td class='text-center text-danger'>".number_format($cashover_deduction, 0, '.', ',')."</td>
				
				<td class='text-right text-black'>".number_format($bank_deduction, 0, '.', ',')."</td>
				<td class='text-right text-success'>".number_format($net_bank_salary, 0, '.', ',')."</td>
				<td class='text-right text-black'>&nbsp;</td>
				";				
			}		
			$grid.= "</tr>";
			
            $i++;
        }
        
        $grid.= "
        <tr class='bg-light'>
			<th colspan='20' class='text-right'>Grand Total</th>
			<th width='12%' class='text-right'>$ttlbank_deduction</th>
			
			<th width='12%' class='text-right'>$ttlnet_bank_salary</th>
			<th width='8%' class='text-right'>&nbsp;</th>
		</tr>";
		
        $grid.= "</table>";	
        $grid.="
        <br>
        <table align='center' width='200%' border='0' class='table table-bordered table-hover custab'>
		<tr>
	       <td class='text-left' style='padding-top:20px; width:20%;'>
			Prepared By: Software<br>
			<div style='padding-top:25px;padding-bottom:0px'>----------------------------</div>
			Signature & Date
	        </td>
	       <td class='text-left' style='padding-top:20px; width: 20%;'>
			Verified By: HR<br>
			<div style='padding-top:25px;padding-bottom:0px'>----------------------------</div>
			Signature & Date
	        </td>
	       <td class='text-left' style='padding-top:20px; width:20%;'>
			Checked By: IT<br>
			<div style='padding-top:25px;padding-bottom:0px'>----------------------------</div>
			Signature & Date
	        </td>
	       <td class='text-left' style='padding-top:20px; width: 20%;'>
			Checked By: Acc & Finance<br>
			<div style='padding-top:25px;padding-bottom:0px'>----------------------------</div>
			Signature & Date
	        </td>
		    <td class='text-left' style='padding-top:20px; width: 20%;'>
			Approved By: 
			<div style='padding-top:25px;padding-bottom:0px'>----------------------------</div>
			Signature & Date 
	        </td>
	        </tr>				
		</table>
        ";
        $grid.= "<div class='float-right'>$Pagination</div>";
        return $grid;
    }
	function GenerateSalaryId($employee_id,$working_day,$present,$full_leave,$absent,$late,$early_out,$salary_day,$cash_salary,$tnt_allowance,$others_payble,$total_fix_payble,$basic_salary,$house_rent,$medical_allowance,$transport_allowance,$others_allowance,$festival_bonus,$total_gross,$pf_deduction,$loan_deduction,$income_tax,$absent_deduction,$gross_deduction,$tnt_td,$tnt_ddt,$tnt_payable,$adjust_payable,$net_salary,$is_bonus,$remarks=NULL){
		$institute_id	    =$this->input->post('company-id');
		$branch_id	    	=$this->input->post('branch-id');
		$session_id			=$this->input->post('session-id');
		$salary_month		=$this->input->post('month-name');
		$salary_year		=$this->input->post('salary-year');
		
		if(empty($institute_id)){$institute_id = $this->session->userdata('company_id');}
		
		$created_by	    	=$this->session->userdata('created_by');
		$version_id = 0; $communication=0; if($tnt_allowance==""){$tnt_allowance=0;}
		if($tnt_td==""){$tnt_td=0;} if($tnt_ddt==""){$tnt_ddt=0;} if($tnt_payable==""){$tnt_payable=0;}
		$salary_day = 30;
		if($employee_id >0 && $salary_month >0 && $salary_year >0){
						
			$data = array(
				"employee_id"		=>$employee_id,
				"institute_id"		=>$institute_id,
				"branch_id"   		=>$branch_id,
				"session_id"   		=>$session_id,
				"version_id"    	=>$version_id,
				"salary_month"    	=>$salary_month,
				"salary_year"    	=>$salary_year,
				"is_bonus"          =>$is_bonus,
				"working_day"    	=>$working_day,
				"present"    	    =>$present,
				"full_leave"    	=>$full_leave,
				"absent"    	    =>$absent,
				"late"    	        =>$late,
				"early_out"    	    =>$early_out,
				"salary_day"    	=>$salary_day,
				"cash_salary"    	=>$cash_salary,
				"tnt_allowance"    	=>$tnt_allowance,
				"others_payble"     =>$others_payble,
				"total_fix_payble"  =>$total_fix_payble,
				"basic_salary"    	=>$basic_salary,
				"house_rent"    	=>$house_rent,
				"transport_allowance"=>$transport_allowance,
				"medical_allowance" =>$medical_allowance,
				"communication_allowance"=>$communication,
				"others_allowance"  =>$others_allowance,
				"festival_bonus"    =>$festival_bonus,
				"total_gross"   	=>$total_gross,
				"pf" 				=>$pf_deduction,
				"loan" 				=>$loan_deduction,
				"income_tax" 		=>$income_tax,
				"absent_fine"    	=>$absent_deduction,
				"total_deduction"   =>$gross_deduction,
				"tnt_td"            =>$tnt_td,
				"tnt_ddt"           =>$tnt_ddt,
				"tnt_payable"       =>$tnt_payable,
				"adjust_payable"    =>$adjust_payable,
				"net_salary"    	=>$net_salary,
				"remarks"    	    =>$remarks,
				"status"    		=>0,
				"generate_by" 		=>$created_by
			);	
			$this->db->insert(SALARY_SHEET_TBL, $data); //echo $this->db->last_query();
			return $this->db->insert_id();
		}
	}
	
    function saveSalarySheet(){
		$salary_id			=$this->input->post('salary-id');
		$employee_id		=$this->input->post('employee-id');
		$institute_id	    =$this->input->post('company-id');
		$branch_id	    	=$this->input->post('branch-id');
		$session_id			=$this->input->post('session-id');
		$version_id			=$this->input->post('version-id');
		$salary_month		=$this->input->post('month-name');
		$salary_year		=$this->input->post('salary-year');		
		$field_id			=$this->input->post('field-id');
		$modified_by	    =$this->session->userdata('created_by');
		$modified_time		=date("Y-m-d H:i:s");
		$CSQL = "SELECT * FROM ".SALARY_SHEET_TBL." WHERE institute_id=".$institute_id." AND branch_id=".$branch_id." AND salary_month=".$salary_month." AND salary_year=".$salary_year." AND employee_id=".$employee_id." AND salary_sheet_id=".$salary_id;
		$CRES = $this->db->query($CSQL);
		if($CRES->num_rows() >0){
			$salary_id 				= $CRES->row()->salary_sheet_id;
			
			$working_day    	    = $CRES->row()->working_day;
			$present   	            = $CRES->row()->present;
			$full_leave    	        = $CRES->row()->full_leave;
			$absent    	            = $CRES->row()->absent;
			$late    	            = $CRES->row()->late;
			$early_out   	        = $CRES->row()->early_out;
			$salary_day    	        = $CRES->row()->salary_day;
			$cash_salary    	    = $CRES->row()->cash_salary;
			$tnt_allowance    	    = $CRES->row()->tnt_allowance;
			$others_payble          = $CRES->row()->others_payble;
			$total_fix_payble       = $CRES->row()->total_fix_payble;
				
			$basic_salary 			= $CRES->row()->basic_salary;
			$house_rent 			= $CRES->row()->house_rent;
			$transport_allowance 	= $CRES->row()->transport_allowance;
			$medical_allowance 		= $CRES->row()->medical_allowance;
			$communication_allowance= $CRES->row()->communication_allowance;
			$others_allowance 		= $CRES->row()->others_allowance;
			$festival_bonus 		= $CRES->row()->festival_bonus;
					
			$pf_deduction 			= $CRES->row()->pf;
			$loan_deduction 		= $CRES->row()->loan;
			$income_tax 			= $CRES->row()->income_tax;
			$absent_deduction 		= $CRES->row()->absent_fine;
			
			$tnt_td    	            = $CRES->row()->tnt_td;
			$tnt_ddt    	        = $CRES->row()->tnt_ddt;
			$tnt_payable            = $CRES->row()->tnt_payable;
			$adjust_payable         = $CRES->row()->adjust_payable;
			$remarks    	        = $CRES->row()->remarks;
			$status					= $CRES->row()->status;
			$communication_allowance= 0;
			if($institute_id==0){$institute_id=$this->session->userdata('company_id');}
			if($field_id==1){ $cash_salary=$this->input->post('field-value'); 
			}else if($field_id==2){ $others_payble=$this->input->post('field-value'); 
			}else if($field_id==3){ $basic_salary=$this->input->post('field-value'); 
			}else if($field_id==4){ $house_rent=$this->input->post('field-value');
			
			}else if($field_id==5){	$medical_allowance=$this->input->post('field-value');
			}else if($field_id==6){	$transport_allowance=$this->input->post('field-value');
			}else if($field_id==7){ $others_allowance=$this->input->post('field-value');
			}else if($field_id==8){ $festival_bonus=$this->input->post('field-value');
			
			}else if($field_id==9){ $working_day=$this->input->post('field-value');
			}else if($field_id==10){ $present=$this->input->post('field-value');
			}else if($field_id==11){ $full_leave=$this->input->post('field-value');
			}else if($field_id==12){ $absent=$this->input->post('field-value');
			}else if($field_id==13){ $late=$this->input->post('field-value');
			}else if($field_id==14){ $early_out=$this->input->post('field-value');
			}else if($field_id==15){ $salary_day=$this->input->post('field-value');
			 
			}else if($field_id==16){ $pf_deduction=$this->input->post('field-value');
			}else if($field_id==17){ $loan_deduction=$this->input->post('field-value');
			}else if($field_id==18){ $income_tax=$this->input->post('field-value');
			}else if($field_id==19){ $absent_deduction=$this->input->post('field-value');
			
			}else if($field_id==20){ $tnt_allowance=$this->input->post('field-value');
			}else if($field_id==21){ $tnt_td=$this->input->post('field-value');
			}else if($field_id==22){ $tnt_ddt=$this->input->post('field-value');
			}else if($field_id==23){ $tnt_payable=$this->input->post('field-value');
			}else if($field_id==24){ $adjust_payable=$this->input->post('field-value');
			}else if($field_id==25){ $remarks=$this->input->post('field-value');}
			$cash_salary=round($cash_salary,0,PHP_ROUND_HALF_UP); $others_payble=round($others_payble,0,PHP_ROUND_HALF_UP);
			$basic_salary=round($basic_salary,0,PHP_ROUND_HALF_UP); $house_rent=round($house_rent,0,PHP_ROUND_HALF_UP);
			$medical_allowance=round($medical_allowance,0,PHP_ROUND_HALF_UP); $transport_allowance=round($transport_allowance,0,PHP_ROUND_HALF_UP); 
			$others_allowance=round($others_allowance,0,PHP_ROUND_HALF_UP); $festival_bonus=round($festival_bonus,0,PHP_ROUND_HALF_UP);
			$pf_deduction=round($pf_deduction,0,PHP_ROUND_HALF_UP); $loan_deduction=round($loan_deduction,0,PHP_ROUND_HALF_UP);
			$income_tax=round($income_tax,0,PHP_ROUND_HALF_UP); $absent_deduction=round($absent_deduction,0,PHP_ROUND_HALF_UP);
			$tnt_allowance=round($tnt_allowance,0,PHP_ROUND_HALF_UP); $tnt_payable=round($tnt_payable,0,PHP_ROUND_HALF_UP);
			$adjust_payable=round($adjust_payable,0,PHP_ROUND_HALF_UP); 
			
			$total_fix_payble= ($cash_salary+$others_payble);
			$total_gross     = ($basic_salary+$house_rent+$transport_allowance+$medical_allowance+$communication_allowance+$others_allowance+$festival_bonus);
			
			$total_fix_payble= round($total_fix_payble,0,PHP_ROUND_HALF_UP); $total_gross=round($total_gross,0,PHP_ROUND_HALF_UP);
			$total_deduction = ($pf_deduction+$loan_deduction+$income_tax+$absent_deduction);
			$total_deduction = round($total_deduction,0,PHP_ROUND_HALF_UP);
			$tnt_payable     = ($tnt_allowance - $tnt_ddt);
			$tnt_payable     = round($tnt_payable,0,PHP_ROUND_HALF_UP);
			$net_salary		 = (($total_fix_payble+$total_gross+$tnt_payable+$adjust_payable)-$total_deduction);
			$net_salary      = round($net_salary,0,PHP_ROUND_HALF_UP);
			$data = array(
				"employee_id"		=>$employee_id,
				"institute_id"		=>$institute_id,
				"branch_id"   		=>$branch_id,
				"session_id"   		=>$session_id,
				"version_id"    	=>$version_id,
				"salary_month"    	=>$salary_month,
				"salary_year"    	=>$salary_year,
				"working_day"    	=>$working_day,
				"present"    	    =>$present,
				"full_leave"    	=>$full_leave,
				"absent"    	    =>$absent,
				"late"    	        =>$late,
				"early_out"    	    =>$early_out,
				"salary_day"    	=>$salary_day,
				"cash_salary"    	=>$cash_salary,
				"tnt_allowance"    	=>$tnt_allowance,
				"others_payble"     =>$others_payble,
				"total_fix_payble"  =>$total_fix_payble,
				"basic_salary"    	=>$basic_salary,
				"house_rent"    	=>$house_rent,
				"transport_allowance"=>$transport_allowance,
				"medical_allowance" =>$medical_allowance,
				"communication_allowance"=>$communication_allowance,
				"others_allowance"  =>$others_allowance,
				"festival_bonus"    =>$festival_bonus,
				"total_gross"   	=>$total_gross,
				"pf" 				=>$pf_deduction,
				"loan" 				=>$loan_deduction,
				"income_tax" 		=>$income_tax,
				"absent_fine"    	=>$absent_deduction,
				"total_deduction"   =>$total_deduction,
				"tnt_td"            =>$tnt_td,
				"tnt_ddt"           =>$tnt_ddt,
				"tnt_payable"       =>$tnt_payable,
				"adjust_payable"    =>$adjust_payable,
				"net_salary"    	=>$net_salary,
				"remarks"    	    =>$remarks,
				"status"    		=>$status,
				"modified_by" 		=>$modified_by,
				"modified_time" 	=>$modified_time
			);
			$this->db->where('salary_sheet_id',$salary_id);
            $this->db->where('institute_id',$institute_id);
            $this->db->where('branch_id',$branch_id);
            $this->db->where('employee_id',$employee_id);
			$this->db->update(SALARY_SHEET_TBL, $data);	//print $this->db->last_query();		
		}		
    }
	function FinalizeSalarySheet(){
		$institute_id	    =$this->input->post('company-id');
		$branch_id	    	=$this->input->post('branch-id');
		$session_id			=$this->input->post('session-id');
		$version_id			=$this->input->post('version-id');
		$salary_month		=$this->input->post('month-name');
		$salary_year		=$this->input->post('salary-year');
		$status				=$this->input->post('status');	
		$generate_by	    =$this->session->userdata('created_by');
		$generate_time		=date("Y-m-d H:i:s");					
		$data = array(
			"status"    		=>$status,
			"generate_by" 		=>$generate_by,
			"generate_time" 	=>$generate_time
		);
		$this->db->where('salary_month',$salary_month);
		$this->db->where('salary_year',$salary_year);
		$this->db->where('institute_id',$institute_id);
		$this->db->where('branch_id',$branch_id);
		$this->db->update(SALARY_SHEET_TBL, $data);	
	}	
	function GetLastDaysOfMonth($month,$year){
		$DSQL  = "SELECT DAY(`date_field`) as last_days FROM ".MONTH_DAYS_TBL." WHERE MONTH(`date_field`)='".$month."' AND YEAR(`date_field`)='".$year."' ORDER BY date_field DESC LIMIT 0,1";
		$query = $this->db->query($DSQL);
		if($query->num_rows() >0){			
			return $query->row()->last_days;
		}else{
			return 30;
		}
	}
	function ApprovedSalarySheet(){
		$institute_id	    =$this->input->post('company-id');
		$branch_id	    	=$this->input->post('branch-id');
		$session_id			=$this->input->post('session-id');
		$version_id			=$this->input->post('version-id');
		$salary_month		=$this->input->post('month-name');
		$salary_year		=$this->input->post('salary-year');
		$withbonus		    =$this->input->post('with-bonus');
		$status				=$this->input->post('status');	
		$approved_by	    =$this->session->userdata('created_by');
		$approved_time		=date("Y-m-d H:i:s");
		$bdate   			=$this->GetLastDaysOfMonth($salary_month,$salary_year);
		$bill_date = $salary_year."-".$salary_month."-".$bdate;
		$ssql   = "SELECT * FROM ".SALARY_SHEET_TBL." WHERE institute_id = $institute_id AND branch_id =$branch_id AND session_id=$session_id AND salary_month=$salary_month AND salary_year=$salary_year AND status=1";
		$squery = $this->db->query($ssql);
		foreach($squery->result() as $sdrow){
    		$salary_id      = $sdrow->salary_sheet_id;
    		$account_id     = $sdrow->employee_id;
    		$gross_salary   = $sdrow->total_gross;
    		$total_deduction= $sdrow->total_deduction;
    		$net_salary     = $sdrow->net_salary;
    		if($withbonus==0){
    		$gross_salary   = $sdrow->total_gross;
    		$total_deduction= $sdrow->total_deduction;
    		$net_salary     = $sdrow->net_salary;
    		$bill_id = $this->saveSalaryBill($salary_id,$bill_date,$salary_month,$salary_year,$institute_id,$branch_id,$session_id,$version_id,$account_id,$gross_salary,$total_deduction,$net_salary);
    		$cashover_deduction=0;
    		//==== Start Deduction =====
			$cash_salary 			= $sdrow->cash_salary;
			$others_payble 	        = $sdrow->others_payble;
			$total_fix_payble 	    = ($cash_salary+$others_payble);
    		$gross_deduction 		= ($sdrow->absent_fine+$sdrow->loan);
			if($gross_deduction>0){
			    $cash_deduction         = (($sdrow->absent_fine/100)*40);
			    $cash_deduction         = ($cash_deduction + $sdrow->loan);
			}else{
			    $cash_deduction         = 0;    
			}
			if($total_fix_payble>=$cash_deduction){
			    $net_cash_salary 		= ($total_fix_payble - $cash_deduction);
			}else{
			    $net_cash_salary 		= 0; 
			    $cashover_deduction 	= ($cash_deduction - $total_fix_payble); 
			}
			
			$gross_deduction 		= ($sdrow->absent_fine + $sdrow->income_tax + $sdrow->pf);
			if($gross_deduction >0){
			  $bank_deduction         = (($sdrow->absent_fine/100)*60);
			  $bank_deduction         = ($bank_deduction + $sdrow->income_tax + $sdrow->pf);
			}else{
			  $bank_deduction         = 0;    
			}
			
    		//====== Update Salary Sheet ========
    		$USQL= "UPDATE ".SALARY_SHEET_TBL." SET cash_deduction='".$cash_deduction."', cashover_deduction='".$cashover_deduction."', bank_deduction='".$bank_deduction."' WHERE institute_id = $institute_id AND branch_id = $branch_id AND salary_sheet_id = $salary_id AND employee_id=$account_id";
    		$this->db->query($USQL);
    		//==== End Deduction ========
    		    
    		}else{
    		$gross_salary    = $sdrow->festival_bonus;
    		$total_deduction = 0;
    		$net_salary      = $sdrow->festival_bonus;
    		$bill_id = $this->saveSalaryBill($salary_id,$bill_date,$salary_month,$salary_year,$institute_id,$branch_id,$session_id,$version_id,$account_id,$gross_salary,$total_deduction,$net_salary);
    		}
		}
		if($bill_id>0){
			$data = array(
				"status"    		=>$status,
				"approved_by" 		=>$approved_by,
				"approved_time" 	=>$approved_time
			);
			$this->db->where('salary_month',$salary_month);
			$this->db->where('salary_year',$salary_year);
			$this->db->where('institute_id',$institute_id);
			$this->db->where('branch_id',$branch_id);
			$this->db->update(SALARY_SHEET_TBL, $data);	
		}
	}
	//======= Save Employee Pay Journal =======
			
	function saveSalaryBill($salary_id,$bill_date,$billing_month,$billing_year,$institute_id,$branch_id,$session_id,$version_id,$account_id,$gross_salary,$total_deduction,$net_salary){
		$description = "Salary journal of monthly ";
		if($billing_month==1){$description.="jan ".$billing_year;}
		elseif($billing_month==2){$description.="feb ".$billing_year;}
		elseif($billing_month==3){$description.="mar ".$billing_year;}
		elseif($billing_month==4){$description.="apr ".$billing_year;}
		elseif($billing_month==5){$description.="may ".$billing_year;}
		elseif($billing_month==6){$description.="jun ".$billing_year;}
		elseif($billing_month==7){$description.="jul ".$billing_year;}
		elseif($billing_month==8){$description.="aug ".$billing_year;}
		elseif($billing_month==9){$description.="sep ".$billing_year;}
		elseif($billing_month==10){$description.="oct ".$billing_year;}
		elseif($billing_month==11){$description.="nov ".$billing_year;}
		elseif($billing_month==12){$description.="dec ".$billing_year;}
		
		$invoice_note1		= $this->input->post('invoice_note1');
		$invoice_note2		= $this->input->post('invoice_note2');		
		$shift_id 			= $this->session->userdata('default_shift');
		if(empty($discount_percentage)){$discount_percentage=0;} if(empty($discount_amount)){$discount_amount=0;}
		
		$created_by			= $this->session->userdata('created_by');
		$bill_no			= $this->getBillID($institute_id,$branch_id,$session_id,$billing_month,$bill_date);
	    $bill_type			= 3; // 3=Salary Journal
	    //======== Save Bill Master ========
	    $mdata = array(
			'bill_no'  			=>$bill_no,
			'billing_month' 	=>$billing_month,
			'billing_date'		=>$bill_date,
			'account_id'   		=>$account_id,
			'admission_id'		=>0,
			'institute_id'    	=>$institute_id,
			'branch_id'    		=>$branch_id,
			'session_id'    	=>$session_id,
			'version_id'    	=>$version_id,
			'shift_id'			=>$shift_id,
			'bill_amount'    	=>$gross_salary,
			'discount_amount'   =>$total_deduction,
			'net_bill_amount'  	=>$net_salary,
			'due_amount'  		=>$net_salary,
			'description'  		=>$description,
			'invoice_note1'  	=>$invoice_note1,
			'invoice_note2'  	=>$invoice_note2,
			'bill_type'			=>$bill_type,
			'created_by'  		=>$created_by
		);
		$this->db->insert(BILL_MASTER_TBL, $mdata); //print $this->db->last_query();
		$bill_id = $this->db->insert_id(); 
		//====== Update Bill Details ========
		$USQL= "UPDATE ".SALARY_SHEET_TBL." SET bill_id='".$bill_id."'WHERE bill_id=0 AND institute_id = $institute_id AND branch_id = $branch_id AND salary_month = $billing_month AND salary_year=$billing_year 
		AND employee_id=$account_id";
		$this->db->query($USQL);
		
		$contra_id=$this->SaveJV($bill_id,$institute_id,$branch_id,$session_id,$version_id,$salary_id,$account_id,$gross_salary,$bill_date,$billing_month,$billing_year);
		return $bill_id;					
	}
	function InsertBillDetails($bill_id,$bill_date,$billing_month,$employee_id,$feeaccount_id,$unit_price,$remarks){
		
		$institute_id	    =$this->input->post('company-id');
		$branch_id	    	=$this->input->post('branch-id');
		$session_id			=$this->input->post('session-id');
		$version_id			=$this->input->post('version-id');
		$withbonus		    =$this->input->post('with-bonus');
		$quantity			= 1;
		$total_price		= ($quantity * $unit_price);
		$total_price 		= round($total_price,0,PHP_ROUND_HALF_UP);
    	$created_by			= $this->session->userdata('created_by');
		if($withbonus==0){$bill_type=1;}else{$bill_type=2;}							
		$ddata = array(
		'bill_id'  			=>$bill_id,
		'billing_month' 	=>$billing_month,
		'billing_date'		=>$bill_date,
		'bill_type'		    =>$bill_type,
		'fee_id'			=>0,
		'fee_account'		=>$feeaccount_id,
		'account_id'   		=>$employee_id,
		'admission_id'		=>0,
		'institute_id'    	=>$institute_id,
		'branch_id'    		=>$branch_id,
		'session_id'    	=>$session_id,
		'version_id'    	=>$version_id,
		'class_id'			=>0,
		'group_id'			=>0,
		'section_id'		=>0,
		'quantity'    		=>$quantity,
		'unit_price'    	=>$unit_price,
		'total_price'  		=>$total_price,
		'remarks'  			=>$remarks,
		'status'  			=>1,
		'created_by'  		=>$created_by
	    );
	   	   
		$this->db->insert(BILL_DETAILS_TBL, $ddata); //print $this->db->last_query();
		
	}
	function SaveJV($bill_id,$institute_id,$branch_id,$session_id,$version_id,$salary_id,$account_head,$gross_salary,$voucher_date,$salary_month,$salary_year,$note=NULL){			
	    $voucher_no 		= $this->getVoucherID($voucher_date);	
		$agency_percentage	= $this->input->post('agency-percentage');	
		$agency_commission	= $this->input->post('agency-commission');	
		$vat_percentage		= $this->input->post('vat-percentage');	
		$vat_amount			= $this->input->post('vat-amount');
		$withbonus		    =$this->input->post('with-bonus');
		if(empty($net_bill_amount)){$net_bill_amount=0;} 		
		$net_bill_amount = round($net_bill_amount,0,PHP_ROUND_HALF_UP);
		if(empty($agency_percentage)){$agency_percentage=0;$agency_commission=0;} 		
		$agency_commission = round($agency_commission,0,PHP_ROUND_HALF_UP);
		if(empty($vat_percentage)){$vat_percentage=0;$vat_amount=0;}  		
		$vat_amount = round($vat_amount,0,PHP_ROUND_HALF_UP);

		$dr_amount 			= ($gross_salary);
		$cr_amount 			= ($gross_salary);		
		$mode_of_payment	= 10; // 10=Others
		$voucher_type		= 3; // 3=Journal	
		if($withbonus==0){
		  $description		= "The monthly payable amount against employee salary";	  
		}else{
		  $description		= "The monthly payable amount against employee festival bonus";	   
		}		
		$created_by			= $this->session->userdata('created_by');
		$contra_id			= 0;
		
		$esql   = "SELECT * FROM ".EMPLOYEE_TBL." WHERE employee_id ='".$account_head."' AND company_id = $institute_id AND branch_id =$branch_id";
		$equery = $this->db->query($esql);
		$erow   = $equery->row();
			
		$ssql   = "SELECT * FROM ".SALARY_SHEET_TBL." WHERE employee_id ='".$account_head."' AND institute_id = $institute_id AND branch_id =$branch_id AND salary_month=$salary_month AND salary_year=$salary_year AND status=1";
		$squery = $this->db->query($ssql);
		$srow   = $squery->row();
		
		$vsql = "SELECT contra_id,voucher_no FROM ".VOUCHER_MASTER_TBL." WHERE invoice_no ='".$bill_id."' AND voucher_type = 3 AND status ='1'";
		$vquery = $this->db->query($vsql);
		if($vquery->num_rows() >0){
		   $contra_id = $vquery->row()->contra_id;
		   $voucher_no= $vquery->row()->voucher_no;		   		         
		   //=== Update Master =====
		   $SQL= "UPDATE ".VOUCHER_MASTER_TBL." SET invoice_no=".$bill_id.",institute_id='".$institute_id."',branch_id='".$branch_id."',session_id='".$session_id."',version_id='".$version_id."',dr_amount='".$dr_amount."',cr_amount='".$cr_amount."', mode_of_payment='".$mode_of_payment."', voucher_type='".$voucher_type."', description='".$description."' WHERE contra_id = ".$contra_id;
		   $this->db->query($SQL);
		   //===== Delete All Voucher Details ======
		   if($contra_id >0){
			$DLSQL1= "DELETE FROM ".VOUCHER_DETAILS_TBL." WHERE contra_id = ".$contra_id." AND invoice_no='".$bill_id."'";
			$this->db->query($DLSQL1);
			$DLSQL2= "DELETE FROM ".BILL_DETAILS_TBL." WHERE bill_id=".$bill_id;
			$this->db->query($DLSQL2);
			$DLSQL3= "DELETE FROM ".ACC_LEDGER_TBL." WHERE voucher_no='".$voucher_no."' AND invoice_no = ".$bill_id;
			$this->db->query($DLSQL3);   
		   }
		   //==== Start All Dr Account =====			    
			if($srow->cash_salary >0){		
			   $cash_salary_id = $this->GetAccountId(6,11,36,15,18);
			   $bcsql = "SELECT * FROM ".VOUCHER_DETAILS_TBL." WHERE account_id ='".$cash_salary_id."' AND contra_id = $contra_id AND status ='1'";
			   $bquery = $this->db->query($bcsql);
			   if($bquery->num_rows() >0){
				$description ="Debit amount against employee cash salary";
				$DSQL= "UPDATE ".VOUCHER_DETAILS_TBL." SET amount='".$srow->cash_salary."', voucher_type='".$voucher_type."', description='".$description."' WHERE account_id='".$cash_salary_id."' AND contra_id = ".$contra_id; $this->db->query($DSQL);			
				$this->SaveAccountLedger($voucher_no,$bill_id,$voucher_date,$cash_salary_id,$voucher_type,$description,$srow->cash_salary,"Cr","U");
			   }else{					   
				$description ="Debit amount against employee cash salary";
				$DSQL="INSERT INTO ".VOUCHER_DETAILS_TBL."(contra_id,invoice_no,voucher_no,headtypes,account_id,amount,voucher_type,description,created_by) ";
				$DSQL.="VALUES('".$contra_id."','".$bill_id."','".$voucher_no."','Dr','".$cash_salary_id."','".$srow->cash_salary."','".$voucher_type."','".$description."','".$created_by."')";
				$this->db->query($DSQL);
				$this->SaveAccountLedger($voucher_no,$bill_id,$voucher_date,$cash_salary_id,$voucher_type,$description,$srow->cash_salary,"Dr","I");
			   }
			   $this->InsertBillDetails($bill_id,$voucher_date,$salary_month,$account_head,$cash_salary_id,$srow->cash_salary,"Payable amount against employee cash salary");
			} // end cash_salary dr			    
			if($srow->tnt_allowance >0){		
			   $tnt_allowance_id = $this->GetAccountId(6,11,36,15,28);
			   $bcsql = "SELECT * FROM ".VOUCHER_DETAILS_TBL." WHERE account_id ='".$tnt_allowance_id."' AND contra_id = $contra_id AND status ='1'";
			   $bquery = $this->db->query($bcsql);
			   if($bquery->num_rows() >0){
				$description ="Debit amount against employee t&t allowance";
				$DSQL= "UPDATE ".VOUCHER_DETAILS_TBL." SET amount='".$srow->tnt_allowance."', voucher_type='".$voucher_type."', description='".$description."' WHERE account_id='".$tnt_allowance_id."' AND contra_id = ".$contra_id; $this->db->query($DSQL);			
				$this->SaveAccountLedger($voucher_no,$bill_id,$voucher_date,$tnt_allowance_id,$voucher_type,$description,$srow->tnt_allowance,"Cr","U");
			   }else{					   
				$description ="Debit amount against employee t&t allowance";
				$DSQL="INSERT INTO ".VOUCHER_DETAILS_TBL."(contra_id,invoice_no,voucher_no,headtypes,account_id,amount,voucher_type,description,created_by) ";
				$DSQL.="VALUES('".$contra_id."','".$bill_id."','".$voucher_no."','Dr','".$tnt_allowance_id."','".$srow->tnt_allowance."','".$voucher_type."','".$description."','".$created_by."')";
				$this->db->query($DSQL);
				$this->SaveAccountLedger($voucher_no,$bill_id,$voucher_date,$tnt_allowance_id,$voucher_type,$description,$srow->tnt_allowance,"Dr","I");
			   }
			   $this->InsertBillDetails($bill_id,$voucher_date,$salary_month,$account_head,$tnt_allowance_id,$srow->tnt_allowance,"Payable amount against employee t&t allowance");
			} // end cash_salary dr
						    
			if($srow->others_payble >0){		
			   $others_payble_id = $this->GetAccountId(6,11,36,15,29);
			   $bcsql = "SELECT * FROM ".VOUCHER_DETAILS_TBL." WHERE account_id ='".$others_payble_id."' AND contra_id = $contra_id AND status ='1'";
			   $bquery = $this->db->query($bcsql);
			   if($bquery->num_rows() >0){
				$description ="Debit amount against employee others payable";
				$DSQL= "UPDATE ".VOUCHER_DETAILS_TBL." SET amount='".$srow->others_payble."', voucher_type='".$voucher_type."', description='".$description."' WHERE account_id='".$others_payble_id."' AND contra_id = ".$contra_id; $this->db->query($DSQL);			
				$this->SaveAccountLedger($voucher_no,$bill_id,$voucher_date,$others_payble_id,$voucher_type,$description,$srow->others_payble,"Cr","U");
			   }else{					   
				$description ="Debit amount against employee others payble";
				$DSQL="INSERT INTO ".VOUCHER_DETAILS_TBL."(contra_id,invoice_no,voucher_no,headtypes,account_id,amount,voucher_type,description,created_by) ";
				$DSQL.="VALUES('".$contra_id."','".$bill_id."','".$voucher_no."','Dr','".$others_payble_id."','".$srow->others_payble."','".$voucher_type."','".$description."','".$created_by."')";
				$this->db->query($DSQL);
				$this->SaveAccountLedger($voucher_no,$bill_id,$voucher_date,$others_payble_id,$voucher_type,$description,$srow->others_payble,"Dr","I");
			   }
			   $this->InsertBillDetails($bill_id,$voucher_date,$salary_month,$account_head,$others_payble_id,$srow->others_payble,"Payable amount against employee others payble");
			} // end cash_salary dr
						    
			if($srow->basic_salary >0){		
			   $basic_salary_id = $this->GetAccountId(6,11,0,0,19);
			   $bcsql = "SELECT * FROM ".VOUCHER_DETAILS_TBL." WHERE account_id ='".$basic_salary_id."' AND contra_id = $contra_id AND status ='1'";
			   $bquery = $this->db->query($bcsql);
			   if($bquery->num_rows() >0){
				$description ="Debit amount against employee basic salary";
				$DSQL= "UPDATE ".VOUCHER_DETAILS_TBL." SET amount='".$srow->basic_salary."', voucher_type='".$voucher_type."', description='".$description."' WHERE account_id='".$basic_salary_id."' AND contra_id = ".$contra_id; $this->db->query($DSQL);			
				$this->SaveAccountLedger($voucher_no,$bill_id,$voucher_date,$basic_salary_id,$voucher_type,$description,$srow->basic_salary,"Cr","U");
			   }else{					   
				$description ="Debit amount against employee basic salary";
				$DSQL="INSERT INTO ".VOUCHER_DETAILS_TBL."(contra_id,invoice_no,voucher_no,headtypes,account_id,amount,voucher_type,description,created_by) ";
				$DSQL.="VALUES('".$contra_id."','".$bill_id."','".$voucher_no."','Dr','".$basic_salary_id."','".$srow->basic_salary."','".$voucher_type."','".$description."','".$created_by."')";
				$this->db->query($DSQL);
				$this->SaveAccountLedger($voucher_no,$bill_id,$voucher_date,$basic_salary_id,$voucher_type,$description,$srow->basic_salary,"Dr","I");
			   }
			   $this->InsertBillDetails($bill_id,$voucher_date,$salary_month,$account_head,$basic_salary_id,$srow->basic_salary,"Payable amount against employee basic salary");
			} // end basic_salary dr
			
			if($srow->house_rent >0){		
			   $house_rent_id = $this->GetAccountId(6,11,0,0,20);
			   $bcsql = "SELECT * FROM ".VOUCHER_DETAILS_TBL." WHERE account_id ='".$house_rent_id."' AND contra_id = $contra_id AND status ='1'";
			   $bquery = $this->db->query($bcsql);
			   if($bquery->num_rows() >0){
				$description ="Debit amount against employee house rent";
				$DSQL= "UPDATE ".VOUCHER_DETAILS_TBL." SET amount='".$srow->house_rent."', voucher_type='".$voucher_type."', description='".$description."' WHERE account_id='".$house_rent_id."' AND contra_id = ".$contra_id; $this->db->query($DSQL);			
				$this->SaveAccountLedger($voucher_no,$bill_id,$voucher_date,$house_rent_id,$voucher_type,$description,$srow->house_rent,"Cr","U");
			   }else{
				$description ="Debit amount against employee house rent";
				$DSQL="INSERT INTO ".VOUCHER_DETAILS_TBL."(contra_id,invoice_no,voucher_no,headtypes,account_id,amount,voucher_type,description,created_by) ";
				$DSQL.="VALUES('".$contra_id."','".$bill_id."','".$voucher_no."','Dr','".$house_rent_id."','".$srow->house_rent."','".$voucher_type."','".$description."','".$created_by."')";
				$this->db->query($DSQL);
				$this->SaveAccountLedger($voucher_no,$bill_id,$voucher_date,$house_rent_id,$voucher_type,$description,$srow->house_rent,"Dr","I");
			   }
			   $this->InsertBillDetails($bill_id,$voucher_date,$salary_month,$account_head,$house_rent_id,$srow->house_rent,"Payable amount against employee house rent");
			} // end house_rent dr			    
			if($srow->medical_allowance >0){		
			   $mallowance_id = $this->GetAccountId(6,11,0,0,21);
			   $bcsql = "SELECT * FROM ".VOUCHER_DETAILS_TBL." WHERE account_id ='".$mallowance_id."' AND contra_id = $contra_id AND status ='1'";
			   $bquery = $this->db->query($bcsql);
			   if($bquery->num_rows() >0){
				$description ="Debit amount against employee medical allowance";
				$DSQL= "UPDATE ".VOUCHER_DETAILS_TBL." SET amount='".$srow->medical_allowance."', voucher_type='".$voucher_type."', description='".$description."' WHERE account_id='".$mallowance_id."' AND contra_id = ".$contra_id; $this->db->query($DSQL);			
				$this->SaveAccountLedger($voucher_no,$bill_id,$voucher_date,$mallowance_id,$voucher_type,$description,$srow->medical_allowance,"Cr","U");
			   }else{
				$description ="Debit amount against employee medical allowance";
				$DSQL="INSERT INTO ".VOUCHER_DETAILS_TBL."(contra_id,invoice_no,voucher_no,headtypes,account_id,amount,voucher_type,description,created_by) ";
				$DSQL.="VALUES('".$contra_id."','".$bill_id."','".$voucher_no."','Dr','".$mallowance_id."','".$srow->medical_allowance."','".$voucher_type."','".$description."','".$created_by."')";
				$this->db->query($DSQL);
				$this->SaveAccountLedger($voucher_no,$bill_id,$voucher_date,$mallowance_id,$voucher_type,$description,$srow->medical_allowance,"Dr","I");
			   }
			   $this->InsertBillDetails($bill_id,$voucher_date,$salary_month,$account_head,$mallowance_id,$srow->medical_allowance,"Payable amount against employee medical allowance");
			} // end medical_allowance dr			    
			if($srow->transport_allowance >0){		
			   $tallowance_id = $this->GetAccountId(6,11,0,0,24);
			   $bcsql = "SELECT * FROM ".VOUCHER_DETAILS_TBL." WHERE account_id ='".$tallowance_id."' AND contra_id = $contra_id AND status ='1'";
			   $bquery = $this->db->query($bcsql);
			   if($bquery->num_rows() >0){
				$description ="Debit amount against employee communication allowance";
				$DSQL= "UPDATE ".VOUCHER_DETAILS_TBL." SET amount='".$srow->transport_allowance."', voucher_type='".$voucher_type."', description='".$description."' WHERE account_id='".$tallowance_id."' AND contra_id = ".$contra_id; $this->db->query($DSQL);			
				$this->SaveAccountLedger($voucher_no,$bill_id,$voucher_date,$tallowance_id,$voucher_type,$description,$srow->transport_allowance,"Cr","U");
			   }else{
				$description ="Debit amount against employee communication allowance";
				$DSQL="INSERT INTO ".VOUCHER_DETAILS_TBL."(contra_id,invoice_no,voucher_no,headtypes,account_id,amount,voucher_type,description,created_by) ";
				$DSQL.="VALUES('".$contra_id."','".$bill_id."','".$voucher_no."','Dr','".$tallowance_id."','".$srow->transport_allowance."','".$voucher_type."','".$description."','".$created_by."')";
				$this->db->query($DSQL);
				$this->SaveAccountLedger($voucher_no,$bill_id,$voucher_date,$tallowance_id,$voucher_type,$description,$srow->transport_allowance,"Dr","I");
			   }
			   $this->InsertBillDetails($bill_id,$voucher_date,$salary_month,$account_head,$tallowance_id,$srow->transport_allowance,"Payable amount against employee transport allowance");
			} // end transport_allowance dr	
			/*
			if($srow->communication_allowance >0){		
			   $callowance_id = $this->GetAccountId(6,11,0,0,24);
			   $bcsql = "SELECT * FROM ".VOUCHER_DETAILS_TBL." WHERE account_id ='".$callowance_id."' AND contra_id = $contra_id AND status ='1'";
			   $bquery = $this->db->query($bcsql);
			   if($bquery->num_rows() >0){
				$description ="Debit amount against employee communication allowance";
				$DSQL= "UPDATE ".VOUCHER_DETAILS_TBL." SET amount='".$srow->communication_allowance."', voucher_type='".$voucher_type."', description='".$description."' WHERE account_id='".$callowance_id."' AND contra_id = ".$contra_id; $this->db->query($DSQL);			
				$this->SaveAccountLedger($voucher_no,$bill_id,$voucher_date,$callowance_id,$voucher_type,$description,$srow->communication_allowance,"Cr","U");
			   }else{
				$description ="Debit amount against employee communication allowance";
				$DSQL="INSERT INTO ".VOUCHER_DETAILS_TBL."(contra_id,invoice_no,voucher_no,headtypes,account_id,amount,voucher_type,description,created_by) ";
				$DSQL.="VALUES('".$contra_id."','".$bill_id."','".$voucher_no."','Dr','".$callowance_id."','".$srow->communication_allowance."','".$voucher_type."','".$description."','".$created_by."')";
				$this->db->query($DSQL);
				$this->SaveAccountLedger($voucher_no,$bill_id,$voucher_date,$callowance_id,$voucher_type,$description,$srow->communication_allowance,"Dr","I");
			   }
			   $this->InsertBillDetails($bill_id,$voucher_date,$salary_month,$account_head,$callowance_id,$srow->communication_allowance,"Payable amount against employee communication allowance");
			} // end communication_allowance dr			    
			*/				
			if($srow->others_allowance >0){		
			   $oallowance_id = $this->GetAccountId(6,11,0,0,25);
			   $bcsql = "SELECT * FROM ".VOUCHER_DETAILS_TBL." WHERE account_id ='".$oallowance_id."' AND contra_id = $contra_id AND status ='1'";
			   $bquery = $this->db->query($bcsql);
			   if($bquery->num_rows() >0){
				$description ="Debit amount against employee others allowance";
				$DSQL= "UPDATE ".VOUCHER_DETAILS_TBL." SET amount='".$srow->others_allowance."', voucher_type='".$voucher_type."', description='".$description."' WHERE account_id='".$oallowance_id."' AND contra_id = ".$contra_id; $this->db->query($DSQL);			
				$this->SaveAccountLedger($voucher_no,$bill_id,$voucher_date,$oallowance_id,$voucher_type,$description,$srow->others_allowance,"Cr","U");
			   }else{
				$description ="Debit amount against employee others allowance";
				$DSQL="INSERT INTO ".VOUCHER_DETAILS_TBL."(contra_id,invoice_no,voucher_no,headtypes,account_id,amount,voucher_type,description,created_by) ";
				$DSQL.="VALUES('".$contra_id."','".$bill_id."','".$voucher_no."','Dr','".$oallowance_id."','".$srow->others_allowance."','".$voucher_type."','".$description."','".$created_by."')";
				$this->db->query($DSQL);
				$this->SaveAccountLedger($voucher_no,$bill_id,$voucher_date,$oallowance_id,$voucher_type,$description,$srow->others_allowance,"Dr","I");
			   }
			   $this->InsertBillDetails($bill_id,$voucher_date,$salary_month,$account_head,$oallowance_id,$srow->others_allowance,"Payable amount against employee others allowance");
			} // end others_allowance dr			    
			if($srow->festival_bonus >0){		
			   $festivalbonus_id = $this->GetAccountId(6,11,0,0,23);
			   $bcsql = "SELECT * FROM ".VOUCHER_DETAILS_TBL." WHERE account_id ='".$festivalbonus_id."' AND contra_id = $contra_id AND status ='1'";
			   $bquery = $this->db->query($bcsql);
			   if($bquery->num_rows() >0){
				$description ="Debit amount against employee others allowance";
				$DSQL= "UPDATE ".VOUCHER_DETAILS_TBL." SET amount='".$srow->festival_bonus."', voucher_type='".$voucher_type."', description='".$description."' WHERE account_id='".$festivalbonus_id."' AND contra_id = ".$contra_id; $this->db->query($DSQL);			
				$this->SaveAccountLedger($voucher_no,$bill_id,$voucher_date,$festivalbonus_id,$voucher_type,$description,$srow->festival_bonus,"Cr","U");
			   }else{
				$description ="Debit amount against employee festival bonus";
				$DSQL="INSERT INTO ".VOUCHER_DETAILS_TBL."(contra_id,invoice_no,voucher_no,headtypes,account_id,amount,voucher_type,description,created_by) ";
				$DSQL.="VALUES('".$contra_id."','".$bill_id."','".$voucher_no."','Dr','".$festivalbonus_id."','".$srow->festival_bonus."','".$voucher_type."','".$description."','".$created_by."')";
				$this->db->query($DSQL);
				$this->SaveAccountLedger($voucher_no,$bill_id,$voucher_date,$festivalbonus_id,$voucher_type,$description,$srow->festival_bonus,"Dr","I");
			   }
			   $this->InsertBillDetails($bill_id,$voucher_date,$salary_month,$account_head,$festivalbonus_id,$srow->festival_bonus,"Payable amount against employee festival bonus");
			} // end festival_bonus dr
			
			//===== Insert All Cr Account =====
			
			/***** Cr Employee Account *****/
			if($account_head >0){				   
			   $dcsql = "SELECT * FROM ".VOUCHER_DETAILS_TBL." WHERE account_id ='".$account_head."' AND contra_id = $contra_id AND status ='1'";
			   $dquery = $this->db->query($dcsql);
			   if($dquery->num_rows() >0){
			   $description ="Payable amount against monthly net salary";
			   $DSQL= "UPDATE ".VOUCHER_DETAILS_TBL." SET amount='".$srow->net_salary."', voucher_type='".$voucher_type."', description='".$description."' WHERE account_id='".$account_head."' AND contra_id = ".$contra_id; $this->db->query($DSQL);			
			   $this->SaveAccountLedger($voucher_no,$bill_id,$voucher_date,$account_head,$voucher_type,$description,$srow->net_salary,"Cr","U");
			   }else{
				//==== Start Cr Employee Account =====
				$DSQL="INSERT INTO ".VOUCHER_DETAILS_TBL."(contra_id,invoice_no,voucher_no,headtypes,account_id,amount,voucher_type,description,created_by) ";
				$DSQL.="VALUES('".$contra_id."','".$bill_id."','".$voucher_no."','Cr','".$account_head."','".$srow->net_salary."','".$voucher_type."','".$description."','".$created_by."')";
				$this->db->query($DSQL);
				$this->SaveAccountLedger($voucher_no,$bill_id,$voucher_date,$account_head,$voucher_type,$description,$srow->net_salary,"Cr","I");
			   }//end else
			}		   
			/***** Cr P.F Account *****/
			if($srow->pf >0 && $erow->pf_achead_mapping >0){
			   $pf_account = $erow->pf_achead_mapping;
			   $dcsql = "SELECT * FROM ".VOUCHER_DETAILS_TBL." WHERE account_id ='".$pf_account."' AND contra_id = $contra_id AND status ='1'";
			   $dquery = $this->db->query($dcsql);
			   if($dquery->num_rows() >0){
			   $description ="Credit amount against provident fund";
			   $DSQL= "UPDATE ".VOUCHER_DETAILS_TBL." SET amount='".$srow->pf."', voucher_type='".$voucher_type."', description='".$description."' WHERE account_id='".$pf_account."' AND contra_id = ".$contra_id; $this->db->query($DSQL);			
			   $this->SaveAccountLedger($voucher_no,$bill_id,$voucher_date,$pf_account,$voucher_type,$description,$srow->pf,"Cr","U");
			   }else{
				//==== Start Cr P.F Account =====
				$pf_account = $erow->pf_achead_mapping;
				$DSQL="INSERT INTO ".VOUCHER_DETAILS_TBL."(contra_id,invoice_no,voucher_no,headtypes,account_id,amount,voucher_type,description,created_by) ";
				$DSQL.="VALUES('".$contra_id."','".$bill_id."','".$voucher_no."','Cr','".$pf_account."','".$srow->pf."','".$voucher_type."','".$description."','".$created_by."')";
				$this->db->query($DSQL);
				$this->SaveAccountLedger($voucher_no,$bill_id,$voucher_date,$pf_account,$voucher_type,$description,$srow->pf,"Cr","I");
			   }//end else
			}		   
			/***** Cr Loan Account *****/
			if($srow->loan >0 && $erow->loan_achead_mapping >0){
			   $loan_account = $erow->loan_achead_mapping;
			   $dcsql = "SELECT * FROM ".VOUCHER_DETAILS_TBL." WHERE account_id ='".$loan_account."' AND contra_id = $contra_id AND status ='1'";
			   $dquery = $this->db->query($dcsql);
			   if($dquery->num_rows() >0){
			   $description ="Adjust loan against previous loan & advance";
			   $DSQL= "UPDATE ".VOUCHER_DETAILS_TBL." SET amount='".$srow->loan."', voucher_type='".$voucher_type."', description='".$description."' WHERE account_id='".$pf_account."' AND contra_id = ".$contra_id; $this->db->query($DSQL);			
			   $this->SaveAccountLedger($voucher_no,$bill_id,$voucher_date,$loan_account,$voucher_type,$description,$srow->loan,"Cr","U");
			   }else{
				//==== Start Cr Loan Account =====
				$loan_account = $erow->loan_achead_mapping;
				$DSQL="INSERT INTO ".VOUCHER_DETAILS_TBL."(contra_id,invoice_no,voucher_no,headtypes,account_id,amount,voucher_type,description,created_by) ";
				$DSQL.="VALUES('".$contra_id."','".$bill_id."','".$voucher_no."','Cr','".$loan_account."','".$srow->loan."','".$voucher_type."','".$description."','".$created_by."')";
				$this->db->query($DSQL);
				$this->SaveAccountLedger($voucher_no,$bill_id,$voucher_date,$loan_account,$voucher_type,$description,$srow->loan,"Cr","I");
			   }//end else
			}		   
			/***** Cr salary income_tax Account *****/
			if($srow->income_tax >0){
			   $incometax_id = $this->GetAccountId(6,11,41,17,6);
			   $dcsql = "SELECT * FROM ".VOUCHER_DETAILS_TBL." WHERE account_id ='".$incometax_id."' AND contra_id = $contra_id AND status ='1'";
			   $dquery = $this->db->query($dcsql);
			   if($dquery->num_rows() >0){
			   $description ="Credit amount against salary income tax";
			   $DSQL= "UPDATE ".VOUCHER_DETAILS_TBL." SET amount='".$srow->income_tax."', voucher_type='".$voucher_type."', description='".$description."' WHERE account_id='".$pf_account."' AND contra_id = ".$contra_id; $this->db->query($DSQL);			
			   $this->SaveAccountLedger($voucher_no,$bill_id,$voucher_date,$incometax_id,$voucher_type,$description,$srow->income_tax,"Cr","U");
			   }else{
				//==== Start Cr salary income_tax Account =====
				$incometax_id = $this->GetAccountId(6,11,41,17,6);
				$DSQL="INSERT INTO ".VOUCHER_DETAILS_TBL."(contra_id,invoice_no,voucher_no,headtypes,account_id,amount,voucher_type,description,created_by) ";
				$DSQL.="VALUES('".$contra_id."','".$bill_id."','".$voucher_no."','Cr','".$incometax_id."','".$srow->income_tax."','".$voucher_type."','".$description."','".$created_by."')";
				$this->db->query($DSQL);
				$this->SaveAccountLedger($voucher_no,$bill_id,$voucher_date,$incometax_id,$voucher_type,$description,$srow->income_tax,"Cr","I");
			   }//end else
			}		   
			/***** Cr salary absent_fine Account *****/
			if($srow->absent_fine >0){
			   $absentfine_id = $this->GetAccountId(4,8,51,9,16);
			   $dcsql = "SELECT * FROM ".VOUCHER_DETAILS_TBL." WHERE account_id ='".$absentfine_id."' AND contra_id = $contra_id AND status ='1'";
			   $dquery = $this->db->query($dcsql);
			   if($dquery->num_rows() >0){
			   $description ="Credit amount against salary income tax";
			   $DSQL= "UPDATE ".VOUCHER_DETAILS_TBL." SET amount='".$srow->absent_fine."', voucher_type='".$voucher_type."', description='".$description."' WHERE account_id='".$pf_account."' AND contra_id = ".$contra_id; $this->db->query($DSQL);			
			   $this->SaveAccountLedger($voucher_no,$bill_id,$voucher_date,$absentfine_id,$voucher_type,$description,$srow->absent_fine,"Cr","U");
			   }else{
				//==== Start Cr salary absent_fine Account =====
				$absentfine_id = $this->GetAccountId(4,8,51,9,16);
				$DSQL="INSERT INTO ".VOUCHER_DETAILS_TBL."(contra_id,invoice_no,voucher_no,headtypes,account_id,amount,voucher_type,description,created_by) ";
				$DSQL.="VALUES('".$contra_id."','".$bill_id."','".$voucher_no."','Cr','".$absentfine_id."','".$srow->absent_fine."','".$voucher_type."','".$description."','".$created_by."')";
				$this->db->query($DSQL);
				$this->SaveAccountLedger($voucher_no,$bill_id,$voucher_date,$absentfine_id,$voucher_type,$description,$srow->absent_fine,"Cr","I");
			   }//end else
			}
		   return $contra_id;
		   
		}else{
			$SQL= "INSERT INTO ".VOUCHER_MASTER_TBL."(invoice_no,institute_id,branch_id,session_id,version_id,voucher_no,voucher_date,dr_amount,cr_amount, mode_of_payment,voucher_type,description,created_by) ";
		    $SQL.="VALUES('".$bill_id."','".$institute_id."','".$branch_id."','".$session_id."','".$version_id."','".$voucher_no."','".$voucher_date."','".$dr_amount."','".$cr_amount."','".$mode_of_payment."','".$voucher_type."','".$description."','".$created_by."')";
		    if($voucher_no !=""){
		      $this->db->query($SQL);
		      $contra_id = $this->db->insert_id();
		      if($contra_id >0){
			    //==== Start All Dr Account =====			    
			    if($srow->cash_salary >0){		
			       $cash_salary_id = $this->GetAccountId(6,11,0,0,18);
				   $bcsql = "SELECT * FROM ".VOUCHER_DETAILS_TBL." WHERE account_id ='".$cash_salary_id."' AND contra_id = $contra_id AND status ='1'";
				   $bquery = $this->db->query($bcsql);
				   if($bquery->num_rows() >0){
				    $description ="Debit amount against employee cash salary";
				    $DSQL= "UPDATE ".VOUCHER_DETAILS_TBL." SET amount='".$srow->cash_salary."', voucher_type='".$voucher_type."', description='".$description."' WHERE account_id='".$cash_salary_id."' AND contra_id = ".$contra_id; $this->db->query($DSQL);			
				    $this->SaveAccountLedger($voucher_no,$bill_id,$voucher_date,$cash_salary_id,$voucher_type,$description,$srow->cash_salary,"Cr","U");
				   }else{					   
					$description ="Debit amount against employee cash salary";
					$DSQL="INSERT INTO ".VOUCHER_DETAILS_TBL."(contra_id,invoice_no,voucher_no,headtypes,account_id,amount,voucher_type,description,created_by) ";
					$DSQL.="VALUES('".$contra_id."','".$bill_id."','".$voucher_no."','Dr','".$cash_salary_id."','".$srow->cash_salary."','".$voucher_type."','".$description."','".$created_by."')";
					$this->db->query($DSQL);
					$this->SaveAccountLedger($voucher_no,$bill_id,$voucher_date,$cash_salary_id,$voucher_type,$description,$srow->basic_salary,"Dr","I");
				   }
				   $this->InsertBillDetails($bill_id,$voucher_date,$salary_month,$account_head,$cash_salary_id,$srow->cash_salary,"Payable amount against employee cash salary");
				} // end cash_salary dr
							    
			    if($srow->tnt_allowance >0){		
			       $tnt_allowance_id = $this->GetAccountId(6,11,0,0,28);
				   $bcsql = "SELECT * FROM ".VOUCHER_DETAILS_TBL." WHERE account_id ='".$tnt_allowance_id."' AND contra_id = $contra_id AND status ='1'";
				   $bquery = $this->db->query($bcsql);
				   if($bquery->num_rows() >0){
				    $description ="Debit amount against employee t&t allowance";
				    $DSQL= "UPDATE ".VOUCHER_DETAILS_TBL." SET amount='".$srow->tnt_allowance."', voucher_type='".$voucher_type."', description='".$description."' WHERE account_id='".$tnt_allowance_id."' AND contra_id = ".$contra_id; $this->db->query($DSQL);			
				    $this->SaveAccountLedger($voucher_no,$bill_id,$voucher_date,$tnt_allowance_id,$voucher_type,$description,$srow->tnt_allowance,"Cr","U");
				   }else{					   
					$description ="Debit amount against employee t&t allowance";
					$DSQL="INSERT INTO ".VOUCHER_DETAILS_TBL."(contra_id,invoice_no,voucher_no,headtypes,account_id,amount,voucher_type,description,created_by) ";
					$DSQL.="VALUES('".$contra_id."','".$bill_id."','".$voucher_no."','Dr','".$tnt_allowance_id."','".$srow->tnt_allowance."','".$voucher_type."','".$description."','".$created_by."')";
					$this->db->query($DSQL);
					$this->SaveAccountLedger($voucher_no,$bill_id,$voucher_date,$tnt_allowance_id,$voucher_type,$description,$srow->tnt_allowance,"Dr","I");
				   }
				   $this->InsertBillDetails($bill_id,$voucher_date,$salary_month,$account_head,$tnt_allowance_id,$srow->tnt_allowance,"Payable amount against employee t&t allowance");
				} // end tnt_allowance dr
				
							    
			    if($srow->others_payble >0){		
			       $others_payble_id = $this->GetAccountId(6,11,0,0,29);
				   $bcsql = "SELECT * FROM ".VOUCHER_DETAILS_TBL." WHERE account_id ='".$others_payble_id."' AND contra_id = $contra_id AND status ='1'";
				   $bquery = $this->db->query($bcsql);
				   if($bquery->num_rows() >0){
				    $description ="Debit amount against employee others payble";
				    $DSQL= "UPDATE ".VOUCHER_DETAILS_TBL." SET amount='".$srow->others_payble."', voucher_type='".$voucher_type."', description='".$description."' WHERE account_id='".$others_payble_id."' AND contra_id = ".$contra_id; $this->db->query($DSQL);			
				    $this->SaveAccountLedger($voucher_no,$bill_id,$voucher_date,$others_payble_id,$voucher_type,$description,$srow->others_payble,"Cr","U");
				   }else{					   
					$description ="Debit amount against employee others payble";
					$DSQL="INSERT INTO ".VOUCHER_DETAILS_TBL."(contra_id,invoice_no,voucher_no,headtypes,account_id,amount,voucher_type,description,created_by) ";
					$DSQL.="VALUES('".$contra_id."','".$bill_id."','".$voucher_no."','Dr','".$others_payble_id."','".$srow->others_payble."','".$voucher_type."','".$description."','".$created_by."')";
					$this->db->query($DSQL);
					$this->SaveAccountLedger($voucher_no,$bill_id,$voucher_date,$others_payble_id,$voucher_type,$description,$srow->others_payble,"Dr","I");
				   }
				   $this->InsertBillDetails($bill_id,$voucher_date,$salary_month,$account_head,$others_payble_id,$srow->others_payble,"Payable amount against employee others payble");
				} // end tnt_allowance dr
							    
			    if($srow->basic_salary >0){		
			       $basic_salary_id = $this->GetAccountId(6,11,0,0,19);
				   $bcsql = "SELECT * FROM ".VOUCHER_DETAILS_TBL." WHERE account_id ='".$basic_salary_id."' AND contra_id = $contra_id AND status ='1'";
				   $bquery = $this->db->query($bcsql);
				   if($bquery->num_rows() >0){
				    $description ="Debit amount against employee basic salary";
				    $DSQL= "UPDATE ".VOUCHER_DETAILS_TBL." SET amount='".$srow->basic_salary."', voucher_type='".$voucher_type."', description='".$description."' WHERE account_id='".$basic_salary_id."' AND contra_id = ".$contra_id; $this->db->query($DSQL);			
				    $this->SaveAccountLedger($voucher_no,$bill_id,$voucher_date,$basic_salary_id,$voucher_type,$description,$srow->basic_salary,"Cr","U");
				   }else{					   
					$description ="Debit amount against employee basic salary";
					$DSQL="INSERT INTO ".VOUCHER_DETAILS_TBL."(contra_id,invoice_no,voucher_no,headtypes,account_id,amount,voucher_type,description,created_by) ";
					$DSQL.="VALUES('".$contra_id."','".$bill_id."','".$voucher_no."','Dr','".$basic_salary_id."','".$srow->basic_salary."','".$voucher_type."','".$description."','".$created_by."')";
					$this->db->query($DSQL);
					$this->SaveAccountLedger($voucher_no,$bill_id,$voucher_date,$basic_salary_id,$voucher_type,$description,$srow->basic_salary,"Dr","I");
				   }
				   $this->InsertBillDetails($bill_id,$voucher_date,$salary_month,$account_head,$basic_salary_id,$srow->basic_salary,"Payable amount against employee basic salary");
				} // end basic_salary dr
			    if($srow->house_rent >0){		
				   $house_rent_id = $this->GetAccountId(6,11,0,0,20);
				   $bcsql = "SELECT * FROM ".VOUCHER_DETAILS_TBL." WHERE account_id ='".$house_rent_id."' AND contra_id = $contra_id AND status ='1'";
				   $bquery = $this->db->query($bcsql);
				   if($bquery->num_rows() >0){
				    $description ="Debit amount against employee house rent";
				    $DSQL= "UPDATE ".VOUCHER_DETAILS_TBL." SET amount='".$srow->house_rent."', voucher_type='".$voucher_type."', description='".$description."' WHERE account_id='".$house_rent_id."' AND contra_id = ".$contra_id; $this->db->query($DSQL);			
				    $this->SaveAccountLedger($voucher_no,$bill_id,$voucher_date,$house_rent_id,$voucher_type,$description,$srow->house_rent,"Cr","U");
				   }else{
					$description ="Debit amount against employee house rent";
					$DSQL="INSERT INTO ".VOUCHER_DETAILS_TBL."(contra_id,invoice_no,voucher_no,headtypes,account_id,amount,voucher_type,description,created_by) ";
					$DSQL.="VALUES('".$contra_id."','".$bill_id."','".$voucher_no."','Dr','".$house_rent_id."','".$srow->house_rent."','".$voucher_type."','".$description."','".$created_by."')";
					$this->db->query($DSQL);
					$this->SaveAccountLedger($voucher_no,$bill_id,$voucher_date,$house_rent_id,$voucher_type,$description,$srow->house_rent,"Dr","I");
				   }
				   $this->InsertBillDetails($bill_id,$voucher_date,$salary_month,$account_head,$house_rent_id,$srow->house_rent,"Payable amount against employee house rent");
			    } // end house_rent dr			    
			    if($srow->medical_allowance >0){		
				   $mallowance_id = $this->GetAccountId(6,11,0,0,21);
				   $bcsql = "SELECT * FROM ".VOUCHER_DETAILS_TBL." WHERE account_id ='".$mallowance_id."' AND contra_id = $contra_id AND status ='1'";
				   $bquery = $this->db->query($bcsql);
				   if($bquery->num_rows() >0){
				    $description ="Debit amount against employee medical allowance";
				    $DSQL= "UPDATE ".VOUCHER_DETAILS_TBL." SET amount='".$srow->medical_allowance."', voucher_type='".$voucher_type."', description='".$description."' WHERE account_id='".$mallowance_id."' AND contra_id = ".$contra_id; $this->db->query($DSQL);			
				    $this->SaveAccountLedger($voucher_no,$bill_id,$voucher_date,$mallowance_id,$voucher_type,$description,$srow->medical_allowance,"Cr","U");
				   }else{
					$description ="Debit amount against employee medical allowance";
					$DSQL="INSERT INTO ".VOUCHER_DETAILS_TBL."(contra_id,invoice_no,voucher_no,headtypes,account_id,amount,voucher_type,description,created_by) ";
					$DSQL.="VALUES('".$contra_id."','".$bill_id."','".$voucher_no."','Dr','".$mallowance_id."','".$srow->medical_allowance."','".$voucher_type."','".$description."','".$created_by."')";
					$this->db->query($DSQL);
					$this->SaveAccountLedger($voucher_no,$bill_id,$voucher_date,$mallowance_id,$voucher_type,$description,$srow->medical_allowance,"Dr","I");
				   }
				   $this->InsertBillDetails($bill_id,$voucher_date,$salary_month,$account_head,$mallowance_id,$srow->medical_allowance,"Payable amount against employee medical allowance");
			    } // end medical_allowance dr			    
			    if($srow->transport_allowance >0){		
			       $tallowance_id = $this->GetAccountId(6,11,0,0,22);
				   $bcsql = "SELECT * FROM ".VOUCHER_DETAILS_TBL." WHERE account_id ='".$tallowance_id."' AND contra_id = $contra_id AND status ='1'";
				   $bquery = $this->db->query($bcsql);
				   if($bquery->num_rows() >0){
				    $description ="Debit amount against employee transport allowance";
				    $DSQL= "UPDATE ".VOUCHER_DETAILS_TBL." SET amount='".$srow->transport_allowance."', voucher_type='".$voucher_type."', description='".$description."' WHERE account_id='".$tallowance_id."' AND contra_id = ".$contra_id; $this->db->query($DSQL);			
				    $this->SaveAccountLedger($voucher_no,$bill_id,$voucher_date,$tallowance_id,$voucher_type,$description,$srow->transport_allowance,"Cr","U");
				   }else{
					$description ="Debit amount against employee transport allowance";
					$DSQL="INSERT INTO ".VOUCHER_DETAILS_TBL."(contra_id,invoice_no,voucher_no,headtypes,account_id,amount,voucher_type,description,created_by) ";
					$DSQL.="VALUES('".$contra_id."','".$bill_id."','".$voucher_no."','Dr','".$tallowance_id."','".$srow->transport_allowance."','".$voucher_type."','".$description."','".$created_by."')";
					$this->db->query($DSQL);
					$this->SaveAccountLedger($voucher_no,$bill_id,$voucher_date,$tallowance_id,$voucher_type,$description,$srow->transport_allowance,"Dr","I");
				   }
				   $this->InsertBillDetails($bill_id,$voucher_date,$salary_month,$account_head,$tallowance_id,$srow->transport_allowance,"Payable amount against employee transport allowance");
			    } // end transport_allowance dr			    
			    if($srow->communication_allowance >0){		
			       $callowance_id = $this->GetAccountId(6,11,0,0,24);
				   $bcsql = "SELECT * FROM ".VOUCHER_DETAILS_TBL." WHERE account_id ='".$callowance_id."' AND contra_id = $contra_id AND status ='1'";
				   $bquery = $this->db->query($bcsql);
				   if($bquery->num_rows() >0){
				    $description ="Debit amount against employee communication allowance";
				    $DSQL= "UPDATE ".VOUCHER_DETAILS_TBL." SET amount='".$srow->communication_allowance."', voucher_type='".$voucher_type."', description='".$description."' WHERE account_id='".$callowance_id."' AND contra_id = ".$contra_id; $this->db->query($DSQL);			
				    $this->SaveAccountLedger($voucher_no,$bill_id,$voucher_date,$callowance_id,$voucher_type,$description,$srow->communication_allowance,"Cr","U");
				   }else{
					$description ="Debit amount against employee communication allowance";
					$DSQL="INSERT INTO ".VOUCHER_DETAILS_TBL."(contra_id,invoice_no,voucher_no,headtypes,account_id,amount,voucher_type,description,created_by) ";
					$DSQL.="VALUES('".$contra_id."','".$bill_id."','".$voucher_no."','Dr','".$callowance_id."','".$srow->communication_allowance."','".$voucher_type."','".$description."','".$created_by."')";
					$this->db->query($DSQL);
					$this->SaveAccountLedger($voucher_no,$bill_id,$voucher_date,$callowance_id,$voucher_type,$description,$srow->communication_allowance,"Dr","I");
				   }
				   $this->InsertBillDetails($bill_id,$voucher_date,$salary_month,$account_head,$callowance_id,$srow->communication_allowance,"Payable amount against employee communication allowance");
			    } // end communication_allowance dr			    
			    			    
			    if($srow->others_allowance >0){		
				   $oallowance_id = $this->GetAccountId(6,11,0,0,25);
				   $bcsql = "SELECT * FROM ".VOUCHER_DETAILS_TBL." WHERE account_id ='".$oallowance_id."' AND contra_id = $contra_id AND status ='1'";
				   $bquery = $this->db->query($bcsql);
				   if($bquery->num_rows() >0){
				    $description ="Debit amount against employee others allowance";
				    $DSQL= "UPDATE ".VOUCHER_DETAILS_TBL." SET amount='".$srow->others_allowance."', voucher_type='".$voucher_type."', description='".$description."' WHERE account_id='".$oallowance_id."' AND contra_id = ".$contra_id; $this->db->query($DSQL);			
				    $this->SaveAccountLedger($voucher_no,$bill_id,$voucher_date,$oallowance_id,$voucher_type,$description,$srow->others_allowance,"Cr","U");
				   }else{
					$description ="Debit amount against employee others allowance";
					$DSQL="INSERT INTO ".VOUCHER_DETAILS_TBL."(contra_id,invoice_no,voucher_no,headtypes,account_id,amount,voucher_type,description,created_by) ";
					$DSQL.="VALUES('".$contra_id."','".$bill_id."','".$voucher_no."','Dr','".$oallowance_id."','".$srow->others_allowance."','".$voucher_type."','".$description."','".$created_by."')";
					$this->db->query($DSQL);
					$this->SaveAccountLedger($voucher_no,$bill_id,$voucher_date,$oallowance_id,$voucher_type,$description,$srow->others_allowance,"Dr","I");
				   }
				   $this->InsertBillDetails($bill_id,$voucher_date,$salary_month,$account_head,$oallowance_id,$srow->others_allowance,"Payable amount against employee others allowance");
			    } // end others_allowance dr			    
			    if($srow->festival_bonus >0){		
			       $festivalbonus_id = $this->GetAccountId(6,11,0,0,23);
				   $bcsql = "SELECT * FROM ".VOUCHER_DETAILS_TBL." WHERE account_id ='".$festivalbonus_id."' AND contra_id = $contra_id AND status ='1'";
				   $bquery = $this->db->query($bcsql);
				   if($bquery->num_rows() >0){
				    $description ="Debit amount against employee others allowance";
				    $DSQL= "UPDATE ".VOUCHER_DETAILS_TBL." SET amount='".$srow->festival_bonus."', voucher_type='".$voucher_type."', description='".$description."' WHERE account_id='".$festivalbonus_id."' AND contra_id = ".$contra_id; $this->db->query($DSQL);			
				    $this->SaveAccountLedger($voucher_no,$bill_id,$voucher_date,$festivalbonus_id,$voucher_type,$description,$srow->festival_bonus,"Cr","U");
				   }else{
					$description ="Debit amount against employee festival bonus";
					$DSQL="INSERT INTO ".VOUCHER_DETAILS_TBL."(contra_id,invoice_no,voucher_no,headtypes,account_id,amount,voucher_type,description,created_by) ";
					$DSQL.="VALUES('".$contra_id."','".$bill_id."','".$voucher_no."','Dr','".$festivalbonus_id."','".$srow->festival_bonus."','".$voucher_type."','".$description."','".$created_by."')";
					$this->db->query($DSQL);
					$this->SaveAccountLedger($voucher_no,$bill_id,$voucher_date,$festivalbonus_id,$voucher_type,$description,$srow->festival_bonus,"Dr","I");
				   }
				   $this->InsertBillDetails($bill_id,$voucher_date,$salary_month,$account_head,$festivalbonus_id,$srow->festival_bonus,"Payable amount against employee festival bonus");
			    } // end festival_bonus dr
			    
			    //===== Insert All Cr Account =====
			    
				/***** Cr Employee Account *****/
			    if($account_head >0){				   
				   $dcsql = "SELECT * FROM ".VOUCHER_DETAILS_TBL." WHERE account_id ='".$account_head."' AND contra_id = $contra_id AND status ='1'";
				   $dquery = $this->db->query($dcsql);
				   if($dquery->num_rows() >0){
				   $description ="Payable amount against monthly net salary";
				   $DSQL= "UPDATE ".VOUCHER_DETAILS_TBL." SET amount='".$srow->net_salary."', voucher_type='".$voucher_type."', description='".$description."' WHERE account_id='".$account_head."' AND contra_id = ".$contra_id; $this->db->query($DSQL);			
				   $this->SaveAccountLedger($voucher_no,$bill_id,$voucher_date,$account_head,$voucher_type,$description,$srow->net_salary,"Cr","U");
				   }else{
					//==== Start Cr Employee Account =====
					$DSQL="INSERT INTO ".VOUCHER_DETAILS_TBL."(contra_id,invoice_no,voucher_no,headtypes,account_id,amount,voucher_type,description,created_by) ";
					$DSQL.="VALUES('".$contra_id."','".$bill_id."','".$voucher_no."','Cr','".$account_head."','".$srow->net_salary."','".$voucher_type."','".$description."','".$created_by."')";
					$this->db->query($DSQL);
					$this->SaveAccountLedger($voucher_no,$bill_id,$voucher_date,$account_head,$voucher_type,$description,$srow->net_salary,"Cr","I");
				   }//end else
			    }		   
			    /***** Cr P.F Account *****/
			    if($srow->pf >0 && $erow->pf_achead_mapping >0){
				   $pf_account = $erow->pf_achead_mapping;
				   $dcsql = "SELECT * FROM ".VOUCHER_DETAILS_TBL." WHERE account_id ='".$pf_account."' AND contra_id = $contra_id AND status ='1'";
				   $dquery = $this->db->query($dcsql);
				   if($dquery->num_rows() >0){
				   $description ="Credit amount against provident fund";
				   $DSQL= "UPDATE ".VOUCHER_DETAILS_TBL." SET amount='".$srow->pf."', voucher_type='".$voucher_type."', description='".$description."' WHERE account_id='".$pf_account."' AND contra_id = ".$contra_id; $this->db->query($DSQL);			
				   $this->SaveAccountLedger($voucher_no,$bill_id,$voucher_date,$pf_account,$voucher_type,$description,$srow->pf,"Cr","U");
				   }else{
					//==== Start Cr P.F Account =====
					$pf_account = $erow->pf_achead_mapping;
					$DSQL="INSERT INTO ".VOUCHER_DETAILS_TBL."(contra_id,invoice_no,voucher_no,headtypes,account_id,amount,voucher_type,description,created_by) ";
					$DSQL.="VALUES('".$contra_id."','".$bill_id."','".$voucher_no."','Cr','".$pf_account."','".$srow->pf."','".$voucher_type."','".$description."','".$created_by."')";
					$this->db->query($DSQL);
					$this->SaveAccountLedger($voucher_no,$bill_id,$voucher_date,$pf_account,$voucher_type,$description,$srow->pf,"Cr","I");
				   }//end else
			    }		   
			    /***** Cr Loan Account *****/
			    if($srow->loan >0 && $erow->loan_achead_mapping >0){
				   $loan_account = $erow->loan_achead_mapping;
				   $dcsql = "SELECT * FROM ".VOUCHER_DETAILS_TBL." WHERE account_id ='".$loan_account."' AND contra_id = $contra_id AND status ='1'";
				   $dquery = $this->db->query($dcsql);
				   if($dquery->num_rows() >0){
				   $description ="Adjust loan against previous loan & advance";
				   $DSQL= "UPDATE ".VOUCHER_DETAILS_TBL." SET amount='".$srow->loan."', voucher_type='".$voucher_type."', description='".$description."' WHERE account_id='".$pf_account."' AND contra_id = ".$contra_id; $this->db->query($DSQL);			
				   $this->SaveAccountLedger($voucher_no,$bill_id,$voucher_date,$loan_account,$voucher_type,$description,$srow->loan,"Cr","U");
				   }else{
					//==== Start Cr Loan Account =====
					$loan_account = $erow->loan_achead_mapping;
					$DSQL="INSERT INTO ".VOUCHER_DETAILS_TBL."(contra_id,invoice_no,voucher_no,headtypes,account_id,amount,voucher_type,description,created_by) ";
					$DSQL.="VALUES('".$contra_id."','".$bill_id."','".$voucher_no."','Cr','".$loan_account."','".$srow->loan."','".$voucher_type."','".$description."','".$created_by."')";
					$this->db->query($DSQL);
					$this->SaveAccountLedger($voucher_no,$bill_id,$voucher_date,$loan_account,$voucher_type,$description,$srow->loan,"Cr","I");
				   }//end else
			    }		   
			    /***** Cr salary income_tax Account *****/
			    if($srow->income_tax >0){
				   $incometax_id = $this->GetAccountId(6,11,41,17,6);
				   $dcsql = "SELECT * FROM ".VOUCHER_DETAILS_TBL." WHERE account_id ='".$incometax_id."' AND contra_id = $contra_id AND status ='1'";
				   $dquery = $this->db->query($dcsql);
				   if($dquery->num_rows() >0){
				   $description ="Credit amount against salary income tax";
				   $DSQL= "UPDATE ".VOUCHER_DETAILS_TBL." SET amount='".$srow->income_tax."', voucher_type='".$voucher_type."', description='".$description."' WHERE account_id='".$pf_account."' AND contra_id = ".$contra_id; $this->db->query($DSQL);			
				   $this->SaveAccountLedger($voucher_no,$bill_id,$voucher_date,$incometax_id,$voucher_type,$description,$srow->income_tax,"Cr","U");
				   }else{
					//==== Start Cr salary income_tax Account =====					
					$DSQL="INSERT INTO ".VOUCHER_DETAILS_TBL."(contra_id,invoice_no,voucher_no,headtypes,account_id,amount,voucher_type,description,created_by) ";
					$DSQL.="VALUES('".$contra_id."','".$bill_id."','".$voucher_no."','Cr','".$incometax_id."','".$srow->income_tax."','".$voucher_type."','".$description."','".$created_by."')";
					$this->db->query($DSQL);
					$this->SaveAccountLedger($voucher_no,$bill_id,$voucher_date,$incometax_id,$voucher_type,$description,$srow->income_tax,"Cr","I");
				   }//end else
			    }		   
			    /***** Cr salary absent_fine Account *****/
			    if($srow->absent_fine >0){
				   $absentfine_id = $this->GetAccountId(4,8,51,9,16);
				   $dcsql = "SELECT * FROM ".VOUCHER_DETAILS_TBL." WHERE account_id ='".$absentfine_id."' AND contra_id = $contra_id AND status ='1'";
				   $dquery = $this->db->query($dcsql);
				   if($dquery->num_rows() >0){
				   $description ="Credit amount against salary income tax";
				   $DSQL= "UPDATE ".VOUCHER_DETAILS_TBL." SET amount='".$srow->absent_fine."', voucher_type='".$voucher_type."', description='".$description."' WHERE account_id='".$pf_account."' AND contra_id = ".$contra_id; $this->db->query($DSQL);			
				   $this->SaveAccountLedger($voucher_no,$bill_id,$voucher_date,$absentfine_id,$voucher_type,$description,$srow->absent_fine,"Cr","U");
				   }else{
					//==== Start Cr salary absent_fine Account =====
					$absentfine_id = $this->GetAccountId(4,8,51,9,16);
					$DSQL="INSERT INTO ".VOUCHER_DETAILS_TBL."(contra_id,invoice_no,voucher_no,headtypes,account_id,amount,voucher_type,description,created_by) ";
					$DSQL.="VALUES('".$contra_id."','".$bill_id."','".$voucher_no."','Cr','".$absentfine_id."','".$srow->absent_fine."','".$voucher_type."','".$description."','".$created_by."')";
					$this->db->query($DSQL);
					$this->SaveAccountLedger($voucher_no,$bill_id,$voucher_date,$absentfine_id,$voucher_type,$description,$srow->absent_fine,"Cr","I");
				   }//end else
			    }				
			   return $contra_id;
			  }else{
				return 0;  
			  }			  
		    }else{
				return 0;
			}
		}//end else
	}
	function DelMonthlySalaryBillRecord(){
		$bill_id= $this->input->post('id');
		$sql   = "SELECT * FROM ".SALARY_SHEET_TBL." WHERE bill_id ='".$bill_id."' AND status=2";
		$query = $this->db->query($sql);
		$salary_month   = $query->row()->salary_month;
		$salary_year    = $query->row()->salary_year;
		$institute_id   = $query->row()->institute_id;
		$branch_id      = $query->row()->branch_id;
		
		$ssql   = "SELECT * FROM ".SALARY_SHEET_TBL." WHERE salary_month =".$salary_month." AND salary_year =".$salary_year." AND status=2";
		$squery = $this->db->query($ssql);		
		if($squery->num_rows() >0){
			$bill_ids = array(); 
			foreach($squery->result() as $row){
			 $bill_ids[] = $row->bill_id;
			}
			$this->db->where('institute_id',$institute_id);	
			$this->db->where('branch_id',$branch_id);	
			$this->db->where_in('bill_id',$bill_ids);
			$this->db->delete(BILL_MASTER_TBL);
			
			$this->db->where('institute_id',$institute_id);	
			$this->db->where('branch_id',$branch_id);	
			$this->db->where_in('bill_id',$bill_ids);
			$this->db->delete(BILL_DETAILS_TBL);
			
			$this->db->where('institute_id',$institute_id);	
			$this->db->where('branch_id',$branch_id);	
			$this->db->where_in('invoice_no',$bill_ids);
			$this->db->delete(VOUCHER_MASTER_TBL);
			
			$this->db->where_in('invoice_no',$bill_ids);
			$this->db->delete(VOUCHER_DETAILS_TBL);
			$this->db->where_in('invoice_no',$bill_ids);
			$this->db->delete(ACC_LEDGER_TBL);
			
		    $USQL= "UPDATE ".SALARY_SHEET_TBL." SET bill_id='0', status=0 WHERE salary_month =".$salary_month." AND salary_year =".$salary_year." AND status=2";
			$this->db->query($USQL);
		}
		
	}
    function GetAccountId($group_id,$subsidiary_level1,$subsidiary_level2,$subsidiary_level3,$head_type=NULL){		
        $this->db->select('*');
        $this->db->from(ACC_HEAD_TBL);
		if($group_id >0){
			$this->db->where("group_id", $group_id);
		}
		if($subsidiary_level1 >0){
			$this->db->where("subsidiary_level1", $subsidiary_level1);
		}
		if($subsidiary_level2 >0){
			$this->db->where("subsidiary_level2", $subsidiary_level2);
		}
		if($subsidiary_level3 >0){
			$this->db->where("subsidiary_level3", $subsidiary_level3);
		}		
		if($head_type >0){
			$this->db->where("head_type", $head_type);
		}
		$this->db->where('status',1);
        $aquery = $this->db->get();
		return $aquery->row()->account_id;         
    }
	
	function SaveAccountLedger($voucher_no,$invoice_no,$received_date,$account_id,$transaction_type,$description,$amount,$headtype,$mode){
		$created_by	= $this->session->userdata('created_by');
		if($headtype=="Dr"){$dr=$amount; $cr=0;}else{$dr=0; $cr=$amount;}
		
		if($mode=="I"){		
		$SQL="INSERT INTO ".ACC_LEDGER_TBL."(voucher_no,invoice_no,received_date, 	account_id,transaction_type,description,dr,cr,created_by) ";
		$SQL.="VALUES('".$voucher_no."','".$invoice_no."','".$received_date."','".$account_id."','".$transaction_type."','".$description."','".$dr."','".$cr."','".$created_by."')";
		$this->db->query($SQL);
		}else{
		$CSQL= "UPDATE ".ACC_LEDGER_TBL." SET transaction_type='".$transaction_type."', description='".$description."', dr='".$dr."', cr='".$cr."' WHERE account_id='".$account_id."' AND invoice_no = ".$invoice_no;
		$this->db->query($CSQL);
		}

	}
	function AdjustCollection($bill_id){
	  $BSQL	= "SELECT * FROM ".BILL_ADJUST_HISTORY_TBL." WHERE bill_id = '".$bill_id."'";
	  $query = $this->db->query($BSQL);
	  $BNum  = $query->num_rows();
	  $due_amount = 0;
	  if($BNum >0){
	    foreach($query->result() as $row){
		$bill_id 			= $row->bill_id; 
		$dr_account 		= $row->dr_account; 
		$adjust_tbl 		= $row->adjust_tbl; 
		$adjust_ref 		= $row->adjust_ref;  
		$adjust_amount		= $row->adjust_amount; 
		$adjust_type		= $row->adjust_type; 
		$including_vat		= $row->including_vat;
		$headtypes      	= $this->getHeadType($dr_account);
		//======= adjust previous collection amount =====
		if($adjust_tbl=="bill_master" && $adjust_type=="+"){			 
			$HSql= "SELECT * FROM ".BILL_MASTER_TBL." WHERE bill_id = '".$bill_id."' AND bill_no='".$adjust_ref."'";
			$hquery 	= $this->db->query($HSql);
			$srow   	= $hquery->row();
			$paid_amount 	= ($srow->paid_amount+$adjust_amount);
			$due_amount 	= ($srow->due_amount-$adjust_amount); 
			$Usql="UPDATE ".BILL_MASTER_TBL." ";
			if($including_vat >0){
				$Usql.= " SET vat_paid=1,";
		    }else{
				$Usql.= "SET ";
		    }
			$Usql.="paid_amount='$paid_amount', due_amount='$due_amount' WHERE bill_id='".$bill_id."' AND bill_no='".$adjust_ref."'";
			$this->db->query($Usql); 
		} // End if adjust_tbl
		
	     }// End foreach
	  } // End if
    }
	function getBillID($institute_id,$branch_id,$session_id,$billing_month,$bill_date){
		$SL=""; $BSL =""; $TotalNo=1; $BillNo = ""; $yearArr = explode("-",$bill_date);
		$ssql = "SELECT COUNT(*) as total FROM ".BILL_MASTER_TBL." WHERE institute_id =$institute_id AND branch_id = $branch_id AND session_id = $session_id AND billing_month=$billing_month AND status < 5";
		$squery = $this->db->query($ssql);				
		if($squery->num_rows() >0){
		   $TotalNo = $squery->row()->total+1;		
		   if($TotalNo <10){
		      $SL="0000";
		   }elseif($TotalNo <100){
		      $SL="000";
		   }elseif($TotalNo <1000){
		      $SL="00";
		   }elseif($TotalNo <10000){
		      $SL="0";
		   }else{
		      $SL=$TotalNo;
		   }
		}else{
		      $SL="0000";
		}
		$BSL=$SL.$TotalNo;
		$psql  = "SELECT branch_code FROM ".BRANCH_TBL." WHERE branch_id =$branch_id";
		$query = $this->db->query($psql);
		//print $this->db->last_query(); exit;
		$BC =""; $BM = 0; $BM=$billing_month;
		if($query->num_rows() == 1){
		 $row = $query->row();		
		 $BC  = $row->branch_code;
		}		
		$BillNo = $BC.$yearArr[0]."/".$BM."/".$BSL;
		return $BillNo;		
	}
	function getHeadType($account_id){
        $this->db->select('head_type');
        $this->db->from(ACC_HEAD_TBL);
        $this->db->where('account_id', $account_id);
        $query = $this->db->get();
        return $query->row()->head_type; 
    }
    function getVoucherID($invoice_date){
	$invoice_id = "";
	$INVArr     = explode("-",$invoice_date);
	$INVSL      = $this->getNextVoucherNo($INVArr[0],$INVArr[1]);
	$invoice_id = $INVArr[0]."/".$INVArr[1]."/".$INVSL;
	return $invoice_id;	
    }
    function getNextVoucherNo($year,$month){
		$INVSL=""; $INVNo="";		
		if ($year !="" && $month !="") {
			$SQL = "SELECT COUNT(voucher_no) AS voucher_no FROM ".VOUCHER_MASTER_TBL." WHERE YEAR(voucher_date)='".$year."' AND MONTH(voucher_date)='".$month."'";
			$query = $this->db->query($SQL);				
			$INVNo = $query->row()->voucher_no+1;
			if($INVNo <10){
			$INVSL = "000".$INVNo;	
			}elseif($INVNo <100){
			$INVSL = "00".$INVNo;	
			}elseif($INVNo <1000){
			$INVSL = "0".$INVNo;	
			}else{
			$INVSL = $INVNo;	
			}
		}else{
			$INVSL="0001";
		}
		return $INVSL;
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
